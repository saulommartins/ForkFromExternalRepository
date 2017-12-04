/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de função PLPGSQL
*
*

	$Id: somatorioReceitaBalanco.plsql 59612 2014-09-02 12:00:51Z gelson $

*
* Casos de uso: uc-02.01.10
*/

/*
$Log$
Revision 1.9  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_receita_balanco(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidades         ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN

    CREATE TABLE tmp_relatorio AS
        SELECT
            CASE WHEN substr(cod_estrutural,1,1)::integer = 9
                 THEN orcamento.fn_consulta_class_receita(cod_conta, stExercicio,
                                (
                                SELECT
                                    administracao.configuracao.valor
                                FROM
                                    administracao.configuracao
                                WHERE
                                    administracao.configuracao.cod_modulo = 8 AND
                                    administracao.configuracao.parametro = ''masc_class_receita_dedutora'' AND
                                    administracao.configuracao.exercicio = stExercicio
                                )
                        )
                 ELSE orcamento.fn_consulta_class_receita(cod_conta, stExercicio,
                                (
                                SELECT
                                    administracao.configuracao.valor
                                FROM
                                    administracao.configuracao
                                WHERE
                                    administracao.configuracao.cod_modulo = 8 AND
                                    administracao.configuracao.parametro = ''masc_class_receita'' AND
                                    administracao.configuracao.exercicio = stExercicio
                                )
                        )
            END as classificacao,
            CASE WHEN substr(cod_estrutural,1,1)::integer = 9
                THEN publico.fn_mascarareduzida( orcamento.fn_consulta_class_receita( cod_conta, stExercicio,
                                (
                                SELECT
                                    administracao.configuracao.valor
                                FROM
                                    administracao.configuracao
                                WHERE
                                    administracao.configuracao.cod_modulo = 8 AND
                                    administracao.configuracao.parametro = ''masc_class_receita_dedutora'' AND
                                    administracao.configuracao.exercicio = stExercicio
                                )
                        ))
                ELSE publico.fn_mascarareduzida( orcamento.fn_consulta_class_receita( cod_conta, stExercicio,
                                (
                                SELECT
                                    administracao.configuracao.valor
                                FROM
                                    administracao.configuracao
                                WHERE
                                    administracao.configuracao.cod_modulo = 8 AND
                                    administracao.configuracao.parametro = ''masc_class_receita'' AND
                                    administracao.configuracao.exercicio = stExercicio
                                )
                        ))
            END as classificacao_reduzida,
            CASE WHEN substr(cod_estrutural,1,1)::integer = 9
                THEN publico.fn_nivel( orcamento.fn_consulta_class_receita( cod_conta, stExercicio,
                        (
                        SELECT
                            administracao.configuracao.valor
                        FROM
                            administracao.configuracao
                        WHERE
                            administracao.configuracao.cod_modulo = 8 AND
                            administracao.configuracao.parametro = ''masc_class_receita_dedutora'' AND
                            administracao.configuracao.exercicio = stExercicio
                        )
                    ))
                ELSE publico.fn_nivel( orcamento.fn_consulta_class_receita( cod_conta, stExercicio,
                        (
                        SELECT
                            administracao.configuracao.valor
                        FROM
                            administracao.configuracao
                        WHERE
                            administracao.configuracao.cod_modulo = 8 AND
                            administracao.configuracao.parametro = ''masc_class_receita'' AND
                            administracao.configuracao.exercicio = stExercicio
                        )
                    ))
            END as nivel,
            cod_conta,
            exercicio,
            descricao
        FROM
            orcamento.conta_receita
        WHERE
            exercicio = stExercicio
        ORDER BY classificacao;

stSql := ''CREATE TABLE tmp_debito AS (
        SELECT
            vl.vl_lancamento,
            CASE WHEN substr(cod_estrutural,1,1)::integer = 9
                THEN orcamento.fn_consulta_class_receita(ocr.cod_conta, ore.exercicio, ( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''''masc_class_receita_dedutora'''' AND administracao.configuracao.exercicio = ore.exercicio)) 
                ELSE orcamento.fn_consulta_class_receita(ocr.cod_conta, ore.exercicio, ( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''''masc_class_receita'''' AND administracao.configuracao.exercicio = ore.exercicio)) 
            END as classificacao
        FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote ,
            contabilidade.conta_debito          as ccd
            --contabilidade.conta_receita         as ccr
        WHERE
            ore.cod_entidade    IN ( ''|| stEntidades ||'' )
        AND ore.exercicio       = ''''''||stExercicio||''''''
        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio
        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.cod_entidade     = ore.cod_entidade
        AND lr.estorno          = true
        -- join nas tabelas lancamento_receita e lancamento
        AND lan.cod_lote        = lr.cod_lote
        AND lan.sequencia       = lr.sequencia
        AND lan.exercicio       = lr.exercicio
        AND lan.cod_entidade    = lr.cod_entidade
        AND lan.tipo            = lr.tipo
        -- join nas tabelas lancamento e valor_lancamento
        AND vl.exercicio        = lan.exercicio
        AND vl.sequencia        = lan.sequencia
        AND vl.cod_entidade     = lan.cod_entidade
        AND vl.cod_lote         = lan.cod_lote
        AND vl.tipo             = lan.tipo
        -- ligar conta debito
        AND ccd.cod_lote        = vl.cod_lote
        AND ccd.tipo            = vl.tipo
        AND ccd.sequencia       = vl.sequencia
        AND ccd.exercicio       = vl.exercicio
        AND ccd.tipo_valor      = vl.tipo_valor
        AND ccd.cod_entidade    = vl.cod_entidade
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND lr.tipo             = ''''A''''
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''''D''''
        -- Data Inicial e Data Final, antes iguala codigo do lote
        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo
        AND lote.dt_lote BETWEEN to_date(''''''||stDataInicial||'''''',''''dd/mm/yyyy'''') AND to_date(''''''||stDataFinal||'''''',''''dd/mm/yyyy'''')
    )
    '';
    EXECUTE stSql;

    stSql:=''CREATE TABLE tmp_credito AS (
        SELECT
            vl.vl_lancamento,
            CASE WHEN substr(cod_estrutural,1,1)::integer = 9
                THEN orcamento.fn_consulta_class_receita(ocr.cod_conta, ore.exercicio, ( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''''masc_class_receita_dedutora'''' AND administracao.configuracao.exercicio = ore.exercicio))
                ELSE orcamento.fn_consulta_class_receita(ocr.cod_conta, ore.exercicio, ( SELECT administracao.configuracao.valor FROM administracao.configuracao WHERE administracao.configuracao.cod_modulo = 8 AND administracao.configuracao.parametro = ''''masc_class_receita'''' AND administracao.configuracao.exercicio = ore.exercicio)) 
            END as classificacao
        FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote ,
            contabilidade.conta_credito         as ccr
        WHERE
            ore.cod_entidade    IN( ''||stEntidades||'' )
        AND ore.exercicio       = ''''''||stExercicio||''''''
        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio
        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.cod_entidade     = ore.cod_entidade
        AND lr.estorno          = false
        -- join nas tabelas lancamento_receita e lancamento
        AND lan.cod_lote        = lr.cod_lote
        AND lan.sequencia       = lr.sequencia
        AND lan.exercicio       = lr.exercicio
        AND lan.cod_entidade    = lr.cod_entidade
        AND lan.tipo            = lr.tipo
        -- join nas tabelas lancamento e valor_lancamento
        AND vl.exercicio        = lan.exercicio
        AND vl.sequencia        = lan.sequencia
        AND vl.cod_entidade     = lan.cod_entidade
        AND vl.cod_lote         = lan.cod_lote
        AND vl.tipo             = lan.tipo
        -- ligar conta debito
        AND ccr.cod_lote        = vl.cod_lote
        AND ccr.tipo            = vl.tipo
        AND ccr.sequencia       = vl.sequencia
        AND ccr.exercicio       = vl.exercicio
        AND ccr.tipo_valor      = vl.tipo_valor
        AND ccr.cod_entidade    = vl.cod_entidade
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND lr.tipo             = ''''A''''
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''''C''''
        -- Data Inicial e Data Final, antes iguala codigo do lote
        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo
        AND lote.dt_lote BETWEEN to_date(''''''||stDataInicial||'''''',''''dd/mm/yyyy'''') AND to_date(''''''||stDataFinal||'''''',''''dd/mm/yyyy'''')
    )'';
    EXECUTE stSql;

    FOR reRegistro IN
        SELECT   cod_conta
                ,nivel
                ,exercicio
                ,descricao
                ,classificacao
                ,classificacao_reduzida
                ,0.00 as valor
        FROM
                 tmp_relatorio
        WHERE
            exercicio = stExercicio
    LOOP
        reRegistro.valor := coalesce(orcamento.fn_totaliza_receita_balanco(reRegistro.classificacao_reduzida),0)*-1;
        RETURN next reRegistro;
    END LOOP;
   DROP TABLE tmp_relatorio;
   DROP TABLE tmp_debito;
   DROP TABLE tmp_credito;

    RETURN;
    --RETURN null;
END;
'language 'plpgsql';

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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.09
*/

/*
$Log$
Revision 1.7  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_orcamento_somatorio_receita_balanco(varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stCodEntidade       ALIAS FOR $3;
    stDataInicial       ALIAS FOR $4;
    stDataFinal         ALIAS FOR $5;
    stSql               VARCHAR   := '';
    stMascara           VARCHAR   := '';
    reRegistro          RECORD;

BEGIN

    stMascara := selectIntoVarchar('SELECT valor
                                      FROM administracao.configuracao
                                     WHERE cod_modulo  = 8
                                       AND parametro   = ''masc_class_receita''    
                                       AND exercicio   = '''||stExercicio||''' ');

    stSql := '
    CREATE TABLE tmp_relatorio AS
    SELECT
        orcamento.fn_consulta_class_receita(cod_conta, exercicio, '''||stMascara||''') as classificacao,
        publico.fn_mascarareduzida(orcamento.fn_consulta_class_receita(cod_conta, exercicio, '''||stMascara||''')) as classificacao_reduzida,
        publico.fn_nivel(orcamento.fn_consulta_class_receita(cod_conta, exercicio, '''||stMascara||''')) as nivel,
        cod_conta,
        exercicio,
        descricao
    FROM
        orcamento.conta_receita
    WHERE
        exercicio = '||quote_literal(stExercicio)||'
    ORDER BY
        classificacao';

    EXECUTE stSql;

    stSql := 'CREATE TABLE tmp_debito AS (
        SELECT
            vl.vl_lancamento,
            orcamento.fn_consulta_class_receita(ocr.cod_conta, ore.exercicio, ''' || stMascara || ''') as classificacao
        FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote
        WHERE
            ore.cod_entidade    IN ( '|| stCodEntidade ||' )
        AND ore.exercicio       = '''||stExercicio||'''

        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio

        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.cod_entidade     = ore.cod_entidade
       AND lr.estorno          = true
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND lr.tipo             = ''A''

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
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''D''

        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo
        AND lote.dt_lote BETWEEN to_date('''||stDataInicial||'''::varchar,''dd/mm/yyyy'') AND to_date('''||stDataFinal||'''::varchar,''dd/mm/yyyy''))';

    EXECUTE stSql;

    stSql:='CREATE TABLE tmp_credito AS (
        SELECT
            vl.vl_lancamento,
            orcamento.fn_consulta_class_receita(ocr.cod_conta, ore.exercicio, ''' || stMascara || ''') as classificacao
        FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote
        WHERE
            ore.cod_entidade    IN( '||stCodEntidade||' )
        AND ore.exercicio       = '''||stExercicio||'''

        AND ocr.cod_conta       = ore.cod_conta
        AND ocr.exercicio       = ore.exercicio

        -- join lancamento receita
        AND lr.cod_receita      = ore.cod_receita
        AND lr.exercicio        = ore.exercicio
        AND lr.cod_entidade     = ore.cod_entidade
        AND lr.estorno          = false
        -- tipo de lancamento receita deve ser = A , de arrecadação
        AND lr.tipo             = ''A''

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
        -- na tabela valor lancamento  tipo_valor deve ser credito
        AND vl.tipo_valor       = ''C''

        AND lote.cod_lote       = lan.cod_lote
        AND lote.cod_entidade   = lan.cod_entidade
        AND lote.exercicio      = lan.exercicio
        AND lote.tipo           = lan.tipo
        AND lote.dt_lote BETWEEN to_date('''||stDataInicial||'''::varchar,''dd/mm/yyyy'') AND to_date('''||stDataFinal||'''::varchar,''dd/mm/yyyy''))';

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
    LOOP
        reRegistro.valor := coalesce(orcamento.fn_totaliza_receita_balanco(reRegistro.classificacao_reduzida),0)*-1;
        RETURN next reRegistro;
    END LOOP;
   DROP TABLE tmp_relatorio;
   DROP TABLE tmp_debito;
   DROP TABLE tmp_credito;

    RETURN;
END;
$$ language 'plpgsql';

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
* $Revision: 46946 $
* $Name$
* $Author: tonismar $
* $Date: 2012-06-29 13:30:55 -0300 (Fri, 29 Jun 2012) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.08.07
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.7  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.6  2006/07/05 20:37:44  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_balancete_receita_transparencia(varchar,varchar,varchar,
                                                                        varchar,varchar,varchar,
                                                                        varchar,varchar,varchar,
                                                                        varchar,varchar,varchar, integer) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stCodEstruturalInicial  ALIAS FOR $6;
    stCodEstruturalFinal    ALIAS FOR $7;
    stCodReduzidoInicial    ALIAS FOR $8;
    stCodReduzidoFinal      ALIAS FOR $9;
    inCodRecurso            ALIAS FOR $10;
    stDestinacaoRecurso     ALIAS FOR $11;
    inCodDetalhamento       ALIAS FOR $12;
    inMes                   ALIAS FOR $13;
    dtInicioAno         VARCHAR   := '  ''';
    dtFimAno            VARCHAR   := '''';
    stSql               VARCHAR   := '''';
    stMascClassReceita  VARCHAR   := '''';
    stMascRecurso       VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
        dtInicioAno := '01/01/' || stExercicio;

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE
                    ore.cod_entidade    IN (''' || stCodEntidades || ''')
                AND ore.exercicio       = ''' || stExercicio || '''

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
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

            UNION

            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                    ore.cod_entidade    IN(''' || stCodEntidades || ''')
                AND ore.exercicio       = ''' || stExercicio || '''
                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
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

                -- Data Inicial e Data Final, antes iguala codigo do lote
                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo )';
        EXECUTE stSql;


        stSql := '
            SELECT tbl.cod_estrutural
                 , tbl.receita
                 , tbl.recurso
                 , tbl.descricao
                 , coalesce(sum(tbl.valor_previsto),0.00) as valor_original
                 , coalesce(sum(tbl.arrecadado_jan)*-1,0.00) as ar_jan
                 , coalesce(sum(tbl.arrecadado_fev)*-1,0.00) as ar_fev
                 , coalesce(sum(tbl.arrecadado_mar)*-1,0.00) as ar_mar
                 , coalesce(sum(tbl.arrecadado_abr)*-1,0.00) as ar_abr
                 , coalesce(sum(tbl.arrecadado_mai)*-1,0.00) as ar_mai
                 , coalesce(sum(tbl.arrecadado_jun)*-1,0.00) as ar_jun
                 , coalesce(sum(tbl.arrecadado_jul)*-1,0.00) as ar_jul
                 , coalesce(sum(tbl.arrecadado_ago)*-1,0.00) as ar_ago
                 , coalesce(sum(tbl.arrecadado_set)*-1,0.00) as ar_set
                 , coalesce(sum(tbl.arrecadado_out)*-1,0.00) as ar_out
                 , coalesce(sum(tbl.arrecadado_nov)*-1,0.00) as ar_nov
                 , coalesce(sum(tbl.arrecadado_dez)*-1,0.00) as ar_dez
            FROM (
                SELECT
                    ocr.cod_estrutural as cod_estrutural,
                    coalesce(r.cod_receita,0)  as receita,
                    coalesce(rec.masc_recurso_red,''0'') as recurso,
                    ocr.descricao::varchar AS descricao,
                    orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                        ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                        , ''' || stCodEntidades || '''
                    ) as valor_previsto,
                    CASE WHEN '||inMes||' >= 1 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/01/'||stExercicio||'''
                                                                    ,''31/01/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_jan,
                    CASE WHEN '||inMes||' >= 2 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/02/'||stExercicio||'''
                                                                    ,''28/02/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_fev,
                    CASE WHEN '||inMes||' >= 3 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/03/'||stExercicio||'''
                                                                    ,''31/03/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_mar,
                    CASE WHEN '||inMes||' >= 4 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/04/'||stExercicio||'''
                                                                    ,''30/04/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_abr,
                    CASE WHEN '||inMes||' >= 5 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/05/'||stExercicio||'''
                                                                    ,''31/05/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_mai,
                    CASE WHEN '||inMes||' >= 6 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/06/'||stExercicio||'''
                                                                    ,''30/06/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_jun,
                    CASE WHEN '||inMes||' >= 7 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/07/'||stExercicio||'''
                                                                    ,''31/07/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_jul,
                    CASE WHEN '||inMes||' >= 8 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/08/'||stExercicio||'''
                                                                    ,''31/08/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_ago,
                    CASE WHEN '||inMes||' >= 9 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/09/'||stExercicio||'''
                                                                    ,''30/09/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_set,
                    CASE WHEN '||inMes||' >= 10 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/10/'||stExercicio||'''
                                                                    ,''31/10/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_out,
                    CASE WHEN '||inMes||' >= 11 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/11/'||stExercicio||'''
                                                                    ,''30/11/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_nov,
                    CASE WHEN '||inMes||' >= 12 THEN
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                    ,''01/12/'||stExercicio||'''
                                                                    ,''31/12/'||stExercicio||'''
                            )
                        ELSE 0.00
                    END AS arrecadado_dez
                FROM
                    orcamento.conta_receita ocr
                        LEFT OUTER JOIN orcamento.receita as r ON
                            ocr.exercicio = r.exercicio AND
                            ocr.cod_conta = r.cod_conta AND
                            r.cod_entidade    IN (''' || stCodEntidades || ''') AND
                            r.exercicio       = ''' || stExercicio || '''
                        LEFT JOIN orcamento.recurso(''' || stExercicio || ''') as rec ON
                            rec.cod_recurso = r.cod_recurso AND
                            rec.exercicio   = r.exercicio
                WHERE
                    ocr.cod_conta = ocr.cod_conta
                AND ocr.exercicio =  ''' || stExercicio || '''
                    ';

                    if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '') then
                        stSql := stSql || ' AND ocr.cod_estrutural >= ''' || stCodEstruturalInicial || '''';
                    end if;

                    if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '') then
                        stSql := stSql || ' AND ocr.cod_estrutural <= ''' || stCodEstruturalFinal || '''';
                    end if;

                    stSql := stSql || ' ' || stFiltro || ' ORDER BY ocr.cod_estrutural) as tbl

                    WHERE
                           orcamento.fn_movimento_balancete_receita( ''' || stExercicio || '''
                                                                    ,publico.fn_mascarareduzida(tbl.cod_estrutural)
                                                                    ,''' || stCodEntidades || '''
                                                                    ,''' || dtInicioAno || '''
                                                                    ,''' || dtFinal   || '''
                                                                    ) = true
                                                                    
                    GROUP BY tbl.cod_estrutural, tbl.receita, tbl.recurso, tbl.descricao
                        ';
                        
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;

    RETURN;
END;
$$language 'plpgsql';
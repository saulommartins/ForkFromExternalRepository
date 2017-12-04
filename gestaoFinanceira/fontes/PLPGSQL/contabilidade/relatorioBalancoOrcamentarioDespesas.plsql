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
* $Id:$
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_balanco_orcamentario_despesas(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio            ALIAS FOR $1;
    stCodEntidade          ALIAS FOR $2;
    stDtInicial            ALIAS FOR $3;
    stDtFilnal             ALIAS FOR $4;
    stTipo                 ALIAS FOR $5;
    stSql                  VARCHAR   := '''';
    stSqlAux               VARCHAR   := '''';
    stSqlIntra             VARCHAR   := '''';
    stSqlInsert            VARCHAR   := '''';
    stSqlClassificacao     VARCHAR   := '''';
    reRegistro             RECORD;
    reRegLoop              RECORD;
    reRegIntra             RECORD;
    reRegEstrutural        RECORD;
    reRegClassificacao     RECORD;

BEGIN

    stSql := ''
    CREATE TEMPORARY TABLE tmp_despesas_anexo12 AS (
    SELECT estrutural_reduzido::varchar as estrutural_reduzido
     , descricao_despesa::varchar as descricao_despesa
     , sum(vl_original)::numeric(14,2) as vl_original
     , sum(vl_despesa)::numeric(14,2) as vl_despesa
     , sum(vl_diferenca)::numeric(14,2) as vl_diferenca
     , nivel::integer as nivel
     , classificacao::integer as classificacao
     , intra::text as intra
  FROM (
                SELECT estrutural_reduzido
                     , descricao_despesa
                     , CASE WHEN (EXISTS (SELECT 1 FROM orcamento.suplementacao_suplementada
                                             INNER JOIN orcamento.suplementacao
                                                     ON suplementacao_suplementada.exercicio         = suplementacao.exercicio
                                                    AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                                  WHERE suplementacao_suplementada.exercicio         = orcamentario.exercicio
                                                    AND suplementacao_suplementada.cod_despesa       = orcamentario.cod_despesa
                                                    AND suplementacao.cod_tipo in (6,7,8,9,10,11)
                                                    AND orcamentario.classificacao not in (2,3)
                                         )
                                 ) THEN sum(vl_suplementado) - sum(vl_reduzido)
                            WHEN (EXISTS (SELECT 1 FROM orcamento.suplementacao_reducao
                                             INNER JOIN orcamento.suplementacao
                                                     ON suplementacao_reducao.exercicio         = suplementacao.exercicio
                                                    AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                                                  WHERE suplementacao_reducao.exercicio         = orcamentario.exercicio
                                                    AND suplementacao_reducao.cod_despesa       = orcamentario.cod_despesa
                                                    AND suplementacao.cod_tipo in (6,7,8,9,10,11)
                                                    AND orcamentario.classificacao not in (2,3)
                                         )
                                 ) THEN sum(vl_suplementado) - sum(vl_reduzido)
                       ELSE
                           sum( orcamentario.vl_original ) + sum(vl_suplementado) - sum(vl_reduzido)
                       END AS vl_original
                     , sum(vl_despesa::numeric(14,2)) as vl_despesa
                     , sum(vl_diferenca::numeric(14,2)) as vl_diferenca
                     , nivel
                     , intra
                     , classificacao
                
                  FROM (
                        SELECT
                            cast(tabela.estrutural_reduzido as varchar) as estrutural_reduzido,
                            cast(OCD.descricao as varchar) as descricao_despesa,
                            sum( tabela.vl_original ) as vl_original,
                            sum( tabela.suplementado )  as vl_suplementado,
                            sum( tabela.reduzido )  as vl_reduzido,
                            sum ( tabela.vl_total ) - sum( tabela.vl_total_anulado ) AS vl_despesa,
                            cast(0.00 as numeric(14,2)) as vl_diferenca,
                            publico.fn_nivel (OCD.cod_estrutural) as nivel,
                            ''''F''''::text as intra,
                            tabela.cod_despesa,
                            tabela.exercicio,
                            CASE WHEN (EXISTS (
                                                SELECT 1 FROM orcamento.suplementacao_suplementada ss1
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = ss1.cod_suplementacao
                                                          AND suplementacao.exercicio         = ss1.exercicio
                                                        WHERE ss1.exercicio         = tabela.exercicio
                                                          AND ss1.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo = 11
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = ss1.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = ss1.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = ss1.exercicio
                                                                              AND o_sa2.cod_suplementacao = ss1.cod_suplementacao
                                                                         )
                                            UNION 
                                                SELECT 1 FROM orcamento.suplementacao_reducao
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                          AND suplementacao.exercicio         = suplementacao_reducao.exercicio
                                                        WHERE suplementacao_reducao.exercicio         = tabela.exercicio
                                                          AND suplementacao_reducao.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo = 11
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = suplementacao_reducao.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = suplementacao_reducao.exercicio
                                                                              AND o_sa2.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                        )
                            ) THEN 3 
                            WHEN (EXISTS (
                                                SELECT 1 FROM orcamento.suplementacao_suplementada
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                                                          AND suplementacao.exercicio         = suplementacao_suplementada.exercicio
                                                        WHERE suplementacao_suplementada.exercicio         = tabela.exercicio
                                                          AND suplementacao_suplementada.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo in (6,7,8,9,10)
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = suplementacao_suplementada.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = suplementacao_suplementada.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = suplementacao_suplementada.exercicio
                                                                              AND o_sa2.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                                                                         )
                                            UNION                   
                                                SELECT 1 FROM orcamento.suplementacao_reducao
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                          AND suplementacao.exercicio         = suplementacao_reducao.exercicio
                                                        WHERE suplementacao_reducao.exercicio         = tabela.exercicio
                                                          AND suplementacao_reducao.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo in (6,7,8,9,10)
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = suplementacao_reducao.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = suplementacao_reducao.exercicio
                                                                              AND o_sa2.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                        )
                            ) THEN 2
                            ELSE 1
                            END as classificacao
                
                
                        FROM(
                            SELECT
                                substr(OCD.cod_estrutural,1,3) AS estrutural_reduzido,
                                OCD.exercicio,
                                OD.cod_entidade,
                                coalesce( OD.vl_original, 0.00 )              AS vl_original,
                                coalesce( OSS.valor, 0.00 )  as suplementado,
                                coalesce( OSR.valor, 0.00 )  as reduzido,
                                OD.cod_despesa

            '';
    
        IF stTipo = ''Empenhado'' THEN
            stSql := stSql || ''
                ,sum( coalesce( EIPE.vl_total, 0.00 ) )        AS vl_total
                ,sum( coalesce( EEA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
            '';
        END IF;
    
        IF stTipo = ''Liquidado'' THEN
            stSql := stSql || ''
                ,sum( coalesce( ENLI.vl_total, 0.00 ) )    AS vl_total
                ,sum( coalesce( ENLIA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
            '';
        END IF;
    
        IF stTipo = ''Pago'' THEN
            stSql := stSql || ''
                ,sum( coalesce( ENLP.vl_total, 0.00 ) )  AS vl_total
                ,sum( coalesce( ENLPA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
            '';
        END IF;
    
        stSql := stSql || ''
            FROM
                orcamento.conta_despesa AS OCD
                    LEFT JOIN orcamento.despesa  AS OD ON(
                        OCD.exercicio  = OD.exercicio AND
                        OCD.cod_conta  = OD.cod_conta
                    )
                    LEFT JOIN (
                        SELECT
                            OSS.exercicio,
                            OSS.cod_despesa,
                            sum( OSS.valor ) AS valor
                        FROM
                            orcamento.suplementacao_suplementada AS OSS,
                            orcamento.suplementacao              AS OS
                        WHERE
                            OSS.exercicio         = OS.exercicio            AND
                            OSS.cod_suplementacao = OS.cod_suplementacao    AND
                            OS.dt_suplementacao BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                    AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa
                                          WHERE o_sa.exercicio                  = OSS.exercicio
                                            AND o_sa.cod_suplementacao_anulacao = OSS.cod_suplementacao
                                       )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa2
                                          WHERE o_sa2.exercicio         = OSS.exercicio
                                            AND o_sa2.cod_suplementacao = OSS.cod_suplementacao
                                       )
                        GROUP BY
                            OSS.exercicio,
                            OSS.cod_despesa
                        ORDER BY
                            OSS.exercicio,
                            OSS.cod_despesa
                    ) AS OSS ON(
                        OSS.exercicio   = OD.exercicio AND
                        OSS.cod_despesa = OD.cod_despesa
                    )
                    LEFT JOIN (
                        SELECT
                            OSR.exercicio,
                            OSR.cod_despesa,
                            sum( OSR.valor ) AS valor
                        FROM
                            orcamento.suplementacao_reducao AS OSR,
                            orcamento.suplementacao         AS OS
                        WHERE
                            OSR.exercicio         = OS.exercicio            AND
                            OSR.cod_suplementacao = OS.cod_suplementacao
    
    
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa3
                                          WHERE o_sa3.exercicio                  = OSR.exercicio
                                            AND o_sa3.cod_suplementacao_anulacao = OSR.cod_suplementacao
                                       )
    
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa4
                                          WHERE o_sa4.exercicio         = OSR.exercicio
                                            AND o_sa4.cod_suplementacao = OSR.cod_suplementacao
                                       )
    
                        AND OS.dt_suplementacao BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                    AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            OSR.exercicio,
                            OSR.cod_despesa
                        ORDER BY
                            OSR.exercicio,
                            OSR.cod_despesa
                    ) AS OSR ON(
                        OSR.exercicio   = OD.exercicio  AND
                        OSR.cod_despesa = OD.cod_despesa
                    )
                    LEFT JOIN empenho.pre_empenho_despesa AS EPED ON(
                        OD.cod_despesa = EPED.cod_despesa AND
                        OD.exercicio   = EPED.exercicio
                    )
                    LEFT JOIN empenho.pre_empenho AS EPE ON(
                        EPED.cod_pre_empenho = EPE.cod_pre_empenho AND
                        EPED.exercicio       = EPE.exercicio
                    )
                    LEFT JOIN empenho.empenho AS EE ON(
                        EPE.cod_pre_empenho = EE.cod_pre_empenho AND
                        EPE.exercicio       = EE.exercicio
                    )
        '';
    
        IF stTipo = ''Empenhado'' THEN
            stSql := stSql || ''
                    LEFT JOIN (
                        SELECT
                            sum( coalesce( EIPE.vl_total, 0.00 ) ) as vl_total,
                            EIPE.cod_pre_empenho,
                            EIPE.exercicio
                        FROM
                            empenho.item_pre_empenho AS EIPE,
                            empenho.empenho          AS EE
                        WHERE
                            EE.exercicio       = EIPE.exercicio         AND
                            EE.cod_pre_empenho = EIPE.cod_pre_empenho   AND
                            coalesce( EE.dt_empenho , TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                                BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                    AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            EIPE.exercicio,
                            EIPE.cod_pre_empenho
                        ORDER BY
                            EIPE.exercicio,
                            EIPE.cod_pre_empenho
                    ) AS EIPE ON(
                        EPE.cod_pre_empenho  = EIPE.cod_pre_empenho AND
                        EPE.exercicio        = EIPE.exercicio       AND
                        EIPE.cod_pre_empenho = EE.cod_pre_empenho   AND
                        EIPE.exercicio       = EE.exercicio
                    )
    
                    LEFT JOIN (
                        SELECT
                            sum( coalesce( EEAI.vl_anulado, 0.00 ) ) AS vl_total_anulado,
                            EEA.exercicio,
                            EEA.cod_entidade,
                            EEA.cod_empenho
                        FROM
                            empenho.empenho_anulado AS EEA,
                            empenho.empenho_anulado_item AS EEAI
                        WHERE
                            EEA.exercicio    = EEAI.exercicio       AND
                            EEA.cod_entidade = EEAI.cod_entidade    AND
                            EEA.cod_empenho  = EEAI.cod_empenho     AND
                            EEA.timestamp    = EEAI.timestamp       AND
                            coalesce(   TO_DATE( EEA.timestamp::text, ''''yyyy-mm-dd'''' ),
                                        TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) ) BETWEEN
                                        TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND
                                        TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' ) AND
                            EEA.exercicio = '''''' || stExercicio || ''''''
                        GROUP BY
                            EEA.exercicio,
                            EEA.cod_entidade,
                            EEA.cod_empenho
                        ORDER BY
                            EEA.exercicio,
                            EEA.cod_entidade,
                            EEA.cod_empenho
                    ) AS EEA ON(
                        EE.exercicio    = EEA.exercicio     AND
                        EE.cod_entidade = EEA.cod_entidade  AND
                        EE.cod_empenho  = EEA.cod_empenho
                    )
            '';
        END IF;
    
        IF stTipo = ''Liquidado'' THEN
            stSql := stSql || ''
                    LEFT JOIN (
                        SELECT
                            ENL.cod_empenho,
                            ENL.exercicio_empenho,
                            ENL.exercicio,
                            ENL.cod_entidade,
                            ENL.cod_nota
                        FROM
                            empenho.nota_liquidacao AS ENL
                        WHERE
                            coalesce( ENL.dt_liquidacao, TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                                   BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                       AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENL.cod_empenho,
                            ENL.exercicio_empenho,
                            ENL.exercicio,
                            ENL.cod_entidade,
                            ENL.cod_nota
                        ORDER BY
                            ENL.cod_empenho,
                            ENL.exercicio_empenho,
                            ENL.exercicio,
                            ENL.cod_entidade,
                            ENL.cod_nota
                    ) AS ENL ON(
                        EE.cod_empenho  = ENL.cod_empenho       AND
                        EE.exercicio    = ENL.exercicio_empenho AND
                        EE.cod_entidade = ENL.cod_entidade
                    )
                    LEFT JOIN empenho.nota_liquidacao_item AS ENLI ON(
                        ENL.exercicio    = ENLI.exercicio   AND
                        ENL.cod_nota     = ENLI.cod_nota    AND
                        ENL.cod_entidade = ENLI.cod_entidade
                    )
                    LEFT JOIN (
                        SELECT
                            sum( ENLIA.vl_anulado ) AS vl_total_anulado,
                            ENLIA.exercicio,
                            ENLIA.cod_nota,
                            ENLIA.num_item,
                            ENLIA.exercicio_item,
                            ENLIA.cod_pre_empenho,
                            ENLIA.cod_entidade
                        FROM
                            empenho.nota_liquidacao_item_anulado AS ENLIA
                        WHERE
                            coalesce( TO_DATE( ENLIA.timestamp::text, ''''yyyy-mm-dd'''' ),
                                      TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                      BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                      AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENLIA.exercicio,
                            ENLIA.cod_nota,
                            ENLIA.num_item,
                            ENLIA.exercicio_item,
                            ENLIA.cod_pre_empenho,
                            ENLIA.cod_entidade
                        ORDER BY
                            ENLIA.exercicio,
                            ENLIA.cod_nota,
                            ENLIA.num_item,
                            ENLIA.exercicio_item,
                            ENLIA.cod_pre_empenho,
                            ENLIA.cod_entidade
                    ) AS ENLIA ON(
                        ENLI.cod_entidade    = ENLIA.cod_entidade       AND
                        ENLI.cod_nota        = ENLIA.cod_nota           AND
                        ENLI.exercicio       = ENLIA.exercicio          AND
                        ENLI.cod_pre_empenho = ENLIA.cod_pre_empenho    AND
                        ENLI.exercicio_item  = ENLIA.exercicio_item     AND
                        ENLI.num_item        = ENLIA.num_item
                    )
            '';
        END IF;
    
        IF stTipo = ''Pago'' THEN
            stSql := stSql || ''
                    LEFT JOIN empenho.nota_liquidacao AS ENL ON(
                        EE.cod_empenho  = ENL.cod_empenho       AND
                        EE.exercicio    = ENL.exercicio_empenho AND
                        EE.cod_entidade = ENL.cod_entidade
                    )
                    LEFT JOIN(
                        SELECT
                            ENLP.cod_entidade,
                            ENLP.exercicio,
                            ENLP.cod_nota,
                            ENLP.timestamp,
                            sum( coalesce( ENLP.vl_pago, 0.00 ) ) as vl_total
                        FROM
                            empenho.nota_liquidacao_paga AS ENLP
                        WHERE
                            coalesce( TO_DATE( ENLP.timestamp::text, ''''yyyy-mm-dd'''' ),
                                      TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                      BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                      AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENLP.exercicio,
                            ENLP.cod_entidade,
                            ENLP.cod_nota,
                            ENLP.timestamp
                        ORDER BY
                            ENLP.exercicio,
                            ENLP.cod_entidade,
                            ENLP.cod_nota,
                            ENLP.timestamp
                    ) AS ENLP ON(
                        ENL.exercicio    = ENLP.exercicio       AND
                        ENL.cod_entidade = ENLP.cod_entidade    AND
                        ENL.cod_nota     = ENLP.cod_nota
                    )
                    LEFT JOIN(
                        SELECT
                            ENLPA.exercicio,
                            ENLPA.cod_entidade,
                            ENLPA.cod_nota,
                            ENLPA.timestamp,
                            sum( coalesce( ENLPA.vl_anulado, 0.00 ) ) as vl_total_anulado
                        FROM
                            empenho.nota_liquidacao_paga_anulada AS ENLPA
                        WHERE
                            coalesce( TO_DATE( ENLPA.timestamp_anulada::text, ''''yyyy-mm-dd'''' ),
                                      TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                      BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                          AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENLPA.exercicio,
                            ENLPA.cod_entidade,
                            ENLPA.cod_nota,
                            ENLPA.timestamp
                        ORDER BY
                            ENLPA.exercicio,
                            ENLPA.cod_entidade,
                            ENLPA.cod_nota,
                            ENLPA.timestamp
                    ) AS ENLPA ON(
                        ENLP.exercicio    = ENLPA.exercicio     AND
                        ENLP.cod_entidade = ENLPA.cod_entidade  AND
                        ENLP.cod_nota     = ENLPA.cod_nota      AND
                        ENLP.timestamp    = ENLPA.timestamp
                    )
            '';
        END IF;
    
        stSql := stSql || ''
                WHERE
                    OCD.exercicio = '''''' || stExercicio || '''''' AND
                    coalesce( OD.cod_entidade, 0 ) IN ( 0, '' || stCodEntidade || '' )
        '';
    
        stSql := stSql || ''
                AND   OCD.cod_estrutural NOT LIKE ''''9.0.%''''
                AND   OCD.cod_estrutural NOT LIKE substr( OCD.cod_estrutural,1,3 ) || ''''.9.1.%''''
                GROUP BY OCD.exercicio
                        ,OD.cod_entidade
                        ,OD.cod_despesa
                        ,OCD.cod_estrutural
                        ,OD.vl_original
                        ,OSS.valor
                        ,OSR.valor
                ORDER BY OCD.exercicio
                        ,OD.cod_entidade
                        ,OD.cod_despesa
                        ,OCD.cod_estrutural
                        ,OD.vl_original
            ) AS tabela,
            orcamento.conta_despesa AS OCD
        WHERE
            tabela.estrutural_reduzido = substr( OCD.cod_estrutural, 1, 3 ) AND
            tabela.exercicio           = OCD.exercicio                      AND
            length( publico.fn_mascarareduzida( OCD.cod_estrutural ) ) <= 3
        GROUP BY
            tabela.estrutural_reduzido,
            OCD.descricao,
            OCD.cod_estrutural,
            tabela.cod_despesa,
            tabela.exercicio
    
    UNION 
    
        SELECT
                            cast(tabela.estrutural_reduzido as varchar) as estrutural_reduzido,
                            cast(trim(OCD.descricao) || '''' - OP. INTRA'''' as varchar) as descricao_despesa,
                            sum( tabela.vl_original ) as vl_original,
                            sum( tabela.suplementado )  as vl_suplementado,
                            sum( tabela.reduzido )  as vl_reduzido,
                            sum ( tabela.vl_total ) - sum( tabela.vl_total_anulado ) AS vl_despesa,
                            cast(0.00 as numeric(14,2)) as vl_diferenca,
                            publico.fn_nivel (OCD.cod_estrutural) as nivel,
                            ''''T''''::text as intra,
                            tabela.cod_despesa,
                            tabela.exercicio,
                            CASE WHEN (EXISTS (
                                                SELECT 1 FROM orcamento.suplementacao_suplementada ss1
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = ss1.cod_suplementacao
                                                          AND suplementacao.exercicio         = ss1.exercicio
                                                        WHERE ss1.exercicio         = tabela.exercicio
                                                          AND ss1.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo = 11
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = ss1.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = ss1.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = ss1.exercicio
                                                                              AND o_sa2.cod_suplementacao = ss1.cod_suplementacao
                                                                         )
                                            UNION 
                                                SELECT 1 FROM orcamento.suplementacao_reducao
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                          AND suplementacao.exercicio         = suplementacao_reducao.exercicio
                                                        WHERE suplementacao_reducao.exercicio         = tabela.exercicio
                                                          AND suplementacao_reducao.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo = 11
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = suplementacao_reducao.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = suplementacao_reducao.exercicio
                                                                              AND o_sa2.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                        )
                            ) THEN 3 
                            WHEN (EXISTS (
                                                SELECT 1 FROM orcamento.suplementacao_suplementada
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                                                          AND suplementacao.exercicio         = suplementacao_suplementada.exercicio
                                                        WHERE suplementacao_suplementada.exercicio         = tabela.exercicio
                                                          AND suplementacao_suplementada.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo in (6,7,8,9,10)
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = suplementacao_suplementada.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = suplementacao_suplementada.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = suplementacao_suplementada.exercicio
                                                                              AND o_sa2.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                                                                         )
                                            UNION                   
                                                SELECT 1 FROM orcamento.suplementacao_reducao
                                                   INNER JOIN orcamento.suplementacao
                                                           ON suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                          AND suplementacao.exercicio         = suplementacao_reducao.exercicio
                                                        WHERE suplementacao_reducao.exercicio         = tabela.exercicio
                                                          AND suplementacao_reducao.cod_despesa       = tabela.cod_despesa
                                                          AND suplementacao.cod_tipo in (6,7,8,9,10)
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa
                                                                            WHERE o_sa.exercicio                  = suplementacao_reducao.exercicio
                                                                              AND o_sa.cod_suplementacao_anulacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                                          AND NOT EXISTS ( SELECT 1
                                                                             FROM orcamento.suplementacao_anulada o_sa2
                                                                            WHERE o_sa2.exercicio         = suplementacao_reducao.exercicio
                                                                              AND o_sa2.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                                         )
                                        )
                            ) THEN 2
                            ELSE 1
                            END as classificacao
                        FROM(
                            SELECT
                                substr(OCD.cod_estrutural,1,3) AS estrutural_reduzido,
                                OCD.exercicio,
                                OD.cod_entidade,
                                coalesce( OD.vl_original, 0.00 )              AS vl_original,
                                coalesce(OSS.valor , 0.00 ) as suplementado,
                                coalesce(OSR.valor , 0.00 ) as reduzido,
                                OD.cod_despesa
        '';
    
        IF stTipo = ''Empenhado'' THEN
            stSql := stSql || ''
                ,sum( coalesce( EIPE.vl_total, 0.00 ) )        AS vl_total
                ,sum( coalesce( EEA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
            '';
        END IF;
    
        IF stTipo = ''Liquidado'' THEN
            stSql := stSql || ''
                ,sum( coalesce( ENLI.vl_total, 0.00 ) )    AS vl_total
                ,sum( coalesce( ENLIA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
            '';
        END IF;
    
        IF stTipo = ''Pago'' THEN
            stSql := stSql || ''
                ,sum( coalesce( ENLP.vl_total, 0.00 ) )  AS vl_total
                ,sum( coalesce( ENLPA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
            '';
        END IF;
    
        stSql := stSql || ''
            FROM
                orcamento.conta_despesa AS OCD
                    LEFT JOIN orcamento.despesa  AS OD ON(
                        OCD.exercicio  = OD.exercicio AND
                        OCD.cod_conta  = OD.cod_conta
                    )
                    LEFT JOIN (
                        SELECT
                            OSS.exercicio,
                            OSS.cod_despesa,
                            sum( OSS.valor ) AS valor
                        FROM
                            orcamento.suplementacao_suplementada AS OSS,
                            orcamento.suplementacao              AS OS
                        WHERE
                            OSS.exercicio         = OS.exercicio            AND
                            OSS.cod_suplementacao = OS.cod_suplementacao    AND
                            OS.dt_suplementacao BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                    AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa
                                          WHERE o_sa.exercicio                  = OSS.exercicio
                                            AND o_sa.cod_suplementacao_anulacao = OSS.cod_suplementacao
                                       )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa2
                                          WHERE o_sa2.exercicio         = OSS.exercicio
                                            AND o_sa2.cod_suplementacao = OSS.cod_suplementacao
                                       )
                        GROUP BY
                            OSS.exercicio,
                            OSS.cod_despesa
                        ORDER BY
                            OSS.exercicio,
                            OSS.cod_despesa
                    ) AS OSS ON(
                        OSS.exercicio   = OD.exercicio AND
                        OSS.cod_despesa = OD.cod_despesa
                    )
                    LEFT JOIN (
                        SELECT
                            OSR.exercicio,
                            OSR.cod_despesa,
                            sum( OSR.valor ) AS valor
                        FROM
                            orcamento.suplementacao_reducao AS OSR,
                            orcamento.suplementacao         AS OS
                        WHERE
                            OSR.exercicio         = OS.exercicio            AND
                            OSR.cod_suplementacao = OS.cod_suplementacao
    
    
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa3
                                          WHERE o_sa3.exercicio                  = OSR.exercicio
                                            AND o_sa3.cod_suplementacao_anulacao = OSR.cod_suplementacao
                                       )
    
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa4
                                          WHERE o_sa4.exercicio         = OSR.exercicio
                                            AND o_sa4.cod_suplementacao = OSR.cod_suplementacao
                                       )
    
                        AND OS.dt_suplementacao BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                    AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            OSR.exercicio,
                            OSR.cod_despesa
                        ORDER BY
                            OSR.exercicio,
                            OSR.cod_despesa
                    ) AS OSR ON(
                        OSR.exercicio   = OD.exercicio  AND
                        OSR.cod_despesa = OD.cod_despesa
                    )
                    LEFT JOIN empenho.pre_empenho_despesa AS EPED ON(
                        OD.cod_despesa = EPED.cod_despesa AND
                        OD.exercicio   = EPED.exercicio
                    )
                    LEFT JOIN empenho.pre_empenho AS EPE ON(
                        EPED.cod_pre_empenho = EPE.cod_pre_empenho AND
                        EPED.exercicio       = EPE.exercicio
                    )
                    LEFT JOIN empenho.empenho AS EE ON(
                        EPE.cod_pre_empenho = EE.cod_pre_empenho AND
                        EPE.exercicio       = EE.exercicio
                    )
        '';
    
        IF stTipo = ''Empenhado'' THEN
            stSql := stSql || ''
                    LEFT JOIN (
                        SELECT
                            sum( coalesce( EIPE.vl_total, 0.00 ) ) as vl_total,
                            EIPE.cod_pre_empenho,
                            EIPE.exercicio
                        FROM
                            empenho.item_pre_empenho AS EIPE,
                            empenho.empenho          AS EE
                        WHERE
                            EE.exercicio       = EIPE.exercicio         AND
                            EE.cod_pre_empenho = EIPE.cod_pre_empenho   AND
                            coalesce( EE.dt_empenho , TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                                BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                    AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            EIPE.exercicio,
                            EIPE.cod_pre_empenho
                        ORDER BY
                            EIPE.exercicio,
                            EIPE.cod_pre_empenho
                    ) AS EIPE ON(
                        EPE.cod_pre_empenho  = EIPE.cod_pre_empenho AND
                        EPE.exercicio        = EIPE.exercicio       AND
                        EIPE.cod_pre_empenho = EE.cod_pre_empenho   AND
                        EIPE.exercicio       = EE.exercicio
                    )
    
                    LEFT JOIN (
                        SELECT
                            sum( coalesce( EEAI.vl_anulado, 0.00 ) ) AS vl_total_anulado,
                            EEA.exercicio,
                            EEA.cod_entidade,
                            EEA.cod_empenho
                        FROM
                            empenho.empenho_anulado AS EEA,
                            empenho.empenho_anulado_item AS EEAI
                        WHERE
                            EEA.exercicio    = EEAI.exercicio       AND
                            EEA.cod_entidade = EEAI.cod_entidade    AND
                            EEA.cod_empenho  = EEAI.cod_empenho     AND
                            EEA.timestamp    = EEAI.timestamp       AND
                            coalesce(   TO_DATE( EEA.timestamp::text, ''''yyyy-mm-dd'''' ),
                                        TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) ) BETWEEN
                                        TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND
                                        TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' ) AND
                            EEA.exercicio = '''''' || stExercicio || ''''''
                        GROUP BY
                            EEA.exercicio,
                            EEA.cod_entidade,
                            EEA.cod_empenho
                        ORDER BY
                            EEA.exercicio,
                            EEA.cod_entidade,
                            EEA.cod_empenho
                    ) AS EEA ON(
                        EE.exercicio    = EEA.exercicio     AND
                        EE.cod_entidade = EEA.cod_entidade  AND
                        EE.cod_empenho  = EEA.cod_empenho
                    )
            '';
        END IF;
    
        IF stTipo = ''Liquidado'' THEN
            stSql := stSql || ''
                    LEFT JOIN (
                        SELECT
                            ENL.cod_empenho,
                            ENL.exercicio_empenho,
                            ENL.exercicio,
                            ENL.cod_entidade,
                            ENL.cod_nota
                        FROM
                            empenho.nota_liquidacao AS ENL
                        WHERE
                            coalesce( ENL.dt_liquidacao, TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                                   BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                                       AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENL.cod_empenho,
                            ENL.exercicio_empenho,
                            ENL.exercicio,
                            ENL.cod_entidade,
                            ENL.cod_nota
                        ORDER BY
                            ENL.cod_empenho,
                            ENL.exercicio_empenho,
                            ENL.exercicio,
                            ENL.cod_entidade,
                            ENL.cod_nota
                    ) AS ENL ON(
                        EE.cod_empenho  = ENL.cod_empenho       AND
                        EE.exercicio    = ENL.exercicio_empenho AND
                        EE.cod_entidade = ENL.cod_entidade
                    )
                    LEFT JOIN empenho.nota_liquidacao_item AS ENLI ON(
                        ENL.exercicio    = ENLI.exercicio   AND
                        ENL.cod_nota     = ENLI.cod_nota    AND
                        ENL.cod_entidade = ENLI.cod_entidade
                    )
                    LEFT JOIN (
                        SELECT
                            sum( ENLIA.vl_anulado ) AS vl_total_anulado,
                            ENLIA.exercicio,
                            ENLIA.cod_nota,
                            ENLIA.num_item,
                            ENLIA.exercicio_item,
                            ENLIA.cod_pre_empenho,
                            ENLIA.cod_entidade
                        FROM
                            empenho.nota_liquidacao_item_anulado AS ENLIA
                        WHERE
                            coalesce( TO_DATE( ENLIA.timestamp::text, ''''yyyy-mm-dd'''' ),
                                      TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                      BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                      AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENLIA.exercicio,
                            ENLIA.cod_nota,
                            ENLIA.num_item,
                            ENLIA.exercicio_item,
                            ENLIA.cod_pre_empenho,
                            ENLIA.cod_entidade
                        ORDER BY
                            ENLIA.exercicio,
                            ENLIA.cod_nota,
                            ENLIA.num_item,
                            ENLIA.exercicio_item,
                            ENLIA.cod_pre_empenho,
                            ENLIA.cod_entidade
                    ) AS ENLIA ON(
                        ENLI.cod_entidade    = ENLIA.cod_entidade       AND
                        ENLI.cod_nota        = ENLIA.cod_nota           AND
                        ENLI.exercicio       = ENLIA.exercicio          AND
                        ENLI.cod_pre_empenho = ENLIA.cod_pre_empenho    AND
                        ENLI.exercicio_item  = ENLIA.exercicio_item     AND
                        ENLI.num_item        = ENLIA.num_item
                    )
            '';
        END IF;
    
        IF stTipo = ''Pago'' THEN
            stSql := stSql || ''
                    LEFT JOIN empenho.nota_liquidacao AS ENL ON(
                        EE.cod_empenho  = ENL.cod_empenho       AND
                        EE.exercicio    = ENL.exercicio_empenho AND
                        EE.cod_entidade = ENL.cod_entidade
                    )
                    LEFT JOIN(
                        SELECT
                            ENLP.cod_entidade,
                            ENLP.exercicio,
                            ENLP.cod_nota,
                            ENLP.timestamp,
                            sum( coalesce( ENLP.vl_pago, 0.00 ) ) as vl_total
                        FROM
                            empenho.nota_liquidacao_paga AS ENLP
                        WHERE
                            coalesce( TO_DATE( ENLP.timestamp::text, ''''yyyy-mm-dd'''' ),
                                      TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                      BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                      AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENLP.exercicio,
                            ENLP.cod_entidade,
                            ENLP.cod_nota,
                            ENLP.timestamp
                        ORDER BY
                            ENLP.exercicio,
                            ENLP.cod_entidade,
                            ENLP.cod_nota,
                            ENLP.timestamp
                    ) AS ENLP ON(
                        ENL.exercicio    = ENLP.exercicio       AND
                        ENL.cod_entidade = ENLP.cod_entidade    AND
                        ENL.cod_nota     = ENLP.cod_nota
                    )
                    LEFT JOIN(
                        SELECT
                            ENLPA.exercicio,
                            ENLPA.cod_entidade,
                            ENLPA.cod_nota,
                            ENLPA.timestamp,
                            sum( coalesce( ENLPA.vl_anulado, 0.00 ) ) as vl_total_anulado
                        FROM
                            empenho.nota_liquidacao_paga_anulada AS ENLPA
                        WHERE
                            coalesce( TO_DATE( ENLPA.timestamp_anulada::text, ''''yyyy-mm-dd'''' ),
                                      TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                                      BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
                                          AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                        GROUP BY
                            ENLPA.exercicio,
                            ENLPA.cod_entidade,
                            ENLPA.cod_nota,
                            ENLPA.timestamp
                        ORDER BY
                            ENLPA.exercicio,
                            ENLPA.cod_entidade,
                            ENLPA.cod_nota,
                            ENLPA.timestamp
                    ) AS ENLPA ON(
                        ENLP.exercicio    = ENLPA.exercicio     AND
                        ENLP.cod_entidade = ENLPA.cod_entidade  AND
                        ENLP.cod_nota     = ENLPA.cod_nota      AND
                        ENLP.timestamp    = ENLPA.timestamp
                    )
            '';
        END IF;
    
        stSql := stSql || ''
                WHERE
                    OCD.exercicio = '''''' || stExercicio || '''''' AND
                    coalesce( OD.cod_entidade, 0 ) IN ( 0, '' || stCodEntidade || '' )
        '';
    
        stSql := stSql || ''
                AND   OCD.cod_estrutural NOT LIKE ''''9.0.%''''
                AND   OCD.cod_estrutural LIKE substr( OCD.cod_estrutural,1,3 ) || ''''.9.1.%''''
                GROUP BY OCD.exercicio
                        ,OD.cod_entidade
                        ,OD.cod_despesa
                        ,OCD.cod_estrutural
                        ,OD.vl_original
                        ,OSS.valor
                        ,OSR.valor
                ORDER BY OCD.exercicio
                        ,OD.cod_entidade
                        ,OD.cod_despesa
                        ,OCD.cod_estrutural
                        ,OD.vl_original
            ) AS tabela,
            orcamento.conta_despesa AS OCD
        WHERE
            tabela.estrutural_reduzido = substr( OCD.cod_estrutural, 1, 3 ) AND
            tabela.exercicio           = OCD.exercicio                      AND
            length( publico.fn_mascarareduzida( OCD.cod_estrutural ) ) <= 3
        GROUP BY
            tabela.estrutural_reduzido,
            OCD.descricao,
            OCD.cod_estrutural,
            tabela.cod_despesa,
            tabela.exercicio
    ) AS orcamentario
                
                    GROUP BY
                        estrutural_reduzido,
                        descricao_despesa,
                        nivel,
                        intra,
                        cod_despesa,
                        exercicio,
                        classificacao
                    ORDER BY
                        estrutural_reduzido

         ) as tbl

   WHERE (descricao_despesa <> '''''''' AND (vl_original > 0.00 OR vl_despesa > 0.00) OR nivel = 1)

GROUP BY estrutural_reduzido
       , descricao_despesa
       , nivel
       , intra
       , classificacao
ORDER BY estrutural_reduzido

    )
    '';

    EXECUTE stSql;

    stSql := '' SELECT DISTINCT substr(estrutural_reduzido, 1, 1) AS nivel FROM tmp_despesas_anexo12 '';

    FOR reRegLoop IN EXECUTE stSql
    LOOP

        stSqlClassificacao := '' SELECT DISTINCT classificacao FROM tmp_despesas_anexo12 WHERE substr(estrutural_reduzido, 1, 1)::integer = '' || quote_literal(reRegLoop.nivel) || '' AND classificacao > 1 '';

        FOR reRegClassificacao IN EXECUTE stSqlClassificacao
        LOOP

            stSqlInsert := '' INSERT INTO tmp_despesas_anexo12 VALUES ('' || reRegLoop.nivel || '' || ''''.0'''' , (SELECT descricao_despesa FROM tmp_despesas_anexo12 WHERE substr(estrutural_reduzido, 1, 1) = '' || quote_literal(reRegLoop.nivel) || '' AND nivel = 1 AND classificacao = 1 ), 0.00, 0.00, 0.00, 1, '' || reRegClassificacao.classificacao ||'', ''''F'''' ) '';

            EXECUTE stSqlInsert;

        END LOOP;

    END LOOP;
    

    stSql := '' SELECT DISTINCT substr(estrutural_reduzido, 1, 1) AS nivel FROM tmp_despesas_anexo12 '';

    FOR reRegLoop IN EXECUTE stSql
    LOOP

        stSqlClassificacao := '' SELECT DISTINCT classificacao FROM tmp_despesas_anexo12 WHERE substr(estrutural_reduzido, 1, 1) = '' || quote_literal(reRegLoop.nivel) || '' '';

        FOR reRegClassificacao IN EXECUTE stSqlClassificacao
        LOOP

            stSqlAux := '' UPDATE tmp_despesas_anexo12 SET
                                vl_original = coalesce (( SELECT SUM(COALESCE(vl_original, 0.00)) FROM tmp_despesas_anexo12 WHERE substr(estrutural_reduzido, 1, 1) = '' || quote_literal(reRegLoop.nivel) || '' AND nivel > 1 AND classificacao = '' || quote_literal(reRegClassificacao.classificacao) || '' ) , 0.00 ),
                                vl_despesa = coalesce (( SELECT SUM(COALESCE(vl_despesa, 0.00)) FROM tmp_despesas_anexo12 WHERE substr(estrutural_reduzido, 1, 1) = '' || quote_literal(reRegLoop.nivel) || '' AND nivel > 1 AND classificacao = '' || quote_literal(reRegClassificacao.classificacao) || '' ) , 0.00 )
                           WHERE nivel = 1 AND substr(estrutural_reduzido, 1, 1) = '' || quote_literal(reRegLoop.nivel) || '' AND classificacao = '' || quote_literal(reRegClassificacao.classificacao) || '' '';

            EXECUTE stSqlAux;

        END LOOP;

    END LOOP;

    stSql := '' SELECT DISTINCT estrutural_reduzido FROM tmp_despesas_anexo12 '';

    FOR reRegEstrutural IN EXECUTE stSql
    LOOP

        stSqlClassificacao := '' SELECT classificacao FROM tmp_despesas_anexo12 WHERE estrutural_reduzido = '''''' || reRegEstrutural.estrutural_reduzido || ''''''::varchar '';

        FOR reRegClassificacao IN EXECUTE stSqlClassificacao
        LOOP

            stSqlIntra := '' SELECT intra FROM tmp_despesas_anexo12 WHERE estrutural_reduzido = '''''' || reRegEstrutural.estrutural_reduzido || ''''''::varchar AND classificacao = '' || reRegClassificacao.classificacao || '' '';

            FOR reRegIntra IN EXECUTE stSqlIntra
            LOOP

                stSqlAux := '' UPDATE tmp_despesas_anexo12 SET
                                    vl_diferenca = coalesce ((SELECT vl_original - vl_despesa FROM tmp_despesas_anexo12 WHERE estrutural_reduzido = '''''' || reRegEstrutural.estrutural_reduzido || ''''''::varchar AND intra = '''''' || reRegIntra.intra || '''''' AND classificacao = '' || reRegClassificacao.classificacao || '' ) , 0.00)
                               WHERE estrutural_reduzido = '''''' || reRegEstrutural.estrutural_reduzido || ''''''::varchar AND intra = '''''' || reRegIntra.intra || '''''' AND classificacao = '' || reRegClassificacao.classificacao || '' '';

                EXECUTE stSqlAux;
            END LOOP;

        END LOOP;

    END LOOP;


    stSql := '' SELECT * FROM tmp_despesas_anexo12 ORDER BY estrutural_reduzido'';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_despesas_anexo12;

    RETURN;
END;
' LANGUAGE 'plpgsql';

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
* Casos de uso: uc-02.05.09
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:50  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_rel_modelo7( varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio                 ALIAS FOR $1;
    stCodEntidades              ALIAS FOR $2;
    stDtFinal                   ALIAS FOR $3;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN

    stSql := ''CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            EE.exercicio
           ,CASE WHEN OD.cod_recurso is not null
               THEN OD.cod_recurso
               ELSE ERPE.recurso
            END AS cod_recurso
           ,sum(EIPE.vl_total)          AS valor
        FROM
            empenho.empenho             AS EE
           ,empenho.pre_empenho         AS EPE
           -- Left join com empenho.restos_pre_empenho
           LEFT JOIN empenho.restos_pre_empenho AS ERPE
           ON( EPE.cod_pre_empenho = ERPE.cod_pre_empenho
           AND EPE.exercicio       = ERPE.exercicio       )
           -- Left Join com orcamento.despesa
           LEFT JOIN (
                SELECT
                       EPED.cod_pre_empenho
                      ,EPED.exercicio
                      ,OD.cod_recurso
                FROM
                     empenho.pre_empenho_despesa AS EPED
                    ,orcamento.despesa           AS OD
                WHERE
                     EPED.cod_despesa = OD.cod_despesa
                 AND EPED.exercicio   = OD.exercicio
           ) AS OD
           ON( OD.cod_pre_empenho = EPE.cod_pre_empenho
           AND OD.exercicio       = EPE.exercicio       )
           ,empenho.item_pre_empenho    AS EIPE
        WHERE
            EE.cod_entidade     IN ('' || stCodEntidades || '')
        AND EE.exercicio        <= '' || stExercicio || ''
        AND EE.dt_empenho       <= TO_DATE( '''''' || stDtFinal || '''''', ''''dd/mm/yyyy'''' )

            --Ligação EMPENHO : PRE_EMPENHO
        AND EE.exercicio         = EPE.exercicio
        AND EE.cod_pre_empenho   = EPE.cod_pre_empenho

            --Ligação PRE_EMPENHO : ITEM_PRE_EMPENHO
        AND EPE.exercicio        = EIPE.exercicio
        AND EPE.cod_pre_empenho  = EIPE.cod_pre_empenho

        GROUP BY
            EE.exercicio
           ,OD.cod_recurso
           ,ERPE.recurso
        ORDER BY
            EE.exercicio
           ,OD.cod_recurso
    )'';

    EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_anulado AS (
        SELECT
            EE.exercicio
           ,CASE WHEN OD.cod_recurso is not null
               THEN OD.cod_recurso
               ELSE ERPE.recurso
            END AS cod_recurso
           ,sum(EEAI.vl_anulado) AS valor
        FROM
            empenho.empenho                 AS EE
           ,empenho.pre_empenho             AS EPE
           -- Left join com empenho.restos_pre_empenho
           LEFT JOIN empenho.restos_pre_empenho AS ERPE
           ON( EPE.cod_pre_empenho = ERPE.cod_pre_empenho
           AND EPE.exercicio       = ERPE.exercicio       )
           -- Left Join com orcamento.despesa
           LEFT JOIN (
                SELECT
                       EPED.cod_pre_empenho
                      ,EPED.exercicio
                      ,OD.cod_recurso
                FROM
                     empenho.pre_empenho_despesa AS EPED
                    ,orcamento.despesa           AS OD
                WHERE
                     EPED.cod_despesa = OD.cod_despesa
                 AND EPED.exercicio   = OD.exercicio
           ) AS OD
           ON( OD.cod_pre_empenho = EPE.cod_pre_empenho
           AND OD.exercicio       = EPE.exercicio       )
           ,empenho.empenho_anulado         AS EEA
           ,empenho.empenho_anulado_item    AS EEAI
        WHERE
            EE.cod_entidade     IN ('' || stCodEntidades || '')
        AND EE.exercicio        <= '' || stExercicio || ''
        AND TO_DATE( EEA.timestamp, ''''yyyy-mm-dd'''' ) <= TO_DATE( '''''' || stDtFinal   || '''''', ''''dd/mm/yyyy'''' )

            --Ligação EMPENHO : PRE_EMPENHO
        AND EE.cod_pre_empenho  = EPE.cod_pre_empenho
        AND EE.exercicio        = EPE.exercicio

            --Ligação EMPENHO : EMPENHO ANULADO
        AND EE.exercicio        = EEA.exercicio
        AND EE.cod_entidade     = EEA.cod_entidade
        AND EE.cod_empenho      = EEA.cod_empenho

            --Ligação EMPENHO ANULADO : EMPENHO ANULADO ITEM
        AND EEA.exercicio        = EEAI.exercicio
        AND EEA.timestamp        = EEAI.timestamp
        AND EEA.cod_entidade     = EEAI.cod_entidade
        AND EEA.cod_empenho      = EEAI.cod_empenho

        GROUP BY
            EE.exercicio
           ,OD.cod_recurso
           ,ERPE.recurso
        ORDER BY
            EE.exercicio
           ,OD.cod_recurso
    )'';

    EXECUTE stSql;


    stSql := ''CREATE TEMPORARY TABLE tmp_liquidado AS (
        SELECT
            EE.exercicio
           ,CASE WHEN OD.cod_recurso is not null
               THEN OD.cod_recurso
               ELSE ERPE.recurso
            END AS cod_recurso
           ,sum(ENLI.vl_total)   AS valor
        FROM
            empenho.empenho                 AS EE
           ,empenho.pre_empenho             AS EPE
           -- Left join com empenho.restos_pre_empenho
           LEFT JOIN empenho.restos_pre_empenho AS ERPE
           ON( EPE.cod_pre_empenho = ERPE.cod_pre_empenho
           AND EPE.exercicio       = ERPE.exercicio       )
           -- Left Join com orcamento.despesa
           LEFT JOIN (
                SELECT
                       EPED.cod_pre_empenho
                      ,EPED.exercicio
                      ,OD.cod_recurso
                FROM
                     empenho.pre_empenho_despesa AS EPED
                    ,orcamento.despesa           AS OD
                WHERE
                     EPED.cod_despesa = OD.cod_despesa
                 AND EPED.exercicio   = OD.exercicio
           ) AS OD
           ON( OD.cod_pre_empenho = EPE.cod_pre_empenho
           AND OD.exercicio       = EPE.exercicio       )
           ,empenho.nota_liquidacao         AS ENL
           ,empenho.nota_liquidacao_item    AS ENLI
        WHERE
            EE.cod_entidade      IN ('' || stCodEntidades || '')
        AND EE.exercicio         <= '' || stExercicio || ''
        AND ENL.dt_liquidacao    <= TO_DATE( '''''' || stDtFinal || '''''', ''''dd/mm/yyyy'''' )

            --Ligação EMPENHO : PRE_EMPENHO
        AND EE.cod_pre_empenho   = EPE.cod_pre_empenho
        AND EE.exercicio         = EPE.exercicio

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND EE.exercicio         = ENL.exercicio_empenho
        AND EE.cod_entidade      = ENL.cod_entidade
        AND EE.cod_empenho       = ENL.cod_empenho

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
        AND ENL.exercicio        = ENLI.exercicio
        AND ENL.cod_nota         = ENLI.cod_nota
        AND ENL.cod_entidade     = ENLI.cod_entidade
        GROUP BY
            EE.exercicio
           ,OD.cod_recurso
           ,ERPE.recurso
        ORDER BY
            EE.exercicio
           ,OD.cod_recurso
    )'';

    EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_liquidado_anulado AS (
        SELECT
            EE.exercicio
           ,CASE WHEN OD.cod_recurso is not null
               THEN OD.cod_recurso
               ELSE ERPE.recurso
            END AS cod_recurso
           ,sum(ENLIA.vl_anulado) as valor
        FROM
            empenho.empenho                      AS EE
           ,empenho.pre_empenho                  AS EPE
           -- Left join com empenho.restos_pre_empenho
           LEFT JOIN empenho.restos_pre_empenho AS ERPE
           ON( EPE.cod_pre_empenho = ERPE.cod_pre_empenho
           AND EPE.exercicio       = ERPE.exercicio       )
           -- Left Join com orcamento.despesa
           LEFT JOIN (
                SELECT
                       EPED.cod_pre_empenho
                      ,EPED.exercicio
                      ,OD.cod_recurso
                FROM
                     empenho.pre_empenho_despesa AS EPED
                    ,orcamento.despesa           AS OD
                WHERE
                     EPED.cod_despesa = OD.cod_despesa
                 AND EPED.exercicio   = OD.exercicio
           ) AS OD
           ON( OD.cod_pre_empenho = EPE.cod_pre_empenho
           AND OD.exercicio       = EPE.exercicio       )
           ,empenho.nota_liquidacao              AS ENL
           ,empenho.nota_liquidacao_item         AS ENLI
           ,empenho.nota_liquidacao_item_anulado AS ENLIA
        WHERE
            EE.cod_entidade      IN ('' || stCodEntidades || '')
        AND EE.exercicio         <= '' || stExercicio || ''
        AND TO_DATE( ENLIA.timestamp, ''''yyyy-mm-dd'''' ) <= TO_DATE( '''''' || stDtFinal || '''''', ''''dd/mm/yyyy'''' )

            --Ligação EMPENHO : PRE_EMPENHO
        AND EE.cod_pre_empenho   = EPE.cod_pre_empenho
        AND EE.exercicio         = EPE.exercicio

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND EE.exercicio         = ENL.exercicio_empenho
        AND EE.cod_entidade      = ENL.cod_entidade
        AND EE.cod_empenho       = ENL.cod_empenho

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
        AND ENL.exercicio        = ENLI.exercicio
        AND ENL.cod_nota         = ENLI.cod_nota
        AND ENL.cod_entidade     = ENLI.cod_entidade

            --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
        AND ENLI.exercicio       = ENLIA.exercicio
        AND ENLI.cod_nota        = ENLIA.cod_nota
        AND ENLI.cod_entidade    = ENLIA.cod_entidade
        AND ENLI.num_item        = ENLIA.num_item
        AND ENLI.cod_pre_empenho = ENLIA.cod_pre_empenho
        AND ENLI.exercicio_item  = ENLIA.exercicio_item
        GROUP BY
            EE.exercicio
           ,OD.cod_recurso
           ,ERPE.recurso
        ORDER BY
            EE.exercicio
           ,OD.cod_recurso
    )'';

    EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            EE.exercicio
           ,CASE WHEN OD.cod_recurso is not null
               THEN OD.cod_recurso
               ELSE ERPE.recurso
            END AS cod_recurso
           ,sum(ENLP.vl_pago)    as valor
        FROM
            empenho.empenho                 AS EE
           ,empenho.pre_empenho             AS EPE
           -- Left join com empenho.restos_pre_empenho
           LEFT JOIN empenho.restos_pre_empenho AS ERPE
           ON( EPE.cod_pre_empenho = ERPE.cod_pre_empenho
           AND EPE.exercicio       = ERPE.exercicio       )
           -- Left Join com orcamento.despesa
           LEFT JOIN (
                SELECT
                       EPED.cod_pre_empenho
                      ,EPED.exercicio
                      ,OD.cod_recurso
                FROM
                     empenho.pre_empenho_despesa AS EPED
                    ,orcamento.despesa           AS OD
                WHERE
                     EPED.cod_despesa = OD.cod_despesa
                 AND EPED.exercicio   = OD.exercicio
           ) AS OD
           ON( OD.cod_pre_empenho = EPE.cod_pre_empenho
           AND OD.exercicio       = EPE.exercicio       )
           ,empenho.nota_liquidacao         AS ENL
           ,empenho.nota_liquidacao_paga    AS ENLP
        WHERE
            EE.cod_entidade     IN ('' || stCodEntidades || '')
        AND EE.exercicio         <= '' || stExercicio || ''
        AND TO_CHAR( ENLP.timestamp, ''''yyyy-mm-dd'''' ) <= TO_DATE( '''''' || stDtFinal || '''''', ''''dd/mm/yyyy'''' )

            --Ligação EMPENHO : EMPENHO_PRE_EMPENHO
        AND EE.cod_pre_empenho   = EPE.cod_pre_empenho
        AND EE.exercicio         = EPE.exercicio

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND EE.exercicio         = ENL.exercicio_empenho
        AND EE.cod_entidade      = ENL.cod_entidade
        AND EE.cod_empenho       = ENL.cod_empenho

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
        AND ENL.exercicio        = ENLP.exercicio
        AND ENL.cod_nota         = ENLP.cod_nota
        AND ENL.cod_entidade     = ENLP.cod_entidade
        GROUP BY
            EE.exercicio
           ,OD.cod_recurso
           ,ERPE.recurso
        ORDER BY
            EE.exercicio
           ,OD.cod_recurso
    )'';

    EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            EE.exercicio
           ,CASE WHEN OD.cod_recurso is not null
               THEN OD.cod_recurso
               ELSE ERPE.recurso
            END AS cod_recurso
           ,sum(ENLPA.vl_anulado)    AS valor
        FROM
            empenho.empenho                         AS EE
           ,empenho.pre_empenho                     AS EPE
           -- Left join com empenho.restos_pre_empenho
           LEFT JOIN empenho.restos_pre_empenho AS ERPE
           ON( EPE.cod_pre_empenho = ERPE.cod_pre_empenho
           AND EPE.exercicio       = ERPE.exercicio       )
           -- Left Join com orcamento.despesa
           LEFT JOIN (
                SELECT
                       EPED.cod_pre_empenho
                      ,EPED.exercicio
                      ,OD.cod_recurso
                FROM
                     empenho.pre_empenho_despesa AS EPED
                    ,orcamento.despesa           AS OD
                WHERE
                     EPED.cod_despesa = OD.cod_despesa
                 AND EPED.exercicio   = OD.exercicio
           ) AS OD
           ON( OD.cod_pre_empenho = EPE.cod_pre_empenho
           AND OD.exercicio       = EPE.exercicio       )
           ,empenho.nota_liquidacao                 AS ENL
           ,empenho.nota_liquidacao_paga            AS ENLP
           ,empenho.nota_liquidacao_paga_anulada    AS ENLPA
        WHERE
            EE.cod_entidade          IN ('' || stCodEntidades || '')
        AND EE.exercicio             <= '' || stExercicio || ''
        AND TO_DATE( ENLPA.timestamp_anulada, ''''yyyy-mm-dd'''' ) <= TO_DATE( '''''' || stDtFinal || '''''', ''''dd/mm/yyyy'''' )

            --Ligação EMPENHO : PRE_EMPENHO
        AND EE.cod_pre_empenho       = EPE.cod_pre_empenho
        AND EE.exercicio             = EPE.exercicio

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND EE.exercicio             = ENL.exercicio_empenho
        AND EE.cod_entidade          = ENL.cod_entidade
        AND EE.cod_empenho           = ENL.cod_empenho

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
        AND ENL.exercicio            = ENLP.exercicio
        AND ENL.cod_nota             = ENLP.cod_nota
        AND ENL.cod_entidade         = ENLP.cod_entidade

            --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
        AND ENLP.exercicio           = ENLPA.exercicio
        AND ENLP.cod_nota            = ENLPA.cod_nota
        AND ENLP.cod_entidade        = ENLPA.cod_entidade
        AND ENLP.timestamp           = ENLPA.timestamp
        GROUP BY
            EE.exercicio
           ,OD.cod_recurso
           ,ERPE.recurso
        ORDER BY
            EE.exercicio
           ,OD.cod_recurso
    )'';
    EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_saldo AS (
        SELECT CPR.cod_recurso
              ,CPB.exercicio
              ,sum( coalesce( CVLD.valor, 0.00 ) ) + sum( coalesce( CVLC.valor, 0.00 ) ) as valor
        FROM contabilidade.plano_banco      AS CPB
            ,contabilidade.plano_analitica  AS CPA
            -- LEFT JOIN com valor debito
            LEFT JOIN (
                SELECT CCD.exercicio
                      ,CCD.cod_plano
                      ,sum( coalesce( CVL.vl_lancamento, 0.00 ) ) AS valor
                FROM contabilidade.conta_debito     AS CCD
                    ,contabilidade.valor_lancamento AS CVL
                    ,contabilidade.lote             AS CL
                WHERE CCD.tipo_valor   = ''''D''''
                -- Join conta_debito : valor_lancamento
                  AND CCD.cod_lote     = CVL.cod_lote
                  AND CCD.tipo         = CVL.tipo
                  AND CCD.sequencia    = CVL.sequencia
                  AND CCD.exercicio    = CVL.exercicio
                  AND CCD.tipo_valor   = CVL.tipo_valor
                  AND CCD.cod_entidade = CVL.cod_entidade
                -- Join valor_lancamento : lote
                  AND CVL.cod_lote     = CL.cod_lote
                  AND CVL.tipo         = CL.tipo
                  AND CVL.exercicio    = CL.exercicio
                  AND CVL.cod_entidade = CL.cod_entidade
                -- Filtros
                  AND CCD.exercicio     = '''''' || stExercicio || ''''''
                  AND CCD.cod_entidade IN ( '' || stCodEntidades || '' )
                  AND CL.dt_lote       <= TO_DATE( '''''' || stDtFinal || '''''', ''''dd/mm/yyyy'''' )
                GROUP BY CCD.exercicio
                        ,CCD.cod_plano
                ORDER BY CCD.exercicio
                        ,CCD.cod_plano
            ) AS CVLD ON (
                CPA.cod_plano = CVLD.cod_plano
            AND CPA.exercicio = CVLD.exercicio
            )
            -- LEFT JOIN com valor credito
            LEFT JOIN (
                SELECT CCC.exercicio
                      ,CCC.cod_plano
                      ,sum( coalesce( CVL.vl_lancamento, 0.00 ) ) AS valor
                FROM contabilidade.conta_credito    AS CCC
                    ,contabilidade.valor_lancamento AS CVL
                    ,contabilidade.lote             AS CL
                WHERE CCC.tipo_valor   = ''''C''''
                -- Join conta_debito : valor_lancamento
                  AND CCC.cod_lote     = CVL.cod_lote
                  AND CCC.tipo         = CVL.tipo
                  AND CCC.sequencia    = CVL.sequencia
                  AND CCC.exercicio    = CVL.exercicio
                  AND CCC.tipo_valor   = CVL.tipo_valor
                  AND CCC.cod_entidade = CVL.cod_entidade
                -- Join valor_lancamento : lote
                  AND CVL.cod_lote     = CL.cod_lote
                  AND CVL.tipo         = CL.tipo
                  AND CVL.exercicio    = CL.exercicio
                  AND CVL.cod_entidade = CL.cod_entidade
                -- Filtros
                  AND CCC.exercicio     = '''''' || stExercicio || ''''''
                  AND CCC.cod_entidade IN ( '' || stCodEntidades || '' )
                  AND CL.dt_lote       <= TO_DATE( '''''' || stDtFinal || '''''', ''''dd/mm/yyyy'''' )
                GROUP BY CCC.exercicio
                        ,CCC.cod_plano
                ORDER BY CCC.exercicio
                        ,CCC.cod_plano
            ) AS CVLC ON (
                CPA.cod_plano = CVLC.cod_plano
            AND CPA.exercicio = CVLC.exercicio
            )
           ,contabilidade.plano_recurso     AS CPR
           --Ligação plano_banco : plano_analitica
        WHERE CPB.cod_plano = CPA.cod_plano
          AND CPB.exercicio = CPA.exercicio
          --Ligação plano_analitica : plano_recurso
          AND CPA.exercicio = CPR.exercicio
          AND CPA.cod_plano = CPR.cod_plano
          --Filtro
          AND CPB.exercicio = '''''' || stExercicio || ''''''
        GROUP BY CPR.cod_recurso
                ,CPB.exercicio
        ORDER BY CPR.cod_recurso
                ,CPB.exercicio
    )
    '';

    EXECUTE stSql;

    stSql := ''
        SELECT
            EE.exercicio
           ,EE.cod_recurso
           ,EE.valor                 AS vl_empenhado
           ,EA.valor                 AS vl_anulado
           ,EL.valor                 AS vl_liquidado
           ,LA.valor                 AS vl_liquidado_anulado
           ,EP.valor                 AS vl_pago
           ,PA.valor                 AS vl_estornado
           ,SUM( ARML.vl_ajuste  )   AS vl_lq_ajustado
           ,SUM( ARMNL.vl_ajuste )   AS vl_n_lq_ajustado
           ,SA.valor                 AS vl_saldo
           ,SUM( ARMS.vl_ajuste  )   AS vl_saldo_ajustado
        FROM
            tmp_empenhado          AS EE

        -- Empenho : Empenho Anulado
        LEFT JOIN tmp_anulado  AS EA
        ON ( EE.exercicio    = EA.exercicio
        AND EE.cod_recurso  = EA.cod_recurso  )

        -- Empenho : Liquidacao
        LEFT JOIN tmp_liquidado AS EL
        ON( EE.exercicio    = EL.exercicio
        AND EE.cod_recurso  = EL.cod_recurso )

        -- Liquidacao : Liquidacao Anulada
        LEFT JOIN tmp_liquidado_anulado  AS LA
        ON( EL.exercicio    = LA.exercicio
        AND EL.cod_recurso  = LA.cod_recurso  )

        -- Liquidacao : Pagamento
        LEFT JOIN tmp_pago AS EP
        ON( EL.exercicio    = EP.exercicio
        AND EL.cod_recurso  = EP.cod_recurso  )

        -- Pagamento : Pagamento Anulado
        LEFT JOIN tmp_estornado AS PA
        ON( EP.exercicio    = PA.exercicio
        AND EP.cod_recurso  = PA.cod_recurso  )

        -- Empenhado : Saldo
        LEFT JOIN tmp_saldo AS SA
        ON( EE.exercicio   = SA.exercicio
        AND EE.cod_recurso = SA.cod_recurso )

        -- Valor Ajustado de Recurso Liquidado
        LEFT JOIN tcers.ajuste_recurso_modelo_lrf AS ARML
        ON( EE.cod_recurso  = ARML.cod_recurso
        AND EE.exercicio    = ARML.exercicio
        AND ARML.cod_modelo = 7
        AND ARML.cod_quadro = 1
        AND ARML.mes       <= CAST( SUBSTR( '''''' || stDtFinal || '''''', 4, 2 ) AS integer )
        )

        -- Valor Ajustado de Recurso não Liquidado
        LEFT JOIN tcers.ajuste_recurso_modelo_lrf AS ARMNL
        ON( EE.cod_recurso   = ARMNL.cod_recurso
        AND EE.exercicio     = ARMNL.exercicio
        AND ARMNL.cod_modelo = 7
        AND ARMNL.cod_quadro = 2
        AND ARMNL.mes       <= CAST( SUBSTR( '''''' || stDtFinal || '''''', 4, 2 ) AS integer )
        )

        -- Valor Ajustado de Recurso não Liquidado
        LEFT JOIN tcers.ajuste_recurso_modelo_lrf AS ARMS
        ON( EE.cod_recurso  = ARMS.cod_recurso
        AND EE.exercicio    = ARMS.exercicio
        AND ARMS.cod_modelo = 7
        AND ARMS.cod_quadro = 3
        AND ARMS.mes       <= CAST( SUBSTR( '''''' || stDtFinal || '''''', 4, 2 ) AS integer )
        )

        GROUP BY EE.exercicio
                ,EE.cod_recurso
                ,EE.valor
                ,EA.valor
                ,EL.valor
                ,LA.valor
                ,EP.valor
                ,PA.valor
                ,SA.valor

        ORDER BY EE.cod_recurso
                ,EE.exercicio
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_liquidado_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;
    DROP TABLE tmp_saldo;

    RETURN;
END;
'language 'plpgsql';

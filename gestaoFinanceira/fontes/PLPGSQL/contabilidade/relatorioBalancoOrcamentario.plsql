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
* $Revision: 28801 $
* $Name$
* $Author: eduardoschitz $
* $Date: 2008-03-26 17:57:40 -0300 (Qua, 26 Mar 2008) $
*
* Casos de uso: uc-02.02.09
*/

/*
$Log$
Revision 1.13  2006/07/18 20:09:51  andre.almeida
Bug #6556#

Revision 1.12  2006/07/14 17:58:30  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.11  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_balanco_orcamentario(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio            ALIAS FOR $1;
    stCodEntidade          ALIAS FOR $2;
    stDtInicial            ALIAS FOR $3;
    stDtFilnal             ALIAS FOR $4;
    stTipo                 ALIAS FOR $5;
    stSql                  VARCHAR   := '''';
    reRegistro             RECORD;

BEGIN

stSql := ''
SELECT * FROM (
    SELECT
        cast(tabela.estrutural_reduzido_receita as varchar ) as estrutural_reduzido_receita,
        OCR.descricao AS descricao_receita,
        sum( tabela.vl_original ) as vl_inicial_receita,
        sum( tabela.vl_arrecadado_debito ) + sum(  tabela.vl_arrecadado_credito ) as vl_atual_receita,
        ''''''''   AS estrutural_reduzido_despesa,
        ''''''''   AS descricao_despesa,
        0.00 AS vl_inicial_despesa,
        0.00 AS vl_atual_despesa,
        publico.fn_nivel (OCR.cod_estrutural),
        '''''''' as tipo
    FROM(
        SELECT
            substr( OCR.cod_estrutural, 1, 3 ) AS estrutural_reduzido_receita,
            OCR.exercicio,
            coalesce( ORE.vl_original,0.00 )                     AS vl_original,
            sum( coalesce( CVLD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito,
            sum( coalesce( CVLC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito
        FROM
            orcamento.conta_receita AS OCR
                LEFT JOIN orcamento.receita  AS ORE ON(
                    OCR.cod_conta = ORE.cod_conta   AND
                    OCR.exercicio = ORE.exercicio
                )
                LEFT JOIN contabilidade.lancamento_receita AS CLR ON(
                    ORE.exercicio    = CLR.exercicio    AND
                    ORE.cod_entidade = CLR.cod_entidade AND
                    ORE.cod_receita  = CLR.cod_receita
                )
                LEFT JOIN (
                    SELECT
                        CLO.cod_lote,
                        CLO.exercicio,
                        CLO.tipo,
                        CLO.cod_entidade,
                        CLO.dt_lote
                    FROM
                        contabilidade.lote AS CLO
                    GROUP BY
                        CLO.cod_lote,
                        CLO.exercicio,
                        CLO.tipo,
                        CLO.cod_entidade,
                        CLO.dt_lote
                    ORDER BY
                        CLO.cod_lote,
                        CLO.exercicio,
                        CLO.tipo,
                        CLO.cod_entidade,
                        CLO.dt_lote
                ) AS CLO ON(
                    CLR.cod_lote     = CLO.cod_lote     AND
                    CLR.exercicio    = CLO.exercicio    AND
                    CLR.tipo         = CLO.tipo         AND
                    CLR.cod_entidade = CLO.cod_entidade
                )
                    LEFT JOIN contabilidade.valor_lancamento AS CVLD ON(
                        CLR.cod_lote       = CVLD.cod_lote      AND
                        CLR.tipo           = CVLD.tipo          AND
                        CLR.sequencia      = CVLD.sequencia     AND
                        CLR.exercicio      = CVLD.exercicio     AND
                        CLR.cod_entidade   = CVLD.cod_entidade  AND
                        CLR.estorno       = true                AND
                        CVLD.tipo         = ''''A''''           AND
                        CVLD.tipo_valor   = ''''D''''           AND
                        coalesce( CLO.dt_lote, TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ))
                        BETWEEN TO_DATE('''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND
                        TO_DATE('''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                    )
                    LEFT JOIN contabilidade.valor_lancamento AS CVLC ON(
                        CLR.cod_lote       = CVLC.cod_lote      AND
                        CLR.tipo           = CVLC.tipo          AND
                        CLR.sequencia      = CVLC.sequencia     AND
                        CLR.exercicio      = CVLC.exercicio     AND
                        CLR.cod_entidade   = CVLC.cod_entidade  AND
                        CLR.estorno       = false               AND
                        CVLC.tipo         = ''''A''''           AND
                        CVLC.tipo_valor   = ''''C''''           AND
                        coalesce( CLO.dt_lote,  TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) )
                        BETWEEN TO_DATE('''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND
                        TO_DATE('''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
                    )
        WHERE
            OCR.exercicio = '''''' || stExercicio || '''''' AND
            coalesce( ORE.cod_entidade, 0 ) IN ( 0, '' || stCodEntidade || '' )
        GROUP BY
            OCR.cod_estrutural,
            OCR.exercicio,
            ORE.cod_entidade,
            ORE.vl_original
        ORDER BY
            OCR.cod_estrutural,
            OCR.exercicio,
            ORE.cod_entidade,
            ORE.vl_original
        ) AS tabela,
        orcamento.conta_receita AS OCR
    WHERE
        tabela.estrutural_reduzido_receita = substr( OCR.cod_estrutural, 1, 3 ) AND
        tabela.exercicio           = OCR.exercicio                              AND
        length(publico.fn_mascarareduzida(OCR.cod_estrutural)) <= 3
    GROUP BY
        tabela.estrutural_reduzido_receita,
        OCR.descricao,
        OCR.cod_estrutural

UNION

    SELECT
        '''''''',
        '''''''',
        0.00,
        0.00,
        cast(tabela.estrutural_reduzido as varchar) as estrutural_reduzido,
        OCD.descricao,
        sum( tabela.vl_original ) + sum(tabela.vl_suplementado) - sum(tabela.vl_reduzido) as vl_original,
        ( sum ( tabela.vl_total ) - sum( tabela.vl_total_anulado ) ) AS vl_despesa,
        publico.fn_nivel (OCD.cod_estrutural),
        cast(substr (tabela.estrutural_reduzido, 1,1) as varchar) as tipo
    FROM(
        SELECT
            substr( OCD.cod_estrutural,1,3 ) AS estrutural_reduzido,
            OCD.exercicio,
            OD.cod_entidade,
            coalesce( OD.vl_original, 0.00 )              AS vl_original,
            coalesce(OSS.valor, 0.00) as vl_suplementado,
            coalesce(OSR.valor , 0.00 ) as vl_reduzido
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


--    IF stTipo = ''Liquidado'' THEN
--
--    stSql := stSql || ''
--                AND   coalesce( ENL.dt_liquidacao, '''''' || stDtInicial || '''''' )  BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
--                                                                                          AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
--    '';
--
--    END IF;


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
        OCD.cod_estrutural

UNION 

    SELECT
        '''''''',
        '''''''',
        0.00,
        0.00,
        cast(tabela.estrutural_reduzido as varchar) as estrutural_reduzido,
        trim(OCD.descricao) || '''' - OP. INTRA'''' as descricao,
        sum( tabela.vl_original ) + sum(tabela.vl_suplementado) - sum(tabela.vl_reduzido) as vl_original,
        ( sum ( tabela.vl_total ) - sum( tabela.vl_total_anulado ) ) AS vl_despesa,
        publico.fn_nivel (OCD.cod_estrutural),
        cast(substr (tabela.estrutural_reduzido, 1,3) as varchar) as tipo
    FROM(
        SELECT
            substr( OCD.cod_estrutural,1,3 ) AS estrutural_reduzido,
            OCD.exercicio,
            OD.cod_entidade,
            coalesce( OD.vl_original, 0.00 )              AS vl_original,
            coalesce(OSS.valor, 0.00) as vl_suplementado,
            coalesce(OSR.valor , 0.00 ) as vl_reduzido
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


--    IF stTipo = ''Liquidado'' THEN
--
--    stSql := stSql || ''
--                AND   coalesce( ENL.dt_liquidacao, '''''' || stDtInicial || '''''' )  BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' )
--                                                                                          AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
--    '';
--
--    END IF;


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
        OCD.cod_estrutural
) AS orcamentario
WHERE
    (descricao_receita <> '''''''' AND (vl_inicial_receita > 0 OR vl_atual_receita > 0))
    OR
    (descricao_despesa <> '''''''' AND (vl_inicial_despesa > 0 OR vl_atual_despesa > 0) OR fn_nivel = 1)
ORDER BY
    tipo
'';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;
END;
' LANGUAGE 'plpgsql';

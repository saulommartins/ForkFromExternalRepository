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
* Casos de uso: uc-02.02.10
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_balanco_financeiro(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS '
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
    SELECT
        tabela.cod_funcao,
        ORF.descricao,
        sum(tabela.vl_original) AS vl_original,
        sum( tabela.vl_total ) - sum( tabela.vl_total_anulado ) AS vl_despesa
    FROM(
        SELECT
            ORF.cod_funcao,
            OCD.exercicio,
            OD.cod_entidade,
            coalesce( OD.vl_original, 0.00 ) AS vl_original'';

    IF stTipo = ''E'' THEN
        stSql := stSql || ''
            ,sum( coalesce( EIPE.vl_total, 0.00 ) )        AS vl_total
            ,sum( coalesce( EEA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
        '';
    END IF;

    IF stTipo = ''L'' THEN
        stSql := stSql || ''
            ,sum( coalesce( ENLI.vl_total, 0.00 ) )          AS vl_total
            ,sum( coalesce( ENLIA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
        '';
    END IF;

    IF stTipo = ''P'' THEN
        stSql := stSql || ''
            ,sum( coalesce( ENLP.vl_total, 0.00 ) )  AS vl_total
            ,sum( coalesce( ENLPA.vl_total_anulado, 0.00 ) ) AS vl_total_anulado
        '';
    END IF;

    stSql := stSql || ''
        FROM
            orcamento.funcao   AS ORF
                LEFT JOIN orcamento.despesa  AS OD ON(
                    ORF.exercicio  = OD.exercicio   AND
                    ORF.cod_funcao = OD.cod_funcao )
                LEFT JOIN orcamento.conta_despesa AS OCD ON(
                    OD.exercicio   = OCD.exercicio  AND
                    OD.cod_conta   = OCD.cod_conta )
                LEFT JOIN empenho.pre_empenho_despesa AS EPED ON (
                    OD.cod_despesa = EPED.cod_despesa AND
                    OD.exercicio   = EPED.exercicio  )
                LEFT JOIN empenho.pre_empenho AS EPE ON(
                    EPED.cod_pre_empenho = EPE.cod_pre_empenho  AND
                    EPED.exercicio       = EPE.exercicio )
                LEFT JOIN empenho.empenho AS EE ON(
                    EPE.cod_pre_empenho = EE.cod_pre_empenho    AND
                    EPE.exercicio       = EE.exercicio )
    '';

    IF stTipo = ''E'' THEN
        stSql := stSql || ''
               LEFT JOIN (
                    SELECT
                        sum(coalesce( EIPE.vl_total, 0.00 ) ) as vl_total,
                        EIPE.cod_pre_empenho,
                        EIPE.exercicio,
                    FROM
                        empenho.item_pre_empenho AS EIPE
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
                    EIPE.exercicio       = EE.exercicio         AND
                    EE.dt_empenho BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
               )
               LEFT JOIN (
                    SELECT
                        sum( coalesce( EEAI.vl_anulado, 0.00 ) ) AS vl_total_anulado,
                        EEA.exercicio,
                        EEA.cod_entidade,
                        EEA.cod_empenho,
                    FROM
                        empenho.empenho_anulado AS EEA,
                        empenho.empenho_anulado_item AS EEAI
                    WHERE
                        EEA.exercicio    = EEAI.exercicio       AND
                        EEA.cod_entidade = EEAI.cod_entidade    AND
                        EEA.cod_empenho  = EEAI.cod_empenho     AND
                        EEA.timestamp    = EEAI.timestamp       AND
                        TO_DATE( EEA.timestamp, ''''yyyy-mm-dd'''' ) BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
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

    IF stTipo = ''L'' THEN
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
                    ENL.dt_liquidacao BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
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
                ENL.exercicio    = ENLI.exercicio       AND
                ENL.cod_nota     = ENLI.cod_nota        AND
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
                    TO_DATE( ENLIA.timestamp, ''''yyyy-mm-dd'''' ) BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
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

    IF stTipo = ''P'' THEN
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
                    TO_DATE( ENLP.timestamp, ''''yyyy-mm-dd'''' ) BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
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
                    TO_DATE( ENLPA.timestamp_anulada, ''''yyyy-mm-dd'''' ) BETWEEN TO_DATE( '''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''' ) AND TO_DATE( '''''' || stDtFilnal  || '''''', ''''dd/mm/yyyy'''' )
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
            OCD.exercicio = '''''' || stExercicio || ''''''     AND
            OD.cod_entidade IN ( '' || stCodEntidade || '' )
        GROUP BY
            OCD.exercicio,
            OD.cod_entidade,
            ORF.cod_funcao,
            OD.cod_despesa,
            OD.vl_original
        ORDER BY
            OCD.exercicio,
            OD.cod_entidade,
            ORF.cod_funcao,
            OD.cod_despesa,
            OD.vl_original
        ) AS tabela,
        orcamento.funcao AS ORF
    WHERE
        tabela.cod_funcao = ORF.cod_funcao  AND
        tabela.exercicio  = ORF.exercicio
    GROUP BY
        tabela.cod_funcao,
        ORF.descricao
    ORDER BY
        ORF.descricao
    '';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;
END;
' LANGUAGE 'plpgsql';

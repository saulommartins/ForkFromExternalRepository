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

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_balanco_orcamentario_receitas(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio            ALIAS FOR $1;
    stCodEntidade          ALIAS FOR $2;
    stDtInicial            ALIAS FOR $3;
    stDtFilnal             ALIAS FOR $4;
    stTipo                 ALIAS FOR $5;
    stSql                  VARCHAR   := '''';
    stSqlAux               VARCHAR   := '''';
    reRegistro             RECORD;
    reRegLoop              RECORD;
    reRegAux               RECORD;

BEGIN

    stSql := ''
    CREATE TEMPORARY TABLE tmp_receitas_anexo12 AS (
    SELECT * FROM (
        SELECT
            cast(tabela.estrutural_reduzido_receita as varchar ) as estrutural_reduzido_receita,
            OCR.descricao AS descricao_receita,
            sum( tabela.vl_original ) as vl_inicial_receita,
            sum( tabela.vl_arrecadado_debito ) + sum(  tabela.vl_arrecadado_credito ) as vl_atual_receita,
            0.00 as vl_diferenca, 
            publico.fn_nivel (OCR.cod_estrutural) as nivel
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
    
    ) AS orcamentario
    WHERE
        (descricao_receita <> '''''''' AND (vl_inicial_receita <> 0 OR vl_atual_receita <> 0) OR nivel = 1)
    ORDER BY
        estrutural_reduzido_receita
    )
    '';

    EXECUTE stSql;


    stSql := '' SELECT DISTINCT substr(estrutural_reduzido_receita, 1, 1) AS nivel FROM tmp_receitas_anexo12 '';

    FOR reRegLoop IN EXECUTE stSql
    LOOP 
        stSqlAux := '' UPDATE tmp_receitas_anexo12 SET
                            vl_inicial_receita = coalesce (( SELECT SUM(COALESCE(vl_inicial_receita, 0.00)) FROM tmp_receitas_anexo12 WHERE substr(estrutural_reduzido_receita, 1, 1) = '' || reRegLoop.nivel || ''::varchar AND nivel > 1 ) , 0.00 ), 
                            vl_atual_receita = coalesce (( SELECT SUM(COALESCE(vl_atual_receita, 0.00)) FROM tmp_receitas_anexo12 WHERE substr(estrutural_reduzido_receita, 1, 1) = '' || reRegLoop.nivel || ''::varchar AND nivel > 1 ) , 0.00 )
                       WHERE nivel = 1 AND substr(estrutural_reduzido_receita, 1, 1) = '' || reRegLoop.nivel || ''::varchar '';
        EXECUTE stSqlAux;

    END LOOP;

    stSql := '' SELECT estrutural_reduzido_receita FROM tmp_receitas_anexo12 '';
    
    FOR reRegAux IN EXECUTE stSql
    LOOP
        stSqlAux := '' UPDATE tmp_receitas_anexo12 SET
                            vl_diferenca = coalesce ((SELECT vl_inicial_receita - vl_atual_receita FROM tmp_receitas_anexo12 WHERE estrutural_reduzido_receita = '' || reRegAux.estrutural_reduzido_receita || ''::varchar) , 0.00)
                       WHERE estrutural_reduzido_receita = '' || reRegAux.estrutural_reduzido_receita || ''::varchar '';

        EXECUTE stSqlAux;

    END LOOP;


    stSql := ''SELECT * FROM tmp_receitas_anexo12 ORDER BY estrutural_reduzido_receita'';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_receitas_anexo12;

    RETURN;
END;
' LANGUAGE 'plpgsql';

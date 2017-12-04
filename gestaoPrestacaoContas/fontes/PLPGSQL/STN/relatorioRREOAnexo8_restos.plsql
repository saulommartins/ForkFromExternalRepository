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
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 8 a partir de 2015
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: relatorioRREOAnexo8_restos.plsql 66357 2016-08-17 14:31:50Z michel $
*
* Casos de uso: uc-06.01.10
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo8_restos( stExercicio VARCHAR, stDtFim VARCHAR, stEntidades VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE

    --stDtFim         VARCHAR := '';
    stDtIni         VARCHAR := '';
    --arDatas         VARCHAR[] ;

    stExMin         VARCHAR := '';
    stDtExMinIni    VARCHAR := '';

    reRegistro      RECORD;
    stSql           VARCHAR := '';

BEGIN

    stDtIni := '01/01/' || stExercicio;
    --arDatas := publico.bimestre ( stExercicio, inBimestre );
    --stDtFim := arDatas [ 1 ];
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_rreo_resto AS (
    SELECT
        emp.exercicio,
        emp.num_orgao,
        emp.cod_funcao, 
        emp.vl_empenhado,
        CASE WHEN vinculo_recurso.cod_tipo IS NOT NULL
             THEN 2
             ELSE 1
        END AS nivel,
        COALESCE(pag.vl_pago, 0.00) AS vl_pago, 
        COALESCE(canc.vl_cancelado, 0.00) AS vl_cancelado  
    FROM 
        
        (SELECT
            pe.exercicio,
            CASE
                WHEN pe.implantado IS TRUE THEN rpe.num_orgao 
                ELSE ped.num_orgao
            END AS num_orgao,
            CASE
                WHEN pe.implantado IS TRUE THEN rpe.cod_funcao  
                ELSE ped.cod_funcao 
            END AS cod_funcao,
            CASE
                WHEN pe.implantado IS TRUE THEN rpe.cod_recurso 
                ELSE ped.cod_recurso
            END AS cod_recurso,
            pe.cod_pre_empenho,
            e.cod_entidade,
            e.cod_empenho, 
            (SUM(COALESCE(ipe.vl_total,0.00)) - SUM(COALESCE(eai.vl_anulado,0.00)) ) AS vl_empenhado 
        FROM
            empenho.empenho e 
            INNER JOIN
            empenho.pre_empenho pe ON
                pe.exercicio = e.exercicio AND
                pe.cod_pre_empenho = e.cod_pre_empenho 
            INNER JOIN
            empenho.item_pre_empenho ipe ON
                ipe.exercicio = pe.exercicio AND
                ipe.cod_pre_empenho = pe.cod_pre_empenho 
            LEFT JOIN
            (SELECT
                eai.exercicio,
                eai.cod_pre_empenho,
                eai.num_item,
                SUM(eai.vl_anulado) AS vl_anulado
            FROM
                empenho.empenho_anulado_item eai 
            WHERE
                TO_DATE(eai.timestamp::VARCHAR,''yyyy-mm-dd'') <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
            GROUP BY
                eai.exercicio,
                eai.cod_pre_empenho,
                eai.num_item
            ) AS eai ON
                eai.exercicio = ipe.exercicio AND
                eai.cod_pre_empenho = ipe.cod_pre_empenho AND
                eai.num_item = ipe.num_item
            
            LEFT JOIN
            (SELECT
                d.exercicio,
                d.cod_funcao,
                d.cod_subfuncao,
                d.num_orgao, 
                ped.cod_pre_empenho,
                d.cod_recurso
            FROM
                empenho.pre_empenho_despesa ped
                INNER JOIN
                orcamento.despesa d ON
                    d.exercicio = ped.exercicio AND
                    d.cod_despesa = ped.cod_despesa 
            WHERE
                d.exercicio < ''' || stExercicio || '''  
            ) AS ped ON
                ped.exercicio = pe.exercicio AND
                ped.cod_pre_empenho = pe.cod_pre_empenho 
            
            LEFT JOIN
            (SELECT
                rpe.exercicio,
                rpe.cod_funcao,
                rpe.cod_subfuncao,
                rpe.num_orgao,
                rpe.cod_pre_empenho,
                rpe.recurso AS cod_recurso
            FROM
                empenho.restos_pre_empenho rpe 
            WHERE
                rpe.exercicio < ''' || stExercicio || ''' 
            ) AS rpe ON 
                rpe.exercicio = pe.exercicio AND
                rpe.cod_pre_empenho = pe.cod_pre_empenho
        WHERE 
            e.exercicio < ''' || stExercicio || ''' AND
            e.dt_empenho < TO_DATE(''' || stDtIni || ''', ''dd/mm/yyyy'') AND
            e.cod_entidade IN (' || stEntidades || ') 
        GROUP BY 
            pe.exercicio,
            pe.cod_pre_empenho,
            e.cod_entidade,
            e.cod_empenho,
            pe.implantado,
            rpe.num_orgao,
            ped.num_orgao,
            rpe.cod_funcao,
            ped.cod_funcao,
            rpe.cod_recurso,
            ped.cod_recurso
        ) AS emp

        LEFT JOIN ( SELECT vinculo_recurso.exercicio
                        , vinculo_recurso.cod_tipo
                        , vinculo_recurso.cod_recurso
                        , vinculo_recurso.cod_entidade
                     FROM stn.vinculo_recurso
                    WHERE exercicio = '''||stExercicio||'''
                      AND vinculo_recurso.cod_vinculo  = 1
                 GROUP BY vinculo_recurso.exercicio
                        , vinculo_recurso.cod_tipo
                        , vinculo_recurso.cod_recurso
                        , vinculo_recurso.cod_entidade
                 ) AS vinculo_recurso
              ON vinculo_recurso.exercicio    = emp.exercicio
             AND vinculo_recurso.cod_entidade = emp.cod_entidade
             AND vinculo_recurso.cod_recurso  = emp.cod_recurso
    
        LEFT JOIN 
    
        (SELECT
            SUM(liquidacao_paga.vl_total) AS vl_pago,
            pre_empenho.exercicio,
            pre_empenho.cod_pre_empenho,
            empenho.cod_entidade,
            empenho.cod_empenho 
        FROM
            empenho.nota_liquidacao
            INNER JOIN
            empenho.empenho ON
                empenho.exercicio = nota_liquidacao.exercicio_empenho AND
                empenho.cod_entidade = nota_liquidacao.cod_entidade AND
                empenho.cod_empenho = nota_liquidacao.cod_empenho
            INNER JOIN
            empenho.pre_empenho ON
                pre_empenho.exercicio = empenho.exercicio AND
                pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            INNER JOIN
            (SELECT
                nota_liquidacao_paga.exercicio,
                nota_liquidacao_paga.cod_entidade,
                nota_liquidacao_paga.cod_nota,
                (SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total 
            FROM
                (SELECT
                    nota_liquidacao_paga.exercicio,
                    nota_liquidacao_paga.cod_entidade,
                    nota_liquidacao_paga.cod_nota,
                    SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                FROM
                    empenho.nota_liquidacao_paga
                WHERE
                    TO_DATE(nota_liquidacao_paga.timestamp::VARCHAR, ''yyyy-mm-dd'') <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
                GROUP BY
                    nota_liquidacao_paga.exercicio,
                    nota_liquidacao_paga.cod_entidade,
                    nota_liquidacao_paga.cod_nota
                ) AS nota_liquidacao_paga
                LEFT JOIN
                (SELECT
                    nota_liquidacao_paga_anulada.exercicio,
                    nota_liquidacao_paga_anulada.cod_entidade,
                    nota_liquidacao_paga_anulada.cod_nota,
                    SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado 
                FROM
                    empenho.nota_liquidacao_paga_anulada
                WHERE
                    TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::VARCHAR, ''yyyy-mm-dd'') <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'')  
                GROUP BY
                    nota_liquidacao_paga_anulada.exercicio,
                    nota_liquidacao_paga_anulada.cod_entidade,
                    nota_liquidacao_paga_anulada.cod_nota
                ) AS nota_liquidacao_paga_anulada ON
                    nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio AND
                    nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade AND
                    nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
            GROUP BY
                nota_liquidacao_paga.exercicio,
                nota_liquidacao_paga.cod_entidade,
                nota_liquidacao_paga.cod_nota 
            ) AS liquidacao_paga ON
                liquidacao_paga.exercicio = nota_liquidacao.exercicio AND
                liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade AND
                liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
            WHERE
                empenho.exercicio < ''' || stExercicio || ''' AND
                empenho.dt_empenho < TO_DATE(''' || stDtIni || ''', ''dd/mm/yyyy'') AND 
                nota_liquidacao.dt_liquidacao <= TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') AND 
                empenho.cod_entidade IN (' || stEntidades || ') 
        GROUP BY
            pre_empenho.exercicio,
            pre_empenho.cod_pre_empenho,
            empenho.cod_entidade,
            empenho.cod_empenho 
        ) AS pag ON 
            pag.exercicio       = emp.exercicio AND
            pag.cod_entidade    = emp.cod_entidade AND 
            pag.cod_pre_empenho = emp.cod_pre_empenho AND
            pag.cod_empenho     = emp.cod_empenho
        
        -- Cancelamentos até o final do periodo selecionado (bimestre) do Exercicio corrente
        
        LEFT JOIN
        (SELECT
            eai.exercicio,
            eai.cod_entidade, 
            eai.cod_pre_empenho, 
            eai.cod_empenho,
            SUM(eai.vl_anulado) AS vl_cancelado
        FROM
            empenho.empenho_anulado_item eai
        WHERE
            TO_DATE(eai."timestamp"::VARCHAR, ''yyyy-mm-dd'') BETWEEN TO_DATE(''' || stDtIni || ''', ''dd/mm/yyyy'') AND TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'') 
        GROUP BY
            eai.exercicio,
            eai.cod_entidade,
            eai.cod_pre_empenho,
            eai.cod_empenho 
        ) AS canc ON 
            canc.exercicio       = emp.exercicio AND
            canc.cod_entidade    = emp.cod_entidade AND 
            canc.cod_pre_empenho = emp.cod_pre_empenho AND
            canc.cod_empenho     = emp.cod_empenho
    
    WHERE 
        emp.vl_empenhado <> 0 AND
        -- Compara com o Vínculo do Exercicio Atual
        emp.num_orgao IN (  SELECT
                                num_orgao 
                            FROM
                                stn.vinculo_recurso
                            WHERE
                                exercicio = ''' || stExercicio || ''' AND
                                cod_vinculo IN (1,2,3,4,5) 
                            GROUP BY num_orgao
                        ) AND 
        emp.cod_funcao = 12 
    )';
    
    EXECUTE stSQL;
    
    -- select de retorno
    
    stSQL := '
    SELECT
        CAST((SUM(vl_empenhado) - SUM(vl_pago)) AS NUMERIC(14,2)) AS vl_resto,
        CAST(SUM(vl_cancelado) AS NUMERIC(14,2)) AS vl_cancelado,
        nivel
    FROM
        tmp_rreo_resto
    GROUP BY nivel';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
    DROP TABLE tmp_rreo_resto;
    RETURN;
 
END;

$$ language 'plpgsql';

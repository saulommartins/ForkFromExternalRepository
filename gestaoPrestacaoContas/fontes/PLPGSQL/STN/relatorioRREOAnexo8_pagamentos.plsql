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
* para trazer os Pagamentos realizados com Recursos Vinculados ao FUNDEB
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

*  $Id: relatorioRREOAnexo8_pagamentos.plsql 61214 2014-12-16 19:49:31Z evandro $
*
* Casos de uso: 
*/
CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo8_pagamentos( stExercicio VARCHAR, stDtFim VARCHAR, stEntidades VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE

    --arDatas         VARCHAR[] ;
    stDtIni         VARCHAR := '';
    --stDtFim         VARCHAR := '';

    reRegistro      RECORD;
    stSql           VARCHAR := '';

BEGIN

    stDtIni := '01/01/' || stExercicio;
    --arDatas := publico.bimestre ( stExercicio, inBimestre );
    --stDtFim := arDatas [ 1 ];

    stSQL := '
    SELECT CAST(COALESCE(SUM(vl_pago), 0.00) AS NUMERIC(14,2)) AS vl_pago
    FROM (
      SELECT 
            (COALESCE(SUM(vl_pago), 0.00) - COALESCE(SUM(vl_anulado), 0.00)) AS vl_pago
            --COALESCE(SUM(vl_pago), 0.00) AS vl_pago 
        FROM orcamento.recurso r 
       INNER JOIN orcamento.despesa d
          ON d.exercicio = r.exercicio
         AND d.cod_recurso = r.cod_recurso 
       INNER JOIN empenho.pre_empenho_despesa ped
          ON ped.exercicio = d.exercicio
         AND ped.cod_despesa = d.cod_despesa
       INNER JOIN empenho.pre_empenho pe
          ON pe.exercicio = ped.exercicio
         AND pe.cod_pre_empenho = ped.cod_pre_empenho
       INNER JOIN empenho.empenho e
          ON e.exercicio = pe.exercicio
         AND e.cod_pre_empenho = pe.cod_pre_empenho
       INNER JOIN empenho.nota_liquidacao nl
          ON nl.exercicio_empenho = e.exercicio
         AND nl.cod_entidade = e.cod_entidade
         AND nl.cod_empenho = e.cod_empenho
       INNER JOIN empenho.nota_liquidacao_paga nlp
          ON nlp.exercicio = nl.exercicio
         AND nlp.cod_entidade = nl.cod_entidade
         AND nlp.cod_nota = nl.cod_nota
       INNER JOIN empenho.nota_liquidacao_conta_pagadora nlcp
          ON nlcp.cod_entidade = nlp.cod_entidade
         AND nlcp.cod_nota = nlp.cod_nota
         AND nlcp.exercicio_liquidacao = nlp.exercicio
         AND nlcp.timestamp = nlp.timestamp
       INNER JOIN contabilidade.plano_analitica cpa
          ON cpa.cod_plano = nlcp.cod_plano
         AND cpa.exercicio = nlcp.exercicio
       INNER JOIN stn.vinculo_fundeb svf
          ON svf.cod_plano = cpa.cod_plano
         AND svf.exercicio = cpa.exercicio
        LEFT JOIN empenho.nota_liquidacao_paga_anulada nlpa
          ON nlp.exercicio = nlpa.exercicio
         AND nlp.cod_nota = nlpa.cod_nota
         AND nlp.cod_entidade = nlpa.cod_entidade
         AND nlp.timestamp = nlpa.timestamp 
       WHERE nlp.cod_entidade IN (' || stEntidades || ')
         AND TO_DATE(nlp.timestamp::VARCHAR, ''yyyy-mm-dd'') BETWEEN TO_DATE(''' || stDtIni || ''', ''dd/mm/yyyy'')
                                                                 AND TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'')  
         AND r.exercicio =''' || stExercicio || '''
            /* Recursos do Fundeb devem ser definidos aqui.
               Em stn.vinculo_stn_recurso, cod_vinculo = 1 */
    --        d.cod_recurso IN (SELECT cod_recurso FROM stn.vinculo_recurso WHERE exercicio = ''' || stExercicio || ''' AND cod_vinculo = 1 GROUP BY cod_recurso) AND 
    

    UNION
          --transferencias conta credito
        SELECT COALESCE(SUM(tt.valor), 0.00) - COALESCE(SUM(tte.valor), 0.00) as vl_pago
          FROM tesouraria.transferencia tt
    
    INNER JOIN stn.vinculo_fundeb svf
            ON tt.cod_plano_credito = svf.cod_plano
           AND tt.exercicio = svf.exercicio
    LEFT JOIN tesouraria.transferencia_estornada tte
            ON tte.cod_entidade = tt.cod_entidade
           AND tte.exercicio = tt.exercicio
           AND tte.cod_lote = tt.cod_lote
           AND tte.tipo = tt.tipo
         WHERE tt.tipo =''T''
           AND tt.cod_entidade IN (' || stEntidades || ')
           AND tt.dt_autenticacao BETWEEN TO_DATE(''' || stDtIni || ''', ''dd/mm/yyyy'')
                                      AND TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'')
    UNION
        -- transferencia da conta debito
        SELECT (COALESCE(SUM(tt.valor), 0.00) * -1) as vl_pago
          FROM tesouraria.transferencia tt
    
    INNER JOIN stn.vinculo_fundeb svf
            ON tt.cod_plano_debito = svf.cod_plano
           AND tt.exercicio = svf.exercicio
         WHERE tt.tipo = ''T''
           AND tt.cod_entidade IN (' || stEntidades || ')
           AND tt.dt_autenticacao BETWEEN TO_DATE(''' || stDtIni || ''', ''dd/mm/yyyy'')
                                      AND TO_DATE(''' || stDtFim || ''', ''dd/mm/yyyy'')
    ) AS tabela 
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    RETURN;
 
END;

$$ language 'plpgsql';

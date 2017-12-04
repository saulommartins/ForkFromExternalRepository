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
 * PL do Relatório RGF Anexo 6 
 * Data de Criação   : 29/07/2008


 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Alexandre Melo
 
 * @package URBEM
 * @subpackage 

 $Id:$
*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo6novo_recurso(varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
  
  stExercicio         ALIAS FOR $1;
  stCodEntidade       ALIAS FOR $2;
  dtFinal             ALIAS FOR $3;

  dtInicial           VARCHAR := '';
  stExercicioAnterior VARCHAR := ''; 
  stSql               VARCHAR := '';  
  reRegistro          RECORD;
  inIdentificador     INTEGER; 

BEGIN
  dtInicial := '01/01/' || stExercicio;
  stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));

  --verifica se a sequence rgf_anexo_6 existe
  IF((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='rgf_anexo_6') IS NOT NULL)
  THEN
      SELECT NEXTVAL('stn.rgf_anexo_6')
        INTO inIdentificador;
  ELSE 
      CREATE SEQUENCE stn.rgf_anexo_6 START 1;   
      SELECT NEXTVAL('stn.rgf_anexo_6')
        INTO inIdentificador;
  END IF;
 

  -- cria a tabela temporaria para o valor processado no exercicios anteriores
  stSql := '
    CREATE TEMPORARY TABLE tmp_rec_proc_exer_ant_' || inIdentificador || ' AS

      SELECT busca_recurso.cod_recurso
           , liquidado.cod_empenho
           , liquidado.cod_entidade

           , busca_recurso.nom_recurso
           , sw_cgm.nom_cgm as nom_entidade

           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) AS vl_total
        FROM (  SELECT pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_empenho
                     , empenho.cod_entidade
                     , ( SUM(liquidado.vl_total) ) AS vl_liquidado
                  FROM empenho.nota_liquidacao
            
            INNER JOIN empenho.empenho
                    ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
                               , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                            FROM empenho.nota_liquidacao_item
                       LEFT JOIN (  SELECT exercicio
                                         , cod_nota
                                         , num_item
                                         , exercicio_item
                                         , cod_pre_empenho
                                         , cod_entidade
                                         , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                      FROM empenho.nota_liquidacao_item_anulado
                                     WHERE TO_DATE(timestamp::varchar,''yyyy-mm-dd'') <= TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                       AND cod_entidade IN ('||stCodEntidade||')
                                  GROUP BY exercicio
                                         , cod_nota
                                         , num_item
                                         , exercicio_item
                                         , cod_pre_empenho
                                         , cod_entidade
                                 ) AS nota_liquidacao_item_anulado
                              ON nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                             AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                             AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                             AND nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                             AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                             AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                           WHERE nota_liquidacao_item.cod_entidade  IN ('||stCodEntidade||')
                        GROUP BY nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
            
                       ) AS liquidado
                    ON liquidado.exercicio = nota_liquidacao.exercicio
                   AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                   AND liquidado.cod_nota = nota_liquidacao.cod_nota
            
                 WHERE empenho.exercicio < '|| quote_literal(stExercicioAnterior) ||'
                   AND empenho.dt_empenho < TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') 
                   AND nota_liquidacao.dt_liquidacao <= TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'') 
                   AND empenho.cod_entidade IN ('||stCodEntidade||')

              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
             ) AS liquidado
     LEFT JOIN (  SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_pago
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_empenho
                       , empenho.cod_entidade
              
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
              INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
              
                              FROM (  SELECT nota_liquidacao_paga.exercicio
                                            , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                           , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                                        FROM empenho.nota_liquidacao_paga
                                       WHERE TO_DATE(nota_liquidacao_paga.timestamp::varchar,''yyyy-mm-dd'') <= TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                         AND nota_liquidacao_paga.cod_entidade IN ('||stCodEntidade||')
                                    GROUP BY nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                   ) AS nota_liquidacao_paga
              
                         LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_paga_anulada
                                       WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::varchar,''yyyy-mm-dd'') < TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                         AND nota_liquidacao_paga_anulada.cod_entidade IN ('||stCodEntidade||')
                                    GROUP BY nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                   ) AS nota_liquidacao_paga_anulada
                                ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                               AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                               AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                          GROUP BY nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
              
                         ) AS liquidacao_paga
                      ON liquidacao_paga.exercicio = nota_liquidacao.exercicio
                     AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
              
                 WHERE empenho.exercicio < '|| quote_literal(stExercicioAnterior) ||'
                   AND empenho.dt_empenho < TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'')
                   AND nota_liquidacao.dt_liquidacao <= TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('||stCodEntidade||')
    
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_empenho
                     , empenho.cod_entidade

               ) AS pago
            ON pago.exercicio = liquidado.exercicio
           AND pago.cod_pre_empenho = liquidado.cod_pre_empenho
           AND pago.cod_entidade = liquidado.cod_entidade
           AND pago.cod_empenho = liquidado.cod_empenho

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = liquidado.exercicio
           AND entidade.cod_entidade = liquidado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = liquidado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = liquidado.cod_pre_empenho
      
     LEFT JOIN ( SELECT d.exercicio
                      , d.cod_despesa
                      , recurso.cod_recurso
                      , recurso.nom_recurso
                   FROM orcamento.despesa as d
                        LEFT JOIN ( SELECT r.exercicio 
                                         , r.cod_recurso
                                         , r.nom_recurso 
                                      FROM orcamento.recurso as r ) as recurso
                               ON (     recurso.exercicio   = d.exercicio
                                    AND recurso.cod_recurso = d.cod_recurso )
                  WHERE d.cod_entidade IN ('||stCodEntidade||')                   ) as busca_recurso
            ON busca_recurso.exercicio = pre_empenho_despesa.exercicio
           AND busca_recurso.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = liquidado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = liquidado.cod_pre_empenho

      GROUP BY busca_recurso.cod_recurso
             , liquidado.cod_empenho
             , liquidado.cod_entidade
             , busca_recurso.nom_recurso
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural



        HAVING ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) > 0 

  ';
  
 
  EXECUTE stSql;

  -- cria a tabela temporaria para o valor processado no exercicio anterior
  stSql := '
    CREATE TEMPORARY TABLE tmp_recursos_processados_exercicio_anterior_' || inIdentificador || ' AS

      SELECT busca_recurso.cod_recurso
           , liquidado.cod_empenho
           , liquidado.cod_entidade

           , busca_recurso.nom_recurso
           , sw_cgm.nom_cgm as nom_entidade

           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) AS vl_total
        FROM (  SELECT pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
                     , ( SUM(liquidado.vl_total) ) AS vl_liquidado
                  FROM empenho.nota_liquidacao
            
            INNER JOIN empenho.empenho
                    ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
                               , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                            FROM empenho.nota_liquidacao_item
                                 LEFT JOIN (  SELECT exercicio
                                                   , cod_nota
                                                   , num_item
                                                   , exercicio_item
                                                   , cod_pre_empenho
                                                   , cod_entidade
                                                   , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                                FROM empenho.nota_liquidacao_item_anulado
                                               WHERE TO_DATE(timestamp::varchar,''yyyy-mm-dd'') BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                                 AND nota_liquidacao_item_anulado.cod_entidade IN ('||stCodEntidade||')
                                            GROUP BY exercicio
                                                   , cod_nota
                                                   , num_item
                                                   , exercicio_item
                                                   , cod_pre_empenho
                                                   , cod_entidade
                                           ) AS nota_liquidacao_item_anulado
                                        ON nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                                       AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                                       AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                                       AND nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                                       AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                       AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                           WHERE nota_liquidacao_item.cod_entidade IN ('||stCodEntidade||')
                        GROUP BY nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
            
                       ) AS liquidado
                    ON liquidado.exercicio = nota_liquidacao.exercicio
                   AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                   AND liquidado.cod_nota = nota_liquidacao.cod_nota
            
                 WHERE empenho.exercicio = '|| quote_literal(stExercicioAnterior) ||'
                   AND empenho.dt_empenho BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicioAnterior) ||',''dd/mm/yyyy'')
                   AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('||stCodEntidade||')

              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
             ) AS liquidado
     LEFT JOIN (  SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_pago
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho 
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
              INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
              
                              FROM (  SELECT nota_liquidacao_paga.exercicio
                                            , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                           , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                                        FROM empenho.nota_liquidacao_paga
                                       WHERE TO_DATE(nota_liquidacao_paga.timestamp::varchar,''yyyy-mm-dd'') BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                         AND nota_liquidacao_paga.cod_entidade IN ('||stCodEntidade||')
                                    GROUP BY nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                   ) AS nota_liquidacao_paga
              
                         LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_paga_anulada
                                       WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::varchar,''yyyy-mm-dd'') BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                         AND nota_liquidacao_paga_anulada.cod_entidade IN ('||stCodEntidade||')
                                    GROUP BY nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                   ) AS nota_liquidacao_paga_anulada
                                ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                               AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                               AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                          GROUP BY nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
              
                         ) AS liquidacao_paga
                      ON liquidacao_paga.exercicio = nota_liquidacao.exercicio
                     AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
              
                 WHERE empenho.exercicio = '''||stExercicioAnterior||'''
                   AND empenho.dt_empenho BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicioAnterior) ||',''dd/mm/yyyy'')
                   AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('||stCodEntidade||')
    
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
               ) AS pago
            ON pago.exercicio = liquidado.exercicio
           AND pago.cod_pre_empenho = liquidado.cod_pre_empenho
           AND pago.cod_entidade = liquidado.cod_entidade
           AND pago.cod_empenho = liquidado.cod_empenho

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = liquidado.exercicio
           AND entidade.cod_entidade = liquidado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = liquidado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = liquidado.cod_pre_empenho

     LEFT JOIN ( SELECT d.exercicio
                      , d.cod_despesa
                      , recurso.cod_recurso
                      , recurso.nom_recurso
                   FROM orcamento.despesa as d
                        LEFT JOIN ( SELECT r.exercicio
                                         , r.cod_recurso
                                         , r.nom_recurso
                                      FROM orcamento.recurso as r ) as recurso
                               ON (     recurso.exercicio   = d.exercicio
                                    AND recurso.cod_recurso = d.cod_recurso ) 
                  WHERE d.cod_entidade IN ('||stCodEntidade||')                                ) as busca_recurso
            ON busca_recurso.exercicio = pre_empenho_despesa.exercicio
           AND busca_recurso.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = liquidado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = liquidado.cod_pre_empenho

      GROUP BY busca_recurso.cod_recurso
             , liquidado.cod_empenho
             , liquidado.cod_entidade
             , busca_recurso.nom_recurso
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural

        HAVING ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) > 0
  ';

  EXECUTE stSql;

  -- cria a tabela temporaria para o valor nao processado em exercicios anteriores
  StSql := '
    CREATE TEMPORARY TABLE tmp_rec_nao_proc_exer_ant_' || inIdentificador || ' AS

      SELECT busca_recurso.cod_recurso
           , empenhado.cod_empenho
           , empenhado.cod_entidade

           , busca_recurso.nom_recurso
           , sw_cgm.nom_cgm as nom_entidade

           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00))) AS vl_total
        FROM (  SELECT (  SUM(COALESCE(item_pre_empenho.vl_total,0.00))
                          -
                          SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) AS vl_empenhado
                     , pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
                  FROM empenho.empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
            INNER JOIN empenho.item_pre_empenho
                    ON item_pre_empenho.exercicio = pre_empenho.exercicio
                   AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                               , SUM(empenho_anulado_item.vl_anulado) AS vl_anulado
                            FROM empenho.empenho_anulado_item
                           WHERE TO_DATE(empenho_anulado_item.timestamp::varchar,''yyyy-mm-dd'') <= TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'') 
                             AND empenho_anulado_item.cod_entidade  IN ('||stCodEntidade||')
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND empenho_anulado_item.num_item = item_pre_empenho.num_item
            
                 WHERE empenho.exercicio < '|| quote_literal(stExercicioAnterior) ||'
                   AND empenho.dt_empenho < TO_DATE('|| quote_literal('31/12/'||stExercicioAnterior) ||',''dd/mm/yyyy'') 
                   AND empenho.cod_entidade IN ('||stCodEntidade||')
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
             ) AS empenhado 

     LEFT JOIN (  SELECT ( SUM(COALESCE(liquidado.vl_total,0.00)) ) AS vl_liquidado
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
              
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
               LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                              FROM empenho.nota_liquidacao_item
                         LEFT JOIN (  SELECT exercicio
                                           , cod_nota
                                           , num_item
                                           , exercicio_item
                                           , cod_pre_empenho
                                           , cod_entidade
                                           , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_item_anulado
                                       WHERE TO_DATE(timestamp::varchar,''yyyy-mm-dd'') <= TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                         AND nota_liquidacao_item_anulado.cod_entidade IN ('||stCodEntidade||')
                                    GROUP BY exercicio
                                           , cod_nota
                                           , num_item
                                           , exercicio_item
                                           , cod_pre_empenho
                                           , cod_entidade
                                   ) AS nota_liquidacao_item_anulado
                                ON nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                               AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                               AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                               AND nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                               AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                               AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                             WHERE nota_liquidacao_item.cod_entidade  IN ('||stCodEntidade||') 
                          GROUP BY nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
              
                       ) AS liquidado
                      ON liquidado.exercicio = nota_liquidacao.exercicio
                     AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidado.cod_nota = nota_liquidacao.cod_nota

                   WHERE empenho.exercicio < '|| quote_literal(stExercicioAnterior) ||'
                     AND empenho.dt_empenho < TO_DATE('|| quote_literal('31/12/'||stExercicioAnterior) ||',''dd/mm/yyyy'')

                     AND nota_liquidacao.dt_liquidacao <= TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                     AND empenho.cod_entidade IN ('||stCodEntidade||')
                GROUP BY pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho

               ) AS liquidado 
            ON liquidado.exercicio = empenhado.exercicio
           AND liquidado.cod_pre_empenho = empenhado.cod_pre_empenho
           AND liquidado.cod_entidade = empenhado.cod_entidade
           AND liquidado.cod_empenho = empenhado.cod_empenho

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = empenhado.exercicio
           AND entidade.cod_entidade = empenhado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = empenhado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = empenhado.cod_pre_empenho

   LEFT JOIN ( SELECT d.exercicio
                    , d.cod_despesa
                    , recurso.cod_recurso
                    , recurso.nom_recurso
                 FROM orcamento.despesa as d
                      LEFT JOIN ( SELECT r.exercicio
                                       , r.cod_recurso
                                       , r.nom_recurso
                                    FROM orcamento.recurso as r ) as recurso
                             ON (     recurso.exercicio   = d.exercicio
                                  AND recurso.cod_recurso = d.cod_recurso )
                WHERE d.cod_entidade IN ('||stCodEntidade||')                            ) as busca_recurso
          ON busca_recurso.exercicio = pre_empenho_despesa.exercicio
         AND busca_recurso.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

      GROUP BY busca_recurso.cod_recurso
             , busca_recurso.nom_recurso
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , empenhado.cod_empenho
             , empenhado.cod_entidade

        HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
  ';
  
  EXECUTE stSql;
  
  -- cria a tabela temporaria para o valor nao processado no exercicio anterior
  StSql := '
    CREATE TEMPORARY TABLE tmp_recursos_nao_processados_exercicio_anterior_' || inIdentificador || ' AS

      SELECT busca_recurso.cod_recurso
           , empenhado.cod_empenho
           , empenhado.cod_entidade

           , busca_recurso.nom_recurso
           , sw_cgm.nom_cgm as nom_entidade

           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural

           , (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00))) AS vl_total
        FROM (  SELECT (  SUM(COALESCE(item_pre_empenho.vl_total,0.00))
                          -
                          SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) AS vl_empenhado
                     , pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
            
                  FROM empenho.empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
            INNER JOIN empenho.item_pre_empenho
                    ON item_pre_empenho.exercicio = pre_empenho.exercicio
                   AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
            
             LEFT JOIN (  SELECT empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                               , SUM(empenho_anulado_item.vl_anulado) AS vl_anulado
                            FROM empenho.empenho_anulado_item
                           WHERE TO_DATE(empenho_anulado_item.timestamp::varchar,''yyyy-mm-dd'') BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'|| stExercicio) ||',''dd/mm/yyyy'')
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND empenho_anulado_item.num_item = item_pre_empenho.num_item
            
                 WHERE empenho.exercicio = '|| quote_literal(stExercicioAnterior) ||'
                   AND empenho.dt_empenho BETWEEN TO_DATE('|| quote_literal('01/01/'|| stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'|| stExercicio) ||',''dd/mm/yyyy'')
                   AND empenho.cod_entidade IN ('|| stCodEntidade ||')
              GROUP BY pre_empenho.exercicio
                     , pre_empenho.cod_pre_empenho
                     , empenho.cod_entidade
                     , empenho.cod_empenho
             ) AS empenhado 

     LEFT JOIN (  SELECT ( SUM(liquidado.vl_total) ) AS vl_liquidado
                       , pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho
              
                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
              
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
               LEFT JOIN (  SELECT nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
                                 , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_total
                              FROM empenho.nota_liquidacao_item
                                   LEFT JOIN (  SELECT exercicio
                                                     , cod_nota
                                                     , num_item
                                                     , exercicio_item
                                                     , cod_pre_empenho
                                                     , cod_entidade
                                                     , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                                  FROM empenho.nota_liquidacao_item_anulado
                                                 WHERE TO_DATE(timestamp::varchar,''yyyy-mm-dd'') BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                                              GROUP BY exercicio
                                                     , cod_nota
                                                     , num_item
                                                     , exercicio_item
                                                     , cod_pre_empenho
                                                     , cod_entidade
                                             ) AS nota_liquidacao_item_anulado
                                          ON nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                                         AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                                         AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                                         AND nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                                         AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                         AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                             WHERE nota_liquidacao_item.cod_entidade IN ('|| stCodEntidade ||')
                          GROUP BY nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
              
                       ) AS liquidado
                      ON liquidado.exercicio = nota_liquidacao.exercicio
                     AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidado.cod_nota = nota_liquidacao.cod_nota
                   WHERE empenho.exercicio = '|| quote_literal(stExercicioAnterior) ||'
                     AND empenho.dt_empenho BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                     AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('|| quote_literal('01/01/'||stExercicioAnterior) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal('31/12/'||stExercicio) ||',''dd/mm/yyyy'')
                     AND empenho.cod_entidade IN ('||stCodEntidade||')
                GROUP BY pre_empenho.exercicio
                       , pre_empenho.cod_pre_empenho
                       , empenho.cod_entidade
                       , empenho.cod_empenho

               ) AS liquidado 
            ON liquidado.exercicio = empenhado.exercicio
           AND liquidado.cod_pre_empenho = empenhado.cod_pre_empenho
           AND liquidado.cod_entidade = empenhado.cod_entidade

-- inner para achar a entidade a que ele pertence
    INNER JOIN orcamento.entidade
            ON entidade.exercicio = empenhado.exercicio
           AND entidade.cod_entidade = empenhado.cod_entidade
    
    INNER JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
     LEFT JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = empenhado.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = empenhado.cod_pre_empenho

     LEFT JOIN ( SELECT d.exercicio
                      , d.cod_despesa
                      , recurso.cod_recurso
                      , recurso.nom_recurso
                   FROM orcamento.despesa as d
                        LEFT JOIN ( SELECT r.exercicio
                                         , r.cod_recurso
                                         , r.nom_recurso
                                      FROM orcamento.recurso as r ) as recurso
                               ON (     recurso.exercicio   = d.exercicio
                                    AND recurso.cod_recurso = d.cod_recurso )
                  WHERE d.cod_entidade IN ('||stCodEntidade||')                             ) as busca_recurso
            ON busca_recurso.exercicio = pre_empenho_despesa.exercicio
           AND busca_recurso.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

      GROUP BY busca_recurso.cod_recurso
             , busca_recurso.nom_recurso
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , empenhado.cod_empenho
             , empenhado.cod_entidade



        HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
  ';
  
  EXECUTE stSql;

  stSql := 'UPDATE tmp_rec_proc_exer_ant_' || inIdentificador || ' SET cod_recurso = 0, nom_recurso =''Não Informado'' WHERE cod_recurso is null';
  stSql := 'UPDATE tmp_recursos_processados_exercicio_anterior_' || inIdentificador || ' SET cod_recurso = 0, nom_recurso = ''Não Informado'' WHERE cod_recurso is null';
  stSql := 'UPDATE tmp_rec_nao_proc_exer_ant_' || inIdentificador || '  SET cod_recurso = 0, nom_recurso = ''Não Informado'' WHERE cod_recurso is null';
  stSql := 'UPDATE tmp_recursos_nao_processados_exercicio_anterior_' || inIdentificador || '    SET cod_recurso = 0, nom_recurso = ''Não Informado'' WHERE cod_recurso is null';

--consulta para retornar todas os orgaos para nao intra-orcamentarias
  stSql := '
    CREATE TEMPORARY TABLE tmp_recursos_orgao_' || inIdentificador || ' AS 
      SELECT cod_recurso
           , nom_recurso
        FROM tmp_rec_proc_exer_ant_' || inIdentificador || '
       UNION
      SELECT cod_recurso
           , nom_recurso
        FROM tmp_recursos_processados_exercicio_anterior_' || inIdentificador || '
       UNION 
      SELECT cod_recurso
           , nom_recurso
        FROM tmp_rec_nao_proc_exer_ant_' || inIdentificador || '
       UNION 
      SELECT cod_recurso
           , nom_recurso
        FROM tmp_recursos_nao_processados_exercicio_anterior_' || inIdentificador || '
    GROUP BY cod_recurso, nom_recurso
  ';

  EXECUTE stSql;
 
  stSql := 'CREATE TEMPORARY TABLE tmp_rec_liq_e_nao_liq_do_exer_' || inIdentificador || ' AS
            SELECT *
	      FROM stn.fn_rel_rgf6novo_emp_liq_exercicio_recurso ('|| quote_literal(stCodEntidade) ||', '|| quote_literal(stExercicio) ||', '|| quote_literal(dtFinal) ||') as retorno
              (cod_entidade integer, cod_recurso integer, nom_recurso varchar, liquidados_nao_pagos numeric, empenhados_nao_liquidados numeric)
            ';
  EXECUTE stSql;

  stSql := '
    CREATE TEMPORARY TABLE tmp_recursos_resultados_' || inIdentificador || ' AS

    SELECT
           tlnl.cod_recurso
         , tlnl.nom_recurso as tipo
         , tlnl.cod_entidade as entidade
         , 0.00 AS tmp_rec_proc_exer_ant
         , 0.00 AS tmp_recursos_processados_exercicio_anterior
         , 0.00 AS tmp_rec_nao_proc_exer_ant
         , 0.00 AS tmp_recursos_nao_processados_exercicio_anterior
         , sum(coalesce(tlnl.liquidados_nao_pagos,0.00))        as liquidados_nao_pagos
         , sum(coalesce(tlnl.empenhados_nao_liquidados,0.00))   as empenhados_nao_liquidados
         , 0.00 as empenhados_nao_liquidados_cancelados
         , 0.00 as caixa_liquida
      FROM tmp_rec_liq_e_nao_liq_do_exer_' || inIdentificador || '    as tlnl
  GROUP BY tlnl.cod_recurso
         , tlnl.nom_recurso
         , tlnl.cod_entidade

    UNION ALL

    SELECT tmp_recursos_orgao_' || inIdentificador || '.cod_recurso AS cod_recurso
         , tmp_recursos_orgao_' || inIdentificador || '.nom_recurso AS tipo
         , 0 as entidade
         , sum(coalesce(trpeas.vl_total,0.00))  AS tmp_rec_proc_exer_ant
         , sum(coalesce(trpea.vl_total,0.00))   AS tmp_recursos_processados_exercicio_anterior
         , sum(coalesce(trnpeas.vl_total,0.00)) AS  tmp_rec_nao_proc_exer_ant
         , sum(coalesce(trnpea.vl_total,0.00))  AS  tmp_recursos_nao_processados_exercicio_anterior
         , 0.00 as liquidados_nao_pagos
         , 0.00 as empenhados_nao_liquidados
         , 0.00 as empenhados_nao_liquidados_cancelados
         , 0.00 as caixa_liquida
      FROM tmp_recursos_orgao_' || inIdentificador || '

           LEFT JOIN( SELECT SUM(coalesce(vl_total,0.00)) as vl_total
                           , cod_recurso
                        FROM tmp_rec_proc_exer_ant_' || inIdentificador || '
                    GROUP BY cod_recurso ) as trpeas
                  ON(  trpeas.cod_recurso = tmp_recursos_orgao_' || inIdentificador || '.cod_recurso ) 
           LEFT JOIN( SELECT SUM(coalesce(vl_total,0.00)) as vl_total
                           , cod_recurso
                        FROM tmp_recursos_processados_exercicio_anterior_' || inIdentificador || '
                    GROUP BY cod_recurso )  as trpea
                  ON(  trpea.cod_recurso = tmp_recursos_orgao_' || inIdentificador || '.cod_recurso ) 
           LEFT JOIN( SELECT SUM(coalesce(vl_total,0.00)) as vl_total
                           , cod_recurso
                        FROM tmp_rec_nao_proc_exer_ant_' || inIdentificador || '
                    GROUP BY cod_recurso )  as trnpeas
                  ON(  trnpeas.cod_recurso = tmp_recursos_orgao_' || inIdentificador || '.cod_recurso )
           LEFT JOIN( SELECT SUM(coalesce(vl_total,0.00)) as vl_total
                           , cod_recurso
                        FROM tmp_recursos_nao_processados_exercicio_anterior_' || inIdentificador || '
                    GROUP BY cod_recurso )  as trnpea
                  ON(  trnpea.cod_recurso = tmp_recursos_orgao_' || inIdentificador || '.cod_recurso )
  GROUP BY tmp_recursos_orgao_' || inIdentificador || '.cod_recurso, tmp_recursos_orgao_' || inIdentificador || '.nom_recurso
  
    UNION ALL
              
    SELECT caixa_liquida.cod_recurso AS cod_recurso
         , caixa_liquida.nom_recurso AS tipo
         , caixa_liquida.cod_entidade as entidade
         , 0.00 AS tmp_rec_proc_exer_ant
         , 0.00 AS tmp_recursos_processados_exercicio_anterior
         , 0.00 AS tmp_rec_nao_proc_exer_ant
         , 0.00 AS tmp_recursos_nao_processados_exercicio_anterior
         , 0.00 AS liquidados_nao_pagos
         , 0.00 AS empenhados_nao_liquidados
         , 0.00 AS empenhados_nao_liquidados_cancelados
         , SUM(COALESCE(caixa_liquida.vl_lancamento,0.00)) as caixa_liquida
      FROM
          (
            SELECT SUM(COALESCE(valor_lancamento.vl_lancamento,0.00)) AS vl_lancamento
                 , recurso.cod_recurso
                 , recurso.nom_recurso
                 , lote.cod_entidade
              FROM orcamento.recurso
              JOIN contabilidade.plano_recurso
                ON plano_recurso.exercicio = recurso.exercicio
               AND plano_recurso.cod_recurso = recurso.cod_recurso
              JOIN contabilidade.plano_analitica
                ON plano_analitica.exercicio = plano_recurso.exercicio
               AND plano_analitica.cod_plano = plano_recurso.cod_plano
              JOIN orcamento.recurso_direto
                ON recurso_direto.exercicio = plano_recurso.exercicio
               AND recurso_direto.cod_recurso = plano_recurso.cod_recurso
              JOIN contabilidade.conta_credito
                ON conta_credito.exercicio = plano_analitica.exercicio
               AND conta_credito.cod_plano = plano_analitica.cod_plano
              JOIN contabilidade.valor_lancamento
                ON conta_credito.exercicio = valor_lancamento.exercicio
               AND conta_credito.cod_entidade = valor_lancamento.cod_entidade 
               AND conta_credito.tipo = valor_lancamento.tipo         
               AND conta_credito.cod_lote = valor_lancamento.cod_lote     
               AND conta_credito.sequencia = valor_lancamento.sequencia    
               AND conta_credito.tipo_valor = valor_lancamento.tipo_valor
              JOIN contabilidade.lote
                ON lote.exercicio = valor_lancamento.exercicio
               AND lote.cod_entidade = valor_lancamento.cod_entidade
               AND lote.tipo = valor_lancamento.tipo
               AND lote.cod_lote = valor_lancamento.cod_lote
              JOIN contabilidade.lancamento
                ON lancamento.cod_lote = valor_lancamento.cod_lote
               AND lancamento.tipo = valor_lancamento.tipo
               AND lancamento.exercicio = valor_lancamento.exercicio
               AND lancamento.sequencia = valor_lancamento.sequencia
               AND lancamento.cod_entidade = valor_lancamento.cod_entidade
               AND lancamento.cod_historico NOT BETWEEN 800 AND 899
              JOIN contabilidade.plano_banco
                ON plano_banco.exercicio = plano_analitica.exercicio
               AND plano_banco.cod_plano = plano_analitica.cod_plano
              JOIN contabilidade.plano_conta
                ON plano_conta.exercicio = plano_analitica.exercicio
               AND plano_conta.cod_conta = plano_analitica.cod_conta
             WHERE plano_conta.exercicio = '|| quote_literal(stExercicio) ||'
               AND lote.dt_lote BETWEEN TO_DATE( '|| quote_literal(dtInicial) ||' , ''dd/mm/yyyy'' )
               AND TO_DATE( '|| quote_literal(dtFinal) ||' , ''dd/mm/yyyy'' )
               AND lote.cod_entidade IN (' || stCodEntidade || ')
          GROUP BY recurso.cod_recurso
                 , recurso.nom_recurso
                 , lote.cod_entidade
         UNION ALL
            SELECT SUM(COALESCE(valor_lancamento.vl_lancamento,0.00)) AS vl_lancamento
                 , recurso.cod_recurso
                 , recurso.nom_recurso
                 , lote.cod_entidade
              FROM orcamento.recurso
              JOIN contabilidade.plano_recurso
                ON plano_recurso.exercicio = recurso.exercicio
               AND plano_recurso.cod_recurso = recurso.cod_recurso
              JOIN contabilidade.plano_analitica
                ON plano_analitica.exercicio = plano_recurso.exercicio
               AND plano_analitica.cod_plano = plano_recurso.cod_plano
              JOIN orcamento.recurso_direto
                ON recurso_direto.exercicio = plano_recurso.exercicio
               AND recurso_direto.cod_recurso = plano_recurso.cod_recurso
              JOIN contabilidade.conta_debito
                ON conta_debito.exercicio = plano_analitica.exercicio
               AND conta_debito.cod_plano = plano_analitica.cod_plano
              JOIN contabilidade.valor_lancamento
                ON conta_debito.exercicio = valor_lancamento.exercicio
               AND conta_debito.cod_entidade = valor_lancamento.cod_entidade 
               AND conta_debito.tipo = valor_lancamento.tipo         
               AND conta_debito.cod_lote = valor_lancamento.cod_lote     
               AND conta_debito.sequencia = valor_lancamento.sequencia    
               AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
              JOIN contabilidade.lote
                ON lote.exercicio = valor_lancamento.exercicio
               AND lote.cod_entidade = valor_lancamento.cod_entidade
               AND lote.tipo = valor_lancamento.tipo
               AND lote.cod_lote = valor_lancamento.cod_lote
              JOIN contabilidade.lancamento
                ON lancamento.cod_lote = valor_lancamento.cod_lote
               AND lancamento.tipo = valor_lancamento.tipo
               AND lancamento.exercicio = valor_lancamento.exercicio
               AND lancamento.sequencia = valor_lancamento.sequencia
               AND lancamento.cod_entidade = valor_lancamento.cod_entidade
               AND lancamento.cod_historico NOT BETWEEN 800 AND 899
              JOIN contabilidade.plano_banco
                ON plano_banco.exercicio = plano_analitica.exercicio
               AND plano_banco.cod_plano = plano_analitica.cod_plano
              JOIN contabilidade.plano_conta
                ON plano_conta.exercicio = plano_analitica.exercicio
               AND plano_conta.cod_conta = plano_analitica.cod_conta
             WHERE plano_conta.exercicio = '|| quote_literal(stExercicio) ||'
               AND lote.dt_lote BETWEEN TO_DATE( '|| quote_literal(dtInicial) ||' , ''dd/mm/yyyy'' )
               AND TO_DATE( '|| quote_literal(dtFinal) ||' , ''dd/mm/yyyy'' )
               AND lote.cod_entidade IN (' || stCodEntidade || ')
          GROUP BY recurso.cod_recurso
                 , recurso.nom_recurso
                 , lote.cod_entidade
          ORDER BY cod_recurso
        ) as caixa_liquida
  GROUP BY cod_recurso
         , nom_recurso
         , entidade
  ORDER BY cod_recurso
  ';
  EXECUTE stSql;

  stSql := ' 
             SELECT * FROM tmp_recursos_resultados_' || inIdentificador || '
           ';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  EXECUTE 'DROP TABLE tmp_rec_proc_exer_ant_' || inIdentificador;
  EXECUTE 'DROP TABLE tmp_recursos_processados_exercicio_anterior_' || inIdentificador;
  EXECUTE 'DROP TABLE tmp_rec_nao_proc_exer_ant_' || inIdentificador;
  EXECUTE 'DROP TABLE tmp_recursos_nao_processados_exercicio_anterior_' || inIdentificador;
  EXECUTE 'DROP TABLE tmp_recursos_orgao_' || inIdentificador;
  EXECUTE 'DROP TABLE tmp_recursos_resultados_' || inIdentificador;
  EXECUTE 'DROP TABLE tmp_rec_liq_e_nao_liq_do_exer_' || inIdentificador;

END;

$$ language 'plpgsql';

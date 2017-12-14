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

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo6_orgao(varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
  
  stExercicio         ALIAS FOR $1;
  stCodEntidade       ALIAS FOR $2;
  dtFinal             ALIAS FOR $3;

  dtInicial           VARCHAR := '';
  stExercicioAnterior VARCHAR := ''; 
  stSql               VARCHAR := '';  
  reRegistro          RECORD;

BEGIN
   
  dtInicial := '01/01/' || stExercicio;
  stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));
  
  -- cria a tabela temporaria para o valor processado no exercicios anteriores
  stSql := '
    CREATE TEMPORARY TABLE tmp_processados_exercicios_anteriores AS

      SELECT liquidado.cod_empenho
           , liquidado.cod_entidade
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao 
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao      

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
                                     WHERE TO_DATE(timestamp::VARCHAR,''dd/mm/yyyy'') <= TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'') 
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
                        GROUP BY nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
            
                       ) AS liquidado
                    ON liquidado.exercicio = nota_liquidacao.exercicio
                   AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                   AND liquidado.cod_nota = nota_liquidacao.cod_nota
            
                 WHERE empenho.exercicio < '''||stExercicioAnterior||'''
                   AND empenho.dt_empenho < TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') 
                   AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'') 
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
                                       WHERE TO_DATE(nota_liquidacao_paga.timestamp::VARCHAR,''dd/mm/yyyy'') <= TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
                                    GROUP BY nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                   ) AS nota_liquidacao_paga
              
                         LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_paga_anulada
                                       WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::VARCHAR,''dd/mm/yyyy'') < TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
              
                 WHERE empenho.exercicio < '''||stExercicioAnterior||'''
                   AND empenho.dt_empenho < TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                   AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao
            ON orgao.exercicio = ''' || stExercicio || '''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = liquidado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = liquidado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY liquidado.cod_empenho
             , liquidado.cod_entidade
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , despesa.dt_criacao
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao

        HAVING ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) > 0 

  ';
  
 
  EXECUTE stSql;

  -- cria a tabela temporaria para o valor processado no exercicio anterior
  stSql := '
    CREATE TEMPORARY TABLE tmp_processados_exercicio_anterior AS

      SELECT liquidado.cod_empenho
           , liquidado.cod_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao      
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
                                     WHERE TO_DATE(timestamp::VARCHAR,''dd/mm/yyyy'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
                        GROUP BY nota_liquidacao_item.exercicio
                               , nota_liquidacao_item.cod_entidade
                               , nota_liquidacao_item.cod_nota
            
                       ) AS liquidado
                    ON liquidado.exercicio = nota_liquidacao.exercicio
                   AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                   AND liquidado.cod_nota = nota_liquidacao.cod_nota
            
                 WHERE empenho.exercicio = '''||stExercicioAnterior||'''
                   AND empenho.dt_empenho BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                   AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
                                       WHERE TO_DATE(nota_liquidacao_paga.timestamp::VARCHAR,''dd/mm/yyyy'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
                                    GROUP BY nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                   ) AS nota_liquidacao_paga
              
                         LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_paga_anulada
                                       WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::VARCHAR,''dd/mm/yyyy'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
                   AND empenho.dt_empenho BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                   AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao 
            ON orgao.exercicio = '''||stExercicio||'''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = liquidado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = liquidado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY liquidado.cod_empenho
             , liquidado.cod_entidade
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao
             , despesa.dt_criacao
        HAVING ( SUM(COALESCE(liquidado.vl_liquidado,0.00)) - SUM(COALESCE(pago.vl_pago,0.00)) ) > 0
  ';

  EXECUTE stSql;

  -- cria a tabela temporaria para o valor nao processado em exercicios anteriores
  StSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_exercicios_anteriores AS

      SELECT empenhado.cod_empenho
           , empenhado.cod_entidade
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao      
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
                           WHERE TO_DATE(empenho_anulado_item.timestamp::VARCHAR,''dd/mm/yyyy'') <= TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'') 
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND empenho_anulado_item.num_item = item_pre_empenho.num_item
            
                 WHERE empenho.exercicio < '''||stExercicioAnterior||'''
                   AND empenho.dt_empenho < TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'') 
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
                                       WHERE TO_DATE(timestamp::VARCHAR,''dd/mm/yyyy'') <= TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
                          GROUP BY nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
              
                       ) AS liquidado
                      ON liquidado.exercicio = nota_liquidacao.exercicio
                     AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidado.cod_nota = nota_liquidacao.cod_nota

                   WHERE empenho.exercicio < '''||stExercicioAnterior||'''
                     AND empenho.dt_empenho < TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')

                     AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao 
            ON orgao.exercicio = '''||stExercicio||'''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY empenhado.cod_empenho
             , empenhado.cod_entidade
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao
             , despesa.dt_criacao
        HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
  ';
  
  EXECUTE stSql;
  
  -- cria a tabela temporaria para o valor nao processado no exercicio anterior
  StSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_exercicio_anterior AS

      SELECT empenhado.cod_empenho
           , empenhado.cod_entidade
           , sw_cgm.nom_cgm AS nom_entidade
           , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                  THEN (  CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                               THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                               ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                       )
                  ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
             END AS cod_estrutural
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN ''ÓRGÃO NÃO INFORMADO''
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.nom_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.nom_orgao
             END AS nom_orgao
           , CASE WHEN (orgao_implantado.num_orgao IS NULL) AND (orgao.num_orgao IS NULL)
                  THEN 99
                  WHEN (orgao.num_orgao IS NULL)
                  THEN orgao_implantado.num_orgao
                  WHEN (orgao_implantado.num_orgao IS NULL)
                  THEN orgao.num_orgao
             END AS num_orgao      

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
                           WHERE TO_DATE(empenho_anulado_item.timestamp::VARCHAR,''dd/mm/yyyy'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'|| stExercicio||''',''dd/mm/yyyy'')
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND empenho_anulado_item.num_item = item_pre_empenho.num_item
            
                 WHERE empenho.exercicio = '''|| stExercicioAnterior ||'''
                   AND empenho.dt_empenho BETWEEN TO_DATE(''01/01/'|| stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'|| stExercicio||''',''dd/mm/yyyy'')
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
                                       WHERE TO_DATE(timestamp::VARCHAR,''dd/mm/yyyy'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
                          GROUP BY nota_liquidacao_item.exercicio
                                 , nota_liquidacao_item.cod_entidade
                                 , nota_liquidacao_item.cod_nota
              
                       ) AS liquidado
                      ON liquidado.exercicio = nota_liquidacao.exercicio
                     AND liquidado.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidado.cod_nota = nota_liquidacao.cod_nota
                   WHERE empenho.exercicio = '''||stExercicioAnterior||'''
                     AND empenho.dt_empenho BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
                     AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicio||''',''dd/mm/yyyy'')
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
      
     LEFT JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           
     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN orcamento.orgao
            ON orgao.exercicio = '''||stExercicio||'''
           AND orgao.num_orgao = despesa.num_orgao

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

     LEFT JOIN orcamento.orgao AS orgao_implantado
            ON orgao_implantado.exercicio = '''||stExercicio||'''
           AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

      GROUP BY empenhado.cod_empenho
             , empenhado.cod_entidade
             , sw_cgm.nom_cgm
             , restos_pre_empenho.cod_estrutural
             , conta_despesa.cod_estrutural
             , orgao.num_orgao
             , orgao.nom_orgao
             , orgao_implantado.nom_orgao
             , orgao_implantado.num_orgao
             , despesa.dt_criacao
        HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
  ';
  
  EXECUTE stSql;

--consulta para retornar todas os orgaos para nao intra-orcamentarias
  stSql := '
    CREATE TEMPORARY TABLE tmp_orgao AS 
      SELECT '' ÓRGÃO NÃO INFORMADO'' as nom_orgao
           , 0    as num_orgao
           , null as  nom_entidade    
           , null as cod_estrutural
       UNION
      SELECT nom_orgao
           , num_orgao
           , nom_entidade
           , cod_estrutural
        FROM tmp_processados_exercicios_anteriores
       UNION
      SELECT nom_orgao
           , num_orgao
           , nom_entidade
           , cod_estrutural
        FROM tmp_processados_exercicio_anterior
       UNION 
      SELECT nom_orgao
           , num_orgao
           , nom_entidade
           , cod_estrutural
        FROM tmp_processados_exercicios_anteriores
       UNION 
      SELECT nom_orgao
           , num_orgao
           , nom_entidade
           , cod_estrutural
        FROM tmp_processados_exercicio_anterior
    ORDER BY num_orgao
  ';

  EXECUTE stSql;

    stSql := '

        CREATE TEMPORARY TABLE tmp_liquidados_e_nao_liquidados_do_exercicio AS
        SELECT *
          FROM stn.fn_rel_rgf6_emp_liq_exercicio_orgao( '''||stCodEntidade||''', '''||stExercicio||''', '''||dtInicial||''', '''||dtFinal||''', '''||dtFinal||''' ) as retorno 
             ( num_orgao integer, nom_orgao varchar, liquidados_nao_pagos numeric, empenhados_nao_liquidados numeric)  

    ';

    EXECUTE stSql;
 
    UPDATE tmp_liquidados_e_nao_liquidados_do_exercicio SET num_orgao = 0, nom_orgao= 'ÓRGÃO NÃO INFORMADO' WHERE num_orgao is null;
 
  stSql := '
    CREATE TEMPORARY TABLE tmp_resultados AS

    SELECT
           tlnl.num_orgao
         , tlnl.nom_orgao
         , 0.00 AS tmp_recursos_processados_exercicios_anteriores
         , 0.00 AS tmp_recursos_processados_exercicio_anterior
         , 0.00 AS tmp_recursos_nao_processados_exercicios_anteriores
         , 0.00 AS tmp_recursos_nao_processados_exercicio_anterior
         , sum(coalesce(tlnl.liquidados_nao_pagos,0.00))        as liquidados_nao_pagos
         , sum(coalesce(tlnl.empenhados_nao_liquidados,0.00))   as empenhados_nao_liquidados
      FROM tmp_liquidados_e_nao_liquidados_do_exercicio    as tlnl
  GROUP BY tlnl.num_orgao
         , tlnl.nom_orgao 

    UNION ALL

    SELECT tmp_orgao.num_orgao as num_orgao
         , tmp_orgao.nom_orgao AS nom_orgao
         , ( SELECT SUM(coalesce(vl_total,0.00))
               FROM tmp_processados_exercicios_anteriores
              WHERE num_orgao = tmp_orgao.num_orgao
                AND nom_orgao = tmp_orgao.nom_orgao
           ) AS valor_processado_exercicios_anteriores
         , ( SELECT SUM(coalesce(vl_total,0.00))
               FROM tmp_processados_exercicio_anterior
              WHERE num_orgao = tmp_orgao.num_orgao
                AND nom_orgao = tmp_orgao.nom_orgao
           ) AS valor_processado_exercicio_anterior
         , ( SELECT SUM(coalesce(vl_total,0.00))
               FROM tmp_nao_processados_exercicios_anteriores
              WHERE num_orgao = tmp_orgao.num_orgao
                AND nom_orgao = tmp_orgao.nom_orgao
           ) AS valor_nao_processado_exercicios_anteriores
         , ( SELECT SUM(coalesce(vl_total,0.00))
               FROM tmp_nao_processados_exercicio_anterior
              WHERE num_orgao = tmp_orgao.num_orgao
                AND nom_orgao = tmp_orgao.nom_orgao
           ) AS valor_nao_processado_exercicio_anterior
         , 0.00 as liquidados_nao_pagos
         , 0.00 as empenhados_nao_liquidados
      FROM tmp_orgao
  GROUP BY num_orgao, nom_orgao
  
    ORDER BY num_orgao

  ';


  EXECUTE stSql;

  stSql := ' 
             SELECT * FROM tmp_resultados
           ';


  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_processados_exercicios_anteriores;
  DROP TABLE tmp_processados_exercicio_anterior;
  DROP TABLE tmp_nao_processados_exercicios_anteriores;
  DROP TABLE tmp_nao_processados_exercicio_anterior;
  DROP TABLE tmp_orgao;
  DROP TABLE tmp_resultados;

  RETURN;
END;

$$ language 'plpgsql';

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
 * Script de função PLPGSQL - Relatório STN - RREO - Anexo 9
 *
 * URBEM Soluções de Gestão Pública Ltda
 * www.urbem.cnm.org.br
 *
 * Casos de uso: uc-06.01.10
 * 
 * $Id: $
 */

/**
 * Recebe como paramentro exercicio, entidade, periodo
 */

CREATE OR REPLACE FUNCTION contabilidade.restos_pagar_nao_processados(varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
  
  stExercicio         ALIAS FOR $1;
  stCodEntidade       ALIAS FOR $2;
  dtFinal             ALIAS FOR $3;

  dtInicial           VARCHAR := '';
  stExercicioAnterior VARCHAR := ''; 
  stSql               VARCHAR := '';
  stComplemento       VARCHAR := '';
  stDespesasCorrentes VARCHAR := '';
  reRegistro          RECORD;
  
  arTipo              VARCHAR[];
  indice              INT;

BEGIN
   
  dtInicial := '01/01/' || stExercicio;
  stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));
  
  -- cria a tabela temporaria para o valor nao processado em exercicios anteriores
  StSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_exercicios_anteriores AS

      SELECT empenhado.cod_empenho
           , empenhado.cod_entidade
           , sw_cgm.nom_cgm AS nom_entidade
           
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           ,''tmp_nao_processados_exercicios_anteriores''::VARCHAR AS tipo 
             
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
                           WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'') 
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND empenho_anulado_item.num_item = item_pre_empenho.num_item
            
                 WHERE empenho.exercicio < '''||stExercicioAnterior||'''
                   AND empenho.dt_empenho < TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') 
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
                                       WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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

                     AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

      GROUP BY empenhado.cod_empenho
             , empenhado.cod_entidade
             , sw_cgm.nom_cgm
             , conta_despesa.cod_estrutural
             , conta_despesa.descricao
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
           
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           ,''tmp_nao_processados_exercicio_anterior''::VARCHAR AS tipo 
             
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
                           WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'|| stExercicioAnterior||''',''dd/mm/yyyy'')
                        GROUP BY empenho_anulado_item.exercicio
                               , empenho_anulado_item.cod_pre_empenho
                               , empenho_anulado_item.num_item
                       ) AS empenho_anulado_item
                    ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                   AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                   AND empenho_anulado_item.num_item = item_pre_empenho.num_item
            
                 WHERE empenho.exercicio = '''|| stExercicioAnterior ||'''
                   AND empenho.dt_empenho BETWEEN TO_DATE(''01/01/'|| stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'|| stExercicioAnterior||''',''dd/mm/yyyy'')
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
                                       WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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
                     AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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

     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.exercicio = empenhado.exercicio
           AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

      GROUP BY empenhado.cod_empenho
             , empenhado.cod_entidade
             , sw_cgm.nom_cgm
             , conta_despesa.cod_estrutural
             , conta_despesa.descricao
             , despesa.dt_criacao
        HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
  ';

  EXECUTE stSql;

  --cria a tabela temporaria para o valor nao processado cancelado
  stSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_cancelado AS
      SELECT ( SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) AS vl_total
           , empenho.cod_empenho
           , empenho.cod_entidade
           , sw_cgm.nom_cgm AS nom_entidade
           
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           ,''tmp_nao_processados_cancelado''::VARCHAR AS tipo 
           
        FROM empenho.empenho 
  
  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio = empenho.exercicio
         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
  
  INNER JOIN (  SELECT empenho_anulado_item.exercicio
                     , empenho_anulado_item.cod_pre_empenho
                     , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                  FROM empenho.empenho_anulado_item
                 WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
              GROUP BY empenho_anulado_item.exercicio
                     , empenho_anulado_item.cod_pre_empenho
             ) AS empenho_anulado_item 
          ON empenho_anulado_item.exercicio = pre_empenho.exercicio
         AND empenho_anulado_item.cod_pre_empenho = pre_empenho.cod_pre_empenho
  
-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
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
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

   LEFT JOIN orcamento.orgao AS orgao_implantado
          ON orgao_implantado.exercicio = '''||stExercicio||'''
         AND orgao_implantado.num_orgao = restos_pre_empenho.num_orgao

       WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
         AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND ( NOT EXISTS ( SELECT 1
                              FROM empenho.nota_liquidacao
                             WHERE nota_liquidacao.exercicio_empenho = empenho.exercicio
                               AND nota_liquidacao.cod_empenho = empenho.cod_empenho
                               AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                               --AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                          ) 
                       OR
                   EXISTS ( SELECT 1
                              FROM empenho.nota_liquidacao
                             WHERE nota_liquidacao.exercicio_empenho = empenho.exercicio
                               AND nota_liquidacao.cod_empenho = empenho.cod_empenho
                               AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                               --AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                          )
             )
         AND empenho.cod_entidade IN ('||stCodEntidade||')

    GROUP BY empenho.cod_empenho
           , empenho.cod_entidade
           , sw_cgm.nom_cgm
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           , despesa.dt_criacao
      HAVING ( SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) > 0
  ';

  EXECUTE stSql;
  
  
  stSql := '
      CREATE TEMPORARY TABLE tmp_nao_processados_liquidado AS
     SELECT (COALESCE(SUM(nota_liquidacao_item.vl_total),0) - COALESCE(SUM(nota_liquidacao_item_anulado.vl_anulado),0)) AS vl_total
           , empenho.cod_empenho
           , empenho.cod_entidade
           , sw_cgm.nom_cgm AS nom_entidade
           
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           ,''tmp_nao_processados_liquidado''::VARCHAR AS tipo           
           
        FROM empenho.nota_liquidacao
        
  INNER JOIN empenho.empenho
          ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
         AND empenho.cod_entidade = nota_liquidacao.cod_entidade
         AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
  
  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio       = empenho.exercicio
         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

  INNER JOIN empenho.nota_liquidacao_item 
	  ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio 
	 AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade 
	 AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota 

  LEFT JOIN empenho.nota_liquidacao_item_anulado 
	  ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio 
	 AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota 
	 AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade 
	 AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item 
	 AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho 
	 AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item 
  
-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
   LEFT JOIN orcamento.despesa
          ON despesa.exercicio = pre_empenho_despesa.exercicio
         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         
   LEFT JOIN orcamento.conta_despesa
          ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
         AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

   LEFT JOIN empenho.restos_pre_empenho
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
         
       WHERE empenho.exercicio <= '''||stExercicio||'''
         AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
         AND empenho.cod_entidade IN ('||stCodEntidade||')

       -- AND nota_liquidacao_item_anulado.exercicio       IS NULL
       -- AND nota_liquidacao_item_anulado.cod_nota        IS NULL
       -- AND nota_liquidacao_item_anulado.cod_entidade    IS NULL
       -- AND nota_liquidacao_item_anulado.num_item        IS NULL
       -- AND nota_liquidacao_item_anulado.cod_pre_empenho IS NULL
       -- AND nota_liquidacao_item_anulado.exercicio_item  IS NULL

    GROUP BY empenho.cod_empenho
           , empenho.cod_entidade
           , sw_cgm.nom_cgm
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           , despesa.dt_criacao
      HAVING ( SUM(nota_liquidacao_item.vl_total) ) > 0';
   
   EXECUTE stSql;

  --cria a tabela temporaria para o valor nao processado pago
  stSql := '
    CREATE TEMPORARY TABLE tmp_nao_processados_pago AS
      SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_total
           , empenho.cod_empenho
           , empenho.cod_entidade
           , sw_cgm.nom_cgm AS nom_entidade
           
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           ,''tmp_nao_processados_pago''::VARCHAR AS tipo           
           
        FROM empenho.nota_liquidacao
        
  INNER JOIN empenho.empenho
          ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
         AND empenho.cod_entidade = nota_liquidacao.cod_entidade
         AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
  
  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio       = empenho.exercicio
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
                           WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga.exercicio
                               , nota_liquidacao_paga.cod_entidade
                               , nota_liquidacao_paga.cod_nota
                       ) AS nota_liquidacao_paga
  
             LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                               , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                            FROM empenho.nota_liquidacao_paga_anulada
                           WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                       ) AS nota_liquidacao_paga_anulada
                       
                    ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                   AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                   AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                   
              GROUP BY nota_liquidacao_paga.exercicio
                     , nota_liquidacao_paga.cod_entidade
                     , nota_liquidacao_paga.cod_nota
             ) AS liquidacao_paga
             
          ON liquidacao_paga.exercicio    = nota_liquidacao.exercicio
         AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
         AND liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota

-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
   LEFT JOIN orcamento.despesa
          ON despesa.exercicio = pre_empenho_despesa.exercicio
         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         
   LEFT JOIN orcamento.conta_despesa
          ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
         AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

   LEFT JOIN empenho.restos_pre_empenho
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

       WHERE empenho.exercicio <= '''||stExercicio||'''
         AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
         AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
         AND empenho.cod_entidade IN ('||stCodEntidade||')

    GROUP BY empenho.cod_empenho
           , empenho.cod_entidade
           , sw_cgm.nom_cgm
           , conta_despesa.cod_estrutural
           , conta_despesa.descricao
           , despesa.dt_criacao
      HAVING ( SUM(liquidacao_paga.vl_total) ) > 0 
  ';

  EXECUTE stSql; 

--consulta para retornar todas os orgaos para nao intra-orcamentarias
  stSql := '
    CREATE TEMPORARY TABLE tmp_restos AS
    
      SELECT nom_entidade
           , cod_estrutural
           , descricao
           , vl_total
           , tipo
        FROM tmp_nao_processados_exercicios_anteriores
      
       UNION ALL
      
      SELECT nom_entidade
           , cod_estrutural
           , descricao
           , vl_total
           , tipo
        FROM tmp_nao_processados_exercicio_anterior
        
       UNION ALL
       
      SELECT nom_entidade
           , cod_estrutural
           , descricao
           , vl_total
           , tipo
        FROM tmp_nao_processados_cancelado
        
       UNION ALL
       
      SELECT 
             nom_entidade
           , cod_estrutural
           , descricao
           , vl_total
           , tipo
        FROM tmp_nao_processados_pago
        
       UNION ALL
       
      SELECT 
             nom_entidade
           , cod_estrutural
           , descricao
           , vl_total
           , tipo
        FROM tmp_nao_processados_liquidado
    
        ORDER BY cod_estrutural ';

    EXECUTE stSql;


--  LAÇO PARA CALCULO DOS VALORES
	arTipo[1] := '3.%';
	arTipo[2] := '3.1.%';
	arTipo[3] := '3.2.%';
	arTipo[4] := '3.3.%';
    arTipo[5] := '4.%';
	arTipo[6] := '4.4.%';
	arTipo[7] := '4.5.%';
	arTipo[8] := '4.6.%'; 

	FOR indice IN 1..array_upper(arTipo,1) LOOP 
    
      IF indice = 1 THEN
	       stComplemento := ' ''DESPESAS CORRENTES''::VARCHAR AS descricao, 1::INTEGER AS nivel ';
           stDespesasCorrentes:= 'DESPESAS';
	  END IF;
      
      IF indice = 2 THEN
	       stComplemento := ' ''PESSOAS E ENCARGOS SOCIAIS''::VARCHAR AS descricao, 2::INTEGER AS nivel ';
           stDespesasCorrentes:= '';
	  END IF;
      
      IF indice = 3 THEN
	       stComplemento := ' ''JUROS E ENCARGOS DA DÍVIDA''::VARCHAR AS descricao, 2::INTEGER AS nivel ';
           stDespesasCorrentes:= '';
	  END IF;
      
      IF indice = 4 THEN
	       stComplemento := ' ''OUTRAS DESPESAS CORRENTES''::VARCHAR AS descricao, 2::INTEGER AS nivel ';
           stDespesasCorrentes:= '';
	  END IF;
      
      IF indice = 5 THEN
	       stComplemento := ' ''DESPESAS DE CAPITAL''::VARCHAR AS descricao, 1::INTEGER AS nivel ';  
	       stDespesasCorrentes:= '';
      END IF;
      
      IF indice = 6 THEN
	       stComplemento := ' ''INVESTIMENTOS''::VARCHAR AS descricao, 2::INTEGER AS nivel ';
           stDespesasCorrentes:= '';
	  END IF;
      
      IF indice = 7 THEN
	       stComplemento := ' ''INVERSÕES FINANCEIRAS''::VARCHAR AS descricao, 2::INTEGER AS nivel ';
           stDespesasCorrentes:= '';
	  END IF;

      IF indice = 8 THEN
	       stComplemento := ' ''AMORTIZAÇÃO DA DÍVIDA''::VARCHAR AS descricao, 2::INTEGER AS nivel ';
           stDespesasCorrentes:= '';
	  END IF;
    
      stSql := 'SELECT
                    '||stComplemento||',
                    
                    -- tmp_nao_processados_exercicios_anteriores
                    (  SELECT COALESCE(SUM(vl_total), 0.00)::numeric(14,2)
                        FROM tmp_restos
                       WHERE tipo = ''tmp_nao_processados_exercicios_anteriores''
                         AND '''||stDespesasCorrentes||''' ILIKE ''DESPESAS%'') AS exercicios_anteriores,
                    
                    -- tmp_nao_processados_exercicio_anterior
                    (  SELECT COALESCE(SUM(vl_total), 0.00)::numeric(14,2)
                         FROM tmp_restos
                        WHERE tipo = ''tmp_nao_processados_exercicio_anterior''
                          AND cod_estrutural LIKE '''||arTipo[indice]||''') AS exercicio_anterior,
                    
                    -- tmp_nao_processados_cancelado
                    (   SELECT COALESCE(SUM(vl_total), 0.00)::numeric(14,2)
                          FROM tmp_restos
                         WHERE tipo = ''tmp_nao_processados_cancelado''   
                           AND cod_estrutural LIKE '''||arTipo[indice]||''') AS cancelados,
                    
                    -- tmp_nao_processados_pago
                    (  SELECT COALESCE(SUM(vl_total), 0.00)::numeric(14,2)
                        FROM tmp_restos
                       WHERE tipo = ''tmp_nao_processados_pago''
                         AND cod_estrutural LIKE '''||arTipo[indice]||''') AS pagos,
                         
                    -- tmp_nao_processados_liquidado
                    (  SELECT COALESCE(SUM(vl_total), 0.00)::numeric(14,2)
                        FROM tmp_restos
                       WHERE tipo = ''tmp_nao_processados_liquidado''
                         AND cod_estrutural LIKE '''||arTipo[indice]||''') AS liquidados';
        FOR reRegistro IN EXECUTE stSql LOOP      
            RETURN next reRegistro;
        END LOOP;
  
  END LOOP;

  DROP TABLE tmp_nao_processados_exercicios_anteriores;
  DROP TABLE tmp_nao_processados_exercicio_anterior;
  DROP TABLE tmp_nao_processados_cancelado;
  DROP TABLE tmp_nao_processados_liquidado;
  DROP TABLE tmp_nao_processados_pago;
  DROP TABLE tmp_restos;

END;

$$ language 'plpgsql';

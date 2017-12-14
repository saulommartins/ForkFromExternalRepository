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
 * $Id: FSICONFIRelatorioAnexoDCAIG.plsql 62933 2015-07-09 14:18:16Z franver $
 */
/*
CREATE TYPE relatorio_anexo_dca_ig
    AS ( nivel                            INTEGER
       , cod_funcao                       INTEGER
       , cod_subfuncao                    INTEGER
       , descricao                        VARCHAR
       , vl_rp_nao_processados_pagos      NUMERIC
       , vl_rp_nao_processados_cancelados NUMERIC
       , vl_rp_processados_pagos          NUMERIC
       , vl_rp_processados_cancelados     NUMERIC
    );
*/
CREATE OR REPLACE FUNCTION siconfi.fn_relatorio_anexo_dca_ig(varchar,varchar,varchar) RETURNS SETOF relatorio_anexo_dca_ig AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    dtFinal             ALIAS FOR $3;
    dtInicial           VARCHAR := '';
    stExercicioAnterior VARCHAR := ''; 
    stSql               VARCHAR := '';  
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
BEGIN
    dtInicial := '01/01/'||stExercicio;
    stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));

    -- cria a tabela temporaria para o valor cancelado processado
    stSql := '
       CREATE TEMPORARY TABLE tmp_processados_cancelado AS
       SELECT empenho.cod_empenho
            , empenho.exercicio
            , empenho.cod_entidade
            , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                   THEN (CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                              THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                              ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                        )
                   ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
               END AS estrutural
            , CASE WHEN restos_pre_empenho.cod_funcao IS NOT NULL
                   THEN restos_pre_empenho.cod_funcao
                   ELSE despesa.cod_funcao
               END AS funcao
            , CASE WHEN restos_pre_empenho.cod_subfuncao IS NOT NULL
                   THEN restos_pre_empenho.cod_subfuncao
                   ELSE despesa.cod_subfuncao
               END AS subfuncao
            , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_total 
         FROM empenho.empenho 
   INNER JOIN empenho.pre_empenho
           ON pre_empenho.exercicio = empenho.exercicio
          AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
   INNER JOIN empenho.item_pre_empenho
           ON pre_empenho.exercicio = item_pre_empenho.exercicio
          AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
   INNER JOIN (SELECT empenho_anulado_item.exercicio
                    , empenho_anulado_item.cod_pre_empenho
                    , empenho_anulado_item.num_item
                    , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                 FROM empenho.empenho_anulado_item
                WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
             GROUP BY empenho_anulado_item.exercicio
                    , empenho_anulado_item.cod_pre_empenho
                    , empenho_anulado_item.num_item
              ) AS empenho_anulado_item 
           ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
          AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
          AND empenho_anulado_item.num_item = item_pre_empenho.num_item
   INNER JOIN (SELECT nota_liquidacao.cod_empenho
                    , nota_liquidacao.exercicio_empenho
                    , nota_liquidacao.cod_entidade
                 FROM empenho.nota_liquidacao
                WHERE nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
             GROUP BY nota_liquidacao.cod_empenho
                    , nota_liquidacao.exercicio_empenho
                    , nota_liquidacao.cod_entidade
              ) AS liquidacao
           ON liquidacao.cod_empenho = empenho.cod_empenho
          AND liquidacao.exercicio_empenho = empenho.exercicio
          AND liquidacao.cod_entidade = empenho.cod_entidade
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
        WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
          AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
          AND empenho.cod_entidade IN ('||stCodEntidade||')
     GROUP BY empenho.cod_empenho
            , empenho.exercicio
            , empenho.cod_entidade
            , estrutural
            , funcao
            , subfuncao
       HAVING ( SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) > 0.00
    ';
 
    EXECUTE stSql;

    -- cria a table temporaria para o valor processado pago
    stSql := '
       CREATE TEMPORARY TABLE tmp_processados_pago AS
       SELECT empenho.cod_empenho
            , empenho.exercicio
            , empenho.cod_entidade
            , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                   THEN (CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                              THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                              ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                          END
                        )
                   ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
               END AS estrutural
            , CASE WHEN restos_pre_empenho.cod_funcao IS NOT NULL
                   THEN restos_pre_empenho.cod_funcao
                   ELSE despesa.cod_funcao
               END AS funcao
            , CASE WHEN restos_pre_empenho.cod_subfuncao IS NOT NULL
                   THEN restos_pre_empenho.cod_subfuncao
                   ELSE despesa.cod_subfuncao
               END AS subfuncao
            , ( SUM(COALESCE(liquidacao_paga.vl_total,0.00)) ) AS vl_total
         FROM empenho.nota_liquidacao
   INNER JOIN empenho.empenho
           ON empenho.exercicio = nota_liquidacao.exercicio_empenho
          AND empenho.cod_entidade = nota_liquidacao.cod_entidade
          AND empenho.cod_empenho = nota_liquidacao.cod_empenho
   INNER JOIN empenho.pre_empenho
           ON pre_empenho.exercicio = empenho.exercicio
          AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
   INNER JOIN (SELECT nota_liquidacao_paga.exercicio
                    , nota_liquidacao_paga.cod_entidade
                    , nota_liquidacao_paga.cod_nota
                    , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
                 FROM (SELECT nota_liquidacao_paga.exercicio
                            , nota_liquidacao_paga.cod_entidade
                            , nota_liquidacao_paga.cod_nota
                            , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                         FROM empenho.nota_liquidacao_paga
                        WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                     GROUP BY nota_liquidacao_paga.exercicio
                            , nota_liquidacao_paga.cod_entidade
                            , nota_liquidacao_paga.cod_nota
                      ) AS nota_liquidacao_paga
            LEFT JOIN (SELECT nota_liquidacao_paga_anulada.exercicio
                            , nota_liquidacao_paga_anulada.cod_entidade
                            , nota_liquidacao_paga_anulada.cod_nota
                            , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                         FROM empenho.nota_liquidacao_paga_anulada
                        WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
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
          AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
          AND empenho.cod_entidade IN ('||stCodEntidade||')
     GROUP BY empenho.cod_empenho
            , empenho.exercicio
            , empenho.cod_entidade
            , estrutural
            , funcao
            , subfuncao
       HAVING ( SUM(COALESCE(liquidacao_paga.vl_total,0.00)) ) > 0.00
    ';
  
    EXECUTE stSql;

    --cria a tabela temporaria para o valor nao processado cancelado
    stSql := '
         CREATE TEMPORARY TABLE tmp_nao_processados_cancelado AS
         SELECT empenho.cod_empenho
              , empenho.exercicio
              , empenho.cod_entidade
              , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                     THEN (CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                                THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                                ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                            END
                          )
                     ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
                 END AS estrutural
              , CASE WHEN restos_pre_empenho.cod_funcao IS NOT NULL
                     THEN restos_pre_empenho.cod_funcao
                     ELSE despesa.cod_funcao
                 END AS funcao
              , CASE WHEN restos_pre_empenho.cod_subfuncao IS NOT NULL
                     THEN restos_pre_empenho.cod_subfuncao
                     ELSE despesa.cod_subfuncao
                 END AS subfuncao
              , ( SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) AS vl_total
           FROM empenho.empenho 
     INNER JOIN empenho.pre_empenho
             ON pre_empenho.exercicio = empenho.exercicio
            AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
     INNER JOIN (SELECT empenho_anulado_item.exercicio
                      , empenho_anulado_item.cod_pre_empenho
                      , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                   FROM empenho.empenho_anulado_item
                  WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
               GROUP BY empenho_anulado_item.exercicio
                      , empenho_anulado_item.cod_pre_empenho
                ) AS empenho_anulado_item 
             ON empenho_anulado_item.exercicio = pre_empenho.exercicio
            AND empenho_anulado_item.cod_pre_empenho = pre_empenho.cod_pre_empenho
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
          WHERE empenho.exercicio <= '''||stExercicioAnterior||'''
            AND empenho.dt_empenho <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
            AND (NOT EXISTS( SELECT 1
                               FROM empenho.nota_liquidacao
                              WHERE nota_liquidacao.exercicio_empenho = empenho.exercicio
                                AND nota_liquidacao.cod_empenho = empenho.cod_empenho
                                AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                                --AND nota_liquidacao.dt_liquidacao <= TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
                           ) OR
                     EXISTS( SELECT 1
                               FROM empenho.nota_liquidacao
                              WHERE nota_liquidacao.exercicio_empenho = empenho.exercicio
                                AND nota_liquidacao.cod_empenho = empenho.cod_empenho
                                AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                                --AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                           )
                )
            AND empenho.cod_entidade IN ('||stCodEntidade||')
       GROUP BY empenho.cod_empenho
              , empenho.exercicio
              , empenho.cod_entidade
              , estrutural
              , funcao
              , subfuncao
         HAVING ( SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) ) > 0
    ';
  
    EXECUTE stSql;

    --cria a tabela temporaria para o valor nao processado cancelado
    stSql := '
      CREATE TEMPORARY TABLE tmp_nao_processados_pago AS
         SELECT empenho.cod_empenho
              , empenho.exercicio
              , empenho.cod_entidade
              , CASE WHEN restos_pre_empenho.cod_estrutural IS NOT NULL 
                     THEN (CASE WHEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),1,2) = ''00''
                                THEN SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),5,2)
                                ELSE SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,''.'',''''),3,2)
                            END
                          )
                     ELSE SUBSTR(REPLACE(conta_despesa.cod_estrutural,''.'',''''),3,2)
                 END AS estrutural
              , CASE WHEN restos_pre_empenho.cod_funcao IS NOT NULL
                     THEN restos_pre_empenho.cod_funcao
                     ELSE despesa.cod_funcao
                 END AS funcao
              , CASE WHEN restos_pre_empenho.cod_subfuncao IS NOT NULL
                     THEN restos_pre_empenho.cod_subfuncao
                     ELSE despesa.cod_subfuncao
                 END AS subfuncao
              , ( SUM(liquidacao_paga.vl_total) ) AS vl_total
           FROM empenho.nota_liquidacao
     INNER JOIN empenho.empenho
             ON empenho.exercicio = nota_liquidacao.exercicio_empenho
            AND empenho.cod_entidade = nota_liquidacao.cod_entidade
            AND empenho.cod_empenho = nota_liquidacao.cod_empenho
     INNER JOIN empenho.pre_empenho
             ON pre_empenho.exercicio = empenho.exercicio
            AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
     INNER JOIN (SELECT nota_liquidacao_paga.exercicio
                      , nota_liquidacao_paga.cod_entidade
                      , nota_liquidacao_paga.cod_nota
                      , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
                   FROM (SELECT nota_liquidacao_paga.exercicio
                              , nota_liquidacao_paga.cod_entidade
                              , nota_liquidacao_paga.cod_nota
                              , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                           FROM empenho.nota_liquidacao_paga
                          WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                       GROUP BY nota_liquidacao_paga.exercicio
                              , nota_liquidacao_paga.cod_entidade
                              , nota_liquidacao_paga.cod_nota
                        ) AS nota_liquidacao_paga
    
              LEFT JOIN (SELECT nota_liquidacao_paga_anulada.exercicio
                              , nota_liquidacao_paga_anulada.cod_entidade
                              , nota_liquidacao_paga_anulada.cod_nota
                              , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                           FROM empenho.nota_liquidacao_paga_anulada
                          WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
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
              , empenho.exercicio
              , empenho.cod_entidade
              , estrutural
              , funcao
              , subfuncao
         HAVING ( SUM(COALESCE(liquidacao_paga.vl_total,0.00)) ) > 0.00
    ';
   
    EXECUTE stSql; 

    stSql := '
        SELECT nivel
             , cod_funcao
             , cod_subfuncao
             , descricao
             , 0.00::NUMERIC AS vl_rp_nao_processados_pagos
             , 0.00::NUMERIC AS vl_rp_nao_processados_cancelados
             , 0.00::NUMERIC AS vl_rp_processados_pagos
             , 0.00::NUMERIC AS vl_rp_processados_cancelados
          FROM (SELECT 1::INTEGER AS nivel
                     , cod_funcao
                     , 0 AS cod_subfuncao
                     , descricao
                  FROM orcamento.funcao
              GROUP BY cod_funcao
                     , descricao
             UNION ALL 
                SELECT 2::INTEGER AS nivel
                     , cod_funcao
                     , cod_subfuncao
                     , subfuncao.descricao
                  FROM orcamento.funcao
                     , orcamento.subfuncao
              GROUP BY cod_funcao
                     , cod_subfuncao
                     , subfuncao.descricao
               ) AS foo
      ORDER BY cod_funcao
             , cod_subfuncao
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := siconfi.fn_relatorio_anexo_dca_ig_totalizador( reRegistro.cod_funcao , reRegistro.cod_subfuncao );
        reRegistro.vl_rp_nao_processados_pagos      := arRetorno[1];
        reRegistro.vl_rp_nao_processados_cancelados := arRetorno[2];
        reRegistro.vl_rp_processados_pagos          := arRetorno[3];
        reRegistro.vl_rp_processados_cancelados     := arRetorno[4];
        
        IF  ( reRegistro.vl_rp_nao_processados_pagos      = 0.00 ) AND
            ( reRegistro.vl_rp_nao_processados_cancelados = 0.00 ) AND
            ( reRegistro.vl_rp_processados_pagos          = 0.00 ) AND
            ( reRegistro.vl_rp_processados_cancelados     = 0.00 )
        THEN
        
        ELSE
            RETURN NEXT reRegistro;
        END IF;
    END LOOP;


    DROP TABLE tmp_processados_cancelado;
    DROP TABLE tmp_processados_pago;
    DROP TABLE tmp_nao_processados_cancelado;
    DROP TABLE tmp_nao_processados_pago;

END;
$$ language 'plpgsql';

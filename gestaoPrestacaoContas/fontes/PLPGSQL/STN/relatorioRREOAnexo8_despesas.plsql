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
    * Nota:
    * O parametro de entrada inOpcao (integer)
    * se refere ao tipo de despesa desejada.
    * Opções válidas para inOpcao:
    
    1) 1 = Despesas realizadas com recursos do Fundeb
    2) 2 = MDE - Despesas Realizadas com Recursos de 
           Ações Típicas de Manutenção do Ensino
    3) 3 = Outras Despesas MDE
   
*/
/*
    $Id: relatorioRREOAnexo8_despesas.plsql 66693 2016-11-25 18:28:18Z carlos.silva $
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo8_despesas(stExercicio VARCHAR, stEntidades VARCHAR, inOpcao INTEGER, stDtIni VARCHAR, stDtFim VARCHAR) RETURNS SETOF RECORD AS $$

DECLARE 

    stDtIniExercicio VARCHAR := ''; 
    stOperacao       CHARACTER(1);
    stSQL            VARCHAR := '';
    stSQLaux         VARCHAR := '';
    stSQLaux2        VARCHAR := '';
    stSQLRestos      VARCHAR := '';
    reReg            RECORD;
    reRegNivel       RECORD;

BEGIN 
    -- Definicao de Datas conforme Bimestre selecionado
    stDtIniExercicio := '01/01/' || stExercicio;

    --Validando restos de acordo com o bimestre
    IF ( stDtIni = '01/11/'||stExercicio||'' AND stDtFim = '31/12/'||stExercicio||'') THEN
        stSQLRestos := ' COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                , '|| quote_literal(stExercicio)      ||'
                                                                                , '|| quote_literal(stEntidades)      ||'
                                                                                , '|| quote_literal(stDtIni) ||'
                                                                                , '|| quote_literal(stDtFim)          ||'
                                                                                , false )
                        ), 0.00) AS vl_restos_inscritos';
    ELSE
        stSQLRestos := ' COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                , '|| quote_literal(stExercicio) ||'
                                                                                , '|| quote_literal(stEntidades) ||'
                                                                                , '|| quote_literal(stDtIni)     ||'
                                                                                , '|| quote_literal(stDtFim)     ||'
                                                                                , false )
                        ), 0.00) AS vl_restos_inscritos';
    END IF;

    -- --------------------------------------------
    -- Inicio das Tabelas Temporarias
    -- --------------------------------------------

    stSQL := '
    
    CREATE TEMPORARY TABLE tmp_despesa_liberada AS (
      SELECT cd.exercicio
           , cd.cod_estrutural
           , d.cod_entidade
           , d.num_orgao
           , d.num_unidade
           , d.cod_despesa
           , d.cod_recurso
           , SUM(COALESCE(d.vl_original,0.00)) AS vl_original
           , COALESCE((SUM(COALESCE(sups.vl_suplementado,0.00)) - SUM(COALESCE(supr.vl_reduzido,0.00))), 0.00) AS vl_credito_adicional 
        FROM orcamento.conta_despesa AS cd
  INNER JOIN orcamento.despesa AS d
          ON d.exercicio = cd.exercicio
         AND d.cod_conta = cd.cod_conta
   LEFT JOIN (
              SELECT sups.exercicio
                   , sups.cod_despesa
                   , SUM(sups.valor) AS vl_suplementado 
                FROM orcamento.suplementacao AS sup
           LEFT JOIN orcamento.suplementacao_anulada AS sup_a
                  ON sup_a.exercicio = sup.exercicio
                 AND sup_a.cod_suplementacao = sup.cod_suplementacao
           LEFT JOIN orcamento.suplementacao_anulada AS sup_anulada
                  ON sup_anulada.exercicio = sup.exercicio
                 AND sup_anulada.cod_suplementacao_anulacao = sup.cod_suplementacao
          INNER JOIN orcamento.suplementacao_suplementada AS sups
                  ON sup.exercicio = sups.exercicio
                 AND sup.cod_suplementacao = sups.cod_suplementacao
               WHERE sup.exercicio = '''||stExercicio||'''
                 AND sup.dt_suplementacao BETWEEN TO_DATE('''||stDtIniExercicio||''', ''dd/mm/yyyy'')
                                              AND TO_DATE('''||stDtFim||''', ''dd/mm/yyyy'')
                 AND sup_a.cod_suplementacao_anulacao IS NULL
                 AND sup_anulada.cod_suplementacao IS NULL
            GROUP BY sups.exercicio
                   , sups.cod_despesa
             ) sups
          ON sups.exercicio = d.exercicio
         AND sups.cod_despesa = d.cod_despesa
   LEFT JOIN (
              SELECT supr.exercicio
                   , supr.cod_despesa
                   , SUM(supr.valor) as vl_reduzido
                FROM orcamento.suplementacao AS sup
           LEFT JOIN orcamento.suplementacao_anulada AS sup_a
                  ON sup_a.exercicio = sup.exercicio
                 AND sup_a.cod_suplementacao = sup.cod_suplementacao
           LEFT JOIN orcamento.suplementacao_anulada AS sup_anulada
                  ON sup_anulada.exercicio = sup.exercicio
                 AND sup_anulada.cod_suplementacao_anulacao = sup.cod_suplementacao
          INNER JOIN orcamento.suplementacao_reducao AS supr
                  ON sup.exercicio = supr.exercicio
                 AND sup.cod_suplementacao = supr.cod_suplementacao
               WHERE sup.exercicio = '''||stExercicio||'''
                 AND sup.dt_suplementacao BETWEEN TO_DATE('''||stDtIniExercicio||''', ''dd/mm/yyyy'')
                                              AND TO_DATE('''||stDtFim||''', ''dd/mm/yyyy'')
                 AND sup_a.cod_suplementacao_anulacao IS NULL
                 AND sup_anulada.cod_suplementacao IS NULL
            GROUP BY supr.exercicio
                   , supr.cod_despesa
             ) AS supr
          ON supr.exercicio = d.exercicio
         AND supr.cod_despesa = d.cod_despesa 
       WHERE cd.exercicio = '''||stExercicio||'''
         AND d.cod_entidade IN ('||stEntidades||') 
    GROUP BY cd.exercicio
           , cd.cod_estrutural
           , d.cod_entidade
           , d.num_orgao
           , d.num_unidade
           , d.cod_despesa
           , d.cod_recurso
    );';
    
    EXECUTE stSQL;

    /*
    Despesas Liquidadas do primeiro dia do exercicio
    até o último dia do bimestre selecionado
    */
    
    stSQL := '
      CREATE TEMPORARY TABLE tmp_despesa_liquidada_total AS (
      SELECT pedcd.exercicio
           , pedcd.cod_despesa
           , pedcd.cod_recurso
           , pedcd.cod_funcao
           , pedcd.cod_subfuncao
           , pedcd.cod_estrutural
           , COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado 
        FROM empenho.pre_empenho AS pe
   LEFT JOIN (
              SELECT ped.exercicio
                   , ped.cod_pre_empenho
                   , d.cod_despesa
                   , d.cod_recurso
                   , d.cod_funcao
                   , d.cod_subfuncao
                   , cd.cod_estrutural
                FROM empenho.pre_empenho_despesa AS ped
          INNER JOIN orcamento.despesa AS d
                  ON ped.exercicio   = d.exercicio
                 AND ped.cod_despesa = d.cod_despesa
          INNER JOIN orcamento.conta_despesa AS cd
                  ON cd.exercicio = d.exercicio
                 AND cd.cod_conta = d.cod_conta
               WHERE ped.exercicio = '''||stExercicio||'''
             ) AS pedcd
          ON pe.exercicio = pedcd.exercicio
         AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
  INNER JOIN empenho.empenho AS e
          ON e.exercicio = pe.exercicio
         AND e.cod_pre_empenho = pe.cod_pre_empenho
  INNER JOIN empenho.nota_liquidacao AS nl
          ON nl.exercicio_empenho = e.exercicio
         AND nl.cod_entidade = e.cod_entidade
         AND nl.cod_empenho = e.cod_empenho
  INNER JOIN empenho.nota_liquidacao_item AS nli
          ON nli.exercicio = nl.exercicio
         AND nli.cod_entidade = nl.cod_entidade
         AND nli.cod_nota = nl.cod_nota 
       WHERE e.exercicio = '''||stExercicio||'''
         AND e.cod_entidade IN ('||stEntidades||')
         AND nl.dt_liquidacao BETWEEN to_date('''||stDtIniExercicio||''', ''dd/mm/yyyy'')
                                  AND to_date('''||stDtFim||''', ''dd/mm/yyyy'') 
    GROUP BY pedcd.exercicio
           , pedcd.cod_despesa
           , pedcd.cod_recurso
           , pedcd.cod_funcao
           , pedcd.cod_subfuncao
           , pedcd.cod_estrutural
    )';
    
    EXECUTE stSQL;
    
    /*
    Despesas Estornadas do primeiro dia do exercicio
    até o último dia do bimestre selecionado
    */
    stSQL := '
    CREATE TEMPORARY TABLE tmp_despesa_estornada_total AS (
      SELECT pedcd.exercicio
           , pedcd.cod_despesa
           , pedcd.cod_subfuncao
           , pedcd.cod_recurso
           , pedcd.cod_estrutural
           , COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_estornado 
        FROM empenho.pre_empenho AS pe
   LEFT JOIN (
              SELECT ped.exercicio
                   , ped.cod_pre_empenho
                   , d.cod_despesa
                   , d.cod_subfuncao
                   , d.cod_recurso
                   , conta_despesa.cod_estrutural
                FROM empenho.pre_empenho_despesa AS ped
          INNER JOIN orcamento.despesa AS d
                  ON ped.exercicio   = d.exercicio
                 AND ped.cod_despesa = d.cod_despesa
          INNER JOIN orcamento.conta_despesa
                  ON conta_despesa.exercicio = d.exercicio
                 AND conta_despesa.cod_conta = d.cod_conta
               WHERE ped.exercicio = '''||stExercicio||'''
             ) AS pedcd
          ON pe.exercicio = pedcd.exercicio
         AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
  INNER JOIN empenho.empenho AS e
          ON e.exercicio = pe.exercicio
         AND e.cod_pre_empenho = pe.cod_pre_empenho
  INNER JOIN empenho.nota_liquidacao AS nl
          ON nl.exercicio_empenho = e.exercicio
         AND nl.cod_entidade = e.cod_entidade
         AND nl.cod_empenho = e.cod_empenho
  INNER JOIN empenho.nota_liquidacao_item AS nli
          ON nli.exercicio = nl.exercicio
         AND nli.cod_entidade = nl.cod_entidade
         AND nli.cod_nota = nl.cod_nota 
  INNER JOIN (
              SELECT exercicio
                   , cod_entidade
                   , cod_nota
                   , num_item
                   , cod_pre_empenho
                   , exercicio_item
                   , timestamp
                   , COALESCE(SUM(vl_anulado), 0.00) AS vl_anulado 
                FROM empenho.nota_liquidacao_item_anulado 
               WHERE exercicio = '''||stExercicio||'''
                 AND cod_entidade IN ('||stEntidades||')
            GROUP BY exercicio
                   , cod_entidade
                   , cod_nota
                   , num_item
                   , cod_pre_empenho
                   , exercicio_item
                   , timestamp 
             ) AS nlia
          ON nli.exercicio = nlia.exercicio
         AND nli.cod_nota = nlia.cod_nota
         AND nli.cod_entidade = nlia.cod_entidade
         AND nli.num_item = nlia.num_item
         AND nli.cod_pre_empenho = nlia.cod_pre_empenho
         AND nli.exercicio_item = nlia.exercicio_item 
       WHERE e.exercicio = '''||stExercicio||'''
         AND e.cod_entidade IN ('||stEntidades||')
         AND TO_DATE(TO_CHAR(nlia.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN TO_DATE('''||stDtIniExercicio||''', ''dd/mm/yyyy'') 
                                                                              AND TO_DATE('''||stDtFim||''', ''dd/mm/yyyy'') 
    GROUP BY pedcd.exercicio
           , pedcd.cod_despesa
           , pedcd.cod_recurso
           , pedcd.cod_subfuncao
           , pedcd.cod_estrutural
    )';
    
    EXECUTE stSQL;
    
    -- --------------------------------------------
    -- Fim das Tabelas Temporarias
    -- --------------------------------------------

    -- --------------------------------------------
    -- FUNDEB
    -- Despesas do Fundeb
    -- --------------------------------------------
    IF (inOpcao = 1) THEN
        
        stSQL := '
        CREATE TEMPORARY TABLE tmp_rreo_retorno AS (
          SELECT *
            FROM (
                  SELECT CAST(13 AS INTEGER) AS grupo
                       , CAST(0 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Pagamento dos Profissionais do Magistério'' AS VARCHAR) AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) tb1
       UNION ALL 
          SELECT *
            FROM (
               SELECT CAST(13 AS INTEGER) AS grupo
                    , CAST(1 AS INTEGER) AS nivel
                    , CAST(0 AS INTEGER) AS sub_nivel
                    , CAST(''Com Ensino Infantil'' AS VARCHAR) AS descricao
                    , COALESCE(SUM(dot_ini), 0.00) AS dot_ini
                    , COALESCE(SUM(dot_atu), 0.00) AS dot_atu
                    , COALESCE(SUM(liq_tot), 0.00) AS liq_tot
                    , COALESCE(SUM(pct_liquidado), 0.00) AS pct_liquidado
                    , COALESCE(SUM(pct_empenhado), 0.00) AS pct_empenhado
                    , COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) AS vl_empenhado_ate_bimestre
                    , COALESCE((SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot)), 0.00) AS vl_restos_inscritos
                 FROM (
                       SELECT 
                              COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                            , SUM(COALESCE(tdl.vl_original, 0.00) + COALESCE(tdl.vl_credito_adicional, 0.00)) AS dot_atu
                            , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                            , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                            , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                            , COALESCE((SELECT *
                                          FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                             , '''||stExercicio||'''
                                                                                             , '''||stEntidades||'''
                                                                                             , '''||stDtIniExercicio||'''
                                                                                             , '''||stDtFim||'''
                                                                                             , cdd.cod_recurso::VARCHAR
                                                                                             , ''12''
                                                                                             , cdd.cod_subfuncao::VARCHAR
                                                                                             , cdd.cod_despesa::VARCHAR
                                                                                             , false )
                                      ), 0.00) AS vl_empenhado_ate_bimestre
                         FROM (
                               SELECT despesa.exercicio
                                 , despesa.cod_recurso
                                 , despesa.cod_despesa
                                 , despesa.cod_subfuncao
                                 , despesa.cod_funcao
                                 , conta_despesa.cod_estrutural
                                 , vinculo_recurso_acao.cod_tipo_educacao
                                 , vinculo_stn_recurso.cod_vinculo
                          
                                FROM stn.vinculo_recurso_acao
                          
                          INNER JOIN ppa.acao
                              ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                 AND acao.ativo = TRUE
                          
                          INNER JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                          
                          INNER JOIN orcamento.pao
                              ON pao.num_pao = pao_ppa_acao.num_pao 
                                 AND pao.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.despesa
                              ON despesa.num_pao = pao_ppa_acao.num_pao 
                                 AND despesa.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.conta_despesa
                              ON despesa.exercicio = conta_despesa.exercicio
                                 AND despesa.cod_conta = conta_despesa.cod_conta
                                 
                          INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso
                          
                          INNER JOIN stn.vinculo_stn_recurso
                              ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                          
                              WHERE despesa.cod_funcao = 12
                                AND despesa.exercicio = '''||stExercicio||'''
                                AND despesa.cod_entidade IN ('||stEntidades||')     
                                AND despesa.cod_subfuncao = 365 -- educação infantil
                                AND vinculo_recurso_acao.cod_tipo = 1 -- Recursos Pagamento Profissionais Magistério
                                AND vinculo_recurso_acao.cod_vinculo = 1 -- fundeb -- recurso 30
                              ) AS cdd
                           -- suplementacoes e reduções de despesa
                    LEFT JOIN tmp_despesa_liberada AS tdl
                           ON tdl.exercicio   = cdd.exercicio
                          AND tdl.cod_despesa = cdd.cod_despesa
                          AND tdl.cod_recurso = cdd.cod_recurso
                           -- liquidacao total no exercicio 
                    LEFT JOIN tmp_despesa_liquidada_total AS tt
                           ON tt.exercicio   = cdd.exercicio
                          AND tt.cod_despesa = cdd.cod_despesa
                          AND tt.cod_recurso = cdd.cod_recurso
                    LEFT JOIN tmp_despesa_estornada_total AS test_tot
                           ON test_tot.exercicio = cdd.exercicio
                          AND test_tot.cod_despesa = cdd.cod_despesa
                          AND test_tot.cod_recurso = cdd.cod_recurso
                           -- educação infantil 
                        WHERE cdd.cod_subfuncao IN (365)
                     GROUP BY cdd.cod_recurso
                            , cdd.cod_estrutural
                            , cdd.cod_subfuncao
                            , cdd.cod_despesa
                      ) AS tb2_a
                  ) AS tb2a
       UNION ALL
          SELECT *
            FROM (
               SELECT CAST(13 AS INTEGER) AS grupo
                    , CAST(2 AS INTEGER) AS nivel
                    , CAST(0 AS INTEGER) AS sub_nivel
                    , CAST(''Com Ensino Fundamental'' AS VARCHAR) AS descricao
                    , COALESCE(SUM(dot_ini), 0.00) AS dot_ini
                    , COALESCE(SUM(dot_atu), 0.00) AS dot_atu
                    , COALESCE(SUM(liq_tot), 0.00) AS liq_tot
                    , COALESCE(SUM(pct_liquidado), 0.00) AS pct_liquidado
                    , COALESCE(SUM(pct_empenhado), 0.00) AS pct_empenhado
                    , COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) AS vl_empenhado_ate_bimestre
                    , COALESCE((SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot)), 0.00) AS vl_restos_inscritos
                 FROM (
                       SELECT 
                              COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                            , SUM(COALESCE(tdl.vl_original, 0.00) + COALESCE(tdl.vl_credito_adicional, 0.00)) AS dot_atu
                            , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                            , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                            , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                            , COALESCE((SELECT *
                                          FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                             , '''||stExercicio||'''
                                                                                             , '''||stEntidades||'''
                                                                                             , '''||stDtIniExercicio||'''
                                                                                             , '''||stDtFim||'''
                                                                                             , cdd.cod_recurso::VARCHAR
                                                                                             , ''12''
                                                                                             , cdd.cod_subfuncao::VARCHAR
                                                                                             , cdd.cod_despesa::VARCHAR
                                                                                             , false )
                                      ), 0.00) AS vl_empenhado_ate_bimestre
                         FROM (
                               SELECT despesa.exercicio
                                 , despesa.cod_recurso
                                 , despesa.cod_despesa
                                 , despesa.cod_subfuncao
                                 , despesa.cod_funcao
                                 , conta_despesa.cod_estrutural
                                 , vinculo_recurso_acao.cod_tipo_educacao
                                 , vinculo_stn_recurso.cod_vinculo
                          
                                FROM stn.vinculo_recurso_acao
                          
                          INNER JOIN ppa.acao
                              ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                 AND acao.ativo = TRUE
                          
                          INNER JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                          
                          INNER JOIN orcamento.pao
                              ON pao.num_pao = pao_ppa_acao.num_pao 
                                 AND pao.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.despesa
                              ON despesa.num_pao = pao_ppa_acao.num_pao 
                                 AND despesa.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.conta_despesa
                              ON despesa.exercicio = conta_despesa.exercicio
                                 AND despesa.cod_conta = conta_despesa.cod_conta
                                 
                          INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso
                          
                          INNER JOIN stn.vinculo_stn_recurso
                              ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                          
                              WHERE despesa.cod_funcao = 12
                                AND despesa.exercicio = '''||stExercicio||'''
                                AND despesa.cod_entidade IN ('||stEntidades||')       
                                AND despesa.cod_subfuncao = 361 -- educação fundamental
                                AND vinculo_recurso_acao.cod_tipo = 1 -- Recursos Pagamento Profissionais Magistério
                                AND vinculo_recurso_acao.cod_vinculo = 1 -- fundeb -- recurso 30
                              ) AS cdd
                             
                           -- suplementacoes e reduções de despesa
                    LEFT JOIN tmp_despesa_liberada AS tdl
                           ON tdl.exercicio   = cdd.exercicio
                          AND tdl.cod_despesa = cdd.cod_despesa
                          AND tdl.cod_recurso = cdd.cod_recurso
                           -- liquidacao total no exercicio 
                    LEFT JOIN tmp_despesa_liquidada_total AS tt
                           ON tt.exercicio   = cdd.exercicio
                          AND tt.cod_despesa = cdd.cod_despesa
                          AND tt.cod_recurso = cdd.cod_recurso
                    LEFT JOIN tmp_despesa_estornada_total AS test_tot
                           ON test_tot.exercicio = cdd.exercicio
                          AND test_tot.cod_despesa = cdd.cod_despesa
                          AND test_tot.cod_recurso = cdd.cod_recurso
                             -- educação infantil 
                   -- ensino fundamental , educação de jovens e adultos, educação especial
                        WHERE cdd.cod_subfuncao = 361
                     GROUP BY cdd.cod_recurso
                            , cdd.cod_estrutural
                            , cdd.cod_subfuncao
                            , cdd.cod_despesa
                      ) AS tb2_b
                 ) AS tb2b
        UNION ALL
          SELECT *
            FROM (
                  SELECT CAST(14 AS INTEGER) AS grupo
                       , CAST(0 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Outras Despesas'' AS VARCHAR) AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) tb3 
        UNION ALL 
          SELECT *
            FROM (
               SELECT CAST(14 AS INTEGER) AS grupo
                    , CAST(1 AS INTEGER) AS nivel
                    , CAST(0 AS INTEGER) AS sub_nivel
                    , CAST(''Com Ensino Infantil'' AS VARCHAR) AS descricao
                    , COALESCE(SUM(dot_ini), 0.00) AS dot_ini
                    , COALESCE(SUM(dot_atu), 0.00) AS dot_atu
                    , COALESCE(SUM(liq_tot), 0.00) AS liq_tot
                    , COALESCE(SUM(pct_liquidado), 0.00) AS pct_liquidado
                    , COALESCE(SUM(pct_empenhado), 0.00) AS pct_empenhado
                    , COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) AS vl_empenhado_ate_bimestre
                    , COALESCE((SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot)), 0.00) AS vl_restos_inscritos
                 FROM (
                       SELECT 
                              COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                            , SUM(COALESCE(tdl.vl_original, 0.00) + COALESCE(tdl.vl_credito_adicional, 0.00)) AS dot_atu
                            , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                            , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                            , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                            , COALESCE((SELECT *
                                          FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                             , '''||stExercicio||'''
                                                                                             , '''||stEntidades||'''
                                                                                             , '''||stDtIniExercicio||'''
                                                                                             , '''||stDtFim||'''
                                                                                             , cdd.cod_recurso::VARCHAR
                                                                                             , ''12''
                                                                                             , cdd.cod_subfuncao::VARCHAR
                                                                                             , cdd.cod_despesa::VARCHAR
                                                                                             , false )
                                      ), 0.00) AS vl_empenhado_ate_bimestre
                         FROM (
                                    SELECT despesa.exercicio
                                      , despesa.cod_recurso
                                      , despesa.cod_despesa
                                      , despesa.cod_subfuncao
                                      , despesa.cod_funcao
                                      , conta_despesa.cod_estrutural
                                      , vinculo_recurso_acao.cod_tipo_educacao
                                      , vinculo_stn_recurso.cod_vinculo
                               
                                     FROM stn.vinculo_recurso_acao
                               
                               INNER JOIN ppa.acao
                                   ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                      AND acao.ativo = TRUE
                               
                               INNER JOIN orcamento.pao_ppa_acao
                                   ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                               
                               INNER JOIN orcamento.pao
                                   ON pao.num_pao = pao_ppa_acao.num_pao 
                                      AND pao.exercicio = pao_ppa_acao.exercicio 
                               
                               INNER JOIN orcamento.despesa
                                   ON despesa.num_pao = pao_ppa_acao.num_pao 
                                      AND despesa.exercicio = pao_ppa_acao.exercicio 
                               
                               INNER JOIN orcamento.conta_despesa
                                   ON despesa.exercicio = conta_despesa.exercicio
                                      AND despesa.cod_conta = conta_despesa.cod_conta
                                      
                          INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso
                               
                               INNER JOIN stn.vinculo_stn_recurso
                                   ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                               
                                   WHERE despesa.cod_funcao = 12
                                     AND despesa.exercicio = '''||stExercicio||'''
                                     AND despesa.cod_entidade IN ('||stEntidades||')       
                                     AND despesa.cod_subfuncao = 365 -- educação infantil
                                     AND vinculo_recurso_acao.cod_tipo = 2 -- Outros Recursos
                                     AND vinculo_recurso_acao.cod_vinculo = 1 -- fundeb -- recurso 30
                              ) AS cdd
                           -- suplementacoes e reduções de despesa
                    LEFT JOIN tmp_despesa_liberada AS tdl
                           ON tdl.exercicio   = cdd.exercicio
                          AND tdl.cod_despesa = cdd.cod_despesa
                          AND tdl.cod_recurso = cdd.cod_recurso
                           -- liquidacao total no exercicio 
                    LEFT JOIN tmp_despesa_liquidada_total AS tt
                           ON tt.exercicio   = cdd.exercicio
                          AND tt.cod_despesa = cdd.cod_despesa
                          AND tt.cod_recurso = cdd.cod_recurso
                    LEFT JOIN tmp_despesa_estornada_total AS test_tot
                           ON test_tot.exercicio = cdd.exercicio
                          AND test_tot.cod_despesa = cdd.cod_despesa
                          AND test_tot.cod_recurso = cdd.cod_recurso
                           -- educação infantil 
                        WHERE cdd.cod_subfuncao = 365
                     GROUP BY cdd.cod_recurso
                            , cdd.cod_estrutural
                            , cdd.cod_subfuncao
                            , cdd.cod_despesa
                      ) AS tb3_a
                 ) AS tb3a
            
        UNION ALL 
          SELECT *
            FROM (
              SELECT CAST(14 AS INTEGER) AS grupo
                   , CAST(2 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''Com Ensino Fundamental'' AS VARCHAR) AS descricao
                   , COALESCE(SUM(dot_ini), 0.00) AS dot_ini
                   , COALESCE(SUM(dot_atu), 0.00) AS dot_atu
                   , COALESCE(SUM(liq_tot), 0.00) AS liq_tot
                   , COALESCE(SUM(pct_liquidado), 0.00) AS pct_liquidado
                   , COALESCE(SUM(pct_empenhado), 0.00) AS pct_empenhado
                   , COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) AS vl_empenhado_ate_bimestre
                   , COALESCE((SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot)), 0.00) AS vl_restos_inscritos
                FROM (
                      SELECT 
                             COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                           , SUM(COALESCE(tdl.vl_original, 0.00) + COALESCE(tdl.vl_credito_adicional, 0.00)) AS dot_atu
                           , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                           , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                           , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                           , COALESCE((SELECT *
                                         FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                            , '''||stExercicio||'''
                                                                                            , '''||stEntidades||'''
                                                                                            , '''||stDtIniExercicio||'''
                                                                                            , '''||stDtFim||'''
                                                                                            , cdd.cod_recurso::VARCHAR
                                                                                            , ''12''
                                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                                            , cdd.cod_despesa::VARCHAR
                                                                                            , false )
                                     ), 0.00) AS vl_empenhado_ate_bimestre
                        FROM (
                                SELECT despesa.exercicio
                                      , despesa.cod_recurso
                                      , despesa.cod_despesa
                                      , despesa.cod_subfuncao
                                      , despesa.cod_funcao
                                      , conta_despesa.cod_estrutural
                                      , vinculo_recurso_acao.cod_tipo_educacao
                                      , vinculo_stn_recurso.cod_vinculo
                               
                                     FROM stn.vinculo_recurso_acao
                               
                               INNER JOIN ppa.acao
                                   ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                      AND acao.ativo = TRUE
                               
                               INNER JOIN orcamento.pao_ppa_acao
                                   ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                               
                               INNER JOIN orcamento.pao
                                   ON pao.num_pao = pao_ppa_acao.num_pao 
                                      AND pao.exercicio = pao_ppa_acao.exercicio 
                               
                               INNER JOIN orcamento.despesa
                                   ON despesa.num_pao = pao_ppa_acao.num_pao 
                                      AND despesa.exercicio = pao_ppa_acao.exercicio 
                               
                               INNER JOIN orcamento.conta_despesa
                                   ON despesa.exercicio = conta_despesa.exercicio
                                      AND despesa.cod_conta = conta_despesa.cod_conta
                                      
                          INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso
                               
                               INNER JOIN stn.vinculo_stn_recurso
                                   ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                               
                                   WHERE despesa.cod_funcao = 12
                                     AND despesa.exercicio = '''||stExercicio||'''
                                     AND despesa.cod_entidade IN ('||stEntidades||')       
                                     AND despesa.cod_subfuncao = 361 -- educação fundamental
                                     AND vinculo_recurso_acao.cod_tipo = 2 -- Outros Recursos
                                     AND vinculo_recurso_acao.cod_vinculo = 1 -- fundeb -- recurso 30
                             ) AS cdd
                            
                          -- suplementacoes e reduções de despesa
                   LEFT JOIN tmp_despesa_liberada AS tdl
                          ON tdl.exercicio   = cdd.exercicio
                         AND tdl.cod_despesa = cdd.cod_despesa
                         AND tdl.cod_recurso = cdd.cod_recurso
                          -- liquidacao total no exercicio 
                   LEFT JOIN tmp_despesa_liquidada_total AS tt
                          ON tt.exercicio   = cdd.exercicio
                         AND tt.cod_despesa = cdd.cod_despesa
                         AND tt.cod_recurso = cdd.cod_recurso
                   LEFT JOIN tmp_despesa_estornada_total AS test_tot
                          ON test_tot.exercicio = cdd.exercicio
                         AND test_tot.cod_despesa = cdd.cod_despesa
                         AND test_tot.cod_recurso = cdd.cod_recurso
                            -- educação infantil 
                  -- ensino fundamental , educação de jovens e adultos, educação especial
                       WHERE cdd.cod_subfuncao IN (361,366,367)
                    GROUP BY cdd.cod_recurso
                           , cdd.cod_estrutural
                           , cdd.cod_subfuncao
                           , cdd.cod_despesa
                     ) AS tb3_b
                   ) AS tb3b
        UNION ALL
          SELECT *
            FROM (
                  SELECT CAST(16 AS INTEGER) AS grupo
                       , CAST(0 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''RESTOS A PAGAR INSCRITOS NO EXERCÍCIO SEM DISPONIBILIDADE FINANCEIRA DE RECURSOS DO FUNDEB'' AS VARCHAR) AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) tb4
        UNION ALL 
          SELECT *
            FROM (
                  SELECT CAST(16 AS INTEGER) AS grupo
                       , vinculo_recurso.cod_tipo AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CASE WHEN vinculo_recurso.cod_tipo = 1
                              THEN ''FUNDEB 60%''
                              ELSE ''FUNDEB 40%''
                         END AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                       , SUM(
                             CASE WHEN vinculo_recurso.cod_tipo = 1 AND SUBSTRING(cod_estrutural, 1, 2) = ''31''
                                  THEN ( COALESCE(nota_liquidacao_item.vl_total,0.00) - (COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) )
                                  WHEN vinculo_recurso.cod_tipo = 2 AND SUBSTRING(cod_estrutural, 1, 2) <> ''31''
                                  THEN ( COALESCE(nota_liquidacao_item.vl_total,0.00) - (COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) )
                                  ELSE 0.00
                             END
                         ) AS vl_restos_inscritos

                    FROM empenho.nota_liquidacao_item
              INNER JOIN empenho.nota_liquidacao
                      ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                     AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
               LEFT JOIN (  SELECT exercicio
                                 , cod_nota
                                 , num_item
                                 , exercicio_item
                                 , cod_pre_empenho
                                 , cod_entidade
                                 , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                              FROM empenho.nota_liquidacao_item_anulado
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

              INNER JOIN ( SELECT pre_empenho_despesa.exercicio
                                , pre_empenho_despesa.cod_pre_empenho
                                , despesa.cod_recurso
                                , despesa.cod_funcao
                                , despesa.cod_subfuncao
                                , REPLACE(conta_despesa.cod_estrutural, ''.'', '''') AS cod_estrutural
                                , num_orgao
                                , num_unidade
                             FROM empenho.pre_empenho_despesa
                       INNER JOIN orcamento.despesa
                               ON despesa.exercicio   = pre_empenho_despesa.exercicio
                              AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                       INNER JOIN orcamento.conta_despesa
                               ON despesa.exercicio = conta_despesa.exercicio
                              AND despesa.cod_conta = conta_despesa.cod_conta
                            UNION
                           SELECT restos_pre_empenho.exercicio
                                , restos_pre_empenho.cod_pre_empenho
                                , restos_pre_empenho.recurso AS cod_recurso
                                , restos_pre_empenho.cod_funcao
                                , restos_pre_empenho.cod_subfuncao
                                , restos_pre_empenho.cod_estrutural
                                , num_orgao
                                , num_unidade
                             FROM empenho.restos_pre_empenho
                         ) AS despesa
                      ON despesa.exercicio       = nota_liquidacao_item.exercicio_item
                     AND despesa.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho

              INNER JOIN ( SELECT vinculo_recurso.exercicio
                                , vinculo_recurso.cod_tipo
                                , vinculo_recurso.cod_recurso
                                , vinculo_recurso.cod_entidade
                             FROM stn.vinculo_recurso
                            WHERE exercicio = '''||stExercicio||'''
                              AND vinculo_recurso.cod_vinculo = 1
                              AND vinculo_recurso.cod_tipo    = 2
                         GROUP BY vinculo_recurso.exercicio
                                , vinculo_recurso.cod_tipo
                                , vinculo_recurso.cod_recurso
                                , vinculo_recurso.cod_entidade
                         ) AS vinculo_recurso
                      ON vinculo_recurso.exercicio    = nota_liquidacao_item.exercicio
                     AND vinculo_recurso.cod_entidade = nota_liquidacao_item.cod_entidade
                     AND vinculo_recurso.cod_recurso  = despesa.cod_recurso

                   WHERE nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('''||stDtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||stDtFim||''',''dd/mm/yyyy'')
                     AND nota_liquidacao.exercicio_empenho < '''||stExercicio||'''
                     AND nota_liquidacao_item.exercicio = '''||stExercicio||'''
                     AND nota_liquidacao_item.cod_entidade IN ('||stEntidades||')

                GROUP BY vinculo_recurso.cod_tipo
                 ) tb4
        )';
          
        
    -- --------------------------------------------
    -- MDE
    -- Despesas com Ações Típicas de Manutenção
    -- e Desenvolvimento do Ensino
    -- --------------------------------------------
    
    
    ELSEIF (inOpcao = 2) THEN 
        
        stSQL := '
        CREATE TEMPORARY TABLE tmp_rreo_retorno AS (
        
        -- Educacao Infantil (cod_subfuncao = 365)
        
          SELECT *
            FROM (
              SELECT CAST(23 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''EDUCAÇÃO INFANTIL'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl
        UNION ALL

          SELECT *
            FROM (
              SELECT CAST(23 AS INTEGER) AS grupo
                   , CAST(1 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''Creche'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl
        UNION ALL

          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(1 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Recursos do FUNDEB'' AS VARCHAR) AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   UNION ALL
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(1 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Recursos do FUNDEB'' AS VARCHAR) AS descricao
                       , COALESCE(tdl.vl_original, 0.00) AS dot_ini
                       , (COALESCE(tdl.vl_original, 0.00) + COALESCE(tdl.vl_credito_adicional, 0.00)) AS dot_atu
                       , (COALESCE(tt.vl_liquidado, 0.00) - COALESCE(test_tot.vl_estornado, 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT *
                                     FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                        , '''||stExercicio||'''
                                                                                        , '''||stEntidades||'''
                                                                                        , '''||stDtIniExercicio||'''
                                                                                        , '''||stDtFim||'''
                                                                                        , cdd.cod_recurso::VARCHAR
                                                                                        , ''12''
                                                                                        , cdd.cod_subfuncao::VARCHAR
                                                                                        , cdd.cod_despesa::VARCHAR
                                                                                        , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
              
                    FROM (
                            SELECT despesa.exercicio
                                 , despesa.cod_recurso
                                 , despesa.cod_despesa
                                 , despesa.cod_subfuncao
                                 , despesa.cod_funcao
                                 , conta_despesa.cod_estrutural
                                 , vinculo_recurso_acao.cod_tipo_educacao
                                 , vinculo_stn_recurso.cod_vinculo
                          
                                FROM stn.vinculo_recurso_acao
                          
                          INNER JOIN ppa.acao
                              ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                 AND acao.ativo = TRUE
                          
                          INNER JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                          
                          INNER JOIN orcamento.pao
                              ON pao.num_pao = pao_ppa_acao.num_pao 
                                 AND pao.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.despesa
                              ON despesa.num_pao = pao_ppa_acao.num_pao 
                                 AND despesa.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.conta_despesa
                              ON despesa.exercicio = conta_despesa.exercicio
                                 AND despesa.cod_conta = conta_despesa.cod_conta
                                 
                          INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso
                          
                          INNER JOIN stn.vinculo_stn_recurso
                              ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                          
                              WHERE despesa.cod_funcao = 12
                                AND despesa.exercicio = '''||stExercicio||'''
                                AND despesa.cod_entidade IN ('||stEntidades||')
                                AND vinculo_recurso_acao.cod_tipo_educacao  = 1
                                AND vinculo_recurso_acao.cod_vinculo = 1 -- fundeb
                                --AND vinculo_recurso_acao.cod_tipo = 1 -- profissionais magistério
                                AND despesa.cod_subfuncao = 365
                                
                         ) AS cdd
                         
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                    -- Educacao Infantil
                   WHERE cdd.cod_subfuncao = 365
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                       , liq_tot
                       , dot_ini
                       , dot_atu
                ORDER BY grupo
                       , nivel
                     ) AS tbl23_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl23a
        UNION ALL
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(2 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Outros Recursos de Impostos'' AS VARCHAR) AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   UNION ALL
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(2 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Outros Recursos de Impostos'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                            SELECT despesa.exercicio
                                 , despesa.cod_recurso
                                 , despesa.cod_despesa
                                 , despesa.cod_subfuncao
                                 , despesa.cod_funcao
                                 , conta_despesa.cod_estrutural
                                 , vinculo_recurso_acao.cod_tipo_educacao
                                 , vinculo_stn_recurso.cod_vinculo
                          
                                FROM stn.vinculo_recurso_acao
                          
                          INNER JOIN ppa.acao
                              ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                 AND acao.ativo = TRUE
                          
                          INNER JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                          
                          INNER JOIN orcamento.pao
                              ON pao.num_pao = pao_ppa_acao.num_pao 
                                 AND pao.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.despesa
                              ON despesa.num_pao = pao_ppa_acao.num_pao 
                                 AND despesa.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.conta_despesa
                              ON despesa.exercicio = conta_despesa.exercicio
                                 AND despesa.cod_conta = conta_despesa.cod_conta
                                 
                          INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso
                          
                          INNER JOIN stn.vinculo_stn_recurso
                              ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                          
                              WHERE despesa.cod_funcao = 12
                                AND despesa.exercicio = '''||stExercicio||'''
                                AND despesa.cod_entidade IN ('||stEntidades||')
                                AND vinculo_recurso_acao.cod_tipo_educacao  = 1
                                AND vinculo_recurso_acao.cod_vinculo <> 1 -- NÃO fundeb
                                AND despesa.cod_subfuncao = 365

                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                    -- Educacao Infantil
                   WHERE cdd.cod_subfuncao = 365
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl23_b
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl23b
        UNION ALL

          SELECT *
            FROM (
              SELECT CAST(23 AS INTEGER) AS grupo
                   , CAST(2 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''Pré-Escola'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl
        UNION ALL
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(2 AS INTEGER) AS nivel
                       , CAST(1 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Recursos do FUNDEB'' AS VARCHAR) AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   UNION ALL
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(2 AS INTEGER) AS nivel
                       , CAST(1 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Recursos do FUNDEB'' AS VARCHAR) AS descricao
                       , COALESCE(tdl.vl_original, 0.00) AS dot_ini
                       , (COALESCE(tdl.vl_original, 0.00) + COALESCE(tdl.vl_credito_adicional, 0.00)) AS dot_atu
                       , (COALESCE(tt.vl_liquidado, 0.00) - COALESCE(test_tot.vl_estornado, 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT *
                                     FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                                        , '''||stExercicio||'''
                                                                                        , '''||stEntidades||'''
                                                                                        , '''||stDtIniExercicio||'''
                                                                                        , '''||stDtFim||'''
                                                                                        , cdd.cod_recurso::VARCHAR
                                                                                        , ''12''
                                                                                        , cdd.cod_subfuncao::VARCHAR
                                                                                        , cdd.cod_despesa::VARCHAR
                                                                                        , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
              
                    FROM (
                            SELECT despesa.exercicio
                                 , despesa.cod_recurso
                                 , despesa.cod_despesa
                                 , despesa.cod_subfuncao
                                 , despesa.cod_funcao
                                 , conta_despesa.cod_estrutural
                                 , vinculo_recurso_acao.cod_tipo_educacao
                                 , vinculo_stn_recurso.cod_vinculo
                          
                                FROM stn.vinculo_recurso_acao
                          
                          INNER JOIN ppa.acao
                              ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                 AND acao.ativo = TRUE
                          
                          INNER JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                          
                          INNER JOIN orcamento.pao
                              ON pao.num_pao = pao_ppa_acao.num_pao 
                                 AND pao.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.despesa
                              ON despesa.num_pao = pao_ppa_acao.num_pao 
                                 AND despesa.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.conta_despesa
                              ON despesa.exercicio = conta_despesa.exercicio
                                 AND despesa.cod_conta = conta_despesa.cod_conta
                                 
                          INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso
                          
                          INNER JOIN stn.vinculo_stn_recurso
                              ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                          
                              WHERE despesa.cod_funcao = 12
                                AND despesa.exercicio = '''||stExercicio||'''
                                AND despesa.cod_entidade IN ('||stEntidades||')
                                AND vinculo_recurso_acao.cod_tipo_educacao = 2 -- pre-escola
                                AND vinculo_recurso_acao.cod_vinculo = 1 -- fundeb
                                AND despesa.cod_subfuncao = 365 -- infantil
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                    -- Educacao Infantil
                   WHERE cdd.cod_subfuncao = 365
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                       , liq_tot
                       , dot_ini
                       , dot_atu
                ORDER BY grupo
                       , nivel
                     ) AS tbl23_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl23a
        UNION ALL
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(2 AS INTEGER) AS nivel
                       , CAST(2 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Outros Recursos de Impostos'' AS VARCHAR) AS descricao
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                       , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                       , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   UNION ALL
                  SELECT CAST(23 AS INTEGER) AS grupo
                       , CAST(2 AS INTEGER) AS nivel
                       , CAST(2 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Outros Recursos de Impostos'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                            SELECT despesa.exercicio
                                 , despesa.cod_recurso
                                 , despesa.cod_despesa
                                 , despesa.cod_subfuncao
                                 , despesa.cod_funcao
                                 , conta_despesa.cod_estrutural
                                 , vinculo_recurso_acao.cod_tipo_educacao
                                 , vinculo_stn_recurso.cod_vinculo
                          
                                FROM stn.vinculo_recurso_acao
                          
                          INNER JOIN ppa.acao
                              ON acao.cod_acao = vinculo_recurso_acao.cod_acao
                                 AND acao.ativo = TRUE
                          
                          INNER JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.cod_acao  = acao.cod_acao 
                          
                          INNER JOIN orcamento.pao
                              ON pao.num_pao = pao_ppa_acao.num_pao 
                                 AND pao.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.despesa
                              ON despesa.num_pao = pao_ppa_acao.num_pao 
                                 AND despesa.exercicio = pao_ppa_acao.exercicio 
                          
                          INNER JOIN orcamento.conta_despesa
                              ON despesa.exercicio = conta_despesa.exercicio
                                 AND despesa.cod_conta = conta_despesa.cod_conta
                                 
                       /*   INNER JOIN orcamento.recurso
                              ON recurso.exercicio = vinculo_recurso_acao.exercicio                             
                              AND recurso.cod_recurso = vinculo_recurso_acao.cod_recurso
                              AND recurso.cod_recurso = despesa.cod_recurso  */
                          
                          INNER JOIN stn.vinculo_stn_recurso
                              ON vinculo_stn_recurso.cod_vinculo  = vinculo_recurso_acao.cod_vinculo
                          
                              WHERE despesa.cod_funcao = 12
                                AND despesa.exercicio = '''||stExercicio||'''
                                AND despesa.cod_entidade IN ('||stEntidades||')
                                AND vinculo_recurso_acao.cod_tipo_educacao  = 2
                                --AND vinculo_recurso_acao.cod_vinculo <> 1 não funciona pq precisaria cadastrar todos recursos
                                AND despesa.cod_recurso <> 30
                                AND despesa.cod_subfuncao = 365
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                    -- Educacao Infantil
                   WHERE cdd.cod_subfuncao = 365
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl23_b
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl23b

        UNION ALL
        
          SELECT *
            FROM (
              SELECT CAST(24 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''ENSINO FUNDAMENTAL'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl4
        UNION ALL 
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(24 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Recursos do FUNDEB'' AS VARCHAR) AS descricao
                       , COALESCE(tdl.vl_original, 0.00) AS dot_ini
                       , (COALESCE(tdl.vl_original, 0.00) + COALESCE(tdl.vl_credito_adicional, 0.00)) AS dot_atu
                       , (COALESCE(tt.vl_liquidado, 0.00) - COALESCE(test_tot.vl_estornado, 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                          SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM stn.vinculo_recurso AS vr 
                      INNER JOIN orcamento.despesa AS d
                              ON d.exercicio = vr.exercicio
                             AND d.cod_entidade = vr.cod_entidade
                             AND d.cod_recurso = vr.cod_recurso
                             AND d.num_orgao = vr.num_orgao
                             AND d.num_unidade = vr.num_unidade 
                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta 
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND vr.cod_vinculo = 1
                             -- apenas Função Educação (12)
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- Ensino Fundamental, Educação de Jovens e Adultos, Educação Especial
                   WHERE cdd.cod_subfuncao IN (361)
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                       , dot_ini
                       , dot_atu
                       , liq_tot
                ORDER BY grupo
                       , nivel
                     ) AS tbl24_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl24a
        UNION ALL
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(24 AS INTEGER) AS grupo
                       , CAST(2 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas Custeadas com Outros Recursos de Impostos'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                          SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM orcamento.despesa AS d

                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta
                             
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND d.cod_recurso <> 30
                             AND d.cod_subfuncao = 361
                             -- apenas Função Educação (12)
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- Ensino Fundamental, Educação de Jovens e Adultos, Educação Especial
                   WHERE cdd.cod_subfuncao = 361
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl24_b
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl24b
        UNION ALL 
          SELECT *
            FROM (
              SELECT CAST(25 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''ENSINO MÉDIO'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tb25
        UNION ALL
        -- Despesas com Ensino Medio
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(25 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(''Despesas com Ensino Médio'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                            SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM orcamento.despesa AS d

                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta
                             
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND d.cod_subfuncao = 362
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- Ensino Medio
                   WHERE cdd.cod_subfuncao = 362
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl25_a
            GROUP BY grupo
                   , nivel
                   , descricao
                 ) AS tbl25a
        UNION ALL 
          SELECT *
            FROM (
              SELECT CAST(26 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''ENSINO SUPERIOR'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl26
        UNION ALL
        -- Despesas com Ensino Superior
           SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(26 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas com Ensino Superior'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                            SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM orcamento.despesa AS d

                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta
                             
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND d.cod_subfuncao = 364
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- Ensino Superior
                   WHERE cdd.cod_subfuncao = 364
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl26_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl26a       
        UNION ALL 
          SELECT *
            FROM (
              SELECT CAST(27 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''ENSINO PROFISSIONAL NÃO INTEGRADO AO ENSINO REGULAR'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl27
        UNION ALL
        -- Despesas com Ensino Profissional
           SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(27 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Despesas com Ensino Profissional'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                            SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM orcamento.despesa AS d

                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta
                             
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND d.cod_subfuncao = 363
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- Ensino Superior
                   WHERE cdd.cod_subfuncao = 363
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl27_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl27a  
        UNION ALL 
          SELECT *
            FROM (
              SELECT CAST(28 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''OUTRAS'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl13
        UNION ALL
        -- Outras Despesas - Exclui as subfunções 361, 362, 363, 364, 365, 366
           SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(28 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Outras Despesas'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                            SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM orcamento.despesa AS d

                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta
                             
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND d.cod_subfuncao NOT IN (361,362,363,364,365)
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- Outras Despesas - Exclui Subfunções: 361, 362, 363, 364, 365, 366.
                      -- Retirada também a subfunção 367
                   WHERE cdd.cod_subfuncao NOT IN (361,362,363,364,365)
                     AND cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl28_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl28a
    )';
    
    -- --------------------------------------------
    -- OUTRAS DESPESAS
    -- Outras Despesas com Recursos 
    -- Destinados a MDE
    -- --------------------------------------------    
    
    ELSEIF (inOpcao = 3) THEN

        stSQL := '        
        CREATE TEMPORARY TABLE tmp_rreo_retorno AS (        
          SELECT *
            FROM (
              SELECT CAST(40 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''DESPESAS CUSTEADAS COM A APLICAÇÃO FINANCEIRA DE OUTROS RECURSOS DE IMPOSTOS VINCULADOS AO ENSINO'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl1
        UNION ALL
          SELECT *
            FROM (
              SELECT CAST(41 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''DESPESAS CUSTEADAS COM A CONTRIBUIÇÃO SOCIAL DO SALÁRIO-EDUCAÇÃO'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl1
        UNION ALL
        -- Contribuição Social do Salário Educação com Recursos do MDE

          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(41 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Contribuição Social do Salário Educação'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                          SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM stn.vinculo_recurso AS vr 
                      INNER JOIN orcamento.despesa AS d
                              ON d.exercicio = vr.exercicio
                             AND d.cod_entidade = vr.cod_entidade
                             AND d.cod_recurso = vr.cod_recurso
                             AND d.num_orgao = vr.num_orgao
                             AND d.num_unidade = vr.num_unidade 
                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta 
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND vr.cod_vinculo = 3
                             AND vr.cod_tipo = 2
                             -- apenas Função Educação (12)
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                   WHERE cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl41_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl41a
        UNION ALL
        
        -- 33. RECURSOS DE OPERAÇÕES DE CRÉDITO
        
          SELECT *
            FROM (
              SELECT CAST(42 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''DESPESAS CUSTEADAS COM OPERAÇÕES DE CRÉDITO'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl3 
        UNION ALL 
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(42 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Recursos de Operações de Crédito'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                          SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM stn.vinculo_recurso AS vr 
                      INNER JOIN orcamento.despesa AS d
                              ON d.exercicio = vr.exercicio
                             AND d.cod_entidade = vr.cod_entidade
                             AND d.cod_recurso = vr.cod_recurso
                             AND d.num_orgao = vr.num_orgao
                             AND d.num_unidade = vr.num_unidade 
                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta 
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND vr.cod_vinculo = 4
                             AND vr.cod_tipo = 2
                             -- apenas Função Educação (12)
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                   WHERE cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl42_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl42a
        UNION ALL 
        
        -- 34. OUTROS RECURSOS DESTINADOS À EDUCAÇÃO
          SELECT *
            FROM (
              SELECT CAST(43 AS INTEGER) AS grupo
                   , CAST(0 AS INTEGER) AS nivel
                   , CAST(0 AS INTEGER) AS sub_nivel
                   , CAST(''DESPESAS CUSTEADAS COM OUTRAS RECEITAS PARA O FINANCIAMENTO DE ENSINO'' AS VARCHAR) AS descricao
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_ini
                   , CAST(0.00 AS NUMERIC(14,2)) AS dot_atu
                   , CAST(0.00 AS NUMERIC(14,2)) AS liq_tot
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                   , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre
                   , CAST(0.00 AS NUMERIC(14,2)) AS vl_restos_inscritos
                 ) AS tbl5
        UNION ALL 
          SELECT *
            FROM (
              SELECT grupo
                   , nivel
                   , sub_nivel
                   , descricao
                   , SUM(dot_ini) AS dot_ini
                   , SUM(dot_atu) AS dot_atu
                   , SUM(liq_tot) AS liq_tot
                   , SUM(pct_liquidado) AS pct_liquidado
                   , SUM(pct_empenhado) AS pct_empenhado
                   , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , SUM(vl_empenhado_ate_bimestre)-SUM(liq_tot) AS vl_restos_inscritos
                FROM (
                  SELECT CAST(43 AS INTEGER) AS grupo
                       , CAST(1 AS INTEGER) AS nivel
                       , CAST(0 AS INTEGER) AS sub_nivel
                       , CAST(''Outros Recursos Destinados a Educação'' AS VARCHAR) AS descricao
                       , COALESCE(SUM(tdl.vl_original), 0.00) AS dot_ini
                       , (COALESCE(SUM(tdl.vl_original), 0.00) + COALESCE(SUM(tdl.vl_credito_adicional), 0.00)) AS dot_atu
                       , (COALESCE(SUM(tt.vl_liquidado), 0.00) - COALESCE(SUM(test_tot.vl_estornado), 0.00)) AS liq_tot
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_liquidado
                       , CAST(0.00 AS NUMERIC(14,2)) AS pct_empenhado
                       , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_funcao_subfuncao( publico.fn_mascarareduzida(cdd.cod_estrutural)
                                                                            , '''||stExercicio||'''
                                                                            , '''||stEntidades||'''
                                                                            , '''||stDtIniExercicio||'''
                                                                            , '''||stDtFim||'''
                                                                            , cdd.cod_recurso::VARCHAR
                                                                            , ''12''
                                                                            , cdd.cod_subfuncao::VARCHAR
                                                                            , cdd.cod_despesa::VARCHAR
                                                                            , false )
                         ), 0.00) AS vl_empenhado_ate_bimestre
                    FROM (
                          SELECT d.exercicio
                               , d.cod_recurso
                               , d.cod_despesa
                               , d.cod_subfuncao
                               , d.cod_funcao
                               , cd.cod_estrutural 
                            FROM stn.vinculo_recurso AS vr 
                      INNER JOIN orcamento.despesa AS d
                              ON d.exercicio = vr.exercicio
                             AND d.cod_entidade = vr.cod_entidade
                             AND d.cod_recurso = vr.cod_recurso
                             AND d.num_orgao = vr.num_orgao
                             AND d.num_unidade = vr.num_unidade 
                      INNER JOIN orcamento.conta_despesa AS cd
                              ON cd.exercicio = d.exercicio
                             AND cd.cod_conta = d.cod_conta 
                           WHERE d.exercicio = '''||stExercicio||'''
                             AND d.cod_entidade IN ('||stEntidades||')
                             AND vr.cod_vinculo = 5
                             AND vr.cod_tipo = 2
                             -- apenas Função Educação (12)
                             AND d.cod_funcao = 12
                         ) AS cdd
                      -- suplementacoes e reduções de despesa
               LEFT JOIN tmp_despesa_liberada AS tdl
                      ON tdl.exercicio   = cdd.exercicio
                     AND tdl.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                      -- liquidacao total no exercicio 
               LEFT JOIN tmp_despesa_liquidada_total AS tt
                      ON tt.exercicio    = cdd.exercicio
                     AND tt.cod_despesa  = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
               LEFT JOIN tmp_despesa_estornada_total AS test_tot
                      ON test_tot.exercicio = cdd.exercicio
                     AND test_tot.cod_despesa = cdd.cod_despesa
                     AND tdl.cod_recurso = cdd.cod_recurso
                   WHERE cdd.exercicio = '''||stExercicio||'''
                GROUP BY cdd.cod_recurso
                       , cdd.cod_estrutural
                       , cdd.cod_subfuncao
                       , cdd.cod_despesa
                ORDER BY grupo
                       , nivel
                     ) AS tbl43_a
            GROUP BY grupo
                   , nivel
                   , sub_nivel
                   , descricao
                 ) AS tbl43a       
        )';
    
    END IF;

    EXECUTE stSQL;
    
    -- Calcular totais do nivel pai
    
    stSQL := 'SELECT DISTINCT grupo FROM tmp_rreo_retorno ';
    
    FOR reReg IN EXECUTE stSQL
    LOOP
        
        stSQLaux := '
        UPDATE tmp_rreo_retorno SET
            dot_ini                     = (SELECT COALESCE(SUM(dot_ini), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            dot_atu                     = (SELECT COALESCE(SUM(dot_atu), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            liq_tot                     = (SELECT COALESCE(SUM(liq_tot), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            vl_empenhado_ate_bimestre   = (SELECT COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) FROM tmp_rreo_retorno  WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            vl_restos_inscritos         = (SELECT COALESCE(SUM(vl_restos_inscritos), 0.00) FROM tmp_rreo_retorno        WHERE grupo = ' || reReg.grupo || ' AND nivel > 0),
            pct_liquidado               = (SELECT COALESCE(SUM((liq_tot / dot_atu) * 100), 0.00) FROM tmp_rreo_retorno  WHERE grupo = ' || reReg.grupo || ' AND nivel > 0 and liq_tot > 0 AND dot_atu > 0),
            pct_empenhado               = (SELECT COALESCE(SUM((vl_empenhado_ate_bimestre / dot_atu) * 100), 0.00) FROM tmp_rreo_retorno WHERE grupo = ' || reReg.grupo || ' AND nivel > 0 AND dot_atu > 0 AND vl_empenhado_ate_bimestre > 0)
            
        WHERE
            grupo = ' || reReg.grupo || ' AND nivel = 0 ';
            
        EXECUTE stSQLaux;
        
        stSQLaux2 := 'SELECT DISTINCT nivel FROM tmp_rreo_retorno WHERE grupo = ' || reReg.grupo || ' ';
        
        FOR reRegNivel IN EXECUTE stSQLaux2
        LOOP

            stSQLaux := '
            UPDATE tmp_rreo_retorno SET
            dot_ini                     = (SELECT COALESCE(SUM(dot_ini), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || '),
            dot_atu                     = (SELECT COALESCE(SUM(dot_atu), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || '),
            liq_tot                     = (SELECT COALESCE(SUM(liq_tot), 0.00) FROM tmp_rreo_retorno                    WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || '),
            vl_empenhado_ate_bimestre   = (SELECT COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) FROM tmp_rreo_retorno  WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || '),
            vl_restos_inscritos         = (SELECT COALESCE(SUM(vl_restos_inscritos), 0.00) FROM tmp_rreo_retorno        WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || '),
            pct_liquidado               = (SELECT COALESCE(SUM((liq_tot / dot_atu) * 100), 0.00) FROM tmp_rreo_retorno  WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || ' and liq_tot > 0 AND dot_atu > 0),
            pct_empenhado               = (SELECT COALESCE(SUM((vl_empenhado_ate_bimestre / dot_atu) * 100), 0.00) FROM tmp_rreo_retorno WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || ' AND dot_atu > 0 AND vl_empenhado_ate_bimestre > 0)
            WHERE grupo = ' || reReg.grupo || ' AND nivel = ' || reRegNivel.nivel || ' AND sub_nivel = 0 ';

            EXECUTE stSQLaux;

        END LOOP;
        
    END LOOP;
    
    
    -- Calcular porcentagens
    
    -- stSQL := '  UPDATE tmp_rreo_retorno SET 
    --             pct_liquidado = CAST(((liq_tot / dot_atu) * 100) AS NUMERIC(14,2))                
    --             WHERE liq_tot > 0 AND dot_atu > 0;

    --             UPDATE tmp_rreo_retorno SET                 
    --             pct_empenhado = CAST(((vl_empenhado_ate_bimestre / dot_atu) * 100) AS NUMERIC(14,2)) 
    --             WHERE dot_atu > 0 AND vl_empenhado_ate_bimestre > 0;
    --         ';

    -- EXECUTE stSQL;
    
    -- Seleção de Retorno
    
    -- --------------------------------------
    -- Select de Retorno
    -- --------------------------------------
    
    stSQL := '  SELECT  CAST(grupo AS INTEGER) AS grupo, 
                        CAST(nivel AS INTEGER) AS nivel,
                        CAST(sub_nivel AS INTEGER) AS sub_nivel,
                        CAST(descricao AS VARCHAR) AS descricao, 
                        CAST(dot_ini AS NUMERIC(14,2)) AS dot_ini, 
                        CAST(dot_atu AS NUMERIC(14,2)) AS dot_atu,                         
                        CAST(liq_tot AS NUMERIC(14,2)) AS liq_tot, 
                        CAST(pct_liquidado AS NUMERIC(14,2)) AS pct_liquidado,
                        CAST(pct_empenhado AS NUMERIC(14,2)) AS pct_empenhado,                        
                        CAST(vl_empenhado_ate_bimestre AS NUMERIC(14,2)) AS vl_empenhado_ate_bimestre,
                        CAST(vl_restos_inscritos AS NUMERIC(14,2)) AS vl_restos_inscritos
                FROM tmp_rreo_retorno 
                ORDER BY grupo, nivel, sub_nivel, descricao';
    
    FOR reReg IN EXECUTE stSQL
    LOOP    
        RETURN NEXT reReg;  
    END LOOP;

    DROP TABLE tmp_despesa_liberada;
    DROP TABLE tmp_despesa_liquidada_total;
    DROP TABLE tmp_despesa_estornada_total;
    DROP TABLE tmp_rreo_retorno;
    
    RETURN;

END;
$$ LANGUAGE 'plpgsql';

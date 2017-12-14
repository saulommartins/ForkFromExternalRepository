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
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 16
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29316 $
* $Name$
* $Author: lbbarreiro $
* $Date: 2008-04-17 18:13:29 -0300 (Qui, 17 Abr 2008) $
*
* Casos de uso: uc-04.05.28
*/


CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo12_despesas_subfuncao( varchar, varchar, varchar, varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    stDataInicial  	ALIAS FOR $2;
    stDataFinal     ALIAS FOR $3;
    stCodEntidades 	ALIAS FOR $4;

    dtInicial  		varchar := '';
    dtFinal    		varchar := '';
    dtIniExercicio 	VARCHAR := '';
    stExercicioAnterior VARCHAR := '';

    reRegistro 		record ;
    stSql 			varchar := '';

BEGIN

    dtInicial := stDataInicial;
    dtFinal   := stDataFinal;
    
    dtIniExercicio := '01/01/'||stExercicio;
    stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));

    -- --------------------
	-- TABELAS TEMPORARIAS
    -- --------------------

    --
    -- Tabela com os valores a serem retornados
    --
    CREATE TEMPORARY TABLE tmp_retorno (
        grupo INTEGER,
        subgrupo INTEGER,
        descricao VARCHAR,
        dotacao_inicial NUMERIC(14,2) DEFAULT 0.00,
        dotacao_atualizada NUMERIC(14,2) DEFAULT 0.00,
        despesa_empenhada NUMERIC(14,2) DEFAULT 0.00,
        porcentagem_empenhada NUMERIC(14,2) DEFAULT 0.00,
        despesa_liquidada NUMERIC(14,2) DEFAULT 0.00,
        porcentagem NUMERIC(14,2) DEFAULT 0.00,
        restos_nao_processados NUMERIC(14,2) DEFAULT 0.00
    );        
	
    

stSql := 'CREATE TEMPORARY TABLE tmp_balancete_despesa AS (
                                SELECT 
                                            retorno.exercicio       ,                                                                                
                                            retorno.cod_despesa  ,                                                                                
                                            retorno.cod_entidade   ,                                                                                
                                            retorno.cod_programa    ,                                                                                
                                            retorno.cod_conta       ,                                                                                
                                            retorno.num_pao        ,                                                                                
                                            retorno.num_orgao      ,                                                                                
                                            retorno.num_unidade    ,                                                                                
                                            retorno.cod_recurso    ,                                                                                
                                            retorno.cod_funcao      ,                                                                                
                                            retorno.cod_subfuncao   ,                                                                                
                                            retorno.tipo_conta      ,                                                                                
                                            retorno.vl_original     ,                                                                                
                                            retorno.dt_criacao      ,                                                                                   
                                            retorno.classificacao   ,                                                                                
                                            retorno.descricao       ,                                                                                
                                            retorno.num_recurso    ,                                                                                
                                            retorno.nom_recurso    ,                                                                                
                                            retorno.nom_orgao       ,                                                                                
                                            retorno.nom_unidade     ,                                                                                
                                            retorno.nom_funcao      ,                                                                                
                                            retorno.nom_subfuncao   ,                                                                                
                                            retorno.nom_programa   ,                                                                                
                                            retorno.nom_pao        ,                                                                                
                                            retorno.empenhado_ano   ,                                                                                
                                            retorno.empenhado_per   ,                                                                                 
                                            retorno.anulado_ano    ,                                                                                
                                            retorno.anulado_per     ,                                                                                
                                            retorno.pago_ano        ,                                                                                
                                            retorno.pago_per        ,                                                                                 
                                            retorno.liquidado_ano   ,                                                                                
                                            retorno.liquidado_per   ,                                                                                
                                            retorno.saldo_inicial   ,                                                                                
                                            retorno.suplementacoes  ,                                                                                
                                            retorno.reducoes        ,                                                                                
                                            retorno.total_creditos  ,                                                                                
                                            retorno.credito_suplementar ,                                                                            
                                            retorno.credito_especial  ,                                                                            
                                            retorno.credito_extraordinario  ,                                                                            
                                            retorno.num_programa ,                                                                            
                                            retorno.num_acao             
                                   FROM orcamento.fn_balancete_despesa('|| quote_literal(stExercicio) ||',
                                                                       ''AND od.cod_entidade IN  ( '||stCodEntidades||') AND od.cod_funcao = 10 '',
                                                                       '|| quote_literal(dtInicial) ||',
                                                                       '|| quote_literal(dtFinal) || ',
                                                                       '''',
                                                                       '''',
                                                                       '''',
                                                                       '''',
                                                                       '''',
                                                                       '''',
                                                                       '''',
                                                                       ''''
                                                                          ) AS retorno (
                                                                         exercicio              CHAR(4), 
                                                                         cod_despesa            INTEGER, 
                                                                         cod_entidade           INTEGER,                                                                                
                                                                         cod_programa           INTEGER, 
                                                                         cod_conta              INTEGER, 
                                                                         num_pao                INTEGER,                                                                                
                                                                         num_orgao              INTEGER, 
                                                                         num_unidade            INTEGER, 
                                                                         cod_recurso            INTEGER,                                                                                
                                                                         cod_funcao             INTEGER, 
                                                                         cod_subfuncao          INTEGER, 
                                                                         tipo_conta             VARCHAR,
                                                                         vl_original            NUMERIC, 
                                                                         dt_criacao             DATE,    
                                                                         classificacao          VARCHAR,                                                                                
                                                                         descricao              VARCHAR, 
                                                                         num_recurso            VARCHAR, 
                                                                         nom_recurso            VARCHAR,                                                                                
                                                                         nom_orgao              VARCHAR, 
                                                                         nom_unidade            VARCHAR, 
                                                                         nom_funcao             VARCHAR,                                                                                
                                                                         nom_subfuncao          VARCHAR, 
                                                                         nom_programa           VARCHAR, 
                                                                         nom_pao                VARCHAR,
                                                                         empenhado_ano          NUMERIC, 
                                                                         empenhado_per          NUMERIC, 
                                                                         anulado_ano            NUMERIC,                                                                                
                                                                         anulado_per            NUMERIC, 
                                                                         pago_ano               NUMERIC, 
                                                                         pago_per               NUMERIC,                                                                                 
                                                                         liquidado_ano          NUMERIC, 
                                                                         liquidado_per          NUMERIC, 
                                                                         saldo_inicial          NUMERIC,                                                                                
                                                                         suplementacoes         NUMERIC, 
                                                                         reducoes               NUMERIC, 
                                                                         total_creditos         NUMERIC,                                                                                
                                                                         credito_suplementar    NUMERIC, 
                                                                         credito_especial       NUMERIC, 
                                                                         credito_extraordinario NUMERIC,
                                                                         num_programa           VARCHAR, 
                                                                         num_acao               VARCHAR
        ))';
        
        EXECUTE stSql;
    
    
    --
	-- Saldo Inicial e atualizado das Despesas
    --
    stSql := '
        CREATE TEMPORARY TABLE tmp_despesa AS
            SELECT conta_despesa.cod_estrutural 
                  , despesa.cod_subfuncao 
                 , despesa.exercicio
                 , SUM(vl_original) AS vl_original
                 , (SUM(COALESCE(suplementacao.valor,0)) - SUM(COALESCE(reducao.valor,0))) AS vl_suplementacoes
                 , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada_anexo_12( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), ''' || stExercicio || ''', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00) AS despesa_empenhada  
              FROM orcamento.despesa
        INNER JOIN orcamento.conta_despesa
                ON conta_despesa.cod_conta = despesa.cod_conta
               AND conta_despesa.exercicio = despesa.exercicio
         LEFT JOIN (    SELECT suplementacao_suplementada.exercicio
                             , suplementacao_suplementada.cod_despesa
                             , SUM(COALESCE(suplementacao_suplementada.valor,0)) AS valor
                          FROM orcamento.suplementacao_suplementada
                    INNER JOIN orcamento.suplementacao
                            ON suplementacao.exercicio = suplementacao_suplementada.exercicio
                           AND suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                         WHERE suplementacao.dt_suplementacao::date BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                      GROUP BY suplementacao_suplementada.cod_despesa
                             , suplementacao_suplementada.exercicio
                   ) AS suplementacao
                ON suplementacao.exercicio = despesa.exercicio
               AND suplementacao.cod_despesa = despesa.cod_despesa
         LEFT JOIN (    SELECT suplementacao_reducao.exercicio
                             , suplementacao_reducao.cod_despesa
                             , SUM(COALESCE(suplementacao_reducao.valor,0)) AS valor
                          FROM orcamento.suplementacao_reducao
                    INNER JOIN orcamento.suplementacao
                            ON suplementacao.exercicio = suplementacao_reducao.exercicio
                           AND suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                         WHERE suplementacao.dt_suplementacao::date BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                      GROUP BY suplementacao_reducao.cod_despesa
                             , suplementacao_reducao.exercicio
                   ) AS reducao
                ON reducao.exercicio = despesa.exercicio
               AND reducao.cod_despesa = despesa.cod_despesa
             WHERE (    conta_despesa.cod_estrutural LIKE ''3.1%''
                     OR conta_despesa.cod_estrutural LIKE ''3.2%''
                     OR conta_despesa.cod_estrutural LIKE ''3.3%''
                     OR conta_despesa.cod_estrutural LIKE ''4.4%''
                     OR conta_despesa.cod_estrutural LIKE ''4.5%''
                     OR conta_despesa.cod_estrutural LIKE ''4.6%''
                   )
               AND conta_despesa.exercicio = '''||stExercicio||'''
               AND despesa.cod_funcao = 10
               AND despesa.cod_entidade IN ( '||stCodEntidades||' )
         GROUP BY despesa.cod_subfuncao
                , despesa.exercicio
                , conta_despesa.cod_estrutural 
    ';
    
    EXECUTE stSql;

    --
    -- Valores liquidados para as despesas
    --
    stSql := '
    CREATE TEMPORARY TABLE tmp_despesa_liquidada AS
        SELECT nota_liquidacao.vl_total
             , despesa.cod_subfuncao
          FROM ( SELECT nota_liquidacao.exercicio_empenho
                      , nota_liquidacao.cod_empenho
                      , nota_liquidacao.cod_entidade
                      , SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)) AS vl_total
                   FROM empenho.nota_liquidacao
             INNER JOIN ( SELECT exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                               , SUM(vl_total) AS vl_total
                            FROM empenho.nota_liquidacao_item
                        GROUP BY exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                        ) AS nota_liquidacao_item
                     ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                    AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                    AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
              LEFT JOIN ( SELECT exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                               , SUM(vl_anulado) AS vl_anulado
                            FROM empenho.nota_liquidacao_item_anulado
                           WHERE timestamp::date BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                        GROUP BY exercicio_item
                               , cod_pre_empenho
                               , num_item
                               , exercicio
                               , cod_entidade
                               , cod_nota
                        ) AS nota_liquidacao_item_anulado
                     ON nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                    AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                    AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                    AND nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                    AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                    AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                  WHERE nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
               GROUP BY nota_liquidacao.exercicio_empenho
                      , nota_liquidacao.cod_empenho
                      , nota_liquidacao.cod_entidade
               ) AS nota_liquidacao
    INNER JOIN empenho.empenho
            ON empenho.exercicio = nota_liquidacao.exercicio_empenho
           AND empenho.cod_empenho = nota_liquidacao.cod_empenho
           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
    INNER JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.exercicio = empenho.exercicio
           AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
    INNER JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
    INNER JOIN orcamento.despesa
            ON despesa.exercicio = pre_empenho_despesa.exercicio
           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         WHERE despesa.cod_funcao = 10
           AND despesa.cod_entidade IN ('||stCodEntidades||')
           AND nota_liquidacao.cod_entidade IN ('||stCodEntidades||')
           AND empenho.dt_empenho BETWEEN TO_DATE('''||dtIniExercicio||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
           AND (    conta_despesa.cod_estrutural LIKE ''3.1%''
                 OR conta_despesa.cod_estrutural LIKE ''3.2%''
                 OR conta_despesa.cod_estrutural LIKE ''3.3%''
                 OR conta_despesa.cod_estrutural LIKE ''4.4%''
                 OR conta_despesa.cod_estrutural LIKE ''4.5%''
                 OR conta_despesa.cod_estrutural LIKE ''4.6%''
               )
    ';

    EXECUTE stSql;
    
    -- cria a tabela temporaria para o valor nao processado no exercicio anterior
    stSql := '
      CREATE TEMPORARY TABLE tmp_nao_processados_exercicio_anterior AS
  
        SELECT empenhado.cod_empenho
             , empenhado.cod_entidade
             , (SUM(empenhado.vl_empenhado) - SUM(liquidado.vl_liquidado)) AS vl_total
             , despesa.cod_subfuncao
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
                             WHERE empenho_anulado_item.timestamp::date BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'|| stExercicioAnterior||''',''dd/mm/yyyy'')
                          GROUP BY empenho_anulado_item.exercicio
                                 , empenho_anulado_item.cod_pre_empenho
                                 , empenho_anulado_item.num_item
                         ) AS empenho_anulado_item
                      ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                     AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                     AND empenho_anulado_item.num_item = item_pre_empenho.num_item
              
                   WHERE empenho.exercicio = '''|| stExercicioAnterior ||'''
                     AND empenho.dt_empenho BETWEEN TO_DATE(''01/01/'|| stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'|| stExercicioAnterior||''',''dd/mm/yyyy'')
                     AND empenho.cod_entidade IN ('|| stCodEntidades ||')
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
                                         WHERE timestamp::date BETWEEN TO_DATE(''01/01/'||stExercicioAnterior||''',''dd/mm/yyyy'') AND TO_DATE(''31/12/'||stExercicioAnterior||''',''dd/mm/yyyy'')
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
                       AND empenho.cod_entidade IN ('||stCodEntidades||')
                  GROUP BY pre_empenho.exercicio
                         , pre_empenho.cod_pre_empenho
                         , empenho.cod_entidade
                         , empenho.cod_empenho
  
                 ) AS liquidado 
              ON liquidado.exercicio = empenhado.exercicio
             AND liquidado.cod_pre_empenho = empenhado.cod_pre_empenho
             AND liquidado.cod_entidade = empenhado.cod_entidade
  
  --left para achar o cod_estrutural
       LEFT JOIN empenho.pre_empenho_despesa
              ON pre_empenho_despesa.exercicio = empenhado.exercicio
             AND pre_empenho_despesa.cod_pre_empenho = empenhado.cod_pre_empenho
        
       LEFT JOIN orcamento.despesa
              ON despesa.exercicio = pre_empenho_despesa.exercicio
             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             
      LEFT JOIN empenho.restos_pre_empenho
              ON restos_pre_empenho.exercicio = empenhado.exercicio
             AND restos_pre_empenho.cod_pre_empenho = empenhado.cod_pre_empenho

       WHERE restos_pre_empenho.cod_estrutural LIKE ''31%''
              OR restos_pre_empenho.cod_estrutural LIKE ''32%''
              OR restos_pre_empenho.cod_estrutural LIKE ''33%''
              OR restos_pre_empenho.cod_estrutural LIKE ''44%''
              OR restos_pre_empenho.cod_estrutural LIKE ''45%''
              OR restos_pre_empenho.cod_estrutural LIKE ''46%''
  
       GROUP BY empenhado.cod_empenho
              , empenhado.cod_entidade
              , despesa.num_orgao                
              , restos_pre_empenho.num_orgao
              ,despesa.cod_subfuncao
          HAVING (SUM(COALESCE(empenhado.vl_empenhado,0.00)) - SUM(COALESCE(liquidado.vl_liquidado,0.00)) ) > 0
             AND ( despesa.num_orgao = 08 OR restos_pre_empenho.num_orgao = 10 ) 
    ';
    
    EXECUTE stSql;
    
    -- --------------------------------------
    -- Povoa a tabela temporaria tmp_retorno
    -- --------------------------------------
    INSERT INTO tmp_retorno VALUES (   1
                                     , 1
                                     , 'Atenção Básica'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 301 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 301 )
                                     , ( SELECT SUM(empenhado_per) - SUM(anulado_per) FROM tmp_balancete_despesa WHERE cod_subfuncao = 301 GROUP BY cod_subfuncao )
                                     , 0
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 301 )
                                     , 0
                                     , CASE WHEN (SELECT SUM(COALESCE(vl_total,0)) FROM tmp_nao_processados_exercicio_anterior WHERE cod_subfuncao = 301) IS NULL THEN 0.00 END 
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   2
                                     , 1
                                     , 'Assistência Hospitalar e Ambulatorial'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 302 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 302 )
                                     , ( SELECT SUM(empenhado_per) - SUM(anulado_per) FROM tmp_balancete_despesa WHERE cod_subfuncao = 302 GROUP BY cod_subfuncao )
                                     , 0
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 302 )
                                     , 0
                                     , CASE WHEN (SELECT SUM(COALESCE(vl_total,0)) FROM tmp_nao_processados_exercicio_anterior WHERE cod_subfuncao = 302) IS NULL THEN 0.00 END 
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   3
                                     , 1
                                     , 'Suporte Profiático e Terapêutico'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 303 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 303 )
                                     , ( SELECT SUM(empenhado_per) - SUM(anulado_per) FROM tmp_balancete_despesa WHERE cod_subfuncao = 303 GROUP BY cod_subfuncao )
                                     , 0
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 303 )
                                     , 0
                                     , CASE WHEN (SELECT SUM (COALESCE(vl_total,0)) FROM tmp_nao_processados_exercicio_anterior WHERE cod_subfuncao = 303) IS NULL THEN 0.00 END 
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   4
                                     , 1
                                     , 'Vigilância Sanitária'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 304 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 304 )
                                     , ( SELECT SUM(empenhado_per) - SUM(anulado_per) FROM tmp_balancete_despesa WHERE cod_subfuncao = 304 GROUP BY cod_subfuncao )
                                     , 0
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 304 )
                                     , 0
                                     , CASE WHEN (SELECT SUM (COALESCE(vl_total,0)) FROM tmp_nao_processados_exercicio_anterior WHERE cod_subfuncao = 304) IS NULL THEN 0.00 END 
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   5
                                     , 1
                                     , 'Vigilância Epidemiológica'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 305 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 305 )
                                     , ( SELECT SUM(empenhado_per) - SUM(anulado_per) FROM tmp_balancete_despesa WHERE cod_subfuncao = 305 GROUP BY cod_subfuncao )
                                     , 0
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 305 )
                                     , 0
                                     , CASE WHEN (SELECT SUM (COALESCE(vl_total,0)) FROM tmp_nao_processados_exercicio_anterior WHERE cod_subfuncao = 305) IS NULL THEN 0.00 END 
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   6
                                     , 1
                                     , 'Alimentação e Nutrição'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao = 306 )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao = 306 )
                                     , ( SELECT SUM(empenhado_per) - SUM(anulado_per) FROM tmp_balancete_despesa WHERE cod_subfuncao = 306 GROUP BY cod_subfuncao )
                                     , 0
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao = 306 )
                                     , 0
                                     , CASE WHEN (SELECT SUM (COALESCE(vl_total,0)) FROM tmp_nao_processados_exercicio_anterior WHERE cod_subfuncao = 306) IS NULL THEN 0.00 END 
                                   ) ;

    INSERT INTO tmp_retorno VALUES (   7
                                     , 1
                                     , 'Outras Subfunções'
                                     , ( SELECT SUM(COALESCE(vl_original,0)) FROM tmp_despesa WHERE cod_subfuncao NOT IN (301,302,303,304,305,306) )
                                     , ( SELECT SUM(COALESCE(vl_original,0)) + SUM(COALESCE(vl_suplementacoes,0)) FROM tmp_despesa WHERE cod_subfuncao NOT IN (301,302,303,304,305,306) )
                                     , ( SELECT SUM(total) FROM (SELECT SUM(empenhado_per) - SUM(anulado_per) AS total FROM tmp_balancete_despesa WHERE cod_subfuncao NOT IN (301,302,303,304,305,306 ) GROUP BY cod_subfuncao) AS t99 )
                                     , 0
                                     , ( SELECT SUM(COALESCE(vl_total,0)) FROM tmp_despesa_liquidada WHERE cod_subfuncao NOT IN (301,302,303,304,305,306) )
                                     , 0
                                     , CASE WHEN (SELECT SUM (COALESCE(vl_total,0)) FROM tmp_nao_processados_exercicio_anterior WHERE cod_subfuncao NOT IN (301,302,303,304,305,306)) IS NULL THEN 0.00 END 
                                   ) ;
                                
    UPDATE tmp_retorno SET porcentagem = CASE WHEN despesa_liquidada > 0 THEN ROUND((despesa_liquidada/( SELECT SUM(despesa_liquidada) FROM tmp_retorno ))*100,2) ELSE 0.00 END;
    UPDATE tmp_retorno SET porcentagem_empenhada = CASE WHEN despesa_empenhada > 0 THEN ROUND((despesa_empenhada/( SELECT SUM(despesa_empenhada) FROM tmp_retorno ))*100,2) ELSE 0.00 END;

    stSql := '
        SELECT grupo
             , subgrupo
             , descricao 
             , COALESCE(dotacao_inicial,0) AS dotacao_inicial
             , COALESCE(dotacao_atualizada,0) AS dotacao_atualizada
             , COALESCE(despesa_empenhada,0) AS despesa_empenhada
             , COALESCE(porcentagem_empenhada,0) AS porcentagem_empenhada
             , COALESCE(despesa_liquidada,0) AS despesa_liquidada
             , COALESCE(porcentagem,0) AS porcentagem
             , restos_nao_processados
          FROM tmp_retorno
      ORDER BY grupo, subgrupo
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    --
    -- Remove as tabelas temporarias utilizadas
    -- 
    DROP TABLE tmp_retorno;
    DROP TABLE tmp_despesa;
    DROP TABLE tmp_balancete_despesa;
    DROP TABLE tmp_despesa_liquidada;
    DROP TABLE tmp_nao_processados_exercicio_anterior;

    RETURN;
 
END;

$$ language 'plpgsql';

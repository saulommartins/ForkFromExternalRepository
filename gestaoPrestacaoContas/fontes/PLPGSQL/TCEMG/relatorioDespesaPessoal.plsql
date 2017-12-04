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

    * Script de função PLPGSQL - Relatório de Demonstrativo de Gastos com Pessoal
    * Data de Criação: 10/07/2014

    * @author Carolina Schwaab Marçal

    * $Id: $

*/

CREATE OR REPLACE FUNCTION tcemg.fn_relatorio_despesa_pessoal ( VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, BOOLEAN) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    dtInicial       ALIAS FOR $2;
    dtFinal         ALIAS FOR $3;
    stCodEntidades 	ALIAS FOR $4;
    stSituacao      ALIAS FOR $5;
    stRestos        ALIAS FOR $6;
    
    reRegistro      record;
    reReg           record;
    stSql           varchar   := '';
    stSqlAux        varchar   := '';
    stValor         varchar   := '';
    arEntidade      varchar[] := array[0];
    inCount         INTEGER   := 1;
    inCountUpdate   INTEGER   := 1;
    valorArr        INTEGER   := 0;
    
    inAno       INTEGER;
    inMes       INTEGER;
    inExercicio INTEGER;
    i           INTEGER;
    
    nuVlMesalDespesa      NUMERIC := 0.00;
    nuVlTotalMesalDespesaCamara     NUMERIC := 0.00;
    nuVlTotalMesalDespesaPrefeitura NUMERIC := 0.00;
    nuVlTotalMesalDespesaInstituto  NUMERIC := 0.00;
    
    stMes       VARCHAR;
    arDatas     VARCHAR[];
BEGIN

   
    IF (dtFinal = '31/12/'||stExercicio) THEN
         SELECT valor INTO stValor
                     FROM administracao.configuracao 
                            WHERE parametro ilike '%virada%'
                            AND exercicio =  stExercicio   ;
    END IF;

    arEntidade := string_to_array(stCodEntidades, ',');
    valorArr := array_upper(arEntidade ,1);
       
      -- CRIA A TABELA TEMPORÁRIA DO BALANCETE DE DESPESA PARA TRAZER TODOS OS DADOS
    
    stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS (
    
                SELECT ( select publico.fn_nivel(classificacao)) AS nivel  
                          , cod_entidade 
                          , classificacao as cod_estrutural
                          , descricao
                          , exercicio
                          , SUM(empenhado_per) as empenhado
                          , SUM(liquidado_per)   as liquidado
                          , SUM(pago_per) as pago
                   FROM orcamento.fn_balancete_despesa('|| quote_literal(stExercicio) ||',
                                                             ''AND od.cod_entidade IN  (1,2,3)'',
                                                             '|| quote_literal(dtInicial) ||',
                                                             '|| quote_literal(dtFinal) || ',
                                                             '''','''','''','''','''','''','''',''''
                                                            ) AS retorno(
                                                                          exercicio              CHAR(4) 
                                                                        , cod_despesa            INTEGER 
                                                                        , cod_entidade           INTEGER                                                                                
                                                                        , cod_programa           INTEGER 
                                                                        , cod_conta              INTEGER 
                                                                        , num_pao                INTEGER                                                                                
                                                                        , num_orgao              INTEGER 
                                                                        , num_unidade            INTEGER 
                                                                        , cod_recurso            INTEGER                                                                                
                                                                        , cod_funcao             INTEGER 
                                                                        , cod_subfuncao          INTEGER 
                                                                        , tipo_conta             VARCHAR
                                                                        , vl_original            NUMERIC 
                                                                        , dt_criacao             DATE    
                                                                        , classificacao          VARCHAR                                                                                
                                                                        , descricao              VARCHAR 
                                                                        , num_recurso            VARCHAR 
                                                                        , nom_recurso            VARCHAR                                                                                
                                                                        , nom_orgao              VARCHAR 
                                                                        , nom_unidade            VARCHAR 
                                                                        , nom_funcao             VARCHAR                                                                                
                                                                        , nom_subfuncao          VARCHAR 
                                                                        , nom_programa           VARCHAR 
                                                                        , nom_pao                VARCHAR
                                                                        , empenhado_ano          NUMERIC 
                                                                        , empenhado_per          NUMERIC 
                                                                        , anulado_ano            NUMERIC                                                                                
                                                                        , anulado_per            NUMERIC 
                                                                        , pago_ano               NUMERIC 
                                                                        , pago_per               NUMERIC                                                                                 
                                                                        , liquidado_ano          NUMERIC 
                                                                        , liquidado_per          NUMERIC 
                                                                        , saldo_inicial          NUMERIC                                                                                
                                                                        , suplementacoes         NUMERIC 
                                                                        , reducoes               NUMERIC 
                                                                        , total_creditos         NUMERIC
                                                                        , credito_suplementar    NUMERIC 
                                                                        , credito_especial       NUMERIC 
                                                                        , credito_extraordinario NUMERIC
                                                                        , num_programa           VARCHAR 
                                                                        , num_acao               VARCHAR
                                                                        )
            GROUP BY nivel  
                          , cod_entidade 
                          , cod_estrutural
                          , descricao
                          , exercicio
    )';

    EXECUTE stSql;
    
    IF stRestos = true THEN
        -- PARA SITUAÇÕES LIQUIDADAS
        IF stSituacao = '2' THEN
            stSql := '
                INSERT INTO tmp_despesa
                         SELECT (SELECT publico.fn_nivel(conta_despesa.cod_estrutural)) AS nivel
                          , retorno.entidade AS cod_entidade 
                          , conta_despesa.cod_estrutural
                          , conta_despesa.descricao
                          , retorno.exercicio
                          , 0.00 AS empenhado
                          , retorno.valor AS liquidado
                          , 0.00 AS pago
                     FROM empenho.fn_empenho_restos_pagar_anulado_liquidado_estornoliquidacao(
                          '''',
                          '''',
                          '''||dtInicial||''', 
                          '''||dtFinal||''',
                          ''1,2,3'',
                          '''',
                          '''',
                          '''',
                          '''',
                          '''',
                          '''',
                          ''2''
                          ,''''
                          ,''''
                        ) AS retorno( entidade     INTEGER
                                    , empenho      INTEGER
                                    , exercicio    CHAR(4)
                                    , cgm          INTEGER
                                    , razao_social VARCHAR
                                    , cod_nota     INTEGER
                                    , valor        NUMERIC
                                    , data         TEXT
                                    )
                         JOIN empenho.empenho
                           ON empenho.exercicio    = retorno.exercicio
                          AND empenho.cod_entidade = retorno.entidade
                          AND empenho.cod_empenho  = retorno.empenho
                    
                         JOIN empenho.pre_empenho
                           ON pre_empenho.exercicio       = empenho.exercicio
                          AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    
                         JOIN empenho.restos_pre_empenho
                           ON restos_pre_empenho.exercicio       = pre_empenho.exercicio
                          AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                         JOIN orcamento.conta_despesa
                           ON conta_despesa.exercicio      = restos_pre_empenho.exercicio
                          AND REPLACE(conta_despesa.cod_estrutural,''.'','''') = restos_pre_empenho.cod_estrutural
                          AND (
                                 conta_despesa.cod_estrutural ILIKE ''3.1.9.0.04.%''
                              OR conta_despesa.cod_estrutural ILIKE ''3.1.9.0.05.%''
			                  OR conta_despesa.cod_estrutural ILIKE ''3.1.9.0.11.%''
			                  OR conta_despesa.cod_estrutural ILIKE ''3.1.9.0.13.%''
			                  OR conta_despesa.cod_estrutural ILIKE ''3.1.9.0.16.%''
			                  OR conta_despesa.cod_estrutural ILIKE ''3.1.9.1.13.%''
			                  OR restos_pre_empenho.cod_estrutural ILIKE ''319004%''
			                  OR restos_pre_empenho.cod_estrutural ILIKE ''319005%''
			                  OR restos_pre_empenho.cod_estrutural ILIKE ''319011%''
			                  OR restos_pre_empenho.cod_estrutural ILIKE ''319013%''
			                  OR restos_pre_empenho.cod_estrutural ILIKE ''319016%''
			                  OR restos_pre_empenho.cod_estrutural ILIKE ''319113%''
			                  )            
            ';
            
            EXECUTE stSql;
        -- PARA SITUAÇÕES PAGOS
        ELSIF stSituacao = '3' THEN
            stSql := '
                INSERT INTO tmp_despesa
                  SELECT (SELECT publico.fn_nivel(TRIM(REPLACE(TO_CHAR(retorno.cod_estrutural::BIGINT,''0:0:0:0:00:00:00:00:00''),'':'',''.'')))) AS nivel
                      , retorno.entidade AS cod_entidade 
                      , TRIM(REPLACE(TO_CHAR(retorno.cod_estrutural::BIGINT,''0:0:0:0:00:00:00:00:00''),'':'',''.'')) AS cod_estrutural
                      , retorno.credor
                      , retorno.exercicio
                      , 0.00 AS empenhado
                      , 0.00 AS liquidado
                      , retorno.valor AS pago
                  FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
                  ( ''''                      
                  , ''''                      
                  , '''||dtInicial||'''
                  , '''||dtFinal||'''
                  , ''1,2,3''
                  , ''''
                  , ''''
                  , ''''
                  , ''''
                  , ''''
                  , ''''
                  , ''''
                  , ''1''
                  , ''''
                  , ''true''
                  , ''''
                  , ''''
                  ) as retorno(      
                  entidade            integer,                             
                  empenho             integer,                             
                  exercicio           char(4),                             
                  credor              varchar,                             
                  cod_estrutural      varchar,                             
                  cod_nota            integer,                             
                  data                text,                                
                  conta               integer,                             
                  banco               varchar,                             
                  valor               numeric                              
                )
                JOIN (SELECT cod_estrutural, exercicio
                        FROM (SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.04.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.05.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.11.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.13.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.16.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.1.13.%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319004%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319005%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319011%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319013%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319016%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319113%''
                             ) AS despesa
                     ) AS despesas_pessoal 
                  ON despesas_pessoal.cod_estrutural = retorno.cod_estrutural
                 AND despesas_pessoal.exercicio = retorno.exercicio
            ';
            EXECUTE stSql;
        END IF;
    END IF;
    
  	stSql := '
	    CREATE TEMPORARY TABLE tmp_valor_despesa  (
                    nome_entidade varchar   
                  , cod_entidade integer
                  , nivel integer
                  , cod_estrutural varchar
                  , descricao varchar
                  , empenhado numeric(14,2)
                  , liquidado  numeric(14,2) 
                  , pago numeric(14,2)
                    
              )
    ';    

    EXECUTE stSql;

    WHILE inCount <= valorArr LOOP

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao, empenhado, liquidado, pago ) VALUES(
                    (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )  
                  , arEntidade[inCount]::INTEGER 
                  , 2
                  , '3.1.0.0.00.00.00.00.00'   
                  , (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )  
                  , NULL   
                  , NULL  
                  , NULL
        );

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago ) VALUES (
                    (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR
                  , arEntidade[inCount]::INTEGER
                  , 3
                  , '3.1.9.0.00.00.00.00.00'
                  , 'APLICACOES DIRETAS'
                  , 0.00
                  , 0.00
                  , 0.00
        );
 
        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago ) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , nivel
                  , cod_estrutural
                  , descricao
                  , empenhado
                  , liquidado
                  , pago
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.0.04%'   
                AND (cod_entidade =  arEntidade[inCount]::INTEGER OR cod_entidade IS NULL)
              GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago     
        );

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago ) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , nivel
                  , cod_estrutural
                  , descricao
                  , empenhado
                  , liquidado
                  , pago
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.0.05%'   
                AND (cod_entidade =  arEntidade[inCount]::INTEGER OR cod_entidade IS NULL)
              GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago
        );

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago ) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , nivel
                  , cod_estrutural
                  , descricao
                  , empenhado
                  , liquidado
                  , pago
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.0.11%'   
                AND (cod_entidade =  arEntidade[inCount]::INTEGER OR cod_entidade IS NULL)
              GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago
        );

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago ) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , nivel
                  , cod_estrutural
                  , descricao
                  , empenhado
                  , liquidado
                  , pago
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.0.13%'   
                AND (cod_entidade =  arEntidade[inCount]::INTEGER OR cod_entidade IS NULL)
              GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago
        );

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago ) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , nivel
                  , cod_estrutural
                  , descricao
                  , empenhado
                  , liquidado
                  , pago
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.0.16%'   
                AND (cod_entidade =  arEntidade[inCount]::INTEGER OR cod_entidade IS NULL)
              GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago
        );

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao, empenhado, liquidado, pago) VALUES (
                    (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR
                  , arEntidade[inCount]::INTEGER
                  , 3
                  , '3.1.9.1.00.00.00.00.00'
                  , 'APLICACOES DIRETAS - OPERAÇÕES INTRA-ORÇAMENTARIAS'
                  , 0.00
                  , 0.00
                  , 0.00
                              
        );

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , nivel
                  , cod_estrutural
                  , descricao
                  , empenhado
                  , liquidado
                  , pago
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.1.13%'   
                AND (cod_entidade =  arEntidade[inCount]::INTEGER )
              GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago
        );     

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao, empenhado, liquidado, pago ) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , 5
                  , '3.1.9.0.01.00.00.00.00' AS cod_estrutural
                  , 'APOSENTADORIAS E REFORMAS'
                  , COALESCE(SUM(empenhado), 0.00)
                  , COALESCE(SUM(liquidado), 0.00)
                  , COALESCE(SUM(pago)     , 0.00)
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.0.01.%'   
                AND (cod_entidade = arEntidade[inCount]::INTEGER OR cod_entidade IS NULL)
           GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago
        );               

        INSERT INTO tmp_valor_despesa (nome_entidade, cod_entidade, nivel, cod_estrutural, descricao,  empenhado, liquidado, pago ) (
             SELECT (SELECT sw_cgm.nom_cgm 
                       FROM sw_cgm
                      INNER JOIN orcamento.entidade 
                         ON entidade.numcgm = sw_cgm.numcgm 
                      INNER JOIN administracao.configuracao 
                         ON configuracao.valor = entidade.cod_entidade::VARCHAR 
                      WHERE entidade.exercicio = stExercicio::VARCHAR
                        AND entidade.cod_entidade = arEntidade[inCount]::INTEGER 
                      GROUP BY sw_cgm.nom_cgm
                          , entidade.cod_entidade )::VARCHAR AS entidade
                  , arEntidade[inCount]::INTEGER AS cod_entidade
                  , 5
                  , '3.1.9.0.03.00.00.00.00' AS cod_estrutural
                  , 'PENSOES'
                  , COALESCE(SUM(empenhado), 0.00)
                  , COALESCE(SUM(liquidado), 0.00)
                  , COALESCE(SUM(pago)     , 0.00)
               FROM tmp_despesa 
              WHERE cod_estrutural LIKE '3.1.9.0.03.%'   
                AND (cod_entidade = arEntidade[inCount]::INTEGER OR cod_entidade IS NULL)
           GROUP BY entidade
                  , cod_entidade
                  , nivel
                  , cod_estrutural 
                  , descricao 
                  , empenhado
                  , liquidado
                  , pago
        );               
        inCount := inCount + 1;
    END LOOP;

    WHILE inCountUpdate <= valorArr LOOP      

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.04%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.04%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.04%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                               WHERE cod_estrutural like '3.1.9.0.04.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.05%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.05%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.05%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                               WHERE cod_estrutural like '3.1.9.0.05.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.11%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.11%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.11%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                               WHERE cod_estrutural like '3.1.9.0.11.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.13%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.13%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.13%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                               WHERE cod_estrutural like '3.1.9.0.13.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.16%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.16%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.16%' AND cod_entidade = arEntidade[inCountUpdate]::integer)
                               WHERE cod_estrutural like '3.1.9.0.16.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0%' and cod_estrutural not like '3.1.9.0.01.%' and cod_estrutural not like '3.1.9.0.03.%' and nivel=5 AND cod_entidade= arEntidade[inCountUpdate]::integer) 
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0%' and cod_estrutural not like '3.1.9.0.01.%' and cod_estrutural not like '3.1.9.0.03.%' and nivel=5 AND cod_entidade= arEntidade[inCountUpdate]::integer) 
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0%' and cod_estrutural not like '3.1.9.0.01.%' and cod_estrutural not like '3.1.9.0.03.%' and nivel=5 AND cod_entidade= arEntidade[inCountUpdate]::integer) 
                               WHERE cod_estrutural like '3.1.9.0.00.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer ;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1.13%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1.13%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1.13%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                               WHERE cod_estrutural like '3.1.9.1.13.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1%' and nivel=5 AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1%' and nivel=5 AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1%' and nivel=5 AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                               WHERE cod_estrutural like '3.1.9.1.00.00.00.00.00' AND cod_entidade = arEntidade[inCountUpdate]::integer ;

        UPDATE tmp_valor_despesa SET empenhado = (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.00.00.00.00.00%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , liquidado = (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.00.00.00.00.00%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , pago      = (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.0.00.00.00.00.00%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                               WHERE cod_estrutural like '3.1.0.0.00.00.00.00.00' AND nivel = 2 AND cod_entidade = arEntidade[inCountUpdate]::integer;

        UPDATE tmp_valor_despesa SET empenhado = empenhado + (SELECT SUM(empenhado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1.00.00.00.00.00%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , liquidado = liquidado + (SELECT SUM(liquidado) FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1.00.00.00.00.00%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                                   , pago      = pago      + (SELECT SUM(pago)      FROM tmp_valor_despesa WHERE cod_estrutural like '3.1.9.1.00.00.00.00.00%' AND cod_entidade = arEntidade[inCountUpdate]::integer) 
                               WHERE cod_estrutural like '3.1.0.0.00.00.00.00.00' AND nivel = 2 AND cod_entidade = arEntidade[inCountUpdate]::integer ;


        inCountUpdate := inCountUpdate + 1;
    END LOOP;

    ---------------------------------------------------
    -- Buscando o valor gasto no exercicio anterior ---
    ---------------------------------------------------
    inAno :=  substr(dtFinal, 7, 4 ) ;
    inMes :=  substr(dtFinal, 4, 2 ) ; 
    
    inExercicio := inAno;
    
    i := 1;
    WHILE i <= 12 loop
        IF ( inMes < 10 ) THEN
            stMes := '0' || inMes;
        ELSE
            stMes := inMes;
        END IF;
    
        arDatas[i] := '01/' || stMes || '/'|| inAno;
    
        i := i +1;
        inMes := inMes -1;
        IF ( inMes = 0 ) THEN
            inAno := inAno -1;
            inMes := 12;
        END IF;
    END LOOP;

    i := 12;
    WHILE i >= 1 LOOP

        IF SUBSTR(arDatas[i],7,4)::INTEGER < inExercicio THEN
            SELECT COALESCE(SUM(valor), 0) INTO nuVlMesalDespesa FROM stn.despesa_pessoal WHERE mes = SUBSTR(arDatas[i],4,2)::INTEGER AND ano = SUBSTR(arDatas[i],7,4) AND cod_entidade IN (1);
        ELSE
            nuVlMesalDespesa := 0.00;
        END IF;
        nuVlTotalMesalDespesaCamara := nuVlTotalMesalDespesaCamara + nuVlMesalDespesa;
        
        IF SUBSTR(arDatas[i],7,4)::INTEGER < inExercicio THEN
            SELECT COALESCE(SUM(valor), 0) INTO nuVlMesalDespesa FROM stn.despesa_pessoal WHERE mes = SUBSTR(arDatas[i],4,2)::INTEGER AND ano = SUBSTR(arDatas[i],7,4) AND cod_entidade IN (2);
        ELSE
            nuVlMesalDespesa := 0.00;
        END IF;
        nuVlTotalMesalDespesaPrefeitura := nuVlTotalMesalDespesaPrefeitura + nuVlMesalDespesa;
        
        IF SUBSTR(arDatas[i],7,4)::INTEGER < inExercicio THEN
            SELECT COALESCE(SUM(valor), 0) INTO nuVlMesalDespesa FROM stn.despesa_pessoal WHERE mes = SUBSTR(arDatas[i],4,2)::INTEGER AND ano = SUBSTR(arDatas[i],7,4) AND cod_entidade IN (3);
        ELSE
            nuVlMesalDespesa := 0.00;
        END IF;
        nuVlTotalMesalDespesaInstituto := nuVlTotalMesalDespesaInstituto + nuVlMesalDespesa;
        
        i := i - 1;
    
    END LOOP;
    
    -------------------------------------------------
    -- Adiciona o valor da despesa pessoal vinculada
    -------------------------------------------------
	stSql := '
          SELECT DISTINCT
                 cod_entidade
               , nome_entidade
               , nivel
               , cod_estrutural
               , descricao
               , empenhado
               , liquidado
               , pago
               , 0.00 AS valor_ano_anterior_camara
               , 0.00 AS valor_ano_anterior_prefeitura
               , 0.00 AS valor_ano_anterior_instituto
            FROM tmp_valor_despesa
        GROUP BY cod_entidade
               , nome_entidade
               , nivel
               , cod_estrutural 
               , descricao 
               , empenhado
               , liquidado
               , pago
               , valor_ano_anterior_camara
               , valor_ano_anterior_prefeitura
               , valor_ano_anterior_instituto
        ORDER BY cod_estrutural
    ';
 	
    FOR reRegistro IN EXECUTE stSql
    LOOP
        reRegistro.valor_ano_anterior_camara        := nuVlTotalMesalDespesaCamara;
        reRegistro.valor_ano_anterior_prefeitura    := nuVlTotalMesalDespesaPrefeitura;
        reRegistro.valor_ano_anterior_instituto     := nuVlTotalMesalDespesaInstituto;

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_despesa; 
    DROP TABLE tmp_valor_despesa; 

    RETURN;
END;
$$ language 'plpgsql';

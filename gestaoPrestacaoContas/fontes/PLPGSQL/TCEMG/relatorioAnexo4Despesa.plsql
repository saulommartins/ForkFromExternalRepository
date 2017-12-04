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

CREATE OR REPLACE FUNCTION tcemg.relatorio_anexo4_despesa ( varchar,varchar,varchar,varchar, VARCHAR, BOOLEAN ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio    	  ALIAS FOR $1;
    stDtInicial       ALIAS FOR $2;
    stDtFinal      	  ALIAS FOR $3;
    stExercicioRestos ALIAS FOR $4;
    stSituacao        ALIAS FOR $5;
    stRestos          ALIAS FOR $6;
    
    reRegistro 		    RECORD;
    reReg   		    RECORD;
    stSql 		        varchar := '';
    stSqlAux    	    varchar := '';
    stSqlTipoDespesa    varchar := '';
    stValor        	    varchar := '';
    intI                integer;
    intMes              integer;
    dtInicioMes         varchar[];
    dtFimMes            varchar[];
    stCodEstrutural     varchar;
    stDataFinalAnterior VARCHAR;
    
BEGIN
    -- CRIA A TABELA TEMPORÁRIA DO BALANCETE DE DESPESA PARA TRAZER TODOS OS DADOS
    stSql := '
        CREATE TEMPORARY TABLE tmp_balancete_despesa AS (
                SELECT ( select publico.fn_nivel(classificacao)) AS nivel  
                          , cod_entidade 
                          , classificacao
                          , descricao
                          , exercicio
                          , SUM(empenhado_per) as empenhado_per
                          , SUM(liquidado_per) as liquidado_per
                          , SUM(pago_per) as pago_per';
    
    IF stSituacao = '3' THEN
      stSql := stSql || ' , (SUM(pago_ano)-SUM(pago_per)) as empenhado_ate_periodo ';
    ELSIF stSituacao = '2' THEN
      stSql := stSql || ' , (SUM(liquidado_ano)-SUM(liquidado_per)) as empenhado_ate_periodo ';
    ELSE
      stSql := stSql || ' , (SUM(empenhado_ano)-SUM(empenhado_per)) as empenhado_ate_periodo ';
    END IF;
    
    stSql := stSql || 'FROM orcamento.fn_balancete_despesa('|| quote_literal(stExercicio) ||',
                                                             ''AND od.cod_entidade IN  (1,2,3)'',
                                                             '|| quote_literal(stDtInicial) ||',
                                                             '|| quote_literal(stDtFinal) || ',
                                                             '''','''','''','''','''','''','''',''''
                                                            ) AS retorno(
                                                                         exercicio           CHAR(4), cod_despesa      INTEGER, cod_entidade           INTEGER,                                                                                
                                                                         cod_programa        INTEGER, cod_conta        INTEGER, num_pao                INTEGER,                                                                                
                                                                         num_orgao           INTEGER, num_unidade      INTEGER, cod_recurso            INTEGER,                                                                                
                                                                         cod_funcao          INTEGER, cod_subfuncao    INTEGER, tipo_conta             VARCHAR,
                                                                         vl_original         NUMERIC, dt_criacao       DATE,    classificacao          VARCHAR,                                                                                
                                                                         descricao           VARCHAR, num_recurso      VARCHAR, nom_recurso            VARCHAR,                                                                                
                                                                         nom_orgao           VARCHAR, nom_unidade      VARCHAR, nom_funcao             VARCHAR,                                                                                
                                                                         nom_subfuncao       VARCHAR, nom_programa     VARCHAR, nom_pao                VARCHAR,
                                                                         empenhado_ano       NUMERIC, empenhado_per    NUMERIC, anulado_ano            NUMERIC,                                                                                
                                                                         anulado_per         NUMERIC, pago_ano         NUMERIC, pago_per               NUMERIC,                                                                                 
                                                                         liquidado_ano       NUMERIC, liquidado_per    NUMERIC, saldo_inicial          NUMERIC,                                                                                
                                                                         suplementacoes      NUMERIC, reducoes         NUMERIC, total_creditos         NUMERIC,                                                                                
                                                                         credito_suplementar NUMERIC, credito_especial NUMERIC, credito_extraordinario NUMERIC,
                                                                         num_programa        VARCHAR, num_acao         VARCHAR
                                                                        )
                   GROUP BY nivel
                          , cod_entidade
                          , classificacao
                          , descricao
                          , exercicio
    )';
            
    EXECUTE stSql;

    IF stRestos = true THEN
        -- PARA SITUAÇÕES LIQUIDADAS
        IF stSituacao = '2' THEN
            stSql := '
                INSERT INTO tmp_balancete_despesa
                         SELECT (SELECT publico.fn_nivel(conta_despesa.cod_estrutural)) AS nivel
                          , retorno.entidade AS cod_entidade 
                          , conta_despesa.cod_estrutural AS classificacao
                          , conta_despesa.descricao
                          , retorno.exercicio
                          , 0.00 AS empenhado_per
                          , retorno.valor AS liquidado_per
                          , 0.00 AS pago_per
                          , 0.00 AS empenhado_ate_periodo
                     FROM empenho.fn_empenho_restos_pagar_anulado_liquidado_estornoliquidacao(
                          '''',
                          '''',
                          '''||stDtInicial||''', 
                          '''||stDtFinal||''',
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
                    
                         JOIN (SELECT cod_conta, exercicio
                                 FROM (SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.04.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.05.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.11.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.13.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.16.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.1.13.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.03.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.01.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.91.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.94.%''
                                      ) AS despesa
                              ) AS despesas_pessoal 
                           ON despesas_pessoal.cod_conta = conta_despesa.cod_conta
                          AND despesas_pessoal.exercicio = conta_despesa.exercicio
            
            ';
            EXECUTE stSql;
            
            SELECT TO_CHAR(TO_DATE(stDtInicial,'dd/mm/yyyy')-1,'dd/mm/yyyy') INTO stDataFinalAnterior;
            
            stSql := '
                INSERT INTO tmp_balancete_despesa
                         SELECT (SELECT publico.fn_nivel(conta_despesa.cod_estrutural)) AS nivel
                          , retorno.entidade AS cod_entidade 
                          , conta_despesa.cod_estrutural AS classificacao
                          , conta_despesa.descricao
                          , retorno.exercicio
                          , 0.00 AS empenhado_per
                          , 0.00 AS liquidado_per
                          , 0.00 AS pago_per
                          , retorno.valor AS empenhado_ate_periodo
                     FROM empenho.fn_empenho_restos_pagar_anulado_liquidado_estornoliquidacao(
                          '''',
                          '''',
                          ''01/01/'||substr(stDtInicial, 7, 4 )||''', 
                          '''||stDataFinalAnterior||''',
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
                    
                         JOIN (SELECT cod_conta, exercicio
                                 FROM (SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.04.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.05.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.11.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.13.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.16.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.1.13.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.03.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.01.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.91.%''
                                       UNION
                                       SELECT cod_conta, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.94.%''
                                      ) AS despesa
                              ) AS despesas_pessoal 
                           ON despesas_pessoal.cod_conta = conta_despesa.cod_conta
                          AND despesas_pessoal.exercicio = conta_despesa.exercicio
            
            ';
            EXECUTE stSql;
            
        -- PARA SITUAÇÕES PAGOS
        ELSIF stSituacao = '3' THEN
            stSql := '
                INSERT INTO tmp_balancete_despesa
                  SELECT (SELECT publico.fn_nivel(TRIM(REPLACE(TO_CHAR(retorno.cod_estrutural::BIGINT,''0:0:0:0:00:00:00:00:00''),'':'',''.'')))) AS nivel
                      , retorno.entidade AS cod_entidade 
                      , TRIM(REPLACE(TO_CHAR(retorno.cod_estrutural::BIGINT,''0:0:0:0:00:00:00:00:00''),'':'',''.'')) AS classificacao
                      , retorno.credor
                      , retorno.exercicio
                      , 0.00 AS empenhado_per
                      , 0.00 AS liquidado_per
                      , retorno.valor AS pago_per
                      , 0.00 AS empenhado_ate_periodo
                  FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
                  ( ''''                      
                  , ''''                      
                  , '''||stDtInicial||'''
                  , '''||stDtFinal||'''
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
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.03.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.01.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.91.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.94.%''
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
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319003%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319001%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319091%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319094%''
                             ) AS despesa
                     ) AS despesas_pessoal 
                  ON despesas_pessoal.cod_estrutural = retorno.cod_estrutural
                 AND despesas_pessoal.exercicio = retorno.exercicio
            ';
            EXECUTE stSql;
            
            SELECT TO_CHAR(TO_DATE(stDtInicial,'dd/mm/yyyy')-1,'dd/mm/yyyy') INTO stDataFinalAnterior;
            
            stSql := '
                INSERT INTO tmp_balancete_despesa
                  SELECT (SELECT publico.fn_nivel(TRIM(REPLACE(TO_CHAR(retorno.cod_estrutural::BIGINT,''0:0:0:0:00:00:00:00:00''),'':'',''.'')))) AS nivel
                      , retorno.entidade AS cod_entidade 
                      , TRIM(REPLACE(TO_CHAR(retorno.cod_estrutural::BIGINT,''0:0:0:0:00:00:00:00:00''),'':'',''.'')) AS classificacao
                      , retorno.credor
                      , retorno.exercicio
                      , 0.00 AS empenhado_per
                      , 0.00 AS liquidado_per
                      , 0.00 AS pago_per
                      , retorno.valor AS empenhado_ate_periodo
                  FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
                  ( ''''                      
                  , ''''                      
                  , ''01/01/'||substr(stDtInicial, 7, 4 )||'''
                  , '''||stDataFinalAnterior||'''
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
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.03.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.01.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.91.%''
                              UNION
                              SELECT REPLACE(cod_estrutural,''.'','''') AS cod_estrutural, exercicio FROM orcamento.conta_despesa WHERE cod_estrutural ILIKE ''3.1.9.0.94.%''
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
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319003%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319001%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319091%''
                              UNION
                              SELECT cod_estrutural, exercicio FROM empenho.restos_pre_empenho WHERE cod_estrutural ILIKE ''319094%''
                             ) AS despesa
                     ) AS despesas_pessoal 
                  ON despesas_pessoal.cod_estrutural = retorno.cod_estrutural
                 AND despesas_pessoal.exercicio = retorno.exercicio
            ';
            EXECUTE stSql;
        END IF;
    END IF;
        
        stSql := '
        --SELECT * FROM (
                        SELECT
                                ''3.1.00.00.00'' AS cod_estrutural,
                                1 AS nivel,
                                1 AS tipo,
                                ''PESSOAL E ENCARGOS SOCIAIS'' AS descricao,
                                0.00 AS empenhado,
                                0.00 AS liquidado,
                                0.00 AS pago,
                                0.00 AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.04.00'' AS cod_estrutural,
                                2 AS nivel,
                                1 AS tipo,
                                ''Contratação por Tempo Determinado'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.05.01'' AS cod_estrutural,
                                3 AS nivel,
                                1 AS tipo,
                                ''Outros Benefícios Previdenciários de Pessoal Ativo'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.11.00'' AS cod_estrutural,
                                4 AS nivel,
                                1 AS tipo,
                                ''Vencimentos e Vantagens Fixas - Pessoal Civil'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.13.00'' AS cod_estrutural,
                                5 AS nivel,
                                1 AS tipo,
                                ''Obrigações Patronais'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.16.00'' AS cod_estrutural,
                                6 AS nivel,
                                1 AS tipo,
                                ''Outras Despesas Variáveis - Pessoal Civil'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.16%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.16%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.16%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.16%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.91.13.00'' AS cod_estrutural,
                                7 AS nivel,
                                1 AS tipo,
                                ''Obrigações Patronais'' AS descricao,
                                COALESCE((SELECT sum(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado,
                                COALESCE((SELECT sum(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS liquidado,
                                COALESCE((SELECT sum(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS pago                ,
                                COALESCE((SELECT sum(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 2),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.00.00.00'' AS cod_estrutural,
                                1 AS nivel,
                                2 AS tipo,
                                ''PESSOAL E ENCARGOS SOCIAIS'' AS descricao,
                                0.00 AS empenhado,
                                0.00 AS liquidado,
                                0.00 AS pago,
                                0.00 AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.03.02'' AS cod_estrutural,
                                2 AS nivel,
                                2 AS tipo,
                                ''Pensões Custeadas com Recursos Ordinários do Tesouro'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado_ate_periodo
                        
                        UNION
                        SELECT
                                ''3.1.90.05.01'' AS cod_estrutural,
                                3 AS nivel,
                                2 AS tipo,
                                ''Outros Benefícios Previdenciários de Pessoal Ativo'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.11.00'' AS cod_estrutural,
                                4 AS nivel,
                                2 AS tipo,
                                ''Vencimentos e Vantagens Fixas - Pessoal Civil'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.13.00'' AS cod_estrutural,
                                5 AS nivel,
                                2 AS tipo,
                                ''Obrigações Patronais'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.91.13.00'' AS cod_estrutural,
                                6 AS nivel,
                                2 AS tipo,
                                ''Obrigações Patronais'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 1),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.00.00.00'' AS cod_estrutural,
                                1 AS nivel,
                                3 AS tipo,
                                ''PESSOAL E ENCARGOS SOCIAIS'' AS descricao,
                                0.00 AS empenhado,
                                0.00 AS liquidado,
                                0.00 AS pago,
                                0.00 AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.01.01'' AS cod_estrutural,
                                2 AS nivel,
                                3 AS tipo,
                                ''Aposentadorias Custeadas com Recursos do RPPS'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.01.02'' AS cod_estrutural,
                                3 AS nivel,
                                3 AS tipo,
                                ''Aposentadorias Custeadas com Recursos Ordinários do Tesouro'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.03.01'' AS cod_estrutural,
                                4 AS nivel,
                                3 AS tipo,
                                ''Pensões Custeadas com Recursos do RPPS'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.03.02'' AS cod_estrutural,
                                5 AS nivel,
                                3 AS tipo,
                                ''Pensões Custeadas com Recursos Ordinários do Tesouro'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.02%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.04.00'' AS cod_estrutural,
                                6 AS nivel,
                                3 AS tipo,
                                ''Contratação por Tempo Determinado'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.04%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.05.01'' AS cod_estrutural,
                                7 AS nivel,
                                3 AS tipo,
                                ''Outros Benefícios Previdenciários de Pessoal Ativo'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.01%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.11.00'' AS cod_estrutural,
                                8 AS nivel,
                                3 AS tipo,
                                ''Vencimentos e Vantagens Fixas - Pessoal Civil'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.11%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.90.13.00'' AS cod_estrutural,
                                9 AS nivel,
                                3 AS tipo,
                                ''Obrigações Patronais'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''3.1.91.13.00'' AS cod_estrutural,
                                10 AS nivel,
                                3 AS tipo,
                                ''Obrigações Patronais'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.1.13%'' AND tmp_balancete_despesa.cod_entidade = 3),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                    ''01'' AS cod_estrutural,
                                    1 AS nivel,
                                    4 AS tipo,
                                    ''(-) Inativos e Pensionistas com Fonte de Custeio Própria'' AS descricao,
                                    COALESCE(SUM(retorno.empenhado),0.00) AS empenhado,
                                    COALESCE(SUM(retorno.liquidado),0.00) AS liquidado,
                                    COALESCE(SUM(retorno.pago),0.00) AS pago,
                                    COALESCE(SUM(retorno.empenhado_ate_periodo),0.00) AS empenhado_ate_periodo
                                    
                            FROM
                                (
                                    SELECT
                                            ''01'' AS cod_estrutural,
                                            COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.01%''),0.00) AS empenhado,
                                            COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.01%''),0.00) AS liquidado,
                                            COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01.01%''),0.00) AS pago,
                                            COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.01%''),0.00) AS empenhado_ate_periodo
                                    UNION
                                    
                                    SELECT
                                            ''01'' AS cod_estrutural,
                                            COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.01%''),0.00) AS empenhado,
                                            COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.01%''),0.00) AS liquidado,
                                            COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.03.01%''),0.00) AS pago,
                                            COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.05.02%''),0.00) AS empenhado_ate_periodo
                                ) AS retorno
                                
                            GROUP BY cod_estrutural, descricao, nivel, tipo
                            
                        UNION
                        SELECT
                                ''02'' AS cod_estrutural,
                                2 AS nivel,
                                4 AS tipo,
                                ''(-) Sentenças Judiciais Anteriores'' AS descricao,
                                COALESCE((SELECT SUM(empenhado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.91%''),0.00) AS empenhado,
                                COALESCE((SELECT SUM(liquidado_per) FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.91%''),0.00) AS liquidado,
                                COALESCE((SELECT SUM(pago_per)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.91%''),0.00) AS pago,
                                COALESCE((SELECT SUM(empenhado_ate_periodo)      FROM tmp_balancete_despesa WHERE tmp_balancete_despesa.classificacao like ''3.1.9.0.91%''),0.00) AS empenhado_ate_periodo
                        UNION
                        SELECT
                                ''03'' AS cod_estrutural,
                                3 AS nivel,
                                4 AS tipo,
                                ''(-) Aposentadorias e Pensões Custeadas com Recursos da Fonte Tesouro'' AS descricao,
                                0.00 AS empenhado,
                                0.00 AS liquidado,
                                0.00 AS pago,
                                0.00 AS empenhado_ate_periodo
            --) AS tabela
        ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
        DROP TABLE tmp_balancete_despesa;
        --DROP TABLE tmp_empenhos_restos ;
        
    RETURN;
 
END;
$$
language 'plpgsql';

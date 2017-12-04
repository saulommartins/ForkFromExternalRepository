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
    * PL do TCEMG Despesa Total com Pessoal - Arquivo TCEMG da GPC 
    * Data de Criação   : 06/01/2014

    * @author Analista:      
    * @author Desenvolvedor: Arthur Cruz
    
    $Id: FTCEMGDespesaTotalPessoal.plsql 63419 2015-08-26 19:33:43Z jean $
*/
CREATE OR REPLACE FUNCTION tcemg.fn_relatorio_despesa_total_pessoal(VARCHAR, VARCHAR, VARCHAR, VARCHAR, INTEGER, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE 
    stExercicio    ALIAS FOR $1;
    stEntidades    ALIAS FOR $2;
    stDtIni        ALIAS FOR $3;
    stDtFim        ALIAS FOR $4;
    -- 1 para DESPESAS
    -- 2 para EXCLUSÕES
    inTipoDados    ALIAS FOR $5;
    stTipoSituacao ALIAS FOR $6;
    
    inExercicio    INTEGER;
    inCountNivel   INTEGER := 0;
    stSql          VARCHAR := '';
    reReg	   RECORD;
    
BEGIN 
    inExercicio :=  substr(stDtFim, 7, 4 ) ;
    
    IF ( stTipoSituacao = 'empenhado' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_valor_despesa AS (
                    SELECT
                          cd.cod_estrutural as cod_estrutural
                        , coalesce(ipe.vl_total,0.00) as valor
                        , e.dt_empenho as data
                    FROM
                          orcamento.despesa           AS od
                        , orcamento.conta_despesa     AS cd
                        , empenho.pre_empenho_despesa AS ped
                        , empenho.empenho             AS e
                        , empenho.pre_empenho         AS pe
                        , empenho.item_pre_empenho    AS ipe
                    WHERE
                            cd.cod_conta               = ped.cod_conta
                        AND cd.exercicio               = ped.exercicio
            
                        And od.cod_despesa             = ped.cod_despesa
                        AND od.exercicio               = ped.exercicio
            
                        And pe.exercicio               = ped.exercicio
                        And pe.cod_pre_empenho         = ped.cod_pre_empenho
            
                        And e.cod_entidade             IN (' || stEntidades || ')
                        And e.exercicio                = ' || quote_literal(stExercicio) || '
            
                        AND e.exercicio                = pe.exercicio
                        AND e.cod_pre_empenho          = pe.cod_pre_empenho
            
                        AND pe.exercicio               = ipe.exercicio
                        AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
                        
                        And od.exercicio IN ('''||inExercicio||''', '''||inExercicio-1||''')
                  );';
            EXECUTE stSql;
    END IF;
            
    IF ( stTipoSituacao = 'liquidado' ) THEN
                  
        stSql := 'CREATE TEMPORARY TABLE tmp_valor_despesa AS (
                    SELECT
                          cd.cod_estrutural as cod_estrutural
                        , nli.vl_total as valor
                        , nl.dt_liquidacao as data                       
                    FROM
                        orcamento.despesa             AS od,
                        orcamento.conta_despesa       AS cd,
                        empenho.pre_empenho_despesa   AS ped,
                        empenho.pre_empenho           AS pe,
                        empenho.empenho               AS e,
                        empenho.nota_liquidacao_item  AS nli,
                        empenho.nota_liquidacao       AS nl
                    WHERE
                            cd.cod_conta       = ped.cod_conta
                        AND cd.exercicio       = ped.exercicio
        
                        And od.cod_despesa     = ped.cod_despesa
                        AND od.exercicio       = ped.exercicio
        
                        And pe.exercicio       = ped.exercicio
                        And pe.cod_pre_empenho = ped.cod_pre_empenho
        
                        And e.cod_entidade     IN (' || stEntidades || ')
                        And e.exercicio        = ' || quote_literal(stExercicio) || '
        
                        AND e.exercicio        = pe.exercicio
                        AND e.cod_pre_empenho  = pe.cod_pre_empenho
        
                        AND e.exercicio        = nl.exercicio_empenho
                        AND e.cod_entidade     = nl.cod_entidade
                        AND e.cod_empenho      = nl.cod_empenho
        
                        AND nl.exercicio       = nli.exercicio
                        AND nl.cod_nota        = nli.cod_nota
                        AND nl.cod_entidade    = nli.cod_entidade
                        
                        And od.exercicio    IN ('''||inExercicio||''', '''||inExercicio-1||''')
                        And nl.cod_entidade IN ('||stEntidades||')
                 );';
        EXECUTE stSql;
    END IF;
    
    IF ( stTipoSituacao = 'pago' ) THEN 
        
        stSql := 'CREATE TEMPORARY TABLE tmp_valor_despesa AS (
                    SELECT 
                         OCD.cod_estrutural as cod_estrutural	    
                       , ENLP.vl_pago as valor
                       , ENL.dt_liquidacao as data                       
                    FROM
                        orcamento.despesa               AS OD,
                        orcamento.conta_despesa         AS OCD,
                        empenho.pre_empenho_despesa     AS EPED,
                        empenho.empenho                 AS EE,
                        empenho.pre_empenho             AS EPE,
                        empenho.nota_liquidacao         AS ENL,
                        empenho.nota_liquidacao_paga    AS ENLP
            
                    WHERE
                            OCD.cod_conta            = EPED.cod_conta
                        AND OCD.exercicio            = EPED.exercicio
            
                        AND OD.cod_despesa           = EPED.cod_despesa
                        AND OD.exercicio             = EPED.exercicio
            
                        And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                        And EPED.exercicio           = EPE.exercicio
            
                        And EPE.exercicio            = EE.exercicio
                        And EPE.cod_pre_empenho      = EE.cod_pre_empenho
            
                        And EE.exercicio             = '|| quote_literal(stExercicio) ||'
                        And EE.cod_entidade          IN ('||stEntidades||')
            
                        And EE.cod_empenho           = ENL.cod_empenho
                        And EE.exercicio             = ENL.exercicio_empenho
                        And EE.cod_entidade          = ENL.cod_entidade
            
                        And ENL.cod_nota             = ENLP.cod_nota
                        And ENL.cod_entidade         = ENLP.cod_entidade
                        And ENL.exercicio            = ENLP.exercicio 
            
                        And OD.exercicio    IN ('''||inExercicio||''', '''||inExercicio-1||''')
                        And ENL.cod_entidade IN ('||stEntidades||')
                );';
        EXECUTE stSql;
    END IF;
    
  	-- -------------------------------------	
	-- Estrutura de Tabelas Temporarias
	-- -------------------------------------

	-- Tabela tmp_tcemg_despesa_total_pessoal
	-- Formatação e Cálculos Agrupados para Exibição de Resultados

    stSQL := '
    CREATE TEMPORARY TABLE tmp_tcemg_despesa_total_pessoal (
	 ordem          INTEGER 
       , cod_conta      VARCHAR
       , nom_conta      VARCHAR
       , cod_estrutural VARCHAR
       , mes_1          NUMERIC(14,2)
       , mes_2          NUMERIC(14,2)
       , mes_3          NUMERIC(14,2)
       , mes_4          NUMERIC(14,2)
       , mes_5          NUMERIC(14,2)
       , mes_6          NUMERIC(14,2)
       , mes_7          NUMERIC(14,2)
       , mes_8          NUMERIC(14,2)
       , mes_9          NUMERIC(14,2)
       , mes_10         NUMERIC(14,2)
       , mes_11         NUMERIC(14,2)
       , mes_12         NUMERIC(14,2)
       , total          NUMERIC(14,2)
    ); ';
	
    EXECUTE stSQL ;
    --Tabela temporaria para fazer o calculo dos primeiros
    stSQL := '
    CREATE TEMPORARY TABLE tmp_vencimentos_vantagens_1 (
         ordem          INTEGER 
       , cod_conta      VARCHAR
       , nom_conta      VARCHAR
       , cod_estrutural VARCHAR
       , mes_1          NUMERIC(14,2)
       , mes_2          NUMERIC(14,2)
       , mes_3          NUMERIC(14,2)
       , mes_4          NUMERIC(14,2)
       , mes_5          NUMERIC(14,2)
       , mes_6          NUMERIC(14,2)
       , mes_7          NUMERIC(14,2)
       , mes_8          NUMERIC(14,2)
       , mes_9          NUMERIC(14,2)
       , mes_10         NUMERIC(14,2)
       , mes_11         NUMERIC(14,2)
       , mes_12         NUMERIC(14,2)
       , total          NUMERIC(14,2)
    ); ';
	
    EXECUTE stSQL;
    --Tabela temporaria para fazer o calculo dos primeiros
    stSQL := '
    CREATE TEMPORARY TABLE tmp_vencimentos_vantagens_2 (
         ordem          INTEGER 
       , cod_conta      VARCHAR
       , nom_conta      VARCHAR
       , cod_estrutural VARCHAR
       , mes_1          NUMERIC(14,2)
       , mes_2          NUMERIC(14,2)
       , mes_3          NUMERIC(14,2)
       , mes_4          NUMERIC(14,2)
       , mes_5          NUMERIC(14,2)
       , mes_6          NUMERIC(14,2)
       , mes_7          NUMERIC(14,2)
       , mes_8          NUMERIC(14,2)
       , mes_9          NUMERIC(14,2)
       , mes_10         NUMERIC(14,2)
       , mes_11         NUMERIC(14,2)
       , mes_12         NUMERIC(14,2)
       , total          NUMERIC(14,2)
    ); ';
	
    EXECUTE stSQL;
    
    IF inTipoDados = 1 THEN
        
        /* Calculando os valores referente a Vencimentos e vantagens*/
        -- Calculando os valores referente as contas 3.1.90.04.00.00 + 3.1.90.11.00.00 + 3.1.90.16.00.00 + 3.1.90.94.00.00
        stSql := '
                INSERT INTO tmp_vencimentos_vantagens_1
                 SELECT 1 AS ordem
                      , ''9999'' AS cod_conta
                      , ''Vencimentos e vantagens'' AS nom_conta
                      , ''9.9.9.9.99.99.99.99.99'' cod_estrutural
                      , SUM( mes_1 ) AS mes_1
                      , SUM( mes_2 ) AS mes_2
                      , SUM( mes_3 ) AS mes_3
                      , SUM( mes_4 ) AS mes_4
                      , SUM( mes_5 ) AS mes_5
                      , SUM( mes_6 ) AS mes_6
                      , SUM( mes_7 ) AS mes_7
                      , SUM( mes_8 ) AS mes_8
                      , SUM( mes_9 ) AS mes_9
                      , SUM( mes_10 ) AS mes_10
                      , SUM( mes_11 ) AS mes_11
                      , SUM( mes_12 ) AS mes_12
                      , SUM( total ) AS total
                 FROM (
                 
                    -- Calculando os valores referente a conta 3.1.90.04.00.00  Nível 6
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total      
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.04'', 6, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
                                   
                UNION
                    
                    -- Calculando os valores referente a conta 3.1.90.11.00.00  Nível 6
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total      
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.11'', 6, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
                                   
                UNION
                
                    -- Calculando os valores referente a conta 3.1.90.16.00.00  Nível 6
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total      
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.16'', 6, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
                                   
                UNION
                
                    -- Calculando os valores referente a conta 3.1.90.94.00.00  Nível 6
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total      
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.94'', 6, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
                    
                    ) AS vencimentos_vantagens_1';
                    
        EXECUTE stSql;
       
        -- Calculando os valores referente as contas 3.1.90.11.07, 3.1.90.11.08, 3.1.90.11.09
        stSql := '
                 INSERT INTO tmp_vencimentos_vantagens_2
                 SELECT 1 AS ordem
                      , ''9999'' AS cod_conta
                      , ''Vencimentos e vantagens'' AS nom_conta
                      , ''9.9.9.9.99.99.99.99.99'' cod_estrutural
                      , SUM( mes_1 ) AS mes_1
                      , SUM( mes_2 ) AS mes_2
                      , SUM( mes_3 ) AS mes_3
                      , SUM( mes_4 ) AS mes_4
                      , SUM( mes_5 ) AS mes_5
                      , SUM( mes_6 ) AS mes_6
                      , SUM( mes_7 ) AS mes_7
                      , SUM( mes_8 ) AS mes_8
                      , SUM( mes_9 ) AS mes_9
                      , SUM( mes_10 ) AS mes_10
                      , SUM( mes_11 ) AS mes_11
                      , SUM( mes_12 ) AS mes_12
                      , SUM( total ) AS total
                 FROM (
                 
                    -- Calculando os valores referente a conta 3.1.90.11.07  Nível 7
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total      
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.11.07'', 7, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
                                   
                UNION
                   
                    -- Calculando os valores referente a conta 3.1.90.11.08  Nível 7
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total      
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.11.08'', 7, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC ) 
                
                UNION
                
                    -- Calculando os valores referente a conta 3.1.90.11.09  Nível 7
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total      
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.11.09'', 7, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
        
                ) AS vencimentos_vantagens_2 ';
                    
        EXECUTE stSql;
 
        /* OBS.: O calculo está sendo feito apartir das contas 3.1.90.04.00.00 + 3.1.90.11.00.00 + 3.1.90.16.00.00 + 3.1.90.94.00.00 - 3.1.90.11.07 - 3.1.90.11.08 - 3.1.90.11.09 */
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal    
                SELECT 1 AS ordem
                      , ''9999'' AS cod_conta
                      , ''Vencimentos e vantagens'' AS nom_conta
                      , ''9.9.9.9.99.99.99.99.99'' cod_estrutural
                      , SUM( tmp_vencimentos_vantagens_1.mes_1 - tmp_vencimentos_vantagens_2.mes_1 ) AS mes_1
                      , SUM( tmp_vencimentos_vantagens_1.mes_2 - tmp_vencimentos_vantagens_2.mes_2 ) AS mes_2
                      , SUM( tmp_vencimentos_vantagens_1.mes_3 - tmp_vencimentos_vantagens_2.mes_3 ) AS mes_3
                      , SUM( tmp_vencimentos_vantagens_1.mes_4 - tmp_vencimentos_vantagens_2.mes_4 ) AS mes_4
                      , SUM( tmp_vencimentos_vantagens_1.mes_5 - tmp_vencimentos_vantagens_2.mes_5 ) AS mes_5
                      , SUM( tmp_vencimentos_vantagens_1.mes_6 - tmp_vencimentos_vantagens_2.mes_6 ) AS mes_6
                      , SUM( tmp_vencimentos_vantagens_1.mes_7 - tmp_vencimentos_vantagens_2.mes_7 ) AS mes_7
                      , SUM( tmp_vencimentos_vantagens_1.mes_8 - tmp_vencimentos_vantagens_2.mes_8 ) AS mes_8
                      , SUM( tmp_vencimentos_vantagens_1.mes_9 - tmp_vencimentos_vantagens_2.mes_9 ) AS mes_9
                      , SUM( tmp_vencimentos_vantagens_1.mes_10 - tmp_vencimentos_vantagens_2.mes_10 ) AS mes_10
                      , SUM( tmp_vencimentos_vantagens_1.mes_11 - tmp_vencimentos_vantagens_2.mes_11 ) AS mes_11
                      , SUM( tmp_vencimentos_vantagens_1.mes_12 - tmp_vencimentos_vantagens_2.mes_12 ) AS mes_12
                      , SUM( tmp_vencimentos_vantagens_1.total - tmp_vencimentos_vantagens_2.total ) AS total
                
                  FROM tmp_vencimentos_vantagens_1 
            INNER JOIN tmp_vencimentos_vantagens_2
                    ON tmp_vencimentos_vantagens_2.cod_estrutural = tmp_vencimentos_vantagens_1.cod_estrutural
                   AND tmp_vencimentos_vantagens_2.cod_conta      = tmp_vencimentos_vantagens_1.cod_conta; ';
        
        EXECUTE stSql;
        -- Calculando os valores referente a conta 3.1.90.01.00.00  Nível 6
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 2 AS ordem
                      , cod_conta
                      , ''Inativos'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.01'', 6, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;

        -- Calculando os valores referente a conta 3.1.90.03.00.00  Nível 6 
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 2 AS ordem
                      , cod_conta
                      , ''Pensionistas'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.03'', 6, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;

        -- Calculando os valores referente a conta 3.1.90.05.00.00 Nível 6 
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 3 AS ordem
                      , cod_conta
                      , ''Salário Família'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.05'', 6, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;
        
        -- Calculando os valores referente a conta 3.1.90.11.07  Nível 7 
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 4 AS ordem
                      , cod_conta
                      , ''Subsídio do Prefeito'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.11.07'', 7, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;
    
        -- Calculando os valores referente a conta 3.1.90.11.08  Nível 7 
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 5 AS ordem
                      , cod_conta
                      , ''Subsídio do Vice-Prefeito'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.11.08'', 7, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;
       
        -- Calculando os valores referente a conta 3.1.90.11.09  Nível 7 
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 6 AS ordem
                      , cod_conta
                      , ''Subsídio Secretários'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.11.09'', 7, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;
       
        -- Calculando os valores referente a conta 3.1.90.13.03 + 3.3.1.9.0.13.03  Nível 7 
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                  SELECT 7 AS ordem
                      , ''9999'' AS cod_conta
                      , ''Obrigações Patronais'' AS nom_conta
                      , ''9.9.9.9.99.99.99.99.99'' cod_estrutural
                      , SUM( mes_1 ) AS mes_1
                      , SUM( mes_2 ) AS mes_2
                      , SUM( mes_3 ) AS mes_3
                      , SUM( mes_4 ) AS mes_4
                      , SUM( mes_5 ) AS mes_5
                      , SUM( mes_6 ) AS mes_6
                      , SUM( mes_7 ) AS mes_7
                      , SUM( mes_8 ) AS mes_8
                      , SUM( mes_9 ) AS mes_9
                      , SUM( mes_10 ) AS mes_10
                      , SUM( mes_11 ) AS mes_11
                      , SUM( mes_12 ) AS mes_12
                      , SUM( total ) AS total
                   FROM (
                 
                        -- Calculando os valores referente a conta 3.3.1.9.0.13.03  Nível 7 
                        SELECT mes_1
                             , mes_2
                             , mes_3
                             , mes_4
                             , mes_5
                             , mes_6
                             , mes_7
                             , mes_8
                             , mes_9
                             , mes_10
                             , mes_11
                             , mes_12
                             , total
                          FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.13.03'', 7, '||quote_literal(stTipoSituacao)||')
                                      AS retorno ( cod_conta      VARCHAR
                                                 , nom_conta      VARCHAR
                                                 , cod_estrutural VARCHAR
                                                 , mes_1          NUMERIC
                                                 , mes_2          NUMERIC
                                                 , mes_3          NUMERIC
                                                 , mes_4          NUMERIC
                                                 , mes_5          NUMERIC
                                                 , mes_6          NUMERIC
                                                 , mes_7          NUMERIC
                                                 , mes_8          NUMERIC
                                                 , mes_9          NUMERIC
                                                 , mes_10         NUMERIC
                                                 , mes_11         NUMERIC
                                                 , mes_12         NUMERIC
                                                 , total          NUMERIC )
                        UNION

                         -- Calculando os valores referente a conta 3.3.1.9.1.13.99  Nível 7 
                        SELECT mes_1
                             , mes_2
                             , mes_3
                             , mes_4
                             , mes_5
                             , mes_6
                             , mes_7
                             , mes_8
                             , mes_9
                             , mes_10
                             , mes_11
                             , mes_12
                             , total
                          FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.1.13.99'', 7, '||quote_literal(stTipoSituacao)||')
                                      AS retorno ( cod_conta      VARCHAR
                                                 , nom_conta      VARCHAR
                                                 , cod_estrutural VARCHAR
                                                 , mes_1          NUMERIC
                                                 , mes_2          NUMERIC
                                                 , mes_3          NUMERIC
                                                 , mes_4          NUMERIC
                                                 , mes_5          NUMERIC
                                                 , mes_6          NUMERIC
                                                 , mes_7          NUMERIC
                                                 , mes_8          NUMERIC
                                                 , mes_9          NUMERIC
                                                 , mes_10         NUMERIC
                                                 , mes_11         NUMERIC
                                                 , mes_12         NUMERIC
                                                 , total          NUMERIC )

                      UNION

                         -- Calculando os valores referente a conta 3.1.9.0.13.99  Nível 7
                        SELECT mes_1
                             , mes_2
                             , mes_3
                             , mes_4
                             , mes_5
                             , mes_6
                             , mes_7
                             , mes_8
                             , mes_9
                             , mes_10
                             , mes_11
                             , mes_12
                             , total
                          FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.13.99'', 7, '||quote_literal(stTipoSituacao)||')
                                      AS retorno ( cod_conta      VARCHAR
                                                 , nom_conta      VARCHAR
                                                 , cod_estrutural VARCHAR
                                                 , mes_1          NUMERIC
                                                 , mes_2          NUMERIC
                                                 , mes_3          NUMERIC
                                                 , mes_4          NUMERIC
                                                 , mes_5          NUMERIC
                                                 , mes_6          NUMERIC
                                                 , mes_7          NUMERIC
                                                 , mes_8          NUMERIC
                                                 , mes_9          NUMERIC
                                                 , mes_10         NUMERIC
                                                 , mes_11         NUMERIC
                                                 , mes_12         NUMERIC
                                                 , total          NUMERIC ) 
                    ) AS obrigacoes_patronais ';
        EXECUTE stSql;

        -- Calculando os valores referente as contas 3.1.90.13.02 + 3.1.91.13.02  Nível 7 
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 8 AS ordem
                      , ''9999'' AS cod_conta
                      , ''Repasse Patronal'' AS nom_conta
                      , ''9.9.9.9.99.99.99.99.99'' cod_estrutural
                      , SUM( mes_1 ) AS mes_1
                      , SUM( mes_2 ) AS mes_2
                      , SUM( mes_3 ) AS mes_3
                      , SUM( mes_4 ) AS mes_4
                      , SUM( mes_5 ) AS mes_5
                      , SUM( mes_6 ) AS mes_6
                      , SUM( mes_7 ) AS mes_7
                      , SUM( mes_8 ) AS mes_8
                      , SUM( mes_9 ) AS mes_9
                      , SUM( mes_10 ) AS mes_10
                      , SUM( mes_11 ) AS mes_11
                      , SUM( mes_12 ) AS mes_12
                      , SUM( total ) AS total
                  FROM (
                 
                    -- Calculando os valores referente a conta 3.1.90.13.02  Nível 7 
                     SELECT mes_1
                          , mes_2
                          , mes_3
                          , mes_4
                          , mes_5
                          , mes_6
                          , mes_7
                          , mes_8
                          , mes_9
                          , mes_10
                          , mes_11
                          , mes_12
                          , total
                       FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.13.02'', 7, '||quote_literal(stTipoSituacao)||')
                         AS retorno ( cod_conta      VARCHAR
                                    , nom_conta      VARCHAR
                                    , cod_estrutural VARCHAR
                                    , mes_1          NUMERIC
                                    , mes_2          NUMERIC
                                    , mes_3          NUMERIC
                                    , mes_4          NUMERIC
                                    , mes_5          NUMERIC
                                    , mes_6          NUMERIC
                                    , mes_7          NUMERIC
                                    , mes_8          NUMERIC
                                    , mes_9          NUMERIC
                                    , mes_10         NUMERIC
                                    , mes_11         NUMERIC
                                    , mes_12         NUMERIC
                                    , total          NUMERIC )
                        
                        UNION
                        
                        -- Calculando os valores referente a conta 3.1.91.13.02  Nível 7 
                        SELECT mes_1
                             , mes_2
                             , mes_3
                             , mes_4
                             , mes_5
                             , mes_6
                             , mes_7
                             , mes_8
                             , mes_9
                             , mes_10
                             , mes_11
                             , mes_12
                             , total
                          FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.1.13.02'', 7, '||quote_literal(stTipoSituacao)||')
                            AS retorno ( cod_conta      VARCHAR
                                       , nom_conta      VARCHAR
                                       , cod_estrutural VARCHAR
                                       , mes_1          NUMERIC
                                       , mes_2          NUMERIC
                                       , mes_3          NUMERIC
                                       , mes_4          NUMERIC
                                       , mes_5          NUMERIC
                                       , mes_6          NUMERIC
                                       , mes_7          NUMERIC
                                       , mes_8          NUMERIC
                                       , mes_9          NUMERIC
                                       , mes_10         NUMERIC
                                       , mes_11         NUMERIC
                                       , mes_12         NUMERIC
                                       , total          NUMERIC )
                        ) AS repasse_patronal';
            
        EXECUTE stSql;
       
        -- Calculando os valores referente a conta 3.1.90.91.00.00  Nível 6
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 9 AS ordem
                      , cod_conta
                      , ''Sentenças Judiciais de pessoal'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.91'', 6, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;
        
        -- Calculando os valores referente a conta 3.3.1.7.1.70  Nível 6
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                    SELECT 10 AS ordem
                          , cod_conta
                          , ''Outras Despesas de Pessoal'' AS nom_conta
                          , cod_estrutural
                          , mes_1
                          , mes_2
                          , mes_3
                          , mes_4
                          , mes_5
                          , mes_6
                          , mes_7
                          , mes_8
                          , mes_9
                          , mes_10
                          , mes_11
                          , mes_12
                          , total
                       FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.7.1.70'', 6, '||quote_literal(stTipoSituacao)||')
                                   AS retorno ( cod_conta      VARCHAR
                                              , nom_conta      VARCHAR
                                              , cod_estrutural VARCHAR
                                              , mes_1          NUMERIC
                                              , mes_2          NUMERIC
                                              , mes_3          NUMERIC
                                              , mes_4          NUMERIC
                                              , mes_5          NUMERIC
                                              , mes_6          NUMERIC
                                              , mes_7          NUMERIC
                                              , mes_8          NUMERIC
                                              , mes_9          NUMERIC
                                              , mes_10         NUMERIC
                                              , mes_11         NUMERIC
                                              , mes_12         NUMERIC
                                              , total          NUMERIC ); ';
        EXECUTE stSql;
        
        -- Calculando os valores referente a conta 3.1.90.92.00.00  Nível 6
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 11 AS ordem
                      , cod_conta
                      , ''Despesas exercícios anteriores'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.92'', 6, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;
   
        -- Valores a serem conferidos
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                VALUES ( 12 
                        , 0 
                        , ''Correspondente ao Período de Apuração/Móvel''
                        , ''8.8.8.8.88.88.88.88.88''
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                       )';
        EXECUTE stSql;
        
        -- Valores a serem conferidos
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                VALUES ( 13
                        , 0 
                        , ''Competência de Período Anterior ao da Apuração/Móvel''
                        , ''0.0.0.0.00.00.00.00.00''
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                       )';
        EXECUTE stSql;
 
    ------ EXCLUSÕES 
    ELSIF inTipoDados = 2 THEN
        
        -- Calculando os valores referente a conta 3.1.90.94.00.00  Nível 6
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                 SELECT 1 AS ordem
                      , cod_conta
                      , ''Indenização por demissão'' AS nom_conta
                      , cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.94'', 6, '||quote_literal(stTipoSituacao)||')
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC );';
        EXECUTE stSql;
        
        -- Valores a serem conferidos
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                VALUES ( 2
                        , 0 
                        , ''Incentivos a Demissão voluntária''
                        , ''0.0.0.0.00.00.00.00.00''
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                       )';
        EXECUTE stSql;
        
         -- Valores a serem conferidos
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                VALUES ( 3
                        , 0 
                        , ''Sentenças Judiciárias Anteriores''
                        , ''0.0.0.0.00.00.00.00.00''
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                       )';
        EXECUTE stSql;
        
        -- Calculando os valores referente a conta 3.1.90.01.01 + 3.1.90.03.01 da UG RPPS  Nível 7
        stSql := '
            INSERT INTO tmp_tcemg_despesa_total_pessoal
             SELECT 4 AS ordem
                  , ''9999'' AS cod_conta
                  , ''Inativos e Pensionistas com Fonte de Custeio Própria'' AS nom_conta
                  , ''9.9.9.9.99.99.99.99.99'' cod_estrutural
                  , SUM( mes_1 ) AS mes_1
                  , SUM( mes_2 ) AS mes_2
                  , SUM( mes_3 ) AS mes_3
                  , SUM( mes_4 ) AS mes_4
                  , SUM( mes_5 ) AS mes_5
                  , SUM( mes_6 ) AS mes_6
                  , SUM( mes_7 ) AS mes_7
                  , SUM( mes_8 ) AS mes_8
                  , SUM( mes_9 ) AS mes_9
                  , SUM( mes_10 ) AS mes_10
                  , SUM( mes_11 ) AS mes_11
                  , SUM( mes_12 ) AS mes_12
                  , SUM( total ) AS total
             FROM (
                    -- Calculando os valores referente a conta 3.1.90.01.01  Nível 7
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','''||
                                                                                                                                (SELECT valor FROM administracao.configuracao
                                                                                                                                              WHERE cod_modulo = 8 
														                              AND parametro ilike 'cod_entidade_rpps'
                                                                                                                     AND exercicio = stExercicio)||''',''3.3.1.9.0.01.01'',7,'
                                                                                                                     ||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
                
                UNION
                
                    -- Calculando os valores referente a conta 3.1.90.03.01  Nível 7
                    SELECT mes_1
                         , mes_2
                         , mes_3
                         , mes_4
                         , mes_5
                         , mes_6
                         , mes_7
                         , mes_8
                         , mes_9
                         , mes_10
                         , mes_11
                         , mes_12
                         , total
                      FROM tcemg.sub_consulta_despesa_total_pessoal('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','''
                                                                                                                    ||(select valor from administracao.configuracao
                                                                                                                        WHERE cod_modulo = 8 
														     AND parametro ilike 'cod_entidade_rpps'
                                                                                                                     AND exercicio = stExercicio)||''',''3.3.1.9.0.03.01'', 7, '||quote_literal(stTipoSituacao)||')
                        AS retorno ( cod_conta      VARCHAR
                                   , nom_conta      VARCHAR
                                   , cod_estrutural VARCHAR
                                   , mes_1          NUMERIC
                                   , mes_2          NUMERIC
                                   , mes_3          NUMERIC
                                   , mes_4          NUMERIC
                                   , mes_5          NUMERIC
                                   , mes_6          NUMERIC
                                   , mes_7          NUMERIC
                                   , mes_8          NUMERIC
                                   , mes_9          NUMERIC
                                   , mes_10         NUMERIC
                                   , mes_11         NUMERIC
                                   , mes_12         NUMERIC
                                   , total          NUMERIC )
                ) AS Inativos_pensionistas ';
                
        EXECUTE stSql;

         -- Valores a serem conferidos
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                VALUES ( 5
                       , 0 
                       , ''Correspondente ao Período de Apuração/Móvel''
                       , ''8.8.8.8.88.88.88.88.88''
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       )';
        EXECUTE stSql;
        
        -- Valores a serem conferidos
        stSql := '
                INSERT INTO tmp_tcemg_despesa_total_pessoal
                VALUES ( 6
                       , 0 
                       , ''Competência de Período Anterior ao da Apuração/Móvel''
                       , ''0.0.0.0.00.00.00.00.00''
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       , 0.00
                       )';
        EXECUTE stSql;

    END IF;
    
    stSql := ' SELECT ordem
                    , cod_conta      
                    , nom_conta      
                    , SUBSTRING(REPLACE(cod_estrutural,''.'','''')::VARCHAR, 1, 8 )::VARCHAR AS cod_estrutural
                    , mes_1          
                    , mes_2          
                    , mes_3          
                    , mes_4          
                    , mes_5          
                    , mes_6          
                    , mes_7          
                    , mes_8          
                    , mes_9          
                    , mes_10         
                    , mes_11         
                    , mes_12         
                    , total
                 FROM tmp_tcemg_despesa_total_pessoal
             ORDER BY ordem ASC;';
    
    FOR reReg IN EXECUTE stSql
    LOOP
	RETURN NEXT reReg;	
    END LOOP;
	
    DROP TABLE tmp_tcemg_despesa_total_pessoal;
    DROP TABLE tmp_vencimentos_vantagens_1;
    DROP TABLE tmp_vencimentos_vantagens_2;
    DROP TABLE tmp_valor_despesa;
    
    RETURN;
END;
$$ LANGUAGE 'plpgsql';
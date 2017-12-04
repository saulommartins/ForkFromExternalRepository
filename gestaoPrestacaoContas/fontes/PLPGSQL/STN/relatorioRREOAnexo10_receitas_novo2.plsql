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
/**
    * Relatório RREO Anexo 10 Versão 7 (2008) - Receitas.
    * Data de Criação: 02/05/2008


    * @author Leopoldo Braga Barreiro

    * Casos de uso: 

    $Id: relatorioRREOAnexo10_receitas.plsql 46943 2012-06-29 12:10:50Z tonismar $

*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo10_receitas_novo(stExercicio VARCHAR, stEntidades VARCHAR, stOpcao VARCHAR, stDtIni VARCHAR, stDtFim VARCHAR) RETURNS SETOF RECORD AS 
$$

DECLARE 

    stDtIniExercicio     VARCHAR := '';	
    --stDtIni 	         VARCHAR := '';
    --stDtFim 	         VARCHAR := '';
    --arDatas 	         VARCHAR[];
    stContasConfiguracao VARCHAR := '';
    stInsertConfiguracao1 VARCHAR := '';
    stInsertConfiguracao2 VARCHAR := '';
    stInsertConfiguracao3 VARCHAR := '';
    stInsertConfiguracao4 VARCHAR := '';
    stInsertConfiguracao5 VARCHAR := '';
    stOperacao 	         CHARACTER(1);
    stSQL 		         VARCHAR := '';
    stSQLgrupo	         VARCHAR := '';
    stSQLsubgrupo	     VARCHAR := '';
    stSQLaux	         VARCHAR := '';
    reReg		         RECORD;
    reReggrupo 	         RECORD;
    reRegSubgrupo        RECORD;
	
BEGIN 

    --arDatas := publico.bimestre ( stExercicio, inBimestre #000000);
    --stDtIni := arDatas[0];
    --stDtFim := arDatas[1];
    
	-- Definicao de Datas conforme Bimestre selecionado
	stDtIniExercicio := '01/01/' || stExercicio;
	
	stSQL := '
	CREATE TEMPORARY TABLE tmp_valor AS (
	SELECT
		ocr.cod_estrutural as cod_estrutural , 
		lote.dt_lote       as data , 
		vl.vl_lancamento   as valor , 
		vl.oid             as primeira 
	FROM
		contabilidade.valor_lancamento      as vl   ,
		orcamento.conta_receita             as ocr  ,
		orcamento.receita                   as ore  ,
		contabilidade.lancamento_receita    as lr   ,
		contabilidade.lancamento            as lan  ,
		contabilidade.lote                  as lote
	WHERE
		ore.exercicio       = ''' || stExercicio || ''' 
		
		AND ore.cod_entidade    IN (' || stEntidades || ') 

		AND ocr.cod_conta       = ore.cod_conta
		AND ocr.exercicio       = ore.exercicio

		-- join lancamento receita
		AND lr.cod_receita      = ore.cod_receita
		AND lr.exercicio        = ore.exercicio
		AND lr.estorno          = true
		-- tipo de lancamento receita deve ser = A , de arrecadação
		AND lr.tipo             = ''A''

		-- join nas tabelas lancamento_receita e lancamento
		AND lan.cod_lote        = lr.cod_lote
		AND lan.sequencia       = lr.sequencia
		AND lan.exercicio       = lr.exercicio
		AND lan.cod_entidade    = lr.cod_entidade
		AND lan.tipo            = lr.tipo

		-- join nas tabelas lancamento e valor_lancamento
		AND vl.exercicio        = lan.exercicio
		AND vl.sequencia        = lan.sequencia
		AND vl.cod_entidade     = lan.cod_entidade
		AND vl.cod_lote         = lan.cod_lote
		AND vl.tipo             = lan.tipo
		-- na tabela valor lancamento  tipo_valor deve ser credito
		AND vl.tipo_valor       = ''D''

		AND lote.cod_lote       = lan.cod_lote
		AND lote.cod_entidade   = lan.cod_entidade
		AND lote.exercicio      = lan.exercicio
		AND lote.tipo           = lan.tipo

	UNION

	SELECT
		ocr.cod_estrutural as cod_estrutural , 
		lote.dt_lote       as data , 
		vl.vl_lancamento   as valor , 
		vl.oid             as segunda 
	FROM
		contabilidade.valor_lancamento      as vl   ,
		orcamento.conta_receita             as ocr  ,
		orcamento.receita                   as ore  ,
		contabilidade.lancamento_receita    as lr   ,
		contabilidade.lancamento            as lan  ,
		contabilidade.lote                  as lote

	WHERE
		ore.exercicio       = ''' || stExercicio || ''' 
		AND ore.cod_entidade    IN (' || stEntidades || ') 
		AND ocr.cod_conta       = ore.cod_conta
		AND ocr.exercicio       = ore.exercicio
		-- join lancamento receita
		AND lr.cod_receita      = ore.cod_receita
		AND lr.exercicio        = ore.exercicio
		AND lr.estorno          = false
		-- tipo de lancamento receita deve ser = A , de arrecadação
		AND lr.tipo             = ''A''

		-- join nas tabelas lancamento_receita e lancamento
		AND lan.cod_lote        = lr.cod_lote
		AND lan.sequencia       = lr.sequencia
		AND lan.exercicio       = lr.exercicio
		AND lan.cod_entidade    = lr.cod_entidade
		AND lan.tipo            = lr.tipo

		-- join nas tabelas lancamento e valor_lancamento
		AND vl.exercicio        = lan.exercicio
		AND vl.sequencia        = lan.sequencia
		AND vl.cod_entidade     = lan.cod_entidade
		AND vl.cod_lote         = lan.cod_lote
		AND vl.tipo             = lan.tipo
		-- na tabela valor lancamento  tipo_valor deve ser credito
		AND vl.tipo_valor       = ''C''

		-- Data Inicial e Data Final, antes iguala codigo do lote
		AND lote.cod_lote       = lan.cod_lote
		AND lote.cod_entidade   = lan.cod_entidade
		AND lote.exercicio      = lan.exercicio
		AND lote.tipo           = lan.tipo
	) 	
	';

	EXECUTE stSQL;

	-- -------------------------------------	
	-- Estrutura de Tabelas Temporarias
	-- -------------------------------------

	-- Tabela tmp_rreo_an10_receita
	-- Formatação e Cálculos Agrupados para Exibição de Resultados

	stSQL := '
	CREATE TEMPORARY TABLE tmp_rreo_an10_receita (
		grupo INTEGER DEFAULT 0 , 
		subgrupo INTEGER DEFAULT 0 , 
		item INTEGER DEFAULT 0 , 
		descricao VARCHAR(150) DEFAULT NULL , 
		ini NUMERIC(14,2) DEFAULT 0.00 , 
		atu NUMERIC(14,2) DEFAULT 0.00 , 
		no_bi NUMERIC(14,2) DEFAULT 0.00 , 
		ate_bi NUMERIC(14,2) DEFAULT 0.00 , 
		pct NUMERIC(14,2) DEFAULT 0.00 
	) ; 
	
	CREATE INDEX idx_rreo_an10_receita ON 
		tmp_rreo_an10_receita (descricao) ;	
	';
	
	EXECUTE stSQL ; 	
	
	-- -------------------------------------
	-- Fim Estrutura de Tabelas Temporarias
	-- -------------------------------------	
	
	-- -------------------------------------
	-- Opcao de Relatorio
	-- -------------------------------------	
	
	-- Inicio Opcao 'receitas_ensino'
	
	IF stOpcao = 'receitas_ensino' THEN 
        stContasConfiguracao := array_to_string(ARRAY(SELECT cod_estrutural
                 FROM stn.conta_dedutora_tributos
           INNER JOIN orcamento.receita
                   ON conta_dedutora_tributos.cod_receita = receita.cod_receita
                  AND conta_dedutora_tributos.exercicio = receita.exercicio
           INNER JOIN orcamento.conta_receita
                   ON conta_receita.cod_conta = receita.cod_conta
                  AND conta_receita.exercicio = receita.exercicio
                WHERE cod_tributo = 1 -- 1 = Deduções de Receita do IPTU
                  AND timestamp = (SELECT MAX(timestamp)
                                               FROM stn.conta_dedutora_tributos t2
                                              WHERE t2.exercicio = receita.exercicio)), '|') 
        ;

        IF stContasConfiguracao = '' OR stContasConfiguracao IS NULL THEN
            stInsertConfiguracao1 := ' INSERT INTO tmp_rreo_an10_receita VALUES (1,1,5,''(-) Deduções da Receita do IPTU'', 0.00,0.00,0.00,0.00,0.00); ';
        ELSE
            stInsertConfiguracao1 := ' INSERT INTO tmp_rreo_an10_receita
            SELECT grupo, subgrupo, item, ''(-) Deduções da Receita do IPTU'' as descricao, SUM(ini) as ini, SUM(atu) as atu, SUM(no_bi) as no_bi, SUM(ate_bi) as ate_bi, SUM(pct) as pct
            FROM stn.fn_rreo_valor_conta(
                    ''' || stExercicio || ''',
                    ''R'',
                    '''||stContasConfiguracao||''' ,
                    ''' || stEntidades || ''',
                    '''||stDtIni||''','''|| stDtFim||''',
                    true,
                    1,
                    1,
                    5
            )
            GROUP BY grupo
                   , subgrupo
                   , item; ';
        END IF;

        stContasConfiguracao := array_to_string(ARRAY(SELECT cod_estrutural
                 FROM stn.conta_dedutora_tributos
           INNER JOIN orcamento.receita
                   ON conta_dedutora_tributos.cod_receita = receita.cod_receita
                  AND conta_dedutora_tributos.exercicio = receita.exercicio
           INNER JOIN orcamento.conta_receita
                   ON conta_receita.cod_conta = receita.cod_conta
                  AND conta_receita.exercicio = receita.exercicio
                WHERE cod_tributo = 2
                  AND timestamp = (SELECT MAX(timestamp)
                                               FROM stn.conta_dedutora_tributos t2
                                              WHERE t2.exercicio = receita.exercicio)), '|') -- 2 = Deduções de Receita do ITBI
        ;

        IF stContasConfiguracao = '' OR stContasConfiguracao IS NULL THEN
            stInsertConfiguracao2 := ' INSERT INTO tmp_rreo_an10_receita VALUES (1,2,5,''(-) Deduções da Receita do ITBI'', 0.00,0.00,0.00,0.00,0.00); ';
        ELSE
            stInsertConfiguracao2 := ' INSERT INTO tmp_rreo_an10_receita
            SELECT grupo, subgrupo, item, ''(-) Deduções da Receita do ITBI'' as descricao, SUM(ini) as ini, SUM(atu) as atu, SUM(no_bi) as no_bi, SUM(ate_bi) as ate_bi, SUM(pct) as pct
            FROM stn.fn_rreo_valor_conta(
                    ''' || stExercicio || ''',
                    ''R'',
                    '''||stContasConfiguracao||''' ,
                    ''' || stEntidades || ''',
                    '''||stDtIni||''','''|| stDtFim||''',
                    true,
                    1,
                    2,
                    5
            )
            GROUP BY grupo
                   , subgrupo
                   , item; ';
        END IF;

        stContasConfiguracao := array_to_string(ARRAY(SELECT cod_estrutural
                 FROM stn.conta_dedutora_tributos
           INNER JOIN orcamento.receita
                   ON conta_dedutora_tributos.cod_receita = receita.cod_receita
                  AND conta_dedutora_tributos.exercicio = receita.exercicio
           INNER JOIN orcamento.conta_receita
                   ON conta_receita.cod_conta = receita.cod_conta
                  AND conta_receita.exercicio = receita.exercicio
                WHERE cod_tributo = 3
                  AND timestamp = (SELECT MAX(timestamp)
                                               FROM stn.conta_dedutora_tributos t2
                                              WHERE t2.exercicio = receita.exercicio)), '|') -- 3 = Deduções de Receita do IRRF
        ;

        IF stContasConfiguracao = '' OR stContasConfiguracao IS NULL THEN
            stInsertConfiguracao3 := ' INSERT INTO tmp_rreo_an10_receita VALUES (1,3,5,''(-) Deduções da Receita do ISS'', 0.00,0.00,0.00,0.00,0.00); ';
        ELSE
            stInsertConfiguracao3 := ' INSERT INTO tmp_rreo_an10_receita
            SELECT grupo, subgrupo, item, ''(-) Deduções da Receita do ISS'' as descricao, SUM(ini) as ini, SUM(atu) as atu, SUM(no_bi) as no_bi, SUM(ate_bi) as ate_bi, SUM(pct) as pct
            FROM stn.fn_rreo_valor_conta(
                    ''' || stExercicio || ''',
                    ''R'',
                    '''||stContasConfiguracao||''' ,
                    ''' || stEntidades || ''',
                    '''||stDtIni||''','''|| stDtFim||''',
                    true,
                    1,
                    3,
                    5
            )
            GROUP BY grupo
                   , subgrupo
                   , item; ';
        END IF;

        stContasConfiguracao := array_to_string(ARRAY(SELECT cod_estrutural
                 FROM stn.conta_dedutora_tributos
           INNER JOIN orcamento.receita
                   ON conta_dedutora_tributos.cod_receita = receita.cod_receita
                  AND conta_dedutora_tributos.exercicio = receita.exercicio
           INNER JOIN orcamento.conta_receita
                   ON conta_receita.cod_conta = receita.cod_conta
                  AND conta_receita.exercicio = receita.exercicio
                WHERE cod_tributo = 4
                  AND timestamp = (SELECT MAX(timestamp)
                                               FROM stn.conta_dedutora_tributos t2
                                              WHERE t2.exercicio = receita.exercicio)), '|') -- 4 = Deduções de Receita do IRRF
        ;

        IF stContasConfiguracao = '' OR stContasConfiguracao IS NULL THEN
            stInsertConfiguracao4 := ' INSERT INTO tmp_rreo_an10_receita VALUES (1,4,5,''(-) Deduções da Receita do IRRF'', 0.00,0.00,0.00,0.00,0.00); ';
        ELSE
            stInsertConfiguracao4 := ' INSERT INTO tmp_rreo_an10_receita
            SELECT grupo, subgrupo, item, ''(-) Deduções da Receita do IRRF'' as descricao, SUM(ini) as ini, SUM(atu) as atu, SUM(no_bi) as no_bi, SUM(ate_bi) as ate_bi, SUM(pct) as pct
            FROM stn.fn_rreo_valor_conta(
                    ''' || stExercicio || ''',
                    ''R'',
                    '''||stContasConfiguracao||''' ,
                    ''' || stEntidades || ''',
                    '''||stDtIni||''','''|| stDtFim||''',
                    true,
                    1,
                    4,
                    5
            )
            GROUP BY grupo
                   , subgrupo
                   , item; ';
        END IF; 

        stContasConfiguracao := array_to_string(ARRAY(SELECT cod_estrutural
                 FROM stn.conta_dedutora_tributos
           INNER JOIN orcamento.receita
                   ON conta_dedutora_tributos.cod_receita = receita.cod_receita
                  AND conta_dedutora_tributos.exercicio = receita.exercicio
           INNER JOIN orcamento.conta_receita
                   ON conta_receita.cod_conta = receita.cod_conta
                  AND conta_receita.exercicio = receita.exercicio
                WHERE cod_tributo = 5
                  AND timestamp = (SELECT MAX(timestamp)
                                               FROM stn.conta_dedutora_tributos t2
                                              WHERE t2.exercicio = receita.exercicio)), '|') -- 5 = Deduções de Receita do ITR
        ;

        IF stContasConfiguracao = '' OR stContasConfiguracao IS NULL THEN
            stInsertConfiguracao5 := ' INSERT INTO tmp_rreo_an10_receita VALUES (1,5,5,''(-) Deduções da Receita do ITR'', 0.00,0.00,0.00,0.00,0.00); ';
        ELSE
            stInsertConfiguracao5 := ' INSERT INTO tmp_rreo_an10_receita
            SELECT grupo, subgrupo, item, ''(-) Deduções da Receita do ITR'' as descricao, SUM(ini) as ini, SUM(atu) as atu, SUM(no_bi) as no_bi, SUM(ate_bi) as ate_bi, SUM(pct) as pct
            FROM stn.fn_rreo_valor_conta(
                    ''' || stExercicio || ''',
                    ''R'',
                    '''||stContasConfiguracao||''' ,
                    ''' || stEntidades || ''',
                    '''||stDtIni||''','''|| stDtFim||''',
                    true,
                    1,
                    5,
                    5
            )
            GROUP BY grupo
                   , subgrupo
                   , item; ';
        END IF;
	
		-- Tabela de itens de Planilha Padrao
		
		stSQL := '
		INSERT INTO tmp_rreo_an10_receita VALUES (1, 0, 0, ''RECEITAS DE IMPOSTOS'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

		-- 1. RECEITAS DE IMPOSTOS
		
		-- 1.1. Receita Resultante do Imposto Sobre a Propriedade Predial e Territorial Urbana - IPTU 
		
		INSERT INTO tmp_rreo_an10_receita VALUES (1, 1, 0, ''Receita Resultante do Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		
		-- 1.1.1 - Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU
		-- 1.1.1.2.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''IPTU'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.2.02.00.00.00.00'', ''' || stEntidades || ''',  '''||stDtIni||''','''|| stDtFim||''', true, 1, 1, 1) ;
		
		-- 1.1.2 - Multas, Juros de Mora e Outros Encargos do IPTU
		-- 1.9.1.1.38.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multa, Juros de Mora e Outros Encargos do IPTU'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.1.38.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 1, 2) ;

		-- 1.1.3 - Dívida Ativa do IPTU
		-- 1.9.3.1.11.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Dívida Ativa do IPTU'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.3.1.11.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 1, 3) ;

		-- 1.1.4 - Multas, Juros de Mora, Atualização Monetária e Outros Encargos da Dívida Ativa do IPTU
		-- 1.9.1.3.11.00.00.00.00

		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multas, Juros de Mora, Atualização Montária e Outros Encargos da Dívida Ativa do IPTU'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.3.11.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 1, 4) ;

        -- 1.1.5 - (-) Deduções da Receita do IPTU
        -- pega contas da configuração
        '||stInsertConfiguracao1||'

		
		-- 1.2. Receita Resultante do Imposto sobre Transmissão Inter Vivos - ITBI
		
		INSERT INTO tmp_rreo_an10_receita VALUES (1, 2, 0, ''Receita Resultante do Imposto sobre Transmissão Inter Vivos - ITBI'', 0.00, 0.00, 0.00, 0.00, 0.00) ;


		-- 1.2.1 - ITBI
		-- 1.1.1.2.08.00.00.00.00
			
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''ITBI'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.2.08.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 2, 1) ;


		-- 1.2.2 - Multas, Juros de Mora e Outros Encargos do ITBI
		-- 1.9.1.1.39.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multas, Juros de Mora e Outros Encargos do ITBI'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.1.39.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 2, 2) ;
		
		-- 1.2.3 - Dívida Ativa do ITBI
		-- 1.9.3.1.12.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Dívida Ativa do ITBI'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.3.1.12.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 2, 3) ;
		
		-- 1.2.4 - Multas, Juros de Mora, Atualização Monetária e Outros Encargos da Dívida Ativa do ITBI
		-- 1.9.1.3.12.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multas, Juros de Mora, Atualização Monetária e Outros Encargos da Dívida Ativa do ITBI'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.3.12.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 2, 4) ;

        -- 1.2.5 - (-) Deduções da Receita do ITBI
        -- pega contas da configuração
        '||stInsertConfiguracao2||'

		-- 1.3 - Receita Resultante do Imposto sobre Serviços de Qualquer Natureza - ISS 
		
		INSERT INTO tmp_rreo_an10_receita VALUES (1, 3, 0, ''Receita Resultante do Imposto sobre Serviços de Qualquer Natureza - ISS'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		
		-- 1.3.1 - ISS
		-- 1.1.1.3.05.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''ISS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.3.05.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 3, 1) ;
		
		-- 1.3.2 - Multas, Juros de Mora e Outros Encargos do ISS
		-- 1.9.1.1.40.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multas, Juros de Mora e Outros Encargos do ISS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.1.40.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 3, 2) ;
		
		-- 1.3.3 - Dívida Ativa do ISS
		-- 1.9.3.1.13.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Dívida Ativa do ISS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.3.1.13.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 3, 3) ;
		
		-- 1.3.4 - Multas, Juros de Mora, Atualização Monetária e Outros Encargos da Dívida Ativa do ISS
		-- 1.9.1.3.13.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multas, Juros de Mora, Atualização Monetária e Outros Encargos da Dívida Ativa do ISS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.3.13.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 3, 4) ;

        -- 1.3.5 - (-) Deduções da Receita do ISS
        -- pega contas da configuração
        '||stInsertConfiguracao3||'

		-- 1.4 - Receita Resultante do Imposto de Renda Retido na Fonte - IRRF
		
		INSERT INTO tmp_rreo_an10_receita VALUES (1, 4, 0, ''Receita Resultante do Imposto de Renda Retido na Fonte - IRRF'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		
		-- 1.4.1 - Imposto de Renda Retido na Fonte - IRRF
		-- 1.1.1.2.04.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''IRRF'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.2.04.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 4, 1) ;
		
		-- 1.4.2 - Multas, Juros de Mora e Outros Encargos do IRRF
		-- 1.9.1.1.02.03.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multas, Juros de Mora e Outros Encargos do IRRF'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.1.02.03.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 4, 2) ;

		-- 1.4.3 - Dívida Ativa do IRRF
		-- 1.9.3.1.01.03.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Dívida Ativa do IRRF'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.3.1.01.03.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 4, 3) ;
		
		-- 1.4.4 - Multas, Juros de Mora,  Atualização Monetária e Outros Encargos da Dívida Ativa do IRRF
		-- 1.9.1.3.02.03.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Multas, Juros de Mora,  Atualização Monetária e Outros Encargos da Dívida Ativa do IRRF'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.3.02.03.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 4, 4) ;

        -- 1.4.5 - (-) Deduções da Receita do IRRF
        -- pega contas da configuração
        '||stInsertConfiguracao4||'

        -- 1.5 - Receita Resultante de Imposto Territorial Rural - ITR (CF, art. 153, §4º, inciso III)
        INSERT INTO tmp_rreo_an10_receita VALUES (1, 5, 0, ''Receita Resultante de Imposto Territorial Rural - ITR (CF, art. 153, §4º, inciso III)'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

        -- 1.5.1 - ITR
        -- não possui contas no sistema
        INSERT INTO tmp_rreo_an10_receita VALUES (1, 5, 1, ''ITR'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

        -- 1.5.2 - Multas, Juros de Mora e Outros Encargos do ITR
        -- não possui contas no sistema
        INSERT INTO tmp_rreo_an10_receita VALUES (1, 5, 2, ''Multas, Juros de Mora e Outros Encargos do ITR'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

        -- 1.5.3 - Dívida Ativa do ITR
        -- não possui contas no sistema
        INSERT INTO tmp_rreo_an10_receita VALUES (1, 5, 3, ''Dívida Ativa do ITR'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

        -- 1.5.4 - Multas, Juros de Mora, Atualização Monetária e Outros Encargos da Dívida Ativa do ITR
        -- não possui contas no sistema
        INSERT INTO tmp_rreo_an10_receita VALUES (1, 5, 4, ''Multas, Juros de Mora, Atualização Monetária e Outros Encargos da Dívida Ativa do ITR'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

        -- 1.5.5 - (-) Deduções da Receita do ITR
        -- pega contas da configuração
        '||stInsertConfiguracao5||'

--		-- 1.5 - Deduções da Receita Tributária
--		
--		INSERT INTO tmp_rreo_an10_receita VALUES (1, 5, 0, ''Deduções da Receita Tributária'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
--
--		-- 1.5.1 - Deducao da receita 
--		-- 9.1.1.00.00.00.00.00.00
--		
--		INSERT INTO tmp_rreo_an10_receita 
--		SELECT grupo, subgrupo, item, ''ITR'' as descricao, (ini*-1), (atu*-1), (no_bi*-1), (ate_bi*-1), pct 
-- 		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''9.1.1.0.0.00.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 1, 5, 1) ;
		
		-- 2 - RECEITAS DE TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS
		
		INSERT INTO tmp_rreo_an10_receita VALUES (2, 0, 0, ''RECEITAS DE TRANSFERÊNCIAS CONSTITUCIONAIS E LEGAIS'');

		-- 2.1 - Cota-Parte FPM

        INSERT INTO tmp_rreo_an10_receita VALUES (2, 1, 0, ''Cota-Parte FPM'');
	
        -- 2.1.1 - Parcela referente à CF, art. 159, I, alínea b
        -- 1.7.2.1.01.02.00.00.00
        INSERT INTO tmp_rreo_an10_receita VALUES (2, 1, 1, ''Parcela referente à CF, art. 159, I, alínea b'');
        
        UPDATE tmp_rreo_an10_receita SET ini = tbl.ini, atu = tbl.atu, no_bi = tbl.no_bi, ate_bi = tbl.ate_bi, pct = tbl.pct 
        FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.01.02.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 2, 1, 1) tbl
        WHERE tmp_rreo_an10_receita.grupo = tbl.grupo
        AND tmp_rreo_an10_receita.subgrupo = tbl.subgrupo
        AND tmp_rreo_an10_receita.item = tbl.item;

        -- 2.1.2 - Parcela referente à CF, art. 159, I, alínea d
        -- 1.7.2.1.01.02.07.00.00   
        INSERT INTO tmp_rreo_an10_receita VALUES (2, 1, 2, ''Parcela referente à CF, art. 159, I, alínea d'');

        UPDATE tmp_rreo_an10_receita SET ini = tbl.ini, atu = tbl.atu, no_bi = tbl.no_bi, ate_bi = tbl.ate_bi, pct = tbl.pct 
        FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.01.02.07.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 2, 1, 2) tbl
        WHERE tmp_rreo_an10_receita.grupo = tbl.grupo
        AND tmp_rreo_an10_receita.subgrupo = tbl.subgrupo
        AND tmp_rreo_an10_receita.item = tbl.item;
		
		-- 2.2 - Cota-Parte ICMS
		-- 1.7.2.2.01.01.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Cota-Parte ICMS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.2.01.01.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 2, 2, 0) ;
		
		-- 2.3. ICMS-Desoneração - L.C. no87/1996
		-- 1.7.2.1.36.00.00.00.00

		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''ICMS-Desoneração - L.C. no87/1996'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.36.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 2, 3, 0) ;

		-- 2.4 - Cota-Parte IPI-Exportação
		-- 1.7.2.2.01.04.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Cota-Parte IPI-Exportação'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.2.01.04.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 2, 4, 0) ;
		
		-- 2.5 - Cota-Parte ITR
		-- 1.7.2.1.01.05.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Cota-Parte ITR'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.01.05.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 2, 5, 0) ;
		
		-- 2.6 - Cota-Parte IPVA
		-- 1.7.2.2.01.02.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, ''Cota-Parte IPVA'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.2.01.02.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 2, 6, 0) ;

		-- 2.7 - Cota-Parte IOF-Ouro
		-- Falta o cod_estrutural desta conta receita
		
		INSERT INTO tmp_rreo_an10_receita VALUES (2, 7, 0, ''Cota-Parte IOF-Ouro'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

		
		-- 3. TOTAL DA RECEITA BRUTA DE IMPOSTOS (1 + 2)
		--
		--INSERT INTO tmp_rreo_an10_receita VALUES (3, 0, 0, ''TOTAL DA RECEITA BRUTA DE IMPOSTOS'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

		';
		
		EXECUTE stSQL;
	
		-- 
		-- publico.fn_mascarareduzida(''1.9.1.1.39.00.01.00.00'')
		-- 
		
	-- ---------------------------------------
	-- Fim Opcao 'receitas_ensino'
	-- ---------------------------------------
	
	-- ---------------------------------------
	-- Inicio Opcao 'outras_receitas_ensino'
	-- ---------------------------------------
	
	ELSEIF stOpcao = 'outras_receitas_ensino' THEN 

		stSQL := '
		
		-- 4. RECEITA DA APLICAÇÃO FINANCEIRA DE OUTROS RECURSOS DE IMPOSTOS VINCULADOS AO ENSINO
		
		INSERT INTO tmp_rreo_an10_receita VALUES (4, 0, 0, ''RECEITA DA APLICAÇÃO FINANCEIRA DE OUTROS RECURSOS DE IMPOSTOS VINCULADOS AO ENSINO'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
	
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.3.2.5.01.05.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 4, 1, 1) ;


		-- 5. RECEITA DE TRANSFERÊNCIAS DO FNDE
		
		INSERT INTO tmp_rreo_an10_receita VALUES (5, 0, 0, ''RECEITA DE TRANSFERÊNCIAS DO FNDE'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		
		-- 5.1. Transferências do Salário-Educação
                
                /*
                1.7.2.1.35.01.00.00.00
                1.7.2.1.35.02.00.00.00
                1.7.2.1.35.03.00.00.00
                1.7.2.1.35.04.00.00.00
                */

                INSERT INTO tmp_rreo_an10_receita VALUES (5, 1, 0, ''Transferências do Salário-Educação'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
                
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.35.01.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 5, 1, 1) ;

		-- 5.2. Outras Transferências do FNDE
                /*
                1.7.2.1.35.01.00.00.00
                1.7.2.1.35.02.00.00.00
                1.7.2.1.35.03.00.00.00
                1.7.2.1.35.04.00.00.00
                */
                
                INSERT INTO tmp_rreo_an10_receita VALUES (5, 2, 0, ''RECEITA DE TRANSFERÊNCIAS DO FNDE'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
                
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.35.02.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 5, 2, 1) ;
                
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.35.03.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 5, 2, 2) ;
                
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.35.04.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 5, 2, 3) ;
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.35.99.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 5, 2, 4) ;
		
		-- 6. TRANSFERÊNCIAS DE CONVÊNIOS DESTINADAS A PROGRAMAS DE EDUCAÇÃO
                
		INSERT INTO tmp_rreo_an10_receita VALUES (6, 0, 0, ''RECEITA DE TRANSFERÊNCIAS DE CONVÊNIOS'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		
		-- 1.7.6.1.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.6.1.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 1, 0) ;
		
		-- 1.7.6.2.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.6.2.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 2, 0) ;
		
		-- 1.7.6.3.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.6.3.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 3, 0) ;
		
		-- 2.4.2.1.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.4.2.1.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 4, 0) ;

		-- 2.4.2.2.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.4.2.2.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 5, 0) ;

		-- 2.4.2.3.02.00.00.00.00

		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.4.2.3.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 6, 0) ;
		
		-- 2.4.7.1.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.4.7.1.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 7, 0) ;

		-- 2.4.7.2.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.4.7.2.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 8, 0) ;

		-- 2.4.7.3.02.00.00.00.00
		
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.4.7.3.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 6, 9, 0) ;
		
		-- 7. RECEITA DE OPERAÇÕES DE CRÉDITO DESTINADA À EDUCAÇÃO
               
                INSERT INTO tmp_rreo_an10_receita VALUES (7, 0, 0, ''RECEITA DE OPERAÇÕES DE CRÉDITO'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
        
                -- 2.1.1.4.01.00.00.00.00 | OPERACOES DE CREDITO INTERNAS PARA PROGRAMAS DE EDUCACAO
                -- 2.1.2.3.01.00.00.00.00 | OPERACOES DE CREDITO EXTERNAS PARA PROGRAMAS DE EDUCACAO
           
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.1.1.4.01.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 7, 1, 0) ;
        
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''2.1.2.3.01.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 7, 2, 0) ;
		
		-- 8. OUTRAS RECEITAS DESTINADAS À EDUCAÇÃO
		
		INSERT INTO tmp_rreo_an10_receita VALUES (8, 0, 0, ''OUTRAS RECEITAS PARA FINANCIAMENTO DO ENSINO'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

                -- 1.3.2.5.01.05.00.00.00 | RECEITA DE REMUNERACAO DE DEPOSITOS  BANCARIOS DE RECURSOS VINCULADOS - MANUTENCAO E DESENVOLVIMENTO DO ENSINO - MDE
                -- 1.3.2.5.01.11.00.00.00 | RECEITA DE REMUNERACAO DE DEPOSITOS  BANCARIOS DE RECURSOS DO FNDE

		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.3.2.5.01.05.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 8, 1, 0) ;
        
                INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.3.2.5.01.11.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 8, 2, 0) ;
		
		-- 8. TOTAL DAS OUTRAS RECEITAS DESTINADAS À EDUCAÇÃO
		-- INSERT INTO tmp_rreo_an10_receita VALUES (8, 0, 0, ''TOTAL DAS OUTRAS RECEITAS DESTINADAS AO ENSINO (4 + 5 + 6 + 7)'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		';
		
		EXECUTE stSQL;
		
	-- ---------------------------------------
	-- Fim Opcao 'outras_receitas_ensino'
	-- ---------------------------------------
	
	-- ---------------------------------------
	-- Inicio Opcao 'receitas_fundeb'
	-- ---------------------------------------
	
	ELSEIF stOpcao = 'receitas_fundeb' THEN 

		stSQL := '
		
		-- 11. RECEITAS DESTINADAS AO FUNDEB
		INSERT INTO tmp_rreo_an10_receita VALUES (11, 0, 0, ''RECEITAS DESTINADAS AO FUNDEB'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		
		-- 11.1- Cota-Parte FPM Destinada ao FUNDEB - (18,33% de 2.1)
                INSERT INTO tmp_rreo_an10_receita VALUES (11, 1, 0, ''Cota-Parte FPM Destinada ao FUNDEB - (20% de 2.1.1)'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

                -- 9.1.7.2.1.01.02.06.00.00 | (R) DEDUÇÃO DA RECEITA PARA FORMAÇÃO DO FUNDEB - FPM
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''9.1.7.2.1.01.02.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 11, 1, 1) ;
		
		-- 11.2- Cota-Parte ICMS Destinada ao FUNDEB - (18,33% de 2.2)
                INSERT INTO tmp_rreo_an10_receita VALUES (11, 2, 0, ''Cota-Parte ICMS Destinada ao FUNDEB - (20% de 2.2)'', 0.00, 0.00, 0.00, 0.00, 0.00) ;

                -- 9.1.7.2.2.01.01.05.00.00 | (R) DEDUÇÃO DA RECEITA PARA FORMACAO DO FUNDEB - ICMS
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''9.1.7.2.2.01.01.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 11, 2, 1) ;
		
		-- 11.3- ICMS-Desoneração Destinada ao FUNDEB - (18,33% de 2.3)
                INSERT INTO tmp_rreo_an10_receita VALUES (11, 3, 0, ''ICMS-Desoneração Destinada ao FUNDEB - (20% de 2.3)'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
        
                -- 9.1.7.2.1.36.00.05.00.00 | (R) DEDUÇÃO DA RECEITA PARA FORMACAO DO FUNDEB - ICMS DESONERACAO - LEI COMPLEMENTAR 87/96
                INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''9.1.7.2.1.36.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 11, 3, 1) ;
		
		-- 11.4- Cota-Parte IPI-Exportação Destinada ao FUNDEB - (18,33% de 2.4)
                INSERT INTO tmp_rreo_an10_receita VALUES (11, 4, 0, ''Cota-Parte IPI-Exportação Destinada ao FUNDEB - (20% de 2.4)'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
		
                -- 9.1.7.2.2.01.04.05.00.00 | (R) DEDUÇÃO DA RECEITA PARA FORMACAO DO FUNDEB - IPI/EXPORTACAO
                INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''9.1.7.2.2.01.04.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 11, 4, 1) ;
        
		-- 11.5- Cota-Parte ITR Destinada ao FUNDEB - (13,33% de 2.5)
                INSERT INTO tmp_rreo_an10_receita VALUES (11, 5, 0, ''Cota-Parte ITR ou ITR Arrecadado Destinados ao FUNDEB - (20% de ((1.5 - 1.5.5) + 2.5))'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
        
                -- 9.1.7.2.1.01.05.04.00.00 | (R) DEDUÇÃO DA RECEITA PARA FORMAÇÃO DO FUNDEB - ITR
                INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''9.1.7.2.1.01.05.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 11, 5, 1) ;
	
		-- 11.6- Cota-Parte IPVA Destinada ao FUNDEB - (13,33% de 2.6)
                INSERT INTO tmp_rreo_an10_receita VALUES (11, 6, 0, ''Cota-Parte IPVA Destinada ao FUNDEB - (20% de 2.6)'', 0.00, 0.00, 0.00, 0.00, 0.00) ;
        
                -- 9.1.7.2.2.01.02.04.00.00 | (R) DEDUÇÃO DA RECEITA PARA FORMACAO DO FUNDEB - IPVA
                INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''9.1.7.2.2.01.02.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 11, 6, 1) ;
		
		-- 12. RECEITAS RECEBIDAS DO FUNDEB
		INSERT INTO tmp_rreo_an10_receita VALUES (12, 0, 0, ''RECEITAS RECEBIDAS DO FUNDEB'', 0.00, 0.00, 0.00, 0.00, 0.00) ; 

		-- 12.1- Transferências de Recursos do FUNDEB
		-- 1.7.2.4.01.00.00.00.00
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.4.01.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 12, 1, 0) ;
		
		-- 12.2- Complementação da União ao FUNDEB
		-- 1.7.2.4.02.00.00.00.00
		INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.4.02.00.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 12, 2, 0) ;
		
		-- 12.3- Receita de Aplicação Financeira dos Recursos do FUNDEB
                -- 1.3.2.5.01.02.00.00.00 | RECEITA DE REMUNERACAO DE DEPOSITOS  BANCARIOS DE RECURSOS VINCULADOS - FUNDEB
                INSERT INTO tmp_rreo_an10_receita 
		SELECT grupo, subgrupo, item, descricao, ini, atu, no_bi, ate_bi, pct 
		FROM stn.fn_rreo_valor_conta(''' || stExercicio || ''', ''R'', ''1.3.2.5.01.02.00.00.00'', ''' || stEntidades || ''', '''||stDtIni||''','''|| stDtFim||''', true, 12, 3, 0) ;
        
		
		-- 13. RESULTADO LÍQUIDO DAS TRANSFERÊNCIAS DO FUNDEB (12.1 - 11)
		-- INSERT INTO tmp_rreo_an10_receita VALUES (11, 0, 0, ''RESULTADO LÍQUIDO DAS TRANSFERÊNCIAS DO FUNDEB (12.1 - 11)'', 0.00, 0.00, 0.00, 0.00, 0.00) ; 

		-- [SE RESULTADO LÍQUIDO DA TRANSFERÊNCIA (11) > 0] = ACRÉSCIMO RESULTANTE DAS TRANSFERÊNCIAS DO FUNDEB
		-- [SE RESULTADO LÍQUIDO DA TRANSFERÊNCIA (11) < 0] = DECRÉSCIMO RESULTANTE DAS TRANSFERÊNCIAS DO FUNDEB	
		';

		EXECUTE stSQL;
	
	-- ---------------------------------------
	-- Fim Opcao 'receitas_fundeb'
	-- ---------------------------------------

	-- ---------------------------------------
	-- Inicio Opcao 'receitas_acoes_tipicas'
	-- ---------------------------------------
	
	ELSEIF stOpcao = 'receitas_acoes_tipicas' THEN 
		
		-- ( 25% do item 3 )

		stSQL := '
		INSERT INTO tmp_rreo_an10_receita VALUES (16, 0, 0, ''IMPOSTOS E TRANSFERÊNCIAS DESTINADAS À MDE (25% DE 3)<SUP>1</SUP>'') ;
		';
		
		EXECUTE stSQL;

	END IF;
	
	-- ----------------------------------
	-- Fim stOpcao
	-- ----------------------------------

	-- ----------------------------------------
	-- Sub-totalizadores
	-- ----------------------------------------
	
	stSQL := 'SELECT DISTINCT grupo FROM tmp_rreo_an10_receita t1';
	
	FOR reReggrupo IN EXECUTE stSQL
	LOOP
	
		stSQLgrupo := '
		UPDATE tmp_rreo_an10_receita 
		SET ini    = ( SELECT SUM(ini)    FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' ) , 
			atu    = ( SELECT SUM(atu)    FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' ) , 
			no_bi  = ( SELECT SUM(no_bi)  FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' ) , 
			ate_bi = ( SELECT SUM(ate_bi) FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' ) 	
		WHERE grupo = ' || reReggrupo.grupo || ' AND subgrupo = 0 AND item = 0 ';
		
		EXECUTE stSQLgrupo;
		
		stSQLsubgrupo := 'SELECT DISTINCT subgrupo FROM tmp_rreo_an10_receita t2 WHERE grupo = ' || reReggrupo.grupo || ' ';
		
		FOR reRegSubgrupo IN EXECUTE stSQLsubgrupo
		LOOP
		
			stSQLaux := '
			UPDATE tmp_rreo_an10_receita 
			SET ini    = ( SELECT SUM(ini)    FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) , 
				atu    = ( SELECT SUM(atu)    FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) , 
				no_bi  = ( SELECT SUM(no_bi)  FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) , 
				ate_bi = ( SELECT SUM(ate_bi) FROM tmp_rreo_an10_receita WHERE grupo = ' || reReggrupo.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) 
			WHERE grupo = ' || reReggrupo.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' AND item = 0 ';
			
			EXECUTE stSQLaux;
			
		END LOOP;
	
	END LOOP;


	-- --------------------------------------
	-- Totalizadores Personalizados
	-- --------------------------------------
	
	-- receitas_educacao
	
	stSQL := '
	UPDATE tmp_rreo_an10_receita 
	SET ini    = ( SELECT COALESCE(SUM(ini), 0.00)    FROM tmp_rreo_an10_receita WHERE grupo IN (1, 2) AND subgrupo = 0 AND item = 0 ) , 
		atu    = ( SELECT COALESCE(SUM(atu), 0.00)    FROM tmp_rreo_an10_receita WHERE grupo IN (1, 2) AND subgrupo = 0 AND item = 0 ) , 
		no_bi  = ( SELECT COALESCE(SUM(no_bi), 0.00)  FROM tmp_rreo_an10_receita WHERE grupo IN (1, 2) AND subgrupo = 0 AND item = 0 ) , 
		ate_bi = ( SELECT COALESCE(SUM(ate_bi), 0.00) FROM tmp_rreo_an10_receita WHERE grupo IN (1, 2) AND subgrupo = 0 AND item = 0 ) , 
		pct    = ( SELECT COALESCE(SUM(pct), 0.00)    FROM tmp_rreo_an10_receita WHERE grupo IN (1, 2) AND subgrupo = 0 AND item = 0 )  
	WHERE grupo = 3 AND subgrupo = 0 AND item = 0 ';
	
	EXECUTE stSQL;
	
	
	-- outras_receitas_educacao
	
	stSQL := '
	UPDATE tmp_rreo_an10_receita 
	SET ini    = ( SELECT COALESCE(SUM(ini), 0.00)    FROM tmp_rreo_an10_receita WHERE grupo IN (4, 5, 6, 7, 8) AND subgrupo = 0 AND item = 0 ) , 
		atu    = ( SELECT COALESCE(SUM(atu), 0.00)    FROM tmp_rreo_an10_receita WHERE grupo IN (4, 5, 6, 7, 8) AND subgrupo = 0 AND item = 0 ) , 
		no_bi  = ( SELECT COALESCE(SUM(no_bi), 0.00)  FROM tmp_rreo_an10_receita WHERE grupo IN (4, 5, 6, 7, 8) AND subgrupo = 0 AND item = 0 ) , 
		ate_bi = ( SELECT COALESCE(SUM(ate_bi), 0.00) FROM tmp_rreo_an10_receita WHERE grupo IN (4, 5, 6, 7, 8) AND subgrupo = 0 AND item = 0 ) , 
		pct    = ( SELECT COALESCE(SUM(pct), 0.00)    FROM tmp_rreo_an10_receita WHERE grupo IN (4, 5, 6, 7, 8) AND subgrupo = 0 AND item = 0 ) 
	WHERE grupo = 8 AND subgrupo = 0 AND item = 0 ';
	
	EXECUTE stSQL;
	
	-- receitas_fundeb
	
	-- 11 = (10.1 - 9)
	stSQL := '
	UPDATE tmp_rreo_an10_receita 
	SET ini   = ( (SELECT COALESCE(SUM(ini), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 12 AND subgrupo = 1 AND item = 0) - (SELECT COALESCE(SUM(ini), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 11 AND subgrupo = 0 AND item = 0) ) , 
		atu   = ( (SELECT COALESCE(SUM(atu), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 12 AND subgrupo = 1 AND item = 0) - (SELECT COALESCE(SUM(atu), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 11 AND subgrupo = 0 AND item = 0) ) , 
		no_bi = ( (SELECT COALESCE(SUM(no_bi), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 12 AND subgrupo = 1 AND item = 0) - (SELECT COALESCE(SUM(no_bi), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 11 AND subgrupo = 0 AND item = 0) ) , 
		ate_bi = ( (SELECT COALESCE(SUM(ate_bi), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 12 AND subgrupo = 1 AND item = 0) - (SELECT COALESCE(SUM(ate_bi), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 11 AND subgrupo = 0 AND item = 0) ) , 
		pct = ( (SELECT COALESCE(SUM(pct), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 12 AND subgrupo = 1 AND item = 0) - (SELECT COALESCE(SUM(pct), 0.00) FROM tmp_rreo_an10_receita WHERE grupo = 11 AND subgrupo = 0 AND item = 0) )  
	WHERE grupo = 13 AND subgrupo = 0 AND item = 0 ';
	
	EXECUTE stSQL;
	
	
	-- Totalizar porcentagens (ao final de tudo)
	
	stSQL := 'UPDATE tmp_rreo_an10_receita SET pct = ( ate_bi / atu ) * 100 WHERE ate_bi > 0 AND atu > 0';
	
	EXECUTE stSQL ;

	-- --------------------------------------
	-- Select de Retorno
	-- --------------------------------------

	stSQL := 'SELECT * FROM tmp_rreo_an10_receita ORDER BY grupo , subgrupo , item ';

	FOR reReg IN EXECUTE stSQL
	LOOP	
		RETURN NEXT reReg;	
	END LOOP;
	
	
    DROP TABLE tmp_rreo_an10_receita ;
    DROP TABLE tmp_valor ;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

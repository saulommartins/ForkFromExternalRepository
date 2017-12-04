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
    * PL do TCEMG AnexoI - Arquivo TCEMG da GPC 
    * Data de Criação   : 18/07/2014

    * @author Analista:      Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes
    
    $Id: $ tcemg.fn_relatorio_anexo_valor_conta
*/
CREATE OR REPLACE FUNCTION tcemg.fn_relatorio_anexoI(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, BOOLEAN) RETURNS SETOF RECORD AS $$
DECLARE 
    stExercicio      ALIAS FOR $1;
    stEntidades      ALIAS FOR $2;
    stOpcao          ALIAS FOR $3;
    stDtIni          ALIAS FOR $4;
    stDtFim          ALIAS FOR $5;
    stSituacao       ALIAS FOR $6;
    boRestos         ALIAS FOR $7;
   
    inCount          INTEGER := 0;
    stDtIniExercicio VARCHAR :='';	
    stSql            VARCHAR :='';
    nuSubTotalA      NUMERIC := 0.00;
    nuSubTotalB      NUMERIC := 0.00;
    nuSubTotalC      NUMERIC := 0.00;
    nuSubTotalD      NUMERIC := 0.00;
    vlSubTotalE      NUMERIC := 0.00;
    nuSubTotalE      NUMERIC := 0.00;
    nuTotalReceitas  NUMERIC := 0.00;
    reReg            RECORD;
    reDeducoes       RECORD;
	
BEGIN 
	-- Definicao de Datas conforme Bimestre selecionado
	stDtIniExercicio := '01/01/' || stExercicio;

    IF stSituacao = 'empenhado' THEN
        boRestos := false;
    END IF;
	
	stSql := '
	CREATE TEMPORARY TABLE tmp_valor AS (
	SELECT ocr.cod_estrutural as cod_estrutural
         , lote.dt_lote       as data
         , vl.vl_lancamento   as valor
         , vl.oid             as primeira 
	  FROM contabilidade.valor_lancamento AS vl
         , orcamento.conta_receita AS ocr
         , orcamento.receita AS ore
         JOIN orcamento.recurso
            ON recurso.exercicio    = ore.exercicio
            AND recurso.cod_recurso  = ore.cod_recurso
         JOIN ppa.acao_recurso
            ON acao_recurso.cod_recurso        = recurso.cod_recurso
            AND acao_recurso.exercicio_recurso  = recurso.exercicio
         JOIN ppa.acao_dados
            ON acao_dados.cod_acao             = acao_recurso.cod_acao
            AND acao_dados.timestamp_acao_dados = acao_recurso.timestamp_acao_dados 
         , contabilidade.lancamento_receita AS lr
         , contabilidade.lancamento AS lan
         , contabilidade.lote AS lote
	 WHERE ore.exercicio = '''||stExercicio||''' 
       AND ore.cod_entidade IN ('||stEntidades||') 
       AND ocr.cod_conta = ore.cod_conta
       AND ocr.exercicio = ore.exercicio

		-- join lancamento receita
       AND lr.cod_receita = ore.cod_receita
       AND lr.exercicio   = ore.exercicio
       AND lr.estorno     = true
		-- tipo de lancamento receita deve ser = A , de arrecadação
       AND lr.tipo = ''A''

       -- join nas tabelas lancamento_receita e lancamento
       AND lan.cod_lote     = lr.cod_lote
       AND lan.sequencia    = lr.sequencia
       AND lan.exercicio    = lr.exercicio
       AND lan.cod_entidade = lr.cod_entidade
       AND lan.tipo         = lr.tipo

       -- join nas tabelas lancamento e valor_lancamento
       AND vl.exercicio    = lan.exercicio
       AND vl.sequencia    = lan.sequencia
       AND vl.cod_entidade = lan.cod_entidade
       AND vl.cod_lote     = lan.cod_lote
       AND vl.tipo         = lan.tipo
       -- na tabela valor lancamento  tipo_valor deve ser credito
       AND vl.tipo_valor   = ''D''

       AND lote.cod_lote     = lan.cod_lote
       AND lote.cod_entidade = lan.cod_entidade
       AND lote.exercicio    = lan.exercicio
       AND lote.tipo         = lan.tipo
	
    UNION

	SELECT ocr.cod_estrutural as cod_estrutural
         , lote.dt_lote       as data
         , vl.vl_lancamento   as valor
         , vl.oid             as segunda 
	  FROM contabilidade.valor_lancamento AS vl
         , orcamento.conta_receita AS ocr
         , orcamento.receita AS ore
         JOIN orcamento.recurso
            ON recurso.exercicio    = ore.exercicio
            AND recurso.cod_recurso  = ore.cod_recurso
         JOIN ppa.acao_recurso
            ON acao_recurso.cod_recurso        = recurso.cod_recurso
            AND acao_recurso.exercicio_recurso  = recurso.exercicio
         JOIN ppa.acao_dados
            ON acao_dados.cod_acao             = acao_recurso.cod_acao
            AND acao_dados.timestamp_acao_dados = acao_recurso.timestamp_acao_dados 
         , contabilidade.lancamento_receita AS lr
         , contabilidade.lancamento AS lan
         , contabilidade.lote AS lote
     WHERE ore.exercicio = '''||stExercicio||''' 
       AND ore.cod_entidade IN (' || stEntidades || ') 
       AND ocr.cod_conta = ore.cod_conta
       AND ocr.exercicio = ore.exercicio
       -- join lancamento receita
       AND lr.cod_receita = ore.cod_receita
       AND lr.exercicio   = ore.exercicio
       AND lr.estorno     = false
       -- tipo de lancamento receita deve ser = A , de arrecadação
       AND lr.tipo = ''A''

       -- join nas tabelas lancamento_receita e lancamento
       AND lan.cod_lote     = lr.cod_lote
       AND lan.sequencia    = lr.sequencia
       AND lan.exercicio    = lr.exercicio
       AND lan.cod_entidade = lr.cod_entidade
       AND lan.tipo         = lr.tipo

       -- join nas tabelas lancamento e valor_lancamento
       AND vl.exercicio    = lan.exercicio
       AND vl.sequencia    = lan.sequencia
       AND vl.cod_entidade = lan.cod_entidade
       AND vl.cod_lote     = lan.cod_lote
       AND vl.tipo         = lan.tipo
       -- na tabela valor lancamento  tipo_valor deve ser credito
       AND vl.tipo_valor = ''C''

       -- Data Inicial e Data Final, antes iguala codigo do lote
       AND lote.cod_lote     = lan.cod_lote
       AND lote.cod_entidade = lan.cod_entidade
       AND lote.exercicio    = lan.exercicio
       AND lote.tipo         = lan.tipo
	) 	
	';
	EXECUTE stSql;

	-- -------------------------------------	
	-- Estrutura de Tabelas Temporarias
	-- -------------------------------------

	-- Tabela tmp_tcemg_anI_receita
	-- Formatação e Cálculos Agrupados para Exibição de Resultados

	stSql := '
    CREATE TEMPORARY TABLE tmp_tcemg_anI_receita (
		grupo     INTEGER       DEFAULT    0 , 
		subgrupo  INTEGER       DEFAULT    0 , 
		item      INTEGER       DEFAULT    0 , 
		descricao VARCHAR(150)  DEFAULT NULL ,
        ini       NUMERIC(14,2) DEFAULT 0.00 , 
		atu       NUMERIC(14,2) DEFAULT 0.00 , 
		no_bi     NUMERIC(14,2) DEFAULT 0.00 , 
		ate_bi    NUMERIC(14,2) DEFAULT 0.00 , 
		pct       NUMERIC(14,2) DEFAULT 0.00 
    ); 
	
    CREATE INDEX idx_tcemg_anI_receita ON 
        tmp_tcemg_anI_receita (descricao) ;	
    ';
	
	EXECUTE stSql ; 	
	
	-- -------------------------------------
	-- Fim Estrutura de Tabelas Temporarias
	-- -------------------------------------	
	
	-- -------------------------------------
	-- Opcao de Relatorio
	-- -------------------------------------	

    ----------------------------------------
	-- A - Impostos:
	----------------------------------------
    INSERT INTO tmp_tcemg_anI_receita VALUES (1,0,1,'A - Impostos:', 0.00,0.00,0.00,0.00,0.00);

    ---------------------------------------------
    -- COD ESTRUTURAL : 1.1.1.2.02.00.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount
      FROM orcamento.conta_receita
     WHERE exercicio = stExercicio
       AND cod_estrutural ILIKE '1.1.1.2.02.00.00.00.00';
    
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (1,1,1,''00.1112.02.00    IPTU - Imposto sobre a Propriedade Predial e Territorial Urbana'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1112.02.00    IPTU - Imposto sobre a Propriedade Predial e Territorial Urbana'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.2.02.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 1, 1, 1) ;
        ';
    END IF;
    
    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.1.1.2.04.31.00.00.00 --
    ---------------------------------------------
    
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.1.1.2.04.31.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (1,1,2,''00.1112.04.31    Imposto de Renda Retido nas Fontes sobre os Rendimentos do Trabalho'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1112.04.31    Imposto de Renda Retido nas Fontes sobre os Rendimentos do Trabalho'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.2.04.31.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 1, 1, 2) ;
        ';
    END IF;
    
    EXECUTE stSql;
    
     ---------------------------------------------
    -- COD ESTRUTURAL : 1.1.1.2.04.34.00.00.00 --
    ---------------------------------------------
    
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.1.1.2.04.34.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (1,1,3,''00.1112.04.34    Retido Nas Fontes - Outros Rendimentos'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1112.04.34    Retido Nas Fontes - Outros Rendimentos'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.2.04.34.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 1, 1, 3) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.1.1.2.08.00.00.00.00 --
    ---------------------------------------------
    
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.1.1.2.08.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (1,1,4,''00.1112.08.00    Imposto sobre Transmissão "Inter-Vivos" de Bens Imóveis e de Direitos Reais sobre Imóveis'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1112.08.00    Imposto sobre Transmissão "Inter-Vivos" de Bens Imóveis e de Direitos Reais sobre Imóveis'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.2.08.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 1, 1, 4) ;
        ';
    END IF;

    EXECUTE stSql;
        
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.1.1.3.05.01.00.00.00 --
    ---------------------------------------------
    
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.1.1.3.05.01.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (1,1,5,''00.1113.05.01    Imposto sobre Serviços de Qualquer Natureza'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1113.05.01    Imposto sobre Serviços de Qualquer Natureza'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.3.05.01.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 1, 1, 5) ;
        ';
    END IF;

    EXECUTE stSql;

    ---------------------------------------------
    -- COD ESTRUTURAL : 1.1.1.3.05.03.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.1.1.3.05.03.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (1,1,6,''00.1113.05.03    ISS - Simples Nacional'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1113.05.03    ISS - Simples Nacional'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.1.1.3.05.03.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 1, 1, 6) ;
        ';
    END IF;

    EXECUTE stSql;

    ----------------------------------------
	-- A - Impostos:Subtotal
	----------------------------------------
    SELECT SUM(no_bi) INTO nuSubTotalA
      FROM tmp_tcemg_anI_receita
     WHERE grupo = 1
       AND subgrupo = 1
  GROUP BY grupo, subgrupo;
  
    INSERT INTO tmp_tcemg_anI_receita VALUES (1,2,1,'Subtotal', 0.00,0.00,nuSubTotalA,0.00,0.00);


    ----------------------------------------
	-- B - Transferência Correntes
	----------------------------------------
    INSERT INTO tmp_tcemg_anI_receita VALUES (2,0,1,'B - Transferência Correntes:', 0.00,0.00,0.00,0.00,0.00);

    ---------------------------------------------
    -- COD ESTRUTURAL : 1.7.2.1.01.02.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.7.2.1.01.02.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (2,1,1,''00.1721.01.02    Cota-Parte do Fundo de Participação dos Municípios'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1721.01.02    Cota-Parte do Fundo de Participação dos Municípios'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.01.02.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 2, 1, 1) ;
        ';
    END IF;
    
    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.7.2.1.01.05.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.7.2.1.01.05.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (2,1,2,''00.1721.01.05    Cota-Parte do Imposto sobre a Propriedade Territorial Rural'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1721.01.05    Cota-Parte do Imposto sobre a Propriedade Territorial Rural'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.01.05.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 2, 1, 2) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.7.2.1.36.00.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.7.2.1.36.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (2,1,3,''00.1721.36.00    Transferência Financeira do ICMS - Desoneração - LC 87/96'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1721.36.00    Transferência Financeira do ICMS - Desoneração - LC 87/96'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.1.36.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 2, 1, 3) ;
        ';
    END IF;
    
    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.7.2.2.01.01.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.7.2.2.01.01.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (2,1,4,''00.1722.01.01    Cota-Parte do ICMS'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1722.01.01    Cota-Parte do ICMS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.2.01.01.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 2, 1, 4) ;
        ';
    END IF;
    
    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.7.2.2.01.02.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.7.2.2.01.02.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (2,1,5,''00.1722.01.02    Cota-Parte do Imposto sobre a Propriedade de Veículos Automotores'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1722.01.02    Cota-Parte do Imposto sobre a Propriedade de Veículos Automotores'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.2.01.02.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 2, 1, 5) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.7.2.2.01.04.00.00.00 --
    ---------------------------------------------

    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.7.2.2.01.04.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (2,1,6,''00.1722.01.04    Cota-Parte do IPI sobre Exportação'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1722.01.04    Cota-Parte do IPI sobre Exportação'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.7.2.2.01.04.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 2, 1, 6) ;
        ';
    END IF;
    
    EXECUTE stSql;
    
    ----------------------------------------
	--  B - Transferência Correntes:Subtotal
	----------------------------------------
    SELECT SUM(no_bi) INTO nuSubTotalB
      FROM tmp_tcemg_anI_receita
     WHERE grupo = 2
       AND subgrupo = 1
  GROUP BY grupo, subgrupo;

    INSERT INTO tmp_tcemg_anI_receita VALUES (2,2,1,'Subtotal', 0.00,0.00,nuSubTotalB,0.00,0.00);

    ----------------------------------------
	-- C - Outras Receitas Correntes:
	----------------------------------------

    INSERT INTO tmp_tcemg_anI_receita VALUES (3,0,1,'C - Outras Receitas Correntes:', 0.00,0.00,0.00,0.00,0.00);

    ---------------------------------------------
    -- COD ESTRUTURAL : 1.9.1.1.38.00.00.00.00 --
    ---------------------------------------------
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.9.1.1.38.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (3,1,1,''00.1911.38.00    Multas e Juros de Mora do Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1911.38.00    Multas e Juros de Mora do Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.1.38.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 3, 1, 1) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.9.1.1.40.00.00.00.00 --
    ---------------------------------------------
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.9.1.1.40.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (3,1,2,''00.1911.40.00    Mora do Imposto sobre Serviços de Qualquer Natureza - ISS'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1911.40.00    Mora do Imposto sobre Serviços de Qualquer Natureza - ISS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.1.40.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 3, 1, 2) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.9.1.3.11.00.00.00.00 --
    ---------------------------------------------
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.9.1.3.11.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (3,1,3,''00.1913.11.00    Multas e Juros de Mora da Divida Ativa do Imp. sobre a Propriedade Predial e Territ. Urbana - IPTU'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1913.11.00    Multas e Juros de Mora da Divida Ativa do Imp. sobre a Propriedade Predial e Territ. Urbana - IPTU'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.3.11.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 3, 1, 3) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.9.1.3.13.00.00.00.00 --
    ---------------------------------------------
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.9.1.3.13.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (3,1,4,''00.1913.13.00    Multas e Juros de Mora da Dívida Ativa do Imposto sobre Serviços de Qualquer Natureza - ISS'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1913.13.00    Multas e Juros de Mora da Dívida Ativa do Imposto sobre Serviços de Qualquer Natureza - ISS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.1.3.13.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 3, 1, 4) ;
        ';
    END IF;
    
    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.9.3.1.11.00.00.00.00 --
    ---------------------------------------------
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.9.3.1.11.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (3,1,5,''00.1931.11.00    Receita da Dívida Ativa do Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1931.11.00    Receita da Dívida Ativa do Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.3.1.11.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 3, 1, 5) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ---------------------------------------------
    -- COD ESTRUTURAL : 1.9.3.1.13.00.00.00.00 --
    ---------------------------------------------
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '1.9.3.1.13.00.00.00.00';
    
    IF inCount = 0 THEN
        stSql := 'INSERT INTO tmp_tcemg_anI_receita VALUES (3,1,6,''00.1931.13.00    Receita da Dívida Ativa do Imposto sobre Serviços de Qualquer Natureza - ISS'', 0.00,0.00,0.00,0.00,0.00);';
    ELSE
        stSql := 'INSERT INTO tmp_tcemg_anI_receita
            SELECT grupo, subgrupo, item, ''00.1931.13.00    Receita da Dívida Ativa do Imposto sobre Serviços de Qualquer Natureza - ISS'' as descricao, ini, atu, no_bi, ate_bi, pct 
		FROM tcemg.fn_relatorio_anexo_valor_conta(''' || stExercicio || ''', ''R'', ''1.9.3.1.13.00.00.00.00'','''||stEntidades||''', '''||stDtIni||''','''||stDtFim||''', true, 3, 1, 6) ;
        ';
    END IF;

    EXECUTE stSql;
    
    ----------------------------------------
	--  C - Outras Receitas Correntes:Subtotal
	----------------------------------------
    SELECT SUM(no_bi) INTO nuSubTotalC
      FROM tmp_tcemg_anI_receita
     WHERE grupo = 3
       AND subgrupo = 1
  GROUP BY grupo, subgrupo;

    INSERT INTO tmp_tcemg_anI_receita VALUES (3,2,1,'Subtotal', 0.00,0.00,nuSubTotalC,0.00,0.00);

    ----------------------------------------
	-- D - Transferências de Capital:
	----------------------------------------
    INSERT INTO tmp_tcemg_anI_receita VALUES (4,0,1,'D - Transferências de Capital:', 0.00,0.00,0.00,0.00,0.00);

    ----------------------------------------
	--  D - Transferências de Capital:Subtotal
	----------------------------------------
    SELECT COUNT(*) INTO inCount FROM orcamento.conta_receita WHERE exercicio = stExercicio AND cod_estrutural ILIKE '2.4.0.0.00.00.00.00.00';
    
    IF inCount = 0 THEN
        nuSubTotalD := 0.00;
    ELSE
        SELECT no_bi INTO nuSubTotalD FROM tcemg.fn_relatorio_anexo_valor_conta(stExercicio, 'R', '2.4.0.0.00.00.00.00.00',stEntidades, stDtIni,stDtFim, true, 4, 2, 1) ;
    END IF;
    /*#22472 
      Ajusta a coluna:
      D - Transferências de Capital:
      onde hoje é demonstrado valor - deixar parametrizado 0,00
    */
    INSERT INTO tmp_tcemg_anI_receita VALUES (4,2,1,'Subtotal', 0.00,0.00,0.00,0.00,0.00);


    ----------------------------------------
    -- E - Dedução das Receitas (exceto FUNDEB):
    ----------------------------------------
    
    INSERT INTO tmp_tcemg_anI_receita VALUES (5,0,1,'E - Dedução das Receitas (exceto FUNDEB):', 0.00,0.00,0.00,0.00,0.00);

    ----------------------------------------
    --  E - Dedução das Receitas (exceto FUNDEB):Subtotal
    ----------------------------------------
    stSql := '
        CREATE TEMPORARY TABLE tmp_tcemg_anI_receita_deducao (
		cod_estrutural VARCHAR(150)  DEFAULT NULL
         ); ';
    
    EXECUTE stSql ; 	
    
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.1.1.2.01.01.02.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.01.02.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.01.02.06.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.01.05.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.01.05.04.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.02.05.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.02.06.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.36.00.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.1.36.00.05.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.2.01.01.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.2.01.01.05.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.2.01.02.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.2.01.02.04.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.2.01.04.00.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.2.01.04.05.00.00');
    INSERT INTO tmp_tcemg_anI_receita_deducao VALUES ('9.1.7.2.4.00.00.00.00.00');
    
    stSql := 'SELECT tmp_valor.cod_estrutural
                FROM tmp_valor
               WHERE tmp_valor.cod_estrutural ILIKE ''9.%''
                 AND NOT EXISTS (
                                 SELECT cod_estrutural
                                   FROM tmp_tcemg_anI_receita_deducao
                                  WHERE tmp_valor.cod_estrutural ILIKE tmp_tcemg_anI_receita_deducao.cod_estrutural
                                )
            GROUP BY cod_estrutural; ';

    FOR reDeducoes IN EXECUTE stSql
    LOOP
        SELECT no_bi INTO vlSubTotalE FROM tcemg.fn_relatorio_anexo_valor_conta(stExercicio, 'R', reDeducoes.cod_estrutural, stEntidades, stDtIni,stDtFim, true, 4, 2, 1) ;
        RAISE NOTICE 'stExercicio: %', stExercicio;
        RAISE NOTICE 'reDeducoes.cod_estrutural: %', reDeducoes.cod_estrutural;
        RAISE NOTICE 'stEntidades: %', stEntidades;
        RAISE NOTICE 'stDtIni: %', stDtIni;
        RAISE NOTICE 'stDtFim: %', stDtFim;
        RAISE NOTICE 'vlSubTotalE: %', vlSubTotalE;
        RAISE NOTICE 'nuSubTotalE: %', nuSubTotalE;
        nuSubTotalE := nuSubTotalE + vlSubTotalE;
    END LOOP;
    INSERT INTO tmp_tcemg_anI_receita VALUES (5,2,1,'Subtotal', 0.00,0.00,nuSubTotalE,0.00,0.00);

    /*#22472 
      Ajusta a coluna:
      D - Transferências de Capital:
      Total das Receitas (A + B + C + D - E)
      Não considerar mais o D na soma. Como ficará parametrizado 0,00 não terá mais esse valor na soma
      soma da versao anterior ao ticket #22472 = nuTotalReceitas := nuSubTotalA + nuSubTotalB + nuSubTotalC + nuSubTotalD - nuSubTotalE;
    */
    nuTotalReceitas := nuSubTotalA + nuSubTotalB + nuSubTotalC - nuSubTotalE;
    
	-- --------------------------------------
	-- Select de Retorno
	-- --------------------------------------

    stSql := ' SELECT grupo
                   , subgrupo
                   , item
                   , descricao
                   , ini
                   , atu
                   , no_bi
                   , ate_bi
                   , pct
                   , '||nuTotalReceitas||'::NUMERIC AS total_receitas
                   , ( SELECT (vl_fundeb + vl_sub_total) AS total
                         FROM tcemg.relatorio_anexoII('|| quote_literal(stExercicio) ||'
                                                     ,'' AND od.cod_entidade IN (2) AND od.cod_recurso = 101 AND od.cod_funcao = 12 AND od.cod_subfuncao IN (122,272,361,365,367)''
                                                     ,'|| quote_literal(stDtIni) ||'
                                                     ,'|| quote_literal(stDtFim) ||'
                                                     ,'|| quote_literal(stSituacao) ||'
                                                     ,'|| boRestos ||'
                                                     ,''''
                                                     ,''''
                                                     ,''''
                                                     ,''''
                                                     ,''''
                                                     ,''''
                                                     ,''''
                                                     , '''' ) AS tbl1
                                      ( cod_funcao           INTEGER
                                      , cod_subfuncao        INTEGER
                                      , cod_programa         VARCHAR
                                      , nom_programa         VARCHAR
                                      , vl_tipo_situacao_per NUMERIC
                                      , vl_fundeb            NUMERIC
                                      , vl_sub_total         NUMERIC
                            )
                       WHERE cod_funcao = 12
                         AND cod_subfuncao = 0
                          ) AS total_anexo_ii
                      FROM tmp_tcemg_anI_receita
                  ORDER BY grupo
                         , subgrupo
                         , item';

	FOR reReg IN EXECUTE stSql
	LOOP
	    RETURN NEXT reReg;	
	END LOOP;
	
    DROP TABLE tmp_tcemg_anI_receita ;
    --Em tcemg.relatorio_anexoII também é criado uma tabela temporaria que utiliza o mesmo nome, utiliza em diversas funções internas, por isso IF EXISTS.
    DROP TABLE IF EXISTS tmp_valor ;
    DROP TABLE tmp_tcemg_anI_receita_deducao ;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
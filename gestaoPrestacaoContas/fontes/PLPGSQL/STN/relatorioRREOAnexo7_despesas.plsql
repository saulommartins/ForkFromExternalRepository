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
    * Script de função PLPGSQL - Relatório STN - Anexo7 - Despesas
    *
    * URBEM Soluções de Gestão Pública Ltda
    * www.urbem.cnm.org.br
    *
    * $Revision: 30617 $
    * $Name$
    * $Author: eduardoschitz $
    * $Date: 2008-06-30 16:12:53 -0300 (Seg, 30 Jun 2008) $
    *
    * 
    * Casos de uso:
    *
    * $id: $
    *
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo7_despesas( varchar,varchar,varchar,varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    stCodEntidades 	ALIAS FOR $2;
    dtInicial  		ALIAS FOR $3;
    dtFinal    		ALIAS FOR $4;
    dtIniExercicio 	VARCHAR := '';
    stExercicioAnterior VARCHAR := '';
    dtInicialExercicioAnterior VARCHAR := '';
    dtFinalExercicioAnterior VARCHAR := '';
    
    arDatas 		varchar[] ;
    arDatasExercicioAnterior varchar[] ;
    stSQLsubgrupo   VARCHAR   := '';
    stSQLaux        VARCHAR   := '';
    stSQLaux2       VARCHAR   := '';
    reReg           RECORD;
    reRegSubgrupo   RECORD;
    reRegistro 	    record ;
    stSql	    varchar := '';

BEGIN

    stExercicioAnterior := cast((cast(stExercicio as integer) - 1) as varchar);

    dtIniExercicio := '01/01/'||stExercicio;
    dtInicialExercicioAnterior := '01/01/' || stExercicioAnterior;
    dtFinalExercicioAnterior :=  SUBSTRING(dtFinal,0,6) || stExercicioAnterior;

	-- TABELAS TEMPORARIAS
	
	-- Total das Suplementações	
    -- Suplementado
    
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_suplementada AS
               SELECT   sups.cod_despesa
                       ,sum(sups.valor)  as vl_suplementado
               FROM     orcamento.suplementacao                as sup
                       ,orcamento.suplementacao_suplementada   as sups
                       ,orcamento.despesa                      as de
               WHERE   sup.exercicio           = '|| quote_literal(stExercicio) ||' 
                 AND   sup.dt_suplementacao BETWEEN to_date('''||dtIniExercicio||''',''dd/mm/yyyy'') 
                                                AND to_date('''||dtFinal  ||''',''dd/mm/yyyy'') ';
    
    if ( stCodEntidades != '' ) then
        stSql := stSql || ' AND   de.cod_entidade IN (' || stCodEntidades || ' )';
    end if;
    
    stSql := stSql|| ' AND   sup.exercicio           = sups.exercicio
			AND   sup.cod_suplementacao   = sups.cod_suplementacao
			AND   sups.exercicio          = de.exercicio
			AND   sups.cod_despesa        = de.cod_despesa
		GROUP BY sups.cod_despesa;
	CREATE INDEX unq_tmp_suplementacao_suplementada ON tmp_suplementacao_suplementada (cod_despesa);
	';

    EXECUTE stSql;

    -- Reduzido
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_suplementacao_reduzida AS
    	SELECT 
    		supr.cod_despesa, 
    		sum(supr.valor) as vl_reduzido 
    	FROM 
    		orcamento.suplementacao as sup, 
    		orcamento.suplementacao_reducao as supr, 
    		orcamento.despesa as de 
    	WHERE 
    		sup.exercicio = ' || quote_literal(stExercicio) ||' AND 
    		sup.dt_suplementacao BETWEEN 	to_date('''||dtIniExercicio||''', ''dd/mm/yyyy'') 
            	                     AND 	to_date('''||dtFinal  ||''', ''dd/mm/yyyy'')
	';
    if (stCodEntidades != '' ) then 
         stSql := stSql || ' AND de.cod_entidade IN (' ||  stCodEntidades ||' ) ';
    end if ;

    stSql := stSql || 'AND sup.exercicio = supr.exercicio AND 
    		sup.cod_suplementacao   = supr.cod_suplementacao AND 
    		supr.exercicio          = de.exercicio AND 
    		supr.cod_despesa        = de.cod_despesa 
    	GROUP BY supr.cod_despesa ;
    	
    CREATE INDEX unq_tmp_suplementacao_reduzida ON tmp_suplementacao_reduzida (cod_despesa);
    ';
    
    EXECUTE stSql;

    -- Total das Despesas
    
	stSql := '
	CREATE TEMPORARY TABLE tmp_despesa_totais AS
		SELECT 
			de.exercicio, 
			de.cod_despesa, 
			de.vl_original 
		FROM 
			orcamento.despesa de, 
			empenho.pre_empenho_despesa ped 
		WHERE 
			de.exercicio    = ' || quote_literal(stExercicio) || ' AND 
			de.exercicio    = ped.exercicio AND 
			de.cod_despesa  = ped.cod_despesa 
		GROUP BY 
			de.exercicio, 
			de.cod_despesa, 
			de.vl_original 
		ORDER BY 
			de.exercicio, 
			de.cod_despesa ;
			
	CREATE INDEX unq_tmp_despesa_totais ON tmp_despesa_totais (exercicio,cod_despesa);
	';

	EXECUTE stSql;

	
	-- --------------------------------------------
	-- Total por Despesa
	-- --------------------------------------------


	stSql := '
	CREATE TEMPORARY TABLE tmp_despesa AS 
		SELECT 
			de.cod_conta, 
			de.exercicio, 
			ocd.cod_estrutural, 
			sum(coalesce(de.vl_original,0.00)) as vl_original, 			
			(sum(coalesce(sups.vl_suplementado,0.00)) - sum(coalesce(supr.vl_reduzido,0.00))) as vl_suplementacoes, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_bimestre, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_total 
		FROM 
			orcamento.despesa de 
			INNER JOIN 
			orcamento.conta_despesa ocd ON 
				ocd.exercicio = de.exercicio and 
				ocd.cod_conta = de.cod_conta 
			LEFT JOIN 
			tmp_despesa_totais tdt ON 
				tdt.exercicio = de.exercicio AND 
				tdt.cod_despesa = de.cod_despesa 
			--Suplementacoes
			LEFT JOIN 
			tmp_suplementacao_suplementada sups ON 
				de.cod_despesa = sups.cod_despesa
			LEFT JOIN 
			tmp_suplementacao_reduzida supr ON 
				de.cod_despesa = supr.cod_despesa 
		WHERE 
			de.exercicio = ' || quote_literal(stExercicio) || ' AND 
			de.cod_entidade IN (' ||  stCodEntidades ||' ) 
		GROUP BY 
			de.cod_conta, 
			de.exercicio, 
			ocd.cod_estrutural 
		ORDER BY 
			de.cod_conta, 
			de.exercicio 
	';
	
	EXECUTE stSql;
	
	--- FIM DAS TEMPORARIAS
    
	stSql := '
	CREATE TEMPORARY TABLE tmp_rreo_an7_despesa AS (

    SELECT
        1 as ordem,
        1 as grupo,
        0 as subgrupo,
        0 as item,
        CAST(''3.0.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
        CAST(''DESPESAS CORRENTES (VIII)'' as VARCHAR) as descricao,
        1 as nivel,
        0.00 as dotacao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION ALL

    SELECT * FROM (	
    	SELECT
            2 as ordem,
            1 as grupo,
            1 as subgrupo,
            0 as item,
    		cast(''3.1.0.0.00.00.00.00.00'' as varchar) as cod_estrutural , 
    		cast(''Pessoal e Encargos Sociais'' as varchar) as descricao , 
    		2 as nivel , 
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
    		COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) AS no_bimestre , 
    		COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00) AS ate_bimestre,
    		COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) AS ate_bimestre_exercicio_anterior 
    	FROM 
    		orcamento.conta_despesa ocd 
    	LEFT JOIN tmp_despesa tmp
               ON ocd.exercicio = tmp.exercicio
              AND tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
    	WHERE 
            ocd.cod_estrutural = ''3.1.0.0.00.00.00.00.00'' AND
            publico.fn_nivel(ocd.cod_estrutural) = 2 AND 
            ocd.exercicio = '|| quote_literal(stExercicio) ||'
            
            -- Exceto intra
	    AND substring(ocd.cod_estrutural, 5, 3) <> ''9.1''
            AND substring(tmp.cod_estrutural, 5, 3) <> ''9.1''
            
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
    	ORDER BY 
    		ocd.cod_estrutural 
    ) as tbl

    UNION ALL

    SELECT * FROM (
        SELECT
            3 as ordem,
            1 as grupo,
            2 as subgrupo,
            0 as item,
            cast(''3.2.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Juros e Encargos da Dívida (IX)'' as varchar) as descricao ,
            2 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', false )), 0.00) AS no_bimestre ,
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS ate_bimestre,
            COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '''|| stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', false )), 0.00) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3.2%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
            publico.fn_nivel(ocd.cod_estrutural) < 3 AND
            
            -- Exceto intra
                ocd.exercicio = '|| quote_literal(stExercicio) ||'
            AND substring(ocd.cod_estrutural, 5, 3) <> ''9.1''
            AND substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
            
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT * FROM(
        SELECT
            4 as ordem,
            1 as grupo,
            3 as subgrupo,
            0 as item,
            cast(''3.3.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Outras Despesas Correntes'' as varchar) as descricao ,
            2 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3.3%'' AND 
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
            publico.fn_nivel(ocd.cod_estrutural) < 3 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT
        5 as ordem,
        2 as grupo,
        0 as subgrupo,
        0 as item,
        CAST(''4.9.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
        CAST(''DESPESAS PRIMÁRIAS CORRENTES (X) = (VIII - IX)'' as VARCHAR) as descricao,
        1 as nivel,
        0.00 as dotacao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION ALL

    SELECT
        6 as ordem,
        3 as grupo,
        0 as subgrupo,
        0 as item,
        CAST(''4.0.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
        CAST(''DESPESAS DE CAPITAL (XI)'' as VARCHAR) as descricao,
        1 as nivel,
        0.00 as dotacao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION ALL

    SELECT * FROM(
        SELECT
            7 as ordem,
            3 as grupo,
            1 as subgrupo,
            0 as item,
            cast(''4.4.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Investimentos'' as varchar) as descricao ,
            2 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4.4%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
            publico.fn_nivel(ocd.cod_estrutural) < 3 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT
        8 as ordem,
        3 as grupo,
        2 as subgrupo,
        0 as item,
        CAST(''4.5.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
        CAST(''Inversões Financeiras'' as VARCHAR) as descricao,
        2 as nivel,
        0.00 as dotacao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION ALL

    SELECT * FROM(
        SELECT
            9 as ordem,
            3 as grupo,
            2 as subgrupo,
            1 as item,
            cast(''4.5.9.0.66.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Concessão do Empréstimos (XII)'' as varchar) as descricao ,
            3 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4.5.9.0.66%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND
            publico.fn_nivel(ocd.cod_estrutural) < 6 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT * FROM(
        SELECT
            10 as ordem,
            3 as grupo,
            2 as subgrupo,
            2 as item,
            cast(''4.5.9.0.64.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Aquisição de Título de Capital já Integralizado (XIII)'' as varchar) as descricao ,
            3 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4.5.9.0.64%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
            publico.fn_nivel(ocd.cod_estrutural) < 6 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'

        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT 
        ordem,
        grupo,
        subgrupo,
        item,
        cod_estrutural,
        descricao,
        nivel,
        COALESCE(sum(dotacao_atualizada), 0.00),
        COALESCE(sum(no_bimestre), 0.00),
        COALESCE(sum(ate_bimestre), 0.00),
        COALESCE(sum(ate_bimestre_exercicio_anterior), 0.00)
    FROM(
        SELECT
            11 as ordem,
            3 as grupo,
            2 as subgrupo,
            3 as item,
            cast(''4.5.1.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Demais Inversões Financeiras'' as varchar) as descricao ,
            3 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) LIKE ''4.5%'' AND
            publico.fn_mascarareduzida(ocd.cod_estrutural) NOT LIKE ''4.5.9.0.64%'' AND
            publico.fn_mascarareduzida(ocd.cod_estrutural) NOT LIKE ''4.5.9.0.66%'' AND
            publico.fn_nivel(ocd.cod_estrutural) = 5 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'

        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl
    GROUP BY
        ordem,
        grupo,
        subgrupo,
        item,
        cod_estrutural,
        descricao,
        nivel
      

    UNION ALL

    SELECT * FROM(
        SELECT
            12 as ordem,
            3 as grupo,
            3 as subgrupo,
            0 as item,
            cast(''4.6.0.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Amortização da Dívida (XIV)'' as varchar) as descricao ,
            2 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4.6%'' AND
            publico.fn_nivel(ocd.cod_estrutural) >  1 AND  
            publico.fn_nivel(ocd.cod_estrutural) < 3 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'

        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT
        13 as ordem,
        4 as grupo,
        0 as subgrupo,
        0 as item,
        CAST(''4.7.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
        CAST(''DESPESAS PRIMÁRIAS DE CAPITAL (XV) = (XI - XII - XIII - XIV)'' as VARCHAR) as descricao,
        1 as nivel,
        0.00 as dotacao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION ALL

    SELECT * FROM(
        SELECT
            14 as ordem,
            5 as grupo,
            0 as subgrupo,
            0 as item,
            cast(''9.9.9.9.99.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''RESERVA DE CONTINGÊNCIA (XVI)'' as varchar) as descricao ,
            1 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            ocd.cod_estrutural = ''9.9.9.9.99.00.00.00.00'' AND
            publico.fn_nivel(ocd.cod_estrutural) > 4 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'
        GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

    UNION ALL

    SELECT * FROM(
        SELECT
            15 as ordem,
            6 as grupo,
            0 as subgrupo,
            0 as item,
            cast(''7.7.9.9.99.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''RESERVA DO RPPS (XVII)'' as varchar) as descricao ,
            1 as nivel ,
            CAST(COALESCE(SUM((tmp.vl_original + tmp.vl_suplementacoes)), 0.00 )as numeric(14,2)) as dotacao_atualizada ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''||dtInicial||''', '''||dtFinal||''', true )), 0.00) as numeric(14,2)) AS no_bimestre ,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''||dtIniExercicio||''', '''||dtFinal||''', true )), 0.00)as numeric(14,2)) AS ate_bimestre,
            CAST(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ''' || stExercicioAnterior || ''', ''' ||  stCodEntidades ||''', '''||dtInicialExercicioAnterior||''', '''||dtFinalExercicioAnterior||''', true )), 0.00) as numeric(14,2)) AS ate_bimestre_exercicio_anterior
        FROM
            orcamento.conta_despesa ocd
            LEFT JOIN
            tmp_despesa tmp ON
                ocd.exercicio = tmp.exercicio AND
                tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' 
        WHERE
            ocd.cod_estrutural LIKE ''7.7.9.9.99.99%'' AND
            publico.fn_nivel(ocd.cod_estrutural) > 5 AND
            -- Exceto intra
            ocd.exercicio = '|| quote_literal(stExercicio) ||'
       GROUP BY
            ocd.descricao,
            ocd.cod_estrutural
        ORDER BY
            ocd.cod_estrutural
    ) as tbl

	) 
	';
	
	EXECUTE stSql;

    -- Calcular totais do nivel pai

    stSql := 'SELECT DISTINCT grupo FROM tmp_rreo_an7_despesa ';

    FOR reReg IN EXECUTE stSql
    LOOP

        stSQLsubgrupo := 'SELECT DISTINCT subgrupo FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' ';

        FOR reRegSubgrupo IN EXECUTE stSQLsubgrupo
        LOOP

            stSQLaux2 := '
            UPDATE tmp_rreo_an7_despesa SET
                dotacao_atualizada = ( SELECT SUM(dotacao_atualizada) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) ,
                no_bimestre = ( SELECT SUM(no_bimestre) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) ,
                ate_bimestre  = ( SELECT SUM(ate_bimestre) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) ,
                ate_bimestre_exercicio_anterior = ( SELECT SUM (ate_bimestre_exercicio_anterior) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' )
            WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' AND item = 0 AND cod_estrutural not in (''4.9.0.0.00.00.00.00.00'', ''4.7.0.0.00.00.00.00.00'' , ''9.9.9.9.99.00.00.00.00'', ''7.7.9.9.99.00.00.00.00'')';

            EXECUTE stSQLaux2;

        END LOOP;


        stSQLaux := '
        UPDATE tmp_rreo_an7_despesa SET
            dotacao_atualizada = (SELECT COALESCE(SUM(dotacao_atualizada), 0.00) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND nivel = 2),
            no_bimestre = (SELECT COALESCE(SUM(no_bimestre), 0.00) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND nivel = 2),
            ate_bimestre = (SELECT COALESCE(SUM(ate_bimestre), 0.00) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND nivel = 2),
            ate_bimestre_exercicio_anterior = (SELECT COALESCE(SUM(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_rreo_an7_despesa WHERE grupo = ' || reReg.grupo || ' AND nivel = 2)
        WHERE
            grupo = ' || reReg.grupo || ' AND nivel = 1 AND cod_estrutural not in (''4.9.0.0.00.00.00.00.00'', ''4.7.0.0.00.00.00.00.00'' , ''9.9.9.9.99.00.00.00.00'', ''7.7.9.9.99.00.00.00.00'')';

        EXECUTE stSQLaux;

    END LOOP;

    stSql := '
    UPDATE tmp_rreo_an7_despesa SET
        dotacao_atualizada = ((SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.0.0.0.00.00.00.00.00'')
                  - (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.66.00.00.00.00'')
                  - (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.64.00.00.00.00'')
                  - (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),
        no_bimestre = ((SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.0.0.0.00.00.00.00.00'')
                     - (SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.66.00.00.00.00'')
                     - (SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.64.00.00.00.00'')
                     - (SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),
        ate_bimestre = ((SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.0.0.0.00.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.66.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.64.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')),
        ate_bimestre_exercicio_anterior = ((SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.0.0.0.00.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.66.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.5.9.0.64.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''4.6.0.0.00.00.00.00.00'')  )
    WHERE cod_estrutural = ''4.7.0.0.00.00.00.00.00'';
    ';
    EXECUTE stSql;

    stSql := '
    UPDATE tmp_rreo_an7_despesa SET
        dotacao_atualizada = ((SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.0.0.0.00.00.00.00.00'')
                  - (SELECT coalesce(sum(dotacao_atualizada), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')),
        no_bimestre = ((SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.0.0.0.00.00.00.00.00'')
                     - (SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')),
        ate_bimestre = ((SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.0.0.0.00.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')),
        ate_bimestre_exercicio_anterior = ((SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.0.0.0.00.00.00.00.00'')
                      - (SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_rreo_an7_despesa where cod_estrutural = ''3.2.0.0.00.00.00.00.00'')  )
    WHERE cod_estrutural = ''4.9.0.0.00.00.00.00.00'';
    ';
    EXECUTE stSql;

	stSql := 'SELECT 
                     grupo
                    ,cod_estrutural
                    ,cast(trim(descricao) as varchar) AS descricao
                    ,nivel
                    ,dotacao_atualizada
                    ,no_bimestre
                    ,ate_bimestre
                    ,ate_bimestre_exercicio_anterior
               FROM tmp_rreo_an7_despesa ORDER BY ordem';
  
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;


	DROP TABLE tmp_suplementacao_suplementada ; 
	DROP TABLE tmp_suplementacao_reduzida ; 
	DROP TABLE tmp_despesa_totais ; 
	DROP TABLE tmp_despesa ; 
	DROP TABLE tmp_rreo_an7_despesa ; 

    RETURN;
 
END;

$$ language 'plpgsql';

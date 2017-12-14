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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 14.
    * Data de Criação: 28/05/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-06.01.14

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_anexo14_despesas ( varchar,integer,varchar,varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    inBimestre     	ALIAS FOR $2;
    stCodEntidades 	ALIAS FOR $3;
    stCodRecursos 	ALIAS FOR $4;

    dtInicial  		varchar := '';
    dtFinal    		varchar := '';
    dtIniExercicio 	VARCHAR := '';
    
    arDatas 		varchar[] ;
    reRegistro 		record ;
    stSql 		varchar := '';

BEGIN

    arDatas := publico.bimestre ( stExercicio, inBimestre );   
    dtInicial := arDatas [ 0 ];
    dtFinal   := arDatas [ 1 ];
    
    dtIniExercicio := '01/01/' || stExercicio;

	-- TABELAS TEMPORARIAS
	
	-- Total das Suplementações	
    -- Suplementado
    
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_suplementada AS
               SELECT
                    sup.dt_suplementacao, 
                    sups.exercicio,                     
                    sups.cod_despesa, 
                    sum(sups.valor)  as vl_suplementado 
               FROM orcamento.suplementacao                as sup, 
                    orcamento.suplementacao_suplementada   as sups, 
                    orcamento.despesa                      as de 
               WHERE sup.exercicio           = '|| quote_literal(stExercicio) ||' 
                 AND sup.dt_suplementacao BETWEEN to_date('''|| dtIniExercicio ||''',''dd/mm/yyyy'') 
                                              AND to_date('''|| dtFinal ||''',''dd/mm/yyyy'') ';
    
    if ( stCodEntidades != '' ) then
        stSql := stSql || 'AND   de.cod_entidade IN (' || stCodEntidades || ' )';
    end if;
    
    stSql := stSql|| ' AND   sup.exercicio           = sups.exercicio
			AND   sup.cod_suplementacao   = sups.cod_suplementacao
			AND   sups.exercicio          = de.exercicio
			AND   sups.cod_despesa        = de.cod_despesa
		GROUP BY sups.cod_despesa, sups.exercicio, sup.dt_suplementacao;
	CREATE INDEX unq_tmp_suplementacao_suplementada ON tmp_suplementacao_suplementada (cod_despesa);
	';

    EXECUTE stSql;

    -- Reduzido
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_suplementacao_reduzida AS
    	SELECT
                sup.dt_suplementacao, 
                supr.exercicio, 
                supr.cod_despesa, 
    		sum(supr.valor) as vl_reduzido 
    	FROM 
    		orcamento.suplementacao as sup, 
    		orcamento.suplementacao_reducao as supr, 
    		orcamento.despesa as de 
    	WHERE 
    		sup.exercicio = '|| quote_literal(stExercicio) ||' AND 
    		sup.dt_suplementacao BETWEEN to_date('''|| dtIniExercicio ||''', ''dd/mm/yyyy'') 
                                         AND to_date('''|| dtFinal ||''', ''dd/mm/yyyy'')
	';
    if (stCodEntidades != '' ) then 
         stSql := stSql || ' AND de.cod_entidade IN (' ||  stCodEntidades ||' ) ';
    end if ;

    stSql := stSql || 'AND sup.exercicio = supr.exercicio AND 
    		sup.cod_suplementacao   = supr.cod_suplementacao AND 
    		supr.exercicio          = de.exercicio AND 
    		supr.cod_despesa        = de.cod_despesa 
    	GROUP BY supr.cod_despesa, sup.dt_suplementacao, supr.exercicio ;
    	
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
			de.exercicio    = '|| quote_literal(stExercicio) ||' AND 
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
			CAST(0.00 AS NUMERIC(14,2)) as vl_empenhado_bimestre, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_bimestre, 
			CAST(0.00 AS NUMERIC(14,2)) as despesas_empenhadas, 
			CAST(0.00 AS NUMERIC(14,2)) as liquidado_ate_bimestre,
			de.cod_recurso 
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
			de.exercicio = '|| quote_literal(stExercicio) ||' AND 
			de.cod_entidade IN ('||  stCodEntidades ||') 
		GROUP BY 
			de.cod_conta, 
			de.exercicio, 
			ocd.cod_estrutural,
			de.cod_recurso 
		ORDER BY 
			de.cod_conta, 
			de.exercicio 
	';
	
	EXECUTE stSql;
	
	--- FIM DAS TEMPORARIAS
    
	stSql := '
	CREATE TEMPORARY TABLE tmp_rreo_anexo14_despesa AS (

	SELECT * FROM 
		(SELECT 
			CAST(1 AS INTEGER) AS grupo , 
			ocd.cod_estrutural , 
			ocd.descricao , 
			publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
			COALESCE(CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)), 0.00 ) as dotacao_atualizada , 
			COALESCE((SELECT * FROM stn.fn_anexo14_despesas_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS despesas_empenhadas , 
            COALESCE((SELECT * FROM stn.fn_anexo14_despesas_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS liquidado_ate_bimestre 
		FROM 
			orcamento.conta_despesa ocd 
			LEFT JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' AND
			    tmp.cod_recurso IN (' || stCodRecursos || ') AND 
			    substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4%'' AND
			publico.fn_nivel(ocd.cod_estrutural) < 3 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND
			ocd.exercicio = ' || quote_literal(stExercicio) || '
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl
		
	UNION ALL
	
	SELECT * FROM 
		(SELECT 
			CAST(2 AS INTEGER) AS grupo , 
			ocd.cod_estrutural , 
			CAST ( ''Despesas Correntes dos Regimes de Previdência'' as varchar ) as descricao,
			publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
			COALESCE(CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)), 0.00) as dotacao_atualizada ,
			COALESCE((SELECT * FROM stn.fn_anexo14_despesas_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS despesas_empenhadas , 
            COALESCE((SELECT * FROM stn.fn_anexo14_despesas_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS liquidado_ate_bimestre 
		FROM 
			orcamento.conta_despesa ocd 
			LEFT JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' AND
			    tmp.cod_recurso IN (' || stCodRecursos || ') AND 
			    substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3%'' AND 
			publico.fn_nivel(ocd.cod_estrutural) < 2 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND
			ocd.exercicio = '|| quote_literal(stExercicio) ||' 
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl
		
	UNION ALL
	
	SELECT * FROM 
		(SELECT 
			CAST(2 AS INTEGER) AS grupo , 
			CAST ( ''3.X.0.0.00.00.00.00.00'' as varchar ) as cod_estrutural , 
			CAST ( ''Regime Geral de Previdência Social'' as varchar ) as descricao,
			2 AS nivel , 
			COALESCE(CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)), 0.00 )as dotacao_atualizada ,
			COALESCE((SELECT * FROM stn.fn_anexo14_despesas_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS despesas_empenhadas , 
            COALESCE((SELECT * FROM stn.fn_anexo14_despesas_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS liquidado_ate_bimestre 
		FROM 
			orcamento.conta_despesa ocd 
			LEFT JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' AND
			    tmp.cod_recurso IN (' || stCodRecursos || ') AND 
			    substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3%'' AND 
			publico.fn_nivel(ocd.cod_estrutural) < 2 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND
			ocd.exercicio = '|| quote_literal(stExercicio) ||'
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl
		
	UNION ALL
	
	SELECT
	        CAST(2 AS INTEGER) AS grupo , 
		    CAST ( ''3.Y.0.0.00.00.00.00.00'' as varchar ) as cod_estrutural , 
			CAST ( ''Regime Próprio dos Servidores Públicos'' as varchar ) as descricao,
			2 AS nivel , 
			''0.00'' AS dotacao_atualizada ,
			''0.00'' AS despesas_empenhadas , 
            ''0.00'' AS liquidado_ate_bimestre 
		
	) 
	';
	
	EXECUTE stSql;
		
	stSql := 'SELECT * FROM tmp_rreo_anexo14_despesa ORDER BY grupo, nivel';
	
  
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;


	DROP TABLE tmp_suplementacao_suplementada ; 
	DROP TABLE tmp_suplementacao_reduzida ; 
	DROP TABLE tmp_despesa_totais ; 
	DROP TABLE tmp_despesa ; 
	DROP TABLE tmp_rreo_anexo14_despesa ; 

    RETURN;
 
END;
   
$$ language 'plpgsql';

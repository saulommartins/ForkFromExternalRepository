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
    * Script de função PLPGSQL - Relatório Despesas Municipais com Saúde - Prefeitura de Mariana Pimentel.
    * Data de Criação: 12/06/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-02.01.01

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_saude_despesas( VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,CHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    stDataInicial  	ALIAS FOR $2;
    stDataFinal  	ALIAS FOR $3;
    stCodEntidades 	ALIAS FOR $4;
    stCodOrgao   	ALIAS FOR $5;
    chEstilo            ALIAS FOR $6;
    
    stCondicao          VARCHAR := '';
    reRegistro 		RECORD ;
    stSql 		VARCHAR := '';

BEGIN


IF chEstilo = 'S' THEN
    stCondicao := '
                JOIN contabilidade.plano_conta       as pc ON
                        pc.exercicio = ocd.exercicio AND
                        pc.cod_estrutural = ' || quote_literal('3.') || quote_literal('ocd.cod_estrutural') || '  AND
                        pc.exercicio = ' || quote_literal(stExercicio) || '
                JOIN contabilidade.sistema_contabil  as sc ON
                        pc.exercicio   = sc.exercicio   AND
                        pc.cod_sistema = sc.cod_sistema
                WHERE
                        NOT EXISTS ( SELECT 1
                                       FROM contabilidade.plano_analitica c_pa
                                      WHERE c_pa.cod_conta = pc.cod_conta
                                        AND c_pa.exercicio = pc.exercicio
                                        AND c_pa.exercicio = ' || quote_literal(stExercicio) || '
                                   ) AND
                  ';
END IF;

IF chEstilo = 'A' THEN
    stCondicao := '
                     WHERE
                  ';
END IF;
    -- TABELAS TEMPORARIAS
    -- Total das Suplementações	
    -- Suplementado
    
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_suplementada AS
               SELECT   sups.cod_despesa
                       ,sum(sups.valor)  as vl_suplementado
               FROM     orcamento.suplementacao                as sup
                       ,orcamento.suplementacao_suplementada   as sups
                       ,orcamento.despesa                      as de
               WHERE   sup.exercicio           = ' || quote_literal(stExercicio) || ' 
                 AND   sup.dt_suplementacao BETWEEN to_date('||quote_literal(stDataInicial)||'::varchar,''dd/mm/yyyy'') 
                                                AND to_date('||quote_literal(stDataFinal)||  '::varchar,''dd/mm/yyyy'')';
    
    if ( stCodEntidades != '' ) then
        stSql := stSql || 'AND   de.cod_entidade IN (' || stCodEntidades || ' )';
    end if;
    
    stSql := stSql|| ' AND sup.exercicio           = sups.exercicio
		       AND sup.cod_suplementacao   = sups.cod_suplementacao
		       AND sups.exercicio          = de.exercicio
		       AND sups.cod_despesa        = de.cod_despesa
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
    		sup.dt_suplementacao BETWEEN 	to_date('||quote_literal(stDataInicial)||', ''dd/mm/yyyy'') 
            	                     AND 	to_date('||quote_literal(stDataFinal)||', ''dd/mm/yyyy'')
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
			CAST(0.00 AS NUMERIC) as vl_empenhado_bimestre, 
			CAST(0.00 AS NUMERIC) as vl_liquidado_bimestre, 
			CAST(0.00 AS NUMERIC) as vl_empenhado_total, 
			CAST(0.00 AS NUMERIC) as vl_liquidado_total,
			de.num_orgao 
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
			ocd.cod_estrutural,
			de.num_orgao 
		ORDER BY 
			de.cod_conta, 
			de.exercicio 
	';
	
	EXECUTE stSql;
	
	--- FIM DAS TEMPORARIAS
	stSql := '
	CREATE TEMPORARY TABLE tmp_despesa_saude AS (
	
	SELECT * FROM  
		(SELECT
			CAST(1 AS INTEGER) as grupo , 
			ocd.cod_estrutural , 
			ocd.descricao , 
			publico.fn_nivel(ocd.cod_estrutural)  as nivel , 
			COALESCE((CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) as numeric) ), 0.00) as dotacao_atualizada , 
			COALESCE((SELECT * FROM orcamento.fn_despesa_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ' ||  quote_literal(stCodEntidades) ||', '|| quote_literal(stDataInicial)||', '|| quote_literal(stDataFinal) ||', '|| quote_literal(stCodOrgao) ||', true )), ' || quote_literal('0.00') ||') AS vl_empenhado , 
			COALESCE((SELECT * FROM orcamento.fn_despesa_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ' ||  quote_literal(stCodEntidades) ||', '|| quote_literal(stDataInicial)||', '|| quote_literal(stDataFinal) ||', '|| quote_literal(stCodOrgao) ||', true )), ' || quote_literal('0.00') ||') AS vl_liquidado ,
			COALESCE((SELECT * FROM orcamento.fn_despesa_paga( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ' ||  quote_literal(stCodEntidades) ||', '|| quote_literal(stDataInicial)||', '|| quote_literal(stDataFinal) ||', '|| quote_literal(stCodOrgao) ||', true )), '|| quote_literal('0.00') ||') AS vl_pago
		FROM 
		    orcamento.conta_despesa ocd 
			LEFT JOIN 
			tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||'|| quote_literal('%') || 'AND
				tmp.num_orgao IN ( ' || stCodOrgao || ' )
 		' || stCondicao || '
            ocd.exercicio = ' || quote_literal(stExercicio) || '
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural 
		) AS tbl
    WHERE
        dotacao_atualizada <> '|| quote_literal('0.00') ||' OR
        vl_empenhado       <> '|| quote_literal('0.00') ||' OR
        vl_liquidado       <> '|| quote_literal('0.00') ||' OR
        vl_pago            <> '|| quote_literal('0.00') ||'
	) 
	';

	EXECUTE stSql;
	
	
	stSql := 'SELECT *	FROM tmp_despesa_saude ORDER BY grupo, cod_estrutural';
	
  
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

	DROP TABLE tmp_suplementacao_suplementada ; 
	DROP TABLE tmp_suplementacao_reduzida ; 
	DROP TABLE tmp_despesa_totais ; 
	DROP TABLE tmp_despesa ; 
	DROP TABLE tmp_despesa_saude ; 

    RETURN;
 
END;

$$ language 'plpgsql';

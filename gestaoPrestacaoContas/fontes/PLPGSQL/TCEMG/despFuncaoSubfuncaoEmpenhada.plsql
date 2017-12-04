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
CREATE OR REPLACE FUNCTION tcemg.fn_desp_funcao_subfuncao_empenhada(inCodDespesa INTEGER, stExercicio VARCHAR, stEntidades VARCHAR, stDtIni VARCHAR, stDtFim VARCHAR) RETURNS NUMERIC(14,2) AS $$

DECLARE 
	stSQL 		VARCHAR;
	nuEmpenhado NUMERIC(14,2);
    crCursor 	REFCURSOR;

BEGIN 

	stSql := '
	SELECT 
		coalesce(sum(vl_total), 0.00) as vl_total 
	FROM 
		orcamento.conta_despesa ocd 
		INNER JOIN 
		orcamento.despesa ode ON 
			ode.exercicio = ocd.exercicio AND 
			ode.cod_conta = ocd.cod_conta 
		INNER JOIN 
		empenho.pre_empenho_despesa ped ON 
			ped.exercicio = ode.exercicio AND 
			ped.cod_despesa = ode.cod_despesa 
		INNER JOIN 
		empenho.pre_empenho pe ON 
			ped.exercicio = pe.exercicio AND 
			ped.cod_pre_empenho = pe.cod_pre_empenho 
		INNER JOIN 
		empenho.item_pre_empenho ipe ON 
			ipe.cod_pre_empenho = pe.cod_pre_empenho AND 
			ipe.exercicio = pe.exercicio 
		INNER JOIN 
		empenho.empenho e ON 
			e.exercicio = pe.exercicio AND 
			e.cod_pre_empenho = pe.cod_pre_empenho 
	WHERE 
		e.exercicio = ''' || stExercicio || ''' AND 
		e.cod_entidade IN (' || stEntidades || ') AND 
		e.dt_empenho BETWEEN to_date(''' || stDtIni || ''', ''dd/mm/yyyy'') AND 
							 to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') AND
        ode.cod_despesa = ' || inCodDespesa || '
        AND substring(ocd.cod_estrutural, 5, 3) <> ''9.1''
        ';

    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuEmpenhado;
    CLOSE crCursor;

	if (nuEmpenhado is null) then 
		nuEmpenhado := 0.00;
	end if;

    RETURN nuEmpenhado;
	
END;

$$ LANGUAGE 'plpgsql';


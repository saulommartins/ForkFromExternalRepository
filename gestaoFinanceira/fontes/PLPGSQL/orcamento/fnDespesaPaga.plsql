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
    * Script de função PLPGSQL - Relatório Despesas Municipais com Saúde - Despesas Pagas.
    * Data de Criação: 13/06/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-02.01.01

    $Id: $

*/

CREATE OR REPLACE FUNCTION orcamento.fn_despesa_paga(stMascRed VARCHAR, stExercicio VARCHAR, stEntidades VARCHAR, stDtIni VARCHAR, stDtFim VARCHAR, stCodOrgao VARCHAR, boIntra BOOLEAN) RETURNS NUMERIC(14,2) AS 
$$

DECLARE 
	stSQL 		   VARCHAR;
	reReg 		   RECORD;
	nuVlPago       NUMERIC(14,2);
	nuVlAnulado	   NUMERIC(14,2);
	nuTotal 	   NUMERIC(14,2);
    crCursor 	   REFCURSOR;

BEGIN 

	stSql := '
    SELECT
        coalesce(sum(nlp.vl_pago), 0.00) as vl_pago 
    FROM 
	    empenho.pre_empenho pe 
	LEFT JOIN 
	    ( SELECT 
              ped.exercicio, 
              ped.cod_pre_empenho, 
              cd.cod_estrutural 
	      FROM
              orcamento.conta_despesa cd 
	      INNER JOIN 
              empenho.pre_empenho_despesa ped ON 
                  ped.cod_conta   = cd.cod_conta AND 
                  ped.exercicio   = cd.exercicio 
		  INNER JOIN  
              orcamento.despesa d ON 
                  ped.cod_despesa = d.cod_despesa AND 
                  ped.exercicio   = d.exercicio
	 	  WHERE 
		      ped.exercicio = ''' || stExercicio || ''' AND
		      d.num_orgao IN ( ' || stCodOrgao || ' )
		) AS pedcd ON 
		    pe.exercicio = pedcd.exercicio AND 
		    pe.cod_pre_empenho = pedcd.cod_pre_empenho 
	INNER JOIN 
	    empenho.empenho e ON 
		    e.exercicio = pe.exercicio AND 
		    e.cod_pre_empenho = pe.cod_pre_empenho 
	INNER JOIN 
	    empenho.nota_liquidacao nl ON 
		    e.exercicio = nl.exercicio_empenho AND 
		    e.cod_entidade = nl.cod_entidade AND 
		    e.cod_empenho = nl.cod_empenho 
	INNER JOIN 
	    empenho.nota_liquidacao_paga nlp ON 
		    nl.exercicio = nlp.exercicio AND 
		    nl.cod_nota = nlp.cod_nota AND 
		    nl.cod_entidade = nlp.cod_entidade  
	WHERE 
        e.exercicio = ''' || stExercicio || ''' AND 
		e.cod_entidade IN (' || stEntidades || ') AND 
		to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtIni || ''',''dd/mm/yyyy'') AND
		                                                                       to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') AND 
		pedcd.cod_estrutural like ''' || stMascRed || '%'' ';
		
	-- não inclui as despesas intra-orçamentárias
	IF (boIntra = FALSE) THEN 
		stSql := stSql || ' AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''  ';
	END IF;

    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuVlPago;
    CLOSE crCursor;


	stSQL := '
	SELECT 
        coalesce(sum(nlpa.vl_anulado), 0.00) as valor 
    FROM 
	    empenho.pre_empenho pe 
	LEFT JOIN 
	    ( SELECT 
              ped.exercicio, 
              ped.cod_pre_empenho, 
              cd.cod_estrutural 
	      FROM
              orcamento.conta_despesa cd 
	      INNER JOIN 
              empenho.pre_empenho_despesa ped ON 
                  ped.cod_conta   = cd.cod_conta AND 
                  ped.exercicio   = cd.exercicio 
		  INNER JOIN  
              orcamento.despesa d ON 
                  ped.cod_despesa = d.cod_despesa AND 
                  ped.exercicio   = d.exercicio
	 	  WHERE 
		      ped.exercicio = ''' || stExercicio || ''' AND
		      d.num_orgao IN ( ' || stCodOrgao || ' )
		) AS pedcd ON 
		    pe.exercicio = pedcd.exercicio AND 
		    pe.cod_pre_empenho = pedcd.cod_pre_empenho 
	INNER JOIN 
	    empenho.empenho e ON 
		    e.exercicio = pe.exercicio AND 
		    e.cod_pre_empenho = pe.cod_pre_empenho 
	INNER JOIN 
	    empenho.nota_liquidacao nl ON 
		    e.exercicio = nl.exercicio_empenho AND 
		    e.cod_entidade = nl.cod_entidade AND 
		    e.cod_empenho = nl.cod_empenho 
	INNER JOIN 
	    empenho.nota_liquidacao_paga nlp ON 
		    nl.exercicio = nlp.exercicio AND 
		    nl.cod_nota = nlp.cod_nota AND 
		    nl.cod_entidade = nlp.cod_entidade 
	INNER JOIN 
	    empenho.nota_liquidacao_paga_anulada nlpa ON 
		    nlp.exercicio = nlpa.exercicio AND 
		    nlp.cod_nota = nlpa.cod_nota AND 
		    nlp.cod_entidade = nlpa.cod_entidade AND 
		    nlp.timestamp = nlpa.timestamp
	WHERE
		e.exercicio = ''' || stExercicio || ''' AND 
		e.cod_entidade IN (' || stEntidades || ') AND 
		to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtIni || ''',''dd/mm/yyyy'') AND
		                                                                       to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') AND 
		pedcd.cod_estrutural like ''' || stMascRed || '%'' ';
		
	-- não inclui as despesas intra-orçamentárias
	IF (boIntra = FALSE) THEN 
		stSql := stSql || ' AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''  ';
	END IF;

    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuVlAnulado;
    CLOSE crCursor;
    

	nuTotal := nuVlPago - nuVlAnulado;

	if (nuTotal is null) then 
		nuTotal := 0.00;
	end if;

    RETURN nuTotal;

END;

$$ LANGUAGE 'plpgsql';

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
CREATE OR REPLACE FUNCTION tcemg.fn_desp_funcao_subfuncao_anulada(inCodDespesa INTEGER, stExercicio VARCHAR, stEntidades VARCHAR, stDtIni VARCHAR, stDtFim VARCHAR) RETURNS NUMERIC(14,2) AS $$

DECLARE 
	stSQL 		VARCHAR;
	nuAnulado 	NUMERIC(14,2);
    crCursor 	REFCURSOR;

BEGIN 

	stSQL := '
    	SELECT coalesce(sum(eai.vl_anulado), 0.00) AS valor 
          FROM orcamento.conta_despesa ocd 
    INNER JOIN orcamento.despesa 
            ON orcamento.despesa.exercicio = ocd.exercicio 
           AND orcamento.despesa.cod_conta = ocd.cod_conta 
    INNER JOIN empenho.pre_empenho_despesa 
            ON pre_empenho_despesa.exercicio = orcamento.despesa.exercicio 
           AND pre_empenho_despesa.cod_despesa = orcamento.despesa.cod_despesa 
    INNER JOIN empenho.pre_empenho pe 
            ON pre_empenho_despesa.exercicio = pe.exercicio 
           AND pre_empenho_despesa.cod_pre_empenho = pe.cod_pre_empenho 
    INNER JOIN empenho.empenho e 
            ON e.exercicio = pe.exercicio 
           AND e.cod_pre_empenho = pe.cod_pre_empenho 
    INNER JOIN empenho.empenho_anulado ea 
            ON ea.exercicio = e.exercicio 
           AND ea.cod_entidade = e.cod_entidade 
           AND ea.cod_empenho = e.cod_empenho 		
    INNER JOIN empenho.empenho_anulado_item eai 
            ON eai.exercicio = ea.exercicio 
           AND eai.cod_entidade = ea.cod_entidade 
           AND eai.cod_empenho = ea.cod_empenho 
           AND eai.timestamp = ea.timestamp 
         WHERE e.exercicio = ''' || stExercicio || ''' 
           AND e.cod_entidade IN (' || stEntidades || ') 
           AND to_date( to_char( ea.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) 
    			BETWEEN to_date(''' || stDtIni || ''', ''dd/mm/yyyy'') 
                    AND to_date(''' || stDtFim || ''', ''dd/mm/yyyy'') 
           AND orcamento.despesa.cod_despesa = ' || inCodDespesa || '
           AND substring(ocd.cod_estrutural, 5, 3) <> ''9.1''
    ';
	
    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuAnulado;
    CLOSE crCursor;
    
	IF (nuAnulado IS null) THEN
		nuAnulado := 0.00;
	END IF;

    RETURN nuAnulado;
	
END;

$$ LANGUAGE 'plpgsql';

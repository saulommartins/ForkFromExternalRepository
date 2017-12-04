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
CREATE OR REPLACE FUNCTION stn.fn_rreo_valor_conta_uniao( stExerc VARCHAR, stTipo VARCHAR, stCodEstru VARCHAR, stEntidades VARCHAR, stDtInicial VARCHAR, stDtFinal VARCHAR, boIntra BOOLEAN, inGrupo INTEGER, inSubGrupo INTEGER , inItem INTEGER) RETURNS SETOF stn.tp_rreo_valor_conta AS $$
DECLARE 
	
	i 			INT;
	inAux 		INT;
	arCodEstru  VARCHAR[];
	low 		INT;
	high 		INT;
	stMascRed 	VARCHAR;
	arDatas 	VARCHAR[];
	stDtIni 	VARCHAR;
	stDtFim 	VARCHAR;
	stDtIniExer VARCHAR;
	inNivel 	INTEGER;
	stTabela 	VARCHAR;
	stSQL 		VARCHAR;
	reReg 		RECORD;
	tyRetorno   stn.tp_rreo_valor_conta%ROWTYPE;

BEGIN


	arCodEstru := string_to_array(stCodestru, '|');
	stMascRed  := publico.fn_mascarareduzida( stCodEstru );
	stDtIni    := stDtInicial;
	stDtFim    := stDtFinal;
	
	stDtIniExer := '01/01/' || stExerc;

	low  := replace(split_part(array_dims(arCodEstru),':',1),'[','')::INT;
	high := replace(split_part(array_dims(arCodEstru),':',2),']','')::INT;


    stSQL := '
    SELECT 
        CAST(' || inGrupo || ' AS INTEGER) AS grupo , 
        CAST(' || inSubGrupo || ' AS INTEGER) AS subgrupo , 
        CAST(' || inItem || ' AS INTEGER) AS item , 
        ocr.exercicio , 
        ocr.cod_conta , 
        CAST(publico.fn_nivel(ocr.cod_estrutural) AS INTEGER) AS nivel , 
        TRIM(ocr.cod_estrutural) AS cod_estrutural , 
        publico.fn_mascarareduzida(ocr.cod_estrutural) AS masc_red , 
        initcap(TRIM(ocr.descricao)) AS descricao , 
        CAST(''' || stTipo || ''' AS CHARACTER(1)) AS tipo , 
        
        COALESCE(orcamento.fn_receita_valor_previsto_uniao( ''' || stExerc || ''', publico.fn_mascarareduzida(ocr.cod_estrutural) , ''' || stEntidades || ''' ) , 0.00) AS ini , 
        CAST(0.00 AS NUMERIC(14,2)) AS cred_adi , 
        COALESCE(orcamento.fn_receita_valor_previsto_uniao( ''' || stExerc || ''', publico.fn_mascarareduzida(ocr.cod_estrutural) , ''' || stEntidades || ''' ) , 0.00) AS atu ,
        
        (COALESCE(orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural) , ''' || stDtIni || ''' , ''' || stDtFim || '''), 0.00)*-1) AS no_bi , 
        (COALESCE(orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural) , ''' || stDtIniExer || ''' , ''' || stDtFim || '''), 0.00)*-1) AS ate_bi , 
        CAST(0.00 AS NUMERIC(14,2)) AS pct 
    FROM 
        orcamento.conta_receita ocr 
    WHERE 
        ocr.exercicio = ''' || stExerc || ''' AND 
        ( ';

    FOR i IN low..high LOOP
        IF (I > 1) THEN 
            stSQL := stSQL || ' OR ';
        END IF;	
        
        stSQL := stSQL || ' ocr.cod_estrutural = ''' || arCodEstru[i] || ''' ';
        
    END LOOP;
        
    stSQL := stSQL || ' ) 
    GROUP BY ocr.exercicio, ocr.cod_conta, nivel, cod_estrutural, masc_red, descricao ';
	
	
	FOR reReg IN EXECUTE stSQL 	
	LOOP
		tyRetorno.grupo 	 := reReg.grupo;
		tyRetorno.subgrupo 	 := reReg.subgrupo;
		tyRetorno.item 		 := reReg.item;
		tyRetorno.exercicio 	 := reReg.exercicio;
		tyRetorno.cod_conta 	 := reReg.cod_conta;
		tyRetorno.nivel 	 := reReg.nivel;
		tyRetorno.cod_estrutural := reReg.cod_estrutural;
		tyRetorno.masc_red 	 := reReg.masc_red;
		tyRetorno.descricao 	 := reReg.descricao;
		tyRetorno.tipo 		 := reReg.tipo;
        
		if (stTipo = 'R') AND (SUBSTRING(reReg.cod_estrutural, 1, 1) = '9') THEN         
			tyRetorno.ini      	:= reReg.ini * (-1);
			tyRetorno.cred_adi  	:= reReg.cred_adi * (-1);
			tyRetorno.atu       	:= reReg.atu * (-1);
			tyRetorno.no_bi 	:= reReg.no_bi * (-1);
			tyRetorno.ate_bi 	:= reReg.ate_bi * (-1);
		ELSE 
			tyRetorno.ini       	:= reReg.ini;
			tyRetorno.cred_adi  	:= reReg.cred_adi;
			tyRetorno.atu       	:= reReg.atu;
			tyRetorno.no_bi 	:= reReg.no_bi;
			tyRetorno.ate_bi 	:= reReg.ate_bi;
		END IF;        

		
		IF (reReg.no_bi <> 0 AND reReg.atu <> 0) THEN 
			tyRetorno.pct 		:= ((reReg.no_bi / reReg.atu) * 100);
		ELSE 
			tyRetorno.pct 		:= 0.00;
		END IF;

	 	RETURN NEXT tyRetorno;

	END LOOP;
	
	RETURN;

END;
$$ LANGUAGE 'plpgsql';

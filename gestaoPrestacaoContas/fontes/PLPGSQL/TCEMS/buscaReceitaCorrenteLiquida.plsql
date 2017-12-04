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
    * Script de função PLPGSQL - Relatório STN - RGF - Anexo 1.
    * Data de Criação: 28/07/2011


    * @author Davi Ritter Aroldi

    * Casos de uso:

    $Id: $

*/

CREATE OR REPLACE FUNCTION tcems.fn_busca_receita_corrente_liquida ( varchar,varchar ) RETURNS NUMERIC(14,2) AS '
DECLARE
    stExercicio    	ALIAS FOR $1;
    dtFinal      	ALIAS FOR $2;
    
    stSql 		varchar := '''';
    stValor        	numeric(14,2) := 0.00;
    stWhere             varchar := '''';
    crCursor		REFCURSOR;

BEGIN
    IF (dtFinal = ''31/12/''||stExercicio) THEN
        stWhere := ''9,10,11,12'';
    ELSEIF (dtFinal = ''31/08/''||stExercicio) THEN
	stWhere := ''5,6,7,8'';
    ELSE
	stWhere := ''1,2,3,4'';
    END IF;
   
    stSql := ''
	SELECT sum(valor) as valor
	FROM tcems.receita_corrente_liquida
	WHERE mes in (''||stWhere||'')
	AND exercicio = ''''''||stExercicio||''''''
    '';
    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO stValor;
    CLOSE crCursor;
    
    RETURN stValor;
 
END;
   
'language 'plpgsql';

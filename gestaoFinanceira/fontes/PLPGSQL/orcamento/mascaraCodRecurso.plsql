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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* @author: Tonismar Régis Bernardo
* @date: 18/12/2007
*
* Casos de uso: uc-02.03.05
*
* $Id: mascaraCodRecurso.plsql 59612 2014-09-02 12:00:51Z gelson $
*/

create or replace function orcamento.fn_mascara_cod_recurso( integer, varchar ) returns  varchar as '

DECLARE
	inCodRecurso		ALIAS FOR $1;
	stExercicio 		ALIAS FOR $2;
	
	stMascaraRecurso    varchar := '''';
	stSql				varchar := '''';
	stNovoCodRecurso	varchar := '''';
	stMascara       	varchar := ''masc_recurso'';
	
BEGIN	

	SELECT 
		valor 			
	INTO
		stMascaraRecurso
	FROM 
		administracao.configuracao 
	WHERE 
			cod_modulo = 8 
		AND parametro= ''masc_recurso''
		AND exercicio=  stExercicio;
		
	
	stNovoCodRecurso := sw_fn_mascara_dinamica(stMascaraRecurso,inCodRecurso::varchar);
	
	RETURN stNovoCodRecurso;

END;
'LANGUAGE 'plpgsql';

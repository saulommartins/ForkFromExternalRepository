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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
* Caso de uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION buscaValorAtributoElemento( int,int,int,int,int,int,varchar )  RETURNS varchar AS '
DECLARE
    inInscricaoEconomica    ALIAS FOR $1;
    inCodAtividade          ALIAS FOR $2;
    inCodElemento           ALIAS FOR $3;
    inOcorrenciaElemento    ALIAS FOR $4;
    inOcorrenciaAtividade   ALIAS FOR $5;
    inCodModulo		    ALIAS FOR $6;
    stParametro		    ALIAS FOR $7;
    stRetorno               varchar;
    
BEGIN

	SELECT 
		atributo_elem_cad_economico_valor.valor
   	INTO
		stRetorno

	FROM
		economico.atributo_elem_cad_economico_valor

	INNER JOIN
		administracao.atributo_dinamico 
	ON
		atributo_elem_cad_economico_valor.cod_cadastro = atributo_dinamico.cod_cadastro
		AND  atributo_elem_cad_economico_valor.cod_atributo = atributo_dinamico.cod_atributo

	WHERE
		atributo_elem_cad_economico_valor.inscricao_economica = inInscricaoEconomica
		AND atributo_elem_cad_economico_valor.cod_atividade = inCodAtividade
		AND atributo_elem_cad_economico_valor.ocorrencia_atividade = inOcorrenciaAtividade
		AND atributo_elem_cad_economico_valor.cod_elemento = inCodElemento
		AND atributo_elem_cad_economico_valor.ocorrencia_elemento = inOcorrenciaElemento
		AND atributo_dinamico.nom_atributo LIKE ''%''||stParametro||''%''
		AND atributo_dinamico.cod_modulo = inCodModulo;



    RETURN stRetorno; 
END;
' LANGUAGE 'plpgsql';

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
* $Revision: 56934 $
* $Name$
* $Author: gelson $
* $Date: 2014-01-08 17:46:44 -0200 (Wed, 08 Jan 2014) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.6  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.5  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tceto.fn_retorno_atributo_normas(NUMERIC, NUMERIC, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodTipoNorma      ALIAS FOR $1;
    inCodNorma          ALIAS FOR $2;
    stNomAtributo       ALIAS FOR $3;
    crCursor            REFCURSOR;
    stSql               VARCHAR := '';
    stValor             VARCHAR := '';


BEGIN

    stSql := '
	SELECT
     		VALOR.valor
  	FROM
     		administracao.atributo_dinamico AS AD,
     		administracao.tipo_atributo AS TA,
     		normas.atributo_tipo_norma  AS ACA
     	LEFT JOIN
     		normas.atributo_norma_valor AS VALOR
  	ON (
		    ACA.cod_atributo    = VALOR.cod_atributo
          	AND ACA.cod_cadastro    = VALOR.cod_cadastro
            AND VALOR.cod_tipo_norma    = ACA.cod_tipo_norma
          	AND VALOR.timestamp::varchar||VALOR.cod_atributo::varchar IN (
             	SELECT
                	max(VALOR.timestamp)::varchar||VALOR.cod_atributo::varchar
             	FROM
                	normas.atributo_tipo_norma      AS ACA,
                	normas.atributo_norma_valor     AS VALOR,
                	administracao.atributo_dinamico AS AD,
                	administracao.tipo_atributo     AS TA
             	WHERE
                	    ACA.cod_atributo 	    = AD.cod_atributo
             		AND ACA.cod_atributo 	    = VALOR.cod_atributo
             		AND ACA.cod_cadastro 	    = VALOR.cod_cadastro
              		AND VALOR.cod_tipo_norma    = ACA.cod_tipo_norma
             		AND AD.cod_tipo             = TA.cod_tipo
             		AND ACA.ativo               = true
             		AND AD.cod_modulo           = 15
             		AND ACA.cod_cadastro        = 1
             	GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo  ,VALOR.cod_tipo_norma  ,VALOR.cod_norma
                )
              	AND VALOR.cod_tipo_norma    =  '|| inCodTipoNorma ||'
		        AND VALOR.cod_norma         =   '|| inCodNorma    ||'
     	)
 	WHERE
     		    ACA.cod_atributo 	= AD.cod_atributo
  		AND AD.cod_tipo  	= TA.cod_tipo
	  	AND ACA.ativo 		= true
  		AND AD.cod_modulo   	= 15
  		AND ACA.cod_cadastro	= 1
  		AND ACA.cod_tipo_norma  = '|| inCodTipoNorma ||'
  		AND AD.nom_atributo     = '|| quote_literal(stNomAtributo)||' ';


    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO stValor;
    CLOSE crCursor;

    RETURN stValor;
END;
$$ LANGUAGE 'plpgsql';

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
* Lucas Stephanou 10/03/2007
*
* $Revision: 23244 $
* $Name$
* $Author: domluc $
* $Date: 2007-06-13 18:36:03 -0300 (Qua, 13 Jun 2007) $
*
* Casos de uso: uc-02.04.33
*/
/*
$Log$
Revision 1.2  2007/06/13 21:36:03  domluc
Alterações para comportar arr. via banco em receitas orcamentarias e/ou extra-orcamentarias

Revision 1.1  2007/03/15 19:02:17  domluc
Caso de Uso 02.04.33

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_verifica_acrescimo( int, int , int, int , int, int) RETURNS BOOLEAN AS $$
DECLARE
    inCodCredito        ALIAS FOR $1; 
    inCodEspecie        ALIAS FOR $2; 
    inCodGenero         ALIAS FOR $3; 
    inCodNatureza       ALIAS FOR $4; 
    inCodAcrescimo      ALIAS FOR $5; 
    inCodTipo           ALIAS FOR $6; 
    inTeste             integer;
BEGIN

    select plano_analitica_credito_acrescimo.cod_credito
      into inTeste
      from contabilidade.plano_analitica_credito_acrescimo
     where plano_analitica_credito_acrescimo.cod_credito    = inCodCredito 
       and plano_analitica_credito_acrescimo.cod_especie    = inCodEspecie
       and plano_analitica_credito_acrescimo.cod_genero     = inCodGenero
       and plano_analitica_credito_acrescimo.cod_natureza   = inCodNatureza
       and plano_analitica_credito_acrescimo.cod_acrescimo  = inCodAcrescimo
       and plano_analitica_credito_acrescimo.cod_tipo       = inCodTipo
     limit 1;

IF NOT FOUND THEN
	select receita_credito_acrescimo.cod_credito
	  into inTeste
	  from orcamento.receita_credito_acrescimo
	  where receita_credito_acrescimo.cod_credito    = inCodCredito 
	    and receita_credito_acrescimo.cod_especie    = inCodEspecie
	    and receita_credito_acrescimo.cod_genero     = inCodGenero
	    and receita_credito_acrescimo.cod_natureza   = inCodNatureza
	    and receita_credito_acrescimo.cod_acrescimo  = inCodAcrescimo
	    and receita_credito_acrescimo.cod_tipo       = inCodTipo
	  limit 1;

	IF NOT FOUND THEN
 		return FALSE;
	ELSE
	 	return TRUE;
	END IF;

ELSE
 return TRUE;
END IF;

END;

$$ language 'plpgsql';

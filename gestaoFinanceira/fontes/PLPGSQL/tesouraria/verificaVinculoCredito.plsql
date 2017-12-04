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
* $Revision: 24264 $
* $Name$
* $Author: domluc $
* $Date: 2007-07-25 12:49:49 -0300 (Qua, 25 Jul 2007) $
*
* Casos de uso: uc-02.04.33
*/
/*
$Log$
Revision 1.2  2007/07/25 15:48:27  domluc
Add Verificação de  vinculo na Receita

Revision 1.1  2007/03/15 19:02:17  domluc
Caso de Uso 02.04.33

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_verifica_credito( int, int , int, int ) RETURNS BOOLEAN AS '
DECLARE
    inCodCredito        ALIAS FOR $1; 
    inCodEspecie        ALIAS FOR $2; 
    inCodGenero         ALIAS FOR $3; 
    inCodNatureza       ALIAS FOR $4; 
    inTeste             integer;
BEGIN

    select plano_analitica_credito.cod_credito
      into inTeste
      from contabilidade.plano_analitica_credito
     where plano_analitica_credito.cod_credito    = inCodCredito 
       and plano_analitica_credito.cod_especie    = inCodEspecie
       and plano_analitica_credito.cod_genero     = inCodGenero
       and plano_analitica_credito.cod_natureza   = inCodNatureza
     limit 1;
    
    IF NOT FOUND THEN
        select receita_credito.cod_credito
          into inTeste
          from orcamento.receita_credito
         where receita_credito.cod_credito    = inCodCredito
           and receita_credito.cod_especie    = inCodEspecie
           and receita_credito.cod_genero     = inCodGenero
           and receita_credito.cod_natureza   = inCodNatureza
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

'language 'plpgsql';

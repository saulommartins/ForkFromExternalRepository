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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_busca_nivel_valor.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.2  2006/09/15 10:19:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_nivel_valor(INT,INT,INT)  RETURNS integer AS '
DECLARE
    inCodLocalizacao           ALIAS FOR $1;
    inCodVigencia              ALIAS FOR $2;
    inCodNivel                 ALIAS FOR $3;
    inResultado                integer;
BEGIN
        SELECT INTO inResultado 
                    valor 
               from imobiliario.localizacao_nivel 
              where cod_localizacao = inCodLocalizacao 
                and cod_vigencia = inCodVigencia
                and cod_nivel    = inCodNivel;
    RETURN inResultado;
END;
' LANGUAGE 'plpgsql';

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
* $Revision: 3077 $
* $Name$
* $Author: pablo $
* $Date: 2005-11-29 14:53:37 -0200 (Ter, 29 Nov 2005) $
*
* Casos de uso: uc-01.01.00
*/

--   CREATE OR REPLACE FUNCTION fn_strcount(varchar,varchar) RETURNS integer AS '
--   DECLARE
--    stString    ALIAS FOR $1;
--    stSubString ALIAS FOR $2;
--       inOut       integer := 0;
--
--   BEGIN
--       inOut := substring( array_dims(string_to_array(stString,stSubString)) from 4 for ( length(array_dims(string_to_array(stString,stSubString)))-4) );
--    RETURN inOut-1;
--   END;
--   ' LANGUAGE 'plpgsql';
CREATE OR REPLACE FUNCTION publico.fn_strcount(varchar,varchar) RETURNS integer AS '
DECLARE
   stString    ALIAS FOR $1;
   stSubString ALIAS FOR $2;

BEGIN
   RETURN publico.fn_countelements(stString,stSubString)-1;
END;
' LANGUAGE 'plpgsql';

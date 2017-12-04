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
/* string_to_integer
 * 
 * Data de Criação : 15/01/2009


 * @author Analista : Gelson W
 * @author Desenvolvedor : Luiz Felipe P Teixeira
 
 * @package URBEM
 * @subpackage 

 $Id:$

 */

CREATE OR REPLACE FUNCTION string_to_integer(VARCHAR) RETURNS INTEGER AS $$
DECLARE
    stTemp     VARCHAR:=$1;
    stRetorno  INTEGER;
BEGIN
    stTemp := upper(trim(stTemp));              --remove acentuação da string 
    stTemp := translate(stTemp, ' .,-/*~^|;_', '');                 --remove caracteres especiais
    stTemp := translate(stTemp, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '');  --remove letras

    IF stTemp IS NULL or trim(stTemp) = '' THEN
        stRetorno := 0;
    ELSE 
        stRetorno := stTemp::integer;
    END IF;

    RETURN stRetorno;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

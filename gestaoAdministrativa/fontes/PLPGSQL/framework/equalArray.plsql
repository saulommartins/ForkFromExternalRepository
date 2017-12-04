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
* Efetua a comparacao de dois Arrays, verificando se os elementos do array2 existem no array1 e vice-versa.
* So compara os valores.
* Ex:
* SELECT publico.equal_array(array[1,2,3,3],array[1,2,3]);
* equal_array
*-------------
* t
*(1 registro)
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 8291 $
* $Name$
* $Author: diegovic $
* $Date: 2006-04-06 09:54:46 -0300 (Qui, 06 Abr 2006) $
*
* Casos de uso: uc-01.01.00
*/


CREATE OR REPLACE FUNCTION publico.equal_array(anyarray,anyarray) RETURNS boolean AS '
    DECLARE
    BEGIN
        IF publico.in_array( $1, $2 ) = true AND publico.in_array( $2, $1 ) = true THEN
            return TRUE;
        ELSE
            return FALSE;
        END IF;
     END;
' LANGUAGE 'plpgsql';

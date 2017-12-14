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
* Efetua a comparacao do array2 com o array1, verificando se os elementos do array2 existem no array1.
* So compara os valores.
* Ex:
* SELECT publico.in_array(array[1,3,2],array[1,2]);
* in_array
*----------
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


CREATE OR REPLACE FUNCTION publico.in_array(anyarray,anyarray) RETURNS boolean AS '
    DECLARE
        retorno     BOOLEAN := FALSE;
        iCount1     INTEGER := 1;
        iCount2     INTEGER := 1;
    BEGIN
        
        WHILE $2[ iCount2 ] IS NOT NULL LOOP
            iCount1  := 1;
            retorno  := FALSE;
            WHILE $1[ iCount1 ] IS NOT NULL LOOP
                IF $1[ iCount1 ] = $2[ iCount2 ] THEN
                    retorno := TRUE;
                END IF;
                iCount1 := iCount1 + 1;
            END LOOP;
            IF retorno = FALSE THEN
                return retorno;
            END IF;
            iCount2 := iCount2 + 1;
        END LOOP;
         
        RETURN retorno;
     END;
' LANGUAGE 'plpgsql';


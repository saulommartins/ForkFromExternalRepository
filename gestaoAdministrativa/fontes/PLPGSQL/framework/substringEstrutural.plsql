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
* $Revision: 4185 $
* $Name$
* $Author: galvao $
* $Date: 2005-12-21 15:03:49 -0200 (Qua, 21 Dez 2005) $
*
* Casos de uso: uc-01.01.00
*/

-- Criaçao da função publico.substring_estrutural.
CREATE OR REPLACE FUNCTION publico.substring_estrutural(VARCHAR, VARCHAR, INTEGER) RETURNS VARCHAR AS '
DECLARE
    stEstrutural        ALIAS FOR $1;
    stDelimitador       ALIAS FOR $2;
    inNivel             ALIAS FOR $3;
    arEstrutural        VARCHAR[];
    stRetorno           VARCHAR := '''';
    inCount             INTEGER := 1;
BEGIN
        arEstrutural := string_to_array(stEstrutural,stDelimitador);
        WHILE inCount <= inNivel LOOP
            stRetorno := stRetorno || arEstrutural[inCount];
            IF inCount <> inNivel THEN
                stRetorno := stRetorno || stDelimitador;
            END IF;
            inCount := inCount + 1;
        END LOOP;

    RETURN stRetorno;
END;
'language 'plpgsql';


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
* $Revision: 28841 $
* $Name$
* $Author: rodrigosoares $
* $Date: 2008-03-28 09:44:54 -0300 (Sex, 28 Mar 2008) $
*
* Casos de uso: uc-01.01.00
*/
CREATE OR REPLACE FUNCTION publico.fn_nivel(varchar) RETURNS integer AS '
DECLARE
   stString    ALIAS FOR $1;
    inOut       integer := 0;

    inCount     integer := 0;
    inValue     integer := 0;
    chDelimita  char := ''.'';
    arString    varchar[];
    stTmp       varchar;
    boTipo      varchar := ''string'';
BEGIN
    inCount  := publico.fn_countelements(stString,chDelimita);
    arString := string_to_array(stString,chDelimita);
    WHILE inCount > 0 LOOP
        stTmp := trim(arString[inCount]);
        IF cast(stTmp as varchar) ~ ''[(A-Za-z)(/)]'' or abs(to_number(stTmp,''999999999999'')) <> 0 THEN
                inOut   := inCount;
                inCount := 0;
        END IF;
        inCount := inCount - 1;
    END LOOP;
   RETURN inOut;
END;
' LANGUAGE 'plpgsql';

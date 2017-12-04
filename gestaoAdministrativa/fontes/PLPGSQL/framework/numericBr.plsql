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
--
---- Função que altera o valor numérico no formato EN p/ BR
--
CREATE OR REPLACE FUNCTION publico.fn_numeric_br(NUMERIC) RETURNS VARCHAR AS $$
DECLARE
    nuParametro     ALIAS FOR $1;
    stOut           VARCHAR := '';
    stAux           VARCHAR;
    inLoop          INTEGER;
    inCount         INTEGER := 4;
BEGIN
    inLoop := length(nuParametro::VARCHAR);
    WHILE inLoop > 0 LOOP
        stAux = substr(nuParametro::VARCHAR,inLoop,1);
        IF stAux = cast('.' as VARCHAR) THEN
            stOut   := ','||stOut;
            inCount := 0;
        ELSIF inCount = 3 and inLoop > 1 THEN
            stOut   := '.' || stAux || stOut;
            inCount := 0;
        ELSE
            stOut := stAux || stOut;
        END IF;
            inCount := inCount + 1;
            inLoop  := inLoop  - 1;
    END LOOP;
    RETURN stOut;
END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION publico.fn_numeric_br(VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    nuParametroVar  ALIAS FOR $1;
    stOut           VARCHAR := '';
    stAux           VARCHAR;
    inLoop          INTEGER;
    inCount         INTEGER := 4;
    nuParametro     NUMERIC := 0;
BEGIN
    nuParametro := nuParametroVar;
    inLoop      := length(nuParametro::VARCHAR);
    WHILE inLoop > 0 LOOP
        stAux = substr(nuParametro::VARCHAR,inLoop,1);
        IF stAux = cast('.' as VARCHAR) THEN
            stOut   := ','||stOut;
            inCount := 0;
        ELSIF inCount = 3 and inLoop > 1 THEN
            stOut   := '.' || stAux || stOut;
            inCount := 0;
        ELSE
            stOut := stAux || stOut;
        END IF;
            inCount := inCount + 1;
            inLoop  := inLoop  - 1;
    END LOOP;
    RETURN stOut;
END;
$$ LANGUAGE 'plpgsql';

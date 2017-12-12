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
* $Revision: 29158 $
* $Name$
* $Author: rgarbin $
* $Date: 2008-04-11 17:21:13 -0300 (Sex, 11 Abr 2008) $
*
* Casos de uso: uc-01.01.00
*/

-- Função q formata numerico para valor monetário.
CREATE OR REPLACE FUNCTION to_real(NUMERIC) RETURNS VARCHAR AS $$

DECLARE
    nuValorNumeric        NUMERIC(14,2) := $1;
    mascara               VARCHAR = '';
BEGIN
    IF nuValorNumeric IS NOT NULL THEN
        mascara := repeat('999,',(char_length(nuValorNumeric::varchar)-3)/3)||'990.99';
        RETURN selectIntovarchar('SELECT trim(translate(to_char('||nuValorNumeric||', '|| quote_literal(mascara) ||'), '',.'', ''.,''))');
    ELSE
        RETURN '';
    END IF;
END;
$$LANGUAGE 'plpgsql';

-- Função q formata numerico para valor monetário conforme marcara passada.
CREATE OR REPLACE FUNCTION to_real(NUMERIC, VARCHAR) RETURNS VARCHAR AS $$

DECLARE
    nuValorNumeric        NUMERIC := $1;
    mascara               VARCHAR := $2;
BEGIN
    IF nuValorNumeric IS NOT NULL THEN
        RETURN selectIntovarchar('SELECT trim(translate(to_char('||nuValorNumeric||', '|| quote_literal(mascara) ||'), '',.'', ''.,''))');
    ELSE
        RETURN '';
    END IF;
END;
$$LANGUAGE 'plpgsql';
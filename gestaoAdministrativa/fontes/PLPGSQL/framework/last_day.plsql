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
* $Revision:
* $Name$
* $Author: rgarbin $
* $Date: 2008-08-29 16:00:00 -0300 (Sex, 11 Abr 2008) $
*
* Casos de uso:
*/

CREATE OR REPLACE FUNCTION last_day(date) RETURNS DATE AS $$
DECLARE
    stdata      ALIAS FOR $1;
    stSQL       VARCHAR='';
    retorno     DATE;
    crCursor    REFCURSOR;
BEGIN
    stSQL := 'select cast(date_trunc(''month'', '''||stdata||'''::date) + ''1 month''::interval as date) - 1';

    OPEN crCursor FOR EXECUTE stSQL;
        FETCH crCursor INTO retorno;
    CLOSE crCursor;

    RETURN trim(retorno::varchar)::date;
END;
$$LANGUAGE 'plpgsql';

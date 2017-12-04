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
* script de funcao PLSQL
*
* URBEM Solugues de Gestco Pzblica Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/05/23 10:50:00 $
*
* Caso de uso: uc-04.05.00
*/


CREATE OR REPLACE FUNCTION adiciona_meses(date,integer) RETURNS date AS $$
DECLARE
    dtData      ALIAS FOR $1;
    inMeses     ALIAS FOR $2;
    stSQL       VARCHAR='';
    stData      VARCHAR='';
BEGIN
    stSQL := 'SELECT to_char(('|| quote_literal(dtData) ||' ::date + interval '|| quote_literal(inMeses) ||' month), ''dd/mm/yyyy'')';
    stData := selectintovarchar(stSql);
    return to_date(stData,'dd/mm/yyyy');
END;
$$LANGUAGE 'plpgsql';

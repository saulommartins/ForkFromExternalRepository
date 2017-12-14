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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: diff_datas_em_meses.plsql 64141 2015-12-08 16:23:59Z evandro $
*
* Caso de uso: uc-05.03.00
*/

CREATE OR REPLACE FUNCTION diff_datas_em_meses(date,date) RETURNS integer as $$
DECLARE
    dtInicio    ALIAS FOR $1;
    dtFim       ALIAS FOR $2;
    inAnoInicio     INTEGER;
    inAnoFim        INTEGER;
    inDiff          INTEGER;

BEGIN

    inDiff      := (extract (month from dtFim)) - (extract (month from dtInicio));
    inAnoInicio := extract(year from dtInicio);
    inAnoFim    := extract(year from dtFim);
    
    IF ( inAnoFim > inAnoInicio ) THEN
        inDiff := (inDiff * 1) + (inAnoFim - inAnoInicio) * 12;
    ELSE
        inDiff := inDiff;
    END IF;

    RETURN inDiff; 

END;
$$ LANGUAGE 'plpgsql';

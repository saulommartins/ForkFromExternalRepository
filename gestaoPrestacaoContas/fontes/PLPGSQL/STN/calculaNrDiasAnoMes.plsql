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
-- script de funcao PLSQL
 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br

-- $Revision: 59612 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/12/07 10:50:00 $

-- Caso de uso: uc-4.5.48

-- Objetivo: Recebe o ano e o mes e retorna o nr. de dias do mesmo


create or replace function stn.calculaNrDiasAnoMes(integer,integer) RETURNS integer as '

DECLARE
   inAno                      ALIAS FOR $1;
   inMes                      ALIAS FOR $2;
   inDias                     INTEGER := 0;
   dtData                     Date;
   stData                     VARCHAR;
   inMes1                     INTEGER;
   inAno1                     INTEGER;
BEGIN

   if ( inMes >= 1 and inMes <= 12 ) then

       if ( inMes = 12 ) then
           inMes1 := 01;
           inAno1 := inAno+1;
       else
           inMes1 := inMes +1;
           inAno1 := inAno;
       end if;
       stData := ''01/''||inMes1::varchar||''/''||inAno1;
       dtData := to_date(stData, ''dd/mm/yyyy'');

       inDias := coalesce(extract(day from date (dtData - integer ''1''))::integer,0);
   end if;

   RETURN inDias;
END;

' LANGUAGE 'plpgsql';




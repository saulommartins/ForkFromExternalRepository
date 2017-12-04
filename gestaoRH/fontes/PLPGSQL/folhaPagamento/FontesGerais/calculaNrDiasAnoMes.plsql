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

-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/12/07 10:50:00 $

-- Caso de uso: uc-04.05.48

-- Objetivo: Recebe o ano e o mes e retorna o nr. de dias do mesmo




CREATE OR REPLACE FUNCTION calculaNrDiasAnoMes(integer,integer) RETURNS integer as '

DECLARE
   inAno                      ALIAS FOR $1;
   inMes                      ALIAS FOR $2;
   inDias                     INTEGER := 0;
   dtData                     Date;
   stData                     VARCHAR;
   inMes1                     INTEGER;
   inAno1                     INTEGER;
BEGIN
   IF ( inMes >= 1 AND inMes <= 12 ) THEN

       IF ( inMes = 12 ) THEN
           inMes1 := 01;
           inAno1 := inAno+1;
       ELSE
           inMes1 := inMes +1;
           inAno1 := inAno;
       END IF;
       stData := ''01/''||inMes1::varchar||''/''||inAno1;
       dtData := to_date(stData, ''dd/mm/yyyy'');

       inDias := COALESCE(extract(day FROM date (dtData - integer ''1''))::integer,0);

   END IF;

   RETURN inDias;
END;

' LANGUAGE 'plpgsql';




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
* $Author: $
* $Date: $
*
* Casos de uso:
*/

CREATE OR REPLACE FUNCTION next_useful_day( dtToday DATE ) RETURNS date as $$
DECLARE
  nextDate date;
  weekDay integer:=-1;
  nextDateFeriado date;
BEGIN
 -- verificar dia da semana
  nextDate = dtToday + 1;
  IF extract(dow from nextDate) = 0 THEN
     nextDate = nextDate + 1;
  ELSEIF extract (dow from nextDate) = 6 THEN
     nextDate = nextDate + 2;
  END IF;

-- verificar se a data é feriado
  nextDateFeriado = nextDate;
  WHILE nextDateFeriado IS NOT NULL LOOP
    SELECT dt_feriado INTO nextDateFeriado FROM calendario.feriado WHERE dt_feriado = nextDate;
    IF nextDateFeriado IS NOT NULL THEN
       nextDate = nextDateFeriado + 1;
       IF extract(dow from nextDate) = 0 THEN
          nextDate = nextDate + 1;
       ELSEIF extract (dow from nextDate) = 6 THEN
          nextDate = nextDate + 2;
       END IF;

    END IF;
  END LOOP;  
  
  RETURN nextDate;   
END;
$$LANGUAGE 'plpgsql'

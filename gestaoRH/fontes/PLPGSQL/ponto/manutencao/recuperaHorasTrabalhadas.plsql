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
/* pega0DiasPorCargaHoraria
 * 
 * Data de Criação   : 29/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Rafael Luis de Souza Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION recuperaHorasTrabalhadas(VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    parHorarios         VARCHAR:=$1;
    arHorarios          VARCHAR[];
    arHorasTrab         VARCHAR[];
    inContador          INTEGER:=1;
    inCont              INTEGER:=1;
    stHorasAcumuladas   VARCHAR:='00:00';
    stSQL               VARCHAR;
    reRegistro          RECORD;
BEGIN
    arHorarios := string_to_array(parHorarios, '-');

    -- Joga no array arHorasTrab as horas trabalhadas
    LOOP
    IF arHorarios[inContador+1] IS NULL THEN
       EXIT;
    END IF;

        stSQL := 'SELECT to_char(interval '||quote_literal(trim(arHorarios[inContador+1]))||' - interval '||quote_literal(trim(arHorarios[inContador]))||', ''hh24:mi'') as horas_trabalhadas';

        FOR reRegistro IN  EXECUTE stSQL
        LOOP
            arHorasTrab[inCont] := reRegistro.horas_trabalhadas;
            inCont := inCont + 1;
        END LOOP;  

        inContador := inContador + 2;
    END LOOP;

    -- Somando as horas trabalhadas
    inContador := 1;
    LOOP
        IF arHorasTrab[inContador] IS NULL THEN
        EXIT;
        END IF;
    
        stSQL := 'SELECT to_char(interval '||quote_literal(arHorasTrab[inContador])||' + interval '||quote_literal(stHorasAcumuladas)||', ''hh24:mi'') as soma_horas_trabalhadas';
    
        FOR reRegistro IN  EXECUTE stSQL    
        LOOP
            stHorasAcumuladas := reRegistro.soma_horas_trabalhadas;
        END LOOP; 
        
        inContador := inContador + 1;   
    END LOOP;

    RETURN stHorasAcumuladas;
END
$$ LANGUAGE 'plpgsql';

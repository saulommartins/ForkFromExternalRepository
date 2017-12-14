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

CREATE OR REPLACE FUNCTION divideHorasTrabEmTurnos(VARCHAR,VARCHAR) RETURNS VARCHAR[][] as $$
DECLARE
    stHorarios          ALIAS FOR $1;
    stHoraPadrao        ALIAS FOR $2;    
    arHorarios          varchar[];
    arPrimeiroTurno     varchar[];
    arSegundaTurno      varchar[];
    arTurnos            varchar[][];
    inIndex             INTEGER:=1;
    inPrimeiroTurno     INTEGER:=1;
    inSegundoTurno      INTEGER:=1;
    inUltimoBatida      INTEGER;
BEGIN
    arHorarios := string_to_array(stHorarios,'-');

    WHILE arHorarios[inIndex] IS NOT NULL LOOP
        IF arHorarios[inIndex]::time <= stHoraPadrao::time THEN
            arPrimeiroTurno[inPrimeiroTurno] := arHorarios[inIndex];
            inUltimoBatida := inIndex;
            inPrimeiroTurno := inPrimeiroTurno + 1;
        ELSE
            IF inUltimoBatida%2 = 1 AND inSegundoTurno = 1 THEN
                arPrimeiroTurno[inPrimeiroTurno] := stHoraPadrao;
                arSegundaTurno[inSegundoTurno]   := stHoraPadrao;
                inSegundoTurno                   := inSegundoTurno + 1;
            END IF;
            arSegundaTurno[inSegundoTurno] := arHorarios[inIndex];
            inSegundoTurno := inSegundoTurno + 1;

        END IF;
        --Entrada
        inIndex := inIndex + 1;
    END LOOP;

    arTurnos[1] = arPrimeiroTurno;
    arTurnos[2] = arSegundaTurno;
    return arTurnos;
END
$$ LANGUAGE 'plpgsql';

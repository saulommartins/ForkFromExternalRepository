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

CREATE OR REPLACE FUNCTION recuperaHorasTrabalhadasEmHorarioPadrao(VARCHAR, INTEGER, VARCHAR, VARCHAR, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    parData              VARCHAR:=$1;
    inCodContrato        INTEGER:=$2;
    stEntidade           VARCHAR:=$3;
    parHorarioPadrao     VARCHAR:=$4;
    parHorarioTrabalhado VARCHAR:=$5;
    stSQL                VARCHAR:='';
    stHorarioPadrao      VARCHAR:='';
    stHorarioTrabalhado  VARCHAR:='';
    carga_horaria_padrao VARCHAR:='00:00';
    arHorarioPadrao      VARCHAR[];
    arHorarioTrabalhado  VARCHAR[];
    arHorarioTurnos      VARCHAR[][];
    arHorarioPadraoTurno VARCHAR[];
BEGIN
    IF (trim(parHorarioPadrao)='') THEN
        stHorarioPadrao  := recuperaHorarioPadrao(parData, inCodContrato, stEntidade);
    ELSE
        stHorarioPadrao := parHorarioPadrao;
    END IF;

    IF (trim(parHorarioTrabalhado)='') THEN
        stHorarioTrabalhado := recuperaHorarioTrabalhado(parData, inCodContrato, stEntidade);
    ELSE
        stHorarioTrabalhado := parHorarioTrabalhado;
    END IF;


    IF trim(stHorarioPadrao) != '' THEN

        arHorarioPadrao := string_to_array(stHorarioPadrao, '-');
        arHorarioTrabalhado := string_to_array(stHorarioTrabalhado, '-');

        IF arHorarioPadrao[3] IS NOT NULL AND arHorarioPadrao[4] IS NOT NULL THEN 
            -- 4 horarios
            arHorarioTurnos := divideHorasTrabEmTurnos(stHorarioTrabalhado,arHorarioPadrao[2]);
             
            arHorarioPadraoTurno[1] := arHorarioPadrao[1];
            arHorarioPadraoTurno[2] := arHorarioPadrao[2];                        
            arHorarioTrabalhado := arHorarioTurnos[1];
            carga_horaria_padrao := processaHorasTrabalhadas(arHorarioTrabalhado, arHorarioPadraoTurno);

            arHorarioPadraoTurno[1] := arHorarioPadrao[3];
            arHorarioPadraoTurno[2] := arHorarioPadrao[4];
            arHorarioTrabalhado := arHorarioTurnos[2];
            stSQL := 'SELECT to_char((interval '||quote_literal(carga_horaria_padrao)||' + '||quote_literal(processaHorasTrabalhadas(arHorarioTrabalhado, arHorarioPadraoTurno))||'), ''hh24:mi'')';            
            carga_horaria_padrao := selectintovarchar(stSQL);
        ELSE
            carga_horaria_padrao := processaHorasTrabalhadas(arHorarioTrabalhado, arHorarioPadrao);
        END IF;
    END IF;
  
    RETURN carga_horaria_padrao;
END
$$ LANGUAGE 'plpgsql';

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
CREATE OR REPLACE FUNCTION recuperaHorasExtrasNoturnasRelatorioEspelhoPonto(VARCHAR, VARCHAR, VARCHAR) RETURNS VARCHAR as $$
DECLARE
    stHorarioTrabalhado              ALIAS FOR $1; -- Horas Batidas dentro do adicional noturno
    stHorarioAdicionalNoturno        ALIAS FOR $2; -- Horário do adicional noturno
    stGradeHorario                   ALIAS FOR $3;
    stSQL                            VARCHAR;
    carga_horaria                    VARCHAR;
    arHorarioTrabalhado              VARCHAR[];
    arHorarioAdicionalNoturno        VARCHAR[];
    arGradeHorario                   VARCHAR[];
    arTemp                           VARCHAR[];
    inIndex                          INTEGER:=1;

BEGIN
    arHorarioAdicionalNoturno := string_to_array(stHorarioAdicionalNoturno, '-');
    arHorarioTrabalhado := string_to_array(stHorarioTrabalhado, '-');
    arGradeHorario :=  string_to_array(stGradeHorario, '-');
    
    IF arHorarioAdicionalNoturno[2]::time > arGradeHorario[1]::time THEN
        arHorarioAdicionalNoturno[2] := arGradeHorario[1];
    END IF;
    
    IF arHorarioAdicionalNoturno[3]::time < arGradeHorario[4]::time THEN
        arHorarioAdicionalNoturno[3] := arGradeHorario[4];
    END IF;

    arTemp[1] := arHorarioAdicionalNoturno[1];
    arTemp[2] := arHorarioAdicionalNoturno[2];  
    
    carga_horaria := processaHorasTrabalhadas(arHorarioTrabalhado, arTemp);

    arTemp[1] := arHorarioAdicionalNoturno[3];
    arTemp[2] := arHorarioAdicionalNoturno[4];
    carga_horaria := selectIntoVarchar('SELECT to_char(INTERVAL '|| quote_literal(carga_horaria) ||' + INTERVAL '|| quote_literal(processaHorasTrabalhadas(arHorarioTrabalhado, arTemp)) ||', ''hh24:mi'') ');
    return carga_horaria;
END 
$$ LANGUAGE 'plpgsql';

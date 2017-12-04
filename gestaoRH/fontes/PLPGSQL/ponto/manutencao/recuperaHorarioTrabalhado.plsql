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

CREATE OR REPLACE FUNCTION recuperaHorarioTrabalhado(VARCHAR, INTEGER, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    parData             VARCHAR:=$1;
    inCodContrato       INTEGER:=$2;
    stEntidade          VARCHAR:=$3;
    stSQL               VARCHAR:='';
    stHorario           VARCHAR:= '';
    reRegistro          RECORD;
    inCodPonto          INTEGER;
BEGIN
    --Busca Horarios
    stSQL := 'SELECT to_char(relogio_ponto_horario.hora,''HH24:mi'') as hora
                FROM ponto'||stEntidade||'.relogio_ponto_dias
          INNER JOIN ponto'||stEntidade||'.relogio_ponto_horario
                  ON relogio_ponto_dias.cod_ponto = relogio_ponto_horario.cod_ponto
                 AND relogio_ponto_dias.cod_contrato = relogio_ponto_horario.cod_contrato
          INNER JOIN ( SELECT cod_contrato
                            , cod_ponto
                            , MAX(timestamp) as timestamp
                         FROM ponto'||stEntidade||'.relogio_ponto_horario
                     GROUP BY cod_contrato
                            , cod_ponto) as max_relogio_ponto_horario
                  ON relogio_ponto_horario.cod_contrato = max_relogio_ponto_horario.cod_contrato
                 AND relogio_ponto_horario.cod_ponto = max_relogio_ponto_horario.cod_ponto
                 AND relogio_ponto_horario.timestamp = max_relogio_ponto_horario.timestamp
               WHERE relogio_ponto_dias.dt_ponto = '||quote_literal(to_date(parData,'dd/mm/yyyy'))||'
                 AND relogio_ponto_dias.cod_contrato = '||inCodContrato||'
            ORDER BY relogio_ponto_horario.hora';
 
    FOR reRegistro IN EXECUTE stSQL LOOP
        stHorario  := stHorario || reRegistro.hora ||' - ';
    END LOOP;

    IF trim(stHorario) != '' THEN
        stHorario := substr(stHorario,1,char_length(stHorario)-3);
    END IF;
        
    IF trim(stHorario) = '' THEN
        stSQL := 'SELECT cod_ponto 
                    FROM ponto'||stEntidade||'.importacao_ponto
                    WHERE cod_contrato = '||inCodContrato||'
                        AND dt_ponto = '||quote_literal(to_date(parData,'dd/mm/yyyy'))||'';

        inCodPonto :=  selectintointeger(stSQL);
        IF inCodPonto IS NOT NULL THEN
            stHorario := recuperaListaHorariosContrato(inCodContrato, inCodPonto, stEntidade);
        END IF; 
    END IF;

    RETURN stHorario;
END 
$$ LANGUAGE 'plpgsql';

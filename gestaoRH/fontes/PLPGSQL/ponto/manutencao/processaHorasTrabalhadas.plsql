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

CREATE OR REPLACE FUNCTION processaHorasTrabalhadas(VARCHAR[], VARCHAR[]) RETURNS VARCHAR AS $$
DECLARE
arHorarioTrabalhadoPar   ALIAS FOR $1;
arHorarioPadraoPar       ALIAS FOR $2;
carga_horaria_padrao     VARCHAR:='00:00';
stSQL                    VARCHAR;
inIndexImpar             INTEGER;
inIndexPar               INTEGER;
arHorarioTrabalhado      VARCHAR[];
arHorarioPadrao          VARCHAR[];
BEGIN

    arHorarioTrabalhado := arHorarioTrabalhadoPar;
    arHorarioPadrao     := arHorarioPadraoPar;

    inIndexImpar := 1;
    inIndexPar := 2;

    WHILE arHorarioTrabalhado[inIndexImpar] IS NOT NULL LOOP
        IF arHorarioTrabalhado[inIndexImpar]::time < arHorarioPadrao[1]::time THEN
            arHorarioTrabalhado[inIndexImpar] := arHorarioPadrao[1];
        END IF;

        IF arHorarioTrabalhado[inIndexImpar]::time > arHorarioPadrao[2]::time THEN
            arHorarioTrabalhado[inIndexImpar] := arHorarioPadrao[2];
        END IF;

        inIndexImpar := inIndexImpar + 1;
        inIndexPar   := inIndexPar + 1;
    END LOOP;

    inIndexImpar := 1;
    inIndexPar   := 2;

    WHILE arHorarioTrabalhado[inIndexPar] IS NOT NULL  LOOP
        IF arHorarioTrabalhado[inIndexPar+1] IS NULL THEN
            IF arHorarioTrabalhado[inIndexPar]::time > arHorarioPadrao[2]::time THEN
                arHorarioTrabalhado[inIndexPar] := arHorarioPadrao[2];
            END IF;
        END IF;

        --PROCESSAR DIFERENÇA ENTRE INPAR E PAR
        stSQL := 'SELECT to_char((interval '|| quote_literal(trim(arHorarioTrabalhado[inIndexPar])) ||' - '|| quote_literal(trim(arHorarioTrabalhado[inIndexImpar])) ||') + interval '|| quote_literal(carga_horaria_padrao) ||', ''hh24:mi'')';            
        carga_horaria_padrao := selectintovarchar(stSQL);

        inIndexImpar := inIndexImpar + 2;
        inIndexPar   := inIndexPar + 2;
    END LOOP;

    RETURN carga_horaria_padrao;
END
$$ LANGUAGE 'plpgsql';

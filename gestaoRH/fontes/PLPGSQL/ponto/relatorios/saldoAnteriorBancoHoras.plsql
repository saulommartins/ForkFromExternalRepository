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
 * PL para Relatorio Banco de Horas
 * Data de Criação   : 10/12/2008


 * @author Analista Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage

 $Id:$
 
*/ 
CREATE OR REPLACE FUNCTION saldoAnteriorBancoHoras(INTEGER,INTEGER,INTEGER,DATE,DATE,VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodContratoParametro      ALIAS FOR $1;
    inCodConfiguracaoPonto      ALIAS FOR $2;
    inCodGrade                  ALIAS FOR $3;
    dtInicioSaldoParametro      ALIAS FOR $4;
    dtFimSaldoParametro         ALIAS FOR $5;
    stEntidadeParametro         ALIAS FOR $6;
    stExercicioParametro        ALIAS FOR $7;
    inCodContrato               INTEGER;
    dtInicioPeriodo             DATE;
    dtFimPeriodo                DATE;
    dtFimSaldo                  DATE;
    dtInicioSaldo               DATE;
    stSql                       VARCHAR;
    stExercicio                 VARCHAR;
    stEntidade                  VARCHAR;
    stContagemTempo             VARCHAR;
    stSaldoAnterior             VARCHAR:='00:00';
    reRegistro                  RECORD;    
BEGIN
    inCodContrato := criarBufferInteiro('inCodContrato',inCodContratoParametro);
    stExercicio   := criarBufferTexto('stExercicioSistema',stExercicioParametro);
    stEntidade    := criarBufferTexto('stEntidade',stEntidadeParametro);
    stContagemTempo := pega0DataContagemTempoContrato();

    dtInicioSaldo := dtInicioSaldoParametro;
    IF dtInicioSaldo < stContagemTempo::date THEN
        dtInicioSaldo := stContagemTempo::DATE;
    END IF;

    --Desconta um dia da data final para processamento do saldo
    dtFimSaldo      := dtFimSaldoParametro - 1;
    dtInicioPeriodo := dtInicioSaldo;
    dtFimPeriodo    := last_day(dtInicioPeriodo);
    LOOP    
        stSql := 'SELECT *
                    FROM recuperaRelatorioEspelhoPonto('|| inCodContrato ||',
                                                       '|| inCodConfiguracaoPonto ||',
                                                       '|| inCodGrade ||',
                                                       '|| quote_literal(to_char(dtInicioPeriodo,'dd/mm/yyyy')) ||',';
        IF dtFimPeriodo > dtFimSaldo THEN
            stSql := stSql || ' '|| quote_literal(to_char(dtFimSaldo,'dd/mm/yyyy')) ||',';
        ELSE
            stSql := stSql || ' '|| quote_literal(to_char(dtFimPeriodo,'dd/mm/yyyy')) ||',';
        END IF;
        stSql := stSql || ' '|| quote_literal(stEntidade) ||')';
        FOR reRegistro IN EXECUTE stSql LOOP
            IF reRegistro.extras IS NOT NULL THEN
                stSaldoAnterior := stSaldoAnterior::INTERVAL + reRegistro.extras::interval;
            END IF;
            IF reRegistro.ext_not IS NOT NULL THEN
                stSaldoAnterior := stSaldoAnterior::INTERVAL + reRegistro.ext_not::interval;
            END IF;
            IF reRegistro.faltas IS NOT NULL THEN
                stSaldoAnterior := stSaldoAnterior::INTERVAL - reRegistro.faltas::interval;
            END IF;
        END LOOP;
        dtInicioPeriodo := dtFimPeriodo + 1;
        dtFimPeriodo    := last_day(dtInicioPeriodo);
        EXIT WHEN substr(dtFimPeriodo,1,7) > substr(dtFimSaldo,1,7);
    END LOOP;

    RETURN stSaldoAnterior;
END; 
$$ LANGUAGE 'plpgsql';

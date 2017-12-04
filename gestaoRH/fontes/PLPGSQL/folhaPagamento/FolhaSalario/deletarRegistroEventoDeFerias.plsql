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
CREATE OR REPLACE FUNCTION deletarRegistroEventoDeFerias(INTEGER,INTEGER) RETURNS BOOLEAN as $$

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    stSql                       VARCHAR := '';
    Registro                    RECORD;
    boRetorno                   BOOLEAN;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    stSql := 'SELECT ultimo_registro_evento.*
                 FROM folhapagamento'||stEntidade||'.ultimo_registro_evento
                    , folhapagamento'||stEntidade||'.registro_evento_periodo
                    , folhapagamento'||stEntidade||'.evento_calculado
                WHERE ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                  AND ultimo_registro_evento.cod_registro     = evento_calculado.cod_registro
                  AND ultimo_registro_evento.cod_evento       = evento_calculado.cod_evento
                  AND ultimo_registro_evento.timestamp        = evento_calculado.timestamp_registro
                  AND evento_calculado.desdobramento != '||quote_literal('')||'
                  AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                  AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
    FOR Registro IN EXECUTE stSql
    LOOP
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_calculado_dependente 
                    WHERE cod_registro = '||Registro.cod_registro||'
                      AND cod_evento   = '||Registro.cod_evento||'
                      AND timestamp_registro    = '||quote_literal(Registro.timestamp)||'';
        EXECUTE stSql;    
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_calculado 
                    WHERE cod_registro = '||Registro.cod_registro||'
                      AND cod_evento   = '||Registro.cod_evento||'
                      AND timestamp_registro    = '||quote_literal(Registro.timestamp)||'';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo 
                   WHERE cod_registro = '||Registro.cod_registro||'
                     AND cod_evento   = '||Registro.cod_evento||'
                     AND timestamp    = '||quote_literal(Registro.timestamp)||'';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento 
                        WHERE cod_registro = '||Registro.cod_registro||'
                          AND cod_evento   = '||Registro.cod_evento||'
                          AND timestamp    = '||quote_literal(Registro.timestamp)||'';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento 
                        WHERE cod_registro = '||Registro.cod_registro||'
                          AND cod_evento   = '||Registro.cod_evento||'
                          AND timestamp    = '||quote_literal(Registro.timestamp)||'';
        EXECUTE stSql;
    END LOOP;
    RETURN true;
END;
$$ LANGUAGE 'plpgsql';

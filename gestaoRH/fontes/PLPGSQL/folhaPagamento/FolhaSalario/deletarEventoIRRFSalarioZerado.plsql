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
--/**
--    * Função PLSQL
--    * Data de Criação: 10/04/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION deletarEventoIRRFSalarioZerado(VARCHAR,INTEGER,INTEGER,INTEGER) RETURNS BOOLEAN as '
DECLARE
    dtVigencia                  ALIAS FOR $1;
    inCodTipo                   ALIAS FOR $2;
    inCodContrato               ALIAS FOR $3;
    inCodPeriodoMovimentacao    ALIAS FOR $4;

    stSql                       VARCHAR;
    stTimestampRegistro         VARCHAR;
    boRetorno                   BOOLEAN := TRUE;

    crCursor                    REFCURSOR;
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;    
    inCodConfiguracao           INTEGER;  
    nuTotal                     NUMERIC;
    nuValor                     NUMERIC;
    reRegistro                  RECORD;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    stSql := ''SELECT evento_calculado.cod_registro
                 FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
                    , folhapagamento''||stEntidade||''.tabela_irrf
                    , (   SELECT cod_tabela
                               , max(timestamp) as timestamp
                            FROM folhapagamento''||stEntidade||''.tabela_irrf
                           WHERE tabela_irrf.vigencia = ''''''||dtVigencia||''''''
                        GROUP BY cod_tabela) as max_tabela_irrf
                    , folhapagamento''||stEntidade||''.evento
                    , folhapagamento''||stEntidade||''.registro_evento
                    , folhapagamento''||stEntidade||''.ultimo_registro_evento
                    , folhapagamento''||stEntidade||''.evento_calculado
                    , folhapagamento''||stEntidade||''.registro_evento_periodo
                WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                  AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                  AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                  AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                  AND tabela_irrf_evento.cod_evento = evento.cod_evento
                  AND evento.cod_evento             = registro_evento.cod_evento
                  AND registro_evento.cod_evento    = ultimo_registro_evento.cod_evento
                  AND registro_evento.timestamp     = ultimo_registro_evento.timestamp
                  AND registro_evento.cod_registro  = ultimo_registro_evento.cod_registro
                  AND registro_evento.cod_evento    = evento_calculado.cod_evento
                  AND registro_evento.timestamp     = evento_calculado.timestamp_registro
                  AND registro_evento.cod_registro  = evento_calculado.cod_registro
                  AND registro_evento.cod_registro  = registro_evento_periodo.cod_registro
                  AND evento_calculado.valor        = ''''0.00''''
                  AND tabela_irrf_evento.cod_tipo = ''||inCodTipo||''
                  AND registro_evento_periodo.cod_contrato = ''||inCodContrato||''
                  AND registro_evento_periodo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||'' '';

    --OPEN crCursor FOR EXECUTE stSql;
    --    FETCH crCursor INTO inCodRegistro;
    --CLOSE crCursor;

    FOR reRegistro IN EXECUTE stSql LOOP
        nuTotal := nuTotal + reRegistro.valor;
        IF reRegistro.valor = 0.00 THEN
            stSql := ''DELETE FROM folhapagamento''||stEntidade||''.evento_calculado              WHERE cod_registro = ''||inCodRegistro;
            EXECUTE stSql;
            stSql := ''DELETE FROM folhapagamento''||stEntidade||''.evento_calculado_dependente   WHERE cod_registro = ''||inCodRegistro;
            EXECUTE stSql;
            stSql := ''DELETE FROM folhapagamento''||stEntidade||''.log_erro_calculo              WHERE cod_registro = ''||inCodRegistro;
            EXECUTE stSql;
            stSql := ''DELETE FROM folhapagamento''||stEntidade||''.registro_evento_parcela       WHERE cod_registro = ''||inCodRegistro;
            EXECUTE stSql;
            stSql := ''DELETE FROM folhapagamento''||stEntidade||''.ultimo_registro_evento        WHERE cod_registro = ''||inCodRegistro;
            EXECUTE stSql;
        END IF;
    END LOOP;

    IF nuTotal = 0.00 THEN
        FOR inCodConfiguracao IN 1 .. 4
        LOOP
            nuValor := recuperarBufferNumerico(''nuValorDeducaoDependente'');
            inCodEvento         := recuperarBufferInteiro(''inCodEventoSPensao'');
            inCodRegistro       := recuperarBufferInteiro(''inCodRegistroSPensao'');
            stTimestampRegistro := recuperarBufferTexto(''stTimestampRegistroSPensao'');
            IF inCodEvento != 0 THEN
                stSql := ''UPDATE folhapagamento''||stEntidade||''.evento_complementar_calculado SET valor = valor - ''||nuValor||''
                 WHERE cod_evento         = ''||inCodEvento||''
                   AND cod_registro       = ''||inCodRegistro||''
                   AND cod_configuracao   = ''||inCodConfiguracao||''
                   AND timestamp_registro = ''''''||stTimestampRegistro||'''''' '';
                EXECUTE stSql;
            END IF;

            inCodEvento         := recuperarBufferInteiro(''inCodEventoCPensao'');
            inCodRegistro       := recuperarBufferInteiro(''inCodRegistroCPensao'');
            stTimestampRegistro := recuperarBufferTexto(''stTimestampRegistroCPensao'');
            IF inCodEvento != 0 THEN
                stSql := ''UPDATE folhapagamento''||stEntidade||''.evento_complementar_calculado SET valor = valor - ''||nuValor||''
                 WHERE cod_evento         = ''||inCodEvento||''
                   AND cod_registro       = ''||inCodRegistro||''
                   AND cod_configuracao   = ''||inCodConfiguracao||''
                   AND timestamp_registro = ''''''||stTimestampRegistro||'''''' '';
                EXECUTE stSql;
            END IF;
        END LOOP;
    END IF;

    RETURN boRetorno; 
END;
'LANGUAGE 'plpgsql';

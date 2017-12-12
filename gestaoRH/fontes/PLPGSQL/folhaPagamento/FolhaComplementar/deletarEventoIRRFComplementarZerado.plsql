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
--    * Data de Criação: 25/05/2006 
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
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION deletarEventoIRRFComplementarZerado(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER) RETURNS BOOLEAN as $$
DECLARE
    dtVigencia                  ALIAS FOR $1;
    inCodTipo                   ALIAS FOR $2;
    inCodContrato               ALIAS FOR $3;
    inCodPeriodoMovimentacao    ALIAS FOR $4;
    inCodComplementar           ALIAS FOR $5;
    stSql                       VARCHAR;
    stTimestampRegistro         VARCHAR;
    boRetorno                   BOOLEAN := TRUE;
    crCursor                    REFCURSOR;
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;    
    nuValor                     NUMERIC;
    nuTotal                     NUMERIC:=0.00;
    reRegistro                  RECORD;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    stSql := 'SELECT evento_complementar_calculado.cod_registro
                    , evento_complementar_calculado.valor
                 FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                    , folhapagamento'||stEntidade||'.tabela_irrf
                    , (   SELECT cod_tabela
                               , max(timestamp) as timestamp
                            FROM folhapagamento'||stEntidade||'.tabela_irrf
                           WHERE tabela_irrf.vigencia = '''||dtVigencia||'''
                        GROUP BY cod_tabela) as max_tabela_irrf
                    , folhapagamento'||stEntidade||'.evento
                    , folhapagamento'||stEntidade||'.registro_evento_complementar
                    , folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                    , folhapagamento'||stEntidade||'.evento_complementar_calculado
                WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                  AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                  AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                  AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                  AND tabela_irrf_evento.cod_evento = evento.cod_evento
                  AND evento.cod_evento             = registro_evento_complementar.cod_evento
                  AND registro_evento_complementar.cod_evento    = ultimo_registro_evento_complementar.cod_evento
                  AND registro_evento_complementar.timestamp     = ultimo_registro_evento_complementar.timestamp
                  AND registro_evento_complementar.cod_registro  = ultimo_registro_evento_complementar.cod_registro
                  AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
                  AND registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
                  AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
                  AND registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
                  AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                  --AND evento_complementar_calculado.valor        = ''0.00''
                  AND tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                  AND registro_evento_complementar.cod_contrato = '||inCodContrato||'
                  AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||' 
                  AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'  ';

    --OPEN crCursor FOR EXECUTE stSql;
    --    FETCH crCursor INTO inCodRegistro,nuValor;
    --CLOSE crCursor;
    
    FOR reRegistro IN EXECUTE stSql LOOP
        nuTotal := nuTotal + reRegistro.valor;
        IF reRegistro.valor = 0.00 THEN
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_complementar_calculado        WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_complementar        WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_complementar_parcela WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar  WHERE cod_registro = '||reRegistro.cod_registro;
            EXECUTE stSql;
        END IF;
    END LOOP;    

    IF nuTotal = 0.00 THEN
        FOR inCodConfiguracao IN 1 .. 4
        LOOP
            nuValor             := recuperarBufferNumerico('nuValorDeducaoDependente'||inCodConfiguracao);
            inCodEvento         := recuperarBufferInteiro('inCodEventoSPensao'||inCodConfiguracao);
            inCodRegistro       := recuperarBufferInteiro('inCodRegistroSPensao'||inCodConfiguracao);
            stTimestampRegistro := recuperarBufferTexto('stTimestampRegistroSPensao'||inCodConfiguracao);        
            IF nuValor is not null THEN
                IF inCodEvento != 0 THEN
                    stSql := 'UPDATE folhapagamento'||stEntidade||'.evento_complementar_calculado SET valor = valor - '||nuValor||'
                    WHERE cod_evento         = '||inCodEvento||'
                    AND cod_registro       = '||inCodRegistro||'
                    AND cod_configuracao   = '||inCodConfiguracao||'
                    AND timestamp_registro = '''||stTimestampRegistro||''' '; 
                    EXECUTE stSql;
                END IF;
    
                inCodEvento         := recuperarBufferInteiro('inCodEventoCPensao'||inCodConfiguracao);
                inCodRegistro       := recuperarBufferInteiro('inCodRegistroCPensao'||inCodConfiguracao);
                stTimestampRegistro := recuperarBufferTexto('stTimestampRegistroCPensao'||inCodConfiguracao);
                IF inCodEvento != 0 THEN
                    stSql := 'UPDATE folhapagamento'||stEntidade||'.evento_complementar_calculado SET valor = valor - '||nuValor||'
                    WHERE cod_evento         = '||inCodEvento||'
                    AND cod_registro       = '||inCodRegistro||'
                    AND cod_configuracao   = '||inCodConfiguracao||'
                    AND timestamp_registro = '''||stTimestampRegistro||''' ';
                    EXECUTE stSql;
                END IF;
            END IF;

        END LOOP;
    END IF;
    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';

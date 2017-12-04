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
--    * Data de Criação: 05/01/2007
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28448 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $
--
--    * Casos de uso: uc-04.04.14
--*/

CREATE OR REPLACE FUNCTION excluirRegistroEventoAutomatico(INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS BOOLEAN as $$

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodEvento                 ALIAS FOR $3;
    stTipo                      ALIAS FOR $4;
    boRetorno                   BOOLEAN;
    inCodRegistro               INTEGER;
    stSql                       VARCHAR;
    stEntidade                  VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    IF stTipo = 'P' THEN
        inCodRegistro := selectIntoInteger('SELECT ultimo_registro_evento.cod_registro
                                     FROM folhapagamento'||stEntidade||'.ultimo_registro_evento
                                        , folhapagamento'||stEntidade||'.registro_evento_periodo
                                        , folhapagamento'||stEntidade||'.registro_evento
                                    WHERE ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                      AND ultimo_registro_evento.cod_registro = registro_evento.cod_registro
                                      AND ultimo_registro_evento.cod_evento = registro_evento.cod_evento
                                      AND ultimo_registro_evento.timestamp = registro_evento.timestamp
                                      AND registro_evento.proporcional IS TRUE
                                      AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                                      AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                      AND ultimo_registro_evento.cod_evento = '||inCodEvento);
    ELSE
        inCodRegistro := selectIntoInteger('SELECT ultimo_registro_evento.cod_registro
                                     FROM folhapagamento'||stEntidade||'.ultimo_registro_evento
                                        , folhapagamento'||stEntidade||'.registro_evento_periodo
                                    WHERE ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                      AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                                      AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                      AND ultimo_registro_evento.cod_evento = '||inCodEvento);
    END IF;
    IF inCodRegistro IS NOT NULL THEN
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_calculado_dependente WHERE cod_registro   = '|| inCodRegistro;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_calculado WHERE cod_registro              = '|| inCodRegistro;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo WHERE cod_registro              = '|| inCodRegistro;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_parcela WHERE cod_registro       = '|| inCodRegistro;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento WHERE cod_registro        = '|| inCodRegistro;   
        EXECUTE stSql;
    END IF;

    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';

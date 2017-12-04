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
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23133 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-07 12:40:10 -0300 (Qui, 07 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION buscaDadosRegistroEventoDeDescontoIRRF(VARCHAR,INTEGER,INTEGER,INTEGER) RETURNS VARCHAR as $$
DECLARE
    dtVigencia                  ALIAS FOR $1;
    inCodTipo                   ALIAS FOR $2;
    inCodContrato               ALIAS FOR $3;
    inCodPeriodoMovimentacao    ALIAS FOR $4;
    stSql                       VARCHAR;
    stTimestampRegistro         VARCHAR;
    stRetorno                   VARCHAR;
    crCursor                    REFCURSOR;
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;   
    stEntidade               VARCHAR; 
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade'); 
    inCodEvento := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                        FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                       WHERE tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                                    ORDER BY timestamp desc LIMIT 1');       
    stSql := 'SELECT evento_calculado.cod_evento
                    , evento_calculado.cod_registro
                    , evento_calculado.timestamp_registro
                 FROM folhapagamento'||stEntidade||'.evento_calculado
                    , folhapagamento'||stEntidade||'.registro_evento_periodo
                WHERE registro_evento_periodo.cod_registro  = evento_calculado.cod_registro
                  AND evento_calculado.cod_evento = '||inCodEvento||'
                  AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                  AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||' ';
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO inCodEvento,inCodRegistro,stTimestampRegistro;
    CLOSE crCursor;
    stRetorno := inCodEvento||'#'||inCodRegistro||'#'||stTimestampRegistro;   
    RETURN stRetorno; 
END;
$$ LANGUAGE 'plpgsql';

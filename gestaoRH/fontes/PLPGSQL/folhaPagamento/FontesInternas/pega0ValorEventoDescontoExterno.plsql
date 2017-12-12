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
--*
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 25629 $
-- $Name$
-- $Autor: Diego $
-- Date: 2005/12/14 10:50:00 $
--
-- Caso de uso: uc-04.05.61
--
-- Objetivo: Recupera o valor do registro de evento do contrato
-- no referido periodo de movimentacao.
--
--/
--



CREATE OR REPLACE FUNCTION pega0ValorEventoDescontoExterno(VARCHAR) RETURNS numeric as $$

DECLARE
    stConfiguracao                  ALIAS FOR $1;
    nuValor                         NUMERIC:=0.00;
    inCodContrato                   INTEGER:=recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao        INTEGER:=recuperarBufferInteiro('inCodPeriodoMovimentacao');
    stEntidade                   VARCHAR:=recuperarBufferTexto('stEntidade');
BEGIN    
    IF stConfiguracao = 'base_irrf' THEN
        nuValor := selectIntoNumeric('SELECT vl_base_irrf
                                        FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf
                                           , (SELECT cod_contrato
                                                   , MAX(timestamp) as timestamp
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf
                                               WHERE vigencia <= '|| quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)) ||'
                                              GROUP BY cod_contrato) as max_desconto_externo_irrf
                                       WHERE desconto_externo_irrf.cod_contrato = max_desconto_externo_irrf.cod_contrato
                                         AND desconto_externo_irrf.timestamp = max_desconto_externo_irrf.timestamp
                                         AND desconto_externo_irrf.cod_contrato = '|| inCodContrato ||'
                                         AND NOT EXISTS (SELECT 1
                                                           FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf_anulado
                                                          WHERE desconto_externo_irrf.cod_contrato = desconto_externo_irrf_anulado.cod_contrato
                                                            AND desconto_externo_irrf.timestamp = desconto_externo_irrf_anulado.timestamp)');          
    END IF; 
    IF stConfiguracao = 'desconto_irrf' THEN
        nuValor := selectIntoNumeric('SELECT valor_irrf
                                        FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf
                                   LEFT JOIN (SELECT desconto_externo_irrf_valor.*
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf_valor
                                                   , (SELECT cod_contrato
                                                           , max(timestamp_valor) as timestamp_valor
                                                        FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf_valor
                                                      GROUP BY cod_contrato) as max_desconto_externo_irrf_valor
                                               WHERE desconto_externo_irrf_valor.cod_contrato = max_desconto_externo_irrf_valor.cod_contrato
                                                 AND desconto_externo_irrf_valor.timestamp_valor = max_desconto_externo_irrf_valor.timestamp_valor) as desconto_externo_irrf_valor
                                          ON desconto_externo_irrf_valor.cod_contrato = desconto_externo_irrf.cod_contrato
                                         AND desconto_externo_irrf_valor.timestamp = desconto_externo_irrf.timestamp 
                                           , (SELECT cod_contrato
                                                   , MAX(timestamp) as timestamp
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf
                                               WHERE vigencia <= '|| quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)) ||'
                                              GROUP BY cod_contrato) as max_desconto_externo_irrf
                                       WHERE desconto_externo_irrf.cod_contrato = max_desconto_externo_irrf.cod_contrato
                                         AND desconto_externo_irrf.timestamp = max_desconto_externo_irrf.timestamp
                                         AND desconto_externo_irrf.cod_contrato = '|| inCodContrato ||'
                                         AND NOT EXISTS (SELECT 1
                                                           FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf_anulado
                                                          WHERE desconto_externo_irrf.cod_contrato = desconto_externo_irrf_anulado.cod_contrato
                                                            AND desconto_externo_irrf.timestamp = desconto_externo_irrf_anulado.timestamp)');          
    END IF; 
    IF stConfiguracao = 'base_previdencia' THEN
        nuValor := selectIntoNumeric('SELECT vl_base_previdencia
                                        FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia
                                           , (SELECT cod_contrato
                                                   , MAX(timestamp) as timestamp
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia
                                               WHERE vigencia <= '|| quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)) ||'
                                              GROUP BY cod_contrato) as max_desconto_externo_previdencia
                                       WHERE desconto_externo_previdencia.cod_contrato = max_desconto_externo_previdencia.cod_contrato
                                         AND desconto_externo_previdencia.timestamp = max_desconto_externo_previdencia.timestamp       
                                         AND desconto_externo_previdencia.cod_contrato = '|| inCodContrato ||'
                                         AND NOT EXISTS (SELECT 1
                                                           FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia_anulado
                                                          WHERE desconto_externo_previdencia.cod_contrato = desconto_externo_previdencia_anulado.cod_contrato
                                                            AND desconto_externo_previdencia.timestamp = desconto_externo_previdencia_anulado.timestamp)');          
    END IF; 
    IF stConfiguracao = 'desconto_previdencia' THEN
        nuValor := selectIntoNumeric('SELECT valor_previdencia
                                        FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia
                                   LEFT JOIN (SELECT desconto_externo_previdencia_valor.*
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia_valor
                                                   , (SELECT cod_contrato
                                                           , max(timestamp_valor) as timestamp_valor
                                                        FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia_valor
                                                      GROUP BY cod_contrato) as max_desconto_externo_previdencia_valor
                                               WHERE desconto_externo_previdencia_valor.cod_contrato = max_desconto_externo_previdencia_valor.cod_contrato
                                                 AND desconto_externo_previdencia_valor.timestamp_valor = max_desconto_externo_previdencia_valor.timestamp_valor) as desconto_externo_previdencia_valor
                                          ON desconto_externo_previdencia_valor.cod_contrato = desconto_externo_previdencia.cod_contrato
                                         AND desconto_externo_previdencia_valor.timestamp = desconto_externo_previdencia.timestamp 
                                           , (SELECT cod_contrato
                                                   , MAX(timestamp) as timestamp
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia
                                               WHERE vigencia <= '|| quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)) ||'
                                              GROUP BY cod_contrato) as max_desconto_externo_previdencia
                                       WHERE desconto_externo_previdencia.cod_contrato = max_desconto_externo_previdencia.cod_contrato
                                         AND desconto_externo_previdencia.timestamp = max_desconto_externo_previdencia.timestamp       
                                         AND desconto_externo_previdencia.cod_contrato = '|| inCodContrato ||'
                                         AND NOT EXISTS (SELECT 1
                                                           FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia_anulado
                                                          WHERE desconto_externo_previdencia.cod_contrato = desconto_externo_previdencia_anulado.cod_contrato
                                                            AND desconto_externo_previdencia.timestamp = desconto_externo_previdencia_anulado.timestamp)');          
    END IF; 

    RETURN nuValor;
END;
$$ LANGUAGE 'plpgsql';

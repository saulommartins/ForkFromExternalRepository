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
--    * Pl para retorno o percentual de pagamento do adiantamento
--    * Data de Criacão: 23/03/2009
--
--
--    * @author Diego Lemos de Souza
--
--    * Casos de uso: uc-04.00.00
--
--    $Id: pega0PercentualAdiantamentoDecimo 31697 2008-08-04 19:33:31Z souzadl $
--*/
CREATE OR REPLACE FUNCTION pega0PercentualAdiantamentoDecimo() RETURNS NUMERIC AS $$
DECLARE
    stSelect                    VARCHAR:='';
    inCodContrato               INTEGER;
    inExercicio                 INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    stEntidade                  VARCHAR:='';
    nuRetorno                   NUMERIC;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inExercicio   := to_char(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)::timestamp,'yyyy')::INTEGER;

    stSelect := '    SELECT configuracao_adiantamento.percentual
                       FROM folhapagamento'|| stEntidade ||'.configuracao_adiantamento
                 INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                         ON periodo_movimentacao.cod_periodo_movimentacao = configuracao_adiantamento.cod_periodo_movimentacao
                        AND to_char(periodo_movimentacao.dt_final,''yyyy'') = '|| quote_literal(inExercicio) ||'
                      WHERE configuracao_adiantamento.cod_contrato = '||inCodContrato||' 
                        AND configuracao_adiantamento.desdobramento = ''A'' ';
   nuRetorno := selectIntoNumeric(stSelect);
   RETURN nuRetorno;
END;   
$$LANGUAGE 'plpgsql';



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
--    * Data de Criação: 13/11/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23101 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-06 10:17:40 -0300 (Qua, 06 Jun 2007) $
--
--    * Casos de uso: uc-04.05.11
--*/



CREATE OR REPLACE FUNCTION pegaValorCalculadoDecimoCompetenciasAnteriores(INTEGER) RETURNS NUMERIC AS $$
DECLARE
    inCodEvento                     ALIAS FOR $1;
    inCodPeriodoMovimentacaoFinal   INTEGER;
    inCodPeriodoMovimentacaoInicial INTEGER;
    inExercicio                     INTEGER;
    inCodContrato                   INTEGER;
    stSql                           VARCHAR := '';
    nuValorEvento                   NUMERIC := 0.00;
    nuValorTemp                     NUMERIC := 0.00;
    nuValorEvCalc                   NUMERIC := 0.00;
    nuValorEvComp                   NUMERIC := 0.00;
    stTimestamp                     VARCHAR;
    arTimestamp                     VARCHAR[];
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade'); 
BEGIN
    stTimestamp := pega1DataFinalCompetencia();
    arTimestamp := string_to_array(stTimestamp,'-');    
    inExercicio := arTimestamp[1];

    inCodPeriodoMovimentacaoInicial := selectIntoInteger('SELECT min(periodo_movimentacao.cod_periodo_movimentacao) as cod_periodo_movimentacao
                                                   FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                  WHERE to_char(periodo_movimentacao.dt_final,''yyyy'') = '||quote_literal(inExercicio) );
    inCodPeriodoMovimentacaoFinal := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodContrato                 := recuperarBufferInteiro('inCodContrato');

    WHILE inCodPeriodoMovimentacaoInicial < inCodPeriodoMovimentacaoFinal 
    LOOP
        nuValorTemp   := selectIntoNumeric('SELECT evento_decimo_calculado.valor
                                               FROM folhapagamento'||stEntidade||'.registro_evento_decimo
                                                  , folhapagamento'||stEntidade||'.evento_decimo_calculado
                                              WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                                AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                                                AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                                                AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                                AND registro_evento_decimo.desdobramento = ''A''
                                                AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacaoInicial||'
                                                AND registro_evento_decimo.cod_contrato = '||inCodContrato||'
                                                AND registro_evento_decimo.cod_evento = '||inCodEvento
                                          );
        nuValorEvCalc := selectIntoNumeric(' SELECT evento_calculado.valor
                                                FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                                                , folhapagamento'||stEntidade||'.registro_evento
                                                , folhapagamento'||stEntidade||'.evento_calculado
                                                WHERE registro_evento_periodo.cod_registro = registro_evento.cod_registro
                                                 AND  registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacaoInicial||'
                                                 AND  registro_evento_periodo.cod_contrato = '||inCodContrato||'
                                                 AND  registro_evento.cod_evento = '||inCodEvento||'
                                                 AND  registro_evento.cod_registro = evento_calculado.cod_registro
                                                 AND  registro_evento.timestamp = evento_calculado.timestamp_registro
                                                 AND  evento_calculado.desdobramento = ''I''
                                                 AND  registro_evento.cod_evento = evento_calculado.cod_evento'
                                          );
        nuValorEvComp := selectIntoNumeric(' SELECT evento_complementar_calculado.valor
                                                FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                                                , folhapagamento'||stEntidade||'.evento_complementar_calculado
                                                WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                                                AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                                                AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                                                AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                                AND registro_evento_complementar.cod_configuracao = 3
                                                AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacaoInicial||'
                                                AND registro_evento_complementar.cod_contrato = '||inCodContrato||'
                                                AND registro_evento_complementar.cod_evento = '||inCodEvento
                                          );
        IF nuValorTemp IS NULL THEN
           nuValorTemp := 0.00;
        END IF;

        IF nuValorEvCalc IS NULL THEN
           nuValorEvCalc := 0.00;
        END IF;

        IF nuValorEvComp IS NULL THEN
           nuValorEvComp := 0.00;
        END IF;
        nuValorEvento := nuValorEvento + nuValorTemp + nuValorEvCalc + nuValorEvComp; 
        inCodPeriodoMovimentacaoInicial := inCodPeriodoMovimentacaoInicial + 1;
    END LOOP;
    RETURN nuValorEvento;
END;
$$ LANGUAGE 'plpgsql';

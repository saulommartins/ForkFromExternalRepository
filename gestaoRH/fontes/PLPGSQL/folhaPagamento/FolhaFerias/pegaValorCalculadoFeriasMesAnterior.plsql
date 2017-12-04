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
--    * Data de Criação: 19/07/2006
--
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 27265 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-12-20 09:19:48 -0200 (Qui, 20 Dez 2007) $
--
--    * Casos de uso: uc-04.05.48
--    * Casos de uso: uc-04.05.19
--*/

CREATE OR REPLACE FUNCTION pegaValorCalculadoFeriasMesAnterior(INTEGER,VARCHAR) RETURNS NUMERIC AS $$
DECLARE
    inCodEvento                 ALIAS FOR $1;
    stDesdobramento             ALIAS FOR $2;
    inCodPeriodoMovimentacao    INTEGER;
    inCodContrato               INTEGER;
    nuValorEvento               NUMERIC := 0.00;  
    stSql                       VARCHAR :=''; 
    crCursor                    REFCURSOR;
    boRetornoFerias             BOOLEAN := FALSE;  
    dtCompetencia               DATE;  
    arCompetencia               VARCHAR[];
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade'); 
BEGIN
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao') - 1;
    inCodContrato               := recuperarBufferInteiro('inCodContrato');
    dtCompetencia               := pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao);
    arCompetencia               := string_to_array(dtCompetencia::VARCHAR,'-');
    stSql := 'SELECT TRUE as boRetorno
                FROM pessoal'|| stEntidade ||'.lancamento_ferias
                   , pessoal'|| stEntidade ||'.ferias
               WHERE lancamento_ferias.cod_ferias = ferias.cod_ferias
                 AND cod_tipo = 2
                 AND cod_contrato = '|| inCodContrato ||'
                 AND ((to_char(dt_inicio,''mm'') = '|| quote_literal(arCompetencia[2]) ||' OR to_char(dt_fim,''mm'') = '|| quote_literal(arCompetencia[2]) ||')
                  OR  (mes_competencia = '|| quote_literal(arCompetencia[2]) ||' AND ano_competencia = '|| quote_literal(arCompetencia[1]) ||'))';

    boRetornoFerias := selectIntoBoolean(stSql);

    IF boRetornoFerias IS TRUE THEN
        stSql:='SELECT evento_calculado.valor
               FROM folhapagamento'|| stEntidade ||'.evento_calculado
                  , folhapagamento'|| stEntidade ||'.registro_evento_periodo
              WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                AND registro_evento_periodo.cod_contrato = '|| inCodContrato ||'
                AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                AND evento_calculado.cod_evento = '|| inCodEvento ||'                
                AND evento_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';     
    ELSE
        stSql:='SELECT evento_ferias_calculado.valor
               FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                  , folhapagamento'|| stEntidade ||'.registro_evento_ferias
              WHERE evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
                AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                AND registro_evento_ferias.cod_contrato = '|| inCodContrato ||'
                AND registro_evento_ferias.cod_evento = '|| inCodEvento ||'
                AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                AND registro_evento_ferias.desdobramento = '|| quote_literal(stDesdobramento) ||' '; 
    END IF;
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO nuValorEvento; 
    CLOSE crCursor;

    RETURN nuValorEvento;
END;
$$ LANGUAGE 'plpgsql';

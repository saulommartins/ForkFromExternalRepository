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

CREATE OR REPLACE FUNCTION pegaValorCalculadoFeriasMesAtual() RETURNS NUMERIC AS $$
DECLARE
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
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodContrato               := recuperarBufferInteiro('inCodContrato');
    nuValorEvento:=selectintonumeric(
    'SELECT COALESCE(sum(quantidade),0.00) as nuValor
       FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
          , folhapagamento'||stEntidade||'.registro_evento_ferias
     WHERE ultimo_registro_evento_ferias.cod_registro=registro_evento_ferias.cod_registro
       AND ultimo_registro_evento_ferias.timestamp=registro_evento_ferias.timestamp
       AND ultimo_registro_evento_ferias.cod_evento=registro_evento_ferias.cod_evento
       AND ultimo_registro_evento_ferias.desdobramento=registro_evento_ferias.desdobramento
       AND cod_contrato='||inCodContrato||' 
       AND cod_periodo_movimentacao='||inCodPeriodoMovimentacao||'');

    IF nuValorEvento is null THEN
        nuValorEvento=0.00;
    END IF;

    RETURN nuValorEvento;
END;
$$ LANGUAGE 'plpgsql';

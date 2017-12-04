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
--    * Data de Criação: 08/06/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23095 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.10
--*/


CREATE OR REPLACE FUNCTION inserirEventosAutomaticosComplementar(INTEGER) RETURNS BOOLEAN as $$

DECLARE
    inCodTipo                   ALIAS FOR $1;
    inCodContrato               INTEGER;
    stSql                       VARCHAR := '';
    inCodPeriodoMovimentacao    INTEGER;
    inCodEvento                 INTEGER;
    inCodComplementar           INTEGER;
    inCodConfiguracao           INTEGER;
    inCountFolha                INTEGER:=0;
	inCountComplementar         INTEGER:=0;
    dtVigencia                  VARCHAR := '';
    boRetorno                   BOOLEAN;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodComplementar          := recuperarBufferInteiro('inCodComplementar');
    dtVigencia                 := recuperarBufferTexto('dtVigenciaIrrf');

    inCodEvento := selectIntoInteger(' SELECT cod_evento
                               FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                  , folhapagamento'||stEntidade||'.tabela_irrf
                                  , (SELECT max(timestamp) as timestamp
                                          , cod_tabela
                                       FROM folhapagamento'||stEntidade||'.tabela_irrf
                                      WHERE tabela_irrf.vigencia          = '''||dtVigencia||'''
                                   GROUP BY cod_tabela) as max_tabela_irrf
                              WHERE tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                                AND tabela_irrf_evento.cod_tabela = tabela_irrf.cod_tabela
                                AND tabela_irrf_evento.timestamp  = tabela_irrf.timestamp
                                AND tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp');
    boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,inCodComplementar,1);
    inCountFolha := selectIntoInteger('SELECT count(*)
                                         FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                                            , folhapagamento'||stEntidade||'.evento_ferias_calculado
                                        WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                          AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                                          AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                          AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                                          AND registro_evento_ferias.cod_contrato = '||inCodContrato||'
                                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao);
    IF inCountFolha > 0 THEN
        boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,inCodComplementar,2);
	ELSE
	    inCountComplementar := selectIntoInteger('SELECT COUNT(ultimo_registro_evento_complementar.*) AS contador
                                                     FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                                                        , folhapagamento'||stEntidade||'.registro_evento_complementar
												 WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
												   AND ultimo_registro_evento_complementar.cod_evento   = registro_evento_complementar.cod_evento
												   AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
												   AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
												   AND registro_evento_complementar.cod_contrato = '||inCodContrato||'
												   AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
												   AND registro_evento_complementar.cod_configuracao = 2
												   AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
												   AND ultimo_registro_evento_complementar.cod_evento = '||inCodEvento);
		IF inCountComplementar = 0 THEN
            boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,inCodComplementar,2);	
	    END IF;
    END IF;
    inCountFolha := selectIntoInteger('SELECT count(*)
                                         FROM folhapagamento'||stEntidade||'.registro_evento_decimo
                                            , folhapagamento'||stEntidade||'.evento_decimo_calculado
                                        WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                          AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                                          AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                          AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                                          AND registro_evento_decimo.cod_contrato = '||inCodContrato||'
                                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao);
    IF inCountFolha > 0 THEN
        boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,inCodComplementar,3);
	ELSE
	    inCountComplementar := selectIntoInteger('SELECT COUNT(ultimo_registro_evento_complementar.*) AS contador
                                                     FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                                                        , folhapagamento'||stEntidade||'.registro_evento_complementar
												 WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
												   AND ultimo_registro_evento_complementar.cod_evento   = registro_evento_complementar.cod_evento
												   AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
												   AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
												   AND registro_evento_complementar.cod_contrato = '||inCodContrato||'
												   AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
												   AND registro_evento_complementar.cod_configuracao = 3
												   AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
												   AND ultimo_registro_evento_complementar.cod_evento = '||inCodEvento);
		IF inCountComplementar = 0 THEN
            boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,inCodComplementar,3);	
	    END IF;    
	END IF;
    inCountFolha := selectIntoInteger('SELECT count(*)
                                         FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                                            , folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                        WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                          AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                          AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                          AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                                          AND registro_evento_rescisao.cod_contrato = '||inCodContrato||'
                                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao);
    IF inCountFolha > 0 THEN
        boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,inCodComplementar,4);
	ELSE
	    inCountComplementar := selectIntoInteger('SELECT COUNT(ultimo_registro_evento_complementar.*) AS contador
                                                     FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                                                        , folhapagamento'||stEntidade||'.registro_evento_complementar
												 WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro
												   AND ultimo_registro_evento_complementar.cod_evento   = registro_evento_complementar.cod_evento
												   AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
												   AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp
												   AND registro_evento_complementar.cod_contrato = '||inCodContrato||'
												   AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
												   AND registro_evento_complementar.cod_configuracao = 4
												   AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
												   AND ultimo_registro_evento_complementar.cod_evento = '||inCodEvento);
		IF inCountComplementar = 0 THEN
            boRetorno := insertRegistroEventoAutomaticoComplementar(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,inCodComplementar,4);	
	    END IF; 
    END IF;
    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';

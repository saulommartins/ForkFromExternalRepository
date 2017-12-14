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
--    * Data de Criação: 16/11/2006
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

CREATE OR REPLACE FUNCTION processarSomatorioDeducoesDecimo(BOOLEAN) RETURNS NUMERIC as '

DECLARE
    boComPensao                 ALIAS FOR $1;
    nuDeducoes                  NUMERIC := 0.00;
    nuValorDeducaoDependente    NUMERIC := 0.00;
    nuValorTemp                 NUMERIC := 0.00;
    inNumCGM                    INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodPrevidencia            INTEGER;
    inCodTipo                   INTEGER;
    dtVigencia                  VARCHAR := '''';
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    inCodPeriodoMovimentacao := recuperarBufferInteiro(''inCodPeriodoMovimentacao'');
    inCodPrevidencia         := recuperarBufferInteiro(''inCodPrevidenciaOficial'');
    inNumCGM                 := recuperarBufferInteiro(''inNumCGM'');
    dtVigencia               := recuperarBufferTexto(''dtVigenciaPrevidencia'');
    IF boComPensao IS TRUE THEN
        inCodTipo := 5;
    ELSE
        inCodTipo := 4;
    END IF;
    nuDeducoes := selectIntoNumeric(''SELECT sum(evento_decimo_calculado.valor) as valor
                             FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
                                , (  SELECT cod_tabela
                                          , max(timestamp) as timestamp
                                       FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
                                   GROUP BY cod_tabela) as max_tabela_irrf_evento
                                , folhapagamento''||stEntidade||''.registro_evento_decimo
                                , folhapagamento''||stEntidade||''.ultimo_registro_evento_decimo
                                , folhapagamento''||stEntidade||''.evento_decimo_calculado
                                , pessoal''||stEntidade||''.servidor_contrato_servidor
                                , pessoal''||stEntidade||''.servidor
                            WHERE tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                              AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp
                              AND tabela_irrf_evento.cod_evento = ultimo_registro_evento_decimo.cod_evento
                              AND ultimo_registro_evento_decimo.cod_registro     = evento_decimo_calculado.cod_registro
                              AND ultimo_registro_evento_decimo.cod_evento       = evento_decimo_calculado.cod_evento
                              AND ultimo_registro_evento_decimo.desdobramento    = evento_decimo_calculado.desdobramento
                              AND ultimo_registro_evento_decimo.timestamp        = evento_decimo_calculado.timestamp_registro
                              AND ultimo_registro_evento_decimo.cod_registro     = registro_evento_decimo.cod_registro
                              AND ultimo_registro_evento_decimo.cod_evento       = registro_evento_decimo.cod_evento
                              AND ultimo_registro_evento_decimo.desdobramento    = registro_evento_decimo.desdobramento
                              AND ultimo_registro_evento_decimo.timestamp        = registro_evento_decimo.timestamp
                              AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato
                              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                              AND servidor.numcgm = ''||inNumCGM||''
                              AND tabela_irrf_evento.cod_tipo = ''||inCodTipo||''
                              AND registro_evento_decimo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao);
    IF nuDeducoes IS NULL THEN
        nuDeducoes := 0.00;
    END IF;
    RETURN nuDeducoes;
END;
'LANGUAGE 'plpgsql';


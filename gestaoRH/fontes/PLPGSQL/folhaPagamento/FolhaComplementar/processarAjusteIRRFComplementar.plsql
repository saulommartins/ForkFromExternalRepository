--﻿
--    **********************************************************************************
--    *                                                                                *
--    * @package URBEM CNM - Soluções em Gestão Pública                                *
--    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
--    * @author Confederação Nacional de Municípios                                    *
--    *                                                                                *
--    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
--    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
--    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
--    *                                                                                *
--    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
--    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
--    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
--    * para mais detalhes.                                                            *
--    *                                                                                *
--    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU LICENCA.txt *
--    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
--    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
--    *                                                                                *
--    **********************************************************************************
--
--/**
--    * Função PLSQL
--    * Data de Criação: 11/04/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25629 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-09-25 11:46:37 -0300 (Ter, 25 Set 2007) $
--
--    * Casos de uso: uc-04.05.46
--*/

CREATE OR REPLACE FUNCTION processarAjusteIRRFComplementar(BOOLEAN,INTEGER) RETURNS BOOLEAN as $$

DECLARE
    boComPensao                 ALIAS FOR $1;
    inCodConfiguracao           ALIAS FOR $2;
    inCodPeriodoMovimentacao    INTEGER;
    inCodContrato               INTEGER;
    inContComplementar          INTEGER;
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;
    inCodTipo                   INTEGER;
    inCodComplementar           INTEGER;
    inNumCgm                    INTEGER;
    inCountFolhaSalario         INTEGER;
    inCountFolhaComplementar    INTEGER;
    inCountFolhaFerias          INTEGER;
    inCountFolhaDecimo          INTEGER;
    inCountFolhaRescisao        INTEGER;
    nuValorBaseCs               NUMERIC:=0.00;
    nuValorBaseFP               NUMERIC:=0.00;
    nuValorBaseFF               NUMERIC:=0.00;
    nuValorBaseFD               NUMERIC:=0.00;
    nuValorBaseFR               NUMERIC:=0.00;
    nuValorBase                 NUMERIC:=0.00;
    nuValorBaseDeducaoCs        NUMERIC:=0.00;
    nuValorBaseDeducaoFP        NUMERIC:=0.00;
    nuValorBaseDeducao          NUMERIC:=0.00;
    nuAliquotaDesconto          NUMERIC:=0.00;
    nuValorDescontoFC           NUMERIC:=0.00;
    nuParcelaDeduzir            NUMERIC:=0.00;
    nuValorDescontoCs           NUMERIC:=0.00;
    nuValorDescontoFP           NUMERIC:=0.00;
    nuValorDescontoFF           NUMERIC:=0.00;
    nuValorDescontoFD           NUMERIC:=0.00;
    nuValorDescontoFR           NUMERIC:=0.00;
    nuSomaDescontoExterno       NUMERIC:=0.00;
    stSql                       VARCHAR := '';
    dtVigencia                  VARCHAR := '';
    stTimestampRegistro         VARCHAR := '';
    stSituacaoFolhaSalario      VARCHAR := '';
    stDadosRegistro             VARCHAR := '';
    stDesdobramento             VARCHAR := '';
    arDadosRegistro             VARCHAR[];
    reRegistro                  RECORD;
    reDescontoExterno           RECORD;
    boRetorno                   BOOLEAN := TRUE;
    boValorMaior                BOOLEAN := TRUE;
    crCursor                    REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inNumCgm                    := recuperarBufferInteiro('inNumCgm');
    inCodContrato               := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');    
    dtVigencia                  := recuperarBufferTexto('dtVigenciaIrrf');
    inCodComplementar           := recuperarBufferInteiro('inCodComplementar');    
    stSituacaoFolhaSalario      := pega0SituacaoDaFolhaSalario();
    inCountFolhaComplementar := selectIntoInteger('SELECT count(*) as contador
                                            FROM (   SELECT registro_evento_complementar.cod_contrato
                                                          , registro_evento_complementar.cod_complementar
                                                       FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                                          , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
                                                          , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                          , pessoal'|| stEntidade ||'.servidor
                                                      WHERE registro_evento_complementar.cod_registro     = ultimo_registro_evento_complementar.cod_registro
                                                        AND registro_evento_complementar.timestamp        = ultimo_registro_evento_complementar.timestamp
                                                        AND registro_evento_complementar.cod_evento       = ultimo_registro_evento_complementar.cod_evento
                                                        AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
                                                        AND registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro
                                                        AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro
                                                        AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento
                                                        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                                        AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                        AND servidor.numcgm = '|| inNumCgm ||'
                                                        AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                   GROUP BY registro_evento_complementar.cod_contrato
                                               , registro_evento_complementar.cod_complementar) as complementar');
    inCountFolhaSalario := selectIntoInteger('SELECT count(*) as contador
                                       FROM (SELECT registro_evento_periodo.cod_contrato
                                               FROM folhapagamento'|| stEntidade ||'.evento_calculado
                                                  , folhapagamento'|| stEntidade ||'.registro_evento
                                                  , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                                  , folhapagamento'|| stEntidade ||'.folha_situacao
                                                  , (  SELECT cod_periodo_movimentacao
                                                            , max(timestamp) as timestamp
                                                         FROM folhapagamento'|| stEntidade ||'.folha_situacao
                                                     GROUP BY cod_periodo_movimentacao) as max_folha_situacao
                                                  , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                  , pessoal'|| stEntidade ||'.servidor
                                              WHERE evento_calculado.cod_evento = registro_evento.cod_evento
                                                AND evento_calculado.cod_registro = registro_evento.cod_registro
                                                AND evento_calculado.timestamp_registro = registro_evento.timestamp
                                                AND registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                                AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                AND registro_evento_periodo.cod_periodo_movimentacao = folha_situacao.cod_periodo_movimentacao
                                                AND folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao
                                                AND folha_situacao.timestamp = max_folha_situacao.timestamp
                                                AND folha_situacao.situacao = ''f''
                                                AND servidor.numcgm = '|| inNumCgm ||'
                                                AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                             GROUP BY registro_evento_periodo.cod_contrato) as salario');
    inCountFolhaFerias := selectIntoInteger('SELECT count(*) as contador
                                      FROM (SELECT registro_evento_ferias.cod_contrato
                                              FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                                 , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                                                 , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                                 , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                 , pessoal'|| stEntidade ||'.lancamento_ferias
                                                 , pessoal'|| stEntidade ||'.ferias
                                                 , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                 , pessoal'|| stEntidade ||'.servidor
                                             WHERE registro_evento_ferias.cod_registro     = ultimo_registro_evento_ferias.cod_registro
                                               AND registro_evento_ferias.timestamp        = ultimo_registro_evento_ferias.timestamp
                                               AND registro_evento_ferias.cod_evento       = ultimo_registro_evento_ferias.cod_evento
                                               AND registro_evento_ferias.desdobramento    = ultimo_registro_evento_ferias.desdobramento
                                               AND registro_evento_ferias.cod_registro     = evento_ferias_calculado.cod_registro
                                               AND registro_evento_ferias.timestamp        = evento_ferias_calculado.timestamp_registro
                                               AND registro_evento_ferias.cod_evento       = evento_ferias_calculado.cod_evento
                                               AND registro_evento_ferias.desdobramento    = evento_ferias_calculado.desdobramento
                                               AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
                                               AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                               AND registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                               AND ferias.cod_contrato = registro_evento_ferias.cod_contrato
                                               AND ferias.cod_ferias = lancamento_ferias.cod_ferias
                                               AND servidor.numcgm = '|| inNumCgm ||'
                                               AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                               AND lancamento_ferias.ano_competencia = to_char(periodo_movimentacao.dt_final,''yyyy'')
                                               AND lancamento_ferias.mes_competencia = to_char(periodo_movimentacao.dt_final,''mm'')
                                          GROUP BY registro_evento_ferias.cod_contrato) as ferias');
    inCountFolhaRescisao := selectIntoInteger('SELECT count(*) as contador
                                      FROM (SELECT registro_evento_rescisao.cod_contrato
                                              FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                                 , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                                                 , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                                 , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                 , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                 , pessoal'|| stEntidade ||'.servidor
                                             WHERE registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro
                                               AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
                                               AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento
                                               AND registro_evento_rescisao.desdobramento    = ultimo_registro_evento_rescisao.desdobramento
                                               AND registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
                                               AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
                                               AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
                                               AND registro_evento_rescisao.desdobramento    = evento_rescisao_calculado.desdobramento
                                               AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                                               AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                               AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                               AND servidor.numcgm ='|| inNumCgm ||'
                                               AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                          GROUP BY registro_evento_rescisao.cod_contrato) as rescisao');
    inCountFolhaDecimo := selectIntoInteger('SELECT count(*) as contador
                                      FROM (SELECT registro_evento_decimo.cod_contrato
                                              FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                                 , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                                                 , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                                 , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                 , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                 , pessoal'|| stEntidade ||'.servidor
                                             WHERE registro_evento_decimo.cod_registro     = ultimo_registro_evento_decimo.cod_registro
                                               AND registro_evento_decimo.timestamp        = ultimo_registro_evento_decimo.timestamp
                                               AND registro_evento_decimo.cod_evento       = ultimo_registro_evento_decimo.cod_evento
                                               AND registro_evento_decimo.desdobramento    = ultimo_registro_evento_decimo.desdobramento
                                               AND registro_evento_decimo.cod_registro     = evento_decimo_calculado.cod_registro
                                               AND registro_evento_decimo.timestamp        = evento_decimo_calculado.timestamp_registro
                                               AND registro_evento_decimo.cod_evento       = evento_decimo_calculado.cod_evento
                                               AND registro_evento_decimo.desdobramento    = evento_decimo_calculado.desdobramento
                                               AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                               AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                               AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                               AND servidor.numcgm = '|| inNumCgm ||'
                                               AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                          GROUP BY registro_evento_decimo.cod_contrato) as decimo');                                          
    IF inCountFolhaComplementar >= 1 AND NOT (inCountFolhaComplementar = 1 AND inCountFolhaSalario = 0 AND inCountFolhaFerias = 0 AND inCountFolhaRescisao = 0 AND inCountFolhaDecimo = 0) THEN
--         --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA COMPLEMENTARES DO CONTRATO QUE ESTÁ SENDO CALCULADO
--         nuValorBaseCs := selectIntoNumeric('SELECT evento_complementar_calculado.valor
--                                      FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
--                                         , folhapagamento'|| stEntidade ||'.tabela_irrf
--                                         , (   SELECT cod_tabela
--                                                    , max(timestamp) as timestamp
--                                                 FROM folhapagamento'||stEntidade||'.tabela_irrf
--                                                WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
--                                             GROUP BY cod_tabela) as max_tabela_irrf
--                                         , folhapagamento'|| stEntidade ||'.evento
--                                         , folhapagamento'|| stEntidade ||'.registro_evento_complementar
--                                         , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
--                                         , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
--                                         , pessoal'|| stEntidade ||'.servidor_contrato_servidor
--                                         , pessoal'|| stEntidade ||'.servidor
--                                     WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
--                                       AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
--                                       AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
--                                       AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
--                                       AND tabela_irrf_evento.cod_evento = evento.cod_evento
--                                       AND evento.cod_evento             = registro_evento_complementar.cod_evento
--                                       AND registro_evento_complementar.cod_evento    = ultimo_registro_evento_complementar.cod_evento
--                                       AND registro_evento_complementar.timestamp     = ultimo_registro_evento_complementar.timestamp
--                                       AND registro_evento_complementar.cod_registro  = ultimo_registro_evento_complementar.cod_registro
--                                       AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
--                                       AND registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
--                                       AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
--                                       AND registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
--                                       AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
--                                       AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
--                                       AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
--                                       AND servidor.numcgm = '|| inNumCgm ||'
--                                       AND tabela_irrf_evento.cod_tipo = 7
--                                       AND registro_evento_complementar.cod_configuracao = '|| inCodConfiguracao ||'
--                                       AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao);     

        IF  stSituacaoFolhaSalario = 'f'  AND inCodConfiguracao = 1 THEN
            nuValorBaseFP := selectIntoNumeric('SELECT SUM(evento_calculado.valor) as valor
                                         FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                            , folhapagamento'|| stEntidade ||'.tabela_irrf
                                            , (   SELECT cod_tabela
                                                       , max(timestamp) as timestamp
                                                    FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                   WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                GROUP BY cod_tabela) as max_tabela_irrf
                                            , folhapagamento'|| stEntidade ||'.evento                               
                                            , folhapagamento'|| stEntidade ||'.evento_calculado
                                            , folhapagamento'|| stEntidade ||'.registro_evento
                                            , folhapagamento'|| stEntidade ||'.ultimo_registro_evento
                                            , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                            , folhapagamento'|| stEntidade ||'.contrato_servidor_periodo
                                            , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            , folhapagamento'|| stEntidade ||'.folha_situacao
                                            , (  SELECT cod_periodo_movimentacao
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.folha_situacao
                                               GROUP BY cod_periodo_movimentacao) as max_folha_situacao
                                            , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                            , pessoal'|| stEntidade ||'.servidor
                                        WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                          AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                          AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                          AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                          AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                          AND evento.cod_evento = registro_evento.cod_evento
                                          AND evento_calculado.cod_registro = registro_evento.cod_registro
                                          AND evento_calculado.cod_evento = registro_evento.cod_evento
                                          AND evento_calculado.timestamp_registro = registro_evento.timestamp
                                          AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                                          AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento
                                          AND registro_evento.timestamp = ultimo_registro_evento.timestamp
                                          AND registro_evento.cod_registro =  registro_evento_periodo.cod_registro
                                          AND registro_evento_periodo.cod_contrato = contrato_servidor_periodo.cod_contrato
                                          AND registro_evento_periodo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                                          AND contrato_servidor_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                          AND periodo_movimentacao.cod_periodo_movimentacao = folha_situacao.cod_periodo_movimentacao
                                          AND folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao
                                          AND folha_situacao.timestamp = max_folha_situacao.timestamp
                                          AND folha_situacao.situacao = ''f''
                                          AND contrato_servidor_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                          AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                          AND servidor.numcgm = '|| inNumCgm ||'
                                          AND tabela_irrf_evento.cod_tipo = 7
                                          AND periodo_movimentacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao);
        END IF;
        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA FÉRIAS DO CONTRATO QUE ESTÁ SENDO CALCULADO
        IF inCodConfiguracao = 2 THEN
            nuValorBaseFF := selectIntoNumeric(' SELECT SUM(evento_ferias_calculado.valor) AS valor
                                         FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                            , folhapagamento'|| stEntidade ||'.tabela_irrf
                                            , (   SELECT cod_tabela
                                                       , max(timestamp) as timestamp
                                                    FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                   WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                GROUP BY cod_tabela) as max_tabela_irrf
                                            , folhapagamento'|| stEntidade ||'.evento
                                            , folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                            , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                                            , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                            , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                            , pessoal'|| stEntidade ||'.servidor
                                        WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                          AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                          AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                          AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                          AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                          AND evento.cod_evento             = registro_evento_ferias.cod_evento
                                          AND registro_evento_ferias.cod_evento    = ultimo_registro_evento_ferias.cod_evento
                                          AND registro_evento_ferias.timestamp     = ultimo_registro_evento_ferias.timestamp
                                          AND registro_evento_ferias.cod_registro  = ultimo_registro_evento_ferias.cod_registro
                                          AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento
                                          AND registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                                          AND registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                                          AND registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                                          AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                          AND registro_evento_ferias.cod_contrato  = servidor_contrato_servidor.cod_contrato
                                          AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                          AND tabela_irrf_evento.cod_tipo = 7
                                          AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                          AND servidor.numcgm = '|| inNumCgm);
        END IF;                                          
        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA DÉCIMO DO CONTRATO QUE ESTÁ SENDO CALCULADO
        IF inCodConfiguracao = 3 THEN
            nuValorBaseFD := selectIntoNumeric(' SELECT SUM(evento_decimo_calculado.valor) AS valor
                                         FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                            , folhapagamento'|| stEntidade ||'.tabela_irrf
                                            , (   SELECT cod_tabela
                                                       , max(timestamp) as timestamp
                                                    FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                   WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                GROUP BY cod_tabela) as max_tabela_irrf
                                            , folhapagamento'|| stEntidade ||'.evento
                                            , folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                            , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                                            , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                            , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                            , pessoal'|| stEntidade ||'.servidor
                                        WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                          AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                          AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                          AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                          AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                          AND evento.cod_evento             = registro_evento_decimo.cod_evento
                                          AND registro_evento_decimo.cod_evento    = ultimo_registro_evento_decimo.cod_evento
                                          AND registro_evento_decimo.timestamp     = ultimo_registro_evento_decimo.timestamp
                                          AND registro_evento_decimo.cod_registro  = ultimo_registro_evento_decimo.cod_registro
                                          AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento
                                          AND registro_evento_decimo.cod_evento    = evento_decimo_calculado.cod_evento
                                          AND registro_evento_decimo.timestamp     = evento_decimo_calculado.timestamp_registro
                                          AND registro_evento_decimo.cod_registro  = evento_decimo_calculado.cod_registro
                                          AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                          AND (registro_evento_decimo.desdobramento = ''D'' OR registro_evento_decimo.desdobramento = ''C'')
                                          AND registro_evento_decimo.cod_contrato  = servidor_contrato_servidor.cod_contrato
                                          AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                          AND tabela_irrf_evento.cod_tipo = 7
                                          AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                          AND servidor.numcgm = '|| inNumCgm); 
        END IF;                                          
        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA RESCISÃO DO CONTRATO QUE ESTÁ SENDO CALCULADO                
        IF inCodConfiguracao = 1 THEN
            stDesdobramento = 'S';
        END IF;
        IF inCodConfiguracao = 2 THEN
            stDesdobramento = 'V,P';
        END IF;
        IF inCodConfiguracao = 3 THEN
            stDesdobramento = 'D';
        END IF;                
        nuValorBaseFR := selectIntoNumeric(' SELECT SUM(evento_rescisao_calculado.valor) AS valor
                                             FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                , (   SELECT cod_tabela
                                                           , max(timestamp) as timestamp
                                                        FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                       WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                    GROUP BY cod_tabela) as max_tabela_irrf
                                                , folhapagamento'|| stEntidade ||'.evento
                                                , folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                                , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                                                , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                                , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                , pessoal'|| stEntidade ||'.servidor
                                            WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                              AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                              AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                              AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                              AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                              AND evento.cod_evento             = registro_evento_rescisao.cod_evento
                                              AND registro_evento_rescisao.cod_evento    = ultimo_registro_evento_rescisao.cod_evento
                                              AND registro_evento_rescisao.timestamp     = ultimo_registro_evento_rescisao.timestamp
                                              AND registro_evento_rescisao.cod_registro  = ultimo_registro_evento_rescisao.cod_registro
                                              AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento
                                              AND registro_evento_rescisao.cod_evento    = evento_rescisao_calculado.cod_evento
                                              AND registro_evento_rescisao.timestamp     = evento_rescisao_calculado.timestamp_registro
                                              AND registro_evento_rescisao.cod_registro  = evento_rescisao_calculado.cod_registro
                                              AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                              AND registro_evento_rescisao.desdobramento IN ('|| quote_literal(stDesdobramento) ||')
                                              AND registro_evento_rescisao.cod_contrato  = servidor_contrato_servidor.cod_contrato
                                              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                              AND tabela_irrf_evento.cod_tipo = 7
                                              AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                              AND servidor.numcgm = '|| inNumCgm);                                                                              
        IF nuValorBaseFP IS NULL THEN
            nuValorBaseFP := 0;
        END IF;       
        IF nuValorBaseFF IS NULL THEN
            nuValorBaseFF := 0;
        END IF;       
        IF nuValorBaseFD IS NULL THEN
            nuValorBaseFD := 0;
        END IF;       
        IF nuValorBaseFR IS NULL THEN
            nuValorBaseFR := 0;
        END IF;                                       
    END IF;
    
    ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------
    ---------------------SOMENTE DEVERÁ AJUSTAR COM DESCONTO EXTERNO QUANDO
    ---------------------A CONFIGURACAO FOR IGUAL A 1 (SALÁRIO)
    ---------------------POIS SOMENTE ESTÁ SENDO INCLUÍDO O EVENTO AUTOMÁTICO DE DESCONTO
    ---------------------EXTERNO PARA A CONFIGURAÇÃO 1
    IF inCodConfiguracao = 1 THEN
    stSql := ' SELECT registro_evento_complementar.cod_contrato
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                    , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                    , pessoal'|| stEntidade ||'.servidor
                WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                  AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                  AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                  AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                  AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                  AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                  AND registro_evento_complementar.cod_configuracao = '|| inCodConfiguracao ||'
                  AND numcgm = '|| inNumCgm ||'
             GROUP BY registro_evento_complementar.cod_contrato';
    FOR reRegistro IN EXECUTE stSql
    LOOP        
        stSql := '     SELECT desconto_externo_irrf.vl_base_irrf as base
                            , desconto_externo_irrf_valor.valor_irrf as desconto
                         FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf                                    
                    LEFT JOIN (SELECT desconto_externo_irrf_valor.*
                                 FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf_valor
                                    , (   SELECT cod_contrato
                                               , max(timestamp_valor) as timestamp_valor
                                            FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf_valor
                                        GROUP BY cod_contrato) as max_desconto_externo_irrf_valor
                                WHERE desconto_externo_irrf_valor.cod_contrato = max_desconto_externo_irrf_valor.cod_contrato
                                  AND desconto_externo_irrf_valor.timestamp_valor = max_desconto_externo_irrf_valor.timestamp_valor) AS desconto_externo_irrf_valor
                           ON desconto_externo_irrf_valor.cod_contrato = desconto_externo_irrf.cod_contrato
                          AND desconto_externo_irrf_valor.timestamp = desconto_externo_irrf_valor.timestamp      
                            , (  SELECT cod_contrato
                                      , max(timestamp) as timestamp
                                   FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf
                                  WHERE vigencia <= '|| quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)) ||'
                               GROUP BY cod_contrato) as max_desconto_externo_irrf
                        WHERE desconto_externo_irrf.cod_contrato = max_desconto_externo_irrf.cod_contrato
                          AND desconto_externo_irrf.timestamp = max_desconto_externo_irrf.timestamp
                          AND NOT EXISTS (SELECT 1
                                            FROM folhapagamento'|| stEntidade ||'.desconto_externo_irrf_anulado
                                           WHERE desconto_externo_irrf.cod_contrato = desconto_externo_irrf_anulado.cod_contrato
                                             AND desconto_externo_irrf.timestamp = desconto_externo_irrf_anulado.timestamp)
                          AND desconto_externo_irrf.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reDescontoExterno;
        CLOSE crCursor;   
        IF reDescontoExterno.base IS NOT NULL THEN 
            nuValorBase     := nuValorBase + reDescontoExterno.base;
        END IF;
        IF reDescontoExterno.desconto IS NOT NULL THEN
            nuSomaDescontoExterno := nuSomaDescontoExterno + reDescontoExterno.desconto;
        END IF;
    END LOOP;
    END IF;
    ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------        
    
        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA COMPLEMENTARES DO CONTRATO QUE ESTÁ SENDO CALCULADO
        nuValorBaseCs := selectIntoNumeric('SELECT sum(evento_complementar_calculado.valor) as valor
                                     FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                        , folhapagamento'|| stEntidade ||'.tabela_irrf
                                        , (   SELECT cod_tabela
                                                   , max(timestamp) as timestamp
                                                FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                               WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                            GROUP BY cod_tabela) as max_tabela_irrf
                                        , folhapagamento'|| stEntidade ||'.evento
                                        , folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                        , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
                                        , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                        , pessoal'|| stEntidade ||'.servidor
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
                                      AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                                      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                      AND servidor.numcgm = '|| inNumCgm ||'
                                      AND tabela_irrf_evento.cod_tipo = 7
                                      AND registro_evento_complementar.cod_configuracao = '|| inCodConfiguracao ||'
                                      AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao);         
        IF nuValorBaseCs IS NULL THEN
            nuValorBaseCs := 0;
        END IF;   
            
    --SOMA DO VALOR DA BASE DA FOLHA SALÁRIO COM O SOMATÓRIO DAS BASES DAS COMPLEMENTARES
    nuValorBase := nuValorBase + nuValorBaseCs + nuValorBaseFP + nuValorBaseFF + nuValorBaseFD + nuValorBaseFR;

    --VERIFICAÇÃO SE O VALOR (nuValorBase) É MAIOR OU IGUAL A PRIMEIRA FAIXA DE DESCONTO DA TABELA DE IRRF
    boValorMaior := selectIntoBoolean('SELECT TRUE as booleano
                                FROM folhapagamento'|| stEntidade ||'.faixa_desconto_irrf
                                   , (  SELECT cod_tabela
                                             , max(timestamp) as timestamp
                                          FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                         WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                      GROUP BY cod_tabela) as max_tabela_irrf
                               WHERE faixa_desconto_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                 AND faixa_desconto_irrf.timestamp = max_tabela_irrf.timestamp
                                 AND '|| nuValorBase ||' >= faixa_desconto_irrf.vl_inicial
                            GROUP BY faixa_desconto_irrf.cod_tabela');
    IF boValorMaior = TRUE THEN
        --BUSCA VALOR DA BASE DE DEDUÇÃO DE IRRF DA FOLHA SALÁRIO QUE ESTÁ SENDO CALCULADA
        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE DEDUÇÃO DE IRRF DAS FOLHA COMPLEMENTARES DO CONTRATO QUE ESTÁ SENDO CALCULADO
        nuValorBaseDeducao := processarSomatorioDeducoesComplementar(boComPensao,inCodConfiguracao);

        --SUBTRAÇÃO DO SOMATÓRIO VALOR DA BASE DO VALOR DA BASE DE DEDUÇÃO
        nuValorBase := nuValorBase - nuValorBaseDeducao;
        --BUSCA DA ALIQUOTA DE DESCONTO QUE SE ENQUADRA NO VALOR (nuValorBase) ENCONTRADO
        stSql := 'SELECT faixa_desconto_irrf.aliquota
                        , faixa_desconto_irrf.parcela_deduzir
                     FROM folhapagamento'|| stEntidade ||'.faixa_desconto_irrf
                        , folhapagamento'|| stEntidade ||'.tabela_irrf
                        , (  SELECT cod_tabela
                                  , max(timestamp) as timestamp
                               FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                              WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                           GROUP BY cod_tabela) as max_tabela_irrf
                    WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                      AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                      AND tabela_irrf.cod_tabela = faixa_desconto_irrf.cod_tabela
                      AND tabela_irrf.timestamp  = faixa_desconto_irrf.timestamp
                      AND faixa_desconto_irrf.vl_inicial <= '|| nuValorBase ||'
                      AND faixa_desconto_irrf.vl_final   >= '|| nuValorBase ||' ';
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO nuAliquotaDesconto,nuParcelaDeduzir;
        CLOSE crCursor;

        IF nuAliquotaDesconto IS NOT NULL THEN
            --VALOR ENCONTRADO BASEADO NA ALIQUOTA DE DESCONTO
            nuValorDescontoFC := nuValorBase * nuAliquotaDesconto / 100;
            --SUBTRAÇÃO DO VALOR ENCONTRATO (nuValorDescontoFC) DO CAMPO parcela_deduzir 
            nuValorDescontoFC := nuValorDescontoFC - nuParcelaDeduzir;

            --BUSCA SOMATÓRIO DO VALOR DOS DESCONTOS  DE IRRF DAS FOLHA COMPLEMENTARES DO CONTRATO QUE ESTÁ SENDO CALCULADO
            IF boComPensao = TRUE THEN
                inCodTipo = 6;
            ELSE
                inCodTipo = 3;
            END IF;
            
            nuValorDescontoCs := selectIntoNumeric('SELECT SUM(evento_complementar_calculado.valor) AS valor
                                             FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                , (   SELECT cod_tabela
                                                           , max(timestamp) as timestamp
                                                        FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                       WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                    GROUP BY cod_tabela) as max_tabela_irrf
                                                , folhapagamento'|| stEntidade ||'.evento
                                                , folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                                , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
                                                , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                                , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                , pessoal'|| stEntidade ||'.servidor
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
                                              AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                                              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                              AND servidor.numcgm = '|| inNumCgm ||'
                                              AND tabela_irrf_evento.cod_tipo = '|| inCodTipo ||'
                                              AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                              AND registro_evento_complementar.cod_configuracao = '|| inCodConfiguracao ||'
                                              AND not (registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'
                                                       AND registro_evento_complementar.cod_contrato = '|| inCodContrato ||')');

            IF  stSituacaoFolhaSalario = 'f' AND inCodConfiguracao = 1 THEN
                nuValorDescontoFP := selectIntoNumeric('SELECT SUM(evento_calculado.valor) as valor
                                                 FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                    , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                    , (   SELECT cod_tabela
                                                               , max(timestamp) as timestamp
                                                            FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                           WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                        GROUP BY cod_tabela) as max_tabela_irrf
                                                    , folhapagamento'|| stEntidade ||'.evento                               
                                                    , folhapagamento'|| stEntidade ||'.evento_calculado
                                                    , folhapagamento'|| stEntidade ||'.registro_evento
                                                    , folhapagamento'|| stEntidade ||'.ultimo_registro_evento
                                                    , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                                    , folhapagamento'|| stEntidade ||'.contrato_servidor_periodo
                                                    , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                    , folhapagamento'|| stEntidade ||'.folha_situacao
                                                    , (  SELECT cod_periodo_movimentacao
                                                              , max(timestamp) as timestamp
                                                           FROM folhapagamento'|| stEntidade ||'.folha_situacao
                                                       GROUP BY cod_periodo_movimentacao) as max_folha_situacao
                                                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                    , pessoal'|| stEntidade ||'.servidor
                                                WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                                  AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                                  AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                                  AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                                  AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                                  AND evento.cod_evento = registro_evento.cod_evento
                                                  AND evento_calculado.cod_registro = registro_evento.cod_registro
                                                  AND evento_calculado.cod_evento = registro_evento.cod_evento
                                                  AND evento_calculado.timestamp_registro = registro_evento.timestamp
                                                  AND registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                                                  AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento
                                                  AND registro_evento.timestamp = ultimo_registro_evento.timestamp
                                                  AND registro_evento.cod_registro =  registro_evento_periodo.cod_registro
                                                  AND registro_evento_periodo.cod_contrato = contrato_servidor_periodo.cod_contrato
                                                  AND registro_evento_periodo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                                                  AND contrato_servidor_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                                  AND periodo_movimentacao.cod_periodo_movimentacao = folha_situacao.cod_periodo_movimentacao
                                                  AND folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao
                                                  AND folha_situacao.timestamp = max_folha_situacao.timestamp
                                                  AND folha_situacao.situacao = ''f''
                                                  AND contrato_servidor_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                  AND servidor.numcgm = '|| inNumCgm ||'
                                                  AND tabela_irrf_evento.cod_tipo = '|| inCodTipo ||'
                                                  AND periodo_movimentacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao);
            END IF;

            IF inCodConfiguracao = 2 THEN
                nuValorDescontoFF := selectIntoNumeric(' SELECT SUM(evento_ferias_calculado.valor) AS valor
                                                  FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                     , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                     , (   SELECT cod_tabela
                                                                , max(timestamp) as timestamp
                                                             FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                            WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                         GROUP BY cod_tabela) as max_tabela_irrf
                                                     , folhapagamento'|| stEntidade ||'.evento
                                                     , folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                                     , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                                                     , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                                     , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                     , pessoal'|| stEntidade ||'.servidor
                                                 WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                                   AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                                   AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                                   AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                                   AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                                   AND evento.cod_evento             = registro_evento_ferias.cod_evento
                                                   AND registro_evento_ferias.cod_evento    = ultimo_registro_evento_ferias.cod_evento
                                                   AND registro_evento_ferias.timestamp     = ultimo_registro_evento_ferias.timestamp
                                                   AND registro_evento_ferias.cod_registro  = ultimo_registro_evento_ferias.cod_registro
                                                   AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento
                                                   AND registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                                                   AND registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                                                   AND registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                                                   AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                                   AND registro_evento_ferias.cod_contrato  = servidor_contrato_servidor.cod_contrato
                                                   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                   AND tabela_irrf_evento.cod_tipo = 7
                                                   AND tabela_irrf_evento.cod_tipo = '|| inCodTipo ||'
                                                   AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                   AND servidor.numcgm = '|| inNumCgm);
            END IF;
            
            IF inCodConfiguracao = 3 THEN
                nuValorDescontoFD := selectIntoInteger(' SELECT SUM(evento_decimo_calculado.valor) AS valor
                                                  FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                     , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                     , (   SELECT cod_tabela
                                                                , max(timestamp) as timestamp
                                                             FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                            WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                         GROUP BY cod_tabela) as max_tabela_irrf
                                                     , folhapagamento'|| stEntidade ||'.evento
                                                     , folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                                     , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                                                     , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                                     , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                     , pessoal'|| stEntidade ||'.servidor
                                                 WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                                   AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                                   AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                                   AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                                   AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                                   AND evento.cod_evento             = registro_evento_decimo.cod_evento
                                                   AND registro_evento_decimo.cod_evento    = ultimo_registro_evento_decimo.cod_evento
                                                   AND registro_evento_decimo.timestamp     = ultimo_registro_evento_decimo.timestamp
                                                   AND registro_evento_decimo.cod_registro  = ultimo_registro_evento_decimo.cod_registro
                                                   AND registro_evento_decimo.desdobramento = ultimo_registro_evento_decimo.desdobramento
                                                   AND registro_evento_decimo.cod_evento    = evento_decimo_calculado.cod_evento
                                                   AND registro_evento_decimo.timestamp     = evento_decimo_calculado.timestamp_registro
                                                   AND registro_evento_decimo.cod_registro  = evento_decimo_calculado.cod_registro
                                                   AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                                   AND registro_evento_decimo.cod_contrato  = servidor_contrato_servidor.cod_contrato
                                                   AND (registro_evento_decimo.desdobramento  = ''D'' OR registro_evento_decimo.desdobramento  = ''C'')
                                                   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                   AND tabela_irrf_evento.cod_tipo = 7
                                                   AND tabela_irrf_evento.cod_tipo = '|| inCodTipo ||'
                                                   AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                   AND servidor.numcgm = '|| inNumCgm);
            END IF;      
            
            IF inCodConfiguracao = 1 THEN
                stDesdobramento = 'S';
            END IF;
            IF inCodConfiguracao = 2 THEN
                stDesdobramento = 'V,P';
            END IF;
            IF inCodConfiguracao = 3 THEN
                stDesdobramento = 'D';
            END IF;                
            nuValorDescontoFR := selectIntoNumeric(' SELECT SUM(evento_rescisao_calculado.valor) AS valor
                                                  FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                     , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                     , (   SELECT cod_tabela
                                                                , max(timestamp) as timestamp
                                                             FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                            WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                                         GROUP BY cod_tabela) as max_tabela_irrf
                                                     , folhapagamento'|| stEntidade ||'.evento
                                                     , folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                                     , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                                                     , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                                     , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                     , pessoal'|| stEntidade ||'.servidor
                                                 WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                                   AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                                   AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                                                   AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                                                   AND tabela_irrf_evento.cod_evento = evento.cod_evento
                                                   AND evento.cod_evento             = registro_evento_rescisao.cod_evento
                                                   AND registro_evento_rescisao.cod_evento    = ultimo_registro_evento_rescisao.cod_evento
                                                   AND registro_evento_rescisao.timestamp     = ultimo_registro_evento_rescisao.timestamp
                                                   AND registro_evento_rescisao.cod_registro  = ultimo_registro_evento_rescisao.cod_registro
                                                   AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento
                                                   AND registro_evento_rescisao.cod_evento    = evento_rescisao_calculado.cod_evento
                                                   AND registro_evento_rescisao.timestamp     = evento_rescisao_calculado.timestamp_registro
                                                   AND registro_evento_rescisao.cod_registro  = evento_rescisao_calculado.cod_registro
                                                   AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                                   AND registro_evento_rescisao.cod_contrato  = servidor_contrato_servidor.cod_contrato
                                                   AND registro_evento_rescisao.desdobramento IN ('|| quote_literal(stDesdobramento) ||')
                                                   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                   AND tabela_irrf_evento.cod_tipo = 7
                                                   AND tabela_irrf_evento.cod_tipo = '|| inCodTipo ||'
                                                   AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                   AND servidor.numcgm = '|| inNumCgm);                          
            
            --SUBTRAÇÃO DOS VALORES JÁ DESCONTADOS NAS FOLHAS COMPLEMENTARES
            IF nuValorDescontoCs IS NULL THEN
                nuValorDescontoCs := 0;
            END IF;
            IF nuValorDescontoFP IS NULL THEN
                nuValorDescontoFP := 0;
            END IF;
            IF nuValorDescontoFF IS NULL THEN
                nuValorDescontoFF := 0;
            END IF;
            IF nuValorDescontoFD IS NULL THEN
                nuValorDescontoFD := 0;
            END IF;
            IF nuValorDescontoFR IS NULL THEN
                nuValorDescontoFR := 0;
            END IF;                                
            nuValorDescontoFC := nuValorDescontoFC - (nuValorDescontoCs + nuValorDescontoFP + nuValorDescontoFF + nuValorDescontoFD + nuValorDescontoFR + nuSomaDescontoExterno);
            
            --BUSCA COD_EVENTO, COD_REGISTRO E TIMESTAMP_REGISTRO DO EVENTO DE DESCONTO PARA ATUALIZAÇÃO
            stDadosRegistro     := buscaDadosRegistroEventoComplementarDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,inCodConfiguracao);               
            arDadosRegistro     := string_to_array(stDadosRegistro,'#');
            inCodEvento         := arDadosRegistro[1];
            inCodRegistro       := arDadosRegistro[2];
            stTimestampRegistro := arDadosRegistro[3];
            --ATUALIZA TABELA
            stSql := 'UPDATE folhapagamento'|| stEntidade ||'.evento_complementar_calculado SET valor = '|| nuValorDescontoFC ||',
                                                                    quantidade = '|| nuAliquotaDesconto ||'
             WHERE cod_evento         = '|| inCodEvento ||'
               AND cod_registro       = '|| inCodRegistro ||'
               AND timestamp_registro = '|| quote_literal(stTimestampRegistro) ||'
               AND cod_configuracao   = '|| inCodConfiguracao;
            IF stSql IS NOT NULL THEN
                EXECUTE stSql;
            END IF;
        END IF;    
    END IF;    
    
    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';


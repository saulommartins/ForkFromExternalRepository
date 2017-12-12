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
/**
   * Funç PLSQL
   * Data de Criaç: 12/04/2007


   * @author Analista: Dagiane
   * @author Desenvolvedor: Diego Lemos de Souza

   * @package URBEM
   * @subpackage

   $Revision: 25971 $
   $Name$
   $Author: souzadl $
   $Date: 2007-10-10 13:08:17 -0300 (Qua, 10 Out 2007) $

   * Casos de uso: uc-04.05.18
*/

CREATE OR REPLACE FUNCTION processarAjusteIRRFRescisao(BOOLEAN) RETURNS BOOLEAN as $$
DECLARE
    boComPensao                     ALIAS FOR $1;
    inCodPeriodoMovimentacao        INTEGER;
    inCodContrato                   INTEGER;
    inCodEvento                     INTEGER;
    inCodRegistro                   INTEGER;
    inCodTipo                       INTEGER;
    inNumCgm                        INTEGER;
    inCountFolhaRescisao            INTEGER;
    inCountFolhaDecimo              INTEGER;
    inCountFolhaComplementar        INTEGER;
    inCountFolhaSalario             INTEGER;
    inCountFolhaFerias              INTEGER;
    inCodEventoDescontoIRRF         INTEGER;
    nuValorBaseFR                   NUMERIC := 0.00;
    nuValorBase                     NUMERIC := 0.00;
    nuValorBaseDeducao              NUMERIC := 0.00;
    nuSomaBaseDeducaoDecimo         NUMERIC := 0.00;
    nuSomaBaseDeducaoSaldoAviso     NUMERIC := 0.00;
    nuSomaBaseDeducaoFerias         NUMERIC := 0.00;
    nuAliquotaDesconto              NUMERIC := 0.00;
    nuParcelaDeduzir                NUMERIC := 0.00;
    nuValorDescontoOFR              NUMERIC := 0.00;
    nuSomaDescontoOFRSaldoAviso     NUMERIC := 0.00;
    nuSomaDescontoOFRFerias         NUMERIC := 0.00;
    nuSomaDescontoOFRDecimo         NUMERIC := 0.00;
    nuValorDescontoFR               NUMERIC := 0.00;
    nuSomaBaseSaldoAviso            NUMERIC := 0.00;
    nuSomaBaseDecimo                NUMERIC := 0.00;
    nuSomaBaseFerias                NUMERIC := 0.00;
    nuTemp                          NUMERIC := 0.00;
    nuSomaDescontoExterno           NUMERIC := 0.00;
    nuValorDescontoIRRFComplementar NUMERIC := 0.00;
    nuValorDescontoIRRFFolhas       NUMERIC := 0.00;
    nuBaseRescisaoDesdobramentoSaldoAviso   NUMERIC := 0.00;
    nuBaseRescisaoDesdobramentoDecimo       NUMERIC := 0.00;
	nuBaseRescisaoDesdobramentoFerias NUMERIC := 0.00;
    nuBaseRescisaoDesdobramentoFeriasVencidas NUMERIC := 0.00;
	nuBaseRescisaoDesdobramentoFeriasProporc  NUMERIC := 0.00;
    stSql                           VARCHAR := '';
    dtVigencia                      VARCHAR := '';
    stTimestampRegistro             VARCHAR := '';
    stDadosRegistro                 VARCHAR := '';
    stDesdobramentoRegistro         VARCHAR := '';
    stSituacaoFolhaSalario          VARCHAR := '';
    stSituacaoFolhaComplementar     VARCHAR := '';
    arDadosRegistro                 VARCHAR[];
    arDesdobramento                 VARCHAR[];
    reRegistro                      RECORD;
    reBases                         RECORD;
    reDescontoExterno               RECORD;
    boRetorno                       BOOLEAN := TRUE;
    boValorMaior                    BOOLEAN := TRUE;
    boAjustar                       BOOLEAN := FALSE;   
    crCursor                        REFCURSOR;
    stEntidade                      VARCHAR;
    nuValorDescontoIRRFComplementarFerias NUMERIC := 0.00;
    nuValorDescontoIRRFComplementarDecimo NUMERIC := 0.00;
    nuValorDescontoIRRFFolhasComplementar NUMERIC := 0.00;
    nuValorDescontoIRRFFolhasComplementarDecimo NUMERIC := 0.00;
    nuValorDescontoIRRFFolhasComplementarFerias NUMERIC := 0.00;
BEGIN
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');    
    inCodContrato               := recuperarBufferInteiro('inCodContrato');
    inNumCgm                    := recuperarBufferInteiro('inNumCgm');
    dtVigencia                  := recuperarBufferTexto('dtVigenciaIrrf');
    stEntidade                  := recuperarBufferTexto('stEntidade');

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
                                               AND servidor.numcgm = '|| inNumCgm ||'
                                               AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                          GROUP BY registro_evento_rescisao.cod_contrato) as rescisao');
    inCountFolhaComplementar := selectIntoInteger('SELECT count(*)
                                            FROM (SELECT registro_evento_complementar.cod_contrato
                                                       , registro_evento_complementar.cod_complementar
                                                    FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                                       , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
                                                       , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                                       , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                       , pessoal'|| stEntidade ||'.servidor
                                                       , folhapagamento'|| stEntidade ||'.complementar_situacao
                                                       , (SELECT cod_periodo_movimentacao
                                                               , cod_complementar
                                                               ,  max(timestamp) as timestamp
                                                            FROM folhapagamento'|| stEntidade ||'.complementar_situacao
                                                          GROUP BY cod_periodo_movimentacao
                                                                 , cod_complementar) as max_complementar_situacao
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
                                                     AND registro_evento_complementar.cod_complementar = complementar_situacao.cod_complementar
                                                     AND registro_evento_complementar.cod_periodo_movimentacao = complementar_situacao.cod_periodo_movimentacao
                                                     AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                                                     AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                                                     AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                                                     AND complementar_situacao.situacao = ''f''
                                                     AND servidor.numcgm = '|| inNumCgm ||'
                                                     AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                GROUP BY registro_evento_complementar.cod_contrato
                                                       , registro_evento_complementar.cod_complementar) as complementar');
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
    inCountFolhaFerias := selectIntoInteger('SELECT COUNT(*) AS contador
                                      FROM (SELECT registro_evento_ferias.cod_contrato
                                              FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                                 , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                                                 , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                                 , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                 , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                 , pessoal'|| stEntidade ||'.servidor
                                                 , pessoal'|| stEntidade ||'.ferias
                                                 , pessoal'|| stEntidade ||'.lancamento_ferias
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
                                               AND registro_evento_ferias.cod_contrato = ferias.cod_contrato
                                               AND ferias.cod_ferias = lancamento_ferias.cod_ferias
                                               AND lancamento_ferias.cod_tipo = 1
                                               AND servidor.numcgm = '|| inNumCgm ||'
                                               AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                          GROUP BY registro_evento_ferias.cod_contrato) AS ferias');
        
    IF inCountFolhaRescisao >= 1 AND NOT (inCountFolhaRescisao = 1 AND inCountFolhaComplementar = 0 AND inCountFolhaDecimo = 0 AND inCountFolhaSalario = 0 AND inCountFolhaFerias = 0) THEN  
        -------------------INÍIO DO AJUSTE COM O COMPLEMENTAR--------------------
        stSituacaoFolhaComplementar := pega0SituacaoDaFolhaComplementar();
        IF stSituacaoFolhaComplementar = 'f' THEN
            --BUSCA SOMATÓIO DO VALOR DAS BASES DE IRRF DAS FOLHA COMPLEMENTARES DO CONTRATO QUE ESTÁSENDO CALCULADO
            FOR inCodConfiguracao IN 1 .. 3 LOOP
                nuTemp := selectIntoNumeric('SELECT COALESCE(SUM(evento_complementar_calculado.valor),0.00) AS valor
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
                                         , folhapagamento'|| stEntidade ||'.contrato_servidor_complementar
                                         , folhapagamento'|| stEntidade ||'.complementar
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
                                       AND registro_evento_complementar.cod_periodo_movimentacao = contrato_servidor_complementar.cod_periodo_movimentacao
                                       AND registro_evento_complementar.cod_complementar = contrato_servidor_complementar.cod_complementar
                                       AND registro_evento_complementar.cod_contrato = contrato_servidor_complementar.cod_contrato
                                       AND contrato_servidor_complementar.cod_periodo_movimentacao = complementar.cod_periodo_movimentacao
                                       AND contrato_servidor_complementar.cod_complementar = complementar.cod_complementar
                                       AND servidor.numcgm = '|| inNumCgm ||'
                                       AND tabela_irrf_evento.cod_tipo = 7
                                       AND registro_evento_complementar.cod_configuracao = '|| inCodConfiguracao ||'
                                       AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao);        
                IF inCodConfiguracao = 1 THEN
                    nuSomaBaseSaldoAviso := nuSomaBaseSaldoAviso + nuTemp;
                END IF;
                IF inCodConfiguracao = 2 THEN
                    nuSomaBaseFerias := nuSomaBaseFerias + nuTemp;
                END IF;
                IF inCodConfiguracao = 3 THEN
                    nuSomaBaseDecimo := nuSomaBaseDecimo + nuTemp;
                END IF;
            END LOOP;
        END IF;
        -------------------FIM DO AJUSTE COM O COMPLEMENTAR-----------------------
        -------------------INÍIO DO AJUSTE COM O DÉIMO--------------------------
        nuSomaBaseDecimo := selectIntoNumeric(' SELECT COALESCE(SUM(evento_decimo_calculado.valor),0.00) AS valor
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
                                      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                      AND tabela_irrf_evento.cod_tipo = 7
                                      AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                      AND servidor.numcgm = '|| inNumCgm);
        -------------------FIM DO AJUSTE COM O DÉIMO-----------------------------
        -------------------INÍIO DO AJUSTE COM O SALÁIO-------------------------
        stSituacaoFolhaSalario      := pega0SituacaoDaFolhaSalario();
        IF  stSituacaoFolhaSalario = 'f'  THEN
            nuTemp := selectIntoNumeric('SELECT COALESCE(SUM(evento_calculado.valor),0.00) as valor
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
                                          AND contrato_servidor_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                          AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                          AND servidor.numcgm = '|| inNumCgm ||'
                                          AND tabela_irrf_evento.cod_tipo = 7
                                          AND periodo_movimentacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao);
            nuSomaBaseSaldoAviso := nuSomaBaseSaldoAviso + nuTemp; 
        END IF;
        -------------------FIM DO AJUSTE COM O SALÁIO----------------------------

        -------------------INICIO DO AJUSTE COM O FÉIAS--------------------------
/*        nuTemp := selectIntoNumeric('SELECT COALESCE(SUM(evento_ferias_calculado.valor),0.00) AS valor
                              FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                 , folhapagamento'|| stEntidade ||'.tabela_irrf
                                 , (   SELECT cod_tabela
                                            , max(timestamp) as timestamp
                                         FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                        WHERE tabela_irrf.vigencia = '|| quote_literal(dtVigencia) ||'
                                     GROUP BY cod_tabela) as max_tabela_irrf
                                 , folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                 , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                                 , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                 , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                 , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                 , pessoal'|| stEntidade ||'.servidor
                                 , pessoal'|| stEntidade ||'.ferias
                                 , pessoal'|| stEntidade ||'.lancamento_ferias
                             WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                               AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                               AND tabela_irrf.cod_tabela = tabela_irrf_evento.cod_tabela
                               AND tabela_irrf.timestamp  = tabela_irrf_evento.timestamp
                               AND tabela_irrf_evento.cod_evento = registro_evento_ferias.cod_evento
                               AND registro_evento_ferias.cod_registro     = ultimo_registro_evento_ferias.cod_registro
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
                               AND registro_evento_ferias.cod_contrato = ferias.cod_contrato
                               AND ferias.cod_ferias = lancamento_ferias.cod_ferias
                               AND lancamento_ferias.ano_competencia = to_char(periodo_movimentacao.dt_inicial,''YYYY'')
                               AND lancamento_ferias.mes_competencia = to_char(periodo_movimentacao.dt_inicial,''MM'')							   
                               AND lancamento_ferias.cod_tipo = 1
                               AND servidor.numcgm = '|| inNumCgm ||'
                               AND tabela_irrf_evento.cod_tipo = 7
                               AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao);
*/							   
        nuSomaBaseFerias := nuSomaBaseFerias + nuTemp;
        -------------------FIM DO AJUSTE COM O FÉIAS-----------------------------


        -------------------INICIO DO AJUSTE COM O RESCISÃ------------------------
        --BUSCA SOMATÓIO DO VALOR DAS BASES DE IRRF DAS FOLHA DÉIMO DO CONTRATO QUE ESTÁSENDO CALCULADO
        stSql := 'SELECT COALESCE(evento_rescisao_calculado.valor,0.00) as valor
                        , evento_rescisao_calculado.desdobramento
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
                      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                      AND tabela_irrf_evento.cod_tipo = 7
                      AND registro_evento_rescisao.cod_periodo_movimentacao ='||  inCodPeriodoMovimentacao ||'
                      AND servidor.numcgm ='|| inNumCgm;
        FOR reBases IN EXECUTE stSql LOOP
            IF reBases.desdobramento = 'S' OR reBases.desdobramento = 'A' THEN
                nuSomaBaseSaldoAviso := nuSomaBaseSaldoAviso + reBases.valor;
                nuBaseRescisaoDesdobramentoSaldoAviso := nuBaseRescisaoDesdobramentoSaldoAviso + reBases.valor;
            END IF;
            --IF reBases.desdobramento = 'V' THEN
            --    nuSomaBaseFerias := nuSomaBaseFerias + reBases.valor;
            --    nuBaseRescisaoDesdobramentoFerias := nuBaseRescisaoDesdobramentoFerias + reBases.valor;
            --END IF;
			IF reBases.desdobramento = 'P' THEN
                nuSomaBaseFerias := nuSomaBaseFerias + reBases.valor;
                nuBaseRescisaoDesdobramentoFeriasProporc := nuBaseRescisaoDesdobramentoFeriasProporc + reBases.valor;
            END IF;
			IF reBases.desdobramento = 'V' THEN
                nuSomaBaseFerias := nuSomaBaseFerias + reBases.valor;
                nuBaseRescisaoDesdobramentoFeriasVencidas := nuBaseRescisaoDesdobramentoFeriasVencidas + reBases.valor;
            END IF;						
            IF reBases.desdobramento = 'D' THEN
                nuSomaBaseDecimo := nuSomaBaseDecimo + reBases.valor;
                nuBaseRescisaoDesdobramentoDecimo := nuBaseRescisaoDesdobramentoDecimo + reBases.valor;
            END IF;            
        END LOOP;
        
        --BUSCA VALOR DA BASE DE DEDUÇO DE IRRF DA FOLHA DÉIMO QUE ESTÁSENDO CALCULADA
        nuSomaBaseDeducaoSaldoAviso := nuSomaBaseDeducaoSaldoAviso + processarSomatorioDeducoesRescisao(boComPensao, 'S');
        IF nuSomaBaseDeducaoSaldoAviso = 0.00 OR nuSomaBaseDeducaoSaldoAviso IS NULL THEN
            nuSomaBaseDeducaoSaldoAviso := nuSomaBaseDeducaoSaldoAviso + processarSomatorioDeducoesRescisao(boComPensao, 'A');
        END IF;
        nuSomaBaseDeducaoFerias     := nuSomaBaseDeducaoFerias     + processarSomatorioDeducoesRescisao(boComPensao, 'P');
		
        IF nuSomaBaseDeducaoFerias = 0.00 OR nuSomaBaseDeducaoFerias IS NULL THEN
            nuSomaBaseDeducaoFerias     := nuSomaBaseDeducaoFerias     + processarSomatorioDeducoesRescisao(boComPensao, 'P');
        END IF;
        nuSomaBaseDeducaoDecimo     := nuSomaBaseDeducaoDecimo     + processarSomatorioDeducoesRescisao(boComPensao, 'D');
 
        --BUSCA VALOR DA BASE DE DEDUCAO DE IRRF DA FOLHA SALARIO DESDOBRAMENTO VAZIO DO CONTRATO QUE ESTA SENDO CALCULADO
        nuSomaBaseDeducaoSaldoAviso := nuSomaBaseDeducaoSaldoAviso + processarSomatorioDeducoesRescisao(boComPensao, '');
        
        IF boComPensao = TRUE THEN
            inCodTipo = 6;
        ELSE
            inCodTipo = 3;
        END IF;

        /* Recupera o Desconto de IRRF da completar e da rescisã*/
        inCodEventoDescontoIRRF := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                                        FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                       WHERE tabela_irrf_evento.cod_tipo = '|| inCodTipo ||'
                                                    ORDER BY timestamp desc 
                                                       LIMIT 1');  

        arDesdobramento := string_to_array('S#A#V#P#D','#');
        FOR inIndex IN 1 .. 5 LOOP 
            nuTemp := selectIntoNumeric(' SELECT COALESCE(SUM(evento_rescisao_calculado.valor),0.00) AS valor
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
                                   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                   AND tabela_irrf_evento.cod_tipo = '|| inCodTipo ||'
                                   AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                   AND registro_evento_rescisao.desdobramento = '|| quote_literal(arDesdobramento[inIndex]) ||'
                                   AND servidor.numcgm = '|| inNumCgm ||'
                                   AND registro_evento_rescisao.cod_contrato != '|| inCodContrato);
                                                                         
            IF arDesdobramento[inIndex] = 'S' OR arDesdobramento[inIndex] = 'A' THEN
                nuSomaDescontoOFRSaldoAviso := nuSomaDescontoOFRSaldoAviso + nuTemp;
    
                -- Desconto do IRRF da Complementar, Configuraç Saláo
                IF stSituacaoFolhaComplementar = 'f' THEN
                    stSql := 'SELECT SUM(evento_complementar_calculado.valor) AS valor
                                FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                          INNER JOIN folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                  ON registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
                                 AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
                                 AND registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
                                 AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                          INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                  ON registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                          INNER JOIN pessoal'|| stEntidade ||'.servidor
                                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               WHERE servidor.numcgm = '|| inNumCgm ||'
                                 AND evento_complementar_calculado.cod_evento = '|| inCodEventoDescontoIRRF ||'
                                 AND registro_evento_complementar.cod_configuracao = 1
                                 AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;

                    nuValorDescontoIRRFComplementar := COALESCE(selectIntoNumeric(stSql),0);
                END IF;
            END IF;
            IF arDesdobramento[inIndex] = 'P' OR arDesdobramento[inIndex] = 'V' THEN
                nuSomaDescontoOFRFerias := nuSomaDescontoOFRFerias + nuTemp;

                -- Desconto do IRRF da Complementar, Configuraç Féas
                IF stSituacaoFolhaComplementar = 'f' THEN
                    stSql := 'SELECT SUM(evento_complementar_calculado.valor) AS valor
                                FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                          INNER JOIN folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                  ON registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
                                 AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
                                 AND registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
                                 AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                          INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                  ON registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                          INNER JOIN pessoal'|| stEntidade ||'.servidor
                                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               WHERE servidor.numcgm = '|| inNumCgm ||'
                                 AND evento_complementar_calculado.cod_evento = '|| inCodEventoDescontoIRRF ||'
                                 AND registro_evento_complementar.cod_configuracao = 2
                                 AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;

                    nuValorDescontoIRRFComplementarFerias := nuValorDescontoIRRFComplementarFerias + COALESCE(selectIntoNumeric(stSql),0);
                END IF;
            END IF;
            IF arDesdobramento[inIndex] = 'D' THEN
                nuSomaDescontoOFRDecimo := nuSomaDescontoOFRDecimo + nuTemp;

                -- Desconto do IRRF da Complementar, configuraç Démo
                IF stSituacaoFolhaComplementar = 'f' THEN
                    stSql := 'SELECT SUM(evento_complementar_calculado.valor) AS valor
                                FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                          INNER JOIN folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                  ON registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
                                 AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
                                 AND registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
                                 AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                          INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                  ON registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                          INNER JOIN pessoal'|| stEntidade ||'.servidor
                                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               WHERE servidor.numcgm = '|| inNumCgm ||'
                                 AND evento_complementar_calculado.cod_evento = '|| inCodEventoDescontoIRRF ||'
                                 AND registro_evento_complementar.cod_configuracao = 3
                                 AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;

                    nuValorDescontoIRRFComplementarDecimo := nuValorDescontoIRRFComplementarDecimo + COALESCE(selectIntoNumeric(stSql),0);
                END IF;
            END IF;
        END LOOP;
        nuValorDescontoIRRFFolhasComplementar := nuValorDescontoIRRFComplementar + nuSomaDescontoOFRDecimo + nuSomaDescontoOFRFerias + nuSomaDescontoOFRSaldoAviso;
        nuValorDescontoIRRFFolhasComplementarFerias := nuValorDescontoIRRFComplementarFerias + nuSomaDescontoOFRDecimo + nuSomaDescontoOFRFerias + nuSomaDescontoOFRSaldoAviso;
        nuValorDescontoIRRFFolhasComplementarDecimo := nuValorDescontoIRRFComplementarDecimo + nuSomaDescontoOFRDecimo + nuSomaDescontoOFRFerias + nuSomaDescontoOFRSaldoAviso;
        -------------------FIM DO AJUSTE COM O RESCISÃ---------------------------
    END IF; 
    
    ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------
    stSql := ' SELECT registro_evento_rescisao.cod_contrato
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                    , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                    , pessoal'|| stEntidade ||'.servidor
                WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                  AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                  AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                  AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                  AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                  AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                  AND numcgm = '|| inNumCgm ||'
             GROUP BY registro_evento_rescisao.cod_contrato';
    
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
            nuSomaBaseSaldoAviso     := nuSomaBaseSaldoAviso + reDescontoExterno.base;
        END IF;
        IF reDescontoExterno.desconto IS NOT NULL THEN
            nuSomaDescontoExterno := nuSomaDescontoExterno + reDescontoExterno.desconto;
        END IF;
    END LOOP;
    ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------        
    
    IF (nuSomaBaseSaldoAviso+nuSomaBaseDecimo+nuSomaBaseFerias) > 0 THEN        
        arDesdobramento := string_to_array('S#P#V#D','#');
        FOR inIndex IN 1 .. 4 LOOP
            boAjustar := FALSE;
            IF arDesdobramento[inIndex] = 'S' THEN
                nuValorBase         := nuSomaBaseSaldoAviso;
                nuValorBaseDeducao  := nuSomaBaseDeducaoSaldoAviso;
                nuValorDescontoOFR  := nuSomaDescontoOFRSaldoAviso + nuSomaDescontoExterno; 
                IF nuBaseRescisaoDesdobramentoSaldoAviso > 0 THEN 
                    boAjustar := TRUE;
                END IF;
            END IF;
            IF arDesdobramento[inIndex] = 'P' THEN
                --nuValorBase         := nuSomaBaseFerias;
		nuValorBase         := nuBaseRescisaoDesdobramentoFeriasProporc;
                nuValorBaseDeducao  := nuSomaBaseDeducaoFerias;
                nuValorDescontoOFR  := nuSomaDescontoOFRFerias;
                IF nuBaseRescisaoDesdobramentoFeriasProporc > 0 THEN 
                    boAjustar := TRUE;
                END IF;                
            END IF;
	    IF arDesdobramento[inIndex] = 'V' THEN
                --nuValorBase         := nuSomaBaseFerias;
		nuValorBase         := nuBaseRescisaoDesdobramentoFeriasVencidas;
                nuValorBaseDeducao  := nuSomaBaseDeducaoFerias;
                nuValorDescontoOFR  := nuSomaDescontoOFRFerias;
                IF nuBaseRescisaoDesdobramentoFeriasVencidas > 0 THEN 
                    boAjustar := TRUE;
                END IF;                
            END IF;
            IF arDesdobramento[inIndex] = 'D' THEN
                nuValorBase := nuSomaBaseDecimo;
                nuValorBaseDeducao  := nuSomaBaseDeducaoDecimo;
                nuValorDescontoOFR  := nuSomaDescontoOFRDecimo;
                IF nuBaseRescisaoDesdobramentoDecimo > 0 THEN 
                    boAjustar := TRUE;
                END IF;                
            END IF;             
                        
            --VERIFICAÇO SE O VALOR (nuValorBase) É MAIOR OU IGUAL A PRIMEIRA FAIXA DE DESCONTO DA TABELA DE IRRF
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
            IF boValorMaior = TRUE AND boAjustar = TRUE THEN
                --SUBTRAÇO DO SOMATÓIO VALOR DA BASE DO VALOR DA BASE DE DEDUÇO
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
                    nuValorDescontoFR := nuValorBase * nuAliquotaDesconto / 100;

                    --SUBTRAÇO DO VALOR ENCONTRATO (nuValorDescontoFR) DO CAMPO parcela_deduzir 
                    nuValorDescontoFR := nuValorDescontoFR - nuParcelaDeduzir;
                    
                    --SUBTRAÇO DOS VALORES JÁDESCONTADOS NAS FOLHAS COMPLEMENTARES
                    IF nuValorDescontoOFR IS NULL THEN
                        nuValorDescontoOFR := 0;
                    END IF;
                    IF arDesdobramento[inIndex] = 'S' THEN
                        nuValorDescontoFR := nuValorDescontoFR - nuValorDescontoIRRFFolhasComplementar;
                    END IF;
                    IF arDesdobramento[inIndex] = 'D' THEN
                        nuValorDescontoFR := nuValorDescontoFR - nuValorDescontoIRRFFolhasComplementarDecimo;
                    END IF;
                    IF arDesdobramento[inIndex] = 'P' THEN
                        nuValorDescontoFR := nuValorDescontoFR - nuValorDescontoIRRFFolhasComplementarFerias;
                    END IF;
                    
                    --BUSCA COD_EVENTO, COD_REGISTRO E TIMESTAMP_REGISTRO DO EVENTO DE DESCONTO PARA ATUALIZAÇO

                    stDadosRegistro         := buscaDadosRegistroEventoRescisaoDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao,arDesdobramento[inIndex]);              
                    arDadosRegistro         := string_to_array(stDadosRegistro,'#');
                    inCodEvento             := arDadosRegistro[1];
                    inCodRegistro           := arDadosRegistro[2];
                    stTimestampRegistro     := arDadosRegistro[3];

					-- NÃO DEVE CONSIDERAR VALORES ABAIXO DE DEZ REAIS, CFE RECEITA FEDERAL
					IF nuValorDescontoFR <= 10 THEN
					   nuValorDescontoFR := 0;
					END IF;
										
                    --ATUALIZA TABELA
                    stSql := 'UPDATE folhapagamento'|| stEntidade ||'.evento_rescisao_calculado 
                                SET valor = '|| nuValorDescontoFR ||'
                                  , quantidade = '|| nuAliquotaDesconto ||'
                              WHERE cod_evento = '|| inCodEvento ||'
                                AND cod_registro = '|| inCodRegistro ||'
                                AND timestamp_registro = '|| quote_literal(stTimestampRegistro) ||'
                                AND desdobramento = '|| quote_literal(arDesdobramento[inIndex]) ||' ';
                    EXECUTE stSql;
                END IF;
            END IF;
        END LOOP;
    END IF;    

    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';



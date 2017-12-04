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
--    * Data de Criação: 01/09/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25459 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-09-13 15:21:44 -0300 (Qui, 13 Set 2007) $
--
--    * Casos de uso: uc-04.05.19
--*/

CREATE OR REPLACE FUNCTION processarAjusteIRRFFerias(BOOLEAN) RETURNS BOOLEAN as $$

DECLARE
    boComPensao                 ALIAS FOR $1;
    inCodPeriodoMovimentacao    INTEGER;
    inCodContrato               INTEGER;
    inCodEvento                 INTEGER;
    inCodEventoBaseIRRF         INTEGER;
    inCodEventoDescontoIRRF     INTEGER;
    inCodRegistro               INTEGER;
    inCodTipo                   INTEGER;
    inNumCgm                    INTEGER;
    inCountFolhaComplementar    INTEGER;
    inCountFolhaFerias          INTEGER;
    nuValorBaseCs               NUMERIC;
    nuValorBaseFF               NUMERIC;
    nuValorBase                 NUMERIC;
    nuValorBaseDeducao          NUMERIC;
    nuAliquotaDesconto          NUMERIC;
    nuParcelaDeduzir            NUMERIC;
    nuValorDescontoCs           NUMERIC;
    nuValorDescontoOFF          NUMERIC;
    nuValorDescontoFF           NUMERIC;
    stSql                       VARCHAR := '';
    dtVigencia                  VARCHAR := '';
    stTimestampRegistro         VARCHAR := '';
    stDadosRegistro             VARCHAR := '';
    stDesdobramentoRegistro     VARCHAR := '';
    stSituacaoComplementar      VARCHAR;
    arDadosRegistro             VARCHAR[];
    reRegistro                  RECORD;
    boRetorno                   BOOLEAN := TRUE;
    boValorMaior                BOOLEAN := TRUE;
    crCursor                    REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');    
    inCodContrato               := recuperarBufferInteiro('inCodContrato');
    inNumCgm                    := recuperarBufferInteiro('inNumCgm');
    dtVigencia                  := recuperarBufferTexto('dtVigenciaIrrf');
    inCountFolhaComplementar := selectIntoInteger('SELECT count(*)
                                            FROM (SELECT registro_evento_complementar.cod_contrato
                                                       , registro_evento_complementar.cod_complementar
                                                    FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                                                       , folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                                                       , folhapagamento'||stEntidade||'.evento_complementar_calculado
                                                       , pessoal'||stEntidade||'.servidor_contrato_servidor
                                                       , pessoal'||stEntidade||'.servidor
                                                       , folhapagamento'||stEntidade||'.complementar_situacao
                                                       , (SELECT cod_periodo_movimentacao
                                                               , cod_complementar
                                                               ,  max(timestamp) as timestamp
                                                            FROM folhapagamento'||stEntidade||'.complementar_situacao
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
                                                     AND registro_evento_complementar.cod_configuracao = 2
                                                     AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                                                     AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                                                     AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                                                     AND complementar_situacao.situacao = ''f''
                                                     AND servidor.numcgm = '||inNumCgm||'
                                                     AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                                GROUP BY registro_evento_complementar.cod_contrato
                                                       , registro_evento_complementar.cod_complementar) as complementar');
    inCountFolhaFerias := selectIntoInteger('SELECT count(*) as contador
                                      FROM (SELECT registro_evento_ferias.cod_contrato
                                              FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                                                 , folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                                                 , folhapagamento'||stEntidade||'.evento_ferias_calculado
                                                 , folhapagamento'||stEntidade||'.periodo_movimentacao
                                                 , pessoal'||stEntidade||'.lancamento_ferias
                                                 , pessoal'||stEntidade||'.ferias
                                                 , pessoal'||stEntidade||'.servidor_contrato_servidor
                                                 , pessoal'||stEntidade||'.servidor
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
                                               AND servidor.numcgm = '||inNumCgm||'
                                               AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                               AND (lancamento_ferias.ano_competencia||lancamento_ferias.mes_competencia = to_char(periodo_movimentacao.dt_final,''yyyymm'')
                                                    OR
                                                    to_char(periodo_movimentacao.dt_final,''yyyymm'') BETWEEN to_char(lancamento_ferias.dt_inicio,''yyyymm'')
                                                                                                          AND to_char(lancamento_ferias.dt_fim,''yyyymm''))
                                          GROUP BY registro_evento_ferias.cod_contrato) as ferias');

    IF inCountFolhaFerias >= 1 AND NOT (inCountFolhaFerias = 1 AND inCountFolhaComplementar = 0) THEN  
        --Busca código do Evento de Base IRRF Salário/Ferias/13o.Salário
        inCodEventoBaseIRRF := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                                       FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                                      WHERE tabela_irrf_evento.cod_tipo = 7
                                                   ORDER BY timestamp desc LIMIT 1');                                                                                                  
                                               
        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA COMPLEMENTARES DO CONTRATO QUE ESTÁ SENDO CALCULADO
        stSql := 'SELECT complementar_situacao.situacao
                    FROM folhapagamento'||stEntidade||'.complementar_situacao
                    , (SELECT cod_periodo_movimentacao
                            , cod_complementar
                            , max(timestamp) as timestamp
                            FROM folhapagamento'||stEntidade||'.complementar_situacao
                        GROUP BY cod_periodo_movimentacao
                            , cod_complementar) as max_complementar_situacao                
                    WHERE complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                    AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                    AND complementar_situacao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
        stSituacaoComplementar := selectIntoVarchar(stSql);     
        IF stSituacaoComplementar = 'f' THEN  
            stSql := 'SELECT SUM(evento_complementar_calculado.valor) AS valor
                        FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                           , folhapagamento'||stEntidade||'.evento_complementar_calculado
                           , pessoal'||stEntidade||'.servidor_contrato_servidor
                           , pessoal'||stEntidade||'.servidor
                       WHERE registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
                         AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
                         AND registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
                         AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                         AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                         AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                         AND servidor.numcgm = '||inNumCgm||'
                         AND evento_complementar_calculado.cod_evento = '||inCodEventoBaseIRRF||'
                         AND registro_evento_complementar.cod_configuracao = 1
                         AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
            nuValorBaseCs := selectIntoNumeric(stSql);
        END IF;                                                       


        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA FÉRIAS DO CONTRATO QUE ESTÁ SENDO CALCULADO
        stSql := ' SELECT SUM(evento_ferias_calculado.valor) AS valor
                       FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                          , folhapagamento'||stEntidade||'.evento_ferias_calculado
                          , pessoal'||stEntidade||'.servidor_contrato_servidor
                          , pessoal'||stEntidade||'.servidor
                      WHERE registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                        AND registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                        AND registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                        AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                        AND registro_evento_ferias.cod_contrato  = servidor_contrato_servidor.cod_contrato
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND evento_ferias_calculado.cod_evento = '||inCodEventoBaseIRRF||'
                        AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                        AND servidor.numcgm = '||inNumCgm;
        nuValorBaseFF := selectIntoNumeric(stSql);

        --SOMA DO VALOR DA BASE DA FOLHA SALÁRIO COM O SOMATÓRIO DAS BASES DAS COMPLEMENTARES
        IF nuValorBaseCs IS NULL THEN
            nuValorBaseCs := 0;
        END IF;
        IF nuValorBaseFF IS NULL THEN
            nuValorBaseFF := 0;
        END IF;
        nuValorBase := nuValorBaseCs + nuValorBaseFF;

        --VERIFICAÇÃO SE O VALOR (nuValorBase) É MAIOR OU IGUAL A PRIMEIRA FAIXA DE DESCONTO DA TABELA DE IRRF
        boValorMaior := selectIntoBoolean('SELECT TRUE as booleano
                                    FROM folhapagamento'||stEntidade||'.faixa_desconto_irrf
                                       , (  SELECT cod_tabela
                                                 , max(timestamp) as timestamp
                                              FROM folhapagamento'||stEntidade||'.tabela_irrf
                                             WHERE tabela_irrf.vigencia = '''||dtVigencia||'''
                                          GROUP BY cod_tabela) as max_tabela_irrf
                                   WHERE faixa_desconto_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                     AND faixa_desconto_irrf.timestamp = max_tabela_irrf.timestamp
                                     AND '||nuValorBase||' >= faixa_desconto_irrf.vl_inicial
                                GROUP BY faixa_desconto_irrf.cod_tabela');
        IF boValorMaior = TRUE THEN
            --BUSCA VALOR DA BASE DE DEDUÇÃO DE IRRF DA FOLHA FÉRIAS QUE ESTÁ SENDO CALCULADA
            nuValorBaseDeducao := processarSomatorioDeducoesFerias(boComPensao);
            --SUBTRAÇÃO DO SOMATÓRIO VALOR DA BASE DO VALOR DA BASE DE DEDUÇÃO
            nuValorBase := nuValorBase - nuValorBaseDeducao;

            --BUSCA DA ALIQUOTA DE DESCONTO QUE SE ENQUADRA NO VALOR (nuValorBase) ENCONTRADO
            stSql := 'SELECT faixa_desconto_irrf.aliquota
                            , faixa_desconto_irrf.parcela_deduzir
                         FROM folhapagamento'||stEntidade||'.faixa_desconto_irrf
                            , folhapagamento'||stEntidade||'.tabela_irrf
                            , (  SELECT cod_tabela
                                      , max(timestamp) as timestamp
                                   FROM folhapagamento'||stEntidade||'.tabela_irrf
                                  WHERE tabela_irrf.vigencia = '''||dtVigencia||'''
                               GROUP BY cod_tabela) as max_tabela_irrf
                        WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                          AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                          AND tabela_irrf.cod_tabela = faixa_desconto_irrf.cod_tabela
                          AND tabela_irrf.timestamp  = faixa_desconto_irrf.timestamp
                          AND faixa_desconto_irrf.vl_inicial <= '||nuValorBase||'
                          AND faixa_desconto_irrf.vl_final   >= '||nuValorBase||' ';
            OPEN crCursor FOR EXECUTE stSql;
                FETCH crCursor INTO nuAliquotaDesconto,nuParcelaDeduzir;
            CLOSE crCursor;
            IF nuAliquotaDesconto IS NOT NULL THEN
                --VALOR ENCONTRADO BASEADO NA ALIQUOTA DE DESCONTO
                nuValorDescontoFF := nuValorBase * nuAliquotaDesconto / 100;

                --SUBTRAÇÃO DO VALOR ENCONTRATO (nuValorDescontoFF) DO CAMPO parcela_deduzir 
                nuValorDescontoFF := nuValorDescontoFF - nuParcelaDeduzir;
                --BUSCA SOMATÓRIO DO VALOR DOS DESCONTOS  DE IRRF DAS FOLHA COMPLEMENTARES, FOLHA SALÁRIO E FOLHA FÉRIAS DO CONTRATO QUE ESTÁ SENDO CALCULADO
                IF boComPensao = TRUE THEN
                    inCodTipo = 6;
                ELSE
                    inCodTipo = 3;
                END IF;
                --Busca código do Evento de Base IRRF Salário/Ferias/13o.Salário
                inCodEventoDescontoIRRF := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                                                FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                                               WHERE tabela_irrf_evento.cod_tipo = '||inCodTipo||'
                                                            ORDER BY timestamp desc LIMIT 1');                  
                
                IF stSituacaoComplementar = 'f' THEN                               
                    stSql := 'SELECT SUM(evento_complementar_calculado.valor) AS valor
                                  FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                                     , folhapagamento'||stEntidade||'.evento_complementar_calculado
                                     , pessoal'||stEntidade||'.servidor_contrato_servidor
                                     , pessoal'||stEntidade||'.servidor
                                 WHERE registro_evento_complementar.cod_evento    = evento_complementar_calculado.cod_evento
                                   AND registro_evento_complementar.timestamp     = evento_complementar_calculado.timestamp_registro
                                   AND registro_evento_complementar.cod_registro  = evento_complementar_calculado.cod_registro
                                   AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                   AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                                   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                   AND servidor.numcgm = '||inNumCgm||'
                                   AND evento_complementar_evento.cod_evento = '||inCodEventoDescontoIRRF||'
                                   AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
                    nuValorDescontoCs := selectIntoNumeric(stSql);                                                  
                END IF;
                
                stSql :=' SELECT SUM(evento_ferias_calculado.valor) AS valor
                             FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                                , folhapagamento'||stEntidade||'.evento_ferias_calculado
                                , pessoal'||stEntidade||'.servidor_contrato_servidor
                                , pessoal'||stEntidade||'.servidor
                            WHERE registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                              AND registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                              AND registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                              AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                              AND registro_evento_ferias.cod_contrato  = servidor_contrato_servidor.cod_contrato
                              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                              AND evento_ferias_calculado.cod_evento = '||inCodEventoDescontoIRRF||'
                              AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                              AND servidor.numcgm = '||inNumCgm||'
                              AND registro_evento_ferias.cod_contrato != '||inCodContrato;
                nuValorDescontoOFF := selectIntoNumeric(stSql);  

                --SUBTRAÇÃO DOS VALORES JÁ DESCONTADOS NAS FOLHAS COMPLEMENTARES
                IF nuValorDescontoCs IS NULL THEN
                    nuValorDescontoCs := 0;
                END IF;
                IF nuValorDescontoOFF IS NULL THEN
                    nuValorDescontoOFF := 0;
                END IF;
                nuValorDescontoFF := nuValorDescontoFF - (nuValorDescontoCs + nuValorDescontoOFF);
                --BUSCA COD_EVENTO, COD_REGISTRO E TIMESTAMP_REGISTRO DO EVENTO DE DESCONTO PARA ATUALIZAÇÃO
                stDadosRegistro         := buscaDadosRegistroEventoFeriasDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao);              
                arDadosRegistro         := string_to_array(stDadosRegistro,'#');
                inCodEvento             := arDadosRegistro[1];
                inCodRegistro           := arDadosRegistro[2];
                stTimestampRegistro     := arDadosRegistro[3];
                stDesdobramentoRegistro := arDadosRegistro[4];
                --ATUALIZA TABELA
                stSql := 'UPDATE folhapagamento'||stEntidade||'.evento_ferias_calculado SET valor      = '||nuValorDescontoFF||',
                                                                  quantidade = '||nuAliquotaDesconto||'
                 WHERE cod_evento         = '||inCodEvento||'
                   AND cod_registro       = '||inCodRegistro||'
                   AND timestamp_registro = '''||stTimestampRegistro||'''
                   AND desdobramento      = '''||stDesdobramentoRegistro||''' ';
                EXECUTE stSql;
            END IF;
        END IF;
    END IF; 

    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';

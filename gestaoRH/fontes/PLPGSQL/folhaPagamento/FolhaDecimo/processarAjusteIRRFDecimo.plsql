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
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.11
--*/

CREATE OR REPLACE FUNCTION processarAjusteIRRFDecimo(BOOLEAN) RETURNS BOOLEAN as '

DECLARE
    boComPensao                 ALIAS FOR $1;
    inCodPeriodoMovimentacao    INTEGER;
    inCodContrato               INTEGER;
    inCodEvento                 INTEGER;
    inCodRegistro               INTEGER;
    inCodTipo                   INTEGER;
    inNumCgm                    INTEGER;
    inCountFolhaDecimo          INTEGER;
    nuValorBaseFD               NUMERIC;
    nuValorBase                 NUMERIC;
    nuValorBaseDeducao          NUMERIC;
    nuAliquotaDesconto          NUMERIC;
    nuParcelaDeduzir            NUMERIC;
    nuValorDescontoOFD          NUMERIC;
    nuValorDescontoFD           NUMERIC;
    stSql                       VARCHAR := '''';
    dtVigencia                  VARCHAR := '''';
    stTimestampRegistro         VARCHAR := '''';
    stDadosRegistro             VARCHAR := '''';
    stDesdobramentoRegistro     VARCHAR := '''';
    arDadosRegistro             VARCHAR[];
    reRegistro                  RECORD;
    boRetorno                   BOOLEAN := TRUE;
    boValorMaior                BOOLEAN := TRUE;
    crCursor                    REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    inCodPeriodoMovimentacao    := recuperarBufferInteiro(''inCodPeriodoMovimentacao'');    
    inCodContrato               := recuperarBufferInteiro(''inCodContrato'');
    inNumCgm                    := recuperarBufferInteiro(''inNumCgm'');
    dtVigencia                  := recuperarBufferTexto(''dtVigenciaIrrf'');
    inCountFolhaDecimo := selectIntoInteger(''SELECT count(*) as contador
                                      FROM (SELECT registro_evento_decimo.cod_contrato
                                              FROM folhapagamento''||stEntidade||''.registro_evento_decimo
                                                 , folhapagamento''||stEntidade||''.ultimo_registro_evento_decimo
                                                 , folhapagamento''||stEntidade||''.evento_decimo_calculado
                                                 , folhapagamento''||stEntidade||''.periodo_movimentacao
                                                 , pessoal''||stEntidade||''.servidor_contrato_servidor
                                                 , pessoal''||stEntidade||''.servidor
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
                                               AND servidor.numcgm = ''||inNumCgm||''
                                               AND registro_evento_decimo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                                          GROUP BY registro_evento_decimo.cod_contrato) as decimo'');
    IF inCountFolhaDecimo > 1 THEN  
        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA DÉCIMO DO CONTRATO QUE ESTÁ SENDO CALCULADO
        nuValorBaseFD := selectIntoNumeric('' SELECT SUM(evento_decimo_calculado.valor) AS valor
                                     FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
                                        , folhapagamento''||stEntidade||''.tabela_irrf
                                        , (   SELECT cod_tabela
                                                   , max(timestamp) as timestamp
                                                FROM folhapagamento''||stEntidade||''.tabela_irrf
                                               WHERE tabela_irrf.vigencia = ''''''||dtVigencia||''''''
                                            GROUP BY cod_tabela) as max_tabela_irrf
                                        , folhapagamento''||stEntidade||''.evento
                                        , folhapagamento''||stEntidade||''.registro_evento_decimo
                                        , folhapagamento''||stEntidade||''.ultimo_registro_evento_decimo
                                        , folhapagamento''||stEntidade||''.evento_decimo_calculado
                                        , pessoal''||stEntidade||''.servidor_contrato_servidor
                                        , pessoal''||stEntidade||''.servidor
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
                                      AND registro_evento_decimo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                                      AND servidor.numcgm = ''||inNumCgm);

        --SOMA DO VALOR DA BASE DA FOLHA SALÁRIO COM O SOMATÓRIO DAS BASES DAS COMPLEMENTARES
        IF nuValorBaseFD IS NULL THEN
            nuValorBaseFD := 0;
        END IF;
        nuValorBase := nuValorBaseFD;

        --VERIFICAÇÃO SE O VALOR (nuValorBase) É MAIOR OU IGUAL A PRIMEIRA FAIXA DE DESCONTO DA TABELA DE IRRF
        boValorMaior := selectIntoBoolean(''SELECT TRUE as booleano
                                    FROM folhapagamento''||stEntidade||''.faixa_desconto_irrf
                                       , (  SELECT cod_tabela
                                                 , max(timestamp) as timestamp
                                              FROM folhapagamento''||stEntidade||''.tabela_irrf
                                             WHERE tabela_irrf.vigencia = ''''''||dtVigencia||''''''
                                          GROUP BY cod_tabela) as max_tabela_irrf
                                   WHERE faixa_desconto_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                     AND faixa_desconto_irrf.timestamp = max_tabela_irrf.timestamp
                                     AND ''||nuValorBase||'' >= faixa_desconto_irrf.vl_inicial
                                GROUP BY faixa_desconto_irrf.cod_tabela'');
        IF boValorMaior = TRUE THEN
            --BUSCA VALOR DA BASE DE DEDUÇÃO DE IRRF DA FOLHA DÉCIMO QUE ESTÁ SENDO CALCULADA
            nuValorBaseDeducao := processarSomatorioDeducoesDecimo(boComPensao);
            --SUBTRAÇÃO DO SOMATÓRIO VALOR DA BASE DO VALOR DA BASE DE DEDUÇÃO
            nuValorBase := nuValorBase - nuValorBaseDeducao;

            --BUSCA DA ALIQUOTA DE DESCONTO QUE SE ENQUADRA NO VALOR (nuValorBase) ENCONTRADO
            stSql := ''SELECT faixa_desconto_irrf.aliquota
                            , faixa_desconto_irrf.parcela_deduzir
                         FROM folhapagamento''||stEntidade||''.faixa_desconto_irrf
                            , folhapagamento''||stEntidade||''.tabela_irrf
                            , (  SELECT cod_tabela
                                      , max(timestamp) as timestamp
                                   FROM folhapagamento''||stEntidade||''.tabela_irrf
                                  WHERE tabela_irrf.vigencia = ''''''||dtVigencia||''''''
                               GROUP BY cod_tabela) as max_tabela_irrf
                        WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                          AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                          AND tabela_irrf.cod_tabela = faixa_desconto_irrf.cod_tabela
                          AND tabela_irrf.timestamp  = faixa_desconto_irrf.timestamp
                          AND faixa_desconto_irrf.vl_inicial <= ''||nuValorBase||''
                          AND faixa_desconto_irrf.vl_final   >= ''||nuValorBase||'' '';
            OPEN crCursor FOR EXECUTE stSql;
                FETCH crCursor INTO nuAliquotaDesconto,nuParcelaDeduzir;
            CLOSE crCursor;
            IF nuAliquotaDesconto IS NOT NULL THEN
                --VALOR ENCONTRADO BASEADO NA ALIQUOTA DE DESCONTO
                nuValorDescontoFD := nuValorBase * nuAliquotaDesconto / 100;

                --SUBTRAÇÃO DO VALOR ENCONTRATO (nuValorDescontoFD) DO CAMPO parcela_deduzir 
                nuValorDescontoFD := nuValorDescontoFD - nuParcelaDeduzir;
                --BUSCA SOMATÓRIO DO VALOR DOS DESCONTOS  DE IRRF DAS FOLHA COMPLEMENTARES, FOLHA SALÁRIO E FOLHA FÉRIAS DO CONTRATO QUE ESTÁ SENDO CALCULADO
                IF boComPensao = TRUE THEN
                    inCodTipo = 6;
                ELSE
                    inCodTipo = 3;
                END IF;

                nuValorDescontoOFD := selectIntoNumeric('' SELECT SUM(evento_decimo_calculado.valor) AS valor
                                                  FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
                                                     , folhapagamento''||stEntidade||''.tabela_irrf
                                                     , (   SELECT cod_tabela
                                                                , max(timestamp) as timestamp
                                                             FROM folhapagamento''||stEntidade||''.tabela_irrf
                                                            WHERE tabela_irrf.vigencia = ''''''||dtVigencia||''''''
                                                         GROUP BY cod_tabela) as max_tabela_irrf
                                                     , folhapagamento''||stEntidade||''.evento
                                                     , folhapagamento''||stEntidade||''.registro_evento_decimo
                                                     , folhapagamento''||stEntidade||''.ultimo_registro_evento_decimo
                                                     , folhapagamento''||stEntidade||''.evento_decimo_calculado
                                                     , pessoal''||stEntidade||''.servidor_contrato_servidor
                                                     , pessoal''||stEntidade||''.servidor
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
                                                   AND tabela_irrf_evento.cod_tipo = ''||inCodTipo||''
                                                   AND registro_evento_decimo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                                                   AND servidor.numcgm = ''||inNumCgm||''
                                                   AND registro_evento_decimo.cod_contrato != ''||inCodContrato);

                --SUBTRAÇÃO DOS VALORES JÁ DESCONTADOS NAS FOLHAS COMPLEMENTARES
                IF nuValorDescontoOFD IS NULL THEN
                    nuValorDescontoOFD := 0;
                END IF;
                nuValorDescontoFD := nuValorDescontoFD - nuValorDescontoOFD;
                --BUSCA COD_EVENTO, COD_REGISTRO E TIMESTAMP_REGISTRO DO EVENTO DE DESCONTO PARA ATUALIZAÇÃO
                stDadosRegistro         := buscaDadosRegistroEventoDecimoDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao);              
                arDadosRegistro         := string_to_array(stDadosRegistro,''#'');
                inCodEvento             := arDadosRegistro[1];
                inCodRegistro           := arDadosRegistro[2];
                stTimestampRegistro     := arDadosRegistro[3];
                stDesdobramentoRegistro := arDadosRegistro[4];
                --ATUALIZA TABELA
                stSql := ''UPDATE folhapagamento''||stEntidade||''.evento_decimo_calculado SET valor      = ''||nuValorDescontoFD||'',
                                                                  quantidade = ''||nuAliquotaDesconto||''
                 WHERE cod_evento         = ''||inCodEvento||''
                   AND cod_registro       = ''||inCodRegistro||''
                   AND timestamp_registro = ''''''||stTimestampRegistro||''''''
                   AND desdobramento      = ''''''||stDesdobramentoRegistro||'''''' '';
                EXECUTE stSql;
            END IF;
        END IF;
    END IF; 

    RETURN boRetorno; 
END;
'LANGUAGE 'plpgsql';

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
--    * Data de Criação: 10/04/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25598 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-09-21 17:14:37 -0300 (Sex, 21 Set 2007) $
--
--    * Casos de uso: uc-04.05.46
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION processarAjusteIRRF(BOOLEAN,INTEGER) RETURNS BOOLEAN as $$

DECLARE
    boComPensao                     ALIAS FOR $1;
    inCodContrato                   ALIAS FOR $2;
    inCodPeriodoMovimentacao        INTEGER;
    inContComplementar              INTEGER;
    inCodEvento                     INTEGER;
    inCodEventoBaseIRRF             INTEGER;
    inCodEventoDescontoIRRF         INTEGER;
    inCodRegistro                   INTEGER;
    inCodTipo                       INTEGER;
    inNumCgm                        INTEGER;
    inCountFolhaComplementar        INTEGER;
    inCountFolhaSalario             INTEGER;
    inCountFolhaRescisao            INTEGER;
    inCountDescExternoPrev          INTEGER;
    inCodTabelaIRRF                 INTEGER;
    nuValorBaseFP                   NUMERIC;
    nuValorBaseCs                   NUMERIC;
    nuValorBaseFR                   NUMERIC;
    nuValorBase                     NUMERIC:=0;
    nuValorBaseDeducaoFP            NUMERIC;
    nuValorBaseDeducaoCs            NUMERIC;
    nuValorBaseDeducao              NUMERIC;
    nuAliquotaDesconto              NUMERIC;
    nuValorDescontoFP               NUMERIC;
    nuParcelaDeduzir                NUMERIC;
    nuValorDescontoCs               NUMERIC;
    nuValorDescontoOFP              NUMERIC;
    nuValorDescontoFR               NUMERIC;
    nuSomaDescontoExterno           NUMERIC :=0;
    nuValorTemp                     NUMERIC;
    stSql                           VARCHAR := '';
    dtVigencia                      VARCHAR := '';
    stTimestampRegistro             VARCHAR := '';
    stDadosRegistro                 VARCHAR := '';
    stSituacaoComplementar          VARCHAR;
    arDadosRegistro                 VARCHAR[];
    reRegistro                      RECORD;
    reDescontoExterno               RECORD;     
    boRetorno                       BOOLEAN := TRUE;
    boValorMaior                    BOOLEAN := TRUE;
    crCursor                        REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');    
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
                                                     AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                                                     AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                                                     AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                                                     AND complementar_situacao.situacao = ''f''
                                                     AND servidor.numcgm = '||inNumCgm||'
                                                     AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                                     AND registro_evento_complementar.cod_configuracao = 1
                                                GROUP BY registro_evento_complementar.cod_contrato
                                                       , registro_evento_complementar.cod_complementar) as complementar');
    inCountFolhaSalario := selectIntoInteger('SELECT count(*) as contador
                                       FROM (SELECT registro_evento_periodo.cod_contrato
                                               FROM folhapagamento'||stEntidade||'.evento_calculado
                                                  , folhapagamento'||stEntidade||'.registro_evento
                                                  , folhapagamento'||stEntidade||'.registro_evento_periodo
                                                  , pessoal'||stEntidade||'.servidor_contrato_servidor
                                                  , pessoal'||stEntidade||'.servidor
                                              WHERE evento_calculado.cod_evento = registro_evento.cod_evento
                                                AND evento_calculado.cod_registro = registro_evento.cod_registro
                                                AND evento_calculado.timestamp_registro = registro_evento.timestamp
                                                AND registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                                AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                AND servidor.numcgm = '||inNumCgm||'
                                                AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                             GROUP BY registro_evento_periodo.cod_contrato) as salario');
    inCountFolhaRescisao := selectIntoInteger('SELECT count(*) as contador
                                      FROM (SELECT registro_evento_rescisao.cod_contrato
                                              FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                                                 , folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                                                 , folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                                 , pessoal'||stEntidade||'.servidor_contrato_servidor
                                                 , pessoal'||stEntidade||'.servidor
                                             WHERE registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro
                                               AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
                                               AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento
                                               AND registro_evento_rescisao.desdobramento    = ultimo_registro_evento_rescisao.desdobramento
                                               AND registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
                                               AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
                                               AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
                                               AND registro_evento_rescisao.desdobramento    = evento_rescisao_calculado.desdobramento
                                               AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                                               AND registro_evento_rescisao.desdobramento    = ''S''
                                               AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                               AND servidor.numcgm = '||inNumCgm||'                                               
                                               AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                          GROUP BY registro_evento_rescisao.cod_contrato) as rescisao'); 

	inCountDescExternoPrev  := selectIntoInteger('SELECT count(*) as contador
                                      FROM (SELECT desconto_externo_previdencia.vl_base_previdencia as base
                                                 , desconto_externo_previdencia_valor.valor_previdencia as desconto
                                              FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia                                    
                                              LEFT JOIN (SELECT desconto_externo_previdencia_valor.*
                                                           FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia_valor
                                                        , (SELECT cod_contrato
                                                                , max(timestamp_valor) as timestamp_valor
                                                             FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia_valor
                                                           GROUP BY cod_contrato) as max_desconto_externo_previdencia_valor
                                                          WHERE desconto_externo_previdencia_valor.cod_contrato = max_desconto_externo_previdencia_valor.cod_contrato
                                                            AND desconto_externo_previdencia_valor.timestamp_valor = max_desconto_externo_previdencia_valor.timestamp_valor) AS desconto_externo_previdencia_valor
                                                             ON desconto_externo_previdencia_valor.cod_contrato = desconto_externo_previdencia.cod_contrato
                                                            AND desconto_externo_previdencia_valor.timestamp = desconto_externo_previdencia_valor.timestamp      
                                                        , (  SELECT cod_contrato
                                                                  , max(timestamp) as timestamp
                                                               FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia
                                                              WHERE vigencia <= '||quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao))||'
                                                             GROUP BY cod_contrato) as max_desconto_externo_previdencia
                                             WHERE desconto_externo_previdencia.cod_contrato = max_desconto_externo_previdencia.cod_contrato
                                               AND desconto_externo_previdencia.timestamp = max_desconto_externo_previdencia.timestamp
                                               AND NOT EXISTS (SELECT 1
                                                                 FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia_anulado
                                                                WHERE desconto_externo_previdencia.cod_contrato = desconto_externo_previdencia_anulado.cod_contrato
                                                                  AND desconto_externo_previdencia.timestamp = desconto_externo_previdencia_anulado.timestamp)
                                               AND desconto_externo_previdencia.cod_contrato = '||inCodContrato||') as descontoexterno');
										   
    --Busca código do Evento de Base IRRF Salário/Ferias/13o.Salário
    inCodEventoBaseIRRF := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                                   FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                                  WHERE tabela_irrf_evento.cod_tipo = 7
                                               ORDER BY timestamp desc LIMIT 1');    
											   
	
    IF inCountFolhaSalario >= 1 AND NOT (inCountFolhaSalario = 1 AND inCountFolhaComplementar = 0 AND inCountFolhaRescisao = 0 ) THEN                  
        
        --BUSCA VALOR DA BASE DE IRRF DA FOLHA SALÁRIO QUE ESTÁ SENDO CALCULADA
        --O valor de base do contrato que está sendo calculado será pego em uma consulta mais a baixo        
        stSql := 'SELECT SUM(evento_calculado.valor) AS valor
                     FROM folhapagamento'||stEntidade||'.evento_calculado
                        , folhapagamento'||stEntidade||'.registro_evento_periodo
                        , pessoal'||stEntidade||'.servidor_contrato_servidor
                        , pessoal'||stEntidade||'.servidor
                    WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                      AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                      AND servidor.numcgm = '||inNumCgm||'
                      AND registro_evento_periodo.cod_contrato != '||inCodContrato||'
                      AND evento_calculado.cod_evento = '||inCodEventoBaseIRRF||'
                      AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
        nuValorBaseFP := selectIntoNumeric(stSql);

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
                    AND complementar_situacao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                    ORDER BY complementar_situacao.timestamp desc LIMIT 1';
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

        --BUSCA SOMATÓRIO DO VALOR DAS BASES DE IRRF DAS FOLHA RESCISÃO DO CONTRATO QUE ESTÁ SENDO CALCULADO
        stSql := ' SELECT SUM(evento_rescisao_calculado.valor) AS valor
                     FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                        , folhapagamento'||stEntidade||'.evento_rescisao_calculado
                        , pessoal'||stEntidade||'.servidor_contrato_servidor
                        , pessoal'||stEntidade||'.servidor
                    WHERE registro_evento_rescisao.cod_evento    = evento_rescisao_calculado.cod_evento
                      AND registro_evento_rescisao.timestamp     = evento_rescisao_calculado.timestamp_registro
                      AND registro_evento_rescisao.cod_registro  = evento_rescisao_calculado.cod_registro
                      AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                      AND registro_evento_rescisao.desdobramento = ''S''
                      AND registro_evento_rescisao.cod_contrato  = servidor_contrato_servidor.cod_contrato
                      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                      AND evento_rescisao_calculado.cod_evento = '||inCodEventoBaseIRRF||'
                      AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                      AND servidor.numcgm = '||inNumCgm;
        nuValorBaseFR := selectIntoNumeric(stSql);                                      


        --SOMA DO VALOR DA BASE DA FOLHA SALÁRIO COM O SOMATÓRIO DAS BASES DAS COMPLEMENTARES
        IF nuValorBaseFP IS NULL THEN
            nuValorBaseFP := 0;
        END IF;
        IF nuValorBaseCs IS NULL THEN
            nuValorBaseCs := 0;
        END IF;
        IF nuValorBaseFR IS NULL THEN
            nuValorBaseFR := 0;
        END IF;        
        nuValorBase := nuValorBaseFP + nuValorBaseCs + nuValorBaseFR;
    END IF; 
    ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------
    stSql := ' SELECT registro_evento_periodo.cod_contrato
                 FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                    , folhapagamento'||stEntidade||'.evento_calculado
                    , pessoal'||stEntidade||'.servidor_contrato_servidor
                    , pessoal'||stEntidade||'.servidor
                WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                  AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                  AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                  AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                  AND numcgm = '||inNumCgm||'
             GROUP BY registro_evento_periodo.cod_contrato';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP        
        stSql := '     SELECT desconto_externo_irrf.vl_base_irrf as base
                            , desconto_externo_irrf_valor.valor_irrf as desconto
                         FROM folhapagamento'||stEntidade||'.desconto_externo_irrf                                    
                    LEFT JOIN (SELECT desconto_externo_irrf_valor.*
                                 FROM folhapagamento'||stEntidade||'.desconto_externo_irrf_valor
                                    , (   SELECT cod_contrato
                                               , max(timestamp_valor) as timestamp_valor
                                            FROM folhapagamento'||stEntidade||'.desconto_externo_irrf_valor
                                        GROUP BY cod_contrato) as max_desconto_externo_irrf_valor
                                WHERE desconto_externo_irrf_valor.cod_contrato = max_desconto_externo_irrf_valor.cod_contrato
                                  AND desconto_externo_irrf_valor.timestamp_valor = max_desconto_externo_irrf_valor.timestamp_valor) AS desconto_externo_irrf_valor
                           ON desconto_externo_irrf_valor.cod_contrato = desconto_externo_irrf.cod_contrato
                          AND desconto_externo_irrf_valor.timestamp = desconto_externo_irrf_valor.timestamp      
                            , (  SELECT cod_contrato
                                      , max(timestamp) as timestamp
                                   FROM folhapagamento'||stEntidade||'.desconto_externo_irrf
                                  WHERE vigencia <= '||quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao))||'
                               GROUP BY cod_contrato) as max_desconto_externo_irrf
                        WHERE desconto_externo_irrf.cod_contrato = max_desconto_externo_irrf.cod_contrato
                          AND desconto_externo_irrf.timestamp = max_desconto_externo_irrf.timestamp
                          AND NOT EXISTS (SELECT 1
                                            FROM folhapagamento'||stEntidade||'.desconto_externo_irrf_anulado
                                           WHERE desconto_externo_irrf.cod_contrato = desconto_externo_irrf_anulado.cod_contrato
                                             AND desconto_externo_irrf.timestamp = desconto_externo_irrf_anulado.timestamp)
                          AND desconto_externo_irrf.cod_contrato = '||reRegistro.cod_contrato;
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
    ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------    
    
    --No caso de existir apenas valor de base gerado atraves do desconto externo
    --é necessário incluir o valor de base do contrato que está sendo calculado    
    IF nuValorBase > 0 OR inCountDescExternoPrev > 0 THEN
        stSql := 'SELECT evento_calculado.valor
          FROM folhapagamento'||stEntidade||'.evento_calculado
             , folhapagamento'||stEntidade||'.registro_evento_periodo
         WHERE evento_calculado.cod_registro  = registro_evento_periodo.cod_registro
           AND evento_calculado.cod_evento = '||inCodEventoBaseIRRF||'
           AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
           AND cod_contrato = '||inCodContrato;
        nuValorTemp := selectIntoNumeric(stSql);
        IF nuValorTemp is not null THEN
            nuValorBase := nuValorBase + nuValorTemp;
        END IF;
    END IF;

    --VERIFICAÇÃO SE O VALOR (nuValorBase) É MAIOR OU IGUAL A PRIMEIRA FAIXA DE DESCONTO DA TABELA DE IRRF
    stSql := 'SELECT TRUE as booleano
                   FROM folhapagamento'||stEntidade||'.faixa_desconto_irrf
                      , (  SELECT cod_tabela
                                , max(timestamp) as timestamp
                             FROM folhapagamento'||stEntidade||'.tabela_irrf
                            WHERE tabela_irrf.vigencia = '||quote_literal(dtVigencia)||'
                         GROUP BY cod_tabela) as max_tabela_irrf
                  WHERE faixa_desconto_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                    AND faixa_desconto_irrf.timestamp = max_tabela_irrf.timestamp
                    AND '||nuValorBase||' >= faixa_desconto_irrf.vl_inicial
               GROUP BY faixa_desconto_irrf.cod_tabela';
    boValorMaior := selectIntoBoolean(stSql);


    IF boValorMaior = TRUE THEN
        --BUSCA VALOR DA BASE DE DEDUÇÃO DE IRRF DA FOLHA SALÁRIO QUE ESTÁ SENDO CALCULADA
        nuValorBaseDeducao := processarSomatorioDeducoes(boComPensao);
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
                                WHERE tabela_irrf.vigencia = '||quote_literal(dtVigencia)||'
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
            nuValorDescontoFP := nuValorBase * nuAliquotaDesconto / 100;
    
            --SUBTRAÇÃO DO VALOR ENCONTRATO (nuValorDescontoFP) DO CAMPO parcela_deduzir 
            nuValorDescontoFP := nuValorDescontoFP - nuParcelaDeduzir;
    
            --BUSCA SOMATÓRIO DO VALOR DOS DESCONTOS  DE IRRF DAS FOLHA COMPLEMENTARES DO CONTRATO QUE ESTÁ SENDO CALCULADO
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
            stSql := 'SELECT SUM(evento_calculado.valor) AS valor
                        FROM folhapagamento'||stEntidade||'.evento_calculado
                           , folhapagamento'||stEntidade||'.registro_evento_periodo
                           , pessoal'||stEntidade||'.servidor_contrato_servidor
                           , pessoal'||stEntidade||'.servidor
                       WHERE registro_evento_periodo.cod_registro  = evento_calculado.cod_registro
                         AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                         AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                         AND servidor.numcgm = '||inNumCgm||'
                         AND evento_calculado.cod_evento = '||inCodEventoDescontoIRRF||'
                         AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                         AND registro_evento_periodo.cod_contrato != '||inCodContrato;            
            nuValorDescontoOFP := selectIntoNumeric(stSql);
    
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
                             AND evento_complementar_calculado.cod_evento = '||inCodEventoDescontoIRRF||'
                             AND registro_evento_complementar.cod_configuracao = 1
                             AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
                nuValorDescontoCs := selectIntoNumeric(stSql);
            END IF;
    
            stSql := 'SELECT SUM(evento_rescisao_calculado.valor) AS valor
                        FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                           , folhapagamento'||stEntidade||'.evento_rescisao_calculado
                           , pessoal'||stEntidade||'.servidor_contrato_servidor
                           , pessoal'||stEntidade||'.servidor
                       WHERE registro_evento_rescisao.cod_evento    = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.timestamp     = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_registro  = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.cod_contrato  = servidor_contrato_servidor.cod_contrato
                         AND registro_evento_rescisao.desdobramento = ''S''
                         AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                         AND evento_rescisao_calculado.cod_evento = '||inCodEventoDescontoIRRF||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                         AND servidor.numcgm = '||inNumCgm;
            nuValorDescontoFR := selectIntoNumeric(stSql);                                                   
                                                
            --SUBTRAÇÃO DOS VALORES JÁ DESCONTADOS NAS FOLHAS COMPLEMENTARES
            IF nuValorDescontoCs IS NULL THEN
                nuValorDescontoCs := 0;
            END IF;
            IF nuValorDescontoOFP IS NULL THEN
                nuValorDescontoOFP := 0;
            END IF;
            IF nuValorDescontoFR IS NULL THEN
                nuValorDescontoFR := 0;
            END IF;


            nuValorDescontoFP := nuValorDescontoFP - (nuValorDescontoCs + nuValorDescontoFR + nuValorDescontoOFP + nuSomaDescontoExterno);
            --BUSCA COD_EVENTO, COD_REGISTRO E TIMESTAMP_REGISTRO DO EVENTO DE DESCONTO PARA ATUALIZAÇÃO
            stDadosRegistro     := buscaDadosRegistroEventoDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao);               
            arDadosRegistro     := string_to_array(stDadosRegistro,'#');
            inCodEvento         := arDadosRegistro[1];
            inCodRegistro       := arDadosRegistro[2];
            stTimestampRegistro := arDadosRegistro[3];
            --ATUALIZA TABELA
            stSql := 'UPDATE folhapagamento'||stEntidade||'.evento_calculado SET valor = '||nuValorDescontoFP||',
                                                        quantidade = '||nuAliquotaDesconto||'
                WHERE cod_evento       = '||inCodEvento||'
                AND cod_registro       = '||inCodRegistro||'
                AND timestamp_registro = '||quote_literal(stTimestampRegistro)||'';
            EXECUTE stSql;
        END IF;
    END IF;

    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';

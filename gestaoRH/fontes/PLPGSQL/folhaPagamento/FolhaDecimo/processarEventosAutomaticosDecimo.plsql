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
-- /**
--     * Função PLSQL
--     * Data de Criação: 12/09/2006
--
--
--     * @author Diego Lemos de Souza
--
--     * Casos de uso: uc-04.05.11
--
--     $Id: processarEventosAutomaticosDecimo.plsql 59612 2014-09-02 12:00:51Z gelson $
-- */

CREATE OR REPLACE FUNCTION processarEventosAutomaticosDecimo() RETURNS BOOLEAN as $$
DECLARE
    inCodContrato                       INTEGER;
    inCodPeriodoMovimentacao            INTEGER;
    inContadorDependentes               INTEGER;
    inDiasServidor                      INTEGER;
    inQtdDependentesPensaoAlimenticia   INTEGER;
    inCodEvento                         INTEGER;
    inCodServidor                       INTEGER;
    boRetorno                           BOOLEAN;
    dtVigencia                          VARCHAR :='';
    stSql                               VARCHAR :='';
    stDataFinalCompetencia              VARCHAR :='';
    stAnoAdiantamento                   VARCHAR :='';
    stDesdobramento                     CHAR;
    stDesdobramentoConcessao            CHAR;
    reRegistro                          RECORD;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    stDesdobramento            := recuperarBufferTexto('stDesdobramento');
    stDataFinalCompetencia     := substr(recuperarBufferTexto('stDataFinalCompetencia'),1,10);
    stSql := 'SELECT ultimo_registro_evento_decimo.*
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                    , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                    , folhapagamento'|| stEntidade ||'.evento
                WHERE registro_evento_decimo.cod_registro = ultimo_registro_evento_decimo.cod_registro
                  AND registro_evento_decimo.cod_evento   = ultimo_registro_evento_decimo.cod_evento
                  AND registro_evento_decimo.timestamp    = ultimo_registro_evento_decimo.timestamp
                  AND registro_evento_decimo.desdobramento= ultimo_registro_evento_decimo.desdobramento
                  AND registro_evento_decimo.cod_evento = evento.cod_evento
                  AND evento.evento_sistema = true
                  AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                  AND registro_evento_decimo.cod_contrato = '|| inCodContrato ||'
                  AND registro_evento_decimo.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado_dependente
                   WHERE cod_registro = '|| reRegistro.cod_registro ||'
                     AND cod_evento = '|| reRegistro.cod_evento ||'
                     AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                     AND timestamp_registro = '|| quote_literal(reRegistro.timestamp) ||' ';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                   WHERE cod_registro = '|| reRegistro.cod_registro ||'
                     AND cod_evento = '|| reRegistro.cod_evento ||'
                     AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                     AND timestamp_registro = '|| quote_literal(reRegistro.timestamp) ||' ';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.log_erro_calculo_decimo
                   WHERE cod_registro = '|| reRegistro.cod_registro ||'
                     AND cod_evento = '|| reRegistro.cod_evento ||'
                     AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                     AND timestamp = '|| quote_literal(reRegistro.timestamp) ||' ';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo_parcela
                   WHERE cod_registro = '|| reRegistro.cod_registro ||'
                     AND cod_evento = '|| reRegistro.cod_evento ||'
                     AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                     AND timestamp = '|| quote_literal(reRegistro.timestamp) ||' ';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                   WHERE cod_registro = '|| reRegistro.cod_registro ||'
                     AND cod_evento = '|| reRegistro.cod_evento ||'
                     AND desdobramento = '|| quote_literal(reRegistro.desdobramento) ||'
                     AND timestamp = '|| quote_literal(reRegistro.timestamp) ||' ';
        EXECUTE stSql;
    END LOOP;

    IF stDesdobramento != 'A' THEN
        dtVigencia := selectIntoVarchar(' SELECT vigencia
                                  FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                     , (SELECT cod_tabela
                                             , max(timestamp) as timestamp
                                          FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                         WHERE vigencia <= '|| quote_literal(stDataFinalCompetencia) ||'
                                         GROUP BY cod_tabela) as max_tabela_irrf
                                 WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                   AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp');
        dtVigencia := criarBufferTexto('dtVigenciaIrrf',dtVigencia);

        --Sql que verifica se o servidor possui dependentes
        inContadorDependentes := selectIntoInteger(' SELECT count(servidor.cod_servidor) as contador
                                             FROM pessoal'|| stEntidade ||'.servidor
                                                , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                , pessoal'|| stEntidade ||'.servidor_dependente
                                            WHERE servidor_contrato_servidor.cod_contrato = (SELECT recuperaContratoServidorPensionista('|| inCodContrato ||'))
                                              AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                              AND servidor.cod_servidor                   = servidor_dependente.cod_servidor');
        --Se possuir dependentes faz a inclusão de registro de evento
        IF inContadorDependentes > 0 THEN
            boRetorno := inserirEventosAutomaticosDecimo(1);
        END IF;

        --Sql que verifica se o servidor se enquadra como inativo ou pensionista acima de 65 anos
        IF retornaDataAniversarioCom(65,inCodContrato) <= to_date(stDataFinalCompetencia,'yyyy/mm/dd') THEN
            boRetorno := inserirEventosAutomaticosDecimo(2);
        END IF;

        --Inclusão dos registro de eventos referentes aos tipos 3,4,5,6 e 7
        boRetorno := inserirEventosAutomaticosDecimo(3);
        boRetorno := inserirEventosAutomaticosDecimo(4);
        boRetorno := inserirEventosAutomaticosDecimo(5);
        boRetorno := inserirEventosAutomaticosDecimo(6);
        boRetorno := inserirEventosAutomaticosDecimo(7);
    ELSE
        dtVigencia := criarBufferTexto('dtVigenciaIrrf','NULL');
    END IF;

    ----Código para inclusão de registro de eventos FGTS
    dtVigencia := selectIntoVarchar(' SELECT MAX(vigencia)
                              FROM folhapagamento'|| stEntidade ||'.fgts
                             WHERE vigencia <= '|| quote_literal(stDataFinalCompetencia) ||' ');
    stSql := 'SELECT cod_evento
                    , cod_tipo
                    , fgts_categoria.aliquota_deposito
                    , fgts_categoria.aliquota_contribuicao
                 FROM pessoal'|| stEntidade ||'.contrato_servidor
                    , folhapagamento'|| stEntidade ||'.fgts_categoria
                    , folhapagamento'|| stEntidade ||'.fgts
                    , (  SELECT max(timestamp) as timestamp
                              , cod_fgts
                           FROM folhapagamento'|| stEntidade ||'.fgts
                          WHERE fgts.vigencia <= '|| quote_literal(dtVigencia) ||'
                       GROUP BY cod_fgts) as max_fgts
                    , folhapagamento'|| stEntidade ||'.fgts_evento
                WHERE contrato_servidor.cod_categoria = fgts_categoria.cod_categoria
                  AND fgts_categoria.cod_fgts = fgts.cod_fgts
                  AND fgts_categoria.timestamp= fgts.timestamp
                  AND fgts.cod_fgts = fgts_evento.cod_fgts
                  AND fgts.timestamp= fgts_evento.timestamp
                  AND fgts.cod_fgts = max_fgts.cod_fgts
                  AND fgts.timestamp= max_fgts.timestamp
                  AND contrato_servidor.cod_contrato = '|| inCodContrato;
    FOR reRegistro IN  EXECUTE stSql
    LOOP

        IF (reRegistro.aliquota_deposito     > 0 AND reRegistro.cod_tipo = 1) or
           (reRegistro.aliquota_contribuicao > 0 AND reRegistro.cod_tipo = 2) or
           (reRegistro.cod_tipo = 3                                         ) THEN
            boRetorno := insertRegistroEventoAutomaticoDecimo(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,stDesdobramento);
        END IF;
    END LOOP;


    --Código para inclusão de registro de eventos Previdência
    IF stDesdobramento != 'A' THEN
        stSql := '    SELECT MAX(vigencia)
                    FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
              INNER JOIN (
                        SELECT contrato_servidor_previdencia.cod_contrato
                             , contrato_servidor_previdencia.cod_previdencia
                          FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                         WHERE contrato_servidor_previdencia.timestamp = ( SELECT timestamp
                                                                             FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia as contrato_servidor_previdencia_interna
                                                                            WHERE contrato_servidor_previdencia_interna.cod_contrato = contrato_servidor_previdencia.cod_contrato
                                                                              AND contrato_servidor_previdencia_interna.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                                                              AND contrato_servidor_previdencia.bo_excluido = false
                                                                         ORDER BY timestamp desc
                                                                            LIMIT 1
                                                                         )

                        UNION
                        SELECT contrato_pensionista_previdencia.cod_contrato
                             , contrato_pensionista_previdencia.cod_previdencia
                          FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                         WHERE contrato_pensionista_previdencia.timestamp = ( SELECT timestamp
                                                                                FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia as contrato_pensionista_previdencia_interna
                                                                               WHERE contrato_pensionista_previdencia_interna.cod_contrato = contrato_pensionista_previdencia.cod_contrato
                                                                            ORDER BY timestamp desc
                                                                               LIMIT 1
                                                                             )
                        ) as previdencia_contrato on folhapagamento'|| stEntidade ||'.previdencia_previdencia.cod_previdencia = previdencia_contrato.cod_previdencia
              WHERE previdencia_contrato.cod_contrato = '|| inCodContrato ||'
                AND vigencia <= '|| quote_literal(stDataFinalCompetencia) ||' ';
        dtVigencia := selectIntoVarchar(stSql);
        --VERIFICAÇÃO DE EXISTENCIA DE PREVIDENCIAS CADASTRADAS NO CONTRATO DO SERVIDOR
        IF dtVigencia IS NOT NULL THEN
            dtVigencia := criarBufferTexto('dtVigenciaPrevidencia',dtVigencia);

            stSql := 'SELECT cod_evento
                        , cod_tipo
                     FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                        , (  SELECT max(timestamp) as timestamp
                                  , cod_previdencia
                               FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                              WHERE previdencia_previdencia.vigencia = '|| quote_literal(dtVigencia) ||'
                           GROUP BY cod_previdencia) as max_previdencia_previdencia
                        , folhapagamento'|| stEntidade ||'.previdencia_evento
                        , (SELECT contrato_servidor_previdencia.cod_contrato
                                , contrato_servidor_previdencia.cod_previdencia
                             FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                , (  SELECT max(timestamp) as timestamp
                                         , cod_contrato
                                      FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                  GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                            WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                              AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                              AND contrato_servidor_previdencia.bo_excluido = false
                            UNION
                           SELECT contrato_pensionista_previdencia.cod_contrato
                                , contrato_pensionista_previdencia.cod_previdencia
                             FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                , (  SELECT max(timestamp) as timestamp
                                         , cod_contrato
                                      FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                  GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                            WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                              AND contrato_pensionista_previdencia.timestamp    = max_contrato_pensionista_previdencia.timestamp) as servidor_pensionista_previdencia
                    WHERE servidor_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                      AND previdencia_previdencia.cod_previdencia    = max_previdencia_previdencia.cod_previdencia
                      AND previdencia_previdencia.timestamp          = max_previdencia_previdencia.timestamp
                      AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                      AND previdencia_previdencia.timestamp       = previdencia_evento.timestamp
                      AND previdencia_evento.cod_tipo != 3
                      AND servidor_pensionista_previdencia.cod_contrato = '|| inCodContrato ||' ';
            FOR reRegistro IN  EXECUTE stSql
            LOOP
                boRetorno := insertRegistroEventoAutomaticoDecimo(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,stDesdobramento);
            END LOOP;
        ELSE
            dtVigencia := criarBufferTexto('dtVigenciaPrevidencia','NULL');
        END IF;
    ELSE
        dtVigencia := criarBufferTexto('dtVigenciaPrevidencia','NULL');
    END IF;

    --   INÍCIO PENSÃO     --
    --Código para inclusão de registro de eventos Pensão
    inQtdDependentesPensaoAlimenticia = pega0QtdDependentesPensaoAlimenticia( inCodContrato, stDataFinalCompetencia );
    inQtdDependentesPensaoAlimenticia = criarBufferInteiro('inQtdDependentesPensaoAlimenticia',inQtdDependentesPensaoAlimenticia);
    IF inQtdDependentesPensaoAlimenticia > 0 THEN
        --Verifica se existe alguma pensao judicial com incidência em décimo terceiro salário
        --cod_incidencia igual a 1
        inCodServidor := recuperarBufferInteiro('inCodServidor');
        stSql := 'SELECT count(pensao.cod_pensao) as contador
                    FROM pessoal'|| stEntidade ||'.pensao
                       , (  SELECT cod_pensao
                                 , max(timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.pensao
                          GROUP BY cod_pensao) as max_pensao
                       , pessoal'|| stEntidade ||'.pensao_incidencia
                   WHERE pensao.cod_pensao NOT IN (SELECT cod_pensao
                                                     FROM pessoal'|| stEntidade ||'.pensao_excluida)
                     AND pensao.cod_pensao = max_pensao.cod_pensao
                     AND pensao.timestamp  = max_pensao.timestamp
                     AND pensao.cod_pensao = pensao_incidencia.cod_pensao
                     AND pensao.timestamp = pensao_incidencia.timestamp
                     AND pensao.cod_servidor = '|| inCodServidor ||'
                     AND cod_incidencia = 1';
        IF selectIntoInteger(stSql) > 0 THEN
            inCodEvento := pega2CodEventoDescontoPensaoAlimenticia();
            boRetorno := insertRegistroEventoAutomaticoDecimo(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,stDesdobramento);
        END IF;
    END IF;
    --   FIM PENSÃO        --

    -- VERIFICAÇÃO PARA EFETUAR OU NÃO O REGISTRO DE EVENTO DE DESCONTO DE ADIANTAMENTO DE DÉCIMO
	-- COMENTADO O IF: INSERE O EVENTO DE DESC DE ADIANT PARA TODOS, BASTA SER DESDOBRAMENTO "D".AVALIAÇÃO DIRETO PELO GERADOR
    stSql := 'SELECT desdobramento
                FROM folhapagamento'|| stEntidade ||'.concessao_decimo
               WHERE cod_contrato = '|| inCodContrato ||'
                 AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
    stDesdobramentoConcessao := selectIntoVarchar(stSql);
    IF (stDesdobramentoConcessao = 'D') THEN
        --stAnoAdiantamento := SUBSTR(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao),1,4);
        --boRetorno := verificaAdiantamento(stAnoAdiantamento);
        --IF boRetorno is TRUE THEN
            boRetorno := inserirEventoAutomaticoDescontoAdiantamento(1,stDesdobramentoConcessao);
        --END IF;
    END IF;

    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';


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
CREATE OR REPLACE FUNCTION comprovanteRendimentosIRRF(VARCHAR,VARCHAR,VARCHAR,BOOLEAN,VARCHAR,VARCHAR) RETURNS SETOF linhaComprovanteRendimentosIRRF AS $$
DECLARE
    stTipoFiltro                        ALIAS FOR $1;
    stValoresFiltro                     ALIAS FOR $2;
    stSituacaoCadastro                  ALIAS FOR $3;
    boAgrupar                           ALIAS FOR $4;
    stEntidade                          ALIAS FOR $5;
    stExercicio                         ALIAS FOR $6;

    rwComprovanteRendimentosIRRF        linhaComprovanteRendimentosIRRF%ROWTYPE;
    stSql                               VARCHAR;
    stEventoCalculadoSalario            VARCHAR;
    stEventoCalculadoSalario1           VARCHAR;
    stEventoCalculadoSalario2           VARCHAR;
    stEventoCalculadoComplementar       VARCHAR;
    stEventoCalculadoComplementar1      VARCHAR;
    stEventoCalculadoComplementar2      VARCHAR;
    stEventoCalculadoFerias             VARCHAR;
    stEventoCalculadoFerias1            VARCHAR;
    stEventoCalculadoFerias2            VARCHAR;
    stEventoCalculadoRescisao           VARCHAR;
    stEventoCalculadoRescisao1          VARCHAR;
    stEventoCalculadoRescisao2          VARCHAR;
    stEventoCalculadoDecimo             VARCHAR;
    stEventoCalculadoDecimo1            VARCHAR;
    stEventoCalculadoDecimo2            VARCHAR;
    stIRRFCID                           VARCHAR:='';
    stCodPeriodoMovimentacao            VARCHAR:='';
    stCodPeriodoMovimentacaoLaudo       VARCHAR:='';
    stCodEventoCompRendimento           VARCHAR;
    stTimestampFechamentoPeriodo        VARCHAR;
    inCodPeriodoMovimentacao            INTEGER:=0;
    inCodEventoBaseIRRF                 INTEGER;
    inCodEventoPrevOficial              INTEGER;
    inCodEventoPrevPrivada              INTEGER;
    inCodEventoPensaoAlimenticia        INTEGER;
    inCodEventoDescIRRF                 INTEGER;
    inCodEventoDescIRRFPensao           INTEGER;
    inCodEvento65Anos                   INTEGER;
    inCodEventoBaseDeducaoPensao        INTEGER;
    inCodEventoMolestia                 INTEGER;
    dtFinal                             DATE;
    reRegistro                          RECORD;
    reRegistro2                         RECORD;
    reTabelaIRRF                        RECORD;
    rePrevidenciaPrivada                RECORD;
    nuValor                             NUMERIC;
    nuSoma                              NUMERIC:=0;
    nuTotalRendimentos                  NUMERIC;
    nuMolestiaAcidente                  NUMERIC;
    nuPrevidenciaOficial                NUMERIC;
    nuPrevidenciaPrivada                NUMERIC;
    nuPensaoAlimenticia                 NUMERIC;
    nuIRRFRetido                        NUMERIC;
    nuInfAposentadoria                  NUMERIC;
    nuDiariasAjudaCusto                 NUMERIC;
    nuDecimoTerceiro                    NUMERIC;

    crCursor                            REFCURSOR;
BEGIN

    --Busca os cod_periodo_movimentação do ano inteiro
    stSql := '  SELECT *
                  FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                 WHERE to_char(periodo_movimentacao.dt_final,''yyyy'') = '||quote_literal(stExercicio)||'
              ORDER BY cod_periodo_movimentacao';

    FOR reRegistro IN EXECUTE stSql LOOP
        stCodPeriodoMovimentacao := stCodPeriodoMovimentacao || reRegistro.cod_periodo_movimentacao||',';
        dtFinal                  := reRegistro.dt_final;
        inCodPeriodoMovimentacao := reRegistro.cod_periodo_movimentacao;
    END LOOP;

    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);

    stCodPeriodoMovimentacao := substr(stCodPeriodoMovimentacao,0,length(stCodPeriodoMovimentacao));

    --Evento de pensão
    stSql := '    SELECT pensao_evento.cod_evento
                    FROM folhapagamento'||stEntidade||'.pensao_evento
              INNER JOIN (  SELECT cod_configuracao_pensao
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'||stEntidade||'.pensao_evento
                             WHERE timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                          GROUP BY cod_configuracao_pensao) as max_pensao_evento
                      ON pensao_evento.cod_configuracao_pensao = max_pensao_evento.cod_configuracao_pensao
                     AND pensao_evento.timestamp = max_pensao_evento.timestamp
                   WHERE pensao_evento.cod_tipo = 1';
    inCodEventoPensaoAlimenticia := selectIntoInteger(stSql);

    --Tabela IRRF
    stSql := '     SELECT cod_tabela
                        , max(timestamp) as timestamp
                     FROM folhapagamento'||stEntidade||'.tabela_irrf
                    WHERE timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                      AND vigencia <= '||quote_literal(dtFinal)||'
                    GROUP BY cod_tabela';

    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reTabelaIRRF;
    CLOSE crCursor;



    --Tabela Comprovante rendimentos
    IF reTabelaIRRF.cod_tabela IS NOT NULL THEN
        stSql := 'SELECT tabela_irrf_comprovante_rendimento.cod_evento
                    FROM folhapagamento'||stEntidade||'.tabela_irrf_comprovante_rendimento
                   WHERE tabela_irrf_comprovante_rendimento.cod_tabela = '||reTabelaIRRF.cod_tabela||'
                     AND tabela_irrf_comprovante_rendimento.timestamp = '||quote_literal(reTabelaIRRF.timestamp)||' ';
        stCodEventoCompRendimento := '';
        FOR reRegistro IN EXECUTE stSql LOOP
            stCodEventoCompRendimento := stCodEventoCompRendimento || reRegistro.cod_evento || ',';
        END LOOP;
        stCodEventoCompRendimento := substr(stCodEventoCompRendimento,0,length(stCodEventoCompRendimento));
    END IF;

    --Tabela IRRF CID
    IF reTabelaIRRF.cod_tabela IS NOT NULL THEN
        stSql := 'SELECT tabela_irrf_cid.cod_cid
                    FROM folhapagamento'||stEntidade||'.tabela_irrf_cid
                   WHERE tabela_irrf_cid.cod_tabela = '||reTabelaIRRF.cod_tabela||'
                     AND tabela_irrf_cid.timestamp = '||quote_literal(reTabelaIRRF.timestamp)||' ';
        stIRRFCID := '';
        FOR reRegistro IN EXECUTE stSql LOOP
            stIRRFCID := stIRRFCID || reRegistro.cod_cid || ',';
        END LOOP;
        stIRRFCID := substr(stIRRFCID,0,length(stIRRFCID));
    END IF;

    stSql := 'SELECT configuracao_dirf.cod_evento_molestia
                FROM ima.configuracao_dirf
               WHERE configuracao_dirf.exercicio = '||quote_literal(stExercicio)||' ';
    inCodEventoMolestia := selectIntoInteger(stSql);

    IF reTabelaIRRF.cod_tabela IS NOT NULL THEN
        stSql := 'SELECT * FROM folhapagamento'||stEntidade||'.tipo_evento_irrf';
        FOR reRegistro IN EXECUTE stSql LOOP
            stSql := 'SELECT tabela_irrf_evento.cod_evento
                        FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                       WHERE tabela_irrf_evento.cod_tabela = '||reTabelaIRRF.cod_tabela||'
                         AND tabela_irrf_evento.timestamp = '||quote_literal(reTabelaIRRF.timestamp)||'
                         AND tabela_irrf_evento.cod_tipo = '||reRegistro.cod_tipo;

            --Evento Informativo de isenção (inativos/pensionistas) acima de 65 anos
            IF reRegistro.cod_tipo = 2 THEN
                inCodEvento65Anos := selectIntoInteger(stSql);
            END IF;
            --Evento de Desconto IRRF para Salário/Férias/13o.Salário
            IF reRegistro.cod_tipo = 3 THEN
                inCodEventoDescIRRF := selectIntoInteger(stSql);
            END IF;
            --Evento de Base de Dedução com Pensão Alimentécia
            IF reRegistro.cod_tipo = 5 THEN
                inCodEventoBaseDeducaoPensao := selectIntoInteger(stSql);
            END IF;
            --Evento de Desconto IRRF Salário/Férias/13o.Salário c/Dedução de Pensão Alimentécia
            IF reRegistro.cod_tipo = 6 THEN
                inCodEventoDescIRRFPensao := selectIntoInteger(stSql);
            END IF;
            -- Evento de Base IRRF Salário/Ferias/13o.Salário
            IF reRegistro.cod_tipo = 7 THEN
                inCodEventoBaseIRRF := selectIntoInteger(stSql);
            END IF;
        END LOOP;
    END IF;

    --Folha Salário
    stEventoCalculadoSalario1 := '    SELECT sum(evento_calculado.valor) as valor
                                       FROM folhapagamento'||stEntidade||'.evento_calculado
                                 INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                                         ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                                        AND registro_evento_periodo.cod_periodo_movimentacao IN (';
    stEventoCalculadoSalario2 := ')';
    stEventoCalculadoSalario := stEventoCalculadoSalario1||stCodPeriodoMovimentacao||stEventoCalculadoSalario2;

    --Folha Complementar
    stEventoCalculadoComplementar1 := '    SELECT sum(evento_complementar_calculado.valor) as valor
                                            FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                                      INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                                              ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                                             AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                                             AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                             AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                                             AND registro_evento_complementar.cod_periodo_movimentacao IN (';
    stEventoCalculadoComplementar2 := ')';
    stEventoCalculadoComplementar := stEventoCalculadoComplementar1||stCodPeriodoMovimentacao||stEventoCalculadoComplementar2;

    --Folha Férias
    stEventoCalculadoFerias1 := '          SELECT sum(evento_ferias_calculado.valor) as valor
                                            FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                                      INNER JOIN folhapagamento'||stEntidade||'.registro_evento_ferias
                                              ON registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                             AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                                             AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                             AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                                             AND registro_evento_ferias.cod_periodo_movimentacao IN (';
    stEventoCalculadoFerias2 := ')';
    stEventoCalculadoFerias := stEventoCalculadoFerias1||stCodPeriodoMovimentacao||stEventoCalculadoFerias2;

    --Folha Rescisão
    stEventoCalculadoRescisao1 := '        SELECT sum(evento_rescisao_calculado.valor) as valor
                                            FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                      INNER JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao
                                              ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                             AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                             AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                             AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                                             AND registro_evento_rescisao.cod_periodo_movimentacao IN (';
    stEventoCalculadoRescisao2 := ')';
    stEventoCalculadoRescisao := stEventoCalculadoRescisao1||stCodPeriodoMovimentacao||stEventoCalculadoRescisao2;

    --Folha Décimo
    stEventoCalculadoDecimo1 := '          SELECT sum(evento_decimo_calculado.valor) as valor
                                            FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                                      INNER JOIN folhapagamento'||stEntidade||'.registro_evento_decimo
                                              ON registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                             AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                                             AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                             AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                                             AND registro_evento_decimo.cod_periodo_movimentacao IN (';
    stEventoCalculadoDecimo2 := ')';
    stEventoCalculadoDecimo := stEventoCalculadoDecimo1||stCodPeriodoMovimentacao||stEventoCalculadoDecimo2;

    --CONSULTA PRINCIPAL
    stSql := '';
    IF stSituacaoCadastro = 'pensionista'
    OR stSituacaoCadastro = 'todos' THEN
        stSql := '      SELECT cod_contrato
                             , registro
                             , numcgm
                             , nom_cgm
                             , cpf
                             , cod_cid
                             , cod_previdencia
                             , desc_orgao
                             , '''' as desc_local
                             , desc_funcao
                             , desc_especialidade_funcao
                             , '''' as valor_atributo
                             , data_laudo
                          FROM recuperarContratoPensionista(''cgm,o,l,f,ef,cid,pr''
                                                        ,'||quote_literal(stEntidade)||'
                                                        ,'||inCodPeriodoMovimentacao||'
                                                        ,'||quote_literal(stTipoFiltro)||'
                                                        ,'||quote_literal(stValoresFiltro)||'
                                                        ,'||quote_literal(stExercicio)||') as contrato';
    END IF;
    IF stSituacaoCadastro = 'todos' THEN
       stSql := stSql || ' UNION ';
    END IF;
    IF stSituacaoCadastro = 'ativo'
    OR stSituacaoCadastro = 'rescindido'
    OR stSituacaoCadastro = 'aposentado'
    OR stSituacaoCadastro = 'todos' THEN
        stSql := stSql || '
                        SELECT cod_contrato
                             , registro
                             , numcgm
                             , nom_cgm
                             , cpf
                             , cod_cid
                             , cod_previdencia
                             , desc_orgao
                             , desc_local
                             , desc_funcao
                             , desc_especialidade_funcao
                             , valor_atributo
                             , data_laudo
                          FROM recuperarContratoServidor(''cgm,o,l,f,ef,cid,pr''
                                                        ,'||quote_literal(stEntidade)||'
                                                        ,'||inCodPeriodoMovimentacao||'
                                                        ,'||quote_literal(stTipoFiltro)||'
                                                        ,'||quote_literal(stValoresFiltro)||'
                                                        ,'||quote_literal(stExercicio)||') as contrato';

        IF stSituacaoCadastro = 'ativo' THEN
            stSql := stSql || ' WHERE recuperarSituacaoDoContrato(contrato.cod_contrato,'||inCodPeriodoMovimentacao||','||quote_literal(stEntidade)||') = ''A'' ';
        END IF;
        IF stSituacaoCadastro = 'rescindido' THEN
            stSql := stSql || ' WHERE recuperarSituacaoDoContrato(contrato.cod_contrato,'||inCodPeriodoMovimentacao||','||quote_literal(stEntidade)||') = ''R'' ';
        END IF;
        IF stSituacaoCadastro = 'aposentado' THEN
            stSql := stSql || ' WHERE recuperarSituacaoDoContrato(contrato.cod_contrato,'||inCodPeriodoMovimentacao||','||quote_literal(stEntidade)||') = ''P'' ';
        END IF;
    END IF;

    FOR reRegistro IN EXECUTE stSql LOOP
        --####################################################################
        -- Evento de Base IRRF Salário/Ferias/13o.Salário
        nuSoma := 0;
        IF inCodEventoBaseIRRF IS NOT NULL THEN
            stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                   AND evento_calculado.cod_evento = '||inCodEventoBaseIRRF;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuSoma := nuSoma + nuValor;
            END IF;

            stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_complementar_calculado.cod_evento = '||inCodEventoBaseIRRF;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuSoma := nuSoma + nuValor;
            END IF;

            stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_ferias_calculado.cod_evento = '||inCodEventoBaseIRRF;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuSoma := nuSoma + nuValor;
            END IF;
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEventoBaseIRRF||'
                                                    AND evento_rescisao_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuSoma := nuSoma + nuValor;
            END IF;
        END IF;

        nuMolestiaAcidente := 0;

        --se zero entao ignora cid e mostra total rendimentos
        IF reRegistro.cod_cid = 0 THEN
            -- cid nao informado ou nao existente
            nuTotalRendimentos := nuSoma;
            nuMolestiaAcidente := 0;
        ELSE
             IF strpos(stIRRFCID, reRegistro.cod_cid::VARCHAR) > 0 AND reRegistro.data_laudo IS NOT NULL THEN

                --se a data do laudo é anterior ao ano consultado, todos os valores sao isentos
                IF to_char(reRegistro.data_laudo,'yyyy') < stExercicio THEN
                    nuMolestiaAcidente = nuSoma;
                ELSE
                    --se a data do laudo esta no ano consultado, os valores isentos sao aqueles apos a data do laudo
                    --se inCodEventoMolestia for nulo, faltou configuracao para o referido exercicio
                    IF to_char(reRegistro.data_laudo,'yyyy') = stExercicio AND inCodEventoMolestia IS NOT NULL THEN
                        --busca o periodo de movimentacao a partir da data do laudo ate o fim do ano
                        stSql := '  SELECT *
                                      FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                     WHERE to_char(periodo_movimentacao.dt_final,''yyyy'') = '|| quote_literal(stExercicio) ||'
                                     AND to_char(periodo_movimentacao.dt_final,''yyyy-mm-dd'') >= '|| quote_literal(to_char(reRegistro.data_laudo,'yyyy-mm-dd')) ||'
                                  ORDER BY cod_periodo_movimentacao';
                        FOR reRegistro2 IN EXECUTE stSql LOOP
                            stCodPeriodoMovimentacaoLaudo := stCodPeriodoMovimentacaoLaudo || reRegistro2.cod_periodo_movimentacao||',';
                        END LOOP;
                        stCodPeriodoMovimentacaoLaudo := substr(stCodPeriodoMovimentacaoLaudo,0,length(stCodPeriodoMovimentacaoLaudo));

                        --busca os valores nao tributaveis a partir da data do laudo
                        stSql := stEventoCalculadoSalario1 || stCodPeriodoMovimentacaoLaudo || stEventoCalculadoSalario2 ||
                                ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                AND evento_calculado.cod_evento = '||inCodEventoMolestia;
                        nuValor := selectIntoNumeric(stSql);
                        IF nuValor IS NOT NULL THEN
                            nuMolestiaAcidente := nuMolestiaAcidente + nuValor;
                        END IF;

                        stSql := stEventoCalculadoComplementar1 || stCodPeriodoMovimentacaoLaudo || stEventoCalculadoComplementar2 ||
                            ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                              AND evento_complementar_calculado.cod_evento = '||inCodEventoMolestia;
                        nuValor := selectIntoNumeric(stSql);
                        IF nuValor IS NOT NULL THEN
                            nuMolestiaAcidente := nuMolestiaAcidente + nuValor;
                        END IF;

                        stSql := stEventoCalculadoFerias1 || stCodPeriodoMovimentacaoLaudo || stEventoCalculadoFerias2 ||
                            ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                              AND evento_ferias_calculado.cod_evento = '||inCodEventoMolestia;
                        nuValor := selectIntoNumeric(stSql);
                        IF nuValor IS NOT NULL THEN
                            nuMolestiaAcidente := nuMolestiaAcidente + nuValor;
                        END IF;

                        stSql := stEventoCalculadoRescisao1 || stCodPeriodoMovimentacaoLaudo || stEventoCalculadoRescisao2 ||
                            ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                              AND evento_rescisao_calculado.cod_evento = '||inCodEventoMolestia||'
                              AND evento_rescisao_calculado.desdobramento != ''D'' ';
                        nuValor := selectIntoNumeric(stSql);
                        IF nuValor IS NOT NULL THEN
                            nuMolestiaAcidente := nuMolestiaAcidente + nuValor;
                        END IF;
                    END IF;
                END IF;

                nuTotalRendimentos := nuSoma - nuMolestiaAcidente;
            ELSE
                nuTotalRendimentos := nuSoma;
                nuMolestiaAcidente := 0;
            END IF;
        END IF;

        --####################################################################
        -- Evento de Previdência Oficial
        IF reRegistro.cod_previdencia IS NOT NULL THEN
            stSql := '    SELECT previdencia_evento.cod_evento
                            FROM folhapagamento'||stEntidade||'.previdencia_evento
                      INNER JOIN (  SELECT cod_previdencia
                                         , max(timestamp) as timestamp
                                      FROM folhapagamento'||stEntidade||'.previdencia_evento
                                     WHERE timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                                  GROUP BY cod_previdencia) as max_previdencia_evento
                              ON previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia
                             AND previdencia_evento.timestamp = max_previdencia_evento.timestamp
                           WHERE previdencia_evento.cod_tipo = 1
                             AND previdencia_evento.cod_previdencia = '||reRegistro.cod_previdencia;

            inCodEventoPrevOficial := selectIntoInteger(stSql);

            nuPrevidenciaOficial := 0;
            IF inCodEventoPrevOficial IS NOT NULL THEN
                stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                       AND evento_calculado.cod_evento = '||inCodEventoPrevOficial;
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaOficial := nuPrevidenciaOficial + nuValor;
                END IF;
                stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                            AND evento_complementar_calculado.cod_evento = '||inCodEventoPrevOficial;
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaOficial := nuPrevidenciaOficial + nuValor;
                END IF;

                stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                      AND evento_ferias_calculado.cod_evento = '||inCodEventoPrevOficial||'
                                                      AND evento_ferias_calculado.desdobramento != ''D'' ';
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaOficial := nuPrevidenciaOficial + nuValor;
                END IF;
                stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_rescisao_calculado.cod_evento = '||inCodEventoPrevOficial||'
                                                        AND evento_rescisao_calculado.desdobramento != ''D'' ';
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaOficial := nuPrevidenciaOficial + nuValor;
                END IF;
            END IF;
        END IF;

        --####################################################################
        -- Evento de Previdência Privada
        nuPrevidenciaPrivada := 0;
        stSql := '    SELECT contrato_servidor_previdencia.cod_previdencia
                        FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                  INNER JOIN (  SELECT contrato_servidor_previdencia.cod_contrato
                                     , max(contrato_servidor_previdencia.timestamp) as timestamp
                                  FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                 WHERE contrato_servidor_previdencia.timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                              GROUP BY contrato_servidor_previdencia.cod_contrato) as max_contrato_servidor_previdencia
                          ON max_contrato_servidor_previdencia.cod_contrato = contrato_servidor_previdencia.cod_contrato
                         AND max_contrato_servidor_previdencia.timestamp = contrato_servidor_previdencia.timestamp
                  INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                          ON previdencia_previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                         AND previdencia_previdencia.tipo_previdencia = ''p''
                  INNER JOIN (  SELECT previdencia_previdencia.cod_previdencia
                                     , max(previdencia_previdencia.timestamp) as timestamp
                                  FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                 WHERE timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                              GROUP BY previdencia_previdencia.cod_previdencia) as max_previdencia_previdencia
                          ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                         AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp
                       WHERE contrato_servidor_previdencia.bo_excluido = false
                         AND contrato_servidor_previdencia.cod_contrato = '||reRegistro.cod_contrato;

        FOR rePrevidenciaPrivada IN EXECUTE stSql LOOP
            stSql := '    SELECT previdencia_evento.cod_evento
                            FROM folhapagamento'||stEntidade||'.previdencia_evento
                      INNER JOIN (  SELECT cod_previdencia
                                         , max(timestamp) as timestamp
                                      FROM folhapagamento'||stEntidade||'.previdencia_evento
                                     WHERE timestamp <= '||quote_literal(stTimestampFechamentoPeriodo)||'
                                  GROUP BY cod_previdencia) as max_previdencia_evento
                              ON previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia
                             AND previdencia_evento.timestamp = max_previdencia_evento.timestamp
                           WHERE previdencia_evento.cod_tipo = 1
                             AND previdencia_evento.cod_previdencia = '||rePrevidenciaPrivada.cod_previdencia;
            inCodEventoPrevPrivada := selectIntoInteger(stSql);
            IF inCodEventoPrevPrivada IS NOT NULL THEN
                stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                       AND evento_calculado.cod_evento = '||inCodEventoPrevPrivada;
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaPrivada := nuPrevidenciaPrivada + nuValor;
                END IF;
                stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                            AND evento_complementar_calculado.cod_evento = '||inCodEventoPrevPrivada;
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaPrivada := nuPrevidenciaPrivada + nuValor;
                END IF;

                stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                      AND evento_ferias_calculado.cod_evento = '||inCodEventoPrevPrivada||'
                                                      AND evento_ferias_calculado.desdobramento != ''D'' ';
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaPrivada := nuPrevidenciaPrivada + nuValor;
                END IF;
                stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_rescisao_calculado.cod_evento = '||inCodEventoPrevPrivada||'
                                                        AND evento_rescisao_calculado.desdobramento != ''D'' ';
                nuValor := selectIntoNumeric(stSql);
                IF nuValor IS NOT NULL THEN
                    nuPrevidenciaPrivada := nuPrevidenciaPrivada + nuValor;
                END IF;
            END IF;
        END LOOP;

        --####################################################################
        -- Evento de Pensão Alimentícia
        nuPensaoAlimenticia := 0;
        IF inCodEventoPensaoAlimenticia IS NOT NULL THEN
            stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                   AND evento_calculado.cod_evento = '||inCodEventoPensaoAlimenticia;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuPrevidenciaOficial := nuPensaoAlimenticia + nuValor;
            END IF;
            stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_complementar_calculado.cod_evento = '||inCodEventoPensaoAlimenticia;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuPensaoAlimenticia := nuPensaoAlimenticia + nuValor;
            END IF;

            stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_ferias_calculado.cod_evento = '||inCodEventoPensaoAlimenticia||'
                                                  AND evento_ferias_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuPensaoAlimenticia := nuPensaoAlimenticia + nuValor;
            END IF;
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEventoPensaoAlimenticia||'
                                                    AND evento_rescisao_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuPensaoAlimenticia := nuPensaoAlimenticia + nuValor;
            END IF;
        END IF;

        --####################################################################
        -- Evento de Desconto IRRF para Salário/Férias/13o.Salário
        -- Evento de Desconto IRRF para Salário/Férias/13o.Salário com Pensão
        nuIRRFRetido := 0;
        IF inCodEventoDescIRRF IS NOT NULL THEN
            --Folha Salário
            stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                   AND evento_calculado.cod_evento = '||inCodEventoDescIRRF;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
            --Folha Complementar
            stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_complementar_calculado.cod_evento = '||inCodEventoDescIRRF;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
            --Folha Férias
            stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_ferias_calculado.cod_evento = '||inCodEventoDescIRRF||'
                                                  AND evento_ferias_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
            --Folha Rescisão
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEventoDescIRRF||'
                                                    AND evento_rescisao_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
        END IF;
        IF inCodEventoDescIRRFPensao IS NOT NULL THEN
            --Folha Salário
            stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                   AND evento_calculado.cod_evento = '||inCodEventoDescIRRFPensao;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
            --Folha Complementar
            stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_complementar_calculado.cod_evento = '||inCodEventoDescIRRFPensao;

            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
            --Folha Férias
            stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_ferias_calculado.cod_evento = '||inCodEventoDescIRRFPensao||'
                                                  AND evento_ferias_calculado.desdobramento != ''D'' ';

            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
            --Folha Rescisão
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEventoDescIRRFPensao||'
                                                    AND evento_rescisao_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuIRRFRetido := nuIRRFRetido + nuValor;
            END IF;
        END IF;


        --####################################################################
        --Evento Informativo de isenção (inativos/pensionistas) acima de 65 anos
        nuInfAposentadoria := 0;
        IF inCodEvento65Anos IS NOT NULL THEN
            --Folha Salário
            stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                   AND evento_calculado.cod_evento = '||inCodEvento65Anos;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuInfAposentadoria := nuInfAposentadoria + nuValor;
            END IF;
            --Folha Complementar
            stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_complementar_calculado.cod_evento = '||inCodEvento65Anos;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuInfAposentadoria := nuInfAposentadoria + nuValor;
            END IF;
            --Folha Férias
            stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_ferias_calculado.cod_evento = '||inCodEvento65Anos||'
                                                  AND evento_ferias_calculado.desdobramento != ''D'' ';

            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuInfAposentadoria := nuInfAposentadoria + nuValor;
            END IF;
            --Folha Rescisão
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEvento65Anos||'
                                                    AND evento_rescisao_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuInfAposentadoria := nuInfAposentadoria + nuValor;
            END IF;
        END IF;

        --####################################################################
        --Evento Diarias e Ajuda de Custo
        nuDiariasAjudaCusto := 0;
        IF trim(stCodEventoCompRendimento) != '' THEN
            --Folha Salário
            stSql := stEventoCalculadoSalario || ' AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                                   AND evento_calculado.cod_evento IN ('||stCodEventoCompRendimento||')';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDiariasAjudaCusto := nuDiariasAjudaCusto + nuValor;
            END IF;
            --Folha Complementar
            stSql := stEventoCalculadoComplementar || ' AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                                        AND evento_complementar_calculado.cod_evento IN ('||stCodEventoCompRendimento||')';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDiariasAjudaCusto := nuDiariasAjudaCusto + nuValor;
            END IF;
            --Folha Férias
            stSql := stEventoCalculadoFerias || ' AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_ferias_calculado.cod_evento IN ('||stCodEventoCompRendimento||')
                                                  AND evento_ferias_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDiariasAjudaCusto := nuDiariasAjudaCusto + nuValor;
            END IF;
            --Folha Rescisão
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento IN ('||stCodEventoCompRendimento||')
                                                    AND evento_rescisao_calculado.desdobramento != ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDiariasAjudaCusto := nuDiariasAjudaCusto + nuValor;
            END IF;
        END IF;

        --####################################################################
        --Décimo Terceiro Salário
        nuDecimoTerceiro := 0;
        IF inCodEventoBaseIRRF IS NOT NULL THEN
            --Folha Rescisão
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEventoBaseIRRF||'
                                                    AND evento_rescisao_calculado.desdobramento = ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDecimoTerceiro := nuDecimoTerceiro + nuValor;
            END IF;
            --Folha Décimo
            stSql := stEventoCalculadoDecimo || ' AND registro_evento_decimo.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_decimo_calculado.cod_evento = '||inCodEventoBaseIRRF;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDecimoTerceiro := nuDecimoTerceiro + nuValor;
            END IF;
        END IF;
        IF inCodEventoBaseDeducaoPensao IS NOT NULL THEN
            --Folha Rescisão
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEventoBaseDeducaoPensao||'
                                                    AND evento_rescisao_calculado.desdobramento = ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDecimoTerceiro := nuDecimoTerceiro - nuValor;
            END IF;
            --Folha Décimo
            stSql := stEventoCalculadoDecimo || ' AND registro_evento_decimo.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_decimo_calculado.cod_evento = '||inCodEventoBaseDeducaoPensao;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDecimoTerceiro := nuDecimoTerceiro - nuValor;
            END IF;
        END IF;
        IF inCodEventoDescIRRF IS NOT NULL THEN
            --Folha Rescisão
            stSql := stEventoCalculadoRescisao || ' AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                                    AND evento_rescisao_calculado.cod_evento = '||inCodEventoDescIRRF||'
                                                    AND evento_rescisao_calculado.desdobramento = ''D'' ';
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDecimoTerceiro := nuDecimoTerceiro - nuValor;
            END IF;
            --Folha Décimo
            stSql := stEventoCalculadoDecimo || ' AND registro_evento_decimo.cod_contrato = '||reRegistro.cod_contrato||'
                                                  AND evento_decimo_calculado.cod_evento = '||inCodEventoDescIRRF;
            nuValor := selectIntoNumeric(stSql);
            IF nuValor IS NOT NULL THEN
                nuDecimoTerceiro := nuDecimoTerceiro - nuValor;
            END IF;
        END IF;

        IF nuTotalRendimentos > 0 OR nuMolestiaAcidente > 0 OR nuPrevidenciaOficial > 0
        OR nuPensaoAlimenticia > 0 OR nuIRRFRetido > 0 OR nuInfAposentadoria > 0
        OR nuDiariasAjudaCusto > 0 OR nuDecimoTerceiro > 0 THEN
            rwComprovanteRendimentosIRRF.numcgm                                 := reRegistro.numcgm;
            rwComprovanteRendimentosIRRF.nom_cgm                                := reRegistro.nom_cgm;
            rwComprovanteRendimentosIRRF.cpf                                    := reRegistro.cpf;
            rwComprovanteRendimentosIRRF.cod_cid                                := reRegistro.cod_cid;
            IF boAgrupar IS TRUE THEN
                IF stTipoFiltro = 'lotacao_grupo' THEN
                    rwComprovanteRendimentosIRRF.agrupamento                    := reRegistro.desc_orgao;
                END IF;
                IF stTipoFiltro = 'local_grupo' THEN
                    rwComprovanteRendimentosIRRF.agrupamento                    := reRegistro.desc_local;
                END IF;
                IF stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
                    IF reRegistro.desc_especialidade_funcao IS NOT NULL THEN
                        rwComprovanteRendimentosIRRF.agrupamento                := reRegistro.desc_funcao||'/'||reRegistro.desc_especialidade_funcao;
                    ELSE
                        rwComprovanteRendimentosIRRF.agrupamento                := reRegistro.desc_funcao;
                    END IF;
                END IF;
                IF stTipoFiltro = 'atributo_servidor_grupo' OR stTipoFiltro = 'atributo_pensionista_grupo' THEN
                    rwComprovanteRendimentosIRRF.agrupamento                    := reRegistro.valor_atributo;
                END IF;
            ELSE
                rwComprovanteRendimentosIRRF.agrupamento                        := '';
            END IF;
            rwComprovanteRendimentosIRRF.total_rendimentos                      := nuTotalRendimentos;
            rwComprovanteRendimentosIRRF.pensao_proventos_molestia_acidente     := nuMolestiaAcidente;
            rwComprovanteRendimentosIRRF.contribuicao_previdenciaria_oficial    := nuPrevidenciaOficial;
            rwComprovanteRendimentosIRRF.contribuicao_previdenciaria_privada    := nuPrevidenciaPrivada;
            rwComprovanteRendimentosIRRF.pensao_alimenticia                     := nuPensaoAlimenticia;
            rwComprovanteRendimentosIRRF.imposto_renda_retido                   := nuIRRFRetido;
            rwComprovanteRendimentosIRRF.informativo_aposentadoria              := nuInfAposentadoria;
            rwComprovanteRendimentosIRRF.diarias_ajuda_custo                    := nuDiariasAjudaCusto;
            rwComprovanteRendimentosIRRF.decimo_terceiro                        := nuDecimoTerceiro;
            RETURN NEXT rwComprovanteRendimentosIRRF;
        END IF;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

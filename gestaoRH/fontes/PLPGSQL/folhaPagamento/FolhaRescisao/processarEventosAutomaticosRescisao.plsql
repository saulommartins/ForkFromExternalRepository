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
--    * Data de Criação: 25/10/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28747 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-03-26 09:51:14 -0300 (Qua, 26 Mar 2008) $
--
--    * Casos de uso: uc-04.05.18
--*/
--Ticket #13872
CREATE OR REPLACE FUNCTION  processarEventosAutomaticosRescisao() RETURNS BOOLEAN as $$
DECLARE
    boRetorno                               BOOLEAN;
    inCodContrato                           INTEGER;
    inCodPeriodoMovimentacao                INTEGER;
    inContadorDependentes                   INTEGER;
    inDiasServidor                          INTEGER;
    inQtdDependentesPensaoAlimenticia       INTEGER;
    inQtdDependentesSalarioFamilia          INTEGER;
    inCodServidor                           INTEGER;
    inCodRegimePrevidencia                  INTEGER;
    inCodEvento                             INTEGER;
    inCodContratoPensionista                INTEGER;
    stDataFinalCompetencia                  VARCHAR;
    stSql                                   VARCHAR;
    stNatureza                              VARCHAR;
    dtVigencia                              VARCHAR;
    stDataAtributoIpe                       VARCHAR;
    stMatAtributoIpe                        VARCHAR;
    arIncidencias                           VARCHAR[];
    stIncidencias                           VARCHAR;
    reRegistro                              RECORD;
    reConfiguracao                          RECORD;
    reIncidencias                           RECORD;
    crCursor                                REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    
    --- Pega o codigo para fazer rescisão se Pensionista
    inCodContratoPensionista   := pega0ContratoDoGeradorBeneficio(inCodContrato);
    
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    stDataFinalCompetencia     := substr(recuperarBufferTexto('stDataFinalCompetencia'),1,10);
    stSql := 'SELECT ultimo_registro_evento_rescisao.cod_registro
                 FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                    , folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                    , folhapagamento'||stEntidade||'.evento
                WHERE registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro
                  AND registro_evento_rescisao.cod_evento   = ultimo_registro_evento_rescisao.cod_evento
                  AND registro_evento_rescisao.timestamp    = ultimo_registro_evento_rescisao.timestamp
                  AND registro_evento_rescisao.desdobramento= ultimo_registro_evento_rescisao.desdobramento
                  AND registro_evento_rescisao.cod_evento = evento.cod_evento
                  AND evento.evento_sistema = true
                  AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                  AND registro_evento_rescisao.cod_contrato = '||inCodContrato||' ';
                  
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado WHERE cod_registro         = '||reRegistro.cod_registro;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_rescisao WHERE cod_registro         = '||reRegistro.cod_registro;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela WHERE cod_registro  = '||reRegistro.cod_registro;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao  WHERE cod_registro  = '||reRegistro.cod_registro;
        EXECUTE stSql;
    END LOOP;

    dtVigencia := selectIntoVarchar(' SELECT vigencia
                              FROM folhapagamento'||stEntidade||'.tabela_irrf
                                 , (SELECT cod_tabela
                                         , max(timestamp) as timestamp
                                      FROM folhapagamento'||stEntidade||'.tabela_irrf
                                     WHERE vigencia <= '''||stDataFinalCompetencia||'''
                                     GROUP BY cod_tabela) as max_tabela_irrf
                             WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                               AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp');
        
    dtVigencia := criarBufferTexto('dtVigenciaIrrf',dtVigencia);

    -- Para pensionistas
    --Busca as informações de incidência da causa de rescisão do contrato
    IF inCodContratoPensionista IS NOT NULL THEN
        stSql := 'SELECT caso_causa.*
                    FROM pessoal'||stEntidade||'.contrato_pensionista_caso_causa
                    , pessoal'||stEntidade||'.caso_causa
                WHERE contrato_pensionista_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa
                    AND contrato_pensionista_caso_causa.cod_contrato = '||inCodContrato;
    ELSE
        stSql := 'SELECT caso_causa.*
            FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
               , pessoal'||stEntidade||'.caso_causa
           WHERE contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa
             AND contrato_servidor_caso_causa.cod_contrato = '||inCodContrato;    
    END IF;             
                              
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reIncidencias;
    CLOSE crCursor;
    stIncidencias := '{';
    IF reIncidencias.inc_irrf_13 is true THEN
        stIncidencias := stIncidencias ||  '"t",';
    ELSE
        stIncidencias := stIncidencias ||  '"f",';
    END IF;
    IF reIncidencias.inc_irrf_ferias is true THEN
        stIncidencias := stIncidencias ||  '"t",';
    ELSE
        stIncidencias := stIncidencias ||  '"f",';
    END IF;
    IF reIncidencias.inc_irrf_aviso_previo is true THEN
        stIncidencias := stIncidencias ||  '"t"';
    ELSE
        stIncidencias := stIncidencias ||  '"f"';
    END IF;
    stIncidencias := stIncidencias || '}';
    arIncidencias := stIncidencias;

    --########################   IRRF   #####################################
    --Sql que verifica se o servidor possui dependentes
    inContadorDependentes := selectIntoInteger(' SELECT count(servidor.cod_servidor) as contador
                                         FROM pessoal'||stEntidade||'.servidor
                                            , pessoal'||stEntidade||'.servidor_contrato_servidor
                                            , pessoal'||stEntidade||'.servidor_dependente
                                        WHERE servidor_contrato_servidor.cod_contrato = '||inCodContrato||'
                                          AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                          AND servidor.cod_servidor                   = servidor_dependente.cod_servidor');
    --Se possuir dependentes faz a inclusão de registro de evento
    IF inContadorDependentes > 0 THEN
        boRetorno := inserirEventosAutomaticosRescisao(1,'S#D#V#P',arIncidencias);
    END IF;
    
    --Sql que verifica se o servidor se enquadra como inativo ou pensionista acima de 65 anos
    IF retornaDataAniversarioCom(65,inCodContrato) <= to_date(stDataFinalCompetencia,'yyyy/mm/dd') THEN
        boRetorno := inserirEventosAutomaticosRescisao(2,'S#D',arIncidencias);
    END IF;

    --Inclusão dos registro de eventos referentes aos tipos 3,4,5,6 e 7
    boRetorno := inserirEventosAutomaticosRescisao(3,'S#D#V#P',arIncidencias);
    boRetorno := inserirEventosAutomaticosRescisao(4,'S#D#V#P',arIncidencias);
    boRetorno := inserirEventosAutomaticosRescisao(5,'S#D#V#P',arIncidencias);
    boRetorno := inserirEventosAutomaticosRescisao(6,'S#D#V#P',arIncidencias);
    boRetorno := inserirEventosAutomaticosRescisao(7,'S#A#V#P#D',arIncidencias);
    
    --########################   IRRF   #####################################

    --########################   FGTS   #####################################
    ----Código para inclusão de registro de eventos FGTS
    dtVigencia := selectIntoVarchar(' SELECT MAX(vigencia)
                              FROM folhapagamento'||stEntidade||'.fgts
                             WHERE vigencia <= '''||stDataFinalCompetencia||''' ');
    stSql := 'SELECT cod_evento
                    , cod_tipo
                    , fgts_categoria.aliquota_deposito
                    , fgts_categoria.aliquota_contribuicao
                 FROM pessoal'||stEntidade||'.contrato_servidor
                    , folhapagamento'||stEntidade||'.fgts_categoria
                    , folhapagamento'||stEntidade||'.fgts
                    , (  SELECT max(timestamp) as timestamp
                              , cod_fgts
                           FROM folhapagamento'||stEntidade||'.fgts
                          WHERE fgts.vigencia <= '''||dtVigencia||'''
                       GROUP BY cod_fgts) as max_fgts
                    , folhapagamento'||stEntidade||'.fgts_evento
                WHERE contrato_servidor.cod_categoria = fgts_categoria.cod_categoria
                  AND fgts_categoria.cod_fgts = fgts.cod_fgts
                  AND fgts_categoria.timestamp= fgts.timestamp
                  AND fgts.cod_fgts = fgts_evento.cod_fgts
                  AND fgts.timestamp= fgts_evento.timestamp
                  AND fgts.cod_fgts = max_fgts.cod_fgts
                  AND fgts.timestamp= max_fgts.timestamp
                  AND contrato_servidor.cod_contrato = '||inCodContrato||' ';

    FOR reRegistro IN  EXECUTE stSql
    LOOP

        IF (reRegistro.aliquota_deposito     > 0 AND reRegistro.cod_tipo = 1) or
           (reRegistro.aliquota_contribuicao > 0 AND reRegistro.cod_tipo = 2) or
           (reRegistro.cod_tipo = 3                                         ) THEN
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'S');
            IF reIncidencias.inc_fgts_aviso_previo is true THEN
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'A');
            END IF;
            IF reIncidencias.inc_fgts_13 is true THEN
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'D');
            END IF;
        END IF;
    END LOOP;
    --########################   FGTS   #####################################

    --########################   PREVIDÊNCIA   #####################################
    --Código para inclusão de registro de eventos Previdência
    stSql := '    SELECT MAX(vigencia)
                    FROM folhapagamento'||stEntidade||'.previdencia_previdencia
              INNER JOIN (
                        SELECT contrato_servidor_previdencia.cod_contrato
                             , contrato_servidor_previdencia.cod_previdencia
                          FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                         WHERE contrato_servidor_previdencia.timestamp = ( SELECT timestamp
                                                                             FROM pessoal'||stEntidade||'.contrato_servidor_previdencia as contrato_servidor_previdencia_interna
                                                                            WHERE contrato_servidor_previdencia_interna.cod_contrato = contrato_servidor_previdencia.cod_contrato
                                                                              AND contrato_servidor_previdencia_interna.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                                                              AND contrato_servidor_previdencia.bo_excluido = false
                                                                         ORDER BY timestamp desc
                                                                            LIMIT 1
                                                                         )

                        UNION
                        SELECT contrato_pensionista_previdencia.cod_contrato
                             , contrato_pensionista_previdencia.cod_previdencia
                          FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                         WHERE contrato_pensionista_previdencia.timestamp = ( SELECT timestamp
                                                                                FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia as contrato_pensionista_previdencia_interna
                                                                               WHERE contrato_pensionista_previdencia_interna.cod_contrato = contrato_pensionista_previdencia.cod_contrato
                                                                            ORDER BY timestamp desc
                                                                               LIMIT 1
                                                                             )
                        ) as previdencia_contrato on folhapagamento'||stEntidade||'.previdencia_previdencia.cod_previdencia = previdencia_contrato.cod_previdencia
              WHERE previdencia_contrato.cod_contrato = '||inCodContrato||'
                AND vigencia <= '''||stDataFinalCompetencia||'''';
    
    dtVigencia := selectIntoVarchar(stSql);
    --VERIFICAÇÃO DE EXISTENCIA DE PREVIDENCIAS CADASTRADAS NO CONTRATO DO SERVIDOR
    IF dtVigencia IS NOT NULL THEN
        dtVigencia := criarBufferTexto('dtVigenciaPrevidencia',dtVigencia);

        IF inCodContratoPensionista IS NOT NULL THEN
            stSql := 'SELECT cod_evento
                          , cod_tipo
                       FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                          , folhapagamento'||stEntidade||'.previdencia_previdencia
                          , (  SELECT max(timestamp) as timestamp
                                    , cod_previdencia
                                 FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                WHERE previdencia_previdencia.vigencia = '''||dtVigencia||'''
                             GROUP BY cod_previdencia) as max_previdencia_previdencia
                          , (  SELECT max(timestamp) as timestamp
                                    , cod_contrato
                                 FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                             GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                          , folhapagamento'||stEntidade||'.previdencia_evento
                      WHERE contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                        AND contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                        AND contrato_pensionista_previdencia.timestamp    = max_contrato_pensionista_previdencia.timestamp
                        --AND contrato_pensionista_previdencia.bo_excluido  = false
                        AND previdencia_previdencia.cod_previdencia    = max_previdencia_previdencia.cod_previdencia
                        AND previdencia_previdencia.timestamp          = max_previdencia_previdencia.timestamp
                        AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                        AND previdencia_previdencia.timestamp       = previdencia_evento.timestamp
                        AND previdencia_evento.cod_tipo != 3
                        AND contrato_pensionista_previdencia.cod_contrato = '||inCodContrato||' ';
        ELSE
            stSql := 'SELECT cod_evento
                            , cod_tipo
                         FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                            , folhapagamento'||stEntidade||'.previdencia_previdencia
                            , (  SELECT max(timestamp) as timestamp
                                      , cod_previdencia
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                  WHERE previdencia_previdencia.vigencia = '''||dtVigencia||'''
                               GROUP BY cod_previdencia) as max_previdencia_previdencia
                            , (  SELECT max(timestamp) as timestamp
                                      , cod_contrato
                                   FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                               GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                            , folhapagamento'||stEntidade||'.previdencia_evento
                        WHERE contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                          AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                          AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                          AND contrato_servidor_previdencia.bo_excluido  = false
                          AND previdencia_previdencia.cod_previdencia    = max_previdencia_previdencia.cod_previdencia
                          AND previdencia_previdencia.timestamp          = max_previdencia_previdencia.timestamp
                          AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                          AND previdencia_previdencia.timestamp       = previdencia_evento.timestamp
                          AND previdencia_evento.cod_tipo != 3
                          AND contrato_servidor_previdencia.cod_contrato = '||inCodContrato||' ';
        END IF;          
        
        FOR reRegistro IN  EXECUTE stSql
        LOOP
            stNatureza := pega0NaturezaEvento(reRegistro.cod_evento);
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'S');
            IF NOT(stNatureza = 'D') AND reIncidencias.inc_prev_aviso_previo is true THEN
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'A');
            END IF;
            IF reIncidencias.inc_prev_13 is true THEN
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'D');
            END IF;
        END LOOP;
    ELSE
        dtVigencia := criarBufferTexto('dtVigenciaPrevidencia','NULL');
    END IF;
    --########################   PREVIDÊNCIA   #####################################


    --########################   PENSÃO   #####################################
    --Código para inclusão de registro de eventos Pensão
    inQtdDependentesPensaoAlimenticia = pega0QtdDependentesPensaoAlimenticia( inCodContrato, stDataFinalCompetencia );
    
    inQtdDependentesPensaoAlimenticia = criarBufferInteiro('inQtdDependentesPensaoAlimenticia',inQtdDependentesPensaoAlimenticia );
    IF inQtdDependentesPensaoAlimenticia > 0 THEN
        inCodServidor := pega0ServidorDoContrato(inCodContrato);
        stSql := 'SELECT pensao.cod_pensao
                        , pensao_incidencia.cod_incidencia
                     FROM pessoal'||stEntidade||'.pensao
                        , (  SELECT cod_pensao
                                  , max(timestamp) as timestamp
                               FROM pessoal'||stEntidade||'.pensao
                           GROUP BY cod_pensao) as max_pensao
                        , pessoal'||stEntidade||'.pensao_incidencia
                    WHERE pensao.cod_pensao NOT IN (SELECT cod_pensao
                                                      FROM pessoal'||stEntidade||'.pensao_excluida)
                      AND pensao.cod_servidor = '||inCodServidor||'
                      AND pensao.cod_pensao = max_pensao.cod_pensao
                      AND pensao.timestamp  = max_pensao.timestamp
                      AND pensao.cod_pensao = pensao_incidencia.cod_pensao
                      AND pensao.timestamp = pensao_incidencia.timestamp';
        inCodEvento                       = pega2CodEventoDescontoPensaoAlimenticia();
        boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,'S');
        FOR reRegistro IN  EXECUTE stSql
        LOOP
            IF reRegistro.cod_incidencia = 1 THEN
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,'D');
            END IF;
            IF reRegistro.cod_incidencia = 5 THEN
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,'V');
                boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,'P');
            END IF;
        END LOOP;
    END IF;
    --########################   PENSÃO   #####################################


    --########################   SALÁRIO FAMÍLIA   #####################################
    --Código para inclusão de registro de eventos Salário Família
    inQtdDependentesSalarioFamilia = pega0QtdDependentesSalarioFamilia( inCodContrato, stDataFinalCompetencia );
    inQtdDependentesSalarioFamilia = criarBufferInteiro('inQtdDependentesSalarioFamilia',inQtdDependentesSalarioFamilia);
    
    IF inQtdDependentesSalarioFamilia > 0 THEN
        
        IF inCodContratoPensionista IS NOT NULL THEN                                          
            inCodRegimePrevidencia := selectIntoInteger('SELECT previdencia.cod_regime_previdencia
                                                  FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                                                     , (  SELECT cod_contrato
                                                               , max(timestamp) as timestamp
                                                            FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                                                        GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                                     , folhapagamento'||stEntidade||'.previdencia_previdencia
                                                     , (  SELECT cod_previdencia
                                                               , max(timestamp) as timestamp
                                                            FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                                        GROUP BY cod_previdencia) as max_previdencia_previdencia
                                                     , folhapagamento'||stEntidade||'.previdencia
                                                 WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                                   AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
                                                   AND contrato_pensionista_previdencia.cod_contrato = '||inCodContrato||'
                                                   AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                                   AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                                   AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                                   AND previdencia_previdencia.tipo_previdencia = ''o''
                                                   AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia');    
        ELSE
            inCodRegimePrevidencia := selectIntoInteger('SELECT previdencia.cod_regime_previdencia
                                                  FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                                     , (  SELECT cod_contrato
                                                               , max(timestamp) as timestamp
                                                            FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                                        GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                                     , folhapagamento'||stEntidade||'.previdencia_previdencia
                                                     , (  SELECT cod_previdencia
                                                               , max(timestamp) as timestamp
                                                            FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                                        GROUP BY cod_previdencia) as max_previdencia_previdencia
                                                     , folhapagamento'||stEntidade||'.previdencia
                                                 WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                                   AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                                   AND contrato_servidor_previdencia.cod_contrato = '||inCodContrato||'
                                                   AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                                   AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                                   AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                                   AND previdencia_previdencia.tipo_previdencia = ''o''
                                                   AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia');
        END IF;
        
        stSql := 'SELECT salario_familia_evento.cod_evento
                    FROM folhapagamento'||stEntidade||'.salario_familia_evento
                       , (  SELECT cod_regime_previdencia
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'||stEntidade||'.salario_familia
                             WHERE vigencia <= '''||stDataFinalCompetencia||'''
                          GROUP BY cod_regime_previdencia) as max_salario_familia
                   WHERE salario_familia_evento.cod_regime_previdencia  = max_salario_familia.cod_regime_previdencia
                     AND salario_familia_evento.timestamp = max_salario_familia.timestamp
                     AND salario_familia_evento.cod_regime_previdencia = '||inCodRegimePrevidencia;
        FOR reRegistro IN EXECUTE stSql LOOP
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'S');
        END LOOP;
    END IF;
    --########################   SALÁRIO FAMÍLIA   #####################################


    --########################   FÉRIAS   #####################################
    --Inclusão de 1/3 de férias
    stSql := ' SELECT cod_evento
                  FROM folhapagamento'||stEntidade||'.ferias_evento
                     , (SELECT max(timestamp) as timestamp
                              , cod_tipo
                           FROM folhapagamento'||stEntidade||'.ferias_evento
                       GROUP by cod_tipo) as max_timestamp
                 WHERE max_timestamp.cod_tipo  = ferias_evento.cod_tipo
                   AND max_timestamp.timestamp = ferias_evento.timestamp
                   AND ferias_evento.cod_tipo  = 2 ';

    FOR reRegistro IN  EXECUTE stSql
    LOOP
        boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'V');
        boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'P');
    END LOOP;
    --########################   FÉRIAS   ####################################


    --########################   13º Salário   ####################################
    stSql := ' SELECT cod_evento
                  FROM folhapagamento'||stEntidade||'.decimo_evento
                     , (SELECT max(timestamp) as timestamp
                              , cod_tipo
                           FROM folhapagamento'||stEntidade||'.decimo_evento
                       GROUP by cod_tipo) as max_timestamp
                 WHERE max_timestamp.cod_tipo  = decimo_evento.cod_tipo
                   AND max_timestamp.timestamp = decimo_evento.timestamp
                   AND decimo_evento.cod_tipo  = 1 ';

    FOR reRegistro IN  EXECUTE stSql
    LOOP
        boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'D');
    END LOOP;
    --########################   13º Salário   ####################################


    --EVENTOS DE DESCONTO EXTERNO
    stSql := 'SELECT *
                 FROM folhapagamento'||stEntidade||'.configuracao_eventos_desconto_externo';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.evento_desconto_irrf,'S');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.evento_base_irrf,'S');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.evento_desconto_previdencia,'S');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reRegistro.evento_base_previdencia,'S');
    END LOOP;

    --EVENTOS DE DESCONTO EXTERNO

    --EVENTOS AUTOMÁTICOS DE IPERS
    stSql := '
    SELECT configuracao_ipe.*
      FROM folhapagamento'||stEntidade||'.configuracao_ipe
         , (SELECT max(cod_configuracao) as cod_configuracao
                 , vigencia
              FROM folhapagamento'||stEntidade||'.configuracao_ipe
            GROUP BY vigencia) as max_configuracao_ipe
      WHERE configuracao_ipe.cod_configuracao = max_configuracao_ipe.cod_configuracao
        AND configuracao_ipe.vigencia = max_configuracao_ipe.vigencia
        AND configuracao_ipe.vigencia <= (SELECT dt_final
                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                           WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')
    ORDER BY configuracao_ipe.vigencia desc
    LIMIT 1';
    
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reConfiguracao;
    CLOSE crCursor;
    IF reConfiguracao.cod_configuracao IS NOT NULL THEN
        stSql := '
        SELECT atributo_contrato_servidor_valor.valor
          FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                , (SELECT cod_contrato
                        , cod_atributo
                        , max(timestamp) as timestamp
                    FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                    GROUP BY cod_contrato
                        , cod_atributo) as max_atributo_contrato_servidor_valor
          WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
            AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
            AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp
            AND atributo_contrato_servidor_valor.cod_contrato = '||inCodContrato||'
            AND atributo_contrato_servidor_valor.cod_atributo = '||reConfiguracao.cod_atributo_data||'
            AND atributo_contrato_servidor_valor.cod_modulo = '||reConfiguracao.cod_modulo_data||'
            AND atributo_contrato_servidor_valor.cod_cadastro = '||reConfiguracao.cod_cadastro_data;
        stDataAtributoIpe := selectIntoVarchar(stSql);
        stDataAtributoIpe := replace(stDataAtributoIpe, ' ', '');
        stSql := '
         SELECT atributo_contrato_servidor_valor.valor
           FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                , (SELECT cod_contrato
                        , cod_atributo
                        , max(timestamp) as timestamp
                    FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                    GROUP BY cod_contrato
                        , cod_atributo) as max_atributo_contrato_servidor_valor
          WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
            AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
            AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp
            AND atributo_contrato_servidor_valor.cod_contrato = '||inCodContrato||'
            AND atributo_contrato_servidor_valor.cod_atributo = '||reConfiguracao.cod_atributo_mat||'
            AND atributo_contrato_servidor_valor.cod_modulo = '||reConfiguracao.cod_modulo_mat||'
            AND atributo_contrato_servidor_valor.cod_cadastro = '||reConfiguracao.cod_cadastro_mat;
        stMatAtributoIpe := selectIntoVarchar(stSql);
        stMatAtributoIpe := replace(stMatAtributoIpe, ' ', '');
        IF stDataAtributoIpe IS NOT NULL
        AND stDataAtributoIpe != ''
        AND stMatAtributoIpe IS NOT NULL
        AND stMatAtributoIpe != ''
        THEN
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_automatico,'S');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_automatico,'V');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_automatico,'P');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_base,'S');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_base,'V');
            boRetorno := insertRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_base,'P');
        END IF;
    END IF;
    --EVENTOS AUTOMÁTICOS DE IPERS

    RETURN boRetorno;
END;
$$ LANGUAGE 'plpgsql';

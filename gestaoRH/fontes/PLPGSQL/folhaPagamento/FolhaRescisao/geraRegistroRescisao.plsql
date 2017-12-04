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
CREATE OR REPLACE FUNCTION geraRegistroRescisao(integer,integer,varchar,varchar) RETURNS BOOLEAN as $$
DECLARE
    inCodContrato                    ALIAS FOR $1;
    inCodPeriodoMovimentacao         ALIAS FOR $2;
    stExercicioAtual                 ALIAS FOR $3;
    stEntidadeParametro              ALIAS FOR $4;
    stEntidade                       VARCHAR;
    stExercicioAtualTemp             VARCHAR; 
    inCodPeriodoMovimentacaoInicial  INTEGER := 0;
    inCodPeriodoMovimentacaoAberta   INTEGER ;
    inCodContrato0                   INTEGER := 0;
    stSql                            VARCHAR :='';
    crCursor                         REFCURSOR;
    reRegistro                       RECORD;
    reRegistro1                      RECORD;
    reRescisao                       RECORD;
    reFerias                         RECORD; 
    boRetorno                        BOOLEAN := TRUE;
    dtInicialPeriodo                 DATE;
    dtFinalPeriodo                   DATE;
    stSituacao                       VARCHAR := 'f';
    stDataFinalCompetencia           VARCHAR := '';
    stAnoAdiantamento                VARCHAR := '';
    stDesdobramento                  VARCHAR := '';
    stDesdobramentoDecimo            VARCHAR := '';
    inCodEvento                      INTEGER := 0;
    InCodSubDivisao                  INTEGER := 1;
    inCodFuncao                      INTEGER := 1;
    inCodEspecialidade               INTEGER := 1;
    inNrRegistros                    INTEGER := 0;
    inContador                       INTEGER := 0;
    stFormula                        VARCHAR := '';
    stExecutaFormula                 VARCHAR := '';
    nuExecutaFormula                 NUMERIC := 0;
    nuValor                          NUMERIC := 0;
    nuQuantidade                     NUMERIC := 0;
    nuPercentualAdiantamento         NUMERIC := 0;
    boGerarApenasFixo                VARCHAR := 'f';
    boGravouRegistro                 BOOLEAN;
    stSituacaFerias                  VARCHAR := ''; 
    stDataRescisao                   VARCHAR := '';
    inCountFerias                    INTEGER := 0;
    inGeraRegistroRescisao           INTEGER := 1;
    inControleExecucaoRescisaoFerias INTEGER;
    dtRescisao                       VARCHAR; 
    boIncFolhaSalario                BOOLEAN;
    boIncFolhaDecimo                 BOOLEAN;
    boPagaFeriasVencida              BOOLEAN;
    boPagaFeriasProporcional         BOOLEAN;
    stAvisoPrevio                    VARCHAR;
    dtFinalAquisitivo                VARCHAR;
    inAnoCompetenciaAtual            INTEGER;
    inAnoCompetenciaAnterior         INTEGER;
    inCodContratoPensionista         INTEGER;
BEGIN
    stEntidade := criarBufferTexto('stEntidade',stEntidadeParametro);
    stExercicioAtualTemp := criarBufferTexto('stExercicioAtual',stExercicioAtual);
    inCodPeriodoMovimentacaoAberta :=  pega0CodigoPeriodoMovimentacaoAberta();
    
    --- Pega o codigo para fazer rescisão se Pensionista
    inCodContratoPensionista := pega0ContratoDoGeradorBeneficio(inCodContrato);

    IF(inCodPeriodoMovimentacaoAberta = inCodPeriodoMovimentacao ) THEN
        stDataFinalCompetencia := pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao);
        stDataFinalCompetencia := criarBufferTexto(  'stDataFinalCompetencia',  stDataFinalCompetencia );
        inCodContrato0         := criarBufferInteiro( 'inCodContrato' , inCodContrato );
        inCodSubDivisao        := pega0SubDivisaoDoContratoNaData( inCodContrato, stDataFinalCompetencia );
        inCodFuncao            := pega0FuncaoDoContratoNaData( inCodContrato, stDataFinalCompetencia );
        inCodEspecialidade     := pega0EspecialidadeDoContratoNaData( inCodContrato, stDataFinalCompetencia );
        stSituacao             := pega0SituacaoDaFolhaSalario();
        inGeraRegistroRescisao := criarBufferInteiro('inGeraRegistroRescisao',inGeraRegistroRescisao);
        inControleExecucaoRescisaoFerias := criarBufferInteiro('inControleExecucaoRescisaoFerias',1);
    
        inCodPeriodoMovimentacaoInicial := inCodPeriodoMovimentacao - 12;
    
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado 
               WHERE cod_registro IN ( SELECT cod_registro 
                                        FROM folhapagamento'||stEntidade||'.registro_evento_rescisao 
                                       WHERE cod_contrato = '||inCodContrato||'
                                         AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
               WHERE cod_registro IN ( SELECT cod_registro 
                                        FROM folhapagamento'||stEntidade||'.registro_evento_rescisao 
                                       WHERE cod_contrato = '||inCodContrato||'
                                         AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')';
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                                       WHERE cod_contrato = '||inCodContrato||'
                                         AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
        EXECUTE stSql;
    
        --CONSULTA PARA IDENTIFICAR QUAIS SERÃO OS TIPO DE DESDOBRAMENTO POSSÍVEIS NO REGISTRO DE RESCISÃO
        --- Verifica se pensionista ou servidor
        IF inCodContratoPensionista IS NOT NULL THEN
            stSql := ' SELECT contrato_pensionista_caso_causa.*
                        FROM  pessoal'||stEntidade||'.caso_causa
                        JOIN  pessoal'||stEntidade||'.contrato_pensionista_caso_causa
                          ON  (contrato_pensionista_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa)
                        WHERE  contrato_pensionista_caso_causa.cod_contrato = '||inCodContrato;
                        
            FOR reRescisao IN EXECUTE stSql LOOP
              dtRescisao          := reRescisao.dt_rescisao;
              boIncFolhaSalario   := reRescisao.inc_folha_salario;
              boIncFolhaDecimo    := reRescisao.inc_folha_decimo;
              boPagaFeriasVencida := NULL;
              boPagaFeriasProporcional := NULL;
              stAvisoPrevio       := NULL;
            END LOOP;
        ELSE               
            stSql := 'SELECT contrato_servidor_caso_causa.*
                           ,aviso_previo.aviso_previo
                           ,caso_causa.paga_ferias_vencida
                           ,caso_causa.paga_ferias_proporcional
                      FROM  pessoal'||stEntidade||'.caso_causa
                      JOIN  pessoal'||stEntidade||'.contrato_servidor_caso_causa
                        ON  (contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa)
                 LEFT JOIN  pessoal'||stEntidade||'.aviso_previo
                        ON  (aviso_previo.cod_contrato = contrato_servidor_caso_causa.cod_contrato)
                     WHERE  contrato_servidor_caso_causa.cod_contrato = '||inCodContrato;
            
            FOR reRescisao IN EXECUTE stSql LOOP
                dtRescisao          := reRescisao.dt_rescisao;
                boIncFolhaSalario   := reRescisao.inc_folha_salario;
                boIncFolhaDecimo    := reRescisao.inc_folha_decimo;
                boPagaFeriasVencida := reRescisao.paga_ferias_vencida;
                boPagaFeriasProporcional := reRescisao.paga_ferias_proporcional;
                stAvisoPrevio       := reRescisao.aviso_previo;
            END LOOP;
        END IF;
            
        stDataRescisao := CAST(reRescisao.dt_rescisao as VARCHAR);
        stDataRescisao := criarBufferTexto('stDataRescisao',stDataRescisao);
    
        -- COM A OPÇÃO INCORPORAR FOLHA SALÁRIO NA RESCISÃO
        -- O SISTEMA APAGARÁ OS CÁLCULOS DE SALÁRIO E COPIAR OS MESMOS PARA A RESCISÃO. 
        IF boIncFolhaSalario = 't' AND stSituacao =  'a' THEN
            boRetorno := copiarRegistroEventoSalarioParaRegistroEventoRescisao(inCodContrato,inCodPeriodoMovimentacao);
        END IF;
        -- COM A OPÇÃO INCORPORAR FOLHA 13º NA RESCISÃO 
        -- O SISTEMA APAGARÁ OS CÁLCULOS DE 13º E OS REGISTROS DE EVENTOS DE 13º E COPIÁ-LOS PARA RESCISÃO. 
        IF(boIncFolhaDecimo = 't') THEN
            boRetorno := deletarEventoCalculadoDecimo(inCodContrato,inCodPeriodoMovimentacao);
            boRetorno := deletarRegistroEventoDecimo(inCodContrato,inCodPeriodoMovimentacao,'D');
            boRetorno := geraRegistroDecimo(inCodContrato,inCodPeriodoMovimentacao,'D',stEntidade);
        ELSE 
    
            stSql := ' SELECT to_char(dt_final, ''YYYY'')::int as ano 
                           FROM folhapagamento'||stEntidade||'.periodo_movimentacao 
                          WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
            inAnoCompetenciaAtual := selectIntoInteger(stSql);                     
              
            stSql := ' SELECT to_char(dt_final, ''YYYY'')::int as ano 
                           FROM folhapagamento'||stEntidade||'.periodo_movimentacao 
                          WHERE cod_periodo_movimentacao = '||(inCodPeriodoMovimentacao-1);
            inAnoCompetenciaAnterior := selectIntoInteger(stSql);                                           
                        
              -- VERIFICA SE EXISTE CONCESSAO DE SALDO DECIMO NO EXERCICIO
            IF inAnoCompetenciaAtual != inAnoCompetenciaAnterior THEN
                boRetorno := geraRegistroDecimo(inCodContrato,inCodPeriodoMovimentacao,'D',stEntidade);
            ELSE
                boRetorno := selectIntoBoolean('SELECT true as retorno
                                                    FROM folhapagamento'||stEntidade||'.concessao_decimo
                                                    WHERE (cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                                      OR  cod_periodo_movimentacao = '||inCodPeriodoMovimentacao-1||')
                                                      AND cod_contrato = '||inCodContrato||'
                                                      AND desdobramento = ''D''');
                IF boRetorno IS NULL THEN
                    boRetorno := geraRegistroDecimo(inCodContrato,inCodPeriodoMovimentacao,'D',stEntidade);
                END IF;
            END IF;
        END IF;
        IF boIncFolhaSalario = 't' AND stSituacao =  'a' THEN
            boRetorno := deletarEventoCalculado(inCodContrato,inCodPeriodoMovimentacao);
        END IF;
    
        --- Calcula valores de férias e aviso prévio somente para Servidor
        IF inCodContratoPensionista IS NULL THEN 
            -- OPÇÃO PAGAR FÉRIAS
            -- VERIFICA EM QUE SITUAÇÃO SE ENCONTRAM AS FÉRIAS 
        
            stSituacaFerias  := retornaSituacaoFeriasContrato(inCodContrato,inCodPeriodoMovimentacao);
            stSituacaFerias  := criarBufferTexto('stSituacaFerias',stSituacaFerias);
                
            -- COM A SITUAÇÃO DAS FÉRIAS VENCIDAS E NA CAUSA DE RESCISÃO ESTIVER HABILITADA A OPÇÃO PAGA_FERIAS_VENCIDA
            -- O SISTEMA DEVERÁ GERAR REGISTRO DE EVENTO DE RESCISÃO TIPO FÉRIAS
            IF(stSituacaFerias = 'V') THEN 
                WHILE inCountFerias = 0  LOOP
                    IF(boPagaFeriasVencida = 't') THEN
                        boRetorno := concederFeriasAutomatico(inCodContrato, inCodPeriodoMovimentacao,'V');
                        dtFinalAquisitivo := selectIntoVarchar( 'SELECT ferias.dt_final_aquisitivo
                                 FROM   pessoal'||stEntidade||'.ferias
                                       ,pessoal'||stEntidade||'.lancamento_ferias
                                WHERE cod_contrato = '||inCodContrato||'
                             ORDER BY ferias.cod_ferias
                                 DESC LIMIT 1');
        
                        IF dtFinalAquisitivo > stDataFinalCompetencia THEN
                            stSql := 'DELETE FROM pessoal'||stEntidade||'.lancamento_ferias WHERE cod_ferias = (SELECT MAX(cod_ferias) FROM pessoal'||stEntidade||'.ferias)';
                            EXECUTE stSql;
                            stSql := 'DELETE FROM pessoal'||stEntidade||'.ferias WHERE cod_ferias = (SELECT MAX(cod_ferias) FROM pessoal'||stEntidade||'.ferias)';
                            EXECUTE stSql;
                            inCountFerias := 1;
                        ELSE 
                             boRetorno := geraRegistroFerias(inCodContrato,inCodPeriodoMovimentacao,stExercicioAtual,stEntidade);
                        END IF;
                    ELSE 
                        inCountFerias := 1;
                    END IF;
                END LOOP;
            END IF;
        
            -- COM A SITUAÇÃO DAS FÉRIAS VENCIDAS E NA CAUSA DE RESCISÃO ESTIVER HABILITADA A OPÇÃO PAGA_FERIAS_PROPORCIONAL
            -- O SISTEMA DEVERÁ CONCEDER AS FÉRIAS VENCIDAS MAS NÃO GERAR REGISTRO DE EVENTO DE RESCISÃO 
            inCountFerias = 0; 
            stSituacaFerias  := retornaSituacaoFeriasContrato(inCodContrato,inCodPeriodoMovimentacao);
            IF(boPagaFeriasProporcional = 't' AND stSituacaFerias = 'V' ) THEN
                WHILE inCountFerias = 0  LOOP
                    boRetorno := concederFeriasAutomatico(inCodContrato, inCodPeriodoMovimentacao,'V');
                    dtFinalAquisitivo := selectIntoVarchar( 'SELECT ferias.dt_final_aquisitivo
                                 FROM   pessoal'||stEntidade||'.ferias
                                       ,pessoal'||stEntidade||'.lancamento_ferias
                                WHERE cod_contrato = '||inCodContrato||'
                             ORDER BY ferias.cod_ferias
                                 DESC LIMIT 1');
        
                    IF reFerias.dt_final_aquisitivo > stDataFinalCompetencia THEN 
                        stSql := 'DELETE FROM pessoal'||stEntidade||'.lancamento_ferias WHERE cod_ferias = (SELECT MAX(cod_ferias) FROM pessoal'||stEntidade||'.ferias)';
                        EXECUTE stSql;
                        stSql := 'DELETE FROM pessoal'||stEntidade||'.ferias WHERE cod_ferias = (SELECT MAX(cod_ferias) FROM pessoal'||stEntidade||'.ferias)';
                        EXECUTE stSql;
                        inCountFerias := 1;
                    END IF;
                END LOOP;
            END IF;
        
            -- COM A SITUAÇÃO DAS FÉRIAS A VENCER E NA CAUSA DE RESCISÃO ESTIVER HABILITADA A OPÇÃO PAGA_FERIAS_PROPORCIONAL
            -- O SISTEMA DEVERÁ CONCEDER AS FÉRIAS VENCIDAS E GERAR REGISTRO DE EVENTO DE RESCISÃO
            IF(stSituacaFerias = 'A') THEN
                stSituacaFerias := criarBufferTexto('stSituacaFerias','P');
                IF(boPagaFeriasProporcional = 't') THEN
                    boRetorno := concederFeriasAutomatico(inCodContrato, inCodPeriodoMovimentacao,'A');
                    boRetorno := geraRegistroFerias(inCodContrato,inCodPeriodoMovimentacao,stExercicioAtual,stEntidade);
                END IF;
            END IF;
        
            -- COM A OPÇÃO PAGAR AVISO PRÉVIO
            -- OPÇÃO INDENIZADO 
            IF(stAvisoPrevio IS NOT NULL) THEN
                IF(stAvisoPrevio = 'i') THEN
                  
                    -- CRIA TABELA TEMPORÁRIA QUE SERÁ UTILIZADA NO CÁLCULO DAS MÉDIAS
                    CREATE TEMPORARY TABLE tmp_registro_evento_rescisao 
                               (cod_evento               INTEGER,
                                valor                    NUMERIC(14,2),
                                quantidade               NUMERIC(14,2),
                                cod_periodo_movimentacao INTEGER,
                                natureza                 VARCHAR,
                                fixado                   VARCHAR,
                                unidade_quantitativa     NUMERIC(14,2),
                                lido_de                  VARCHAR
                               );
        
                    -- LEITURA E INSERÇÃO DO REGISTRO DE EVENTOS ATUAL - (PONTO FIXO)
                    stSql := ' INSERT INTO tmp_registro_evento_rescisao 
                                    SELECT
                                          fpre.cod_evento                 as cod_evento
                                         ,COALESCE(fpre.valor,0.00)       as valor
                                         ,COALESCE(fpre.quantidade,0.00)  as quantidade
                                         ,fprepe.cod_periodo_movimentacao as cod_periodo_movimentacao
                                         ,fpe.natureza                 as natureza
                                         ,fpe.fixado                      as fixado
                                         ,fpee.unidade_quantitativa       as unidade_quantitativa
                                         ,''fixo_atual''              as lido_de
            
                                    FROM                                                     
                                          folhapagamento'||stEntidade||'.registro_evento_periodo       as fprepe             
                                    JOIN  folhapagamento'||stEntidade||'.ultimo_registro_evento        as fpure
                                      ON  fprepe.cod_registro = fpure.cod_registro
                              
                                    JOIN  folhapagamento'||stEntidade||'.registro_evento               as fpre                      
                                      ON  fpure.cod_registro = fpre.cod_registro
                                     AND  fpure.timestamp    = fpre.timestamp
            
                                    JOIN  folhapagamento'||stEntidade||'.evento                        as fpe
                                      ON  fpe.cod_evento = fpre.cod_evento
                                     AND  fpe.natureza IN ( ''P'',''D'' )         
                                     AND  fpe.tipo =  ''F''
            
                                    JOIN  (SELECT max_evento.cod_evento, max_evento.timestamp, COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                                      FROM folhapagamento'||stEntidade||'.evento_evento,
                                                           (SELECT cod_evento, max(timestamp) as timestamp
                                                              FROM folhapagamento'||stEntidade||'.evento_evento
                                                          GROUP BY cod_evento) as max_evento
                                                     WHERE max_evento.cod_evento = evento_evento.cod_evento
                                                       AND max_evento.timestamp  = evento_evento.timestamp
                                                     ORDER by evento_evento.cod_evento, evento_evento.timestamp desc) as fpee
                                      ON  fpee.cod_evento = fpe.cod_evento
            
                                    JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                                      ON fpcec.cod_evento = fpee.cod_evento
                                     AND fpcec.timestamp  = fpee.timestamp
                                     AND fpcec.cod_configuracao = 2
                              
                                   JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                                     ON fptecm.cod_configuracao = 2
                                    AND fptecm.timestamp = fpcec.timestamp 
                                    AND fptecm.cod_evento = fpcec.cod_evento
                                    AND fptecm.cod_caso = fpcec.cod_caso
            
                               LEFT JOIN  folhapagamento'||stEntidade||'.registro_evento_parcela       as fprep    
                                      ON  fpre.cod_registro     = fprep.cod_registro           
                                     AND  fpre.timestamp        = fprep.timestamp            
            
            
                                   WHERE  fprepe.cod_periodo_movimentacao = '||incodPeriodoMovimentacao||'
                                     AND  fprepe.cod_registro             = fpre.cod_registro
                                     AND  fprepe.cod_contrato             = '||incodContrato||'
                                     AND  fpre.proporcional               = FALSE
                                ORDER BY  fpre.cod_evento ';
                    EXECUTE stSql;
    
                    -- INSERE INFORMAÇÕES DA FOLHA SALÁRIO NA TABELA TEMPORÁRIO DA COMPETÊNCIA DE ATUAL E AS ULTIMAS 12 COMPETÊNCIAS
                    stSql := ' INSERT INTO tmp_registro_evento_rescisao 
                               SELECT
                                     fpec.cod_evento                 as cod_evento
                                    ,COALESCE(fpec.valor,0.00)       as valor
                                    ,COALESCE(fpec.quantidade,0.00)  as quantidade
                                    ,fprepe.cod_periodo_movimentacao as cod_periodo_movimentacao
                                    ,fpe.natureza                    as natureza
                                    ,fpe.fixado                      as fixado
                                    ,fpee.unidade_quantitativa       as unidade_quantitativa
                                    ,''evento_calculado''            as lido_de
    
                               FROM                                                     
                                     folhapagamento'||stEntidade||'.registro_evento_periodo   as fprepe             
    
                               JOIN folhapagamento'||stEntidade||'.evento_calculado           as fpec
                                 ON  fpec.cod_registro = fprepe.cod_registro
    
                               JOIN  folhapagamento'||stEntidade||'.evento                    as fpe
                                 ON  fpe.cod_evento = fpec.cod_evento
                                AND  fpe.natureza IN ( ''P'',''D'' )         
    
                               JOIN (SELECT max_evento.cod_evento, max_evento.timestamp, COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                                 FROM folhapagamento'||stEntidade||'.evento_evento,
                                                      (SELECT cod_evento, max(timestamp) as timestamp
                                                         FROM folhapagamento'||stEntidade||'.evento_evento
                                                     GROUP BY cod_evento) as max_evento
                                                WHERE max_evento.cod_evento = evento_evento.cod_evento
                                                  AND max_evento.timestamp  = evento_evento.timestamp
                                                ORDER by evento_evento.cod_evento, evento_evento.timestamp desc) as fpee
                                 ON  fpee.cod_evento = fpe.cod_evento
    
                               JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                                 ON fpcec.cod_evento = fpee.cod_evento
                                AND fpcec.timestamp  = fpee.timestamp
                                AND fpcec.cod_configuracao = 2
                         
                               JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                                 ON fptecm.timestamp = fpcec.timestamp 
                                AND fptecm.cod_evento = fpcec.cod_evento
                                AND fptecm.cod_caso = fpcec.cod_caso
                                AND fptecm.cod_configuracao = 2
    
                               JOIN (SELECT  cod_periodo_movimentacao
                                             ,max(timestamp) as timestamp 
                                       FROM  folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                                   GROUP BY cod_periodo_movimentacao) as fppms
                                 ON fppms.cod_periodo_movimentacao = fprepe.cod_periodo_movimentacao
                                AND (SELECT situacao 
                                       FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as fppms_
                                      WHERE fppms_.cod_periodo_movimentacao = fppms.cod_periodo_movimentacao
                                        AND fppms_.timestamp = fppms.timestamp) = ''f''
    
                              WHERE  fprepe.cod_periodo_movimentacao 
                            BETWEEN '||inCodPeriodoMovimentacaoInicial||' 
                                AND '||inCodPeriodoMovimentacao||'
                                
                                AND  fprepe.cod_registro = fpec.cod_registro
                                AND  fprepe.cod_contrato = '||incodContrato||'
    
                                AND  fpec.valor > 0 
                           ORDER BY  fpec.cod_evento';
    
                    EXECUTE stSql;

                    -- INSERE INFORMAÇÕES DA FOLHA COMPLEMENTAR NA TABELA TEMPORÁRIO DA COMPETÊNCIA DE JANEIRO ATÉ A COMPETÊNCIA DE CALCULO
                    stSql := ' INSERT INTO tmp_registro_evento_rescisao
                                   SELECT
                                     fpecc.cod_evento                 as cod_evento
                                    ,COALESCE(fpecc.valor,0.00)       as valor
                                    ,COALESCE(fpecc.quantidade,0.00)  as quantidade
                                    ,fprec.cod_periodo_movimentacao   as cod_periodo_movimentacao
                                    ,fpe.natureza                     as natureza
                                    ,fpe.fixado                       as fixado
                                    ,fpee.unidade_quantitativa        as unidade_quantitativa
                                    ,''evento_calculado_complementar''        as lido_de
    
                               FROM                                                     
                                     folhapagamento'||stEntidade||'.registro_evento_complementar   as fprec             
    
                               JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado     as fpecc
                                 ON  fpecc.cod_registro = fprec.cod_registro
    
                               JOIN  folhapagamento'||stEntidade||'.evento                    as fpe
                                 ON  fpe.cod_evento = fpecc.cod_evento
                                AND  fpe.natureza IN ( ''P'',''D'' )         
    
                               JOIN (SELECT max_evento.cod_evento, max_evento.timestamp, COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                       FROM folhapagamento'||stEntidade||'.evento_evento,
                                            (SELECT cod_evento, max(timestamp) as timestamp
                                               FROM folhapagamento'||stEntidade||'.evento_evento
                                           GROUP BY cod_evento) as max_evento
                                      WHERE max_evento.cod_evento = evento_evento.cod_evento
                                        AND max_evento.timestamp  = evento_evento.timestamp
                                      ORDER by evento_evento.cod_evento, evento_evento.timestamp desc) as fpee
                                 ON  fpee.cod_evento = fpe.cod_evento
    
                               JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                                 ON fpcec.cod_evento = fpee.cod_evento
                                AND fpcec.timestamp  = fpee.timestamp
                                AND fpcec.cod_configuracao = 2
                         
                              JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                                ON fptecm.cod_configuracao = 2
                               AND fptecm.timestamp = fpcec.timestamp 
                               AND fptecm.cod_evento = fpcec.cod_evento
                               AND fptecm.cod_caso = fpcec.cod_caso
    
                               JOIN ( SELECT cod_periodo_movimentacao
                                             ,max(timestamp) as timestamp 
                                        FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                                    GROUP BY 1) as fppms
                                 ON fppms.cod_periodo_movimentacao = fprec.cod_periodo_movimentacao
                                AND ( SELECT situacao 
                                        FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as fppms_
                                       WHERE fppms_.cod_periodo_movimentacao = fppms.cod_periodo_movimentacao
                                         AND fppms_.timestamp = fppms.timestamp ) = ''f''
                              WHERE  fprec.cod_periodo_movimentacao 
                            BETWEEN '||inCodPeriodoMovimentacaoInicial||' 
                                AND '||inCodPeriodoMovimentacao ||'
                                AND  fprec.cod_registro             = fpecc.cod_registro
                                AND  fprec.cod_contrato             = '||incodContrato||'
                                AND  fpecc.valor > 0 
                           ORDER BY  fpecc.cod_evento';
                    EXECUTE stSql;
    
                    stSql := 'SELECT count(cod_evento) FROM tmp_registro_evento_rescisao';
        
                    OPEN crCursor FOR EXECUTE stSql;
                        FETCH crCursor INTO inNrRegistros ;
                    CLOSE crCursor;
                    
                    IF inNrRegistros > 0 THEN
        
                        -- CRIA TABELA TEMPORÁRIA ONDE SERÃO AGRUPADOS OS EVENTOS PARA O CÁLCULO DA MÉDIAS
                        CREATE TEMPORARY TABLE tmp_registro_evento_rescisao_medias 
                              (cod_evento               INTEGER,
                               codigo                   VARCHAR,
                               descricao                VARCHAR,
                               unidade_quantitativa     NUMERIC(14,2),
                               fixado                   VARCHAR,
                               formula                  VARCHAR,
                               valor                    NUMERIC(14,2),
                               quantidade               NUMERIC(14,2),
                               avos                     INTEGER,
                               nr_ocorrencias           INTEGER
                              );
        
                        stSql := ' INSERT INTO tmp_registro_evento_rescisao_medias 
                              SELECT  distinct tmp_registro_evento_rescisao.cod_evento as cod_evento
                                     , fpe.codigo                                    as codigo
                                     , fpe.descricao                                 as descricao
                                     , COALESCE(fpee.unidade_quantitativa,0)         as unidade_quantitativa
                                     , fpe.fixado                                    as fixado
                                     , ''0.0.0''                                     as formula
                                     , 0.00                                          as valor
                                     , 0.00                                          as quantidade
                                     , 0                                             as avos
                                     , 0                                             as nr_ocorrencias
                                FROM tmp_registro_evento_rescisao
        
                                LEFT OUTER JOIN folhapagamento'||stEntidade||'.evento  as fpe 
                                  ON fpe.cod_evento = tmp_registro_evento_rescisao.cod_evento
        
                                LEFT OUTER JOIN( SELECT max_evento.cod_evento, 
                                                        max_evento.timestamp, 
                                                        COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                                   FROM folhapagamento'||stEntidade||'.evento_evento,
                                                        (SELECT cod_evento, max(timestamp) as timestamp
                                                           FROM folhapagamento'||stEntidade||'.evento_evento
                                                       GROUP BY cod_evento) as max_evento
                                                  WHERE max_evento.cod_evento = evento_evento.cod_evento
                                                    AND max_evento.timestamp  = evento_evento.timestamp
                                                  ORDER BY evento_evento.cod_evento, evento_evento.timestamp desc) as fpee
                                  ON fpee.cod_evento = fpe.cod_evento
                            ORDER BY tmp_registro_evento_rescisao.cod_evento';
        
                        EXECUTE stSql;
        
                        stSql := 'SELECT COUNT(cod_evento) 
                                     FROM tmp_registro_evento_rescisao_medias
                                    WHERE formula IS NOT NULL';
                                   
                        OPEN crCursor FOR EXECUTE stSql;
                             FETCH crCursor INTO inNrRegistros;
                        CLOSE crCursor;
        
                        --SE POSSUI MAIS DE UM REGISTRO CONTINUA PROCESSO
                        IF inNrRegistros IS NOT NULL THEN
                            stSql := 'SELECT * FROM tmp_registro_evento_rescisao_medias
                                      WHERE formula is not null  ';
                            -- SE EXISTE AO MENOS UM REGISTRO NA LISTA COM FÓRMULA PARA CALCULO
                            FOR reRegistro1 IN  EXECUTE stSql LOOP
        
                                inCodEvento := reRegistro1.cod_evento;
                                inCodEvento := criarBufferInteiro( 'incodevento', reRegistro1.cod_evento );
        
                                -- BUSCA A FORMULA DE MEDIA PARA O EVENTO - FOI UTILIZADA A MESMA FORMULA PASSANDO A CONFIGURAÇÃO DO DÉCIMO 
                                stFormula := pegaFormulaMediaFerias(inCodEvento,4,inCodSubDivisao,inCodFuncao,inCodEspecialidade);
                                -- executa formula
                             
                                --   stFormula := '123'; 
                                IF stFormula IS NOT NULL THEN
                                    stExecutaFormula := executaGCNumerico( stFormula );
                                    nuExecutaFormula := to_number( stExecutaFormula , '99999999999.99' );
                                    --nuExecutaFormula := '200.00';
                                    IF nuExecutaFormula != 0 THEN 
                                        IF reRegistro1.fixado = 'V' THEN
                                            nuValor      := arredondar(nuExecutaFormula,2);
                                            nuQuantidade := 0;
                                        ELSE
                                            nuQuantidade := arredondar(nuExecutaFormula,2);
                                            nuValor      := 0;
                                        END IF;
            
                                        -- INSERI REGISTRO DE EVENTO DE RESCISAO  - AVISO PRÉVIO
                                        stDesdobramento := 'A';
                                        boGravouRegistro := gravaRegistroEventoRescisao( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento );
                                    END IF;
                                END IF; --- FIM (stFormula IS NOT NULL)
                            --DROP TABLE tmp#inCodEvento ;
                            END LOOP;
                        END IF; --- FIM (inNrRegistros IS NOT NULL) 
                    END IF; --- FIM (inNrRegistros > 0)
                END IF; --- FIM (stAvisoPrevio = 'i')
            END IF; --- FIM (stAvisoPrevio IS NOT NULL)
        boRetorno := deletarTemporariasDoCalculo(true); 
        END IF; --- FIM (inCodContratoPensionista IS NULL)
    END IF; --- FIM (inCodPeriodoMovimentacaoAberta = inCodPeriodoMovimentacao)
    RETURN TRUE; 
END;
$$LANGUAGE 'plpgsql';

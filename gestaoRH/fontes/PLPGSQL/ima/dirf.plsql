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
--    * PLPGSQL para retorno dos dados da dirf
--    * Data de Criação: 10/12/2007
--    
--
--    * @author Diego Lemos de Souza
--       
--    * Casos de uso: uc-04.08.15   
--           
--    $Id: dirf.sql 31697 2008-08-04 19:33:31Z souzadl $
--*/

--
DROP TYPE colunasDirf CASCADE;
CREATE TYPE colunasDirf AS (
    nome_beneficiario   VARCHAR(200),
    beneficiario        VARCHAR,
    uso_declarante      INTEGER,
    jan1         NUMERIC,
    jan2         NUMERIC,
    jan3         NUMERIC,
    fev1         NUMERIC,
    fev2         NUMERIC,
    fev3         NUMERIC,
    mar1         NUMERIC,
    mar2         NUMERIC,
    mar3         NUMERIC,
    abr1         NUMERIC,
    abr2         NUMERIC,
    abr3         NUMERIC,
    mai1         NUMERIC,
    mai2         NUMERIC,
    mai3         NUMERIC,
    jun1         NUMERIC,
    jun2         NUMERIC,
    jun3         NUMERIC,
    jul1         NUMERIC,
    jul2         NUMERIC,
    jul3         NUMERIC,
    ago1         NUMERIC,
    ago2         NUMERIC,
    ago3         NUMERIC,
    set1         NUMERIC,
    set2         NUMERIC,
    set3         NUMERIC,
    out1         NUMERIC,
    out2         NUMERIC,
    out3         NUMERIC,
    nov1         NUMERIC,
    nov2         NUMERIC,
    nov3         NUMERIC,
    dez1         NUMERIC,
    dez2         NUMERIC,
    dez3         NUMERIC,
    dec1         NUMERIC,
    dec2         NUMERIC,
    dec3         NUMERIC
);

CREATE OR REPLACE FUNCTION recupera_evento_calculado_dirf_servidor(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS NUMERIC AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inExercicio                 ALIAS FOR $2;
    inNumCGM                    ALIAS FOR $3;
    inCodEvento                 ALIAS FOR $4;
    stEntidade               ALIAS FOR $5;
    stSelect                    VARCHAR;
    nuRetorno                   NUMERIC;
BEGIN
    IF inCodConfiguracao = 0 THEN
        stSelect := 'SELECT sum(evento_complementar_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                           , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                           , (SELECT servidor.numcgm
                                   , servidor_contrato_servidor.cod_contrato
                                FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                   , pessoal'|| stEntidade ||'.servidor
                               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               UNION  
                              SELECT pensionista.numcgm
                                   , contrato_pensionista.cod_contrato
                                FROM pessoal'|| stEntidade ||'.pensionista
                                   , pessoal'|| stEntidade ||'.contrato_pensionista
                               WHERE pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                                 AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente) as servidor_pensionista
                       WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                         AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                         AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                         AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                         AND registro_evento_complementar.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_complementar.cod_contrato = servidor_pensionista.cod_contrato
                         AND servidor_pensionista.numcgm = '|| inNumCGM ||'
                         AND to_char(periodo_movimentacao.dt_final,''yyyy'') = '|| quote_literal(inExercicio) ||'
                         AND evento_complementar_calculado.cod_evento = '|| inCodEvento;
    END IF;
    IF inCodConfiguracao = 1 THEN
        stSelect := 'SELECT sum(evento_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            , folhapagamento'|| stEntidade ||'.evento_calculado
                            , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                           , (SELECT servidor.numcgm
                                   , servidor_contrato_servidor.cod_contrato
                                FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                   , pessoal'|| stEntidade ||'.servidor
                               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               UNION  
                              SELECT pensionista.numcgm
                                   , contrato_pensionista.cod_contrato
                                FROM pessoal'|| stEntidade ||'.pensionista
                                   , pessoal'|| stEntidade ||'.contrato_pensionista
                               WHERE pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                                 AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente) as servidor_pensionista                            
                        WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                        AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                        AND registro_evento_periodo.cod_contrato = servidor_pensionista.cod_contrato
                        AND servidor_pensionista.numcgm = '|| inNumCGM ||'
                        AND to_char(periodo_movimentacao.dt_final,''yyyy'') = '|| quote_literal(inExercicio) ||'
                        AND evento_calculado.cod_evento = '|| inCodEvento; 
    END IF;
    IF inCodConfiguracao = 2 THEN
        stSelect := 'SELECT sum(evento_ferias_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                           , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                           , (SELECT servidor.numcgm
                                   , servidor_contrato_servidor.cod_contrato
                                FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                   , pessoal'|| stEntidade ||'.servidor
                               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               UNION  
                              SELECT pensionista.numcgm
                                   , contrato_pensionista.cod_contrato
                                FROM pessoal'|| stEntidade ||'.pensionista
                                   , pessoal'|| stEntidade ||'.contrato_pensionista
                               WHERE pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                                 AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente) as servidor_pensionista                                                       
                       WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                         AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                         AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                         AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                         AND registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_ferias.cod_contrato = servidor_pensionista.cod_contrato
                         AND servidor_pensionista.numcgm = '|| inNumCGM ||'
                         AND to_char(periodo_movimentacao.dt_final,''yyyy'') = '|| quote_literal(inExercicio) ||'
                         AND evento_ferias_calculado.cod_evento = '|| inCodEvento;                                         
    END IF;
    IF inCodConfiguracao = 3 THEN
        stSelect := 'SELECT sum(evento_decimo_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                           , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                           , (SELECT servidor.numcgm
                                   , servidor_contrato_servidor.cod_contrato
                                FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                   , pessoal'|| stEntidade ||'.servidor
                               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               UNION  
                              SELECT pensionista.numcgm
                                   , contrato_pensionista.cod_contrato
                                FROM pessoal'|| stEntidade ||'.pensionista
                                   , pessoal'|| stEntidade ||'.contrato_pensionista
                               WHERE pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                                 AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente) as servidor_pensionista                                                                                  
                       WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                         AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                         AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                         AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                         AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_decimo.cod_contrato = servidor_pensionista.cod_contrato
                         AND servidor_pensionista.numcgm = '|| inNumCGM ||'
                         AND to_char(periodo_movimentacao.dt_final,''yyyy'') = '|| quote_literal(inExercicio) ||'
                         AND evento_decimo_calculado.cod_evento = '|| inCodEvento;
    END IF;
    IF inCodConfiguracao = 4 THEN
        stSelect := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                           , (SELECT servidor.numcgm
                                   , servidor_contrato_servidor.cod_contrato
                                FROM pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                   , pessoal'|| stEntidade ||'.servidor
                               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               UNION  
                              SELECT pensionista.numcgm
                                   , contrato_pensionista.cod_contrato
                                FROM pessoal'|| stEntidade ||'.pensionista
                                   , pessoal'|| stEntidade ||'.contrato_pensionista
                               WHERE pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                                 AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente) as servidor_pensionista                           
                       WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_rescisao.cod_contrato = servidor_pensionista.cod_contrato
                         AND servidor_pensionista.numcgm = '|| inNumCGM ||'
                         AND to_char(periodo_movimentacao.dt_final,''yyyy'') = '|| quote_literal(inExercicio) ||'
                         AND evento_rescisao_calculado.cod_evento = '|| inCodEvento ||'
                         AND evento_rescisao_calculado.desdobramento != ''D'' ';                
    END IF;  
      
    nuRetorno := selectIntoNumeric(stSelect);
    IF nuRetorno IS NOT NULL THEN
        RETURN nuRetorno;
    ELSE
        RETURN 0.00;
    END IF;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION recupera_evento_calculado_dirf(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS NUMERIC AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodContrato               ALIAS FOR $3;
    inCodEvento                 ALIAS FOR $4;
    stEntidade               ALIAS FOR $5;
    stSelect                    VARCHAR;
    nuRetorno                   NUMERIC;
BEGIN
    IF inCodConfiguracao = 0 THEN
        stSelect := 'SELECT sum(evento_complementar_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                           , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                         AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                         AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                         AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                         AND registro_evento_complementar.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_complementar.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         AND evento_complementar_calculado.cod_evento = '|| inCodEvento;
    END IF;
    IF inCodConfiguracao = 1 THEN
        stSelect := 'SELECT sum(evento_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            , folhapagamento'|| stEntidade ||'.evento_calculado
                            , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                        WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                        AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                        AND registro_evento_periodo.cod_contrato = '|| inCodContrato ||'
                        AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND evento_calculado.cod_evento = '|| inCodEvento; 
    END IF;
    IF inCodConfiguracao = 2 THEN
        stSelect := 'SELECT sum(evento_ferias_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                           , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                         AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                         AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                         AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                         AND registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_ferias.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         AND evento_ferias_calculado.cod_evento = '|| inCodEvento;                                         
    END IF;
    IF inCodConfiguracao = 3 THEN
        stSelect := 'SELECT sum(evento_decimo_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                           , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                         AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                         AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                         AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                         AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_decimo.cod_contrato = '|| inCodContrato ||'
                         AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                         AND evento_decimo_calculado.cod_evento = '|| inCodEvento;
    END IF;
    IF inCodConfiguracao = 4 THEN
                
    END IF;  
    nuRetorno := selectIntoNumeric(stSelect);
    IF nuRetorno IS NOT NULL THEN
        RETURN nuRetorno;
    ELSE
        RETURN 0.00;
    END IF;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION dirf(VARCHAR,INTEGER,VARCHAR,VARCHAR,INTEGER) RETURNS SETOF colunasDirf AS $$ 
DECLARE
    stEntidade                      ALIAS FOR $1;
    inExercicio                     ALIAS FOR $2;    
    stTipoFiltro                    ALIAS FOR $3;
    stCodigos                       ALIAS FOR $4;
    inSequenciaEventos              ALIAS FOR $5;
    stSelect                        VARCHAR;
    stCodEventosColuna1             VARCHAR;
    reRegistro                      RECORD;
    rePeriodos                      RECORD;
    reEventos                       RECORD;
    reCodTipo                       RECORD;
    inSequencia                     INTEGER:=2;
    inIndex                         INTEGER:=1;
    inCodEventoIRRFDesconto         INTEGER;
    inCodEventoIRRFDeducaoSPensao   INTEGER;
    inCodEventoIRRFDeducaoCPensao   INTEGER;
    inCodEventoIRRFDescontoCPensao  INTEGER;
    inCodEventoIRRFBase             INTEGER;
    inCodEventoPrevidenciaOficial   INTEGER := 0;
    inCodEventoPrevidenciaPrivada   INTEGER := 0;
    inCodEventoInfDeducaoDependente INTEGER;
    inCodEventoPensao               INTEGER;
    inCodEventoDecimo               INTEGER;
    inCodServidor                   INTEGER;
    inCodEventoColuna1              INTEGER;
    inCodEventoColuna2              INTEGER;
    inCodEventoColuna3              INTEGER;
    inCodTipo                       INTEGER;
    stCGMAnterior                   INTEGER;
    nuSomaBaseIRRF                  NUMERIC:=0;
    nuColuna1                       NUMERIC:=0;
    nuSomaColuna2                   NUMERIC:=0;
    nuSomaColuna3                   NUMERIC:=0;
    nuSomaDescontoIRRF              NUMERIC:=0;
    nuTemp                          NUMERIC:=0;
    nuValor                         NUMERIC:=0;
    nuValorRendimentosDecimo        NUMERIC:=0;
    nuValorRetidoDecimo             NUMERIC:=0;
    nuValorDeducoesDecimo           NUMERIC:=0;
    nuValorDecimo                   NUMERIC:=0;
    stValor                         VARCHAR:='';
    arValores1                      VARCHAR[];
    arValores2                      VARCHAR[];
    arValores3                      VARCHAR[];
    rwDirf                          colunasDirf%ROWTYPE;
    stCPFAnterior                   VARCHAR;
    boSomaDeducaoDecimo             BOOLEAN;
BEGIN

    --O parametro inSequenciaEventos identifica qual sequencia de eventos será buscada 
    --para ser inserida nos registros do tipo 2
    --Tempos tres sequencia possíveis
    --Sequencia 1: Rendimento Tributável, Dedução e IRRF
    --Sequencia 2: Previdencia Oficial, Dependentes e Pensão Alimentícia
    --Sequencia 3: Previdencia Privada e ao FAPI
    
    stSelect := 'SELECT tabela_irrf_evento.*
                        , trim(evento.descricao) as descricao
                        , evento.codigo
                    FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                        , (SELECT cod_tabela
                                , max(timestamp) as timestamp
                            FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                            GROUP BY cod_tabela) as max_tabela_irrf_evento
                        , folhapagamento'|| stEntidade ||'.evento
                    WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                    AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                    AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp';
    FOR reRegistro IN EXECUTE stSelect LOOP
        IF reRegistro.cod_tipo = 1 THEN
            inCodEventoInfDeducaoDependente := reRegistro.cod_evento;
        END IF;    
        IF reRegistro.cod_tipo = 3 THEN
            inCodEventoIRRFDesconto := reRegistro.cod_evento;
        END IF;
        IF reRegistro.cod_tipo = 4 THEN
            inCodEventoIRRFDeducaoSPensao := reRegistro.cod_evento;
        END IF;
        IF reRegistro.cod_tipo = 5 THEN
            inCodEventoIRRFDeducaoCPensao := reRegistro.cod_evento;
        END IF;
        IF reRegistro.cod_tipo = 6 THEN
            inCodEventoIRRFDescontoCPensao := reRegistro.cod_evento;
        END IF;        
        IF reRegistro.cod_tipo = 7 THEN
            inCodEventoIRRFBase := reRegistro.cod_evento;
        END IF;        
    END LOOP;
        
    --stSelect := 'SELECT previdencia_evento.cod_evento
    --                FROM folhapagamento'|| stEntidade ||'.previdencia_evento
    --                    , folhapagamento'|| stEntidade ||'.previdencia_previdencia
    --                    , (  SELECT cod_previdencia
    --                                , max(timestamp) as timestamp                                                        COMENTADO PORQUE ESTÁ ERRADO, CORRIGIDO LOGO ABAIXO,
    --                            FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia                              DEIXADO AQUI A NÍVEL DE ENTENDIMENTO ATÉ SER FEITA 
    --                        GROUP BY cod_previdencia) as max_previdencia_previdencia                                     UMA PL DECENTE.
    --                WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia        
    --                    AND previdencia_evento.timestamp = previdencia_previdencia.timestamp
    --                    AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia        
    --                    AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
    --                    AND previdencia_previdencia.tipo_previdencia = ''o''
    --                    AND previdencia_evento.cod_tipo = 1';
    --inCodEventoPrevidenciaOficial := selectIntoInteger(stSelect);

    stSelect := 'SELECT pensao_evento.cod_evento
                       FROM folhapagamento'|| stEntidade ||'.pensao_evento
                           , (  SELECT cod_configuracao_pensao
                                     , max(timestamp) as timestamp
                                  FROM folhapagamento'|| stEntidade ||'.pensao_evento
                              GROUP BY cod_configuracao_pensao) as max_pensao_evento
                       WHERE pensao_evento.cod_configuracao_pensao = max_pensao_evento.cod_configuracao_pensao
                         AND pensao_evento.timestamp = max_pensao_evento.timestamp';
    inCodEventoPensao := selectIntoInteger(stSelect);
    IF inCodEventoPensao IS NULL THEN
        inCodEventoPensao := 0;
    END IF;

    stSelect := 'SELECT previdencia_evento.*
                       FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                           , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                           , (  SELECT cod_previdencia
                                     , max(timestamp) as timestamp
                                  FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                              GROUP BY cod_previdencia) as max_previdencia_previdencia
                       WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia        
                         AND previdencia_evento.timestamp = previdencia_previdencia.timestamp
                         AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia        
                         AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                         AND previdencia_previdencia.tipo_previdencia = ''p''
                         AND previdencia_evento.cod_tipo = 1';
    inCodEventoPrevidenciaPrivada := selectIntoInteger(stSelect);
    IF inCodEventoPrevidenciaPrivada IS NULL THEN
        inCodEventoPrevidenciaPrivada := 0;
    END IF;
    
    stSelect := '    SELECT cadastro.cod_contrato
                          , cadastro.registro                                                                                                
                          , sem_acentos(cadastro.nom_cgm) as nom_cgm
                          , cadastro.numcgm                                                                           
                          , cadastro.cpf
                       FROM (
                 	            SELECT cod_contrato, registro, nom_cgm, numcgm, cpf FROM recuperarContratoServidor(''cgm'','|| quote_literal(stEntidade) ||',0,'|| quote_literal(stTipoFiltro) ||','|| quote_literal(stCodigos) ||','|| quote_literal(inExercicio) ||')
                 	            UNION
                 	            SELECT cod_contrato, registro, nom_cgm, numcgm, cpf FROM recuperarContratoPensionista(''cgm'','|| quote_literal(stEntidade) ||',0,'|| quote_literal(stTipoFiltro) ||','|| quote_literal(stCodigos) ||','|| quote_literal(inExercicio) ||')
             	            ) as cadastro
							
                      WHERE NOT EXISTS (SELECT 1
                                          FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                         WHERE contrato_servidor_caso_causa.cod_contrato = cadastro.cod_contrato
                                           AND to_char(dt_rescisao,''yyyy'') < '|| quote_literal(inExercicio) ||')
										   
                        AND NOT EXISTS (SELECT 1
                                          FROM pessoal'|| stEntidade ||'.aposentadoria_excluida
                                         WHERE aposentadoria_excluida.cod_contrato = cadastro.cod_contrato)
                        
						AND NOT EXISTS (SELECT 1
                                          FROM pessoal'|| stEntidade ||'.aposentadoria_encerramento
                                         WHERE aposentadoria_encerramento.cod_contrato = cadastro.cod_contrato)                      
										 
                        AND NOT EXISTS (SELECT 1
                                          FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                         WHERE contrato_pensionista.cod_contrato = cadastro.cod_contrato
                                           AND contrato_pensionista.dt_encerramento IS NULL)';
                                  
    stSelect := stSelect  ||' ORDER BY numcgm , cod_contrato ';

-- o select acima traz os servidores com cod_contrato, registro, etc...
    
-- para cada servidor encontrado na consulta anterior faça a select abaixo    
    FOR reRegistro IN EXECUTE stSelect LOOP
        stSelect := 'SELECT * 
                       FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao 
                      WHERE to_char(dt_final,''yyyy'') = '|| quote_literal(inExercicio) ||'
                      ORDER BY dt_final';
        nuValor         := 0;
        nuSomaBaseIRRF  := 0;
        nuSomaDescontoIRRF := 0;
        nuSomaColuna2   := 0;
        nuSomaColuna3   := 0;
        inIndex         := 1;
        arValores1      := null;
        arValores2      := null;
        arValores3      := null;
        nuValorRendimentosDecimo := 0;
        nuValorDeducoesDecimo    := 0; 
        nuValorRetidoDecimo      := 0; 

        -- flag para controlar o pagto de decimo entre mais de um contrato
         IF  reRegistro.numcgm != stCGMAnterior THEN
	     boSomaDeducaoDecimo := TRUE;
	 END IF;
	      
        
        -- Estrutura do arquivo 
        
            --inSequenciaEventos 1
            --  arValores1: Rendimento Tributável
            --  arValores2: Deduções
            --  arValores3: IRRF
            --inSequenciaEventos 2 
            --  arValores1: Previdencia Oficial
            --  arValores2: Dependentes
            --  arValores3: Pensão Alimentícia
            --inSequenciaEventos 3 
            --  arValores1: Previdencia Privada
            --  arValores2: FAPI
            --inIndex identifica o período de movimentação|        
            
        FOR rePeriodos IN EXECUTE stSelect LOOP
            nuColuna1 := 0;            
            --###########Início Base de IRRF   
            --Salário         
            nuTemp := recupera_evento_calculado_dirf(1,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFBase,stEntidade);           
            IF nuTemp IS NOT NULL THEN
                --nuSomaBaseIRRF := nuSomaBaseIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuColuna1 := nuColuna1 + nuTemp;
                END IF;
            END IF;           
                                        
            --Complementar
            nuTemp := recupera_evento_calculado_dirf(0,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFBase,stEntidade);                         
            IF nuTemp IS NOT NULL THEN
                --nuSomaBaseIRRF := nuSomaBaseIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuColuna1 := nuColuna1 + nuTemp;
                END IF;                
            END IF;

            --Férias
            nuTemp := recupera_evento_calculado_dirf(2,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFBase,stEntidade);                         
            IF nuTemp IS NOT NULL THEN
                --nuSomaBaseIRRF := nuSomaBaseIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuColuna1 := nuColuna1 + nuTemp;
                END IF;                
            END IF;
                                     
            stSelect := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                         AND evento_rescisao_calculado.cod_evento = '|| inCodEventoIRRFBase ||'
                         AND evento_rescisao_calculado.desdobramento != ''D''';
            nuTemp := selectIntoNumeric(stSelect);                                               
            IF nuTemp IS NOT NULL THEN           
                --nuSomaBaseIRRF := nuSomaBaseIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuColuna1 := nuColuna1 + nuTemp;
                END IF;                
            END IF;
            --###########Fim Base de IRRF            

            ---############ BUSCANDO COD_EVENTO DA PREVIDENCIA POR CONTRATO

            stSelect := ' SELECT previdencia_evento.cod_evento
                            FROM   folhapagamento'|| stEntidade ||'.previdencia
                                 , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                 , folhapagamento'|| stEntidade ||'.previdencia_evento
                                 , (SELECT   contrato_servidor_previdencia.cod_contrato
                                           , contrato_servidor_previdencia.cod_previdencia
                                      FROM   pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                           , (  SELECT max(timestamp) as timestamp
                                                       , cod_contrato
                                                  FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                              GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                      WHERE     contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                            AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                                            AND contrato_servidor_previdencia.bo_excluido = false
                                      UNION
                                     SELECT   contrato_pensionista_previdencia.cod_contrato
                                            , contrato_pensionista_previdencia.cod_previdencia
                                       FROM   pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                            , (  SELECT   max(timestamp) as timestamp
                                                        , cod_contrato
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                              GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                       WHERE      contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                              AND contrato_pensionista_previdencia.timestamp    = max_contrato_pensionista_previdencia.timestamp) as servidor_pensionista_previdencia
                           WHERE servidor_pensionista_previdencia.cod_previdencia = previdencia.cod_previdencia
                             AND previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                             AND previdencia_previdencia.tipo_previdencia= ''o''                
                             AND servidor_pensionista_previdencia.cod_contrato = '|| reRegistro.cod_contrato ||'
                             AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                             AND previdencia_evento.cod_tipo = 1 LIMIT 1';


            inCodEventoPrevidenciaOficial := selectIntoInteger(stSelect);   

            IF inCodEventoPrevidenciaOficial IS NULL THEN
                inCodEventoPrevidenciaOficial := 0;
            END IF;

            --###########Início Previdência Oficial
            IF inSequenciaEventos = 2 THEN
                --Salário         
                nuTemp := recupera_evento_calculado_dirf(1,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoPrevidenciaOficial,stEntidade);                       
                IF nuTemp IS NOT NULL THEN
                    IF inSequenciaEventos = 2 THEN
                        nuColuna1 := nuColuna1 + nuTemp;
                    END IF;                                
                END IF;           
                                            
                --Complementar
                nuTemp := recupera_evento_calculado_dirf(0,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoPrevidenciaOficial,stEntidade);                       
                IF nuTemp IS NOT NULL THEN
                    IF inSequenciaEventos = 2 THEN
                        nuColuna1 := nuColuna1 + nuTemp;
                    END IF;                                  
                END IF;
                
                --Férias
                nuTemp := recupera_evento_calculado_dirf(2,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoPrevidenciaOficial,stEntidade);                       
                IF nuTemp IS NOT NULL THEN
                    IF inSequenciaEventos = 2 THEN
                        nuColuna1 := nuColuna1 + nuTemp;
                    END IF;                                  
                END IF;       
                                
                stSelect := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                            , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                            , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                        WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                            AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                            AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                            AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                            AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                            AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                            AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                            AND evento_rescisao_calculado.cod_evento = '|| inCodEventoPrevidenciaOficial ||'
                            AND evento_rescisao_calculado.desdobramento != ''D'''; 
                nuTemp := selectIntoNumeric(stSelect);                                               
                IF nuTemp IS NOT NULL THEN           
                    IF inSequenciaEventos = 2 THEN
                        nuColuna1 := nuColuna1 + nuTemp;
                    END IF;                                  
                END IF;
            END IF;                      
            arValores1[inIndex] := nuColuna1;
            
            
            IF inSequenciaEventos = 1 THEN
                inCodEventoDecimo := inCodEventoIRRFBase;
            END IF;
            IF inSequenciaEventos = 2 THEN
                inCodEventoDecimo := inCodEventoPrevidenciaOficial;
            END IF;
            IF inSequenciaEventos = 3 THEN
                inCodEventoDecimo := inCodEventoPrevidenciaPrivada;
            END IF;                        
            
            --###########Início Décimo            
            nuTemp := selectIntoNumeric('SELECT sum(evento_rescisao_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                         AND evento_rescisao_calculado.cod_evento = '|| inCodEventoDecimo ||'
                         AND evento_rescisao_calculado.desdobramento = ''D'''); 
            IF nuTemp IS NOT NULL THEN
                nuValorRendimentosDecimo := nuValorRendimentosDecimo + nuTemp;
            END IF;    
                         
            --Décimo
            nuTemp := recupera_evento_calculado_dirf(3,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoDecimo,stEntidade);
          
            IF nuTemp IS NOT NULL THEN
                nuValorRendimentosDecimo := nuValorRendimentosDecimo + nuTemp;
            END IF;    

            IF inSequenciaEventos = 1 THEN
                IF (nuValorRendimentosDecimo IS NULL) THEN
                    nuValorRendimentosDecimo := 0;
                END IF;

                INSERT INTO tmp_valores_decimo VALUES (reRegistro.cod_contrato, nuValorRendimentosDecimo);
            ELSEIF (inSequenciaEventos = 2 OR inSequenciaEventos = 3) THEN
                nuValorDecimo := selectIntoNumeric('SELECT COALESCE(SUM(valor), 0.00) FROM tmp_valores_decimo WHERE cod_contrato = '|| reRegistro.cod_contrato);

                IF (nuValorDecimo = 0.00) THEN
                    nuValorRendimentosDecimo := 0;
                END IF;
            END IF;
 
            --###########Fim Décimo
            --###########Fim Previdência Oficial                                  
                          
            
            --Início dependentes
            --apresenta informativos de deducao de dependente somente de uma das matriculas do servidor
            --Se o cod_contrato teve deducao de dependente na competencia na tabela deducao_dependente e deducao_depedente_complementar utiliza-se esse para a dirf
            
            nuValor := 0;           
                                               
                stSelect := 'SELECT COALESCE(cod_tipo) as cod_tipo
                             FROM ( SELECT cod_tipo FROM folhapagamento'|| stEntidade ||'.deducao_dependente 
                                     WHERE cod_periodo_movimentacao =  '|| rePeriodos.cod_periodo_movimentacao ||' 
                                       AND cod_contrato = '|| reRegistro.cod_contrato ||'
                                    UNION 
                                    SELECT cod_tipo FROM folhapagamento'|| stEntidade ||'.deducao_dependente_complementar
                                     WHERE cod_periodo_movimentacao =  '|| rePeriodos.cod_periodo_movimentacao ||' 
                                       AND numcgm = '|| reRegistro.numcgm ||'
                                  ) as deducao_dependente                                                              
                            ';
                --inCodTipo := selectIntoInteger(stSelect); 
                FOR reCodTipo IN EXECUTE stSelect LOOP   
                --IF inCodTipo > 0 THEN          
                    IF inSequenciaEventos = 2 THEN
                        IF reCodTipo.cod_tipo = 2 THEN
                            --Salário         
                            stSelect := 'SELECT evento_calculado.valor as valor
                                            FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                            , folhapagamento'|| stEntidade ||'.evento_calculado
                                            , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                                            AND registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                            AND registro_evento_periodo.cod_contrato = '|| reRegistro.cod_contrato ||'
                                            AND registro_evento_periodo.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                                            AND evento_calculado.cod_evento = '|| inCodEventoInfDeducaoDependente ||'
                                            LIMIT 1';
                            nuTemp := selectIntoNumeric(stSelect);
                            IF nuTemp IS NOT NULL AND nuValor = 0 THEN
                               nuValor := nuValor + nuTemp;
                            END IF;                           
                        END IF;                    
                        -- verifica se nao possuir nuVAlor quer dizer que nao houve deducao de dependente na folha salario
                        IF reCodTipo.cod_tipo = 3 AND nuValor = 0 THEN                                  
                            --Complementar         
                            --nuTemp := recupera_evento_calculado_dirf(0,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoInfDeducaoDependente,stEntidade);                       
                            stSelect := 'SELECT evento_complementar_calculado.valor as valor
                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                        , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                        , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                    WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                                        AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                                        AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                                        AND registro_evento_complementar.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                        AND registro_evento_complementar.cod_contrato = '|| reRegistro.cod_contrato ||'
                                        AND registro_evento_complementar.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                                        AND evento_complementar_calculado.cod_evento = '|| inCodEventoInfDeducaoDependente ||'
                                        AND evento_complementar_calculado.cod_configuracao = 1
                                      LIMIT 1';
                            nuTemp := selectIntoNumeric(stSelect);
                            IF nuTemp IS NOT NULL THEN
                                nuValor := nuValor + nuTemp;
                            END IF;                          
                        END IF;                    
                        
                        IF reCodTipo.cod_tipo = 1 THEN
                            --Ferias
                            --nuTemp := recupera_evento_calculado_dirf(2,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoInfDeducaoDependente,stEntidade);                       
                            stSelect := 'SELECT evento_ferias_calculado.valor as valor
                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                        , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                        , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                    WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                        AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                                        AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                        AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                                        AND registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                        AND registro_evento_ferias.cod_contrato = '|| reRegistro.cod_contrato ||'
                                        AND registro_evento_ferias.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                                        AND evento_ferias_calculado.cod_evento = '|| inCodEventoInfDeducaoDependente ||'
                                        AND evento_ferias_calculado.desdobramento = ''F''
                                      LIMIT 1';
                            nuTemp := selectIntoNumeric(stSelect);
                            IF nuTemp IS NOT NULL AND nuValor = 0 THEN
                                nuValor := nuValor + nuTemp;
                            END IF; 
                        END IF;                    
                        
                        IF reCodTipo.cod_tipo = 5 THEN
                            -- rescisao    
                            stSelect := 'SELECT evento_rescisao_calculado.valor as valor
                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                        , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                        , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                    WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                        AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                        AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                        AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                                        AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                        AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                                        AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                                        AND evento_rescisao_calculado.cod_evento = '|| inCodEventoInfDeducaoDependente ||'
                                        AND evento_rescisao_calculado.desdobramento != ''D''
                                      LIMIT 1';
                            nuTemp := selectIntoNumeric(stSelect);
                            IF nuTemp IS NOT NULL AND nuValor = 0 THEN           
                                nuValor := nuValor + nuTemp;                                 
                            END IF;                                                         
                        END IF;                    
                        
                        IF reCodTipo.cod_tipo = 5 THEN
                            -- Décimo na rescisao
                            stSelect := 'SELECT evento_rescisao_calculado.valor as valor
                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                        , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                        , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                    WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                        AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                        AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                        AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                                        AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                        AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                                        AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                                        AND evento_rescisao_calculado.cod_evento = '|| inCodEventoInfDeducaoDependente ||'
                                        AND evento_rescisao_calculado.desdobramento = ''D''
                                      LIMIT 1';
                            nuTemp := selectIntoNumeric(stSelect);                
                            IF nuTemp IS NOT NULL THEN				                              
                                IF  reRegistro.numcgm = stCGMAnterior THEN
                                    IF boSomaDeducaoDecimo IS TRUE THEN                                
					nuValorDeducoesDecimo := nuValorDeducoesDecimo + nuTemp;
					boSomaDeducaoDecimo := FALSE;
				    END IF;
				ELSE
				    nuValorDeducoesDecimo := nuValorDeducoesDecimo + nuTemp;
				    boSomaDeducaoDecimo := FALSE;
                                END IF;
                            END IF;
                        END IF;                    
                        
                        IF reCodTipo.cod_tipo = 4 THEN                                                          
                            --Décimo
                            --nuTemp := recupera_evento_calculado_dirf(3,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoInfDeducaoDependente,stEntidade);
                            stSelect := 'SELECT evento_decimo_calculado.valor as valor
                                            FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                            , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                            , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                            AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                                            AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                            AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                                            AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                            AND registro_evento_decimo.cod_contrato = '|| reRegistro.cod_contrato ||'
                                            AND registro_evento_decimo.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                                            AND evento_decimo_calculado.cod_evento = '|| inCodEventoInfDeducaoDependente ||'
                                            AND evento_decimo_calculado.desdobramento = ''D''
                                          LIMIT 1';
                            nuTemp := selectIntoNumeric(stSelect);
                            IF nuTemp IS NOT NULL THEN
                                nuValorDeducoesDecimo := nuValorDeducoesDecimo + nuTemp;
                                boSomaDeducaoDecimo := FALSE;
			    END IF;
                        END IF;                          
                    END IF;
                END LOOP;                                         
            --Fim dependentes     
            stCGMAnterior   := reRegistro.numcgm;        
            arValores2[inIndex] := nuValor;       
                   
            --###########Imposto retido
            --###########Desconto de IRRF                       
            nuValor := 0;
            --Salário
            nuTemp := recupera_evento_calculado_dirf(1,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFDesconto,stEntidade);
            nuTemp := nuTemp + recupera_evento_calculado_dirf(1,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFDescontoCPensao,stEntidade);            
            
            IF nuTemp IS NOT NULL THEN
                --nuSomaDescontoIRRF := nuSomaDescontoIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuValor := nuTemp;
                END IF;
            END IF;                            
                            
                            
            IF inSequenciaEventos = 2 THEN
                --Salário
                nuTemp := recupera_evento_calculado_dirf(1,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoPensao,stEntidade);                                    
                IF nuTemp IS NOT NULL THEN
                    nuValor := nuTemp;
                END IF;
            END IF; 

            --Complementar
            nuTemp := recupera_evento_calculado_dirf(0,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFDesconto,stEntidade);
            nuTemp := nuTemp + recupera_evento_calculado_dirf(0,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFDescontoCPensao,stEntidade);            
            IF nuTemp IS NOT NULL THEN
                --nuSomaDescontoIRRF := nuSomaDescontoIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuValor := nuValor +  nuTemp;
                END IF;
            END IF;
                                     
            IF inSequenciaEventos = 2 THEN
                --Complementar
                nuTemp := recupera_evento_calculado_dirf(0,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoPensao,stEntidade);                                    
                IF nuTemp IS NOT NULL THEN
                    nuValor := nuValor +  nuTemp;
                END IF;
            END IF; 
                         
            --Férias
            nuTemp := recupera_evento_calculado_dirf(2,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFDesconto,stEntidade);
            nuTemp := nuTemp + recupera_evento_calculado_dirf(2,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoIRRFDescontoCPensao,stEntidade);
            IF nuTemp IS NOT NULL THEN
                --nuSomaDescontoIRRF := nuSomaDescontoIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuValor := nuValor +  nuTemp;
                END IF;
            END IF;
            
            IF inSequenciaEventos = 2 THEN
                --Férias
                nuTemp := recupera_evento_calculado_dirf(2,rePeriodos.cod_periodo_movimentacao,reRegistro.cod_contrato,inCodEventoPensao,stEntidade);                                    
                IF nuTemp IS NOT NULL THEN
                    nuValor := nuValor + nuTemp;
                END IF;
            END IF;
            
            stSelect := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                         AND evento_rescisao_calculado.desdobramento != ''D''
                         AND (evento_rescisao_calculado.cod_evento = '|| inCodEventoIRRFDesconto ||' OR evento_rescisao_calculado.cod_evento = '|| inCodEventoIRRFDescontoCPensao ||')';
            nuTemp := selectIntoNumeric(stSelect);
            IF nuTemp IS NOT NULL THEN
                --nuSomaDescontoIRRF := nuSomaDescontoIRRF + nuTemp;
                IF inSequenciaEventos = 1 THEN
                    nuValor := nuValor + nuTemp;
                END IF;
            END IF;
                                     
            IF inSequenciaEventos = 2 THEN
                stSelect := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                         AND evento_rescisao_calculado.desdobramento != ''D''
                         AND evento_rescisao_calculado.cod_evento = '|| inCodEventoPensao;
                nuTemp := selectIntoNumeric(stSelect);
                IF nuTemp IS NOT NULL THEN
                    nuValor := nuValor + nuTemp;
                END IF;                          
            END IF; 
                       
            arValores3[inIndex] := nuValor;
                                    
            --###########Início Décimo
            nuTemp := null;
            IF inSequenciaEventos = 1 THEN                         
                stSelect := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                         AND evento_rescisao_calculado.desdobramento = ''D'' 
                         AND (evento_rescisao_calculado.cod_evento = '|| inCodEventoIRRFDesconto ||' OR evento_rescisao_calculado.cod_evento = '|| inCodEventoIRRFDescontoCPensao ||')';
                nuTemp := selectIntoNumeric(stSelect);
            END IF;
            IF inSequenciaEventos = 2 THEN
                stSelect := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                           , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                         AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                         AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                         AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                         AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                         AND evento_rescisao_calculado.desdobramento = ''D''
                         AND evento_rescisao_calculado.cod_evento = '|| inCodEventoPensao;
                nuTemp := selectIntoNumeric(stSelect);
            END IF;            
            IF nuTemp IS NOT NULL THEN
                nuValorRetidoDecimo := nuValorRetidoDecimo + nuTemp;
            END IF;
                
            nuTemp := null;
            IF inSequenciaEventos = 1 THEN                         
                stSelect := 'SELECT sum(evento_decimo_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                           , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                         AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                         AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                         AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                         AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_decimo.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_decimo.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'            
                         AND (evento_decimo_calculado.cod_evento = '|| inCodEventoIRRFDesconto ||' OR evento_decimo_calculado.cod_evento = '|| inCodEventoIRRFDescontoCPensao ||')';
                nuTemp := selectIntoNumeric(stSelect);
            END IF;
            IF inSequenciaEventos = 2 THEN
                stSelect := 'SELECT sum(evento_decimo_calculado.valor) as valor
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                           , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                           , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                       WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                         AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                         AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                         AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                         AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                         AND registro_evento_decimo.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_decimo.cod_periodo_movimentacao = '|| rePeriodos.cod_periodo_movimentacao ||'
                         AND evento_decimo_calculado.cod_evento = '|| inCodEventoPensao;
                nuTemp := selectIntoNumeric(stSelect);
            END IF;                                 
            IF nuTemp IS NOT NULL THEN
                nuValorRetidoDecimo := nuValorRetidoDecimo + nuTemp;
            END IF;               
            --###########Fim Décimo 
            inIndex := inIndex + 1;
        END LOOP;

        --Salário         
        nuSomaBaseIRRF := recupera_evento_calculado_dirf_servidor(1,inExercicio,reRegistro.numcgm,inCodEventoIRRFBase,stEntidade);                   
        nuSomaDescontoIRRF := recupera_evento_calculado_dirf_servidor(1,inExercicio,reRegistro.numcgm,inCodEventoIRRFDesconto,stEntidade);
        nuSomaDescontoIRRF := nuSomaDescontoIRRF + recupera_evento_calculado_dirf_servidor(1,inExercicio,reRegistro.numcgm,inCodEventoIRRFDescontoCPensao,stEntidade);                            
        
        --Complementar
        nuSomaBaseIRRF := nuSomaBaseIRRF + recupera_evento_calculado_dirf_servidor(0,inExercicio,reRegistro.numcgm,inCodEventoIRRFBase,stEntidade);                           
        nuSomaDescontoIRRF := nuSomaDescontoIRRF + recupera_evento_calculado_dirf_servidor(0,inExercicio,reRegistro.numcgm,inCodEventoIRRFDesconto,stEntidade);
        nuSomaDescontoIRRF := nuSomaDescontoIRRF + recupera_evento_calculado_dirf_servidor(0,inExercicio,reRegistro.numcgm,inCodEventoIRRFDescontoCPensao,stEntidade);                            
                
        --Férias
        nuSomaBaseIRRF := nuSomaBaseIRRF + recupera_evento_calculado_dirf_servidor(2,inExercicio,reRegistro.numcgm,inCodEventoIRRFBase,stEntidade);                                   
        nuSomaDescontoIRRF := nuSomaDescontoIRRF + recupera_evento_calculado_dirf_servidor(2,inExercicio,reRegistro.numcgm,inCodEventoIRRFDesconto,stEntidade);
        nuSomaDescontoIRRF := nuSomaDescontoIRRF + recupera_evento_calculado_dirf_servidor(2,inExercicio,reRegistro.numcgm,inCodEventoIRRFDescontoCPensao,stEntidade);                            
                
        --Rescisão
        nuSomaBaseIRRF := nuSomaBaseIRRF + recupera_evento_calculado_dirf_servidor(4,inExercicio,reRegistro.numcgm,inCodEventoIRRFBase,stEntidade);                                   
        nuSomaDescontoIRRF := nuSomaDescontoIRRF + recupera_evento_calculado_dirf_servidor(4,inExercicio,reRegistro.numcgm,inCodEventoIRRFDesconto,stEntidade);
        nuSomaDescontoIRRF := nuSomaDescontoIRRF + recupera_evento_calculado_dirf_servidor(4,inExercicio,reRegistro.numcgm,inCodEventoIRRFDescontoCPensao,stEntidade);                                    
        
        IF nuSomaDescontoIRRF > 0 OR nuSomaBaseIRRF > 6000 THEN      
            inIndex := 1;
            nuTemp := 0;
            WHILE inIndex <= 12 LOOP
                nuTemp := nuTemp + arValores1[inIndex]::NUMERIC;
                nuTemp := nuTemp + arValores2[inIndex]::NUMERIC;
                nuTemp := nuTemp + arValores3[inIndex]::NUMERIC;
                inIndex := inIndex + 1;
            END LOOP;
            nuTemp := nuTemp + nuValorRendimentosDecimo;
            nuTemp := nuTemp + nuValorDeducoesDecimo;
            nuTemp := nuTemp + nuValorRetidoDecimo;
            
            IF nuTemp > 0 THEN
                rwDirf.uso_declarante        := reRegistro.registro;
                rwDirf.nome_beneficiario     := reRegistro.nom_cgm;
                rwDirf.beneficiario          := reRegistro.cpf;
                rwDirf.jan1                  := arValores1[1];
                rwDirf.fev1                  := arValores1[2];
                rwDirf.mar1                  := arValores1[3];
                rwDirf.abr1                  := arValores1[4];
                rwDirf.mai1                  := arValores1[5];
                rwDirf.jun1                  := arValores1[6];
                rwDirf.jul1                  := arValores1[7];
                rwDirf.ago1                  := arValores1[8];
                rwDirf.set1                  := arValores1[9];
                rwDirf.out1                  := arValores1[10];
                rwDirf.nov1                  := arValores1[11];
                rwDirf.dez1                  := arValores1[12];
                rwDirf.jan2                  := arValores2[1];
                rwDirf.fev2                  := arValores2[2];
                rwDirf.mar2                  := arValores2[3];
                rwDirf.abr2                  := arValores2[4];
                rwDirf.mai2                  := arValores2[5];
                rwDirf.jun2                  := arValores2[6];
                rwDirf.jul2                  := arValores2[7];
                rwDirf.ago2                  := arValores2[8];
                rwDirf.set2                  := arValores2[9];
                rwDirf.out2                  := arValores2[10];
                rwDirf.nov2                  := arValores2[11];
                rwDirf.dez2                  := arValores2[12];
                rwDirf.jan3                  := arValores3[1];
                rwDirf.fev3                  := arValores3[2];
                rwDirf.mar3                  := arValores3[3];
                rwDirf.abr3                  := arValores3[4];
                rwDirf.mai3                  := arValores3[5];
                rwDirf.jun3                  := arValores3[6];
                rwDirf.jul3                  := arValores3[7];
                rwDirf.ago3                  := arValores3[8];
                rwDirf.set3                  := arValores3[9];
                rwDirf.out3                  := arValores3[10];
                rwDirf.nov3                  := arValores3[11];
                rwDirf.dez3                  := arValores3[12];                        
                rwDirf.dec1                  := nuValorRendimentosDecimo;
                rwDirf.dec2                  := nuValorDeducoesDecimo;
                rwDirf.dec3                  := nuValorRetidoDecimo;
                RETURN NEXT rwDirf;
            END IF;
            inSequencia := inSequencia + 1;
        END IF;            
        
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

DROP TYPE colunasDirfReduzida CASCADE;
CREATE TYPE colunasDirfReduzida AS (
    uso_declarante      INTEGER,
    sequencia           INTEGER,
    nome_beneficiario   VARCHAR(200),
    beneficiario        VARCHAR(14),
    ident_especializacao VARCHAR(1),
    codigo_retencao      VARCHAR(4),
    ident_especie_beneficiario INTEGER,
    jan         VARCHAR(49),
    fev         VARCHAR(49),
    mar         VARCHAR(49),
    abr         VARCHAR(49),
    mai         VARCHAR(49),
    jun         VARCHAR(49),
    jul         VARCHAR(49),
    ago         VARCHAR(49),
    SET         VARCHAR(49),
    out         VARCHAR(49),
    nov         VARCHAR(49),
    dez         VARCHAR(49),
    dec         VARCHAR(49)
);

CREATE OR REPLACE FUNCTION dirf_reduzida(VARCHAR,INTEGER,VARCHAR,VARCHAR) RETURNS SETOF colunasDirfReduzida AS $$ 
DECLARE
    stEntidade                      ALIAS FOR $1;
    inExercicio                     ALIAS FOR $2;    
    stTipoFiltro                    ALIAS FOR $3;
    stCodigos                       ALIAS FOR $4;
    stSelect                        VARCHAR:='';
    reRegistro                      RECORD;
    inSequencia                     INTEGER:=2;
    rwDirf                          colunasDirfReduzida%ROWTYPE;
BEGIN

    stSelect := '
  SELECT nome_beneficiario
       , beneficiario
       , ident_especializacao
       , MAX(uso_declarante) as uso_declarante
       , SUM(jan1 ) AS jan1
       , SUM(fev1 ) AS fev1
       , SUM(mar1 ) AS mar1
       , SUM(abr1 ) AS abr1
       , SUM(mai1 ) AS mai1
       , SUM(jun1 ) AS jun1
       , SUM(jul1 ) AS jul1
       , SUM(ago1 ) AS ago1
       , SUM(set1 ) AS set1
       , SUM(out1 ) AS out1
       , SUM(nov1 ) AS nov1
       , SUM(dez1 ) AS dez1
       , SUM(jan2 ) AS jan2
       , SUM(fev2 ) AS fev2
       , SUM(mar2 ) AS mar2
       , SUM(abr2 ) AS abr2
       , SUM(mai2 ) AS mai2
       , SUM(jun2 ) AS jun2
       , SUM(jul2 ) AS jul2
       , SUM(ago2 ) AS ago2
       , SUM(set2 ) AS set2
       , SUM(out2 ) AS out2
       , SUM(nov2 ) AS nov2
       , SUM(dez2 ) AS dez2
       , SUM(jan3 ) AS jan3
       , SUM(fev3 ) AS fev3
       , SUM(mar3 ) AS mar3
       , SUM(abr3 ) AS abr3
       , SUM(mai3 ) AS mai3
       , SUM(jun3 ) AS jun3
       , SUM(jul3 ) AS jul3
       , SUM(ago3 ) AS ago3
       , SUM(set3 ) AS set3
       , SUM(out3 ) AS out3
       , SUM(nov3 ) AS nov3
       , SUM(dez3 ) AS dez3        
       , SUM(dec1 ) AS dec1
       , SUM(dec2 ) AS dec2
       , SUM(dec3 ) AS dec3
  FROM (
  SELECT nome_beneficiario
       , beneficiario
       , MAX(uso_declarante) as uso_declarante
       , SUM(jan1 ) AS jan1
       , SUM(fev1 ) AS fev1
       , SUM(mar1 ) AS mar1
       , SUM(abr1 ) AS abr1
       , SUM(mai1 ) AS mai1
       , SUM(jun1 ) AS jun1
       , SUM(jul1 ) AS jul1
       , SUM(ago1 ) AS ago1
       , SUM(set1 ) AS set1
       , SUM(out1 ) AS out1
       , SUM(nov1 ) AS nov1
       , SUM(dez1 ) AS dez1
       , SUM(jan2 ) AS jan2
       , SUM(fev2 ) AS fev2
       , SUM(mar2 ) AS mar2
       , SUM(abr2 ) AS abr2
       , SUM(mai2 ) AS mai2
       , SUM(jun2 ) AS jun2
       , SUM(jul2 ) AS jul2
       , SUM(ago2 ) AS ago2
       , SUM(set2 ) AS set2
       , SUM(out2 ) AS out2
       , SUM(nov2 ) AS nov2
       , SUM(dez2 ) AS dez2
       , SUM(jan3 ) AS jan3
       , SUM(fev3 ) AS fev3
       , SUM(mar3 ) AS mar3
       , SUM(abr3 ) AS abr3
       , SUM(mai3 ) AS mai3
       , SUM(jun3 ) AS jun3
       , SUM(jul3 ) AS jul3
       , SUM(ago3 ) AS ago3
       , SUM(set3 ) AS set3
       , SUM(out3 ) AS out3
       , SUM(nov3 ) AS nov3
       , SUM(dez3 ) AS dez3        
       , SUM(dec1 ) AS dec1
       , SUM(dec2 ) AS dec2
       , SUM(dec3 ) AS dec3
       , CASE WHEN '|| inExercicio ||' >= ''2007'' AND '|| inExercicio ||' <= ''2010'' THEN
                 ''0'' 
              WHEN '|| inExercicio ||' >= ''2004'' AND '|| inExercicio ||' <= ''2006'' THEN
                 '''' 
         END AS ident_especializacao         
    FROM dirf('''|| stEntidade ||''','|| inExercicio ||','''|| stTipoFiltro ||''','''|| stCodigos ||''',1)
GROUP BY nome_beneficiario
       , beneficiario       
UNION
  SELECT nome_beneficiario
       , beneficiario
       , MAX(uso_declarante) AS uso_declarante
       , SUM(jan1 ) AS jan1
       , SUM(fev1 ) AS fev1
       , SUM(mar1 ) AS mar1
       , SUM(abr1 ) AS abr1
       , SUM(mai1 ) AS mai1
       , SUM(jun1 ) AS jun1
       , SUM(jul1 ) AS jul1
       , SUM(ago1 ) AS ago1
       , SUM(set1 ) AS set1
       , SUM(out1 ) AS out1
       , SUM(nov1 ) AS nov1
       , SUM(dez1 ) AS dez1
       , SUM(jan2 ) AS jan2
       , SUM(fev2 ) AS fev2
       , SUM(mar2 ) AS mar2
       , SUM(abr2 ) AS abr2
       , SUM(mai2 ) AS mai2
       , SUM(jun2 ) AS jun2
       , SUM(jul2 ) AS jul2
       , SUM(ago2 ) AS ago2
       , SUM(set2 ) AS set2
       , SUM(out2 ) AS out2
       , SUM(nov2 ) AS nov2
       , SUM(dez2 ) AS dez2
       , SUM(jan3 ) AS jan3
       , SUM(fev3 ) AS fev3
       , SUM(mar3 ) AS mar3
       , SUM(abr3 ) AS abr3
       , SUM(mai3 ) AS mai3
       , SUM(jun3 ) AS jun3
       , SUM(jul3 ) AS jul3
       , SUM(ago3 ) AS ago3
       , SUM(set3 ) AS set3
       , SUM(out3 ) AS out3
       , SUM(nov3 ) AS nov3
       , SUM(dez3 ) AS dez3        
       , SUM(dec1 ) AS dec1
       , SUM(dec2 ) AS dec2
       , SUM(dec3 ) AS dec3
       , ''1'' AS ident_especializacao         
    FROM dirf('''|| stEntidade ||''','|| inExercicio ||','''|| stTipoFiltro ||''','''|| stCodigos ||''',2)
GROUP BY nome_beneficiario
       , beneficiario
       , uso_declarante
UNION
  SELECT nome_beneficiario
       , beneficiario
       , MAX(uso_declarante) as uso_declarante
       , SUM(jan1 ) AS jan1
       , SUM(fev1 ) AS fev1
       , SUM(mar1 ) AS mar1
       , SUM(abr1 ) AS abr1
       , SUM(mai1 ) AS mai1
       , SUM(jun1 ) AS jun1
       , SUM(jul1 ) AS jul1
       , SUM(ago1 ) AS ago1
       , SUM(set1 ) AS set1
       , SUM(out1 ) AS out1
       , SUM(nov1 ) AS nov1
       , SUM(dez1 ) AS dez1
       , SUM(jan2 ) AS jan2
       , SUM(fev2 ) AS fev2
       , SUM(mar2 ) AS mar2
       , SUM(abr2 ) AS abr2
       , SUM(mai2 ) AS mai2
       , SUM(jun2 ) AS jun2
       , SUM(jul2 ) AS jul2
       , SUM(ago2 ) AS ago2
       , SUM(set2 ) AS set2
       , SUM(out2 ) AS out2
       , SUM(nov2 ) AS nov2
       , SUM(dez2 ) AS dez2
       , SUM(jan3 ) AS jan3
       , SUM(fev3 ) AS fev3
       , SUM(mar3 ) AS mar3
       , SUM(abr3 ) AS abr3
       , SUM(mai3 ) AS mai3
       , SUM(jun3 ) AS jun3
       , SUM(jul3 ) AS jul3
       , SUM(ago3 ) AS ago3
       , SUM(set3 ) AS set3
       , SUM(out3 ) AS out3
       , SUM(nov3 ) AS nov3
       , SUM(dez3 ) AS dez3        
       , SUM(dec1 ) AS dec1
       , SUM(dec2 ) AS dec2
       , SUM(dec3 ) AS dec3
       , CASE WHEN '|| inExercicio ||' >= ''2007'' AND '|| inExercicio ||' <= ''2010'' THEN
                 ''0'' 
              WHEN '|| inExercicio ||' >= ''2004'' AND '|| inExercicio ||' <= ''2006'' THEN
                 '''' 
         END AS ident_especializacao         
    FROM dirf('''|| stEntidade ||''','|| inExercicio ||','''|| stTipoFiltro ||''','''|| stCodigos ||''',3)
GROUP BY nome_beneficiario
       , beneficiario
) AS tabelas
GROUP BY nome_beneficiario
       , beneficiario
       , ident_especializacao
ORDER BY nome_beneficiario
       , ident_especializacao';
       
    FOR reRegistro IN EXECUTE stSelect LOOP    
        rwDirf.uso_declarante       := reRegistro.uso_declarante;
        rwDirf.nome_beneficiario    := reRegistro.nome_beneficiario;
        rwDirf.beneficiario         := reRegistro.beneficiario;
        rwDirf.sequencia            := inSequencia;
        rwDirf.ident_especializacao := reRegistro.ident_especializacao;
        rwDirf.codigo_retencao      := '0561';
        rwDirf.ident_especie_beneficiario := 1;

        IF reRegistro.jan1 >= 0 THEN                
            rwDirf.jan              := lpad(replace(trunc(reRegistro.jan1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.jan2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.jan3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.jan              := lpad('',39,'0');
        END IF;
        IF reRegistro.fev1 >= 0 THEN        
            rwDirf.fev              := lpad(replace(trunc(reRegistro.fev1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.fev2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.fev3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.fev              := lpad('',39,'0');
        END IF;
        IF reRegistro.mar1 >= 0 THEN        
            rwDirf.mar              := lpad(replace(trunc(reRegistro.mar1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.mar2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.mar3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.mar              := lpad('',39,'0');
        END IF;
        IF reRegistro.abr1 >= 0 THEN        
            rwDirf.abr              := lpad(replace(trunc(reRegistro.abr1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.abr2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.abr3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.abr              := lpad('',39,'0');
        END IF;
        IF reRegistro.mai1 >= 0 THEN        
            rwDirf.mai              := lpad(replace(trunc(reRegistro.mai1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.mai2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.mai3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.mai              := lpad('',39,'0');
        END IF;
        IF reRegistro.jun1 >= 0 THEN        
            rwDirf.jun              := lpad(replace(trunc(reRegistro.jun1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.jun2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.jun3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.jun              := lpad('',39,'0');
        END IF;
        IF reRegistro.jul1 >= 0 THEN                
            rwDirf.jul              := lpad(replace(trunc(reRegistro.jul1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.jul2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.jul3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.jul              := lpad('',39,'0');
        END IF;
        IF reRegistro.ago1 >= 0 THEN        
            rwDirf.ago              := lpad(replace(trunc(reRegistro.ago1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.ago2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.ago3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.ago              := lpad('',39,'0');
        END IF;
        IF reRegistro.set1 >= 0 THEN        
            rwDirf.set              := lpad(replace(trunc(reRegistro.set1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.set2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.set3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.set              := lpad('',39,'0');
        END IF;
        IF reRegistro.out1 >= 0 THEN        
            rwDirf.out              := lpad(replace(trunc(reRegistro.out1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.out2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.out3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.out              := lpad('',39,'0');
        END IF;            
        IF reRegistro.nov1 >= 0 THEN        
            rwDirf.nov              := lpad(replace(trunc(reRegistro.nov1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.nov2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.nov3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.nov              := lpad('',39,'0');
        END IF;
        IF reRegistro.dez1 >= 0 THEN
            rwDirf.dez              := lpad(replace(trunc(reRegistro.dez1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.dez2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.dez3,2)::varchar,'.',''),13,'0');                       
        ELSE
            rwDirf.dez              := lpad('',39,'0');
        END IF;
        IF reRegistro.dec1 > 0 THEN
            rwDirf.dec              := lpad(replace(trunc(reRegistro.dec1,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.dec2,2)::varchar,'.',''),13,'0')||lpad(replace(trunc(reRegistro.dec3,2)::varchar,'.',''),13,'0');
        ELSE
            rwDirf.dec              := lpad('',39,'0');
        END IF;
        RETURN NEXT rwDirf;        
        inSequencia := inSequencia + 1;
    END LOOP;

    inSequencia := criarbufferinteiro(  'inSequencia' , inSequencia  );

    DELETE FROM tmp_valores_decimo;
    DELETE FROM tmp_cpf_controle_dependentes;
END;
$$ LANGUAGE 'plpgsql';

--SELECT * FROM dirf_reduzida('',2007,'contrato_todos','4','',0);

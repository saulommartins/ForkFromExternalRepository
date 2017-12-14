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
/*
 * PL para Relatorio Espelho Ponto
 * Data de Criação   : 17/10/2008


 * @author Analista Dagiane Vieira
 * @author Desenvolvedor Alex Cardoso
 
 * @package URBEM
 * @subpackage

 $Id:$
 
*/ 
 
CREATE OR REPLACE FUNCTION recuperaRelatorioEspelhoPonto(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasRelatorioCartaoPonto AS $$
DECLARE
    inCodContrato                               ALIAS FOR $1;
    inCodConfiguracaoPonto                      ALIAS FOR $2;
    inCodGrade                                  ALIAS FOR $3;
    dtInicioPeriodo                             ALIAS FOR $4;
    dtFimPeriodo                                ALIAS FOR $5;
    stEntidade                                  ALIAS FOR $6;
    
    rwCartaoPonto                               colunasRelatorioCartaoPonto%ROWTYPE;

    crCursor                                    REFCURSOR;    
    reRegistro                                  RECORD;
    reRegistroConfiguracao                      RECORD;
    stSql                                       VARCHAR;
    stAux                                       VARCHAR;
    stAux2                                      VARCHAR;
    
    inSequencial                                INTEGER;
    inAux                                       INTEGER;
    inDiasBetween                               INTEGER;
    inAddDias                                   INTEGER;
    arHorariosGrade                             VARCHAR[][] := ARRAY[ARRAY['', '', '', '', ''], 
                                                                     ARRAY['', '', '', '', ''],
                                                                     ARRAY['', '', '', '', ''],
                                                                     ARRAY['', '', '', '', ''],
                                                                     ARRAY['', '', '', '', ''],
                                                                     ARRAY['', '', '', '', ''],
                                                                     ARRAY['', '', '', '', ''],
                                                                     ARRAY['', '', '', '', '']];
    arHorariosGradeEscala                       VARCHAR[];
    stHorariosGradeEscala                       VARCHAR;
    stHorariosGradeEscalaAntesPrimeiroTurno     VARCHAR;
    stHorariosGradeEscalaDepoisSegundoTurno     VARCHAR;
    stHorariosGradeEscalaIntervalo              VARCHAR;    
    arHorariosImpManut                          VARCHAR[];
    arHorariosCalculoHsTrab                     VARCHAR[];
    stHorariosCalculoHsTrab                     VARCHAR;
    arTemp                                      VARCHAR[];
    inIndex                                     INTEGER;
    
    intTotalHorasCargaHorariaDia                VARCHAR;/*INTERVAL*/
    intTotalHorasCumpridasDia                   VARCHAR;/*INTERVAL*/
    intTotalHorasCargaHorariaCumpridasDia       VARCHAR;/*INTERVAL*/
    
    intTotalHorasTrabalhadas                    VARCHAR;/*INTERVAL*/
    
    intTotalHorasAdicionalNoturno               VARCHAR;/*INTERVAL*/
    intTotalHorasAdicionalNoturnoDaSemana       VARCHAR;/*INTERVAL*/
    
    intTotalHorasExtras                         VARCHAR;/*INTERVAL*/
    intTotalHorasExtrasDaSemana                 VARCHAR;/*INTERVAL*/

    intTotalHorasExtrasNoturnas                 VARCHAR;/*INTERVAL*/
    intTotalHorasExtrasNoturnasDaSemana         VARCHAR;/*INTERVAL*/
    
    intTotalHorasAtraso                         VARCHAR;/*INTERVAL*/
    intTotalHorasAtrasoDaSemana                 VARCHAR;/*INTERVAL*/    
    
    intTotalHorasFalta                          VARCHAR;/*INTERVAL*/
    intTotalHorasFaltaDaSemana                  VARCHAR;/*INTERVAL*/
    
    intTemp                                     VARCHAR;/*INTERVAL*/
    
    intHorarioAdicionalNoturnoMadrugada1        VARCHAR;/*INTERVAL*/
    intHorarioAdicionalNoturnoMadrugada2        VARCHAR;/*INTERVAL*/
    
    intHorarioAdicionalNoturnoNoite1            VARCHAR;/*INTERVAL*/
    intHorarioAdicionalNoturnoNoite2            VARCHAR;/*INTERVAL*/
    
    --variaveis que influem nas regras
    boOrigemCompensacao                         BOOLEAN;
    stOrigemCompensacaoDtFalta                  VARCHAR;
    boOrigemEscala                              BOOLEAN;
    boOrigemCalendario                          BOOLEAN;
    boOrigemGrade                               BOOLEAN;
    boDiaTrabalho                               BOOLEAN;/*trabaho ou folga (false)*/
    
    boDadosHorariosEscala                       BOOLEAN;
    boDadosHorariosGrade                        BOOLEAN;
    
    boDadosManutencao                           BOOLEAN;
    boDadosImportacao                           BOOLEAN;
    
    boAutorizarHorasExtras                      BOOLEAN;
    boCalcularExtras                            BOOLEAN;
    boCalcularFaltas                            BOOLEAN;
    
    inCodDiaSemana                              INTEGER;
    
    --variaveis de conteudo de retorno
    boCpDsr                                     BOOLEAN;
    stCpData                                    VARCHAR;
    stCpDia                                     VARCHAR;
    stCpTipo                                    VARCHAR;
    stCpOrigem                                  VARCHAR;
    stCpHorarios                                VARCHAR;
    stCpJustificativa                           VARCHAR;
    stCpCargaHoraria                            VARCHAR;
    stCpHsTrab                                  VARCHAR;
    stCpAdNot                                   VARCHAR;
    stCpExtras                                  VARCHAR;
    stCpExtNot                                  VARCHAR;
    stCpAtrasos                                 VARCHAR;
    stCpFaltas                                  VARCHAR;
    stCpHsTot                                   VARCHAR;
    
BEGIN

    /* LIMPA DADOS DA TABELA TEMPORARIA DE RELATORIO */
    inSequencial := 1;
    
    stSql := 'DELETE FROM ponto'|| stEntidade ||'.relatorio_espelho_ponto';
    EXECUTE stSql;  
    
    /* CONFIGURACAO */
    stSql := '   SELECT remarcacoes_consecutivas.minutos as remarcacoes_minutos
                      , to_char(arredondar_tempo.hora_entrada1, ''hh24:mi'') as arredondar_hora_entrada1
                      , to_char(arredondar_tempo.hora_entrada2, ''hh24:mi'') as arredondar_hora_entrada2
                      , to_char(arredondar_tempo.hora_saida1, ''hh24:mi'') as arredondar_hora_saida1
                      , to_char(arredondar_tempo.hora_saida2, ''hh24:mi'') as arredondar_hora_saida2
                      , configuracao_parametros_gerais.trabalho_feriado
                      , to_char(configuracao_parametros_gerais.hora_noturno1, ''hh24:mi'') as hora_noturno1
                      , to_char(configuracao_parametros_gerais.hora_noturno2, ''hh24:mi'') as hora_noturno2
                      , configuracao_parametros_gerais.separar_adicional
                      , COALESCE(fator_multiplicacao.fator, 1) as fator_multiplicacao
                      , COALESCE(horas_extras.minutos, 0) as horas_extras_minutos
                      , COALESCE(horas_extras.periodo, ''D'') as horas_extras_periodo --FIX ME (PERIODO DEVE SER COMUM PARA FALTAS E ATRASOS TAMBEM)
                      , COALESCE(horas_extras.periodo, ''D'') as tolerancia_periodo
                      , COALESCE(configuracao_horas_extras_2.anterior_periodo_1, false) as horas_extras_anterior
                      , COALESCE(configuracao_horas_extras_2.entre_periodo_1_2, false) as horas_extras_intervalo
                      , COALESCE(configuracao_horas_extras_2.posterior_periodo_2, false) as horas_extras_posterior
                      , COALESCE(configuracao_horas_extras_2.autorizacao, false) as horas_extras_somente_com_autorizacao
                      , COALESCE(configuracao_horas_extras_2.atrasos, false) as horas_extras_compensar_atrasos
                      , COALESCE(configuracao_horas_extras_2.faltas, false) as horas_extras_compensar_faltas
                      , configuracao_parametros_gerais.somar_extras as horas_extras_noturnas_somar
                      , configuracao_parametros_gerais.limitar_atrasos
                      , COALESCE(atrasos.minutos, 0) as tolerancia_atrasos
                      , COALESCE(faltas.minutos, 0) as tolerancia_faltas
                      , configuracao_parametros_gerais.cod_dia_dsr
                   FROM ponto'|| stEntidade ||'.configuracao_relogio_ponto
                   JOIN ponto'|| stEntidade ||'.configuracao_parametros_gerais
                     ON (    configuracao_relogio_ponto.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND configuracao_relogio_ponto.ultimo_timestamp = configuracao_parametros_gerais.timestamp)
                         
              LEFT JOIN ponto'|| stEntidade ||'.remarcacoes_consecutivas
                     ON (    remarcacoes_consecutivas.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND remarcacoes_consecutivas.timestamp = configuracao_parametros_gerais.timestamp)
                         
              LEFT JOIN ponto'|| stEntidade ||'.arredondar_tempo
                     ON (    arredondar_tempo.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND arredondar_tempo.timestamp = configuracao_parametros_gerais.timestamp)
                         
              LEFT JOIN ponto'|| stEntidade ||'.fator_multiplicacao
                     ON (    fator_multiplicacao.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND fator_multiplicacao.timestamp = configuracao_parametros_gerais.timestamp)
                         
              LEFT JOIN ponto'|| stEntidade ||'.horas_extras
                     ON (    horas_extras.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND horas_extras.timestamp = configuracao_parametros_gerais.timestamp)
                         
              LEFT JOIN ponto'|| stEntidade ||'.configuracao_horas_extras_2
                     ON (    configuracao_horas_extras_2.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND configuracao_horas_extras_2.timestamp = configuracao_parametros_gerais.timestamp)
                         
              LEFT JOIN ponto'|| stEntidade ||'.atrasos
                     ON (    atrasos.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND atrasos.timestamp = configuracao_parametros_gerais.timestamp)
                         
              LEFT JOIN ponto'|| stEntidade ||'.faltas
                     ON (    faltas.cod_configuracao = configuracao_parametros_gerais.cod_configuracao
                         AND faltas.timestamp = configuracao_parametros_gerais.timestamp)
                         
                  WHERE configuracao_relogio_ponto.cod_configuracao = '|| inCodConfiguracaoPonto;
                  
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reRegistroConfiguracao;
    CLOSE crCursor;

    
    IF reRegistroConfiguracao.remarcacoes_minutos = 0 THEN
        reRegistroConfiguracao.remarcacoes_minutos := NULL;
    END IF;
    
    IF reRegistroConfiguracao.arredondar_hora_entrada1 = '00:00' OR 
       reRegistroConfiguracao.arredondar_hora_entrada1 = '' OR
       reRegistroConfiguracao.arredondar_hora_entrada1 = '0' THEN
       reRegistroConfiguracao.arredondar_hora_entrada1 := NULL;
    END IF;
    
    IF reRegistroConfiguracao.arredondar_hora_entrada2 = '00:00' OR 
       reRegistroConfiguracao.arredondar_hora_entrada2 = '' OR
       reRegistroConfiguracao.arredondar_hora_entrada2 = '0' THEN
       reRegistroConfiguracao.arredondar_hora_entrada2 := NULL;
    END IF;
    
    IF reRegistroConfiguracao.arredondar_hora_saida1 = '00:00' OR 
       reRegistroConfiguracao.arredondar_hora_saida1 = '' OR
       reRegistroConfiguracao.arredondar_hora_saida1 = '0' THEN
       reRegistroConfiguracao.arredondar_hora_saida1 := NULL;
    END IF;
    
    IF reRegistroConfiguracao.arredondar_hora_saida2 = '00:00' OR 
       reRegistroConfiguracao.arredondar_hora_saida2 = '' OR
       reRegistroConfiguracao.arredondar_hora_saida2 = '0' THEN
       reRegistroConfiguracao.arredondar_hora_saida2 := NULL;
    END IF;
    
    IF reRegistroConfiguracao.hora_noturno1 = '00:00' OR 
       reRegistroConfiguracao.hora_noturno1 = '' OR
       reRegistroConfiguracao.hora_noturno1 = '0' THEN
       reRegistroConfiguracao.hora_noturno1 := NULL;
    END IF;
    
    IF reRegistroConfiguracao.hora_noturno2 = '00:00' OR 
       reRegistroConfiguracao.hora_noturno2 = '' OR
       reRegistroConfiguracao.hora_noturno2 = '0' THEN
       reRegistroConfiguracao.hora_noturno2 := NULL;
    END IF;
    
    IF reRegistroConfiguracao.hora_noturno1 IS NULL OR 
       reRegistroConfiguracao.hora_noturno2 IS NULL THEN
        reRegistroConfiguracao.separar_adicional := false;
        reRegistroConfiguracao.hora_noturno1 := '00:00';
        reRegistroConfiguracao.hora_noturno2 := '00:00';
    END IF;
    
    IF reRegistroConfiguracao.hora_noturno1::interval > reRegistroConfiguracao.hora_noturno2::interval THEN
        intHorarioAdicionalNoturnoMadrugada1 := '00:00';
        intHorarioAdicionalNoturnoMadrugada2 := reRegistroConfiguracao.hora_noturno2;
        
        intHorarioAdicionalNoturnoNoite1 := reRegistroConfiguracao.hora_noturno1;
        intHorarioAdicionalNoturnoNoite2 := '23:59';
    ELSE
        intHorarioAdicionalNoturnoMadrugada1 := '00:00';
        intHorarioAdicionalNoturnoMadrugada2 := '00:00';
        
        intHorarioAdicionalNoturnoNoite1 := reRegistroConfiguracao.hora_noturno1;
        intHorarioAdicionalNoturnoNoite2 := reRegistroConfiguracao.hora_noturno2;
    END IF;
    
    /* LOOP PARA PERCORRER DATAS ATE DATA FINAL */
    intTotalHorasAdicionalNoturnoDaSemana := '00:00';
    intTotalHorasExtrasDaSemana           := '00:00';
    intTotalHorasExtrasNoturnasDaSemana   := '00:00';
    intTotalHorasAtrasoDaSemana           := '00:00';
    intTotalHorasFaltaDaSemana            := '00:00';
    
    inDiasBetween := selectIntoInteger('SELECT diff_datas_em_dias(TO_DATE('|| quote_literal(dtInicioPeriodo) ||',''dd/mm/yyyy''), TO_DATE('|| quote_literal(dtFimPeriodo) ||', ''dd/mm/yyyy''))');
     
    FOR inAddDias IN 0 .. inDiasBetween LOOP
        boCpDsr             := FALSE;
        stCpData            := NULL;
        stCpDia             := NULL;
        stCpTipo            := NULL;
        stCpOrigem          := NULL;
        stCpHorarios        := NULL;
        stCpJustificativa   := NULL;
        stCpCargaHoraria    := NULL;
        stCpHsTrab          := NULL;
        stCpAdNot           := NULL;
        stCpExtras          := NULL;
        stCpExtNot          := NULL;
        stCpAtrasos         := NULL;
        stCpFaltas          := NULL;
        stCpHsTot           := NULL;
    
        intTotalHorasTrabalhadas       := NULL;
        
        intTotalHorasAdicionalNoturno  := NULL;
        intTotalHorasExtras            := NULL;
        intTotalHorasExtrasNoturnas    := NULL;
        intTotalHorasAtraso            := NULL;
        intTotalHorasFalta             := NULL;
        intTemp                        := NULL;        
        
        /*DATA --*/
        stCpData := selectIntoVarchar('SELECT TO_CHAR(TO_DATE('|| quote_literal(dtInicioPeriodo) ||', ''dd/mm/yyyy'')+'|| inAddDias ||', ''dd/mm/yyyy'')');
        
        /*DIA DA SEMANA --*/
        stCpDia  := selectIntoVarchar('SELECT dia_semana( TO_DATE('|| quote_literal(stCpData) ||', ''dd/mm/yyyy'') )');
        
        inCodDiaSemana := extract(dow FROM TO_DATE(stCpData, 'dd/mm/yyyy'))+1;
        
        IF inCodDiaSemana = reRegistroConfiguracao.cod_dia_dsr THEN
            boCpDsr := TRUE;
        END IF;
        
        IF inCodDiaSemana = 1 THEN /*domingo*/
            intTotalHorasAdicionalNoturnoDaSemana := '00:00';
            intTotalHorasExtrasDaSemana           := '00:00';
            intTotalHorasExtrasNoturnasDaSemana   := '00:00';
            intTotalHorasAtrasoDaSemana           := '00:00';
            intTotalHorasFaltaDaSemana            := '00:00';
        END IF;
        
        /*TIPO --*/
        stSql := 'SELECT * FROM recuperaTipoOrigemTurnoServidor('|| inCodContrato ||','|| quote_literal(stCpData) ||','|| quote_literal(stEntidade) ||')';
        
        FOR reRegistro IN  EXECUTE stSql
        LOOP
            boOrigemCompensacao         := reRegistro.compensacao;
            stOrigemCompensacaoDtFalta  := reRegistro.compensacao_dt_falta;
            boOrigemEscala              := reRegistro.escala;
            boOrigemCalendario          := reRegistro.calendario;
            boOrigemGrade               := reRegistro.grade;
            boDiaTrabalho               := reRegistro.dia_trabalho;
            
            stCpTipo                    := reRegistro.descricao_tipo;
            stCpOrigem                  := reRegistro.descricao_origem;
        END LOOP;
        
        /*JUSTIFICATIVAS --*/
        stSql := '     SELECT substring(trim(justificativa.descricao) FROM 1 FOR 30)
                        FROM ponto'|| stEntidade ||'.relogio_ponto_justificativa
                  INNER JOIN ( SELECT cod_contrato
                                    , sequencia
                                    , cod_justificativa
                                    , max(timestamp) as timestamp
                                 FROM ponto'|| stEntidade ||'.relogio_ponto_justificativa
                             GROUP BY cod_contrato
                                    , sequencia
                                    , cod_justificativa) as max_relogio_ponto_justificativa
                          ON relogio_ponto_justificativa.cod_contrato = max_relogio_ponto_justificativa.cod_contrato
                         AND relogio_ponto_justificativa.cod_justificativa = max_relogio_ponto_justificativa.cod_justificativa
                         AND relogio_ponto_justificativa.sequencia = max_relogio_ponto_justificativa.sequencia
                         AND relogio_ponto_justificativa.timestamp = max_relogio_ponto_justificativa.timestamp
                  INNER JOIN ponto'|| stEntidade ||'.justificativa
                          ON relogio_ponto_justificativa.cod_justificativa = justificativa.cod_justificativa
                   LEFT JOIN ponto'|| stEntidade ||'.justificativa_horas
                          ON justificativa.cod_justificativa = justificativa_horas.cod_justificativa
                       WHERE relogio_ponto_justificativa.cod_contrato = '|| inCodContrato ||'
                         AND TO_DATE('|| quote_literal(stCpData) ||', ''dd/mm/yyyy'') between relogio_ponto_justificativa.periodo_inicio AND relogio_ponto_justificativa.periodo_termino
                         AND NOT EXISTS (SELECT 1
                                           FROM ponto'|| stEntidade ||'.relogio_ponto_justificativa_exclusao
                                          WHERE relogio_ponto_justificativa.cod_contrato = relogio_ponto_justificativa_exclusao.cod_contrato
                                            AND relogio_ponto_justificativa.cod_justificativa = relogio_ponto_justificativa_exclusao.cod_justificativa
                                            AND relogio_ponto_justificativa.sequencia = relogio_ponto_justificativa_exclusao.sequencia
                                            AND relogio_ponto_justificativa.timestamp = relogio_ponto_justificativa_exclusao.timestamp)';
                         
        stAux := selectIntoVarchar(stSql);
        
        IF stAux IS NULL THEN
            stSql := 'SELECT substring(trim(assentamento_assentamento.descricao) FROM 1 FOR 30)
                        FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                    INNER JOIN pessoal'|| stEntidade ||'.assentamento_gerado
                            ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                    INNER JOIN ( SELECT cod_assentamento_gerado
                                    , MAX(timestamp) as timestamp
                                FROM pessoal'|| stEntidade ||'.assentamento_gerado
                            GROUP BY cod_assentamento_gerado ) as max_assentamento_gerado
                            ON assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                        AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                    INNER JOIN pessoal'|| stEntidade ||'.assentamento_assentamento
                            ON assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                    INNER JOIN pessoal'|| stEntidade ||'.classificacao_assentamento
                            ON assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao                    
                    INNER JOIN pessoal'|| stEntidade ||'.tipo_classificacao                                                           
                            ON classificacao_assentamento.cod_tipo = tipo_classificacao.cod_tipo                                             
                        WHERE assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                        AND classificacao_assentamento.cod_tipo = 2  --tipo de classificação para assentamento de afstamento temporario
                        AND TO_DATE('|| quote_literal(stCpData) ||', ''dd/mm/yyyy'') between assentamento_gerado.periodo_inicial AND assentamento_gerado.periodo_final
                        AND NOT EXISTS (  SELECT 1
                                            FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                           WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                             AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)';
            stAux := selectIntoVarchar(stSql);
        END IF;
        
        IF stAux IS NOT NULL THEN
            stCpJustificativa := stAux;
        END IF;
        
        
        /* DETERMINA SE HORARIO REFERENCIA = PROVENIENTE DE GRADE OU ESCALA 
         * verifica se o tipo de dia foi proveniente de uma compensacao com dia de trabalho para a data corrente. 
         * se sim, busca a escala tipo trabalho / grade de horários do dia da falta da compensacao cadastrada.
         *
         * caso a origem do dia nao seja compensacao, busca escala / grade de horarios do dia.
         * 
         * caso nao exista para o dia escala / grade de horarios, todas as horas deverao ser contabilizadas como extras
         */
         
        boDadosHorariosEscala := false;
        boDadosHorariosGrade  := false;
        
        arHorariosGradeEscala[1] := '';
        arHorariosGradeEscala[2] := '';
        arHorariosGradeEscala[3] := '';
        arHorariosGradeEscala[4] := '';
        
        IF (boOrigemCompensacao IS TRUE AND boDiaTrabalho IS TRUE) OR
           boOrigemEscala IS TRUE OR
           boOrigemGrade IS TRUE THEN
        
            IF boOrigemCompensacao IS TRUE AND boDiaTrabalho IS TRUE THEN
               stAux := stOrigemCompensacaoDtFalta;
            ELSE
               stAux := stCpData;
            END IF;
    
            /* recupera horarios de escala do contrato para a data */
            stSql := '  SELECT to_char(escala_turno.hora_entrada_1, ''hh24:mi'') as hora_entrada_1
                             , to_char(escala_turno.hora_entrada_2, ''hh24:mi'') as hora_entrada_2
                             , to_char(escala_turno.hora_saida_1,   ''hh24:mi'') as hora_saida_1
                             , to_char(escala_turno.hora_saida_2,   ''hh24:mi'') as hora_saida_2
                          FROM ponto'|| stEntidade ||'.escala
                          JOIN ponto'|| stEntidade ||'.escala_turno
                            ON (escala_turno.cod_escala = escala.cod_escala AND
                                escala_turno.timestamp = escala.ultimo_timestamp)
                          JOIN ponto'|| stEntidade ||'.escala_contrato
                            ON (escala.cod_escala = escala_contrato.cod_escala)
                          JOIN ( SELECT cod_contrato
                                      , cod_escala
                                      , max(timestamp) as timestamp
                                   FROM ponto'|| stEntidade ||'.escala_contrato
                               GROUP BY cod_contrato
                                      , cod_escala
                               ) as max_escala_contrato
                            ON (max_escala_contrato.cod_contrato = escala_contrato.cod_contrato AND
                                max_escala_contrato.cod_escala = escala_contrato.cod_escala AND
                                max_escala_contrato.timestamp = escala_contrato.timestamp
                                )
                         WHERE NOT EXISTS (SELECT 1 
                                             FROM ponto'|| stEntidade ||'.escala_contrato_exclusao
                                            WHERE escala_contrato_exclusao.cod_contrato = max_escala_contrato.cod_contrato 
                                              AND escala_contrato_exclusao.cod_escala = max_escala_contrato.cod_escala
                                              AND escala_contrato_exclusao.timestamp = max_escala_contrato.timestamp)
                               AND NOT EXISTS (SELECT 1
                                                 FROM ponto'|| stEntidade ||'.escala_exclusao
                                                WHERE escala_exclusao.cod_escala = escala.cod_escala)
                               AND escala_contrato.cod_contrato = '|| inCodContrato ||'
                               AND escala_turno.dt_turno = TO_DATE('|| quote_literal(stAux) ||', ''dd/mm/yyyy'')';
                               
            FOR reRegistro IN  EXECUTE stSql
            LOOP
                arHorariosGradeEscala[1] := reRegistro.hora_entrada_1;
                arHorariosGradeEscala[2] := reRegistro.hora_saida_1;
                
                IF reRegistro.hora_entrada_2 <> '00:00' THEN
                    arHorariosGradeEscala[3] := reRegistro.hora_entrada_2;
                END IF;
                
                IF reRegistro.hora_saida_2 <> '00:00' THEN
                    arHorariosGradeEscala[4] := reRegistro.hora_saida_2;
                END IF;
                
                boDadosHorariosEscala := true;
            END LOOP;
            
            /* caso nao existam daods de escala para o dia, verifica existencia de grade */
            
            IF boDadosHorariosEscala IS FALSE THEN
    
                /* recupera os horarios da grade para o dia da semana (day of week + 1 ou dias_turno.cod_dia) especificado
                   como a grade tende a ser bastante utilizada no relatorio armazena em uma matriz indexada por cod_dia
                   nao limpa a matriz ate o final da pl
                   
                   arHorariosGrade[inIndex][1] - primeiro indice sinaliza se ja foi preenchida ou nao, para o 
                    do dia da semana ser verificado varias vezes e nao ter horarios especificados na grade
                 */
                inIndex := extract(dow FROM TO_DATE(stAux, 'dd/mm/yyyy'))+1;
                
                IF arHorariosGrade[inIndex][1] = '' THEN
                    /* BUSCA HORARIOS DA GRADE DO SERVIDOR */
                    stSql := 'SELECT to_char(faixa_turno.hora_entrada,   ''hh24:mi'') as hora_entrada_1
                                   , to_char(faixa_turno.hora_saida,     ''hh24:mi'') as hora_saida_1
                                   , to_char(faixa_turno.hora_entrada_2, ''hh24:mi'') as hora_entrada_2
                                   , to_char(faixa_turno.hora_saida_2,   ''hh24:mi'') as hora_saida_2
                                FROM pessoal'|| stEntidade ||'.faixa_turno
                                JOIN (  SELECT cod_grade
                                             , max(timestamp) as timestamp
                                          FROM pessoal'|| stEntidade ||'.faixa_turno
                                      GROUP BY cod_grade) as max_faixa_turno
                                  ON (     faixa_turno.cod_grade = max_faixa_turno.cod_grade 
                                       AND faixa_turno.timestamp = max_faixa_turno.timestamp)
                               WHERE faixa_turno.cod_grade = '|| inCodGrade ||'
                                 AND faixa_turno.cod_dia = '|| inIndex;
                        
                    arHorariosGrade[inIndex][1] := 'preenchido';
                    FOR reRegistro IN  EXECUTE stSql
                    LOOP
                        arHorariosGrade[inIndex][2] := reRegistro.hora_entrada_1;
                        arHorariosGrade[inIndex][3] := reRegistro.hora_saida_1;
                        arHorariosGrade[inIndex][4] := reRegistro.hora_entrada_2;
                        arHorariosGrade[inIndex][5] := reRegistro.hora_saida_2;
                    END LOOP;
                END IF;
                
                IF arHorariosGrade[inIndex][2] <> '' THEN
                    boDadosHorariosGrade := true;
                END IF;
                
                /* ignora o primeiro indice da matriz que sinaliza flag de preenchimento */
                arHorariosGradeEscala[1] := arHorariosGrade[inIndex][2];
                arHorariosGradeEscala[2] := arHorariosGrade[inIndex][3];
                arHorariosGradeEscala[3] := arHorariosGrade[inIndex][4];
                arHorariosGradeEscala[4] := arHorariosGrade[inIndex][5];
            END IF;
            
        END IF;/* verificacao de horarios */
        
        stHorariosGradeEscala := '';
        stHorariosGradeEscalaAntesPrimeiroTurno := '00:00 - ';
        stHorariosGradeEscalaDepoisSegundoTurno := ' - 23:59';
        stHorariosGradeEscalaIntervalo := '';
        
        /* se existe justificativa cadastrada e existem horarios batidos calcula todos como extra */
        IF stCpJustificativa IS NOT NULL THEN
            arHorariosGradeEscala[1] := '';
            arHorariosGradeEscala[2] := '';
            arHorariosGradeEscala[3] := '';
            arHorariosGradeEscala[4] := '';
        END IF;
        
        inIndex := 1;
        WHILE NOT arHorariosGradeEscala[inIndex] IS NULL AND 
              arHorariosGradeEscala[inIndex] <> '' LOOP
            stHorariosGradeEscala := stHorariosGradeEscala ||' - '|| arHorariosGradeEscala[inIndex];
            
            IF inIndex = 1 THEN
                stHorariosGradeEscalaAntesPrimeiroTurno := stHorariosGradeEscalaAntesPrimeiroTurno||arHorariosGradeEscala[inIndex];
            END IF;
            
            IF inIndex = 2 OR inIndex = 3 THEN
                stHorariosGradeEscalaIntervalo := stHorariosGradeEscalaIntervalo ||' - '|| arHorariosGradeEscala[inIndex];
            END IF;
            
            IF inIndex = 2 OR inIndex = 4 THEN
                IF inIndex = 4 THEN
                    stHorariosGradeEscalaIntervalo := substring(stHorariosGradeEscalaIntervalo FROM 4);
                    stHorariosGradeEscalaDepoisSegundoTurno := ' - 23:59';
                END IF;
                stHorariosGradeEscalaDepoisSegundoTurno := arHorariosGradeEscala[inIndex]||stHorariosGradeEscalaDepoisSegundoTurno;
            END IF;
            
            inIndex := inIndex + 1;
        END LOOP;
        
        stHorariosGradeEscala := substring(stHorariosGradeEscala FROM 4);        
        
        /* se dia de folga ou dia com justificativa, seta para pegar horarios do dia todo. usado posteriormente para calcular extras */
        IF boDiaTrabalho IS FALSE OR stCpJustificativa IS NOT NULL THEN
            stHorariosGradeEscalaAntesPrimeiroTurno := '00:00 - 12:00';
            stHorariosGradeEscalaDepoisSegundoTurno := '12:00 - 23:59';
        ELSE
            IF inIndex = 3 THEN /* nao existe segundo turno - elimina horarios escala intervalo */
                stHorariosGradeEscalaIntervalo := '';
            END IF;
        END IF;
        
        /* BUSCA DADOS DE HORARIOS IMPORTACAO PONTO OU MANUTENCAO PONTO */
        boAutorizarHorasExtras := false;
        boDadosManutencao      := false;
        boDadosImportacao      := false;
        
        /* limpa o array temporario de horarios de importacao e manutencao */
        arHorariosImpManut := '{}';
        
        /* busca horarios de tabelas de manutenção do ponto para o dia escolhido. caso contrário irá buscar diretamente da importacao do ponto */
        stSql := '  SELECT to_char(relogio_ponto_horario.hora, ''HH24:mi'') as horario
                         , dados_relogio_ponto_extras.autorizar_horas_extras
                      FROM ponto'|| stEntidade ||'.dados_relogio_ponto
                      JOIN ponto'|| stEntidade ||'.dados_relogio_ponto_extras
                        ON dados_relogio_ponto_extras.cod_contrato = dados_relogio_ponto.cod_contrato

                      JOIN (   SELECT cod_contrato
                                    , max(timestamp) as timestamp
                                 FROM ponto'|| stEntidade ||'.dados_relogio_ponto_extras
                             GROUP BY cod_contrato) as max_dados_relogio_ponto_extras
                        ON dados_relogio_ponto_extras.cod_contrato = max_dados_relogio_ponto_extras.cod_contrato
                       AND dados_relogio_ponto_extras.timestamp = max_dados_relogio_ponto_extras.timestamp
                        
                      JOIN ponto'|| stEntidade ||'.relogio_ponto_dias
                        ON relogio_ponto_dias.cod_contrato = dados_relogio_ponto.cod_contrato
                             
                      JOIN ponto'|| stEntidade ||'.relogio_ponto_horario
                        ON relogio_ponto_horario.cod_ponto        = relogio_ponto_dias.cod_ponto
                       AND relogio_ponto_horario.cod_contrato     = relogio_ponto_dias.cod_contrato

                      JOIN (   SELECT cod_contrato
                                    , cod_ponto
                                    , cod_horario
                                    , max(timestamp) as timestamp
                                 FROM ponto'|| stEntidade ||'.relogio_ponto_horario
                             GROUP BY cod_contrato
                                     , cod_ponto
                                     , cod_horario) as max_relogio_ponto_horario
                        ON relogio_ponto_horario.cod_contrato = max_relogio_ponto_horario.cod_contrato
                       AND relogio_ponto_horario.cod_ponto = max_relogio_ponto_horario.cod_ponto
                       AND relogio_ponto_horario.cod_horario = max_relogio_ponto_horario.cod_horario
                       AND relogio_ponto_horario.timestamp = max_relogio_ponto_horario.timestamp
                            
                     WHERE relogio_ponto_dias.cod_contrato = '|| inCodContrato ||'
                       AND relogio_ponto_dias.dt_ponto = TO_DATE('|| quote_literal(stCpData) ||', ''dd/mm/yyyy'')
                  ORDER BY horario ASC';
                       
        inIndex := 0;
        FOR reRegistro IN EXECUTE stSql LOOP
            IF inIndex = 0 THEN
                inIndex := 1;
            END IF;
            boAutorizarHorasExtras      := reRegistro.autorizar_horas_extras;
            arHorariosImpManut[inIndex] := reRegistro.horario;
            inIndex := inIndex + 1;
            boDadosManutencao := true;
        END LOOP;
        
        /* caso nao tenha encontrato horarios para o dia na manutenção do ponto, busca diretamente das tabelas de importacao */
        IF inIndex = 0 THEN
            boAutorizarHorasExtras := false;
            stSql := '  SELECT to_char(importacao_ponto_horario.horario,  ''HH24:mi'') as horario
                          FROM ponto'|| stEntidade ||'.importacao_ponto
                          JOIN ponto'|| stEntidade ||'.importacao_ponto_horario
                            ON (    importacao_ponto.cod_ponto      = importacao_ponto_horario.cod_ponto 
                                AND importacao_ponto.cod_contrato   = importacao_ponto_horario.cod_contrato
                                AND importacao_ponto.cod_importacao = importacao_ponto_horario.cod_importacao )
                         WHERE importacao_ponto.cod_contrato = '|| inCodContrato ||'
                           AND importacao_ponto.dt_ponto = TO_DATE('|| quote_literal(stCpData) ||', ''dd/mm/yyyy'')
                      ORDER BY horario ASC';
                         
            inIndex := 0;
            FOR reRegistro IN EXECUTE stSql LOOP
                IF inIndex = 0 THEN
                    inIndex := 1;
                END IF;
                arHorariosImpManut[inIndex] := reRegistro.horario;
                inIndex := inIndex + 1;
                boDadosImportacao := true;
            END LOOP;
        END IF;
        
        /* SETA O VALOR DE RETORNO PARA A COLUNA HORARIOS, BASEADO NOS DADOS DE MANUTENCAO PONTO / IMPORTACAO */
        stAux   := '';
        inIndex := 1;
        WHILE NOT arHorariosImpManut[inIndex] IS NULL LOOP
            stAux := stAux || ' - ' || arHorariosImpManut[inIndex];
            inIndex := inIndex + 1;
        END LOOP;
        stAux := substring(stAux FROM 4);
        IF NOT stAux IS NULL THEN
            stCpHorarios := stAux;
        END IF;
        
        
        /* TRATAMENTO DE DADOS PONTO PARA CALCULAR HORAS TRABALHADAS */
        
        /* limpa o array que armazenará os horarios validados - arredondados e descartados */
        arHorariosCalculoHsTrab := '{}';
        arTemp := '{}';
        
        IF (boDadosImportacao IS TRUE OR boDadosManutencao IS TRUE) THEN
           
            /* trata a diretiva de configuracao para descarte de remarcacoes consecutivas em um periodo de tempo a partir 
             * do primeiro horario presente nos dados de manutencao ou importacao */
           IF reRegistroConfiguracao.remarcacoes_minutos IS NOT NULL THEN
               inIndex := 1;
               inAux   := 1;
               WHILE NOT arHorariosImpManut[inIndex] IS NULL LOOP
                    IF inIndex > 1 THEN
                        stSql := 'SELECT time '|| quote_literal(arHorariosImpManut[inIndex]) ||' > interval '|| quote_literal(reRegistroConfiguracao.remarcacoes_minutos ||' minutes') ||' + interval '|| quote_literal(arHorariosImpManut[inIndex-inAux]) ||' ';
                        stAux := selectIntoVarchar(stSql);
                        IF stAux = 't' THEN
                            arTemp := array_append(arTemp, arHorariosImpManut[inIndex]);
                            inAux := 1;
                        ELSE
                            inAux := inAux + 1;
                        END IF;
                    ELSEIF inIndex = 1 THEN
                        arTemp := array_append(arTemp, arHorariosImpManut[inIndex]);
                    END IF;
                    inIndex := inIndex + 1;
               END LOOP;
               arHorariosCalculoHsTrab := arTemp;
            ELSE
                arHorariosCalculoHsTrab := arHorariosImpManut;
            END IF;
            
            /* trata a diretiva de configuracao para arredondamento de horarios arredondamento de horarios
             *
             * efetua arredondamento caso horario real batida ponto estiver entre horario referencia batida + intervalo 
             * ou horario referencia batida - intervalo
             *
             * horario referencia = hora entrada 1 = 08:00
             * intervalo = 5 min
             * ex. 08:35 (dado ponto) entre 08:00 - 5min ou 08:00 + 5min = nao arredonda
             * ex. 08:02 (dado ponto) entre 08:00 - 5min ou 08:00 + 5min = arredonda p/ 08:00
             */
             
            IF boDadosHorariosEscala IS TRUE OR boDadosHorariosGrade IS TRUE THEN
            
                IF reRegistroConfiguracao.arredondar_hora_entrada1 IS NOT NULL AND 
                   arHorariosCalculoHsTrab[1] IS NOT NULL THEN
                   
                   stSql := '   SELECT time '|| quote_literal(arHorariosCalculoHsTrab[1]) ||' 
                               BETWEEN (interval '|| quote_literal(arHorariosGradeEscala[1]) ||' - interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_entrada1) ||') 
                                   AND (interval '|| quote_literal(arHorariosGradeEscala[1]) ||' + interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_entrada1) ||')';
                   stAux := selectIntoVarchar(stSql);
                   IF stAux = 't' THEN
                        arHorariosCalculoHsTrab[1] := arHorariosGradeEscala[1];
                   END IF;
                END IF;
                
                IF reRegistroConfiguracao.arredondar_hora_saida1 IS NOT NULL AND 
                   arHorariosCalculoHsTrab[2] IS NOT NULL THEN
                   
                   stSql := '   SELECT time '|| quote_literal(arHorariosCalculoHsTrab[2]) ||'
                               BETWEEN (interval '|| quote_literal(arHorariosGradeEscala[2]) ||' - interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_saida1) ||') 
                                   AND (interval '|| quote_literal(arHorariosGradeEscala[2]) ||' + interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_saida1) ||')';
                   stAux := selectIntoVarchar(stSql);
                   IF stAux = 't' THEN
                        arHorariosCalculoHsTrab[2] := arHorariosGradeEscala[2];
                   END IF;
                END IF;
                
                IF reRegistroConfiguracao.arredondar_hora_entrada2 IS NOT NULL AND 
                   arHorariosCalculoHsTrab[3] IS NOT NULL AND 
                   arHorariosGradeEscala[3] <> '' THEN /* caso existam dados na grade, mas a entrada2 e/ou saida2 estejam vazias*/
                   
                   stSql := '   SELECT time '|| quote_literal(arHorariosCalculoHsTrab[3]) ||'
                               BETWEEN (interval '|| quote_literal(arHorariosGradeEscala[3]) ||' - interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_entrada2) ||') 
                                   AND (interval '|| quote_literal(arHorariosGradeEscala[3]) ||' + interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_entrada2) ||')';
                   stAux := selectIntoVarchar(stSql);
                   IF stAux = 't' THEN
                        arHorariosCalculoHsTrab[3] := arHorariosGradeEscala[3];
                   END IF;
                END IF;
                
                IF reRegistroConfiguracao.arredondar_hora_saida2 IS NOT NULL AND 
                   arHorariosCalculoHsTrab[4] IS NOT NULL AND 
                   arHorariosGradeEscala[4] <> '' THEN /* caso existam dados na grade, mas a entrada2 e/ou saida2 estejam vazias*/
                   
                   stSql := '   SELECT time '|| quote_literal(arHorariosCalculoHsTrab[4]) ||'
                               BETWEEN (interval '|| quote_literal(arHorariosGradeEscala[4]) ||' - interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_saida2) ||') 
                                   AND (interval '|| quote_literal(arHorariosGradeEscala[4]) ||' + interval '|| quote_literal(reRegistroConfiguracao.arredondar_hora_saida2) ||')';
                   stAux := selectIntoVarchar(stSql);
                   IF stAux = 't' THEN
                        arHorariosCalculoHsTrab[4] := arHorariosGradeEscala[4];
                   END IF;
                END IF;
                
            END IF;
            
        END IF;
        


        /* EFETUA CALCULO DAS HORAS QUE O CONTRATO EFETIVAMENTE CUMPRIU DURANTE O DIA E TRANSFORMA O VETOR DE HORAS TRAB EM PARES
         * baseado nos dados ja descartados e arredondados em vetor arHorariosCalculoHsTrab 
         *
         * aproveita para reescrever o vetor de intervalos trabalhados no dia para conter somente pares de entrada e saida
         *
         * ex. {08:00 12:00 13:00 .. ? } -> 08:00 12:00
         */
         
        intTotalHorasCumpridasDia := '00:00';
        arTemp := '{}';

        IF (boDadosImportacao IS TRUE OR boDadosManutencao IS TRUE) THEN
            inIndex := 1;
            stAux   := '';
            WHILE NOT arHorariosCalculoHsTrab[inIndex] IS NULL LOOP
                IF inIndex % 2 = 0 THEN
                    stAux := stAux || ' + ( interval '|| quote_literal(arHorariosCalculoHsTrab[inIndex]) ||' - interval '|| quote_literal(arHorariosCalculoHsTrab[inIndex-1]) ||' ) ';
                    arTemp[inIndex-1] := arHorariosCalculoHsTrab[inIndex-1];
                    arTemp[inIndex] := arHorariosCalculoHsTrab[inIndex];
                END IF;
                inIndex := inIndex + 1;
            END LOOP;
            IF stAux <> '' THEN
                stSql := 'SELECT to_char('|| substring(stAux FROM 3) ||', ''hh24:mi'')';
                intTotalHorasCumpridasDia := selectIntoVarchar(stSql);
            END IF;
        END IF;
        arHorariosCalculoHsTrab := arTemp;
        
        /* transforma o array de horarios em string */
        stHorariosCalculoHsTrab := '';
        inIndex := 1;
        WHILE NOT arHorariosCalculoHsTrab[inIndex] IS NULL LOOP
            stHorariosCalculoHsTrab := stHorariosCalculoHsTrab ||' - '|| arHorariosCalculoHsTrab[inIndex];
            inIndex := inIndex + 1;
        END LOOP;
        stHorariosCalculoHsTrab := substring(stHorariosCalculoHsTrab FROM 4);
        
        
        
        
        /* CALCULA O TOTAL DE HORAS DE TRABALHO REFERENCIA EM UM DIA DE TRABALHO CONFORME GRADE/ESCALA */
        intTotalHorasCargaHorariaDia := '00:00';
        
        IF stHorariosGradeEscala <> '' THEN /* caso nao seja dia trabalho, lancar tudo como hora extra*/
            
            stAux := '';
            IF arHorariosGradeEscala[3] <> '' THEN
                stAux := ' + (interval '|| quote_literal(arHorariosGradeEscala[4]) ||' - interval '|| quote_literal(arHorariosGradeEscala[3]) ||')';
            END IF;
            
            stSql := 'SELECT to_char( (interval '|| quote_literal(arHorariosGradeEscala[2]) ||' - interval '|| quote_literal(arHorariosGradeEscala[1]) ||') '|| stAux ||',''hh24:mi'');';
            intTotalHorasCargaHorariaDia := selectIntoVarchar(stSql);
        END IF;
        
        stCpCargaHoraria := intTotalHorasCargaHorariaDia;
        

        
        /* EFETUA O CALCULO DAS HORAS TRABALHADAS ENTRE A GRADE DE HORARIOS
         *
         * ocorre somente quando diaTrabalho = true. Quando diaTrabalho = false, horas cumpridas deverao ser jogadas em extras.
         *
         * Calcula o numero de horas existentes na manutencao do ponto / importacao que coincidem com a escala / grade (se houver escala / grade).
         * O numero de horas coincidentes, sero as horas trabalhadas
         *
         */
        
        intTotalHorasCargaHorariaCumpridasDia := '00:00';
        
        IF stHorariosGradeEscala <> '' AND stHorariosCalculoHsTrab <> '' THEN /* caso nao seja dia trabalho, lancar tudo como hora extra*/
           
                IF stHorariosCalculoHsTrab <> '' THEN
                    stSql := 'SELECT to_char(recuperaHorasTrabalhadasEmHorarioPadrao('''', 0, '''', '|| quote_literal(stHorariosGradeEscala) ||', '|| quote_literal(stHorariosCalculoHsTrab) ||')::interval, ''hh24:mi'')';
                    intTotalHorasCargaHorariaCumpridasDia := selectIntoVarchar(stSql);
                END IF;
        END IF;
        
        
        /* PREPARA O RETORNO DE HORAS TRABALHADAS CONFORME CONFIGURACAO 
         *
         * Situacoes possiveis
         *
         * Grade   |   Cumprido na Grade  |  Cumpridas no dia
         *  1h                0h                    0h
         *  1h                0h                    1h
         *  1h                0h                    2h
         *  1h                1h                    1h
         *  1h                1h                    2h
         */
         
        boCalcularExtras := false;
        boCalcularFaltas := false;
        
        /* se dia trabalho ou, se nao dia trabalho, mas feriado e trabalho feriado nao conta como extra verifica se existem horas extras ou 
          faltas a calcular */
        IF boDiaTrabalho IS TRUE OR
           (boDiaTrabalho IS FALSE AND boOrigemCalendario IS TRUE AND reRegistroConfiguracao.trabalho_feriado IS FALSE) 
            THEN
           
            stSql := 'SELECT interval '|| quote_literal(intTotalHorasCargaHorariaDia) ||' > '|| quote_literal(intTotalHorasCargaHorariaCumpridasDia) ||' AND
                             interval '|| quote_literal(intTotalHorasCargaHorariaDia) ||' > '|| quote_literal(intTotalHorasCumpridasDia) ||' ';
            stAux := selectIntoVarchar(stSql);
            
            IF stAux = 't' THEN
            
                stSql := 'SELECT interval '|| quote_literal(intTotalHorasCumpridasDia) ||' > '|| quote_literal(intTotalHorasCargaHorariaCumpridasDia) ||' ';
                stAux := selectIntoVarchar(stSql);
                
                IF stAux = 't' THEN
                    boCalcularExtras := true;
                END IF;
            
                intTotalHorasTrabalhadas := intTotalHorasCargaHorariaCumpridasDia;
                boCalcularFaltas := true;
            ELSE
                stSql := 'SELECT interval '|| quote_literal(intTotalHorasCargaHorariaDia) ||' = '|| quote_literal(intTotalHorasCargaHorariaCumpridasDia) ||' AND
                                 interval '|| quote_literal(intTotalHorasCargaHorariaDia) ||' < '|| quote_literal(intTotalHorasCumpridasDia) ||' ';
                stAux := selectIntoVarchar(stSql);
                
                IF stAux = 't' THEN
                    intTotalHorasTrabalhadas := intTotalHorasCargaHorariaDia;
                    boCalcularExtras := true;
                ELSE
                    intTotalHorasTrabalhadas := intTotalHorasCargaHorariaCumpridasDia;
                    boCalcularFaltas := true;
                    boCalcularExtras := true;
                END IF;
            END IF;
        ELSE 
            intTotalHorasTrabalhadas := intTotalHorasCargaHorariaCumpridasDia;
            boCalcularExtras := true;
        END IF;
        
        
        
        
        /* PREPARA COLUNA DE ATRASOS - FALTAS DIARIAS */
        IF boCalcularFaltas IS TRUE AND boDiaTrabalho IS TRUE THEN
        
            /* horas falta do dia corrente */
            intTemp := to_char(intTotalHorasCargaHorariaDia::interval - intTotalHorasCargaHorariaCumpridasDia::interval,'hh24:mi');
            
            IF reRegistroConfiguracao.limitar_atrasos IS TRUE THEN
            
                /* se limitar atrasos = true e atraso passar o limite de tolerancia, apresenta o limite de tolerancia */
                IF intTemp::interval > (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval THEN
                
                    intTotalHorasAtraso := to_char( (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval, 'hh24:mi');
                    
                    IF (intTemp::interval - (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval) > (reRegistroConfiguracao.tolerancia_faltas ||' minutes')::interval THEN
                        intTotalHorasFalta := to_char(intTemp::interval - (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval, 'hh24:mi');
                    END IF;
                END IF;                
                
            ELSE
                /* se limitar atrasos = false e atraso passar do limite tolerancia atrasos, joga todo o atraso 
                 * (caso ultrapasse o limite de tolerancia de faltas), para faltas 
                 */
                IF intTemp::interval > (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval THEN
                    IF intTemp::interval > (reRegistroConfiguracao.tolerancia_faltas ||' minutes')::interval THEN
                        intTotalHorasFalta := intTemp;
                    END IF;                    
                END IF;
            END IF;
            
            intTotalHorasAtrasoDaSemana := to_char(intTotalHorasAtrasoDaSemana::interval + intTemp::interval, 'hh24:mi');

        END IF;        
        
        /* PREPARA COLUNA ADICIONAL NOTURNO */
        /* verifica o quanto que dos horarios que o servidor efetuou, estavam em horas noturnas */
        IF stHorariosCalculoHsTrab <> '' THEN
            stSql := 'SELECT to_char(recuperaHorasTrabalhadasEmHorarioPadrao('''', 0, '''', '|| quote_literal(intHorarioAdicionalNoturnoMadrugada1 ||' - '|| intHorarioAdicionalNoturnoMadrugada2 ||' - '|| intHorarioAdicionalNoturnoNoite1 ||' - '|| intHorarioAdicionalNoturnoNoite2) ||', '|| quote_literal(stHorariosCalculoHsTrab) ||')::interval, ''hh24:mi'')';
            stAux := selectIntoVarchar(stSql);
            
            IF stAux::interval > '00:00'::interval THEN
                intTotalHorasAdicionalNoturno := to_char(stAux::interval * reRegistroConfiguracao.fator_multiplicacao,'hh24:mi');
                intTotalHorasAdicionalNoturnoDaSemana := to_char(intTotalHorasAdicionalNoturno::interval + intTotalHorasAdicionalNoturnoDaSemana::interval, 'hh24:mi');
            END IF;
        END IF;
        
        
        
        /* PREPARA COLUNA HORAS EXTRAS */
        IF boCalcularExtras IS TRUE AND 
           ((reRegistroConfiguracao.horas_extras_somente_com_autorizacao IS TRUE AND boAutorizarHorasExtras IS TRUE) OR 
           reRegistroConfiguracao.horas_extras_somente_com_autorizacao IS FALSE) THEN
           
            intTemp := '00:00';
            
            IF reRegistroConfiguracao.horas_extras_anterior IS TRUE THEN
                stSql := 'SELECT to_char(recuperaHorasTrabalhadasEmHorarioPadrao('''', 0, '''', '|| quote_literal(stHorariosGradeEscalaAntesPrimeiroTurno) ||', '|| quote_literal(stHorariosCalculoHsTrab) ||')::interval, ''hh24:mi'')';
                stAux := selectIntoVarchar(stSql);
                
                
                IF stAux <> '00:00' THEN
                    intTemp := to_char(intTemp::interval + stAux::interval, 'hh24:mi');
                END IF;
            END IF;
            
            IF reRegistroConfiguracao.horas_extras_intervalo IS TRUE AND 
               arHorariosGradeEscala[3] <> '' THEN
                stSql := 'SELECT to_char(recuperaHorasTrabalhadasEmHorarioPadrao('''', 0, '''', '|| quote_literal(stHorariosGradeEscalaIntervalo) ||', '|| quote_literal(stHorariosCalculoHsTrab) ||')::interval, ''hh24:mi'')';
                stAux := selectIntoVarchar(stSql);
                
                IF stAux <> '00:00' THEN
                    intTemp := to_char(intTemp::interval + stAux::interval, 'hh24:mi');
                END IF;
            END IF;
            
            IF reRegistroConfiguracao.horas_extras_posterior IS TRUE THEN
                stSql := 'SELECT to_char(recuperaHorasTrabalhadasEmHorarioPadrao('''', 0, '''', '|| quote_literal(stHorariosGradeEscalaDepoisSegundoTurno) ||', '|| quote_literal(stHorariosCalculoHsTrab) ||')::interval, ''hh24:mi'')';
                stAux := selectIntoVarchar(stSql);
                
                IF stAux <> '00:00' THEN
                    intTemp := to_char(intTemp::interval + stAux::interval, 'hh24:mi');
                END IF;
                
            END IF;
            
            /* se periodo diario, compara com limite tolerancia, senão adiciona as horasExtrasDaSemana */
            IF intTemp::interval > (reRegistroConfiguracao.horas_extras_minutos ||' minutes')::interval THEN 
                intTotalHorasExtras := intTemp;
            END IF;
            
            intTotalHorasExtrasDaSemana := to_char(intTemp::interval + intTotalHorasExtrasDaSemana::interval, 'hh24:mi');
            
            /* abono de horas falta e horas atraso para o dia */
            IF reRegistroConfiguracao.horas_extras_compensar_atrasos IS TRUE THEN
                IF intTotalHorasExtras::interval > intTotalHorasAtraso::interval THEN
                    intTotalHorasExtras := to_char(intTotalHorasExtras::interval - intTotalHorasAtraso::interval, 'hh24:mi');
                    intTotalHorasAtraso := '00:00';
                ELSE
                    intTotalHorasExtras := '00:00';
                    intTotalHorasAtraso := to_char(intTotalHorasAtraso::interval - intTotalHorasExtras::interval, 'hh24:mi');
                END IF;
            END IF;
            
            IF reRegistroConfiguracao.horas_extras_compensar_faltas IS TRUE THEN
                IF intTotalHorasExtras::interval > intTotalHorasFalta::interval THEN
                    intTotalHorasExtras := to_char(intTotalHorasExtras::interval - intTotalHorasFalta::interval, 'hh24:mi');
                    intTotalHorasFalta := '00:00';
                ELSE
                    intTotalHorasFalta := to_char(intTotalHorasFalta::interval - intTotalHorasExtras::interval, 'hh24:mi');
                    intTotalHorasExtras := '00:00';
                END IF;
            END IF;
            

             
            
            /* PREPARA COLUNA DE EXTRAS NOTURNOS 
             *
             * Hora extra noturna = horas extras, efetuadas em periodo noturno. Verifica, quantas horas o servidor efetuou em periodo noturno.
             */
            IF intTotalHorasExtras::interval > '00:00'::interval THEN
            
                /* Periodo Adicional Noturno */
                stAux := intHorarioAdicionalNoturnoMadrugada1 ||' - '|| intHorarioAdicionalNoturnoMadrugada2 ||' - '|| intHorarioAdicionalNoturnoNoite1 ||' - '|| intHorarioAdicionalNoturnoNoite2;
                intTemp := recuperaHorasExtrasNoturnasRelatorioEspelhoPonto(stHorariosCalculoHsTrab, stAux, stHorariosGradeEscala);
                
                IF intTemp::interval > '00:00'::interval THEN
                
                    intTotalHorasExtrasNoturnas := intTemp;
                    
                    /* como a opcao foi para separar, diminui do valor de horas extras a retornar da PL */
                    IF reRegistroConfiguracao.horas_extras_noturnas_somar IS FALSE THEN
                        intTotalHorasExtras         := to_char(intTotalHorasExtras::interval - intTotalHorasExtrasNoturnas::interval, 'hh24:mi');
                        
                        intTotalHorasExtrasDaSemana := to_char(intTotalHorasExtrasDaSemana::interval - intTotalHorasExtrasNoturnas::interval, 'hh24:mi');
                        intTotalHorasExtrasNoturnasDaSemana := to_char(intTotalHorasExtrasNoturnas::interval + intTotalHorasExtrasNoturnasDaSemana::interval, 'hh24:mi');
                    END IF;
                    
                    IF (reRegistroConfiguracao.horas_extras_noturnas_somar IS FALSE AND reRegistroConfiguracao.separar_adicional IS TRUE ) OR 
                       (reRegistroConfiguracao.horas_extras_noturnas_somar IS FALSE AND reRegistroConfiguracao.separar_adicional IS FALSE) THEN
                    intTotalHorasAdicionalNoturno := to_char(intTotalHorasAdicionalNoturno::interval - (intTotalHorasExtrasNoturnas::interval * reRegistroConfiguracao.fator_multiplicacao),'hh24:mi');
                    intTotalHorasAdicionalNoturnoDaSemana := to_char(intTotalHorasAdicionalNoturnoDaSemana::interval - (intTotalHorasExtrasNoturnas::interval * reRegistroConfiguracao.fator_multiplicacao), 'hh24:mi');
                    END IF;
                    
                    IF (reRegistroConfiguracao.horas_extras_noturnas_somar IS TRUE AND reRegistroConfiguracao.separar_adicional IS TRUE) OR
                       (reRegistroConfiguracao.horas_extras_noturnas_somar IS TRUE AND reRegistroConfiguracao.separar_adicional IS FALSE)
                       THEN
                        intTotalHorasExtrasNoturnas := NULL;
                    END IF;
                    
                END IF;            
            END IF;
        END IF;
        
        
        /* processa totalizadores semanais */
        IF reRegistroConfiguracao.tolerancia_periodo = 'S' AND 
           (inCodDiaSemana = 7 OR stCpData = dtFimPeriodo) THEN
           
            /* verifica limites de faltas e atrasos para a semana */
            IF intTotalHorasAtrasoDaSemana::interval > '00:00'::interval THEN
            
                IF reRegistroConfiguracao.limitar_atrasos IS TRUE THEN
                
                    /* se limitar atrasos = true e atraso passar o limite de tolerancia, apresenta o limite de tolerancia */
                    IF intTotalHorasAtrasoDaSemana::interval > (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval THEN
                    
                        IF (intTotalHorasAtrasoDaSemana::interval - (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval) > (reRegistroConfiguracao.tolerancia_faltas ||' minutes')::interval THEN
                            intTotalHorasFaltaDaSemana := to_char(intTotalHorasAtrasoDaSemana::interval - (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval, 'hh24:mi');
                        END IF;
                        
                        intTotalHorasAtrasoDaSemana := to_char( (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval, 'hh24:mi');
                    ELSE
                        intTotalHorasAtrasoDaSemana := '00:00';
                    END IF;                
                    
                ELSE
                    /* se limitar atrasos = false e atraso passar do limite tolerancia atrasos, joga todo o atraso 
                     * (caso ultrapasse o limite de tolerancia de faltas), para faltas 
                     */
                    IF intTotalHorasAtrasoDaSemana::interval > (reRegistroConfiguracao.tolerancia_atrasos ||' minutes')::interval THEN
                        IF intTotalHorasAtrasoDaSemana::interval > (reRegistroConfiguracao.tolerancia_faltas ||' minutes')::interval THEN
                            intTotalHorasFaltaDaSemana  := intTotalHorasAtrasoDaSemana;
                            intTotalHorasAtrasoDaSemana := '00:00';
                        ELSE
                            intTotalHorasAtrasoDaSemana := '00:00';
                        END IF;                    
                    ELSE
                        intTotalHorasAtrasoDaSemana := '00:00';
                    END IF;                
                END IF;
                
            END IF;
            
            /* verifica extras da semana. se horas extras > tolerancia apura extras da semana, senao descarta */
            IF intTotalHorasExtrasDaSemana::interval > (reRegistroConfiguracao.horas_extras_minutos ||' minutes')::interval THEN
            
                /* verifica os abonos de atraso para a semana */
                IF reRegistroConfiguracao.horas_extras_compensar_atrasos IS TRUE THEN
                    IF intTotalHorasExtrasDaSemana::interval > intTotalHorasAtrasoDaSemana::interval THEN
                        intTotalHorasExtrasDaSemana := to_char(intTotalHorasExtrasDaSemana::interval - intTotalHorasAtrasoDaSemana::interval, 'hh24:mi');
                        intTotalHorasAtrasoDaSemana := '00:00';
                    ELSE
                        intTotalHorasAtrasoDaSemana := to_char(intTotalHorasAtrasoDaSemana::interval - intTotalHorasExtrasDaSemana::interval, 'hh24:mi');
                        intTotalHorasExtrasDaSemana := '00:00';
                    END IF;
                END IF;
                
                /* verifica os abonos de falta para a semana */
                IF reRegistroConfiguracao.horas_extras_compensar_faltas IS TRUE THEN
                    IF intTotalHorasExtrasDaSemana::interval > intTotalHorasFaltaDaSemana::interval THEN
                        intTotalHorasExtrasDaSemana := to_char(intTotalHorasExtrasDaSemana::interval - intTotalHorasFaltaDaSemana::interval, 'hh24:mi');
                        intTotalHorasFaltaDaSemana := '00:00';
                    ELSE
                        intTotalHorasFaltaDaSemana  := to_char(intTotalHorasFaltaDaSemana::interval - intTotalHorasExtrasDaSemana::interval, 'hh24:mi');
                        intTotalHorasExtrasDaSemana := '00:00';
                    END IF;
                END IF;
                
            ELSE
                intTotalHorasExtrasDaSemana := NULL;
                intTotalHorasExtrasNoturnasDaSemana := NULL;
            END IF;
            
        END IF;
        
        
        /*-- RETORNO --*/
        
        stCpHsTrab  := intTotalHorasTrabalhadas;
        IF reRegistroConfiguracao.tolerancia_periodo = 'D' THEN
        
            stCpAdNot   := intTotalHorasAdicionalNoturno;
            stCpExtras  := intTotalHorasExtras;
            stCpExtNot  := intTotalHorasExtrasNoturnas;
            stCpAtrasos := intTotalHorasAtraso;
            stCpFaltas  := intTotalHorasFalta;
            
        ELSEIF inCodDiaSemana = 7 OR stCpData = dtFimPeriodo THEN
        
            stCpAdNot   := intTotalHorasAdicionalNoturnoDaSemana;
            stCpExtras  := intTotalHorasExtrasDaSemana;
            stCpExtNot  := intTotalHorasExtrasNoturnasDaSemana;
            stCpAtrasos := intTotalHorasAtrasoDaSemana;
            stCpFaltas  := intTotalHorasFaltaDaSemana;
            
        END IF;
        
        /* PREPARA COLUNA HORAS TOTAIS */
        stSql := 'interval '|| quote_literal(COALESCE(stCpHsTrab, '00:00')) ||' + 
                  interval '|| quote_literal(COALESCE(stCpExtras,'00:00')) ||' + 
                  interval '|| quote_literal(COALESCE(stCpExtNot,'00:00')) ||' - 
                  ( interval '|| quote_literal(COALESCE(stCpAtrasos,'00:00')) ||' + 
                  interval '|| quote_literal(COALESCE(stCpFaltas,'00:00')) ||')';
        
        stSql := 'SELECT CASE WHEN '|| stSql ||' >= interval ''00:00'' THEN to_char('|| stSql ||',''hh24:mi'') ELSE ''-''|| to_char(('|| stSql ||')*-1,''hh24:mi'') END';
        stCpHsTot := selectIntoVarchar(stSql);       
        
        /* INSERE REGISTROS EM TABELA TEMPORARIA DO ESPELHO PONTO */
        stSql := 'INSERT INTO ponto'|| stEntidade ||'.relatorio_espelho_ponto VALUES (
                    '|| inCodContrato ||',
                    '|| inSequencial ||',';
                    
        IF stCpData IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpData) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
        
        IF stCpDia IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpDia) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
        
        IF stCpTipo IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpTipo) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
                    
        IF stCpOrigem IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpOrigem) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
                    
        IF stCpHorarios IS NOT NULL THEN
            stSql := stSql ||''|| quote_literal(stCpHorarios) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
                    
        IF stCpJustificativa IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpJustificativa) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
                    
        IF stCpCargaHoraria IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpCargaHoraria) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
                    
        IF stCpHsTrab IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpHsTrab) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    

        IF stCpAdNot IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpAdNot) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    

        IF stCpExtras IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpExtras) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
        
        IF stCpExtNot IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpExtNot) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    

        IF stCpAtrasos IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpAtrasos) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    

        IF stCpFaltas IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpFaltas) ||',';
        ELSE
            stSql := stSql ||'null,';
        END IF;                    
        
        IF stCpHsTot IS NOT NULL THEN
            stSql := stSql ||' '|| quote_literal(stCpHsTot) ||')';
        ELSE
            stSql := stSql ||'null)';
        END IF;
        
        EXECUTE stSql;
        
        inSequencial := inSequencial + 1;
        
        rwCartaoPonto.dsr           := boCpDsr;
        rwCartaoPonto.data          := stCpData;
        rwCartaoPonto.dia           := stCpDia;
        rwCartaoPonto.tipo          := stCpTipo;
        rwCartaoPonto.origem        := stCpOrigem;
        rwCartaoPonto.horarios      := stCpHorarios;
        rwCartaoPonto.justificativa := stCpJustificativa;
        rwCartaoPonto.carga_horaria := stCpCargaHoraria;
        rwCartaoPonto.hs_trab       := stCpHsTrab;
        rwCartaoPonto.ad_not        := stCpAdNot;
        rwCartaoPonto.extras        := stCpExtras;
        rwCartaoPonto.ext_not       := stCpExtNot;
        rwCartaoPonto.atrasos       := stCpAtrasos;
        rwCartaoPonto.faltas        := stCpFaltas;
        rwCartaoPonto.hs_tot        := stCpHsTot;
        RETURN NEXT rwCartaoPonto;
    END LOOP;    
END;
$$ language 'plpgsql';

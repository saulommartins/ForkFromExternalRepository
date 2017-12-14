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
CREATE OR REPLACE FUNCTION  CALCULAFOLHA(INTEGER,INTEGER,BOOLEAN,VARCHAR,VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    inCodContratoParametro        ALIAS FOR $1;
    INCODCONFIGURACAO             ALIAS FOR $2;
    BOERRO                        ALIAS FOR $3;
    stEntidadeParametro        ALIAS FOR $4;
    stExercicioParametro          ALIAS FOR $5;
    BORETORNO                     BOOLEAN := TRUE;
    boRetornoFerias               BOOLEAN := FALSE;
    boRetornoDecimo               BOOLEAN := FALSE;
    INCODCONTRATO                 INTEGER;
    INCODESPECIALIDADE            INTEGER;
    INCODFUNCAO                   INTEGER;
    INCODPERIODOMOVIMENTACAO      INTEGER;
    INCODREGIME                   INTEGER;
    INCODSUBDIVISAO               INTEGER;
    INCODPREVIDENCIAOFICIAL       INTEGER;
    INCODSERVIDOR                 INTEGER;
    INNUMCGM                      INTEGER;
    INCODCONTRATOGERADORBENEFICIO INTEGER;
    inPensionista                 INTEGER := 0;
    INCONTROLE                    INTEGER := 1;
    inCodRegistro                 INTEGER;
    STDATAFINALCOMPETENCIA        VARCHAR := '';
    STTIPOFOLHA                   VARCHAR := 'S';
    stSql                         VARCHAR;
    stEntidade                 VARCHAR;
    stExercicioSistema            VARCHAR;
    stTimestamp                   TIMESTAMP:=now()::timestamp(3);
    stAutomatico                  VARCHAR;
    arCompetencia                 VARCHAR[];
    reRegistro                    RECORD;
    reRegistro2                   RECORD;
BEGIN
    boRetorno := removerTodosBuffers();
    stEntidade               := criarBufferTexto('stEntidade',stEntidadeParametro);
    INCODCONTRATOGERADORBENEFICIO := pega0ContratoDoGeradorBeneficio(inCodContratoParametro);
    IF INCODCONTRATOGERADORBENEFICIO IS NULL THEN
        inCodContrato := inCodContratoParametro;
        inPensionista := criarBufferInteiro('inPensionista',0);
    ELSE
        inCodContrato := INCODCONTRATOGERADORBENEFICIO;
        inPensionista := criarBufferInteiro('inPensionista',1);
    END IF;
    INCODPERIODOMOVIMENTACAO    := PEGA0CODIGOPERIODOMOVIMENTACAOABERTA(  ); 
    STDATAFINALCOMPETENCIA      := PEGA0DATAFINALCOMPETENCIADOPERIODOMOVIMENTO(  INCODPERIODOMOVIMENTACAO  ); 


    --INÍCIO DO CÓDIGO PARA CALCULAR FÉRIAS
    --Código que verifica a existencia de concessão de férias para o contrato que está sendo calculado
    --na competência atual. Caso haja ferias a serem pagas dentro da folha salário, será calculado
    --a folha férias, depois a folha salário e depois os registros e eventos calculados de férias
    --seram transferidos para a folha salário.
    IF INCODCONTRATOGERADORBENEFICIO IS NULL THEN
        arCompetencia := string_to_array(STDATAFINALCOMPETENCIA,'-');
        stSql := 'SELECT TRUE as boRetorno
                    FROM pessoal'||stEntidadeParametro||'.lancamento_ferias
                       , pessoal'||stEntidadeParametro||'.ferias
                   WHERE lancamento_ferias.cod_ferias = ferias.cod_ferias
                     AND cod_tipo = 2
                     AND cod_contrato = '||INCODCONTRATO||'
                     AND ((to_char(dt_inicio, ''mm/yyyy'')='''||arCompetencia[2]||'/'||arCompetencia[1]||''' OR to_char(dt_fim,''mm/yyyy'')= '''||arCompetencia[2]||'/'||arCompetencia[1]||''')
                      OR  (mes_competencia ='||quote_literal(arCompetencia[2])||' AND ano_competencia ='||quote_literal(arCompetencia[1])||'))';
        boRetornoFerias := selectIntoBoolean(stSql);
        IF boRetornoFerias IS TRUE THEN
            BORETORNO := deletarInformacoesCalculo(INCODCONTRATO::varchar,'F',0,stEntidadeParametro);
            BORETORNO := calculaFolhaFerias(INCODCONTRATO,BOERRO,stEntidadeParametro,stExercicioParametro);
        END IF;
    END IF;
    --FIM DO CÓDIGO PARA CALCULAR FÉRIAS

    --INÍCIO DO CÓDIGO PARA CALCULAR DÉCIMO
    --Código que verifica a existência de concessão de décimo terceiro (adiantamento apenas)
    --com pagamento em folha salário. Caso haja décimo a ser pago dentro da folha salário, será calculado
    --a folha décimo, depois a folha salário e depois os registros e eventos calculados de décimo
    --seram transferidos para a folha salário.
    stSql := 'SELECT TRUE AS boRetorno
                FROM folhapagamento'||stEntidadeParametro||'.concessao_decimo
               WHERE concessao_decimo.cod_contrato = '||INCODCONTRATO||'
                 AND concessao_decimo.cod_periodo_movimentacao = '||INCODPERIODOMOVIMENTACAO||'
                 AND concessao_decimo.desdobramento = ''A''
                 AND concessao_decimo.folha_salario IS TRUE';
    boRetornoDecimo := selectIntoBoolean(stSql);
    IF boRetornoDecimo IS TRUE THEN
        BORETORNO := deletarInformacoesCalculo(INCODCONTRATO::varchar,'D',0,stEntidadeParametro);
        BORETORNO := calculaFolhaDecimo(INCODCONTRATO,'A',BOERRO,stEntidadeParametro,stExercicioParametro);
    END IF;
    --FIM DO CÓDIGO PARA CALCULAR DÉCIMO

    --Analise do boRetorno para verificar se houve erro no calculo das férias
    IF BORETORNO IS TRUE THEN
        stEntidade               := criarBufferEntidade(stEntidadeParametro);
        INCODFUNCAO                 := PEGA0FUNCAODOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  ); 
        IF inCodFuncao IS NOT NULL THEN
            inCodContrato               := CriarBufferInteiro('inCodContrato',inCodContrato);
            STDATAFINALCOMPETENCIA      := CRIARBUFFERTEXTO(  'stDataFinalCompetencia' , STDATAFINALCOMPETENCIA  ); 
            INCODPERIODOMOVIMENTACAO    := CRIARBUFFERINTEIRO(  'inCodPeriodoMovimentacao' , INCODPERIODOMOVIMENTACAO  ); 
            stExercicioSistema          := criarBufferTexto('stExercicioSistema',stExercicioParametro);
            STTIPOFOLHA                 := CRIARBUFFERTEXTO('stTipoFolha',STTIPOFOLHA);
            INCODREGIME                 := PEGA0REGIMEDOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  ); 
            INCODREGIME                 := CRIARBUFFERINTEIRO(  'inCodRegime' , INCODREGIME  ); 
            INCODSUBDIVISAO             := PEGA0SUBDIVISAODOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  ); 
            INCODSUBDIVISAO             := CRIARBUFFERINTEIRO(  'inCodSubDivisao' , INCODSUBDIVISAO  );     
            INCODFUNCAO                 := CRIARBUFFERINTEIRO(  'inCodFuncao' , INCODFUNCAO  ); 
            INCODESPECIALIDADE          := PEGA0ESPECIALIDADEDOCONTRATONADATA(  INCODCONTRATO , STDATAFINALCOMPETENCIA  ); 
            INCODESPECIALIDADE          := CRIARBUFFERINTEIRO(  'inCodEspecialidade' , INCODESPECIALIDADE  ); 
            INCODSERVIDOR               := PEGA0SERVIDORDOCONTRATO(INCODCONTRATO);
            INCODSERVIDOR               := CRIARBUFFERINTEIRO('inCodServidor',INCODSERVIDOR);
            INNUMCGM                    := PEGA0NUMCGMSERVIDOR(INCODSERVIDOR);
            INNUMCGM                    := CRIARBUFFERINTEIRO('inNumCgm',INNUMCGM);
            IF INCODCONTRATOGERADORBENEFICIO IS NOT NULL THEN
                inCodContrato := criarBufferInteiro('inCodContrato',inCodContratoParametro);
            END IF;
            INCODPREVIDENCIAOFICIAL     := PEGA1PREVIDENCIAOFICIALDOCONTRATO();
            INCODPREVIDENCIAOFICIAL     := CRIARBUFFERINTEIRO('inCodPrevidenciaOficial',INCODPREVIDENCIAOFICIAL);
    
            --Varável utilizada no controle para a função pegaValorCalculadoFixo
            --Esta variável controla se o valor será gravado em banco ou apenas em memória
            INCONTROLE  := CRIARBUFFERINTEIRO(  'inControle', INCONTROLE  );
    
            BORETORNO := PROCESSAREVENTOSAUTOMATICOS();
            BORETORNO := CRIARTEMPORARIAREGISTROSFIXOS();
            IF BOERRO IS TRUE THEN
                BORETORNO := CALCULAEVENTOPORCONTRATOERRO(INCODCONTRATO);
            ELSE
                BORETORNO := CALCULAEVENTOPORCONTRATO(INCODCONTRATO);
            END IF;
    
    
            --INÍCIO DO CÓDIGO PARA INCORPORAR OS REGISTROS E EVENTOS CALCULADOS DE FÉRIAS EM SALÁRIO
            IF INCODCONTRATOGERADORBENEFICIO IS NULL THEN
                IF boRetornoFerias IS TRUE THEN
                    stSql := 'SELECT registro_evento_ferias.cod_evento
                                , registro_evento_ferias.desdobramento
                                , registro_evento_ferias.valor
                                , registro_evento_ferias.quantidade
                                , registro_evento_ferias.automatico
                                , evento_ferias_calculado.cod_registro
                                , evento_ferias_calculado.timestamp_registro
                                , evento_ferias_calculado.valor as valor_calculado
                                , evento_ferias_calculado.quantidade as quantidade_calculado
                                FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                                , folhapagamento'||stEntidade||'.evento_ferias_calculado
                            WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                                AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                                AND registro_evento_ferias.cod_contrato = '||INCODCONTRATO||'
                                AND registro_evento_ferias.cod_periodo_movimentacao = '||INCODPERIODOMOVIMENTACAO;
                    inCodRegistro := selectIntoInteger('SELECT max(cod_registro)+1
                                                        FROM folhapagamento'||stEntidade||'.registro_evento_periodo');
                    FOR reRegistro IN EXECUTE stSql LOOP
                        stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento_periodo (cod_registro,cod_contrato,cod_periodo_movimentacao)
                                VALUES ('||inCodRegistro||','||inCodContrato||','||inCodPeriodoMovimentacao||')';
                        EXECUTE stSql;
                        stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento (cod_registro,cod_evento,timestamp,valor,quantidade,automatico)
                                VALUES ('||inCodRegistro||','||reRegistro.cod_evento||','||quote_literal(stTimestamp)||','||reRegistro.valor||','||reRegistro.quantidade||',true)';
                        EXECUTE stSql;
                        stSql := 'INSERT INTO folhapagamento'||stEntidade||'.ultimo_registro_evento (cod_registro,cod_evento,timestamp)
                                VALUES ('||inCodRegistro||','||reRegistro.cod_evento||','||quote_literal(stTimestamp)||')';
                        EXECUTE stSql;
                        stSql := 'INSERT INTO folhapagamento'||stEntidade||'.evento_calculado (cod_registro,cod_evento,timestamp_registro,valor,quantidade,desdobramento)
                                VALUES ('||inCodRegistro||','||reRegistro.cod_evento||','||quote_literal(stTimestamp)||','||reRegistro.valor_calculado||','||reRegistro.quantidade_calculado||','||quote_literal(reRegistro.desdobramento)||')';
                        EXECUTE stSql;
                        
                        stSql := 'SELECT evento_ferias_calculado_dependente.*
                                    FROM folhapagamento'||stEntidade||'.evento_ferias_calculado_dependente
                                WHERE cod_registro = '||reRegistro.cod_registro||'
                                    AND cod_evento = '||reRegistro.cod_evento||'
                                    AND desdobramento = '||quote_literal(reRegistro.desdobramento)||'
                                    AND timestamp_registro = '||quote_literal(reRegistro.timestamp_registro)||'';
                        FOR reRegistro2 IN EXECUTE stSql LOOP
                            stSql := 'INSERT INTO folhapagamento'||stEntidade||'.evento_calculado_dependente (cod_registro,cod_evento,timestamp_registro,valor,quantidade,desdobramento,cod_dependente)
                                    VALUES ('||inCodRegistro||','||reRegistro2.cod_evento||','||quote_literal(stTimestamp)||','||reRegistro2.valor||','||reRegistro2.quantidade||','||quote_literal(reRegistro2.desdobramento)||','||reRegistro2.cod_dependente||')';
                            EXECUTE stSql;                                 
                        END LOOP;
    
                        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_ferias_calculado_dependente
                                WHERE cod_registro = '||reRegistro.cod_registro||'
                                AND cod_evento = '||reRegistro.cod_evento||'
                                AND desdobramento = '||quote_literal(reRegistro.desdobramento)||'
                                AND timestamp_registro = '||quote_literal(reRegistro.timestamp_registro)||'';
                        EXECUTE stSql;
    
                        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_ferias_calculado 
                                WHERE cod_registro = '||reRegistro.cod_registro||'
                                AND cod_evento = '||reRegistro.cod_evento||'
                                AND desdobramento = '||quote_literal(reRegistro.desdobramento)||'
                                AND timestamp_registro = '||quote_literal(reRegistro.timestamp_registro)||'';
                        EXECUTE stSql;
    
                        inCodRegistro := inCodRegistro + 1;
                        stTimestamp   := stTimestamp + (time '00:00:01');
                    END LOOP;
                END IF;
            END IF;
            --FIM DO CÓDIGO PARA INCORPORAR OS REGISTROS E EVENTOS CALCULADOS DE FÉRIAS EM SALÁRIO

            --INÍCIO DO CÓDIGO PARA INCORPORAR OS REGISTROS E EVENTOS CALCULADOS DE DÉCIMO EM SALÁRIO
            IF boRetornoDecimo IS TRUE THEN
                stSql := 'SELECT registro_evento_decimo.cod_evento
                               , registro_evento_decimo.desdobramento
                               , registro_evento_decimo.valor
                               , registro_evento_decimo.quantidade
                               , registro_evento_decimo.automatico
                               , evento_decimo_calculado.cod_registro
                               , evento_decimo_calculado.timestamp_registro
                               , evento_decimo_calculado.valor as valor_calculado
                               , evento_decimo_calculado.quantidade as quantidade_calculado
                            FROM folhapagamento'||stEntidade||'.registro_evento_decimo
                               , folhapagamento'||stEntidade||'.evento_decimo_calculado
                           WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                             AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                             AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                             AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                             AND registro_evento_decimo.cod_contrato = '||INCODCONTRATO||'
                             AND registro_evento_decimo.cod_periodo_movimentacao = '||INCODPERIODOMOVIMENTACAO;
                inCodRegistro := selectIntoInteger('SELECT max(cod_registro)+1
                                                      FROM folhapagamento'||stEntidade||'.registro_evento_periodo');
                FOR reRegistro IN EXECUTE stSql LOOP
                    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento_periodo (cod_registro,cod_contrato,cod_periodo_movimentacao)
                            VALUES ('||inCodRegistro||','||inCodContrato||','||inCodPeriodoMovimentacao||')';
                    EXECUTE stSql;
                    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento (cod_registro,cod_evento,timestamp,valor,quantidade,automatico)
                            VALUES ('||inCodRegistro||','||reRegistro.cod_evento||','||quote_literal(stTimestamp)||','||reRegistro.valor||','||reRegistro.quantidade||',true)';
                    EXECUTE stSql;                                                    
                    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.ultimo_registro_evento (cod_registro,cod_evento,timestamp)
                            VALUES ('||inCodRegistro||','||reRegistro.cod_evento||','||quote_literal(stTimestamp)||')';
                    EXECUTE stSql;                                                     
                    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.evento_calculado (cod_registro,cod_evento,timestamp_registro,valor,quantidade,desdobramento)
                            VALUES ('||inCodRegistro||','||reRegistro.cod_evento||','||quote_literal(stTimestamp)||','||reRegistro.valor_calculado||','||reRegistro.quantidade_calculado||',''I'')';
                    EXECUTE stSql;
                    
                    stSql := 'SELECT evento_decimo_calculado_dependente.*
                                FROM folhapagamento'||stEntidade||'.evento_decimo_calculado_dependente
                               WHERE cod_registro = '||reRegistro.cod_registro||'
                                 AND cod_evento = '||reRegistro.cod_evento||'
                                 AND desdobramento = '||quote_literal(reRegistro.desdobramento)||'
                                 AND timestamp_registro = '||quote_literal(reRegistro.timestamp_registro)||'';
                    FOR reRegistro2 IN EXECUTE stSql LOOP
                        stSql := 'INSERT INTO folhapagamento'||stEntidade||'.evento_calculado_dependente (cod_registro,cod_evento,timestamp_registro,valor,quantidade,desdobramento,cod_dependente)
                                VALUES ('||inCodRegistro||','||reRegistro2.cod_evento||','||quote_literal(stTimestamp)||','||reRegistro2.valor||','||reRegistro2.quantidade||','||quote_literal(reRegistro2.desdobramento)||','||reRegistro2.cod_dependente||')';
                        EXECUTE stSql;                                 
                    END LOOP;
    
                    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_decimo_calculado_dependente
                               WHERE cod_registro = '||reRegistro.cod_registro||'
                                 AND cod_evento = '||reRegistro.cod_evento||'
                                 AND desdobramento = '''||reRegistro.desdobramento||'''
                                 AND timestamp_registro = '''||reRegistro.timestamp_registro||'''';
                    EXECUTE stSql;
    
                    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_decimo_calculado 
                               WHERE cod_registro = '||reRegistro.cod_registro||'
                                 AND cod_evento = '||reRegistro.cod_evento||'
                                 AND desdobramento = '||quote_literal(reRegistro.desdobramento)||'
                                 AND timestamp_registro = '||quote_literal(reRegistro.timestamp_registro)||'';
                    EXECUTE stSql;
    
                    inCodRegistro := inCodRegistro + 1;
                    stTimestamp   := stTimestamp + (time '00:00:01');
                END LOOP;
            END IF;
            --FIM DO CÓDIGO PARA INCORPORAR OS REGISTROS E EVENTOS CALCULADOS DE DÉCIMO EM SALÁRIO
        ELSE
            boRetorno := FALSE;
        END IF;
    END IF;
    RETURN BORETORNO;
END;
$$ LANGUAGE 'plpgsql'; 

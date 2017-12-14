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
--     * Funç Plsql para retorno de valor/quantidade fixo atual deduzindo admissãafastamentos/rescisã 
--     * Data de Criaç: 23/01/2008
-- 
-- 
--     * @author Diego Lemos de Souza
-- 
--     * Casos de uso: uc-04.05.24
-- 
--     $Id: mediaDecimoValorQuantidadeFixoAtualDeduzindo.sql 32220 2008-08-15 12:10:32Z souzadl $
--*/


CREATE OR REPLACE FUNCTION mediaDecimoValorQuantidadeFixoAtualDeduzindo() RETURNS NUMERIC as $$

DECLARE
    stSql                       VARCHAR := '';
    stFixado                    VARCHAR := '';
    stAdmissaoPosse             VARCHAR := '';
    stExercicio                 VARCHAR := '';
    stEntidade                  VARCHAR := recuperarBufferTexto('stEntidade');
    reRegistro                  RECORD;
    crCursor                    REFCURSOR;
    inDeduzir                   INTEGER := 0;
    inMesInicio                 INTEGER := 1;
    inDiasTrabalhados           INTEGER := 0;
    inMesInicioAfastamento      INTEGER := 1;    
    inDiasInicioAfastamento     INTEGER := 0;
    inDiasAfastado              INTEGER := 0;
    inMesFimAfastamento         INTEGER := 12;
    inMesRescisao               INTEGER := 0;
    inDiasFimAfastamento        INTEGER := 0;
    inCodContrato               INTEGER := recuperarBufferInteiro('inCodContrato');
    inCodEvento                 INTEGER := recuperarBufferInteiro('inCodEvento');    
    inCodPeriodoMovimentacao    INTEGER := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    nuValor                     NUMERIC;
    nuQuantidade                NUMERIC;
    nuRetorno                   NUMERIC;
    nuPercentualAdiantamento    NUMERIC := recuperarBufferNumerico('nuPercentualAdiantamento');
    dtDataAdmissao              DATE;
    dtPerInicial                DATE;
    dtPerFinal                  DATE;
    dtRescisao                  DATE;
    dtCompetencia               DATE;
    dtPerFinalAssentamentoAnterior DATE;
    arDataArray                 VARCHAR[];

BEGIN
    --########
    --Verifica a data de admissãdo servidor a partir do inío do ano
    --para verificar se existe meses a serem removidos do calculo
    --para encontrar a quantidade de meses a serem pagos no démo
    dtCompetencia := pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao);
    arDataArray := string_to_array(dtCompetencia::varchar,'-'::varchar);
    stExercicio := arDataArray[1];   
   
   SELECT TRIM(configuracao.valor)
     INTO stAdmissaoPosse
     FROM administracao.configuracao 
    WHERE cod_modulo = 22
      AND parametro = 'dtContagemInicial'||stEntidade
      AND exercicio =  stExercicio;  
                                        
    IF stAdmissaoPosse = 'dtPosse' THEN
        dtDataAdmissao := selectIntoVarchar('SELECT contrato_servidor_nomeacao_posse.dt_posse
                                       FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                          , (SELECT cod_contrato
                                                  ,  max(timestamp) as timestamp
                                               FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                             GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                      WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                        AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                        AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
    END IF;
    IF stAdmissaoPosse = 'dtNomeacao' THEN
        dtDataAdmissao := selectIntoVarchar('SELECT contrato_servidor_nomeacao_posse.dt_nomeacao
                                       FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                          , (SELECT cod_contrato
                                                  ,  max(timestamp) as timestamp
                                               FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                             GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                      WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                        AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                        AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
    END IF;                                    
    IF stAdmissaoPosse = 'dtAdmissao' THEN
        dtDataAdmissao := selectIntoVarchar('SELECT contrato_servidor_nomeacao_posse.dt_admissao
                                       FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                          , (SELECT cod_contrato
                                                  ,  max(timestamp) as timestamp
                                               FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                             GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                      WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                        AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                        AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
    END IF;                              
    arDataArray := string_to_array(dtDataAdmissao::varchar,'-'::varchar);    

    IF arDataArray[1] = stExercicio THEN    
        WHILE arDataArray[2]::integer >= inMesInicio LOOP
            IF arDataArray[2]::integer = inMesInicio THEN
                --Quando o mes a ser pesquisado é mes de admissao, verifica se houve mais que 15 dias trabalhados
                --Se houver mais de 15 dias trabalhados conta para a media do decimo
                --Se nao houver deduz um  mes
                inDiasTrabalhados := calculaNrDiasAnoMes(arDataArray[1]::integer,arDataArray[2]::integer)-arDataArray[3]::integer+1;
                IF inDiasTrabalhados < 15 THEN             
                    inDeduzir := inDeduzir + 1;
                END IF;
            ELSE
                inDeduzir := inDeduzir + 1;
            END IF;
            inMesInicio := inMesInicio + 1;
        END LOOP;
    END IF;

    --#######
    --Verifica afastamentos do tipo: Auxilio doenca, Acidente Trabalho, Lic Maternidade ou Licenca
    --Se existir algum afastamento para o contrato, faz a contagem de dias afastado
    --para verificar se o mes sera deduzido ou nao da quantidade total de meses 
    --a serem pagos no démo
    stSql := 'SELECT assentamento_gerado.periodo_inicial
                   , assentamento_gerado.periodo_final
                FROM pessoal'||stEntidade||'.assentamento_assentamento
                   , pessoal'||stEntidade||'.assentamento_gerado
                   , pessoal'||stEntidade||'.assentamento_gerado_contrato_servidor
				   ,(SELECT cod_assentamento_gerado,
						max(timestamp)	as	timestamp
						FROM pessoal'||stEntidade||'.assentamento_gerado
						GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
               WHERE assentamento_assentamento.cod_assentamento = assentamento_gerado.cod_assentamento
                 AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                 AND assentamento_assentamento.cod_motivo IN (5,6,7,3)
                 AND (assentamento_gerado.periodo_inicial BETWEEN '''||stExercicio||'-01-01'' AND '''||stExercicio||'-12-31''
                  OR  assentamento_gerado.periodo_final BETWEEN '''||stExercicio||'-01-01'' AND '''||stExercicio||'-12-31'')
                 AND NOT EXISTS (SELECT 1
                                   FROM pessoal'||stEntidade||'.assentamento_gerado_excluido
                                  WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado
                                    AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp)                 
                 AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
				 AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
				 AND assentamento_gerado_contrato_servidor.cod_contrato = '||inCodContrato;
    FOR reRegistro IN EXECUTE stSql LOOP                 
        dtPerInicial := reRegistro.periodo_inicial;
        dtPerFinal := reRegistro.periodo_final;
    
        IF dtPerInicial IS NOT NULL THEN
            arDataArray := string_to_array(dtPerInicial::varchar,'-'::varchar);
            IF arDataArray[1]::integer = stExercicio::integer THEN
                inMesInicioAfastamento := arDataArray[2]::INTEGER;        
                inDiasInicioAfastamento := arDataArray[3]::INTEGER;
                inDiasAfastado := calculaNrDiasAnoMes(stExercicio::integer,inMesInicioAfastamento::integer)-arDataArray[3]::integer+1;
            ELSE
                inDiasAfastado := 31;
            END IF;
            arDataArray := string_to_array(dtPerFinal::varchar,'-'::varchar);
            IF arDataArray[1]::integer = stExercicio::integer THEN
                inMesFimAfastamento := arDataArray[2]::INTEGER;
                inDiasFimAfastamento := arDataArray[3]::INTEGER;
		    ELSE 
                inMesFimAfastamento := 12;
                inDiasFimAfastamento := 31;			    				
            END IF;
            IF inMesInicioAfastamento = inMesFimAfastamento THEN
                inDiasAfastado := inDiasFimAfastamento - inDiasInicioAfastamento+1;
            END IF;
        
            WHILE inMesInicioAfastamento <= inMesFimAfastamento LOOP
                inDiasTrabalhados := calculaNrDiasAnoMes(stExercicio::integer,inMesInicioAfastamento::integer)-inDiasAfastado::INTEGER;
                IF inDiasTrabalhados < 15 THEN
                    inDeduzir := inDeduzir + 1;
                END IF;
                inMesInicioAfastamento := inMesInicioAfastamento + 1;
                IF inMesInicioAfastamento = inMesFimAfastamento THEN
                    inDiasAfastado := inDiasFimAfastamento;
                ELSE
                    inDiasAfastado := calculaNrDiasAnoMes(stExercicio::integer,inMesInicioAfastamento::integer);
                END IF;
            END LOOP;
            --######
            --Esse IF serve para a seguinte situaçao:
            --Um servidor com dois ou mais afastamento, onde um afastamento termina no dia 15 de um mês com 30 dias
            --e o outro afastamento inicia no dia 16 do mesmo mê isso faz com que o programa entenda que nesse
            --mes o servidor esteve nos dois afastamento com 15 dias trabalhados e 15 dias afastados, o que nãestá 
            --correto, porque ele esteve 30 dias afastado.
            IF dtPerFinalAssentamentoAnterior = (reRegistro.periodo_inicial-1) THEN
                inDeduzir := inDeduzir + 1;
            END IF;            
        END IF;
        dtPerFinalAssentamentoAnterior := reRegistro.periodo_final;
    END LOOP;

    --#######
    --Contar a quantidade de meses posterior a rescisao a fim de utilizar para deduçao
    dtRescisao := selectIntoVarchar('SELECT dt_rescisao
                                       FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                                      WHERE cod_contrato = '||inCodContrato);
    IF dtRescisao IS NOT NULL THEN                                      
        arDataArray := string_to_array(dtRescisao::varchar,'-'::varchar);
        IF arDataArray[3]::integer < 15 THEN
            inDeduzir := inDeduzir + 1;
        END IF;
        inMesRescisao := arDataArray[2]::integer+1;
        WHILE inMesRescisao <= 12 LOOP
            inDeduzir := inDeduzir + 1;
            inMesRescisao := inMesRescisao + 1;
        END LOOP;
    END IF;   

    stSql := ' SELECT valor
                    , quantidade
                    , fixado 
                 FROM tmp_registro_evento_13 
                WHERE cod_evento = '||inCodEvento||'
                  AND lido_de = ''fixo_atual'' ';
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO nuValor, nuQuantidade, stFixado ;
    CLOSE crCursor;

    IF stFixado = 'V' THEN
        nuRetorno := nuValor;
    ELSE
        nuRetorno := nuQuantidade;
    END IF;    
    nuRetorno := (nuretorno/12)*(12-inDeduzir);
    
    --SE O PERCENTUAL  FOR MAIOR QUE ZERO IMPLICA EM DIZER QUE ÉADIANTAMENTO E POSSUI PERCENTUAL
    IF nuPercentualAdiantamento > 0 AND pega0ProporcaoAdiantamentoDecimo() IS TRUE THEN
        nuRetorno := nuRetorno * (nuPercentualAdiantamento/100);
    END IF;    
    RETURN nuRetorno; 
END;
$$ LANGUAGE 'plpgsql';


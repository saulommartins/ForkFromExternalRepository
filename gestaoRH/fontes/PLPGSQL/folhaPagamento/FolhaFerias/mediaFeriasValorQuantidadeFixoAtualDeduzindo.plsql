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
/**
     * Função Plsql para retorno de valor/quantidade fixo atual deduzindo admissão/afastamentos/rescisão. 
     * Data de Criação: 23/01/2008

     * @author Diego Lemos de Souza

     * Casos de uso: uc-04.05.24
 
     $Id: mediaFeriasValorQuantidadeFixoAtualDeduzindo.plsql 66263 2016-08-03 21:43:52Z michel $
*/

CREATE OR REPLACE FUNCTION mediaFeriasValorQuantidadeFixoAtualDeduzindo() RETURNS NUMERIC as $$

DECLARE
    stSql                       VARCHAR := '';
    stFixado                    VARCHAR := '';
    stAdmissaoPosse             VARCHAR := '';
    stMesCompetencia            VARCHAR := '';
    stAfastamentoInicio         VARCHAR := '';
    stAfastamentoFim            VARCHAR := '';
    stExercicio                 VARCHAR := '';
    stEntidade                  VARCHAR := '';
    boGerandoRescisao           VARCHAR := '';
    stDesdobramento             VARCHAR := '';
    reRegistro                  RECORD;
    crCursor                    REFCURSOR;
    inDeduzir                   INTEGER := 0;
    inMesInicio                 INTEGER := 1;
    inDiasTrabalhados           INTEGER := 0;
    inAnoInicioAfastamento      INTEGER := 0;    
    inMesInicioAfastamento      INTEGER := 0;    
    inDiasInicioAfastamento     INTEGER := 0;
    inDiasFimPerAquisitivo      INTEGER := 0;
    inDiasAfastado              INTEGER := 0;
    inAnoFimAfastamento         INTEGER := 0;
    inAnoFimPerAquisitivo       INTEGER := 0;
    inMesFimAfastamento         INTEGER := 0;
    inMesFimPerAquisitivo       INTEGER := 0;
    inDiasFimAfastamento        INTEGER := 0;
    inAnoCompetencia            INTEGER := 0;
    inCodContrato               INTEGER := 0;
    inCodPeriodoMovimentacao    INTEGER := 0;
    inCodEvento                 INTEGER := 0;
    inPagamento                 INTEGER := 0;
    inQtdDiasAnoCompetencia     INTEGER := 0;
    stQtdDiasAnoCompetencia     VARCHAR;
    nuValor                     NUMERIC;
    nuQuantidade                NUMERIC;
    nuRetorno                   NUMERIC;
    dtInicioPerAquisitivo       DATE;
    dtFimPerAquisitivo          DATE;
    dtPerInicial                DATE;
    dtPerFinal                  DATE;
    dtRescisao                  DATE;
    dtCompetencia               DATE;
    dtLastDay                   DATE;
    dtPerFinalAssentamentoAnterior DATE;
    arDataArray                 VARCHAR[];

BEGIN
    stEntidade                  := recuperarBufferTexto('stEntidade');
    inCodContrato               := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodEvento                 := recuperarBufferInteiro('inCodEvento'); 
    boGerandoRescisao           := recuperarBufferTexto('boGerandoRescisao');


    --########
    --Verifica a data de admissão do servidor a partir do início do ano
    --para verificar se existe meses a serem removidos do calculo
    --para encontrar a quantidade de meses a serem pagos no décimo
    dtCompetencia := pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao);
    arDataArray := string_to_array(dtCompetencia::varchar,'-'::varchar);
    stExercicio := arDataArray[1];
    inAnoCompetencia := arDataArray[1];
    stMesCompetencia := arDataArray[2];
    inMesInicio := arDataArray[2];

    inQtdDiasAnoCompetencia := selectIntoInteger('SELECT (('''|| inAnoCompetencia ||'-12-31''::DATE - '''|| inAnoCompetencia ||'-01-01''::DATE)+1)');

    stQtdDiasAnoCompetencia := '( (((ferias.dt_inicial_aquisitivo::date) + INTERVAL ''1 year'')::date) - ferias.dt_inicial_aquisitivo )';

    stSql := 'SELECT ferias.dt_inicial_aquisitivo
                   , ferias.dt_final_aquisitivo
                FROM pessoal'|| stEntidade ||'.ferias
                   , pessoal'|| stEntidade ||'.lancamento_ferias
               WHERE ferias.cod_ferias = lancamento_ferias.cod_ferias
                 AND cod_contrato = '|| inCodContrato ||'
                 AND ((ano_competencia = '|| quote_literal(inAnoCompetencia) ||' AND mes_competencia = '|| quote_literal(stMesCompetencia) ||')
                      OR (to_char(dt_inicio,''yyyy-mm'') = '|| quote_literal(inAnoCompetencia ||'-'|| stMesCompetencia) ||')
                      OR (to_char(dt_fim,''yyyy-mm'') = '|| quote_literal(inAnoCompetencia ||'-'|| stMesCompetencia) ||'))
                 ';
    IF boGerandoRescisao  = 't' THEN
        stDesdobramento := recuperarBufferTexto('stSituacaFerias');
        IF stDesdobramento = 'P' THEN
            stSql := stSql || 'AND (ferias.dt_final_aquisitivo-ferias.dt_inicial_aquisitivo+1) < '||stQtdDiasAnoCompetencia;
        END IF;
        IF stDesdobramento = 'V' THEN
            stSql := stSql || 'AND (ferias.dt_final_aquisitivo-ferias.dt_inicial_aquisitivo+1) >= '||stQtdDiasAnoCompetencia;
        END IF;
    END IF;

    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO dtInicioPerAquisitivo, dtFimPerAquisitivo ;
    CLOSE crCursor;

    arDataArray := string_to_array(dtInicioPerAquisitivo::varchar,'-'::varchar);    
    IF arDataArray[1] = stExercicio THEN    
        WHILE arDataArray[2]::integer >= inMesInicio LOOP
            IF arDataArray[2]::integer = inMesInicio THEN
                --Quando o mes a ser pesquisado é o mês de admissão é verificado se houve mais que 15 dias trabalhados
                --Se houver mais de 15 dias trabalhados conta para o décimo
                --Se não houver deduz um mês
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
    arDataArray := string_to_array(dtFimPerAquisitivo::varchar,'-'::varchar);    
    inDiasFimPerAquisitivo := arDataArray[3];
    inMesFimPerAquisitivo  := arDataArray[2];
    inAnoFimPerAquisitivo  := arDataArray[1];
    --#######
    --Verifica afastamentos do tipo: Auxilio doença, Acidente Trabalho e Licença
    --Se existir algum afastamento para o contrato, faz a contagem de dias afastado
    --para verificar se o mês será deduzido ou não da quantidade total de meses 
    --a serem pagos no décimo
    stSql := 'SELECT CASE WHEN assentamento.quant_dias_onus_empregador IS NOT NULL THEN 
                                    (assentamento_gerado.periodo_inicial + assentamento.quant_dias_onus_empregador)
                               ELSE assentamento_gerado.periodo_inicial END AS periodo_inicial
                   , assentamento_gerado.periodo_final
                FROM pessoal'|| stEntidade ||'.assentamento_assentamento
                   , (SELECT cod_assentamento_gerado
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.assentamento_gerado
                      GROUP BY cod_assentamento_gerado) as max_assentamento_gerado                
                   , pessoal'|| stEntidade ||'.assentamento
                   , (   SELECT cod_assentamento
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.assentamento
                        GROUP BY cod_assentamento) AS max_assentamento                      
                   , pessoal'|| stEntidade ||'.assentamento_gerado
                   , pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
               WHERE assentamento_assentamento.cod_assentamento = assentamento_gerado.cod_assentamento
                 AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                 AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp               
                 AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                 AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento
                 AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                 AND assentamento.timestamp = max_assentamento.timestamp                 
                 AND assentamento_assentamento.cod_motivo IN (5,6,3)
                 AND (assentamento_gerado.periodo_inicial BETWEEN '|| quote_literal(dtInicioPerAquisitivo) ||' AND '|| quote_literal(dtFimPerAquisitivo) ||'
                  OR  assentamento_gerado.periodo_final   BETWEEN '|| quote_literal(dtInicioPerAquisitivo) ||' AND '|| quote_literal(dtFimPerAquisitivo) ||')                 
                 AND NOT EXISTS (SELECT 1
                                   FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                  WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado
                                    AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp)                 
                 AND assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato;                
    FOR reRegistro IN EXECUTE stSql LOOP                 
        dtPerInicial := reRegistro.periodo_inicial;
        dtPerFinal := reRegistro.periodo_final;
        IF dtPerInicial IS NOT NULL THEN
            IF dtPerInicial < dtInicioPerAquisitivo THEN
                dtPerInicial := dtInicioPerAquisitivo;
            END IF;
            arDataArray := string_to_array(dtPerInicial::varchar,'-'::varchar);
    
            inAnoInicioAfastamento := arDataArray[1]::INTEGER;        
            inMesInicioAfastamento := arDataArray[2]::INTEGER;        
            inDiasInicioAfastamento := arDataArray[3]::INTEGER;
            IF inMesInicioAfastamento < 10 THEN    
                stAfastamentoInicio := inAnoInicioAfastamento::varchar ||'0'|| inMesInicioAfastamento::VARCHAR;
            ELSE
                stAfastamentoInicio := inAnoInicioAfastamento::varchar||inMesInicioAfastamento::VARCHAR;
            END IF;
            inDiasAfastado := calculaNrDiasAnoMes(stExercicio::integer,inMesInicioAfastamento::integer)-arDataArray[3]::integer+1;
            IF dtPerFinal IS NULL or dtPerFinal > dtFimPerAquisitivo THEN
                inAnoFimAfastamento := inAnoFimPerAquisitivo;
                inMesFimAfastamento := inMesFimPerAquisitivo;
                inDiasFimAfastamento := inDiasFimPerAquisitivo;                                    
            ELSE
                arDataArray := string_to_array(dtPerFinal::varchar,'-'::varchar); 
                inAnoFimAfastamento := arDataArray[1]::INTEGER;   
                inMesFimAfastamento := arDataArray[2]::INTEGER;
                inDiasFimAfastamento := arDataArray[3]::INTEGER;
            END IF;
            IF inMesFimAfastamento < 10 THEN    
                stAfastamentoFim := inAnoFimAfastamento::varchar ||'0'|| inMesFimAfastamento::VARCHAR;
            ELSE
                stAfastamentoFim := inAnoFimAfastamento::varchar||inMesFimAfastamento::VARCHAR;
            END IF;        
            IF inMesInicioAfastamento = inMesFimAfastamento THEN
                inDiasAfastado := inDiasFimAfastamento - inDiasInicioAfastamento+1;
            END IF;
            
            WHILE stAfastamentoInicio <= stAfastamentoFim LOOP      
                --Calcula os dias trabalhados do mês diminuindo o número de dias do mês dos dias afastados
                inDiasTrabalhados := calculaNrDiasAnoMes(stExercicio::integer,inMesInicioAfastamento::integer)-inDiasAfastado::INTEGER;
    
                --Se os dias trabalhados foram menos de 15 será deduzido um mês da média
                IF inDiasTrabalhados < 15 THEN
                    inDeduzir := inDeduzir + 1;
                END IF;            
                --Caso o afastamento se dê de um ano para o outro, quando chegar no mês 12
                --é iniciado novamente o mes de início em janeiro até o mês e ano de encerramento 
                --do afastamento
                IF inMesInicioAfastamento = 12 THEN
                    inMesInicioAfastamento := 0;  
                    inAnoInicioAfastamento := inAnoInicioAfastamento + 1;
                END IF;
                inMesInicioAfastamento := inMesInicioAfastamento + 1;           
                IF inMesInicioAfastamento = inMesFimAfastamento THEN
                    inDiasAfastado := inDiasFimAfastamento;
                ELSE
                    inDiasAfastado := calculaNrDiasAnoMes(stExercicio::integer,inMesInicioAfastamento::integer);
                END IF;
                --Código que monta os valores para verificar se já foi percorrido toda a
                --extensão que compõem o período de afastamento
                IF inMesInicioAfastamento < 10 THEN    
                    stAfastamentoInicio := inAnoInicioAfastamento::varchar ||'0'|| inMesInicioAfastamento::VARCHAR;
                ELSE
                    stAfastamentoInicio := inAnoInicioAfastamento::varchar||inMesInicioAfastamento::VARCHAR;
                END IF;            
            END LOOP;
            --######
            --Esse IF serve para a seguinte situaçao:
            --Um servidor com dois ou mais afastamento, onde um afastamento termina no dia 15 de um mês com 30 dias
            --e o outro afastamento inicia no dia 16 do mesmo mês, isso faz com que o programa entenda que nesse
            --mes o servidor esteve nos dois afastamento com 15 dias trabalhados e 15 dias afastados, o que não está
            --correto, porque ele esteve 30 dias afastado.
            IF dtPerFinalAssentamentoAnterior = (reRegistro.periodo_inicial-1) THEN
                inDeduzir := inDeduzir + 1;
            END IF;
        END IF;
        dtPerFinalAssentamentoAnterior := reRegistro.periodo_final;
    END LOOP;

    --#######
    --Contar a quantidade de meses posterior a rescisão a fim de utilizá-ça para dedução
    dtRescisao := selectIntoVarchar('SELECT dt_rescisao
                                       FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                      WHERE cod_contrato = '|| inCodContrato);
    IF dtRescisao IS NOT NULL THEN
        dtPerInicial := dtInicioPerAquisitivo;
        dtPerFinal   := dtFimPerAquisitivo;
        WHILE to_char(dtPerInicial,'yyyy-mm') <= to_char(dtPerFinal,'yyyy-mm') LOOP
            IF dtPerInicial = dtInicioPerAquisitivo THEN
                --Verificação da data inicial do período aquisitivo
                --Caso a quantidade de dias trabalhados nessa data seje maior ou igual a 15 dias
                --deverá ser pago 1/12 anos para o mês
                dtLastDay := last_day(dtPerInicial);
                IF dtLastDay > dtRescisao THEN
                    dtLastDay := dtRescisao;
                END IF;
                IF dtLastDay-dtPerInicial >= 15 THEN
                    inPagamento := inPagamento + 1;    
                END IF;            
            ELSE
                --Verificação da data de rescisão
                --Ou se for férias vencida na rescisão, compara com a data final do período aquisito
                --Caso a quantidade de dias trabalhados nessa data seje maior ou igual a 15 dias
                --deverá ser pago 1/12 anos para o mês
                IF to_char(dtPerInicial,'yyyy-mm') = to_char(dtRescisao,'yyyy-mm') OR to_char(dtPerInicial,'yyyy-mm') = to_char(dtPerFinal,'yyyy-mm') THEN
                    IF to_char(dtPerFinal,'dd')::integer >= 15 THEN
                        inPagamento := inPagamento + 1;
                    END IF;
                ELSE
                    --Para cada mês cheio que conste entre a data inícial do período aquisitivo
                    --e a data de rescisão é acrescentado 1/12 anos de pagamento na variável
                    --inPagamento.
                    inPagamento := inPagamento + 1;
                END IF;
            END IF; 

            dtPerInicial := adiciona_meses(dtPerInicial,1);
        END LOOP;
        inDeduzir := inDeduzir + (12-inPagamento);
    END IF;   

    stSql := ' SELECT valor
                    , quantidade
                    , fixado 
                 FROM tmp_registro_evento_ferias 
                WHERE cod_evento = '|| inCodEvento ||'
                  AND lido_de = ''fixo_atual'' ';
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO nuValor, nuQuantidade, stFixado ;
    CLOSE crCursor;

    IF stFixado = 'V' THEN
        nuRetorno := nuValor;
    ELSE
        nuRetorno := nuQuantidade;
    END IF;
    nuRetorno := (nuRetorno/12)*(12-inDeduzir);
    RETURN nuRetorno; 
END;
$$ LANGUAGE 'plpgsql';

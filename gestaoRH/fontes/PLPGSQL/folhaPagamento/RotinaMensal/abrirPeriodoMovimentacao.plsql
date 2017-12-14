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
    * Abertura de Periodo de Movimentação
    * Data de Criação: 16/05/2007


    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 27917 $
    $Name$
    $Author: souzadl $
    $Date: 2008-02-08 12:36:33 -0200 (Sex, 08 Fev 2008) $

    * Casos de uso: uc-04.05.40
*/
CREATE OR REPLACE FUNCTION abrirPeriodoMovimentacao(VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS BOOLEAN AS $$
DECLARE
    dtInicial                           ALIAS FOR $1;
    dtFinal                             ALIAS FOR $2;
    stExercicio                         ALIAS FOR $3;
    stEntidadeParametro                 ALIAS FOR $4;
    stEntidade                          VARCHAR;
    inCodPeriodoMovimentacaoFechada     INTEGER;
    inCodPeriodoMovimentacaoAberta      INTEGER;
    inCodRegistro                       INTEGER;
    inCodPadrao                         INTEGER;
    inCodNivelPadrao                    INTEGER;
    inCodFuncao                         INTEGER;
    inCodSubDivisao                     INTEGER;
    inCodEspecialidade                  INTEGER;
    inContador                          INTEGER;
    inAnoAnterior                       INTEGER;
    inDiferencaMeses                    INTEGER;
    inDiferencaAnos                     INTEGER;
    inGozoFerias                        INTEGER;
    stSql                               VARCHAR;
    stSituacaoFolha                     VARCHAR;
    arDataInicial                       VARCHAR[];
    arDataFinal                         VARCHAR[];
    stDataFinal                         VARCHAR;
    stDataInicial                       VARCHAR;
    stFormula                           VARCHAR;
    stSqlEventoFixos                    VARCHAR;
    stSqlAux                            VARCHAR;
    stTimestamp                         TIMESTAMP;
    reRegistro                          RECORD;
    reSalario                           RECORD;
    reFerias                            RECORD;
    reEventosConfigurados               RECORD;
    nuQuantidade                        NUMERIC;
    nuHorasMensais                      NUMERIC;
    nuHorasMensaisPadrao                NUMERIC;
    nuSalarioPadrao                     NUMERIC;
    nuSalarioHoraPadrao                 NUMERIC;
    nuSalario                           NUMERIC;
    nuSalarioAtual                      NUMERIC;
    nuHorasSemanais                     NUMERIC;
    dtVigencia                          DATE;
    boRetorno                           BOOLEAN;
    boRegistrar                         BOOLEAN;
    boInsereParcelaProporcional         BOOLEAN:=FALSE;
    boParcelaFerias                     BOOLEAN;
    boProporcional                      BOOLEAN;
    inMesCarencia                       INTEGER;
    inCodContrato                       INTEGER;
    crCursor                            REFCURSOR;
BEGIN
    boRetorno := removerTodosBuffers();
    stEntidade := criarBufferEntidade(stEntidadeParametro);
    --INÍCIO###################################
    --Fechar periodo atual e abre um novo
    inCodPeriodoMovimentacaoFechada := selectIntoInteger('SELECT max(cod_periodo_movimentacao) FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao');
    IF inCodPeriodoMovimentacaoFechada IS NOT NULL THEN
        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao (cod_periodo_movimentacao,situacao)
                  VALUES ('|| inCodPeriodoMovimentacaoFechada ||',''f'')';
        EXECUTE stSql;
        inCodPeriodoMovimentacaoAberta := inCodPeriodoMovimentacaoFechada + 1;
    ELSE
        inCodPeriodoMovimentacaoAberta  := 1;
        inCodPeriodoMovimentacaoFechada := 0;
    END IF;
    stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.periodo_movimentacao (cod_periodo_movimentacao,dt_inicial,dt_final)
              VALUES ('|| inCodPeriodoMovimentacaoAberta ||','|| quote_literal(to_date(dtInicial,'dd-mm-yyyy')) ||','|| quote_literal(to_date(dtFinal,'dd-mm-yyyy')) ||')';
    EXECUTE stSql;
    stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao (cod_periodo_movimentacao,situacao)
              VALUES ('|| inCodPeriodoMovimentacaoAberta ||',''a'')';
    EXECUTE stSql;
    --FIM######################################

    --INÍCIO###################################
    

    --Processo de cópia dos eventos para o novo periodo de movimentacao
    inCodRegistro := selectIntoInteger('SELECT max(cod_registro)+1 FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo');
    stSql := '   SELECT registro_evento.*
                      , registro_evento_periodo.cod_contrato
                      , registro_evento_parcela.parcela
                      , registro_evento_parcela.mes_carencia
                      , evento.fixado
                   FROM folhapagamento'|| stEntidade ||'.registro_evento
                      , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                      , folhapagamento'|| stEntidade ||'.ultimo_registro_evento
              LEFT JOIN folhapagamento'|| stEntidade ||'.registro_evento_parcela
                     ON ultimo_registro_evento.cod_registro =  registro_evento_parcela.cod_registro
                      , folhapagamento'|| stEntidade ||'.evento
                  WHERE registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                    AND registro_evento.cod_registro = registro_evento_periodo.cod_registro
                    AND registro_evento.proporcional is false
                    AND registro_evento.automatico is false
                    AND (evento.tipo != ''V'' OR registro_evento_parcela.parcela IS NOT NULL)
                    AND registro_evento.cod_evento = evento.cod_evento
                    AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoFechada ||'
                    AND (parcela is null or pega0MaiorParcela(registro_evento.quantidade,registro_evento_periodo.cod_contrato,registro_evento_periodo.cod_periodo_movimentacao,registro_evento.cod_evento) < registro_evento_parcela.parcela)
                    AND NOT EXISTS (SELECT 1
                                      FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                     WHERE registro_evento_periodo.cod_contrato = contrato_servidor_caso_causa.cod_contrato)
                    ORDER BY registro_evento_periodo.cod_contrato';
    stTimestamp := now()::text::timestamp(3);
    inCodContrato := 0;
    FOR reRegistro IN EXECUTE stSql
    LOOP
        inMesCarencia := reRegistro.mes_carencia;
        boInsereParcelaProporcional := FALSE;
        
        IF reRegistro.parcela IS NOT NULL THEN
            --Verifica se o evento do contrato possui tipo média para a aba de férias
            inCodFuncao := pega0FuncaoDoContratoNaData(reRegistro.cod_contrato,to_date(dtFinal,'dd-mm-yyyy')::varchar);
            inCodSubDivisao := pega0SubDivisaoDoContratoNaData(reRegistro.cod_contrato,to_date(dtFinal,'dd-mm-yyyy')::varchar);
            inCodEspecialidade := pega0EspecialidadeDoContratoNaData(reRegistro.cod_contrato,to_date(dtFinal,'dd-mm-yyyy')::varchar);
            stFormula := pegaFormulaMediaFerias(reRegistro.cod_evento,2,inCodSubDivisao,inCodFuncao,inCodEspecialidade);
            IF stFormula IS NOT NULL THEN
                --Se (o contrato possui férias na competência = pagto de férias ou pelo menos um dia de gozo de férias na competência)
                stSql := 'SELECT ferias.cod_contrato
                               , ferias.dias_ferias
                               , ferias.dias_abono
                               , lancamento_ferias.*
                            FROM pessoal'|| stEntidade ||'.ferias
                            JOIN pessoal'|| stEntidade ||'.lancamento_ferias
                              ON lancamento_ferias.cod_ferias = ferias.cod_ferias
                           WHERE (   (lancamento_ferias.ano_competencia = '|| quote_literal(to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy')) ||' AND lancamento_ferias.mes_competencia = '|| quote_literal(to_char(to_date(dtFinal,'dd-mm-yyyy'),'mm')) ||')
                                  OR to_char(dt_inicio,''yyyy-mm'') = '|| quote_literal(to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm')) ||'
                                  OR to_char(dt_fim,''yyyy-mm'') = '|| quote_literal(to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm')) ||')
                             AND ferias.cod_contrato = '|| reRegistro.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reFerias;
                CLOSE crCursor;
                IF reFerias.cod_contrato IS NOT NULL THEN

                    --Se (campo pagar_13 = false) então
                    IF pegaPagamentoUmTercoFerias(reRegistro.cod_contrato,inCodPeriodoMovimentacaoAberta) = 'f' THEN
                        --Início e fim das férias dentro da competência
                        IF  to_char(reFerias.dt_inicio,'yyyy-mm') = to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm')
                        AND to_char(reFerias.dt_fim,'yyyy-mm')    = to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm') THEN
                            inGozoFerias := (reFerias.dt_fim-reFerias.dt_inicio)+1;
                        ELSE
                            --Início das férias dentro da competência
                            IF to_char(reFerias.dt_inicio,'yyyy-mm') = to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm') THEN
                                inGozoFerias := (to_date(dtFinal,'dd-mm-yyyy')-reFerias.dt_inicio)+1;
                            ELSE
                                --Fim das férias dentro da competência
                                IF to_char(reFerias.dt_fim,'yyyy-mm')    = to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm') THEN
                                    inGozoFerias := to_char(reFerias.dt_fim,'dd')::INTEGER;
                                END IF;
                            END IF;
                        END IF;

                        --Proporção dos dias em caso de pagamento de abono.
                        IF reFerias.dias_abono IS NOT NULL AND reFerias.dias_abono > 0 THEN
                            inGozoFerias := (inGozoFerias*30)/(30-reFerias.dias_abono);
                        END IF;

                        --Se (mês do pagamento das férias = competência) então
                        IF reFerias.ano_competencia ||'-'|| reFerias.mes_competencia = to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm') THEN

                            --Se (o contrato possui mais que 15 dias de férias na competência) então
                            --O contrato possuindo mais que 15 dias de gozo de férias na competência, não incluir evento com parcelas
                            IF inGozoFerias > 15 THEN
                                boInsereParcelaProporcional := TRUE;
                            END IF;
                        ELSE
                            --Se (o contrato possui 15 ou mais dias de férias no mês)
                            IF inGozoFerias >= 15 THEN
                                boInsereParcelaProporcional := TRUE;
                            END IF;
                        END IF;
                    END IF;
                END IF;
            END IF;
        END IF;

        inContador := selectIntoInteger('SELECT COUNT(*)
                                        FROM folhapagamento'|| stEntidade ||'.contrato_servidor_periodo
                                        WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoAberta ||'
                                            AND cod_contrato = '|| reRegistro.cod_contrato);
        IF inContador = 0 THEN
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.contrato_servidor_periodo (cod_periodo_movimentacao,cod_contrato)
                    VALUES ('|| inCodPeriodoMovimentacaoAberta ||','|| reRegistro.cod_contrato ||')';
            EXECUTE stSql;
        END IF;
        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_periodo (cod_registro,cod_periodo_movimentacao,cod_contrato)
                VALUES ('|| inCodRegistro ||','|| inCodPeriodoMovimentacaoAberta ||','|| reRegistro.cod_contrato ||')';
        EXECUTE stSql;
        IF reRegistro.quantidade IS NULL THEN
            reRegistro.quantidade := 0;
        END IF;
        IF reRegistro.parcela IS NOT NULL THEN
            --Se boProporcional igual a TRUE, o evento possui um proporcional zerado no mês passado
            --Por isso esse evento não incrementa a quantidade de parcelas no novo periodo
            stSql := 'SELECT proporcional
                        FROM folhapagamento'|| stEntidade ||'.registro_evento
                        JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
                          ON registro_evento.cod_registro = registro_evento_periodo.cod_registro
                        JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento
                          ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                       WHERE registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoFechada ||'
                         AND registro_evento_periodo.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento.cod_evento = '|| reRegistro.cod_evento ||'
                         AND registro_evento.proporcional IS TRUE
                         AND registro_evento.valor = 0
                         AND registro_evento.quantidade = 0';
            boProporcional := selectIntoBoolean(stSql);
           
            --Verifica se o evento de parcelas foi inserido na folha de férias
            --Caso o evento exista na folha de férias a quantidade do evento no
            --novo período tem que receber uma unidade extra na sua quantidade
            stSql := 'SELECT true as retorno
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                        JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                          ON registro_evento_ferias.cod_registro = ultimo_registro_evento_ferias.cod_registro
                         AND registro_evento_ferias.cod_evento = ultimo_registro_evento_ferias.cod_evento
                         AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento
                         AND registro_evento_ferias.timestamp = ultimo_registro_evento_ferias.timestamp
                        JOIN folhapagamento'|| stEntidade ||'.registro_evento_ferias_parcela
                          ON registro_evento_ferias.cod_registro = registro_evento_ferias_parcela.cod_registro
                         AND registro_evento_ferias.cod_evento = registro_evento_ferias_parcela.cod_evento
                         AND registro_evento_ferias.desdobramento = registro_evento_ferias_parcela.desdobramento
                         AND registro_evento_ferias.timestamp = registro_evento_ferias_parcela.timestamp
                       WHERE registro_evento_ferias.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoFechada ||'
                         AND registro_evento_ferias.cod_evento = '|| reRegistro.cod_evento;
            boParcelaFerias := selectIntoBoolean(stSql);

            IF boProporcional IS TRUE THEN
                nuQuantidade := reRegistro.quantidade;
            ELSE
                IF inMesCarencia > 0 THEN
                    inMesCarencia := inMesCarencia -1;
                    nuQuantidade := reRegistro.quantidade;
                ELSE
                    nuQuantidade := reRegistro.quantidade + 1;
                END IF;
                IF nuQuantidade > reRegistro.parcela THEN
                    nuQuantidade := 0;
                END IF;
            END IF;
            IF boParcelaFerias IS TRUE THEN
                nuQuantidade := nuQuantidade + 1;
                IF nuQuantidade > reRegistro.parcela THEN
                    nuQuantidade := 0;
                END IF;
            END IF;
        ELSE
            nuQuantidade := reRegistro.quantidade;
        END IF;
        IF reRegistro.valor IS NULL THEN
            reRegistro.valor := 0.00;
        END IF;
        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento (cod_registro,cod_evento,timestamp,valor,quantidade,proporcional,automatico)
                VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||','|| reRegistro.valor ||','|| nuQuantidade ||',false,false)';
        EXECUTE stSql;
        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.ultimo_registro_evento (cod_registro,cod_evento,timestamp)
                VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||')';
        EXECUTE stSql;
        IF reRegistro.parcela IS NOT NULL THEN
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_parcela (cod_registro,cod_evento,timestamp,parcela,mes_carencia)
                    VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||','|| reRegistro.parcela ||',' || inMesCarencia || ')';
            EXECUTE stSql;
        END IF;
        inCodRegistro := inCodRegistro + 1;

        --INSERE EVENTO DE PARCELA NA ABA PROPORCIONAL ZERADO
        IF boInsereParcelaProporcional THEN
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_periodo (cod_registro,cod_periodo_movimentacao,cod_contrato)
                    VALUES ('|| inCodRegistro ||','|| inCodPeriodoMovimentacaoAberta ||','|| reRegistro.cod_contrato ||')';
            EXECUTE stSql;
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento (cod_registro,cod_evento,timestamp,valor,quantidade,proporcional,automatico)
                    VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||',0,0,TRUE,false)';
            EXECUTE stSql;
            stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.ultimo_registro_evento (cod_registro,cod_evento,timestamp)
                    VALUES ('|| inCodRegistro ||','|| reRegistro.cod_evento ||','|| quote_literal(stTimestamp) ||')';
            EXECUTE stSql;
            inCodRegistro := inCodRegistro + 1;
        END IF;


        --Valida contrato para inserir apenas 1 vez em cada contrado os eventos automaticos
        --Busca dados na configuracao de eventos automaticos
        --Gestão Recursos Humanos :: Folha de Pagamento :: Configuração :: Configurar Eventos Automáticos
        IF inCodContrato != reRegistro.cod_contrato THEN
            stSqlEventoFixos := '
                    SELECT  evento_evento.cod_evento
                            ,evento_evento.valor_quantidade
                            ,evento.fixado
                      FROM ( SELECT evento_evento.*
                               FROM folhapagamento'|| stEntidade ||'.evento_evento
                         INNER JOIN ( SELECT cod_evento
                                         ,   MAX(timestamp) as timestamp
                                        FROM folhapagamento'|| stEntidade ||'.evento_evento as max
                                 GROUP BY cod_evento
                                    ) as max
                                 ON evento_evento.cod_evento = max.cod_evento
                                AND evento_evento.timestamp = max.timestamp         
                           ) as evento_evento
                    
                INNER JOIN (SELECT regexp_split_to_table(NULLIF(valor,''''),'','')::integer as cod_evento 
                              FROM administracao.configuracao 
                             WHERE cod_modulo = 27 
                               AND exercicio = '''||stExercicio||'''
                               AND parametro = ''evento_automatico''
                           ) as evento_fixo_configurado    
                        ON evento_evento.cod_evento = evento_fixo_configurado.cod_evento

                INNER JOIN folhapagamento.evento
                        ON evento.cod_evento = evento_evento.cod_evento
                        ';
            FOR reEventosConfigurados IN EXECUTE stSqlEventoFixos
            LOOP
                /*
                    INSERE OS EVENTOS AUTOMATICOS CONFIGURADOS
                    SE folhapagamento.evento.fixado = Q ENTAO
                        Gravar na coluna registro_evento.quantidade com o valor de folhapagamento.evento_evento.valor_quantidade
                    SENAO folhapagamento.evento.fixado = V
                        Gravar na coluna registro_evento.valor com o valor de folhapagamento.evento_evento.valor_quantidade
                */

                IF reEventosConfigurados.fixado = 'Q' THEN        
                    stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_periodo 
                                    (   
                                        cod_registro
                                        ,cod_periodo_movimentacao
                                        ,cod_contrato
                                    )
                                    VALUES 
                                    (   
                                        '|| inCodRegistro ||'
                                        ,'|| inCodPeriodoMovimentacaoAberta ||'
                                        ,'|| reRegistro.cod_contrato ||'
                                    )';
                    EXECUTE stSqlAux;
                    stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento 
                                    (   
                                        cod_registro
                                        ,cod_evento
                                        ,timestamp
                                        ,valor
                                        ,quantidade
                                        ,proporcional
                                        ,automatico
                                    )
                                    VALUES 
                                    (
                                        '|| inCodRegistro ||'
                                        ,'|| reEventosConfigurados.cod_evento ||'
                                        ,'|| quote_literal(stTimestamp) ||'
                                        , 0.00 
                                        ,'|| reEventosConfigurados.valor_quantidade ||'
                                        ,false
                                        ,false
                                    )';
                    EXECUTE stSqlAux;
                    stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.ultimo_registro_evento 
                                    (
                                        cod_registro
                                        ,cod_evento
                                        ,timestamp
                                    )
                                    VALUES 
                                    (
                                        '|| inCodRegistro ||'
                                        ,'|| reEventosConfigurados.cod_evento ||'
                                        ,'|| quote_literal(stTimestamp) ||'
                                    )';
                    EXECUTE stSqlAux;

                    IF reRegistro.parcela IS NOT NULL THEN
                        stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_parcela 
                                            (cod_registro
                                                ,cod_evento
                                                ,timestamp
                                                ,parcela
                                                ,mes_carencia
                                            )
                                            VALUES 
                                            (
                                                '|| inCodRegistro ||'
                                                ,'|| reEventosConfigurados.cod_evento ||'
                                                ,'|| quote_literal(stTimestamp) ||'
                                                ,'|| reRegistro.parcela ||'
                                                ,'|| inMesCarencia ||'
                                            )';
                        EXECUTE stSqlAux;
                    END IF;
            
                ELSEIF reEventosConfigurados.fixado = 'V' THEN
    
                    stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_periodo 
                                    (
                                        cod_registro
                                        ,cod_periodo_movimentacao
                                        ,cod_contrato
                                    )
                                    VALUES 
                                    (
                                        '|| inCodRegistro ||'
                                        ,'|| inCodPeriodoMovimentacaoAberta ||'
                                        ,'|| reRegistro.cod_contrato ||'
                                    )';
                    EXECUTE stSqlAux;
                    stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento 
                                    (
                                        cod_registro
                                        ,cod_evento
                                        ,timestamp
                                        ,valor
                                        ,quantidade
                                        ,proporcional
                                        ,automatico
                                    )
                                    VALUES 
                                    (
                                        '|| inCodRegistro ||'
                                        ,'|| reEventosConfigurados.cod_evento ||'
                                        ,'|| quote_literal(stTimestamp) ||'
                                        ,'|| reEventosConfigurados.valor_quantidade ||'
                                        , 0.00 
                                        ,false
                                        ,false
                                    )';
                    EXECUTE stSqlAux;
                    stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.ultimo_registro_evento 
                                    (
                                        cod_registro
                                        ,cod_evento
                                        ,timestamp
                                    )
                                    VALUES 
                                    (
                                        '|| inCodRegistro ||'
                                        ,'|| reEventosConfigurados.cod_evento ||'
                                        ,'|| quote_literal(stTimestamp) ||'
                                    )';
                    EXECUTE stSqlAux;
                    
                    IF reRegistro.parcela IS NOT NULL THEN
                        stSqlAux := 'INSERT INTO folhapagamento'|| stEntidade ||'.registro_evento_parcela 
                                            (cod_registro
                                                ,cod_evento
                                                ,timestamp
                                                ,parcela
                                                ,mes_carencia
                                            )
                                            VALUES 
                                            (
                                                '|| inCodRegistro ||'
                                                ,'|| reEventosConfigurados.cod_evento ||'
                                                ,'|| quote_literal(stTimestamp) ||'
                                                ,'|| reRegistro.parcela ||'
                                                ,'|| inMesCarencia ||'
                                            )';
                        EXECUTE stSqlAux;
                    END IF;

                END IF;        
            
            inCodRegistro := inCodRegistro + 1;
    
            END LOOP; --END LOOP EVENTOS AUTOMATICOS CONFIGURADOS
        END IF; --IF cod contrato

        --Atribuindo o contrato para a validacao dos eventos automaticos
        inCodContrato := reRegistro.cod_contrato;
        
    END LOOP;-- END LOOP FINAL
    --FIM######################################

    --INÍCIO###################################
    --Processo de abertura de folha caso a mesma se encontre fecheda
    stSituacaoFolha := selectIntoVarchar('SELECT situacao
                                            FROM folhapagamento'|| stEntidade ||'.folha_situacao
                                               , (SELECT MAX(timestamp) as timestamp
                                                     FROM folhapagamento'|| stEntidade ||'.folha_situacao) max_folha_situacao
                                           WHERE max_folha_situacao.timestamp = folha_situacao.timestamp');
    IF stSituacaoFolha = 'f' THEN
        stSql := 'INSERT INTO folhapagamento'|| stEntidade ||'.folha_situacao (cod_periodo_movimentacao,situacao)
                  VALUES ('|| inCodPeriodoMovimentacaoAberta ||',''a'')';
        EXECUTE stSql;
    END IF;
    --FIM######################################

    --INÍCIO###################################
    --Processo de inclusão de registro de eventos de férias
    arDataFinal := string_to_array(dtFinal,'/');
    inAnoAnterior := arDataFinal[3]::integer-1;
    stSql := 'SELECT ferias.cod_contrato
                FROM pessoal'|| stEntidade ||'.ferias
                   , pessoal'|| stEntidade ||'.lancamento_ferias
               WHERE ferias.cod_ferias = lancamento_ferias.cod_ferias
                 AND ((ano_competencia = '|| quote_literal(arDataFinal[3]) ||' AND mes_competencia = '|| quote_literal(arDataFinal[2]) ||')
                      OR (to_char(dt_inicio,''yyyy-mm'') = '|| quote_literal(arDataFinal[3] ||'-'|| arDataFinal[2]) ||')
                      OR (to_char(dt_fim,''yyyy-mm'') = '|| quote_literal(arDataFinal[3] ||'-'|| arDataFinal[2]) ||' ))
                 ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        boRetorno := geraRegistroFerias(reRegistro.cod_contrato,inCodPeriodoMovimentacaoAberta,stExercicio,stEntidade);
    END LOOP;
    --FIM#####################################

    --INÍCIO###################################
    --Processo de inclusão de registro de eventos dos assentamento
    stSql := 'SELECT assentamento_gerado_contrato_servidor.cod_contrato
                   , assentamento_gerado.cod_assentamento
                   , to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') as periodo_inicial
                   , to_char(assentamento_gerado.periodo_final  ,''yyyy-mm'') as periodo_final
                FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                   , pessoal'|| stEntidade ||'.assentamento_gerado
                   , (SELECT cod_assentamento_gerado
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.assentamento_gerado
                      GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado
               WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                 AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                 AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                 AND NOT EXISTS (SELECT *
                                   FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                  WHERE assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp
                                    AND assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado)
                 AND NOT EXISTS (SELECT 1
                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                  WHERE assentamento_gerado_contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato)
                 AND to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') <= '|| quote_literal(arDataFinal[3] ||'-'|| arDataFinal[2]) ||'
                 AND to_char(assentamento_gerado.periodo_final  ,''yyyy-mm'') >= '|| quote_literal(arDataFinal[3] ||'-'|| arDataFinal[2]) ||' ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        boRetorno := selectIntoBoolean('SELECT TRUE AS retorno
                                          FROM pessoal'|| stEntidade ||'.assentamento_assentamento
                                         WHERE assentamento_assentamento.cod_assentamento = '|| reRegistro.cod_assentamento ||'
                                           AND assentamento_assentamento.cod_motivo = 2');
        boRegistrar := TRUE;
        IF boRetorno IS TRUE THEN
            stSql := 'SELECT TRUE as retorno
                        FROM pessoal'|| stEntidade ||'.ferias
                           , pessoal'|| stEntidade ||'.lancamento_ferias
                       WHERE ferias.cod_ferias = lancamento_ferias.cod_ferias
                         AND lancamento_ferias.pagar_13 IS TRUE
                         AND ferias.cod_contrato = '|| reRegistro.cod_contrato ||'
                         AND ((to_char(dt_inicio,''mm'')='|| quote_literal(arDataFinal[2]) ||' OR to_char(dt_fim,''mm'')='|| quote_literal(arDataFinal[2]) ||')
                          OR  (mes_competencia = '|| quote_literal(arDataFinal[2]) ||' AND ano_competencia = '|| quote_literal(arDataFinal[3]) ||'))';
            boRetorno := selectIntoBoolean(stSql);
            IF boRetorno IS TRUE THEN
                --No caso do assentamento FOR do motivo férias
                --e ass férias forem cadastradas com pagamento de 13 de férias
                --não registro eventos proporcionais.
                boRegistrar := FALSE;
            END IF;
        END IF;
        IF boRegistrar IS TRUE THEN
            boRetorno := registrarEventoPorAssentamento(reRegistro.cod_contrato,reRegistro.cod_assentamento,'incluir',stEntidade);
        END IF;
    END LOOP;
    --FIM#####################################


    --INÍCIO DO PROCESSO PARA ATUALIZAÇÃO DA PROGRESSÃO DOS CONTRATOS ######################
    stSql := 'SELECT contrato_servidor_inicio_progressao.*
                FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
                   , (  SELECT cod_contrato
                             , max(timestamp) as timestamp
                          FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
                      GROUP BY cod_contrato) as max_contrato_servidor_inicio_progressao
               WHERE contrato_servidor_inicio_progressao.cod_contrato = max_contrato_servidor_inicio_progressao.cod_contrato
                 AND contrato_servidor_inicio_progressao.timestamp = max_contrato_servidor_inicio_progressao.timestamp
                 AND NOT EXISTS (SELECT 1
                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                  WHERE contrato_servidor_inicio_progressao.cod_contrato = contrato_servidor_caso_causa.cod_contrato)';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        stDataFinal := to_char(to_date(dtFinal,'dd-mm-yyyy'),'yyyy-mm-dd');
        stDataInicial := to_char(reRegistro.dt_inicio_progressao,'yyyy-mm-dd');
        inDiferencaAnos  := date_part('year',age(stDataFinal::timestamp,stDataInicial::timestamp));
        inDiferencaMeses := date_part('month',age(stDataFinal::timestamp,stDataInicial::timestamp));
        inDiferencaMeses := inDiferencaAnos * 12 + inDiferencaMeses;


        inCodPadrao := selectIntoInteger('SELECT contrato_servidor_padrao.cod_padrao
                                            FROM pessoal'|| stEntidade ||'.contrato_servidor_padrao
                                               , (  SELECT cod_contrato
                                                         , max(timestamp) as timestamp
                                                      FROM pessoal'|| stEntidade ||'.contrato_servidor_padrao
                                                  GROUP BY cod_contrato) as max_contrato_servidor_padrao
                                           WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato
                                             AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp
                                             AND contrato_servidor_padrao.cod_contrato ='|| reRegistro.cod_contrato );

        nuHorasMensais := selectIntoNumeric('SELECT contrato_servidor_salario.horas_mensais
                                               FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                                                  , (  SELECT cod_contrato
                                                            , max(timestamp) as timestamp
                                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                                                     GROUP BY cod_contrato) as max_contrato_servidor_salario
                                              WHERE contrato_servidor_salario.cod_contrato = max_contrato_servidor_salario.cod_contrato
                                                AND contrato_servidor_salario.timestamp = max_contrato_servidor_salario.timestamp
                                                AND contrato_servidor_salario.cod_contrato ='|| reRegistro.cod_contrato);


        IF inCodPadrao IS NOT NULL THEN
            inCodNivelPadrao := selectIntoInteger('SELECT nivel_padrao_nivel.cod_nivel_padrao
                                                 FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                                                    , (  SELECT cod_padrao
                                                              , max(timestamp) as timestamp
                                                           FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                                                       GROUP BY cod_padrao) as max_nivel_padrao_nivel
                                                WHERE nivel_padrao_nivel.cod_padrao = max_nivel_padrao_nivel.cod_padrao
                                                  AND nivel_padrao_nivel.timestamp = max_nivel_padrao_nivel.timestamp
                                                  AND nivel_padrao_nivel.cod_padrao = '|| inCodPadrao ||'
                                                  AND nivel_padrao_nivel.qtdmeses <='|| inDiferencaMeses ||'
                                             ORDER BY nivel_padrao_nivel.qtdmeses desc LIMIT 1');

            nuHorasMensais := selectIntoNumeric('SELECT horas_mensais
                                                   FROM folhapagamento'|| stEntidade ||'.padrao
                                                  WHERE cod_padrao = '|| inCodPadrao);

            IF nuHorasMensais IS NULL OR nuHorasMensais = 0 THEN
                nuHorasMensais := 1;
            END IF;

            stSql := 'SELECT contrato_servidor_salario.*
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                           , (  SELECT cod_contrato
                                     , max(timestamp) as timestamp
                                  FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                              GROUP BY cod_contrato) as max_contrato_servidor_salario
                       WHERE contrato_servidor_salario.cod_contrato = max_contrato_servidor_salario.cod_contrato
                         AND contrato_servidor_salario.timestamp = max_contrato_servidor_salario.timestamp
                         AND contrato_servidor_salario.cod_contrato ='|| reRegistro.cod_contrato;
            FOR reSalario IN EXECUTE stSql
            LOOP
                nuHorasMensais := reSalario.horas_mensais;
                nuHorasSemanais := reSalario.horas_semanais;
                dtVigencia := reSalario.vigencia;
                nuSalarioAtual := reSalario.salario;
            END LOOP;

            IF inCodNivelPadrao IS NOT NULL THEN
                nuSalarioPadrao := selectIntoNumeric('SELECT nivel_padrao_nivel.valor
                                                 FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                                                    , (  SELECT cod_padrao
                                                              , max(timestamp) as timestamp
                                                           FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                                                       GROUP BY cod_padrao ) as max_nivel_padrao_nivel
                                                WHERE nivel_padrao_nivel.cod_padrao = max_nivel_padrao_nivel.cod_padrao
                                                  AND nivel_padrao_nivel.timestamp = max_nivel_padrao_nivel.timestamp
                                                  AND nivel_padrao_nivel.cod_padrao = '|| inCodPadrao ||'
                                                  AND nivel_padrao_nivel.qtdmeses <='|| inDiferencaMeses ||'
                                             ORDER BY nivel_padrao_nivel.qtdmeses desc LIMIT 1');
                nuSalarioHoraPadrao :=  nuSalarioPadrao / nuHorasMensais;
                nuSalario := nuSalarioHoraPadrao * nuHorasMensais;

                IF nuSalario > nuSalarioAtual THEN
                    stSql := 'INSERT INTO pessoal'|| stEntidade ||'.contrato_servidor_nivel_padrao (cod_contrato,cod_nivel_padrao,cod_periodo_movimentacao,reajuste) VALUES ('|| reRegistro.cod_contrato ||','|| inCodNivelPadrao ||','|| inCodPeriodoMovimentacaoAberta ||',true)';
                    EXECUTE stSql;
                END IF;
            ELSE
                nuHorasMensaisPadrao := selectIntoNumeric('SELECT horas_mensais
                                                             FROM folhapagamento'|| stEntidade ||'.padrao
                                                            WHERE cod_padrao = '|| inCodPadrao);

                IF nuHorasMensaisPadrao IS NULL OR nuHorasMensaisPadrao = 0 THEN
                    nuHorasMensaisPadrao := 1;
                END IF;

                nuSalarioPadrao := selectIntoNumeric('SELECT valor
                                                        FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                                                           , (  SELECT cod_padrao
                                                                     , max(timestamp) as timestamp
                                                                  FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                                                              GROUP BY cod_padrao) as max_padrao_padrao
                                                       WHERE padrao_padrao.cod_padrao = max_padrao_padrao.cod_padrao
                                                         AND padrao_padrao.timestamp = max_padrao_padrao.timestamp
                                                         AND padrao_padrao.cod_padrao = '|| inCodPadrao);
                nuSalarioHoraPadrao :=  nuSalarioPadrao / nuHorasMensaisPadrao;
                nuSalario := nuSalarioHoraPadrao * nuHorasMensais;
            END IF;

            IF nuSalario > nuSalarioAtual THEN
                stSql := 'INSERT INTO pessoal'|| stEntidade ||'.contrato_servidor_salario (cod_contrato,salario,horas_mensais,horas_semanais,vigencia,cod_periodo_movimentacao,reajuste) VALUES ('|| reRegistro.cod_contrato ||','|| nuSalario ||','|| nuHorasMensais ||','|| nuHorasSemanais ||','|| quote_literal(dtVigencia) ||','|| inCodPeriodoMovimentacaoAberta ||',true)';
                EXECUTE stSql;
            END IF;
        END IF;
    END LOOP;
    --FIM DO PROCESSO PARA ATUALIZAÇÃO DA PROGRESSÃO DOS CONTRATOS #########################

    --INÍCIO DO PROCESSO PARA ATUALIZAÇÃO DAS FALTAS DOS ESTAGIÁRIOS NA COMPETÊNCIA#########################
    --As faltas dos estagiários são zeradas na virada para a próxima competência
    stSql := 'SELECT estagiario_estagio_bolsa.*
                FROM estagio'|| stEntidade ||'.estagiario_estagio_bolsa
                   , (  SELECT cgm_instituicao_ensino
                             , cgm_estagiario
                             , cod_curso
                             , cod_estagio
                             , MAX(timestamp) as timestamp
                          FROM estagio'|| stEntidade ||'.estagiario_estagio_bolsa
                      GROUP BY cgm_instituicao_ensino
                             , cgm_estagiario
                             , cod_curso
                             , cod_estagio) AS max_estagiario_estagio_bolsa
               WHERE estagiario_estagio_bolsa.cgm_instituicao_ensino =  max_estagiario_estagio_bolsa.cgm_instituicao_ensino
                 AND estagiario_estagio_bolsa.cgm_estagiario         =  max_estagiario_estagio_bolsa.cgm_estagiario
                 AND estagiario_estagio_bolsa.cod_curso              =  max_estagiario_estagio_bolsa.cod_curso
                 AND estagiario_estagio_bolsa.cod_estagio            =  max_estagiario_estagio_bolsa.cod_estagio
                 AND estagiario_estagio_bolsa.timestamp              =  max_estagiario_estagio_bolsa.timestamp
                 AND estagiario_estagio_bolsa.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacaoFechada;
    FOR reRegistro IN EXECUTE stSql
    LOOP
        stSql := 'INSERT INTO estagio'|| stEntidade ||'.estagiario_estagio_bolsa (cgm_instituicao_ensino, cgm_estagiario, cod_curso, cod_estagio, cod_periodo_movimentacao, vl_bolsa)
                  VALUES ('|| reRegistro.cgm_instituicao_ensino ||','|| reRegistro.cgm_estagiario ||','|| reRegistro.cod_curso ||','|| reRegistro.cod_estagio ||','|| inCodPeriodoMovimentacaoAberta ||','|| reRegistro.vl_bolsa ||')';
        EXECUTE stSql;
    END LOOP;
    --FIM DO PROCESSO PARA ATUALIZAÇÃO DAS FALTAS DOS ESTAGIÁRIOS NA COMPETÊNCIA#########################

    RETURN TRUE;
END
$$ LANGUAGE plpgsql;
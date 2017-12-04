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
--    * Data de Criação: 00/00/0000
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28992 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-04-04 09:21:21 -0300 (Sex, 04 Abr 2008) $
--
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION calculaEventoPorContratoErro(INTEGER) RETURNS BOOLEAN as $$

DECLARE
    inCodContrato               ALIAS FOR $1;
    stSql                       VARCHAR := '';
    stSql2                      VARCHAR := '';
    stSql3                      VARCHAR := '';
    stSql4                      VARCHAR := '';
    reRegistro                  RECORD;
    boRetorno                   BOOLEAN := TRUE;
    stRetorno                   VARCHAR := '';
    stDelete                    VARCHAR := '';
    stCodigoEvento              VARCHAR := '';
    stNatureza                  VARCHAR := '';
    boAjusteEntreFolhas         VARCHAR := '';
    dtVigencia                  VARCHAR := '';
    stDadosRegistro             VARCHAR := '';
    arDadosRegistro             VARCHAR[];
    stTimestamp                 TIMESTAMP;
    stTimestampRegistro         TIMESTAMP;
    inCodRegistro               INTEGER;
    inCodSubDivisao             INTEGER;
    inCodFuncao                 INTEGER;
    inCodEspecialidade          INTEGER;
    inCodEvento                 INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodConfiguracao           INTEGER;
    inCodPrevidencia            INTEGER;
    inIndex                     INTEGER;
    inCodComplementar           INTEGER;
    inCodRegistroDescontoIRRF   INTEGER;
    inCodTipo                   INTEGER;
    nuREQuantidade              NUMERIC;
    nuREValor                   NUMERIC;
    nuREParcela                 NUMERIC;
    nuValor                     NUMERIC;
    nuTotalDescontoCalculo      NUMERIC;
    nuPercentualDesconto        NUMERIC;
    nuDescontoBase              NUMERIC;
    nuDescontoDesconto          NUMERIC;
    crCursor                    REFCURSOR;
    stEntidade               VARCHAR;
BEGIN    
    stEntidade              := recuperarBufferTexto('stEntidade');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodSubDivisao            := recuperarBufferInteiro('inCodSubDivisao');
    inCodFuncao                := recuperarBufferInteiro('inCodFuncao');
    inCodEspecialidade         := recuperarBufferInteiro('inCodEspecialidade');

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ordenado';
    execute stSql;
    
    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento_ordenado (
                 SELECT ultimo_registro_evento.cod_evento
                      , evento.codigo
                      , ultimo_registro_evento.cod_registro
                      , registro_evento_periodo.cod_contrato
                      , COALESCE(registro_evento.valor,0.00) as valor
                      , COALESCE(registro_evento.quantidade,0.00) as quantidade
                      , registro_evento.proporcional
                      , (SELECT COALESCE(registro_evento_parcela.parcela,0.00) FROM folhapagamento'||stEntidade||'.registro_evento_parcela
                          WHERE ultimo_registro_evento.cod_registro = registro_evento_parcela.cod_registro
                            AND ultimo_registro_evento.cod_evento = registro_evento_parcela.cod_evento
                            AND ultimo_registro_evento.timestamp = registro_evento_parcela.timestamp) as parcela                      
                      , registro_evento_periodo.cod_periodo_movimentacao
                      , ultimo_registro_evento.timestamp
                      , pega1FormulaEvento(ultimo_registro_evento.cod_evento,1,'||inCodSubDivisao||','||inCodFuncao||','||inCodEspecialidade||',evento.natureza) as formula
                      , evento.natureza
                      , 1 as cod_configuracao
                      , sequencia_calculo.sequencia
                   FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                      , folhapagamento'||stEntidade||'.ultimo_registro_evento
                      , folhapagamento'||stEntidade||'.registro_evento
                      , folhapagamento'||stEntidade||'.evento
                      , folhapagamento'||stEntidade||'.sequencia_calculo_evento
                      , folhapagamento'||stEntidade||'.sequencia_calculo
                  WHERE registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                    AND registro_evento_periodo.cod_contrato             = '||inCodContrato||'
                    AND registro_evento_periodo.cod_registro             = registro_evento.cod_registro
                    AND registro_evento.cod_registro                     = ultimo_registro_evento.cod_registro
                    AND registro_evento.cod_evento                       = evento.cod_evento
                    AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
                    AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
               ORDER BY sequencia_calculo.sequencia);';              
    EXECUTE stSql;

    stSql := 'SELECT count(cod_evento) as contador, cod_evento FROM folhapagamento'||stEntidade||'.registro_evento_ordenado GROUP BY cod_evento';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        IF reRegistro.contador > 1 THEN
            stSql4 := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ordenado WHERE proporcional = false AND cod_evento = '||reRegistro.cod_evento;
            EXECUTE stSql4;
        END IF;
    END LOOP;

    stSql := 'SELECT * FROM folhapagamento'||stEntidade||'.registro_evento_ordenado ORDER BY sequencia';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        inCodEvento         := criarBufferInteiro('inCodEvento',reRegistro.cod_evento);   
        stTimestamp         := criarBufferTimestamp('stTimestamp',reRegistro.timestamp); 
        inCodRegistro       := criarBufferInteiro('inCodRegistro',reRegistro.cod_registro);
        stCodigoEvento      := criarBufferTexto('stCodigoEvento',reRegistro.codigo);
        nuREQuantidade      := criarBufferNumerico('nuREQuantidade',reRegistro.quantidade);
        IF reRegistro.parcela is not null THEN
            nuREParcela         := criarBufferNumerico('nuREParcela',reRegistro.parcela);
        ELSE
            nuREParcela         := criarBufferNumerico('nuREParcela',0.00);
        END IF;            
        nuREValor           := criarBufferNumerico('nuREValor',reRegistro.valor);
        inCodConfiguracao   := criarBufferInteiro('inCodConfiguracao',reRegistro.cod_configuracao);
        --Validação para não executa a função executaGCNumerico em caso de eventos inseridos automaticamente
        --que não possuem cod_regime, cod_cargo na tabela configuracao_evento_caso_cargo ...
        IF reRegistro.formula IS NOT NULL THEN
            stRetorno       := executaGCNumerico( reRegistro.formula );
        END IF;
    END LOOP;
    
    --Chamada da função que realiza o ajuste da previdencia entre folhas
    boRetorno := processarAjustePrevidencia(inCodContrato);

    --Chamada da função que correige os valores das bases de dedução
    --4 | Evento de Base de Dedução sem Pensão Alimentícia
    --5 | Evento de Base de Dedução com Pensão Alimentícia
    boRetorno := processarValorBaseDeducao(inCodContrato);


    --Chamada da função que remove o evento de imposto de renda
    --Caso exista dependentes com pensão remove os evento de IRRF com pensão
    dtVigencia := recuperarBufferTexto('dtVigenciaIrrf');
    IF pega1QtdDependentesPensaoAlimenticia() > 0 THEN        
        inCodTipo = 3;
        --Chamada da função que realiza o ajuste do IRRF entre folhas
        --TRUE:  Ajuste de IRRF COM PENSAO
        boRetorno := processarAjusteIRRF(TRUE,inCodContrato);
    ELSE
        inCodTipo = 6;
        --Chamada da função que realiza o ajuste do IRRF entre folhas
        --FALSE: Ajuste de IRRF SEM PENSAO
        boRetorno := processarAjusteIRRF(FALSE,inCodContrato);
    END IF;

    --Procedimento que apagará o registro de evento conforme o ajute acima
    --Caso o ajuste de IRRF ocorra com pensão será excluido o registro de evento sem pensão
    --Caso o ajuste de IRRF ocorra sem pensão será excluido o registro de evento com pensão
    IF dtVigencia != 'null' THEN
        stDadosRegistro             := buscaDadosRegistroEventoDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao);
        arDadosRegistro             := string_to_array(stDadosRegistro,'#');
        inCodRegistroDescontoIRRF   := arDadosRegistro[2];
        IF inCodRegistroDescontoIRRF IS NOT NULL THEN
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_calculado             WHERE cod_registro = '||inCodRegistroDescontoIRRF;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_calculado_dependente  WHERE cod_registro = '||inCodRegistroDescontoIRRF;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo             WHERE cod_registro = '||inCodRegistroDescontoIRRF;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_parcela      WHERE cod_registro = '||inCodRegistroDescontoIRRF;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento       WHERE cod_registro = '||inCodRegistroDescontoIRRF;
            EXECUTE stSql;
        END IF;
    END IF;

    --Deletar o Evento de sistema calculados onde o valor FOR 0
    boRetorno := deletarEventosDeSistemaSalarioZerado(inCodContrato,inCodPeriodoMovimentacao);

    RETURN TRUE; 
END;
$$ LANGUAGE 'plpgsql';

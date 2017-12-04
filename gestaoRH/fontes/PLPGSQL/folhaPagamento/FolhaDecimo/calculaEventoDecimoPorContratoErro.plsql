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
--    * Data de Criação: 12/09/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 27631 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-01-21 09:16:22 -0200 (Seg, 21 Jan 2008) $
--
--    * Casos de uso: uc-04.05.11
--*/

CREATE OR REPLACE FUNCTION calculaEventoDecimoPorContratoErro() RETURNS BOOLEAN as $$

DECLARE
    stSql                           VARCHAR := '';
    reRegistro                      RECORD;
    stRetorno                       VARCHAR := '';
    stCodigoEvento                  VARCHAR := '';
    stFormula                       VARCHAR := '';
    stNatureza                      VARCHAR := '';
    stDesdobramento                 VARCHAR := '';
    dtVigencia                      VARCHAR := '';   
    stDadosRegistro                 VARCHAR := '';    
    arDadosRegistro                 VARCHAR[];
    stTimestamp                     TIMESTAMP;
    inCodContrato                   INTEGER;
    inCodRegistro                   INTEGER;
    inCodRegime                     INTEGER;
    inCodSubDivisao                 INTEGER;
    inCodFuncao                     INTEGER;
    inCodEspecialidade              INTEGER;
    inCodEvento                     INTEGER;
    inCodPeriodoMovimentacao        INTEGER;
    inCodConfiguracao               INTEGER;
    inCodTipo                       INTEGER;
    inCodRegistroDescontoIRRF       INTEGER;
    inCodPrevidencia                INTEGER;
    nuREQuantidade                  NUMERIC;
    nuREValor                       NUMERIC;
    nuREParcela                     NUMERIC;
    boRetorno                       BOOLEAN;
    stEntidade                      VARCHAR;
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    stDesdobramento            := recuperarBufferTexto('stDesdobramento');
    stEntidade := recuperarBufferTexto('stEntidade');
    
    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_decimo_ordenado';
    execute stSql;
    
    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento_decimo_ordenado (
              SELECT  evento.cod_evento
                    , evento.codigo
                    , registro_evento_decimo.cod_registro       
                    , registro_evento_decimo.cod_contrato
                    , COALESCE(registro_evento_decimo.valor,0.00) as valor
                    , COALESCE(registro_evento_decimo.quantidade,0.00) as quantidade
                    , registro_evento_decimo.desdobramento
                    , (SELECT COALESCE(parcela,0.00) as parcela
                        FROM folhapagamento'||stEntidade||'.registro_evento_decimo_parcela
                        WHERE ultimo_registro_evento_decimo.cod_registro = registro_evento_decimo_parcela.cod_registro
                        AND ultimo_registro_evento_decimo.cod_evento   = registro_evento_decimo_parcela.cod_evento
                        AND ultimo_registro_evento_decimo.timestamp    = registro_evento_decimo_parcela.timestamp
                        AND ultimo_registro_evento_decimo.desdobramento= registro_evento_decimo_parcela.desdobramento) AS parcela                              
                    , registro_evento_decimo.cod_periodo_movimentacao
                    , registro_evento_decimo.timestamp                      
                    , evento.natureza 
                    , sequencia_calculo.sequencia
                FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_decimo
                   , folhapagamento'||stEntidade||'.registro_evento_decimo   
                   , folhapagamento'||stEntidade||'.evento 
                   , folhapagamento'||stEntidade||'.sequencia_calculo_evento
                   , folhapagamento'||stEntidade||'.sequencia_calculo
               WHERE ultimo_registro_evento_decimo.cod_registro = registro_evento_decimo.cod_registro
                 AND ultimo_registro_evento_decimo.cod_evento = registro_evento_decimo.cod_evento
                 AND ultimo_registro_evento_decimo.timestamp = registro_evento_decimo.timestamp
                 AND ultimo_registro_evento_decimo.desdobramento = registro_evento_decimo.desdobramento
                 AND ultimo_registro_evento_decimo.cod_evento = evento.cod_evento
                 AND evento.cod_evento = sequencia_calculo_evento.cod_evento
                 AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia
                 AND registro_evento_decimo.cod_contrato = '||inCodContrato||'
                 AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                 AND registro_evento_decimo.desdobramento = '''||stDesdobramento||'''
            ORDER BY sequencia_calculo.sequencia)';
    EXECUTE stSql;

    inCodRegime                := recuperarBufferInteiro('inCodRegime');
    inCodSubDivisao            := recuperarBufferInteiro('inCodSubDivisao');
    inCodFuncao                := recuperarBufferInteiro('inCodFuncao');
    inCodEspecialidade         := recuperarBufferInteiro('inCodEspecialidade');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodConfiguracao          := recuperarBufferInteiro('inCodConfiguracao');
    stSql                      := '  SELECT * FROM folhapagamento'||stEntidade||'.registro_evento_decimo_ordenado ORDER BY sequencia';
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
        stNatureza          := criarBufferTexto('stNatureza',reRegistro.natureza);
        --Validação para não executa a função executaGCNumerico em caso de eventos inseridos automaticamente
        --que não possuem cod_regime, cod_cargo na tabela configuracao_evento_caso_cargo ...
        stFormula = pegaFormulaEvento(reRegistro.cod_evento,inCodConfiguracao,inCodSubDivisao,inCodFuncao,inCodEspecialidade);    
        IF stFormula IS NOT NULL THEN
            stRetorno       := executaGCNumerico( stFormula );
        END IF;
    END LOOP;

    --TODOS OS AJUSTES SÓ DEVERÃO SER FEITOS ENTRE CONTRATOS
    --NÃO DEVERÃO SER FEITOS AJUSTES ENTRE FOLHAS
        
    --Chamada para a função de ajuste de previdencia entre contratos do mesmo CGM
    boRetorno := processarAjustePrevidenciaDecimo();

    --Chamada da função que corrige os valores das bases de dedução
    --4 | Evento de Base de Dedução sem Pensão Alimentícia
    --5 | Evento de Base de Dedução com Pensão Alimentícia
    --são processadas as base em virtude dos ajustes da previdencia
    boRetorno := processarValorBaseDeducaoDecimo();

    --Chamada da função que remove o evento de imposto de renda
    --Caso exista dependentes com pensão remove os evento de IRRF com pensão
    dtVigencia := recuperarBufferTexto('dtVigenciaIrrf');
    IF dtVigencia != 'NULL' THEN
        IF pega1QtdDependentesPensaoAlimenticia() > 0 THEN
            inCodTipo := 3;
            --Chamada da função que realiza o ajuste do IRRF entre folhas
            --TRUE:  Ajuste de IRRF COM PENSAO
            boRetorno := processarAjusteIRRFDecimo(TRUE);
            -- Deleta o Evento  calculado onde do valor do desconto = 0.00
            boRetorno := deletarEventoIRRFDecimoZerado(dtVigencia,'6',inCodContrato,inCodPeriodoMovimentacao);
        ELSE 
            inCodTipo := 6;
            --Chamada da função que realiza o ajuste do IRRF entre folhas
            --TRUE:  Ajuste de IRRF COM PENSAO
            boRetorno := processarAjusteIRRFDecimo(FALSE);
            -- Deleta o Evento  calculado onde do valor do desconto = 0.00
            boRetorno := deletarEventoIRRFDecimoZerado(dtVigencia,'3',inCodContrato,inCodPeriodoMovimentacao);
        END IF;
        stDadosRegistro             := buscaDadosRegistroEventoDecimoDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao);              
        arDadosRegistro             := string_to_array(stDadosRegistro,'#');
        inCodRegistroDescontoIRRF   := arDadosRegistro[2];
        IF inCodRegistroDescontoIRRF IS NOT NULL THEN
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_decimo_calculado WHERE cod_registro           ='|| inCodRegistroDescontoIRRF;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_decimo WHERE cod_registro           ='|| inCodRegistroDescontoIRRF;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_decimo_parcela WHERE cod_registro    ='|| inCodRegistroDescontoIRRF;
            EXECUTE stSql;
            stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_decimo  WHERE cod_registro    ='|| inCodRegistroDescontoIRRF;
            EXECUTE stSql;
        END IF;
    END IF;

    --Deletar o Evento de sistema calculados onde o valor FOR 0
    boRetorno := deletarEventosDeSistemaDecimoZerado(inCodContrato,inCodPeriodoMovimentacao);
    RETURN TRUE; 
END;
$$ LANGUAGE 'plpgsql';

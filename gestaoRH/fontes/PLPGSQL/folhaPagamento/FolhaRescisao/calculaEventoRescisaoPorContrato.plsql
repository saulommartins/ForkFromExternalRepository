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
--    * Data de Criação: 18/10/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23402 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-20 16:57:16 -0300 (Qua, 20 Jun 2007) $
--
--    * Casos de uso: uc-04.05.18
--*/

CREATE OR REPLACE FUNCTION calculaEventoRescisaoPorContrato() RETURNS BOOLEAN as $$

DECLARE
    stSql                           VARCHAR := '';
    reRegistro                      RECORD;
    stRetorno                       VARCHAR := '';
    stCodigoEvento                  VARCHAR := '';
    stFormula                       VARCHAR := '';
    stNatureza                      VARCHAR := '';
    dtVigencia                      VARCHAR := '';   
    stDadosRegistro                 VARCHAR := '';    
    stDesdobramento                 VARCHAR := '';    
    arDadosRegistro                 VARCHAR[];
    arDesdobramento                 VARCHAR[];
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
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    
    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_ordenado';
    execute stSql;      
    
    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento_rescisao_ordenado (
       SELECT evento.cod_evento
            , evento.codigo
            , registro_evento_rescisao.cod_registro       
            , registro_evento_rescisao.cod_contrato
            , COALESCE(registro_evento_rescisao.valor,0.00) as valor
            , COALESCE(registro_evento_rescisao.quantidade,0.00) as quantidade
            , registro_evento_rescisao.desdobramento
            , (SELECT COALESCE(parcela,0.00) as parcela
                 FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela
                WHERE ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao_parcela.cod_registro
                  AND ultimo_registro_evento_rescisao.cod_evento   = registro_evento_rescisao_parcela.cod_evento
                  AND ultimo_registro_evento_rescisao.timestamp    = registro_evento_rescisao_parcela.timestamp
                  AND ultimo_registro_evento_rescisao.desdobramento= registro_evento_rescisao_parcela.desdobramento) AS parcela                              
            , registro_evento_rescisao.cod_periodo_movimentacao
            , registro_evento_rescisao.timestamp                      
            , evento.natureza     
            , sequencia_calculo.sequencia
         FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
            , folhapagamento'||stEntidade||'.registro_evento_rescisao   
            , folhapagamento'||stEntidade||'.evento 
            , folhapagamento'||stEntidade||'.sequencia_calculo_evento
            , folhapagamento'||stEntidade||'.sequencia_calculo
        WHERE ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro
          AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento
          AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp
          AND ultimo_registro_evento_rescisao.desdobramento = registro_evento_rescisao.desdobramento
          AND ultimo_registro_evento_rescisao.cod_evento = evento.cod_evento
          AND evento.cod_evento = sequencia_calculo_evento.cod_evento
          AND sequencia_calculo_evento.cod_sequencia = sequencia_calculo.cod_sequencia
          AND registro_evento_rescisao.cod_contrato = '||inCodContrato||'
          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
     ORDER BY sequencia_calculo.sequencia,
              registro_evento_rescisao.desdobramento)';

    EXECUTE stSql;

    inCodRegime                := recuperarBufferInteiro('inCodRegime');
    inCodSubDivisao            := recuperarBufferInteiro('inCodSubDivisao');
    inCodFuncao                := recuperarBufferInteiro('inCodFuncao');
    inCodEspecialidade         := recuperarBufferInteiro('inCodEspecialidade');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');

    stSql                      := 'SELECT * FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_ordenado ORDER BY sequencia';
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
        stDesdobramento     := criarBufferTexto('stDesdobramento',reRegistro.desdobramento);
        IF reRegistro.desdobramento = 'S' THEN
            inCodConfiguracao := 1;
        END IF;
        IF reRegistro.desdobramento = 'P' OR reRegistro.desdobramento = 'V' THEN
            inCodConfiguracao := 2;
        END IF;
        IF reRegistro.desdobramento = 'D' THEN
            inCodConfiguracao := 3;
        END IF;
        IF reRegistro.desdobramento = 'A' THEN
            inCodConfiguracao := 4;
        END IF;
        inCodConfiguracao = criarBufferInteiro('inCodConfiguracao',inCodConfiguracao);
        --Validação para não executa a função executaGCNumerico em caso de eventos inseridos automaticamente
        --que não possuem cod_regime, cod_cargo na tabela configuracao_evento_caso_cargo ...
        stFormula = pegaFormulaEvento(reRegistro.cod_evento,inCodConfiguracao,inCodSubDivisao,inCodFuncao,inCodEspecialidade);   
        IF stFormula IS NOT NULL THEN
            stRetorno       := executaGCNumerico( stFormula );            
        END IF;
    END LOOP;

    --Chamada para a função de ajuste de previdencia entre folha salário (se estiver fechada)
    --, folhas complementares e folha férias do mesmo CGM
    boRetorno := processarAjustePrevidenciaRescisao();

    --Chamada da função que corrige os valores das bases de dedução
    --4 | Evento de Base de Dedução sem Pensão Alimentícia
    --5 | Evento de Base de Dedução com Pensão Alimentícia
    --são processadas as base em virtude dos ajustes da previdencia
    boRetorno := processarValorBaseDeducaoRescisao('S');
    boRetorno := processarValorBaseDeducaoRescisao('D');

    --Chamada da função que remove o evento de imposto de renda
    --Caso exista dependentes com pensão remove os evento de IRRF com pensão
    dtVigencia := recuperarBufferTexto('dtVigenciaIrrf');
    IF pega1QtdDependentesPensaoAlimenticia() > 0 THEN
        inCodTipo := 3;
        --Chamada da função que realiza o ajuste do IRRF entre folhas
        --TRUE:  Ajuste de IRRF COM PENSAO
        boRetorno := processarAjusteIRRFRescisao(TRUE);
        ---- Deleta o Evento  calculado onde do valor do desconto = 0.00
        --boRetorno := deletarEventoIRRFRescisaoZerado(dtVigencia,'6',inCodContrato,inCodPeriodoMovimentacao);
    ELSE 
        inCodTipo := 6;
        --Chamada da função que realiza o ajuste do IRRF entre folhas
        --TRUE:  Ajuste de IRRF COM PENSAO
        boRetorno := processarAjusteIRRFRescisao(FALSE);
        ---- Deleta o Evento  calculado onde do valor do desconto = 0.00
        --boRetorno := deletarEventoIRRFRescisaoZerado(dtVigencia,'3',inCodContrato,inCodPeriodoMovimentacao);
    END IF;

    arDesdobramento := string_to_array('S#A#V#P#D','#');
    FOR inIndex IN 1 .. 5 LOOP      
	stDadosRegistro             := buscaDadosRegistroEventoRescisaoDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao,arDesdobramento[inIndex]);              
	arDadosRegistro             := string_to_array(stDadosRegistro,'#');
	inCodRegistroDescontoIRRF   := arDadosRegistro[2];
	IF inCodRegistroDescontoIRRF IS NOT NULL THEN      
	  stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado WHERE cod_registro         ='||inCodRegistroDescontoIRRF;
	  EXECUTE stSql;
	  stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_rescisao WHERE cod_registro         ='||inCodRegistroDescontoIRRF;
	  EXECUTE stSql;
	  stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_rescisao_parcela WHERE cod_registro  ='||inCodRegistroDescontoIRRF;
	  EXECUTE stSql;
	  stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao  WHERE cod_registro  ='||inCodRegistroDescontoIRRF;
	  EXECUTE stSql;
	END IF;
    END LOOP;

    --Deletar o Evento de sistema calculados onde o valor FOR 0
    boRetorno := deletarEventosDeSistemaRescisaoZerado(inCodContrato,inCodPeriodoMovimentacao);
    RETURN TRUE; 
EXCEPTION    
    WHEN others THEN 
        stSql := 'INSERT INTO folhapagamento'||stEntidade||'.log_erro_calculo_rescisao (cod_evento,cod_registro,timestamp,desdobramento,erro) VALUES ('||inCodEvento||','||inCodRegistro||','''||stTimestamp||''','''||stDesdobramento||''',''Erro ao calcular o evento.'')';       
        EXECUTE stSql;
        RETURN FALSE;
END;
$$ LANGUAGE 'plpgsql';

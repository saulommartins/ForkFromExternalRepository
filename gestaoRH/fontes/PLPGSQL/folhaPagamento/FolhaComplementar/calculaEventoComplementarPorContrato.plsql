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
--    $Revision: 25628 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-09-25 11:45:17 -0300 (Ter, 25 Set 2007) $
--
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION calculaEventoComplementarPorContrato(INTEGER) RETURNS BOOLEAN as $$
DECLARE
codContrato               ALIAS FOR $1;
stSql                     VARCHAR := '';
stSql2                    VARCHAR := '';
stSql4                    VARCHAR := '';
reRegistro                RECORD;
boRetorno                 BOOLEAN := TRUE;
stRetorno                 VARCHAR := '';
stCodigoEvento            VARCHAR := '';
stNatureza                VARCHAR := '';
dtVigencia                VARCHAR := '';
boAjusteEntreFolhas       VARCHAR := '';
stDadosRegistro           VARCHAR := '';
arDadosRegistro           VARCHAR[];
stTimestamp               TIMESTAMP;
stTimestampRegistro       TIMESTAMP;
inCodContrato             INTEGER;
inCodRegistro             INTEGER;
inCodRegime               INTEGER;
inCodSubDivisao           INTEGER;
inCodFuncao               INTEGER;
inCodEspecialidade        INTEGER;
inCodEvento               INTEGER;
inCodPeriodoMovimentacao  INTEGER;
inCodComplementar         INTEGER;
inCodConfiguracao         INTEGER;
inCodPrevidencia          INTEGER;
inIndex                   INTEGER;
inCodRegistroDescontoIRRF INTEGER;
inCodTipo                 INTEGER;
nuREQuantidade            NUMERIC;
nuREValor                 NUMERIC;
nuREParcela               NUMERIC;
nuValor                   NUMERIC;
nuTotalDescontoCalculo    NUMERIC;
nuPercentualDesconto      NUMERIC;
nuDescontoBase            NUMERIC;
nuDescontoDesconto        NUMERIC;
crCursor                  REFCURSOR;
stEntidade                VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodComplementar          := recuperarBufferInteiro('inCodComplementar');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodRegime                := recuperarBufferInteiro('inCodRegime');
    inCodSubDivisao            := recuperarBufferInteiro('inCodSubDivisao');
    inCodFuncao                := recuperarBufferInteiro('inCodFuncao');
    inCodEspecialidade         := recuperarBufferInteiro('inCodEspecialidade');

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ordenado';
    execute stSql;

    stSql := 'INSERT INTO folhapagamento'||stEntidade||'.registro_evento_ordenado (
                  SELECT ultimo_registro_evento_complementar.cod_evento
                       , evento.codigo
                       , ultimo_registro_evento_complementar.cod_registro
                       , registro_evento_complementar.cod_contrato
                       , COALESCE(registro_evento_complementar.valor,0.00) as valor
                       , COALESCE(registro_evento_complementar.quantidade,0.00) as quantidade
                       , false as proporcional
                       , (SELECT COALESCE(registro_evento_complementar_parcela.parcela,0.00) FROM folhapagamento'||stEntidade||'.registro_evento_complementar_parcela
                           WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar_parcela.cod_registro
                             AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar_parcela.cod_evento
                             AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar_parcela.timestamp) as parcela                      
                       , registro_evento_complementar.cod_periodo_movimentacao
                       , ultimo_registro_evento_complementar.timestamp
                       , pega1FormulaEvento(ultimo_registro_evento_complementar.cod_evento,1,'||inCodSubDivisao||','||inCodFuncao||','||inCodEspecialidade||',evento.natureza) as formula
                       , evento.natureza
                       , registro_evento_complementar.cod_configuracao
                       , sequencia_calculo.sequencia
                    FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                       , folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar
                       , folhapagamento'||stEntidade||'.evento
                       , folhapagamento'||stEntidade||'.sequencia_calculo_evento
                       , folhapagamento'||stEntidade||'.sequencia_calculo
                   WHERE registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                     AND registro_evento_complementar.cod_contrato             = '||codContrato||'
                     AND registro_evento_complementar.cod_complementar         = '||inCodComplementar||'      
                     AND registro_evento_complementar.cod_registro        = ultimo_registro_evento_complementar.cod_registro
                     AND registro_evento_complementar.cod_evento        = ultimo_registro_evento_complementar.cod_evento
                     AND registro_evento_complementar.cod_configuracao  = ultimo_registro_evento_complementar.cod_configuracao
                     AND registro_evento_complementar.timestamp        = ultimo_registro_evento_complementar.timestamp
                     AND registro_evento_complementar.cod_evento                       = evento.cod_evento
                     AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
                     AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
                ORDER BY sequencia_calculo.sequencia)';               
    EXECUTE stSql;

    stSql := 'SELECT count(cod_evento) as contador, cod_evento,cod_configuracao FROM folhapagamento'||stEntidade||'.registro_evento_ordenado GROUP BY cod_evento,cod_configuracao';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        IF reRegistro.contador > 1 THEN
            stSql4 := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ordenado WHERE cod_configuracao = '||reRegistro.cod_configuracao||' AND cod_evento = '||reRegistro.cod_evento;
            EXECUTE stSql4;
        END IF;
    END LOOP; 
--     stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ordenado
--                WHERE cod_registro IN ( 
--               SELECT registro_evento_ordenado.cod_registro
--                 FROM (SELECT count(cod_evento) as contador
--                            , cod_evento 
--                         FROM folhapagamento'||stEntidade||'.registro_evento_ordenado 
--                     GROUP BY cod_evento) as contador
--                   , folhapagamento'||stEntidade||'.registro_evento_ordenado 
--               WHERE registro_evento_ordenado.cod_evento = contador.cod_evento
--                 AND contador.contador >= 2
--                 AND registro_evento_ordenado.proporcional is false)';
--     execute stSql;    
    
   
    stSql := 'SELECT * FROM folhapagamento'||stEntidade||'.registro_evento_ordenado ORDER BY sequencia';
    --Código para realizar a inclusão na tabela evento_complementar_calculado
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

    --Chamada para a função de ajuste de previdencia entre folha salaário (se estiver fechada)
    --e folhas complementares do mesmo CGM
    boRetorno := processarAjustePrevidenciaComplementar();

    --Chamada da função que corrige os valores das bases de dedução
    --4 | Evento de Base de Dedução sem Pensão Alimentícia
    --5 | Evento de Base de Dedução com Pensão Alimentícia
    --são processadas as base em virtude dos ajustes da previdencia
    FOR inCodConfiguracao IN 1 .. 4
    LOOP
        boRetorno := processarValorBaseDeducaoComplementar(inCodConfiguracao);
    END LOOP;

    --Chamada da função que remove o evento de imposto de renda
    --Caso exista dependentes com pensão remove os evento de IRRF com pensão
    dtVigencia := recuperarBufferTexto('dtVigenciaIrrf');    
    IF pega1QtdDependentesPensaoAlimenticia() > 0 THEN
        inCodTipo := 3;
        --Chamada da função que realiza o ajuste do IRRF entre folhas
        --TRUE:  Ajuste de IRRF COM PENSAO
        FOR inCodConfiguracao IN 1 .. 4
        LOOP
            boRetorno := processarAjusteIRRFComplementar(TRUE,inCodConfiguracao);
        END LOOP;
        --Deleta o evento de desconto de IRRF = 0.00
        --boRetorno := deletarEventoIRRFComplementarZerado(dtVigencia,'6',inCodContrato,inCodPeriodoMovimentacao,inCodComplementar);              
    ELSE 
        inCodTipo := 6;
        --Chamada da função que realiza o ajuste do IRRF entre folhas
        --TRUE:  Ajuste de IRRF SEM PENSAO
        FOR inCodConfiguracao IN 1 .. 4
        LOOP
            boRetorno := processarAjusteIRRFComplementar(FALSE,inCodConfiguracao);
        END LOOP;
        --Deleta o evento de desconto de IRRF = 0.00
        --boRetorno := deletarEventoIRRFComplementarZerado(dtVigencia,'3',inCodContrato,inCodPeriodoMovimentacao,inCodComplementar);              
    END IF;
    stDadosRegistro             := buscaDadosRegistroEventoComplementarDeDescontoIRRF(dtVigencia,inCodTipo,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,inCodConfiguracao);              
    arDadosRegistro             := string_to_array(stDadosRegistro,'#');
    inCodRegistroDescontoIRRF   := arDadosRegistro[2];
    IF inCodRegistroDescontoIRRF IS NOT NULL THEN
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_complementar_calculado WHERE cod_registro         ='|| inCodRegistroDescontoIRRF;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_complementar WHERE cod_registro         ='|| inCodRegistroDescontoIRRF;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_complementar_parcela WHERE cod_registro  ='|| inCodRegistroDescontoIRRF;
        EXECUTE stSql;
        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar  WHERE cod_registro  ='|| inCodRegistroDescontoIRRF;
        EXECUTE stSql;
    END IF;

    --Deletar o Evento de sistema calculados onde o valor FOR 0
    boRetorno := deletarEventosDeSistemaComplementarZerado(inCodContrato,inCodPeriodoMovimentacao,inCodComplementar);
    RETURN TRUE;
EXCEPTION    
    WHEN others THEN 
        stSql := 'INSERT INTO folhapagamento'||stEntidade||'.log_erro_calculo_complementar (cod_configuracao,cod_evento,cod_registro,timestamp,erro) VALUES ('||inCodConfiguracao||','||inCodEvento||','||inCodRegistro||','''||stTimestamp||''',''Erro ao calcular o evento.'')';       
        EXECUTE stSql;
        RETURN FALSE;
END;
$$ LANGUAGE 'plpgsql';

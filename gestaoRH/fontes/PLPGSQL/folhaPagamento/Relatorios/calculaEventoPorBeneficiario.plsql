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
--    * Data de Criação: 26/03/2004
--
--
--    * @author Analista: Dagiane  
--    * @author Desenvolvedor: André Machado
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23125 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-07 10:29:24 -0300 (Qui, 07 Jun 2007) $
--
--    * Casos de uso: uc-04.05.57
--*/

CREATE OR REPLACE FUNCTION calculaEventoPorBeneficiario(INTEGER, INTEGER, VARCHAR) RETURNS NUMERIC as '

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodBeneficiario           ALIAS FOR $2;
    stEntidade               ALIAS FOR $3;
    stRelatorio                 VARCHAR := '''';
    inCodPeriodoMovimentacao    INTEGER;
    stSql                       VARCHAR := '''';
    reRegistro                  RECORD;
    stSql4                      VARCHAR := '''';
    inCodRegime                 INTEGER;
    inCodSubDivisao             INTEGER;    
    inCodEspecialidade          INTEGER;
    stSql3                      VARCHAR := '''';
    stNatureza                  VARCHAR := '''';
    inCodEvento                 INTEGER;
    stTimestamp                 TIMESTAMP;
    inCodRegistro               INTEGER;
    stCodigoEvento              VARCHAR := '''';
    nuREQuantidade              NUMERIC;
    nuREValor                   NUMERIC;
    nuREParcela                 NUMERIC;
    nuValorEvento               NUMERIC;
    nuQuantidadeEvento          NUMERIC;
    inCodConfiguracao           INTEGER;    
    stRetorno                   VARCHAR := '''';
    inCodFuncao                 INTEGER;
BEGIN   
    stRelatorio     := criarBufferTexto(''stRelatorio'',''s''); 
    inCodPeriodoMovimentacao    := PEGA0CODIGOPERIODOMOVIMENTACAOABERTA(  ); 
    
    stSql := ''CREATE TEMPORARY TABLE tmp_registro_evento_ordenado as
                 SELECT ultimo_registro_evento.cod_evento
                      , evento.codigo
                      , ultimo_registro_evento.cod_registro
                      , registro_evento_periodo.cod_contrato
                      , COALESCE(registro_evento.valor,0.00) as valor
                      , COALESCE(registro_evento.quantidade,0.00) as quantidade
                      , registro_evento.proporcional
                      , COALESCE(registro_evento_parcela.parcela,0.00) as parcela
                      , registro_evento_periodo.cod_periodo_movimentacao
                      , ultimo_registro_evento.timestamp
                      , cast(''''formula'''' as varchar) as formula
                      , evento.natureza
                      , 1 as cod_configuracao
                   FROM folhapagamento''||stEntidade||''.registro_evento_periodo
                      , folhapagamento''||stEntidade||''.ultimo_registro_evento
              LEFT JOIN folhapagamento''||stEntidade||''.registro_evento_parcela
                     ON ultimo_registro_evento.cod_registro = registro_evento_parcela.cod_registro
                    AND ultimo_registro_evento.cod_evento = registro_evento_parcela.cod_evento
                    AND ultimo_registro_evento.timestamp = registro_evento_parcela.timestamp
                      , folhapagamento''||stEntidade||''.registro_evento
                      , folhapagamento''||stEntidade||''.evento
                      , folhapagamento''||stEntidade||''.sequencia_calculo_evento
                      , folhapagamento''||stEntidade||''.sequencia_calculo
                  WHERE registro_evento_periodo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                    AND registro_evento_periodo.cod_contrato             = ''||codContrato||''
                    
                    AND registro_evento_periodo.cod_registro             = registro_evento.cod_registro
                    AND registro_evento.cod_registro                     = ultimo_registro_evento.cod_registro
                    AND registro_evento.cod_evento                       = ultimo_registro_evento.cod_evento
                    AND registro_evento.timestamp                        = ultimo_registro_evento.timestamp
                    AND registro_evento.cod_evento                       = evento.cod_evento
                    AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
                    AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
               ORDER BY sequencia_calculo.sequencia'';
    EXECUTE stSql;

    stSql := ''SELECT count(cod_evento) as contador, cod_evento FROM tmp_registro_evento_ordenado GROUP BY cod_evento'';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        IF reRegistro.contador > 1 THEN
            stSql4 := ''DELETE FROM tmp_registro_evento_ordenado WHERE proporcional = false AND cod_evento = ''||reRegistro.cod_evento;
            EXECUTE stSql4;
        END IF; 
    END LOOP;    

    --inCodContrato              := codContrato;
    inCodRegime                := recuperarBufferInteiro(''inCodRegime'');
    inCodSubDivisao            := recuperarBufferInteiro(''inCodSubDivisao'');
    inCodFuncao                := recuperarBufferInteiro(''inCodFuncao'');
    inCodEspecialidade         := recuperarBufferInteiro(''inCodEspecialidade'');
    
    stSql := ''SELECT * FROM tmp_registro_evento_ordenado'';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
       stNatureza := criarBufferTexto(''stNatureza'',reRegistro.natureza);
       stSql3 := ''UPDATE tmp_registro_evento_ordenado 
                      SET formula = pegaFormulaEvento(''||reRegistro.cod_evento||'',1,''||inCodSubDivisao||'',''||inCodFuncao||'',''||inCodEspecialidade||'') 
                    WHERE cod_evento = ''||reRegistro.cod_evento||''
                      AND cod_contrato = ''||reRegistro.cod_contrato||''
                      AND cod_periodo_movimentacao = ''||reRegistro.cod_periodo_movimentacao||'''';
       EXECUTE stSql3;
       DROP TABLE tmp_stNatureza;
    END LOOP;



    FOR reRegistro IN EXECUTE stSql
    LOOP        
        inCodEvento     := criarBufferInteiro(''inCodEvento'',reRegistro.cod_evento);   
        stTimestamp     := criarBufferTimestamp(''stTimestamp'',reRegistro.timestamp); 
        inCodRegistro   := criarBufferInteiro(''inCodRegistro'',reRegistro.cod_registro);
        stCodigoEvento  := criarBufferTexto(''stCodigoEvento'',reRegistro.codigo);
        nuREQuantidade  := criarBufferNumerico(''nuREQuantidade'',reRegistro.quantidade);
        nuREParcela     := criarBufferNumerico(''nuREParcela'',reRegistro.parcela);
        nuREValor       := criarBufferNumerico(''nuREValor'',reRegistro.valor);
        
        
        
        inCodConfiguracao   := criarBufferInteiro(''inCodConfiguracao'',reRegistro.cod_configuracao);
        --Validação para não executa a função executaGCNumerico em caso de eventos inseridos automaticamente
        --que não possuem cod_regime, cod_cargo na tabela configuracao_evento_caso_cargo ...
        IF reRegistro.formula IS NOT NULL THEN        
            stRetorno       := executaGCNumerico( reRegistro.formula );
        END IF;
    END LOOP;
    
    nuValorEvento      := recuperarBufferNumerico(''nuValorEvento'');
    nuQuantidadeEvento := recuperarBufferNumerico(''nuQuantidadeEvento'');
    
    RETURN nuValorEvento; 
END;
'LANGUAGE 'plpgsql';

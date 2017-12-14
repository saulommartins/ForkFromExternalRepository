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
--    * Data de Criação: 14/11/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23631 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-29 10:45:57 -0300 (Sex, 29 Jun 2007) $
--
--    * Casos de uso: uc-04.05.11
--*/

CREATE OR REPLACE FUNCTION processarAjustePrevidenciaDecimo() RETURNS BOOLEAN as '
DECLARE
    stSql                       VARCHAR := '''';
    reRegistro                  RECORD;
    reBases                     RECORD;
    reFaixaDesconto             RECORD;
    boRetorno                   BOOLEAN := TRUE;
    stCodigoEvento              VARCHAR := '''';
    stNatureza                  VARCHAR := '''';
    dtVigencia                  VARCHAR := '''';
    stTimestamp                 VARCHAR := '''';
    stDesdobramentoDesconto     VARCHAR := '''';
    stTimestampRegistro         TIMESTAMP;
    stTimestampDesconto         TIMESTAMP;
    inCodContrato               INTEGER;
    inCodRegistro               INTEGER;
    inCodRegime                 INTEGER;
    inCodEvento                 INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodPrevidencia            INTEGER;
    inNumCgm                    INTEGER;
    inCodRegistroDesconto       INTEGER;
    inCodEventoDesconto         INTEGER;
    inCountFolhaDecimo          INTEGER;
    nuTotalDescontoCalculo      NUMERIC := 0.00;
    nuPercentualDesconto        NUMERIC := 0.00;
    nuSomaBase                  NUMERIC := 0.00;
    nuSomaDesconto              NUMERIC := 0.00;
    crCursor                    REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
BEGIN
    --BUSCA CÓDIGO E TIMESTAMP DA PREVIDENCIA
    --PARA BUSCAR OS EVENTOS VINCULADOS A ESSA PREVIDENCIA
    inCodPrevidencia := recuperarBufferInteiro(''inCodPrevidenciaOficial'');
    IF inCodPrevidencia > 0 THEN
        stTimestamp      := pega1TimestampTabelaPrevidencia();
        inNumCgm                    := recuperarBufferInteiro(''inNumCgm'');
        inCodContrato               := recuperarBufferInteiro(''inCodContrato'');
        inCodPeriodoMovimentacao    := recuperarBufferInteiro(''inCodPeriodoMovimentacao'');
        inCountFolhaDecimo := selectIntoInteger(''SELECT count(*) as contador
                                        FROM (SELECT registro_evento_decimo.cod_contrato
                                                FROM folhapagamento''||stEntidade||''.registro_evento_decimo
                                                    , folhapagamento''||stEntidade||''.ultimo_registro_evento_decimo
                                                    , folhapagamento''||stEntidade||''.evento_decimo_calculado
                                                    , folhapagamento''||stEntidade||''.periodo_movimentacao
                                                    , pessoal''||stEntidade||''.servidor_contrato_servidor
                                                    , pessoal''||stEntidade||''.servidor
                                                WHERE registro_evento_decimo.cod_registro     = ultimo_registro_evento_decimo.cod_registro
                                                AND registro_evento_decimo.timestamp        = ultimo_registro_evento_decimo.timestamp
                                                AND registro_evento_decimo.cod_evento       = ultimo_registro_evento_decimo.cod_evento
                                                AND registro_evento_decimo.desdobramento    = ultimo_registro_evento_decimo.desdobramento
                                                AND registro_evento_decimo.cod_registro     = evento_decimo_calculado.cod_registro
                                                AND registro_evento_decimo.timestamp        = evento_decimo_calculado.timestamp_registro
                                                AND registro_evento_decimo.cod_evento       = evento_decimo_calculado.cod_evento
                                                AND registro_evento_decimo.desdobramento    = evento_decimo_calculado.desdobramento
                                                AND registro_evento_decimo.cod_contrato     = servidor_contrato_servidor.cod_contrato
                                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                                AND servidor.numcgm = ''||inNumCgm||''
                                                AND registro_evento_decimo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
												AND EXISTS (SELECT 1
                                                                FROM pessoal''||stEntidade||''.contrato_servidor_previdencia
                                                                    , (  SELECT cod_contrato
                                                                            , cod_previdencia
                                                                            , max(timestamp) as timestamp
                                                                            FROM pessoal''||stEntidade||''.contrato_servidor_previdencia
                                                                        GROUP BY cod_contrato  
                                                                            , cod_previdencia) as max_contrato_servidor_previdencia
                                                                WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                                                AND contrato_servidor_previdencia.cod_previdencia = max_contrato_servidor_previdencia.cod_previdencia
                                                                AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                                                AND contrato_servidor_previdencia.bo_excluido IS FALSE
                                                                AND contrato_servidor_previdencia.cod_contrato = registro_evento_decimo.cod_contrato
																AND contrato_servidor_previdencia.cod_previdencia = ''||inCodPrevidencia||'')
                                            GROUP BY registro_evento_decimo.cod_contrato) as decimo'');
        IF inCountFolhaDecimo > 1  THEN
            --Código para ajustes da previdência
            dtVigencia        := recuperarBufferTexto(''dtVigenciaPrevidencia'');
        
            stSql := ''SELECT *
                        FROM folhapagamento''||stEntidade||''.tipo_evento_previdencia'';
            FOR reRegistro IN EXECUTE stSql
            LOOP
                --Consulta que busca os eventos da previdencia
                stSql := ''SELECT evento.cod_evento
                                , evento.natureza
                            FROM folhapagamento''||stEntidade||''.previdencia_evento 
                                , folhapagamento''||stEntidade||''.evento
                            WHERE cod_tipo = ''||reRegistro.cod_tipo||''
                            AND cod_previdencia = ''||inCodPrevidencia||''
                            AND timestamp       = ''''''||stTimestamp||''''''
                            AND previdencia_evento.cod_evento = evento.cod_evento'';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO inCodEvento,stNatureza;
                CLOSE crCursor;           
    
                -------------------INÍCIO DO AJUSTE COM O DÉCIMO--------------------
                stSql := ''SELECT evento_decimo_calculado.valor
                                , evento_decimo_calculado.cod_registro
                                , evento_decimo_calculado.cod_evento
                                , evento_decimo_calculado.timestamp_registro
                                , evento_decimo_calculado.desdobramento
                                , registro_evento_decimo.cod_contrato
                            FROM folhapagamento''||stEntidade||''.registro_evento_decimo
                                , folhapagamento''||stEntidade||''.ultimo_registro_evento_decimo
                                , folhapagamento''||stEntidade||''.evento_decimo_calculado
                                , folhapagamento''||stEntidade||''.periodo_movimentacao
                                , pessoal''||stEntidade||''.servidor_contrato_servidor
                                , pessoal''||stEntidade||''.servidor
                            WHERE registro_evento_decimo.cod_registro     = ultimo_registro_evento_decimo.cod_registro
                            AND registro_evento_decimo.timestamp        = ultimo_registro_evento_decimo.timestamp
                            AND registro_evento_decimo.cod_evento       = ultimo_registro_evento_decimo.cod_evento
                            AND registro_evento_decimo.desdobramento    = ultimo_registro_evento_decimo.desdobramento
                            AND registro_evento_decimo.cod_registro     = evento_decimo_calculado.cod_registro
                            AND registro_evento_decimo.timestamp        = evento_decimo_calculado.timestamp_registro
                            AND registro_evento_decimo.cod_evento       = evento_decimo_calculado.cod_evento
                            AND registro_evento_decimo.desdobramento    = evento_decimo_calculado.desdobramento
                            AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato
                            AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                            AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                            AND servidor.numcgm = ''||inNumCgm||''
                            AND registro_evento_decimo.cod_periodo_movimentacao = ''||inCodPeriodoMovimentacao||''
                            AND registro_evento_decimo.cod_evento = ''||inCodEvento||'''';
                FOR reBases IN EXECUTE stSql
                LOOP
                    IF stNatureza = ''B'' THEN                        
                        nuSomaBase := nuSomaBase + reBases.valor;
                    END IF;
                    IF stNatureza = ''D'' AND NOT ( reBases.cod_contrato = inCodContrato ) THEN
                        nuSomaDesconto := nuSomaDesconto + reBases.valor;
                    END IF;
                    IF stNatureza = ''D'' AND reBases.cod_contrato = inCodContrato THEN
                        inCodRegistroDesconto     = reBases.cod_registro;
                        inCodEventoDesconto       = reBases.cod_evento;
                        stTimestampDesconto       = reBases.timestamp_registro;
                        stDesdobramentoDesconto   = reBases.desdobramento;
                    END IF;
                END LOOP;
                -------------------FIM DO AJUSTE COM O DÉCIMO-----------------------
            END LOOP;
            IF nuSomaBase > 0 THEN
                --Percentual de desconto baseado na faixa de desconto da tabela folhapagamento''||stEntidade||''.faixa_desconto
                nuPercentualDesconto := selectIntoNumeric(''SELECT percentual_desconto 
                                                    FROM folhapagamento''||stEntidade||''.faixa_desconto
                                                    , folhapagamento''||stEntidade||''.previdencia_previdencia
                                                WHERE valor_inicial <= ''||nuSomaBase||''
                                                    AND valor_final   >= ''||nuSomaBase||''
                                                    AND faixa_desconto.cod_previdencia = ''||inCodPrevidencia||''
                                                    AND faixa_desconto.timestamp_previdencia = ''''''||stTimestamp||''''''
                                                    AND previdencia_previdencia.timestamp       = faixa_desconto.timestamp_previdencia
                                                    AND previdencia_previdencia.cod_previdencia = faixa_desconto.cod_previdencia
                                                    AND previdencia_previdencia.vigencia        = ''''''||dtVigencia||'''''' '');
    
    
                
                IF nuPercentualDesconto IS NULL THEN
                    nuPercentualDesconto := selectIntoNumeric(''Select COALESCE(percentual_desconto,0.00) as percentual_desconto
                                        FROM folhapagamento''||stEntidade||''.faixa_desconto
                                            , folhapagamento''||stEntidade||''.previdencia_previdencia
                                        WHERE valor_final <= ''||nuSomaBase||''
                                            AND valor_inicial > 0.00
                                            AND faixa_desconto.cod_previdencia = ''||inCodPrevidencia||''
                                            AND faixa_desconto.timestamp_previdencia = ''''''||stTimestamp||''''''
                                            AND previdencia_previdencia.timestamp       = faixa_desconto.timestamp_previdencia
                                            AND previdencia_previdencia.cod_previdencia = faixa_desconto.cod_previdencia
                                            AND previdencia_previdencia.vigencia        = ''''''||dtVigencia||''''''
                                    ORDER BY valor_final DESC
                                        LIMIT 1'');
                    nuSomaBase := selectIntoNumeric(''Select COALESCE(valor_final,0.00) as valor_final
                                        FROM folhapagamento''||stEntidade||''.faixa_desconto
                                            , folhapagamento''||stEntidade||''.previdencia_previdencia
                                        WHERE valor_final <= ''||nuSomaBase||''
                                            AND valor_inicial > 0.00
                                            AND faixa_desconto.cod_previdencia = ''||inCodPrevidencia||''
                                            AND faixa_desconto.timestamp_previdencia = ''''''||stTimestamp||''''''
                                            AND previdencia_previdencia.timestamp       = faixa_desconto.timestamp_previdencia
                                            AND previdencia_previdencia.cod_previdencia = faixa_desconto.cod_previdencia
                                            AND previdencia_previdencia.vigencia        = ''''''||dtVigencia||''''''
                                    ORDER BY valor_final DESC
                                        LIMIT 1'');
                END IF;
                nuTotalDescontoCalculo := nuSomaBase * nuPercentualDesconto / 100;
                nuTotalDescontoCalculo := nuTotalDescontoCalculo - nuSomaDesconto;
                nuTotalDescontoCalculo := truncarNumerico(nuTotalDescontoCalculo,2);
        
                stSql := ''UPDATE folhapagamento''||stEntidade||''.evento_decimo_calculado SET valor = ''||nuTotalDescontoCalculo||'',
                                                                        quantidade = ''||nuPercentualDesconto||''
                            WHERE cod_evento         = ''||inCodEventoDesconto||''
                            AND cod_registro       = ''||inCodRegistroDesconto||''
                            AND desdobramento      = ''''''||stDesdobramentoDesconto||''''''
                            AND timestamp_registro = ''''''||stTimestampDesconto||'''''''';
                EXECUTE stSql;
            END IF;
        END IF;
    END IF;
    RETURN TRUE;
END;
'LANGUAGE 'plpgsql';

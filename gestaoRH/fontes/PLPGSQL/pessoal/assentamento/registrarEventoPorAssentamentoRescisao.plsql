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
--    * Data de Criação: 23/04/2007
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28478 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-03-11 09:09:08 -0300 (Ter, 11 Mar 2008) $
--
--    * Casos de uso: uc-04.04.44
--*/

CREATE OR REPLACE FUNCTION  registrarEventoPorAssentamentoRescisao(INTEGER,INTEGER,VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    inCodContrato                                       ALIAS FOR $1;
    inCodAssentamento                                   ALIAS FOR $2;
    stEntidadeParametro                              ALIAS FOR $3;
    stEntidade                                       VARCHAR := '';
    inQuantDiasMes                                      INTEGER;
    inQuantDiasAssentamento                             INTEGER:=0;
    inCodRegistro                                       INTEGER;
    nuValorQuantidadeRegistro                           NUMERIC;
    nuValorQuantidade                                   NUMERIC;
    nuQuantidadeValor                                   NUMERIC;    
    inQuantDiasAssentamentoTotalAfastamentoTemporario   INTEGER := 0;
    inQuantDiasAssentamentoTotalVantagem                INTEGER := 0;
    stSql                                               VARCHAR := '';
    stProporcional                                      VARCHAR := 'false';
    reAssentamento                                      RECORD;
    reEvento                                            RECORD;   
    rePeriodoMovimentacao                               RECORD;      
    boRetorno                                           BOOLEAN; 
    dtFinal                                             DATE;
    dtInicial                                           DATE;
    dtFinalPeriodoMovimentacao                          DATE;
    inCodPeriodoMovimentacao                            INTEGER;
BEGIN
    stEntidade := criarBufferEntidade(stEntidadeParametro);
    --Busca a data inicial e final do último período de movimentação
    stSql := 'SELECT (to_char(periodo_movimentacao.dt_final,''yyyy-mm'')||''-01'')::date as dt_inicial
                    , ((to_char(periodo_movimentacao.dt_final,''yyyy-mm'')||''-01'')::date + INTERVAL ''1 MONTH'')::date -1 as dt_final
                    , periodo_movimentacao.cod_periodo_movimentacao
           FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
       ORDER BY cod_periodo_movimentacao DESC
        LIMIT 1';
    FOR rePeriodoMovimentacao IN EXECUTE stSql LOOP
        dtFinal     := rePeriodoMovimentacao.dt_final;
        dtInicial   := rePeriodoMovimentacao.dt_inicial;
        dtFinalPeriodoMovimentacao := dtFinal;
        inCodPeriodoMovimentacao := rePeriodoMovimentacao.cod_periodo_movimentacao;
    END LOOP;
    inQuantDiasMes := calculaNrDiasAnoMes((to_char(dtFinal::date,'yyyy'))::integer,(to_char(dtFinal::date,'mm'))::integer);

    --Busca as informações do assentamento filtrando pelo código do contrato e código do assentamento
    stSql := 'SELECT assentamento_gerado.cod_assentamento
                    , to_date(to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') || ''-01'',''yyyy-mm-dd'') as periodo_inicial
                    , assentamento_gerado.periodo_final
                    , assentamento_validade.dt_inicial as dt_inicial_validade
                    , CASE WHEN assentamento_validade.dt_final IS NULL THEN ''9999-12-31''
                           ELSE assentamento_validade.dt_final END as dt_final_validade
                    , assentamento.evento_automatico
                    , assentamento.timestamp as timestamp_assentamento
                    , classificacao_assentamento.cod_tipo
                 FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                    , pessoal'|| stEntidade ||'.assentamento_gerado
                    , (   SELECT cod_assentamento_gerado
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.assentamento_gerado
                        GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado
                    , pessoal'|| stEntidade ||'.assentamento
                    , (   SELECT cod_assentamento
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.assentamento
                        GROUP BY cod_assentamento) AS max_assentamento
                    , pessoal'|| stEntidade ||'.assentamento_validade
                    , pessoal'|| stEntidade ||'.assentamento_assentamento
                    , pessoal'|| stEntidade ||'.classificacao_assentamento
                WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                  AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                  AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                  AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento
                  AND assentamento.cod_assentamento = assentamento_validade.cod_assentamento
                  AND assentamento.timestamp = assentamento_validade.timestamp
                  AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                  AND assentamento.timestamp = max_assentamento.timestamp
                  AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                  AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao
                  AND classificacao_assentamento.cod_tipo = 3
                  AND assentamento.cod_assentamento = '|| inCodAssentamento ||'
                  AND assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                  --AND (to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') = '|| quote_literal(to_char(rePeriodoMovimentacao.dt_final,'yyyy-mm')) ||'
                  --  OR to_char(assentamento_gerado.periodo_final,''yyyy-mm'')   = '|| quote_literal(to_char(rePeriodoMovimentacao.dt_final,'yyyy-mm')) ||') 
                  AND NOT EXISTS (SELECT *
                                    FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                   WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                     AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp) ';
    FOR reAssentamento IN EXECUTE stSql
    LOOP
        --Valida se a data inicial ou final de validade do assentamento se encontra dentro do período de movimentacão
        IF reAssentamento.dt_inicial_validade >= dtInicial AND reAssentamento.dt_inicial_validade <= dtFinal 
        OR reAssentamento.dt_final_validade   >= dtInicial AND reAssentamento.dt_final_validade   <= dtFinal 
        OR reAssentamento.dt_inicial_validade <= dtInicial AND reAssentamento.dt_final_validade   >= dtFinal
        THEN             
            IF reAssentamento.periodo_final IS NULL THEN
                reAssentamento.periodo_final := dtFinalPeriodoMovimentacao;
            END IF;

            --######Período inicial dentro da competência
            IF to_char(reAssentamento.periodo_inicial,'mm/yyyy')  = to_char(dtFinal,'mm/yyyy') AND
               to_char(reAssentamento.periodo_final,'mm/yyyy')   != to_char(dtFinal,'mm/yyyy')
            THEN
                inQuantDiasAssentamento := (inQuantDiasMes - to_char(reAssentamento.periodo_inicial,'dd')::integer);
                IF inQuantDiasAssentamento = 0 THEN
                    inQuantDiasAssentamento := 1;
                END IF;
            END IF;
            --######

            --######Período final dentro da competência
            IF to_char(reAssentamento.periodo_inicial,'mm/yyyy') != to_char(dtFinal,'mm/yyyy') AND
               to_char(reAssentamento.periodo_final,'mm/yyyy')    = to_char(dtFinal,'mm/yyyy')
            THEN
                inQuantDiasAssentamento := to_char(reAssentamento.periodo_final,'dd');
            END IF;
            --######

            --######Período integralmente dentro da competência        
            IF to_char(reAssentamento.periodo_inicial,'yyyy-mm') = to_char(dtFinal,'yyyy-mm') AND
               to_char(reAssentamento.periodo_final,'yyyy-mm')   = to_char(dtFinal,'yyyy-mm')
            THEN
                inQuantDiasAssentamento := (to_char(reAssentamento.periodo_final,'dd')::integer-to_char(reAssentamento.periodo_inicial,'dd')::integer) + 1;
                IF to_char(reAssentamento.periodo_inicial,'dd') = to_char(dtFinalPeriodoMovimentacao,'dd')
                   AND inQuantDiasAssentamento = 0 THEN
                    inQuantDiasAssentamento := 1;
                END IF;
            END IF;
            --######

            --######Período inicía antes da competência e termina depois da competência
            IF to_char(reAssentamento.periodo_inicial,'yyyy-mm') < to_char(dtFinal,'yyyy-mm') AND
               to_char(reAssentamento.periodo_final,'yyyy-mm')   > to_char(dtFinal,'yyyy-mm')
            THEN
               inQuantDiasAssentamento := inQuantDiasMes;
            END IF;
            --######

            IF inQuantDiasAssentamento = 31 OR (inQuantDiasAssentamento = 28 AND to_char(dtFinal,'mm')::integer = 02) THEN
                inQuantDiasAssentamento := 30;
                inQuantDiasMes := 30;
            ELSE
                IF inQuantDiasAssentamento = 30 AND inQuantDiasMes = 31 THEN
                    inQuantDiasAssentamento := 29;
                    inQuantDiasMes := 30;
                END IF;
            END IF;

            --INÍCIO EVENTOS AUTOMÁTICOS A SEREM PROPORCIONALIDADOS--
            --Consulta que busca os eventos proporcionais automáticos do assentamento
            stSql := 'SELECT evento.*
                            , evento_evento.valor_quantidade
                            , evento_evento.unidade_quantitativa
                         FROM pessoal'|| stEntidade ||'.assentamento_evento_proporcional
                            , folhapagamento'|| stEntidade ||'.evento
                            , folhapagamento'|| stEntidade ||'.evento_evento
                            , (  SELECT cod_evento
                                      , max(timestamp) as timestamp
                                  FROM folhapagamento'|| stEntidade ||'.evento_evento
                               GROUP BY cod_evento) as max_evento_evento
                        WHERE assentamento_evento_proporcional.cod_assentamento = '|| reAssentamento.cod_assentamento ||'
                          AND assentamento_evento_proporcional.timestamp = '|| quote_literal(reAssentamento.timestamp_assentamento) ||'
                          AND assentamento_evento_proporcional.cod_evento = evento.cod_evento
                          AND evento.cod_evento = evento_evento.cod_evento
                          AND evento_evento.cod_evento = max_evento_evento.cod_evento
                          AND evento_evento.timestamp = max_evento_evento.timestamp';
            --Condição para Assentamentos de Afastamento Permanente
            FOR reEvento IN EXECUTE stSql
            LOOP
                --Consulta que verifica se o evento é um evento do tipo fixo e se está registrado
                --O evento só será registrado como proporcional se o mesmo se encontra registrado como fixo
                --caso contrário não faz nada
                nuValorQuantidadeRegistro := selectIntoNumeric('SELECT CASE evento.fixado 
                                                              WHEN ''V'' THEN registro_evento_rescisao.valor
                                                              WHEN ''Q'' THEN registro_evento_rescisao.quantidade 
                                                               END as valor_quantidade
                                                         FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                                            , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                                                            , folhapagamento'|| stEntidade ||'.evento
                                                        WHERE registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro
                                                          AND registro_evento_rescisao.cod_evento   = ultimo_registro_evento_rescisao.cod_evento
                                                          AND registro_evento_rescisao.timestamp    = ultimo_registro_evento_rescisao.timestamp                    
                                                          AND registro_evento_rescisao.desdobramento= ultimo_registro_evento_rescisao.desdobramento
                                                          AND registro_evento_rescisao.cod_evento = evento.cod_evento
                                                          AND registro_evento_rescisao.cod_evento = '|| reEvento.cod_evento ||'
                                                          AND registro_evento_rescisao.cod_contrato = '|| inCodContrato ||'
                                                          AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                          AND registro_evento_rescisao.desdobramento = ''S''   
                                                          AND evento.tipo = ''F'' ');
                IF nuValorQuantidadeRegistro IS NOT NULL THEN
                    nuQuantidadeValor := (nuValorQuantidadeRegistro / 30) * inQuantDiasAssentamento;
                    boRetorno := alterarRegistroEventoAutomaticoRescisao(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,reEvento.fixado,'S',nuQuantidadeValor);           
                END IF;
            END LOOP;            
            --FIM EVENTOS AUTOMÁTICOS PROPORCIONAIS--
        END IF; 
    END LOOP;
    RETURN true;
END;
 $$ LANGUAGE 'plpgsql'; 

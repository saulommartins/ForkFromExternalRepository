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

CREATE OR REPLACE FUNCTION  pega1QuantDiasAfastadosNaCompetencia(VARCHAR) RETURNS INTEGER as $$ 
DECLARE
    stCodMotivoParametro                                ALIAS FOR $1;
    inQuantDiasMes                                      INTEGER;
    inQuantDiasAfastamento                              INTEGER := 0;
    inCodPeriodoMovimentacao                            INTEGER;
    inCodRegistro                                       INTEGER;
    inCodContrato                                       INTEGER;	
    inQuantDiasAfastamentoTotalAfastamentoTemporario    INTEGER := 0;
    inQuantDiasAfastamentoTotalVantagem                 INTEGER := 0;
    inCodServidor                                       INTEGER;
    inNumCgm                                            INTEGER;	
    nuValorQuantidadeRegistro                           NUMERIC;
    nuValorQuantidade                                   NUMERIC;
    nuQuantidadeValor                                   NUMERIC;   
    dtInicialPeriodoMovimentacao                        DATE;
    dtFinalPeriodoMovimentacao                          DATE;
    stSql                                               VARCHAR := '';
    stProporcional                                      VARCHAR := 'false';
	stDataInicialCompetencia                            VARCHAR := '';
	stDataFinalCompetencia                              VARCHAR := '';
    stEntidade                                          VARCHAR := '';
    reAssentamento                                      RECORD;
    reEvento                                            RECORD;   
    boRetorno                                           BOOLEAN; 
    boAutomatico                                        BOOLEAN:=TRUE;
    boAcumulaDiasAfastamento                            BOOLEAN:=FALSE;
	boAssentamentoForadaCompetencia                     BOOLEAN:=FALSE;
    crCursor                                            REFCURSOR;
BEGIN    
        
	stEntidade := recuperarBufferTexto('stEntidade');
	inCodContrato := recuperarBufferInteiro('inCodContrato');
	stDataFinalCompetencia := recuperarBufferTexto('stDataFinalCompetencia');      
	dtFinalPeriodoMovimentacao := substr(stDataFinalCompetencia,1,10) ; 
	dtInicialPeriodoMovimentacao := to_char(dtFinalPeriodoMovimentacao,'yyyy-mm') ||'-01';    
		
        --Pega a qte de dias do mes, conforme competencia
        inQuantDiasMes := calculaNrDiasAnoMes(to_char(dtFinalPeriodoMovimentacao,'yyyy')::integer,to_char(dtFinalPeriodoMovimentacao,'mm')::integer);
		
        --Busca as informações do assentamento filtrando pelo código do contrato e código do assentamento
        stSql := 'SELECT CASE WHEN assentamento.quant_dias_onus_empregador IS NOT NULL THEN 
                                   (assentamento_gerado.periodo_inicial + assentamento.quant_dias_onus_empregador)
                               ELSE assentamento_gerado.periodo_inicial END AS periodo_inicial
                        , CASE WHEN assentamento_gerado.periodo_final IS NOT NULL THEN
                                    assentamento_gerado.periodo_final
                               ELSE '|| quote_literal(dtFinalPeriodoMovimentacao) ||' END AS periodo_final
                        , assentamento_gerado.automatico
                        , assentamento_gerado.cod_assentamento
                        , assentamento_validade.dt_inicial as dt_inicial_validade
                        , CASE WHEN assentamento_validade.dt_final IS NULL THEN ''9999-12-31''
                               ELSE assentamento_validade.dt_final END as dt_final_validade
                        , assentamento.evento_automatico
                        , assentamento.timestamp as timestamp_assentamento
                        , classificacao_assentamento.cod_tipo
                        , assentamento_assentamento.cod_motivo
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
					  AND NOT ((to_char(assentamento_gerado.periodo_final::date,''yyyy-mm'') < to_char(('|| quote_literal(dtFinalPeriodoMovimentacao) ||')::date,''yyyy-mm'')) OR
			                   (to_char(assentamento_gerado.periodo_inicial::date,''yyyy-mm'') > to_char(('|| quote_literal(dtFinalPeriodoMovimentacao) ||')::date,''yyyy-mm'')))	
					  AND assentamento_assentamento.cod_motivo in ('|| stCodMotivoParametro ||')
                      AND assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                      AND NOT EXISTS (SELECT *
                                        FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                       WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                         AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp) ';
        FOR reAssentamento IN EXECUTE stSql
        LOOP                
            --Valida se a data inicial ou final de validade do assentamento se encontra dentro do período de movimentacão
            IF reAssentamento.dt_inicial_validade >= dtInicialPeriodoMovimentacao AND reAssentamento.dt_inicial_validade <= dtFinalPeriodoMovimentacao 
            OR reAssentamento.dt_final_validade   >= dtInicialPeriodoMovimentacao AND reAssentamento.dt_final_validade   <= dtFinalPeriodoMovimentacao 
            OR reAssentamento.dt_inicial_validade <= dtInicialPeriodoMovimentacao AND reAssentamento.dt_final_validade   >= dtFinalPeriodoMovimentacao
            THEN   
                IF reAssentamento.periodo_final IS NULL THEN
                    reAssentamento.periodo_final := dtFinalPeriodoMovimentacao;
                END IF;             
                
				--##### Calculo da quantidade de dias Afastados do assentamento
                IF reAssentamento.periodo_inicial = reAssentamento.periodo_final THEN
                    inQuantDiasAfastamento := 1;
					boAcumulaDiasAfastamento := TRUE;
                ELSE                
                    --######Período inicial dentro da competência
                    IF to_char(reAssentamento.periodo_inicial,'mm/yyyy')  = to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') AND
                    to_char(reAssentamento.periodo_final,'mm/yyyy')   != to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') 
                    THEN
                        inQuantDiasAfastamento := (inQuantDiasMes - to_char(reAssentamento.periodo_inicial,'dd')::integer)+1;
                        boAcumulaDiasAfastamento := TRUE;
                    END IF;
    
                    --######Período final dentro da competência
                    IF to_char(reAssentamento.periodo_inicial,'mm/yyyy') != to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') AND
                    to_char(reAssentamento.periodo_final,'mm/yyyy')    = to_char(dtFinalPeriodoMovimentacao,'mm/yyyy')
                    THEN
                        inQuantDiasAfastamento := to_char(reAssentamento.periodo_final,'dd');					
					    inQuantDiasMes := 30;
                        boAcumulaDiasAfastamento := TRUE;
                    END IF;
                    --######
                    --######Período integralmente dentro da competência        
                    IF to_char(reAssentamento.periodo_inicial,'yyyy-mm') = to_char(dtFinalPeriodoMovimentacao,'yyyy-mm') AND
                    to_char(reAssentamento.periodo_final,'yyyy-mm')   = to_char(dtFinalPeriodoMovimentacao,'yyyy-mm')
                    THEN               
                        inQuantDiasAfastamento := (to_char(reAssentamento.periodo_final,'dd')::integer-to_char(reAssentamento.periodo_inicial,'dd')::integer)+1;
                        boAcumulaDiasAfastamento := TRUE;
                    END IF;                
                    --######
                    --######Período inicía antes da competência e termina depois da competência
                    IF to_char(reAssentamento.periodo_inicial,'yyyy-mm') < to_char(dtFinalPeriodoMovimentacao,'yyyy-mm') AND
                    to_char(reAssentamento.periodo_final,'yyyy-mm')   > to_char(dtFinalPeriodoMovimentacao,'yyyy-mm')
                    THEN
                    inQuantDiasAfastamento := inQuantDiasMes;
                    boAcumulaDiasAfastamento := TRUE;
                    END IF;
                    --######
                    --######Período inicia/termina antes ou depois da competência , ou seja, fora do intervalo nao tem afastamento. 
                    IF (to_char(reAssentamento.periodo_final::date,'yyyy-mm')   < to_char(dtInicialPeriodoMovimentacao::date,'yyyy-mm')  OR
					    to_char(reAssentamento.periodo_inicial::date,'yyyy-mm') > to_char(dtFinalPeriodoMovimentacao,'yyyy-mm')) THEN
						inQuantDiasAfastamento := 0;
						boAssentamentoForadaCompetencia := TRUE;
                    END IF;
                    --######					
                END IF;
                --####
                --Quando a quantidade de dias de afastamento FOR de 31 dias gerado por um período de 01 a 31 do mês corrente,
                --deverá ser substituído a quantidade de dias afastado por 30 porque a quantidade não pode ser superior a 30
                --No caso da quantidade de dias afastado FOR 28 ou 29 e o mês FOR fevereiro também deverá ser substituido por 
                --30 a quantidade de dias afastado.
                IF inQuantDiasAfastamento = 31 OR ((inQuantDiasAfastamento = 28 OR inQuantDiasAfastamento = 29) AND to_char(dtFinalPeriodoMovimentacao,'mm') = '02') THEN
                    inQuantDiasAfastamento := 30;
                    inQuantDiasMes := 30;
                END IF;
                -- Quando a quantidade de dias de afastamento for negativa devido a soma de dias de onus do empregador
				IF inQuantDiasAfastamento < 0.00 THEN
                    inQuantDiasAfastamento := 0;
                    inQuantDiasMes := 30;
                END IF;

				--Condição para Acumular em inQuantDiasAfastamentoTotalAfastamentoTemporario as  inQuantDiasAfastamento quando for 
				--Assentamentos de Afastamento e estes estiverem dentro do período de movimentacao.
                IF (reAssentamento.cod_tipo = 2 OR reAssentamento.cod_tipo = 1) AND boAcumulaDiasAfastamento IS TRUE AND boAssentamentoForadaCompetencia IS FALSE
				THEN	
                   inQuantDiasAfastamentoTotalAfastamentoTemporario := inQuantDiasAfastamentoTotalAfastamentoTemporario  + inQuantDiasAfastamento; 
                END IF;              
                IF inQuantDiasAfastamentoTotalAfastamentoTemporario IS NULL THEN
				   inQuantDiasAfastamentoTotalAfastamentoTemporario := 0;
                END IF;
			END IF;
		END LOOP;
    RETURN inQuantDiasAfastamentoTotalAfastamentoTemporario;
END;
$$ LANGUAGE 'plpgsql'; 


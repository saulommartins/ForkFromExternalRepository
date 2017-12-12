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
--    * FunÃ§Ã£o PLSQL
--    * Data de CriaÃ§Ã£o: 23/08/2006
--
--
--    * @author Analista: VandrÃ© Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 24590 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-08-08 10:14:10 -0300 (Qua, 08 Ago 2007) $
--
--    * Casos de uso: uc-04.04.14
--*/

CREATE OR REPLACE FUNCTION  registrarEventoPorAssentamento(INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    inCodContrato                                       ALIAS FOR $1;
    inCodAssentamento                                   ALIAS FOR $2;
    stAcao                                              ALIAS FOR $3;
    stEntidadeParametro                                 ALIAS FOR $4;
    stEntidade                                          VARCHAR := '';
    inQuantDiasMes                                      INTEGER;
    inQuantDiasAfastamento                              INTEGER := 0;
    inCodPeriodoMovimentacao                            INTEGER;
    inCodRegistro                                       INTEGER;
    nuValorQuantidadeRegistro                           NUMERIC;
    nuValorQuantidade                                   NUMERIC;
    nuQuantidadeValor                                   NUMERIC;    
    inQuantDiasAfastamentoTotalAfastamentoTemporario    INTEGER := 0;
    inQuantDiasAfastamentoTotalVantagem                 INTEGER := 0;
    inCodServidor                                       INTEGER;
    inNumCgm                                            INTEGER;
    dtInicialPeriodoMovimentacao                        DATE;
    dtFinalPeriodoMovimentacao                          DATE;
    stSql                                               VARCHAR := '';
    stProporcional                                      VARCHAR := 'false';
    reAssentamento                                      RECORD;
    reEvento                                            RECORD;   
    boRetorno                                           BOOLEAN; 
    boAutomatico                                        BOOLEAN:=TRUE;
    boAcumulaDiasAfastamento                            BOOLEAN:=FALSE;
	boAssentamentoForadaCompetencia                     BOOLEAN:=FALSE;
    crCursor                                            REFCURSOR;
BEGIN    
    stEntidade := criarBufferEntidade(stEntidadeParametro);
    IF pega0SituacaoDaFolhaSalario() = 'a' THEN
        --Busca a data inicial e final do Ãºltimo perÃ­odo de movimentaÃ§Ã£o
        stSql := 'SELECT periodo_movimentacao.dt_inicial
                        , periodo_movimentacao.dt_final
                        , periodo_movimentacao.cod_periodo_movimentacao
                     FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                 ORDER BY cod_periodo_movimentacao DESC
                    LIMIT 1';
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO dtInicialPeriodoMovimentacao,dtFinalPeriodoMovimentacao,inCodPeriodoMovimentacao;
        CLOSE crCursor;
        dtInicialPeriodoMovimentacao := to_char(dtFinalPeriodoMovimentacao,'yyyy-mm')||'-01';
        dtFinalPeriodoMovimentacao   := (dtInicialPeriodoMovimentacao::date + INTERVAL '1 MONTH')::date -1;

        inQuantDiasMes := calculaNrDiasAnoMes(to_char(dtFinalPeriodoMovimentacao,'yyyy')::integer,to_char(dtFinalPeriodoMovimentacao,'mm')::integer);

        --Busca as informações do assentamento filtrando pelo código do contrato e código do assentamento        
        stSql := 'SELECT CASE WHEN assentamento.quant_dias_onus_empregador IS NOT NULL THEN 
                                   (assentamento_gerado.periodo_inicial + assentamento.quant_dias_onus_empregador)
                               ELSE assentamento_gerado.periodo_inicial END AS periodo_inicial
                        , CASE WHEN assentamento_gerado.periodo_final IS NOT NULL THEN
                                    assentamento_gerado.periodo_final
                               ELSE '''||dtFinalPeriodoMovimentacao||''' END AS periodo_final
                        , assentamento_gerado.automatico
                        , assentamento_gerado.cod_assentamento
                        , assentamento_validade.dt_inicial as dt_inicial_validade
                        , CASE WHEN assentamento_validade.dt_final IS NULL THEN ''9999-12-31''
                               ELSE assentamento_validade.dt_final END as dt_final_validade
                        , assentamento.evento_automatico
                        , assentamento.timestamp as timestamp_assentamento
                        , classificacao_assentamento.cod_tipo
                        , assentamento_assentamento.cod_motivo
                     FROM pessoal'||stEntidade||'.assentamento_gerado_contrato_servidor
                        , pessoal'||stEntidade||'.assentamento_gerado
                        , (   SELECT cod_assentamento_gerado
                                   , max(timestamp) as timestamp
                                FROM pessoal'||stEntidade||'.assentamento_gerado
                            GROUP BY cod_assentamento_gerado) AS max_assentamento_gerado
                        , pessoal'||stEntidade||'.assentamento
                        , (   SELECT cod_assentamento
                                   , max(timestamp) as timestamp
                                FROM pessoal'||stEntidade||'.assentamento
                            GROUP BY cod_assentamento) AS max_assentamento
                        , pessoal'||stEntidade||'.assentamento_validade
                        , pessoal'||stEntidade||'.assentamento_assentamento
                        , pessoal'||stEntidade||'.classificacao_assentamento
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
		      AND assentamento.cod_assentamento = '||inCodAssentamento||'
                      AND assentamento_gerado_contrato_servidor.cod_contrato = '||inCodContrato||'
                      AND NOT EXISTS (SELECT *
                                        FROM pessoal'||stEntidade||'.assentamento_gerado_excluido
                                       WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                         AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp) ';
        FOR reAssentamento IN EXECUTE stSql
        LOOP
            --Verifica se o motivo do assentamento Ã© nomeaÃ§Ã£o/posse/admissÃ£o
            --se FOR um desses 3 os eventos inseridos nÃ£o deverÃ£o se automativos
            IF reAssentamento.cod_motivo = 11 OR reAssentamento.cod_motivo = 12 OR reAssentamento.cod_motivo = 13 THEN
                boAutomatico := FALSE;
            END IF;
                
            --Valida se a data inicial ou final de validade do assentamento se encontra dentro do perÃ­odo de movimentacÃ£o
            IF reAssentamento.dt_inicial_validade >= dtInicialPeriodoMovimentacao AND reAssentamento.dt_inicial_validade <= dtFinalPeriodoMovimentacao 
            OR reAssentamento.dt_final_validade   >= dtInicialPeriodoMovimentacao AND reAssentamento.dt_final_validade   <= dtFinalPeriodoMovimentacao 
            OR reAssentamento.dt_inicial_validade <= dtInicialPeriodoMovimentacao AND reAssentamento.dt_final_validade   >= dtFinalPeriodoMovimentacao
            THEN   
                
                --Para contratos que possuem assentamento com motivo igual a admissÃ£o/posse/nomeaÃ§Ã£o
                IF (reAssentamento.cod_motivo = 11 OR reAssentamento.cod_motivo = 12 OR reAssentamento.cod_motivo = 13) AND to_char(reAssentamento.periodo_inicial,'mm/yyyy')  = to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') THEN
				--(reAssentamento.periodo_inicial > dtInicialPeriodoMovimentacao) THEN
                    --reAssentamento.periodo_inicial := dtInicialPeriodoMovimentacao;
					reAssentamento.periodo_final   := dtFinalPeriodoMovimentacao;
                END IF;
                IF reAssentamento.periodo_final IS NULL THEN
                    reAssentamento.periodo_final := dtFinalPeriodoMovimentacao;
                END IF;             
                
				--##### Calculo da quantidade de dias Afastados do assentamento
                IF reAssentamento.periodo_inicial = reAssentamento.periodo_final THEN
                    inQuantDiasAfastamento := 1;
					boAcumulaDiasAfastamento := TRUE;
                ELSE                
                    --######PerÃ­odo inicial dentro da competÃªncia
                    IF to_char(reAssentamento.periodo_inicial,'mm/yyyy')  = to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') AND
                    to_char(reAssentamento.periodo_final,'mm/yyyy')   != to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') 
                    THEN
                        inQuantDiasAfastamento := (inQuantDiasMes - to_char(reAssentamento.periodo_inicial,'dd')::integer)+1;
                        boAcumulaDiasAfastamento := TRUE;
                    END IF;
    
                    --######PerÃ­odo final dentro da competÃªncia
                    IF to_char(reAssentamento.periodo_inicial,'mm/yyyy') != to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') AND
                    to_char(reAssentamento.periodo_final,'mm/yyyy')    = to_char(dtFinalPeriodoMovimentacao,'mm/yyyy')
                    THEN
                        inQuantDiasAfastamento := to_char(reAssentamento.periodo_final,'dd');					
					    inQuantDiasMes := 30;
                        boAcumulaDiasAfastamento := TRUE;
                    END IF;
                    --######
                    --######PerÃ­odo integralmente dentro da competÃªncia        
                    IF to_char(reAssentamento.periodo_inicial,'yyyy-mm') = to_char(dtFinalPeriodoMovimentacao,'yyyy-mm') AND
                    to_char(reAssentamento.periodo_final,'yyyy-mm')   = to_char(dtFinalPeriodoMovimentacao,'yyyy-mm')
                    THEN               
                        inQuantDiasAfastamento := (to_char(reAssentamento.periodo_final,'dd')::integer-to_char(reAssentamento.periodo_inicial,'dd')::integer)+1;
                        boAcumulaDiasAfastamento := TRUE;
                    END IF;                
                    --######
                    --######PerÃ­odo inicÃ­a antes da competÃªncia e termina depois da competÃªncia
                    IF to_char(reAssentamento.periodo_inicial,'yyyy-mm') < to_char(dtFinalPeriodoMovimentacao,'yyyy-mm') AND
                    to_char(reAssentamento.periodo_final,'yyyy-mm')   > to_char(dtFinalPeriodoMovimentacao,'yyyy-mm')
                    THEN
                    inQuantDiasAfastamento := inQuantDiasMes;
                    boAcumulaDiasAfastamento := TRUE;
                    END IF;
                    --######
                    --######PerÃ­odo inicia/termina antes ou depois da competÃªncia , ou seja, fora do intervalo nao tem afastamento. 
                    IF (to_char(reAssentamento.periodo_final::date,'yyyy-mm')   < to_char(dtInicialPeriodoMovimentacao::date,'yyyy-mm')  OR
					    to_char(reAssentamento.periodo_inicial::date,'yyyy-mm') > to_char(dtFinalPeriodoMovimentacao,'yyyy-mm')) THEN
						inQuantDiasAfastamento := 0;
						boAssentamentoForadaCompetencia := TRUE;
                    END IF;
                    --######					
                END IF;
                --####
                --Quando a quantidade de dias de afastamento FOR de 31 dias gerado por um perÃ­odo de 01 a 31 do mÃªs corrente,
                --deverÃ¡ ser substituÃ­do a quantidade de dias afastado por 30 porque a quantidade nÃ£o pode ser superior a 30
                --No caso da quantidade de dias afastado FOR 28 ou 29 e o mÃªs FOR fevereiro tambÃ©m deverÃ¡ ser substituido por 
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

				--CondiÃ§Ã£o para Acumular em inQuantDiasAfastamentoTotalAfastamentoTemporario as  inQuantDiasAfastamento quando for 
				--Assentamentos de Afastamento e estes estiverem dentro do perÃ­odo de movimentacao.
                IF (reAssentamento.cod_tipo = 2 OR reAssentamento.cod_tipo = 1) AND boAcumulaDiasAfastamento IS TRUE AND boAssentamentoForadaCompetencia IS FALSE
				THEN	
                   inQuantDiasAfastamentoTotalAfastamentoTemporario := inQuantDiasAfastamentoTotalAfastamentoTemporario  + inQuantDiasAfastamento; 
                END IF;

                --INÃCIO EVENTOS AUTOMÃTICOS--
                IF reAssentamento.evento_automatico IS TRUE THEN
                    --Consulta que busca os eventos automÃ¡ticos do assentamento
                    stSql := 'SELECT evento.*                               
                                    , evento_evento.valor_quantidade
                                    , evento_evento.unidade_quantitativa 
                                 FROM pessoal'||stEntidade||'.assentamento_evento
                                    , folhapagamento'||stEntidade||'.evento
                                    , folhapagamento'||stEntidade||'.evento_evento
                                    , (  SELECT cod_evento
                                              , max(timestamp) as timestamp
                                          FROM folhapagamento'||stEntidade||'.evento_evento
                                       GROUP BY cod_evento) as max_evento_evento
                                WHERE assentamento_evento.cod_assentamento = '||reAssentamento.cod_assentamento||'
                                  AND assentamento_evento.timestamp = '''||reAssentamento.timestamp_assentamento||'''
                                  AND assentamento_evento.cod_evento = evento.cod_evento
                                  AND evento.cod_evento = evento_evento.cod_evento
                                  AND evento_evento.cod_evento = max_evento_evento.cod_evento
                                  AND evento_evento.timestamp = max_evento_evento.timestamp';                    
                    FOR reEvento IN EXECUTE stSql
                    LOOP
                        IF reEvento.tipo = 'F' THEN
                            IF stAcao = 'incluir' THEN
							   boRetorno := excluirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,'');
                               IF reEvento.valor_quantidade > 0.00 AND boAssentamentoForadaCompetencia IS FALSE THEN       					      							   
                                  boRetorno := inserirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,reEvento.valor_quantidade,reEvento.fixado,'',boAutomatico);
                               END IF;
                            ELSE
							    IF boAssentamentoForadaCompetencia IS FALSE THEN
                                   boRetorno := excluirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,''); 								  
								END IF;
                            END IF;
                        ELSE
                            IF reEvento.unidade_quantitativa = 0 THEN
                                nuQuantidadeValor := (reEvento.valor_quantidade / 30) * inQuantDiasAfastamentoTotalAfastamentoTemporario;
								--nuQuantidadeValor := (reEvento.valor_quantidade / 30) * inQuantDiasAfastamento;
                            ELSE
                                --nuQuantidadeValor := (reEvento.valor_quantidade / reEvento.unidade_quantitativa) * ((reEvento.unidade_quantitativa * inQuantDiasAfastamento) / 30);
                                nuQuantidadeValor := (reEvento.valor_quantidade / reEvento.unidade_quantitativa) * ((reEvento.unidade_quantitativa * inQuantDiasAfastamentoTotalAfastamentoTemporario) / 30);
							END IF;
                            --CondiÃ§Ã£o para Assentamentos/Vantagem
                            IF reAssentamento.cod_tipo = 4 THEN
                                inQuantDiasAfastamentoTotalVantagem := inQuantDiasAfastamentoTotalVantagem + inQuantDiasAfastamento;
                            END IF;      
                            IF stAcao = 'incluir' THEN
							    boRetorno := excluirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,'');	
                                IF nuQuantidadeValor > 0.00 AND boAssentamentoForadaCompetencia IS FALSE THEN							
                                   boRetorno := inserirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,nuQuantidadeValor,reEvento.fixado,'',boAutomatico);
                                END IF;
                            ELSE
							    IF boAssentamentoForadaCompetencia IS FALSE THEN
								   boRetorno := excluirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,'');
								END IF;								
                            END IF;
                        END IF;
                    END LOOP;
                END IF;
                --FIM EVENTOS AUTOMÃTICOS--
                
                --INÃCIO EVENTOS AUTOMÃTICOS A SEREM PROPORCIONALIDADOS--
                --Consulta que busca os eventos proporcionais automÃ¡ticos do assentamento
                IF inQuantDiasAfastamentoTotalAfastamentoTemporario >= 0 THEN
                    stSql := 'SELECT evento.*
                                    , evento_evento.valor_quantidade
                                    , evento_evento.unidade_quantitativa
                                FROM pessoal'||stEntidade||'.assentamento_evento_proporcional
                                    , folhapagamento'||stEntidade||'.evento
                                    , folhapagamento'||stEntidade||'.evento_evento
                                    , (  SELECT cod_evento
                                            , max(timestamp) as timestamp
                                        FROM folhapagamento'||stEntidade||'.evento_evento
                                    GROUP BY cod_evento) as max_evento_evento
                                WHERE assentamento_evento_proporcional.cod_assentamento = '||reAssentamento.cod_assentamento||'
                                AND assentamento_evento_proporcional.timestamp = '''||reAssentamento.timestamp_assentamento||'''
                                AND assentamento_evento_proporcional.cod_evento = evento.cod_evento
                                AND evento.cod_evento = evento_evento.cod_evento
                                AND evento_evento.cod_evento = max_evento_evento.cod_evento
                                AND evento_evento.timestamp = max_evento_evento.timestamp';
                    --CondiÃ§Ã£o para Assentamentos de Afastamento TemporÃ¡rio
               
                    FOR reEvento IN EXECUTE stSql
                    LOOP
                        --Consulta que verifica se o evento Ã© um evento do tipo fixo e se estÃ¡ registrado
                        --O evento sÃ³ serÃ¡ registrado como proporcional se o mesmo se encontra registrado como fixo
                        --caso contrÃ¡rio nÃ£o faz nada
                        nuValorQuantidadeRegistro := selectIntoNumeric('SELECT CASE evento.fixado 
                                                                    WHEN ''V'' THEN registro_evento.valor
                                                                    WHEN ''Q'' THEN registro_evento.quantidade 
                                                                    END as valor_quantidade
                                                                FROM folhapagamento'||stEntidade||'.registro_evento
                                                                    , folhapagamento'||stEntidade||'.ultimo_registro_evento
                                                                    , folhapagamento'||stEntidade||'.registro_evento_periodo
                                                                    , folhapagamento'||stEntidade||'.evento
                                                                WHERE registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                                                                AND registro_evento.cod_evento = ultimo_registro_evento.cod_evento
                                                                AND registro_evento.timestamp = ultimo_registro_evento.timestamp                    
                                                                AND registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                                                AND registro_evento.cod_evento = evento.cod_evento
                                                                AND registro_evento.cod_evento = '||reEvento.cod_evento||'
                                                                AND registro_evento.proporcional IS FALSE
                                                                AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                                                                AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                                                AND evento.tipo = ''F'' ');
                        IF nuValorQuantidadeRegistro IS NOT NULL THEN
                            IF to_char(dtFinalPeriodoMovimentacao,'mm') = '02' THEN
                            inQuantDiasMes := 30;  
                            END IF;
                            IF reEvento.valor_quantidade = 0 THEN
                                nuValorQuantidade := nuValorQuantidadeRegistro;
                            ELSE
                                nuValorQuantidade := reEvento.valor_quantidade;
                            END IF;
                            --ClassificaÃ§Ã£o do tipo Afastamento TemporÃ¡rio
                            --ClassificaÃ§Ã£o do tipo Assentamento
                            IF (reAssentamento.cod_tipo = 2 OR reAssentamento.cod_tipo = 1) AND (reAssentamento.cod_motivo != 11 AND reAssentamento.cod_motivo != 12 AND reAssentamento.cod_motivo != 13) THEN
                                IF reEvento.unidade_quantitativa = 0 THEN
                                    nuQuantidadeValor := (nuValorQuantidade / 30) * (inQuantDiasMes - inQuantDiasAfastamentoTotalAfastamentoTemporario);
                                ELSE   
                                    nuQuantidadeValor := (nuValorQuantidade / reEvento.unidade_quantitativa) * ((reEvento.unidade_quantitativa * (inQuantDiasMes - inQuantDiasAfastamentoTotalAfastamentoTemporario)))/30;
                                END IF;
                                --reEvento.unidade_quantitativa/30 igual a uma unidade de medida com relaÃ§Ã£o aos 30 dias do mes
                                --Se a quantidade de dias de afastamento multiplidado pela unidade de medida mais
                                --a quantidadeValor encontrada no calculo FOR maior que a unidade quantitativa deve ser
                                --retirado uma unidade de medida do nuQuantidadeValor, com isso tornando o valor correto.
                                IF (((reEvento.unidade_quantitativa/30)*inQuantDiasAfastamentoTotalAfastamentoTemporario)+nuQuantidadeValor) > reEvento.unidade_quantitativa THEN
                                    nuQuantidadeValor := nuQuantidadeValor - (reEvento.unidade_quantitativa/30);
                                END IF;
                            END IF;
							
							--Proporcionalidade dos eventos para assentamentos de cod_motivo Admissao/Posse/Nomeacao
							IF (reAssentamento.cod_motivo = 11 OR reAssentamento.cod_motivo = 12 OR reAssentamento.cod_motivo = 13) AND to_char(reAssentamento.periodo_inicial,'mm/yyyy')  = to_char(dtFinalPeriodoMovimentacao,'mm/yyyy') THEN
                                IF reEvento.unidade_quantitativa = 0 THEN
                                    nuQuantidadeValor := (nuValorQuantidade / 30) * (inQuantDiasAfastamentoTotalAfastamentoTemporario);
                                ELSE   
                                    nuQuantidadeValor := (nuValorQuantidade / reEvento.unidade_quantitativa) * ((reEvento.unidade_quantitativa * (inQuantDiasAfastamentoTotalAfastamentoTemporario)))/30;
                                END IF;
                            END IF;
							
                            --ClassificaÃ§Ã£o do tipo Vantagem
                            IF reAssentamento.cod_tipo = 4 THEN
                                IF reEvento.unidade_quantitativa = 0 THEN
                                    nuQuantidadeValor := (nuValorQuantidade / 30) * (inQuantDiasMes - inQuantDiasAfastamentoTotalVantagem);
                                ELSE
                                    nuQuantidadeValor := (nuValorQuantidade / reEvento.unidade_quantitativa) * ((reEvento.unidade_quantitativa * (inQuantDiasMes - inQuantDiasAfastamentoTotalVantagem)))/30;
                                END IF;
                            END IF;
                            IF stAcao = 'incluir' THEN
  							    boRetorno := excluirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,'P');	
                                IF nuQuantidadeValor >= 0 AND inQuantDiasAfastamento > 0 AND boAssentamentoForadaCompetencia IS FALSE THEN									   
                                   boRetorno := inserirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,nuQuantidadeValor,reEvento.fixado,'P',boAutomatico);
								END IF;
                            ELSE
                                IF boAssentamentoForadaCompetencia IS FALSE THEN
								   boRetorno := excluirRegistroEventoAutomatico(inCodContrato,inCodPeriodoMovimentacao,reEvento.cod_evento,'P');							      
								END IF;											
                            END IF;
                        END IF;
                    END LOOP;            
                END IF;
                --FIM EVENTOS AUTOMÃTICOS PROPORCIONAIS--
            END IF; 
            boAcumulaDiasAfastamento := FALSE;
			boAssentamentoForadaCompetencia := FALSE;
        END LOOP;
			--ExclusÃ£o da informaÃ§Ã£o de utilizaÃ§Ã£o da deduÃ§Ã£o de dependente do salÃ¡rio
			inCodServidor := pega0ServidorDoContrato(inCodContrato);
			inNumCgm := pega0NumcgmServidor(inCodServidor);
			stSql := 'DELETE FROM folhapagamento'||stEntidade||'.deducao_dependente
						WHERE numcgm = '||inNumCgm||'
						  AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
						  AND cod_tipo = 2';
			EXECUTE stSql;
    END IF;
    RETURN true;
END;
$$ LANGUAGE 'plpgsql'; 


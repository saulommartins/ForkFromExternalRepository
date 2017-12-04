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
/* recuperaQuantidadeMesesProgressaoAfastamento
 * 
 * Data de Criação : 28/08/2013


 * @author Analista : Eduardo Schitz
 * @author Desenvolvedor : Franver Sarmento de Moraes
 

 */

CREATE OR REPLACE FUNCTION recuperaQuantidadeMesesProgressaoAfastamento(varchar, integer, date, date) RETURNS integer as $$
DECLARE
    stEntidade          ALIAS FOR $1;
    inCodContrato       ALIAS FOR $2;
    dtPeriodoInicial    ALIAS FOR $3;
    dtPeriodoFinal      ALIAS FOR $4;
    inQtdTotalMeses     INTEGER :=0;
    inQtdMesPorOperador INTEGER :=0;
    inAno               INTEGER;
    inMes               INTEGER;
    inAnoFim            INTEGER;
    inMesFim            INTEGER;
    stSql               VARCHAR;
    inIndex             INTEGER;
    reRegistro          RECORD;
BEGIN
    -- Faz a soma total de meses entre a data progreção até a data final de movimentação;
    IF to_char(dtPeriodoFinal,'yyyy') >= to_char(dtPeriodoInicial,'yyyy') THEN
        inQtdTotalMeses := ((to_char(dtPeriodoFinal,'mm')::INTEGER - to_char(dtPeriodoInicial,'mm')::INTEGER)+1) + ((to_char(dtPeriodoFinal,'yyyy')::INTEGER - to_char(dtPeriodoInicial,'yyyy')::INTEGER) * 12);
    ELSIF to_char(dtPeriodoFinal,'mm') >= to_char(dtPeriodoInicial,'mm') THEN
        inQtdTotalMeses := ((to_char(dtPeriodoFinal,'mm')::INTEGER - to_char(dtPeriodoInicial,'mm')::INTEGER)+1);
    END IF;

    -- Separa as datas de inicio e fim.
    inAno := to_char(dtPeriodoInicial,'yyyy');
    inMes := to_char(dtPeriodoInicial,'mm');
    inAnoFim := to_char(dtPeriodoFinal,'yyyy');
    inMesFim := to_char(dtPeriodoFinal,'mm');
    
    WHILE inAno <= inAnoFim LOOP 
        FOR inIndex IN 1 .. 12 LOOP
        
            -- Consulta para verificar se o codigo de contrato possui Afastamento durante o periodo solicitado;
            stSql := 'SELECT assentamento_gerado.cod_assentamento
                            , assentamento_gerado.periodo_inicial
                            , assentamento_gerado.periodo_final
                            , assentamento_assentamento.cod_operador
                         FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                            , pessoal'|| stEntidade ||'.assentamento_gerado
                            , pessoal'|| stEntidade ||'.assentamento_assentamento
                            , pessoal'|| stEntidade ||'.assentamento
                            , (SELECT cod_assentamento
                                    , max(timestamp) as timestamp
                                 FROM pessoal'|| stEntidade ||'.assentamento
                                GROUP BY cod_assentamento
                               ) AS max_assentamento
                            , ( SELECT cod_assentamento_gerado, cod_assentamento
                                     , max(timestamp) as timestamp
                                  FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                 GROUP BY cod_assentamento_gerado ,cod_assentamento
                              ) AS max_assentamento_gerado
                        WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                          AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                          AND assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento
                          AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                          AND assentamento.timestamp = max_assentamento.timestamp
                          AND (to_char(assentamento_gerado.periodo_inicial,''yyyy-mm'') <= '|| quote_literal(to_char(to_date(inAno ||'-'|| inMes,'yyyy-mm'),'yyyy-mm')) ||' 
                           AND  to_char(assentamento_gerado.periodo_final,''yyyy-mm'')   >= '|| quote_literal(to_char(to_date(inAno ||'-'|| inMes,'yyyy-mm'),'yyyy-mm')) ||')
                          AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                          AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                          AND assentamento_gerado.cod_assentamento = max_assentamento_gerado.cod_assentamento
                          AND assentamento_gerado_contrato_servidor.cod_contrato = '|| inCodContrato ||'
                          AND cod_motivo = 3
                          AND assentamento_gerado.cod_assentamento_gerado NOT IN (SELECT cod_assentamento_gerado                                                                                      
                                                                                    FROM pessoal.assentamento_gerado_excluido
                                                                                   WHERE timestamp <= to_date('|| quote_literal(dtPeriodoFinal) ||',''yyyy-mm-dd'')
                                                                                 )';
            
            -- Verifica todos os afastamentos registrado pelo codigo de contrato.
            FOR reRegistro IN EXECUTE stSql LOOP
                IF inMes <= 12 THEN
                    CASE reRegistro.cod_operador
                        -- Soma os numeros de meses na Quatidade total de meses.
                        WHEN 1 THEN
                            inQtdTotalMeses := inQtdTotalMeses + 1;
                        -- Diminui os numeros de meses na Quatidade total de meses.
                        WHEN 2 THEN
                            inQtdTotalMeses := inQtdTotalMeses - 1;
				        ELSE 
						    inQtdTotalMeses := inQtdTotalMeses;
							
                    END CASE;
                END IF;
            END LOOP;
            
            IF inAno = inAnoFim AND inMes = inMesFim THEN
	       exit;
	    ELSE
	       inMes := inMes + 1;
	    END IF; 
        END LOOP;
        inAno := inAno + 1;
        inMes := 1;
    END LOOP;
    
    IF inQtdTotalMeses < 0 THEN
       inQtdTotalMeses := 0;
    END IF;
    
    RETURN inQtdTotalMeses;
END;
$$ language 'plpgsql';
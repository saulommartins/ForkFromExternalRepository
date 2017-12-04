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

DROP FUNCTION patrimonio.fn_depreciacao_acumulada(INTEGER);

CREATE OR REPLACE FUNCTION patrimonio.fn_depreciacao_acumulada(inCodBem INTEGER) RETURNS SETOF RECORD
AS $$
DECLARE
    stQuery                 VARCHAR;
    reRegistro              RECORD;
    nuValorDepreciado       NUMERIC (14,2);
BEGIN

    SELECT * INTO reRegistro FROM patrimonio.reavaliacao WHERE cod_bem = inCodBem;
    IF FOUND THEN
       stQuery := '   SELECT bem.cod_bem
                           , (COALESCE(SUM(reavaliacao.valor_depreciado),0))::NUMERIC(14,2) AS vl_acumulado
                           , (COALESCE(reavaliacao.vl_reavaliacao, bem.vl_bem) - (COALESCE(ABS(SUM(reavaliacao.valor_depreciado)),0)))::NUMERIC(14,2) AS vl_atualizado
                           , COALESCE(reavaliacao.vl_reavaliacao, bem.vl_bem)::NUMERIC(14,2) AS vl_bem
                           , min_competencia::VARCHAR 
                           , max_competencia::VARCHAR
                        FROM patrimonio.bem 

                  INNER JOIN (
                  SELECT bem.cod_bem
				       , CASE WHEN depreciado.situacao = ''anulada''
					      THEN 0
					      ELSE depreciado.vl_depreciado
					  END AS valor_depreciado
				       , reavaliacao.vl_reavaliacao
				       , competencia_depreciado.min_competencia
				       , competencia_depreciado.max_competencia
				   FROM patrimonio.bem

			    -- Recupera o valor do bem, para calcular vl_acumumlado e quota das proximas inserções.
                -- Quando o bem possuir reavaliação, é desconsiderado as deprecaiações antigas e feitas novas a partir so novo valor.
	            LEFT JOIN (
					   SELECT depreciacao.cod_bem
						    , depreciacao.cod_depreciacao
						    , depreciacao.timestamp
						    , depreciacao.competencia
                            , CASE WHEN (SELECT COUNT(cod_depreciacao) FROM patrimonio.depreciacao_reavaliacao WHERE cod_bem = '||inCodBem||') = 0
						           THEN 0.00
						           ELSE depreciacao.vl_depreciado
						      END vl_depreciado
						    , CASE
							WHEN depreciacao_anulada.cod_bem         IS NOT NULL
							 AND depreciacao_anulada.cod_depreciacao IS NOT NULL
							 AND depreciacao_anulada.timestamp       IS NOT NULL THEN
								''anulada''
							ELSE
								''depreciado''
						     END AS situacao
                                                      
						FROM patrimonio.depreciacao
						
				   LEFT JOIN patrimonio.depreciacao_anulada
						  ON depreciacao_anulada.cod_bem         = depreciacao.cod_bem
						 AND depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
						 AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                            
                  INNER JOIN patrimonio.depreciacao_reavaliacao
					 	  ON depreciacao.cod_depreciacao = depreciacao_reavaliacao.cod_depreciacao 
					     AND depreciacao.cod_bem         = depreciacao_reavaliacao.cod_bem  
					     AND depreciacao.timestamp       = depreciacao_reavaliacao.timestamp
				 
			                ) AS depreciado
		                  ON depreciado.cod_bem = bem.cod_bem
                            
                       -- Recupera as competências minima e máxima reavaliadas e que ainda não foram anuladas.
		          LEFT JOIN (        
		               SELECT depreciacao.cod_bem
					        , MIN(depreciacao.competencia) AS min_competencia
					        , MAX(depreciacao.competencia) AS max_competencia
					     FROM patrimonio.depreciacao
                                        
                                        INNER JOIN patrimonio.depreciacao_reavaliacao
						ON depreciacao.cod_depreciacao = depreciacao_reavaliacao.cod_depreciacao 
					       AND depreciacao.cod_bem         = depreciacao_reavaliacao.cod_bem  
					       AND depreciacao.timestamp       = depreciacao_reavaliacao.timestamp
	 
				        LEFT JOIN patrimonio.depreciacao_anulada
					       ON depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
					      AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem         
					      AND depreciacao_anulada.timestamp       = depreciacao.timestamp  
	 
					    WHERE depreciacao_anulada.cod_bem         IS NULL
					      AND depreciacao_anulada.cod_depreciacao IS NULL
					      AND depreciacao_anulada.timestamp       IS NULL

                          AND depreciacao_reavaliacao.cod_reavaliacao = (SELECT MAX(cod_reavaliacao)
                                                                           FROM patrimonio.reavaliacao
                                                                          WHERE cod_bem = '||inCodBem||' )
	 
				         GROUP BY depreciacao.cod_bem
                                         
			               ) AS competencia_depreciado
		                     ON competencia_depreciado.cod_bem = bem.cod_bem
                             
                     -- Recupera relação com a última reavaliação do bem
		             INNER JOIN (
                                SELECT MAX(cod_reavaliacao) AS cod_reavaliacao
                                     , cod_bem
                                 FROM patrimonio.reavaliacao
                                WHERE cod_bem = '||inCodBem||'
                             GROUP BY cod_bem
                             
                            ) AS max_reavaliacao
                         ON max_reavaliacao.cod_bem = bem.cod_bem

			     INNER JOIN patrimonio.reavaliacao
				     ON max_reavaliacao.cod_reavaliacao = reavaliacao.cod_reavaliacao
				    AND max_reavaliacao.cod_bem         = reavaliacao.cod_bem

			      LEFT JOIN patrimonio.depreciacao_reavaliacao
				     ON reavaliacao.cod_reavaliacao = depreciacao_reavaliacao.cod_reavaliacao
				    AND reavaliacao.cod_bem         = depreciacao_reavaliacao.cod_bem
				    AND depreciado.cod_bem          = depreciacao_reavaliacao.cod_bem
				    AND depreciado.timestamp        = depreciacao_reavaliacao.timestamp
				    AND depreciado.cod_depreciacao  = depreciacao_reavaliacao.cod_depreciacao

			          WHERE bem.cod_bem = '||inCodBem||'
  
                            )AS reavaliacao
                          ON reavaliacao.cod_bem = bem.cod_bem
                    
                    GROUP BY bem.cod_bem
                           , bem.vl_bem
                           , bem.vl_depreciacao
                           , reavaliacao.vl_reavaliacao
                           , min_competencia
                           , max_competencia ';                           
    ELSE
        stQuery:= 'SELECT bem.cod_bem
                        , (COALESCE(SUM(depreciacao.valor_depreciado),0) + COALESCE(bem.vl_depreciacao,0))::NUMERIC(14,2) AS vl_acumulado
                        , (bem.vl_bem - (COALESCE(ABS(SUM(depreciacao.valor_depreciado)),0) + COALESCE(bem.vl_depreciacao,0)))::NUMERIC(14,2) AS vl_atualizado
                        , bem.vl_bem
                        , min_competencia::VARCHAR
                        , max_competencia::VARCHAR
                     FROM patrimonio.bem
                     
               INNER JOIN (
                                 SELECT bem.cod_bem
                                      , CASE WHEN depreciado.situacao = ''anulada''
                                             THEN 0
                                             ELSE depreciado.vl_depreciado
                                        END AS valor_depreciado
                                      , competencia_depreciado.min_competencia
                                      , competencia_depreciado.max_competencia
                                   FROM patrimonio.bem
                              
                              -- Recupera o valor do bem.
                              LEFT JOIN (
                                               SELECT depreciacao.cod_bem
                                                    , depreciacao.cod_depreciacao
                                                    , depreciacao.timestamp
                                                    , depreciacao.competencia
                                                    , depreciacao.vl_depreciado
                                                    , CASE
                                                        WHEN depreciacao_anulada.cod_bem         IS NOT NULL
                                                         AND depreciacao_anulada.cod_depreciacao IS NOT NULL
                                                         AND depreciacao_anulada.timestamp       IS NOT NULL THEN
                                                                ''anulada''
                                                        ELSE
                                                                ''depreciado''
                                                      END AS situacao
                                                FROM patrimonio.depreciacao
                                                
                                           LEFT JOIN patrimonio.depreciacao_anulada
                                                  ON depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                                 AND depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                                 AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                                 
                                         ) AS depreciado
                                     ON depreciado.cod_bem = bem.cod_bem
                              
                              -- Recupera as competências minima e máxima que ainda não foram anuladas.
                              LEFT JOIN (
                                                SELECT depreciacao.cod_bem
                                                      , MIN(depreciacao.competencia) AS min_competencia
                                                      , MAX(depreciacao.competencia) AS max_competencia
                                                  FROM patrimonio.depreciacao
                 
                                             LEFT JOIN patrimonio.depreciacao_anulada
                                                    ON depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                                   AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem         
                                                   AND depreciacao_anulada.timestamp       = depreciacao.timestamp  
                 
                                                 WHERE depreciacao_anulada.cod_bem         IS NULL
                                                   AND depreciacao_anulada.cod_depreciacao IS NULL
                                                   AND depreciacao_anulada.timestamp       IS NULL
                 
                                              GROUP BY depreciacao.cod_bem
                                             
                                        ) AS competencia_depreciado
                                     ON competencia_depreciado.cod_bem = bem.cod_bem
                                        
                                  WHERE bem.cod_bem = '||inCodBem||'

                          ) AS depreciacao
                         ON depreciacao.cod_bem = bem.cod_bem       
           
                   GROUP BY bem.cod_bem
                          , bem.vl_bem
                          , bem.vl_depreciacao
                          , min_competencia
                          , max_competencia';        
    END IF;

    FOR reRegistro IN EXECUTE stQuery LOOP
        RETURN NEXT reRegistro;
    END LOOP;
RETURN;
END;
$$ LANGUAGE 'plpgsql';
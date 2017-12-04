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
/*
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
*/


CREATE OR REPLACE FUNCTION divida.fn_acrescimo_divida_individual(stExercicio VARCHAR, inCodInscricao INTEGER) RETURNS BOOLEAN AS $$
DECLARE
	recDivida	RECORD;
	recModalidade	RECORD;
	st_valor_acrescimo VARCHAR;
	valor_acrescimo	VARCHAR;
    inContLoop      INTEGER;
BEGIN

	FOR recDivida IN SELECT b.cod_inscricao, 
				b.exercicio, 
				cod_modalidade, 
				dt_vencimento_origem, 
				dt_inscricao, 
				a.num_parcelamento, 
				sum(d.valor) AS valor
                                
			   FROM divida.parcelamento 	   AS a, 
				divida.divida_ativa 	   AS b, 
				divida.divida_parcelamento AS c, 
				divida.parcela_origem      AS d
                                
			  WHERE
				a.exercicio          = '-1' AND 
				a.num_parcelamento   = c.num_parcelamento AND 
				c.cod_inscricao      = b.cod_inscricao AND 
				c.exercicio          = b.exercicio AND 
				a.num_parcelamento   = d.num_parcelamento 
				AND b.cod_inscricao  = inCodInscricao
				AND b.exercicio	     = stExercicio
                                
		       GROUP BY b.cod_inscricao, b.exercicio, cod_modalidade, dt_vencimento_origem, dt_inscricao, a.num_parcelamento
	LOOP
        
	    FOR recModalidade IN SELECT cod_tipo, cod_acrescimo 
				   FROM divida.modalidade as a, divida.modalidade_acrescimo as b 
				  WHERE a.cod_modalidade     = recDivida.cod_modalidade AND 
                                        a.cod_modalidade   = b.cod_modalidade AND 
                                        a.ultimo_timestamp = b.timestamp AND 
                                        b.pagamento        = false
	    LOOP
            
		SELECT 	aplica_acrescimo_modalidade(0,
                                                    recDivida.cod_inscricao,
                                                    recDivida.exercicio::integer,
                                                    recDivida.cod_modalidade,
                                                    recModalidade.cod_tipo,
                                                    recDivida.cod_inscricao,
                                                    recDivida.valor,
                                                    recDivida.dt_vencimento_origem,
                                                    recDivida.dt_inscricao,
                                                    'false'::text
                                                    ) INTO st_valor_acrescimo;
                inContLoop:=2;
                
                valor_acrescimo:=SPLIT_PART(st_valor_acrescimo,';',inContLoop);
                
                LOOP
                
                    IF valor_acrescimo <> '' THEN
                        IF recModalidade.cod_acrescimo = (COALESCE(SPLIT_PART(st_valor_acrescimo,';',inContLoop+1),'0'))::integer THEN
                            INSERT INTO divida.divida_acrescimo( cod_inscricao,
                                                                 exercicio,
                                                                 cod_acrescimo,
                                                                 cod_tipo, valor
                                                                )
                                                         VALUES( recDivida.cod_inscricao,
                                                                 recDivida.exercicio,
                                                                 recModalidade.cod_acrescimo,
                                                                 recModalidade.cod_tipo,
                                                                 valor_acrescimo::NUMERIC
                                                                );
                        END IF;
                        
                        inContLoop:=inContLoop+3;
                        valor_acrescimo:=SPLIT_PART(st_valor_acrescimo,';',inContLoop);
                    END IF;
                    
                    EXIT WHEN valor_acrescimo = '';
                END LOOP;
	    END LOOP;
	END LOOP;
        
    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';

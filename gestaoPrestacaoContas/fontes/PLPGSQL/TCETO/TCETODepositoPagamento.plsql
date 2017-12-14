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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 58434 $
* $Name$
* $Author: $
* $Date: $
* $Id: TCETODepositoPagamento.plsql 60815 2014-11-17 18:07:36Z evandro $
*
*/

CREATE OR REPLACE FUNCTION tceto.fn_deposito_pagamento(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF
RECORD AS $$

DECLARE
    stExercicio         ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    stFiltro            ALIAS FOR $5;

    stSql               VARCHAR := '';
    nuTotalLiquidacao   NUMERIC := 0.00;
    reRegistro          RECORD;

BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_item AS
                SELECT
                         li.cod_nota,
                         li.num_item,
                         li.cod_entidade,
                         li.vl_total
                    FROM
                        empenho.pagamento_liquidacao    as pg,
                        empenho.nota_liquidacao         as nl,
                        empenho.nota_liquidacao_item    as li,
                        empenho.pagamento_liquidacao_nota_liquidacao_paga as nl_pg
                        
                    WHERE   nl.exercicio        =  pg.exercicio_liquidacao
                    AND     nl.cod_entidade     =  pg.cod_entidade
                    AND     nl.cod_nota         =  pg.cod_nota 
                    AND     li.exercicio        =  nl.exercicio
                    AND     li.cod_nota         =  nl.cod_nota
                    AND     li.cod_entidade     =  nl.cod_entidade

                    AND     nl_pg.exercicio     =  pg.exercicio
                    AND     nl_pg.cod_entidade  =  pg.cod_entidade
                    AND     nl_pg.cod_ordem     =  pg.cod_ordem
                    AND     nl_pg.exercicio_liquidacao =  pg.exercicio_liquidacao
                    AND     nl_pg.cod_nota      =  pg.cod_nota
                    
                    AND     pg.exercicio        = ' || quote_literal(stExercicio)     || '
                    AND     pg.cod_entidade     IN (' || stCodEntidades                 || ')
                    AND     nl_pg.timestamp::date  between to_date(' || quote_literal(stDtInicial)     || ',''dd/mm/yyyy'') AND
                                                       to_date(' || quote_literal(stDtFinal)       || ',''dd/mm/yyyy'')
                    ' || stFiltro
                    ; 
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_total AS
                SELECT * FROM(                
                    SELECT
                        em.exercicio,
                        nl.exercicio as exercicio_nota,
                        em.cod_empenho,
                        em.cod_entidade,
                        em.dt_empenho,
                        pre_empenho.cgm_beneficiario AS cgm,
                        pre_empenho.cod_pre_empenho,
                        nl.cod_nota,
                        nl.dt_liquidacao as data_liquidacao,
                        pl_nl.timestamp::date AS data_pagamento,                        
                        tceto.fn_exportacao_liquidacao_total_liquidado(nl.exercicio,nl.cod_nota,nl.cod_entidade) as valor_liquidacao,
                        tceto.fn_exportacao_liquidacao_total_pago(nl.exercicio,nl.cod_nota,nl.cod_entidade,'''||stExercicio||''') as valor_pago,
                        cast(''+'' as character(1)) as sinal_valor,
                        CASE WHEN nl.observacao='''' THEN ''.''
                        ELSE nl.observacao
                        END as observacao,
                        cast(2 as integer) as ordem,
                        pl_nl.cod_ordem
                        
                    FROM
                        empenho.empenho as em,
                        empenho.nota_liquidacao as nl,
                        empenho.pre_empenho,
                        empenho.pagamento_liquidacao AS pl,
                        empenho.pagamento_liquidacao_nota_liquidacao_paga as pl_nl
                        
                    WHERE nl.exercicio          < '''||stExercicio||'''
                    AND em.exercicio          = nl.exercicio_empenho
                    AND em.cod_empenho        = nl.cod_empenho
                    AND em.cod_entidade       = nl.cod_entidade
                    AND em.cod_entidade       IN (' || stCodEntidades  || ')
                    AND em.cod_pre_empenho    = pre_empenho.cod_pre_empenho
                    AND em.exercicio          = pre_empenho.exercicio
                    AND pl_nl.timestamp::date between to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')
                    AND pl.exercicio_liquidacao       = nl.exercicio
                    AND pl.cod_nota 		        = nl.cod_nota
                    AND pl.cod_entidade 	        = nl.cod_entidade
                    AND pl_nl.exercicio		= pl.exercicio
                    AND pl_nl.cod_entidade	        = pl.cod_entidade
                    AND pl_nl.cod_ordem		= pl.cod_ordem
                    AND pl_nl.exercicio_liquidacao    = pl.exercicio_liquidacao
                    AND pl_nl.cod_nota		= pl.cod_nota

		            GROUP BY em.exercicio,
                          nl.exercicio,
                          nl.cod_entidade,
                          em.cod_empenho,
                          em.cod_entidade,
                          em.dt_empenho,
                          pre_empenho.cgm_beneficiario,
                          pre_empenho.cod_pre_empenho,
                          nl.cod_nota,
                          nl.dt_liquidacao,
                          pl_nl.timestamp::date,
                          nl.observacao,
                          pl_nl.cod_ordem
                ) AS tbl
                
                WHERE tbl.valor_liquidacao  <>   0.00
                AND tbl.data_pagamento between to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') AND
                                               to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')';
             
    EXECUTE stSql;

    stSql := '  SELECT * from(
                    SELECT
                        em.exercicio,
                        em.cod_empenho,
		                em.cod_entidade,
                        em.dt_empenho,
                        pre_empenho.cgm_beneficiario AS cgm,
                        pre_empenho.cod_pre_empenho,
                        nl.cod_nota,
                        pl_nl.timestamp::date AS data_pagamento,
                        nl.dt_liquidacao as data_liquidacao,
                        sum(nl_p.vl_pago)::numeric  as valor_liquidacao,
                        ''+'' as sinal_valor,
                        CASE WHEN nl.observacao='''' THEN ''.''
                             ELSE TRIM(replace(nl.observacao, E''\r\n'', ''''))::VARCHAR
                        END AS observacao,
                        0 as ordem,
                        em.oid::integer,
                        pl_nl.cod_ordem
                    
                    FROM
                    empenho.empenho as em,
                    empenho.nota_liquidacao as nl,
                    empenho.pre_empenho,
                    empenho.pagamento_liquidacao AS pl,
                    empenho.pagamento_liquidacao_nota_liquidacao_paga as pl_nl,
                    empenho.nota_liquidacao_paga as nl_p
                    
                    WHERE
                    pl_nl.timestamp::date
                    between to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')
		            AND nl.exercicio        	    = ' || quote_literal(stExercicio) || '
                    AND em.exercicio                = nl.exercicio_empenho
                    AND em.cod_empenho              = nl.cod_empenho
                    AND em.cod_entidade             = nl.cod_entidade
		            AND em.cod_entidade    	    IN (' || stCodEntidades  || ')
                    AND em.cod_pre_empenho          = pre_empenho.cod_pre_empenho
                    AND em.exercicio                = pre_empenho.exercicio
                    AND pl.exercicio_liquidacao     = nl.exercicio
                    AND pl.cod_nota 		    = nl.cod_nota
                    AND pl.cod_entidade 	    = nl.cod_entidade
                    AND pl_nl.exercicio		    = pl.exercicio
                    AND pl_nl.cod_entidade	    = pl.cod_entidade
                    AND pl_nl.cod_ordem		    = pl.cod_ordem
                    AND pl_nl.exercicio_liquidacao  = pl.exercicio_liquidacao
                    AND pl_nl.cod_nota		    = pl.cod_nota
                    AND nl_p.cod_entidade	    = pl_nl.cod_entidade
                    AND nl_p.cod_nota	            = pl_nl.cod_nota
                    AND nl_p.exercicio 	            = pl_nl.exercicio_liquidacao
                    AND nl_p.timestamp	            = pl_nl.timestamp

                    GROUP BY em.exercicio,
                       em.cod_empenho,
                       em.cod_entidade,
                       em.dt_empenho,
                       pre_empenho.cgm_beneficiario,
                       pre_empenho.cod_pre_empenho,
                       nl.cod_nota,
                       pl_nl.timestamp::date,
                       nl.dt_liquidacao,
                       nl.observacao,
                       em.oid,
                       pl_nl.cod_ordem
                    
            UNION
                
                SELECT
                    em.exercicio,
                    em.cod_empenho,
                    em.cod_entidade,
                    em.dt_empenho,
                    pre_empenho.cgm_beneficiario AS cgm,
                    pre_empenho.cod_pre_empenho,
                    nl.cod_nota,
                    pl_nl_p.timestamp::date as data_pagamento,
                    to_date(to_char(nl.dt_liquidacao,''dd/mm/yyyy''),''dd/mm/yyyy'') as data_liquidacao,
                    sum(nl_p_a.vl_anulado) as valor_liquidacao,
                    ''-'' as sinal_valor,
                    CASE WHEN nl.observacao='''' THEN ''.''
                    ELSE TRIM(replace(nl.observacao, E''\r\n'', ''''))::VARCHAR
                    END as observacao,
                    1 as ordem,
                    la.oid::integer,
                    pl_nl_p.cod_ordem 
    
                FROM
                    empenho.empenho as em

                JOIN empenho.nota_liquidacao as nl
		             ON em.exercicio                = nl.exercicio_empenho
		            AND em.cod_empenho              = nl.cod_empenho
		            AND em.cod_entidade             = nl.cod_entidade
                JOIN empenho.nota_liquidacao_item as li
		             ON nl.exercicio                = li.exercicio
		            AND nl.cod_nota                 = li.cod_nota
		            AND nl.cod_entidade             = li.cod_entidade
                LEFT JOIN empenho.nota_liquidacao_item_anulado as la
		             ON li.exercicio                = la.exercicio
		            AND li.cod_nota                 = la.cod_nota
		            AND li.cod_entidade             = la.cod_entidade
		            AND li.num_item                 = la.num_item
		            AND li.cod_pre_empenho          = la.cod_pre_empenho
		            AND li.exercicio_item           = la.exercicio_item
                JOIN empenho.pre_empenho
		             ON pre_empenho.cod_pre_empenho = em.cod_pre_empenho
		            AND pre_empenho.exercicio       = em.exercicio
                JOIN empenho.pagamento_liquidacao as pl
		             ON pl.cod_nota                 = nl.cod_nota
		            AND pl.cod_entidade             = nl.cod_entidade
		            AND pl.exercicio_liquidacao     = nl.exercicio
                JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga as pl_nl_p
		             ON pl_nl_p.exercicio           =  pl.exercicio
		            AND pl_nl_p.cod_entidade        =  pl.cod_entidade
		            AND pl_nl_p.cod_ordem           =  pl.cod_ordem
		            AND pl_nl_p.exercicio_liquidacao=  pl.exercicio_liquidacao
		            AND pl_nl_p.cod_nota            =  pl.cod_nota
                JOIN empenho.nota_liquidacao_paga as nl_p
		             ON nl_p.cod_entidade	   = pl_nl_p.cod_entidade
		            AND nl_p.cod_nota	           = pl_nl_p.cod_nota
		            AND nl_p.exercicio	           = pl_nl_p.exercicio_liquidacao
		            AND nl_p.timestamp	           = pl_nl_p.timestamp
                JOIN empenho.nota_liquidacao_paga_anulada AS nl_p_a
		             ON nl_p.cod_entidade	   = nl_p_a.cod_entidade
		            AND nl_p.cod_nota	           = nl_p_a.cod_nota
		            AND nl_p.exercicio	           = nl_p_a.exercicio
		            AND nl_p.timestamp	           = nl_p_a.timestamp
    
                WHERE pl.cod_entidade     IN (' || stCodEntidades  || ') 
                AND pl_nl_p.timestamp::date between to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') and
                                                       to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')
                GROUP BY em.exercicio,
                       em.cod_empenho,
                       em.cod_entidade,
                       em.dt_empenho,
                       pre_empenho.cgm_beneficiario,
                       pre_empenho.cod_pre_empenho,
                       nl.cod_nota,
                       pl_nl_p.timestamp::date,
                       nl.dt_liquidacao,
                       nl.observacao,
                       la.oid,
                       pl_nl_p.cod_ordem 
                  
            UNION
                
                SELECT exercicio
                    ,cod_empenho
                    ,cod_entidade
                    ,dt_empenho
                    ,cgm
                    ,cod_pre_empenho
                    ,min(cod_nota) AS cod_nota
                    ,data_pagamento
                    ,data_liquidacao
                    ,sum(valor_liquidacao) AS valor_liquidacao
                    ,sinal_valor
                    ,max(observacao)
                    ,ordem
                    ,1::integer AS oid
                    ,cod_ordem
                        
                FROM tmp_total as tm
               
                GROUP BY exercicio
                    ,cod_entidade
                    ,cod_empenho
                    ,ordem
                    ,sinal_valor
                    ,exercicio_nota
                    ,dt_empenho
                    ,cgm
                    ,cod_pre_empenho
                    ,data_pagamento
                    ,data_liquidacao
                    ,cod_ordem
                   
                HAVING sum(valor_liquidacao) <> 0.00
             
                ORDER BY exercicio
                    ,cod_entidade
                    ,cod_empenho
            ) AS tabela
             
            ORDER BY tabela.exercicio DESC
                    ,tabela.cod_entidade
                    ,tabela.cod_empenho
                    ,tabela.cod_nota
                    ,tabela.data_pagamento ASC
                    ,tabela.sinal_valor DESC
                    ,tabela.ordem ASC
            ';


    FOR reRegistro IN EXECUTE stSql
    LOOP
         RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_item;
    DROP TABLE tmp_total;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';

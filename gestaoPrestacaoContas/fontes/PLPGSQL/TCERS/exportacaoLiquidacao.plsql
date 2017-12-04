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
* $Id: exportacaoLiquidacao.plsql 66563 2016-09-26 18:31:21Z franver $
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.08.01
* Casos de uso: uc-02.08.01
*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_liquidacao(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF
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
                        empenho.nota_liquidacao         as nl,
                        empenho.nota_liquidacao_item    as li
                    WHERE   li.exercicio        =  nl.exercicio
                    AND     li.cod_nota         =  nl.cod_nota
                    AND     li.cod_entidade     =  nl.cod_entidade

                    AND     nl.exercicio        = ' || quote_literal(stExercicio)     || '
                    AND     nl.cod_entidade    IN (' || stCodEntidades                 || ')
                    AND     nl.dt_liquidacao   between to_date(' || quote_literal(stDtInicial)     || ',''dd/mm/yyyy'') AND
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
                        nl.cod_nota,
                        nl.dt_liquidacao as data_pagamento,
                        ((tcers.fn_exportacao_liquidacao_total_liquidado(nl.exercicio,nl.cod_nota,nl.cod_entidade)
                        - tcers.fn_exportacao_liquidacao_total_anulado(nl.exercicio,nl.cod_nota,nl.cod_entidade,'''||stExercicio||'''))
                        -(tcers.fn_exportacao_liquidacao_total_pago(nl.exercicio,nl.cod_nota,nl.cod_entidade,'''|| stExercicio||''')
                        - tcers.fn_exportacao_liquidacao_anulacao_pagamento(nl.exercicio,nl.cod_nota,nl.cod_entidade,'''||stExercicio||''')))
                        as valor_liquidacao,
                        cast(''+'' as character(1)) as sinal_valor,
                        CASE WHEN nl.observacao='''' THEN ''.''
                        ELSE nl.observacao
                        END as observacao,
                        cast(2 as integer) as ordem
                    FROM
                        empenho.empenho as em,
                        empenho.nota_liquidacao as nl
                    WHERE   nl.exercicio            < ' || quote_literal(stExercicio)     || '
                    AND     em.exercicio            = nl.exercicio_empenho
                    AND     em.cod_empenho          = nl.cod_empenho
                    AND     em.cod_entidade         = nl.cod_entidade
                    AND     em.cod_entidade        IN (' || stCodEntidades  || ')
                ) AS tbl
                WHERE tbl.valor_liquidacao  <>   0.00 ';
    EXECUTE stSql;

    stSql := 'SELECT * from(
                  SELECT
                    em.exercicio,
                    em.cod_empenho,
                    em.cod_entidade,
                    nl.cod_nota,
                    nl.dt_liquidacao as data_pagamento,
                    tcers.fn_total_valor_liquidado(nl.cod_nota,nl.cod_entidade) as valor_liquidacao,
                    ''+'' as sinal_valor,
                    CASE WHEN TRIM(nl.observacao)='''' THEN ''.''
                         ELSE TRIM(replace(nl.observacao, E''\r\n'', ''''))::VARCHAR
                    END AS observacao,
                    0 as ordem,
                    em.oid
                  FROM
                    empenho.empenho as em,
                    empenho.nota_liquidacao as nl
                  WHERE
                        nl.dt_liquidacao   between to_date(' || quote_literal(stDtInicial)     || ',''dd/mm/yyyy'') AND
                                                   to_date(' || quote_literal(stDtFinal)       || ',''dd/mm/yyyy'')
                  AND     nl.exercicio            = ' || quote_literal(stExercicio)     || '

                  AND     em.exercicio            = nl.exercicio_empenho
                  AND     em.cod_empenho          = nl.cod_empenho
                  AND     em.cod_entidade         = nl.cod_entidade
                  AND     em.cod_entidade        IN (' || stCodEntidades  || ')

                UNION

                SELECT
                    em.exercicio,
                    em.cod_empenho,
                    em.cod_entidade,
                    nl.cod_nota,
                    to_date(to_char(la."timestamp",''dd/mm/yyyy''),''dd/mm/yyyy'') as data_pagamento,
                    la.vl_anulado as valor_liquidacao,
                    ''-'' as sinal_valor,
                    CASE WHEN TRIM(nl.observacao)='''' THEN ''.''
                         ELSE TRIM(replace(nl.observacao, E''\r\n'', ''''))::VARCHAR
                    END as observacao,
                    1 as ordem,
                    la.oid
                FROM
                    empenho.empenho as em,
                    empenho.nota_liquidacao as nl,
                    empenho.nota_liquidacao_item as li,
                    empenho.nota_liquidacao_item_anulado as la
                WHERE   em.exercicio            = nl.exercicio_empenho
                AND     em.cod_empenho          = nl.cod_empenho
                AND     em.cod_entidade         = nl.cod_entidade
                AND     em.cod_entidade         IN (' || stCodEntidades  || ')

                AND     nl.exercicio            = li.exercicio
                AND     nl.cod_nota             = li.cod_nota
                AND     nl.cod_entidade         = li.cod_entidade

                AND     to_date(to_char(la."timestamp",''dd/mm/yyyy''),''dd/mm/yyyy'')
                        between to_date(' || quote_literal(stDtInicial)     || ',''dd/mm/yyyy'') AND
                                to_date(' || quote_literal(stDtFinal)       || ',''dd/mm/yyyy'')

                AND     li.exercicio            = la.exercicio
                AND     li.cod_nota             = la.cod_nota
                AND     li.cod_entidade         = la.cod_entidade
                AND     li.num_item             = la.num_item
                AND     li.cod_pre_empenho      = la.cod_pre_empenho
                AND     li.exercicio_item       = la.exercicio_item

                UNION
                SELECT   exercicio
                        ,cod_empenho
                        ,cod_entidade
                        ,min(cod_nota)
                        ,(  SELECT  dt_liquidacao 
                            FROM    empenho.nota_liquidacao as nl 
                            WHERE   nl.exercicio_empenho= tm.exercicio 
                            AND     nl.exercicio        = tm.exercicio_nota
                            AND     nl.cod_entidade     = tm.cod_entidade 
                            AND     nl.cod_nota         = min(tm.cod_nota) 
                            AND     nl.cod_empenho      = tm.cod_empenho
                         ) as data_pagamento
                        ,sum(valor_liquidacao)
                        ,sinal_valor
                        , CASE WHEN max(TRIM(observacao))=''''
                               THEN ''.''
                               ELSE TRIM(replace(max(observacao), E''\r\n'', ''''))::VARCHAR
                           END as observacao
                           
                        ,ordem
                        ,1 AS oid
               FROM    tmp_total as tm
                WHERE
                        tm.data_pagamento BETWEEN TO_DATE('||quote_literal(stDtInicial)||', ''dd/mm/yyyy'') - interval ''1 year'' AND TO_DATE('||quote_literal(stDtFinal)||', ''dd/mm/yyyy'')
                GROUP BY     exercicio
                            ,cod_entidade
                            ,cod_empenho
                            ,ordem
                            ,sinal_valor
                            ,exercicio_nota
                HAVING  sum(valor_liquidacao) <> 0.00 
                ORDER BY     exercicio
                            ,cod_entidade
                            ,cod_empenho

             ) AS tabela
             ORDER BY        tabela.exercicio DESC,tabela.cod_entidade,tabela.cod_empenho,tabela.cod_nota,tabela.ordem ASC
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

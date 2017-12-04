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
* $Id:$
*
*/

CREATE OR REPLACE FUNCTION tcmgo.fn_rl_anexo11 (varchar, varchar, varchar, varchar, varchar , varchar) RETURNS SETOF RECORD AS
$$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSituacao    		ALIAS FOR $5;
    stCodEntidades      ALIAS FOR $6;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
BEGIN
    stSqlComplemento := ' AND dt_suplementacao BETWEEN 
     to_date( ' || quote_literal(stDtInicial) || ' , ' || quote_literal('dd/mm/yyyy') || '   ) 
AND  to_date( ' || quote_literal(stDtFinal)   || ' , ' || quote_literal('dd/mm/yyyy') || '   ) ';

    --cria tabela temporaria para totalizar valores originais
    stSql := 'CREATE TEMPORARY TABLE tmp_valor_original AS
                    SELECT
                    CD.cod_estrutural
                    ,SUM(D.vl_original) as valor
                FROM
                    orcamento.conta_despesa CD LEFT JOIN orcamento.despesa D ON
                    CD.exercicio     = D.exercicio
                    AND CD.cod_conta = D.cod_conta
                    AND CD.exercicio = ' || quote_literal(stExercicio)  || '
                    AND D.cod_entidade IN (' || quote_literal(stCodEntidades) || ') ' || stFiltro || '
                GROUP BY
                    CD.cod_estrutural
                ORDER BY
                    CD.cod_estrutural ';
    EXECUTE stSql;

    --cria tabela temporaria para armazenar valores de suplementacao
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_suplementada AS
                    SELECT
                        CD.cod_estrutural
                        ,S.cod_tipo
                        ,SUM(SS.valor) as valor
                    FROM
                         orcamento.conta_despesa              CD
                        ,orcamento.despesa                     D
                        ,orcamento.suplementacao_suplementada SS
                        ,orcamento.suplementacao               S
                    WHERE
                            CD.exercicio   = D.exercicio
                        AND CD.cod_conta   = D.cod_conta
                        AND SS.cod_despesa = D.cod_despesa
                        AND SS.exercicio   = D.exercicio
                        AND SS.exercicio   = S.exercicio
                        AND SS.cod_suplementacao = S.cod_suplementacao
                        AND CD.exercicio = ' || quote_literal(stExercicio)  || '
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa
                                          WHERE o_sa.cod_suplementacao = SS.cod_suplementacao
                                            AND o_sa.exercicio         = SS.exercicio
                                       )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa2
                                          WHERE o_sa2.cod_suplementacao_anulacao = SS.cod_suplementacao
                                            AND o_sa2.exercicio                  = SS.exercicio
                                       )
                        AND D.cod_entidade IN (' || quote_literal(stCodEntidades) || ') ' || stFiltro || '
                        ' || stSqlComplemento || '
                    GROUP BY
                        CD.cod_estrutural,
                        S.cod_tipo
                    ORDER BY
                        CD.cod_estrutural,
                        S.cod_tipo ';
    EXECUTE stSql;

    --cria tabela temporaria para armazenar valores de suplementacao reduzida
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_reduzida AS
                    SELECT
                        CD.cod_estrutural
                        ,S.cod_tipo
                        ,SUM(SR.valor) as valor
                    FROM
                         orcamento.conta_despesa         CD
                        ,orcamento.despesa               D
                        ,orcamento.suplementacao_reducao SR
                        ,orcamento.suplementacao         S
                    WHERE
                            CD.exercicio   = D.exercicio
                        AND CD.cod_conta   = D.cod_conta
                        AND SR.cod_despesa = D.cod_despesa
                        AND SR.exercicio   = D.exercicio
                        AND SR.exercicio   = S.exercicio
                        AND SR.cod_suplementacao = S.cod_suplementacao

                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa3
                                          WHERE o_sa3.cod_suplementacao = SR.cod_suplementacao
                                            AND o_sa3.exercicio         = SR.exercicio
                                       )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa4
                                          WHERE o_sa4.cod_suplementacao_anulacao = SR.cod_suplementacao
                                            AND o_sa4.exercicio                  = SR.exercicio
                                       )
                        AND CD.exercicio = ' || quote_literal(stExercicio) || '
                        AND D.cod_entidade IN (' || quote_literal(stCodEntidades) || ') ' || stFiltro || '
                        ' || stSqlComplemento || '
                    GROUP BY
                        CD.cod_estrutural,
                        S.cod_tipo
                    ORDER BY
                        CD.cod_estrutural,
                        S.cod_tipo ';
    EXECUTE stSql;

    --cria tabela temporaria para totalizar valores de liquidacao
         
    stSql := '
        CREATE TEMPORARY TABLE tmp_total_liquidacao AS (
        SELECT
        ';
    
    IF stSituacao = 'Empenhado' THEN
    stSql := stSql || ' 
                 cod_estrutural
                ,coalesce(sum(vl_total)) as vl_total
                ,coalesce(sum(vl_anulado),0.00) as vl_anulado 
    ';
    END IF;
    IF stSituacao = 'Liquidado' THEN
    stSql := stSql || '
                 ocd.cod_estrutural 
                ,sum(nl.vl_total) as vl_total
                ,sum(coalesce( nla.vl_anulado,0.00) ) as vl_anulado
                ';
    END IF;
    IF stSituacao = 'Pago' THEN
    stSql := stSql || '
                 ocd.cod_estrutural 
                ,sum(nlp.vl_total) as vl_total
                ,coalesce(sum(nlp.vl_anulado),0.00) as vl_anulado
    ';
    END IF;

    IF stSituacao != 'Empenhado' THEN

    stSql := stSql || ' 
        FROM
                orcamento.conta_despesa as ocd
                JOIN empenho.pre_empenho_despesa as ped
                ON (    ped.cod_conta = ocd.cod_conta
                    AND ped.exercicio   = ocd.exercicio
                )
                JOIN orcamento.despesa as D
                ON (    D.exercicio = ped.exercicio
                    AND D.cod_despesa = ped.cod_despesa
                )
                JOIN empenho.pre_empenho as pe 
                ON (    pe.cod_pre_empenho  = ped.cod_pre_empenho
                    AND pe.exercicio        = ped.exercicio
                )
                JOIN empenho.empenho as e
                ON (    e.cod_pre_empenho   = pe.cod_pre_empenho
                    AND e.exercicio         = pe.exercicio
                )
                ';



    END IF;	            
        IF stSituacao = 'Empenhado' THEN
        stSql := stSql || ' 
	
        FROM
        (

            SELECT

                     ocd.cod_estrutural
                    ,sum(ipe.vl_total) as vl_total
                    ,0.00 as vl_anulado

            FROM
                    orcamento.conta_despesa as ocd
                    JOIN empenho.pre_empenho_despesa as ped
                    ON (    ped.cod_conta = ocd.cod_conta
                        AND ped.exercicio   = ocd.exercicio
                    )
                    JOIN orcamento.despesa as D
                    ON (    D.exercicio = ped.exercicio
                        AND D.cod_despesa = ped.cod_despesa
                    )
                    JOIN empenho.pre_empenho as pe
                    ON (    pe.cod_pre_empenho  = ped.cod_pre_empenho
                        AND pe.exercicio        = ped.exercicio
                    )
                    JOIN empenho.empenho as e
                    ON (    e.cod_pre_empenho   = pe.cod_pre_empenho
                        AND e.exercicio         = pe.exercicio
                    )

                    JOIN (  SELECT
                                exercicio
                                ,cod_pre_empenho
                                ,sum(vl_total) as vl_total
                            FROM
                                empenho.item_pre_empenho
                            GROUP BY
                                 exercicio
                                ,cod_pre_empenho
                    ) as ipe
                    ON (       pe.cod_pre_empenho  = ipe.cod_pre_empenho
                           AND pe.exercicio        = ipe.exercicio
                    )
                    WHERE
                        e.dt_empenho BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')
                                     AND     to_date('|| quote_literal(stDtFinal) || ',''dd/mm/yyyy'')
                      AND e.cod_entidade IN (' || quote_literal(stCodEntidades) || ')
                     ';

                    if stFiltro is not null then
                        stSql := stSql || ' ' || stFiltro;
                    end if;

                    stSql = stSql || ' GROUP BY ocd.cod_estrutural

            UNION

            SELECT
                 ocd.cod_estrutural
                ,0.00 as vl_total
                ,coalesce(sum(ipe.vl_anulado),0.00) as vl_anulado
            FROM
                orcamento.conta_despesa as ocd
                JOIN empenho.pre_empenho_despesa as ped
                ON (    ped.cod_conta = ocd.cod_conta
                    AND ped.exercicio   = ocd.exercicio
                )
                JOIN orcamento.despesa as D
                ON (    D.exercicio = ped.exercicio
                    AND D.cod_despesa = ped.cod_despesa
                )
                JOIN empenho.pre_empenho as pe
                ON (    pe.cod_pre_empenho  = ped.cod_pre_empenho
                    AND pe.exercicio        = ped.exercicio
                )
                JOIN empenho.empenho as e
                ON (    e.cod_pre_empenho   = pe.cod_pre_empenho
                    AND e.exercicio         = pe.exercicio
                )
                JOIN ( SELECT cod_pre_empenho
                               ,exercicio
                               ,coalesce(sum(vl_anulado),0.00) as vl_anulado
                               FROM empenho.empenho_anulado_item
                       WHERE to_date(to_char(timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                     BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')
                                     AND     to_date('|| quote_literal(stDtFinal) || ',''dd/mm/yyyy'')
                             AND exercicio = '''''' || stExercicio || ''''''
                       GROUP BY cod_pre_empenho,exercicio
                ) as ipe
                ON (    pe.cod_pre_empenho  = ipe.cod_pre_empenho
                    AND pe.exercicio        = ipe.exercicio
                )

            WHERE e.cod_entidade IN (' || quote_literal(stCodEntidades) || ') ';


            if stFiltro is not null then
                stSql := stSql || ' ' || stFiltro;
            end if;

            stSql := stSql || '
            
            GROUP BY
                ocd.cod_estrutural
        ) as tudo
        GROUP BY cod_estrutural
        ORDER BY cod_estrutural
	    ';
	    END IF;
	    
	    IF stSituacao = 'Liquidado' THEN
		    stSql := stSql || '    ---liquidações do periodo
                               LEFT JOIN (  select nota_liquidacao.exercicio_empenho
                                                , nota_liquidacao.cod_entidade
                                                , nota_liquidacao.cod_empenho
                                                , sum ( nota_liquidacao_item.vl_total ) as vl_total
                                             from empenho.nota_liquidacao
                                             join empenho.nota_liquidacao_item
                                               on ( nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                                              and   nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                                              and   nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota    )
                                            where nota_liquidacao.dt_liquidacao BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')
                                                                                AND     to_date(' || quote_literal(stDtFinal )  || ',''dd/mm/yyyy'')
                                         group by nota_liquidacao.exercicio_empenho
                                                , nota_liquidacao.cod_entidade
                                                , nota_liquidacao.cod_empenho
                                       ) as nl
                                       ON ( nl.exercicio_empenho    = e.exercicio
                                      AND   nl.cod_entidade         = e.cod_entidade
                                      AND   nl.cod_empenho          = e.cod_empenho ) 

                                    --- anulações de liquidações do periodo

                                        left join ( select nota_liquidacao.exercicio_empenho
                                                         , nota_liquidacao.cod_entidade
                                                         , nota_liquidacao.cod_empenho
                                                         , sum ( nota_liquidacao_item_anulado.vl_anulado ) as vl_anulado
                                                      from empenho.nota_liquidacao
                                                      join empenho.nota_liquidacao_item_anulado
                                                        on ( nota_liquidacao_item_anulado.exercicio    = nota_liquidacao.exercicio
                                                       and   nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao.cod_entidade
                                                       and   nota_liquidacao_item_anulado.cod_nota     = nota_liquidacao.cod_nota )
                                                     where to_date(to_char(timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'')
              										  	BETWEEN to_date('|| quote_literal(stDtInicial) || ',''dd/mm/yyyy'')
			               							  	AND     to_date('|| quote_literal(stDtFinal ) || ',''dd/mm/yyyy'')
                                                    group by nota_liquidacao.exercicio_empenho
                                                           , nota_liquidacao.cod_entidade
                                                           , nota_liquidacao.cod_empenho
                                               ) as nla
                                              ON ( nla.exercicio_empenho    = e.exercicio
                                             AND nla.cod_entidade         = e.cod_entidade
                                             AND nla.cod_empenho          = e.cod_empenho )
       
                                    where e.exercicio =  '|| quote_literal(stExercicio) || '  
                                      AND e.cod_entidade IN (' || quote_literal(stCodEntidades) || ') ' || stFiltro || '

                                    GROUP BY ocd.cod_estrutural
                                    ORDER BY ocd.cod_estrutural    		    
                                '
                            ;
	    END IF;    

	    IF stSituacao = 'Pago' THEN
		stSql := stSql || ' 		
		      
                JOIN empenho.nota_liquidacao as nl
                ON (
                        nl.exercicio_empenho    = e.exercicio
                    AND nl.cod_entidade         = e.cod_entidade
                    AND nl.cod_empenho          = e.cod_empenho
		        )	
                JOIN (	SELECT 	 nlp.exercicio	
                                ,nlp.cod_nota
                                ,nlp.cod_entidade
                                ,nlp.timestamp
                                ,sum(nlp.vl_pago) as vl_total
                                ,nlpa.vl_anulado as vl_anulado
                        FROM  empenho.nota_liquidacao_paga as nlp
                            LEFT JOIN ( SELECT	 timestamp
                                                ,exercicio
                                                ,cod_nota
                                                ,cod_entidade
                                                ,coalesce(sum(vl_anulado),0.00) as vl_anulado
                                                ,timestamp_anulada
                                        FROM empenho.nota_liquidacao_paga_anulada
                                        WHERE   to_date(to_char(timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                                                BETWEEN to_date(' || quote_literal( stDtInicial) || ',''dd/mm/yyyy'') 
                                                AND     to_date('|| quote_literal( stDtFinal )  || ',''dd/mm/yyyy'')
                                        GROUP BY timestamp, exercicio, cod_nota, cod_entidade, timestamp_anulada
                                       ) as nlpa ON (       nlp.exercicio       = nlpa.exercicio
                                                        AND nlp.cod_nota        = nlpa.cod_nota 
                                                        AND nlp.cod_entidade    = nlpa.cod_entidade
                                                        AND nlp.timestamp       = nlpa.timestamp
                                        			)
                            -- INICIO INCLUIDO NO TICKET 10045
                            WHERE to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                                  BETWEEN to_date(' || quote_literal( stDtInicial) || ',''dd/mm/yyyy'') 
                                  AND to_date(' || quote_literal( stDtFinal )  || ',''dd/mm/yyyy'')
                            -- FIM TICKET 10045
                        GROUP BY nlp.exercicio,nlp.cod_nota,nlp.timestamp,nlp.cod_entidade,nlpa.vl_anulado
                ) as nlp ON (
                                    nlp.cod_nota        = nl.cod_nota
                                AND nlp.exercicio       = nl.exercicio
                                AND nlp.cod_entidade    = nl.cod_entidade
	            )
           -- TICKET 10045
--        WHERE e.dt_empenho  BETWEEN to_date('|| quote_literal( stDtInicial )|| ',''dd/mm/yyyy'') 
--                            AND     to_date(' || quote_literal( stDtFinal )  || ',''dd/mm/yyyy'')

            WHERE e.exercicio = '|| quote_literal( stExercicio ) || ' 
              AND e.cod_entidade IN ('|| quote_literal( stCodEntidades ) || ') ';
            
            if stFiltro is not null then
                stSql := stSql || ' ' || stFiltro;
            end if;

            stSql := stSql || '
            GROUP BY ocd.cod_estrutural
            ORDER BY ocd.cod_estrutural    
		
	    ';
	    END IF; 
	   
	    stSql := stSql || ' 
	    )  -- Fim do create temporary table ';
	   
    EXECUTE stSql;
    
    CREATE UNIQUE INDEX unq_valor_liquidacao   ON tmp_total_liquidacao           ( cod_estrutural varchar_pattern_ops           );
    CREATE UNIQUE INDEX unq_valor_original     ON tmp_valor_original             ( cod_estrutural varchar_pattern_ops           );
    CREATE UNIQUE INDEX unq_suplemento_aumento ON tmp_suplementacao_suplementada ( cod_estrutural varchar_pattern_ops, cod_tipo );
    CREATE UNIQUE INDEX unq_suplemento_reducao ON tmp_suplementacao_reduzida     ( cod_estrutural varchar_pattern_ops, cod_tipo );

    --seleciona todas as contas...
    stSql := 'SELECT
                    CD.cod_estrutural
                    ,CD.descricao
                    ,publico.fn_nivel(CD.cod_estrutural) as nivel
                    ,0.00 as vl_original
                    ,0.00 as vl_credito_orcamentario
                    ,0.00 as vl_credito_especial
                    ,0.00 as vl_liquidado
                FROM
                     orcamento.conta_despesa CD LEFT JOIN orcamento.despesa D ON
                            CD.exercicio   = D.exercicio
                        AND CD.cod_conta   = D.cod_conta
                        AND CD.exercicio = '' || quote_literal(stExercicio)  || ''
                        AND D.cod_entidade IN (' || quote_literal( stCodEntidades) || ') ' || stFiltro || '
                WHERE
                        CD.exercicio  = ' || quote_literal(stExercicio) || '
                GROUP BY
                    CD.cod_estrutural,CD.descricao
                ORDER BY
                    CD.cod_estrutural ' ;
    FOR reRegistro IN EXECUTE stSql LOOP
        arRetorno := contabilidade.fn_totaliza_suplementacoes( publico.fn_mascarareduzida(reRegistro.cod_estrutural) );
        reRegistro.vl_original := coalesce( arRetorno[5] , 0.00);
        reRegistro.vl_credito_orcamentario := ( ( reRegistro.vl_original + arRetorno[1] ) - arRetorno[2] );
        reRegistro.vl_credito_orcamentario := coalesce( reRegistro.vl_credito_orcamentario , 0.00);
        reRegistro.vl_credito_especial     := ( arRetorno[3] - arRetorno[4]);
        reRegistro.vl_liquidado            := arRetorno[6];
        RETURN NEXT reRegistro;
    END LOOP;

    DROP INDEX unq_valor_original;
    DROP INDEX unq_valor_liquidacao;
    DROP INDEX unq_suplemento_aumento;
    DROP INDEX unq_suplemento_reducao;

    DROP TABLE tmp_valor_original;
    DROP TABLE tmp_total_liquidacao;
    DROP TABLE tmp_suplementacao_suplementada;
    DROP TABLE tmp_suplementacao_reduzida;

    RETURN;
END;
$$ LANGUAGE plpgsql

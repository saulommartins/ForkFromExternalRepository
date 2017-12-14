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
* $Revision: 28549 $
* $Name:  $
* $Author: tonismar $
* $Date: 2008-03-14 09:16:14 -0300 (Sex, 14 Mar 2008) $
*
* Casos de uso: uc-02.04.24
*/
CREATE OR REPLACE FUNCTION tcemg.fn_saldos_bancos(varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    inCodEntidade           ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stCodEstruturalInicial  ALIAS FOR $5;
    stCodEstruturalFinal    ALIAS FOR $6;
    inCodReduzidoInicio     ALIAS FOR $7;
    inCodReduzidoFim        ALIAS FOR $8;
    inCodRecurso            ALIAS FOR $9;
    boSemMovimento          ALIAS FOR $10;
    stDestinacaoRecurso     ALIAS FOR $11;
    inCodDetalhamento       ALIAS FOR $12;
    boUtilizaEstruturalTCE  ALIAS FOR $13;

    reRegistro          RECORD;

    stDataAno           VARCHAR := '';
    stSql               VARCHAR := '';

BEGIN

--SELECT tesouraria.fn_listar_arrecadacao('','') INTO boTabela;

stDataAno := '01/01/' || stExercicio;



stSql := '
    SELECT 
        *
    FROM (
        SELECT
            cast (CPC.exercicio as varchar) as exercicio
            ,CPC.cod_estrutural
            ,CPA.cod_plano
            ,CPC.nom_conta
            ,ABS((SELECT saldo_anterior                                                                                 
		            FROM tesouraria.fn_relatorio_demostrativo_saldos('|| quote_literal(stExercicio)   ||'
                                                                    ,'|| quote_literal(inCodEntidade) ||'
                                                                    ,'|| quote_literal(stDtInicial)   ||'
                                                                    ,'|| quote_literal(stDtFinal)     ||'
                                                                    ,''''
                                                                    ,''''
                                                                    ,CPA.cod_plano::VARCHAR,CPA.cod_plano::VARCHAR
                                                                    ,''''
                                                                    ,''S''
                                                                    ,''''
                                                                    ,''''
                                                                    ,''true''  
								                    ) as retorno( exercicio          VARCHAR                                                        
									                	 ,cod_estrutural     VARCHAR                                                        
									                	 ,cod_plano          INTEGER                                                        
									                	 ,nom_conta          VARCHAR                                                        
									                	 ,saldo_anterior     NUMERIC                                                        
									                	 ,vl_credito         NUMERIC                                                        
									                	 ,vl_debito          NUMERIC                                                        
									                	 ,cod_recurso        INTEGER                                                        
									                	 ,nom_recurso        VARCHAR                                                        
									                	)                                                                                               
		         ORDER BY cod_estrutural ASC  )
            ) as saldo_anterior
            ,ABS((SELECT (vl_debito + saldo_anterior)- vl_credito  AS saldo_atual                                                                    
		            FROM tesouraria.fn_relatorio_demostrativo_saldos('|| quote_literal(stExercicio)   ||'
                                                                    ,'|| quote_literal(inCodEntidade) ||'
                                                                    ,'|| quote_literal(stDtInicial)   ||'
                                                                    ,'|| quote_literal(stDtFinal)     ||'
                                                                    ,''''
                                                                    ,''''
                                                                    ,CPA.cod_plano::VARCHAR,CPA.cod_plano::VARCHAR
                                                                    ,''''
                                                                    ,''S''
                                                                    ,''''
                                                                    ,''''
                                                                    ,''true''  
								                    ) as retorno( exercicio          VARCHAR                                                        
									                	 ,cod_estrutural     VARCHAR                                                        
									                	 ,cod_plano          INTEGER                                                        
									                	 ,nom_conta          VARCHAR                                                        
									                	 ,saldo_anterior     NUMERIC                                                        
									                	 ,vl_credito         NUMERIC                                                        
									                	 ,vl_debito          NUMERIC                                                        
									                	 ,cod_recurso        INTEGER                                                        
									                	 ,nom_recurso        VARCHAR                                                        
									                	)                                                                                               
		         ORDER BY cod_estrutural ASC  )
            ) as saldo_atual
            ,coalesce(arrecadacoes.vl_credito,0.00) + coalesce(pagamentos.vl_credito,0.0) + coalesce(transferencias.vl_credito,0.00) AS vl_credito
            ,coalesce(arrecadacoes.vl_debito,0.00)  + coalesce(pagamentos.vl_debito,0.00) + coalesce(transferencias.vl_debito,0.00) AS vl_debito
            ,cpr.cod_recurso as cod_recurso
            ,rec.nom_recurso as nom_recurso
            ,CPB.cod_agencia
            ,CPB.conta_corrente
        FROM 
             contabilidade.plano_conta     AS CPC
            ,contabilidade.plano_analitica AS CPA
            -- PAGAMENTOS
            LEFT JOIN(
                SELECT tbl.cod_plano
                      ,tbl.exercicio
                      ,sum(tbl.vl_credito) as vl_credito
                      ,sum(tbl.vl_debito ) as vl_debito
                 FROM (
                    --
                    -- Pagamentos
                    --
                    SELECT   tp.cod_plano
                            ,tp.exercicio_plano as exercicio
                            ,coalesce(sum(nlp.vl_pago),0.00) as vl_credito
                            ,0.00 as vl_debito
                      FROM   tesouraria.pagamento as tp
                             JOIN empenho.nota_liquidacao_paga as nlp
                                ON (    nlp.exercicio    = tp.exercicio
                                    AND nlp.cod_nota     = tp.cod_nota
                                    AND nlp.cod_entidade = tp.cod_entidade
                                    AND nlp.timestamp    = tp.timestamp
                                )
                      WHERE TO_DATE(TO_CHAR(tp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                                                                                                    AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                  GROUP BY  tp.cod_plano
                           ,tp.exercicio_plano

                    UNION ALL
                    --
                    -- estornos de pagamentos
                    --
                    SELECT   tp.cod_plano
                            ,tp.exercicio_plano as exercicio
                            ,0.00 as vl_credito
                            ,coalesce(sum(tpe.vl_anulado),0.00) as vl_debito
                      FROM   tesouraria.pagamento as tp
                             JOIN(
                                SELECT   tpe.exercicio
                                        ,tpe.timestamp
                                        ,tpe.cod_nota
                                        ,tpe.cod_entidade
                                        ,coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado
                                  FROM  tesouraria.pagamento_estornado as tpe
                                        JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                                            ON (    nlpa.exercicio         = tpe.exercicio
                                                AND nlpa.cod_entidade      = tpe.cod_entidade
                                                AND nlpa.cod_nota          = tpe.cod_nota
                                                AND nlpa.timestamp         = tpe.timestamp
                                                AND nlpa.timestamp_anulada = tpe.timestamp_anulado
                                                AND TO_DATE(TO_CHAR(tpe.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                                                 BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                                                    AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                                            )
                              GROUP BY  tpe.exercicio
                                        ,tpe.timestamp
                                        ,tpe.cod_nota
                                        ,tpe.cod_entidade

                             ) AS tpe ON (     tp.exercicio    = tpe.exercicio
                                           AND tp.cod_entidade = tpe.cod_entidade
                                           AND tp.cod_nota     = tpe.cod_nota
                                           AND tp.timestamp    = tpe.timestamp
                             )
                    
                   GROUP BY  tp.cod_plano
                            ,tp.exercicio_plano
                ) as tbl
                GROUP BY cod_plano, exercicio
            ) as pagamentos ON (    pagamentos.cod_plano = cpa.cod_plano
                                AND pagamentos.exercicio = cpa.exercicio 
            )
            -- TRANSFERENCIAS
            LEFT JOIN(
                SELECT  exercicio, 
                        cod_plano, 
                        coalesce(sum(vl_debito),0.00)  as vl_debito, 
                        coalesce(sum(vl_credito),0.00) as vl_credito

                FROM (
                        SELECT
                                tt.cod_plano_debito as cod_plano
                               ,tt.exercicio
                               ,coalesce(tt.valor,0.00) as vl_debito
                               ,0.00 as vl_credito
                          FROM tesouraria.transferencia as tt
                               JOIN contabilidade.plano_banco as pb 
                                    ON (    pb.cod_plano  = tt.cod_plano_debito
                                        AND pb.exercicio = tt.exercicio 
                                    ) 
                         WHERE TO_DATE(TO_CHAR(tt.timestamp_transferencia,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                              BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                                 AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                    UNION ALL

                        SELECT
                                tt.cod_plano_credito as cod_plano
                               ,tt.exercicio
                               ,0.00 as vl_debito
                               ,coalesce(tt.valor,0.00) as vl_credito
                          FROM tesouraria.transferencia as tt
                               JOIN contabilidade.plano_banco as pb
                                    ON (    pb.cod_plano  = tt.cod_plano_credito
                                        AND pb.exercicio = tt.exercicio
                                    )
                         WHERE TO_DATE(TO_CHAR(tt.timestamp_transferencia,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                              BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                                 AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                    UNION ALL

                        SELECT
                                tt.cod_plano_debito as cod_plano
                               ,tt.exercicio
                               ,0.00 as vl_debito
                               ,coalesce(tte.valor,0.00) as vl_credito
                          FROM tesouraria.transferencia as tt
                               JOIN tesouraria.transferencia_estornada as tte
                                    ON (    tte.exercicio    = tt.exercicio
                                        AND tte.cod_entidade = tt.cod_entidade
                                        AND tte.cod_lote     = tt.cod_lote
                                        AND tte.tipo         = tt.tipo
                                        AND tt.cod_tipo in (1,2)
                                        AND TO_DATE(TO_CHAR(tte.timestamp_estornada,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                              BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                                 AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                                    )
                               JOIN contabilidade.plano_banco as pb
                                    ON (    pb.cod_plano  = tt.cod_plano_debito
                                        AND pb.exercicio  = tt.exercicio
                                    )
                    UNION ALL

                         SELECT
                                tt.cod_plano_credito as cod_plano
                               ,tt.exercicio
                               ,coalesce(tte.valor,0.00) as vl_debito
                               ,0.00 as vl_credito
                          FROM tesouraria.transferencia as tt
                               JOIN tesouraria.transferencia_estornada as tte
                                    ON (    tte.exercicio    = tt.exercicio
                                        AND tte.cod_entidade = tt.cod_entidade
                                        AND tte.cod_lote     = tt.cod_lote
                                        AND tte.tipo         = tt.tipo
                                        AND tt.cod_tipo in (1,2)
                                        AND TO_DATE(TO_CHAR(tte.timestamp_estornada,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                              BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                                 AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                                    )
                               JOIN contabilidade.plano_banco as pb
                                    ON (    pb.cod_plano  = tt.cod_plano_credito
                                        AND pb.exercicio  = tt.exercicio
                                    )
                 ) as tbl
                
                GROUP BY exercicio, cod_plano

            ) as transferencias ON (    transferencias.cod_plano = cpa.cod_plano
                                    AND transferencias.exercicio = cpa.exercicio
            )

            -- ARRECADAÇÕES
            LEFT JOIN(
                SELECT  
                         exercicio
                        ,cod_plano
                        ,sum(vl_credito) as vl_credito
                        ,sum(vl_debito) as vl_debito
                
                FROM (     
                    -- CONTA DE BANCO
                    -- Valor da dedução (-) valor estornado da dedução (+) Valor arrecadado Estornado = Valor a Crédito      (OK)
                    -- Valor arrecadado (-) Valor arrecadado Estornado - (Valor da dedução - Valor estornado da dedução) = Valor a débito   (OK)

                    SELECT
                          substr(random()::VARCHAR,3,10) as id
                         ,exercicio 
                         ,cod_plano as cod_plano -- conta de banco
                         -- Caso seja uma Devolução tem q inverter a demonstração do valor.
                         ,CASE WHEN devolucao = false
                            THEN coalesce(vl_deducao,0.00) + coalesce(vl_estornado,0.00)
                            ELSE coalesce(vl_arrecadacao,0.00) 
                         END AS vl_credito
                         ,CASE WHEN devolucao = false
                            THEN coalesce(vl_arrecadacao,0.00) + coalesce(vl_deducao_estornado,0.00) 
                            ELSE coalesce(vl_estornado,0.00)
                         END AS vl_debito
                         --
                         -- Antes da devolução de receita
                         --,coalesce(ad.vl_deducao,0.00) + coalesce(ae.vl_estornado,0.00) as vl_credito
                         --,coalesce(ar.vl_arrecadacao,0.00) - coalesce(ae.vl_estornado,0.00) - coalesce(ad.vl_deducao,0.00) as vl_debito
                    FROM 
                         --  ARRECADACAO RECEITA (Valor Arrecadado)
                       (   SELECT  ar.cod_arrecadacao
                                  ,ar.timestamp_arrecadacao
                                  ,ar.exercicio
                                  ,ta.cod_plano
                                  ,ar.cod_receita
                                  ,ta.devolucao
                                  ,vl_arrecadacao
                                  ,0.00 as vl_estornado
                                  ,0.00 as vl_deducao
                                  ,0.00 as vl_deducao_estornado
                             FROM  tesouraria.arrecadacao as ta
                                   JOIN tesouraria.arrecadacao_receita as ar
                                   ON (    ta.cod_arrecadacao = ar.cod_arrecadacao
                                       AND ta.timestamp_arrecadacao = ar.timestamp_arrecadacao
                                       AND ta.cod_arrecadacao   = ar.cod_arrecadacao
                                   )

                             WHERE TO_DATE(TO_CHAR(ar.timestamp_arrecadacao,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                               BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                                  AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                         

                        UNION ALL

                         -- ARRECADACAO ESTORNADA (Valor arrecadado estornado)
                              SELECT  ae.cod_arrecadacao
                                     ,ae.timestamp_arrecadacao
                                     ,ae.exercicio
                                     ,ta.cod_plano
                                     ,aer.cod_receita
                                     ,ta.devolucao
                                     ,0.00 as vl_arrecadacao
                                     ,coalesce(sum(aer.vl_estornado),0.00) as vl_estornado
                                     ,0.00 as vl_deducao
                                     ,0.00 as vl_deducao_estornado
                
                               FROM  tesouraria.arrecadacao as ta
                                     JOIN tesouraria.arrecadacao_estornada as ae
                                        USING ( cod_arrecadacao, exercicio, timestamp_arrecadacao )
                                     JOIN tesouraria.arrecadacao_estornada_receita as aer
                                     ON (    ae.timestamp_estornada   = aer.timestamp_estornada
                                         AND ae.timestamp_arrecadacao = aer.timestamp_arrecadacao
                                         AND ae.exercicio             = aer.exercicio
                                         AND ae.cod_arrecadacao       = aer.cod_arrecadacao
                                     )
                              WHERE TO_DATE(TO_CHAR(ae.timestamp_estornada,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                          BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                             AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')

                            GROUP BY ae.timestamp_arrecadacao
                                     ,ae.cod_arrecadacao
                                     ,ae.exercicio
                                     ,aer.cod_receita
                                     ,ta.cod_plano
                                     ,ta.devolucao
                

                        UNION ALL
                        -- ARRECADACAO DEDUTORA 
                            SELECT   ard.cod_arrecadacao
                                    ,ard.timestamp_arrecadacao
                                    ,ard.exercicio
                                    ,ta.cod_plano
                                    ,ard.cod_receita
                                    ,ta.devolucao
                                    ,0.00 as vl_arrecadacao
                                    ,0.00 as vl_estornado
                                    ,coalesce(ard.vl_deducao,0.00) as vl_deducao
                                    ,0.00 as vl_deducao_estornado

                              FROM  tesouraria.arrecadacao as ta
                                    JOIN tesouraria.arrecadacao_receita as tar
                                        USING ( cod_arrecadacao, timestamp_arrecadacao, exercicio )
                                    JOIN tesouraria.arrecadacao_receita_dedutora as ard
                                        USING ( cod_arrecadacao, timestamp_arrecadacao, exercicio, cod_receita )
                            WHERE TO_DATE(TO_CHAR(ard.timestamp_arrecadacao,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                    BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                       AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')


                        UNION ALL
                        -- ARRECADACAO DEDUTORA ESTORNADA
                            SELECT   ard.cod_arrecadacao
                                    ,ard.timestamp_arrecadacao
                                    ,ard.exercicio
                                    ,ta.cod_plano
                                    ,ard.cod_receita
                                    ,ta.devolucao
                                    ,0.00 as vl_arrecadacao
                                    ,0.00 as vl_estornado
                                    ,0.00 as vl_deducao
                                    ,coalesce(sum(arde.vl_estornado),0.00) as vl_deducao_estornado
                              FROM  tesouraria.arrecadacao as ta
                                    JOIN tesouraria.arrecadacao_receita as tar
                                        USING ( cod_arrecadacao, timestamp_arrecadacao, exercicio )
                                    JOIN tesouraria.arrecadacao_receita_dedutora as ard
                                        USING ( cod_arrecadacao, timestamp_arrecadacao, exercicio, cod_receita )
                                    JOIN tesouraria.arrecadacao_receita_dedutora_estornada as arde
                                    ON (    arde.cod_arrecadacao        = ard.cod_arrecadacao
                                        AND arde.cod_receita            = ard.cod_receita
                                        AND arde.exercicio              = ard.exercicio
                                        AND arde.timestamp_arrecadacao  = ard.timestamp_arrecadacao
                                        AND arde.cod_receita_dedutora   = ard.cod_receita_dedutora
                                        AND TO_DATE(TO_CHAR(arde.timestamp_dedutora_estornada,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                                           BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                                              AND  TO_DATE(''' || stDtFinal || '''  ,''dd/mm/yyyy'')
                                    )
                                                                                                                                      
                           GROUP BY  ard.cod_arrecadacao
                                    ,ard.cod_receita
                                    ,ard.exercicio 
                                    ,ard.timestamp_arrecadacao 
                                    ,ta.devolucao
                                    ,ta.cod_plano
                    ) as pim
                ) as tbl
                GROUP BY exercicio, cod_plano
            ) as arrecadacoes ON (    arrecadacoes.cod_plano = cpa.cod_plano
                                  AND arrecadacoes.exercicio = cpa.exercicio
                              )

            ,contabilidade.plano_banco     AS CPB
            ,contabilidade.plano_recurso   AS CPR
             JOIN orcamento.recurso('''|| stExercicio ||''') as REC
             ON ( rec.cod_recurso = cpr.cod_recurso
                AND rec.exercicio = cpr.exercicio )
        WHERE 
                CPC.exercicio = ''' || stExercicio || '''             
            AND CPB.cod_entidade IN (' || inCodEntidade || ') 
            AND CPC.exercicio    = CPA.exercicio                       
            AND CPC.cod_conta    = CPA.cod_conta                       
            AND CPA.exercicio    = CPB.exercicio                       
            AND CPA.cod_plano    = CPB.cod_plano                       
            AND CPA.exercicio    = CPR.exercicio                       
            AND CPA.cod_plano    = CPR.cod_plano          
    ';


    IF stCodEstruturalInicial is not null AND stCodEstruturalFinal is not null AND stCodEstruturalInicial != '' AND stCodEstruturalFinal != '' THEN
        stSql := stSql || ' AND CPC.cod_estrutural between ''' || stCodEstruturalInicial || ''' AND ''' || stCodEstruturalFinal || '''';
    ELSIF stCodEstruturalInicial is not null AND stCodEstruturalInicial != '' THEN
        stSql := stSql || ' AND CPC.cod_estrutural = ''' || stCodEstruturalInicial || '''';
    ELSIF stCodEstruturalFinal is not null AND stCodEstruturalFinal != '' THEN
        stSql := stSql || ' AND CPC.cod_estrutural = ''' || stCodEstruturalFinal || '''';
    END IF;
    
    IF inCodReduzidoInicio is not null AND inCodReduzidoFim is not null AND inCodReduzidoInicio != '' AND inCodReduzidoFim != '' THEN
        stSql := stSql || ' AND CPA.cod_plano between ' || inCodReduzidoInicio || ' AND ' || inCodReduzidoFim;
    ELSIF inCodReduzidoInicio is not null AND inCodReduzidoInicio != '' THEN
        stSql := stSql || ' AND CPA.cod_plano = ' || inCodReduzidoInicio;
    ELSIF inCodReduzidoFim is not null AND inCodReduzidoFim != '' THEN
        stSql := stSql || ' AND CPA.cod_plano = ' || inCodReduzidoFim;
    END IF;
    
    IF inCodRecurso is not null AND inCodRecurso != '' THEN
        stSql := stSql || ' AND CPR.cod_recurso IN (' || inCodRecurso || ')';
    END IF;

    IF (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') THEN
        stSql := stSql || ' AND rec.masc_recurso_red like '''|| stDestinacaoRecurso||'%'||''' ';
    END IF;

    IF (inCodDetalhamento is not null and inCodDetalhamento <> '') THEN
        stSql := stSql || ' AND rec.cod_detalhamento = '|| inCodDetalhamento ;
    END IF;

    stSql := stSql || '   
        ORDER BY 
            CPC.exercicio,
            CPC.cod_estrutural                                                                                          
    ) AS tbl 
    WHERE 1 = 1 ';
    
    IF boSemMovimento = 'N' THEN
    stSql := stSql || '
     AND  
        ( tbl.vl_credito      != 0.00 OR 
        tbl.vl_debito       != 0.00 OR 
        tbl.saldo_anterior  != 0.00 )
    ';
    END IF;

    IF boUtilizaEstruturalTCE = 'true' THEN
        stSql := stSql || ' AND tbl.cod_estrutural like ''1.1.1.%'' ';
    END IF;

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP;

RETURN;

END;

$$ language 'plpgsql';

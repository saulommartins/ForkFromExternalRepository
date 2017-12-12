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

CREATE OR REPLACE FUNCTION tcemg.relatorio_anexo3a_despesas ( varchar,varchar,varchar,varchar,varchar ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio         ALIAS FOR $1;
    stDtInicial     	ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    stCodConta          ALIAS FOR $5;
    
    reRegistro          record ;    
    stSql 		        varchar := '';
    
BEGIN
    -- CRIA A TABELA TEMPORÁRIA DO BALANCETE DE DESPESA PARA TRAZER TODOS OS DADOS
    
    stSql := '
              CREATE TEMPORARY TABLE tmp_despesas_educacao AS (
              SELECT 
                    cod_funcao
                    ,nom_funcao
                    ,cod_subfuncao
                    ,nom_subfuncao
                    ,cod_programa 
                    ,nom_programa                    
                    ,COALESCE(SUM(valor_pagamento),0.00) as valor_pagamento
                    
                FROM (
                        SELECT 
                                cod_funcao
                                ,nom_funcao
                                ,cod_subfuncao
                                ,nom_subfuncao
                                ,cod_programa 
                                ,nom_programa
                                ,COALESCE(SUM(valor_pagamento),0.00) as valor_pagamento
                                
                        FROM (
                                SELECT
                                        cod_ordem                                                                                                                        
                                        ,exercicio                                                                                                                        
                                        ,cod_entidade                                                                                                                     
                                        ,dt_emissao                                                                                                                       
                                        ,dt_vencimento                                                                                                                    
                                        ,observacao                                                                                                                       
                                        ,entidade                                                                                                                         
                                        ,cod_recurso                                                                                                                      
                                        ,masc_recurso_red                                                                                                                       
                                        ,cod_detalhamento                                                                                                                      
                                        ,cgm_beneficiario                                                                                                                 
                                        ,implantado                                                                                                                       
                                        ,beneficiario                                                                                                                     
                                        ,sum(num_exercicio_empenho) as num_exercicio_empenho                                                                              
                                        ,exercicio_empenho                                                                                                                
                                        ,'''' as dt_estorno                                                                                                                 
                                        ,coalesce(sum(valor_pagamento),0.00) as valor_pagamento                                                                           
                                        ,coalesce(sum(valor_anulada  ),0.00) as valor_anulada                                                                             
                                        ,coalesce(sum(saldo_pagamento),0.00) as saldo_pagamento                                                                           
                                        ,nota_empenho                                                                                                                     
                                        ,vl_nota                                                                                                                          
                                        ,vl_nota_anulacoes                                                                                                                
                                        ,vl_nota_original                                                                                                                 
                                        ,CASE WHEN (sum(coalesce(saldo_pagamento,0.00)) < coalesce(vl_nota,0.00)) AND (vl_nota > 0.00 )
                                              THEN ''A Pagar''::VARCHAR                                                                                
                                              WHEN (sum(coalesce(saldo_pagamento,0.00)) = coalesce(vl_nota,0.00)) AND (vl_nota > 0.00 )
                                              THEN ''Paga''::VARCHAR                                                                                   
                                              WHEN ( vl_nota = 0.00  )
                                              THEN ''Anulada''::VARCHAR                                                                              
                                        END AS situacao                                                                                                                  
                                        ,CASE WHEN coalesce(sum(valor_anulada),0.00) > 0.00 then ''Sim''  
                                        ELSE ''Não''                                                                                                                       
                                        END AS pagamento_estornado
                                        ,cod_conta                                                                    
                                        ,nom_conta
                                        ,cod_funcao
                                        ,nom_funcao
                                        ,cod_subfuncao
                                        ,nom_subfuncao
                                        ,cod_programa 
                                        ,nom_programa
                                        
                                FROM (                                                                                                                                  
                                        SELECT
                                                op.cod_ordem                                                                                                                
                                                ,op.exercicio                                                                                                                
                                                ,op.cod_entidade                                                                                                             
                                                ,to_char( op.dt_emissao   , ''dd/mm/yyyy'' ) as dt_emissao                                                                     
                                                ,to_char( op.dt_vencimento, ''dd/mm/yyyy'' ) as dt_vencimento                                                                  
                                                ,op.observacao                                                                                                               
                                                ,cgm.nom_cgm as entidade                                                                                                     
                                                ,1 as num_exercicio_empenho                                                                                                  
                                                ,em.exercicio as exercicio_empenho                                                                                           
                                                ,rec.masc_recurso_red                                                                                                              
                                                ,rec.cod_recurso                                                                                                              
                                                ,rec.cod_detalhamento                                                                                                            
                                                ,pe.cgm_beneficiario                                                                                                         
                                                ,pe.implantado                                                                                                               
                                                ,cgm_pe.nom_cgm as beneficiario                                                                                              
                                                ,coalesce(nota_liq_paga.vl_pago, 0.00) as valor_pagamento                                                           
                                                ,coalesce(nota_liq_paga.vl_anulado, 0.00 ) as valor_anulada                                                              
                                                ,coalesce(nota_liq_paga.saldo_pagamento, 0.00) as saldo_pagamento                                                       
                                                ,nota_liq_paga.cod_nota                                                                                                      
                                                ,empenho.retorna_notas_empenhos(op.exercicio,op.cod_ordem, op.cod_entidade) as nota_empenho                                  
                                                ,sum(coalesce(tot_op.total_op,0.00)) as vl_nota                                                                                   
                                                ,sum(coalesce(tot_op.anulacoes_op,0.00)) as vl_nota_anulacoes                                                                     
                                                ,sum(coalesce(tot_op.vl_original_op,0.00)) as vl_nota_original
                                                ,nota_liq_paga.cod_conta                                                                    
                                                ,nota_liq_paga.nom_conta
                                                ,de.cod_funcao
                                                ,funcao.descricao as nom_funcao
                                                ,de.cod_subfuncao
                                                ,subfuncao.descricao as nom_subfuncao                       
                                                ,de.cod_programa
                                                ,programa.descricao as nom_programa
                                                
                                        FROM empenho.ordem_pagamento as op
                                        
                                   LEFT JOIN empenho.ordem_pagamento_anulada AS opa
                                          ON op.cod_ordem    = opa.cod_ordem                                                                                        
                                         AND op.exercicio    = opa.exercicio                                                                                        
                                         AND op.cod_entidade = opa.cod_entidade
                                         AND op.exercicio    = '|| quote_literal(stExercicio) ||'
                                           
                                        JOIN empenho.pagamento_liquidacao AS pl
                                          ON op.cod_ordem    = pl.cod_ordem                                                                                          
                                         AND op.exercicio    = pl.exercicio                                                                                          
                                         AND op.cod_entidade = pl.cod_entidade                                                                                       
                                         AND op.exercicio    = '|| quote_literal(stExercicio) ||'
                                         
                                        JOIN (
                                                SELECT
                                                        coalesce(sum(pl.vl_pagamento),0.00) - coalesce(opla.vl_anulado,0.00) AS total_op    
                                                        ,coalesce(opla.vl_anulado,0.00) AS anulacoes_op                                      
                                                        ,coalesce(sum(pl.vl_pagamento),0.00) AS vl_original_op                                      
                                                        ,pl.cod_ordem                                                                  
                                                        ,pl.cod_entidade                                                               
                                                        ,pl.exercicio
                                                        
                                                FROM empenho.pagamento_liquidacao AS pl
                                                
                                           LEFT JOIN ( 
                                                        SELECT
                                                                opla.cod_ordem 
                                                                ,opla.cod_entidade 
                                                                ,opla.exercicio 
                                                                ,opla.exercicio_liquidacao 
                                                                ,opla.cod_nota 
                                                                ,coalesce( sum(opla.vl_anulado), 0.00 ) AS vl_anulado
                                                                
                                                        FROM empenho.ordem_pagamento_liquidacao_anulada as opla
                                                        
                                                    GROUP BY opla.cod_ordem 
                                                            ,opla.cod_entidade 
                                                            ,opla.exercicio 
                                                            ,opla.cod_nota 
                                                            ,opla.exercicio_liquidacao 
                                                    ) AS opla 
                                                  ON opla.cod_ordem            = pl.cod_ordem 
                                                 AND opla.cod_entidade         = pl.cod_entidade 
                                                 AND opla.exercicio            = pl.exercicio 
                                                 AND opla.exercicio_liquidacao = pl.exercicio_liquidacao 
                                                 AND opla.cod_nota             = pl.cod_nota
                                                 
                                               WHERE pl.cod_ordem is not null                                                                   
                                                 AND pl.exercicio = '|| quote_literal(stExercicio) ||'
                                                 
                                            GROUP BY pl.cod_ordem                                                                        
                                                    ,pl.cod_entidade                                                                     
                                                    ,pl.exercicio                                                                        
                                                    ,opla.vl_anulado
                                                    
                                            ) AS tot_op
                                          ON tot_op.cod_ordem    = pl.cod_ordem                                                                                      
                                         AND tot_op.exercicio    = pl.exercicio                                                                                      
                                         AND tot_op.cod_entidade = pl.cod_entidade
                                         
                                        JOIN empenho.nota_liquidacao as nl
                                          ON pl.cod_nota              = nl.cod_nota                                             
                                         AND pl.cod_entidade          = nl.cod_entidade                                         
                                         AND pl.exercicio_liquidacao  = nl.exercicio
                                         
                                   LEFT JOIN (                                                                                                                    
                                                SELECT  nlp.cod_entidade                                                                                           
                                                        ,nlp.cod_nota                                                                                               
                                                        ,plnlp.cod_ordem                                                                                            
                                                        ,plnlp.exercicio                                                                                            
                                                        ,nlp.exercicio as exercicio_liquidacao                                                                      
                                                        ,sum(coalesce(nlp.vl_pago ,0.00)) as vl_pago                                                            
                                                        ,sum(coalesce(nlpa.vl_anulado ,0.00)) as vl_anulado                                                         
                                                        ,(SUM(nlp.vl_pago) - coalesce(nlpa.vl_anulado,0.00)) as saldo_pagamento
                                                        ,plano_conta.cod_conta
                                                        ,plano_conta.nom_conta
                                                        
                                                FROM empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                                                
                                                JOIN empenho.nota_liquidacao_paga as nlp
                                                  ON nlp.cod_entidade = plnlp.cod_entidade                                                                    
                                                 AND nlp.cod_nota     = plnlp.cod_nota                                                                        
                                                 AND nlp.exercicio    = plnlp.exercicio_liquidacao                                                            
                                                 AND nlp.timestamp    = plnlp.timestamp
                                                 
                                                JOIN empenho.nota_liquidacao_conta_pagadora 
                                                  ON nota_liquidacao_conta_pagadora.cod_entidade         = nlp.cod_entidade
                                                 AND nota_liquidacao_conta_pagadora.cod_nota             = nlp.cod_nota
                                                 AND nota_liquidacao_conta_pagadora.exercicio_liquidacao = nlp.exercicio
                                                 AND nota_liquidacao_conta_pagadora.timestamp            = nlp.timestamp
                                                 
                                                JOIN contabilidade.plano_analitica
                                                  ON plano_analitica.cod_plano   = nota_liquidacao_conta_pagadora.cod_plano
                                                 AND plano_analitica.exercicio   = nota_liquidacao_conta_pagadora.exercicio
                                                 
                                                JOIN contabilidade.plano_conta 
                                                  ON plano_conta.exercicio   = plano_analitica.exercicio
                                                 AND plano_conta.cod_conta   = plano_analitica.cod_conta
                                                 
                                           LEFT JOIN (                                                                                       
                                                        SELECT
                                                                exercicio                                                                  
                                                                ,cod_nota                                                                   
                                                                ,cod_entidade                                                               
                                                                ,timestamp                                                                  
                                                                ,coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado
                                                                
                                                        FROM empenho.nota_liquidacao_paga_anulada as nlpa
                                                        
                                                    GROUP BY exercicio, cod_nota, cod_entidade, timestamp                             
                                                    ) AS nlpa
                                                  ON nlp.exercicio    = nlpa.exercicio                                              
                                                 AND nlp.cod_nota     = nlpa.cod_nota             
                                                 AND nlp.cod_entidade = nlpa.cod_entidade         
                                                 AND nlp.timestamp    = nlpa.timestamp
                                                 
                                               WHERE plnlp.exercicio = '|| quote_literal(stExercicio) ||'
                                               
                                            GROUP BY nlp.cod_entidade                                                                                         
                                                    ,nlp.cod_nota                                                                                             
                                                    ,nlp.exercicio                                                                                            
                                                    ,nlpa.vl_anulado                                                                                          
                                                    ,plnlp.cod_ordem                                                                                          
                                                    ,plnlp.exercicio
                                                    ,plano_conta.cod_conta
                                                    ,plano_conta.nom_conta
                                                    
                                            ) AS nota_liq_paga
                                          ON pl.cod_nota             = nota_liq_paga.cod_nota                                                 
                                         AND pl.cod_entidade         = nota_liq_paga.cod_entidade                                             
                                         AND pl.exercicio            = nota_liq_paga.exercicio                                                
                                         AND pl.cod_ordem            = nota_liq_paga.cod_ordem                                                
                                         AND pl.exercicio_liquidacao = nota_liq_paga.exercicio_liquidacao
                                         
                                        JOIN empenho.empenho AS em 
                                          ON nl.cod_empenho       = em.cod_empenho                                                        
                                         AND nl.exercicio_empenho = em.exercicio                                                          
                                         AND nl.cod_entidade      = em.cod_entidade                                                       
                                         AND em.exercicio         = '|| quote_literal(stExercicio) ||'
                                         
                                        JOIN empenho.pre_empenho as pe 
                                          ON em.exercicio       = pe.exercicio                                                    
                                         AND em.cod_pre_empenho = pe.cod_pre_empenho                                              
                                         AND em.exercicio       = '|| quote_literal(stExercicio) ||'
                                         
                                        JOIN sw_cgm as cgm_pe 
                                          ON pe.cgm_beneficiario = cgm_pe.numcgm
                                          
                                   LEFT JOIN empenho.pre_empenho_despesa as ped 
                                          ON pe.cod_pre_empenho = ped.cod_pre_empenho                                  
                                         AND pe.exercicio       = ped.exercicio
                                         
                                        JOIN orcamento.despesa as de 
                                          ON ped.cod_despesa = de.cod_despesa                                                     
                                         AND ped.exercicio   = de.exercicio
                                         
                                        JOIN orcamento.funcao
                                          ON funcao.exercicio  = de.exercicio
                                         AND funcao.cod_funcao = de.cod_funcao
                                         
                                        JOIN orcamento.subfuncao
                                          ON subfuncao.exercicio     = de.exercicio
                                         AND subfuncao.cod_subfuncao = de.cod_subfuncao
                                         
                                        JOIN orcamento.programa
                                          ON programa.exercicio    = de.exercicio
                                         AND programa.cod_programa = de.cod_programa
                                         
                                        JOIN orcamento.recurso('|| quote_literal(stExercicio) ||') as rec                                                                                              
                                          ON de.cod_recurso = rec.cod_recurso                                                                                 
                                         AND de.exercicio   = rec.exercicio
                                         
                                        JOIN orcamento.entidade as en 
                                          ON op.cod_entidade = en.cod_entidade                                                        
                                         AND op.exercicio    = en.exercicio
                                         
                                        JOIN sw_cgm as cgm 
                                         ON en.numcgm = cgm.numcgm
                                         
                                    GROUP BY op.cod_ordem                                                                                                                    
                                            ,op.exercicio                                                                                                                    
                                            ,op.cod_entidade                                                                                                                 
                                            ,to_char( op.dt_emissao   , ''dd/mm/yyyy'' )                                                                                       
                                            ,to_char( op.dt_vencimento, ''dd/mm/yyyy'' )                                                                                       
                                            ,op.observacao                                                                                                                   
                                            ,cgm.nom_cgm                                                                                                                     
                                            ,num_exercicio_empenho                                                                                                           
                                            ,em.exercicio                                                                                                                    
                                            ,de.cod_recurso                                                                                                                  
                                            ,rec.masc_recurso_red                                                                                                                 
                                            ,rec.cod_recurso                                                                                                                 
                                            ,rec.cod_detalhamento                                                                                                                  
                                            ,pe.cgm_beneficiario                                                                                                             
                                            ,pe.implantado                                                                                                                   
                                            ,cgm_pe.nom_cgm                                                                                                                  
                                            ,empenho.retorna_notas_empenhos(op.exercicio,op.cod_ordem, op.cod_entidade)                                                      
                                            ,de.cod_conta                                  
                                            ,pl.exercicio_liquidacao                                                                                                         
                                            ,nota_liq_paga.cod_nota                                                                                                          
                                            ,nota_liq_paga.vl_pago                                                                                                           
                                            ,nota_liq_paga.vl_anulado                                                                                                        
                                            ,nota_liq_paga.saldo_pagamento
                                            ,nota_liq_paga.cod_conta                                                                    
                                            ,nota_liq_paga.nom_conta
                                            ,de.cod_funcao
                                            ,funcao.descricao 
                                            ,de.cod_subfuncao
                                            ,subfuncao.descricao 
                                            ,de.cod_programa
                                            ,programa.descricao                                                                                                   
                                    ) as tabela
                                    
                            GROUP BY                                                                                                                                
                                    cod_ordem                                                                                                                           
                                    ,exercicio                                                                                                                           
                                    ,cod_entidade                                                                                                                        
                                    ,dt_emissao                                                                                                                          
                                    ,dt_vencimento                                                                                                                       
                                    ,observacao                                                                                                                          
                                    ,entidade                                                                                                                            
                                    ,cod_recurso                                                                                                                         
                                    ,masc_recurso_red                                                                                                                         
                                    ,cod_detalhamento                                                                                                                         
                                    ,cgm_beneficiario                                                                                                                    
                                    ,implantado                                                                                                                          
                                    ,beneficiario                                                                                                                        
                                    ,dt_estorno                                                                                                                          
                                    ,nota_empenho                                                                                                                        
                                    ,exercicio_empenho                                                                                                                   
                                    ,vl_nota                                                                                                                             
                                    ,vl_nota_original                                                                                                                    
                                    ,vl_nota_anulacoes
                                    ,cod_conta                                                                    
                                    ,nom_conta
                                    ,cod_funcao
                                    ,nom_funcao
                                    ,cod_subfuncao
                                    ,nom_subfuncao
                                    ,cod_programa 
                                    ,nom_programa
                            ) as tbl
                            
                        WHERE num_exercicio_empenho > 0                                                                                                          
                          AND tbl.exercicio = '|| quote_literal(stExercicio) ||'
                          AND cod_entidade in ('|| stCodEntidades || ') 
                          AND  cod_recurso IN (118,119) 
                          --AND situacao = ''Paga''
                          AND to_date(dt_emissao,''dd/mm/yyyy'') BETWEEN to_date('''||stDtInicial||''',''dd/mm/yyyy'')
                                                                     AND to_date('''||stDtFinal||''',''dd/mm/yyyy'')
                          AND cod_conta IN ('|| stCodConta ||')
                          
                    GROUP BY cod_funcao
                            ,nom_funcao
                            ,cod_subfuncao
                            ,nom_subfuncao
                            ,cod_programa 
                            ,nom_programa
                            ,dt_emissao
                    ) as retorno
                    
            GROUP BY cod_funcao
                    ,nom_funcao
                    ,cod_subfuncao
                    ,nom_subfuncao
                    ,cod_programa 
                    ,nom_programa
                    
            ORDER BY cod_funcao, cod_subfuncao, cod_programa DESC
            )
    ';

    EXECUTE stSql;
        
        stSql := '
                CREATE TEMPORARY TABLE tmp_resultado AS 
                SELECT 
                          nivel
                        , cod_funcao
                        , cod_subfuncao
                        , cod_programa                        
                        , descricao
                        , SUM(valor_pagamento) as valor_pagamento
                FROM(

                        SELECT 
                              1 as nivel
                            , cod_funcao
                            , 0 as cod_subfuncao
                            , 0 as cod_programa                            
                            , nom_funcao as descricao
                            , valor_pagamento    
                        FROM tmp_despesas_educacao

                    UNION

                        SELECT 
                              2 as nivel
                            , cod_funcao
                            , cod_subfuncao
                            , 0 as cod_programa                            
                            , nom_subfuncao as descricao
                            , valor_pagamento    
                            FROM tmp_despesas_educacao

                    UNION

                        SELECT
                              3 as nivel  
                            , cod_funcao
                            , cod_subfuncao
                            , cod_programa                            
                            , '''' as descricao
                            , 0.00 as valor_pagamento    
                            FROM tmp_despesas_educacao
                    UNION

                        SELECT 
                              4 as nivel 
                            , cod_funcao
                            , cod_subfuncao
                            , cod_programa                                                        
                            , nom_programa as descricao
                            , valor_pagamento    
                            FROM tmp_despesas_educacao
                )as retorno
                GROUP BY
                          nivel
                        , cod_funcao
                        , cod_subfuncao
                        , cod_programa                        
                        , descricao
                        
                ORDER BY cod_funcao, cod_subfuncao, cod_programa ASC
    ';
    EXECUTE stSql;

    stSql := '  SELECT 
                        * 
                FROM tmp_resultado
                ORDER BY cod_funcao
                        , cod_subfuncao
                        , cod_programa 
                        , descricao DESC';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
        --DROP TABLE tmp_despesas_educacao;
        --DROP TABLE tmp_resultado;
        
    RETURN;
 
END;
$$
language 'plpgsql';

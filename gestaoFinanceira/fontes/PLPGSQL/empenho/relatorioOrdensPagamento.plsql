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
* $Id: relatorioOrdensPagamento.plsql 61039 2014-12-02 16:07:14Z michel $
*
* Casos de uso: uc-02.03.12
*/

CREATE OR REPLACE FUNCTION empenho.fn_relatorio_ordens_pagamento( inCodOrdemInicial   VARCHAR
                                                                 ,inCodOrdemFinal     VARCHAR
                                                                 ,inCodEmpenhoInicial VARCHAR
                                                                 ,inCodEmpenhoFinal   VARCHAR
                                                                 ,stExercicioOP       VARCHAR
                                                                 ,stEntidade          VARCHAR
                                                                 ,inCodRecurso        VARCHAR
                                                                 ,stDestinacaoRecurso VARCHAR
                                                                 ,inCodDetalhamento   VARCHAR
                                                                 ,stDtInicial         VARCHAR   
                                                                 ,stDtFinal           VARCHAR
                                                                 ,stDtInicialPG       VARCHAR
                                                                 ,stDtFinalPG         VARCHAR
                                                                 ,inNumCGM            VARCHAR
                                                                 ,stSituacao          VARCHAR
                                                                 ,stTipo              VARCHAR
                                                                 ,stExercicioEmpenho  VARCHAR
                                                                ) RETURNS SETOF RECORD AS $$

DECLARE

reRegistro  RECORD;
stSql       VARCHAR := '';
inCount     INTEGER := 0;
nuValorPago NUMERIC := 0;
inCodOrdem  INTEGER := 0;
nuSaldoOP   NUMERIC := 0;
BEGIN

stSql := '

    CREATE TEMPORARY TABLE tmp_ordens AS (

        SELECT eop.cod_ordem                                                                                                                  
              ,epl.cod_nota
              ,TO_CHAR(eop.dt_emissao,''dd/mm/yyyy'') AS data_emissao                                                                           
              ,pagamento_liquidacao.valor_op AS valor_op 
              ,COALESCE(ordem_pagamento_anulada.valor_anulado,0.00) AS valor_anulado
              ,COALESCE(nota_liquidacao_paga.valor_pago,0.00) AS valor_pago
              ,(pagamento_liquidacao.valor_op-COALESCE(ordem_pagamento_anulada.valor_anulado,0.00))-(COALESCE(nota_liquidacao_paga.valor_pago,0.00)-COALESCE(nota_liquidacao_paga.valor_estornado, 0.00)) AS saldo_op 
              ,CASE WHEN (pagamento_liquidacao.valor_op-COALESCE(ordem_pagamento_anulada.valor_anulado,0.00)) = 0
                        THEN ''Anulada''::VARCHAR
                    WHEN (pagamento_liquidacao.valor_op-COALESCE(ordem_pagamento_anulada.valor_anulado,0.00))-(COALESCE(nota_liquidacao_paga.valor_pago,0.00)-COALESCE(nota_liquidacao_paga.valor_estornado, 0.00)) > 0
                        THEN ''A Pagar''::VARCHAR
                        ELSE ''Paga''::VARCHAR	
               END AS situacao
              ,nota_liquidacao.cod_empenho||''/''||nota_liquidacao.exercicio_empenho as cod_empenho
              ,TO_CHAR(nota_liquidacao.dt_empenho,''dd/mm/yyyy'') AS dt_empenho
              ,nota_liquidacao.credor
              ,TO_CHAR(nota_liquidacao_paga.dt_pagamento,''dd/mm/yyyy'') AS dt_pagamento
              ,TO_CHAR(ordem_pagamento_anulada.dt_anulado,''dd/mm/yyyy'') AS dt_anulado
              ,COALESCE(nota_liquidacao_paga.valor_estornado, 0.00) AS vl_estornado
              ,array_to_string(NF_MG.nota_fiscal_mg, '', '') AS nota_fiscal_mg
              
          FROM empenho.ordem_pagamento as eop
                                                                                                                                  
          JOIN ( SELECT exercicio 
                       ,cod_entidade
                       ,cod_ordem                                                                                             
                       ,COALESCE(SUM(vl_pagamento),0)              AS valor_op                                                                                        
                  FROM empenho.pagamento_liquidacao                                                                                                                       
                 GROUP BY exercicio 
                         ,cod_entidade
                         ,cod_ordem                                              
              ) AS pagamento_liquidacao ON pagamento_liquidacao.exercicio    = eop.exercicio
                                       AND pagamento_liquidacao.cod_entidade = eop.cod_entidade
                                       AND pagamento_liquidacao.cod_ordem    = eop.cod_ordem                                                                                                                                    

          JOIN empenho.pagamento_liquidacao as epl ON epl.exercicio       = eop.exercicio
                                                  AND epl.cod_entidade    = eop.cod_entidade
                                                  AND epl.cod_ordem       = eop.cod_ordem      
                                                    
             -- Valor Anulado';
                IF stSituacao = '3' THEN
                    stSql := stSql || '
                       INNER JOIN ( SELECT ordem_pagamento_liquidacao_anulada.exercicio 
                               ,ordem_pagamento_liquidacao_anulada.cod_entidade                                           
                               ,ordem_pagamento_liquidacao_anulada.cod_ordem                                                                                             
                               ,COALESCE(SUM(ordem_pagamento_liquidacao_anulada.vl_anulado),0) AS valor_anulado
                               ,MAX(ordem_pagamento_liquidacao_anulada.timestamp)              AS dt_anulado
                       FROM empenho.ordem_pagamento_liquidacao_anulada      
                       WHERE to_date(to_char(ordem_pagamento_liquidacao_anulada.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date('''|| stDtInicial ||''',''dd/mm/yyyy'') 
                         AND to_date(to_char(ordem_pagamento_liquidacao_anulada.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date(''' || stDtFinal   ||''',''dd/mm/yyyy'')                 
                    ';
                ELSE
                    stSql := stSql || '
                       LEFT JOIN ( SELECT ordem_pagamento_liquidacao_anulada.exercicio 
                               ,ordem_pagamento_liquidacao_anulada.cod_entidade                                           
                               ,ordem_pagamento_liquidacao_anulada.cod_ordem                                                                                             
                               ,COALESCE(SUM(ordem_pagamento_liquidacao_anulada.vl_anulado),0) AS valor_anulado
                               ,MAX(ordem_pagamento_liquidacao_anulada.timestamp)              AS dt_anulado
                       FROM empenho.ordem_pagamento_liquidacao_anulada
                    ';
                END IF;
                
                
                stSql := stSql || '                         
                       GROUP BY  ordem_pagamento_liquidacao_anulada.exercicio
                                ,ordem_pagamento_liquidacao_anulada.cod_entidade
                                ,ordem_pagamento_liquidacao_anulada.cod_ordem
                       ) AS ordem_pagamento_anulada ON ordem_pagamento_anulada.exercicio    = eop.exercicio                                        
                                                   AND ordem_pagamento_anulada.cod_entidade = eop.cod_entidade                                     
                                                   AND ordem_pagamento_anulada.cod_ordem    = eop.cod_ordem                                        
          -- Valor pagamento
          ';
          IF stSituacao = '2' THEN
            stSql := stSql || 'INNER ';
          ELSE
            stSql := stSql || 'LEFT ';
          END IF;
          
          stSql := stSql || 'JOIN ( SELECT pagamento_liquidacao_nota_liquidacao_paga.exercicio                                                                                      
                            ,pagamento_liquidacao_nota_liquidacao_paga.cod_entidade                                                                                   
                            ,pagamento_liquidacao_nota_liquidacao_paga.cod_ordem                                                                                      
                            ,pagamento_liquidacao_nota_liquidacao_paga.cod_nota                                                                                      
                            ,TO_DATE(TO_CHAR(nota_liquidacao_paga.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') AS dt_pagamento                                             
                            ,SUM(empenho.nota_liquidacao_paga.vl_pago) AS valor_pago
                            ,COALESCE(SUM(nota_liquidacao_paga_anulada.vl_anulado),0.00)  AS valor_estornado

                      FROM  empenho.pagamento_liquidacao_nota_liquidacao_paga                                                                                        
                           ,empenho.nota_liquidacao_paga                                                                                                             
                           LEFT JOIN ( SELECT nota_liquidacao_paga_anulada.exercicio                                                                                
                                             ,nota_liquidacao_paga_anulada.cod_entidade                                                                             
                                             ,nota_liquidacao_paga_anulada.cod_nota                                                                                 
                                             ,nota_liquidacao_paga_anulada.timestamp                                                                                
                                             ,SUM(nota_liquidacao_paga_anulada.vl_anulado) AS vl_anulado                                                             
                                       FROM empenho.nota_liquidacao_paga_anulada                                                                                  
                                       GROUP BY nota_liquidacao_paga_anulada.exercicio                                                                           
                                               ,nota_liquidacao_paga_anulada.cod_entidade                                                                          
                                               ,nota_liquidacao_paga_anulada.cod_nota                                                                              
                                               ,nota_liquidacao_paga_anulada.timestamp                                                                             
                            ) AS nota_liquidacao_paga_anulada  ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio             
                                                              AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade          
                                                              AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota              
                                                              AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp             

                     WHERE pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = nota_liquidacao_paga.cod_entidade                                       
                       AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = nota_liquidacao_paga.cod_nota                                            
                       AND pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio                                           
                       AND pagamento_liquidacao_nota_liquidacao_paga.timestamp            = nota_liquidacao_paga.timestamp                                           
                 ';                       
            
                 stSql := stSql || '                 
                       AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade IN ( '|| stEntidade ||' )
                  GROUP BY pagamento_liquidacao_nota_liquidacao_paga.exercicio                                                                                      
                          ,pagamento_liquidacao_nota_liquidacao_paga.cod_entidade                                                                                   
                          ,pagamento_liquidacao_nota_liquidacao_paga.cod_ordem                                                                                      
                          ,pagamento_liquidacao_nota_liquidacao_paga.cod_nota                                                                                      
                          ,TO_CHAR(nota_liquidacao_paga.timestamp,''dd/mm/yyyy'')                                                        
        ) AS nota_liquidacao_paga ON nota_liquidacao_paga.exercicio    = epl.exercicio                                                   
                                 AND nota_liquidacao_paga.cod_entidade = epl.cod_entidade                                                
                                 AND nota_liquidacao_paga.cod_ordem    = epl.cod_ordem                                                   
                                 AND nota_liquidacao_paga.cod_nota     = epl.cod_nota                                                   

        -- Informaçoes do Empenho                                                                                                                                     
        JOIN ( SELECT pagamento_liquidacao.exercicio    
                  ,pagamento_liquidacao.cod_entidade
                  ,pagamento_liquidacao.cod_ordem                                              
                  ,nota_liquidacao.cod_nota
                  ,nota_liquidacao.exercicio_empenho 
                  ,empenho.cod_empenho
                  ,empenho.dt_empenho                                                                        
                  ,(SELECT nom_cgm FROM sw_cgm WHERE pre_empenho.cgm_beneficiario = sw_cgm.numcgm) AS credor                                                          
                  ,CASE WHEN pre_empenho.implantado THEN restos_pre_empenho.recurso ELSE pre_empenho_despesa.cod_recurso END AS cod_recurso                           
                  ,pre_empenho.cgm_beneficiario  AS cgm_credor                                                                                                        

              FROM empenho.pagamento_liquidacao                                                                                                                      
        INNER JOIN empenho.nota_liquidacao
                ON pagamento_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio
               AND pagamento_liquidacao.cod_entidade         = nota_liquidacao.cod_entidade
               AND pagamento_liquidacao.cod_nota             = nota_liquidacao.cod_nota
        INNER JOIN empenho.empenho
                ON nota_liquidacao.cod_empenho       = empenho.cod_empenho
               AND nota_liquidacao.exercicio_empenho = empenho.exercicio
               AND nota_liquidacao.cod_entidade      = empenho.cod_entidade
        INNER JOIN empenho.pre_empenho
                ON empenho.exercicio       = pre_empenho.exercicio
               AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
         LEFT JOIN empenho.restos_pre_empenho 
                ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
               AND restos_pre_empenho.exercicio       = pre_empenho.exercicio 
         LEFT JOIN (   SELECT pre_empenho_despesa.exercicio
                            , pre_empenho_despesa.cod_pre_empenho
                            , despesa.cod_recurso                                          
                            , recurso.masc_recurso_red
                            , recurso.cod_detalhamento
                         FROM empenho.pre_empenho_despesa                                                                                                     
                            , orcamento.despesa
			        LEFT JOIN (  SELECT recurso.*
                                      , CASE WHEN ( SELECT valor
                                                      FROM administracao.configuracao
                                                     WHERE cod_modulo = 8
                                                       AND parametro = ''recurso_destinacao''
                                                       AND exercicio = recurso.exercicio
                                                  ) !=''false'' THEN substr(recurso.cod_fonte,0,7)
                                        ELSE
                                            sw_fn_mascara_dinamica( ( SELECT valor
                                                                        FROM administracao.configuracao
                                                                       WHERE cod_modulo = 8
                                                                         AND parametro=''masc_recurso''
                                                                         AND exercicio=recurso.exercicio
                                                                    ), recurso.cod_recurso::varchar)
                                        END AS masc_recurso_red
                                      , CASE WHEN ( SELECT valor
                                                      FROM administracao.configuracao
                                                     WHERE cod_modulo = 8
                                                       AND parametro = ''recurso_destinacao''
                                                       AND exercicio = recurso.exercicio
                                                  ) !=''false'' THEN recurso_destinacao.cod_detalhamento
                                        END AS cod_detalhamento
                                   FROM orcamento.recurso
                              LEFT JOIN orcamento.recurso_destinacao
                                     ON recurso.exercicio   = recurso_destinacao.exercicio
                                    AND recurso.cod_recurso = recurso_destinacao.cod_recurso
                              ) AS recurso
			               ON recurso.cod_recurso = despesa.cod_recurso
			              AND recurso.exercicio   = despesa.exercicio
                        WHERE pre_empenho_despesa.cod_despesa = despesa.cod_despesa                                                                           
                          AND pre_empenho_despesa.exercicio   = despesa.exercicio                                                                           
          ';

        IF inCodRecurso != '' THEN
            stSql := stSql || ' AND despesa.cod_recurso = '|| inCodRecurso ||' ';     
        END IF;
        
        stSql := stSql || '  
                           ) AS  pre_empenho_despesa 
                        ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio                                                 
                       AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                                                      
             WHERE pagamento_liquidacao.cod_entidade IN('|| stEntidade ||')
          ';
        IF inNumCGM != '' THEN
          stSql := stSql || '              
               AND pre_empenho.cgm_beneficiario = '|| inNumCGM ||'                 
          ';
        END IF;
        
        IF stDestinacaoRecurso != '' THEN
          stSql := stSql || '              
               AND pre_empenho_despesa.masc_recurso_red like '''|| stDestinacaoRecurso ||'''||''%''
          ';     
        END IF;

        IF inCodDetalhamento != '' THEN
          stSql := stSql || '              
               AND pre_empenho_despesa.cod_detalhamento = '|| inCodDetalhamento ||'
          ';     
        END IF;

        IF inCodEmpenhoInicial != '' THEN
          stSql := stSql || '                      
               AND  empenho.cod_empenho >= '|| inCodEmpenhoInicial ||' 
          ';     
        END IF;

        IF inCodEmpenhoFinal != '' THEN 
          stSql := stSql || '                      
              AND  empenho.cod_empenho <= '|| inCodEmpenhoFinal ||'
          ';    
        END IF;
        
        
        IF stExercicioEmpenho != '' THEN 
          stSql := stSql || '
              AND empenho.exercicio = '''|| stExercicioEmpenho ||'''
          ';    
        END IF;
        
          stSql := stSql || '          
           ) AS nota_liquidacao ON nota_liquidacao.exercicio     = eop.exercicio
                               AND nota_liquidacao.cod_entidade  = eop.cod_entidade
                               AND nota_liquidacao.cod_ordem     = eop.cod_ordem
                               AND nota_liquidacao.cod_nota      = epl.cod_nota
                               
         LEFT JOIN (   SELECT NFL_MG.cod_nota_liquidacao
                            , NFL_MG.exercicio_liquidacao
                            , NFL_MG.cod_entidade
                            , NFL_MG.vl_liquidacao
                            , SUM(NFL_MG.vl_associado) AS vl_associado
                            , ARRAY_AGG(NF_MG.nro_nota||'''') AS nota_fiscal_mg
                         FROM tcemg.nota_fiscal_empenho_liquidacao AS NFL_MG
                         JOIN tcemg.nota_fiscal AS NF_MG
                           ON NF_MG.exercicio   = NFL_MG.exercicio
                          AND NF_MG.cod_entidade    = NFL_MG.cod_entidade
                          AND NF_MG.cod_nota        = NFL_MG.cod_nota
                         GROUP BY NFL_MG.cod_nota_liquidacao
                            , NFL_MG.exercicio_liquidacao
                            , NFL_MG.cod_entidade
                            , NFL_MG.vl_liquidacao
                   ) AS NF_MG
                ON NF_MG.exercicio_liquidacao   = nota_liquidacao.exercicio
               AND NF_MG.cod_entidade           = nota_liquidacao.cod_entidade
               AND NF_MG.cod_nota_liquidacao    = nota_liquidacao.cod_nota
          
        WHERE eop.cod_entidade IN ( ' || stEntidade || ' ) 
          
        ';                                                                                                                                                          
    IF stExercicioOP != '' THEN
        stSql := stSql || '
            AND eop.exercicio = ''' || stExercicioOP || '''
        ';
    END IF;


    IF inCodOrdemInicial != '' THEN 
        stSql := stSql || ' 
          AND eop.cod_ordem >= ' || inCodOrdemInicial || '
        ';  
    END IF;

    IF inCodOrdemFinal != '' THEN 
        stSql := stSql || '     
          AND eop.cod_ordem <= ' || inCodOrdemFinal || '
        '; 
    END IF;

    IF( inCodOrdemInicial = inCodOrdemFinal AND inCodOrdemInicial != '' ) THEN
        stSql := stSql || '         
          AND eop.cod_ordem = ' || inCodOrdemInicial || '
        ';  
    END IF;   
    
    IF stSituacao <> '3' THEN
        stSql := stSql || '             
              AND to_date(to_char(eop.dt_emissao,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date(''' || stDtInicial ||''',''dd/mm/yyyy'')
              AND to_date(to_char(eop.dt_emissao,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date(''' || stDtFinal   ||''',''dd/mm/yyyy'')
        ';
    END IF;
    
    IF stDtInicialPG <> '' AND stDtFinalPG <> '' THEN
    	stSql := stSql || '    
                  AND  to_date(to_char(nota_liquidacao_paga.dt_pagamento,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date(''' || stDtInicialPG ||''',''dd/mm/yyyy'')    
                  AND  to_date(to_char(nota_liquidacao_paga.dt_pagamento,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date(''' || stDtFinalPG   ||''',''dd/mm/yyyy'')      
            ';                       
    END IF;

      stSql := stSql || '                                                                                
)
';  

EXECUTE stSql;

   stSql := ' SELECT  
                      cod_ordem
                     ,data_emissao
                     ,valor_op             AS valor
                     ,valor_pago 
                     ,valor_anulado
                     ,saldo_op    
                     ,situacao                         
                     ,dt_pagamento
                     ,dt_anulado  
                     ,cod_empenho  
                     ,dt_empenho  
                     ,credor      
                     ,vl_estornado
                     ,nota_fiscal_mg
               FROM tmp_ordens 
               WHERE 1 = 1
               ';   

        -- A Pagar                        
        IF stSituacao = '1' THEN
            stSql := stSql || '                                                                                                                                         
                    AND situacao = ''A Pagar''
            ';         
        END IF;
        
        -- Pagas              
        IF stSituacao = '2' THEN
            stSql := stSql || '
                    AND ( situacao = ''Paga'' OR dt_pagamento IS NOT NULL )
            ';         
       END IF;

       -- Anulada
       IF stSituacao = '3' THEN
            stSql := stSql || '
                    AND situacao = ''Anulada''
          ';  
       END IF;


stSql := stSql || '   
            ORDER BY cod_ordem ';


FOR reRegistro IN
    EXECUTE stSql
    LOOP
                
    RETURN next reRegistro;
    END LOOP;
    
DROP TABLE tmp_ordens;

END;

$$ language 'plpgsql';

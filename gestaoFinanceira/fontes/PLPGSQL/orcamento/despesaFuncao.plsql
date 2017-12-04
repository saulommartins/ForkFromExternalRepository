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
/**
 * Arquivo que busca os dados do relatório
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
 */

CREATE OR REPLACE FUNCTION orcamento.despesaFuncao (VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stDemonstrarDespesa ALIAS FOR $5;
    stSql               VARCHAR   := '';
    reReg               RECORD;
BEGIN

    IF (stDemonstrarDespesa = 'E') THEN
    stSql := '
    SELECT    CAST(OFU.cod_funcao as VARCHAR)
             ,OFU.descricao                                           
           -- EMPENHADO                                               
           ,sum( coalesce( EIPE.vl_total       , 0.00 ) ) -           
            sum( coalesce( EEA.vl_total_anulado, 0.00 ) ) AS vl_total 
           FROM orcamento.funcao        AS OFU                        
           -- Join com orcamento.despesa                              
           LEFT JOIN orcamento.despesa  AS OD                         
           ON( OFU.exercicio  = OD.exercicio                          
           AND OFU.cod_funcao = OD.cod_funcao )                       
           -- Join com empenho.pre_empenho_despesa                    
           LEFT JOIN empenho.pre_empenho_despesa AS EPED              
           ON( OD.cod_despesa = EPED.cod_despesa                      
           AND OD.exercicio   = EPED.exercicio  )                     
           -- Join com empenho.pre_empenho                            
           LEFT JOIN empenho.pre_empenho AS EPE                       
           ON( EPED.cod_pre_empenho = EPE.cod_pre_empenho             
           AND EPED.exercicio       = EPE.exercicio       )           
           -- Join com empenho.empenho                                
           LEFT JOIN empenho.empenho AS EE                            
           ON( EPE.cod_pre_empenho = EE.cod_pre_empenho               
           AND EPE.exercicio       = EE.exercicio         )           
           -- EMPENHADO                                               
           -- Join com empenho.item_pre_empenho                       
           LEFT JOIN ( SELECT sum( coalesce( EIPE.vl_total, 0.00 ) ) as vl_total 
                             ,EIPE.cod_pre_empenho                    
                             ,EIPE.exercicio                          
                       FROM empenho.item_pre_empenho AS EIPE          
                       GROUP BY EIPE.exercicio                        
                               ,EIPE.cod_pre_empenho                  
                       ORDER BY EIPE.exercicio                        
                               ,EIPE.cod_pre_empenho                  
           ) AS EIPE ON( EPE.cod_pre_empenho  = EIPE.cod_pre_empenho  
                     AND EPE.exercicio        = EIPE.exercicio        
                     AND EIPE.cod_pre_empenho = EE.cod_pre_empenho    
                     AND EIPE.exercicio       = EE.exercicio          
                     AND coalesce( EE.dt_empenho, ''' || stDataInicial || ''' )               
                                 BETWEEN TO_DATE( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'' ) 
                                     AND TO_DATE( ''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'' ) 
           )                                                      
           -- Join com empenho.empenho_anulado                    
           LEFT JOIN ( SELECT sum( coalesce( EEAI.vl_anulado, 0.00 ) ) AS vl_total_anulado
                             ,EEA.exercicio                       
                             ,EEA.cod_entidade                    
                             ,EEA.cod_empenho                     
                       FROM empenho.empenho_anulado AS EEA        
                           ,empenho.empenho_anulado_item AS EEAI  
                       WHERE EEA.exercicio    = EEAI.exercicio    
                       AND   EEA.cod_entidade = EEAI.cod_entidade 
                       AND   EEA.cod_empenho  = EEAI.cod_empenho  
                       AND   EEA.timestamp    = EEAI.timestamp    
                       AND coalesce( TO_DATE( EEA.timestamp::text, ''yyyy-mm-dd'' ), TO_DATE( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'' ) ) 
                                                                     BETWEEN TO_DATE( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'' )
                                                                         AND TO_DATE( ''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'' )
                       GROUP BY EEA.exercicio                       
                               ,EEA.cod_entidade                    
                               ,EEA.cod_empenho                     
                       ORDER BY EEA.exercicio                       
                               ,EEA.cod_entidade                    
                               ,EEA.cod_empenho                     
           ) AS EEA ON( EE.exercicio    = EEA.exercicio             
                    AND EE.cod_entidade = EEA.cod_entidade          
                    AND EE.cod_empenho  = EEA.cod_empenho   )       
        WHERE OFU.exercicio = ''' || stExercicio || '''   
        AND   coalesce( OD.cod_entidade, 0 ) IN ( 0,' || stCodEntidades || ' )
        GROUP BY OFU.exercicio   
                ,OFU.cod_funcao  
                ,OFU.descricao   
        ORDER BY OFU.exercicio   
                ,OFU.cod_funcao  
                ,OFU.descricao   ';

    ELSEIF (stDemonstrarDespesa = 'L') THEN
    stSql := '
    SELECT CAST(funcao.cod_funcao as VARCHAR)
         , funcao.descricao 
         , sum( coalesce( nota_liquidacao_item.vl_total                , 0.00 ) ) - 
           sum( coalesce( nota_liquidacao_item_anulado.vl_total_anulado, 0.00 ) ) AS vl_total 
    FROM   orcamento.funcao 
         , orcamento.despesa  
         , empenho.pre_empenho_despesa  
         , empenho.pre_empenho  
         , empenho.empenho  
    LEFT JOIN ( SELECT sum( nota_liquidacao_item.vl_total ) AS vl_total  
                      ,nota_liquidacao.exercicio_empenho  
                      ,nota_liquidacao.cod_empenho  
                      ,nota_liquidacao.cod_entidade 
                FROM  empenho.nota_liquidacao  
                    , empenho.nota_liquidacao_item  
                WHERE nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio 
                  AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota 
                  AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade  
                  AND nota_liquidacao.dt_liquidacao  BETWEEN  TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )
                                                         AND  TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )
                GROUP BY nota_liquidacao.exercicio_empenho  
                        ,nota_liquidacao.cod_empenho 
                        ,nota_liquidacao.cod_entidade  
              ) AS nota_liquidacao_item  
                    ON ( empenho.exercicio    = nota_liquidacao_item.exercicio_empenho  
                   AND   empenho.cod_empenho  = nota_liquidacao_item.cod_empenho  
                   AND   empenho.cod_entidade = nota_liquidacao_item.cod_entidade) 
    LEFT JOIN ( SELECT sum( nota_liquidacao_item_anulado.vl_anulado ) AS vl_total_anulado  
                      ,nota_liquidacao.exercicio_empenho 
                      ,nota_liquidacao.cod_empenho 
                      ,nota_liquidacao.cod_entidade  
                FROM  empenho.nota_liquidacao  
                    , empenho.nota_liquidacao_item_anulado  
                WHERE nota_liquidacao.exercicio    = nota_liquidacao_item_anulado.exercicio 
                  AND nota_liquidacao.cod_nota     = nota_liquidacao_item_anulado.cod_nota 
                  AND nota_liquidacao.cod_entidade = nota_liquidacao_item_anulado.cod_entidade  
                  AND coalesce( TO_DATE( timestamp::text, ''yyyy-mm-dd'' ), TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' ) ) 
                                                            BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )
                                                                AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )
                GROUP BY nota_liquidacao.exercicio_empenho 
                        ,nota_liquidacao.cod_empenho 
                        ,nota_liquidacao.cod_entidade 
              ) AS nota_liquidacao_item_anulado  
                    ON ( empenho.exercicio    = nota_liquidacao_item_anulado.exercicio_empenho 
                   AND   empenho.cod_empenho  = nota_liquidacao_item_anulado.cod_empenho 
                   AND   empenho.cod_entidade = nota_liquidacao_item_anulado.cod_entidade) 
    WHERE funcao.exercicio     = despesa.exercicio 
      AND funcao.cod_funcao    = despesa.cod_funcao  
      AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa  
      AND despesa.exercicio    = pre_empenho_despesa.exercicio  
      AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho 
      AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio  
      AND pre_empenho.cod_pre_empenho         = empenho.cod_pre_empenho  
      AND pre_empenho.exercicio               = empenho.exercicio 
      AND despesa.cod_entidade IN ( ' || stCodEntidades || ' ) 
      AND funcao.exercicio = ''' || stExercicio || ''' 
    GROUP BY funcao.exercicio 
           , funcao.cod_funcao
           , funcao.descricao 
    ORDER BY funcao.exercicio 
           , funcao.cod_funcao
           , funcao.descricao  ';

    ELSEIF (stDemonstrarDespesa = 'P') THEN
    stSql := '
    SELECT CAST(funcao.cod_funcao as VARCHAR)
         , funcao.descricao  
         , sum( COALESCE(nota_liquidacao_paga.vl_total                , 0.00)) -          
           sum( COALESCE(nota_liquidacao_paga_anulada.vl_total_anulado, 0.00)) as vl_total
      FROM orcamento.funcao                
         , orcamento.despesa               
         , empenho.pre_empenho_despesa     
         , empenho.pre_empenho             
         , empenho.empenho                 
         , empenho.nota_liquidacao         
           LEFT JOIN ( SELECT cod_entidade 
                            , exercicio    
                            , cod_nota     
                            , sum( vl_pago ) as vl_total  
                         FROM empenho.nota_liquidacao_paga
                        WHERE COALESCE( TO_DATE( timestamp, ''yyyy-mm-dd'' ), TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' ) )
                                                                    BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )  
                                                                        AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )  
                        GROUP BY exercicio                                                                        
                               , cod_entidade                                                                     
                               , cod_nota                                                                         
                     ) AS nota_liquidacao_paga ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio   
                                              AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                              AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota    
           LEFT JOIN ( SELECT exercicio                                                                           
                            , cod_entidade                                                                        
                            , cod_nota                                                                            
                            , sum( coalesce( vl_anulado, 0.00 ) ) as vl_total_anulado                             
                         FROM empenho.nota_liquidacao_paga_anulada                                                
                        WHERE coalesce( TO_DATE( timestamp_anulada, ''yyyy-mm-dd'' ), TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' ) )
                                                                            BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )
                                                                                AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )
                        GROUP BY exercicio                                                                                         
                               , cod_entidade                                                                                      
                               , cod_nota                                                                                          
                     ) AS nota_liquidacao_paga_anulada ON nota_liquidacao.exercicio    = nota_liquidacao_paga_anulada.exercicio    
                                                      AND nota_liquidacao.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade 
                                                      AND nota_liquidacao.cod_nota     = nota_liquidacao_paga_anulada.cod_nota     
    WHERE funcao.exercicio     = despesa.exercicio                          
      AND funcao.cod_funcao    = despesa.cod_funcao                         
      AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa            
      AND despesa.exercicio    = pre_empenho_despesa.exercicio              
      AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho 
      AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio       
      AND pre_empenho.cod_pre_empenho         = empenho.cod_pre_empenho     
      AND pre_empenho.exercicio               = empenho.exercicio           
      AND empenho.cod_empenho  = nota_liquidacao.cod_empenho                
      AND empenho.exercicio    = nota_liquidacao.exercicio_empenho          
      AND empenho.cod_entidade = nota_liquidacao.cod_entidade               
      AND despesa.cod_entidade IN ( ' || stCodEntidades || ' )   
      AND funcao.exercicio = ''' || stExercicio || '''            
    GROUP BY funcao.exercicio 
           , funcao.cod_funcao
           , funcao.descricao 
    ORDER BY funcao.exercicio 
           , funcao.cod_funcao
           , funcao.descricao '; 
    END IF;


    FOR reReg IN EXECUTE stSql
    LOOP
        RETURN NEXT reReg;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE plpgsql

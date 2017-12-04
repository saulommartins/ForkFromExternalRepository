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

CREATE OR REPLACE FUNCTION tcmgo.despesaCategoriaEconomica (VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stDemonstrarDespesa ALIAS FOR $5;
    stSql               VARCHAR   := '';
    reReg               RECORD;
BEGIN

    stSql := '
    SELECT CAST(tbl.cod_estrutural AS VARCHAR)
          ,CAST(OCD.descricao AS VARCHAR)                        
          ,CAST(sum( tbl.vl_total ) AS NUMERIC(14,2)) as vl_total
    FROM( SELECT substr( OCD.cod_estrutural, 1, 3 ) as cod_estrutural
                ,EE.exercicio  ';
    IF (stDemonstrarDespesa = 'E') THEN
    stSql := stSql || '
               -- EMPENHADO                                              
               ,sum( coalesce( EIPE.vl_total       , 0.00 ) ) -          
                sum( coalesce( EEA.vl_total_anulado, 0.00 ) ) AS vl_total ';
    ELSEIF (stDemonstrarDespesa = 'P') THEN
    stSql := stSql || '
               -- PAGO                                                     
               ,sum( coalesce( ENLP.vl_total         , 0.00 ) ) -          
                sum( coalesce( ENLPA.vl_total_anulado, 0.00 ) ) AS vl_total ';
    END IF;

    stSql := stSql || '
          FROM orcamento.conta_despesa     AS OCD             
              ,empenho.pre_empenho_despesa AS EPED            
              ,empenho.pre_empenho         AS EPE             
              --Ligação pre_empenho : empenho                 
              LEFT JOIN empenho.empenho    AS EE              
              ON( EPE.exercicio       = EE.exercicio          
              AND EPE.cod_pre_empenho = EE.cod_pre_empenho  ) ';

    IF (stDemonstrarDespesa = 'E') THEN
    stSql := stSql || '
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
                                     BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )
                                         AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )
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
                           AND coalesce( TO_DATE( EEA.timestamp::text, ''yyyy-mm-dd'' ), TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' ) )
                                                                         BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )
                                                                             AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )
                           GROUP BY EEA.exercicio                 
                                   ,EEA.cod_entidade              
                                   ,EEA.cod_empenho               
                           ORDER BY EEA.exercicio                 
                                   ,EEA.cod_entidade              
                                   ,EEA.cod_empenho               
               ) AS EEA ON( EE.exercicio    = EEA.exercicio       
                        AND EE.cod_entidade = EEA.cod_entidade    
                        AND EE.cod_empenho  = EEA.cod_empenho   ) ';

    ELSEIF (stDemonstrarDespesa = 'P') THEN
    stSql := stSql || '
            -- PAGO                                         
            -- Join com empenho.nota_liquidacao             
            LEFT JOIN empenho.nota_liquidacao AS ENL        
            ON( EE.cod_empenho  = ENL.cod_empenho           
            AND EE.exercicio    = ENL.exercicio_empenho     
            AND EE.cod_entidade = ENL.cod_entidade       )  
            -- Join com empenho.nota+liquidacao_paga        
            LEFT JOIN( SELECT ENLP.cod_entidade             
                             ,ENLP.exercicio                
                             ,ENLP.cod_nota                 
                             ,ENLP.timestamp                
                             ,sum( coalesce( ENLP.vl_pago, 0.00 ) ) as vl_total
                       FROM  empenho.nota_liquidacao_paga AS ENLP              
                       WHERE coalesce( TO_DATE( ENLP.timestamp::text, ''yyyy-mm-dd'' ), TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' ) )
                                                                        BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )  
                                                                            AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )  
                       GROUP BY ENLP.exercicio                  
                               ,ENLP.cod_entidade               
                               ,ENLP.cod_nota                   
                               ,ENLP.timestamp                  
                       ORDER BY ENLP.exercicio                  
                               ,ENLP.cod_entidade               
                               ,ENLP.cod_nota                   
                               ,ENLP.timestamp                  
            ) AS ENLP ON( ENL.exercicio    = ENLP.exercicio     
                      AND ENL.cod_entidade = ENLP.cod_entidade  
                      AND ENL.cod_nota     = ENLP.cod_nota     )
            -- Join com empenho.nota+liquidacao_paga_anulada    
            LEFT JOIN( SELECT ENLPA.exercicio                   
                             ,ENLPA.cod_entidade                
                             ,ENLPA.cod_nota                    
                             ,ENLPA.timestamp                   
                             ,sum( coalesce( ENLPA.vl_anulado, 0.00 ) ) as vl_total_anulado
                       FROM empenho.nota_liquidacao_paga_anulada AS ENLPA                  
                       WHERE coalesce( TO_DATE( ENLPA.timestamp_anulada::text, ''yyyy-mm-dd'' ),   
                                                                 TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' ) )
                                                         BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )  
                                                             AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )  
                       GROUP BY ENLPA.exercicio                     
                               ,ENLPA.cod_entidade                  
                               ,ENLPA.cod_nota                      
                               ,ENLPA.timestamp                     
                       ORDER BY ENLPA.exercicio                     
                               ,ENLPA.cod_entidade                  
                               ,ENLPA.cod_nota                      
                               ,ENLPA.timestamp                     
            ) AS ENLPA ON( ENLP.exercicio    = ENLPA.exercicio      
                       AND ENLP.cod_entidade = ENLPA.cod_entidade   
                       AND ENLP.cod_nota     = ENLPA.cod_nota       
                       AND ENLP.timestamp    = ENLPA.timestamp     ) ';
    END IF;
    
    stSql := stSql || '
          WHERE                         
            --Ligação conta_despesa : pre_empenho_despesa 
                OCD.cod_conta        = EPED.cod_conta     
            AND OCD.exercicio        = EPED.exercicio     
            --Ligação pre_empenho_despesa : pre_empenho   
            AND EPED.exercicio       = EPE.exercicio      
            AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
            -- FILTRO                                     
            AND EE.exercicio         = ''' || stExercicio || ''' 
            AND EE.cod_entidade     IN ( ' || stCodEntidades || ' )
            AND ( OCD.cod_estrutural  like ( ''3%'' )  
               OR OCD.cod_estrutural  like ( ''4%'' )  
               OR OCD.cod_estrutural  like ( ''9%'' ) )
          GROUP BY OCD.cod_estrutural                
                  ,EE.exercicio                      
          ORDER BY OCD.cod_estrutural                
                  ,EE.exercicio                      
    ) as tbl                                         
    ,orcamento.conta_despesa AS OCD                  
    WHERE tbl.exercicio = OCD.exercicio              
      AND tbl.cod_estrutural = publico.fn_mascarareduzida( OCD.cod_estrutural )
    GROUP BY tbl.cod_estrutural
            ,OCD.descricao     
    ORDER BY tbl.cod_estrutural ';
 
    -- SE FOR PARA DEMONSTRAR AS LIQUIDADAS, DESCONSIDERA TUDO ACIMA
    IF (stDemonstrarDespesa = 'L') THEN
        stSql := '
    select   publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )  as cod_estrutural
           , conta_despesa.descricao
           , sum ( anexo.vl_total ) as vl_total
    from (
            SELECT   
                   conta_despesa.exercicio  
                 , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )  as cod_estrutural
                 , sum( coalesce( nota_liquidacao_item.vl_total                , 0.00 ) ) -  
                   sum( coalesce( nota_liquidacao_item_anulado.vl_total_anulado, 0.00 ) ) AS vl_total  
            FROM   orcamento.despesa 
                 , orcamento.conta_despesa
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
            WHERE conta_despesa.exercicio  = despesa.exercicio 
              AND conta_despesa.cod_conta  = despesa.cod_conta
              AND despesa.cod_despesa  = pre_empenho_despesa.cod_despesa 
              AND despesa.exercicio    = pre_empenho_despesa.exercicio 
              AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho 
              AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio 
              AND pre_empenho.cod_pre_empenho         = empenho.cod_pre_empenho 
              AND pre_empenho.exercicio               = empenho.exercicio 
              AND conta_despesa.exercicio = ''' || stExercicio || ''' 
              AND despesa.cod_entidade IN ( ' || stCodEntidades || ' )
            GROUP BY conta_despesa.exercicio 
                    , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) ) 
            ORDER BY  conta_despesa.exercicio 
                    , publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )
    )as anexo
    join orcamento.conta_despesa 
         on      anexo.cod_estrutural = publico.fn_mascarareduzida( conta_despesa.cod_estrutural )
             and anexo.exercicio      = conta_despesa.exercicio 
    group by   publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) )
             , conta_despesa.descricao
    order by publico.fn_mascarareduzida( substr( conta_despesa.cod_estrutural,1,3) ) ';
    END IF;


    FOR reReg IN EXECUTE stSql
    LOOP
        RETURN NEXT reReg;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE plpgsql

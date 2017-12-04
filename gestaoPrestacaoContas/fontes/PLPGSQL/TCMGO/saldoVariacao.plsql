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

CREATE OR REPLACE FUNCTION tcmgo.saldoVariacao (VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stSql               VARCHAR   := '';
    reReg               RECORD;
BEGIN

    stSql := '
    SELECT max(abs(saldo_receita)) as variacao_receita , max(abs(saldo_despesa)) as variacao_despesa from (       
        SELECT sum( coalesce(tabela.vl_arrecadado, 0.00) ) as saldo_receita, 0.00 as saldo_despesa from (        
        SELECT                                                                                                   
            tbl.cod_estrutural,                                                                                  
            sum( coalesce(tbl.vl_arrecadado_debito,0.00) ) + sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado, 
            sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado_credito,                              
            sum(coalesce(tbl.vl_arrecadado_debito,0.00)) as vl_arrecadado_debito                                 
        FROM(                                                                                                    
            SELECT                                                                                               
                substr( OPC.cod_estrutural, 1,15 ) AS cod_estrutural,                                            
                OPC.exercicio,                                                                                   
                sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito,                     
                sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito                     
            FROM                                                                                                 
                contabilidade.plano_conta      AS OPC                                                            
                    LEFT JOIN contabilidade.plano_analitica AS OCA ON (                                          
                        OPC.cod_conta = OCA.cod_conta AND                                                        
                        OPC.exercicio = OCA.exercicio                                                            
                    )                                                                                            
                    LEFT JOIN (                                                                                  
                        SELECT                                                                                   
                            CCD.cod_plano,                                                                       
                            CCD.exercicio,                                                                       
                            sum( vl_lancamento ) as vl_lancamento                                                
                        FROM                                                                                     
                            contabilidade.plano_conta      AS CPC,                                               
                            contabilidade.plano_analitica  AS CPA,                                               
                            contabilidade.conta_debito     AS CCD,                                               
                            contabilidade.valor_lancamento AS CVLD,                                              
                            contabilidade.lancamento       AS CLA,                                               
                            contabilidade.lote             AS CLO                                                
                        WHERE                                                                                    
                                CPC.cod_conta      = CPA.cod_conta                                               
                            AND CPC.exercicio      = CPA.exercicio                                               
                            AND CPC.cod_sistema    = 1                                                           
                            AND CPA.cod_plano      = CCD.cod_plano                                               
                            AND CPA.exercicio      = CCD.exercicio                                               
                            AND CCD.cod_lote       = CVLD.cod_lote                                               
                            AND CCD.tipo           = CVLD.tipo                                                   
                            AND CCD.sequencia      = CVLD.sequencia                                              
                            AND CCD.exercicio      = CVLD.exercicio                                              
                            AND CCD.tipo_valor     = CVLD.tipo_valor                                             
                            AND CCD.cod_entidade   = CVLD.cod_entidade                                           
                            AND CVLD.tipo_valor    = ''D''                                                         
                            AND CVLD.cod_lote      = CLA.cod_lote                                                
                            AND CVLD.tipo          = CLA.tipo                                                    
                            AND CVLD.cod_entidade  = CLA.cod_entidade                                            
                            AND CVLD.exercicio     = CLA.exercicio                                               
                            AND CVLD.sequencia     = CLA.sequencia                                               
                            AND CLA.cod_lote      = CLO.cod_lote                                                 
                            AND CLA.tipo          = CLO.tipo                                                     
                            AND CLA.cod_entidade  = CLO.cod_entidade                                             
                            AND CLA.exercicio     = CLO.exercicio                                                
                            AND CCD.exercicio      = ''' || stExercicio || '''                           
                            AND CVLD.cod_entidade  IN( ' || stCodEntidades || ' )                      
                            AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )  
                                                AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )  
                            AND CLO.tipo != ''I''                                                                  
                            AND CLA.cod_historico not between 800 and 899                                        
                        GROUP BY                                                                                 
                            CCD.cod_plano,                                                                       
                            CCD.exercicio                                                                        
                        ORDER BY                                                                                 
                            CCD.cod_plano,                                                                       
                            CCD.exercicio                                                                        
                    ) AS CCD ON (                                                                                
                        OCA.cod_plano = CCD.cod_plano AND                                                        
                        OCA.exercicio = CCD.exercicio                                                            
                    )                                                                                            
                    LEFT JOIN (                                                                                  
                        SELECT                                                                                   
                            CCC.cod_plano,                                                                       
                            CCC.exercicio,                                                                       
                            sum(vl_lancamento) as vl_lancamento                                                  
                        FROM                                                                                     
                            contabilidade.plano_conta      AS CPC,                                               
                            contabilidade.plano_analitica  AS CPA,                                               
                            contabilidade.conta_credito    AS CCC,                                               
                            contabilidade.valor_lancamento AS CVLC,                                              
                            contabilidade.lancamento       AS CLA,                                               
                            contabilidade.lote             AS CLO                                                
                        WHERE                                                                                    
                                CPC.cod_conta      = CPA.cod_conta                                               
                            AND CPC.exercicio      = CPA.exercicio                                               
                            AND CPC.cod_sistema    = 1                                                           
                            AND CPA.cod_plano      = CCC.cod_plano                                               
                            AND CPA.exercicio      = CCC.exercicio                                               
                            AND CCC.cod_lote       = CVLC.cod_lote                                               
                            AND CCC.tipo           = CVLC.tipo                                                   
                            AND CCC.sequencia      = CVLC.sequencia                                              
                            AND CCC.exercicio      = CVLC.exercicio                                              
                            AND CCC.tipo_valor     = CVLC.tipo_valor                                             
                            AND CCC.cod_entidade   = CVLC.cod_entidade                                           
                            AND CVLC.tipo_valor    = ''C''                                                         
                            AND CVLC.cod_lote      = CLA.cod_lote                                                
                            AND CVLC.tipo          = CLA.tipo                                                    
                            AND CVLC.cod_entidade  = CLA.cod_entidade                                            
                            AND CVLC.exercicio     = CLA.exercicio                                               
                            AND CVLC.sequencia     = CLA.sequencia                                               
                            AND CLA.cod_lote      = CLO.cod_lote                                                 
                            AND CLA.tipo          = CLO.tipo                                                     
                            AND CLA.cod_entidade  = CLO.cod_entidade                                             
                            AND CLA.exercicio     = CLO.exercicio                                                
                            AND CCC.exercicio      = ''' || stExercicio || '''                           
                            AND CVLC.cod_entidade  IN( ' || stCodEntidades || ' )                      
                            AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )  
                                                AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )  
                            AND CLO.tipo != ''I''                                                                  
                            AND CLA.cod_historico not between 800 and 899                                        
                        GROUP BY                                                                                 
                            CCC.cod_plano,                                                                       
                            CCC.exercicio                                                                        
                        ORDER BY                                                                                 
                            CCC.cod_plano,                                                                       
                            CCC.exercicio                                                                        
                    ) AS CCC ON (                                                                                
                        OCA.cod_plano = CCC.cod_plano AND                                                        
                        OCA.exercicio = CCC.exercicio                                                            
                    )                                                                                            
                WHERE                                                                                            
                    OPC.exercicio = ''' || stExercicio || '''                                            
                   AND  OPC.cod_estrutural    like  ''6.%''                                                        
                   AND  OPC.cod_estrutural   not like  ''6.1.1%''                                                  
                   AND  OPC.cod_estrutural   not like  ''6.1.2%''                                                  
                   AND  OPC.cod_estrutural   not like  ''6.2.3.3.1.05%''                                           
              GROUP BY                                                                                           
                    OPC.cod_estrutural,                                                                          
                    OPC.exercicio                                                                                
              ORDER BY                                                                                           
                    OPC.cod_estrutural,                                                                          
                    OPC.exercicio                                                                                
            ) AS tbl                                                                                            
        GROUP BY                                                                                                 
            tbl.cod_estrutural                                                                                  
        ORDER BY                                                                                                 
            tbl.cod_estrutural                                                                                  
        ) as tabela                                                                                              
        where tabela.vl_arrecadado <> 0                                                                          
                                                                                                                 
    UNION ALL                                                                                                    
                                                                                                                 
        SELECT 0.00 as saldo_receita, sum( coalesce(tabela.vl_arrecadado, 0.00) ) as saldo_despesa from (        
        SELECT                                                                                                   
            tbl.cod_estrutural,                                                                                  
            sum( coalesce(tbl.vl_arrecadado_debito,0.00) ) + sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado, 
            sum(coalesce(tbl.vl_arrecadado_credito,0.00)) as vl_arrecadado_credito,                              
            sum(coalesce(tbl.vl_arrecadado_debito,0.00)) as vl_arrecadado_debito                                 
        FROM(                                                                                                    
            SELECT                                                                                               
                substr( OPC.cod_estrutural, 1,15 ) AS cod_estrutural,                                            
                OPC.exercicio,                                                                                   
                sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito,                     
                sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito                     
            FROM                                                                                                 
                contabilidade.plano_conta      AS OPC                                                            
                    LEFT JOIN contabilidade.plano_analitica AS OCA ON (                                          
                        OPC.cod_conta = OCA.cod_conta AND                                                        
                        OPC.exercicio = OCA.exercicio                                                            
                    )                                                                                            
                    LEFT JOIN (                                                                                  
                        SELECT                                                                                   
                            CCD.cod_plano,                                                                       
                            CCD.exercicio,                                                                       
                            sum( vl_lancamento ) as vl_lancamento                                                
                        FROM                                                                                     
                            contabilidade.plano_conta      AS CPC,                                               
                            contabilidade.plano_analitica  AS CPA,                                               
                            contabilidade.conta_debito     AS CCD,                                               
                            contabilidade.valor_lancamento AS CVLD,                                              
                            contabilidade.lancamento       AS CLA,                                               
                            contabilidade.lote             AS CLO                                                
                        WHERE                                                                                    
                                CPC.cod_conta      = CPA.cod_conta                                               
                            AND CPC.exercicio      = CPA.exercicio                                               
                            AND CPC.cod_sistema    = 1                                                           
                            AND CPA.cod_plano      = CCD.cod_plano                                               
                            AND CPA.exercicio      = CCD.exercicio                                               
                            AND CCD.cod_lote       = CVLD.cod_lote                                               
                            AND CCD.tipo           = CVLD.tipo                                                   
                            AND CCD.sequencia      = CVLD.sequencia                                              
                            AND CCD.exercicio      = CVLD.exercicio                                              
                            AND CCD.tipo_valor     = CVLD.tipo_valor                                             
                            AND CCD.cod_entidade   = CVLD.cod_entidade                                           
                            AND CVLD.tipo_valor    = ''D''                                                         
                            AND CVLD.cod_lote      = CLA.cod_lote                                                
                            AND CVLD.tipo          = CLA.tipo                                                    
                            AND CVLD.cod_entidade  = CLA.cod_entidade                                            
                            AND CVLD.exercicio     = CLA.exercicio                                               
                            AND CVLD.sequencia     = CLA.sequencia                                               
                            AND CLA.cod_lote      = CLO.cod_lote                                                 
                            AND CLA.tipo          = CLO.tipo                                                     
                            AND CLA.cod_entidade  = CLO.cod_entidade                                             
                            AND CLA.exercicio     = CLO.exercicio                                                
                            AND CCD.exercicio      = ''' || stExercicio || '''                           
                            AND CVLD.cod_entidade  IN( ' || stCodEntidades || ' )                      
                            AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )  
                                                AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )  
                            AND CLO.tipo != ''I''                                                                  
                            AND CLA.cod_historico not between 800 and 899                                        
                        GROUP BY                                                                                 
                            CCD.cod_plano,                                                                       
                            CCD.exercicio                                                                        
                        ORDER BY                                                                                 
                            CCD.cod_plano,                                                                       
                            CCD.exercicio                                                                        
                    ) AS CCD ON (                                                                                
                        OCA.cod_plano = CCD.cod_plano AND                                                        
                        OCA.exercicio = CCD.exercicio                                                            
                    )                                                                                            
                    LEFT JOIN (                                                                                  
                        SELECT                                                                                   
                            CCC.cod_plano,                                                                       
                            CCC.exercicio,                                                                       
                            sum(vl_lancamento) as vl_lancamento                                                  
                        FROM                                                                                     
                            contabilidade.plano_conta      AS CPC,                                               
                            contabilidade.plano_analitica  AS CPA,                                               
                            contabilidade.conta_credito    AS CCC,                                               
                            contabilidade.valor_lancamento AS CVLC,                                              
                            contabilidade.lancamento       AS CLA,                                               
                            contabilidade.lote             AS CLO--,                                             
                        WHERE                                                                                    
                                CPC.cod_conta      = CPA.cod_conta                                               
                            AND CPC.exercicio      = CPA.exercicio                                               
                            AND CPC.cod_sistema    = 1                                                           
                            AND CPA.cod_plano      = CCC.cod_plano                                               
                            AND CPA.exercicio      = CCC.exercicio                                               
                            AND CCC.cod_lote       = CVLC.cod_lote                                               
                            AND CCC.tipo           = CVLC.tipo                                                   
                            AND CCC.sequencia      = CVLC.sequencia                                              
                            AND CCC.exercicio      = CVLC.exercicio                                              
                            AND CCC.tipo_valor     = CVLC.tipo_valor                                             
                            AND CCC.cod_entidade   = CVLC.cod_entidade                                           
                            AND CVLC.tipo_valor    = ''C''                                                         
                            AND CVLC.cod_lote      = CLA.cod_lote                                                
                            AND CVLC.tipo          = CLA.tipo                                                    
                            AND CVLC.cod_entidade  = CLA.cod_entidade                                            
                            AND CVLC.exercicio     = CLA.exercicio                                               
                            AND CVLC.sequencia     = CLA.sequencia                                               
                            AND CLA.cod_lote      = CLO.cod_lote                                                 
                            AND CLA.tipo          = CLO.tipo                                                     
                            AND CLA.cod_entidade  = CLO.cod_entidade                                             
                            AND CLA.exercicio     = CLO.exercicio                                                
                            AND CCC.exercicio      = ''' || stExercicio || '''                           
                            AND CVLC.cod_entidade  IN( ' || stCodEntidades || ' )                      
                            AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )  
                                                AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )  
                            AND CLO.tipo != ''I''                                                                  
                            AND CLA.cod_historico not between 800 and 899                                        
                        GROUP BY                                                                                 
                            CCC.cod_plano,                                                                       
                            CCC.exercicio                                                                        
                        ORDER BY                                                                                 
                            CCC.cod_plano,                                                                       
                            CCC.exercicio                                                                        
                    ) AS CCC ON (                                                                                
                        OCA.cod_plano = CCC.cod_plano AND                                                        
                        OCA.exercicio = CCC.exercicio                                                            
                    )                                                                                            
                WHERE                                                                                            
                    OPC.exercicio = ''' || stExercicio || '''                                            
                    AND OPC.cod_estrutural    like  ''5.%''                                                        
                    AND  OPC.cod_estrutural   not like  ''5.1.1%''                                                  
                    AND  OPC.cod_estrutural   not like  ''5.1.2%''                                                  
              GROUP BY                                                                                           
                    OPC.cod_estrutural,                                                                          
                    OPC.exercicio                                                                                
              ORDER BY                                                                                           
                    OPC.cod_estrutural,                                                                          
                    OPC.exercicio                                                                                
            ) AS tbl                                                                                            
        GROUP BY                                                                                                 
            tbl.cod_estrutural                                                                                  
        ORDER BY                                                                                                 
            tbl.cod_estrutural                                                                                  
        ) as tabela                                                                                              
    where tabela.vl_arrecadado <> 0                                                                              
    ) as saldo
    ';

    FOR reReg IN EXECUTE stSql
    LOOP
        RETURN NEXT reReg;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE plpgsql

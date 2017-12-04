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

CREATE OR REPLACE FUNCTION orcamento.saldoReceita (VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stSql               VARCHAR   := '';
    reReg               RECORD;
BEGIN

    stSql := '
  SELECT CAST(tbl.cod_estrutural AS VARCHAR)
        ,CAST(publico.fn_nivel(tbl.cod_estrutural) AS INTEGER) as nivel 
        ,sum( tbl.vl_arrecadado_debito ) as vl_arrecadado_debito
        ,sum( tbl.vl_arrecadado_credito ) as vl_arrecadado_credito 
        ,abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado 
        ,OCR.nom_conta 
  FROM(                                                                                             
        SELECT substr( OPC.cod_estrutural, 1,9)    AS cod_estrutural                                
              ,OPC.exercicio                                                                        
              ,sum( coalesce( CCD.vl_lancamento, 0.00 ) ) AS vl_arrecadado_debito       
              ,sum( coalesce( CCC.vl_lancamento, 0.00 ) ) AS vl_arrecadado_credito      
        FROM contabilidade.plano_conta    AS OPC                                        
        -- Join com plano analitica                                                     
        LEFT JOIN contabilidade.plano_analitica AS OCA                                  
        ON( OPC.cod_conta = OCA.cod_conta                                               
        AND OPC.exercicio = OCA.exercicio  )                                            
        LEFT JOIN contabilidade.plano_banco AS pb                                       
        ON( pb.cod_plano = OCA.cod_plano                                                
        AND pb.exercicio = OCA.exercicio  )                                             
        -- Join com contabilidade.valor_lancamento                                      
        LEFT JOIN ( SELECT CCD.cod_plano                                                
                          ,CCD.exercicio                                                
                          ,sum( CVLD.vl_lancamento ) as vl_lancamento                   
                    FROM contabilidade.conta_debito     AS CCD              
                        ,contabilidade.valor_lancamento AS CVLD             
                        ,contabilidade.lote             AS CLO              
                    WHERE CCD.cod_lote       = CVLD.cod_lote                
                      AND CCD.tipo           = CVLD.tipo                    
                      AND CCD.sequencia      = CVLD.sequencia               
                      AND CCD.exercicio      = CVLD.exercicio               
                      AND CCD.tipo_valor     = CVLD.tipo_valor              
                      AND CCD.cod_entidade   = CVLD.cod_entidade            
                      AND CVLD.tipo_valor    = ''D''
                      AND CVLD.cod_lote      = CLO.cod_lote                 
                      AND CVLD.tipo          = CLO.tipo                     
                      AND CASE WHEN TO_DATE(''' || stDataFinal || ''',''dd/mm/yyyy'' ) = TO_DATE(''01/01/' || stExercicio || ''',''dd/mm/yyyy'')
                               THEN CASE WHEN CVLD.tipo = ''I''
                                      THEN true                                       
                                      ELSE false                                      
                                    END                                               
                               ELSE true                                              
                           END                                                        
                      AND CVLD.cod_entidade  = CLO.cod_entidade                       
                      AND CVLD.exercicio     = CLO.exercicio                          
                      AND CCD.exercicio      = ''' || stExercicio || '''      
                      AND CVLD.cod_entidade  IN( ' || stCodEntidades || ' ) 
                      AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' )
                                          AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' )
                    GROUP BY CCD.cod_plano                            
                            ,CCD.exercicio                            
                    ORDER BY CCD.cod_plano                            
                            ,CCD.exercicio                            
        ) AS CCD ON( OCA.cod_plano = CCD.cod_plano                    
                 AND OCA.exercicio = CCD.exercicio                    
        )                                                             
        -- Join com contabilidade.valor_lancamento                    
        LEFT JOIN ( SELECT CCC.cod_plano                              
                          ,CCC.exercicio                              
                          ,sum( CVLC.vl_lancamento ) as vl_lancamento 
                    FROM contabilidade.conta_credito    AS CCC     
                        ,contabilidade.valor_lancamento AS CVLC    
                        ,contabilidade.lote             AS CLO     
                    WHERE CCC.cod_lote       = CVLC.cod_lote       
                      AND CCC.tipo           = CVLC.tipo           
                      AND CCC.sequencia      = CVLC.sequencia      
                      AND CCC.exercicio      = CVLC.exercicio      
                      AND CCC.tipo_valor     = CVLC.tipo_valor     
                      AND CCC.cod_entidade   = CVLC.cod_entidade   
                      AND CVLC.tipo_valor    = ''C''                 
                      AND CVLC.cod_lote      = CLO.cod_lote        
                      AND CVLC.tipo          = CLO.tipo            
                      AND CASE WHEN TO_DATE( ''' || stDataFinal || ''' ,''dd/mm/yyyy'' ) = TO_DATE(''01/01/' || stExercicio || ''',''dd/mm/yyyy'') 
                               THEN CASE WHEN CVLC.tipo = ''I''     
                                      THEN true                   
                                      ELSE false                  
                                    END                           
                               ELSE true                          
                           END                                    
                      AND CVLC.cod_entidade  = CLO.cod_entidade   
                      AND CVLC.exercicio     = CLO.exercicio      
                      AND CCC.exercicio      = ''' || stExercicio || '''      
                      AND CVLC.cod_entidade  IN( ' || stCodEntidades || ' )  
                      AND CLO.dt_lote BETWEEN TO_DATE( ''' || stDataInicial || ''', ''dd/mm/yyyy'' ) 
                                          AND TO_DATE( ''' || stDataFinal || ''', ''dd/mm/yyyy'' ) 
                    GROUP BY CCC.cod_plano             
                            ,CCC.exercicio             
                    ORDER BY CCC.cod_plano             
                            ,CCC.exercicio             
        ) AS CCC ON ( OCA.cod_plano = CCC.cod_plano    
                 AND  OCA.exercicio = CCC.exercicio    
        )                                              
        WHERE OPC.exercicio = ''' || stExercicio || '''
        AND (OPC.cod_estrutural    like  ''1.1.1.1.1%''                                 
         OR  OPC.cod_estrutural    like  ''1.1.1.1.2%''                                 
         OR  OPC.cod_estrutural    like  ''1.1.1.1.3%''                                 
         OR  OPC.cod_estrutural    like  ''1.1.5%''     )                               
        AND CASE WHEN     OPC.cod_estrutural like ''1.1.5%'' 
                      AND ( CCC.cod_plano is not null OR CCD.cod_plano is not null )  
                 THEN pb.cod_plano is not null                                        
                 ELSE true                                                            
             END                                                                      
        GROUP BY OPC.cod_estrutural                                                   
                ,OPC.exercicio                                                        
        ORDER BY OPC.cod_estrutural                                                   
                ,OPC.exercicio                                                        
  ) AS tbl                                                                            
  ,contabilidade.plano_conta AS OCR                                                   
  WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )                       
  AND   length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9               
  AND   tbl.exercicio      = OCR.exercicio                                            
  GROUP BY tbl.cod_estrutural                                                         
          ,OCR.nom_conta                                                              
  ORDER BY tbl.cod_estrutural                                                         
          ,OCR.nom_conta;                                                             

    ';

    FOR reReg IN EXECUTE stSql
    LOOP
        RETURN NEXT reReg;
    END LOOP;

    RETURN;
END;
$$ LANGUAGE plpgsql

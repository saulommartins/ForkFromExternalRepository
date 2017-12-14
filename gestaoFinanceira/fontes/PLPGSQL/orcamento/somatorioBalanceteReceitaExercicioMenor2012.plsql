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
CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS NUMERIC(14,2) AS $$
 DECLARE                                                                                                               
     stCodEstrutural             ALIAS FOR $1;                                                                         
     dtInicial                   ALIAS FOR $2;                                                                         
     dtFinal                     ALIAS FOR $3;                                                                         
     stExercicio                 ALIAS FOR $4;                                                                         
     stSql                       VARCHAR   := '';                                                                      
     nuSoma                      NUMERIC   := 0;                                                                       
     crCursor                    REFCURSOR;                                                                            
                                                                                                                       
 BEGIN                                                                                                                 
                                                                                                                       
                                                                                                                       
 stSql := '                                                                                                            
     SELECT SUM(valor)::NUMERIC(14,2) as valor                                                                         
       FROM (                                                                                                          
             SELECT orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural) 
                                                          ,''' || dtInicial || '''                                     
                                                          ,''' || dtFinal || '''                                       
                    ) as valor                                                                                         
               FROM contabilidade.plano_conta                                                                          
         INNER JOIN orcamento.conta_receita                                                                            
                 ON plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural                                
                AND plano_conta.exercicio        = conta_receita.exercicio                                             
              WHERE conta_receita.cod_estrutural = ''' || stCodEstrutural || '''                                       
            --    AND publico.fn_nivel(conta_receita.cod_estrutural) = publico.fn_nivel(''' || stCodEstrutural || ''') 
                AND conta_receita.exercicio = ''' || stExercicio || '''                                                
            )  as tbl                                                                                                  
     ';                                                                                                                
     OPEN crCursor FOR EXECUTE stSql;                                                                                  
         FETCH crCursor INTO nuSoma;                                                                                   
     CLOSE crCursor;                                                                                                   
                                                                                                                       
     RETURN nuSoma;                                                                                                    
 END;                                                                                                                  
$$ language 'plpgsql';

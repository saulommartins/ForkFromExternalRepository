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
CREATE OR REPLACE FUNCTION  empenho.fn_empenho_liquidado( character varying, integer, integer, character varying, character varying ) RETURNS NUMERIC AS $$
    DECLARE                                                                                                                                                  
        stExercicio             ALIAS FOR $1;                                                                                                                
        stCodEmpenho            ALIAS FOR $2;                                                                                                                
        stCodEntidades          ALIAS FOR $3;                                                                                                                
        dtInicial               ALIAS FOR $4;                                                                                                                
        dtFinal                 ALIAS FOR $5;                                                                                                                
        stSql                   VARCHAR   := '';                                                                                                             
        nuSoma                  NUMERIC   := 0;                                                                                                              
        crCursor                REFCURSOR;                                                                                                                   
                                                                                                                                                             
    BEGIN                                                                                                                                                    
          stSql := '
                    SELECT                                                                                                                                   
                    coalesce(sum( nli.vl_total ),0.00) as soma                                                                                               
                    FROM    empenho.empenho e,                                                                                                               
                            empenho.nota_liquidacao nl,                                                                                                      
                            empenho.nota_liquidacao_item nli                                                                                                 
                    WHERE   e.exercicio = ''' || stExercicio || ''' AND                                                                                      
                            e.cod_entidade IN ( ' || stCodEntidades || ' ) AND                                                                               
                            e.cod_empenho = ' || stCodEmpenho || ' AND                                                                                       
                                                                                                                                                             
                            --Ligação EMPENHO : NOTA LIQUIDAÇÃO                                                                                              
                            e.exercicio = nl.exercicio_empenho AND                                                                                           
                            e.cod_entidade = nl.cod_entidade AND                                                                                             
                            e.cod_empenho = nl.cod_empenho AND                                                                                               
                                                                                                                                                             
                            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM                                                                               
          ';
          IF dtInicial = '' OR dtInicial IS NULL THEN
                stSql := stSql || ' nl.dt_liquidacao <= to_date(''' || dtFinal || ''',''dd/mm/yyyy'') AND ';
          ELSE
                stSql := stSql || ' nl.dt_liquidacao BETWEEN to_date(''' || dtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || dtFinal || ''',''dd/mm/yyyy'') AND ';
          END IF;
          stSql := stSql || '
                            nl.exercicio = nli.exercicio AND                                                                                                 
                            nl.exercicio_empenho = nli.exercicio_item AND                                                                                    
                            nl.cod_nota = nli.cod_nota AND                                                                                                   
                            nl.cod_entidade = nli.cod_entidade ';

        OPEN crCursor FOR EXECUTE stSql;                                                                                                                     
            FETCH crCursor INTO nuSoma;                                                                                                                          
        CLOSE crCursor;                                                                                                                                      
                                                                                                                                                             
        RETURN nuSoma;                                                                                                                                       
    END;                                                                                                                                                     
$$ LANGUAGE 'plpgsql';

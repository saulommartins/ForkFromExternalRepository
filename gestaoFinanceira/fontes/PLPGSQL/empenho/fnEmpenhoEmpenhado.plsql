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
CREATE OR REPLACE FUNCTION empenho.fn_empenho_empenhado ( character varying, integer, integer, character varying, character varying ) RETURNS NUMERIC AS $$
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
                coalesce(sum( ipe.vl_total ),0.00) as soma                                                                                           
                FROM    empenho.empenho e,                                                                                                           
                        empenho.pre_empenho pe,                                                                                                      
                        empenho.item_pre_empenho ipe                                                                                                 
                WHERE   e.exercicio = ''' || stExercicio || ''' AND                                                                                  
                        e.cod_entidade IN ( ' || stCodEntidades || ' ) AND                                                                           
                        e.cod_empenho = ' || stCodEmpenho || ' AND                                                                                   
    ';
    IF dtInicial = '' OR dtInicial IS NULL THEN
        stSql := stSql || ' e.dt_empenho <= to_date(''' || dtFinal || ''',''dd/mm/yyyy'') AND ';
    ELSE
        stSql := stSql || ' e.dt_empenho BETWEEN to_date(''' || dtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || dtFinal || ''',''dd/mm/yyyy'') AND ';
    END IF;
    stSql := stSql || '
                        e.exercicio = pe.exercicio AND
                        e.cod_pre_empenho = pe.cod_pre_empenho AND                                                                                   
                        pe.exercicio = ipe.exercicio AND                                                                                             
                        pe.cod_pre_empenho = ipe.cod_pre_empenho
    ';
                                                                                                                                                     
    OPEN crCursor FOR EXECUTE stSql;                                                                                                                 
    FETCH crCursor INTO nuSoma;                                                                                                                      
    CLOSE crCursor;                                                                                                                                  
                                                                                                                                                     
    RETURN nuSoma;                                                                                                                                   
END;
$$ LANGUAGE 'plpgsql';

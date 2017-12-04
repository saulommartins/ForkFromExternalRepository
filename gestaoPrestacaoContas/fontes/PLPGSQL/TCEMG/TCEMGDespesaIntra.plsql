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
 */

/*

*/

CREATE OR REPLACE FUNCTION tcemg.despesa_intra (varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    stMes                   ALIAS FOR $5;
    
    stSql                   VARCHAR   := '';
    reRegistro              RECORD;
BEGIN
    stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT SUM(COALESCE(saldo_inicial, 0.00)) AS saldo_inicial
                     , SUM(COALESCE(pago_mes, 0.00))      AS total_credito
                     , SUM(COALESCE(empenhado_mes,0.00))  AS empenhado_mes
                     , SUM(COALESCE(liquidado_mes,0.00))  AS liquidado_mes
                     , SUM(COALESCE(anulado_mes,0.00))    AS anulado_mes
                     , 0.00 AS juros_encargos_divida
                     , 0.00 AS amort_divida
                FROM ( SELECT *                                                                                                    
                         FROM orcamento.fn_consolidado_elem_despesa('''||stExercicio||''','''','''||stDataInicial||''','''||stDataFinal||''','''||stCodEntidades||''','''','''','''','''','''','''', 0, 0)
                              as retorno( classificacao   VARCHAR ,        
                                          cod_reduzido    VARCHAR ,        
                                          descricao       VARCHAR ,        
                                          num_orgao       INTEGER ,        
                                          nom_orgao       VARCHAR ,        
                                          num_unidade     INTEGER ,        
                                          nom_unidade     VARCHAR ,        
                                          saldo_inicial   NUMERIC ,        
                                          suplementacoes  NUMERIC ,        
                                          reducoes        NUMERIC ,        
                                          empenhado_mes   NUMERIC ,        
                                          empenhado_ano   NUMERIC ,        
                                          anulado_mes     NUMERIC ,        
                                          anulado_ano     NUMERIC ,        
                                          pago_mes        NUMERIC ,        
                                          pago_ano        NUMERIC ,        
                                          liquidado_mes   NUMERIC ,        
                                          liquidado_ano   NUMERIC ,        
                                          tipo_conta      VARCHAR ,        
                                          nivel           INTEGER         
                                        )
                        WHERE cod_reduzido ilike ''%.9.1.%''
                          AND nivel = 5
                     ORDER BY classificacao
                    )AS tabela ';
    EXECUTE stSql;
        
     stSql := 'CREATE TEMPORARY TABLE tmp_despesa_intra AS
               
                    SELECT 12 AS mes
                         , saldo_inicial AS demais_despesas_intra
                         , ''01'' AS cod_tipo
                         , juros_encargos_divida
                         , amort_divida
                      FROM tmp_despesa
                      
                 UNION ALL
                 
                    SELECT 12 AS mes
                         , saldo_inicial AS demais_despesas_intra
                         , ''02'' AS cod_tipo
                         , juros_encargos_divida
                         , amort_divida
                      FROM tmp_despesa
                      
                 UNION ALL     
                    
                    SELECT 12 AS mes
                         , saldo_inicial AS demais_despesas_intra
                         , ''03'' AS cod_tipo
                         , juros_encargos_divida
                         , amort_divida
                      FROM tmp_despesa
                 
                 UNION ALL
                 
                    SELECT 12 AS mes
                         , empenhado_mes AS demais_despesas_intra
                         , ''04'' AS cod_tipo
                         , juros_encargos_divida
                         , amort_divida
                      FROM tmp_despesa
                      
                 UNION ALL     
                    
                    SELECT 12 AS mes
                         , liquidado_mes AS demais_despesas_intra
                         , ''05'' AS cod_tipo
                         , juros_encargos_divida
                         , amort_divida
                      FROM tmp_despesa
                      
                 UNION ALL     
                    
                    SELECT 12 AS mes
                         , anulado_mes AS demais_despesas_intra
                         , ''06'' AS cod_tipo
                         , juros_encargos_divida
                         , amort_divida
                      FROM tmp_despesa            
            ';
    EXECUTE stSql;        

    stSql := 'SELECT * FROM tmp_despesa_intra ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    DROP TABLE tmp_despesa;
    DROP TABLE tmp_despesa_intra;

    RETURN;
    
END;
$$ LANGUAGE 'plpgsql';
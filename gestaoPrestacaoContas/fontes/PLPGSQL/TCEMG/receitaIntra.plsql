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
    * Arquivo de mapeamento para a função que busca os dados de receita previdenciária
    * Data de Criação   : 22/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_receita_intra(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    inMes                   ALIAS FOR $3;
    stSql                   VARCHAR := '';
    dtInicial               VARCHAR := '';
    dtFinal                 VARCHAR := '';    
    arDatas                 VARCHAR[];
    reRegistro              RECORD;

BEGIN
    
    arDatas   := publico.mes(stExercicio,inMes);
    dtInicial := arDatas[0];
    dtFinal   := arDatas[1];

    stSql :='CREATE TEMPORARY TABLE tmp_balancete_receita AS 
            (
                SELECT
                        cod_estrutural                                                 
                        ,ABS(valor_previsto) as valor_previsto
                        ,ABS(arrecadado_periodo) as arrecadado_periodo
                        ,ABS(arrecadado_ano) as arrecadado_ano
                        ,ABS(diferenca) as diferenca
                FROM orcamento.fn_balancete_receita('''||stExercicio||''','''','''||dtInicial||''','''||dtFinal||''','''||stCodEntidades||'''
                                                    ,'''','''','''','''','''','''','''') 
                as retorno(                      
                        cod_estrutural      varchar,                                           
                        receita             integer,                                           
                        recurso             varchar,                                           
                        descricao           varchar,                                           
                        valor_previsto      numeric,                                           
                        arrecadado_periodo  numeric,                                           
                        arrecadado_ano      numeric,                                           
                        diferenca           numeric                                           
                )
                ORDER BY cod_estrutural
            )
    ';

    EXECUTE stSql;
 
    stSql :='
            SELECT 
                    *
            FROM (
                    SELECT 
                            ''01''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE (cod_estrutural LIKE (''7.0.0.0.00.00.00.00.00'') OR cod_estrutural LIKE (''8.0.0.0.00.00.00.00.00'')) AND cod_estrutural NOT LIKE ''8.3.%'') AS demais_receita_intra
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''8.3.0.0.00.00.00.00.00'' ) AS amortizacao_emprestimos
                    
                    UNION

                    SELECT
                            ''02''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE (cod_estrutural LIKE (''7.0.0.0.00.00.00.00.00'') OR cod_estrutural LIKE (''8.0.0.0.00.00.00.00.00'')) AND cod_estrutural NOT LIKE ''8.3.%'') AS demais_receita_intra
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''8.3.0.0.00.00.00.00.00'' ) AS amortizacao_emprestimos

                    UNION

                    SELECT
                            ''03''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE (cod_estrutural LIKE (''7.0.0.0.00.00.00.00.00'') OR cod_estrutural LIKE (''8.0.0.0.00.00.00.00.00'')) AND cod_estrutural NOT LIKE ''8.3.%'') AS demais_receita_intra
                            ,( SELECT COALESCE(SUM(valor_previsto),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''8.3.0.0.00.00.00.00.00'' ) AS amortizacao_emprestimos
                    UNION

                    SELECT
                            ''04''::VARCHAR AS cod_tipo
                            ,( SELECT COALESCE(SUM(arrecadado_ano),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE (cod_estrutural LIKE (''7.0.0.0.00.00.00.00.00'') OR cod_estrutural LIKE (''8.0.0.0.00.00.00.00.00'')) AND cod_estrutural NOT LIKE ''8.3.%'') AS demais_receita_intra
                            ,( SELECT COALESCE(SUM(arrecadado_ano),0)::VARCHAR as valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''8.3.0.0.00.00.00.00.00'' ) AS amortizacao_emprestimos

            ) AS retorno
    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_balancete_receita;

    RETURN;
END;
$$ language 'plpgsql';

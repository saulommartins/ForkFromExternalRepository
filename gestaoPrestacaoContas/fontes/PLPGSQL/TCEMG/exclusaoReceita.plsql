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
    * Arquivo de mapeamento para a função que busca os dados exclusao de receita
    * Data de Criação   : 27/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    
    * @package URBEM
    * @subpackage 

    $Id: exclusaoReceita.plsql 62756 2015-06-16 17:20:02Z franver $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_exclusao_receita(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;

    stSql               VARCHAR := '';
    reRegistro          RECORD;
BEGIN

    stSql := '  CREATE TEMPORARY TABLE tmp_exclusao_receita AS (
                  SELECT * 
                     FROM orcamento.fn_balancete_receita(  '|| quote_literal( stExercicio ) ||'
                                                         , ''''
                                                         , '|| quote_literal( stDtInicial ) ||'
                                                         , '|| quote_literal( stDtFinal ) ||'
                                                         , '|| quote_literal( stCodEntidade ) ||'
                                                         , ''''
                                                         , ''''
                                                         , ''''
                                                         , ''''
                                                         , ''''
                                                         , ''''
                                                         , '''' ) 
                           AS retorno(                      
                                        cod_estrutural      VARCHAR ,                                           
                                        receita             INTEGER ,                                           
                                        recurso             VARCHAR ,                                           
                                        descricao           VARCHAR ,                                           
                                        valor_previsto      NUMERIC ,                                           
                                        arrecadado_periodo  NUMERIC ,                                           
                                        arrecadado_ano      NUMERIC ,                                           
                                        diferenca           NUMERIC                                           
                            )
                ) ';
                
    EXECUTE stSql;

    stSql := 'SELECT *
                FROM (
                        SELECT 0 AS bimestre
                            , ( SELECT ABS(COALESCE(SUM( arrecadado_periodo ),0)) AS arrecadado_periodo
                                  FROM tmp_exclusao_receita
                                 WHERE cod_estrutural = ''1.2.1.0.29.00.00.00.00''
                              ) AS contr_serv
                            , ( SELECT ABS(COALESCE(SUM( arrecadado_periodo ),0)) AS arrecadado_periodo
                                  FROM tmp_exclusao_receita
                                 WHERE cod_estrutural = ''1.2.1.0.99.00.10.00.00''
                              ) AS compens_reg_prev          
                            , 0.00 AS out_duplic                
                            , 0.00 AS contr_patronal
                            , 0.00 descOutrasDuplic
                            , 0.00 AS fundacoes_transf_corrente 
                            , 0.00 AS autarquias_transf_corrente
                            , 0.00 AS empestdep_transf_corrente 
                            , 0.00 AS demaisent_transf_corrente 
                            , 0.00 AS fundacoes_transf_capital  
                            , 0.00 AS autarquias_transf_capital 
                            , 0.00 AS empestdep_transf_capital  
                            , 0.00 AS demaisent_transf_capital  
                ) AS retorno ';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_exclusao_receita;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
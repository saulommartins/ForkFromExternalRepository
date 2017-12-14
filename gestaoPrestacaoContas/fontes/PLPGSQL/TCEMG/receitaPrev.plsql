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

CREATE OR REPLACE FUNCTION tcemg.fn_receita_prev(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;
    stSql                   VARCHAR := '';
    reRegistro              RECORD;

BEGIN
    
    stSql :='
          CREATE TEMPORARY TABLE tmp_balancete_receita AS (
          SELECT cod_estrutural
               , ABS(valor_previsto) as valor_previsto
               , ABS(arrecadado_periodo) as arrecadado_periodo
            FROM orcamento.fn_balancete_receita('''||stExercicio||'''
                                               ,''''
                                               ,'''||dtInicial||'''
                                               ,'''||dtFinal||'''
                                               ,'''||stCodEntidades||'''
                                               ,''''
                                               ,''''
                                               ,''''
                                               ,''''
                                               ,''''
                                               ,''''
                                               ,'''') 
              AS retorno( cod_estrutural      VARCHAR
                        , receita             INTEGER
                        , recurso             VARCHAR
                        , descricao           VARCHAR
                        , valor_previsto      NUMERIC
                        , arrecadado_periodo  NUMERIC
                        , arrecadado_ano      NUMERIC
                        , diferenca           NUMERIC
                 )
        ORDER BY cod_estrutural
          )
    ';
    EXECUTE stSql;  

    stSql :='
          SELECT *
            FROM (
    ';
    IF EXTRACT( month FROM TO_DATE(''||dtInicial||'','dd/mm/yyyy') ) = 1 THEN
    stSql := stSql || '
                  SELECT ''01''::VARCHAR AS cod_tipo
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.01.00.00.00''  ) AS contrib_pat
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.07%''          ) AS contrib_serv_ativo
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.09%''          ) AS contrib_serv_inat_pens
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.3.0.0.00.00.00.00.00''  ) AS rec_patrimoniais
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.2%''                    ) AS alienacao_bens
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.5%''                    ) AS outras_rec_cap
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.99.00.10.00.00''  ) AS comp_prev
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.9.9.0.99%''             ) AS outras_rec
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''9.0.0.0.0.00.00.00.00.00'') AS deducoes_receita
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''7.0.0.%''                 ) AS receitas_prev_intra
                   UNION
    ';
    END IF;
    stSql := stSql || '
                  SELECT ''02''::VARCHAR AS cod_tipo
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.01.00.00.00''  ) AS contrib_pat
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.07%''          ) AS contrib_serv_ativo
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.09%''          ) AS contrib_serv_inat_pens
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.3.0.0.00.00.00.00.00''  ) AS rec_patrimoniais
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.2%''                    ) AS alienacao_bens
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.5%''                    ) AS outras_rec_cap
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.99.00.10.00.00''  ) AS comp_prev
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.9.9.0.99%''             ) AS outras_rec
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''9.0.0.0.0.00.00.00.00.00'') AS deducoes_receita
                       , (SELECT SUM(COALESCE(valor_previsto,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''7.0.0.%''                 ) AS receitas_prev_intra
                   UNION
                  SELECT ''04''::VARCHAR AS cod_tipo
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.01.00.00.00''  ) AS contrib_pat
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.07%''          ) AS contrib_serv_ativo
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.29.09%''          ) AS contrib_serv_inat_pens
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.3.0.0.00.00.00.00.00''  ) AS rec_patrimoniais
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.2%''                    ) AS alienacao_bens
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''2.5%''                    ) AS outras_rec_cap
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.2.1.0.99.00.10.00.00''  ) AS comp_prev
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''1.9.9.0.99%''             ) AS outras_rec
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''9.0.0.0.0.00.00.00.00.00'') AS deducoes_receita
                       , (SELECT SUM(COALESCE(arrecadado_periodo,0.00))::VARCHAR AS valor FROM tmp_balancete_receita WHERE cod_estrutural LIKE ''7.0.0.%''                 ) AS receitas_prev_intra
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
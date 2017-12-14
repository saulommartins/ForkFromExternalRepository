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
    * Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 16/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id: despFuncaoSubfuncao.plsql 63314 2015-08-17 13:48:57Z franver $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_desp_funcao_subfuncao(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;    
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    
    inPeriodo           VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN
    
    inPeriodo := EXTRACT( month FROM TO_DATE(stDtInicial,'dd/mm/yyyy') );

    stSql := '
    CREATE TEMPORARY TABLE tmp_arquivo AS (
        SELECT ' || inPeriodo || ' AS mes
             , CASE WHEN (sw_cgm.nom_cgm ILIKE ''%prefeitura%'') THEN CAST(''01'' AS VARCHAR)
                    WHEN (sw_cgm.nom_cgm ILIKE ''%instituto%'')  THEN CAST(''02'' AS VARCHAR)
                    WHEN (sw_cgm.nom_cgm ILIKE ''%câmara%'')     THEN CAST(''03'' AS VARCHAR)
               END AS cod_vinculo
             , CAST(LPAD(despesa.cod_funcao::VARCHAR, 2, ''0'') AS VARCHAR) AS cod_funcao
             , CAST(LPAD(despesa.cod_subfuncao::VARCHAR, 3, ''0'') AS VARCHAR) AS cod_subfuncao
             , CASE WHEN EXTRACT( month FROM TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'') ) = 1
                    THEN COALESCE(sum(despesa.vl_original), 0.00)
                    ELSE 0.00
                END AS vl_inicial
             , COALESCE((sum(coalesce(despesa.vl_original,0.00)) + (sum(coalesce(suplementado.vl_suplementado,0.00)) - sum(coalesce(reduzido.vl_reduzido,0.00)))), 0.00) as vl_atualizada
             , sum(COALESCE((SELECT * FROM tcemg.fn_desp_funcao_subfuncao_empenhada(despesa.cod_despesa, ''' || stExercicio || ''', ''' || stCodEntidade || ''', ''' || stDtInicial || ''', ''' || stDtFinal || ''' )), 0.00)) AS vl_empenhado
             , sum(COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada_anexo2(despesa.cod_despesa, ''' || stExercicio || ''', ''' || stCodEntidade || ''', ''' || stDtInicial || ''', ''' || stDtFinal || ''', false )), 0.00)) AS vl_liquidado
             , sum(COALESCE((SELECT * FROM tcemg.fn_desp_funcao_subfuncao_anulada(despesa.cod_despesa, ''' || stExercicio || ''', ''' || stCodEntidade || ''', ''' || stDtInicial || ''', ''' || stDtFinal || ''' )), 0.00)) AS vl_anulada
             , despesa.cod_entidade AS cod_entidade_relacionada
          FROM orcamento.despesa
          JOIN orcamento.funcao
            ON funcao.exercicio  = despesa.exercicio
           AND funcao.cod_funcao = despesa.cod_funcao
          JOIN orcamento.subfuncao 
            ON subfuncao.exercicio = despesa.exercicio
           AND subfuncao.cod_subfuncao = despesa.cod_subfuncao
          JOIN orcamento.conta_despesa
            ON conta_despesa.cod_conta = despesa.cod_conta
           AND conta_despesa.exercicio = despesa.exercicio
          JOIN orcamento.entidade
            ON entidade.cod_entidade = despesa.cod_entidade
           AND entidade.exercicio    = despesa.exercicio
          JOIN sw_cgm
            ON sw_cgm.numcgm = entidade.numcgm
     LEFT JOIN ( SELECT suplementacao_suplementada.exercicio
                      , suplementacao_suplementada.cod_despesa
                      , sum(suplementacao_suplementada.valor) AS vl_suplementado
                   FROM orcamento.suplementacao   
                      , orcamento.suplementacao_suplementada 
                  WHERE suplementacao.exercicio         = suplementacao_suplementada.exercicio
                    AND suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                    AND TO_DATE(suplementacao.dt_suplementacao::VARCHAR,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''', ''dd/mm/yyyy'') AND TO_DATE(''' || stDtFinal || ''', ''dd/mm/yyyy'')
               GROUP BY suplementacao_suplementada.exercicio
                      , suplementacao_suplementada.cod_despesa
               ORDER BY suplementacao_suplementada.cod_despesa ) AS suplementado
            ON suplementado.exercicio   = despesa.exercicio
           AND suplementado.cod_despesa = despesa.cod_despesa
     LEFT JOIN ( SELECT suplementacao_reducao.exercicio
                      , suplementacao_reducao.cod_despesa
                      , sum(suplementacao_reducao.valor) AS vl_reduzido
                   FROM orcamento.suplementacao 
                      , orcamento.suplementacao_reducao 
                  WHERE suplementacao.exercicio         = suplementacao_reducao.exercicio
                    AND suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                    AND suplementacao.exercicio         = ''' || stExercicio || '''
                    AND TO_DATE(suplementacao.dt_suplementacao::VARCHAR,''yyyy-mm-dd'') BETWEEN TO_DATE(''01/01/'||stExercicio||''' , ''dd/mm/yyyy'') AND TO_DATE(''' || stDtFinal || ''', ''dd/mm/yyyy'')
               GROUP BY suplementacao_reducao.exercicio
                      , suplementacao_reducao.cod_despesa
               ORDER BY suplementacao_reducao.cod_despesa ) AS reduzido
            ON reduzido.exercicio   = despesa.exercicio
           AND reduzido.cod_despesa = despesa.cod_despesa
         WHERE despesa.exercicio = ''' || stExercicio || '''
           AND despesa.cod_entidade IN (' || stCodEntidade || ')
           AND substring(conta_despesa.cod_estrutural, 5, 3) <> ''9.1''
      GROUP BY despesa.cod_funcao
             , despesa.cod_subfuncao
             , despesa.cod_entidade
             , sw_cgm.nom_cgm
      ORDER BY despesa.cod_entidade
             , despesa.cod_funcao
             , despesa.cod_subfuncao )';
    
    EXECUTE stSql;

    stSql := ' SELECT mes
                    , cod_vinculo
                    , COALESCE(vl_inicial, 0.00) AS vl_inicial
                    , COALESCE(vl_atualizada, 0.00) AS vl_atualizada
                    , COALESCE(vl_empenhado, 0.00) AS vl_empenhado
                    , COALESCE(vl_liquidado, 0.00) AS vl_liquidado
                    , COALESCE(vl_anulada, 0.00) AS vl_anulada
                    , cod_funcao AS cod_funcao
                    , cod_subfuncao AS cod_subfuncao
                    , cod_entidade_relacionada
                 FROM tmp_arquivo ORDER BY cod_vinculo, cod_funcao, cod_subfuncao; ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';
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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 5
    * Data de Criação   : 10/06/2008


    * @author Analista Alexandre Melo
    * @author Desenvolvedor Henrique Girardi dos Santos
    
    * @package URBEM
    * @subpackage 

    * @ignore

    * Casos de uso : uc-06.01.04

    $Id: relatorioRREOAnexo4_despesa.plsql 61499 2015-01-26 19:45:57Z evandro $
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo4_despesas(varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;    
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    dtInicioAno             VARCHAR := '';
    dtFimAno                VARCHAR := '';
    stSql                   VARCHAR := '';
    stSql1                  VARCHAR := '';
    stMascClassReceita      VARCHAR := '';
    stMascRecurso           VARCHAR := '';
    reRegistro              RECORD;
    dtInicialAnterior       varchar := ''; 
    dtFinalAnterior         varchar := '';
    stExercicioAnterior     varchar := '';
    dtInicioAnoAnterior     varchar := '';
    arDatas varchar[] ;

BEGIN
        
        stExercicioAnterior :=  trim(to_char((to_number(stExercicio, '99999')-1), '99999'));
        dtInicioAno := '01/01/' || stExercicio;
        dtInicioAnoAnterior := '01/01/' || stExercicioAnterior;
        dtInicialAnterior := SUBSTRING(stDataInicial,0,6) || stExercicioAnterior;
        dtFinalAnterior := SUBSTRING(stDataFinal,0,6) || stExercicioAnterior;        
        
        stSql := '
        CREATE TEMPORARY TABLE tmp_despesa_lib AS (
            SELECT
                cd.exercicio,
                cd.cod_estrutural, 
                d.cod_entidade,
                d.num_orgao,
                d.num_unidade, 
                d.cod_despesa,
                d.cod_funcao,
                d.cod_subfuncao, 
                d.cod_recurso, 
                SUM(COALESCE(d.vl_original,0.00)) AS vl_original,
                COALESCE((SUM(COALESCE(sups.vl_suplementado,0.00)) - SUM(COALESCE(supr.vl_reduzido,0.00))), 0.00) AS vl_credito_adicional 
            FROM
                orcamento.conta_despesa cd
                INNER JOIN
                orcamento.despesa d ON
                    d.exercicio = cd.exercicio AND
                    d.cod_conta = cd.cod_conta
            
            --Suplementacoes
        
            LEFT JOIN (
                SELECT
                    sups.exercicio, 
                    sups.cod_despesa, 
                    SUM(sups.valor) AS vl_suplementado 
                FROM
                    orcamento.suplementacao sup
                    INNER JOIN 
                    orcamento.suplementacao_suplementada sups ON
                        sup.exercicio = sups.exercicio AND
                        sup.cod_suplementacao = sups.cod_suplementacao 
                WHERE 
                    sup.exercicio = ' || quote_literal(stExercicio) || '  AND
                    sup.dt_suplementacao BETWEEN TO_DATE(' || quote_literal(dtInicioAno) || ', ''dd/mm/yyyy'') AND
                                                 TO_DATE(' || quote_literal(stDataFinal) || ', ''dd/mm/yyyy'') 
                    
                GROUP BY
                    sups.exercicio, 
                    sups.cod_despesa
            ) sups ON
                sups.exercicio = d.exercicio AND 
                sups.cod_despesa = d.cod_despesa 
            
            LEFT JOIN (
                SELECT
                    supr.exercicio, 
                    supr.cod_despesa, 
                    SUM(supr.valor) as vl_reduzido 
                FROM 
                    orcamento.suplementacao sup
                    INNER JOIN
                    orcamento.suplementacao_reducao supr ON
                        sup.exercicio = supr.exercicio AND 
                        sup.cod_suplementacao = supr.cod_suplementacao 
                WHERE 
                    sup.exercicio = ' || quote_literal(stExercicio) || '  AND
                    sup.dt_suplementacao BETWEEN TO_DATE(' || quote_literal(dtInicioAno) || ', ''dd/mm/yyyy'') AND
                                             TO_DATE(' || quote_literal(stDataFinal) || ', ''dd/mm/yyyy'') 
                GROUP BY 
                    supr.exercicio, 
                    supr.cod_despesa
            ) AS supr ON
              supr.exercicio = d.exercicio AND 
              supr.cod_despesa = d.cod_despesa 
            
            WHERE 
                cd.exercicio = ' || quote_literal(stExercicio) || ' AND 
                d.cod_entidade IN (' || stCodEntidades || ') 
            
            GROUP BY 
                cd.exercicio, 
                cd.cod_estrutural, 
                d.cod_entidade, 
                d.num_orgao, 
                d.num_unidade, 
                d.cod_despesa, 
                d.cod_funcao, 
                d.cod_subfuncao, 
                d.cod_recurso 
            )
            ';
        EXECUTE stSql;
        
        
        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS (
            SELECT
                    cast(1 as integer) AS grupo
                  , cast(1 as integer) as nivel
                  , conta_despesa.descricao as nom_conta
                  , conta_despesa.cod_estrutural
                  , sum(tmp_despesa_lib.vl_original) as vl_original
                  , (coalesce(sum(tmp_despesa_lib.vl_original),0.00)) + (coalesce(sum(tmp_despesa_lib.vl_credito_adicional),0.00))  as vl_suplementacoes 
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), ' || quote_literal(stExercicio)         || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(stDataInicial)       || ', ' || quote_literal(stDataFinal)     || ', true )), 0.00) AS vl_empenhado_bimestre
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), ' || quote_literal(stExercicio)         || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(dtInicioAno)         || ', ' || quote_literal(stDataFinal)     || ', true )), 0.00) AS vl_empenhado_ate_bimestre
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_empenhada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), ' || quote_literal(stExercicioAnterior) || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(dtInicioAnoAnterior) || ', ' || quote_literal(dtFinalAnterior) || ', true )), 0.00) AS vl_empenhado_ate_bimestre_anterior
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), ' || quote_literal(stExercicio)         || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(stDataInicial)       || ', ' || quote_literal(stDataFinal)     || ', true )), 0.00) AS vl_liquidado_bimestre
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), ' || quote_literal(stExercicio)         || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(dtInicioAno)         || ', ' || quote_literal(stDataFinal)     || ', true )), 0.00) AS vl_liquidado_ate_bimestre
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), ' || quote_literal(stExercicioAnterior) || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(dtInicioAnoAnterior) || ', ' || quote_literal(dtFinalAnterior) || ', true )), 0.00) AS vl_liquidado_ate_bimestre_anterior
                  
                  --Calcula os restos nao processados do exercicio anterior
                  , COALESCE((SELECT * FROM stn.fn_rreo_anexo4_despesas_restos_nao_processados(' || quote_literal(stExercicio) || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(stDataFinal) || ', conta_despesa.cod_estrutural, ''nao_processados_exercicio_anterior'')),0.00) AS vl_restos_nao_processados_exercicio_anterior
                  --Calcula os restos nao processados do exercicio
                  , COALESCE((SELECT * FROM stn.fn_rreo_anexo4_despesas_restos_nao_processados(' || quote_literal(stExercicio) || ', ' || quote_literal(stCodEntidades) || ', ' || quote_literal(stDataFinal) || ', conta_despesa.cod_estrutural, ''nao_processados_exercicio'')),0.00) AS vl_restos_nao_processados_exercicio

            FROM orcamento.despesa
            LEFT JOIN   orcamento.conta_despesa
                    ON  conta_despesa.cod_conta = despesa.cod_conta
                    AND conta_despesa.exercicio = despesa.exercicio
            
            
            LEFT JOIN tmp_despesa_lib
                   ON tmp_despesa_lib.exercicio    = despesa.exercicio 
                  AND tmp_despesa_lib.cod_despesa  = despesa.cod_despesa 
                       
            where despesa.exercicio = ' || quote_literal(stExercicio) || '
                and despesa.cod_entidade IN (' || stCodEntidades || ')                 
                -- and (conta_despesa.cod_estrutural ilike ''3.%''
                --      or conta_despesa.cod_estrutural ilike ''4.%''
                --      or conta_despesa.cod_estrutural ilike ''9.%''
                --      or conta_despesa.cod_estrutural ilike ''7.%'')
                
            group by    conta_despesa.cod_estrutural, conta_despesa.descricao
            order by    conta_despesa.cod_estrutural    
        ) '; 

        EXECUTE stSql;

    
    UPDATE tmp_despesa SET nom_conta = 'Aposentadorias'                    WHERE cod_estrutural = '3.3.9.0.01.00.00.00.00';
    UPDATE tmp_despesa SET nom_conta = 'Pensões'                           WHERE cod_estrutural = '3.3.9.0.03.00.00.00.00';
    UPDATE tmp_despesa SET nom_conta = 'Outros Benefícios Previdenciários' WHERE cod_estrutural = '3.3.9.0.05.00.00.00.00';
    UPDATE tmp_despesa SET nom_conta = 'Outros Benefícios Previdenciários' 
                         , cod_estrutural = '3.3.9.0.05.00.00.00.00' WHERE cod_estrutural = '3.3.9.0.09.00.00.00.00';
    
    INSERT INTO tmp_despesa VALUES (3, 3, 'Pessoal Militar', 'militar1');
    INSERT INTO tmp_despesa VALUES (3, 4, 'Reformas', 'militar2');
    INSERT INTO tmp_despesa VALUES (3, 4, 'Pensões' , 'militar3');
    INSERT INTO tmp_despesa VALUES (3, 4, 'Outros Benefícios Previdenciários', 'militar4');
    
    stSql := '
        SELECT cast(1 as integer) as grupo
            ,  cast(1 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''DESPESAS PREVIDENCIÁRIAS - RPPS (EXCETO INTRA-ORÇAMENTÁRIAS) (IV)'' as varchar) as nom_conta
            ,  sum(coalesce(vl_original                                 , 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes                           , 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado_bimestre                       , 0.00)) as vl_empenhado_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre                   , 0.00)) as vl_empenhado_ate_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre_anterior          , 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre                       , 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre                   , 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior          , 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE (cod_estrutural LIKE ''3.%''
        or  cod_estrutural LIKE ''4.%''
        or  cod_estrutural LIKE ''9.%'')
        and cod_estrutural NOT LIKE ''%.9.1.%''
    
    UNION ALL
    

        SELECT cast(1 as integer) as grupo
            ,  cast(2 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''ADMINISTRAÇÃO'' as varchar) as nom_conta
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado_bimestre, 0.00)) as vl_empenhado_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre, 0.00)) as vl_empenhado_ate_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre_anterior, 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre, 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre, 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior, 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE (cod_estrutural LIKE ''3.%''
        or  cod_estrutural LIKE ''4.%''
        or  cod_estrutural LIKE ''9.%'')
        and cod_estrutural NOT LIKE ''%.9.1.%''
        and cod_estrutural NOT IN ( ''3.3.9.0.01.00.00.00.00''
                            , ''3.3.9.0.03.00.00.00.00''
                            , ''3.3.9.0.05.00.00.00.00'')
    
    UNION ALL
    
        SELECT cast(1 as integer) as grupo
            ,  cast(3 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''Despesas Correntes'' as varchar) as nom_conta
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado_bimestre, 0.00)) as vl_empenhado_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre, 0.00)) as vl_empenhado_ate_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre_anterior, 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre, 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre, 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior, 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE (cod_estrutural LIKE ''3.%''
        or  cod_estrutural LIKE ''9.%'')
        and cod_estrutural NOT LIKE ''%.9.1.%''
        and cod_estrutural NOT IN ( ''3.3.9.0.01.00.00.00.00''
                            , ''3.3.9.0.03.00.00.00.00''
                            , ''3.3.9.0.05.00.00.00.00'')
    
    
    UNION ALL
    
        SELECT cast(1 as integer) as grupo
            ,  cast(3 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''Despesas de Capital'' as varchar) as nom_conta
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado_bimestre, 0.00)) as vl_empenhado_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre, 0.00)) as vl_empenhado_ate_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre_anterior, 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre, 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre, 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior, 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural LIKE ''4.%''
    
    
    UNION ALL
    
        SELECT cast(2 as integer) as grupo
            ,  cast(2 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''PREVIDÊNCIA'' as varchar) as nom_conta
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado_bimestre, 0.00)) as vl_empenhado_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre, 0.00)) as vl_empenhado_ate_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre_anterior, 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre, 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre, 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior, 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural IN ( ''3.3.9.0.01.00.00.00.00''
                                , ''3.3.9.0.03.00.00.00.00''
                                , ''3.3.9.0.05.00.00.00.00'')
        
    UNION ALL
    
        SELECT cast(2 as integer) as grupo
            ,  cast(3 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''Pessoal Civil'' as varchar) as nom_conta
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado_bimestre, 0.00)) as vl_empenhado_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre, 0.00)) as vl_empenhado_ate_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre_anterior, 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre, 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre, 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior, 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural IN ( ''3.3.9.0.01.00.00.00.00''
                                , ''3.3.9.0.03.00.00.00.00''
                                , ''3.3.9.0.05.00.00.00.00'')
                            
    UNION ALL
    
        SELECT cast(2 as integer) as grupo
            ,  cast(4 as integer) as nivel
            ,  cod_estrutural
            ,  nom_conta
            ,  coalesce(vl_original, 0.00) as vl_original
            ,  coalesce(vl_suplementacoes, 0.00) as vl_suplementacoes
            ,  coalesce(vl_empenhado_bimestre, 0.00) as vl_empenhado_bimestre
            ,  coalesce(vl_empenhado_ate_bimestre, 0.00) as vl_empenhado_ate_bimestre
            ,  coalesce(vl_empenhado_ate_bimestre_anterior, 0.00) as vl_empenhado_ate_bimestre_anterior
            ,  coalesce(vl_liquidado_bimestre, 0.00) as vl_liquidado_bimestre
            ,  coalesce(vl_liquidado_ate_bimestre, 0.00) as vl_liquidado_ate_bimestre
            ,  coalesce(vl_liquidado_ate_bimestre_anterior, 0.00) as vl_liquidado_ate_bimestre_anterior
            ,  coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00) as vl_restos_nao_processados_exercicio_anterior
            ,  coalesce(vl_restos_nao_processados_exercicio         , 0.00) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural = ''3.3.9.0.01.00.00.00.00''
        GROUP BY  grupo
                , nivel 
                , cod_estrutural
                , nom_conta
                , vl_original
                , vl_suplementacoes
                , vl_empenhado_bimestre
                , vl_empenhado_ate_bimestre
                , vl_empenhado_ate_bimestre_anterior
                , vl_liquidado_bimestre
                , vl_liquidado_ate_bimestre
                , vl_liquidado_ate_bimestre_anterior
                , vl_restos_nao_processados_exercicio_anterior
                , vl_restos_nao_processados_exercicio
    UNION ALL
    
        SELECT cast(2 as integer) as grupo
            ,  cast(4 as integer) as nivel
            ,  cod_estrutural
            ,  nom_conta
            ,  coalesce(vl_original, 0.00) as vl_original
            ,  coalesce(vl_suplementacoes, 0.00) as vl_suplementacoes
            ,  coalesce(vl_empenhado_bimestre, 0.00) as vl_empenhado_bimestre
            ,  coalesce(vl_empenhado_ate_bimestre, 0.00) as vl_empenhado_ate_bimestre
            ,  coalesce(vl_empenhado_ate_bimestre_anterior, 0.00) as vl_empenhado_ate_bimestre_anterior
            ,  coalesce(vl_liquidado_bimestre, 0.00) as vl_liquidado_bimestre
            ,  coalesce(vl_liquidado_ate_bimestre, 0.00) as vl_liquidado_ate_bimestre
            ,  coalesce(vl_liquidado_ate_bimestre_anterior, 0.00) as vl_liquidado_ate_bimestre_anterior
            ,  coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00) as vl_restos_nao_processados_exercicio_anterior
            ,  coalesce(vl_restos_nao_processados_exercicio         , 0.00) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural = ''3.3.9.0.03.00.00.00.00''
        GROUP BY  grupo
                , nivel 
                , cod_estrutural
                , nom_conta
                , vl_original
                , vl_suplementacoes
                , vl_empenhado_bimestre
                , vl_empenhado_ate_bimestre
                , vl_empenhado_ate_bimestre_anterior
                , vl_liquidado_bimestre
                , vl_liquidado_ate_bimestre
                , vl_liquidado_ate_bimestre_anterior
                , vl_restos_nao_processados_exercicio_anterior
                , vl_restos_nao_processados_exercicio
    UNION ALL
    
        SELECT cast(2 as integer) as grupo
            ,  cast(4 as integer) as nivel
            ,  cod_estrutural
            ,  nom_conta
            ,  SUM(coalesce(vl_original, 0.00)) as vl_original
            ,  SUM(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  SUM(coalesce(vl_empenhado_bimestre, 0.00)) as vl_empenhado_bimestre
            ,  SUM(coalesce(vl_empenhado_ate_bimestre, 0.00)) as vl_empenhado_ate_bimestre
            ,  SUM(coalesce(vl_empenhado_ate_bimestre_anterior, 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre, 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre, 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior, 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural = ''3.3.9.0.05.00.00.00.00''
        GROUP BY  grupo
                , nivel 
                , cod_estrutural
                , nom_conta
    
    UNION ALL
    
        SELECT grupo
            ,  nivel
            ,  cod_estrutural
            ,  nom_conta
            ,  coalesce(vl_original, 0.00) as vl_original
            ,  coalesce(vl_suplementacoes, 0.00) as vl_suplementacoes
            ,  coalesce(vl_empenhado_bimestre, 0.00) as vl_empenhado_bimestre
            ,  coalesce(vl_empenhado_ate_bimestre, 0.00) as vl_empenhado_ate_bimestre
            ,  coalesce(vl_empenhado_ate_bimestre_anterior, 0.00) as vl_empenhado_ate_bimestre_anterior
            ,  coalesce(vl_liquidado_bimestre, 0.00) as vl_liquidado_bimestre
            ,  coalesce(vl_liquidado_ate_bimestre, 0.00) as vl_liquidado_ate_bimestre
            ,  coalesce(vl_liquidado_ate_bimestre_anterior, 0.00) as vl_liquidado_ate_bimestre_anterior
            ,  coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00) as vl_restos_nao_processados_exercicio_anterior
            ,  coalesce(vl_restos_nao_processados_exercicio         , 0.00) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural like ''militar%''
        GROUP BY  grupo
                , nivel 
                , cod_estrutural
                , nom_conta
                , vl_original
                , vl_suplementacoes
                , vl_empenhado_bimestre
                , vl_empenhado_ate_bimestre
                , vl_empenhado_ate_bimestre_anterior
                , vl_liquidado_bimestre
                , vl_liquidado_ate_bimestre
                , vl_liquidado_ate_bimestre_anterior
                , vl_restos_nao_processados_exercicio_anterior
                , vl_restos_nao_processados_exercicio
    UNION ALL
    
        SELECT cast(4 as integer) as grupo
            ,  cast(3 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''Outras Despesas Previdenciárias'' as varchar) as nom_conta
            ,  cast(0.00 as numeric(14,2)) as vl_original
            ,  cast(0.00 as numeric(14,2)) as vl_suplementacoes
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_ate_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_ate_bimestre_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_ate_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_ate_bimestre_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_restos_nao_processados_exercicio_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_restos_nao_processados_exercicio
        
    UNION ALL
    
        SELECT cast(4 as integer) as grupo
            ,  cast(4 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''Compensação Previdenciária do RPPS para o RGPS'' as varchar) as nom_conta
            ,  cast(0.00 as numeric(14,2)) as vl_original
            ,  cast(0.00 as numeric(14,2)) as vl_suplementacoes
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_ate_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_ate_bimestre_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_ate_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_ate_bimestre_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_restos_nao_processados_exercicio_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_restos_nao_processados_exercicio
        
    UNION ALL
    
        SELECT cast(4 as integer) as grupo
            ,  cast(4 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''Demais Despesas Previdenciárias'' as varchar) as nom_conta
            ,  cast(0.00 as numeric(14,2)) as vl_original
            ,  cast(0.00 as numeric(14,2)) as vl_suplementacoes
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_ate_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_empenhado_ate_bimestre_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_ate_bimestre
            ,  cast(0.00 as numeric(14,2)) as vl_liquidado_ate_bimestre_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_restos_nao_processados_exercicio_anterior
            ,  cast(0.00 as numeric(14,2)) as vl_restos_nao_processados_exercicio
    
    UNION ALL
    
        SELECT cast(5 as integer) as grupo
            ,  cast(2 as integer) as nivel
            ,  cast('''' as varchar) as cod_estrutural
            ,  cast(''DESPESAS PREVIDENCIÁRIAS - RPPS (INTRA-ORÇAMENTÁRIAS) (V)'' as varchar) as nom_conta 
            ,  sum(coalesce(vl_original, 0.00)) as vl_original
            ,  sum(coalesce(vl_suplementacoes, 0.00)) as vl_suplementacoes
            ,  sum(coalesce(vl_empenhado_bimestre, 0.00)) as vl_empenhado_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre, 0.00)) as vl_empenhado_ate_bimestre
            ,  sum(coalesce(vl_empenhado_ate_bimestre_anterior, 0.00)) as vl_empenhado_ate_bimestre_anterior
            ,  sum(coalesce(vl_liquidado_bimestre, 0.00)) as vl_liquidado_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre, 0.00)) as vl_liquidado_ate_bimestre
            ,  sum(coalesce(vl_liquidado_ate_bimestre_anterior, 0.00)) as vl_liquidado_ate_bimestre_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio_anterior, 0.00)) as vl_restos_nao_processados_exercicio_anterior
            ,  sum(coalesce(vl_restos_nao_processados_exercicio         , 0.00)) as vl_restos_nao_processados_exercicio
        FROM tmp_despesa
        WHERE cod_estrutural like ''%.9.1.%''

    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_despesa_lib;
    DROP TABLE tmp_despesa;

    RETURN;
END;
$$ language 'plpgsql';

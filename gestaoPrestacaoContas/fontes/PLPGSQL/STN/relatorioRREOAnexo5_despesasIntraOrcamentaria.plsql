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

    $Id: OCGeraRREOAnexo5.php 28716 2008-03-27 15:28:33Z lbbarreiro $
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo5_despesas_intra_orcamentarias(varchar,varchar, varchar, varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;
    dtInicioAno             VARCHAR := '''';
    stSql                   VARCHAR := '''';
    reRegistro              RECORD;
    dtInicialAnterior       varchar := '''';  
    dtFinalAnterior         varchar := '''';
    stExercicioAnterior     varchar := '''';
    dtInicioAnoAnterior     varchar := '''';
    arDatas varchar[] ;
    stDado                  varchar := '''';

BEGIN
        
        stExercicioAnterior :=  trim(to_char((to_number(stExercicio, ''99999'')-1), ''99999''));
        
        dtInicioAno := ''01/01/'' || stExercicio;
        
        dtInicioAnoAnterior := ''01/01/'' || stExercicioAnterior;
        
        dtInicialAnterior := SUBSTRING(dtInicial,0,6) || stExercicioAnterior;
        dtFinalAnterior := SUBSTRING(dtFinal,0,6) || stExercicioAnterior;  
        
        stSql := ''CREATE TEMPORARY TABLE tmp_despesa_intra AS (
            SELECT
                    cast(1 as integer) AS grupo
                  , cast(1 as integer) as nivel
                  , funcao.descricao as nom_funcao
                  ,	conta_despesa.cod_estrutural
                  , sum(tmp_despesa_lib.vl_original) as vl_original
                  , (coalesce(sum(tmp_despesa_lib.vl_original),0.00)) + (coalesce(sum(tmp_despesa_lib.vl_credito_adicional),0.00))  as vl_suplementacoes 
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), '''''' || stExercicio || '''''', '''''' ||  stCodEntidades ||'''''', ''''''||dtInicial||'''''',   ''''''||dtFinal||'''''', true )), 0.00) AS vl_empenhado_bimestre
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), '''''' || stExercicio || '''''', '''''' ||  stCodEntidades ||'''''', ''''''||dtInicioAno||'''''', ''''''||dtFinal||'''''', true )), 0.00) AS vl_empenhado_ate_bimestre
                  , COALESCE((SELECT * FROM stn.fn_rreo_despesa_liquidada( publico.fn_mascarareduzida(conta_despesa.cod_estrutural), '''''' || stExercicioAnterior || '''''', '''''' ||  stCodEntidades ||'''''', ''''''||dtInicioAnoAnterior||'''''', ''''''||dtFinalAnterior||'''''', true )), 0.00) AS vl_empenhado_ate_bimestre_anterior
                  
            FROM orcamento.despesa
            LEFT JOIN 	orcamento.conta_despesa
                    ON	conta_despesa.cod_conta = despesa.cod_conta
                    AND	conta_despesa.exercicio = despesa.exercicio
            LEFT JOIN orcamento.funcao
                   ON   funcao.exercicio = despesa.exercicio 
                   AND  funcao.cod_funcao = despesa.cod_funcao
            
            LEFT JOIN tmp_despesa_lib
                   ON tmp_despesa_lib.exercicio    = despesa.exercicio 
                  AND tmp_despesa_lib.cod_despesa  = despesa.cod_despesa 
                       
            where despesa.exercicio = '''''' || stExercicio || ''''''
                and despesa.cod_entidade IN ('' || stCodEntidades || '') 
                and (conta_despesa.cod_estrutural ilike ''''3.%''''
                     or conta_despesa.cod_estrutural ilike ''''4.%'''')
                and conta_despesa.cod_estrutural ilike ''''%.9.1.%''''
            group by 	funcao.descricao, conta_despesa.cod_estrutural, conta_despesa.descricao
            order by 	conta_despesa.cod_estrutural
        ) ''; 
        
    EXECUTE stSql;
    
    SELECT nivel INTO stDado
    FROM tmp_despesa_intra;
    
    IF stDado IS NULL THEN
        INSERT INTO tmp_despesa_intra VALUES (1, 1, ''ADMINISTRACAO'', '''', 0.00, 0.00, 0.00, 0.00, 0.00 );
        INSERT INTO tmp_despesa_intra VALUES (1, 2, ''Despesas Correntes'', ''3.0'', 0.00, 0.00, 0.00, 0.00, 0.00 );
        INSERT INTO tmp_despesa_intra VALUES (1, 2, ''Despesas de Capital'', ''4.0'', 0.00, 0.00, 0.00, 0.00, 0.00 );
        
    ELSE
        stDado := '''';
        
        SELECT nivel INTO stDado
        FROM  tmp_despesa_intra
        WHERE tmp_despesa_intra.cod_estrutural ilike ''3.%''
              and tmp_despesa_intra.grupo = 1
        GROUP BY nivel;
        
        IF stDado IS NULL THEN
            INSERT INTO tmp_despesa_intra VALUES (1, 2, ''Despesas Correntes'', ''3.0'', 0.00, 0.00, 0.00, 0.00, 0.00 );
            
        ELSE
            
            stDado := '''';
            
            SELECT nivel INTO stDado
            FROM  tmp_despesa_intra
            WHERE tmp_despesa_intra.cod_estrutural ilike ''4.%''
                  and tmp_despesa_intra.grupo = 1
            GROUP BY nivel;
            
            
            IF stDado IS NULL THEN
              INSERT INTO tmp_despesa_intra VALUES (1, 2, ''Despesas de Capital'', ''4.0'', 0.00, 0.00, 0.00, 0.00, 0.00 );
            END IF;
            
        END IF;
    END IF;
    
    stSql := ''
    
        SELECT
                grupo
            ,   cast(1 as integer) as nivel
            ,   cast('''''''' as varchar) as estrutural
            ,   cast(''''ADMINISTRACAO'''' as varchar) as nom_funcao
            ,	COALESCE(SUM(vl_original), 0.00) AS vl_original
            ,	COALESCE(SUM(vl_suplementacoes), 0.00) AS vl_suplementacoes
            ,	COALESCE(SUM(vl_empenhado_bimestre), 0.00) AS vl_empenhado_bimestre
            ,	COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) AS vl_empenhado_ate_bimestre
            ,	COALESCE(SUM(vl_empenhado_ate_bimestre_anterior), 0.00) AS vl_empenhado_ate_bimestre_anterior
        FROM tmp_despesa_intra
        WHERE grupo = 1
        GROUP BY grupo
        
        UNION ALL 
        
        SELECT  grupo
            ,   cast(2 as integer) AS nivel
            ,   cast('''''''' as varchar) as estrutural
            ,   ''''Despesas Correntes'''' as nom_funcao
            ,	vl_original
            ,	vl_suplementacoes
            ,	vl_empenhado_bimestre
            ,	vl_empenhado_ate_bimestre
            ,	vl_empenhado_ate_bimestre_anterior
        
        FROM (
            SELECT
                    grupo
                ,	COALESCE(SUM(vl_original), 0.00) AS vl_original
                ,	COALESCE(SUM(vl_suplementacoes), 0.00) AS vl_suplementacoes
                ,	COALESCE(SUM(vl_empenhado_bimestre), 0.00) AS vl_empenhado_bimestre
                ,	COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) AS vl_empenhado_ate_bimestre
                ,	COALESCE(SUM(vl_empenhado_ate_bimestre_anterior), 0.00) AS vl_empenhado_ate_bimestre_anterior
            FROM tmp_despesa_intra
            WHERE tmp_despesa_intra.cod_estrutural ilike ''''3.%''''
              and tmp_despesa_intra.grupo = 1
            GROUP BY grupo
        ) AS tbl
        
        UNION ALL 
                
        SELECT  grupo
            ,   cast(2 as integer) AS nivel
            ,   cast('''''''' as varchar) as estrutural
            ,   ''''Despesas de Capital'''' AS nom_funcao
            ,	vl_original
            ,	vl_suplementacoes
            ,	vl_empenhado_bimestre
            ,	vl_empenhado_ate_bimestre
            ,	vl_empenhado_ate_bimestre_anterior
        
        FROM (
            SELECT
                    grupo
                ,   cast(2 as integer) AS nivel
                ,   cast('''''''' as varchar) as estrutural
                ,   ''''Despesas de Capital'''' AS nom_funcao
                ,	COALESCE(SUM(vl_original), 0.00) AS vl_original
                ,	COALESCE(SUM(vl_suplementacoes), 0.00) AS vl_suplementacoes
                ,	COALESCE(SUM(vl_empenhado_bimestre), 0.00) AS vl_empenhado_bimestre
                ,	COALESCE(SUM(vl_empenhado_ate_bimestre), 0.00) AS vl_empenhado_ate_bimestre
                ,	COALESCE(SUM(vl_empenhado_ate_bimestre_anterior), 0.00) AS vl_empenhado_ate_bimestre_anterior
            FROM tmp_despesa_intra
            WHERE tmp_despesa_intra.cod_estrutural ilike ''''4.%''''
              and tmp_despesa_intra.grupo = 1
            GROUP BY grupo
        ) AS tbl
        
        ORDER BY nom_funcao
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_despesa_intra;
    DROP TABLE tmp_despesa_lib;

    RETURN;
END;
'language 'plpgsql';

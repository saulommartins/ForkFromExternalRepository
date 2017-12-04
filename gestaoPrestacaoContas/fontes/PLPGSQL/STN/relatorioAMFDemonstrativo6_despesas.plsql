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
    * Script de função PLPGSQL - Relatório STN - AMF - Demonstrativo VI
    * Data de Criação   : 26/06/2008


    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Henrique Girardi dos Santos
    
    * @package URBEM
    * @subpackage 

    * @ignore

    * Casos de uso : uc-06.01.36

    $Id: $
*/

CREATE OR REPLACE FUNCTION stn.fn_amf_demonstrativo6_despesas(varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    ALIAS FOR $1;
    stCodEntidades ALIAS FOR $2;
    dtInicial2     VARCHAR := '';
    dtFinal2       VARCHAR := '';
    dtInicial3     VARCHAR := '';
    dtFinal3       VARCHAR := '';
    dtInicial4     VARCHAR := '';
    dtFinal4       VARCHAR := '';
    stExercicio2   VARCHAR := '';
    stExercicio3   VARCHAR := '';
    stExercicio4   VARCHAR := '';
    stSql          VARCHAR := '';
    stDado         VARCHAR := '';
    reRegistro     RECORD;
BEGIN
    
    stExercicio2 := trim(to_char((to_number(stExercicio, '99999')-2), '99999'));
    stExercicio3 := trim(to_char((to_number(stExercicio, '99999')-3), '99999'));
    stExercicio4 := trim(to_char((to_number(stExercicio, '99999')-4), '99999'));
    
    dtInicial2 := '01/01/' || stExercicio2;
    dtFinal2   := '31/12/' || stExercicio2;
    
    dtInicial3 := '01/01/' || stExercicio3;
    dtFinal3   := '31/12/' || stExercicio3;
    
    dtInicial4 := '01/01/' || stExercicio4;
    dtFinal4   := '31/12/' || stExercicio4;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS (
            SELECT
                    cast(1 as integer) AS grupo
                  , cast(1 as integer) as nivel
                  , conta_despesa.descricao as nom_conta
                  ,	conta_despesa.cod_estrutural
                  , sum(coalesce(ano2.vl_total,0.00)) as ano2
                  , sum(coalesce(ano3.vl_total,0.00)) as ano3
                  , sum(coalesce(ano4.vl_total,0.00)) as ano4
            FROM orcamento.despesa
            LEFT JOIN 	orcamento.conta_despesa
                    ON	conta_despesa.cod_conta = despesa.cod_conta
                    AND	conta_despesa.exercicio = despesa.exercicio
            LEFT JOIN orcamento.funcao
                   ON   funcao.exercicio = despesa.exercicio 
                   AND  funcao.cod_funcao = despesa.cod_funcao
                   
            LEFT JOIN( SELECT
                             sum(coalesce(ipe.vl_total, 0.00)) - sum(coalesce(item_empenho_anulado.vl_anulado,0.00))  as vl_total
                           , ped.cod_despesa
                        FROM
                             empenho.pre_empenho_despesa  as ped
                           , empenho.pre_empenho          as pe
                           , empenho.item_pre_empenho     as ipe
                             LEFT JOIN( SELECT eai.vl_anulado
                                             , eai.exercicio
                                             , eai.cod_pre_empenho
                                             , eai.num_item
                                          FROM empenho.empenho_anulado_item as eai
                                         WHERE to_date(eai.timestamp::VARCHAR,''yyyy-mm-dd'') BETWEEN to_date( '|| quote_literal(dtInicial2) ||', ''dd/mm/yyyy'')
                                                                     AND to_date( '|| quote_literal(dtFinal2) ||', ''dd/mm/yyyy'')  ) as item_empenho_anulado
                                    ON(     item_empenho_anulado.exercicio       = ipe.exercicio
                                        AND item_empenho_anulado.cod_pre_empenho = ipe.cod_pre_empenho
                                        AND item_empenho_anulado.num_item        = ipe.num_item )
                            , empenho.empenho  as e
                       WHERE
                             ped.exercicio = pe.exercicio
                         AND ped.cod_pre_empenho = pe.cod_pre_empenho
                         AND pe.cod_pre_empenho = ipe.cod_pre_empenho
            
                         AND pe.exercicio = ipe.exercicio
                         AND e.exercicio = pe.exercicio
                         AND e.cod_pre_empenho = pe.cod_pre_empenho
                         AND e.exercicio = '|| quote_literal(stExercicio2) ||'
                         AND e.cod_entidade IN ('|| stCodEntidades ||')
                         AND to_date(e.dt_empenho::VARCHAR,''yyyy-mm-dd'') BETWEEN to_date('|| quote_literal(dtInicial2) ||', ''dd/mm/yyyy'') -- DATA INCIAL DO BIMESTRE
                                              AND to_date('|| quote_literal(dtFinal2) ||', ''dd/mm/yyyy'')
                    GROUP BY ped.cod_despesa                                                   ) as ano2
                  ON( ano2.cod_despesa = despesa.cod_despesa )
            
            
            LEFT JOIN( SELECT  sum(coalesce(ipe.vl_total, 0.00)) - sum(coalesce(item_empenho_anulado.vl_anulado,0.00))  as vl_total
                           , ped.cod_despesa
                        FROM
                             empenho.pre_empenho_despesa  as ped
                           , empenho.pre_empenho          as pe
                           , empenho.item_pre_empenho     as ipe
                             LEFT JOIN( SELECT eai.vl_anulado
                                             , eai.exercicio
                                             , eai.cod_pre_empenho
                                             , eai.num_item
                                          FROM empenho.empenho_anulado_item as eai
                                         WHERE to_date(eai.timestamp::VARCHAR,''yyyy-mm-dd'') BETWEEN to_date( '|| quote_literal(dtInicial3) ||', ''dd/mm/yyyy'')
                                                                     AND to_date( '|| quote_literal(dtFinal3) ||', ''dd/mm/yyyy'')  ) as item_empenho_anulado
                                    ON(     item_empenho_anulado.exercicio       = ipe.exercicio
                                        AND item_empenho_anulado.cod_pre_empenho = ipe.cod_pre_empenho
                                        AND item_empenho_anulado.num_item        = ipe.num_item )
                           , empenho.empenho              as e
                       WHERE
                             ped.exercicio = pe.exercicio
                         AND ped.cod_pre_empenho = pe.cod_pre_empenho
                         AND pe.cod_pre_empenho = ipe.cod_pre_empenho
                         AND pe.exercicio = ipe.exercicio
                         AND e.exercicio = pe.exercicio
                         AND e.cod_pre_empenho = pe.cod_pre_empenho
                         AND ped.exercicio = '|| quote_literal(stExercicio3) ||'
                         AND e.exercicio = '|| quote_literal(stExercicio3) ||'
                         AND e.cod_entidade IN ('|| quote_literal(stCodEntidades) ||')
                         AND to_date(e.dt_empenho::VARCHAR,''yyyy-mm-dd'') BETWEEN to_date('|| quote_literal(dtInicial3) ||', ''dd/mm/yyyy'') -- DATA INCIAL DO EXERCICIO
                                              AND to_date('|| quote_literal(dtFinal3) ||', ''dd/mm/yyyy'')
                    GROUP BY ped.cod_despesa                                                     ) as ano3
                                    ON( ano3.cod_despesa = despesa.cod_despesa )
            
            LEFT JOIN( SELECT  sum(coalesce(ipe.vl_total, 0.00)) - sum(coalesce(item_empenho_anulado.vl_anulado,0.00))  as vl_total
                           , ped.cod_despesa
                        FROM
                             empenho.pre_empenho_despesa  as ped
                           , empenho.pre_empenho          as pe
                           , empenho.item_pre_empenho     as ipe
                             LEFT JOIN( SELECT eai.vl_anulado
                                             , eai.exercicio
                                             , eai.cod_pre_empenho
                                             , eai.num_item
                                          FROM empenho.empenho_anulado_item as eai
                                         WHERE to_date(eai.timestamp::VARCHAR,''yyyy-mm-dd'') BETWEEN to_date('|| quote_literal(dtInicial4) ||', ''dd/mm/yyyy'')
                                                                     AND to_date( '|| quote_literal(dtFinal4) ||', ''dd/mm/yyyy'')  ) as item_empenho_anulado
                                    ON(     item_empenho_anulado.exercicio       = ipe.exercicio
                                        AND item_empenho_anulado.cod_pre_empenho = ipe.cod_pre_empenho
                                        AND item_empenho_anulado.num_item        = ipe.num_item )
                           , empenho.empenho              as e
                       WHERE
                             ped.exercicio = pe.exercicio
                         AND ped.cod_pre_empenho = pe.cod_pre_empenho
                         AND pe.cod_pre_empenho = ipe.cod_pre_empenho
                         AND pe.exercicio = ipe.exercicio
                         AND e.exercicio = pe.exercicio
                         AND e.cod_pre_empenho = pe.cod_pre_empenho
                         AND ped.exercicio = '|| quote_literal(stExercicio4) ||'
                         AND e.exercicio = '|| quote_literal(stExercicio4) ||'
                         AND e.cod_entidade IN (' || quote_literal(stCodEntidades) || ')
                         AND to_date(e.dt_empenho::VARCHAR,''yyyy-mm-dd'') BETWEEN to_date('|| quote_literal(dtInicial4) ||', ''dd/mm/yyyy'') -- DATA INCIAL DO EXERCICIO
                                              AND to_date('|| quote_literal(dtFinal4) ||', ''dd/mm/yyyy'')
                    GROUP BY ped.cod_despesa ) as ano4
                            ON( ano4.cod_despesa = despesa.cod_despesa )
                  
            where despesa.exercicio IN  ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')
                and despesa.cod_entidade IN ('|| quote_literal(stCodEntidades) ||') 
                --and despesa.cod_funcao IN (4,9)
                and (conta_despesa.cod_estrutural ilike ''3.%''
                     or conta_despesa.cod_estrutural ilike ''4.%''
                     or conta_despesa.cod_estrutural ilike ''9.%'')
                
            group by 	funcao.descricao, conta_despesa.cod_estrutural, conta_despesa.descricao
            order by 	funcao.descricao, conta_despesa.cod_estrutural    
        ) '; 

    EXECUTE stSql;
    
    SELECT nivel INTO stDado
    FROM tmp_despesa
    WHERE (tmp_despesa.cod_estrutural ilike '3.%'
           OR tmp_despesa.cod_estrutural ilike '4.%')
      AND tmp_despesa.cod_estrutural ilike '%.9.1.%';
      
    IF stDado IS NULL THEN
        INSERT INTO tmp_despesa VALUES (4, 3, 'Despesas Correntes' , 'intra2', 0.00, 0.00, 0.00 );
        INSERT INTO tmp_despesa VALUES (4, 3, 'Despesas de Capital', 'intra3', 0.00, 0.00, 0.00 );
        
    ELSE
        stDado := '';
        
        SELECT nivel INTO stDado
        FROM  tmp_despesa
        WHERE tmp_despesa.cod_estrutural ilike '3.%'
              and tmp_despesa.grupo = 1
        GROUP BY nivel;
        
        IF stDado IS NULL THEN
            INSERT INTO tmp_despesa VALUES (4, 3, 'Despesas Correntes', 'intra2', 0.00, 0.00, 0.00 );
            
            UPDATE  tmp_despesa
            SET     cod_estrutural = 'intra3'
            WHERE   tmp_despesa.cod_estrutural ilike '4.%'
              AND   tmp_despesa.cod_estrutural ilike '%.9.1.%';
            
        ELSE
            
            stDado := '';
            
            SELECT nivel INTO stDado
            FROM  tmp_despesa
            WHERE tmp_despesa.cod_estrutural ilike '4.%'
                  and tmp_despesa.grupo = 1
            GROUP BY nivel;
            
            IF stDado IS NULL THEN
              INSERT INTO tmp_despesa VALUES (4, 3, 'Despesas de Capital', 'intra3', 0.00, 0.00, 0.00 );
              
            UPDATE  tmp_despesa
            SET     cod_estrutural = 'intra2'
            WHERE   tmp_despesa.cod_estrutural ilike '3.%'
              AND   tmp_despesa.cod_estrutural ilike '%.9.1.%';
              
            END IF;
            
        END IF;
    END IF;
    
    
    INSERT INTO tmp_despesa VALUES (3, 3, 'Pessoal Militar'                               , 'previdencia1', 0.00, 0.00, 0.00);
    INSERT INTO tmp_despesa VALUES (3, 3, 'Outras Despesas Previdenciárias'               , 'previdencia2', 0.00, 0.00, 0.00);
    INSERT INTO tmp_despesa VALUES (3, 4, 'Compensação Previdenciária do RPPS para o RGPS', 'previdencia3', 0.00, 0.00, 0.00);
    INSERT INTO tmp_despesa VALUES (3, 4, 'Demais Despesas Previdenciárias'               , 'previdencia4', 0.00, 0.00, 0.00);
    
    stSql := '
        SELECT CAST(1 AS INTEGER) AS grupo
            ,  CAST(1 AS INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR) AS cod_estrutural
            ,  CAST(''DESPESAS PREVIDENCIÁRIAS - RPPS (EXCETO INTRA-ORÇAMENTÁRIAS)'' AS VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) AS ano2
            ,  COALESCE(SUM(ano3), 0.00) AS ano3
            ,  COALESCE(SUM(ano4), 0.00) AS ano4
        FROM tmp_despesa
        WHERE (cod_estrutural LIKE ''3.%''
           OR  cod_estrutural LIKE ''4.%''
           OR  cod_estrutural LIKE ''9.%'')
          AND  cod_estrutural NOT LIKE ''%.9.1.%''
                                
        UNION ALL
    
        SELECT CAST(2 AS INTEGER) AS grupo
            ,  CAST(2 AS INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR) AS cod_estrutural
            ,  CAST(''ADMINISTRAÇÃO'' AS VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) AS ano2
            ,  COALESCE(SUM(ano3), 0.00) AS ano3
            ,  COALESCE(SUM(ano4), 0.00) AS ano4
        FROM tmp_despesa
        WHERE (cod_estrutural LIKE ''3.%''
           OR  cod_estrutural LIKE ''4.%''
           OR  cod_estrutural LIKE ''9.%'')
          AND  cod_estrutural NOT LIKE ''%.9.1.%''
          AND  cod_estrutural NOT IN ( ''3.3.9.0.01.00.00.00.00''
                                    , ''3.3.9.0.03.00.00.00.00''
                                    , ''3.3.9.0.05.00.00.00.00''
                                    )
        
        UNION ALL
        
        SELECT CAST(2 AS INTEGER) AS grupo
            ,  CAST(3 AS INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR) AS cod_estrutural
            ,  CAST(''Despesas Correntes'' AS VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) AS ano2
            ,  COALESCE(SUM(ano3), 0.00) AS ano3
            ,  COALESCE(SUM(ano4), 0.00) AS ano4
        FROM tmp_despesa
        WHERE (cod_estrutural LIKE ''3.%''
           OR  cod_estrutural LIKE ''9.%'')
          AND  cod_estrutural NOT LIKE ''%.9.1.%''
          AND  cod_estrutural NOT IN ( ''3.3.9.0.01.00.00.00.00''
                                     , ''3.3.9.0.03.00.00.00.00''
                                     , ''3.3.9.0.05.00.00.00.00''
                                     )
        
        
        UNION ALL
        
        SELECT CAST(2 as integer) as grupo
            ,  CAST(3 as integer) as nivel
            ,  CAST('''' as varchar) as cod_estrutural
            ,  CAST(''Despesas de Capital'' as varchar) as nom_conta
            ,  COALESCE(SUM(ano2), 0.00) AS ano2
            ,  COALESCE(SUM(ano3), 0.00) AS ano3
            ,  COALESCE(SUM(ano4), 0.00) AS ano4
        FROM tmp_despesa
        WHERE cod_estrutural LIKE ''4.%''
        
        UNION ALL
    
        SELECT CAST(3 AS INTEGER) AS grupo
            ,  CAST(2 AS INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR) AS cod_estrutural
            ,  CAST(''PREVIDÊNCIA SOCIAL'' AS VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) AS ano2
            ,  COALESCE(SUM(ano3), 0.00) AS ano3
            ,  COALESCE(SUM(ano4), 0.00) AS ano4
        FROM tmp_despesa
        WHERE cod_estrutural IN ( ''3.3.9.0.01.00.00.00.00''
                                , ''3.3.9.0.03.00.00.00.00''
                                , ''3.3.9.0.05.00.00.00.00''
                                )
        
        UNION ALL
        
        SELECT CAST(3 AS INTEGER) AS grupo
            ,  CAST(3 AS INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR) AS cod_estrutural
            ,  CAST(''Pessoa Civil'' AS VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) AS ano2
            ,  COALESCE(SUM(ano3), 0.00) AS ano3
            ,  COALESCE(SUM(ano4), 0.00) AS ano4
        FROM tmp_despesa
        WHERE cod_estrutural IN ( ''3.3.9.0.01.00.00.00.00''
                                , ''3.3.9.0.03.00.00.00.00''
                                , ''3.3.9.0.05.00.00.00.00'')
        UNION ALL
        
        SELECT grupo
            ,  nivel
            ,  cod_estrutural
            ,  nom_conta
            ,  ano2
            ,  ano3
            ,  ano4
        FROM tmp_despesa
        WHERE cod_estrutural like ''previdencia%''
        
        
        UNION ALL
        
        SELECT CAST(4 as INTEGER) AS grupo
            ,  CAST(1 as INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR ) AS cod_estrutural
            ,  CAST(''DESPESAS PREVIDENCIÁRIAS - RPPS (INTRA-ORÇAMENTÁRIAS)'' as VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) as ano2
            ,  COALESCE(SUM(ano3), 0.00) as ano3
            ,  COALESCE(SUM(ano4), 0.00) as ano4
        FROM tmp_despesa
        WHERE cod_estrutural like ''intra%''
        
        UNION ALL
        
        SELECT CAST(4 as INTEGER) AS grupo
            ,  CAST(2 as INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR ) AS cod_estrutural
            ,  CAST(''ADMINISTRAÇÃO'' as VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) as ano2
            ,  COALESCE(SUM(ano3), 0.00) as ano3
            ,  COALESCE(SUM(ano4), 0.00) as ano4
        FROM tmp_despesa
        WHERE cod_estrutural like ''intra%''
        
        UNION ALL
        
        SELECT CAST(4 as INTEGER) AS grupo
            ,  CAST(3 as INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR ) AS cod_estrutural
            ,  CAST(''Despesas Correntes'' as VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) as ano2
            ,  COALESCE(SUM(ano3), 0.00) as ano3
            ,  COALESCE(SUM(ano4), 0.00) as ano4
        FROM tmp_despesa
        WHERE cod_estrutural like ''intra2''
        
        UNION ALL
        
        SELECT CAST(4 as INTEGER) AS grupo
            ,  CAST(3 as INTEGER) AS nivel
            ,  CAST('''' AS VARCHAR ) AS cod_estrutural
            ,  CAST(''Despesas de Capital'' as VARCHAR) AS nom_conta
            ,  COALESCE(SUM(ano2), 0.00) as ano2
            ,  COALESCE(SUM(ano3), 0.00) as ano3
            ,  COALESCE(SUM(ano4), 0.00) as ano4
        FROM tmp_despesa
        WHERE cod_estrutural like ''intra3''
        
        ORDER BY grupo, cod_estrutural
    ';
    

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
    DROP TABLE tmp_despesa;
    
    RETURN;
END;
$$ language 'plpgsql';

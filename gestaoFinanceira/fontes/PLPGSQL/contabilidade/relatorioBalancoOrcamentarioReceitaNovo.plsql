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

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_balanco_orcamentario_receita_novo(varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    dtInicial           ALIAS FOR $2;
    dtFinal             ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    
    dtInicioAno         VARCHAR   := '';
    dtFimAno            VARCHAR   := '';
    stSql               VARCHAR   := '';
    stSql1              VARCHAR   := '';
    stMascClassReceita  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;
    dtInicioExercicio   VARCHAR := '01/01/'||stExercicio;

    arDatas varchar[] ;

BEGIN
        dtInicioAno := '01/01/' || stExercicio;
        arDatas := publico.bimestre ( stExercicio, 6 );

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE

                    ore.exercicio       = ' || quote_literal(stExercicio) ;
                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
                end if;

            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = true
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''D''

                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo

            UNION

            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                ore.exercicio       = '|| quote_literal(stExercicio);  

                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
                end if;
            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio


                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = false
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''C''

                -- Data Inicial e Data Final, antes iguala codigo do lote
                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo ) '; 

        EXECUTE stSql;


stSql := '
CREATE TEMPORARY TABLE tmp_rreo_an1_receita AS (
  
    SELECT
        1 as grupo,
        cod_estrutural::VARCHAR as cod_estrutural,
        nivel,
        descricao::VARCHAR as descricao,
        previsao_inicial::numeric(14,2) as previsao_inicial,
        previsao_inicial::numeric(14,2) as previsao_atualizada,
        (coalesce(no_bimestre,0.00)*-1)::numeric(14,2) as no_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
           CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''::numeric(14,2) 
        END as p_no_bimestre,    
        (coalesce(ate_bimestre,0.00)*-1)::numeric(14,2) as ate_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''::numeric(14,2) 
        END as p_ate_bimestre,    
        coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00)::numeric(14,2) as a_realizar
        FROM (SELECT nivel , 
                    cod_estrutural,
                    descricao,
                    SUM (previsao_inicial) as previsao_inicial,
                    SUM (no_bimestre) as no_bimestre,
                    SUM (ate_bimestre) as ate_bimestre
                    FROM(
                        SELECT
                            CASE WHEN ocr.cod_estrutural like ''9.1%'' AND  publico.fn_nivel(ocr.cod_estrutural) =2 THEN
                                publico.fn_nivel(''1.7.0.0.00.00.00.00.00'') 
                            WHEN ocr.cod_estrutural like ''9.1%'' AND  publico.fn_nivel(ocr.cod_estrutural) =3 THEN
                                publico.fn_nivel(''1.7.2.0.00.00.00.00.00'') 
                            ELSE
                                publico.fn_nivel(ocr.cod_estrutural) 
                            END as nivel,
                            CASE WHEN ocr.cod_estrutural like ''9.1%'' AND  publico.fn_nivel(ocr.cod_estrutural) =2 THEN
                               cast (''1.7.0.0.00.00.00.00.00'' as varchar)
                            WHEN ocr.cod_estrutural like ''9.1%'' AND  publico.fn_nivel(ocr.cod_estrutural) =3 THEN
                                  cast (''1.7.2.0.00.00.00.00.00'' as varchar)
                            ELSE
                                ocr.cod_estrutural 
                           END  as cod_estrutural,
                          TRIM( CASE WHEN ocr.cod_estrutural like ''9.1%'' AND  publico.fn_nivel(ocr.cod_estrutural) =2 THEN
                                 ''TRANSFERENCIAS CORRENTES'' 
                           WHEN ocr.cod_estrutural like ''9.1%'' AND  publico.fn_nivel(ocr.cod_estrutural) =3 THEN
                                ''TRANSFERENCIAS INTERGOVERNAMENTAIS'' 
                            ELSE
                                ocr.descricao
                            END) AS descricao,
                            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                , '|| quote_literal(stCodEntidades) ||'
                            ) as previsao_inicial,
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                     ,'|| quote_literal(dtInicial) ||'
                                                                     ,'|| quote_literal(dtFinal)   ||'
                            ) as no_bimestre,
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                                     ,'|| quote_literal(dtFinal)     ||'
                            ) as ate_bimestre
                        FROM

                            orcamento.conta_receita     as ocr
                        WHERE            
                            -- Filtros
                                (ocr.cod_estrutural like ''1%''  OR     ocr.cod_estrutural like ''9.1%'' )
                               AND ocr.cod_estrutural <> ''1.2.2.0.00.00.00.00.00''     
                        AND publico.fn_nivel(ocr.cod_estrutural) >= 1         
                        AND publico.fn_nivel(ocr.cod_estrutural) <= 3   
                        AND ocr.exercicio = '|| quote_literal(stExercicio) ||'

                        ORDER BY
                            ocr.cod_estrutural
                    ) as tbl
        GROUP BY
        cod_estrutural,
        nivel,
        descricao
                   ) as tb
UNION 

    SELECT
        2 as grupo,
        cod_estrutural,
        nivel,
        descricao,
        coalesce(previsao_inicial,0.00),
        coalesce(previsao_inicial,0.00) as previsao_atualizada,
        coalesce(no_bimestre,0.00)*-1 as no_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END as p_no_bimestre,    
        coalesce(ate_bimestre,0.00)*-1 as ate_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END as p_ate_bimestre,    
        coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(ocr.cod_estrutural) as nivel,

            ocr.cod_estrutural,
            ocr.descricao,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , '|| quote_literal(stCodEntidades) ||'
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
           
            orcamento.conta_receita     as ocr
        WHERE
         
        ocr.cod_estrutural   like ''2.%'' 

        AND publico.fn_nivel(ocr.cod_estrutural) >=     1   
        AND publico.fn_nivel(ocr.cod_estrutural) <=     3   
        AND ocr.exercicio = '|| quote_literal(stExercicio) ||'
    
        ORDER BY
            ocr.cod_estrutural
    ) as tbl

UNION 

    SELECT
        3 as grupo,
        ''7.0.0.0.0.00.00.00.00.00'' as cod_estrutural,
        1 as nivel,
        CAST(''RECEITAS (INTRA-ORÇAMENTÁRIAS)'' AS VARCHAR) AS  descricao,
        SUM(coalesce(previsao_inicial,0.00)),
        SUM(coalesce(previsao_inicial,0.00)) as previsao_atualizada,
        SUM(coalesce(no_bimestre,0.00)*-1) as no_bimestre,
        SUM(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_no_bimestre,    
        SUM(coalesce(ate_bimestre,0.00)*-1) as ate_bimestre,
        SUM(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_ate_bimestre,    
        SUM(coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00)) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(ocr.cod_estrutural) as nivel,

            ocr.cod_estrutural,
            ocr.descricao,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , '|| quote_literal(stCodEntidades) ||'
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
           
            orcamento.conta_receita     as ocr
        WHERE
         
         (ocr.cod_estrutural  like  ''7.0.0.0.00.00.00.00.00%'' or ocr.cod_estrutural   like ''8.0.0.0.00.00.00.00.00%'' ) 
        AND publico.fn_nivel(ocr.cod_estrutural) >=     1   
        AND publico.fn_nivel(ocr.cod_estrutural) <=     3   
        AND ocr.exercicio = '|| quote_literal(stExercicio) ||'

        GROUP BY descricao
			, cod_estrutural 
        ORDER BY
            ocr.cod_estrutural
    ) as tbl     
)
';
EXECUTE stSql;
stSql := '    
UPDATE tmp_rreo_an1_receita SET 
       previsao_inicial = (SELECT coalesce(sum(previsao_inicial), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2) ,
       previsao_atualizada = (SELECT coalesce(sum(previsao_atualizada), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2) ,
       no_bimestre =(SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2), 
       ate_bimestre =(SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2) ,
       a_realizar =(SELECT coalesce(sum(a_realizar), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2)
    WHERE cod_estrutural like ''1.0.0.0.00.00.00.00.00'';
';
EXECUTE stSql;
stSql := 'SELECT * FROM tmp_rreo_an1_receita ORDER BY  cod_estrutural';
    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_rreo_an1_receita ;
    
    RETURN;
END;
$$ language 'plpgsql';



/*
CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_balanco_orcamentario_novo_refinanciamento(varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    dtInicial           ALIAS FOR $2;
    dtFinal             ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    
    dtInicioAno         VARCHAR   := '';
    dtFimAno            VARCHAR   := '';
    stSql               VARCHAR   := '';
    stSql1              VARCHAR   := '';
    stMascClassReceita  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;
    --dtInicial           varchar := ''; 
    --dtFinal             varchar := ''; 
    dtInicioExercicio   VARCHAR := '01/01/'||stExercicio;

    arDatas varchar[] ;

BEGIN
BEGIN
        dtInicioAno := '01/01/' || stExercicio;
        arDatas := publico.bimestre ( stExercicio, 6 );
        raise notice '%', arDatas [ 0 ];
        raise notice '%', arDatas [ 1 ];

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE

                    ore.exercicio       = ' || quote_literal(stExercicio) ;
                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
                end if;

            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = true
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''D''

                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo

            UNION

            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                ore.exercicio       = '|| quote_literal(stExercicio);  

                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
                end if;
            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio


                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = false
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''C''

                -- Data Inicial e Data Final, antes iguala codigo do lote
                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo ) '; 

        EXECUTE stSql;


stSql := '
CREATE TEMPORARY TABLE tmp_rreo_an1_receita AS (

    SELECT
        3 as grupo,
        ''7.0.0.0.0.00.00.00.00.00'' as cod_estrutural,
        1 as nivel,
        CAST(''RECEITAS (INTRA-ORÇAMENTÁRIAS) (II)'' AS VARCHAR) AS  descricao,
        SUM(coalesce(previsao_inicial,0.00)),
        SUM(coalesce(previsao_inicial,0.00)) as previsao_atualizada,
        SUM(coalesce(no_bimestre,0.00)*-1) as no_bimestre,
        SUM(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_no_bimestre,    
        SUM(coalesce(ate_bimestre,0.00)*-1) as ate_bimestre,
        SUM(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_ate_bimestre,    
        SUM(coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00)) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(ocr.cod_estrutural) as nivel,

            ocr.cod_estrutural,
            ocr.descricao,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , '|| quote_literal(stCodEntidades) ||'
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
           
            orcamento.conta_receita     as ocr
        WHERE
         
         (ocr.cod_estrutural  like  ''7.0.0.0.00.00.00.00.00%'' or ocr.cod_estrutural   like ''8.0.0.0.00.00.00.00.00%'' ) 
        AND publico.fn_nivel(ocr.cod_estrutural) >=     1   
        AND publico.fn_nivel(ocr.cod_estrutural) <=     3   
        AND ocr.exercicio = '|| quote_literal(stExercicio) ||'

        GROUP BY descricao
			, cod_estrutural 
        ORDER BY
            ocr.cod_estrutural
    ) as tbl     
)';


EXECUTE stSql;
stSql := '    
UPDATE tmp_rreo_an1_receita SET 
       previsao_inicial = (SELECT coalesce(sum(previsao_inicial), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2) ,
       previsao_atualizada = (SELECT coalesce(sum(previsao_atualizada), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2) ,
       no_bimestre =(SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2), 
       ate_bimestre =(SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2) ,
       a_realizar =(SELECT coalesce(sum(a_realizar), 0.00) FROM tmp_rreo_an1_receita where grupo = 1 and nivel = 2)
    WHERE cod_estrutural like ''1.0.0.0.00.00.00.00.00'';
';
EXECUTE stSql;
stSql := 'SELECT * FROM tmp_rreo_an1_receita ORDER BY  cod_estrutural';
    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_rreo_an1_receita ;
    
    RETURN;
END;
$$ language 'plpgsql';*/


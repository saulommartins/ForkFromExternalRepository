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

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo5_receitas_novo(varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;   
    stOpcao                 ALIAS FOR $3;
    dtInicial               ALIAS FOR $4;
    dtFinal                 ALIAS FOR $5;
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
        dtInicialAnterior := SUBSTRING(dtInicial,0,6) || stExercicioAnterior;
        dtFinalAnterior := SUBSTRING(dtFinal,0,6) || stExercicioAnterior;

IF (stOpcao = 'repasse') THEN
    
    stSql := '
        SELECT
            CAST(1 as integer) as grupo,
            CAST('''' AS varchar) AS cod_estrutural,
            CAST(1 as integer) as nivel,
            CAST(''REPASSES PREVIDENCIÁRIOS PARA COBERTURA DE DÉFCIT ATUARIAL - RPPS (III)'' as varchar) AS nom_conta,
            CAST(0.00 as numeric(14,2)) as previsao_inicial,
            CAST(0.00 as numeric(14,2)) as previsao_atualizada,
            CAST(0.00 as numeric(14,2)) as no_bimestre,
            CAST(0.00 as numeric(14,2)) as ate_bimestre,
            CAST(0.00 as numeric(14,2)) as ate_bimestre_anterior
            
    UNION ALL
        
        SELECT
            CAST(1 as integer) as grupo,
            CAST('''' AS varchar) AS cod_estrutural,
            CAST(1 as integer) as nivel,
            CAST(''REPASSES PREVIDENCIÁRIOS PARA COBERTURA DE DÉFCIT FINANCEIRO - RPPS (IV)'' as varchar) AS nom_conta,
            CAST(0.00 as numeric(14,2)) as previsao_inicial,
            CAST(0.00 as numeric(14,2)) as previsao_atualizada,
            CAST(0.00 as numeric(14,2)) as no_bimestre,
            CAST(0.00 as numeric(14,2)) as ate_bimestre,
            CAST(0.00 as numeric(14,2)) as ate_bimestre_anterior
            
    UNION ALL
        
        SELECT
            CAST(1 as integer) as grupo,
            cod_estrutural,
            CAST(1 as integer) as nivel,
            CAST(''OUTROS APORTES AO RPPS (V)'' as varchar) AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM(
            SELECT
                plano_conta.cod_estrutural,
                plano_conta.nom_conta,
                orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                        ,publico.fn_mascarareduzida(plano_conta.cod_estrutural)
                                        , ''' || stCodEntidades || '''
                ) as previsao_inicial,
                orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(plano_conta.cod_estrutural)
                                                         ,''' || dtInicial || '''
                                                         ,''' || dtFinal || '''
                ) as no_bimestre,
                orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(plano_conta.cod_estrutural)
                                                         ,''' || dtInicioAno || '''
                                                         ,''' || dtFinal || '''
                ) as ate_bimestre,
--                orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(plano_conta.cod_estrutural)
--                                                         ,''' || dtInicioAnoAnterior || '''
--                                                         ,''' || dtFinalAnterior || '''
--                ) as ate_bimestre_anterior
                orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioAnoAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
                ) as ate_bimestre_anterior
            FROM
                contabilidade.plano_conta
                
            WHERE plano_conta.cod_estrutural = ''5.1.2.1.7.99.00.00.00.00''
                AND plano_conta.exercicio = ''' || stExercicio || '''
          ) as tbl
    ';
    
ELSE 
    
    stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as primeira
        FROM
            contabilidade.valor_lancamento      ,
            orcamento.conta_receita             ,
            orcamento.receita                   ,
            contabilidade.lancamento_receita    ,
            contabilidade.lancamento            ,
            contabilidade.lote                  
        WHERE

                receita.exercicio       IN (''' || stExercicio || ''', ''' || stExercicioAnterior || ''')';
            if ( stCodEntidades != '' ) then
               stSql := stSql || ' AND receita.cod_entidade    IN (' || stCodEntidades || ') ';
            end if;

        stSql := stSql || '

            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio

            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = true
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''

            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo

            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''D''

            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo

        UNION

        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as segunda
        FROM
            contabilidade.valor_lancamento      ,
            orcamento.conta_receita             ,
            orcamento.receita                   ,
            contabilidade.lancamento_receita    ,
            contabilidade.lancamento            ,
            contabilidade.lote                  

        WHERE
            receita.exercicio       IN (''' || stExercicio || ''', ''' || stExercicioAnterior || ''')';

            if ( stCodEntidades != '' ) then
               stSql := stSql || ' AND receita.cod_entidade    IN (' || stCodEntidades || ') ';
            end if;
        stSql := stSql || '

            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio


            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = false
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''

            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo

            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''C''

            -- Data Inicial e Data Final, antes iguala codigo do lote
            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo ) ';

    EXECUTE stSql;
    
    stSql1 := '
    CREATE TEMPORARY TABLE tmp_receitas AS (
    SELECT
            cast(1 as integer) as grupo,
            cod_estrutural,
            cast(1 as integer) as nivel,
            nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00)*-1 as no_bimestre,
            coalesce(ate_bimestre,0.00)*-1 as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00)*-1 as ate_bimestre_anterior
    FROM(
        SELECT
            conta_receita.cod_estrutural,
            conta_receita.descricao AS nom_conta,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                    , ''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicial || '''
                                                     ,''' || dtFinal || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioAno || '''
                                                     ,''' || dtFinal || '''
            ) as ate_bimestre,
--            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
--                                                     ,''' || dtInicioAnoAnterior || '''
--                                                     ,''' || dtFinalAnterior || '''
--            ) as ate_bimestre_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioAnoAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_anterior
        FROM
--            contabilidade.plano_conta,
            orcamento.conta_receita
            
--        WHERE plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  
--            AND plano_conta.exercicio        = conta_receita.exercicio
          WHERE (
                (conta_receita.cod_estrutural ILIKE ''1.2%''
                 AND publico.fn_nivel(conta_receita.cod_estrutural) = 2)
                OR (conta_receita.cod_estrutural ILIKE ''1.2.1.0.01%''
                    AND publico.fn_nivel(conta_receita.cod_estrutural) = 7)
                OR (conta_receita.cod_estrutural ILIKE ''1.2.1.0.29%''
                    AND publico.fn_nivel(conta_receita.cod_estrutural) = 6)
                OR (conta_receita.cod_estrutural like ''1.2.1.0.99%''
                    AND publico.fn_nivel(conta_receita.cod_estrutural) = 7)
                    
                OR (conta_receita.cod_estrutural ILIKE ''1.3%''
                    AND publico.fn_nivel(conta_receita.cod_estrutural) <= 3)
                    
                OR (conta_receita.cod_estrutural ILIKE ''1.6%''
                    AND publico.fn_nivel(conta_receita.cod_estrutural) = 2)
                
                OR (conta_receita.cod_estrutural ILIKE ''1.9%'')
                
                OR ((conta_receita.cod_estrutural ILIKE ''1.1%''
                    OR conta_receita.cod_estrutural ILIKE ''1.4%''
                    OR conta_receita.cod_estrutural ILIKE ''1.5%''
                    OR conta_receita.cod_estrutural ILIKE ''1.7%'')
                     AND publico.fn_nivel(conta_receita.cod_estrutural) = 2)
                     
                OR (conta_receita.cod_estrutural ILIKE ''2%''
                 AND publico.fn_nivel(conta_receita.cod_estrutural) <= 2)
                 
                OR ((conta_receita.cod_estrutural ILIKE ''7%''
                 --   OR conta_receita.cod_estrutural ILIKE ''8%''
                    )
                    AND publico.fn_nivel(conta_receita.cod_estrutural) = 1)
                    
--                OR (conta_receita.cod_estrutural ILIKE ''4.%''
--                    AND publico.fn_nivel(conta_receita.cod_estrutural) = 2)
                    
                )
             AND conta_receita.exercicio = ''' || stExercicio || '''
        ) as tbl
        
    UNION ALL 
    
    SELECT
            cast(1 as integer) as grupo,
            cod_estrutural,
            cast(1 as integer) as nivel,
            ''(-) DEDUÇÕES DA RECEITA'' AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00)*-1 as no_bimestre,
            coalesce(ate_bimestre,0.00)*-1 as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00)*-1 as ate_bimestre_anterior
    FROM(
        SELECT
            conta_receita.cod_estrutural,
            conta_receita.descricao AS nom_conta,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                    , ''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicial || '''
                                                     ,''' || dtFinal || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioAno || '''
                                                     ,''' || dtFinal || '''
            ) as ate_bimestre,
--            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
--                                                     ,''' || dtInicioAnoAnterior || '''
--                                                     ,''' || dtFinalAnterior || '''
--            ) as ate_bimestre_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioAnoAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_anterior
        FROM
            --contabilidade.plano_conta,
            orcamento.conta_receita
            
        WHERE
--                plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  
                conta_receita.cod_estrutural   like ''9%'' 
          
--          AND plano_conta.exercicio = conta_receita.exercicio 
          AND publico.fn_nivel(conta_receita.cod_estrutural) = 1 
          AND conta_receita.exercicio = ''' || stExercicio || '''
        
        ) as tbl
        
        ORDER BY cod_estrutural
    )
    ';
    EXECUTE stSql1;
    
    INSERT INTO tmp_receitas VALUES (6, 'militar1', 4, 'Pessoal Militar');
    INSERT INTO tmp_receitas VALUES (6, 'militar2', 5, 'Ativo');
    INSERT INTO tmp_receitas VALUES (6, 'militar3', 5, 'Inativo');
    INSERT INTO tmp_receitas VALUES (6, 'militar4', 5, 'Pensionista');
    
    UPDATE tmp_receitas SET nom_conta = 'Receita Patrimonial'              WHERE cod_estrutural = '1.3.0.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Receitas Imobiliárias'            WHERE cod_estrutural = '1.3.1.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Receitas de Valores Mobiliários' WHERE cod_estrutural = '1.3.2.0.00.00.00.00.00';
    
    UPDATE tmp_receitas SET nom_conta = 'Receitas de Serviços'      WHERE cod_estrutural = '1.6.0.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Outras Receitas Correntes' WHERE cod_estrutural = '1.9.0.0.00.00.00.00.00';
    
    UPDATE tmp_receitas SET nom_conta = 'Compensação Previdenciária do RGPS para o RPPS' WHERE cod_estrutural = '1.9.2.2.10.00.00.00.00';
    
    UPDATE tmp_receitas SET nom_conta = 'Alienação de Bens'      WHERE cod_estrutural = '2.2.0.0.00.00.00.00.00';
    UPDATE tmp_receitas SET nom_conta = 'Amortização de Empréstimos' WHERE cod_estrutural = '2.3.0.0.00.00.00.00.00';
    
    stSql := '
        SELECT
            cast(1 as integer) as grupo,
            cast('''' as varchar) as cod_estrutural,
            cast(1 as integer) as nivel,
            cast(''RECEITAS PREVIDENCIÁRIAS - RPPS (EXCETO INTRA-ORÇAMENTÁRIAS) (I)'' as varchar) as nom_conta,
            (sum(coalesce(receitas.previsao_inicial, 0.00)) - sum(coalesce(deducao.previsao_inicial, 0.00))) as previsao_inicial,
            (sum(coalesce(receitas.previsao_inicial, 0.00)) - sum(coalesce(deducao.previsao_inicial, 0.00))) as previsao_atualizada,
            (sum(coalesce(receitas.no_bimestre,0.00))) as no_bimestre,
            (sum(coalesce(receitas.ate_bimestre,0.00))) as ate_bimestre,
            (sum(coalesce(receitas.ate_bimestre_anterior,0.00)) - sum(coalesce(deducao.ate_bimestre_anterior,0.00))) as ate_bimestre_anterior
        FROM    tmp_receitas AS receitas
            ,   ( SELECT    CAST(SUM(COALESCE(previsao_inicial, 0.00)) AS numeric(14,2)) AS previsao_inicial
                       ,    CAST(SUM(COALESCE(previsao_inicial, 0.00)) AS numeric(14,2)) AS previsao_atualizada
                       ,    CAST(SUM(COALESCE(no_bimestre,0.00)) AS numeric(14,2)) AS no_bimestre
                       ,    CAST(SUM(COALESCE(ate_bimestre,0.00)) AS numeric(14,2)) AS ate_bimestre
                       ,    CAST(SUM(COALESCE(ate_bimestre_anterior,0.00)) AS numeric(14,2)) AS ate_bimestre_anterior
                  FROM tmp_receitas
--                  WHERE (cod_estrutural ILIKE ''4.9%''
--                    OR  cod_estrutural ILIKE ''9.%'')
                  WHERE cod_estrutural ILIKE ''9.%''
                    AND publico.fn_nivel(cod_estrutural) = 1
                ) AS deducao
--        WHERE (receitas.cod_estrutural NOT ILIKE ''4.9%''
--          OR  receitas.cod_estrutural NOT ILIKE ''9.%'')
        WHERE receitas.cod_estrutural NOT ILIKE ''9.%''
          AND publico.fn_nivel(receitas.cod_estrutural) = 2
            UNION ALL
                SELECT
                    publico.fn_nivel(cod_estrutural) as grupo,
                    cod_estrutural,
                    publico.fn_nivel(cod_estrutural) as nivel,
                    CASE WHEN cod_estrutural = ''1.0.0.0.00.00.00.00.00'' THEN
                        ''RECEITAS CORRENTES''
                         WHEN cod_estrutural = ''1.2.0.0.00.00.00.00.00'' THEN
                        ''Receita de Contribuições dos Segurados''
                    ELSE
                        nom_conta
                    END AS nom_conta,
                    coalesce(previsao_inicial, 0.00) as previsao_inicial,
                    coalesce(previsao_inicial, 0.00) as previsao_atualizada,
                    coalesce(no_bimestre,0.00) as no_bimestre,
                    coalesce(ate_bimestre,0.00) as ate_bimestre,
                    coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
                FROM  tmp_receitas
                
                WHERE (cod_estrutural ILIKE ''1.0%''
                       OR cod_estrutural ILIKE ''1.2%'')
                  AND   publico.fn_nivel(cod_estrutural) < 3
    
    UNION ALL
    
        SELECT
            cast(4 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            cast(4 as integer) as nivel,
            CAST(''Pessoal Civil'' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural ILIKE ''1.2%''
          AND   publico.fn_nivel(cod_estrutural) > 2
          
    UNION ALL
    
        SELECT
            cast(5 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            cast(5 as integer) as nivel,
            CAST(''Ativo'' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural IN (''1.2.1.0.01.01.01.00.00''
                                  ,''1.2.1.0.01.01.03.00.00''
                                  ,''1.2.1.0.29.01.00.00.00''
                                  ,''1.2.1.0.29.07.00.00.00''
                                  ,''1.2.1.0.99.00.10.00.00''
                                  )
                                  
    UNION ALL
    
        SELECT
            cast(5 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            cast(5 as integer) as nivel,
            CAST(''Inativo'' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural IN (''1.2.1.0.01.01.02.00.00''
                                  ,''1.2.1.0.01.01.04.00.00''
                                  ,''1.2.1.0.29.09.00.00.00''
                                  ,''1.2.1.0.99.00.11.00.00''
                                  )
    
    
    UNION ALL
    
        SELECT
            cast(5 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            cast(5 as integer) as nivel,
            CAST(''Pensionista'' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural IN (''1.2.1.0.01.01.05.00.00''
                                  ,''1.2.1.0.29.11.00.00.00''
                                  )
                                  
    UNION ALL
    
        SELECT
            grupo,
            cod_estrutural,
            nivel,
            nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural ILIKE ''militar%''
    UNION ALL
            SELECT
                CAST(7 as integer) as grupo,
                CAST('''' AS varchar) AS cod_estrutural,
                CAST(3 as integer) as nivel,
                CAST(''Outras Receitas de Contribuições'' as varchar) AS nom_conta,
                CAST(0.00 as numeric(14,2)) as previsao_inicial,
                CAST(0.00 as numeric(14,2)) as previsao_atualizada,
                CAST(0.00 as numeric(14,2)) as no_bimestre,
                CAST(0.00 as numeric(14,2)) as ate_bimestre,
                CAST(0.00 as numeric(14,2)) as ate_bimestre_anterior
    UNION ALL
    
        SELECT
            cast(7 as integer) as grupo,
            cod_estrutural,
            publico.fn_nivel(cod_estrutural) as nivel,
            CASE WHEN cod_estrutural = ''1.3.2.0.00.00.00.00.00'' THEN
                ''Receitas de Valores Mobiliários''
            ELSE
                nom_conta
            END AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural IN (''1.3.0.0.00.00.00.00.00''
                                  ,''1.3.1.0.00.00.00.00.00''
                                  ,''1.3.2.0.00.00.00.00.00''
                                  )
                                  
    UNION ALL
    
        SELECT
            CAST(8 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            CAST(3 as integer) as nivel,
            CAST(''Outras Receitas Patrimoniais'' as varchar) AS nom_conta,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(COALESCE(no_bimestre,0.00)) as no_bimestre,
            SUM(COALESCE(ate_bimestre,0.00)) as ate_bimestre,
            SUM(COALESCE(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural ILIKE ''1.3.3.%''
          OR    cod_estrutural ILIKE ''1.3.4.%''
          OR    cod_estrutural ILIKE ''1.3.9.%''
          
    UNION ALL
    
        SELECT
            CAST(9 as integer) as grupo,
            cod_estrutural,
            publico.fn_nivel(cod_estrutural) AS nivel,
            nom_conta,
            COALESCE(previsao_inicial, 0.00) as previsao_inicial,
            COALESCE(previsao_inicial, 0.00) as previsao_atualizada,
            COALESCE(no_bimestre,0.00) as no_bimestre,
            COALESCE(ate_bimestre,0.00) as ate_bimestre,
            COALESCE(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural ILIKE ''1.6.%''
            
            
    UNION ALL
    
        SELECT
            CAST(10 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            CAST(2 as integer) AS nivel,
            CAST(''Outras Receitas Correntes'' as varchar) AS nom_conta,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(COALESCE(no_bimestre,0.00)) as no_bimestre,
            SUM(COALESCE(ate_bimestre,0.00)) as ate_bimestre,
            SUM(COALESCE(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
--        WHERE cod_estrutural IN (''1.1.0.0.00.00.00.00.00''
--                                ,''1.4.0.0.00.00.00.00.00''
--                                ,''1.5.0.0.00.00.00.00.00''
--                                ,''1.7.0.0.00.00.00.00.00''
--                                ,''1.9.0.0.00.00.00.00.00''
--                                )
        WHERE cod_estrutural IN (''1.9.0.0.00.00.00.00.00'')
    
    UNION ALL
    
        SELECT
            CAST(10 as integer) as grupo,
            cod_estrutural,
            CAST(3 as integer) AS nivel,
            nom_conta,
            COALESCE(previsao_inicial, 0.00) as previsao_inicial,
            COALESCE(previsao_inicial, 0.00) as previsao_atualizada,
            COALESCE(no_bimestre,0.00) as no_bimestre,
            COALESCE(ate_bimestre,0.00) as ate_bimestre,
            COALESCE(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE cod_estrutural = ''1.9.2.2.10.00.00.00.00''
          
    UNION ALL
    
        SELECT
            CAST(11 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            CAST(3 as integer) AS nivel,
            CAST(''Outras Receitas Correntes'' as varchar) AS nom_conta,
            (total.previsao_inicial      - COALESCE(tmp_receitas.previsao_inicial, 0.00)) as previsao_inicial,
            (total.previsao_inicial      - COALESCE(tmp_receitas.previsao_inicial, 0.00)) as previsao_atualizada,
            (total.no_bimestre           - COALESCE(tmp_receitas.no_bimestre,0.00)) as no_bimestre,
            (total.ate_bimestre          - COALESCE(tmp_receitas.ate_bimestre,0.00)) as ate_bimestre,
            (total.ate_bimestre_anterior - COALESCE(tmp_receitas.ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM tmp_receitas
            , ( SELECT  CAST(SUM(COALESCE(previsao_inicial, 0.00)) as numeric(14,2)) AS previsao_inicial
                    ,   CAST(SUM(COALESCE(previsao_inicial, 0.00)) as numeric(14,2)) AS previsao_atualizada
                    ,   CAST(SUM(COALESCE(no_bimestre,0.00)) as numeric(14,2)) AS no_bimestre
                    ,   CAST(SUM(COALESCE(ate_bimestre,0.00)) as numeric(14,2)) AS ate_bimestre
                    ,   CAST(SUM(COALESCE(ate_bimestre_anterior,0.00)) as numeric(14,2)) AS ate_bimestre_anterior
                    
                FROM tmp_receitas
--                WHERE cod_estrutural IN (''1.1.0.0.00.00.00.00.00''
--                                        ,''1.4.0.0.00.00.00.00.00''
--                                        ,''1.5.0.0.00.00.00.00.00''
--                                        ,''1.7.0.0.00.00.00.00.00''
--                                        ,''1.9.0.0.00.00.00.00.00''
--                                        )
                WHERE cod_estrutural IN (''1.9.0.0.00.00.00.00.00'')
            ) AS total
        WHERE tmp_receitas.cod_estrutural = ''1.9.2.2.10.00.00.00.00''
          
    UNION ALL
    
        SELECT
            CAST(12 as integer) as grupo,
            cod_estrutural,
            publico.fn_nivel(cod_estrutural) as nivel,
            CASE WHEN cod_estrutural = ''2.0.0.0.00.00.00.00.00'' THEN
                ''RECEITAS DE CAPITAL''
            ELSE
                nom_conta
            END AS nom_conta,
            COALESCE(previsao_inicial, 0.00) as previsao_inicial,
            COALESCE(previsao_inicial, 0.00) as previsao_atualizada,
            COALESCE(no_bimestre,0.00) as no_bimestre,
            COALESCE(ate_bimestre,0.00) as ate_bimestre,
            COALESCE(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural IN (''2.0.0.0.00.00.00.00.00'' 
                                  ,''2.2.0.0.00.00.00.00.00''
                                  ,''2.3.0.0.00.00.00.00.00''
                                  )
    
    UNION ALL
    
        SELECT
            CAST(13 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            CAST(3 as integer) as nivel,
            CAST(''Outras Receitas de Capital'' as varchar) AS nom_conta,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(COALESCE(no_bimestre,0.00)) as no_bimestre,
            SUM(COALESCE(ate_bimestre,0.00)) as ate_bimestre,
            SUM(COALESCE(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
        WHERE   cod_estrutural ILIKE ''2.1%''
          OR    cod_estrutural ILIKE ''2.4%''
          OR    cod_estrutural ILIKE ''2.5%''
    
    UNION ALL
    
        SELECT
            CAST(14 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            CAST(2 as integer) as nivel,
            CAST(''(-) DEDUÇÕES DA RECEITA'' as varchar) AS nom_conta,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(COALESCE(no_bimestre,0.00)) as no_bimestre,
            SUM(COALESCE(ate_bimestre,0.00)) as ate_bimestre,
            SUM(COALESCE(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
        
--        WHERE   cod_estrutural ILIKE ''4.9%''
--          OR    cod_estrutural ILIKE ''9.%''
        WHERE cod_estrutural ILIKE ''9.%''
          
    UNION ALL
    
        SELECT
            CAST(15 as integer) as grupo,
            CAST('''' as varchar) AS cod_estrutural,
            CAST(1 as integer) as nivel,
            CAST(''RECEITAS PREVIDENCIÁRIAS - RPPS (INTRA-ORÇAMENTÁRIAS) (II)'' as varchar) AS nom_conta,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(COALESCE(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(COALESCE(no_bimestre,0.00)) as no_bimestre,
            SUM(COALESCE(ate_bimestre,0.00)) as ate_bimestre,
            SUM(COALESCE(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas
       WHERE   (cod_estrutural ILIKE ''7%''
--          OR    cod_estrutural ILIKE ''8%''
               )
         AND   publico.fn_nivel(cod_estrutural) = 1
  
    ORDER BY    grupo, cod_estrutural    
    ';
    
END IF;

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    --DROP TABLE tmp_valor;


    RETURN;
END;
$$language 'plpgsql';

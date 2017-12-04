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


CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo5_receitas_intra_orcamentarias_novo(varchar,varchar, varchar, varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;
    dtInicioAno             VARCHAR := '''';
    dtFimAno                VARCHAR := '''';
    stSql                   VARCHAR := '''';
    stSql1                  VARCHAR := '''';
    stMascClassReceita      VARCHAR := '''';
    stMascRecurso           VARCHAR := '''';
    reRegistro              RECORD;
    dtInicialAnterior       varchar := ''''; 
    dtFinalAnterior         varchar := '''';
    stExercicioAnterior     varchar := '''';
    dtInicioAnoAnterior     varchar := '''';
    arDatas varchar[] ;

BEGIN
        
        stExercicioAnterior :=  trim(to_char((to_number(stExercicio, ''99999'')-1), ''99999''));
        
        dtInicioAno := ''01/01/'' || stExercicio;
        
        dtInicioAnoAnterior := ''01/01/'' || stExercicioAnterior;
        
        dtInicialAnterior := SUBSTRING(dtInicial,0,6) || stExercicioAnterior;
        dtFinalAnterior := SUBSTRING(dtFinal,0,6) || stExercicioAnterior;

stSql := ''
CREATE TEMPORARY TABLE tmp_receitas_intra_orcamentarias AS (
    
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
            conta_receita.descricao as nom_conta,
            orcamento.fn_receita_valor_previsto( '''''' || stExercicio || ''''''
                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                    , '''''' || stCodEntidades || ''''''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,'''''' || dtInicial || ''''''
                                                     ,'''''' || dtFinal || ''''''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,'''''' || dtInicioAno || ''''''
                                                     ,'''''' || dtFinal || ''''''
            ) as ate_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,'''''' || dtInicioAnoAnterior || ''''''
                                                     ,'''''' || dtFinalAnterior || ''''''
            ) as ate_bimestre_anterior
        FROM
            orcamento.conta_receita
            
        WHERE 
                ((conta_receita.cod_estrutural ILIKE ''''7%''''
                    OR conta_receita.cod_estrutural ILIKE ''''8%''''
                    OR conta_receita.cod_estrutural ILIKE ''''1.6%'''')
                    )
             AND conta_receita.exercicio = '''''' || stExercicio || ''''''
        ) as tbl
        
--    UNION ALL 
--    
--    SELECT
--            cast(1 as integer) as grupo,
--            cod_estrutural,
--            cast(1 as integer) as nivel,
--            ''''(-) DEDUÇÕES DA RECEITA'''' AS nom_conta,
--            coalesce(previsao_inicial, 0.00) as previsao_inicial,
--            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
--            coalesce(no_bimestre,0.00)*-1 as no_bimestre,
--            coalesce(ate_bimestre,0.00)*-1 as ate_bimestre,
--            coalesce(ate_bimestre_anterior,0.00)*-1 as ate_bimestre_anterior
--    FROM(
--        SELECT
--            plano_conta.cod_estrutural,
--            plano_conta.nom_conta,
--            orcamento.fn_receita_valor_previsto( '''''' || stExercicio || ''''''
--                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
--                                    , '''''' || stCodEntidades || ''''''
--            ) as previsao_inicial,
--            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
--                                                     ,'''''' || dtInicial || ''''''
--                                                     ,'''''' || dtFinal || ''''''
--            ) as no_bimestre,
--            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
--                                                     ,'''''' || dtInicioAno || ''''''
--                                                     ,'''''' || dtFinal || ''''''
--            ) as ate_bimestre,
--            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
--                                                     ,'''''' || dtInicioAnoAnterior || ''''''
--                                                     ,'''''' || dtFinalAnterior || ''''''
--            ) as ate_bimestre_anterior
--        FROM
--            contabilidade.plano_conta,
--            orcamento.conta_receita
--            
--        WHERE
----          CASE WHEN plano_conta.exercicio <= ''''2007'''' THEN
----                plano_conta.cod_estrutural   = ''''4.''''||conta_receita.cod_estrutural  
----                AND plano_conta.cod_estrutural   like ''''4.9%'''' 
----          ELSE
----            plano_conta.cod_estrutural   = conta_receita.cod_estrutural  
--              plano_conta.cod_estrutural   like ''''9.%'''' 
----          END
--          
----          AND plano_conta.exercicio = conta_receita.exercicio 
--          AND publico.fn_nivel(plano_conta.cod_estrutural) = 1 
--          AND plano_conta.exercicio = '''''' || stExercicio || ''''''
--        
--        ) as tbl
--        
--        ORDER BY cod_estrutural
   
   
)  

'';
EXECUTE stSql;


INSERT INTO tmp_receitas_intra_orcamentarias VALUES (6, ''militar1'', 4, ''Pessoal Militar'');

INSERT INTO tmp_receitas_intra_orcamentarias VALUES (1, ''deducoes'', 1, ''DEDUÇÕES DA RECEITA(X)'', 0.00, 0.00, 0.00, 0.00, 0.00);
INSERT INTO tmp_receitas_intra_orcamentarias VALUES (6, ''militar2'', 5, ''Ativo'');
INSERT INTO tmp_receitas_intra_orcamentarias VALUES (6, ''militar3'', 5, ''Inativo'');
INSERT INTO tmp_receitas_intra_orcamentarias VALUES (6, ''militar4'', 5, ''Pensionista'');

--Atualiza valores das deduções
stSql := ''
    SELECT
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00)*-1 as no_bimestre,
            coalesce(ate_bimestre,0.00)*-1 as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00)*-1 as ate_bimestre_anterior
    FROM(
        SELECT
            conta_receita.cod_estrutural,
            conta_receita.descricao as nom_conta,
            orcamento.fn_receita_valor_previsto( '''''' || stExercicio || ''''''
                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                    , '''''' || stCodEntidades || ''''''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,'''''' || dtInicial || ''''''
                                                     ,'''''' || dtFinal || ''''''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,'''''' || dtInicioAno || ''''''
                                                     ,'''''' || dtFinal || ''''''
            ) as ate_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,'''''' || dtInicioAnoAnterior || ''''''
                                                     ,'''''' || dtFinalAnterior || ''''''
            ) as ate_bimestre_anterior
        FROM
            orcamento.conta_receita

        WHERE
                conta_receita.cod_estrutural   like ''''9.%''''

          AND publico.fn_nivel(conta_receita.cod_estrutural) = 2
          AND conta_receita.exercicio = '''''' || stExercicio || ''''''

        ) as tbl

        ORDER BY cod_estrutural
'';
FOR reRegistro IN EXECUTE stSql
LOOP
    UPDATE tmp_receitas_intra_orcamentarias SET previsao_inicial = reRegistro.previsao_inicial, previsao_atualizada = reRegistro.previsao_atualizada, no_bimestre = reRegistro.no_bimestre, ate_bimestre = reRegistro.ate_bimestre, ate_bimestre_anterior = reRegistro.ate_bimestre_anterior WHERE cod_estrutural = ''deducoes'';    
END LOOP;

UPDATE tmp_receitas_intra_orcamentarias SET nom_conta = ''Contribuição Patronal de Servidor Inativo Civil''   WHERE cod_estrutural = ''7.2.1.0.29.03.00.00.00'';
UPDATE tmp_receitas_intra_orcamentarias SET nom_conta = ''Contribuição Patronal de Pensionista Civil''        WHERE cod_estrutural = ''7.2.1.0.29.05.00.00.00'';

UPDATE tmp_receitas_intra_orcamentarias SET nom_conta = ''Alienação de Bens''   WHERE cod_estrutural = ''8.2.0.0.00.00.00.00.00'';
UPDATE tmp_receitas_intra_orcamentarias SET nom_conta = ''Amotização de Empréstimos''   WHERE cod_estrutural = ''8.3.0.0.00.00.00.00.00'';
UPDATE tmp_receitas_intra_orcamentarias SET nom_conta = ''Outras Receitas de Capital''   WHERE cod_estrutural = ''8.5.0.0.00.00.00.00.00'';

UPDATE tmp_receitas_intra_orcamentarias SET nom_conta = ''Receita Patrimonial''         WHERE cod_estrutural = ''7.3.0.0.00.00.00.00.00'';
UPDATE tmp_receitas_intra_orcamentarias SET nom_conta = ''Outras Receitas Correntes''   WHERE cod_estrutural = ''7.9.0.0.00.00.00.00.00'';

stSql := ''
        SELECT
            CAST(1 as integer) as grupo,
            cod_estrutural,
            CAST(1 as integer) as nivel,
            CAST(''''RECEITAS CORRENTES(VIII)'''' as varchar) AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE cod_estrutural = ''''7.0.0.0.00.00.00.00.00''''
          
    UNION ALL
    
        SELECT
            cast(1 as integer) as grupo,
            cod_estrutural,
            cast(2 as integer) as nivel,
            CAST(''''Receita de Contribuição'''' as varchar) AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE   cod_estrutural = ''''7.2.0.0.00.00.00.00.00''''
   
    UNION ALL

        SELECT
            cast(2 as integer) as grupo,
            CAST('''''''' as varchar) AS cod_estrutural,
            cast(3 as integer) as nivel,
            CAST(''''Patronal'''' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias

       WHERE  cod_estrutural IN ( ''''7.2.1.0.01.00.00.00.00''''
                                , ''''7.2.1.0.29.01.00.00.00''''
                                , ''''7.2.1.0.29.03.00.00.00''''
                                , ''''7.2.1.0.29.05.00.00.00'''')
          OR cod_estrutural LIKE ''''militar%''''
        
    UNION ALL
    
        SELECT 
            cast(3 as integer) as grupo,
            CAST('''''''' as varchar) AS cod_estrutural,
            cast(4 as integer) as nivel,
            CAST(''''Pessoal Civil'''' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE cod_estrutural IN ( ''''7.2.1.0.01.00.00.00.00''''
                                , ''''7.2.1.0.29.01.00.00.00''''
                                , ''''7.2.1.0.29.03.00.00.00''''
                                , ''''7.2.1.0.29.05.00.00.00'''')
        
    UNION ALL
    
        SELECT 
            cast(4 as integer) as grupo,
            CAST('''''''' as varchar) AS cod_estrutural,
            cast(5 as integer) as nivel,
            CAST(''''Ativo'''' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE cod_estrutural IN ( ''''7.2.1.0.01.00.00.00.00''''
                                , ''''7.2.1.0.29.01.00.00.00'''')
                                
    UNION ALL
    
        SELECT 
            cast(4 as integer) as grupo,
            cod_estrutural,
            cast(5 as integer) as nivel,
            nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE cod_estrutural IN ( ''''7.2.1.0.29.03.00.00.00''''
                                , ''''7.2.1.0.29.05.00.00.00'''')
        
     UNION ALL
    
        SELECT 
            cast(5 as integer) as grupo,
            cod_estrutural,
            nivel,
            nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE cod_estrutural LIKE ''''militar%''''
       
     UNION ALL
    
        SELECT 
            cast(6 as integer) as grupo,
            CAST('''''''' as varchar) AS cod_estrutural,
            cast(3 as integer) as nivel,
            CAST(''''Para Cobertura de Défcit Atuarial'''' as varchar) AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE cod_estrutural LIKE ''''7.2.1.0.29.13.00.00.00''''
    
    UNION ALL
        SELECT 
            cast(6 as integer) as grupo,
            CAST('''''''' as varchar) AS cod_estrutural,
            cast(3 as integer) as nivel,
            CAST(''''Em Regime de Débitos e Parcelamentos'''' as varchar) AS nom_conta,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_inicial,
            SUM(coalesce(previsao_inicial, 0.00)) as previsao_atualizada,
            SUM(coalesce(no_bimestre,0.00)) as no_bimestre,
            SUM(coalesce(ate_bimestre,0.00)) as ate_bimestre,
            SUM(coalesce(ate_bimestre_anterior,0.00)) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE cod_estrutural IN (''''7.2.1.0.29.15.00.00.00''''
                                ,''''7.2.1.0.99.00.00.00.00'''')

    UNION ALL
        SELECT
            CAST(7 AS INTEGER) AS grupo,
            --definido somente para ordenação, e demonstração no relatório
            CAST(''''7.4'''' AS VARCHAR) AS cod_estrutural,
            CAST(2 AS INTEGER) AS nivel,
            CAST(''''Receita de Serviços'''' as varchar) AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias 
       WHERE cod_estrutural = ''''1.6.0.0.00.00.00.00.00''''

    UNION ALL
    
        SELECT
            cast(7 as integer) as grupo,
            cod_estrutural,
            cast(2 as integer) as nivel,
            nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE   cod_estrutural IN (''''7.3.0.0.00.00.00.00.00''''
                                  ,''''7.9.0.0.00.00.00.00.00''''
                                  )
    
    
    UNION ALL
    
        SELECT
            cast(8 as integer) as grupo,
            cod_estrutural,
            cast(1 as integer) as nivel,
            CAST(''''RECEITAS DE CAPITAL(IX)'''' as varchar) AS nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE   cod_estrutural = ''''8.0.0.0.00.00.00.00.00''''
        
    UNION ALL
    
        SELECT
            cast(9 as integer) as grupo,
            CAST('''''''' as varchar) as cod_estrutural,
            cast(2 as integer) as nivel,
            nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias
        
        WHERE   cod_estrutural IN (  ''''8.2.0.0.00.00.00.00.00''''
                                   , ''''8.3.0.0.00.00.00.00.00''''
                                   , ''''8.5.0.0.00.00.00.00.00'''')
                                   
    UNION ALL
    
        SELECT
            cast(10 as integer) as grupo,
            CAST('''''''' as varchar) as cod_estrutural,
            cast(1 as integer) as nivel,
            nom_conta,
            coalesce(previsao_inicial, 0.00) as previsao_inicial,
            coalesce(previsao_inicial, 0.00) as previsao_atualizada,
            coalesce(no_bimestre,0.00) as no_bimestre,
            coalesce(ate_bimestre,0.00) as ate_bimestre,
            coalesce(ate_bimestre_anterior,0.00) as ate_bimestre_anterior
        FROM  tmp_receitas_intra_orcamentarias

        WHERE   cod_estrutural = ''''deducoes''''
    
    ORDER BY grupo, cod_estrutural
    
'';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    --DROP TABLE tmp_valor;
--    DROP TABLE tmp_receitas_intra_orcamentarias;

    RETURN;
END;
'language 'plpgsql';

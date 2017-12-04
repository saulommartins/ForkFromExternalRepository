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
    * Relatório RREO Anexo 18 - Receitas. 
    * Data de Criação: 17/12/2014


    * @author Desenvolvedor Carolina Schwaab Marçal
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_receitas(VARCHAR, VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE 

    stExercicio       ALIAS FOR $1;
    stEntidades       ALIAS FOR $2;
    stPerdidiocidade  ALIAS FOR $3;
    inValor           ALIAS FOR $4;
    
    stDtIniExercicio  VARCHAR := '';	
    stDtIni 	      VARCHAR := '';
    stDtFim 	      VARCHAR := '';
    stSQL 	      VARCHAR := '';
    stSQLaux          VARCHAR := '';
    arDatas 	      VARCHAR[];
    inMin             INTEGER;
    inMax             INTEGER;
    
    reReg	      RECORD;

BEGIN

    stDtIniExercicio := '01/01/' || stExercicio;
    
    IF stPerdidiocidade = 'mes' THEN
        arDatas := publico.mes ( stExercicio, inValor );
    ELSEIF stPerdidiocidade = 'bimestre' THEN
        arDatas := publico.bimestre ( stExercicio, inValor );
    END IF;
        
    stDtIni := arDatas [ 0 ];
    stDtFim := arDatas [ 1 ];
    
    -- ---------------------------------------
    -- Criação de Tabelas Temporarias
    -- ---------------------------------------
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_valor AS (
    SELECT
            ocr.cod_estrutural as cod_estrutural , 
            lote.dt_lote       as data , 
            vl.vl_lancamento   as valor , 
            vl.oid             as primeira 
    FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote
    WHERE
            ore.exercicio       = ''' || stExercicio || ''' 
            
            AND ore.cod_entidade    IN (' || stEntidades || ') 

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
            AND ocr.cod_estrutural LIKE ''2.1%'' 

    UNION

    SELECT
            ocr.cod_estrutural as cod_estrutural , 
            lote.dt_lote       as data , 
            vl.vl_lancamento   as valor , 
            vl.oid             as segunda 
    FROM
            contabilidade.valor_lancamento      as vl   ,
            orcamento.conta_receita             as ocr  ,
            orcamento.receita                   as ore  ,
            contabilidade.lancamento_receita    as lr   ,
            contabilidade.lancamento            as lan  ,
            contabilidade.lote                  as lote

    WHERE
            ore.exercicio       = ''' || stExercicio || ''' 
            AND ore.cod_entidade    IN (' || stEntidades || ') 
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
            AND lote.tipo           = lan.tipo
            AND ocr.cod_estrutural LIKE ''2.1%'' 
    ) 	
    ';
    
    EXECUTE stSQL;
    

    stSQL := '
    CREATE TEMPORARY TABLE tmp_rreo_receita AS (
        SELECT
            tbl.grupo, 
            tbl.nivel, 
            CAST(''RECEITAS DE OPERAÇÕES DE CRÉDITO'' AS VARCHAR(160)) AS descricao , 
            tbl.atu,
            tbl.ate_bi, 
            CAST(0.00 AS NUMERIC(14,2)) AS saldo 
        FROM
            stn.fn_rreo_valor_conta(
                ''' || stExercicio || ''' ,
                ''R'',
                ''2.1.0.0.00.00.00.00.00'',
                ''' || stEntidades || ''',
                 '''||stDtIni||''','''|| stDtFim||''',
                FALSE,
                1,
                0,
                0
            ) AS tbl
    )';
    
    EXECUTE stSQL;
    
    stSQL := ' UPDATE tmp_rreo_receita SET saldo = ( atu - ate_bi ) ';
    
    EXECUTE stSQL;
 

    -- Seleção de Retorno

    -- --------------------------------------
    -- Select de Retorno
    -- --------------------------------------


    stSQL := '
    SELECT
        grupo,
        nivel, 
        descricao, 
        atu,
        ate_bi,
        saldo 
    FROM
        tmp_rreo_receita 
    ORDER BY
        grupo,
        nivel,
        descricao 
    ';

    FOR reReg IN EXECUTE stSQL
    LOOP	
        RETURN NEXT reReg;	
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_rreo_receita;    

    RETURN;

END;

$$ LANGUAGE 'plpgsql';

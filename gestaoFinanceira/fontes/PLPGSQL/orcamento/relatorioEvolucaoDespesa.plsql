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
    * Relatório Demonstrativo da Evolução da Despesa
    * Gestao Financeira - Modulo Orçamento - Funcionalidade Relatórios
    * Data de Criação: 15/07/2008


    * @author Leopoldo Braga Barreiro

    * Casos de uso: UC.02.01.36

    $Id: $

*/


CREATE OR REPLACE FUNCTION orcamento.fn_relatorio_evolucao_despesa(stExercicio VARCHAR, stEntidades VARCHAR, stCodEstruturalMin VARCHAR, stCodEstruturalMax VARCHAR, inCodRecurso INTEGER ) RETURNS SETOF RECORD AS 

$$

DECLARE

    stDtIniCorrente     VARCHAR;
    stDtFimCorrente     VARCHAR;
    stDtIniAnterior     VARCHAR;
    stDtFimAnterior     VARCHAR;
    stDtFimAnterior2     VARCHAR;
    stDtFimAnterior3     VARCHAR;
    stSQL               VARCHAR;
    inTamanho           INTEGER;
    reReg               RECORD;

BEGIN

    -- Definicao de Datas 
    
    stDtIniCorrente := '01/01/' || stExercicio;
    stDtFimCorrente := '31/12/' || stExercicio;
    
    stDtIniAnterior := '01/01/' || CAST(stExercicio AS INTEGER) - 1;
    stDtFimAnterior := '31/12/' || CAST(stExercicio AS INTEGER) - 1;
    stDtFimAnterior2 := '31/12/' || CAST(stExercicio AS INTEGER) - 2;
    stDtFimAnterior3 := '31/12/' || CAST(stExercicio AS INTEGER) - 3;
    
    inTamanho := 10;
        
    -- Despesas Liquidadas em Anos Anteriores
    -- Liquidadas dentro do Ano Anterior
    -- Estornadas até o Final do Exercicio Corrente
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_desp_liq AS (
    SELECT 
        c.exercicio, 
        c.cod_estrutural, 
        COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado 
    FROM 
        empenho.nota_liquidacao_item nli 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio = nli.exercicio AND 
            nl.cod_entidade = nli.cod_entidade AND 
            nl.cod_nota = nli.cod_nota 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = nl.exercicio_empenho AND 
            e.cod_entidade = nl.cod_entidade AND 
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.pre_empenho pe ON 
            pe.exercicio = e.exercicio AND 
            pe.cod_pre_empenho = e.cod_pre_empenho 
        INNER JOIN 
        empenho.pre_empenho_despesa ped ON 
            ped.exercicio = pe.exercicio AND 
            ped.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        orcamento.despesa d ON 
            d.exercicio = ped.exercicio AND 
            d.cod_despesa = ped.cod_despesa 
        INNER JOIN 
        orcamento.conta_despesa c ON 
            c.exercicio = d.exercicio AND 
            c.cod_conta = d.cod_conta 
    WHERE 
        e.exercicio = ''' || (CAST(stExercicio AS INTEGER) - 1)  || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND 
        nl.dt_liquidacao <= TO_DATE(''' || stDtFimAnterior || '''::varchar, ''dd/mm/yyyy'') ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( c.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;

    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;    
    
    
    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 

    UNION ALL

    SELECT 
        c.exercicio, 
        c.cod_estrutural, 
        COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado 
    FROM 
        empenho.nota_liquidacao_item nli 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio = nli.exercicio AND 
            nl.cod_entidade = nli.cod_entidade AND 
            nl.cod_nota = nli.cod_nota 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = nl.exercicio_empenho AND 
            e.cod_entidade = nl.cod_entidade AND 
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.pre_empenho pe ON 
            pe.exercicio = e.exercicio AND 
            pe.cod_pre_empenho = e.cod_pre_empenho 
        INNER JOIN 
        empenho.pre_empenho_despesa ped ON 
            ped.exercicio = pe.exercicio AND 
            ped.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        orcamento.despesa d ON 
            d.exercicio = ped.exercicio AND 
            d.cod_despesa = ped.cod_despesa 
        INNER JOIN 
        orcamento.conta_despesa c ON 
            c.exercicio = d.exercicio AND 
            c.cod_conta = d.cod_conta 
    WHERE 
        e.exercicio = ''' || (CAST(stExercicio AS INTEGER) - 2)  || ''' AND
        e.cod_entidade IN (' || stEntidades || ') AND
        nl.dt_liquidacao <= TO_DATE(''' || stDtFimAnterior2 || '''::varchar, ''dd/mm/yyyy'') ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( c.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;

    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;    
    
    
    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 

    UNION ALL

    SELECT 
        c.exercicio, 
        c.cod_estrutural, 
        COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado 
    FROM 
        empenho.nota_liquidacao_item nli 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio = nli.exercicio AND 
            nl.cod_entidade = nli.cod_entidade AND 
            nl.cod_nota = nli.cod_nota 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = nl.exercicio_empenho AND 
            e.cod_entidade = nl.cod_entidade AND 
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.pre_empenho pe ON 
            pe.exercicio = e.exercicio AND 
            pe.cod_pre_empenho = e.cod_pre_empenho 
        INNER JOIN 
        empenho.pre_empenho_despesa ped ON 
            ped.exercicio = pe.exercicio AND 
            ped.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        orcamento.despesa d ON 
            d.exercicio = ped.exercicio AND 
            d.cod_despesa = ped.cod_despesa 
        INNER JOIN 
        orcamento.conta_despesa c ON 
            c.exercicio = d.exercicio AND 
            c.cod_conta = d.cod_conta 
    WHERE 
        e.exercicio = ''' || (CAST(stExercicio AS INTEGER) - 3)  || ''' AND
        e.cod_entidade IN (' || stEntidades || ') AND
        nl.dt_liquidacao <= TO_DATE(''' || stDtFimAnterior3 || '''::varchar, ''dd/mm/yyyy'') ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( c.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;

    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;    
    
    
    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 
    );
    
    CREATE UNIQUE INDEX unq_desp_liq ON tmp_desp_liq (exercicio, cod_estrutural) ;
    ';

    EXECUTE stSQL;
    
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_desp_est AS (
    SELECT 
        c.exercicio, 
        c.cod_estrutural, 
        COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_anulado 
    FROM 
        empenho.nota_liquidacao_item_anulado nlia
        INNER JOIN
        empenho.nota_liquidacao_item nli ON
            nlia.exercicio = nli.exercicio AND
            nlia.cod_nota = nli.cod_nota AND
            nlia.num_item = nli.num_item AND
            nlia.exercicio_item = nli.exercicio_item AND
            nlia.cod_pre_empenho = nli.cod_pre_empenho AND
            nlia.cod_entidade = nli.cod_entidade 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio = nli.exercicio AND 
            nl.cod_entidade = nli.cod_entidade AND 
            nl.cod_nota = nli.cod_nota 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = nl.exercicio_empenho AND 
            e.cod_entidade = nl.cod_entidade AND 
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.pre_empenho pe ON 
            pe.exercicio = e.exercicio AND 
            pe.cod_pre_empenho = e.cod_pre_empenho 
        INNER JOIN 
        empenho.pre_empenho_despesa ped ON 
            ped.exercicio = pe.exercicio AND 
            ped.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        orcamento.despesa d ON 
            d.exercicio = ped.exercicio AND 
            d.cod_despesa = ped.cod_despesa 
        INNER JOIN 
        orcamento.conta_despesa c ON 
            c.exercicio = d.exercicio AND 
            c.cod_conta = d.cod_conta 
    WHERE 
        e.exercicio = ''' || (CAST(stExercicio AS INTEGER) - 1) || ''' AND 
        e.cod_entidade IN (' || stEntidades || ') AND
        TO_DATE(nlia."timestamp"::varchar, ''yyyy-mm-dd'') <= TO_DATE(''' || stDtFimAnterior || '''::varchar, ''dd/mm/yyyy'')
        ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( c.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;
    
    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;    
    
    
    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 

    UNION ALL

    SELECT 
        c.exercicio, 
        c.cod_estrutural, 
        COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_anulado 
    FROM 
        empenho.nota_liquidacao_item_anulado nlia
        INNER JOIN
        empenho.nota_liquidacao_item nli ON
            nlia.exercicio = nli.exercicio AND
            nlia.cod_nota = nli.cod_nota AND
            nlia.num_item = nli.num_item AND
            nlia.exercicio_item = nli.exercicio_item AND
            nlia.cod_pre_empenho = nli.cod_pre_empenho AND
            nlia.cod_entidade = nli.cod_entidade 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio = nli.exercicio AND 
            nl.cod_entidade = nli.cod_entidade AND 
            nl.cod_nota = nli.cod_nota 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = nl.exercicio_empenho AND 
            e.cod_entidade = nl.cod_entidade AND 
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.pre_empenho pe ON 
            pe.exercicio = e.exercicio AND 
            pe.cod_pre_empenho = e.cod_pre_empenho 
        INNER JOIN 
        empenho.pre_empenho_despesa ped ON 
            ped.exercicio = pe.exercicio AND 
            ped.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        orcamento.despesa d ON 
            d.exercicio = ped.exercicio AND 
            d.cod_despesa = ped.cod_despesa 
        INNER JOIN 
        orcamento.conta_despesa c ON 
            c.exercicio = d.exercicio AND 
            c.cod_conta = d.cod_conta 
    WHERE 
        e.exercicio = ''' || (CAST(stExercicio AS INTEGER) - 2) || ''' AND
        e.cod_entidade IN (' || stEntidades || ') AND
        TO_DATE(nlia."timestamp"::varchar, ''yyyy-mm-dd'') <= TO_DATE(''' || stDtFimAnterior2 || '''::varchar, ''dd/mm/yyyy'')
        ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( c.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;
    
    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;    
    
    
    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 

    UNION ALL

    SELECT 
        c.exercicio, 
        c.cod_estrutural, 
        COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_anulado 
    FROM 
        empenho.nota_liquidacao_item_anulado nlia
        INNER JOIN
        empenho.nota_liquidacao_item nli ON
            nlia.exercicio = nli.exercicio AND
            nlia.cod_nota = nli.cod_nota AND
            nlia.num_item = nli.num_item AND
            nlia.exercicio_item = nli.exercicio_item AND
            nlia.cod_pre_empenho = nli.cod_pre_empenho AND
            nlia.cod_entidade = nli.cod_entidade 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio = nli.exercicio AND 
            nl.cod_entidade = nli.cod_entidade AND 
            nl.cod_nota = nli.cod_nota 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = nl.exercicio_empenho AND 
            e.cod_entidade = nl.cod_entidade AND 
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.pre_empenho pe ON 
            pe.exercicio = e.exercicio AND 
            pe.cod_pre_empenho = e.cod_pre_empenho 
        INNER JOIN 
        empenho.pre_empenho_despesa ped ON 
            ped.exercicio = pe.exercicio AND 
            ped.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        orcamento.despesa d ON 
            d.exercicio = ped.exercicio AND 
            d.cod_despesa = ped.cod_despesa 
        INNER JOIN 
        orcamento.conta_despesa c ON 
            c.exercicio = d.exercicio AND 
            c.cod_conta = d.cod_conta 
    WHERE 
        e.exercicio = ''' || (CAST(stExercicio AS INTEGER) - 3) || ''' AND
        e.cod_entidade IN (' || stEntidades || ') AND
        TO_DATE(nlia."timestamp"::varchar, ''yyyy-mm-dd'') <= TO_DATE(''' || stDtFimAnterior3 || '''::varchar, ''yyyy-mm-dd'')
        ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( c.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;
    
    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;    
    
    
    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 
    );
    
    CREATE UNIQUE INDEX unq_desp_est ON tmp_desp_est (exercicio, cod_estrutural) ;
    ';

    EXECUTE stSQL;
    
    
    -- Despesas Fixadas do Exercicio Corrente
    -- Dotações Atualizadas (Inicial + Suplementações)

    stSQL := '
    CREATE TEMPORARY TABLE tmp_dotacao_corrente AS (
    SELECT
        cd.exercicio,
        cd.cod_estrutural, 
        SUM(COALESCE(d.vl_original,0.00)) AS vl_original, 
        COALESCE((SUM(COALESCE(sups.vl_suplementado,0.00)) - SUM(COALESCE(supr.vl_reduzido,0.00))), 0.00) AS vl_credito_adicional 
    FROM
        orcamento.conta_despesa cd
        INNER JOIN
        orcamento.despesa d ON
            d.exercicio = cd.exercicio AND
            d.cod_conta = cd.cod_conta
            
        --Suplementacoes
        
        LEFT JOIN 
        
        (SELECT
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
            sup.exercicio = ''' || stExercicio || ''' AND 
            sup.dt_suplementacao BETWEEN TO_DATE(''' || stDtIniCorrente || '''::varchar, ''dd/mm/yyyy'') AND 
                                         TO_DATE(''' || stDtFimCorrente || '''::varchar, ''dd/mm/yyyy'') 
        GROUP BY
            sups.exercicio, 
            sups.cod_despesa
        ) sups ON
            sups.exercicio = d.exercicio AND 
            sups.cod_despesa = d.cod_despesa 
        
        LEFT JOIN
        
        (SELECT
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
            sup.exercicio = ''' || stExercicio || ''' AND 
            sup.dt_suplementacao BETWEEN TO_DATE(''' || stDtIniCorrente || '''::varchar, ''dd/mm/yyyy'') AND 
                                         TO_DATE(''' || stDtFimCorrente || '''::varchar, ''dd/mm/yyyy'') 
        GROUP BY 
            supr.exercicio, 
            supr.cod_despesa
        ) AS supr ON
            supr.exercicio = d.exercicio AND 
            supr.cod_despesa = d.cod_despesa 
    WHERE 
        cd.exercicio = ''' || stExercicio || ''' AND 
        d.cod_entidade IN (' || stEntidades || ') 
    ';
    
    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN 
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;
    
    stSQL := stSQL || ' 
    GROUP BY 
        cd.exercicio, 
        cd.cod_estrutural 
    );
    
    CREATE UNIQUE INDEX unq_dotacao_corrente ON tmp_dotacao_corrente (exercicio, cod_estrutural) ;';
    
    EXECUTE stSQL;
    
    
    -- Despesas Previstas do Exercicio Seguinte
    -- Dotação Inicial considerada

    stSQL := '
    CREATE TEMPORARY TABLE tmp_dotacao_seguinte AS (
    SELECT 
        c.exercicio, 
        c.cod_estrutural,
        COALESCE(SUM(d.vl_original), 0.00) AS vl_dotacao 
    FROM 
        orcamento.conta_despesa c 
        INNER JOIN 
        orcamento.despesa d ON 
            d.exercicio = c.exercicio AND 
            d.cod_conta = c.cod_conta 
    WHERE 
        c.exercicio = ''' || (CAST(stExercicio AS INTEGER) + 1) || ''' AND 
        d.cod_entidade IN (' || stEntidades || ') ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( c.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( c.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;

    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND d.cod_recurso = ' || inCodRecurso || ' ';
    END IF;
    
    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 
    );
    
    CREATE UNIQUE INDEX unq_dotacao_seguinte ON tmp_dotacao_seguinte (exercicio, cod_estrutural) ;';

    EXECUTE stSQL;    
  
    -- Select de Retorno

    stSQL := '
    SELECT
        CAST(codigo as text) as codigo,
        CAST(nivel as INTEGER) as nivel,
        CAST(descricao as text) as descricao,
        CAST(vl_dot_atu as numeric(14,2)) as vl_dot_atu,
        CAST(vl_liq01 as numeric(14,2)) as vl_liq01,
        CAST(vl_liq02 as numeric(14,2)) as vl_liq02,
        CAST(vl_liq03 as numeric(14,2)) as vl_liq03,
        CAST(vl_prev as numeric(14,2)) as vl_prev 
    FROM (
    SELECT
        codigo,
        nivel,
        c.descricao, 
        SUM(vl_dot_atu) as vl_dot_atu,
        SUM(vl_liq01) as vl_liq01,
        SUM(vl_liq02) as vl_liq02,
        SUM(vl_liq03) as vl_liq03,
        SUM(vl_prev) as vl_prev,
        tb.cod_estrutural 
    FROM
    (
    (SELECT
        c.cod_estrutural, 
        SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ') AS codigo,
        publico.fn_nivel(c.cod_estrutural) AS nivel, 
        COALESCE((SELECT SUM(COALESCE(t.vl_original, 0.00)) FROM tmp_dotacao_corrente t WHERE t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%'' ), 0.00) AS vl_dot_atu,
        0.00 AS vl_liq01, 
        0.00 AS vl_liq02, 
        0.00 AS vl_liq03, 
        0.00 AS vl_prev
    FROM 
        orcamento.conta_despesa c 
    WHERE 
        c.exercicio = ''' || stExercicio || ''' 
    GROUP BY 
        c.cod_estrutural, 
        c.descricao 
    ORDER BY 
        c.cod_estrutural)

    UNION ALL

    (SELECT
        c.cod_estrutural,
        SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ') AS codigo,
        publico.fn_nivel(c.cod_estrutural) AS nivel,
        0.00 AS vl_dot_atu,
        COALESCE((SELECT (sum(COALESCE(t.vl_liquidado, 0.00)) - sum(COALESCE(t1.vl_anulado, 0.00))) FROM tmp_desp_liq t LEFT JOIN tmp_desp_est t1 ON t1.cod_estrutural = t.cod_estrutural AND t1.exercicio = t.exercicio WHERE t.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 1) AS VARCHAR ) AND t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_liq01,
        0.00 AS vl_liq02,
        0.00 AS vl_liq03,
        0.00 AS vl_prev
    FROM
        orcamento.conta_despesa c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 1) AS VARCHAR ) 
    GROUP BY
        c.cod_estrutural,
        c.descricao
    ORDER BY
        c.cod_estrutural)

    UNION ALL

    (SELECT
        c.cod_estrutural,
        SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ') AS codigo,
        publico.fn_nivel(c.cod_estrutural) AS nivel,
        0.00 AS vl_dot_atu,
        0.00 AS vl_liq01,
        COALESCE((SELECT (sum(COALESCE(t.vl_liquidado, 0.00)) - SUM(COALESCE(t1.vl_anulado, 0.00))) FROM tmp_desp_liq t LEFT JOIN tmp_desp_est t1 ON t1.cod_estrutural = t.cod_estrutural AND t1.exercicio = t.exercicio WHERE t.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 2) AS VARCHAR ) AND t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_liq02,
        0.00 AS vl_liq03,
        0.00 AS vl_prev
    FROM
        orcamento.conta_despesa c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 2) AS VARCHAR )
    GROUP BY
        c.cod_estrutural,
        c.descricao
    ORDER BY
        c.cod_estrutural)

    UNION ALL

    (SELECT
        c.cod_estrutural,
        SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ') AS codigo,
        publico.fn_nivel(c.cod_estrutural) AS nivel,
        0.00 AS vl_dot_atu,
        0.00 AS vl_liq01,
        0.00 AS vl_liq02,
        COALESCE((SELECT (SUM(COALESCE(t.vl_liquidado, 0.00)) - SUM(COALESCE(t1.vl_anulado, 0.00))) FROM tmp_desp_liq t LEFT JOIN tmp_desp_est t1 ON t1.cod_estrutural = t.cod_estrutural AND t1.exercicio = t.exercicio WHERE t.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 3) AS VARCHAR ) AND t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_liq03,
        0.00 AS vl_prev
    FROM
        orcamento.conta_despesa c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 3) AS VARCHAR )
    GROUP BY
        c.cod_estrutural,
        c.descricao
    ORDER BY
        c.cod_estrutural)

    UNION ALL

    (SELECT
        c.cod_estrutural,
        SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ') AS codigo,
        publico.fn_nivel(c.cod_estrutural) AS nivel,
        0.00 AS vl_dot_atu,
        0.00 AS vl_liq01,
        0.00 AS vl_liq02,
        0.00 AS vl_liq03,
        COALESCE((SELECT SUM(COALESCE(t.vl_dotacao, 0.00)) FROM tmp_dotacao_seguinte t WHERE t.exercicio = ''' || (CAST(stExercicio AS INTEGER) + 1) || ''' AND t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_prev
    FROM
        orcamento.conta_despesa c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) + 1) AS VARCHAR )
    GROUP BY
        c.cod_estrutural,
        c.descricao
    ORDER BY
        c.cod_estrutural)

    ) AS tb
    JOIN orcamento.conta_despesa c ON (
            c.cod_estrutural = tb.cod_estrutural AND
            c.exercicio = ''' || stExercicio || ''' )
    WHERE
        (
        vl_dot_atu <> 0 OR
        vl_liq01   <> 0 OR
        vl_liq02   <> 0 OR
        vl_liq03   <> 0 OR
        vl_prev    <> 0         
        )
    ';
    
    -- -------------------------------------------------
    -- Codigos Estruturais
    
    -- Mínimo e Máximo
    IF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) > 0) THEN         
        stSQL := stSQL || ' AND ( tb.cod_estrutural BETWEEN ''' || stCodEstruturalMin || ''' AND ''' || stCodEstruturalMax || ''' ) ';
    -- Mínimo
    ELSEIF (LENGTH(stCodEstruturalMin) > 0 AND LENGTH(stCodEstruturalMax) = 0) THEN         
        stSQL := stSQL || ' AND ( tb.cod_estrutural >= ''' || stCodEstruturalMin || ''' ) ';
    -- Máximo
    ELSEIF (LENGTH(stCodEstruturalMin) = 0 AND LENGTH(stCodEstruturalMax) > 0) THEN     
        stSQL := stSQL || ' AND ( tb.cod_estrutural <= ''' || stCodEstruturalMax || ''' ) ';
    END IF;
    
    -- -------------------------------------------------
    -- Demonstrar Sintéticas
    
    stSQL := stSQL || ' AND publico.fn_nivel(tb.cod_estrutural) <= 5 ';

    stSQL := stSQL || '
     GROUP BY
        codigo,
        nivel,
        tb.cod_estrutural,
        c.descricao
     ORDER BY
        codigo
    ';

    stSQL := stSQL || '
        ) tabela
    ';
    
    
    FOR reReg IN EXECUTE stSQL
    LOOP 
        RETURN NEXT reReg;	
    END LOOP;

    DROP TABLE tmp_dotacao_corrente;
    DROP TABLE tmp_dotacao_seguinte;
    DROP TABLE tmp_desp_liq;
    DROP TABLE tmp_desp_est;
    
    RETURN;

END;

$$ LANGUAGE 'plpgsql';

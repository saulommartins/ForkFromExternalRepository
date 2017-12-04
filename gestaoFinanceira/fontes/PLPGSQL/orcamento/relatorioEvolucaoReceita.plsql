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
    * Relatório Demonstrativo da Evolução da Receita
    * Gestao Financeira - Modulo Orçamento - Funcionalidade Relatórios
    * Data de Criação: 16/07/2008


    * @author Leopoldo Braga Barreiro

    * Casos de uso: UC.02.01.37

    $Id: $

*/

CREATE OR REPLACE FUNCTION orcamento.fn_relatorio_evolucao_receita(stExercicio VARCHAR, stEntidades VARCHAR, inSinteticas INTEGER, inCodRecurso INTEGER) RETURNS SETOF RECORD AS 

$$

DECLARE

    stDtIniCorrente     VARCHAR;
    stDtFimCorrente     VARCHAR;
    stSQL               VARCHAR;
    inTamanho           INTEGER;
    inTamanhoDedutora   INTEGER;
    reReg               RECORD;

BEGIN

    -- Definicao de Datas 
    
    stDtIniCorrente := '01/01/' || stExercicio;
    stDtFimCorrente := '31/12/' || stExercicio;
    
    IF inSinteticas > 0 THEN 
        inTamanho := 10;
        inTamanhoDedutora := 12;
    ELSE 
        inTamanho := 7;
        inTamanhoDedutora := 7;
    END IF; 
    
    
    -- Receitas Arrecadadas em Exercícios Anteriores
    
    stSQL := '
    CREATE TEMPORARY TABLE tmp_arrecadacao_anterior AS (
    SELECT
        exercicio,
        cod_estrutural,
        (COALESCE(SUM(COALESCE(valor, 0.00)), 0.00) * -1) AS valor
    FROM (
        SELECT
            c.exercicio, 
            c.cod_estrutural, 
            vl.vl_lancamento AS valor 
        FROM
            contabilidade.valor_lancamento vl 
            INNER JOIN
            contabilidade.lancamento la ON
                la.exercicio = vl.exercicio AND
                la.cod_entidade = vl.cod_entidade AND
                la.tipo = vl.tipo AND
                la.cod_lote = vl.cod_lote AND
                la.sequencia = vl.sequencia 
            INNER JOIN
            contabilidade.lote lo ON 
                lo.exercicio = la.exercicio AND
                lo.cod_entidade = la.cod_entidade AND
                lo.tipo = la.tipo AND
                lo.cod_lote = la.cod_lote 
            INNER JOIN
            contabilidade.lancamento_receita lr ON 
                lr.exercicio = la.exercicio AND 
                lr.cod_entidade = la.cod_entidade AND 
                lr.tipo = la.tipo AND 
                lr.cod_lote = la.cod_lote AND 
                lr.sequencia = la.sequencia 
            INNER JOIN  
            orcamento.receita r ON
                r.cod_receita = lr.cod_receita AND
                r.exercicio = lr.exercicio 
            INNER JOIN 
            orcamento.conta_receita c ON
                c.exercicio = r.exercicio AND
                c.cod_conta = r.cod_conta 
        WHERE 
            r.exercicio BETWEEN ''' || (CAST(stExercicio AS INTEGER) - 3) || ''' AND ''' || (CAST(stExercicio AS INTEGER) - 1) || ''' AND 
            r.cod_entidade IN (' || stEntidades || ') AND 
            lr.estorno = true AND 
            -- lancamento receita tipo A (arrecadação) 
            lr.tipo = ''A'' AND 
            -- tipo_valor = D (debito) 
            vl.tipo_valor = ''D'' ';

            IF (inCodRecurso > 0) THEN
                stSQL := stSQL || ' AND r.cod_recurso = ' || inCodRecurso || ' ';
            END IF;

        stSQL := stSQL || '
          
        UNION ALL
        
        SELECT
            c.exercicio, 
            c.cod_estrutural, 
            vl.vl_lancamento AS valor 
        FROM
            contabilidade.valor_lancamento vl 
            INNER JOIN
            contabilidade.lancamento la ON
                la.exercicio = vl.exercicio AND
                la.cod_entidade = vl.cod_entidade AND
                la.tipo = vl.tipo AND
                la.cod_lote = vl.cod_lote AND
                la.sequencia = vl.sequencia 
            INNER JOIN
            contabilidade.lote lo ON 
                lo.exercicio = la.exercicio AND
                lo.cod_entidade = la.cod_entidade AND
                lo.tipo = la.tipo AND
                lo.cod_lote = la.cod_lote 
            INNER JOIN
            contabilidade.lancamento_receita lr ON 
                lr.exercicio = la.exercicio AND 
                lr.cod_entidade = la.cod_entidade AND 
                lr.tipo = la.tipo AND 
                lr.cod_lote = la.cod_lote AND 
                lr.sequencia = la.sequencia 
            INNER JOIN  
            orcamento.receita r ON
                r.cod_receita = lr.cod_receita AND
                r.exercicio = lr.exercicio 
            INNER JOIN 
            orcamento.conta_receita c ON
                c.exercicio = r.exercicio AND
                c.cod_conta = r.cod_conta 
        WHERE 
            r.exercicio BETWEEN ''' || (CAST(stExercicio AS INTEGER) - 3) || ''' AND ''' || (CAST(stExercicio AS INTEGER) - 1) || ''' AND 
            r.cod_entidade IN (' || stEntidades || ') AND 
            lr.estorno = false AND 
            -- lancamento receita tipo A (arrecadação) 
            lr.tipo = ''A'' AND 
            -- tipo_valor = C (credito) 
            vl.tipo_valor = ''C'' ';

            IF (inCodRecurso > 0) THEN
                stSQL := stSQL || ' AND r.cod_recurso = ' || inCodRecurso || ' ';
            END IF;

        stSQL := stSQL || '

        ) as tb
        GROUP BY
            exercicio,
            cod_estrutural 
    ) ';
    
    EXECUTE stSQL;
    
    

    -- Receita Orçada no Exercicio Corrente

    stSQL := '
    CREATE TEMPORARY TABLE tmp_receita_corrente AS (
    SELECT
        c.exercicio,
        c.cod_estrutural, 
        SUM(COALESCE(r.vl_original,0.00)) AS vl_original 
    FROM
        orcamento.conta_receita c
        INNER JOIN 
        orcamento.receita r ON 
            r.exercicio = c.exercicio AND
            r.cod_conta = c.cod_conta 
    WHERE 
        c.exercicio = ''' || stExercicio || ''' AND 
        r.cod_entidade IN (' || stEntidades || ') 
    ';

    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN 
        stSQL := stSQL || ' AND r.cod_recurso = ' || inCodRecurso || ' ';
    END IF;

    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 
    );
    
    CREATE UNIQUE INDEX unq_receita_corrente ON tmp_receita_corrente (exercicio, cod_estrutural) ;';
    
    EXECUTE stSQL;
    
    
    
    -- Receitas Previstas para o Exercicio Seguinte

    stSQL := '
    CREATE TEMPORARY TABLE tmp_receita_seguinte AS (
    SELECT 
        c.exercicio, 
        c.cod_estrutural,
        COALESCE(SUM(COALESCE(r.vl_original, 0.00)), 0.00) AS vl_original 
    FROM 
        orcamento.conta_receita c 
        INNER JOIN 
        orcamento.receita r ON 
            r.exercicio = c.exercicio AND 
            r.cod_conta = c.cod_conta 
    WHERE 
        c.exercicio = ''' || (CAST(stExercicio AS INTEGER) + 1) || ''' AND 
        r.cod_entidade IN (' || stEntidades || ') ';

    -- -------------------------------------------------
    -- Codigo de Recurso
    
    IF (inCodRecurso > 0) THEN
        stSQL := stSQL || ' AND r.cod_recurso = ' || inCodRecurso || ' ';
    END IF;

    stSQL := stSQL || ' 
    GROUP BY 
        c.exercicio, 
        c.cod_estrutural 
    );
    
    CREATE UNIQUE INDEX unq_receita_seguinte ON tmp_receita_seguinte (exercicio, cod_estrutural) ;';

    EXECUTE stSQL;    
    
    

    -- Select de Retorno

    stSQL := '
    SELECT
        CAST(tb.cod_estrutural as VARCHAR), 
        CAST(codigo as text),
        CAST(tb.nivel as INTEGER),
        CAST(c.descricao as text), 
        SUM(COALESCE(vl_orcado, 0.00)), 
        SUM(COALESCE(vl_arrec01, 0.00)),
        SUM(COALESCE(vl_arrec02, 0.00)),
        SUM(COALESCE(vl_arrec03, 0.00)),
        SUM(COALESCE(vl_prev, 0.00)) 
    FROM
    (
    (SELECT ';
    IF ( CAST( stExercicio  AS INTEGER) < 2008) THEN
        stSQL := stSQL || '
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                ''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                ''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             ELSE
                c.cod_estrutural
        END as cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                SUBSTRING(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                SUBSTRING(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                publico.fn_nivel(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                publico.fn_nivel(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             ELSE
                publico.fn_nivel(c.cod_estrutural)
        END as nivel, ';
    ELSE
        stSQL := stSQL || '
        c.cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as integer) = 9) THEN
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanhoDedutora || ' )
            ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        publico.fn_nivel(c.cod_estrutural) as nivel, ';
    END IF;

        stSQL := stSQL || '
        COALESCE((SELECT SUM(COALESCE(t.vl_original, 0.00)) FROM tmp_receita_corrente t WHERE t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%'' ), 0.00) AS vl_orcado,
        0.00 as vl_arrec01,
        0.00 as vl_arrec02,
        0.00 as vl_arrec03,
        0.00 as vl_prev
    FROM
        orcamento.conta_receita c
    WHERE
        c.exercicio = ''' || stExercicio || ''' 
    GROUP BY
        c.cod_estrutural
    ORDER BY
        c.cod_estrutural)

    UNION ALL

    (SELECT ';
    IF ( (CAST( stExercicio  AS INTEGER) -1) < 2008) THEN
        stSQL := stSQL || '
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                ''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                ''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             ELSE
                c.cod_estrutural
        END as cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                SUBSTRING(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                SUBSTRING(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                publico.fn_nivel(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                publico.fn_nivel(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             ELSE
                publico.fn_nivel(c.cod_estrutural)
        END as nivel,
 ';
    ELSE
        stSQL := stSQL || '
        c.cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as integer) = 9) THEN
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanhoDedutora || ' )
            ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        publico.fn_nivel(c.cod_estrutural) as nivel, ';
    END IF;

        stSQL := stSQL || '
        0.00 as vl_orcado,
        COALESCE((SELECT SUM(COALESCE(t.valor, 0.00)) FROM tmp_arrecadacao_anterior t WHERE t.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 1) AS VARCHAR ) AND t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_arrec01,
        0.00 as vl_arrec02,
        0.00 as vl_arrec03,
        0.00 as vl_prev
    FROM
        orcamento.conta_receita c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 1) AS VARCHAR )
    GROUP BY
        c.cod_estrutural
    ORDER BY
        c.cod_estrutural)

    UNION ALL

    (SELECT ';
    IF ( (CAST( stExercicio  AS INTEGER) - 2 ) < 2008) THEN
        stSQL := stSQL || '
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                ''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                ''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             ELSE
                c.cod_estrutural
        END as cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                SUBSTRING(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                SUBSTRING(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                publico.fn_nivel(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                publico.fn_nivel(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             ELSE
                publico.fn_nivel(c.cod_estrutural)
        END as nivel,
 ';
    ELSE
        stSQL := stSQL || '
        c.cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as integer) = 9) THEN
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanhoDedutora || ' )
            ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        publico.fn_nivel(c.cod_estrutural) as nivel, ';
    END IF;
        stSQL := stSQL || '
        0.00 as vl_orcado,
        0.00 as vl_arrec01,
        COALESCE((SELECT SUM(COALESCE(t.valor, 0.00)) FROM tmp_arrecadacao_anterior t WHERE t.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 2) AS VARCHAR ) AND t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_arrec02,
        0.00 as vl_arrec03,
        0.00 as vl_prev
    FROM
        orcamento.conta_receita c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 2) AS VARCHAR )
    GROUP BY
        c.cod_estrutural
    ORDER BY
        c.cod_estrutural)

    UNION ALL

    (SELECT ';
    IF ( (CAST( stExercicio  AS INTEGER) - 3) < 2008) THEN
        stSQL := stSQL || '
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                ''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                ''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             ELSE
                c.cod_estrutural
        END as cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                SUBSTRING(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                SUBSTRING(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                publico.fn_nivel(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                publico.fn_nivel(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             ELSE
                publico.fn_nivel(c.cod_estrutural)
        END as nivel,
 ';
    ELSE
        stSQL := stSQL || '
        c.cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as integer) = 9) THEN
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanhoDedutora || ' )
            ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        publico.fn_nivel(c.cod_estrutural) as nivel, ';
    END IF;
        stSQL := stSQL || '
        0.00 as vl_orcado,
        0.00 as vl_arrec01,
        0.00 as vl_arrec02,
        COALESCE((SELECT SUM(COALESCE(t.valor, 0.00)) FROM tmp_arrecadacao_anterior t WHERE t.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 3) AS VARCHAR ) AND t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_arrec03,
        0.00 as vl_prev
    FROM
        orcamento.conta_receita c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) - 3) AS VARCHAR )
    GROUP BY
        c.cod_estrutural
    ORDER BY
        c.cod_estrutural)

    UNION ALL

    (SELECT ';
    IF ( (CAST( stExercicio  AS INTEGER) + 1) < 2008) THEN
        stSQL := stSQL || '
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                ''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                ''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) )
             ELSE
                c.cod_estrutural
        END as cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                SUBSTRING(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                SUBSTRING(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ), 1, ' || inTamanhoDedutora || ' )
             ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as varchar) = ''9'' AND cast(substr(c.cod_estrutural, 1, 3) as varchar) != ''9.0'') THEN
                publico.fn_nivel(''9.1.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             WHEN (cast(substr(c.cod_estrutural, 1, 3) as varchar) = ''9.0'') THEN
                publico.fn_nivel(''9.0.''|| substr(c.cod_estrutural, 3, length(c.cod_estrutural) ))
             ELSE
                publico.fn_nivel(c.cod_estrutural)
        END as nivel,
 ';
    ELSE
        stSQL := stSQL || '
        c.cod_estrutural,
        CASE WHEN (cast(substr(c.cod_estrutural, 1, 1) as integer) = 9) THEN
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanhoDedutora || ' )
            ELSE
                SUBSTRING(c.cod_estrutural, 1, ' || inTamanho || ' )
        END as codigo,
        publico.fn_nivel(c.cod_estrutural) as nivel, ';
    END IF;
        stSQL := stSQL || '
        0.00 as vl_orcado,
        0.00 as vl_arrec01,
        0.00 as vl_arrec02,
        0.00 as vl_arrec03,
        COALESCE((SELECT SUM(COALESCE(t.vl_original, 0.00)) FROM tmp_receita_seguinte t WHERE t.cod_estrutural LIKE publico.fn_mascarareduzida(c.cod_estrutural) || ''%''), 0.00) AS vl_prev 
    FROM
        orcamento.conta_receita c
    WHERE
        c.exercicio = CAST( (CAST(''' || stExercicio || ''' AS INTEGER) + 1) AS VARCHAR )
    GROUP BY
        c.cod_estrutural
    ORDER BY
        c.cod_estrutural)
    ) AS tb
    JOIN orcamento.conta_receita c ON (
            c.cod_estrutural = tb.cod_estrutural AND
            c.exercicio = ''' || stExercicio || ''' ) 
    WHERE
        TRUE 
   ';
    -- -------------------------------------------------
    -- Demonstrar Sintéticas
    
    IF inSinteticas > 0 THEN stSQL := stSQL || ' AND publico.fn_nivel(tb.cod_estrutural) <= 5 ';
    ELSE stSQL := stSQL || ' AND publico.fn_nivel(tb.cod_estrutural) <= 4 ';    
    END IF;
    
    stSQL := stSQL || ' AND 
        (
        vl_orcado  <> 0 OR
        vl_arrec01 <> 0 OR
        vl_arrec02 <> 0 OR
        vl_arrec03 <> 0 OR
        vl_prev    <> 0  
        ) 
    ';
    
    stSQL := stSQL || '
     GROUP BY
        tb.cod_estrutural,
        codigo,
        nivel,
        c.descricao ,
        c.exercicio
     ORDER BY
        tb.cod_estrutural
    ';

 
    
    
    FOR reReg IN EXECUTE stSQL
    LOOP 
        RETURN NEXT reReg;	
    END LOOP;

    DROP TABLE tmp_arrecadacao_anterior;
    DROP TABLE tmp_receita_corrente;
    DROP TABLE tmp_receita_seguinte;
    
    RETURN;

END;

$$ LANGUAGE 'plpgsql';

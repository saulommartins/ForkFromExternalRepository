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

CREATE OR REPLACE FUNCTION tceal.fn_detalhamento_receitas(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS NUMERIC AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stCodEstruturalInicial  ALIAS FOR $6;
    stCodEstruturalFinal    ALIAS FOR $7;
    stCodReduzidoInicial    ALIAS FOR $8;
    stCodReduzidoFinal      ALIAS FOR $9;
    inCodRecurso            ALIAS FOR $10;
    stDestinacaoRecurso     ALIAS FOR $11;
    inCodDetalhamento       ALIAS FOR $12;
    dtInicioAno             VARCHAR := '';
    dtFimAno                VARCHAR := '';
    stSql                   VARCHAR := '';
    stMascClassReceita      VARCHAR := '';
    stMascRecurso           VARCHAR := '';
    reRegistro              NUMERIC := 0.00;

BEGIN
        dtInicioAno := '01/01/' || stExercicio;

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural AS cod_estrutural
                , lote.dt_lote       AS data
                , vl.vl_lancamento   AS valor
                , vl.oid             AS primeira
            FROM
                contabilidade.valor_lancamento      AS vl   ,
                orcamento.conta_receita             AS ocr  ,
                orcamento.receita                   AS ore  ,
                contabilidade.lancamento_receita    AS lr   ,
                contabilidade.lancamento            AS lan  ,
                contabilidade.lote                  AS lote
                
            WHERE
                    ore.cod_entidade    IN ('|| stCodEntidades ||')
                AND ore.exercicio       = '|| quote_literal(stExercicio) ||'

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
                  ocr.cod_estrutural AS cod_estrutural
                , lote.dt_lote       AS data
                , vl.vl_lancamento   AS valor
                , vl.oid             AS segunda
            FROM
                contabilidade.valor_lancamento      AS vl   ,
                orcamento.conta_receita             AS ocr  ,
                orcamento.receita                   AS ore  ,
                contabilidade.lancamento_receita    AS lr   ,
                contabilidade.lancamento            AS lan  ,
                contabilidade.lote                  AS lote

            WHERE
                    ore.cod_entidade    IN('|| stCodEntidades ||')
                AND ore.exercicio       = '|| quote_literal(stExercicio) ||'
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
                AND lote.tipo           = lan.tipo )';

        EXECUTE stSql;


        stSql := '
            
            SELECT arrecadado_periodo FROM (
                            SELECT tbl.cod_estrutural
                                , tbl.receita
                                , tbl.recurso
                                , tbl.descricao
                                , COALESCE(SUM(tbl.arrecadado_periodo),0.00) AS arrecadado_periodo
                            FROM (
                                SELECT
                                    ocr.cod_estrutural AS cod_estrutural,
                                    r.cod_receita AS receita,
                                    rec.masc_recurso_red AS recurso,
                                    ocr.descricao AS descricao,
                                    orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                                            ,'|| quote_literal(dtInicial) ||'
                                                                            ,'|| quote_literal(dtFinal)   ||'
                                    ) AS arrecadado_periodo
                                
                                FROM orcamento.conta_receita ocr
                      
                      LEFT OUTER JOIN orcamento.receita AS r ON
                                      ocr.exercicio = r.exercicio AND
                                      ocr.cod_conta = r.cod_conta AND
                                      r.cod_entidade    IN ('|| stCodEntidades ||') AND
                                      r.exercicio       = '|| quote_literal(stExercicio) ||'
                            
                            LEFT JOIN orcamento.recurso('|| quote_literal(stExercicio) ||') as rec ON
                                      rec.cod_recurso = r.cod_recurso AND
                                      rec.exercicio   = r.exercicio
                                
                                WHERE
                                      ocr.cod_conta = ocr.cod_conta
                                  AND ocr.exercicio =  '|| quote_literal(stExercicio) ||'
                                    ';
                
                                    if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '') then
                                        stSql := stSql ||' AND ocr.cod_estrutural >= '|| quote_literal(stCodEstruturalInicial) ||' ';
                                    end if;
                                    
                                    if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '') then
                                        stSql := stSql ||' AND ocr.cod_estrutural <= '|| quote_literal(stCodEstruturalFinal) ||' ';
                                    end if;
                                    
                                    if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '') then
                                        stSql := stSql ||' AND r.cod_receita >= '|| quote_literal(stCodReduzidoInicial) ||' ';
                                    end if;
                                    
                                    if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '') then
                                        stSql := stSql ||' AND r.cod_receita <= '|| quote_literal(stCodReduzidoFinal) ||' ';
                                    end if;
                                    
                                    if (inCodRecurso is not null and inCodRecurso <> '') then
                                        stSql := stSql ||' AND r.cod_recurso = '|| quote_literal(inCodRecurso) ||' ';
                                    end if;
                                    
                                    if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                                            stSql := stSql ||' AND rec.masc_recurso_red like '|| quote_literal(stDestinacaoRecurso||'%') ||' ';
                                    end if;
                                    
                                    if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
                                            stSql := stSql ||' AND rec.cod_detalhamento = '|| quote_literal(inCodDetalhamento) ||' ';
                                    end if;
                                    
                                    stSql := stSql ||' '|| stFiltro ||' ORDER BY ocr.cod_estrutural
                                ) AS tbl
                
                                    WHERE
                                        orcamento.fn_movimento_balancete_receita( '|| quote_literal(stExercicio) ||'
                                                                                    ,publico.fn_mascarareduzida(tbl.cod_estrutural)
                                                                                    ,'|| quote_literal(stCodEntidades) ||'
                                                                                    ,'|| quote_literal(dtInicioAno) ||'
                                                                                    ,'|| quote_literal(dtFinal) ||'
                                                                                    ) = true
                                                                                    
                                    GROUP BY tbl.cod_estrutural
                                        , tbl.receita
                                        , tbl.recurso
                                        , tbl.descricao
                        ) AS tabela ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
    END LOOP;
    
    DROP TABLE tmp_valor;
    
    RETURN reRegistro;
    
END;
$$ language 'plpgsql';

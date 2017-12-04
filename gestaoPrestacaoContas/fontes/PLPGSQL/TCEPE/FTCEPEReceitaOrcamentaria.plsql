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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 27052 $
* $Name$
* $Author: cako $
* $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $
*
* Casos de uso: uc-02.01.21
                uc-02.08.07
*/

CREATE OR REPLACE FUNCTION tcepe.receita_orcamentaria (varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    dtInicial               ALIAS FOR $2;
    dtFinal                 ALIAS FOR $3;
    stCodEntidades          ALIAS FOR $4;
    
    dtInicioAno             VARCHAR   := '';
    dtFimAno                VARCHAR   := '';
    stSql                   VARCHAR   := '';
    stMascClassReceita      VARCHAR   := '';
    stMascRecurso           VARCHAR   := '';
    reRegistro              RECORD;
BEGIN
        dtInicioAno := '01/01/' || stExercicio;

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
                , lr.estorno         as estorno
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
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
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
                , lr.estorno         as estorno
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

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
            SELECT tbl.cod_estrutural
                 , tbl.receita
                 , tbl.recurso
                 , tbl.descricao
                 , coalesce(sum(tbl.arrecadado_periodo),0.00) AS arrecadacao_periodo
                 , tbl.tipo_registro
            FROM (
            
             SELECT ocr.cod_estrutural   AS cod_estrutural
                  , r.cod_receita        AS receita
                  , cfr.cod_recurso      AS recurso
                  , ocr.descricao        AS descricao
                  , tcepe.somatorio_receita_orcamentaria( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                        , '|| quote_literal(dtInicial) ||'
                                                        , '|| quote_literal(dtFinal)   ||'
                                                        , ''false''
                    ) AS arrecadado_periodo
                  , 1 AS tipo_registro
                
                FROM orcamento.conta_receita ocr
     
     LEFT OUTER JOIN orcamento.receita AS r
                  ON ocr.exercicio  = r.exercicio
                 AND ocr.cod_conta  = r.cod_conta
                 AND r.cod_entidade IN ('|| stCodEntidades ||')
                 AND r.exercicio    = '|| quote_literal(stExercicio) ||'
                        
           LEFT JOIN orcamento.recurso('|| quote_literal(stExercicio) ||') AS rec
                  ON rec.cod_recurso = r.cod_recurso
                 AND rec.exercicio   = r.exercicio
                 
           LEFT JOIN tcepe.codigo_fonte_recurso AS cfr
                  ON cfr.cod_recurso = rec.cod_recurso
                 AND cfr.exercicio   = rec.exercicio
                 
           LEFT JOIN contabilidade.lancamento_receita
                  ON lancamento_receita.cod_receita = r.cod_receita
                 AND lancamento_receita.exercicio   = r.exercicio
                
               WHERE ocr.cod_conta = ocr.cod_conta
                 AND ocr.exercicio =  '|| quote_literal(stExercicio) ||'
                 AND lancamento_receita.cod_receita IS NULL
                 
            UNION
        
             SELECT ocr.cod_estrutural   AS cod_estrutural
                  , r.cod_receita        AS receita
                  , cfr.cod_recurso      AS recurso
                  , ocr.descricao        AS descricao
                  , tcepe.somatorio_receita_orcamentaria( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                         , '|| quote_literal(dtInicial) ||'
                                                         , '|| quote_literal(dtFinal)   ||'
                                                         , ''true''
                    ) AS arrecadado_periodo
                  , 2 AS tipo_registro
                
                FROM orcamento.conta_receita ocr
     
     LEFT OUTER JOIN orcamento.receita AS r
                  ON ocr.exercicio  = r.exercicio
                 AND ocr.cod_conta  = r.cod_conta
                 AND r.cod_entidade IN ('|| stCodEntidades ||')
                 AND r.exercicio    = '|| quote_literal(stExercicio) ||'
                        
           LEFT JOIN orcamento.recurso('|| quote_literal(stExercicio) ||') AS rec
                  ON rec.cod_recurso = r.cod_recurso
                 AND rec.exercicio   = r.exercicio
                 
           LEFT JOIN tcepe.codigo_fonte_recurso AS cfr
                  ON cfr.cod_recurso = rec.cod_recurso
                 AND cfr.exercicio   = rec.exercicio
                 
           LEFT JOIN contabilidade.lancamento_receita
                  ON lancamento_receita.cod_receita = r.cod_receita
                 AND lancamento_receita.exercicio   = r.exercicio
                
               WHERE ocr.cod_conta = ocr.cod_conta
                 AND ocr.exercicio =  '|| quote_literal(stExercicio) ||'
                 AND lancamento_receita.cod_receita IS NULL
                 
            ) AS tbl

       WHERE orcamento.fn_movimento_balancete_receita( '|| quote_literal(stExercicio) ||'
                                                      , publico.fn_mascarareduzida(tbl.cod_estrutural)
                                                      , '|| quote_literal(stCodEntidades) ||'
                                                      , '|| quote_literal(dtInicioAno) ||'
                                                      , '|| quote_literal(dtFinal) ||'
                                                     ) = true
    GROUP BY tbl.cod_estrutural
           , tbl.receita
           , tbl.recurso
           , tbl.descricao
           , tbl.tipo_registro ';


    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;

    RETURN;
END;
$$ language 'plpgsql';

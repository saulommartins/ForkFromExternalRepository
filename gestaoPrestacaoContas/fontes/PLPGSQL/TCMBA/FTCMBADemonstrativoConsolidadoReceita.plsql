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
* $Revision:$
* $Name$
* $Author: $
* $Date: $
*/

/*
CREATE TYPE fn_demonstrativo_consolidado_receita
    AS (cod_estrutural              varchar,                                           
        receita                     integer,                                           
        recurso                     varchar,                                           
        descricao                   varchar,                                           
        valor_previsto              numeric,                                           
        arrecadado_mes              numeric,                                           
        arrecadado_ate_periodo      numeric,                                           
        anulado_mes                 numeric,
        anulado_ate_periodo         numeric,
        valor_diferenca             numeric
    );
*/

CREATE OR REPLACE FUNCTION tcmba.fn_demonstrativo_consolidado_receita(varchar,varchar,varchar,varchar) RETURNS SETOF fn_demonstrativo_consolidado_receita AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    dtInicial           ALIAS FOR $2;
    dtFinal             ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    
    dtInicioAno         VARCHAR   := '';
    dtFimAno            VARCHAR   := '';
    stSql               VARCHAR   := '';
    stMascClassReceita  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;

BEGIN
        dtInicioAno := '01/01/' || stExercicio;

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
             SELECT conta_receita.cod_estrutural  AS cod_estrutural
                  , lote.dt_lote                  AS data
                  , CASE WHEN lancamento_receita.estorno = TRUE
                       THEN valor_lancamento.vl_lancamento
                       ELSE 0.00
                    END AS valor_estorno
                  , CASE WHEN lancamento_receita.estorno = FALSE
                       THEN valor_lancamento.vl_lancamento
                       ELSE 0.00
                    END AS valor
                  , valor_lancamento.oid           AS primeira
               
               FROM orcamento.receita

         INNER JOIN orcamento.conta_receita
                 ON conta_receita.cod_conta = receita.cod_conta
                AND conta_receita.exercicio = receita.exercicio

         INNER JOIN contabilidade.lancamento_receita
                 ON lancamento_receita.cod_receita  = receita.cod_receita
                AND lancamento_receita.exercicio    = receita.exercicio
                AND lancamento_receita.estorno      = TRUE
                AND lancamento_receita.tipo         = ''A''

         INNER JOIN contabilidade.lancamento
                 ON lancamento.cod_lote        = lancamento_receita.cod_lote
                AND lancamento.sequencia       = lancamento_receita.sequencia
                AND lancamento.exercicio       = lancamento_receita.exercicio
                AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
                AND lancamento.tipo            = lancamento_receita.tipo

         INNER JOIN contabilidade.valor_lancamento
                 ON valor_lancamento.exercicio    = lancamento.exercicio
                AND valor_lancamento.sequencia    = lancamento.sequencia
                AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                AND valor_lancamento.cod_lote     = lancamento.cod_lote
                AND valor_lancamento.tipo         = lancamento.tipo
                AND valor_lancamento.tipo_valor   = ''D''
                
         INNER JOIN contabilidade.lote
                 ON lote.cod_lote       = lancamento.cod_lote
                AND lote.cod_entidade   = lancamento.cod_entidade
                AND lote.exercicio      = lancamento.exercicio
                AND lote.tipo           = lancamento.tipo
                
              WHERE receita.cod_entidade IN ('|| stCodEntidades ||')
                AND receita.exercicio    = '|| quote_literal(stExercicio) ||'

            UNION

             SELECT conta_receita.cod_estrutural    AS cod_estrutural
                  , lote.dt_lote                    AS data
                  , CASE WHEN lancamento_receita.estorno = TRUE
                       THEN valor_lancamento.vl_lancamento
                       ELSE 0.00
                    END AS valor_estorno
                  , CASE WHEN lancamento_receita.estorno = FALSE
                       THEN valor_lancamento.vl_lancamento
                       ELSE 0.00
                    END AS valor
                  , valor_lancamento.oid            AS segunda
            
               FROM orcamento.receita

         INNER JOIN orcamento.conta_receita 
                 ON conta_receita.cod_conta = receita.cod_conta
                AND conta_receita.exercicio = receita.exercicio

         INNER JOIN contabilidade.lancamento_receita
                 ON lancamento_receita.cod_receita = receita.cod_receita
                AND lancamento_receita.exercicio   = receita.exercicio
                AND lancamento_receita.estorno     = FALSE
                AND lancamento_receita.tipo        = ''A''

         INNER JOIN contabilidade.lancamento
                 ON lancamento.cod_lote      = lancamento_receita.cod_lote
                AND lancamento.sequencia     = lancamento_receita.sequencia
                AND lancamento.exercicio     = lancamento_receita.exercicio
                AND lancamento.cod_entidade  = lancamento_receita.cod_entidade
                AND lancamento.tipo          = lancamento_receita.tipo

         INNER JOIN contabilidade.valor_lancamento
                 ON valor_lancamento.exercicio    = lancamento.exercicio
                AND valor_lancamento.sequencia    = lancamento.sequencia
                AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                AND valor_lancamento.cod_lote     = lancamento.cod_lote
                AND valor_lancamento.tipo         = lancamento.tipo
                AND valor_lancamento.tipo_valor   = ''C''

         INNER JOIN contabilidade.lote
                 ON lote.cod_lote     = lancamento.cod_lote
                AND lote.cod_entidade = lancamento.cod_entidade
                AND lote.exercicio    = lancamento.exercicio
                AND lote.tipo         = lancamento.tipo
                
              WHERE receita.cod_entidade IN('|| stCodEntidades ||')
                AND receita.exercicio    = '|| quote_literal(stExercicio) ||' )';
        EXECUTE stSql;
                       
        stSql := '
            SELECT tabela.cod_estrutural
                 , tabela.receita
                 , tabela.recurso
                 , tabela.descricao
                 , coalesce(sum(tabela.valor_previsto),0.00)
                 , coalesce(sum(tabela.arrecadado_mes + tabela.anulado_mes ),0.00)
                 , coalesce(sum(tabela.arrecadado_ate_periodo + tabela.anulado_ate_periodo),0.00)
                 , coalesce(sum(tabela.anulado_mes),0.00)
                 , coalesce(sum(tabela.anulado_ate_periodo),0.00)
                 , coalesce(sum(tabela.valor_previsto),0.00) + coalesce(sum(tabela.arrecadado_ate_periodo + tabela.anulado_ate_periodo),0.00) AS valor_diferenca
              FROM ( 
                    SELECT conta_receita.cod_estrutural AS cod_estrutural
                         , receita.cod_receita          AS receita
                         , recurso.masc_recurso_red     AS recurso
                         , conta_receita.descricao      AS descricao
                         , orcamento.fn_receita_valor_previsto ( '|| quote_literal(stExercicio) ||'
                                                                , publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                , '|| quote_literal(stCodEntidades) ||'
                           ) AS valor_previsto
                         , orcamento.fn_somatorio_balancete_receita ( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     ,'|| quote_literal(dtInicial) ||'
                                                                     ,'|| quote_literal(dtFinal)   ||'
                           ) AS arrecadado_mes
                         , orcamento.fn_somatorio_balancete_receita ( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                                     ,'|| quote_literal(dtFinal)     ||'
                           ) AS arrecadado_ate_periodo
                         , tcmba.fn_somatorio_demonstrativo_consolidado_receita_estorno ( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     ,'|| quote_literal(dtInicial) ||'
                                                                     ,'|| quote_literal(dtFinal)   ||'
                           ) AS anulado_mes
                         , tcmba.fn_somatorio_demonstrativo_consolidado_receita_estorno ( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                                     ,'|| quote_literal(dtFinal)     ||'
                           ) AS anulado_ate_periodo
                           
                      FROM orcamento.conta_receita
           
                INNER JOIN orcamento.receita
                        ON conta_receita.exercicio = receita.exercicio
                       AND conta_receita.cod_conta = receita.cod_conta
                
                 LEFT JOIN orcamento.recurso ('|| quote_literal(stExercicio) ||')
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio   = receita.exercicio
                     
                     WHERE conta_receita.exercicio =  '|| quote_literal(stExercicio) ||'
                       AND receita.cod_entidade    IN ('|| stCodEntidades ||')

                   ) AS tabela
                   
               WHERE orcamento.fn_movimento_balancete_receita( '|| quote_literal(stExercicio) ||'
                                                              , publico.fn_mascarareduzida(tabela.cod_estrutural)
                                                              , '|| quote_literal(stCodEntidades) ||'
                                                              , '|| quote_literal(dtInicioAno) ||'
                                                              , '|| quote_literal(dtFinal) ||'
                                                              ) = true
            GROUP BY tabela.cod_estrutural
                   , tabela.receita
                   , tabela.recurso
                   , tabela.descricao ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;

    RETURN;
END;

$$ language 'plpgsql';
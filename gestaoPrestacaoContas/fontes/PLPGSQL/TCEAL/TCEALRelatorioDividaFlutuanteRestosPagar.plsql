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
* $Id: TCEALRelatorioDividaFlutuanteRestosPagar.plsql 64655 2016-03-18 16:50:13Z michel $
*/

/**
 * Recebe como paramentro exercicio, entidade, data inicial e final
 */

CREATE OR REPLACE FUNCTION tceal.relatorio_divida_flutuante_restos_pagar(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
  
  stExercicio         ALIAS FOR $1;
  stCodEntidade       ALIAS FOR $2;
  dtInicial           ALIAS FOR $3;
  dtFinal             ALIAS FOR $4;

  stExercicioAnterior   VARCHAR := '';
  stSql                 VARCHAR := '';
  stSqlExercicios       VARCHAR := '';
  reRecord              RECORD;
  reRegistro            RECORD;

BEGIN

  stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));

  CREATE TEMPORARY TABLE tmp_restos_tceal
                       ( cod_empenho                       INTEGER,
                         cod_entidade                      INTEGER,
                         exercicio                         VARCHAR,
                         empenhado                         NUMERIC(14,2),
                         liquidado                         NUMERIC(14,2),
                         restos_nao_processados            NUMERIC(14,2),
                         restos_processados                NUMERIC(14,2),
                         restos_processados_anulado        NUMERIC(14,2),
                         liquidado_nao_processados         NUMERIC(14,2),
                         empenhado_anulado                 NUMERIC(14,2),
                         pagamento                         NUMERIC(14,2),
                         liquidado_nao_processados_anulado NUMERIC(14,2)
                       );

  stSqlExercicios := ' SELECT COALESCE(min(exercicio), '''||stExercicio||''') AS stExercicioMin
                         FROM empenho.empenho
                        WHERE exercicio < '''||stExercicio||'''; ';

  FOR reRecord IN EXECUTE stSqlExercicios LOOP
    FOR stExercicioAnteriores IN (reRecord.stExercicioMin::INTEGER)..(stExercicioAnterior::INTEGER) LOOP
              stSql := ' SELECT retorno.exercicio
                              , retorno.cod_empenho
                              , retorno.cod_entidade
                              , sum(saldoempenhado)  AS empenhado
                              , sum(liquidado)       AS liquidado
                              , sum(aliquidar)       AS restos_nao_processados
                              , sum(liquidadoapagar) AS restos_processados
                              , sum(COALESCE(nota_liquidacao_item_anulado_processado.vl_anulado, 0.00)) AS restos_processados_anulado --INSCRIÇÃO NÃO PROCESSADOS E BAIXA PROCESSADOS
                              , sum(COALESCE(liquidado.vl_total, 0.00))                                 AS liquidado_nao_processados --BAIXA NÃO PROCESSADOS E INSCRIÇÃO PROCESSADOS
                              , sum(COALESCE(empenho_anulado.vl_anulado, 0.00))                         AS empenhado_anulado --CANCELAMENTO
                              , sum(COALESCE(liquidacao_paga.vl_total, 0.00))                           AS pagamento
                              , sum(COALESCE(liquidado.vl_anulado, 0.00))                               AS liquidado_nao_processados_anulado --INSCRIÇÃO NÃO PROCESSADOS E BAIXA PROCESSADOS
                           from empenho.fn_situacao_empenho('''||stCodEntidade||'''                                     
                                                           ,'''||stExercicioAnteriores||'''
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           ,''31/12/'||stExercicioAnterior||'''
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           ,''31/12/'||stExercicioAnterior||'''
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           ,''31/12/'||stExercicioAnterior||'''
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           ,''31/12/'||stExercicioAnterior||'''
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           ,''31/12/'||stExercicioAnterior||'''
                                                           ,''01/01/'||stExercicioAnteriores||'''
                                                           ,''31/12/'||stExercicioAnterior||'''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ,''''
                                                           ) as retorno(cod_empenho         integer,
                                                                        cod_entidade        integer,
                                                                        exercicio           char(4),
                                                                        emissao             text,
                                                                        credor              varchar,
                                                                        empenhado           numeric,
                                                                        anulado             numeric,
                                                                        saldoempenhado      numeric,
                                                                        liquidado           numeric,
                                                                        pago                numeric,
                                                                        aliquidar           numeric,
                                                                        empenhadoapagar     numeric,
                                                                        liquidadoapagar     numeric,
                                                                        cod_recurso         integer
                                                                        )

                     INNER JOIN empenho.empenho
                             ON empenho.exercicio    = retorno.exercicio
                            AND empenho.cod_empenho  = retorno.cod_empenho
                            AND empenho.cod_entidade = retorno.cod_entidade

                      LEFT JOIN (  SELECT exercicio_item
                                        , cod_pre_empenho
                                        , cod_entidade
                                        , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                     FROM empenho.nota_liquidacao_item_anulado
                                    WHERE TO_DATE(timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                      AND exercicio < '''||stExercicio||'''
                                 GROUP BY exercicio_item
                                        , cod_pre_empenho
                                        , cod_entidade
                                ) AS nota_liquidacao_item_anulado_processado                              
                             ON nota_liquidacao_item_anulado_processado.exercicio_item  = empenho.exercicio
                            AND nota_liquidacao_item_anulado_processado.cod_pre_empenho = empenho.cod_pre_empenho
                            AND nota_liquidacao_item_anulado_processado.cod_entidade    = empenho.cod_entidade

                      LEFT JOIN (  SELECT nota_liquidacao_item.exercicio_item
                                        , nota_liquidacao_item.cod_pre_empenho
                                        , nota_liquidacao_item.cod_entidade
                                        , ( SUM(COALESCE(nota_liquidacao_item.vl_total,0.00)) ) AS vl_total
                                        , ( SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) ) AS vl_anulado
                                     FROM empenho.nota_liquidacao_item
                               INNER JOIN empenho.nota_liquidacao
                                       ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                                      AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                                      AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota                               
                                LEFT JOIN (  SELECT exercicio
                                                  , cod_nota
                                                  , num_item
                                                  , exercicio_item
                                                  , cod_pre_empenho
                                                  , cod_entidade
                                                  , SUM(COALESCE(vl_anulado,0.00)) AS vl_anulado
                                               FROM empenho.nota_liquidacao_item_anulado
                                           GROUP BY exercicio
                                                  , cod_nota
                                                  , num_item
                                                  , exercicio_item
                                                  , cod_pre_empenho
                                                  , cod_entidade
                                          ) AS nota_liquidacao_item_anulado
                                       ON nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                                      AND nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                                      AND nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                                      AND nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                                      AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                      AND nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                                    WHERE nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                 GROUP BY nota_liquidacao_item.exercicio_item
                                        , nota_liquidacao_item.cod_pre_empenho
                                        , nota_liquidacao_item.cod_entidade
                                ) AS liquidado
                             ON liquidado.exercicio_item  = empenho.exercicio
                            AND liquidado.cod_pre_empenho = empenho.cod_pre_empenho
                            AND liquidado.cod_entidade    = empenho.cod_entidade

                      LEFT JOIN (  SELECT empenho_anulado_item.exercicio
                                        , empenho_anulado_item.cod_pre_empenho
                                        , empenho_anulado_item.cod_entidade
                                        , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                                     FROM empenho.empenho_anulado_item
                                    WHERE TO_DATE(empenho_anulado_item.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                 GROUP BY empenho_anulado_item.exercicio
                                        , empenho_anulado_item.cod_pre_empenho
                                        , empenho_anulado_item.cod_entidade
                                ) AS empenho_anulado
                             ON empenho_anulado.exercicio       = empenho.exercicio
                            AND empenho_anulado.cod_pre_empenho = empenho.cod_pre_empenho
                            AND empenho_anulado.cod_entidade    = empenho.cod_entidade

                      LEFT JOIN (  SELECT nota_liquidacao.exercicio_empenho
                                        , nota_liquidacao.cod_empenho
                                        , nota_liquidacao.cod_entidade
                                        , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
                                     FROM (  SELECT nota_liquidacao_paga.exercicio
                                                  , nota_liquidacao_paga.cod_entidade
                                                  , nota_liquidacao_paga.cod_nota
                                                  , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                                               FROM empenho.nota_liquidacao_paga
                                              WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                           GROUP BY nota_liquidacao_paga.exercicio
                                                  , nota_liquidacao_paga.cod_entidade
                                                  , nota_liquidacao_paga.cod_nota
                                          ) AS nota_liquidacao_paga
                                LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                                  , nota_liquidacao_paga_anulada.cod_entidade
                                                  , nota_liquidacao_paga_anulada.cod_nota
                                                  , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                               FROM empenho.nota_liquidacao_paga_anulada
                                              WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||dtInicial||''',''dd/mm/yyyy'') AND TO_DATE('''||dtFinal||''',''dd/mm/yyyy'')
                                           GROUP BY nota_liquidacao_paga_anulada.exercicio
                                                  , nota_liquidacao_paga_anulada.cod_entidade
                                                  , nota_liquidacao_paga_anulada.cod_nota
                                          ) AS nota_liquidacao_paga_anulada
                                       ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                      AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                      AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                               INNER JOIN empenho.nota_liquidacao
                                       ON nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                                      AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                                      AND nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota    
                                    WHERE nota_liquidacao.exercicio_empenho < '''||stExercicio||'''
                                 GROUP BY nota_liquidacao.exercicio_empenho
                                        , nota_liquidacao.cod_empenho
                                        , nota_liquidacao.cod_entidade
                                ) AS liquidacao_paga
                             ON liquidacao_paga.exercicio_empenho  = empenho.exercicio
                            AND liquidacao_paga.cod_empenho        = empenho.cod_empenho
                            AND liquidacao_paga.cod_entidade       = empenho.cod_entidade

                          WHERE ( aliquidar > 0 OR liquidadoapagar > 0 )

                       GROUP BY retorno.exercicio
                              , retorno.cod_empenho
                              , retorno.cod_entidade;
              ';

              FOR reRegistro IN EXECUTE stSQL LOOP
                   INSERT
                     INTO tmp_restos_tceal
                   VALUES ( reRegistro.cod_empenho
                          , reRegistro.cod_entidade
                          , reRegistro.exercicio
                          , reRegistro.empenhado
                          , reRegistro.liquidado
                          , reRegistro.restos_nao_processados
                          , reRegistro.restos_processados
                          , reRegistro.restos_processados_anulado
                          , reRegistro.liquidado_nao_processados
                          , reRegistro.empenhado_anulado
                          , reRegistro.pagamento
                          , reRegistro.liquidado_nao_processados_anulado
                          );
              END LOOP;
    END LOOP;
  END LOOP;

  stSql := '
            SELECT *
            FROM tmp_restos_tceal
  ';

  FOR reRegistro IN EXECUTE stSql
  LOOP
      RETURN next reRegistro;
  END LOOP;

  DROP TABLE tmp_restos_tceal;

END;

$$ language 'plpgsql';

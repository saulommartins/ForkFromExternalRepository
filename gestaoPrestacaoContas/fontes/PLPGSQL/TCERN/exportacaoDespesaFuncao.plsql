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
* $Revision: 25648 $
* $Name$
* $Author: gris $
* $Date: 2007-09-26 11:50:48 -0300 (Qua, 26 Set 2007) $
*
* Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

CREATE OR REPLACE FUNCTION tcern.fn_exportacao_despesa_funcao(varchar,varchar,varchar,varchar) RETURNS SETOF
RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1   ;
    inCodEntidade       ALIAS FOR $2   ;
    stDataInicial       ALIAS FOR $3   ;
    stDataFinal         ALIAS FOR $4   ;
    stSql               VARCHAR   := '';
    reRegistro          RECORD         ;
BEGIN

    stSql := '
        CREATE TEMPORARY TABLE tmp_empenho_implantado AS
                            SELECT  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
                                 ,  SUM(COALESCE(item_pre_empenho.vl_total,0)) AS valor
                              FROM  empenho.restos_pre_empenho
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = restos_pre_empenho.exercicio
                               AND  pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             WHERE  empenho.cod_entidade IN ('|| inCodEntidade ||')
                               AND  empenho.exercicio < '|| quote_literal(stExercicio) ||'
                          GROUP BY  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
    ';

    EXECUTE stSql;

    stSql := '
        CREATE TEMPORARY TABLE tmp_empenho_anulado AS
                            SELECT  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
                                 ,  SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) AS valor
                              FROM  empenho.restos_pre_empenho
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = restos_pre_empenho.exercicio
                               AND  pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN  empenho.empenho_anulado
                                ON  empenho_anulado.exercicio = empenho.exercicio
                               AND  empenho_anulado.cod_entidade = empenho.cod_entidade
                               AND  empenho_anulado.cod_empenho = empenho.cod_empenho
                         LEFT JOIN  empenho.empenho_anulado_item
                                ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                               AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                               AND  empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                               AND  empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                               AND  empenho_anulado_item.timestamp = empenho_anulado.timestamp
                             WHERE  empenho.cod_entidade IN ('|| inCodEntidade ||')
                               AND  empenho.exercicio < '|| quote_literal(stExercicio) ||'
                          GROUP BY  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
    ';

    EXECUTE stSql;
      
    stSql := '
        CREATE TEMPORARY TABLE tmp_liquidacao AS
                            SELECT  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
                                 ,  SUM(COALESCE(nota_liquidacao_item.vl_total,0)) AS valor
                              FROM  empenho.restos_pre_empenho
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = restos_pre_empenho.exercicio
                               AND  pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN  empenho.nota_liquidacao
                                ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                               AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                               AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                               AND  nota_liquidacao.dt_liquidacao <= TO_DATE('|| quote_literal(stDataFinal) ||', ''dd/mm/yyyy'')
                         LEFT JOIN  empenho.nota_liquidacao_item
                                ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                               AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                               AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                               AND  nota_liquidacao_item.exercicio_item = item_pre_empenho.exercicio
                               AND  nota_liquidacao_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND  nota_liquidacao_item.num_item = item_pre_empenho.num_item
                             WHERE  empenho.cod_entidade IN ('|| inCodEntidade ||')
                               AND  empenho.exercicio < '|| quote_literal(stExercicio) ||'
                          GROUP BY  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
    ';

    EXECUTE stSql;

    stSql := '
        CREATE TEMPORARY TABLE tmp_liquidacao_anulado AS
                            SELECT  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
                                 ,  SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)) AS valor
                              FROM  empenho.restos_pre_empenho
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = restos_pre_empenho.exercicio
                               AND  pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN  empenho.nota_liquidacao
                                ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                               AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                               AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                               AND  nota_liquidacao.dt_liquidacao <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.nota_liquidacao_item
                                ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                               AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                               AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                               AND  nota_liquidacao_item.exercicio_item = item_pre_empenho.exercicio
                               AND  nota_liquidacao_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND  nota_liquidacao_item.num_item = item_pre_empenho.num_item
                         LEFT JOIN  empenho.nota_liquidacao_item_anulado
                                ON  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                               AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                               AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                               AND  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                               AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                               AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                               AND  TO_DATE(nota_liquidacao_item_anulado.timestamp::VARCHAR,''yyyy-mm-dd'') <=  TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                             WHERE  empenho.cod_entidade IN ('|| inCodEntidade ||')
                               AND  empenho.exercicio < '|| quote_literal(stExercicio) ||'
                          GROUP BY  restos_pre_empenho.cod_funcao
                                 ,  restos_pre_empenho.cod_subfuncao
    ';

    EXECUTE stSql;

    stSql := '
                  SELECT
                          despesa.cod_funcao
                       ,  despesa.cod_subfuncao
                       ,  SUM(despesa.vl_original) as vl_original
                       ,  (SUM(despesa.vl_original) + SUM(COALESCE(suplementacao_suplementada.valor,0)) -  SUM(COALESCE(suplementacao_reducao.valor,0)) )as vl_atualizado
                       ,  SUM(empenho_bimestre.valor) AS vl_empenho_bimestre 
                       ,  SUM(empenho_ano.valor) AS vl_empenho_ano
                       ,  SUM(liquidacao_bimestre.valor) AS vl_liquidacao_bimestre 
                       ,  SUM(liquidacao_exercicio.valor) AS vl_liquidacao_exercicio                   
                       ,  ( SUM(COALESCE(restos_pagar.valor,0))
                            +
                            (SELECT SUM(tmp_empenho_implantado.valor) FROM tmp_empenho_implantado WHERE tmp_empenho_implantado.cod_funcao = despesa.cod_funcao AND tmp_empenho_implantado.cod_subfuncao = despesa.cod_subfuncao )
                            -
                            (SELECT SUM(tmp_empenho_anulado.valor) FROM tmp_empenho_anulado WHERE tmp_empenho_anulado.cod_funcao = despesa.cod_funcao AND tmp_empenho_anulado.cod_subfuncao = despesa.cod_subfuncao )
                            -
                            (SELECT SUM(tmp_liquidacao.valor) FROM tmp_liquidacao WHERE tmp_liquidacao.cod_funcao = despesa.cod_funcao AND tmp_liquidacao.cod_subfuncao = despesa.cod_subfuncao )
                            +
                            (SELECT SUM(tmp_liquidacao_anulado.valor) FROM tmp_liquidacao_anulado WHERE tmp_liquidacao_anulado.cod_funcao = despesa.cod_funcao AND tmp_liquidacao_anulado.cod_subfuncao = despesa.cod_subfuncao )

                          ) AS restos_pagar
                    FROM  orcamento.despesa
              
              INNER JOIN  orcamento.conta_despesa
                      ON  conta_despesa.exercicio = despesa.exercicio
                     AND  conta_despesa.cod_conta = despesa.cod_conta

                --suplementacoes
               LEFT JOIN (  SELECT  suplementacao_suplementada.cod_despesa
                                 ,  suplementacao_suplementada.exercicio
                                 ,  SUM(valor) AS valor
                              FROM  orcamento.suplementacao_suplementada
                        INNER JOIN  orcamento.suplementacao
                                ON  suplementacao.exercicio = suplementacao_suplementada.exercicio
                               AND  suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                               AND  suplementacao.dt_suplementacao <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                             WHERE  NOT EXISTS (  SELECT  1
                                                    FROM  orcamento.suplementacao_anulada
                                                   WHERE  suplementacao_anulada.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                                                     AND  suplementacao_anulada.exercicio = suplementacao_suplementada.exercicio
                                               )
                          GROUP BY  suplementacao_suplementada.cod_despesa
                                 ,  suplementacao_suplementada.exercicio
                         ) AS suplementacao_suplementada
                     ON  suplementacao_suplementada.exercicio = despesa.exercicio
                    AND  suplementacao_suplementada.cod_despesa = despesa.cod_despesa
                --reducoes
               LEFT JOIN (  SELECT  suplementacao_reducao.cod_despesa
                                 ,  suplementacao_reducao.exercicio
                                 ,  SUM(valor) AS valor
                              FROM  orcamento.suplementacao_reducao
                        INNER JOIN  orcamento.suplementacao
                                ON  suplementacao.exercicio = suplementacao_reducao.exercicio
                               AND  suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                               AND  suplementacao.dt_suplementacao <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                             WHERE  NOT EXISTS (  SELECT  1
                                                    FROM  orcamento.suplementacao_anulada
                                                   WHERE  suplementacao_anulada.cod_suplementacao = suplementacao_reducao.cod_suplementacao
                                                     AND  suplementacao_anulada.exercicio = suplementacao_reducao.exercicio
                                               )
                         GROUP BY  suplementacao_reducao.cod_despesa
                                 ,  suplementacao_reducao.exercicio
                         ) AS suplementacao_reducao
                     ON  suplementacao_reducao.exercicio = despesa.exercicio
                    AND  suplementacao_reducao.cod_despesa = despesa.cod_despesa
 
                --empenho_bimestre
               LEFT JOIN (  SELECT  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                                 ,  (SUM(COALESCE(item_pre_empenho.vl_total,0))-SUM(COALESCE(empenho_anulado_item.vl_anulado,0))) as valor
                              FROM  empenho.pre_empenho_despesa
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                               AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                        INNER JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               AND  empenho.dt_empenho BETWEEN TO_DATE('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.empenho_anulado
                                ON  empenho_anulado.exercicio = empenho.exercicio
                               AND  empenho_anulado.cod_entidade = empenho.cod_entidade
                               AND  empenho_anulado.cod_empenho = empenho.cod_empenho
                               AND  TO_DATE(empenho_anulado.timestamp::VARCHAR,''yyyy-mm-dd'') BETWEEN TO_DATE('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.empenho_anulado_item
                                ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                               AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                               AND  empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                               AND  empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                               AND  empenho_anulado_item.timestamp = empenho_anulado.timestamp
                          GROUP BY  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                         ) AS empenho_bimestre
                     ON  empenho_bimestre.exercicio = despesa.exercicio
                    AND  empenho_bimestre.cod_despesa = despesa.cod_despesa
                    AND  empenho_bimestre.cod_entidade = despesa.cod_entidade
            
                --empenho_ano
               LEFT JOIN (  SELECT  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                                 ,  (SUM(COALESCE(item_pre_empenho.vl_total,0))-SUM(COALESCE(empenho_anulado_item.vl_anulado,0))) as valor
                              FROM  empenho.pre_empenho_despesa
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                               AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                        INNER JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               AND  empenho.dt_empenho <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.empenho_anulado
                                ON  empenho_anulado.exercicio = empenho.exercicio
                               AND  empenho_anulado.cod_entidade = empenho.cod_entidade
                               AND  empenho_anulado.cod_empenho = empenho.cod_empenho
                               AND  TO_DATE(empenho_anulado.timestamp::VARCHAR,''yyyy-mm-dd'') <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.empenho_anulado_item
                                ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                               AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                               AND  empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                               AND  empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                               AND  empenho_anulado_item.timestamp = empenho_anulado.timestamp
                          GROUP BY  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                         ) AS empenho_ano
                     ON  empenho_ano.exercicio = despesa.exercicio
                    AND  empenho_ano.cod_despesa = despesa.cod_despesa
                    AND  empenho_ano.cod_entidade = despesa.cod_entidade

                --liquidacao_bimestre
               LEFT JOIN (  SELECT  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                                 ,  (SUM(COALESCE(nota_liquidacao_item.vl_total,0)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0))) as valor
                              FROM  empenho.pre_empenho_despesa
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                               AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               AND  empenho.dt_empenho BETWEEN TO_DATE('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                        INNER JOIN  empenho.nota_liquidacao
                                ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                               AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                               AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                               AND  nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                        INNER JOIN  empenho.nota_liquidacao_item
                                ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                               AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                               AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                         LEFT JOIN  empenho.nota_liquidacao_item_anulado
                                ON  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                               AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                               AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                               AND  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                               AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                               AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                               AND  TO_DATE(nota_liquidacao_item_anulado.timestamp::VARCHAR,''yyyy-mm-dd'') BETWEEN  TO_DATE('|| quote_literal(stDataInicial) ||',''dd/mm/yyyy'') AND TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')

                          GROUP BY  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                         ) AS liquidacao_bimestre
                     ON  liquidacao_bimestre.exercicio = despesa.exercicio
                    AND  liquidacao_bimestre.cod_despesa = despesa.cod_despesa
                    AND  liquidacao_bimestre.cod_entidade = despesa.cod_entidade


                --liquidacao_exercicio
               LEFT JOIN (  SELECT  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                                 ,  (SUM(COALESCE(nota_liquidacao_item.vl_total,0)) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0))) as valor
                              FROM  empenho.pre_empenho_despesa
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                               AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               AND  empenho.dt_empenho <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                        INNER JOIN  empenho.nota_liquidacao
                                ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                               AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                               AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                               AND  nota_liquidacao.dt_liquidacao <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                        INNER JOIN  empenho.nota_liquidacao_item
                                ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                               AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                               AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                         LEFT JOIN  empenho.nota_liquidacao_item_anulado
                                ON  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                               AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                               AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                               AND  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                               AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                               AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                               AND  TO_DATE(nota_liquidacao_item_anulado.timestamp::VARCHAR,''yyyy-mm-dd'') <=  TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')

                          GROUP BY  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                         ) AS liquidacao_exercicio
                     ON  liquidacao_exercicio.exercicio = despesa.exercicio
                    AND  liquidacao_exercicio.cod_despesa = despesa.cod_despesa
                    AND  liquidacao_exercicio.cod_entidade = despesa.cod_entidade


                --restos a pagar
               LEFT JOIN (  SELECT  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                                 ,  (SUM(COALESCE(item_pre_empenho.vl_total,0))-SUM(COALESCE(empenho_anulado_item.vl_anulado,0))-SUM(COALESCE(nota_liquidacao_item.vl_total,0))+SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0)) ) as valor
                              FROM  empenho.pre_empenho_despesa
                        INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                               AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                        INNER JOIN  empenho.item_pre_empenho
                                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        INNER JOIN  empenho.empenho
                                ON  empenho.exercicio = pre_empenho.exercicio
                               AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                               AND  empenho.dt_empenho <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.empenho_anulado
                                ON  empenho_anulado.exercicio = empenho.exercicio
                               AND  empenho_anulado.cod_entidade = empenho.cod_entidade
                               AND  empenho_anulado.cod_empenho = empenho.cod_empenho
                               AND  TO_DATE(empenho_anulado.timestamp::VARCHAR,''yyyy-mm-dd'') <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.empenho_anulado_item
                                ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                               AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                               AND  empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                               AND  empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                               AND  empenho_anulado_item.timestamp = empenho_anulado.timestamp
                         LEFT JOIN  empenho.nota_liquidacao
                                ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                               AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                               AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                               AND  nota_liquidacao.dt_liquidacao <= TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')
                         LEFT JOIN  empenho.nota_liquidacao_item
                                ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                               AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                               AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                         LEFT JOIN  empenho.nota_liquidacao_item_anulado
                                ON  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                               AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                               AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                               AND  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                               AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                               AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                               AND  TO_DATE(nota_liquidacao_item_anulado.timestamp::VARCHAR,''yyyy-mm-dd'') <=  TO_DATE('|| quote_literal(stDataFinal) ||',''dd/mm/yyyy'')

                          GROUP BY  pre_empenho_despesa.exercicio
                                 ,  pre_empenho_despesa.cod_despesa
                                 ,  empenho.cod_entidade
                         ) AS restos_pagar
                     ON  restos_pagar.exercicio < despesa.exercicio
                    AND  restos_pagar.cod_despesa = despesa.cod_despesa
                    AND  restos_pagar.cod_entidade = despesa.cod_entidade

                  WHERE despesa.exercicio = '|| quote_literal(stExercicio) ||'
                    AND despesa.cod_entidade IN ('|| inCodEntidade ||')
               GROUP BY despesa.cod_funcao
                      , despesa.cod_subfuncao
             ORDER BY despesa.cod_funcao
                    , despesa.cod_subfuncao
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_empenho_implantado;
    DROP TABLE tmp_empenho_anulado;
    DROP TABLE tmp_liquidacao;
    DROP TABLE tmp_liquidacao_anulado;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';


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
    * Script de função PLPGSQL - Relatório Despesas Municipais com Educação e Cultura.
    * Data de Criação: 23/06/2008


    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-02.01.40

    $Id: $

*/

CREATE OR REPLACE FUNCTION orcamento.fn_educacao_despesas( VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    stDataInicial  	ALIAS FOR $2;
    stDataFinal  	ALIAS FOR $3;
    stCodEntidades 	ALIAS FOR $4;
    stCodOrgao   	ALIAS FOR $5;
    
    stCodRecursoMDE             VARCHAR[];
    stCodRecursoFUNDEB          VARCHAR[];
    stCodRecursoSalarioEducacao VARCHAR[];
    
    stCodRecurso         VARCHAR := '';
    stExercicioPosterior VARCHAR := '';
    stSql 			     VARCHAR := '';
    stFiltro		     VARCHAR := '';
    reRegistro 		     RECORD ;

BEGIN

    stExercicioPosterior := trim(to_char((to_number(stExercicio,'9999')+1),'9999'));

    -----------------------------------------
    -- cria a tabela temporaria de retorno --
    -----------------------------------------
    CREATE TEMPORARY TABLE tmp_retorno(
        cod_subfuncao         INTEGER,
        grupo                 VARCHAR,
        nivel                 INTEGER,
        nom_subfuncao         VARCHAR,
        cod_estrutural        VARCHAR,
        nom_estrutural        VARCHAR,
        vl_dotacao_atualizada NUMERIC(14,2),
        vl_empenhado          NUMERIC(14,2),
        vl_liquidado          NUMERIC(14,2),
        vl_pago               NUMERIC(14,2),
        vl_despesa_orcada     NUMERIC(14,2)
    );

    EXECUTE 'SELECT (array(SELECT cod_recurso FROM stn.vinculo_recurso WHERE exercicio = '''||stExercicio||''' AND cod_entidade IN ('||stCodEntidades||') AND num_orgao = '||stCodOrgao||' AND cod_vinculo = 2))' INTO stCodRecursoMDE;
    EXECUTE 'SELECT (array(SELECT cod_recurso FROM stn.vinculo_recurso WHERE exercicio = '''||stExercicio||''' AND cod_entidade IN ('||stCodEntidades||') AND num_orgao = '||stCodOrgao||' AND cod_vinculo = 1))' INTO stCodRecursoFUNDEB;
    EXECUTE 'SELECT (array(SELECT cod_recurso FROM stn.vinculo_recurso WHERE exercicio = '''||stExercicio||''' AND cod_entidade IN ('||stCodEntidades||') AND num_orgao = '||stCodOrgao||' AND cod_vinculo = 3))' INTO stCodRecursoSalarioEducacao;
    
    ------------------------------------------------------
    -- recupera os valores das contas mae  do exercicio --
    ------------------------------------------------------
    stSql := '
        INSERT INTO tmp_retorno 
            SELECT d.cod_subfuncao
                 , CASE WHEN d.cod_subfuncao NOT IN (362, 363, 364) THEN
                       CASE WHEN d.cod_recurso IN ('||array_to_string(stCodRecursoMDE, ',')||')             THEN ''1|Despesas Próprias Custeadas com Impostos e Transferencias - Exceto Fundeb''
                            WHEN d.cod_recurso IN ('||array_to_string(stCodRecursoFUNDEB, ',')||')          THEN ''2|FUNDEB''
                            WHEN d.cod_recurso IN ('||array_to_string(stCodRecursoSalarioEducacao, ',')||') THEN ''3|Salário Educação''
                            ELSE ''4|Outros Recursos Vinculados a Educação'' END
                   ELSE
                        ''4|Outros Recursos Vinculados a Educação''
                   END AS grupo
                   
                 , 0 AS nivel
                 , sf.descricao AS nom_subfuncao
                 , conta_despesa.cod_estrutural 
                 , conta_despesa.descricao AS nom_estrutural
                 , (sum(coalesce(d.vl_original,0.00)) + (sum(coalesce(suplementado.vl_suplementado,0.00)) - sum(coalesce(reduzido.vl_reduzido,0.00)))) AS vl_suplementacoes
                 , CAST(0.00 AS NUMERIC) AS vl_empenhado
                 , CAST(0.00 AS NUMERIC) AS vl_liquidado
                 , CAST(0.00 AS NUMERIC) AS vl_pago
                 , CAST(0.00 AS NUMERIC) AS vl_despesa_orcada
              FROM
                   orcamento.despesa  as d
                   LEFT JOIN ( SELECT ss.exercicio
                                    , ss.cod_despesa
                                    , sum(ss.valor)           as vl_suplementado
                                 FROM orcamento.suplementacao                   as s
                                    , orcamento.suplementacao_suplementada      as ss
                                WHERE s.exercicio         = ss.exercicio
                                  AND s.cod_suplementacao = ss.cod_suplementacao
                                  AND to_date(s.dt_suplementacao::varchar,''yyyy-mm-dd'') BETWEEN to_date( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'')   
                                                             AND to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'')  
                             GROUP BY ss.exercicio
                                    , ss.cod_despesa
                             ORDER BY ss.cod_despesa ) as suplementado
                          ON (     suplementado.exercicio   = d.exercicio
                               AND suplementado.cod_despesa = d.cod_despesa )
                   LEFT JOIN ( SELECT sr.exercicio
                                    , sr.cod_despesa
                                    , sum(sr.valor)           as vl_reduzido
                                 FROM orcamento.suplementacao              as s
                                    , orcamento.suplementacao_reducao      as sr
                                WHERE s.exercicio         = sr.exercicio
                                  AND s.cod_suplementacao = sr.cod_suplementacao
                                  AND s.exercicio         = ''' || stExercicio || '''
                                  AND to_date(s.dt_suplementacao::varchar,''yyyy-mm-dd'') BETWEEN to_date( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'')      
                                                             AND to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'')   
                             GROUP BY sr.exercicio
                                    , sr.cod_despesa
                             ORDER BY sr.cod_despesa ) as reduzido
                          ON (     reduzido.exercicio   = d.exercicio
                               AND reduzido.cod_despesa = d.cod_despesa )
                  INNER JOIN orcamento.conta_despesa
                          ON d.cod_conta = conta_despesa.cod_conta
                         AND d.exercicio = conta_despesa.exercicio
                  INNER JOIN orcamento.funcao as f
                          ON f.exercicio  = d.exercicio
                         AND f.cod_funcao = d.cod_funcao
                  INNER JOIN orcamento.subfuncao as sf
                          ON sf.exercicio = d.exercicio
                         AND sf.cod_subfuncao = d.cod_subfuncao
             WHERE d.exercicio   =  ''' || stExercicio || '''
               AND d.cod_entidade IN (' || stCodEntidades || ')
               AND d.num_orgao = ' || stCodOrgao || ' ';

        IF stCodRecurso <> '' THEN
            stSql := stSql || ' AND d.cod_recurso IN (' || stCodRecurso || ') ';
        END IF;

        stSql := stSql || '
               AND d.cod_funcao = 12
          GROUP BY d.cod_funcao
                 , d.cod_subfuncao
                 , d.exercicio
                 , d.cod_recurso
                 , d.num_orgao
                 , d.num_unidade
                 , d.cod_entidade
                 , f.descricao
                 , sf.descricao
                 , conta_despesa.cod_estrutural
                 , conta_despesa.descricao '; 

    EXECUTE(stSql);
    
    ------------------------------------
    -- recupera os valores empenhados --
    ------------------------------------
    stSql := '
        INSERT INTO tmp_retorno
                SELECT cod_subfuncao
                     , grupo
                     , 1
                     , ''''
                     , cod_estrutural
                     , nom_estrutural
                     , CAST(0 AS NUMERIC)
                     , SUM(COALESCE(vl_total, 0.00)) as vl_total
                     , CAST(0 AS NUMERIC)
                     , CAST(0 AS NUMERIC)
                     , CAST(0 AS NUMERIC)
                FROM (
                            SELECT ode.cod_subfuncao
                                  
                                , CASE WHEN ode.cod_subfuncao NOT IN (362, 363, 364) THEN
                                      CASE WHEN ode.cod_recurso IN ('||array_to_string(stCodRecursoMDE, ',')||')             THEN ''1|Despesas Próprias Custeadas com Impostos e Transferencias - Exceto Fundeb''
                                           WHEN ode.cod_recurso IN ('||array_to_string(stCodRecursoFUNDEB, ',')||')          THEN ''2|FUNDEB''
                                           WHEN ode.cod_recurso IN ('||array_to_string(stCodRecursoSalarioEducacao, ',')||') THEN ''3|Salário Educação''
                                           ELSE ''4|Outros Recursos Vinculados a Educação'' END
                                  ELSE
                                       ''4|Outros Recursos Vinculados a Educação''
                                  END AS grupo
                                  
                                 , 1
                                 , ''''
                                 , ocd.cod_estrutural
                                 , ocd.descricao AS nom_estrutural
                                 , CAST(0 AS NUMERIC)
                                 , COALESCE(SUM(vl_total), 0.00) AS vl_total
                                 , CAST(0 AS NUMERIC)
                                 , CAST(0 AS NUMERIC)
                                 , CAST(0 AS NUMERIC)
                              FROM orcamento.conta_despesa ocd 
                        INNER JOIN empenho.pre_empenho_despesa ped 
                                ON ped.exercicio = ocd.exercicio
                               AND ped.cod_conta = ocd.cod_conta
                        INNER JOIN orcamento.despesa ode 
                                ON ode.exercicio = ped.exercicio 
                               AND ode.cod_despesa = ped.cod_despesa
                        INNER JOIN empenho.pre_empenho pe
                                ON ped.exercicio = pe.exercicio 
                               AND ped.cod_pre_empenho = pe.cod_pre_empenho 
                        INNER JOIN empenho.item_pre_empenho ipe 
                                ON ipe.cod_pre_empenho = pe.cod_pre_empenho 
                               AND ipe.exercicio = pe.exercicio 
                        INNER JOIN empenho.empenho e 
                                ON e.exercicio = pe.exercicio 
                               AND e.cod_pre_empenho = pe.cod_pre_empenho 
                            WHERE e.exercicio = ''' || stExercicio || ''' 
                              AND e.cod_entidade IN (' || stCodEntidades || ') 
                              AND e.dt_empenho BETWEEN to_date(''' || stDataInicial || '''::varchar,''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || '''::varchar,''dd/mm/yyyy'')
                              AND ode.num_orgao = ' || stCodOrgao || ' ';

                            IF stCodRecurso <> '' THEN
                                stSql := stSql || ' AND ode.cod_recurso IN (' || stCodRecurso || ')';
                            END IF;
                    
                            stSql := stSql || '

                              AND ode.cod_funcao = 12
                         GROUP BY ode.cod_subfuncao
                                , ode.cod_recurso
                                , ocd.cod_estrutural
                                , ocd.descricao
                                , ode.exercicio
                                , ode.num_orgao
                                , ode.num_unidade
                                , ode.cod_entidade
                
                UNION
                        SELECT ode.cod_subfuncao
                                  
                                , CASE WHEN ode.cod_subfuncao NOT IN (362, 363, 364) THEN
                                      CASE WHEN ode.cod_recurso IN ('||array_to_string(stCodRecursoMDE, ',')||')             THEN ''1|Despesas Próprias Custeadas com Impostos e Transferencias - Exceto Fundeb''
                                           WHEN ode.cod_recurso IN ('||array_to_string(stCodRecursoFUNDEB, ',')||')          THEN ''2|FUNDEB''
                                           WHEN ode.cod_recurso IN ('||array_to_string(stCodRecursoSalarioEducacao, ',')||') THEN ''3|Salário Educação''
                                           ELSE ''4|Outros Recursos Vinculados a Educação'' END
                                  ELSE
                                       ''4|Outros Recursos Vinculados a Educação''
                                  END AS grupo
                                  
                                 , 1
                                 , ''''
                                 , ocd.cod_estrutural
                                 , ocd.descricao AS nom_estrutural
                                 , CAST(0 AS NUMERIC)
                                 , SUM(COALESCE(eai.vl_anulado, 0)) * -1 AS vl_total
                                 , CAST(0 AS NUMERIC)
                                 , CAST(0 AS NUMERIC)
                                 , CAST(0 AS NUMERIC)
                              FROM orcamento.conta_despesa ocd
                        INNER JOIN empenho.pre_empenho_despesa ped
                                ON ped.exercicio = ocd.exercicio
                               AND ped.cod_conta = ocd.cod_conta
                        INNER JOIN orcamento.despesa ode
                                ON ode.exercicio = ped.exercicio
                               AND ode.cod_despesa = ped.cod_despesa
                        INNER JOIN empenho.pre_empenho pe
                                ON ped.exercicio = pe.exercicio
                               AND ped.cod_pre_empenho = pe.cod_pre_empenho
                        INNER JOIN empenho.item_pre_empenho ipe
                                ON ipe.cod_pre_empenho = pe.cod_pre_empenho
                               AND ipe.exercicio = pe.exercicio
                        INNER JOIN empenho.empenho e
                                ON e.exercicio = pe.exercicio
                               AND e.cod_pre_empenho = pe.cod_pre_empenho
                        INNER JOIN empenho.empenho_anulado ea
                                ON e.cod_empenho = ea.cod_empenho
                               AND e.cod_entidade = ea.cod_entidade
                               AND e.exercicio = ea.exercicio
                        INNER JOIN empenho.empenho_anulado_item eai
                                ON ea.cod_empenho = eai.cod_empenho
                               AND ea.cod_entidade = eai.cod_entidade
                               AND ea.exercicio = eai.exercicio
                               AND ea.timestamp = eai.timestamp
                               AND ipe.num_item = eai.num_item
                            WHERE e.exercicio = ''' || stExercicio || '''
                              AND e.cod_entidade IN (' || stCodEntidades || ')
                              AND e.dt_empenho BETWEEN to_date(''' || stDataInicial || '''::varchar,''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || '''::varchar,''dd/mm/yyyy'')
                              AND ode.num_orgao = ' || stCodOrgao || ' ';

                            IF stCodRecurso <> '' THEN
                                stSql := stSql || ' AND ode.cod_recurso IN (' || stCodRecurso || ')';
                            END IF;
                    
                            stSql := stSql || '

                              AND ode.cod_funcao = 12
                            GROUP BY ode.cod_subfuncao
                                , ode.cod_recurso
                                , ocd.cod_estrutural
                                , ocd.descricao
                                , ode.exercicio
                                , ode.num_orgao
                                , ode.num_unidade
                                , ode.cod_entidade
                ) as tabela
                GROUP BY cod_subfuncao
                       , grupo
                       , cod_estrutural
                       , nom_estrutural

         ORDER BY cod_estrutural';

    EXECUTE(stSql);

    ---------------------------------------
    -- recupera os valores da liquidacao --
    ---------------------------------------
    stSql := '
    INSERT INTO tmp_retorno 
            SELECT despesa.cod_subfuncao
                   
                , CASE WHEN despesa.cod_subfuncao NOT IN (362, 363, 364) THEN
                      CASE WHEN despesa.cod_recurso IN ('||array_to_string(stCodRecursoMDE, ',')||')             THEN ''1|Despesas Próprias Custeadas com Impostos e Transferencias - Exceto Fundeb''
                           WHEN despesa.cod_recurso IN ('||array_to_string(stCodRecursoFUNDEB, ',')||')          THEN ''2|FUNDEB''
                           WHEN despesa.cod_recurso IN ('||array_to_string(stCodRecursoSalarioEducacao, ',')||') THEN ''3|Salário Educação''
                           ELSE ''4|Outros Recursos Vinculados a Educação'' END
                  ELSE
                       ''4|Outros Recursos Vinculados a Educação''
                  END AS grupo
                   
                 , 1
                 , ''''
                 , conta_despesa.cod_estrutural
                 , conta_despesa.descricao AS nom_estrutural
                 , CAST(0 AS NUMERIC)
                 , CAST(0 AS NUMERIC)
                 , COALESCE(SUM(vl_total), 0.00) - SUM(COALESCE(vl_anulado,0)) AS vl_total
                 , CAST(0 AS NUMERIC)
                 , CAST(0 AS NUMERIC)
              FROM empenho.pre_empenho  
        INNER JOIN empenho.pre_empenho_despesa
                ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
               AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
        INNER JOIN orcamento.conta_despesa
                ON pre_empenho_despesa.exercicio = conta_despesa.exercicio
               AND pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
        INNER JOIN orcamento.despesa
                ON pre_empenho_despesa.exercicio   = despesa.exercicio
               AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
        INNER JOIN empenho.empenho e 
                ON e.exercicio = pre_empenho.exercicio 
               AND e.cod_pre_empenho = pre_empenho.cod_pre_empenho 
        INNER JOIN empenho.nota_liquidacao nl 
                ON nl.exercicio_empenho = e.exercicio 
               AND nl.cod_entidade = e.cod_entidade 
               AND nl.cod_empenho = e.cod_empenho 
        INNER JOIN empenho.nota_liquidacao_item nli 
                ON nli.exercicio = nl.exercicio 
               AND nli.cod_entidade = nl.cod_entidade 
               AND nli.cod_nota = nl.cod_nota 
         LEFT JOIN (SELECT exercicio
                         , cod_nota
                         , exercicio_item
                         , cod_pre_empenho
                         , cod_entidade
                         , num_item
                         , SUM(COALESCE(vl_anulado,0)) AS vl_anulado
                      FROM empenho.nota_liquidacao_item_anulado
                     WHERE cod_entidade IN (' || stCodEntidades || ')
                       AND exercicio = ''' || stExercicio || '''
                       AND TO_DATE(timestamp::varchar,''yyyy-mm-dd'') BETWEEN TO_DATE(''' || stDataInicial || '''::varchar,''dd/mm/yyyy'') AND TO_DATE(''' || stDataFinal || '''::varchar,''dd/mm/yyyy'') 
                  GROUP BY exercicio
                         , cod_nota
                         , exercicio_item
                         , cod_pre_empenho
                         , cod_entidade
                         , num_item
                   ) AS nota_liquidacao_item_anulado
                ON nota_liquidacao_item_anulado.exercicio = nli.exercicio
               AND nota_liquidacao_item_anulado.cod_nota = nli.cod_nota
               AND nota_liquidacao_item_anulado.exercicio_item = nli.exercicio_item
               AND nota_liquidacao_item_anulado.cod_pre_empenho = nli.cod_pre_empenho
               AND nota_liquidacao_item_anulado.cod_entidade = nli.cod_entidade
               AND nota_liquidacao_item_anulado.num_item = nli.num_item
             WHERE e.exercicio = ''' || stExercicio || ''' 
               AND e.cod_entidade IN (' || stCodEntidades || ') 
               AND nl.dt_liquidacao BETWEEN to_date(''' || stDataInicial || '''::varchar,''dd/mm/yyyy'') AND to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'')
               AND despesa.num_orgao = ' || stCodOrgao || ' ';

             IF stCodRecurso <> '' THEN
                 stSql := stSql || ' AND despesa.cod_recurso IN (' || stCodRecurso || ') ';
             END IF;
             
             stSql := stSql || '

               AND despesa.cod_funcao = 12
          GROUP BY despesa.cod_subfuncao
                 , despesa.cod_recurso
                 , despesa.exercicio
                 , despesa.num_orgao
                 , despesa.num_unidade
                 , despesa.cod_entidade
                 , conta_despesa.cod_estrutural
                 , conta_despesa.descricao';

    EXECUTE(stSql);

    -------------------------------
    -- recupera os valores pagos --
    -------------------------------
    stSql := '
        INSERT INTO tmp_retorno
            SELECT despesa.cod_subfuncao
            
                , CASE WHEN despesa.cod_subfuncao NOT IN (362, 363, 364) THEN
                      CASE WHEN despesa.cod_recurso IN ('||array_to_string(stCodRecursoMDE, ',')||')             THEN ''1|Despesas Próprias Custeadas com Impostos e Transferencias - Exceto Fundeb''
                           WHEN despesa.cod_recurso IN ('||array_to_string(stCodRecursoFUNDEB, ',')||')          THEN ''2|FUNDEB''
                           WHEN despesa.cod_recurso IN ('||array_to_string(stCodRecursoSalarioEducacao, ',')||') THEN ''3|Salário Educação''
                           ELSE ''4|Outros Recursos Vinculados a Educação'' END
                  ELSE
                       ''4|Outros Recursos Vinculados a Educação''
                  END AS grupo
                  
                 , 1
                 , ''''
                 , conta_despesa.cod_estrutural
                 , conta_despesa.descricao AS nom_estrutural
                 , CAST(0 AS NUMERIC)
                 , CAST(0 AS NUMERIC)
                 , CAST(0 AS NUMERIC)
                 , COALESCE(SUM(vl_pago), 0.00) - SUM(COALESCE(vl_anulado,0)) AS vl_total
                 , CAST(0 AS NUMERIC)
              FROM empenho.pre_empenho  
        INNER JOIN empenho.pre_empenho_despesa
                ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
               AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
        INNER JOIN orcamento.conta_despesa
                ON pre_empenho_despesa.exercicio = conta_despesa.exercicio
               AND pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
        INNER JOIN orcamento.despesa
                ON pre_empenho_despesa.exercicio   = despesa.exercicio
               AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
        INNER JOIN empenho.empenho e 
                ON e.exercicio = pre_empenho.exercicio 
               AND e.cod_pre_empenho = pre_empenho.cod_pre_empenho 
        INNER JOIN empenho.nota_liquidacao nl 
                ON nl.exercicio_empenho = e.exercicio 
               AND nl.cod_entidade = e.cod_entidade 
               AND nl.cod_empenho = e.cod_empenho 
        INNER JOIN empenho.nota_liquidacao_paga nlp 
                ON nl.exercicio = nlp.exercicio 
               AND nl.cod_nota = nlp.cod_nota 
               AND nl.cod_entidade = nlp.cod_entidade 
         LEFT JOIN ( SELECT exercicio
                          , cod_nota
                          , cod_entidade
                          , timestamp
                          , SUM(COALESCE(vl_anulado,0)) AS vl_anulado
                       FROM empenho.nota_liquidacao_paga_anulada 
                      WHERE exercicio = ''' || stExercicio || '''
                        AND cod_entidade IN (' || stCodEntidades || ')
                        AND TO_DATE(timestamp_anulada::varchar,''yyyy-mm-dd'') BETWEEN to_date(''' || stDataInicial || '''::varchar,''dd/mm/yyyy'') AND to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'') 
                   GROUP BY exercicio
                          , cod_nota
                          , cod_entidade
                          , timestamp
                   ) AS nota_liquidacao_paga_anulada
                ON nlp.exercicio    = nota_liquidacao_paga_anulada.exercicio
               AND nlp.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade
               AND nlp.cod_nota     = nota_liquidacao_paga_anulada.cod_nota
               AND nlp.timestamp    = nota_liquidacao_paga_anulada.timestamp  
             WHERE e.exercicio = ''' || stExercicio || ''' 
               AND e.cod_entidade IN (' || stCodEntidades || ') 
               AND TO_DATE(nlp.timestamp::varchar,''yyyy-mm-dd'') BETWEEN to_date(''' || stDataInicial || '''::varchar,''dd/mm/yyyy'') AND to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'') 
               AND despesa.num_orgao = ' || stCodOrgao || ' ';

             IF stCodRecurso <> '' THEN
                 stSql := stSql || ' AND despesa.cod_recurso IN (' || stCodRecurso || ') ';
             END IF;
             
             stSql := stSql || '

               AND despesa.cod_funcao = 12
          GROUP BY despesa.cod_subfuncao
                 , despesa.cod_recurso
                 , despesa.exercicio
                 , despesa.num_orgao
                 , despesa.num_unidade
                 , despesa.cod_entidade
                 , conta_despesa.cod_estrutural
                 , conta_despesa.descricao';

    EXECUTE(stSql);

    -------------------------------------------------------------
    -- recupera os valores das contas mae do proximo exercicio --
    -------------------------------------------------------------
    stSql := '
        INSERT INTO tmp_retorno 
            SELECT d.cod_subfuncao
            
                , CASE WHEN d.cod_subfuncao NOT IN (362, 363, 364) THEN
                      CASE WHEN d.cod_recurso IN ('||array_to_string(stCodRecursoMDE, ',')||')             THEN ''1|Despesas Próprias Custeadas com Impostos e Transferencias - Exceto Fundeb''
                           WHEN d.cod_recurso IN ('||array_to_string(stCodRecursoFUNDEB, ',')||')          THEN ''2|FUNDEB''
                           WHEN d.cod_recurso IN ('||array_to_string(stCodRecursoSalarioEducacao, ',')||') THEN ''3|Salário Educação''
                           ELSE ''4|Outros Recursos Vinculados a Educação'' END
                  ELSE
                       ''4|Outros Recursos Vinculados a Educação''
                  END AS grupo
                   
                 , 0 AS nivel
                 , sf.descricao            AS nom_subfuncao
                 , conta_despesa.cod_estrutural 
                 , conta_despesa.descricao AS nom_estrutural
                 , CAST(0.00 AS NUMERIC) AS vl_despesa_orcada
                 , CAST(0.00 AS NUMERIC) AS vl_empenhado
                 , CAST(0.00 AS NUMERIC) AS vl_liquidado
                 , CAST(0.00 AS NUMERIC) AS vl_pago
                 , (sum(coalesce(d.vl_original,0.00)) + (sum(coalesce(suplementado.vl_suplementado,0.00)) - sum(coalesce(reduzido.vl_reduzido,0.00)))) AS vl_despesa_orcada
              FROM
                   orcamento.despesa as d
                   LEFT JOIN ( SELECT ss.exercicio
                                    , ss.cod_despesa
                                    , sum(ss.valor)           as vl_suplementado
                                 FROM orcamento.suplementacao                   as s
                                    , orcamento.suplementacao_suplementada      as ss
                                WHERE s.exercicio         = ss.exercicio
                                  AND s.cod_suplementacao = ss.cod_suplementacao
                                  AND to_date(s.dt_suplementacao::varchar,''yyyy-mm-dd'') BETWEEN to_date( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'')   
                                                             AND to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'')  
                             GROUP BY ss.exercicio
                                    , ss.cod_despesa
                             ORDER BY ss.cod_despesa ) as suplementado
                          ON (     suplementado.exercicio   = d.exercicio
                               AND suplementado.cod_despesa = d.cod_despesa )
                   LEFT JOIN ( SELECT sr.exercicio
                                    , sr.cod_despesa
                                    , sum(sr.valor)           as vl_reduzido
                                 FROM orcamento.suplementacao              as s
                                    , orcamento.suplementacao_reducao      as sr
                                WHERE s.exercicio         = sr.exercicio
                                  AND s.cod_suplementacao = sr.cod_suplementacao
                                  AND s.exercicio         = ''' || stExercicioPosterior || '''
                                  AND to_date(s.dt_suplementacao::varchar,''yyyy-mm-dd'') BETWEEN to_date( ''' || stDataInicial || '''::varchar, ''dd/mm/yyyy'')      
                                                             AND to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'')   
                             GROUP BY sr.exercicio
                                    , sr.cod_despesa
                             ORDER BY sr.cod_despesa ) as reduzido
                          ON (     reduzido.exercicio   = d.exercicio
                               AND reduzido.cod_despesa = d.cod_despesa )
                  INNER JOIN orcamento.conta_despesa
                          ON d.cod_conta = conta_despesa.cod_conta
                         AND d.exercicio = conta_despesa.exercicio
                  INNER JOIN orcamento.funcao as f
                          ON f.exercicio  = d.exercicio
                         AND f.cod_funcao = d.cod_funcao
                  INNER JOIN orcamento.subfuncao as sf
                          ON sf.exercicio = d.exercicio
                         AND sf.cod_subfuncao = d.cod_subfuncao
             WHERE d.exercicio   =  ''' || stExercicioPosterior || '''
               AND d.cod_entidade IN (' || stCodEntidades || ')
               AND d.num_orgao = ' || stCodOrgao || ' ';

             IF stCodRecurso <> '' THEN
                 stSql := stSql || ' AND d.cod_recurso IN (' || stCodRecurso || ') ';
             END IF;
             
             stSql := stSql || '

               AND d.cod_funcao = 12
          GROUP BY d.cod_funcao
                 , d.cod_subfuncao
                 , d.exercicio
                 , d.cod_recurso         
                 , d.num_orgao
                 , d.num_unidade
                 , d.cod_entidade
                 , f.descricao
                 , sf.descricao
                 , conta_despesa.cod_estrutural
                 , conta_despesa.descricao '; 

    EXECUTE(stSql);
    
    -----------------
    -- SQL de retorno
    -----------------
    stSql := '
            SELECT cod_subfuncao
                 , grupo               
                 , nivel               
                 , nom_subfuncao      
                 , cod_estrutural    
                 , nom_estrutural   
                 , SUM(vl_dotacao_atualizada)
                 , SUM(vl_empenhado)
                 , SUM(vl_liquidado)      
                 , SUM(vl_pago)           
                 , SUM(vl_despesa_orcada)
              FROM tmp_retorno
          GROUP BY cod_subfuncao
                 , grupo  
                 , nivel        
                 , nom_subfuncao        
                 , cod_estrutural        
                 , nom_estrutural 
          ORDER BY cod_estrutural
                 , nivel
             ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno;
 
    RETURN;
END; 
$$ language plpgsql;

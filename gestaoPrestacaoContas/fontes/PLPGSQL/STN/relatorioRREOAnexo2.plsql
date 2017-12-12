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
    * PL do RREOAnexo2 - Arquivo STN da GPC 
    * Data de Criação   : 01/06/2008


    * @author Analista      Alexandre Melo
    * @author Desenvolvedor Alexandre Melo
    
    * @package URBEM
    * @subpackage 

    $Id: relatorioRREOAnexo2.plsql 66616 2016-10-05 17:32:18Z franver $
*/

/*
    ESTA PL GERA OS VALORES DO CORPO PRINCIPAL DO RELATORIO
    Prog.: Alexandre Melo
*/
CREATE OR REPLACE FUNCTION stn.fn_anexo2(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    dtInicial     	ALIAS FOR $2;
    dtFinal     	ALIAS FOR $3;
    stCodEntidades 	ALIAS FOR $4;
    
    dtIniExercicio      VARCHAR := '';
    stNomFuncao         VARCHAR := '';
    stSql               VARCHAR := '';
    stSqlAux            VARCHAR := '';
    reRegistro          RECORD ;
BEGIN

    dtIniExercicio := '01/01/' || stExercicio;
    IF stExercicio::integer <= 2012 THEN
        stNomFuncao := 'DESPESAS (EXCETO INTRA-ORÇAMENTÁRIAS)';
    ELSE
        stNomFuncao := 'DESPESAS (EXCETO INTRA-ORÇAMENTÁRIAS) (I)';
    END IF;
 
    stSql := '
    CREATE TEMPORARY TABLE tmp_orcamentarias AS
      SELECT d.cod_funcao
           , d.cod_subfuncao
           , f.descricao        AS nom_funcao
           , sf.descricao       AS nom_subfuncao
           , sum(d.vl_original) AS vl_original
           , (sum(coalesce(d.vl_original,0.00)) + (sum(coalesce(suplementado.vl_suplementado,0.00)) - sum(coalesce(reduzido.vl_reduzido,0.00)))) AS vl_suplementacoes
           , SUM(COALESCE((COALESCE(empenhado_no_bimestre.vl_empenhado,0.00) - COALESCE(empenhado_anulado_no_bimestre.vl_empenhado_anulado,0.00)),0.00)) AS vl_empenhado_bimestre
           , SUM(COALESCE((COALESCE(empenhado_ate_bimestre.vl_empenhado,0.00) - COALESCE(empenhado_anulado_ate_bimestre.vl_empenhado_anulado,0.00)),0.00)) AS vl_empenhado_ate_bimestre
           , SUM(COALESCE((COALESCE(liquidado_no_bimestre.vl_liquidado,0.00) - COALESCE(liquidado_anulado_no_bimestre.vl_liquidado_anulado,0.00)),0.00)) AS vl_liquidado_bimestre
           , SUM(COALESCE((COALESCE(liquidado_ate_bimestre.vl_liquidado,0.00) - COALESCE(liquidado_anulado_ate_bimestre.vl_liquidado_anulado,0.00)),0.00)) AS vl_liquidado_ate_bimestre
        FROM orcamento.despesa  AS d
   LEFT JOIN (
              SELECT ss.exercicio
                   , ss.cod_despesa
                   , sum(COALESCE(ss.valor,0.00)) AS vl_suplementado
                FROM orcamento.suplementacao AS s
                   , orcamento.suplementacao_suplementada AS ss
               WHERE s.exercicio         = ss.exercicio
                 AND s.cod_suplementacao = ss.cod_suplementacao
                 AND s.dt_suplementacao::date BETWEEN to_date( '''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                  AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
            GROUP BY ss.exercicio
                   , ss.cod_despesa
            ORDER BY ss.cod_despesa
             ) AS suplementado
          ON suplementado.exercicio   = d.exercicio
         AND suplementado.cod_despesa = d.cod_despesa

   LEFT JOIN (
              SELECT sr.exercicio
                   , sr.cod_despesa
                   , sum(COALESCE(sr.valor,0.00)) AS vl_reduzido
                FROM orcamento.suplementacao AS s
                   , orcamento.suplementacao_reducao AS sr
               WHERE s.exercicio         = sr.exercicio
                 AND s.cod_suplementacao = sr.cod_suplementacao
                 AND s.exercicio         = '''||stExercicio||'''
                 AND s.dt_suplementacao::date BETWEEN to_date('''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                  AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
            GROUP BY sr.exercicio
                   , sr.cod_despesa
            ORDER BY sr.cod_despesa
             ) AS reduzido
          ON reduzido.exercicio   = d.exercicio
         AND reduzido.cod_despesa = d.cod_despesa

   LEFT JOIN (
              SELECT sum(coalesce(vl_total,0.00)) as vl_empenhado
                   , ode.cod_despesa
                   , ode.exercicio
                   , ode.cod_entidade
                FROM orcamento.conta_despesa AS ocd
          INNER JOIN orcamento.despesa AS ode
                  ON ode.exercicio = ocd.exercicio
                 AND ode.cod_conta = ocd.cod_conta
          INNER JOIN empenho.pre_empenho_despesa AS ped
                  ON ped.exercicio = ode.exercicio
                 AND ped.cod_despesa = ode.cod_despesa
          INNER JOIN empenho.pre_empenho AS pe
                  ON ped.exercicio = pe.exercicio
                 AND ped.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.item_pre_empenho AS ipe
                  ON ipe.cod_pre_empenho = pe.cod_pre_empenho
                 AND ipe.exercicio = pe.exercicio
          INNER JOIN empenho.empenho AS e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND e.dt_empenho BETWEEN to_date('''|| dtInicial||''', ''dd/mm/yyyy'')
                                      AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY ode.cod_despesa
                   , ode.exercicio
             ) AS empenhado_no_bimestre
          ON empenhado_no_bimestre.exercicio   = d.exercicio
         AND empenhado_no_bimestre.cod_despesa = d.cod_despesa
             
   LEFT JOIN (
              SELECT sum(COALESCE(eai.vl_anulado, 0.00)) as vl_empenhado_anulado
                   , ode.cod_despesa
                   , ode.exercicio
                FROM orcamento.conta_despesa ocd
          INNER JOIN orcamento.despesa ode
                  ON ode.exercicio = ocd.exercicio
                 AND ode.cod_conta = ocd.cod_conta
          INNER JOIN empenho.pre_empenho_despesa ped
                  ON ped.exercicio = ode.exercicio
                 AND ped.cod_despesa = ode.cod_despesa
          INNER JOIN empenho.pre_empenho pe
                  ON ped.exercicio = pe.exercicio
                 AND ped.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.empenho e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.empenho_anulado ea
                  ON ea.exercicio = e.exercicio
                 AND ea.cod_entidade = e.cod_entidade
                 AND ea.cod_empenho = e.cod_empenho
          INNER JOIN empenho.empenho_anulado_item eai
                  ON eai.exercicio = ea.exercicio
                 AND eai.cod_entidade = ea.cod_entidade
                 AND eai.cod_empenho = ea.cod_empenho
                 AND eai.timestamp = ea.timestamp
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND to_date(to_char(ea.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN to_date('''|| dtInicial||''', ''dd/mm/yyyy'')
                                                                                        AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY ode.cod_despesa
                   , ode.exercicio
             ) AS empenhado_anulado_no_bimestre
          ON empenhado_anulado_no_bimestre.exercicio   = d.exercicio
         AND empenhado_anulado_no_bimestre.cod_despesa = d.cod_despesa
             

   LEFT JOIN (
              SELECT sum(coalesce(vl_total,0.00)) as vl_empenhado
                   , ode.cod_despesa
                   , ode.exercicio
                   , ode.cod_entidade
                FROM orcamento.conta_despesa AS ocd
          INNER JOIN orcamento.despesa AS ode
                  ON ode.exercicio = ocd.exercicio
                 AND ode.cod_conta = ocd.cod_conta
          INNER JOIN empenho.pre_empenho_despesa AS ped
                  ON ped.exercicio = ode.exercicio
                 AND ped.cod_despesa = ode.cod_despesa
          INNER JOIN empenho.pre_empenho AS pe
                  ON ped.exercicio = pe.exercicio
                 AND ped.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.item_pre_empenho AS ipe
                  ON ipe.cod_pre_empenho = pe.cod_pre_empenho
                 AND ipe.exercicio = pe.exercicio
          INNER JOIN empenho.empenho AS e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND e.dt_empenho BETWEEN to_date('''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                      AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY ode.cod_despesa
                   , ode.exercicio
             ) AS empenhado_ate_bimestre
          ON empenhado_ate_bimestre.exercicio   = d.exercicio
         AND empenhado_ate_bimestre.cod_despesa = d.cod_despesa
             
   LEFT JOIN (
              SELECT sum(COALESCE(eai.vl_anulado, 0.00)) as vl_empenhado_anulado
                   , ode.cod_despesa
                   , ode.exercicio
                FROM orcamento.conta_despesa ocd
          INNER JOIN orcamento.despesa ode
                  ON ode.exercicio = ocd.exercicio
                 AND ode.cod_conta = ocd.cod_conta
          INNER JOIN empenho.pre_empenho_despesa ped
                  ON ped.exercicio = ode.exercicio
                 AND ped.cod_despesa = ode.cod_despesa
          INNER JOIN empenho.pre_empenho pe
                  ON ped.exercicio = pe.exercicio
                 AND ped.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.empenho e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.empenho_anulado ea
                  ON ea.exercicio = e.exercicio
                 AND ea.cod_entidade = e.cod_entidade
                 AND ea.cod_empenho = e.cod_empenho
          INNER JOIN empenho.empenho_anulado_item eai
                  ON eai.exercicio = ea.exercicio
                 AND eai.cod_entidade = ea.cod_entidade
                 AND eai.cod_empenho = ea.cod_empenho
                 AND eai.timestamp = ea.timestamp
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND to_date(to_char(ea.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN to_date('''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                                                                        AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(ocd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY ode.cod_despesa
                   , ode.exercicio
             ) AS empenhado_anulado_ate_bimestre
          ON empenhado_anulado_ate_bimestre.exercicio   = d.exercicio
         AND empenhado_anulado_ate_bimestre.cod_despesa = d.cod_despesa

   LEFT JOIN (
              SELECT coalesce(sum(vl_total), 0.00) as vl_liquidado
                   , pedcd.cod_despesa
                   , pedcd.exercicio_despesa
                FROM empenho.pre_empenho pe
          INNER JOIN (
                      SELECT ped.exercicio
                           , ped.cod_pre_empenho
                           , cd.cod_estrutural
                           , d.cod_despesa
                           , d.exercicio AS exercicio_despesa
                        FROM orcamento.conta_despesa cd
                  INNER JOIN empenho.pre_empenho_despesa ped
                          ON ped.cod_conta   = cd.cod_conta
                         AND ped.exercicio   = cd.exercicio
                  INNER JOIN orcamento.despesa d
                          ON ped.cod_despesa = d.cod_despesa
                         AND ped.exercicio   = d.exercicio
                       WHERE ped.exercicio = '''||stExercicio||'''
                     ) AS pedcd
                  ON pe.exercicio = pedcd.exercicio
                 AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
          INNER JOIN empenho.empenho e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.nota_liquidacao nl
                  ON nl.exercicio_empenho = e.exercicio
                 AND nl.cod_entidade = e.cod_entidade
                 AND nl.cod_empenho = e.cod_empenho
          INNER JOIN empenho.nota_liquidacao_item nli
                  ON nli.exercicio = nl.exercicio
                 AND nli.cod_entidade = nl.cod_entidade
                 AND nli.cod_nota = nl.cod_nota
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND nl.dt_liquidacao BETWEEN to_date('''|| dtInicial||''', ''dd/mm/yyyy'')
                                          AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY pedcd.cod_despesa
                   , pedcd.exercicio_despesa
             ) AS liquidado_no_bimestre
          ON liquidado_no_bimestre.exercicio_despesa = d.exercicio
         AND liquidado_no_bimestre.cod_despesa       = d.cod_despesa

   LEFT JOIN (
              SELECT SUM(coalesce(nlia.vl_anulado, 0.00)) as vl_liquidado_anulado
                   , pedcd.cod_despesa
                   , pedcd.exercicio_despesa
                FROM empenho.pre_empenho pe
                JOIN (
                      SELECT ped.exercicio
                           , ped.cod_pre_empenho
                           , cd.cod_estrutural
                           , d.cod_despesa
                           , d.exercicio AS exercicio_despesa
                        FROM orcamento.conta_despesa cd
                  INNER JOIN empenho.pre_empenho_despesa ped
                          ON ped.cod_conta   = cd.cod_conta
                         AND ped.exercicio   = cd.exercicio
                  INNER JOIN orcamento.despesa d
                          ON ped.cod_despesa = d.cod_despesa
                         AND ped.exercicio   = d.exercicio
                       WHERE ped.exercicio = '''||stExercicio||'''
                     ) AS pedcd
                  ON pe.exercicio = pedcd.exercicio
                 AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
          INNER JOIN empenho.empenho e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.nota_liquidacao nl
                  ON e.exercicio = nl.exercicio_empenho
                 AND e.cod_entidade = nl.cod_entidade
                 AND e.cod_empenho = nl.cod_empenho
          INNER JOIN empenho.nota_liquidacao_item nli
                  ON nl.exercicio = nli.exercicio
                 AND nl.cod_nota = nli.cod_nota
                 AND nl.cod_entidade = nli.cod_entidade
          INNER JOIN empenho.nota_liquidacao_item_anulado nlia
                  ON nli.exercicio = nlia.exercicio
                 AND nli.cod_nota = nlia.cod_nota
                 AND nli.cod_entidade = nlia.cod_entidade
                 AND nli.num_item = nlia.num_item
                 AND nli.cod_pre_empenho = nlia.cod_pre_empenho
                 AND nli.exercicio_item = nlia.exercicio_item
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''|| dtInicial||''',''dd/mm/yyyy'')
                                                                                        AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY pedcd.cod_despesa
                   , pedcd.exercicio_despesa
             ) AS liquidado_anulado_no_bimestre
          ON liquidado_anulado_no_bimestre.exercicio_despesa = d.exercicio
         AND liquidado_anulado_no_bimestre.cod_despesa       = d.cod_despesa

   LEFT JOIN (
              SELECT coalesce(sum(vl_total), 0.00) as vl_liquidado
                   , pedcd.cod_despesa
                   , pedcd.exercicio_despesa
                FROM empenho.pre_empenho pe
          INNER JOIN (
                      SELECT ped.exercicio
                           , ped.cod_pre_empenho
                           , cd.cod_estrutural
                           , d.cod_despesa
                           , d.exercicio AS exercicio_despesa
                        FROM orcamento.conta_despesa cd
                  INNER JOIN empenho.pre_empenho_despesa ped
                          ON ped.cod_conta   = cd.cod_conta
                         AND ped.exercicio   = cd.exercicio
                  INNER JOIN orcamento.despesa d
                          ON ped.cod_despesa = d.cod_despesa
                         AND ped.exercicio   = d.exercicio
                       WHERE ped.exercicio = '''||stExercicio||'''                      
                     ) AS pedcd
                  ON pe.exercicio = pedcd.exercicio
                 AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
          INNER JOIN empenho.empenho e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.nota_liquidacao nl
                  ON nl.exercicio_empenho = e.exercicio
                 AND nl.cod_entidade = e.cod_entidade
                 AND nl.cod_empenho = e.cod_empenho
          INNER JOIN empenho.nota_liquidacao_item nli
                  ON nli.exercicio = nl.exercicio
                 AND nli.cod_entidade = nl.cod_entidade
                 AND nli.cod_nota = nl.cod_nota
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND nl.dt_liquidacao BETWEEN to_date('''||dtIniExercicio||''', ''dd/mm/yyyy'')
                                          AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY pedcd.cod_despesa
                   , pedcd.exercicio_despesa
             ) AS liquidado_ate_bimestre
          ON liquidado_ate_bimestre.exercicio_despesa = d.exercicio
         AND liquidado_ate_bimestre.cod_despesa       = d.cod_despesa

   LEFT JOIN (
              SELECT SUM(coalesce(nlia.vl_anulado, 0.00)) as vl_liquidado_anulado
                   , pedcd.cod_despesa
                   , pedcd.exercicio_despesa
                FROM empenho.pre_empenho pe
                JOIN (
                      SELECT ped.exercicio
                           , ped.cod_pre_empenho
                           , cd.cod_estrutural
                           , d.cod_despesa
                           , d.exercicio AS exercicio_despesa
                        FROM orcamento.conta_despesa cd
                  INNER JOIN empenho.pre_empenho_despesa ped
                          ON ped.cod_conta   = cd.cod_conta
                         AND ped.exercicio   = cd.exercicio
                  INNER JOIN orcamento.despesa d
                          ON ped.cod_despesa = d.cod_despesa
                         AND ped.exercicio   = d.exercicio
                       WHERE ped.exercicio = '''||stExercicio||'''
                     ) AS pedcd
                  ON pe.exercicio = pedcd.exercicio
                 AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
          INNER JOIN empenho.empenho e
                  ON e.exercicio = pe.exercicio
                 AND e.cod_pre_empenho = pe.cod_pre_empenho
          INNER JOIN empenho.nota_liquidacao nl
                  ON e.exercicio = nl.exercicio_empenho
                 AND e.cod_entidade = nl.cod_entidade
                 AND e.cod_empenho = nl.cod_empenho
          INNER JOIN empenho.nota_liquidacao_item nli
                  ON nl.exercicio = nli.exercicio
                 AND nl.cod_nota = nli.cod_nota
                 AND nl.cod_entidade = nli.cod_entidade
          INNER JOIN empenho.nota_liquidacao_item_anulado nlia
                  ON nli.exercicio = nlia.exercicio
                 AND nli.cod_nota = nlia.cod_nota
                 AND nli.cod_entidade = nlia.cod_entidade
                 AND nli.num_item = nlia.num_item
                 AND nli.cod_pre_empenho = nlia.cod_pre_empenho
                 AND nli.exercicio_item = nlia.exercicio_item
               WHERE e.exercicio = '''||stExercicio||'''
                 AND e.cod_entidade IN ('||stCodEntidades||')
                 AND to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''||dtIniExercicio||''',''dd/mm/yyyy'')
                                                                                        AND to_date('''||dtFinal||''', ''dd/mm/yyyy'')
                 AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''
            GROUP BY pedcd.cod_despesa
                   , pedcd.exercicio_despesa
             ) AS liquidado_anulado_ate_bimestre
          ON liquidado_anulado_ate_bimestre.exercicio_despesa = d.exercicio
         AND liquidado_anulado_ate_bimestre.cod_despesa       = d.cod_despesa

   LEFT JOIN orcamento.funcao AS f
          ON f.exercicio  = d.exercicio
         AND f.cod_funcao = d.cod_funcao

   LEFT JOIN orcamento.subfuncao AS sf
          ON sf.exercicio     = d.exercicio
         AND sf.cod_subfuncao = d.cod_subfuncao
           , orcamento.conta_despesa AS cd

       WHERE d.cod_conta = cd.cod_conta
         AND d.exercicio = cd.exercicio
         AND d.exercicio = '''||stExercicio||'''
         AND d.cod_entidade IN ('||stCodEntidades||')
         AND substring(cd.cod_estrutural, 5, 3) <> ''9.1''
    GROUP BY d.cod_funcao
           , d.cod_subfuncao
           , f.descricao
           , sf.descricao
    ORDER BY f.descricao

    ';


    EXECUTE stSql;

    stSql :='
    INSERT INTO tmp_orcamentarias
    SELECT cod_funcao                        AS cod_funcao
         , 0                                 AS cod_subfuncao
         , nom_funcao                        AS nom_funcao
         , nom_funcao                        AS nom_subfuncao
         , sum(vl_original)                  AS vl_original
         , sum(vl_suplementacoes)            AS vl_suplementacoes
         , sum(vl_empenhado_bimestre)        AS vl_empenhado_bimestre
         , sum(vl_empenhado_ate_bimestre)    AS vl_empenhado_ate_bimestre
         , sum(vl_liquidado_bimestre)        AS vl_liquidado_bimestre
         , sum(vl_liquidado_ate_bimestre)    AS vl_liquidado_ate_bimestre
      FROM tmp_orcamentarias 
  GROUP BY cod_funcao
         , nom_funcao; ';

    EXECUTE stSql;

    --Verificando se existe despesa para RESERVA DO RPPS - 7.7.9.9.99.99.00.00.00
    stSqlAux := 'ORDER BY cod_funcao, cod_subfuncao';
    PERFORM 1 
        FROM orcamento.conta_despesa 
        INNER JOIN orcamento.despesa
                ON despesa.cod_conta  = conta_despesa.cod_conta
                AND despesa.exercicio = conta_despesa.exercicio
        WHERE cod_estrutural ilike '%7.7.9.9.99.99.00.00.00%'
        AND conta_despesa.exercicio = stExercicio;

    IF NOT FOUND THEN
        stSqlAux := 'UNION ALL
                    
                    SELECT 
                          99
                        , 999
                        , ''RESERVA DO RPPS''
                        , ''RESERVA DO RPPS''
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00
                        , 0.00

                    ORDER BY cod_funcao, cod_subfuncao
                    ';
    END IF;                

    stSql := '
              SELECT  
                     0                              AS cod_funcao
                   , 0                              AS cod_subfuncao
                   , '''|| stNomFuncao ||'''        AS nom_funcao
                   , '''|| stNomFuncao ||'''        AS nom_subfuncao
                   , sum(vl_original)               AS vl_original                      
                   , sum(vl_suplementacoes)         AS vl_suplementacoes
                   , sum(vl_empenhado_bimestre)     AS vl_empenhado_bimestre
                   , sum(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
                   , sum(vl_liquidado_bimestre)     AS vl_liquidado_bimestre
                   , sum(vl_liquidado_ate_bimestre) AS vl_liquidado_ate_bimestre
                FROM
                     tmp_orcamentarias
               WHERE cod_subfuncao = 0

              UNION ALL

              SELECT 
                     cod_funcao
                   , cod_subfuncao
                   , nom_funcao
                   , nom_subfuncao
                   , vl_original
                   , vl_suplementacoes
                   , vl_empenhado_bimestre
                   , vl_empenhado_ate_bimestre
                   , vl_liquidado_bimestre
                   , vl_liquidado_ate_bimestre
                FROM
                     tmp_orcamentarias

            '||stSqlAux||'
                
            ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
    RETURN;

END;
$$
LANGUAGE plpgsql;
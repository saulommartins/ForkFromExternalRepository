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
* $Revision: 60827 $
* $Name$
* $Author: franver $
* $Date: 2014-11-17 18:48:25 -0200 (Mon, 17 Nov 2014) $
*
*/

CREATE OR REPLACE FUNCTION tceto.fn_balancete_depesa(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    ALIAS FOR $1;
    stDataInicial  ALIAS FOR $2;
    stDataFinal    ALIAS FOR $3;
    stCodEntidades ALIAS FOR $4;

    stSql            VARCHAR   := '';
    dataInicio       VARCHAR   := '';
    dataFim          VARCHAR   := '';
    stNomePrefeitura VARCHAR;
    reRegistro       RECORD;

BEGIN
    dataInicio := '1/1/' || stExercicio;

    IF stExercicio >= TO_CHAR(now(), 'yyyy') THEN
        dataFim := TO_CHAR(NOW(), 'dd/mm/yyyy');
    ELSE
        dataFim := '31/12/' || stExercicio;
    END IF;

    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
              SELECT e.dt_empenho as dataConsulta
                   , coalesce(ipe.vl_total,0.00) as valor
                   , cd.cod_estrutural as cod_estrutural
                   , od.num_orgao as num_orgao
                   , od.num_unidade as num_unidade
                FROM orcamento.despesa           as od
                   , orcamento.conta_despesa     as cd
                   , empenho.pre_empenho_despesa as ped
                   , empenho.empenho             as e
                   , empenho.pre_empenho         as pe
                   , empenho.item_pre_empenho    as ipe
               WHERE cd.cod_conta       = ped.cod_conta
                 AND cd.exercicio       = ped.exercicio
                 AND od.cod_despesa     = ped.cod_despesa
                 AND od.exercicio       = ped.exercicio
                 AND pe.exercicio       = ped.exercicio
                 AND pe.cod_pre_empenho = ped.cod_pre_empenho
                 AND e.cod_entidade     IN ('||stCodEntidades||')
                 AND e.exercicio        = '''||stExercicio||'''
                 AND e.exercicio        = pe.exercicio
                 AND e.cod_pre_empenho  = pe.cod_pre_empenho
                 AND pe.exercicio       = ipe.exercicio
                 AND pe.cod_pre_empenho = ipe.cod_pre_empenho
              )';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
              SELECT TO_DATE(TO_CHAR(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') AS dataConsulta
                   , EEAI.vl_anulado AS valor
                   , OCD.cod_estrutural AS cod_estrutural
                   , OD.num_orgao
                   , OD.num_unidade
                FROM orcamento.despesa            AS OD
                   , orcamento.conta_despesa      AS OCD
                   , empenho.pre_empenho_despesa  AS EPED
                   , empenho.pre_empenho          AS EPE
                   , empenho.item_pre_empenho     AS EIPE
                   , empenho.empenho_anulado_item AS EEAI
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EPE.exercicio        = EIPE.exercicio
                 AND EPE.cod_pre_empenho  = EIPE.cod_pre_empenho
                 AND EIPE.exercicio       = EEAI.exercicio
                 AND EIPE.cod_pre_empenho = EEAI.cod_pre_empenho
                 AND EIPE.num_item        = EEAI.num_item
                 AND EEAI.exercicio       = '''||stExercicio||'''
                 AND EEAI.cod_entidade    IN ('||stCodEntidades||')
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
              )';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
              SELECT TO_DATE(TO_CHAR(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') AS dataConsulta
                   , ENLP.vl_pago AS valor
                   , OCD.cod_estrutural AS cod_estrutural
                   , OD.num_orgao AS num_orgao
                   , OD.num_unidade AS num_unidade
                FROM orcamento.despesa               AS OD
                   , orcamento.conta_despesa         AS OCD
                   , empenho.pre_empenho_despesa     AS EPED
                   , empenho.empenho                 AS EE
                   , empenho.pre_empenho             AS EPE
                   , empenho.nota_liquidacao         AS ENL
                   , empenho.nota_liquidacao_paga    AS ENLP
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPE.exercicio        = EE.exercicio
                 AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
                 AND EE.exercicio         ='''||stExercicio||'''
                 AND EE.cod_entidade      IN ('||stCodEntidades||')
                 AND EE.cod_empenho       = ENL.cod_empenho
                 AND EE.exercicio         = ENL.exercicio_empenho
                 AND EE.cod_entidade      = ENL.cod_entidade
                 AND ENL.cod_nota         = ENLP.cod_nota
                 AND ENL.cod_entidade     = ENLP.cod_entidade
                 AND ENL.exercicio        = ENLP.exercicio
              )';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
              SELECT TO_DATE(TO_CHAR(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') AS dataConsulta
                   , ENLPA.vl_anulado AS valor
                   , OCD.cod_estrutural AS cod_estrutural
                   , OD.num_orgao AS num_orgao
                   , OD.num_unidade AS num_unidade
                FROM orcamento.despesa                    AS OD
                   , orcamento.conta_despesa              AS OCD
                   , empenho.pre_empenho_despesa          AS EPED
                   , empenho.empenho                      AS EE
                   , empenho.pre_empenho                  AS EPE
                   , empenho.nota_liquidacao              AS ENL
                   , empenho.nota_liquidacao_paga         AS ENLP
                   , empenho.nota_liquidacao_paga_anulada AS ENLPA
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EPE.exercicio        = EE.exercicio
                 AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
                 AND EE.cod_entidade      IN ('||stCodEntidades||')
                 AND EE.exercicio         = '''||stExercicio||'''
                 AND EE.cod_empenho       = ENL.cod_empenho
                 AND EE.exercicio         = ENL.exercicio_empenho
                 AND EE.cod_entidade      = ENL.cod_entidade
                 AND ENL.exercicio        = ENLP.exercicio
                 AND ENL.cod_nota         = ENLP.cod_nota
                 AND ENL.cod_entidade     = ENLP.cod_entidade
                 AND ENLP.cod_entidade    = ENLPA.cod_entidade
                 AND ENLP.cod_nota        = ENLPA.cod_nota
                 AND ENLP.exercicio       = ENLPA.exercicio
                 AND ENLP.timestamp       = ENLPA.timestamp
              )';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
              SELECT nl.dt_liquidacao AS dataConsulta
                   , nli.vl_total AS valor
                   , cd.cod_estrutural AS cod_estrutural
                   , od.num_orgao AS num_orgao
                   , od.num_unidade AS num_unidade
                FROM orcamento.despesa             AS od
                   , orcamento.conta_despesa       AS cd
                   , empenho.pre_empenho_despesa   AS ped
                   , empenho.pre_empenho           AS pe
                   , empenho.empenho               AS e
                   , empenho.nota_liquidacao_item  AS nli
                   , empenho.nota_liquidacao       AS nl
               WHERE cd.cod_conta       = ped.cod_conta
                 AND cd.exercicio       = ped.exercicio
                 AND od.cod_despesa     = ped.cod_despesa
                 AND od.exercicio       = ped.exercicio
                 AND pe.exercicio       = ped.exercicio
                 AND pe.cod_pre_empenho = ped.cod_pre_empenho
                 AND e.cod_entidade     IN ('||stCodEntidades||')
                 AND e.exercicio        = '''||stExercicio||'''
                 AND e.exercicio        = pe.exercicio
                 AND e.cod_pre_empenho  = pe.cod_pre_empenho
                 AND e.exercicio        = nl.exercicio_empenho
                 AND e.cod_entidade     = nl.cod_entidade
                 AND e.cod_empenho      = nl.cod_empenho
                 AND nl.exercicio       = nli.exercicio
                 AND nl.cod_nota        = nli.cod_nota
                 AND nl.cod_entidade    = nli.cod_entidade
              )';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
              SELECT TO_DATE(TO_CHAR(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') AS dataConsulta
                   , ENLIA.vl_anulado AS valor
                   , OCD.cod_estrutural AS cod_estrutural
                   , OD.num_orgao
                   , OD.num_unidade
                FROM orcamento.despesa                    AS OD
                   , orcamento.conta_despesa              AS OCD
                   , empenho.pre_empenho_despesa          AS EPED
                   , empenho.pre_empenho                  AS EPE
                   , empenho.empenho                      AS EE
                   , empenho.nota_liquidacao              AS ENL
                   , empenho.nota_liquidacao_item         AS ENLI
                   , empenho.nota_liquidacao_item_anulado AS ENLIA
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
                 AND EPE.exercicio        = EE.exercicio
                 AND EE.exercicio         = ENL.exercicio_empenho
                 AND EE.cod_entidade      = ENL.cod_entidade
                 AND EE.cod_empenho       = ENL.cod_empenho
                 AND EE.cod_entidade      IN ('||stCodEntidades||')
                 AND EE.exercicio         = '''||stExercicio||'''
                 AND ENL.exercicio        = ENLI.exercicio
                 AND ENL.cod_nota         = ENLI.cod_nota
                 AND ENL.cod_entidade     = ENLI.cod_entidade
                 AND ENLI.exercicio       = ENLIA.exercicio
                 AND ENLI.cod_pre_empenho = ENLIA.cod_pre_empenho
                 AND ENLI.num_item        = ENLIA.num_item
                 AND ENLI.cod_entidade    = ENLIA.cod_entidade
                 AND ENLI.exercicio_item  = ENLIA.exercicio_item
                 AND ENLI.cod_nota        = ENLIA.cod_nota
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
                 AND OD.cod_entidade      IN ('||stCodEntidades||')
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho 
              )';
    EXECUTE stSql;

    stSql := '--CRIA TABELA TEMPORÁRIA COM TODOS AS DESPESAS DA DESPESA, SETA ELAS COMO MÃE
              CREATE TEMPORARY TABLE tmp_pre_empenho_despesa AS (
              SELECT exercicio
                   , cod_conta
                   , cod_despesa
                   , CAST(''M'' as varchar) AS tipo_conta
                FROM orcamento.despesa AS od
               WHERE cod_entidade IN ('||stCodEntidades||')
              )';
    EXECUTE stSql;

    stSql := '--ATUALIZA O TOPO DA SOMA PARA TODOS OS REGISTRO QUE ESTIVEREM NA TABELA PRE_EMPENHO
              UPDATE tmp_pre_empenho_despesa
                 SET tipo_conta=''D''
               WHERE exercicio||''-''||cod_conta IN (SELECT exercicio||''-''||cod_conta
                                                       FROM empenho.pre_empenho_despesa)';
    EXECUTE stSql;

    stSql := '--INSERE NA TABELA TEMPORARIA OS REGISTROS RESUTADOS DE UM SELECT
              --ESTE SELECT PREVEM DA TABELA PRE_EMPENHO_DESPESA ONDE TODOS OS REGISTROS SÃO SETADOS COMO FILHAS
              INSERT
                INTO tmp_pre_empenho_despesa
              SELECT ped.exercicio
                   , ped.cod_conta
                   , ped.cod_despesa
                   , CAST(''F'' as varchar) AS tipo_conta
                FROM empenho.pre_empenho_despesa ped
                   , empenho.pre_empenho pe
                   , empenho.empenho e
               WHERE NOT EXISTS (SELECT 1
                                   FROM tmp_pre_empenho_despesa
                                  WHERE exercicio = ped.exercicio
                                    AND cod_conta = ped.cod_conta
                                )
                 AND ped.exercicio       = pe.exercicio
                 AND ped.cod_pre_empenho = pe.cod_pre_empenho
                 AND pe.exercicio        = e.exercicio
                 AND pe.cod_pre_empenho  = e.cod_pre_empenho
                 AND e.cod_entidade IN ('||stCodEntidades||')';
    EXECUTE stSql;


    SELECT valor
      INTO stNomePrefeitura
      FROM administracao.configuracao
     WHERE exercicio = ''||stExercicio||''
       AND parametro = 'nom_prefeitura';

    stSql := 'CREATE TEMPORARY TABLE tmp_relacao AS
                   --SELECIONA ORCAMENTO.DESPESA
              SELECT od.exercicio        AS exercicio
                   , od.cod_despesa      AS cod_despesa
                   , od.cod_entidade     AS cod_entidade
                   --SELECIONA EMPENHO.PRE_EMPENHO_DESPESA 
                   , eped.tipo_conta     AS tipo_conta
                   --SELECIONA ORCAMENT.CONTA_DESPESA
                   , ocd.cod_estrutural  AS classificacao
                   , publico.fn_mascarareduzida(ocd.cod_estrutural) as cod_reduzido
                   , ocd.descricao       AS descricao
                   --SELECIONA ORCAMENTO.SUPLEMENTACOES_SUPLEMENTADA
                   , oss.valor           AS suplementacoes
                   , oss.cod_tipo        AS tipo_suplementacao
                   --SELECIONA ORCAMENTO.SUPLEMENTACOES_REDUZIDA
                   , osr.valor           AS reducoes
                   , 0                   AS num_orgao
                   , eped.tipo_conta     AS nom_orgao
                   , 0                   AS num_unidade
                   , eped.tipo_conta     AS nom_unidade
                FROM tmp_pre_empenho_despesa eped
                   , orcamento.conta_despesa ocd
                   , orcamento.despesa od
           LEFT JOIN (SELECT cod_despesa AS cod_despesa
                           , MAX(oss1.exercicio) AS exercicio
                           , os.cod_tipo
                           , SUM(valor) AS valor
                        FROM orcamento.suplementacao_suplementada AS oss1
                           , orcamento.suplementacao AS os
                       WHERE os.cod_suplementacao = oss1.cod_suplementacao
                         AND os.exercicio         = oss1.exercicio
                         AND os.dt_suplementacao BETWEEN TO_DATE('''||dataInicio||''',''dd/mm/yyyy'')
                                                     AND TO_DATE('''||stDataFinal||''',''dd/mm/yyyy'')
                         AND os.cod_suplementacao||''-''|| os.exercicio IN (SELECT cod_suplementacao||''-''||cl.exercicio
                                                                              FROM contabilidade.transferencia_despesa ctd
                                                                                 , contabilidade.lote cl
                                                                             WHERE ctd.exercicio    = cl.exercicio
                                                                               AND ctd.cod_lote     = cl.cod_lote
                                                                               AND ctd.tipo         = cl.tipo
                                                                               AND ctd.cod_entidade = cl.cod_entidade
                                                                               AND cl.dt_lote BETWEEN TO_DATE('''||dataInicio||''',''dd/mm/yyyy'')
                                                                                                  AND TO_DATE('''||stDataFinal||''',''dd/mm/yyyy''))
                         AND NOT EXISTS ( SELECT 1
                                            FROM orcamento.suplementacao_anulada o_sa
                                           WHERE o_sa.cod_suplementacao = os.cod_suplementacao
                                             AND o_sa.exercicio         = os.exercicio
                                             AND o_sa.exercicio         = '''||stExercicio||'''
                                        )
                                       
                         AND NOT EXISTS ( SELECT 1
                                             FROM orcamento.suplementacao_anulada o_sa2
                                            WHERE o_sa2.cod_suplementacao_anulacao = os.cod_suplementacao
                                              AND o_sa2.exercicio                  = os.exercicio
                                              AND o_sa2.exercicio                  = '''||stExercicio||'''
                                        )
                                       
                    GROUP BY oss1.exercicio
                           , oss1.cod_despesa
                           , cod_tipo
                     ) AS oss
                  ON od.cod_despesa = oss.cod_despesa
                 AND od.exercicio = oss.exercicio
           LEFT JOIN (SELECT cod_despesa
                           , MAX(osr1.exercicio) AS exercicio
                           , SUM(valor) AS valor
                        FROM orcamento.suplementacao_reducao AS osr1
                           , orcamento.suplementacao AS os
                       WHERE os.cod_suplementacao = osr1.cod_suplementacao
                         AND os.exercicio         = osr1.exercicio
                         AND os.cod_suplementacao || ''-'' || os.exercicio IN (SELECT cod_suplementacao||''-''||cl.exercicio
                                                                                 FROM contabilidade.transferencia_despesa ctd
                                                                                    , contabilidade.lote cl
                                                                                WHERE ctd.exercicio = cl.exercicio
                                                                                  AND ctd.cod_lote  = cl.cod_lote
                                                                                  AND ctd.tipo      = cl.tipo
                                                                                  AND ctd.cod_entidade = cl.cod_entidade
                                                                                  AND cl.dt_lote BETWEEN TO_DATE('''||dataInicio||''',''dd/mm/yyyy'')
                                                                                                     AND TO_DATE('''||stDataFinal||''',''dd/mm/yyyy''))
                         AND NOT EXISTS ( SELECT 1
                                            FROM orcamento.suplementacao_anulada o_sa3
                                           WHERE o_sa3.cod_suplementacao = os.cod_suplementacao
                                             AND o_sa3.exercicio         = os.exercicio
                                             AND o_sa3.exercicio         = '''||stExercicio||'''
                                        )
                         AND NOT EXISTS ( SELECT 1
                                            FROM orcamento.suplementacao_anulada o_sa4
                                           WHERE o_sa4.cod_suplementacao_anulacao = os.cod_suplementacao
                                             AND o_sa4.exercicio                  = os.exercicio
                                             AND o_sa4.exercicio                  = '''||stExercicio||'''
                                        )
                    GROUP BY osr1.exercicio
                           , cod_despesa
                     ) AS osr
                  ON od.cod_despesa = osr.cod_despesa
                 AND od.exercicio   = osr.exercicio
          INNER JOIN orcamento.recurso('''||stExercicio||''') as rec
                  ON od.cod_recurso = rec.cod_recurso
                 AND od.exercicio   = rec.exercicio
               WHERE eped.cod_despesa      = od.cod_despesa
                 AND eped.exercicio        = od.exercicio
                 AND eped.cod_conta        = ocd.cod_conta
                 AND eped.exercicio        = ocd.exercicio
                 AND od.cod_entidade       IN ('||stCodEntidades||')
                 AND od.exercicio          = '''||stExercicio||'''
            GROUP BY ocd.cod_estrutural
                   , od.cod_entidade
                   , od.exercicio
                   , od.cod_despesa
                   , ocd.descricao
                   , oss.valor
                   , osr.valor
                   , oss.cod_tipo
                   , eped.tipo_conta
            ORDER BY ocd.cod_estrutural
    ';
    EXECUTE stSql;

    stSql := '
        SELECT tbl.classificacao
             , tbl.cod_reduzido
             , tbl.descricao
             , tbl.num_orgao
             , tbl.nom_orgao
             , tbl.num_unidade
             , tbl.nom_unidade
             , tbl.cod_funcao
             , tbl.cod_subfuncao
             , tbl.num_programa as cod_programa
             , tbl.num_acao as cod_proj_atividade
             , tbl.cod_recurso as cod_rec_vinculado
             , tbl.tipo_suplementacao
             , tbl.periodo
             , tbl.vl_previsto
             , coalesce(sum(tbl.saldo_inicial),0.00) as saldo_inicial
             , coalesce(sum(tbl.suplementacoes),0.00) as suplementacoes
             , coalesce(sum(tbl.reducoes),0.00) as reducoes
            -- Empenhado
             , coalesce(orcamento.fn_consolidado_empenhado('''||stDataInicial||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) as empenhado_per
             , coalesce(orcamento.fn_consolidado_empenhado('''||dataInicio||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) as empenhado_ano
            -- Anulado
             , coalesce(orcamento.fn_consolidado_anulado('''||stDataInicial||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) as anulado_per
             , coalesce(orcamento.fn_consolidado_anulado('''||dataInicio||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) as anulado_ano
            -- Pago
             , (coalesce(orcamento.fn_consolidado_pago('''||stDataInicial||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_estornado(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00)) as pago_per
             , (coalesce(orcamento.fn_consolidado_pago('''||dataInicio||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_estornado(''' || dataInicio || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00)) as pago_ano
            -- Liquidado
             , (coalesce(orcamento.fn_consolidado_liquidado('''||stDataInicial||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_liquidado_estornado(''' || stDataInicial || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00)) as liquidado_per
             , (coalesce(orcamento.fn_consolidado_liquidado('''||dataInicio||''', '''||stDataFinal||''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_liquidado_estornado(''' || dataInicio || ''', ''' || stDataFinal || ''', publico.fn_mascarareduzida(tbl.classificacao),tbl.num_orgao,tbl.num_unidade),0.00)) as liquidado_ano
             , tbl.tipo_conta
             , publico.fn_nivel(classificacao) AS nivel
          FROM (SELECT CASE WHEN tr.classificacao IS NOT NULL
                            THEN tr.classificacao
                            ELSE ocd.cod_estrutural
                        END as classificacao
                     , CASE WHEN tr.cod_reduzido IS NOT NULL
                            THEN tr.cod_reduzido
                            ELSE publico.fn_mascarareduzida(ocd.cod_estrutural)
                        END as cod_reduzido
                     , CASE WHEN tr.descricao IS NOT NULL
                            THEN tr.descricao
                            ELSE ocd.descricao
                        END as descricao
                     , coalesce(od.vl_original) as  saldo_inicial
                     , coalesce(tr.suplementacoes) as suplementacoes
                     , coalesce(tr.reducoes) as reducoes
                     , od.cod_funcao
                     , od.cod_subfuncao
                     , ppa.programa.num_programa
                     , acao.num_acao
                     , rec.cod_recurso
                     , tr.tipo_suplementacao
                     , previsao_despesa.periodo
                     , previsao_despesa.vl_previsto
                     , CASE WHEN tr.tipo_conta IS NOT NULL
                            THEN tr.tipo_conta
                            ELSE ''M''
                        END as tipo_conta
                     , od.num_orgao
                     , tr.descricao as nom_orgao
                     , od.num_unidade
                     , tr.descricao as nom_unidade
                  FROM orcamento.conta_despesa  ocd
                     , orcamento.despesa        od
             LEFT JOIN tmp_relacao tr
                    ON od.cod_despesa = tr.cod_despesa
                   AND od.exercicio   = tr.exercicio
            INNER JOIN orcamento.recurso('''||stExercicio||''') as rec
                    ON od.cod_recurso = rec.cod_recurso
                   AND od.exercicio   = rec.exercicio
            INNER JOIN orcamento.programa op
                    ON op.exercicio    = od.exercicio
                   AND op.cod_programa = od.cod_programa
            INNER JOIN orcamento.programa_ppa_programa
                    ON programa_ppa_programa.cod_programa = od.cod_programa
                   AND programa_ppa_programa.exercicio   = od.exercicio
            INNER JOIN ppa.programa
                    ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
            INNER JOIN orcamento.pao_ppa_acao
                    ON pao_ppa_acao.num_pao = od.num_pao
                   AND pao_ppa_acao.exercicio = od.exercicio
            INNER JOIN ppa.acao
                    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
             LEFT JOIN orcamento.previsao_despesa
                    ON previsao_despesa.exercicio   = od.exercicio
                   AND previsao_despesa.cod_despesa = od.cod_despesa
                 WHERE od.exercicio    = ocd.exercicio
                   AND od.cod_conta    = ocd.cod_conta
                   AND od.cod_entidade IN ('||stCodEntidades||')
                   AND od.exercicio    = '''||stExercicio||''' 
              ORDER BY classificacao
                     , od.num_orgao
                     , od.num_unidade
               ) as tbl
        WHERE tbl.classificacao IS NOT NULL 
     GROUP BY tbl.classificacao
            , tbl.cod_reduzido
            , tbl.descricao
            , tbl.num_orgao
            , tbl.nom_orgao
            , tbl.num_unidade
            , tbl.nom_unidade
            , tbl.cod_funcao
            , tbl.cod_subfuncao
            , tbl.num_programa
            , tbl.num_acao
            , tbl.cod_recurso
            , tbl.tipo_suplementacao
            , tbl.periodo
            , tbl.vl_previsto
            , tbl.tipo_conta
     ORDER BY tbl.classificacao
            , tbl.num_orgao
            , tbl.num_unidade
            , tbl.descricao
';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_pre_empenho_despesa;

    DROP TABLE tmp_relacao;

    DROP TABLE tmp_estornado;
    DROP TABLE tmp_liquidado_estornado;
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_liquidado;

    RETURN;
END;
$$language 'plpgsql';

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
    * Script de função PLPGSQL - Arquivo de mapeamento para a função que busca os dados do recurso de alienacao do ativo
    * Data de Criação: 05/02/2015
    * @author Lisiane da Rosa Morais
    $Id:$
*/


-- CRIANDO O TYPE PARA A FUNÇÃO
/*
  CREATE TYPE tcemg_fn_recurso_alienacao_ativo AS (
    cod_entidade    INTEGER,
    cod_vinculo     INTEGER,
    rec_realizada   NUMERIC,       
    saldo_inicial   NUMERIC,        
    empenhado_per   NUMERIC,        
    pago_per        NUMERIC,        
    liquidado_per   NUMERIC
  );
*/

CREATE OR REPLACE FUNCTION tcemg.fn_recurso_alienacao_ativo(varchar,varchar,varchar,varchar) RETURNS SETOF tcemg_fn_recurso_alienacao_ativo AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    stCodEntidades  ALIAS FOR $2;
    dtInicial       ALIAS FOR $3;
    dtFinal         ALIAS FOR $4;

    stSql           VARCHAR := '';
    stNomEntidade   VARCHAR := '';
    arCodEntidade   VARCHAR[];
    reRegistro      RECORD;

    BEGIN

    arCodEntidade := string_to_array(stCodEntidades,',');

    ---------------------------------
    -- Recupera o nome das entidades
    ---------------------------------
    stSql := '
        CREATE TEMPORARY TABLE tmp_entidade AS (
            SELECT cod_entidade
                 , nom_cgm AS nom_entidade
              FROM orcamento.entidade
        INNER JOIN sw_cgm
                ON entidade.numcgm = sw_cgm.numcgm
             WHERE entidade.exercicio = ''' || stExercicio || '''
               AND entidade.cod_entidade IN (' || stCodEntidades || ')
        )      
    ';

    EXECUTE stSql;
 
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.dt_empenho as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor,
            cd.cod_estrutural as cod_estrutural,
            od.num_orgao as num_orgao,
            od.num_unidade as num_unidade--,
            --od.cod_entidade
        FROM
            orcamento.despesa           as od,
            orcamento.conta_despesa     as cd,
            empenho.pre_empenho_despesa as ped,
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                cd.cod_conta               = ped.cod_conta
            AND cd.exercicio               = ped.exercicio
            AND od.cod_despesa              = ped.cod_despesa
            AND od.exercicio                = ped.exercicio
            AND pe.exercicio               = ped.exercicio
            AND pe.cod_pre_empenho         = ped.cod_pre_empenho
            AND e.cod_entidade             IN (' || stCodEntidades || ')
            AND e.exercicio                = ''' || stExercicio || '''
            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho
            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
                   EEAI.vl_anulado as valor,
                   OCD.cod_estrutural as cod_estrutural,
                   OD.num_orgao,
                   OD.num_unidade--,
                   --OD.cod_entidade

               FROM orcamento.despesa           as OD,
                    orcamento.conta_despesa     as OCD,
                    empenho.pre_empenho_despesa as EPED,
                    empenho.pre_empenho         as EPE,
                    empenho.item_pre_empenho    as EIPE,
                    empenho.empenho_anulado_item as EEAI

               WHERE
                     OCD.cod_conta            = EPED.cod_conta
                 AND OCD.exercicio            = EPED.exercicio
                 AND EPED.exercicio           = EPE.exercicio
                 AND EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                 AND EPE.exercicio            = EIPE.exercicio
                 AND EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
                 AND EIPE.exercicio           = EEAI.exercicio
                 AND EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
                 AND EIPE.num_item            = EEAI.num_item
                 AND EEAI.exercicio           = '''|| stExercicio ||'''
                 AND EEAI.cod_entidade        IN ('||stCodEntidades||')
                 AND OD.cod_despesa           = EPED.cod_despesa
                 AND OD.exercicio             = EPED.exercicio )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLP.vl_pago as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade--,
            --OD.cod_entidade
        FROM
            orcamento.despesa               as OD,
            orcamento.conta_despesa         as OCD,
            empenho.pre_empenho_despesa     as EPED,
            empenho.empenho                 as EE,
            empenho.pre_empenho             as EPE,
            empenho.nota_liquidacao         as ENL,
            empenho.nota_liquidacao_paga    as ENLP

        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio
            AND OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio
            AND EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            AND EPED.exercicio           = EPE.exercicio
            AND EPE.exercicio            = EE.exercicio
            AND EPE.cod_pre_empenho      = EE.cod_pre_empenho
            AND EE.exercicio             = '''|| stExercicio ||'''
            AND EE.cod_entidade          IN ('||stCodEntidades||')
            AND EE.cod_empenho           = ENL.cod_empenho
            AND EE.exercicio             = ENL.exercicio_empenho
            AND EE.cod_entidade          = ENL.cod_entidade
            AND ENL.cod_nota             = ENLP.cod_nota
            AND ENL.cod_entidade         = ENLP.cod_entidade
            AND ENL.exercicio            = ENLP.exercicio )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLPA.vl_anulado as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade--,
            --OD.cod_entidade
        FROM
            orcamento.despesa                    as OD,
            orcamento.conta_despesa              as OCD,
            empenho.pre_empenho_despesa          as EPED,
            empenho.empenho                      as EE,
            empenho.pre_empenho                  as EPE,
            empenho.nota_liquidacao              as ENL,
            empenho.nota_liquidacao_paga         as ENLP,
            empenho.nota_liquidacao_paga_anulada as ENLPA
        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio
            AND OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio
            AND EPED.exercicio           = EPE.exercicio
            AND EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            AND EPE.exercicio            = EE.exercicio
            AND EPE.cod_pre_empenho      = EE.cod_pre_empenho
            AND EE.cod_entidade          IN ('||stCodEntidades||')
            AND EE.exercicio             = '''|| stExercicio ||'''
            AND EE.cod_empenho           = ENL.cod_empenho
            AND EE.exercicio             = ENL.exercicio_empenho
            AND EE.cod_entidade          = ENL.cod_entidade
            AND ENL.exercicio            = ENLP.exercicio
            AND ENL.cod_nota             = ENLP.cod_nota
            AND ENL.cod_entidade         = ENLP.cod_entidade
            AND ENLP.cod_entidade        = ENLPA.cod_entidade
            AND ENLP.cod_nota            = ENLPA.cod_nota
            AND ENLP.exercicio           = ENLPA.exercicio
            AND ENLP.timestamp           = ENLPA.timestamp )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
                , ore.cod_recurso    as recurso
                , ore.cod_entidade   as cod_entidade
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE
                    ore.exercicio       = '|| quote_literal(stExercicio) ||'
                AND ore.cod_entidade    IN (' || stCodEntidades || ')
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
                , ore.cod_recurso    as recurso
                , ore.cod_entidade   as cod_entidade
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                ore.exercicio           = '|| quote_literal(stExercicio) ||'
                AND ore.cod_entidade    IN (' || stCodEntidades || ')
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
                AND lote.tipo           = lan.tipo ) ';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
                SELECT
                    nl.dt_liquidacao as dataConsulta,
                    nli.vl_total as valor,
                    cd.cod_estrutural as cod_estrutural,
                    od.num_orgao as num_orgao,
                    od.num_unidade as num_unidade--,
                    --od.cod_entidade
                FROM
                    orcamento.despesa             as od,
                    orcamento.conta_despesa       as cd,
                    empenho.pre_empenho_despesa   as ped,
                    empenho.pre_empenho           as pe,
                    empenho.empenho               as e,
                    empenho.nota_liquidacao_item  as nli,
                    empenho.nota_liquidacao       as nl
                WHERE
                        cd.cod_conta               = ped.cod_conta
                    AND cd.exercicio               = ped.exercicio
                    AND od.cod_despesa             = ped.cod_despesa
                    AND od.exercicio               = ped.exercicio
                    AND pe.exercicio               = ped.exercicio
                    AND pe.cod_pre_empenho         = ped.cod_pre_empenho
                    AND e.cod_entidade             IN (' || stCodEntidades || ')
                    AND e.exercicio                = ''' || stExercicio || '''
                    AND e.exercicio                = pe.exercicio
                    AND e.cod_pre_empenho          = pe.cod_pre_empenho
                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho
                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT
            to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLIA.vl_anulado as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao,
            OD.num_unidade--,
            --OD.cod_entidade

        FROM orcamento.despesa                    as OD,
             orcamento.conta_despesa              as OCD,
             empenho.pre_empenho_despesa          as EPED,
             empenho.pre_empenho                  as EPE,
             empenho.empenho                      as EE,
             empenho.nota_liquidacao              as ENL,
             empenho.nota_liquidacao_item         as ENLI,
             empenho.nota_liquidacao_item_anulado as ENLIA

        WHERE OCD.cod_conta            = EPED.cod_conta
          AND OCD.exercicio            = EPED.exercicio
          AND EPE.cod_pre_empenho      = EE.cod_pre_empenho
          AND EPE.exercicio            = EE.exercicio
          AND EE.exercicio             = ENL.exercicio_empenho
          AND EE.cod_entidade          = ENL.cod_entidade
          AND EE.cod_empenho           = ENL.cod_empenho
          AND EE.cod_entidade          IN ('||stCodEntidades||')
          AND EE.exercicio             = '''|| stExercicio || '''
          AND ENL.exercicio            = ENLI.exercicio
          AND ENL.cod_nota             = ENLI.cod_nota
          AND ENL.cod_entidade         = ENLI.cod_entidade
          AND ENLI.exercicio           = ENLIA.exercicio
          AND ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
          AND ENLI.num_item            = ENLIA.num_item
          AND ENLI.cod_entidade        = ENLIA.cod_entidade
          AND ENLI.exercicio_item      = ENLIA.exercicio_item
          AND ENLI.cod_nota            = ENLIA.cod_nota
          AND OD.cod_despesa           = EPED.cod_despesa
          AND OD.exercicio             = EPED.exercicio
          AND OD.cod_entidade          IN ('||stCodEntidades||')
          AND EPED.exercicio           = EPE.exercicio
          AND EPED.cod_pre_empenho     = EPE.cod_pre_empenho )';
        EXECUTE stSql;

stSql := '
    --CRIA TABELA TEMPORÁRIA COM TODOS AS DESPESAS DA DESPESA, SETA ELAS COMO MÃE
    CREATE TEMPORARY TABLE tmp_pre_empenho_despesa AS
        SELECT
                  od.exercicio
                 ,cod_conta
                 ,cod_despesa
                 ,cast(''M'' as varchar) as tipo_conta
                 --,od.cod_entidade
        FROM
                 orcamento.despesa as od
                 JOIN orcamento.recurso('''|| stExercicio ||''') as rec
                   ON (  rec.exercicio   = od.exercicio
                  AND rec.cod_recurso = od.cod_recurso )
        WHERE
                 cod_entidade IN ('||stCodEntidades||')';


EXECUTE stSql;

stSql := '
     --ATUALIZA O TOPO DA SOMA PARA TODOS OS REGISTRO QUE ESTIVEREM NA TABELA PRE_EMPENHO
        UPDATE tmp_pre_empenho_despesa SET tipo_conta=''D''
            WHERE   exercicio||''-''||cod_conta IN (
                        SELECT  exercicio||''-''||cod_conta
                        FROM    empenho.pre_empenho_despesa
                    )';
EXECUTE stSql;

stSql := '
     --INSERE NA TABELA TEMPORARIA OS REGISTROS RESUTADOS DE UM SELECT
     --ESTE SELECT PREVEM DA TABELA PRE_EMPENHO_DESPESA ONDE TODOS OS REGISTROS SÃO SETADOS COMO FILHAS
        INSERT INTO tmp_pre_empenho_despesa
            SELECT
                    ped.exercicio
                    ,ped.cod_conta
                    ,ped.cod_despesa
                    ,cast(''F'' as varchar) as tipo_conta
            FROM
                    empenho.pre_empenho_despesa ped,
                    empenho.pre_empenho pe,
                    empenho.empenho e
            WHERE NOT EXISTS ( SELECT 1
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

stSql := '
    CREATE TEMPORARY TABLE tmp_despesa AS
        SELECT
                 cd.cod_estrutural,
                 cd.exercicio,
                 cd.cod_conta,
                 cd.descricao,
                 CASE WHEN tmp.tipo_conta> ''0'' THEN
                    tmp.tipo_conta
                 ELSE
                    cast(''M'' as varchar)
                 END as tipo_conta
        FROM
                 orcamento.conta_despesa as cd
                 LEFT OUTER JOIN tmp_pre_empenho_despesa as tmp ON (
                    cd.cod_conta = tmp.cod_conta AND
                    cd.exercicio = tmp.exercicio
                 )
       WHERE cd.exercicio = '''|| stExercicio ||'''
        GROUP BY
            cd.cod_estrutural,
            cd.exercicio,
            cd.cod_conta,
            cd.descricao,
            tipo_conta ';
EXECUTE stSql;

stSql := '
    CREATE TEMPORARY TABLE tmp_relacao AS
        SELECT
            --SELECIONA ORCAMENTO.DESPESA
            od.exercicio        as exercicio,

            --SELECIONA EMPENHO.PRE_EMPENHO_DESPESA
            eped.tipo_conta     as tipo_conta,

            --SELECIONA ORCAMENT.CONTA_DESPESA
            ocd.cod_estrutural  as classificacao,
            publico.fn_mascarareduzida(ocd.cod_estrutural) as cod_reduzido,
            ocd.descricao       as descricao ,

            --SELECIONA ORCAMENTO.SUPLEMENTACOES_SUPLEMENTADA
            sum( oss.valor )          as suplementacoes,

            --SELECIONA ORCAMENTO.SUPLEMENTACOES_REDUZIDA
            sum( osr.valor )          as reducoes

            , 0 as num_orgao
            , eped.tipo_conta as nom_orgao
            , 0 as num_unidade
            , eped.tipo_conta AS nom_unidade
            , od.cod_entidade
        FROM
            tmp_pre_empenho_despesa eped,
            orcamento.conta_despesa ocd,
            orcamento.despesa od
                LEFT JOIN (
                    SELECT
                        cod_despesa as cod_despesa,
                        max(oss1.exercicio) as exercicio,
                        sum(valor) as valor
                    FROM
                        orcamento.suplementacao_suplementada as oss1,
                        orcamento.suplementacao as os
                    WHERE
                        os.cod_suplementacao = oss1.cod_suplementacao AND
                        os.exercicio         = oss1.exercicio AND
                        os.cod_suplementacao || os.exercicio IN (
                            SELECT
                                cod_suplementacao || cl.exercicio
                            FROM
                                contabilidade.transferencia_despesa ctd,
                                contabilidade.lote cl
                            WHERE
                                ctd.exercicio = cl.exercicio AND
                                ctd.cod_lote  = cl.cod_lote AND
                                ctd.tipo      = cl.tipo AND
                                ctd.cod_entidade = cl.cod_entidade AND
                                cl.dt_lote between  to_date('''|| dtInicial ||''',''dd/mm/yyyy'') And to_date('''|| dtFinal ||''',''dd/mm/yyyy'')
                         )
                         
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa3
                                          WHERE o_sa3.cod_suplementacao = os.cod_suplementacao
                                            AND o_sa3.exercicio         = os.exercicio
                                            AND o_sa3.exercicio         = ''' || stExercicio  || '''
                        )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa4
                                          WHERE o_sa4.cod_suplementacao_anulacao = os.cod_suplementacao
                                            AND o_sa4.exercicio                  = os.exercicio
                                            AND o_sa4.exercicio                  = ''' || stExercicio  || '''
                        )

                    GROUP BY
                        oss1.exercicio, oss1.cod_despesa
                ) AS oss ON (
                    od.cod_despesa = oss.cod_despesa AND
                    od.exercicio   = oss.exercicio
                )
                LEFT JOIN (
                    SELECT
                        cod_despesa,max(osr1.exercicio) as exercicio, sum(valor) as valor
                    FROM
                        orcamento.suplementacao_reducao as osr1,
                        orcamento.suplementacao as os
                    WHERE
                        os.cod_suplementacao = osr1.cod_suplementacao AND
                        os.exercicio         = osr1.exercicio AND
                        os.cod_suplementacao || os.exercicio IN (
                        
                        
                            SELECT
                                cod_suplementacao || cl.exercicio
                            FROM
                                contabilidade.transferencia_despesa ctd,
                                contabilidade.lote cl
                            WHERE
                                ctd.exercicio = cl.exercicio AND
                                ctd.cod_lote  = cl.cod_lote AND
                                ctd.tipo      = cl.tipo AND
                                ctd.cod_entidade = cl.cod_entidade AND
                                cl.dt_lote between to_date('''||  dtInicial ||''',''dd/mm/yyyy'') And to_date('''|| dtFinal ||''',''dd/mm/yyyy'')
                        )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa
                                          WHERE o_sa.cod_suplementacao = os.cod_suplementacao
                                            AND o_sa.exercicio         = os.exercicio
                                            AND o_sa.exercicio         = ''' || stExercicio  || '''
                        )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa2
                                          WHERE o_sa2.cod_suplementacao_anulacao = os.cod_suplementacao
                                            AND o_sa2.exercicio                  = os.exercicio
                                            AND o_sa2.exercicio                  = ''' || stExercicio  || '''
                        )

                    GROUP BY
                        osr1.exercicio,cod_despesa
                ) AS osr ON (
                    od.cod_despesa        = osr.cod_despesa AND
                    od.exercicio          = osr.exercicio
                )
                JOIN orcamento.recurso('''|| stExercicio ||''') as rec
                ON ( od.cod_recurso = rec.cod_recurso
                 AND od.exercicio   = rec.exercicio )
        WHERE
            eped.cod_despesa      = od.cod_despesa      AND
            eped.exercicio        = od.exercicio        AND

            eped.cod_conta        = ocd.cod_conta       AND
            eped.exercicio        = ocd.exercicio       AND

            od.cod_entidade       IN ('||stCodEntidades||') AND

            od.exercicio          = ''' || stExercicio  || '''
        GROUP BY
            ocd.cod_estrutural,
            od.exercicio ,
            ocd.descricao,
            eped.tipo_conta,
            od.cod_entidade
        ORDER BY
            ocd.cod_estrutural
';

    EXECUTE stSql;

stSql := '
CREATE TEMPORARY TABLE tmp_relatorio AS
    SELECT sum( tbl.saldo_inicial ) as saldo_inicial,
           tbl.empenhado_per,
           tbl.pago_per,
           tbl.liquidado_per,
           0.00 AS rec_realizada,
           tbl.cod_entidade,
           tbl.classificacao
    FROM (
        SELECT
            tbl.cod_entidade,
            tbl.classificacao,
            tbl.cod_despesa,
            coalesce((tbl.saldo_inicial),0.00) as saldo_inicial,
-- Empenhado
            coalesce(orcamento.fn_consolidado_empenhado(''' || dtInicial || ''', ''' || dtFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00) as empenhado_per,
-- Pago
            (coalesce(orcamento.fn_consolidado_pago(''' || dtInicial || ''', ''' || dtFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_estornado(''' || dtInicial || ''', ''' || dtFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00)) as pago_per,
-- Liquidado
            (coalesce(orcamento.fn_consolidado_liquidado(''' || dtInicial || ''', ''' || dtFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_liquidado_estornado(''' || dtInicial || ''', ''' || dtFinal || ''', publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00)) as liquidado_per
        FROM
            tmp_despesa as d
            INNER JOIN (
            SELECT
                CASE WHEN tr.classificacao IS NOT NULL THEN
                    tr.classificacao
                ELSE
                    ocd.cod_estrutural
                END as classificacao,
                od.cod_despesa,
                od.cod_entidade,
                coalesce((od.vl_original),0.00) as  saldo_inicial,
                0 as num_orgao,
                0 as num_unidade
            FROM
                 orcamento.despesa        od
                 JOIN orcamento.recurso('''|| stExercicio ||''') as rec
                 ON ( rec.cod_recurso = od.cod_recurso  
                  AND rec.exercicio   = od.exercicio )
                ,orcamento.conta_despesa  ocd
                    LEFT JOIN tmp_relacao tr ON(
                        ocd.cod_estrutural = tr.classificacao AND
                        ocd.exercicio   = tr.exercicio
                    )
            WHERE
                od.exercicio          = ocd.exercicio       AND
                od.cod_conta          = ocd.cod_conta       AND
                od.cod_entidade       IN ('||stCodEntidades||') AND
                od.exercicio          = ''' || stExercicio || '''
            ORDER BY
                classificacao,
                od.num_orgao,
                od.num_unidade
        ) as tbl ON (
            tbl.classificacao = d.cod_estrutural
        )
        WHERE d.cod_estrutural IS NOT NULL
          AND d.cod_estrutural like ''4.%''

        GROUP BY
            d.cod_estrutural,
            tbl.num_orgao,
            tbl.num_unidade,
            tbl.cod_entidade,
            tbl.saldo_inicial,
            tbl.classificacao,
            tbl.cod_despesa
        ORDER BY
            d.cod_estrutural
        ) as tbl
    GROUP BY tbl.cod_entidade,
             tbl.empenhado_per,
             tbl.pago_per,
             tbl.liquidado_per,
             tbl.classificacao

    UNION

    SELECT 0.00 AS saldo_inicial,
           0.00 AS empenhado_per,
           0.00 AS pago_per,
           0.00 AS liquidado_per,
           tbl.arrecadado_periodo AS rec_realizada,
           tbl.cod_entidade,
           tbl.cod_estrutural AS classificacao

      FROM (
          SELECT
              ocr.cod_estrutural as cod_estrutural,
              r.cod_receita  as receita,
              r.cod_entidade,
              orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                       ,'|| quote_literal(dtInicial) ||'
                                                       ,'|| quote_literal(dtFinal)   ||'
              ) as arrecadado_periodo
          FROM
              orcamento.conta_receita ocr
                  INNER JOIN orcamento.receita as r ON
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

          ORDER BY ocr.cod_estrutural
          ) as tbl

      WHERE orcamento.fn_movimento_balancete_receita( '|| quote_literal(stExercicio) ||'
                                                      ,publico.fn_mascarareduzida(tbl.cod_estrutural)
                                                      ,'|| quote_literal(stCodEntidades) ||'
                                                      ,'|| quote_literal(dtInicial) ||'
                                                      ,'|| quote_literal(dtFinal) ||'
                                                    ) = true
        AND tbl.cod_estrutural IS NOT NULL
        AND tbl.cod_estrutural like ''2.%''

      GROUP BY tbl.cod_estrutural,
               tbl.cod_entidade,
               tbl.receita,
               tbl.arrecadado_periodo
    ';

    EXECUTE stSql;

-- Faz as consultas para cada entidade 
    ---------------------------------------
    FOR i IN 1..ARRAY_UPPER(arCodEntidade,1) LOOP
        stCodEntidades := arCodEntidade[i];
        SELECT nom_entidade 
          INTO stNomEntidade
          FROM tmp_entidade
         WHERE cod_entidade::VARCHAR = arCodEntidade[i];

        stSql := '
                    SELECT 
                         cod_entidade,
                         0 AS cod_vinculo,
                         COALESCE(SUM(rec_realizada),0.00)*-1 AS rec_realizada,
                         COALESCE(SUM(saldo_inicial),0.00) AS saldo_inicial,
                         COALESCE(SUM(empenhado_per),0.00) AS empenhado_per,
                         COALESCE(SUM(pago_per),0.00) AS pago_per,
                         COALESCE(SUM(liquidado_per),0.00) AS liquidado_per
                    FROM
                        tmp_relatorio
                    WHERE cod_entidade = ' || arCodEntidade[i] || '
                    GROUP BY cod_entidade
                  ';

        FOR reRegistro IN EXECUTE stSql
          LOOP
            IF (stNomEntidade ILIKE '%prefeitura%') THEN reRegistro.cod_vinculo := 1;
              ELSIF (stNomEntidade ILIKE '%camara%' OR stNomEntidade ILIKE '%câmara%') THEN reRegistro.cod_vinculo := 3;
              ELSE reRegistro.cod_vinculo := 2;
            END IF;
            RETURN NEXT reRegistro;
          END LOOP;
    END LOOP;

    DROP TABLE tmp_entidade;
    DROP TABLE tmp_pre_empenho_despesa;
    DROP TABLE tmp_despesa;
    DROP TABLE tmp_relacao;
    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_estornado;
    DROP TABLE tmp_liquidado_estornado;
    DROP TABLE tmp_valor;
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_liquidado;

    RETURN;
END;

$$ language 'plpgsql';
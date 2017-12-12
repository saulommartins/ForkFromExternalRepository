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
* $Id: variacoesQualitativas.plsql 64402 2016-02-16 20:59:49Z michel $
*
* Casos de uso: uc-02.01.23
*/

CREATE OR REPLACE FUNCTION variacoesQualitativas(varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stExercicioAnterior     ALIAS FOR $6;

    stSql                   VARCHAR := '';
    stSqlAux                VARCHAR := '';
    dataInicio              VARCHAR := '';
    dataFim                 VARCHAR := '';
    stDataInicioAnterior    VARCHAR := '';
    stDataFimAnterior       VARCHAR := '';
    reRegistro          RECORD;

BEGIN
    dataInicio := '01/01/' || stExercicio;
    
    stDataInicioAnterior := '01/01/' || stExercicioAnterior;
    stDataFimAnterior    := '31/12/' || stExercicioAnterior;

    IF stExercicio >= TO_CHAR(now(), 'yyyy') THEN
        dataFim := TO_CHAR(NOW(), 'dd/mm/yyyy');
    ELSE
        dataFim := '31/12/' || stExercicio;
    END IF;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.dt_empenho as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor,
            cd.cod_estrutural as cod_estrutural,
            od.num_orgao as num_orgao,
            od.num_unidade as num_unidade
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
            
            And od.cod_despesa              = ped.cod_despesa
            AND od.exercicio                = ped.exercicio
            
            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho
            
            And e.cod_entidade             IN (' || stCodEntidades || ')
            And e.exercicio                IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
            
            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho
            
            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho';
            
          stSql := stSql || ' ' || stFiltro || ' )';
        EXECUTE stSql;
        
    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta, EEAI.vl_anulado as valor, OCD.cod_estrutural as cod_estrutural, OD.num_orgao, OD.num_unidade
               from orcamento.despesa           as OD,
                    orcamento.conta_despesa     as OCD,
                    empenho.pre_empenho_despesa as EPED,
                    empenho.pre_empenho         as EPE,
                    empenho.item_pre_empenho    as EIPE,
                    empenho.empenho_anulado_item as EEAI
                    
               Where
                     OCD.cod_conta            = EPED.cod_conta
                 AND OCD.exercicio            = EPED.exercicio
                 And EPED.exercicio           = EPE.exercicio
                 And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                 And EPE.exercicio            = EIPE.exercicio
                 And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
                 And EIPE.exercicio           = EEAI.exercicio
                 And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
                 And EIPE.num_item            = EEAI.num_item
                 And EEAI.exercicio           IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
                 And EEAI.cod_entidade        IN ('||stCodEntidades||')
                 And OD.cod_despesa           = EPED.cod_despesa
                 AND OD.exercicio             = EPED.exercicio';
                 
              stSql := stSql || ' ' || stFiltro || ' )';
        EXECUTE stSql;
        
    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLP.vl_pago as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade
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
            
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            And EPED.exercicio           = EPE.exercicio
            
            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho
            
            And EE.exercicio             IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
            And EE.cod_entidade          IN ('||stCodEntidades||')
            
            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade
            
            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio ';
            
        stSql := stSql || ' ' || stFiltro ||' )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLPA.vl_anulado as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade
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
            And OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio
            
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            
            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho
            
            And EE.cod_entidade          IN ('||stCodEntidades||')
            And EE.exercicio             IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
            
            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade
            
            And ENL.exercicio            = ENLP.exercicio
            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            
            And ENLP.cod_entidade        = ENLPA.cod_entidade
            And ENLP.cod_nota            = ENLPA.cod_nota
            And ENLP.exercicio           = ENLPA.exercicio
            And ENLP.timestamp           = ENLPA.timestamp ';
            
        stSql := stSql || ' ' || stFiltro || ' )';
        EXECUTE stSql;
        
    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
                SELECT
                    nl.dt_liquidacao as dataConsulta,
                    nli.vl_total as valor,
                    cd.cod_estrutural as cod_estrutural,
                    od.num_orgao as num_orgao,
                    od.num_unidade as num_unidade
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
                    
                    And od.cod_despesa              = ped.cod_despesa
                    AND od.exercicio                = ped.exercicio
                    
                    And pe.exercicio               = ped.exercicio
                    And pe.cod_pre_empenho         = ped.cod_pre_empenho
                    
                    And e.cod_entidade             IN (' || stCodEntidades || ')
                    And e.exercicio                IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
                    
                    AND e.exercicio                = pe.exercicio
                    AND e.cod_pre_empenho          = pe.cod_pre_empenho
                    
                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho
                    
                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade';
                    
        stSql := stSql || ' ' || stFiltro || ')';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT
            to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta, ENLIA.vl_anulado as valor, OCD.cod_estrutural as cod_estrutural, OD.num_orgao, OD.num_unidade
        FROM orcamento.despesa                    as OD,
             orcamento.conta_despesa              as OCD,
             empenho.pre_empenho_despesa          as EPED,
             empenho.pre_empenho                  as EPE,
             empenho.empenho                      as EE,
             empenho.nota_liquidacao              as ENL,
             empenho.nota_liquidacao_item         as ENLI,
             empenho.nota_liquidacao_item_anulado as ENLIA
             
        WHERE OCD.cod_conta               = EPED.cod_conta
          AND OCD.exercicio               = EPED.exercicio
          And EPE.cod_pre_empenho         = EE.cod_pre_empenho
          And EPE.exercicio               = EE.exercicio
          
          And EE.exercicio                = ENL.exercicio_empenho
          And EE.cod_entidade             = ENL.cod_entidade
          And EE.cod_empenho              = ENL.cod_empenho
          And EE.cod_entidade             IN ('||stCodEntidades||')
          And EE.exercicio                IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
          
          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade
          And ENLI.exercicio              = ENLIA.exercicio
          And ENLI.cod_pre_empenho        = ENLIA.cod_pre_empenho
          And ENLI.num_item               = ENLIA.num_item
          And ENLI.cod_entidade           = ENLIA.cod_entidade
          And ENLI.exercicio_item         = ENLIA.exercicio_item
          And ENLI.cod_nota               = ENLIA.cod_nota
          And OD.cod_despesa              = EPED.cod_despesa
          AND OD.exercicio                = EPED.exercicio
          And OD.cod_entidade             IN ('||stCodEntidades||')
          And EPED.exercicio              = EPE.exercicio
          And EPED.cod_pre_empenho        = EPE.cod_pre_empenho ';
          
        stSql := stSql || ' ' || stFiltro || ' )';
        EXECUTE stSql;
        
stSql := '
    --CRIA TABELA TEMPORÁRIA COM TODOS AS DESPESAS DA DESPESA, SETA ELAS COMO MÃE
    CREATE TEMPORARY TABLE tmp_pre_empenho_despesa AS
        SELECT
                  od.exercicio
                 ,cod_conta
                 ,cod_despesa
                 ,cast(''M'' as varchar) as tipo_conta
        FROM
                 orcamento.despesa as od
                 JOIN orcamento.recurso(' || quote_literal(stExercicio) || ') as rec
                      ON (  rec.exercicio   = od.exercicio
                        AND rec.cod_recurso = od.cod_recurso )
        WHERE
                 cod_entidade IN ('||stCodEntidades||')';
                 
        stSql := stSql || ' ' ||  stFiltro;
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
                 CASE WHEN tmp.tipo_conta > ''0'' THEN
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
       WHERE cd.exercicio = ' || quote_literal(stExercicio) || '
        GROUP BY
            cd.cod_estrutural,
            cd.exercicio,
            cd.cod_conta,
            cd.descricao,
            tipo_conta
        ';
        
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
    ';
    
    stSql := stSql || '
            , 0 as num_orgao
            , eped.tipo_conta as nom_orgao
        ';
        
    stSql := stSql || '
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
                                cl.dt_lote between  to_date(' || quote_literal(dataInicio) || ',''dd/mm/yyyy'') And to_date(' || quote_literal(stDataFinal) || ', ''dd/mm/yyyy'')
                         )
                         
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa3
                                          WHERE o_sa3.cod_suplementacao = os.cod_suplementacao
                                            AND o_sa3.exercicio         = os.exercicio
                                            AND o_sa3.exercicio         = ' || quote_literal(stExercicio)  || '
                        )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa4
                                          WHERE o_sa4.cod_suplementacao_anulacao = os.cod_suplementacao
                                            AND o_sa4.exercicio                  = os.exercicio
                                            AND o_sa4.exercicio                  = ' || quote_literal(stExercicio)  || '
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
                                cl.dt_lote between to_date(' ||  quote_literal(dataInicio) || ', ''dd/mm/yyyy'') And to_date(' || quote_literal(stDataFinal) || ', ''dd/mm/yyyy'')
                        )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa
                                          WHERE o_sa.cod_suplementacao = os.cod_suplementacao
                                            AND o_sa.exercicio         = os.exercicio
                                            AND o_sa.exercicio         = ' || quote_literal(stExercicio)  || '
                        )
                        AND NOT EXISTS ( SELECT 1
                                           FROM orcamento.suplementacao_anulada o_sa2
                                          WHERE o_sa2.cod_suplementacao_anulacao = os.cod_suplementacao
                                            AND o_sa2.exercicio                  = os.exercicio
                                            AND o_sa2.exercicio                  = ' || quote_literal(stExercicio) || '
                        )
                        
                    GROUP BY
                        osr1.exercicio,cod_despesa
                ) AS osr ON (
                    od.cod_despesa        = osr.cod_despesa AND
                    od.exercicio          = osr.exercicio
                )
                JOIN orcamento.recurso(' || quote_literal(stExercicio) || ') as rec
                  ON ( od.cod_recurso = rec.cod_recurso
                 AND od.exercicio   = rec.exercicio )
        WHERE
            eped.cod_despesa      = od.cod_despesa      AND
            eped.exercicio        = od.exercicio        AND
            
            eped.cod_conta        = ocd.cod_conta       AND
            eped.exercicio        = ocd.exercicio       AND
            
            od.cod_entidade       IN ('||stCodEntidades||') AND
            
            od.exercicio          = ' || quote_literal(stExercicio)  || ' ' || stFiltro;

stSql := stSql || ' ' || stFiltro || '
        GROUP BY
            ocd.cod_estrutural,
            od.exercicio ,
            ocd.descricao,
            eped.tipo_conta
            
        ORDER BY
            ocd.cod_estrutural
    ';
    
    EXECUTE stSql;

stSql := '
CREATE TEMPORARY TABLE tmp_relatorio AS
    SELECT tbl.classificacao
          ,tbl.cod_reduzido
          ,tbl.descricao
          ,tbl.num_orgao
          ,tbl.nom_orgao
          ,tbl.num_unidade
          ,tbl.nom_unidade
          ,sum( tbl.saldo_inicial ) as saldo_inicial
          ,tbl.suplementacoes
          ,tbl.reducoes
          ,tbl.empenhado_per
          ,tbl.empenhado_ano
          ,tbl.empenhado_anterior
          ,tbl.anulado_per
          ,tbl.anulado_ano
          ,tbl.anulado_anterior
          ,tbl.pago_per
          ,tbl.pago_ano
          ,tbl.liquidado_per
          ,tbl.liquidado_ano
          ,tbl.tipo_conta
    FROM (
        SELECT
            d.cod_estrutural as classificacao,
            tbl.cod_despesa,
            publico.fn_mascarareduzida(d.cod_estrutural) as cod_reduzido,
            d.descricao,
            tbl.num_orgao,
            tbl.nom_orgao,
            tbl.num_unidade,
            tbl.nom_unidade,
            coalesce((tbl.saldo_inicial),0.00) as saldo_inicial,
            coalesce((tbl.suplementacoes),0.00) as suplementacoes,
            coalesce((tbl.reducoes),0.00) as reducoes,
-- Empenhado
            coalesce(orcamento.fn_consolidado_empenhado(' || quote_literal(stDataInicial) || ', ' || quote_literal(stDataFinal) || ', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) as empenhado_per,
            coalesce(orcamento.fn_consolidado_empenhado(' || quote_literal(dataInicio) || ', ' || quote_literal(stDataFinal) || ', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) as empenhado_ano,
-- Empenhado Ano Anterior
            coalesce(orcamento.fn_consolidado_empenhado(' || quote_literal(stDataInicioAnterior) || ', ' || quote_literal(stDataFimAnterior) || ', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) as empenhado_anterior,
-- Anulado
            coalesce(orcamento.fn_consolidado_anulado(' || quote_literal(stDataInicial) || ', ' || quote_literal(stDataFinal) || ', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) as anulado_per,
            coalesce(orcamento.fn_consolidado_anulado(' || quote_literal(dataInicio) || ', ' || quote_literal(stDataFinal) || ', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) as anulado_ano,
-- Anulado Ano Anterior
            coalesce(orcamento.fn_consolidado_anulado(' || quote_literal(stDataInicioAnterior) || ', ' || quote_literal(stDataFimAnterior) || ', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) as anulado_anterior,
-- Pago
            coalesce(orcamento.fn_consolidado_pago('''||stDataInicial||''', '''||stDataFinal||''', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_estornado('''||stDataInicial||''', '''||stDataFinal||''',
                publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00) as pago_per,
            coalesce(orcamento.fn_consolidado_pago(' || quote_literal(dataInicio) || ', ' || quote_literal(stDataFinal) || ',publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_estornado(' || quote_literal(dataInicio) || ', ' || quote_literal(stDataFinal) || ',
                publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00) as pago_ano,
-- Liquidado
            (coalesce(orcamento.fn_consolidado_liquidado(' || quote_literal(stDataInicial) || ', ' || quote_literal(stDataFinal) || ', publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_liquidado_estornado(' || quote_literal(stDataInicial) || ', ' || quote_literal(stDataFinal) || ',
                publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00)) as liquidado_per,
            (coalesce(orcamento.fn_consolidado_liquidado(' || quote_literal(dataInicio) || ', ' || quote_literal(stDataFinal) || ',publico.fn_mascarareduzida(d.cod_estrutural),
                tbl.num_orgao,tbl.num_unidade),0.00) - coalesce(orcamento.fn_consolidado_liquidado_estornado(' || quote_literal(dataInicio) || ', ' || quote_literal(stDataFinal) || ',
                publico.fn_mascarareduzida(d.cod_estrutural),tbl.num_orgao,tbl.num_unidade),0.00)) as liquidado_ano,
            tbl.tipo_conta
            
        FROM
            tmp_despesa as d
            LEFT OUTER JOIN
            (
            SELECT
                CASE WHEN tr.classificacao IS NOT NULL THEN
                    tr.classificacao
                ELSE
                    ocd.cod_estrutural
                END as classificacao,
    
                CASE WHEN tr.cod_reduzido IS NOT NULL THEN
                    tr.cod_reduzido
                ELSE
                    publico.fn_mascarareduzida(ocd.cod_estrutural)
                END as cod_reduzido,
    
                CASE WHEN tr.descricao IS NOT NULL THEN
                    tr.descricao
                ELSE
                    ocd.descricao
                END as descricao,
    
                od.cod_despesa,
    
                coalesce((od.vl_original),0.00) as  saldo_inicial,
                coalesce((tr.suplementacoes),0.00) as suplementacoes,
                coalesce((tr.reducoes),0.00) as reducoes,
    
                CASE WHEN tr.tipo_conta IS NOT NULL THEN
                    tr.tipo_conta
                ELSE
                    ''M''
                END as tipo_conta,
                0 as num_orgao,
                tr.descricao as nom_orgao,
                0 as num_unidade,
                tr.descricao as nom_unidade
                
            FROM
                 orcamento.despesa        od
                 JOIN orcamento.recurso(' || quote_literal(stExercicio) || ') as rec
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
                od.exercicio          = ' || quote_literal(stExercicio) || ' ' || stFiltro || '
            ORDER BY
                classificacao,
                od.num_orgao,
                od.num_unidade
        ) as tbl ON (
            tbl.classificacao = d.cod_estrutural
        )
        WHERE d.cod_estrutural IS NOT NULL
        
        GROUP BY
            d.cod_estrutural,
            tbl.cod_despesa,
            d.descricao,
            tbl.num_orgao,
            tbl.nom_orgao,
            tbl.num_unidade,
            tbl.nom_unidade,
            tbl.tipo_conta
           ,tbl.suplementacoes
           ,tbl.reducoes
           ,tbl.saldo_inicial
        ORDER BY
            d.cod_estrutural,
            tbl.num_orgao,
            tbl.num_unidade,
            d.descricao
    ) as tbl
    GROUP BY tbl.classificacao
            ,tbl.cod_reduzido
            ,tbl.descricao
            ,tbl.num_orgao
            ,tbl.nom_orgao
            ,tbl.num_unidade
            ,tbl.nom_unidade
            ,tbl.suplementacoes
            ,tbl.reducoes
            ,tbl.empenhado_per
            ,tbl.empenhado_ano
            ,tbl.empenhado_anterior
            ,tbl.anulado_per
            ,tbl.anulado_ano
            ,tbl.anulado_anterior
            ,tbl.pago_per
            ,tbl.pago_ano
            ,tbl.liquidado_per
            ,tbl.liquidado_ano
            ,tbl.tipo_conta
    ';
    
    EXECUTE stSql;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE
                    ore.cod_entidade    IN ('|| stCodEntidades ||')
                AND ore.exercicio       IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
                
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
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
                
            WHERE
                    ore.cod_entidade    IN('|| stCodEntidades ||')
                AND ore.exercicio       IN ('|| quote_literal(stExercicio) ||',' || quote_literal(stExercicioAnterior) || ')
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
        
    stSqlAux := '( SELECT
                        ocr.cod_estrutural as cod_estrutural,
                        orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                             ,'|| quote_literal(stDataInicial) ||'
                                                             ,'|| quote_literal(stDataFinal)   ||'
                        ) as arrecadado_periodo,
                        
                        orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                             ,'|| quote_literal(stDataInicioAnterior) ||'
                                                             ,'|| quote_literal(stDataFimAnterior)   ||'
                        ) as arrecadado_anterior,
                        
                        ocr.cod_estrutural as codigo_estrutural
                FROM
                     orcamento.conta_receita ocr
     LEFT OUTER JOIN orcamento.receita as r
                  ON ocr.exercicio = r.exercicio
                 AND ocr.cod_conta = r.cod_conta
                 AND r.cod_entidade IN ('|| stCodEntidades ||')
                 AND r.exercicio = '|| quote_literal(stExercicio) ||'
           LEFT JOIN orcamento.recurso('|| quote_literal(stExercicio) ||') as rec
                  ON rec.cod_recurso = r.cod_recurso
                 AND rec.exercicio   = r.exercicio
               WHERE
                     ocr.cod_conta = ocr.cod_conta
                 AND ocr.exercicio =  '|| quote_literal(stExercicio) ||'
                    ';
    stSqlAux := stSqlAux ||' '|| stFiltro ||' ORDER BY ocr.cod_estrutural
    )';
    
    
    stSql := 'SELECT
                        classe,
                        grupo,
                        CAST(descricao AS VARCHAR) as descricao,
                        SUM(saldo_atual) as saldo_atual,
                        SUM(saldo_anterior) as saldo_anterior,
                        borda,
                        linha
                FROM (
                        --VARIAÇÕES PATRIMONIAIS AUMENTATIVAS
                        SELECT ''4.4.0.0.00.00.00.00.00''::varchar AS classe
                             , coalesce((empenhado_per - anulado_per),0.00) as saldo_atual';
                        IF stExercicio::INTEGER > 2013 THEN
                             stSql := stSql || ', coalesce((empenhado_anterior - anulado_anterior),0.00) AS saldo_anterior';
                        ELSE
                             stSql := stSql || ', 0.00 AS saldo_anterior';
                        END IF;
                        
                        stSql := stSql || '
                             , ''Incorporação de ativo''::varchar AS descricao
                             , 1 AS grupo
                             , 0 AS borda
                             , 0 AS linha
                        FROM tmp_relatorio
                        WHERE cod_reduzido = ''4.4''
                             
                     UNION ALL
                        SELECT ''4.4.0.0.00.00.00.00.00''::varchar AS classe
                             , coalesce((empenhado_per - anulado_per), 0.00) as saldo_atual';
                        IF stExercicio::INTEGER > 2013 THEN
                             stSql := stSql || ', coalesce((empenhado_anterior - anulado_anterior),0.00) AS saldo_anterior';
                        ELSE
                             stSql := stSql || ', 0.00 AS saldo_anterior';
                        END IF;
                        
                        stSql := stSql || '
                             , ''Incorporação de ativo''::varchar AS descricao
                             , 1 AS grupo
                             , 0 AS borda
                             , 0 AS linha
                        FROM tmp_relatorio
                        WHERE cod_reduzido = ''4.5''
                             
                    UNION ALL
                        SELECT ''4.6.0.0.00.00.00.00.00''::varchar AS classe
                             , coalesce((empenhado_per - anulado_per),0.00) as saldo_atual';
                        IF stExercicio::INTEGER > 2013 THEN
                             stSql := stSql || ', coalesce((empenhado_anterior - anulado_anterior),0.00) AS saldo_anterior';
                        ELSE
                             stSql := stSql || ', 0.00 AS saldo_anterior';
                        END IF;
                        
                        stSql := stSql || '
                             , ''Desincorporação de passivo''::varchar AS descricao
                             , 2 AS grupo
                             , 0 AS borda
                             , 0 AS linha
                        FROM tmp_relatorio
                        WHERE cod_reduzido = ''4.6''
                             
                    UNION ALL
                        SELECT ''2.1.0.0.00.00.00.00.00''::varchar AS classe
                             , coalesce(sum(tbl.arrecadado_periodo),0.00) as saldo_atual';
                        IF stExercicio::INTEGER > 2013 THEN
                             stSql := stSql || ', coalesce(sum(tbl.arrecadado_anterior),0.00) as saldo_anterior';
                        ELSE
                             stSql := stSql || ', 0.00 AS saldo_anterior';
                        END IF;
                        
                        stSql := stSql || '
                             
                             , ''Incorporação de passivo''::varchar AS descricao
                             , 3 AS grupo
                             , 0 AS borda
                             , 0 AS linha
                        FROM ' || stSqlAux || ' as tbl
                        WHERE orcamento.fn_movimento_balancete_receita( '|| quote_literal(stExercicio) ||'
                                                                        ,publico.fn_mascarareduzida(tbl.codigo_estrutural)
                                                                        ,'|| quote_literal(stCodEntidades) ||'
                                                                        ,'|| quote_literal(dataInicio) ||'
                                                                        ,'|| quote_literal(stDataFinal) ||'
                                                                    ) = true
                         AND tbl.codigo_estrutural like (''2.1.0.0.00%'')
                           
                    UNION ALL
                        SELECT ''2.2.0.0.00.00.00.00.00''::varchar AS classe
                             , coalesce(sum(tbl.arrecadado_periodo),0.00) as saldo_atual';
                        IF stExercicio::INTEGER > 2013 THEN
                             stSql := stSql || ', coalesce(sum(tbl.arrecadado_anterior),0.00) as saldo_anterior';
                        ELSE
                             stSql := stSql || ', 0.00 AS saldo_anterior';
                        END IF;
                        
                        stSql := stSql || '
                        
                             , ''Desincorporação de ativo''::varchar AS descricao
                             , 4 AS grupo
                             , 1 AS borda
                             , 0 AS linha
                        FROM ' || stSqlAux || ' as tbl
                        WHERE orcamento.fn_movimento_balancete_receita( '|| quote_literal(stExercicio) ||'
                                                                        ,publico.fn_mascarareduzida(tbl.codigo_estrutural)
                                                                        ,'|| quote_literal(stCodEntidades) ||'
                                                                        ,'|| quote_literal(dataInicio) ||'
                                                                        ,'|| quote_literal(stDataFinal) ||'
                                                                    ) = true
                         AND tbl.codigo_estrutural like (''2.2.0.0.00%'')
                             
                    UNION ALL
                        SELECT  ''2.2.0.0.00.00.00.00.00''::varchar AS classe
                             , coalesce(sum(tbl.arrecadado_periodo),0.00) as saldo_atual';
                        IF stExercicio::INTEGER > 2013 THEN
                             stSql := stSql || ', coalesce(sum(tbl.arrecadado_anterior),0.00) as saldo_anterior';
                        ELSE
                             stSql := stSql || ', 0.00 AS saldo_anterior';
                        END IF;
                        
                        stSql := stSql || '
                           
                             , ''Desincorporação de ativo''::varchar AS descricao
                             , 4 AS grupo
                             , 1 AS borda
                             , 0 AS linha
                        FROM ' || stSqlAux || ' as tbl
                        WHERE orcamento.fn_movimento_balancete_receita( '|| quote_literal(stExercicio) ||'
                                                                        ,publico.fn_mascarareduzida(tbl.codigo_estrutural)
                                                                        ,'|| quote_literal(stCodEntidades) ||'
                                                                        ,'|| quote_literal(dataInicio) ||'
                                                                        ,'|| quote_literal(stDataFinal) ||'
                                                                    ) = true
                         AND tbl.codigo_estrutural like (''2.3.0.0.00%'')
                        
                    ) as resultado
            GROUP BY resultado.classe, resultado.grupo, resultado.descricao, resultado.borda, resultado.linha';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_pre_empenho_despesa;
    DROP TABLE tmp_despesa;

    DROP TABLE tmp_relacao;
    DROP TABLE tmp_relatorio;

    DROP TABLE tmp_estornado;
    DROP TABLE tmp_liquidado_estornado;
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_liquidado;
    
    DROP TABLE tmp_valor;

    RETURN;
END;
$$ language 'plpgsql';

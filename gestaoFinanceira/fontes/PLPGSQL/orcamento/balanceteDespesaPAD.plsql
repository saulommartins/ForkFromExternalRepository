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
* $Revision: 15428 $
* $Name$
* $Author: cleisson $
* $Date: 2006-09-14 12:09:01 -0300 (Qui, 14 Set 2006) $
*
* Casos de uso: uc-02.01.22, uc-02-08-01
*/

CREATE OR REPLACE  FUNCTION orcamento.fn_balancete_despesa_pad( character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying ) RETURNS SETOF record AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    stCodEstruturalInicial  ALIAS FOR $5;
    stCodEstruturalFinal    ALIAS FOR $6;
    stCodReduzidoInicial    ALIAS FOR $7;
    stCodReduzidoFinal      ALIAS FOR $8;
    stControleDetalhado     ALIAS FOR $9;

    stSql               VARCHAR   := '''';
    stMascClassDespesa  VARCHAR   := '''';
    stMascRecurso       VARCHAR   := '''';
    reRegistro          RECORD;
    arEmpenhado         NUMERIC[] := Array[0];
    arAnulado           NUMERIC[] := Array[0];
    arPaga              NUMERIC[] := Array[0];
    arLiquidado         NUMERIC[] := Array[0];
    dtInicioAno         VARCHAR;

BEGIN


    stSql := ''CREATE TEMPORARY TABLE tmp_empenhado AS (
               SELECT
                     EE.dt_empenho       as dt_empenho,
                     EIPE.vl_total       as vl_total,
                     OCD.cod_conta       as cod_conta,
                     OD.num_orgao        as num_orgao,
                     OD.num_unidade      as num_unidade,
                     OD.cod_funcao       as cod_funcao,
                     OD.cod_subfuncao    as cod_subfuncao,
                     acao.num_acao       as num_pao,
                     programa.num_programa as cod_programa,
                     OD.cod_entidade     as cod_entidade,
                     OD.cod_recurso      as cod_recurso,
                     OD.cod_despesa      as cod_despesa

               FROM
                     orcamento.despesa           as OD,
                     orcamento.conta_despesa     as OCD,
                     empenho.pre_empenho_despesa as EPED,
                     empenho.empenho             as EE,
                     empenho.pre_empenho         as EPE,
                     empenho.item_pre_empenho    as EIPE,
                     orcamento.despesa_acao,
                     ppa.acao,
                     ppa.programa
               WHERE
                     OCD.cod_conta               = EPED.cod_conta
                 And OCD.exercicio               = EPED.exercicio
                 And OD.exercicio                = EPED.exercicio
                 And OD.cod_despesa              = EPED.cod_despesa

                 And EPED.exercicio              = EPE.exercicio
                 And EPED.cod_pre_empenho        = EPE.cod_pre_empenho

                 AND despesa_acao.exercicio_despesa = OD.exercicio
                 AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                 AND acao.cod_acao = despesa_acao.cod_acao
                  
                 AND programa.cod_programa = acao.cod_programa

                 And EPE.exercicio               = EE.exercicio
                 And EPE.cod_pre_empenho         = EE.cod_pre_empenho
                 And EPE.exercicio               = EIPE.exercicio
                 And EPE.cod_pre_empenho         = EIPE.cod_pre_empenho
                 And EPE.exercicio               = '''''' || stExercicio  ||'''''' '' || stFiltro ;

                if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
                end if;

                if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                       stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                end if;
                 if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
                end if;

                if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                       stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
                end if;
              stSql := stSql || '')'';
              EXECUTE stSql;


       stSql := ''CREATE TEMPORARY TABLE tmp_anulado as (
               SELECT
                     EEAI.timestamp      as timestamp,
                     EEAI.vl_anulado     as vl_anulado,
                     OCD.cod_conta       as cod_conta,
                     OD.num_orgao        as num_orgao,
                     OD.num_unidade      as num_unidade,
                     OD.cod_funcao       as cod_funcao,
                     OD.cod_subfuncao    as cod_subfuncao,
                     acao.num_acao       as num_pao,
                     programa.num_programa as cod_programa,
                     OD.cod_entidade     as cod_entidade,
                     OD.cod_recurso      as cod_recurso,
                     OD.cod_despesa      as cod_despesa

                FROM
                    orcamento.despesa           as OD,
                    orcamento.conta_despesa     as OCD,
                    empenho.pre_empenho_despesa as EPED,
                    empenho.pre_empenho         as EPE,
                    empenho.item_pre_empenho    as EIPE,
                    empenho.empenho_anulado_item as EEAI,
                    orcamento.despesa_acao,
                    ppa.acao,
                    ppa.programa
               WHERE
                    OCD.cod_conta            = EPED.cod_conta
                And OCD.exercicio            = EPED.exercicio

                And OD.cod_despesa           = EPED.cod_despesa
                And OD.exercicio             = EPED.exercicio

                And EPED.exercicio           = EPE.exercicio
                And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

                And EPE.exercicio            = EIPE.exercicio
                And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho

                AND despesa_acao.exercicio_despesa = OD.exercicio
                AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                AND acao.cod_acao = despesa_acao.cod_acao
                  
                AND programa.cod_programa = acao.cod_programa

                And EIPE.exercicio           = EEAI.exercicio
                And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
                And EIPE.num_item            = EEAI.num_item
                And EEAI.exercicio           = '''''' || stExercicio  || '''''' '' || stFiltro ;

                if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
                end if;

                if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                       stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                end if;
                 if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
                end if;

                if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                       stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
                end if;
              stSql := stSql || '')'';
              EXECUTE stSql;



   stSql := ''CREATE TEMPORARY TABLE  tmp_nota_liquidacao AS(
                 SELECT
                       ENLI.vl_total     as vl_total,
                       ENL.dt_liquidacao as dt_liquidacao,
                       OCD.cod_conta       as cod_conta,
                       OD.num_orgao        as num_orgao,
                       OD.num_unidade      as num_unidade,
                       OD.cod_funcao       as cod_funcao,
                       OD.cod_subfuncao    as cod_subfuncao,
                       acao.num_acao       as num_pao,
                       programa.num_programa as cod_programa,
                       OD.cod_entidade     as cod_entidade,
                       OD.cod_recurso      as cod_recurso,
                       OD.cod_despesa      as cod_despesa

                  FROM
                       orcamento.despesa             as OD,
                       orcamento.conta_despesa       as OCD,
                       empenho.pre_empenho_despesa   as EPED,
                       empenho.pre_empenho           as EPE,
                       empenho.empenho               as EE,
                       empenho.nota_liquidacao_item  as ENLI,
                       empenho.nota_liquidacao       as ENL,
                       orcamento.despesa_acao,
                       ppa.acao,
                       ppa.programa
                 WHERE
                       OCD.cod_conta               = EPED.cod_conta
                   And OCD.exercicio               = EPED.exercicio

                   And OD.cod_despesa              = EPED.cod_despesa
                   And OD.exercicio                = EPED.exercicio

                   And EPE.cod_pre_empenho         = EE.cod_pre_empenho
                   And EPE.exercicio               = EE.exercicio

                   And EE.exercicio                = ENL.exercicio_empenho
                   And EE.cod_entidade             = ENL.cod_entidade
                   And EE.cod_empenho              = ENL.cod_empenho

                   And ENL.exercicio               = ENLI.exercicio
                   And ENL.cod_nota                = ENLI.cod_nota
                   And ENL.cod_entidade            = ENLI.cod_entidade

                   AND despesa_acao.exercicio_despesa = OD.exercicio
                   AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                   AND acao.cod_acao = despesa_acao.cod_acao
                  
                   AND programa.cod_programa = acao.cod_programa

                   And EPE.exercicio               = EPED.exercicio
                   And EPE.cod_pre_empenho         = EPED.cod_pre_empenho
                   And EE.exercicio                = ''''''|| stExercicio || ''''''
                   And EPE.exercicio               = ''''''|| stExercicio || '''''' '' || stFiltro ;

                if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
                end if;

                if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                       stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                end if;
                 if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
                end if;

                if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                       stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
                end if;
              stSql := stSql || '')'';
              EXECUTE stSql;


   stSql := ''CREATE TEMPORARY TABLE  tmp_nota_liquidacao_anulada AS (
                 SELECT
                       ENLIA.timestamp  as timestamp,
                       ENLIA.vl_anulado as vl_anulado,
                       OCD.cod_conta       as cod_conta,
                       OD.num_orgao        as num_orgao,
                       OD.num_unidade      as num_unidade,
                       OD.cod_funcao       as cod_funcao,
                       OD.cod_subfuncao    as cod_subfuncao,
                       acao.num_acao       as num_pao,
                       programa.num_programa as cod_programa,
                       OD.cod_entidade     as cod_entidade,
                       OD.cod_recurso      as cod_recurso,
                       OD.cod_despesa      as cod_despesa

                   FROM
                       orcamento.despesa                    as OD,
                       orcamento.conta_despesa              as OCD,
                       empenho.pre_empenho_despesa          as EPED,
                       empenho.pre_empenho                  as EPE,
                       empenho.empenho                      as EE,
                       empenho.nota_liquidacao              as ENL,
                       empenho.nota_liquidacao_item         as ENLI,
                       empenho.nota_liquidacao_item_anulado as ENLIA,
                       orcamento.despesa_acao,
                       ppa.acao,
                       ppa.programa
                  WHERE
                       OCD.cod_conta            = EPED.cod_conta
                   And OCD.exercicio            = EPED.exercicio

                   And OD.cod_despesa           = EPED.cod_despesa
                   And OD.exercicio             = EPED.exercicio

                   And EPE.cod_pre_empenho      = EE.cod_pre_empenho
                   And EPE.exercicio            = EE.exercicio

                   And EE.exercicio             = ENL.exercicio_empenho
                   And EE.cod_entidade          = ENL.cod_entidade
                   And EE.cod_empenho           = ENL.cod_empenho

                   AND despesa_acao.exercicio_despesa = OD.exercicio
                   AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                   AND acao.cod_acao = despesa_acao.cod_acao
                  
                   AND programa.cod_programa = acao.cod_programa

                   And ENL.exercicio            = ENLI.exercicio
                   And ENL.cod_nota             = ENLI.cod_nota
                   And ENL.cod_entidade         = ENLI.cod_entidade

                   And ENLI.exercicio           = ENLIA.exercicio
                   And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
                   And ENLI.num_item            = ENLIA.num_item
                   And ENLI.cod_entidade        = ENLIA.cod_entidade
                   And ENLI.exercicio_item      = ENLIA.exercicio_item
                   And ENLI.cod_nota            = ENLIA.cod_nota

                   And EPED.exercicio           = EPE.exercicio
                   And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                   And EE.exercicio             = ''''''|| stExercicio || ''''''
                   And ENLIA.exercicio          = ''''''|| stExercicio || '''''' '' || stFiltro ;

                if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
                end if;

                if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                       stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                end if;
                 if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
                end if;

                if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                       stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
                end if;
              stSql := stSql || '')'';
              EXECUTE stSql;



   stSql := ''CREATE TEMPORARY TABLE  tmp_nota_liquidacao_paga AS (
               SELECT
                     ENLP.vl_pago as vl_pago,
                     ENLP.timestamp as timestamp,
                     OCD.cod_conta       as cod_conta,
                     OD.num_orgao        as num_orgao,
                     OD.num_unidade      as num_unidade,
                     OD.cod_funcao       as cod_funcao,
                     OD.cod_subfuncao    as cod_subfuncao,
                     acao.num_acao       as num_pao,
                     programa.num_programa as cod_programa,
                     OD.cod_entidade     as cod_entidade,
                     OD.cod_recurso      as cod_recurso,
                     OD.cod_despesa      as cod_despesa

               FROM
                    orcamento.despesa          as OD,
                    orcamento.conta_despesa    as OCD,
                    empenho.pre_empenho_despesa          as EPED,
                    empenho.empenho                      as EE,
                    empenho.pre_empenho                  as EPE,
                    empenho.nota_liquidacao              as ENL,
                    empenho.nota_liquidacao_paga         as ENLP,
                    orcamento.despesa_acao,
                    ppa.acao,
                    ppa.programa
               Where
                     OCD.cod_conta            = EPED.cod_conta
                 And OCD.exercicio            = EPED.exercicio

                 And OD.cod_despesa           = EPED.cod_despesa
                 And OD.exercicio             = EPED.exercicio

                 AND despesa_acao.exercicio_despesa = OD.exercicio
                 AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                 AND acao.cod_acao = despesa_acao.cod_acao
                  
                 AND programa.cod_programa = acao.cod_programa

                 And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                 And EPED.exercicio           = EPE.exercicio

                 And EPE.exercicio            = EE.exercicio
                 And EPE.cod_pre_empenho      = EE.cod_pre_empenho

                 And EE.cod_empenho           = ENL.cod_empenho
                 And EE.exercicio             = ENL.exercicio_empenho
                 And EE.cod_entidade          = ENL.cod_entidade

                 And ENL.cod_nota             = ENLP.cod_nota
                 And ENL.cod_entidade         = ENLP.cod_entidade
                 And ENL.exercicio            = ENLP.exercicio
                 And EE.exercicio             = ''''''|| stExercicio || ''''''
                 And ENLP.exercicio           = ''''''|| stExercicio || '''''' '' || stFiltro ;

                if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
                end if;

                if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                       stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                end if;
                 if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
                end if;

                if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                       stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
                end if;
              stSql := stSql || '')'';
              EXECUTE stSql;




   stSql := ''CREATE TEMPORARY TABLE tmp_nota_liquidacao_paga_anulada  AS(
              SELECT
                    ENLPA.timestamp_anulada as timestamp_anulada,
                    ENLPA.vl_anulado as vl_anulado,
                    OCD.cod_conta       as cod_conta,
                    OD.num_orgao        as num_orgao,
                    OD.num_unidade      as num_unidade,
                    OD.cod_funcao       as cod_funcao,
                    OD.cod_subfuncao    as cod_subfuncao,
                    acao.num_acao       as num_pao,
                    programa.num_programa as cod_programa,
                    OD.cod_entidade     as cod_entidade,
                    OD.cod_recurso      as cod_recurso,
                    OD.cod_despesa      as cod_despesa

              FROM
                    orcamento.despesa          as OD,
                    orcamento.conta_despesa    as OCD,
                    empenho.pre_empenho_despesa          as EPED,
                    empenho.empenho                      as EE,
                    empenho.pre_empenho                  as EPE,
                    empenho.nota_liquidacao              as ENL,
                    empenho.nota_liquidacao_paga         as ENLP,
                    empenho.nota_liquidacao_paga_anulada as ENLPA,
                    orcamento.despesa_acao,
                    ppa.acao,
                    ppa.programa
               Where OCD.cod_conta            = EPED.cod_conta
                 And OCD.exercicio            = EPED.exercicio

                 And OD.cod_despesa           = EPED.cod_despesa
                 And OD.exercicio             = EPED.exercicio

                 AND despesa_acao.exercicio_despesa = OD.exercicio
                 AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                 AND acao.cod_acao = despesa_acao.cod_acao
                  
                 AND programa.cod_programa = acao.cod_programa

                 And EPED.exercicio           = EPE.exercicio
                 And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

                 And EPE.exercicio            = EE.exercicio
                 And EPE.cod_pre_empenho      = EE.cod_pre_empenho

                 And EE.cod_empenho           = ENL.cod_empenho
                 And EE.exercicio             = ENL.exercicio_empenho
                 And EE.cod_entidade          = ENL.cod_entidade

                 And ENL.exercicio            = ENLP.exercicio
                 And ENL.cod_nota             = ENLP.cod_nota
                 And ENL.cod_entidade         = ENLP.cod_entidade

                 And ENLP.cod_entidade        = ENLPA.cod_entidade
                 And ENLP.cod_nota            = ENLPA.cod_nota
                 And ENLP.exercicio           = ENLPA.exercicio
                 And ENLP.timestamp           = ENLPA.timestamp
                 And EE.exercicio             = ''''''|| stExercicio || ''''''
                 And ENLP.exercicio           = ''''''|| stExercicio || '''''' '' || stFiltro ;

                if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
                end if;

                if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                       stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                end if;
                 if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
                end if;

                if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                       stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
                end if;
              stSql := stSql || '')'';
              EXECUTE stSql;


        SELECT INTO
                   stMascRecurso
                   administracao.configuracao.valor
         FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = ''masc_recurso''
          AND   administracao.configuracao.exercicio = stExercicio;

   --CRIA TABELA TEMPORÁRIA COM TODOS AS DESPESAS DA DESPESA, SETA ELAS COMO MÃE
    CREATE TEMPORARY TABLE tmp_pre_empenho_despesa AS
        SELECT
                  exercicio
                 ,cod_conta
                 ,cod_despesa
                 ,cast(''M'' as varchar) as tipo_conta
        FROM
                 orcamento.despesa as d;

     --INSERE NA TABELA TEMPORARIA OS REGISTROS RESUTADOS DE UM SELECT
     --ESTE SELECT PREVEM DA TABELA PRE_EMPENHO_DESPESA ONDE TODOS OS REGISTROS SÃO SETADOS COMO FILHAS
        INSERT INTO tmp_pre_empenho_despesa
            SELECT
                    exercicio
                    ,cod_conta
                    ,cod_despesa
                    ,cast(''F'' as varchar) as tipo_conta
            FROM    empenho.pre_empenho_despesa e_ped

            WHERE NOT EXISTS ( 
                             SELECT 1
                               FROM orcamento.despesa o_d
                              WHERE o_d.exercicio   = e_ped.exercicio
                                AND o_d.cod_conta   = e_ped.cod_conta
                                AND o_d.cod_despesa = e_ped.cod_despesa
            );

        dtInicioAno := ''01/01/'' || stExercicio;

        stSql := ''CREATE TEMPORARY TABLE tmp_relacao AS
                SELECT
                   od.exercicio as exercicio,
                   od.cod_despesa as cod_despesa,
                   od.cod_entidade as cod_entidade,
                   eped.cod_conta,
                   acao.num_acao as num_pao,
                   programa.num_programa as cod_programa,
                   od.num_orgao as num_orgao,
                   od.num_unidade as num_unidade,
                   od.cod_recurso as cod_recurso ,
                   od.cod_funcao as cod_funcao ,
                   od.cod_subfuncao as cod_subfuncao,
                   od.vl_original as vl_original,
                   od.dt_criacao as dt_criacao,
                   ocd.cod_estrutural as classificacao,
                   ocd.descricao as descricao ,
                   sw_fn_mascara_dinamica(''''''||stMascRecurso||'''''', cast(od.cod_recurso as varchar)) AS num_recurso,
                   0.00    as empenhado_ano,
                   0.00    as empenhado_per,
                   0.00    as anulado_ano,
                   0.00    as anulado_per,
                   0.00    as paga_ano,
                   0.00    as paga_per,
                   0.00    as liquidado_ano,
                   0.00    as liquidado_per,
                   MAX(eped.tipo_conta) as tipo_conta,
                   coalesce(od.vl_original,0.00)as saldo_inicial,
                   coalesce(oss.valor,0.00)     as suplementacoes,
                   coalesce(osr.valor,0.00)     as reducoes,
                   (coalesce(od.vl_original,0.00)+coalesce(oss.valor,0.00)-coalesce(osr.valor,0.00)) as total_creditos,
                   coalesce(oss.credito_suplementar,0.00)        as credito_suplementar,
                   coalesce(oss.credito_especial,0.00)           as credito_especial,
                   coalesce(oss.credito_extraordinario,0.00)     as credito_extraordinario

                FROM
                --  empenho.pre_empenho_despesa eped,
                    tmp_pre_empenho_despesa eped,
                    orcamento.conta_despesa ocd,
                    orcamento.despesa od
                 LEFT JOIN
                 (
                  select
                  SUM(Case When (os.cod_tipo >= 1 and os.cod_tipo <= 5) or (os.cod_tipo = 15) or (os.cod_tipo = 14) or (os.cod_tipo = 13) Then
                                   oss1.valor
                           Else 0 End) as credito_suplementar,
                  SUM(Case When os.cod_tipo >= 6 and os.cod_tipo <= 10 Then
                                   oss1.valor
                           Else 0 End) as credito_especial,
                  SUM(Case When os.cod_tipo = 11 Then
                                   oss1.valor
                           Else 0 End) as credito_extraordinario,

                     cod_despesa,max(oss1.exercicio) as exercicio, sum(valor) as valor


                    from orcamento.suplementacao_suplementada as oss1,
                         orcamento.suplementacao as os
                         where os.cod_suplementacao = oss1.cod_suplementacao
                           and os.cod_suplementacao || os.exercicio IN (Select
                                                                              cod_suplementacao || exercicio
                                               FROM orcamento.suplementacao
                                              WHERE suplementacao.cod_suplementacao = os.cod_suplementacao
                                                AND suplementacao.exercicio      = os.exercicio
                                                AND suplementacao.dt_suplementacao between  to_date(''''''|| dtInicioAno ||'''''',''''dd/mm/yyyy'''') And to_date(''''''|| stDataFinal ||'''''',''''dd/mm/yyyy'''')
                           )
                           
                            AND NOT EXISTS ( SELECT 1
                                               FROM orcamento.suplementacao_anulada o_sa
                                              WHERE o_sa.cod_suplementacao   = os.cod_suplementacao
                                                AND o_sa.exercicio           = os.exercicio
                                                AND o_sa.exercicio           = '''''' || stExercicio  || ''''''
                            )
                            AND NOT EXISTS ( SELECT 1
                                               FROM orcamento.suplementacao_anulada o_sa2
                                              WHERE o_sa2.cod_suplementacao_anulacao   = os.cod_suplementacao
                                                AND o_sa2.exercicio                    = os.exercicio
                                                AND o_sa2.exercicio                    = '''''' || stExercicio  || ''''''
                            )
                         group by oss1.exercicio,oss1.cod_despesa) as oss

                  ON ( od.cod_despesa = oss.cod_despesa and
                       od.exercicio = oss.exercicio        )

                  LEFT JOIN
                    (select cod_despesa,max(osr1.exercicio) as exercicio,
                        SUM(Case When (os.cod_tipo >= 1 and os.cod_tipo <= 11) or (os.cod_tipo=15) or (os.cod_tipo=14) or (os.cod_tipo=13)Then
                                   osr1.valor
                        Else 0 End) as valor
                       from orcamento.suplementacao_reducao as osr1,
                            orcamento.suplementacao as os
                            where os.cod_suplementacao = osr1.cod_suplementacao
                            and   os.exercicio         = osr1.exercicio
                            
                            AND EXISTS ( SELECT 1
                                               FROM orcamento.suplementacao
                                              WHERE suplementacao.cod_suplementacao = os.cod_suplementacao
                                                AND suplementacao.exercicio      = os.exercicio
                                                AND suplementacao.dt_suplementacao between  to_date(''''''|| dtInicioAno ||'''''',''''dd/mm/yyyy'''') And to_date(''''''|| stDataFinal ||'''''',''''dd/mm/yyyy'''')
                            )
                            AND NOT EXISTS ( SELECT 1
                                               FROM orcamento.suplementacao_anulada o_sa
                                              WHERE o_sa.cod_suplementacao   = os.cod_suplementacao
                                                AND o_sa.exercicio           = os.exercicio
                                                AND o_sa.exercicio           = '''''' || stExercicio  || ''''''
                            )
                            AND NOT EXISTS ( SELECT 1
                                               FROM orcamento.suplementacao_anulada o_sa2
                                              WHERE o_sa2.cod_suplementacao_anulacao   = os.cod_suplementacao
                                                AND o_sa2.exercicio                    = os.exercicio
                                                AND o_sa2.exercicio                    = '''''' || stExercicio  || ''''''
                            )
                            group by osr1.exercicio,cod_despesa
                         ) as osr
                    ON(
                        od.cod_despesa        = osr.cod_despesa        and
                        od.exercicio          = osr.exercicio),

                    orcamento.recurso  oru,
                    orcamento.orgao oo,
                    orcamento.unidade ou,
                    orcamento.funcao ofu,
                    orcamento.subfuncao osf,
                    orcamento.despesa_acao,
                    ppa.acao,
                    ppa.programa
                WHERE
                        eped.cod_despesa      = od.cod_despesa
                AND     eped.exercicio        = od.exercicio
                AND     od.cod_conta          = ocd.cod_conta
                AND     od.exercicio          = ocd.exercicio
                AND     od.cod_recurso        = oru.cod_recurso
                AND     od.exercicio          = oru.exercicio
                AND     od.num_orgao          = oo.num_orgao
                AND     od.exercicio          = oo.exercicio
                AND     ou.num_unidade        = od.num_unidade
                AND     ou.num_orgao          = od.num_orgao
                AND     ou.exercicio          = od.exercicio
                AND     od.cod_funcao         = ofu.cod_funcao
                AND     od.exercicio          = ofu.exercicio
                AND     od.cod_subfuncao      = osf.cod_subfuncao
                AND     od.exercicio          = osf.exercicio
                AND despesa_acao.exercicio_despesa = OD.exercicio
                AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                AND acao.cod_acao = despesa_acao.cod_acao
                 
                AND programa.cod_programa = acao.cod_programa
                AND     od.exercicio          = '''''' || stExercicio  ||'''''' '' || stFiltro;

                if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                        stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
                end if;

                if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                       stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
                end if;
                 if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                        stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
                end if;

                if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                       stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
                end if;

                      stSql := stSql || '' group by  od.cod_entidade,
                                                     od.num_orgao,
                                                     od.num_unidade,
                                                     od.cod_funcao,
                                                     od.cod_subfuncao,
                                                     programa.num_programa,
                                                     acao.num_acao,                                                     
                                                     ocd.cod_estrutural,
                                                     od.cod_recurso,
                                                     num_recurso,
                                                     od.cod_despesa,
                                                     od.exercicio ,
                                                     eped.cod_conta,
                                                     od.vl_original,
                                                     od.dt_criacao,
                                                     ocd.descricao,
                                                     ofu.descricao,
                                                     osf.descricao,
                                                     empenhado_ano,
                                                     empenhado_per,
                                                     anulado_ano,
                                                     anulado_per,
                                                     paga_ano,
                                                     paga_per,
                                                     liquidado_ano,
                                                     liquidado_per,
                                                     saldo_inicial,
                                                     suplementacoes,
                                                     reducoes,
                                                     credito_suplementar,
                                                     credito_especial,
                                                     credito_extraordinario,
                                                     total_creditos
                                           order by  od.cod_entidade,
                                                     od.num_orgao,
                                                     od.num_unidade,
                                                     od.cod_funcao,
                                                     od.cod_subfuncao,
                                                     programa.num_programa,
                                                     acao.num_acao,
                                                     ocd.cod_estrutural,
                                                     od.cod_recurso,
                                                     num_recurso,
                                                     od.cod_despesa,
                                                     od.exercicio ,
                                                     eped.cod_conta,
                                                     od.vl_original,
                                                     od.dt_criacao,
                                                     ocd.descricao,
                                                     ofu.descricao,
                                                     osf.descricao,
                                                     empenhado_ano,
                                                     empenhado_per,
                                                     anulado_ano,
                                                     anulado_per,
                                                     paga_ano,
                                                     paga_per,
                                                     liquidado_ano,
                                                     liquidado_per,
                                                     saldo_inicial,
                                                     suplementacoes,
                                                     reducoes,
                                                     credito_suplementar,
                                                     credito_especial,
                                                     credito_extraordinario,
                                                     total_creditos  '';



        EXECUTE stSql;
        stSql := '' SELECT
           od.exercicio,
           od.cod_despesa,
           od.cod_entidade,
           programa.num_programa AS cod_programa,
           acao.num_acao AS num_pao,
           CASE WHEN tr.cod_conta IS NOT NULL THEN tr.cod_conta
                ELSE ocd.cod_conta
           END AS cod_conta,
           od.num_orgao,
           od.num_unidade,
           od.cod_recurso ,
           od.cod_funcao ,
           od.cod_subfuncao,
           cast(tr.tipo_conta as varchar) as tipo_conta,
           coalesce(od.vl_original,0.00) as vl_original,
           od.dt_criacao,
           CASE WHEN tr.classificacao IS NOT NULL THEN tr.classificacao
                ELSE ocd.cod_estrutural
           END as classificacao,
           CASE WHEN tr.descricao IS NOT NULL THEN tr.descricao
                ELSE ocd.descricao
           END as descricao,
           sw_fn_mascara_dinamica(''''''||stMascRecurso||'''''', cast(od.cod_recurso as varchar)) AS num_recurso,
           coalesce(tr.empenhado_ano) as empenhado_ano,
           coalesce(tr.empenhado_per) as empenhado_per,
           coalesce(tr.anulado_ano) as anulado_ano,
           coalesce(tr.anulado_per) as anulado_per,
           coalesce(tr.paga_ano) as paga_ano,
           coalesce(tr.paga_per) as paga_per,
           coalesce(tr.liquidado_ano) as liquidado_ano,
           coalesce(tr.liquidado_per) as liquidado_per,
           coalesce(od.vl_original) as  saldo_inicial,
           coalesce(tr.suplementacoes) as suplementacoes,
           coalesce(tr.reducoes) as reducoes ,
           coalesce(tr.total_creditos) as total_creditos,
           coalesce(tr.credito_suplementar) as credito_suplementar,
           coalesce(tr.credito_especial) as credito_especial,
           coalesce(tr.credito_extraordinario) as credito_extraordinario
        FROM
           orcamento.conta_despesa  ocd
           ,orcamento.despesa        od
        LEFT JOIN
           tmp_relacao tr
        ON(od.cod_despesa = tr.cod_despesa and
           od.exercicio   = tr.exercicio
           ),
                    orcamento.recurso  oru,
                    orcamento.orgao oo,
                    orcamento.unidade ou,
                    orcamento.funcao ofu,
                    orcamento.subfuncao osf,
                    orcamento.despesa_acao,
                    ppa.acao,
                    ppa.programa
                WHERE
                        od.exercicio          = ocd.exercicio
                AND     od.cod_conta          = ocd.cod_conta
                AND     od.cod_recurso        = oru.cod_recurso
                AND     od.exercicio          = oru.exercicio
                AND     od.num_orgao          = oo.num_orgao
                AND     od.exercicio          = oo.exercicio
                AND     ou.num_unidade        = od.num_unidade
                AND     ou.num_orgao          = od.num_orgao
                AND     ou.exercicio          = od.exercicio
                AND     od.cod_funcao         = ofu.cod_funcao
                AND     od.exercicio          = ofu.exercicio
                AND     od.cod_subfuncao      = osf.cod_subfuncao
                AND     od.exercicio          = osf.exercicio
                AND despesa_acao.exercicio_despesa = OD.exercicio
                AND despesa_acao.cod_despesa       = OD.cod_despesa
                  
                AND acao.cod_acao = despesa_acao.cod_acao
                 
                AND programa.cod_programa = acao.cod_programa
                AND     od.exercicio          = '''''' || stExercicio || '''''' '' || stFiltro;

           if (stCodEstruturalInicial is not null and stCodEstruturalInicial <> '''') then
                   stSql := stSql || '' AND ocd.cod_estrutural >= '''''' || stCodEstruturalInicial || '''''' AND '';
           end if;

           if (stCodEstruturalFinal is not null and stCodEstruturalFinal <> '''') then
                  stSql := stSql || '' ocd.cod_estrutural <= '''''' || stCodEstruturalFinal || '''''''';
           end if;
            if (stCodReduzidoInicial is not null and stCodReduzidoInicial <> '''') then
                   stSql := stSql || '' AND od.cod_despesa >= '''''' || stCodReduzidoInicial || '''''' AND '';
           end if;

           if (stCodReduzidoFinal is not null and stCodReduzidoFinal <> '''') then
                  stSql := stSql || '' od.cod_despesa <= '''''' || stCodReduzidoFinal || '''''''';
           end if;

                  stSql := stSql || '' ORDER BY od.cod_entidade,
                                                od.num_orgao,
                                                od.num_unidade,
                                                od.cod_funcao ,
                                                od.cod_subfuncao,
                                                programa.num_programa,
                                                acao.num_acao,'';

                 if(stControleDetalhado  <> '''') then
                    -- Detalhado Orcamento
                    stSql := stSql || ''  to_number(translate(classificacao, ''''.'''',''''''''),''''99999999999999''''),
                                          od.cod_recurso'';
                 else
                    -- Detalhado na execução
                    stSql := stSql || ''  od.cod_despesa,
                                          to_number(translate(classificacao, ''''.'''',''''''''),''''99999999999999''''),
                                          od.cod_recurso'';
                 end if;

    FOR reRegistro IN  EXECUTE stSql

    LOOP
       IF reRegistro.cod_conta IS NOT NULL THEN
        arEmpenhado := empenho.fn_despesa_empenhado_mes_ano_pad(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao, reRegistro.cod_programa, reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
        reRegistro.empenhado_ano := coalesce(arEmpenhado[1],0.00);
        reRegistro.empenhado_per := coalesce(arEmpenhado[2],0.00);

        arAnulado := empenho.fn_despesa_anulado_mes_ano_pad(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao,reRegistro.cod_programa, reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
        reRegistro.anulado_ano := coalesce(arAnulado[1],0.00);
        reRegistro.anulado_per := coalesce(arAnulado[2],0.00);

        arPaga := empenho.fn_despesa_paga_mes_ano_pad(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao,reRegistro.cod_programa , reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
        reRegistro.paga_ano := coalesce(arPaga[1],0.00);
        reRegistro.paga_per := coalesce(arPaga[2],0.00);

        arLiquidado := empenho.fn_despesa_liquidado_mes_ano_pad(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao,reRegistro.cod_programa, reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
        reRegistro.liquidado_ano := coalesce(arLiquidado[1],0.00);
        reRegistro.liquidado_per := coalesce(arLiquidado[2],0.00);

    END IF;
       IF
          reRegistro.empenhado_ano   <> 0.00 or
          reRegistro.empenhado_per   <> 0.00 or
          reRegistro.anulado_per     <> 0.00 or
          reRegistro.anulado_ano     <> 0.00 or
          reRegistro.paga_per        <> 0.00 or
          reRegistro.paga_ano        <> 0.00 or
          reRegistro.liquidado_per   <> 0.00 or
          reRegistro.liquidado_ano   <> 0.00 or
          reRegistro.tipo_conta      <> ''F''
       THEN
       RETURN next reRegistro;
     END IF;
    END LOOP;


    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_nota_liquidacao;
    DROP TABLE tmp_nota_liquidacao_anulada;
    DROP TABLE tmp_nota_liquidacao_paga;
    DROP TABLE tmp_nota_liquidacao_paga_anulada;

    DROP TABLE tmp_pre_empenho_despesa;
    DROP TABLE tmp_relacao;

    RETURN;
END;
'
    LANGUAGE plpgsql;

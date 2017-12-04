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
CREATE OR REPLACE  FUNCTION tcemg.relatorio_anexoII(character varying, character varying, character varying, character varying, character varying, boolean, character varying, character varying, character varying, character varying, character varying, varchar, varchar, varchar ) RETURNS SETOF record
AS $$
DECLARE
    stExercicio                   ALIAS FOR $1;
    stFiltro                      ALIAS FOR $2;
    stDataInicial                 ALIAS FOR $3;
    stDataFinal                   ALIAS FOR $4;
    stTipoSituacao                ALIAS FOR $5;
    boRestosPagar                 ALIAS FOR $6; 
    stCodEstruturalInicial        ALIAS FOR $7;
    stCodEstruturalFinal          ALIAS FOR $8;
    stCodReduzidoInicial          ALIAS FOR $9;
    stCodReduzidoFinal            ALIAS FOR $10;
    stControleDetalhado           ALIAS FOR $11;
    inNumOrgao                    ALIAS FOR $12;
    inNumUnidade                  ALIAS FOR $13;
    stVerificaCreateDropTables    ALIAS FOR $14;
    
    stSql                         VARCHAR   := '';
    stMascClassDespesa            VARCHAR   := '';
    stMascRecurso                 VARCHAR   := '';
    dtInicioAno                   VARCHAR;
    dtFim                         VARCHAR;
    stValorRecursoDestinacao      VARCHAR;
    stNomePrefeitura              VARCHAR;
    arEmpenhado                   NUMERIC[] := Array[0];
    arAnulado                     NUMERIC[] := Array[0];
    arPaga                        NUMERIC[] := Array[0];
    arLiquidado                   NUMERIC[] := Array[0];
    
    nuValorFundeb                 NUMERIC;
    nuValorSubTotal               NUMERIC;
    
    nuSomaEmpenhado               NUMERIC := 0.00;
    nuSomaAnulado                 NUMERIC := 0.00;
    nuSomaPago                    NUMERIC := 0.00;
    nuSomaLiquidado               NUMERIC := 0.00;

    reRegistro                    RECORD;
BEGIN

    dtFim := TO_CHAR(NOW(), 'dd/mm/yyyy');

    IF stVerificaCreateDropTables = '' or stVerificaCreateDropTables = null or stVerificaCreateDropTables = 'create' THEN

        IF ( stTipoSituacao = 'empenhado' ) THEN
            stSql := 'CREATE TABLE tmp_empenhado AS (
                       SELECT
                             EE.dt_empenho       as dt_empenho,
                             EIPE.vl_total       as vl_total,
                             OCD.cod_conta       as cod_conta,
                             OD.num_orgao        as num_orgao,
                             OD.num_unidade      as num_unidade,
                             OD.cod_funcao       as cod_funcao,
                             OD.cod_subfuncao    as cod_subfuncao,
                             acao.num_acao       as num_pao,
                             programa.num_programa     as cod_programa,
                             OD.cod_entidade     as cod_entidade,
                             OD.cod_recurso      as cod_recurso,
                             OD.cod_despesa      as cod_despesa
        
                       FROM
                             orcamento.despesa           as OD
                             JOIN orcamento.recurso(''' || stExercicio || ''') as oru
                             ON ( oru.cod_recurso = od.cod_recurso
                              AND oru.exercicio   = od.exercicio )
                            JOIN orcamento.programa_ppa_programa
                              ON programa_ppa_programa.cod_programa = od.cod_programa
                             AND programa_ppa_programa.exercicio   = od.exercicio
                            JOIN ppa.programa
                              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                            JOIN orcamento.pao_ppa_acao
                              ON pao_ppa_acao.num_pao = od.num_pao
                             AND pao_ppa_acao.exercicio = od.exercicio
                            JOIN ppa.acao 
                              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                             ,orcamento.conta_despesa     as OCD,
                             empenho.pre_empenho_despesa as EPED,
                             empenho.empenho             as EE,
                             empenho.pre_empenho         as EPE,
                             empenho.item_pre_empenho    as EIPE
                       WHERE
                             OCD.cod_conta               = EPED.cod_conta
                         And OCD.exercicio               = EPED.exercicio
                         And OD.exercicio                = EPED.exercicio
                         And OD.cod_despesa              = EPED.cod_despesa
        
                         And EPED.exercicio              = EPE.exercicio
                         And EPED.cod_pre_empenho        = EPE.cod_pre_empenho
        
                         And EPE.exercicio               = EE.exercicio
                         And EPE.cod_pre_empenho         = EE.cod_pre_empenho
                         And EPE.exercicio               = EIPE.exercicio
                         And EPE.cod_pre_empenho         = EIPE.cod_pre_empenho
                         And EPE.exercicio               = ''' || stExercicio  ||''' ' || stFiltro ;
                      stSql := stSql || ')';
        
                      EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_empenhado_anulada AS (
                       SELECT
                             ea.timestamp          AS dt_anulado,
                             eai.vl_anulado        AS vl_anulado,
                             OCD.cod_conta         AS cod_conta,
                             OD.num_orgao          AS num_orgao,
                             OD.num_unidade        AS num_unidade,
                             OD.cod_funcao         AS cod_funcao,
                             OD.cod_subfuncao      AS cod_subfuncao,
                             acao.num_acao         AS num_pao,
                             programa.num_programa AS cod_programa,
                             OD.cod_entidade       AS cod_entidade,
                             OD.cod_recurso        AS cod_recurso,
                             OD.cod_despesa        AS cod_despesa
        
                        FROM orcamento.despesa     AS OD
                             
                       INNER JOIN orcamento.recurso(''' || stExercicio || ''') AS oru
                               ON oru.cod_recurso = od.cod_recurso
                              AND oru.exercicio   = od.exercicio
                             
                       INNER JOIN orcamento.programa_ppa_programa
                               ON programa_ppa_programa.cod_programa = od.cod_programa
                              AND programa_ppa_programa.exercicio    = od.exercicio
                             
                       INNER JOIN ppa.programa
                               ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                             
                       INNER JOIN orcamento.pao_ppa_acao
                               ON pao_ppa_acao.num_pao   = od.num_pao
                              AND pao_ppa_acao.exercicio = od.exercicio
                             
                       INNER JOIN ppa.acao 
                               ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                             
                       INNER JOIN empenho.pre_empenho_despesa as EPED
                               ON EPED.exercicio   = OD.exercicio
                              And EPED.cod_despesa = OD.cod_despesa 
                       
                       INNER JOIN orcamento.conta_despesa as OCD
                               ON OCD.cod_conta = EPED.cod_conta
                              AND OCD.exercicio = EPED.exercicio
                     
                       INNER JOIN empenho.pre_empenho AS EPE
                               ON EPED.exercicio        = EPE.exercicio
                              AND EPED.cod_pre_empenho  = EPE.cod_pre_empenho
                              
                       INNER JOIN  empenho.empenho AS EE
                               ON EPE.exercicio        = EE.exercicio
                              AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
                              
                       INNER JOIN empenho.item_pre_empenho AS EIPE
                               ON EPE.exercicio       = EIPE.exercicio
                              AND EPE.cod_pre_empenho = EIPE.cod_pre_empenho

                       INNER JOIN empenho.empenho_anulado AS ea
                               ON ea.exercicio    = EE.exercicio
                              AND ea.cod_entidade = EE.cod_entidade
                              AND ea.cod_empenho  = EE.cod_empenho
                              AND TO_DATE( TO_CHAR( ea.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN TO_DATE('''|| stDataInicial ||''',''dd/mm/yyyy'') AND TO_DATE('''|| stDataFinal ||''',''dd/mm/yyyy'')
          
                       INNER JOIN empenho.empenho_anulado_item AS eai
                               ON eai.exercicio       = ea.exercicio
                              AND eai.cod_entidade    = ea.cod_entidade
                              AND eai.cod_empenho     = ea.cod_empenho
                              AND eai.timestamp       = ea.timestamp
                              AND eai.exercicio       = EE.exercicio
                              AND eai.cod_pre_empenho = EIPE.cod_pre_empenho
                              AND eai.num_item        = EIPE.num_item
                            
                            WHERE EPE.exercicio = ''' || stExercicio  ||''' ' || stFiltro ;
                      stSql := stSql || ')';

                      EXECUTE stSql;
            END IF;
        
            IF ( stTipoSituacao = 'liquidado' ) THEN
                stSql := 'CREATE TABLE  tmp_nota_liquidacao AS(
                              SELECT
                                    ENLI.vl_total     as vl_total,
                                    ENL.dt_liquidacao as dt_liquidacao,
                                    OCD.cod_conta       as cod_conta,
                                    OD.num_orgao        as num_orgao,
                                    OD.num_unidade      as num_unidade,
                                    OD.cod_funcao       as cod_funcao,
                                    OD.cod_subfuncao    as cod_subfuncao,
                                    acao.num_acao       as num_pao,
                                    programa.num_programa     as cod_programa,
                                    OD.cod_entidade     as cod_entidade,
                                    OD.cod_recurso      as cod_recurso,
                                    OD.cod_despesa      as cod_despesa
             
                               FROM
                                    orcamento.despesa             as OD
                                    JOIN orcamento.recurso(''' || stExercicio || ''') as oru
                                    ON ( oru.cod_recurso = od.cod_recurso
                                     AND oru.exercicio   = od.exercicio )
                                 
                                 JOIN orcamento.programa_ppa_programa
                                   ON programa_ppa_programa.cod_programa = od.cod_programa
                                  AND programa_ppa_programa.exercicio   = od.exercicio
                                 JOIN ppa.programa
                                   ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                                 JOIN orcamento.pao_ppa_acao
                                   ON pao_ppa_acao.num_pao = od.num_pao
                                  AND pao_ppa_acao.exercicio = od.exercicio
                                 JOIN ppa.acao 
                                   ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                                 
                                    ,
                                    orcamento.conta_despesa       as OCD,
                                    empenho.pre_empenho_despesa   as EPED,
                                    empenho.pre_empenho           as EPE,
                                    empenho.empenho               as EE,
                                    empenho.nota_liquidacao_item  as ENLI,
                                    empenho.nota_liquidacao       as ENL
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
             
                                And EPE.exercicio               = EPED.exercicio
                                And EPE.cod_pre_empenho         = EPED.cod_pre_empenho
                                And EE.exercicio                = '''|| stExercicio || '''
                                And ENL.exercicio               = '''|| stExercicio || ''' ' || stFiltro ;
                           stSql := stSql || ')';
             
                           EXECUTE stSql;
                           
                stSql := 'CREATE TABLE  tmp_nota_liquidacao_anulada AS (
                              SELECT
                                    ENLIA.timestamp        as timestamp,
                                    ENLIA.vl_anulado       as vl_anulado,
                                    OCD.cod_conta          as cod_conta,
                                    OD.num_orgao           as num_orgao,
                                    OD.num_unidade         as num_unidade,
                                    OD.cod_funcao          as cod_funcao,
                                    OD.cod_subfuncao       as cod_subfuncao,
                                    acao.num_acao          as num_pao,
                                    programa.num_programa  as cod_programa,
                                    OD.cod_entidade        as cod_entidade,
                                    OD.cod_recurso         as cod_recurso,
                                    OD.cod_despesa         as cod_despesa
             
                                FROM
                                    orcamento.despesa                    as OD
                                    JOIN orcamento.recurso(''' || stExercicio || ''') as oru
                                    ON ( oru.cod_recurso = od.cod_recurso
                                     AND oru.exercicio   = od.exercicio )
                                     
                                 JOIN orcamento.programa_ppa_programa
                                   ON programa_ppa_programa.cod_programa = od.cod_programa
                                  AND programa_ppa_programa.exercicio   = od.exercicio
                                 JOIN ppa.programa
                                   ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                                 JOIN orcamento.pao_ppa_acao
                                   ON pao_ppa_acao.num_pao = od.num_pao
                                  AND pao_ppa_acao.exercicio = od.exercicio
                                 JOIN ppa.acao 
                                   ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                                     
                                     ,
                                    orcamento.conta_despesa              as OCD,
                                    empenho.pre_empenho_despesa          as EPED,
                                    empenho.pre_empenho                  as EPE,
                                    empenho.empenho                      as EE,
                                    empenho.nota_liquidacao              as ENL,
                                    empenho.nota_liquidacao_item         as ENLI,
                                    empenho.nota_liquidacao_item_anulado as ENLIA
             
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
                                And EE.exercicio             = '''|| stExercicio || '''
                                And ENLIA.exercicio          = '''|| stExercicio || ''' ' || stFiltro ;
                    stSql := stSql || ')';
             
                    EXECUTE stSql;
            END IF;
        
            IF ( stTipoSituacao = 'pago' ) THEN     
            stSql := 'CREATE TABLE  tmp_nota_liquidacao_paga AS (
                        SELECT
                              ENLP.vl_pago as vl_pago,
                              ENLP.timestamp as timestamp,
                              OCD.cod_conta       as cod_conta,
                              OD.num_orgao        as num_orgao,
                              OD.num_unidade      as num_unidade,
                              OD.cod_funcao       as cod_funcao,
                              OD.cod_subfuncao    as cod_subfuncao,
                              acao.num_acao       as num_pao,
                              programa.num_programa     as cod_programa,
                              OD.cod_entidade     as cod_entidade,
                              OD.cod_recurso      as cod_recurso,
                              OD.cod_despesa      as cod_despesa
         
                        FROM
                             orcamento.despesa          as OD
                             JOIN orcamento.recurso(''' || stExercicio || ''') as oru
                             ON ( oru.cod_recurso = od.cod_recurso
                              AND oru.exercicio   = od.exercicio )
                              
                             JOIN orcamento.programa_ppa_programa
                               ON programa_ppa_programa.cod_programa = od.cod_programa
                              AND programa_ppa_programa.exercicio   = od.exercicio
                             JOIN ppa.programa
                               ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                             JOIN orcamento.pao_ppa_acao
                               ON pao_ppa_acao.num_pao = od.num_pao
                              AND pao_ppa_acao.exercicio = od.exercicio
                             JOIN ppa.acao 
                               ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                               
                               ,
                             orcamento.conta_despesa    as OCD,
                             empenho.pre_empenho_despesa          as EPED,
                             empenho.empenho                      as EE,
                             empenho.pre_empenho                  as EPE,
                             empenho.nota_liquidacao              as ENL,
                             empenho.nota_liquidacao_paga         as ENLP
         
                        Where
                              OCD.cod_conta            = EPED.cod_conta
                          And OCD.exercicio            = EPED.exercicio
         
                          And OD.cod_despesa           = EPED.cod_despesa
                          And OD.exercicio             = EPED.exercicio
         
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
                          And EE.exercicio             = '''|| stExercicio || '''
                          And ENLP.exercicio           = '''|| stExercicio || ''' ' || stFiltro ;
                       stSql := stSql || ')';
         
                       EXECUTE stSql;
         
            stSql := 'CREATE TABLE tmp_nota_liquidacao_paga_anulada  AS(
                       SELECT
                             ENLPA.timestamp_anulada   as timestamp_anulada,
                             ENLPA.vl_anulado          as vl_anulado,
                             OCD.cod_conta             as cod_conta,
                             OD.num_orgao              as num_orgao,
                             OD.num_unidade            as num_unidade,
                             OD.cod_funcao             as cod_funcao,
                             OD.cod_subfuncao          as cod_subfuncao,
                             acao.num_acao             as num_pao,
                             programa.num_programa     as cod_programa,
                             OD.cod_entidade           as cod_entidade,
                             OD.cod_recurso            as cod_recurso,
                             OD.cod_despesa            as cod_despesa
         
                       FROM
                             orcamento.despesa          as OD
                             JOIN orcamento.recurso(''' || stExercicio || ''') as oru
                               ON ( oru.cod_recurso = od.cod_recurso
                                AND oru.exercicio   = od.exercicio )
                             
                             JOIN orcamento.programa_ppa_programa
                               ON programa_ppa_programa.cod_programa = od.cod_programa
                              AND programa_ppa_programa.exercicio   = od.exercicio
                             JOIN ppa.programa
                               ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                             JOIN orcamento.pao_ppa_acao
                               ON pao_ppa_acao.num_pao = od.num_pao
                              AND pao_ppa_acao.exercicio = od.exercicio
                             JOIN ppa.acao 
                               ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                             
                               ,
                             orcamento.conta_despesa    as OCD,
                             empenho.pre_empenho_despesa          as EPED,
                             empenho.empenho                      as EE,
                             empenho.pre_empenho                  as EPE,
                             empenho.nota_liquidacao              as ENL,
                             empenho.nota_liquidacao_paga         as ENLP,
                             empenho.nota_liquidacao_paga_anulada as ENLPA
         
                        Where OCD.cod_conta            = EPED.cod_conta
                          And OCD.exercicio            = EPED.exercicio
         
                          And OD.cod_despesa           = EPED.cod_despesa
                          And OD.exercicio             = EPED.exercicio
         
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
                          And EE.exercicio             = '''|| stExercicio || '''
                          And ENLP.exercicio           = '''|| stExercicio || ''' ' || stFiltro ;
                       stSql := stSql || ')';
                       
                       EXECUTE stSql;
                END IF;
              
    END IF;

        SELECT INTO
                   stMascRecurso
                   administracao.configuracao.valor
         FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = 'masc_recurso'
          AND   administracao.configuracao.exercicio = stExercicio;

   --CRIA TABELA TEMPORÁRIA COM TODOS AS DESPESAS DA DESPESA, SETA ELAS COMO MÃE
    CREATE TABLE tmp_pre_empenho_despesa AS
        SELECT
                  exercicio
                 ,cod_conta
                 ,cod_despesa
                 ,cast('M' as varchar) as tipo_conta
        FROM
                 orcamento.despesa as d;

     --INSERE NA TABELA TEMPORARIA OS REGISTROS RESUTADOS DE UM SELECT
     --ESTE SELECT PREVEM DA TABELA PRE_EMPENHO_DESPESA ONDE TODOS OS REGISTROS SÃO SETADOS COMO FILHAS
        INSERT INTO tmp_pre_empenho_despesa
            SELECT
                    exercicio
                    ,cod_conta
                    ,cod_despesa
--                    ,cast('F' as varchar) as tipo_conta
            FROM    empenho.pre_empenho_despesa
            WHERE NOT EXISTS ( SELECT 1
                               FROM orcamento.despesa o_d
                              WHERE o_d.exercicio   = empenho.pre_empenho_despesa.exercicio
                                AND o_d.cod_conta   = empenho.pre_empenho_despesa.cod_conta
                                AND o_d.cod_despesa = empenho.pre_empenho_despesa.cod_despesa
                        );
        dtInicioAno := '01/01/' || stExercicio;
        
        -- Essa busca era feito dentro de um case no select abaixo, com isso ele era gerado cada vez
        -- consumindo muito tempo, ele foi retirado e colocado o valor em uma variavel para ser verificado apenas uma vez
        -- para assim poder montar corretamente o sql, sem precisar case, que consome muito tempo de sql
        SELECT valor INTO stValorRecursoDestinacao
          FROM administracao.configuracao
         WHERE exercicio  = stExercicio
           AND cod_modulo = 8
           AND parametro  = 'recurso_destinacao';

        SELECT valor INTO stNomePrefeitura FROM administracao.configuracao WHERE exercicio = '' || stExercicio || '' AND parametro = 'nom_prefeitura';
        
        stSql := 'CREATE TEMPORARY TABLE tmp_relacao AS
                SELECT
                   od.exercicio          as exercicio,
                   od.cod_despesa        as cod_despesa,
                   od.cod_entidade       as cod_entidade,
                   programa.num_programa as cod_programa,
                   eped.cod_conta,
                   acao.num_acao         as num_pao,
                   od.num_orgao          as num_orgao,
                   od.num_unidade        as num_unidade,
                   --od.cod_recurso      as cod_recurso ,
                   oru.masc_recurso_red  as cod_recurso,
                   od.cod_funcao         as cod_funcao ,
                   od.cod_subfuncao      as cod_subfuncao,
                   od.vl_original        as vl_original,
                   od.dt_criacao         as dt_criacao,
                   ocd.cod_estrutural    as classificacao,
                   ocd.descricao         as descricao ,
                   oru.masc_recurso_red  as num_recurso,
                   oru.nom_recurso       as nom_recurso,
                   oo.nom_orgao,
                   ou.nom_unidade,
                   ofu.descricao         as nom_funcao,
                   osf.descricao         as nom_subfuncao,
                   opg.descricao         as nom_programa,
                   opao.nom_pao          as nom_pao,
                   0.00 AS vl_tipo_situacao_per,
                   0.00    AS vl_fundeb,
                   0.00    AS vl_sub_total,
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
                  SUM(Case When os.cod_tipo >= 1 and os.cod_tipo <= 5 Then
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
                                     and os.exercicio         = oss1.exercicio
                                     and os.cod_suplementacao || os.exercicio IN (Select
                                                                                        cod_suplementacao || cl.exercicio
                                                                                    from
                                                                                        contabilidade.transferencia_despesa ctd,
                                                                                        contabilidade.lote cl
                                                                                    where
                                                                                        ctd.exercicio = cl.exercicio
                                                                                    and ctd.cod_lote  = cl.cod_lote
                                                                                    and ctd.tipo      = cl.tipo
                                                                                    and ctd.cod_entidade = cl.cod_entidade
                                                                                    --and cl.dt_lote between  to_date('''|| stDataInicial ||''',''dd/mm/yyyy'') And to_date('''|| stDataFinal ||''',''dd/mm/yyyy'')
                                                                                    and cl.dt_lote between  to_date('''|| dtInicioAno  ||''',''dd/mm/yyyy'') And to_date('''|| stDataFinal ||''',''dd/mm/yyyy'')
                                    )
                            AND NOT EXISTS ( SELECT 1
                                               FROM orcamento.suplementacao_anulada o_sa
                                              WHERE o_sa.cod_suplementacao = os.cod_suplementacao
                                                AND o_sa.exercicio         = os.exercicio
                                                AND o_sa.exercicio   = ''' || stExercicio  || '''
                                        )
                            AND NOT EXISTS ( SELECT 1
                                               FROM orcamento.suplementacao_anulada o_sa2
                                              WHERE o_sa2.cod_suplementacao_anulacao = os.cod_suplementacao
                                                AND o_sa2.exercicio                  = os.exercicio
                                                AND o_sa2.exercicio   = ''' || stExercicio  || '''
                                        )
                         group by oss1.exercicio,oss1.cod_despesa) as oss

                  ON ( od.cod_despesa = oss.cod_despesa and
                       od.exercicio = oss.exercicio        )

                  LEFT JOIN
                    (select cod_despesa,max(osr1.exercicio) as exercicio, sum(valor) as valor
                       from orcamento.suplementacao_reducao as osr1,
                            orcamento.suplementacao as os
                            where os.cod_suplementacao = osr1.cod_suplementacao
                            and   os.exercicio         = osr1.exercicio
                            
                  AND EXISTS ( SELECT 1
                                 FROM contabilidade.transferencia_despesa ctd
                                    , contabilidade.lote cl
                                WHERE cod_suplementacao = os.cod_suplementacao
                                  AND cl.exercicio      = os.exercicio
                                  AND ctd.exercicio     = cl.exercicio
                                  AND ctd.cod_lote      = cl.cod_lote
                                  AND ctd.tipo          = cl.tipo
                                  AND ctd.cod_entidade  = cl.cod_entidade
                               -- AND cl.dt_lote between to_date('''|| stDataInicial ||''',''dd/mm/yyyy'') And to_date('''|| stDataFinal ||''',''dd/mm/yyyy'')
                                  AND cl.dt_lote between to_date('''|| dtInicioAno ||''',''dd/mm/yyyy'')And to_date('''|| stDataFinal ||''',''dd/mm/yyyy'')
                            )
                            
                  AND NOT EXISTS ( SELECT 1
                                     FROM orcamento.suplementacao_anulada
                                    WHERE cod_suplementacao = os.cod_suplementacao
                                      AND exercicio         = os.exercicio
                                      AND exercicio         = ''' || stExercicio  || '''
                            )

                  AND NOT EXISTS ( SELECT 1
                                     FROM orcamento.suplementacao_anulada
                                    WHERE cod_suplementacao_anulacao = os.cod_suplementacao
                                      AND exercicio                  = os.exercicio
                                      AND exercicio                  = ''' || stExercicio  || '''
                                 )
                           group by osr1.exercicio,cod_despesa
                         ) as osr
                    ON(
                        od.cod_despesa        = osr.cod_despesa        and
                        od.exercicio          = osr.exercicio)
                    
                    JOIN orcamento.programa_ppa_programa
                      ON programa_ppa_programa.cod_programa = od.cod_programa
                     AND programa_ppa_programa.exercicio   = od.exercicio
                    JOIN ppa.programa
                      ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                    JOIN orcamento.pao_ppa_acao
                      ON pao_ppa_acao.num_pao = od.num_pao
                     AND pao_ppa_acao.exercicio = od.exercicio
                    JOIN ppa.acao 
                      ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao

                    ,orcamento.recurso(''' || stExercicio  || ''') as oru,
                    orcamento.orgao oo,
                    orcamento.unidade ou,
                    orcamento.funcao ofu,
                    orcamento.subfuncao osf,
                    orcamento.programa opg,
                    orcamento.pao opao
                WHERE
                        eped.cod_despesa      = od.cod_despesa
                AND     eped.exercicio        = od.exercicio

                AND     eped.exercicio        = ocd.exercicio
              --AND     od.cod_conta          = ocd.cod_conta
                AND     eped.cod_conta        = ocd.cod_conta

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
                AND     od.cod_programa       = opg.cod_programa
                AND     od.exercicio          = opg.exercicio
                AND     od.num_pao            = opao.num_pao
                AND     od.exercicio          = opao.exercicio
                AND     od.exercicio          = ''' || stExercicio  ||''' ' || stFiltro;

                stSql := stSql || ' group by  od.cod_entidade,
                                                     od.num_orgao,
                                                     od.num_unidade,
                                                     od.cod_funcao,
                                                     od.cod_subfuncao,
                                                     od.cod_programa,
                                                     od.num_pao,
                                                     ocd.cod_estrutural,
                                                     od.cod_recurso,
                                                     oru.masc_recurso_red,
                                                     num_recurso,
                                                     od.cod_despesa,
                                                     od.exercicio ,
                                                     eped.cod_conta,
                                                     od.vl_original,
                                                     od.dt_criacao,
                                                     ocd.descricao,
                                                     oru.nom_recurso,
                                                     nom_orgao,
                                                     nom_unidade,
                                                     ofu.descricao,
                                                     osf.descricao,
                                                     opg.descricao,
                                                     opao.nom_pao,
                                                     vl_tipo_situacao_per,
                                                     saldo_inicial,
                                                     suplementacoes,
                                                     reducoes,
                                                     credito_suplementar,
                                                     credito_especial,
                                                     credito_extraordinario,
                                                     total_creditos,
                                                     programa.num_programa,
                                                     acao.num_acao,
                                                     vl_sub_total,
                                                     vl_fundeb
                                           order by  od.cod_entidade,
                                                     od.num_orgao,
                                                     od.num_unidade,
                                                     od.cod_funcao,
                                                     od.cod_subfuncao,
                                                     od.cod_programa,
                                                     od.num_pao,
                                                     ocd.cod_estrutural,
                                                     od.cod_recurso,
                                                     num_recurso,
                                                     od.cod_despesa,
                                                     od.exercicio ,
                                                     eped.cod_conta,
                                                     od.vl_original,
                                                     od.dt_criacao,
                                                     ocd.descricao,
                                                     oru.nom_recurso,
                                                     nom_orgao,
                                                     nom_unidade,
                                                     ofu.descricao,
                                                     osf.descricao,
                                                     opg.descricao,
                                                     opao.nom_pao,
                                                     vl_tipo_situacao_per,                                                     
                                                     saldo_inicial,
                                                     suplementacoes,
                                                     reducoes,
                                                     credito_suplementar,
                                                     credito_especial,
                                                     credito_extraordinario,
                                                     total_creditos,
                                                     vl_sub_total,
                                                     vl_fundeb ';
        EXECUTE stSql;

--Tabela temporária com os valores referentes da tabela relação acima.
stSql := 'CREATE TEMPORARY TABLE tmp_retorno AS (
           SELECT
                od.exercicio,
                od.cod_despesa,
                od.cod_entidade,
                programa.num_programa as cod_programa,
                CASE WHEN tr.cod_conta IS NOT NULL THEN tr.cod_conta
                        ELSE ocd.cod_conta
                END AS cod_conta,
                acao.num_acao as num_pao,
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
                       
                oru.masc_recurso_red as num_recurso,
                CASE WHEN tr.nom_recurso IS NOT NULL THEN tr.nom_recurso
                        ELSE oru.nom_recurso
                        END  as nom_recurso,
                CASE WHEN tr.nom_orgao IS NOT NULL THEN tr.nom_orgao
                        ELSE oo.nom_orgao
                END as nom_orgao,
                
                CASE WHEN tr.nom_unidade IS NOT NULL
                     THEN tr.nom_unidade
                     ELSE ou.nom_unidade
                END as nom_unidade,
                
                CASE WHEN tr.nom_funcao IS NOT NULL THEN tr.nom_funcao
                        ELSE ofu.descricao
                END as nom_funcao,
                
                CASE WHEN tr.nom_subfuncao IS NOT NULL
                     THEN tr.nom_subfuncao
                     ELSE osf.descricao
                END as nom_subfuncao,
                
                CASE WHEN tr.nom_programa IS NOT NULL
                     THEN tr.nom_programa
                     ELSE opg.descricao
                END as nom_programa,
                
                CASE WHEN tr.nom_pao IS NOT NULL THEN tr.nom_pao
                        ELSE opao.nom_pao
                END as nom_pao, ';
                                                          
    IF ( stTipoSituacao = 'empenhado' ) THEN
        stSql := stSql || ' coalesce(tcemg.fn_despesa_empenhado_mes_anexo_ii(''' || stExercicio || ''',''' || stDataInicial || ''', ''' || stDataFinal || ''', tr.cod_conta, od.num_orgao, od.num_unidade, od.cod_funcao, od.cod_subfuncao, acao.num_acao, programa.num_programa, od.cod_entidade, od.cod_recurso, od.cod_despesa ),0.00) as vl_tipo_situacao_per, --empenhado_per, ';
    END IF;    

    IF ( stTipoSituacao = 'pago' ) THEN                
        stSql := stSql || ' coalesce(tcemg.fn_despesa_paga_mes_anexo_ii(''' || stExercicio || ''',''' || stDataInicial || ''', ''' || stDataFinal || ''', tr.cod_conta, od.num_orgao, od.num_unidade, od.cod_funcao, od.cod_subfuncao, acao.num_acao, programa.num_programa, od.cod_entidade, od.cod_recurso, od.cod_despesa ),0.00) as vl_tipo_situacao_per, --paga_per, ';
    END IF;
    
    IF ( stTipoSituacao = 'liquidado' ) THEN
        stSql := stSql || ' coalesce(tcemg.fn_despesa_liquidado_mes_anexo_ii(''' || stExercicio || ''',''' || stDataInicial || ''', ''' || stDataFinal || ''', tr.cod_conta, od.num_orgao, od.num_unidade, od.cod_funcao, od.cod_subfuncao, acao.num_acao, programa.num_programa, od.cod_entidade, od.cod_recurso, od.cod_despesa ),0.00) as vl_tipo_situacao_per, --liquidado_per, ';
    END IF;
    
    stSql := stSql || '
                coalesce(tr.vl_fundeb) as vl_fundeb,
                coalesce(tr.vl_sub_total) as vl_sub_total,
                coalesce(od.vl_original) as  saldo_inicial,
                coalesce(tr.suplementacoes) as suplementacoes,
                coalesce(tr.reducoes) as reducoes ,
                coalesce(tr.total_creditos) as total_creditos,
                coalesce(tr.credito_suplementar) as credito_suplementar,
                coalesce(tr.credito_especial) as credito_especial,
                coalesce(tr.credito_extraordinario) as credito_extraordinario,
                ppa.programa.num_programa::VARCHAR AS num_programa,
                ppa.acao.num_acao::VARCHAR AS num_acao
           FROM orcamento.conta_despesa  ocd
              , orcamento.despesa        od
       
       LEFT JOIN  tmp_relacao tr  ON (od.cod_despesa = tr.cod_despesa and
                                      od.exercicio   = tr.exercicio )
            JOIN orcamento.programa_ppa_programa
              ON programa_ppa_programa.cod_programa = od.cod_programa
             AND programa_ppa_programa.exercicio   = od.exercicio
            JOIN ppa.programa
              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
            JOIN orcamento.pao_ppa_acao
              ON pao_ppa_acao.num_pao = od.num_pao
             AND pao_ppa_acao.exercicio = od.exercicio
            JOIN ppa.acao 
              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
              
           , orcamento.recurso(''' || stExercicio || ''') as oru
           , orcamento.orgao oo
           , orcamento.unidade ou
           , orcamento.funcao ofu
           , orcamento.subfuncao osf
           , orcamento.programa opg
           , orcamento.pao opao
       WHERE od.exercicio          = ocd.exercicio
         AND od.cod_conta          = ocd.cod_conta
         AND od.cod_recurso        = oru.cod_recurso
         AND od.exercicio          = oru.exercicio
         AND od.num_orgao          = oo.num_orgao
         AND od.exercicio          = oo.exercicio
         AND ou.num_unidade        = od.num_unidade
         AND ou.num_orgao          = od.num_orgao
         AND ou.exercicio          = od.exercicio
         AND od.cod_funcao         = ofu.cod_funcao
         AND od.exercicio          = ofu.exercicio
         AND od.cod_subfuncao      = osf.cod_subfuncao
         AND od.exercicio          = osf.exercicio
         AND od.cod_programa       = opg.cod_programa
         AND od.exercicio          = opg.exercicio
         AND od.num_pao            = opao.num_pao
         AND od.exercicio          = opao.exercicio
         AND od.exercicio          = ''' || stExercicio || ''' ' || stFiltro;

    stSql := stSql || '
    ORDER BY od.cod_entidade,
             od.num_orgao,
             od.num_unidade,
             od.cod_funcao ,
             od.cod_subfuncao,
             od.cod_programa,
             od.num_pao,
             od.cod_despesa,
             to_number(translate(classificacao, ''.'',''''),''99999999999999''),
             od.cod_recurso
    ) ';
    
    EXECUTE stSql;

--Insere a função para Educação
stSql := '
    INSERT INTO tmp_retorno
         SELECT 
                exercicio,
                cod_despesa,
                cod_entidade,
                0 AS cod_programa,
                cod_conta,
                num_pao,
                num_orgao,
                num_unidade,
                cod_recurso ,
                cod_funcao ,
                0 AS cod_subfuncao,
                tipo_conta,
                vl_original,
                dt_criacao,
                classificacao,
                descricao,
                num_recurso,
                nom_recurso,
                nom_orgao,
                nom_unidade,
                ''EDUCAÇÃO'' AS nom_funcao,
                ''EDUCAÇÃO'' AS nom_subfuncao,
                ''EDUCAÇÃO'' AS nom_programa,
                nom_pao,
                0.00 AS vl_tipo_situacao_per,
                0.00 AS vl_fundeb,
                0.00 AS vl_sub_total,
                0.00 AS saldo_inicial,
                suplementacoes,
                reducoes ,
                total_creditos,
                credito_suplementar,
                credito_especial,
                credito_extraordinario,
                num_programa,
                num_acao            
           FROM tmp_retorno; ';

    EXECUTE stSql;
    
    --considera restos a pagar para situação liquidado
    IF (stTipoSituacao = 'liquidado' AND boRestosPagar = TRUE) THEN 
        stSql := '
                    INSERT INTO tmp_retorno
                        SELECT  '''' AS exercicio,
                                0 AS cod_despesa,
                                0 AS cod_entidade,
                                restos_pre_empenho.cod_programa AS cod_programa,
                                0 AS cod_conta,
                                0 AS num_pao,
                                0 AS num_orgao,
                                0 AS num_unidade,
                                0 AS cod_recurso ,
                                restos_pre_empenho.cod_funcao AS cod_funcao ,
                                restos_pre_empenho.cod_subfuncao AS cod_subfuncao,
                                '''' AS tipo_conta,
                                0.00 AS vl_original,
                                NOW() AS dt_criacao,
                                '''' AS classificacao,
                                '''' AS descricao,
                                '''' AS num_recurso,
                                '''' AS nom_recurso,
                                '''' AS nom_orgao,
                                '''' AS nom_unidade,
                                funcao.descricao AS nom_funcao,
                                subfuncao.descricao AS nom_subfuncao,
                                CASE WHEN programa_dados.identificacao IS NOT NULL 
				     THEN programa_dados.identificacao
				     ELSE programa.descricao
				END AS nom_programa,
                                '''' AS nom_pao,
                                retorno.valor AS vl_tipo_situacao_per,
                                0.00 AS vl_fundeb,
                                0.00 AS vl_sub_total,
                                0.00 AS saldo_inicial,
                                0.00 AS suplementacoes,
                                0.00 AS reducoes ,
                                0.00 AS total_creditos,
                                0.00 AS credito_suplementar,
                                0.00 AS credito_especial,
                                0.00 AS credito_extraordinario,
                                '''' AS num_programa,
                                '''' AS num_acao
                         
                         FROM empenho.fn_empenho_restos_pagar_anulado_liquidado_estornoliquidacao( 
                            ''''
                            ,''''
                            ,''' || stDataInicial || '''
                            , ''' || stDataFinal || '''
                            ,''2''
                            ,''''
                            ,''''
                            ,''101''
                            ,''''
                            ,''''
                            ,''''
                            ,''2''
                            ,''''
                            ,'''') 
                           AS retorno( entidade     INTEGER
                                     , empenho      INTEGER
                                     , exercicio    CHAR(4)
                                     , cgm          INTEGER
                                     , razao_social VARCHAR
                                     , cod_nota     INTEGER
                                     , valor        NUMERIC
                                     , data         TEXT
                                     )
                        
                       INNER JOIN empenho.empenho
                               ON empenho.exercicio    = retorno.exercicio
                              AND empenho.cod_entidade = retorno.entidade
                              AND empenho.cod_empenho  = retorno.empenho
                        
                       INNER JOIN empenho.pre_empenho
                               ON pre_empenho.exercicio       = empenho.exercicio
                              AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        
                       INNER JOIN empenho.restos_pre_empenho
                               ON restos_pre_empenho.exercicio       = pre_empenho.exercicio
                              AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        
                       INNER JOIN orcamento.conta_despesa
                               ON conta_despesa.exercicio      = restos_pre_empenho.exercicio
                              AND REPLACE(conta_despesa.cod_estrutural,''.'','''') = restos_pre_empenho.cod_estrutural
                              
                       LEFT JOIN ppa.programa_dados
                              ON programa_dados.cod_programa = restos_pre_empenho.cod_programa

                       LEFT JOIN orcamento.programa
			      ON programa.cod_programa = restos_pre_empenho.cod_programa
			     AND programa.exercicio = restos_pre_empenho.exercicio
                              
                       INNER JOIN orcamento.funcao
                               ON funcao.cod_funcao = restos_pre_empenho.cod_funcao
                              AND funcao.exercicio = restos_pre_empenho.exercicio

                       INNER JOIN orcamento.subfuncao
			       ON subfuncao.cod_subfuncao = restos_pre_empenho.cod_subfuncao
			      AND subfuncao.exercicio = restos_pre_empenho.exercicio

                            WHERE restos_pre_empenho.cod_funcao = 12
                              AND restos_pre_empenho.cod_subfuncao IN ( 122,272,361,365,367 )
                              AND restos_pre_empenho.recurso = 101
                              AND retorno.valor <> 0.00 ';
                              
            EXECUTE stSql;
            
    END IF;
   
   --considera restos a pagar para situação pago
   IF (stTipoSituacao = 'pago' AND boRestosPagar = TRUE) THEN 

        stSql := ' 
         INSERT INTO tmp_retorno
            SELECT  '''' AS exercicio,
                    0 AS cod_despesa,
                    0 AS cod_entidade,
                    restos_pre_empenho.cod_programa AS cod_programa,
                    0 AS cod_conta,
                    0 AS num_pao,
                    0 AS num_orgao,
                    0 AS num_unidade,
                    0 AS cod_recurso ,
                    restos_pre_empenho.cod_funcao AS cod_funcao ,
                    restos_pre_empenho.cod_subfuncao AS cod_subfuncao,
                    '''' AS tipo_conta,
                    0.00 AS vl_original,
                    NOW() AS dt_criacao,
                    '''' AS classificacao,
                    '''' AS descricao,
                    '''' AS num_recurso,
                    '''' AS nom_recurso,
                    '''' AS nom_orgao,
                    '''' AS nom_unidade,
                    funcao.descricao AS nom_funcao,
                    subfuncao.descricao AS nom_subfuncao,
                    CASE WHEN programa_dados.identificacao IS NOT NULL 
			 THEN programa_dados.identificacao
			 ELSE programa.descricao
		    END AS nom_programa,
                    '''' AS nom_pao,
                    retorno.valor AS vl_tipo_situacao_per,
                    0.00 AS vl_fundeb,
                    0.00 AS vl_sub_total,
                    0.00 AS saldo_inicial,
                    0.00 AS suplementacoes,
                    0.00 AS reducoes ,
                    0.00 AS total_creditos,
                    0.00 AS credito_suplementar,
                    0.00 AS credito_especial,
                    0.00 AS credito_extraordinario,
                    '''' AS num_programa,
                    '''' AS num_acao
                FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor( '''', '''', '|| quote_literal(stDataInicial) ||', '|| quote_literal(stDataFinal) ||', ''2'', '''', '''', ''101'', '''', '''', '''', '''', ''1'', '''', ''true'', ''12'', ''122,272,361,365,367'')
                   AS retorno(      
                                entidade            integer,                             
                                empenho             integer,                             
                                exercicio           char(4),                             
                                credor              varchar,                             
                                cod_estrutural      varchar,                             
                                cod_nota            integer,                             
                                data                text,                                
                                conta               integer,                             
                                banco               varchar,                             
                                valor               numeric                              
                    )
    
            INNER JOIN empenho.empenho
                    ON empenho.exercicio    = retorno.exercicio
                   AND empenho.cod_entidade = retorno.entidade
                   AND empenho.cod_empenho  = retorno.empenho
            
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio     = empenho.exercicio
                 AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

            INNER JOIN empenho.restos_pre_empenho
                    ON restos_pre_empenho.exercicio       = pre_empenho.exercicio
                   AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

             LEFT JOIN orcamento.conta_despesa
                    ON conta_despesa.exercicio  = restos_pre_empenho.exercicio
                   AND REPLACE(conta_despesa.cod_estrutural,''.'','''') = restos_pre_empenho.cod_estrutural
                   
             LEFT JOIN ppa.programa_dados
                    ON programa_dados.cod_programa = restos_pre_empenho.cod_programa

             LEFT JOIN orcamento.programa
                    ON programa.cod_programa = restos_pre_empenho.cod_programa
                   AND programa.exercicio = restos_pre_empenho.exercicio

            INNER JOIN orcamento.funcao
                    ON funcao.cod_funcao = restos_pre_empenho.cod_funcao
                   AND funcao.exercicio = restos_pre_empenho.exercicio

            INNER JOIN orcamento.subfuncao
                    ON subfuncao.cod_subfuncao = restos_pre_empenho.cod_subfuncao
                   AND subfuncao.exercicio = restos_pre_empenho.exercicio 

                WHERE restos_pre_empenho.cod_funcao = 12
                  AND restos_pre_empenho.cod_subfuncao IN ( 122,272,361,365,367 )
                  AND restos_pre_empenho.recurso = 101 
                  AND retorno.valor <> 0.00 ';
                  
                EXECUTE stSql;
    END IF;
    
    
    stSql := '
        SELECT cod_funcao
            , cod_subfuncao
            , LPAD(cod_programa::VARCHAR,4,''0'')::VARCHAR AS cod_programa
            , nom_programa
            , SUM(vl_tipo_situacao_per) AS vl_tipo_situacao_per
            , vl_fundeb 
            , vl_sub_total
          FROM (
            SELECT tr_1.cod_funcao 
                 , tr_1.cod_subfuncao 
                 , ''0'' AS cod_programa
                 , tr_1.nom_subfuncao as nom_programa
                 , SUM(tr_1.vl_tipo_situacao_per) AS vl_tipo_situacao_per
                 , tr_1.vl_fundeb 
                 , tr_1.vl_sub_total
              FROM tmp_retorno AS tr_1
             
           GROUP BY tr_1.cod_funcao
                  , tr_1.cod_subfuncao
                  , tr_1.cod_programa
                  , tr_1.nom_subfuncao
                  , tr_1.vl_fundeb 
                  , tr_1.vl_sub_total
            
            UNION
            
            SELECT tr_2.cod_funcao 
                 , tr_2.cod_subfuncao 
                 , tr_2.cod_programa as cod_programa
                 , tr_2.nom_programa
                 , SUM(tr_2.vl_tipo_situacao_per) AS vl_tipo_situacao_per        
                 , tr_2.vl_fundeb 
                 , tr_2.vl_sub_total
              FROM tmp_retorno AS tr_2
             
          GROUP BY tr_2.cod_funcao
                  , tr_2.cod_subfuncao
                  , tr_2.cod_programa
                  , tr_2.nom_programa
                  , tr_2.vl_fundeb 
                  , tr_2.vl_sub_total
              ) AS temp_final
       GROUP BY cod_funcao, cod_subfuncao, cod_programa, nom_programa, vl_fundeb , vl_sub_total
       ORDER BY cod_funcao, cod_subfuncao, cod_programa ';

   --Adicioando para quando vem do AnexoI, pois já existem funções nela que se utilizam da uma tabela com mesmo nome, no calculo do fundeb é criado uma tabela temporaria com o mesmo nome
   -- onde outras funções internas utilizam , para não criar outras funções que realizam o mesmo procedimento, necessario dar o drop para não ter conflito.
   DROP TABLE IF EXISTS tmp_valor;

    --Valor FUNDEB
    SELECT SUM(arrecadado_periodo) INTO nuValorFundeb
      FROM tcemg.fn_anexoII_fundeb(stExercicio,'',stDataInicial,stDataFinal,'2','9.1.7.2.1.00.00.00.00.00','9.1.7.2.3.00.00.00.00.00','','','101','','')
        AS ( cod_estrutural      VARCHAR,                                           
             receita             INTEGER,                                           
             recurso             VARCHAR,                                           
             descricao           VARCHAR,                                           
             valor_previsto      NUMERIC,                                           
             arrecadado_periodo  NUMERIC,                                           
             arrecadado_ano      NUMERIC,                                           
             diferenca           NUMERIC );

    --Atualiza valor sub_total por cod_funcao e sub_funcao
    SELECT sum(tab.vl_sub_total) INTO nuValorSubTotal FROM (
        SELECT sum(vl_tipo_situacao_per) AS vl_sub_total FROM tmp_retorno WHERE cod_funcao = 12 GROUP BY cod_funcao, cod_subfuncao
    ) AS tab;

   FOR reRegistro IN EXECUTE stSql
   LOOP
      reRegistro.vl_fundeb := nuValorFundeb;
      reRegistro.vl_sub_total = nuValorSubTotal;
      RETURN next reRegistro; 
   END LOOP;

    IF stVerificaCreateDropTables = '' or stVerificaCreateDropTables = null or stVerificaCreateDropTables = 'drop' THEN
        DROP TABLE IF EXISTS tmp_empenhado;
        DROP TABLE IF EXISTS tmp_nota_liquidacao;
        DROP TABLE IF EXISTS tmp_nota_liquidacao_anulada;
        DROP TABLE IF EXISTS tmp_nota_liquidacao_paga;
        DROP TABLE IF EXISTS tmp_nota_liquidacao_paga_anulada;
    END IF;   
    
    DROP TABLE tmp_pre_empenho_despesa;
    DROP TABLE tmp_relacao;
    DROP TABLE tmp_retorno;
    
    RETURN;
END;
$$
    LANGUAGE plpgsql;
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
* $Id: balanceteDespesa.plsql 65648 2016-06-07 17:19:01Z franver $
*
* Casos de uso: uc-02.01.22
*/


CREATE OR REPLACE  FUNCTION orcamento.fn_balancete_despesa(character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, character varying, varchar, varchar, varchar ) RETURNS SETOF record
    AS $$
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
    inNumOrgao              ALIAS FOR $10;
    inNumUnidade            ALIAS FOR $11;
    stVerificaCreateDropTables    ALIAS FOR $12;
    /*
    Faz uma verificacao se deve fazer um create e drops das tabelas. Isso é usado na hora de alterar a despesa. Há uma demora muito grande para
    gerar a tela pois é chamada essa função várias vezes, com isso acontece uma demora muito grande na criação da tela.
    Vai ser feita uma verificacao para não precisar criar todas as vezes as tabelas temporarias, criando-as somente uma vez e as dropando no final.
    */


    stSql               VARCHAR   := '';
    stMascClassDespesa  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;
    arEmpenhado         NUMERIC[] := Array[0];
    arAnulado           NUMERIC[] := Array[0];
    arPaga              NUMERIC[] := Array[0];
    arLiquidado         NUMERIC[] := Array[0];
    dtInicioAno         VARCHAR;
    dtFim               VARCHAR;
    stValorRecursoDestinacao VARCHAR;
    stNomePrefeitura VARCHAR;
BEGIN

    dtFim := TO_CHAR(NOW(), 'dd/mm/yyyy');

    IF stVerificaCreateDropTables = '' or stVerificaCreateDropTables = null or stVerificaCreateDropTables = 'create' THEN

        stSql := '
        CREATE TABLE tmp_empenhado AS (
              SELECT EE.dt_empenho         AS dt_empenho
                   , EIPE.vl_total         AS vl_total
                   , OCD.cod_conta         AS cod_conta
                   , OD.num_orgao          AS num_orgao
                   , OD.num_unidade        AS num_unidade
                   , OD.cod_funcao         AS cod_funcao
                   , OD.cod_subfuncao      AS cod_subfuncao
                   , acao.num_acao         AS num_pao
                   , programa.num_programa AS cod_programa
                   , OD.cod_entidade       AS cod_entidade
                   , OD.cod_recurso        AS cod_recurso
                   , OD.cod_despesa        AS cod_despesa
                FROM orcamento.despesa AS OD
          INNER JOIN orcamento.recurso('''||stExercicio||''') AS oru
                  ON oru.cod_recurso = od.cod_recurso
                 AND oru.exercicio   = od.exercicio
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
                   , orcamento.conta_despesa     AS OCD
                   , empenho.pre_empenho_despesa AS EPED
                   , empenho.empenho             AS EE
                   , empenho.pre_empenho         AS EPE
                   , empenho.item_pre_empenho    AS EIPE
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND OD.exercicio         = EPED.exercicio
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EPE.exercicio        = EE.exercicio
                 AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
                 AND EPE.exercicio        = EIPE.exercicio
                 AND EPE.cod_pre_empenho  = EIPE.cod_pre_empenho
                 AND EPE.exercicio        = '''||stExercicio||'''
        '||stFiltro ;

        IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural >= ''' || stCodEstruturalInicial || '''';
        END IF;
        IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural <= ''' || stCodEstruturalFinal || '''';
        END IF;
        IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
            stSql := stSql || ' AND od.cod_despesa >= ''' || stCodReduzidoInicial || '''';
        END IF;
        IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
            stSql := stSql || ' AND od.cod_despesa <= ''' || stCodReduzidoFinal || '''';
        END IF;
        IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
            stSql := stSql || ' AND OD.num_orgao = ' || inNumOrgao || '';
        END IF;
        IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
            stSql := stSql || ' AND OD.num_unidade = ' || inNumUnidade;
        END IF;
        stSql := stSql || ')';

        EXECUTE stSql;


        stSql := '
        CREATE TABLE tmp_anulado AS (
              SELECT EEAI.timestamp        AS timestamp
                   , EEAI.vl_anulado       AS vl_anulado
                   , OCD.cod_conta         AS cod_conta
                   , OD.num_orgao          AS num_orgao
                   , OD.num_unidade        AS num_unidade
                   , OD.cod_funcao         AS cod_funcao
                   , OD.cod_subfuncao      AS cod_subfuncao
                   , acao.num_acao         AS num_pao
                   , programa.num_programa AS cod_programa
                   , OD.cod_entidade       AS cod_entidade
                   , OD.cod_recurso        AS cod_recurso
                   , OD.cod_despesa        AS cod_despesa
                FROM orcamento.despesa AS OD
          INNER JOIN orcamento.recurso('''||stExercicio||''') AS oru
                  ON oru.cod_recurso = od.cod_recurso
                 AND oru.exercicio   = od.exercicio
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
                   , orcamento.conta_despesa     AS OCD
                   , empenho.pre_empenho_despesa AS EPED
                   , empenho.pre_empenho         AS EPE
                   , empenho.item_pre_empenho    AS EIPE
                   , empenho.empenho_anulado_item AS EEAI
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EPE.exercicio        = EIPE.exercicio
                 AND EPE.cod_pre_empenho  = EIPE.cod_pre_empenho
                 AND EIPE.exercicio       = EEAI.exercicio
                 AND EIPE.cod_pre_empenho = EEAI.cod_pre_empenho
                 AND EIPE.num_item        = EEAI.num_item
                 AND EEAI.exercicio       = '''||stExercicio||'''
            '||stFiltro ;

         IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural >= '''||stCodEstruturalInicial||''' ';
         END IF;

         IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural <= '''||stCodEstruturalFinal||''' ';
         END IF;
          IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
            stSql := stSql || ' AND od.cod_despesa >= '''||stCodReduzidoInicial||''' ';
         END IF;

         IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
            stSql := stSql || ' AND od.cod_despesa <= '''||stCodReduzidoFinal||''' ';
         END IF;
         IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
            stSql := stSql || ' AND OD.num_orgao = '||inNumOrgao||' ';
         END IF;
         IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
            stSql := stSql || ' AND OD.num_unidade = '||inNumUnidade;
         END IF;

        stSql := stSql || ')';

        EXECUTE stSql;

        stSql := '
        CREATE TABLE tmp_nota_liquidacao AS(
              SELECT ENLI.vl_total     AS vl_total
                   , ENL.dt_liquidacao AS dt_liquidacao
                   , OCD.cod_conta       AS cod_conta
                   , OD.num_orgao        AS num_orgao
                   , OD.num_unidade      AS num_unidade
                   , OD.cod_funcao       AS cod_funcao
                   , OD.cod_subfuncao    AS cod_subfuncao
                   , acao.num_acao       AS num_pao
                   , programa.num_programa     AS cod_programa
                   , OD.cod_entidade     AS cod_entidade
                   , OD.cod_recurso      AS cod_recurso
                   , OD.cod_despesa      AS cod_despesa
                FROM orcamento.despesa             AS OD
          INNER JOIN orcamento.recurso('''||stExercicio||''') AS oru
                  ON oru.cod_recurso = od.cod_recurso
                 AND oru.exercicio   = od.exercicio
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
                   , orcamento.conta_despesa       AS OCD
                   , empenho.pre_empenho_despesa   AS EPED
                   , empenho.pre_empenho           AS EPE
                   , empenho.empenho               AS EE
                   , empenho.nota_liquidacao_item  AS ENLI
                   , empenho.nota_liquidacao       AS ENL
               WHERE OCD.cod_conta       = EPED.cod_conta
                 AND OCD.exercicio       = EPED.exercicio
                 AND OD.cod_despesa      = EPED.cod_despesa
                 AND OD.exercicio        = EPED.exercicio
                 AND EPE.cod_pre_empenho = EE.cod_pre_empenho
                 AND EPE.exercicio       = EE.exercicio
                 AND EE.exercicio        = ENL.exercicio_empenho
                 AND EE.cod_entidade     = ENL.cod_entidade
                 AND EE.cod_empenho      = ENL.cod_empenho
                 AND ENL.exercicio       = ENLI.exercicio
                 AND ENL.cod_nota        = ENLI.cod_nota
                 AND ENL.cod_entidade    = ENLI.cod_entidade
                 AND EPE.exercicio       = EPED.exercicio
                 AND EPE.cod_pre_empenho = EPED.cod_pre_empenho
                 AND EE.exercicio        = '''||stExercicio||'''
                 AND ENL.exercicio       = '''||stExercicio||'''
        ' || stFiltro ;

        IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural >= '''||stCodEstruturalInicial||''' ';
        END IF;

        IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural <= '''||stCodEstruturalFinal||''' ';
        END IF;
         IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
            stSql := stSql || ' AND od.cod_despesa >= '''||stCodReduzidoInicial||''' ';
        END IF;

        IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
            stSql := stSql || ' AND od.cod_despesa <= '''||stCodReduzidoFinal||''' ';
        END IF;
        IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
            stSql := stSql || ' AND OD.num_orgao = '||inNumOrgao||' ';
        END IF;
        IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
            stSql := stSql || ' AND OD.num_unidade = '||inNumUnidade;
        END IF;

        stSql := stSql || ')';

        EXECUTE stSql;

        stSql := '
        CREATE TABLE tmp_nota_liquidacao_anulada AS (
              SELECT ENLIA.timestamp  AS timestamp
                   , ENLIA.vl_anulado AS vl_anulado
                   , OCD.cod_conta       AS cod_conta
                   , OD.num_orgao        AS num_orgao
                   , OD.num_unidade      AS num_unidade
                   , OD.cod_funcao       AS cod_funcao
                   , OD.cod_subfuncao    AS cod_subfuncao
                   , acao.num_acao       AS num_pao
                   , programa.num_programa     AS cod_programa
                   , OD.cod_entidade     AS cod_entidade
                   , OD.cod_recurso      AS cod_recurso
                   , OD.cod_despesa      AS cod_despesa
                FROM orcamento.despesa AS OD
                JOIN orcamento.recurso('''||stExercicio||''') AS oru
                  ON oru.cod_recurso = od.cod_recurso
                 AND oru.exercicio   = od.exercicio
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
                   , orcamento.conta_despesa AS OCD
                   , empenho.pre_empenho_despesa AS EPED
                   , empenho.pre_empenho AS EPE
                   , empenho.empenho AS EE
                   , empenho.nota_liquidacao AS ENL
                   , empenho.nota_liquidacao_item AS ENLI
                   , empenho.nota_liquidacao_item_anulado AS ENLIA
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
                 AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
                 AND EPE.exercicio        = EE.exercicio
                 AND EE.exercicio         = ENL.exercicio_empenho
                 AND EE.cod_entidade      = ENL.cod_entidade
                 AND EE.cod_empenho       = ENL.cod_empenho
                 AND ENL.exercicio        = ENLI.exercicio
                 AND ENL.cod_nota         = ENLI.cod_nota
                 AND ENL.cod_entidade     = ENLI.cod_entidade
                 AND ENLI.exercicio       = ENLIA.exercicio
                 AND ENLI.cod_pre_empenho = ENLIA.cod_pre_empenho
                 AND ENLI.num_item        = ENLIA.num_item
                 AND ENLI.cod_entidade    = ENLIA.cod_entidade
                 AND ENLI.exercicio_item  = ENLIA.exercicio_item
                 AND ENLI.cod_nota        = ENLIA.cod_nota
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EE.exercicio         = '''||stExercicio||'''
                 AND ENLIA.exercicio      = '''||stExercicio||'''
            '||stFiltro ;

        IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural >= '''||stCodEstruturalInicial||''' ';
        END IF;

        IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural <= '''||stCodEstruturalFinal||''' ';
        END IF;
         IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
            stSql := stSql || ' AND od.cod_despesa >= '''||stCodReduzidoInicial||''' ';
        END IF;

        IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
            stSql := stSql || ' AND od.cod_despesa <= '''||stCodReduzidoFinal||''' ';
        END IF;
        IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
            stSql := stSql || ' AND OD.num_orgao = '||inNumOrgao||' ';
        END IF;
        IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
            stSql := stSql || ' AND OD.num_unidade = '||inNumUnidade;
        END IF;

        stSql := stSql || ')';

        EXECUTE stSql;


        stSql := '
        CREATE TABLE tmp_nota_liquidacao_paga AS (
              SELECT ENLP.vl_pago as vl_pago
                   , ENLP.timestamp as timestamp
                   , OCD.cod_conta       as cod_conta
                   , OD.num_orgao        as num_orgao
                   , OD.num_unidade      as num_unidade
                   , OD.cod_funcao       as cod_funcao
                   , OD.cod_subfuncao    as cod_subfuncao
                   , acao.num_acao       as num_pao
                   , programa.num_programa     as cod_programa
                   , OD.cod_entidade     as cod_entidade
                   , OD.cod_recurso      as cod_recurso
                   , OD.cod_despesa      as cod_despesa
                FROM orcamento.despesa          as OD
                JOIN orcamento.recurso(''' || stExercicio || ''') as oru
                  ON oru.cod_recurso = od.cod_recurso
                 AND oru.exercicio   = od.exercicio
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
                   , orcamento.conta_despesa    as OCD
                   , empenho.pre_empenho_despesa          as EPED
                   , empenho.empenho                      as EE
                   , empenho.pre_empenho                  as EPE
                   , empenho.nota_liquidacao              as ENL
                   , empenho.nota_liquidacao_paga         as ENLP
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPE.exercicio        = EE.exercicio
                 AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
                 AND EE.cod_empenho       = ENL.cod_empenho
                 AND EE.exercicio         = ENL.exercicio_empenho
                 AND EE.cod_entidade      = ENL.cod_entidade
                 AND ENL.cod_nota         = ENLP.cod_nota
                 AND ENL.cod_entidade     = ENLP.cod_entidade
                 AND ENL.exercicio        = ENLP.exercicio
                 AND EE.exercicio         = '''|| stExercicio || '''
                 AND ENLP.exercicio       = '''|| stExercicio || '''
            ' || stFiltro ;

        IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural >= '''||stCodEstruturalInicial||''' ';
        END IF;

        IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural <= '''||stCodEstruturalFinal||''' ';
        END IF;
         IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
            stSql := stSql || ' AND od.cod_despesa >= '''||stCodReduzidoInicial||''' ';
        END IF;

        IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
            stSql := stSql || ' AND od.cod_despesa <= '''||stCodReduzidoFinal||''' ';
        END IF;
        IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
            stSql := stSql || ' AND OD.num_orgao = '||inNumOrgao||' ';
        END IF;
        IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
            stSql := stSql || ' AND OD.num_unidade = '||inNumUnidade;
        END IF;

        stSql := stSql || ')';

        EXECUTE stSql;


        stSql := '
        CREATE TABLE tmp_nota_liquidacao_paga_anulada  AS(
              SELECT ENLPA.timestamp_anulada as timestamp_anulada
                   , ENLPA.vl_anulado as vl_anulado
                   , OCD.cod_conta       as cod_conta
                   , OD.num_orgao        as num_orgao
                   , OD.num_unidade      as num_unidade
                   , OD.cod_funcao       as cod_funcao
                   , OD.cod_subfuncao    as cod_subfuncao
                   , acao.num_acao       as num_pao
                   , programa.num_programa     as cod_programa
                   , OD.cod_entidade     as cod_entidade
                   , OD.cod_recurso      as cod_recurso
                   , OD.cod_despesa      as cod_despesa
                FROM orcamento.despesa          as OD
          INNER JOIN orcamento.recurso('''||stExercicio||''') as oru
                  ON oru.cod_recurso = od.cod_recurso
                 AND oru.exercicio   = od.exercicio
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
                   , orcamento.conta_despesa  AS OCD
                   , empenho.pre_empenho_despesa AS EPED
                   , empenho.empenho AS EE
                   , empenho.pre_empenho AS EPE
                   , empenho.nota_liquidacao AS ENL
                   , empenho.nota_liquidacao_paga AS ENLP
                   , empenho.nota_liquidacao_paga_anulada AS ENLPA
               WHERE OCD.cod_conta        = EPED.cod_conta
                 AND OCD.exercicio        = EPED.exercicio
                 AND OD.cod_despesa       = EPED.cod_despesa
                 AND OD.exercicio         = EPED.exercicio
                 AND EPED.exercicio       = EPE.exercicio
                 AND EPED.cod_pre_empenho = EPE.cod_pre_empenho
                 AND EPE.exercicio        = EE.exercicio
                 AND EPE.cod_pre_empenho  = EE.cod_pre_empenho
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
                 AND EE.exercicio         = '''||stExercicio||'''
                 AND ENLP.exercicio       = '''||stExercicio||'''
            ' || stFiltro ;
    
        IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural >= '''||stCodEstruturalInicial||''' ';
        END IF;
    
        IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
            stSql := stSql || ' AND ocd.cod_estrutural <= '''||stCodEstruturalFinal||''' ';
        END IF;
        IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
            stSql := stSql || ' AND od.cod_despesa >= '''||stCodReduzidoInicial||''' ';
        END IF;
    
        IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
            stSql := stSql || ' AND od.cod_despesa <= '''||stCodReduzidoFinal||''' ';
        END IF;
        IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
            stSql := stSql || ' AND OD.num_orgao = '||inNumOrgao||' ';
        END IF;
        IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
            stSql := stSql || ' AND OD.num_unidade = '||inNumUnidade;
        END IF;
    
        stSql := stSql || ')';
        EXECUTE stSql;
    END IF;

      SELECT
        INTO stMascRecurso
             administracao.configuracao.valor
        FROM administracao.configuracao
       WHERE administracao.configuracao.cod_modulo = 8
         AND administracao.configuracao.parametro = 'masc_recurso'
         AND administracao.configuracao.exercicio = stExercicio;

    --CRIA TABELA TEMPORÁRIA COM TODOS AS DESPESAS DA DESPESA, SETA ELAS COMO MÃE
    CREATE TABLE tmp_pre_empenho_despesa AS
      SELECT exercicio
           , cod_conta
           , cod_despesa
           , cast('M' as varchar) as tipo_conta
        FROM orcamento.despesa as d;

    --INSERE NA TABELA TEMPORARIA OS REGISTROS RESUTADOS DE UM SELECT
    --ESTE SELECT PREVEM DA TABELA PRE_EMPENHO_DESPESA ONDE TODOS OS REGISTROS SÃO SETADOS COMO FILHAS
      INSERT
        INTO tmp_pre_empenho_despesa
      SELECT exercicio
           , cod_conta
           , cod_despesa
        FROM empenho.pre_empenho_despesa
       WHERE NOT EXISTS (
                         SELECT 1
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
        
    stSql := '
    CREATE TEMPORARY TABLE tmp_relacao AS
          SELECT od.exercicio AS exercicio
               , od.cod_despesa AS cod_despesa
               , od.cod_entidade AS cod_entidade
               , programa.num_programa AS cod_programa
               , eped.cod_conta
               , acao.num_acao AS num_pao
               , od.num_orgao AS num_orgao
               , od.num_unidade AS num_unidade
               --, od.cod_recurso AS cod_recurso
               , oru.masc_recurso_red AS cod_recurso
               , od.cod_funcao AS cod_funcao
               , od.cod_subfuncao AS cod_subfuncao
               , od.vl_original AS vl_original
               , od.dt_criacao AS dt_criacao
               , ocd.cod_estrutural AS classificacao
               , ocd.descricao AS descricao
               , oru.masc_recurso_red AS num_recurso
               , oru.nom_recurso AS nom_recurso
               , oo.nom_orgao
               , ou.nom_unidade
               , ofu.descricao AS nom_funcao
               , osf.descricao AS nom_subfuncao
               , opg.descricao AS nom_programa
               , opao.nom_pao AS nom_pao
               , 0.00 AS empenhado_ano
               , 0.00 AS empenhado_per
               , 0.00 AS anulado_ano
               , 0.00 AS anulado_per
               , 0.00 AS paga_ano
               , 0.00 AS paga_per
               , 0.00 AS liquidado_ano
               , 0.00 AS liquidado_per
               , MAX(eped.tipo_conta) AS tipo_conta
               , coalesce(od.vl_original,0.00)AS saldo_inicial
               , coalesce(oss.valor,0.00)     AS suplementacoes
               , coalesce(osr.valor,0.00)     AS reducoes
               , (coalesce(od.vl_original,0.00)+coalesce(oss.valor,0.00)-coalesce(osr.valor,0.00)) AS total_creditos
               , coalesce(oss.credito_suplementar,0.00)        AS credito_suplementar
               , coalesce(oss.credito_especial,0.00)           AS credito_especial
               , coalesce(oss.credito_extraordinario,0.00)     AS credito_extraordinario
            FROM tmp_pre_empenho_despesa eped
               , orcamento.conta_despesa ocd
               , orcamento.despesa od
       LEFT JOIN (
                  SELECT SUM(CASE WHEN os.cod_tipo >= 1 AND os.cod_tipo <= 5
                                  THEN oss1.valor
                                  ELSE 0.00
                              END
                            ) AS credito_suplementar
                       , SUM(CASE WHEN os.cod_tipo >= 6 AND os.cod_tipo <= 10
                                  THEN oss1.valor
                                  ELSE 0.00
                              END
                            ) AS credito_especial
                       , SUM(CASE WHEN os.cod_tipo = 11
                                  THEN oss1.valor
                                  ELSE 0.00
                              END
                            ) AS credito_extraordinario
                       , cod_despesa
                       , MAX(oss1.exercicio) AS exercicio
                       , SUM(valor) AS valor
                    FROM orcamento.suplementacao_suplementada AS oss1
                       , orcamento.suplementacao AS os
                   WHERE os.cod_suplementacao = oss1.cod_suplementacao
                     AND os.exercicio         = oss1.exercicio
                     AND os.cod_suplementacao||os.exercicio IN (
                                                                SELECT cod_suplementacao||cl.exercicio
                                                                  FROM contabilidade.transferencia_despesa AS ctd
                                                                     , contabilidade.lote AS cl
                                                                 WHERE ctd.exercicio = cl.exercicio
                                                                   AND ctd.cod_lote  = cl.cod_lote
                                                                   AND ctd.tipo      = cl.tipo
                                                                   AND ctd.cod_entidade = cl.cod_entidade
                                                                   --AND cl.dt_lote between to_date('''|| stDataInicial ||''',''dd/mm/yyyy'') And to_date('''|| stDataFinal ||''',''dd/mm/yyyy'')
                                                                   AND cl.dt_lote BETWEEN TO_DATE('''||dtInicioAno||''',''dd/mm/yyyy'') AND TO_DATE('''||stDataFinal||''',''dd/mm/yyyy'')
                                                               )
                     AND NOT EXISTS (
                                     SELECT 1
                                       FROM orcamento.suplementacao_anulada o_sa
                                      WHERE o_sa.cod_suplementacao = os.cod_suplementacao
                                        AND o_sa.exercicio = os.exercicio
                                        AND o_sa.exercicio = '''||stExercicio||'''
                                    )
                     AND NOT EXISTS (
                                     SELECT 1
                                       FROM orcamento.suplementacao_anulada o_sa2
                                      WHERE o_sa2.cod_suplementacao_anulacao = os.cod_suplementacao
                                        AND o_sa2.exercicio = os.exercicio
                                        AND o_sa2.exercicio = '''||stExercicio||'''
                                    )
                GROUP BY oss1.exercicio
                       , oss1.cod_despesa
                 ) AS oss
              ON od.cod_despesa = oss.cod_despesa
             AND od.exercicio = oss.exercicio
       LEFT JOIN (
                  SELECT cod_despesa
                       , MAX(osr1.exercicio) AS exercicio
                       , SUM(valor) AS valor
                    FROM orcamento.suplementacao_reducao AS osr1
                       , orcamento.suplementacao AS os
                   WHERE os.cod_suplementacao = osr1.cod_suplementacao
                     AND os.exercicio = osr1.exercicio
                     AND EXISTS (
                                 SELECT 1
                                   FROM contabilidade.transferencia_despesa AS ctd
                                      , contabilidade.lote AS cl
                                  WHERE cod_suplementacao = os.cod_suplementacao
                                    AND cl.exercicio      = os.exercicio
                                    AND ctd.exercicio     = cl.exercicio
                                    AND ctd.cod_lote      = cl.cod_lote
                                    AND ctd.tipo          = cl.tipo
                                    AND ctd.cod_entidade  = cl.cod_entidade
                                    --AND cl.dt_lote between to_date('''|| stDataInicial ||''',''dd/mm/yyyy'') And to_date('''|| stDataFinal ||''',''dd/mm/yyyy'')
                                    AND cl.dt_lote BETWEEN TO_DATE('''|| dtInicioAno ||''',''dd/mm/yyyy'') AND TO_DATE('''|| stDataFinal ||''',''dd/mm/yyyy'')
                                )
                     AND NOT EXISTS ( SELECT 1
                                        FROM orcamento.suplementacao_anulada
                                       WHERE cod_suplementacao = os.cod_suplementacao
                                         AND exercicio = os.exercicio
                                         AND exercicio = '''||stExercicio||'''
                                    )
                     AND NOT EXISTS (
                                     SELECT 1
                                       FROM orcamento.suplementacao_anulada
                                      WHERE cod_suplementacao_anulacao = os.cod_suplementacao
                                        AND exercicio = os.exercicio
                                        AND exercicio = '''||stExercicio||'''
                                    )
                GROUP BY osr1.exercicio
                       , cod_despesa
                 ) as osr
              ON od.cod_despesa = osr.cod_despesa
             AND od.exercicio   = osr.exercicio
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
               , orcamento.recurso(''' || stExercicio  || ''') as oru
               , orcamento.orgao AS oo
               , orcamento.unidade AS ou
               , orcamento.funcao AS ofu
               , orcamento.subfuncao AS osf
               , orcamento.programa AS opg
               , orcamento.pao AS opao
           WHERE eped.cod_despesa = od.cod_despesa
             AND eped.exercicio = od.exercicio
             AND eped.exercicio = ocd.exercicio
             --AND od.cod_conta = ocd.cod_conta
             AND eped.cod_conta = ocd.cod_conta
             AND od.cod_recurso   = oru.cod_recurso
             AND od.exercicio     = oru.exercicio
             AND od.num_orgao     = oo.num_orgao
             AND od.exercicio     = oo.exercicio
             AND ou.num_unidade   = od.num_unidade
             AND ou.num_orgao     = od.num_orgao
             AND ou.exercicio     = od.exercicio
             AND od.cod_funcao    = ofu.cod_funcao
             AND od.exercicio     = ofu.exercicio
             AND od.cod_subfuncao = osf.cod_subfuncao
             AND od.exercicio     = osf.exercicio
             AND od.cod_programa  = opg.cod_programa
             AND od.exercicio     = opg.exercicio
             AND od.num_pao       = opao.num_pao
             AND od.exercicio     = opao.exercicio
             AND od.exercicio     = '''||stExercicio||'''
             '||stFiltro;

    IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
        stSql := stSql || ' AND ocd.cod_estrutural >= '''||stCodEstruturalInicial||''' ';
    END IF;

    IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
        stSql := stSql || ' AND ocd.cod_estrutural <= '''||stCodEstruturalFinal||''' ';
    END IF;
    IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
        stSql := stSql || ' AND od.cod_despesa >= '''||stCodReduzidoInicial||''' ';
    END IF;

    IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
        stSql := stSql || ' AND od.cod_despesa <= '''||stCodReduzidoFinal||''' ';
    END IF;
    IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
        stSql := stSql || ' AND OD.num_orgao = '||inNumOrgao||' ';
    END IF;
    IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
        stSql := stSql || ' AND OD.num_unidade = '||inNumUnidade;
    END IF;

    stSql := stSql || '
        GROUP BY od.cod_entidade
               , od.num_orgao
               , od.num_unidade
               , od.cod_funcao
               , od.cod_subfuncao
               , od.cod_programa
               , od.num_pao
               , ocd.cod_estrutural
               , od.cod_recurso
               , oru.masc_recurso_red
               , num_recurso
               , od.cod_despesa
               , od.exercicio
               , eped.cod_conta
               , od.vl_original
               , od.dt_criacao
               , ocd.descricao
               , oru.nom_recurso
               , nom_orgao
               , nom_unidade
               , ofu.descricao
               , osf.descricao
               , opg.descricao
               , opao.nom_pao
               , empenhado_ano
               , empenhado_per
               , anulado_ano
               , anulado_per
               , paga_ano
               , paga_per
               , liquidado_ano
               , liquidado_per
               , saldo_inicial
               , suplementacoes
               , reducoes
               , credito_suplementar
               , credito_especial
               , credito_extraordinario
               , total_creditos
               , programa.num_programa
               , acao.num_acao
        ORDER BY od.cod_entidade
               , od.num_orgao
               , od.num_unidade
               , od.cod_funcao
               , od.cod_subfuncao
               , od.cod_programa
               , od.num_pao
               , ocd.cod_estrutural
               , od.cod_recurso
               , num_recurso
               , od.cod_despesa
               , od.exercicio
               , eped.cod_conta
               , od.vl_original
               , od.dt_criacao
               , ocd.descricao
               , oru.nom_recurso
               , nom_orgao
               , nom_unidade
               , ofu.descricao
               , osf.descricao
               , opg.descricao
               , opao.nom_pao
               , empenhado_ano
               , empenhado_per
               , anulado_ano
               , anulado_per
               , paga_ano
               , paga_per
               , liquidado_ano
               , liquidado_per
               , saldo_inicial
               , suplementacoes
               , reducoes
               , credito_suplementar
               , credito_especial
               , credito_extraordinario
               , total_creditos
    ';

    EXECUTE stSql;

    stSql := '
          SELECT od.exercicio
               , od.cod_despesa
               , od.cod_entidade
               , programa.num_programa as cod_programa
               , CASE WHEN tr.cod_conta IS NOT NULL
                      THEN tr.cod_conta
                      ELSE ocd.cod_conta
                  END AS cod_conta
               , acao.num_acao as num_pao
               , od.num_orgao
               , od.num_unidade
               , od.cod_recurso
               , od.cod_funcao
               , od.cod_subfuncao
               , cast(tr.tipo_conta as varchar) AS tipo_conta
               , coalesce(od.vl_original,0.00) AS vl_original
               , od.dt_criacao
               , CASE WHEN tr.classificacao IS NOT NULL
                      THEN tr.classificacao
                      ELSE ocd.cod_estrutural
                  END AS classificacao
               , CASE WHEN tr.descricao IS NOT NULL
                      THEN tr.descricao
                      ELSE ocd.descricao
                  END AS descricao
               --, sw_fn_mascara_dinamica('''||stMascRecurso||''', cast(od.cod_recurso as varchar)) AS num_recurso
               , oru.masc_recurso_red AS num_recurso
               --, CASE WHEN ((SELECT valor 
               --                FROM administracao.configuracao
               --               WHERE exercicio = '''||stExercicio||''' 
               --                 AND cod_modulo = 8
               --                 AND parametro = ''recurso_destinacao''
               --            ) = ''true'' )
               --       THEN oru.masc_recurso_red
               --       ELSE cast(oru.cod_fonte AS VARCHAR)
               --END AS num_recurso
               , CASE WHEN tr.nom_recurso IS NOT NULL
                      THEN tr.nom_recurso
                      ELSE oru.nom_recurso
                  END AS nom_recurso
               , CASE WHEN tr.nom_orgao IS NOT NULL
                      THEN tr.nom_orgao
                      ELSE oo.nom_orgao
                  END AS nom_orgao
               , CASE WHEN tr.nom_unidade IS NOT NULL
                      THEN tr.nom_unidade
                      ELSE ou.nom_unidade
                  END AS nom_unidade
               , CASE WHEN tr.nom_funcao IS NOT NULL
                      THEN tr.nom_funcao
                      ELSE ofu.descricao
                  END AS nom_funcao
               , CASE WHEN tr.nom_subfuncao IS NOT NULL
                      THEN tr.nom_subfuncao
                      ELSE osf.descricao
                  END AS nom_subfuncao
               , CASE WHEN tr.nom_programa IS NOT NULL
                      THEN tr.nom_programa
                      ELSE opg.descricao
                  END AS nom_programa
               , CASE WHEN tr.nom_pao IS NOT NULL
                      THEN tr.nom_pao
                      ELSE opao.nom_pao
                  END AS nom_pao
               , COALESCE(tr.empenhado_ano) AS empenhado_ano
               , COALESCE(tr.empenhado_per) AS empenhado_per
               , COALESCE(tr.anulado_ano) AS anulado_ano
               , COALESCE(tr.anulado_per) AS anulado_per
               , COALESCE(tr.paga_ano) AS paga_ano
               , COALESCE(tr.paga_per) AS paga_per
               , COALESCE(tr.liquidado_ano) AS liquidado_ano
               , COALESCE(tr.liquidado_per) AS liquidado_per
               , COALESCE(od.vl_original) AS saldo_inicial
               , COALESCE(tr.suplementacoes) AS suplementacoes
               , COALESCE(tr.reducoes) AS reducoes
               , COALESCE(tr.total_creditos) AS total_creditos
               , COALESCE(tr.credito_suplementar) AS credito_suplementar
               , COALESCE(tr.credito_especial) AS credito_especial
               , COALESCE(tr.credito_extraordinario) AS credito_extraordinario
               , ppa.programa.num_programa::VARCHAR AS num_programa
               , ppa.acao.num_acao::VARCHAR AS num_acao
            FROM orcamento.conta_despesa AS ocd
      INNER JOIN orcamento.despesa AS od
              ON od.exercicio = ocd.exercicio
             AND od.cod_conta = ocd.cod_conta
       LEFT JOIN tmp_relacao AS tr
              ON od.cod_despesa = tr.cod_despesa
             AND od.exercicio   = tr.exercicio
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
      INNER JOIN orcamento.recurso('''||stExercicio||''') AS oru
              ON od.cod_recurso = oru.cod_recurso
             AND od.exercicio   = oru.exercicio
      INNER JOIN orcamento.orgao AS oo
              ON od.num_orgao = oo.num_orgao
             AND od.exercicio = oo.exercicio
      INNER JOIN orcamento.unidade AS ou
              ON ou.num_unidade = od.num_unidade
             AND ou.num_orgao   = od.num_orgao
             AND ou.exercicio   = od.exercicio
      INNER JOIN orcamento.funcao AS ofu
              ON od.cod_funcao = ofu.cod_funcao
             AND od.exercicio  = ofu.exercicio
      INNER JOIN orcamento.subfuncao AS osf
              ON od.cod_subfuncao = osf.cod_subfuncao
             AND od.exercicio     = osf.exercicio
      INNER JOIN orcamento.programa AS opg
              ON od.cod_programa = opg.cod_programa
             AND od.exercicio    = opg.exercicio
      INNER JOIN orcamento.pao AS opao
              ON od.num_pao   = opao.num_pao
             AND od.exercicio = opao.exercicio
           WHERE od.exercicio = '''||stExercicio||'''
    ';

--    IF (stCodEstruturalInicial IS NOT NULL AND stCodEstruturalInicial <> '') THEN
--            stSql := stSql || ' AND ocd.cod_estrutural >= ''' || stCodEstruturalInicial || '''';
--    END IF;
--
--    IF (stCodEstruturalFinal IS NOT NULL AND stCodEstruturalFinal <> '') THEN
--           stSql := stSql || ' AND ocd.cod_estrutural <= ''' || stCodEstruturalFinal || '''';
--    END IF;
    IF (stCodReduzidoInicial IS NOT NULL AND stCodReduzidoInicial <> '') THEN
        stSql := stSql || ' AND od.cod_despesa >= '''||stCodReduzidoInicial||''' ';
    END IF;
    IF (stCodReduzidoFinal IS NOT NULL AND stCodReduzidoFinal <> '') THEN
        stSql := stSql || ' AND od.cod_despesa <= '''||stCodReduzidoFinal||''' ';
    END IF;
    IF (inNumOrgao IS NOT NULL AND inNumOrgao <> '') THEN
        stSql := stSql || ' AND OD.num_orgao = '||inNumOrgao||' ';
    END IF;
    IF (inNumUnidade IS NOT NULL AND inNumUnidade <> '') THEN
        stSql := stSql || ' AND OD.num_unidade = '||inNumUnidade;
    END IF;

    stSql := stSql || '
        ORDER BY od.cod_entidade
               , od.num_orgao
               , od.num_unidade
               , od.cod_funcao
               , od.cod_subfuncao
               , od.cod_programa
               , od.num_pao
    ';

    IF (stControleDetalhado  <> '') THEN
        -- Detalhado Orcamento
        stSql := stSql || '
               , to_number(translate(classificacao, ''.'',''''),''99999999999999'')
               , od.cod_recurso
        ';
    ELSE
        -- Detalhado na execução
        stSql := stSql || '
               , od.cod_despesa
               , to_number(translate(classificacao, ''.'',''''),''99999999999999'')
               , od.cod_recurso
        ';
    END IF;


    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF reRegistro.cod_conta IS NOT NULL THEN
            arEmpenhado := empenho.fn_despesa_empenhado_mes_ano(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao, reRegistro.cod_programa, reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
            reRegistro.empenhado_ano := coalesce(arEmpenhado[1],0.00);
            reRegistro.empenhado_per := coalesce(arEmpenhado[2],0.00);

            arAnulado := empenho.fn_despesa_anulado_mes_ano(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao,reRegistro.cod_programa, reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
            reRegistro.anulado_ano := coalesce(arAnulado[1],0.00);
            reRegistro.anulado_per := coalesce(arAnulado[2],0.00);

            arPaga := empenho.fn_despesa_paga_mes_ano(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao,reRegistro.cod_programa , reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
            reRegistro.paga_ano := coalesce(arPaga[1],0.00);
            reRegistro.paga_per := coalesce(arPaga[2],0.00);

            arLiquidado := empenho.fn_despesa_liquidado_mes_ano(stExercicio,stDataInicial, stDataFinal, reRegistro.cod_conta, reRegistro.num_orgao, reRegistro.num_unidade,reRegistro.cod_funcao, reRegistro.cod_subfuncao,reRegistro.num_pao,reRegistro.cod_programa, reRegistro.cod_entidade, reRegistro.cod_recurso, reRegistro.cod_despesa );
            reRegistro.liquidado_ano := coalesce(arLiquidado[1],0.00);
            reRegistro.liquidado_per := coalesce(arLiquidado[2],0.00);
        END IF;

        IF reRegistro.empenhado_ano <> 0.00 OR reRegistro.empenhado_per   <> 0.00 OR
           reRegistro.anulado_per   <> 0.00 OR reRegistro.anulado_ano     <> 0.00 OR
           reRegistro.paga_per      <> 0.00 OR reRegistro.paga_ano        <> 0.00 OR
           reRegistro.liquidado_per <> 0.00 OR reRegistro.liquidado_ano   <> 0.00 OR
           --reRegistro.suplementacoes  <> 0.00 or reRegistro.reducoes        <> 0.00 or
           reRegistro.tipo_conta    <> 'F' THEN
           
           RETURN next reRegistro;
        END IF;
    END LOOP;
    
    IF stVerificaCreateDropTables = '' OR stVerificaCreateDropTables = NULL OR stVerificaCreateDropTables = 'drop' THEN
        DROP TABLE tmp_empenhado;
        DROP TABLE tmp_anulado;
        DROP TABLE tmp_nota_liquidacao;
        DROP TABLE tmp_nota_liquidacao_anulada;
        DROP TABLE tmp_nota_liquidacao_paga;
        DROP TABLE tmp_nota_liquidacao_paga_anulada;
    END IF;   
    
    DROP TABLE tmp_pre_empenho_despesa;
    DROP TABLE tmp_relacao;
    
    RETURN;
END;
$$ LANGUAGE 'plpgsql';


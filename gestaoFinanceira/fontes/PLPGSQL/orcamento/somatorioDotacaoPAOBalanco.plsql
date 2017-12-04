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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.13
*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_dotacao_pao_balanco(varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    stCodEntidades      ALIAS FOR $5;
    stSituacao          ALIAS FOR $6;
                
    stSql               VARCHAR   := '';
    reRegistro          RECORD;
    arDotacao           VARCHAR[];
    arMascDespesa       VARCHAR[];

    inOrgaoAnt          INTEGER := 0;
    inUnidadeAnt        INTEGER := 0;
    inFuncaoAnt         INTEGER := 0;
    inSubFuncaoAnt      INTEGER := 0;
    inProgramaAnt       INTEGER := 0;
    inPaoAnt            INTEGER := 0;
    inNivel             INTEGER := 0;
    inPosicao           INTEGER;
    stOrgao             VARCHAR;
    stUnidade           VARCHAR;
    stFuncao            VARCHAR;
    stSubFuncao         VARCHAR;
    stPrograma          VARCHAR;
    stPao               VARCHAR;
    stNumPao            VARCHAR;
    stClassDespesa      VARCHAR;
    stDigitoProjeto     VARCHAR;
    stDigitoAtividade   VARCHAR;
    stDigitoOperacao    VARCHAR;
    stDetalhamento      VARCHAR;


BEGIN

 IF ( stSituacao = 'empenhados' ) THEN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
       SELECT
            e.dt_empenho as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor,
            cd.cod_estrutural as cod_estrutural,
            d.num_orgao as num_orgao,
            d.num_unidade as num_unidade,
            d.num_pao as num_pao,
            orcamento.fn_consulta_despesa(d.exercicio, d.cod_despesa, true)  as dotacao
            ,orcamento.fn_consulta_despesa(d.exercicio, d.cod_despesa, false) as dotacao_sem_ponto

        FROM
            orcamento.despesa           as d,
            orcamento.conta_despesa     as cd,
            empenho.pre_empenho_despesa as ped,
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                cd.cod_conta               = ped.cod_conta
            AND cd.exercicio               = ped.exercicio

            And d.cod_despesa              = ped.cod_despesa
            AND d.exercicio                = ped.exercicio

            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho

            And e.cod_entidade             IN (' || stCodEntidades || ')
            And e.exercicio                = '|| quote_literal(stExercicio) ||'

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
            AND e.dt_empenho BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')';

          stSql := stSql || '
        )';

        EXECUTE stSql;
        stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT
            to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            EEAI.vl_anulado as valor,
            od.num_pao as num_pao,
            orcamento.fn_consulta_despesa(od.exercicio, od.cod_despesa, true)  as dotacao
            ,orcamento.fn_consulta_despesa(od.exercicio, od.cod_despesa, false) as dotacao_sem_ponto
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
                 And EEAI.exercicio           ='|| quote_literal(stExercicio) ||'
                 And EEAI.cod_entidade        IN ('||stCodEntidades||')
                 And OD.cod_despesa           = EPED.cod_despesa
                 AND OD.exercicio             = EPED.exercicio
                 AND to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')';

              stSql := stSql || ')';
        EXECUTE stSql;
  END IF;
 
 IF ( stSituacao = 'pagos' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLP.vl_pago as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade,
            OD.num_pao as num_pao,
            orcamento.fn_consulta_despesa(OD.exercicio, OD.cod_despesa, true)  as dotacao
            ,orcamento.fn_consulta_despesa(od.exercicio, od.cod_despesa, false) as dotacao_sem_ponto

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

            And EE.exercicio                ='|| quote_literal(stExercicio) ||'
            And EE.cod_entidade          IN ('||stCodEntidades||')

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio 
            AND to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')';
            
stSql := stSql || ')';
        EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLPA.vl_anulado as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao as num_orgao,
            OD.num_unidade as num_unidade,
            OD.num_pao as num_pao,
            orcamento.fn_consulta_despesa(OD.exercicio, OD.cod_despesa, true)  as dotacao
            ,orcamento.fn_consulta_despesa(od.exercicio, od.cod_despesa, false) as dotacao_sem_ponto

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
            AND OD.exercicio             = EPED.exercicio';

            stSql := stSql || '
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.cod_entidade          IN ('||stCodEntidades||')
            And EE.exercicio             = '|| quote_literal(stExercicio) ||'

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
            AND to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')';
        stSql := stSql || ')';
        EXECUTE stSql;
  END IF;
  
  IF ( stSituacao = 'liquidados' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
                SELECT
                    nl.dt_liquidacao as dataConsulta,
                    nli.vl_total as valor,
                    cd.cod_estrutural as cod_estrutural,
                    d.num_orgao as num_orgao,
                    d.num_unidade as num_unidade,
                    d.num_pao as num_pao,
                    orcamento.fn_consulta_despesa(d.exercicio, d.cod_despesa, true)  as dotacao
                    ,orcamento.fn_consulta_despesa(d.exercicio, d.cod_despesa, false) as dotacao_sem_ponto
                   
                FROM
                    orcamento.despesa             as d,
                    orcamento.conta_despesa       as cd,
                    empenho.pre_empenho_despesa   as ped,
                    empenho.pre_empenho           as pe,
                    empenho.empenho               as e,
                    empenho.nota_liquidacao_item  as nli,
                    empenho.nota_liquidacao       as nl
                WHERE
                        cd.cod_conta               = ped.cod_conta
                    AND cd.exercicio               = ped.exercicio

                    And d.cod_despesa              = ped.cod_despesa
                    AND d.exercicio                = ped.exercicio

                    And pe.exercicio               = ped.exercicio
                    And pe.cod_pre_empenho         = ped.cod_pre_empenho

                    And e.cod_entidade             IN (' || stCodEntidades || ')
                    And e.exercicio                = ' || quote_literal(stExercicio) || '

                    AND e.exercicio                = pe.exercicio
                    AND e.cod_pre_empenho          = pe.cod_pre_empenho

                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho

                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade
                    AND nl.dt_liquidacao BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')';
                    
        stSql := stSql || ')';
        EXECUTE stSql;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT
            to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta,
            ENLIA.vl_anulado as valor,
            OCD.cod_estrutural as cod_estrutural,
            OD.num_orgao,
            OD.num_unidade,
            OD.num_pao as num_pao,
            orcamento.fn_consulta_despesa(od.exercicio, od.cod_despesa, true)  as dotacao
            ,orcamento.fn_consulta_despesa(od.exercicio, od.cod_despesa, false) as dotacao_sem_ponto

        from orcamento.despesa                    as OD,
             orcamento.conta_despesa              as OCD,
             empenho.pre_empenho_despesa          as EPED,
             empenho.pre_empenho                  as EPE,
             empenho.empenho                      as EE,
             empenho.nota_liquidacao              as ENL,
             empenho.nota_liquidacao_item         as ENLI,
             empenho.nota_liquidacao_item_anulado as ENLIA

        Where OCD.cod_conta               = EPED.cod_conta
          AND OCD.exercicio               = EPED.exercicio
          And EPE.cod_pre_empenho         = EE.cod_pre_empenho
          And EPE.exercicio               = EE.exercicio

          And EE.exercicio                = ENL.exercicio_empenho
          And EE.cod_entidade             = ENL.cod_entidade
          And EE.cod_empenho              = ENL.cod_empenho
          And EE.cod_entidade             IN ('||stCodEntidades||')
          And EE.exercicio                = '|| quote_literal(stExercicio) || '

          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade';
        stSql := stSql || '
          And ENLI.exercicio           = ENLIA.exercicio
          And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
          And ENLI.num_item            = ENLIA.num_item
          And ENLI.cod_entidade        = ENLIA.cod_entidade
          And ENLI.exercicio_item      = ENLIA.exercicio_item
          And ENLI.cod_nota            = ENLIA.cod_nota
          And OD.cod_despesa           = EPED.cod_despesa
          AND OD.exercicio             = EPED.exercicio
          And OD.cod_entidade          IN ('||stCodEntidades||')
          And EPED.exercicio           = EPE.exercicio
          And EPED.cod_pre_empenho     = EPE.cod_pre_empenho 
          AND to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')';
          
        stSql := stSql || ')';
        EXECUTE stSql;
  END IF;

    CREATE TEMPORARY TABLE tmp_relatorio(
             dotacao        VARCHAR(80)
            ,detalhamento   TEXT
            ,cod_despesa    INTEGER
            ,descricao      VARCHAR(200)
            ,vl_projeto     NUMERIC(14,2)
            ,vl_atividade   NUMERIC(14,2)
            ,vl_operacao    NUMERIC(14,2)
            ,vl_total       NUMERIC(14,2)
            ,nivel          INTEGER
        );

        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     *
                     ,orcamento.fn_consulta_despesa(exercicio, cod_despesa, true)  as dotacao
                     ,orcamento.fn_consulta_despesa(exercicio, cod_despesa, false) as dotacao_sem_ponto
                FROM    orcamento.despesa
                WHERE   exercicio = ' || quote_literal(stExercicio) || '
                ' || stFiltro ;

        EXECUTE stSql;

        FOR reRegistro IN
            SELECT   *
            FROM     tmp_despesa
            ORDER BY num_orgao, num_unidade, cod_funcao, cod_subfuncao, cod_programa, num_pao, dotacao
        LOOP
            arDotacao := string_to_array(reRegistro.dotacao_sem_ponto,'.');
            IF reRegistro.num_orgao <> inOrgaoAnt THEN
                SELECT INTO
                    stOrgao
                    oo.nom_orgao
                FROM  orcamento.orgao as oo
                WHERE  oo.num_orgao     = reRegistro.num_orgao
                  AND  oo.exercicio     = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1] , stOrgao, 1 );
                inUnidadeAnt   := 0;
                inFuncaoAnt    := 0;
                inSubFuncaoAnt := 0;
                inProgramaAnt  := 0;
                inPaoAnt       := 0;
            END IF;
            inOrgaoAnt := reRegistro.num_orgao;

            IF reRegistro.num_unidade <> inUnidadeAnt THEN
                SELECT INTO
                    stUnidade
                    ou.nom_unidade
                FROM   orcamento.unidade as ou
                WHERE  ou.num_orgao    = reRegistro.num_orgao
                  AND  ou.num_unidade  = reRegistro.num_unidade
                  AND  ou.exercicio    = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao , nivel) values ( arDotacao[1]||'.'||
                                                                                 arDotacao[2] , stUnidade , 2);

                inFuncaoAnt    := 0;
                inSubFuncaoAnt := 0;
                inProgramaAnt  := 0;
                inPaoAnt       := 0;
            END IF;
            inUnidadeAnt := reRegistro.num_unidade;

            IF reRegistro.cod_funcao <> inFuncaoAnt THEN
                SELECT INTO
                    stFuncao
                    descricao
                FROM   orcamento.funcao
                WHERE  cod_funcao = reRegistro.cod_funcao
                  AND  exercicio  = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||'.'||
                                                                                 arDotacao[2]||'.'||
                                                                                 arDotacao[3], stFuncao, 3 );
                inSubFuncaoAnt := 0;
                inProgramaAnt  := 0;
                inPaoAnt       := 0;
            END IF;
            inFuncaoAnt := reRegistro.cod_funcao;

            IF reRegistro.cod_subfuncao <> inSubfuncaoAnt THEN
                SELECT INTO
                    stSubfuncao
                    descricao
                FROM   orcamento.subfuncao
                WHERE  cod_subfuncao = reRegistro.cod_subfuncao
                  AND  exercicio     = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel) values ( arDotacao[1]||'.'||
                                                                                arDotacao[2]||'.'||
                                                                                arDotacao[3]||'.'||
                                                                                arDotacao[4], stSubfuncao, 4 );
                inProgramaAnt  := 0;
                inPaoAnt       := 0;
            END IF;
            inSubfuncaoAnt := reRegistro.cod_subfuncao;

            IF reRegistro.cod_programa <> inProgramaAnt THEN
                SELECT INTO
                    stPrograma
                    descricao
                FROM   orcamento.programa
                WHERE  cod_programa  = reRegistro.cod_programa
                  AND  exercicio     = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||'.'||
                                                                                 arDotacao[2]||'.'||
                                                                                 arDotacao[3]||'.'||
                                                                                 arDotacao[4]||'.'||
                                                                                 arDotacao[5], stPrograma, 5 );
                inPaoAnt       := 0;
            END IF;
            inProgramaAnt := reRegistro.cod_programa;

            IF reRegistro.num_pao <> inPaoAnt THEN
                SELECT INTO
                    stPao, stDetalhamento
                    nom_pao, detalhamento
                FROM   orcamento.pao
                WHERE  num_pao    = reRegistro.num_pao
                  AND  exercicio  = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( detalhamento, dotacao, descricao, nivel ) values ( stDetalhamento,
                                                                                 arDotacao[1]||'.'||
                                                                                 arDotacao[2]||'.'||
                                                                                 arDotacao[3]||'.'||
                                                                                 arDotacao[4]||'.'||
                                                                                 arDotacao[5]||'.'||
                                                                                 arDotacao[6], stPao, 6 );
            END IF;
            inPaoAnt := reRegistro.num_pao;

            SELECT INTO
                   stClassDespesa
                   descricao
             FROM  orcamento.conta_despesa
            WHERE  cod_conta  = reRegistro.cod_conta
              AND  exercicio  = reRegistro.exercicio;
            INSERT INTO tmp_relatorio ( dotacao, cod_despesa, descricao, nivel ) values ( arDotacao[1]||'.'||
                                                                                          arDotacao[2]||'.'||
                                                                                          arDotacao[3]||'.'||
                                                                                          arDotacao[4]||'.'||
                                                                                          arDotacao[5]||'.'||
                                                                                          arDotacao[6]||'.'||
                                                                                          arDotacao[7],
                                                                                          reRegistro.cod_despesa,
                                                                                          stClassDespesa, 7 );
        END LOOP;

        SELECT INTO
                   inPosicao
                   cast(administracao.configuracao.valor as INTEGER)
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_posicao_digito_id'
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   stDigitoProjeto
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_digitos_id_projeto'
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   stDigitoAtividade
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_digitos_id_atividade'
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   stDigitoOperacao
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_digitos_id_oper_especiais'
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   arMascDespesa
                   string_to_array(administracao.configuracao.valor,'.')
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'masc_despesa'
               AND administracao.configuracao.exercicio = stExercicio;

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY dotacao
    LOOP
        IF ( stSituacao = 'empenhados' ) THEN
            reRegistro.vl_projeto   := orcamento.fn_totaliza_dotacao_pao_empenhado(arMascDespesa[6],stDigitoProjeto,reRegistro.dotacao,inPosicao) -  orcamento.fn_totaliza_dotacao_pao_anulado(arMascDespesa[6],stDigitoProjeto,reRegistro.dotacao,inPosicao);
            reRegistro.vl_atividade := orcamento.fn_totaliza_dotacao_pao_empenhado(arMascDespesa[6],stDigitoAtividade,reRegistro.dotacao,inPosicao)- orcamento.fn_totaliza_dotacao_pao_anulado(arMascDespesa[6],stDigitoAtividade,reRegistro.dotacao,inPosicao);
            reRegistro.vl_operacao  := orcamento.fn_totaliza_dotacao_pao_empenhado(arMascDespesa[6],stDigitoOperacao,reRegistro.dotacao,inPosicao) - orcamento.fn_totaliza_dotacao_pao_anulado(arMascDespesa[6],stDigitoOperacao,reRegistro.dotacao,inPosicao);
        END IF;
        IF ( stSituacao = 'liquidados' ) THEN
            reRegistro.vl_projeto   := orcamento.fn_totaliza_dotacao_pao_liquidado(arMascDespesa[6],stDigitoProjeto,reRegistro.dotacao,inPosicao) -  orcamento.fn_totaliza_dotacao_pao_liquidado_estornado(arMascDespesa[6],stDigitoProjeto,reRegistro.dotacao,inPosicao);
            reRegistro.vl_atividade := orcamento.fn_totaliza_dotacao_pao_liquidado(arMascDespesa[6],stDigitoAtividade,reRegistro.dotacao,inPosicao)- orcamento.fn_totaliza_dotacao_pao_liquidado_estornado(arMascDespesa[6],stDigitoAtividade,reRegistro.dotacao,inPosicao);
            reRegistro.vl_operacao  := orcamento.fn_totaliza_dotacao_pao_liquidado(arMascDespesa[6],stDigitoOperacao,reRegistro.dotacao,inPosicao) - orcamento.fn_totaliza_dotacao_pao_liquidado_estornado(arMascDespesa[6],stDigitoOperacao,reRegistro.dotacao,inPosicao);
        END IF;
        IF ( stSituacao = 'pagos' ) THEN
            reRegistro.vl_projeto   := orcamento.fn_totaliza_dotacao_pao_pago(arMascDespesa[6],stDigitoProjeto,reRegistro.dotacao,inPosicao) -  orcamento.fn_totaliza_dotacao_pao_estornado(arMascDespesa[6],stDigitoProjeto,reRegistro.dotacao,inPosicao);
            reRegistro.vl_atividade := orcamento.fn_totaliza_dotacao_pao_pago(arMascDespesa[6],stDigitoAtividade,reRegistro.dotacao,inPosicao)- orcamento.fn_totaliza_dotacao_pao_estornado(arMascDespesa[6],stDigitoAtividade,reRegistro.dotacao,inPosicao);
            reRegistro.vl_operacao  := orcamento.fn_totaliza_dotacao_pao_pago(arMascDespesa[6],stDigitoOperacao,reRegistro.dotacao,inPosicao) - orcamento.fn_totaliza_dotacao_pao_estornado(arMascDespesa[6],stDigitoOperacao,reRegistro.dotacao,inPosicao);
        END IF;
        
        reRegistro.vl_total     := coalesce(reRegistro.vl_projeto,0) + coalesce(reRegistro.vl_atividade,0) + coalesce(reRegistro.vl_operacao,0);
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;
    IF ( stSituacao = 'empenhados' ) THEN
        DROP TABLE tmp_empenhado;
        DROP TABLE tmp_anulado;
    END IF;
    IF ( stSituacao = 'liquidados' ) THEN
        DROP TABLE tmp_liquidado;
        DROP TABLE tmp_liquidado_estornado;
    END IF;
    IF ( stSituacao = 'pagos' ) THEN
        DROP TABLE tmp_pago;
        DROP TABLE tmp_estornado;
    END IF;

    RETURN;
END;
$$ language 'plpgsql';

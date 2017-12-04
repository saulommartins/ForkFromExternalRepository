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

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_dotacao_pao_orgao(varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;
    arDotacao           VARCHAR[];
    arDotacaoSemPonto   VARCHAR[];
    arMascDespesa       VARCHAR[];

    inCount             INTEGER := 1;
    inCountE            INTEGER := 0;
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
    stDotacao           VARCHAR;
    stDotacaoSemPonto   VARCHAR;
    stMascClassDespesa  VARCHAR;


BEGIN
        SELECT INTO
                   stMascClassDespesa
                   valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = ''masc_class_despesa''
               AND administracao.configuracao.exercicio = stExercicio;


        CREATE TEMPORARY TABLE tmp_relatorio(
             dotacao        VARCHAR(80)
            ,cod_despesa    INTEGER
            ,descricao      VARCHAR(200)
            ,vl_projeto     NUMERIC(14,2)
            ,vl_atividade   NUMERIC(14,2)
            ,vl_operacao    NUMERIC(14,2)
            ,vl_total       NUMERIC(14,2)
            ,nivel          INTEGER
        );

        CREATE TEMPORARY TABLE tmp_despesa(
             exercicio          VARCHAR(4)
            ,cod_despesa        INTEGER
            ,cod_entidade       INTEGER
            ,cod_programa       INTEGER
            ,cod_conta          INTEGER
            ,num_pao            INTEGER
            ,num_orgao          INTEGER
            ,num_unidade        INTEGER
            ,cod_recurso        INTEGER
            ,cod_funcao         INTEGER
            ,cod_subfuncao      INTEGER
            ,vl_original        NUMERIC(14,2)
            ,dt_criacao         DATE
            ,dotacao            VARCHAR(100)
            ,dotacao_sem_ponto  VARCHAR(100)
        );

        stSql := ''SELECT
                     *
                     ,orcamento.fn_consulta_despesa(exercicio, cod_despesa, true)  as dotacao
                     ,orcamento.fn_consulta_despesa(exercicio, cod_despesa, false) as dotacao_sem_ponto
                FROM    orcamento.despesa
                WHERE   exercicio = '' || stExercicio || ''
                '' || stFiltro ;
        FOR reRegistro IN EXECUTE stSql
        LOOP
            arDotacao           := string_to_array(reRegistro.dotacao,''.'');
            arDotacaoSemPonto   := string_to_array(reRegistro.dotacao_sem_ponto,''.'');
            stDotacao           := arDotacao[1]||''.''||arDotacao[3]||''.''||arDotacao[4]||''.''||arDotacao[5]||''.''||arDotacao[6];
            inCountE := publico.fn_countelements(stMascClassDespesa,''.'');
            WHILE inCount<=inCountE LOOP
                stDotacao   := stDotacao||''.''||arDotacao[inCount+6];
                inCount     := inCount + 1;
            END LOOP;
            --RAISE EXCEPTION ''% %'',inCountE,inCount;
            stDotacaoSemPonto   := arDotacaoSemPonto[1]||''.''||arDotacaoSemPonto[3]||''.''||arDotacaoSemPonto[4]||''.''||arDotacaoSemPonto[5]||''.''||arDotacaoSemPonto[6]||''.''||arDotacaoSemPonto[7];
            INSERT INTO tmp_despesa ( exercicio, cod_despesa, cod_entidade, cod_programa, cod_conta, num_pao, num_orgao, num_unidade, cod_recurso, cod_funcao, cod_subfuncao, vl_original, dt_criacao, dotacao, dotacao_sem_ponto )
            VALUES (reRegistro.exercicio, reRegistro.cod_despesa, reRegistro.cod_entidade, reRegistro.cod_programa ,reRegistro.cod_conta ,reRegistro.num_pao ,reRegistro.num_orgao ,reRegistro.num_unidade ,reRegistro.cod_recurso ,reRegistro.cod_funcao ,reRegistro.cod_subfuncao ,reRegistro.vl_original ,reRegistro.dt_criacao ,stDotacao, stDotacaoSemPonto);
        END LOOP;

        FOR reRegistro IN
            SELECT   *
            FROM     tmp_despesa
            ORDER BY num_orgao, num_unidade, cod_funcao, cod_subfuncao, cod_programa, num_pao, dotacao
        LOOP
            arDotacao := string_to_array(reRegistro.dotacao_sem_ponto,''.'');
            IF reRegistro.num_orgao <> inOrgaoAnt THEN
                SELECT INTO
                    stOrgao
                    oo.nom_orgao
                FROM   orcamento.orgao as oo
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
            IF reRegistro.cod_funcao <> inFuncaoAnt THEN
                SELECT INTO
                    stFuncao
                    descricao
                FROM   orcamento.funcao
                WHERE  cod_funcao = reRegistro.cod_funcao
                  AND  exercicio  = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||''.''||
                                                                                 arDotacao[2], stFuncao, 2 );
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
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel) values ( arDotacao[1]||''.''||
                                                                                arDotacao[2]||''.''||
                                                                                arDotacao[3], stSubfuncao, 3 );
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
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||''.''||
                                                                                 arDotacao[2]||''.''||
                                                                                 arDotacao[3]||''.''||
                                                                                 arDotacao[4], stPrograma, 4 );
                inPaoAnt       := 0;
            END IF;
            inProgramaAnt := reRegistro.cod_programa;

            IF reRegistro.num_pao <> inPaoAnt THEN
                SELECT INTO
                    stPao
                    nom_pao
                FROM   orcamento.pao
                WHERE  num_pao    = reRegistro.num_pao
                  AND  exercicio  = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||''.''||
                                                                                 arDotacao[2]||''.''||
                                                                                 arDotacao[3]||''.''||
                                                                                 arDotacao[4]||''.''||
                                                                                 arDotacao[5], stPao, 5 );
            END IF;
            inPaoAnt := reRegistro.num_pao;

            SELECT INTO
                   stClassDespesa
                   descricao
             FROM  orcamento.conta_despesa
            WHERE  cod_conta  = reRegistro.cod_conta
              AND  exercicio  = reRegistro.exercicio;
            INSERT INTO tmp_relatorio ( dotacao, cod_despesa, descricao, nivel ) values ( arDotacao[1]||''.''||
                                                                                          arDotacao[2]||''.''||
                                                                                          arDotacao[3]||''.''||
                                                                                          arDotacao[4]||''.''||
                                                                                          arDotacao[5]||''.''||
                                                                                          arDotacao[6],
                                                                                          reRegistro.cod_despesa,
                                                                                          stClassDespesa, 6 );
        END LOOP;

        SELECT INTO
                   inPosicao
                   cast(administracao.configuracao.valor as INTEGER)
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = ''pao_posicao_digito_id''
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   stDigitoProjeto
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = ''pao_digitos_id_projeto''
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   stDigitoAtividade
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = ''pao_digitos_id_atividade''
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   stDigitoOperacao
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = ''pao_digitos_id_oper_especiais''
               AND administracao.configuracao.exercicio = stExercicio;
        SELECT INTO
                   arMascDespesa
                   string_to_array(administracao.configuracao.valor,''.'')
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = ''masc_despesa''
               AND administracao.configuracao.exercicio = stExercicio;

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY dotacao
    LOOP
        reRegistro.vl_projeto   := orcamento.fn_totaliza_dotacao_pao(arMascDespesa[6],stDigitoProjeto,reRegistro.dotacao,inPosicao);
        reRegistro.vl_atividade := orcamento.fn_totaliza_dotacao_pao(arMascDespesa[6],stDigitoAtividade,reRegistro.dotacao,inPosicao);
        reRegistro.vl_operacao  := orcamento.fn_totaliza_dotacao_pao(arMascDespesa[6],stDigitoOperacao,reRegistro.dotacao,inPosicao);
        reRegistro.vl_total     := coalesce(reRegistro.vl_projeto,0) + coalesce(reRegistro.vl_atividade,0) + coalesce(reRegistro.vl_operacao,0);
        RETURN next reRegistro;
    END LOOP;


    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;

    RETURN;
END;
'language 'plpgsql';

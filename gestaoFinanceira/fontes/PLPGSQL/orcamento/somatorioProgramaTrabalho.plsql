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
* Casos de uso: uc-02.01.17
*/

CREATE OR REPLACE FUNCTION orcamento.fn_somatorio_programa_trabalho(varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
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
    nuValorReserva      NUMERIC(14,2);


BEGIN
        CREATE TEMPORARY TABLE tmp_relatorio(
             dotacao        VARCHAR(80)
            ,cod_despesa    INTEGER
            ,descricao      VARCHAR(200)
            ,vl_corrente    NUMERIC
            ,vl_capital     NUMERIC
            ,vl_total       NUMERIC
            ,nivel          INTEGER
        );

        stSql := 'CREATE TEMPORARY TABLE tmp_despesa AS
                SELECT
                     *
                     ,orcamento.fn_consulta_despesa(exercicio, cod_despesa, true)  as dotacao
                     ,orcamento.fn_consulta_despesa(exercicio, cod_despesa, false) as dotacao_sem_ponto
                     ,string_to_array( orcamento.fn_consulta_despesa(exercicio, cod_despesa, true) ,' || quote_literal('.') || ') as dotacao_array
                FROM    orcamento.despesa
                WHERE   exercicio = ' ||  quote_literal(stExercicio) || '  ' || stFiltro ;

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
                    stPao
                    nom_pao
                FROM   orcamento.pao
                WHERE  num_pao    = reRegistro.num_pao
                  AND  exercicio  = reRegistro.exercicio;
                INSERT INTO tmp_relatorio ( dotacao, descricao, nivel ) values ( arDotacao[1]||'.'||
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
            INSERT INTO tmp_relatorio ( dotacao, cod_despesa, descricao, nivel ) values ( reRegistro.dotacao,
                                                                                          reRegistro.cod_despesa,
                                                                                          stClassDespesa, 7 );
        END LOOP;

        SELECT INTO
                   inPosicao
                   cast(administracao.configuracao.valor as INTEGER)
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_posicao_digito_id'
               AND administracao.configuracao.exercicio = quote_literal(stExercicio);
        SELECT INTO
                   stDigitoProjeto
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_digitos_id_projeto'
               AND administracao.configuracao.exercicio = quote_literal(stExercicio);
        SELECT INTO
                   stDigitoAtividade
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_digitos_id_atividade'
               AND administracao.configuracao.exercicio = quote_literal(stExercicio);
        SELECT INTO
                   stDigitoOperacao
                   administracao.configuracao.valor
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'pao_digitos_id_oper_especiais'
               AND administracao.configuracao.exercicio = quote_literal(stExercicio);
        SELECT INTO
                   arMascDespesa
                   string_to_array(administracao.configuracao.valor,'.')
              FROM administracao.configuracao
             WHERE administracao.configuracao.cod_modulo = 8
               AND administracao.configuracao.parametro = 'masc_despesa'
               AND administracao.configuracao.exercicio = quote_literal(stExercicio);

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relatorio
        ORDER BY dotacao
    LOOP
        reRegistro.vl_corrente  := orcamento.fn_totaliza_programa_trabalho(reRegistro.dotacao,3);
        reRegistro.vl_capital   := orcamento.fn_totaliza_programa_trabalho(reRegistro.dotacao,4);
        nuValorReserva          := orcamento.fn_totaliza_programa_trabalho(reRegistro.dotacao,9);
        reRegistro.vl_total     := ( coalesce(reRegistro.vl_corrente,0) + coalesce(reRegistro.vl_capital,0) + coalesce(nuValorReserva,0));
        RETURN next reRegistro;
    END LOOP;


    DROP TABLE tmp_relatorio;
    DROP TABLE tmp_despesa;

    RETURN;
END;
$$language 'plpgsql';

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
* $Revision: 27254 $
* $Name$
* $Author: tonismar $
* $Date: 2007-12-19 17:21:09 -0200 (Qua, 19 Dez 2007) $
*
* Casos de uso: uc-02.01.18
*/

CREATE OR REPLACE FUNCTION orcamento.fn_relacao_despesa(varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    inNumOrgao          ALIAS FOR $3;
    inNumUnidade        ALIAS FOR $4;
    inCodRecurso        ALIAS FOR $5;
    stDestinacaoRecurso ALIAS FOR $6;
    inCodDetalhamento   ALIAS FOR $7;

    stSql               VARCHAR   := '';
    stMascClassDespesa  VARCHAR   := '';
    stMascRecurso       VARCHAR   := '';
    reRegistro          RECORD;

BEGIN
        SELECT INTO
                   stMascClassDespesa
                   configuracao.valor
         FROM   administracao.configuracao
        WHERE   configuracao.cod_modulo = 8
          AND   configuracao.parametro = 'masc_class_despesa'
          AND   configuracao.exercicio = stExercicio;

        SELECT INTO
                   stMascRecurso
                   configuracao.valor
         FROM   administracao.configuracao
        WHERE   configuracao.cod_modulo = 8
          AND   configuracao.parametro = 'masc_recurso'
          AND   configuracao.exercicio = stExercicio;

        stSql := 'CREATE TEMPORARY TABLE tmp_relacao AS
                SELECT
                    od.exercicio,
                    od.cod_despesa,
                    od.cod_entidade,
                    od.cod_programa,
                    od.cod_conta,
                    od.num_pao,
                    od.num_orgao,
                    od.num_unidade,
                    od.cod_recurso,
                    od.cod_funcao,
                    od.cod_subfuncao,
                    od.vl_original,
                    od.dt_criacao,

                    orcamento.fn_consulta_class_despesa(ocd.cod_conta, ocd.exercicio, ' || quote_literal(stMascClassDespesa) || ') AS classificacao,
                    ocd.descricao,
                    oru.cod_fonte as num_recurso,
                    oru.nom_recurso,
                    oo.nom_orgao,
                    ou.nom_unidade,
                    ofu.descricao AS nom_funcao,
                    osf.descricao AS nom_subfuncao,
                    opg.descricao AS nom_programa,
                    opao.nom_pao,
                    ppa.programa.num_programa::VARCHAR AS num_programa,
                    ppa.acao.num_acao::VARCHAR AS num_acao
                FROM
                    orcamento.conta_despesa ocd,
                    orcamento.despesa od
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
--                    orcamento.recurso(' || quote_literal(stExercicio) || ') oru,
                    ,orcamento.recurso oru,
                    orcamento.orgao oo,
                    orcamento.unidade ou,
                    orcamento.funcao ofu,
                    orcamento.subfuncao osf,
                    orcamento.programa opg,
                    orcamento.pao opao
                WHERE   ocd.cod_conta   = od.cod_conta
                AND     ocd.exercicio   = od.exercicio
                AND     od.cod_recurso  = oru.cod_recurso
                AND     od.exercicio    = oru.exercicio
                AND     od.num_orgao    = oo.num_orgao
                AND     od.exercicio    = oo.exercicio
                AND     ou.num_unidade  = od.num_unidade
                AND     ou.num_orgao    = od.num_orgao
                AND     ou.exercicio    = od.exercicio
                AND     od.cod_funcao   = ofu.cod_funcao
                AND     od.exercicio    = ofu.exercicio
                AND     od.cod_subfuncao= osf.cod_subfuncao
                AND     od.exercicio    = osf.exercicio
                AND     od.cod_programa = opg.cod_programa
                AND     od.exercicio    = opg.exercicio
                AND     od.num_pao      = opao.num_pao
                AND     od.exercicio    = opao.exercicio
                AND     od.exercicio    = ' || quote_literal(stExercicio) ;

                    --sw_fn_mascara_dinamica(' || quote_literal(stMascRecurso) || ', od.cod_recurso::character varying) AS num_recurso,
--                '' || stFiltro ;
                if (stFiltro is not NULL and stFiltro <> '') then
                    stSql := stSql || stFiltro;
                end if;
                
                if (inNumOrgao is not null and inNumOrgao<>'') then
                   stSql := stSql || ' AND oo.num_orgao = ' || inNumOrgao;
                end if;


                if (inNumUnidade is not null and inNumUnidade<>'') then
                   stSql := stSql || ' AND ou.num_unidade = ' || inNumUnidade;
                end if;

                if (inCodRecurso is not null and inCodRecurso<>'') then
                   stSql := stSql || ' AND od.cod_recurso = ' || inCodRecurso;
                end if;

                if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
--                        stSql := stSql || '' AND oru.masc_recurso_red like '' || quote_literal(stDestinacaoRecurso) || quote_literal(''%'');
                        stSql := stSql || ' AND oru.cod_fonte like '|| quote_literal(stDestinacaoRecurso) || quote_literal('%') || ' ';
                end if;
                
                if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
                        stSql := stSql || ' AND oru.cod_detalhamento = ' || inCodDetalhamento ||' ';
                end if;
        EXECUTE stSql;

    FOR reRegistro IN
        SELECT  *
        FROM    tmp_relacao
        ORDER BY cod_despesa
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_relacao;

    RETURN;
END;
$$language 'plpgsql';

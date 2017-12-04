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
* $Revision: 27033 $
* $Name$
* $Author: cako $
* $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $
*
* Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.7  2007/08/08 19:44:56  cako
Bug#9819#

Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_empenho_razao_credor(varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stCodEntidades              ALIAS FOR $1;
    stExercicioEmpenho          ALIAS FOR $2;
    stCodOrgao                  ALIAS FOR $3;
    stCodUnidade                ALIAS FOR $4;
    stCodElementoDispensa       ALIAS FOR $5;
    stCodElementoDispensaMasc   ALIAS FOR $6;
    stCodRecurso                ALIAS FOR $7;
    stDestinacaoRecurso         ALIAS FOR $8;
    inCodDetalhamento           ALIAS FOR $9;
    stCGM                       ALIAS FOR $10;
    stExercicio                 ALIAS FOR $11;
    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN
    --CRIA TABELA TEMPORÁRIA A MASCARA DE RECURSO DE TODOS OS EXERCICIOS
    CREATE TEMPORARY TABLE tmp_mascara AS
        SELECT
            administracao.configuracao.valor as valor,
            administracao.configuracao.parametro as parametro,
            administracao.configuracao.exercicio as exercicio
         FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = 'masc_recurso';

    --CRIA TABELA TEMPORÁRIA A MASCARA DE RECURSO DE TODOS OS EXERCICIOS
    INSERT INTO tmp_mascara
        SELECT
            administracao.configuracao.valor as valor,
            administracao.configuracao.parametro as parametro,
            administracao.configuracao.exercicio as exercicio
         FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = 'masc_despesa';

    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(ipe.vl_total)   as valor
        FROM
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
            e.cod_entidade      IN (' || stCodEntidades || ') AND
            e.exercicio::FLOAT <= '|| stExercicioEmpenho ||' AND

            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação PRE_EMPENHO : ITEM_PRE_EMPENHO
            pe.exercicio        = ipe.exercicio AND
            pe.cod_pre_empenho  = ipe.cod_pre_empenho 

        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(eai.vl_anulado) as valor
        FROM
            empenho.empenho                 as e,
            empenho.empenho_anulado         as ea,
            empenho.empenho_anulado_item    as eai
        WHERE
            e.cod_entidade      IN (' || stCodEntidades || ') AND
            e.exercicio::FLOAT <= '|| stExercicioEmpenho ||' AND

            --Ligação EMPENHO : EMPENHO ANULADO
            e.exercicio        = ea.exercicio AND
            e.cod_entidade     = ea.cod_entidade AND
            e.cod_empenho      = ea.cod_empenho AND

            --Ligação EMPENHO ANULADO : EMPENHO ANULADO ITEM
            ea.exercicio        = eai.exercicio AND
            ea.timestamp        = eai.timestamp AND
            ea.cod_entidade     = eai.cod_entidade AND
            ea.cod_empenho      = eai.cod_empenho AND
            EXTRACT(YEAR from ea.timestamp) <= '|| stExercicio ||'

        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(nli.vl_total)   as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_item    as nli
        WHERE
            e.cod_entidade      IN (' || stCodEntidades || ') AND
            e.exercicio::FLOAT <= '|| stExercicioEmpenho ||' AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
            nl.exercicio        = nli.exercicio AND
            nl.cod_nota         = nli.cod_nota AND
            nl.cod_entidade     = nli.cod_entidade AND
            EXTRACT(YEAR from nl.dt_liquidacao) <= '|| stExercicio ||'
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_anulado AS (
        SELECT
            e.cod_entidade       as entidade,
            e.cod_empenho        as empenho,
            e.exercicio          as exercicio,
            sum(nlia.vl_anulado) as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_item    as nli,
            empenho.nota_liquidacao_item_anulado nlia
        WHERE
            e.cod_entidade      IN (' || stCodEntidades || ') AND
            e.exercicio::FLOAT <= '|| stExercicioEmpenho ||' AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
            nl.exercicio        = nli.exercicio AND
            nl.cod_nota         = nli.cod_nota AND
            nl.cod_entidade     = nli.cod_entidade AND

            --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
            nli.exercicio       = nlia.exercicio AND
            nli.cod_nota        = nlia.cod_nota AND
            nli.cod_entidade    = nlia.cod_entidade AND
            nli.num_item        = nlia.num_item AND
            nli.cod_pre_empenho = nlia.cod_pre_empenho AND
            nli.exercicio_item  = nlia.exercicio_item AND
            EXTRACT(YEAR from nlia.timestamp) <= '|| stExercicio ||'

        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(nlp.vl_pago)    as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_paga    as nlp
        WHERE
            e.cod_entidade      IN (' || stCodEntidades || ') AND
            e.exercicio::FLOAT <= '|| stExercicioEmpenho ||' AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            nl.exercicio        = nlp.exercicio AND
            nl.cod_nota         = nlp.cod_nota AND
            nl.cod_entidade     = nlp.cod_entidade AND
            EXTRACT(YEAR from nlp.timestamp) <= '|| stExercicio ||'

        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            e.cod_entidade          as entidade,
            e.cod_empenho           as empenho,
            e.exercicio             as exercicio,
            sum(nlpa.vl_anulado)    as valor
        FROM
            empenho.empenho                         as e,
            empenho.nota_liquidacao                 as nl,
            empenho.nota_liquidacao_paga            as nlp,
            empenho.nota_liquidacao_paga_anulada    as nlpa
        WHERE
            e.cod_entidade          IN (' || stCodEntidades || ') AND
            e.exercicio::FLOAT <= '|| stExercicioEmpenho ||' AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio             = nl.exercicio_empenho AND
            e.cod_entidade          = nl.cod_entidade AND
            e.cod_empenho           = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            nl.exercicio            = nlp.exercicio AND
            nl.cod_nota             = nlp.cod_nota AND
            nl.cod_entidade         = nlp.cod_entidade AND

            --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
            nlp.exercicio           = nlpa.exercicio AND
            nlp.cod_nota            = nlpa.cod_nota AND
            nlp.cod_entidade        = nlpa.cod_entidade AND
            nlp.timestamp           = nlpa.timestamp AND
            EXTRACT(YEAR from nlpa.timestamp_anulada) <= '|| stExercicio ||'

        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )';
        EXECUTE stSql;

        stSql := '
        SELECT
            exercicio,
            numcgm,
            credor,
            to_char(dataEmpenho, ''dd/mm/yyyy'') as dtEmpenho,
            entidade,
            empenho,
            empenho.fn_mascara_despesa(exercicio, despesa) as despesa,
            coalesce(empenho.fn_somatorio_razao_credor_empenhado(empenho, entidade, exercicio),0.00) as empenhado,
            coalesce(empenho.fn_somatorio_razao_credor_anulado(empenho, entidade, exercicio),0.00) as anulado,
            (coalesce(empenho.fn_somatorio_razao_credor_liquidado(empenho, entidade, exercicio),0.00) - coalesce(empenho.fn_somatorio_razao_credor_liquidado_anulado(empenho, entidade, exercicio),0.00)) as liquidado,
            (coalesce(empenho.fn_somatorio_razao_credor_pago(empenho, entidade, exercicio),0.00) - coalesce(empenho.fn_somatorio_razao_credor_estornado(empenho, entidade, exercicio),0.00)) as pago
        FROM (
            SELECT
                e.exercicio             as exercicio,
                pe.cgm_beneficiario     as numcgm,
                cgm.nom_cgm             as credor,
                e.dt_empenho            as dataEmpenho,
                e.cod_entidade          as entidade,
                e.cod_empenho           as empenho,
                CASE WHEN pe.implantado = true THEN
                    rpe.num_orgao || ''.'' || rpe.num_unidade || ''.'' || rpe.cod_funcao || ''.'' || rpe.cod_subfuncao || ''.'' || rpe.cod_programa || ''.'' || rpe.num_pao || ''.'' || RPAD(rpe.cod_estrutural,14,''0'') || ''.'' || rpe.recurso
                ELSE
                    ped_d_cd.num_orgao || ''.'' || ped_d_cd.num_unidade || ''.'' || ped_d_cd.cod_funcao || ''.'' || ped_d_cd.cod_subfuncao || ''.'' || ped_d_cd.num_programa || ''.'' || ped_d_cd.num_acao || ''.'' || replace(ped_d_cd.cod_estrutural,''.'','''') || ''.'' || ped_d_cd.cod_recurso
                END as despesa
            FROM
                empenho.empenho     as e,
                sw_cgm              as cgm,
                empenho.pre_empenho as pe
                LEFT JOIN empenho.restos_pre_empenho as rpe ON pe.exercicio = rpe.exercicio AND  pe.cod_pre_empenho = rpe.cod_pre_empenho
                LEFT JOIN (
                    SELECT
                        ped.exercicio, ppa.programa.num_programa, ppa.acao.num_acao, rec.masc_recurso_red, rec.cod_detalhamento, ped.cod_pre_empenho, d.cod_programa, d.num_pao, d.num_orgao,d.num_unidade, d.cod_recurso, d.cod_funcao, d.cod_subfuncao, cd.cod_estrutural
                    FROM
                        empenho.pre_empenho_despesa as ped
                        , orcamento.despesa as d
                          JOIN orcamento.recurso(''' || stExercicio || ''') as rec
                            ON ( d.cod_recurso = rec.cod_recurso
                           AND d.exercicio = rec.exercicio     )
                          JOIN orcamento.programa_ppa_programa
                            ON programa_ppa_programa.cod_programa = d.cod_programa
                           AND programa_ppa_programa.exercicio   = d.exercicio
                          JOIN ppa.programa
                            ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                          JOIN orcamento.pao_ppa_acao
                            ON pao_ppa_acao.num_pao = d.num_pao
                           AND pao_ppa_acao.exercicio = d.exercicio
                          JOIN ppa.acao 
                            ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                        , orcamento.conta_despesa as cd
                    WHERE
                        ped.cod_despesa = d.cod_despesa and ped.exercicio = d.exercicio and ped.cod_conta = cd.cod_conta and d.exercicio = cd.exercicio
                ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho

            WHERE
                    e.cod_entidade          IN (' || stCodEntidades || ')
                AND pe.cgm_beneficiario     = ' || stCGM || '

                AND e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho

                AND pe.cgm_beneficiario = cgm.numcgm ';

                if (stExercicioEmpenho is not null and stExercicioEmpenho <> '') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.exercicio::FLOAT = '|| stExercicioEmpenho ||' ELSE ped_d_cd.exercicio::FLOAT = '|| stExercicioEmpenho ||' END ';
                end if;

                if (stCodOrgao is not null and stCodOrgao<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.num_orgao = '|| stCodOrgao ||' ELSE ped_d_cd.num_orgao = '|| stCodOrgao ||' END ';
                end if;

                if (stCodUnidade is not null and stCodUnidade<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.num_unidade = '|| stCodUnidade ||' ELSE ped_d_cd.num_unidade = '|| stCodUnidade ||' END ';
                end if;

                if (stCodRecurso is not null and stCodRecurso<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.recurso = '|| stCodRecurso ||' ELSE ped_d_cd.cod_recurso = '|| stCodRecurso ||' END ';
                end if;

                if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = false THEN ped_d_cd.masc_recurso_red like '''|| stDestinacaoRecurso ||'%'||'''  END ';
                end if;

                if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = false THEN ped_d_cd.cod_detalhamento = '|| inCodDetalhamento ||' END ';
                end if;

                if (stCodElementoDispensa is not null and stCodElementoDispensa<>'') then
                    stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.cod_estrutural like rtrim(''' || stCodElementoDispensaMasc || ''',''0'') || ''%'' ELSE ped_d_cd.cod_estrutural like publico.fn_mascarareduzida(''' || stCodElementoDispensa || ''')|| ''%'' END ';
                end if;

            stSql := stSql || '
            ORDER BY
                e.exercicio,
                pe.cgm_beneficiario,
                cgm.nom_cgm,
                to_char(e.dt_empenho, ''dd/mm/yyyy''),
                e.cod_entidade,
                e.cod_empenho,
                despesa
        ) as tbl
        GROUP BY
            exercicio,
            numcgm,
            credor,
            dataEmpenho,
            entidade,
            empenho,
            despesa
        ORDER BY
            exercicio,
            numcgm,
            credor,
            dataEmpenho,
            entidade,
            empenho,
            despesa
            ';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_mascara;

    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_liquidado_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;

    RETURN;
END;
$$ language 'plpgsql';

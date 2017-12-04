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
 * PL para verificar se as despesas do orcamento estao vinculadas com a contabilidade 
 * Data de Criação   : 07/01/2009


 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Henrique Boaventura 
 
 * @package URBEM
 * @subpackage 

 $Id:$
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_verifica_vinculo_restos(varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio                     ALIAS FOR $1;

    stDataInicial       VARCHAR   := '''';
    stDataFinal         VARCHAR   := '''';
    stDataSituacao      VARCHAR   := '''';
    stCodEntidades      VARCHAR   := '''';
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
    stDataInicial  := ''01/01/'' || stExercicio;
    stDataFinal    := ''31/12/'' || stExercicio;
    stDataSituacao := ''31/12/'' || stExercicio;

    -------------------------------------
    -- Retorna as entidades do sistema --
    -------------------------------------
    SELECT ARRAY_TO_STRING(ARRAY(SELECT CAST(entidade.cod_entidade AS VARCHAR)
                                   FROM orcamento.entidade
                                  WHERE entidade.exercicio = stExercicio
                                    AND EXISTS ( SELECT 1
                                                   FROM contabilidade.conta_lancamento_rp
                                                  WHERE entidade.exercicio    = conta_lancamento_rp.exercicio
                                                    AND entidade.cod_entidade = conta_lancamento_rp.cod_entidade
                                               )
                           ),'','')
      INTO stCodEntidades;


    stSql := ''CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(ipe.vl_total)   as valor
        FROM
            empenho.empenho             as e,
            empenho.item_pre_empenho    as ipe,
            empenho.pre_empenho         as pe
            LEFT JOIN empenho.pre_empenho_despesa as ped on(
                pe.exercicio        = ped.exercicio
            AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            )
            LEFT JOIN orcamento.despesa as ode on(
                ped.exercicio       = ode.exercicio
            AND ped.cod_despesa     = ode.cod_despesa
            )

        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '''''' || stExercicio || '''''' AND 

            e.dt_empenho >= to_date('''''' || stDataInicial || ''''''::varchar,''''dd/mm/yyyy'''') AND

            e.dt_empenho <= to_date('''''' || stDataFinal || ''''''::varchar,''''dd/mm/yyyy'''') AND

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
        )'';

        EXECUTE stSql;

   stSql := ''CREATE TEMPORARY TABLE tmp_anulado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(eai.vl_anulado) as valor
        FROM
            empenho.empenho                 as e,
            empenho.empenho_anulado         as ea,
            empenho.empenho_anulado_item    as eai,
            empenho.pre_empenho             as pe
            LEFT JOIN empenho.pre_empenho_despesa as ped on(
                pe.exercicio        = ped.exercicio
            AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            )
            LEFT JOIN orcamento.despesa as ode on(
                ped.exercicio       = ode.exercicio
            AND ped.cod_despesa     = ode.cod_despesa
            )

        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '''''' || stExercicio || '''''' AND '';

        if (stDataInicial is not null and stDataInicial<>'''') then
            stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicial || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataFinal is not null and stDataFinal<>'''') then
            stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinal || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataSituacao is not null and stDataSituacao<>'''') then
           stSql := stSql || '' to_date( to_char( ea.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date ('''''' || stDataSituacao || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;


        stSql := stSql || ''
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : EMPENHO ANULADO
            e.exercicio        = ea.exercicio AND
            e.cod_entidade     = ea.cod_entidade AND
            e.cod_empenho      = ea.cod_empenho AND

            --Ligação EMPENHO ANULADO : EMPENHO ANULADO ITEM
            ea.exercicio        = eai.exercicio AND
            ea.timestamp        = eai.timestamp AND
            ea.cod_entidade     = eai.cod_entidade AND
            ea.cod_empenho      = eai.cod_empenho
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )'';
        EXECUTE stSql;

   stSql := ''CREATE TEMPORARY TABLE tmp_liquidado AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(nli.vl_total)   as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_item    as nli,
            empenho.pre_empenho             as pe
            LEFT JOIN empenho.pre_empenho_despesa as ped on(
                pe.exercicio        = ped.exercicio
            AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            )
            LEFT JOIN orcamento.despesa as ode on(
                ped.exercicio       = ode.exercicio
            AND ped.cod_despesa     = ode.cod_despesa
            )
        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '''''' || stExercicio || '''''' AND '';

        if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicial || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinal || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataSituacao is not null and stDataSituacao<>'''') then
           stSql := stSql || '' nl.dt_liquidacao <= to_date('''''' || stDataSituacao || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;

        stSql := stSql || ''
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
            nl.exercicio        = nli.exercicio AND
            nl.cod_nota         = nli.cod_nota AND
            nl.cod_entidade     = nli.cod_entidade
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )'';
        EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_liquidado_anulado AS (
        SELECT
            e.cod_entidade       as entidade,
            e.cod_empenho        as empenho,
            e.exercicio          as exercicio,
            sum(nlia.vl_anulado) as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_item    as nli,
            empenho.nota_liquidacao_item_anulado nlia,
            empenho.pre_empenho             as pe
            LEFT JOIN empenho.pre_empenho_despesa as ped on(
                pe.exercicio        = ped.exercicio
            AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            )
            LEFT JOIN orcamento.despesa as ode on(
                ped.exercicio       = ode.exercicio
            AND ped.cod_despesa     = ode.cod_despesa
            )

        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '''''' || stExercicio || '''''' AND '';

       if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicial || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinal || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataSituacao is not null and stDataSituacao<>'''') then
           stSql := stSql || '' to_date(to_char(nlia.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date('''''' || stDataSituacao || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;

        stSql := stSql || ''
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

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
            nli.exercicio_item  = nlia.exercicio_item
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )'';
        EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
            e.cod_entidade      as entidade,
            e.cod_empenho       as empenho,
            e.exercicio         as exercicio,
            sum(nlp.vl_pago)    as valor
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_paga    as nlp,
            empenho.pre_empenho             as pe
            LEFT JOIN empenho.pre_empenho_despesa as ped on(
                pe.exercicio        = ped.exercicio
            AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            )
            LEFT JOIN orcamento.despesa as ode on(
                ped.exercicio       = ode.exercicio
            AND ped.cod_despesa     = ode.cod_despesa
            )

        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '''''' || stExercicio || '''''' AND '';

       if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicial || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinal || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataSituacao is not null and stDataSituacao<>'''') then
           stSql := stSql || '' to_date(to_char(nlp.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date('''''' || stDataSituacao || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;


        stSql := stSql || ''
             --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            nl.exercicio        = nlp.exercicio AND
            nl.cod_nota         = nlp.cod_nota AND
            nl.cod_entidade     = nlp.cod_entidade
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )'';
        EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            e.cod_entidade          as entidade,
            e.cod_empenho           as empenho,
            e.exercicio             as exercicio,
            sum(nlpa.vl_anulado)    as valor
        FROM
            empenho.empenho                         as e,
            empenho.nota_liquidacao                 as nl,
            empenho.nota_liquidacao_paga            as nlp,
            empenho.nota_liquidacao_paga_anulada    as nlpa,
            empenho.pre_empenho             as pe
            LEFT JOIN empenho.pre_empenho_despesa as ped on(
                pe.exercicio        = ped.exercicio
            AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            )
            LEFT JOIN orcamento.despesa as ode on(
                ped.exercicio       = ode.exercicio
            AND ped.cod_despesa     = ode.cod_despesa
            )

        WHERE
            e.cod_entidade          IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '''''' || stExercicio || '''''' AND '';

       if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicial || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinal || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataSituacao is not null and stDataSituacao<>'''') then
           stSql := stSql || '' to_date(to_char(nlpa.timestamp_anulada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date('''''' || stDataSituacao || ''''''::varchar,''''dd/mm/yyyy'''') AND'';
        end if;


        stSql := stSql || ''
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

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
            nlp.timestamp           = nlpa.timestamp
        GROUP BY
            e.cod_entidade,
            e.cod_empenho,
            e.exercicio
        )'';
        EXECUTE stSql;

stSql := ''

SELECT * FROM (
    SELECT
    	cod_entidade,
	    cast(exercicio AS varchar) as exercicio, 
        CAST(cod_plano as varchar)          AS cod_plano_debito,
        cod_estrutural
    FROM (
        SELECT
            ode.cod_recurso,
            ode.nom_recurso,
            e.cod_empenho,
            e.cod_entidade,
            e.exercicio,
    	    cpa.cod_plano,
            conta_despesa.cod_estrutural
        FROM
            empenho.empenho      as e
           , sw_cgm              as cgm
           , empenho.pre_empenho as pe
            LEFT JOIN empenho.pre_empenho_despesa as ped on(
                pe.exercicio        = ped.exercicio
            AND pe.cod_pre_empenho  = ped.cod_pre_empenho
            )
            LEFT JOIN( SELECT 
                              despesa.exercicio
                            , despesa.cod_despesa
                            , recurso.cod_recurso
                            , recurso.nom_recurso
                         FROM orcamento.despesa
                              LEFT JOIN orcamento.recurso
                                     ON (     recurso.exercicio   = despesa.exercicio
                                          AND recurso.cod_recurso = despesa.cod_recurso ) ) as ode 
                   ON(     ped.exercicio       = ode.exercicio
                       AND ped.cod_despesa     = ode.cod_despesa )

     LEFT JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = ped.exercicio
           AND conta_despesa.cod_conta = ped.cod_conta

     LEFT JOIN contabilidade.plano_conta AS CPC ON(
           CPC.exercicio = conta_despesa.exercicio AND
           CPC.cod_estrutural = ''''3.''''||conta_despesa.cod_estrutural
     )
     LEFT JOIN contabilidade.plano_analitica AS CPA ON(
           CPA.exercicio = CPC.exercicio AND
           CPA.cod_conta = CPC.cod_conta
     )


        WHERE
                e.cod_entidade          IN ('' || stCodEntidades || '')
                AND e.exercicio         =   '''''' || stExercicio || '''''' '';

                stSql := stSql || ''
                AND e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho

                AND pe.cgm_beneficiario = cgm.numcgm '';

                if (stDataInicial is not null and stDataInicial<>'''') then
                   stSql := stSql || '' AND e.dt_empenho >= to_date('''''' || stDataInicial || ''''''::varchar,''''dd/mm/yyyy'''') '';
                end if;

                if (stDataFinal is not null and stDataFinal<>'''') then
                   stSql := stSql || '' AND e.dt_empenho <= to_date('''''' || stDataFinal || ''''''::varchar,''''dd/mm/yyyy'''') '';
                end if;

           stSql := stSql || ''
            GROUP BY
                ode.cod_recurso,
                ode.nom_recurso,
                e.cod_empenho,
                e.cod_entidade,
                e.exercicio,
                conta_despesa.cod_estrutural,
		cpa.cod_plano
        ) as tbl
        GROUP BY
            cod_recurso,
            nom_recurso,
	    cod_entidade,
	    exercicio,
	    cod_plano,
        cod_estrutural
        ) as tmp
    WHERE cod_plano_debito IS NULL;
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_liquidado_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;

END;
'language 'plpgsql';

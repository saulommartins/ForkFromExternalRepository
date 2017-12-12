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
* $Revision: 27052 $
* $Name$
* $Author: cako $
* $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $
*
* Casos de uso: uc-02.03.26
*/

/*
$Log$
Revision 1.7  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_relatorio_programacao_pagamentos(
    VARCHAR, 
    VARCHAR,
    VARCHAR,
    VARCHAR, 
    VARCHAR, 
    VARCHAR, 
    VARCHAR, 
    VARCHAR, 
    VARCHAR, 
    VARCHAR) RETURNS SETOF RECORD AS '

DECLARE
    stFiltro                        ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    stExercicio                     ALIAS FOR $3;
    stDataInicial                   ALIAS FOR $4;
    stDataFinal                     ALIAS FOR $5;
    inCGM                           ALIAS FOR $6;
    inCodDespesa                    ALIAS FOR $7;
    inCodRecurso                    ALIAS FOR $8;
    stDestinacaoRecurso             ALIAS FOR $9;
    inCodDetalhamento               ALIAS FOR $10;

    stSql               VARCHAR := '''';
    stMascRecurso       VARCHAR := '''';
    reRegistro          RECORD;

BEGIN

   stSql := ''CREATE TEMPORARY TABLE tmp_liquidado AS (
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
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

       if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' nl.dt_vencimento >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' nl.dt_vencimento <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        stSql := stSql || ''

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
            empenho.nota_liquidacao_item_anulado nlia
        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '' || quote_literal(stExercicio) ||'' AND '';

       if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' nl.dt_vencimento >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' nl.dt_vencimento <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        stSql := stSql || ''
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

    stSql :=''CREATE TEMPORARY TABLE tmp_pago AS (
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
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

       if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' nl.dt_vencimento >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' nl.dt_vencimento <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        stSql := stSql || ''

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
            empenho.nota_liquidacao_paga_anulada    as nlpa
        WHERE
            e.cod_entidade          IN ('' || stCodEntidades || '') AND
            e.exercicio         =       '' || quote_literal(stExercicio) || '' AND '';

       if (stDataInicial is not null and stDataInicial<>'''') then
          stSql := stSql || '' nl.dt_vencimento >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinal is not null and stDataFinal<>'''') then
          stSql := stSql || '' nl.dt_vencimento <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        stSql := stSql || ''

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

        SELECT INTO
                   stMascRecurso
                   administracao.configuracao.valor
        FROM   administracao.configuracao
        WHERE   administracao.configuracao.cod_modulo = 8
          AND   administracao.configuracao.parametro = ''masc_recurso''
          AND   administracao.configuracao.exercicio = stExercicio;

stSql := ''
SELECT * FROM (
    SELECT
        cod_entidade,
        cod_empenho,
        exercicio,
        dt_vencimento,
        cgm,
        credor,
        --sw_fn_mascara_dinamica(''''''||stMascRecurso||'''''', cast(cod_recurso as varchar)) AS cod_recurso,
        cod_recurso, -- Já formatado com a máscara
        nom_recurso,
        (sum(liquidado) - sum(estornoliquidado)) - (sum(pago) - sum(estornopago)) as apagar
    FROM (
        SELECT
            e.cod_entidade          as cod_entidade,
            e.cod_empenho           as cod_empenho,
            e.exercicio             as exercicio,
            to_char(enl.dt_vencimento, ''''dd/mm/yyyy'''') as dt_vencimento,
            ore.masc_recurso_red as cod_recurso,
            ore.nom_recurso,
            pe.cgm_beneficiario     as cgm,
            cgm.nom_cgm             as credor,
          coalesce(empenho.fn_somatorio_razao_credor_liquidado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as liquidado,
          coalesce(empenho.fn_somatorio_razao_credor_liquidado_anulado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as estornoliquidado,
          coalesce(empenho.fn_somatorio_razao_credor_pago(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as pago,
          coalesce(empenho.fn_somatorio_razao_credor_estornado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as estornopago
        FROM
            empenho.empenho             as e,
            empenho.nota_liquidacao     as enl,
            sw_cgm                      as cgm,
            empenho.pre_empenho         as pe,
            empenho.pre_empenho_despesa as ped,
            orcamento.despesa           as ode,
            orcamento.recurso('''''' || stExercicio || '''''') as ore

        WHERE
                e.cod_entidade          IN ('' || stCodEntidades || '')
                AND e.exercicio         =   '' || quote_literal(stExercicio) || '' '';

                stSql := stSql || ''
                AND e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho

                AND e.cod_empenho       = enl.cod_empenho
                AND e.exercicio         = enl.exercicio_empenho
                AND e.cod_entidade      = enl.cod_entidade

                AND pe.cod_pre_empenho  = ped.cod_pre_empenho
                AND pe.exercicio        = ped.exercicio

                AND ped.cod_despesa     = ode.cod_despesa
                AND ped.exercicio       = ode.exercicio

                AND ode.cod_recurso     = ore.cod_recurso
                AND ode.exercicio       = ore.exercicio

                AND pe.cgm_beneficiario = cgm.numcgm '';

                if (stDataInicial is not null and stDataInicial<>'''') then
                   stSql := stSql || '' AND enl.dt_vencimento >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') '';
                end if;

                if (stDataFinal is not null and stDataFinal<>'''') then
                   stSql := stSql || '' AND enl.dt_vencimento <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') '';
                end if;

                if (inCGM is not null and inCGM<>'''') then
                    stSql := stSql || '' AND pe.cgm_beneficiario     = '' || inCGM || '' '';
                end if;

                if (inCodRecurso is not null and inCodRecurso<>'''') then
                    stSql := stSql || '' AND ore.cod_recurso         = '' || inCodRecurso || '' '';
                end if;

                if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '''') then
                    stSql := stSql || '' AND ore.masc_recurso_red like ''''''|| stDestinacaoRecurso || ''%'' ||'''''' '';
                end if;

                if (inCodDetalhamento is not null and inCodDetalhamento <> '''') then
                        stSql := stSql || '' AND ore.cod_detalhamento = ''|| inCodDetalhamento ||'' '';
                end if;

                if (inCodDespesa is not null and inCodDespesa<>'''') then
                    stSql := stSql || '' AND ode.cod_despesa     = '' || inCodDespesa || '' '';
                end if;


           stSql := stSql || ''
            GROUP BY
                e.cod_entidade,
                e.cod_empenho,
                e.exercicio,
                enl.dt_vencimento,
                pe.cgm_beneficiario,
                cgm.nom_cgm,
                ore.masc_recurso_red,
                ore.nom_recurso
            ORDER BY
                enl.dt_vencimento,
                ore.masc_recurso_red,
                ore.nom_recurso,
                e.cod_entidade,
                e.cod_empenho,
                e.exercicio,
                cgm.nom_cgm,
                pe.cgm_beneficiario
        ) as tbl
        GROUP BY
            dt_vencimento,
            cod_recurso,
            nom_recurso,
            cod_entidade,
            cod_empenho,
            exercicio,
            cgm,
            credor
        ) as tmp
        WHERE apagar>0
        '';


    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_liquidado_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;

    RETURN;

END;

'LANGUAGE 'plpgsql';


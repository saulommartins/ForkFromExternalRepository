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
* CNM Confederação Nacional de Municípios
* www.cnm.org.br
*
* $Revision: 27033 $
* $Name$
* $Author: cako $
* $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $
*
* Casos de uso: uc-02.03.34
*/

/*

$Log: 

*/

CREATE OR REPLACE FUNCTION empenho.fn_situacao_autorizacao_empenho(varchar, varchar,varchar, varchar,varchar, varchar,varchar, varchar,varchar, varchar,varchar, varchar, varchar,varchar,varchar,varchar,varchar, varchar,varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS '
DECLARE
    stCodEntidades                  ALIAS FOR $1;
    stExercicio                     ALIAS FOR $2;
    stDataInicialEmissao            ALIAS FOR $3;
    stDataFinalEmissao              ALIAS FOR $4;
    stDataInicialAnulacao           ALIAS FOR $5;
    stDataFinalAnulacao             ALIAS FOR $6;
    stDataInicialLiquidacao         ALIAS FOR $7;
    stDataFinalLiquidacao           ALIAS FOR $8;
    stDataInicialEstornoLiquidacao  ALIAS FOR $9;
    stDataFinalEstornoLiquidacao    ALIAS FOR $10;
    stDataInicialPagamento          ALIAS FOR $11;
    stDataFinalPagamento            ALIAS FOR $12;
    stDataInicialEstornoPagamento   ALIAS FOR $13;
    stDataFinalEstornoPagamento     ALIAS FOR $14;
    inCodEmpenhoInicial             ALIAS FOR $15;
    inCodEmpenhoFinal               ALIAS FOR $16;
    inCodDotacao                    ALIAS FOR $17;
    stCodDespesa                    ALIAS FOR $18;
    inCodRecurso                    ALIAS FOR $19;
    stDestinacaoRecurso             ALIAS FOR $20;
    inCodDetalhamento               ALIAS FOR $21;
    inNumOrgao                      ALIAS FOR $22;
    inNumUnidade                    ALIAS FOR $23;
    inOrdenacao                     ALIAS FOR $24;
    inCGM                           ALIAS FOR $25;
    inSituacao                      ALIAS FOR $26;
    inAutorizacao                   ALIAS FOR $27;

    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
    stSql := ''CREATE TEMPORARY TABLE tmp_autorizado AS (
        SELECT
            ae.cod_autorizacao      as autorizado,
            ae.cod_entidade       as entidade,
            ae.exercicio         as exercicio,
            sum(ipe.vl_total)   as valor
        FROM
            empenho.pre_empenho         as pe
        LEFT JOIN
            empenho.empenho             as e
        ON
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho,

            empenho.item_pre_empenho    as ipe,
            empenho.autorizacao_empenho as ae
        WHERE
            ae.cod_entidade      IN ('' || stCodEntidades || '') AND 
            ae.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

            if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
               stSql := stSql || '' ae.dt_autorizacao >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
            end if;

            if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
               stSql := stSql || '' ae.dt_autorizacao <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
            end if;

            if( inAutorizacao is not null and inAutorizacao <> '''') then
                stSql := stSql || '' ae.cod_autorizacao <= '' || inAutorizacao || '' AND '';
            end if;

        stSql := stSql || ''

            --Ligação PRE_EMPENHO : ITEM_PRE_EMPENHO
            pe.exercicio        = ipe.exercicio AND
            pe.cod_pre_empenho  = ipe.cod_pre_empenho AND

            ae.cod_pre_empenho  = pe.cod_pre_empenho AND
            ae.exercicio        = pe.exercicio

        GROUP BY
            ae.cod_autorizacao,
            ae.cod_entidade,
            ae.exercicio
        )'';

        EXECUTE stSql;

    stSql := ''CREATE TEMPORARY TABLE tmp_autorizado_anulado AS (
        SELECT
            ae.cod_autorizacao      as autorizado,
            ae.cod_entidade       as entidade,
            ae.exercicio         as exercicio,
            sum(ipe.vl_total)   as valor
        FROM
            empenho.pre_empenho         as pe
        LEFT JOIN
            empenho.empenho             as e
        ON
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho,

            empenho.item_pre_empenho    as ipe,
            empenho.autorizacao_empenho as ae
        LEFT JOIN
            empenho.autorizacao_anulada as aa
        ON
            ae.exercicio = aa.exercicio AND
            ae.cod_entidade = aa.cod_entidade AND
            ae.cod_autorizacao = aa.cod_autorizacao 
        WHERE
            aa.cod_autorizacao is not null AND

            ae.cod_entidade      IN ('' || stCodEntidades || '') AND
            ae.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

            if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
               stSql := stSql || '' ae.dt_autorizacao >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
            end if;

            if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
               stSql := stSql || '' ae.dt_autorizacao <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
            end if;

            if( inAutorizacao is not null and inAutorizacao <> '''') then
                stSql := stSql || '' ae.cod_autorizacao <= '' || inAutorizacao || '' AND '';
            end if;

        stSql := stSql || ''

            --Ligação PRE_EMPENHO : ITEM_PRE_EMPENHO
            pe.exercicio        = ipe.exercicio AND
            pe.cod_pre_empenho  = ipe.cod_pre_empenho AND

            ae.cod_pre_empenho  = pe.cod_pre_empenho AND
            ae.exercicio        = pe.exercicio

        GROUP BY
            ae.cod_autorizacao,
            ae.cod_entidade,
            ae.exercicio
        )'';

        EXECUTE stSql;



    stSql := ''CREATE TEMPORARY TABLE tmp_empenhado AS (
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
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

            if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
               stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
            end if;

            if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
               stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
            end if;

            if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <>'''') then
               stSql := stSql || '' e.cod_empenho >= '' || inCodEmpenhoInicial || '' AND '';
            end if;

            if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <>'''') then
               stSql := stSql || '' e.cod_empenho <= '' || inCodEmpenhoFinal || '' AND '';
            end if;

        stSql := stSql || ''
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
            empenho.empenho_anulado_item    as eai
        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

       if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataInicialAnulacao is not null and stDataInicialAnulacao<>'''') then
           stSql := stSql || '' to_date( to_char( ea.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) >= to_date('''''' || stDataInicialAnulacao || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataFinalAnulacao is not null and stDataFinalAnulacao<>'''') then
           stSql := stSql || '' to_date( to_char( ea.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date('''''' || stDataFinalAnulacao || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <>'''') then
           stSql := stSql || '' e.cod_empenho >= '' || inCodEmpenhoInicial || '' AND'';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <>'''') then
           stSql := stSql || '' e.cod_empenho <= '' || inCodEmpenhoFinal || '' AND'';
        end if;

        stSql := stSql || ''

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
            empenho.nota_liquidacao_item    as nli
        WHERE
            e.cod_entidade      IN ('' || stCodEntidades || '') AND
            e.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

       if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataInicialLiquidacao is not null and stDataInicialLiquidacao<>'''') then
           stSql := stSql || '' nl.dt_liquidacao >= to_date('''''' || stDataInicialLiquidacao || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataFinalLiquidacao is not null and stDataFinalLiquidacao<>'''') then
           stSql := stSql || '' nl.dt_liquidacao <= to_date('''''' || stDataFinalLiquidacao || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <>'''') then
           stSql := stSql || '' e.cod_empenho >= '' || inCodEmpenhoInicial || '' AND'';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <>'''') then
           stSql := stSql || '' e.cod_empenho <= '' || inCodEmpenhoFinal || '' AND'';
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
            e.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

       if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataInicialEstornoLiquidacao is not null and stDataInicialEstornoLiquidacao<>'''') then
           stSql := stSql || '' to_date(to_char(nlia.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') >= to_date('''''' || stDataInicialEstornoLiquidacao || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataFinalEstornoLiquidacao is not null and stDataFinalEstornoLiquidacao<>'''') then
           stSql := stSql || '' to_date(to_char(nlia.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date('''''' || stDataFinalEstornoLiquidacao || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <>'''') then
           stSql := stSql || '' e.cod_empenho >= '' || inCodEmpenhoInicial || '' AND'';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <>'''') then
           stSql := stSql || '' e.cod_empenho <= '' || inCodEmpenhoFinal || '' AND'';
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

    stSql := ''CREATE TEMPORARY TABLE tmp_pago AS (
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

       if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataInicialPagamento is not null and stDataInicialPagamento<>'''') then
           stSql := stSql || '' to_date(to_char(nlp.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') >= to_date('''''' || stDataInicialPagamento || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataFinalPagamento is not null and stDataFinalPagamento<>'''') then
           stSql := stSql || '' to_date(to_char(nlp.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date('''''' || stDataFinalPagamento || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <>'''') then
           stSql := stSql || '' e.cod_empenho >= '' || inCodEmpenhoInicial || '' AND'';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <>'''') then
           stSql := stSql || '' e.cod_empenho <= '' || inCodEmpenhoFinal || '' AND'';
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
            e.exercicio         =   '' || quote_literal(stExercicio)|| '' AND '';

       if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

       if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
          stSql := stSql || '' e.dt_empenho <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') AND'';
       end if;

        if (stDataInicialEstornoPagamento is not null and stDataInicialEstornoPagamento<>'''') then
           stSql := stSql || '' to_date(to_char(nlpa.timestamp_anulada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') >= to_date('''''' || stDataInicialEstornoPagamento || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (stDataFinalEstornoPagamento is not null and stDataFinalEstornoPagamento<>'''') then
           stSql := stSql || '' to_date(to_char(nlpa.timestamp_anulada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date('''''' || stDataFinalEstornoPagamento || '''''',''''dd/mm/yyyy'''') AND'';
        end if;

        if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <>'''') then
           stSql := stSql || '' e.cod_empenho >= '' || inCodEmpenhoInicial || '' AND'';
        end if;

        if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <>'''') then
           stSql := stSql || '' e.cod_empenho <= '' || inCodEmpenhoFinal || '' AND'';
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


        stSql := ''
		
SELECT 
	*
FROM (
	SELECT
		autorizacao,
		exercicio,
		emissao,
		entidade,
		credor,
        empenho,
		sum(autorizado)         as autorizado,
        sum(autorizado_anulado) as autorizado_anulado,
        sum(autorizado) - sum(autorizado_anulado) as saldoautorizado,
		(sum(liquidado) - sum(estornoliquidado))   as liquidado,
		(sum(pago) - sum(estornopago))             as pago,
        (sum(empenhado) - sum(anulado)) - (sum(pago) - sum(estornopago)) as empenhadoapagar
	FROM (
		SELECT
			ae.cod_autorizacao as autorizacao,
			ae.exercicio as exercicio,
			to_char(ae.dt_autorizacao, ''''dd/mm/yyyy'''') as emissao,
			ae.cod_entidade as entidade,
			cgm.nom_cgm as credor,
			coalesce(empenho.fn_somatorio_razao_credor_autorizado(ae.cod_autorizacao, ae.cod_entidade, ae.exercicio),0.00) as autorizado,
            coalesce(empenho.fn_somatorio_razao_credor_autorizado_anulado(ae.cod_autorizacao, ae.cod_entidade, ae.exercicio),0.00) as autorizado_anulado,
			e.empenhado,
			e.anulado,
			e.liquidado,
			e.estornoliquidado,
			e.pago,
			e.estornopago,
            e.cod_empenho as empenho
		FROM
			empenho.autorizacao_empenho as ae,
			sw_cgm as cgm,
			empenho.pre_empenho as pe
		LEFT JOIN
			( select
				e.exercicio,
				e.cod_pre_empenho,
                e.cod_empenho,
				coalesce(empenho.fn_somatorio_razao_credor_empenhado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as empenhado,
				coalesce(empenho.fn_somatorio_razao_credor_anulado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as anulado,
				coalesce(empenho.fn_somatorio_razao_credor_liquidado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as liquidado,
				coalesce(empenho.fn_somatorio_razao_credor_liquidado_anulado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as estornoliquidado,
				coalesce(empenho.fn_somatorio_razao_credor_pago(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as pago,
				coalesce(empenho.fn_somatorio_razao_credor_estornado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as estornopago
			  from
				empenho.empenho as e
			) as e
		ON
			pe.exercicio	=	e.exercicio and
			pe.cod_pre_empenho	= e.cod_pre_empenho
		LEFT JOIN
			( select
				d.cod_despesa,
				d.num_orgao,
				d.cod_programa,
				d.num_pao,
				d.num_unidade,
				d.cod_recurso,
				d.cod_funcao,
				d.cod_subfuncao,
				d.cod_conta,
				cd.cod_estrutural,
				cd.descricao,
				ped.exercicio,
				ped.cod_pre_empenho,
                rec.masc_recurso_red,
                rec.cod_detalhamento
			from
				empenho.pre_empenho_despesa as ped,
				orcamento.despesa as d
                JOIN orcamento.recurso(''''''|| stExercicio ||'''''') as rec
                    ON ( rec.cod_recurso = d.cod_recurso       
                     AND rec.exercicio   = d.exercicio ),
				orcamento.conta_despesa as cd
			where
				ped.cod_despesa = d.cod_despesa and
				ped.exercicio   = d.exercicio   and
			--	ped.cod_conta   = d.cod_conta   and ????
				d.cod_conta     = cd.cod_conta  and
				d.exercicio     = cd.exercicio
			) as ped_cd
		ON
			ped_cd.exercicio = pe.exercicio and
			ped_cd.cod_pre_empenho = pe.cod_pre_empenho
		WHERE

			ae.cod_entidade          IN ('' || stCodEntidades || '')
			AND ae.exercicio         =   '' || quote_literal(stExercicio) || '' AND '';

			stSql := stSql || ''

			ae.exercicio	    = pe.exercicio and
			ae.cod_pre_empenho	= pe.cod_pre_empenho and
			
			pe.cgm_beneficiario = cgm.numcgm '';

			if (stDataInicialEmissao is not null and stDataInicialEmissao<>'''') then
			   stSql := stSql || ''AND ae.dt_autorizacao >= to_date('''''' || stDataInicialEmissao || '''''',''''dd/mm/yyyy'''') '';
			end if;

			if (stDataFinalEmissao is not null and stDataFinalEmissao<>'''') then
			   stSql := stSql || ''AND ae.dt_autorizacao <= to_date('''''' || stDataFinalEmissao || '''''',''''dd/mm/yyyy'''') '';
			end if;

			if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial <>'''') then
			   stSql := stSql || ''AND e.cod_empenho >= '' || inCodEmpenhoInicial || '' '';
			end if;

            if (inAutorizacao is not null and inAutorizacao <>'''') then
                stSql := stSQl || ''AND ae.cod_autorizacao = '' || inAutorizacao || '' '';
            end if;

			if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal <>'''') then
			   stSql := stSql || ''AND e.cod_empenho <= '' || inCodEmpenhoFinal || '' '';
			end if;

			if (inCodRecurso is not null and inCodRecurso<>'''') then
				stSql := stSql || '' AND ped_cd.cod_recurso = ''|| inCodRecurso ||'' '';
			end if;

			if (inCodDetalhamento is not null and inCodDetalhamento <>'''') then
				stSql := stSql || '' AND ped_cd.cod_detalhamento = ''|| inCodDetalhamento ||'' '';
			end if;

			if (stDestinacaoRecurso is not null and stDestinacaoRecurso <>'''') then
				stSql := stSql || '' AND ped_cd.masc_recurso_red = ''''''|| stDestinacaoRecurso ||'''''' '';
			end if;

			if (inNumOrgao is not null and inNumOrgao<>'''') then
				stSql := stSql || '' ped_cd.num_orgao = ''|| inNumOrgao ||'' '';
			end if;

			if (inNumUnidade is not null and inNumUnidade<>'''') then
				stSql := stSql || '' AND  ped_d_cd.num_unidade = ''|| inNumUnidade ||'' '';
			end if;

			if (inCGM is not null and inCGM<>'''') then
				stSql := stSql || '' AND pe.cgm_beneficiario  = '' || inCGM || '' '';
			end if;

	stSql := stSql || ''			
		GROUP BY
			ae.cod_autorizacao,
			ae.exercicio,
			ae.dt_autorizacao,
			ae.cod_entidade,
			cgm.nom_cgm,
			autorizado,
			e.empenhado,
			e.anulado,
			e.liquidado,
			e.estornoliquidado,
			e.pago,
			e.estornopago,
            e.cod_empenho
		ORDER BY
			ae.cod_autorizacao,
			ae.exercicio,
			ae.dt_autorizacao,
			ae.cod_entidade,
			cgm.nom_cgm,
			autorizado,
			e.empenhado,
			e.anulado,
			e.liquidado,
			e.estornoliquidado,
			e.pago,
			e.estornopago,
            e.cod_empenho			
		) as tbl
		GROUP BY
			autorizacao,
			exercicio,
			emissao,
			entidade,
			credor,
            empenho
	) as tmp '';
	
	if (inSituacao is not null and inSituacao<>'''') then
		if(inSituacao = ''1'') then
			stSql := stSql || '' WHERE empenho is not null '';
		end if;
		if(inSituacao = ''2'') then
			stSql := stSql || '' WHERE empenho is null '';
		end if;
		if(inSituacao = ''3'') then
			stSql := stSql || '' WHERE autorizado_anulado > 0 '';
		end if;
--		if(inSituacao = ''4'') then
--			stSql := stSql || '' WHERE aliquidar > 0 '';
--		end if;
--		if(inSituacao = ''5'') then
--			stSql := stSql || '' WHERE pago > 0 '';
--		end if;
--		if(inSituacao = ''6'') then
--			stSql := stSql || '' WHERE empenhadoapagar > 0 '';
--		end if;
--		if(inSituacao = ''7'') then
--			stSql := stSql || '' WHERE empenhado > 0 '';
--		end if;
	end if;

	stSql := stSql || ''
	ORDER BY '';

	if (inOrdenacao is not null and inOrdenacao<>'''') then
		if(inOrdenacao = ''1'') then
			stSql := stSql || ''
				autorizacao,
				exercicio,
				emissao,
				entidade,
				credor,
                empenho
			'';
		end if;
		if(inOrdenacao = ''2'') then
			stSql := stSql || ''
				credor,
				autorizacao,
				exercicio,
				emissao,
				entidade,
                empenho
			'';
		end if;
		if(inOrdenacao = ''3'') then
			stSql := stSql || ''
				emissao,
				credor,
				autorizacao,
				exercicio,
				entidade,
                empenho
			'';
		end if;
	else
		stSql := stSql || ''
			autorizacao,
			exercicio,
			emissao,
			entidade,
			credor,
            empenho
		'';
	end if;

 
    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_autorizado_anulado;
    DROP TABLE tmp_autorizado;
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_liquidado_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;

    RETURN;
END;
'language 'plpgsql';

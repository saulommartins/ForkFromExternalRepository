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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.08.08
* Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.8  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_estornemp(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidades      ALIAS FOR $2;
    stDtInicial       ALIAS FOR $3;
    stDtFinal         ALIAS FOR $4;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD           ;
BEGIN

stSql := ''
    CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            e.cod_empenho as cod_empenho,
            e.dt_empenho as dataConsulta,
            coalesce(ipe.vl_total,0.00) as valor
        FROM
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                e.cod_entidade             IN ('' || stCodEntidades || '')
            AND e.exercicio                = '' || stExercicio || ''

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
    )'';
EXECUTE stSql;

stSql := ''
    CREATE TEMPORARY TABLE tmp_anulado AS (
        SELECT
            e.cod_empenho as cod_empenho,
            to_date(to_char(eai.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') as dataConsulta,
            coalesce(eai.vl_anulado,0.00) as valor
        FROM
            empenho.empenho                 as e,
            empenho.pre_empenho             as pe,
            empenho.item_pre_empenho        as ipe,
            empenho.empenho_anulado_item    as eai
        WHERE
                e.cod_entidade             IN ('' || stCodEntidades || '')
            AND e.exercicio                = '' || stExercicio || ''

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho

            AND ipe.exercicio              = eai.exercicio
            AND ipe.cod_pre_empenho        = eai.cod_pre_empenho
            AND ipe.num_item               = eai.num_item
    )'';
EXECUTE stSql;

stSql := ''
    CREATE TEMPORARY TABLE tmp_liquidado AS (
        SELECT
            e.cod_empenho as cod_empenho,
            nl.dt_liquidacao as dataConsulta,
            coalesce(nli.vl_total,0.00) as valor
        FROM
            empenho.empenho               as e,
            empenho.nota_liquidacao_item  as nli,
            empenho.nota_liquidacao       as nl
        WHERE
                e.cod_entidade      IN ('' || stCodEntidades || '')
            AND e.exercicio         = '' || stExercicio || ''

            AND e.exercicio         = nl.exercicio_empenho
            AND e.cod_entidade      = nl.cod_entidade
            AND e.cod_empenho       = nl.cod_empenho

            AND nl.exercicio        = nli.exercicio
            AND nl.cod_nota         = nli.cod_nota
            AND nl.cod_entidade     = nli.cod_entidade
    )'';
EXECUTE stSql;

stSql := ''
    CREATE TEMPORARY TABLE tmp_estornado_liquidado AS (
        SELECT
            e.cod_empenho as cod_empenho,
            to_date(to_char(nlia.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') as dataConsulta,
            coalesce(nlia.vl_anulado,0.00) as valor
        FROM
            empenho.empenho                      as e,
            empenho.nota_liquidacao              as nl,
            empenho.nota_liquidacao_item         as nli,
            empenho.nota_liquidacao_item_anulado as nlia
        WHERE
                e.cod_entidade          IN ('' || stCodEntidades || '')
            AND e.exercicio             = '' || stExercicio || ''

            AND e.exercicio             = nl.exercicio_empenho
            AND e.cod_entidade          = nl.cod_entidade
            AND e.cod_empenho           = nl.cod_empenho

            AND nl.exercicio           = nli.exercicio
            AND nl.cod_nota            = nli.cod_nota
            AND nl.cod_entidade        = nli.cod_entidade

            AND nli.exercicio          = nlia.exercicio
            AND nli.cod_pre_empenho    = nlia.cod_pre_empenho
            AND nli.num_item           = nlia.num_item
            AND nli.cod_entidade       = nlia.cod_entidade
            AND nli.exercicio_item     = nlia.exercicio_item
            AND nli.cod_nota           = nlia.cod_nota
    )'';
EXECUTE stSql;



stSql := ''
    SELECT
        ped_d_cd.num_unidade                        as unidade,
        e.exercicio                                 as exercicio_empenho,
        e.cod_entidade                              as entidade,
        e.cod_empenho                               as empenho,
        to_char(ea.timestamp,''''dd/mm/yyyy'''')    as stData,
        substr(ea.motivo,1,120)                     as motivo,
        sum(eai.vl_anulado)                         as valor,
        ( (coalesce(tcerj.fn_exportacao_empenhado(e.cod_empenho)) - coalesce(tcerj.fn_exportacao_anulado(e.cod_empenho))) - (coalesce(tcerj.fn_exportacao_liquidado(e.cod_empenho)) - coalesce(tcerj.fn_exportacao_estornado_liquidado(e.cod_empenho))) ) as despesa_liquidada,
        ped_d_cd.num_orgao                          as orgao
    FROM
        empenho.empenho                 as e ,
        empenho.empenho_anulado         as ea ,
        empenho.empenho_anulado_item    as eai,
        empenho.pre_empenho as pe
            LEFT OUTER JOIN (
                SELECT
                    d.num_unidade,
                    d.exercicio,
                    d.num_orgao,
                    ped.cod_pre_empenho,
                    ped.cod_despesa
                FROM
                    empenho.pre_empenho_despesa as ped,
                    orcamento.despesa as d,
                    orcamento.conta_despesa as cd
                WHERE
                    ped.cod_despesa     = d.cod_despesa and
                    ped.exercicio       = d.exercicio and
                    ped.cod_conta       = cd.cod_conta and
                    ped.exercicio       = cd.exercicio
            ) as ped_d_cd ON
            pe.exercicio = ped_d_cd.exercicio AND
            pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
     WHERE
            e.exercicio         = '''''' || stExercicio || ''''''
        AND e.cod_entidade      IN ('' || stCodEntidades || '')

        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho

        AND to_date( to_char( ea.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') AND to_date ('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''')

        AND ea.exercicio        = e.exercicio
        AND ea.cod_entidade     = e.cod_entidade
        AND ea.cod_empenho      = e.cod_empenho

        AND ea.exercicio = eai.exercicio
        AND ea.timestamp = eai.timestamp
        AND ea.cod_entidade = eai.cod_entidade
        AND  ea.cod_empenho = eai.cod_empenho

    GROUP BY
        ped_d_cd.num_unidade,
        e.exercicio,
        e.cod_entidade,
        e.cod_empenho,
        to_char(ea.timestamp,''''dd/mm/yyyy''''),
        substr(ea.motivo,1,120),
        ped_d_cd.num_orgao
    ORDER BY
        ped_d_cd.num_unidade,
        e.exercicio,
        e.cod_entidade,
        e.cod_empenho,
        to_char(ea.timestamp,''''dd/mm/yyyy''''),
        substr(ea.motivo,1,120),
        ped_d_cd.num_orgao
'';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_estornado_liquidado;

    RETURN;
END;
'language 'plpgsql';


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

Revision 1.10  2006/08/08 13:46:39  cako
Bug #6732#

Revision 1.9  2006/07/18 13:03:52  cako
Bug #6149#

Revision 1.8  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_liqemp(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio                 ALIAS FOR $1;
    stCodEntidades              ALIAS FOR $2;
    stDtInicial                 ALIAS FOR $3;
    stDtFinal                   ALIAS FOR $4;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
stSql := ''
    SELECT
        ped_d_cd.num_unidade as num_unidade,
        e.cod_entidade      as entidade,
        e.cod_empenho       as empenho,
        e.exercicio         as exercicio,
        to_char(nlia.timestamp,''''dd/mm/yyyy'''') as stData,
        nl.exercicio        as ano,
        /*
        CASE WHEN sum(nlia.vl_anulado) < 0 THEN
            sum(nlia.vl_anulado)*-1
        ELSE
            sum(nlia.vl_anulado)
        END AS valor,
        */
        sum(nlia.vl_anulado)*-1 as valor,
        to_char(nlia.timestamp,''''yyyymm'''') as dtCompetencia,
        ped_d_cd.num_orgao  as num_orgao
    FROM
        empenho.empenho                 as e
        , empenho.nota_liquidacao         as nl
        , empenho.nota_liquidacao_item    as nli
        , empenho.nota_liquidacao_item_anulado    as nlia
        , empenho.pre_empenho             as pe
        LEFT OUTER JOIN (
            SELECT
                ped.exercicio, ped.cod_pre_empenho, d.num_orgao,d.num_unidade, cd.cod_estrutural
            FROM
                empenho.pre_empenho_despesa as ped, orcamento.despesa as d, orcamento.conta_despesa as cd
            WHERE
                ped.cod_despesa = d.cod_despesa and ped.exercicio = d.exercicio and ped.cod_conta = cd.cod_conta and d.exercicio = cd.exercicio
        ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho

    WHERE
            e.exercicio         = '''''' || stExercicio || ''''''
        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho
        AND e.cod_entidade      IN ('' || stCodEntidades || '')

        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND e.exercicio = nl.exercicio_empenho
        AND e.cod_entidade = nl.cod_entidade
        AND e.cod_empenho = nl.cod_empenho

        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
        AND to_date(to_char(nlia.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') 
                             BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') 
                                 AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''')
        AND nl.exercicio = nli.exercicio
        AND nl.cod_nota = nli.cod_nota
        AND nl.cod_entidade = nli.cod_entidade

        --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
        AND nli.exercicio = nlia.exercicio
        AND nli.cod_nota         = nlia.cod_nota
        AND nli.num_item         = nlia.num_item
        AND nli.exercicio_item   = nlia.exercicio_item
        AND nli.cod_pre_empenho  = nlia.cod_pre_empenho
        AND nli.cod_entidade     = nlia.cod_entidade

    GROUP BY
        ped_d_cd.num_unidade,
        e.cod_entidade,
        e.cod_empenho,
        e.exercicio,
        to_char(nlia.timestamp,''''dd/mm/yyyy''''),
        nl.exercicio,
        to_char(nlia.timestamp,''''yyyymm''''),
        ped_d_cd.num_orgao

    UNION

    SELECT
        ped_d_cd.num_unidade as num_unidade,
        e.cod_entidade      as entidade,
        e.cod_empenho       as empenho,
        e.exercicio         as exercicio,
        to_char(nl.dt_liquidacao,''''dd/mm/yyyy'''') as stData,
        nl.exercicio        as ano,
        sum(nli.vl_total)   as valor,
        to_char(nl.dt_liquidacao,''''yyyymm'''') as dtCompetencia,
        ped_d_cd.num_orgao  as num_orgao
    FROM
        empenho.empenho                 as e
        , empenho.nota_liquidacao         as nl
        , empenho.nota_liquidacao_item    as nli
        , empenho.pre_empenho             as pe
        LEFT OUTER JOIN (
            SELECT
                ped.exercicio, ped.cod_pre_empenho, d.num_orgao,d.num_unidade, cd.cod_estrutural
            FROM
                empenho.pre_empenho_despesa as ped, orcamento.despesa as d, orcamento.conta_despesa as cd
            WHERE
                ped.cod_despesa = d.cod_despesa and ped.exercicio = d.exercicio and ped.cod_conta = cd.cod_conta and d.exercicio = cd.exercicio
        ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
    WHERE
            e.exercicio         = '' || stExercicio || ''
        AND e.exercicio         = pe.exercicio
        AND e.cod_pre_empenho   = pe.cod_pre_empenho
        AND e.cod_entidade      IN ('' || stCodEntidades || '')

        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
        AND e.exercicio = nl.exercicio_empenho
        AND e.cod_entidade = nl.cod_entidade
        AND e.cod_empenho = nl.cod_empenho

        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
        AND nl.dt_liquidacao BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''')
        AND nl.exercicio = nli.exercicio
        AND nl.cod_nota = nli.cod_nota
        AND nl.cod_entidade = nli.cod_entidade

    GROUP BY
        ped_d_cd.num_unidade,
        e.cod_entidade,
        e.cod_empenho,
        e.exercicio,
        to_char(nl.dt_liquidacao,''''dd/mm/yyyy''''),
        nl.exercicio,
        to_char(nl.dt_liquidacao,''''yyyymm''''),
        ped_d_cd.num_orgao
    ORDER BY
        num_unidade,
        entidade,
        empenho,
        exercicio,
        stData,
        ano,
        valor desc,
        dtCompetencia,
        num_orgao
        '';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    RETURN;
END;
'language 'plpgsql';

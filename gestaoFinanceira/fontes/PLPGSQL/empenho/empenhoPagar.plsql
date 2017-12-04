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
* Casos de uso: uc-02.03.07
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_empenho_pagar(varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
    stSql := ''
        SELECT data, empenho, exercicio, cgm, razao_social, valor_empenhado, valor_liquidado, valor_anulado, valor_pago FROM (
            SELECT
                to_char(e.dt_empenho,''''dd/mm/yyyy'''') as data,
                (e.cod_entidade || ''''''||''-''||''''''|| lpad(e.cod_empenho,6,0) ||''''''||''/''||''''''|| e.exercicio) as empenho,
                e.exercicio as exercicio,
                pe.cgm_beneficiario as cgm,
                cgm.nom_cgm as razao_social,
                empenho.fn_empenho_empenhado(e.exercicio,e.cod_empenho,e.cod_entidade,'''''' || stDtInicial || '''''','''''' || stDtFinal || '''''') as valor_empenhado,
                (empenho.fn_empenho_liquidado(e.exercicio,e.cod_empenho,e.cod_entidade,'''''' || stDtInicial || '''''','''''' || stDtFinal || '''''') - empenho.fn_empenho_estorno_liquidacao(e.exercicio,e.cod_empenho,e.cod_entidade,'''''' || stDtInicial || '''''','''''' || stDtFinal || '''''')) as valor_liquidado,
                empenho.fn_empenho_anulado(e.exercicio,e.cod_empenho,e.cod_entidade,'''''' || stDtInicial || '''''','''''' || stDtFinal || '''''') as valor_anulado,
                (empenho.fn_empenho_pago(e.exercicio,e.cod_empenho,e.cod_entidade,'''''' || stDtInicial || '''''','''''' || stDtFinal || '''''') - empenho.fn_empenho_estornado(e.exercicio,e.cod_empenho,e.cod_entidade,'''''' || stDtInicial || '''''','''''' || stDtFinal || '''''')) as valor_pago
            FROM
                  empenho.empenho     as e
                , sw_cgm              as cgm
                , empenho.pre_empenho as pe
                    LEFT OUTER JOIN empenho.restos_pre_empenho as rpe ON pe.exercicio = rpe.exercicio AND pe.cod_pre_empenho = rpe.cod_pre_empenho
                    LEFT OUTER JOIN (
                        SELECT
                            ped.exercicio, ped.cod_pre_empenho, d.num_pao, d.num_orgao,d.num_unidade, d.cod_recurso, cd.cod_estrutural
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
                AND pe.cgm_beneficiario = cgm.numcgm

                ORDER BY e.dt_empenho, e.cod_empenho) as tbl where valor_empenhado  <> ''''0.00'''' '';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    RETURN;

END;
'language 'plpgsql';

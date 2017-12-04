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
* Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_saldo_anterior(VARCHAR,INTEGER,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio             ALIAS FOR $1;
    inCodDespesa            ALIAS FOR $2;
    inCodEmpenho            ALIAS FOR $3;
    nuTotal                 NUMERIC := 0.00;
    nuValorOriginal         NUMERIC := 0.00;
    nuTotalItens            NUMERIC := 0.00;
    nuValorReserva          NUMERIC := 0.00;
    nuValorReservaManual    NUMERIC := 0.00;
    nuValorAnulado          NUMERIC := 0.00;
    nuValorSuplementado     NUMERIC := 0.00;
    nuValorReduzido         NUMERIC := 0.00;

BEGIN
    SELECT      coalesce(sum(vl_total),0.00)
        INTO    nuTotalItens
    FROM     empenho.pre_empenho         as pe
            ,empenho.pre_empenho_despesa as pd
            ,orcamento.despesa           as de
            ,empenho.item_pre_empenho    as it
            ,empenho.empenho             as em
    WHERE   pe.cod_pre_empenho  = pd.cod_pre_empenho
    AND     pe.exercicio        = pd.exercicio
    AND     pd.cod_despesa      = de.cod_despesa
    AND     pd.exercicio        = de.exercicio
    AND     pe.cod_pre_empenho  = it.cod_pre_empenho
    AND     pe.exercicio        = it.exercicio
    AND     pe.cod_pre_empenho  = em.cod_pre_empenho
    AND     pe.exercicio        = em.exercicio
    AND     pe.exercicio        = stExercicio
    AND     de.cod_despesa      = inCodDespesa
    AND     em.cod_empenho      >= inCodEmpenho
    ;
    IF nuTotalItens IS NULL THEN
        nuTotalItens := 0.00;
    END IF;

   SELECT    coalesce(sum(ei.vl_anulado),0.00)
       INTO    nuValorAnulado
    FROM     empenho.pre_empenho            as pe
            ,empenho.pre_empenho_despesa    as pd
            ,orcamento.despesa              as de
            ,empenho.item_pre_empenho       as it
            ,empenho.empenho                as em
            ,empenho.empenho_anulado        as ea
            ,empenho.empenho_anulado_item   as ei
    WHERE   pe.cod_pre_empenho  = pd.cod_pre_empenho
    AND     pe.exercicio        = pd.exercicio
    AND     pd.cod_despesa      = de.cod_despesa
    AND     pd.exercicio        = de.exercicio
    AND     pe.cod_pre_empenho  = it.cod_pre_empenho
    AND     pe.exercicio        = it.exercicio
    AND     pe.cod_pre_empenho  = em.cod_pre_empenho
    AND     pe.exercicio        = em.exercicio
    AND     em.cod_empenho      = ea.cod_empenho
    AND     em.exercicio        = ea.exercicio
    AND     em.cod_entidade     = ea.cod_entidade
    AND     ea.cod_empenho      = ei.cod_empenho
    AND     ea.exercicio        = ei.exercicio
    AND     ea.cod_entidade     = ei.cod_entidade
    AND     ea."timestamp"      = ei."timestamp"
    AND     it.cod_pre_empenho  = ei.cod_pre_empenho
    AND     it.num_item         = ei.num_item
    AND     it.exercicio        = ei.exercicio
    AND     pe.exercicio        = stExercicio
    AND     de.cod_despesa      = inCodDespesa
    AND     em.cod_empenho      >= inCodEmpenho
    ;

    IF nuValorAnulado IS NULL THEN
        nuValorAnulado := 0.00;
    END IF;

    RETURN (nuTotalItens - nuValorAnulado) + empenho.fn_saldo_dotacao(stExercicio, inCodDespesa);

END;
'LANGUAGE 'plpgsql';

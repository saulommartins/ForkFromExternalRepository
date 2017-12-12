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
* Script de função PLPGSQL - Desenvolvida apartir da funcao empenho.fn_saldo_dotacao, porém considerando uma data limite para 
* considerar os valores. Utilizada para trazer os valores que estavam valendo na data da anulação do empenho
* para usar na nota de reemissão de anulação de empenho.
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.2  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_saldo_dotacao_data(VARCHAR, INTEGER, VARCHAR) RETURNS NUMERIC AS '

DECLARE
    stExercicio             ALIAS FOR $1;
    inCodDespesa            ALIAS FOR $2;
    dtAnulacaoEmp           ALIAS FOR $3;
    nuTotal                 NUMERIC := 0.00;
    nuValorOriginal         NUMERIC := 0.00;
    nuTotalItens            NUMERIC := 0.00;
    nuValorReserva          NUMERIC := 0.00;
    nuValorReservaManual    NUMERIC := 0.00;
    nuValorAnulado          NUMERIC := 0.00;
    nuValorSuplementado     NUMERIC := 0.00;
    nuValorReduzido         NUMERIC := 0.00;

BEGIN
    --VALOR ORIGINAL
    SELECT
        coalesce(vl_original,0.00)
    INTO
        nuValorOriginal
    FROM
        orcamento.despesa
    WHERE
        cod_despesa = inCodDespesa  AND
        exercicio   = stExercicio;


    SELECT
        coalesce(sum(vl_total),0.00)
    INTO
        nuTotalItens
    FROM
        empenho.pre_empenho_despesa as pd,
        empenho.pre_empenho         as pe,
        empenho.item_pre_empenho    as it,
        empenho.empenho             as em
    WHERE
        pd.cod_pre_empenho  = pe.cod_pre_empenho    AND
        pd.exercicio        = pe.exercicio          AND

        pe.cod_pre_empenho  = it.cod_pre_empenho    AND
        pe.exercicio        = it.exercicio          AND

        pe.cod_pre_empenho  = em.cod_pre_empenho    AND
        pe.exercicio        = em.exercicio          AND

        pd.exercicio        = stExercicio           AND
        pd.cod_despesa      = inCodDespesa          AND
        em.dt_empenho <=  TO_DATE(dtAnulacaoEmp, ''yyyy-mm-dd'' )
        
    ;


    SELECT
        coalesce(sum(vl_reserva),0.00)
    INTO
        nuValorReserva
    FROM
        orcamento.despesa              as de,
        empenho.pre_empenho_despesa    as pd,
        empenho.pre_empenho            as pe,
        empenho.autorizacao_empenho    as ae,
        empenho.autorizacao_reserva    as ar,
        orcamento.reserva_saldos       as re
            LEFT JOIN orcamento.reserva_saldos_anulada as rsa ON
                re.cod_reserva  = rsa.cod_reserva AND
                re.exercicio    = rsa.exercicio
    WHERE
        de.cod_despesa      = pd.cod_despesa        AND
        de.exercicio        = pd.exercicio          AND

        pd.cod_pre_empenho  = pe.cod_pre_empenho    AND
        pd.exercicio        = pe.exercicio          AND

        pe.cod_pre_empenho  = ae.cod_pre_empenho    AND
        pe.exercicio        = ae.exercicio          AND

        ae.cod_autorizacao  = ar.cod_autorizacao    AND
        ae.exercicio        = ar.exercicio          AND
        ae.cod_entidade     = ar.cod_entidade       AND

        ar.exercicio        = re.exercicio          AND
        ar.cod_reserva      = re.cod_reserva        AND

        de.exercicio        = stExercicio           AND
        de.cod_despesa      = inCodDespesa          AND

        re.cod_despesa      = inCodDespesa          AND
        rsa.cod_reserva     is null                 

-- NOTA: A funcao empenho.fn_saldo_dotacao, não considera as datas de validades
--       inicial e final das reservas de saldo o que, indevidamente, acaba trazendo valores de reservas inativas.

--      TO_DATE(re.dt_validade_inicial, ''yyyy/mm/dd'') <= TO_DATE(dtAnulacaoEmp, ''dd/mm/yyyy'' ) AND
--      TO_DATE(re.dt_validade_final,   ''yyyy/mm/dd'') >= TO_DATE(dtAnulacaoEmp, ''dd/mm/yyyy'' )

    ;

    SELECT
        coalesce(sum(rs.vl_reserva),0.00)
    INTO
        nuValorReservaManual
    FROM
        orcamento.reserva_saldos            as rs
            LEFT JOIN orcamento.reserva_saldos_anulada as rsa ON
                rs.cod_reserva  = rsa.cod_reserva AND
                rs.exercicio    = rsa.exercicio
    WHERE
        rs.exercicio        = stExercicio           AND
        rs.cod_despesa      = inCodDespesa          AND

        rs.tipo             = ''M''                 AND
        rsa.cod_reserva     is null                 

-- NOTA: A funcao empenho.fn_saldo_dotacao, não considera as datas de validades
--       inicial e final das reservas de saldo o que, indevidamente, acaba trazendo valores de reservas inativas.

--      TO_DATE(rs.dt_validade_inicial,''yyyy/mm/dd'') <= TO_DATE(dtAnulacaoEmp, ''dd/mm/yyyy'') AND
--      TO_DATE(rs.dt_validade_final, ''yyyy/mm/dd'') >= TO_DATE(dtAnulacaoEmp, ''dd/mm/yyyy'' )

    ;


   SELECT
        coalesce(sum(ei.vl_anulado),0.00)
   INTO
        nuValorAnulado
   FROM
        orcamento.despesa              as de,
        empenho.pre_empenho_despesa    as pd,
        empenho.pre_empenho            as pe,
        empenho.item_pre_empenho       as it,
        empenho.empenho_anulado_item   as ei,
        empenho.empenho_anulado        as ea
    WHERE
        de.cod_despesa      = pd.cod_despesa        AND
        de.exercicio        = pd.exercicio          AND

        pd.cod_pre_empenho  = pe.cod_pre_empenho    AND
        pd.exercicio        = pe.exercicio          AND

        pe.cod_pre_empenho  = it.cod_pre_empenho    AND
        pe.exercicio        = it.exercicio          AND

        it.cod_pre_empenho  = ei.cod_pre_empenho    AND
        it.num_item         = ei.num_item           AND
        it.exercicio        = ei.exercicio          AND

        ei.cod_empenho      = ea.cod_empenho        AND
        ei.exercicio        = ea.exercicio          AND
        ei.cod_entidade     = ea.cod_entidade       AND
        ei.timestamp        = ea.timestamp          AND

        de.exercicio        = stExercicio           AND
        de.cod_despesa      = inCodDespesa          AND

--      TO_DATE( TO_CHAR( ea.timestamp, ''yyyy-mm-dd hh:mm:ss''), ''yyyy-mm-dd hh:mm:ss'' ) <= TO_DATE(dtAnulacaoEmp,''yyyy-mm-dd hh:mm:ss'')
--      ea.timestamp < TO_DATE(dtAnulacaoEmp,''yyyy-mm-dd hh:mm:ss'')
        ea.timestamp::varchar < dtAnulacaoEmp

    ;

    SELECT
        coalesce( sum(oss.valor), 0.00 )
    INTO
        nuValorSuplementado
    FROM
        orcamento.suplementacao_suplementada as oss,
        orcamento.suplementacao as os
    WHERE
        os.exercicio         = oss.exercicio         AND
        os.cod_suplementacao = oss.cod_suplementacao AND

        oss.cod_despesa      = inCodDespesa          AND
        oss.exercicio        = stExercicio           AND

--      TO_DATE(os.dt_suplementacao, ''yyyy-mm-dd'') <= TO_DATE(dtAnulacaoEmp, ''yyyy-mm-dd'' )
        os.dt_suplementacao <= TO_DATE(dtAnulacaoEmp, ''yyyy-mm-dd'' )
    ;


    SELECT
        coalesce( sum(osr.valor), 0.00 )
    INTO
        nuValorReduzido
    FROM
        orcamento.suplementacao_reducao as osr,
        orcamento.suplementacao as os
    WHERE
        os.exercicio         = osr.exercicio         AND
        os.cod_suplementacao = osr.cod_suplementacao AND

        osr.cod_despesa = inCodDespesa  AND
        osr.exercicio   = stExercicio   AND

--      TO_DATE(os.dt_suplementacao, ''yyyy-mm-dd'') <= TO_DATE(dtAnulacaoEmp, ''yyyy-mm-dd'' )
        os.dt_suplementacao <= TO_DATE(dtAnulacaoEmp, ''yyyy-mm-dd'' )
    ;


    RETURN nuValorOriginal - nuTotalItens - nuValorReserva -nuValorReservaManual + nuValorAnulado + nuValorSuplementado - nuValorReduzido;

END;
'LANGUAGE 'plpgsql';

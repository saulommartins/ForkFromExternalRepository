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
* Busca saldo do empenho até a data passada;
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: saldoEmpenho.plsql 65190 2016-04-29 19:36:51Z michel $
*
* Casos de uso: uc-06.00.00
*/


CREATE OR REPLACE FUNCTION tc.fn_saldo_empenho (   inCodEmpenho  integer
                                                    , inCodEntidade integer
                                                    , inExercicio   integer
                                                    , stDataFinal   varchar
                                                    ) RETURNS NUMERIC AS '

DECLARE
    stSql                   varchar;
    reRegistro              record;
    nuSaldo                 numeric := 0.00;
    nuEmpenhado             numeric := 0.00;
    nuAnulado               numeric := 0.00;
    nuPago                  numeric := 0.00;
    nuEstorno               numeric := 0.00;

BEGIN
        SELECT
            coalesce(sum(ipe.vl_total),0.00)   as valor
        INTO  nuEmpenhado
        FROM
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
            e.cod_entidade      =   inCodEntidade AND
            e.exercicio         =   inExercicio::VARCHAR AND 
            e.cod_empenho       =   inCodEmpenho  AND 
            e.dt_empenho       <=   to_date( stDataFinal ,''dd/mm/yyyy'') AND 
            --Ligação EMPENHO : PRE_EMPENHO
            e.exercicio         = pe.exercicio AND
            e.cod_pre_empenho   = pe.cod_pre_empenho AND

            --Ligação PRE_EMPENHO : ITEM_PRE_EMPENHO
            pe.exercicio        = ipe.exercicio AND
            pe.cod_pre_empenho  = ipe.cod_pre_empenho ;


    -- EMPENHO ANULADO
        SELECT
            coalesce(sum(eai.vl_anulado),0.00) as valor
        INTO nuAnulado 
        FROM
            empenho.empenho                 as e,
            empenho.empenho_anulado         as ea,
            empenho.empenho_anulado_item    as eai
        WHERE
            e.cod_entidade      = inCodEmpenho AND
            e.exercicio         = inExercicio::VARCHAR AND
            e.cod_empenho       = inCodEmpenho AND
            to_date( to_char( ea.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) <= to_date( stDataFinal ,''dd/mm/yyyy'') AND 

            e.exercicio        = ea.exercicio AND
            e.cod_entidade     = ea.cod_entidade AND
            e.cod_empenho      = ea.cod_empenho AND

            --Ligação EMPENHO ANULADO : EMPENHO ANULADO ITEM
            ea.exercicio        = eai.exercicio AND
            ea.timestamp        = eai.timestamp AND
            ea.cod_entidade     = eai.cod_entidade AND
            ea.cod_empenho      = eai.cod_empenho ;
    
        SELECT
            coalesce(sum(nlp.vl_pago),0.00)    as valor
        INTO nuPago 
        FROM
            empenho.empenho                 as e,
            empenho.nota_liquidacao         as nl,
            empenho.nota_liquidacao_paga    as nlp
        WHERE
            e.cod_entidade      = inCodEntidade AND
            e.exercicio         = inExercicio::VARCHAR AND
            e.cod_empenho       = inCodEmpenho  AND
            to_date( to_char( nlp.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) <= to_date(stDataFinal ,''dd/mm/yyyy'') AND

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            e.exercicio         = nl.exercicio_empenho AND
            e.cod_entidade      = nl.cod_entidade AND
            e.cod_empenho       = nl.cod_empenho AND

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            nl.exercicio        = nlp.exercicio AND
            nl.cod_nota         = nlp.cod_nota AND
            nl.cod_entidade     = nlp.cod_entidade;

        SELECT
            coalesce(sum(nlpa.vl_anulado),0.00)    as valor
        INTO nuEstorno
        FROM
            empenho.empenho                         as e,
            empenho.nota_liquidacao                 as nl,
            empenho.nota_liquidacao_paga            as nlp,
            empenho.nota_liquidacao_paga_anulada    as nlpa
        WHERE
            e.cod_entidade      = inCodEntidade AND
            e.exercicio         = inExercicio::VARCHAR AND
            e.cod_empenho       = inCodEmpenho  AND
            to_date( to_char( nlpa.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) <= to_date(stDataFinal ,''dd/mm/yyyy'') AND

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
            nlp.timestamp           = nlpa.timestamp ;

    nuSaldo :=0.00;
    nuSaldo := ( nuEmpenhado - nuAnulado ) - ( nuPago - nuEstorno );

    RETURN coalesce(nuSaldo,0.00);
END;
'LANGUAGE 'plpgsql';

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
* $Revision: 22934 $
* $Name$
* $Author: domluc $
* $Date: 2007-05-29 11:14:00 -0300 (Ter, 29 Mai 2007) $
*
* Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.1  2007/05/29 14:14:00  domluc
Saldo do Empenho

Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_saldo_empenho ( inCodEmpenho  integer
                                                    , inCodEntidade integer
                                                    , inExercicio   integer
                                                    , stDataFinal   varchar
                                                    ) RETURNS NUMERIC AS '

DECLARE
    stSql                   varchar;
    reRegistro              RECORD;
    nuSaldo                 NUMERIC := 0.00;

BEGIN

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
            e.cod_entidade      =   '' || inCodEntidade || '' AND
            e.exercicio         =   '' || inExercicio   || '' AND 
            e.cod_empenho       =   '' || inCodEmpenho  || '' AND 
            e.dt_empenho       <=   to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND 
            '';
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



    -- EMPENHO ANULADO
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
            e.cod_entidade      =   '' || inCodEmpenho  || '' AND
            e.exercicio         =   '' || inExercicio   || '' AND
            e.cod_empenho       =   '' || inCodEmpenho  || '' AND
            to_date( to_char( ea.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND '' ;

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
            e.cod_entidade      =   '' || inCodEntidade || '' AND
            e.exercicio         =   '' || inExercicio   || '' AND
            e.cod_empenho       =   '' || inCodEmpenho  || '' AND
            to_date( to_char( nlp.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND '';

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
            e.cod_entidade      =   '' || inCodEntidade || '' AND
            e.exercicio         =   '' || inExercicio   || '' AND
            e.cod_empenho       =   '' || inCodEmpenho  || '' AND
            to_date( to_char( nlpa.timestamp, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') AND '';

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
        SELECT * FROM (
        SELECT
            (sum(empenhado) - sum(anulado)) - (sum(pago) - sum(estornopago)) as saldo
        FROM (
            SELECT
                coalesce(empenho.fn_somatorio_razao_credor_empenhado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as empenhado,
                coalesce(empenho.fn_somatorio_razao_credor_anulado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as anulado,
                coalesce(empenho.fn_somatorio_razao_credor_pago(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as pago,
                coalesce(empenho.fn_somatorio_razao_credor_estornado(e.cod_empenho, e.cod_entidade, e.exercicio),0.00) as estornopago
            FROM
                empenho.empenho     as e
            WHERE
                    e.cod_entidade          =  '' || inCodEntidade || ''
                    AND e.cod_empenho       =  '' || inCodEmpenho || ''
                    AND e.exercicio         =  '' || inExercicio || '' ) as tabela ) as outra_tabela''; 

    nuSaldo :=0.00;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        nuSaldo := nuSaldo + reRegistro.saldo;
    END LOOP;

    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;

    RETURN coalesce(nuSaldo,0.00);
END;
'LANGUAGE 'plpgsql';

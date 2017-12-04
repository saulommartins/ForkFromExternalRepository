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
* Casos de uso: uc-02.03.04
*               uc-02.03.05
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_pagar(VARCHAR,INTEGER,INTEGER,TIMESTAMP,INTEGER, VARCHAR) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    stTimestamp                ALIAS FOR $4;
    inCodOrdem                 ALIAS FOR $5;
    stExercicioOrdem           ALIAS FOR $6;
    nuValorPagar               NUMERIC;
    stSql                      VARCHAR   := '''';
    crCursor                   REFCURSOR;
BEGIN

stSql := ''
SELECT
  coalesce(emp.valorEmpenhado,0.00) - coalesce(anu.valorAnulado,0.00) - (  coalesce(pag.valorPago,0.00)- coalesce(est.valorEstornado,0.00))
FROM
    empenho.empenho as e
        LEFT OUTER JOIN (
            SELECT
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade,
                coalesce(sum(nlpa.vl_anulado),0.00) as valorEstornado
            FROM
                empenho.empenho                         as e,
                empenho.nota_liquidacao                 as nl,
                empenho.nota_liquidacao_paga            as nlp,
                empenho.nota_liquidacao_paga_anulada    as nlpa,
                empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp,
                empenho.pagamento_liquidacao            as pl,
                empenho.ordem_pagamento                 as op
            WHERE
                    e.exercicio         = '''' || stExercicio || ''''
                AND e.cod_empenho       = '' || inCodEmpenho || ''
                AND e.cod_entidade      = '' || inCodEntidade || ''
                AND nlp.timestamp       <= '''''' || stTimestamp || '''''' '';
--                AND to_date( to_char(op.dt_emissao, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date('''''' || dtEmissao || '''''',''''dd/mm/yyyy'''')

                if (inCodOrdem > 0) then
                    stSql := stSql || ''
                        AND op.cod_ordem <= '' || inCodOrdem || ''
                    '';
                end if;

                if (stExercicioOrdem is not null and stExercicioOrdem<>'''') then
                    stSql := stSql || ''
                        AND op.exercicio = '''' || stExercicioOrdem || ''''
                    '';
                end if;

                stSql := stSql || ''
                AND e.cod_empenho       = nl.cod_empenho
                AND e.exercicio         = nl.exercicio_empenho
                AND e.cod_entidade      = nl.cod_entidade

                AND nl.exercicio        = nlp.exercicio
                AND nl.cod_nota         = nlp.cod_nota
                AND nl.cod_entidade     = nlp.cod_entidade

                AND nlp.cod_entidade    = plnlp.cod_entidade
                AND nlp.cod_nota        = plnlp.cod_nota
                AND nlp.exercicio       = plnlp.exercicio_liquidacao
                AND nlp.timestamp       = plnlp.timestamp

                AND pl.cod_ordem        = plnlp.cod_ordem
                AND pl.exercicio        = plnlp.exercicio
                AND pl.cod_entidade     = plnlp.cod_entidade
                AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                AND pl.cod_nota         = plnlp.cod_nota

                AND pl.cod_ordem        = op.cod_ordem
                AND pl.exercicio        = op.exercicio
                AND pl.cod_entidade     = op.cod_entidade

                AND nlp.exercicio       = nlpa.exercicio
                AND nlp.cod_nota        = nlpa.cod_nota
                AND nlp.cod_entidade    = nlpa.cod_entidade
                AND nlp.timestamp       = nlpa.timestamp
            GROUP BY
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade
        ) as est ON
            e.exercicio     = est.exercicio     AND
            e.cod_empenho   = est.cod_empenho   AND
            e.cod_entidade  = est.cod_entidade
        LEFT OUTER JOIN (
            SELECT
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade,
                coalesce(sum(nlp.vl_pago),0.00) as valorPago
            FROM
                empenho.empenho                 as e,
                empenho.nota_liquidacao         as nl,
                empenho.nota_liquidacao_paga    as nlp,
                empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp,
                empenho.pagamento_liquidacao            as pl,
                empenho.ordem_pagamento                 as op
            WHERE
                    e.exercicio         = '''' || stExercicio || ''''
                AND e.cod_empenho       = '' || inCodEmpenho || ''
                AND e.cod_entidade      = '' || inCodEntidade || ''
                AND nlp.timestamp      <= '''''' || stTimestamp || '''''' '';
                --AND to_date( to_charop.dt_emissao, ''''dd/mm/yyyy''''), ''''dd/mm/yyyy'''' ) <= to_date('''''' || dtEmissao || '''''',''''dd/mm/yyyy'''')

                if (inCodOrdem > 0) then
                    stSql := stSql || ''
                        AND op.cod_ordem <= '' || inCodOrdem || ''
                    '';
                end if;

                if (stExercicioOrdem is not null and stExercicioOrdem<>'''') then
                    stSql := stSql || ''
                        AND op.exercicio = '''' || stExercicioOrdem || ''''
                    '';
                end if;

                stSql := stSql || ''
                AND e.exercicio         = nl.exercicio_empenho
                AND e.cod_entidade      = nl.cod_entidade
                AND e.cod_empenho       = nl.cod_empenho

                AND nl.exercicio        = nlp.exercicio
                AND nl.cod_nota         = nlp.cod_nota
                AND nl.cod_entidade     = nlp.cod_entidade

                AND nlp.cod_entidade    = plnlp.cod_entidade
                AND nlp.cod_nota        = plnlp.cod_nota
                AND nlp.exercicio       = plnlp.exercicio_liquidacao
                AND nlp.timestamp       = plnlp.timestamp

                AND pl.cod_ordem        = plnlp.cod_ordem
                AND pl.exercicio        = plnlp.exercicio
                AND pl.cod_entidade     = plnlp.cod_entidade
                AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                AND pl.cod_nota         = plnlp.cod_nota

                AND pl.cod_ordem        = op.cod_ordem
                AND pl.exercicio        = op.exercicio
                AND pl.cod_entidade     = op.cod_entidade
            GROUP BY
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade
        ) as pag ON (
            e.exercicio     = pag.exercicio     AND
            e.cod_empenho   = pag.cod_empenho   AND
            e.cod_entidade  = pag.cod_entidade
        )
        LEFT OUTER JOIN (
            SELECT
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade,
                coalesce(sum(ipe.vl_total),0.00) as valorEmpenhado
            FROM
                empenho.empenho           as e,
                empenho.pre_empenho       as pe,
                empenho.item_pre_empenho  as ipe
            WHERE
                    e.exercicio         = '''' || stExercicio || ''''
                AND e.cod_empenho       = '' || inCodEmpenho || ''
                AND e.cod_entidade      = '' || inCodEntidade || ''

                AND e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho

                AND pe.exercicio        = ipe.exercicio
                AND pe.cod_pre_empenho  = ipe.cod_pre_empenho
            GROUP BY
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade
        ) as emp ON (
            e.exercicio     = emp.exercicio     AND
            e.cod_empenho   = emp.cod_empenho   AND
            e.cod_entidade  = emp.cod_entidade
        )
        LEFT OUTER JOIN (
           SELECT
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade,
                coalesce(sum(eai.vl_anulado),0.00) as valorAnulado
            FROM
                empenho.empenho                 as e,
                empenho.empenho_anulado         as ea,
                empenho.empenho_anulado_item    as eai
            WHERE
                    e.exercicio         = '''' || stExercicio || ''''
                AND e.cod_empenho       = '' || inCodEmpenho || ''
                AND e.cod_entidade      = '' || inCodEntidade || ''

                AND ea.exercicio    = e.exercicio
                AND ea.cod_entidade = e.cod_entidade
                AND ea.cod_empenho  = e.cod_empenho

                AND ea.exercicio    = eai.exercicio
                AND ea.timestamp    = eai.timestamp
                AND ea.cod_entidade = eai.cod_entidade
                AND ea.cod_empenho  = eai.cod_empenho
            GROUP BY
                e.exercicio,
                e.cod_empenho,
                e.cod_entidade
        ) as anu ON (
            e.exercicio     = anu.exercicio     AND
            e.cod_empenho   = anu.cod_empenho   AND
            e.cod_entidade  = anu.cod_entidade
        )
WHERE
        e.exercicio         = '''' || stExercicio || ''''
    AND e.cod_empenho       = '' || inCodEmpenho || ''
    AND e.cod_entidade      = '' || inCodEntidade;

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuValorPagar;
    CLOSE crCursor;

    RETURN nuValorPagar;

END;
'LANGUAGE 'plpgsql';


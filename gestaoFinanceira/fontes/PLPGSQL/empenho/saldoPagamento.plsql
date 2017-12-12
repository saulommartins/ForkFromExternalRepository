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
* Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_saldo_pagamento(VARCHAR,INTEGER,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio             ALIAS FOR $1;
    inCodEntidade           ALIAS FOR $2;
    inCodOrdem              ALIAS FOR $3;
    nuSaldo                 NUMERIC := 0.00;

BEGIN
    SELECT
        sum(coalesce(nlp.vl_pago,0.00)) - sum(coalesce(nla.vl_anulado,0.00))
            INTO nuSaldo
    FROM
        empenho.pagamento_liquidacao_nota_liquidacao_paga as pln,
        empenho.nota_liquidacao_paga as nlp
        LEFT JOIN empenho.nota_liquidacao_paga_anulada as nla on(
            nlp.cod_entidade= nla.cod_entidade
        and nlp.cod_nota    = nla.cod_nota
        and nlp.exercicio   = nla.exercicio
        and nlp."timestamp" = nla."timestamp"
        )
    WHERE
        pln.exercicio_liquidacao = nlp.exercicio
    AND pln.cod_nota             = nlp.cod_nota
    AND pln.cod_entidade         = nlp.cod_entidade
    AND pln."timestamp"          = nlp."timestamp"

    AND pln.cod_entidade         = inCodEntidade
    AND pln.exercicio            = stExercicio
    AND pln.cod_ordem            = inCodOrdem;

    IF nuSaldo IS NULL THEN
        nuSaldo := 0.00;
    END IF;

    RETURN nuSaldo;
END;
'LANGUAGE 'plpgsql';

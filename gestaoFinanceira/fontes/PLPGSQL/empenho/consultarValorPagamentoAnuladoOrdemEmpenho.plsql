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
* $Revision: 25202 $
* $Name$
* $Author: cako $
* $Date: 2007-09-03 11:36:02 -0300 (Seg, 03 Set 2007) $
*
* Casos de uso: uc-02.03.22
*/

/*
$Log$
Revision 1.1  2007/09/03 14:36:02  cako
Ticket#10049#


*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_pagamento_anulado_ordem_empenho(VARCHAR,INTEGER,INTEGER,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodOrdem                 ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inCodEmpenho               ALIAS FOR $4;
    nuValor                    NUMERIC := 0.00;
BEGIN

    SELECT
        coalesce(sum(vl_anulado),0.00)
        INTO nuValor
    FROM empenho.ordem_pagamento_liquidacao_anulada as opla
         JOIN empenho.pagamento_liquidacao as pl
         ON (   opla.cod_ordem            = pl.cod_ordem
            AND opla.exercicio            = pl.exercicio
            AND opla.cod_nota             = pl.cod_nota
            AND opla.exercicio_liquidacao = pl.exercicio_liquidacao
            AND opla.cod_entidade         = pl.cod_entidade
         )
         JOIN empenho.nota_liquidacao as nl
         ON (   pl.cod_nota             = nl.cod_nota
            AND pl.cod_entidade         = nl.cod_entidade
            AND pl.exercicio_liquidacao = nl.exercicio
         )
             
    WHERE   opla.cod_entidade  = inCodEntidade
    AND     opla.cod_ordem     = inCodOrdem
    AND     opla.exercicio     = stExercicio
    AND     nl.cod_empenho     = inCodEmpenho
    ;

    IF nuValor IS NULL THEN
        nuValor := 0.00;
    END IF;

    RETURN nuValor;

END;
'LANGUAGE 'plpgsql';

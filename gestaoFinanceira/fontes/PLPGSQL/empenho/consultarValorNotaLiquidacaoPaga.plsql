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
Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_nota_liquidacao_paga(VARCHAR,INTEGER,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEntidade              ALIAS FOR $2;
    inCodOrdem                 ALIAS FOR $3;
    nuValorPago                NUMERIC := 0.00;
BEGIN

    SELECT
        coalesce(sum(nlp.vl_pago),0.00) as vl_pago
        INTO    nuValorPago
    FROM
        empenho.pagamento_liquidacao_nota_liquidacao_paga   as plp,
        empenho.nota_liquidacao_paga                        as nlp
    WHERE
        plp.cod_nota                = nlp.cod_nota
    AND plp.exercicio_liquidacao    = nlp.exercicio
    AND plp.cod_entidade            = nlp.cod_entidade
    AND plp.timestamp               = nlp.timestamp

    AND plp.cod_entidade            = inCodEntidade
    AND plp.exercicio               = stExercicio
    AND plp.cod_ordem               = inCodOrdem;

    RETURN nuValorPago;

END;
'LANGUAGE 'plpgsql';

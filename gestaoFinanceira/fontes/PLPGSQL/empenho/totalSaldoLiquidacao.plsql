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
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_total_saldo_liquidacao(VARCHAR,NUMERIC,NUMERIC) RETURNS NUMERIC AS '
DECLARE
    stExercicio       		ALIAS FOR $1;
    inCodNota               ALIAS FOR $2;
    inCodEntidade           ALIAS FOR $3;
    nuTotal         		NUMERIC;

BEGIN
    SELECT
        coalesce(sum(nli.vl_total),0.00) - coalesce(sum(nla.vl_anulado),0.00)
        INTO nuTotal
    FROM
	    empenho.nota_liquidacao_item                    as nli
        LEFT JOIN empenho.nota_liquidacao_item_anulado  as nla on(
    	    nli.exercicio       = nla.exercicio
        AND nli.cod_nota        = nla.cod_nota
        AND nli.cod_entidade    = nla.cod_entidade
        AND nli.num_item        = nla.num_item
        AND nli.cod_pre_empenho = nla.cod_pre_empenho
        AND nli.exercicio_item  = nla.exercicio_item
        )
    WHERE
        nli.exercicio    	= stExercicio
    AND nli.cod_nota    	= inCodNota
	AND nli.cod_entidade  	= inCodEntidade
    ;

    RETURN nuTotal;
END;
' LANGUAGE 'plpgsql';

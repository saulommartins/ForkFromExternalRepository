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
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:37  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_liquidado_anulado_nota_item(VARCHAR,INTEGER,INTEGER,INTEGER, INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inCodNota                  ALIAS FOR $4;
    inNumItem                  ALIAS FOR $5;
    nuValorLiquidacaoAnulada   NUMERIC := 0.00;
BEGIN

    SELECT
        coalesce(sum(IA.vl_anulado),0.00)
        INTO nuValorLiquidacaoAnulada
    FROM     empenho.empenho                       AS  E
            ,empenho.nota_liquidacao               AS NL
            ,empenho.nota_liquidacao_item          AS LI
            ,empenho.nota_liquidacao_item_anulado  AS IA
    WHERE   NL.exercicio_empenho = E.exercicio
    AND     NL.cod_empenho       = E.cod_empenho
    AND     NL.cod_entidade      = E.cod_entidade
    AND     LI.exercicio         = NL.exercicio
    AND     LI.cod_nota          = NL.cod_nota
    AND     LI.cod_entidade      = NL.cod_entidade
    AND     IA.cod_entidade      = LI.cod_entidade
    AND     IA.cod_nota          = LI.cod_nota
    AND     IA.exercicio         = LI.exercicio
    AND     IA.num_item          = LI.num_item
    AND     E.cod_entidade       = inCodEntidade
    AND     E.cod_empenho        = inCodEmpenho
    AND     NL.exercicio         = stExercicio
    AND     LI.cod_nota          = inCodNota
    AND     LI.num_item          = inNumItem
    ;

    IF nuValorLiquidacaoAnulada IS NULL THEN
        nuValorLiquidacaoAnulada := 0.00;
    END IF;

    RETURN nuValorLiquidacaoAnulada;

END;
'LANGUAGE 'plpgsql';

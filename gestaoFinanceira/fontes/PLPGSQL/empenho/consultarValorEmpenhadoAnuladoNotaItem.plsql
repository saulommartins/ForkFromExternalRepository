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
* Casos de uso: uc-02.03.03
*               uc-02.03.04
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:37  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_empenhado_anulado_item(VARCHAR,INTEGER,INTEGER,INTEGER,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    inNumItem                  ALIAS FOR $4;
    inNumNota                  ALIAS FOR $5;
    nuValorAnulado             NUMERIC := 0.00;
    inCodPreEmpenho            INTEGER := 0;
BEGIN
    SELECT      cod_pre_empenho
         INTO   inCodPreEmpenho
    FROM    empenho.empenho
    WHERE   cod_empenho     = inCodEmpenho
    AND     exercicio       = stExercicio
    AND     cod_entidade    = inCodEntidade
    ;

    SELECT      coalesce(sum(vl_anulado),0.00)
        INTO    nuValorAnulado
    FROM
    empenho.empenho_anulado_item as eai,
    empenho.item_pre_empenho as ipe,
    empenho.nota_liquidacao_item as nli

    WHERE
        eai.cod_pre_empenho = ipe.cod_pre_empenho   AND
        eai.exercicio       = ipe.exercicio       AND
        eai.num_item        = ipe.num_item          AND

        ipe.cod_pre_empenho = nli.cod_pre_empenho   AND
        ipe.exercicio       = nli.exercicio_item       AND
        ipe.num_item        = nli.num_item          AND



            eai.cod_entidade     = inCodEntidade
    AND     eai.cod_empenho      = inCodEmpenho
    AND     eai.exercicio        = stExercicio
    AND     eai.num_item         = inNumItem
    AND     nli.cod_nota         = inNumNota
    AND     eai.cod_pre_empenho  = inCodPreEmpenho
    ;

    IF nuValorAnulado IS NULL THEN
        nuValorAnulado := 0.00;
    END IF;

    RETURN nuValorAnulado;

END;
'LANGUAGE 'plpgsql';

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
* $Revision: 16074 $
* $Name$
* $Author: eduardo $
* $Date: 2006-09-28 06:56:56 -0300 (Qui, 28 Set 2006) $
*
* Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.6  2006/09/28 09:56:56  eduardo
Bug #7060#

Revision 1.5  2006/07/05 20:37:37  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_apagar_anulado_nota(VARCHAR,INTEGER,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodNota                  ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    nuValor                    NUMERIC := 0.00;
BEGIN

    SELECT
        coalesce(sum(opa.vl_anulado), 0.00)
        INTO nuValor
    FROM     (
                select  opla.cod_ordem
                       ,opla.exercicio
                       ,opla.cod_entidade
                       ,coalesce(sum(vl_anulado),0.00) as vl_anulado
                from empenho.ordem_pagamento_liquidacao_anulada as opla
                
                where     opla.cod_nota             = inCodNota
                      and opla.cod_entidade         = inCodEntidade
                      and opla.exercicio_liquidacao = stExercicio

                group by  opla.cod_ordem
                         ,opla.exercicio
                         ,opla.cod_entidade
             ) as opa

            ,empenho.ordem_pagamento         AS OP
            ,empenho.pagamento_liquidacao    AS PL
            ,empenho.nota_liquidacao         AS NL
    WHERE   OPA.cod_ordem           = OP.cod_ordem
    AND     OPA.exercicio           = OP.exercicio
    AND     OPA.cod_entidade        = OP.cod_entidade

    AND     OP.cod_ordem            = PL.cod_ordem
    AND     OP.exercicio            = PL.exercicio
    AND     OP.cod_entidade         = PL.cod_entidade

    AND     PL.cod_nota             = NL.cod_nota
    AND     Pl.exercicio_liquidacao = NL.exercicio
    AND     PL.cod_entidade         = NL.cod_entidade

    AND     NL.cod_entidade         = inCodEntidade
    AND     NL.cod_nota             = inCodNota
    AND     NL.exercicio            = stExercicio
    ;

    IF nuValor IS NULL THEN
        nuValor := 0.00;
    END IF;

    RETURN nuValor;

END;
'LANGUAGE 'plpgsql';

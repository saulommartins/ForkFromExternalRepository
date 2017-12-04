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
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:37  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_consultar_valor_empenhado_pago_anulado(VARCHAR,INTEGER,INTEGER) RETURNS NUMERIC AS $$

DECLARE
    stExercicio                ALIAS FOR $1;
    inCodEmpenho               ALIAS FOR $2;
    inCodEntidade              ALIAS FOR $3;
    nuValor                    NUMERIC := 0.00;
BEGIN

set search_path = empenho,public;
select
        sum( coalesce( nota_liquidacao_paga_anulada.vl_anulado, 0 ) ) as vl_anulado
            INTO nuValor
        from nota_liquidacao, nota_liquidacao_paga_anulada, empenho e
        where e.exercicio    = nota_liquidacao.exercicio_empenho and
               e.cod_empenho  = nota_liquidacao.cod_empenho       and
               e.cod_entidade = nota_liquidacao.cod_entidade      and
               nota_liquidacao.exercicio                      = nota_liquidacao_paga_anulada.exercicio    and
               nota_liquidacao.cod_nota                       = nota_liquidacao_paga_anulada.cod_nota     and
               nota_liquidacao.cod_entidade                   = nota_liquidacao_paga_anulada.cod_entidade and
               e.cod_empenho  = inCodEmpenho   and
               e.cod_entidade = inCodEntidade  and
               e.exercicio    = stExercicio
        group by e.exercicio,
                  e.cod_empenho,
                  e.cod_entidade,
                  nota_liquidacao.exercicio_empenho,
                  nota_liquidacao.cod_empenho,
                  nota_liquidacao.cod_entidade
;

    IF nuValor IS NULL THEN
        nuValor := 0.00;
    END IF;

    SET search_path = public, pg_catalog;

    RETURN nuValor;

END;
$$ LANGUAGE 'plpgsql';

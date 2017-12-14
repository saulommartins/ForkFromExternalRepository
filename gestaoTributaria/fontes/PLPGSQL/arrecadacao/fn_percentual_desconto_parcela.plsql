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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_percentual_desconto_parcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.8  2006/10/02 17:40:09  domluc
Correção de Bug relatado pela Michelle, repassado pelo Fabio

Revision 1.7  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_percentual_desconto_parcela(int,date, int) returns numeric as '
declare
    inCodParcela    ALIAS FOR $1;
    dtDataCalculo   ALIAS FOR $2;
    inExercicio        ALIAS FOR $3;
    nuRetorno       numeric;
    nuOriginal      numeric;
    nuDesconto      numeric;
    nuDescontoO     numeric;
begin
-- Valor Original
select valor into nuOriginal  from arrecadacao.parcela where cod_parcela = inCodParcela;
select valor into nuDescontoO from arrecadacao.parcela_desconto where cod_parcela = inCodParcela;
-- Valor Desconto
    
    SELECT 
        sum(alc.valor) 
    INTO 
        nuDesconto 
    FROM
        arrecadacao.lancamento_calculo alc 
        INNER JOIN arrecadacao.calculo as calc ON calc.cod_calculo = alc.cod_calculo
        INNER JOIN arrecadacao.calculo_grupo_credito cgc ON cgc.cod_calculo = alc.cod_calculo 
        INNER JOIN arrecadacao.credito_grupo cg ON cg.cod_credito = calc.cod_credito  
                                                                                AND cgc.cod_grupo = cg.cod_grupo  
                                                                                AND cgc.ano_exercicio = cg.ano_exercicio
    WHERE
        alc.cod_lancamento in (  select cod_lancamento 
                                                from arrecadacao.parcela 
                                                where cod_parcela = inCodParcela )
       and cg.desconto = true
       and cg.ano_exercicio = quote_literal(inExercicio);


if ( nuOriginal > nuDescontoO ) and (nuDesconto > 0) and (nuOriginal > 0)then
    nuRetorno := arrecadacao.fn_juro_multa_aplicado_reemissao(nuDesconto,nuDesconto+(nuOriginal-nuDescontoO));
else
    nuRetorno := NULL;
end if;

    return coalesce(nuRetorno,0.00);
    --
end;
'language 'plpgsql';

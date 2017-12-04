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
* $Id: calculaJuroOrMultaParcelasReemissao.plsql 59612 2014-09-02 12:00:51Z gelson $

* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.6  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.calculaJuroOrMultaParcelasReemissao(varchar,integer,integer,date,varchar) returns varchar as '
declare
    stNumeracao     ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodParcela    ALIAS FOR $3;
    dtDataBase      ALIAS FOR $4;
    stTipo          ALIAS FOR $5;
    nuResultado         numeric := 0.00;
    nuProporcao         numeric;
    nuValorParcela      numeric;
    nuValorCorrigido    numeric;
    nuRetorno           numeric := 0.00;
begin
    -- valor da parcela
    SELECT valor INTO nuValorParcela FROM arrecadacao.parcela WHERE cod_parcela = inCodParcela;
    -- proporcao da parcela para lancamento
    nuProporcao := arrecadacao.calculaProporcaoParcela(inCodParcela);
    -- aplica
    if  stTipo = ''j'' then
        nuResultado := aplica_juro(stNumeracao,inExercicio,inCodParcela,dtDataBase) * nuProporcao;
    elsif stTipo = ''m'' then
        nuResultado := aplica_multa(stNumeracao,inExercicio,inCodParcela,dtDataBase) * nuProporcao;
    end if;

    -- valor da parcela corrigido
    nuValorCorrigido := nuValorParcela + nuResultado;  
    -- retorno
    nuRetorno := arrecadacao.fn_juro_multa_aplicado_reemissao(nuValorParcela,nuValorCorrigido::numeric(14,2));    

    -- arredonda para valores utilizados por petropolis

    if  stTipo = ''m'' then
        if nuRetorno = 4 or nuRetorno = 6 then
            nuRetorno := 5;
        elsif nuRetorno = 9 or nuRetorno = 11 then
            nuRetorno := 10;
        elsif nuRetorno = 14 or nuRetorno = 16 then
            nuRetorno := 15;
        elsif nuRetorno = 19 or nuRetorno = 21 then
            nuRetorno := 20;
        end if;
    end if;
/*
    if  stTipo = ''j'' then
        if nuRetorno = 4 or nuRetorno = 6 then
            nuRetorno := 5;
        elsif nuRetorno = 9 or nuRetorno = 11 then
            nuRetorno := 10;
        elsif nuRetorno = 14 or nuRetorno = 16 then
            nuRetorno := 15;
        elsif nuRetorno = 19 or nuRetorno = 21 then
            nuRetorno := 20;
        end if;
    end if;
*/
   return lpad(nuRetorno::varchar,2,0::varchar);

end;
'language 'plpgsql';

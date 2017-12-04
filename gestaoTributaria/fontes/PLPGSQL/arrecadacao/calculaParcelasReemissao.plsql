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
* $Id: calculaParcelasReemissao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.6  2006/10/24 18:43:46  dibueno
Verificação se há registro na tabela parcela_reemissao, para buscar o valor orignal para o cálculo

Revision 1.5  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.calculaParcelasReemissao(varchar,integer,integer,date) returns numeric as '
declare
    stNumeracao     ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodParcela    ALIAS FOR $3;
    dtDataBase      ALIAS FOR $4;
    nuJuros         numeric;
    nuMulta         numeric;
    nuProporcao     numeric;
    nuValorParcela  numeric;
    nuRetorno       numeric := 0.00;
    inNrParcela     integer;
begin
    -- valor da parcela
    SELECT  nr_parcela,
			case when pd.cod_parcela is not null then
				pd.valor
            else
				case when apr.cod_parcela is not null then
					apr.valor
				else
                	p.valor
				end
            end as valor
       into inNrParcela , nuValorParcela
       FROM arrecadacao.parcela p
  		left join arrecadacao.parcela_desconto pd
        on pd.cod_parcela = p.cod_parcela
		LEFT JOIN
        (
            select apr.cod_parcela, vencimento, valor
            from arrecadacao.parcela_reemissao apr
            inner join (
                select cod_parcela, min(timestamp) as timestamp
                from arrecadacao.parcela_reemissao as x
                group by cod_parcela
            ) as apr2
            ON apr2.cod_parcela = apr.cod_parcela AND
            apr2.timestamp = apr.timestamp
        ) as apr
        ON apr.cod_parcela = p.cod_parcela

      WHERE p.cod_parcela = inCodParcela;

    -- proporcao da parcela para lancamento
    nuProporcao := arrecadacao.calculaProporcaoParcela(inCodParcela);
    -- juros
    nuJuros := aplica_juro(stNumeracao,inExercicio,inCodParcela,dtDataBase) * nuProporcao;
    -- multa
    nuMulta := aplica_multa(stNumeracao,inExercicio,inCodParcela,dtDataBase) * nuProporcao;

    -- retorno
    nuRetorno := nuValorParcela + nuMulta + nuJuros;  

   return nuRetorno::numeric(14,2);

end;
'language 'plpgsql';

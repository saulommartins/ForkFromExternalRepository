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
* $Id: calculaValoresParcelasReemissao.plsql 64141 2015-12-08 16:23:59Z evandro $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.3  2007/08/30 12:57:02  cercato
Bug#10043#

Revision 1.2  2007/06/26 17:59:25  cercato
Bug #9488#

Revision 1.1  2006/12/12 15:20:55  cercato
*** empty log message ***

*/

CREATE OR REPLACE FUNCTION arrecadacao.calculaValoresParcelasReemissao(varchar,integer,integer,date) returns varchar as $$
DECLARE
    stNumeracao     ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodParcela    ALIAS FOR $3;
    dtDataBase      ALIAS FOR $4;
    nuJuros         numeric;
    nuMulta         numeric;
    nuCorrecao      numeric;
    nuProporcao     numeric;
    nuValorParcela  numeric;
    nuDescontoParcela  numeric;
    nuTotal         numeric;
    nuRetorno       varchar;
    inNrParcela     integer;
begin
    -- valor da parcela
    SELECT  nr_parcela,
			case when ( pd.cod_parcela is not null ) AND ( pd.vencimento >= p.vencimento ) then
				pd.valor
            else
				case when apr.cod_parcela is not null then
					apr.valor
				else
                	p.valor
				end
            end as valor,
            case when ( pd.cod_parcela is not null ) AND ( pd.vencimento >= p.vencimento ) then
                case when apr.cod_parcela is not null then
                    apr.valor - pd.valor
                else
                    p.valor - pd.valor
                end
            else
                0.00
            end as desconto
    
    INTO inNrParcela 
        , nuValorParcela
        , nuDescontoParcela
    
    FROM arrecadacao.parcela p
    
    LEFT JOIN arrecadacao.parcela_desconto pd
           ON pd.cod_parcela = p.cod_parcela
		
    LEFT JOIN(  SELECT apr.cod_parcela, vencimento, valor
                FROM arrecadacao.parcela_reemissao apr
                INNER JOIN ( SELECT cod_parcela
                                    , min(timestamp) as timestamp
                             FROM arrecadacao.parcela_reemissao as x
                             GROUP BY cod_parcela
                ) as apr2
                    ON apr2.cod_parcela = apr.cod_parcela 
                   AND apr2.timestamp = apr.timestamp
    ) as apr
        ON apr.cod_parcela = p.cod_parcela

    WHERE p.cod_parcela = inCodParcela;

    -- proporcao da parcela para lancamento
    nuProporcao := COALESCE(arrecadacao.calculaProporcaoParcela(inCodParcela),0.00);
    -- juros
    nuJuros     := COALESCE(aplica_juro(stNumeracao,inExercicio,inCodParcela,dtDataBase),0.00);
    -- multa
    nuMulta     := COALESCE(aplica_multa(stNumeracao,inExercicio,inCodParcela,dtDataBase),0.00);
    -- correcao
    nuCorrecao  := COALESCE(aplica_correcao(stNumeracao,inExercicio,inCodParcela,dtDataBase),0.00);

    -- retorno
    nuTotal := ( nuValorParcela + nuMulta + nuJuros + nuCorrecao );
    nuRetorno := nuTotal||'§'||nuValorParcela||'§'||nuMulta||'§'||nuJuros||'§'||nuDescontoParcela||'§'||nuCorrecao;

   return nuRetorno::varchar;

end;
$$ language 'plpgsql';

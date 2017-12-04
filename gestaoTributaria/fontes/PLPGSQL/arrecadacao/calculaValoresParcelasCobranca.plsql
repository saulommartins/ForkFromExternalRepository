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
* $Id: calculaValoresParcelasReemissao.plsql 61622 2015-02-18 15:50:46Z evandro $
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

CREATE OR REPLACE FUNCTION arrecadacao.calculaValoresParcelasCobranca(integer,integer,integer,integer) returns varchar as $$
DECLARE        
    inNumParcelamento   ALIAS FOR $1;
    inNumParcela        ALIAS FOR $2;    
    inCodLancamento     ALIAS FOR $3;    
    inCodParcela        ALIAS FOR $4;
    nuJuros             numeric;
    nuMulta             numeric;
    nuCorrecao          numeric;
    nuProporcao         numeric;
    nuValorOrigem       numeric;
    nuValorParcela      numeric;
    nuDescontoParcela   numeric;
    nuComissao          numeric;
    nuRetorno           varchar;
    inNrParcela         integer;
BEGIN

    --valor de origem da parcela    
    SELECT 
            SUM(valor)
            INTO nuValorOrigem                
    FROM divida.parcela_origem 
    WHERE num_parcelamento = inNumParcelamento;

    -- valor da parcela
    SELECT DISTINCT
                  parcela.num_parcela
                , parcela.vlr_parcela
                , parcela_reducao.valor as desconto    
                , acrescimos.vlr_correcao
                , acrescimos.vlr_juros
                , acrescimos.vlr_multas
                , acrescimos.vlr_comissao
            INTO 
                 inNrParcela
                , nuValorParcela
                , nuDescontoParcela
                , nuCorrecao
                , nuJuros
                , nuMulta
                , nuComissao

    FROM divida.parcela 

    JOIN divida.parcelamento
        ON parcelamento.num_parcelamento = parcela.num_parcelamento

    JOIN divida.parcela_reducao
         ON parcela_reducao.num_parcelamento = parcela.num_parcelamento
        AND parcela_reducao.num_parcela     = parcela.num_parcela

    JOIN divida.parcela_acrescimo
         ON parcela_acrescimo.num_parcelamento   = parcela.num_parcelamento
        AND parcela_acrescimo.num_parcela       = parcela.num_parcela

    JOIN (  SELECT    SUM(vlr_correcao) as vlr_correcao
                    , SUM(vlr_juros) as vlr_juros
                    , SUM(vlr_multas) as vlr_multas
                    , SUM(vlr_comissao) as vlr_comissao
                    , num_parcelamento
            FROM(
                SELECT    vlracrescimo as vlr_correcao
                        , 0.00 as vlr_juros
                        , 0.00 as vlr_multas
                        , 0.00 as vlr_comissao
                        , parcela_acrescimo.num_parcelamento
                FROM divida.parcela_acrescimo 
                WHERE num_parcelamento = inNumParcelamento
                AND cod_tipo = 1
                AND num_parcela = inNumParcela

                UNION
    
                SELECT    0.00 as vlr_correcao
                        , vlracrescimo as vlr_juros
                        , 0.00 as vlr_multas
                        , 0.00 as vlr_comissao
                        , parcela_acrescimo.num_parcelamento
                FROM divida.parcela_acrescimo 
                WHERE num_parcelamento = inNumParcelamento
                AND cod_tipo = 2
                AND num_parcela = inNumParcela

                UNION
    
                SELECT    0.00 as vlr_correcao
                        , 0.00 as vlr_juros
                        , vlracrescimo as vlr_multas
                        , 0.00 as vlr_comissao
                        , parcela_acrescimo.num_parcelamento
                FROM divida.parcela_acrescimo 
                WHERE num_parcelamento = inNumParcelamento
                AND cod_tipo = 3
                AND cod_acrescimo = 3
                AND num_parcela = inNumParcela

                UNION 
                
                SELECT    0.00 as vlr_correcao
                        , 0.00 as vlr_juros
                        , 0.00 as vlr_multas
                        , vlracrescimo as vlr_comissao
                        , parcela_acrescimo.num_parcelamento
                FROM divida.parcela_acrescimo 
                WHERE num_parcelamento = inNumParcelamento
                AND cod_tipo = 3
                AND cod_acrescimo = 4
                AND num_parcela = inNumParcela

            )as retorno
            GROUP BY num_parcelamento
    )as acrescimos
        ON acrescimos.num_parcelamento = parcela.num_parcelamento
        
    JOIN arrecadacao.parcela as AP
        on AP.cod_lancamento = inCodLancamento
        AND AP.cod_parcela = inCodParcela

    WHERE parcela.num_parcelamento = inNumParcelamento
    AND parcela.num_parcela = inNumParcela;


    -- retorno
    nuRetorno := nuValorOrigem||'§'||nuJuros||'§'||nuMulta||'§'||nuComissao||'§'||nuCorrecao||'§'||nuDescontoParcela||'§'||nuValorParcela;

   return nuRetorno::varchar;

end;
$$ language 'plpgsql';

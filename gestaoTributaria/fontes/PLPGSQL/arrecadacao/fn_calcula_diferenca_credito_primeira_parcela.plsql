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
* $Id: fn_calcula_diferenca_credito_primeira_parcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_calcula_diff_credito_primeira_parcela( INTEGER, INTEGER )  RETURNS NUMERIC AS '
DECLARE
    inCodLancamento ALIAS FOR $1;
    inCodCalculo         ALIAS FOR $2;
    nuRetorno            NUMERIC;
BEGIN
    select
        sum(tabelaA.valor_credito) - tabelaA.valor_total_credito
    INTO
        nuRetorno
    from
    (
        select
            alc.valor as valor_total_credito,
            ( alc.valor
            * arrecadacao.calculaProporcaoParcela(apar.cod_parcela)
            )::numeric(14,2) as valor_credito,
            apar.nr_parcela
        from
            arrecadacao.lancamento as al
            INNER JOIN arrecadacao.lancamento_calculo as alc ON alc.cod_lancamento = al.cod_lancamento
            INNER JOIN arrecadacao.parcela as apar ON apar.cod_lancamento = al.cod_lancamento
        where al.cod_lancamento = inCodLancamento
        and alc.cod_calculo = inCodCalculo
        and apar.nr_parcela > 0
        ORDER BY apar.nr_parcela
        
    ) as tabelaA
    
    GROUP BY  tabelaA.valor_total_credito;
    
    RETURN nuRetorno;
END;
' LANGUAGE 'plpgsql';

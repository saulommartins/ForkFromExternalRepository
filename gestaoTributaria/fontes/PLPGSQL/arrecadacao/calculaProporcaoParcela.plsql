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
* $Id: calculaProporcaoParcela.plsql 61622 2015-02-18 15:50:46Z evandro $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.4  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.calculaProporcaoParcela(integer) RETURNS numeric as $$
    DECLARE
        inCodParcela        ALIAS FOR $1;
        inCodLancamento     INT;
        nuValorParcela      NUMERIC;
        nuValorLancamento   NUMERIC;
        nuRetorno           NUMERIC;
    BEGIN
        
        SELECT lancamento.cod_lancamento
             , lancamento.valor
             , CASE WHEN parcela_reemissao.cod_parcela IS NOT NULL THEN
                 parcela_reemissao.valor 
               ELSE
                 parcela.valor
               END AS valor
          INTO inCodLancamento
             , nuValorLancamento
             , nuValorParcela 
          FROM arrecadacao.parcela
     LEFT JOIN (  SELECT valor
                       , cod_parcela
                    FROM arrecadacao.parcela_reemissao
                   WHERE cod_parcela= inCodParcela
                ORDER BY timestamp ASC LIMIT 1) AS parcela_reemissao
            ON parcela.cod_parcela = parcela_reemissao.cod_parcela
    INNER JOIN arrecadacao.lancamento
            ON lancamento.cod_lancamento = parcela.cod_lancamento
         WHERE parcela.cod_parcela= inCodParcela;

        IF ( nuValorLancamento > 0 and nuValorParcela > 0 ) THEN
            nuRetorno := ((nuValorParcela*100)/nuValorLancamento)/100;
        ELSE
            nuRetorno := 0.00;
        END IF;

        return nuRetorno;  
    END;
$$ language 'plpgsql';
           

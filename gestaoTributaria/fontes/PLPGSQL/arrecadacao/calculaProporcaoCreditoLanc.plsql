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
* $Id: calculaProporcaoCreditoLanc.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.calculaProporcaoCreditoLanc(integer) RETURNS numeric as '
    DECLARE
        inCodCalculo        ALIAS FOR $1;
        nuValorCalculo      NUMERIC;
        nuValorLancamento   NUMERIC;
        nuRetorno           NUMERIC;
    BEGIN

           select c.valor, l.valor
             into nuValorCalculo, nuValorLancamento
             from arrecadacao.calculo c
                , arrecadacao.lancamento_calculo lc
                , arrecadacao.lancamento l
            where c.cod_calculo = lc.cod_calculo
              and lc.cod_lancamento = l.cod_lancamento
              and c.cod_calculo = inCodCalculo; 

        IF nuValorLancamento > 0 THEN
            nuRetorno := ((nuValorCalculo*100)/nuValorLancamento)/100;
        ELSE
            nuRetorno := 0;
        END IF;

        return nuRetorno;  
    END;
'language 'plpgsql';
           

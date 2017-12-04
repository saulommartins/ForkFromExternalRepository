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
* Valor de um credito do lancamento
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
* Caso de uso: uc-05.03.05
*/


CREATE OR REPLACE FUNCTION arrecadacao.buscaValorCreditoLancamento( integer, integer, integer, integer, integer ) returns numeric as '
declare
    inLancamento    ALIAS FOR $1;
    inCodCredito    ALIAS FOR $2;
    inCodEspecie    ALIAS FOR $3;
    inCodGenero     ALIAS FOR $4;
    inCodNatureza   ALIAS FOR $5;
    nuValorCredito  numeric;

begin
    SELECT
        lancamento_calculo.valor

    INTO
        nuValorCredito

    FROM
        arrecadacao.lancamento_calculo

    INNER JOIN
        arrecadacao.calculo
    ON
        calculo.cod_calculo = lancamento_calculo.cod_calculo

    WHERE
        lancamento_calculo.cod_lancamento = inLancamento
        AND calculo.cod_credito = inCodCredito
        AND calculo.cod_especie = inCodEspecie
        AND calculo.cod_genero = inCodGenero
        AND calculo.cod_natureza = inCodNatureza
    ;

    return nuValorCredito;
end;
'language 'plpgsql';

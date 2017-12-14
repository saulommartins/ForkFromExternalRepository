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
* $Id: fn_urm_mariana.plsql 64154 2015-12-10 13:31:39Z fabio $
*
* Caso de uso: uc-05.03.00
* Calculo Valor de Juros Especial (Mata)
*/

CREATE OR REPLACE FUNCTION fn_urm_mariana (date,date,numeric,integer,integer) RETURNS numeric as $$

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        nuValor         ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        nuValorOrigem   NUMERIC;
        nuValorCalculo  NUMERIC;
        nuPercent       NUMERIC = 0.00;
        nuCorrecao      NUMERIC = 0.00;
        nuRetorno       NUMERIC = 0.00;
        inDiff          INTEGER;
        dtMinVigencia   DATE;
        dtMaxVigencia   DATE;

    BEGIN
        SELECT valor_acrescimo.valor
          INTO nuValorOrigem
          FROM monetario.valor_acrescimo
          JOIN (
                   SELECT MAX(inicio_vigencia) AS vigencia
                        , cod_acrescimo
                        , cod_tipo
                     FROM monetario.valor_acrescimo
                    WHERE cod_acrescimo    = inCodAcrescimo
                      AND cod_tipo         = inCodTipo
                      AND inicio_vigencia <= dtVencimento
                 GROUP BY cod_acrescimo
                        , cod_tipo
               ) AS interna
            ON interna.cod_acrescimo = valor_acrescimo.cod_acrescimo
           AND interna.cod_tipo      = valor_acrescimo.cod_tipo
           AND interna.vigencia      = valor_acrescimo.inicio_vigencia
         WHERE valor_acrescimo.cod_acrescimo   = inCodAcrescimo
           AND valor_acrescimo.cod_tipo        = inCodTipo
             ;

        SELECT valor_acrescimo.valor
          INTO nuValorCalculo
          FROM monetario.valor_acrescimo
          JOIN (
                   SELECT MAX(inicio_vigencia) AS vigencia
                        , cod_acrescimo
                        , cod_tipo
                     FROM monetario.valor_acrescimo
                    WHERE cod_acrescimo    = inCodAcrescimo
                      AND cod_tipo         = inCodTipo
                      AND inicio_vigencia <= dtDataCalculo
                 GROUP BY cod_acrescimo
                        , cod_tipo
               ) AS interna
            ON interna.cod_acrescimo = valor_acrescimo.cod_acrescimo
           AND interna.cod_tipo      = valor_acrescimo.cod_tipo
           AND interna.vigencia      = valor_acrescimo.inicio_vigencia
         WHERE valor_acrescimo.cod_acrescimo   = inCodAcrescimo
           AND valor_acrescimo.cod_tipo        = inCodTipo
             ;

        nuCorrecao := ((nuValor / nuValorOrigem) * nuValorCalculo) - nuValor;

        nuRetorno  := nuCorrecao::NUMERIC(14,2);

        RETURN nuRetorno::numeric(14,2);
    END;
$$ language 'plpgsql';


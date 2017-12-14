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
CREATE OR REPLACE FUNCTION monetario.buscaValorAcrescimo(INTEGER,INTEGER,INTEGER,INTEGER) RETURNS NUMERIC AS $$
DECLARE
    inCodAcrescimo      ALIAS FOR $1;
    inCodTipo           ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    inAno               ALIAS FOR $4;
    flRetorno           NUMERIC;

BEGIN
    IF ( inMes >= 0 ) THEN
        SELECT
            sum(valor)
        INTO
            flRetorno
        FROM
            monetario.valor_acrescimo
        WHERE
            cod_acrescimo = inCodAcrescimo
            AND cod_tipo = inCodTipo
            AND extract(year from inicio_vigencia) = inAno
            AND extract(month from inicio_vigencia) = inMes;
    ELSE
        SELECT
            sum(valor)
        INTO
            flRetorno
        FROM
            monetario.valor_acrescimo
        WHERE
            cod_acrescimo = inCodAcrescimo
            AND cod_tipo = inCodTipo
            AND extract(year from inicio_vigencia) = inAno;
    END IF;

    RETURN flRetorno;
END;
$$ LANGUAGE 'plpgsql';

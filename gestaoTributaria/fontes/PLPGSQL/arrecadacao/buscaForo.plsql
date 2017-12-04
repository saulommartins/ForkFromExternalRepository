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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 1.3 $
* $Name:  $
* $Author: fabio $
* $Date: 2006/09/15 10:20:09 $
*
* Casos d uso: uc-05.03.05
*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaForo( INTEGER ) RETURNS BOOLEAN AS $$
DECLARE
    inCodLancamento     ALIAS FOR $1;
    inResultado         INTEGER;
    boRetorno           BOOLEAN;

BEGIN
    SELECT DISTINCT
        lancamento_calculo.cod_lancamento
    INTO
        inResultado

    FROM
        arrecadacao.lancamento_calculo
    
    INNER JOIN
        arrecadacao.calculo
    ON
        calculo.cod_credito = 7
        and calculo.cod_especie = 100
        and calculo.cod_natureza = 2
        and calculo.cod_genero = 7
        and calculo.cod_calculo = lancamento_calculo.cod_calculo
    
    WHERE
        lancamento_calculo.cod_lancamento = inCodLancamento;

    IF inResultado IS NOT NULL THEN
        boRetorno := true;
    ELSE
        boRetorno := false;
    END IF;

    RETURN boRetorno;
END;

$$ LANGUAGE 'plpgsql';

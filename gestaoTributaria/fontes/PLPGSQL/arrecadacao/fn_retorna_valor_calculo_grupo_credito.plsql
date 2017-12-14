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
* $Id: $
*
* Caso de uso: uc-05.03.00
*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_retorna_valor_calculo_grupo_credito(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,INTEGER) returns numeric AS $$
DECLARE
    inInscricao         ALIAS FOR $1;
    inCodGrupo          ALIAS FOR $2;
    inExercicioGrupo    ALIAS FOR $3;
    inExercicio         ALIAS FOR $4;
    inCodCredito        ALIAS FOR $5;
    inCodEspecie        ALIAS FOR $6;
    inCodGenero         ALIAS FOR $7;
    inCodNatureza       ALIAS FOR $8;
    nuResultado     NUMERIC;
BEGIN

    SELECT
        lancamento_calculo.valor
    INTO 
        nuResultado
    FROM
        arrecadacao.lancamento_calculo

    INNER JOIN
        arrecadacao.calculo
    ON
        calculo.cod_calculo = lancamento_calculo.cod_calculo

    INNER JOIN
        arrecadacao.imovel_calculo
    ON
        imovel_calculo.cod_calculo = calculo.cod_calculo

    INNER JOIN
        arrecadacao.calculo_grupo_credito
    ON
        calculo_grupo_credito.cod_calculo = calculo.cod_calculo

    WHERE
        calculo.cod_credito = inCodCredito
        AND calculo.cod_especie = inCodEspecie
        AND calculo.cod_genero = inCodGenero
        AND calculo.cod_natureza = inCodNatureza
        AND calculo.exercicio = inExercicio
        AND imovel_calculo.inscricao_municipal = inInscricao
        AND calculo.ativo = true
        AND calculo_grupo_credito.cod_grupo = inCodGrupo
        AND calculo_grupo_credito.ano_exercicio = ''||inExercicioGrupo||''

    ORDER BY
        calculo.timestamp DESC

    LIMIT 1;

    return coalesce( nuResultado, 0.00 );

END;
$$ LANGUAGE 'plpgsql';

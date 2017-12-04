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
* $Id: areaUnidadeDependente.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/


CREATE OR REPLACE FUNCTION imobiliario.fn_area_unidade_dependente( INTEGER, INTEGER ) RETURNS numeric AS '
DECLARE
    inCodConstrucao      ALIAS FOR $1;
    inInscricaoMunicipal ALIAS FOR $2;
    nuAreaTotal          NUMERIC;
    reRegistro           RECORD;
    stSql                VARCHAR;

BEGIN

    SELECT
        aud.area
    INTO nuAreaTotal
    FROM
    (
        SELECT
            aud.inscricao_municipal,
            aud.cod_construcao_dependente,
            aud.cod_tipo,
            aud.cod_construcao,
            aud."timestamp",
            aud.area
        FROM
            imobiliario.area_unidade_dependente aud,
            (
                SELECT
                    max(area_unidade_dependente."timestamp") AS "timestamp",
                    area_unidade_dependente.cod_construcao_dependente,
                    area_unidade_dependente.inscricao_municipal
                FROM
                    imobiliario.area_unidade_dependente
                GROUP BY
                    area_unidade_dependente.cod_construcao_dependente,
                    area_unidade_dependente.inscricao_municipal
            ) maud
        WHERE
            aud.cod_construcao_dependente = maud.cod_construcao_dependente AND
            aud.inscricao_municipal = maud.inscricao_municipal AND
            aud."timestamp" = maud."timestamp"
    ) AS aud
    WHERE
        aud.cod_construcao_dependente = $1 AND
        aud.inscricao_municipal = $2;

    RETURN nuAreaTotal;
END;
'language 'plpgsql';

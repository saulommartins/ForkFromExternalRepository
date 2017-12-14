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
* $Id: areaUnidadeAutonoma.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/


CREATE OR REPLACE FUNCTION imobiliario.fn_area_unidade_autonoma( INTEGER, INTEGER ) RETURNS numeric AS '
DECLARE
    inCodConstrucao      ALIAS FOR $1;
    inInscricaoMunicipal ALIAS FOR $2;
    nuAreaTotal          NUMERIC;
    reRegistro           RECORD;
    stSql                VARCHAR;

BEGIN

    SELECT
        aua.area
    INTO nuAreaTotal
    FROM (
    SELECT
        aua.inscricao_municipal,
        aua.cod_tipo,
        aua.cod_construcao,
        aua."timestamp",
        aua.area
    FROM
        imobiliario.area_unidade_autonoma aua,
        (
            SELECT
                max(area_unidade_autonoma."timestamp") AS "timestamp",
                area_unidade_autonoma.cod_construcao,
                area_unidade_autonoma.inscricao_municipal
            FROM
                imobiliario.area_unidade_autonoma
            GROUP BY
                area_unidade_autonoma.cod_construcao,
                area_unidade_autonoma.inscricao_municipal
        ) maua
    WHERE
        aua.cod_construcao = maua.cod_construcao AND
        aua.inscricao_municipal = maua.inscricao_municipal AND
        aua."timestamp"= maua."timestamp"
    ) aua
    WHERE
        aua.cod_construcao = $1 AND
        aua.inscricao_municipal = $2;

    RETURN nuAreaTotal;
END;
'language 'plpgsql';

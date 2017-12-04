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
* $Id: fn_busca_area_construida.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.9  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_area_construida(INTEGER,INTEGER)  RETURNS NUMERIC(20,4) AS '
DECLARE
    inIM                        ALIAS FOR $1;
    inCodConstrucao             ALIAS FOR $2;
    inTipo                      INTEGER;
    stSql                       VARCHAR := '''';
    nuResultado                 NUMERIC := 0;
    boLog                       BOOLEAN;

BEGIN
-- 1 = AUTONOMA
-- 2 = DEPENDENTE
    SELECT
        CASE
            WHEN ua.cod_construcao              IS NOT NULL THEN 1
            WHEN ud.cod_construcao_dependente   IS NOT NULL THEN 2
        END AS tipo
    INTO
        inTipo
    FROM
        imobiliario.imovel i
    LEFT JOIN
        imobiliario.unidade_autonoma ua ON  ua.inscricao_municipal  = i.inscricao_municipal AND
                                            ua.cod_construcao       = inCodConstrucao
    LEFT JOIN
        imobiliario.unidade_dependente ud  ON   ud.inscricao_municipal       = i.inscricao_municipal AND
                                                ud.cod_construcao_dependente = inCodConstrucao
    WHERE
        i.inscricao_municipal = inIM;

    IF inTipo = 1 THEN
        SELECT
            coalesce(aua.area,0)
        INTO
            nuResultado
        FROM
            imobiliario.unidade_autonoma ua
        INNER JOIN
            (
                SELECT aua.inscricao_municipal, aua.cod_tipo, aua.cod_construcao, aua."timestamp", aua.area
                FROM
                     imobiliario.area_unidade_autonoma aua,
                     ( SELECT max(area_unidade_autonoma."timestamp") AS "timestamp", area_unidade_autonoma.cod_construcao
                       FROM imobiliario.area_unidade_autonoma
                       WHERE area_unidade_autonoma.cod_construcao = inCodConstrucao and area_unidade_autonoma.inscricao_municipal = inIM
                       GROUP BY area_unidade_autonoma.cod_construcao
                     ) maua
                WHERE aua.cod_construcao = maua.cod_construcao AND aua."timestamp" = maua."timestamp"
            )             aua ON    aua.inscricao_municipal = ua.inscricao_municipal    AND
                                                        aua.cod_tipo            = ua.cod_tipo            AND
                                                        aua.cod_construcao      = ua.cod_construcao      AND
                                                        aua.cod_construcao      = inCodConstrucao
        WHERE
            ua.inscricao_municipal  = inIM AND
            ua.cod_construcao       = inCodConstrucao;
    ELSE
    SELECT
        coalesce(aud.area,0)
    INTO
        nuResultado
    FROM
    imobiliario.unidade_dependente ud
    INNER JOIN
        (
            SELECT aud.inscricao_municipal, aud.cod_construcao_dependente, aud.cod_tipo, aud.cod_construcao, aud."timestamp", aud.area
            FROM
                imobiliario.area_unidade_dependente aud,
                ( SELECT max(area_unidade_dependente."timestamp") AS "timestamp", area_unidade_dependente.cod_construcao_dependente
                  FROM imobiliario.area_unidade_dependente
                  WHERE area_unidade_dependente.cod_construcao_dependente = inCodConstrucao AND area_unidade_dependente.inscricao_municipal = inIM
                  GROUP BY area_unidade_dependente.cod_construcao_dependente
                ) maud
            WHERE aud.cod_construcao_dependente = maud.cod_construcao_dependente AND aud."timestamp" = maud."timestamp"
        )                        aud ON aud.inscricao_municipal         = ud.inscricao_municipal        AND
                                        aud.cod_construcao              = ud.cod_construcao             AND
                                        aud.cod_construcao_dependente   = ud.cod_construcao_dependente  AND
                                        aud.cod_tipo                    = ud.cod_tipo
    WHERE
        ud.inscricao_municipal       = inIM AND
        ud.cod_construcao_dependente = inCodConstrucao;
    END IF;

    nuResultado:= coalesce(cast(nuResultado as numeric(20,4)),0);
    boLog := arrecadacao.salva_log(''arrecadacao.fn_busca_area_construida'',nuResultado::varchar);

    return nuResultado;
END;
' LANGUAGE 'plpgsql';

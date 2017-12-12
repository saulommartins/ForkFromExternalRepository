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
* $Id: calculaAreaConstrucao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.9  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_calcula_area_construcao( INTEGER ) RETURNS numeric AS '
DECLARE
    inCodConstrucao      ALIAS FOR $1;
    nuAreaTotal          NUMERIC;
    reRegistro           RECORD;
    stSql                VARCHAR;

BEGIN
nuAreaTotal = 0.00;

    stSql := ''
            SELECT
                (coalesce (AUTONOMA.area_autonoma, 0) + coalesce(DEPENDENTE.area_dependente,0) ) as AREA_TOTAL,
                AUTONOMA.area_real
            FROM
            (
                SELECT
                    SUM( UA.area ) as area_autonoma,
                    MAX( AC.area_real ) as area_real
                FROM
                    (
                        SELECT
                            IMUA.*
                        FROM
                            imobiliario.area_unidade_autonoma AS IMUA,
                            (SELECT
                                MAX (TIMESTAMP) AS TIMESTAMP,
                                COD_CONSTRUCAO,
                                COD_TIPO,
                                INSCRICAO_MUNICIPAL
                            FROM
                                imobiliario.area_unidade_autonoma
                            GROUP BY
                                COD_CONSTRUCAO,
                                COD_TIPO,
                                INSCRICAO_MUNICIPAL
                            ) AS IMUAA
                        WHERE
                            IMUA.COD_CONSTRUCAO      = IMUAA.COD_CONSTRUCAO
                            AND IMUA.COD_TIPO            = IMUAA.COD_TIPO
                            AND IMUA.INSCRICAO_MUNICIPAL = IMUAA.INSCRICAO_MUNICIPAL
                            AND IMUA.COD_CONSTRUCAO      = '' || inCodConstrucao || ''
                            AND IMUA.TIMESTAMP           = IMUAA.TIMESTAMP
                    ) AS UA
                     
                    LEFT JOIN imobiliario.baixa_unidade_autonoma as bua
                    ON bua.cod_construcao = UA.cod_construcao AND bua.inscricao_municipal = UA.inscricao_municipal
                    
                    INNER JOIN imobiliario.construcao_edificacao CE
                    ON  UA.cod_construcao = CE.cod_construcao   AND UA.cod_tipo = CE.cod_tipo
                    
                    INNER JOIN imobiliario.construcao             C
                    ON C.cod_construcao =  CE.cod_construcao 
                    ,
                    (
                        SELECT
                            IMAC.*
                        FROM
                            imobiliario.area_construcao AS IMAC,
                            (SELECT
                                MAX (TIMESTAMP) AS TIMESTAMP,
                                COD_CONSTRUCAO
                            FROM
                                imobiliario.area_construcao
                            GROUP BY
                                COD_CONSTRUCAO
                            ) AS IMACC
                        WHERE
                            IMAC.COD_CONSTRUCAO = IMACC.COD_CONSTRUCAO
                            AND IMAC.TIMESTAMP  = IMACC.TIMESTAMP
                    ) AS AC
                WHERE
                    UA.COD_CONSTRUCAO = '' || inCodConstrucao || ''
                    AND C.cod_construcao  = AC.cod_construcao
                    and
                    case 
                        when bua.cod_construcao is not null then
                            case 
                                when ( bua.dt_inicio::date > now()::date OR bua.dt_termino::date < now()::date ) then
                                    true
                                else
                                    false
                            end
                       else
                            true
                    end     
            ) AS AUTONOMA,
            (
                SELECT
                    COALESCE( sum( UD.area ), 0 ) as area_dependente

                FROM
                    imobiliario.area_unidade_dependente UD
                    INNER JOIN
                    (
                        SELECT
                            max(timestamp) as timestamp,
                            cod_construcao_dependente,
                            cod_construcao
                        FROM
                            imobiliario.area_unidade_dependente
                        WHERE 
                            cod_construcao = '' || inCodConstrucao || ''
                        GROUP BY cod_construcao, cod_construcao_dependente
                    ) as tabela ON tabela.timestamp = UD.timestamp 
                      AND tabela.cod_construcao = UD.cod_construcao
                      AND tabela.cod_construcao_dependente = UD.cod_construcao_dependente
                      
                    LEFT JOIN imobiliario.baixa_unidade_dependente as bud
                    ON bud.cod_construcao_dependente = UD.cod_construcao_dependente
                      AND bud.cod_construcao = UD.cod_construcao
                      AND bud.inscricao_municipal = UD.inscricao_municipal

                    LEFT JOIN 
                        (
                            SELECT
                                BAL.*
                            FROM
                                imobiliario.baixa_construcao AS BAL,
                                (
                                SELECT
                                    MAX (TIMESTAMP) AS TIMESTAMP,
                                    cod_construcao
                                FROM
                                    imobiliario.baixa_construcao 
                                GROUP BY
                                    cod_construcao
                                ) AS BT
                            WHERE
                                BAL.cod_construcao = BT.cod_construcao AND
                                BAL.timestamp = BT.timestamp
                        ) bl
                    ON
                        (UD.cod_construcao = bl.cod_construcao OR UD.cod_construcao_dependente = bl.cod_construcao)
                WHERE
                    UD.COD_CONSTRUCAO      = '' || inCodConstrucao || ''
                    and
                    CASE WHEN bl.cod_construcao IS NOT NULL THEN
                        CASE WHEN bl.dt_termino IS NOT NULL THEN
                            true 
                        ELSE
                            false
                        END
                    ELSE
                        true
                    END
                    AND
                    case 
                        when bud.cod_construcao is not null then
                            case 
                                when ( bud.dt_inicio::date > now()::date OR bud.dt_termino::date < now()::date ) then
                                    true
                                else
                                    false
                            end
                       else
                            true
                    end     
            ) AS DEPENDENTE
    '';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        nuAreaTotal := nuAreaTotal + reRegistro.AREA_TOTAL;
    END LOOP;

    RETURN nuAreaTotal;
END;
'language 'plpgsql';

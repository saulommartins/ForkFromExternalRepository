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
* $Id: calculaAreaImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.15  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_calcula_area_imovel( INTEGER ) RETURNS numeric AS '
DECLARE
    stInscricaoMunicipal ALIAS FOR $1;
    nuAreaTotal         NUMERIC := 0;
    nuAreaAutonoma      NUMERIC := 0;
    nuAreaDependente    NUMERIC := 0;
BEGIN

    SELECT 
        Coalesce( areaa.area , 0)
    INTO 
        nuAreaAutonoma
    FROM 
        imobiliario.area_unidade_autonoma as areaa
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
        ) bl  ON areaa.cod_construcao = bl.cod_construcao
        
        INNER JOIN
        (
        SELECT 
            max (ua.timestamp) as timestamp,
            ua.cod_construcao
        FROM 
            imobiliario.area_unidade_autonoma ua
        WHERE 
            ua.inscricao_municipal =  stInscricaoMunicipal 
        GROUP BY 
            ua.cod_construcao
     ) as tabela ON tabela.timestamp = areaa.timestamp 
     AND areaa.cod_construcao = tabela.cod_construcao

    LEFT JOIN 
        (
            SELECT
                BAL.*
            FROM
                imobiliario.baixa_unidade_autonoma AS BAL,
                (
                SELECT
                    MAX (TIMESTAMP) AS TIMESTAMP,
                    cod_construcao,
                    inscricao_municipal
                FROM
                    imobiliario.baixa_unidade_autonoma
                GROUP BY
                    cod_construcao,
                    inscricao_municipal
                ) AS BT
            WHERE
                BAL.cod_construcao = BT.cod_construcao AND
                BAL.inscricao_municipal = BT.inscricao_municipal AND
                BAL.timestamp = BT.timestamp
        )as bua
    ON bua.cod_construcao = areaa.cod_construcao AND bua.inscricao_municipal = areaa.inscricao_municipal

     WHERE
        case 
            when bua.cod_construcao is not null then
                case 
                     when ( bua.dt_inicio::date > now()::date OR bua.dt_termino < now()::date ) then
                        true
                    else
                        false
                end
           else
                true
        end     
   ;

SELECT
    Coalesce( sum( aread.area ) , 0) as area
INTO
    nuAreaDependente
FROM 
    imobiliario.area_unidade_dependente as aread
    INNER JOIN
    (
        SELECT 
            max (ud.timestamp) as timestamp,
            ud.cod_construcao_dependente
        FROM 
            imobiliario.area_unidade_dependente ud
        WHERE 
            ud.inscricao_municipal = stInscricaoMunicipal
        GROUP BY 
            ud.cod_construcao_dependente
     ) as tabela ON tabela.timestamp = aread.timestamp 
     AND aread.cod_construcao_dependente = tabela.cod_construcao_dependente
     
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
        ( aread.cod_construcao = bl.cod_construcao OR aread.cod_construcao_dependente = bl.cod_construcao )
    
    LEFT JOIN 
        (
            SELECT
                BAL.*
            FROM
                imobiliario.baixa_unidade_dependente AS BAL,
                (
                SELECT
                    MAX (TIMESTAMP) AS TIMESTAMP,
                    cod_construcao,
                    cod_construcao_dependente,
                    inscricao_municipal
                FROM
                    imobiliario.baixa_unidade_dependente
                GROUP BY
                    cod_construcao,
                    cod_construcao_dependente,
                    inscricao_municipal
                ) AS BT
            WHERE
                BAL.cod_construcao = BT.cod_construcao AND
                BAL.cod_construcao_dependente = BT.cod_construcao_dependente AND
                BAL.inscricao_municipal = BT.inscricao_municipal AND
                BAL.timestamp = BT.timestamp
        )as bud
    ON bud.cod_construcao_dependente = aread.cod_construcao_dependente
    AND bud.cod_construcao = aread.cod_construcao
    AND bud.inscricao_municipal = aread.inscricao_municipal

    WHERE 
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
                    when ( bud.dt_inicio::date > now()::date OR bud.dt_termino < now()::date ) then
                        true
                    else
                        false
                end
           else
                true
        end     
   ;
   nuAreaTotal := Coalesce(nuAreaAutonoma,0) + Coalesce(nuAreaDependente,0) ;

    RETURN nuAreaTotal;
END;
'language 'plpgsql';

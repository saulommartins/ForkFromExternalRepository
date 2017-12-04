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
* $Id: calculaAreaUnidadeAutonoma.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.11
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_calcula_area_unidade_autonoma( INTEGER , INTEGER ) RETURNS numeric AS '
DECLARE
    inInscricaoMunicipal    ALIAS FOR $1;
    inCodConstrucao         ALIAS FOR $2; 
    nuAreaAutonoma        NUMERIC := 0;
BEGIN

        SELECT Coalesce( sum( aread.area ) , 0)         as area
          INTO nuAreaAutonoma
          FROM imobiliario.area_unidade_autonoma        as aread
    INNER JOIN (
                 SELECT max (ud.timestamp)              as timestamp
                      , ud.cod_construcao            
                   FROM imobiliario.area_unidade_autonoma   ud
                  WHERE ud.inscricao_municipal = inInscricaoMunicipal
                    AND ud.cod_construcao      = inCodConstrucao
               GROUP BY ud.cod_construcao           
               )                                        as tabela 
            ON tabela.timestamp                = aread.timestamp 
           AND aread.cod_construcao            = tabela.cod_construcao           
         
     LEFT JOIN (
                 SELECT BAL.*
                   FROM imobiliario.baixa_construcao    AS BAL
                      , (
                          SELECT MAX (TIMESTAMP)        AS TIMESTAMP
                               , cod_construcao
                            FROM imobiliario.baixa_construcao 
                        GROUP BY cod_construcao
                        )                               AS BT
                  WHERE BAL.cod_construcao     = BT.cod_construcao 
                    AND BAL.timestamp          = BT.timestamp
               )                                        AS bl
            ON aread.cod_construcao            = bl.cod_construcao 
            OR aread.cod_construcao            = bl.cod_construcao 
        
     LEFT JOIN imobiliario.baixa_unidade_autonoma       as bud
            ON bud.cod_tipo                    = aread.cod_tipo                 
           AND bud.cod_construcao              = aread.cod_construcao
           AND bud.inscricao_municipal         = aread.inscricao_municipal
          
         WHERE CASE 
                    WHEN bl.cod_construcao IS NOT NULL THEN
                        CASE 
                            WHEN bl.dt_termino IS NOT NULL THEN
                                true 
                            ELSE
                                false
                        END
                    ELSE
                        true
                    END
           AND case 
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

    RETURN nuAreaAutonoma;
END;
'language 'plpgsql';

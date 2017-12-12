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
* $Id: calculaAreaImovelConstrucao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.09
*               uc-05.01.12
*/

/*
$Log$
Revision 1.6  2007/04/13 17:56:21  dibueno
Raise's comentados

Revision 1.5  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_calcula_area_imovel_construcao( INTEGER ) RETURNS numeric AS '
DECLARE
    stInscricaoMunicipal ALIAS FOR $1;
    nuAreaConstrucao     NUMERIC;
    inCodConstrucao      INTEGER;
    reRegistro           RECORD;
    reRegistroArea       RECORD;
    stSql                VARCHAR;
    stSql2               VARCHAR;
BEGIN

    BEGIN
        stSql := ''
            SELECT
                max(IUA.timestamp),
                IUA.cod_construcao
            FROM
                imobiliario.unidade_autonoma IUA
            WHERE
                IUA.inscricao_municipal = '' || stInscricaoMunicipal || ''
            GROUP BY
                IUA.cod_construcao
        '';

        EXECUTE stSql;

    EXCEPTION
        WHEN plpgsql_error OR raise_exception THEN
    END;

    FOR reRegistro IN EXECUTE stSql LOOP
        inCodConstrucao := reRegistro.cod_construcao;
    END LOOP;

    IF inCodConstrucao IS NULL THEN
        nuAreaConstrucao := 0;
    ELSE

        stSql := ''
            SELECT
                IUA.*
            FROM
                imobiliario.unidade_autonoma IUA,
                (
                    SELECT
                        max(timestamp) as timestamp,
                        inscricao_municipal,
                        cod_construcao
                    FROM
                        imobiliario.unidade_autonoma
                    WHERE
                        cod_construcao = '' || inCodConstrucao || ''
                    GROUP BY
                        inscricao_municipal,
                        cod_construcao
                ) as IUAA
            WHERE
                IUAA.cod_construcao = IUA.cod_construcao AND
                IUAA.timestamp = IUA.timestamp
        '';

        nuAreaConstrucao := 0;

        FOR reRegistro IN EXECUTE stSql LOOP

            stSql := '' select imobiliario.fn_calcula_area_construcao( ''|| inCodConstrucao ||'' ) as area '';
            FOR reRegistroArea IN EXECUTE stSql LOOP
                nuAreaConstrucao := nuAreaConstrucao + reRegistroArea.area;
            END LOOP;

        END LOOP;

    END IF;

    RETURN nuAreaConstrucao;
END;
'language 'plpgsql';

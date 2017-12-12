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
* $Id: calculaAreaImovelLote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.09
*               uc-05.01.08
*/

/*
$Log$
Revision 1.9  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_calcula_area_imovel_lote( INTEGER ) RETURNS numeric AS '
DECLARE
    stInscricaoMunicipal ALIAS FOR $1;
    nuAreaLote           NUMERIC;
    inCodLote            INTEGER;
    reRegistro           RECORD;
BEGIN

   SELECT imovel_lote.cod_lote
     INTO inCodLote
     FROM imobiliario.imovel_lote
    WHERE imovel_lote.inscricao_municipal = stInscricaoMunicipal
    ORDER BY timestamp DESC LIMIT 1;

    nuAreaLote := 0;
    FOR reRegistro IN 
    SELECT 
        imobiliario.fn_calcula_area_imovel(imovel_lote.inscricao_municipal) as area_imovel
    FROM 
        imobiliario.imovel_lote
        INNER JOIN ( SELECT MAX(imovel_lote.timestamp) as timestamp
                , imovel_lote.inscricao_municipal
                , imovel_lote.cod_lote
                FROM imobiliario.imovel_lote
            WHERE imovel_lote.cod_lote = inCodLote
        
            GROUP BY imovel_lote.inscricao_municipal, imovel_lote.cod_lote
     ) as imovel_lote_max
    
    ON
        imovel_lote.cod_lote            = imovel_lote_max.cod_lote
        AND imovel_lote.timestamp           = imovel_lote_max.timestamp
        AND imovel_lote.inscricao_municipal = imovel_lote_max.inscricao_municipal
        AND imovel_lote.inscricao_municipal = imovel_lote_max.inscricao_municipal
    WHERE
        imovel_lote.cod_lote            = inCodLote
        AND imobiliario.fn_busca_situacao_imovel( imovel_lote.inscricao_municipal, now()::date ) = ''Ativo''

    LOOP
      nuAreaLote := nuAreaLote + COALESCE(reRegistro.area_imovel,0);
    END LOOP;

    RETURN nuAreaLote;
END;
'language 'plpgsql';


/*
-- Função antiga, antes da otimização.
CREATE OR REPLACE FUNCTION imobiliario.fn_calcula_area_imovel_lote( INTEGER ) RETURNS numeric AS '
DECLARE
    stInscricaoMunicipal ALIAS FOR $1;
    nuAreaLote           NUMERIC;
    inCodLote            INTEGER;
    reRegistro           RECORD;
    reRegistroArea       RECORD;
    stSql                VARCHAR;
    stSql2               VARCHAR;
BEGIN

    stSql := ''
        SELECT
            max(IL.timestamp),
            IL.cod_lote
        FROM
            imobiliario.imovel_lote IL
        WHERE
            IL.inscricao_municipal = '' || stInscricaoMunicipal || ''
        GROUP BY
            IL.cod_lote
        ORDER BY max DESC LIMIT 1
    '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        inCodLote := reRegistro.cod_lote;
    END LOOP;

    stSql := ''
        SELECT
            IL.inscricao_municipal
        FROM
            imobiliario.imovel_lote IL,
            (
                SELECT
                    max(IL.timestamp) as timestamp,
                    IL.inscricao_municipal,
                    IL.cod_lote
                FROM
                    imobiliario.imovel_lote IL
                WHERE
                    IL.cod_lote = '' || inCodLote || ''
                GROUP BY
                    IL.inscricao_municipal,
                    IL.cod_lote
            ) as IIL
        WHERE
            IL.cod_lote = IIL.cod_lote AND
            IL.timestamp = IIL.timestamp AND
            IL.inscricao_municipal = IIL.inscricao_municipal
    '';
    nuAreaLote := 0;
    FOR reRegistro IN EXECUTE stSql LOOP

        stSql2 := '' select imobiliario.fn_calcula_area_imovel( ''||reRegistro.inscricao_municipal||'' ) as area '';

        FOR reRegistroArea IN EXECUTE stSql2 LOOP
            nuAreaLote = coalesce(nuAreaLote,0);
            nuAreaLote := nuAreaLote + coalesce(reRegistroArea.area,0);

        END LOOP;
    END LOOP;

    RETURN nuAreaLote;
END;
'language 'plpgsql';
*/

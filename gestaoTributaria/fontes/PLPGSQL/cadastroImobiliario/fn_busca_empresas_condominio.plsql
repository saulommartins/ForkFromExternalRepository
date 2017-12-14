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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_busca_empresas_condominio.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.1  2007/03/19 20:20:38  dibueno
*** empty log message ***

Revision 1.1  2007/02/22 18:06:06  dibueno
Bug #8416#

Revision 1.1  2007/02/22 15:01:21  dibueno
Bug #8416#

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_empresas_condominio( INTEGER ) RETURNS VARCHAR AS '
DECLARE
    inCodCondominio ALIAS FOR $1;
    stRetorno       VARCHAR := '''';
    stSql           VARCHAR;
    boPrimeiro      BOOLEAN;

    reRegistro      RECORD;

BEGIN

    stSql := ''

SELECT
    res.inscricao_economica
    , res.timestamp
FROM
    (
        SELECT
            EDF.inscricao_economica
            , EDF.timestamp
        FROM
            (
                SELECT
                    IC.cod_condominio
                    , IIC.inscricao_municipal
                FROM
                    imobiliario.imovel_condominio as IIC
                    INNER JOIN imobiliario.condominio as IC
                    ON IC.cod_condominio = IIC.cod_condominio
            ) as imoveis

            INNER JOIN (
                    SELECT
                        inscricao_economica
                        , inscricao_municipal
                        , max(timestamp) as timestamp
                    FROM
                        economico.domicilio_fiscal
                    GROUP BY
                        inscricao_economica
                        , inscricao_municipal
            ) as EDF
            ON EDF.inscricao_municipal = imoveis.inscricao_municipal

            LEFT JOIN imobiliario.baixa_imovel as ibi
            ON ibi.inscricao_municipal = imoveis.inscricao_municipal
            AND ibi.dt_termino is null

        WHERE
            ibi.inscricao_municipal is null
            AND  imoveis.cod_condominio = ''|| inCodCondominio ||''

    ) as res

    LEFT JOIN economico.baixa_cadastro_economico as ebce
    ON ebce.inscricao_economica = res.inscricao_economica
    and ebce.dt_termino is null

WHERE
    ebce.inscricao_economica is null

    '';

    boPrimeiro := true;
    FOR reRegistro IN EXECUTE stSql LOOP
        IF boPrimeiro = true THEN
            stRetorno := reRegistro.inscricao_economica;
            boPrimeiro := false;
        ELSE
            stRetorno := stRetorno||'' ,''||reRegistro.inscricao_economica;
        END IF;
    END LOOP;

    RETURN stRetorno;

END;
' LANGUAGE 'plpgsql';

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
* $Id: fn_busca_empresas_logradouro.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.1  2007/03/19 20:20:43  dibueno
*** empty log message ***


*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_empresas_logradouro( INTEGER ) RETURNS VARCHAR AS '
DECLARE
    inCodLogradouro ALIAS FOR $1;
    stRetorno       VARCHAR := '''';
    stSql           VARCHAR;
    boPrimeiro      BOOLEAN := true;

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
            sw_logradouro as LO

            INNER JOIN imobiliario.confrontacao_trecho as CT
            ON CT.cod_logradouro = LO.cod_logradouro
            INNER JOIN imobiliario.imovel_confrontacao as IC
            ON IC.cod_lote = CT.cod_lote

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
            ON EDF.inscricao_municipal = IC.inscricao_municipal

            LEFT JOIN imobiliario.baixa_imovel as ibi
            ON ibi.inscricao_municipal = IC.inscricao_municipal
            AND ibi.dt_termino is null

        WHERE
            ibi.inscricao_municipal is null 
            AND LO.cod_logradouro = ''|| inCodLogradouro ||''

UNION
            select
                EDI.inscricao_economica
                , EDI.timestamp
            from
                economico.domicilio_informado as EDI
                INNER JOIN (
                    SELECT
                        inscricao_economica,
                        max(timestamp) as timestamp
                    FROM
                        economico.domicilio_informado
                    GROUP BY
                        inscricao_economica
                ) as EDI2
                ON EDI2.inscricao_economica = EDI.inscricao_economica
                
            where
                EDI.cod_logradouro = ''|| inCodLogradouro ||''
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

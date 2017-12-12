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
* $Id: consultaEdificacao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_consulta_edificacao( INTEGER ) RETURNS VARCHAR[] AS '

    DECLARE
        reRecord   RECORD;
        arRetorno  VARCHAR[] := array[0];
        stSql      VARCHAR;
        reRegistro RECORD;

        inInscricaoMunicipal ALIAS FOR $1;
    BEGIN
        stSql := ''
            SELECT
                ve.cod_construcao,
                ve.cod_construcao_autonoma,
                to_char(ve.data_construcao,''''dd/mm/yyyy''''    )    as data_construcao,
                to_char(ve.data_baixa,''''dd/mm/yyyy''''    )    as data_baixa,
                ve.cod_tipo,
                ve.nom_tipo,
                ve.area_real,
                ve.cod_processo,
                ve.exercicio,
                ve.nom_condominio,
                ve.area_unidade,
                ve.tipo_vinculo,
                ve.timestamp_construcao,
                ve.imovel_cond
            FROM
                imobiliario.vw_edificacao as ve
            WHERE
                ve.imovel_cond = ''||inInscricaoMunicipal||''
                '';


        FOR reRegistro IN EXECUTE stSql LOOP
            arRetorno[1]  := reRegistro.cod_construcao;
            IF reRegistro.cod_construcao_autonoma  IS NULL THEN
                arRetorno[2] := '''';
            ELSE
                arRetorno[2] := reRegistro.cod_construcao_autonoma;
            END IF;
            IF reRegistro.data_construcao IS NULL THEN
                arRetorno[3] :='''' ;
            ELSE
                arRetorno[3]  := reRegistro.data_construcao;
            END IF;
            IF reRegistro.data_baixa IS NULL THEN
                arRetorno[4] := '''';
            ELSE
                arRetorno[4]  := reRegistro.data_baixa;
            END IF;
            IF reRegistro.cod_tipo IS NULL THEN
                arRetorno[5] := '''';
            ELSE
                arRetorno[5]  := reRegistro.cod_tipo;
            END IF;
            IF reRegistro.nom_tipo IS NULL THEN
                arRetorno[6] := '''';
            ELSE
                arRetorno[6]  := reRegistro.nom_tipo;
            END IF;
            IF reRegistro.area_real IS NULL THEN
                arRetorno[7] := '''';
            ELSE
                arRetorno[7]  := reRegistro.area_real;
            END IF;
            IF reRegistro.cod_processo IS NULL THEN
                arRetorno[8] := '''';
            ELSE
                arRetorno[8]  := reRegistro.cod_processo;
            END IF;
            IF reRegistro.exercicio IS NULL THEN
                arRetorno[9] := '''';
            ELSE
                arRetorno[9]  := reRegistro.exercicio;
            END IF;
            IF reRegistro.nom_condominio IS NULL THEN
                arRetorno[10] := '''';
            ELSE
                arRetorno[10]  := reRegistro.nom_condominio;
            END IF;
            IF reRegistro.area_unidade IS NULL THEN
                arRetorno[11] := '''';
            ELSE
                arRetorno[11]  := reRegistro.area_unidade;
            END IF;
            IF reRegistro.tipo_vinculo IS NULL THEN
                arRetorno[12] := '''';
            ELSE
                arRetorno[12]  := reRegistro.tipo_vinculo;
            END IF;
            IF reRegistro.timestamp_construcao IS NULL THEN
                arRetorno[13] := '''';
            ELSE
                arRetorno[13]  := reRegistro.timestamp_construcao;
            END IF;
            IF reRegistro.imovel_cond IS NULL THEN
                arRetorno[14] := '''';
            ELSE
                arRetorno[14] := reRegistro.imovel_cond;
            END IF;

        END LOOP;

        RETURN arRetorno;
    END;

'language 'plpgsql';

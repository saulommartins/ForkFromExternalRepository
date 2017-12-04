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
* $Id: $
*
* Caso de uso: uc-05.04.00
*/

CREATE OR REPLACE FUNCTION lista_inscricao_por_documento(integer,integer,integer,char(4)) returns varchar as $$
DECLARE
    inNumDocumento          ALIAS FOR $1;
    inCodDocumento          ALIAS FOR $2;
    inCodTipoDocumento      ALIAS FOR $3;
    chExercicioEmissao      ALIAS FOR $4;
    stSqlFuncoes            VARCHAR;
    stExecuta               VARCHAR;
    stRetorno               VARCHAR := '';
    reRecordFuncoes         RECORD;

BEGIN
    stSqlFuncoes := '
        SELECT DISTINCT
            DIVIDA_PARCELAMENTO.cod_inscricao,
            DIVIDA_PARCELAMENTO.exercicio

        FROM
            DIVIDA.DIVIDA_PARCELAMENTO

        INNER JOIN
            (
                SELECT
                    tmp.*
                FROM
                    DIVIDA.EMISSAO_DOCUMENTO AS tmp
                INNER JOIN
                    (
                        SELECT
                            MAX(num_emissao) AS num_emissao,
                            cod_documento,
                            cod_tipo_documento,
                            num_documento
                        FROM
                            DIVIDA.EMISSAO_DOCUMENTO
                        GROUP BY
                            cod_documento,
                            cod_tipo_documento,
                            num_documento
                    )AS tmp2
                ON
                    tmp.cod_documento = tmp2.cod_documento
                    AND tmp.cod_tipo_documento = tmp2.cod_tipo_documento
                    AND tmp.num_documento = tmp2.num_documento
                    AND tmp.num_emissao = tmp2.num_emissao
            )AS EMISSAO_DOCUMENTO
        ON
            EMISSAO_DOCUMENTO.num_parcelamento = DIVIDA_PARCELAMENTO.num_parcelamento

        WHERE
            EMISSAO_DOCUMENTO.cod_documento = '||inCodDocumento||'
            AND EMISSAO_DOCUMENTO.cod_tipo_documento = '||inCodTipoDocumento||'
            AND EMISSAO_DOCUMENTO.num_documento = '||inNumDocumento||'
            AND EMISSAO_DOCUMENTO.exercicio = '|| quote_literal(chExercicioEmissao) ||'
    
    ';

    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
        stRetorno := stRetorno || reRecordFuncoes.cod_inscricao || '/' || reRecordFuncoes.exercicio || '<br>';
    END LOOP;

    return stRetorno;
end;
$$ language 'plpgsql';

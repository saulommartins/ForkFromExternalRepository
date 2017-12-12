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
* $Id: fn_area_lote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.5  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_area_lote(INTEGER)  RETURNS NUMERIC AS '
DECLARE
    inCodLote                   ALIAS FOR $1;
    stSql                       VARCHAR := '''';
    nuSomaEdificadasLote        NUMERIC(20,4) := 0.00;
    nuRetorno                   NUMERIC(20,4) := 0.00;
    reRecord                    RECORD;
    boLog                       BOOLEAN;
BEGIN
    -- IMOVEIS DO LOTE
    stSql :=''
            SELECT
                A.INSCRICAO_MUNICIPAL
            FROM
                IMOBILIARIO.IMOVEL_LOTE A,
                (
                SELECT
                    MAX (TIMESTAMP) AS TIMESTAMP,
                    INSCRICAO_MUNICIPAL
                FROM
                    IMOBILIARIO.IMOVEL_LOTE
                WHERE
                    COD_LOTE =''||inCodLote||''
                GROUP BY
                    INSCRICAO_MUNICIPAL
                ) AS MA
            WHERE
            A.COD_LOTE =  ''||inCodLote||'' AND
            A.INSCRICAO_MUNICIPAL = MA.INSCRICAO_MUNICIPAL AND
            A.TIMESTAMP = MA.TIMESTAMP
    '';
    -- soma  area dos imoveis do lote
    FOR reRecord IN EXECUTE stSql LOOP
        nuSomaEdificadasLote := coalesce(imobiliario.fn_calcula_area_imovel(reRecord.INSCRICAO_MUNICIPAL),0);
        nuRetorno := nuRetorno + nuSomaEdificadasLote;
    END LOOP;
    boLog := arrecadacao.salva_log(''arrecadacao.fn_area_lote'',nuRetorno::varchar);
    RETURN nuRetorno;
END;
' LANGUAGE 'plpgsql';

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
* $Id: fn_area_real.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.2  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_area_real(INTEGER)  RETURNS numeric(20,4) AS '
DECLARE
    inIM                        ALIAS FOR $1;
    inCodLote                   INTEGER;
    stSql                       VARCHAR   := '''';
    reRecord                    RECORD;
    nuResultado                 NUMERIC   := 0.00;
    boLog   BOOLEAN;
BEGIN
    inCodLote := imobiliario.fn_busca_lote_imovel(inIm);
    SELECT tbl_area.area INTO nuResultado
    FROM (
        SELECT
            coalesce(a.area_real,0) as area
        FROM
            imobiliario.area_lote a,
            imobiliario.lote b
        WHERE
            a.cod_lote = b.cod_lote  and
            a.cod_lote = inCodLote
        ORDER BY a.timestamp DESC
        LIMIT 1
        ) as tbl_area;
    RETURN nuResultado;
END;
' LANGUAGE 'plpgsql';

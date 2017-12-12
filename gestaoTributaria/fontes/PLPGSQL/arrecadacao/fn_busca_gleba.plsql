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
* $Id: fn_busca_gleba.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.11  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_gleba(INTEGER,INTEGER)  RETURNS NUMERIC(20,4) AS '
DECLARE
    inIM                    ALIAS FOR $1;
    inExercicio             ALIAS FOR $2;
    --inCodConstrucao         ALIAS FOR $3;
    inCodLote               INTEGER;
    nuAreaTerreno           NUMERIC := 0.0000;
    nuRetFunc               NUMERIC := 0.0000;
    stSql                   VARCHAR := '''';
    nuResultado             NUMERIC := 0.0000;
    boLog   BOOLEAN;
BEGIN

    inCodLote       := arrecadacao.fn_busca_lote_imovel(inIM);
    nuAreaTerreno   := arrecadacao.fn_area_lote(inCodLote);

    nuRetFunc := arrecadacao.fn_verifica_tbl1p2(10,inExercicio,cast(nuAreaTerreno as varchar));

    IF nuRetFunc = 1.00 OR nuRetFunc IS NULL THEN
        nuResultado := 1.0;
    ELSE
        nuResultado := nuRetFunc;
    END IF;

/* Retorna valor calculado */
    nuResultado := cast(nuResultado as numeric(20,4));
    boLog := arrecadacao.salva_log(''arrecadacao.fn_busca_gleba'',nuResultado::varchar);
    RETURN nuResultado;
END;
' LANGUAGE 'plpgsql';


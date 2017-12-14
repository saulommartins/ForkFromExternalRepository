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
* $Id: consultaCarneDevolucao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.11
* Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.consultaCarneDevolucao( varchar, integer ) RETURNS boolean AS '
DECLARE
    stNumeracao          ALIAS FOR $1;
    inCodConvenio        ALIAS FOR $2;
    stNumeracaoDevolucao varchar;
    stRetorno            boolean;

BEGIN

    SELECT
        numeracao
    INTO
        stNumeracaoDevolucao
    FROM
        arrecadacao.carne_devolucao
    WHERE
            numeracao    = stNumeracao
        and cod_convenio = inCodConvenio;

    IF stNumeracaoDevolucao IS NULL THEN
        stRetorno = FALSE;
    ELSE
        stRetorno = TRUE;
    END IF;

    RETURN stRetorno;

END;
' LANGUAGE 'plpgsql';

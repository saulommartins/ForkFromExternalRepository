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
* $Id: fn_busca_valor_venal_territorial_calculado.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2006/11/13 11:52:34  fabio
criada PL para buscar o valor venal territorial durante calculo de IPTU (Mata)

Revision 1.1  2006/10/18 10:28:03  cercato
*** empty log message ***

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_valor_venal_territorial_calculado( INTEGER ) returns numeric AS '
DECLARE
    inInscricaoMunicipal   ALIAS FOR $1;
    nuResultado     NUMERIC;
    stTemp          VARCHAR;
    boLog           BOOLEAN;
BEGIN

    SELECT
        iv.venal_territorial_calculado
    INTO
        nuResultado
    FROM
        arrecadacao.imovel_v_venal as iv
    WHERE
        inscricao_municipal = inInscricaoMunicipal
    ORDER BY timestamp DESC
    LIMIT 1;

    return coalesce(nuResultado,0.00);
END;
' LANGUAGE 'plpgsql';

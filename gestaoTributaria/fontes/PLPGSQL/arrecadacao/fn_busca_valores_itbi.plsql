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
* $Id: fn_busca_valores_itbi.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.8  2006/10/18 10:28:25  cercato
correcoes para a consulta do itbi.

Revision 1.7  2006/10/10 15:39:52  cercato
alterando consulta de acordo com modificacao na tabela.

Revision 1.6  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_valores_itbi( INTEGER, BOOLEAN ) returns numeric AS '
DECLARE
    inInscricaoMunicipal   ALIAS FOR $1;
    boInformado            ALIAS FOR $2;
    nuResultado     NUMERIC;
    stTemp          VARCHAR;
    boLog           BOOLEAN;
BEGIN

IF boInformado = true THEN
    SELECT
        coalesce(iv.venal_total_informado, 0.00)
    INTO
        nuResultado
    FROM
        arrecadacao.imovel_v_venal as iv
    WHERE
        inscricao_municipal = inInscricaoMunicipal
    ORDER BY timestamp DESC
    LIMIT 1;
ELSE
    SELECT
        coalesce(iv.venal_total_calculado, 0.00)
    INTO
        nuResultado
    FROM
        arrecadacao.imovel_v_venal as iv
    WHERE
        inscricao_municipal = inInscricaoMunicipal
    ORDER BY timestamp DESC
    LIMIT 1;
END IF;


    return coalesce(nuResultado,0.00);
END;
' LANGUAGE 'plpgsql';

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
* $Id: fn_ultimo_venal_por_im.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.4  2006/10/16 15:43:51  cercato
correcoes para funcionar com a tabela imovel_v_venal que foi modificada.

Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_ultimo_venal_por_im( INTEGER, CHAR(4) ) returns numeric AS '
DECLARE
    inInscricaoMunicipal   ALIAS FOR $1;
    stExercicio            ALIAS FOR $2;
    nuResultado     NUMERIC;
BEGIN
    
SELECT
    coalesce(iv.venal_total_informado, iv.venal_total_calculado, 0.00)
INTO
    nuResultado
FROM
    arrecadacao.imovel_v_venal as iv
WHERE
    inscricao_municipal = inInscricaoMunicipal
AND
    exercicio = stExercicio
ORDER BY timestamp DESC
LIMIT 1;

    return coalesce(nuResultado,0.00);
END;
' LANGUAGE 'plpgsql';

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
* $Id: fn_busca_lote_imovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.2  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_lote_imovel(INTEGER)  RETURNS INTEGER AS '
DECLARE
    inIM                        ALIAS FOR $1;
    arRetorno                  VARCHAR ;
    inResultado                 INTEGER := 0;
    boLog                       BOOLEAN;

BEGIN
    SELECT INTO inResultado (
                SELECT
                    coalesce(l.cod_lote,0)
                FROM
                    imobiliario.lote l
                INNER JOIN  (
                            SELECT il.inscricao_municipal, il.cod_lote, il."timestamp"
                            FROM imobiliario.imovel_lote il
                            WHERE il.inscricao_municipal = inIM
                            ORDER BY il.timestamp DESC
                            LIMIT 1
                             ) ilote     ON l.cod_lote                   = ilote.cod_lote
                INNER JOIN imobiliario.imovel i                     ON ilote.inscricao_municipal    = i.inscricao_municipal

                WHERE
                   i.inscricao_municipal = inIM     );

    RETURN inResultado;
END;
' LANGUAGE 'plpgsql';

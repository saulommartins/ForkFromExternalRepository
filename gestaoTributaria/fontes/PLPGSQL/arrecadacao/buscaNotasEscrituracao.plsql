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
* Valor de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaNotasEscrituracao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2007/04/13 17:55:41  dibueno
Raise's comentados

Revision 1.1  2007/04/02 14:16:39  cercato
*** empty log message ***

Revision 1.3  2007/02/21 19:49:02  dibueno
Bug #8416#

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaNotasEscrituracao( int, timestamp ) returns varchar as '
declare
    inInscricao     ALIAS FOR $1;
    stTimestamp     ALIAS FOR $2;
    stRetorno       varchar := '''';
    reRecord        RECORD;
    stSql           varchar := '''';
begin


    stSql := ''
        SELECT
            an.nro_nota
        FROM
            arrecadacao.nota_servico AS ans
        INNER JOIN
            arrecadacao.nota AS an
        ON
            an.cod_nota = ans.cod_nota 
        WHERE
            ans.timestamp = ''''''||stTimestamp||''''''
            AND ans.inscricao_economica = ''||inInscricao
    ;


    FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := stRetorno||reRecord.nro_nota||'', '';
    END LOOP;

    return substring(stRetorno from 1 for char_length(stRetorno)-2);
end;
'language 'plpgsql';

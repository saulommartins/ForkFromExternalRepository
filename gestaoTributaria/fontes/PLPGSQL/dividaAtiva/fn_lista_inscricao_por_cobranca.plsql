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
* $Id: fn_lista_inscricao_por_cobranca.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.00
*/

/*
$Log$
Revision 1.1  2007/08/16 19:57:00  cercato
*** empty log message ***

*/


CREATE OR REPLACE FUNCTION lista_inscricao_por_cobranca(integer, integer) returns varchar as '
declare
    inNumeroParcelamento    ALIAS FOR $1;
    inExercicio             ALIAS FOR $2;
    stSqlFuncoes            VARCHAR;
    stExecuta               VARCHAR;
    stRetorno               VARCHAR := '''';
    reRecordFuncoes         RECORD;

begin
    stSqlFuncoes := ''
        SELECT
            DIVIDA_PARCELAMENTO.cod_inscricao,
            DIVIDA_PARCELAMENTO.exercicio

        FROM
            DIVIDA.PARCELAMENTO

        INNER JOIN
            DIVIDA.DIVIDA_PARCELAMENTO
        ON
            DIVIDA_PARCELAMENTO.num_parcelamento = PARCELAMENTO.num_parcelamento

        WHERE
            PARCELAMENTO.numero_parcelamento = ''||inNumeroParcelamento||''
            AND PARCELAMENTO.exercicio = ''||inExercicio||''
    '';

    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
        stRetorno := stRetorno || reRecordFuncoes.cod_inscricao || ''/'' || reRecordFuncoes.exercicio || ''<br>'';
    END LOOP;

    return stRetorno;
end;
'language 'plpgsql';

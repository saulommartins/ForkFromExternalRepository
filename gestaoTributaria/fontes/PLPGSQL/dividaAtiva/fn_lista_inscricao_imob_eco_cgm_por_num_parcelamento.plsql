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
* $Id: fn_lista_inscricao_por_num_parcelamento.plsql 29207 2008-04-15 14:51:15Z fabio $
*
* Caso de uso: uc-05.04.00
*/

/*
$Log$
Revision 1.1  2007/08/16 19:57:00  cercato
*** empty log message ***

*/


CREATE OR REPLACE FUNCTION lista_inscricao_imob_eco_cgm_por_num_parcelamento(integer) returns varchar as '
declare
    inNumParcelamento    ALIAS FOR $1;
    stSqlFuncoes            VARCHAR;
    stExecuta               VARCHAR;
    stRetorno               VARCHAR := '''';
    reRecordFuncoes         RECORD;

begin
    stSqlFuncoes := ''
        SELECT DISTINCT
            COALESCE( divida_imovel.inscricao_municipal, divida_empresa.inscricao_economica, divida_cgm.numcgm ) AS inscricao

        FROM
            divida.divida_parcelamento

        INNER JOIN
            divida.divida_cgm
        ON
            divida_cgm.cod_inscricao = divida_parcelamento.cod_inscricao
            AND divida_cgm.exercicio = divida_parcelamento.exercicio

        LEFT JOIN 
            divida.divida_imovel
        ON 
            divida_imovel.cod_inscricao = divida_parcelamento.cod_inscricao
            AND divida_imovel.exercicio = divida_parcelamento.exercicio

        LEFT JOIN 
            divida.divida_empresa
        ON 
            divida_empresa.cod_inscricao = divida_parcelamento.cod_inscricao
            AND divida_empresa.exercicio = divida_parcelamento.exercicio

        WHERE
            DIVIDA_PARCELAMENTO.num_parcelamento = ''||inNumParcelamento||''
    '';

    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
        stRetorno := stRetorno || reRecordFuncoes.inscricao || ''<br>'';
    END LOOP;

    return stRetorno;
end;
'language 'plpgsql';

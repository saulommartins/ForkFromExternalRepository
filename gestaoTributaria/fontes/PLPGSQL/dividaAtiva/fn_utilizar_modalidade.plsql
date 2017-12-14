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
* $Id: fn_utilizar_modalidade.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.00
*/

/*
$Log$
Revision 1.1  2007/02/09 18:36:27  cercato
*** empty log message ***

*/


CREATE OR REPLACE FUNCTION utilizar_modalidade(integer,integer,date) returns boolean as '
declare
    inCodModalidade     ALIAS FOR $1;
    inRegistro          ALIAS FOR $2;
    dtDataBase          ALIAS FOR $3;
    stSqlFuncoes        VARCHAR;
    stExecuta           VARCHAR;
    reRecordFuncoes     RECORD;
    reRecordExecuta     RECORD;
    boRetorno           BOOLEAN;

begin
    stSqlFuncoes := ''                                       
        SELECT
             (
                SELECT
                    administracao.funcao.nom_funcao
                FROM
                    administracao.funcao
                WHERE
                    administracao.funcao.cod_funcao = divida.modalidade_vigencia.cod_funcao
                    AND administracao.funcao.cod_modulo = divida.modalidade_vigencia.cod_modulo
                    AND administracao.funcao.cod_biblioteca = divida.modalidade_vigencia.cod_biblioteca
              )AS funcao_valida
            , divida.modalidade_vigencia.vigencia_inicial
            , divida.modalidade_vigencia.vigencia_final
        FROM
            divida.modalidade
        
        INNER JOIN
            divida.modalidade_vigencia
        ON
            divida.modalidade_vigencia.timestamp = divida.modalidade.ultimo_timestamp
            AND divida.modalidade_vigencia.cod_modalidade = divida.modalidade.cod_modalidade

        WHERE
            divida.modalidade.cod_modalidade = ''||inCodModalidade||''
    '';

    boRetorno := false;

    -- executa
    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP

        IF ( ( dtDataBase >= reRecordFuncoes.vigencia_inicial ) AND ( dtDataBase <= reRecordFuncoes.vigencia_final ) ) THEN

            stExecuta :=  ''SELECT ''||reRecordFuncoes.funcao_valida||''( ''||inRegistro||'' ) as utilizar '';
            FOR reRecordExecuta IN EXECUTE stExecuta LOOP
                boRetorno := reRecordExecuta.utilizar;
            END LOOP;
        END IF;
    END LOOP;


   return boRetorno;
end;
'language 'plpgsql';

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
* $Id: fn_aplica_reducao_modalidade.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.00
*/

/*
$Log$
Revision 1.1  2007/02/09 18:36:27  cercato
*** empty log message ***

*/


CREATE OR REPLACE FUNCTION aplica_reducao_modalidade(integer,integer,numeric) returns numeric as '
declare
    inCodModalidade     ALIAS FOR $1;
    inRegistro          ALIAS FOR $2;
    nuValor             ALIAS FOR $3;
    stSqlFuncoes        VARCHAR;
    stExecuta           VARCHAR;
    reRecordFuncoes     RECORD;
    reRecordExecuta     RECORD;
    nuRetorno           NUMERIC;
    boUtilizar          BOOLEAN;

begin
    stSqlFuncoes := ''                                       
        SELECT
             (
                SELECT
                    administracao.funcao.nom_funcao
                FROM
                    administracao.funcao
                WHERE
                    administracao.funcao.cod_funcao = divida.modalidade_reducao.cod_funcao
                    AND administracao.funcao.cod_modulo = divida.modalidade_reducao.cod_modulo
                    AND administracao.funcao.cod_biblioteca = divida.modalidade_reducao.cod_biblioteca
              )AS funcao_valida
            , divida.modalidade_reducao.valor
            , divida.modalidade_reducao.percentual
        FROM
            divida.modalidade
        
        INNER JOIN
            divida.modalidade_reducao
        ON
            divida.modalidade_reducao.timestamp = divida.modalidade.ultimo_timestamp
            AND divida.modalidade_reducao.cod_modalidade = divida.modalidade.cod_modalidade

        WHERE
            divida.modalidade.cod_modalidade = ''||inCodModalidade||''
    '';

    nuRetorno := 0;

    -- executa
    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
        stExecuta :=  ''SELECT ''||reRecordFuncoes.funcao_valida||''( ''||inRegistro||'' ) as utilizar '';
        FOR reRecordExecuta IN EXECUTE stExecuta LOOP
            boUtilizar := reRecordExecuta.utilizar;
        END LOOP;

        IF ( boUtilizar ) THEN
            IF ( reRecordFuncoes.percentual ) THEN
                nuRetorno := nuValor - ((nuValor * reRecordFuncoes.valor) / 100);
            ELSE
                nuRetorno := nuValor - reRecordFuncoes.valor;
            END IF;
        END IF;
    END LOOP;


   return nuRetorno::numeric(14,2);
end;
'language 'plpgsql';

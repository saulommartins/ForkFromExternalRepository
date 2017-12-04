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
* $Id: fn_aplica_reducao_modalidade_acrescimo.plsql 63888 2015-10-30 15:35:08Z evandro $
*
* Caso de uso: uc-05.04.00
*/

/*
$Log$
Revision 1.3  2007/07/18 20:09:36  cercato
correcao na reducao da cobranca.

Revision 1.2  2007/06/22 19:06:04  cercato
adicionado terceiro parametro "numero de parcelas".

Revision 1.1  2007/04/24 15:25:59  cercato
*** empty log message ***

*/


CREATE OR REPLACE FUNCTION aplica_reducao_modalidade_acrescimo(integer,integer,numeric,integer,integer,date,integer) returns numeric as $$
declare
    inCodModalidade     ALIAS FOR $1;
    inRegistro          ALIAS FOR $2;
    nuValor             ALIAS FOR $3;
    inCodAcrescimo      ALIAS FOR $4;
    inCodTipo           ALIAS FOR $5;
    dtDataVencimento    ALIAS FOR $6;
    inQtdParcelas       ALIAS FOR $7;
    stSqlFuncoes        VARCHAR;
    stExecuta           VARCHAR;
    reRecordFuncoes     RECORD;
    reRecordExecuta     RECORD;
    nuRetorno           NUMERIC;
    boUtilizar          BOOLEAN;

begin
    stSqlFuncoes := '                                       
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

        LEFT JOIN
            divida.modalidade_reducao_acrescimo
        ON
            divida.modalidade_reducao_acrescimo.timestamp = modalidade_reducao.timestamp
            AND divida.modalidade_reducao_acrescimo.cod_modalidade = modalidade_reducao.cod_modalidade
            AND divida.modalidade_reducao_acrescimo.cod_modulo = modalidade_reducao.cod_modulo
            AND divida.modalidade_reducao_acrescimo.cod_funcao = modalidade_reducao.cod_funcao
            AND divida.modalidade_reducao_acrescimo.cod_biblioteca = modalidade_reducao.cod_biblioteca
            AND divida.modalidade_reducao_acrescimo.valor = modalidade_reducao.valor
            AND divida.modalidade_reducao_acrescimo.percentual = modalidade_reducao.percentual


        WHERE
            divida.modalidade.cod_modalidade = '||inCodModalidade||'
            AND divida.modalidade_reducao_acrescimo.cod_acrescimo = '||inCodAcrescimo||'
            AND divida.modalidade_reducao_acrescimo.cod_tipo = '||inCodTipo||'
    ';

    nuRetorno := 0;

    -- executa
    FOR reRecordFuncoes IN EXECUTE stSqlFuncoes LOOP
        stExecuta :=  'SELECT '||reRecordFuncoes.funcao_valida||'( '||inRegistro||', '''||dtDataVencimento||''', '||inQtdParcelas||' ) as utilizar ';
        FOR reRecordExecuta IN EXECUTE stExecuta LOOP
            boUtilizar := reRecordExecuta.utilizar;
        END LOOP;

        IF ( boUtilizar ) THEN
            IF ( reRecordFuncoes.percentual ) THEN
                nuRetorno := (nuValor * reRecordFuncoes.valor) / 100;
            ELSE
                nuRetorno := reRecordFuncoes.valor;
            END IF;
        END IF;
    END LOOP;


   return nuRetorno::numeric(14,2);
end;
$$ language 'plpgsql';

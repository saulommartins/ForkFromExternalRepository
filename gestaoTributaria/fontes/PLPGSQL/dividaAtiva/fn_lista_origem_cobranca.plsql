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
* Lista as origens dos lançamentos vinculados à cobrança
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_lista_origem_cobranca.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2007/10/03 20:37:42  vitor
Ticket#10301#

Revision 1.1  2007/09/06 15:51:51  fabio
dibueno safado nao comitou!

Revision 1.2  2007/08/16 14:16:58  dibueno
*** empty log message ***


*/

--CREATE OR REPLACE FUNCTION divida.fn_lista_origem_cobranca ( integer, integer, integer) RETURNS SETOF RECORD AS '
CREATE OR REPLACE FUNCTION divida.fn_lista_origem_cobranca ( integer, integer ) RETURNS VARCHAR AS '
declare
    inCodCobranca    ALIAS FOR $1;
    inExercicio      ALIAS FOR $2;
    reRecord        record;
    stRetorno       VARCHAR := '''';
    stSqlPrincipal  VARCHAR := '''';
    boPrimeiro      BOOLEAN := true;
begin

    stSqlPrincipal = ''
        SELECT
            arrecadacao.fn_busca_origem_lancamento ( ap.cod_lancamento, carne.exercicio, 1, 1 ) as origem
        FROM
            divida.parcela_origem as dpo
            INNER JOIN divida.parcelamento as dp
            ON dp.num_parcelamento = dpo.num_parcelamento
            INNER JOIN arrecadacao.parcela as ap
            ON ap.cod_parcela = dpo.cod_parcela
            INNER JOIN arrecadacao.carne
            ON carne.cod_parcela = ap.cod_parcela
        WHERE
            dpo.num_parcelamento = ''|| inCodCobranca ||''
            AND dp.exercicio = ''''|| inExercicio ||''''
        GROUP BY
            ap.cod_lancamento
            , carne.exercicio
        ORDER BY
            ap.cod_lancamento
    '';

    

    FOR reRecord IN EXECUTE stSqlPrincipal LOOP
        IF ( boPrimeiro ) THEN
            stRetorno := reRecord.origem;
        ELSE
            stRetorno := stRetorno||''<br>''||''D.A. - ''||reRecord.origem ;
        END IF;
        boPrimeiro := false;
    END LOOP;

    RETURN ''D.A. - ''||stRetorno;

end;

'language 'plpgsql';

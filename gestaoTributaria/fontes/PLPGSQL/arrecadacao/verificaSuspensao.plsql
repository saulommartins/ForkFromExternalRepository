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
* $Id: verificaSuspensao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.08
*/

/*
$Log$
Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.verificaSuspensao(INTEGER) RETURNS SETOF RECORD AS '

DECLARE
    inIM              ALIAS FOR $1;
    stSql             VARCHAR;
    reRegistro        RECORD;

BEGIN
    stSql := ''
        SELECT
            L.valor,
            S.cod_suspensao,
            S.cod_lancamento
        FROM
            arrecadacao.lancamento L,
            arrecadacao.suspensao S 
            LEFT JOIN arrecadacao.suspensao_termino ST ON
                ST.cod_suspensao = S.cod_suspensao
        WHERE
            ST.cod_suspensao IS NULL AND
            L.cod_lancamento = S.cod_lancamento AND
            L.cod_lancamento IN
            (
                SELECT 
                    distinct(cod_lancamento) 
                FROM
                    arrecadacao.lancamento_calculo 
                WHERE 
                    cod_calculo in 
                    (
                        select cod_calculo 
                        from arrecadacao.imovel_calculo 
                        where inscricao_municipal = ''||inIM||''
                    )
            ) 
    '';

    FOR reRegistro IN EXECUTE stSql LOOP
        return NEXT reRegistro;
    END LOOP;
    return;
END;

' LANGUAGE 'plpgsql';

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
* $Id: fn_verifica_tbl1p2.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.10  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_verifica_tbl1p2(INTEGER,INTEGER,VARCHAR)  RETURNS NUMERIC AS $$
DECLARE
    inCodTabela             ALIAS FOR $1;
    inExercicio             ALIAS FOR $2;
    stValor1                ALIAS FOR $3;
    arRetorno               VARCHAR;
    nuResultado             NUMERIC:=0.00;
    nuTemp                  NUMERIC:=0.00;
    boLog                   BOOLEAN;
BEGIN
    nuTemp:= to_number(stValor1, '999999');
        SELECT
            to_number(valor,9999999999999999.9999)
        INTO
            nuResultado
        FROM
            arrecadacao.tabela_conversao_valores b
        WHERE
             b.cod_tabela = inCodTabela      AND
             b.exercicio  = inExercicio      AND
             nuTemp BETWEEN to_number( b.parametro_1,'999999' ) AND to_number( b.parametro_2,'999999');

        IF NOT FOUND THEN
            boLog := arrecadacao.salva_log('Busca Tabela de Conversao','Erro:'||nuResultado::varchar);
        ELSE
            boLog := arrecadacao.salva_log('Busca Tabela de Conversao',nuResultado::varchar);
        END IF;

    RETURN nuResultado;
END;
$$ LANGUAGE plpgsql;

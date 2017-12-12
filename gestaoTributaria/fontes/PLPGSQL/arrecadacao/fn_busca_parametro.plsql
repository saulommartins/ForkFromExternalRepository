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
* $Id: fn_busca_parametro.plsql 59612 2014-09-02 12:00:51Z gelson $
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

CREATE OR REPLACE FUNCTION arrecadacao.fn_verifica_tbl4(INTEGER,INTEGER,VARCHAR,VARCHAR)  RETURNS NUMERIC(20,4) AS '
DECLARE
    inCodTabela             ALIAS FOR $1;
    inExercicio             ALIAS FOR $2;
    stConfPrinc             ALIAS FOR $3;
    stTestPadrao            ALIAS FOR $4;
    reRegistro              RECORD;
    stSql                   VARCHAR := '''';
    nuResultado             NUMERIC:=0;
    boLog   BOOLEAN;
BEGIN

    SELECT
        b.valor
    INTO
        nuResultado
    FROM
        arrecadacao.tabela_conversao a,
        arrecadacao.tabela_conversao_valores b
    WHERE
        a.cod_tabela = b.cod_tabela  AND
        a.exercicio  = b.exercicio   AND
        a.cod_tabela = inCodTabela   AND
        a.exercicio  = inExercicio   AND
        stConfPrinc  >=  parametro_1 AND
        stConfPrinc  <=  parametro_2 AND
        stTestPadrao =   parametro_3 ;



    IF nuResultado = 0 OR nuResultado IS NULL THEN
        nuResultado := -1;
    END IF;
    boLog := arrecadacao.salva_log(''Busca na tabela de conversao'',nuResultado::varchar);
    RETURN nuResultado;
END;
' LANGUAGE 'plpgsql';

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
* $Id: fn_busca_tabela_conversao.plsql 66548 2016-09-21 13:05:07Z evandro $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/


/*
$Log$
Revision 1.10  2006/11/13 11:42:28  fabio
ajustes para a nova estrutura de calculo

Revision 1.9  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_tabela_conversao(INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR)  RETURNS NUMERIC(20,4) AS $$
DECLARE
    inCodTabela             ALIAS FOR $1;
    inExercicio             ALIAS FOR $2;
    stParam1                ALIAS FOR $3;
    stParam2                ALIAS FOR $4;
    stParam3                ALIAS FOR $5;
    stParam4                ALIAS FOR $6;
    reRegistro              RECORD;
    stSql                   VARCHAR ;
    nuResultado             NUMERIC;
    stResultado             VARCHAR;
    boLog   BOOLEAN;
BEGIN

    stSql := '
        SELECT b.valor AS valor
          FROM arrecadacao.tabela_conversao_valores b
         WHERE b.cod_tabela    = '||inCodTabela||'
           AND b.exercicio     = '''||inExercicio||'''
            ';

            IF stParam1 IS NOT NULL AND stParam1 != '' THEN
                stSql := stSql||' AND formata_string(b.parametro_1)   = formata_string('''|| stParam1||''') ';
            END IF;
            IF stParam2 IS NOT NULL AND stParam2 != '' THEN
                stSql := stSql||' AND formata_string(b.parametro_2)   = formata_string('''||stParam2||''')' ;
            END IF;
            IF stParam3 IS NOT NULL AND stParam3 != '' THEN
                stSql := stSql||' AND formata_string(b.parametro_3)   = formata_string('''||stParam3||''')' ;
            END IF;
            IF stParam4 IS NOT NULL AND stParam4 != '' THEN
                stSql := stSql||' AND formata_string(b.parametro_4)   = formata_string('''||stParam4||''')' ;
            END IF;


    FOR reRegistro  IN EXECUTE stSql LOOP
        stResultado := reRegistro.valor;
    END LOOP;
	IF stResultado IS NOT NULL THEN
	   nuResultado := to_number(stResultado, '9999.9999');
        END IF;
    RETURN nuResultado;
END;
$$ LANGUAGE 'plpgsql';

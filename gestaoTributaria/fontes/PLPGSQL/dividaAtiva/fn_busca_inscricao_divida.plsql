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
* $Id: fn_busca_inscricao_divida.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.1  2007/10/10 20:45:27  cercato
*** empty log message ***

*/


CREATE OR REPLACE FUNCTION divida.fn_busca_inscricao_divida(integer) RETURNS INTEGER AS '
DECLARE
    inExercicio             ALIAS FOR $1;
	stSql					varchar;
    stSql2                  varchar;
    stSql3                  varchar;
	reRecord                 RECORD;
    reRecord2                RECORD;
	nuRetorno		        integer;

BEGIN

stSql := ''
    SELECT
		acd.valor
	FROM
		administracao.configuracao as acd
	WHERE
		acd.cod_modulo = 33
		and parametro = ''''numeracao_inscricao''''
	ORDER BY acd.exercicio DESC
	LIMIT 1;
'';

stSql2 := ''
    SELECT
        max(cod_inscricao) AS valor

    FROM
        divida.divida_ativa;
'';

stSql3 := ''
    SELECT
        MAX(cod_inscricao) AS valor

    FROM
        divida.divida_ativa
    WHERE
        exercicio = ''|| quote_literal (inExercicio)||''
'';
FOR reRecord IN EXECUTE stSql LOOP
    IF ( reRecord.valor = ''exercicio'' ) THEN
        FOR reRecord2 IN EXECUTE stSql3 LOOP
	        nuRetorno := reRecord2.valor;
        END LOOP;
    ELSE
        FOR reRecord2 IN EXECUTE stSql2 LOOP
            nuRetorno := reRecord2.valor;
        END LOOP;
    END IF;
END LOOP;

if ( nuRetorno IS NULL ) then
    nuRetorno := 0;
end if;

return nuRetorno;

END;
' LANGUAGE 'plpgsql';

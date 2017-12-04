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
* $Id: fn_recupera_parcelas_divida_lancamento.plsql 61284 2014-12-30 10:58:38Z evandro $
*
* Caso de uso: uc-05.04.02
*/

/*
$Log:
*/


CREATE OR REPLACE FUNCTION divida.fn_recupera_parcelas_divida_lancamento( INTEGER )
RETURNS SETOF RECORD AS $$

DECLARE
    
    inCodLancamento ALIAS FOR $1;
    
    cod_parcela_unica 	integer;
	retorno 			varchar := '';
    reRecord            RECORD;
    stSql               VARCHAR;
    
BEGIN

/* Buscamos parcela unica, se só existir ela */

SELECT
	ap.cod_parcela
INTO
	cod_parcela_unica
FROM
	arrecadacao.parcela as ap
	INNER JOIN arrecadacao.carne
	ON carne.cod_parcela = ap.cod_parcela
WHERE
	cod_lancamento = inCodLancamento
	and ap.cod_lancamento NOT IN
	(
		SELECT	ap.cod_lancamento
		FROM 	arrecadacao.parcela as ap
		WHERE	ap.cod_lancamento = inCodLancamento
				and ap.nr_parcela > 0
	);
 
/* SE O LANCAMENTO SÓ CONTER ESSA PARCELA ÚNICA, UTILIZA O VALOR DELA */
	stSql := ' SELECT 
                         numeracao
                        , cod_convenio
                        , exercicio
                        , cod_parcela
                        , cod_calculo
                        , cod_lancamento
                        , nr_parcela
                        , cod_credito
                        , descricao_credito
                        , cod_natureza
                        , cod_genero
                        , cod_especie
                        , valor
                        , valor_exato
                FROM tmp_todas_parcelas
                WHERE cod_lancamento = '||inCodLancamento||' ';

IF  cod_parcela_unica is not null THEN
    stSql := stSql || 'AND nr_parcela = 0';
ELSE
    stSql := stSql || 'AND nr_parcela > 0';
END IF;

    FOR reRecord IN EXECUTE stSql LOOP
		return next reRecord;
    END LOOP;
	return;

END;
$$ LANGUAGE 'plpgsql';

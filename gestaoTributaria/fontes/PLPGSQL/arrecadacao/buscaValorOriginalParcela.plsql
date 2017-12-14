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
* Recupera valor de uma parcela
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaValorOriginalParcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.3  2006/11/17 12:13:35  dibueno
Bug #7471#

Revision 1.2  2006/11/02 18:56:01  dibueno
*** empty log message ***

Revision 1.1  2006/10/25 18:16:22  dibueno
*** empty log message ***

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaValorOriginalParcela( varchar ) returns numeric as '

DECLARE
    inNumeracao     ALIAS FOR $1;
    nuValor         numeric;

BEGIN
    -- Verifica se a parcela possui desconto
nuValor := null;

    SELECT
        apd.valor
    INTO
        nuValor
    FROM
        arrecadacao.parcela_desconto as apd
		INNER JOIN arrecadacao.carne as ac
		ON ac.cod_parcela = apd.cod_parcela
    WHERE
		apd.cod_parcela is not null and
        ac.numeracao = inNumeracao;


    IF ( nuValor IS NULL ) THEN
        SELECT
            case when apr.valor is not null then
				apr.valor
			else
				ap.valor
			end
        INTO
            nuValor
        FROM
            arrecadacao.parcela as ap
			INNER JOIN arrecadacao.carne as ac
			ON ac.cod_parcela = ap.cod_parcela
			LEFT JOIN                                                                                
			(                                                                                        
				select apr.cod_parcela, valor
				from arrecadacao.parcela_reemissao apr
				inner join (
					select cod_parcela, min(timestamp) as timestamp
					from arrecadacao.parcela_reemissao
					group by cod_parcela
					) as apr2
					ON apr2.cod_parcela = apr.cod_parcela AND
					apr2.timestamp = apr.timestamp
				) as apr
			ON apr.cod_parcela = ap.cod_parcela
        WHERE
            ac.numeracao = inNumeracao;
    END IF;

    return nuValor;
end;
'language 'plpgsql';

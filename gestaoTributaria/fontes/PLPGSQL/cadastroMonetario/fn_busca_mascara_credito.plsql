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
* $Id: fn_busca_mascara_credito.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.05.10
*/

/*
$Log$
Revision 1.3  2007/01/31 11:00:22  dibueno
Melhoria: adição da descrição do crédito no retorno

Revision 1.2  2007/01/23 17:17:47  dibueno
Bug #7926#

*/

CREATE OR REPLACE FUNCTION monetario.fn_busca_mascara_credito( INTEGER, INTEGER, INTEGER, INTEGER ) RETURNS varchar AS '
DECLARE

    inCodCredito    ALIAS FOR $1;
    inCodEspecie    ALIAS FOR $2;
    inCodGenero     ALIAS FOR $3;
    inCodNatureza   ALIAS FOR $4;
    stRetorno       VARCHAR;
	reRecord        record;
	stSql 			VARCHAR;
    
BEGIN

stSql = ''
    SELECT
        c.descricao_credito,
        lpad ( c.cod_credito::varchar, max_credito.valor, ''''0'''' ) as cod_credito,
        lpad ( c.cod_especie::varchar, max_especie.valor, ''''0'''' ) as cod_especie,
        lpad ( c.cod_genero::varchar,  max_genero.valor, ''''0'''' ) as cod_genero,
        lpad ( c.cod_natureza::varchar, max_natureza.valor, ''''0'''' ) as cod_natureza,

        ( lpad ( c.cod_credito::varchar, max_credito.valor, ''''0'''' )||''''.''''|| lpad( c.cod_especie::varchar, max_especie.valor, ''''0'''' )||''''.''''|| lpad (c.cod_genero::varchar, max_genero.valor, ''''0'''' )||''''.''''|| lpad( c.cod_natureza::varchar, max_natureza.valor, ''''0'''' )) as codigo_composto

    FROM
        monetario.credito as c,
        ( select length(max(cod_credito)::varchar) as valor from monetario.credito ) as max_credito,
        ( select length(max(cod_genero)::varchar) as valor from monetario.genero_credito ) as max_genero,
        ( select length(max(cod_especie)::varchar) as valor from monetario.especie_credito ) as max_especie,
        ( select length(max(cod_natureza)::varchar) as valor from monetario.natureza_credito ) as max_natureza
    WHERE
        c.cod_credito = ''|| inCodCredito ||''
        AND c.cod_especie   = ''|| inCodEspecie ||''
        AND c.cod_genero    = ''|| inCodGenero ||''
        AND c.cod_natureza  = ''|| inCodNatureza ||''
    GROUP BY
        c.cod_credito, c.descricao_credito, c.cod_especie, c.cod_genero, c.cod_natureza,
        max_credito.valor, max_especie.valor, max_genero.valor, max_natureza.valor
    '';

	FOR reRecord IN EXECUTE stSql LOOP
        stRetorno := reRecord.codigo_composto;
        stRetorno := stRetorno||''§''||reRecord.cod_credito;
        stRetorno := stRetorno||''§''||reRecord.cod_especie;
        stRetorno := stRetorno||''§''||reRecord.cod_genero;
        stRetorno := stRetorno||''§''||reRecord.cod_natureza;
        stRetorno := stRetorno||''§''||reRecord.descricao_credito;
    END LOOP;

    RETURN stRetorno;
END;
' LANGUAGE 'plpgsql';

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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.8  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION pegaempenholiquidacaomodalidade(varchar,integer,integer) RETURNS VARCHAR  AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    inCodNota               ALIAS FOR $2;
    inCodEntidade           ALIAS FOR $3;

    crCursor            REFCURSOR;

    stSql               VARCHAR :='';
    stSqlGroupBy        VARCHAR :='';
    stRetorno           VARCHAR :='';

    stValorPadrao       VARCHAR :='';
    stValorPadraoDesc   VARCHAR :='';
    stValorDesc         VARCHAR :='';
    stValor             VARCHAR :='';
    inCodTipo           INTEGER := 0;
    arValorPadraoDesc   VARCHAR[] := ARRAY[0];

    inCodModulo         INTEGER := 10;
    inCodCadastro       INTEGER := 2;
    inCodAtributo       INTEGER := 101;

BEGIN

stSqlGroupBy := ' ,VALOR.cod_entidade, VALOR.cod_nota, VALOR.exercicio  ';

stSql := '
    SELECT
--        CASE AD.cod_tipo WHEN 4 THEN
--            administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor)
--        ELSE
--            administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,'''')
--        END AS valor_padrao,
--        CASE AD.cod_tipo
--            WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''))
--            WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
--            ELSE null
--        END AS valor_padrao_desc,
--        CASE AD.cod_tipo WHEN 4 THEN
--            administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor)
--        ELSE
--            null
--        END AS valor_desc,
        PADRAO.valor_padrao,
        VALOR.valor,
        AD.cod_tipo
    FROM
        administracao.atributo_dinamico  AS AD,
        empenho.atributo_liquidacao_valor   AS VALOR,
        administracao.atributo_valor_padrao AS PADRAO
    WHERE
            AD.cod_modulo   = '|| inCodModulo   ||'
        AND AD.cod_atributo = '|| inCodAtributo ||'
        AND AD.cod_cadastro= '|| inCodCadastro ||'
        AND AD.ativo       = true

        AND AD.cod_cadastro = PADRAO.cod_cadastro
        AND AD.cod_atributo = PADRAO.cod_atributo
        AND AD.cod_modulo = PADRAO.cod_modulo

        AND VALOR.cod_nota        = '|| inCodNota ||'
        AND VALOR.cod_entidade    = '|| inCodEntidade ||'
        AND VALOR.exercicio       = '|| quote_literal(stExercicio) ||'

        AND AD.cod_atributo    = VALOR.cod_atributo
        AND AD.cod_cadastro    = VALOR.cod_cadastro
        AND PADRAO.cod_valor   = VALOR.valor::integer
        AND VALOR.cod_atributo||''-''||VALOR.timestamp =  (
            SELECT
                VALOR.cod_atributo||''-''||max(VALOR.timestamp)
            FROM
                administracao.atributo_dinamico             AS AD,
                empenho.atributo_liquidacao_valor   AS VALOR
            WHERE
                    AD.cod_modulo   = '|| inCodModulo   ||'
                AND AD.cod_atributo = '|| inCodAtributo ||'
                AND AD.cod_cadastro= '|| inCodCadastro ||'
                AND AD.ativo       = true

                AND VALOR.cod_nota        = '|| inCodNota ||'
                AND VALOR.cod_entidade    = '|| inCodEntidade ||'
                AND VALOR.exercicio       = '|| quote_literal(stExercicio) ||'

                AND AD.cod_atributo    = VALOR.cod_atributo
                AND AD.cod_cadastro    = VALOR.cod_cadastro
            GROUP BY
                VALOR.cod_cadastro, VALOR.cod_atributo '|| stSqlGroupBy ||'
            )
';

OPEN  crCursor FOR EXECUTE stSql;
--FETCH crCursor INTO stValorPadrao ,stValorPadraoDesc ,stValorDesc ,stValor ,inCodTipo;
FETCH crCursor INTO stValorPadrao, stValor, inCodTipo;
CLOSE crCursor;

IF inCodTipo = 3 or inCodTipo = 4 THEN
    If stValor Is Not Null And stValor != '' Then
--      arValorPadraoDesc := string_to_array( stValorPadraoDesc, '[][][]' );
--      stRetorno         := arValorPadraoDesc[ to_number(stValor,'99999999999') ];
      stRetorno := stValorPadrao;
    Else
      stRetorno := '';
    End If;
ELSE
    stRetorno := stValor;
END IF;

IF stRetorno IS NULL THEN
    stRetorno := '';
END IF;

    RETURN stRetorno;
END;
$$ language 'plpgsql';

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
* $Id: recuperaarrecadacaodesoneracaoabono.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION recuperaarrecadacaodesoneracaoabono(integer, integer) RETURNS character varying AS
                $_$
                DECLARE stSql VARCHAR;
                crCursor  REFCURSOR;
                rsRetorno RECORD;
                inCodDesoneracao ALIAS FOR $1;
                inNumcgm ALIAS FOR $2;

                BEGIN
                stSql := '  SELECT
                 AD.cod_cadastro,
                 AD.cod_atributo,
                 AD.ativo,
                 AD.nao_nulo,
                 AD.nom_atributo,
                 CASE TA.cod_tipo
                     WHEN 4 THEN  administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
                     ELSE         administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,'''')
                 END AS valor_padrao,
                 CASE TA.cod_tipo
                   WHEN 3 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,''''))
                   WHEN 4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,administracao.valor_padrao(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro,VALOR.valor))
                     ELSE         null
                 END AS valor_padrao_desc,
                 CASE TA.cod_tipo WHEN
                     4 THEN  administracao.valor_padrao_desc(AD.cod_atributo,AD.cod_modulo,AD.cod_cadastro ,VALOR.valor)
                     ELSE         null
                 END AS valor_desc,
                 AD.ajuda,
                 AD.mascara,
                 TA.cod_tipo,
                 TA.nom_tipo,
                 VALOR.valor,
                 VALOR.timestamp
              FROM
                 administracao.atributo_dinamico          AS AD,
                 administracao.tipo_atributo              AS TA,
                 arrecadacao.atributo_desoneracao AS ACA
                 LEFT JOIN
                 arrecadacao.atributo_desoneracao_valor         AS VALOR
              ON ( ACA.cod_atributo = VALOR.cod_atributo
                      AND ACA.cod_cadastro = VALOR.cod_cadastro
                          AND VALOR.cod_desoneracao = '||inCodDesoneracao||'
             AND VALOR.numcgm = '||inNumcgm||'
             AND ACA.cod_atributo = 1
                      AND VALOR.timestamp||VALOR.cod_atributo IN (
                         SELECT
                            max(VALOR.timestamp) 
                         FROM
                            arrecadacao.atributo_desoneracao AS ACA,
                            arrecadacao.atributo_desoneracao_valor         AS VALOR,
                            administracao.atributo_dinamico          AS AD,
                            administracao.tipo_atributo              AS TA
                         WHERE
                            ACA.cod_atributo = AD.cod_atributo
                            AND ACA.cod_cadastro = AD.cod_cadastro
                            AND ACA.cod_modulo   = AD.cod_modulo
                         AND ACA.cod_atributo = VALOR.cod_atributo
                         AND ACA.cod_cadastro = VALOR.cod_cadastro
                         AND ACA.cod_modulo   = VALOR.cod_modulo
                          AND VALOR.cod_desoneracao = '||inCodDesoneracao||'
             AND VALOR.numcgm = '||inNumcgm||'
             AND ACA.cod_atributo = 1
                         AND AD.cod_tipo = TA.cod_tipo
                         AND ACA.ativo = true
                         AND AD.cod_modulo   =25
                         AND AD.cod_cadastro=3

                         GROUP BY VALOR.cod_cadastro, VALOR.cod_atributo
                                              )

                 )
              WHERE
                  AD.cod_tipo = TA.cod_tipo
              AND ACA.ativo = true
              AND     AD.ativo
              AND AD.cod_atributo =  ACA.cod_atributo
              AND AD.cod_modulo   = ACA.cod_modulo
              AND AD.cod_cadastro = ACA.cod_cadastro
              AND ACA.cod_cadastro=3
              AND ACA.cod_modulo  =25


             AND ACA.cod_atributo = 1;';OPEN crCursor FOR EXECUTE stSql;
                                                FETCH crCursor INTO rsRetorno;
                                                CLOSE crCursor;
                                                RETURN rsRetorno.valor;
                                                END;
                                                $_$
                LANGUAGE plpgsql;

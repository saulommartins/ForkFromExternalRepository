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
* $Id: fn_tipo_edificacao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-535
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.6  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE  FUNCTION arrecadacao.fn_tipo_edificacao(integer, integer) RETURNS integer  AS '
DECLARE
    inim                        ALIAS FOR $1;
    incodconstrucao             ALIAS FOR $2;
    stTipo                      VARCHAR := '''';
    stSql                       VARCHAR := '''';
    inCodTipo                   INTEGER;
    stRetorno                   VARCHAR := ''Autonoma'';
    boLog                       BOOLEAN;
BEGIN
    SELECT
        CASE
            WHEN ua.cod_construcao              IS NOT NULL THEN
                (SELECT ce.cod_tipo FROM imobiliario.construcao_edificacao ce, imobiliario.unidade_autonoma ua
                WHERE ce.cod_construcao = inCodConstrucao AND ua.cod_construcao = ce.cod_construcao and ua.inscricao_municipal= inIM)
            WHEN ud.cod_construcao_dependente   IS NOT NULL THEN
                (SELECT ce.cod_tipo FROM imobiliario.construcao_edificacao ce, imobiliario.unidade_dependente ud
                WHERE ce.cod_construcao = inCodConstrucao AND  ud.cod_construcao_dependente = ce.cod_construcao and ud.inscricao_municipal = inIM)
        END AS tipo
    INTO
        inCodTipo
    FROM
        imobiliario.imovel i
    LEFT JOIN
        imobiliario.unidade_autonoma ua ON  ua.inscricao_municipal  = i.inscricao_municipal AND
                                            ua.cod_construcao       = inCodConstrucao
    LEFT JOIN
        imobiliario.unidade_dependente ud  ON   ud.inscricao_municipal       = i.inscricao_municipal AND
                                                ud.cod_construcao_dependente = inCodConstrucao
    WHERE
        i.inscricao_municipal = inIM
    ;
--     boLog := arrecadacao.salva_log(''arrecadacao.fn_tipo_edificacao'',inCodTipo::varchar);
    RETURN inCodTipo;

END;
'
    LANGUAGE plpgsql;

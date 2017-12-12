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
* $Id: fn_tipo_construcao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-535
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.11  2007/04/13 17:55:41  dibueno
Raise's comentados

Revision 1.10  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE  FUNCTION arrecadacao.fn_tipo_construcao(integer, integer) RETURNS VARCHAR  AS '
DECLARE
    inim                        ALIAS FOR $1;
    incodconstrucao             ALIAS FOR $2;
    stTipo                      VARCHAR := '''';
    stSql                       VARCHAR := '''';
    inCodTipo                   INTEGER;
    stValorParametro2           VARCHAR;
    stValorParametro3           VARCHAR;
    stRetorno                   VARCHAR := ''Autonoma'';
    boLog                       BOOLEAN;
BEGIN
/* BUSCAR se é unid dep ou aut*/
    SELECT
        CASE
            WHEN ua.cod_construcao              IS NOT NULL THEN ''Autonoma''::varchar
            WHEN ud.cod_construcao_dependente   IS NOT NULL THEN ''Dependente''::varchar
        END AS tipo
    INTO
        stTipo
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
    -- pega codigo do tipo da edificação
    IF stTipo = ''Dependente'' THEN
        SELECT ce.cod_tipo INTO inCodTipo FROM imobiliario.construcao_edificacao ce, imobiliario.unidade_dependente ud
        WHERE ce.cod_construcao = inCodConstrucao AND  ce.cod_construcao = ud.cod_construcao_dependente;
    ELSIF stTipo = ''Autonoma'' THEN
        SELECT ce.cod_tipo INTO inCodTipo FROM imobiliario.construcao_edificacao ce, imobiliario.unidade_autonoma ua
        WHERE ce.cod_construcao = ua.cod_construcao;
    END IF;
    -- pega parametro_2 , ref ao atributo 75
    SELECT av.valor INTO stValorParametro2 FROM imobiliario.construcao_edificacao ce, imobiliario.atributo_tipo_edificacao_valor av
    WHERE   ce.cod_construcao = inCodConstrucao     AND
            av.cod_construcao = ce.cod_construcao   AND
            av.cod_atributo   = 74                  AND
            av.cod_cadastro   = 5                   AND
            av.cod_tipo       = ce.cod_tipo
    ORDER BY av."timestamp" DESC LIMIT 1;
    -- pega parametro_3, ref ao atributo
    SELECT av.valor INTO stValorParametro3 FROM imobiliario.construcao_edificacao ce, imobiliario.atributo_tipo_edificacao_valor av
    WHERE   ce.cod_construcao = inCodConstrucao     AND
            av.cod_construcao = ce.cod_construcao   AND
            av.cod_atributo   = 75                  AND
            av.cod_cadastro   = 5                   AND
            av.cod_tipo       = ce.cod_tipo
    ORDER BY av."timestamp" DESC LIMIT 1;                                                                                                  

    IF stValorParametro1 IS NULL THEN
        stValorParametro3 = ''0'';
    END IF;
    IF stValorParametro2 IS NULL THEN
        stValorParametro2 = ''0'';
    END IF;
    stRetorno := inCodTipo||'',''||stValorParametro2||'',''||stValorParametro3;
    boLog := arrecadacao.salva_log(''arrecadacao.fn_tipo_construcao'',stRetorno);
    RETURN stRetorno;

END;
'
    LANGUAGE plpgsql;

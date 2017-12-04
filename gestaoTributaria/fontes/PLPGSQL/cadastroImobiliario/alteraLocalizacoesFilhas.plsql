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
* $Id: alteraLocalizacoesFilhas.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.altera_filhas(integer,integer) RETURNS varchar  AS '
DECLARE
    inCodLocalizacao    ALIAS FOR $1    ;
    inCodVigencia       ALIAS FOR $2    ;
    inCodNivel          integer     ;
    stValor             varchar     ;
    stSql               varchar     ;
    stSql2              varchar     ;
    stSql3              varchar     ;
    stAntigoReduzido    varchar     ;
    reRegistro          RECORD      ;
BEGIN
-- get nivel
SELECT INTO inCodNivel(
    select  max(cod_nivel)
    from    imobiliario.localizacao_nivel
    where   cod_localizacao= inCodLocalizacao  and cod_vigencia= inCodVigencia and valor !=''0'');
-- get reduzido
SELECT INTO stAntigoReduzido( select publico.fn_mascarareduzida( (select codigo_composto from imobiliario.localizacao where cod_localizacao = inCodLocalizacao) ) );
SELECT INTO stValor(
        select  valor
        from    imobiliario.localizacao_nivel
        where   cod_localizacao = inCodLocalizacao  and
                cod_vigencia    = inCodVigencia     and
                cod_nivel       = inCodNivel);
stSql2 := '''';
-- pega tamanho do novo reduzido
stSql := ''
        SELECT *
        FROM imobiliario.localizacao
        WHERE
        codigo_composto like ''''''||stAntigoReduzido||''.%'''''';

-- varre resultado da consulta modificando os registros encontrados
FOR reRegistro IN EXECUTE stSql
LOOP
    stSql2 := stSql2 || ''
        UPDATE
            imobiliario.localizacao_nivel
        SET
            valor  =  ''||quote_literal(stValor)||''
        WHERE
            cod_localizacao = ''||reRegistro.cod_localizacao || ''  AND
            cod_vigencia    = ''|| inCodVigencia||''                AND
            cod_nivel       = ''|| inCodNivel||'';
        '';
END LOOP;
-- adiciona ao sql, update em imobilario localizacao para trocar codigo composto
stSql3 :=''
        UPDATE  imobiliario.localizacao
        SET     codigo_composto = codigo_composto
        WHERE   codigo_composto LIKE ''''''||stAntigoReduzido||''.%''''
        '';

EXECUTE stSql2;
    RETURN stSql3;

END;
' LANGUAGE 'plpgsql';

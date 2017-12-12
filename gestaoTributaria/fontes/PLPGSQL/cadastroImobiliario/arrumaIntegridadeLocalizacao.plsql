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
* $Id: arrumaIntegridadeLocalizacao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.arrumaIntegridadeLocalizacao(integer,integer,varchar) RETURNS varchar  AS '
DECLARE
    inCodNivel          ALIAS FOR $1;
    inCodVigencia       ALIAS FOR $2;
    stMascara           ALIAS FOR $3;
    stSql               varchar     ;
    stSql2              varchar     ;
    stSql3              varchar     ;
    reRegistro          RECORD      ;
BEGIN
stSql2 := '''';
stSql3 := '''';
-- pega tamanho do novo reduzido
stSql := ''
        SELECT cod_localizacao
        FROM imobiliario.localizacao
        '';

-- varre resultado da consulta modificando os registros encontrados
FOR reRegistro IN EXECUTE stSql
LOOP
        stSql2 := stSql2 || ''
        INSERT INTO
            imobiliario.localizacao_nivel
        VALUES (''||inCodNivel||'',''|| inCodVigencia||'',''|| reRegistro.cod_localizacao || '',''''0'''')
        ;'';
END LOOP;
-- adiciona ao sql, update em imobilario localizacao para trocar codigo composto
stSql3 :=''
        UPDATE  imobiliario.localizacao
        SET     codigo_composto = codigo_composto
        '';

EXECUTE stSql2;
    RETURN ''true'';

END;
' LANGUAGE 'plpgsql';

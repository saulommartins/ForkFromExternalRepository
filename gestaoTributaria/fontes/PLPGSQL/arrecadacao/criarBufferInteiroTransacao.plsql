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
* $Id: $
*
* Casos d uso: uc-05.03.05 
*/

CREATE OR REPLACE FUNCTION criarBufferInteiroTransacao(VARCHAR, INTEGER ) RETURNS INTEGER AS 
$$
DECLARE
    stNome                     ALIAS FOR $1;
    inValor                    ALIAS FOR $2;
    stSql                      VARCHAR := '';
    stTabela                   VARCHAR;
BEGIN
    
    stSql := 'tmp_'||lower(stNome);

    SELECT tablename
        INTO stTabela
    FROM pg_tables
    WHERE tablename = stSql;

    IF stTabela IS NULL THEN
        stSql := '
            CREATE TEMPORARY TABLE tmp_'|| lower(stNome) ||' ( 
                valor integer
            )
        ';
        EXECUTE stSql;
    ELSE
        stSql := 'DELETE FROM tmp_'||lower(stNome);

        EXECUTE stSql;
    END IF;

    stSql := '
        INSERT INTO tmp_'|| lower(stNome) ||' VALUES ('|| inValor ||')
    ';
    EXECUTE stSql;

    RETURN inValor;
END;
$$
LANGUAGE 'plpgsql';

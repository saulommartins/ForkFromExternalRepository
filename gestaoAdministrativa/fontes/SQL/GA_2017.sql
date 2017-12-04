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
* Script de DDL e DML
*
* Versao 2.01.7
*
* Fabio Bertoldi - 20130626
*
*/

-----------------------------------------------
-- REESTABELECENDO PERMISOES PARA GRUPO siamweb
-----------------------------------------------

GRANT ALL ON organograma.vw_orgao_nivel TO GROUP siamweb;

CREATE OR REPLACE FUNCTION gera_grants() RETURNS VOID AS $$
DECLARE
    stSql   VARCHAR;
BEGIN
    stSql := 'GRANT ALL ON SCHEMA publico,' ||
              array_to_string( ARRAY(
                                      SELECT DISTINCT table_schema::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 )
                                    ), ',' )
              || ' TO siamweb;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_schema::text || '.' || table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 )
                                    ), ',' )
              || ' TO siamweb;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema = 'public'
                                    ), ',' )
              || ' TO siamweb;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                        SELECT pg_namespace.nspname || '.' || pg_class.relname
                                          FROM pg_class
                                             , pg_attribute
                                             , pg_namespace
                                         WHERE pg_class.relkind ='S'
                                           AND pg_namespace.oid = pg_class.relnamespace
                                      GROUP BY pg_namespace.nspname
                                             , pg_class.relname
                                    ), ',' )
              || ' TO siamweb;';
    EXECUTE stSql;

END;
$$ LANGUAGE 'plpgsql';

SELECT        gera_grants();
DROP FUNCTION gera_grants();


CREATE OR REPLACE FUNCTION grants_siamweb() RETURNS VOID AS $$
DECLARE
    stSql   VARCHAR;
BEGIN
    stSql := 'GRANT siamweb TO "' ||
              array_to_string( ARRAY(
                                      SELECT 'sw.' || usuario.username
                                        FROM administracao.usuario
                                    ), '","' )
              || '";';
    EXECUTE stSql;
END;
$$ LANGUAGE 'plpgsql';

SELECT        grants_siamweb();
DROP FUNCTION grants_siamweb();


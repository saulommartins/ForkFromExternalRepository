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
* Versao 2.01.0
*
* Fabio Bertoldi - 20120817
*
*/

------------------------------------------------
-- MIGRACAO DO GRUPO DE USUARIOS PARA ROLE urbem
------------------------------------------------

--CREATE ROLE urbem LOGIN SUPERUSER PASSWORD 'UrB3m';

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL   VARCHAR;
BEGIN

    stSql := 'GRANT urbem TO ' ||
              array_to_string( ARRAY(
                                      SELECT '"' || pg_user.usename::text
                                        FROM pg_user
                                       WHERE usename ilike 'sw.%'
                                    ), '", ' )
              || '";';
    RAISE NOTICE 'sql: %',  stSql;


    stSql := 'REVOKE siamweb FROM ' ||
              array_to_string( ARRAY(
                                      SELECT '"' || pg_user.usename::text
                                        FROM pg_user
                                       WHERE usename ilike 'sw.%'
                                    ), '", ' )
              || '";';
    RAISE NOTICE 'sql: %',  stSql;


-- GRANT tabelas

    stSql := 'GRANT ALL ON SCHEMA publico,' ||
              array_to_string( ARRAY(
                                      SELECT DISTINCT table_schema::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 , 'migra_bairro'
                                                                 , 'migra_condominio'
                                                                 , 'migra_edificacao'
                                                                 , 'migra_localizacao'
                                                                 , 'migra_lote'
                                                                 , 'migra_loteamento_lotes'
                                                                 , 'migra_proprietario'
                                                                 , 'migra_trecho'
                                                                 , 'migra_cgm'
                                                                 , 'migra_confrontacao_lote'
                                                                 , 'migra_imovel'
                                                                 , 'migra_logradouro'
                                                                 , 'migra_loteamento'
                                                                 , 'migra_nivel'
                                                                 , 'migra_rh'
                                                                 )
                                    ), ',' )
              || ' TO urbem;';
    RAISE NOTICE 'sql: %',  stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_schema::text || '.' || table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 , 'migra_bairro'
                                                                 , 'migra_condominio'
                                                                 , 'migra_edificacao'
                                                                 , 'migra_localizacao'
                                                                 , 'migra_lote'
                                                                 , 'migra_loteamento_lotes'
                                                                 , 'migra_proprietario'
                                                                 , 'migra_trecho'
                                                                 , 'migra_cgm'
                                                                 , 'migra_confrontacao_lote'
                                                                 , 'migra_imovel'
                                                                 , 'migra_logradouro'
                                                                 , 'migra_loteamento'
                                                                 , 'migra_nivel'
                                                                 , 'migra_rh'
                                                                 )
                                    ), ',' )
              || ' TO urbem;';
    RAISE NOTICE 'sql: %',  stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema = 'public'
                                    ), ',' )
              || ' TO urbem;';
    RAISE NOTICE 'sql: %',  stSql;


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
              || ' TO urbem;';
    RAISE NOTICE 'sql: %',  stSql;


-- REVOKE tabelas

    stSql := 'REVOKE ALL ON SCHEMA publico,' ||
              array_to_string( ARRAY(
                                      SELECT DISTINCT table_schema::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 , 'migra_bairro'
                                                                 , 'migra_condominio'
                                                                 , 'migra_edificacao'
                                                                 , 'migra_localizacao'
                                                                 , 'migra_lote'
                                                                 , 'migra_loteamento_lotes'
                                                                 , 'migra_proprietario'
                                                                 , 'migra_trecho'
                                                                 , 'migra_cgm'
                                                                 , 'migra_confrontacao_lote'
                                                                 , 'migra_imovel'
                                                                 , 'migra_logradouro'
                                                                 , 'migra_loteamento'
                                                                 , 'migra_nivel'
                                                                 , 'migra_rh'
                                                                 )
                                    ), ',' )
              || ' FROM siamweb;';
    RAISE NOTICE 'sql: %',  stSql;


    stSql := 'REVOKE ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_schema::text || '.' || table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 , 'migra_bairro'
                                                                 , 'migra_condominio'
                                                                 , 'migra_edificacao'
                                                                 , 'migra_localizacao'
                                                                 , 'migra_lote'
                                                                 , 'migra_loteamento_lotes'
                                                                 , 'migra_proprietario'
                                                                 , 'migra_trecho'
                                                                 , 'migra_cgm'
                                                                 , 'migra_confrontacao_lote'
                                                                 , 'migra_imovel'
                                                                 , 'migra_logradouro'
                                                                 , 'migra_loteamento'
                                                                 , 'migra_nivel'
                                                                 , 'migra_rh'
                                                                 )
                                    ), ',' )
              || ' FROM siamweb;';
    RAISE NOTICE 'sql: %',  stSql;


    stSql := 'REVOKE ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema = 'public'
                                    ), ',' )
              || ' FROM siamweb;';
    RAISE NOTICE 'sql: %',  stSql;


    stSql := 'REVOKE ALL ON ' ||
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
              || ' FROM siamweb;';
    RAISE NOTICE 'sql: %',  stSql;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

--DROP ROLE "sw.siamweb";


------------------------
-- INATIVANDO MODULO LRF
------------------------

UPDATE administracao.modulo  SET ativo = FALSE WHERE cod_modulo = 24;


------------------------------------------
-- INATIVANDO ACOES DE CONFIGURACAO TCM-GO
------------------------------------------

UPDATE administracao.acao  SET ativo = FALSE WHERE cod_acao = 1763;
UPDATE administracao.acao  SET ativo = FALSE WHERE cod_acao = 1764;
UPDATE administracao.acao  SET ativo = FALSE WHERE cod_acao = 1765;
UPDATE administracao.acao  SET ativo = FALSE WHERE cod_acao = 1766;
UPDATE administracao.acao  SET ativo = FALSE WHERE cod_acao = 1767;
UPDATE administracao.acao  SET ativo = FALSE WHERE cod_acao = 1768;
UPDATE administracao.acao  SET ativo = FALSE WHERE cod_acao = 1769;


----------------------------------------
-- ALTERANDO ORDEM DO MENU - Nota Avulsa
----------------------------------------

UPDATE administracao.acao SET ordem = 11 where cod_acao = 2241;


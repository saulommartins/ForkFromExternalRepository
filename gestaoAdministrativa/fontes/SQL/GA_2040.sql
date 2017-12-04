/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - SoluÃ§Ãµes em GestÃ£o PÃºblica                                *
    * @copyright (c) 2013 ConfederaÃ§Ã£o Nacional de MunicÃ­pos                         *
    * @author ConfederaÃ§Ã£o Nacional de MunicÃ­pios                                    *
    *                                                                                *
    * O URBEM CNM Ã© um software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo sob *
    * os  termos  da LicenÃ§a PÃºblica Geral GNU conforme  publicada  pela FundaÃ§Ã£o do *
    * Software Livre (FSF - Free Software Foundation); na versÃ£o 2 da LicenÃ§a.       *
    *                                                                                *
    * Este  programa  Ã©  distribuÃ­do  na  expectativa  de  que  seja  Ãºtil,   porÃ©m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implÃ­cita  de  COMERCIABILIDADE  OU *
    * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU "LICENCA.txt" *
    * com  este  programa; se nÃ£o, escreva para  a  Free  Software Foundation  Inc., *
    * no endereÃ§o 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* Script de DDL e DML
*
* Versao 2.04.0
*
* Fabio Bertoldi - 20150415
*
*/

----------------
-- Ticket #22926
----------------

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
     VALUES
     ( 5
     , '2015'
     , 'centro_custo'
     , 'false'
     );

ALTER TABLE sw_processo ADD COLUMN cod_centro INTEGER;
ALTER TABLE sw_processo ADD CONSTRAINT fk_processo_5 FOREIGN KEY                          (cod_centro)
                                                     REFERENCES almoxarifado.centro_custo (cod_centro);


-------------------------------------
-- GRANTs EM TODAS AS TABELAS - urbem
-------------------------------------


GRANT ALL ON organograma.vw_orgao_nivel TO urbem;

CREATE OR REPLACE FUNCTION gera_grants() RETURNS VOID AS $$
DECLARE
    stSql   VARCHAR;
BEGIN
    -- GRANTS
    stSql := 'GRANT ALL ON SCHEMA publico,' ||
              array_to_string( ARRAY(
                                      SELECT DISTINCT schema_name::text
                                        FROM information_schema.schemata
                                       WHERE schema_name NOT IN ( 'information_schema'
                                                                , 'pg_catalog'
                                                                , 'bethadba'
                                                                , 'bethadba2'
                                                                , 'bethadba3'
                                                                )
                                    ), ',' )
              || ' TO urbem;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON FUNCTION ' ||
              array_to_string( ARRAY(
                                      SELECT busca.schema || '.' || busca.nome || '(' || busca.args || ')'
                                        FROM (
                                               SELECT pg_namespace.nspname                            AS schema
                                                    , pg_proc.proname::text                           AS nome
                                                    , pg_catalog.oidvectortypes(pg_proc.proargtypes)  AS args
                                                 FROM pg_catalog.pg_proc
                                                 JOIN pg_catalog.pg_namespace
                                                   ON pg_namespace.oid = pg_proc.pronamespace
                                                WHERE pg_proc.oid > 200000
                                                  AND proisagg = FALSE
                                             ) AS busca
                                    ), ',' )
              || ' TO urbem;';
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
              || ' TO urbem;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema = 'public'
                                    ), ',' )
              || ' TO urbem;';
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
              || ' TO urbem;';
    EXECUTE stSql;

END;
$$ LANGUAGE 'plpgsql';

SELECT        gera_grants();
DROP FUNCTION gera_grants();


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
* $Id: reGeradorCalculos.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-01.03.95
*/

-- Gris - 31/03/2005 - Author Gris
   -- Função responsável por excluir do banco todas as funções externas do módulo gerador
   -- e recria-las utilizando o código fonte da tabela funcao_externa.

CREATE OR REPLACE FUNCTION criar_regera() RETURNS BOOLEAN AS $$
DECLARE

  varAchouArr VARCHAR;

BEGIN


    SELECT proname
      INTO varAchouArr
      from pg_proc
     where proname ilike 'reGeradorCalculos';

    IF NOT FOUND THEN

                 CREATE OR REPLACE FUNCTION administracao.reGeradorCalculos() RETURNS BOOLEAN AS '
                 DECLARE
                     reFuncoes          RECORD;
                     stFuncoes          VARCHAR;
                 BEGIN
                      --
                      -- Ira excluir todas as funços do gerador no banco.
                      --
                      FOR reFuncoes IN  SELECT '' Drop Function ''                                   ||
                                               Btrim(pg_proc.proname)                                ||
                                               ''( ''                                                ||
                                               BTrim(pg_catalog.oidvectortypes(pg_proc.proargtypes)) ||
                                               '' ) ''                                      as  comando
                                          FROM pg_catalog.pg_proc LEFT JOIN pg_catalog.pg_namespace  ON (pg_namespace.oid = pg_proc.pronamespace)
                                             , administracao.funcao
                                             , administracao.funcao_externa
                                         WHERE pg_proc.prorettype     <> ''pg_catalog.cstring''::pg_catalog.regtype
                                           AND pg_proc.proargtypes[0] <> ''pg_catalog.cstring''::pg_catalog.regtype
                                           AND NOT pg_proc.proisagg
                                           AND pg_namespace.nspname = ''public''
                                           AND pg_proc.proname != ''plpgsql_call_handler''
                                           AND Btrim(pg_proc.proname) = BTrim(Lower(funcao.nom_funcao))
                                           AND funcao.cod_funcao       = funcao_externa.cod_funcao
                                           And funcao.cod_modulo       = funcao_externa.cod_modulo
                                           And funcao.cod_biblioteca   = funcao_externa.cod_biblioteca
                      LOOP

                          stFuncoes := reFuncoes.comando ;
                          EXECUTE stFuncoes;
                      END LOOP;

                      --
                      -- Ira criar todas as funçoes do gerador no banco.
                      --
                      FOR reFuncoes IN SELECT '' Create  Or Replace '' ||
                                              Replace(funcao_externa.corpo_pl, ''\\\\'', '''') as comando
                                             , funcao.cod_funcao
                                         FROM administracao.funcao
                                            , administracao.funcao_externa
                                        WHERE funcao.cod_funcao       = funcao_externa.cod_funcao
                                          And funcao.cod_modulo       = funcao_externa.cod_modulo
                                          And funcao.cod_biblioteca   = funcao_externa.cod_biblioteca
                                          And funcao_externa.corpo_pl IS NOT NULL
                      LOOP
                          stFuncoes := reFuncoes.comando ;
                          EXECUTE stFuncoes;
                      END LOOP;

                     RETURN true;
                 END;
                 ' LANGUAGE 'plpgsql'
                 ;

    ELSE

                 DROP FUNCTION administracao.reGeradorCalculos();
                 
                 CREATE OR REPLACE FUNCTION administracao.reGeradorCalculos() RETURNS BOOLEAN AS '
                 DECLARE
                     reFuncoes          RECORD;
                     stFuncoes          VARCHAR;
                 BEGIN
                      --
                      -- Ira excluir todas as funços do gerador no banco.
                      --
                      FOR reFuncoes IN  SELECT '' Drop Function ''                                   ||
                                               Btrim(pg_proc.proname)                                ||
                                               ''( ''                                                ||
                                               BTrim(pg_catalog.oidvectortypes(pg_proc.proargtypes)) ||
                                               '' ) ''                                      as  comando
                                          FROM pg_catalog.pg_proc LEFT JOIN pg_catalog.pg_namespace  ON (pg_namespace.oid = pg_proc.pronamespace)
                                             , administracao.funcao
                                             , administracao.funcao_externa
                                         WHERE pg_proc.prorettype     <> ''pg_catalog.cstring''::pg_catalog.regtype
                                           AND pg_proc.proargtypes[0] <> ''pg_catalog.cstring''::pg_catalog.regtype
                                           AND NOT pg_proc.proisagg
                                           AND pg_namespace.nspname = ''public''
                                           AND pg_proc.proname != ''plpgsql_call_handler''
                                           AND Btrim(pg_proc.proname) = BTrim(Lower(funcao.nom_funcao))
                                           AND funcao.cod_funcao       = funcao_externa.cod_funcao
                                           And funcao.cod_modulo       = funcao_externa.cod_modulo
                                           And funcao.cod_biblioteca   = funcao_externa.cod_biblioteca
                      LOOP
                
                          stFuncoes := reFuncoes.comando ;
                          EXECUTE stFuncoes;
                      END LOOP;
                
                      --
                      -- Ira criar todas as funçoes do gerador no banco.
                      --
                      FOR reFuncoes IN SELECT '' Create  Or Replace '' ||
                                              Replace(funcao_externa.corpo_pl, ''\\\\'', '''') as comando
                                             , funcao.cod_funcao
                                         FROM administracao.funcao
                                            , administracao.funcao_externa
                                        WHERE funcao.cod_funcao       = funcao_externa.cod_funcao
                                          And funcao.cod_modulo       = funcao_externa.cod_modulo
                                          And funcao.cod_biblioteca   = funcao_externa.cod_biblioteca
                                          And funcao_externa.corpo_pl IS NOT NULL
                      LOOP
                          stFuncoes := reFuncoes.comando ;
                          EXECUTE stFuncoes;
                      END LOOP;
                
                     RETURN true;
                 END;
                 ' LANGUAGE 'plpgsql'
                 ;

    END IF;

    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';

SELECT          criar_regera();
DROP   FUNCTION criar_regera();

--Select administracao.reGeradorCalculos();



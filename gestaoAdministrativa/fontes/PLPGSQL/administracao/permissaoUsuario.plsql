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
* $Revision: 3077 $
* $Name$
* $Author: pablo $
* $Date: 2005-11-29 14:53:37 -0200 (Ter, 29 Nov 2005) $
*
* Casos de uso: uc-01.03.93
*/

CREATE OR REPLACE FUNCTION administracao.permissao_usuario() RETURNS BOOLEAN AS '
DECLARE
    reFuncoes          RECORD;
    stFuncoes          VARCHAR;
BEGIN
     --
     -- Ira excluir todas as funços do gerador no banco.
     --
     FOR reFuncoes IN  SELECT '' Grant All On Function  ''                          ||
                              Btrim(pg_proc.proname)                                ||
                              ''( ''                                                ||
                              BTrim(pg_catalog.oidvectortypes(pg_proc.proargtypes)) ||
                              '' ) To Group urbem ''                    as  comando
                         FROM pg_catalog.pg_proc LEFT JOIN pg_catalog.pg_namespace  ON (pg_namespace.oid = pg_proc.pronamespace)
                            , administracao.funcao
                            , administracao.funcao_externa
                        WHERE pg_proc.prorettype     <> ''pg_catalog.cstring''::pg_catalog.regtype
                          AND pg_proc.proargtypes[0] <> ''pg_catalog.cstring''::pg_catalog.regtype
                          AND NOT pg_proc.proisagg
                          AND pg_namespace.nspname = ''public''
                          AND pg_proc.proname != ''plpgsql_call_handler''
                          AND Btrim(pg_proc.proname) = BTrim(Lower(funcao.nom_funcao))
                          AND funcao.cod_funcao      = funcao_externa.cod_funcao
     LOOP

         stFuncoes := reFuncoes.comando ;
         EXECUTE stFuncoes;
     END LOOP;

     RETURN true;

END;
' LANGUAGE 'plpgsql'
;

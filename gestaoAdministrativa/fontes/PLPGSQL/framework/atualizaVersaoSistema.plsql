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
* $Revision: 27926 $
* $Name$
* $Author: rodrigosoares $
* $Date: 2008-02-08 16:03:10 -0200 (Sex, 08 Fev 2008) $
*
* Casos de uso: uc-01.05.01
*/


/*
 SELECT * from administracao.gestao  order by cod_gestao;
 SELECT * from administracao.historico_versao;
 INSERT INTO administracao.historico_versao ( cod_gestao, versao, versao_db ) VALUES ( 1, '1.40.0', 1 ) ;
*/

--
--  Ticket #11326 - Realocar arquivos sql das Gestões Criação tabela historico_versao
--

--
-- Função de atualização de versao.
--
   CREATE OR REPLACE FUNCTION administracao.fn_atualiza_historico_versao_sistema()
      RETURNS TRIGGER AS $$
   DECLARE
      varVersao      VARCHAR(30);
      intVersaoDb    INTEGER;
   BEGIN
      IF TG_OP='INSERT' THEN

         SELECT gestao.versao, gestao.versao_db
           INTO varVersao    , intVersaoDb
           FROM administracao.gestao
          WHERE gestao.cod_gestao = new.cod_gestao;

         UPDATE administracao.gestao SET versao = versao WHERE cod_gestao = new.cod_gestao;
      ELSE
         RAISE EXCEPTION 'atualização ou exclusão em "historico_versao" viola restrição de integridade referencial:.';
      END IF;

      RETURN NEW;
   END;
   $$ LANGUAGE plpgsql;

--
--
--
   CREATE OR REPLACE FUNCTION administracao.fn_atualiza_versao_sistema()
      RETURNS TRIGGER AS $$
   DECLARE
      varVersao      VARCHAR(30);
      intVersaoDb    INTEGER;
   BEGIN

      SELECT historico_versao.versao, historico_versao.versao_db
        INTO varVersao              , intVersaoDb
        FROM administracao.historico_versao
       WHERE historico_versao.cod_gestao = new.cod_gestao
       ORDER BY timestamp desc LIMIT 1;

      IF FOUND THEN
         new.versao     := varVersao;
         new.versao_db  := intVersaoDb;
      END IF;


      Return NEW;
   END;
   $$ LANGUAGE plpgsql;

--
--
--
--    INSERT INTO administracao.historico_versao ( cod_gestao, versao, versao_db )
--                    ( SELECT gestao.cod_gestao, gestao.versao, 0
--                        FROM administracao.gestao order by cod_gestao) ;

--
--
--
   DROP TRIGGER fn_atualiza_historico_versao_sistema ON administracao.historico_versao;
   CREATE TRIGGER fn_atualiza_historico_versao_sistema
    AFTER INSERT OR UPDATE OR DELETE
       ON administracao.historico_versao
      FOR EACH ROW
  EXECUTE PROCEDURE administracao.fn_atualiza_historico_versao_sistema()
  ;

--
--
--
   DROP TRIGGER fn_atualiza_versao_sistema ON administracao.gestao;
   CREATE TRIGGER fn_atualiza_versao_sistema
   BEFORE UPDATE
       ON administracao.gestao
      FOR EACH ROW
  EXECUTE PROCEDURE administracao.fn_atualiza_versao_sistema()
  ;

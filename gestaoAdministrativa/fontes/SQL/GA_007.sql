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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29583 $
* $Name$
* $Author: gris $
* $Date: 2008-03-27 14:37:38 -0300 (Qui, 27 Mar 2008) $
*
* Versão 006.
*/

-------------------
-- Ticket 12633
-------------------

CREATE OR REPLACE FUNCTION cgmArquivador() RETURNS VOID AS $$

DECLARE

   recProcesso      RECORD;

   varProcesso      VARCHAR;

BEGIN

   ALTER TABLE sw_processo_arquivado ADD COLUMN cgm_arquivador INTEGER;

   FOR recProcesso IN SELECT objeto
                           , numcgm
                        FROM administracao.auditoria
                       WHERE extract(YEAR from timestamp)  = '2008'
                         AND (cod_acao = 127 OR cod_acao = 2162)

   LOOP

      IF recProcesso.objeto ilike 'Processo%' THEN

         varProcesso := substr(recProcesso.objeto,11, (position ('/' IN recProcesso.objeto)) -1);

      ELSE

         varProcesso := recProcesso.objeto;

      END IF;

      RAISE NOTICE 'Processo-campo %', recProcesso.objeto;
      RAISE NOTICE 'Processo %', varProcesso;

      UPDATE sw_processo_arquivado
         SET cgm_arquivador = recProcesso.numcgm
       WHERE TO_NUMBER(BTRIM(varProcesso),'999999999') = cod_processo
         AND ano_exercicio = '2008';

   END LOOP;

   UPDATE sw_processo_arquivado
      SET cgm_arquivador = 0
    WHERE cgm_arquivador IS NULL;

   ALTER TABLE sw_processo_arquivado ALTER COLUMN cgm_arquivador SET NOT NULL;
   ALTER TABLE sw_processo_arquivado ADD CONSTRAINT fk_processo_arquivado_3 FOREIGN KEY (cgm_arquivador) REFERENCES sw_cgm(numcgm);

END;

$$ LANGUAGE 'plpgsql';

SELECT cgmArquivador();
DROP FUNCTION cgmArquivador();

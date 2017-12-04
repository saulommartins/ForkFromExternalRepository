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
* $Id: buscaAliquotaAtividade.sql 29200 2008-04-15 13:48:27Z fabio $
*
* Casos de uso: uc-05.02.07
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$
Revision 1.1  2007/10/08 14:22:44  fabio
para calculos do economico


*/

--BEGIN;
--INSERT INTO administracao.funcao values(14,1,36,4,'buscaAliquotaAtividade');
--INSERT INTO administracao.variavel  values(14,1,36,1,'intCodigoAtividade',1);
--INSERT INTO administracao.parametro values(14,1,36,1,1);
--COMMIT;

   CREATE OR REPLACE FUNCTION buscaAliquotaAtividade( intCodigoAtividade INTEGER )
   RETURNS NUMERIC AS $$
   DECLARE
      nuValor             NUMERIC;
   BEGIN

      SELECT valor
        INTO nuValor
        FROM economico.aliquota_atividade
       WHERE cod_atividade = intCodigoAtividade
         AND dt_vigencia = (
                             SELECT MAX(dt_vigencia)
                               FROM economico.aliquota_atividade
                              WHERE dt_vigencia < now()::date
                                    AND cod_atividade = intCodigoAtividade
                           )
      ;

      RETURN nuValor;
   END;
   $$ LANGUAGE 'plpgsql';

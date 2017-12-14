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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-06.00.00
*/
   --
   -- Função que atribui sequencial para um pagamento de empenho.
   --
   CREATE OR REPLACE FUNCTION tc.numero_pagamento_empenho( varExercicio     VARCHAR
                                                         , intCodEntidade   INTEGER
                                                         , intCodNota       INTEGER
                                                         , varTimeStamp     TIMESTAMP)
   RETURNS INTEGER AS $$
   DECLARE
      recRegistro       RECORD;
      intRetorno        INTEGER;
   BEGIN
      ALTER SEQUENCE tc.seqEmpenhoPagamento RESTART WITH 1;

      FOR recRegistro
       IN SELECT timestamp
            FROM empenho.nota_liquidacao_paga
           WHERE exercicio    = varExercicio
             AND cod_entidade = intCodEntidade
             AND cod_nota     = intCodNota
           ORDER BY  timestamp
      LOOP
         intRetorno := nextval('tc.seqEmpenhoPagamento');
         IF recRegistro.timestamp = varTimeStamp THEN
            EXIT;
         END IF;
      END LOOP;

      RETURN intRetorno;
   END;
   $$ LANGUAGE 'plpgsql'  SECURITY DEFINER;



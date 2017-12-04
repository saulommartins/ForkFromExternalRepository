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
* $Id: seq_num_op_extra.plsql 59612 2014-09-02 12:00:51Z gelson $
* $Revision: $
* $Author: $
* $Date: $
*
* Caso de uso: uc-06.04.00
*/
CREATE OR REPLACE FUNCTION tcemg.seq_num_op_extra( varExercicio         VARCHAR
                                                 , intCodEntidade       INTEGER
                                                 , intCodTipo           INTEGER
                                                 , intCodLote           INTEGER  ) RETURNS INTEGER AS $$
DECLARE
   recRegistro       RECORD;
   intRetorno        INTEGER;
BEGIN

   ALTER SEQUENCE tcemg.seq_num_op_extra RESTART WITH 1;

   FOR recRegistro
    IN SELECT cod_lote
         FROM tesouraria.transferencia
        WHERE exercicio                  = varExercicio
          AND transferencia.cod_entidade = intCodEntidade
          AND transferencia.cod_tipo     = intCodTipo
     ORDER BY exercicio, cod_entidade, cod_tipo,cod_lote
   LOOP
      intRetorno := nextval('tcemg.seq_num_op_extra');
      IF recRegistro.cod_lote = intCodlote THEN
         EXIT;
      END IF;
   END LOOP;

   RETURN intRetorno;
END;

$$ LANGUAGE 'plpgsql' SECURITY DEFINER;
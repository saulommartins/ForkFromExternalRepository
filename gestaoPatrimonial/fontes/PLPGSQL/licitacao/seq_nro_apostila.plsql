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
* $Id:$
* $Revision: $
* $Author: $
* $Date: $
*
*/
CREATE OR REPLACE FUNCTION licitacao.seq_nro_contrato_apostila( varExercicio          VARCHAR
                                                 , intCodEntidade           INTEGER
                                                 , intCodContrato           INTEGER
                                                 , intCodApostila           INTEGER  ) RETURNS INTEGER AS $$
DECLARE
   recRegistro       RECORD;
   intRetorno        INTEGER;
BEGIN

   ALTER SEQUENCE licitacao.seq_nro_contrato_apostila RESTART WITH 1;

   FOR recRegistro
    IN SELECT cod_apostila 
         FROM licitacao.contrato_apostila
        WHERE exercicio                  = varExercicio
          AND contrato_apostila.cod_entidade = intCodEntidade
          AND contrato_apostila.num_contrato     = intCodContrato
     ORDER BY exercicio, cod_entidade,num_contrato,cod_apostila
   LOOP
      intRetorno := nextval('licitacao.seq_nro_contrato_apostila');
      IF recRegistro.cod_apostila = intCodApostila THEN
         EXIT;
      END IF;
   END LOOP;

   RETURN intRetorno;
END;

$$ LANGUAGE 'plpgsql' SECURITY DEFINER;
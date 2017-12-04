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
* $Id:$
*
* Versão 1.98.5
*/

---------------------------------
-- AJUSTE DE COBRANCAS CANCELADAS
---------------------------------

INSERT
  INTO divida.parcelamento_cancelamento
     ( num_parcelamento
     , numcgm
     , motivo
     , timestamp
     )
SELECT distinct(num_parcelamento)   AS num_parcelamento
     , 0                            AS numcgm
     , 'Cobranças cabceladas antes de '||(
         SELECT date_part('day', MIN(timestamp))||'/'||date_part('month', MIN(timestamp))||'/'||date_part('year', MIN(timestamp))
           FROM administracao.historico_versao
          WHERE cod_gestao = 5
            AND versao = '1.91.0'
             )                      AS motivo
     , (
         SELECT MIN(timestamp)
           FROM administracao.historico_versao
          WHERE cod_gestao = 5
            AND versao = '1.91.0'
       )                            AS timestamp
  FROM divida.parcela
 WHERE cancelada = TRUE
   AND num_parcelamento NOT IN (
                                 SELECT num_parcelamento
                                   FROM divida.parcelamento_cancelamento
                               )
     ;

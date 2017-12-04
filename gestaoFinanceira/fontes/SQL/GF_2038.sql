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
*
* Script de DDL e DML
*
* Versao 2.03.8
*
* Fabio Bertoldi - 20150424
*
*/

----------------
-- Ticket #22864
----------------

INSERT
  INTO contabilidade.historico_contabil
SELECT 220
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 820
   AND exercicio = '2014'
     ;
INSERT
  INTO contabilidade.historico_contabil
SELECT 221
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 821
   AND exercicio = '2014'
     ;
INSERT
  INTO contabilidade.historico_contabil
SELECT 222
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 822
   AND exercicio = '2014'
     ;
INSERT
  INTO contabilidade.historico_contabil
SELECT 223
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 823
   AND exercicio = '2014'
     ;
INSERT
  INTO contabilidade.historico_contabil
SELECT 220
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 820
   AND exercicio = '2015'
     ;
INSERT
  INTO contabilidade.historico_contabil
SELECT 221
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 821
   AND exercicio = '2015'
     ;
INSERT
  INTO contabilidade.historico_contabil
SELECT 222
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 822
   AND exercicio = '2015'
     ;
INSERT
  INTO contabilidade.historico_contabil
SELECT 223
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
  FROM contabilidade.historico_contabil
 WHERE cod_historico = 823
   AND exercicio = '2015'
     ;


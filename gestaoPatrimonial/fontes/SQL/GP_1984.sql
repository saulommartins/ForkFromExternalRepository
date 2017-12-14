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
* Versão 1.98.4
*/

--------------------------------------------
-- REMOVENDO ACOES DAS ROTINAS DE INVENTARIO
--------------------------------------------

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN ( 2403
                   , 2404
                   , 2713
                   , 2406
                   , 2407
                   , 2408
                   , 2409
                   , 2405
                   )
     ;

DELETE
  FROM administracao.auditoria
 WHERE cod_acao IN ( 2403
                   , 2404
                   , 2713
                   , 2406
                   , 2407
                   , 2408
                   , 2409
                   , 2405
                   )
     ;

DELETE
  FROM administracao.acao
 WHERE cod_acao IN ( 2403
                   , 2404
                   , 2713
                   , 2406
                   , 2407
                   , 2408
                   , 2409
                   , 2405
                   )
     ;


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
* Versao 2.02.5
*
* Fabio Bertoldi - 20140725
*
*/

----------------
-- Ticket #21712
----------------

UPDATE administracao.funcionalidade 
   SET nom_funcionalidade = 'Conta-Corrente'
 WHERE cod_funcionalidade = 250 
     ;

UPDATE administracao.acao 
   SET nom_acao = 'Incluir Conta-Corrente'
 WHERE cod_acao = 1071
     ;

UPDATE administracao.acao 
   SET nom_acao = 'Alterar Conta-Corrente'
 WHERE cod_acao = 1072
     ;

UPDATE administracao.acao 
   SET nom_acao = 'Exlcuir Conta-Corrente'
 WHERE cod_acao = 1073
     ;


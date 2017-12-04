/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municí­pos                         *
    * @author Confederação Nacional de Municí­pios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí­-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuí­do  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implí­cita  de  COMERCIABILIDADE  OU *
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
* Versao 2.04.9
*
* Fabio Bertoldi - 20160422
*
*/

----------------
-- Ticket #23676
----------------

UPDATE administracao.tabelas_rh SET sequencia = 3 where schema_cod = 1 and nome_tabela = 'causa_rescisao'   ;
UPDATE administracao.tabelas_rh SET sequencia = 2 WHERE schema_cod = 3 and nome_tabela = 'curso'            ;
UPDATE administracao.tabelas_rh SET sequencia = 2 where schema_cod = 1 and nome_tabela = 'mov_sefip_saida'  ;
UPDATE administracao.tabelas_rh SET sequencia = 2 where schema_cod = 1 and nome_tabela = 'mov_sefip_retorno';


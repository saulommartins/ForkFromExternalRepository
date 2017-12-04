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
* Versao 2.01.7
*
* Fabio Bertoldi - 20130604
*
*/

----------------
-- Ticket #20350
----------------

UPDATE administracao.acao SET ativo = FALSE where cod_acao = 1545;
UPDATE administracao.acao SET ativo = FALSE where cod_acao = 1546;
UPDATE administracao.acao SET ativo = FALSE where cod_acao = 1547;
UPDATE administracao.acao SET ativo = FALSE where cod_acao = 1552;


----------------
-- Ticket #20352
----------------

ALTER TABLE ppa.programa ADD   COLUMN num_programa INTEGER;
UPDATE      ppa.programa SET          num_programa = cod_programa;
ALTER TABLE ppa.programa ALTER COLUMN num_programa SET NOT NULL;

ALTER TABLE ppa.acao     ADD   COLUMN num_acao     INTEGER;
UPDATE      ppa.acao     SET          num_acao     = cod_acao;
ALTER TABLE ppa.acao     ALTER COLUMN num_acao     SET NOT NULL;


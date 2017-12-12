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
* Versão 1.98.9
*/

----------------
-- Ticket #16591
----------------

SELECT atualizarbanco('ALTER TABLE pessoal.assentamento_gerado ALTER COLUMN observacao TYPE VARCHAR(1800);');


----------------
-- Ticket #16549
----------------

SELECT atualizarbanco('INSERT INTO pessoal.assentamento_motivo (cod_motivo, descricao) VALUES (14, \'Alteração Cargo\'        );');
SELECT atualizarbanco('INSERT INTO pessoal.assentamento_motivo (cod_motivo, descricao) VALUES (15, \'Alteração Função\'       );');
SELECT atualizarbanco('INSERT INTO pessoal.assentamento_motivo (cod_motivo, descricao) VALUES (16, \'Alteração Lotação/Local\');');
SELECT atualizarbanco('INSERT INTO pessoal.assentamento_motivo (cod_motivo, descricao) VALUES (17, \'Averbação Tempo Serviço\');');


----------------
-- Ticket #16559
----------------

SELECT atualizarbanco('ALTER TABLE pessoal.assentamento_gerado_norma DROP CONSTRAINT pk_assentamento_gerado_norma;                                                            ');
SELECT atualizarbanco('ALTER TABLE pessoal.assentamento_gerado_norma ADD  CONSTRAINT pk_assentamento_gerado_norma PRIMARY KEY (cod_assentamento_gerado, timestamp, cod_norma);');


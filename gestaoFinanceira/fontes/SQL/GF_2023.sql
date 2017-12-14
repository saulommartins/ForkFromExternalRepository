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
* Versao 2.02.3
*
* Eduardo Schitz - 20140326
*
*/

----------------
-- Ticket #21576
----------------

ALTER TABLE empenho.item_pre_empenho ALTER COLUMN nom_item TYPE text;

----------------
-- Ticket #21596
----------------

INSERT INTO contabilidade.nota_explicativa_acao (cod_acao) VALUES (2896);
INSERT INTO contabilidade.nota_explicativa_acao (cod_acao) VALUES (2894);
INSERT INTO contabilidade.nota_explicativa_acao (cod_acao) VALUES (2931);
INSERT INTO contabilidade.nota_explicativa_acao (cod_acao) VALUES (2893);
INSERT INTO contabilidade.nota_explicativa_acao (cod_acao) VALUES (2892);
INSERT INTO contabilidade.nota_explicativa_acao (cod_acao) VALUES (2897);

----------------
-- Ticket #21555
----------------

UPDATE contabilidade.plano_conta SET nom_conta = REPLACE(nom_conta, '–', '-') WHERE nom_conta LIKE '%–%';

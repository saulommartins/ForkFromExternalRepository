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
* Versao 2.02.9
*
* Fabio Bertoldi - 20140716
*
*/

----------------
-- Ticket #21534
----------------

SELECT atualizarbanco('UPDATE folhapagamento.tipo_evento_salario_familia SET descricao = ''Evento de Provento para Salário-Família'' WHERE cod_tipo = 1;');
SELECT atualizarbanco('UPDATE folhapagamento.tipo_evento_salario_familia SET descricao = ''Evento Base Salário-Família''             WHERE cod_tipo = 2;');


----------------
-- Ticket #21534
----------------

SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario DROP CONSTRAINT fk_beneficiario_2;');
SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario ADD  CONSTRAINT fk_beneficiario_2 FOREIGN KEY (cgm_fornecedor)
                                                                                            REFERENCES compras.fornecedor(cgm_fornecedor);');


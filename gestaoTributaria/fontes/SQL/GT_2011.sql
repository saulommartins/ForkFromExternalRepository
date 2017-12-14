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
* Versao 2.01.1
*
* Fabio Bertoldi - 20121008
*
*/

----------------
-- Ticket #19815
----------------

INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES (2012, 25, 'imprimir_carne_isento', '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES (2012, 25, 'grupo_nota_avulsa'    , '');


----------------
-- Ticket #19813
----------------

ALTER TABLE arrecadacao.nota_servico DROP CONSTRAINT pk_nota_servico;
ALTER TABLE arrecadacao.nota_servico ADD  CONSTRAINT pk_nota_servico PRIMARY KEY (cod_nota,cod_servico, cod_atividade, inscricao_economica, ocorrencia, timestamp);

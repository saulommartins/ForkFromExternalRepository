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
* Gelson Gonçalves - 20140313
*
*/

----------------
-- Ticket #21523
----------------

--Cria tabela frota.tipo_baixa
CREATE TABLE frota.tipo_baixa (
cod_tipo INTEGER NOT NULL,
descricao VARCHAR NULL,
CONSTRAINT pk_tipo_baixa PRIMARY KEY (cod_tipo)
);

--Carga dos dados
INSERT INTO frota.tipo_baixa (cod_tipo, descricao) VALUES (1, 'Alienação');
INSERT INTO frota.tipo_baixa (cod_tipo, descricao) VALUES (2, 'Obsolescência (bens inservíveis)');
INSERT INTO frota.tipo_baixa (cod_tipo, descricao) VALUES (3, 'Sinistro');
INSERT INTO frota.tipo_baixa (cod_tipo, descricao) VALUES (4, 'Doação');
INSERT INTO frota.tipo_baixa (cod_tipo, descricao) VALUES (5, 'Cessão');
INSERT INTO frota.tipo_baixa (cod_tipo, descricao) VALUES (7, 'Transferência');
INSERT INTO frota.tipo_baixa (cod_tipo, descricao) VALUES (99,'Outros');

--Adicionar campo e fk na tabela veiculo_baixado

ALTER TABLE frota.veiculo_baixado ADD cod_tipo_baixa INTEGER NOT NULL DEFAULT 99;
ALTER TABLE frota.veiculo_baixado ADD CONSTRAINT fk_tipo_baixa FOREIGN KEY (cod_tipo_baixa) REFERENCES frota.tipo_baixa(cod_tipo);
----------------


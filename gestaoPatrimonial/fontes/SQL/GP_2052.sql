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
* Versao 2.05.2
*
* Fabio Bertoldi - 20160530
*
*/

----------------
-- Ticket ##23761
----------------

CREATE TABLE almoxarifado.lancamento_ordem(
    cod_lancamento          INTEGER     NOT NULL,
    cod_item                INTEGER     NOT NULL,
    cod_marca               INTEGER     NOT NULL,
    cod_almoxarifado        INTEGER     NOT NULL,
    cod_centro              INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_ordem               INTEGER     NOT NULL,
    tipo                    CHAR(1)     NOT NULL,
    cod_pre_empenho         INTEGER     NOT NULL,
    exercicio_pre_empenho   CHAR(4)     NOT NULL,
    num_item                INTEGER     NOT NULL,
    CONSTRAINT pk_lancamento_ordem      PRIMARY KEY  (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro, exercicio, cod_entidade, cod_ordem, tipo, cod_pre_empenho, exercicio_pre_empenho, num_item),
    CONSTRAINT fk_lancamento_ordem_1    FOREIGN KEY                                            (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro)
                                        REFERENCES almoxarifado.lancamento_material            (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro),
    CONSTRAINT fk_lancamento_ordem_2    FOREIGN KEY                   (exercicio, cod_entidade, cod_ordem, tipo, cod_pre_empenho, exercicio_pre_empenho, num_item)
                                        REFERENCES compras.ordem_item (exercicio, cod_entidade, cod_ordem, tipo, cod_pre_empenho, exercicio_pre_empenho, num_item)
);
GRANT ALL ON almoxarifado.lancamento_ordem TO urbem;


----------------
-- Ticket #23834
----------------

ALTER TABLE patrimonio.tipo_baixa ALTER COLUMN descricao TYPE VARCHAR(60);
UPDATE      patrimonio.tipo_baixa SET descricao = 'Baixa Patrimonial por Alienação com Ganho' WHERE cod_tipo = 7;
UPDATE      patrimonio.tipo_baixa SET descricao = 'Baixa Patrimonial por Alienação com Perda' WHERE cod_tipo = 8;


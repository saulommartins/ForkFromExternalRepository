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
* Versao 2.02.4
*
* Fabio Bertoldi - 20140407
*
*/

------------------------------------------
-- CORRECAO DA ACAO 1711 - Silvia 20140407
------------------------------------------

UPDATE administracao.acao SET nom_acao = 'Excluir Lançamentos Contábeis de Encerramento' WHERE cod_acao = 1711;


----------------
-- Ticket #21577
----------------

ALTER TABLE empenho.item_pre_empenho ADD COLUMN cod_item integer;
ALTER TABLE empenho.item_pre_empenho ADD CONSTRAINT fk_item_pre_empenho_3 FOREIGN KEY (cod_item)
                                         REFERENCES almoxarifado.catalogo_item (cod_item);


----------------
-- Ticket #21663
----------------

CREATE TABLE orcamento.receita_credito_tributario (
    cod_receita         INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_conta           INTEGER     NOT NULL,
    CONSTRAINT pk_receita_credito_tributario    PRIMARY KEY                          (cod_receita, exercicio),
    CONSTRAINT fk_receita_credito_tributario_1  FOREIGN KEY                          (cod_receita, exercicio)
                                                REFERENCES orcamento.receita         (cod_receita, exercicio),
    CONSTRAINT fk_receita_credito_tributario_2  FOREIGN KEY                          (cod_conta, exercicio)
                                                REFERENCES contabilidade.plano_conta (cod_conta, exercicio)
);
GRANT ALL ON orcamento.receita_credito_tributario TO urbem;


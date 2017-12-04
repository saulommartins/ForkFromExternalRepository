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
* Versao 2.02.7
*
* Fabio Bertolri - 20140612
*
*/

----------------
-- Ticket #21803
----------------

CREATE TABLE compras.justificativa_razao(
    cod_compra_direta   INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    exercicio_entidade  CHAR(4)     NOT NULL,
    cod_modalidade      INTEGER     NOT NULL,
    justificativa       TEXT        NOT NULL,
    razao               TEXT        NOT NULL,
    CONSTRAINT pk_justificativa_razao   PRIMARY KEY (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade),
    CONSTRAINT fk_justificativa_razao_1 FOREIGN KEY (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade)
                                        REFERENCES compras.compra_direta(cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade)
);
GRANT ALL ON compras.justificativa_razao TO urbem;


----------------
-- Ticket #21805
----------------

ALTER TABLE licitacao.publicacao_edital ADD COLUMN num_publicacao INTEGER;


----------------
-- Ticket #21807
----------------

UPDATE administracao.acao SET nom_acao = 'Anular Processo Licitatório' WHERE cod_acao = 1569;

ALTER TABLE licitacao.licitacao_anulada ADD COLUMN fracassada BOOLEAN NOT NULL DEFAULT FALSE;


----------------
-- Ticket #21811
----------------

CREATE TABLE licitacao.justificativa_razao(
    cod_licitacao       INTEGER     NOT NULL,
    cod_modalidade      INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    justificativa       TEXT        NOT NULL,
    razao               TEXT        NOT NULL,
    CONSTRAINT pk_justificativa_razao   PRIMARY KEY (cod_licitacao, cod_modalidade, cod_entidade, exercicio),
    CONSTRAINT fk_justificativa_razao_1 FOREIGN KEY (cod_licitacao, cod_modalidade, cod_entidade, exercicio)
                                        REFERENCES licitacao.licitacao (cod_licitacao, cod_modalidade, cod_entidade, exercicio)
);
GRANT ALL ON licitacao.justificativa_razao TO urbem;


----------------
-- Ticket #21880
----------------

CREATE TABLE tcemg.regime_execucao_obras (
    cod_regime      INTEGER         NOT NULL,
    descricao       VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_regime_execucao_obras PRIMARY KEY (cod_regime)                    
);
GRANT ALL ON tcemg.regime_execucao_obras TO urbem;

INSERT INTO tcemg.regime_execucao_obras VALUES (1, 'Empreitada por Preço Global');
INSERT INTO tcemg.regime_execucao_obras VALUES (2, 'Empreitada por Preço Unitário');
INSERT INTO tcemg.regime_execucao_obras VALUES (3, 'Empreitada Integral');
INSERT INTO tcemg.regime_execucao_obras VALUES (4, 'Tarefa');
INSERT INTO tcemg.regime_execucao_obras VALUES (5, 'Execução Direta');

ALTER TABLE licitacao.licitacao ADD COLUMN num_orgao   INTEGER;
ALTER TABLE licitacao.licitacao ADD COLUMN num_unidade INTEGER;
ALTER TABLE licitacao.licitacao ADD COLUMN cod_regime  INTEGER;

ALTER TABLE licitacao.licitacao ADD CONSTRAINT fk_licitacao_9  FOREIGN KEY (cod_regime)
                                                               REFERENCES tcemg.regime_execucao_obras(cod_regime);
ALTER TABLE licitacao.licitacao ADD CONSTRAINT fk_licitacao_10 FOREIGN KEY (exercicio, num_unidade, num_orgao)
                                                               REFERENCES orcamento.unidade(exercicio, num_unidade, num_orgao);


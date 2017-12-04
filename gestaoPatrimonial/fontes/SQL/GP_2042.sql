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
* Versao 2.04.2
*
* Fabio Bertoldi - 20150720
*
*/

----------------
-- Ticket #23145
----------------

CREATE TABLE licitacao.tipo_alteracao_valor(
    cod_tipo        INTEGER             NOT NULL,
    descricao       VARCHAR(40)         NOT NULL,
    CONSTRAINT pk_tipo_alteracao_valor  PRIMARY KEY (cod_tipo)
);
GRANT ALL ON licitacao.tipo_alteracao_valor TO urbem;

INSERT INTO licitacao.tipo_alteracao_valor (cod_tipo, descricao) VALUES (1, 'Acréscimo de valor'          );
INSERT INTO licitacao.tipo_alteracao_valor (cod_tipo, descricao) VALUES (2, 'Decréscimo de valor'         );
INSERT INTO licitacao.tipo_alteracao_valor (cod_tipo, descricao) VALUES (3, 'Não houve alteração de valor');


CREATE TABLE licitacao.tipo_termo_aditivo(
    cod_tipo        INTEGER             NOT NULL,
    descricao       VARCHAR(100)        NOT NULL,
    CONSTRAINT pk_tipo_termo_aditivo    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON licitacao.tipo_termo_aditivo TO urbem;


INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (04, 'Reajuste'                                                                      );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (05, 'Recomposição (Equilíbrio Financeiro)'                                          );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (06, 'Outros'                                                                        );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (07, 'Alteração de Prazo de Vigência'                                                );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (08, 'Alteração de Prazo de Execução'                                                );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (09, 'Acréscimo de Item(ns)'                                                         );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (10, 'Decréscimo de Item(ns)'                                                        );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (11, 'Acréscimo e Decréscimo de Item(ns)'                                            );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (12, 'Alteração de Projeto/Especificação (Art. 65, I, a, da Lei n. 8.666/93)'        );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (13, 'Alteração de Prazo de vigência e Prazo de Execução'                            );
INSERT INTO licitacao.tipo_termo_aditivo (cod_tipo, descricao) VALUES (14, 'Acréscimo/Decréscimo de item(ns) conjugado com outros tipos de termos aditivos');


----------------
-- Ticket #23147
----------------

ALTER TABLE licitacao.contrato_aditivos ADD COLUMN fim_execucao     DATE;
ALTER TABLE licitacao.contrato_aditivos ADD COLUMN justificativa    VARCHAR(250);

ALTER TABLE licitacao.contrato_aditivos ADD COLUMN tipo_termo_aditivo   INTEGER;
ALTER TABLE licitacao.contrato_aditivos ADD CONSTRAINT fk_contrato_aditivos_4   FOREIGN KEY (tipo_termo_aditivo)
                                                                                REFERENCES licitacao.tipo_termo_aditivo(cod_tipo);

ALTER TABLE licitacao.contrato_aditivos ADD COLUMN tipo_valor           INTEGER;
ALTER TABLE licitacao.contrato_aditivos ADD CONSTRAINT fk_contrato_aditivos_5   FOREIGN KEY (tipo_valor)
                                                                                REFERENCES licitacao.tipo_alteracao_valor(cod_tipo);

ALTER TABLE licitacao.contrato_aditivos ALTER COLUMN objeto TYPE TEXT;


ALTER TABLE licitacao.contrato_aditivos_anulacao ADD COLUMN valor_anulacao NUMERIC(14,2) NOT NULL DEFAULT 0;


----------------
-- Ticket #23123
----------------

CREATE TABLE licitacao.tipo_garantia(
    cod_garantia    INTEGER         NOT NULL,
    descricao       VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_tipo_garantia     PRIMARY KEY (cod_garantia)
);
GRANT ALL ON licitacao.tipo_garantia TO urbem;

INSERT INTO licitacao.tipo_garantia (cod_garantia, descricao) VALUES (1, 'Caução em dinheiro'      );
INSERT INTO licitacao.tipo_garantia (cod_garantia, descricao) VALUES (2, 'Título da dívida pública');
INSERT INTO licitacao.tipo_garantia (cod_garantia, descricao) VALUES (3, 'Seguro garantia'         );
INSERT INTO licitacao.tipo_garantia (cod_garantia, descricao) VALUES (4, 'Fiança bancária'         );
INSERT INTO licitacao.tipo_garantia (cod_garantia, descricao) VALUES (5, 'Sem garantia'            );

ALTER TABLE licitacao.contrato ADD   COLUMN numero_contrato NUMERIC(14);
UPDATE      licitacao.contrato SET          numero_contrato = num_contrato;
ALTER TABLE licitacao.contrato ALTER COLUMN numero_contrato SET NOT NULL;
ALTER TABLE licitacao.contrato ADD CONSTRAINT uk_contrato_1 UNIQUE (numero_contrato, exercicio, cod_entidade);

ALTER TABLE licitacao.contrato ADD COLUMN exercicio_orgao CHAR(4);
ALTER TABLE licitacao.contrato ADD COLUMN num_orgao       INTEGER;
ALTER TABLE licitacao.contrato ADD COLUMN num_unidade     INTEGER;
ALTER TABLE licitacao.contrato ADD CONSTRAINT fk_contrato_6 FOREIGN KEY (exercicio_orgao, num_orgao, num_unidade)
                                                            REFERENCES orcamento.unidade (exercicio, num_orgao, num_unidade);

ALTER TABLE licitacao.contrato ADD COLUMN tipo_objeto     INTEGER;
ALTER TABLE licitacao.contrato ADD CONSTRAINT fk_contrato_7 FOREIGN KEY (tipo_objeto)
                                                            REFERENCES compras.tipo_objeto(cod_tipo_objeto);

ALTER TABLE licitacao.contrato ADD COLUMN objeto TEXT;

ALTER TABLE licitacao.contrato ADD COLUMN cod_garantia    INTEGER;
ALTER TABLE licitacao.contrato ADD CONSTRAINT fk_contrato_8 FOREIGN KEY (cod_garantia)
                                                            REFERENCES licitacao.tipo_garantia(cod_garantia);

ALTER TABLE licitacao.contrato ADD COLUMN forma_fornecimento    VARCHAR(50);
ALTER TABLE licitacao.contrato ADD COLUMN forma_pagamento       VARCHAR(100);

ALTER TABLE licitacao.contrato ADD COLUMN cgm_signatario  INTEGER;
ALTER TABLE licitacao.contrato ADD CONSTRAINT fk_contrato_9 FOREIGN KEY (cgm_signatario)
                                                            REFERENCES sw_cgm_pessoa_fisica(numcgm);

ALTER TABLE licitacao.contrato_anulado ADD COLUMN valor_anulacao NUMERIC(14,2) NOT NULL DEFAULT 0;

ALTER TABLE licitacao.contrato ADD COLUMN justificativa       VARCHAR(250)  NOT NULL DEFAULT '';
ALTER TABLE licitacao.contrato ADD COLUMN razao               VARCHAR(250)  NOT NULL DEFAULT '';
ALTER TABLE licitacao.contrato ADD COLUMN fundamentacao_legal VARCHAR(250)  NOT NULL DEFAULT '';
ALTER TABLE licitacao.contrato ADD COLUMN multa_rescisoria    VARCHAR(100)  NOT NULL DEFAULT '';
ALTER TABLE licitacao.contrato ADD COLUMN prazo_execucao      VARCHAR(100)  NOT NULL DEFAULT '';


----------------
-- Ticket #23152
----------------

ALTER TABLE compras.justificativa_razao   ADD COLUMN fundamentacao_legal VARCHAR(250) NOT NULL DEFAULT '';
ALTER TABLE licitacao.justificativa_razao ADD COLUMN fundamentacao_legal VARCHAR(250) NOT NULL DEFAULT '';


----------------
-- Ticket #23153
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3074
     , 356
     , 'FLManterPublicacaoCompraDireta.php'
     , 'manter'
     , 25
     , ''
     , 'Manter Publicação'
     , TRUE
     );

UPDATE administracao.acao SET ordem = 15 WHERE cod_acao = 2818;
UPDATE administracao.acao SET ordem = 18 WHERE cod_acao = 1716;
UPDATE administracao.acao SET ordem = 19 WHERE cod_acao = 2325;
UPDATE administracao.acao SET ordem = 21 WHERE cod_acao = 1730;

CREATE TABLE compras.publicacao_compra_direta(
    cod_compra_direta   INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    exercicio_entidade  CHAR(4)     NOT NULL,
    cod_modalidade      INTEGER     NOT NULL,
    cgm_veiculo         INTEGER     NOT NULL,
    data_publicacao     DATE        NOT NULL,
    observacao          VARCHAR(80)         ,
    num_publicacao      INTEGER             ,
    CONSTRAINT pk_publicacao_compra_direta      PRIMARY KEY (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade, cgm_veiculo),
    CONSTRAINT fk_publicacao_compra_direta_1    FOREIGN KEY (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade)
                            REFERENCES compras.compra_direta(cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade),
    CONSTRAINT fk_publicacao_compra_direta_2    FOREIGN KEY (cgm_veiculo)
                            REFERENCES licitacao.veiculos_publicidade(numcgm)
);
GRANT ALL ON compras.publicacao_compra_direta TO urbem;

UPDATE administracao.acao SET ordem = 21 WHERE cod_acao = 2821;
UPDATE administracao.acao SET ordem = 22 WHERE cod_acao = 1730;


----------------
-- Ticket #22850
----------------

CREATE TABLE licitacao.participante_certificacao_licitacao(
    num_certificacao        INTEGER     NOT NULL,
    exercicio_certificacao  CHAR(4)     NOT NULL,
    cgm_fornecedor          INTEGER     NOT NULL,
    cod_licitacao           INTEGER     NOT NULL,
    cod_modalidade          INTEGER     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    exercicio_licitacao     CHAR(4)     NOT NULL,
    CONSTRAINT pk_participante_certificacao_licitacao   PRIMARY KEY                                   (num_certificacao, exercicio_certificacao, cgm_fornecedor, cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao),
    CONSTRAINT fk_participante_certificacao_licitacao_1 FOREIGN KEY                                   (num_certificacao, exercicio_certificacao, cgm_fornecedor)
                                                        REFERENCES licitacao.participante_certificacao(num_certificacao, exercicio, cgm_fornecedor),
    CONSTRAINT fk_participante_certificacao_licitacao_2 FOREIGN KEY                   (cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao)
                                                        REFERENCES licitacao.licitacao(cod_licitacao, cod_modalidade, cod_entidade, exercicio)
);
GRANT ALL ON licitacao.participante_certificacao_licitacao TO urbem;


----------------
-- Ticket #22695
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3075
     , 287
     , 'FLRelatorioNotasFiscais.php'
     , 'emitir'
     , 20
     , 'Relatório de Notas Fiscais'
     , 'Relatório de Notas Fiscais'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 3
     , 29
     , 15
     , 'Relatório de Notas Fiscais'
     , 'relatorioNotasFiscais.rptdesign'
     );
 


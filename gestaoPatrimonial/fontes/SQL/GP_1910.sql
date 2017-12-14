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
* $Id:  $
*
* Versão 1.91.0
*/

update administracao.acao set ordem = 20 where cod_acao = 1574;
update administracao.acao set ordem = 30 where cod_acao = 1575;
update administracao.acao set ordem = 40 where cod_acao = 1589;
update administracao.acao set ordem = 50 where cod_acao = 1590;
update administracao.acao set ordem = 60 where cod_acao = 2185;
update administracao.acao set ordem = 20 where cod_acao = 103 ;


 INSERT INTO administracao.relatorio
             (cod_gestao
           , cod_modulo
           , cod_relatorio
           , nom_relatorio
           , arquivo)
      VALUES (3
           , 6
           , 2
           , 'Termo de Responsabilidade'
           , 'termoResponsabilidade.rptdesign') ;

 INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2183
          , 28
          , 'termoResponsabilidade.php'
          , 'imprimir'
          , 100
          , ''
          , 'Termo de Responsabilidade');

--------------
-- gp_almox_inventario
--------------

-- uc_03-03-15

CREATE TABLE almoxarifado.inventario (
  exercicio        CHAR(4)        NOT NULL,
  cod_almoxarifado INTEGER        NOT NULL,
  cod_inventario   INTEGER        NOT NULL,
  dt_inventario    DATE           NULL,
  observacao       VARCHAR(160)   NULL,
  processado       BOOLEAN        NOT NULL DEFAULT FALSE,
  CONSTRAINT pk_inventario PRIMARY KEY(exercicio, cod_almoxarifado, cod_inventario),
  CONSTRAINT fk_inventario_1 FOREIGN KEY (cod_almoxarifado) REFERENCES almoxarifado.almoxarifado(cod_almoxarifado)
);

CREATE TABLE almoxarifado.inventario_itens (
  exercicio          CHAR(4)       NOT NULL,
  cod_almoxarifado   INTEGER       NOT NULL,
  cod_inventario     INTEGER       NOT NULL,
  cod_item           INTEGER       NOT NULL,
  cod_marca          INTEGER       NOT NULL,
  cod_centro         INTEGER       NOT NULL,
  quantidade         NUMERIC(14,4) NULL,
  justificativa      VARCHAR(160)  NULL,
  timestamp          TIMESTAMP     NULL DEFAULT ('now'::text)::timestamp(3) WITH time zone,
  CONSTRAINT pk_inventario_itens PRIMARY KEY (exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro),
  CONSTRAINT fk_inventario_itens_1 FOREIGN KEY(cod_item, cod_marca, cod_almoxarifado, cod_centro) REFERENCES almoxarifado.estoque_material(cod_item, cod_marca, cod_almoxarifado, cod_centro),
  CONSTRAINT fk_inventario_itens_2 FOREIGN KEY(exercicio, cod_almoxarifado, cod_inventario) REFERENCES almoxarifado.inventario (exercicio, cod_almoxarifado, cod_inventario)
);

CREATE TABLE almoxarifado.inventario_anulacao (
  exercicio          CHAR(4)      NOT NULL,
  cod_almoxarifado   INTEGER      NOT NULL,
  cod_inventario     INTEGER      NOT NULL,
  timestamp          TIMESTAMP    NULL DEFAULT ('now'::text)::timestamp(3) WITH time zone,
  motivo             VARCHAR(160) NULL,
  CONSTRAINT pk_inventario_anulacao PRIMARY KEY(exercicio, cod_almoxarifado, cod_inventario),
  CONSTRAINT fk_inventario_anulacao_1 FOREIGN KEY (exercicio, cod_almoxarifado, cod_inventario) REFERENCES almoxarifado.inventario(exercicio, cod_almoxarifado, cod_inventario)
);

CREATE TABLE almoxarifado.lancamento_inventario_itens (
  exercicio         CHAR(4) NOT NULL,
  cod_almoxarifado  INTEGER NOT NULL,
  cod_inventario    INTEGER NOT NULL,
  cod_item          INTEGER NOT NULL,
  cod_marca         INTEGER NOT NULL,
  cod_centro        INTEGER NOT NULL,
  cod_lancamento    INTEGER NOT NULL,
  CONSTRAINT pk_lancamento_inventario_itens PRIMARY KEY(exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro),
  CONSTRAINT fk_lancamento_inventario_itens_1 FOREIGN KEY (exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro) REFERENCES almoxarifado.inventario_itens( exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro),
  CONSTRAINT fk_lancamento_inventario_itens_2 FOREIGN KEY (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro)            REFERENCES almoxarifado.lancamento_material (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro)
);

CREATE TABLE almoxarifado.catalogo_classificacao_bloqueio (
  cod_classificacao  INTEGER NOT NULL,
  cod_catalogo       INTEGER NOT NULL,
  cod_almoxarifado   INTEGER NOT NULL,
  exercicio          CHAR(4) NOT NULL,
  cod_inventario     INTEGER NOT NULL,
  CONSTRAINT pk_catalogo_classificacao_bloqueio PRIMARY KEY(cod_classificacao, cod_catalogo, cod_almoxarifado),
  CONSTRAINT fk_catalogo_classificacao_bloqueio_1 FOREIGN KEY (exercicio, cod_almoxarifado, cod_inventario) REFERENCES almoxarifado.inventario (exercicio, cod_almoxarifado, cod_inventario),
  CONSTRAINT fk_catalogo_classificacao_bloqueio_2 FOREIGN KEY (cod_classificacao, cod_catalogo) REFERENCES almoxarifado.catalogo_classificacao(cod_classificacao, cod_catalogo)
);

GRANT INSERT, UPDATE, SELECT, DELETE ON  almoxarifado.inventario TO GROUP urbem;
GRANT INSERT, UPDATE, SELECT, DELETE ON  almoxarifado.inventario_itens TO GROUP urbem;
GRANT INSERT, UPDATE, SELECT, DELETE ON  almoxarifado.inventario_anulacao TO GROUP urbem;
GRANT INSERT, UPDATE, SELECT, DELETE ON  almoxarifado.lancamento_inventario_itens TO GROUP urbem;
GRANT INSERT, UPDATE, SELECT, DELETE ON  almoxarifado.catalogo_classificacao_bloqueio TO GROUP urbem;

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 397
          , 29
          , 'Inventário'
          , 'instancias/inventario/'
          , 14
    WHERE 0 = (SELECT count(*)
                 FROM administracao.funcionalidade
                WHERE cod_funcionalidade = 397);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2045
          , 397
          , 'FMManterInventario.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Inventário');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2044
          , 397
          , 'FLManterInventario.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Inventário');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2042
          , 397
          , 'FLManterInventario.php'
          , 'anular'
          , 3
          , ''
          , 'Anular Inventário');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2043
          , 397
          , 'FLManterInventario.php'
          , 'processar'
          , 4
          , ''
          , 'Processar Inventário');


--------------
-- gp_almox_transferencia
--------------

-- uc_03-03-08

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 306
          , 29
          , 'Nota de Transferência'
          , 'instancias/notaTransferencia/'
          , 13
      WHERE  0 = (SELECT COUNT(1)
                    FROM administracao.funcionalidade
                   WHERE cod_funcionalidade = 306);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1461
          , 306
          , 'FMManterNotaTransferencia.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Nota de Transferência');

INSERT INTO administracao.acao
           (cod_acao
         , cod_funcionalidade
         , nom_arquivo
         , parametro
         , ordem
         , complemento_acao
         , nom_acao)
    VALUES (1462
         , 306
         , 'FLManterNotaTransferencia.php'
         , 'alterar'
         , 2
         ,  ''
         , 'Alterar Nota de Transferência');

INSERT INTO administracao.acao
           (cod_acao
         , cod_funcionalidade
         , nom_arquivo
         , parametro
         , ordem
         , complemento_acao
         , nom_acao)
    VALUES (1463
         , 306
         , 'FLManterNotaTransferencia.php'
         , 'anular'
         , 3
         , ''
         , 'Anular Nota de Transferência');



-- Altera tabela almoxarifado.pedido_transferencia_itens / almoxarifado.pedido_transferencia_item

   ALTER TABLE  almoxarifado.pedido_transferencia_itens      RENAME TO  pedido_transferencia_item;
   ALTER TABLE  almoxarifado.pedido_transferencia_item       DROP CONSTRAINT  uk_pedido_transferencia_itens_1;
   ALTER TABLE  almoxarifado.pedido_transferencia_item       ADD  CONSTRAINT  uk_pedido_transferencia_item          UNIQUE      (exercicio, cod_transferencia);
   ALTER TABLE  almoxarifado.transferencia_almoxarifado_item DROP CONSTRAINT fk_transferencia_almoxarifado_item_1;
   ALTER TABLE  almoxarifado.pedido_transferencia_item       DROP CONSTRAINT pk_pedido_transferencia_itens;
   ALTER TABLE  almoxarifado.pedido_transferencia_item       ADD  CONSTRAINT pk_pedido_transferencia_item           PRIMARY KEY (exercicio, cod_transferencia, cod_item, cod_marca, cod_almoxarifado, cod_centro);
   ALTER TABLE  almoxarifado.transferencia_almoxarifado_item ADD  CONSTRAINT fk_transferencia_almoxarifado_item_1   FOREIGN KEY (cod_item, cod_marca, cod_almoxarifado, cod_centro, exercicio, cod_transferencia) REFERENCES almoxarifado.pedido_transferencia_item (cod_item, cod_marca, cod_almoxarifado, cod_centro, exercicio, cod_transferencia);
   ALTER TABLE  almoxarifado.pedido_transferencia_item       DROP CONSTRAINT fk_pedido_transferencia_itens_1;
   ALTER TABLE  almoxarifado.pedido_transferencia_item       DROP CONSTRAINT fk_pedido_transferencia_itens_2;
   ALTER TABLE  almoxarifado.pedido_transferencia_item       ADD  CONSTRAINT fk_pedido_transferencia_item_1         FOREIGN KEY (cod_item, cod_marca, cod_almoxarifado, cod_centro)  REFERENCES almoxarifado.estoque_material     (cod_item, cod_marca, cod_almoxarifado, cod_centro);
   ALTER TABLE  almoxarifado.pedido_transferencia_item       ADD  CONSTRAINT fk_pedido_transferencia_item_2         FOREIGN KEY (cod_transferencia, exercicio)                       REFERENCES almoxarifado.pedido_transferencia (cod_transferencia, exercicio);

-- Inclusão do almoxarifado destino na tabela pedido_transferencia

ALTER TABLE almoxarifado.pedido_transferencia RENAME COLUMN cod_almoxarifado TO cod_almoxarifado_origem;
ALTER TABLE almoxarifado.pedido_transferencia ADD COLUMN cod_almoxarifado_destino INTEGER NOT NULL;
ALTER TABLE almoxarifado.pedido_transferencia ADD CONSTRAINT fk_pedido_transferencia_3 FOREIGN KEY (cod_almoxarifado_destino) REFERENCES almoxarifado.almoxarifado(cod_almoxarifado);

ALTER TABLE almoxarifado.transferencia_almoxarifado_item DROP COLUMN tipo_transferencia;
ALTER TABLE almoxarifado.pedido_transferencia_item DROP COLUMN tipo_transferencia;
ALTER TABLE almoxarifado.transferencia_almoxarifado_item ADD CONSTRAINT pk_transferencia_almoxarifado_item PRIMARY KEY (exercicio, cod_transferencia,  cod_item, cod_marca, cod_almoxarifado, cod_centro);

ALTER TABLE almoxarifado.pedido_transferencia_item DROP CONSTRAINT uk_pedido_transferencia_item;


------------- Retirado o relacionamento entre as tabelas almoxarifado.pedido_transferencia_itens
---- e almoxarifado.transferencia_almoxarifado_item

ALTER TABLE almoxarifado.pedido_transferencia_item     DROP CONSTRAINT fk_pedido_transferencia_item_1;
ALTER TABLE almoxarifado.transferencia_almoxarifado_item DROP CONSTRAINT fk_transferencia_almoxarifado_item_1;
ALTER TABLE almoxarifado.pedido_transferencia_item     DROP CONSTRAINT pk_pedido_transferencia_item;
ALTER TABLE almoxarifado.pedido_transferencia_item     DROP COLUMN     cod_item;
ALTER TABLE almoxarifado.pedido_transferencia_item     DROP COLUMN      cod_marca;
ALTER TABLE almoxarifado.pedido_transferencia_item     DROP COLUMN      cod_almoxarifado;
ALTER TABLE almoxarifado.pedido_transferencia_item     DROP COLUMN      cod_centro;
ALTER TABLE almoxarifado.transferencia_almoxarifado_item DROP CONSTRAINT   pk_transferencia_almoxarifado_item;
ALTER TABLE almoxarifado.pedido_transferencia_item     ADD CONSTRAINT    pk_pedido_transferencia_item PRIMARY KEY(exercicio, cod_transferencia);
ALTER TABLE almoxarifado.transferencia_almoxarifado_item ADD CONSTRAINT    fk_transferencia_almoxarifado_item_1 FOREIGN KEY (exercicio, cod_transferencia) REFERENCES almoxarifado.pedido_transferencia_item(exercicio, cod_transferencia);
ALTER TABLE almoxarifado.transferencia_almoxarifado_item ADD CONSTRAINT pk_transferencia_almoxarifado_item PRIMARY KEY (exercicio, cod_transferencia, cod_item, cod_marca, cod_almoxarifado, cod_centro, cod_lancamento);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1466
          , 290
          , 'FLMovimentacaoTransferencia.php'
          , 'entrada'
          ,10
          ,''
          ,'Entrada por Transferência') ;

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1467
          , 291
          , 'FLMovimentacaoTransferencia.php'
          , 'saida'
          , 10
          , ''
          , 'Saída por Transferência') ;


-- Incluída a coluna cod_lancamento na pk da tabela transferencia_almoxarifado_item.
   ALTER TABLE almoxarifado.transferencia_almoxarifado_item DROP CONSTRAINT pk_transferencia_almoxarifado_item;
   ALTER TABLE almoxarifado.transferencia_almoxarifado_item Add CONSTRAINT pk_transferencia_almoxarifado_item PRIMARY KEY (exercicio, cod_transferencia, cod_item, cod_marca, cod_almoxarifado, cod_centro, cod_lancamento);

--------------
-- Ticket 12433
--------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2234
          , 326
          , 'FLManterEdital.php'
          , 'imprimir'
          , 7
          , ''
          , 'Reemitir Edital');

UPDATE administracao.funcionalidade set ordem = 1  WHERE cod_funcionalidade = 269;
UPDATE administracao.funcionalidade set ordem = 2  WHERE cod_funcionalidade = 263;
UPDATE administracao.funcionalidade set ordem = 3  WHERE cod_funcionalidade = 268;
UPDATE administracao.funcionalidade set ordem = 4  WHERE cod_funcionalidade = 278;
UPDATE administracao.funcionalidade set ordem = 6  WHERE cod_funcionalidade = 277;
UPDATE administracao.funcionalidade set ordem = 9  WHERE cod_funcionalidade = 288;
UPDATE administracao.funcionalidade set ordem = 5  WHERE cod_funcionalidade = 289;
UPDATE administracao.funcionalidade set ordem = 8  WHERE cod_funcionalidade = 290;
UPDATE administracao.funcionalidade set ordem = 10 WHERE cod_funcionalidade = 291;
UPDATE administracao.funcionalidade set ordem = 7  WHERE cod_funcionalidade = 311;

----------------
-- Ticket #12801
----------------

DROP TABLE almoxarifado.transferencia_almoxarifado_item;
DROP TABLE almoxarifado.pedido_transferencia_item;

CREATE TABLE almoxarifado.pedido_transferencia_item (
  exercicio          char(4)       NOT NULL,
  cod_transferencia  integer       NOT NULL,
  cod_item           integer       NOT NULL,
  cod_marca          integer       NOT NULL,
  cod_centro         integer       NOT NULL,
  quantidade         numeric(14,4) NULL,
  CONSTRAINT pk_pedido_transferencia_item PRIMARY KEY(exercicio, cod_transferencia, cod_item, cod_marca, cod_centro),
  CONSTRAINT fk_pedido_transferencia_item_1 FOREIGN KEY(cod_transferencia, exercicio) REFERENCES almoxarifado.pedido_transferencia(cod_transferencia, exercicio),
  CONSTRAINT fk_pedido_transferencia_item_2 FOREIGN KEY(cod_item) REFERENCES almoxarifado.catalogo_item(cod_item),
  CONSTRAINT fk_pedido_transferencia_item_3 FOREIGN KEY(cod_marca) REFERENCES almoxarifado.marca(cod_marca),
  CONSTRAINT fk_pedido_transferencia_item_4 FOREIGN KEY(cod_centro) REFERENCES almoxarifado.centro_custo(cod_centro)
);

CREATE TABLE almoxarifado.atributo_pedido_transferencia_item (
  exercicio          char(4)       NOT NULL,
  cod_transferencia  integer       NOT NULL,
  cod_sequencial     integer       NOT NULL,
  cod_item           integer       NOT NULL,
  cod_marca          integer       NOT NULL,
  cod_centro         integer       NOT NULL,
  quantidade         numeric(14,4) NOT NULL,
  CONSTRAINT pk_atributo_pedido_transferencia_item PRIMARY KEY(exercicio, cod_transferencia, cod_sequencial, cod_item, cod_marca, cod_centro),
  CONSTRAINT fk_atributo_pedido_transferencia_item_1   FOREIGN KEY(exercicio, cod_transferencia, cod_item, cod_marca, cod_centro) REFERENCES almoxarifado.pedido_transferencia_item(exercicio, cod_transferencia, cod_item, cod_marca, cod_centro)
);

CREATE TABLE almoxarifado.atributo_pedido_transferencia_item_valor (
  exercicio          char(4) NOT NULL,
  cod_transferencia  integer NOT NULL,
  cod_item           integer NOT NULL,
  cod_sequencial     integer NOT NULL,
  cod_modulo         integer NOT NULL,
  cod_cadastro       integer NOT NULL,
  cod_atributo       integer NOT NULL,
  cod_marca          integer NOT NULL,
  cod_centro         integer NOT NULL,
  valor              text    NOT NULL,
  CONSTRAINT pk_atributo_pedido_transferencia_item_valor PRIMARY KEY(exercicio, cod_transferencia, cod_item, cod_sequencial, cod_modulo, cod_cadastro, cod_atributo, cod_marca, cod_centro),
  CONSTRAINT fk_atributo_pedido_transferencia_item_valor_1 FOREIGN KEY(cod_sequencial, cod_transferencia, exercicio, cod_item, cod_marca, cod_centro) REFERENCES almoxarifado.atributo_pedido_transferencia_item(cod_sequencial, cod_transferencia, exercicio, cod_item, cod_marca, cod_centro),
  CONSTRAINT fk_atributo_pedido_transferencia_item_valor_2 FOREIGN KEY(cod_item, cod_atributo, cod_cadastro, cod_modulo) REFERENCES almoxarifado.atributo_catalogo_item(cod_item, cod_atributo, cod_cadastro, cod_modulo)
);

CREATE TABLE almoxarifado.transferencia_almoxarifado_item (
  cod_item           integer NOT NULL,
  cod_marca          integer NOT NULL,
  cod_almoxarifado   integer NOT NULL,
  cod_centro         integer NOT NULL,
  cod_lancamento     integer NOT NULL,
  cod_transferencia  integer NOT NULL,
  exercicio          char(4) NOT NULL,
  CONSTRAINT pk_transferencia_almoxarifado_item PRIMARY KEY(cod_item, cod_marca, cod_almoxarifado, cod_centro, cod_lancamento, cod_transferencia, exercicio),
  CONSTRAINT fk_transferencia_almoxarifado_item_1 FOREIGN KEY(cod_lancamento, cod_item, cod_centro, cod_marca, cod_almoxarifado) REFERENCES almoxarifado.lancamento_material(cod_lancamento, cod_item, cod_centro, cod_marca, cod_almoxarifado),
  CONSTRAINT fk_transferencia_almoxarifado_item_2 FOREIGN KEY(exercicio, cod_transferencia, cod_item, cod_marca, cod_centro) REFERENCES almoxarifado.pedido_transferencia_item(exercicio, cod_transferencia, cod_item, cod_marca, cod_centro)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON almoxarifado.pedido_transferencia_item TO GROUP urbem;
GRANT INSERT, DELETE, UPDATE, SELECT ON almoxarifado.atributo_pedido_transferencia_item TO GROUP urbem;
GRANT INSERT, DELETE, UPDATE, SELECT ON almoxarifado.atributo_pedido_transferencia_item_valor TO GROUP urbem;
GRANT INSERT, DELETE, UPDATE, SELECT ON almoxarifado.transferencia_almoxarifado_item TO GROUP urbem;


----------------
-- Ticket #11296
----------------

UPDATE administracao.acao 
   SET ordem = ordem + 1 
 WHERE cod_funcionalidade = 326 
   AND ordem > 3;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2378
          , 326
          , 'FLManterProcessoLicitatorio.php'
          , 'consultar'
          , 4
          , ''
          , 'Consultar Processo Licitatório'
          );


----------------
-- Ticket #13384
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2317
          , 24
          , 'FLModificarResponsavel.php'
          , 'rescindir'
          , 10
          , ''
          , 'Modificar Responsável'
          );


----------------
-- Ticket #13525
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2324
          , 326
          , 'FLManterJulgamentoProposta.php'
          , 'reemitir'
          , 55
          , ''
          , 'Reemitir Julgamento das Propostas'
          );

----------------
-- Ticket #13526
----------------

UPDATE administracao.acao
   SET ordem              = ordem + 1
 WHERE cod_funcionalidade = 356
   AND ordem > 6;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2325
          , 356
          , 'FLManterJulgamento.php'
          , 'reemitir'
          , 7
          , ''
          , 'Reemitir Julgamento das Propostas'
          );


----------------
-- Ticket #13564
----------------

CREATE TABLE licitacao.contrato_licitacao(
    num_contrato            INTEGER         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    cod_licitacao           INTEGER         NOT NULL,
    cod_modalidade          INTEGER         NOT NULL,
    CONSTRAINT pk_contrato_licitacao        PRIMARY KEY                      (num_contrato,cod_entidade, exercicio),
    CONSTRAINT fk_contrato_licitacao_1      FOREIGN KEY                      (num_contrato,cod_entidade, exercicio)
                                            REFERENCES licitacao.contrato    (num_contrato,cod_entidade, exercicio),
    CONSTRAINT fk_contrato_licitacao_2      FOREIGN KEY                      (cod_licitacao, cod_modalidade, cod_entidade, exercicio)
                                            REFERENCES licitacao.licitacao   (cod_licitacao, cod_modalidade, cod_entidade, exercicio)
 );

CREATE TABLE licitacao.contrato_compra_direta(
    num_contrato            INTEGER         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    cod_compra_direta       INTEGER         NOT NULL,
    cod_modalidade          INTEGER         NOT NULL,
    CONSTRAINT pk_contrato_compra_direta    PRIMARY KEY                      (num_contrato, cod_entidade, exercicio),
    CONSTRAINT fk_contrato_compra_direta_1  FOREIGN KEY                      (num_contrato, cod_entidade, exercicio)
                                            REFERENCES licitacao.contrato    (num_contrato, cod_entidade, exercicio),
    CONSTRAINT fk_contrato_compra_direta_2  FOREIGN KEY                      (cod_compra_direta, cod_entidade, exercicio, cod_modalidade)
                                            REFERENCES compras.compra_direta (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade)
 );

GRANT ALL ON licitacao.contrato_licitacao       TO GROUP urbem;
GRANT ALL ON licitacao.contrato_compra_direta   TO GROUP urbem;

ALTER TABLE licitacao.publicacao_contrato DROP CONSTRAINT fk_publicacao_contrato_3;
ALTER TABLE licitacao.publicacao_contrato DROP COLUMN     cod_licitacao;
ALTER TABLE licitacao.publicacao_contrato DROP COLUMN     cod_modalidade;

INSERT  
  INTO licitacao.contrato_licitacao 
SELECT num_contrato
     , cod_entidade
     , exercicio
     , cod_licitacao
     , cod_modalidade 
  FROM licitacao.contrato;

ALTER TABLE licitacao.contrato DROP CONSTRAINT fk_contrato_5;
ALTER TABLE licitacao.contrato DROP COLUMN cod_licitacao;
ALTER TABLE licitacao.contrato DROP COLUMN cod_modalidade;

    -------------------------------------------------------------
    -- adicionando acoes p/ cadastro de contrato - modulo COMPRAS
    -------------------------------------------------------------

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 428
         , 35
         , 'Contrato'
         , 'instancias/contrato/'
         , 35
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2334
          , 428
          , 'FMManterContrato.php'
          , 'incluirCD'
          , 1
          , ''
          , 'Incluir Contrato'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2335
          , 428
          , 'FLManterContrato.php'
          , 'alterarCD'
          , 2
          , ''
          , 'Alterar Contrato'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2336
          , 428
          , 'FLManterContrato.php'
          , 'anularCD'
          , 3
          , ''
          , 'Anular Contrato'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2337
          , 428
          , 'FLManterContrato.php'
          , 'rescindir'
          , 4
          , ''
          , 'Rescindir Contrato'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2338
          , 428
          , 'FLManterAditivoContrato.php'
          , 'incluirCD'
          , 5
          , ''
          , 'Incluir Aditivo'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2339
          , 428
          , 'FLManterAditivoContrato.php'
          , 'alterarCD'
          , 6
          , ''
          , 'Alterar Aditivo'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2340
          , 428
          , 'FLManterAditivoContrato.php'
          , 'anularCD'
          , 7
          , ''
          , 'Anular Aditivo'
          );


----------------
-- Ticket #12723
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 35
         , 4
         , 'Mapa de Compra'
         , 'mapaCompra.rptdesign'
         );


----------------
-- Ticket #11315
----------------

INSERT 
  INTO administracao.configuracao 
     ( exercicio
     , cod_modulo
     , parametro
     , valor ) 
VALUES ( 2008
     , 35
     , 'numeracao_automatica'
     , 't'
     );


----------------
-- Ticket #13736
----------------

-- ALTER TABLE almoxarifado.catalogo ADD COLUMN importado BOOLEAN DEFAULT FALSE NOT NULL;


----------------
-- Ticket #13798
----------------

ALTER TABLE compras.cotacao_fornecedor_item_desclassificacao ALTER  COLUMN timestamp SET DEFAULT ('now'::text)::timestamp(3) with time zone;

ALTER TABLE compras.julgamento ALTER  COLUMN timestamp SET DEFAULT ('now'::text)::timestamp(3) with time zone;
ALTER TABLE compras.julgamento ALTER  COLUMN timestamp SET NOT NULL;


----------------
-- Ticket #13810
----------------

CREATE TABLE almoxarifado.atributo_inventario_item_valor (
    exercicio               CHAR(4)                 NOT NULL,
    cod_almoxarifado        INTEGER                 NOT NULL,
    cod_inventario          INTEGER                 NOT NULL,
    cod_item                INTEGER                 NOT NULL,
    cod_marca               INTEGER                 NOT NULL,
    cod_centro              INTEGER                 NOT NULL,
    cod_modulo              INTEGER                 NOT NULL,
    cod_cadastro            INTEGER                 NOT NULL,
    cod_atributo            INTEGER                 NOT NULL,
    valor                   TEXT                    NOT NULL DEFAULT '',
    CONSTRAINT pk_atributo_inventario_item_valor    PRIMARY KEY (exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro, cod_modulo, cod_cadastro, cod_atributo),
    CONSTRAINT fk_atributo_inventario_item_valor_1  FOREIGN KEY                                     (exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro)
                                                    REFERENCES almoxarifado.inventario_itens        (exercicio, cod_almoxarifado, cod_inventario, cod_item, cod_marca, cod_centro),
    CONSTRAINT fk_atributo_inventario_item_valor_2  FOREIGN KEY                                     (cod_modulo, cod_cadastro, cod_atributo, cod_item)
                                                    REFERENCES almoxarifado.atributo_catalogo_item  (cod_modulo, cod_cadastro, cod_atributo, cod_item)
);

GRANT ALL ON almoxarifado.atributo_inventario_item_valor TO GROUP urbem;


----------------
-- Ticket #10788
----------------

CREATE TABLE almoxarifado.lancamento_material_estorno (
    cod_lancamento_estorno  integer NOT NULL,
    cod_lancamento          integer NOT NULL,
    cod_almoxarifado        integer NOT NULL,
    cod_item                integer NOT NULL,
    cod_marca               integer NOT NULL,
    cod_centro              integer NOT NULL,
    CONSTRAINT pk_lancamento_material_estorno   PRIMARY KEY (cod_lancamento_estorno, cod_lancamento, cod_almoxarifado, cod_item, cod_marca, cod_centro),
    CONSTRAINT fk_lancamento_material_estorno_1 FOREIGN KEY                                 (cod_lancamento_estorno, cod_almoxarifado, cod_item, cod_marca, cod_centro)
                                                REFERENCES almoxarifado.lancamento_material (cod_lancamento, cod_almoxarifado, cod_item, cod_marca, cod_centro),
    CONSTRAINT fk_lancamento_material_estorno_2 FOREIGN KEY                                 (cod_lancamento, cod_almoxarifado, cod_item, cod_marca, cod_centro)
                                                REFERENCES almoxarifado.lancamento_material (cod_lancamento, cod_almoxarifado, cod_item, cod_marca, cod_centro)
);

GRANT ALL ON almoxarifado.lancamento_material_estorno TO GROUP urbem;

DELETE FROM administracao.auditoria where cod_acao = 1701;
DELETE FROM administracao.permissao where cod_acao = 1701;
DELETE FROM administracao.acao where cod_acao = 1701;


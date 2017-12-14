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
* $Id: GP_1930.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.93.0
*/

----------------
-- Ticket #12844
----------------

CREATE TABLE licitacao.ata(
    id          INTEGER         NOT NULL,
    num_edital      INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    num_ata         INTEGER         NOT NULL,
    exercicio_ata   CHAR(4)         NOT NULL,
    timestamp       TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    descricao       TEXT            NOT NULL,
    CONSTRAINT pk_ata               PRIMARY KEY                     (id),
    CONSTRAINT fk_ata_1             FOREIGN KEY                     (num_edital, exercicio)
                                    REFERENCES licitacao.edital     (num_edital, exercicio),
    CONSTRAINT uk_ata_1             UNIQUE (num_ata, exercicio_ata)
 );

GRANT ALL ON licitacao.ata TO GROUP urbem;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2460
          , 326
          , 'FMManterAta.php'
          , 'incluir'
          , 70
          , ''
          , 'Incluir Ata de Encerramento'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2461
          , 326
          , 'FLManterAta.php'
          , 'alterar'
          , 71
          , ''
          , 'Alterar Ata de Encerramento'
          );

----------------
-- Ticket #12654
----------------

UPDATE administracao.funcionalidade
   SET ordem = 8
 WHERE cod_funcionalidade = 28
   AND cod_modulo = 6;

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 447
         , 6
         , 'Inventário'
         , 'instancias/inventario/'
         , 7
         );


INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2403
          , 447
          , 'FMManterInventario.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2404
          , 447
          , 'FLManterInventario.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2405
          , 447
          , 'FLManterInventario.php'
          , 'anular'
          , 3
          , ''
          , 'Anular Inventário'
          );

INSERT INTO administracao.acao     ----------------
              ( cod_acao           -- Ticket #12654
              , cod_funcionalidade ----------------
              , nom_arquivo 
              , parametro 
              , ordem 
              , complemento_acao 
              , nom_acao ) 
         VALUES ( 2713
              , 447 
              , 'FLManterInventario.php' 
              , 'processar' 
              , 4 
              , '' 
              , 'Processar Inventário' 
              ); 

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2406
          , 447
          , 'FLManterInventario.php'
          , 'abertura'
          , 5
          , ''
          , 'Termo de Abertura'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2407
          , 447
          , 'FLManterInventario.php'
          , 'inventario'
          , 6
          , ''
          , 'Relatório do Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2408
          , 447
          , 'FLManterInventario.php'
          , 'historico'
          , 7
          , ''
          , 'Histórico do Inventário'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2409
          , 447
          , 'FLManterInventario.php'
          , 'encerramento'
          , 8
          , ''
          , 'Termo de Encerramento'
          );

INSERT INTO compras.modalidade(cod_modalidade, descricao) VALUES (4, 'Leilão');
INSERT INTO compras.modalidade(cod_modalidade, descricao) VALUES (6, 'Pregão Presencial');
INSERT INTO compras.modalidade(cod_modalidade, descricao) VALUES (7, 'Pregão Eletrônico');

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 6
         , 7
         , 'Termo de Abertura do Inventário'
         , 'termoAberturaInventario.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 6
         , 8
         , 'Termo de Encerramento do Inventário'
         , 'termoEncerramentoInventario.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 6
         , 9
         , 'Relatório do Inventário'
         , 'relatorioInventario.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 6
         , 10
         , 'Histórico do Inventário'
         , 'relatorioHistoricoInventario.rptdesign'
         );


CREATE TABLE patrimonio.inventario (
    exercicio           CHAR(4)         NOT NULL,
    id_inventario       INTEGER         NOT NULL,
    dt_inicio           DATE            NOT NULL,
    dt_fim              DATE                    ,
    observacao          TEXT                    ,
    processado          BOOLEAN         NOT NULL    DEFAULT FALSE,
    CONSTRAINT pk_inventario            PRIMARY KEY (exercicio, id_inventario)
);

GRANT ALL ON patrimonio.inventario TO GROUP urbem;

CREATE TABLE patrimonio.inventario_especie (
    exercicio           CHAR(4)         NOT NULL,
    id_inventario       INTEGER         NOT NULL,
    cod_especie         INTEGER         NOT NULL,
    cod_grupo           INTEGER         NOT NULL,
    cod_natureza        INTEGER         NOT NULL,
    processado          BOOLEAN         NOT NULL    DEFAULT FALSE,
    CONSTRAINT pk_inventario_especie    PRIMARY KEY                         (exercicio, id_inventario, cod_especie, cod_grupo, cod_natureza),
    CONSTRAINT fk_inventario_especie_1  FOREIGN KEY                         (exercicio, id_inventario)
                                        REFERENCES patrimonio.inventario    (exercicio, id_inventario),
    CONSTRAINT fk_inventario_especie_2  FOREIGN KEY                         (cod_especie, cod_grupo, cod_natureza)
                                        REFERENCES patrimonio.especie       (cod_especie, cod_grupo, cod_natureza)
);

GRANT ALL ON patrimonio.inventario_especie TO GROUP urbem;

CREATE TABLE patrimonio.inventario_historico_bem (
    exercicio           CHAR(4)         NOT NULL,
    id_inventario       INTEGER         NOT NULL,
    cod_bem             INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_situacao        INTEGER         NOT NULL,
    cod_local           INTEGER         NOT NULL,
    cod_orgao           INTEGER         NOT NULL,
    descricao           VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_inventario_historico_bem    PRIMARY KEY                           (exercicio, id_inventario, cod_bem, timestamp),
    CONSTRAINT fk_inventario_historico_bem_1  FOREIGN KEY                           (exercicio, id_inventario)
                                              REFERENCES patrimonio.inventario      (exercicio, id_inventario),
    CONSTRAINT fk_inventario_historico_bem_2  FOREIGN KEY                           (cod_bem, timestamp)
                                              REFERENCES patrimonio.historico_bem   (cod_bem, timestamp),
    CONSTRAINT fk_inventario_historico_bem_3  FOREIGN KEY                           (cod_situacao)
                                              REFERENCES patrimonio.situacao_bem    (cod_situacao),
    CONSTRAINT fk_inventario_historico_bem_4  FOREIGN KEY                           (cod_local)
                                              REFERENCES organograma.local          (cod_local),
    CONSTRAINT fk_inventario_historico_bem_5  FOREIGN KEY                           (cod_orgao)
                                              REFERENCES organograma.orgao          (cod_orgao),
    CONSTRAINT uk_inventario_historico_bem_1  UNIQUE                                (exercicio, id_inventario, cod_bem)

);

GRANT ALL ON patrimonio.inventario_historico_bem TO GROUP urbem;

CREATE TABLE patrimonio.inventario_anulacao (
    exercicio           CHAR(4)         NOT NULL,
    id_inventario       INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    observacao          TEXT                    ,
    CONSTRAINT pk_inventario_anulacao   PRIMARY KEY                         (exercicio, id_inventario),
    CONSTRAINT fk_inventario_anulacao_1 FOREIGN KEY                         (exercicio, id_inventario)
                                        REFERENCES patrimonio.inventario    (exercicio, id_inventario)
);

GRANT ALL ON patrimonio.inventario_anulacao TO GROUP urbem;


----------------
-- Ticket #15373
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2731
          , 278
          , 'FLManterItem.php'
          , 'consultar'
          , 10
          , ''
          , 'Consultar Item'
          );


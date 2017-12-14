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
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* Versão 001.
*/


/* Aguardando liberacao

-- Inclusao da funcionalidade 'Configuracao'

   INSERT INTO administracao.modulo ( cod_modulo, cod_responsavel, nom_modulo, nom_diretorio, ordem, cod_gestao )
        SELECT 29,  0, 'Almoxarifado', 'almoxarifado/',  10, 3
         WHERE 0  = ( SELECT Count(1)
                        FROM administracao.modulo
                       WHERE cod_modulo = 29 );

   INSERT INTO administracao.funcionalidade ( cod_funcionalidade, cod_modulo, nom_funcionalidade, nom_diretorio, ordem )
        SELECT  279, 29, 'Configuração', 'instancias/configuracao/',0
         WHERE 0  = ( SELECT Count(1)
                        FROM administracao.funcionalidade
                       WHERE cod_funcionalidade = 279 );

   UPDATE administracao.funcionalidade SET ordem = 1
    WHERE cod_funcionalidade = 269
      AND cod_modulo = 29;

   UPDATE administracao.funcionalidade SET ordem = 2
    WHERE cod_funcionalidade = 263
      AND cod_modulo = 29;

   INSERT INTO administracao.acao (cod_acao, cod_funcionalidade, nom_acao, nom_arquivo, parametro, ordem, complemento_acao)
        VALUES (1361, 279, 'Importação de Catálogos', 'FMManterImportacaoCatalogo.php','importar', 1, 'Importação de Catálogo');
*/

--
-- Escluída e comentada criação  Ação: Saída por Estorno de Entrada
-- http://trac.urbem/ticket/10902
--
/*
   insert into administracao.acao( cod_acao
                                 , cod_funcionalidade
                                 , nom_arquivo,parametro
                                 , ordem
                                 , complemento_acao
                                 , nom_acao)
                          values ( 2157
                                 , 291
                                 , 'FLEstornoEntrada.php'
                                 , 'saida'
                                 , 20
                                 , ''
                                 , 'Saída por Estorno de Entrada');
*/


--
-- Aguardando liberacao.
--
/*
INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1829
          , 290
          , 'FLMovimentacaoOrdemCompra.php'
          , 'incluir'
          , 5
          , ''
          , 'Entrada com Ordem de Compra');
*/

--
--
--
/*
  =============================================================================================
  Aguardando liberação de versão, não excluir
   -- INSERT INTO compras.modalidade(cod_modalidade, descricao) VALUES (4, 'Leilão');
   -- INSERT INTO compras.modalidade(cod_modalidade, descricao) VALUES (6, 'Pregão Presencial');
   -- INSERT INTO compras.modalidade(cod_modalidade, descricao) VALUES (7, 'Pregão Eletrônico');
  =============================================================================================

*/

--
--
--
/* Ações do objeto foram migradas para a funcionalidade configuração

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 313
          , 35
          , 'Objeto'
          , 'instancias/objeto/'
          , 10
      WHERE 0 = (SELECT COUNT(1)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 313);

*/


--
--
--
/*
   -- Criação da Funcionalidade Nota Fiscal.

   INSERT INTO administracao.funcionalidade
               (cod_funcionalidade
             , cod_modulo
             , nom_funcionalidade
             , nom_diretorio
             , ordem)
        SELECT 352
             , 35
             , 'Nota de Compra'
             , 'instancias/notaCompra/'
             , 24
         WHERE 0 = (SELECT COUNT(1)
                      FROM administracao.funcionalidade
                     WHERE cod_funcionalidade = 352);


   INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES (1697
             , 352
             , 'FMManterNotaCompra.php'
             , 'incluir'
             , 1
             , ''
             , 'Incluir');

   INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES (1698
             , 352
             , 'FLManterNotaCompra.php'
             , 'consultar'
             , 2
             , ''
             , 'Consultar');

   INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES (1699
             , 352
             , 'LSManterNotaCompra.php'
             , 'excluir'
             , 3
             , ''
             , 'Excluir');
*/


--
-- Ultimas atualizações realizadas por hboaventura scrip uc_03-02-00
--

--CREATE TABLE frota.motorista (
--  cgm_motorista integer NOT NULL,
--  ativo         boolean NOT NULL,
--  CONSTRAINT pk_motorista PRIMARY KEY(cgm_motorista),
--  CONSTRAINT fk_motorista_1 FOREIGN KEY(cgm_motorista) REFERENCES sw_cgm(numcgm)
--);

--CREATE TABLE frota.tipo_item (
--  cod_tipo  INTEGER     NOT NULL,
--  descricao VARCHAR(40) NULL,
--  CONSTRAINT pk_tipo_item PRIMARY KEY(cod_tipo)
--);
--
--CREATE TABLE frota.item (
--  cod_item INTEGER NOT NULL,
--  cod_tipo INTEGER NOT NULL,
--  CONSTRAINT pk_item PRIMARY KEY(cod_item),
--  CONSTRAINT fk_item_1 FOREIGN KEY(cod_item) REFERENCES almoxarifado.catalogo_item(cod_item),
--  CONSTRAINT fk_item_2 FOREIGN KEY(cod_tipo) REFERENCES frota.tipo_item(cod_tipo)
--);

--CREATE TABLE frota.autorizacao (
--  cod_autorizacao        INTEGER       NOT NULL,
--  exercicio              CHAR(4)       NOT NULL,
--  cod_item               INTEGER       NOT NULL,
--  cgm_motorista          INTEGER       NOT NULL,
--  cgm_resp_autorizacao   INTEGER       NOT NULL,
--  cgm_fornecedor         INTEGER       NOT NULL,
--  cod_veiculo            INTEGER       NOT NULL,
--  timestamp       timestamp NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH time zone,
--  quantidade             NUMERIC(14,4) NULL,
--  situacao               BOOLEAN       NULL,
--  valor                  NUMERIC(14,2) NOT NULL,
--  observacao      text      NULL,
--  CONSTRAINT pk_autorizacao PRIMARY KEY(cod_autorizacao, exercicio),
--  CONSTRAINT fk_autorizacao_1 FOREIGN KEY (cod_veiculo) REFERENCES frota.veiculo(cod_veiculo),
--  CONSTRAINT fk_autorizacao_2 FOREIGN KEY (cgm_motorista) REFERENCES frota.motorista(cgm_motorista),
--  CONSTRAINT fk_autorizacao_3 FOREIGN KEY (cgm_resp_autorizacao) REFERENCES sw_cgm(numcgm),
--  CONSTRAINT fk_autorizacao_4 FOREIGN KEY (cgm_fornecedor) REFERENCES sw_cgm(numcgm),
--  CONSTRAINT fk_autorizacao_5 FOREIGN KEY (cod_item) REFERENCES frota.item(cod_item)
--);
--
--CREATE TABLE frota.manutencao (
--  cod_manutencao INTEGER       NOT NULL,
--  exercicio      CHAR(4)       NOT NULL,
--  cod_veiculo    INTEGER       NOT NULL,
--  km             NUMERIC(14,1) NOT NULL,
--  dt_manutencao  DATE          NOT NULL,
--  CONSTRAINT pk_manutencao PRIMARY KEY(cod_manutencao, exercicio),
--  CONSTRAINT fk_manutencao_1 FOREIGN KEY(cod_veiculo) REFERENCES frota.veiculo(cod_veiculo)
--);
--
--CREATE TABLE frota.efetivacao (
--  cod_autorizacao        INTEGER NOT NULL,
--  cod_manutencao         INTEGER NOT NULL,
--  exercicio_autorizacao  CHAR(4) NOT NULL,
--  exercicio_manutencao   CHAR(4) NOT NULL,
--  CONSTRAINT pk_efetivacao PRIMARY KEY(cod_autorizacao, cod_manutencao, exercicio_autorizacao, exercicio_manutencao),
--  CONSTRAINT fk_efetivacao_1 FOREIGN KEY(cod_autorizacao, exercicio_autorizacao) REFERENCES frota.autorizacao(cod_autorizacao,exercicio),
--  CONSTRAINT fk_efetivacao_2 FOREIGN KEY(cod_manutencao, exercicio_manutencao) REFERENCES frota.manutencao(cod_manutencao, exercicio)
--);
--
--CREATE TABLE frota.manutencao_anulacao (
--  timestamp       timestamp NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH time zone,
--  exercicio       char(4)   NOT NULL,
--  cod_manutencao  integer   NOT NULL,
--  observacao      text      NULL,
--  CONSTRAINT pk_manutencao_anulacao PRIMARY KEY(exercicio, cod_manutencao),
--  CONSTRAINT fk_manutencao_anulacao_1 FOREIGN KEY(exercicio, cod_manutencao) REFERENCES frota.manutencao(exercicio, cod_manutencao)
--);
--
--CREATE TABLE frota.manutencao_item (
--  cod_manutencao  integer       NOT NULL,
--  cod_item        integer       NOT NULL,
--  exercicio       char(4)       NOT NULL,
--  quantidade      NUMERIC(14,4) NOT NULL,
--  valor           NUMERIC(14,2) NOT NULL,
--  CONSTRAINT pk_manutencao_item PRIMARY KEY(cod_manutencao, cod_item, exercicio),
--  CONSTRAINT fk_manutencao_item_1 FOREIGN KEY(cod_item) REFERENCES frota.item(cod_item),
--  CONSTRAINT fk_manutencao_item_2 FOREIGN KEY(cod_manutencao, exercicio) REFERENCES frota.manutencao(cod_manutencao, exercicio)
--);
--
--CREATE TABLE frota.motorista_veiculo (
--  cod_veiculo   INTEGER NOT NULL,
--  cgm_motorista INTEGER NOT NULL,
--  padrao        BOOLEAN NULL,
--  CONSTRAINT pk_motorista_veiculo PRIMARY KEY(cod_veiculo, cgm_motorista),
--  CONSTRAINT fk_motorista_veiculo_1 FOREIGN KEY(cgm_motorista) REFERENCES frota.motorista(cgm_motorista),
--  CONSTRAINT fk_motorista_veiculo_2 FOREIGN KEY(cod_veiculo) REFERENCES frota.veiculo(cod_veiculo)
--);

--INSERT INTO frota.tipo_item
--            (cod_tipo
--          , descricao)
--     VALUES (1
--           , 'Combustível');
--
--INSERT INTO frota.tipo_item
--            (cod_tipo
--           , descricao)
--      VALUES (2
--           , 'Peça');
--
--INSERT INTO frota.tipo_item
--           (cod_tipo
--         , descricao)
--    VALUES (3
--         , 'Serviço');
--
--INSERT INTO frota.tipo_item
--            (cod_tipo
--          , descricao)
--     VALUES (4
--          , 'Pneu');


--
-- Ultimas atualizações realizadas por hboaventura scrip uc_03-02-03
--
-- Adicionar os registros básicos de marca e modelo na base padrão do urbem para instalação em novos clientes.
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (13, 'Agrale');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (14, 'Alfa Romeu');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (15, 'Asia');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (16, 'Audi');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (17, 'BMW');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (18, 'CBT');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (19, 'Cross Lander');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (20, 'Daewoo');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (21, 'DKW - Vemag');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (22, 'Dodge');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (23, 'FNM');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (24, 'GMC');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (25, 'Gurgel');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (26, 'Hyundai');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (27, 'Iveco');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (28, 'Jeep');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (29, 'Kia');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (30, 'Lada');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (32, 'Mazda');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (33, 'Mercedes-Benz');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (34, 'Mitsubishi');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (35, 'Miura');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (36, 'Nissan');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (37, 'Seat');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (38, 'Suzuki');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (39, 'Troller');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (40, 'Volvo');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (42, 'Yamaha');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (43, 'Kasinski');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (44, 'Sundown');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (45, 'Kawasaki');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (46, 'Case');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (47, 'Danmar');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (48, 'Clark');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (49, 'Caterpillar');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (50, 'Komatsu');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (51, 'Agco');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (52, 'Huber Warco');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (53, 'Neobus');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (55, 'Maxibus');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (56, 'Massey Fergusson');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (57, 'John Deere');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (58, 'Lavrale');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (59, 'New Holland');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (60, 'Semeato');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (61, 'Valmet');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (54, 'Incasel');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (31, 'Marcopolo');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (41, 'Scânia');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (62, 'Michigan');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (63, 'Poclayn');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (64, 'Muller');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (65, 'Milbus');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (66, 'Comil');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (67, 'Busscar');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (68, 'Caio');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (69, 'Fiat Allis');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (9, 'Outros');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (1, 'Chevrolet');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (2, 'Volkswagen');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (3, 'Ford');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (6, 'Daimler Chrysler');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (7, 'Renault');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (8, 'Peugeot');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (4, 'Fiat do Brasil');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (10, 'Citroën');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (11, 'Honda');
   --INSERT INTO frota.marca (cod_marca, nom_marca) VALUES (12, 'Toyota');

   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 16, 'A3');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 1, 'Astra Hatch');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 1, 'Astra Sedan');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 1, 'Celta Hatch');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (4, 1, 'Classic Sedan');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (5, 1, 'Corsa Hatch');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (6, 1, 'Corsa Wagon');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (7, 1, 'Corsa Pick-up');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (8, 1, 'Corsa Sedan');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (9, 1, 'Meriva');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (10, 1, 'Montana');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (11, 1, 'Vectra');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (12, 1, 'Zafira');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 10, 'Berlingo Multispace');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 10, 'C3');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 10, 'Xsara Picasso');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 4, 'Brava');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 4, 'Doblo');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 4, 'Idea');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (4, 4, 'Marea');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (5, 4, 'Mille Fire');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (6, 4, 'Palio');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (7, 4, 'Palio Weekend');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (8, 4, 'Siena');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (9, 4, 'Stilo');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (10, 4, 'Strada');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (11, 4, 'Uno Mille');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (12, 4, 'Uno Furgão Fiorino');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 3, 'Courier');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 3, 'Ecosport');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 3, 'Escort');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (4, 3, 'Fiesta');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (5, 3, 'Focus');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (6, 3, 'Ka');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 11, 'Civic');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 11, 'Fit');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 33, 'Classe A');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 8, '206');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 8, '307');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 7, 'Clio');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 7, 'Kangoo');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 7, 'Megane');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (4, 7, 'Scenic');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (5, 7, 'Twingo');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 12, 'Corolla');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 2, 'Crossfox');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 2, 'Fox');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 2, 'Gol');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (4, 2, 'Golf');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (5, 2, 'Kombi');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (6, 2, 'Parati');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (7, 2, 'Polo');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (8, 2, 'Polo Sedan');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (9, 2, 'Santana');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (10, 2, 'Saveiro');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (11, 2, 'Van');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 14, '147');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 14, '156 ');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 14, '166');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 16, 'A4');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 16, 'A6');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (4, 16, 'A8');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (5, 16, 'RS6');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (6, 16, 'S3');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (7, 16, 'S4');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (8, 16, 'S6');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (9, 16, 'S8');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (10, 16, 'Avant');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (11, 16, 'TT');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (1, 17, '120');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 17, '130');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (3, 17, '320');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (4, 17, '325');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (5, 17, '330');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (6, 17, '525');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (7, 17, '530');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (8, 17, '540');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (9, 17, '545');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (10, 17, '550');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (11, 17, '645');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (12, 17, '745');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (13, 17, '750');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (14, 17, '760');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (15, 17, 'M3');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (16, 17, 'M5');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (17, 17, 'M6');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (18, 17, 'X3');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (19, 17, 'X5');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (20, 17, 'Z3');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (21, 17, 'Z4');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (13, 1, 'Omega');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (12, 2, 'Passat');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (14, 1, 'S-10 Blazer');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (15, 1, 'S-10');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (7, 3, 'Ranger');
   --INSERT INTO frota.modelo (cod_modelo, cod_marca, nom_modelo) VALUES (2, 33, 'Sprinter');

--
--
--
/*
-- Criar estrutura para efetuar a rescisão de convênios:
   CREATE TABLE licitacao.rescisao_convenio (
     exercicio_convenio    CHAR(4)        NOT NULL,
     num_convenio          INTEGER        NOT NULL,
     exercicio             CHAR(4)        NOT NULL,
     num_rescisao          INTEGER        NOT NULL,
     responsavel_juridico  INTEGER        NOT NULL,
     dt_rescisao           DATE           NOT NULL,
     vlr_multa             NUMERIC(14,2)  NOT NULL DEFAULT 0,
     vlr_indenizacao       NUMERIC(14,2)  NOT NULL DEFAULT 0,
     motivo                TEXT           NOT NULL,
     CONSTRAINT pk_rescisao_convenio   PRIMARY KEY (exercicio_convenio, num_convenio),
     CONSTRAINT fk_rescisao_convenio_1 FOREIGN KEY (exercicio_convenio, num_convenio)   REFERENCES licitacao.convenio(exercicio, num_convenio),
     CONSTRAINT fk_rescisao_convenio_2 FOREIGN KEY (responsavel_juridico)       REFERENCES public.sw_cgm(numcgm),
     CONSTRAINT uk_rescisao_convenio_1 UNIQUE (exercicio, num_convenio)
   );


   CREATE TABLE licitacao.publicacao_rescisao_convenio (
     exercicio_convenio    CHAR(4)        NOT NULL,
     num_convenio          INTEGER        NOT NULL,
     cgm_imprensa          INTEGER        NOT NULL,
     dt_publicacao         DATE           NOT NULL,
     observacao            VARCHAR(100)   NOT NULL,
     CONSTRAINT pk_publicacao_rescisao_convenio   PRIMARY KEY (exercicio_convenio, num_convenio, cgm_imprensa, dt_publicacao),
     CONSTRAINT fk_publicacao_rescisao_convenio_1 FOREIGN KEY (exercicio_convenio, num_convenio)   REFERENCES licitacao.rescisao_convenio(exercicio_convenio, num_convenio),
     CONSTRAINT fk_publicacao_rescisao_convenio_2 FOREIGN KEY (cgm_imprensa)       REFERENCES public.sw_cgm(numcgm)
   );


   GRANT INSERT, DELETE, SELECT, UPDATE ON licitacao.rescisao_convenio              TO GROUP urbem;
   GRANT INSERT, DELETE, SELECT, UPDATE ON licitacao.publicacao_rescisao_convenio   TO GROUP urbem;

   INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES ( 2017
             , 331
             , 'FLManterConvenios.php'
             , 'rescindir'
             , 8
             , ''
             , 'Rescindir Convênio');

 */


--
--
--
--insert into administracao.relatorio values (3,6,1,'Relatório Patrimonial','relatorioPatrimonial.rptdesign');
/*
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
*/
INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2184
           , 356
           , 'FLManterJulgamento.php'
           , 'excluir'
           , 7
           , ''
           , 'Excluir Julgamento');

--Reordena acoes
UPDATE administracao.acao set ordem=8 where cod_acao=1730;


--Nova acao
INSERT INTO administracao.acao
           (cod_acao
         , cod_funcionalidade
         , nom_arquivo
         , parametro
         , ordem
         , complemento_acao
         , nom_acao)
    VALUES (2185
         , 326
         , 'FLManterJulgamentoProposta.php'
         , 'excluir'
         , 12
         , ''
         , 'Excluir Julgamento');

-- Ajuste coluna exercicio tabela  licitacao.contrato_aditivos_anulacao;
   Alter Table licitacao.contrato_aditivos_anulacao Add Column new_exercico CHAR(04);
   UPDATE licitacao.contrato_aditivos_anulacao SET new_exercico = BTRIM(TO_CHAR( exercicio , '9999'));
   ALTER TABLE licitacao.contrato_aditivos_anulacao DROP COLUMN exercicio;
   Alter TABLE licitacao.contrato_aditivos_anulacao RENAME COLUMN new_exercico TO exercicio;
   ALTER TABLE licitacao.contrato_aditivos_anulacao ADD CONSTRAINT pk_contrato_aditivos_anulacao   PRIMARY KEY (exercicio_contrato, cod_entidade, num_contrato, exercicio, num_aditivo);
   ALTER TABLE licitacao.contrato_aditivos_anulacao ADD CONSTRAINT fk_contrato_aditivos_anulacao_2 FOREIGN KEY (exercicio_contrato, cod_entidade, num_contrato, exercicio, num_aditivo) REFERENCES licitacao.contrato_aditivos(exercicio_contrato, cod_entidade, num_contrato, exercicio, num_aditivo);

--Reordena acoes
UPDATE administracao.acao set ordem=13 where cod_acao=1595;
UPDATE administracao.acao set ordem=14 where cod_acao=1596;

--  Ticket #11348  Ajustes contra-barras almoxarifado.catalogo_item
update almoxarifado.catalogo_item
    set descricao          = replace(descricao,E'\\','')
      , descricao_resumida = replace(descricao_resumida,E'\\','')
  where descricao_resumida like E'%\'%';

CREATE TABLE compras.solicitacao_homologada_anulacao
             (exercicio      char(4)  not null,
             cod_entidade    integer  not null,
             cod_solicitacao integer  not null,
             numcgm          integer  not null,
             timestamp       timestamp default ('now'::text)::timestamp(3) with time zone,
  CONSTRAINT pk_solicitacao_homologada_anulacao PRIMARY KEY (exercicio, cod_entidade, cod_solicitacao),
  CONSTRAINT fk_solicitacao_homologada_anulacao_1 FOREIGN KEY (numcgm) REFERENCES administracao.usuario(numcgm),
  CONSTRAINT fk_solicitacao_homologada_anulacao_2 FOREIGN KEY (exercicio, cod_entidade, cod_solicitacao) REFERENCES compras.solicitacao_homologada(exercicio, cod_entidade, cod_solicitacao));

  GRANT INSERT, SELECT, DELETE, UPDATE ON compras.solicitacao_homologada_anulacao TO GROUP urbem;






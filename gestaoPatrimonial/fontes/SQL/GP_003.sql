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
* Versão 003.
*/

--------------------------------------------------
-- Estava no GP_001.sql e não saiu em versão ainda
--------------------------------------------------

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

--------------
-- gp_ordem_compra
--------------

--uc_03-04-24

   -- Criação da Funcionalidade Ordem de Compra.
   INSERT INTO administracao.funcionalidade
               (cod_funcionalidade
             , cod_modulo
             , nom_funcionalidade
             , nom_diretorio
             , ordem)
        SELECT 351
             , 35
             , 'Ordem de Compra'
             , 'instancias/ordemCompra/'
             , 23
         WHERE 0 = (SELECT COUNT(1)
                      FROM administracao.funcionalidade
                     WHERE cod_funcionalidade = 351);


   INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES (1692
             , 351
             , 'FLManterOrdemCompra.php'
             , 'incluir'
             , 1
             , ''
             , 'Incluir Ordem de Compra');

      INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES (1693
             , 351
             , 'FLManterOrdemCompra.php'
             , 'alterar'
             , 2
             , ''
             , 'Alterar Ordem de Compra');

   INSERT INTO administracao.acao
               (cod_acao
             , cod_funcionalidade
             , nom_arquivo
             , parametro
             , ordem
             , complemento_acao
             , nom_acao)
        VALUES (1700
             , 351
             , 'FLManterOrdemCompra.php'
             , 'consultar'
             , 5
             , ''
             , 'Consultar Ordem de Compra');


   ALTER  TABLE compras.ordem_compra_item DROP CONSTRAINT fk_ordem_compra_item_1;
   ALTER  TABLE compras.ordem_compra_item ADD  CONSTRAINT fk_ordem_compra_item_1
          FOREIGN KEY (exercicio, cod_pre_empenho, num_item) REFERENCES empenho.item_pre_empenho_julgamento(exercicio, cod_pre_empenho, num_item) ;
   DROP TABLE  empenho.catalogo_item_pre_empenho ;

   ALTER TABLE compras.ordem_compra ADD COLUMN timestamp TIMESTAMP NULL DEFAULT ('now'::text)::timestamp(3) WITH time zone;

   -- campo obsevacao adicionado à tabela --
   ALTER TABLE compras.ordem_compra ADD COLUMN observacao varchar(200) NOT NULL DEFAULT '';

   INSERT INTO administracao.acao
              (cod_acao
            , cod_funcionalidade
            , nom_arquivo
            , parametro
            , ordem
            , complemento_acao
            , nom_acao)
       VALUES (1828
            , 351
            , 'FLManterOrdemCompra.php'
            , 'anular'
            , 4
            , ''
            , 'Anular Ordem de Compra');

   INSERT INTO administracao.relatorio
              (cod_gestao
            , cod_modulo
            , cod_relatorio
            , nom_relatorio
            , arquivo)
       VALUES (3
            , 35
            , 3
            , 'Ordem de Compra'
            , 'ordemCompra.rptdesign');
   -- Inclusão de default para coluna timestap tabela    compras.ordem_compra_anulacao.
   Alter table compras.ordem_compra_anulacao Alter column timestamp set  DEFAULT ('now'::text)::timestamp(3) WITH time zone;

UPDATE administracao.acao SET ordem =  8  WHERE cod_acao = 1573;
UPDATE administracao.acao SET ordem =  9  WHERE cod_acao = 1574;
UPDATE administracao.acao SET ordem = 10  WHERE cod_acao = 1575;
UPDATE administracao.acao SET ordem = 11  WHERE cod_acao = 1589;
UPDATE administracao.acao SET ordem = 12  WHERE cod_acao = 1590;
UPDATE administracao.acao SET ordem = 13  WHERE cod_acao = 2185;
UPDATE administracao.acao SET ordem = 14  WHERE cod_acao = 1595;
UPDATE administracao.acao SET ordem = 15  WHERE cod_acao = 1596;


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

--------------
-- Ticket #11252
--------------
--Adiciona novo campo tipo nas tabelas
ALTER TABLE compras.ordem_compra ADD COLUMN tipo char(1) NOT NULL DEFAULT 'C';
ALTER TABLE compras.ordem_compra_item ADD COLUMN tipo char(1) NOT NULL DEFAULT 'C';
ALTER TABLE compras.ordem_compra_anulacao ADD COLUMN tipo char(1) NOT NULL DEFAULT 'C';
ALTER TABLE compras.ordem_compra_item_anulacao ADD COLUMN tipo char(1) NOT NULL DEFAULT 'C';
ALTER TABLE compras.nota_fiscal_fornecedor ADD COLUMN tipo char(1) NOT NULL DEFAULT 'C';

--Renomeia as tabelas de ordem de compras, renomeando para ordem
ALTER TABLE compras.ordem_compra RENAME TO ordem;
ALTER TABLE compras.ordem_compra_item RENAME TO ordem_item;
ALTER TABLE compras.ordem_compra_anulacao RENAME TO ordem_anulacao;
ALTER TABLE compras.ordem_compra_item_anulacao RENAME TO ordem_item_anulacao;

--Dropa as constantes existentes
ALTER TABLE compras.ordem_item_anulacao DROP CONSTRAINT pk_ordem_compra_item_anulacao;
ALTER TABLE compras.ordem_item_anulacao DROP CONSTRAINT fk_ordem_compra_item_anulacao_1;
ALTER TABLE compras.ordem_item_anulacao DROP CONSTRAINT fk_ordem_compra_item_anulacao_2;
ALTER TABLE compras.ordem_item DROP CONSTRAINT pk_ordem_compra_item;
ALTER TABLE compras.ordem_item DROP CONSTRAINT fk_ordem_compra_item_1;
ALTER TABLE compras.ordem_item DROP CONSTRAINT fk_ordem_compra_item_2;
ALTER TABLE compras.ordem_anulacao DROP CONSTRAINT pk_ordem_compra_anulacao;
ALTER TABLE compras.ordem_anulacao DROP CONSTRAINT fk_ordem_compra_anulacao_1;
ALTER TABLE compras.nota_fiscal_fornecedor DROP CONSTRAINT fk_nota_fiscal_fornecedor_2;
ALTER TABLE compras.ordem DROP CONSTRAINT pk_ordem_compra;
ALTER TABLE compras.ordem DROP CONSTRAINT fk_ordem_compra_1;

--Recria as constantes
ALTER TABLE compras.ordem ADD CONSTRAINT pk_ordem PRIMARY KEY (exercicio, cod_entidade, cod_ordem, tipo);
ALTER TABLE compras.ordem ADD CONSTRAINT fk_ordem_1 FOREIGN KEY (exercicio_empenho, cod_entidade, cod_empenho) REFERENCES empenho.empenho(exercicio, cod_entidade, cod_empenho);
ALTER TABLE compras.nota_fiscal_fornecedor ADD CONSTRAINT fk_nota_fiscal_fornecedor_2 FOREIGN KEY (exercicio_ordem_compra, cod_entidade, cod_ordem, tipo) REFERENCES compras.ordem(exercicio, cod_entidade, cod_ordem, tipo);
ALTER TABLE compras.ordem_anulacao ADD CONSTRAINT pk_ordem_anulacao PRIMARY KEY (exercicio, cod_entidade, cod_ordem, timestamp, tipo);
ALTER TABLE compras.ordem_anulacao ADD CONSTRAINT fk_ordem_anulacao_1 FOREIGN KEY (cod_entidade, cod_ordem, exercicio, tipo) REFERENCES compras.ordem(cod_entidade, cod_ordem, exercicio, tipo);
ALTER TABLE compras.ordem_item ADD CONSTRAINT pk_ordem_item PRIMARY KEY (exercicio, cod_entidade, cod_ordem, cod_pre_empenho, num_item, tipo); ALTER TABLE compras.ordem_item ADD CONSTRAINT fk_ordem_item_1 FOREIGN KEY (exercicio, cod_pre_empenho, num_item) REFERENCES empenho.item_pre_empenho_julgamento(exercicio, cod_pre_empenho, num_item);
ALTER TABLE compras.ordem_item ADD CONSTRAINT fk_ordem_item_2 FOREIGN KEY (cod_entidade, cod_ordem, exercicio, tipo) REFERENCES compras.ordem(cod_entidade, cod_ordem, exercicio, tipo);
ALTER TABLE compras.ordem_item_anulacao ADD CONSTRAINT pk_ordem_item_anulacao PRIMARY KEY (exercicio, cod_entidade, cod_ordem, cod_pre_empenho, num_item, timestamp, tipo);
ALTER TABLE compras.ordem_item_anulacao ADD CONSTRAINT fk_ordem_item_anulacao_1 FOREIGN KEY (cod_entidade, cod_ordem, timestamp, exercicio, tipo) REFERENCES compras.ordem_anulacao(cod_entidade, cod_ordem, timestamp, exercicio, tipo);
ALTER TABLE compras.ordem_item_anulacao ADD CONSTRAINT fk_ordem_item_anulacao_2 FOREIGN KEY (exercicio, cod_entidade, cod_ordem, cod_pre_empenho, num_item, tipo) REFERENCES compras.ordem_item(exercicio, cod_entidade, cod_ordem, cod_pre_empenho, num_item, tipo);

--Alteracao do nome da coluna exercicio
ALTER TABLE compras.nota_fiscal_fornecedor RENAME column exercicio_ordem_compra to exercicio_ordem;

ALTER TABLE COMPRAS.NOTA_FISCAL_FORNECEDOR ALTER NUM_SERIE TYPE VARCHAR(9) ;

ALTER TABLE administracao.acao ALTER COLUMN parametro TYPE VARCHAR(15);

INSERT INTO administracao.funcionalidade
           (cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem)
    SELECT 412
         , 35
         , 'Ordem de Serviço'
         , 'instancias/ordemCompra/'
         , '30'
     WHERE  0 = (SELECT COUNT(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 412);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2248
          , 412
          , 'FLManterOrdemCompra.php'
          , 'incluirOS'
          , 1
          , ''
          , 'Incluir Ordem de Serviço');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2249
          , 412
          , 'FLManterOrdemCompra.php'
          , 'alterarOS'
          , 2
          , ''
          , 'Alterar Ordem de Serviço');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2250
          , 412
          , 'FLManterOrdemCompra.php'
          , 'anularOS'
          , 3
          , ''
          , 'Anular Ordem de Serviço');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2251
          , 412
          , 'FLManterOrdemCompra.php'
          , 'consultarOS'
          , 4
          , ''
          ,'Consultar Ordem de Serviço');

----------------
--Ticket #12587
----------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2252
          , 351
          , 'FLManterOrdemCompra.php'
          , 'reemitir'
          , 6
          , ''
          , 'Reemitir Ordem de Compra');

-------------
-- Enviado pelo Jabber pelo Gelson
-------------

 INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2255
          , 412
          , 'FLManterOrdemCompra.php'
          , 'reemitirOS'
          , 5
          , ''
          , 'Reemitir Ordem de Serviço');

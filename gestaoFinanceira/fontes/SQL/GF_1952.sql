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
* $Id: GF_1952.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.95.2
*/

----------------
-- Ticket #15311
----------------

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2354;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2354;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2354
   AND nom_acao = 'Incluir Ação';

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2355;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2355;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2355
   AND nom_acao = 'Alterar Ação';

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2356;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2356;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2356
   AND nom_acao = 'Excluir Ação';

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade = 434
   AND nom_funcionalidade = 'Ações';

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 434
         , 43
         , 'Ações'
         , 'instancias/acao/'
         , 11
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2354
          , 434
          , 'FMManterAcao.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Ação'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2355
          , 434
          , 'FLManterAcao.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Ação'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2356
          , 434
          , 'FLManterAcao.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Ação'
          );


--------------------------------------
-- ALTERACOES NA TABELA ppa.acao_dados
--------------------------------------

ALTER TABLE ppa.acao_dados DROP CONSTRAINT fk_acao_dados_4;
ALTER TABLE ppa.acao_dados DROP COLUMN cod_contrato;

ALTER TABLE ppa.acao_dados ADD COLUMN cod_forma          INTEGER      NOT NULL;
ALTER TABLE ppa.acao_dados ADD COLUMN cod_tipo_orcamento INTEGER      NOT NULL;
ALTER TABLE ppa.acao_dados ADD COLUMN detalhamento       VARCHAR(480) NOT NULL;


----------------
-- Ticket #15346
----------------

CREATE TABLE ppa.acao_periodo (
    cod_acao                INTEGER     NOT NULL,
    timestamp_acao_dados    TIMESTAMP   NOT NULL,
    data_inicio             DATE        NOT NULL,
    data_termino            DATE        NOT NULL,
    constraint pk_acao_periodo          PRIMARY KEY               (cod_acao, timestamp_acao_dados),
    constraint fk_acao_periodo_1        FOREIGN KEY               (cod_acao, timestamp_acao_dados)
                                        REFERENCES ppa.acao_dados (cod_acao, timestamp_acao_dados)
);

GRANT ALL ON ppa.acao_periodo TO GROUP urbem;

ALTER TABLE ppa.acao_dados ADD COLUMN valor_estimado NUMERIC(14,2) NOT NULL;
ALTER TABLE ppa.acao_dados ADD COLUMN meta_estimada  NUMERIC(14,2)  NOT NULL;


------------------------------------
-- ALTERACAO NA TABELA ppa.tipo_acao
------------------------------------

ALTER TABLE ppa.tipo_acao ALTER COLUMN descricao TYPE VARCHAR(40);

INSERT INTO ppa.tipo_acao VALUES (4,'Financiamentos'                 );
INSERT INTO ppa.tipo_acao VALUES (5,'Parcerias'                      );
INSERT INTO ppa.tipo_acao VALUES (6,'Plano de Dispêndio das Estatais');
INSERT INTO ppa.tipo_acao VALUES (7,'Renúncia Fiscal'                );
INSERT INTO ppa.tipo_acao VALUES (8,'Outras Iniciativas e Diretrizes');


----------------------------------------------------
-- ADICIONANDO COLUNA cod_natureza EM ppa.acao_dados
----------------------------------------------------

ALTER TABLE ppa.acao_dados ADD COLUMN cod_natureza INTEGER NOT NULL;


------------------------------------------------------------------------
-- ADICIONANDO FUNCIONALIDADES E ACOES P/ CADASTROS DE regiao E produtos
------------------------------------------------------------------------

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2344;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2344;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2344
   AND nom_acao = 'Incluir Região';

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2345;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2345;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2345
   AND nom_acao = 'Alterar Região';

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2346;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2346;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2346
   AND nom_acao = 'Excluir Região';

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade = 430
   AND nom_funcionalidade = 'Regiões';

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 430
         , 43
         , 'Regiões'
         , 'instancias/regioes/'
         , 4
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2344
          , 430
          , 'FMManterRegioes.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Região'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2345
          , 430
          , 'FLManterRegioes.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Região'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2346
          , 430
          , 'FLManterRegioes.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Região'
          );


DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2347;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2347;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2347
   AND nom_acao = 'Incluir Produto';

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2348;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2348;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2348
   AND nom_acao = 'Alterar Produto';

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 2349;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 2349;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 2349
   AND nom_acao = 'Excluir Produto';

DELETE
  FROM administracao.funcionalidade
 WHERE cod_funcionalidade = 431
   AND nom_funcionalidade = 'Produtos';

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 431
         , 43
         , 'Produtos'
         , 'instancias/produtos/'
         , 5
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2347
          , 431
          , 'FMManterProdutos.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Produto'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2348
          , 431
          , 'FLManterProdutos.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Produto'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2349
          , 431
          , 'FLManterProdutos.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Produto'
          );


-------------------------------------------------------------------------
-- ALTERANDO QUANTIDADE DE CARACTERES DA  COLUNA nom_pao EM orcamento.pao
-------------------------------------------------------------------------

ALTER TABLE orcamento.pao ALTER COLUMN nom_pao TYPE VARCHAR(480);


-----------------------------------------------------
-- ALTERANDO COLUNA quantidade DE ppa.acao_quantidade
-----------------------------------------------------

ALTER TABLE ppa.acao_quantidade ALTER COLUMN quantidade TYPE NUMERIC(14,2);


-------------------------------------------
-- CADASTRANDO UNIDADE DE MEDIDA percentual
-------------------------------------------

INSERT
  INTO administracao.grandeza
     ( cod_grandeza
     , nom_grandeza
     )
VALUES
     ( 8
     , 'Proporção'
     );

INSERT
  INTO administracao.unidade_medida
     ( cod_unidade
     , cod_grandeza
     , nom_unidade
     , simbolo
     )
VALUES 
     ( 1
     , 8
     , 'Percentual'
     , '%'
     );

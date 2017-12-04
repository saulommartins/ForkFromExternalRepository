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
* Versão 1.92.4
*/

----------------
-- Ticket #15633
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2751
          , 359
          , 'FLExportacaoGF.php'
          , 'pessoal'
          , 3
          , ''
          , 'Arquivos de Pessoal'
          );


----------------
-- Ticket #15617
----------------

CREATE TABLE tcepb.tipo_cargo_tce (
    cod_tipo_cargo_tce  INTEGER         NOT NULL,
    descricao           VARCHAR(450)    NOT NULL,
    CONSTRAINT pk_tipo_cargo_tce        PRIMARY KEY (cod_tipo_cargo_tce)
);

GRANT ALL ON tcepb.tipo_cargo_tce TO GROUP urbem;


SELECT atualizarbanco('
CREATE TABLE pessoal.de_para_tipo_cargo (
    cod_sub_divisao     INTEGER         NOT NULL,
    cod_tipo_cargo_tce  INTEGER         NOT NULL,
    CONSTRAINT pk_de_para_tipo_cargo    PRIMARY KEY                         (cod_sub_divisao, cod_tipo_cargo_tce),
    CONSTRAINT pk_de_para_tipo_cargo_1  FOREIGN KEY                         (cod_sub_divisao)
                                        REFERENCES pessoal.sub_divisao      (cod_sub_divisao),
    CONSTRAINT pk_de_para_tipo_cargo_2  FOREIGN KEY                         (cod_tipo_cargo_tce)
                                        REFERENCES tcepb.tipo_cargo_tce     (cod_tipo_cargo_tce)
      );
');

SELECT atualizarbanco('GRANT ALL ON pessoal.de_para_tipo_cargo TO GROUP urbem;');

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod 
     , nome_tabela             
     , sequencia
     )
VALUES ( 1
     , 'de_para_tipo_cargo'
     , 1
     );


----------------
-- Ticket #15662
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2753
          , 365
          , 'FLManterTipoCargo.php'
          , 'configurar'
          , 90
          , ''
          , 'Relacionar Tipo de Cargo'
          );

INSERT INTO tcepb.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (0, 'Inativos / Pensionistas'                      );
INSERT INTO tcepb.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (1, 'Efetivos'                                     );
INSERT INTO tcepb.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (2, 'Eletivos'                                     );
INSERT INTO tcepb.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (3, 'Cargo comissionado'                           );
INSERT INTO tcepb.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (4, 'Função de confiança'                          );
INSERT INTO tcepb.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (5, 'Contratação por excepcional interesse público');
INSERT INTO tcepb.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (6, 'Emprego público'                              );


----------------
-- Ticket #15618
----------------

CREATE TABLE tcepb.tipo_regime_trabalho_tce (
    cod_tipo_regime_trabalho_tce    INTEGER         NOT NULL,
    descricao                       VARCHAR(450)    NOT NULL,
    CONSTRAINT pk_tipo_regime_trabalho_tce          PRIMARY KEY (cod_tipo_regime_trabalho_tce)
);

GRANT ALL ON tcepb.tipo_regime_trabalho_tce TO GROUP urbem;

INSERT INTO tcepb.tipo_regime_trabalho_tce VALUES (0, 'Estatutário');
INSERT INTO tcepb.tipo_regime_trabalho_tce VALUES (1, 'Celetista'  );
INSERT INTO tcepb.tipo_regime_trabalho_tce VALUES (2, 'Contratual' );
INSERT INTO tcepb.tipo_regime_trabalho_tce VALUES (3, 'Eletivo'    );

SELECT atualizarbanco('
CREATE TABLE pessoal.de_para_tipo_regime_trabalho (
    cod_sub_divisao                 INTEGER         NOT NULL,
    cod_tipo_regime_trabalho_tce    INTEGER         NOT NULL,
    CONSTRAINT pk_de_para_tipo_regime_trabalho      PRIMARY KEY                               (cod_sub_divisao, cod_tipo_regime_trabalho_tce),
    CONSTRAINT pk_de_para_tipo_regime_trabalho_1    FOREIGN KEY                               (cod_sub_divisao)
                                                    REFERENCES pessoal.sub_divisao            (cod_sub_divisao),
    CONSTRAINT pk_de_para_tipo_regime_trabalho_2    FOREIGN KEY                               (cod_tipo_regime_trabalho_tce)
                                                    REFERENCES tcepb.tipo_regime_trabalho_tce (cod_tipo_regime_trabalho_tce)
    );
');

SELECT atualizarbanco('GRANT ALL ON pessoal.de_para_tipo_regime_trabalho TO GROUP urbem;');

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
VALUES ( 1
     , 'de_para_tipo_regime_trabalho'
     , 1
     );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2754
          , 365
          , 'FLManterTipoRegimeTrabalho.php'
          , 'configurar'
          , 91
          , ''
          , 'Relacionar Tipo de Regime de Trabalho'
          );

SELECT atualizarbanco('
CREATE TABLE pessoal.de_para_orgao_unidade(
    cod_orgao       INTEGER         NOT NULL,
    num_orgao       INTEGER         NOT NULL,
    num_unidade     INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    CONSTRAINT pk_de_para_orgao_unidade PRIMARY KEY                         (cod_orgao, num_orgao, num_unidade, exercicio),
    CONSTRAINT fk_de_para_orgao_unidade_1   FOREIGN KEY                     (cod_orgao)
                                            REFERENCES organograma.orgao    (cod_orgao),
    CONSTRAINT fk_de_para_orgao_unidade_2   FOREIGN KEY                     (num_orgao, num_unidade, exercicio)
                                            REFERENCES orcamento.unidade    (num_orgao, num_unidade, exercicio)
);
');

SELECT atualizarbanco('GRANT ALL ON pessoal.de_para_orgao_unidade TO GROUP urbem;');

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
VALUES ( 1
     , 'pessoal.de_para_orgao_unidade'
     , 1
     );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2755
          , 365
          , 'FLManterUnidadeOrcamentaria.php'
          , 'configurar'
          , 92
          , ''
          , 'Relacionar Unidades Orçamentárias'
          );


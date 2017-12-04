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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id:  $
*
* Versão 1.98.1
*/

----------------
-- Ticket #15803
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2770
          , 354
          , 'FMManterConfiguracaoEmprestimoBanrisul.php'
          , 'incluir'
          , 26
          , ''
          , 'Configuração Empréstimo Banrisul'
          );


----------------
-- Ticket #15804
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 4
          , 40
          , 14
          , 'Conferência Empréstimo Banrisul'
          , 'conferenciaEmprestimosBanrisul.rptdesign'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2771
          , 409
          , 'FLEmprestimoBanrisul.php'
          , 'incluir'
          , 2
          , ''
          , 'Empréstimos Banrisul'
          );


-------------------------------------------------
-- CORRIGINDO ENTRATA EM administracao.tabelas_rh
-------------------------------------------------

UPDATE administracao.tabelas_rh
   SET nome_tabela = 'de_para_orgao_unidade'
 WHERE nome_tabela = 'pessoal.de_para_orgao_unidade'
     ;


----------------
-- Ticket #16067
----------------

SELECT atualizarbanco('
CREATE TABLE pessoal.contrato_servidor_historico_funcional(
    cod_contrato            INTEGER     NOT NULL,
    periodo_movimentacao    CHAR(6)     NOT NULL,
    ato_movimentacao        INTEGER     NOT NULL,
    data_apresentada        DATE        NOT NULL,
    CONSTRAINT pk_contrato_servidor_historico_funcional     PRIMARY KEY                         (cod_contrato, periodo_movimentacao),
    CONSTRAINT fk_contrato_servidor_historico_funcional_1   FOREIGN KEY                         (cod_contrato)
                                                            REFERENCES pessoal.contrato_servidor(cod_contrato)
);
');

SELECT atualizarbanco('GRANT ALL ON pessoal.contrato_servidor_historico_funcional TO GROUP urbem;');

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
VALUES
     ( 1
     , 'contrato_servidor_historico_funcional'
     , 1
     );


----------------
-- Ticket #15803
----------------

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_banrisul_emprestimo(
   cod_evento          INTEGER     NOT NULL,
   CONSTRAINT fk_configuracao_banrisul_emprestimo_2  FOREIGN KEY                      (cod_evento)
                                                     REFERENCES folhapagamento.evento (cod_evento)
);
');

SELECT atualizarbanco('GRANT ALL ON ima.configuracao_banrisul_emprestimo TO GROUP urbem;');

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
VALUES
     ( 7
     , 'configuracao_banrisul_emprestimo'
     , 1
     );


----------------
-- Ticket #15804
----------------

SELECT atualizarbanco('
CREATE TABLE ima.consignacao_emprestimo_banrisul_configuracao(
    cod_periodo_movimentacao    INTEGER         NOT NULL,
    cod_convenio                INTEGER         NOT NULL,
    nom_convenio                VARCHAR(50)     NOT NULL,
    ano_mes                     VARCHAR(6)      NOT NULL,
    CONSTRAINT pk_consignacao_emprestimo_banrisul_configuracao PRIMARY KEY (cod_periodo_movimentacao)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.consignacao_emprestimo_banrisul_configuracao TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.consignacao_emprestimo_banrisul(
    num_linha                   INTEGER         NOT NULL,
    cod_periodo_movimentacao    INTEGER         NOT NULL,
    oa                          INTEGER                 ,
    matricula                   INTEGER                 ,
    cpf                         VARCHAR(15)             ,
    nom_funcionario             VARCHAR(35)             ,
    cod_canal                   INTEGER                 ,
    nro_contrato                VARCHAR(20)             ,
    prestacao                   VARCHAR(7)              ,
    val_consignar               INTEGER                 ,
    val_consignado              INTEGER                 ,
    filler                      VARCHAR(200)            ,
    cod_contrato                INTEGER                 ,
    origem_pagamento            CHAR(1)                 ,
    CONSTRAINT pk_consignacao_emprestimo_banrisul   PRIMARY KEY                                                 (num_linha,cod_periodo_movimentacao),
    CONSTRAINT fk_consignacao_emprestimo_banrisul_1 FOREIGN KEY                                                 (cod_periodo_movimentacao)
                                                    REFERENCES ima.consignacao_emprestimo_banrisul_configuracao (cod_periodo_movimentacao)
);
');

SELECT atualizarbanco('GRANT ALL ON ima.consignacao_emprestimo_banrisul TO GROUP urbem;');

SELECT atualizarbanco('
CREATE TABLE ima.motivos_rejeicao_consignacao_emprestimo_banrisul(
    cod_motivo_rejeicao         CHAR(2)         NOT NULL,
    descricao                   VARCHAR(200)    NOT NULL,
    CONSTRAINT pk_motivos_rejeicao_consignacao_emprestimo_banrisul PRIMARY KEY (cod_motivo_rejeicao)
);
');

SELECT atualizarbanco('GRANT ALL ON ima.motivos_rejeicao_consignacao_emprestimo_banrisul TO GROUP urbem;');

SELECT atualizarbanco('
CREATE TABLE ima.consignacao_emprestimo_banrisul_erro(
    num_linha                   INTEGER         NOT NULL,
    cod_periodo_movimentacao    INTEGER         NOT NULL,
    cod_motivo_rejeicao         CHAR(2)         NOT NULL,    
    descricao_motivo            VARCHAR(200)    NOT NULL,
    CONSTRAINT pk_consignacao_emprestimo_banrisul_erro   PRIMARY KEY                                                    (num_linha,cod_periodo_movimentacao),
    CONSTRAINT fk_consignacao_emprestimo_banrisul_erro_1 FOREIGN KEY                                                    (num_linha,cod_periodo_movimentacao)
                                                         REFERENCES ima.consignacao_emprestimo_banrisul                 (num_linha,cod_periodo_movimentacao),
    CONSTRAINT fk_consignacao_emprestimo_banrisul_erro_2 FOREIGN KEY                                                    (cod_motivo_rejeicao)
                                                         REFERENCES ima.motivos_rejeicao_consignacao_emprestimo_banrisul(cod_motivo_rejeicao)
);
');

SELECT atualizarbanco('GRANT ALL ON ima.consignacao_emprestimo_banrisul_erro TO GROUP urbem;');

SELECT atualizarbanco('
INSERT INTO ima.motivos_rejeicao_consignacao_emprestimo_banrisul VALUES (\'BI\',\'Falecimento servidor\');
INSERT INTO ima.motivos_rejeicao_consignacao_emprestimo_banrisul VALUES (\'HM\',\'Servidor não identificado\');
INSERT INTO ima.motivos_rejeicao_consignacao_emprestimo_banrisul VALUES (\'HN\',\'Tipo de contrato não permite empréstimo\');
INSERT INTO ima.motivos_rejeicao_consignacao_emprestimo_banrisul VALUES (\'HW\',\'Margem consignável excedida para o servidor\');
INSERT INTO ima.motivos_rejeicao_consignacao_emprestimo_banrisul VALUES (\'H3\',\'Não descontados outros motivos\');
INSERT INTO ima.motivos_rejeicao_consignacao_emprestimo_banrisul VALUES (\'H8\',\'Servidor desligado da entidade\');
INSERT INTO ima.motivos_rejeicao_consignacao_emprestimo_banrisul VALUES (\'H9\',\'Servidor afastado por Licença\');
');



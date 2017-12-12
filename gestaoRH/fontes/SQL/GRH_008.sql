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
* Versão 006.
*/

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2221
          , 354
          , 'FMConfiguracaoBradesco.php'
          , 'configurar'
          , 17
          , ''
          , 'Exportação Banco Bradesco');



----------
-- Ticket #12287
----------
UPDATE administracao.acao SET ordem = 5 WHERE cod_acao = 2213;

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2222
          , 353
          , 'FLExportarBradesco.php'
          , 'exportar'
          , 6
          , ''
          , 'Remessa Banco Bradesco');

----------
-- Ticket #12288
----------

select atualizarBanco('
CREATE TABLE ima.configuracao_convenio_bradesco (
  cod_convenio       INTEGER NOT NULL,
  cod_empresa        INTEGER NOT NULL,
  cod_banco          INTEGER NOT NULL,
  cod_agencia        INTEGER NOT NULL,
  cod_conta_corrente INTEGER NOT NULL,
  CONSTRAINT pk_configuracao_convenio_bradesco PRIMARY KEY(cod_convenio),
  CONSTRAINT fk_configuracao_convenio_bradesco_1 FOREIGN KEY(cod_conta_corrente, cod_agencia, cod_banco) REFERENCES monetario.conta_corrente(cod_conta_corrente, cod_agencia, cod_banco)
);
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.configuracao_convenio_bradesco TO GROUP urbem;
');

select atualizarConfiguracao(40,'num_sequencial_arquivo_bradesco','1');
select atualizarConfiguracao(40,'dt_num_sequencial_arquivo_bradesco','2008-01-01');
select atualizarConfiguracao(40,'num_remessa_pasep','1');
select atualizarConfiguracao(40,'exercicio_remessa_pasep','2008');

select atualizarConfiguracao(40,'codigo_outras_entidades_sefip','0000');

------------
-- Ticket #12313
------------

select atualizarBanco('
CREATE TABLE ima.configuracao_pasep (
  cod_convenio       INTEGER     NOT NULL,
  num_convenio       INTEGER     NOT NULL,
  cod_banco          INTEGER     NOT NULL,
  cod_agencia        INTEGER     NOT NULL,
  cod_conta_corrente INTEGER     NOT NULL,
  cod_evento         INTEGER     NOT NULL,
  email              VARCHAR(50) NOT NULL,
  CONSTRAINT pk_configuracao_pasep PRIMARY KEY(cod_convenio),
  CONSTRAINT fk_configuracao_pasep_1 FOREIGN KEY(cod_evento) REFERENCES folhapagamento.evento(cod_evento),
  CONSTRAINT fk_configuracao_pasep_2 FOREIGN KEY(cod_conta_corrente, cod_agencia, cod_banco) REFERENCES monetario.conta_corrente(cod_conta_corrente, cod_agencia, cod_banco)
);
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.configuracao_pasep TO GROUP urbem;
');

------------
-- Ticket #12312
------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2223
          , 354
          , 'FMConfiguracaoPASEP.php'
          , 'configurar'
          , 18
          , ''
          , 'Exportação PASEP');

------------
-- Ticket #12314
------------

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 408
          , 40
          , 'PASEP'
          , 'instancias/pasep/'
          , 9
      WHERE 0 = (SELECT COUNT(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 408);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2224
          , 408
          , 'FLExportarPASEP.php'
          , 'exportar'
          , 1
          , ''
          , 'Gerar Arquivo');

------------
-- Ticket #12317
------------

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 6
          , 'Erros'
          , 'errosPasep.rptdesign');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 7
          , 'Conferência FPS900'
          , 'conferenciaFPS900.rptdesign');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 8
          , 'Conferência FPS910'
          , 'conferenciaFPS910.rptdesign');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 9
          , 'Não Pagos FPS950'
          , 'naoPagosFPS950.rptdesign');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 10
          , 'Erros 910'
          , 'errosPasep910.rptdesign');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 11
          , 'Erros 952'
          , 'errosPasep952.rptdesign');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 12
          , 'Erros 959'
          , 'errosPasep959.rptdesign');


-----------
--Ticket #12341
-----------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_cabecalho_909 (
  num_ocorrencia INTEGER      NOT NULL,
  posicao        INTEGER      NOT NULL,
  descricao      VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorencia_remessa PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_cabecalho_909 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_cabecalho_909'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (1 , 40, \'Tipo de registro diferente de ?1?                                                                                                                   \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (2 , 41, \'Nome do arquivo diferente de ?FPSF900?                                                                                                              \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (3 , 42, \'Data da geração do arquivo não informada ou não numérica ou inválida                                                                                \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (4 , 43, \'Data da geração do arquivo fora do exercício Pasep corrente                                                                                         \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (5 , 44, \'Entidade não cadastrada no Pasep ou com cadastro inativo                                                                                            \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (6 , 45, \'Número da remessa não informado ou não numérico                                                                                                     \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (7 , 46, \'Número da remessa fora de seqüência ou mais de uma remessa na mesma data                                                                            \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (8 , 47, \'Agência de controle não informada ou não numérica                                                                                                   \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (9 , 48, \'Agência de controle inexistente ou inativa                                                                                                          \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (10, 49, \'Dígito-verificador da agência de controle invalido                                                                                                  \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (11, 50, \'Data de pagamento não informada ou não numérica ou inválida                                                                                         \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (12, 51, \'Data de pagamento sem antecedência de 2 dias                                                                                                        \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (13, 52, \'Número de convênio não informado ou não numérico                                                                                                    \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (14, 53, \'Entidade não tem convênio Fopag firmado com o BB                                                                                                    \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (15, 54, \'Número do convênio não confere com o cadastrado                                                                                                     \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (16, 55, \'Código da forma de repasse não informado ou não numérico                                                                                            \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (17, 56, \'Código da forma de repasse diferente de 1, 2 ou 3                                                                                                   \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (18, 57, \'Código da forma de repasse informado é 1, mas outros dados além de agência e conta foram informados desnecessariamente.                             \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (19, 58, \'Código da forma de repasse informado é 2, mas outros dados além de código e dv do Banco de crédito foram informados desnecessariamente.             \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (20, 59, \'Código da forma de repasse informado é 3, mas outros dados além de código de lançamento p/ Tesouro Nacional foram informados desnecessariamente.    \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (21, 60, \'Agência de lançamento não informada ou não numérica                                                                                                 \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (22, 61, \'Dígito-verificador da agência de lançamento inválido                                                                                                \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (23, 62, \'Agência de lançamento inexistente ou inativa                                                                                                        \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (24, 63, \'Conta de lançamento não informada ou não numérica                                                                                                   \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (25, 64, \'Dígito-verificador da conta de lançamento inválido                                                                                                  \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (26, 65, \'Agência/Conta de lançamento não existente ou inativa ou não pertence a pessoa jurídica ou com cadastro inconsistente.                               \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (27, 66, \'Agência/Conta de lançamento não pertence à entidade                                                                                                 \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (28, 67, \'Código de lançamento na conta única do Tesouro Nacional não informado                                                                               \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (29, 68, \'Código de lançamento na conta única do Tesouro Nacional inválido                                                                                    \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (30, 69, \'Código do Banco não informado ou não numérico                                                                                                       \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (31, 70, \'Dígito-verificador do Banco inválido                                                                                                                \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (32, 71, \'Banco inexistente ou inativo                                                                                                                        \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (33, 72, \'Arquivo sem registro trailler                                                                                                                       \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (34, 73, \'Quantidade de registros não informada ou não numérica                                                                                               \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (35, 74, \'Quantidade de registros não confere                                                                                                                 \');
INSERT INTO ima.ocorrencia_cabecalho_909 VALUES (36, 75, \'Arquivo sem nenhum registro válido                                                                                                                  \');
');

-----------
--Ticket #12340
-----------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_detalhe_909_910 (
  num_ocorrencia INTEGER      NOT NULL,
  posicao        INTEGER      NOT NULL,
  descricao      VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorrencia_detalhes PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_detalhe_909_910 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_detalhe_909_910'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (1 , 81, \'Tipo de registro diferente de ?2?                                  \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (2 , 82, \'Inscrição não informada ou não numérica                            \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (3 , 83, \'Inscrição fora da faixa PIS-Pasep                                  \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (4 , 84, \'Inscrição não cadastrada                                           \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (5 , 85, \'Inscrição informada em duplicidade neste arquivo                   \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (6 , 86, \'Inscrição transferida para o PIS                                   \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (7 , 87, \'Inscrição cancelada com remanescente transferida para o PIS        \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (8 , 88, \'Inscrição cancelada ou expurgada                                   \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (9 , 89, \'Nome do participante não informado                                 \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (10, 90, \'Nome do participante não confere com o cadastro do Pasep           \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (11, 91, \'Participante encontra-se como ?falecido? no cadastro do Pasep      \');
INSERT INTO ima.ocorrencia_detalhe_909_910 VALUES (12, 92, \'Pariticipante já foi pago por meio de Pasep-Fopag neste exercício  \');
');

-----------
--Ticket #12344
-----------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_cabecalho_910 (
  num_ocorrencia INTEGER NOT NULL,
  posicao       INTEGER NOT NULL,
  descricao     VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorrencia_cabecalho_910 PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_cabecalho_910 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_cabecalho_910'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_cabecalho_910 VALUES (1, 124, \'Arquivo sem nenhum registro válido                                                                               \');
INSERT INTO ima.ocorrencia_cabecalho_910 VALUES (2, 125, \'Agência/Conta de lançamento não existente, inativa, não pertence a pessoa jurídica ou com cadastro inconsistente \');
INSERT INTO ima.ocorrencia_cabecalho_910 VALUES (3, 126, \'Cdigo de lançamento  na conta única do Tesouro Nacional inválido                                                 \');
');


-----------
--Ticket #12345
-----------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_detalhe_910 (
  num_ocorrencia INTEGER      NOT NULL,
  posicao        INTEGER      NOT NULL,
  descricao      VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorrencia_detalhe_910 PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_detalhe_910 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_detalhe_910'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_detalhe_910 VALUES (1, 87, \' Registro rejeitado previamente                          \');
INSERT INTO ima.ocorrencia_detalhe_910 VALUES (2, 88, \' Participante já foi pago no exercício                   \');
INSERT INTO ima.ocorrencia_detalhe_910 VALUES (3, 89, \' Inscrição cancelada                                     \');
INSERT INTO ima.ocorrencia_detalhe_910 VALUES (4, 90, \' Participante com conta bloqueada                        \');
INSERT INTO ima.ocorrencia_detalhe_910 VALUES (5, 91, \' Participante sem direito a abono salarial ou rendimento \');
');

-----------
--Ticket #12345
-----------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_cabecalho_952 (
  num_ocorrencia INTEGER      NOT NULL,
  posicao        INTEGER      NOT NULL,
  descricao      VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorrencia_cabecalho_952 PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_cabecalho_952 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_cabecalho_952'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_cabecalho_952 VALUES (1, 40, \' Arquivo sem nenhum registro válido                                \');
INSERT INTO ima.ocorrencia_cabecalho_952 VALUES (2, 41, \' Agência/conta de lançamento não existente, inativa, não pertence  \');
INSERT INTO ima.ocorrencia_cabecalho_952 VALUES (3, 42, \' Autorização inativa ou expirada                                   \');
INSERT INTO ima.ocorrencia_cabecalho_952 VALUES (4, 43, \' Código GRU inválido                                               \');
INSERT INTO ima.ocorrencia_cabecalho_952 VALUES (5, 44, \' Código do Banco Inválido                                          \');
');

-----------
--Ticket #12348
-----------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_detalhe_952 (
  num_ocorrencia INTEGER      NOT NULL,
  posicao        INTEGER      NOT NULL,
  descricao      VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorrencia_detalhe_952 PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_detalhe_952 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_detalhe_952'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_detalhe_952 VALUES (1, 87, \'Registro rejeitado previamente             \');
INSERT INTO ima.ocorrencia_detalhe_952 VALUES (2, 88, \'Pagamento do participante já foi devolvido \');
');



-------------
-- Ticket 12366
-------------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_cabecalho_959 (
  num_ocorrencia INTEGER      NOT NULL,
  posicao        INTEGER      NOT NULL,
  descricao      VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorrencia_cabecalho_959 PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_cabecalho_959 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_cabecalho_959'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (1,  40, \'Tipo de registro diferente de ?1?                                        \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (2,  41, \'Nome do arquivo diferente de FPSF950                                     \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (3,  42, \'Data de geração não informada, não numérica ou inválida                  \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (4,  43, \'Data de geração do arquivo fora do exercício Pasep corrente              \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (5,  44, \'Entidade não cadastrada ou com cadastro inativo                          \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (6,  45, \'Número do convênio não informado ou não numérico                         \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (7,  46, \'Entidade sem convênio Fopag firmado com o BB                             \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (8,  47, \'Número do convênio não confere com o cadastrado                          \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (9,  48, \'Número da remessa não informado ou não numérico                          \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (10, 49, \'Número da remessa fora de seqüência ou mais de uma remessa na mesma data \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (11, 50, \'Arquivo sem registro trailer                                             \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (12, 51, \'Quantidade de registros não informada ou não numérica                    \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (13, 52, \'Quantidade de registros não confere                                      \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (14, 53, \'Arquivo sem nenhum registro válido                                       \');
INSERT INTO ima.ocorrencia_cabecalho_959 VALUES (15, 54, \'Entidade não participou da Fopag                                         \');
');

-------------
-- Ticket 12367
-------------

select atualizarBanco ('
CREATE TABLE ima.ocorrencia_detalhe_959 (
  num_ocorrencia INTEGER      NOT NULL,
  posicao        INTEGER      NOT NULL,
  descricao      VARCHAR(180) NOT NULL,
  CONSTRAINT pk_ocorrencia_detalhe_959 PRIMARY KEY(num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.ocorrencia_detalhe_959 TO GROUP urbem;
');

INSERT INTO administracao.tabelas_rh
            (schema_cod
          , nome_tabela
          , sequencia)
     VALUES (7
          , 'ocorrencia_detalhe_959'
          , 1);

select atualizarBanco ('
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (1 , 40, \' Tipo de registro diferente de ?2?                           \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (2 , 41, \' Inscrição não informada ou não numérica                     \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (3 , 42, \' Inscrição fora da faixa Pis-Pasep                           \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (4 , 43, \' Inscrição não cadastrada                                    \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (5 , 44, \' Inscrição em duplicidade neste arquivo                      \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (6 , 45, \' Inscrição cancelada ou expurgada                            \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (7 , 46, \' Inscrição transferida para o Pis                            \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (8 , 47, \' Inscrição cancelada com remanescente transferida para o Pis \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (9 , 48, \' Participante ainda não foi pago                             \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (10, 49, \' Participante foi pago por outra entidade                    \');
INSERT INTO ima.ocorrencia_detalhe_959 VALUES (11, 50, \' Pagamento do participante já foi devolvido                  \');
');

-----------
--Ticket #12339
-----------
select atualizarBanco ('
CREATE TABLE ima.erros_pasep (
  cod_erro       INTEGER NOT NULL,
  num_ocorrencia INTEGER NOT NULL,
  registro       INTEGER NOT NULL,
  nome           VARCHAR(200) NOT NULL,
  pis_pasep      VARCHAR(15) NOT NULL,
  CONSTRAINT pk_erros_pasep PRIMARY KEY(cod_erro),
  CONSTRAINT fk_erros_pasep_1 FOREIGN KEY(num_ocorrencia) REFERENCES ima.ocorrencia_detalhe_909_910 (num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.erros_pasep TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE ima.erros_pasep_910 (
  cod_erro       INTEGER NOT NULL,
  num_ocorrencia INTEGER NOT NULL,
  registro       INTEGER NOT NULL,
  nome           VARCHAR(200) NOT NULL,
  pis_pasep      VARCHAR(15) NOT NULL,
  CONSTRAINT pk_erros_pasep_910 PRIMARY KEY(cod_erro),
  CONSTRAINT fk_erros_pasep_910_1 FOREIGN KEY(num_ocorrencia) REFERENCES ima.ocorrencia_detalhe_910 (num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.erros_pasep_910 TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE ima.erros_pasep_952 (
  cod_erro       INTEGER NOT NULL,
  num_ocorrencia INTEGER NOT NULL,
  registro       INTEGER NOT NULL,
  nome           VARCHAR(200) NOT NULL,
  pis_pasep      VARCHAR(15) NOT NULL,
  CONSTRAINT pk_erros_pasep_952 PRIMARY KEY(cod_erro),
  CONSTRAINT fk_erros_pasep_952_1 FOREIGN KEY(num_ocorrencia) REFERENCES ima.ocorrencia_detalhe_952 (num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.erros_pasep_952 TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE ima.erros_pasep_959 (
  cod_erro       INTEGER NOT NULL,
  num_ocorrencia INTEGER NOT NULL,
  registro       INTEGER NOT NULL,
  nome           VARCHAR(200) NOT NULL,
  pis_pasep      VARCHAR(15) NOT NULL,
  CONSTRAINT pk_erros_pasep_959 PRIMARY KEY(cod_erro),
  CONSTRAINT fk_erros_pasep_959_1 FOREIGN KEY(num_ocorrencia) REFERENCES ima.ocorrencia_detalhe_959 (num_ocorrencia)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.erros_pasep_959 TO GROUP urbem;
');


-----------
--Ticket #12361
-----------

select atualizarBanco ('
CREATE TABLE ima.conferencia_910 (
  cod_conferencia INTEGER       NOT NULL,
  cod_contrato    INTEGER       NOT NULL,
  valor_pasep     NUMERIC(15,2) NOT NULL,
  CONSTRAINT pk_conferencia_910 PRIMARY KEY(cod_conferencia),
  CONSTRAINT fk_conferencia_910_1 FOREIGN KEY(cod_contrato) REFERENCES pessoal.contrato(cod_contrato)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.conferencia_910 TO GROUP urbem;
');

-----------
--Ticket #12323
-----------

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 409
          , 40
          , 'Consignação'
          , 'instancias/consignacao/'
          , 10
      WHERE 0 = (SELECT COUNT(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 409);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2229
          , 409
          , 'FLCreditoBanrisul.php'
          , 'exportar'
          , 1
          , ''
          , 'Crédito Banrisul');
------------
-- Ticket #12419
------------

select atualizarBanco ('
CREATE TABLE ima.consignacao_banrisul_liquido (
  cod_evento INTEGER NOT NULL,
  CONSTRAINT pk_consignacao_banrisul_liquido PRIMARY KEY(cod_evento),
  CONSTRAINT fk_consignacao_banrisul_liquido FOREIGN KEY(cod_evento) REFERENCES folhapagamento.evento(cod_evento)
);
');

select atualizarBanco ('
CREATE TABLE ima.consignacao_banrisul_remuneracao (
  cod_evento INTEGER NOT NULL,
  CONSTRAINT pk_consignacao_banrisul_remuneracao PRIMARY KEY(cod_evento),
  CONSTRAINT fk_consignacao_banrisul_remuneracao_1 FOREIGN KEY(cod_evento) REFERENCES folhapagamento.evento(cod_evento)
);
');

select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON ima.consignacao_banrisul_remuneracao TO GROUP urbem;
');
select atualizarBanco ('
GRANT INSERT, DELETE, UPDATE, SELECT ON  ima.consignacao_banrisul_liquido TO GROUP urbem;
');

------------
-- Ticket 12418
------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2233
          , 354
          , 'FMConsignacaoBanrisul.php'
          , 'configurar'
          , 19
          , ''
          , 'Consignação Banrisul');

------------
-- Ticket #12494
------------

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 27
          , 17
          , 'Recibo de Pensão Judicial'
          , 'reciboPensaoJudicial.rptdesign');

------------
-- Ticket #12493
------------

UPDATE administracao.acao
   SET ordem = 16
 WHERE cod_acao = 1839;

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2242
          , 276
          , 'FLReciboPensaoJudicial.php'
          , 'imprimir'
          , 17
          , ''
          , 'Recibo de Pensão Judicial');

-------------------
-- Ticket 12679
-------------------
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       DROP CONSTRAINT fk_configuracao_empenho_evento_subdivisao_1;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       DROP CONSTRAINT pk_configuracao_empenho_evento_subdivisao;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_local            DROP CONSTRAINT fk_configuracao_empenho_evento_local_1;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_local            DROP CONSTRAINT pk_configuracao_empenho_evento_local;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   DROP CONSTRAINT pk_configuracao_empenho_evento_atributo_valor;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   DROP CONSTRAINT fk_configuracao_empenho_evento_atributo_valor_1;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         DROP CONSTRAINT fk_configuracao_empenho_evento_atributo_1;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         DROP CONSTRAINT pk_configuracao_empenho_evento_atributo;
');

select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          DROP CONSTRAINT fk_configuracao_empenho_evento_lotacao_1;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          DROP CONSTRAINT pk_configuracao_empenho_evento_lotacao;
');

select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento DROP CONSTRAINT pk_configuracao_empenho_evento;
');

select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento RENAME COLUMN exercicio TO exercicio_pao;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento ADD COLUMN exercicio CHAR(4);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento ADD COLUMN sequencia INTEGER;
');

select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao      ADD COLUMN sequencia INTEGER;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_local           ADD COLUMN sequencia INTEGER;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo        ADD COLUMN sequencia INTEGER;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor  ADD COLUMN sequencia INTEGER;
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao         ADD COLUMN sequencia INTEGER;
');
select atualizarBanco ('
UPDATE folhapagamento.configuracao_empenho_evento_subdivisao     SET sequencia = 1;
');
select atualizarBanco ('
UPDATE folhapagamento.configuracao_empenho_evento_local          SET sequencia = 1;
');
select atualizarBanco ('
UPDATE folhapagamento.configuracao_empenho_evento_atributo       SET sequencia = 1;
');
select atualizarBanco ('
UPDATE folhapagamento.configuracao_empenho_evento_atributo_valor SET sequencia = 1;
');
select atualizarBanco ('
UPDATE folhapagamento.configuracao_empenho_evento_lotacao        SET sequencia = 1;
');
select atualizarBanco ('
UPDATE folhapagamento.configuracao_empenho_evento SET sequencia = 1;
');
select atualizarBanco ('
UPDATE folhapagamento.configuracao_empenho_evento SET exercicio = exercicio_pao;
');

select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento ADD CONSTRAINT pk_configuracao_empenho_evento PRIMARY KEY(exercicio, cod_evento, cod_configuracao, sequencia);
');

select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao ADD CONSTRAINT fk_configuracao_empenho_evento_subdivisao_1 FOREIGN KEY (exercicio, cod_evento, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho_evento(exercicio, cod_evento, cod_configuracao, sequencia);
');

select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_local ADD CONSTRAINT fk_configuracao_empenho_evento_local_1 FOREIGN KEY (exercicio, cod_evento, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho_evento(exercicio, cod_evento, cod_configuracao, sequencia);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo ADD CONSTRAINT fk_configuracao_empenho_evento_atributo_1 FOREIGN KEY (exercicio, cod_evento, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho_evento(exercicio, cod_evento, cod_configuracao, sequencia);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao ADD CONSTRAINT fk_configuracao_empenho_evento_lotacao_1 FOREIGN KEY (exercicio, cod_evento, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho_evento(exercicio, cod_evento, cod_configuracao, sequencia);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       ADD CONSTRAINT pk_configuracao_empenho_evento_subdivisao PRIMARY KEY(exercicio, cod_evento, cod_configuracao, sequencia, cod_sub_divisao);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_local            ADD CONSTRAINT pk_configuracao_empenho_evento_local PRIMARY KEY(exercicio, cod_evento, cod_configuracao, sequencia, cod_local);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         ADD CONSTRAINT pk_configuracao_empenho_evento_atributo PRIMARY KEY(cod_cadastro, cod_modulo, cod_atributo, exercicio, cod_evento, cod_configuracao, sequencia);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   ADD CONSTRAINT pk_configuracao_empenho_evento_atributo_valor PRIMARY KEY(exercicio, cod_evento, cod_configuracao, sequencia, cod_atributo, cod_modulo, cod_cadastro, valor);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   ADD CONSTRAINT fk_configuracao_empenho_evento_atributo_valor_1 FOREIGN KEY (exercicio, cod_evento, cod_configuracao, sequencia, cod_atributo, cod_modulo, cod_cadastro) REFERENCES folhapagamento.configuracao_empenho_evento_atributo(exercicio, cod_evento, cod_configuracao, sequencia, cod_atributo, cod_modulo, cod_cadastro);
');
select atualizarBanco ('
ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          ADD CONSTRAINT pk_configuracao_empenho_evento_lotacao PRIMARY KEY(exercicio, cod_evento, cod_configuracao, sequencia, cod_orgao);
');

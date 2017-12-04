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
* Versao 2.03.2
*
* Fabio Bertoldi - 20141001
*
*/

----------------
-- Ticket #22133
----------------

CREATE SCHEMA tcepe;

CREATE TABLE tcepe.codigo_fonte_tce(
    cod_fonte       INTEGER         NOT NULL,
    descricao       VARCHAR(90)     NOT NULL,
    CONSTRAINT pk_codigo_fonte_tce  PRIMARY KEY (cod_fonte)
);
GRANT ALL ON tcepe.codigo_fonte_tce TO urbem;

INSERT INTO tcepe.codigo_fonte_tce VALUES ( 5, 'Recursos do FUNDEB - Magistério'                                                );
INSERT INTO tcepe.codigo_fonte_tce VALUES ( 6, 'Recursos do FUNDEB - Outras Despesas'                                           );
INSERT INTO tcepe.codigo_fonte_tce VALUES ( 9, 'Recursos Transferidos pelo SUS'                                                 );
INSERT INTO tcepe.codigo_fonte_tce VALUES (12, 'Recursos de Contribuições para o RPPS (Patronal, servidores e comp. financeira)');
INSERT INTO tcepe.codigo_fonte_tce VALUES (13, 'Recursos Ordinários - Não vinculados)'                                          );
INSERT INTO tcepe.codigo_fonte_tce VALUES (16, 'Recursos Transferidos pelo FNAS'                                                );
INSERT INTO tcepe.codigo_fonte_tce VALUES (17, 'Impostos e Transferências Educação - MDE'                                       );
INSERT INTO tcepe.codigo_fonte_tce VALUES (18, 'Impostos e Transferências Saúde'                                                );
INSERT INTO tcepe.codigo_fonte_tce VALUES (19, 'Transferências da CIDE'                                                         );
INSERT INTO tcepe.codigo_fonte_tce VALUES (20, 'Alienação de Bens'                                                              );
INSERT INTO tcepe.codigo_fonte_tce VALUES (21, 'Recursos do Salário-Educação'                                                   );
INSERT INTO tcepe.codigo_fonte_tce VALUES (22, 'Recursos do Programa Dinheiro Direto na Escola – PDDE'                          );
INSERT INTO tcepe.codigo_fonte_tce VALUES (23, 'Recursos do Programa Nacional de Alimentação Escolar – PNAE'                    );
INSERT INTO tcepe.codigo_fonte_tce VALUES (24, 'Recursos do Programa Nacional de Apoio ao Transporte Escolar – PNATE'           );
INSERT INTO tcepe.codigo_fonte_tce VALUES (25, 'Outras Transferências do FNDE'                                                  );
INSERT INTO tcepe.codigo_fonte_tce VALUES (26, 'Transferências de Convênios – Educação'                                         );
INSERT INTO tcepe.codigo_fonte_tce VALUES (27, 'Transferências de Convênios – Saúde'                                            );
INSERT INTO tcepe.codigo_fonte_tce VALUES (28, 'Transferências de Outros Convênios'                                             );
INSERT INTO tcepe.codigo_fonte_tce VALUES (29, 'Recursos de Serviços de Saúde'                                                  );
INSERT INTO tcepe.codigo_fonte_tce VALUES (30, 'Operações de Crédito – Educação'                                                );
INSERT INTO tcepe.codigo_fonte_tce VALUES (31, 'Operações de Crédito – Saúde'                                                   );
INSERT INTO tcepe.codigo_fonte_tce VALUES (32, 'Outras Operações de Crédito'                                                    );
INSERT INTO tcepe.codigo_fonte_tce VALUES (99, 'Outras Fontes'                                                                  );


CREATE TABLE tcepe.codigo_fonte_recurso(
    cod_recurso             INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    cod_fonte               INTEGER         NOT NULL,
    CONSTRAINT pk_codigo_fonte_recurso      PRIMARY KEY                      (cod_recurso, exercicio),
    CONSTRAINT fk_codigo_fonte_recurso_1    FOREIGN KEY                      (cod_recurso, exercicio)
                                            REFERENCES orcamento.recurso     (cod_recurso, exercicio),
    CONSTRAINT fk_codigo_fonte_recurso_2    FOREIGN KEY                      (cod_fonte)
                                            REFERENCES tcepe.codigo_fonte_tce(cod_fonte)
);
GRANT ALL ON tcepe.codigo_fonte_recurso TO urbem;



----------------
-- Ticket #22136
----------------

CREATE TABLE tcepe.tipo_documento(
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_documento    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcepe.tipo_documento TO urbem;

INSERT INTO tcepe.tipo_documento VALUES (1, 'Nota Fiscal');
INSERT INTO tcepe.tipo_documento VALUES (9, 'Outros'     );

CREATE TABLE tcepe.documento(
    exercicio       CHAR(4)          NOT NULL,
    cod_nota        INTEGER          NOT NULL,
    cod_entidade    INTEGER          NOT NULL,
    cod_tipo        INTEGER          NOT NULL,
    nro_documento   VARCHAR(20)      NOT NULL,
    serie           VARCHAR(5)       NOT NULL,
    cod_uf          INTEGER          NOT NULL,
    CONSTRAINT pk_documento         PRIMARY KEY                         (exercicio, cod_nota, cod_entidade),
    CONSTRAINT fk_documento_1       FOREIGN KEY                         (exercicio, cod_nota, cod_entidade)
                                    REFERENCES empenho.nota_liquidacao  (exercicio, cod_nota, cod_entidade),
    CONSTRAINT fk_documento_2       FOREIGN KEY                         (cod_tipo)
                                    REFERENCES tcepe.tipo_documento     (cod_tipo),
    CONSTRAINT fk_documento_3       FOREIGN KEY                         (cod_uf)
                                    REFERENCES sw_uf                    (cod_uf)
);
GRANT ALL ON tcepe.documento TO urbem;


----------------
-- Ticket #20570
----------------

ALTER TABLE tceal.documento ADD CONSTRAINT fk_documento_2 FOREIGN KEY                     (cod_tipo)
                                                          REFERENCES tceal.tipo_documento (cod_tipo);


----------------
-- Ticket #19759
----------------

CREATE TABLE contabilidade.plano_conta_encerrada(
    cod_conta               INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    dt_encerramento         DATE            NOT NULL,
    motivo                  TEXT            NOT NULL,
    CONSTRAINT pk_plano_conta_encerrada     PRIMARY KEY                         (cod_conta, exercicio),
    CONSTRAINT fk_plano_conta_encerrada_1   FOREIGN KEY                         (cod_conta, exercicio)
                                            REFERENCES contabilidade.plano_conta(cod_conta, exercicio)
);
GRANT ALL ON contabilidade.plano_conta_encerrada TO urbem;

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
     ( 2997
     , 57
     , 'FLEncerrarConta.php'
     , 'encerrar'
     , 7
     , ''
     , 'Encerrar Conta'
     , TRUE
     );


----------------
-- Ticket #22240
----------------

CREATE TABLE tcepe.tipo_transferencia(
    cod_tipo        INTEGER             NOT NULL,
    descricao       VARCHAR(250)        NOT NULL,
    CONSTRAINT pk_tipo_transferencia    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcepe.tipo_transferencia TO urbem;

INSERT INTO tcepe.tipo_transferencia VALUES(1, 'Duodécimo Câmara'           );
INSERT INTO tcepe.tipo_transferencia VALUES(2, 'Transferência FMS'          );
INSERT INTO tcepe.tipo_transferencia VALUES(3, 'Transferência FMAS'         );
INSERT INTO tcepe.tipo_transferencia VALUES(4, 'Devolução de Recursos'      );
INSERT INTO tcepe.tipo_transferencia VALUES(5, 'Outros Repasses Financeiros');
INSERT INTO tcepe.tipo_transferencia VALUES(6, 'Transferência RPPS'         );


CREATE TABLE tcepe.tipo_transferencia_concedida(
    cod_lote                    INTEGER   NOT NULL,
    cod_entidade                INTEGER   NOT NULL,
    exercicio                   CHAR(4)   NOT NULL,
    tipo                        CHAR(1)   NOT NULL,
    cod_tipo_tcepe              INTEGER   NOT NULL,
    cod_entidade_beneficiada    INTEGER   NOT NULL,
    CONSTRAINT pk_tipo_transf_concedida   PRIMARY KEY         (cod_lote, cod_entidade, exercicio, tipo, cod_tipo_tcepe),
    CONSTRAINT fk_tipo_transf_concedida_1 FOREIGN KEY                         (cod_lote, cod_entidade, exercicio, tipo)
                                          REFERENCES tesouraria.transferencia (cod_lote, cod_entidade, exercicio, tipo),
    CONSTRAINT fk_tipo_transf_concedida_2 FOREIGN KEY                         (cod_tipo_tcepe)
                                          REFERENCES tcepe.tipo_transferencia (cod_tipo),
    CONSTRAINT fk_tipo_transf_concedida_3 FOREIGN KEY                         (exercicio, cod_entidade_beneficiada)
                                          REFERENCES orcamento.entidade       (exercicio, cod_entidade)
);
GRANT ALL ON tcepe.tipo_transferencia_concedida TO urbem;


----------------
-- Ticket #22241
----------------

CREATE TABLE tcepe.tipo_transferencia_recebida(
    cod_lote                    INTEGER   NOT NULL,
    cod_entidade                INTEGER   NOT NULL,
    exercicio                   CHAR(4)   NOT NULL,
    tipo                        CHAR(1)   NOT NULL,
    cod_tipo_tcepe              INTEGER   NOT NULL,
    cod_entidade_transferidora  INTEGER   NOT NULL,
    CONSTRAINT pk_tipo_transf_recebida    PRIMARY KEY         (cod_lote, cod_entidade, exercicio, tipo, cod_tipo_tcepe),
    CONSTRAINT fk_tipo_transf_recebida_1  FOREIGN KEY                         (cod_lote, cod_entidade, exercicio, tipo)
                                          REFERENCES tesouraria.transferencia (cod_lote, cod_entidade, exercicio, tipo),
    CONSTRAINT fk_tipo_transf_recebida_2  FOREIGN KEY                         (cod_tipo_tcepe)
                                          REFERENCES tcepe.tipo_transferencia (cod_tipo),
    CONSTRAINT fk_tipo_transf_recebida_3  FOREIGN KEY                         (exercicio, cod_entidade_transferidora)
                                          REFERENCES orcamento.entidade       (exercicio, cod_entidade)
);
GRANT ALL ON tcepe.tipo_transferencia_recebida TO urbem;


----------------
-- Ticket #22239
----------------

CREATE TABLE tcepe.tipo_conta_banco (
    cod_tipo_conta_banco    INTEGER         NOT NULL,
    descricao               VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tcepe_tipo_conta_banco    PRIMARY KEY (cod_tipo_conta_banco)
);
GRANT ALL ON tcepe.tipo_conta_banco TO urbem;

INSERT INTO tcepe.tipo_conta_banco VALUES (1, 'Conta Corrente'           );
INSERT INTO tcepe.tipo_conta_banco VALUES (2, 'Conta Aplicação Corrente' );
INSERT INTO tcepe.tipo_conta_banco VALUES (3, 'Conta Poupança'           );
INSERT INTO tcepe.tipo_conta_banco VALUES (4, 'Conta Salário'            );
INSERT INTO tcepe.tipo_conta_banco VALUES (5, 'Conta Vinculada'          );
INSERT INTO tcepe.tipo_conta_banco VALUES (6, 'Conta Aplicação Vinculada');


CREATE TABLE tcepe.plano_banco_tipo_conta_banco (
    exercicio               CHAR(4)     NOT NULL,
    cod_plano               INTEGER     NOT NULL,
    cod_tipo_conta_banco    INTEGER     NOT NULL,
    CONSTRAINT pk_plano_banco_tipo_conta_banco      PRIMARY KEY   (exercicio, cod_plano, cod_tipo_conta_banco),
    CONSTRAINT fk_plano_banco_tipo_conta_banco_2    FOREIGN KEY                         (exercicio, cod_plano)
                                                    REFERENCES contabilidade.plano_banco(exercicio, cod_plano),
    CONSTRAINT fk_plano_banco_tipo_conta_banco_1    FOREIGN KEY                         (cod_tipo_conta_banco)
                                                    REFERENCES tcepe.tipo_conta_banco   (cod_tipo_conta_banco)
);
GRANT ALL ON tcepe.plano_banco_tipo_conta_banco TO urbem;


----------------
-- Ticket #22242
----------------

CREATE TABLE tcepe.modalidade_despesa (
    exercicio           CHAR(4)         NOT NULL,
    cod_modalidade      INTEGER         NOT NULL,
    modalidade          VARCHAR(90)     NOT NULL,
    CONSTRAINT pk_modalidade_despesa    PRIMARY KEY (exercicio, cod_modalidade)
);
GRANT ALL ON TABLE tcepe.modalidade_despesa TO urbem;

CREATE TABLE tcepe.orcamento_modalidade_despesa (
    exercicio           CHAR(4)     NOT NULL,
    cod_despesa         INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    cod_modalidade      INTEGER     NOT NULL,
    CONSTRAINT pk_orcamento_modalidade_despesa   PRIMARY KEY            (exercicio, cod_despesa, cod_entidade, cod_modalidade),
    CONSTRAINT fk_orcamento_modalidade_despesa_1 FOREIGN KEY                         (exercicio, cod_despesa)
                                                 REFERENCES orcamento.despesa        (exercicio, cod_despesa),
    CONSTRAINT fk_orcamento_modalidade_despesa_2 FOREIGN KEY                         (exercicio, cod_entidade)
                                                 REFERENCES orcamento.entidade       (exercicio, cod_entidade),
    CONSTRAINT fk_orcamento_modalidade_despesa_3 FOREIGN KEY                         (exercicio, cod_modalidade)
                                                 REFERENCES tcepe.modalidade_despesa (exercicio, cod_modalidade)
);
GRANT ALL ON TABLE tcepe.orcamento_modalidade_despesa TO urbem;

    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 01, 'Aposentadorias do RPPS, Reserva Remunerada e Reformas dos Militares');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 03, 'Pensões do RPPS e do militar');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 04, 'Contratação por Tempo Determinado');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 05, 'Outros Benefícios Previdenciários do servidor ou do militar');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 06, 'Beneficio Mensal ao Deficiente e ao Idoso');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 07, 'Contribuição a Entidades Fechadas de Previdência');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 08, 'Outros Benefícios Assistenciais do servidor e do militar');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 10, 'Seguro Desemprego e Abono Salarial');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 11, 'Vencimentos e Vantagens Fixas - Pessoal Civil');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 12, 'Vencimentos e Vantagens Fixas - Pessoal Militar');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 13, 'Obrigações Patronais');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 14, 'Diárias - Civil');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 15, 'Diárias - Militar');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 16, 'Outras Despesas Variáveis - Pessoal Civil');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 17, 'Outras Despesas Variáveis - Pessoal Militar');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 18, 'Auxílio Financeiro a Estudantes');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 19, 'Auxílio Fardamento');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 20, 'Auxílio Financeiro a Pesquisadores');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 21, 'Juros sobre a Dívida por Contrato');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 22, 'Outros Encargos sobre a Dívida por Contrato');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 23, 'Juros, Deságios e Descontos da Dívida Mobiliária');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 24, 'Outros Encargos sobre a Dívida Mobiliária');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 25, 'Encargos sobre Operações de Crédito por Antecipação da Receita');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 26, 'Obrigações Decorrentes de Política Monetária');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 27, 'Encargos pela Honra de Avais, Garantias, Seguros e Similares');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 28, 'Remuneração de Cotas de Fundos Autárquicos');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 29, 'Distribuição de Resultado de Empresas Estatais Dependentes');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 30, 'Material de Consumo');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 31, 'Premiações Culturais, Artísticas, Científicas, Desportivas e Outras');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 32, 'Material, Bem ou Serviço para Distribuição Gratuita');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 33, 'Passagens e Despesas com Locomoção');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 34, 'Outras Despesas de Pessoal Decorrentes de Contratos de Terceirização');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 35, 'Serviços de Consultoria');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 36, 'Outros Serviços de Terceiros – Pessoa Física');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 37, 'Locação de Mão-de-Obra');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 38, 'Arrendamento Mercantil');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 39, 'Outros Serviços de Terceiros – Pessoa Jurídica');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 41, 'Contribuições');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 42, 'Auxílios');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 43, 'Subvenções Sociais');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 45, 'Subvenções Econômicas');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 46, 'Auxílio-Alimentação');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 47, 'Obrigações Tributárias e Contributivas');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 48, 'Outros Auxílios Financeiros a Pessoas Físicas');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 49, 'Auxílio-Transporte');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 51, 'Obras e Instalações');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 52, 'Equipamentos e Material Permanente');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 53, 'Aposentadorias do RGPS – Área Rural');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 54, 'Aposentadorias do RGPS – Área Urbana');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 55, 'Pensões do RGPS – Área Rural');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 56, 'Pensões do RGPS – Área Urbana');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 57, 'Outros Benefícios do RGPS – Área Rural');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 58, 'Outros Benefícios do RGPS – Área Urbana');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 59, 'Pensões Especiais');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 61, 'Aquisição de Imóveis');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 62, 'Aquisição de Produtos para Revenda');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 63, 'Aquisição de Títulos de Crédito');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 64, 'Aquisição de Títulos Representativos de Capital já Integralizado');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 65, 'Constituição ou Aumento de Capital de Empresas');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 66, 'Concessão de Empréstimos e Financiamentos');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 67, 'Depósitos Compulsórios');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 70, 'Rateio pela Participação em Consórcio Público');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 71, 'Principal da Dívida Contratual Resgatado');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 72, 'Principal da Dívida Mobiliária Resgatado');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 73, 'Correção Monetária ou Cambial da Dívida Contratual Resgatada');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 74, 'Correção Monetária ou Cambial da Dívida Mobiliária Resgatada');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 75, 'Correção Monetária da Dívida de Operações de Crédito por Antecipação da Receita');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 76, 'Principal Corrigido da Dívida Mobiliária Refinanciado');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 77, 'Principal Corrigido da Dívida Contratual Refinanciado');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 81, 'Distribuição Constitucional ou Legal de Receitas');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 91, 'Sentenças Judiciais');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 92, 'Despesas de Exercícios Anteriores');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 93, 'Indenizações e Restituições');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 94, 'Indenizações e Restituições Trabalhistas');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 95, 'Indenização pela Execução de Trabalhos de Campo');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 96, 'Ressarcimento de Despesas de Pessoal Requisitado');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 97, 'Aporte para Cobertura do Déficit Atuarial do RPPS');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 98, 'Compensações ao RGPS');
    INSERT INTO tcepe.modalidade_despesa VALUES ( '2014', 99, 'A classificar');


----------------
-- Ticket #22246
----------------

INSERT
  INTO administracao.atributo_valor_padrao
     ( cod_modulo
     , cod_cadastro
     , cod_atributo
     , cod_valor
     , ativo
     , valor_padrao
     )
VALUES
     ( 10
     , 1
     , 101
     , 20
     , FALSE
     , 'Dispensa por Valor'
     );


----------------
-- Ticket #19759
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
     ( 3007
     , 57
     , 'FLEncerrarConta.php'
     , 'excluir'
     , 8
     , ''
     , 'Excluir Encerramento de Conta'
     , TRUE
     );


----------------
-- Ticket #20202
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
     ( 3008
     , 61
     , 'FMGerarLancamentoCreditoReceber.php'
     , 'incluir'
     , 11
     , ''
     , 'Gerar Lançamentos Créditos a Receber'
     , TRUE
     );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 850
     , '2014'
     , 'Previsão de crédito tributário a receber'
     , FALSE
     , FALSE
     );


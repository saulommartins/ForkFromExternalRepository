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
* $Id: GF_1950.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.95.0
*/

-------------------------------------------
-- CRIACAO DA BASE DE DADOS PARA MODULO PPA
-------------------------------------------

CREATE SCHEMA ppa;

CREATE TABLE ppa.estimativa_orcamentaria_base (
    cod_receita             INTEGER                 NOT NULL,
    cod_estrutural          VARCHAR(30)             NOT NULL,
    descricao               VARCHAR(80)             NOT NULL,
    tipo                    CHAR(1)                 NOT NULL,
    CONSTRAINT pk_estimativa_orcamentaria_base      PRIMARY KEY (cod_receita),
    CONSTRAINT ck_estimativa_orcamentaria_base_1    CHECK (tipo IN ('A','S'))
 );

INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (1 , '1.1.0.0.00.00.00.00.00', 'RECEITAS CORRENTES'                                          , 'S');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (2 , '1.1.0.0.00.00.00.00.00', 'RECEITA TRIBUTÁRIA'                                          , 'S');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (3 , '1.1.1.0.00.00.00.00.00', 'IMPOSTOS'                                                    , 'S');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (4 , '1.1.1.2.00.00.00.00.00', 'Impostos sobre o Patrimônio e a Renda '                      , 'S');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (5 , '1.1.1.2.02.00.00.00.00', 'Imposto sobre a Propriedade Predial e Territorial Urbana'    , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (6 , '1.1.1.2.04.00.00.00.00', 'Imposto sobre a Renda e Proventos de Qualquer Natureza'      , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (7 , '1.1.1.2.08.00.00.00.00', 'Imposto sobre Transmissão de Bens e Direitos sobre Imóveis'  , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (8 , '1.1.1.3.00.00.00.00.00', 'Impostos sobre a Produção e a Circulação'                    , 'S');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (9 , '1.1.1.3.05.01.00.00.00', 'Imposto sobre Serviços de Qualquer Natureza'                 , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (10, '1.1.2.0.00.00.00.00.00', 'TAXAS'                                                       , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (11, '1.1.3.0.00.00.00.00.00', 'CONTRIBUIÇÃO DE MELHORIA'                                    , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (12, '1.2.0.0.00.00.00.00.00', 'RECEITA DE CONTRIBUIÇÕES'                                    , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (13, '1.3.0.0.00.00.00.00.00', 'RECEITA PATRIMONIAL'                                         , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (14, '1.4.0.0.00.00.00.00.00', 'RECEITA AGROPECUÁRIA'                                        , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (15, '1.5.0.0.00.00.00.00.00', 'RECEITA INDUSTRIAL'                                          , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (16, '1.6.0.0.00.00.00.00.00', 'RECEITA DE SERVIÇOS'                                         , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (17, '1.7.0.0.00.00.00.00.00', 'TRANSFERÊNCIAS CORRENTES'                                    , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (18, '1.9.0.0.00.00.00.00.00', 'OUTRAS RECEITAS CORRENTES'                                   , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (19, '2.0.0.0.00.00.00.00.00', 'RECEITAS DE CAPITAL'                                         , 'S');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (20, '2.1.0.0.00.00.00.00.00', 'OPERAÇÕES DE CRÉDITO'                                        , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (21, '2.2.0.0.00.00.00.00.00', 'ALIENAÇÃO DE BENS'                                           , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (22, '2.3.0.0.00.00.00.00.00', 'AMORTIZAÇÃO DE EMPRÉSTIMOS'                                  , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (23, '2.4.0.0.00.00.00.00.00', 'TRANSFERÊNCIAS DE CAPITAL'                                   , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (24, '2.5.0.0.00.00.00.00.00', 'OUTRAS RECEITAS DE CAPITAL'                                  , 'A');
INSERT INTO ppa.estimativa_orcamentaria_base (cod_receita, cod_estrutural, descricao, tipo) VALUES (25, '9.1.0.0.0.00.00.00.00.00', 'DEDUÇÕES DA RECEITA CORRENTE'                              , 'A');


CREATE TABLE ppa.ppa (
  cod_ppa                   INTEGER             NOT NULL,
  timestamp                 TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
  ano_inicio                CHAR(4)             NOT NULL,
  ano_final                 CHAR(4)             NOT NULL,
  valor_total_ppa           NUMERIC(14,2)       NOT NULL,
  destinacao_recurso        BOOLEAN             NOT NULL DEFAULT FALSE,
  importado                 BOOLEAN             NOT NULL DEFAULT FALSE,
  CONSTRAINT pk_ppa                             PRIMARY KEY (cod_ppa)
);

CREATE TABLE ppa.ppa_estimativa_orcamentaria_base (
    cod_ppa                 INTEGER                  NOT NULL,
    cod_receita             INTEGER                  NOT NULL,
    valor                   NUMERIC(14,2)            NOT NULL,
    percentual_ano_1        NUMERIC(5,2)             NOT NULL,
    percentual_ano_2        NUMERIC(5,2)             NOT NULL,
    percentual_ano_3        NUMERIC(5,2)             NOT NULL,
    percentual_ano_4        NUMERIC(5,2)             NOT NULL,
    CONSTRAINT pk_ppa_estimativa_orcamentaria_base   PRIMARY KEY                                    (cod_ppa, cod_receita),
    CONSTRAINT fk_ppa_estimativa_orcamentaria_base_1 FOREIGN KEY                                    (cod_ppa)
                                                     REFERENCES ppa.ppa                             (cod_ppa),
    CONSTRAINT fk_ppa_estimativa_orcamentaria_base_2 FOREIGN KEY                                    (cod_receita)
                                                     REFERENCES ppa.estimativa_orcamentaria_base    (cod_receita)
);

CREATE TABLE ppa.macro_objetivo (
    cod_macro               INTEGER             NOT NULL,
    cod_ppa                 INTEGER             NOT NULL,
    descricao               VARCHAR(450)        NOT NULL,
    CONSTRAINT pk_macro_objetivo                PRIMARY KEY        (cod_macro),
    CONSTRAINT fk_macro_objetivo_1              FOREIGN KEY        (cod_ppa)
                                                REFERENCES ppa.ppa (cod_ppa)
);

CREATE TABLE ppa.programa_setorial (
    cod_setorial            INTEGER             NOT NULL,
    cod_macro               INTEGER             NOT NULL,
    descricao               VARCHAR(450)        NOT NULL,
    CONSTRAINT pk_programa_setorial             PRIMARY KEY                     (cod_setorial),
    CONSTRAINT fk_programa_setorial_1           FOREIGN KEY                     (cod_macro)
                                                REFERENCES ppa.macro_objetivo   (cod_macro)
 );

CREATE TABLE ppa.periodicidade (
  cod_periodicidade         INTEGER             NOT NULL,
  nom_periodicidade         VARCHAR(40)         NOT NULL,
  CONSTRAINT pk_periodicidade                   PRIMARY KEY (cod_periodicidade)
);

INSERT INTO ppa.periodicidade VALUES (1,'Diária'       );
INSERT INTO ppa.periodicidade VALUES (2,'Semanal'      );
INSERT INTO ppa.periodicidade VALUES (3,'Mensal'       );
INSERT INTO ppa.periodicidade VALUES (4,'Bimestral'    );
INSERT INTO ppa.periodicidade VALUES (5,'Trimestral'   );
INSERT INTO ppa.periodicidade VALUES (6,'Quadrimestral');
INSERT INTO ppa.periodicidade VALUES (7,'Semestral'    );
INSERT INTO ppa.periodicidade VALUES (8,'Anual'        );

CREATE TABLE ppa.precisao (
  cod_precisao              INTEGER             NOT NULL,
  nivel                     VARCHAR(50)         NOT NULL,
  CONSTRAINT pk_precisao                        PRIMARY KEY (cod_precisao)
);

INSERT INTO ppa.precisao (cod_precisao, nivel) VALUES (1,'Unidade' );
INSERT INTO ppa.precisao (cod_precisao, nivel) VALUES (2,'Centavos');
INSERT INTO ppa.precisao (cod_precisao, nivel) VALUES (3,'Dezena'  );
INSERT INTO ppa.precisao (cod_precisao, nivel) VALUES (4,'Centena' );
INSERT INTO ppa.precisao (cod_precisao, nivel) VALUES (5,'Milhar'  );

CREATE TABLE ppa.ppa_precisao (
    cod_ppa                 INTEGER             NOT NULL,
    cod_precisao            INTEGER             NOT NULL,
    CONSTRAINT pk_ppa_precisao                  PRIMARY KEY                                 (cod_ppa),
    CONSTRAINT fk_ppa_precisao_1                FOREIGN KEY                                 (cod_ppa)
                                                REFERENCES ppa.ppa                          (cod_ppa),
    CONSTRAINT fk_ppa_precisao_2                FOREIGN KEY                                 (cod_precisao)
                                                REFERENCES ppa.precisao                     (cod_precisao)
);

CREATE TABLE ppa.ppa_encaminhamento (
  cod_ppa                   INTEGER             NOT NULL,
  cod_periodicidade         INTEGER             NOT NULL,
  dt_encaminhamento         DATE                NOT NULL,
  dt_devolucao              DATE                NOT NULL,
  nro_protocolo             CHAR(9)             NOT NULL,
  CONSTRAINT pk_ppa_encaminhamento              PRIMARY KEY                                 (cod_ppa),
  CONSTRAINT fk_ppa_encaminhamento_1            FOREIGN KEY                                 (cod_ppa)
                                                REFERENCES ppa.ppa                          (cod_ppa),
  CONSTRAINT fk_ppa_encaminhamento_2            FOREIGN KEY                                 (cod_periodicidade)
                                                REFERENCES ppa.periodicidade                (cod_periodicidade)
);

CREATE TABLE ppa.ppa_norma (
  cod_ppa                   INTEGER             NOT NULL,
  cod_norma                 INTEGER             NOT NULL,
  timestamp                 TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
  CONSTRAINT pk_ppa_norma                       PRIMARY KEY                                 (cod_ppa),
  CONSTRAINT fk_ppa_norma_1                     FOREIGN KEY                                 (cod_ppa)
                                                REFERENCES ppa.ppa                          (cod_ppa),
  CONSTRAINT fk_ppa_norma_2                     FOREIGN KEY                                 (cod_norma)
                                                REFERENCES normas.norma                     (cod_norma)
);

CREATE TABLE ppa.ppa_publicacao (
  cod_ppa                   INTEGER             NOT NULL,
  numcgm_veiculo            INTEGER             NOT NULL,
  CONSTRAINT pk_ppa_publicacao                  PRIMARY KEY                                 (cod_ppa),
  CONSTRAINT fk_ppa_publicacao_1                FOREIGN KEY                                 (cod_ppa)
                                                REFERENCES ppa.ppa                          (cod_ppa),
  CONSTRAINT fk_ppa_publicacao_2                FOREIGN KEY                                 (numcgm_veiculo)
                                                REFERENCES licitacao.veiculos_publicidade   (numcgm)
);

CREATE TABLE ppa.produto (
  cod_produto               INTEGER             NOT NULL,
  descricao                 VARCHAR(80)         NOT NULL,
  especificacao             VARCHAR(450)        NOT NULL,
  CONSTRAINT pk_produto                         PRIMARY KEY                                 (cod_produto)
);

CREATE TABLE ppa.programa (
  cod_programa              INTEGER             NOT NULL,
  cod_setorial              INTEGER             NOT NULL,
  ultimo_timestamp_programa_dados  TIMESTAMP    NOT NULL,
  ativo                     BOOLEAN             NOT NULL DEFAULT TRUE,
  CONSTRAINT pk_programa                        PRIMARY KEY                                 (cod_programa),
  CONSTRAINT pk_programa_1                      FOREIGN KEY                                 (cod_setorial)
                                                REFERENCES ppa.programa_setorial            (cod_setorial)
);

CREATE TABLE ppa.tipo_programa (
    cod_tipo_programa       INTEGER             NOT NULL,
    descricao               VARCHAR(150)        NOT NULL,
    CONSTRAINT pk_tipo_programa                 PRIMARY KEY (cod_tipo_programa)
);

INSERT INTO ppa.tipo_programa (cod_tipo_programa, descricao) VALUES (1,'Programa Finalístico'                    );
INSERT INTO ppa.tipo_programa (cod_tipo_programa, descricao) VALUES (2,'Programa de Serviço ao Estado'           );
INSERT INTO ppa.tipo_programa (cod_tipo_programa, descricao) VALUES (3,'Programa de Gestão de Políticas Públicas');
INSERT INTO ppa.tipo_programa (cod_tipo_programa, descricao) VALUES (4,'Programa de Apoio Administrativo'        );


CREATE TABLE ppa.programa_dados (
  cod_programa              INTEGER             NOT NULL,
  timestamp_programa_dados  TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
  cod_tipo_programa         INTEGER             NOT NULL,
  identificacao             VARCHAR(280)        NOT NULL,
  diagnostico               VARCHAR(480)        NOT NULL,
  objetivo                  VARCHAR(480)        NOT NULL,
  diretriz                  VARCHAR(480)        NOT NULL,
  continuo                  BOOLEAN             NOT NULL DEFAULT 'true',
  publico_alvo              VARCHAR(480)            NULL,
  justificativa             VARCHAR(480)        NOT NULL,
  CONSTRAINT pk_programa_dados                  PRIMARY KEY                                 (cod_programa, timestamp_programa_dados),
  CONSTRAINT fk_programa_dados_1                FOREIGN KEY                                 (cod_programa)
                                                REFERENCES ppa.programa                     (cod_programa),
  CONSTRAINT fk_programa_dados_2                FOREIGN KEY                                 (cod_tipo_programa)
                                                REFERENCES ppa.tipo_programa                (cod_tipo_programa)
);

CREATE TABLE ppa.programa_indicadores (
  cod_programa              INTEGER             NOT NULL,
  timestamp_programa_dados  TIMESTAMP           NOT NULL,
  cod_indicador             INTEGER             NOT NULL,
  cod_periodicidade         INTEGER             NOT NULL,
  cod_unidade               INTEGER             NOT NULL,
  cod_grandeza              INTEGER             NOT NULL,
  indice_recente            NUMERIC(14,2)       NOT NULL,
  descricao                 VARCHAR(100)        NOT NULL,
  indice_desejado           NUMERIC(14,2)               ,
  unidade_medida            VARCHAR(100)        NOT NULL,
  fonte                     VARCHAR(100)        NOT NULL,
  formula_calculo           VARCHAR(100)        NOT NULL,
  base_geografica           VARCHAR(100)        NOT NULL,
  
  CONSTRAINT pk_programa_indicadores            PRIMARY KEY                                 (cod_programa, timestamp_programa_dados, cod_indicador),
  CONSTRAINT fk_programa_indicadores_1          FOREIGN KEY                                 (cod_programa, timestamp_programa_dados)
                                                REFERENCES ppa.programa_dados               (cod_programa, timestamp_programa_dados),
  CONSTRAINT fk_programa_indicadores_2          FOREIGN KEY                                 (cod_periodicidade)
                                                REFERENCES ppa.periodicidade                (cod_periodicidade),
  CONSTRAINT fk_programa_indicadores_3          FOREIGN KEY                                 (cod_unidade, cod_grandeza)
                                                REFERENCES administracao.unidade_medida     (cod_unidade, cod_grandeza)
);

CREATE TABLE ppa.programa_norma (
  cod_programa              INTEGER             NOT NULL,
  timestamp_programa_dados  TIMESTAMP           NOT NULL,
  cod_norma                 INTEGER             NOT NULL,
  CONSTRAINT pk_programa_norma                  PRIMARY KEY                                 (cod_programa, timestamp_programa_dados, cod_norma),
  CONSTRAINT fk_programa_norma_1                FOREIGN KEY                                 (cod_programa, timestamp_programa_dados)
                                                REFERENCES ppa.programa_dados               (cod_programa, timestamp_programa_dados),
  CONSTRAINT fk_programa_norma_2                FOREIGN KEY                                 (cod_norma)
                                                REFERENCES normas.norma                     (cod_norma)
);

CREATE TABLE ppa.programa_orgao_responsavel (
  cod_programa              INTEGER             NOT NULL,
  timestamp_programa_dados  TIMESTAMP           NOT NULL,
  exercicio                 CHAR(4)             NOT NULL,
  num_orgao                 INTEGER             NOT NULL,
  CONSTRAINT pk_programa_orgao_responsavel      PRIMARY KEY                                 (cod_programa, timestamp_programa_dados, exercicio, num_orgao),
  CONSTRAINT fk_programa_orgao_responsavel_1    FOREIGN KEY                                 (cod_programa, timestamp_programa_dados)
                                                REFERENCES ppa.programa_dados               (cod_programa, timestamp_programa_dados),
  CONSTRAINT fk_programa_orgao_responsavel_2    FOREIGN KEY                                 (exercicio, num_orgao)
                                                REFERENCES orcamento.orgao                  (exercicio, num_orgao)
);

CREATE TABLE ppa.programa_temporario_vigencia (
  cod_programa              INTEGER             NOT NULL,
  timestamp_programa_dados  TIMESTAMP           NOT NULL,
  dt_inicial                DATE                NOT NULL,
  dt_final                  DATE                NOT NULL,
  valor_global              NUMERIC(14,2)       NOT NULL,
  CONSTRAINT pk_programa_temporario_vigencia    PRIMARY KEY                                 (cod_programa, timestamp_programa_dados),
  CONSTRAINT fk_programa_temporario_vigencia_1  FOREIGN KEY                                 (cod_programa, timestamp_programa_dados)
                                                REFERENCES ppa.programa_dados               (cod_programa, timestamp_programa_dados)
);

CREATE TABLE ppa.regiao (
  cod_regiao                INTEGER             NOT NULL,
  nome                      VARCHAR(80)         NOT NULL,
  descricao                 VARCHAR(240)        NOT NULL,
  CONSTRAINT pk_regiao                          PRIMARY KEY                                 (cod_regiao)
);

CREATE TABLE ppa.tipo_acao (
  cod_tipo                  INTEGER             NOT NULL,
  descricao                 VARCHAR(20)         NOT NULL,
  CONSTRAINT pk_tipo_acao                       PRIMARY KEY                                 (cod_tipo)
);

INSERT INTO ppa.tipo_acao VALUES (1,'Projeto'          );
INSERT INTO ppa.tipo_acao VALUES (2,'Atividade'        );
INSERT INTO ppa.tipo_acao VALUES (3,'Operação Especial');

CREATE TABLE ppa.acao (
  cod_acao                  INTEGER             NOT NULL,
  cod_programa              INTEGER             NOT NULL,
  num_acao                  CHAR(3)             NOT NULL,
  descricao                 VARCHAR(480)        NOT NULL,
  ultimo_timestamp_acao_dados  TIMESTAMP        NOT NULL,
  ativo                     BOOLEAN             NOT NULL DEFAULT TRUE,
  CONSTRAINT pk_acao                            PRIMARY KEY                                 (cod_acao),
  CONSTRAINT fk_acao_1                          FOREIGN KEY                                 (cod_programa)
                                                REFERENCES ppa.programa                     (cod_programa)
);

CREATE TABLE ppa.acao_dados (
  cod_acao                  INTEGER             NOT NULL,
  timestamp_acao_dados      TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
  cod_tipo                  INTEGER             NOT NULL,
  cod_produto               INTEGER             NOT NULL,
  cod_regiao                INTEGER             NOT NULL,
  cod_contrato              INTEGER             NOT NULL,
  exercicio                 CHAR(4)             NOT NULL,
  cod_funcao                INTEGER             NOT NULL,
  cod_subfuncao             INTEGER             NOT NULL,
  cod_grandeza              INTEGER             NOT NULL,
  cod_unidade_medida        INTEGER             NOT NULL,
  exercicio_unidade         CHAR(4)             NOT NULL,
  num_unidade               INTEGER             NOT NULL,
  num_orgao                 INTEGER             NOT NULL,
  CONSTRAINT pk_acao_dados                      PRIMARY KEY                                 (cod_acao, timestamp_acao_dados),
  CONSTRAINT fk_acao_dados_1                    FOREIGN KEY                                 (cod_acao)
                                                REFERENCES ppa.acao                         (cod_acao),
  CONSTRAINT fk_acao_dados_2                    FOREIGN KEY                                 (cod_tipo)
                                                REFERENCES ppa.tipo_acao                    (cod_tipo),
  CONSTRAINT fk_acao_dados_3                    FOREIGN KEY                                 (cod_unidade_medida, cod_grandeza)
                                                REFERENCES administracao.unidade_medida     (cod_unidade, cod_grandeza),
  CONSTRAINT fk_acao_dados_4                    FOREIGN KEY                                 (cod_contrato)
                                                REFERENCES pessoal.contrato                 (cod_contrato),
  CONSTRAINT fk_acao_dados_5                    FOREIGN KEY                                 (cod_funcao, exercicio)
                                                REFERENCES orcamento.funcao                 (cod_funcao, exercicio),
  CONSTRAINT fk_acao_dados_6                    FOREIGN KEY                                 (exercicio, cod_subfuncao)
                                                REFERENCES orcamento.subfuncao              (exercicio, cod_subfuncao),
  CONSTRAINT fk_acao_dados_7                    FOREIGN KEY                                 (cod_regiao)
                                                REFERENCES ppa.regiao                       (cod_regiao),
  CONSTRAINT fk_acao_dados_8                    FOREIGN KEY                                 (cod_produto)
                                                REFERENCES ppa.produto                      (cod_produto),
  CONSTRAINT fk_acao_dados_9                    FOREIGN KEY                                 (exercicio_unidade, num_unidade, num_orgao)
                                                REFERENCES orcamento.unidade                (exercicio, num_unidade, num_orgao)
);


CREATE TABLE ppa.acao_norma (
  cod_acao                  INTEGER             NOT NULL,
  timestamp_acao_dados      TIMESTAMP           NOT NULL,
  cod_norma                 INTEGER             NOT NULL,
  CONSTRAINT pk_acao_norma                      PRIMARY KEY                                 (cod_acao, timestamp_acao_dados, cod_norma),
  CONSTRAINT fk_acao_norma_1                    FOREIGN KEY                                 (cod_acao, timestamp_acao_dados)
                                                REFERENCES ppa.acao_dados                   (cod_acao, timestamp_acao_dados),
  CONSTRAINT fk_acao_norma_2                    FOREIGN KEY                                 (cod_norma)
                                                REFERENCES normas.norma                     (cod_norma)
);

CREATE TABLE ppa.acao_quantidade (
  cod_acao                  INTEGER             NOT NULL,
  timestamp_acao_dados      TIMESTAMP           NOT NULL,
  ano                       CHAR(1)             NOT NULL,
  valor                     NUMERIC(14,2)       NOT NULL,
  CONSTRAINT pk_acao_quantidade                 PRIMARY KEY                                 (cod_acao, timestamp_acao_dados, ano),
  CONSTRAINT fk_acao_quantidade_1               FOREIGN KEY                                 (cod_acao, timestamp_acao_dados)
                                                REFERENCES ppa.acao_dados                   (cod_acao, timestamp_acao_dados)
);

CREATE TABLE ppa.acao_recurso (
  cod_acao                  INTEGER             NOT NULL,
  timestamp_acao_dados      TIMESTAMP           NOT NULL,
  cod_recurso               INTEGER             NOT NULL,
  exercicio_recurso         CHAR(4)             NOT NULL,
  ano                       CHAR(1)             NOT NULL,
  valor                     NUMERIC(14,2)       NOT NULL,
  CONSTRAINT pk_acao_recurso                    PRIMARY KEY                                 (cod_acao, timestamp_acao_dados, cod_recurso, exercicio_recurso, ano),
  CONSTRAINT fk_acao_recurso_1                  FOREIGN KEY                                 (cod_acao, timestamp_acao_dados)
                                                REFERENCES ppa.acao_dados                   (cod_acao, timestamp_acao_dados),
  CONSTRAINT fk_acao_recurso_2                  FOREIGN KEY                                 (cod_recurso, exercicio_recurso)
                                                REFERENCES orcamento.recurso                (cod_recurso, exercicio)
);


GRANT ALL ON SCHEMA ppa                             TO GROUP urbem;

GRANT ALL ON ppa.estimativa_orcamentaria_base       TO GROUP urbem;
GRANT ALL ON ppa.ppa_estimativa_orcamentaria_base   TO GROUP urbem;
GRANT ALL ON ppa.ppa                                TO GROUP urbem;
GRANT ALL ON ppa.macro_objetivo                     TO GROUP urbem;
GRANT ALL ON ppa.programa_setorial                  TO GROUP urbem;
GRANT ALL ON ppa.periodicidade                      TO GROUP urbem;
GRANT ALL ON ppa.precisao                           TO GROUP urbem;
GRANT ALL ON ppa.ppa_precisao                       TO GROUP urbem;
GRANT ALL ON ppa.ppa_encaminhamento                 TO GROUP urbem;
GRANT ALL ON ppa.ppa_norma                          TO GROUP urbem;
GRANT ALL ON ppa.ppa_publicacao                     TO GROUP urbem;
GRANT ALL ON ppa.produto                            TO GROUP urbem;
GRANT ALL ON ppa.programa                           TO GROUP urbem;
GRANT ALL ON ppa.programa_dados                     TO GROUP urbem;
GRANT ALL ON ppa.programa_indicadores               TO GROUP urbem;
GRANT ALL ON ppa.programa_norma                     TO GROUP urbem;
GRANT ALL ON ppa.programa_orgao_responsavel         TO GROUP urbem;
GRANT ALL ON ppa.programa_temporario_vigencia       TO GROUP urbem;
GRANT ALL ON ppa.regiao                             TO GROUP urbem;
GRANT ALL ON ppa.tipo_acao                          TO GROUP urbem;
GRANT ALL ON ppa.acao                               TO GROUP urbem;
GRANT ALL ON ppa.acao_dados                         TO GROUP urbem;
GRANT ALL ON ppa.acao_norma                         TO GROUP urbem;
GRANT ALL ON ppa.acao_quantidade                    TO GROUP urbem;
GRANT ALL ON ppa.acao_recurso                       TO GROUP urbem;
GRANT ALL ON ppa.tipo_programa                      TO GROUP urbem;


CREATE TABLE orcamento.programa_ppa_programa (
  exercicio                 CHAR(4)             NOT NULL,
  cod_programa              INTEGER             NOT NULL,
  cod_programa_ppa          INTEGER             NOT NULL,
  CONSTRAINT pk_programa_ppa_programa           PRIMARY KEY                                 (exercicio, cod_programa, cod_programa_ppa),
  CONSTRAINT pk_programa_ppa_programa_1         FOREIGN KEY                                 (exercicio, cod_programa)
                                                REFERENCES orcamento.programa               (exercicio, cod_programa),
  CONSTRAINT pk_programa_ppa_programa_2         FOREIGN KEY                                 (cod_programa_ppa)
                                                REFERENCES ppa.programa                     (cod_programa)
);

GRANT ALL ON orcamento.programa_ppa_programa  TO GROUP urbem;

CREATE TABLE orcamento.pao_ppa_acao (
    exercicio       CHAR(4)         NOT NULL,
    num_pao         INTEGER         NOT NULL,
    cod_acao        INTEGER         NOT NULL,
    CONSTRAINT pk_pao_ppa_acap      PRIMARY KEY                 (exercicio, num_pao),
    CONSTRAINT fk_pao_ppa_acap_1    FOREIGN KEY                 (exercicio, num_pao)
                                    REFERENCES  orcamento.pao   (exercicio, num_pao),
    CONSTRAINT fk_pao_ppa_acap_2    FOREIGN KEY                 (cod_acao)
                                    REFERENCES ppa.acao         (cod_acao)
 );

GRANT ALL ON orcamento.pao_ppa_acao TO GROUP urbem;


-- MENU --
----------

DELETE
  FROM administracao.permissao
 WHERE cod_acao
    IN (
          SELECT cod_acao
            FROM administracao.acao
           WHERE cod_funcionalidade
              IN (
                   SELECT cod_funcionalidade
                     FROM administracao.funcionalidade
                    WHERE cod_modulo = 43
                 )
       );

DELETE
  FROM administracao.auditoria
 WHERE cod_acao
    IN (
          SELECT cod_acao
            FROM administracao.acao
           WHERE cod_funcionalidade
              IN (
                   SELECT cod_funcionalidade
                     FROM administracao.funcionalidade
                    WHERE cod_modulo = 43
                 )
       );

DELETE
  FROM administracao.acao
 WHERE cod_funcionalidade
    IN (
         SELECT cod_funcionalidade
           FROM administracao.funcionalidade
                    WHERE cod_modulo = 43
       );

DELETE
  FROM administracao.funcionalidade
 WHERE cod_modulo = 43;


ALTER TABLE administracao.funcionalidade ALTER COLUMN nom_diretorio TYPE VARCHAR(50);

-- MENU
-- CONFIGURACAO = 432
INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 432
         , 43
         , 'Configuração'
         , 'instancias/configuracao/'
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
     VALUES ( 2350
          , 432
          , 'FMManterPPA.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir PPA'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2432
          , 432
          , 'LSManterPPA.php'
          , 'listar'
          , 3
          , ''
          , 'Excluir PPA'
          );

-- MACRO OBJETIVOS - 462
INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 462
          , 43
          , 'Macro Objetivos'
          , 'instancias/macroObjetivos/'
          , 2
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2715
          , 462
          , 'FMManterMacroobjetivos.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Macro Objetivos'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2716
          , 462
          , 'FLManterMacroobjetivos.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Macro Objetivos'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2717
          , 462
          , 'FLManterMacroobjetivos.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Macro Objetivos'
          );


-- PROGRAMAS SETORIAIS - 463
INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 463
          , 43
          , 'Programas Setoriais'
          , 'instancias/programasSetoriais/'
          , 3
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2718
          , 463
          , 'FMManterProgramasSetoriais.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Programas Setoriais'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2719
          , 463
          , 'FLManterProgramasSetoriais.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Programas Setoriais'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2720
          , 463
          , 'FLManterProgramasSetoriais.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Programas Setoriais'
          );


-- RELATORIOS
INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
    VALUES( 2
          , 43
          , 1
          , 'Relatório de Regiões'
          , 'relatorioRegioes.rptdesign');

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
    VALUES( 2
          , 43
          , 2
          , 'Relatório de Programa'
          , 'relatorioProgramas.rptdesign');

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
    VALUES( 2
          , 43
          , 4
          , 'Relatório de Ações'
          , 'relatorioAcao.rptdesign');

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
    VALUES( 2
          , 43
          , 5
          , 'Relatório de Metas'
          , 'relatorioMetas.rptdesign');

INSERT INTO administracao.relatorio
          (  cod_gestao
          ,  cod_modulo
          ,  cod_relatorio
          ,  nom_relatorio
          ,  arquivo)
    VALUES( 2
          , 43
          , 6
          , 'Relatório de Programas X Ações'
          , 'relatorioProgramaAcao.rptdesign');

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
    VALUES( 2
          , 43
          , 7
          , 'Relatório de Recurso e Destinação'
          , 'relatorioRecursoDestinacao.rptdesign'); 



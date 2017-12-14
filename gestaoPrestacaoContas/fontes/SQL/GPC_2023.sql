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
* Versao 2.02.3
*
* Fabio Bertoldi - 20140307
*
*/

----------------
-- Ticket #21234
----------------

INSERT 
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES 
     ( 2938
     , 451
     , 'FMManterContrato.php' 
     , 'incluir'
     , 13
     , ''
     , 'Incluir Contrato'
     );

INSERT 
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES 
     ( 2939
     , 451
     , 'FLManterContrato.php'
     , 'alterar'
     , 14
     , ''
     , 'Alterar Contrato'
     );

INSERT 
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES 
     ( 2940
     , 451
     , 'FLManterContrato.php'
     , 'excluir'
     , 15
     , ''
     , 'Excluir Contrato'
     );

CREATE TABLE tcemg.contrato_modalidade_licitacao (
    cod_modalidade_licitacao    INTEGER         NOT NULL,
    descricao                   VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_tcemg_contrato_modalidade_licitacao   PRIMARY KEY (cod_modalidade_licitacao)
);
GRANT ALL ON tcemg.contrato_modalidade_licitacao TO urbem;

INSERT INTO tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao, descricao) VALUES (1, 'Não se Aplica ou Dispensa por valor (art. 24, I e II da Lei 8.666/93)');
INSERT INTO tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao, descricao) VALUES (2, 'Licitação');
INSERT INTO tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao, descricao) VALUES (3, 'Dispensa ou Inexigibilidade');
INSERT INTO tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao, descricao) VALUES (4, 'Adesão à ata de registro de preços');
INSERT INTO tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao, descricao) VALUES (5, 'Licitação realizada por outro órgão ou entidade');
INSERT INTO tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao, descricao) VALUES (6, 'Dispensa ou Inexigibilidade realizada por outro órgão ou entidade');
INSERT INTO tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao, descricao) VALUES (7, 'Licitação - Regime Diferenciado de Contratações Públicas – RDC, conforme Lei nº 12.462/2011');

CREATE TABLE tcemg.contrato_tipo_processo (
    cod_tipo_processo       INTEGER         NOT NULL,
    descricao               VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tcemg_contrato_tipo_processo PRIMARY KEY (cod_tipo_processo)
);

INSERT INTO tcemg.contrato_tipo_processo (cod_tipo_processo, descricao) VALUES (1, 'Dispensa');
INSERT INTO tcemg.contrato_tipo_processo (cod_tipo_processo, descricao) VALUES (2, 'Inexigibilidade');
INSERT INTO tcemg.contrato_tipo_processo (cod_tipo_processo, descricao) VALUES (3, 'Inexigibilidade por credenciamento/chamada pública');
INSERT INTO tcemg.contrato_tipo_processo (cod_tipo_processo, descricao) VALUES (4, 'Dispensa por chamada publica');

CREATE TABLE tcemg.contrato_objeto (
    cod_objeto      INTEGER         NOT NULL,
    descricao       VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tcemg_contrato_objeto PRIMARY KEY (cod_objeto)
);

INSERT INTO tcemg.contrato_objeto (cod_objeto, descricao) VALUES (1, 'Obras e Serviços de Engenharia');
INSERT INTO tcemg.contrato_objeto (cod_objeto, descricao) VALUES (2, 'Compras e serviços');
INSERT INTO tcemg.contrato_objeto (cod_objeto, descricao) VALUES (3, 'Locação');
INSERT INTO tcemg.contrato_objeto (cod_objeto, descricao) VALUES (4, 'Concessão');
INSERT INTO tcemg.contrato_objeto (cod_objeto, descricao) VALUES (5, 'Permissão');

CREATE TABLE tcemg.contrato_instrumento (
    cod_instrumento     INTEGER         NOT NULL,
    descricao           VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tcemg_contrato_instrumento PRIMARY KEY (cod_instrumento)
);

INSERT INTO tcemg.contrato_instrumento (cod_instrumento, descricao) VALUES (1, 'Contrato');
INSERT INTO tcemg.contrato_instrumento (cod_instrumento, descricao) VALUES (2, 'Termos de parceria/OSCIP');
INSERT INTO tcemg.contrato_instrumento (cod_instrumento, descricao) VALUES (3, 'Contratos de gestão');
INSERT INTO tcemg.contrato_instrumento (cod_instrumento, descricao) VALUES (4, 'Outros termos de parceria');

CREATE TABLE tcemg.contrato_garantia (
    cod_garantia    INTEGER         NOT NULL,
    descricao       VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tcemg_contrato_garantia PRIMARY KEY (cod_garantia)
);

INSERT INTO tcemg.contrato_garantia (cod_garantia, descricao) VALUES (1, 'Caução em dinheiro');
INSERT INTO tcemg.contrato_garantia (cod_garantia, descricao) VALUES (2, 'Título da dívida pública');
INSERT INTO tcemg.contrato_garantia (cod_garantia, descricao) VALUES (3, 'Seguro garantia');
INSERT INTO tcemg.contrato_garantia (cod_garantia, descricao) VALUES (4, 'Fiança bancária');
INSERT INTO tcemg.contrato_garantia (cod_garantia, descricao) VALUES (5, 'Sem garantia');

CREATE TABLE tcemg.contrato (
    cod_contrato                INTEGER         NOT NULL,
    cod_entidade                INTEGER         NOT NULL,
    num_orgao                   INTEGER         NOT NULL,
    num_unidade                 INTEGER         NOT NULL,
    nro_contrato                INTEGER         NOT NULL,
    cod_modalidade_licitacao    INTEGER         NOT NULL,
    cod_objeto                  INTEGER         NOT NULL,
    cod_instrumento             INTEGER         NOT NULL,
    exercicio                   CHAR(4)         NOT NULL,    
    objeto_contrato             VARCHAR(500)    NOT NULL,    
    vl_contrato                 NUMERIC(14,2)   NOT NULL,
    numcgm_contratante          INTEGER         NOT NULL,
    numcgm_publicidade          INTEGER         NOT NULL,
    data_assinatura             DATE            NOT NULL,
    data_publicacao             DATE            NOT NULL,
    data_inicio                 DATE            NOT NULL,
    data_final                  DATE            NOT NULL,
    cod_entidade_modalidade     INTEGER                 ,
    cod_tipo_processo           INTEGER                 ,
    cod_garantia                INTEGER                 ,
    num_orgao_modalidade        INTEGER                 ,
    num_unidade_modalidade      INTEGER                 ,
    nro_processo                NUMERIC(5,0)            ,
    exercicio_processo          CHAR(4)                 ,    
    fornecimento                VARCHAR(50)             ,
    pagamento                   VARCHAR(100)            ,
    execucao                    VARCHAR(100)            ,
    multa                       VARCHAR(100)            ,        
    CONSTRAINT pk_tcemg_contrato    PRIMARY KEY                                    (cod_contrato, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_contrato_1  FOREIGN KEY                                    (exercicio, cod_entidade)
                                    REFERENCES orcamento.entidade                  (exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_contrato_2  FOREIGN KEY                                    (exercicio , num_orgao)
                                    REFERENCES orcamento.orgao                     (exercicio , num_orgao),
    CONSTRAINT fk_tcemg_contrato_3  FOREIGN KEY                                    (exercicio , num_unidade, num_orgao)
                                    REFERENCES orcamento.unidade                   (exercicio , num_unidade, num_orgao),
    CONSTRAINT fk_tcemg_contrato_4  FOREIGN KEY                                    (cod_modalidade_licitacao)
                                    REFERENCES tcemg.contrato_modalidade_licitacao (cod_modalidade_licitacao),
    CONSTRAINT fk_tcemg_contrato_5  FOREIGN KEY                                    (cod_tipo_processo)
                                    REFERENCES tcemg.contrato_tipo_processo        (cod_tipo_processo),
    CONSTRAINT fk_tcemg_contrato_6  FOREIGN KEY                                    (cod_objeto)
                                    REFERENCES tcemg.contrato_objeto               (cod_objeto),
    CONSTRAINT fk_tcemg_contrato_7  FOREIGN KEY                                    (cod_instrumento)
                                    REFERENCES tcemg.contrato_instrumento          (cod_instrumento),
    CONSTRAINT fk_tcemg_contrato_8  FOREIGN KEY                                    (cod_garantia)
                                    REFERENCES tcemg.contrato_garantia             (cod_garantia),
    CONSTRAINT fk_tcemg_contrato_9  FOREIGN KEY                                    (numcgm_contratante)
                                    REFERENCES sw_cgm                              (numcgm),
    CONSTRAINT fk_tcemg_contrato_10 FOREIGN KEY                                    (numcgm_publicidade)
                                    REFERENCES licitacao.veiculos_publicidade      (numcgm),
    CONSTRAINT uk_tcemg_contrato    UNIQUE (nro_contrato, exercicio, cod_entidade)
);

CREATE TABLE tcemg.contrato_empenho (
    cod_contrato        INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_empenho         INTEGER     NOT NULL,
    exercicio_empenho   CHAR(4)     NOT NULL,
    CONSTRAINT pk_tcemg_contrato_empenho   PRIMARY KEY                (cod_contrato , exercicio , exercicio_empenho , cod_entidade , cod_empenho ),
    CONSTRAINT fk_tcemg_contrato_empenho_1 FOREIGN KEY                (cod_contrato, exercicio, cod_entidade)
                                           REFERENCES tcemg.contrato  (cod_contrato, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_contrato_empenho_2 FOREIGN KEY                (exercicio_empenho, cod_entidade, cod_empenho)
                                           REFERENCES empenho.empenho (exercicio, cod_entidade, cod_empenho),
    CONSTRAINT uk_tcemg_contrato_empenho   UNIQUE (exercicio_empenho, cod_entidade, cod_empenho)
);

CREATE TABLE tcemg.contrato_fornecedor (
    cod_contrato    INTEGER     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cgm_fornecedor  INTEGER     NOT NULL,
    CONSTRAINT pk_tcemg_contrato_fornecedor   PRIMARY KEY               (cod_contrato, exercicio, cod_entidade, cgm_fornecedor),
    CONSTRAINT fk_tcemg_contrato_fornecedor_1 FOREIGN KEY               (cod_contrato, exercicio, cod_entidade)
                                              REFERENCES tcemg.contrato (cod_contrato, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_contrato_fornecedor_2 FOREIGN KEY (cgm_fornecedor)
                                              REFERENCES compras.fornecedor (cgm_fornecedor),
    CONSTRAINT uk_tcemg_contrato_fornecedor UNIQUE (cod_contrato, exercicio, cod_entidade, cgm_fornecedor)
);

----------------
-- Ticket #18272
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
     ( 2879
     , 403
     , 'FLManterTipoCargo.php'
     , 'configurar'
     , 32
     , ''
     , 'Relacionar Tipo de Cargo'
     , TRUE
     );

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
     ( 2880
     , 403
     , 'FLManterLotacaoFundef.php'
     , 'configurar'
     , 33
     , ''
     , 'Relacionar Lotação Fundef'
     , TRUE
     );

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
     ( 2881
     , 403
     , 'FLManterRemuneracaoBaseFundef.php'
     , 'configurar'
     , 34
     , ''
     , 'Configurar Remuneração Base Fundef'
     , TRUE
     );

CREATE TABLE tcern.descricao_siai(
    cod_siai        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_descricao_siai    PRIMARY KEY (cod_siai)
);

GRANT ALL ON tcern.descricao_siai TO urbem;

INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (01, 'Efetivo'                 );
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (02, 'Celetista'               );
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (03, 'Comissionado'            );
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (04, 'Mandato Eletivo'         );
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (05, 'Contrato Temporário'     );
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (06, 'Estagiário'              );
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (07, 'À Disposição do Órgão'   );
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (08, 'Contrato/Terceirização');
INSERT INTO tcern.descricao_siai (cod_siai, descricao) VALUES (10, 'Outros'                  );

CREATE TABLE tcern.sub_divisao_descricao_siai(
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_sub_divisao         INTEGER     NOT NULL,
    cod_siai                INTEGER     NOT NULL,
    CONSTRAINT pk_sub_divisao_siai      PRIMARY KEY                     (exercicio, cod_entidade, cod_sub_divisao, cod_siai),
    CONSTRAINT fk_sub_divisao_siai_1    FOREIGN KEY                     (exercicio, cod_entidade)
                                        REFERENCES orcamento.entidade   (exercicio, cod_entidade),
    CONSTRAINT fk_sub_divisao_siai_2    FOREIGN KEY                     (cod_sub_divisao)
                                        REFERENCES pessoal.sub_divisao  (cod_sub_divisao),
    CONSTRAINT fk_sub_divisao_siai_3    FOREIGN KEY                     (cod_siai)
                                        REFERENCES tcern.descricao_siai (cod_siai)
);


GRANT ALL ON tcern.sub_divisao_descricao_siai TO urbem;

SELECT atualizarConfiguracao(49, 'lotacao_fundef'         , '');
SELECT atualizarConfiguracao(49, 'remuneracao_base_fundef', '');

----------------
-- Ticket #21453
----------------

CREATE TABLE tcemg.consideracao_arquivo_descricao (
    cod_arquivo     INTEGER             NOT NULL,
    periodo         INTEGER             NOT NULL,
    cod_entidade    INTEGER             NOT NULL,
    exercicio       CHAR(4)             NOT NULL,
    descricao       VARCHAR(3000)               ,    
    CONSTRAINT pk_consideracao_arquivo_descricao    PRIMARY KEY                           (cod_arquivo, periodo, cod_entidade, exercicio),
    CONSTRAINT fk_consideracao_arquivo_descricao_1  FOREIGN KEY                           (cod_arquivo)
                                                    REFERENCES tcemg.consideracao_arquivo (cod_arquivo)
);
GRANT ALL ON tcemg.consideracao_arquivo_descricao TO urbem;

ALTER TABLE tcemg.consideracao_arquivo DROP COLUMN descricao;
ALTER TABLE tcemg.consideracao_arquivo DROP COLUMN periodo;
ALTER TABLE tcemg.consideracao_arquivo DROP COLUMN cod_entidade;

----------------
-- Ticket #21197
----------------

CREATE TABLE tcmgo.configuracao_arquivo_dmr (
    exercicio           CHAR(4)     NOT NULL,
    cod_norma           INTEGER     NOT NULL,
    cod_tipo_decreto    INTEGER     NOT NULL,
    CONSTRAINT pk_tcmgo_configuracao_dmr   PRIMARY KEY             (exercicio, cod_norma),
    CONSTRAINT fk_tcmgo_configuracao_dmr_1 FOREIGN KEY             (cod_norma)
                                           REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcmgo.configuracao_arquivo_dmr TO urbem;

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
     ( 2941
     , 364
     , 'FMManterConfiguracaoDMR.php'
     , ''
     , 1
     , 'DMR – DECRETO MUNICIPAL REGULAMENTADOR DO PREGÃO / REGISTRO DE PREÇOS'
     , 'Configurar Decreto Municipal'
     , TRUE
     );

----------------
-- Ticket #21242
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES 
          ( 2942
          , 451
          , 'FMManterConvenio.php' 
          , 'incluir'
          , 18
          , ''
          , 'Incluir Convênio'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES 
          ( 2943
          , 451
          , 'FLManterConvenio.php'
          , 'alterar'
          , 19
          , ''
          , 'Alterar Convênio'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES 
          ( 2944
          , 451
          , 'FLManterConvenio.php'
          , 'excluir'
          , 20
          , ''
          , 'Excluir Convênio'
          );

CREATE TABLE tcemg.convenio (
    cod_convenio            INTEGER         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    nro_convenio            INTEGER         NOT NULL,
    cod_objeto              INTEGER         NOT NULL,
    data_assinatura         DATE            NOT NULL,
    data_inicio             DATE            NOT NULL,
    data_final              DATE            NOT NULL,
    vl_convenio             NUMERIC(14,2)   NOT NULL,
    vl_contra_partida       NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_tcemg_convenio   PRIMARY KEY               (cod_convenio, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_convenio_1 FOREIGN KEY               (cod_objeto)
                                   REFERENCES compras.objeto (cod_objeto),
    CONSTRAINT uk_tcemg_convenio   UNIQUE (nro_convenio, exercicio, cod_entidade)
);

-------------------------------------------

CREATE TABLE tcemg.convenio_participante (
    cod_convenio                    INTEGER         NOT NULL,
    cod_entidade                    INTEGER         NOT NULL,
    exercicio                       CHAR(4)         NOT NULL,
    cgm_participante                INTEGER         NOT NULL,
    vl_concedido                    NUMERIC(14,2)   NOT NULL,
    percentual                      NUMERIC(5,2)    NOT NULL,
    cod_tipo_participante           INTEGER         NOT NULL,
    num_certificacao_participante   INTEGER         NOT NULL,
    exercicio_participante          CHAR(4)         NOT NULL, 
    CONSTRAINT pk_tcemg_convenio_participante   PRIMARY KEY                                    (cod_convenio, exercicio, cod_entidade, cgm_participante),
    CONSTRAINT fk_tcemg_convenio_participante_1 FOREIGN KEY                                    (cod_convenio, exercicio, cod_entidade)
                                                REFERENCES tcemg.convenio                      (cod_convenio, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_convenio_participante_2 FOREIGN KEY                                    (cod_tipo_participante)
                                                REFERENCES licitacao.tipo_participante         (cod_tipo_participante),
    CONSTRAINT fk_tcemg_convenio_participante_3 FOREIGN KEY                                    (num_certificacao_participante, exercicio_participante, cgm_participante)
                                                REFERENCES licitacao.participante_certificacao (num_certificacao, exercicio, cgm_fornecedor)
);

CREATE TABLE tcemg.convenio_empenho (
    cod_convenio        INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_empenho         INTEGER     NOT NULL,
    exercicio_empenho   CHAR(4)     NOT NULL,
    CONSTRAINT pk_tcemg_convenio_empenho   PRIMARY KEY                (cod_convenio, exercicio, exercicio_empenho, cod_entidade, cod_empenho),
    CONSTRAINT fk_tcemg_convenio_empenho_1 FOREIGN KEY                (cod_convenio, exercicio, cod_entidade)
                                           REFERENCES tcemg.convenio  (cod_convenio, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_convenio_empenho_2 FOREIGN KEY                (exercicio_empenho, cod_entidade, cod_empenho)
                                           REFERENCES empenho.empenho (exercicio, cod_entidade, cod_empenho),
    CONSTRAINT uk_tcemg_convenio_empenho UNIQUE (exercicio_empenho, cod_entidade, cod_empenho)
);

----------------
-- Ticket #21548
----------------

ALTER TABLE tcemg.uniorcam ADD COLUMN cgm_ordenador INTEGER;

----------------
-- Ticket #21487
----------------

ALTER TABLE tcemg.convenio_plano_banco ALTER COLUMN num_convenio  DROP NOT NULL;
ALTER TABLE tcemg.convenio_plano_banco ALTER COLUMN dt_assinatura DROP NOT NULL;

----------------
-- Ticket #21486
----------------

ALTER TABLE tcemg.conta_bancaria DROP CONSTRAINT pk_conta_bancaria;
ALTER TABLE tcemg.conta_bancaria ALTER COLUMN cod_tipo_aplicacao DROP NOT NULL;
ALTER TABLE tcemg.conta_bancaria ADD  CONSTRAINT pk_conta_bancaria PRIMARY KEY (cod_conta, exercicio);

----------------
-- Ticket #21152
----------------

INSERT 
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES 
     ( 2946
     , 451
     , 'FLManterConfiguracaoREGLIC.php' 
     , 'incluir'
     , 19
     , ''
     , 'Configurar Arquivo REGLIC'
     );


----------------
-- Ticket #21152
----------------

CREATE TABLE tcemg.tipo_decreto(
    cod_tipo_decreto    INTEGER         NOT NULL,
    descricao           VARCHAR(70)     NOT NULL,
    CONSTRAINT pk_tipo_decreto PRIMARY KEY (cod_tipo_decreto)
);
GRANT ALL ON tcemg.tipo_decreto TO urbem;

INSERT INTO tcemg.tipo_decreto VALUES (1, 'Registro de Preço');
INSERT INTO tcemg.tipo_decreto VALUES (2, 'Pregão');

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
* Versao 2.02.4
*
* Eduardo Schitz - 20140401
*
*/

----------------
-- Ticket #21302
----------------

UPDATE administracao.acao SET nom_arquivo = 'FLManterConfiguracaoArquivoDDC.php' WHERE cod_acao = 2937;

create table tcemg.configuracao_ddc(
    exercicio                       VARCHAR(4)      NOT NULL,
    mes_referencia                  INTEGER         NOT NULL,
    cod_entidade                    INTEGER         NOT NULL,
    cod_orgao                       INTEGER                 ,
    cod_norma                       INTEGER         NOT NULL,
    nro_contrato_divida             VARCHAR(30)     NOT NULL,
    dt_assinatura                   DATE            NOT NULL,
    contrato_dec_lei                INTEGER                 ,
    objeto_contrato_divida          TEXT            NOT NULL,
    especificacao_contrato_divida   TEXT            NOT NULL,
    tipo_lancamento                 VARCHAR(2)      NOT NULL,
    justificativa_cancelamento      TEXT                    ,
    numcgm                          INTEGER                 ,
    valor_saldo_anterior            NUMERIC(14,2)   NOT NULL,
    valor_contratacao               NUMERIC(14,2)   NOT NULL,
    valor_amortizacao               NUMERIC(14,2)   NOT NULL,
    valor_cancelamento              NUMERIC(14,2)   NOT NULL,
    valor_encampacao                NUMERIC(14,2)   NOT NULL,
    valor_atualizacao               NUMERIC(14,2)   NOT NULL,
    valor_saldo_atual               NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_configuracao_ddc      PRIMARY KEY(exercicio,mes_referencia,cod_entidade,nro_contrato_divida),
    CONSTRAINT fk_configuracao_ddc_1    FOREIGN KEY (numcgm)
                                        REFERENCES sw_cgm(numcgm)
);
GRANT ALL ON tcemg.configuracao_ddc TO urbem;


----------------
-- Ticket #21234
----------------

CREATE TABLE tcemg.contrato_aditivo_tipo(
    cod_tipo_aditivo    integer         NOT NULL,
    descricao           varchar(80)     NOT NULL,
    CONSTRAINT pk_contrato_aditivo_tipo PRIMARY KEY (cod_tipo_aditivo)
);
GRANT ALL ON tcemg.contrato_aditivo_tipo TO urbem;

INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES (  4, 'Reajuste'                                                                      );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES (  5, 'Recomposição (Equilíbrio Financeiro)'                                          );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES (  6, 'Outros'                                                                        );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES (  7, 'Alteração de Prazo de Vigência'                                                );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES (  8, 'Alteração de Prazo de Execução'                                                );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES (  9, 'Acréscimo de Item(ns)'                                                         );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES ( 10, 'Decréscimo de Item(ns)'                                                        );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES ( 11, 'Acréscimo e Decréscimo de Item(ns)'                                            );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES ( 12, 'Alteração de Projeto/Especificação (Art. 65, I, a, da Lei n. 8.666/93)'        );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES ( 13, 'Alteração de Prazo de vigência e Prazo de Execução'                            );
INSERT INTO tcemg.contrato_aditivo_tipo (cod_tipo_aditivo, descricao) VALUES ( 14, 'Acréscimo/Decréscimo de item(ns) conjugado com outros tipos de termos aditivos');


CREATE TABLE tcemg.contrato_aditivo(
    cod_contrato_aditivo    INTEGER         NOT NULL,
    cod_contrato            INTEGER         NOT NULL,
    exercicio_contrato      CHAR(4)         NOT NULL,
    cod_entidade_contrato   INTEGER         NOT NULL,
    nro_aditivo             INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    num_orgao               INTEGER                 ,
    num_unidade             INTEGER                 ,
    data_assinatura         DATE            NOT NULL,
    cod_tipo_valor	        INTEGER         NOT NULL,
    cod_tipo_aditivo        INTEGER         NOT NULL,
    descricao               VARCHAR(250)            ,
    valor                   NUMERIC(14,2)   NOT NULL,
    data_termino            DATE                    ,
    data_publicacao         DATE            NOT NULL,
    cgm_publicacao          INTEGER         NOT NULL,
    CONSTRAINT pk_contrato_aditivo          PRIMARY KEY                                 (cod_contrato_aditivo, exercicio, cod_entidade),
    CONSTRAINT fk_contrato_aditivo_1        FOREIGN KEY                                 (cod_contrato, exercicio_contrato, cod_entidade_contrato)
                                            REFERENCES tcemg.contrato                   (cod_contrato , exercicio , cod_entidade),
    CONSTRAINT fk_contrato_aditivo_2        FOREIGN KEY                                 (exercicio, num_unidade, num_orgao)
                                            REFERENCES orcamento.unidade                (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_contrato_aditivo_3        FOREIGN KEY                                 (cod_tipo_aditivo)
                                            REFERENCES tcemg.contrato_aditivo_tipo      (cod_tipo_aditivo),
    CONSTRAINT fk_contrato_aditivo_4        FOREIGN KEY                                 (cgm_publicacao)
                                            REFERENCES licitacao.veiculos_publicidade   (numcgm)
);
GRANT ALL ON tcemg.contrato_aditivo TO urbem;


CREATE TABLE tcemg.contrato_aditivo_item(
    cod_contrato_aditivo_item   INTEGER     NOT NULL,
    cod_contrato_aditivo        INTEGER     NOT NULL,
    exercicio                   CHAR(4)     NOT NULL,
    cod_entidade                INTEGER     NOT NULL,
    cod_empenho                 INTEGER     NOT NULL,
    exercicio_empenho           CHAR(4)     NOT NULL,
    cod_pre_empenho             INTEGER     NOT NULL,
    exercicio_pre_empenho       CHAR(4)     NOT NULL,
    num_item                    INTEGER     NOT NULL,
    quantidade                  INTEGER     NOT NULL,
    tipo_acresc_decresc         INTEGER             ,
    CONSTRAINT pk_contrato_aditivo_item     PRIMARY KEY                         (cod_contrato_aditivo_item, cod_contrato_aditivo, exercicio, cod_entidade, num_item),
    CONSTRAINT fk_contrato_aditivo_item_1   FOREIGN KEY                         (cod_contrato_aditivo, exercicio, cod_entidade)
                                            REFERENCES tcemg.contrato_aditivo   (cod_contrato_aditivo, exercicio, cod_entidade),
    CONSTRAINT fk_contrato_aditivo_item_2   FOREIGN KEY                         (exercicio_empenho, cod_entidade, cod_empenho)
                                            REFERENCES empenho.empenho          (exercicio , cod_entidade , cod_empenho),
    CONSTRAINT fk_contrato_aditivo_item_3   FOREIGN KEY                         (exercicio_pre_empenho, cod_pre_empenho)
                                            REFERENCES empenho.pre_empenho      (exercicio , cod_pre_empenho),
    CONSTRAINT fk_contrato_aditivo_item_4   FOREIGN KEY                         (exercicio_pre_empenho, cod_pre_empenho, num_item)
                                            REFERENCES empenho.item_pre_empenho (exercicio , cod_pre_empenho, num_item)
);
GRANT ALL ON tcemg.contrato_aditivo_item TO urbem;


----------------
-- Ticket #21656
----------------

GRANT ALL ON tcemg.nota_fiscal_empenho_liquidacao TO urbem;

CREATE TABLE tcemg.nota_fiscal_empenho(
    cod_nota            INTEGER             NOT NULL,
    exercicio           CHAR(4)             NOT NULL,
    cod_entidade        INTEGER             NOT NULL,
    cod_empenho         INTEGER             NOT NULL,
    exercicio_empenho   CHAR(4)             NOT NULL,
    vl_associado        NUMERIC(14,2)       NOT NULL,
    vl_total_liquido    NUMERIC(14,2)       NOT NULL,
    CONSTRAINT pk_nota_fiscal_empenho   PRIMARY KEY                  (cod_nota , exercicio , cod_entidade , cod_empenho),
    CONSTRAINT fk_nota_fiscal_empenho_1 FOREIGN KEY                  (cod_nota, exercicio, cod_entidade)
                                        REFERENCES tcemg.nota_fiscal (cod_nota, exercicio, cod_entidade),
    CONSTRAINT fk_nota_fiscal_empenho_2 FOREIGN KEY                  (cod_empenho, exercicio_empenho, cod_entidade)
                                        REFERENCES empenho.empenho   (cod_empenho,exercicio, cod_entidade)
);
GRANT ALL ON tcemg.nota_fiscal_empenho TO urbem;

ALTER TABLE tcemg.nota_fiscal ALTER COLUMN inscricao_estadual  TYPE VARCHAR(30);
ALTER TABLE tcemg.nota_fiscal ALTER COLUMN inscricao_municipal TYPE VARCHAR(30);


----------------
-- Ticket #21640
----------------

UPDATE administracao.acao SET nom_arquivo = 'FLManterConfiguracaoOrgao.php' WHERE cod_acao = 2914;

CREATE TABLE tcemg.configuracao_orgao(
    cod_entidade            INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    tipo_responsavel        INTEGER     NOT NULL,
    num_cgm                 INTEGER     NOT NULL,
    crc_contador            VARCHAR(11)         ,
    uf_crccontador          VARCHAR(2)          ,
    cargo_ordenador_despesa VARCHAR(50)         ,
    dt_inicio               DATE        NOT NULL,
    dt_fim                  DATE        NOT NULL,
    email                   VARCHAR(35) NOT NULL,
    CONSTRAINT pk_configuracao_orgao    PRIMARY KEY(cod_entidade,exercicio)
);
GRANT ALL ON tcemg.configuracao_orgao TO urbem;

---------------
-- Ticket #21165
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
     ( 2947
     , 451
     , 'FLManterResponsavelLicitacao.php'
     , 'manter'
     , 21
     , ''
     , 'Configurar Responsável Licitação'
     );


----------------
-- Ticket #21165
----------------

CREATE TABLE tcemg.resplic(
    exercicio                       CHAR(4)     NOT NULL,
    cod_entidade                    INTEGER     NOT NULL,
    cod_modalidade                  INTEGER     NOT NULL,
    cod_licitacao                   INTEGER     NOT NULL,
    cgm_resp_abertura_licitacao     INTEGER             ,
    cgm_resp_edital                 INTEGER             ,
    cgm_resp_recurso_orcamentario   INTEGER             ,
    cgm_resp_conducao_licitacao     INTEGER             ,
    cgm_resp_homologacao            INTEGER             ,
    cgm_resp_adjudicacao            INTEGER             ,
    cgm_resp_publicacao             INTEGER             ,
    cgm_resp_avaliacao_bens         INTEGER             ,
    cgm_resp_pesquisa               INTEGER             ,
    CONSTRAINT pk_resplic           PRIMARY KEY                     (exercicio, cod_entidade, cod_modalidade, cod_licitacao),
    CONSTRAINT fk_resplic_1         FOREIGN KEY                     (exercicio, cod_entidade, cod_modalidade, cod_licitacao)
                                    REFERENCES licitacao.licitacao  (exercicio, cod_entidade, cod_modalidade, cod_licitacao),
    CONSTRAINT fk_resplic_2         FOREIGN KEY                     (cgm_resp_abertura_licitacao)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_3         FOREIGN KEY                     (cgm_resp_edital)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_4         FOREIGN KEY                     (cgm_resp_recurso_orcamentario)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_5         FOREIGN KEY                     (cgm_resp_conducao_licitacao)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_6         FOREIGN KEY                     (cgm_resp_homologacao)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_7         FOREIGN KEY                     (cgm_resp_adjudicacao)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_8         FOREIGN KEY                     (cgm_resp_publicacao)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_9         FOREIGN KEY                     (cgm_resp_avaliacao_bens)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplic_10        FOREIGN KEY                     (cgm_resp_pesquisa)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm)

);
GRANT ALL ON tcemg.resplic TO urbem;


----------------
-- Ticket #21548
----------------

ALTER TABLE tcemg.uniorcam ADD CONSTRAINT fk_tcemg_uniorcam_cgm_ordenador FOREIGN KEY (cgm_ordenador)
                                                                          REFERENCES sw_cgm (numcgm);


----------------
-- Ticket #21234
----------------

CREATE TABLE tcemg.contrato_apostila(
    cod_apostila        INTEGER         NOT NULL,
    cod_contrato        INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_tipo            INTEGER         NOT NULL,
    cod_alteracao       INTEGER         NOT NULL,
    descricao           VARCHAR(250)    NOT NULL,
    data_apostila       DATE            NOT NULL,
    valor_apostila      NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_contrato_apostila     PRIMARY KEY               (cod_apostila, cod_contrato , exercicio , cod_entidade ),
    CONSTRAINT fk_contrato_apostila_1   FOREIGN KEY               (cod_contrato, exercicio, cod_entidade)
                                        REFERENCES tcemg.contrato (cod_contrato, exercicio, cod_entidade)
);
GRANT ALL ON tcemg.contrato_apostila TO urbem;


CREATE TABLE tcemg.contrato_rescisao(
    cod_contrato        INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    data_rescisao       DATE            NOT NULL,
    valor_rescisao      NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_contrato_rescisao     PRIMARY KEY               (cod_contrato , exercicio , cod_entidade ),
    CONSTRAINT fk_contrato_rescisao_1   FOREIGN KEY               (cod_contrato, exercicio, cod_entidade)
                                        REFERENCES tcemg.contrato (cod_contrato, exercicio, cod_entidade)
);
GRANT ALL ON tcemg.contrato_rescisao TO urbem;


ALTER TABLE tcemg.contrato ADD COLUMN cgm_signatario integer NOT NULL;
ALTER TABLE tcemg.contrato ADD CONSTRAINT fk_tcemg_contrato_11 FOREIGN KEY                     (cgm_signatario)
                                                               REFERENCES sw_cgm_pessoa_fisica (numcgm);

ALTER TABLE tcemg.contrato_fornecedor ADD COLUMN cgm_representante integer NOT NULL;
ALTER TABLE tcemg.contrato_fornecedor ADD CONSTRAINT fk_tcemg_contrato_fornecedor_3 FOREIGN KEY       (cgm_representante)
                                                                                    REFERENCES sw_cgm (numcgm);



---------------------------------------------
-- REMOVENDO ACAO DUPLICADA - Silvia 20140422
---------------------------------------------

DELETE FROM administracao.auditoria WHERE cod_acao = 2912;
DELETE FROM administracao.permissao WHERE cod_acao = 2912;
DELETE FROM administracao.acao      WHERE cod_acao = 2912;


----------------
-- Ticket #21242
----------------

CREATE TABLE tcemg.convenio_aditivo(
    cod_convenio    INTEGER             NOT NULL,
    cod_entidade    INTEGER             NOT NULL,
    exercicio       CHAR(4)             NOT NULL,
    cod_aditivo     INTEGER             NOT NULL,
    descricao       VARCHAR(500)        NOT NULL,
    data_assinatura DATE                NOT NULL,
    data_final      DATE                        ,
    vl_convenio     NUMERIC(14,2)               ,
    vl_contra       NUMERIC(14,2)               ,
    CONSTRAINT pk_convenio_aditivo      PRIMARY KEY (cod_convenio , exercicio , cod_entidade , cod_aditivo ),
    CONSTRAINT fk_convenio_aditivo_1    FOREIGN KEY (cod_convenio, exercicio, cod_entidade)
                                        REFERENCES tcemg.convenio (cod_convenio, exercicio, cod_entidade)
);
GRANT ALL ON tcemg.convenio_aditivo TO urbem;

ALTER TABLE tcemg.convenio_participante ADD COLUMN esfera CHAR(10) NOT NULL DEFAULT 'Federal';


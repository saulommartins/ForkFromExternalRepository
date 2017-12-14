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
* Versao 2.02.5
*
* Eduardo Schitz - 20140424
*
*/

-------------------------------
-- PERMISSOES PARA SCHEMA tcemg
-------------------------------

GRANT ALL ON SCHEMA tcemg TO urbem;

----------------
-- Ticket #21216
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
     ( 2952
     , 467
     , 'FMConfigurarTipoCertidao.php'
     , 'configurar'
     , 5
     , ''
     , 'Configurar Tipo de Certidão'
     , TRUE
     );

CREATE TABLE tceam.tipo_certidao(
    cod_tipo_certidao   INTEGER         NOT NULL,
    descricao           VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tipo_certidao         PRIMARY KEY (cod_tipo_certidao)
);
GRANT ALL ON tceam.tipo_certidao TO GROUP urbem;

INSERT INTO tceam.tipo_certidao VALUES ( 1, 'INSS');
INSERT INTO tceam.tipo_certidao VALUES ( 2, 'Federal');
INSERT INTO tceam.tipo_certidao VALUES ( 3, 'Estadual');
INSERT INTO tceam.tipo_certidao VALUES ( 4, 'Municipal');
INSERT INTO tceam.tipo_certidao VALUES ( 5, 'FGTS');
INSERT INTO tceam.tipo_certidao VALUES ( 6, 'CAM');
INSERT INTO tceam.tipo_certidao VALUES ( 7, 'CNDT');
INSERT INTO tceam.tipo_certidao VALUES (99, 'Outras Certidões');


CREATE TABLE tceam.tipo_certidao_documento(
    cod_tipo_certidao       INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_tipo_certidao_documento   PRIMARY KEY                    (cod_tipo_certidao, cod_documento),
    CONSTRAINT fk_tipo_certidao_documento_1 FOREIGN KEY                    (cod_tipo_certidao)
                                            REFERENCES tceam.tipo_certidao (cod_tipo_certidao),
    CONSTRAINT fk_tipo_certidao_documento_2 FOREIGN KEY                    (cod_documento)
                                            REFERENCES licitacao.documento (cod_documento)
);
GRANT ALL ON tceam.tipo_certidao_documento TO GROUP urbem;


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
SELECT 2946
     , 451
     , 'FLManterConfiguracaoREGLIC.php'
     , 'incluir'
     , 22
     , ''
     , 'Configurar Arquivo REGLIC'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 2946
           )
     ;

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE tablename  = 'tipo_decreto'
        AND schemaname = 'tcemg'
          ;

    IF NOT FOUND THEN
        CREATE TABLE tcemg.tipo_decreto(
            cod_tipo_decreto    INTEGER         NOT NULL,
            descricao           VARCHAR(70)     NOT NULL,
            CONSTRAINT pk_tipo_decreto PRIMARY KEY (cod_tipo_decreto)
        );
        GRANT ALL ON tcemg.tipo_decreto TO urbem;

        INSERT INTO tcemg.tipo_decreto VALUES (1, 'Registro de Preço');
        INSERT INTO tcemg.tipo_decreto VALUES (2, 'Pregão');
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


CREATE TABLE tcemg.configuracao_reglic(
    exercicio                       CHAR(4)         NOT NULL,
    cod_entidade                    INTEGER         NOT NULL,
    regulamento_art_47              INTEGER         NOT NULL,
    cod_norma                       INTEGER         NOT NULL,
    reg_exclusiva                   INTEGER         NOT NULL,
    artigo_reg_exclusiva            VARCHAR(6)              ,
    valor_limite_reg_exclusiva      NUMERIC(14,2)           ,
    proc_sub_contratacao            INTEGER         NOT NULL,
    artigo_proc_sub_contratacao     VARCHAR(6)              ,
    percentual_sub_contratacao      NUMERIC(6,2)            ,
    criterio_empenho_pagamento      INTEGER         NOT NULL,
    artigo_empenho_pagamento        VARCHAR(6)              ,
    estabeleceu_perc_contratacao    INTEGER         NOT NULL,
    artigo_perc_contratacao         VARCHAR(6)              ,
    percentual_contratacao          NUMERIC(6,2)            ,
    CONSTRAINT pk_configuracao_reglic               PRIMARY KEY                     (exercicio,cod_entidade),
    CONSTRAINT fk_configuracao_reglic_1             FOREIGN KEY                     (exercicio,cod_entidade)
                                                    REFERENCES orcamento.entidade   (exercicio,cod_entidade),
    CONSTRAINT fk_configuracao_reglic_2             FOREIGN KEY                     (cod_norma)
                                                    REFERENCES normas.norma         (cod_norma)
);
GRANT ALL ON tcemg.configuracao_reglic TO urbem;


CREATE TABLE tcemg.tipo_registro_preco(
    exercicio               CHAR(4)         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    cod_norma               INTEGER         NOT NULL,
    cod_tipo_decreto        INTEGER                 ,
    CONSTRAINT pk_tipo_registro_preco       PRIMARY KEY (exercicio, cod_entidade, cod_norma),
    CONSTRAINT fk_tipo_registro_preco_1     FOREIGN KEY                     (exercicio,cod_entidade)
                                            REFERENCES orcamento.entidade   (exercicio,cod_entidade),
    CONSTRAINT fk_tipo_registro_preco_2     FOREIGN KEY                     (cod_norma)
                                            REFERENCES normas.norma         (cod_norma),
    CONSTRAINT fk_tipo_registro_preco_3     FOREIGN KEY                     (cod_tipo_decreto)
                                            REFERENCES tcemg.tipo_decreto   (cod_tipo_decreto)
);
GRANT ALL ON tcemg.tipo_registro_preco TO urbem;


----------------
-- Ticket #21145
----------------

CREATE TABLE tcemg.norma_artigo(
    cod_artigo      INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    cod_norma       INTEGER         NOT NULL,
    num_artigo      INTEGER         NOT NULL,
    descricao       VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_norma_artigo      PRIMARY KEY             (cod_artigo),
    CONSTRAINT fk_norma_artigo_1    FOREIGN KEY             (cod_norma)
                                    REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcemg.norma_artigo TO GROUP urbem;


----------------
-- Ticket #21689
----------------

ALTER TABLE tcemg.convenio_participante DROP CONSTRAINT fk_tcemg_convenio_participante_3;
ALTER TABLE tcemg.convenio_participante DROP COLUMN num_certificacao_participante;
ALTER TABLE tcemg.convenio_participante DROP COLUMN exercicio_participante;
ALTER TABLE tcemg.convenio_participante ADD CONSTRAINT fk_tcemg_convenio_participante_3 FOREIGN KEY (cgm_participante)
                                                                      REFERENCES compras.fornecedor (cgm_fornecedor);


----------------
-- Ticket #21696
----------------

ALTER TABLE tcemg.contrato ADD COLUMN multa_inadimplemento character varying(100);

ALTER TABLE tcemg.contrato_aditivo_item ALTER COLUMN quantidade TYPE numeric(14,4);

UPDATE administracao.acao SET ordem=22 WHERE cod_acao=2938;
UPDATE administracao.acao SET ordem=23 WHERE cod_acao=2939;
UPDATE administracao.acao SET ordem=24 WHERE cod_acao=2940;

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
     ( 2953
     , 451
     , 'FLManterAditivoContrato.php' 
     , 'incluir'
     , 25
     , ''
     , 'Incluir Aditivo de Contrato'
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
     ( 2954
     , 451
     , 'FLManterAditivoContrato.php'
     , 'alterar'
     , 26
     , ''
     , 'Alterar Aditivo de Contrato'
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
     ( 2955
     , 451
     , 'FLManterAditivoContrato.php'
     , 'excluir'
     , 27
     , ''
     , 'Excluir Aditivo de Contrato'
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
     ( 2956
     , 451
     , 'FLManterApostilaContrato.php' 
     , 'incluir'
     , 28
     , ''
     , 'Incluir Apostila de Contrato'
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
     ( 2957
     , 451
     , 'FLManterApostilaContrato.php'
     , 'alterar'
     , 29
     , ''
     , 'Alterar Apostila de Contrato'
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
     ( 2958
     , 451
     , 'FLManterApostilaContrato.php'
     , 'excluir'
     , 30
     , ''
     , 'Excluir Apostila de Contrato'
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
     ( 2959
     , 451
     , 'FLRescindirContrato.php' 
     , 'rescindir'
     , 31
     , ''
     , 'Rescisão de Contrato'
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
     ( 2960
     , 451
     , 'FLRescindirContrato.php' 
     , 'excluir'
     , 32
     , ''
     , 'Excluir Rescisão de Contrato'
     , TRUE
     );


----------------
-- Ticket #21716
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
     ( 2961
     , 451
     , 'FMConfigurarTipoDocumentoCredor.php'
     , 'configurar'
     , 22
     , ''
     , 'Tipo de Documento Credor'
     , TRUE
     );

CREATE TABLE tcemg.tipo_doc_credor(
    codigo      INTEGER             NOT NULL,
    descricao   VARCHAR(40)         NOT NULL,
    CONSTRAINT pk_tipo_doc_credor   PRIMARY KEY (codigo)
);
GRANT ALL ON tcemg.tipo_doc_credor TO GROUP urbem;

INSERT INTO tcemg.tipo_doc_credor VALUES (1, 'CPF'                      );
INSERT INTO tcemg.tipo_doc_credor VALUES (2, 'CNPJ'                     );
INSERT INTO tcemg.tipo_doc_credor VALUES (3, 'Documento de Estrangeiros');

CREATE TABLE tcemg.de_para_documento(
    cod_doc_tce     INTEGER             NOT NULL,
    cod_doc_urbem   INTEGER             NOT NULL,
    CONSTRAINT pk_de_para_documento     PRIMARY KEY                         (cod_doc_tce, cod_doc_urbem),
    CONSTRAINT fk_de_para_documento_1   FOREIGN KEY                         (cod_doc_tce)
                                        REFERENCES  tcemg.tipo_doc_credor   (codigo),
    CONSTRAINT fk_de_para_documento_2   FOREIGN KEY                         (cod_doc_urbem)
                                        REFERENCES  licitacao.documento     (cod_documento)
);
GRANT ALL ON tcemg.de_para_documento TO GROUP urbem;


----------------
-- Ticket #21720
----------------

CREATE TABLE frota.controle_interno(
    cod_veiculo     INTEGER             NOT NULL,
    exercicio       CHAR(4)             NOT NULL,
    verificado      BOOLEAN             NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_controle_interno      PRIMARY KEY               (cod_veiculo, exercicio),
    CONSTRAINT fk_controle_interno_1    FOREIGN KEY               (cod_veiculo)
                                        REFERENCES  frota.veiculo (cod_veiculo)
);
GRANT ALL ON frota.controle_interno TO GROUP urbem;


----------------
-- Ticket #21193
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2014'
        AND parametro  = 'cnpj'
        AND valor      = '24854234000164'
          ;
    IF NOT FOUND THEN
        DELETE FROM tcmgo.vinculo_plano_contas_tcmgo;

        ALTER TABLE tcmgo.vinculo_plano_contas_tcmgo DROP CONSTRAINT fk_vinculo_plano_conta_tcmgo_1;
        ALTER TABLE tcmgo.vinculo_plano_contas_tcmgo DROP COLUMN cod_plano;
        ALTER TABLE tcmgo.vinculo_plano_contas_tcmgo ADD  COLUMN cod_conta INTEGER NOT NULL;
        ALTER TABLE tcmgo.vinculo_plano_contas_tcmgo ADD CONSTRAINT fk_vinculo_plano_conta_tcmgo_1
                                                         FOREIGN KEY                          (cod_conta, exercicio)
                                                         REFERENCES contabilidade.plano_conta (cod_conta, exercicio);

        CREATE TABLE tcmgo.arquivo_pct(
            cod_conta       INTEGER     NOT NULL,
            exercicio       CHAR(4)     NOT NULL,
            mes             INTEGER     NOT NULL,
            CONSTRAINT pk_arquivo_pct   PRIMARY KEY                             (cod_conta, exercicio, mes),
            CONSTRAINT fk_arquivo_pct_1 FOREIGN KEY                             (cod_conta, exercicio)
                                        REFERENCES contabilidade.plano_conta    (cod_conta, exercicio)
        );
        GRANT ALL ON tcmgo.arquivo_pct TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #21182
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
     ( 2962
     , 451
     , 'FMManterRegistroPreco.php'
     , 'incluir'
     , 40
     , 'Detalhameto da Adesão a Registro de Preços'
     , 'Incluir Registro de Preço'
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
     ( 2963
     , 451
     , 'FLManterRegistroPreco.php'
     , 'alterar'
     , 41
     , ''
     , 'Alterar Registro de Preço'
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
     ( 2964
     , 451
     , 'FLManterRegistroPreco.php'
     , 'excluir'
     , 42
     , ''
     , 'Excluir Registro de Preço'
     , TRUE
     );


CREATE TABLE tcemg.processo_adesao_registro_precos (
    cod_entidade                     INTEGER        NOT NULL,
    numero_processo_adesao           INTEGER        NOT NULL,
    exercicio_adesao                 CHAR(4)        NOT NULL,
    data_abertura_processo_adesao    DATE           NOT NULL,
    numcgm                           INTEGER        NOT NULL,
    exercicio_licitacao              CHAR(4)        NOT NULL,
    numero_processo_licitacao        INTEGER        NOT NULL,
    codigo_modalidade_licitacao      INTEGER        NOT NULL,
    numero_modalidade                INTEGER        NOT NULL,
    data_ata_registro_preco          DATE           NOT NULL,
    data_ata_registro_preco_validade DATE           NOT NULL,
    natureza_procedimento            INTEGER        NOT NULL,
    data_publicacao_aviso_intencao   DATE                   ,
    objeto_adesao                    TEXT           NOT NULL,
    cgm_responsavel                  INTEGER        NOT NULL, --<
    desconto_tabela                  INTEGER        NOT NULL,
    processo_lote                    INTEGER        NOT NULL,
    exercicio                        CHAR(4)        NOT NULL,
    num_unidade                      INTEGER        NOT NULL,
    num_orgao                        INTEGER        NOT NULL,
    CONSTRAINT pk_processo_adesao_registro_precos   PRIMARY KEY (cod_entidade, numero_processo_adesao, exercicio_adesao),
    CONSTRAINT fk_processo_adesao_registro_precos_1 FOREIGN KEY (numcgm)
                                                    REFERENCES sw_cgm (numcgm),
    CONSTRAINT fk_processo_adesao_registro_precos_2 FOREIGN KEY (exercicio, num_unidade, num_orgao)
                                                    REFERENCES orcamento.unidade(exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_processo_adesao_registro_precos_3 FOREIGN KEY (cgm_responsavel)
                                                    REFERENCES sw_cgm_pessoa_fisica(numcgm)
);
GRANT ALL ON tcemg.processo_adesao_registro_precos TO urbem;

CREATE TABLE tcemg.lote_registro_precos (
    cod_entidade                INTEGER         NOT NULL,
    numero_processo_adesao      INTEGER         NOT NULL,
    exercicio_adesao            CHAR(4)         NOT NULL,
    cod_lote                    INTEGER         NOT NULL,
    descricao_lote              VARCHAR(250)    NOT NULL,
    percentual_desconto_lote    NUMERIC(6,2)            ,
    CONSTRAINT pk_lote_registro_precos   PRIMARY KEY (cod_entidade, numero_processo_adesao, exercicio_adesao, cod_lote),
    CONSTRAINT fk_lote_registro_precos_1 FOREIGN KEY (cod_entidade, numero_processo_adesao, exercicio_adesao)
                                         REFERENCES tcemg.processo_adesao_registro_precos (cod_entidade, numero_processo_adesao, exercicio_adesao),
    CONSTRAINT uk_lote_registro_precos_1 UNIQUE (cod_entidade, numero_processo_adesao, exercicio_adesao, descricao_lote)
);
GRANT ALL ON tcemg.lote_registro_precos TO urbem;

CREATE TABLE tcemg.item_registro_precos (
    cod_entidade                INTEGER         NOT NULL,
    numero_processo_adesao      INTEGER         NOT NULL,
    exercicio_adesao            CHAR(4)         NOT NULL,
    cod_lote                    INTEGER         NOT NULL,
    cod_item                    INTEGER         NOT NULL,
    num_item                    INTEGER         NOT NULL,
    data_cotacao                DATE            NOT NULL,
    vl_cotacao_preco_unitario   NUMERIC(14,4)   NOT NULL,
    quantidade_cotacao          NUMERIC(14,4)   NOT NULL,
    preco_unitario              NUMERIC(14,4)   NOT NULL,
    quantidade_licitada         NUMERIC(14,4)   NOT NULL,
    quantidade_aderida          NUMERIC(14,4)   NOT NULL,
    percentual_desconto         NUMERIC(6,2)    NOT NULL,
    cgm_vencedor                INTEGER         NOT NULL,
    CONSTRAINT pk_item_registro_precos    PRIMARY KEY (cod_entidade, numero_processo_adesao, exercicio_adesao, cod_lote, cod_item),
    CONSTRAINT fk_item_registro_precos_1  FOREIGN KEY (cod_entidade, numero_processo_adesao, exercicio_adesao, cod_lote )
                                          REFERENCES tcemg.lote_registro_precos (cod_entidade, numero_processo_adesao, exercicio_adesao, cod_lote),
    CONSTRAINT fk_item_registro_precos_2  FOREIGN KEY (cod_item)
                                          REFERENCES almoxarifado.catalogo_item ( cod_item ),
    CONSTRAINT fk_item_registro_precos_3  FOREIGN KEY (cgm_vencedor)
                                          REFERENCES sw_cgm (numcgm)
);
GRANT ALL ON tcemg.item_registro_precos TO urbem;


UPDATE administracao.acao SET ordem =  1 WHERE cod_acao = 2913;
UPDATE administracao.acao SET ordem =  2 WHERE cod_acao = 2914;
UPDATE administracao.acao SET ordem =  3 WHERE cod_acao = 2915;
UPDATE administracao.acao SET ordem =  4 WHERE cod_acao = 2917;
UPDATE administracao.acao SET ordem =  5 WHERE cod_acao = 2916;
UPDATE administracao.acao SET ordem =  6 WHERE cod_acao = 2918;
UPDATE administracao.acao SET ordem =  7 WHERE cod_acao = 2919;
UPDATE administracao.acao SET ordem =  8 WHERE cod_acao = 2923;
UPDATE administracao.acao SET ordem =  9 WHERE cod_acao = 2933;
UPDATE administracao.acao SET ordem = 10 WHERE cod_acao = 2936;
UPDATE administracao.acao SET ordem = 11 WHERE cod_acao = 2947;
UPDATE administracao.acao SET ordem = 12 WHERE cod_acao = 2895;
UPDATE administracao.acao SET ordem = 13 WHERE cod_acao = 2932;
UPDATE administracao.acao SET ordem = 14 WHERE cod_acao = 2930;
UPDATE administracao.acao SET ordem = 15 WHERE cod_acao = 2934;
UPDATE administracao.acao SET ordem = 16 WHERE cod_acao = 2935;
UPDATE administracao.acao SET ordem = 17 WHERE cod_acao = 2937;
UPDATE administracao.acao SET ordem = 18 WHERE cod_acao = 2929;
UPDATE administracao.acao SET ordem = 19 WHERE cod_acao = 2904;
UPDATE administracao.acao SET ordem = 20 WHERE cod_acao = 2901;
UPDATE administracao.acao SET ordem = 21 WHERE cod_acao = 2946;
UPDATE administracao.acao SET ordem = 22 WHERE cod_acao = 2961;
UPDATE administracao.acao SET ordem = 23 WHERE cod_acao = 2924;
UPDATE administracao.acao SET ordem = 24 WHERE cod_acao = 2925;
UPDATE administracao.acao SET ordem = 25 WHERE cod_acao = 2926;
UPDATE administracao.acao SET ordem = 26 WHERE cod_acao = 2942;
UPDATE administracao.acao SET ordem = 27 WHERE cod_acao = 2943;
UPDATE administracao.acao SET ordem = 28 WHERE cod_acao = 2944;
UPDATE administracao.acao SET ordem = 29 WHERE cod_acao = 2938;
UPDATE administracao.acao SET ordem = 30 WHERE cod_acao = 2939;
UPDATE administracao.acao SET ordem = 31 WHERE cod_acao = 2940;
UPDATE administracao.acao SET ordem = 32 WHERE cod_acao = 2953;
UPDATE administracao.acao SET ordem = 33 WHERE cod_acao = 2954;
UPDATE administracao.acao SET ordem = 34 WHERE cod_acao = 2955;
UPDATE administracao.acao SET ordem = 35 WHERE cod_acao = 2956;
UPDATE administracao.acao SET ordem = 36 WHERE cod_acao = 2957;
UPDATE administracao.acao SET ordem = 37 WHERE cod_acao = 2958;
UPDATE administracao.acao SET ordem = 38 WHERE cod_acao = 2959;
UPDATE administracao.acao SET ordem = 39 WHERE cod_acao = 2960;


----------------
-- Ticket #21000
----------------

CREATE TABLE tcemg.arquivo_incamp(
    cod_acao        INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    mes             INTEGER     NOT NULL,
    CONSTRAINT pk_arquivo_pct   PRIMARY KEY         (cod_acao, exercicio, mes),
    CONSTRAINT fk_arquivo_pct_1 FOREIGN KEY         (cod_acao)
                                REFERENCES ppa.acao (cod_acao)
);
GRANT ALL ON tcemg.arquivo_incamp TO urbem;


----------------
-- Ticket #21733
----------------

CREATE TABLE tcemg.arquivo_amp(
    cod_acao        INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    mes             INTEGER     NOT NULL,
    CONSTRAINT pk_arquivo_amp   PRIMARY KEY         (cod_acao, exercicio, mes),
    CONSTRAINT fk_arquivo_amp_1 FOREIGN KEY         (cod_acao)
                                REFERENCES ppa.acao (cod_acao)
);
GRANT ALL ON tcemg.arquivo_amp TO urbem;

CREATE TABLE tcemg.arquivo_uoc(
    num_orgao       INTEGER     NOT NULL,
    num_unidade     INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    mes             INTEGER     NOT NULL,
    CONSTRAINT pk_arquivo_uoc   PRIMARY KEY         (num_orgao, num_unidade, exercicio, mes),
    CONSTRAINT fk_arquivo_uoc_1 FOREIGN KEY         (num_orgao, num_unidade, exercicio)
                                REFERENCES tcemg.uniorcam (num_orgao, num_unidade, exercicio)
);
GRANT ALL ON tcemg.arquivo_uoc TO urbem;


----------------
-- Ticket #21003
----------------

CREATE TABLE tcemg.arquivo_iuoc(
    num_orgao       INTEGER         NOT NULL,
    num_unidade     INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    mes             INTEGER         NOT NULL,
    CONSTRAINT pk_arquivo_iuoc      PRIMARY KEY         (num_orgao, num_unidade, exercicio, mes),
    CONSTRAINT fk_arquivo_iuoc_1    FOREIGN KEY         (num_orgao, num_unidade, exercicio)
                                    REFERENCES tcemg.uniorcam (num_orgao, num_unidade, exercicio)
);
GRANT ALL ON tcemg.arquivo_uoc TO urbem;

CREATE TABLE tcemg.registros_arquivo_inclusao_programa (
    exercicio       CHAR(4)     NOT NULL,
    cod_programa    INTEGER     NOT NULL,
    timestamp       TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,    
    CONSTRAINT pk_registros_arquivo_inclusao_programa   PRIMARY KEY (exercicio, cod_programa),
    CONSTRAINT fk_registros_arquivo_inclusao_programa_1 FOREIGN KEY (cod_programa)
                                               REFERENCES ppa.programa (cod_programa)
);
GRANT ALL ON tcemg.registros_arquivo_inclusao_programa TO urbem;


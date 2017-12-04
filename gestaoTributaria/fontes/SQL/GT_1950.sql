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
* $Id: GT_1950.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.95.0
*/

------------------------------------------
-- CRIANDO INDICE EM divida.parcela_origem
-- FABIO - 20081208 ----------------------

CREATE INDEX ix_parcela_origem_1 ON divida.parcela_origem (num_parcelamento);


----------------
-- Ticket #12808
----------------

CREATE SCHEMA fiscalizacao;


CREATE TABLE fiscalizacao.tipo_fiscalizacao (
    cod_tipo                INTEGER         NOT NULL,
    descricao               VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_tipo_fiscalizacao         PRIMARY KEY                                         (cod_tipo)
);

INSERT INTO fiscalizacao.tipo_fiscalizacao      VALUES (1,'Fiscalização Tributária do ISSQN');
INSERT INTO fiscalizacao.tipo_fiscalizacao      VALUES (2,'Fiscalização de Obras'           );
INSERT INTO fiscalizacao.tipo_fiscalizacao      VALUES (3,'Fiscalização de Posturas'        );
INSERT INTO fiscalizacao.tipo_fiscalizacao      VALUES (4,'Fiscalização Sanitária'          );
INSERT INTO fiscalizacao.tipo_fiscalizacao      VALUES (5,'Fizcalização Urbanística'        );


CREATE TABLE fiscalizacao.natureza_fiscalizacao (
    cod_natureza            INTEGER         NOT NULL,
    descricao               VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_natureza_fiscalizacao     PRIMARY KEY                                         (cod_natureza)
);

INSERT INTO fiscalizacao.natureza_fiscalizacao  VALUES (1,'Denúncia Espontânes'  );
INSERT INTO fiscalizacao.natureza_fiscalizacao  VALUES (2,'Denúncia de Terceiros');
INSERT INTO fiscalizacao.natureza_fiscalizacao  VALUES (3,'Vistoria'             );


CREATE TABLE fiscalizacao.tipo_local (
    cod_local               INTEGER         NOT NULL,
    nom_local               VARCHAR(25)     NOT NULL,
    CONSTRAINT pk_tipo_local                PRIMARY KEY                                         (cod_local)
);

INSERT INTO fiscalizacao.tipo_local             VALUES (1,'Inscrição Imobiliária');
INSERT INTO fiscalizacao.tipo_local             VALUES (2,'Bairro'               );
INSERT INTO fiscalizacao.tipo_local             VALUES (3,'Logradouro'           );
INSERT INTO fiscalizacao.tipo_local             VALUES (4,'Localização'          );
INSERT INTO fiscalizacao.tipo_local             VALUES (5,'Loteamento'           );


CREATE TABLE fiscalizacao.tipo_inutilizacao (
    cod_tipo                INTEGER         NOT NULL,
    descricao               VARCHAR(20)     NOT NULL,
    CONSTRAINT pk_tipo_inutilizacao         PRIMARY KEY                                         (cod_tipo)
);

INSERT INTO fiscalizacao.tipo_inutilizacao      VALUES (1,'Estravio'    );
INSERT INTO fiscalizacao.tipo_inutilizacao      VALUES (2,'Encerramento');
INSERT INTO fiscalizacao.tipo_inutilizacao      VALUES (3,'Furto'       );
INSERT INTO fiscalizacao.tipo_inutilizacao      VALUES (4,'Cancelamento');


CREATE TABLE fiscalizacao.tipo_penalidade (
    cod_tipo                INTEGER         NOT NULL,
    descricao               VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_tipo_penalidade           PRIMARY KEY                                         (cod_tipo)
);

INSERT INTO fiscalizacao.tipo_penalidade VALUES (1,'Multa'     );
INSERT INTO fiscalizacao.tipo_penalidade VALUES (2,'Embargo'   );
INSERT INTO fiscalizacao.tipo_penalidade VALUES (3,'Demoliçãp' );
INSERT INTO fiscalizacao.tipo_penalidade VALUES (4,'Interdição');


CREATE TABLE fiscalizacao.fiscal (
    cod_fiscal              INTEGER         NOT NULL,
    numcgm                  INTEGER         NOT NULL,
    cod_contrato            INTEGER         NOT NULL,
    administrador           BOOLEAN         NOT NULL,
    ativo                   BOOLEAN         NOT NULL,
    CONSTRAINT pk_fiscal                    PRIMARY KEY                                         (cod_fiscal),
    CONSTRAINT fk_fiscal_1                  FOREIGN KEY                                         (numcgm)
                                            REFERENCES sw_cgm_pessoa_fisica                     (numcgm),
    CONSTRAINT fk_fiscal_2                  FOREIGN KEY                                         (cod_contrato)
                                            REFERENCES pessoal.contrato                         (cod_contrato)
);


CREATE TABLE fiscalizacao.fiscal_fiscalizacao (
    cod_fiscal              INTEGER         NOT NULL,
    cod_tipo                INTEGER         NOT NULL,
    CONSTRAINT pk_fiscal_fiscalizacao       PRIMARY KEY                                         (cod_fiscal,cod_tipo),
    CONSTRAINT fk_fiscal_fiscalizacao_1     FOREIGN KEY                                         (cod_fiscal)
                                            REFERENCES fiscalizacao.fiscal                      (cod_fiscal),
    CONSTRAINT fk_fiscal_fiscalizacao_2     FOREIGN KEY                                         (cod_tipo)
                                            REFERENCES fiscalizacao.tipo_fiscalizacao           (cod_tipo)
);


CREATE TABLE fiscalizacao.grafica (
    numcgm                  INTEGER         NOT NULL,
    ativo                   BOOLEAN         NOT NULL,
    CONSTRAINT pk_grafica                   PRIMARY KEY                                         (numcgm),
    CONSTRAINT fk_grafica_1                 FOREIGN KEY                                         (numcgm)
                                            REFERENCES sw_cgm                                   (numcgm)
);


CREATE TABLE fiscalizacao.autorizacao_notas (
    cod_autorizacao         INTEGER         NOT NULL,
    numcgm                  INTEGER         NOT NULL,
    inscricao_economica     INTEGER         NOT NULL,
    numcgm_usuario          INTEGER         NOT NULL,
    serie                   VARCHAR(10)     NOT NULL,
    qtd_taloes              INTEGER         NOT NULL,
    nota_inicial            INTEGER         NOT NULL,
    nota_final              INTEGER         NOT NULL,
    qtd_vias                INTEGER         NOT NULL,
    observacao              TEXT,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_autorizacao_notas         PRIMARY KEY                                         (cod_autorizacao),
    CONSTRAINT fk_autorizacao_notas_1       FOREIGN KEY                                         (numcgm)
                                            REFERENCES fiscalizacao.grafica                     (numcgm),
    CONSTRAINT fk_autorizacao_notas_2       FOREIGN KEY                                         (numcgm_usuario)
                                            REFERENCES sw_cgm                                   (numcgm),
    CONSTRAINT fk_autorizacao_notas_3       FOREIGN KEY                                         (inscricao_economica)
                                            REFERENCES economico.cadastro_economico             (inscricao_economica)
);


CREATE TABLE fiscalizacao.autorizacao_documento (
    cod_autorizacao         INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_autorizacao_documento     PRIMARY KEY                                         (cod_autorizacao,timestamp),
    CONSTRAINT fk_autorizacao_documento_1   FOREIGN KEY                                         (cod_autorizacao)
                                            REFERENCES fiscalizacao.autorizacao_notas           (cod_autorizacao),
    CONSTRAINT fk_autorizacao_documento_2   FOREIGN KEY                                         (cod_tipo_documento,cod_documento)
                                            REFERENCES administracao.modelo_documento           (cod_tipo_documento,cod_documento)
);


CREATE TABLE fiscalizacao.baixa_autorizacao (
    cod_baixa               INTEGER         NOT NULL,
    cod_autorizacao         INTEGER         NOT NULL,
    numcgm_usuario          INTEGER         NOT NULL,
    observacao              TEXT,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_baixa_autorizacao         PRIMARY KEY                                         (cod_baixa),
    CONSTRAINT fk_baixa_autorizacao_1       FOREIGN KEY                                         (cod_autorizacao)
                                            REFERENCES fiscalizacao.autorizacao_notas           (cod_autorizacao),
    CONSTRAINT fk_baixa_autorizacao_2       FOREIGN KEY                                         (numcgm_usuario)
                                            REFERENCES sw_cgm                                   (numcgm)
);


CREATE TABLE fiscalizacao.baixa_notas (
    cod_baixa               INTEGER         NOT NULL,
    nr_nota                 INTEGER         NOT NULL,
    cod_tipo                INTEGER         NOT NULL,
    CONSTRAINT pk_baixa_notas               PRIMARY KEY                                         (cod_baixa,nr_nota),
    CONSTRAINT fk_baixa_notas_1             FOREIGN KEY                                         (cod_baixa)
                                            REFERENCES fiscalizacao.baixa_autorizacao           (cod_baixa),
    CONSTRAINT fk_baixa_notas_2             FOREIGN KEY                                         (cod_tipo)
                                            REFERENCES fiscalizacao.tipo_inutilizacao           (cod_tipo)
);


CREATE TABLE fiscalizacao.baixa_documento (
    cod_baixa               INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_baixa_documento           PRIMARY KEY                                         (cod_baixa,timestamp),
    CONSTRAINT fk_baixa_documento_1         FOREIGN KEY                                         (cod_baixa)
                                            REFERENCES fiscalizacao.baixa_autorizacao           (cod_baixa),
    CONSTRAINT fk_baixa_documento_2         FOREIGN KEY                                         (cod_tipo_documento,cod_documento)
                                            REFERENCES administracao.modelo_documento           (cod_tipo_documento,cod_documento)
);


CREATE TABLE fiscalizacao.autenticacao_livro (
    inscricao_economica     INTEGER         NOT NULL,
    nr_livro                INTEGER         NOT NULL,
    periodo_inicio          DATE            NOT NULL,
    periodo_termino         DATE            NOT NULL,
    qtd_paginas             INTEGER         NOT NULL,
    observacao              TEXT,
    CONSTRAINT pk_autenticacao_livro        PRIMARY KEY                                         (inscricao_economica,nr_livro),
    CONSTRAINT fk_autenticacao_livro_1      FOREIGN KEY                                         (inscricao_economica)
                                            REFERENCES economico.cadastro_economico             (inscricao_economica)
);


CREATE TABLE fiscalizacao.autenticacao_documento (
    inscricao_economica     INTEGER         NOT NULL,
    nr_livro                INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_autenticacao_documento    PRIMARY KEY                                         (inscricao_economica,nr_livro,timestamp),
    CONSTRAINT fk_autenticacao_documento_1  FOREIGN KEY                                         (inscricao_economica,nr_livro)
                                            REFERENCES fiscalizacao.autenticacao_livro          (inscricao_economica,nr_livro),
    CONSTRAINT fk_autenticacao_documento_2  FOREIGN KEY                                         (cod_tipo_documento,cod_documento)
                                            REFERENCES administracao.modelo_documento           (cod_tipo_documento,cod_documento)
);


CREATE TABLE fiscalizacao.documento (
    cod_documento           INTEGER         NOT NULL,
    cod_tipo_fiscalizacao   INTEGER         NOT NULL,
    nom_documento           VARCHAR(80)     NOT NULL,
    uso_interno             BOOLEAN         NOT NULL,
    ativo                   BOOLEAN         NOT NULL,
    CONSTRAINT pk_documento                 PRIMARY KEY                                         (cod_documento),
    CONSTRAINT fk_documento_1               FOREIGN KEY                                         (cod_tipo_fiscalizacao)
                                            REFERENCES fiscalizacao.tipo_fiscalizacao           (cod_tipo)
);


CREATE TABLE fiscalizacao.processo_fiscal (
    cod_processo            INTEGER         NOT NULL,
    cod_processo_protocolo  INTEGER                 ,
    ano_exercicio           CHAR(4)                 ,
    numcgm                  INTEGER         NOT NULL,
    cod_tipo                INTEGER         NOT NULL,
    cod_natureza            INTEGER         NOT NULL,
    periodo_inicio          DATE            NOT NULL,
    periodo_termino         DATE            NOT NULL,
    previsao_inicio         DATE            NOT NULL,
    previsao_termino        DATE            NOT NULL,
    observacao              TEXT            NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_processo_fiscal           PRIMARY KEY                                         (cod_processo),
    CONSTRAINT fk_processo_fiscal_1         FOREIGN KEY                                         (cod_tipo)
                                            REFERENCES fiscalizacao.tipo_fiscalizacao           (cod_tipo),
    CONSTRAINT fk_processo_fiscal_2         FOREIGN KEY                                         (cod_natureza)
                                            REFERENCES fiscalizacao.natureza_fiscalizacao       (cod_natureza),
    CONSTRAINT fk_processo_fiscal_3         FOREIGN KEY                                         (cod_processo_protocolo, ano_exercicio)
                                            REFERENCES sw_processo                              (cod_processo, ano_exercicio),
    CONSTRAINT fk_processo_fiscal_4         FOREIGN KEY                                         (numcgm)
                                            REFERENCES administracao.usuario                    (numcgm)
);


CREATE TABLE fiscalizacao.processo_fiscal_credito (
    cod_processo            INTEGER         NOT NULL,
    cod_natureza            INTEGER         NOT NULL,
    cod_genero              INTEGER         NOT NULL,
    cod_especie             INTEGER         NOT NULL,
    cod_credito             INTEGER         NOT NULL,
    CONSTRAINT pk_processo_fiscal_cred      PRIMARY KEY                                         (cod_processo, cod_natureza, cod_genero, cod_especie, cod_credito),
    CONSTRAINT fk_processo_fiscal_cred_1    FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_processo_fiscal_cred_2    FOREIGN KEY                                         (cod_credito, cod_especie, cod_genero, cod_natureza)
                                            REFERENCES monetario.credito                        (cod_credito, cod_especie, cod_genero, cod_natureza)
);


CREATE TABLE fiscalizacao.processo_fiscal_empresa (
    cod_processo            INTEGER         NOT NULL,
    inscricao_economica     INTEGER         NOT NULL,
    CONSTRAINT pk_processo_fiscal_emp       PRIMARY KEY                                         (cod_processo),
    CONSTRAINT fk_processo_fiscal_emp_1     FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_processo_fiscal_emp_2     FOREIGN KEY                                         (inscricao_economica)
                                            REFERENCES economico.cadastro_economico             (inscricao_economica)
);


CREATE TABLE fiscalizacao.processo_fiscal_grupo (
    cod_processo            INTEGER         NOT NULL,
    ano_exercicio           CHAR(4)         NOT NULL,
    cod_grupo               INTEGER         NOT NULL,
    CONSTRAINT pk_processo_fiscal_grupo     PRIMARY KEY                                         (cod_processo, ano_exercicio, cod_grupo),
    CONSTRAINT fk_processo_fiscal_grupo_1   FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_processo_fiscal_grupo_2   FOREIGN KEY                                         (cod_grupo, ano_exercicio)
                                            REFERENCES arrecadacao.grupo_credito                (cod_grupo, ano_exercicio)
);


CREATE TABLE fiscalizacao.processo_fiscal_obras (
    cod_processo            INTEGER         NOT NULL,
    inscricao_municipal     INTEGER         NOT NULL,
    cod_local               INTEGER         NOT NULL,
    CONSTRAINT pk_processo_fiscal_obras     PRIMARY KEY                                         (cod_processo),
    CONSTRAINT fk_processo_fiscal_obras_1   FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_processo_fiscal_obras_2   FOREIGN KEY                                         (cod_local)
                                            REFERENCES fiscalizacao.tipo_local                  (cod_local),
    CONSTRAINT fk_processo_fiscal_obras_3   FOREIGN KEY                                         (inscricao_municipal)
                                            REFERENCES imobiliario.imovel                       (inscricao_municipal)
);


CREATE TABLE fiscalizacao.fiscal_processo_fiscal (
    cod_processo            INTEGER         NOT NULL,
    cod_fiscal              INTEGER         NOT NULL,
    status                  CHAR(1)         NOT NULL,
    CONSTRAINT pk_fiscal_processo_fiscal    PRIMARY KEY                                         (cod_processo, cod_fiscal),
    CONSTRAINT fk_fiscal_processo_fiscal_1  FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_fiscal_processo_fiscal_2  FOREIGN KEY                                         (cod_fiscal)
                                            REFERENCES fiscalizacao.fiscal                      (cod_fiscal),
    CONSTRAINT ck_fiscal_processo_fiscal_1  CHECK (status in ('A','I'))
);


CREATE TABLE fiscalizacao.inicio_fiscalizacao (
    cod_processo            INTEGER         NOT NULL,
    cod_fiscal              INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    dt_inicio               DATE            NOT NULL,
    local_entrega           VARCHAR(120)    NOT NULL,
    prazo_entrega           DATE            NOT NULL,
    observacao              TEXT            NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_inicio_fiscalizacao       PRIMARY KEY                                         (cod_processo),
    CONSTRAINT fk_inicio_fiscalizacao_1     FOREIGN KEY                                         (cod_processo, cod_fiscal)
                                            REFERENCES fiscalizacao.fiscal_processo_fiscal      (cod_processo, cod_fiscal),
    CONSTRAINT fk_inicio_fiscalizacao_2     FOREIGN KEY                                         (cod_documento, cod_tipo_documento)
                                            REFERENCES administracao.modelo_documento           (cod_documento, cod_tipo_documento),
    CONSTRAINT fk_inicio_fiscalizacao_3     FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo)
);


CREATE TABLE fiscalizacao.inicio_fiscalizacao_documentos (
    cod_documento           INTEGER         NOT NULL,
    cod_processo            INTEGER         NOT NULL,
    CONSTRAINT pk_inicio_fisc_documentos    PRIMARY KEY                                         (cod_documento, cod_processo),
    CONSTRAINT fk_inicio_fisc_documentos_1  FOREIGN KEY                                         (cod_documento)
                                            REFERENCES fiscalizacao.documento                   (cod_documento),
    CONSTRAINT fk_inicio_fisc_documentos_2  FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.inicio_fiscalizacao         (cod_processo)
);


CREATE TABLE fiscalizacao.documentos_entrega (
    situacao                CHAR(1)         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    cod_processo            INTEGER         NOT NULL,
    cod_fiscal              INTEGER         NOT NULL,
    observacao              TEXT                    ,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_documentos_entrega        PRIMARY KEY                                         (situacao,cod_documento,cod_processo),
    CONSTRAINT fk_documentos_entrega_1      FOREIGN KEY                                         (cod_documento,cod_processo)
                                            REFERENCES fiscalizacao.inicio_fiscalizacao_documentos (cod_documento,cod_processo),
    CONSTRAINT fk_documentos_entrega_2      FOREIGN KEY                                         (cod_processo,cod_fiscal)
                                            REFERENCES fiscalizacao.fiscal_processo_fiscal      (cod_processo,cod_fiscal),
    CONSTRAINT chk_documentos_entrega       CHECK (situacao in ('R','D'))
);

COMMENT ON COLUMN fiscalizacao.documentos_entrega.situacao IS 'R = recebido, D = devolvido';

CREATE OR REPLACE FUNCTION fiscalizacao.fn_verifica_entrega() RETURNS TRIGGER AS
$$
DECLARE
    sit varchar;
BEGIN
    IF ( NEW.situacao = 'D' ) THEN
        IF (TG_OP = 'INSERT') THEN
            SELECT situacao
              INTO sit
              FROM fiscalizacao.documentos_entrega
             WHERE documentos_entrega.cod_processo  = NEW.cod_processo
               AND documentos_entrega.cod_documento = NEW.cod_documento;

            IF ( sit = 'R' ) THEN
                return new;
            ELSE
                return null;
            END IF;
        ELSE
            return new;
        END IF;
    ELSE
        return new;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER fn_verifica_entrega BEFORE INSERT ON fiscalizacao.documentos_entrega FOR EACH ROW EXECUTE PROCEDURE fiscalizacao.fn_verifica_entrega();


CREATE TABLE fiscalizacao.documento_atividade (
    cod_atividade           INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_documento_atividade       PRIMARY KEY                                         (cod_atividade, cod_documento),
    CONSTRAINT fk_documento_atividade_1     FOREIGN KEY                                         (cod_atividade)
                                            REFERENCES economico.atividade                      (cod_atividade),
    CONSTRAINT fk_documento_atividade_2     FOREIGN KEY                                         (cod_documento)
                                            REFERENCES fiscalizacao.documento                   (cod_documento)
);


CREATE TABLE fiscalizacao.processo_levantamento(
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    CONSTRAINT pk_processo_levantamento     PRIMARY KEY                                         (cod_processo, competencia),
    CONSTRAINT fk_processo_levantamento_1   FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal_empresa     (cod_processo)
 );


CREATE TABLE fiscalizacao.faturamento_servico (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_servico             INTEGER         NOT NULL,
    cod_atividade           INTEGER         NOT NULL,
    ocorrencia              INTEGER         NOT NULL,
    cod_modalidade          INTEGER         NOT NULL,
    dt_emissao              DATE            NOT NULL,
    CONSTRAINT pk_faturamento_servico       PRIMARY KEY                                         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia),
    CONSTRAINT fk_faturamento_servico_1     FOREIGN KEY                                         (cod_processo, competencia)
                                            REFERENCES fiscalizacao.processo_levantamento       (cod_processo, competencia),
    CONSTRAINT fk_faturamento_servico_2     FOREIGN KEY                                         (cod_atividade, cod_servico)
                                            REFERENCES economico.servico_atividade              (cod_atividade, cod_servico),
    CONSTRAINT fk_faturamento_servico_3     FOREIGN KEY                                         (cod_modalidade)
                                            REFERENCES economico.modalidade_lancamento          (cod_modalidade)
);


CREATE TABLE fiscalizacao.infracao (
    cod_infracao            INTEGER         NOT NULL,
    nom_infracao            VARCHAR(80)     NOT NULL,
    comminar                BOOLEAN         NOT NULL,
    cod_tipo_fiscalizacao   INTEGER         NOT NULL,
    cod_norma               INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_infracao                  PRIMARY KEY                                         (cod_infracao),
    CONSTRAINT fk_infracao_1                FOREIGN KEY                                         (cod_norma)
                                            REFERENCES normas.norma                             (cod_norma),
    CONSTRAINT fk_infracao_2                FOREIGN KEY                                         (cod_tipo_fiscalizacao)
                                            REFERENCES fiscalizacao.tipo_fiscalizacao           (cod_tipo),
    CONSTRAINT fk_infracao_3                FOREIGN KEY                                         (cod_tipo_documento, cod_documento)
                                            REFERENCES administracao.modelo_documento           (cod_tipo_documento, cod_documento)
);


CREATE TABLE fiscalizacao.penalidade (
    cod_penalidade          INTEGER         NOT NULL,
    cod_tipo_penalidade     INTEGER         NOT NULL,
    cod_norma               INTEGER         NOT NULL,
    nom_penalidade          VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_penalidade                PRIMARY KEY                                         (cod_penalidade),
    CONSTRAINT fk_penalidade_1              FOREIGN KEY                                         (cod_tipo_penalidade)
                                            REFERENCES fiscalizacao.tipo_penalidade             (cod_tipo),
    CONSTRAINT fk_penalidade_2              FOREIGN KEY                                         (cod_norma)
                                            REFERENCES normas.norma                             (cod_norma)
);


CREATE TABLE fiscalizacao.penalidade_documento (
    cod_penalidade          INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_penalidade_documento      PRIMARY KEY                                         (cod_penalidade),
    CONSTRAINT fk_penalidade_documento_1    FOREIGN KEY                                         (cod_penalidade)
                                            REFERENCES fiscalizacao.penalidade                  (cod_penalidade),
    CONSTRAINT fk_penalidade_documento_2    FOREIGN KEY                                         (cod_tipo_documento, cod_documento)
                                            REFERENCES  administracao.modelo_documento          (cod_tipo_documento, cod_documento)
);


CREATE TABLE fiscalizacao.infracao_penalidade (
    cod_infracao            INTEGER         NOT NULL,
    cod_penalidade          INTEGER         NOT NULL,
    CONSTRAINT pk_infracao_penalidade       PRIMARY KEY                                         (cod_infracao, cod_penalidade),
    CONSTRAINT fk_infracao_penalidade_1     FOREIGN KEY                                         (cod_infracao)
                                            REFERENCES fiscalizacao.infracao                    (cod_infracao),
    CONSTRAINT fk_infracao_penalidade_2     FOREIGN KEY                                         (cod_penalidade)
                                            REFERENCES fiscalizacao.penalidade                  (cod_penalidade)
);


CREATE TABLE fiscalizacao.nota (
    cod_nota                INTEGER         NOT NULL,
    nro_serie               INTEGER         NOT NULL,
    nro_nota                INTEGER         NOT NULL,
    valor_nota              NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_nota                      PRIMARY KEY                                         (cod_nota)
);


CREATE TABLE fiscalizacao.nota_servico (
    cod_nota                INTEGER         NOT NULL,
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_servico             INTEGER         NOT NULL,
    cod_atividade           INTEGER         NOT NULL,
    ocorrencia              INTEGER         NOT NULL,
    CONSTRAINT pk_nota_servico              PRIMARY KEY                                         (cod_nota, cod_processo, competencia, cod_servico, cod_atividade, ocorrencia),
    CONSTRAINT fk_nota_servico_1            FOREIGN KEY                                         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia)
                                            REFERENCES fiscalizacao.faturamento_servico         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia)
);


CREATE TABLE fiscalizacao.notificacao_fiscalizacao (
    cod_processo            INTEGER         NOT NULL,
    cod_fiscal              INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    dt_notificacao          DATE            NOT NULL,
    observacao              TEXT            NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_notificacao_fiscalizacao   PRIMARY KEY                                         (cod_processo),
    CONSTRAINT fk_notificacao_fiscalizacao_1 FOREIGN KEY                                         (cod_processo, cod_fiscal)
                                             REFERENCES fiscalizacao.fiscal_processo_fiscal      (cod_processo, cod_fiscal),
    CONSTRAINT fk_notificacao_fiscalizacao_2 FOREIGN KEY                                         (cod_processo)
                                             REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_notificacao_fiscalizacao_3 FOREIGN KEY                                         (cod_documento, cod_tipo_documento)
                                             REFERENCES administracao.modelo_documento           (cod_documento, cod_tipo_documento)
);


CREATE TABLE fiscalizacao.notificacao_infracao (
    cod_processo            INTEGER         NOT NULL,
    cod_penalidade          INTEGER         NOT NULL,
    cod_infracao            INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    observacao              TEXT            NOT NULL,
    valor                   NUMERIC(14,2)   NOT NULL,
    quantidade              NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_notificacao_infracao      PRIMARY KEY                                         (cod_processo, cod_penalidade, cod_infracao),
    CONSTRAINT fk_notificacao_infracao_1    FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.notificacao_fiscalizacao    (cod_processo),
    CONSTRAINT fk_notificacao_infracao_2    FOREIGN KEY                                         (cod_infracao, cod_penalidade)
                                            REFERENCES fiscalizacao.infracao_penalidade         (cod_infracao, cod_penalidade),
    CONSTRAINT fk_notificacao_infracao_3    FOREIGN KEY                                         (cod_tipo_documento, cod_documento)
                                            REFERENCES administracao.modelo_documento           (cod_tipo_documento, cod_documento)
);

CREATE SEQUENCE fiscalizacao.notificacao_termo_num_notificacao_seq;


CREATE TABLE fiscalizacao.notificacao_termo (
    cod_processo            INTEGER         NOT NULL,
    num_notificacao         INTEGER         NOT NULL DEFAULT NEXTVAL('fiscalizacao.notificacao_termo_num_notificacao_seq'),
    cod_fiscal              INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    dt_notificacao          DATE            NOT NULL,
    observacao              TEXT            NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_notificacao_termo          PRIMARY KEY                                         (cod_processo, num_notificacao),
    CONSTRAINT fk_notificacao_termo_1        FOREIGN KEY                                         (cod_processo, cod_fiscal)
                                             REFERENCES fiscalizacao.fiscal_processo_fiscal      (cod_processo, cod_fiscal),
    CONSTRAINT fk_notificacao_termo_2        FOREIGN KEY                                         (cod_processo)
                                             REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_notificacao_termo_3        FOREIGN KEY                                         (cod_documento, cod_tipo_documento)
                                             REFERENCES administracao.modelo_documento           (cod_documento, cod_tipo_documento)
);


CREATE TABLE fiscalizacao.notificacao_termo_infracao (
    cod_processo            INTEGER         NOT NULL,
    num_notificacao         INTEGER         NOT NULL,
    cod_infracao            INTEGER         NOT NULL,
    observacao              TEXT                    ,
    CONSTRAINT pk_notificacao_termo_infracao      PRIMARY KEY                                    (cod_processo, num_notificacao, cod_infracao),
    CONSTRAINT fk_notificacao_termo_infracao_1    FOREIGN KEY                                    (cod_processo, num_notificacao)
                                                  REFERENCES fiscalizacao.notificacao_termo      (cod_processo, num_notificacao),
    CONSTRAINT fk_notificacao_termo_infracao_2    FOREIGN KEY                                    (cod_infracao)
                                                  REFERENCES fiscalizacao.infracao               (cod_infracao)
);


CREATE TABLE fiscalizacao.auto_fiscalizacao (
    cod_processo            INTEGER         NOT NULL,
    cod_auto_fiscalizacao   INTEGER         NOT NULL,
    cod_fiscal              INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    dt_notificacao          DATE            NOT NULL,
    observacao              TEXT            NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_auto_fiscalizacao         PRIMARY KEY                                         (cod_processo, cod_auto_fiscalizacao),
    CONSTRAINT fk_auto_fiscalizacao_1       FOREIGN KEY                                         (cod_processo, cod_fiscal)
                                            REFERENCES fiscalizacao.fiscal_processo_fiscal      (cod_processo, cod_fiscal),
    CONSTRAINT fk_auto_fiscalizacao_2       FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_auto_fiscalizacao_3       FOREIGN KEY                                         (cod_documento, cod_tipo_documento)
                                            REFERENCES administracao.modelo_documento           (cod_documento, cod_tipo_documento)
);


CREATE TABLE fiscalizacao.auto_infracao (
    cod_processo            INTEGER         NOT NULL,
    cod_auto_fiscalizacao   INTEGER         NOT NULL,
    cod_penalidade          INTEGER         NOT NULL,
    cod_infracao            INTEGER         NOT NULL,
    observacao              TEXT            NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    CONSTRAINT pk_auto_infracao             PRIMARY KEY                                         (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao),
    CONSTRAINT fk_auto_infracao_1           FOREIGN KEY                                         (cod_processo, cod_auto_fiscalizacao)
                                            REFERENCES fiscalizacao.auto_fiscalizacao           (cod_processo, cod_auto_fiscalizacao),
    CONSTRAINT fk_auto_infracao_2           FOREIGN KEY                                         (cod_infracao, cod_penalidade)
                                            REFERENCES fiscalizacao.infracao_penalidade         (cod_infracao, cod_penalidade),
    CONSTRAINT fk_auto_infracao_3           FOREIGN KEY                                         (cod_tipo_documento, cod_documento)
                                            REFERENCES administracao.modelo_documento           (cod_tipo_documento, cod_documento)
);


CREATE TABLE fiscalizacao.auto_infracao_multa (
    cod_processo            INTEGER         NOT NULL,
    cod_auto_fiscalizacao   INTEGER         NOT NULL,
    cod_infracao            INTEGER         NOT NULL,
    cod_penalidade          INTEGER         NOT NULL,
    valor                   NUMERIC(14,2)   NOT NULL,
    quantidade              NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_auto_infracao_multa       PRIMARY KEY                                         (cod_processo, cod_auto_fiscalizacao, cod_infracao, cod_penalidade),
    CONSTRAINT fk_auto_infracao_multa_1     FOREIGN KEY                                         (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao)
                                            REFERENCES fiscalizacao.auto_infracao               (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao)
);


CREATE TABLE fiscalizacao.auto_infracao_outros (
    cod_processo            INTEGER         NOT NULL,
    cod_auto_fiscalizacao   INTEGER         NOT NULL,
    cod_infracao            INTEGER         NOT NULL,
    cod_penalidade          INTEGER         NOT NULL,
    dt_ocorrencia           DATE            NOT NULL,
    observacao              TEXT                    ,
    CONSTRAINT pk_auto_infracao_outros      PRIMARY KEY                                         (cod_processo, cod_auto_fiscalizacao, cod_infracao, cod_penalidade),
    CONSTRAINT fk_auto_infracao_outros_1    FOREIGN KEY                                         (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao)
                                            REFERENCES fiscalizacao.auto_infracao               (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao)
);


CREATE TABLE fiscalizacao.penalidade_multa (
    cod_penalidade          INTEGER         NOT NULL,
    cod_indicador           INTEGER         NOT NULL,
    cod_modulo              INTEGER         NOT NULL,
    cod_biblioteca          INTEGER         NOT NULL,
    cod_funcao              INTEGER         NOT NULL,
    cod_unidade             INTEGER         NOT NULL,
    cod_grandeza            INTEGER         NOT NULL,
    CONSTRAINT pk_penalidade_multa          PRIMARY KEY                                         (cod_penalidade),
    CONSTRAINT fk_penalidade_multa_1        FOREIGN KEY                                         (cod_penalidade)
                                            REFERENCES fiscalizacao.penalidade                  (cod_penalidade),
    CONSTRAINT fk_penalidade_multa_2        FOREIGN KEY                                         (cod_indicador)
                                            REFERENCES monetario.indicador_economico            (cod_indicador),
    CONSTRAINT fk_penalidade_multa_3        FOREIGN KEY                                         (cod_modulo, cod_biblioteca, cod_funcao)
                                            REFERENCES administracao.funcao                     (cod_modulo, cod_biblioteca, cod_funcao),
    CONSTRAINT fk_penalidade_multa_4        FOREIGN KEY                                         (cod_unidade, cod_grandeza)
                                            REFERENCES administracao.unidade_medida             (cod_unidade, cod_grandeza)
);


CREATE TABLE fiscalizacao.penalidade_desconto (
    cod_penalidade          INTEGER         NOT NULL,
    cod_desconto            INTEGER         NOT NULL,
    prazo                   INTEGER         NOT NULL,
    desconto                NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_penalidade_desconto       PRIMARY KEY                                         (cod_penalidade, cod_desconto),
    CONSTRAINT fk_penalidade_desconto_1     FOREIGN KEY                                         (cod_penalidade)
                                            REFERENCES fiscalizacao.penalidade                  (cod_penalidade)
);


CREATE TABLE fiscalizacao.prorrogacao_entrega (
    cod_processo            INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    dt_prorrogacao          DATE            NOT NULL,
    CONSTRAINT pk_prorrogacao_entrega       PRIMARY KEY                                         (cod_processo, timestamp),
    CONSTRAINT fk_prorrogacao_entrega_1     FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.inicio_fiscalizacao         (cod_processo)
);


CREATE TABLE fiscalizacao.retencao_fonte (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    valor_retencao          NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_retencao_fonte            PRIMARY KEY                                         (cod_processo, competencia),
    CONSTRAINT fk_retencao_fonte_1          FOREIGN KEY                                         (cod_processo, competencia)
                                            REFERENCES fiscalizacao.processo_levantamento       (cod_processo, competencia)
);


CREATE TABLE fiscalizacao.retencao_nota (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_nota                INTEGER         NOT NULL,
    numcgm                  INTEGER         NOT NULL,
    cod_municipio           INTEGER         NOT NULL,
    cod_uf                  INTEGER         NOT NULL,
    num_serie               INTEGER         NOT NULL,
    num_nota                INTEGER         NOT NULL,
    dt_emissao              DATE            NOT NULL,
    valor_nota              NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_retencao_nota             PRIMARY KEY                                         (cod_processo, competencia, cod_nota),
    CONSTRAINT fk_retencao_nota_1           FOREIGN KEY                                         (cod_processo, competencia)
                                            REFERENCES fiscalizacao.retencao_fonte              (cod_processo, competencia),
    CONSTRAINT fk_retencao_nota_2           FOREIGN KEY                                         (cod_uf, cod_municipio)
                                            REFERENCES sw_municipio                             (cod_uf, cod_municipio),
    CONSTRAINT fk_retencao_nota_3           FOREIGN KEY                                         (numcgm)
                                            REFERENCES sw_cgm                                   (numcgm)
);


CREATE TABLE fiscalizacao.retencao_servico (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_nota                INTEGER         NOT NULL,
    num_servico             INTEGER         NOT NULL,
    cod_servico             INTEGER         NOT NULL,
    valor_declarado         NUMERIC(14,2)   NOT NULL,
    valor_deducao           NUMERIC(14,2)   NOT NULL,
    valor_lancado           NUMERIC(14,2)   NOT NULL,
    aliquota                NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_retencao_servico          PRIMARY KEY                                         (cod_processo, competencia, cod_nota, num_servico),
    CONSTRAINT fk_retencao_servico_1        FOREIGN KEY                                         (cod_processo, competencia, cod_nota)
                                            REFERENCES fiscalizacao.retencao_nota               (cod_processo, competencia, cod_nota),
    CONSTRAINT fk_retencao_servico_2        FOREIGN KEY                                         (cod_servico)
                                            REFERENCES economico.servico                        (cod_servico)
);


CREATE TABLE fiscalizacao.servico_com_retencao (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_servico             INTEGER         NOT NULL,
    cod_atividade           INTEGER         NOT NULL,
    ocorrencia              INTEGER         NOT NULL,
    cod_municipio           INTEGER         NOT NULL,
    cod_uf                  INTEGER         NOT NULL,
    numcgm                  INTEGER         NOT NULL,
    valor_retido            NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_servico_com_retencao      PRIMARY KEY                                         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia, cod_municipio, cod_uf, numcgm),
    CONSTRAINT fk_servico_com_retencao_1    FOREIGN KEY                                         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia)
                                            REFERENCES fiscalizacao.faturamento_servico         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia),
    CONSTRAINT fk_servico_com_retencao_2    FOREIGN KEY                                         (cod_uf, cod_municipio)
                                            REFERENCES sw_municipio                             (cod_uf, cod_municipio),
    CONSTRAINT fk_servico_com_retencao_3    FOREIGN KEY                                         (numcgm)
                                            REFERENCES sw_cgm                                   (numcgm)
);


CREATE TABLE fiscalizacao.servico_sem_retencao (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_servico             INTEGER         NOT NULL,
    cod_atividade           INTEGER         NOT NULL,
    ocorrencia              INTEGER         NOT NULL,
    valor_declarado         NUMERIC(14,2)   NOT NULL,
    valor_deducao           NUMERIC(14,2)   NOT NULL,
    valor_deducao_legal     NUMERIC(14,2)   NOT NULL,
    valor_lancado           NUMERIC(14,2)   NOT NULL,
    aliquota                NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_servico_sem_retencao      PRIMARY KEY                                         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia),
    CONSTRAINT fk_servico_sem_retencao_1    FOREIGN KEY                                         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia)
                                            REFERENCES fiscalizacao.faturamento_servico         (cod_processo, competencia, cod_servico, cod_atividade, ocorrencia)
);


CREATE TABLE fiscalizacao.levantamento (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    receita_declarada       NUMERIC(14,2)   NOT NULL,
    receita_efetiva         NUMERIC(14,2)   NOT NULL,
    iss_pago                NUMERIC(14,2)   NOT NULL,
    iss_devido              NUMERIC(14,2)   NOT NULL,
    iss_devolver            NUMERIC(14,2)   NOT NULL,
    iss_pagar               NUMERIC(14,2)   NOT NULL,
    total_devolver          NUMERIC(14,2)   NOT NULL,
    total_pagar             NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_levantamento              PRIMARY KEY                                         (cod_processo, competencia),
    CONSTRAINT fk_levantamento_1            FOREIGN KEY                                         (cod_processo, competencia)
                                            REFERENCES fiscalizacao.processo_levantamento       (cod_processo, competencia)
 );


CREATE TABLE fiscalizacao.levantamento_correcao (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_indicador           INTEGER         NOT NULL,
    valor                   NUMERIC(14,2)   NOT NULL,
    indice                  NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_levantamento_correcao     PRIMARY KEY                                         (cod_processo, competencia, cod_indicador),
    CONSTRAINT fk_levantamento_correcao_1   FOREIGN KEY                                         (cod_processo, competencia)
                                            REFERENCES fiscalizacao.levantamento                (cod_processo, competencia)
 );


CREATE TABLE fiscalizacao.levantamento_acrescimo (
    cod_processo            INTEGER         NOT NULL,
    competencia             CHAR(7)         NOT NULL,
    cod_acrescimo           INTEGER         NOT NULL,
    cod_tipo                INTEGER         NOT NULL,
    valor                   NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_levantamento_acrescimo    PRIMARY KEY                                         (cod_processo, competencia, cod_acrescimo, cod_tipo),
    CONSTRAINT fk_levantamento_acrescimo_1  FOREIGN KEY                                         (cod_processo, competencia)
                                            REFERENCES  fiscalizacao.levantamento               (cod_processo, competencia),
    CONSTRAINT fk_levantamento_acrescimo_2  FOREIGN KEY                                         (cod_acrescimo, cod_tipo)
                                            REFERENCES  monetario.acrescimo                     (cod_acrescimo, cod_tipo)
 );


CREATE TABLE fiscalizacao.termino_fiscalizacao (
    cod_processo            INTEGER         NOT NULL,
    cod_fiscal              INTEGER         NOT NULL,
    cod_tipo_documento      INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    dt_termino              DATE            NOT NULL,
    observacao              TEXT            NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_termino_fiscalizacao      PRIMARY KEY                                         (cod_processo),
    CONSTRAINT fk_termino_fiscalizacao_1    FOREIGN KEY                                         (cod_processo, cod_fiscal)
                                            REFERENCES fiscalizacao.fiscal_processo_fiscal      (cod_processo, cod_fiscal),
    CONSTRAINT fk_termino_fiscalizacao_2    FOREIGN KEY                                         (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_termino_fiscalizacao_3    FOREIGN KEY                                         (cod_documento, cod_tipo_documento)
                                            REFERENCES administracao.modelo_documento           (cod_documento, cod_tipo_documento)
);


CREATE TABLE fiscalizacao.processo_fiscal_cancelado(
    cod_processo            INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT ('now'::text)::timestamp(3) with time zone,
    numcgm                  INTEGER         NOT NULL,
    justificativa           TEXT            NOT NULL,
    CONSTRAINT pk_processo_fiscal_cancelado PRIMARY KEY                                         (cod_processo),
    CONSTRAINT fk_processo_fiscal_cancelado_1    FOREIGN KEY                                    (cod_processo)
                                            REFERENCES fiscalizacao.processo_fiscal             (cod_processo),
    CONSTRAINT fk_processo_fiscal_cancelado_2    FOREIGN KEY                                    (numcgm)
                                            REFERENCES administracao.usuario                    (numcgm)
);



GRANT ALL ON SCHEMA fiscalizacao                                TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.tipo_fiscalizacao              TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.natureza_fiscalizacao          TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.tipo_local                     TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.tipo_inutilizacao              TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.tipo_penalidade                TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.fiscal                         TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.fiscal_fiscalizacao            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.grafica                        TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.autorizacao_notas              TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.autorizacao_documento          TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.baixa_autorizacao              TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.baixa_notas                    TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.baixa_documento                TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.autenticacao_livro             TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.autenticacao_documento         TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.documento                      TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.processo_fiscal                TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.processo_fiscal_credito        TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.processo_fiscal_empresa        TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.processo_fiscal_grupo          TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.processo_fiscal_obras          TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.fiscal_processo_fiscal         TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.inicio_fiscalizacao            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.inicio_fiscalizacao_documentos TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.documentos_entrega             TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.documento_atividade            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.faturamento_servico            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.infracao                       TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.penalidade                     TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.penalidade_documento           TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.infracao_penalidade            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.nota                           TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.nota_servico                   TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.notificacao_fiscalizacao       TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.notificacao_infracao           TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.notificacao_termo              TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.notificacao_termo_infracao     TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.auto_fiscalizacao              TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.auto_infracao                  TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.auto_infracao_multa            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.auto_infracao_outros           TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.penalidade_multa               TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.penalidade_desconto            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.prorrogacao_entrega            TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.retencao_fonte                 TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.retencao_nota                  TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.retencao_servico               TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.servico_com_retencao           TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.servico_sem_retencao           TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.termino_fiscalizacao           TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.processo_fiscal_cancelado      TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.processo_levantamento          TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.levantamento                   TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.levantamento_correcao          TO GROUP urbem;
GRANT ALL ON TABLE  fiscalizacao.levantamento_acrescimo         TO GROUP urbem;

-- MENU FISCALIZACAO
-- CONFIGURACAO
INSERT 
  INTO administracao.funcionalidade 
VALUES ( 418
       , 34
       , 'Configuração'
       , 'instancias/configuracao/'
       , 1
       );

INSERT 
  INTO administracao.acao 
VALUES ( 2275
       , 418
       , 'FMManterConfiguracao.php'
       , 'configurar'
       , 1
       , ''
       , 'Alterar Configuração'
       );

INSERT 
  INTO administracao.acao 
VALUES ( 2311
       , 418
       , 'FMConfiguracaoInfracao.php'
       , 'configurar'
       , 2
       , ''
       , 'Configuração de Infrações'
       );

INSERT 
  INTO administracao.acao 
VALUES ( 2312
       , 418
       , 'FMManterSeries.php'
       , 'configurar'
       , 3
       , ''
       , 'Configurar Séries'
       );

-- FISCAL
INSERT 
  INTO administracao.funcionalidade 
VALUES ( 419
       , 34
       , 'Fiscal'
       , 'instancias/fiscal/'
       , 2
       );

INSERT 
  INTO administracao.acao 
VALUES ( 2276
       , 419
       , 'FMManterFiscal.php'
       , 'incluir'
       , 1
       , ''
       , 'Incluir Fiscal'
       );

INSERT
  INTO administracao.acao
VALUES ( 2277
       , 419
       , 'FLManterFiscal.php'
       , 'alterar'
       , 2
       , ''
       , 'Alterar Fiscal'
       );

INSERT
  INTO administracao.acao
VALUES ( 2278
       , 419
       , 'FLManterFiscal.php'
       , 'excluir'
       , 3
       , ''
       , 'Excluir Fiscal'
       );


-- GRAFICA
INSERT
  INTO administracao.funcionalidade
VALUES ( 420
       , 34
       , 'Gráfica'
       , 'instancias/grafica/'
       , 3
       );

INSERT
  INTO administracao.acao
VALUES ( 2279
       , 420
       , 'FMManterGrafica.php'
       , 'incluir'
       , 1
       , ''
       , 'Incluir Gráfica'
       );

INSERT
  INTO administracao.acao
VALUES ( 2280
       , 420
       , 'FLManterGrafica.php'
       , 'alterar'
       , 2
       , ''
       , 'Alterar Gráfica'
       );

INSERT
  INTO administracao.acao
VALUES ( 2281
       , 420
       , 'FLManterGrafica.php'
       , 'excluir'
       , 3
       , ''
       , 'Excluir Gráfica'
       );

-- DOCUMENTOS FISCAIS
INSERT
  INTO administracao.funcionalidade
VALUES ( 421
       , 34
       , 'Documentos Fiscais'
       , 'instancias/documentoFiscal/'
       , 3
       );

INSERT
  INTO administracao.acao
VALUES ( 2282
       , 421
       , 'FMManterImpressao.php'
       , 'incluir'
       , 1
       , ''
       , 'Autorizar Impressão'
       );

INSERT
  INTO administracao.acao
VALUES ( 2283
       , 421
       , 'FLManterBaixa.php'
       , 'baixar'
       , 2
       , ''
       , 'Baixar Notas Fiscais'
       );

INSERT
  INTO administracao.acao
VALUES ( 2284
       , 421
       , 'FMManterAutenticacao.php'
       , 'autenticar'
       , 3
       , ''
       , 'Autenticar Livro Fiscal'
       );

-- INFRACOES E PENALIDADES
INSERT
  INTO administracao.funcionalidade
VALUES ( 422
       , 34
       , 'Infrações e Penalidades'
       , 'instancias/infracaoPenalidade/'
       , 4
       );

INSERT
  INTO administracao.acao
VALUES ( 2285
       , 422
       , 'FMManterPenalidade.php'
       , 'incluir'
       , 1
       , ''
       , 'Incluir Penalidade'
       );

INSERT
  INTO administracao.acao
VALUES ( 2286
       , 422
       , 'FLManterPenalidade.php'
       , 'alterar'
       , 2
       , ''
       , 'Alterar Penalidade'
       );

INSERT
  INTO administracao.acao
VALUES ( 2287
       , 422
       , 'FLManterPenalidade.php'
       , 'excluir'
       , 3
       , ''
       , 'Excluir Penalidade'
       );

INSERT
  INTO administracao.acao
VALUES ( 2288
       , 422
       , 'FMManterInfracao.php'
       , 'incluir'
       , 4
       , ''
       , 'Incluir Infração'
       );

INSERT
  INTO administracao.acao
VALUES ( 2289
       , 422
       , 'FLManterInfracao.php'
       , 'alterar'
       , 5
       , ''
       , 'Alterar Infração'
       );

INSERT
  INTO administracao.acao
VALUES ( 2290
       , 422
       , 'FLManterInfracao.php'
       , 'excluir'
       , 6
       , ''
       , 'Excluir Infração'
       );

-- DOCUMENTOS
INSERT
  INTO administracao.funcionalidade
VALUES ( 423
       , 34
       , 'Documentos'
       , 'instancias/documento/'
       , 5
       );

INSERT
  INTO administracao.acao
VALUES ( 2291
       , 423
       , 'FMManterDocumento.php'
       , 'incluir'
       , 1
       , ''
       , 'Incluir Documento'
       );

INSERT
  INTO administracao.acao
VALUES ( 2292
       , 423
       , 'FLManterDocumento.php'
       , 'alterar'
       , 2
       , ''
       , 'Alterar Documento'
       );

INSERT
  INTO administracao.acao
VALUES ( 2293
       , 423
       , 'FLManterDocumento.php'
       , 'excluir'
       , 3
       , ''
       , 'Excluir Documento'
       );

INSERT
  INTO administracao.acao
VALUES ( 2294
       , 423
       , 'FMManterVinculo.php'
       , 'vincular'
       , 4
       , ''
       , 'Vincular Documentos à Atividades'
       );

-- PROCESSO FISCAL
INSERT
  INTO administracao.funcionalidade
VALUES ( 424
       , 34
       , 'Processo Fiscal'
       , 'instancias/processoFiscal/'
       , 6
       );

INSERT
  INTO administracao.acao
VALUES ( 2295
       , 424
       , 'FLManterProcesso.php'
       , 'incluir'
       , 1
       , ''
       , 'Cadastrar Processo Fiscal'
       );

INSERT
  INTO administracao.acao
VALUES ( 2296
       , 424
       , 'FLManterProcesso.php'
       , 'alterar'
       , 2
       , ''
       , 'Alterar Processo Fiscal'
       );

INSERT
  INTO administracao.acao
VALUES ( 2297
       , 424
       , 'FLManterProcesso.php'
       , 'cancelar'
       , 3
       , ''
       , 'Cancelar Processo Fiscal'
       );

INSERT
  INTO administracao.acao
VALUES ( 2298
       , 424
       , 'FLIniciarProcessoFiscal.php'
       , 'iniciar'
       , 4
       , ''
       , 'Iniciar Processo Fiscal'
       );

INSERT
  INTO administracao.acao
VALUES ( 2299
       , 424
       , 'FLProrrogarRecebimentoDocumentos.php'
       , 'prorrogar'
       , 5
       , ''
       , 'Prorrogar Recebimento de Documentos'
       );

INSERT
  INTO administracao.acao
VALUES ( 2300
       , 424
       , 'FLReceberDocumentos.php'
       , 'receber'
       , 6
       , ''
       , 'Receber Documentos'
       );

INSERT
  INTO administracao.acao
VALUES ( 2301
       , 424
       , 'FLDevolverDocumentos.php'
       , 'devolver'
       , 7
       , ''
       , 'Devolver Documentos'
       );

INSERT
  INTO administracao.acao
VALUES ( 2302
       , 424
       , 'FLManterLevantamento.php'
       , 'lancar'
       , 8
       , ''
       , 'Cadastrar Lançamentos Fiscais'
       );

INSERT
  INTO administracao.acao
VALUES ( 2303
       , 424
       , 'FLGerarPlanilhaLancamentos.php'
       , 'gerar'
       , 9
       , ''
       , 'Gerar Planilha de Lançamentos'
       );

INSERT
  INTO administracao.acao
VALUES ( 2304
       , 424
       , 'FLManterProcesso.php'
       , 'encerrar'
       , 10
       , ''
       , 'Encerrar Processo Fiscal'
       );

INSERT
  INTO administracao.acao
VALUES ( 2305
       , 424
       , 'FLManterProcesso.php'
       , 'notificar'
       , 11
       , ''
       , 'Emitir Auto de Infração'
       );

-- CONSULTAS
INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 425
         , 34
         , 'Consultas'
         , 'instancias/consultas/'
         , 100
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2307
          , 425
          , 'FLConsultarProcesso.php'
          , 'consultar'
          , 1
          , ''
          , 'Consultar Processo Fiscal'
          );


-- RELATORIOS
INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 426
         , 34
         , 'Relatórios'
         , 'instancias/relatorios/'
         , 101
         );

-- EMISSAO DE TERMOS
INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 448
         , 34
         , 'Emissão de Termos'
         , 'instancias/termo/'
         , 7
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2410
          , 448
          , 'FLEmitirAutoInfracao.php'
          , 'notificar'
          , 1
          , ''
          , 'Auto de infração'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2411
          , 448
          , 'FLEmitirTermo.php'
          , 'demolicao'
          , 2
          , ''
          , 'Termo de Demolição'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2412
          , 448
          , 'FLEmitirTermo.php'
          , 'embargo'
          , 3
          , ''
          , 'Termo de Embargo'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2413
          , 448
          , 'FLEmitirTermo.php'
          , 'interdicao'
          , 4
          , ''
          , 'Termo de Interdição'
          );


-- CONFIGURACOES

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'norma_inicio'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'norma_termino'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'documento_auto_infracao'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'infracao_docs_nao_entregues'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'infracao_docs_fora_prazo'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'infracao_docs_parcial'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'infracao_pagamento_menor'
          , ''
          );

INSERT INTO administracao.configuracao
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( 2008
          , 34
          , 'infracao_declaracao_menor'
          , ''
          );

----------------
-- Ticket #13824
----------------

CREATE OR REPLACE FUNCTION economico.verifica_cgm_empresa( ) RETURNS TRIGGER AS $$
DECLARE

    inCGM       INTEGER;
    inEmpresa   INTEGER;

BEGIN

       SELECT DISTINCT COALESCE( ef.numcgm, ed.numcgm, au.numcgm )                              AS NUMCGM
            , ce.inscricao_economica
         INTO inCGM
            , inEmpresa
         FROM economico.cadastro_economico                                                      AS CE
    LEFT JOIN economico.cadastro_economico_empresa_fato                                         AS EF
           ON ce.inscricao_economica = ef.inscricao_economica
    LEFT JOIN economico.cadastro_economico_autonomo                                             AS AU
           ON ce.inscricao_economica = au.inscricao_economica
    LEFT JOIN economico.cadastro_economico_empresa_direito                                      AS ED
           ON ce.inscricao_economica = ed.inscricao_economica
    LEFT JOIN economico.baixa_cadastro_economico
           ON baixa_cadastro_economico.inscricao_economica = ce.inscricao_economica
          AND baixa_cadastro_economico.dt_termino IS NULL
        WHERE COALESCE( ef.numcgm, ed.numcgm, au.numcgm ) = NEW.numcgm;

    IF FOUND THEN
        RAISE EXCEPTION 'CGM % pertencente a outra Inscrição Econômica. Contate suporte!',NEW.numcgm;
    END IF;

    RETURN NEW;

END;
$$ LANGUAGE 'plpgsql';

CREATE TRIGGER tr_verifica_cgm_autonomo BEFORE INSERT OR UPDATE ON economico.cadastro_economico_autonomo        FOR EACH ROW EXECUTE PROCEDURE economico.verifica_cgm_empresa();
CREATE TRIGGER tr_verifica_cgm_direito  BEFORE INSERT OR UPDATE ON economico.cadastro_economico_empresa_direito FOR EACH ROW EXECUTE PROCEDURE economico.verifica_cgm_empresa();
CREATE TRIGGER tr_verifica_cgm_fato     BEFORE INSERT OR UPDATE ON economico.cadastro_economico_empresa_fato    FOR EACH ROW EXECUTE PROCEDURE economico.verifica_cgm_empresa();


----------------
-- Ticket #13763
----------------

INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1 from administracao.arquivos_documento), 'termoInscricaoDAUrbem.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1 from administracao.modelo_documento), 'Termo Inscrição DA', 'termoInscricaoDAUrbem.agt', 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1648, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1649, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1637, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5); 
INSERT INTO administracao.modelo_arquivos_documento VALUES (1638, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1634, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1635, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1636, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);

INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1 from administracao.arquivos_documento), 'certidaoDAUrbem.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1 from administracao.modelo_documento), 'Certidão DA', 'certidaoDAUrbem.agt', 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1648, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1649, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1637, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1638, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1634, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1635, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1636, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);

INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1 from administracao.arquivos_documento), 'memorialCalculoDAUrbem.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1 from administracao.modelo_documento), 'Memorial Calculo DA', 'memorialCalculoDAUrbem.agt', 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1648, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1649, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1637, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1638, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1634, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1635, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1636, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);

INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1 from administracao.arquivos_documento), 'termoParcelamentoDAUrbem.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1 from administracao.modelo_documento), 'Termo Parcelamento DA', 'termoParcelamentoDAUrbem.agt', 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1648, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1649, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1637, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1638, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1634, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1635, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1636, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);

INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1 from administracao.arquivos_documento), 'termoConsolidacaoDAUrbem.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1 from administracao.modelo_documento), 'Termo Consolidação DA', 'termoConsolidacaoDAUrbem.agt', 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1648, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1649, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1637, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1638, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1634, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1635, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1636, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5); 


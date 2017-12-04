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
* $Id:$
*
* Versão 2.00.7
*/

----------------
-- Ticket #18361
----------------

CREATE TABLE tcern.natureza_juridica(
    cod_natureza    INTEGER     NOT NULL,
    descricao       VARCHAR(80) NOT NULL,
    CONSTRAINT pk_natureza_juridica PRIMARY KEY (cod_natureza)
);
GRANT ALL ON TABLE tcern.natureza_juridica TO urbem;

INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 1, 'Assembléia Legislativa'     );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 2, 'Autarquia'                  );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 3, 'Câmara Municipal'           );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 4, 'Empresa Pública'            );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 5, 'Fundação Pública'           );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 6, 'Governo do Estado'          );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 7, 'Fundo Especial'             );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 8, 'Ministério Público'         );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES ( 9, 'Órgão Autônomo'             );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES (10, 'Órgao Descentralizado'      );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES (11, 'Prefeitura Municipal'       );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES (12, 'Secretaria de Governo'      );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES (13, 'Sociedade de Economia Mista');
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES (14, 'Tribunal de Contas'         );
INSERT INTO tcern.natureza_juridica (cod_natureza, descricao) VALUES (15, 'Tribunal de Justiça'        );


CREATE TABLE tcern.funcao_gestor(
    cod_funcao  NUMERIC(1,0)    NOT NULL,
    descricao   VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_funcao_gestor PRIMARY KEY (cod_funcao)
);
GRANT ALL ON TABLE tcern.funcao_gestor TO urbem;

INSERT INTO tcern.funcao_gestor (cod_funcao, descricao) VALUES (1, 'Gestor'                       );
INSERT INTO tcern.funcao_gestor (cod_funcao, descricao) VALUES (2, 'Gestor e Ordenador de despesa');
INSERT INTO tcern.funcao_gestor (cod_funcao, descricao) VALUES (3, 'Ordenador de despesa'         );


CREATE TABLE tcern.unidade_gestora(
    id                  INTEGER         NOT NULL,
    cod_institucional   NUMERIC(10,0)   NOT NULL,
    cgm_unidade         INTEGER         NOT NULL,
    personalidade       NUMERIC(1,0)    NOT NULL,
    administracao       NUMERIC(1,0)    NOT NULL,
    natureza            INTEGER         NOT NULL,
    cod_norma           INTEGER         NOT NULL,
    situacao            BOOLEAN         NOT NULL,
    exercicio           VARCHAR(4)      NOT NULL,
    CONSTRAINT pk_unidade_gestora       PRIMARY KEY                         (id),
    CONSTRAINT fk_unidade_gestora_1     FOREIGN KEY                         (cgm_unidade)
                                        REFERENCES sw_cgm                   (numcgm),
    CONSTRAINT fk_unidade_gestora_2     FOREIGN KEY                         (natureza)
                                        REFERENCES tcern.natureza_juridica  (cod_natureza),
    CONSTRAINT fk_unidade_gestora_3     FOREIGN KEY                         (cod_norma)
                                        REFERENCES normas.norma             (cod_norma),
    CONSTRAINT uk_unidade_gestora       UNIQUE                              (exercicio, cod_institucional)
);
GRANT ALL ON TABLE tcern.unidade_gestora TO urbem;


CREATE TABLE tcern.unidade_gestora_responsavel(
    id                  INTEGER         NOT NULL,
    id_unidade          INTEGER         NOT NULL,
    cgm_responsavel     INTEGER         NOT NULL,
    cargo               VARCHAR(30)     NOT NULL,
    cod_funcao          NUMERIC(1,0)    NOT NULL,
    dt_inicio           DATE            NOT NULL,
    dt_fim              DATE            NOT NULL,
    CONSTRAINT pk_unidade_gestora_responsavel   PRIMARY KEY                         (id),
    CONSTRAINT fk_unidade_gestora_responsavel_1 FOREIGN KEY                         (id_unidade)
                                                REFERENCES tcern.unidade_gestora    (id),
    CONSTRAINT fk_unidade_gestora_responsavel_2 FOREIGN KEY                         (cgm_responsavel)
                                                REFERENCES sw_cgm                   (numcgm),
    CONSTRAINT fk_unidade_gestora_responsavel_3 FOREIGN KEY                         (cod_funcao)
                                                REFERENCES tcern.funcao_gestor      (cod_funcao)
);
GRANT ALL ON TABLE tcern.unidade_gestora_responsavel TO urbem;


CREATE TABLE tcern.unidade_orcamentaria(
    id                          INTEGER         NOT NULL,
    cod_institucional           NUMERIC(10,0)   NOT NULL,
    cgm_unidade_orcamentaria    INTEGER         NOT NULL,
    cod_norma                   INTEGER         NOT NULL,
    id_unidade_gestora          INTEGER         NOT NULL,
    situacao                    BOOLEAN         NOT NULL,
    exercicio                   VARCHAR(4)      NOT NULL,
    num_unidade                 INTEGER         NOT NULL,
    num_orgao                   INTEGER         NOT NULL,
    CONSTRAINT pk_unidade_orcamentaria          PRIMARY KEY                         (id),
    CONSTRAINT fk_unidade_orcamentaria_1        FOREIGN KEY                         (cgm_unidade_orcamentaria)
                                                REFERENCES sw_cgm                   (numcgm),
    CONSTRAINT fk_unidade_orcamentaria_2        FOREIGN KEY                         (cod_norma)
                                                REFERENCES normas.norma             (cod_norma),
    CONSTRAINT fk_unidade_orcamentaria_3        FOREIGN KEY                         (id_unidade_gestora)
                                                REFERENCES tcern.unidade_gestora    (id),
    CONSTRAINT fk_unidade_orcamentaria_4        FOREIGN KEY                         (num_unidade, num_orgao, exercicio)
                                                REFERENCES orcamento.unidade        (num_unidade, num_orgao, exercicio),
    CONSTRAINT uk_unidade_orcamentaria          UNIQUE                              (exercicio, cod_institucional)
);
GRANT ALL ON TABLE tcern.unidade_orcamentaria TO urbem;


CREATE TABLE tcern.unidade_orcamentaria_responsavel(
    id                  INTEGER         NOT NULL,
    id_unidade          INTEGER         NOT NULL,
    cgm_responsavel     INTEGER         NOT NULL,
    cargo               VARCHAR(30)     NOT NULL,
    cod_funcao          NUMERIC(1,0)    NOT NULL,
    dt_inicio           DATE            NOT NULL,
    dt_fim              DATE            NOT NULL,
    CONSTRAINT pk_unidade_orcamentaria_responsavel      PRIMARY KEY                             (id),
    CONSTRAINT fk_unidade_orcamentaria_responsavel_1    FOREIGN KEY                             (id_unidade)
                                                        REFERENCES tcern.unidade_orcamentaria   (id),
    CONSTRAINT fk_unidade_orcamentaria_responsavel_2    FOREIGN KEY                             (cgm_responsavel)
                                                        REFERENCES sw_cgm                       (numcgm),
    CONSTRAINT fk_unidade_orcamentaria_responsavel_3    FOREIGN KEY                             (cod_funcao)
                                                        REFERENCES tcern.funcao_gestor          (cod_funcao)
);
GRANT ALL ON TABLE tcern.unidade_orcamentaria_responsavel TO urbem;


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
     ( 2819
     , 403
     , 'FMManterConfiguracaoUnidadeGestora.php'
     , 'confgUnGest'
     , 4
     , ''
     , 'Configurar Unidade Gestora'
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
     ( 2820
     , 403
     , 'FLManterConfiguracaoUnidadeOrcamentaria.php'
     , 'confgUnOrc'
     , 5
     , ''
     , 'Configurar Unidade Orçamentária'
     );


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'tcmgo'
        AND tablename  = 'processos'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tcmgo.processos (
            cod_empenho              INTEGER  NOT NULL,
            cod_entidade             INTEGER  NOT NULL,
            exercicio                CHAR(4)  NOT NULL,
            numero_processo          CHAR(8),
            exercicio_processo       CHAR(4),
            processo_administrativo  CHAR(20),
            CONSTRAINT pk_processos         PRIMARY KEY                 (cod_empenho, cod_entidade, exercicio),
            CONSTRAINT fk_processos_empenho FOREIGN KEY                 (cod_empenho, cod_entidade, exercicio)
                                            REFERENCES empenho.empenho  (cod_empenho, cod_entidade, exercicio)
        );
        GRANT ALL ON tcmgo.processos TO GROUP urbem;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #18269
----------------

INSERT
  INTO administracao.acao
VALUES
     ( 2823
     , 403
     , 'FMManterConfiguracaoConvenio.php'
     , 'incluir'
     , 4
     , ''
     , 'Incluir convênio'
     );

INSERT
  INTO administracao.acao
VALUES
     ( 2824
     , 403
     , 'FLManterConfiguracaoConvenio.php'
     , 'manter'
     , 5
     , ''
     , 'Alterar convênio'
     );

INSERT
  INTO administracao.acao
VALUES
     ( 2825
     , 403
     , 'FMManterConfiguracaoContrato.php'
     , 'incluir'
     , 6
     , ''
     , 'Incluir contrato'
     );

INSERT
  INTO administracao.acao
VALUES
     ( 2826
     , 403
     , 'FLManterConfiguracaoContrato.php'
     , 'manter'
     , 7
     , ''
     , 'Alterar contrato'
     );

INSERT
  INTO administracao.acao
VALUES
     ( 2827
     , 403
     , 'FMManterConfiguracaoContratoAditivo.php'
     , 'incluir'
     , 8
     , ''
     , 'Incluir aditivo de contrato'
     );

INSERT
  INTO administracao.acao
VALUES
     ( 2828
     , 403
     , 'FLManterConfiguracaoContratoAditivo.php'
     , 'manter'
     , 9
     , ''
     , 'Alterar aditivo contrato'
     );


CREATE TABLE tcern.convenio(
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    num_convenio        INTEGER         NOT NULL,
    cod_processo        INTEGER         NOT NULL,
    exercicio_processo  CHAR(4)         NOT NULL,
    numcgm_recebedor    INTEGER         NOT NULL,
    cod_objeto          INTEGER         NOT NULL,
    cod_recurso_1       INTEGER,
    cod_recurso_2       INTEGER,
    cod_recurso_3       INTEGER,
    valor_recurso_1     NUMERIC(14,2),
    valor_recurso_2     NUMERIC(14,2),
    valor_recurso_3     NUMERIC(14,2),
    dt_inicio_vigencia  DATE            NOT NULL,
    dt_termino_vigencia DATE            NOT NULL,
    dt_assinatura       DATE            NOT NULL,
    dt_publicacao       DATE            NOT NULL,
    CONSTRAINT pk_tcern_convenio            PRIMARY KEY                 (num_convenio, cod_entidade, exercicio),
    CONSTRAINT fk_tcern_objeto              FOREIGN KEY                 (cod_objeto)
                                            REFERENCES compras.objeto   (cod_objeto),
    CONSTRAINT fk_tcern_convenio_processo   FOREIGN KEY                 (cod_processo, exercicio_processo)
                                            REFERENCES sw_processo      (cod_processo, ano_exercicio)
);
GRANT ALL ON TABLE tcern.convenio TO urbem;


CREATE TABLE tcern.contrato(
    cod_entidade                    INTEGER         NOT NULL,
    num_contrato                    INTEGER         NOT NULL,
    exercicio                       CHAR(4)         NOT NULL,
    exercicio_contrato              CHAR(4)         NOT NULL,
    num_convenio                    INTEGER         NOT NULL,
    cod_processo                    INTEGER         NOT NULL,
    exercicio_processo              CHARACTER(4)    NOT NULL,
    bimestre                        INTEGER         NOT NULL,
    cod_conta_especifica            CHARACTER(50)   NOT NULL,
    dt_entrega_recurso              DATE            NOT NULL,
    valor_repasse                   NUMERIC(14,2)   NOT NULL,
    valor_executado                 NUMERIC(14,2)   NOT NULL,
    receita_aplicacao_financeira    NUMERIC(14,2)   NOT NULL,
    dt_recebimento_saldo            DATE            NOT NULL,
    dt_prestacao_contas             DATE            NOT NULL,
    CONSTRAINT pk_tcern_contrato            PRIMARY KEY                 (num_convenio, cod_entidade, exercicio, num_contrato, exercicio_contrato),
    CONSTRAINT fk_tcern_contrato_convenio   FOREIGN KEY                 (num_convenio, cod_entidade, exercicio)
                                            REFERENCES tcern.convenio   (num_convenio, cod_entidade, exercicio),
    CONSTRAINT fk_tcern_contrato_processo   FOREIGN KEY                 (cod_processo, exercicio_processo)
                                            REFERENCES sw_processo      (cod_processo, ano_exercicio)
);
GRANT ALL ON TABLE tcern.contrato TO urbem;


CREATE TABLE tcern.contrato_aditivo(
    num_convenio            INTEGER         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    num_contrato_aditivo    INTEGER         NOT NULL,
    exercicio_aditivo       CHAR(4)         NOT NULL,
    cod_processo            INTEGER         NOT NULL,
    exercicio_processo      CHAR(4)         NOT NULL,
    bimestre                INTEGER         NOT NULL,
    cod_objeto              INTEGER         NOT NULL,
    valor_aditivo           NUMERIC(14,2)   NOT NULL,
    dt_inicio_vigencia      DATE            NOT NULL,
    dt_termino_vigencia     DATE            NOT NULL,
    dt_assinatura           DATE            NOT NULL,
    dt_publicacao           DATE            NOT NULL,
    CONSTRAINT pk_tcern_contrato_aditivo            PRIMARY KEY (num_convenio, cod_entidade, exercicio, num_contrato_aditivo, exercicio_aditivo),
    CONSTRAINT fk_tcern_contrato_aditivo_convenio   FOREIGN KEY (num_convenio, cod_entidade, exercicio)
                                                    REFERENCES tcern.convenio (num_convenio, cod_entidade, exercicio),
    CONSTRAINT fk_tcern_contrato_aditivo_objeto     FOREIGN KEY (cod_objeto)
                                                    REFERENCES compras.objeto (cod_objeto),
    CONSTRAINT fk_tcern_contrato_aditivo_processo   FOREIGN KEY (cod_processo, exercicio_processo)
                                                    REFERENCES sw_processo (cod_processo, ano_exercicio)
);
GRANT ALL ON TABLE tcern.contrato_aditivo TO urbem;



----------------
-- Ticket #18270
----------------

INSERT INTO administracao.acao VALUES (2829, 403, 'FMManterConfiguracaoObra.php'              , 'incluir', 16, '', 'Incluir obra'                  );
INSERT INTO administracao.acao VALUES (2830, 403, 'FLManterConfiguracaoObra.php'              , 'manter' , 17, '', 'Alterar obra'                  );
INSERT INTO administracao.acao VALUES (2831, 403, 'FMManterConfiguracaoObraContrato.php'      , 'incluir', 18, '', 'Incluir contrato de obra'      );
INSERT INTO administracao.acao VALUES (2832, 403, 'FLManterConfiguracaoObraContrato.php'      , 'manter' , 19, '', 'Alterar contrato de obra'      );
INSERT INTO administracao.acao VALUES (2833, 403, 'FMManterConfiguracaoObraAcompanhamento.php', 'incluir', 20, '', 'Incluir acompanhamento de obra');
INSERT INTO administracao.acao VALUES (2834, 403, 'FLManterConfiguracaoObraAcompanhamento.php', 'manter' , 21, '', 'Alterar acompanhamento de obra');
INSERT INTO administracao.acao VALUES (2835, 403, 'FMManterConfiguracaoObraAditivo.php'       , 'incluir', 22, '', 'Incluir aditivo de obra'       );
INSERT INTO administracao.acao VALUES (2846, 403, 'FLManterConfiguracaoObraAditivo.php'       , 'manter' , 23, '', 'Alterar aditivo de obra'       );


CREATE TABLE tcern.obra (
    cod_entidade          INTEGER        NOT NULL,
    exercicio             VARCHAR(4)     NOT NULL,
    num_obra              INTEGER        NOT NULL,
    obra                  VARCHAR(150)   NOT NULL,
    objetivo              VARCHAR(50)    NOT NULL,
    localizacao           VARCHAR(50)    NOT NULL,
    cod_cidade            INTEGER        NOT NULL,
    cod_recurso_1         INTEGER                ,
    cod_recurso_2         INTEGER                ,
    cod_recurso_3         INTEGER                ,
    valor_recurso_1       NUMERIC(14,2)          ,
    valor_recurso_2       NUMERIC(14,2)          ,
    valor_recurso_3       NUMERIC(14,2)          ,
    valor_orcamento_base  NUMERIC(14,2)  NOT NULL,
    projeto_existente     VARCHAR(255)   NOT NULL,
    observacao            VARCHAR(100)   NOT NULL,
    latitude              NUMERIC(14,2)  NOT NULL,
    longitude             NUMERIC(14,2)  NOT NULL,
    rdc                   INTEGER        NOT NULL,
    CONSTRAINT pk_tcern_obra PRIMARY KEY (cod_entidade, exercicio, num_obra)
);
GRANT ALL ON TABLE tcern.obra TO urbem;


CREATE TABLE tcern.obra_contrato (
    id                        INTEGER        NOT NULL,
    cod_entidade              INTEGER        NOT NULL,
    exercicio                 VARCHAR(4)     NOT NULL,
    num_obra                  INTEGER        NOT NULL,
    num_contrato              VARCHAR(50)    NOT NULL,
    servico                   VARCHAR(255)   NOT NULL,
    processo_licitacao        VARCHAR(10)    NOT NULL,
    numcgm                    INTEGER        NOT NULL,
    valor_contrato            NUMERIC(14,2)  NOT NULL,
    valor_executado_exercicio NUMERIC(14,2)  NOT NULL,
    valor_a_exercutar         NUMERIC(14,2)  NOT NULL,
    dt_inicio_contrato        DATE           NOT NULL,
    dt_termino_contrato       DATE           NOT NULL,
    num_art                   INTEGER        NOT NULL,
    valor_iss                 NUMERIC(14,2)  NOT NULL,
    num_dcms                  INTEGER        NOT NULL,
    valor_inss                NUMERIC(14,2)  NOT NULL,
    numcgm_fiscal             INTEGER        NOT NULL,
    CONSTRAINT pk_tcern_obra_contrato             PRIMARY KEY           (id),
    CONSTRAINT fk_tcern_obra_contrato_obra        FOREIGN KEY           (cod_entidade, exercicio, num_obra)
                                                  REFERENCES tcern.obra (cod_entidade, exercicio, num_obra),
    CONSTRAINT fk_tcern_obra_contrato_cgm         FOREIGN KEY           (numcgm)
                                                  REFERENCES sw_cgm     (numcgm),
    CONSTRAINT fk_tcern_obra_contrato_cgm_fiscal  FOREIGN KEY           (numcgm_fiscal)
                                                  REFERENCES sw_cgm     (numcgm),
    CONSTRAINT uk_tcern_obra_contrato             UNIQUE                (num_contrato)
);
GRANT ALL ON TABLE tcern.obra_contrato TO urbem;


CREATE TABLE tcern.obra_acompanhamento_situacao (
    cod_situacao     INTEGER      NOT NULL,
    situacao         VARCHAR(50)  NOT NULL,
    CONSTRAINT pk_tcern_obra_acompanhamento_situacao PRIMARY KEY (cod_situacao)
);
GRANT ALL ON TABLE tcern.obra_acompanhamento_situacao TO urbem;

INSERT INTO tcern.obra_acompanhamento_situacao VALUES (1, '1 - Contratada aguardando'       );
INSERT INTO tcern.obra_acompanhamento_situacao VALUES (2, '2 - Emitido OS aguardando inÃ­cio');
INSERT INTO tcern.obra_acompanhamento_situacao VALUES (3, '3 - Em andamento normal'         );
INSERT INTO tcern.obra_acompanhamento_situacao VALUES (4, '4 - Em andamento lento'          );
INSERT INTO tcern.obra_acompanhamento_situacao VALUES (5, '5 - Paralisada'                  );
INSERT INTO tcern.obra_acompanhamento_situacao VALUES (6, '6 - Concluida'                   );
INSERT INTO tcern.obra_acompanhamento_situacao VALUES (7, '7 - Inacabada'                   );


CREATE TABLE tcern.obra_acompanhamento (
    id                 INTEGER      NOT NULL,
    obra_contrato_id   INTEGER      NOT NULL,
    dt_evento          DATE         NOT NULL,
    numcgm_responsavel INTEGER      NOT NULL,
    cod_situacao       INTEGER      NOT NULL,
    justificativa      VARCHAR(255) NOT NULL,
    CONSTRAINT pk_tcern_obra_acompanhamento          PRIMARY KEY                                   (id),
    CONSTRAINT fk_tcern_obra_acompanhamento_contrato FOREIGN KEY                                   (obra_contrato_id)
                                                     REFERENCES tcern.obra_contrato                (id),
    CONSTRAINT fk_tcern_obra_acompanhamento_cgm      FOREIGN KEY                                   (numcgm_responsavel)
                                                     REFERENCES sw_cgm                             (numcgm),
    CONSTRAINT fk_tcern_obra_acompanhamento_situacao FOREIGN KEY                                   (cod_situacao)
                                                     REFERENCES tcern.obra_acompanhamento_situacao (cod_situacao)
);
GRANT ALL ON TABLE tcern.obra_acompanhamento TO urbem;


CREATE TABLE tcern.obra_aditivo (
    id                INTEGER       NOT NULL,
    obra_contrato_id  INTEGER       NOT NULL,
    num_aditivo       VARCHAR(10)   NOT NULL,
    dt_aditivo        DATE          NOT NULL,
    prazo             VARCHAR(100)  NOT NULL,
    prazo_aditado     VARCHAR(100)  NOT NULL,
    valor             NUMERIC(14,2) NOT NULL,
    valor_aditado     NUMERIC(14,2) NOT NULL,
    num_art           INTEGER       NOT NULL,
    motivo            VARCHAR(255)  NOT NULL,

    CONSTRAINT pk_tcern_obra_aditivo          PRIMARY KEY                    (id),
    CONSTRAINT fk_tcern_obra_aditivo_contrato FOREIGN KEY                    (obra_contrato_id)
                                              REFERENCES tcern.obra_contrato (id)
);
GRANT ALL ON TABLE tcern.obra_aditivo TO urbem;


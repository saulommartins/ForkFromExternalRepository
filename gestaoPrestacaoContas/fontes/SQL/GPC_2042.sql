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
* Versao 2.04.2
*
* Fabio Bertoldi - 20150728
*
*/

----------------
-- Ticket #23127
----------------

ALTER TABLE tcmba.tipo_responsavel_ordenador ALTER COLUMN descricao TYPE VARCHAR(50);

INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 1, 'Prefeito/Presidente'                     );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 2, 'Secretário'                              );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 3, 'Tesoureiro/Pagador'                      );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 4, 'Responsável Bens Patrimoniais'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 5, 'Responsável Bens Almoxarifado'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 6, 'Presidente Comissão Permanente Licitação');
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 7, 'Chefe Órgão Controle Interno'            );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES ( 9, 'Pregoeiro'                               );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (10, 'Pregoeiro Substituto'                    );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (11, 'Equipe de Apoio (Pregão)'                );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (12, 'Vice-Prefeito'                           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (13, 'Retificador de Despesas'                 );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (14, 'Atuário'                                 );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (15, 'Contador'                                );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (16, 'Secretário de Finanças'                  );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (17, 'Responsável por Fiscalizar Obra'         );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (18, 'Primeiro Secretário da Câmara'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (19, 'Vereador'                                );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (20, 'Delegação do Controle Interno'           );
INSERT INTO  tcmba.tipo_responsavel_ordenador (cod_tipo_responsavel_ordenador, descricao) VALUES (21, 'Participante de Conselho/Comite'         );


----------------
-- Ticket #23166
----------------

CREATE TABLE tcmba.tipo_regime_tce(
    cod_tipo_regime_tce     INTEGER     NOT NULL,
    descricao               VARCHAR(30) NOT NULL,
    CONSTRAINT pk_tipo_regime_tce       PRIMARY KEY (cod_tipo_regime_tce)
);
GRANT ALL ON tcmba.tipo_regime_tce TO urbem;

INSERT INTO tcmba.tipo_regime_tce(cod_tipo_regime_tce, descricao) VALUES (1, 'C.L.T'                );
INSERT INTO tcmba.tipo_regime_tce(cod_tipo_regime_tce, descricao) VALUES (2, 'Estatutário'          );
INSERT INTO tcmba.tipo_regime_tce(cod_tipo_regime_tce, descricao) VALUES (3, 'Regime Administrativo');
INSERT INTO tcmba.tipo_regime_tce(cod_tipo_regime_tce, descricao) VALUES (4, 'Agente Político'      );
INSERT INTO tcmba.tipo_regime_tce(cod_tipo_regime_tce, descricao) VALUES (5, 'Comissionado'         );
INSERT INTO tcmba.tipo_regime_tce(cod_tipo_regime_tce, descricao) VALUES (6, 'Gratificação/Jeton'   );

SELECT atualizarbanco('ALTER TABLE pessoal.de_para_tipo_cargo_tcmba ADD   COLUMN cod_tipo_regime_tce INTEGER;');
SELECT atualizarbanco('UPDATE      pessoal.de_para_tipo_cargo_tcmba SET          cod_tipo_regime_tce = 1;');
SELECT atualizarbanco('ALTER TABLE pessoal.de_para_tipo_cargo_tcmba ALTER COLUMN cod_tipo_regime_tce SET NOT NULL;');

SELECT atualizarbanco('ALTER TABLE pessoal.de_para_tipo_cargo_tcmba ADD CONSTRAINT fk_de_para_tipo_cargo_tcmba_3 FOREIGN KEY                     (cod_tipo_regime_tce)
                                                                                                                 REFERENCES tcmba.tipo_regime_tce(cod_tipo_regime_tce);');


----------------
-- Ticket #23160
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
     ( 3076
     , 390
     , 'FMConfigurarTipoDocumento.php'
     , 'configurar'
     , 8
     , ''
     , 'Configurar Tipo de Documento'
     , TRUE
     );

CREATE TABLE tcmba.tipo_documento_tcm(
    cod_documento_tcm   INTEGER         NOT NULL,
    descricao           VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tipo_documento_tcm    PRIMARY KEY (cod_documento_tcm)
);
GRANT ALL ON tcmba.tipo_documento_tcm TO urbem;

INSERT INTO tcmba.tipo_documento_tcm VALUES (1, 'INSS'               );
INSERT INTO tcmba.tipo_documento_tcm VALUES (2, 'Fazenda Federal'    );
INSERT INTO tcmba.tipo_documento_tcm VALUES (3, 'Fazenda Estadual'   );
INSERT INTO tcmba.tipo_documento_tcm VALUES (4, 'Fazenda Municipal'  );
INSERT INTO tcmba.tipo_documento_tcm VALUES (5, 'FGTS'               );
INSERT INTO tcmba.tipo_documento_tcm VALUES (6, 'C. Reg. Cadastral'  );
INSERT INTO tcmba.tipo_documento_tcm VALUES (7, 'Justiça do Trabalho');
INSERT INTO tcmba.tipo_documento_tcm VALUES (9, 'Outras'             );


CREATE TABLE tcmba.documento_de_para(
    cod_documento_tcm   INTEGER         NOT NULL,
    cod_documento       INTEGER         NOT NULL,
    CONSTRAINT pk_documento_de_para     PRIMARY KEY                         (cod_documento_tcm, cod_documento),
    CONSTRAINT fk_documento_de_para_1   FOREIGN KEY                         (cod_documento_tcm)
                                        REFERENCES tcmba.tipo_documento_tcm (cod_documento_tcm),
    CONSTRAINT fk_documento_de_para_2   FOREIGN KEY                         (cod_documento)
                                        REFERENCES licitacao.documento      (cod_documento)
);
GRANT ALL ON tcmba.documento_de_para TO urbem;


DROP TABLE tcmba.tipo_certidao;


----------------
-- Ticket #22982
----------------

CREATE TYPE tcmba.tp_desp_ext AS ( conta_contabil   VARCHAR     --1
                                 , vl_mes           NUMERIC     --2
                                 , vl_ate_mes       NUMERIC     --3
                                 );


----------------
-- Ticket #23174
----------------

UPDATE administracao.acao SET ordem = 9 WHERE cod_acao = 3076;

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
     ( 3077
     , 390
     , 'FLManterConfiguracaoRatificador.php'
     , 'manter'
     , 8
     , ''
     , 'Configurar Ratificador'
     , TRUE
     );


ALTER TABLE tcmba.configuracao_ordenador DROP CONSTRAINT fk_configuracao_ordenador_4;
ALTER TABLE tcmba.configuracao_ordenador ADD   COLUMN cod_tipo_responsavel INTEGER;
UPDATE      tcmba.configuracao_ordenador SET          cod_tipo_responsavel = cod_tipo_responsavel_ordenador;
ALTER TABLE tcmba.configuracao_ordenador ALTER COLUMN cod_tipo_responsavel SET NOT NULL;
ALTER TABLE tcmba.configuracao_ordenador DROP  COLUMN cod_tipo_responsavel_ordenador;

ALTER TABLE tcmba.tipo_responsavel_ordenador DROP CONSTRAINT pk_tipo_responsavel_ordenador;
ALTER TABLE tcmba.tipo_responsavel_ordenador ADD   COLUMN cod_tipo_responsavel INTEGER;
UPDATE      tcmba.tipo_responsavel_ordenador SET          cod_tipo_responsavel = cod_tipo_responsavel_ordenador;
ALTER TABLE tcmba.tipo_responsavel_ordenador ALTER COLUMN cod_tipo_responsavel SET NOT NULL;
ALTER TABLE tcmba.tipo_responsavel_ordenador DROP  COLUMN cod_tipo_responsavel_ordenador;

ALTER TABLE tcmba.tipo_responsavel_ordenador RENAME TO tipo_responsavel;
ALTER TABLE tcmba.tipo_responsavel       ADD CONSTRAINT pk_tipo_responsavel PRIMARY KEY (cod_tipo_responsavel);

ALTER TABLE tcmba.configuracao_ordenador ADD CONSTRAINT fk_configuracao_ordenador_4 FOREIGN KEY (cod_tipo_responsavel)
                                                                                    REFERENCES tcmba.tipo_responsavel(cod_tipo_responsavel);

CREATE TABLE tcmba.configuracao_ratificador(
    exercicio                           CHAR(4)     NOT NULL,
    cod_entidade                        INTEGER     NOT NULL,
    cgm_ratificador                     INTEGER     NOT NULL,
    num_unidade                         INTEGER     NOT NULL,
    num_orgao                           INTEGER     NOT NULL,
    dt_inicio_vigencia                  DATE        NOT NULL,
    dt_fim_vigencia                     DATE        NOT NULL,
    cod_tipo_responsavel                INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_ratificador          PRIMARY KEY                      (exercicio, cod_entidade, cgm_ratificador, num_unidade, num_orgao),
    CONSTRAINT fk_configuracao_ratificador_1        FOREIGN KEY                      (exercicio, cod_entidade)
                                                    REFERENCES orcamento.entidade    (exercicio, cod_entidade),
    CONSTRAINT fk_configuracao_ratificador_2        FOREIGN KEY                      (cgm_ratificador)
                                                    REFERENCES sw_cgm_pessoa_fisica  (numcgm),
    CONSTRAINT fk_configuracao_ratificador_3        FOREIGN KEY                      (exercicio, num_unidade, num_orgao)
                                                    REFERENCES orcamento.unidade     (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_configuracao_ratificador_4        FOREIGN KEY                      (cod_tipo_responsavel)
                                                    REFERENCES tcmba.tipo_responsavel(cod_tipo_responsavel)
);
GRANT ALL ON tcmba.configuracao_ratificador TO urbem;


----------------
-- Ticket #22984
----------------

CREATE TYPE fn_demonstrativo_consolidado_receita AS (
    cod_estrutural              varchar,
    receita                     integer,
    recurso                     varchar,
    descricao                   varchar,
    valor_previsto              numeric,
    arrecadado_mes              numeric,
    arrecadado_ate_periodo      numeric,
    anulado_mes                 numeric,
    anulado_ate_periodo         numeric 
);


----------------
-- Ticket #23203
----------------

ALTER TABLE tcemg.registro_precos_orgao ALTER COLUMN numero_processo_adesao TYPE VARCHAR(12);


----------------
-- Ticket #23211
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
     ( 3078
     , 390
     , 'FMManterTipoNorma.php'
     , 'configurar'
     , 10
     , ''
     , 'Configurar Tipo Norma'
     , TRUE
     );


DROP TABLE tcmba.tipo_norma;

CREATE TABLE tcmba.tipo_norma(
    cod_tipo    INTEGER         NOT NULL,
    descricao   VARCHAR(20)     NOT NULL,
    CONSTRAINT pk_tipo_norma    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcmba.tipo_norma TO urbem;

INSERT INTO tcmba.tipo_norma VALUES (1, 'Decreto'  );
INSERT INTO tcmba.tipo_norma VALUES (2, 'Edital'   );
INSERT INTO tcmba.tipo_norma VALUES (3, 'Lei'      );
INSERT INTO tcmba.tipo_norma VALUES (4, 'Portaria' );
INSERT INTO tcmba.tipo_norma VALUES (5, 'Resolução');
INSERT INTO tcmba.tipo_norma VALUES (6, 'Ato'      );
INSERT INTO tcmba.tipo_norma VALUES (9, 'Outros'   );

CREATE TABLE tcmba.vinculo_tipo_norma(
    cod_tipo_norma      INTEGER         NOT NULL,
    cod_tipo            INTEGER         NOT NULL,
    CONSTRAINT pk_vinculo_tipo_norma    PRIMARY KEY                  (cod_tipo_norma),
    CONSTRAINT fk_vinculo_tipo_norma_1  FOREIGN KEY                  (cod_tipo_norma)
                                        REFERENCES normas.tipo_norma (cod_tipo_norma),
    CONSTRAINT fk_vinculo_tipo_norma_2  FOREIGN KEY                  (cod_tipo)
                                        REFERENCES tcmba.tipo_norma  (cod_tipo)
);
GRANT ALL ON tcmba.vinculo_tipo_norma TO urbem;


----------------
-- Ticket #23176
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
     ( 3079
     , 390
     , 'FLManterConfiguracaoSubvencoesEmpenho.php'
     , 'configurar'
     , 11
     , ''
     , 'Configurar Subvenções de Empenhos'
     , TRUE
     );

CREATE TABLE tcmba.subvencao_empenho(
    numcgm                  INTEGER     NOT NULL,
    dt_inicio               DATE        NOT NULL,
    dt_termino              DATE        NOT NULL,
    prazo_aplicacao         INTEGER     NOT NULL,
    prazo_comprovacao       INTEGER     NOT NULL,
    cod_norma_utilidade     INTEGER     NOT NULL,
    cod_norma_valor         INTEGER     NOT NULL,
    cod_banco               INTEGER     NOT NULL,
    cod_agencia             INTEGER     NOT NULL,
    cod_conta_corrente      INTEGER     NOT NULL,
    CONSTRAINT pk_subvencao_empenho     PRIMARY KEY                         (numcgm),
    CONSTRAINT fk_subvencao_empenho_1   FOREIGN KEY                         (numcgm)
                                        REFERENCES sw_cgm                   (numcgm),
    CONSTRAINT fk_subvencao_empenho_2   FOREIGN KEY                         (cod_norma_utilidade)
                                        REFERENCES normas.norma             (cod_norma),
    CONSTRAINT fk_subvencao_empenho_3   FOREIGN KEY                         (cod_norma_valor)
                                        REFERENCES normas.norma             (cod_norma),
    CONSTRAINT fk_subvencao_empenho_4   FOREIGN KEY                         (cod_banco, cod_agencia, cod_conta_corrente)
                                        REFERENCES monetario.conta_corrente (cod_banco, cod_agencia, cod_conta_corrente)
);
GRANT ALL ON tcmba.subvencao_empenho TO urbem;


----------------
-- Ticket #23242
----------------

DROP FUNCTION tcemg.recupera_ppa_inclusao_programa(VARCHAR);


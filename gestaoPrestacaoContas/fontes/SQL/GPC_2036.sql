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
* Versao 2.03.6
*
* Franver Sarmento de Moraes - 20150121
*
*/

----------------
-- Ticket #22312
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
     ( 3031
     , 364
     , 'FMManterConfiguracaoLeisPPA.php'
     , 'manter'
     , 50
     , ''
     , 'Configurar Leis PPA'
     , TRUE
     );

CREATE TABLE tcmgo.configuracao_leis_ppa (
    exercicio                   CHAR(4)         NOT NULL,
    cod_norma                   INTEGER         NOT NULL,
    tipo_configuracao           VARCHAR         NOT NULL,
    status                      BOOLEAN         NOT NULL,
    cod_veiculo_publicacao      INTEGER         NOT NULL DEFAULT 0,
    descricao_publicacao        TEXT            NOT NULL DEFAULT '',
    CONSTRAINT pk_tcmgo_configuracao_leis_ppa   PRIMARY KEY             (exercicio, cod_norma, tipo_configuracao),
    CONSTRAINT fk_tcmgo_configuracao_leis_ppa_1 FOREIGN KEY             (cod_norma)
                                                REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcmgo.configuracao_leis_ppa TO urbem;


----------------
-- Ticket #22393
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
     ( 3033
     , 364
     , 'FLManterResponsavelLicitacao.php'
     , 'manter'
     , 52
     , ''
     , 'Configurar Responsável Licitação'
     , TRUE
     );

CREATE TABLE tcmgo.responsavel_licitacao(
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
    CONSTRAINT fk_resplic_8         FOREIGN KEY                     (cgm_resp_pesquisa)
                                    REFERENCES sw_cgm_pessoa_fisica (numcgm)

);
GRANT ALL ON tcmgo.responsavel_licitacao TO urbem;


----------------
-- Ticket #22315
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
     ( 3034
     , 364
     , 'FMManterConfiguracaoLOA.php'
     , 'manter'
     , 51
     , ''
     , 'Configurar LOA'
     , TRUE
     );

CREATE TABLE tcmgo.configuracao_loa (
    exercicio                               CHAR(4)        NOT NULL,
    cod_norma                               INTEGER        NOT NULL,
    percentual_suplementacao                NUMERIC(14,2)          ,
    percentual_credito_interna              NUMERIC(14,2)          ,
    percentual_credito_antecipacao_receita  NUMERIC(14,2)          ,
    CONSTRAINT pk_tcmgo_configuracao_loa                   PRIMARY KEY             (exercicio),
    CONSTRAINT fk_tcmgo_configuracao_loa_1                 FOREIGN KEY             (cod_norma)
                                                           REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcmgo.configuracao_loa TO urbem;


----------------
-- Ticket #22314
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
     ( 3032
     , 364
     , 'FMManterConfiguracaoLeisLDO.php'
     , ''
     , 51
     , ''
     , 'Configurar LDO'
     , TRUE
     );

CREATE TABLE tcmgo.configuracao_leis_ldo (
    exercicio                   CHAR(4)         NOT NULL,
    cod_norma                   INTEGER         NOT NULL,
    tipo_configuracao           VARCHAR         NOT NULL,
    status                      BOOLEAN         NOT NULL,
    CONSTRAINT pk_tcmgo_configuracao_leis_ldo   PRIMARY KEY             (exercicio, cod_norma, tipo_configuracao),
    CONSTRAINT fk_tcmgo_configuracao_leis_ldo_1 FOREIGN KEY             (cod_norma)
                                                REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcmgo.configuracao_leis_ldo TO urbem;


----------------
-- Ticket #22320
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
     ( 3035
     , 364
     , 'FLManterConfiguracaoMetasArrecadacaoReceita.php'
     , ''
     , 56
     , ''
     , 'Metas de Arrecadação de Receita'
     , TRUE
     );

CREATE TABLE tcmgo.metas_arrecadacao_receita (
    exercicio                         CHAR(4)     NOT NULL,
    meta_arrecadacao_1_bi             NUMERIC(14,2)       ,
    meta_arrecadacao_2_bi             NUMERIC(14,2)       ,
    meta_arrecadacao_3_bi             NUMERIC(14,2)       ,
    meta_arrecadacao_4_bi             NUMERIC(14,2)       ,
    meta_arrecadacao_5_bi             NUMERIC(14,2)       ,
    meta_arrecadacao_6_bi             NUMERIC(14,2)       ,
    CONSTRAINT pk_tcmgo_metas_arrecadacao_receita   PRIMARY KEY  (exercicio)
);

GRANT ALL ON tcmgo.metas_arrecadacao_receita TO urbem;


----------------
-- Ticket #22319
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
     ( 3038
     , 364
     , 'FLManterConfiguracaoMetasFiscaisLDO.php'
     , 'manter'
     , 57
     , 'Detalhamento das Metas Fiscais LDO'
     , 'Configurar Metas Fiscais LDO'
     , TRUE
     );

CREATE TABLE tcmgo.metas_fiscais_ldo (
    exercicio                                   char(4) NOT NULL,
    valor_corrente_receita                      NUMERIC(14,2)   ,
    valor_corrente_despesa                      NUMERIC(14,2)   ,
    valor_corrente_resultado_primario           NUMERIC(14,2)   ,
    valor_corrente_resultado_nominal            NUMERIC(14,2)   ,
    valor_corrente_divida_consolidada_liquida   NUMERIC(14,2)   ,
    CONSTRAINT pk_tcmgo_metas_fiscais_ldo PRIMARY KEY (exercicio)
);
GRANT ALL ON tcmgo.metas_fiscais_ldo TO urbem;


----------------
-- Ticket #22649
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
     ( 3037
     , 363
     , 'FLExportacaoOrcamento.php'
     , 'exportar'
     , 3
     , ''
     , 'Arquivos de Orçamento'
     , TRUE
     );


-----------------------
-- Ticket #22311 #22373
-----------------------

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
     ( 3039
     , 364
     , 'FMManterTecnicoResponsavel.php'
     , 'incluir'
     , 58
     , ''
     , 'Configurar Responsável Técnico'
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
     ( 3040
     , 364
     , 'FMManterProvedorSistema.php'
     , 'incluir'
     , 59
     , ''
     , 'Configurar Provedor do Sistema'
     , TRUE
     );

CREATE TABLE tcmgo.tipo_responsavel_tecnico(
    cod_tipo    INTEGER     NOT NULL,
    descricao   VARCHAR(30) NOT NULL,
    CONSTRAINT pk_tipo_responsavel_tecnico  PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcmgo.tipo_responsavel_tecnico TO urbem;

INSERT INTO tcmgo.tipo_responsavel_tecnico VALUES (1, 'Contador');
INSERT INTO tcmgo.tipo_responsavel_tecnico VALUES (2, 'Técnico de Contabilidade');


CREATE TABLE tcmgo.responsavel_tecnico(
    cgm_responsavel     INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_tipo            INTEGER     NOT NULL,
    crc                 varchar(10)         ,
    dt_inicio           DATE        NOT NULL,
    dt_fim              DATE        NOT NULL,
    CONSTRAINT pk_responsavel_tecnico   PRIMARY KEY                      (cgm_responsavel),
    CONSTRAINT fk_responsavel_tecnico_1 FOREIGN KEY                      (cgm_responsavel)
                                                    REFERENCES sw_cgm    (numcgm),
    CONSTRAINT fk_responsavel_tecnico_2 FOREIGN KEY                      (cod_entidade, exercicio)
                                        REFERENCES orcamento.entidade    (cod_entidade, exercicio),
    CONSTRAINT fk_responsavel_tecnico_3 FOREIGN KEY                      (cod_tipo)
                                        REFERENCES tcmgo.tipo_responsavel_tecnico(cod_tipo)
);
GRANT ALL ON tcmgo.responsavel_tecnico TO urbem;


INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2015', 42, 'provedor_sistema', '');


CREATE TABLE tcmgo.divida_fundada (
    exercicio            CHAR(4)       NOT NULL,
    cod_entidade         INTEGER       NOT NULL,
    num_unidade          INTEGER       NOT NULL,
    num_orgao            INTEGER       NOT NULL,
    cod_norma            INTEGER       NOT NULL,
    numcgm               INTEGER               ,
    cod_tipo_lancamento  INTEGER       NOT NULL,
    valor_saldo_anterior NUMERIC(14,2) NOT NULL,
    valor_contratacao    NUMERIC(14,2) NOT NULL,
    valor_amortizacao    NUMERIC(14,2) NOT NULL,
    valor_cancelamento   NUMERIC(14,2) NOT NULL,
    valor_encampacao     NUMERIC(14,2) NOT NULL,
    valor_correcao       NUMERIC(14,2) NOT NULL,
    valor_saldo_atual    NUMERIC(14,2) NOT NULL,
    CONSTRAINT pk_tcmgo_divida_fundada PRIMARY KEY (cod_norma, cod_entidade, exercicio)
);
GRANT ALL ON tcmgo.divida_fundada TO urbem;


INSERT INTO administracao.acao
          (cod_acao,
           cod_funcionalidade,
           nom_arquivo,
           parametro,
           ordem,
           complemento_acao,
           nom_acao,
           ativo
          )
       VALUES
         ( 3044
         , 364
         , 'FLManterDividaFundada.php'
         , 'filtrar'
         , 50
         , 'Configuração da Dívida Fundada Interna, Externa e Diversos'
         , 'Configurar Dívida Fundada Int, Ext e Diversos'
         , TRUE
         );


----------------
-- Ticket #22368
----------------

UPDATE administracao.acao SET ativo= TRUE where cod_acao = 1764;


----------------
-- Ticket #22639
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
     ( 3030
     , 386
     , 'FMManterContratosLiquidacao.php'
     , 'incluir'
     , 3
     , ''
     , 'Configuração para Contratos na Liquidação'
     , TRUE
     );

CREATE TABLE tcers.contratos_liquidacao (
    cod_liquidacao      BIGINT         NOT NULL ,
    cod_contrato        NUMERIC        NOT NULL,
    cod_contrato_tce    INTEGER        NOT NULL,
    exercicio           CHAR(4)        NOT NULL,
    CONSTRAINT pk_contratos_liquidacao PRIMARY KEY (cod_liquidacao, exercicio)
    );
GRANT ALL ON tcers.contratos_liquidacao TO urbem;


----------------
-- Ticket #22362
----------------

CREATE TABLE tcmgo.patrimonio_bem_obra (
    cod_bem      INTEGER     NOT NULL,
    cod_obra     INTEGER     NOT NULL,
    ano_obra     INTEGER     NOT NULL,
    CONSTRAINT pk_patrimonio_bem_obra   PRIMARY KEY               (cod_bem, cod_obra, ano_obra),
    CONSTRAINT fk_patrimonio_bem_obra_1 FOREIGN KEY               (cod_bem)
                                        REFERENCES patrimonio.bem (cod_bem),
    CONSTRAINT fk_patrimonio_bem_obra_2 FOREIGN KEY               (cod_obra,ano_obra)
                                        REFERENCES tcmgo.obra     (cod_obra,ano_obra)
);
GRANT ALL ON tcmgo.patrimonio_bem_obra TO urbem;



----------------
-- Ticket #22684
----------------

CREATE TABLE tcmgo.responsavel_licitacao_dispensa (
    exercicio                   CHAR(4)     NOT NULL,
    cod_entidade                INTEGER     NOT NULL,
    cod_modalidade              INTEGER     NOT NULL,
    cod_licitacao               INTEGER     NOT NULL,
    cgm_resp_abertura_disp      INTEGER,
    cgm_resp_cotacao_precos     INTEGER,
    cgm_resp_recurso            INTEGER,
    cgm_resp_ratificacao        INTEGER,
    cgm_resp_publicacao_orgao   INTEGER,
    cgm_resp_parecer_juridico   INTEGER,
    cgm_resp_parecer_outro      INTEGER,
    CONSTRAINT pk_resplicdis                PRIMARY KEY (exercicio, cod_entidade, cod_modalidade, cod_licitacao),
    CONSTRAINT fk_resplicdis_1              FOREIGN KEY (exercicio, cod_entidade, cod_modalidade, cod_licitacao)
                                            REFERENCES licitacao.licitacao (exercicio, cod_entidade, cod_modalidade, cod_licitacao),
    CONSTRAINT fk_resplicdis_2              FOREIGN KEY (cgm_resp_cotacao_precos)
                                            REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplicdis_3              FOREIGN KEY (cgm_resp_recurso)
                                            REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplicdis_4              FOREIGN KEY (cgm_resp_ratificacao)
                                            REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplicdis_5              FOREIGN KEY (cgm_resp_publicacao_orgao)
                                            REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplicdis_6              FOREIGN KEY (cgm_resp_parecer_juridico)
                                            REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplicdis_7              FOREIGN KEY (cgm_resp_parecer_outro)
                                            REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_resplicdis_8              FOREIGN KEY (cgm_resp_abertura_disp)
                                            REFERENCES sw_cgm_pessoa_fisica (numcgm)
);
GRANT ALL ON tcmgo.responsavel_licitacao_dispensa TO urbem;


----------------
-- Ticket #22640
----------------

ALTER TABLE almoxarifado.localizacao_fisica DROP CONSTRAINT fk_localizacao_fisica_1;
ALTER TABLE almoxarifado.localizacao_fisica ADD  CONSTRAINT fk_localizacao_fisica_1 FOREIGN KEY (cod_almoxarifado)
                                                                                    REFERENCES almoxarifado.almoxarifado (cod_almoxarifado);

DROP TABLE almoxarifado.localizacao;


----------------
-- Ticket #22703
----------------

CREATE TABLE tcemg.arquivo_emp(
    exercicio                   CHAR(4) NOT NULL,
    cod_entidade                INTEGER NOT NULL,
    cod_empenho                 INTEGER NOT NULL,
    cod_licitacao               INTEGER NOT NULL,
    exercicio_licitacao         CHAR(4) NOT NULL,
    cod_modalidade              INTEGER NOT NULL,
    CONSTRAINT pk_arquivo_emp           PRIMARY KEY                   (exercicio, cod_entidade, cod_empenho),
    CONSTRAINT fk_arquivo_emp_1         FOREIGN KEY                   (exercicio, cod_entidade, cod_empenho)
                                        REFERENCES  empenho.empenho   (exercicio, cod_entidade, cod_empenho),
    CONSTRAINT fk_arquivo_emp_2         FOREIGN KEY                   (cod_modalidade)
                                        REFERENCES compras.modalidade (cod_modalidade)
);
GRANT ALL ON tcemg.arquivo_emp TO urbem;

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
     ( 3045
     , 451
     , 'FMManterConfiguracaoEMP.php'
     , 'manter'
     , 50
     , ''
     , 'Configurar EMP'
     , TRUE
     );


----------------
-- Ticket #22684
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
     ( 3046
     , 364
     , 'FLManterResponsavelLicitacaoDispensa.php'
     , 'manter'
     , 53
     , 'Configuração para licitações do tipo dispensa'
     , 'Configurar Responsável Licitação/Dispensa'
     , TRUE
     );


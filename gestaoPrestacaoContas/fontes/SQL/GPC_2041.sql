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
* Versao 2.04.1
*
* Fabio Bertoldi - 20150618
*
*/

----------------
-- Ticket #23021
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
     ( 3051
     , 364
     , 'FMConfigurarTipoDocumento.php'
     , 'configurar'
     , 60
     , ''
     , 'Configurar Tipo de Documento'
     , TRUE
     );

CREATE TABLE tcmgo.tipo_documento_tcm (
    cod_documento_tcm       INTEGER     NOT NULL,
    descricao               varchar(80) NOT NULL,
    CONSTRAINT pk_tipo_documento_tcm    PRIMARY KEY (cod_documento_tcm)
);
GRANT ALL ON tcmgo.tipo_documento_tcm TO urbem;

INSERT INTO tcmgo.tipo_documento_tcm VALUES (1, 'CPF'                                      );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (2, 'CNPJ'                                     );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (3, 'Documento de Estrangeiros'                );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (4, 'Certidão de Regularidade do INSS'         );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (5, 'Certidão de Regularidade do FGTS'         );
INSERT INTO tcmgo.tipo_documento_tcm VALUES (6, 'Certidão Negativa de Débitos Trabalhistas');

CREATE TABLE tcmgo.documento_de_para (
    cod_documento_tcm       INTEGER     NOT NULL,
    cod_documento           INTEGER     NOT NULL,
    CONSTRAINT pk_documento_de_para     PRIMARY KEY                         (cod_documento_tcm, cod_documento),
    CONSTRAINT fk_documento_de_para_1   FOREIGN KEY                         (cod_documento_tcm)
                                        REFERENCES  tcmgo.tipo_documento_tcm(cod_documento_tcm),
    CONSTRAINT fk_documento_de_para_2   FOREIGN KEY                         (cod_documento)
                                        REFERENCES  licitacao.documento     (cod_documento)
);
GRANT ALL ON tcmgo.documento_de_para TO urbem;


---------------
-- Ticket #23018
----------------

CREATE TABLE tcemg.registro_precos_licitacao(
    cod_entidade                INTEGER         NOT NULL,
    numero_registro_precos      INTEGER         NOT NULL,
    exercicio                   CHAR(4)         NOT NULL,
    interno                     BOOLEAN         NOT NULL,
    numcgm_gerenciador          INTEGER         NOT NULL,

    cod_licitacao               INTEGER         NOT NULL,
    cod_modalidade              INTEGER         NOT NULL,
    cod_entidade_licitacao      INTEGER         NOT NULL,
    exercicio_licitacao         CHAR(4)         NOT NULL,

    CONSTRAINT pk_registro_precos_licitacao     PRIMARY KEY                       (cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador, cod_licitacao, cod_modalidade, cod_entidade_licitacao, exercicio_licitacao),
    CONSTRAINT fk_registro_precos_licitacao_1   FOREIGN KEY                       (cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador)
                                                REFERENCES  tcemg.registro_precos (cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador),
    CONSTRAINT fk_registro_precos_licitacao_2   FOREIGN KEY                       (cod_licitacao, cod_modalidade, cod_entidade_licitacao, exercicio_licitacao)
                                                REFERENCES  licitacao.licitacao   (cod_licitacao, cod_modalidade, cod_entidade, exercicio)
);
GRANT ALL ON tcemg.registro_precos_licitacao TO urbem;


----------------
-- Ticket #22969
----------------

UPDATE administracao.acao
   SET nom_acao  = 'Básicos'
     , parametro = 'basicos'
     , ordem     = 1
 WHERE cod_acao = 1851
     ;

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
     ( 3052
     , 382
     , 'FLManterExportacao.php'
     , 'programa'
     , 2
     , ''
     , 'Programa'
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
     ( 3053
     , 382
     , 'FLManterExportacao.php'
     , 'orcamento'
     , 3
     , ''
     , 'Orçamento'
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
     ( 3054
     , 382
     , 'FLManterExportacao.php'
     , 'ldo'
     , 4
     , ''
     , 'LDO'
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
     ( 3055
     , 382
     , 'FLManterExportacao.php'
     , 'programacao'
     , 5
     , ''
     , 'Programação Financeira'
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
     ( 3056
     , 382
     , 'FLManterExportacao.php'
     , 'consumo'
     , 6
     , ''
     , 'Consumo'
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
     ( 3057
     , 382
     , 'FLManterExportacao.php'
     , 'informes'
     , 7
     , ''
     , 'Informes Mensais'
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
     ( 3058
     , 382
     , 'FLManterExportacao.php'
     , 'consolidados'
     , 8
     , ''
     , 'Consolidados'
     , TRUE
     );


----------------
-- Ticket #22360
----------------

DROP FUNCTION tcmgo.arquivo_afr_exportacao10 (varchar, varchar, varchar, varchar, bpchar);
DROP FUNCTION tcmgo.arquivo_afr_exportacao11 (varchar, varchar, varchar, varchar, bpchar);


----------------
-- Ticket #23021
----------------

DELETE FROM administracao.configuracao_entidade WHERE cod_modulo = 42;

CREATE TABLE tcmgo.poder(
    cod_poder       INTEGER     NOT NULL,
    nom_poder       VARCHAR(20) NOT NULL,
    CONSTRAINT pk_poder         PRIMARY KEY (cod_poder)
);
GRANT ALL ON tcmgo.poder TO urbem;

INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (1, 'Poder Executivo'  );
INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (2, 'Poder Legislativo');
INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (3, 'RPPS'             );
INSERT INTO tcmgo.poder (cod_poder, nom_poder) VALUES (4, 'Outros'           );

CREATE TABLE tcmgo.configuracao_orgao_unidade(
    exercicio       CHAR(4)     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    cod_poder       INTEGER     NOT NULL,
    num_orgao       INTEGER     NOT NULL,
    num_unidade     INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_orgao_unidade    PRIMARY KEY                   (exercicio, cod_entidade, cod_poder),
    CONSTRAINT fk_configuracao_orgao_unidade_1  FOREIGN KEY                   (exercicio, cod_entidade)
                                                REFERENCES orcamento.entidade (exercicio, cod_entidade),
    CONSTRAINT fk_configuracao_orgao_unidade_2  FOREIGN KEY                   (cod_poder)
                                                REFERENCES tcmgo.poder        (cod_poder),
    CONSTRAINT fk_configuracao_orgao_unidade_3  FOREIGN KEY                   (exercicio, num_orgao, num_unidade)
                                                REFERENCES orcamento.unidade  (exercicio, num_orgao, num_unidade)
);
GRANT ALL ON tcmgo.configuracao_orgao_unidade TO urbem;


----------------
-- Ticket #23072
----------------

ALTER TABLE tcemg.consideracao_arquivo_descricao ADD   COLUMN modulo_sicom VARCHAR(10);
UPDATE      tcemg.consideracao_arquivo_descricao SET          modulo_sicom = 'mensal';
ALTER TABLE tcemg.consideracao_arquivo_descricao ALTER COLUMN modulo_sicom SET NOT NULL;

ALTER TABLE tcemg.consideracao_arquivo_descricao DROP CONSTRAINT pk_consideracao_arquivo_descricao;
ALTER TABLE tcemg.consideracao_arquivo_descricao ADD  CONSTRAINT pk_consideracao_arquivo_descricao
               PRIMARY KEY (cod_arquivo, periodo, cod_entidade, exercicio, modulo_sicom);

INSERT INTO tcemg.consideracao_arquivo VALUES (42, 'BALANCETE');


----------------
-- Ticket #23027
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
     )
VALUES
     ( 3067
     , 390
     , 'FLManterTipoCargo.php'
     , 'configurar'
     , 6
     , ''
     , 'Relacionar Tipo de Cargo'
     );

CREATE TABLE tcmba.tipo_cargo_tce (
    cod_tipo_cargo_tce  INTEGER         NOT NULL,
    descricao           VARCHAR(450)    NOT NULL,
    CONSTRAINT pk_tipo_cargo            PRIMARY KEY (cod_tipo_cargo_tce)    
);
GRANT ALL ON tcmba.tipo_cargo_tce TO urbem;

INSERT INTO tcmba.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (1, 'Cargo comissionado'                    );
INSERT INTO tcmba.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (2, 'Cargo Efetivo'                         );
INSERT INTO tcmba.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (3, 'Agente Político'                       );
INSERT INTO tcmba.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (4, 'Temporário'                            );
INSERT INTO tcmba.tipo_cargo_tce ( cod_tipo_cargo_tce, descricao ) VALUES (8, 'Função Gratificada/Disposição'         );

SELECT atualizarbanco('
CREATE TABLE pessoal.de_para_tipo_cargo_tcmba (
    cod_sub_divisao         INTEGER             NOT NULL,
    cod_tipo_cargo_tce      INTEGER             NOT NULL,
    CONSTRAINT pk_de_para_tipo_cargo_tcmba      PRIMARY KEY                     (cod_sub_divisao, cod_tipo_cargo_tce),
    CONSTRAINT fk_de_para_tipo_cargo_tcmba_1    FOREIGN KEY                     (cod_sub_divisao)
                                                REFERENCES pessoal.sub_divisao  (cod_sub_divisao),
    CONSTRAINT fk_de_para_tipo_cargo_tcmba_2    FOREIGN KEY                     (cod_tipo_cargo_tce)
                                                REFERENCES tcmba.tipo_cargo_tce (cod_tipo_cargo_tce)
);
');
SELECT atualizarbanco('GRANT ALL ON pessoal.de_para_tipo_cargo_tcmba  TO urbem;');


----------------
-- Ticket #22844
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
     ( 3068
     , 482
     , 'FLExportarBalanceteContabil.php'
     , 'exportar_contab'
     , 4
     , 'Módulo Balancete Contábil'
     , 'Balancete Contábil'
     , TRUE
     );


CREATE TABLE tcemg.balancete_contabil_14_restos_pagar (
    num_orgao                        INTEGER,
    num_unidade                      INTEGER,
    cod_funcao                       INTEGER,
    cod_subfuncao                    INTEGER,
    num_programa                     INTEGER,
    num_acao                         INTEGER,
    cod_recurso                      INTEGER,
    natureza_despesa                 VARCHAR,
    cod_empenho                      INTEGER,
    exercicio_empenho                VARCHAR,
    tipo_valor                       CHAR(1),
    vl_lancamento_rp_processados     NUMERIC,
    vl_lancamento_rp_nao_processados NUMERIC,
    cod_entidade                     INTEGER,
    cod_lote                         INTEGER,
    dt_lote                             DATE,
    exercicio                        VARCHAR,
    tipo                             CHAR(1),
    sequencia                        INTEGER,
    oid_temp                         INTEGER,
    cod_sistema                      INTEGER
);
GRANT ALL ON tcemg.balancete_contabil_14_restos_pagar TO urbem;


CREATE TYPE balancete_contabil_registro_10
    AS ( tipo_registro          INTEGER
       , conta_contabil         VARCHAR
       , saldo_inicial          NUMERIC
       , natureza_saldo_inicial CHAR(1)
       , total_debitos          NUMERIC
       , total_creditos         NUMERIC
       , saldo_final            NUMERIC
       , natureza_saldo_final   CHAR(1)
    );
    
CREATE TYPE balancete_contabil_registro_11
    AS ( tipo_registro             INTEGER
       , conta_contabil            VARCHAR
       , cod_orgao                 VARCHAR
       , num_orgao                 INTEGER
       , num_unidade               INTEGER
       , cod_funcao                INTEGER
       , cod_sub_funcao            INTEGER
       , cod_programa              INTEGER
       , id_acao                   INTEGER
       , id_sub_acao               VARCHAR
       , natureza_despesa          VARCHAR
       , sub_elemento              VARCHAR
       , cod_fonte_recursos        INTEGER
       , saldo_inicial_cd          NUMERIC
       , natureza_saldo_inicial_cd CHAR(1)
       , total_debitos_cd          NUMERIC
       , total_creditos_cd         NUMERIC
       , saldo_final_cd            NUMERIC
       , natureza_saldo_final_cd   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_12
    AS ( tipo_registro             INTEGER
       , conta_contabil            VARCHAR
       , natureza_receita          VARCHAR
       , cod_fonte_recursos        INTEGER
       , saldo_inicial_cr          NUMERIC
       , natureza_saldo_inicial_cr CHAR(1)
       , total_debitos_cr          NUMERIC
       , total_creditos_cr         NUMERIC
       , saldo_final_cr            NUMERIC
       , natureza_saldo_final_cr   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_13
    AS ( tipo_registro             INTEGER
       , conta_contabil            VARCHAR
       , cod_programa              INTEGER
       , id_acao                   INTEGER
       , id_sub_acao               VARCHAR
       , saldo_inicial_pa          NUMERIC
       , natureza_saldo_inicial_pa CHAR(1)
       , total_debitos_pa          NUMERIC
       , total_creditos_pa         NUMERIC
       , saldo_final_pa            NUMERIC
       , natureza_saldo_final_pa   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_14
    AS ( tipo_registro              INTEGER
       , conta_contabil             VARCHAR
       , cod_orgao                  VARCHAR
       , num_orgao                  INTEGER
       , num_unidade                INTEGER
       , cod_funcao                 INTEGER
       , cod_sub_funcao             INTEGER
       , cod_programa               INTEGER
       , id_acao                    INTEGER
       , id_sub_acao                VARCHAR
       , natureza_despesa           VARCHAR
       , sub_elemento               VARCHAR
       , cod_fonte_recursos         INTEGER
       , numero_empenho             INTEGER
       , ano_inscricao              VARCHAR
       , saldo_inicial_rsp          NUMERIC
       , natureza_saldo_inicial_rsp CHAR(1)
       , total_debitos_rsp          NUMERIC
       , total_creditos_rsp         NUMERIC
       , saldo_final_rsp            NUMERIC
       , natureza_saldo_final_rsp   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_15
    AS ( tipo_registro             INTEGER
       , conta_contabil            VARCHAR
       , atributo_sf               VARCHAR
       , saldo_inicial_sf          NUMERIC
       , natureza_saldo_inicial_sf CHAR(1)
       , total_debitos_sf          NUMERIC
       , total_creditos_sf         NUMERIC
       , saldo_final_sf            NUMERIC
       , natureza_saldo_final_sf   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_16
    AS ( tipo_registro                   INTEGER
       , conta_contabil                  VARCHAR
       , atributo_sf                     VARCHAR
       , cod_fonte_recursos              INTEGER
       , saldo_inicial_fonte_sf          NUMERIC
       , natureza_saldo_inicial_fonte_sf CHAR(1)
       , total_debitos_fonte_sf          NUMERIC
       , total_creditos_fonte_sf         NUMERIC
       , saldo_final_fonte_sf            NUMERIC
       , natureza_saldo_final_fonte_sf   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_17
    AS ( tipo_registro              INTEGER
       , conta_contabil             VARCHAR
       , atributo_sf                VARCHAR
       , cod_ctb                    INTEGER
       , cod_fonte_recursos         INTEGER
       , saldo_inicial_ctb          NUMERIC
       , natureza_saldo_inicial_ctb CHAR(1)
       , total_debitos_ctb          NUMERIC
       , total_creditos_ctb         NUMERIC
       , saldo_final_ctb            NUMERIC
       , natureza_saldo_final_ctb   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_18
    AS ( tipo_registro             INTEGER
       , conta_contabil            VARCHAR
       , cod_fonte_recursos        INTEGER
       , saldo_inicial_fr          NUMERIC
       , natureza_saldo_inicial_fr CHAR(1)
       , total_debitos_fr          NUMERIC
       , total_creditos_fr         NUMERIC
       , saldo_final_fr            NUMERIC
       , natureza_saldo_final_fr   CHAR(1)
    );

CREATE TYPE balancete_contabil_registro_22
    AS ( tipo_registro                 INTEGER
       , conta_contabil                VARCHAR
       , atributo_sf                   VARCHAR
       , cod_ctb                       INTEGER
       , saldo_inicial_ctb_sf          NUMERIC
       , natureza_saldo_inicial_ctb_sf CHAR(1)
       , total_debitos_ctb_sf          NUMERIC
       , total_creditos_ctb_sf         NUMERIC
       , saldo_final_ctb_sf            NUMERIC
       , natureza_saldo_final_ctb_sf   CHAR(1)
    );

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2015'
        AND parametro  = 'cnpj'
        AND valor      = '18301002000186'
          ;
    IF FOUND THEN

        -- UPDATE NA TABELA contabilidade.plano_conta, populando o campo escrituracao_pcasp, com o valor S, para ser utilizado na geração do arquivo balancete
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.06.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.06.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.06.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.19.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.30.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.50.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.50.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.50.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.50.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.1.50.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.2.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.2.06.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.2.06.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.1.2.06.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.1.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.1.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.2.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.3.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.4.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.1.5.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.2.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.3.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.4.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.4.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.4.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.4.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.3.4.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.1.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.2.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.3.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.4.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.4.5.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.5.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.6.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.6.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.6.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.6.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.6.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.2.9.5.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.5.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.17.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.18.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.20.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.27.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.28.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.29.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.31.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.8.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.9.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.9.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.9.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.3.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.09.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.10.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.10.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.10.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.10.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.10.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.10.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.11.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.12.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.13.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.14.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.4.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.5.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.6.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.7.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.8.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.5.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.5.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.6.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.7.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.1.9.8.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.99.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.99.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.1.99.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.5.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.1.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.05.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.06.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.98.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.99.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.99.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.2.1.99.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.3.1.99.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.4.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.1.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.95.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.96.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.97.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.95.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.96.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.97.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.2.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.95.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.96.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.97.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.3.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.95.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.96.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.97.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.4.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.95.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.96.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.97.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.1.5.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.7.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.7.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.7.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.7.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.7.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.8.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.8.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.8.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.2.9.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.04.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.08.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.08.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.08.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.99.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.1.1.99.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.05.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.06.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.99.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.99.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.99.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.2.1.99.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.8.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.3.9.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.8.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.8.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.8.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.4.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.5.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.5.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.5.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '1.2.5.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.1.1.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.1.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.2.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.3.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.4.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.2.5.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.1.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.3.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.4.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.1.4.5.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.1.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.1.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.3.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.3.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.3.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.3.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.4.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.4.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.4.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.4.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.5.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.5.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.5.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.5.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.5.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.1.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.1.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.3.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.3.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.4.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.4.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.4.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.4.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.5.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.6.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.6.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.6.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.6.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.8.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.8.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.8.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.8.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.8.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.8.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.8.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.2.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.03.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.1.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.3.2.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.2.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.4.3.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.3.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.4.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.6.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.7.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.4.1.96.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.4.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.4.1.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.4.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.5.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.5.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.5.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.8.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.09.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.09.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.1.8.9.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.1.4.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.1.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.1.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.3.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.3.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.4.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.4.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.5.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.5.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.5.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.1.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.1.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.4.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.4.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.5.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.6.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.8.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.8.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.8.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.8.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.8.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.2.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.1.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.3.2.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.2.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.2.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.2.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.2.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.3.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.3.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.3.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.4.3.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.05.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.2.1.07.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.3.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.4.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.6.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.9.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.9.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.7.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.8.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.8.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.9.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.2.9.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.1.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.1.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.1.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.1.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.1.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.2.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.2.0.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.2.0.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.2.0.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.2.0.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.3.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.3.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.3.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.4.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.4.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.4.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.4.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.3.9.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.4.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.4.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.3.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.3.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.3.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.4.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.4.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.4.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.4.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.5.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.5.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.5.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.5.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.5.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.6.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.6.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.6.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.6.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.6.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.7.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.7.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.7.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.7.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.7.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.8.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.8.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.8.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.8.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.8.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.9.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.9.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.9.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.5.9.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.9.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.9.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.9.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.6.9.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.1.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.2.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.5.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.7.2.5.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '2.3.9.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.28.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.29.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.30.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.31.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.32.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.33.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.35.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.36.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.28.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.29.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.30.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.31.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.32.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.33.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.35.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.2.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.1.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.1.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.2.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.4.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.5.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.5.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.5.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.5.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.5.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.2.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.2.9.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.3.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.9.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.9.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.9.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.1.9.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.2.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.3.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.3.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.4.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.4.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.4.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.4.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.4.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.5.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.12.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.12.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.1.1.12.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.10.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.10.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.3.1.10.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.2.9.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.17.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.18.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.19.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.20.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.21.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.22.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.23.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.24.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.25.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.26.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.27.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.28.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.29.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.30.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.31.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.32.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.33.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.34.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.35.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.36.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.37.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.38.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.39.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.40.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.41.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.42.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.43.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.44.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.45.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.46.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.48.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.49.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.50.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.51.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.52.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.53.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.54.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.55.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.56.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.57.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.58.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.71.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.1.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.18.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.19.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.20.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.21.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.22.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.23.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.24.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.25.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.26.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.27.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.28.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.29.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.30.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.31.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.34.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.35.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.36.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.37.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.38.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.39.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.17.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.18.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.19.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.20.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.21.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.22.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.23.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.24.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.25.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.26.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.27.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.28.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.29.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.30.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.31.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.32.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.33.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.34.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.35.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.36.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.37.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.38.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.39.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.40.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.41.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.42.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.43.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.44.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.45.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.46.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.47.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.51.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.52.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.54.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.55.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.56.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.2.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.3.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.3.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.3.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.3.3.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.1.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.8.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.8.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.8.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.8.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.3.1.99.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.2.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.1.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.3.01.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.3.01.71.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.3.9.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.4.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.9.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.9.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.4.9.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.1.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.1.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.1.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.1.2.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.1.2.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.2.2.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.3.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.3.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.3.2.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.3.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.3.2.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.3.2.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.4.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.4.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.4.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.1.4.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.3.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.4.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.4.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.2.4.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.3.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.3.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.3.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.4.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.5.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.6.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.3.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.4.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.5.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.5.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.1.5.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.7.2.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.9.0.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.5.9.0.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.1.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.4.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.5.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.6.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.6.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.6.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.05.28.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.98.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.05.28.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.05.28.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.05.28.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.20.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.22.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.23.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.24.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.25.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.26.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.27.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.05.28.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.7.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.1.8.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.2.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.3.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.4.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.6.5.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.1.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.7.2.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.1.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.1.0.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.1.0.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.1.0.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.1.0.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.2.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.2.0.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.2.0.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.2.0.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.2.0.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.3.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.3.0.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.3.0.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.3.0.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.8.3.0.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.1.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.1.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.1.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.1.5.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.2.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.2.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.2.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.4.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.4.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.5.0.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.6.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.6.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.6.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.6.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.6.5.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.7.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.7.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.7.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.7.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.7.5.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.7.6.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.7.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.4.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.4.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.4.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.4.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.6.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '3.9.9.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.2.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.2.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.2.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.3.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.11.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.11.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.15.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.15.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.17.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.71.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.72.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.17.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.71.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.72.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.97.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.71.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.72.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.73.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.74.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.70.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.71.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.72.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.73.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.74.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.97.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.2.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.1.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.2.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.3.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.4.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.1.3.9.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.2.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.3.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.3.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.3.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.4.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.4.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.4.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.5.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.5.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.5.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.1.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.3.0.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.2.3.0.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.11.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.11.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.11.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.11.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.12.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.14.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.14.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.14.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.14.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.14.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.15.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.1.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.97.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.97.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.97.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.97.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.97.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.97.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.1.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.97.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.2.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.17.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.18.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.19.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.20.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.21.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.22.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.23.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.24.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.25.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.26.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.27.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.28.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.29.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.30.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.31.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.32.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.33.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.34.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.35.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.36.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.37.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.38.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.39.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.40.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.41.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.42.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.43.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.44.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.45.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.46.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.47.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.48.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.49.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.50.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.51.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.52.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.53.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.54.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.55.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.56.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.57.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.58.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.59.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.60.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.61.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.3.3.9.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.3.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.3.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.1.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.11.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.12.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.13.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.14.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.15.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.16.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.97.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.4.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.2.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.3.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.3.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.9.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.9.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.3.9.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.4.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.5.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.5.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.4.9.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.1.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.1.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.1.2.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.1.2.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.2.2.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.3.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.3.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.3.2.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.3.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.3.2.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.3.2.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.4.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.4.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.4.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.1.4.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.08.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.10.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.1.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.3.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.4.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.4.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.2.4.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.3.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.3.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.3.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.3.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.3.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.3.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.4.0.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.4.0.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.4.0.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.5.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.6.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.4.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.4.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.4.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.5.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.5.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.1.5.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.7.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.8.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.9.0.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.5.9.0.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.1.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.97.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.2.1.97.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.2.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.3.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.3.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.3.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.3.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.4.0.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.4.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.4.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.4.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.4.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.5.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.5.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.5.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.1.5.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.2.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.6.5.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.1.0.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.1.0.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.2.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.2.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.2.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.2.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.7.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.4.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.4.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.4.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.4.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.5.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.6.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.6.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.6.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.6.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.6.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.7.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.7.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.7.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.7.1.70.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.7.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '4.9.9.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.1.1.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.1.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.1.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.1.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.1.2.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.1.2.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.2.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.2.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.2.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.2.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.1.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.1.2.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.2.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.2.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.2.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.2.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.2.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.09.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.9.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.9.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.9.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.9.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.1.9.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.1.09.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.1.09.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.2.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.2.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.2.09.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.2.09.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.9.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.2.9.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.9.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.9.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.9.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.9.2.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.2.2.9.2.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.1.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.1.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.1.6.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.1.7.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.2.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.2.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.2.6.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '5.3.2.7.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.1.1.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.1.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.1.1.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.1.2.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.1.2.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.3.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.3.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.3.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.3.6.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.1.3.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.2.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.1.3.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.1.09.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.1.09.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.2.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.2.09.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.2.09.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.2.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.9.2.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.9.2.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.9.2.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.2.2.9.2.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.4.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.5.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.6.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.7.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.7.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.1.9.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.2.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.2.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.2.6.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.2.7.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.2.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '6.3.2.9.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.1.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.1.9.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.1.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.2.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.07.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.08.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.08.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.08.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.3.1.99.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.1.2.9.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.1.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.1.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.1.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.2.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.3.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.2.4.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.3.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.3.1.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.3.1.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.3.1.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.3.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.3.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.1.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.1.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.1.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.1.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.1.1.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.2.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.4.2.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.5.1.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.5.2.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.5.3.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.5.3.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.5.3.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.5.3.4.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.5.3.5.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.9.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.9.1.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.9.1.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.9.1.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.9.1.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.9.1.2.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '7.9.2.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.01.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.14.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.02.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.03.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.98.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.1.1.04.99.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.02.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.2.1.99.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.06.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.07.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.3.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.1.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.01.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.13.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.15.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.16.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.18.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.19.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.02.21.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.03.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.1.1.04.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.11.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.01.12.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.02.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.07.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.08.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.09.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.2.1.99.10.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.01.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.01.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.02.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.02.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.03.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.03.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.04.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.04.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.05.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.05.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.06.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.06.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.07.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.07.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.08.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.08.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.08.03.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.08.04.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.08.05.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.08.06.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.99.01.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.3.1.99.02.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.1.2.9.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.1.3.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.1.3.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.1.3.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.1.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.2.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.3.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.2.4.0.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.1.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.1.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.1.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.1.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.2.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.3.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.3.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.4.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.1.4.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.3.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.3.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.3.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.3.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.4.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.4.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.4.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.4.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.5.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.5.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.5.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.3.2.5.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.1.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.1.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.2.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.1.2.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.2.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.2.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.2.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.2.1.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.4.2.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.1.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.1.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.2.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.2.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.2.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.2.4.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.2.5.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.2.6.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.2.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.2.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.2.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.2.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.6.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.7.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.3.8.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.4.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.4.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.4.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.4.4.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.4.5.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.5.3.5.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.1.1.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.1.2.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.1.3.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.1.03.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.2.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.2.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.1.2.9.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.1.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.2.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.3.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.4.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.5.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.6.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.7.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.8.0.00.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.9.1.01.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.9.1.02.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.9.1.04.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.9.1.05.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.9.1.06.00.00.00.00';
        UPDATE contabilidade.plano_conta SET escrituracao_pcasp = 'S' WHERE exercicio = '2015' AND cod_estrutural LIKE '8.9.2.9.1.99.00.00.00.00';
        
        -- UPDATE NA TABELA contabilidade.plano_conta, populando o campo atributo_tcemg, com o valor referente a cada conta, para ser utilizado na geração do arquivo balancete.
        -- REGISTRO 11
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.1.02.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.2.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.2.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.2.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.2.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.2.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.2.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.3.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.9.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.9.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.9.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.9.02.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.2.1.9.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 11 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.2.1.3.99.%';
        --REGISTRO 12
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.2.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.2.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.2.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.1.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.2.1.2.9.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.3.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.3.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.3.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.3.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.3.6.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 12 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.2.1.3.9.%';
        --REGISTRO 13
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 13 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.1.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 13 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.1.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 13 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.1.1.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 13 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.1.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 13 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.1.1.2.%';
        -- REGISTRO 14
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.1.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.1.6.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.1.7.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.2.6.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '5.3.2.7.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.6.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.7.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.7.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.9.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.1.9.9.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.2.6.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.2.7.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.2.9.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 14 WHERE exercicio = '2015' AND cod_estrutural ILIKE '6.3.2.9.9.%';
        -- REGISTRO 15
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.1.70.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.2.70.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.3.70.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.4.70.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.1.5.70.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.2.5.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.05.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.06.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.3.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.4.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.4.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.4.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.4.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.3.4.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.07.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.07.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.1.07.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.07.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.07.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.2.07.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.07.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.07.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.3.07.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.07.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.07.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.4.07.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.07.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.07.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.4.5.07.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.3.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.4.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.5.5.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.6.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.6.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.6.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.6.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.6.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.01.70%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.01.70%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.2.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.01.70%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.3.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.01.70%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.4.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.01.70%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.2.9.5.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.1.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.10.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.11.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.12.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.13.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.14.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.2.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.4.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.4.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.10.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.11.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.12.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.13.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.14.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.15.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.16.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.17.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.18.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.20.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.27.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.28.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.29.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.31.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.8.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.9.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.9.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.9.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.9.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.9.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.1.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.3.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.4.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.5.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.6.1.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.7.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.8.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.5.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.4.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.5.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.6.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.7.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.9.8.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.01.70%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.99.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.99.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.99.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.99.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.1.99.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.2.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.3.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.3.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.4.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.4.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.4.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.5.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.5.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.5.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.5.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.1.5.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.11%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.12%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.13%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.14%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.11%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.12%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.13%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.14%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.15%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.05.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.98.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.99.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.99.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.99.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.99.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.99.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.99.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.99.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.99.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.98.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.4.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.9.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.95%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.96%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.97%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.1.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.95%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.96%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.97%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.2.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.95%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.96%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.97%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.3.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.95%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.96%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.97%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.4.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.95%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.96%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.97%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.1.5.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.2.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.2.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.2.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.2.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.3.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.3.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.3.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.7.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.7.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.7.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.7.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.7.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.8.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.8.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.8.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.4.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.2.9.5.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.11%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.12%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.13%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.14%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.15%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.16%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.17%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.18%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.19%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.20%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.21%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.04.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.04.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.05.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.05.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.05.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.05.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.05.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.05.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.07.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.07.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.08.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.08.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.08.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.10.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.99.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.99.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.99.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.1.1.99.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.11%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.12%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.13%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.14%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.15%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.16%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.17%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.18%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.19%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.20%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.21%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.22%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.11%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.12%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.13%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.14%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.15%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.16%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.17%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.18%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.05.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.06.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.06.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.99.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.99.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.99.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.99.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.99.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.2.1.99.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.02.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.8.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.02.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.3.9.1.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.2.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.2.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.2.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.8.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.8.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.8.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.4.9.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.5.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.5.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.5.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.5.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.03.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.1.1.03.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.03.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.1.03.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.03.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.2.03.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.03.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.3.03.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.03.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.4.03.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.03.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.2.5.03.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.3.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.1.98.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.3.98.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.4.98.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.1.4.5.98.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.1.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.1.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.3.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.3.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.3.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.3.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.3.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.4.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.4.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.4.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.4.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.5.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.5.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.5.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.5.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.5.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.1.5.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.1.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.3.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.3.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.3.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.4.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.4.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.3.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.4.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.4.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.4.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.5.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.6.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.6.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.6.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.6.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.8.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.8.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.8.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.8.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.8.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.8.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.8.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.2.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.02.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.03.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.1.1.04.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.3.2.1.02.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.10.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.11.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.12.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.13.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.10.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.11.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.12.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.13.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.09.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.10.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.11.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.12.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.13.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.1.3.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.2.4.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.08.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.4.3.5.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.3.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.3.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.3.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.3.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.4.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.4.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.4.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.6.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.7.9.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.4.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.4.1.96.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.4.1.97.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.4.1.98.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.4.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.5.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.5.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.5.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.11%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.12%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.13%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.14%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.15%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.16%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.17%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.04.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.8.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.05.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.05.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.09.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.09.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.10.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.11.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.12.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.13.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.14.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.16.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.1.8.9.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.1.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.1.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.1.1.02.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.1.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.2.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.2.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.2.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.2.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.3.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.4.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.1.4.5.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.1.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.1.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.3.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.3.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.3.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.3.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.4.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.4.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.4.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.4.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.5.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.5.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.5.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.5.02.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.1.5.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.1.01.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.3.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.4.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.4.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.5.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.6.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.8.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.8.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.8.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.8.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.8.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.2.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.02.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.1.1.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.2.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.2.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.3.2.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.1.3.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.2.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.2.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.2.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.2.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.2.4.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.3.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.3.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.3.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.3.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.3.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.4.3.5.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.02.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.02.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.02.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.02.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.03.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.03.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.03.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.03.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.04.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.05.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.06.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.07.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.07.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.07.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.07.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.2.1.07.98%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.3.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.3.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.3.1.02.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.3.1.02.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.4.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.4.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.4.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.4.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.6.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.9.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.9.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.7.9.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.4.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.10%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.11%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.12%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.13%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.14%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.15%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.16%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.17%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.01.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.03.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.03.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.04.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.04.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.04.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.04.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.04.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.04.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.8.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.8.9.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.9.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.2.9.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.1.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.1.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.1.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.1.2.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.1.2.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.1.2.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.2.0.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.2.0.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.2.0.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.2.0.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.2.0.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.1.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.1.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.1.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.2.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.2.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.2.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.3.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.3.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.3.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.3.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.4.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.4.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.4.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.4.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.4.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.1.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.2.99.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.3.9.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.4.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.4.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.1.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.1.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.1.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.2.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.2.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.2.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.3.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.3.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.3.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.3.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.4.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.4.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.4.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.4.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.4.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.5.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.5.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.5.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.5.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.5.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.6.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.6.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.6.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.6.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.6.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.7.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.7.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.7.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.7.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.7.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.8.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.8.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.8.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.8.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.8.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.9.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.9.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.9.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.9.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.5.9.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.1.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.1.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.1.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.9.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.9.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.9.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.9.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.6.9.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.4.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.5.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.5.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.1.5.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.1.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.1.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.2.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.2.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.2.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.2.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.2.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.2.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.3.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.3.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.3.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.4.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.4.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.4.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.4.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.4.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.4.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.5.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.5.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.5.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.5.04.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.5.05.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.7.2.5.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.1.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.1.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.1.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.2.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.2.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 15 WHERE exercicio = '2015' AND cod_estrutural ILIKE '2.3.9.2.5.%';
        -- REGISTRO 16
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 16 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 16 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.2.1.01.%';
        -- REGISTRO 17
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.06.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.06.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.06.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.06.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.19.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.30.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.50.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.50.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.50.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.50.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.1.50.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.2.06.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.2.06.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.2.06.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.1.2.06.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.2.1.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.1.2.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.08%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.09.09%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.10.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.10.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.10.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.10.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.10.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.10.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.11.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.12.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.13.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 17 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.14.01%';
        --REGISTRO 18
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 18 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.2.1.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 18 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.2.1.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 18 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.2.1.1.3.01.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 18 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.2.1.1.3.02.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 18 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.2.1.1.3.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 18 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.2.1.1.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 18 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.2.1.1.5.%';
        -- REGISTRO 19
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.1.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.1.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.2.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.2.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.2.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 19 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.2.6.%';
        -- REGISTRO 20
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.2.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.2.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.2.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.2.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.5.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.6.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.7.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.3.8.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.4.1.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.4.2.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.4.3.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.4.4.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 20 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.4.5.%';
        -- REGISTRO 21
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 21 WHERE exercicio = '2015' AND cod_estrutural ILIKE '8.5.3.5.%';
        -- REGISTRO 22
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.06.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.3.5.1.07.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.03.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.1.4.1.1.15.%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.01%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.03%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.04%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.05%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.06%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.07%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.2.1.06.99%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.01.02%';
        UPDATE contabilidade.plano_conta SET atributo_tcemg = 22 WHERE exercicio = '2015' AND cod_estrutural ILIKE '1.2.1.3.1.01.03%';

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23109
----------------

ALTER TABLE tcemg.registro_precos_orgao ADD COLUMN   cgm_aprovacao INTEGER;
UPDATE      tcemg.registro_precos_orgao SET          cgm_aprovacao = 0;
ALTER TABLE tcemg.registro_precos_orgao ALTER COLUMN cgm_aprovacao SET NOT NULL;
ALTER TABLE tcemg.registro_precos_orgao ADD CONSTRAINT fk_registro_precos_orgao_3 FOREIGN KEY (cgm_aprovacao)
                                                                                  REFERENCES sw_cgm_pessoa_fisica(numcgm);


----------------
-- Ticket #23112
----------------

INSERT
  INTO administracao.modulo
     ( cod_modulo
     , cod_responsavel
     , nom_modulo
     , nom_diretorio
     , ordem
     , cod_gestao
     , ativo
     )
VALUES
     ( 66
     , 0
     , 'SICONFI'
     , 'SICONFI/'
     , 42
     , 6
     , TRUE
     );

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     , ativo
     )
VALUES
     ( 494
     , 66
     , 'Relatórios'
     , 'instancias/relatorios/'
     , 10
     , TRUE
     );


----------------
-- Ticket #23114
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
     ( 3069
     , 494
     , 'FLRelatorioSiconfi.php'
     , 'anexo_dca_id'
     , 4
     , ''
     , 'Anexo DCA I-D'
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
     ( 3070
     , 494
     , 'FLRelatorioSiconfi.php'
     , 'anexo_dca_ie'
     , 5
     , ''
     , 'Anexo DCA I-E'
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
     ( 3071
     , 494
     , 'FLRelatorioSiconfi.php'
     , 'anexo_dca_if'
     , 6
     , ''
     , 'Anexo DCA I-F'
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
     ( 3072
     , 494
     , 'FLRelatorioSiconfi.php'
     , 'anexo_dca_ig'
     , 7
     , ''
     , 'Anexo DCA I-G'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 66
     , 4
     , 'Anexo DCA I-D'
     , 'LHSinconfiDCAAnexoID.php'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 66
     , 5
     , 'Anexo DCA I-E'
     , 'LHSinconfiDCAAnexoIE.php'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 66
     , 6
     , 'Anexo DCA I-F'
     , 'LHSinconfiDCAAnexoIF.php'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 66
     , 7
     , 'Anexo DCA I-G'
     , 'LHSinconfiDCAAnexoIG.php'
     );


----------------
-- Ticket #22998
----------------

CREATE TYPE metas_arrecada AS (
    cod_receita          INTEGER,
    estrutural           VARCHAR,
    exercicio            VARCHAR,
    mes_1                INTEGER,
    valor_arrecada_1     NUMERIC,
    mes_2                INTEGER,
    valor_arrecada_2     NUMERIC,
    mes_3                INTEGER,
    valor_arrecada_3     NUMERIC,
    mes_4                INTEGER,
    valor_arrecada_4     NUMERIC,
    mes_5                INTEGER,
    valor_arrecada_5     NUMERIC,
    mes_6                INTEGER,
    valor_arrecada_6     NUMERIC,
    mes_7                INTEGER,
    valor_arrecada_7     NUMERIC,
    mes_8                INTEGER,
    valor_arrecada_8     NUMERIC,
    mes_9                INTEGER,
    valor_arrecada_9     NUMERIC,
    mes_10               INTEGER,
    valor_arrecada_10    NUMERIC,
    mes_11               INTEGER,
    valor_arrecada_11    NUMERIC,
    mes_12               INTEGER,
    valor_arrecada_12    NUMERIC
);


----------------
-- Ticket #23117
----------------

CREATE SCHEMA siconfi;
GRANT ALL ON SCHEMA siconfi TO urbem;

CREATE TYPE relatorio_anexo_dca_ig AS (
    nivel                            INTEGER,
    cod_funcao                       INTEGER,
    cod_subfuncao                    INTEGER,
    descricao                        VARCHAR,
    vl_rp_nao_processados_pagos      NUMERIC,
    vl_rp_nao_processados_cancelados NUMERIC,
    vl_rp_processados_pagos          NUMERIC,
    vl_rp_processados_cancelados     NUMERIC
);


----------------
-- Ticket #23127
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
     ( 3073
     , 390
     , 'FLManterConfiguracaoOrdenador.php'
     , 'manter'
     , 7
     , ''
     , 'Configurar Ordenador'
     , TRUE
     );

CREATE TABLE tcmba.tipo_responsavel_ordenador(
    cod_tipo_responsavel_ordenador      INTEGER     NOT NULL,
    descricao                           VARCHAR(20) NOT NULL,
    CONSTRAINT pk_tipo_responsavel_ordenador        PRIMARY KEY (cod_tipo_responsavel_ordenador)
);
GRANT ALL ON tcmba.tipo_responsavel_ordenador TO urbem;

CREATE TABLE tcmba.configuracao_ordenador(
    exercicio                       CHAR(4)     NOT NULL,
    cod_entidade                    INTEGER     NOT NULL,
    cgm_ordenador                   INTEGER     NOT NULL,
    num_unidade                     INTEGER     NOT NULL,
    num_orgao                       INTEGER     NOT NULL,
    dt_inicio_vigencia              DATE        NOT NULL,
    dt_fim_vigencia                 DATE        NOT NULL,
    cod_tipo_responsavel_ordenador  INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_ordenador        PRIMARY KEY (exercicio, cod_entidade, cgm_ordenador, num_unidade, num_orgao),
    CONSTRAINT fk_configuracao_ordenador_1      FOREIGN KEY                     (exercicio, cod_entidade)
                                                REFERENCES orcamento.entidade   (exercicio, cod_entidade),
    CONSTRAINT fk_configuracao_ordenador_2      FOREIGN KEY                     (cgm_ordenador)
                                                REFERENCES sw_cgm_pessoa_fisica (numcgm),
    CONSTRAINT fk_configuracao_ordenador_3      FOREIGN KEY                     (exercicio, num_unidade, num_orgao)
                                                REFERENCES orcamento.unidade    (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_configuracao_ordenador_4      FOREIGN KEY                     (cod_tipo_responsavel_ordenador)
                                                REFERENCES tcmba.tipo_responsavel_ordenador    (cod_tipo_responsavel_ordenador)
);
GRANT ALL ON tcmba.configuracao_ordenador TO urbem;


----------------
-- Ticket #22368
----------------

DROP FUNCTION tcmgo.ativo_permanente_creditos(VARCHAR, VARCHAR, VARCHAR, VARCHAR);
DROP FUNCTION tcmgo.ativo_permanente_creditos(VARCHAR, VARCHAR, VARCHAR, VARCHAR, CHAR);


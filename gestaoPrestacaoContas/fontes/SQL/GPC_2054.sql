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
* Versao 2.05.4
*
* Fabio Bertoldi - 20160712
*
*/

----------------
-- Ticket #24069
----------------

CREATE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM information_schema.sequences
      WHERE sequence_name = 'seqnroop'
          ;
    IF NOT FOUND THEN
        CREATE SEQUENCE tcmgo.seqnroop;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23943
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'stn'
        AND tablename  = 'tipo_educacao_recurso'
          ;
    IF NOT FOUND THEN
        CREATE TABLE stn.tipo_educacao_recurso(
            cod_tipo_educacao   INTEGER     NOT NULL,
            descricao           VARCHAR(80) NOT NULL,
            CONSTRAINT pk_stn_tipo_educacao_recurso PRIMARY KEY (cod_tipo_educacao)
        );
        GRANT ALL ON stn.tipo_educacao_recurso TO urbem;

        INSERT INTO stn.tipo_educacao_recurso VALUES (1, 'Creche'    );
        INSERT INTO stn.tipo_educacao_recurso VALUES (2, 'Pré-Escola');

        CREATE TABLE stn.vinculo_recurso_acao(
            exercicio           CHAR(4) NOT NULL,
            cod_entidade        INTEGER NOT NULL,
            num_orgao           INTEGER NOT NULL,
            num_unidade         INTEGER NOT NULL,
            cod_recurso         INTEGER NOT NULL,
            cod_vinculo         INTEGER NOT NULL,
            cod_tipo            INTEGER NOT NULL,
            cod_acao            INTEGER NOT NULL,
            cod_tipo_educacao   INTEGER NOT NULL,
            CONSTRAINT pk_vinculo_recurso_acao    PRIMARY KEY (exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso, cod_vinculo, cod_tipo, cod_acao, cod_tipo_educacao),
            CONSTRAINT fk_vinculo_recurso_acao_1  FOREIGN KEY                    (exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso, cod_vinculo, cod_tipo)
                                                  REFERENCES stn.vinculo_recurso (exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso, cod_vinculo, cod_tipo),
            CONSTRAINT fk_vinculo_recurso_acao_2  FOREIGN KEY                          (cod_tipo_educacao)
                                                  REFERENCES stn.tipo_educacao_recurso (cod_tipo_educacao),
            CONSTRAINT fk_vinculo_recurso_acao_3  FOREIGN KEY         (cod_acao)
                                                  REFERENCES ppa.acao (cod_acao)
        );
        GRANT ALL ON stn.vinculo_recurso_acao TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #23927
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_type
      WHERE typname = 'anexo2_intra_orcamentaria'
          ;
    IF NOT FOUND THEN
        CREATE TYPE anexo2_intra_orcamentaria AS (
            cod_funcao                 INTEGER,
            cod_subfuncao              INTEGER,
            nom_funcao                 VARCHAR,
            nom_subfuncao              VARCHAR,
            vl_original                NUMERIC,
            vl_suplementacoes          NUMERIC,
            vl_empenhado_bimestre      NUMERIC,
            vl_empenhado_ate_bimestre  NUMERIC,
            vl_liquidado_bimestre      NUMERIC,
            vl_liquidado_ate_bimestre  NUMERIC
        );
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24052
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_class
       JOIN pg_attribute
         ON pg_attribute.attrelid = pg_class.oid
       JOIN pg_namespace
         ON pg_class.relnamespace = pg_namespace.oid
      WHERE pg_namespace.nspname = 'stn'
        AND pg_class.relname = 'receita_corrente_liquida'
        AND pg_attribute.attname = 'valor_iptu'
        AND pg_attribute.attnum > 0
             ;
    IF NOT FOUND THEN
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_iptu                              NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_iss                               NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_itbi                              NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_irrf                              NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_outras_receitas_tributarias       NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_cota_parte_fpm                    NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_cota_parte_icms                   NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_cota_parte_ipva                   NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_cota_parte_itr                    NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_transferencias_lc_871996          NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_transferencias_lc_611989          NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_transferencias_fundeb             NUMERIC(14,2);
        ALTER TABLE stn.receita_corrente_liquida ADD COLUMN valor_outras_transferencias_correntes   NUMERIC(14,2);
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24154
----------------

DROP FUNCTION contabilidade.fn_relatorio_balanco_orcamentario_despesa_novo(VARCHAR, VARCHAR, VARCHAR, VARCHAR);


----------------
-- Ticket #24165
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'tcemg'
        AND tablename  = 'contabilidade_lote_transferencia'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tcemg.contabilidade_lote_transferencia (
            exercicio_debito     CHAR(4) NOT NULL,
            cod_lote_debito      INTEGER NOT NULL,
            tipo_debito          CHAR(1) NOT NULL,
            cod_entidade_debito  INTEGER NOT NULL,
            exercicio_credito    CHAR(4) NOT NULL,
            cod_lote_credito     INTEGER NOT NULL,
            tipo_credito         CHAR(1) NOT NULL,
            cod_entidade_credito INTEGER NOT NULL,
            CONSTRAINT pk_tcemg_contabilidade_lote_transferencia   PRIMARY KEY                   (exercicio_debito, cod_lote_debito, tipo_debito, cod_entidade_debito, exercicio_credito, cod_lote_credito, tipo_credito, cod_entidade_credito),
            CONSTRAINT fk_tcemg_contabilidade_lote_transferencia_1 FOREIGN KEY                   (exercicio_debito, cod_lote_debito, tipo_debito, cod_entidade_debito)
                                                                   REFERENCES contabilidade.lote (exercicio       , cod_lote       , tipo       , cod_entidade       ),
            CONSTRAINT fk_tcemg_contabilidade_lote_transferencia_2 FOREIGN KEY                   (exercicio_credito, cod_lote_credito, tipo_credito, cod_entidade_credito)
                                                                   REFERENCES contabilidade.lote (exercicio        , cod_lote        , tipo        , cod_entidade        )
        );
        GRANT ALL ON tcemg.contabilidade_lote_transferencia TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24180
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM information_schema.sequences
      WHERE sequence_schema = 'stn'
        AND sequence_name   = 'rgf_anexo_6'
          ;
    IF NOT FOUND THEN
        CREATE SEQUENCE stn.rgf_anexo_6 START 1;
    END IF;
    GRANT ALL on stn.rgf_anexo_6 to urbem;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24157
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
SELECT 3123
     , 406
     , 'FMConfigurarRREO12.php'
     , 'vincular'
     , 25
     , ''
     , 'Vincular Receita Saúde Anexo 12'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3123
           )
     ;

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'stn'
        AND tablename  = 'vinculo_saude_rreo12'
          ;
    IF NOT FOUND THEN
        CREATE TABLE stn.vinculo_saude_rreo12(
            cod_receita     INTEGER     NOT NULL,
            exercicio       VARCHAR(4)  NOT NULL,
            CONSTRAINT pk_vinculo_saude_rreo12      PRIMARY KEY (cod_receita, exercicio),
            CONSTRAINT fk_vinculo_saude_rreo12_1    FOREIGN KEY (cod_receita, exercicio)
                                                    REFERENCES orcamento.receita (cod_receita, exercicio)
        );
        GRANT ALL ON stn.vinculo_saude_rreo12 TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24142
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
SELECT 3122
     , 406
     , 'FMConfigurarRGF1.php'
     , 'vincular'
     , 24
     , ''
     , 'Vincular Contas RGF 1'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3122
           )
     ;

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'stn'
        AND tablename  = 'contas_rgf_1'
          ;
    IF NOT FOUND THEN
        CREATE TABLE stn.contas_rgf_1(
            cod_conta       INTEGER         NOT NULL,
            descricao       VARCHAR(100)    NOT NULL,
            CONSTRAINT pk_contas_rgf_1      PRIMARY KEY (cod_conta)
        );
        GRANT ALL ON stn.contas_rgf_1 TO urbem;

        INSERT INTO stn.contas_rgf_1 VALUES (1, 'Conta para Despesas de Exercícios anteriores!');
    END IF;

    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'stn'
        AND tablename  = 'vinculo_contas_rgf_1'
          ;
    IF NOT FOUND THEN
        CREATE TABLE stn.vinculo_contas_rgf_1(
            cod_conta       INTEGER         NOT NULL,
            cod_plano       INTEGER         NOT NULL,
            exercicio       CHAR(4)         NOT NULL,
            timestamp       TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
            CONSTRAINT pk_vinculo_contas_rgf_1      PRIMARY KEY                 (cod_conta, cod_plano, exercicio, timestamp),
            CONSTRAINT fk_vinculo_contas_rgf_1_1    FOREIGN KEY                 (cod_conta)
                                                    REFERENCES stn.contas_rgf_1 (cod_conta),
            CONSTRAINT fk_vinculo_contas_rgf_1_12   FOREIGN KEY                              (cod_plano, exercicio)
                                                    REFERENCES contabilidade.plano_analitica (cod_plano, exercicio)
        );
        GRANT ALL ON stn.vinculo_contas_rgf_1 TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


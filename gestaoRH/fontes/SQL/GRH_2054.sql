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
-- Ticket #23921
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    boExecuta   BOOLEAN;
BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'pessoal'
        AND tablename  = 'tipo_documento_digital'
          ;
    IF NOT FOUND THEN
        stSQL := 'SELECT atualizarbanco(''
        CREATE TABLE pessoal.tipo_documento_digital(
            cod_tipo    INTEGER         NOT NULL,
            descricao   VARCHAR(100)    NOT NULL,
            CONSTRAINT pk_tipo_documento_digital PRIMARY KEY (cod_tipo)
        );
        '')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''GRANT ALL ON pessoal.tipo_documento_digital TO urbem;'')';
        boExecuta := selectIntoBoolean(stSQL);

        stSQL := 'SELECT atualizarbanco(''INSERT INTO pessoal.tipo_documento_digital VALUES (1, ''''CPF''''                      );'')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''INSERT INTO pessoal.tipo_documento_digital VALUES (2, ''''RG''''                       );'')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''INSERT INTO pessoal.tipo_documento_digital VALUES (3, ''''CNH''''                      );'')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''INSERT INTO pessoal.tipo_documento_digital VALUES (4, ''''Título de Eleitor''''        );'')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''INSERT INTO pessoal.tipo_documento_digital VALUES (5, ''''Certificado de Reservista'''');'')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''INSERT INTO pessoal.tipo_documento_digital VALUES (6, ''''CTPS''''                     );'')';
        boExecuta := selectIntoBoolean(stSQL);


        stSQL := 'SELECT atualizarbanco(''CREATE TABLE pessoal.servidor_documento_digital(
            cod_servidor    INTEGER         NOT NULL,
            cod_tipo        INTEGER         NOT NULL,
            nome_arquivo    VARCHAR(100)    NOT NULL,
            arquivo_digital VARCHAR(250)    NOT NULL,
            CONSTRAINT pk_servidor_documento_digital    PRIMARY KEY (cod_servidor, cod_tipo),
            CONSTRAINT fk_servidor_documento_digital_1  FOREIGN KEY (cod_servidor)
                                                        REFERENCES pessoal.servidor (cod_servidor),
            CONSTRAINT fk_servidor_documento_digital_2  FOREIGN KEY (cod_tipo)
                                                        REFERENCES pessoal.tipo_documento_digital (cod_tipo)
        );
        '')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''GRANT ALL ON pessoal.servidor_documento_digital TO urbem;'')';
        boExecuta := selectIntoBoolean(stSQL);
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23923
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    boExecuta   BOOLEAN;
BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'pessoal'
        AND tablename  = 'assentamento_gerado_arquivo_digital'
          ;
    IF NOT FOUND THEN
        stSQL:= 'SELECT atualizarbanco(''
        CREATE TABLE pessoal.assentamento_gerado_arquivo_digital(
            cod_assentamento_gerado INTEGER         NOT NULL,
            nome_arquivo            VARCHAR(100)    NOT NULL,
            arquivo_digital         VARCHAR(250)    NOT NULL,
            CONSTRAINT pk_assentamento_gerado_arquivo_digital   PRIMARY KEY (cod_assentamento_gerado, nome_arquivo),
            CONSTRAINT fk_assentamento_gerado_arquivo_digital_1 FOREIGN KEY (cod_assentamento_gerado)
                                                                REFERENCES pessoal.assentamento_gerado_contrato_servidor (cod_assentamento_gerado)
        );
        '')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''GRANT ALL ON pessoal.assentamento_gerado_arquivo_digital TO urbem;'')';
        boExecuta := selectIntoBoolean(stSQL);
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24040
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    boExecuta   BOOLEAN;
BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'folhapagamento'
        AND tablename  = 'configuracao_empenho_cargo'
          ;
    IF NOT FOUND THEN
        stSQL := 'SELECT atualizarbanco(''
        CREATE TABLE folhapagamento.configuracao_empenho_cargo(
            cod_configuracao    INTEGER         NOT NULL,
            exercicio           CHAR(4)         NOT NULL,
            sequencia           INTEGER         NOT NULL,
            timestamp           TIMESTAMP       NOT NULL,
            cod_sub_divisao     INTEGER         NOT NULL,
            cod_cargo           INTEGER         NOT NULL,
            CONSTRAINT pk_configuracao_empenho_cargo    PRIMARY KEY                                     (cod_configuracao, exercicio, sequencia, timestamp, cod_sub_divisao, cod_cargo),
            CONSTRAINT fk_configuracao_empenho_cargo_1  FOREIGN KEY                                                (cod_configuracao, exercicio, sequencia, timestamp, cod_sub_divisao)
                                                        REFERENCES folhapagamento.configuracao_empenho_subdivisao  (cod_configuracao, exercicio, sequencia, timestamp, cod_sub_divisao),
            CONSTRAINT fk_configuracao_empenho_cargo_2  FOREIGN KEY                                     (cod_cargo)
                                                        REFERENCES pessoal.cargo                        (cod_cargo)
        );
        '')';
        boExecuta := selectIntoBoolean(stSQL);
        stSQL := 'SELECT atualizarbanco(''GRANT ALL ON folhapagamento.configuracao_empenho_cargo TO urbem;'')';
        boExecuta := selectIntoBoolean(stSQL);
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24074
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    boExecuta   BOOLEAN;
BEGIN
    PERFORM 1
       FROM pg_class
       JOIN pg_attribute
         ON pg_attribute.attrelid = pg_class.oid
       JOIN pg_namespace
         ON pg_class.relnamespace = pg_namespace.oid
      WHERE pg_namespace.nspname = 'pessoal'
        AND pg_class.relname = 'assentamento_assentamento'
        AND pg_attribute.attname = 'cod_regime_previdencia'
        AND pg_attribute.attnum > 0
          ;
    IF FOUND THEN
        stSQL := 'SELECT atualizarbanco(''ALTER TABLE pessoal.assentamento_assentamento DROP COLUMN cod_regime_previdencia;'')';
        boExecuta := selectIntoBoolean(stSQL);
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24063
----------------

ALTER TYPE colulasConferenciaSefip DROP ATTRIBUTE IF EXISTS valor_patronal;
ALTER TYPE colulasConferenciaSefip ADD  ATTRIBUTE           valor_patronal NUMERIC;


----------------
-- Ticket #24124
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodENtPref    INTEGER;
    stCriaTRG       VARCHAR;
    stSQL           VARCHAR;
    reRecord        RECORD;
BEGIN
    SELECT valor
      INTO inCodENtPref
      FROM administracao.configuracao
     WHERE exercicio  = '2016'
       AND cod_modulo = 8
       AND parametro  = 'cod_entidade_prefeitura'
         ;

    stSQL := '
                 SELECT '''' as entidade
               UNION
                 SELECT ''_''||cod_entidade AS entidade
                   FROM administracao.entidade_rh
                  WHERE exercicio     = ''2016''
                    AND cod_entidade != '|| inCodENtPref ||'
               GROUP BY cod_entidade
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_pensionista ON pessoal'|| reRecord.entidade ||'.contrato_pensionista';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_pensionista'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.contrato_pensionista';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_pensionista'|| reRecord.entidade ||' BEFORE INSERT OR DELETE ON pessoal'|| reRecord.entidade ||'.contrato_pensionista FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_pensionista();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor ON pessoal'|| reRecord.entidade ||'.contrato_servidor;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.contrato_servidor;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_servidor'|| reRecord.entidade ||' BEFORE INSERT OR DELETE ON pessoal'|| reRecord.entidade ||'.contrato_servidor FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria ON pessoal'|| reRecord.entidade ||'.aposentadoria;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.aposentadoria;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_aposentadoria'|| reRecord.entidade ||' BEFORE INSERT OR DELETE ON pessoal'|| reRecord.entidade ||'.aposentadoria FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria_excluida ON pessoal'|| reRecord.entidade ||'.aposentadoria_excluida;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_aposentadoria_excluida'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.aposentadoria_excluida;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_aposentadoria_excluida'|| reRecord.entidade ||' BEFORE INSERT OR DELETE ON pessoal'|| reRecord.entidade ||'.aposentadoria_excluida FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria_excluida();';
            EXECUTE stCriaTRG;

            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor_caso_causa ON pessoal'|| reRecord.entidade ||'.contrato_servidor_caso_causa;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'DROP TRIGGER IF EXISTS trg_situacao_contrato_servidor_caso_causa'|| reRecord.entidade ||' ON pessoal'|| reRecord.entidade ||'.contrato_servidor_caso_causa;';
            EXECUTE stCriaTRG;
            stCriaTRG := 'CREATE TRIGGER trg_situacao_contrato_servidor_caso_causa'|| reRecord.entidade ||' BEFORE INSERT OR DELETE ON pessoal'|| reRecord.entidade ||'.contrato_servidor_caso_causa FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor_caso_causa();';
            EXECUTE stCriaTRG;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


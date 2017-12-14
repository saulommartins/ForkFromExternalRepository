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
* Versao 2.02.0
*
* Fabio Bertoldi - 20121001
*
*/

CREATE OR REPLACE FUNCTION atualizarBanco(VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    stSqlParametro              ALIAS FOR $1;
    inExercicio                 INTEGER;
    inCodEntidadePrefeitura     INTEGER;    
    stSql                       VARCHAR;
    stInsert                    VARCHAR;
    stBanco                     VARCHAR;
    stEntidade                  VARCHAR;
    stNomeSchema                VARCHAR;
    stNomeTriger                VARCHAR;
    stArray                     VARCHAR[];
    boEsquema                   BOOLEAN:=FALSE;
    boTrigger                   BOOLEAN:=FALSE;
    boGranteEsquema             BOOLEAN:=FALSE;
    boRetorno                   BOOLEAN;
    reRegistro                  RECORD;
    reSchema                    RECORD;
BEGIN
    EXECUTE stSqlParametro;

    inExercicio := selectIntoInteger('SELECT valor FROM administracao.configuracao WHERE parametro = ''ano_exercicio'' ORDER BY exercicio desc LIMIT 1');
    inCodEntidadePrefeitura := selectIntoInteger('SELECT valor::integer as valor
                                                    FROM administracao.configuracao
                                                   WHERE parametro = ''cod_entidade_prefeitura''
                                                     AND exercicio = '|| quote_literal(inExercicio) ||' ');


    IF strpos(trim(upper(stSqlParametro)),upper('CREATE SCHEMA')) > 0 THEN
        boEsquema    := TRUE;
        stNomeSchema := trim(translate(stSqlParametro,'CREATE SCHEMA ;',''));
        stInsert     := 'INSERT INTO administracao.schema_rh (schema_cod,schema_nome) VALUES ((SELECT max(schema_cod) FROM administracao.schema_rh)+1,'|| quote_literal(stNomeSchema) ||')';      
        EXECUTE stInsert;
        
        stSql := 'SELECT TRUE as retorno
                    FROM administracao.entidade_rh
                   WHERE cod_entidade = '|| inCodEntidadePrefeitura ||'
                   LIMIT 1';
        boRetorno := selectIntoBoolean(stSql);
        IF boRetorno IS TRUE THEN
            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) 
                         VALUES
                         ('|| quote_literal(inExercicio) ||','|| inCodEntidadePrefeitura ||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
            EXECUTE stInsert;
        END IF;
    END IF;
    IF strpos(trim(upper(stSqlParametro)),upper('CREATE TRIGGER')) > 0 THEN
        boTrigger    := TRUE;
        stArray      := string_to_array( stSqlParametro, ' ');
        stNomeTriger := stArray[3];
    END IF;
    IF strpos(trim(upper(stSqlParametro)),upper('GRANT ALL ON SCHEMA')) > 0 THEN
        boGranteEsquema := TRUE;
        stArray         := string_to_array( stSqlParametro, ' ');
        stNomeSchema    := stArray[5];
    END IF;

    stSql := '  SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = '|| quote_literal(inExercicio) ||'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = '|| quote_literal(inExercicio) ||'
                                        GROUP BY cod_entidade)
                   AND cod_entidade != ('|| inCodEntidadePrefeitura ||')';
    FOR reRegistro IN EXECUTE stSql LOOP
        stBanco := stSqlParametro;        
        IF boEsquema THEN
            stBanco := trim(replace(stSqlParametro,';','')) ||'_'|| reRegistro.cod_entidade ||';';

            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) 
                         VALUES
                         ('|| quote_literal(inExercicio) ||','|| reRegistro.cod_entidade ||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
            EXECUTE stInsert;
        ELSIF boTrigger THEN
            stBanco := trim(replace(stSqlParametro,stNomeTriger,stNomeTriger ||'_'|| reRegistro.cod_entidade));
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '|| reSchema.schema_nome ||'.',' '|| reSchema.schema_nome ||'_'|| reRegistro.cod_entidade ||'.');
            END LOOP;
        ELSIF boGranteEsquema THEN
            stBanco := replace(stBanco, stNomeSchema,' '|| stNomeSchema ||'_'|| reRegistro.cod_entidade);
        ELSE 
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '|| reSchema.schema_nome ||'.',' '|| reSchema.schema_nome ||'_'|| reRegistro.cod_entidade ||'.');
            END LOOP;
        END IF;
        EXECUTE stBanco;
    END LOOP;
    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';


--------------------------------------------
-- CRIANDO TABELA concurso.atributo_concurso
--------------------------------------------

SELECT atualizarbanco('
CREATE TABLE concurso.atributo_concurso(
    cod_cadastro    INTEGER     NOT NULL,
    cod_atributo    INTEGER     NOT NULL,
    ativo           BOOLEAN     NOT NULL,
    CONSTRAINT pk_atributo_condurso     PRIMARY KEY (cod_cadastro, cod_atributo)
);
');
SELECT atualizarbanco('GRANT ALL ON concurso.atributo_concurso TO urbem;');


----------------
-- Ticket #20140
----------------

UPDATE administracao.acao           SET ativo = FALSE WHERE cod_acao = 1739;
UPDATE administracao.acao           SET ativo = FALSE WHERE cod_acao = 1737;
UPDATE administracao.funcionalidade SET ativo = FALSE WHERE cod_funcionalidade = 360;


----------------
-- Ticket #16389
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 4
     , 27
     , 29
     , 'Relatório Contra Cheque'
     , 'contraCheque.rptdesign'
     );


----------------
-- Ticket #20507
----------------

UPDATE administracao.acao SET nom_acao = 'Exportação Banco Caixa' WHERE cod_acao = 1969 and cod_funcionalidade = 354;


----------------
-- Ticket #19515
----------------

DROP   TYPE colunasCustomizavelEventos CASCADE;
CREATE TYPE colunasCustomizavelEventos AS (
cod_contrato              INTEGER,
registro                  INTEGER,
nom_cgm                   VARCHAR,
cpf                       VARCHAR,
desc_orgao                VARCHAR,
desc_local                VARCHAR,
desc_funcao               VARCHAR,
desc_cargo                VARCHAR,
desc_especialidade_cargo  VARCHAR,
desc_especialidade_funcao VARCHAR,
desc_padrao               VARCHAR,
valor1                    NUMERIC,
quantidade1               NUMERIC,
quantidade1Parcela        NUMERIC,
valor2                    NUMERIC,
quantidade2               NUMERIC,
quantidade2Parcela        NUMERIC,
valor3                    NUMERIC,
quantidade3               NUMERIC,
quantidade3Parcela        NUMERIC,
valor4                    NUMERIC,
quantidade4               NUMERIC,
quantidade4Parcela        NUMERIC,
valor5                    NUMERIC,
quantidade5               NUMERIC,
quantidade5Parcela        NUMERIC,
valor6                    NUMERIC,
quantidade6               NUMERIC,
quantidade6Parcela        NUMERIC
);


----------------
-- Ticket #19689
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo
     )
VALUES
     ( 4
     , 27
     , 30
     , 'Registros de Evento'
     , 'consultarRegistroEvento.rptdesign'
     );


----------------
-- Ticket #15957
----------------

SELECT atualizarbanco('
CREATE TABLE pessoal.contrato_pensionista_caso_causa (
    cod_contrato        INTEGER     NOT NULL,
    dt_rescisao         DATE        NOT NULL,
    timestamp           TIMESTAMP   NOT NULL DEFAULT (''now''::text)::timestamp(3) with time zone,
    cod_caso_causa      INTEGER     NOT NULL,
    inc_folha_salario   BOOLEAN             ,
    inc_folha_decimo    BOOLEAN             ,
    CONSTRAINT pk_contrato_pensionista_caso_causa   PRIMARY KEY                             (cod_contrato),
    CONSTRAINT fk_contrato_pensionista_caso_causa_1 FOREIGN KEY                             (cod_caso_causa)
                                                    REFERENCES pessoal.caso_causa           (cod_caso_causa),
    CONSTRAINT fk_contrato_pensionista_caso_causa_2 FOREIGN KEY                             (cod_contrato)
                                                    REFERENCES pessoal.contrato_pensionista (cod_contrato)
);
');
SELECT atualizarbanco('
GRANT ALL ON pessoal.contrato_pensionista_caso_causa TO urbem;
');

SELECT atualizarbanco('
CREATE TABLE pessoal.causa_obito_pensionista(
    cod_contrato        INTEGER         NOT NULL,
    num_certidao_obito  VARCHAR(10)     NOT NULL,
    causa_mortis        VARCHAR(200)    NOT NULL,
    CONSTRAINT pk_causa_obito_pensionista       PRIMARY KEY (cod_contrato),
    CONSTRAINT fk_causa_obito_pensionista_1     FOREIGN KEY (cod_contrato)
                                                REFERENCES pessoal.contrato_pensionista_caso_causa (cod_contrato)
);
');
SELECT atualizarbanco('
GRANT ALL ON pessoal.causa_obito_pensionista TO urbem;
');

SELECT atualizarbanco('
CREATE TABLE pessoal.contrato_pensionista_caso_causa_norma(
    cod_contrato        INTEGER     NOT NULL,
    cod_norma           INTEGER     NOT NULL,
    CONSTRAINT pk_contrato_pensionista_caso_causa_norma     PRIMARY KEY                                         (cod_contrato),
    CONSTRAINT fk_contrato_pensionista_caso_causa_norma_1   FOREIGN KEY                                         (cod_contrato)
                                                            REFERENCES pessoal.contrato_pensionista_caso_causa  (cod_contrato),
    CONSTRAINT fk_contrato_pensionista_caso_causa_norma_2   FOREIGN KEY                                         (cod_norma)
                                                            REFERENCES normas.norma                             (cod_norma)
);
');
SELECT atualizarbanco('
GRANT ALL ON pessoal.contrato_pensionista_caso_causa_norma TO urbem;
');


----------------
-- Ticket #20422
----------------

DROP   TYPE colunasEventosCalculados CASCADE;
CREATE TYPE colunasEventosCalculados AS (
    cod_contrato                INTEGER,  
    cod_evento                  INTEGER,  
    codigo                      VARCHAR,
    descricao                   VARCHAR,
    natureza                    CHAR,
    tipo                        CHAR,
    fixado                      CHAR,
    limite_calculo              BOOLEAN,
    apresenta_parcela           BOOLEAN,
    evento_sistema              BOOLEAN,
    sigla                       VARCHAR,
    valor                       NUMERIC,        
    quantidade                  NUMERIC,
    desdobramento               VARCHAR,
    desdobramento_texto         VARCHAR,
    sequencia                   INTEGER,
    desc_sequencia              VARCHAR,
    quantidade_total_parcela    INTEGER
);


----------------
-- Ticket #20563
----------------

DROP Function recuperaContribuicaoPrevidenciaria( VARCHAR
                                                , INTEGER
                                                , INTEGER
                                                , INTEGER
                                                , VARCHAR
                                                , VARCHAR
                                                , VARCHAR
                                                );


----------------
-- Ticket #20437
----------------

SELECT atualizarbanco('ALTER TABLE pessoal.contrato_pensionista_previdencia ADD COLUMN bo_excluido BOOLEAN NOT NULL DEFAULT FALSE;');


----------------
-- Ticket #20274
----------------

SELECT atualizarbanco('
ALTER TABLE pessoal.servidor_cid ADD COLUMN data_laudo DATE;
');


----------------
-- Ticket #16515
----------------

SELECT atualizarbanco('CREATE TABLE pessoal.contrato_servidor_situacao(
    cod_contrato                INTEGER         NOT NULL,
    situacao                    CHAR(1)         NOT NULL,
    timestamp                   TIMESTAMP       NOT NULL DEFAULT (''now''::text)::timestamp(3) with time zone,
    cod_periodo_movimentacao    INTEGER         NOT NULL,
    situacao_literal            VARCHAR(25)     NOT NULL,
    deleted                     BOOLEAN         NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_contrato_servidor_situacao    PRIMARY KEY                                    (cod_contrato, timestamp, situacao),
    CONSTRAINT fk_contrato_servidor_situacao_1  FOREIGN KEY                                    (cod_contrato)
                                                REFERENCES pessoal.contrato                    (cod_contrato),
    CONSTRAINT fk_contrato_servidor_situacao_2  FOREIGN KEY                                    (cod_periodo_movimentacao)
                                                REFERENCES folhapagamento.periodo_movimentacao (cod_periodo_movimentacao)
);
');
SELECT atualizarbanco('GRANT ALL ON pessoal.contrato_servidor_situacao TO urbem;');

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodPrefeitura     VARCHAR;
    stSQLEntidade       VARCHAR;
    reRecordEntidade    RECORD;
    stEntidade          VARCHAR;
    stSQL               VARCHAR;
    reRecord            RECORD;
    stInsert            VARCHAR;
    stPrefeitura        VARCHAR;
    inCodPeriodo        INTEGER;
BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF FOUND THEN
        stPrefeitura := 'Manaquiri';
        UPDATE pessoal.contrato_servidor_nomeacao_posse
           SET dt_nomeacao = timestamp::date
             , dt_posse    = timestamp::date
             , dt_admissao = timestamp::date
         WHERE cod_contrato IN (
                                 SELECT cod_contrato
                                   FROM pessoal.contrato_servidor_nomeacao_posse
                                  WHERE dt_admissao = '1111-11-11'
                                     OR dt_admissao = '1010-12-12'
                               )
             ;
    END IF;

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '94068418000184'
          ;
    IF FOUND THEN
        UPDATE pessoal.contrato_servidor_nomeacao_posse
           SET dt_nomeacao = '2012-02-02'
             , dt_posse    = '2012-02-02'
             , dt_admissao = '2012-02-02'
         WHERE cod_contrato = 1355
             ;
    END IF;

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2013'
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        stPrefeitura := 'Mata';
        UPDATE pessoal.contrato_servidor_nomeacao_posse
           SET dt_nomeacao = '2003-03-03'
             , dt_posse    = '2003-03-03'
             , dt_admissao = '2003-03-03'
         WHERE cod_contrato = 7383
             ;
        UPDATE pessoal.contrato_servidor_nomeacao_posse
           SET dt_nomeacao = '2007-04-23'
             , dt_posse    = '2007-04-23'
             , dt_admissao = '2007-04-23'
         WHERE cod_contrato = 7860
            OR cod_contrato = 7864
             ;
    END IF;

    SELECT valor
      INTO inCodPrefeitura
      FROM administracao.configuracao
     WHERE cod_modulo = 8
       AND exercicio  = '2013'
       AND parametro  = 'cod_entidade_prefeitura'
         ;

    stSQLEntidade := '
                       SELECT DISTINCT(cod_entidade)::CHAR
                            , ''entidade_'' || cod_entidade AS entidade
                         FROM administracao.entidade_rh
                        WHERE exercicio = ''2013''
                          AND cod_entidade != '|| inCodPrefeitura ||'::INTEGER
                        UNION
                       SELECT ''''::CHAR AS cod_entidade
                            , ''entidade'' AS entidade
                            ;
                     ';
    FOR reRecordEntidade IN EXECUTE stSQLEntidade LOOP
        IF reRecordEntidade.cod_entidade != '' THEN
            stEntidade := '_' || reRecordEntidade.cod_entidade;
        ELSE
            stEntidade := '';
        END IF;
        stSQL := '
                   Select contrato_servidor_nomeacao_posse.cod_contrato
                        , dt_admissao::timestamp AS timestamp
                        , (
                            SELECT cod_periodo_movimentacao
                              FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                             WHERE contrato_servidor_nomeacao_posse.dt_admissao::DATE BETWEEN dt_inicial AND dt_final
                          )         AS cod_periodo_movimentacao
                        , ''A''     AS situacao
                        , ''Ativo'' AS situacao_literal
                     FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                     JOIN (
                              SELECT cod_contrato
                                   , min(timestamp) AS timestamp
                                FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                            GROUP BY cod_contrato
                          ) AS min_timestamp
                       ON contrato_servidor_nomeacao_posse.cod_contrato = min_timestamp.cod_contrato
                      AND contrato_servidor_nomeacao_posse.timestamp    = min_timestamp.timestamp
                    UNION
                   SELECT cod_contrato
                        , dt_concessao::timestamp AS timestamp
                        , (
                            SELECT cod_periodo_movimentacao
                              FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                             WHERE aposentadoria.dt_concessao::DATE BETWEEN dt_inicial AND dt_final
                          )              AS cod_periodo_movimentacao
                        , ''P''          AS situacao
                        , ''Aposentado'' AS situacao_literal
                     FROM pessoal'|| stEntidade ||'.aposentadoria 
                    WHERE NOT EXISTS (
                                       SELECT 1
                                         FROM pessoal'|| stEntidade ||'.aposentadoria_excluida
                                        WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                     )
                    UNION
                   SELECT cod_contrato
                        , dt_rescisao::timestamp as timestamp
                        , (
                            SELECT cod_periodo_movimentacao
                              FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                             WHERE contrato_servidor_caso_causa.dt_rescisao::DATE BETWEEN dt_inicial AND dt_final
                          )              AS cod_periodo_movimentacao
                        , ''R''          AS situacao
                        , ''Rescindido'' AS situacao_literal
                     FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                    UNION
                   SELECT cod_contrato
                        , dt_inicio_beneficio::TIMESTAMP AS timestamp
                        , (
                            SELECT cod_periodo_movimentacao
                              FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                             WHERE contrato_pensionista.dt_inicio_beneficio::DATE BETWEEN dt_inicial AND dt_final
                          )               AS cod_periodo_movimentacao
                        , ''E''           AS situacao
                        , ''Pensionista'' AS situacao_literal
                     FROM pessoal'|| stEntidade ||'.contrato_pensionista
                        ;
                 ';
        FOR reRecord IN EXECUTE stSQL LOOP
            IF    stPrefeitura = 'Mata'      AND reRecord.cod_periodo_movimentacao IS NULL THEN
                inCodPeriodo = 506;
            ELSIF stPrefeitura = 'Manaquiri' AND reRecord.cod_periodo_movimentacao IS NULL THEN
                inCodPeriodo = 1;
            ELSE
                inCodPeriodo = reRecord.cod_periodo_movimentacao;
            END IF;
            stInsert := '
                          INSERT
                            INTO pessoal'|| stEntidade ||'.contrato_servidor_situacao
                               ( cod_contrato
                               , situacao
                               , timestamp
                               , cod_periodo_movimentacao
                               , situacao_literal
                               )
                          VALUES
                               ( '|| reRecord.cod_contrato                    ||'
                               , '|| quote_literal(reRecord.situacao)         ||'
                               , '|| quote_literal(reRecord.timestamp)        ||'
                               , '|| inCodPeriodo                             ||'
                               , '|| quote_literal(reRecord.situacao_literal) ||'
                               );
                        ';
            EXECUTE stInsert;
        END LOOP;

    END LOOP;

END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();


CREATE OR REPLACE FUNCTION tr_situacao_contrato_aposentadoria() RETURNS TRIGGER AS $$
DECLARE
    stSchema        VARCHAR := '';
    stEntidade      VARCHAR := '';
    inCodPeriodo    INTEGER;
    stSQL           VARCHAR;
BEGIN

    SELECT nspname
      INTO stSchema
      FROM pg_namespace
      JOIN pg_class
        ON pg_class.relnamespace = pg_namespace.oid
     WHERE pg_class.oid = TG_RELID
         ;

    IF substr(stSchema, length(stSchema)-1, 1) = '_' THEN
        stEntidade := substr(stSchema, length(stSchema)-1, 2);
    END IF;

    IF      TG_OP = 'INSERT' THEN
        inCodPeriodo := selectintointeger('
                                              SELECT cod_periodo_movimentacao
                                                FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            ORDER BY cod_periodo_movimentacao DESc
                                               LIMIT 1
                                                   ;
                                          ');

        stSQL := '
                   INSERT
                     INTO '|| stSchema ||'.contrato_servidor_situacao
                        ( cod_contrato
                        , situacao
                        , cod_periodo_movimentacao
                        , situacao_literal
                        )
                   VALUES
                        ( '|| NEW.cod_contrato ||'
                        , '|| quote_literal('P') ||'
                        , '|| inCodPeriodo ||'
                        , '|| quote_literal('Aposentado') ||'
                        );
                 ';
    END IF;

    EXECUTE stSQL;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION tr_situacao_contrato_aposentadoria_excluida() RETURNS TRIGGER AS $$
DECLARE
    stSchema        VARCHAR := '';
    stEntidade      VARCHAR := '';
    inCodPeriodo    INTEGER;
    stSQL           VARCHAR;
BEGIN

    SELECT nspname
      INTO stSchema
      FROM pg_namespace
      JOIN pg_class
        ON pg_class.relnamespace = pg_namespace.oid
     WHERE pg_class.oid = TG_RELID
         ;

    IF substr(stSchema, length(stSchema)-1, 1) = '_' THEN
        stEntidade := substr(stSchema, length(stSchema)-1, 2);
    END IF;

    IF      TG_OP = 'INSERT' THEN
        stSQL := '
                   UPDATE '|| stSchema ||'.contrato_servidor_situacao
                      SET deleted = TRUE
                    WHERE cod_contrato = '|| NEW.cod_contrato   ||'
                      AND situacao     = '|| quote_literal('P') ||'
                        ;
                 ';
    END IF;

    EXECUTE stSQL;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION tr_situacao_contrato_pensionista() RETURNS TRIGGER AS $$
DECLARE
    stSchema        VARCHAR := '';
    stEntidade      VARCHAR := '';
    inCodPeriodo    INTEGER;
    stSQL           VARCHAR;
BEGIN

    SELECT nspname
      INTO stSchema
      FROM pg_namespace
      JOIN pg_class
        ON pg_class.relnamespace = pg_namespace.oid
     WHERE pg_class.oid = TG_RELID
         ;

    IF substr(stSchema, length(stSchema)-1, 1) = '_' THEN
        stEntidade := substr(stSchema, length(stSchema)-1, 2);
    END IF;

    IF      TG_OP = 'INSERT' THEN
        inCodPeriodo := selectintointeger('
                                              SELECT cod_periodo_movimentacao
                                                FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            ORDER BY cod_periodo_movimentacao DESc
                                               LIMIT 1
                                                   ;
                                          ');

        stSQL := '
                   INSERT
                     INTO '|| stSchema ||'.contrato_servidor_situacao
                        ( cod_contrato
                        , situacao
                        , cod_periodo_movimentacao
                        , situacao_literal
                        )
                   VALUES
                        ( '|| NEW.cod_contrato ||'
                        , '|| quote_literal('E') ||'
                        , '|| inCodPeriodo ||'
                        , '|| quote_literal('Pensionista') ||'
                        );
                 ';
    ELSIF TG_OP = 'DELETE' THEN
        stSQL := '
                   UPDATE '|| stSchema ||'.contrato_servidor_situacao
                      SET deleted = TRUE
                    WHERE cod_contrato = '|| OLD.cod_contrato   ||'
                      AND situacao     = '|| quote_literal('E') ||'
                        ;
                 ';
    END IF;

    EXECUTE stSQL;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION tr_situacao_contrato_servidor() RETURNS TRIGGER AS $$
DECLARE
    stSchema        VARCHAR := '';
    stEntidade      VARCHAR := '';
    inCodPeriodo    INTEGER;
    stSQL           VARCHAR;
BEGIN

    SELECT nspname
      INTO stSchema
      FROM pg_namespace
      JOIN pg_class
        ON pg_class.relnamespace = pg_namespace.oid
     WHERE pg_class.oid = TG_RELID
         ;

    IF substr(stSchema, length(stSchema)-1, 1) = '_' THEN
        stEntidade := substr(stSchema, length(stSchema)-1, 2);
    END IF;

    IF      TG_OP = 'INSERT' THEN
        inCodPeriodo := selectintointeger('
                                              SELECT cod_periodo_movimentacao
                                                FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            ORDER BY cod_periodo_movimentacao DESc
                                               LIMIT 1
                                                   ;
                                          ');

        stSQL := '
                   INSERT
                     INTO '|| stSchema ||'.contrato_servidor_situacao
                        ( cod_contrato
                        , situacao
                        , cod_periodo_movimentacao
                        , situacao_literal
                        )
                   VALUES
                        ( '|| NEW.cod_contrato ||'
                        , '|| quote_literal('A') ||'
                        , '|| inCodPeriodo ||'
                        , '|| quote_literal('Ativo') ||'
                        );
                 ';
    ELSIF TG_OP = 'DELETE' THEN
        stSQL := '
                   UPDATE '|| stSchema ||'.contrato_servidor_situacao
                      SET deleted = TRUE
                    WHERE cod_contrato = '|| OLD.cod_contrato   ||'
                        ;
                 ';
    END IF;

    EXECUTE stSQL;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION tr_situacao_contrato_servidor_caso_causa() RETURNS TRIGGER AS $$
DECLARE
    stSchema        VARCHAR := '';
    stEntidade      VARCHAR := '';
    inCodPeriodo    INTEGER;
    stSQL           VARCHAR;
BEGIN

    SELECT nspname
      INTO stSchema
      FROM pg_namespace
      JOIN pg_class
        ON pg_class.relnamespace = pg_namespace.oid
     WHERE pg_class.oid = TG_RELID
         ;

    IF substr(stSchema, length(stSchema)-1, 1) = '_' THEN
        stEntidade := substr(stSchema, length(stSchema)-1, 2);
    END IF;

    IF      TG_OP = 'INSERT' THEN
        inCodPeriodo := selectintointeger('
                                              SELECT cod_periodo_movimentacao
                                                FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                            ORDER BY cod_periodo_movimentacao DESc
                                               LIMIT 1
                                                   ;
                                          ');

        stSQL := '
                   INSERT
                     INTO '|| stSchema ||'.contrato_servidor_situacao
                        ( cod_contrato
                        , situacao
                        , cod_periodo_movimentacao
                        , situacao_literal
                        )
                   VALUES
                        ( '|| NEW.cod_contrato ||'
                        , '|| quote_literal('R') ||'
                        , '|| inCodPeriodo ||'
                        , '|| quote_literal('Rescindido') ||'
                        );
                 ';
    ELSIF TG_OP = 'DELETE' THEN
        stSQL := '
                   UPDATE '|| stSchema ||'.contrato_servidor_situacao
                      SET deleted = TRUE
                    WHERE cod_contrato = '|| OLD.cod_contrato   ||'
                      AND situacao     = '|| quote_literal('R') ||'
                      AND timestamp    = (
                                           SELECT MAX(timestamp)
                                             FROM '|| stSchema ||'.contrato_servidor_situacao
                                            WHERE cod_contrato = '|| OLD.cod_contrato   ||'
                                              AND situacao     = '|| quote_literal('R') ||'
                                         )
                        ;
                 ';
    END IF;

    EXECUTE stSQL;

    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';


SELECT atualizarbanco('CREATE TRIGGER trg_situacao_contrato_pensionista            BEFORE INSERT OR DELETE ON pessoal.contrato_pensionista         FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_pensionista();           ');
SELECT atualizarbanco('CREATE TRIGGER trg_situacao_contrato_servidor               BEFORE INSERT OR DELETE ON pessoal.contrato_servidor            FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor();              ');
SELECT atualizarbanco('CREATE TRIGGER trg_situacao_contrato_aposentadoria          BEFORE INSERT OR DELETE ON pessoal.aposentadoria                FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria();         ');
SELECT atualizarbanco('CREATE TRIGGER trg_situacao_contrato_aposentadoria_excluida BEFORE INSERT OR DELETE ON pessoal.aposentadoria_excluida       FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_aposentadoria_excluida();');
SELECT atualizarbanco('CREATE TRIGGER trg_situacao_contrato_servidor_caso_causa    BEFORE INSERT OR DELETE ON pessoal.contrato_servidor_caso_causa FOR EACH ROW EXECUTE PROCEDURE tr_situacao_contrato_servidor_caso_causa();   ');


----------------
-- Ticket #20273
----------------

SELECT atualizarbanco('
ALTER TABLE pessoal.pensionista_cid ADD COLUMN data_laudo DATE;
');


----------------
-- Ticket #20585
----------------

CREATE TYPE colunasUltimoContratoServidorCargoCbo AS (
    cod_cbo     INTEGER,
    cod_cargo   INTEGER
   );


----------------
-- Ticket #20275
----------------

DROP   TYPE colunasContratoServidor CASCADE;
CREATE TYPE colunasContratoServidor AS (
    cod_contrato                INTEGER,
    cod_norma                   INTEGER,
    cod_forma_pagamento         INTEGER,
    cod_tipo_pagamento          INTEGER,
    cod_tipo_salario            INTEGER,
    cod_tipo_admissao           INTEGER,
    cod_categoria               INTEGER,
    cod_vinculo                 INTEGER,
    cod_cargo                   INTEGER,
    desc_cargo                  VARCHAR,
    cod_regime                  INTEGER,
    desc_regime                 VARCHAR,
    cod_sub_divisao             INTEGER,
    desc_sub_divisao            VARCHAR, 
    nr_cartao_ponto             VARCHAR,
    ativo                       BOOLEAN,
    dt_opcao_fgts               DATE,
    adiantamento                BOOLEAN,
    cod_grade                   INTEGER,
    registro                    INTEGER,
    cod_servidor                INTEGER,
    cod_uf                      INTEGER,
    cod_municipio               INTEGER,
    numcgm                      INTEGER,
    nome_pai                    VARCHAR, 
    nome_mae                    VARCHAR, 
    zona_titulo                 VARCHAR,
    secao_titulo                VARCHAR,
    caminho_foto                VARCHAR,
    nr_titulo_eleitor           VARCHAR,
    cod_estado_civil            INTEGER,
    cod_raca                    INTEGER,
    cod_orgao                   INTEGER,
    desc_orgao                  VARCHAR,
    orgao                       VARCHAR,
    cod_local                   INTEGER,
    desc_local                  VARCHAR,
    cod_regime_funcao           INTEGER,
    desc_regime_funcao          VARCHAR,
    cod_sub_divisao_funcao      INTEGER,
    desc_sub_divisao_funcao     VARCHAR,
    cod_funcao                  INTEGER,
    desc_funcao                 VARCHAR,
    cod_cbo_funcao              INTEGER,
    desc_cbo_funcao             VARCHAR,
    cod_especialidade_funcao    INTEGER,
    desc_especialidade_funcao   VARCHAR,
    cod_especialidade_cargo     INTEGER,
    desc_especialidade_cargo    VARCHAR,
    cod_tipo                    INTEGER,
    desc_tipo_cedencia          VARCHAR,
    cod_conselho                INTEGER,
    sigla_conselho              VARCHAR,
    desc_conselho               VARCHAR,
    cod_agencia_fgts            INTEGER,
    cod_banco_fgts              INTEGER,
    nr_conta_fgts               VARCHAR,
    cod_agencia_salario         INTEGER,
    cod_banco_salario           INTEGER,
    nr_conta_salario            VARCHAR,
    num_banco_salario           VARCHAR,
    nom_banco_salario           VARCHAR,
    num_agencia_salario         VARCHAR,
    nom_agencia_salario         VARCHAR,
    dt_validade_exame           DATE,
    dt_inicio_progressao        DATE,
    cod_nivel_padrao            INTEGER,
    dt_nomeacao                 DATE,
    dt_posse                    DATE,
    dt_admissao                 DATE,
    cod_ocorrencia              INTEGER,
    cod_padrao                  INTEGER,
    valor_padrao                NUMERIC,
    desc_padrao                 VARCHAR,
    cod_previdencia             INTEGER,
    salario                     NUMERIC,
    horas_mensais               NUMERIC,
    horas_semanais              NUMERIC,
    vigencia                    DATE,
    numcgm_sindicato            INTEGER,
    cod_cid                     INTEGER,
    numcgm_conjuge              INTEGER,
    nr_carteira_res             VARCHAR,
    cat_reservista              VARCHAR,
    origem_reservista           VARCHAR,
    nom_cgm                     VARCHAR,
    servidor_pis_pasep          VARCHAR,
    rg                          VARCHAR,
    cpf                         VARCHAR,
    dt_nascimento               DATE,
    valor_atributo              VARCHAR,
    data_laudo                  DATE
);

DROP   TYPE colunasContratoPensionista CASCADE;
CREATE TYPE colunasContratoPensionista AS (
    cod_contrato                INTEGER,
    registro                    INTEGER,
    cod_contrato_cedente        INTEGER,
    cod_dependencia             INTEGER,
    cod_pensionista             INTEGER,
    num_beneficio               VARCHAR,
    percentual_pagamento        NUMERIC,
    dt_inicio_beneficio         DATE,
    dt_encerramento             DATE,
    motivo_encerramento         VARCHAR,
    cod_profissao               INTEGER,
    numcgm                      INTEGER,
    cod_grau                    INTEGER,
    cod_orgao                   INTEGER,
    desc_orgao                  VARCHAR,
    orgao                       VARCHAR,
    cod_local                   INTEGER,
    desc_local                  VARCHAR,
    cod_agencia_salario         INTEGER,
    cod_banco_salario           INTEGER,
    nr_conta_salario            VARCHAR,
    num_banco_salario           VARCHAR,
    nom_banco_salario           VARCHAR,
    num_agencia_salario         VARCHAR,
    nom_agencia_salario         VARCHAR,
    cod_previdencia             INTEGER,
    cod_processo                INTEGER,
    cod_cid                     INTEGER,
    cod_regime_funcao           INTEGER,
    desc_regime_funcao          VARCHAR,
    cod_sub_divisao_funcao      INTEGER,
    desc_sub_divisao_funcao     VARCHAR,
    cod_funcao                  INTEGER,
    desc_funcao                 VARCHAR,
    desc_cbo_funcao             VARCHAR,
    cod_especialidade_funcao    INTEGER,
    desc_especialidade_funcao   VARCHAR,
    nom_cgm                     VARCHAR,
    rg                          VARCHAR,
    cpf                         VARCHAR,
    dt_nascimento               DATE,
    valor_atributo              VARCHAR,
    data_laudo                  DATE
);

CREATE OR REPLACE FUNCTION recuperarContratoServidor(VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasContratoServidor AS $$
DECLARE
    stConfiguracao                  ALIAS FOR $1;
    stEntidade                      ALIAS FOR $2;
    inCodPeriodoMovimentacao        ALIAS FOR $3;
    stTipoFiltro                    ALIAS FOR $4;
    stValoresFiltro                 ALIAS FOR $5;
    stExercicio                     ALIAS FOR $6;
    rwContratoServidor              colunasContratoServidor%ROWTYPE;
    stSql                           VARCHAR;
    stSqlAux                        VARCHAR;
    inCodTipoAtributo               INTEGER;
    stTimestampFechamentoPeriodo    VARCHAR;
    stCodigos                       VARCHAR;
    stContagemTempo                 VARCHAR;
    reContratoServidor              RECORD;
    reRegistro                      RECORD;
    crCursor                        REFCURSOR;
    arConfiguracao                  VARCHAR[];    
    arValoresFiltro                 VARCHAR[];    
    inIndex                         INTEGER := 1;    
    inCodOrganograma                INTEGER;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);
    arConfiguracao := string_to_array(stConfiguracao,','); 

    stSql := 'SELECT valor 
                FROM administracao.configuracao 
               WHERE parametro = '|| quote_literal('dtContagemInicial'|| stEntidade) ||' AND exercicio = '|| quote_literal(stExercicio) ||' ';
    stContagemTempo := selectIntoVarchar(stSql);

    stSql := '    SELECT contrato_servidor.*
                       , contrato.registro
                       , servidor.*';
                       
    IF stTipoFiltro = 'atributo_servidor' OR stTipoFiltro = 'atributo_servidor_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        
        --Verifica o tipo do atributo 
        --TODO - Pegar o cod_modulo e cod_cadastro vindos do parametro stValoresFiltro. atualmente so é passado o cod_atributo
        stSqlAux := 'SELECT cod_tipo FROM administracao.atributo_dinamico WHERE cod_modulo = 22 AND cod_cadastro = 5 AND cod_atributo = '||arValoresFiltro[2];
        inCodTipoAtributo := selectIntoInteger(stSqlAux);
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || ', atributo_valor_padrao.valor_padrao as valor_atributo';
        ELSE
            stSql := stSql || ', atributo_contrato_servidor_valor.valor as valor_atributo';
        END IF;
    ELSE
        stSql := stSql || ', ''''::varchar as valor_atributo';
    END IF;          
    
    stSql := stSql || '
                    FROM pessoal'||stEntidade||'.contrato_servidor 
              INNER JOIN pessoal'||stEntidade||'.contrato
                      ON contrato.cod_contrato = contrato_servidor.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                      ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.servidor
                      ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor';

    IF stTipoFiltro = 'reg_sub_fun_esp' OR stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                    ON contrato_servidor_regime_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_regime_funcao.cod_regime IN ('||arValoresFiltro[1]||')
                            INNER JOIN (  SELECT contrato_servidor_regime_funcao.cod_contrato
                                               , max(contrato_servidor_regime_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                           WHERE contrato_servidor_regime_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_regime_funcao.cod_contrato) as max_contrato_servidor_regime_funcao
                                    ON max_contrato_servidor_regime_funcao.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                   AND max_contrato_servidor_regime_funcao.timestamp = contrato_servidor_regime_funcao.timestamp
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||arValoresFiltro[2]||')
                            INNER JOIN (  SELECT contrato_servidor_sub_divisao_funcao.cod_contrato
                                               , max(contrato_servidor_sub_divisao_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                           WHERE contrato_servidor_sub_divisao_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_sub_divisao_funcao.cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                    ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                   AND max_contrato_servidor_sub_divisao_funcao.timestamp = contrato_servidor_sub_divisao_funcao.timestamp
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_funcao
                                    ON contrato_servidor_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_funcao.cod_cargo IN ('||arValoresFiltro[3]||')
                            INNER JOIN (  SELECT contrato_servidor_funcao.cod_contrato
                                               , max(contrato_servidor_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                                           WHERE contrato_servidor_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao
                                    ON max_contrato_servidor_funcao.cod_contrato = contrato_servidor_funcao.cod_contrato
                                   AND max_contrato_servidor_funcao.timestamp = contrato_servidor_funcao.timestamp';
        IF trim(arValoresFiltro[4]) != '' THEN
            stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                        ON contrato_servidor_especialidade_funcao.cod_contrato = contrato.cod_contrato
                                       AND contrato_servidor_especialidade_funcao.cod_especialidade IN ('||arValoresFiltro[4]||')
                                INNER JOIN (  SELECT contrato_servidor_especialidade_funcao.cod_contrato
                                                   , max(contrato_servidor_especialidade_funcao.timestamp) as timestamp
                                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                               WHERE contrato_servidor_especialidade_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                            GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao
                                        ON max_contrato_servidor_especialidade_funcao.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                       AND max_contrato_servidor_especialidade_funcao.timestamp = contrato_servidor_especialidade_funcao.timestamp';
        END IF;
    END IF;
    IF stTipoFiltro = 'reg_sub_car_esp' OR stTipoFiltro = 'reg_sub_car_esp_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        IF trim(arValoresFiltro[4]) != '' THEN
            stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_especialidade_cargo
                                        ON contrato_servidor_especialidade_cargo.cod_contrato = contrato.cod_contrato
                                       AND contrato_servidor_especialidade_cargo.cod_especialidade IN ('||arValoresFiltro[4]||')';
        END IF;
        stSql := stSql || ' WHERE contrato_servidor.cod_regime IN ('||arValoresFiltro[1]||')
                              AND contrato_servidor.cod_sub_divisao IN ('||arValoresFiltro[2]||')
                              AND contrato_servidor.cod_cargo IN ('||arValoresFiltro[3]||')';
    END IF;
    IF stTipoFiltro = 'contrato'                OR
       stTipoFiltro = 'contrato_todos'          OR
       stTipoFiltro = 'contrato_rescisao'       OR
       stTipoFiltro = 'contrato_aposentado'     OR
       stTipoFiltro = 'cgm_contrato'            OR
       stTipoFiltro = 'cgm_contrato_aposentado' OR
       stTipoFiltro = 'cgm_contrato_rescisao'   OR
       stTipoFiltro = 'cgm_contrato_todos'      THEN
        stSql := stSql || ' WHERE contrato.cod_contrato IN ('||stValoresFiltro||')';
    END IF;
    IF stTipoFiltro = 'lotacao' OR stTipoFiltro = 'lotacao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_orgao
                                    ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_orgao.cod_orgao IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_orgao.cod_contrato
                                               , max(contrato_servidor_orgao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                                           WHERE contrato_servidor_orgao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao
                                    ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   AND max_contrato_servidor_orgao.timestamp = contrato_servidor_orgao.timestamp';
    END IF;
    IF stTipoFiltro = 'local' OR stTipoFiltro = 'local_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_local
                                    ON contrato_servidor_local.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_local.cod_local IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_local.cod_contrato
                                               , max(contrato_servidor_local.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_local
                                           WHERE contrato_servidor_local.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_local.cod_contrato) as max_contrato_servidor_local
                                    ON max_contrato_servidor_local.cod_contrato = contrato_servidor_local.cod_contrato
                                   AND max_contrato_servidor_local.timestamp = contrato_servidor_local.timestamp';
    END IF;
    IF stTipoFiltro = 'sub_divisao' OR stTipoFiltro = 'sub_divisao_grupo' THEN
        stSql := stSql || ' WHERE contrato_servidor.cod_sub_divisao IN ('||stValoresFiltro||')';
    END IF;
    IF stTipoFiltro = 'sub_divisao_funcao' OR stTipoFiltro = 'sub_divisao_funcao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_sub_divisao_funcao.cod_contrato
                                               , max(contrato_servidor_sub_divisao_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                           WHERE contrato_servidor_sub_divisao_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_sub_divisao_funcao.cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                    ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                   AND max_contrato_servidor_sub_divisao_funcao.timestamp = contrato_servidor_sub_divisao_funcao.timestamp';
    END IF;
    
    IF stTipoFiltro = 'atributo_servidor' OR stTipoFiltro = 'atributo_servidor_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                                    ON atributo_contrato_servidor_valor.cod_contrato = contrato.cod_contrato
                                   AND atributo_contrato_servidor_valor.cod_atributo = '||arValoresFiltro[2];

        IF arValoresFiltro[1] = '1' THEN
            stSql := stSql || '        AND atributo_contrato_servidor_valor.valor IN ('||arValoresFiltro[3]||')';
        ELSE
            stSql := stSql || '        AND atributo_contrato_servidor_valor.valor = '''||arValoresFiltro[3]||'''';
        END IF;
                                   
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || '
                            INNER JOIN administracao.atributo_valor_padrao
                                    ON atributo_valor_padrao.cod_modulo = atributo_contrato_servidor_valor.cod_modulo
                                   AND atributo_valor_padrao.cod_cadastro = atributo_contrato_servidor_valor.cod_cadastro
                                   AND atributo_valor_padrao.cod_atributo = atributo_contrato_servidor_valor.cod_atributo
                                   AND atributo_valor_padrao.cod_valor = atributo_contrato_servidor_valor.valor';
        END IF;
        
        stSql := stSql || '
                            INNER JOIN (  SELECT atributo_contrato_servidor_valor.cod_contrato
                                               , atributo_contrato_servidor_valor.cod_atributo
                                               , max(atributo_contrato_servidor_valor.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                                           WHERE atributo_contrato_servidor_valor.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY atributo_contrato_servidor_valor.cod_contrato
                                               , atributo_contrato_servidor_valor.cod_atributo) as max_atributo_contrato_servidor_valor
                                    ON max_atributo_contrato_servidor_valor.cod_contrato = atributo_contrato_servidor_valor.cod_contrato
                                   AND max_atributo_contrato_servidor_valor.cod_atributo = atributo_contrato_servidor_valor.cod_atributo
                                   AND max_atributo_contrato_servidor_valor.timestamp = atributo_contrato_servidor_valor.timestamp';
    END IF;
    
    IF stTipoFiltro = 'padrao' OR stTipoFiltro = 'padrao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_padrao
                                    ON contrato_servidor_padrao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_padrao.cod_padrao IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_padrao.cod_contrato
                                               , max(contrato_servidor_padrao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_padrao
                                           WHERE contrato_servidor_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_padrao.cod_contrato) as max_contrato_servidor_padrao
                                    ON max_contrato_servidor_padrao.cod_contrato = contrato_servidor_padrao.cod_contrato
                                   AND max_contrato_servidor_padrao.timestamp = contrato_servidor_padrao.timestamp';
    END IF;
    IF stTipoFiltro = 'cargo' OR stTipoFiltro = 'cargo_grupo' THEN
        stSql := stSql || ' WHERE contrato_servidor.cod_cargo IN ('||stValoresFiltro||')';
    END IF;
    IF stTipoFiltro = 'funcao' OR stTipoFiltro = 'funcao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_funcao
                                    ON contrato_servidor_funcao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_funcao.cod_cargo IN ('||stValoresFiltro||')
                            INNER JOIN (  SELECT contrato_servidor_funcao.cod_contrato
                                               , max(contrato_servidor_funcao.timestamp) as timestamp
                                            FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                                           WHERE contrato_servidor_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                        GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao
                                    ON max_contrato_servidor_funcao.cod_contrato = contrato_servidor_funcao.cod_contrato
                                   AND max_contrato_servidor_funcao.timestamp = contrato_servidor_funcao.timestamp';
    END IF;


    FOR reContratoServidor IN EXECUTE stSql LOOP
        --DADOS DA TABELA pessoal'||stEntidade||'.contrato
        rwContratoServidor.registro            := reContratoServidor.registro;       
                         
        --DADOS DA TABELA pessoal'||stEntidade||'.servidor
        rwContratoServidor.cod_servidor        := reContratoServidor.cod_servidor;     
        rwContratoServidor.cod_uf              := reContratoServidor.cod_uf;           
        rwContratoServidor.cod_municipio       := reContratoServidor.cod_municipio;    
        rwContratoServidor.numcgm              := reContratoServidor.numcgm;           
        rwContratoServidor.nome_pai            := reContratoServidor.nome_pai;         
        rwContratoServidor.nome_mae            := reContratoServidor.nome_mae;         
        rwContratoServidor.zona_titulo         := reContratoServidor.zona_titulo;      
        rwContratoServidor.secao_titulo        := reContratoServidor.secao_titulo;     
        rwContratoServidor.caminho_foto        := reContratoServidor.caminho_foto;     
        rwContratoServidor.nr_titulo_eleitor   := reContratoServidor.nr_titulo_eleitor;
        rwContratoServidor.cod_estado_civil    := reContratoServidor.cod_estado_civil; 
        rwContratoServidor.cod_raca            := reContratoServidor.cod_raca;         


        --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor
        rwContratoServidor.cod_contrato        := reContratoServidor.cod_contrato;       
        rwContratoServidor.cod_norma           := reContratoServidor.cod_norma;          
        rwContratoServidor.cod_tipo_pagamento  := reContratoServidor.cod_tipo_pagamento; 
        rwContratoServidor.cod_tipo_salario    := reContratoServidor.cod_tipo_salario;   
        rwContratoServidor.cod_tipo_admissao   := reContratoServidor.cod_tipo_admissao;  
        rwContratoServidor.cod_categoria       := reContratoServidor.cod_categoria;      
        rwContratoServidor.cod_vinculo         := reContratoServidor.cod_vinculo;        
        rwContratoServidor.cod_cargo           := reContratoServidor.cod_cargo;          
        rwContratoServidor.cod_regime          := reContratoServidor.cod_regime;         
        rwContratoServidor.cod_sub_divisao     := reContratoServidor.cod_sub_divisao;    
        rwContratoServidor.nr_cartao_ponto     := reContratoServidor.nr_cartao_ponto;    
        rwContratoServidor.ativo               := reContratoServidor.ativo;              
        rwContratoServidor.dt_opcao_fgts       := reContratoServidor.dt_opcao_fgts;      
        rwContratoServidor.adiantamento        := reContratoServidor.adiantamento;       
        rwContratoServidor.cod_grade           := reContratoServidor.cod_grade;  
        rwContratoServidor.valor_atributo  := reContratoServidor.valor_atributo;               

        WHILE arConfiguracao[inIndex] IS NOT NULL LOOP
            --DADOS DA TABELA pessoal.contrato_servidor_nomeacao_posse
            IF arConfiguracao[inIndex] = 'anp' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '  SELECT contrato_servidor_nomeacao_posse.dt_nomeacao
                                 , contrato_servidor_nomeacao_posse.dt_posse
                                 , contrato_servidor_nomeacao_posse.dt_admissao
                              FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                        INNER JOIN (  SELECT contrato_servidor_nomeacao_posse.cod_contrato
                                           , max(contrato_servidor_nomeacao_posse.timestamp) as timestamp
                                        FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                       WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                    GROUP BY contrato_servidor_nomeacao_posse.cod_contrato) as max_contrato_servidor_nomeacao_posse
                                ON max_contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
                               AND max_contrato_servidor_nomeacao_posse.timestamp = contrato_servidor_nomeacao_posse.timestamp
                             WHERE contrato_servidor_nomeacao_posse.cod_contrato = '||reContratoServidor.cod_contrato;

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.dt_nomeacao := reRegistro.dt_nomeacao;
                rwContratoServidor.dt_posse    := reRegistro.dt_posse;
                rwContratoServidor.dt_admissao := reRegistro.dt_admissao;               
            END IF;

            --DADOS DA TABELA pessoal.cargo
            IF arConfiguracao[inIndex] = 'ca' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT cargo.descricao as desc_cargo
                                FROM pessoal'||stEntidade||'.cargo
                               WHERE cargo.cod_cargo = '||reContratoServidor.cod_cargo;
                rwContratoServidor.desc_cargo   := selectIntoVarchar(stSql);
            END IF;

            --DADOS DA TABELA pessoal.regime
            IF arConfiguracao[inIndex] = 'car' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT regime.descricao as desc_regime
                                FROM pessoal'||stEntidade||'.regime
                               WHERE regime.cod_regime = '||reContratoServidor.cod_regime;
                rwContratoServidor.desc_regime   := selectIntoVarchar(stSql);
            END IF;

            --DADOS DA TABELA pessoal.sub_divisao
            IF arConfiguracao[inIndex] = 'cas' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT sub_divisao.descricao as desc_sub_divisao
                                FROM pessoal'||stEntidade||'.sub_divisao
                               WHERE sub_divisao.cod_sub_divisao = '||reContratoServidor.cod_sub_divisao;
                rwContratoServidor.desc_sub_divisao   := selectIntoVarchar(stSql);
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_cedencia
            IF arConfiguracao[inIndex] = 'c' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_cedencia.cod_tipo
                                   , tipo_cedencia.descricao as desc_tipo_cedencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_cedencia
                          INNER JOIN pessoal'||stEntidade||'.tipo_cedencia
                                  ON tipo_cedencia.cod_tipo = contrato_servidor_cedencia.cod_tipo
                               WHERE contrato_servidor_cedencia.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_tipo             := reRegistro.cod_tipo;
                rwContratoServidor.desc_tipo_cedencia   := reRegistro.desc_tipo_cedencia;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_conselho
            IF arConfiguracao[inIndex] = 'co' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_conselho.cod_conselho
                                   , conselho.sigla as sigla_conselho
                                   , conselho.descricao as desc_conselho
                                FROM pessoal'||stEntidade||'.contrato_servidor_conselho
                          INNER JOIN pessoal'||stEntidade||'.conselho
                                  ON conselho.cod_conselho = contrato_servidor_conselho.cod_conselho
                               WHERE contrato_servidor_conselho.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_conselho     := reRegistro.cod_conselho;
                rwContratoServidor.sigla_conselho   := reRegistro.sigla_conselho;
                rwContratoServidor.desc_conselho    := reRegistro.desc_conselho;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_conta_fgts
            IF arConfiguracao[inIndex] = 'cf' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_conta_fgts.cod_agencia as cod_agencia_fgts
                                   , contrato_servidor_conta_fgts.cod_banco as cod_banco_fgts
                                   , contrato_servidor_conta_fgts.nr_conta as nr_conta_fgts
                                FROM pessoal'||stEntidade||'.contrato_servidor_conta_fgts
                               WHERE contrato_servidor_conta_fgts.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_agencia_fgts     := reRegistro.cod_agencia_fgts;
                rwContratoServidor.cod_banco_fgts       := reRegistro.cod_banco_fgts;
                rwContratoServidor.nr_conta_fgts        := reRegistro.nr_conta_fgts;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_conta_salario
            IF arConfiguracao[inIndex] = 'cs' OR arConfiguracao[inIndex] = 'all' THEN
                -- Verifica no histórico se foi pago em crédito em banco ou foi pago em outra forma de pagamento
                -- Caso tenha sido pago em outra forma, retornar vazio os dados da conta salário
                stSql := '    SELECT contrato_servidor_forma_pagamento.*
                                FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                          INNER JOIN (  SELECT contrato_servidor_forma_pagamento.cod_contrato
                                             , max(timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                                         WHERE timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_forma_pagamento.cod_contrato
                                     ) as max_contrato_servidor_forma_pagamento
                                  ON contrato_servidor_forma_pagamento.cod_contrato = max_contrato_servidor_forma_pagamento.cod_contrato
                              AND contrato_servidor_forma_pagamento.timestamp = max_contrato_servidor_forma_pagamento.timestamp
                            WHERE contrato_servidor_forma_pagamento.cod_contrato = '||reContratoServidor.cod_contrato;

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_forma_pagamento := reRegistro.cod_forma_pagamento;

                IF rwContratoServidor.cod_forma_pagamento = 3 THEN --Crédito em conta
                    stSql := '    SELECT contrato_servidor_conta_salario_historico.cod_agencia as cod_agencia_salario
                                    , contrato_servidor_conta_salario_historico.cod_banco as cod_banco_salario
                                    , contrato_servidor_conta_salario_historico.nr_conta as nr_conta_salario
                                    , banco.num_banco as num_banco_salario
                                    , banco.nom_banco as nom_banco_salario
                                    , agencia.num_agencia as num_agencia_salario
                                    , agencia.nom_agencia as nom_agencia_salario
                                 FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico
                           INNER JOIN (  SELECT contrato_servidor_conta_salario_historico.cod_contrato
                                              , max(contrato_servidor_conta_salario_historico.timestamp) as timestamp
                                           FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico
                                          WHERE contrato_servidor_conta_salario_historico.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                       GROUP BY contrato_servidor_conta_salario_historico.cod_contrato) as max_contrato_servidor_conta_salario_historico
                                    ON max_contrato_servidor_conta_salario_historico.cod_contrato = contrato_servidor_conta_salario_historico.cod_contrato
                                   AND max_contrato_servidor_conta_salario_historico.timestamp = contrato_servidor_conta_salario_historico.timestamp
                            INNER JOIN monetario.agencia
                                    ON agencia.cod_banco = contrato_servidor_conta_salario_historico.cod_banco
                                   AND agencia.cod_agencia = contrato_servidor_conta_salario_historico.cod_agencia
                            INNER JOIN monetario.banco
                                    ON banco.cod_banco = contrato_servidor_conta_salario_historico.cod_banco
                                 WHERE contrato_servidor_conta_salario_historico.cod_contrato = '||reContratoServidor.cod_contrato;

                    OPEN crCursor FOR EXECUTE stSql;
                        FETCH crCursor INTO reRegistro;
                    CLOSE crCursor;
                    rwContratoServidor.cod_agencia_salario     := reRegistro.cod_agencia_salario;
                    rwContratoServidor.cod_banco_salario       := reRegistro.cod_banco_salario;
                    rwContratoServidor.nr_conta_salario        := reRegistro.nr_conta_salario;
                    rwContratoServidor.num_banco_salario       := reRegistro.num_banco_salario;
                    rwContratoServidor.nom_banco_salario       := reRegistro.nom_banco_salario;
                    rwContratoServidor.num_agencia_salario     := reRegistro.num_agencia_salario;
                    rwContratoServidor.nom_agencia_salario     := reRegistro.nom_agencia_salario;
                ELSE
                    rwContratoServidor.cod_agencia_salario     := NULL;
                    rwContratoServidor.cod_banco_salario       := NULL;
                    rwContratoServidor.nr_conta_salario        := NULL;
                    rwContratoServidor.num_banco_salario       := NULL;
                    rwContratoServidor.nom_banco_salario       := NULL;
                    rwContratoServidor.num_agencia_salario     := NULL;
                    rwContratoServidor.nom_agencia_salario     := NULL;
                END IF;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_exame_medico
            IF arConfiguracao[inIndex] = 'em' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_exame_medico.dt_validade_exame
                                FROM pessoal'||stEntidade||'.contrato_servidor_exame_medico
                          INNER JOIN (  SELECT contrato_servidor_exame_medico.cod_contrato
                                             , max(contrato_servidor_exame_medico.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_exame_medico
                                         WHERE contrato_servidor_exame_medico.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_exame_medico.cod_contrato) as max_contrato_servidor_exame_medico
                                  ON max_contrato_servidor_exame_medico.cod_contrato = contrato_servidor_exame_medico.cod_contrato
                                 AND max_contrato_servidor_exame_medico.timestamp = contrato_servidor_exame_medico.timestamp
                               WHERE contrato_servidor_exame_medico.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.dt_validade_exame  := reRegistro.dt_validade_exame;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_inicio_progressao
            IF arConfiguracao[inIndex] = 'ip' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_inicio_progressao.dt_inicio_progressao
                                FROM pessoal'||stEntidade||'.contrato_servidor_inicio_progressao
                          INNER JOIN (  SELECT contrato_servidor_inicio_progressao.cod_contrato
                                             , max(contrato_servidor_inicio_progressao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_inicio_progressao
                                         WHERE contrato_servidor_inicio_progressao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_inicio_progressao.cod_contrato) as max_contrato_servidor_inicio_progressao
                                  ON max_contrato_servidor_inicio_progressao.cod_contrato = contrato_servidor_inicio_progressao.cod_contrato
                                 AND max_contrato_servidor_inicio_progressao.timestamp = contrato_servidor_inicio_progressao.timestamp
                               WHERE contrato_servidor_inicio_progressao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.dt_inicio_progressao  := reRegistro.dt_inicio_progressao;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_nivel_padrao
            IF arConfiguracao[inIndex] = 'np' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_nivel_padrao.cod_nivel_padrao
                                FROM pessoal'||stEntidade||'.contrato_servidor_nivel_padrao
                          INNER JOIN (  SELECT contrato_servidor_nivel_padrao.cod_contrato
                                             , max(contrato_servidor_nivel_padrao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_nivel_padrao
                                         WHERE contrato_servidor_nivel_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_nivel_padrao.cod_contrato) as max_contrato_servidor_nivel_padrao
                                  ON max_contrato_servidor_nivel_padrao.cod_contrato = contrato_servidor_nivel_padrao.cod_contrato
                                 AND max_contrato_servidor_nivel_padrao.timestamp = contrato_servidor_nivel_padrao.timestamp
                               WHERE contrato_servidor_nivel_padrao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_nivel_padrao  := reRegistro.cod_nivel_padrao;
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_ocorrencia
            IF arConfiguracao[inIndex] = 'oc' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_ocorrencia.cod_ocorrencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                          INNER JOIN (  SELECT contrato_servidor_ocorrencia.cod_contrato
                                             , max(contrato_servidor_ocorrencia.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                                         WHERE contrato_servidor_ocorrencia.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_ocorrencia.cod_contrato) as max_contrato_servidor_ocorrencia
                                  ON max_contrato_servidor_ocorrencia.cod_contrato = contrato_servidor_ocorrencia.cod_contrato
                                 AND max_contrato_servidor_ocorrencia.timestamp = contrato_servidor_ocorrencia.timestamp
                               WHERE contrato_servidor_ocorrencia.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_ocorrencia  := reRegistro.cod_ocorrencia;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_padrao
            IF arConfiguracao[inIndex] = 'p' OR arConfiguracao[inIndex] = 'pp' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_padrao.cod_padrao
                                FROM pessoal'||stEntidade||'.contrato_servidor_padrao
                          INNER JOIN (  SELECT contrato_servidor_padrao.cod_contrato
                                             , max(contrato_servidor_padrao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_padrao
                                         WHERE contrato_servidor_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_padrao.cod_contrato) as max_contrato_servidor_padrao
                                  ON max_contrato_servidor_padrao.cod_contrato = contrato_servidor_padrao.cod_contrato
                                 AND max_contrato_servidor_padrao.timestamp = contrato_servidor_padrao.timestamp
                               WHERE contrato_servidor_padrao.cod_contrato = '||reContratoServidor.cod_contrato;
                
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_padrao  := reRegistro.cod_padrao;
                
                IF arConfiguracao[inIndex] = 'pp' THEN
                    IF rwContratoServidor.cod_padrao IS NOT NULL THEN 
                        stSql := '    SELECT padrao_padrao.valor
                                        , padrao.descricao
                                        FROM folhapagamento'||stEntidade||'.padrao_padrao
                                INNER JOIN (  SELECT padrao_padrao.cod_padrao
                                                    , max(padrao_padrao.timestamp) as timestamp
                                                FROM folhapagamento'||stEntidade||'.padrao_padrao
                                                WHERE padrao_padrao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                            GROUP BY padrao_padrao.cod_padrao) as max_padrao_padrao
                                        ON max_padrao_padrao.cod_padrao = padrao_padrao.cod_padrao
                                        AND max_padrao_padrao.timestamp = padrao_padrao.timestamp
                                INNER JOIN folhapagamento'||stEntidade||'.padrao
                                        ON padrao.cod_padrao = padrao_padrao.cod_padrao
                                    WHERE padrao_padrao.cod_padrao = '||rwContratoServidor.cod_padrao;
                        OPEN crCursor FOR EXECUTE stSql;
                            FETCH crCursor INTO reRegistro;
                        CLOSE crCursor;
                        rwContratoServidor.desc_padrao   := reRegistro.descricao;
                        rwContratoServidor.valor_padrao  := reRegistro.valor;
                    END IF;
                END IF;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_previdencia
            IF arConfiguracao[inIndex] = 'pr' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_previdencia.cod_previdencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                          INNER JOIN (  SELECT contrato_servidor_previdencia.cod_contrato
                                             , max(contrato_servidor_previdencia.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                         WHERE contrato_servidor_previdencia.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_previdencia.cod_contrato) as max_contrato_servidor_previdencia
                                  ON max_contrato_servidor_previdencia.cod_contrato = contrato_servidor_previdencia.cod_contrato
                                 AND max_contrato_servidor_previdencia.timestamp = contrato_servidor_previdencia.timestamp
                          INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                                  ON previdencia_previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                 AND previdencia_previdencia.tipo_previdencia = ''o''
                          INNER JOIN (  SELECT previdencia_previdencia.cod_previdencia
                                             , max(previdencia_previdencia.timestamp) as timestamp
                                          FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                      GROUP BY previdencia_previdencia.cod_previdencia) as max_previdencia_previdencia
                                  ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                 AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp
                               WHERE contrato_servidor_previdencia.bo_excluido = false
                                 AND contrato_servidor_previdencia.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_previdencia  := reRegistro.cod_previdencia;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_salario
            IF arConfiguracao[inIndex] = 's' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_salario.salario
                                   , contrato_servidor_salario.horas_mensais
                                   , contrato_servidor_salario.horas_semanais
                                   , contrato_servidor_salario.vigencia
                                FROM pessoal'||stEntidade||'.contrato_servidor_salario
                          INNER JOIN (  SELECT contrato_servidor_salario.cod_contrato
                                             , max(contrato_servidor_salario.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_salario
                                         WHERE contrato_servidor_salario.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_salario.cod_contrato) as max_contrato_servidor_salario
                                  ON max_contrato_servidor_salario.cod_contrato = contrato_servidor_salario.cod_contrato
                                 AND max_contrato_servidor_salario.timestamp = contrato_servidor_salario.timestamp
                               WHERE contrato_servidor_salario.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.salario          := reRegistro.salario;       
                rwContratoServidor.horas_mensais    := reRegistro.horas_mensais; 
                rwContratoServidor.horas_semanais   := reRegistro.horas_semanais;
                rwContratoServidor.vigencia         := reRegistro.vigencia;      
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_sindicato
            IF arConfiguracao[inIndex] = 'si' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_sindicato.numcgm_sindicato
                                FROM pessoal'||stEntidade||'.contrato_servidor_sindicato
                               WHERE contrato_servidor_sindicato.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.numcgm_sindicato          := reRegistro.numcgm_sindicato;       
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_orgao
            IF arConfiguracao[inIndex] = 'o' OR arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN        
                stSql := '    SELECT contrato_servidor_orgao.cod_orgao
                                   , recuperadescricaoorgao(contrato_servidor_orgao.cod_orgao,'''||stTimestampFechamentoPeriodo||''') as descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                          INNER JOIN (  SELECT contrato_servidor_orgao.cod_contrato
                                             , max(contrato_servidor_orgao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                                         WHERE contrato_servidor_orgao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_orgao.cod_contrato) as max_contrato_servidor_orgao
                                  ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato
                                 AND max_contrato_servidor_orgao.timestamp = contrato_servidor_orgao.timestamp
                               WHERE contrato_servidor_orgao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_orgao  := reRegistro.cod_orgao;
                rwContratoServidor.desc_orgao := reRegistro.descricao;

                --DADOS DA TABELA organograma.fn_consulta_orgao
                IF arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN        
                    inCodOrganograma := selectIntoInteger('SELECT cod_organograma FROM organograma.orgao_nivel WHERE cod_orgao = '||reRegistro.cod_orgao);
                    stSql := 'SELECT organograma.fn_consulta_orgao('||inCodOrganograma||','||reRegistro.cod_orgao||')';
                    rwContratoServidor.orgao := selectIntoVarchar(stSql); 
                END IF;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_local
            IF arConfiguracao[inIndex] = 'l' OR arConfiguracao[inIndex] = 'all' THEN        
                stSql := '    SELECT contrato_servidor_local.cod_local
                                   , local.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_local
                          INNER JOIN (  SELECT contrato_servidor_local.cod_contrato
                                             , max(contrato_servidor_local.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_local
                                         WHERE contrato_servidor_local.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_local.cod_contrato) as max_contrato_servidor_local
                                  ON max_contrato_servidor_local.cod_contrato = contrato_servidor_local.cod_contrato
                                 AND max_contrato_servidor_local.timestamp = contrato_servidor_local.timestamp
                          INNER JOIN organograma.local
                                  ON local.cod_local = contrato_servidor_local.cod_local
                               WHERE contrato_servidor_local.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_local  := reRegistro.cod_local;
                rwContratoServidor.desc_local := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_regime_funcao
            IF arConfiguracao[inIndex] = 'rf' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_regime_funcao.cod_regime as cod_regime_funcao
                                   , regime.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                          INNER JOIN (  SELECT contrato_servidor_regime_funcao.cod_contrato
                                             , max(contrato_servidor_regime_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                         WHERE contrato_servidor_regime_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_regime_funcao.cod_contrato) as max_contrato_servidor_regime_funcao
                                  ON max_contrato_servidor_regime_funcao.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                 AND max_contrato_servidor_regime_funcao.timestamp = contrato_servidor_regime_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.regime
                                  ON regime.cod_regime = contrato_servidor_regime_funcao.cod_regime
                               WHERE contrato_servidor_regime_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_regime_funcao  := reRegistro.cod_regime_funcao;
                rwContratoServidor.desc_regime_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
            IF arConfiguracao[inIndex] = 'sf' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao as cod_sub_divisao_funcao
                                   , sub_divisao.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                          INNER JOIN (  SELECT contrato_servidor_sub_divisao_funcao.cod_contrato
                                             , max(contrato_servidor_sub_divisao_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                         WHERE contrato_servidor_sub_divisao_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_sub_divisao_funcao.cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                  ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                 AND max_contrato_servidor_sub_divisao_funcao.timestamp = contrato_servidor_sub_divisao_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.sub_divisao
                                  ON sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                               WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_sub_divisao_funcao  := reRegistro.cod_sub_divisao_funcao;
                rwContratoServidor.desc_sub_divisao_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_funcao
            IF arConfiguracao[inIndex] = 'f' OR arConfiguracao[inIndex] = 'all' THEN        
                stSql := '    SELECT contrato_servidor_funcao.cod_cargo as cod_funcao
                                   , cargo.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                          INNER JOIN (  SELECT contrato_servidor_funcao.cod_contrato
                                             , max(contrato_servidor_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                                         WHERE contrato_servidor_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_funcao.cod_contrato) as max_contrato_servidor_funcao
                                  ON max_contrato_servidor_funcao.cod_contrato = contrato_servidor_funcao.cod_contrato
                                 AND max_contrato_servidor_funcao.timestamp = contrato_servidor_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.cargo
                                  ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                               WHERE contrato_servidor_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_funcao  := reRegistro.cod_funcao;
                rwContratoServidor.desc_funcao := reRegistro.descricao;

                IF rwContratoServidor.cod_funcao IS NOT NULL THEN 
                    stSql := ' SELECT * 
                                 FROM ( SELECT cargo.cod_cargo
                                             , cbo.codigo as cbo_codigo
                                             , cbo.cod_cbo as cod_cbo
                                             , cargo.descricao
                                          FROM pessoal'||stEntidade||'.cargo
                                             , pessoal'||stEntidade||'.cbo_cargo
                                             , (  SELECT cod_cargo
                                                       , max(timestamp) as timestamp
                                                    FROM pessoal'||stEntidade||'.cbo_cargo
                                                GROUP BY cod_cargo) as max_cbo_cargo
                                             , pessoal'||stEntidade||'.cbo
                                         WHERE cargo.cod_cargo = cbo_cargo.cod_cargo
                                           AND cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo 
                                           AND cbo_cargo.timestamp = max_cbo_cargo.timestamp
                                           AND cbo_cargo.cod_cbo = cbo.cod_cbo
                                         UNION
                                        SELECT cargo.cod_cargo
                                             , cbo.codigo as cbo_codigo
                                             , cbo.cod_cbo as cod_cbo
                                             , cargo.descricao
                                          FROM pessoal'||stEntidade||'.cargo
                                             , pessoal'||stEntidade||'.especialidade
                                             , pessoal'||stEntidade||'.cbo_especialidade
                                             , (  SELECT cod_especialidade
                                             , max(timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.cbo_especialidade
                                      GROUP BY cod_especialidade) as max_cod_especialidade
                                             , pessoal'||stEntidade||'.cbo
                                         WHERE cargo.cod_cargo = especialidade.cod_cargo
                                           AND especialidade.cod_especialidade = cbo_especialidade.cod_especialidade
                                           AND cbo_especialidade.cod_especialidade = max_cod_especialidade.cod_especialidade 
                                           AND cbo_especialidade.timestamp = max_cod_especialidade.timestamp
                                           AND cbo_especialidade.cod_cbo = cbo.cod_cbo) as funcao
                                         WHERE funcao.cod_cargo = '||reRegistro.cod_funcao;
    
                    OPEN crCursor FOR EXECUTE stSql;
                        FETCH crCursor INTO reRegistro;
                    CLOSE crCursor;
                    rwContratoServidor.cod_cbo_funcao               := reRegistro.cod_cbo;
                    rwContratoServidor.desc_cbo_funcao              := reRegistro.descricao;
                END IF;

            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
            IF arConfiguracao[inIndex] = 'ef' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao
                                   , especialidade.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                          INNER JOIN (  SELECT contrato_servidor_especialidade_funcao.cod_contrato
                                             , max(contrato_servidor_especialidade_funcao.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                         WHERE contrato_servidor_especialidade_funcao.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY contrato_servidor_especialidade_funcao.cod_contrato) as max_contrato_servidor_especialidade_funcao
                                  ON max_contrato_servidor_especialidade_funcao.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                 AND max_contrato_servidor_especialidade_funcao.timestamp = contrato_servidor_especialidade_funcao.timestamp
                          INNER JOIN pessoal'||stEntidade||'.especialidade
                                  ON especialidade.cod_especialidade = contrato_servidor_especialidade_funcao.cod_especialidade
                               WHERE contrato_servidor_especialidade_funcao.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_especialidade_funcao  := reRegistro.cod_especialidade_funcao;
                rwContratoServidor.desc_especialidade_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_especialidade_cargo
            IF arConfiguracao[inIndex] = 'ec' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT contrato_servidor_especialidade_cargo.cod_especialidade as cod_especialidade_cargo
                                   , especialidade.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_cargo
                          INNER JOIN pessoal'||stEntidade||'.especialidade
                                  ON especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade
                               WHERE contrato_servidor_especialidade_cargo.cod_contrato = '||reContratoServidor.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_especialidade_cargo  := reRegistro.cod_especialidade_cargo;
                rwContratoServidor.desc_especialidade_cargo := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.servidor_cid
            IF arConfiguracao[inIndex] = 'cid' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT  servidor_cid.cod_cid
                                    , servidor_cid.data_laudo
                                FROM pessoal'||stEntidade||'.servidor_cid
                          INNER JOIN (  SELECT servidor_cid.cod_servidor
                                             , max(servidor_cid.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.servidor_cid
                                         WHERE servidor_cid.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY servidor_cid.cod_servidor) as max_servidor_cid
                                  ON max_servidor_cid.cod_servidor = servidor_cid.cod_servidor
                                 AND max_servidor_cid.timestamp = servidor_cid.timestamp
                               WHERE servidor_cid.cod_servidor = '||reContratoServidor.cod_servidor;

                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.cod_cid  := reRegistro.cod_cid;
                rwContratoServidor.data_laudo  := reRegistro.data_laudo;
                
            END IF;
            --DADOS DA TABELA pessoal'||stEntidade||'.servidor_conjuge
            IF arConfiguracao[inIndex] = 'con' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT servidor_conjuge.numcgm as numcgm_conjuge
                                FROM pessoal'||stEntidade||'.servidor_conjuge
                          INNER JOIN (  SELECT servidor_conjuge.cod_servidor
                                             , max(servidor_conjuge.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.servidor_conjuge
                                         WHERE servidor_conjuge.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                      GROUP BY servidor_conjuge.cod_servidor) as max_servidor_conjuge
                                  ON max_servidor_conjuge.cod_servidor = servidor_conjuge.cod_servidor
                                 AND max_servidor_conjuge.timestamp = servidor_conjuge.timestamp
                               WHERE servidor_conjuge.bo_excluido = false
                                 AND servidor_conjuge.cod_servidor = '||reContratoServidor.cod_servidor;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.numcgm_conjuge  := reRegistro.numcgm_conjuge;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.servidor_reservista
            IF arConfiguracao[inIndex] = 'res' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT servidor_reservista.nr_carteira_res
                                   , servidor_reservista.cat_reservista
                                   , servidor_reservista.origem_reservista
                                FROM pessoal'||stEntidade||'.servidor_reservista
                               WHERE servidor_reservista.cod_servidor = '||reContratoServidor.cod_servidor;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.nr_carteira_res      := reRegistro.nr_carteira_res;
                rwContratoServidor.cat_reservista       := reRegistro.cat_reservista;
                rwContratoServidor.origem_reservista    := reRegistro.origem_reservista;
            END IF;

            --DADOS DA TABELA cgm
            IF arConfiguracao[inIndex] = 'cgm' OR arConfiguracao[inIndex] = 'all' THEN      
                stSql := '    SELECT sw_cgm.nom_cgm
                                   , sw_cgm_pessoa_fisica.servidor_pis_pasep
                                   , sw_cgm_pessoa_fisica.rg
                                   , cpf
                                   , sw_cgm_pessoa_fisica.dt_nascimento
                                FROM sw_cgm
                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                               WHERE sw_cgm.numcgm = '||reContratoServidor.numcgm;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoServidor.nom_cgm              := reRegistro.nom_cgm;            
                rwContratoServidor.servidor_pis_pasep   := reRegistro.servidor_pis_pasep; 
                rwContratoServidor.rg                   := reRegistro.rg;                 
                rwContratoServidor.cpf                  := reRegistro.cpf;                
                rwContratoServidor.dt_nascimento        := reRegistro.dt_nascimento;      
            END IF;
                
            inIndex := inIndex + 1;
        END LOOP;
        inIndex := 1;

        RETURN NEXT rwContratoServidor;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION recuperarContratoPensionista(VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasContratoPensionista AS $$
DECLARE
    stConfiguracao                  ALIAS FOR $1;
    stEntidade                      ALIAS FOR $2;
    inCodPeriodoMovimentacao        ALIAS FOR $3;
    stTipoFiltro                    ALIAS FOR $4;
    stValoresFiltro                 ALIAS FOR $5;
    stExercicio                     ALIAS FOR $6;
    rwContratoPensionista           colunasContratoPensionista%ROWTYPE;
    stSql                           VARCHAR := '';
    stSqlWhere                      VARCHAR := '';
    stSqlAux                        VARCHAR;
    inCodTipoAtributo               INTEGER;
    stTimestampFechamentoPeriodo    VARCHAR;
    stCodigos                       VARCHAR;
    stContagemTempo                 VARCHAR;
    reContratoPensionista              RECORD;
    reRegistro                      RECORD;
    crCursor                        REFCURSOR;
    arConfiguracao                  VARCHAR[];
    arValoresFiltro                 VARCHAR[];
    inIndex                         INTEGER := 1;
    inCodOrganograma                INTEGER;
BEGIN
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidade);
    arConfiguracao := string_to_array(stConfiguracao,',');

    stSql := 'SELECT valor
                FROM administracao.configuracao
               WHERE parametro = '|| quote_literal('dtContagemInicial'||stEntidade) ||' AND exercicio = '|| quote_literal(stExercicio)||' ';
    stContagemTempo := selectIntoVarchar(stSql);

    stSql := '    SELECT contrato_pensionista.*
                       , contrato.registro
                       , pensionista.*';

    IF stTipoFiltro = 'atributo_pensionista' OR stTipoFiltro = 'atributo_pensionista_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');

        --Verifica o tipo do atributo
        --TODO - Pegar o cod_modulo e cod_cadastro vindos do parametro stValoresFiltro. atualmente so é passado o cod_atributo
        stSqlAux := 'SELECT cod_tipo FROM administracao.atributo_dinamico WHERE cod_modulo = 22 AND cod_cadastro = 7 AND cod_atributo = '||arValoresFiltro[2];
        inCodTipoAtributo := selectIntoInteger(stSqlAux);
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || ', atributo_valor_padrao.valor_padrao as valor_atributo';
        ELSE
            stSql := stSql || ', atributo_contrato_pensionista.valor as valor_atributo';
        END IF;
    ELSE
        stSql := stSql || ', ''''::varchar as valor_atributo';
    END IF;

    stSql := stSql || '
                    FROM pessoal'||stEntidade||'.contrato_pensionista
              INNER JOIN pessoal'||stEntidade||'.contrato
                      ON contrato.cod_contrato = contrato_pensionista.cod_contrato
              INNER JOIN pessoal'||stEntidade||'.pensionista
                      ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                     AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
';

    IF stTipoFiltro = 'reg_sub_fun_esp' OR stTipoFiltro = 'reg_sub_fun_esp_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');

        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                                    ON contrato_servidor_regime_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_regime_funcao.cod_regime IN ('||arValoresFiltro[1]||')
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||arValoresFiltro[2]||')
                            INNER JOIN pessoal'||stEntidade||'.contrato_servidor_funcao
                                    ON contrato_servidor_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_funcao.cod_cargo IN ('||arValoresFiltro[3]||')';

        stSqlWhere := stSqlWhere || ' AND contrato_servidor_regime_funcao.timestamp = (  SELECT timestamp
                                                                             FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao as contrato_servidor_regime_funcao_interna
                                                                            WHERE contrato_servidor_regime_funcao_interna.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                                                              AND contrato_servidor_regime_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                         ORDER BY timestamp desc
                                                                            LIMIT 1 )

                                      AND contrato_servidor_sub_divisao_funcao.timestamp = (  SELECT timestamp
                                                                                                FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao as contrato_servidor_sub_divisao_funcao_interna
                                                                                               WHERE contrato_servidor_sub_divisao_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                                 AND contrato_servidor_sub_divisao_funcao_interna.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                                                                            ORDER BY timestamp desc
                                                                                               LIMIT 1
                                                                                           )
                                      AND contrato_servidor_funcao.timestamp = (  SELECT timestamp
                                                                                    FROM pessoal'||stEntidade||'.contrato_servidor_funcao as contrato_servidor_funcao_interna
                                                                                   WHERE contrato_servidor_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                     AND contrato_servidor_funcao_interna.cod_contrato = contrato_servidor_funcao.cod_contrato
                                                                                ORDER BY timestamp desc
                                                                                   LIMIT 1 )';

        IF trim(arValoresFiltro[4]) != '' THEN
            stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                                        ON contrato_servidor_especialidade_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                       AND contrato_servidor_especialidade_funcao.cod_especialidade IN ('||arValoresFiltro[4]||')';

            stSqlWhere := stSqlWhere || ' AND contrato_servidor_especialidade_funcao.timestamp = ( SELECT timestamp
                                                                                                     FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao as contrato_servidor_especialidade_funcao_interna
                                                                                                    WHERE contrato_servidor_especialidade_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                                      AND contrato_servidor_especialidade_funcao_interna.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                                                                                 ORDER BY timestamp desc
                                                                                                    LIMIT 1
                                                                                                  )';

        END IF;
    END IF;

    IF stTipoFiltro = 'contrato'                   OR
       stTipoFiltro = 'contrato_pensionista'       OR
       stTipoFiltro = 'contrato_todos'             OR
       stTipoFiltro = 'cgm_contrato'               OR
       stTipoFiltro = 'cgm_contrato_pensionista'   OR
       stTipoFiltro = 'cgm_contrato_todos'      THEN
        stSql := stSql || ' WHERE contrato.cod_contrato IN ('||stValoresFiltro||')';
    END IF;
    IF stTipoFiltro = 'lotacao' OR stTipoFiltro = 'lotacao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_pensionista_orgao
                                    ON contrato_pensionista_orgao.cod_contrato = contrato.cod_contrato
                                   AND contrato_pensionista_orgao.cod_orgao IN ('||stValoresFiltro||')';

        stSqlWhere := stSqlWhere || ' AND contrato_pensionista_orgao.timestamp = ( SELECT timestamp
                                                                                     FROM pessoal'||stEntidade||'.contrato_pensionista_orgao as contrato_pensionista_orgao_interna
                                                                                    WHERE contrato_pensionista_orgao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                      AND contrato_pensionista_orgao_interna.cod_contrato = contrato_pensionista_orgao.cod_contrato
                                                                                 ORDER BY timestamp desc
                                                                                    LIMIT 1
                                                                                  )';

    END IF;
    IF stTipoFiltro = 'local' OR stTipoFiltro = 'local_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_local
                                    ON contrato_servidor_local.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_local.cod_local IN ('||stValoresFiltro||')';

        stSqlWhere := stSqlWhere || ' AND contrato_servidor_local.timestamp = (  SELECT timestamp
                                                                                   FROM pessoal'||stEntidade||'.contrato_servidor_local as contrato_servidor_local_interna
                                                                                  WHERE contrato_servidor_local_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                    AND contrato_servidor_local_interna.cod_contrato = contrato_servidor_local.cod_contrato
                                                                               ORDER BY timestamp desc
                                                                                  LIMIT 1
                                                                                  )';

    END IF;
--     IF stTipoFiltro = 'sub_divisao' OR stTipoFiltro = 'sub_divisao_grupo' THEN
--         stSql := stSql || ' WHERE contrato_servidor.cod_sub_divisao IN ('||stValoresFiltro||')';
--     END IF;
    IF stTipoFiltro = 'sub_divisao_funcao' OR stTipoFiltro = 'sub_divisao_funcao_grupo' THEN
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                                    ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||stValoresFiltro||')';

        stSqlWhere := stSqlWhere || ' AND contrato_servidor_sub_divisao_funcao.timestamp = ( SELECT timestamp
                                                                                               FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao as contrato_servidor_sub_divisao_funcao_interna
                                                                                              WHERE contrato_servidor_sub_divisao_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                                AND contrato_servidor_sub_divisao_funcao_interna.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                                                                           ORDER BY timestamp desc
                                                                                              LIMIT 1
                                                                                            )';

    END IF;

    IF stTipoFiltro = 'atributo_pensionista' OR stTipoFiltro = 'atributo_pensionista_grupo' THEN
        arValoresFiltro := string_to_array(stValoresFiltro,'#');
        stSql := stSql || ' INNER JOIN pessoal'||stEntidade||'.atributo_contrato_pensionista
                                    ON atributo_contrato_pensionista.cod_contrato = contrato.cod_contrato
                                   AND atributo_contrato_pensionista.cod_atributo = '||arValoresFiltro[2];
        
        IF arValoresFiltro[1] = '1' THEN
            stSql := stSql || '        AND atributo_contrato_pensionista.valor IN ('||arValoresFiltro[3]||')';
        ELSE
            stSql := stSql || '        AND atributo_contrato_pensionista.valor = '''||arValoresFiltro[3]||'''';
        END IF;

        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql || '
                            INNER JOIN administracao.atributo_valor_padrao
                                    ON atributo_valor_padrao.cod_modulo = atributo_contrato_pensionista.cod_modulo
                                   AND atributo_valor_padrao.cod_cadastro = atributo_contrato_pensionista.cod_cadastro
                                   AND atributo_valor_padrao.cod_atributo = atributo_contrato_pensionista.cod_atributo
                                   AND atributo_valor_padrao.cod_valor = atributo_contrato_pensionista.valor';
        END IF;

        stSqlWhere := stSqlWhere || ' AND atributo_contrato_pensionista.timestamp = (  SELECT timestamp
                                                                                         FROM pessoal'||stEntidade||'.atributo_contrato_pensionista as atributo_contrato_pensionista_interna
                                                                                        WHERE atributo_contrato_pensionista.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                          AND atributo_contrato_pensionista_interna.cod_contrato = atributo_contrato_pensionista.cod_contrato
                                                                                          AND atributo_contrato_pensionista_interna.cod_atributo = atributo_contrato_pensionista.cod_atributo
                                                                                     ORDER BY timestamp desc
                                                                                        LIMIT 1
                                                                                        )';
    END IF;

    stSql := stSql||regexp_replace(stSqlWhere, '^ AND', ' WHERE');
    stSqlWhere := '';

    FOR reContratoPensionista IN EXECUTE stSql LOOP
        rwContratoPensionista.cod_contrato           := reContratoPensionista.cod_contrato;
        rwContratoPensionista.registro               := reContratoPensionista.registro;
        rwContratoPensionista.cod_contrato_cedente   := reContratoPensionista.cod_contrato_cedente;
        rwContratoPensionista.cod_dependencia        := reContratoPensionista.cod_dependencia;
        rwContratoPensionista.cod_pensionista        := reContratoPensionista.cod_pensionista;
        rwContratoPensionista.num_beneficio          := reContratoPensionista.num_beneficio;
        rwContratoPensionista.percentual_pagamento   := reContratoPensionista.percentual_pagamento;
        rwContratoPensionista.dt_inicio_beneficio    := reContratoPensionista.dt_inicio_beneficio;
        rwContratoPensionista.dt_encerramento        := reContratoPensionista.dt_encerramento;
        rwContratoPensionista.motivo_encerramento    := reContratoPensionista.motivo_encerramento;
        rwContratoPensionista.cod_profissao          := reContratoPensionista.cod_profissao;
        rwContratoPensionista.numcgm                 := reContratoPensionista.numcgm;
        rwContratoPensionista.cod_grau               := reContratoPensionista.cod_grau;

        rwContratoPensionista.valor_atributo         := reContratoPensionista.valor_atributo;

        WHILE arConfiguracao[inIndex] IS NOT NULL LOOP
            --DADOS DA TABELA pessoal.contrato_pensionista_conta_salario
            IF arConfiguracao[inIndex] = 'cs' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_conta_salario.cod_agencia as cod_agencia_salario
                                   , contrato_pensionista_conta_salario.cod_banco as cod_banco_salario
                                   , contrato_pensionista_conta_salario.nr_conta as nr_conta_salario
                                   , banco.num_banco as num_banco_salario
                                   , banco.nom_banco as nom_banco_salario
                                   , agencia.num_agencia as num_agencia_salario
                                   , agencia.nom_agencia as nom_agencia_salario
                                FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario
                          INNER JOIN monetario.agencia
                                  ON agencia.cod_banco = contrato_pensionista_conta_salario.cod_banco
                                 AND agencia.cod_agencia = contrato_pensionista_conta_salario.cod_agencia
                          INNER JOIN monetario.banco
                                  ON banco.cod_banco = contrato_pensionista_conta_salario.cod_banco
                               WHERE contrato_pensionista_conta_salario.cod_contrato = '||reContratoPensionista.cod_contrato||'
                                 AND contrato_pensionista_conta_salario.timestamp = ( SELECT timestamp
                                                                                        FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario as contrato_pensionista_conta_salario_interna
                                                                                       WHERE contrato_pensionista_conta_salario_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                         AND contrato_pensionista_conta_salario_interna.cod_contrato = contrato_pensionista_conta_salario.cod_contrato
                                                                                    ORDER BY timestamp DESC
                                                                                       LIMIT 1 )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_agencia_salario     := reRegistro.cod_agencia_salario;
                rwContratoPensionista.cod_banco_salario       := reRegistro.cod_banco_salario;
                rwContratoPensionista.nr_conta_salario        := reRegistro.nr_conta_salario;
                rwContratoPensionista.num_banco_salario       := reRegistro.num_banco_salario;
                rwContratoPensionista.nom_banco_salario       := reRegistro.nom_banco_salario;
                rwContratoPensionista.num_agencia_salario     := reRegistro.num_agencia_salario;
                rwContratoPensionista.nom_agencia_salario     := reRegistro.nom_agencia_salario;
            END IF;

            --DADOS DA TABELA pessoal.contrato_pensionista_previdencia
            IF arConfiguracao[inIndex] = 'pr' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_previdencia.cod_previdencia
                                FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                               WHERE contrato_pensionista_previdencia.timestamp = (  SELECT timestamp
                                                                                       FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia as contrato_pensionista_previdencia_interna
                                                                                      WHERE contrato_pensionista_previdencia_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                        AND contrato_pensionista_previdencia_interna.cod_contrato = contrato_pensionista_previdencia.cod_contrato
                                                                                   ORDER BY timestamp DESC
                                                                                      LIMIT 1 )
                                 AND contrato_pensionista_previdencia.cod_contrato = '||reContratoPensionista.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_previdencia  := reRegistro.cod_previdencia;
            END IF;

            --DADOS DA TABELA pessoal.contrato_pensionista_processo
            IF arConfiguracao[inIndex] = 'pro' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_processo.cod_processo
                                FROM pessoal'||stEntidade||'.contrato_pensionista_processo
                               WHERE contrato_pensionista_processo.cod_contrato = '||reContratoPensionista.cod_contrato;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_processo  := reRegistro.cod_processo;
            END IF;

            --DADOS DA TABELA pessoal.contrato_pensionista_orgao
            IF arConfiguracao[inIndex] = 'o' OR arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_pensionista_orgao.cod_orgao
                                   , recuperadescricaoorgao(contrato_pensionista_orgao.cod_orgao,'''||stTimestampFechamentoPeriodo||''') as descricao
                                FROM pessoal'||stEntidade||'.contrato_pensionista_orgao
                               WHERE contrato_pensionista_orgao.cod_contrato = '||reContratoPensionista.cod_contrato||'
                                 AND contrato_pensionista_orgao.timestamp = ( SELECT timestamp
                                                                                FROM pessoal'||stEntidade||'.contrato_pensionista_orgao as contrato_pensionista_orgao_interna
                                                                               WHERE contrato_pensionista_orgao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                 AND contrato_pensionista_orgao_interna.cod_contrato = contrato_pensionista_orgao.cod_contrato
                                                                            ORDER BY timestamp DESC
                                                                               LIMIT 1 )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_orgao  := reRegistro.cod_orgao;
                rwContratoPensionista.desc_orgao := reRegistro.descricao;

                --DADOS DA TABELA organograma.fn_consulta_orgao
                IF arConfiguracao[inIndex] = 'oo' OR arConfiguracao[inIndex] = 'all' THEN
                    inCodOrganograma := selectIntoInteger('SELECT cod_organograma FROM organograma.orgao_nivel WHERE cod_orgao = '||reRegistro.cod_orgao);
                    stSql := 'SELECT organograma.fn_consulta_orgao('||inCodOrganograma||','||reRegistro.cod_orgao||')';
                    rwContratoPensionista.orgao := selectIntoVarchar(stSql);
                END IF;
            END IF;

            --DADOS DA TABELA pessoal'||stEntidade||'.contrato_servidor_local
            IF arConfiguracao[inIndex] = 'l' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_local.cod_local
                                   , local.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_local
                          INNER JOIN organograma.local
                                  ON local.cod_local = contrato_servidor_local.cod_local
                               WHERE contrato_servidor_local.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_local.timestamp = ( SELECT timestamp
                                                                             FROM pessoal'||stEntidade||'.contrato_servidor_local as contrato_servidor_local_interna
                                                                            WHERE contrato_servidor_local_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                              AND contrato_servidor_local_interna.cod_contrato = contrato_servidor_local.cod_contrato
                                                                         ORDER BY timestamp DESC
                                                                            LIMIT 1 ) ';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_local  := reRegistro.cod_local;
                rwContratoPensionista.desc_local := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.pensionista_cid
            IF arConfiguracao[inIndex] = 'cid' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT pensionista_cid.cod_cid
                                   , pensionista_cid.data_laudo
                                FROM pessoal'||stEntidade||'.pensionista_cid
                               WHERE pensionista_cid.cod_pensionista = '||reContratoPensionista.cod_pensionista;
                
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                
                rwContratoPensionista.cod_cid  := reRegistro.cod_cid;
                rwContratoPensionista.data_laudo  := reRegistro.data_laudo;
            END IF;

            --DADOS DA TABELA cgm
            IF arConfiguracao[inIndex] = 'cgm' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT sw_cgm.nom_cgm
                                   , sw_cgm_pessoa_fisica.servidor_pis_pasep
                                   , sw_cgm_pessoa_fisica.rg
                                   , cpf
                                   , sw_cgm_pessoa_fisica.dt_nascimento
                                FROM sw_cgm
                          INNER JOIN sw_cgm_pessoa_fisica
                                  ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                               WHERE sw_cgm.numcgm = '||reContratoPensionista.numcgm;
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.nom_cgm              := reRegistro.nom_cgm;
                rwContratoPensionista.rg                   := reRegistro.rg;
                rwContratoPensionista.cpf                  := reRegistro.cpf;
                rwContratoPensionista.dt_nascimento        := reRegistro.dt_nascimento;
            END IF;

            --################################################################
            --Daqui pra baixo pega informações do servidor
            --através do cod_contrato_cedente

            --DADOS DA TABELA pessoal.contrato_servidor_regime_funcao
            IF arConfiguracao[inIndex] = 'rf' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_regime_funcao.cod_regime as cod_regime_funcao
                                   , regime.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao
                          INNER JOIN pessoal'||stEntidade||'.regime
                                  ON regime.cod_regime = contrato_servidor_regime_funcao.cod_regime
                               WHERE contrato_servidor_regime_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_regime_funcao.timestamp = ( SELECT timestamp
                                                                                   FROM pessoal'||stEntidade||'.contrato_servidor_regime_funcao as contrato_servidor_regime_funcao_interna
                                                                                  WHERE contrato_servidor_regime_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                    AND contrato_servidor_regime_funcao_interna.cod_contrato = contrato_servidor_regime_funcao.cod_contrato
                                                                               ORDER BY timestamp DESC
                                                                                  LIMIT 1 )';
                                                                                  
                raise notice '%', stSql;
                
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_regime_funcao  := reRegistro.cod_regime_funcao;
                rwContratoPensionista.desc_regime_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_sub_divisao_funcao
            IF arConfiguracao[inIndex] = 'sf' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao as cod_sub_divisao_funcao
                                   , sub_divisao.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao
                          INNER JOIN pessoal'||stEntidade||'.sub_divisao
                                  ON sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                               WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_sub_divisao_funcao.timestamp = ( SELECT timestamp
                                                                                          FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao as contrato_servidor_sub_divisao_funcao_interna
                                                                                         WHERE contrato_servidor_sub_divisao_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                           AND contrato_servidor_sub_divisao_funcao_interna.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                                                                                      ORDER BY timestamp DESC
                                                                                         LIMIT 1 ) ';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_sub_divisao_funcao  := reRegistro.cod_sub_divisao_funcao;
                rwContratoPensionista.desc_sub_divisao_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_funcao
            IF arConfiguracao[inIndex] = 'f' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_funcao.cod_cargo as cod_funcao
                                   , cargo.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                          INNER JOIN pessoal'||stEntidade||'.cargo
                                  ON cargo.cod_cargo = contrato_servidor_funcao.cod_cargo
                               WHERE contrato_servidor_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_funcao.timestamp = ( SELECT timestamp
                                                                              FROM pessoal'||stEntidade||'.contrato_servidor_funcao as contrato_servidor_funcao_interna
                                                                             WHERE contrato_servidor_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                               AND contrato_servidor_funcao_interna.cod_contrato = contrato_servidor_funcao.cod_contrato
                                                                          ORDER BY timestamp DESC
                                                                             LIMIT 1 )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_funcao  := reRegistro.cod_funcao;
                rwContratoPensionista.desc_funcao := reRegistro.descricao;


                stSql := ' SELECT * FROM
                         (   SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.cbo_cargo
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = cbo_cargo.cod_cargo
                               AND cbo_cargo.timestamp = ( SELECT timestamp
                                                             FROM pessoal'||stEntidade||'.cbo_cargo as cbo_cargo_interna
                                                            WHERE cbo_cargo_interna.cod_cargo = cbo_cargo.cod_cargo
                                                         ORDER BY timestamp desc
                                                            LIMIT 1 )
                               AND cbo_cargo.cod_cbo = cbo.cod_cbo
                            UNION
                            SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.especialidade
                                 , pessoal'||stEntidade||'.cbo_especialidade
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = especialidade.cod_cargo
                               AND especialidade.cod_especialidade = cbo_especialidade.cod_especialidade
                               AND cbo_especialidade.timestamp = (  SELECT timestamp
                                                                      FROM pessoal'||stEntidade||'.cbo_especialidade as cbo_especialidade_interna
                                                                     WHERE cbo_especialidade_interna.cod_especialidade = cbo_especialidade.cod_especialidade
                                                                  ORDER BY timestamp desc
                                                                      LIMIT 1 )
                               AND cbo_especialidade.cod_cbo = cbo.cod_cbo) as funcao
                    WHERE funcao.cod_cargo = '||reRegistro.cod_funcao;


                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.desc_cbo_funcao := reRegistro.descricao;
            END IF;

            --DADOS DA TABELA pessoal.contrato_servidor_especialidade_funcao
            IF arConfiguracao[inIndex] = 'ef' OR arConfiguracao[inIndex] = 'all' THEN
                stSql := '    SELECT contrato_servidor_especialidade_funcao.cod_especialidade as cod_especialidade_funcao
                                   , especialidade.descricao
                                FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                          INNER JOIN pessoal'||stEntidade||'.especialidade
                                  ON especialidade.cod_especialidade = contrato_servidor_especialidade_funcao.cod_especialidade
                               WHERE contrato_servidor_especialidade_funcao.cod_contrato = '||reContratoPensionista.cod_contrato_cedente||'
                                 AND contrato_servidor_especialidade_funcao.timestamp = ( SELECT timestamp
                                                                                          FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao as contrato_servidor_especialidade_funcao_interna
                                                                                         WHERE contrato_servidor_especialidade_funcao_interna.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                                                                                           AND contrato_servidor_especialidade_funcao_interna.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato
                                                                                           ORDER BY timestamp desc
                                                                                           LIMIT 1
                                                                                       )';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO reRegistro;
                CLOSE crCursor;
                rwContratoPensionista.cod_especialidade_funcao  := reRegistro.cod_especialidade_funcao;
                rwContratoPensionista.desc_especialidade_funcao := reRegistro.descricao;
            END IF;


            inIndex := inIndex + 1;
        END LOOP;
        inIndex := 1;

        RETURN NEXT rwContratoPensionista;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';


----------------
-- Ticket #19512
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_type
      WHERE typname = 'novoRelatorioFichaFinanceira';
    IF FOUND THEN
        DROP TYPE novoRelatorioFichaFinanceira CASCADE;
    END IF;

    CREATE TYPE novoRelatorioFichaFinanceira AS (
        cod_periodo_movimentacao    INTEGER,
        cod_configuracao            INTEGER,
        cod_complementar            INTEGER,
        descricao_periodo           VARCHAR,
        descricao_configuracao      VARCHAR,
        codigo_evento               VARCHAR,
        descricao_evento            VARCHAR,
        natureza_evento             VARCHAR,
        desdobramento               VARCHAR,
        quantidade                  NUMERIC,
        proventos                   NUMERIC,
        descontos                   NUMERIC,
        valor                       NUMERIC,
        ordem_por_natureza          INTEGER
    );

END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();


---------------------------------------------------------
-- CORRECAO DE PK EM folhapagamento.atributo_padrao_valor
---------------------------------------------------------

ALTER TABLE folhapagamento.atributo_padrao_valor DROP CONSTRAINT pk_atributo_padrao_valor;
ALTER TABLE folhapagamento.atributo_padrao_valor ADD  CONSTRAINT pk_atributo_padrao_valor PRIMARY KEY (cod_modulo, cod_cadastro, cod_atributo, cod_padrao, timestamp_padrao);


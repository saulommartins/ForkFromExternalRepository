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
* $Id: GT_1975.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.97.5
*/

----------------
-- Ticket #15129
----------------

CREATE TABLE divida.remissao_processo (
    cod_inscricao       INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_processo        INTEGER         NOT NULL,
    ano_exercicio       CHAR(4)         NOT NULL,
    CONSTRAINT pk_remissao_processo     PRIMARY KEY                         (cod_inscricao, exercicio),
    CONSTRAINT fk_remissao_processo_1   FOREIGN KEY                         (cod_inscricao, exercicio)
                                        REFERENCES divida.divida_remissao   (cod_inscricao, exercicio),
    CONSTRAINT fk_remissao_processo_2   FOREIGN KEY                         (cod_processo, ano_exercicio)
                                        REFERENCES sw_processo              (cod_processo, ano_exercicio)
);

GRANT ALL ON divida.remissao_processo TO GROUP urbem;


----------------
-- Ticket #15057
----------------

ALTER TABLE fiscalizacao.notificacao_fiscalizacao ADD COLUMN num_notificacao INTEGER;
ALTER TABLE fiscalizacao.notificacao_fiscalizacao ADD COLUMN exercicio       CHAR(4);

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSql       VARCHAR;
    reRecord    RECORD;
    inCount     INTEGER := 1;
BEGIN
    stSql := '    SELECT *
                    FROM fiscalizacao.notificacao_fiscalizacao
                ORDER BY timestamp;
             ';

    FOR reRecord IN EXECUTE stSql LOOP

        UPDATE fiscalizacao.notificacao_fiscalizacao
           SET num_notificacao = inCount
             , exercicio       = EXTRACT (year FROM reRecord.timestamp)
         WHERE cod_processo    = reRecord.cod_processo;

    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION Manutencao();

ALTER TABLE fiscalizacao.notificacao_fiscalizacao ALTER COLUMN num_notificacao SET NOT NULL;
ALTER TABLE fiscalizacao.notificacao_fiscalizacao ALTER COLUMN exercicio       SET NOT NULL;


----------------
-- Ticket #15153
----------------

INSERT INTO administracao.configuracao
          ( cod_modulo
          , exercicio
          , parametro
          , valor
          )
     VALUES ( 25
          , '2009'
          , 'baixa_manual_divida_vencida'
          , '0'
          );


----------------
-- Ticket #14978
----------------

    ALTER TABLE arrecadacao.nota_avulsa ADD COLUMN observacao TEXT;


----------------
-- Ticket #15129
----------------

INSERT
  INTO administracao.tipo_documento
     ( cod_tipo_documento
     , descricao
     )
VALUES
     ( 7
     , 'Certidão de Remissão'
     );

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stAux   VARCHAR;
BEGIN
    SELECT valor
      INTO stAux
      FROM administracao.configuracao
     WHERE parametro  = 'cnpj'
       AND exercicio  = '2009'
       AND cod_modulo = 2
       AND valor      = '13805528000180';

    IF FOUND THEN
        INSERT
          INTO administracao.modelo_documento
        VALUES
             ( ( SELECT MAX (cod_documento) + 1 FROM administracao.modelo_documento )
             , 'Certidão de Remissão DA'
             , 'certidaoDARemissaoMata.agt'
             , 7
             );
        INSERT
          INTO administracao.arquivos_documento
        VALUES
             ( ( SELECT MAX (cod_arquivo) + 1 FROM administracao.arquivos_documento )
             , 'certidaoDARemissaoMata.odt'
             , '7afe3f8ca861bb7ae2c1d3f1b3409d05'
             , true
             );
        INSERT
          INTO administracao.modelo_arquivos_documento
        VALUES
             ( 1634
             , ( SELECT MAX (cod_documento) FROM administracao.modelo_documento )
             , ( SELECT MAX (cod_arquivo)   FROM administracao.arquivos_documento )
             , true
             , true
             , 7
             );
        INSERT
          INTO administracao.modelo_arquivos_documento
        VALUES
             ( 1635
             , ( SELECT MAX (cod_documento) FROM administracao.modelo_documento )
             , ( SELECT MAX (cod_arquivo)   FROM administracao.arquivos_documento )
             , true
             , true
             , 7
             );
        INSERT
          INTO administracao.modelo_arquivos_documento
        VALUES
             ( 1639
             , ( SELECT MAX (cod_documento) FROM administracao.modelo_documento )
             , ( SELECT MAX (cod_arquivo)   FROM administracao.arquivos_documento )
             , true
             , true
             , 7
             );
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


-----------------------------------------------------
-- INSERINDO MODELO DE CARNE GENERICO P/ DIVIDA ATIVA
-----------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

    inMaxModelo         INTEGER;

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE parametro  = 'cnpj'
        AND exercicio  = '2009'
        AND cod_modulo = 2
        AND valor      = '13805528000180'
         OR valor      = '94068418000184';

    IF NOT FOUND THEN

        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE nom_arquivo = 'RCarneDividaUrbem.class.php';

        IF NOT FOUND THEN

            SELECT COALESCE (MAX(cod_modelo) + 1,1)
              INTO inMaxModelo
              FROM arrecadacao.modelo_carne;

            INSERT
              INTO arrecadacao.modelo_carne
                 ( cod_modelo
                 , nom_modelo
                 , nom_arquivo
                 , cod_modulo
                 , capa_primeira_folha
                 )
            VALUES 
                 ( inMaxModelo
                 , 'Carnê Divida - Urbem'
                 , 'RCarneDividaUrbem.class.php'
                 , 33
                 , false
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES 
                 ( inMaxModelo
                 , 1755
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES 
                 ( inMaxModelo
                 , 1648
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 1849
                 );

        END IF;

        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE nom_arquivo = 'RCarneIptuUrbem.class.php';

        IF NOT FOUND THEN

            SELECT COALESCE (MAX(cod_modelo) + 1,1)
              INTO inMaxModelo
              FROM arrecadacao.modelo_carne;

            INSERT
              INTO arrecadacao.modelo_carne
                 ( cod_modelo
                 , nom_modelo
                 , nom_arquivo
                 , cod_modulo
                 , capa_primeira_folha
                 )
            VALUES
                 ( inMaxModelo
                 , 'Carnê IPTU - Urbem'
                 , 'RCarneIptuUrbem.class.php'
                 , 12
                 , false
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 963
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 964
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 978
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 979
                 );

        END IF;

        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE nom_arquivo = 'RCarneItbiUrbem.class.php';

        IF NOT FOUND THEN

            SELECT COALESCE (MAX(cod_modelo) + 1,1)
              INTO inMaxModelo
              FROM arrecadacao.modelo_carne;

            INSERT
              INTO arrecadacao.modelo_carne
                 ( cod_modelo
                 , nom_modelo
                 , nom_arquivo
                 , cod_modulo
                 , capa_primeira_folha
                 )
            VALUES
                 ( inMaxModelo
                 , 'Carnê ITBI - Urbem'
                 , 'RCarneItbiUrbem.class.php'
                 , 12
                 , false
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 978
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 979
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 1672
                 );

        END IF;

        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE nom_arquivo = 'RCarneIssUrbem.class.php';

        IF NOT FOUND THEN

            SELECT COALESCE (MAX(cod_modelo) + 1,1)
              INTO inMaxModelo
              FROM arrecadacao.modelo_carne;

            INSERT
              INTO arrecadacao.modelo_carne
                 ( cod_modelo
                 , nom_modelo
                 , nom_arquivo
                 , cod_modulo
                 , capa_primeira_folha
                 )
            VALUES
                 ( inMaxModelo
                 , 'Carnê ISS - Urbem'
                 , 'RCarneIssUrbem.class.php'
                 , 14
                 , false
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 963
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 964
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 978
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 979
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 1677
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 1678
                 );

        END IF;

        PERFORM 1
           FROM arrecadacao.modelo_carne
          WHERE nom_arquivo = 'RCarneDiversosUrbem.class.php';

        IF NOT FOUND THEN

            SELECT COALESCE (MAX(cod_modelo) + 1,1)
              INTO inMaxModelo
              FROM arrecadacao.modelo_carne;

            INSERT
              INTO arrecadacao.modelo_carne
                 ( cod_modelo
                 , nom_modelo
                 , nom_arquivo
                 , cod_modulo
                 , capa_primeira_folha
                 )
            VALUES
                 ( inMaxModelo
                 , 'Carnê Diversos - Urbem'
                 , 'RCarneDiversosUrbem.class.php'
                 , NULL
                 , false
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 963
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 964
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 978
                 );
            INSERT
              INTO arrecadacao.acao_modelo_carne
                 ( cod_modelo
                 , cod_acao
                 )
            VALUES
                 ( inMaxModelo
                 , 979
                 );

        END IF;

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

ALTER TABLE arrecadacao.modelo_carne ALTER COLUMN nom_modelo  SET NOT NULL;
ALTER TABLE arrecadacao.modelo_carne ALTER COLUMN nom_arquivo SET NOT NULL;


------------------------------------------------
-- CONVERSAO DO RELATORIO DE ARRECADACAO P/ BIRT
------------------------------------------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 5
          , 25
          , 4
          , 'relatorio de arrecadacao'
          , 'RelArrecAnal.rptdesign'
          );


----------------
-- Ticket #15154 
----------------

ALTER TABLE fiscalizacao.auto_infracao_outros DROP CONSTRAINT fk_auto_infracao_outros_1;
ALTER TABLE fiscalizacao.auto_infracao_outros DROP CONSTRAINT pk_auto_infracao_outros;
ALTER TABLE fiscalizacao.auto_infracao_multa  DROP CONSTRAINT fk_auto_infracao_multa_1;
ALTER TABLE fiscalizacao.auto_infracao_multa  DROP CONSTRAINT pk_auto_infracao_multa;
ALTER TABLE fiscalizacao.auto_infracao        DROP CONSTRAINT fk_auto_infracao_2;
ALTER TABLE fiscalizacao.auto_infracao        DROP CONSTRAINT pk_auto_infracao;
ALTER TABLE fiscalizacao.notificacao_infracao DROP CONSTRAINT fk_notificacao_infracao_2;
ALTER TABLE fiscalizacao.notificacao_infracao DROP CONSTRAINT pk_notificacao_infracao;
ALTER TABLE fiscalizacao.infracao_penalidade  DROP CONSTRAINT pk_infracao_penalidade;

ALTER TABLE fiscalizacao.infracao_penalidade  ADD  COLUMN timestamp TIMESTAMP NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH TIME ZONE;
ALTER TABLE fiscalizacao.notificacao_infracao ADD  COLUMN timestamp TIMESTAMP;
ALTER TABLE fiscalizacao.auto_infracao        ADD  COLUMN timestamp TIMESTAMP;
ALTER TABLE fiscalizacao.auto_infracao_multa  ADD  COLUMN timestamp TIMESTAMP;
ALTER TABLE fiscalizacao.auto_infracao_outros ADD  COLUMN timestamp TIMESTAMP;

UPDATE fiscalizacao.notificacao_infracao
   SET timestamp = infracao_penalidade.timestamp
  FROM fiscalizacao.infracao_penalidade
 WHERE notificacao_infracao.cod_infracao   = infracao_penalidade.cod_infracao
   AND notificacao_infracao.cod_penalidade = infracao_penalidade.cod_penalidade;

UPDATE fiscalizacao.auto_infracao
   SET timestamp = infracao_penalidade.timestamp
  FROM fiscalizacao.infracao_penalidade
 WHERE auto_infracao.cod_infracao   = infracao_penalidade.cod_infracao
   AND auto_infracao.cod_penalidade = infracao_penalidade.cod_penalidade;

UPDATE fiscalizacao.auto_infracao_multa
   SET timestamp = auto_infracao.timestamp
  FROM fiscalizacao.auto_infracao
 WHERE auto_infracao_multa.cod_infracao   = auto_infracao.cod_infracao
   AND auto_infracao_multa.cod_penalidade = auto_infracao.cod_penalidade;

UPDATE fiscalizacao.auto_infracao_outros
   SET timestamp = auto_infracao.timestamp
  FROM fiscalizacao.auto_infracao
 WHERE auto_infracao_outros.cod_infracao   = auto_infracao.cod_infracao
   AND auto_infracao_outros.cod_penalidade = auto_infracao.cod_penalidade;

ALTER TABLE fiscalizacao.infracao_penalidade  ADD  CONSTRAINT pk_infracao_penalidade    PRIMARY KEY (cod_infracao, cod_penalidade, timestamp);
ALTER TABLE fiscalizacao.notificacao_infracao ADD  CONSTRAINT pk_notificacao_infracao   PRIMARY KEY (cod_processo, cod_infracao, cod_penalidade, timestamp);
ALTER TABLE fiscalizacao.notificacao_infracao ADD  CONSTRAINT fk_notificacao_infracao_2 FOREIGN KEY (cod_infracao, cod_penalidade, timestamp)
                                                                                        REFERENCES fiscalizacao.infracao_penalidade(cod_infracao, cod_penalidade, timestamp);
ALTER TABLE fiscalizacao.auto_infracao        ADD  CONSTRAINT pk_auto_infracao          PRIMARY KEY (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao, timestamp);
ALTER TABLE fiscalizacao.auto_infracao        ADD  CONSTRAINT fk_auto_infracao_2        FOREIGN KEY (cod_penalidade, cod_infracao, timestamp)
                                                                                        REFERENCES fiscalizacao.infracao_penalidade(cod_penalidade, cod_infracao, timestamp);
ALTER TABLE fiscalizacao.auto_infracao_multa  ADD  CONSTRAINT pk_auto_infracao_multa    PRIMARY KEY (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao, timestamp);
ALTER TABLE fiscalizacao.auto_infracao_multa  ADD  CONSTRAINT fk_auto_infracao_multa_1  FOREIGN KEY (cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao, timestamp)
                                                                                        REFERENCES fiscalizacao.auto_infracao(cod_processo, cod_auto_fiscalizacao, cod_penalidade, cod_infracao, timestamp);
ALTER TABLE fiscalizacao.auto_infracao_outros ADD  CONSTRAINT pk_auto_infracao_outros   PRIMARY KEY (cod_processo, cod_auto_fiscalizacao, cod_infracao, cod_penalidade, timestamp);
ALTER TABLE fiscalizacao.auto_infracao_outros ADD  CONSTRAINT fk_auto_infracao_outros_1 FOREIGN KEY                          (cod_processo, cod_auto_fiscalizacao, cod_infracao, cod_penalidade, timestamp)
                                                                                        REFERENCES fiscalizacao.auto_infracao(cod_processo, cod_auto_fiscalizacao, cod_infracao, cod_penalidade, timestamp);


CREATE TABLE fiscalizacao.penalidade_baixa (
    cod_penalidade      INTEGER         NOT NULL,
    timestamp_inicio    TIMESTAMP       NOT NULL NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    timestamp_termino   TIMESTAMP               ,
    motivo              TEXT            NOT NULL,
    CONSTRAINT pk_penalidade_baixa      PRIMARY KEY                         (cod_penalidade, timestamp_inicio),
    CONSTRAINT fk_penalidade_baixa_1    FOREIGN KEY                         (cod_penalidade)
                                        REFERENCES fiscalizacao.penalidade  (cod_penalidade)
);

GRANT ALL ON fiscalizacao.penalidade_baixa TO GROUP urbem;

CREATE TABLE fiscalizacao.infracao_baixa (
    cod_infracao        INTEGER         NOT NULL,
    timestamp_inicio    TIMESTAMP       NOT NULL NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    timestamp_termino   TIMESTAMP               ,
    motivo              TEXT            NOT NULL,
    CONSTRAINT pk_infracao_baixa        PRIMARY KEY                         (cod_infracao, timestamp_inicio),
    CONSTRAINT fk_infracao_baixa_1      FOREIGN KEY                         (cod_infracao)
                                        REFERENCES fiscalizacao.infracao    (cod_infracao)
);

GRANT ALL ON fiscalizacao.infracao_baixa TO GROUP urbem;

UPDATE administracao.acao
   SET ordem = ordem + 1
 WHERE cod_funcionalidade = 422
   AND ordem > 3;

UPDATE administracao.acao
   SET parametro = 'baixar'
     , nom_acao  = 'Baixar Penalidade'
 WHERE cod_acao  = 2287;

UPDATE administracao.acao
   SET parametro = 'baixar'
     , nom_acao  = 'Baixar Infração'
 WHERE cod_acao  = 2290;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2724
          , 422
          , 'FLManterPenalidade.php'
          , 'reativar'
          , 4
          , ''
          , 'Reativar Penalidade'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2725
          , 422
          , 'FLManterInfracao.php'
          , 'reativar'
          , 8
          , ''
          , 'Reativar Infração'
          );

CREATE TABLE fiscalizacao.infracao_baixa_processo (
    cod_infracao            INTEGER             NOT NULL,
    timestamp_inicio        TIMESTAMP           NOT NULL,
    cod_processo            INTEGER             NOT NULL,
    ano_exercicio           CHAR(4)             NOT NULL,
    CONSTRAINT pk_infracao_baixa_processo       PRIMARY KEY                             (cod_infracao, timestamp_inicio),
    CONSTRAINT fk_infracao_baixa_processo_1     FOREIGN KEY                             (cod_infracao, timestamp_inicio)
                                                REFERENCES fiscalizacao.infracao_baixa  (cod_infracao, timestamp_inicio),
    CONSTRAINT fk_infracao_baixa_processo_2     FOREIGN KEY                             (cod_processo, ano_exercicio)
                                                REFERENCES sw_processo                  (cod_processo, ano_exercicio)
);

GRANT ALL ON fiscalizacao.infracao_baixa_processo TO GROUP urbem;

CREATE TABLE fiscalizacao.penalidade_baixa_processo (
    cod_penalidade          INTEGER             NOT NULL,
    timestamp_inicio        TIMESTAMP           NOT NULL,
    cod_processo            INTEGER             NOT NULL,
    ano_exercicio           CHAR(4)             NOT NULL,
    CONSTRAINT pk_penalidade_baixa_processo     PRIMARY KEY                                 (cod_penalidade, timestamp_inicio),
    CONSTRAINT fk_penalidade_baixa_processo_1   FOREIGN KEY                                 (cod_penalidade, timestamp_inicio)
                                                REFERENCES fiscalizacao.penalidade_baixa    (cod_penalidade, timestamp_inicio),
    CONSTRAINT fk_penalidade_baixa_processo_2   FOREIGN KEY                                 (cod_processo, ano_exercicio)
                                                REFERENCES sw_processo                      (cod_processo, ano_exercicio)
);

GRANT ALL ON fiscalizacao.penalidade_baixa_processo TO GROUP urbem;


----------------
-- Ticket #14450
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stCPF   VARCHAR;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio = '2009'
        AND parametro = 'cnpj'  
        AND valor     = '13805528000180';

    IF FOUND THEN

        DELETE
          FROM administracao.modelo_arquivos_documento
         WHERE cod_documento = 131
           AND cod_acao      = 462;

        INSERT
          INTO administracao.modelo_arquivos_documento
             ( cod_acao
             , cod_documento
             , cod_arquivo
             , sistema
             , padrao
             , cod_tipo_documento
             )
        VALUES
             ( 459
             , 131
             , 40
             , true
             , true
             , 1
             );

        INSERT
          INTO administracao.modelo_arquivos_documento
             ( cod_acao
             , cod_documento
             , cod_arquivo
             , sistema
             , padrao
             , cod_tipo_documento
             )
        VALUES
             ( 460
             , 131
             , 40
             , true
             , true
             , 1
             );

        INSERT
          INTO administracao.modelo_arquivos_documento
             ( cod_acao
             , cod_documento
             , cod_arquivo
             , sistema
             , padrao
             , cod_tipo_documento
             )
        VALUES
             ( 464
             , 131
             , 40
             , true
             , true
             , 1
             );

        INSERT
          INTO administracao.modelo_arquivos_documento
             ( cod_acao
             , cod_documento
             , cod_arquivo
             , sistema
             , padrao
             , cod_tipo_documento
             )
        VALUES
             ( 467
             , 131
             , 40
             , true
             , true
             , 1
             );

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();




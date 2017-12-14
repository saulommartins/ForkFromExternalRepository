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
* Versão 1.98.4
*/

-----------------------------------------------------------
-- CORREÇÂO EM TABELAS RENOMEADAS DOS MODULOS != PREFEITURA
-----------------------------------------------------------

CREATE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCountEnt      INTEGER;
    inCountCol      INTEGER;
    stTesteColuna   VARCHAR;
    stDump          VARCHAR;
    stSqlEnt        VARCHAR;
    reRecord        RECORD;
BEGIN

    SELECT COUNT(DISTINCT cod_entidade)
      INTO inCountEnt
      FROM administracao.entidade_rh
     WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                               FROM administracao.configuracao
                              WHERE exercicio = (
                                                   SELECT MAX(exercicio)
                                                     FROM administracao.configuracao
                                                    WHERE parametro = 'migra_orgao'
                                                )
                                AND parametro = 'cod_entidade_prefeitura'
                           )
         ;

    stSqlEnt := '  SELECT DISTINCT cod_entidade
                     FROM administracao.entidade_rh
                    WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                              FROM administracao.configuracao
                                             WHERE exercicio = (
                                                                  SELECT MAX(exercicio)
                                                                    FROM administracao.configuracao
                                                                   WHERE parametro = \'migra_orgao\'
                                                               )
                                               AND parametro = \'cod_entidade_prefeitura\'
                                          )
                        ;
                ';

    FOR reRecord IN EXECUTE stSqlEnt LOOP

        stTesteColuna := 'concurso_' || reRecord.cod_entidade;
        SELECT tablename
          INTO stDump
          FROM pg_tables
         WHERE schemaname = stTesteColuna
           AND tablename  = 'edital'
             ;

        IF NOT FOUND THEN 
            EXECUTE 'ALTER TABLE concurso_'  || reRecord.cod_entidade ||'.concurso   RENAME TO edital;';
        END IF;

        stTesteColuna := 'ponto_' || reRecord.cod_entidade;
        SELECT tablename
          INTO stDump
          FROM pg_tables
         WHERE schemaname = stTesteColuna
           AND tablename  = 'calendario_ponto'
             ;

        IF NOT FOUND THEN 
            EXECUTE 'ALTER TABLE ponto_'     || reRecord.cod_entidade ||'.calendario RENAME TO calendario_ponto;';
        END IF;

        stTesteColuna := 'calendario_' || reRecord.cod_entidade;
        SELECT tablename
          INTO stDump
          FROM pg_tables
         WHERE schemaname = stTesteColuna
           AND tablename  = 'calendario_cadastro'
             ;

        IF NOT FOUND THEN 
            EXECUTE 'ALTER TABLE calendario_'|| reRecord.cod_entidade ||'.calendario RENAME TO calendario_cadastro;';
        END IF;

        stTesteColuna := 'beneficio_' || reRecord.cod_entidade;
        SELECT tablename
          INTO stDump
          FROM pg_tables
         WHERE schemaname = stTesteColuna
           AND tablename  = 'beneficio_cadastro'
             ;

        IF NOT FOUND THEN 
            EXECUTE 'ALTER TABLE beneficio_' || reRecord.cod_entidade ||'.beneficio  RENAME TO beneficio_cadastro;';
        END IF;


        SELECT COUNT(*)
          INTO inCountCol
          FROM pg_attribute
         WHERE attname = 'ferias_proporcionais'
             ;

        IF inCountCol <= inCountEnt THEN

            EXECUTE 'ALTER TABLE pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias ADD   COLUMN ferias_proporcionais INTEGER;';
            EXECUTE 'UPDATE      pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias SET          ferias_proporcionais = 12;';
            EXECUTE 'ALTER TABLE pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias ALTER COLUMN ferias_proporcionais SET NOT NULL;';
            EXECUTE 'ALTER TABLE pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias ALTER COLUMN dias_gozo TYPE NUMERIC(3,1);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES ( 6,  0,  5, 27.5, 11);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES ( 7,  6, 14, 22.0, 11);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES ( 8, 15, 23, 16.5, 11);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES ( 9, 24, 32, 11.0, 11);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (10,  0,  5, 25.0, 10);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (11,  6, 14, 20.0, 10);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (12, 15, 23, 15.0, 10);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (13, 24, 32, 10.0, 10);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (14,  0,  5, 22.5,  9);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (15,  6, 14, 18.0,  9);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (16, 15, 23, 13.5,  9);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (17, 24, 32,  9.0,  9);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (18,  0,  5, 20.0,  8);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (19,  6, 14, 16.0,  8);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (20, 15, 23, 12.0,  8);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (21, 24, 32,  8.0,  8);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (22,  0,  5, 17.5,  7);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (23,  6, 14, 14.0,  7);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (24, 15, 23, 10.5,  7);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (25, 24, 32,  7.0,  7);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (26,  0,  5, 15.0,  6);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (27,  6, 14, 12.0,  6);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (28, 15, 23,  9.0,  6);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (29, 24, 32,  6.0,  6);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (30,  0,  5, 12.5,  5);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (31,  6, 14, 10.0,  5);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (32, 15, 23,  7.5,  5);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (33, 24, 32,  5.0,  5);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (34,  0,  5, 10.0,  4);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (35,  6, 14,  8.0,  4);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (36, 15, 23,  6.0,  4);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (37, 24, 32,  4.0,  4);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (38,  0,  5,  7.5,  3);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (39,  6, 14,  6.0,  3);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (40, 15, 23,  4.5,  3);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (41, 24, 32,  3.0,  3);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (42,  0,  5,  5.0,  2);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (43,  6, 14,  4.0,  2);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (44, 15, 23,  3.0,  2);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (45, 24, 32,  2.0,  2);';
    
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (46,  0,  5,  2.5,  1);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (47,  6, 14,  2.0,  1);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (48, 15, 23,  1.5,  1);';
            EXECUTE 'INSERT INTO pessoal_'|| reRecord.cod_entidade ||'.configuracao_ferias VALUES (49, 24, 32,  1.0,  1);';

        END IF;

    END LOOP;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #16289
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
VALUES
     ( 2777
     , 354
     , 'FMManterExportacaoHSBC.php'
     , 'incluir'
     , 28
     , ''
     , 'Incluir Exportação HSBC'
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
     ( 2778
     , 354
     , 'LSManterExportacaoHSBC.php'
     , 'alterar'
     , 29
     , ''
     , 'Alterar Exportação HSBC'
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
     ( 2779
     , 354
     , 'LSManterExportacaoHSBC.php'
     , 'excluir'
     , 30
     , ''
     , 'Excluir Exportação HSBC'
     );

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_convenio_hsbc(
    cod_convenio        INTEGER     NOT NULL,
    cod_banco           INTEGER     NOT NULL,
    cod_convenio_banco  VARCHAR(20) NOT NULL,
    CONSTRAINT pk_configuracao_convenio_hsbc    PRIMARY KEY (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_convenio_hsbc_1  FOREIGN KEY               (cod_banco)
                                                REFERENCES monetario.banco(cod_banco)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_convenio_hsbc TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_hsbc_conta(
    cod_convenio        INTEGER     NOT NULL,
    cod_banco           INTEGER     NOT NULL,
    cod_agencia         INTEGER     NOT NULL,
    cod_conta_corrente  INTEGER     NOT NULL,
    timestamp           TIMESTAMP   NOT NULL DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE,
    descricao           VARCHAR(60) NOT NULL,
    vigencia            DATE        NOT NULL,
    CONSTRAINT pk_configuracao_hsbc_conta       PRIMARY KEY                               (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp),
    CONSTRAINT fk_configuracao_hsbc_conta_1     FOREIGN KEY                               (cod_convenio, cod_banco)
                                                REFERENCES ima.configuracao_convenio_hsbc (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_hsbc_conta_2     FOREIGN KEY                               (cod_banco, cod_agencia, cod_conta_corrente)
                                                REFERENCES monetario.conta_corrente       (cod_banco, cod_agencia, cod_conta_corrente)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_hsbc_conta TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_hsbc_orgao(
    cod_convenio        INTEGER     NOT NULL,
    cod_banco           INTEGER     NOT NULL,
    cod_agencia         INTEGER     NOT NULL,
    cod_conta_corrente  INTEGER     NOT NULL,
    timestamp           TIMESTAMP   NOT NULL,
    cod_orgao           INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_hsbc_orgao       PRIMARY KEY                               (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_orgao),
    CONSTRAINT fk_configuracao_hsbc_orgao_1     FOREIGN KEY                               (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                REFERENCES ima.configuracao_hsbc_conta    (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp),
    CONSTRAINT fk_configuracao_hsbc_orgao_2     FOREIGN KEY                               (cod_orgao)
                                                REFERENCES organograma.orgao              (cod_orgao)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_hsbc_orgao TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_hsbc_local(
    cod_convenio        INTEGER     NOT NULL,
    cod_banco           INTEGER     NOT NULL,
    cod_agencia         INTEGER     NOT NULL,
    cod_conta_corrente  INTEGER     NOT NULL,
    timestamp           TIMESTAMP   NOT NULL,
    cod_local           INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_hsbc_local       PRIMARY KEY                               (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp, cod_local),
    CONSTRAINT fk_configuracao_hsbc_local_1     FOREIGN KEY                               (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp)
                                                REFERENCES ima.configuracao_hsbc_conta    (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, timestamp),
    CONSTRAINT fk_configuracao_hsbc_local_2     FOREIGN KEY                               (cod_local)
                                                REFERENCES organograma.local              (cod_local)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_hsbc_local TO GROUP urbem;
');

----------------
-- Ticket #16290
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
VALUES
     ( 2781
     , 353
     , 'FLExportacaoBancoHSBC.php'
     , ''
     , 8
     , ''
     , 'Banco HSBC'
     );

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES
     ( to_char(now(),'YYYY')
     , 40
     , 'num_sequencial_arquivo_hsbc'
     , '1'
     );
INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor
     )
VALUES
     ( to_char(now(),'YYYY')
     , 40
     , 'dt_num_sequencial_arquivo_hsbc'
     , to_char(now(),'YYYY-MM-DD')
     );


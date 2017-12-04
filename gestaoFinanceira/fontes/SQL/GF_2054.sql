
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
* Fabio Bertoldi - 20160707
*
*/

----------------
-- Ticket #23978
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'empenho'
        AND tablename  = 'empenho_contrato_aditivos'
          ;
    IF NOT FOUND THEN
        CREATE TABLE empenho.empenho_contrato_aditivos(
            exercicio_empenho   CHAR(4)     NOT NULL,
            cod_entidade        INTEGER     NOT NULL,
            cod_empenho         INTEGER     NOT NULL,
            exercicio_contrato  CHAR(4)     NOT NULL,
            num_contrato        INTEGER     NOT NULL,
            exercicio_aditivo   CHAR(4)     NOT NULL,
            num_aditivo         INTEGER     NOT NULL,
            CONSTRAINT pk_empenho_contrato_aditivos     PRIMARY KEY (exercicio_empenho, cod_entidade, cod_empenho, exercicio_contrato, num_contrato, exercicio_aditivo, num_aditivo),
            CONSTRAINT fk_empenho_contrato_aditivos_1   FOREIGN KEY                            (exercicio_empenho, cod_entidade, cod_empenho)
                                                        REFERENCES empenho.empenho_contrato    (exercicio        , cod_entidade, cod_empenho),
            CONSTRAINT fk_empenho_contrato_aditivos_2   FOREIGN KEY                            (exercicio_contrato, cod_entidade, num_contrato, exercicio_aditivo, num_aditivo)
                                                        REFERENCES licitacao.contrato_aditivos (exercicio_contrato, cod_entidade, num_contrato, exercicio        , num_aditivo)
        );
        GRANT ALL ON empenho.empenho_contrato_aditivos TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23986
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'empenho'
        AND tablename  = 'empenho_convenio_aditivos'
          ;
    IF NOT FOUND THEN
        CREATE TABLE empenho.empenho_convenio_aditivos(
            exercicio_empenho   CHAR(4)     NOT NULL,
            cod_entidade        INTEGER     NOT NULL,
            cod_empenho         INTEGER     NOT NULL,
            exercicio_convenio  CHAR(4)     NOT NULL,
            num_convenio        INTEGER     NOT NULL,
            exercicio_aditivo   CHAR(4)     NOT NULL,
            num_aditivo         INTEGER     NOT NULL,
            CONSTRAINT pk_empenho_convenio_aditivos     PRIMARY KEY (exercicio_empenho, cod_entidade, cod_empenho, exercicio_convenio, num_convenio, exercicio_aditivo, num_aditivo),
            CONSTRAINT fk_empenho_convenio_aditivos_1   FOREIGN KEY                            (exercicio_empenho, cod_entidade, cod_empenho)
                                                        REFERENCES empenho.empenho_convenio    (exercicio        , cod_entidade, cod_empenho),
            CONSTRAINT fk_empenho_convenio_aditivos_2   FOREIGN KEY                            (exercicio_convenio, num_convenio, exercicio_aditivo, num_aditivo)
                                                        REFERENCES licitacao.convenio_aditivos (exercicio_convenio, num_convenio, exercicio        , num_aditivo)
        );
        GRANT ALL ON empenho.empenho_convenio_aditivos TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24127
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN

    PERFORM 1
          FROM pg_class
          JOIN pg_attribute
            ON pg_attribute.attrelid = pg_class.oid
          JOIN pg_namespace
            ON pg_class.relnamespace = pg_namespace.oid
         WHERE pg_namespace.nspname = 'ldo'
           AND pg_class.relname = 'tipo_evolucao_patrimonio_liquido'
           AND pg_attribute.attname = 'exercicio'
           AND pg_attribute.attnum > 0
             ;
    IF NOT FOUND THEN

        ALTER TABLE ldo.tipo_evolucao_patrimonio_liquido ADD   COLUMN exercicio CHAR(4);
        UPDATE      ldo.tipo_evolucao_patrimonio_liquido          SET exercicio = '2012';
        ALTER TABLE ldo.tipo_evolucao_patrimonio_liquido ALTER COLUMN exercicio SET NOT NULL;

        ALTER TABLE ldo.configuracao_evolucao_patrimonio_liquido ADD COLUMN cod_estrutural VARCHAR(30) NOT NULL;

        ALTER TABLE ldo.configuracao_evolucao_patrimonio_liquido DROP CONSTRAINT fk_configucarao_evolucao_patrimonio_liquido_2;
        ALTER TABLE ldo.tipo_evolucao_patrimonio_liquido         DROP CONSTRAINT pk_tipo_evolucao_patrimonio_liquido;

        stSQL := '
                     SELECT exercicio
                       FROM administracao.configuracao
                      WHERE exercicio::INTEGER < 2012
                   GROUP BY exercicio
                   ORDER BY exercicio
                       ;
                 ';
        FOR reRecord IN EXECUTE stSQL LOOP
            INSERT
              INTO ldo.tipo_evolucao_patrimonio_liquido
                 ( cod_tipo
                 , rpps
                 , cod_estrutural
                 , exercicio
                 , descricao
                 )
            SELECT cod_tipo
                 , rpps
                 , cod_estrutural
                 , reRecord.exercicio
                 , descricao
              FROM ldo.tipo_evolucao_patrimonio_liquido
             WHERE exercicio = '2012'
                 ;
        END LOOP;

        stSQL := '
                     SELECT exercicio
                       FROM administracao.configuracao
                      WHERE exercicio::INTEGER > 2012
                   GROUP BY exercicio
                   ORDER BY exercicio
                       ;
                 ';
        FOR reRecord IN EXECUTE stSQL LOOP
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 1, FALSE, '2.3.1.0.0.0.00.00.00.00.00', 'Patrimônio/Capital'            , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 1, FALSE, '2.3.2.0.0.0.00.00.00.00.00', 'Patrimônio/Capital'            , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2, FALSE, '2.3.3.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2, FALSE, '2.3.4.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2, FALSE, '2.3.5.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2, FALSE, '2.3.6.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 3, FALSE, '2.3.7.0.0.0.00.00.00.00.00', 'Resultado Acumulado'           , reRecord.exercicio);

            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 1,  TRUE, '2.3.1.0.0.0.00.00.00.00.00', 'Patrimônio/Capital'            , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 1,  TRUE, '2.3.2.0.0.0.00.00.00.00.00', 'Patrimônio/Capital'            , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2,  TRUE, '2.3.4.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2,  TRUE, '2.3.3.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2,  TRUE, '2.3.5.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 2,  TRUE, '2.3.6.0.0.0.00.00.00.00.00', 'Reservas'                      , reRecord.exercicio);
            INSERT INTO ldo.tipo_evolucao_patrimonio_liquido ( cod_tipo, rpps, cod_estrutural, descricao, exercicio) VALUES ( 3,  TRUE, '2.3.7.0.0.0.00.00.00.00.00', 'Lucros ou Prejuízos Acumulados', reRecord.exercicio);
        END LOOP;

        ALTER TABLE ldo.tipo_evolucao_patrimonio_liquido         ADD CONSTRAINT  pk_tipo_evolucao_patrimonio_liquido PRIMARY KEY (cod_tipo, rpps, cod_estrutural, exercicio);
        ALTER TABLE ldo.configuracao_evolucao_patrimonio_liquido ADD CONSTRAINT fk_configucarao_evolucao_patrimonio_liquido_2  FOREIGN KEY (cod_tipo, rpps, cod_estrutural, exercicio)
                                                                                                                               REFERENCES  ldo.tipo_evolucao_patrimonio_liquido(cod_tipo, rpps, cod_estrutural, exercicio);

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #24184
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
SELECT 3124
     , 63
     , 'FLAnexo16Lei4320.php'
     , 'imprimir'
     , 16
     , 'Demonstrativo da Dívida Fundada Interna/Externa'
     , 'Anexo 16 Lei 4320'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3124
           )
     ;

UPDATE administracao.acao
   SET ordem = ordem + 1
 WHERE cod_funcionalidade = 63
   AND cod_acao          != 3124
   AND ordem              > 15
     ;

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
SELECT 2
     , 9
     , 21
     , 'Demonstrativo da Dívida Fundada Interna/Externa'
     , 'anexo16Lei4320.rptdesign'
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.relatorio
              WHERE cod_gestao    = 2
                AND cod_modulo    = 9
                AND cod_relatorio = 21
           )
     ;


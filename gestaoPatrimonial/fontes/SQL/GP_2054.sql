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
* Fabio Bertoldi - 20160629
*
*/

----------------
-- Ticket #20391
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
SELECT 3119
     , 24
     , 'FLLancamentoContabilReavaliacao.php'
     , 'incluir'
     , 36
     , ''
     , 'Gerar Lançamento Contábil de Reavaliação'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3119
           )
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
SELECT 3120
     , 24
     , 'FLLancamentoContabilReavaliacao.php'
     , 'estornar'
     , 37
     , ''
     , 'Estorno de Lançamento Contábil de Reavaliação'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3120
           )
     ;

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
SELECT 970
     , '2016'
     , 'Vlr. Ref. Lançamento Contábil de Bem por Reavaliação'
     , TRUE
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM contabilidade.historico_contabil
              WHERE cod_historico = 970
           )
     ;

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
SELECT 971
     , '2016'
     , 'Vlr. Ref. Estorno de Lançamento Contábil de Bem por Reavaliação'
     , TRUE
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM contabilidade.historico_contabil
              WHERE cod_historico = 971
           )
     ;


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'contabilidade'
        AND tablename  = 'lancamento_reavaliacao'
          ;
    IF NOT FOUND THEN
        CREATE TABLE contabilidade.lancamento_reavaliacao(
            id                      INTEGER         NOT NULL,
            competencia             CHAR(2)         NOT NULL,
            exercicio               CHAR(4)         NOT NULL,
            cod_entidade            INTEGER         NOT NULL,
            tipo                    CHAR(1)         NOT NULL,
            cod_lote                INTEGER         NOT NULL,
            sequencia               INTEGER         NOT NULL,
            cod_reavaliacao         INTEGER         NOT NULL,
            cod_bem                 INTEGER         NOT NULL,
            estorno                 BOOLEAN         NOT NULL,
            timestamp               TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
            CONSTRAINT pk_lancamento_reavaliacao    PRIMARY KEY (id),
            CONSTRAINT fk_lancamento_reavaliacao_1  FOREIGN KEY                         (exercicio, cod_entidade, tipo, cod_lote, sequencia)
                                                    REFERENCES contabilidade.lancamento (exercicio, cod_entidade, tipo, cod_lote, sequencia),
            CONSTRAINT fk_lancamento_reavaliacao_2  FOREIGN KEY                         (cod_reavaliacao, cod_bem)
                                                    REFERENCES patrimonio.reavaliacao   (cod_reavaliacao, cod_bem)
        );
        GRANT ALL ON contabilidade.lancamento_reavaliacao TO urbem;
    END IF;

    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'contabilidade'
        AND tablename  = 'lancamento_reavaliacao'
          ;
    IF NOT FOUND THEN
        CREATE TABLE patrimonio.grupo_plano_reavaliacao(
            cod_natureza    INTEGER     NOT NULL,
            cod_grupo       INTEGER     NOT NULL,
            exercicio       CHAR(4)     NOT NULL,
            cod_plano       INTEGER     NOT NULL,
            CONSTRAINT pk_grupo_plano_reavaliacao   PRIMARY KEY (cod_natureza, cod_grupo, exercicio, cod_plano),
            CONSTRAINT fk_grupo_plano_reavaliacao_1 FOREIGN KEY (cod_natureza, cod_grupo)
                                                    REFERENCES patrimonio.grupo (cod_natureza, cod_grupo),
            CONSTRAINT fk_grupo_plano_reavaliacao_2 FOREIGN KEY (exercicio, cod_plano)
                                                    REFERENCES contabilidade.plano_analitica (exercicio, cod_plano)
        );
        GRANT ALL ON patrimonio.grupo_plano_reavaliacao TO urbem;
    END IF;

    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'contabilidade'
        AND tablename  = 'lancamento_reavaliacao'
          ;
    IF NOT FOUND THEN
        CREATE TABLE contabilidade.lancamento_reavaliacao_estorno(
            id          INTEGER     NOT NULL,
            timestamp   TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
            CONSTRAINT  pk_lancamento_reavaliacao_estorno   PRIMARY KEY (id, timestamp),
            CONSTRAINT  fk_lancamento_reavaliacao_estorno_1 FOREIGN KEY (id)
                                                            REFERENCES contabilidade.lancamento_reavaliacao (id)
        );
        GRANT ALL ON contabilidade.lancamento_reavaliacao_estorno TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23976
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
      WHERE pg_namespace.nspname = 'licitacao'
        AND pg_class.relname = 'contrato_aditivos'
        AND pg_attribute.attname = 'descricao_alteracao'
        AND pg_attribute.attnum > 0
          ;
    IF NOT FOUND THEN
        ALTER TABLE licitacao.contrato_aditivos ADD COLUMN descricao_alteracao VARCHAR(250) NOT NULL DEFAULT '';
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #23977
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
      WHERE pg_namespace.nspname = 'licitacao'
        AND pg_class.relname = 'rescisao_contrato'
        AND pg_attribute.attname = 'vlr_cancelamento'
        AND pg_attribute.attnum > 0
          ;
    IF NOT FOUND THEN
        ALTER TABLE licitacao.rescisao_contrato ADD   COLUMN vlr_cancelamento NUMERIC(14,2);
        UPDATE      licitacao.rescisao_contrato SET          vlr_cancelamento = 0.00;
        ALTER TABLE licitacao.rescisao_contrato ALTER COLUMN vlr_cancelamento SET NOT NULL;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


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
* $Id:  $
*
* Versão 1.92.6
*/

----------------
-- Ticket #15651
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2752
          , 411
          , 'FLModelosAMF.php'
          , 'demons1'
          , 1
          , ''
          , 'Demonstrativo I'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 6
          , 36
          , 39
          , 'Metas Anuais'
          , 'AMFDemonstrativo1.rptdesign'
          );


---------------------------------------
-- CONFERINDO PERMISSAO P/ SCHEMA tcepb
---------------------------------------

GRANT ALL ON SCHEMA tcepb TO GROUP urbem;


----------------
-- Ticket #15891
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2765
          , 406
          , 'FMManterAnexo3RCL.php'
          , 'incluirAnexo3'
          , 17
          , ''
          , 'Configurar Anexo 3 RCL'
          );

INSERT INTO administracao.configuracao
          ( cod_modulo
          , exercicio
          , parametro
          , valor
          )
     VALUES ( 36
          , '2009'
          , 'deduzir_irrf_anexo_3'
          , 'false'
          );

CREATE TABLE stn.vinculo_stn_receita (
    exercicio       CHAR(4)     NOT NULL,
    cod_receita     INTEGER     NOT NULL,
    CONSTRAINT pk_vinculo_stn_receita   PRIMARY KEY                  (exercicio, cod_receita),
    CONSTRAINT fk_vinculo_stn_receita_1 FOREIGN KEY                  (exercicio, cod_receita)
                                        REFERENCES orcamento.receita (exercicio, cod_receita)
);

GRANT ALL ON stn.vinculo_stn_receita TO GROUP urbem;


----------------
-- Ticket #15989
----------------

CREATE TABLE stn.tipo_vinculo_stn_receita (
    cod_tipo    INTEGER     NOT NULL,
    descricao   VARCHAR(50) NOT NULL,

    CONSTRAINT pk_tipo_vinculo_stn_receita PRIMARY KEY (cod_tipo)
);

INSERT INTO stn.tipo_vinculo_stn_receita VALUES (1,'Compensação Financeira RPPS');
INSERT INTO stn.tipo_vinculo_stn_receita VALUES (2,'Outras Receitas');

ALTER TABLE stn.vinculo_stn_receita DROP CONSTRAINT pk_vinculo_stn_receita;
ALTER TABLE stn.vinculo_stn_receita ADD COLUMN cod_tipo INTEGER NOT NULL;
ALTER TABLE stn.vinculo_stn_receita ADD CONSTRAINT pk_vinculo_stn_receita   PRIMARY KEY (exercicio, cod_receita, cod_tipo);
ALTER TABLE stn.vinculo_stn_receita ADD CONSTRAINT fk_vinculo_stn_receita_2 FOREIGN KEY                             (cod_tipo)
                                                                            REFERENCES stn.tipo_vinculo_stn_receita (cod_tipo);


-------------------------------------------
-- ADICIONADO TABELA pessoal.arquivo_cargos
-------------------------------------------

SELECT atualizarbanco('
CREATE TABLE pessoal.arquivo_cargos (
    cod_cargo           INTEGER         NOT NULL,
    cod_tipo_cargo_tce  INTEGER         NOT NULL,
    periodo             VARCHAR         NOT NULL,
    CONSTRAINT pk_arquivo_cargos    PRIMARY KEY                     (cod_cargo, cod_tipo_cargo_tce),
    CONSTRAINT fk_arquivo_cargos_1  FOREIGN KEY                     (cod_cargo)
                                    REFERENCES pessoal.cargo        (cod_cargo),
    CONSTRAINT fk_arquivo_cargos_2 FOREIGN KEY                      (cod_tipo_cargo_tce)
                                    REFERENCES tcepb.tipo_cargo_tce (cod_tipo_cargo_tce)
);
');

SELECT atualizarbanco('GRANT ALL ON pessoal.arquivo_cargos TO GROUP urbem;');

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
VALUES ( 1
     , 'arquivo_cargos'
     , 1
     );


CREATE OR REPLACE FUNCTION atualizaArquivoCargos(VARCHAR) RETURNS BOOLEAN AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    stSql                           VARCHAR;
    stSqlAux                        VARCHAR;
    stMesAnoReferencia              VARCHAR;
    reRegistro                      RECORD;
    inNumOrgaos                     INTEGER;
    inCount                         INTEGER := 1;
    inCodCargo                      INTEGER;
    inIdentificador                 INTEGER;
    inCodPeriodoMovimentacao        INTEGER;
BEGIN

    FOR inCount IN 1..7 LOOP
        --verifica se a sequence cargos_tce existe
        IF ((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='cargos_tce') IS NOT NULL) THEN
            SELECT NEXTVAL('tcepb.cargos_tce')
              INTO inIdentificador;
        ELSE
            CREATE SEQUENCE tcepb.cargos_tce START 1;
            SELECT NEXTVAL('tcepb.cargos_tce')
              INTO inIdentificador;
        END IF;

        stMesAnoReferencia := '0'||inCount||'2009';

        inCodPeriodoMovimentacao := selectIntoInteger('SELECT cod_periodo_movimentacao
                                                         FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                        WHERE to_char(dt_final, \'mmyyyy\') = '''||stMesAnoReferencia||''' ');
    
        IF (inCodPeriodoMovimentacao IS NOT NULL) THEN
            stSql := '
            CREATE TEMPORARY TABLE tmp_valores_'||inIdentificador||' AS (
                SELECT cod_cargo
                     , descricao
                     , cod_tipo_cargo_tce
                     , escolaridade
                     , cod_cbo
                  FROM ( SELECT cargo.cod_cargo
                              , cargo.descricao
                              , tipo_cargo_tce.cod_tipo_cargo_tce
                              , 0 AS escolaridade
                              , cbo.codigo AS cod_cbo
                              , getVagasOcupadasCargo(sub_divisao.cod_regime, sub_divisao.cod_sub_divisao, cargo.cod_cargo, '||inCodPeriodoMovimentacao||', false, '''||stEntidade||''') AS vagas_ocupadas
                           FROM pessoal'||stEntidade||'.cargo
                           JOIN pessoal'||stEntidade||'.cargo_sub_divisao
                             ON cargo_sub_divisao.cod_cargo = cargo.cod_cargo
                           JOIN pessoal'||stEntidade||'.sub_divisao
                             ON sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                           JOIN pessoal'||stEntidade||'.de_para_tipo_cargo
                             ON de_para_tipo_cargo.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                           JOIN tcepb.tipo_cargo_tce
                             ON tipo_cargo_tce.cod_tipo_cargo_tce = de_para_tipo_cargo.cod_tipo_cargo_tce
                           JOIN pessoal'||stEntidade||'.cbo_cargo
                             ON cbo_cargo.cod_cargo = cargo.cod_cargo
                            AND cbo_cargo.timestamp = ( SELECT MAX(timestamp)
                                                          FROM pessoal'||stEntidade||'.cbo_cargo
                                                         WHERE cbo_cargo.cod_cargo = cargo.cod_cargo
                                                           AND timestamp <= ( SELECT MAX(timestamp)
                                                                                FROM pessoal'||stEntidade||'.cargo_sub_divisao
                                                                               WHERE cargo_sub_divisao.cod_cargo = cargo.cod_cargo
                                                                                 AND timestamp <= ( SELECT MAX(timestamp) AS timestamp
                                                                                                      FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                                                                                                     WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||' ) ) )
                           JOIN pessoal'||stEntidade||'.cbo
                             ON cbo.cod_cbo = cbo_cargo.cod_cbo
                          WHERE NOT EXISTS ( SELECT 1
                                               FROM tcepb.arquivo_cargos
                                              WHERE arquivo_cargos.cod_cargo    = cargo.cod_cargo
                                                AND arquivo_cargos.periodo      <> '''||stMesAnoReferencia||''')
                       GROUP BY cargo.cod_cargo
                              , cargo.descricao
                              , tipo_cargo_tce.cod_tipo_cargo_tce
                              , cargo_sub_divisao.timestamp
                              , cbo.codigo
                              , sub_divisao.cod_regime
                              , sub_divisao.cod_sub_divisao
                       ORDER BY cargo.cod_cargo, cargo_sub_divisao.timestamp
                      ) AS tabela
                  WHERE vagas_ocupadas > 0  
               GROUP BY cod_cargo
                      , descricao
                      , cod_tipo_cargo_tce
                      , escolaridade
                      , cod_cbo
               ORDER BY cod_cargo
            )
            ';
        
            EXECUTE stSql;
        
            EXECUTE 'DELETE FROM pessoal'||stEntidade||'.arquivo_cargos WHERE periodo = '''||stMesAnoReferencia||''' ';
        
            stSql := ' SELECT * FROM tmp_valores_'||inIdentificador;
        
            FOR reRegistro IN EXECUTE stSql LOOP
                inNumOrgaos := selectIntoInteger(' SELECT COUNT(cod_cargo) AS num_cargos
                                                        , cod_cargo
                                                     FROM tmp_valores_'||inIdentificador||'
                                                    WHERE cod_cargo = '||reRegistro.cod_cargo||'
                                                 GROUP BY cod_cargo ');
        
                IF (inNumOrgaos > 1) THEN
                    reRegistro.cod_tipo_cargo_tce := 1;
                END IF;
        
                inCodCargo := selectIntoInteger('SELECT cod_cargo
                                                   FROM pessoal'||stEntidade||'.arquivo_cargos
                                                  WHERE arquivo_cargos.cod_cargo = '||reRegistro.cod_cargo||' ');
        
                IF (inCodCargo IS NULL) THEN
                    stSqlAux := ' 
                        INSERT INTO pessoal'||stEntidade||'.arquivo_cargos ( cod_cargo
                                                                           , cod_tipo_cargo_tce
                                                                           , periodo ) 
                                                                    VALUES ( '||reRegistro.cod_cargo||'
                                                                           , '||reRegistro.cod_tipo_cargo_tce||'
                                                                           , '''||stMesAnoReferencia||''' ) ';
        
                    EXECUTE stSqlAux;
                END IF;
        
            END LOOP;
        END IF;
    END LOOP;

    RETURN true;

    EXECUTE 'DROP TABLE tmp_valores_'||inIdentificador;
    
END;
$$ LANGUAGE 'plpgsql';

SELECT * FROM atualizaArquivoCargos('');
DROP FUNCTION atualizaArquivoCargos(VARCHAR);

DROP TABLE tcepb.arquivo_cargos;


---------------------------------------------------------------------------------------------------------
-- MIGRANDO TABELA tcepb.arquivo_codigo_vantagens_descontos P/ pessoal.arquivo_codigo_vantagens_descontos
---------------------------------------------------------------------------------------------------------

SELECT atualizarbanco('
CREATE TABLE pessoal.arquivo_codigo_vantagens_descontos (
    cod_vantagem_desconto     VARCHAR       NOT NULL,
    periodo                   VARCHAR       NOT NULL,
    CONSTRAINT pk_arquivo_codigo_vantagens_descontos    PRIMARY KEY                      (cod_vantagem_desconto),
    CONSTRAINT fk_arquivo_codigo_vantagens_descontos_1  FOREIGN KEY                      (cod_vantagem_desconto)
                                                        REFERENCES folhapagamento.evento (codigo)
);
');

SELECT atualizarbanco('GRANT ALL ON pessoal.arquivo_codigo_vantagens_descontos TO GROUP urbem;');

INSERT
  INTO pessoal.arquivo_codigo_vantagens_descontos
     ( cod_vantagem_desconto
     , periodo
     )
SELECT cod_vantagem_desconto
     , periodo
  FROM tcepb.arquivo_codigo_vantagens_descontos
     ;

DROP TABLE tcepb.arquivo_codigo_vantagens_descontos;

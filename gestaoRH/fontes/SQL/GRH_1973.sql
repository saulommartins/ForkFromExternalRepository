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
* $Id: GRH_1973.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.97.3
*/

----------------
-- Ticket #15195
----------------

SELECT atualizarBanco ('
CREATE TABLE folhapagamento.configuracao_desdobramento (
    cod_configuracao    INTEGER         NOT NULL,
    desdobramento       CHAR(1)         NOT NULL,
    descricao           VARCHAR(100)    NOT NULL,
    abreviacao          VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_configuracao_desdobramento    PRIMARY KEY                                     (cod_configuracao, desdobramento),
    CONSTRAINT fk_configuracao_desdobramento_1  FOREIGN KEY                                     (cod_configuracao)
                                                REFERENCES folhapagamento.configuracao_evento   (cod_configuracao)
);
');

SELECT atualizarBanco ('GRANT ALL ON folhapagamento.configuracao_desdobramento TO GROUP urbem;');


SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (1, \'I\', \'Adiantamento de 13°\'    , \'Adiant. de 13°\'         );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (1, \'A\', \'Abono\'                  , \'Abono\'                  );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (1, \'F\', \'Férias\'                 , \'Férias\'                 );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (1, \'D\', \'Adiantamento de Férias\' , \'Adiant. de Férias\'      );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (2, \'A\', \'Abono\'                  , \'Abono\'                  );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (2, \'F\', \'Férias\'                 , \'Férias\'                 );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (2, \'D\', \'Adiantamento de Férias\' , \'Adiant. de Férias\'      );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (3, \'A\', \'Adiantamento\'           , \'Adiant.\'                );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (3, \'F\', \'Complemento 13° Salário\', \'Compl. 13° Salário\'     );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (3, \'D\', \'13° Salário\'            , \'13° Salário\'            );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (4, \'S\', \'Saldo Salário\'          , \'Saldo Salário\'          );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (4, \'V\', \'Férias Vencidas\'        , \'Férias Venc.\'           );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (4, \'P\', \'Férias Proporcionais\'   , \'Férias Prop.\'           );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (4, \'D\', \'13° Salário\'            , \'13° Salário\'            );');
SELECT atualizarBanco ('INSERT INTO folhapagamento.configuracao_desdobramento VALUES (4, \'A\', \'Aviso Prévio\'           , \'Aviso Prévio\'           );');

-- Dropando as funções legadas, pois agora recebe mais um parametro, stEntidade
CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM pg_proc
      WHERE proname = LOWER('getDesdobramentoComplementar');
    IF FOUND THEN
        DROP FUNCTION getDesdobramentoComplementar(VARCHAR);
    END IF;

    PERFORM 1
       FROM pg_proc
      WHERE proname = LOWER('getDesdobramentoSalario');
    IF FOUND THEN
        DROP FUNCTION getDesdobramentoSalario(VARCHAR);
    END IF;

    PERFORM 1
       FROM pg_proc
      WHERE proname = LOWER('getDesdobramentoFerias');
    IF FOUND THEN
        DROP FUNCTION getDesdobramentoFerias(VARCHAR);
    END IF;

    PERFORM 1
       FROM pg_proc
      WHERE proname = LOWER('getDesdobramentoDecimo');
    IF FOUND THEN
        DROP FUNCTION getDesdobramentoDecimo(VARCHAR);
    END IF;

    PERFORM 1
       FROM pg_proc
      WHERE proname = LOWER('getDesdobramentoRescisao');
    IF FOUND THEN
        DROP FUNCTION getDesdobramentoRescisao(VARCHAR);
    END IF;

    PERFORM 1
       FROM pg_proc
      WHERE proname = LOWER('getDesdobramentoFolha');
    IF FOUND THEN
        DROP FUNCTION getDesdobramentoFolha(INTEGER,VARCHAR);
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

--------------------------------
-- CORREÇÂO EM ima.banpara_orgao
--------------------------------

ALTER TABLE ima.banpara_orgao DROP CONSTRAINT fk_banpara_orgao_1;


----------------
-- Ticket #15400
----------------

UPDATE administracao.acao
   SET nom_acao = 'Incluir Configuração BanPará'
 WHERE cod_acao = 2186;
UPDATE administracao.acao
   SET nom_acao = 'Alterar Configuração BanPará'
 WHERE cod_acao = 2187;
UPDATE administracao.acao
   SET nom_acao = 'Excluir Configuração BanPará'
 WHERE cod_acao = 2188;

UPDATE administracao.acao
   SET nom_acao = 'Banco BanPará'
 WHERE cod_acao = 2213;

----------------------------------------------------------
-- Ticket #15217,#15394,#15395,#15396,#15397,#15398,#15399
----------------------------------------------------------

CREATE TYPE colunasEventosCalculadosDependentes AS (
    cod_contrato        INTEGER,  
    cod_dependente      INTEGER,  
    cod_evento          INTEGER,  
    codigo              CHARACTER(5) ,
    descricao           CHARACTER(80),
    natureza            CHARACTER(1) ,
    tipo                CHARACTER(1) ,
    fixado              CHARACTER(1) ,
    limite_calculo      BOOLEAN      ,
    apresenta_parcela   BOOLEAN      ,
    evento_sistema      BOOLEAN      ,
    sigla               CHARACTER VARYING(5),
    valor               NUMERIC(15,2),        
    quantidade          NUMERIC(15,2),
    desdobramento       CHARACTER(1),
    desdobramento_texto VARCHAR,
    sequencia           INTEGER,
    desc_sequencia      CHARACTER VARYING(80)
);


----------------
-- Ticket #15432
----------------

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
 VALUES 
      ( 2
      , 'configuracao_desdobramento'
      , 2
      );


-------------------------------------------
-- ADICIONADO RELATORIO Historico de Ferias
-------------------------------------------

INSERT INTO administracao.relatorio 
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio    
     , arquivo )
VALUES (  4        
     , 22        
     , 12         
     , 'Histórico de Férias'
     , 'historicoDeFerias.rptdesign'
    );


---------------------------------------------------
-- ADICIONANDO TIPO colunasRelatorioHistoricoFerias
---------------------------------------------------

CREATE TYPE colunasRelatorioHistoricoFerias AS (
    registro              INTEGER,
    cod_contrato          INTEGER,
    numcgm                INTEGER,
    nom_cgm               VARCHAR,
    dt_inicio_contagem    DATE,
    dt_inicial_aquisitivo DATE,
    dt_final_aquisitivo   DATE,
    dt_inicial_gozo       DATE,
    dt_final_gozo         DATE,
    faltas                INTEGER,
    dias_ferias           INTEGER,
    dias_abono            INTEGER,
    dias                  INTEGER,
    abono                 INTEGER,
    mes_pagamento         VARCHAR,
    folha                 VARCHAR,
    pagar_13              VARCHAR,
    lotacao               VARCHAR,
    cod_lotacao           INTEGER,
    local                 VARCHAR,
    cod_local             INTEGER,
    cod_regime            INTEGER,
    regime                VARCHAR,
    funcao                VARCHAR,
    dt_posse              VARCHAR,
    dt_nomeacao           VARCHAR
);


----------------
-- Ticket #
----------------

SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_cid                      DROP CONSTRAINT fk_tabela_irrf_cid_1;       ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_evento                   DROP CONSTRAINT fk_tabela_irrf_evento_1;    ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_comprovante_rendimento   DROP CONSTRAINT fk_comprovante_rendimento_1;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.faixa_desconto_irrf                  DROP CONSTRAINT fk_faixa_desconto_irrf_1;   ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf                          DROP CONSTRAINT pk_tabela_irrf;             ');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf_evento
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf_comprovante_rendimento
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.faixa_desconto_irrf
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf_cid
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf                          ADD  CONSTRAINT pk_tabela_irrf PRIMARY KEY (cod_tabela, timestamp);                                                                          ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.faixa_desconto_irrf                  ADD  CONSTRAINT fk_faixa_desconto_irrf_1 FOREIGN KEY (cod_tabela, timestamp) REFERENCES folhapagamento.tabela_irrf(cod_tabela, timestamp);   ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_comprovante_rendimento   ADD  CONSTRAINT fk_comprovante_rendimento_1 FOREIGN KEY (timestamp, cod_tabela) REFERENCES folhapagamento.tabela_irrf(timestamp, cod_tabela);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_evento                   ADD  CONSTRAINT fk_tabela_irrf_evento_1 FOREIGN KEY (cod_tabela, timestamp) REFERENCES folhapagamento.tabela_irrf(cod_tabela, timestamp);    ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_cid                      ADD  CONSTRAINT fk_tabela_irrf_cid_1 FOREIGN KEY (cod_tabela, timestamp) REFERENCES folhapagamento.tabela_irrf(cod_tabela, timestamp);       ');

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
* $Id: GRH_1910.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 008.
*/

----------------
-- Ticket #12932     
----------------
INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 414
         , 27
         , 'Bases'
         , 'instancias/bases/'
         , 15
         );
                                
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2268
          , 414
          , 'FMBaseCalculo.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Base'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2269
          , 414
          , 'FLBaseCalculo.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Base'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2270
          , 414
          , 'FLBaseCalculo.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Base'
          );


----------------
-- Ticket #12934
----------------

INSERT INTO administracao.modulo
         ( cod_modulo
         , cod_responsavel
         , nom_modulo
         , nom_diretorio
         , ordem
         , cod_gestao
         )
    VALUES ( 50
         , 0
         , 'Diárias'
         , 'diarias/'
         , 50
         , 4
         );

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 415
         , 50
         , 'Configuração'
         , 'instancias/configuracao/'
         , 1
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2271
          , 415
          , 'FMTipoDiarias.php'
          , 'incluir'
          , 1
          , ''
          , 'Tipos de Diárias'
          );

----------------
-- Ticket #12935
----------------

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 416
         , 50
         , 'Concessão'
         , 'instancias/concessao/'
         , 2
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2272
          , 416
          , 'FLConcederDiarias.php'
          , 'conceder'
          , 1
          , ''
          , 'Conceder Diárias'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2273
          , 416
          , 'FLConcederDiarias.php'
          , 'consultar'
          , 2
          , ''
          , 'Consultar Diárias'
          );


----------------
-- Ticket #12936
----------------

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 417
         , 50
         , 'Relatórios'
         , 'instancias/relatorios/'
         , 50
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2274
          , 417
          , 'FLRelacaoDiarias.php'
          , 'incluir'
          , 1
          , ''
          , 'Relação de Diárias'
          );


----------------
-- Ticket #12937
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 50
         , 1
         , 'Relação de Diárias'
         , 'relacaoDiarias.rptdesign'
         );


----------------
-- Ticket #12933
----------------

select atualizarBanco('
CREATE TABLE folhapagamento.bases(
    cod_base                INTEGER         NOT NULL,
    nom_base                VARCHAR(30)     NOT NULL,
    tipo_base               CHAR(1)         NOT NULL,
    apresentacao_valor      BOOLEAN         NOT NULL,
    insercao_automatica     BOOLEAN         NOT NULL,
    cod_modulo              INTEGER         NOT NULL,
    cod_biblioteca          INTEGER         NOT NULL,
    cod_funcao              INTEGER         NOT NULL,
    CONSTRAINT pk_bases                     PRIMARY KEY                         (cod_base),
    CONSTRAINT fk_bases_1                   FOREIGN KEY                         (cod_modulo, cod_biblioteca, cod_funcao)
                                            REFERENCES administracao.funcao     (cod_modulo, cod_biblioteca, cod_funcao),
    CONSTRAINT chk_bases                    CHECK                               (tipo_base in (\'V\',\'Q\'))
);
');

select atualizarBanco('
CREATE TABLE folhapagamento.bases_evento(
    cod_base                INTEGER         NOT NULL,
    cod_evento              INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_bases_evento              PRIMARY KEY                         (cod_base,cod_evento,timestamp),
    CONSTRAINT fk_bases_evento_1            FOREIGN KEY                         (cod_base)
                                            REFERENCES folhapagamento.bases     (cod_base),
    CONSTRAINT fk_bases_evento_2            FOREIGN KEY                         (cod_evento)
                                            REFERENCES folhapagamento.evento    (cod_evento)
);
');

select atualizarBanco('
CREATE TABLE folhapagamento.bases_evento_criado(
    cod_base                INTEGER         NOT NULL,
    cod_evento              INTEGER         NOT NULL,
    CONSTRAINT pk_bases_evento_criado       PRIMARY KEY                         (cod_base,cod_evento),
    CONSTRAINT fk_bases_evento_criado_1     FOREIGN KEY                         (cod_base)
                                            REFERENCES folhapagamento.bases     (cod_base),
    CONSTRAINT fk_bases_evento_criado_2     FOREIGN KEY                         (cod_evento)
                                            REFERENCES folhapagamento.evento    (cod_evento)
);
');

select atualizarBanco('
GRANT ALL ON folhapagamento.bases                   TO GROUP urbem;
');

select atualizarBanco('
GRANT ALL ON folhapagamento.bases_evento            TO GROUP urbem;
');

select atualizarBanco('
GRANT ALL ON folhapagamento.bases_evento_criado     TO GROUP urbem;
');


----------------
-- Ticket #12938
----------------
select atualizarBanco('CREATE SCHEMA diarias;');

select atualizarBanco('
CREATE TABLE diarias.tipo_diaria(
    cod_tipo                INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    nom_tipo                VARCHAR(50)     NOT NULL,
    valor                   NUMERIC(14,2)   NOT NULL,
    cod_norma               INTEGER         NOT NULL,
    CONSTRAINT pk_tipo_diaria               PRIMARY KEY                         (cod_tipo, timestamp),
    CONSTRAINT fk_tipo_diaria_1             FOREIGN KEY                         (cod_norma)
                                            REFERENCES normas.norma             (cod_norma)
);
');

select atualizarBanco('
CREATE TABLE diarias.tipo_diaria_despesa(
    cod_tipo                INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL,
    cod_conta               INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    CONSTRAINT pk_tipo_diaria_despesa       PRIMARY KEY                         (cod_tipo, timestamp),
    CONSTRAINT fk_tipo_diaria_despesa_1     FOREIGN KEY                         (cod_tipo, timestamp)
                                            REFERENCES diarias.tipo_diaria      (cod_tipo, timestamp),
    CONSTRAINT fk_tipo_diaria_despesa_2     FOREIGN KEY                         (cod_conta, exercicio)
                                            REFERENCES orcamento.conta_despesa  (cod_conta, exercicio)
);
');

select atualizarBanco('
GRANT ALL ON diarias.tipo_diaria            TO GROUP urbem;
');

select atualizarBanco('
GRANT ALL ON diarias.tipo_diaria_despesa    TO GROUP urbem;
');


----------------
-- Ticket #12940
----------------

select atualizarBanco('
CREATE TABLE diarias.diaria(
    cod_diaria              INTEGER         NOT NULL,
    cod_contrato            INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL    DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    cod_tipo                INTEGER         NOT NULL,
    timestamp_tipo          TIMESTAMP       NOT NULL,
    cod_municipio           INTEGER         NOT NULL,
    cod_uf                  INTEGER         NOT NULL,
    cod_norma               INTEGER         NOT NULL,
    numcgm                  INTEGER         NOT NULL,
    dt_inicio               DATE            NOT NULL,
    dt_termino              DATE            NOT NULL,
    quantidade              NUMERIC(14,2)   NOT NULL,
    vl_total                NUMERIC(14,2)   NOT NULL,
    motivo                  TEXT            NOT NULL,
    CONSTRAINT pk_diaria                    PRIMARY KEY                         (cod_diaria, cod_contrato, timestamp),
    CONSTRAINT fk_diaria_1                  FOREIGN KEY                         (cod_contrato)
                                            REFERENCES pessoal.contrato         (cod_contrato),
    CONSTRAINT fk_diaria_2                  FOREIGN KEY                         (cod_tipo, timestamp_tipo)
                                            REFERENCES diarias.tipo_diaria      (cod_tipo, timestamp),
    CONSTRAINT fk_diaria_3                  FOREIGN KEY                         (cod_municipio, cod_uf)
                                            REFERENCES sw_municipio             (cod_municipio, cod_uf),
    CONSTRAINT fk_diaria_4                  FOREIGN KEY                         (cod_norma)
                                            REFERENCES normas.norma             (cod_norma),
    CONSTRAINT fk_diaria_5                  FOREIGN KEY                         (numcgm)
                                            REFERENCES administracao.usuario    (numcgm)
);
');

select atualizarBanco('
GRANT ALL ON diarias.diaria                 TO GROUP urbem;
');


----------------
-- Ticket #12951
----------------

select atualizarBanco('
CREATE TABLE estagio.estagiario_vale_refeicao(
    cgm_instituicao_ensino      INTEGER         NOT NULL, 
    cgm_estagiario              INTEGER         NOT NULL,
    cod_curso                   INTEGER         NOT NULL,
    cod_estagio                 INTEGER         NOT NULL,
    timestamp                   TIMESTAMP       NOT NULL,
    quantidade                  INTEGER         NOT NULL,
    vl_vale                     NUMERIC(14,2)   NOT NULL,
    vl_desconto                 NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_estagiario_vale_refeicao      PRIMARY KEY                                     (cgm_instituicao_ensino, cgm_estagiario, cod_curso, cod_estagio, timestamp),
    CONSTRAINT fk_estagiario_vale_refeicao_1    FOREIGN KEY                                     (cgm_instituicao_ensino, cgm_estagiario, cod_curso, cod_estagio, timestamp)
                                                REFERENCES  estagio.estagiario_estagio_bolsa    (cgm_instituicao_ensino, cgm_estagiario, cod_curso, cod_estagio, timestamp)
 );
');

select atualizarBanco('
GRANT ALL ON estagio.estagiario_vale_refeicao   TO GROUP urbem;
');


----------------
-- Ticket #12953
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 39
         , 2
         , 'Pagamento de Estagiários'
         , 'pagamentoEstagiarios.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 39
         , 3
         , 'Cadastro de Estagiários'
         , 'cadastroEstagiarios.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 27
         , 18
         , 'Eventos Por Contrato'
         , 'eventosPorContrato.rptdesign'
         );


----------------
-- Ticket #13048
----------------

select atualizarBanco('
CREATE TABLE pessoal.atributo_cargo_valor(
    cod_modulo          INTEGER             NOT NULL,
    cod_cadastro        INTEGER             NOT NULL,
    cod_atributo        INTEGER             NOT NULL,
    cod_cargo           INTEGER             NOT NULL,
    timestamp           TIMESTAMP           NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    valor               TEXT                NOT NULL,
    CONSTRAINT pk_atributo_cargo_valor      PRIMARY KEY                                 (cod_modulo, cod_cadastro, cod_atributo, cod_cargo, timestamp),
    CONSTRAINT fk_atributo_cargo_valor_1    FOREIGN KEY                                 (cod_modulo, cod_cadastro, cod_atributo)
                                            REFERENCES administracao.atributo_dinamico  (cod_modulo, cod_cadastro, cod_atributo)
);
');

select atualizarBanco('
GRANT ALL ON pessoal.atributo_cargo_valor TO GROUP urbem;
');

UPDATE administracao.cadastro 
   SET mapeamento   = 'TPessoalAtributoCargoValor' 
 WHERE cod_modulo   = 22 
   AND cod_cadastro = 4;


---------------------------------------------------------------
-- INSERCAO DA FUNCAO pegaListaEventoDaBase COMO FUNCAO EXTERNA - 20080820 - Rafael Garbin
---------------------------------------------------------------


   --
   -- Insere a função.
   --
   CREATE OR REPLACE function public.manutencao_funcao( intCodmodulo       INTEGER
                                                      , intCodBiblioteca   INTEGER
                                                      , varNomeFunc        VARCHAR
                                                      , intCodTiporetorno INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodFuncao INTEGER := 0;
      varAux       VARCHAR;
   BEGIN

      SELECT cod_funcao
        INTO intCodFuncao
        FROM administracao.funcao
       WHERE cod_modulo                = intCodmodulo
         AND cod_biblioteca            = intCodBiblioteca
         AND Lower(Btrim(nom_funcao))  = Lower(Btrim(varNomeFunc))
      ;

      IF FOUND THEN
         DELETE FROM administracao.corpo_funcao_externa  WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_externa        WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao_referencia     WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.parametro             WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.variavel              WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
         DELETE FROM administracao.funcao                WHERE cod_modulo = intCodmodulo AND cod_biblioteca = intCodBiblioteca AND cod_funcao = intCodFuncao;
      END IF;

     -- Raise Notice ' Entrou 1 ';

     SELECT (max(cod_funcao)+1)
       INTO intCodFuncao
       FROM administracao.funcao
      WHERE cod_modulo       = intCodmodulo
        AND cod_biblioteca   = intCodBiblioteca
     ;

     --varAux := varNomeFunc || '  -   ' || To_Char( intCodFuncao, '999999') ;
     --RAise Notice '=> % ', varAux;

     IF intCodFuncao IS NULL OR intCodFuncao = 0 THEN
        intCodFuncao := 1;
     END IF;

     INSERT INTO administracao.funcao  ( cod_modulo
                                       , cod_biblioteca
                                       , cod_funcao
                                       , cod_tipo_retorno
                                       , nom_funcao)
                                VALUES ( intCodmodulo
                                       , intCodBiblioteca
                                       , intCodFuncao
                                       , intCodTiporetorno
                                       , varNomeFunc);

      RETURN intCodFuncao;

   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Inclusão de Váriaveis.
   --
   CREATE OR REPLACE function public.manutencao_variavel( intCodmodulo       INTEGER
                                                        , intCodBiblioteca   INTEGER
                                                        , intCodFuncao       INTEGER
                                                        , varNomVariavel     VARCHAR
                                                        , intTipoVariavel    INTEGER)
   RETURNS integer as $$
   DECLARE
      intCodVariavel INTEGER := 0;
   BEGIN

      If intCodFuncao != 0 THEN
         SELECT COALESCE((max(cod_variavel)+1),1)
           INTO intCodVariavel
           FROM administracao.variavel
          WHERE cod_modulo       = intCodmodulo
            AND cod_biblioteca   = intCodBiblioteca
            AND cod_funcao       = intCodFuncao
         ;

         INSERT INTO administracao.variavel ( cod_modulo
                                            , cod_biblioteca
                                            , cod_funcao
                                            , cod_variavel
                                            , nom_variavel
                                            , cod_tipo )
                                     VALUES ( intCodmodulo
                                            , intCodBiblioteca
                                            , intCodFuncao
                                            , intCodVariavel
                                            , varNomVariavel
                                            , intTipoVariavel
                                            );
      END IF;

      RETURN intCodVariavel;
   END;
   $$ LANGUAGE 'plpgsql';


   --
   -- Inclusão de parametro.
   --
   CREATE OR REPLACE function public.manutencao_parametro( intCodmodulo       INTEGER
                                                         , intCodBiblioteca   INTEGER
                                                         , intCodFuncao       INTEGER
                                                         , intCodVariavel     INTEGER)
   RETURNS VOID as $$
   DECLARE
      intOrdem INTEGER := 0;
   BEGIN
      If intCodFuncao != 0 THEN
         SELECT COALESCE((max(ordem)+1),1)
           INTO intOrdem
           FROM administracao.parametro
          WHERE cod_modulo       = intCodmodulo
            AND cod_biblioteca   = intCodBiblioteca
            AND cod_funcao       = intCodFuncao
         ;

         INSERT INTO administracao.parametro ( cod_modulo
                                             , cod_biblioteca
                                             , cod_funcao
                                             , cod_variavel
                                             , ordem)
                                      VALUES ( intCodmodulo
                                             , intCodBiblioteca
                                             , intCodFuncao
                                             , intCodVariavel
                                             , intOrdem );
      End If;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';


   --
   -- Inclusão de parametro.
   --
   CREATE OR REPLACE function public.manutencao_funcao_externa( intCodmodulo       INTEGER
                                                              , intCodBiblioteca   INTEGER
                                                              , intCodFuncao       INTEGER )
   RETURNS VOID as $$
   DECLARE
      --intCodFuncao INTEGER;
   BEGIN

      -- RAise Notice ' =====> % ', intCodFuncao;

      If intCodFuncao != 0 THEN
         INSERT INTO administracao.funcao_externa ( cod_modulo
                                                  , cod_biblioteca
                                                  , cod_funcao
                                                  , comentario
                                                  )
                                           VALUES ( intCodmodulo
                                                  , intCodBiblioteca
                                                  , intCodFuncao
                                                  , ''
                                                  );
      END IF;
      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Função principal.
   --
   CREATE OR REPLACE function public.manutencao() RETURNS VOID as $$
   DECLARE
      intCodFuncao   INTEGER;
      intCodVariavel INTEGER;
   BEGIN

      -- 1 | INTEIRO
      -- 2 | TEXTO
      -- 3 | BOOLEANO
      -- 4 | NUMERICO
      -- 5 | DATA

    --Inclusão de função interna arrecadacao/fn_acrescimo_indice.plsql

      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'pegaListaEventosDaBase'      , 2);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  27, 1, intCodFuncao, 'stNomBase'    , 2 );
      PERFORM           public.manutencao_parametro(  27, 1, intCodFuncao, intCodVariavel     );

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Execuçao  função.
   --
   Select public.manutencao();
   Drop Function public.manutencao();
   Drop Function public.manutencao_funcao(integer, integer, varchar, integer );
   Drop Function public.manutencao_variavel( integer, integer, integer, varchar, integer );
   Drop Function public.manutencao_parametro( integer, integer, integer, integer );
   Drop Function public.manutencao_funcao_externa( integer, integer, integer ) ;


----------------
-- Ticket #13200
----------------


----------------
-- Ticket #13198
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 50
         , 2
         , 'Recibo de Diárias'
         , 'reciboDiarias.rptdesign'
         );


CREATE TYPE colunasReciboPensaoJudicial AS (
    numcgm                      INTEGER,
    numcgm_servidor             INTEGER,
    registro                    INTEGER,
    nom_cgm_servidor            VARCHAR(200),
    nom_cgm                     VARCHAR(200),
    cpf                         VARCHAR,
    rg                          VARCHAR,
    dt_nascimento               VARCHAR,    
    nom_cgm_responsavel         VARCHAR(200),
    orgao                       VARCHAR,
    local                       VARCHAR,
    num_agencia                 VARCHAR,
    num_banco                   VARCHAR,
    conta_corrente              VARCHAR,
    dt_inclusao                 VARCHAR,
    desconto_fixado             VARCHAR,
    percentual                  NUMERIC(5,2),   
    dt_limite                   VARCHAR,   
    observacao                  VARCHAR,                                                                        
    valor                       VARCHAR,                                                                        
    valor_calculado             VARCHAR,
    valor_calculado_extenso     VARCHAR
);

CREATE TYPE colunasValoresAcumuladosRescisao AS (
    codigo                    varchar,
    descricao                 varchar,
    valor                     numeric(15,2),    
    folhas                    varchar
);

CREATE TYPE colunasValoresAcumulados AS (
    codigo                    varchar,
    descricao                 varchar,
    valor                     numeric(15,2),    
    folhas                    varchar
);

CREATE TYPE colunasValoresAcumuladosFerias AS (
    codigo                    varchar,
    descricao                 varchar,
    valor                     numeric(15,2),    
    folhas                    varchar
);

CREATE TYPE colunasValoresAcumuladosDecimo AS (
    codigo                    varchar,
    descricao                 varchar,
    valor                     numeric(15,2),    
    folhas                    varchar
);

CREATE TYPE colunasValoresAcumuladosComplementar AS (
    codigo                    varchar,
    descricao                 varchar,
    valor                     numeric(15,2),    
    folhas                    varchar
);

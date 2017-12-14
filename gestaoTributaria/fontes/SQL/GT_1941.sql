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
* $Id: GT_1941.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.1
*/


----------------
-- Ticket #13873
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '08924078000104';

   IF NOT FOUND THEN

        ALTER TABLE arrecadacao.modelo_carne ADD   column capa_primeira_folha BOOLEAN;
        UPDATE      arrecadacao.modelo_carne SET          capa_primeira_folha = false;
        ALTER TABLE arrecadacao.modelo_carne ALTER column capa_primeira_folha SET NOT NULL;

   END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #13852
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '08924078000104';

   IF NOT FOUND THEN

        INSERT
          INTO arrecadacao.tabela_conversao
             ( cod_tabela
             , exercicio
             , cod_modulo
             , nome_tabela
             , parametro_1
             )
        VALUES ( 1
             , '2008'
             , 12
             , 'Alíquota IPTU'
             , 'Tipo de Imóvel'
             );
        
        INSERT
          INTO arrecadacao.tabela_conversao_valores
             ( cod_tabela
             , exercicio
             , parametro_1
             , parametro_2
             , parametro_3
             , parametro_4
             , valor
             )
        VALUES (1
             , '2008'
             , 'Territorial'
             , ''
             , ''
             , ''
             , 0.00
             );
        
        INSERT
          INTO arrecadacao.tabela_conversao_valores
             ( cod_tabela
             , exercicio
             , parametro_1
             , parametro_2
             , parametro_3
             , parametro_4
             , valor
             )
        VALUES (1
             , '2008'
             , 'Predial'
             , ''
             , ''
             , ''
             , 0.00
             );

   END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


   --
   -- Insere a função interna p/ aliquota IPTU.
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

      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'imobiliario.buscaAliquotaIPTU', 2);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Execuçao  função.
   --
CREATE OR REPLACE FUNCTION manutencao_II() RETURNS VOID AS $$

DECLARE

    varAux      VARCHAR;
    stTeste     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '08924078000104';

   IF NOT FOUND THEN

        Select public.manutencao() INTO stTeste;

   END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao_II();
DROP FUNCTION manutencao_II();


   Drop Function public.manutencao();
   Drop Function public.manutencao_funcao(integer, integer, varchar, integer );
   Drop Function public.manutencao_variavel( integer, integer, integer, varchar, integer );
   Drop Function public.manutencao_parametro( integer, integer, integer, integer );
   Drop Function public.manutencao_funcao_externa( integer, integer, integer ) ;



CREATE OR REPLACE FUNCTION manutencao_II() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '08924078000104';

   IF NOT FOUND THEN

        CREATE TABLE arrecadacao.informacao_adicional (
            cod_informacao      INTEGER         NOT NULL,
            descricao           VARCHAR(80)     NOT NULL,
            tamanho             INTEGER         NOT NULL,
            cod_modulo          INTEGER         NOT NULL,
            cod_biblioteca      INTEGER         NOT NULL,
            cod_funcao          INTEGER         NOT NULL,
            CONSTRAINT pk_informacao_adicional  PRIMARY KEY (cod_informacao)
        );
        
        CREATE TABLE arrecadacao.informacao_adicional_layout_carne (
            cod_modelo          INTEGER         NOT NULL,
            cod_informacao      INTEGER         NOT NULL,
            ordem               INTEGER         NOT NULL,
            posicao_inicial     INTEGER         NOT NULL,
            largura             INTEGER         NOT NULL,
            CONSTRAINT pk_informacao_adicional_layout_carne     PRIMARY KEY ( cod_modelo, cod_informacao ),
            CONSTRAINT fk_informacao_adicional_layout_carne_1   FOREIGN KEY (cod_modelo)
                                                                REFERENCES arrecadacao.modelo_carne (cod_modelo),
            CONSTRAINT fk_informacao_adicional_layout_carne_2   FOREIGN KEY (cod_informacao)
                                                                REFERENCES arrecadacao.informacao_adicional (cod_informacao)
        );
        
        GRANT ALL ON arrecadacao.informacao_adicional TO GROUP urbem;
        GRANT ALL ON arrecadacao.informacao_adicional_layout_carne TO GROUP urbem;

   END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao_II();
DROP FUNCTION manutencao_II();



----------------
-- Ticket #13799
----------------

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor )
VALUES ( 14
     , '2008'
     , 'certidao_baixa'
     , 'nao'
     );

CREATE TABLE economico.baixa_emissao (
    inscricao_economica         INTEGER         NOT NULL,
    dt_inicio                   DATE            NOT NULL,
    timestamp                   TIMESTAMP       NOT NULL,
    cod_documento               INTEGER         NOT NULL,
    cod_tipo_documento          INTEGER         NOT NULL,
    num_documento               INTEGER         NOT NULL,
    dt_emissao                  DATE            NOT NULL,
    numcgm_usuario              INTEGER         NOT NULL,
    CONSTRAINT pk_baixa_emissao                 PRIMARY KEY                                     (inscricao_economica, dt_inicio, timestamp),
    CONSTRAINT fk_baixa_emissao_1               FOREIGN KEY                                     (inscricao_economica, dt_inicio, timestamp)
                                                REFERENCES economico.baixa_cadastro_economico   (inscricao_economica, dt_inicio, timestamp),
    CONSTRAINT fk_baixa_emissao_2               FOREIGN KEY                                     (cod_documento, cod_tipo_documento)
                                                REFERENCES administracao.modelo_documento       (cod_documento, cod_tipo_documento)
); 

GRANT ALL ON economico.baixa_emissao TO GROUP urbem;


----------------
-- Ticket #13506
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2402
          , 305
          , 'FLRelatorioValorVenal.php'
          , 'incluir'
          , 8
          , ''
          , 'Valores Venais'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 5
         , 25
         , 3
         , 'Valores Venais'
         , 'valoresVenais.rptdesign'
         );



----------------
-- Ticket #13799
----------------

CREATE OR REPLACE FUNCTION documentos() RETURNS VOID AS $$
DECLARE

    varAux      VARCHAR;
    stTeste     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '94068418000184';

   IF FOUND THEN

        INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1 from administracao.arquivos_documento), 'certidaoBaixaMariana.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
        INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1 from administracao.modelo_documento), 'Certidao de Baixa', 'certidaoBaixaMariana.agt', 3);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (451, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 3);
        INSERT INTO administracao.modelo_arquivos_documento VALUES (452, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 3);

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT documentos();
DROP FUNCTION documentos();



----------------
-- Ticket #13854
----------------

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

      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'calculafracaoIdeal', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );

--      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'fn_comissao_cobranca_mariana', 4);
--                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
--      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'dtVencimento', 5 );
--      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
--      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'dtCalculo', 5 );
--      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
--      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'nuValor', 4 );
--      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
--      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inAcrescimo', 1 );
--      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
--      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inTipo', 1 );
--      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
--
--      PERFORM           public.manutencao_funcao_externa( 25, 1, intCodFuncao ); 


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



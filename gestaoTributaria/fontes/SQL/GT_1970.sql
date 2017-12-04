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
* $Id: GT_1970.sql 64421 2016-02-19 12:14:17Z fabio $
*
* Versão 1.96.1
*/

----------------
-- Ticket #13763
----------------

INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1    from administracao.arquivos_documento), 'notificacaoDAUrbem.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1    from administracao.modelo_documento), 'Notificação DA', 'notificacaoDAUrbem.agt', 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1648, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1649, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1637, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1638, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1634, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1635, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);
INSERT INTO administracao.modelo_arquivos_documento VALUES (1636, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 5);


----------------
-- Ticket #14334
-- Ticket #14445
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

      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'monetario.buscaValorAcrescimo', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inCodAcrescimo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inCodTipo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inMes', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inAno', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );

      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'arrecadacao.fn_retorna_valor_calculo_grupo_credito', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inInscricao', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inGrupo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inExercicioGrupo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inExercicio', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inCredito', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inEspecie', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inGenero', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inNatureza', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );

      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'imobiliario.fn_buscaDataInscricaoImovel', 5);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );

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



------------------------------------------------
-- CADASTRO DA PLANILHA DE LEVANTAMENTOS FISCAIS
------------------------------------------------

INSERT INTO administracao.relatorio VALUES (5, 34, 1, 'Planilha de Lançamentos Fiscais', 'planilhaLevantamentoISSQN.rptdesign');




-----------------------------------------------
-- CADASTRANTO DOCUMENTOS - MÓDULO FISCALIZACAO
-----------------------------------------------

UPDATE administracao.modelo_documento   SET nome_documento   = 'Termo de Início - Processo Fiscal' WHERE nome_documento   = 'Termo de Inídio - Processo Fiscal';
UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'iniciar_processo_fiscal.odt' WHERE nome_arquivo_swx = 'Termo de Inídio - Processo Fiscal';

INSERT INTO administracao.modelo_documento          VALUES ((SELECT MAX (cod_documento)+1 FROM administracao.modelo_documento),'Termo de Encerramento - Processo Fiscal','termo_encerramento_fiscalizacao.odt',2);
INSERT INTO administracao.arquivos_documento        VALUES ((SELECT MAX (cod_arquivo)+1   FROM administracao.arquivos_documento),'termo_encerramento_fiscalizacao.odt','60315c6ab27d6b2b0a2cd5d946867de4', true);
INSERT INTO administracao.modelo_arquivos_documento VALUES (2304,(SELECT MAX (cod_documento) FROM administracao.modelo_documento),(SELECT MAX (cod_arquivo) FROM administracao.arquivos_documento), true, true,2);

INSERT INTO administracao.modelo_documento          VALUES ((SELECT MAX (cod_documento)+1 FROM administracao.modelo_documento),'Auto de Infração','auto_infracao.odt',4);
INSERT INTO administracao.arquivos_documento        VALUES ((SELECT MAX (cod_arquivo)+1   FROM administracao.arquivos_documento),'auto_infracao.odt','60315c6ab27d6b2b0a2cd5d946867de4', true);
INSERT INTO administracao.modelo_arquivos_documento VALUES (2305,(SELECT MAX (cod_documento) FROM administracao.modelo_documento),(SELECT MAX (cod_arquivo) FROM administracao.arquivos_documento), true, true,4);
INSERT INTO administracao.modelo_arquivos_documento VALUES (2410,(SELECT MAX (cod_documento) FROM administracao.modelo_documento),(SELECT MAX (cod_arquivo) FROM administracao.arquivos_documento), true, true,4);

INSERT INTO administracao.modelo_documento          VALUES ((SELECT MAX (cod_documento)+1 FROM administracao.modelo_documento),'Notificação de Processo Fiscal','notificacao_processo_fiscal.odt',4);
INSERT INTO administracao.arquivos_documento        VALUES ((SELECT MAX (cod_arquivo)+1   FROM administracao.arquivos_documento),'notificacao_processo_fiscal.odt','60315c6ab27d6b2b0a2cd5d946867de4', true);
INSERT INTO administracao.modelo_arquivos_documento VALUES (2304,(SELECT MAX (cod_documento) FROM administracao.modelo_documento),(SELECT MAX (cod_arquivo) FROM administracao.arquivos_documento), true, true,4);

INSERT INTO administracao.modelo_documento          VALUES ((SELECT MAX (cod_documento)+1 FROM administracao.modelo_documento),'Termo de Demolição','termo_demolicao.odt',2);
INSERT INTO administracao.arquivos_documento        VALUES ((SELECT MAX (cod_arquivo)+1   FROM administracao.arquivos_documento),'termo_demolicao.odt','60315c6ab27d6b2b0a2cd5d946867de4', true);
INSERT INTO administracao.modelo_arquivos_documento VALUES (2411,(SELECT MAX (cod_documento) FROM administracao.modelo_documento),(SELECT MAX (cod_arquivo) FROM administracao.arquivos_documento), true, true,2);

INSERT INTO administracao.modelo_documento          VALUES ((SELECT MAX (cod_documento)+1 FROM administracao.modelo_documento),'Termo de Embargo','termo_embargo.odt',2);
INSERT INTO administracao.arquivos_documento        VALUES ((SELECT MAX (cod_arquivo)+1   FROM administracao.arquivos_documento),'termo_embargo.odt','60315c6ab27d6b2b0a2cd5d946867de4', true);
INSERT INTO administracao.modelo_arquivos_documento VALUES (2412,(SELECT MAX (cod_documento) FROM administracao.modelo_documento),(SELECT MAX (cod_arquivo) FROM administracao.arquivos_documento), true, true,2);

INSERT INTO administracao.modelo_documento          VALUES ((SELECT MAX (cod_documento)+1 FROM administracao.modelo_documento),'Termo de Interdição','termo_interdicao.odt',2);
INSERT INTO administracao.arquivos_documento        VALUES ((SELECT MAX (cod_arquivo)+1   FROM administracao.arquivos_documento),'termo_interdicao.odt','60315c6ab27d6b2b0a2cd5d946867de4', true);
INSERT INTO administracao.modelo_arquivos_documento VALUES (2413,(SELECT MAX (cod_documento) FROM administracao.modelo_documento),(SELECT MAX (cod_arquivo) FROM administracao.arquivos_documento), true, true,2);


----------------
-- Ticket #14450
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stAux   VARCHAR;
BEGIN
    SELECT valor
      INTO stAux
      FROM administracao.configuracao
     WHERE parametro  = 'cnpj'
       AND exercicio  = '2008'
       AND cod_modulo = 2
       AND valor      = '13805528000180';

    IF FOUND THEN
        INSERT INTO administracao.arquivos_documento        VALUES ( (SELECT MAX(cod_arquivo)+1    from administracao.arquivos_documento), 'autorizacao_provisoria_mata.odt', '7afe3f8ca861bb7ae2c1d3f1b3409d05', true );
        INSERT INTO administracao.modelo_documento          VALUES ( (SELECT MAX(cod_documento)+1    from administracao.modelo_documento), 'Autorização Provisória', 'autorizacao_provisoria_mata.odt', 1);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 462, (SELECT MAX(cod_documento) from administracao.modelo_documento), (SELECT MAX(cod_arquivo) from administracao.arquivos_documento), true, true, 1); 
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #5890
----------------

CREATE TABLE imobiliario.imovel_foto(
    inscricao_municipal         INTEGER     NOT NULL,
    cod_foto                    INTEGER     NOT NULL,
    descricao                   TEXT        NOT NULL,
    foto                        OID         NOT NULL,
    CONSTRAINT pk_imovel_foto               PRIMARY KEY                     (inscricao_municipal, cod_foto),
    CONSTRAINT fk_imovel_foto_1             FOREIGN KEY                     (inscricao_municipal)
                                            REFERENCES imobiliario.imovel   (inscricao_municipal)
);

GRANT ALL ON imobiliario.imovel_foto TO GROUP urbem;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2465
          , 179
          , 'FLManterImovel.php'
          , 'foto'
          , 7
          , ''
          , 'Incluir Imagem'
          );


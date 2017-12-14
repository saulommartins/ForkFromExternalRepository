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
* Versao 2.00.9
*
* Fabio Bertoldi - 20120704
*
*/

----------------
-- Ticket #18714
----------------

 ALTER TABLE imobiliario.licenca_imovel_nova_construcao ADD COLUMN cod_construcao INTEGER NOT NULL;
 ALTER TABLE imobiliario.licenca_imovel_nova_construcao ADD CONSTRAINT fk_licenca_imovel_nova_construcao_2 FOREIGN KEY (cod_construcao) REFERENCES imobiliario.construcao (cod_construcao);

 ALTER TABLE imobiliario.licenca_imovel_nova_edificacao DROP CONSTRAINT fk_licenca_imovel_nova_edificacao_1;
 ALTER TABLE imobiliario.licenca_imovel_nova_edificacao DROP CONSTRAINT fk_licenca_imovel_nova_edificacao_2;
 ALTER TABLE imobiliario.licenca_imovel_nova_edificacao ADD COLUMN cod_construcao INTEGER NOT NULL;
 ALTER TABLE imobiliario.licenca_imovel_nova_edificacao ADD CONSTRAINT fk_licenca_imovel_nova_edificacao_1 FOREIGN KEY (cod_licenca, exercicio, inscricao_municipal) REFERENCES imobiliario.licenca_imovel(cod_licenca, exercicio, inscricao_municipal);
 ALTER TABLE imobiliario.licenca_imovel_nova_edificacao ADD CONSTRAINT fk_licenca_imovel_nova_edificacao_2 FOREIGN KEY (cod_tipo, cod_construcao) REFERENCES imobiliario.construcao_edificacao (cod_tipo, cod_construcao);


----------------
-- Ticket #19368
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM arrecadacao.tipo_pagamento
      WHERE pagamento = FALSE
          ;
    IF NOT FOUND THEN
        INSERT INTO arrecadacao.tipo_pagamento VALUES (11, 'Anulação', TRUE, 'Anulação', FALSE);
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #18714
----------------

CREATE OR REPLACE FUNCTION manutencao_manaquiri() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2012'
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF FOUND THEN

        INSERT INTO administracao.modelo_documento          VALUES ( ( SELECT MAX(cod_documento) + 1 FROM administracao.modelo_documento )   , 'Alvará de Construção - Manaquiri', 'alvara_construcao_MANAQUIRI.odt', 1 );
        INSERT INTO administracao.arquivos_documento        VALUES ( ( SELECT MAX(cod_arquivo) + 1 FROM administracao.arquivos_documento ) , 'alvara_construcao_MANAQUIRI.odt', 'a5103dacaedc7494811e7e4e0f6041a9', false);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 2205, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 2206, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 2204, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);

        INSERT INTO administracao.modelo_documento          VALUES ( ( SELECT MAX(cod_documento) + 1 FROM administracao.modelo_documento )   , 'Alvará Vigilância Sanitária - Manaquiri', 'alvara_vigilancia_sanitaria_MANAQUIRI.odt', 1 );
        INSERT INTO administracao.arquivos_documento        VALUES ( ( SELECT MAX(cod_arquivo) + 1 FROM administracao.arquivos_documento ) , 'alvara_vigilancia_sanitaria_MANAQUIRI.odt', 'a4e801cb82a9bf0b5322722c2be14a9a', false);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 462, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 465, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);

        INSERT INTO administracao.modelo_documento          VALUES ( ( SELECT MAX(cod_documento) + 1 FROM administracao.modelo_documento )   , 'Alvará de Funcionamento - Manaquiri', 'alvara_funcionamento_MANAQUIRI.odt', 1 );
        INSERT INTO administracao.arquivos_documento        VALUES ( ( SELECT MAX(cod_arquivo) + 1 FROM administracao.arquivos_documento ) , 'alvara_funcionamento_MANAQUIRI.odt', 'a4e801cb82a9bf0b5322722c2be14a9a', false);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 462, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 465, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);

        INSERT INTO administracao.modelo_documento          VALUES ( ( SELECT MAX(cod_documento) + 1 FROM administracao.modelo_documento )   , 'Alvará Provisório - Manaquiri', 'alvara_ambulantes_MANAQUIRI.odt', 1 );
        INSERT INTO administracao.arquivos_documento        VALUES ( ( SELECT MAX(cod_arquivo) + 1 FROM administracao.arquivos_documento ) , 'alvara_ambulantes_MANAQUIRI.odt', 'c8ba42396c9862b983fc15d812510789', false);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 462, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);
        INSERT INTO administracao.modelo_arquivos_documento VALUES ( 465, ( SELECT MAX(cod_documento) FROM administracao.modelo_documento ), ( SELECT MAX(cod_arquivo) FROM administracao.arquivos_documento ), false, true, 1);

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao_manaquiri();
DROP FUNCTION manutencao_manaquiri();


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

      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'buscaCodigoConstrucaoAlvaraNovaEdificacao', 1);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );


      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'buscaAreaConstrucaoAlvaraNovaEdificacao', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inCodConstrucao', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );


      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'identificaConstrucaoEdificacaoImovel', 2);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inCodConstrucao', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );


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



CREATE OR REPLACE FUNCTION manutencao_manaquiri() RETURNS VOID AS $$
DECLARE
    stSQL VARCHAR;
BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2012'
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF FOUND THEN

        ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        INSERT INTO administracao.funcao
                             (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , cod_tipo_retorno
                          , nom_funcao)
                       VALUES (12,2,(SELECT MAX(cod_funcao)+1 FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),4,'AlvaraConstrucao');
        INSERT INTO administracao.funcao_externa
                          (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , comentario
                          , corpo_pl
                          , corpo_ln)
                    VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),'função para o cálculo do alvará de construção','FUNCTION AlvaraConstrucao() RETURNS NUMERIC as \\'' 
        DECLARE

          inCodConstrucao INTEGER;
          inCodTipoConstrucao INTEGER;
          inExercicio INTEGER;
          inRegistro INTEGER;
          nuArea NUMERIC;
          nuUFIR NUMERIC;
          nuUFM NUMERIC;
          nuValor NUMERIC;
          nuValorEstrutura NUMERIC;
          stConstrucaoEdificacao VARCHAR := \\''\\''\\''\\'';
          stEstrutura VARCHAR := \\''\\''\\''\\'';
        BEGIN
        inRegistro := RecuperarBufferInteiro(  \\''\\''inRegistro\\''\\''  ); 
        inExercicio := RecuperarBufferInteiro(  \\''\\''inExercicio\\''\\''  ); 
        inCodConstrucao := buscaCodigoConstrucaoAlvaraNovaEdificacao(  inRegistro  ); 
        stConstrucaoEdificacao := identificaConstrucaoEdificacaoImovel(  inCodConstrucao  ); 
        nuArea := buscaAreaConstrucaoAlvaraNovaEdificacao(  inRegistro , inCodConstrucao  ); 
        IF   stConstrucaoEdificacao  =  \\''\\''construcao\\''\\'' THEN
            stEstrutura := recuperaCadastroImobiliarioConstrucaoOutrosCECoeficienteDeEdificacao(  inCodConstrucao  ); 
        ELSE
            inCodTipoConstrucao := arrecadacao.fn_tipo_edificacao(  inRegistro , inCodConstrucao  ); 
            stEstrutura := recuperaCadastroImobiliarioTipoDeEdificacaoCECoeficienteDeEdificacao(  inCodTipoConstrucao , inCodConstrucao  ); 
        END IF;
        nuValorEstrutura := arrecadacao.fn_busca_tabela_conversao(  30 , inExercicio , \\''\\''\\''\\'' , stEstrutura , \\''\\''\\''\\'' , \\''\\''\\''\\''  ); 
        nuUFM := arrecadacao.fn_busca_tabela_conversao(  2 , inExercicio , \\''\\''2012\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\''  ); 
        nuUFIR := arrecadacao.fn_busca_tabela_conversao(  3 , inExercicio , \\''\\''2012\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\''  ); 
        nuValor := nuUFIR*nuUFM *nuArea *nuValorEstrutura ;
        RETURN nuValor;
        END;
         \\'' LANGUAGE \\''plpgsql\\''; 
        ','');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),1,'0','#inRegistro <- RecuperarBufferInteiro(  "inRegistro"  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),2,'0','#inExercicio <- RecuperarBufferInteiro(  "inExercicio"  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),3,'0','#inCodConstrucao <- buscaCodigoConstrucaoAlvaraNovaEdificacao(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),4,'0','#stConstrucaoEdificacao <- identificaConstrucaoEdificacaoImovel(  #inCodConstrucao  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),5,'0','#nuArea <- buscaAreaConstrucaoAlvaraNovaEdificacao(  #inRegistro , #inCodConstrucao  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),6,'1','SE   #stConstrucaoEdificacao  =  "construcao" ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),7,'1','#stEstrutura <- recuperaCadastroImobiliarioConstrucaoOutrosCECoeficienteDeEdificacao(  #inCodConstrucao  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),8,'1','SENAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),9,'1','#inCodTipoConstrucao <- arrecadacao.fn_tipo_edificacao(  #inRegistro , #inCodConstrucao  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),10,'1','#stEstrutura <- recuperaCadastroImobiliarioTipoDeEdificacaoCECoeficienteDeEdificacao(  #inCodTipoConstrucao , #inCodConstrucao  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),11,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),12,'0','#nuValorEstrutura <- arrecadacao.fn_busca_tabela_conversao(  30 , #inExercicio , VAZIO , #stEstrutura , VAZIO , VAZIO  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),13,'0','#nuUFM <- arrecadacao.fn_busca_tabela_conversao(  2 , #inExercicio , "2012" , VAZIO , VAZIO , VAZIO  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),14,'0','#nuUFIR <- arrecadacao.fn_busca_tabela_conversao(  3 , #inExercicio , "2012" , VAZIO , VAZIO , VAZIO  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),15,'0','#nuValor <- #nuUFIR*#nuUFM *#nuArea *#nuValorEstrutura ;');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),16,'0','RETORNA #nuValor');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),1,1,'inCodConstrucao','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),2,1,'inCodTipoConstrucao','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),3,1,'inExercicio','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),4,1,'inRegistro','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),5,4,'nuArea','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),6,4,'nuUFIR','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),7,4,'nuUFM','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),8,4,'nuValor','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),9,4,'nuValorEstrutura','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),10,2,'stConstrucaoEdificacao','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (12,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 12 and cod_biblioteca = 2),11,2,'stEstrutura','');


        -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        INSERT INTO administracao.funcao
                             (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , cod_tipo_retorno
                          , nom_funcao)
                       VALUES (14,2,COALESCE((SELECT MAX(cod_funcao)+1 FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),1),4,'TaxaVerificacao');
        INSERT INTO administracao.funcao_externa
                          (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , comentario
                          , corpo_pl
                          , corpo_ln)
                    VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),'cálculo da taxa de verificação, para emissão de alvarás.','FUNCTION TaxaVerificacao() RETURNS NUMERIC as \\'' 
        DECLARE

          inCodigoAtividade INTEGER;
          inExercicio INTEGER;
          inRegistro INTEGER;
          nuCoeficienteAtividade NUMERIC;
          nuCoeficienteCategoria NUMERIC;
          nuCoeficienteSetor NUMERIC;
          nuValor NUMERIC;
          nuValorUFIR NUMERIC;
          nuValorUFM NUMERIC;
          stCoeficienteCategoria VARCHAR := \\''\\''\\''\\'';
          stCoeficienteSetor VARCHAR := \\''\\''\\''\\'';
          stTipoInscricao VARCHAR := \\''\\''\\''\\'';
        BEGIN
        inRegistro := RecuperarBufferInteiro(  \\''\\''inRegistro\\''\\''  ); 
        inExercicio := RecuperarBufferInteiro(  \\''\\''inExercicio\\''\\''  ); 
        stTipoInscricao := buscaTipoDaInscricaoEconomica(  inRegistro  ); 
        inCodigoAtividade := buscaCodigoAtividadeDaInscricaoEconomica(  inRegistro , 1  ); 
        nuCoeficienteAtividade := buscaAliquotaAtividade(  inCodigoAtividade  ); 
        IF     stTipoInscricao  =  \\''\\''direito\\''\\'' THEN
            stCoeficienteCategoria := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  inRegistro  ); 
            stCoeficienteSetor := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoCZCoeficienteDasZonasSetor(  inRegistro  ); 
        END IF;
        IF   stTipoInscricao  =  \\''\\''fato\\''\\'' THEN
            stCoeficienteCategoria := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  inRegistro  ); 
            stCoeficienteSetor := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoCZCoeficienteDasZonasSetor(  inRegistro  ); 
        END IF;
        IF   stTipoInscricao  =  \\''\\''autonomo\\''\\'' THEN
            stCoeficienteCategoria := recuperaCadastroEconomicoInscricaoEconomicaAutonomoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  inRegistro  ); 
            stCoeficienteSetor := recuperaCadastroEconomicoInscricaoEconomicaAutonomoCZCoeficienteDasZonasSetor(  inRegistro  ); 
        END IF;
        nuCoeficienteCategoria := arrecadacao.fn_busca_tabela_conversao(  28 , inExercicio , \\''\\''\\''\\'' , stCoeficienteCategoria , \\''\\''\\''\\'' , \\''\\''\\''\\''  ); 
        nuCoeficienteSetor := arrecadacao.fn_busca_tabela_conversao(  29 , inExercicio , stCoeficienteSetor , \\''\\''\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\''  ); 
        nuValorUFM := arrecadacao.fn_busca_tabela_conversao(  2 , inExercicio , \\''\\''2012\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\''  ); 
        nuValorUFIR := arrecadacao.fn_busca_tabela_conversao(  3 , inExercicio , \\''\\''2012\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\'' , \\''\\''\\''\\''  ); 
        nuValor := nuValorUFIR*nuValorUFM *nuCoeficienteAtividade *nuCoeficienteCategoria *nuCoeficienteSetor ;
        RETURN nuValor;
        END;
         \\'' LANGUAGE \\''plpgsql\\''; 
        ','');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),1,'0','#inRegistro <- RecuperarBufferInteiro(  "inRegistro"  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),2,'0','#inExercicio <- RecuperarBufferInteiro(  "inExercicio"  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),3,'0','#stTipoInscricao <- buscaTipoDaInscricaoEconomica(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),4,'0','#inCodigoAtividade <- buscaCodigoAtividadeDaInscricaoEconomica(  #inRegistro , 1  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),5,'0','#nuCoeficienteAtividade <- buscaAliquotaAtividade(  #inCodigoAtividade  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),6,'1','SE     #stTipoInscricao  =  "direito" ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),7,'1','#stCoeficienteCategoria <- recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),8,'1','#stCoeficienteSetor <- recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoCZCoeficienteDasZonasSetor(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),9,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),10,'1','SE   #stTipoInscricao  =  "fato" ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),11,'1','#stCoeficienteCategoria <- recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),12,'1','#stCoeficienteSetor <- recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoCZCoeficienteDasZonasSetor(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),13,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),14,'1','SE   #stTipoInscricao  =  "autonomo" ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),15,'1','#stCoeficienteCategoria <- recuperaCadastroEconomicoInscricaoEconomicaAutonomoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),16,'1','#stCoeficienteSetor <- recuperaCadastroEconomicoInscricaoEconomicaAutonomoCZCoeficienteDasZonasSetor(  #inRegistro  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),17,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),18,'0','#nuCoeficienteCategoria <- arrecadacao.fn_busca_tabela_conversao(  28 , #inExercicio , VAZIO , #stCoeficienteCategoria , VAZIO , VAZIO  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),19,'0','#nuCoeficienteSetor <- arrecadacao.fn_busca_tabela_conversao(  29 , #inExercicio , #stCoeficienteSetor , VAZIO , VAZIO , VAZIO  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),20,'0','#nuValorUFM <- arrecadacao.fn_busca_tabela_conversao(  2 , #inExercicio , "2012" , VAZIO , VAZIO , VAZIO  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),21,'0','#nuValorUFIR <- arrecadacao.fn_busca_tabela_conversao(  3 , #inExercicio , "2012" , VAZIO , VAZIO , VAZIO  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),22,'0','#nuValor <- #nuValorUFIR*#nuValorUFM *#nuCoeficienteAtividade *#nuCoeficienteCategoria *#nuCoeficienteSetor ;');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),23,'0','RETORNA #nuValor');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),1,1,'inCodigoAtividade','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),2,1,'inExercicio','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),3,1,'inRegistro','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),4,4,'nuCoeficienteAtividade','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),5,4,'nuCoeficienteCategoria','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),6,4,'nuCoeficienteSetor','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),7,4,'nuValor','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),8,4,'nuValorUFIR','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),9,4,'nuValorUFM','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),10,2,'stCoeficienteCategoria','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),11,2,'stCoeficienteSetor','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (14,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 14 and cod_biblioteca = 2),12,2,'stTipoInscricao','');


        -----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        INSERT INTO administracao.funcao
                             (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , cod_tipo_retorno
                          , nom_funcao)
                       VALUES (25,2,(SELECT MAX(cod_funcao)+1 FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),3,'regradesoneracaoAlvara2012');
        INSERT INTO administracao.funcao_externa
                          (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , comentario
                          , corpo_pl
                          , corpo_ln)
                    VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),'Regra de validação para concessão de desoneração no cálculo de concessão de alvarás 2012','FUNCTION regradesoneracaoAlvara2012(INTEGER) RETURNS BOOLEAN as \\'' 
        DECLARE
        inEmpresa ALIAS FOR $1;

          boRetorno BOOLEAN;
          stIsento VARCHAR := \\''\\''\\''\\'';
          stTipoInscricao VARCHAR := \\''\\''\\''\\'';
          stAlvaraIsento VARCHAR := \\''\\''\\''\\'';
        BEGIN
        stTipoInscricao := buscaTipoDaInscricaoEconomica(  inEmpresa  ); 
        IF   stTipoInscricao  =  \\''\\''direito\\''\\'' THEN
            stIsento := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoAlvaraIsento(  inEmpresa  ); 
        END IF;
        IF   stTipoInscricao  =  \\''\\''fato\\''\\'' THEN
            stIsento := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoAlvaraIsento(  inEmpresa  ); 
        END IF;
        IF   stTipoInscricao  =  \\''\\''autonomo\\''\\'' THEN
            stIsento := recuperaCadastroEconomicoInscricaoEconomicaAutonomoAlvaraIsento(  inEmpresa  ); 
        END IF;
        IF     stIsento  =   1 THEN
            boRetorno := TRUE;
        ELSE
            boRetorno := FALSE;
        END IF;
        RETURN boRetorno;
        END;
         \\'' LANGUAGE \\''plpgsql\\''; 
        ','');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),1,'0','#stTipoInscricao <- buscaTipoDaInscricaoEconomica(  #inEmpresa  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),2,'1','SE   #stTipoInscricao  =  "direito" ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),3,'1','#stIsento <- recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoAlvaraIsento(  #inEmpresa  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),4,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),5,'1','SE   #stTipoInscricao  =  "fato" ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),6,'1','#stIsento <- recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoAlvaraIsento(  #inEmpresa  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),7,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),8,'1','SE   #stTipoInscricao  =  "autonomo" ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),9,'1','#stIsento <- recuperaCadastroEconomicoInscricaoEconomicaAutonomoAlvaraIsento(  #inEmpresa  ); ');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),10,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),11,'1','SE     #stIsento  =   1 ENTAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),12,'1','#boRetorno <- VERDADEIRO;');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),13,'1','SENAO');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),14,'1','#boRetorno <- FALSO;');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),15,'0','FIMSE');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),16,'0','RETORNA #boRetorno');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),1,3,'boRetorno','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),2,2,'stIsento','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),3,2,'stTipoInscricao','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),4,2,'stAlvaraIsento','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),5,1,'inEmpresa','');
        INSERT INTO administracao.parametro
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , ordem)
                       VALUES (25,2,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 2),5,0);


        ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        INSERT INTO administracao.funcao
                             (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , cod_tipo_retorno
                          , nom_funcao)
                       VALUES (25,3,(SELECT MAX(cod_funcao)+1 FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),4,'desoneracaoAlvara2012');
        INSERT INTO administracao.funcao_externa
                          (cod_modulo
                          , cod_biblioteca
                          , cod_funcao
                          , comentario
                          , corpo_pl
                          , corpo_ln)
                    VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),'Fórmula para concessão de desoneração no cálculo de concessão de alvarás 2012','FUNCTION desoneracaoAlvara2012(INTEGER,NUMERIC) RETURNS NUMERIC as \\'' 
        DECLARE
        inEmpresa ALIAS FOR $1;
        nuValor ALIAS FOR $2;
        
          nuValorRetorno NUMERIC;
        BEGIN
        nuValorRetorno := nuValor*0.00;
        RETURN nuValorRetorno;
        END;
         \\'' LANGUAGE \\''plpgsql\\''; 
        ','');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),1,'0','#nuValorRetorno <- #nuValor*0.00;');
        INSERT INTO administracao.corpo_funcao_externa
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_linha
                             , nivel
                             , linha)
                       VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),2,'0','RETORNA #nuValorRetorno');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),1,4,'nuValorRetorno','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),2,1,'inEmpresa','');
        INSERT INTO administracao.variavel
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , cod_tipo
                             , nom_variavel
                             , valor_inicial)
                       VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),3,4,'nuValor','');
        INSERT INTO administracao.parametro
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , ordem)
                       VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),2,0);
        INSERT INTO administracao.parametro
                             (cod_modulo
                             , cod_biblioteca
                             , cod_funcao
                             , cod_variavel
                             , ordem)
                       VALUES (25,3,(SELECT MAX(cod_funcao) FROM administracao.funcao WHERE cod_modulo = 25 and cod_biblioteca = 3),3,1);

---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

    stSQL := '
            CREATE OR REPLACE FUNCTION alvaraconstrucao() RETURNS NUMERIC AS \$\$
            DECLARE
              inCodConstrucao           INTEGER;
              inCodTipoConstrucao       INTEGER;
              inExercicio               INTEGER;
              inRegistro                INTEGER;
              nuArea                    NUMERIC;
              nuUFIR                    NUMERIC;
              nuUFM                     NUMERIC;
              nuValor                   NUMERIC;
              nuValorEstrutura          NUMERIC;
              stConstrucaoEdificacao    VARCHAR := \'\';
              stEstrutura               VARCHAR := \'\';
            BEGIN
            inRegistro := RecuperarBufferInteiro(  \'inRegistro\'  );
            inExercicio := RecuperarBufferInteiro(  \'inExercicio\'  );
            inCodConstrucao := buscaCodigoConstrucaoAlvaraNovaEdificacao(  inRegistro  );
            inCodTipoConstrucao := arrecadacao.fn_tipo_edificacao(  inRegistro , inCodConstrucao  );
            stConstrucaoEdificacao := identificaConstrucaoEdificacaoImovel(  inCodConstrucao  );
            nuArea := buscaAreaConstrucaoAlvaraNovaEdificacao(  inRegistro , inCodConstrucao  );
            IF   stConstrucaoEdificacao  =  \'construcao\' THEN
                stEstrutura := recuperaCadastroImobiliarioConstrucaoOutrosCECoeficienteDeEdificacao(  inCodConstrucao  );
            ELSE
                stEstrutura := recuperaCadastroImobiliarioTipoDeEdificacaoCECoeficienteDeEdificacao(  inCodTipoConstrucao , inCodConstrucao  );
            END IF;
            nuValorEstrutura := arrecadacao.fn_busca_tabela_conversao(  30 , inExercicio , \'\' , stEstrutura , \'\' , \'\'  );
            nuUFM := arrecadacao.fn_busca_tabela_conversao(  2 , inExercicio , \'2012\' , \'\' , \'\' , \'\'  );
            nuUFIR := arrecadacao.fn_busca_tabela_conversao(  3 , inExercicio , \'2012\' , \'\' , \'\' , \'\'  );
            nuValor := nuUFIR*nuUFM *nuArea *nuValorEstrutura ;
            RETURN nuValor;
            END;
            \$\$ LANGUAGE \'plpgsql\';
            ';
    EXECUTE stSQL;


    stSQL := '
            CREATE OR REPLACE FUNCTION taxaverificacao() RETURNS NUMERIC AS \$\$
            DECLARE
              inCodigoAtividade         INTEGER;
              inExercicio               INTEGER;
              inRegistro                INTEGER;
              nuCoeficienteAtividade    NUMERIC;
              nuCoeficienteCategoria    NUMERIC;
              nuCoeficienteSetor        NUMERIC;
              nuValor                   NUMERIC;
              nuValorUFIR               NUMERIC;
              nuValorUFM                NUMERIC;
              stCoeficienteCategoria    VARCHAR := \'\';
              stCoeficienteSetor        VARCHAR := \'\';
              stTipoInscricao           VARCHAR := \'\';
            BEGIN
            inRegistro := RecuperarBufferInteiro(  \'inRegistro\'  );
            inExercicio := RecuperarBufferInteiro(  \'inExercicio\'  );
            stTipoInscricao := buscaTipoDaInscricaoEconomica(  inRegistro  );
            inCodigoAtividade := buscaCodigoAtividadeDaInscricaoEconomica(  inRegistro , 1  );
            nuCoeficienteAtividade := buscaAliquotaAtividade(  inCodigoAtividade  );
            IF     stTipoInscricao  =  \'direito\' THEN
                stCoeficienteCategoria := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  inRegistro  );
                stCoeficienteSetor := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoCZCoeficienteDasZonasSetor(  inRegistro  );
            END IF;
            IF   stTipoInscricao  =  \'fato\' THEN
                stCoeficienteCategoria := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  inRegistro  );
                stCoeficienteSetor := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoCZCoeficienteDasZonasSetor(  inRegistro  );
            END IF;
            IF   stTipoInscricao  =  \'autonomo\' THEN
                stCoeficienteCategoria := recuperaCadastroEconomicoInscricaoEconomicaAutonomoKNCoeficienteDoNumeroDeEmpregadosCCCoeficienteDaCategoria(  inRegistro  );
                stCoeficienteSetor := recuperaCadastroEconomicoInscricaoEconomicaAutonomoCZCoeficienteDasZonasSetor(  inRegistro  );
            END IF;
            nuCoeficienteCategoria := arrecadacao.fn_busca_tabela_conversao(  28 , inExercicio , \'\' , stCoeficienteCategoria , \'\' , \'\'  );
            nuCoeficienteSetor := arrecadacao.fn_busca_tabela_conversao(  29 , inExercicio , stCoeficienteSetor , \'\' , \'\' , \'\'  );
            nuValorUFM := arrecadacao.fn_busca_tabela_conversao(  2 , inExercicio , \'2012\' , \'\' , \'\' , \'\'  );
            nuValorUFIR := arrecadacao.fn_busca_tabela_conversao(  3 , inExercicio , \'2012\' , \'\' , \'\' , \'\'  );
            nuValor := nuValorUFIR*nuValorUFM *nuCoeficienteAtividade *nuCoeficienteCategoria *nuCoeficienteSetor ;
            RETURN nuValor;
            END;
            \$\$ LANGUAGE \'plpgsql\';
            ';
    EXECUTE stSQL;


    stSQL := '
            CREATE OR REPLACE FUNCTION regradesoneracaoAlvara2012(INTEGER) RETURNS BOOLEAN AS \$\$
            DECLARE
              inEmpresa ALIAS FOR $1;
              boRetorno         BOOLEAN;
              stIsento          VARCHAR := \'\';
              stTipoInscricao   VARCHAR := \'\';
              stAlvaraIsento    VARCHAR := \'\';
            BEGIN
            stTipoInscricao := buscaTipoDaInscricaoEconomica(  inEmpresa  );
            IF   stTipoInscricao  =  \'direito\' THEN
                stIsento := recuperaCadastroEconomicoInscricaoEconomicaEmpresaDeDireitoAlvaraIsento(  inEmpresa  );
            END IF;
            IF   stTipoInscricao  =  \'fato\' THEN
                stIsento := recuperaCadastroEconomicoInscricaoEconomicoEmpresaDeFatoAlvaraIsento(  inEmpresa  );
            END IF;
            IF   stTipoInscricao  =  \'autonomo\' THEN
                stIsento := recuperaCadastroEconomicoInscricaoEconomicaAutonomoAlvaraIsento(  inEmpresa  );
            END IF;
            IF     stIsento  =   1 THEN
                boRetorno := TRUE;
            ELSE
                boRetorno := FALSE;
            END IF;
            RETURN boRetorno;
            END;
            \$\$ LANGUAGE \'plpgsql\';
            ';
    EXECUTE stSQL;


    stSQL := '
            CREATE OR REPLACE FUNCTION desoneracaoAlvara2012(INTEGER, NUMERIC) RETURNS NUMERIC AS \$\$
            DECLARE
            inEmpresa ALIAS FOR $1;
            nuValor ALIAS FOR $2;

              nuValorRetorno NUMERIC;
            BEGIN
            nuValorRetorno := nuValor*0.00;
            RETURN nuValorRetorno;
            END;
            \$\$ LANGUAGE \'plpgsql\';
            ';
    EXECUTE stSQL;

        --- tabelas de conversao

        INSERT INTO arrecadacao.tabela_conversao SELECT cod_tabela, '2012' AS exercicio, cod_modulo, nome_tabela, parametro_1, parametro_2, parametro_3, parametro_4 FROM arrecadacao.tabela_conversao WHERE exercicio = '2011';

        INSERT INTO arrecadacao.tabela_conversao (cod_tabela, exercicio, cod_modulo, nome_tabela, parametro_1, parametro_2, parametro_3, parametro_4) VALUES (28, '2012', 14, 'KN - Coeficiente do Nú de Empregados (CC - Coeficiente da Categoria)' , 'Especificacao                              ', 'Codigo', NULL, NULL);
        INSERT INTO arrecadacao.tabela_conversao (cod_tabela, exercicio, cod_modulo, nome_tabela, parametro_1, parametro_2, parametro_3, parametro_4) VALUES (29, '2012', 14, 'CZ - Coeficiente das Zonas(Coeficiente do Setor)'                     , 'Zonas                                      ', NULL    , NULL, NULL);
        INSERT INTO arrecadacao.tabela_conversao (cod_tabela, exercicio, cod_modulo, nome_tabela, parametro_1, parametro_2, parametro_3, parametro_4) VALUES (30, '2012', 14, 'CE - Coeficiente de Edificacao'                                       , 'Tipo de Material Pedrominante na Edificacao', 'Codigo', NULL, NULL);

        INSERT INTO arrecadacao.tabela_conversao_valores SELECT cod_tabela, '2012' AS exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor FROM arrecadacao.tabela_conversao_valores WHERE exercicio = '2011';
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES ( 2, '2012', '2012', '', '', '', 15   );
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES ( 3, '2012', '2012', '', '', '', 1.064);

        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'Até Empregados'                            , '1' , '', '', 0.30);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 4 a 5 Empregados'                       , '2' , '', '', 0.50);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 6 a 7 empregados'                       , '3' , '', '', 0.70);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 8 a 10 Empregados'                      , '4' , '', '', 0.80);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 11 a 20 Empregados'                     , '5' , '', '', 0.85);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 21 a 40 Empregados'                     , '6' , '', '', 0.90);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 41 a 100 Empregados'                    , '7' , '', '', 0.93);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 101 a 500'                              , '8' , '', '', 0.95);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 501 a 1000'                             , '9' , '', '', 0.97);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'De 1001 a 2000 Empregados'                 , '10', '', '', 0.98);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (28, '2012', 'Acima de 2001 Empregados'                  , '11', '', '', 1.00);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '1'                                         , ''  , '', '', 3.00);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '2'                                         , ''  , '', '', 3.00);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '3'                                         , ''  , '', '', 2.50);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '4'                                         , ''  , '', '', 2.50);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '5'                                         , ''  , '', '', 2.00);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '6'                                         , ''  , '', '', 2.00);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '7'                                         , ''  , '', '', 1.50);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '8'                                         , ''  , '', '', 1.50);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (29, '2012', '9'                                         , ''  , '', '', 3.00);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (30, '2012', 'Madeira ou taipa'                          , '1' , '', '', 0.50);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (30, '2012', 'Alvenaria Simples ou Mista'                , '2' , '', '', 0.60);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (30, '2012', 'Alvenaria com estrutura concreto/metalica' , '3' , '', '', 0.70);
        INSERT INTO arrecadacao.tabela_conversao_valores (cod_tabela, exercicio, parametro_1, parametro_2, parametro_3, parametro_4, valor) VALUES (30, '2012', 'Alvenaria com estrutura vertical'          , '4' , '', '', 1.50);


    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao_manaquiri();
DROP FUNCTION manutencao_manaquiri();


----------------
-- Ticket #19377
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2012'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;
    IF FOUND THEN
        UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'certidaoPositivaMata.odt'         , checksum = '0def81480d996a5e08c328dbb75111c7' WHERE cod_arquivo = 19;
        UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'certidaoNegativaMata.odt'         , checksum = '2314cec7be68030c99ea481ddac73bd4' WHERE cod_arquivo = 20;
        UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'certidaoPositivaNegativaMata.odt' , checksum = '0a449cea43ca4d113a3d5477063d902c' WHERE cod_arquivo = 21;
    END IF;

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2012'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF FOUND THEN
        UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'certidao_positiva_manaquiri.odt'          , checksum = 'd855403d6987ebdab071322a966f3a55' WHERE cod_arquivo = 6;
        UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'certidao_negativa_manaquiri.odt'          , checksum = '61bef03eb44b6c08e3aed327a192aad'  WHERE cod_arquivo = 7;
        UPDATE administracao.arquivos_documento SET nome_arquivo_swx = 'certidao_positiva_negativa_manaquiri.odt' , checksum = '302c33ba93c27dc3e3b38b389240a31d' WHERE cod_arquivo = 8;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


-----------------------
-- Ticket #19360 #19361
-----------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio  = '2012'
        AND cod_modulo = 2
        AND parametro  = 'cnpj'
        AND valor      = '04641551000195'
          ;
    IF FOUND THEN
        INSERT INTO arrecadacao.modelo_carne (cod_modelo, nom_modelo, nom_arquivo, cod_modulo, capa_primeira_folha) VALUES (COALESCE((SELECT MAX(cod_modelo)+1 FROM arrecadacao.modelo_carne),1), 'Carnê de Alvará de Funcionamento'        , 'RCarneAlvaraFuncionamentoManaquiri.class.php', 14, FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 963);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 964);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 978);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 979);

        INSERT INTO arrecadacao.modelo_carne (cod_modelo, nom_modelo, nom_arquivo, cod_modulo, capa_primeira_folha) VALUES (COALESCE((SELECT MAX(cod_modelo)+1 FROM arrecadacao.modelo_carne),1), 'Carnê de Taxa de Licença para Construção', 'RCarneLicencaConstrucaoManaquiri.class.php'  , 12, FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 963);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 964);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 978);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo) FROM arrecadacao.modelo_carne), 979);

    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


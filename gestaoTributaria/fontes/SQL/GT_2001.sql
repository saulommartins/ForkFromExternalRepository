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
* Versão 2.00.1
*/

-------------------------
-- Calculo IPTU Manaquiri
-------------------------

CREATE OR REPLACE FUNCTION limpaconversao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio = '2011'
        AND parametro = 'cnpj'
        AND valor    IN ( '13805528000180' -- Mata de Sao Joao
                        )
          ;
    IF NOT FOUND THEN
    RAISE NOTICE 'entra';
        DELETE FROM arrecadacao.tabela_conversao_valores WHERE exercicio = '2006' AND cod_tabela BETWEEN 1 AND 12;
        DELETE FROM arrecadacao.tabela_conversao         WHERE exercicio = '2006' AND cod_tabela BETWEEN 1 AND 12 AND cod_modulo = 12;
    END IF;

    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio = '2011'
        AND parametro = 'cnpj'
        AND valor    IN ( '08924078000104' -- Uirauna
                        )
          ;
    IF NOT FOUND THEN
    RAISE NOTICE 'entra';
        DELETE FROM arrecadacao.tabela_conversao_valores WHERE exercicio::INTEGER > '2006' AND cod_tabela = 1;
        DELETE FROM arrecadacao.tabela_conversao         WHERE exercicio::INTEGER > '2006' AND cod_tabela = 1 AND cod_modulo = 12;
    END IF;

END;
$$ LANGUAGE 'plpgsql';
SELECT limpaconversao();
DROP FUNCTION limpaconversao();

    UPDATE administracao.cadastro SET mapeamento = 'TCIMAtributoTipoEdificacaoValor' WHERE cod_modulo = 12 and cod_cadastro = 5;

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

      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'calculaFracaoIdealLote', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );

      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'buscaFaceQuadraImovel', 1);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );

      intCodFuncao   := public.manutencao_funcao   (  12, 1, 'buscaLocalizacaoImovel', 1);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'inImovel', 1 );
      PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );


      intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_multa_mora_3_6_9_12', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtCorrecao', 5 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'flValor', 4 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );

      PERFORM           public.manutencao_funcao_externa( 28, 2, intCodFuncao );

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
-- Ticket #17854
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE exercicio = '2011'
        AND parametro = 'cnpj'
        AND valor     = '04641551000195'
          ;
    IF FOUND THEN
        INSERT INTO arrecadacao.modelo_carne      VALUES ((SELECT MAX(cod_modelo)+1 FROM arrecadacao.modelo_carne), 'Carne IPTU', 'RCarneIPTUManaquiri.class.php', 12, FALSE);
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 963 ); -- Executar Cálculo
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 964 ); -- Efetuar Lançamentos
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 978 ); -- Emissão Geral de Carnês
        INSERT INTO arrecadacao.acao_modelo_carne VALUES ((SELECT MAX(cod_modelo)   FROM arrecadacao.modelo_carne), 979 ); -- Emissão de Carnês
    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT manutencao();
DROP FUNCTION manutencao();


----------------------------------------------
-- CORRIGINDO TIPO DE CONVENIO PADRAO FEBRABAN
----------------------------------------------

UPDATE monetario.tipo_convenio set cod_modulo=25, cod_biblioteca=4, cod_funcao=54 where cod_tipo=3;


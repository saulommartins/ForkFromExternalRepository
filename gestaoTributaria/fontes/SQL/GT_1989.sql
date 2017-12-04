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
* Versão 1.98.9
*/



----------------
-- Ticket #16597
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

    PERFORM 1
       FROM administracao.funcao
      WHERE nom_funcao = 'fn_acrescimo_indice_composto'
          ;

    IF NOT FOUND THEN
      intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_acrescimo_indice_composto', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
      PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );

    END IF;


    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2010'
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;

        IF FOUND THEN
--          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_acrescimo_indice_mata', 4);
--                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
    
          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_juros_1_porcento_composto', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
    
          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_multa_mora_composto', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
    
          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_acrescimo_infracao_iptu_composto', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
    
          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_acrescimo_infracao_tff_composto', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
    
          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_acrescimo_infracao_iss_composto', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
    
          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_acrescimo_indice_mata_composto', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );

          intCodFuncao   := public.manutencao_funcao   (  28, 2, 'fn_juros_1_porcento_composto_atm_mensal', 4);
                                                     --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtVencimento', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'dtDataCalculo', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'nuValor', 5 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodAcrescimo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );
          intCodVariavel := public.manutencao_variavel (  28, 2, intCodFuncao, 'inCodTipo', 1 );
          PERFORM           public.manutencao_parametro(  28, 2, intCodFuncao, intCodVariavel );

    END IF;

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



CREATE OR REPLACE FUNCTION manutencao_ac() RETURNS VOID AS $$
DECLARE
    inCodFuncao         INTEGER;
    inCodBiblioteca     INTEGER;
    inCodModulo         INTEGER;
BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2010'
        AND parametro  = 'cnpj'
        AND valor      = '13805528000180'
          ;

    IF FOUND THEN
        SELECT cod_funcao
             , cod_biblioteca
             , cod_modulo
          INTO inCodFuncao
             , inCodBiblioteca
             , inCodModulo
          FROM administracao.funcao
--       WHERE nom_funcao = 'fn_juros_1_porcento_composto'
         WHERE nom_funcao = 'fn_juros_1_porcento_composto_atm_mensal'
             ;
    
        INSERT
          INTO monetario.formula_acrescimo
             ( cod_acrescimo 
             , cod_funcao
             , cod_modulo
             , cod_biblioteca
             , cod_tipo
             , timestamp
             )
        VALUES
             ( 1
             , inCodFuncao
             , inCodModulo
             , inCodBiblioteca
             , 2
             , '2010-04-09 00:00:00.01'
             );

        SELECT cod_funcao
             , cod_biblioteca
             , cod_modulo
          INTO inCodFuncao
             , inCodBiblioteca
             , inCodModulo
          FROM administracao.funcao
         WHERE nom_funcao = 'fn_multa_mora_composto'
             ;
    
        INSERT
          INTO monetario.formula_acrescimo
             ( cod_acrescimo 
             , cod_funcao
             , cod_modulo
             , cod_biblioteca
             , cod_tipo
             , timestamp
             )
        VALUES
             ( 2
             , inCodFuncao
             , inCodModulo
             , inCodBiblioteca
             , 3
             , '2010-04-09 00:00:00.01'
             );


        SELECT cod_funcao
             , cod_biblioteca
             , cod_modulo
          INTO inCodFuncao
             , inCodBiblioteca
             , inCodModulo
          FROM administracao.funcao
         WHERE nom_funcao = 'fn_acrescimo_infracao_iptu_composto'
             ;
    
        INSERT
          INTO monetario.formula_acrescimo
             ( cod_acrescimo 
             , cod_funcao
             , cod_modulo
             , cod_biblioteca
             , cod_tipo
             , timestamp
             )
        VALUES
             ( 4
             , inCodFuncao
             , inCodModulo
             , inCodBiblioteca
             , 3
             , '2010-04-09 00:00:00.01'
             );

        SELECT cod_funcao
             , cod_biblioteca
             , cod_modulo
          INTO inCodFuncao
             , inCodBiblioteca
             , inCodModulo
          FROM administracao.funcao
         WHERE nom_funcao = 'fn_acrescimo_infracao_tff_composto'
             ;
    
        INSERT
          INTO monetario.formula_acrescimo
             ( cod_acrescimo 
             , cod_funcao
             , cod_modulo
             , cod_biblioteca
             , cod_tipo
             , timestamp
             )
        VALUES
             ( 6
             , inCodFuncao
             , inCodModulo
             , inCodBiblioteca
             , 3
             , '2010-04-09 00:00:00.01'
             );

        SELECT cod_funcao
             , cod_biblioteca
             , cod_modulo
          INTO inCodFuncao
             , inCodBiblioteca
             , inCodModulo
          FROM administracao.funcao
         WHERE nom_funcao = 'fn_acrescimo_infracao_iss_composto'
             ;
    
        INSERT
          INTO monetario.formula_acrescimo
             ( cod_acrescimo 
             , cod_funcao
             , cod_modulo
             , cod_biblioteca
             , cod_tipo
             , timestamp
             )
        VALUES
             ( 5
             , inCodFuncao
             , inCodModulo
             , inCodBiblioteca
             , 3
             , '2010-04-09 00:00:00.01'
             );

        SELECT cod_funcao
             , cod_biblioteca
             , cod_modulo
          INTO inCodFuncao
             , inCodBiblioteca
             , inCodModulo
          FROM administracao.funcao
         WHERE nom_funcao = 'fn_acrescimo_indice_mata_composto'
             ;
    
        INSERT
          INTO monetario.formula_acrescimo
             ( cod_acrescimo 
             , cod_funcao
             , cod_modulo
             , cod_biblioteca
             , cod_tipo
             , timestamp
             )
        VALUES
             ( 3
             , inCodFuncao
             , inCodModulo
             , inCodBiblioteca
             , 1
             , '2010-04-09 00:00:00.01'
             );

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao_ac();
DROP FUNCTION manutencao_ac();


----------------
-- Ticket #16577
----------------

UPDATE administracao.acao SET nom_arquivo = 'FLConcederRemissao.php' WHERE cod_acao = 2308 AND nom_arquivo = 'FMConcederRemissao.php';

CREATE TABLE divida.relatorio_remissao_credito (
    cod_lancamento  INTEGER    NOT NULL,
    CONSTRAINT pk_relatorio_remissao_credito    PRIMARY KEY                      (cod_lancamento),
    CONSTRAINT fk_relatorio_remissao_credito_1  FOREIGN KEY                      (cod_lancamento)
                                                REFERENCES arrecadacao.lancamento(cod_lancamento)
);
GRANT ALL ON divida.relatorio_remissao_credito TO GROUP urbem;


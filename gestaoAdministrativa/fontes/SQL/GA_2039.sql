/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - SoluÃ§Ãµes em GestÃ£o PÃºblica                                *
    * @copyright (c) 2013 ConfederaÃ§Ã£o Nacional de MunicÃ­pos                         *
    * @author ConfederaÃ§Ã£o Nacional de MunicÃ­pios                                    *
    *                                                                                *
    * O URBEM CNM Ã© um software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo sob *
    * os  termos  da LicenÃ§a PÃºblica Geral GNU conforme  publicada  pela FundaÃ§Ã£o do *
    * Software Livre (FSF - Free Software Foundation); na versÃ£o 2 da LicenÃ§a.       *
    *                                                                                *
    * Este  programa  Ã©  distribuÃ­do  na  expectativa  de  que  seja  Ãºtil,   porÃ©m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implÃ­cita  de  COMERCIABILIDADE  OU *
    * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU "LICENCA.txt" *
    * com  este  programa; se nÃ£o, escreva para  a  Free  Software Foundation  Inc., *
    * no endereÃ§o 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* Script de DDL e DML
*
* Versao 2.03.8
*
* Fabio Bertoldi - 20150415
*
*/

----------------
-- Ticket #22637
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_class
      WHERE relname = 'sw_processo_confidencial'
          ;
    IF NOT FOUND THEN
        CREATE TABLE sw_processo_confidencial(
            cod_processo        INTEGER     NOT NULL,
            ano_exercicio       CHAR(4)     NOT NULL,
            numcgm              INTEGER     NOT NULL,
            CONSTRAINT pk_processo_confidencial     PRIMARY KEY             (cod_processo, ano_exercicio, numcgm),
            CONSTRAINT fk_processo_confidencial_1   FOREIGN KEY             (cod_processo, ano_exercicio)
                                                    REFERENCES sw_processo  (cod_processo, ano_exercicio),
            CONSTRAINT fk_processo_confidencial_2   FOREIGN KEY             (numcgm)
                                                    REFERENCES sw_cgm       (numcgm)
        );
        GRANT ALL ON sw_processo_confidencial TO urbem;
    END IF;

     PERFORM 1
        FROM pg_class
           , pg_attribute
           , pg_type
       WHERE pg_class.relname      = 'sw_processo_arquivado'
         AND pg_attribute.attname  = 'localizacao'
         AND pg_attribute.attnum   > 0
         AND pg_attribute.attrelid = pg_class.oid
         AND pg_attribute.atttypid = pg_type.oid
           ;
    IF NOT FOUND THEN
        ALTER TABLE sw_processo_arquivado ADD COLUMN localizacao VARCHAR(80);
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #22882
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

      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'pega1ValorPlanoSaude', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )


      --PERFORM           public.manutencao_funcao_externa( 25, 1, intCodFuncao );

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
-- Ticket #22098
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_user
      WHERE usename = 'birt'
          ;
    IF NOT FOUND THEN
        CREATE ROLE birt login;
        GRANT urbem TO birt;
        UPDATE pg_authid SET rolpassword = 'md517e68e814c79648270e8a9c28b6386e7' WHERE rolname = 'birt';
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

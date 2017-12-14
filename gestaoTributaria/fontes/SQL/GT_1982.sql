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
* Versão 1.98.2
*/

----------------
-- Ticket #16147
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM pg_class
          , pg_attribute
          , pg_type
      WHERE pg_class.relname      = 'modalidade_reducao_acrescimo'
        AND pg_attribute.attname  = 'percentual'
        AND pg_attribute.attnum   > 0
        AND pg_attribute.attrelid = pg_class.oid
        AND pg_attribute.atttypid = pg_type.oid
          ;

    IF NOT FOUND THEN

        ALTER TABLE divida.modalidade_reducao_acrescimo DROP CONSTRAINT fk_modalidade_reducao_acrescimo_1;
        ALTER TABLE divida.modalidade_reducao_acrescimo DROP CONSTRAINT pk_modalidade_reducao_acrescimo;
        ALTER TABLE divida.modalidade_reducao_credito   DROP CONSTRAINT fk_modalidade_reducao_credito_1;
        ALTER TABLE divida.modalidade_reducao_credito   DROP CONSTRAINT pk_modalidade_reducao_credito;
        ALTER TABLE divida.modalidade_reducao           DROP CONSTRAINT pk_modalidade_reducao;
        
        ALTER TABLE divida.modalidade_reducao_acrescimo ADD COLUMN percentual BOOLEAN;
        ALTER TABLE divida.modalidade_reducao_acrescimo ADD COLUMN valor      NUMERIC(14,2);
        UPDATE divida.modalidade_reducao_acrescimo
           SET percentual = modalidade_reducao.percentual
             , valor      = modalidade_reducao.valor
          FROM divida.modalidade_reducao
         WHERE modalidade_reducao.timestamp      = modalidade_reducao_acrescimo.timestamp     
           AND modalidade_reducao.cod_modalidade = modalidade_reducao_acrescimo.cod_modalidade
           AND modalidade_reducao.cod_funcao     = modalidade_reducao_acrescimo.cod_funcao    
           AND modalidade_reducao.cod_biblioteca = modalidade_reducao_acrescimo.cod_biblioteca
           AND modalidade_reducao.cod_modulo     = modalidade_reducao_acrescimo.cod_modulo    
             ;
        ALTER TABLE divida.modalidade_reducao_acrescimo ALTER COLUMN percentual SET NOT NULL;
        ALTER TABLE divida.modalidade_reducao_acrescimo ALTER COLUMN valor      SET NOT NULL;
        
        ALTER TABLE divida.modalidade_reducao_credito   ADD COLUMN percentual BOOLEAN;
        ALTER TABLE divida.modalidade_reducao_credito   ADD COLUMN valor      NUMERIC(14,2);
        UPDATE divida.modalidade_reducao_credito
           SET percentual = modalidade_reducao.percentual
             , valor      = modalidade_reducao.valor
          FROM divida.modalidade_reducao
         WHERE modalidade_reducao.timestamp      = modalidade_reducao_credito.timestamp
           AND modalidade_reducao.cod_modalidade = modalidade_reducao_credito.cod_modalidade
           AND modalidade_reducao.cod_funcao     = modalidade_reducao_credito.cod_funcao
           AND modalidade_reducao.cod_biblioteca = modalidade_reducao_credito.cod_biblioteca
           AND modalidade_reducao.cod_modulo     = modalidade_reducao_credito.cod_modulo
             ;
        ALTER TABLE divida.modalidade_reducao_credito   ALTER COLUMN percentual SET NOT NULL;
        ALTER TABLE divida.modalidade_reducao_credito   ALTER COLUMN valor      SET NOT NULL;
        
        ALTER TABLE divida.modalidade_reducao           ADD  CONSTRAINT pk_modalidade_reducao               PRIMARY KEY                         (timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo, percentual, valor);
        ALTER TABLE divida.modalidade_reducao_acrescimo ADD  CONSTRAINT pk_modalidade_reducao_acrescimo     PRIMARY KEY                         (timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo, percentual, valor, cod_tipo, cod_acrescimo, pagamento);
        ALTER TABLE divida.modalidade_reducao_acrescimo ADD  CONSTRAINT fk_modalidade_reducao_acrescimo_1   FOREIGN KEY                         (timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo, percentual, valor)
                                                                                                            REFERENCES divida.modalidade_reducao(timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo, percentual, valor);
        ALTER TABLE divida.modalidade_reducao_credito   ADD  CONSTRAINT pk_modalidade_reducao_credito       PRIMARY KEY                         (timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo, percentual, valor, cod_credito, cod_natureza, cod_genero, cod_especie);
        ALTER TABLE divida.modalidade_reducao_credito   ADD  CONSTRAINT fk_modalidade_reducao_credito_1     FOREIGN KEY                         (timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo, percentual, valor)
                                                                                                            REFERENCES divida.modalidade_reducao(timestamp, cod_modalidade, cod_funcao, cod_biblioteca, cod_modulo, percentual, valor);

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


----------------------------------------------------------------------------------------------
-- CADASTRANDO FUNCAO recuperaTrechoValorMetroQuadradoTerritorialExercicio COMO FUNCAO INTERNA
----------------------------------------------------------------------------------------------

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
          WHERE nom_funcao = 'recuperaTrechoValorMetroQuadradoTerritorialExercicio'
              ;
        IF NOT FOUND THEN
            intCodFuncao   := public.manutencao_funcao   (  12, 1, 'recuperaTrechoValorMetroQuadradoTerritorialExercicio', 2);
                                                       --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

            intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'intInscricaoMunicipal', 1 );
            PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );
            intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'intExercicio', 1 );
            PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );
        END IF;

        PERFORM 1
           FROM administracao.funcao
          WHERE nom_funcao = 'recuperaTrechoValorMetroQuadradoPredialExercicio'
              ;
        IF NOT FOUND THEN
            intCodFuncao   := public.manutencao_funcao   (  12, 1, 'recuperaTrechoValorMetroQuadradoPredialExercicio', 2);
                                                       --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

            intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'intInscricaoMunicipal', 1 );
            PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );
            intCodVariavel := public.manutencao_variavel (  12, 1, intCodFuncao, 'intExercicio', 1 );
            PERFORM           public.manutencao_parametro(  12, 1, intCodFuncao, intCodVariavel );
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



----------------
-- Ticket #16134
----------------

UPDATE administracao.acao
   SET ordem = ordem + 1
 WHERE ordem > 1
   AND ordem <> 10
     ;

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES
     ( 2775
     , 366
     , 'FLRelatorioDividaCancelada.php'
     , 'incluir'
     , 2
     , ''
     , 'Dívida Ativa Cancelada'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 5
     , 33
     , 6
     , 'Dívida Ativa Cancelada'
     , 'DividaAtivaCancelada.rptdesign'
     );


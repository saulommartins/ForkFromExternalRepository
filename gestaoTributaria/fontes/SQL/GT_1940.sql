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
* $Id: GT_1940.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.0
*/


----------------
-- Ticket #13761
----------------

CREATE TABLE arrecadacao.tomador_empresa(
    cod_nota                INTEGER         NOT NULL,
    inscricao_economica     INTEGER         NOT NULL,
    CONSTRAINT pk_tomador_empresa           PRIMARY KEY                             (cod_nota),
    CONSTRAINT fk_tomador_empresa_1         FOREIGN KEY                             (cod_nota)
                                            REFERENCES arrecadacao.nota_avulsa      (cod_nota),
    CONSTRAINT fk_tomador_empresa_2         FOREIGN KEY                             (inscricao_economica)
                                            REFERENCES economico.cadastro_economico (inscricao_economica)
);

GRANT ALL ON arrecadacao.tomador_empresa TO GROUP urbem;


----------------
-- Ticket #12095
----------------

UPDATE administracao.acao
   SET ordem = ordem + 2
 WHERE cod_funcionalidade = 225
   AND ordem > 2;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2391
          , 225
          , 'FMSimularCalculo.php'
          , 'simular'
          , 3
          , ''
          , 'Simular Cálculo'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2392
          , 225
          , 'FLValidarCalculo.php'
          , 'validar'
          , 4
          , ''
          , 'Validar Cálculo Simulado'
          );

ALTER TABLE arrecadacao.calculo ADD COLUMN simulado BOOLEAN NOT NULL DEFAULT FALSE;


----------------
-- Ticket #12118
----------------

ALTER TABLE arrecadacao.modelo_carne ADD COLUMN cod_modulo INTEGER;
ALTER TABLE arrecadacao.modelo_carne ADD constraint fk_modelo_carne_1 foreign key (cod_modulo) references administracao.modulo (cod_modulo);

CREATE TABLE arrecadacao.variaveis_layout_carne (
    cod_modelo      INTEGER     NOT NULL,
    cod_modulo      INTEGER     NOT NULL,
    cod_cadastro    INTEGER     NOT NULL,
    cod_atributo    INTEGER     NOT NULL,
    ordem           INTEGER     NOT NULL,
    posicao_inicial INTEGER     NOT NULL,
    largura         INTEGER     NOT NULL,
    CONSTRAINT pk_variaveis_layout_carne    PRIMARY KEY (cod_modelo, cod_modulo, cod_cadastro, cod_atributo),
    CONSTRAINT fk_variaveis_layout_carne_1  FOREIGN KEY                                 (cod_modelo)
                                            REFERENCES arrecadacao.modelo_carne         (cod_modelo),
    CONSTRAINT fk_variaveis_layout_carne_2  FOREIGN KEY                                 (cod_modulo, cod_cadastro, cod_atributo)
                                            REFERENCES administracao.atributo_dinamico  (cod_modulo, cod_cadastro, cod_atributo)
);

CREATE TABLE arrecadacao.observacao_layout_carne (
    cod_modelo      INTEGER     NOT NULL,
    observacao      TEXT,
    capa            boolean     NOT NULL,
    CONSTRAINT pk_observacao_layout_carne   PRIMARY KEY (cod_modelo, capa),
    CONSTRAINT fk_observacao_layout_carne_1 FOREIGN KEY (cod_modelo) REFERENCES arrecadacao.modelo_carne (cod_modelo)
);

GRANT ALL ON TABLE arrecadacao.variaveis_layout_carne  TO GROUP urbem;
GRANT ALL ON TABLE arrecadacao.observacao_layout_carne TO GROUP urbem;




---------------------------------------------------------------
-- INSERCAO DA FUNCAO arrecadacao.buscaForo COMO FUNCAO INTERNA
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

    --Inclusão de função interna arrecadacao/fn_busca_foro.plsql

      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'arrecadacao.buscaForo', 3);

      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inLancamento', 1 );
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



----------------------------------------------------------
-- DEFINICAO DE CONVENIO P/ CREDITO NOTA AVULSA - 20081029
----------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencaoNota( ) RETURNS VOID AS $$
DECLARE

    stGrupoEscrit   VARCHAR;
    inArrayGrupo    INTEGER[];
    inCount         INTEGER;

BEGIN

    SELECT valor
      INTO stGrupoEscrit
      FROM administracao.configuracao
     WHERE cod_modulo = 25
       AND exercicio  = '2008'
       AND parametro  = 'escrituracao_receita';

    IF FOUND THEN

            inArrayGrupo := string_to_array( stGrupoEscrit, '/' );
        
            UPDATE monetario.credito
               SET cod_convenio = (
                                        SELECT MIN (MC.cod_convenio)
                                          FROM monetario.credito             AS MC
                                    INNER JOIN arrecadacao.credito_grupo     AS ACG
                                            ON ACG.cod_credito   = MC.cod_credito
                                           AND ACG.cod_especie   = MC.cod_especie
                                           AND ACG.cod_genero    = MC.cod_genero
                                           AND ACG.cod_natureza  = MC.cod_natureza
                                           AND ACG.cod_grupo     = inArrayGrupo[1]
                                           AND ACG.ano_exercicio = inArrayGrupo[2]
                                  )
             WHERE cod_credito  = 99
               AND cod_especie  = 1
               AND cod_genero   = 2
               AND cod_natureza = 1;
        
            GET DIAGNOSTICS inCount = ROW_COUNT;

    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencaoNota();
DROP FUNCTION manutencaoNota();


----------------------------------------------------
-- CRIANDO FORMULA DE CALCULO P/ CREDITO NOTA AVULSA
----------------------------------------------------

CREATE OR REPLACE FUNCTION notaavulsa ( ) RETURNS NUMERIC AS $$
DECLARE

    inExercicio     INTEGER;
    nuValor         NUMERIC;

BEGIN

    inExercicio := RecuperarBufferInteiro(  'inExercicio'  );
    nuValor     := arrecadacao.fn_busca_tabela_conversao(  0 , inExercicio , 'valor' , '' , '' , ''  );
    RETURN nuValor;
END;
$$ LANGUAGE 'plpgsql';


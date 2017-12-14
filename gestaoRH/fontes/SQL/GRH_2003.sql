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
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* VersÃ£o 2.00.3
*/

---------------------------------------------------------------------------------------------------------------------
-- ADICIONANDO FUNCOES pega1QuantidadeQuinquenios.plsql E pega1QuantidadeAnosParaAnuenios.plsql COMO FUNCOES INTERNAS
---------------------------------------------------------------------------------------------------------------------

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
      WHERE nom_funcao = 'pega1QuantidadeQuinquenios'
          ;
    IF NOT FOUND THEN
      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'pega1QuantidadeQuinquenios', 1);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  27, 1, intCodFuncao, 'dtLei', 5 );
      PERFORM           public.manutencao_parametro(  27, 1, intCodFuncao, intCodVariavel );
    END IF;


    PERFORM 1
       FROM administracao.funcao
      WHERE nom_funcao = 'pega1QuantidadeAnosParaAnuenios'
          ;
    IF NOT FOUND THEN
      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'pega1QuantidadeAnosParaAnuenios', 1);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  27, 1, intCodFuncao, 'dtLei', 5 );
      PERFORM           public.manutencao_parametro(  27, 1, intCodFuncao, intCodVariavel );
    END IF;


      RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   --
   -- Execuçao  função.
   --
   SELECT        public.manutencao();
   Drop Function public.manutencao();
   Drop Function public.manutencao_funcao(integer, integer, varchar, integer );
   Drop Function public.manutencao_variavel( integer, integer, integer, varchar, integer );
   Drop Function public.manutencao_parametro( integer, integer, integer, integer );
   Drop Function public.manutencao_funcao_externa( integer, integer, integer ) ;


----------------
-- Ticket #16720
----------------

DROP TYPE colunasCustomizavelEventos CASCADE;

CREATE TYPE colunasCustomizavelEventos AS (
cod_contrato              INTEGER,
registro                  INTEGER,
nom_cgm                   VARCHAR,
cpf                       VARCHAR,
desc_orgao                VARCHAR,
desc_local                VARCHAR,
desc_funcao               VARCHAR,
desc_cargo                VARCHAR,
desc_especialidade_cargo  VARCHAR,
desc_especialidade_funcao VARCHAR,
desc_padrao               VARCHAR,
valor1                    NUMERIC,
quantidade1               NUMERIC,
valor2                    NUMERIC,
quantidade2               NUMERIC,
valor3                    NUMERIC,
quantidade3               NUMERIC,
valor4                    NUMERIC,
quantidade4               NUMERIC,
valor5                    NUMERIC,
quantidade5               NUMERIC,
valor6                    NUMERIC,
quantidade6               NUMERIC
);



--------------------------------------------
-- Ticket #17120 - CORRECAO DA VERSAO 1.99.6
--------------------------------------------

CREATE OR REPLACE FUNCTION atualizarBanco_2003(VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    stSqlParametro              ALIAS FOR $1;
    inExercicio                 INTEGER;
    inCodEntidadePrefeitura     INTEGER;
    stSql                       VARCHAR;
    stInsert                    VARCHAR;
    stBanco                     VARCHAR;
    stEntidade                  VARCHAR;
    stNomeSchema                VARCHAR;
    stNomeTriger                VARCHAR;
    stArray                     VARCHAR[];
    boEsquema                   BOOLEAN:=FALSE;
    boTrigger                   BOOLEAN:=FALSE;
    boGranteEsquema             BOOLEAN:=FALSE;
    boRetorno                   BOOLEAN;
    reRegistro                  RECORD;
    reSchema                    RECORD;
BEGIN
--    EXECUTE stSqlParametro;

    inExercicio := selectIntoInteger('SELECT valor FROM administracao.configuracao WHERE parametro = \'ano_exercicio\' ORDER BY exercicio desc LIMIT 1');
    inCodEntidadePrefeitura := selectIntoInteger('SELECT valor::integer as valor
                                                    FROM administracao.configuracao
                                                   WHERE parametro = \'cod_entidade_prefeitura\'
                                                     AND exercicio = '''||inExercicio||'''');


    IF strpos(trim(upper(stSqlParametro)),upper('CREATE SCHEMA')) > 0 THEN
        boEsquema    := TRUE;
        stNomeSchema := trim(translate(stSqlParametro,'CREATE SCHEMA ;',''));
        stInsert     := 'INSERT INTO administracao.schema_rh (schema_cod,schema_nome) VALUES ((SELECT max(schema_cod) FROM administracao.schema_rh)+1,\''||stNomeSchema||'\')';
        EXECUTE stInsert;

        stSql := 'SELECT TRUE as retorno
                    FROM administracao.entidade_rh
                   WHERE cod_entidade = '||inCodEntidadePrefeitura||'
                   LIMIT 1';
        boRetorno := selectIntoBoolean(stSql);
        IF boRetorno IS TRUE THEN
            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod).
                         VALUES
                         (\''||inExercicio||'\','||inCodEntidadePrefeitura||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
            EXECUTE stInsert;
        END IF;
    END IF;
    IF strpos(trim(upper(stSqlParametro)),upper('CREATE TRIGGER')) > 0 THEN
        boTrigger    := TRUE;
        stArray      := string_to_array( stSqlParametro, ' ');
        stNomeTriger := stArray[3];
    END IF;
    IF strpos(trim(upper(stSqlParametro)),upper('GRANT ALL ON SCHEMA')) > 0 THEN
        boGranteEsquema := TRUE;
        stArray         := string_to_array( stSqlParametro, ' ');
        stNomeSchema    := stArray[5];
    END IF;

    stSql := '  SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = '''||inExercicio||'''
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = '''||inExercicio||'''
                                        GROUP BY cod_entidade)
                   AND cod_entidade != ('||inCodEntidadePrefeitura||')';
    FOR reRegistro IN EXECUTE stSql LOOP
        stBanco := stSqlParametro;
        IF boEsquema THEN
            stBanco := trim(replace(stSqlParametro,';',''))||'_'||reRegistro.cod_entidade||';';

            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod).
                         VALUES
                         (\''||inExercicio||'\','||reRegistro.cod_entidade||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
            EXECUTE stInsert;
        ELSIF boTrigger THEN
            stBanco := trim(replace(stSqlParametro,stNomeTriger,stNomeTriger||'_'||reRegistro.cod_entidade));
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '||reSchema.schema_nome||'.',' '||reSchema.schema_nome||'_'||reRegistro.cod_entidade||'.');
            END LOOP;
        ELSIF boGranteEsquema THEN
            stBanco := replace(stBanco, stNomeSchema,' '||stNomeSchema||'_'||reRegistro.cod_entidade);
        ELSE
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '||reSchema.schema_nome||'.',' '||reSchema.schema_nome||'_'||reRegistro.cod_entidade||'.');
            END LOOP;
        END IF;
        EXECUTE stBanco;
    END LOOP;
    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';


SELECT atualizarBanco_2003('
ALTER TABLE ima.configuracao_dirf ADD COLUMN pagamento_mes_competencia BOOLEAN not null default TRUE;
');


SELECT atualizarBanco_2003('
CREATE TABLE ima.configuracao_dirf_plano (
    exercicio    CHAR(4)    not null,
    numcgm       INTEGER    not null,
    cod_evento   INTEGER    not null,
    registro_ans NUMERIC(6) not null,

    CONSTRAINT pk_configuracao_dirf_plano PRIMARY KEY (exercicio,numcgm,registro_ans),
    CONSTRAINT fk_configuracao_dirf_plano_1 FOREIGN KEY (exercicio) REFERENCES ima.configuracao_dirf(exercicio),
    CONSTRAINT fk_configuracao_dirf_plano_2 FOREIGN KEY (numcgm) REFERENCES sw_cgm_pessoa_juridica(numcgm),
    CONSTRAINT fk_configuracao_dirf_plano_3 FOREIGN KEY (cod_evento) REFERENCES folhapagamento.evento(cod_evento)
);
');

SELECT atualizarBanco_2003('
ALTER TABLE ima.configuracao_dirf ADD COLUMN cod_evento_molestia integer;
');

SELECT atualizarBanco_2003('
ALTER TABLE ima.configuracao_dirf ADD CONSTRAINT fk_configuracao_dirf_4 FOREIGN KEY (cod_evento_molestia) REFERENCES folhapagamento.evento(cod_evento);
');

SELECT atualizarBanco_2003('
GRANT ALL ON ima.configuracao_dirf_plano TO urbem;
');

DROP FUNCTION atualizarBanco_2003(VARCHAR);


CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE tablename = 'tmp_cpf_controle_dependentes'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tmp_cpf_controle_dependentes (
              cpf                   VARCHAR
            , sequencia_evento      INTEGER
        );
        GRANT ALL ON tmp_cpf_controle_dependentes TO urbem;
    END IF;

    PERFORM 1
       FROM pg_tables
      WHERE tablename = 'tmp_valores_decimo'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tmp_valores_decimo (
                  cod_contrato          INTEGER
                , valor                 DECIMAL(14,2)
        );
        GRANT ALL ON tmp_valores_decimo TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();


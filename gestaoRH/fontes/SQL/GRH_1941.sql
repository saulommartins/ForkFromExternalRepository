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
* $Id: GRH_1941.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.1
*/
create or replace function atualizarBanco(VARCHAR) RETURNS BOOLEAN as $$
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
    EXECUTE stSqlParametro;

    inExercicio := selectIntoInteger('select valor from administracao.configuracao where parametro = \'ano_exercicio\' order by exercicio desc limit 1');
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
            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) 
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

            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod) 
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




---------------------------------------------------
-- CONCEDENDO PERMISSOES P/ SCHEMAS diarias E ponto
---------------------------------------------------

select atualizarBanco('GRANT ALL ON SCHEMA diarias TO GROUP urbem;');
select atualizarBanco('GRANT ALL ON SCHEMA ponto   TO GROUP urbem;');



----------------
-- Ticket #13808 - Bug de Atributo Multiplo Estagiario persistindo descricoes
----------------

create or replace function manutencao() returns void as $$
declare
    stSql varchar;
    stEntidade varchar:='';
    reRegistro record;
    reEntidades record;
    inCodValor integer;
    inCodEntidadePrincipal integer;
begin
    stSql := 'SELECT valor
                FROM administracao.configuracao
               WHERE parametro = \'cod_entidade_prefeitura\'
                 AND exercicio = \'2008\'';
    inCodEntidadePrincipal := selectIntoInteger(stSql);

    stSql := 'SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = \'2008\'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = \'2008\'
                                        GROUP BY cod_entidade)';

    for reEntidades in execute stSql loop
        if reEntidades.cod_entidade != inCodEntidadePrincipal then
            stEntidade := '_'||reEntidades.cod_entidade;
        else
            stEntidade := '';
        end if;

        stSql := 'select atributo_estagiario_estagio.*
                    from estagio'||stEntidade||'.atributo_estagiario_estagio
                where cod_atributo = 2
                    and trim(valor) not in (select cod_valor 
                                            from administracao.atributo_valor_padrao 
                                            where cod_atributo = 2
                                                and cod_cadastro = 1
                                                and cod_modulo = 39)
                    and trim(valor) <> \'\'';
        for reRegistro in execute stSql loop
            stSql := 'select cod_valor
                        from administracao.atributo_valor_padrao
                    where valor_padrao = \''||trim(reRegistro.valor)||'\'
                        and cod_atributo = '||reRegistro.cod_atributo||' 
                        and cod_cadastro = '||reRegistro.cod_cadastro||' 
                        and cod_modulo = '||reRegistro.cod_modulo;
            inCodValor := selectIntoInteger(stSql);
            if inCodValor is not null then
                 raise notice '%',reRegistro.valor;
                 raise notice 'Codigo: %',inCodValor;
                stSql := 'update estagio'||stEntidade||'.atributo_estagiario_estagio
                            set valor = '||inCodValor||'
                        where cod_atributo = '||reRegistro.cod_atributo||' 
                            and cod_cadastro = '||reRegistro.cod_cadastro||' 
                            and cod_modulo = '||reRegistro.cod_modulo||'
                            and cod_cadastro = '||reRegistro.cod_cadastro||' 
                            and timestamp = \''||trim(reRegistro.timestamp)||'\'
                            and valor = \''||trim(reRegistro.valor)||'\'';
                 raise notice '%',stSql;
                execute stSql;
            end if;
        end loop;
    end loop;
end
$$ language 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


CREATE TYPE colunasConsultarServidoresPorCargo AS (
    numcgm       integer,
    registro     integer,
    nom_cgm      varchar(200)
);


SELECT atualizarBanco('ALTER TABLE pessoal.cargo_sub_divisao         DROP COLUMN nro_vagas;');
SELECT atualizarBanco('ALTER TABLE pessoal.especialidade_sub_divisao DROP COLUMN nro_vagas;');


-------------------------------------------------
-- SOLICITADO POR DIEGO LEMOS DE SOUZA - 20081105
-------------------------------------------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 27
         , 19
         , 'Resumo para Emissão das Autorizações de Empenho'
         , 'emitirAutorizacaoEmpenhoDiarias.rptdesign'
         );

-------------------------------------------------------------------------------------------------------------------------------------------
-- Ticket #13850 - Bug de Atributo Multiplo Pensionista salvando String 'Array' em campo valor de pensionista.atributo_contrato_pensionista
-- Alex Cardoso - 20081106 ----------------------------------------------------------------------------------------------------------------

create or replace function manutencao() returns void as $$
declare
    stSql varchar;
    stEntidade varchar:='';
    reRegistro record;
    reEntidades record;
    inCodValor integer;
    inCodEntidadePrincipal integer;
begin
    stSql := 'SELECT valor
                FROM administracao.configuracao
               WHERE parametro = \'cod_entidade_prefeitura\'
                 AND exercicio = \'2008\'';
    inCodEntidadePrincipal := selectIntoInteger(stSql);

    stSql := 'SELECT entidade.cod_entidade
                  FROM orcamento.entidade
                 WHERE exercicio = \'2008\'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = \'2008\'
                                        GROUP BY cod_entidade)';

    for reEntidades in execute stSql loop
        if reEntidades.cod_entidade != inCodEntidadePrincipal then
            stEntidade := '_'||reEntidades.cod_entidade;
        else
            stEntidade := '';
        end if;

        stSql := 'delete from pessoal'||stEntidade||'.atributo_contrato_pensionista WHERE trim(valor) = \'Array\'';
        execute stSql;
    end loop;
end
$$ language 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();



-----------------------------------------------------------------------
-- INSERINDO pega1PercDescontoBaseVTNaData(numeric) COMO FUNCAO INTERNA
-- DIEGO - 20081111 ---------------------------------------------------

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

      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'pega1PercDescontoBaseVTNaData', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  27, 1, intCodFuncao, 'nuParametro', 4 );
      PERFORM           public.manutencao_parametro(  27, 1, intCodFuncao, intCodVariavel );


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
-- Ticket #6165
----------------

UPDATE administracao.acao
   SET nom_acao = 'Alterar Ponto Facultativo'
 WHERE cod_acao = 938;


----------------
-- Ticket #13920
----------------
SELECT atualizarBanco('update folhapagamento.tipo_media set cod_funcao = (select cod_funcao from administracao.funcao where nom_funcao = \'mediaDecimoSomatorioValidosPorNrOcorrenciasHMensaisValidas\') where cod_tipo = 10;');

SELECT atualizarBanco('update folhapagamento.tipo_media set cod_funcao = (select cod_funcao from administracao.funcao where nom_funcao = \'mediaDecimoSomatorioValidosPorNrOcorrenciasValidas\') where cod_tipo = 9;');


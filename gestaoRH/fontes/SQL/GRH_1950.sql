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
* $Id: GRH_1950.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.95.0
*/

----------------
-- Ticket #13780
----------------

CREATE TYPE colunasCreditosPorBanco AS (
    registro            VARCHAR,
    nom_cgm             VARCHAR,
    servidor            VARCHAR,
    cpf                 VARCHAR,
    nr_conta            VARCHAR,
    num_agencia         VARCHAR,
    nom_agencia         VARCHAR,
    cod_agencia         INTEGER,
    num_banco           VARCHAR,
    nom_banco           VARCHAR,
    cod_banco           INTEGER,
    valor               NUMERIC,
    lotacao             VARCHAR,
    cod_estrutural      VARCHAR,
    cod_orgao           INTEGER,
    local               VARCHAR,
    cod_local           INTEGER
);


-------------------------------------------------
-- SOLICITADO POR DIEGO LEMOS DE SOUZA - 20081121
-------------------------------------------------

CREATE TYPE colunaContribuicaoPrevidenciaria AS (
    cod_contrato         INTEGER,
    registro             INTEGER,
    nom_cgm              VARCHAR,
    categoria            VARCHAR,
    num_ocorrencia       INTEGER,
    contador             INTEGER,
    maternidade          NUMERIC,
    desconto             NUMERIC,
    base                 NUMERIC,
    familia              NUMERIC,
    subDivisao          VARCHAR,
    local               VARCHAR,
    orgao               VARCHAR
);

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 27
         , 20
         , 'Contribuição Previdenciária'
         , 'contribuicaoPrevidenciariaGrupo.rptdesign'
         );


-------------------------------------------------
-- SOLICITADO POR DIEGO LEMOS DE SOUZA - 20081126
-------------------------------------------------

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste (
    cod_reajuste        INTEGER         NOT NULL,
    numcgm              INTEGER         NOT NULL,
    dt_reajuste         DATE            NOT NULL,
    percentual          NUMERIC(14,2)   NOT NULL,
    faixa_inicial       NUMERIC(14,2)   NOT NULL, 
    faixa_final         NUMERIC(14,2)   NOT NULL,
    origem              CHAR(1)         NOT NULL,
    CONSTRAINT pk_reajuste              PRIMARY KEY                      (cod_reajuste),
    CONSTRAINT fk_reajuste_1            FOREIGN KEY                      (numcgm)
                                        REFERENCES administracao.usuario (numcgm),
    CONSTRAINT ck_reajuste_1            CHECK (origem IN (\'S\',\'C\',\'F\',\'D\',\'R\',\'P\'))
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_exclusao (
    cod_reajuste        INTEGER         NOT NULL,
    numcgm              INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT  (\'now\'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_reajuste_exclusao     PRIMARY KEY                         (cod_reajuste),
    CONSTRAINT fk_reajuste_exclusao_1   FOREIGN KEY                         (cod_reajuste)
                                        REFERENCES folhapagamento.reajuste  (cod_reajuste),
    CONSTRAINT fk_reajuste_exclusao_2   FOREIGN KEY                         (numcgm)
                                        REFERENCES administracao.usuario    (numcgm)
 );
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_exclusao TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_padrao_padrao (
    cod_reajuste        INTEGER         NOT NULL,
    cod_padrao          INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    CONSTRAINT pk_reajuste_padrao_padrao    PRIMARY KEY                             (cod_reajuste, cod_padrao, timestamp),
    CONSTRAINT fk_reajuste_padrao_padrao_1  FOREIGN KEY                             (cod_padrao, timestamp)
                                            REFERENCES folhapagamento.padrao_padrao (cod_padrao, timestamp),
    CONSTRAINT fk_reajuste_padrao_padrao_2  FOREIGN KEY                             (cod_reajuste)
                                            REFERENCES folhapagamento.reajuste      (cod_reajuste)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_padrao_padrao TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_contrato_servidor_salario (
    cod_reajuste        INTEGER         NOT NULL,
    cod_contrato        INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    CONSTRAINT pk_reajuste_contrato_servidor_salario    PRIMARY KEY                                     (cod_reajuste, cod_contrato, timestamp),
    CONSTRAINT fk_reajuste_contrato_servidor_salario_1  FOREIGN KEY                                     (cod_contrato, timestamp)
                                                        REFERENCES pessoal.contrato_servidor_salario    (cod_contrato, timestamp),
    CONSTRAINT fk_reajuste_contrato_servidor_salario_2  FOREIGN KEY                                     (cod_reajuste)
                                                        REFERENCES folhapagamento.reajuste              (cod_reajuste)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_contrato_servidor_salario TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_registro_evento (
    cod_reajuste        INTEGER         NOT NULL,
    cod_registro        INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_evento          INTEGER         NOT NULL,
    CONSTRAINT pk_reajuste_registro_evento      PRIMARY KEY                                 (cod_reajuste, cod_registro, timestamp, cod_evento),
    CONSTRAINT fk_reajuste_registro_evento_1    FOREIGN KEY                                 (cod_registro, timestamp, cod_evento)
                                                REFERENCES folhapagamento.registro_evento   (cod_registro, timestamp, cod_evento),
    CONSTRAINT fk_reajuste_registro_evento_2    FOREIGN KEY                                 (cod_reajuste)
                                                REFERENCES folhapagamento.reajuste          (cod_reajuste)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_registro_evento TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_registro_evento_ferias (
    cod_reajuste        INTEGER         NOT NULL,
    cod_registro        INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_evento          INTEGER         NOT NULL,
    desdobramento       CHAR(1)         NOT NULL,
    CONSTRAINT pk_reajuste_registro_evento_ferias       PRIMARY KEY                                         (cod_reajuste, cod_registro, timestamp, cod_evento, desdobramento),
    CONSTRAINT fk_reajuste_registro_evento_ferias_1     FOREIGN KEY                                         (cod_registro, timestamp, cod_evento, desdobramento)
                                                        REFERENCES folhapagamento.registro_evento_ferias    (cod_registro, timestamp, cod_evento, desdobramento),
    CONSTRAINT fk_reajuste_registro_evento_ferias_2     FOREIGN KEY                                         (cod_reajuste)
                                                        REFERENCES folhapagamento.reajuste                  (cod_reajuste)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_registro_evento_ferias TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_registro_evento_decimo (
    cod_reajuste        INTEGER         NOT NULL,
    cod_registro        INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_evento          INTEGER         NOT NULL,
    desdobramento       CHAR(1)         NOT NULL,
    CONSTRAINT pk_reajuste_registro_evento_decimo       PRIMARY KEY                                         (cod_reajuste, cod_registro, timestamp, cod_evento, desdobramento),
    CONSTRAINT fk_reajuste_registro_evento_decimo_1     FOREIGN KEY                                         (cod_registro, timestamp, cod_evento, desdobramento)
                                                        REFERENCES folhapagamento.registro_evento_decimo    (cod_registro, timestamp, cod_evento, desdobramento),
    CONSTRAINT fk_reajuste_registro_evento_decimo_2     FOREIGN KEY                                         (cod_reajuste)
                                                        REFERENCES folhapagamento.reajuste                  (cod_reajuste)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_registro_evento_decimo TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_registro_evento_rescisao (
    cod_reajuste        INTEGER         NOT NULL,
    cod_registro        INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_evento          INTEGER         NOT NULL,
    desdobramento       CHAR(1)         NOT NULL,
    CONSTRAINT pk_reajuste_registro_evento_rescisao     PRIMARY KEY                                         (cod_reajuste, cod_registro, timestamp, cod_evento, desdobramento),
    CONSTRAINT fk_reajuste_registro_evento_rescisao_1   FOREIGN KEY                                         (cod_registro, timestamp, cod_evento, desdobramento)
                                                        REFERENCES folhapagamento.registro_evento_rescisao  (cod_registro, timestamp, cod_evento, desdobramento),
    CONSTRAINT fk_reajuste_registro_evento_rescisao_2   FOREIGN KEY                                         (cod_reajuste)
                                                        REFERENCES folhapagamento.reajuste                  (cod_reajuste)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_registro_evento_rescisao TO GROUP urbem;
');

select atualizarBanco ('
CREATE TABLE folhapagamento.reajuste_registro_evento_complementar (
    cod_reajuste        INTEGER         NOT NULL,
    cod_registro        INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_evento          INTEGER         NOT NULL,
    cod_configuracao    INTEGER         NOT NULL,
    CONSTRAINT pk_reajuste_registro_evento_complementar     PRIMARY KEY                                             (cod_reajuste, cod_registro, timestamp, cod_evento, cod_configuracao),
    CONSTRAINT fk_reajuste_registro_evento_complementar_1   FOREIGN KEY                                             (cod_registro, timestamp, cod_evento, cod_configuracao)
                                                            REFERENCES folhapagamento.registro_evento_complementar  (cod_registro, timestamp, cod_evento, cod_configuracao),
    CONSTRAINT fk_reajuste_registro_evento_complementar_2   FOREIGN KEY                                             (cod_reajuste)
                                                            REFERENCES folhapagamento.reajuste                      (cod_reajuste)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.reajuste_registro_evento_complementar TO GROUP urbem;
');

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2419
          , 286
          , 'FMReajustesSalariais.php'
          , 'excluir'
          , 4
          , ''
          , 'Excluir Reajustes Salariais'
          );

UPDATE administracao.acao
   SET parametro = 'incluir'
 WHERE cod_acao  = 1865;


-----------------------------------------------------------------------
-- CADASTRANDO FUNCAO mediaRescisaoSomatorioPorDoze COMO FUNCAO INTERNA
-- 20081127 - DIEGO LEMOS DE SOUZA ------------------------------------

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

      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'mediaRescisaoSomatorioPorDoze', 4);
      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'mediaRescisaoSomatorioPorNrOcorrencias', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

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


select atualizarBanco('
update folhapagamento.tipo_media
   set cod_modulo = 27
     , cod_biblioteca = 1
     , cod_funcao = (select cod_funcao from administracao.funcao where nom_funcao = \'mediaRescisaoSomatorioPorDoze\')
 where cod_tipo =  12;');

select atualizarBanco('
update folhapagamento.tipo_media
   set cod_modulo = 27
     , cod_biblioteca = 1
     , cod_funcao = (select cod_funcao from administracao.funcao where nom_funcao = \'mediaRescisaoSomatorioPorNrOcorrencias\')
 where cod_tipo =  13;');


create or replace function deletarFuncao(integer,integer,integer) returns boolean as $$
declare
    inCodModulo alias for $1;
    inCodBiblioteca alias for $2;
    inCodFuncao alias for $3;    
    stSql varchar;
begin
    stSql := 'delete from administracao.corpo_funcao_externa
              where cod_modulo = '||inCodModulo||'
                and cod_biblioteca = '||inCodBiblioteca||'
                and cod_funcao = '||inCodFuncao;
    execute stSql;
    stSql := 'delete from administracao.funcao_externa
              where cod_modulo = '||inCodModulo||'
                and cod_biblioteca = '||inCodBiblioteca||'
                and cod_funcao = '||inCodFuncao;
    execute stSql;
    stSql := 'delete from administracao.parametro
              where cod_modulo = '||inCodModulo||'
                and cod_biblioteca = '||inCodBiblioteca||'
                and cod_funcao = '||inCodFuncao;
    execute stSql;
    stSql := 'delete from administracao.variavel
              where cod_modulo = '||inCodModulo||'
                and cod_biblioteca = '||inCodBiblioteca||'
                and cod_funcao = '||inCodFuncao;
    execute stSql;
    stSql := 'delete from administracao.funcao
              where cod_modulo = '||inCodModulo||'
                and cod_biblioteca = '||inCodBiblioteca||'
                and cod_funcao = '||inCodFuncao;
    execute stSql;
    return true;
end;
$$ language 'plpgsql';

create or replace function removeFuncaoDuplicada() returns void as $$
declare
    stSql varchar;
    reRegistro record;
    reFuncao   record;
    inContador integer:=1;
    inCodModulo integer;
    inCodBiblioteca integer;
    inCodFuncao integer;
    boFicou boolean;
    boExcluir boolean;
    boRetorno boolean;
begin
    stSql := 'select nom_funcao
                   , count(*) as contador
                from administracao.funcao 
               where cod_modulo = 27 
                 and cod_biblioteca = 1 
                 and nom_funcao ilike \'%media%\'
            group by nom_funcao
              having count(*) > 1';
    for reRegistro in execute stSql loop
        raise notice '-------------------------------';
        boFicou := false;
        boExcluir := true;
        raise notice '%', reRegistro.nom_funcao;
        stSql := 'select *
                       , ( select true
                             from folhapagamento.configuracao_evento_caso
                            where configuracao_evento_caso.cod_modulo = funcao.cod_modulo
                              and configuracao_evento_caso.cod_biblioteca = funcao.cod_biblioteca
                              and configuracao_evento_caso.cod_funcao = funcao.cod_funcao) as evento
                       , ( select 1
                            from folhapagamento.tipo_media
                           where tipo_media.cod_modulo = funcao.cod_modulo
                             and tipo_media.cod_biblioteca = funcao.cod_biblioteca
                             and tipo_media.cod_funcao = funcao.cod_funcao) as media
                    from administracao.funcao 
                   where cod_modulo = 27
                     and trim(nom_funcao) = \''||trim(reRegistro.nom_funcao)||'\'';
        inContador := 1;
        for reFuncao in execute stSql loop
            if reFuncao.evento is null and reFuncao.media is null then
                if reRegistro.contador = inContador then
                    raise notice 'Ficou algum? %',boFicou;
                    if boFicou is false then
                        boExcluir := false; 
                    end if;
                end if;
                
                if boExcluir is true then
                    raise notice 'Excluiu: %',reFuncao;
                    boRetorno := deletarFuncao(reFuncao.cod_modulo,reFuncao.cod_biblioteca,reFuncao.cod_funcao);
                end if;
            else
                raise notice 'Ficou: %',reFuncao;
                boFicou := true;
            end if;
            inContador := inContador + 1;
        end loop;
        raise notice '-------------------------------';
    end loop;

    raise notice '=====================================================';

    stSql := 'select nom_funcao
                   , count(*) as contador
                from administracao.funcao 
               where cod_modulo = 27 
                 and cod_biblioteca = 1 
                 and nom_funcao ilike \'%media%\'
            group by nom_funcao
              having count(*) > 1';
    for reRegistro in execute stSql loop
        raise notice '%', reRegistro.nom_funcao;
    end loop;

end
$$ language 'plpgsql';

select removeFuncaoDuplicada();

drop function removeFuncaoDuplicada();
drop function deletarFuncao(integer,integer,integer);

----------------
-- Ticket #13948
----------------

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       DROP CONSTRAINT fk_configuracao_empenho_evento_subdivisao_1;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       DROP CONSTRAINT fk_configuracao_empenho_evento_subdivisao_2;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       DROP CONSTRAINT pk_configuracao_empenho_evento_subdivisao;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       DROP COLUMN     cod_evento;');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_local            DROP CONSTRAINT fk_configuracao_empenho_evento_local_1;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_local            DROP CONSTRAINT fk_configuracao_empenho_evento_local_2;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_local            DROP CONSTRAINT pk_configuracao_empenho_evento_local;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_local            DROP COLUMN     cod_evento;');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   DROP CONSTRAINT fk_configuracao_empenho_evento_atributo_valor_1;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   DROP CONSTRAINT pk_configuracao_empenho_evento_atributo_valor;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   DROP COLUMN     cod_evento;');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         DROP CONSTRAINT fk_configuracao_empenho_evento_atributo_1;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         DROP CONSTRAINT fk_configuracao_empenho_evento_atributo_2;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         DROP CONSTRAINT pk_configuracao_empenho_evento_atributo;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         DROP COLUMN     cod_evento;');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          DROP CONSTRAINT fk_configuracao_empenho_evento_lotacao_1;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          DROP CONSTRAINT fk_configuracao_empenho_evento_lotacao_2;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          DROP CONSTRAINT pk_configuracao_empenho_evento_lotacao;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          DROP COLUMN     cod_evento;');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento                  DROP CONSTRAINT fk_configuracao_empenho_evento_1;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento                  DROP CONSTRAINT fk_configuracao_empenho_evento_2;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento                  DROP CONSTRAINT fk_configuracao_empenho_evento_3;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento                  DROP CONSTRAINT fk_configuracao_empenho_evento_4;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento                  DROP CONSTRAINT pk_configuracao_empenho_evento;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento                  DROP COLUMN     cod_evento;');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento                  RENAME TO configuracao_empenho;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_lotacao          RENAME TO configuracao_empenho_lotacao;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo         RENAME TO configuracao_empenho_atributo;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_atributo_valor   RENAME TO configuracao_empenho_atributo_valor;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_local            RENAME TO configuracao_empenho_local;');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_evento_subdivisao       RENAME TO configuracao_empenho_subdivisao;');


select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho                  ADD  CONSTRAINT pk_configuracao_empenho   PRIMARY KEY (exercicio, cod_configuracao, sequencia);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho                  ADD  CONSTRAINT fk_configuracao_empenho_1 FOREIGN KEY (cod_configuracao) REFERENCES folhapagamento.configuracao_evento(cod_configuracao);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho                  ADD  CONSTRAINT fk_configuracao_empenho_2 FOREIGN KEY (exercicio_pao, num_pao) REFERENCES orcamento.pao(exercicio, num_pao);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho                  ADD  CONSTRAINT fk_configuracao_empenho_3 FOREIGN KEY (cod_despesa, exercicio_despesa) REFERENCES orcamento.despesa(cod_despesa, exercicio);');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao       ADD  CONSTRAINT pk_configuracao_empenho_subdivisao   PRIMARY KEY (exercicio, cod_configuracao, sequencia, cod_sub_divisao);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao       ADD  CONSTRAINT fk_configuracao_empenho_subdivisao_1 FOREIGN KEY (exercicio, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho(exercicio, cod_configuracao, sequencia);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_subdivisao       ADD  CONSTRAINT fk_configuracao_empenho_subdivisao_2 FOREIGN KEY (cod_sub_divisao) REFERENCES pessoal.sub_divisao(cod_sub_divisao);');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_local            ADD  CONSTRAINT pk_configuracao_empenho_local   PRIMARY KEY (exercicio, cod_configuracao, sequencia, cod_local);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_local            ADD  CONSTRAINT fk_configuracao_empenho_local_1 FOREIGN KEY (exercicio, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho(exercicio, cod_configuracao, sequencia);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_local            ADD  CONSTRAINT fk_configuracao_empenho_local_2 FOREIGN KEY (cod_local) REFERENCES organograma."local"(cod_local);');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_atributo         ADD  CONSTRAINT pk_configuracao_empenho_atributo   PRIMARY KEY (cod_cadastro, cod_modulo, cod_atributo, exercicio, cod_configuracao, sequencia);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_atributo         ADD  CONSTRAINT fk_configuracao_empenho_atributo_1 FOREIGN KEY (exercicio, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho(exercicio, cod_configuracao, sequencia);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_atributo         ADD  CONSTRAINT fk_configuracao_empenho_atributo_2 FOREIGN KEY (cod_cadastro, cod_modulo, cod_atributo) REFERENCES administracao.atributo_dinamico(cod_cadastro, cod_modulo, cod_atributo);');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor   ADD  CONSTRAINT pk_configuracao_empenho_atributo_valor   PRIMARY KEY (exercicio, cod_configuracao, sequencia, cod_atributo, cod_modulo, cod_cadastro, valor);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_atributo_valor   ADD  CONSTRAINT fk_configuracao_empenho_atributo_valor_1 FOREIGN KEY (exercicio, cod_configuracao, sequencia, cod_atributo, cod_modulo, cod_cadastro) REFERENCES folhapagamento.configuracao_empenho_atributo(exercicio, cod_configuracao, sequencia, cod_atributo, cod_modulo, cod_cadastro);');

select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_lotacao          ADD  CONSTRAINT pk_configuracao_empenho_lotacao   PRIMARY KEY (exercicio, cod_configuracao, sequencia, cod_orgao); ');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_lotacao          ADD  CONSTRAINT fk_configuracao_empenho_lotacao_1 FOREIGN KEY (exercicio, cod_configuracao, sequencia) REFERENCES folhapagamento.configuracao_empenho(exercicio, cod_configuracao, sequencia);');
select atualizarBanco ('ALTER TABLE folhapagamento.configuracao_empenho_lotacao          ADD  CONSTRAINT fk_configuracao_empenho_lotacao_2 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao(cod_orgao);');


select atualizarBanco ('
CREATE TABLE folhapagamento.configuracao_empenho_evento (
    exercicio               CHAR(4)             NOT NULL,
    cod_configuracao        INTEGER             NOT NULL,
    sequencia               INTEGER             NOT NULL,
    cod_evento              INTEGER             NOT NULL,
    CONSTRAINT pk_configuracao_empenho_evento   PRIMARY KEY                                     (exercicio, cod_configuracao, sequencia, cod_evento),
    CONSTRAINT fk_configuracao_empenho_evento_1 FOREIGN KEY                                     (exercicio, cod_configuracao, sequencia)
                                                REFERENCES folhapagamento.configuracao_empenho  (exercicio, cod_configuracao, sequencia),
    CONSTRAINT fk_configuracao_empenho_evento_2 FOREIGN KEY                                     (cod_evento)
                                                REFERENCES folhapagamento.evento                (cod_evento)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.configuracao_empenho_evento TO GROUP urbem;
');
----------------
-- Ticket #13948
----------------

select atualizarBanco ('
CREATE TABLE folhapagamento.configuracao_empenho_conta_despesa(
    exercicio               CHAR(4)             NOT NULL,
    cod_configuracao        INTEGER             NOT NULL,
    sequencia               INTEGER             NOT NULL,
    cod_conta               INTEGER             NOT NULL,
    CONSTRAINT pk_configuracao_empenho_conta_despesa    PRIMARY KEY                                     (exercicio, cod_configuracao, sequencia),
    CONSTRAINT fk_configuracao_empenho_conta_despesa_1  FOREIGN KEY                                     (exercicio, cod_configuracao, sequencia)
                                                        REFERENCES  folhapagamento.configuracao_empenho (exercicio, cod_configuracao, sequencia),
    CONSTRAINT fk_configuracao_empenho_conta_despesa_2  FOREIGN KEY                                     (exercicio, cod_conta)
                                                        REFERENCES orcamento.conta_despesa              (exercicio, cod_conta)
);
');

select atualizarBanco ('
GRANT ALL ON folhapagamento.configuracao_empenho_conta_despesa TO GROUP urbem;
');


----------------
-- Ticket #14045
----------------
select atualizarBanco ('ALTER TABLE pessoal.ferias ADD COLUMN rescisao BOOLEAN;');
select atualizarBanco ('UPDATE pessoal.ferias SET rescisao = FALSE;');
select atualizarBanco ('ALTER TABLE pessoal.ferias ALTER COLUMN rescisao SET NOT NULL;');
select atualizarBanco ('ALTER TABLE pessoal.ferias ALTER COLUMN rescisao SET DEFAULT FALSE;');


-----------------------------------------------------
-- ADICIONADO RELATORIO 'Exclusao Reajuste Salariais'
-- DIEGO LEMOS DE SOUZA - 20081209 ------------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 27
         , 21
         , 'Exclusao Reajuste Salariais'
         , 'reajusteSalariaisExclusao.rptdesign'
         );


----------------
-- Ticket #13572
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2423
          , 444
          , 'FLBancoHoras.php'
          , 'incluir'
          , 3
          , ''
          , 'Banco de Horas'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 4
         , 51
         , 9
         , 'Banco de Horas'
         , 'bancoHoras.rptdesign'
         );


----------------
-- Ticket #13872
----------------

SELECT atualizarBanco('
INSERT
  INTO folhapagamento.tipo_evento_salario_familia
     ( cod_tipo
     , descricao )
VALUES ( 2
     , \'Evento Base Salário Família\'
     );
');

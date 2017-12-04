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
* $Id: GRH_1961.sql 38308 2009-02-19 19:26:00Z fabio $
*
* Versão 1.97.0
*/

----------------
-- Ticket #14604
----------------
select atualizarBanco('
CREATE OR REPLACE FUNCTION pessoal.fn_contrato_servidor_conta_salario_historico()  RETURNS TRIGGER AS $$
DECLARE
    reContaSalario      RECORD;
BEGIN
    If TG_OP=\'INSERT\' THEN
        INSERT INTO pessoal.contrato_servidor_conta_salario_historico
        (cod_contrato,cod_banco,cod_agencia,nr_conta) VALUES 
        (new.cod_contrato,new.cod_banco,new.cod_agencia,new.nr_conta);
    ELSE
         SELECT contrato_servidor_conta_salario.*
           INTO reContaSalario
           FROM pessoal.contrato_servidor_conta_salario
          WHERE contrato_servidor_conta_salario.cod_contrato = new.cod_contrato
            AND contrato_servidor_conta_salario.cod_banco = new.cod_banco
            AND contrato_servidor_conta_salario.cod_agencia = new.cod_agencia
            AND contrato_servidor_conta_salario.nr_conta = new.nr_conta;        

        IF reContaSalario.cod_contrato IS NULL THEN
            INSERT INTO pessoal.contrato_servidor_conta_salario_historico
            (cod_contrato,cod_banco,cod_agencia,nr_conta) VALUES 
            (new.cod_contrato,new.cod_banco,new.cod_agencia,new.nr_conta);
        END IF;
    END IF;
    Return new;
END;
$$ LANGUAGE plpgsql;
');

SELECT atualizarbanco('
CREATE TABLE pessoal.contrato_servidor_conta_salario_historico (
    cod_contrato            INTEGER                             NOT NULL,
    timestamp               TIMESTAMP                           NOT NULL DEFAULT  (\'now\'::text)::timestamp(3) with time zone,
    cod_agencia             INTEGER                             NOT NULL,
    cod_banco               INTEGER                             NOT NULL,
    nr_conta                VARCHAR(15)                         NOT NULL,
    CONSTRAINT pk_contrato_servidor_conta_salario_historico    PRIMARY KEY                          (cod_contrato, timestamp),
    CONSTRAINT fk_contrato_servidor_conta_salario_historico_1  FOREIGN KEY                          (cod_contrato)
                                                               REFERENCES pessoal.contrato_servidor (cod_contrato),
    CONSTRAINT fk_contrato_servidor_conta_salario_historico_2  FOREIGN KEY                          (cod_banco, cod_agencia)
                                                               REFERENCES monetario.agencia         (cod_banco, cod_agencia)
);
');

select atualizarBanco('CREATE TRIGGER tr_contrato_servidor_conta_salario_historico BEFORE INSERT OR UPDATE ON pessoal.contrato_servidor_conta_salario FOR EACH ROW EXECUTE PROCEDURE pessoal.fn_contrato_servidor_conta_salario_historico();');

select atualizarBanco('INSERT INTO pessoal.contrato_servidor_conta_salario_historico SELECT cod_contrato, to_timestamp(\'1900-01-01\', \'yyyy-mm-dd\'), cod_agencia, cod_banco, nr_conta FROM pessoal.contrato_servidor_conta_salario');

----------------
-- Ticket #14456
----------------
CREATE TYPE colulasConferenciaSefip AS (
      nom_cgm                       VARCHAR
    , servidor_pis_pasep            VARCHAR                     
    , cod_categoria                 VARCHAR                                                                    
    , registro                      VARCHAR                                                                    
    , num_ocorrencia                VARCHAR                                                         
    , num_sefip                     VARCHAR
    , periodo                       VARCHAR
    , base_fgts                     NUMERIC
    , base_fgts_decimo              NUMERIC
    , base_previdencia              NUMERIC
    , base_previdencia_decimo       NUMERIC
    , desconto_previdencia          NUMERIC
    , desconto_previdencia_decimo   NUMERIC
    , salario_familia               NUMERIC
    , salario_maternidade           NUMERIC
);

----------------
-- Ticket #14804
----------------
UPDATE administracao.acao set nom_arquivo = 'LSManterTabelaIRRF.php' where cod_acao = 1383;




----------------
-- Ticket #14821
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

      intCodFuncao   := public.manutencao_funcao   (  27, 1, 'pega0PercentualAdiantamentoDecimo', 3);
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

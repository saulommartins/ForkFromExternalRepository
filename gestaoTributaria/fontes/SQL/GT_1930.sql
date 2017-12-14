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
* $Id: GT_1930.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.93.0
*/

----------------
-- Ticket #13524
----------------

CREATE TABLE divida.livro (
    num_livro       INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    CONSTRAINT pk_livro         PRIMARY KEY (num_livro, exercicio)
);

ALTER TABLE divida.divida_ativa ADD COLUMN exercicio_livro CHAR(4);
--ALTER TABLE divida.divida_ativa ADD CONSTRAINT fk_divida_ativa_3 FOREIGN KEY (num_livro, exercicio_livro) REFERENCES divida.livro (num_livro, exercicio);


----------------
-- Ticket #12118
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2333
          , 221
          , 'FMManterLayoutCarne.php'
          , 'alterar'
          , 3
          , ''
          , 'Alterar Layout de Carnê'
          );


----------------
-- Ticket #13121
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2309
          , 307
          , 'FMManterConfiguracaoRemissao.php'
          , 'remissao'
          , 3
          , ''
          , 'Configurar Remissão Automática'
          );


INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 427
         , 33
         , 'Remissão'
         , 'instancias/remissao/'
         , 7
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2308
          , 427
          , 'FMConcederRemissao.php'
          , 'conceder'
          , 1
          , ''
          , 'Conceder Remissão Automática'
          );

-- CAMPO SITUACAO DO LANCAMENTO
ALTER TABLE arrecadacao.lancamento ADD COLUMN situacao CHAR(1);
ALTER TABLE arrecadacao.lancamento ADD CONSTRAINT chk_lancamento CHECK (situacao IN ('P','C','D','R'));


-- PLs p/ triggers de situação do lancamento
CREATE OR REPLACE FUNCTION fn_atualizar_lancamento_situacao_pagamento()  RETURNS TRIGGER AS $$
DECLARE
    inCodLancamento     integer;
    inNroParcela        integer;
    inTotalPago         integer;
    inTotalAberto       integer;
    boPagamento         boolean;

BEGIN
    SELECT
        tipo_pagamento.pagamento
    INTO
        boPagamento
    FROM
        arrecadacao.tipo_pagamento
    WHERE
        tipo_pagamento.cod_tipo = new.cod_tipo;

    SELECT DISTINCT
        parcela.cod_lancamento,
        parcela.nr_parcela
    INTO
        inCodLancamento,
        inNroParcela
    FROM
        arrecadacao.carne
    INNER JOIN
        arrecadacao.parcela
    ON
        parcela.cod_parcela = carne.cod_parcela
    WHERE
        carne.numeracao = new.numeracao;

    IF ( boPagamento = false ) THEN --cancelamento
        UPDATE
            arrecadacao.lancamento
        SET
            situacao = 'C'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    ELSE
        IF ( inNroParcela = 0 ) THEN --parcela unica paga
            UPDATE
                arrecadacao.lancamento
            SET
                situacao = 'P'
            WHERE
                lancamento.cod_lancamento = inCodLancamento;
        ELSE
            SELECT
                count(parcela.cod_parcela)
            INTO
                inTotalAberto
            FROM
                arrecadacao.parcela
            INNER JOIN
                arrecadacao.carne
            ON
                carne.cod_parcela = parcela.cod_parcela
            LEFT JOIN
                arrecadacao.carne_devolucao
            ON
                carne_devolucao.numeracao = carne.numeracao
            WHERE
                parcela.nr_parcela <> 0
                AND carne_devolucao.numeracao IS NULL
                AND parcela.cod_lancamento = inCodLancamento;

            SELECT
                count(parcela.cod_parcela)
            INTO
                inTotalPago
            FROM
                arrecadacao.parcela
            INNER JOIN
                arrecadacao.carne
            ON
                carne.cod_parcela = parcela.cod_parcela
            INNER JOIN
                arrecadacao.pagamento
            ON
                pagamento.numeracao = carne.numeracao
            INNER JOIN
                arrecadacao.tipo_pagamento
            ON
                tipo_pagamento.cod_tipo = pagamento.cod_tipo
                AND tipo_pagamento.pagamento = true
            WHERE
                parcela.nr_parcela <> 0
                AND parcela.cod_lancamento = inCodLancamento;

            IF ( inTotalAberto = inTotalPago ) THEN
                UPDATE
                    arrecadacao.lancamento
                SET
                    situacao = 'P'
                WHERE
                    lancamento.cod_lancamento = inCodLancamento;
            END IF;
        END IF;
    END IF;

    Return new;

END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION fn_atualizar_lancamento_situacao_devolucao()  RETURNS TRIGGER AS $$
DECLARE
    inCodLancamento     integer;
BEGIN
    IF ( new.cod_motivo = 11 ) THEN --inscrito em divida ativa
        SELECT
            parcela.cod_lancamento
        INTO
            inCodLancamento
        FROM
            arrecadacao.parcela
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        WHERE
            carne.numeracao = new.numeracao;

        UPDATE
            arrecadacao.lancamento
        SET
            situacao = 'D'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    ELSIF ( new.cod_motivo = 109 ) THEN --cancelado por recalculo
        SELECT
            parcela.cod_lancamento
        INTO
            inCodLancamento
        FROM
            arrecadacao.parcela
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        WHERE
            carne.numeracao = new.numeracao;

        UPDATE
            arrecadacao.lancamento
        SET
            situacao = 'C'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    ELSIF ( new.cod_motivo = 14 ) THEN --remissao
        SELECT
            parcela.cod_lancamento
        INTO
            inCodLancamento
        FROM
            arrecadacao.parcela
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        WHERE
            carne.numeracao = new.numeracao;

        UPDATE
            arrecadacao.lancamento
        SET
            situacao = 'R'
        WHERE
            lancamento.cod_lancamento = inCodLancamento;
    END IF;

    Return new;
END;
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION fn_atualizar_lancamento_situacao_remissao()  RETURNS TRIGGER AS $$
DECLARE
    inCodLancamento     integer;
BEGIN
    SELECT
        parcela.cod_lancamento
    INTO
        inCodLancamento
    FROM
        arrecadacao.parcela
    INNER JOIN
        divida.parcela_origem
    ON
        parcela_origem.cod_parcela = parcela.cod_parcela
    INNER JOIN
        divida.divida_parcelamento
    ON
        divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento

    INNER JOIN
        divida.parcelamento
    ON
        parcelamento.num_parcelamento = divida_parcelamento.num_parcelamento
        AND parcelamento.numero_parcelamento = -1
        AND parcelamento.exercicio = -1

    WHERE
        divida_parcelamento.cod_inscricao = new.cod_inscricao
        AND divida_parcelamento.exercicio = new.exercicio;

    UPDATE
        arrecadacao.lancamento
    SET
        situacao = 'R'
    WHERE
        lancamento.cod_lancamento = inCodLancamento;

    Return new;
END;
$$ LANGUAGE 'plpgsql';

-- TRIGGERS P/ SITUACAO DO LANCAMENTO
CREATE TRIGGER tr_seta_situacao_lancamento_pg AFTER INSERT ON arrecadacao.pagamento         FOR EACH ROW EXECUTE PROCEDURE fn_atualizar_lancamento_situacao_pagamento();
CREATE TRIGGER tr_seta_situacao_lancamento_dv AFTER INSERT ON arrecadacao.carne_devolucao   FOR EACH ROW EXECUTE PROCEDURE fn_atualizar_lancamento_situacao_devolucao();
CREATE TRIGGER tr_seta_situacao_lancamento_rm AFTER INSERT ON divida.divida_remissao        FOR EACH ROW EXECUTE PROCEDURE fn_atualizar_lancamento_situacao_remissao();


-- INSERE FUNCOES REFERENTES A REGRA REMISSAO
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

-- Inclusão de função interna arrecadacao/arrecadacao.buscaValorCreditoLancamento
      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'arrecadacao.buscaValorCreditoLancamento', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'cod_lancamento', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'cod_credito'   , 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'cod_especie'   , 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'cod_genero'    , 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'cod_natureza'  , 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );

-- Inclusão de função interna arrecadacao/arrecadacao.buscaInscricaoLancamento
      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'arrecadacao.buscaInscricaoLancamento', 1);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'cod_lancamento', 1 );
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


----------------------------------------------------------------------------------------------
-- AJUSTE DE MIGRACAO - PARCELAS CANCELADAS SEM ESTERM INSCRITAS EM DIVIDA - MATA DE SAO JOAO 
----------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '13805528000180';

   IF FOUND THEN

            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000211970' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000256980' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000256981' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000294927' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000310443' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000317350' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000323695' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000361956' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000361957' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000361991' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000361992' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000367125' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000369564' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000434619' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000439718' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000457066' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000460713' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000479085' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000489802' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000528185' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000528962' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000529571' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000530831' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000533697' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000534305' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000535632' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000537091' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000537348' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000538469' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000539067' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000539378' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000540441' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '76106900000000531' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000546533' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000550240' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000551191' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000552247' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000552396' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000552926' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000564751' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000565871' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '99990000000574210' AND cod_motivo = 11 ;
            DELETE FROM arrecadacao.carne_devolucao WHERE numeracao = '76106900001009558' AND cod_motivo = 11 ;

   END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();


---------------------------------------------------------------
--lista pagos
---------------------------------------------------------------
UPDATE 
    arrecadacao.lancamento 
SET 
    situacao = 'P'
WHERE
    lancamento.cod_lancamento IN (
        SELECT DISTINCT
            parcela.cod_lancamento
        
        FROM
            arrecadacao.parcela
        
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        
        INNER JOIN
            arrecadacao.pagamento
        ON
            pagamento.numeracao = carne.numeracao
        
        INNER JOIN
            arrecadacao.tipo_pagamento
        ON
            tipo_pagamento.cod_tipo = pagamento.cod_tipo
        AND tipo_pagamento.pagamento = true
        
        WHERE
            parcela.nr_parcela = 0
    );
--------------------------------------------------------------------------------------------------
--lista pagos
--------------------------------------------------------------------------------------------------
UPDATE 
    arrecadacao.lancamento 
SET 
    situacao = 'P'
WHERE
    lancamento.cod_lancamento IN (
        SELECT DISTINCT
            lancamento.cod_lancamento
        
        FROM
            arrecadacao.lancamento
        
        INNER JOIN
            (
                SELECT
                    parcela.cod_lancamento,
                    count( carne.numeracao ) AS total_carnes,
                    count( pagamento.numeracao ) AS total_pagamento
        
                FROM
                    arrecadacao.parcela
        
                INNER JOIN
                    arrecadacao.carne
                ON
                    carne.cod_parcela = parcela.cod_parcela
        
                LEFT JOIN
                    (
                        SELECT
                            pagamento.*
                        FROM
                            arrecadacao.pagamento
                        INNER JOIN
                            arrecadacao.tipo_pagamento
                        ON
                            tipo_pagamento.cod_tipo = pagamento.cod_tipo
                            AND tipo_pagamento.pagamento = true
                    )AS pagamento
                ON
                    pagamento.numeracao = carne.numeracao
        
                WHERE
                    parcela.nr_parcela <> 0
        
                GROUP BY
                    parcela.cod_lancamento
            )AS totais_parcelas
        ON
            totais_parcelas.cod_lancamento = lancamento.cod_lancamento
            AND totais_parcelas.total_carnes = totais_parcelas.total_pagamento
    );
----------------------------------------------------------------------------------------------
--lista em divida
----------------------------------------------------------------------------------------------
UPDATE 
    arrecadacao.lancamento 
SET 
    situacao = 'D'
WHERE
    lancamento.cod_lancamento IN (
        SELECT DISTINCT
            parcela.cod_lancamento

        FROM
            arrecadacao.parcela

        INNER JOIN
            divida.parcela_origem
        ON
            parcela_origem.cod_parcela = parcela.cod_parcela
    );
-----------------------------------------------------------------------------------------------
--lista cancelados
-----------------------------------------------------------------------------------------------
UPDATE 
    arrecadacao.lancamento 
SET 
    situacao = 'C'
WHERE
    lancamento.cod_lancamento IN (
        SELECT DISTINCT
            lancamento.cod_lancamento
        
        FROM
            arrecadacao.lancamento
        
        INNER JOIN
            arrecadacao.parcela
        ON
            parcela.cod_lancamento = lancamento.cod_lancamento
        
        INNER JOIN
            arrecadacao.carne
        ON
            carne.cod_parcela = parcela.cod_parcela
        
        INNER JOIN
            arrecadacao.pagamento
        ON
            pagamento.numeracao = carne.numeracao

        INNER JOIN
            arrecadacao.tipo_pagamento
        ON
            pagamento.cod_tipo = tipo_pagamento.cod_tipo
            and tipo_pagamento.pagamento = false
    );
-----------------------------------------------------------------------------------------------
--lista cancelados
-----------------------------------------------------------------------------------------------
UPDATE 
    arrecadacao.lancamento 
SET 
    situacao = 'C'
WHERE
    lancamento.cod_lancamento IN (
        SELECT DISTINCT
            lancamento.cod_lancamento
        
        FROM
            arrecadacao.lancamento
        
        INNER JOIN
            (
                SELECT
                    parcela.cod_lancamento,
                    count( carne.numeracao ) AS total_carnes,
                    count( carne_devolucao.numeracao ) + count( pagamento.numeracao ) AS total_pagamento
        
                FROM
                    arrecadacao.parcela
        
                INNER JOIN
                    arrecadacao.carne
                ON
                    carne.cod_parcela = parcela.cod_parcela
        
                LEFT JOIN
                    arrecadacao.carne_devolucao
                ON
                    carne_devolucao.numeracao = carne.numeracao
                    AND carne_devolucao.cod_motivo = 109
        
                LEFT JOIN
                    (
                        SELECT
                            pagamento.*
                        FROM
                            arrecadacao.pagamento
                        INNER JOIN
                            arrecadacao.tipo_pagamento
                        ON
                            tipo_pagamento.cod_tipo = pagamento.cod_tipo
                            AND tipo_pagamento.pagamento = false
                    )AS pagamento
                ON
                    pagamento.numeracao = carne.numeracao
        
                WHERE
                    parcela.nr_parcela <> 0
        
                GROUP BY
                    parcela.cod_lancamento
            )AS totais_parcelas
        ON
            totais_parcelas.cod_lancamento = lancamento.cod_lancamento
            AND totais_parcelas.total_carnes = totais_parcelas.total_pagamento
        
        WHERE
            (
                SELECT
                    parcela.cod_lancamento
        
                FROM
                    arrecadacao.parcela
        
                INNER JOIN
                    arrecadacao.carne
                ON
                    carne.cod_parcela = parcela.cod_parcela
        
                INNER JOIN
                    arrecadacao.pagamento
                ON
                    pagamento.numeracao = carne.numeracao
        
                INNER JOIN
                    arrecadacao.tipo_pagamento
                ON
                    tipo_pagamento.cod_tipo = pagamento.cod_tipo
                    AND tipo_pagamento.pagamento = true
        
                WHERE
                    parcela.cod_lancamento = totais_parcelas.cod_lancamento
                    AND parcela.nr_parcela = 0
                LIMIT 1
            ) IS NULL
    );



CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$

DECLARE

   varAux     VARCHAR;

BEGIN

   SELECT valor
     INTO varAux
     FROM administracao.configuracao
    WHERE exercicio = '2008'
      AND parametro = 'cnpj'
      AND valor     = '13805528000180';

   IF FOUND THEN

            INSERT INTO arrecadacao.carne_devolucao values ('99990000000309587', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000309672', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000370739', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000418095', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000418096', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000418097', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000372130', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000385080', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000385079', 11, now()::date, now(), 99 );
            INSERT INTO arrecadacao.carne_devolucao values ('99990000000385078', 11, now()::date, now(), 99 );

   END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT manutencao();
DROP FUNCTION manutencao();



------------------------------------------------------------------------
-- Adicionando ação p/ relatório da REMISSÃO AUTOMÁTICA - FABIO 20081006
------------------------------------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2375
          , 366
          , 'FLRemissao.php'
          , 'incluir'
          , 3
          , ''
          , 'Remissão Automática'
          );


--------------------------------------------------------------------------
-- FUNCAO P/ AJUSTAR NUMERACAO DE LIVRO DE DIVIDA ATIVA - MATA DE SAO JOAO
--------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION ajusta_livro() RETURNS INTEGER AS $$
DECLARE
    stLaco          VARCHAR;
    stLaco2         VARCHAR;
    reRecord         RECORD;
    reRecord2        RECORD;
    total           integer;
    inPagina        integer;
    inLivro         integer;
    inElementos     integer;
    varAux          VARCHAR;

BEGIN

    DROP TRIGGER tr_restringe_divida_ativa ON divida.divida_ativa;

    SELECT valor
      INTO varAux
      FROM administracao.configuracao
     WHERE exercicio = '2008'
       AND parametro = 'cnpj'
       AND valor     = '13805528000180';
    
    IF FOUND THEN

                UPDATE divida.divida_ativa
                   SET exercicio_livro = exercicio;

                stLaco := '
                    SELECT DISTINCT
                        exercicio
                    FROM
                        divida.divida_ativa
                    ORDER BY
                        exercicio ASC
                ';
            
                total := 0;
                FOR reRecord IN EXECUTE stLaco LOOP
                    inElementos := 1;
                    inPagina := 1;
                    inLivro := 1;
                    stLaco2 := '
                        SELECT
                            divida_ativa.*
                        FROM
                            divida.divida_ativa
                        WHERE
                            divida_ativa.exercicio = '||reRecord.exercicio||'
                        ORDER BY
                            divida_ativa.cod_inscricao
                    ';
            
                    FOR reRecord2 IN EXECUTE stLaco2 LOOP
                        UPDATE
                            divida.divida_ativa
                        SET
                            num_livro = inLivro,
                            num_folha = inPagina
                        WHERE
                            cod_inscricao = reRecord2.cod_inscricao
                            AND exercicio = reRecord2.exercicio;
            
                        if ( inElementos >= 54 ) then
                            if ( inPagina >= 999 ) then
                                inPagina := 1;
                                inLivro := inLivro + 1;
                            else
                                inPagina := inPagina + 1;
                            end if;
            
                            inElementos := 1;
                        else
                            inElementos := inElementos + 1;
                        end if;
                    END LOOP;
            
                END LOOP;

    ELSE

        UPDATE divida.divida_ativa
           SET exercicio_livro = '0000';

    END IF;

    CREATE TRIGGER tr_restringe_divida_ativa BEFORE DELETE OR UPDATE ON divida.divida_ativa FOR EACH ROW EXECUTE PROCEDURE divida.fn_restringe_divida_ativa();

    RETURN total;

    END;
$$ LANGUAGE 'plpgsql';

SELECT          ajusta_livro();
DROP FUNCTION   ajusta_livro();

ALTER TABLE divida.divida_ativa ALTER COLUMN exercicio_livro SET NOT NULL;

----------------------
-- NOVAS CONFIGURACOES
----------------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor )
VALUES ( '2008'
     , 33
     , 'modalidade_inscricao_automatica'
     , ''
     );

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor )
VALUES ( '2008'
     , 33
     , 'livro_folha'
     , ''
     );

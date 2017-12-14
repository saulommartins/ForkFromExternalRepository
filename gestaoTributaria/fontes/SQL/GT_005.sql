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
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* Versão 005.
*/

DELETE FROM administracao.funcionalidade where cod_funcionalidade = 300;

DROP TABLE arrecadacao.parcelamento_lancamento;

ALTER TABLE economico.responsavel_tecnico ADD constraint uk_responsavel_tecnico_2 UNIQUE (cod_uf,num_registro);

-----------------
-- Ticket #12756
-----------------

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

      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'fn_multa_simples_quinze_por_cento', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'dtVencimento', 5 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'dtDataCalculo', 5 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'nuValor', 4 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inCodAcrescimo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inCodTipo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );

      PERFORM           public.manutencao_funcao_externa( 25, 1, intCodFuncao );


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

      intCodFuncao   := public.manutencao_funcao   (  25, 1, 'fn_correcao_mariana', 4);
                                                 --( intCodmodulo , intCodBiblioteca , varNomeFunc , intCodTiporetorno )

      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'dtVencimento', 5 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'dtDataCalculo', 5 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'nuValor', 4 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inCodAcrescimo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );
      intCodVariavel := public.manutencao_variavel (  25, 1, intCodFuncao, 'inCodTipo', 1 );
      PERFORM           public.manutencao_parametro(  25, 1, intCodFuncao, intCodVariavel );

      PERFORM           public.manutencao_funcao_externa( 25, 1, intCodFuncao );


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
--Ticket #12734
----------------

CREATE OR REPLACE VIEW economico.vw_licenca_ativa AS
   SELECT DISTINCT ON (lc.cod_licenca, lc.exercicio)
        lc.cod_licenca,
        lc.exercicio,
        lc.dt_inicio,
        lc.dt_termino,
        pl.cod_processo,
        tld.nom_tipo,
        pl.exercicio_processo,
         CASE
             WHEN lca.inscricao_economica::character varying IS NOT NULL THEN 'Atividade'::text
             WHEN lce.inscricao_economica::character varying IS NOT NULL THEN 'Especial'::text
             WHEN lcd.numcgm::character varying IS NOT NULL THEN 'Diversa'::text
             ELSE NULL::text
         END AS especie_licenca,
         lcd.cod_tipo AS cod_tipo_diversa,
         CASE
             WHEN lca.inscricao_economica IS NOT NULL THEN lca.inscricao_economica
             WHEN lce.inscricao_economica IS NOT NULL THEN lce.inscricao_economica
             ELSE NULL::integer
         END AS inscricao_economica,
         CASE
             WHEN ceef.inscricao_economica IS NOT NULL THEN ceef.numcgm
             WHEN ceed.inscricao_economica IS NOT NULL THEN ceed.numcgm
             WHEN cea.inscricao_economica IS NOT NULL THEN cea.numcgm
             ELSE lcd.numcgm
         END AS numcgm,
         cgm.nom_cgm
    FROM
        economico.licenca lc

    LEFT JOIN (
        SELECT
            bl.cod_licenca,
            bl.exercicio,
            bl.dt_inicio,
            bl.dt_termino,
            bl.cod_tipo,
            bl."timestamp",
            bl.motivo

        FROM
            economico.baixa_licenca bl, (
                SELECT
                    baixa_licenca.cod_licenca,
                    max(baixa_licenca."timestamp") AS "timestamp"
                FROM
                    economico.baixa_licenca
                GROUP BY
                    baixa_licenca.cod_licenca
            ) ml
        WHERE
            bl.cod_licenca = ml.cod_licenca
            AND bl."timestamp" = ml."timestamp"
    ) bl
    ON
        lc.cod_licenca = bl.cod_licenca
        AND lc.exercicio = bl.exercicio

    LEFT JOIN economico.processo_licenca pl ON lc.cod_licenca = pl.cod_licenca AND lc.exercicio = pl.exercicio
    LEFT JOIN economico.licenca_atividade lca ON lca.cod_licenca = lc.cod_licenca AND lca.exercicio = lc.exercicio
    LEFT JOIN economico.licenca_especial lce ON lce.cod_licenca = lc.cod_licenca AND lce.exercicio = lc.exercicio
    LEFT JOIN economico.licenca_diversa lcd ON lcd.cod_licenca = lc.cod_licenca AND lcd.exercicio = lc.exercicio
    LEFT JOIN economico.tipo_licenca_diversa tld ON lcd.cod_tipo = tld.cod_tipo
    LEFT JOIN economico.cadastro_economico_empresa_fato ceef ON ceef.inscricao_economica = lca.inscricao_economica OR ceef.inscricao_economica = lce.inscricao_economica
    LEFT JOIN economico.cadastro_economico_empresa_direito ceed ON ceed.inscricao_economica = lca.inscricao_economica OR ceed.inscricao_economica = lce.inscricao_economica
    LEFT JOIN economico.cadastro_economico_autonomo cea ON cea.inscricao_economica = lca.inscricao_economica OR cea.inscricao_economica = lce.inscricao_economica
    LEFT JOIN sw_cgm cgm ON lcd.numcgm = cgm.numcgm OR cea.numcgm = cgm.numcgm OR ceef.numcgm = cgm.numcgm OR ceed.numcgm = cgm.numcgm
   WHERE

lc.dt_inicio <= now()::date
 AND CASE
     WHEN lc.dt_termino IS NOT NULL AND lc.dt_termino <= now()::date  THEN false
     ELSE true
 END

AND
 CASE
     WHEN bl.cod_licenca IS NOT NULL THEN
     CASE
         WHEN bl.cod_tipo = 2 THEN
         CASE
             WHEN bl.dt_termino IS NOT NULL AND bl.dt_termino <= now()::date THEN FALSE
             ELSE true
         END
         ELSE false
     END
     ELSE true
 END
 ORDER BY lc.cod_licenca;

----------------
--Ticket #12834
----------------

 ALTER TABLE monetario.valor_acrescimo ADD    COLUMN valor_new numeric(14,6);
 UPDATE      monetario.valor_acrescimo SET           valor_new = valor;
 ALTER TABLE monetario.valor_acrescimo DROP   COLUMN valor;
 ALTER TABLE monetario.valor_acrescimo RENAME COLUMN valor_new TO valor;
 ALTER TABLE monetario.valor_acrescimo ALTER  COLUMN valor SET NOT NULL;

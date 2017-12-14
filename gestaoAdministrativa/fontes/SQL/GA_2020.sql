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
* Versao 2.02.0
*
* Fabio Bertoldi - 20120924
*
*/

----------------
-- Ticket #18745
----------------

CREATE ROLE urbem LOGIN SUPERUSER PASSWORD 'UrB3m';

CREATE EXTENSION hstore;

CREATE TABLE administracao.auditoria_detalhe(
    numcgm          INTEGER             NOT NULL,
    cod_acao        INTEGER             NOT NULL,
    timestamp       TIMESTAMP           NOT NULL,
    cod_detalhe     INTEGER             NOT NULL,
    valores         hstore              NOT NULL,
    CONSTRAINT pk_auditoria_detalhe     PRIMARY KEY                         (numcgm, cod_acao, timestamp, cod_detalhe),
    CONSTRAINT fk_auditoria_detalhe_1   FOREIGN KEY                         (numcgm, cod_acao, timestamp)
                                        REFERENCES administracao.auditoria  (numcgm, cod_acao, timestamp)
);
GRANT ALL ON administracao.auditoria_detalhe TO urbem;


------------------------
-- USUARIO transparencia
------------------------

INSERT
  INTO sw_cgm
     ( numcgm
     , nom_cgm
     , cod_municipio
     , cod_uf
     , cod_municipio_corresp
     , cod_uf_corresp
     , cod_responsavel
     , cod_pais
     , cod_pais_corresp
     )
VALUES
     ( -1
     , 'Transparência CNM'
     , 0
     , 0
     , 0
     , 0
     , 0
     , 0
     , 0
     );

INSERT
  INTO sw_cgm_pessoa_fisica
     ( numcgm
     , cod_categoria_cnh
     , orgao_emissor
     , cod_nacionalidade
     , cod_uf_orgao_emissor
     )
VALUES
     ( -1
     , 0
     , ''
     , 0
     , 0
     );

INSERT
  INTO administracao.usuario
     ( numcgm
     , cod_orgao
     , dt_cadastro
     , username
     , password
     , status
     )
VALUES
     ( -1
     , (SELECT MIN(cod_orgao) FROM organograma.orgao)
     , now()::date
     , 'transparencia'
     , 'sukaqpXRfHqwI'
     , 'A'
     );


----------------
-- Ticket #16526
----------------

DROP VIEW organograma.vw_orgao_nivel;

CREATE VIEW organograma.vw_orgao_nivel AS
SELECT o.cod_orgao
         , o.num_cgm_pf
         , o.cod_calendar
         , o.cod_norma
         , o.criacao
         , o.inativacao
         , o.sigla_orgao
         , orn.cod_organograma
         , organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao) AS orgao
         , publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS orgao_reduzido
         , publico.fn_nivel(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS nivel
      FROM organograma.orgao o
      JOIN organograma.orgao_nivel orn
        ON o.cod_orgao = orn.cod_orgao
  GROUP BY orn.cod_organograma
         , o.cod_orgao
         , o.num_cgm_pf
         , o.cod_calendar
         , o.cod_norma
         , o.criacao
         , o.inativacao
         , o.sigla_orgao
  ORDER BY o.cod_orgao
         ;


----------------
-- Ticket #20187
----------------

UPDATE administracao.acao
   SET nom_arquivo = 'FMRelatorioCgm.php'
    WHERE cod_acao = 40
         ;


----------------
-- Ticket #15337
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2767
          , 217
          , 'FLRelatorioOrganograma.php'
          , 'imprimir'
          , 10
          , ''
          , 'Relatório do Organograma'
          );


--------------------------------
-- CORRICAO DE FK em SW_PROCESSO
--------------------------------

ALTER TABLE sw_ultimo_andamento ADD CONSTRAINT fk_ultimo_andamento_4 FOREIGN KEY (cod_usuario) REFERENCES administracao.usuario(numcgm);


---------------------------------------------------------------------------------------------
-- FUNCAO atualizarBanco que sera utilizada durante a virada, e seria atualizada apos os SQLs
---------------------------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION atualizarBanco(VARCHAR) RETURNS BOOLEAN as $$
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

    inExercicio := selectIntoInteger('SELECT valor FROM administracao.configuracao WHERE parametro = ''ano_exercicio'' ORDER BY exercicio desc LIMIT 1');
    inCodEntidadePrefeitura := selectIntoInteger('SELECT valor::integer as valor
                                                    FROM administracao.configuracao
                                                   WHERE parametro = ''cod_entidade_prefeitura''
                                                     AND exercicio = '|| quote_literal(inExercicio) ||' ');


    IF strpos(trim(upper(stSqlParametro)),upper('CREATE SCHEMA')) > 0 THEN
        boEsquema    := TRUE;
        stNomeSchema := trim(translate(stSqlParametro,'CREATE SCHEMA ;',''));
        stInsert     := 'INSERT INTO administracao.schema_rh (schema_cod,schema_nome) VALUES ((SELECT max(schema_cod) FROM administracao.schema_rh)+1,'|| quote_literal(stNomeSchema) ||')';
        EXECUTE stInsert;

        stSql := 'SELECT TRUE as retorno
                    FROM administracao.entidade_rh
                   WHERE cod_entidade = '|| inCodEntidadePrefeitura ||'
                   LIMIT 1';
        boRetorno := selectIntoBoolean(stSql);
        IF boRetorno IS TRUE THEN
            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod).
                         VALUES
                         ('|| quote_literal(inExercicio) ||','|| inCodEntidadePrefeitura ||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
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
                 WHERE exercicio = '|| quote_literal(inExercicio) ||'
                   AND cod_entidade IN (SELECT cod_entidade
                                          FROM administracao.entidade_rh
                                         WHERE exercicio = '|| quote_literal(inExercicio) ||'
                                        GROUP BY cod_entidade)
                   AND cod_entidade != ('|| inCodEntidadePrefeitura ||')';
    FOR reRegistro IN EXECUTE stSql LOOP
        stBanco := stSqlParametro;
        IF boEsquema THEN
            stBanco := trim(replace(stSqlParametro,';','')) ||'_'|| reRegistro.cod_entidade ||';';

            stInsert := 'INSERT INTO administracao.entidade_rh (exercicio,cod_entidade,schema_cod).
                         VALUES
                         ('|| quote_literal(inExercicio) ||','|| reRegistro.cod_entidade ||',(SELECT max(schema_cod) FROM administracao.schema_rh))';
            EXECUTE stInsert;
        ELSIF boTrigger THEN
            stBanco := trim(replace(stSqlParametro,stNomeTriger,stNomeTriger ||'_'|| reRegistro.cod_entidade));
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '|| reSchema.schema_nome ||'.',' '|| reSchema.schema_nome ||'_'|| reRegistro.cod_entidade ||'.');
            END LOOP;
        ELSIF boGranteEsquema THEN
            stBanco := replace(stBanco, stNomeSchema,' '|| stNomeSchema ||'_'|| reRegistro.cod_entidade);
        ELSE
            stSql := 'SELECT * FROM administracao.schema_rh';
            FOR reSchema IN EXECUTE stSql LOOP
                stBanco := replace(stBanco,' '|| reSchema.schema_nome ||'.',' '|| reSchema.schema_nome ||'_'|| reRegistro.cod_entidade ||'.');
            END LOOP;
        END IF;
        EXECUTE stBanco;
    END LOOP;
    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';


/*
* Script de Virada de Ano: virada_para_2014.sql
*/

   -- Exclusao de possiveis funcoes de manutencao.
   CREATE OR REPLACE FUNCTION public.manutencaokiller() RETURNS VOID AS $$
   DECLARE
      reRegistro         RECORD;
      varFuncao          VARCHAR;
   BEGIN
       FOR reRegistro IN SELECT 'DROP FUNCTION '         ||
                                pg_namespace.nspname     ||
                                '.'                      ||
                                pg_proc.proname          ||
                                '( '                     ||
                                Btrim(pg_catalog.oidvectortypes(pg_proc.proargtypes)) ||
                                ' ) '  as comando
                           FROM pg_catalog.pg_proc LEFT JOIN pg_catalog.pg_namespace  ON pg_namespace.oid = pg_proc.pronamespace
                          WHERE pg_proc.prorettype    != 'pg_catalog.cstring'::pg_catalog.regtype
                            AND pg_namespace.nspname  != 'pg_catalog'
                            AND ( pg_proc.proname      = 'manutencao' OR pg_proc.proname ILIKE 'temp%' OR pg_proc.proname ILIKE 'tmp%' )
                            ORDER BY 1
       LOOP
          varFuncao := reRegistro.comando;
          EXECUTE varFuncao;
       END LOOP;

       RETURN;
   END;
   $$ LANGUAGE 'plpgsql';

   SELECT        public.manutencaokiller();
   DROP FUNCTION public.manutencaokiller();

   --
   -- Procedimento 01
   --
   CREATE TEMP TABLE tmp_exer_permis
                  AS SELECT ano_exercicio
                FROM administracao.permissao
            GROUP BY ano_exercicio
            ORDER BY 1
                   ;

   DELETE
     FROM administracao.auditoria
    WHERE numcgm = 0
      AND cod_acao IN ( SELECT acao.cod_acao
                          FROM administracao.modulo
                             , administracao.funcionalidade
                             , administracao.acao
                         WHERE modulo.cod_modulo                 = funcionalidade.cod_modulo
                           AND funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
                      )
        ;

   DELETE
     FROM administracao.permissao
    WHERE numcgm = 0
      AND cod_acao IN ( SELECT acao.cod_acao
                          FROM administracao.modulo
                             , administracao.funcionalidade
                             , administracao.acao
                         WHERE modulo.cod_modulo                 = funcionalidade.cod_modulo
                           AND funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
                      )
        ;

   CREATE OR REPLACE FUNCTION public.manutencao() RETURNS VOID AS $$
   DECLARE
      recRecno RECORD;
   BEGIN
      FOR recRecno
       IN   SELECT ano_exercicio
              FROM tmp_exer_permis
          GROUP BY ano_exercicio
          ORDER BY 1
      LOOP
         INSERT
           INTO administracao.permissao
              ( numcgm
              , cod_acao
              , ano_exercicio
              )
         SELECT 0
              , acao.cod_acao
              , recRecno.ano_exercicio
           FROM administracao.modulo
              , administracao.funcionalidade
              , administracao.acao
          WHERE (
                     (     modulo.cod_modulo = 2
                        OR modulo.cod_modulo = 4
                     )
                  OR (     modulo.cod_modulo = 30
                       AND cod_acao IN (1124,1335,1334,1127,1126,1125,1128)
                     )
                )
            AND modulo.cod_modulo                 = funcionalidade.cod_modulo
            AND funcionalidade.cod_funcionalidade = acao.cod_funcionalidade
            AND 0 = (
                      SELECT COALESCE(Count(1),0)
                        FROM administracao.permissao
                       WHERE permissao.ano_exercicio = recRecno.ano_exercicio
                         AND permissao.cod_acao      = acao.cod_acao
                         AND permissao.numcgm        = 0
                    )
              ;

      END LOOP;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql'
   ;

   SELECT         public.manutencao();
   DROP  FUNCTION public.manutencao();

   INSERT
     INTO administracao.permissao
        ( numcgm
        , cod_acao
        , ano_exercicio
        )
   SELECT numcgm
        , cod_acao
        , '2014'
     FROM administracao.permissao AS perm
    WHERE perm.ano_exercicio = '2013'
      AND 0 = (
                SELECT COALESCE(Count(1),0)
                  FROM administracao.permissao
                 WHERE permissao.ano_exercicio = '2014'
                   AND permissao.cod_acao      = perm.cod_acao
                   AND permissao.numcgm        = perm.numcgm
              )
        ;

   DELETE
     FROM Administracao.permissao
    WHERE numcgm != 0
      AND ano_exercicio = '2013'
        ;


   -- Procedimento 02
   INSERT
     INTO administracao.configuracao
        ( exercicio
        , cod_modulo
        , parametro
        , valor
        )
   SELECT '2014'
        , cod_modulo
        , parametro
        , valor
     FROM administracao.configuracao AS proximo
    WHERE exercicio='2013'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.configuracao
                        WHERE exercicio  = '2014'
                          and cod_modulo = proximo.cod_modulo
                          and parametro  = proximo.parametro
                     )
        ;

   -- No caso da prefeitura ter inserido uma nova entidade apos a criacao do prcamento 2013.
   INSERT
     INTO orcamento.entidade 
        ( exercicio
        , cod_entidade
        , numcgm
        , cod_responsavel
        , cod_resp_tecnico
        , cod_profissao
        , sequencia
        )
   SELECT '2014'
        , cod_entidade
        , numcgm
        , cod_responsavel
        , cod_resp_tecnico
        , cod_profissao
        , sequencia
     FROM orcamento.entidade AS proximo
    WHERE exercicio = '2013'
      AND NOT EXISTS (
                       SELECT 1
                         FROM orcamento.entidade
                        WHERE exercicio    = '2014'
                          AND cod_entidade = proximo.cod_entidade
                     )
        ;

   -- No caso da prefeitura ter inserido uma nova entidade apos a criacao do orcamento 2013.
   INSERT
     INTO orcamento.usuario_entidade
        ( exercicio
        , numcgm
        , cod_entidade
        )
   SELECT '2014'
        , numcgm
        , cod_entidade
     FROM orcamento.usuario_entidade as proximo
    WHERE exercicio = '2013'
      AND NOT EXISTS (
                       SELECT 1
                         FROM orcamento.usuario_entidade
                        WHERE exercicio    = '2014'
                          AND cod_entidade = proximo.cod_entidade
                     )
        ;


   INSERT
     INTO administracao.configuracao_entidade
        ( exercicio
        , cod_entidade
        , cod_modulo
        , parametro
        , valor
        )
   SELECT '2014'
        , cod_entidade
        , cod_modulo
        , parametro
        , valor
     FROM administracao.configuracao_entidade as proximo
    WHERE exercicio = '2013'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.configuracao_entidade
                        WHERE exercicio    = '2014'
                          AND cod_entidade = proximo.cod_entidade
                          AND cod_modulo   = proximo.cod_modulo
                          AND parametro    = proximo.parametro
                    )
        ;

   INSERT
     INTO administracao.entidade_rh
        ( exercicio
        , cod_entidade
        , schema_cod
        )
   SELECT '2014'
        , cod_entidade
        , schema_cod
     FROM administracao.entidade_rh as proximo
    WHERE exercicio = '2013'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.entidade_rh
                        WHERE exercicio    = '2014'
                          AND cod_entidade = proximo.cod_entidade
                     )
        ;

    -- ASSINATURA
    INSERT
      INTO administracao.assinatura
         ( exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cargo
         )
    SELECT '2014' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cargo
      FROM administracao.assinatura AS proximo
     WHERE exercicio = '2013'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura
                         WHERE exercicio    = '2014'
                           AND cod_entidade = proximo.cod_entidade
                           AND numcgm       = proximo.numcgm
                           AND timestamp    = proximo.timestamp
                           AND cargo        = proximo.cargo
                      )
         ;

    INSERT
      INTO administracao.assinatura_crc
         ( exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , insc_crc
         )
    SELECT '2014' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , insc_crc
      FROM administracao.assinatura_crc AS proximo
     WHERE exercicio = '2013'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura_crc
                         WHERE exercicio    = '2014'
                           AND cod_entidade = proximo.cod_entidade
                           AND numcgm       = proximo.numcgm
                           AND timestamp    = proximo.timestamp
                           AND insc_crc     = proximo.insc_crc
                      )
         ;

    INSERT
      INTO administracao.assinatura_modulo
         ( exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cod_modulo
         )
    SELECT '2014' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cod_modulo
      FROM administracao.assinatura_modulo AS proximo
     WHERE exercicio = '2013'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura_modulo
                         WHERE exercicio    = '2014'
                           AND cod_entidade = proximo.cod_entidade
                           AND numcgm       = proximo.numcgm
                           AND timestamp    = proximo.timestamp
                           AND cod_modulo   = proximo.cod_modulo
                      )
         ;



   -- Procedimento 03
   UPDATE administracao.configuracao
      SET valor = '2014'
    WHERE exercicio = '2014'
      AND parametro = 'ano_exercicio'
        ;

   DELETE
     FROM administracao.configuracao
    WHERE exercicio = '2013'
     AND parametro  = 'diretorio'
       ;




-- VERIFICACAO DE UTILIZACAO OU NAO DOS MODULOS GF --
-----------------------------------------------------

CREATE OR REPLACE FUNCTION atualiza_gf() RETURNS VOID AS $$
DECLARE
    stAux       VARCHAR;
BEGIN

    PERFORM 1
       FROM orcamento.receita
      WHERE exercicio = '2013';

    IF FOUND THEN

        PERFORM 1
           FROM orcamento.conta_receita
          WHERE exercicio = '2014'
              ;

        IF NOT FOUND THEN
            RAISE EXCEPTION 'É necessário gerar o exercício seguinte na elaboração do orçamento para a aplicação do pacote da virada.'; 
        END IF;

            INSERT
              INTO contabilidade.plano_recurso
                 ( cod_plano
                 , exercicio
                 , cod_recurso
                 )
            SELECT proximo.cod_plano
                 , '2014' AS exercicio
                 , proximo.cod_recurso
              FROM contabilidade.plano_recurso AS proximo
              JOIN orcamento.recurso
                ON recurso.cod_recurso                                       = proximo.cod_recurso
               AND CAST((CAST(recurso.exercicio AS INTEGER) - 1) AS VARCHAR) = proximo.exercicio
             WHERE recurso.exercicio = '2014'
               AND NOT EXISTS (
                                SELECT 1
                                  FROM contabilidade.plano_recurso
                                 WHERE exercicio   = '2014'
                                   AND cod_plano   = proximo.cod_plano
                                   AND cod_recurso = proximo.cod_recurso
                              )
                 ;

                -----------------------------------------------------------------------------------------
                -- TCE RS 
                INSERT
                  INTO tcers.credor
                     ( exercicio
                     , numcgm
                     , tipo
                     )
                SELECT '2014'
                     , numcgm
                     , tipo
                  FROM tcers.credor AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.credor
                                     WHERE exercicio = '2014'
                                       AND numcgm    = proximo.numcgm
                                  )
                     ;

                INSERT
                  INTO tcers.uniorcam
                     ( numcgm
                     , exercicio
                     , num_unidade
                     , num_orgao
                     , identificador
                     )
                SELECT numcgm
                     , '2014'
                     , num_unidade
                     , num_orgao
                     , identificador
                  FROM tcers.uniorcam AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.uniorcam
                                     WHERE exercicio   = '2014'
                                       AND numcgm      = proximo.numcgm
                                       AND num_unidade = proximo.num_unidade
                                       AND num_orgao   = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcers.rd_extra
                     ( cod_conta
                     , exercicio
                     , classificacao
                     )
                SELECT cod_conta
                     , '2014'
                     , classificacao
                  FROM tcers.rd_extra AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.rd_extra
                                     WHERE exercicio = '2014'
                                       AND cod_conta = proximo.cod_conta
                                  )
                     ;

                INSERT
                  INTO tcers.modelo_lrf
                     ( exercicio
                     , cod_modelo
                     , nom_modelo
                     , nom_modelo_orcamento
                     )
                SELECT '2014'
                     , cod_modelo
                     , nom_modelo
                     , nom_modelo_orcamento
                  FROM  tcers.modelo_lrf AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.modelo_lrf
                                     WHERE exercicio='2014'
                                       AND cod_modelo = proximo.cod_modelo
                                  )
                     ;

                INSERT
                  INTO tcers.quadro_modelo_lrf
                     ( exercicio
                     , cod_modelo
                     , cod_quadro
                     , nom_quadro
                     )
                SELECT '2014'
                     , cod_modelo
                     , cod_quadro
                     , nom_quadro
                  FROM tcers.quadro_modelo_lrf AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.quadro_modelo_lrf
                                     WHERE exercicio='2014'
                                       AND cod_modelo = proximo.cod_modelo
                                       AND cod_quadro = proximo.cod_quadro
                                  )
                     ;


                -----------------------------------------------------------------------------------------
                -- TCM GO

                INSERT
                  INTO tcmgo.orgao
                     ( exercicio
                     , num_orgao
                     , numcgm_orgao
                     , numcgm_contador
                     , cod_tipo
                     , crc_contador
                     , uf_crc_contador
                     )
                SELECT '2014'
                     , num_orgao
                     , numcgm_orgao
                     , numcgm_contador
                     , cod_tipo
                     , crc_contador
                     , uf_crc_contador
                  FROM tcmgo.orgao AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao
                                     WHERE exercicio='2014'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.orgao_controle_interno
                     ( exercicio
                     , num_orgao
                     , numcgm
                     )
                SELECT '2014'
                     , num_orgao
                     , numcgm
                  FROM tcmgo.orgao_controle_interno AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao_controle_interno
                                     WHERE exercicio='2014'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.elemento_de_para
                     ( exercicio
                     , cod_conta
                     , estrutural
                     )
                SELECT '2014'
                     , cod_conta
                     , estrutural
                  FROM tcmgo.elemento_de_para AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.elemento_de_para
                                     WHERE exercicio='2014'
                                       AND cod_conta = proximo.cod_conta
                                  )
                     ;

                INSERT
                  INTO tcmgo.orgao_representante
                     ( exercicio
                     , num_orgao
                     , numcgm
                     )
                SELECT '2014'
                     , num_orgao
                     , numcgm
                  FROM tcmgo.orgao_representante AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao_representante
                                     WHERE exercicio='2014'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.tipo_retencao
                     ( exercicio
                     , cod_tipo
                     , descricao
                     )
                SELECT '2014'
                     , cod_tipo
                     , descricao
                  FROM tcmgo.tipo_retencao AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.tipo_retencao
                                     WHERE exercicio = '2014'
                                       AND cod_tipo = proximo.cod_tipo
                                  )
                     ;


                -----------------------------------------------------------------------------------------
                -- TCE RN

                INSERT
                  INTO tcern.contrato_aditivo
                     ( exercicio
                     , num_convenio
                     , cod_entidade
                     , num_contrato_aditivo
                     , exercicio_aditivo
                     , cod_processo
                     , exercicio_processo
                     , bimestre
                     , cod_objeto
                     , valor_aditivo
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                     )
                SELECT '2014'
                     , num_convenio
                     , cod_entidade
                     , num_contrato_aditivo
                     , exercicio_aditivo
                     , cod_processo
                     , exercicio_processo
                     , bimestre
                     , cod_objeto
                     , valor_aditivo
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                  FROM tcern.contrato_aditivo AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.contrato_aditivo
                                     WHERE exercicio='2014'
                                       AND num_convenio         = proximo.num_convenio
                                       AND cod_entidade         = proximo.cod_entidade
                                       AND num_contrato_aditivo = proximo.num_contrato_aditivo
                                       AND exercicio_aditivo    = proximo.exercicio_aditivo
                                  )
                     ;

                INSERT
                  INTO tcern.obra
                     ( exercicio
                     , cod_entidade
                     , num_obra
                     , obra
                     , objetivo
                     , localizacao
                     , cod_cidade
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , valor_orcamento_base
                     , projeto_existente
                     , observacao
                     , latitude
                     , longitude
                     , rdc
                     )
                SELECT '2014'
                     , cod_entidade
                     , num_obra
                     , obra
                     , objetivo
                     , localizacao
                     , cod_cidade
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , valor_orcamento_base
                     , projeto_existente
                     , observacao
                     , latitude
                     , longitude
                     , rdc
                  FROM tcern.obra AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.obra
                                     WHERE exercicio='2014'
                                       AND cod_entidade = proximo.cod_entidade
                                       AND num_obra     = proximo.num_obra
                                  )
                     ;

                INSERT
                  INTO tcern.unidade_orcamentaria
                     ( exercicio
                     , id
                     , cod_institucional
                     , cgm_unidade_orcamentaria
                     , cod_norma
                     , id_unidade_gestora
                     , situacao
                     , num_unidade
                     , num_orgao
                     )
                SELECT '2014'
                     , id
                     , cod_institucional
                     , cgm_unidade_orcamentaria
                     , cod_norma
                     , id_unidade_gestora
                     , situacao
                     , num_unidade
                     , num_orgao
                  FROM tcern.unidade_orcamentaria AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.unidade_orcamentaria
                                     WHERE exercicio='2014'
                                       AND id = proximo.id
                                  )
                     ;


                INSERT
                  INTO tcern.convenio
                     ( exercicio
                     , cod_entidade
                     , num_convenio
                     , cod_processo
                     , exercicio_processo
                     , numcgm_recebedor
                     , cod_objeto
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                     )
                SELECT '2014'
                     , cod_entidade
                     , num_convenio
                     , cod_processo
                     , exercicio_processo
                     , numcgm_recebedor
                     , cod_objeto
                     , cod_recurso_1
                     , cod_recurso_2
                     , cod_recurso_3
                     , valor_recurso_1
                     , valor_recurso_2
                     , valor_recurso_3
                     , dt_inicio_vigencia
                     , dt_termino_vigencia
                     , dt_assinatura
                     , dt_publicacao
                  FROM tcern.convenio AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.convenio
                                     WHERE exercicio='2014'
                                       AND cod_entidade = proximo.cod_entidade
                                       AND num_convenio = proximo.num_convenio
                                  )
                     ;

                INSERT
                  INTO tcern.unidade_gestora
                     ( exercicio
                     , id 
                     , cod_institucional
                     , cgm_unidade
                     , personalidade
                     , administracao
                     , natureza
                     , cod_norma
                     , situacao
                     )
                SELECT '2014'
                     , id 
                     , cod_institucional
                     , cgm_unidade
                     , personalidade
                     , administracao
                     , natureza
                     , cod_norma
                     , situacao
                  FROM tcern.unidade_gestora AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.unidade_gestora
                                     WHERE exercicio='2014'
                                       AND id = proximo.id
                                  )
                     ;



                -----------------------------------------------------------------------------------------
                -- STN
                INSERT
                  INTO stn.vinculo_stn_receita
                     ( exercicio
                     , cod_receita
                     , cod_tipo
                     )
                SELECT '2014'
                     , cod_receita
                     , cod_tipo
                  FROM stn.vinculo_stn_receita AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.vinculo_stn_receita
                                     WHERE exercicio='2014'
                                       AND cod_receita = proximo.cod_receita
                                       AND cod_tipo    = proximo.cod_tipo
                                  )
                     ;

                INSERT
                  INTO stn.vinculo_recurso
                     ( exercicio
                     , cod_entidade
                     , num_orgao
                     , num_unidade
                     , cod_recurso
                     , cod_vinculo
                     , cod_tipo
                     )
                SELECT '2014'
                     , cod_entidade
                     , num_orgao
                     , num_unidade
                     , cod_recurso
                     , cod_vinculo
                     , cod_tipo
                  FROM stn.vinculo_recurso AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.vinculo_recurso
                                     WHERE exercicio='2014'
                                       AND cod_entidade = proximo.cod_entidade
                                       AND num_orgao    = proximo.num_orgao
                                       AND num_unidade  = proximo.num_unidade
                                       AND cod_recurso  = proximo.cod_recurso
                                       AND cod_vinculo  = proximo.cod_vinculo
                                       AND cod_tipo     = proximo.cod_tipo
                                  )
                     ;

                INSERT
                  INTO stn.riscos_fiscais
                     ( exercicio
                     , cod_risco
                     , cod_entidade
                     , descricao 
                     , valor
                     )
                SELECT '2014'
                     , cod_risco
                     , cod_entidade
                     , descricao
                     , valor
                  FROM stn.riscos_fiscais AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.riscos_fiscais
                                     WHERE exercicio='2014'
                                       AND cod_risco    = proximo.cod_risco
                                       AND cod_entidade = proximo.cod_entidade
                                  )
                     ;

                INSERT
                  INTO stn.recurso_rreo_anexo_14
                     ( exercicio
                     , cod_recurso
                     )
                SELECT '2014'
                     , cod_recurso
                  FROM stn.recurso_rreo_anexo_14 AS proximo
                 WHERE exercicio = '2013'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.recurso_rreo_anexo_14
                                     WHERE exercicio='2014'
                                       AND cod_recurso = proximo.cod_recurso
                                  )
                     ;

    ELSE -- RETIRAR NA PROXIMA VIRADA A REPLICACAO DE 2 EXERCICIOS
        -- RECEITA 2012-2013
        ----------
        INSERT
          INTO orcamento.conta_receita
             ( exercicio
             , cod_conta
             , cod_norma
             , descricao
             , cod_estrutural
             )
        SELECT '2013' AS exercicio
             , cod_conta
             , cod_norma
             , descricao
             , cod_estrutural
          FROM orcamento.conta_receita AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.conta_receita
                             WHERE exercicio = '2013'
                               AND cod_conta = proximo.cod_conta
                          )
             ;
        INSERT
          INTO orcamento.posicao_receita
             ( exercicio
             , cod_posicao
             , mascara
             , cod_tipo
             )
        SELECT '2013' AS exercicio
             , cod_posicao
             , mascara
             , cod_tipo
          FROM orcamento.posicao_receita AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.posicao_receita
                             WHERE exercicio   = '2013'
                               AND cod_tipo    = proximo.cod_tipo
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        INSERT
          INTO orcamento.classificacao_receita
             ( exercicio
             , cod_posicao
             , cod_conta
             , cod_classificacao
             , cod_tipo
             )
        SELECT '2013' AS exercicio
             , cod_posicao
             , cod_conta
             , cod_classificacao
             , cod_tipo
          FROM orcamento.classificacao_receita AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.classificacao_receita
                             WHERE exercicio   = '2013'
                               AND cod_tipo    = proximo.cod_tipo
                               AND cod_posicao = proximo.cod_posicao
                               AND cod_conta   = proximo.cod_conta
                          )
             ;
        -- RECEITA 2013-2014
        ----------
        INSERT
          INTO orcamento.conta_receita
             ( exercicio
             , cod_conta
             , cod_norma
             , descricao
             , cod_estrutural
             )
        SELECT '2014' AS exercicio
             , cod_conta
             , cod_norma
             , descricao
             , cod_estrutural
          FROM orcamento.conta_receita AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.conta_receita
                             WHERE exercicio = '2014'
                               AND cod_conta = proximo.cod_conta
                          )
             ;
        INSERT
          INTO orcamento.posicao_receita
             ( exercicio
             , cod_posicao
             , mascara
             , cod_tipo
             )
        SELECT '2014' AS exercicio
             , cod_posicao
             , mascara
             , cod_tipo
          FROM orcamento.posicao_receita AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.posicao_receita
                             WHERE exercicio   = '2014'
                               AND cod_tipo    = proximo.cod_tipo
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        INSERT
          INTO orcamento.classificacao_receita
             ( exercicio
             , cod_posicao
             , cod_conta
             , cod_classificacao
             , cod_tipo
             )
        SELECT '2014' AS exercicio
             , cod_posicao
             , cod_conta
             , cod_classificacao
             , cod_tipo
          FROM orcamento.classificacao_receita AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.classificacao_receita
                             WHERE exercicio   = '2014'
                               AND cod_tipo    = proximo.cod_tipo
                               AND cod_posicao = proximo.cod_posicao
                               AND cod_conta   = proximo.cod_conta
                          )
             ;

        
        -- DESPESA 2012-2013
        ----------
        INSERT
          INTO orcamento.conta_despesa
             ( exercicio
             , cod_conta
             , descricao
             , cod_estrutural
             )
        SELECT '2013' AS exercicio
             , cod_conta
             , descricao
             , cod_estrutural
          FROM orcamento.conta_despesa AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.conta_despesa
                             WHERE exercicio = '2013'
                               AND cod_conta = proximo.cod_conta
                          )
             ;
        INSERT
          INTO orcamento.posicao_despesa
             ( exercicio
             , cod_posicao
             , mascara
             )
        SELECT '2013' AS exercicio
             , cod_posicao
             , mascara
          FROM orcamento.posicao_despesa AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.posicao_despesa
                             WHERE exercicio   = '2013'
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        INSERT
          INTO orcamento.classificacao_despesa
             ( exercicio
             , cod_conta
             , cod_posicao
             , cod_classificacao
             )
        SELECT '2013' AS exercicio
             , cod_conta
             , cod_posicao
             , cod_classificacao
          FROM orcamento.classificacao_despesa AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.classificacao_despesa
                             WHERE exercicio   = '2013'
                               AND cod_conta   = proximo.cod_conta
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        -- DESPESA 2013-2014
        ----------
        INSERT
          INTO orcamento.conta_despesa
             ( exercicio
             , cod_conta
             , descricao
             , cod_estrutural
             )
        SELECT '2014' AS exercicio
             , cod_conta
             , descricao
             , cod_estrutural
          FROM orcamento.conta_despesa AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.conta_despesa
                             WHERE exercicio = '2014'
                               AND cod_conta = proximo.cod_conta
                          )
             ;
        INSERT
          INTO orcamento.posicao_despesa
             ( exercicio
             , cod_posicao
             , mascara
             )
        SELECT '2014' AS exercicio
             , cod_posicao
             , mascara
          FROM orcamento.posicao_despesa AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.posicao_despesa
                             WHERE exercicio   = '2014'
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        INSERT
          INTO orcamento.classificacao_despesa
             ( exercicio
             , cod_conta
             , cod_posicao
             , cod_classificacao
             )
        SELECT '2014' AS exercicio
             , cod_conta
             , cod_posicao
             , cod_classificacao
          FROM orcamento.classificacao_despesa AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM orcamento.classificacao_despesa
                             WHERE exercicio   = '2014'
                               AND cod_conta   = proximo.cod_conta
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;

        -- HISTORICO 2012-2013
        ------------
        DELETE
          FROM contabilidade.historico_contabil
         WHERE exercicio = '2013'
             ;
        INSERT
          INTO contabilidade.historico_contabil
        SELECT cod_historico
             , '2013' as exercicio
             , nom_historico
             , complemento
             , historico_interno
          FROM contabilidade.historico_contabil AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM contabilidade.historico_contabil
                             WHERE exercicio     = '2013'
                               AND cod_historico = proximo.cod_historico
                          )
             ;

        INSERT
          INTO empenho.historico
        SELECT 0
             , '2013'
             , 'Nao Informado'
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM empenho.historico
                      WHERE cod_historico = 0
                        AND exercicio     = '2013'
                   )
             ;
        -- HISTORICO 2013-2014
        ------------
        INSERT
          INTO contabilidade.historico_contabil
        SELECT cod_historico
             , '2014' as exercicio
             , nom_historico
             , complemento
             , historico_interno
          FROM contabilidade.historico_contabil AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM contabilidade.historico_contabil
                             WHERE exercicio     = '2014'
                               AND cod_historico = proximo.cod_historico
                          )
             ;

        -- POSICAO PLANO 2012-2013
        ----------------
        INSERT
          INTO contabilidade.posicao_plano
        SELECT '2013' as exercicio
             , cod_posicao,mascara
          FROM contabilidade.posicao_plano AS proximo
         WHERE exercicio = '2012'
           AND NOT EXISTS (
                            SELECT 1
                              FROM contabilidade.posicao_plano
                             WHERE exercicio   = '2013'
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        -- POSICAO PLANO 2013-2014
        ----------------
        INSERT
          INTO contabilidade.posicao_plano
        SELECT '2014' as exercicio
             , cod_posicao,mascara
          FROM contabilidade.posicao_plano AS proximo
         WHERE exercicio = '2013'
           AND NOT EXISTS (
                            SELECT 1
                              FROM contabilidade.posicao_plano
                             WHERE exercicio   = '2014'
                               AND cod_posicao = proximo.cod_posicao
                          )
             ;
        
        UPDATE administracao.configuracao
            SET valor = '1'
          WHERE parametro = 'multiplos_boletim'
            AND cod_modulo = 30
              ;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        atualiza_gf();
DROP FUNCTION atualiza_gf();


--
-- Procedimento 18
--
    DELETE
      FROM administracao.configuracao
     WHERE parametro = 'virada_GF'
       AND exercicio = '2014'
         ;


--------------------------------
-- Replicacao de ima.codigo_dirf
--------------------------------

    SELECT atualizarbanco('
                           INSERT
                             INTO ima.codigo_dirf
                                ( exercicio
                                , cod_dirf
                                , tipo
                                , descricao
                                )
                           SELECT ''2014'' AS exercicio
                                , cod_dirf
                                , tipo
                                , descricao
                             FROM ima.codigo_dirf
                            WHERE exercicio = ''2013''
                                ;
                          ')
         ;

----------------------------------
-- GRANTs PARA USUARIO/GRUPO urbem
----------------------------------

--
UPDATE      administracao.usuario SET password = 'adpaTlj9FC2po';
ALTER TABLE administracao.usuario ALTER COLUMN password TYPE CHAR(13);
--

GRANT ALL ON organograma.vw_orgao_nivel TO GROUP urbem;

CREATE OR REPLACE FUNCTION gera_grants() RETURNS VOID AS $$
DECLARE
    stSql   VARCHAR;
BEGIN
    -- GRANTS
    stSql := 'GRANT ALL ON SCHEMA publico,' ||
              array_to_string( ARRAY(
                                      SELECT DISTINCT schema_name::text
                                        FROM information_schema.schemata
                                       WHERE schema_name NOT IN ( 'information_schema'
                                                                , 'pg_catalog'
                                                                , 'bethadba'
                                                                , 'bethadba2'
                                                                , 'bethadba3'
                                                                )
                                    ), ',' )
              || ' TO urbem;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON FUNCTION ' ||
              array_to_string( ARRAY(
                                      SELECT busca.schema || '.' || busca.nome || '(' || busca.args || ')'
                                        FROM (
                                               SELECT pg_namespace.nspname                            AS schema
                                                    , pg_proc.proname::text                           AS nome
                                                    , pg_catalog.oidvectortypes(pg_proc.proargtypes)  AS args
                                                 FROM pg_catalog.pg_proc
                                                 JOIN pg_catalog.pg_namespace
                                                   ON pg_namespace.oid = pg_proc.pronamespace
                                                WHERE pg_proc.oid > 200000
                                                  AND proisagg = FALSE
                                             ) AS busca
                                    ), ',' )
              || ' TO siamweb;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_schema::text || '.' || table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 )
                                    ), ',' )
              || ' TO urbem;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema = 'public'
                                    ), ',' )
              || ' TO urbem;';
    EXECUTE stSql;


    stSql := 'GRANT ALL ON ' ||
              array_to_string( ARRAY(
                                        SELECT pg_namespace.nspname || '.' || pg_class.relname
                                          FROM pg_class
                                             , pg_attribute
                                             , pg_namespace
                                         WHERE pg_class.relkind ='S'
                                           AND pg_namespace.oid = pg_class.relnamespace
                                      GROUP BY pg_namespace.nspname
                                             , pg_class.relname
                                    ), ',' )
              || ' TO urbem;';
    EXECUTE stSql;

-- REVOKES
    stSql := 'REVOKE ALL ON SCHEMA publico,' ||
              array_to_string( ARRAY(
                                      SELECT DISTINCT schema_name::text
                                        FROM information_schema.schemata
                                       WHERE schema_name NOT IN ( 'information_schema'
                                                                , 'pg_catalog'
                                                                , 'bethadba'
                                                                , 'bethadba2'
                                                                , 'bethadba3'
                                                                )
                                    ), ',' )
              || ' FROM siamweb;';
    EXECUTE stSql;


    stSql := 'REVOKE ALL ON FUNCTION ' ||
              array_to_string( ARRAY(
                                      SELECT busca.schema || '.' || busca.nome || '(' || busca.args || ')'
                                        FROM (
                                               SELECT pg_namespace.nspname                            AS schema
                                                    , pg_proc.proname::text                           AS nome
                                                    , pg_catalog.oidvectortypes(pg_proc.proargtypes)  AS args
                                                 FROM pg_catalog.pg_proc
                                                 JOIN pg_catalog.pg_namespace
                                                   ON pg_namespace.oid = pg_proc.pronamespace
                                                WHERE pg_proc.oid > 20000
                                                  AND proisagg = FALSE
                                             ) AS busca
                                    ), ',' )
              || ' FROM siamweb;';
    EXECUTE stSql;


    stSql := 'REVOKE ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_schema::text || '.' || table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema NOT IN ( 'information_schema'
                                                                 , 'pg_catalog'
                                                                 , 'bethadba'
                                                                 , 'bethadba2'
                                                                 , 'bethadba3'
                                                                 )
                                    ), ',' )
              || ' FROM siamweb;';
    EXECUTE stSql;


    stSql := 'REVOKE ALL ON ' ||
              array_to_string( ARRAY(
                                      SELECT table_name::text
                                        FROM information_schema.tables
                                       WHERE table_schema = 'public'
                                    ), ',' )
              || ' FROM siamweb;';
    EXECUTE stSql;


    stSql := 'REVOKE ALL ON ' ||
              array_to_string( ARRAY(
                                        SELECT pg_namespace.nspname || '.' || pg_class.relname
                                          FROM pg_class
                                             , pg_attribute
                                             , pg_namespace
                                         WHERE pg_class.relkind ='S'
                                           AND pg_namespace.oid = pg_class.relnamespace
                                      GROUP BY pg_namespace.nspname
                                             , pg_class.relname
                                    ), ',' )
              || ' FROM siamweb;';
    EXECUTE stSql;

-- USERS

    stSql := 'REASSIGN OWNED BY siamweb TO urbem;';
    EXECUTE stSql;

    stSql := 'DROP USER siamweb;';
    EXECUTE stSql;

END;
$$ LANGUAGE 'plpgsql';

SELECT        gera_grants();
DROP FUNCTION gera_grants();


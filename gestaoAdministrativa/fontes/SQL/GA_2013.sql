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
* Versao 2.01.3
*
* Fabio Bertoldi - 20121220
*
*/

----------------
-- Ticket #19951
----------------

/*
* Script de Virada de Ano: virada_para_2013.sql
*/

   -- Exclusão de possiveis funçoes de manutencao.
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
   CREATE TEMP TABLE tmp_exer_permis AS SELECT ano_exercicio  FROM administracao.permissao  GROUP BY ano_exercicio ORDER BY 1;

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
          WHERE ((modulo.cod_modulo = 2 OR modulo.cod_modulo = 4) OR (modulo.cod_modulo = 30 AND cod_acao IN (1124,1335,1334,1127,1126,1125,1128)))
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
        , '2013'
     FROM administracao.permissao AS perm
    WHERE perm.ano_exercicio = '2012'
      AND 0 = (
                SELECT COALESCE(Count(1),0)
                  FROM administracao.permissao
                 WHERE permissao.ano_exercicio = '2013'
                   AND permissao.cod_acao      = perm.cod_acao
                   AND permissao.numcgm        = perm.numcgm
              )
        ;

   DELETE
     FROM Administracao.permissao
    WHERE numcgm != 0
      AND ano_exercicio = '2012'
        ;


   -- Procedimento 02
   INSERT
     INTO administracao.configuracao
        ( exercicio
        , cod_modulo
        , parametro
        , valor
        )
   SELECT '2013'
        , cod_modulo
        , parametro
        , valor
     FROM administracao.configuracao AS proximo
    WHERE exercicio='2012'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.configuracao
                        WHERE exercicio  = '2013'
                          and cod_modulo = proximo.cod_modulo
                          and parametro  = proximo.parametro
                     )
        ;

   -- No caso da pref.  ter sido inserido uma nova entidade após a criação do orçamento 2012.
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
   SELECT '2013'
        , cod_entidade
        , numcgm
        , cod_responsavel
        , cod_resp_tecnico
        , cod_profissao
        , sequencia
     FROM orcamento.entidade AS proximo
    WHERE exercicio = '2012'
      AND NOT EXISTS (
                       SELECT 1
                         FROM orcamento.entidade
                        WHERE exercicio    = '2013'
                          AND cod_entidade = proximo.cod_entidade
                     )
        ;

   -- No caso da pref.  ter sido inserido uma nova entidade após a criação do orçamento 2012.
   INSERT
     INTO orcamento.usuario_entidade
        ( exercicio
        , numcgm
        , cod_entidade
        )
   SELECT '2013'
        , numcgm
        , cod_entidade
     FROM orcamento.usuario_entidade as proximo
    WHERE exercicio = '2012'
      AND NOT EXISTS (
                       SELECT 1
                         FROM orcamento.usuario_entidade
                        WHERE exercicio    = '2013'
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
   SELECT '2013'
        , cod_entidade
        , cod_modulo
        , parametro
        , valor
     FROM administracao.configuracao_entidade as proximo
    WHERE exercicio = '2012'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.configuracao_entidade
                        WHERE exercicio    = '2013'
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
   SELECT '2013'
        , cod_entidade
        , schema_cod
     FROM administracao.entidade_rh as proximo
    WHERE exercicio = '2012'
      AND NOT EXISTS (
                       SELECT 1
                         FROM administracao.entidade_rh
                        WHERE exercicio    = '2013'
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
    SELECT '2013' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cargo
      FROM administracao.assinatura AS proximo
     WHERE exercicio = '2012'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura
                         WHERE exercicio    = '2013'
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
    SELECT '2013' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , insc_crc
      FROM administracao.assinatura_crc AS proximo
     WHERE exercicio = '2012'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura_crc
                         WHERE exercicio    = '2013'
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
    SELECT '2013' AS exercicio
         , cod_entidade
         , numcgm
         , timestamp
         , cod_modulo
      FROM administracao.assinatura_modulo AS proximo
     WHERE exercicio = '2012'
       AND NOT EXISTS ( SELECT 1
                          FROM administracao.assinatura_modulo
                         WHERE exercicio    = '2013'
                           AND cod_entidade = proximo.cod_entidade
                           AND numcgm       = proximo.numcgm
                           AND timestamp    = proximo.timestamp
                           AND cod_modulo   = proximo.cod_modulo
                      )
         ;



   -- Procedimento 03
   UPDATE administracao.configuracao
      SET valor = '2013'
    WHERE exercicio = '2013'
      AND parametro = 'ano_exercicio'
        ;

   DELETE
     FROM administracao.configuracao
    WHERE exercicio = '2012'
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
      WHERE exercicio = '2012';

    IF FOUND THEN
                -----------------------------------------------------------------------------------------
                -- TCE RS 
                INSERT
                  INTO tcers.credor
                     ( exercicio
                     , numcgm
                     , tipo
                     )
                SELECT '2013'
                     , numcgm
                     , tipo
                  FROM tcers.credor AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.credor
                                     WHERE exercicio = '2013'
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
                     , '2013'
                     , num_unidade
                     , num_orgao
                     , identificador
                  FROM tcers.uniorcam AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.uniorcam
                                     WHERE exercicio   = '2013'
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
                     , '2013'
                     , classificacao
                  FROM tcers.rd_extra AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.rd_extra
                                     WHERE exercicio = '2013'
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
                SELECT '2013'
                     , cod_modelo
                     , nom_modelo
                     , nom_modelo_orcamento
                  FROM  tcers.modelo_lrf AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.modelo_lrf
                                     WHERE exercicio='2013'
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
                SELECT '2013'
                     , cod_modelo
                     , cod_quadro
                     , nom_quadro
                  FROM tcers.quadro_modelo_lrf AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcers.quadro_modelo_lrf
                                     WHERE exercicio='2013'
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
                SELECT '2013'
                     , num_orgao
                     , numcgm_orgao
                     , numcgm_contador
                     , cod_tipo
                     , crc_contador
                     , uf_crc_contador
                  FROM tcmgo.orgao AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao
                                     WHERE exercicio='2013'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.orgao_controle_interno
                     ( exercicio
                     , num_orgao
                     , numcgm
                     )
                SELECT '2013'
                     , num_orgao
                     , numcgm
                  FROM tcmgo.orgao_controle_interno AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao_controle_interno
                                     WHERE exercicio='2013'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.elemento_de_para
                     ( exercicio
                     , cod_conta
                     , estrutural
                     )
                SELECT '2013'
                     , cod_conta
                     , estrutural
                  FROM tcmgo.elemento_de_para AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.elemento_de_para
                                     WHERE exercicio='2013'
                                       AND cod_conta = proximo.cod_conta
                                  )
                     ;

                INSERT
                  INTO tcmgo.orgao_representante
                     ( exercicio
                     , num_orgao
                     , numcgm
                     )
                SELECT '2013'
                     , num_orgao
                     , numcgm
                  FROM tcmgo.orgao_representante AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.orgao_representante
                                     WHERE exercicio='2013'
                                       AND num_orgao = proximo.num_orgao
                                  )
                     ;

                INSERT
                  INTO tcmgo.tipo_retencao
                     ( exercicio
                     , cod_tipo
                     , descricao
                     )
                SELECT '2013'
                     , cod_tipo
                     , descricao
                  FROM tcmgo.tipo_retencao AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcmgo.tipo_retencao
                                     WHERE exercicio = '2013'
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
                SELECT '2013'
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
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.contrato_aditivo
                                     WHERE exercicio='2013'
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
                SELECT '2013'
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
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.obra
                                     WHERE exercicio='2013'
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
                SELECT '2013'
                     , id
                     , cod_institucional
                     , cgm_unidade_orcamentaria
                     , cod_norma
                     , id_unidade_gestora
                     , situacao
                     , num_unidade
                     , num_orgao
                  FROM tcern.unidade_orcamentaria AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.unidade_orcamentaria
                                     WHERE exercicio='2013'
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
                SELECT '2013'
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
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.convenio
                                     WHERE exercicio='2013'
                                       AND cod_entidade = proximo.cod_entidade
                                       AND num_convenio = proximo.num_convenio
                                  )
                     ;

                INSERT
                  INTO tcern.receita_tc
                     ( exercicio
                     , cod_receita
                     , cod_tc
                     )
                SELECT '2013'
                     , cod_receita
                     , cod_tc
                  FROM tcern.receita_tc AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.receita_tc
                                     WHERE exercicio='2013'
                                       AND cod_receita = proximo.cod_receita
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
                SELECT '2013'
                     , id 
                     , cod_institucional
                     , cgm_unidade
                     , personalidade
                     , administracao
                     , natureza
                     , cod_norma
                     , situacao
                  FROM tcern.unidade_gestora AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM tcern.unidade_gestora
                                     WHERE exercicio='2013'
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
                SELECT '2013'
                     , cod_receita
                     , cod_tipo
                  FROM stn.vinculo_stn_receita AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.vinculo_stn_receita
                                     WHERE exercicio='2013'
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
                SELECT '2013'
                     , cod_entidade
                     , num_orgao
                     , num_unidade
                     , cod_recurso
                     , cod_vinculo
                     , cod_tipo
                  FROM stn.vinculo_recurso AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.vinculo_recurso
                                     WHERE exercicio='2013'
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
                SELECT '2013'
                     , cod_risco
                     , cod_entidade
                     , descricao
                     , valor
                  FROM stn.riscos_fiscais AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.riscos_fiscais
                                     WHERE exercicio='2013'
                                       AND cod_risco    = proximo.cod_risco
                                       AND cod_entidade = proximo.cod_entidade
                                  )
                     ;

                INSERT
                  INTO stn.recurso_rreo_anexo_14
                     ( exercicio
                     , cod_recurso
                     )
                SELECT '2013'
                     , cod_recurso
                  FROM stn.recurso_rreo_anexo_14 AS proximo
                 WHERE exercicio = '2012'
                   AND NOT EXISTS (
                                    SELECT 1
                                      FROM stn.recurso_rreo_anexo_14
                                     WHERE exercicio='2013'
                                       AND cod_recurso = proximo.cod_recurso
                                  )
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
       AND exercicio = '2013'
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
                           SELECT ''2013'' AS exercicio
                                , cod_dirf
                                , tipo
                                , descricao
                             FROM ima.codigo_dirf
                            WHERE exercicio = ''2012''
                                ;
                          ')
         ;


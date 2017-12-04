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
* $Revision$
* $Name$
* $Author$
* $Date$
*
*/
--
-- Criação da função orcamento.recurso();
--
   drop type orcamento.tp_recurso_direto     cascade;
   CREATE TYPE orcamento.tp_recurso_direto AS ( cod_recurso       INTEGER
                                              , exercicio         CHAR(4)
                                              , nom_recurso       VARCHAR(200)
                                              , tipo              CHAR(1)
                                              , nom_tipo          VARCHAR(09)
                                              , cod_fonte         INTEGER
                                              , masc_recurso      VARCHAR(13)
                                              , masc_recurso_red  VARCHAR(13)
                                              , cod_uso           VARCHAR
                                              , cod_destinacao    VARCHAR
                                              , cod_especificacao VARCHAR
                                              , cod_detalhamento  VARCHAR
                                              , finalidade		  VARCHAR
                                              )
   ;


   --
   --
   --
   CREATE OR REPLACE FUNCTION orcamento.recurso( varExercicio VARCHAR(4))
   RETURNS SETOF orcamento.tp_recurso_direto  AS $$
   DECLARE
      recRecno                         RECORD;
      tyRecurso                        orcamento.tp_recurso_direto%ROWTYPE;
      bolRecursoDestinacao             BOOLEAN;
      varMascaraRecurso_destinacao     VARCHAR;
      varMascaraRecurso                VARCHAR;
   BEGIN
      SELECT valor INTO bolRecursoDestinacao         FROM administracao.configuracao WHERE cod_modulo = 8 AND parametro = 'recurso_destinacao'      AND exercicio = varExercicio;

      IF bolRecursoDestinacao THEN
         SELECT valor INTO varMascaraRecurso_destinacao FROM administracao.configuracao WHERE cod_modulo = 8 AND parametro = 'masc_recurso_destinacao' AND exercicio=varExercicio;

         FOR recRecno
          IN SELECT recurso.cod_recurso                       AS cod_recurso
                 , recurso.exercicio                          AS exercicio
                 , especificacao_destinacao_recurso.descricao AS nom_recurso
                 , ''                                         AS tipo
                 , ''                                         AS nom_tipo
                 , especificacao_destinacao_recurso.cod_fonte AS cod_fonte
                 , recurso.cod_fonte                          AS masc_recurso
                 , substr(recurso.cod_fonte,0,7)              AS masc_recurso_red
                 , recurso_destinacao.cod_uso                 AS cod_uso
                 , recurso_destinacao.cod_destinacao          AS cod_destinacao
                 , recurso_destinacao.cod_especificacao       AS cod_especificacao
                 , recurso_destinacao.cod_detalhamento        AS cod_detalhamento
              FROM orcamento.recurso
                 , orcamento.recurso_destinacao
                 , orcamento.especificacao_destinacao_recurso
             WHERE recurso.exercicio                      = recurso_destinacao.exercicio
               AND recurso.cod_recurso                    = recurso_destinacao.cod_recurso
               AND recurso_destinacao.exercicio           = especificacao_destinacao_recurso.exercicio
               AND recurso_destinacao.cod_especificacao   = especificacao_destinacao_recurso.cod_especificacao
               AND recurso.exercicio   = varExercicio
             ORDER BY recurso.exercicio, recurso.cod_recurso
         LOOP
            tyRecurso.cod_recurso        := recRecno.cod_recurso ;
            tyRecurso.exercicio          := recRecno.exercicio   ;
            tyRecurso.nom_recurso        := recRecno.nom_recurso ;
            tyRecurso.nom_tipo           := recRecno.nom_tipo    ;
            tyRecurso.tipo               := recRecno.tipo        ;
            tyRecurso.cod_fonte          := recRecno.cod_fonte   ;
            tyRecurso.masc_recurso       := recRecno.masc_recurso;
            tyRecurso.masc_recurso_red   := recRecno.masc_recurso_red;
            tyRecurso.cod_uso            := recRecno.cod_uso;
            tyRecurso.cod_destinacao     := recRecno.cod_destinacao;
            tyRecurso.cod_especificacao  := recRecno.cod_especificacao;
            tyRecurso.cod_detalhamento   := recRecno.cod_detalhamento;

            RETURN NEXT tyRecurso;

         END LOOP;
      ELSE
         SELECT valor INTO varMascaraRecurso FROM administracao.configuracao WHERE cod_modulo = 8 AND parametro='masc_recurso' AND exercicio=varExercicio;

         FOR recRecno
          IN SELECT recurso.cod_recurso                                                    AS cod_recurso
                  , recurso.exercicio                                                      AS exercicio
                  , recurso_direto.nom_recurso                                             AS nom_recurso
	        	  , recurso_direto.tipo
                  , CASE WHEN recurso_direto.tipo = 'V' THEN 'Vinculado' ELSE 'Livre'  END AS nom_tipo
                  , recurso_direto.cod_fonte                                               AS cod_fonte
		          , sw_fn_mascara_dinamica(varMascaraRecurso::varchar,recurso_direto.cod_recurso::varchar) as masc_recurso
                  , sw_fn_mascara_dinamica(varMascaraRecurso::varchar,recurso_direto.cod_recurso::varchar) AS masc_recurso_red
                  , ''       AS cod_uso
                  , ''       AS cod_destinacao
                  , ''       AS cod_especificacao
                  , ''       AS cod_detalhamento
                  , recurso_direto.finalidade											   AS finalidade

               FROM orcamento.recurso
                  , orcamento.recurso_direto
              WHERE recurso.exercicio                      = recurso_direto.exercicio
                AND recurso.cod_recurso                    = recurso_direto.cod_recurso
                AND recurso.exercicio                      = varExercicio
              ORDER BY recurso.exercicio, recurso.cod_recurso
         LOOP
            tyRecurso.cod_recurso        := recRecno.cod_recurso ;
            tyRecurso.exercicio          := recRecno.exercicio   ;
            tyRecurso.nom_recurso        := recRecno.nom_recurso ;
            tyRecurso.nom_tipo           := recRecno.nom_tipo    ;
            tyRecurso.tipo               := recRecno.tipo        ;
            tyRecurso.cod_fonte          := recRecno.cod_fonte   ;
            tyRecurso.masc_recurso       := recRecno.masc_recurso;
            tyRecurso.masc_recurso_red   := recRecno.masc_recurso_red;
            tyRecurso.cod_uso            := recRecno.cod_uso;
            tyRecurso.cod_destinacao     := recRecno.cod_destinacao;
            tyRecurso.cod_especificacao  := recRecno.cod_especificacao;
            tyRecurso.cod_detalhamento   := recRecno.cod_detalhamento;
            tyRecurso.finalidade		 := recRecno.finalidade;            

            RETURN NEXT tyRecurso;

         END LOOP;
      END IF;

      RETURN;
   END;
   $$ LANGUAGE 'plpgsql'
   ;




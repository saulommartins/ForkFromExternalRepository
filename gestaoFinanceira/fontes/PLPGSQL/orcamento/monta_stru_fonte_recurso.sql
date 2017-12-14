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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28900 $
* $Name$
* $Author: melo $
* $Date: 2008-03-31 16:01:59 -0300 (Seg, 31 Mar 2008) $
*
* Casos de uso: uc-02.01.38
*/

/*
$Log$
Revision 1.1  2007/09/25 20:45:59  gris
Alteração Recurso Destinação.

*/
   --
   --
   --
   CREATE OR REPLACE FUNCTION orcamento.fn_monta_stru_fonte_recurso() RETURNS TRIGGER AS $$
   DECLARE
      bolRecursoDestinacao       BOOLEAN;
      varMascRecurso             VARCHAR;
      varRecurso                 VARCHAR;
      varNomRecurso              VARCHAR;
      varCodRecurso                  VARCHAR := '';
      intQtdCaracter             INTEGER;
      intCount                   INTEGER := 0;
   BEGIN
      SELECT BTRIM(valor)::VARCHAR  INTO bolRecursoDestinacao
        FROM administracao.configuracao
       WHERE configuracao.exercicio    = OLD.exercicio
         AND configuracao.cod_modulo   = 8
         AND configuracao.parametro    = 'recurso_destinacao'::text;

      SELECT valor         ,  LENGTH(BTRIM(REPLACE(valor, '.', '')))
        INTO varMascRecurso, intQtdCaracter
        FROM administracao.configuracao
       WHERE configuracao.exercicio    = OLD.exercicio
         AND configuracao.cod_modulo   = 8
         AND configuracao.parametro    = 'masc_recurso';

      IF bolRecursoDestinacao THEN
         SELECT BTRIM(LPAD(recurso_destinacao.cod_uso::text          ,1, '0')) || '.' ||
                BTRIM(LPAD(recurso_destinacao.cod_destinacao::text   ,1, '0')) || '.' ||
                BTRIM(LPAD(recurso_destinacao.cod_especificacao::text,2, '0')) || '.' ||
                BTRIM(LPAD(recurso_destinacao.cod_detalhamento::text ,6, '0'))
              , especificacao_destinacao_recurso.descricao
           INTO varCodRecurso, varNomRecurso
           FROM orcamento.recurso_destinacao
              , orcamento.especificacao_destinacao_recurso
          WHERE recurso_destinacao.exercicio           = OLD.exercicio
            AND recurso_destinacao.cod_recurso         = OLD.cod_recurso
            AND recurso_destinacao.exercicio           = especificacao_destinacao_recurso.exercicio
            AND recurso_destinacao.cod_especificacao   = especificacao_destinacao_recurso.cod_especificacao
         ;
      ELSE
         SELECT BTRIM(LPAD(recurso_direto.cod_recurso::text, intQtdCaracter, '0')), recurso_direto.nom_recurso
           INTO varRecurso, varNomRecurso
           FROM orcamento.recurso_direto
          WHERE recurso_direto.exercicio     = OLD.exercicio
            AND recurso_direto.cod_recurso   = OLD.cod_recurso
         ;

         varCodRecurso :=  sw_fn_mascara_dinamica(varMascRecurso::varchar, varRecurso::varchar) as masc_recurso;

      END IF;

      NEW.cod_fonte     := varCodRecurso;
      NEW.nom_recurso   := varNomRecurso;
      RETURN NEW;
   END;
   $$ LANGUAGE 'plpgsql';


   CREATE OR REPLACE FUNCTION orcamento.fn_atualiza_stru_fonte_recurso() RETURNS TRIGGER AS $$
   DECLARE
   BEGIN
      UPDATE orcamento.recurso SET cod_fonte = cod_fonte  WHERE recurso.exercicio = NEW.exercicio  AND recurso.cod_recurso = NEW.cod_recurso;
      RETURN NEW;
   END;
   $$ LANGUAGE 'plpgsql';


--  CREATE TRIGGER tr_monta_stru_fonte_recurso
--     BEFORE UPDATE ON orcamento.recurso
--      FOR EACH ROW EXECUTE PROCEDURE orcamento.fn_monta_stru_fonte_recurso();
--
--    CREATE TRIGGER tr_atualiza_stru_fonte_recurso
--     AFTER UPDATE OR INSERT ON orcamento.recurso_direto
--      FOR EACH ROW EXECUTE PROCEDURE orcamento.fn_atualiza_stru_fonte_recurso();
--
--    CREATE TRIGGER tr_atualiza_stru_fonte_recurso
--     AFTER UPDATE OR INSERT  ON orcamento.recurso_destinacao
--      FOR EACH ROW EXECUTE PROCEDURE orcamento.fn_atualiza_stru_fonte_recurso();

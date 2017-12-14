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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_recupera_acrescimo_modalidade_relatorio_divida_juros.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.10
*/

CREATE OR REPLACE FUNCTION recupera_acrescimo_modalidade_relatorio_divida_juros(
    inCodInscricao integer,
    inExercicio    varchar,
    inCodAcrescimo integer,
    inCodTipo      integer)
  RETURNS VARCHAR AS $$
DECLARE
    stSqlAcrescimos     VARCHAR;
    stRetorno           VARCHAR;
    reRecordAcrescimos  RECORD;
BEGIN
    stSqlAcrescimos := '
    SELECT valor
      FROM divida.divida_acrescimo
      JOIN monetario.acrescimo
        USING(cod_acrescimo, cod_tipo)
      JOIN divida.modalidade_acrescimo
        USING(cod_acrescimo, cod_tipo)
     WHERE divida_acrescimo.cod_tipo      = '||inCodTipo||'
       AND divida_acrescimo.cod_acrescimo = '||inCodAcrescimo||'
       AND divida_acrescimo.cod_inscricao = '||inCodInscricao||'
       AND divida_acrescimo.exercicio     = '''||inExercicio||'''  
     GROUP BY 1
    ';
        FOR reRecordAcrescimos IN EXECUTE stSqlAcrescimos LOOP
               stRetorno := reRecordAcrescimos.valor;
        END LOOP;
   IF stRetorno IS NULL THEN
      stRetorno := '0';
   END IF;
   return stRetorno;
END;
$$ LANGUAGE plpgsql IMMUTABLE;


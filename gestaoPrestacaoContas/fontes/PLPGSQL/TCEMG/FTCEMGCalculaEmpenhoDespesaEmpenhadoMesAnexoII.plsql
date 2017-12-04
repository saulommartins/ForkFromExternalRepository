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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.22, uc-02-08-01
*/

CREATE OR REPLACE  FUNCTION tcemg.fn_despesa_empenhado_mes_anexo_ii(character varying, character varying, character varying, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer) RETURNS numeric
AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    inCodConta          ALIAS FOR $4;
    inCodOrgao          ALIAS FOR $5;
    inCodUnidade        ALIAS FOR $6;
    inCodFuncao         ALIAS FOR $7;
    inCodSubFuncao      ALIAS FOR $8;
    inCodNumPAO         ALIAS FOR $9;
    inCodPrograma       ALIAS FOR $10;
    inCodEntidade       ALIAS FOR $11;
    inCodRecurso        ALIAS FOR $12;
    inCodReduzido       ALIAS FOR $13;

    dtInicioAno         VARCHAR;
    dtFim               VARCHAR;
    stSql               VARCHAR := '';
    nuSoma              NUMERIC := 0;
    nuRetorno           NUMERIC := 0;
    reRegistro          RECORD;
BEGIN

    dtInicioAno := '01/01/' || stExercicio;

    IF stExercicio >= TO_CHAR(now(), 'yyyy') THEN
        dtFim := TO_CHAR(NOW(), 'dd/mm/yyyy');
    ELSE
        dtFim := '31/12/' || stExercicio;
    END IF;

    stSql := 'SELECT (coalesce(Empenhado.empenhado_ano - (coalesce(EAnulada.anulado_ano,0.00)),0.00)) AS empenhado_ano,
                     (coalesce(Empenhado.empenhado_per - (coalesce(EAnulada.anulado_per,0.00)),0.00)) AS empenhado_per
               FROM
               (SELECT SUM( CASE WHEN TE.dt_empenho >= TO_DATE('|| quote_literal(dtInicioAno) ||',''dd/mm/yyyy'') AND
                                    TE.dt_empenho <= TO_DATE('|| quote_literal(stDtFinal) ||',''dd/mm/yyyy'') 
                               THEN TE.vl_total
                               ELSE 0.00
                          END
                    ) AS empenhado_ano,
                    SUM( CASE WHEN TE.dt_empenho >= TO_DATE('|| quote_literal(stDtInicial) ||',''dd/mm/yyyy'') AND
                                   TE.dt_empenho <= TO_DATE('|| quote_literal(stDtFinal) ||',''dd/mm/yyyy'')  
                               THEN TE.vl_total
                               ELSE 0.00
                         END
                    ) AS empenhado_per
               
               FROM  tmp_empenhado as TE
               
               WHERE TE.cod_conta      = ' || inCodConta      || '
                 And TE.num_unidade    = ' || inCodUnidade    || '
                 And TE.num_orgao      = ' || inCodOrgao      || '
                 And TE.num_pao        = ' || inCodNumPAO     || '
                 And TE.cod_funcao     = ' || inCodFuncao     || '
                 And TE.cod_subfuncao  = ' || inCodSubFuncao  || '
                 And TE.cod_programa   = ' || inCodPrograma   || '
                 And TE.cod_recurso    = ' || inCodRecurso    || '
                 And TE.cod_despesa    = ' || inCodReduzido   || ') AS Empenhado,

               (SELECT SUM( CASE WHEN TEA.dt_anulado >= TO_DATE('|| quote_literal(dtInicioAno) ||',''dd/mm/yyyy'') AND
                                    TEA.dt_anulado <= TO_DATE('|| quote_literal(stDtFinal) ||',''dd/mm/yyyy'') 
                               THEN TEA.vl_anulado
                               ELSE 0.00
                          END
                    ) AS anulado_ano,
                    SUM( CASE WHEN TEA.dt_anulado >= TO_DATE('|| quote_literal(stDtInicial) ||',''dd/mm/yyyy'') AND
                                   TEA.dt_anulado <= TO_DATE('|| quote_literal(stDtFinal) ||',''dd/mm/yyyy'')  
                               THEN TEA.vl_anulado
                               ELSE 0.00
                         END
                    ) AS  anulado_per
               from
                    tmp_empenhado_anulada  as TEA

               WHERE TEA.cod_conta      = ' || inCodConta      || '
                 And TEA.num_unidade    = ' || inCodUnidade    || '
                 And TEA.num_orgao      = ' || inCodOrgao      || '
                 And TEA.num_pao        = ' || inCodNumPAO     || '
                 And TEA.cod_funcao     = ' || inCodFuncao     || '
                 And TEA.cod_subfuncao  = ' || inCodSubFuncao  || '
                 And TEA.cod_programa   = ' || inCodPrograma   || '
                 And TEA.cod_recurso    = ' || inCodRecurso    || '
                 And TEA.cod_despesa    = ' || inCodReduzido   || ') AS EAnulada';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        nuRetorno := reRegistro.empenhado_per;
    END LOOP;

    RETURN nuRetorno;
END;
$$
LANGUAGE plpgsql;
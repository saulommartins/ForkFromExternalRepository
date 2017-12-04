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

/*
$Log$
Revision 1.2  2006/07/05 20:38:04  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE  FUNCTION empenho.fn_despesa_liquidado_mes_ano_pad(character varying, character varying, character varying, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer) RETURNS numeric[]
    AS '
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

    stSql               VARCHAR   := '''';
    nuSoma              NUMERIC   := 0;
    reRegistro          RECORD;
    nuRetorno           NUMERIC[] := array[0.00];
    dtInicioAno         VARCHAR;
BEGIN

    dtInicioAno := ''01/01/'' || stExercicio;

    stSql := ''SELECT (((coalesce(tliquidado.valor_liquidado_ano,0.00))) - (coalesce(tanulado.valor_anulado_ano,0.00))) as liquidado_ano,
                      (((coalesce(tliquidado.valor_liquidado_per,0.00))) - (coalesce(tanulado.valor_anulado_per,0.00))) as liquidado_per
               FROM
                   (SELECT
                    Sum( Case When TNL.dt_liquidacao >= to_date(''''''|| dtInicioAno ||'''''',''''dd/mm/yyyy'''') And TNL.dt_liquidacao <=to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')  Then
                                   TNL.vl_total
                              Else 0.00 End ) as valor_liquidado_ano,
                    Sum( Case When TNL.dt_liquidacao >= to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') And TNL.dt_liquidacao <=to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')  Then
                                   TNL.vl_total
                              Else 0.00 End )     as valor_liquidado_per
                    from
                         tmp_nota_liquidacao       as TNL
                    Where
                          TNL.cod_conta                = ''|| inCodConta     ||''
                      And TNL.cod_entidade             = ''|| inCodEntidade  ||''
                      And TNL.num_unidade              = ''|| inCodUnidade   ||''
                      And TNL.num_orgao                = ''|| inCodOrgao     ||''
                      And TNL.num_pao                  = ''|| inCodNumPAO    ||''
                      And TNL.cod_funcao               = ''|| inCodFuncao    ||''
                      And TNL.cod_subfuncao            = ''|| inCodSubFuncao ||''
                      And TNL.cod_programa             = ''|| inCodPrograma  ||''
                      And TNL.cod_recurso              = ''|| inCodRecurso   ||''
                      And TNL.cod_despesa              = ''|| inCodReduzido  ||'' ) as tliquidado,

                     (Select
                        Sum( Case When to_date(to_char(TNLA.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') >= to_date(''''''|| dtInicioAno ||'''''',''''dd/mm/yyyy'''') And to_date(to_char(TNLA.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')  Then
                                  TNLA.vl_anulado
                                  Else 0.00 End) as valor_anulado_ano,
                        Sum( Case When to_date(to_char(TNLA.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') >= to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') And to_date(to_char(TNLA.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') <= to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')  Then
                                       TNLA.vl_anulado
                                  Else 0.00 End )     as valor_anulado_per
                        from
                             tmp_nota_liquidacao_anulada  as TNLA

                        Where
                              TNLA.cod_entidade          = ''|| inCodEntidade  ||''
                          And TNLA.num_unidade           = ''|| inCodUnidade   ||''
                          And TNLA.num_orgao             = ''|| inCodOrgao     ||''
                          And TNLA.cod_conta             = ''|| inCodConta     ||''
                          And TNLA.num_pao               = ''|| inCodNumPAO    ||''
                          And TNLA.cod_funcao            = ''|| inCodFuncao    ||''
                          And TNLA.cod_subfuncao         = ''|| inCodSubFuncao ||''
                          And TNLA.cod_programa          = ''|| inCodPrograma  ||''
                          And TNLA.cod_entidade          = ''|| inCodEntidade  ||''
                          And TNLA.cod_recurso           = ''|| inCodRecurso   ||''
                          And TNLA.cod_despesa           = ''|| inCodReduzido  ||'') as tanulado '';

    FOR reRegistro IN EXECUTE stSql
    LOOP
       nuRetorno[1] := reRegistro.liquidado_ano;
       nuRetorno[2] := reRegistro.liquidado_per;
    END LOOP;

    RETURN nuRetorno;
END;
'
    LANGUAGE plpgsql;

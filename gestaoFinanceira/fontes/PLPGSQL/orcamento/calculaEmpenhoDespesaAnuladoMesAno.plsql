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
Revision 1.9  2006/07/05 20:38:04  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_despesa_anulado_mes_ano(character varying, character varying, character varying, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer) RETURNS numeric[]
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

    stSql               VARCHAR   := '';
    nuSoma              NUMERIC   := 0;
    reRegistro          RECORD;
    dtInicioAno         VARCHAR;
    dtFim               VARCHAR;
    nuRetorno           NUMERIC[] := array[0.00];

BEGIN
    dtInicioAno := '01/01/' || stExercicio;

    IF stExercicio >= TO_CHAR(now(), 'yyyy') THEN
        dtFim := TO_CHAR(NOW(), 'dd/mm/yyyy');
    ELSE
        dtFim := '31/12/' || stExercicio;
    END IF;

    stSql := 'SELECT
               Sum(Case When to_date(to_char(TA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date('|| quote_literal( dtInicioAno ) ||',''dd/mm/yyyy'') And 
                             to_date(to_char(TA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('|| quote_literal( stDtFinal ) ||',''dd/mm/yyyy'')  
                        Then TA.vl_anulado
                        Else 0.00 End) as anulado_ano,
               Sum( Case When to_date(to_char(TA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date('|| quote_literal( stDtInicial ) ||',''dd/mm/yyyy'') And 
                              to_date(to_char(TA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('|| quote_literal( stDtFinal ) ||',''dd/mm/yyyy'') 
                        Then TA.vl_anulado
                        Else 0.00 End )as anulado_per
               from tmp_anulado  as TA

               Where
                     TA.cod_conta             = '|| inCodConta    ||'
                 And TA.num_orgao             = '|| inCodOrgao    ||'
                 And TA.num_unidade           = '|| inCodUnidade  ||'
                 And TA.num_pao               = '|| inCodNumPAO   ||'
                 And TA.cod_recurso           = '|| inCodRecurso  ||'
                 And TA.cod_despesa           = '|| inCodReduzido ||'
                 And TA.cod_entidade          = '|| inCodEntidade ||'
                 And TA.cod_funcao            = '|| inCodFuncao   ||'
                 And TA.cod_subfuncao         = '|| inCodSubFuncao  ||'
                 And TA.cod_programa          = '|| inCodPrograma;

    FOR reRegistro IN EXECUTE stSql
    LOOP
       nuRetorno[1] := reRegistro.anulado_ano;
       nuRetorno[2] := reRegistro.anulado_per;
    END LOOP;

    RETURN nuRetorno;
END;
$$
LANGUAGE plpgsql;
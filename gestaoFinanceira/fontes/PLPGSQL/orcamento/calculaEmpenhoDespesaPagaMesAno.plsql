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

CREATE OR REPLACE FUNCTION empenho.fn_despesa_paga_mes_ano(character varying, character varying, character varying, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer) RETURNS numeric[]
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

    stSql := ' SELECT (TPaga.pago_ano - (coalesce(TAnulada.anulado_ano,0.00))) as pago_ano,
                      (TPaga.pago_per - (coalesce(TAnulada.anulado_per,0.00))) as pago_per
               FROM
               (SELECT
               Sum( Case When to_date(to_char(TNLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date('|| quote_literal( dtInicioAno ) ||',''dd/mm/yyyy'') And 
                              to_date(to_char(TNLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('|| quote_literal( stDtFinal ) ||',''dd/mm/yyyy'') 
                         Then TNLP.vl_pago
                         Else 0.00 End)  as pago_ano,
               Sum( Case When to_date(to_char(TNLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date('|| quote_literal( stDtInicial ) ||',''dd/mm/yyyy'') And 
                              to_date(to_char(TNLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('|| quote_literal( stDtFinal ) ||',''dd/mm/yyyy'') 
                         Then TNLP.vl_pago
                             Else 0.00 End ) as pago_per
               from
                    tmp_nota_liquidacao_paga         as TNLP

               Where
                     TNLP.cod_conta             = '|| inCodConta ||'
                 And TNLP.cod_entidade          = '||inCodEntidade||'
                 And TNLP.cod_despesa           ='|| inCodReduzido  ||'
                 And TNLP.cod_recurso           ='|| inCodRecurso  ||'
                 And TNLP.num_unidade           ='|| inCodUnidade ||'
                 And TNLP.num_orgao             ='|| inCodOrgao   ||'
                 And TNLP.num_pao               ='|| inCodNumPAO   ||'
                 And TNLP.cod_funcao            ='|| inCodFuncao   ||'
                 And TNLP.cod_subfuncao         ='|| inCodSubFuncao  ||'
                 And TNLP.cod_programa          ='|| inCodPrograma  ||') as TPaga,

               (SELECT
               Sum( Case When to_date(to_char(TNLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date('|| quote_literal( dtInicioAno ) ||',''dd/mm/yyyy'') And 
                              to_date(to_char(TNLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('|| quote_literal( stDtFinal ) ||',''dd/mm/yyyy'')
                         Then TNLPA.vl_anulado
                         Else 0.00 End)    as anulado_ano,
               Sum( Case When to_date(to_char(TNLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') >= to_date('|| quote_literal( stDtInicial ) ||',''dd/mm/yyyy'') And 
                              to_date(to_char(TNLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('|| quote_literal( stDtFinal ) ||',''dd/mm/yyyy'')
                         Then TNLPA.vl_anulado
                         Else 0.00 End )   as anulado_per
               from
                    tmp_nota_liquidacao_paga_anulada  as TNLPA

               Where
                     TNLPA.num_unidade           ='|| inCodUnidade   ||'
                 And TNLPA.num_orgao             ='|| inCodOrgao     ||'
                 And TNLPA.num_pao               ='|| inCodNumPAO    ||'
                 And TNLPA.cod_funcao            ='|| inCodFuncao    ||'
                 And TNLPA.cod_subfuncao         ='|| inCodSubFuncao ||'
                 And TNLPA.cod_programa          ='|| inCodPrograma  ||'
                 And TNLPA.cod_conta             ='|| inCodConta     ||'
                 And TNLPA.cod_recurso           ='|| inCodRecurso   ||'
                 And TNLPA.cod_despesa           ='|| inCodReduzido  ||'
                 And TNLPA.cod_entidade          ='|| inCodEntidade  ||' ) as TAnulada ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
       nuRetorno[1] := reRegistro.pago_ano;
       nuRetorno[2] := reRegistro.pago_per;
    END LOOP;

    RETURN nuRetorno;
END;
$$
LANGUAGE plpgsql;
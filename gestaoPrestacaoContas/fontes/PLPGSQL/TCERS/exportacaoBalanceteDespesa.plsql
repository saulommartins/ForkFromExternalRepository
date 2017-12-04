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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.6  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.5  2006/07/05 20:37:44  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_balancete_despesa(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1   ;
    stCodEntidades      ALIAS FOR $2   ;
    stDataInicial       ALIAS FOR $3   ;
    stDataFinal         ALIAS FOR $4   ;
    stSql               VARCHAR   := '';
    reRegistro          RECORD         ;
BEGIN
    stSql := '
    SELECT
         0 as cod_despesa,
         tabela.num_orgao,
         tabela.num_unidade,
         tabela.cod_funcao,
         tabela.cod_subfuncao,
         tabela.cod_programa,
         0 as cod_subprograma,
         tabela.num_pao,
         cod_subelemento,
         tabela.cod_recurso,
         SUM(tabela.saldo_inicial) as saldo_inicial,
         0 as atualizacao,
         SUM(coalesce(tabela.credito_suplementar,0.00)) as credito_suplementar,
         SUM(coalesce(tabela.credito_especial,0.00)) as credito_especial,
         SUM(coalesce(tabela.credito_extraordinario,0.00)) as credito_extraordinario,
         SUM(coalesce(tabela.reducoes,0.00)) as reducoes,
         0.00 as suplementacao,
         0.00 as reducao,
         SUM(tabela.empenhado_per) as empenhado_per,
         SUM(tabela.anulado_per) as anulado_per,
         SUM(tabela.liquidado_per) as liquidado_per,
         SUM(tabela.pago_per) as pago_per,

         MAX(tabela.total_creditos),

         0.00 as valor_liquidado,
         0.00 as recomposicao,
         0.00 as previsao
    FROM (
        SELECT
            tabela.cod_despesa,
            tabela.num_orgao,
            tabela.num_unidade,
            tabela.cod_funcao,
            tabela.cod_subfuncao,
            tabela.cod_programa,
            0 as cod_subprograma,
            tabela.num_pao,
            cast(substr(replace(tabela.classificacao,''.'',''''),1,6) as integer) as cod_subelemento,
            --tabela.classificacao as cod_subelemento,
            tabela.cod_recurso,
            --SUM(tabela.saldo_inicial) as saldo_inicial,
            tabela.saldo_inicial as saldo_inicial,
            0 as atualizacao,

            coalesce(tabela.credito_suplementar,0.00) as credito_suplementar,
            coalesce(tabela.credito_especial,0.00) as credito_especial,
            coalesce(tabela.credito_extraordinario,0.00) as credito_extraordinario,

            coalesce(tabela.reducoes,0.00) as reducoes,
            0.00 as suplementacao,
            0.00 as reducao,
            SUM(tabela.empenhado_per) as empenhado_per,
            SUM(tabela.anulado_per) as anulado_per,
            SUM(tabela.liquidado_per) as liquidado_per,
            SUM(tabela.pago_per) as pago_per,
            tabela.total_creditos as total_creditos,
            0.00 as valor_liquidado,
            0.00 as recomposicao,
            0.00 as previsao

            FROM
            orcamento.fn_balancete_despesa_pad('|| quote_literal(stExercicio) ||','|| quote_literal(stCodEntidades)||','|| quote_literal(stDataInicial) ||','|| quote_literal(stDataFinal) ||','''','''','''','''','''')
            as tabela
                    (
             		exercicio       char(4),
             		cod_despesa     integer,
             		cod_entidade    integer,
             		cod_programa    integer,
                    num_pao         integer,
             		cod_conta       integer,             		
             		num_orgao       integer,
             		num_unidade     integer,
             		cod_recurso     integer,
             		cod_funcao      integer,
             		cod_subfuncao   integer,
             		tipo_conta      varchar,
             		vl_original     numeric,
             		dt_criacao      date,
             		classificacao   varchar,
             		descricao       varchar,
             		num_recurso     varchar,
             		empenhado_ano   numeric,
             		empenhado_per   numeric,
             		anulado_ano     numeric,
             		anulado_per     numeric,
             		pago_ano        numeric,
             		pago_per        numeric,
              		liquidado_ano   numeric,
              		liquidado_per   numeric,
              		saldo_inicial   numeric,
             		suplementacoes  numeric,
              		reducoes        numeric,
              		total_creditos  numeric,
              		credito_suplementar  numeric,
              		credito_especial numeric,
              		credito_extraordinario  numeric
                    )
              GROUP BY
                  tabela.num_orgao,
                  tabela.num_unidade,
                  tabela.cod_funcao,
                  tabela.cod_subfuncao,
                  tabela.cod_programa,
                  tabela.num_pao,
                  tabela.cod_recurso,
                  cast(substr(replace(tabela.classificacao,''.'',''''),1,6) as integer),
                  tabela.cod_despesa,
                  tabela.saldo_inicial,
                  tabela.total_creditos,

                  tabela.reducoes,

                  tabela.credito_suplementar,
                  tabela.credito_especial,
                  tabela.credito_extraordinario

         ) as tabela
         GROUP BY
             tabela.num_orgao,
             tabela.num_unidade,
             tabela.cod_funcao,
             tabela.cod_subfuncao,
             tabela.cod_programa,
             tabela.num_pao,
             tabela.cod_recurso,
             tabela.cod_subelemento

    ';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';


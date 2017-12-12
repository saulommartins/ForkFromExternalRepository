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
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_relatorio_execucao_funcao_subfuncao(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    stSql               VARCHAR   := '''';
    stMascClassDespesa  VARCHAR   := '''';
    stMascRecurso       VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN
    stSql := ''CREATE TEMPORARY TABLE tmp_relatorio AS
                SELECT
                    cod_funcao                   ,
                    cod_subfuncao                ,
                    SUM( (empenho_per - anulado_per) ) as vl_empenhado ,
                    SUM( Liquidado_per               ) as Liquidado_per,
                    SUM( Pago_per                    ) as Pago_per
                FROM
                 tcers.fn_exportacao_balancete_despesa( ''|| quote_literal(stExercicio)   ||'',
                                                        ''|| quote_literal(stFiltro)      ||'',
                                                        ''|| quote_literal(stDataInicial) ||'',
                                                        ''|| quote_literal(stDataFinal)   ||'') AS tabela(
                        cod_despesa                  integer,
                        num_orgao                    integer,
                        num_unidade                  integer,
                        cod_funcao                   integer,
                        cod_subfuncao                integer,
                        cod_programa                 integer,
                        cod_subprograma              integer,
                        num_pao                      integer,
                        cod_subelemento              integer,
                        cod_recurso                  integer,
                        saldo_inicial                numeric,
                        atualizacao                  integer,
                        credito_suplementar          numeric,
                        credito_especial             numeric,
                        credito_extraordinario       numeric,
                        reducoes                     numeric,
                        suplementacao                numeric,
                        reducao                      numeric,
                        empenho_per                  numeric,
                        anulado_per                  numeric,
                        liquidado_per                numeric,
                        pago_per                     numeric,
                        total_creditos               numeric,
                        valor_liquidado              numeric,
                        recomposicao                 numeric,
                        previsao                     numeric
                        )
               GROUP BY cod_funcao, cod_subfuncao
               ORDER BY cod_funcao, cod_subfuncao
             '';

    EXECUTE stSql;

    INSERT INTO tmp_relatorio
        SELECT
             cod_funcao
            ,0 as cod_subfuncao
            ,SUM( vl_empenhado ) as vl_empenhado
            ,SUM( liquidado_per) as liquidado_per
            ,SUM( pago_per     ) as pago_per
        FROM
            tmp_relatorio
        GROUP BY cod_funcao
        ORDER BY cod_funcao
    ;

    FOR reRegistro IN
        SELECT   tmp_relatorio.*
                ,fun.descricao as nom_funcao
                ,sub.descricao as nom_subfuncao
        FROM    tmp_relatorio
               LEFT JOIN
                 orcamento.subfuncao as sub
               ON   (
                        tmp_relatorio.cod_subfuncao   = sub.cod_subfuncao
                    AND sub.exercicio   = stExercicio
                    )
                ,orcamento.funcao    as fun
        WHERE
                tmp_relatorio.cod_funcao      = fun.cod_funcao
        AND     fun.exercicio       = stExercicio
        ORDER BY cod_funcao, cod_subfuncao
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_relatorio;

    RETURN;
END;
' LANGUAGE plpgsql;


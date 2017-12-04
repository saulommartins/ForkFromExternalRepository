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
--                                                                       stEntidades         stExercicio       stDataInicial      stDataFinal       
CREATE OR REPLACE FUNCTION tcems.fn_busca_totais_demonstrativo_restos( character varying, character varying, character varying, character varying ) RETURNS SETOF RECORD AS $$
DECLARE

        stEntidades            ALIAS FOR $1;
        stExercicio            ALIAS FOR $2;
        stDataInicial          ALIAS FOR $3;
        stDataFinal            ALIAS FOR $4;

        stSql                  VARCHAR;
        stDtInicialExercicio   VARCHAR;
        stDtFinalExercicio     VARCHAR;
        stDtInicialAnterior    VARCHAR;
        stDtFinalAnterior      VARCHAR;
        reRecord               RECORD;
        

BEGIN

        stDtInicialExercicio := '01/01/'||stExercicio;
        stDtFinalExercicio := '31/12/'||stExercicio;
        stDtInicialAnterior := '01/01/'||(stExercicio::integer - 1)::varchar;
        stDtFinalAnterior := '31/12/'||(stExercicio::integer - 1)::varchar;

        stSql :=  '
        SELECT
                SUM(empenho.fn_empenho_empenhado(empenho.exercicio ,empenho.cod_empenho, empenho.cod_entidade, '|| quote_literal(stDtInicialExercicio) ||', '|| quote_literal(stDataFinal) ||')) as valor_empenhado_exercicio,
                SUM(empenho.fn_empenho_empenhado(empenho.exercicio ,empenho.cod_empenho, empenho.cod_entidade, '|| quote_literal(stDataInicial) ||', '|| quote_literal(stDataFinal) ||')) as valor_empenhado,
                SUM(empenho.fn_empenho_anulado( empenho.exercicio ,empenho.cod_empenho , empenho.cod_entidade, '|| quote_literal(stDtInicialExercicio) ||', '|| quote_literal(stDataFinal) ||' )) as valor_anulado_exercicio,
                SUM(empenho.fn_empenho_anulado( empenho.exercicio ,empenho.cod_empenho , empenho.cod_entidade, '|| quote_literal(stDataInicial) ||', '|| quote_literal(stDataFinal) ||' )) as valor_anulado,
                SUM(empenho.fn_empenho_pago( empenho.exercicio ,empenho.cod_empenho, empenho.cod_entidade, '|| quote_literal(stDataInicial) ||', '|| quote_literal(stDataFinal) ||')) as valor_pago,
                SUM(empenho.fn_empenho_estornado( empenho.exercicio,empenho.cod_empenho , empenho.cod_entidade ,'|| quote_literal(stDataInicial) ||', '|| quote_literal(stDataFinal) ||'  )) AS valor_estornado,
                SUM((empenho.fn_empenho_pago( empenho.exercicio ,empenho.cod_empenho, empenho.cod_entidade, '|| quote_literal(stDtInicialExercicio) ||', '|| quote_literal(stDataFinal) ||') - empenho.fn_empenho_estornado( empenho.exercicio,empenho.cod_empenho , empenho.cod_entidade ,'|| quote_literal(stDtInicialExercicio) ||', '|| quote_literal(stDataFinal) ||'  ))) as valor_pago_exercicio,
        --empenhado - anulado - (pago - estornado)
                SUM(empenho.fn_empenho_empenhado(empenho.exercicio ,empenho.cod_empenho, empenho.cod_entidade, '''', '|| quote_literal(stDtFinalAnterior) ||')) - SUM(empenho.fn_empenho_anulado( empenho.exercicio ,empenho.cod_empenho , empenho.cod_entidade, '''', '|| quote_literal(stDtFinalAnterior) ||' )) - (SUM(empenho.fn_empenho_pago( empenho.exercicio ,empenho.cod_empenho, empenho.cod_entidade, '''', '|| quote_literal(stDtFinalAnterior) ||') - empenho.fn_empenho_estornado( empenho.exercicio,empenho.cod_empenho , empenho.cod_entidade ,'''','|| quote_literal(stDtFinalAnterior) ||'  ))) AS saldo_balanco
        
        FROM
                empenho.empenho
        INNER JOIN
                empenho.pre_empenho
                ON empenho.exercicio = pre_empenho.exercicio
                AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
        WHERE
                empenho.cod_entidade IN ('||stEntidades||')
                AND empenho.exercicio::integer < '||stExercicio||'::integer
        ';

        FOR reRecord IN EXECUTE stSql
        LOOP
                RETURN NEXT reRecord;
        END LOOP;

        RETURN;

END;

$$ language 'plpgsql';

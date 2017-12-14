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
/**
    * Arquivo de mapeamento para a função que busca os dados de despesas pessoais.
    * Data de Criação   : 23/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes
    
    * @package URBEM
    * @subpackage 

    $Id: despesaCorrente.plsql 63311 2015-08-14 20:49:24Z franver $
*/
CREATE OR REPLACE FUNCTION tcemg.fn_despesa_corrente(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDataInicial       ALIAS FOR $3;
    stDataFinal         ALIAS FOR $4;
    inPeriodo           INTEGER;
    stSql               VARCHAR := '';
    stSqlTmp            VARCHAR := '';
    arDatas             VARCHAR[];
    reRegistro          RECORD;


BEGIN

  inPeriodo := EXTRACT( month FROM TO_DATE(stDataInicial,'dd/mm/yyyy') );

    stSql := '
          CREATE TEMPORARY TABLE tmp_elem_despesa AS (
          SELECT *
            FROM orcamento.fn_consolidado_elem_despesa( '||quote_literal(stExercicio)||'
                                                      , ''''
                                                      , '||quote_literal(stDataInicial)||'
                                                      , '||quote_literal(stDataFinal)||'
                                                      , '||quote_literal(stCodEntidade)||'
                                                      , ''''
                                                      , ''''
                                                      , ''''
                                                      , ''''
                                                      , ''''
                                                      , ''''
                                                      , 0
                                                      , 0 )
              AS retorno ( classificacao   varchar
                         , cod_reduzido    varchar
                         , descricao       varchar
                         , num_orgao       integer
                         , nom_orgao       varchar
                         , num_unidade     integer
                         , nom_unidade     varchar
                         , saldo_inicial   numeric
                         , suplementacoes  numeric
                         , reducoes        numeric
                         , empenhado_mes   numeric
                         , empenhado_ano   numeric
                         , anulado_mes     numeric
                         , anulado_ano     numeric
                         , pago_mes        numeric
                         , pago_ano        numeric
                         , liquidado_mes   numeric
                         , liquidado_ano   numeric
                         , tipo_conta      varchar
                         , nivel           integer
                         )
        ORDER BY classificacao
          )
    ';

    EXECUTE stSql;

    IF inPeriodo = 1 THEN
    stSqlTmp := '
              SELECT '||inPeriodo||' AS periodo
                   , ''01'' AS cod_tipo
                   , COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%'' AND nivel = 5),0.00) AS despPesEncSoc
                   , COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'') AND nivel = 5),0.00) AS despJurEncDivInt
                   , COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00) AS despJurEncDivExt
                   , COALESCE((SELECT SUM(COALESCE(saldo_inicial,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%'' AND nivel = 5),0.00) AS despOutDespCor

               UNION
    ';
    END IF;
    stSql := stSqlTmp || '
              SELECT '||inPeriodo||' AS periodo
                   , ''02'' AS cod_tipo
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%'' AND nivel = 5),0.00) AS despPesEncSoc
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'') AND nivel = 5),0.00) AS despJurEncDivInt
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00) AS despJurEncDivExt
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%'' AND nivel = 5),0.00) AS despOutDespCor
               UNION
              SELECT '||inPeriodo||' AS periodo
                   , ''03'' AS cod_tipo
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%'' AND nivel = 5),0.00) AS despPesEncSoc
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'') AND nivel = 5),0.00) AS despJurEncDivInt
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00) AS despJurEncDivExt
                   , COALESCE((SELECT (SUM(COALESCE(saldo_inicial,0.00)) + SUM(COALESCE(suplementacoes,0.00)) - SUM(COALESCE(reducoes,0.00))) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%'' AND nivel = 5),0.00) AS despOutDespCor
               UNION
              SELECT '||inPeriodo||' AS periodo
                   , ''04'' AS cod_tipo
                   , COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%'' AND nivel = 5),0.00) AS despPesEncSoc
                   , COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'') AND nivel = 5),0.00) AS despJurEncDivInt
                   , COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00) AS despJurEncDivExt
                   , COALESCE((SELECT SUM(COALESCE(empenhado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%'' AND nivel = 5),0.00) AS despOutDespCor
               UNION
              SELECT '||inPeriodo||' AS periodo
                   , ''05'' AS cod_tipo
                   , COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%'' AND nivel = 5),0.00) AS despPesEncSoc
                   , COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'') AND nivel = 5),0.00) AS despJurEncDivInt
                   , COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00) AS despJurEncDivExt
                   , COALESCE((SELECT SUM(COALESCE(liquidado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%'' AND nivel = 5),0.00) AS despOutDespCor
               UNION
              SELECT '||inPeriodo||' AS periodo
                   , ''06'' AS cod_tipo
                   , COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.1%'' AND nivel = 5),0.00) AS despPesEncSoc
                   , COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2%'' AND (classificacao NOT ILIKE ''3.2.9.0.21.03.00.00.00'' AND classificacao NOT ILIKE ''3.2.9.0.92.04.00.00.00'') AND nivel = 5),0.00) AS despJurEncDivInt
                   , COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.2.9.0.21.03.00.00.00'' OR classificacao ILIKE ''3.2.9.0.92.04.00.00.00''),0.00) AS despJurEncDivExt
                   , COALESCE((SELECT SUM(COALESCE(anulado_mes,0.00)) FROM tmp_elem_despesa WHERE classificacao ILIKE ''3.3%'' AND nivel = 5),0.00) AS despOutDespCor
    ';
          
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_elem_despesa;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';


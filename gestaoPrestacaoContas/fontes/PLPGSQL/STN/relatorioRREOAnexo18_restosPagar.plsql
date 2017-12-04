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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 18.
    * Data de Criação: 20/05/2008


    * @author Henrique Boaventura

    * Casos de uso: uc-06.01.17

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_restos_pagar(varchar, integer ,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    inBimestre          ALIAS FOR $2;
    stEntidades         ALIAS FOR $3;
    dtInicioAno         VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;
    dtInicial           VARCHAR := '';
    dtFinal             VARCHAR := '';
    arDatas             VARCHAR[] ;

BEGIN
    dtInicioAno := '01/01/' || stExercicio;
    arDatas := publico.bimestre ( stExercicio, inBimestre );
    dtInicial := arDatas [0];
    dtFinal   := arDatas [1];

    -------------------------------------------------------
    -- Cria uma tabela temporaria para retornar os valores
    -------------------------------------------------------
    CREATE TEMPORARY TABLE tmp_retorno (
        grupo INTEGER,
        ordem INTEGER,
        descricao VARCHAR,
        vl_inscrito DECIMAL(14,2),
        vl_cancelado_bimestre DECIMAL(14,2),
        vl_pago_bimestre DECIMAL(14,2),
        vl_saldo_pagar DECIMAL(14,2)
    );

    ------------------------------
    -- Retorna o valor dos restos
    ------------------------------
    SELECT SUM(COALESCE(total_processados_exercicios_anteriores,0.00)) AS col1
         , SUM(COALESCE(total_processados_exercicio_anterior,0.00)) AS col2
         , SUM(COALESCE(total_processados_cancelado,0.00)) AS col3
         , SUM(COALESCE(total_processados_pago,0.00)) AS col4
         , SUM(COALESCE(total_nao_processados_exercicios_anteriores,0.00)) AS col5
         , SUM(COALESCE(total_nao_processados_exercicio_anterior,0.00)) AS col6
         , SUM(COALESCE(total_nao_processados_cancelado,0.00)) AS col7
         , SUM(COALESCE(total_nao_processados_pago,0.00)) AS col8
     INTO reRegistro
     FROM stn.fn_rreo_anexo9(stExercicio,stEntidades,dtFinal) AS tbl
           (  tipo text
            , ordem integer
            , total_processados_exercicios_anteriores numeric
            , total_processados_exercicio_anterior numeric
            , total_processados_cancelado numeric
            , total_processados_pago numeric
            , total_nao_processados_exercicios_anteriores numeric
            , total_nao_processados_exercicio_anterior numeric
            , total_nao_processados_cancelado numeric
            , total_nao_processados_pago numeric
           )
     WHERE tipo = 'EXECUTIVO'
    ;

    ------------------------------------------
    -- Insere os valores do executivo na base 
    ------------------------------------------
    INSERT INTO tmp_retorno VALUES ( 1,0,'RESTOS A PAGAR PROCESSADOS',NUll,NULL,NULL,NULL);
    INSERT INTO tmp_retorno VALUES ( 2,0,'RESTOS A PAGAR NÃO-PROCESSADOS',NUll,NULL,NULL,NULL);
    INSERT INTO tmp_retorno VALUES ( 1,1,'Poder Executivo',(reRegistro.col1+reRegistro.col2),reRegistro.col3,reRegistro.col4,(reRegistro.col1+reRegistro.col2-reRegistro.col3-reRegistro.col4));
    INSERT INTO tmp_retorno VALUES ( 2,1,'Poder Executivo',(reRegistro.col6),reRegistro.col7,reRegistro.col8,(reRegistro.col5+reRegistro.col6-reRegistro.col7-reRegistro.col8));

    ------------------------------
    -- Retorna o valor dos restos
    ------------------------------
    SELECT SUM(COALESCE(total_processados_exercicios_anteriores,0.00)) AS col1
         , SUM(COALESCE(total_processados_exercicio_anterior,0.00)) AS col2
         , SUM(COALESCE(total_processados_cancelado,0.00)) AS col3
         , SUM(COALESCE(total_processados_pago,0.00)) AS col4
         , SUM(COALESCE(total_nao_processados_exercicios_anteriores,0.00)) AS col5
         , SUM(COALESCE(total_nao_processados_exercicio_anterior,0.00)) AS col6
         , SUM(COALESCE(total_nao_processados_cancelado,0.00)) AS col7
         , SUM(COALESCE(total_nao_processados_pago,0.00)) AS col8
     INTO reRegistro
      FROM stn.fn_rreo_anexo9(stExercicio,stEntidades,dtFinal) AS tbl
           (  tipo text
            , ordem integer
            , total_processados_exercicios_anteriores numeric
            , total_processados_exercicio_anterior numeric
            , total_processados_cancelado numeric
            , total_processados_pago numeric
            , total_nao_processados_exercicios_anteriores numeric
            , total_nao_processados_exercicio_anterior numeric
            , total_nao_processados_cancelado numeric
            , total_nao_processados_pago numeric
           )
     WHERE tipo = 'LEGISLATIVO'
    ;

    ------------------------------------------
    -- Insere os valores do executivo na base 
    ------------------------------------------
    INSERT INTO tmp_retorno VALUES ( 1,2,'Poder Legislativo',(reRegistro.col1+reRegistro.col2),reRegistro.col3,reRegistro.col4,(reRegistro.col1+reRegistro.col2-reRegistro.col3-reRegistro.col4));
    INSERT INTO tmp_retorno VALUES ( 2,2,'Poder Legislativo',(reRegistro.col5+reRegistro.col6),reRegistro.col7,reRegistro.col8,(reRegistro.col5+reRegistro.col6-reRegistro.col7-reRegistro.col8));

    ----------------------------------------------------
    -- Retorna os valores da tabela temporaria
    ----------------------------------------------------
    stSql := '
        SELECT * 
          FROM tmp_retorno
      ORDER BY grupo
             , ordem
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno ;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  

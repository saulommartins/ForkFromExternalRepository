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

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_receita_corrente_liquida(varchar, integer ,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    inBimestre      ALIAS FOR $2;
    stEntidades     ALIAS FOR $3;
    dtInicioAno     VARCHAR := '';
    stSql           VARCHAR := '';
    stSQLaux        VARCHAR := '';
    reRegistro      RECORD;
    flValorRCL      NUMERIC := 0;
    dtInicial       VARCHAR := '';
    dtFinal         VARCHAR := '';
    arDatas         VARCHAR[] ;

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
        vl_no_periodo DECIMAL(14,2),
        vl_ate_periodo DECIMAL(14,2)
    );

    -----------------------------------------------------
    -- Retorna o valor da receita corrente liquida - rcl
    -----------------------------------------------------
    INSERT INTO tmp_retorno SELECT 1
                                 , 0
                                 , 'Receita Corrente Líquida'
                                 , NULL
                                 , SUM(  total_mes_1 
                                       + total_mes_2
                                       + total_mes_3
                                       + total_mes_4
                                       + total_mes_5
                                       + total_mes_6
                                       + total_mes_7
                                       + total_mes_8
                                       + total_mes_9
                                       + total_mes_10
                                       + total_mes_11
                                       + total_mes_12
                                      ) AS vl_rcl
                              FROM stn.pl_total_subcontas ( dtFinal ) AS tbl
                                   (  ordem          INTEGER
                                    , cod_conta      VARCHAR
                                    , nom_conta      VARCHAR
                                    , cod_estrutural VARCHAR
                                    , mes_1          NUMERIC
                                    , mes_2          NUMERIC
                                    , mes_3          NUMERIC
                                    , mes_4          NUMERIC
                                    , mes_5          NUMERIC
                                    , mes_6          NUMERIC
                                    , mes_7          NUMERIC
                                    , mes_8          NUMERIC
                                    , mes_9          NUMERIC
                                    , mes_10         NUMERIC
                                    , mes_11         NUMERIC
                                    , mes_12         NUMERIC
                                    , total_mes_1    NUMERIC
                                    , total_mes_2    NUMERIC
                                    , total_mes_3    NUMERIC
                                    , total_mes_4    NUMERIC
                                    , total_mes_5    NUMERIC
                                    , total_mes_6    NUMERIC
                                    , total_mes_7    NUMERIC
                                    , total_mes_8    NUMERIC
                                    , total_mes_9    NUMERIC
                                    , total_mes_10   NUMERIC
                                    , total_mes_11   NUMERIC
                                    , total_mes_12   NUMERIC
                                   );
    ----------------------------------------------------
    -- Retorna o valor do vinculo da RCL 
    ----------------------------------------------------
    stSql :=' 
    SELECT SUM(valor) AS valor
      FROM stn.receita_corrente_liquida
     WHERE exercicio = ''' || stExercicio || '''
       AND cod_entidade IN ( ' || stEntidades  || ' )
       AND (    TO_DATE(''01/'' || mes || ''/'' || ano, ''dd/mm/yyyy'') < TO_DATE(''' || dtFinal || ''',''dd/mm/yyyy'')
            AND TO_DATE(''01/'' || mes || ''/'' || ano, ''dd/mm/yyyy'') >= TO_DATE(''' || dtFinal || ''',''dd/mm/yyyy'') - INTERVAL ''13 MONTHS'' )';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF(reRegistro.valor IS NOT NULL) THEN
            flValorRCL := reRegistro.valor;
        END IF;
    END LOOP;

    ----------------------------------------------------
    -- Retorna os valores da tabela temporaria
    ----------------------------------------------------
    stSql := ' 
        SELECT grupo
             , ordem
             , descricao
             , vl_no_periodo
             , vl_ate_periodo -- + COALESCE(' || flValorRCL || ',0)
          FROM tmp_retorno  
      ORDER BY grupo
             , ordem';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno ;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  

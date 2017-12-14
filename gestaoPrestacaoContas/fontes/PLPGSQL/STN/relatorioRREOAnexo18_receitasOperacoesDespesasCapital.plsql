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

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_receitas_operacoes_despesas_capital(varchar, integer ,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    inBimestre          ALIAS FOR $2;
    stEntidades         ALIAS FOR $3;
    stPeridiocidade     ALIAS FOR $4;
    
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
        vl_apurado DECIMAL(14,2),
        vl_saldo DECIMAL(14,2)
    );

    ---------------------------------
    -- Retorna os valores da receita
    ---------------------------------
    SELECT atu
         , ate_bi
         , saldo 
      INTO reRegistro
      FROM stn.fn_rreo_anexo18_receitas(stExercicio, stEntidades, stPeridiocidade, inBimestre) AS tbl
           (
              grupo INTEGER
            , nivel INTEGER
            , descricao VARCHAR
            , atu NUMERIC
            , ate_bi NUMERIC 
            , saldo NUMERIC 
           ); 
    
    -------------------------------------------
    -- Insere o resultada na tabela de retorno
    -------------------------------------------
    INSERT INTO tmp_retorno VALUES (1,1,'Receita de Operação de Crédito',reRegistro.ate_bi,reRegistro.saldo);

    ---------------------------------
    -- Retorna os valores da despesa
    ---------------------------------
    SELECT SUM(dot_atu) AS dot_atu
         , SUM(liq_tot) AS liq_tot
      INTO reRegistro
      FROM stn.fn_rreo_anexo18_despesas(stExercicio, stEntidades, stPeridiocidade, inBimestre) AS tbl
         (
            grupo INTEGER 
          , nivel INTEGER
          , cod_estrutural VARCHAR 
          , descricao VARCHAR
          , dot_atu NUMERIC 
          , liq_tot NUMERIC 
          , resto NUMERIC
         );

    -------------------------------------------
    -- Insere o resultada na tabela de retorno
    -------------------------------------------
    INSERT INTO tmp_retorno VALUES (2,1,'Despesa de Capital Líquida',reRegistro.liq_tot,(reRegistro.dot_atu - reRegistro.liq_tot));

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

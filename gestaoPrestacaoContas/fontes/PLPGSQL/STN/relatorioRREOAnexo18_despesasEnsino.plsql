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

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_despesas_ensino(varchar, integer ,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    inBimestre          ALIAS FOR $2;
    stEntidades         ALIAS FOR $3;
    dtInicioAno         VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;
    dtInicial           VARCHAR := '';
    dtFinal             VARCHAR := '';

    vlReceitaFundeb     NUMERIC(14,2) := 0;
    vlResultadoFundeb   NUMERIC(14,2) := 0;
    vlDespesaFundeb     NUMERIC(14,2) := 0;
    vlTotalDeducoes     NUMERIC(14,2) := 0;
    vlDespesaEducacao   NUMERIC(14,2) := 0;
    vlReceitaBruta      NUMERIC(14,2) := 0;

    stPorcentagem       VARCHAR;
    porcBimestre        NUMERIC(14,2) := 0;
    arDatas             VARCHAR[] ;

BEGIN
    dtInicioAno := '01/01/' || stExercicio;
    arDatas := publico.bimestre ( stExercicio, inBimestre );
    dtInicial := arDatas [0];
    dtFinal   := arDatas [1];

--    vlDespesaEducacao :=  
--    vlResultadoFundeb :=
--    vlTotalDeducoes   :=
--    vlReceitaBruta    := 



    -------------------------------------------------------
    -- Cria uma tabela temporaria para retornar os valores
    -------------------------------------------------------
    CREATE TEMPORARY TABLE tmp_retorno (
        grupo INTEGER,
        ordem INTEGER,
        descricao VARCHAR,
        vl_apurado NUMERIC(14,2),
        minimo_aplicar VARCHAR,
        porc_bimestre NUMERIC(14,2)
    );

    --------------------------
    -- Recupera a porcentagem
    --------------------------
    SELECT valor
      INTO stPorcentagem
      FROM administracao.configuracao
     WHERE exercicio = stExercicio
       AND cod_modulo = 36
       AND parametro = 'stn_anexo10_porcentagem';

    -------------------------------------------------------------------------------------
    -- Recupera os valores para o calculo do  minimo anual do fundeb com ensino infantil
    -------------------------------------------------------------------------------------
    stSql := '
        SELECT *
          FROM stn.fn_rreo_anexo10_receitas('''||stExercicio||''', '''||stEntidades||''', ''receitas_fundeb'','||inBimestre||') AS   
               (
                  grupo INTEGER 
                , subgrupo INTEGER
                , item INTEGER
                , descricao VARCHAR
                , ini NUMERIC
                , atu NUMERIC
                , no_bi NUMERIC
                , ate_bi NUMERIC
                , pct NUMERIC 
               )
         WHERE subgrupo IN (0,1,2,3)
           AND item = 0
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF( reRegistro.grupo = 9 AND reRegistro.subgrupo = 0 ) THEN
            vlResultadoFundeb := vlResultadoFundeb - reRegistro.ate_bi;
        END IF;  

        IF (stExercicio::integer > 2012) THEN
            IF( reRegistro.grupo = 9 AND reRegistro.subgrupo = 0 ) THEN
                vlReceitaFundeb := reRegistro.ate_bi;
            END IF;  
        ELSE
            IF( reRegistro.grupo = 10 AND reRegistro.subgrupo = 0 ) THEN
                vlReceitaFundeb := reRegistro.ate_bi;
            END IF;  
        END IF;  

        IF( reRegistro.grupo = 10 AND reRegistro.subgrupo = 1 ) THEN
            vlResultadoFundeb := vlResultadoFundeb + reRegistro.ate_bi;
        END IF;  

        IF( reRegistro.grupo = 10 AND reRegistro.subgrupo = 2 ) THEN
            vlResultadoFundeb := vlResultadoFundeb + reRegistro.ate_bi;
        END IF;  
        
        IF( reRegistro.grupo = 10 AND reRegistro.subgrupo = 3 ) THEN
            vlResultadoFundeb := vlResultadoFundeb + reRegistro.ate_bi;
        END IF;  
    END LOOP;

    SELECT SUM(ate_bi)
      INTO vlDespesaFundeb
      FROM stn.fn_rreo_anexo10_despesas_novo(stExercicio,stEntidades, 1, dtInicial, dtFinal) AS tbl
           (
              grupo INTEGER
            , nivel INTEGER
            , descricao VARCHAR
            , ini NUMERIC(14,2)
            , atu NUMERIC(14,2)
            , no_bi NUMERIC(14,2)
            , ate_bi NUMERIC(14,2)
            , pct NUMERIC(14,2)
           )
     WHERE nivel = 0
       AND grupo = 13;

    ----------------------------------------
    -- Insere os valor na tabela temporaria
    ----------------------------------------
    INSERT INTO tmp_retorno VALUES (  3
                                    , 0
                                    , 'Mínimo Anual de 60% fo FUNDEB na Remuneração do Magistério com Educação Infantil e Ensino Fundamental'
                                    , vlDespesaFundeb
                                    , '60%'
                                    , 0
                                   );

    -- Esses valores vão ser definidos após o desenvolvimento dos relatórios que possuem esses valores
    ----------------------------------------
    -- Insere os valor na tabela temporaria
    ----------------------------------------
    INSERT INTO tmp_retorno VALUES (  2
                                    , 0
                                    , 'Mínimo Anual de 60% fo FUNDEB na Remuneração do Magistério com Ensino Fundamental e Médio'
                                    , 0.00
                                    , '60%'
                                    , 0
                                   );

    IF( vlReceitaFundeb > 0 )THEN
        UPDATE tmp_retorno SET porc_bimestre = ((vlDespesaFundeb/vlReceitaFundeb) * 100) WHERE grupo = 3;
    END IF;

    -------------------------------------------------------------------------------------
    -- Recupera os valores para o calculo do  minimo anual do fundeb com ensino infantil
    -------------------------------------------------------------------------------------
    SELECT COALESCE(cancelado,0) 
      INTO vlTotalDeducoes
      FROM stn.fn_rreo_anexo10_restos(stExercicio, inBimestre, stEntidades) AS tbl 
           (
              saldo NUMERIC
            , cancelado NUMERIC 
           );

    SELECT SUM(ate_bi)
      INTO vlDespesaEducacao
      FROM stn.fn_rreo_anexo10_despesas_novo(stExercicio, stEntidades, 2, dtInicial, dtFinal) AS tbl
           (
              grupo INTEGER
            , nivel INTEGER
            , descricao VARCHAR
            , ini NUMERIC(14,2)
            , atu NUMERIC(14,2)
            , no_bi NUMERIC(14,2)
            , ate_bi NUMERIC(14,2)
            , pct NUMERIC(14,2)
           )
     WHERE grupo IN (24)
       AND nivel = 0;

    SELECT SUM(ate_bi)
      INTO vlReceitaBruta
      FROM stn.fn_rreo_anexo10_receitas(stExercicio, stEntidades, 'receitas_ensino', inBimestre) AS tbl
           (
              grupo INTEGER
            , subgrupo INTEGER
            , item INTEGER
            , descricao VARCHAR
            , ini NUMERIC
            , atu NUMERIC
            , no_bi NUMERIC
            , ate_bi NUMERIC
            , pct NUMERIC 
           )
     WHERE grupo IN (1,2)
       AND subgrupo = 0;

    
    IF(vlReceitaBruta <> 0.00) THEN
        porcBimestre := ((vlDespesaEducacao - (vlResultadoFundeb + vlTotalDeducoes ))/vlReceitaBruta)*100;
    ELSE
        porcBimestre := 0.00;
    END IF;

    ----------------------------------------
    -- Insere os valor na tabela temporaria
    ----------------------------------------
    INSERT INTO tmp_retorno VALUES (  1
                                    , 0
                                    , 'Mínimo Anual de '||stPorcentagem||'% das Receitas de Impostos na Manutenção e Desenvolvimento do Ensino'
                                    , vlDespesaEducacao - (vlResultadoFundeb + vlTotalDeducoes )
                                    , stPorcentagem||'%'
                                    , porcBimestre
                                   );
    
    --Vão ser definidos após o desenvolvimento de relatório que possuem esses valores
    INSERT INTO tmp_retorno VALUES (  4
                                    , 0
                                    , 'Complementação da União ao FUNDEB'
                                    , 0.00
                                    , '0'
                                    , 0
                                   );

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

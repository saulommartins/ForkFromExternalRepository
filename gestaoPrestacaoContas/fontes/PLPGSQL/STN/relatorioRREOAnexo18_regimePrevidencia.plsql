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


CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_regime_previdencia(varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    stEntidades     ALIAS FOR $2;
    dtInicial       ALIAS FOR $3;
    dtFinal         ALIAS FOR $4;
    stSql           VARCHAR := '';
    reRegistro      RECORD;
    arDatas         VARCHAR[] ;
    boExists        BOOLEAN := FALSE;

BEGIN

    DROP TABLE IF EXISTS tmp_valor;
    DROP TABLE  IF EXISTS tmp_receitas;
    DROP TABLE  IF EXISTS tmp_despesa_lib;

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

    ---------------------------------------------------
    -- Insere os valores do anexo IV que nao foi feito
    ---------------------------------------------------
    INSERT INTO tmp_retorno VALUES (1,0,'Regime Geral de Previdência Social',NULL,NULL);
    INSERT INTO tmp_retorno VALUES (1,1,'Receitas Previdenciárias Realizadas(I)',0.00,0.00);
    INSERT INTO tmp_retorno VALUES (1,2,'Despesas Previdenciárias Liquidadas(II)',0.00,0.00);
    INSERT INTO tmp_retorno VALUES (1,3,'Resultado Previdenciário (III) = (I-II)',0.00,0.00);

    INSERT INTO tmp_retorno VALUES (2,0,'Regime Próprio de Previdência Social dos Servidores Públicos',NULL,NULL);

    -----------------------------------
    -- Recupera os valores da receitas
    -----------------------------------
    SELECT SUM(no_bimestre) AS no_bimestre
         , SUM(ate_bimestre) AS ate_bimestre
      INTO reRegistro    
      FROM stn.fn_rreo_anexo5_receitas_novo(stExercicio, stEntidades, '', dtInicial, dtFinal ) AS tbl
           (  grupo                  INTEGER          
            , cod_estrutural         VARCHAR      
            , nivel                  INTEGER               
            , nom_conta              VARCHAR           
            , previsao_inicial       NUMERIC 
            , previsao_atualizada    NUMERIC 
            , no_bimestre            NUMERIC 
            , ate_bimestre           NUMERIC 
            , ate_bimestre_anterior  NUMERIC 
           )
     WHERE nivel = 1;   
    
    -----------------------------------------------------
    -- Insere os valores da receita na tabela temporaria
    -----------------------------------------------------
    INSERT INTO tmp_retorno VALUES (2,1,'Receitas Previdenciárias Realizadas(IV)',reRegistro.no_bimestre,reRegistro.ate_bimestre); 

    ----------------------------------
    -- Recupera os valores da despesa
    ----------------------------------
    SELECT SUM(vl_empenhado_bimestre) AS vl_empenhado_bimestre
         , SUM(vl_empenhado_ate_bimestre) AS vl_empenhado_ate_bimestre
      INTO reRegistro
      FROM stn.fn_rreo_anexo5_despesas( stExercicio,stEntidades, 'nao', dtInicial, dtFinal ) AS tbl
           (   grupo                              INTEGER
             , nivel                              INTEGER
             , cod_estrutural                     VARCHAR
             , nom_funcao                         VARCHAR 
             , vl_original                        NUMERIC 
             , vl_suplementacoes                  NUMERIC 
             , vl_empenhado_bimestre              NUMERIC 
             , vl_empenhado_ate_bimestre          NUMERIC 
             , vl_empenhado_ate_bimestre_anterior NUMERIC
           )
     WHERE nivel = 1;

    -----------------------------------------------------
    -- Insere os valores da receita na tabela temporaria
    -----------------------------------------------------
    INSERT INTO tmp_retorno VALUES (2,2,'Despesas Previdenciárias Liquidadas(V)',reRegistro.vl_empenhado_bimestre,reRegistro.vl_empenhado_ate_bimestre); 
    INSERT INTO tmp_retorno VALUES ( 2
                                   , 3
                                   , 'Resultado Previdenciário (VI) = (IV - V)'
                                   , ( SELECT vl_no_periodo FROM tmp_retorno WHERE grupo = 2 AND ordem = 1 ) - ( SELECT vl_no_periodo FROM tmp_retorno WHERE grupo = 2 AND ordem = 2 )
                                   , ( SELECT vl_ate_periodo FROM tmp_retorno WHERE grupo = 2 AND ordem = 1 ) - ( SELECT vl_ate_periodo FROM tmp_retorno WHERE grupo = 2 AND ordem = 2 )
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
    DROP TABLE tmp_valor;
    DROP TABLE tmp_receitas;
    DROP TABLE tmp_despesa;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  

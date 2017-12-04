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

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_receita_alienacao_ativo(varchar, integer ,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    inBimestre          ALIAS FOR $2;
    stEntidades         ALIAS FOR $3;
    dtInicioAno         VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;
    dtInicial           VARCHAR := '';
    dtFinal             VARCHAR := '';
    stCodRecursos       VARCHAR := '';
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
        grupo      INTEGER,
        ordem      INTEGER,
        descricao  VARCHAR,
        vl_apurado DECIMAL(14,2),
        vl_saldo   DECIMAL(14,2)
    );

    ----------------------------------------
    -- Recupera os recursos para a consulta
    ----------------------------------------
    SELECT ARRAY_TO_STRING(ARRAY( SELECT cod_recurso
                                    FROM stn.recurso_rreo_anexo_14
                                   WHERE exercicio = stExercicio
                                ),',' )
      INTO stCodRecursos;            

    ----------------------------------
    -- Recupera os valores da receita
    ----------------------------------  
    IF (stCodRecursos <> '') THEN
        SELECT previsao_atualizada
             , receitas_realizadas 
          INTO reRegistro
          FROM stn.fn_anexo14_receitas( stExercicio,inBimestre,stEntidades,stCodRecursos,'' ) AS tbl 
               (  grupo INTEGER 
                , cod_estrutural VARCHAR
                , nivel INTEGER
                , nom_conta VARCHAR
                , previsao_atualizada NUMERIC
                , receitas_realizadas NUMERIC
               )
        WHERE nivel = 1;
    ELSE
        SELECT 0 AS previsao_atualizada
             , 0 AS receitas_realizadas
          INTO reRegistro;
    END IF;

    ------------------------------------------
    -- Insere os valores na tabela de retorno 
    ------------------------------------------
    INSERT INTO tmp_retorno VALUES (  1
                                    , 0
                                    , 'Receita de Capital Resultante da Alienação de Ativos'
                                    , reRegistro.receitas_realizadas
                                    , (reRegistro.previsao_atualizada - reRegistro.receitas_realizadas) 
                                   );

    ----------------------------------
    -- Recupera os valores da despesa
    ----------------------------------
    IF(stCodRecursos <> '') THEN
        SELECT SUM(dotacao_atualizada) AS dotacao_atualizada
             , SUM(liquidado_ate_bimestre) AS liquidado_ate_bimestre
          INTO reRegistro
          FROM stn.fn_anexo14_despesas( stExercicio,inBimestre,stEntidades,stCodRecursos ) AS tbl 
               (   grupo                  INTEGER
                 , cod_estrutural         VARCHAR
                 , descricao              VARCHAR
                 , nivel                  INTEGER
                 , dotacao_atualizada     NUMERIC
                 , despesas_empenhadas    NUMERIC
                 , liquidado_ate_bimestre NUMERIC
               )
         WHERE nivel = 1;
    ELSE
        SELECT 0 AS dotacao_atualizada
             , 0 AS liquidado_ate_bimestre
          INTO reRegistro;
    END IF;

    ------------------------------------------
    -- Insere os valores na tabela de retorno 
    ------------------------------------------
    INSERT INTO tmp_retorno VALUES (  2
                                    , 0
                                    , 'Aplicação dos Recursos de Alienação de Ativos'
                                    , reRegistro.liquidado_ate_bimestre
                                    , (reRegistro.dotacao_atualizada - reRegistro.liquidado_ate_bimestre) 
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

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

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_balanco_orcamentario(varchar, integer ,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio        ALIAS FOR $1;
    inBimestre         ALIAS FOR $2;
    stEntidades        ALIAS FOR $3;
    dtInicioAno        VARCHAR := '';
    stSql              VARCHAR := '';
    stSQLaux           VARCHAR := '';
    reRegistro         RECORD;
    reRegistroReceita  RECORD;
    reRegistroDespesa  RECORD;
    dtInicial          VARCHAR := '';
    dtFinal            VARCHAR := '';
    arDatas            VARCHAR[] ;

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

    ----------------------------------------------------------
    -- Retorna os valores da receita para a tabela temporaria
    ----------------------------------------------------------
    SELECT SUM(previsao_inicial) AS previsao_inicial
         , SUM(previsao_atualizada) AS previsao_atualizada
         , SUM(no_bimestre) AS no_bimestre
         , SUM(ate_bimestre) AS ate_bimestre
      INTO reRegistroReceita
      FROM stn.fn_rreo_anexo1_receitas_novo( stExercicio, dtInicial, dtFinal , stEntidades ) as tbl
           (
               grupo               INTEGER     
             , cod_estrutural      VARCHAR
             , nivel               INTEGER
             , nom_conta           VARCHAR
             , previsao_inicial    NUMERIC
             , previsao_atualizada NUMERIC
             , no_bimestre         NUMERIC
             , p_no_bimestre       NUMERIC
             , ate_bimestre        NUMERIC
             , p_ate_bimestre      NUMERIC
             , a_realizar          NUMERIC
          )
      WHERE nivel IN (1,2)
        AND (trim(nom_conta) ILIKE 'RECEITAS CORRENTES' OR trim(nom_conta) ILIKE 'RECEITAS DE CAPITAL' OR trim(nom_conta) ILIKE 'RECEITAS (INTRA-ORÇAMENTÁRIAS) (II)');

    ----------------------------------------------------
    -- Insere o resultado separado na tabela temporaria
    ----------------------------------------------------
    INSERT INTO tmp_retorno VALUES (1,0,'RECEITAS',NULL,NULL);    

    INSERT INTO tmp_retorno VALUES (1,1,'Previsão Inicial da Receita',reRegistroReceita.previsao_inicial,reRegistroReceita.previsao_inicial); 
    
    INSERT INTO tmp_retorno VALUES (1,2,'Previsão Atualizada da Receita',reRegistroReceita.previsao_atualizada,reRegistroReceita.previsao_atualizada); 
    
    INSERT INTO tmp_retorno VALUES (1,3,'Receitas Realizadas',reRegistroReceita.no_bimestre,reRegistroReceita.ate_bimestre); 
    
    INSERT INTO tmp_retorno VALUES (1,4,'Déficit Orçamentário',0.00,0.00); 
    
    INSERT INTO tmp_retorno VALUES (1,5,'Saldos de Exercícios Anteriores',0.00,0.00);

    ----------------------------------------------------------
    -- Retorna os valores da despesa para a tabela temporaria
    ----------------------------------------------------------

    SELECT SUM(dotacao_inicial) AS dotacao_inicial
         , SUM(creditos_adicionais) AS creditos_adicionais
         , SUM(dotacao_atualizada) AS dotacao_atualizada
         , SUM(vl_empenhado_bimestre) AS vl_empenhado_bimestre
         , SUM(vl_empenhado_total) AS vl_empenhado_total
         , SUM(vl_liquidado_bimestre) AS vl_liquidado_bimestre
         , SUM(vl_liquidado_total) AS vl_liquidado_total
      INTO reRegistroDespesa
      FROM stn.fn_rreo_anexo1_despesas_novo( stExercicio, dtInicial, dtFinal, stEntidades ) AS tbl
           (                                                                                          
             grupo                   INTEGER
           , cod_estrutural          VARCHAR
           , descricao               VARCHAR
           , nivel                   INTEGER
           , dotacao_inicial         NUMERIC  
           , creditos_adicionais     NUMERIC  
           , dotacao_atualizada      NUMERIC  
           , vl_empenhado_bimestre   NUMERIC  
           , vl_empenhado_total      NUMERIC  
           , vl_liquidado_bimestre   NUMERIC  
           , vl_liquidado_total      NUMERIC
           , vl_pago_total           NUMERIC
           , percentual              NUMERIC  
           , saldo_liquidar          NUMERIC
           )
     WHERE nivel = 1;

    ----------------------------------------------------
    -- Insere o resultado separado na tabela temporaria
    ----------------------------------------------------
    INSERT INTO tmp_retorno VALUES (2,0,'DESPESAS',NULL,NULL);

    INSERT INTO tmp_retorno VALUES (2,1,'Dotação Inicial',reRegistroDespesa.dotacao_inicial,reRegistroDespesa.dotacao_inicial);
    
    INSERT INTO tmp_retorno VALUES (2,2,'Créditos Adicionais',reRegistroDespesa.creditos_adicionais,reRegistroDespesa.creditos_adicionais);
    
    INSERT INTO tmp_retorno VALUES (2,3,'Dotacao Atualizada',reRegistroDespesa.dotacao_atualizada,reRegistroDespesa.dotacao_atualizada);
    
    INSERT INTO tmp_retorno VALUES (2,4,'Despesas Empenhadas',reRegistroDespesa.vl_empenhado_bimestre,reRegistroDespesa.vl_empenhado_total);
        
    INSERT INTO tmp_retorno VALUES (2,5,'Despesas Liquidadas',reRegistroDespesa.vl_liquidado_bimestre,reRegistroDespesa.vl_liquidado_total);
    
    INSERT INTO tmp_retorno VALUES (2,6,'Superávit Orçamentário',0.00,0.00);

    -------------------------------
    -- Calcula o deficit/superavit
    -------------------------------
    IF( reRegistroReceita.no_bimestre > reRegistroDespesa.vl_liquidado_bimestre ) THEN
        UPDATE tmp_retorno SET vl_no_periodo = (reRegistroReceita.no_bimestre - reRegistroDespesa.vl_liquidado_bimestre) WHERE grupo = 2 AND ordem = 6;
    ELSE
        UPDATE tmp_retorno SET vl_no_periodo = (reRegistroDespesa.vl_liquidado_bimestre - reRegistroReceita.no_bimestre) WHERE grupo = 1 AND ordem = 4;
    END IF;

    IF( reRegistroReceita.ate_bimestre > reRegistroDespesa.vl_liquidado_total ) THEN
        UPDATE tmp_retorno SET vl_ate_periodo = (reRegistroReceita.ate_bimestre - reRegistroDespesa.vl_liquidado_total) WHERE grupo = 2 AND ordem = 6;
    ELSE
        UPDATE tmp_retorno SET vl_ate_periodo = (reRegistroDespesa.vl_liquidado_total - reRegistroReceita.ate_bimestre) WHERE grupo = 1 AND ordem = 4;
    END IF;


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

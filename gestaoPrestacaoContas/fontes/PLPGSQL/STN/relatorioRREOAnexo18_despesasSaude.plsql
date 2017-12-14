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

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo18_despesas_saude(varchar, integer ,varchar) RETURNS SETOF RECORD AS $$
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
    boExists            BOOLEAN := FALSE;

    vlReceita           NUMERIC(14,2);
    vlDespesa           NUMERIC(14,2);
    vlRestos            NUMERIC(14,2);
    vlPorc              NUMERIC(14,2);

BEGIN
    dtInicioAno := '01/01/' || stExercicio;
    arDatas := publico.bimestre ( stExercicio, inBimestre );
    dtInicial := arDatas [0];
    dtFinal   := arDatas [1];

    DROP TABLE IF EXISTS tmp_valor;

    -------------------------------------------------------
    -- Cria uma tabela temporaria para retornar os valores
    -------------------------------------------------------
    CREATE TEMPORARY TABLE tmp_retorno_despesas (
        grupo INTEGER,
        ordem INTEGER,
        descricao VARCHAR,
        vl_ate_bimestre DECIMAL(14,2),
        vl_aplicar VARCHAR,
        vl_porcentagem DECIMAL(14,2)
    );

    ----------------------------------
    -- Recupera os valores da despesa
    ----------------------------------
    IF stExercicio::INTEGER > 2012 THEN
        SELECT SUM(despesa_empenhada) AS despesa_empenhada
          INTO vlDespesa
          FROM stn.fn_rreo_anexo12_despesas( stExercicio,dtInicial, dtFinal,stEntidades ) AS tbl
               (    
                grupo integer,
                subgrupo integer,
                descricao varchar,
                dotacao_inicial numeric,
                dotacao_atualizada numeric,
                despesa_empenhada numeric,
                porcentagem_empenhada numeric,
                despesa_liquidada numeric,
                porcentagem numeric,
                restos_nao_processados numeric
               )
         WHERE subgrupo = 1;
    ELSE
        SELECT SUM(despesa_liquidada) AS despesa_liquidada
          INTO vlDespesa
          FROM stn.fn_rreo_anexo16_despesas_acoes_servicos( stExercicio,dtInicial, dtFinal,stEntidades ) AS tbl
               (    
                  grupo INTEGER
                , subgrupo INTEGER
                , descricao VARCHAR
                , dotacao_inicial NUMERIC
                , dotacao_atualizada NUMERIC
                , despesa_liquidada NUMERIC
                , porcentagem NUMERIC
               ) 
         WHERE subgrupo = 1;
    END IF;

    ----------------------------------
    -- Recupera os valores da receita
    ----------------------------------
    SELECT COALESCE(ate_periodo,0) AS ate_periodo
      INTO vlReceita
      FROM stn.fn_rreo_anexo16_receitas( stExercicio,inBimestre,stEntidades ) AS tbl
           (
            grupo integer,
            subgrupo integer,
            item integer,
            descricao varchar,
            previsao_inicial    numeric,
            previsao_atualizada numeric,
            ate_periodo         numeric,
            porc_periodo        numeric
           )
     WHERE grupo = 1
       AND subgrupo = 0;

    ---------------------------------
    -- Recupera os valores de restos
    ---------------------------------
    IF stExercicio::INTEGER > 2012 THEN
    SELECT SUM(despesa_empenhada) AS despesa_empenhada
      INTO vlRestos
      FROM stn.fn_rreo_anexo12_despesas_acoes_servicos(stExercicio,dtInicial, dtFinal,stEntidades) AS tbl
           (    
            grupo integer,
            subgrupo integer,
            descricao varchar,
            dotacao_inicial numeric,
            dotacao_atualizada numeric,
            despesa_empenhada numeric,
            porcentagem_empenhada numeric,
            despesa_liquidada numeric,
            porcentagem numeric,
            restos_nao_processados numeric
           ) 
     WHERE subgrupo = 1;
    ELSE
        SELECT vl_restos_pagar_cancelados
          INTO vlRestos
          FROM stn.fn_rreo_anexo16_restos_pagar_saude(stExercicio,dtInicial, dtFinal,stEntidades) AS tbl
               (
                  vl_restos_pagar numeric
                , vl_restos_pagar_cancelados numeric
               );
    END IF;



    IF(vlReceita <> 0.00) THEN
        vlPorc := ((vlDespesa - vlRestos)/ vlReceita ) * 100;
    ELSE
        vlPorc := 0.00;
    END IF;

    --raise notice '((% - %)/ %) * 100', vlDespesa, vlRestos, vlReceita;

    ------------------------------------------
    -- Insere os valores na tabela de retorno 
    ------------------------------------------
    IF stExercicio::INTEGER > 2012 THEN
        INSERT INTO tmp_retorno_despesas VALUES (  1
                                                 , 0
                                                 , 'Despesas Próprias com Ações e Serviços Públicos de Saúde'
                                                 , (vlDespesa - vlRestos)
                                                 , ( SELECT valor
                                                       FROM administracao.configuracao
                                                      WHERE exercicio = stExercicio
                                                        AND cod_modulo = 36
                                                        AND parametro = 'stn_anexo16_porcentagem'
                                                   ) 
                                                 , vlPorc
                                                );
    ELSE
        INSERT INTO tmp_retorno_despesas VALUES (  1
                                                 , 0
                                                 , 'Despesas Próprias com Ações e Serviços Públicos de Saúde'
                                                 , vlDespesa
                                                 , ( SELECT valor
                                                       FROM administracao.configuracao
                                                      WHERE exercicio = stExercicio
                                                        AND cod_modulo = 36
                                                        AND parametro = 'stn_anexo16_porcentagem'
                                                   )
                                                 , vlPorc
                                                );
    END IF;
 
    ----------------------------------------------------
    -- Retorna os valores da tabela temporaria
    ----------------------------------------------------
    stSql := '
        SELECT * 
          FROM tmp_retorno_despesas
      ORDER BY grupo
             , ordem
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno_despesas ;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  

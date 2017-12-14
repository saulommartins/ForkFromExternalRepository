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
    * PL que busca os valores do Demonstrativo II do AMF
    * Data de Criação   : 29/06/2009


    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION stn.fn_amf_demonstrativo2(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                 ALIAS FOR $1;
    stEntidades                 ALIAS FOR $2;
    inCodPIB                    ALIAS FOR $3;
    dtInicioAno                 VARCHAR := '';
    dtFinalAno                  VARCHAR := '';
    stSql                       VARCHAR := '';
    stExercicioAnterior         VARCHAR := '';
    boLancamento                BOOLEAN;
    vlPIB                       NUMERIC(14,2) := 1;
    vlDividaConsolidada         NUMERIC(14,2) := 0;
    vlDividaPublica             NUMERIC(14,2) := 0;
    vlApurado                   NUMERIC(14,2) := 0;
    inIdentificador             INTEGER;
    reRegistro                  RECORD;
    reReceitaTotal              RECORD;
    reDespesaTotal              RECORD;
    reReceitasPrimarias         RECORD;
    reDespesasPrimarias         RECORD;
BEGIN
    stExercicioAnterior := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')-1), '99999'));

    dtInicioAno := '01/01/' || stExercicio;
    dtFinalAno  := '31/12/' || stExercicio;

    --verifica se a sequence amf_demostrativo_2 existe
    IF((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='amf_demostrativo_2') IS NOT NULL) THEN
        SELECT NEXTVAL('stn.amf_demostrativo_2')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE stn.amf_demostrativo_2 START 1;
        SELECT NEXTVAL('stn.amf_demostrativo_2')
          INTO inIdentificador;
    END IF;

    -------------------------------------------------------
    -- Cria uma tabela temporaria para retornar os valores
    -------------------------------------------------------
    stSql := '
    CREATE TEMPORARY TABLE tmp_demonstrativo2_'||inIdentificador||' (
          ordem                     INTEGER
        , especificacao             VARCHAR
        , vl_orcado                 DECIMAL(14,2)
        , porcentagem_pib_orcado    DECIMAL(14,2)
        , vl_realizado              DECIMAL(14,2)
        , porcentagem_pib_realizado DECIMAL(14,2)
        , vl_variacao               DECIMAL(14,2)
        , vl_variacao_porcentagem   DECIMAL(14,2)
    ) ';

    EXECUTE stSql;

    -- Busca o valor do PIB
    SELECT indice
      INTO vlPIB
      FROM ldo.indicadores
     WHERE cod_tipo_indicador = inCodPIB
       AND exercicio = stExercicio;

    IF (vlPIB IS NULL OR vlPIB = 0) THEN
        vlPIB := 1;
    END IF;

    -- Verifica se possui lançamentos, se possuir, deve buscar os dados do orçamento, senão buscar da configuração do STN
    SELECT CASE WHEN exercicio IS NOT NULL THEN 
                    true 
                ELSE 
                    false 
           END 
      INTO boLancamento
      FROM contabilidade.lancamento 
     WHERE exercicio = stExercicio 
  GROUP BY exercicio;
    
    -----------------------------------------
    -- Retorna o valor do resultado primario
    -----------------------------------------

    IF (boLancamento) THEN
        SELECT COALESCE(SUM(receita_total), 0.00) AS receita_total
             , COALESCE(SUM(previsao_atualizada), 0.00) AS previsao_atualizada
          INTO reReceitaTotal
          FROM ( SELECT SUM(COALESCE(ate_bimestre, 0.00)) AS receita_total
                      , SUM(COALESCE(previsao_atualizada, 0.00)) AS previsao_atualizada
                   FROM stn.fn_rreo_anexo7_receitas(stExercicio, 6, stEntidades) 
                     AS tbl
                      ( ordem                           INTEGER
                      , grupo                           INTEGER
                      , cod_estrutural                  VARCHAR
                      , nivel                           INTEGER
                      , nom_conta                       VARCHAR
                      , previsao_atualizada             NUMERIC
                      , no_bimestre                     NUMERIC
                      , ate_bimestre                    NUMERIC
                      , ate_bimestre_exercicio_anterior NUMERIC
                      ) 
                  WHERE cod_estrutural IN ( '4.1.0.0.0.00.00.00.00.00'
                                          , '4.3.0.0.0.00.00.00.00.00'
                                          , '4.2.1.0.0.00.00.00.00.00'
                                          , '4.2.3.0.0.00.00.00.00.00'
                                          , '4.2.3.0.0.00.00.00.00.00')
               ) AS tbl;

        SELECT SUM(receitas_primarias) AS receitas_primarias
             , SUM(previsao_atualizada) AS previsao_atualizada
          INTO reReceitasPrimarias
          FROM ( SELECT SUM(COALESCE(ate_bimestre, 0.00)) AS receitas_primarias
                      , SUM(COALESCE(previsao_atualizada, 0.00)) AS previsao_atualizada
                   FROM stn.fn_rreo_anexo7_receitas(stExercicio, 6, stEntidades) 
                     AS tbl
                      ( ordem                           INTEGER
                      , grupo                           INTEGER
                      , cod_estrutural                  VARCHAR
                      , nivel                           INTEGER
                      , nom_conta                       VARCHAR
                      , previsao_atualizada             NUMERIC
                      , no_bimestre                     NUMERIC
                      , ate_bimestre                    NUMERIC
                      , ate_bimestre_exercicio_anterior NUMERIC
                      ) 
                  WHERE cod_estrutural IN ( '4.1.0.0.0.00.00.00.00.00'
                                          , '4.3.0.0.0.00.00.00.00.00')
               ) AS tbl;
        
         SELECT SUM(despesa_total) AS despesa_total
              , SUM(dotacao_atualizada) AS dotacao_atualizada
          INTO reDespesaTotal
          FROM ( SELECT SUM(COALESCE(ate_bimestre, 0.00)) AS despesa_total
                      , SUM(COALESCE(dotacao_atualizada, 0.00)) AS dotacao_atualizada
                   FROM stn.fn_rreo_anexo7_despesas(stExercicio, 6, stEntidades) 
                     AS tbl 
                      ( grupo                           INTEGER
                      , cod_estrutural                  VARCHAR
                      , descricao                       VARCHAR
                      , nivel                           INTEGER
                      , dotacao_atualizada              NUMERIC(14,2)
                      , no_bimestre                     NUMERIC(14,2)
                      , ate_bimestre                    NUMERIC(14,2)
                      , ate_bimestre_exercicio_anterior NUMERIC(14,2) )
                  WHERE cod_estrutural IN ( '4.9.0.0.00.00.00.00.00'
                                          , '4.7.0.0.00.00.00.00.00'
                                          , '9.9.9.9.99.00.00.00.00'
                                          , '4.5.9.0.66.00.00.00.00'
                                          , '4.5.9.0.64.00.00.00.00'
                                          , '4.6.0.0.00.00.00.00.00'
                                          , '7.7.9.9.99.00.00.00.00')
               ) AS tbl;

         SELECT SUM(despesas_primarias) AS despesas_primarias
              , SUM(dotacao_atualizada) AS dotacao_atualizada
          INTO reDespesasPrimarias
          FROM ( SELECT SUM(COALESCE(ate_bimestre, 0.00)) AS despesas_primarias
                      , SUM(COALESCE(dotacao_atualizada, 0.00)) AS dotacao_atualizada
                   FROM stn.fn_rreo_anexo7_despesas(stExercicio, 6, stEntidades) 
                     AS tbl 
                      ( grupo                           INTEGER
                      , cod_estrutural                  VARCHAR
                      , descricao                       VARCHAR
                      , nivel                           INTEGER
                      , dotacao_atualizada              NUMERIC(14,2)
                      , no_bimestre                     NUMERIC(14,2)
                      , ate_bimestre                    NUMERIC(14,2)
                      , ate_bimestre_exercicio_anterior NUMERIC(14,2) )
                  WHERE cod_estrutural IN ( '4.9.0.0.00.00.00.00.00'
                                          , '4.7.0.0.00.00.00.00.00'
                                          , '7.7.9.9.99.00.00.00.00')
               ) AS tbl;
       
    ELSE

        SELECT COALESCE(SUM(vl_arrecadado_liquidado), 0.00) AS receita_total
             , COALESCE(SUM(vl_previsto_fixado), 0.00) AS previsao_atualizada
          INTO reReceitaTotal
          FROM ldo.configuracao_receita_despesa 
          JOIN ldo.tipo_receita_despesa 
            ON tipo_receita_despesa.cod_tipo = configuracao_receita_despesa.cod_tipo 
           AND tipo_receita_despesa.tipo = configuracao_receita_despesa.tipo
         WHERE tipo_receita_despesa.tipo = 'R'
           AND configuracao_receita_despesa.exercicio = stExercicio;

        SELECT COALESCE(SUM(vl_arrecadado_liquidado), 0.00) AS receitas_primarias
             , COALESCE(SUM(vl_previsto_fixado), 0.00) AS previsao_atualizada
          INTO reReceitasPrimarias
          FROM ldo.configuracao_receita_despesa 
          JOIN ldo.tipo_receita_despesa 
            ON tipo_receita_despesa.cod_tipo = configuracao_receita_despesa.cod_tipo 
           AND tipo_receita_despesa.tipo = configuracao_receita_despesa.tipo
         WHERE tipo_receita_despesa.cod_estrutural IN ( '2.1.0.0.00.00.00.00.00'
                                                      , '2.3.0.0.00.00.00.00.00')
            OR tipo_receita_despesa.cod_estrutural LIKE '1.%'
           AND configuracao_receita_despesa.exercicio = stExercicio;

        SELECT COALESCE(SUM(vl_arrecadado_liquidado), 0.00) AS despesa_total
             , COALESCE(SUM(vl_previsto_fixado), 0.00) AS dotacao_atualizada
          INTO reDespesaTotal
          FROM ldo.configuracao_receita_despesa 
          JOIN ldo.tipo_receita_despesa 
            ON tipo_receita_despesa.cod_tipo = configuracao_receita_despesa.cod_tipo 
           AND tipo_receita_despesa.tipo = configuracao_receita_despesa.tipo
         WHERE tipo_receita_despesa.tipo = 'D'
           AND configuracao_receita_despesa.exercicio = stExercicio;

        SELECT COALESCE(SUM(vl_arrecadado_liquidado), 0.00) AS despesas_primarias
             , COALESCE(SUM(vl_previsto_fixado), 0.00) AS dotacao_atualizada
          INTO reDespesasPrimarias
          FROM ldo.configuracao_receita_despesa 
          JOIN ldo.tipo_receita_despesa 
            ON tipo_receita_despesa.cod_tipo = configuracao_receita_despesa.cod_tipo 
           AND tipo_receita_despesa.tipo = configuracao_receita_despesa.tipo
         WHERE (tipo_receita_despesa.cod_estrutural LIKE '3.%'
            OR tipo_receita_despesa.cod_estrutural LIKE '4.%')
           AND tipo_receita_despesa.cod_estrutural NOT LIKE '3.2%'
           AND tipo_receita_despesa.cod_estrutural NOT LIKE '4.5.9.0.66%'
           AND tipo_receita_despesa.cod_estrutural NOT LIKE '4.5.9.0.64%'
           AND tipo_receita_despesa.cod_estrutural NOT LIKE '4.6%'
           AND configuracao_receita_despesa.exercicio = stExercicio;
        
    END IF;

    -------------------------------
    -- Insere os valores na tabela
    -------------------------------
    IF (reReceitaTotal.previsao_atualizada <> 0.00) THEN
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 1
                                                             , ''Receita Total''
                                                             , '||reReceitaTotal.previsao_atualizada||'
                                                             , ('||reReceitaTotal.previsao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reReceitaTotal.receita_total||'
                                                             , ('||reReceitaTotal.receita_total||' * 100) / '||vlPIB||'
                                                             , '||reReceitaTotal.receita_total||' - '||reReceitaTotal.previsao_atualizada||'
                                                             , (('||reReceitaTotal.receita_total||' - '||reReceitaTotal.previsao_atualizada||') / '||reReceitaTotal.previsao_atualizada||') * 100
                                                        ) ';
    ELSE 
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 1
                                                             , ''Receita Total''
                                                             , '||reReceitaTotal.previsao_atualizada||'
                                                             , ('||reReceitaTotal.previsao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reReceitaTotal.receita_total||'
                                                             , ('||reReceitaTotal.receita_total||' * 100) / '||vlPIB||'
                                                             , '||reReceitaTotal.receita_total||' - '||reReceitaTotal.previsao_atualizada||'
                                                             , (('||reReceitaTotal.receita_total||' - '||reReceitaTotal.previsao_atualizada||') / 1) * 100
                                                        ) ';
    END IF;

    EXECUTE stSql;

    IF (reReceitasPrimarias.previsao_atualizada <> 0.00) THEN
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 2
                                                             , ''Receitas Primárias (I)''
                                                             , '||reReceitasPrimarias.previsao_atualizada||'
                                                             , ('||reReceitasPrimarias.previsao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reReceitasPrimarias.receitas_primarias||'
                                                             , ('||reReceitasPrimarias.receitas_primarias||' * 100) / '||vlPIB||'
                                                             , '||reReceitasPrimarias.receitas_primarias||' - '||reReceitasPrimarias.previsao_atualizada||'
                                                             , (('||reReceitasPrimarias.receitas_primarias||' - '||reReceitasPrimarias.previsao_atualizada||') / '||reReceitasPrimarias.previsao_atualizada||') * 100
                                                           ) ';
    ELSE
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 2
                                                             , ''Receitas Primárias (I)''
                                                             , '||reReceitasPrimarias.previsao_atualizada||'
                                                             , ('||reReceitasPrimarias.previsao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reReceitasPrimarias.receitas_primarias||'
                                                             , ('||reReceitasPrimarias.receitas_primarias||' * 100) / '||vlPIB||'
                                                             , '||reReceitasPrimarias.receitas_primarias||' - '||reReceitasPrimarias.previsao_atualizada||'
                                                             , (('||reReceitasPrimarias.receitas_primarias||' - '||reReceitasPrimarias.previsao_atualizada||') / 1) * 100
                                                           ) ';
    END IF;

    EXECUTE stSql;

    IF (reDespesaTotal.dotacao_atualizada <> 0.00) THEN
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 3
                                                             , ''Despesa Total''
                                                             , '||reDespesaTotal.dotacao_atualizada||'
                                                             , ('||reDespesaTotal.dotacao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reDespesaTotal.despesa_total||'
                                                             , ('||reDespesaTotal.despesa_total||' * 100) / '||vlPIB||'
                                                             , '||reDespesaTotal.despesa_total||' - '||reDespesaTotal.dotacao_atualizada||'
                                                             , (('||reDespesaTotal.despesa_total||' - '||reDespesaTotal.dotacao_atualizada||') / '||reDespesaTotal.dotacao_atualizada||') * 100
                                                            ) ';
    ELSE
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 3
                                                             , ''Despesa Total''
                                                             , '||reDespesaTotal.dotacao_atualizada||'
                                                             , ('||reDespesaTotal.dotacao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reDespesaTotal.despesa_total||'
                                                             , ('||reDespesaTotal.despesa_total||' * 100) / '||vlPIB||'
                                                             , '||reDespesaTotal.despesa_total||' - '||reDespesaTotal.dotacao_atualizada||'
                                                             , (('||reDespesaTotal.despesa_total||' - '||reDespesaTotal.dotacao_atualizada||') / 1) * 100
                                                            ) ';

    END IF;

    EXECUTE stSql;

    IF (reDespesasPrimarias.dotacao_atualizada <> 0.00) THEN
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 4
                                                             , ''Despesas Primárias (II)''
                                                             , '||reDespesasPrimarias.dotacao_atualizada||'
                                                             , ('||reDespesasPrimarias.dotacao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reDespesasPrimarias.despesas_primarias||'
                                                             , ('||reDespesasPrimarias.despesas_primarias||' * 100) / '||vlPIB||'
                                                             , '||reDespesasPrimarias.despesas_primarias||' - '||reDespesasPrimarias.dotacao_atualizada||'
                                                             , (('||reDespesasPrimarias.despesas_primarias||' - '||reDespesasPrimarias.dotacao_atualizada||') / '||reDespesasPrimarias.dotacao_atualizada||') * 100
                                                            ) ';
    ELSE
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 4
                                                             , ''Despesas Primárias (II)''
                                                             , '||reDespesasPrimarias.dotacao_atualizada||'
                                                             , ('||reDespesasPrimarias.dotacao_atualizada||' * 100) / '||vlPIB||'
                                                             , '||reDespesasPrimarias.despesas_primarias||'
                                                             , ('||reDespesasPrimarias.despesas_primarias||' * 100) / '||vlPIB||'
                                                             , '||reDespesasPrimarias.despesas_primarias||' - '||reDespesasPrimarias.dotacao_atualizada||'
                                                             , (('||reDespesasPrimarias.despesas_primarias||' - '||reDespesasPrimarias.dotacao_atualizada||') / 1) * 100
                                                            ) ';

    END IF;

    EXECUTE stSql;

    IF (reReceitasPrimarias.previsao_atualizada - reDespesasPrimarias.dotacao_atualizada <> 0.00) THEN
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 5
                                                             , ''Resultado Primário (III) = (I-II)''
                                                             , '||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||'
                                                             , (('||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||') * 100) / '||vlPIB||'
                                                             , '||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||'
                                                             , (('||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||') * 100) / '||vlPIB||'
                                                             , ('||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||') - ('||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||')
                                                             , ((('||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||') - ('||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||')) / ('||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||')) * 100
                                                            ) ';
    ELSE
        stSql := '
        INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 5
                                                             , ''Resultado Primário (III) = (I-II)''
                                                             , '||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||'
                                                             , (('||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||') * 100) / '||vlPIB||'
                                                             , '||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||'
                                                             , (('||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||') * 100) / '||vlPIB||'
                                                             , ('||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||') - ('||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||')
                                                             , ((('||reReceitasPrimarias.receitas_primarias||' - '||reDespesasPrimarias.despesas_primarias||') - ('||reReceitasPrimarias.previsao_atualizada||' - '||reDespesasPrimarias.dotacao_atualizada||')) / 1) * 100
                                                            ) ';
    END IF;

    EXECUTE stSql;

    -----------------------------------------------------
    -- Retorna o valor nominal e as dívidas consolidadas
    -----------------------------------------------------
    SELECT cons.*
         , (COALESCE(ativo_exercicio_anterior, 0.00) + COALESCE(haveres_financeiros_exercicio_anterior, 0.00) + COALESCE(restos_exercicio_anterior, 0.00)) as deducoes_exercicio_anterior
         , (COALESCE(ativo_saldo_bimestre    , 0.00) + COALESCE(haveres_financeiros_bimestre          , 0.00) + COALESCE(restos_bimestre          , 0.00)) as deducoes_bimestre
      INTO reRegistro
      FROM ( SELECT stn.pl_saldo_contas ( stExercicioAnterior
                                        , '01/01/' || stExercicioAnterior
                                        , '31/12/' || stExercicioAnterior
                                        , 'cod_estrutural like '''|| publico.fn_mascarareduzida('1.1.1.0.0.00.00.00.00.00') || '.%'' '
                                        , stEntidades ) AS ativo_exercicio_anterior
                  , stn.pl_saldo_contas ( stExercicio
                                        , dtInicioAno 
                                        , dtFinalAno
                                        , 'cod_estrutural like '''|| publico.fn_mascarareduzida('1.1.1.0.0.00.00.00.00.00') || '.%'' '
                                        , stEntidades ) as ativo_saldo_bimestre
                  , stn.pl_saldo_contas ( stExercicioAnterior
                                        , '01/01/' || stExercicioAnterior
                                        , '31/12/' || stExercicioAnterior
                                        , 'cod_estrutural like ''1.1.2.%'''
                                        , stEntidades ) AS haveres_financeiros_exercicio_anterior
                  , stn.pl_saldo_contas ( stExercicio
                                        , dtInicioAno 
                                        , dtFinalAno
                                        , 'cod_estrutural like ''1.1.2.%'' '
                                        , stEntidades ) AS haveres_financeiros_bimestre
                  , ( SELECT SUM(stn.pl_saldo_contas ( stExercicioAnterior
                                                     , '01/01/' || stExercicioAnterior
                                                     , '31/12/' || stExercicioAnterior
                                                     , 'cod_estrutural like '''|| publico.fn_mascarareduzida(plano_conta.cod_estrutural) || '.%'' '
                                                     , stEntidades )) AS saldo_exercicio_anterior
                        FROM contabilidade.plano_conta
                       WHERE exercicio = stExercicioAnterior
                         AND cod_estrutural IN ('2.1.2.1.1.02.00.00.00.00',
                                                '2.1.2.1.1.03.02.00.00.00',
                                                '2.1.2.1.2.02.00.00.00.00',
                                                '2.1.2.1.2.03.02.00.00.00',
                                                '2.1.2.1.3.01.00.02.00.00',
                                                '2.1.2.1.3.03.00.02.00.00',
                                                '2.1.2.1.3.04.00.02.00.00') ) AS restos_exercicio_anterior
                  , ( SELECT SUM(stn.pl_saldo_contas ( plano_conta.exercicio
                                                     , dtInicioAno
                                                     , dtFinalAno
                                                     , 'cod_estrutural like '''|| publico.fn_mascarareduzida( plano_conta.cod_estrutural ) || '.%'' '
                                                     , stEntidades )) AS saldo_bimestre_anterior
                        FROM contabilidade.plano_conta
                       WHERE exercicio = stExercicio
                         AND cod_estrutural IN('2.1.2.1.1.02.00.00.00.00',
                                               '2.1.2.1.1.03.02.00.00.00',
                                               '2.1.2.1.2.02.00.00.00.00',
                                               '2.1.2.1.2.03.02.00.00.00',
                                               '2.1.2.1.3.01.00.02.00.00',
                                               '2.1.2.1.3.03.00.02.00.00',
                                               '2.1.2.1.3.04.00.02.00.00') ) as restos_bimestre
                  , ( SELECT SUM(stn.pl_saldo_contas ( stExercicioAnterior
                                                     , '01/01/' || stExercicioAnterior
                                                     , '31/12/' || stExercicioAnterior
                                                     , ' ( REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22211%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22221%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22212%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22222%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2121705%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''21231020203%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2223''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2224401%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22244%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22249000002%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22%'')'
                                                     , stEntidades ) * -1 ) ) as divida_exercicio_anterior
                  , ( SELECT SUM(stn.pl_saldo_contas ( stExercicio
                                                     , dtInicioAno
                                                     , dtFinalAno 
                                                     , ' ( REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22211%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22221%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22212%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22222%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2121705%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''21231020203%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2223''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''2224401%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22244%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22249000002%''
                                                        OR REPLACE(plano_conta.cod_estrutural,''.'','''') LIKE ''22%'')'
                                                  , stEntidades ) * -1 )) as divida_bimestre
      ) as cons;

    vlApurado := (reRegistro.divida_bimestre - reRegistro.deducoes_bimestre) - (reRegistro.divida_exercicio_anterior - reRegistro.deducoes_exercicio_anterior); 
    vlDividaPublica     := reRegistro.divida_bimestre; 
    vlDividaConsolidada := (reRegistro.divida_bimestre - reRegistro.deducoes_bimestre); 

    stSql := '
    INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 6
                                                         , ''Resultado Nominal''
                                                         , 0.00
                                                         , 0.00
                                                         , '||vlApurado||'
                                                         , ('||vlApurado||' * 100) / '||vlPIB||'
                                                         , '||vlApurado||'
                                                         , '||vlApurado||' * 100
                                                        ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 7
                                                         , ''Dívida Pública Consolidada''
                                                         , 0.00
                                                         , 0.00
                                                         , '||vlDividaPublica||'
                                                         , ('||vlDividaPublica||' * 100) / '||vlPIB||'
                                                         , '||vlDividaPublica||'
                                                         , '||vlDividaPublica||' * 100
                                                        ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo2_'||inIdentificador||' VALUES ( 8
                                                         , ''Dívida Consolidada Líquida''
                                                         , 0.00
                                                         , 0.00
                                                         , '||vlDividaConsolidada||'
                                                         , ('||vlDividaConsolidada||' * 100) / '||vlPIB||'
                                                         , '||vlDividaConsolidada||'
                                                         , '||vlDividaConsolidada||' * 100
                                                        ) ';

    EXECUTE stSql;

    ----------------------------------------------------
    -- Retorna os valores da tabela temporaria
    ----------------------------------------------------
    stSql := '
        SELECT * 
          FROM tmp_demonstrativo2_'||inIdentificador||'
      ORDER BY ordem
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_demonstrativo2_'||inIdentificador;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

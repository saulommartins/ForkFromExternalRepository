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
    * PL que busca os valores do Demonstrativo I do AMF
    * Data de Criação   : 13/07/2009


    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION stn.fn_amf_demonstrativo1(VARCHAR, INTEGER, INTEGER, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                             ALIAS FOR $1;
    inCodPPA                                ALIAS FOR $2;
    inCodPIB                                ALIAS FOR $3;
    inCodInflacao                           ALIAS FOR $4;
    stSql                                   VARCHAR := '';
    stSqlDivida                             VARCHAR := '';
    stExercicioPrevisto2                    VARCHAR := '';
    stExercicioPrevisto3                    VARCHAR := '';
    vlPIBReceitaTotal                       NUMERIC[] := array[0];
    vlPIBReceitasPrimarias                  NUMERIC[] := array[0];
    vlPIBDespesaTotal                       NUMERIC[] := array[0];
    vlPIBDespesasPrimarias                  NUMERIC[] := array[0];
    vlPIBResultadoPrimario                  NUMERIC[] := array[0];
    vlPIBResultadoNominal                   NUMERIC[] := array[0];
    vlPIBDividaPublicaConsolidada           NUMERIC[] := array[0];
    vlPIBDividaConsolidadaLiquida           NUMERIC[] := array[0];
    vlConstanteReceitaTotal                 NUMERIC[] := array[0];
    vlConstanteReceitasPrimarias            NUMERIC[] := array[0];
    vlConstanteDespesaTotal                 NUMERIC[] := array[0];
    vlConstanteDespesasPrimarias            NUMERIC[] := array[0];
    vlConstanteResultadoPrimario            NUMERIC[] := array[0];
    vlConstanteResultadoNominal             NUMERIC[] := array[0];
    vlConstanteDividaPublicaConsolidada     NUMERIC[] := array[0];
    vlConstanteDividaConsolidadaLiquida     NUMERIC[] := array[0];
    vlDeflacao                              NUMERIC[] := array[0];
    vlTMP                                   NUMERIC(14,2) := 0;
    vlReceitaTotal                          NUMERIC[] := array[0];
    vlReceitasPrimarias                     NUMERIC[] := array[0];
    vlDespesaTotal                          NUMERIC[] := array[0];
    vlDespesasPrimarias                     NUMERIC[] := array[0];
    vlResultadoPrimario                     NUMERIC[] := array[0];
    vlResultadoNominal                      NUMERIC[] := array[0];
    vlDividaPublicaConsolidada              NUMERIC[] := array[0];
    vlDividaConsolidadaLiquida              NUMERIC[] := array[0];
    inIdentificador                         INTEGER;
    inCount                                 INTEGER;
    reRegistro                              RECORD;
    reRegistroLoop                          RECORD;
    reRegistroDivida                        RECORD;
BEGIN
    stExercicioPrevisto2 := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')+1), '99999'));
    stExercicioPrevisto3 := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')+2), '99999'));

    --verifica se a sequence amf_demostrativo_1 existe
    IF((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='amf_demostrativo_1') IS NOT NULL) THEN
        SELECT NEXTVAL('stn.amf_demostrativo_1')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE stn.amf_demostrativo_1 START 1;
        SELECT NEXTVAL('stn.amf_demostrativo_1')
          INTO inIdentificador;
    END IF;

    -------------------------------------------------------
    -- Cria uma tabela temporaria para retornar os valores
    -------------------------------------------------------
    stSql := '
    CREATE TEMPORARY TABLE tmp_demonstrativo1_'||inIdentificador||' (
          ordem             INTEGER
        , especificacao     VARCHAR
        , vl_corrente_1     DECIMAL(14,2)
        , vl_corrente_2     DECIMAL(14,2)
        , vl_corrente_3     DECIMAL(14,2)
        , vl_constante_1    DECIMAL(14,2)
        , vl_constante_2    DECIMAL(14,2)
        , vl_constante_3    DECIMAL(14,2)
        , porcentagem_1     DECIMAL(14,2)
        , porcentagem_2     DECIMAL(14,2)
        , porcentagem_3     DECIMAL(14,2)
    ) ';

    EXECUTE stSql;

    stSql := '
    CREATE TEMPORARY TABLE tmp_divida_'||inIdentificador||' AS (
        SELECT ordem         
             , cod_tipo
             , especificacao 
             , bo_orcamento_1
             , bo_orcamento_2
             , bo_orcamento_3
             , bo_orcamento_4
             , bo_orcamento_5
             , bo_orcamento_6
             , valor_1 
             , valor_2 
             , valor_3 
             , valor_4
             , valor_5 
             , valor_6 
             , exercicio_1   
             , exercicio_2   
             , exercicio_3   
             , exercicio_4   
             , exercicio_5   
             , exercicio_6 
          FROM ldo.evolucao_divida('||inCodPPA||', '''||stExercicio||''') AS (
                  ordem             INTEGER
                , cod_tipo          INTEGER
                , especificacao     VARCHAR
                , valor_1           DECIMAL(14,2)
                , valor_2           DECIMAL(14,2)
                , valor_3           DECIMAL(14,2)
                , valor_4           DECIMAL(14,2)
                , valor_5           DECIMAL(14,2)
                , valor_6           DECIMAL(14,2)
                , bo_orcamento_1    DECIMAL(1)
                , bo_orcamento_2    DECIMAL(1)
                , bo_orcamento_3    DECIMAL(1)
                , bo_orcamento_4    DECIMAL(1)
                , bo_orcamento_5    DECIMAL(1)
                , bo_orcamento_6    DECIMAL(1)
                , exercicio_1       CHAR(4)
                , exercicio_2       CHAR(4)
                , exercicio_3       CHAR(4)
                , exercicio_4       CHAR(4)
                , exercicio_5       CHAR(4)
                , exercicio_6       CHAR(4)
        )
    ) ';

    EXECUTE stSql;

    inCount := 1;

    stSql := '
            SELECT CAST(' || stExercicio || ' AS VARCHAR) AS exercicio
            UNION
            SELECT CAST(' || stExercicioPrevisto2 || ' AS VARCHAR) AS exercicio
            UNION
            SELECT CAST(' || stExercicioPrevisto3 || ' AS VARCHAR) AS exercicio
            ORDER BY exercicio
    ';

    FOR reRegistroLoop IN EXECUTE stSql LOOP
        SELECT COALESCE(SUM(vl_projetado), 0.00)
          INTO vlTMP
          FROM ldo.configuracao_receita_despesa 
          JOIN ldo.tipo_receita_despesa 
            ON tipo_receita_despesa.cod_tipo = configuracao_receita_despesa.cod_tipo 
           AND tipo_receita_despesa.tipo = configuracao_receita_despesa.tipo
         WHERE tipo_receita_despesa.tipo = 'R'
           AND configuracao_receita_despesa.exercicio = reRegistroLoop.exercicio;

        vlReceitaTotal[inCount] := vlTMP;

        SELECT COALESCE(SUM(vl_projetado), 0.00)
          INTO vlTMP
          FROM ldo.configuracao_receita_despesa 
          JOIN ldo.tipo_receita_despesa 
            ON tipo_receita_despesa.cod_tipo = configuracao_receita_despesa.cod_tipo 
           AND tipo_receita_despesa.tipo = configuracao_receita_despesa.tipo
         WHERE tipo_receita_despesa.cod_estrutural IN ( '2.1.0.0.00.00.00.00.00'
                                                      , '2.3.0.0.00.00.00.00.00')
            OR tipo_receita_despesa.cod_estrutural LIKE '1.%'
           AND configuracao_receita_despesa.exercicio = reRegistroLoop.exercicio;

        vlReceitasPrimarias[inCount] := vlTMP;

        SELECT COALESCE(SUM(vl_projetado), 0.00)
          INTO vlTMP
          FROM ldo.configuracao_receita_despesa 
          JOIN ldo.tipo_receita_despesa 
            ON tipo_receita_despesa.cod_tipo = configuracao_receita_despesa.cod_tipo 
           AND tipo_receita_despesa.tipo = configuracao_receita_despesa.tipo
         WHERE tipo_receita_despesa.tipo = 'D'
           AND configuracao_receita_despesa.exercicio = reRegistroLoop.exercicio;

        vlDespesaTotal[inCount] := vlTMP;

        SELECT COALESCE(SUM(vl_projetado), 0.00)
          INTO vlTMP
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
           AND configuracao_receita_despesa.exercicio = reRegistroLoop.exercicio;

        vlDespesasPrimarias[inCount] := vlTMP;
        vlResultadoPrimario[inCount] := vlReceitasPrimarias[inCount] - vlDespesasPrimarias[inCount];

        IF (inCount = 1) THEN
            stSqlDivida := '
            SELECT valor_4
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 1 ';
        ELSEIF (inCount = 2) THEN
            stSqlDivida := '
            SELECT valor_5
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 1 ';
        ELSEIF (inCount = 3) THEN
            stSqlDivida := '
            SELECT valor_6
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 1 ';
        END IF;

        FOR reRegistroDivida IN EXECUTE stSqlDivida LOOP
            IF (inCount = 1) THEN
                vlDividaPublicaConsolidada[inCount] := reRegistroDivida.valor_4;
            ELSEIF (inCount = 2) THEN
                vlDividaPublicaConsolidada[inCount] := reRegistroDivida.valor_5;
            ELSEIF (inCount = 3) THEN
                vlDividaPublicaConsolidada[inCount] := reRegistroDivida.valor_6;
            END IF;
        END LOOP;

        IF (inCount = 1) THEN
            stSqlDivida := '
            SELECT valor_4
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 3 ';
        ELSEIF (inCount = 2) THEN
            stSqlDivida := '
            SELECT valor_5
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 3 ';
        ELSEIF (inCount = 3) THEN
            stSqlDivida := '
            SELECT valor_6
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 3 ';
        END IF;

        FOR reRegistroDivida IN EXECUTE stSqlDivida LOOP
            IF (inCount = 1) THEN
                vlDividaConsolidadaLiquida[inCount] := reRegistroDivida.valor_4;
            ELSEIF (inCount = 2) THEN
                vlDividaConsolidadaLiquida[inCount] := reRegistroDivida.valor_5;
            ELSEIF (inCount = 3) THEN
                vlDividaConsolidadaLiquida[inCount] := reRegistroDivida.valor_6;
            END IF;
        END LOOP;

        IF (inCount = 1) THEN
            stSqlDivida := '
            SELECT valor_4
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 6 ';
        ELSEIF (inCount = 2) THEN
            stSqlDivida := '
            SELECT valor_5
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 6 ';
        ELSEIF (inCount = 3) THEN
            stSqlDivida := '
            SELECT valor_6
              FROM tmp_divida_'||inIdentificador||'
             WHERE cod_tipo = 6 ';
        END IF;

        FOR reRegistroDivida IN EXECUTE stSqlDivida LOOP
            IF (inCount = 1) THEN
                vlResultadoNominal[inCount] := reRegistroDivida.valor_4;
            ELSEIF (inCount = 2) THEN
                vlResultadoNominal[inCount] := reRegistroDivida.valor_5;
            ELSEIF (inCount = 3) THEN
                vlResultadoNominal[inCount] := reRegistroDivida.valor_6;
            END IF;
        END LOOP;

        -- Busca o valor do PIB
        SELECT indice
          INTO vlTMP
          FROM ldo.indicadores
         WHERE cod_tipo_indicador = inCodPIB
           AND exercicio = reRegistroLoop.exercicio;

        IF (vlTMP IS NULL OR vlTMP = 0) THEN
            vlTMP := 1;
        END IF;

        vlPIBReceitaTotal[inCount]             := (vlReceitaTotal[inCount]             / vlTMP) * 100;
        vlPIBReceitasPrimarias[inCount]        := (vlReceitasPrimarias[inCount]        / vlTMP) * 100;
        vlPIBDespesaTotal[inCount]             := (vlDespesaTotal[inCount]             / vlTMP) * 100;
        vlPIBDespesasPrimarias[inCount]        := (vlDespesasPrimarias[inCount]        / vlTMP) * 100;
        vlPIBResultadoPrimario[inCount]        := (vlResultadoPrimario[inCount]        / vlTMP) * 100;
        vlPIBResultadoNominal[inCount]         := (vlResultadoPrimario[inCount]        / vlTMP) * 100;
        vlPIBDividaPublicaConsolidada[inCount] := (vlDividaPublicaConsolidada[inCount] / vlTMP) * 100;
        vlPIBDividaConsolidadaLiquida[inCount] := (vlDividaConsolidadaLiquida[inCount] / vlTMP) * 100;

        SELECT indice
          INTO vlTMP
          FROM ldo.indicadores
         WHERE cod_tipo_indicador = inCodInflacao
           AND exercicio = reRegistroLoop.exercicio;

        IF (vlTMP IS NULL OR vlTMP = 0) THEN
            vlTMP := 1;
        END IF;

        IF (inCount = 1) THEN
            vlDeflacao[inCount] := 1 + (vlTMP / 100);
        ELSE
            vlDeflacao[inCount] := (vlDeflacao[inCount-1]) * (1 + (vlTMP / 100));
        END IF;

        vlConstanteReceitaTotal[inCount]             := vlReceitaTotal[inCount]             / vlDeflacao[inCount];
        vlConstanteReceitasPrimarias[inCount]        := vlReceitasPrimarias[inCount]        / vlDeflacao[inCount];
        vlConstanteDespesaTotal[inCount]             := vlDespesaTotal[inCount]             / vlDeflacao[inCount];
        vlConstanteDespesasPrimarias[inCount]        := vlDespesasPrimarias[inCount]        / vlDeflacao[inCount];
        vlConstanteResultadoPrimario[inCount]        := vlResultadoPrimario[inCount]        / vlDeflacao[inCount];
        vlConstanteResultadoNominal[inCount]         := vlResultadoNominal[inCount]         / vlDeflacao[inCount];
        vlConstanteDividaPublicaConsolidada[inCount] := vlDividaPublicaConsolidada[inCount] / vlDeflacao[inCount];
        vlConstanteDividaConsolidadaLiquida[inCount] := vlDividaConsolidadaLiquida[inCount] / vlDeflacao[inCount];

        inCount := inCount + 1;
    END LOOP;        

    -------------------------------
    -- Insere os valores na tabela
    -------------------------------
    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 1
                                                  , ''Receita Total''
                                                  , '||vlReceitaTotal[1]||'
                                                  , '||vlReceitaTotal[2]||'
                                                  , '||vlReceitaTotal[3]||'
                                                  , '||vlConstanteReceitaTotal[1]||'
                                                  , '||vlConstanteReceitaTotal[2]||'
                                                  , '||vlConstanteReceitaTotal[3]||'
                                                  , '||vlPIBReceitaTotal[1]||'
                                                  , '||vlPIBReceitaTotal[2]||'
                                                  , '||vlPIBReceitaTotal[3]||' ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 2
                                                  , ''Receitas Primárias (I)''
                                                  , '||vlReceitasPrimarias[1]||'
                                                  , '||vlReceitasPrimarias[2]||'
                                                  , '||vlReceitasPrimarias[3]||'
                                                  , '||vlConstanteReceitasPrimarias[1]||'
                                                  , '||vlConstanteReceitasPrimarias[2]||'
                                                  , '||vlConstanteReceitasPrimarias[3]||'
                                                  , '||vlPIBReceitasPrimarias[1]||'
                                                  , '||vlPIBReceitasPrimarias[2]||'
                                                  , '||vlPIBReceitasPrimarias[3]||' ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 3
                                                  , ''Despesa Total''
                                                  , '||vlDespesaTotal[1]||'
                                                  , '||vlDespesaTotal[2]||'
                                                  , '||vlDespesaTotal[3]||'
                                                  , '||vlConstanteDespesaTotal[1]||'
                                                  , '||vlConstanteDespesaTotal[2]||'
                                                  , '||vlConstanteDespesaTotal[3]||'
                                                  , '||vlPIBDespesaTotal[1]||' 
                                                  , '||vlPIBDespesaTotal[2]||' 
                                                  , '||vlPIBDespesaTotal[3]||' ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 4
                                                  , ''Despesas Primárias''
                                                  , '||vlDespesasPrimarias[1]||'
                                                  , '||vlDespesasPrimarias[2]||'
                                                  , '||vlDespesasPrimarias[3]||'
                                                  , '||vlConstanteDespesasPrimarias[1]||'
                                                  , '||vlConstanteDespesasPrimarias[2]||'
                                                  , '||vlConstanteDespesasPrimarias[3]||'
                                                  , '||vlPIBDespesasPrimarias[1]||'
                                                  , '||vlPIBDespesasPrimarias[2]||'
                                                  , '||vlPIBDespesasPrimarias[3]||' ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 5
                                                  , ''Resultado Primário (III) = (I - II)''
                                                  , '||vlResultadoPrimario[1]||'
                                                  , '||vlResultadoPrimario[2]||'
                                                  , '||vlResultadoPrimario[3]||'
                                                  , '||vlConstanteResultadoPrimario[1]||'
                                                  , '||vlConstanteResultadoPrimario[2]||'
                                                  , '||vlConstanteResultadoPrimario[3]||'
                                                  , '||vlPIBResultadoPrimario[1]||'
                                                  , '||vlPIBResultadoPrimario[2]||'
                                                  , '||vlPIBResultadoPrimario[3]||' ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 6
                                                  , ''Resultado Nominal''
                                                  , '||vlResultadoNominal[1]||'
                                                  , '||vlResultadoNominal[2]||'
                                                  , '||vlResultadoNominal[3]||'
                                                  , '||vlConstanteResultadoNominal[1]||'
                                                  , '||vlConstanteResultadoNominal[2]||'
                                                  , '||vlConstanteResultadoNominal[3]||'
                                                  , '||vlPIBResultadoNominal[1]||'
                                                  , '||vlPIBResultadoNominal[2]||'
                                                  , '||vlPIBResultadoNominal[3]||' ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 7
                                                  , ''Dívida Pública Consolidada''
                                                  , '||vlDividaPublicaConsolidada[1]||'
                                                  , '||vlDividaPublicaConsolidada[2]||'
                                                  , '||vlDividaPublicaConsolidada[3]||'
                                                  , '||vlConstanteDividaPublicaConsolidada[1]||'
                                                  , '||vlConstanteDividaPublicaConsolidada[2]||'
                                                  , '||vlConstanteDividaPublicaConsolidada[3]||'
                                                  , '||vlPIBDividaPublicaConsolidada[1]||'
                                                  , '||vlPIBDividaPublicaConsolidada[2]||'
                                                  , '||vlPIBDividaPublicaConsolidada[3]||' ) ';

    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_demonstrativo1_'||inIdentificador||' ( ordem
                                                  , especificacao
                                                  , vl_corrente_1
                                                  , vl_corrente_2
                                                  , vl_corrente_3
                                                  , vl_constante_1
                                                  , vl_constante_2
                                                  , vl_constante_3
                                                  , porcentagem_1
                                                  , porcentagem_2
                                                  , porcentagem_3 )
                                           VALUES ( 8
                                                  , ''Dívida Consolidada Líquida''
                                                  , '||vlDividaConsolidadaLiquida[1]||'
                                                  , '||vlDividaConsolidadaLiquida[2]||'
                                                  , '||vlDividaConsolidadaLiquida[3]||'
                                                  , '||vlConstanteDividaConsolidadaLiquida[1]||'
                                                  , '||vlConstanteDividaConsolidadaLiquida[2]||'
                                                  , '||vlConstanteDividaConsolidadaLiquida[3]||'
                                                  , '||vlPIBDividaConsolidadaLiquida[1]||'
                                                  , '||vlPIBDividaConsolidadaLiquida[2]||'
                                                  , '||vlPIBDividaConsolidadaLiquida[3]||' ) ';

    EXECUTE stSql;

    ----------------------------------------------------
    -- Retorna os valores da tabela temporaria
    ----------------------------------------------------
    stSql := '
        SELECT * 
          FROM tmp_demonstrativo1_'||inIdentificador||'
      ORDER BY ordem
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_demonstrativo1_'||inIdentificador;
    EXECUTE 'DROP TABLE tmp_divida_'||inIdentificador;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

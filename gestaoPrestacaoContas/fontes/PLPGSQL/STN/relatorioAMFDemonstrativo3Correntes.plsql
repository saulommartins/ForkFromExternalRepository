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
    * PL que busca os valores correntes do Demonstrativo III do AMF
    * Data de Criação   : 28/09/2009


    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION stn.fn_amf_demonstrativo3_correntes(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                             ALIAS FOR $1;
    stEntidades                             ALIAS FOR $2;
    inCodPPA                                ALIAS FOR $3;
    stSql                                   VARCHAR := '';
    stEspecificacoes                        VARCHAR[] := array[0];
    stExercicioAnterior1                    VARCHAR := '';
    stExercicioAnterior2                    VARCHAR := '';
    stExercicioAnterior3                    VARCHAR := '';
    stExercicioPrevisto1                    VARCHAR := '';
    stExercicioPrevisto2                    VARCHAR := '';
    vlExercicioAnterior1                    NUMERIC[] := array[0];
    vlExercicioAnterior2                    NUMERIC[] := array[0];
    vlExercicioAnterior3                    NUMERIC[] := array[0];
    vlExercicioPrevisto1                    NUMERIC[] := array[0];
    vlExercicioPrevisto2                    NUMERIC[] := array[0];
    vlExercicioPrevisto3                    NUMERIC[] := array[0];
    vlPorcentagem1                          NUMERIC[] := array[0];
    vlPorcentagem2                          NUMERIC[] := array[0];
    vlPorcentagem3                          NUMERIC[] := array[0];
    vlPorcentagem4                          NUMERIC[] := array[0];
    vlPorcentagem5                          NUMERIC[] := array[0];
    inIdentificador                         INTEGER;
    inCount                                 INTEGER;
    reRegistro                              RECORD;
    reRegistroPrevisao                      RECORD;
    reRegistroRealizado                     RECORD;
BEGIN
    stExercicioAnterior1 := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')-3), '99999'));
    stExercicioAnterior2 := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')-2), '99999'));
    stExercicioAnterior3 := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')-1), '99999'));
    stExercicioPrevisto1 := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')+1), '99999'));
    stExercicioPrevisto2 := TRIM(TO_CHAR((TO_NUMBER(stExercicio, '99999')+2), '99999'));

    --verifica se a sequence amf_demostrativo_3 existe
    IF((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='amf_demostrativo_3') IS NOT NULL) THEN
        SELECT NEXTVAL('stn.amf_demostrativo_3')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE stn.amf_demostrativo_3 START 1;
        SELECT NEXTVAL('stn.amf_demostrativo_3')
          INTO inIdentificador;
    END IF;

    -------------------------------------------------------
    -- Cria uma tabela temporaria para retornar os valores
    -------------------------------------------------------
    stSql := '
    CREATE TEMPORARY TABLE tmp_demonstrativo3_correntes_'||inIdentificador||' (
          ordem             INTEGER
        , especificacao     VARCHAR
        , vl_corrente_1     DECIMAL(14,2)
        , vl_corrente_2     DECIMAL(14,2)
        , vl_corrente_3     DECIMAL(14,2)
        , vl_corrente_4     DECIMAL(14,2)
        , vl_corrente_5     DECIMAL(14,2)
        , vl_corrente_6     DECIMAL(14,2)
        , porcentagem_1     DECIMAL(14,2)
        , porcentagem_2     DECIMAL(14,2)
        , porcentagem_3     DECIMAL(14,2)
        , porcentagem_4     DECIMAL(14,2)
        , porcentagem_5     DECIMAL(14,2)
    ) ';

    EXECUTE stSql;

    -------------------------------------------
    -- Busca os valores de 3 exercícios atrás
    -------------------------------------------
    stSql := '
    SELECT * 
      FROM stn.fn_amf_demonstrativo2('''||stExercicioAnterior1||''', '''||stEntidades||''', 0) AS (
              ordem                     INTEGER
            , especificacao             VARCHAR
            , vl_orcado                 NUMERIC(14,2)
            , porcentagem_pib_orcado    NUMERIC(14,2)
            , vl_realizado              NUMERIC(14,2)
            , porcentagem_pib_realizado NUMERIC(14,2)
            , vl_variacao               NUMERIC(14,2)
            , vl_variacao_porcentagem   NUMERIC(14,2)
    ) ORDER BY ordem ';

    inCount := 1;
    FOR reRegistroRealizado IN EXECUTE stSql LOOP
        vlExercicioAnterior1[inCount] := reRegistroRealizado.vl_realizado;

        inCount := inCount + 1;
    END LOOP;

    -------------------------------------------
    -- Busca os valores de 2 exercícios atrás
    -------------------------------------------
    stSql := '
    SELECT * 
      FROM stn.fn_amf_demonstrativo2('''||stExercicioAnterior2||''', '''||stEntidades||''', 0) AS (
              ordem                     INTEGER
            , especificacao             VARCHAR
            , vl_orcado                 NUMERIC(14,2)
            , porcentagem_pib_orcado    NUMERIC(14,2)
            , vl_realizado              NUMERIC(14,2)
            , porcentagem_pib_realizado NUMERIC(14,2)
            , vl_variacao               NUMERIC(14,2)
            , vl_variacao_porcentagem   NUMERIC(14,2)
    ) ORDER BY ordem ';

    inCount := 1;
    FOR reRegistroRealizado IN EXECUTE stSql LOOP
        vlExercicioAnterior2[inCount] := reRegistroRealizado.vl_realizado;

        inCount := inCount + 1;
    END LOOP;

    -------------------------------------------
    -- Busca os valores de 1 exercício atrás
    -------------------------------------------
    stSql := '
    SELECT * 
      FROM stn.fn_amf_demonstrativo2('''||stExercicioAnterior3||''', '''||stEntidades||''', 0) AS (
              ordem                     INTEGER
            , especificacao             VARCHAR
            , vl_orcado                 NUMERIC(14,2)
            , porcentagem_pib_orcado    NUMERIC(14,2)
            , vl_realizado              NUMERIC(14,2)
            , porcentagem_pib_realizado NUMERIC(14,2)
            , vl_variacao               NUMERIC(14,2)
            , vl_variacao_porcentagem   NUMERIC(14,2)
    ) ORDER BY ordem ';

    inCount := 1;
    FOR reRegistroRealizado IN EXECUTE stSql LOOP
        vlExercicioAnterior3[inCount] := reRegistroRealizado.vl_realizado;

        inCount := inCount + 1;
    END LOOP;


    stSql := '
        SELECT * 
          FROM stn.fn_amf_demonstrativo1('''||stExercicio||''', '||inCodPPA||', 0, 0) AS (
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
        ) ORDER BY ordem ';

    inCount := 1;
    FOR reRegistroPrevisao IN EXECUTE stSql LOOP
        vlExercicioPrevisto1[inCount] := reRegistroPrevisao.vl_corrente_1;
        vlExercicioPrevisto2[inCount] := reRegistroPrevisao.vl_corrente_2;
        vlExercicioPrevisto3[inCount] := reRegistroPrevisao.vl_corrente_3;
        inCount := inCount + 1;
    END LOOP;

    FOR inCount IN 1..8 LOOP
        IF (vlExercicioAnterior1[inCount] <> 0) THEN
            IF (vlExercicioAnterior2[inCount] = 0) THEN
                IF (vlExercicioAnterior1[inCount] > 0) THEN
                    vlPorcentagem1[inCount] := -100;
                ELSE
                    vlPorcentagem1[inCount] := 100;
                END IF;
            ELSE
                vlPorcentagem1[inCount] := ((vlExercicioAnterior2[inCount] / vlExercicioAnterior1[inCount]) - 1) * 100;
                IF (vlExercicioAnterior1[inCount] < vlExercicioAnterior2[inCount] AND vlPorcentagem1[inCount] < 0) THEN
                    vlPorcentagem1[inCount] := vlPorcentagem1[inCount] * (-1);
                END IF;
            END IF;
        ELSE
            vlPorcentagem1[inCount] := 0.00;
        END IF;

        IF (vlExercicioAnterior2[inCount] <> 0) THEN
            IF (vlExercicioAnterior3[inCount] = 0) THEN
                IF (vlExercicioAnterior2[inCount] > 0) THEN
                    vlPorcentagem2[inCount] := -100;
                ELSE
                    vlPorcentagem2[inCount] := 100;
                END IF;
            ELSE
                vlPorcentagem2[inCount] := ((vlExercicioAnterior3[inCount] / vlExercicioAnterior2[inCount]) - 1) * 100;
                IF (vlExercicioAnterior2[inCount] < vlExercicioAnterior3[inCount] AND vlPorcentagem2[inCount] < 0) THEN
                    vlPorcentagem2[inCount] := vlPorcentagem2[inCount] * (-1);
                END IF;
            END IF;
        ELSE
            vlPorcentagem2[inCount] := 0.00;
        END IF;

        IF (vlExercicioAnterior3[inCount] <> 0) THEN
            IF (vlExercicioPrevisto1[inCount] = 0) THEN
                IF (vlExercicioAnterior3[inCount] > 0) THEN
                    vlPorcentagem3[inCount] := -100;
                ELSE
                    vlPorcentagem3[inCount] := 100;
                END IF;
            ELSE
                vlPorcentagem3[inCount] := ((vlExercicioPrevisto1[inCount] / vlExercicioAnterior3[inCount]) - 1) * 100;
                IF (vlExercicioAnterior3[inCount] < vlExercicioPrevisto1[inCount] AND vlPorcentagem3[inCount] < 0) THEN
                    vlPorcentagem3[inCount] := vlPorcentagem3[inCount] * (-1);
                END IF;
            END IF;
        ELSE
            vlPorcentagem3[inCount] := 0.00;
        END IF;

        IF (vlExercicioPrevisto1[inCount] <> 0) THEN
            IF (vlExercicioPrevisto2[inCount] = 0) THEN
                IF (vlExercicioPrevisto1[inCount] > 0) THEN
                    vlPorcentagem4[inCount] := -100;
                ELSE
                    vlPorcentagem4[inCount] := 100;
                END IF;
            ELSE
                vlPorcentagem4[inCount] := ((vlExercicioPrevisto2[inCount] / vlExercicioPrevisto1[inCount]) - 1) * 100;
                IF (vlExercicioPrevisto1[inCount] < vlExercicioPrevisto2[inCount] AND vlPorcentagem4[inCount] < 0) THEN
                    vlPorcentagem4[inCount] := vlPorcentagem4[inCount] * (-1);
                END IF;
            END IF;
        ELSE
            vlPorcentagem4[inCount] := 0.00;
        END IF;

        IF (vlExercicioPrevisto2[inCount] <> 0) THEN
            IF (vlExercicioPrevisto3[inCount] = 0) THEN
                IF (vlExercicioPrevisto2[inCount] > 0) THEN
                    vlPorcentagem5[inCount] := -100;
                ELSE
                    vlPorcentagem5[inCount] := 100;
                END IF;
            ELSE
                vlPorcentagem5[inCount] := ((vlExercicioPrevisto3[inCount] / vlExercicioPrevisto2[inCount]) - 1) * 100;
                IF (vlExercicioPrevisto2[inCount] < vlExercicioPrevisto3[inCount] AND vlPorcentagem5[inCount] < 0) THEN
                    vlPorcentagem5[inCount] := vlPorcentagem5[inCount] * (-1);
                END IF;
            END IF;
        ELSE
            vlPorcentagem5[inCount] := 0.00;
        END IF;

    END LOOP;

    stEspecificacoes[1] := 'Receita Total';
    stEspecificacoes[2] := 'Receitas Primárias(I)';
    stEspecificacoes[3] := 'Despesa Total';
    stEspecificacoes[4] := 'Despesas Primárias(II)';
    stEspecificacoes[5] := 'Resultado Primário (III) = (I - II)';
    stEspecificacoes[6] := 'Resultado Nominal';
    stEspecificacoes[7] := 'Dívida Pública Consolidada';
    stEspecificacoes[8] := 'Dívida Consolidada Líquida';

    FOR inCount IN 1..8 LOOP
        stSql := '
        INSERT INTO tmp_demonstrativo3_correntes_'||inIdentificador||' ( ordem
                                                                       , especificacao
                                                                       , vl_corrente_1
                                                                       , vl_corrente_2
                                                                       , vl_corrente_3
                                                                       , vl_corrente_4
                                                                       , vl_corrente_5
                                                                       , vl_corrente_6
                                                                       , porcentagem_1
                                                                       , porcentagem_2
                                                                       , porcentagem_3
                                                                       , porcentagem_4
                                                                       , porcentagem_5
                                                              ) VALUES ( 1
                                                                       , '''||stEspecificacoes[inCount]||'''
                                                                       , '||vlExercicioAnterior1[inCount]||'
                                                                       , '||vlExercicioAnterior2[inCount]||'
                                                                       , '||vlExercicioAnterior3[inCount]||'
                                                                       , '||vlExercicioPrevisto1[inCount]||'
                                                                       , '||vlExercicioPrevisto2[inCount]||'
                                                                       , '||vlExercicioPrevisto3[inCount]||'
                                                                       , '||vlPorcentagem1[inCount]||'
                                                                       , '||vlPorcentagem2[inCount]||'
                                                                       , '||vlPorcentagem3[inCount]||'
                                                                       , '||vlPorcentagem4[inCount]||'
                                                                       , '||vlPorcentagem5[inCount]||' ) ';
    
        EXECUTE stSql;
    END LOOP;

    ----------------------------------------------------
    -- Retorna os valores da tabela temporaria
    ----------------------------------------------------
    stSql := '
        SELECT ordem        
             , especificacao
             , vl_corrente_1
             , vl_corrente_2
             , vl_corrente_3
             , vl_corrente_4
             , vl_corrente_5
             , vl_corrente_6
             , porcentagem_1
             , porcentagem_2
             , porcentagem_3
             , porcentagem_4
             , porcentagem_5
          FROM tmp_demonstrativo3_correntes_'||inIdentificador||'
      ORDER BY ordem
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_demonstrativo3_correntes_'||inIdentificador;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  

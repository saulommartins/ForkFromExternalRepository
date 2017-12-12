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
    * Relatório RGF Anexo 07 Versão 7 (2008)
    * relatorioRGFAnexo7.plsql
    * Data de Criação: 18/07/2008


    * @author Leopoldo Braga Barreiro

    * Casos de uso: 

    $Id: relatorioRGFAnexo7.plsql 66512 2016-09-08 21:12:48Z michel $
*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo7(stExercicio VARCHAR, stEntidades VARCHAR, stTipoPeriodo VARCHAR, inPeriodo INTEGER) RETURNS SETOF RECORD AS 

$$

DECLARE 

    stDtIniEx       VARCHAR := '';
    stDtIniRetro    VARCHAR := '';
    stSQL           VARCHAR := '';
    reReg           RECORD;
    crCursor        REFCURSOR;

    -- Receita Corrente Líquida
    nuRCL           NUMERIC(14,2);
    flValorRCL      NUMERIC(14,2) := 0.00;

    -- DTP e Limites
    nuDTP           NUMERIC(14,2);
    nuLimMaxDTP     NUMERIC(14,3) := 0.540;
    nuLimPruDTP     NUMERIC(14,3) := 0.513;

    -- Divida Consolidada Líquida
    nuDCL           NUMERIC(14,2);
    nuLimDCL        NUMERIC(14,3) := 1.20;

    -- Garantias de Valores
    nuGV            NUMERIC(14,3);
    nuLimGV         NUMERIC(14,3) := 0.220;

    -- Operações de Crédito Externas e Internas
    nuOCEI          NUMERIC(14,2);
    nuLimOCEI       NUMERIC(14,3) := 0.160;

    -- Operações de Crédito por Antecipação da Receita
    nuOCAR          NUMERIC(14,2);
    nuLimOCAR       NUMERIC(14,3) := 0.070;

    stDtIni           VARCHAR;
    stDtFim           VARCHAR;
    stExercicioRestos VARCHAR;

    stCpA2          VARCHAR;

BEGIN

    -- Definicao de Datas conforme Periodo Selecionado
    stDtIniEx := '01/01/' || stExercicio ||'';

    IF (stTipoPeriodo = 'Quadrimestre' OR stTipoPeriodo = 'UltimoQuadrimestre') THEN

        IF (inPeriodo = 1) THEN
            stDtIni      := '01/01/' || stExercicio||'';
            stDtFim      := '30/04/' || stExercicio||'';
            stDtIniRetro := '01/05/' || CAST((CAST(stExercicio AS INTEGER)-1) AS VARCHAR);
            stCpA2       := 'vl_quad_1';
            stExercicioRestos := CAST((CAST(stExercicio AS INTEGER)-1) AS VARCHAR);

        ELSEIF (inPeriodo = 2) THEN
            stDtIni      := '01/01/' || stExercicio||'';
            stDtFim      := '31/08/' || stExercicio||'';
            stDtIniRetro := '01/09/' || CAST((CAST(stExercicio AS INTEGER)-1) AS VARCHAR);
            stCpA2       := 'vl_quad_2';
            stExercicioRestos := stExercicio;

        ELSE
            stDtIni      := '01/01/' || stExercicio||'';
            stDtFim      := '31/12/' || stExercicio||'';
            stDtIniRetro := '01/01/' || stExercicio||'';
            stCpA2       := 'vl_quad_3';
            stExercicioRestos := stExercicio;

        END IF;

    ELSE

        IF (inPeriodo = 1) THEN
            stDtIni      := '01/01/' || stExercicio||'';
            stDtFim      := '30/06/' || stExercicio||'';
            stDtIniRetro := '01/07/' || CAST((CAST(stExercicio AS INTEGER)-1) AS VARCHAR);
            stCpA2       := 'vl_quad_1';
            stExercicioRestos := CAST((CAST(stExercicio AS INTEGER)-1) AS VARCHAR);

        ELSE
            stDtIni      := '01/01/' || stExercicio||'';
            stDtFim      := '31/12/' || stExercicio||'';
            stDtIniRetro := '01/01/' || stExercicio||'';
            stCpA2       := 'vl_quad_2';
            stExercicioRestos := stExercicio;

        END IF;
        
    END IF;

    -- Receita Corrente Líquida (RCL)
    -- Valor utilizado para calcular as porcentagens e limites

    stSQL := '  SELECT (SUM(rcl.total_mes_12)
                      + SUM(rcl.total_mes_11)
                      + SUM(rcl.total_mes_10)
                      + SUM(rcl.total_mes_9)
                      + SUM(rcl.total_mes_8)
                      + SUM(rcl.total_mes_7)
                      + SUM(rcl.total_mes_6)
                      + SUM(rcl.total_mes_5)
                      + SUM(rcl.total_mes_4)
                      + SUM(rcl.total_mes_3)
                      + SUM(rcl.total_mes_2)
                      + SUM(rcl.total_mes_1)) AS vl_rcl
                  FROM ( SELECT ordem
                              , cod_conta
                              , nom_conta
                              , cod_estrutural
                              , mes_1
                              , mes_2
                              , mes_3
                              , mes_4
                              , mes_5
                              , mes_6
                              , mes_7
                              , mes_8
                              , mes_9
                              , mes_10
                              , mes_11
                              , mes_12
                              , CAST( ( mes_1 + mes_2 + mes_3 + mes_4 + mes_5 + mes_6 + mes_7 + mes_8 + mes_9 + mes_10 + mes_11 + mes_12 ) AS NUMERIC(14,2)) AS total
                              , total_mes_1
                              , total_mes_2
                              , total_mes_3
                              , total_mes_4
                              , total_mes_5
                              , total_mes_6
                              , total_mes_7
                              , total_mes_8
                              , total_mes_9
                              , total_mes_10
                              , total_mes_11
                              , total_mes_12
                              , ( total_mes_1 + total_mes_2 + total_mes_3 + total_mes_4 + total_mes_5 + total_mes_6 + total_mes_7 + total_mes_8 + total_mes_9 + total_mes_10 + total_mes_11 + total_mes_12 ) AS total_geral 
                           FROM stn.pl_total_subcontas_entidade ('''||stDtFim||''', '''||stEntidades||''') AS retorno 
                                (  ordem          INTEGER
                                 , nivel          INTEGER
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
                                 , total_mes_12   NUMERIC )) AS rcl ';

    OPEN crCursor FOR EXECUTE stSQL;
        FETCH crCursor INTO nuRCL;
    CLOSE crCursor;

    IF (nuRCL IS NULL) THEN 
        nuRCL := 0.00;
    END IF;

    -- --------------------------------------
    -- Inicio das Tabelas Temporarias
    -- --------------------------------------

    stSQL := '
    CREATE TEMPORARY TABLE tmp_rgf_retorno (
        grupo       INTEGER NOT NULL,
        linha       INTEGER NOT NULL,
        descricao   VARCHAR(120) NOT NULL,
        valor       NUMERIC(14,2) DEFAULT NULL,
        pct_rcl     NUMERIC(14,2) DEFAULT NULL 
    )';

    EXECUTE stSQL;

    stSQL := ' INSERT INTO tmp_rgf_retorno VALUES (0, 0, ''RECEITA CORRENTE LÍQUIDA'', NULL, NULL); ';

    stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (0, 1, ''Receita Corrente líquida'', ' || nuRCL || '); ';

    EXECUTE stSQL;

    -- Despesa Total com Pessoal (DTP)
    -- Este Valor deve ser igual ao encontrado no RGF Anexo I
    -- (SUM(COALESCE(liquidado, 0.00)) + SUM(COALESCE(restos, 0.00))) AS vl_dtp 
    IF stExercicio > '2012' THEN 
        stSQL := '
        CREATE TEMPORARY TABLE tmp_rgf7_dtp AS (
            SELECT *
              FROM stn.fn_rgf_anexo1_despesas_novo ( ''' || stExercicio || ''', ''' || stDtIniRetro || ''', ''' || stDtFim || ''', ''' || stExercicioRestos || ''' , ''' || stEntidades || ''' ) 
              as retorno (
                      nivel               INTEGER,
                      cod_estrutural      VARCHAR,
                      descricao           VARCHAR,
                      liquidado_mes1      NUMERIC,
                      liquidado_mes2      NUMERIC,
                      liquidado_mes3      NUMERIC,
                      liquidado_mes4      NUMERIC,
                      liquidado_mes5      NUMERIC,
                      liquidado_mes6      NUMERIC,
                      liquidado_mes7      NUMERIC,
                      liquidado_mes8      NUMERIC,
                      liquidado_mes9      NUMERIC,
                      liquidado_mes10     NUMERIC,
                      liquidado_mes11     NUMERIC,
                      liquidado_mes12     NUMERIC,
                      liquidado           NUMERIC,
                      restos              NUMERIC
              )
        )';
    ELSE
        stSQL := '
        CREATE TEMPORARY TABLE tmp_rgf7_dtp AS (
        SELECT
            *
        FROM
            stn.fn_rgf_anexo1_despesas ( ''' || stExercicio || ''', ''' || stDtIniRetro || ''', ''' || stDtFim || ''', ''' || stEntidades || ''')
        AS retorno (
            nivel           INTEGER,
            cod_estrutural  VARCHAR,
            descricao       VARCHAR,
            liquidado       NUMERIC,
            restos          NUMERIC
        )
        WHERE
            nivel = 1
        )';
    END IF;

    EXECUTE stSQL;

    IF stExercicio > '2012' THEN 
        stSQL := '
        SELECT
            liq1 - ded1
        FROM
            (SELECT
                (SUM(liquidado)+SUM(restos)) AS liq1
            FROM
                tmp_rgf7_dtp
            WHERE
                cod_estrutural = ''3.1.0.0.00.00.00.00.00'') AS l,
            (SELECT
                (SUM(liquidado)+SUM(restos)) AS ded1
            FROM
                tmp_rgf7_dtp
            WHERE
                cod_estrutural = ''3.2.0.0.00.00.00.00.00'') AS d
        ';
    ELSE
        stSQL := '
        SELECT
            liq1 - ded1
        FROM
            (SELECT
                (SUM(liquidado)-SUM(restos)) AS liq1
            FROM
                tmp_rgf7_dtp
            WHERE
                cod_estrutural = ''3.1.0.0.00.00.00.00.00'') AS l,
            (SELECT
                (SUM(liquidado)-SUM(restos)) AS ded1
            FROM
                tmp_rgf7_dtp
            WHERE
                cod_estrutural = ''3.2.0.0.00.00.00.00.00'') AS d
        ';
    END IF;

    OPEN crCursor FOR EXECUTE stSQL;
        FETCH crCursor INTO nuDTP;
    CLOSE crCursor;

    IF (nuDTP IS NULL) THEN 
        nuDTP := 0.00;
    END IF;

    stSQL := ' INSERT INTO tmp_rgf_retorno VALUES (1, -1, '''', NULL, NULL); ';

    stSQL := stSQL || ' INSERT INTO tmp_rgf_retorno VALUES (1, 0, ''DESPESA COM PESSOAL'', NULL, NULL); ';
    IF (nuRCL = 0) THEN
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (1, 1, ''Despesa Total com Pessoal - DTP'', ' || nuDTP || ', ' || (nuDTP*100) || '); ';
    ELSE
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (1, 1, ''Despesa Total com Pessoal - DTP'', ' || nuDTP || ', ' || (nuDTP/nuRCL*100) || '); ';
    END IF;
    stSQL := stSQL || '
    INSERT INTO tmp_rgf_retorno VALUES (1, 2, ''Limite Máximo (incisos I, II e III, art. 20 da LRF)'', ' || (nuRCL * nuLimMaxDTP) || ', ' || (nuLimMaxDTP * 100) || '); 
    INSERT INTO tmp_rgf_retorno VALUES (1, 3, ''Limite Prudencial (parágrafo único, art. 22 da LRF)'', ' || (nuRCL * nuLimPruDTP) || ', ' || (nuLimPruDTP * 100) || ');
    ';

    EXECUTE stSQL;

    -- Dívida Consolidada Líquida (DCL)
    -- Este Valor deve ser igual ao encontrado no RGF Anexo II

    IF stExercicio > '''2012''' THEN
        stSQL := '
        SELECT COALESCE(' || stCpA2 || ', 0.00) AS vl
        FROM stn.fn_rgf_anexo2_novo(''' || stExercicio || ''', ''' || stTipoPeriodo || ''', ' || inPeriodo || ', ''' || stEntidades ||''') AS tbl (
            descr varchar,
            ordem integer,
            vl_ex_ant numeric,
            vl_quad_1 numeric,
            vl_quad_2 numeric,
            vl_quad_3 numeric,
            nivel integer
            )
        WHERE
            ordem = 12
        ';
    ELSE
        stSQL := '
        SELECT COALESCE(' || stCpA2 || ', 0.00) AS vl
        FROM stn.fn_rgf_anexo2(''' || stExercicio || ''', ''' || stTipoPeriodo || ''', ' || inPeriodo || ', ''' || stEntidades ||''') AS tbl (
            descr varchar,
            ordem integer,
            vl_ex_ant numeric,
            vl_quad_1 numeric,
            vl_quad_2 numeric,
            vl_quad_3 numeric,
            nivel integer
            )
        WHERE
            ordem = 23
        ';
    END IF;

    OPEN crCursor FOR EXECUTE stSQL;
        FETCH crCursor INTO nuDCL;
    CLOSE crCursor;

    IF (nuDCL IS NULL) THEN 
        nuDCL := 0.00;
    END IF;

    stSQL := '
    INSERT INTO tmp_rgf_retorno VALUES (2, -1, '''', NULL, NULL);
    INSERT INTO tmp_rgf_retorno VALUES (2, 0, ''DÍVIDA CONSOLIDADA'', NULL, NULL); ';

    IF (nuRCL = 0) THEN
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (2, 1, ''Dívida Consolidada Líquida'', ' || nuDCL || ', ' || nuDCL*100 || ');';
    ELSE
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (2, 1, ''Dívida Consolidada Líquida'', ' || nuDCL || ', ' || (nuDCL/nuRCL)*100 || ');';
    END IF;

    stSQL := stSQL || '
    INSERT INTO tmp_rgf_retorno VALUES (2, 2, ''Limite Definido por Resolução do Senado Federal'', ' || (nuRCL * nuLimDCL) || ', ' || (nuLimDCL*100) || ');
    ';

    EXECUTE stSQL;

    -- Garantias de Valores (GV)
    -- Este Valor deve ser igual ao encontrado no RGF Anexo III

    stSQL := '
    SELECT
        (
        sld_ext_aval +
        sld_ext_out +
        sld_int_aval +
        sld_int_out +
        sld_rec_ext_aval +
        sld_rec_int_aval
        ) AS soma_garantias 
    FROM (

        SELECT 
            -- GARANTIAS CONCEDIDAS EXTERNAS AVAL
            COALESCE(
            stn.fn_rgf_calcula_saldo_garantias_anexo3
            (
            '|| quote_literal(stExercicio) ||'
            ,'|| quote_literal(stEntidades) ||'
            ,''and pc.cod_estrutural in (''''1.9.9.5.1.02.02.00.00.00'''', ''''1.9.9.5.2.01.02.00.00.00'''') ''
            ,'|| quote_literal(stDtIniEx) ||'
            ,'|| quote_literal(stDtFim) ||'
            ),0.00) AS sld_ext_aval
            -- GARANTIAS CONCEDIDAS EXTERNAS OUTRO
            ,COALESCE(
            stn.fn_rgf_calcula_saldo_garantias_anexo3
            (
            '|| quote_literal(stExercicio) ||'
            ,'|| quote_literal(stEntidades) ||'
            ,''and pc.cod_estrutural in (''''1.9.9.7.0.00.00.00.00.00'''')''
            ,'|| quote_literal(stDtIniEx) ||'
            ,'|| quote_literal(stDtFim) ||'
            ),0.00) AS sld_ext_out
            -- GARANTIAS CONCEDIDAS INTERNAS AVAL
            ,COALESCE(
            stn.fn_rgf_calcula_saldo_garantias_anexo3
            (
            '|| quote_literal(stExercicio) ||'
            ,'|| quote_literal(stEntidades) ||'
            ,''and pc.cod_estrutural in (''''1.9.9.5.1.02.00.00.00.00'''',''''1.9.9.5.1.02.01.00.00.00'''',''''1.9.9.5.2.00.00.00.00.00'''',''''1.9.9.5.2.01.00.00.00.00'''',''''1.9.9.5.2.01.01.00.00.00'''')''
            ,'|| quote_literal(stDtIniEx) ||'
            ,'|| quote_literal(stDtFim) ||'
            ),0.00) AS sld_int_aval
            -- GARANTIAS CONCEDIDAS INTERNAS OUTRO
            ,COALESCE(
            stn.fn_rgf_calcula_saldo_garantias_anexo3
            (
            '|| quote_literal(stExercicio) ||'
            ,'|| quote_literal(stEntidades) ||'
            ,''and pc.cod_estrutural in (''''1.9.9.5.9.00.00.00.00.00'''') ''
            ,'|| quote_literal(stDtIniEx) ||'
            ,'|| quote_literal(stDtFim) ||'
            ),0.00) as sld_int_out
            -- CONTRAGARANTIAS RECEBIDAS EXTERNAS AVAL
            ,COALESCE(
            stn.fn_rgf_calcula_saldo_garantias_anexo3
            (
            '|| quote_literal(stExercicio) ||'
            ,'|| quote_literal(stEntidades) ||'
            ,''and pc.cod_estrutural in (''''1.9.9.5.6.01.02.00.00.00'''',''''1.9.9.5.6.02.02.00.00.00'''')''
            ,'|| quote_literal(stDtIniEx) ||'
            ,'|| quote_literal(stDtFim) ||'
            ),0.00) AS sld_rec_ext_aval
            -- CONTRAGARANTIAS RECEBIDAS EXTERNAS OUTRO
            ,COALESCE(
            stn.fn_rgf_calcula_saldo_garantias_anexo3
            (
            '|| quote_literal(stExercicio) ||'
            ,'|| quote_literal(stEntidades) ||'
            ,''and pc.cod_estrutural in (''''1.9.9.6.9.00.00.00.00.00'''')''
            ,'|| quote_literal(stDtIniEx) ||'
            ,'|| quote_literal(stDtFim) ||'
            ),0.00) AS sld_rec_ext_out
            -- CONTRAGARANTIAS RECEBIDAS INTERNAS AVAL
            ,COALESCE(
            stn.fn_rgf_calcula_saldo_garantias_anexo3
            (
            '|| quote_literal(stExercicio) ||'
            ,'|| quote_literal(stEntidades) ||'
            ,''and pc.cod_estrutural in (''''1.9.9.5.6.00.00.00.00.00'''',''''1.9.9.5.6.01.00.00.00.00'''',''''1.9.9.5.6.01.01.00.00.00'''',''''1.9.9.5.6.02.00.00.00.00'''',''''1.9.9.5.6.02.01.00.00.00'''') ''
            ,'|| quote_literal(stDtIniEx) ||'
            ,'|| quote_literal(stDtFim) ||'
            ),0.00) AS sld_rec_int_aval
    ) AS tbl
    ';

    OPEN crCursor FOR EXECUTE stSQL;
        FETCH crCursor INTO nuGV;
    CLOSE crCursor;

    stSQL := '
    INSERT INTO tmp_rgf_retorno VALUES (3, -1, '''', NULL, NULL);
    INSERT INTO tmp_rgf_retorno VALUES (3, 0, ''GARANTIAS DE VALORES'', NULL, NULL); ';

    IF (nuRCL = 0) THEN
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (3, 1, ''Total das Garantias de Valores'', ' || nuGV || ', ' || nuGV*100 || ');';
    ELSE
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (3, 1, ''Total das Garantias de Valores'', ' || nuGV || ', ' || (nuGV/nuRCL)*100 || ');';
    END IF;

    stSQL := stSQL || '
    INSERT INTO tmp_rgf_retorno VALUES (3, 2, ''Limite Definido por Resolução do Senado Federal'', ' || (nuRCL * nuLimGV) || ', ' || (nuLimGV*100) || ');
    ';

    EXECUTE stSQL;

    -- Operações de Crédito
    -- Este valor deve ser igual ao encontrado no RGF Anexo IV

    stSQL := '
    SELECT
        valor
    FROM (
        SELECT
            *
        FROM
            stn.fn_rgf_anexo4( ''' || stExercicio || ''', ' || inPeriodo || ', ''' || stEntidades || ''')
        AS ( nivel integer
            ,item  varchar
            ,valor numeric
            ,linha char 
           )
    ) AS tba4
    WHERE
        item LIKE ''OPERAÇÕES DE CRÉDITO (I)'' AND
        nivel = 1
    ';

    OPEN crCursor FOR EXECUTE stSQL;
        FETCH crCursor INTO nuOCEI;
    CLOSE crCursor;

    IF (nuOCEI IS NULL) THEN
        nuOCEI := 0.00;
    END IF;

    stSQL := '
    SELECT
        valor
    FROM (
        SELECT
            *
        FROM
            stn.fn_rgf_anexo4( ''' || stExercicio || ''', ' || inPeriodo || ', ''' || stEntidades || ''')
        AS ( nivel integer
            ,item  varchar
            ,valor numeric
            ,linha char 
           )
    ) AS tba4
    WHERE
        item LIKE ''POR ANTECIPAÇÃO DA RECEITA (II)'' AND 
        nivel = 1 
    ';

    OPEN crCursor FOR EXECUTE stSQL;
        FETCH crCursor INTO nuOCAR;
    CLOSE crCursor;

    IF (nuOCAR IS NULL) THEN
        nuOCAR := 0.00;
    END IF;

    stSQL := '
    INSERT INTO tmp_rgf_retorno VALUES (4, -1, '''', NULL, NULL);
    INSERT INTO tmp_rgf_retorno VALUES (4, 0, ''OPERAÇÕES DE CRÉDITO'', NULL, NULL);';

    IF (nuRCL = 0) THEN
    -- Por enquanto os valores a seguir não tem no sistema os códigos estruturais então os valores ficarão 0,00 como registrado no RGF ANEXO 4
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (4, 1, ''Operações de Crédito Externas e Internas'', 0.00, 0.00 );';
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (4, 2, ''Operações de Crédito por Antecipação da Receita'', ' || nuOCAR || ', ' || nuOCAR*100 || ' );';
    ELSE
    -- Por enquanto os valores a seguir não tem no sistema os códigos estruturais então os valores ficarão 0,00 omo registrado no RGF ANEXO 4
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (4, 1, ''Operações de Crédito Externas e Internas'', 0.00 , 0.00 );';
        stSQL := stSQL || 'INSERT INTO tmp_rgf_retorno VALUES (4, 2, ''Operações de Crédito por Antecipação da Receita'', ' || nuOCAR || ', ' || (nuOCAR/nuRCL)*100 || ' );';
    END IF;

    stSQL := stSQL || '
    INSERT INTO tmp_rgf_retorno VALUES (4, 3, ''Limite definido pelo Senado Federal para Operações de Crédito Externas e Internas'', ' || (nuRCL * nuLimOCEI) || ', ' || (nuLimOCEI*100) || ');
    INSERT INTO tmp_rgf_retorno VALUES (4, 4, ''Limite definido pelo Senado Federal para Operações de Crédito por Antecipação da Receita'', ' || (nuRCL * nuLimOCAR) || ', ' || (nuLimOCAR*100) || ');
    --INSERT INTO tmp_rgf_retorno VALUES (4, 5, '''', NULL, NULL);
    ';

    EXECUTE stSQL;

    -- Restos a Pagar
    -- Este valor deve ser igual ao encontrado no RGF Anexo VI
    -- Deve ser incluído neste Relatório somente no Último Quadrimestre

    IF (SUBSTRING(stDtFim, 1, 5) = '31/12') THEN

        stSQL := '
        INSERT INTO tmp_rgf_retorno VALUES (5, -1, '''', NULL, NULL);
        INSERT INTO tmp_rgf_retorno VALUES (5, 0, ''RESTOS A PAGAR'', NULL, NULL);
        INSERT INTO tmp_rgf_retorno VALUES (5, 1, ''Valor apurado nos Demonstrativos respectivos'', NULL, NULL);
        INSERT INTO tmp_rgf_retorno VALUES (5, 2, '''', NULL, NULL);
        ';

        EXECUTE stSQL;

    END IF;

    -- --------------------------------------
    -- Fim das Tabelas Temporarias
    -- --------------------------------------

    -- --------------------------------------
    -- Select de Retorno
    -- --------------------------------------

    stSQL := 'SELECT * FROM tmp_rgf_retorno ORDER BY grupo, linha, descricao ';

    FOR reReg IN EXECUTE stSQL
    LOOP
        RETURN NEXT reReg;
    END LOOP;

    DROP TABLE tmp_rgf7_dtp;
    DROP TABLE tmp_rgf_retorno;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

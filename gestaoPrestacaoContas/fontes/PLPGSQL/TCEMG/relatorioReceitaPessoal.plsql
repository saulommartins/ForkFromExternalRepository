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
CREATE OR REPLACE FUNCTION tcemg.relatorio_receita_pessoal ( varchar, varchar, varchar,varchar ) RETURNS SETOF RECORD AS $$

DECLARE
    stDataInicial           ALIAS FOR $1;
    stDataFinal             ALIAS FOR $2;
    stExercicio             ALIAS FOR $3;
    stEntidade            ALIAS FOR $4;  
    reRegistro              RECORD;
    reRegistroAux           RECORD;
    stSql                   VARCHAR :='';
    stSqlAux                VARCHAR :='';
    
    inAno       INTEGER;
    inMes       INTEGER;
    inExercicio INTEGER;
    i           INTEGER;
    
    nuVlMensalReceita      NUMERIC := 0.00;
    nuVlTotalMensalReceita NUMERIC := 0.00;
    
    stMes       VARCHAR;
    arDatas     VARCHAR[];
    
    
BEGIN
        stSql := 'CREATE TEMPORARY TABLE tmp_balancete_receita AS (
                    SELECT *
                    
                    FROM orcamento.fn_balancete_receita('|| quote_literal(stExercicio) ||',  
                                                        '''',
                                                        '|| quote_literal(stDataInicial) ||',
                                                        '|| quote_literal(stDataFinal) ||',
                                                        ''1,2,3'',
                                                        '''','''','''','''','''','''',''''
                                                        ) AS retorno (cod_estrutural      VARCHAR,                                           
                                                                      receita             INTEGER,                                           
                                                                      recurso             VARCHAR,                                           
                                                                      descricao           VARCHAR,                                           
                                                                      valor_previsto      NUMERIC,                                           
                                                                      arrecadado_periodo  NUMERIC,                                           
                                                                      arrecadado_ano      NUMERIC,                                           
                                                                      diferenca           NUMERIC
                                                                    )
                    ORDER BY cod_estrutural
                )';
                
        EXECUTE stSql;

       stSql := 'CREATE TEMPORARY TABLE tmp_balancete_receita_corrente AS (
                    SELECT *
                    
                    FROM orcamento.fn_balancete_receita('|| quote_literal(stExercicio) ||',  
                                                        '''',
                                                        '|| quote_literal(stDataInicial) ||',
                                                        '|| quote_literal(stDataFinal) ||',
                                                        ''1,2,3'',
                                                        '''','''','''','''','''','''',''''
                                                        ) AS retorno (cod_estrutural      VARCHAR,                                           
                                                                      receita             INTEGER,                                           
                                                                      recurso             VARCHAR,                                           
                                                                      descricao           VARCHAR,                                           
                                                                      valor_previsto      NUMERIC,                                           
                                                                      arrecadado_periodo  NUMERIC,                                           
                                                                      arrecadado_ano      NUMERIC,                                           
                                                                      diferenca           NUMERIC
                                                                    )
                    ORDER BY cod_estrutural
                )';

        EXECUTE stSql;

        stSql := ' CREATE TEMPORARY TABLE tmp_receitas AS (
        
                        
                        SELECT
                                ''01'' as cod_estrutural,
                                ''RECEITA CORRENTE DO MUNÍCIPIO'' AS descricao,
                                1 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo)*-1 FROM tmp_balancete_receita_corrente WHERE tmp_balancete_receita_corrente.cod_estrutural like ''1.0.0.0.00.00.00.00.00%''), 0.00) AS valor,
                                0.00 AS total
                                        
                        UNION
                        
                        SELECT
                                ''1.2.1.0.29.01.00.00.00'' cod_estrutural,
                                ''(-) 12102901 CONTRIBUIÇÃO PATRONAL DE SERVIDOR ATIVO'' AS descricao,
                                2 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo)*-1 FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''1.2.1.0.29.01.%''), 0.00) AS valor,
                                0.00 AS total
                                
                        UNION
                        
                        SELECT
                                ''1.2.1.0.29.07.00.00.00'' as cod_estrutural,
                                ''(-) 12102907 CONTRIBUIÇÃO DO SERVIDOR PATRONAL DE SERVIDOR ATIVO CIVIL'' AS descricao,
                                2 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo)*-1 FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''1.2.1.0.29.07%''), 0.00) AS valor,
                                0.00 AS total
                        
                        UNION

                             SELECT
                                ''1.2.1.0.29.09.00.00.00'' as cod_estrutural,
                                ''(-) 12102909 CONTRIBUIÇÃO DO SERVIDOR PATRONAL DE SERVIDOR INATIVO CIVIL'' AS descricao,
                                2 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo)*-1 FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''1.2.1.0.29.09%''), 0.00) AS valor,
                                0.00 AS total
                        
                        UNION

                            SELECT
                                ''1.2.1.0.29.11.00.00.00'' as cod_estrutural,
                                ''(-) 12102911 CONTRIBUIÇÃO DE PENSIONISTA CIVIL'' AS descricao,
                                2 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo)*-1 FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''1.2.1.0.29.11%''), 0.00) AS valor,
                                0.00 AS total
                        
                        UNION

                            SELECT
                                ''1.9.2.2.10.00.00.00.00'' as cod_estrutural,
                                ''(-) 19221000 COMPENSAÇÕES FINANCEIRAS ENTRE O REGIME'' AS descricao,
                                2 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo)*-1 FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''1.9.2.2.10.00%''), 0.00) AS valor,
                                0.00 AS total
                    )
        ';

        EXECUTE stSql;
        
        stSql := '
                SELECT
                
                retorno.cod_estrutural::VARCHAR AS cod_estrutural,
                retorno.descricao::VARCHAR AS descricao,
                retorno.nivel::INTEGER AS nivel,
                COALESCE(SUM(retorno.valor),0.00) AS valor,
                0.00 AS total
                
                FROM
                
                (
            
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.1.1.2.01.01.02%''), 0.00) AS valor
                            
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.1.02.05%''), 0.00) AS valor
                    
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.1.02.06%''), 0.00) AS valor
                    
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.1.01.05%''), 0.00) AS valor
                            
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.1.36.00%''), 0.00) AS valor
                            
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.2.01.01%''), 0.00) AS valor
                            
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.2.01.02%''), 0.00) AS valor
                            
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.2.01.04%''), 0.00) AS valor
                            
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.4%''), 0.00) AS valor
                            
                    UNION
                    
                    SELECT
                            ''9.9.9.9.9.99.99'' as cod_estrutural,
                            ''(-) DEDUÇÃO RECEITA PARA FORMAÇÃO FUNDEB'' AS descricao,
                            14 as nivel,
                            COALESCE((SELECT arrecadado_periodo FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.7.2.1.01.02%''), 0.00) AS valor
                ) as retorno
                
                GROUP BY cod_estrutural, descricao, nivel
                ORDER BY cod_estrutural
        ';
        
FOR reRegistroAux IN EXECUTE stSql
    LOOP
        stSqlAux := 'INSERT INTO tmp_receitas (cod_estrutural, descricao, nivel, valor) VALUES (' || quote_literal(reRegistroAux.cod_estrutural) || ',
                                                                                                ' || quote_literal(reRegistroAux.descricao) || ',
                                                                                                ' || reRegistroAux.nivel || ',
                                                                                                ' || reRegistroAux.valor || '
                                                                                        )
                    ';
        EXECUTE stSqlAux;
END LOOP;

stSql := 'UPDATE tmp_receitas SET total = (valor + (SELECT SUM(valor) FROM tmp_receitas WHERE cod_estrutural <> ''01'')) WHERE cod_estrutural = ''01'' ';
EXECUTE stSql;

    ---------------------------------------------------
    -- Buscando o valor gasto no exercicio anterior ---
    ---------------------------------------------------
    inAno :=  substr(stDataFinal, 7, 4 ) ;
    inMes :=  substr(stDataFinal, 4, 2 ) ; 
    
    inExercicio := inAno;
    
    i := 1;
    while i <= 12 loop
        if ( inMes < 10 ) then
            stMes := '0' || inMes;
        else
            stMes := inMes;
        end if;
    
        arDatas[i] := '01/' || stMes || '/'|| inAno;
    
        i := i +1;
        inMes := inMes -1;
        if ( inMes = 0 ) then
            inAno := inAno -1;
            inMes := 12;
        end if;
    end loop;

    i := 12;
    WHILE i >= 1 LOOP

        IF SUBSTR(arDatas[i],7,4)::INTEGER < 2014 THEN
            SELECT COALESCE(SUM(valor), 0) INTO nuVlMensalReceita FROM stn.receita_corrente_liquida WHERE mes = SUBSTR(arDatas[i],4,2)::INTEGER AND ano = SUBSTR(arDatas[i],7,4) AND cod_entidade IN (1,2,3);
        ELSE
            nuVlMensalReceita := 0.00;
        END IF;
        
        nuVlTotalMensalReceita := nuVlTotalMensalReceita + nuVlMensalReceita;
        
        i := i - 1;
    
    END LOOP;



    stSql := 'SELECT cod_estrutural
                   , descricao
                   , nivel
                   , COALESCE(valor,0.00) AS valor
                   , COALESCE(total,0.00) AS total
                   , 0.00 AS valor_ano_anterior
                FROM tmp_receitas ORDER BY nivel
    ';

FOR reRegistro IN EXECUTE stSql
    LOOP
        reRegistro.valor_ano_anterior := nuVlTotalMensalReceita;
        RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_balancete_receita;
DROP TABLE tmp_balancete_receita_corrente;
DROP TABLE tmp_receitas;

END;
$$ language 'plpgsql';

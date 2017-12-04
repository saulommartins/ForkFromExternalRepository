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
CREATE OR REPLACE FUNCTION tcemg.relatorio_anexo4_receita ( varchar, varchar, varchar ) RETURNS SETOF RECORD AS $$

DECLARE
    stDataInicial ALIAS FOR $1;
    stDataFinal   ALIAS FOR $2;
    stExercicio   ALIAS FOR $3;
    reRegistro    RECORD;
    reRegistroAux RECORD;
    stSql         VARCHAR :='';
    stSqlAux      VARCHAR :='';
BEGIN
        stSql := 'CREATE TEMPORARY TABLE tmp_balancete_receita AS (
                    SELECT *
                    
                    FROM orcamento.fn_balancete_receita('|| quote_literal(stExercicio) ||',  
                                                        '''',
                                                        '|| quote_literal(stDataInicial) ||',
                                                        '|| quote_literal(stDataFinal) ||',
                                                        ''1,3,2'',
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
                
       -- RAISE NOTICE '%', stSql;
                
        EXECUTE stSql;
        
        stSql := ' CREATE TEMPORARY TABLE tmp_receitas AS (
        
                        SELECT
                                ''01'' as cod_estrutural,
                                ''Receita Corrente do Município'' AS descricao,
                                1 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo)*-1 FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural  like ''1.0.0.0.00.00.00.00.00''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT (arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural  like ''1.0.0.0.00.00.00.00.00''), 0.00) AS receita_ate_per
                                        
                        UNION
                        
                        SELECT
                                ''7.0.0.0.0.00.00.00'' as cod_estrutural,   
                                ''(-) Receita Corrente Intraorçamentária'' AS descricao,
                                2 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural = ''7.0.0.0.00.00.00.00.00''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural = ''7.0.0.0.00.00.00.00.00''), 0.00) AS receita_ate_per
                                
                                
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.29.07.00'' as cod_estrutural,
                                ''(-) Contribuição do Servidor Ativo Civil para Regime Próprio'' AS descricao,
                                3 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.07%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.07%''), 0.00) AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.29.09.00'' as cod_estrutural,
                                ''(-) Contribuição do Servidor Inativo Civil para o Regime Próprio'' AS descricao,
                                4 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.09%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.09%''), 0.00) AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.29.11.00'' as cod_estrutural,
                                ''(-) Contribuição do Pensionista Civil para o Regime Próprio'' AS descricao,
                                5 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.11%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.11%''), 0.00) AS receita_ate_per
                                
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.99.01.00'' as cod_estrutural,
                                ''(-) Receita de Recolhimento da Contribuição do Servidor Ativo Civil oriunda do Pagamento de Sentenças Judiciais'' AS descricao,
                                6 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.99.01%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.99.01%''), 0.00) AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.99.01.00'' as cod_estrutural,
                                ''(-) Receita de Recolhimento da Contribuição do Servidor Inativo Civil oriunda do Pagamento de Sentenças Judiciais'' AS descricao,
                                7 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.99.01%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.99.01%''), 0.00) AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.99.01.00'' as cod_estrutural,
                                ''(-) Receita de Recolhimento da Contribuição do Servidor Inativo Civil oriunda do Pagamento de Sentenças Judiciais'' AS descricao,
                                8 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.99.01%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.99.01%''), 0.00) AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''4.1.9.2.2.10.01.00'' as cod_estrutural,
                                ''(-) Compensações Financeiras entre o Regime Geral e os Regimes Próprios de Previdências dos Servidores'' AS descricao,
                                9 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.9.2.2.10.01%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.9.2.2.10.01%''), 0.00) AS receita_ate_per
                                
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.29.00.00'' as cod_estrutural,
                                ''(-) Outras Receitas Correntes Dedutíveis'' AS descricao,
                                10 AS nivel,
                                0.00 AS valor,
                                0.00 AS total,
                                0.00 AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''4.1.2.1.0.29.01.00'' as cod_estrutural,
                                '' - Contribuição Patronal de Servidor Ativo Civil para o Regime Próprio'' AS descricao,
                                11 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.01%''), 0.00) AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''4.1.2.1.0.29.01%''), 0.00) AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''9.1.3.2.7.10.00'' as cod_estrutural,
                                ''(-) Deduções das Receitas (exceto FUNDEB)'' AS descricao,
                                12 AS nivel,
                                0.00 AS valor,
                                0.00 AS total,
                                0.00 AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''9.1.3.2.8.10.00'' as cod_estrutural,
                                '' - DEDUÇÃO Remuneração dos Investimentos do Regime Próprio de Previdência do Servidor em Renda Fixa'' AS descricao,
                                13 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.3.2.8.10%''), 0.00)*-1 AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.3.2.8.10%''), 0.00) AS receita_ate_per
                        
                        UNION
                        
                        SELECT
                                ''9.1.3.2.8.20.00'' as cod_estrutural,
                                '' - DEDUÇÃO Remuneração dos Investimentos do Regime Próprio de Previdência do Servidor em Renda Variável'' AS descricao,
                                14 AS nivel,
                                COALESCE((SELECT SUM(arrecadado_periodo) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.3.2.8.20%''), 0.00)*-1 AS valor,
                                0.00 AS total,
                                COALESCE((SELECT SUM(arrecadado_periodo-arrecadado_ano) FROM tmp_balancete_receita WHERE tmp_balancete_receita.cod_estrutural like ''9.1.3.2.8.20%''), 0.00) AS receita_ate_per
                    )
        ';
        
        EXECUTE stSql;
        
        stSql := 'SELECT cod_estrutural,
                         descricao,
                         nivel,
                         SUM(valor)*-1 AS valor,
                         SUM(receita_ate_per) AS receita_ate_per
                         FROM (      
                            SELECT ''9.9.9.9.9.99.99''::VARCHAR AS cod_estrutural,
                                   ''(-) Dedução da Receita para Formação do FUNDEB''::VARCHAR AS descricao,
                                   14 AS nivel,
                                   tmp_balancete_receita.arrecadado_periodo AS valor,
                                   (tmp_balancete_receita.arrecadado_periodo-tmp_balancete_receita.arrecadado_ano) AS receita_ate_per
                            FROM tmp_balancete_receita
                            
                            WHERE ( tmp_balancete_receita.cod_estrutural ILIKE ''9.1.7.2.1.01.02%''  OR
                                    tmp_balancete_receita.cod_estrutural ILIKE ''9.1.7.2.1.01.05%''  OR
                                    tmp_balancete_receita.cod_estrutural ILIKE ''9.1.7.2.1.36%''     OR
                                    tmp_balancete_receita.cod_estrutural ILIKE ''9.1.7.2.2.01.01%''  OR
                                    tmp_balancete_receita.cod_estrutural ILIKE ''9.1.7.2.2.01.02%''  OR
                                    tmp_balancete_receita.cod_estrutural ILIKE ''9.1.7.2.2.01.04%'' )
                            ) AS retorno
                        
                        GROUP BY cod_estrutural, descricao, nivel';
        
FOR reRegistroAux IN EXECUTE stSql
    LOOP
        stSqlAux := 'INSERT INTO tmp_receitas (cod_estrutural, descricao, nivel, valor, receita_ate_per) VALUES (' || quote_literal(reRegistroAux.cod_estrutural) || ',
                                                                                                ' || quote_literal(reRegistroAux.descricao) || ',
                                                                                                ' || reRegistroAux.nivel || ',
                                                                                                ' || reRegistroAux.valor || ',
                                                                                                ' || reRegistroAux.receita_ate_per || '
                                                                                        )
                    ';
        EXECUTE stSqlAux;
END LOOP;

stSql := 'UPDATE tmp_receitas SET total = (valor + (SELECT SUM(valor) FROM tmp_receitas WHERE cod_estrutural <> ''01'')) WHERE cod_estrutural = ''01'' ';
EXECUTE stSql;

stSql := 'SELECT cod_estrutural, descricao, nivel, COALESCE(valor,0.00) AS valor, COALESCE(total,0.00) AS total, COALESCE(receita_ate_per,0.00) AS receita_ate_per  FROM tmp_receitas ORDER BY nivel';

FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_balancete_receita;
DROP TABLE tmp_receitas;

END;
$$ language 'plpgsql';
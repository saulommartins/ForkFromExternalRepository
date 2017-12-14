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
    * Script de função PLPGSQL - Relatório STN - AMF - Demonstrativo VI
    * Data de Criação   : 16/07/2008


    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Henrique Girardi dos Santos
    
    * @package URBEM
    * @subpackage 

    * @ignore

    * Casos de uso : uc-06.01.36

    $Id: $
*/

CREATE OR REPLACE FUNCTION stn.fn_amf_demonstrativo6_saldo(varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    ALIAS FOR $1;
    stCodEntidades ALIAS FOR $2;
    stExercicio2   VARCHAR := '';
    stExercicio3   VARCHAR := '';
    stExercicio4   VARCHAR := '';
    stSql          VARCHAR := '';
    reRegistro     RECORD;
BEGIN
    
    stExercicio2 := trim(to_char((to_number(stExercicio, '99999')-2), '99999'));
    stExercicio3 := trim(to_char((to_number(stExercicio, '99999')-3), '99999'));
    stExercicio4 := trim(to_char((to_number(stExercicio, '99999')-4), '99999'));
    
    stSql := '
        CREATE TEMPORARY TABLE tmp_saldo_disponibilidade AS (
            SELECT 	CAST(plano_conta.cod_estrutural AS VARCHAR) as cod_estrutural
                ,	COALESCE((valor_debito.vl_lancamento), 0.00) - COALESCE((valor_credito.vl_lancamento), 0.00) AS vl_final
                ,   CAST(plano_conta.exercicio AS VARCHAR) as exercicio
            FROM
                ( 
                    SELECT 	plano_conta.cod_estrutural
                        ,	plano_conta.exercicio
                        ,	plano_analitica.cod_plano
                    FROM contabilidade.plano_conta
                    INNER JOIN 	contabilidade.plano_analitica
                                ON	plano_analitica.cod_conta = plano_conta.cod_conta
                                AND	plano_analitica.exercicio = plano_conta.exercicio
                                AND plano_conta.exercicio IN ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')
                    WHERE (plano_conta.cod_estrutural ilike ''1.1.1.1.1.%''
                             OR plano_conta.cod_estrutural ilike ''1.1.1.1.2.%''
                             OR plano_conta.cod_estrutural ilike ''1.4.1.%'')
                        AND plano_conta.exercicio IN ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')
                ) AS plano_conta
            
                LEFT JOIN	(
                        SELECT 	conta_debito.cod_plano
                            ,	conta_debito.exercicio
                            ,	COALESCE(SUM(valor_lancamento.vl_lancamento), 0.00) AS vl_lancamento
                        FROM 	contabilidade.conta_debito
                        INNER JOIN 	contabilidade.valor_lancamento
                                ON	conta_debito.exercicio		=	valor_lancamento.exercicio
                                AND	conta_debito.cod_entidade   =	valor_lancamento.cod_entidade
                                AND	conta_debito.tipo			=	valor_lancamento.tipo
                                AND	conta_debito.cod_lote		=	valor_lancamento.cod_lote
                                AND	conta_debito.sequencia		=	valor_lancamento.sequencia
                                AND	conta_debito.tipo_valor		=	valor_lancamento.tipo_valor
                        INNER JOIN 	contabilidade.lote
                                ON	valor_lancamento.exercicio		=	lote.exercicio
                                AND	valor_lancamento.cod_entidade   =	lote.cod_entidade
                                AND	valor_lancamento.tipo			=	lote.tipo
                                AND	valor_lancamento.cod_lote		=	lote.cod_lote
                        WHERE conta_debito.cod_entidade IN ('|| stCodEntidades ||')
                          AND conta_debito.exercicio IN ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')
                        GROUP BY	conta_debito.cod_plano
                                ,   conta_debito.exercicio
                ) AS valor_debito
                ON	valor_debito.cod_plano = plano_conta.cod_plano
                AND	valor_debito.exercicio = plano_conta.exercicio
            
                LEFT JOIN (			
                        SELECT 	conta_credito.cod_plano
                            ,	conta_credito.exercicio
                            ,	COALESCE(SUM(valor_lancamento.vl_lancamento), 0.00)*-1 AS vl_lancamento
                        FROM 	contabilidade.conta_credito
                        INNER JOIN 	contabilidade.valor_lancamento
                                ON	conta_credito.exercicio			=	valor_lancamento.exercicio
                                AND	conta_credito.cod_entidade  	=	valor_lancamento.cod_entidade
                                AND	conta_credito.tipo				=	valor_lancamento.tipo
                                AND	conta_credito.cod_lote			=	valor_lancamento.cod_lote
                                AND	conta_credito.sequencia			=	valor_lancamento.sequencia
                                AND	conta_credito.tipo_valor		=	valor_lancamento.tipo_valor
                        INNER JOIN 	contabilidade.lote
                                ON	valor_lancamento.exercicio		=	lote.exercicio
                                AND	valor_lancamento.cod_entidade   =	lote.cod_entidade
                                AND	valor_lancamento.tipo			=	lote.tipo
                                AND	valor_lancamento.cod_lote		=	lote.cod_lote
                                
                        WHERE conta_credito.cod_entidade IN ( '|| stCodEntidades ||' )
                          AND conta_credito.exercicio IN ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')
                                                    
                        GROUP BY	conta_credito.cod_plano
                                ,   conta_credito.exercicio      
                ) AS valor_credito
                ON	valor_credito.cod_plano = plano_conta.cod_plano
                AND	valor_credito.exercicio = plano_conta.exercicio
            
            ORDER BY plano_conta.cod_estrutural
        )
    ';
    
    EXECUTE stSql;
    
    stSql := '
        SELECT  COALESCE(SUM(valor2.vl_final), 0.00) AS vl_final_2
            ,   COALESCE(SUM(valor3.vl_final), 0.00) AS vl_final_3
            ,   COALESCE(SUM(valor4.vl_final), 0.00) AS vl_final_4
        FROM    tmp_saldo_disponibilidade AS valor2
            ,   tmp_saldo_disponibilidade AS valor3
            ,   tmp_saldo_disponibilidade AS valor4
        WHERE   valor2.exercicio = '|| quote_literal(stExercicio2) ||'
          AND   valor3.exercicio = '|| quote_literal(stExercicio3) ||'
          AND   valor4.exercicio = '|| quote_literal(stExercicio4) ||'
    ';
    

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
    DROP TABLE tmp_saldo_disponibilidade;
    
    RETURN;
END;
$$ language 'plpgsql';

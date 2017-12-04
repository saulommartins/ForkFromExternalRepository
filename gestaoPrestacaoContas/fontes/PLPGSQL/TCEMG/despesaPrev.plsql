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


CREATE OR REPLACE FUNCTION tcemg.fn_despesa_prev(varchar, varchar ,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;

    dtInicioAno             VARCHAR   := '';
    dtFimAno                VARCHAR   := '';
    stSql                   VARCHAR   := '';
    stSql1                  VARCHAR   := '';
    stMascClassReceita      VARCHAR   := '';
    stMascRecurso           VARCHAR   := '';
    stCodEstrutural         VARCHAR   := '';
    reRegistro              RECORD;

    arDatas varchar[] ;

BEGIN
     --saldo inicial + suplementacoes - reduções

    stSql := '  CREATE TEMPORARY TABLE tmp_despesa_administrativa AS 
                SELECT 
                        *
                        ,COALESCE( ((saldo_inicial + suplementacoes) - reducoes),0.00) as total_credito
                FROM orcamento.fn_consolidado_elem_despesa_sintetica('''||stExercicio||''','''','''||dtInicial||''','''||dtFinal||''','''||stCodEntidades||''','''','''','''','''','''','''', 4, 0) as retorno( 
                    classificacao   varchar,        
                    cod_reduzido    varchar,        
                    descricao       varchar,        
                    num_orgao       integer,        
                    nom_orgao       varchar,        
                    num_unidade     integer,        
                    nom_unidade     varchar,        
                    saldo_inicial   numeric,        
                    suplementacoes  numeric,        
                    reducoes        numeric,        
                    empenhado_mes   numeric,        
                    empenhado_ano   numeric,        
                    anulado_mes     numeric,        
                    anulado_ano     numeric,        
                    pago_mes        numeric,        
                    pago_ano        numeric,        
                    liquidado_mes   numeric,        
                    liquidado_ano   numeric,        
                    tipo_conta      varchar,        
                    nivel           integer         
                    )                                                                                                       
                ORDER BY classificacao 
            ';
    
    EXECUTE stSql;

    stSql := '  CREATE TEMPORARY TABLE tmp_despesa_previdencia AS 
                SELECT 
                        *
                        ,COALESCE( ((saldo_inicial + suplementacoes) - reducoes),0.00) as total_credito
                FROM orcamento.fn_consolidado_elem_despesa_sintetica('''||stExercicio||''','''','''||dtInicial||''','''||dtFinal||''','''||stCodEntidades||''','''','''','''','''','''','''', 9, 0) as retorno( 
                    classificacao   varchar,        
                    cod_reduzido    varchar,        
                    descricao       varchar,        
                    num_orgao       integer,        
                    nom_orgao       varchar,        
                    num_unidade     integer,        
                    nom_unidade     varchar,        
                    saldo_inicial   numeric,        
                    suplementacoes  numeric,        
                    reducoes        numeric,        
                    empenhado_mes   numeric,        
                    empenhado_ano   numeric,        
                    anulado_mes     numeric,        
                    anulado_ano     numeric,        
                    pago_mes        numeric,        
                    pago_ano        numeric,        
                    liquidado_mes   numeric,        
                    liquidado_ano   numeric,        
                    tipo_conta      varchar,        
                    nivel           integer         
                    )                                                                                                       
                ORDER BY classificacao 
            ';
    EXECUTE stSql;    

    stSql := '  CREATE TEMPORARY TABLE tmp_despesa_total AS 
                SELECT 
                        *
                        ,COALESCE( ((saldo_inicial + suplementacoes) - reducoes),0.00) as total_credito
                FROM orcamento.fn_consolidado_elem_despesa_sintetica('''||stExercicio||''','''','''||dtInicial||''','''||dtFinal||''','''||stCodEntidades||''','''','''','''','''','''','''', 0, 0) as retorno( 
                    classificacao   varchar,        
                    cod_reduzido    varchar,        
                    descricao       varchar,        
                    num_orgao       integer,        
                    nom_orgao       varchar,        
                    num_unidade     integer,        
                    nom_unidade     varchar,        
                    saldo_inicial   numeric,        
                    suplementacoes  numeric,        
                    reducoes        numeric,        
                    empenhado_mes   numeric,        
                    empenhado_ano   numeric,        
                    anulado_mes     numeric,        
                    anulado_ano     numeric,        
                    pago_mes        numeric,        
                    pago_ano        numeric,        
                    liquidado_mes   numeric,        
                    liquidado_ano   numeric,        
                    tipo_conta      varchar,        
                    nivel           integer         
                    )                                                                                                       
                ORDER BY classificacao 
            ';
    EXECUTE stSql;

    stSql := '
                --TIPO 01 SALDO INICIAL
                SELECT 
                        01 as codtipo
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_administrativa) as despAdmGeral
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_previdencia   ) as despPrevSoci                        
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.1.9.0.01%'' OR classificacao ILIKE ''3.1.9.0.03%'') as despPrevSocInatPens        
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3%'') as outrasDespCorrentes         
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''4.4%'' ) as despInvestimentos 
                        ,0.00 as despInversoesFinanceiras   
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_total WHERE classificacao ILIKE ''3.1.9.1.13%'' ) as despesasPrevIntra          
                        ,0.00 as despReserva          
                        ,0.00 as despOutrasReservas         
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_administrativa WHERE classificacao ILIKE ''3%'' ) as despCorrentes              
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_administrativa WHERE classificacao ILIKE ''4%'' ) as despCapital                
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3.9.0.05%'') as outrosBeneficios           
                        ,(SELECT SUM(saldo_inicial) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3.2.0.01.01%'' ) as contPrevidenciaria         
                        ,(  SELECT SUM(saldo_inicial) 
                            FROM tmp_despesa_previdencia 
                            WHERE classificacao NOT ILIKE ''3.1.9.0.01%'' 
                            AND classificacao NOT ILIKE   ''3.1.9.0.03%'' 
                            AND classificacao NOT ILIKE   ''3.3.9.0.05%''
                            AND classificacao NOT ILIKE   ''3.1.9.1.13%''  
                            AND classificacao NOT ILIKE   ''3.3.2.0.01.01%''  
                        ) as outrasDespesas
                
                UNION

                --TIPO 02 TOTAL CRÉDITO
                SELECT 
                        02 as codtipo
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_administrativa) as despAdmGeral
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_previdencia   ) as despPrevSoci                        
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.1.9.0.01%'' OR classificacao ILIKE ''3.1.9.0.03%'') as despPrevSocInatPens        
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3%'') as outrasDespCorrentes         
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''4.4%'' ) as despInvestimentos 
                        ,0.00 as despInversoesFinanceiras   
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_total WHERE classificacao ILIKE ''3.1.9.1.13%'' ) as despesasPrevIntra          
                        ,0.00 as despReserva          
                        ,0.00 as despOutrasReservas         
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_administrativa WHERE classificacao ILIKE ''3%'' ) as despCorrentes              
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_administrativa WHERE classificacao ILIKE ''4%'' ) as despCapital                
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3.9.0.05%'') as outrosBeneficios           
                        ,(SELECT SUM(total_credito) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3.2.0.01.01%'' ) as contPrevidenciaria         
                        ,(  SELECT SUM(total_credito) 
                            FROM tmp_despesa_previdencia 
                            WHERE classificacao NOT ILIKE ''3.1.9.0.01%'' 
                            AND classificacao NOT ILIKE   ''3.1.9.0.03%'' 
                            AND classificacao NOT ILIKE   ''3.3.9.0.05%''
                            AND classificacao NOT ILIKE   ''3.1.9.1.13%''  
                            AND classificacao NOT ILIKE   ''3.3.2.0.01.01%''  
                        ) as outrasDespesas

                UNION

                --TIPO 04 EMPENHADO NO MES
                SELECT 
                        04 as codtipo
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_administrativa) as despAdmGeral
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_previdencia   ) as despPrevSoci                        
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.1.9.0.01%'' OR classificacao ILIKE ''3.1.9.0.03%'') as despPrevSocInatPens        
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3%'') as outrasDespCorrentes         
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''4.4%'' ) as despInvestimentos 
                        ,0.00 as despInversoesFinanceiras   
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_total WHERE classificacao ILIKE ''3.1.9.1.13.00.00.00.00%'' ) as despesasPrevIntra          
                        ,0.00 as despReserva          
                        ,0.00 as despOutrasReservas         
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_administrativa WHERE classificacao ILIKE ''3%'' ) as despCorrentes              
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_administrativa WHERE classificacao ILIKE ''4%'' ) as despCapital                
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3.9.0.05%'') as outrosBeneficios           
                        ,(SELECT SUM(empenhado_mes) FROM tmp_despesa_previdencia WHERE classificacao ILIKE ''3.3.2.0.01.01%'' ) as contPrevidenciaria         
                        ,(  SELECT SUM(empenhado_mes) 
                            FROM tmp_despesa_previdencia 
                            WHERE classificacao NOT ILIKE ''3.1.9.0.01%'' 
                            AND classificacao NOT ILIKE   ''3.1.9.0.03%'' 
                            AND classificacao NOT ILIKE   ''3.3.9.0.05%''
                            AND classificacao NOT ILIKE   ''3.1.9.1.13%''  
                            AND classificacao NOT ILIKE   ''3.3.2.0.01.01%''  
                        ) as outrasDespesas
            ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_despesa_administrativa;
    DROP TABLE tmp_despesa_previdencia;
    DROP TABLE tmp_despesa_total;

    RETURN;
END;
$$ language 'plpgsql';


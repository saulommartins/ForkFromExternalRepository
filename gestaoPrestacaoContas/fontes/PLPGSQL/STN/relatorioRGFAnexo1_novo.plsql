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
    * Script de função PLPGSQL - Relatório STN - RGF - Anexo 1.
    * Data de Criação: 28/05/2008


    * @author Eduardo Paculski Schitz

    * Casos de uso:

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo1_despesas_novo ( varchar,varchar,varchar,varchar,varchar ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio    	    ALIAS FOR $1;
    dtInicial     	    ALIAS FOR $2;
    dtFinal      	    ALIAS FOR $3;
    stExercicioRestos 	ALIAS FOR $4;
    stCodEntidades 	    ALIAS FOR $5;
    
    reRegistro 		record ;
    reReg   		record ;
    stSql 		    varchar := '';
    stSqlAux    	varchar := '';
    stValor        	varchar := '';
    boRestos        boolean;   
    intI            integer;
    intMes          integer;
    dtInicioMes     varchar[];
    dtFimMes        varchar[];
    arMes           integer[];
    arAno           varchar[];
    boValorConfigurado BOOLEAN := false;
    stCodEstruturalExercicioAnterior varchar := '';

BEGIN

    boRestos = true;

    IF (dtFinal = '31/12/'||stExercicio) THEN
         SELECT valor INTO stValor
                     FROM administracao.configuracao 
                            WHERE parametro ilike '%virada%'
                            AND exercicio =  stExercicio   ;
    
        IF (stValor = 'T') THEN
            boRestos = true;
        END IF;
    END IF;
        
    dtInicioMes[1] := dtInicial;
    dtFimMes[1]    := to_char(to_date(''||dtInicial||'', 'dd/mm/yyyy') + interval '1 month' - interval '1 day','dd/mm/yyyy');
    
    intI    := 1;
    intMes  := 2;
    
    /*
    -------------ALTERACOES A PARTIR DE 2016----------------
        DEVE BUSCAR OS VALORES DO MES DE ACORDO COM A CONFIGURACAO
        Gestão Prestação de Contas :: STN :: Configuração :: Vincular Despesa Pessoal
        Logado em 2016 só poderá ser informado os periodos de 2015 em diante.
        Armazenas os meses já configurado e o ano de referencia da tabela.
    */
    SELECT  publico.concatenar_array(mes) as mes 
           ,publico.concatenar_array(ano) as ano
    INTO arMes 
        ,arAno
    FROM stn.despesa_pessoal 
    WHERE exercicio >= '2015' --exercicio de 2015 na qual os dados deveram ser informados quando o usuario estiver logado em 2016
    AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','));

    IF array_length(arMes,1) >= 1 THEN
        boValorConfigurado := TRUE;
    END IF;


    stSql := '
	
    CREATE TEMPORARY TABLE tmp_empenhos_restos AS (

        select SUBSTR(dotacao,24,14) as cod_estrutural
                        ,cod_entidade
                        ,empenho                                    
                        ,exercicio                              
                        ,cgm                                      
                        ,razao_social                             
                        ,vl_empenhado
                        ,vl_empenhado_pago                        
                        ,vl_liquidado                            
                        ,vl_anulado                             
                        ,vl_apagar                                
                        ,data_empenho                                
                        ,data_vencimento
        FROM empenho.fn_empenho_restos_pagar_credor(                  
                        '''||stExercicioRestos||''',           
                        '''',                
                        ''01/01/'||stExercicioRestos||''',          
                        '''||dtFinal||''',           
                        '''||stCodEntidades||''',              
                        '''',                 
                        '''',               
                        '''',               
                        '''',     
                        '''',       
                        '''',       
                        '''',                                              
                        '''',                
                        '''',             
                        ''1'',                 
                        ''99.99.99.999.9999.9999.99999999999999'',               
                        '''',                   
                        '''',     
                        ''''        
                    ) as retorno(                                          
                        dotacao varchar,                                 
                        cod_entidade integer,                            
                        empenho text,                                    
                        exercicio char(4),                               
                        cgm integer,                                     
                        razao_social varchar,                            
                        vl_empenhado numeric,                            
                        vl_empenhado_pago numeric,                       
                        vl_liquidado numeric,                            
                        vl_anulado numeric,                              
                        vl_apagar numeric,                               
                        data_empenho text,                               
                        data_vencimento text                             
                    ) 
        )';
    
    EXECUTE stSql;
    
    WHILE intI <= 12 LOOP  
        dtInicioMes[intMes] := to_char(to_date(''||dtInicioMes[intI]||'', 'dd/mm/yyyy') + interval '1 month','dd/mm/yyyy');
        dtFimMes[intMes]    := to_char(to_date(''||dtInicioMes[intMes]||'', 'dd/mm/yyyy') + interval '1 month' - interval '1 day','dd/mm/yyyy');      
        intI   := intI + 1;
        intMes := intMes + 1; 
    END LOOP;
       
    stSql := '
	
        CREATE TEMPORARY TABLE tmp_rgf_anexo1_despesa_liquida_mensal AS (

        SELECT 
            1 as ordem,
            1 as grupo,
            0 as subgrupo,
            CAST(''3.1.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
            CAST(''DESPESA BRUTA COM PESSOAL (I)'' as VARCHAR) as descricao,
            1 as nivel,
            0.00 as liquidado_mes1,
            0.00 as liquidado_mes2,
            0.00 as liquidado_mes3,
            0.00 as liquidado_mes4,
            0.00 as liquidado_mes5,                
            0.00 as liquidado_mes6,
            0.00 as liquidado_mes7,
            0.00 as liquidado_mes8,
            0.00 as liquidado_mes9,
            0.00 as liquidado_mes10,
            0.00 as liquidado_mes11,
            0.00 as liquidado_mes12,
            0.00 as liquidado,
            0.00 as restos

        UNION ALL

        SELECT
            2 as ordem,
            1 as grupo,
            1 as subgrupo,
            cast(''3.1.1.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Pessoal Ativo'' as varchar) as descricao ,
            2 as nivel , ';
        
        intMes := 1;
        WHILE intMes <= 12 LOOP
            --VERIFICA SE O DADO ENCONTRADO NA CONFIGURACAO É REFERENTE AO MESMO MES DO PERIODO
            IF (boValorConfigurado) AND ((SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1) THEN                
                stSql := stSql||' (SELECT valor_pessoal_ativo
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
            ELSE
                IF (SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1 THEN                    
                    stSql := stSql||' (SELECT COALESCE(SUM(valor), 0.00)
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
                ELSE
                    stSql := stSql||'
                    COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicioMes[intMes])||', '||quote_literal(dtFimMes[intMes])||', ' ||  quote_literal(stCodEntidades) ||', ' || quote_literal('(conta_despesa.cod_estrutural like ''3.1%'' and conta_despesa.cod_estrutural not like ''3.1.9.0.01%''
                                      and conta_despesa.cod_estrutural not like ''3.1.9.0.03%''
                                      and conta_despesa.cod_estrutural not like ''3.1.9.0.34%'')') || ' )), 0.00) as liquidado_mes'||intMes||',  ';
                    END IF;
            END IF;
            intMes := intMes + 1;
        END LOOP;      

        stSql := stSql||'
                        COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicial)||', '||quote_literal(dtFinal)||', ' ||  quote_literal(stCodEntidades) ||', ' || quote_literal('(conta_despesa.cod_estrutural like ''3.1%'' and conta_despesa.cod_estrutural not like ''3.1.9.0.01%''
                              and conta_despesa.cod_estrutural not like ''3.1.9.0.03%''
                              and conta_despesa.cod_estrutural not like ''3.1.9.0.34%'')') || ' )), 0.00) as liquidado,
                    ';
        
        if (boRestos = true) then
            stSql := stSql || '
            COALESCE((select sum(vl_apagar) from tmp_empenhos_restos where cod_estrutural like ''31%'' and cod_estrutural not like ''319001%''
                                                and cod_estrutural not like ''319003%''
                                               and cod_estrutural not like ''319034%''), 0.00) as restos ';
        else
          stSql := stSql || ' 0.00 as restos ';
        end if;

    stSql := stSql || '

        UNION ALL

        SELECT
            3 as ordem,
            1 as grupo,
            1 as subgrupo,
            cast(''3.1.2.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Pessoal Inativo e Pensionista'' as varchar) as descricao ,
            2 as nivel , ';
            
        intMes := 1;
        WHILE intMes <= 12 LOOP
            --VERIFICA SE O DADO ENCONTRADO NA CONFIGURACAO É REFERENTE AO MESMO MES DO PERIODO
            IF (boValorConfigurado) AND ((SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1) THEN
                stSql := stSql||' (SELECT valor_pessoal_inativo
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
                                          
            ELSE
                stSql := stSql||'
                    COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicioMes[intMes])||', '||quote_literal(dtFimMes[intMes])||', '||quote_literal(stCodEntidades)||', '|| quote_literal('(conta_despesa.cod_estrutural like  ''3.1.9.0.01%'' OR conta_despesa.cod_estrutural like ''3.1.9.0.03%'')') || ')), 0.00) as liquidado_mes'||intMes||',  ';  
            END IF;
            intMes := intMes + 1;
        END LOOP;  
            
        stSql := stSql || 'COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicial)||', '||quote_literal(dtFinal)||', '||quote_literal(stCodEntidades)||', '|| quote_literal('(conta_despesa.cod_estrutural like  ''3.1.9.0.01%'' OR conta_despesa.cod_estrutural like ''3.1.9.0.03%'')') || ')), 0.00) as liquidado, ';    
            
        if (boRestos = true) then
            stSql := stSql || '
                COALESCE((select sum(vl_apagar) from tmp_empenhos_restos where cod_estrutural like  ''319001%'' OR cod_estrutural like  ''319003%''), 0.00) as restos ';
        else
            stSql := stSql || ' 0.00 as restos ';
        end if;

    stSql := stSql || '

        UNION ALL

        SELECT
            4 as ordem,
            1 as grupo,
            1 as subgrupo,
            cast(''3.1.3.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Outras despesas de pessoal decorrentes de contratos de terceirização (§ 1º art. 18 LRF)'' as varchar) as descricao ,
            2 as nivel , ';
            
        intMes := 1;
        WHILE intMes <= 12 LOOP
            --VERIFICA SE O DADO ENCONTRADO NA CONFIGURACAO É REFERENTE AO MESMO MES DO PERIODO
            IF (boValorConfigurado) AND ((SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1) THEN
                stSql := stSql||' (SELECT valor_terceirizacao
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
                                          
            ELSE
                stSql := stSql||'
                    COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicioMes[intMes])||', '||quote_literal(dtFimMes[intMes])||', ' ||quote_literal(stCodEntidades)||', ' || quote_literal('(conta_despesa.cod_estrutural like  ''3.1.9.0.34%'') ') || ')), 0.00) as liquidado_mes'||intMes||',  ';
            END IF;
            intMes := intMes + 1;
        END LOOP;
        
        stSql := stSql || 'COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicial)||', '||quote_literal(dtFinal)||', ' ||quote_literal(stCodEntidades)||', ' || quote_literal('(conta_despesa.cod_estrutural like  ''3.1.9.0.34%'') ') || ')), 0.00) as liquidado, ';
            
        if (boRestos = true) then
            stSql := stSql || '
                COALESCE((select sum(vl_apagar) from tmp_empenhos_restos where cod_estrutural like ''319034%''), 0.00) as restos ';
        else
            stSql := stSql || ' 0.00 as restos ';
        end if;

    stSql := stSql || '

        UNION ALL

        SELECT
            5 as ordem,
            2 as grupo,
            0 as subgrupo,
            CAST(''3.2.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
            CAST(''DESPESA NÃO COMPUTADAS (§ 1º do art. 19 da LRF) (II)'' as VARCHAR) as descricao,
            1 as nivel,
            0.00 as liquidado_mes1,
            0.00 as liquidado_mes2,
            0.00 as liquidado_mes3,
            0.00 as liquidado_mes4,
            0.00 as liquidado_mes5,                
            0.00 as liquidado_mes6,
            0.00 as liquidado_mes7,
            0.00 as liquidado_mes8,
            0.00 as liquidado_mes9,
            0.00 as liquidado_mes10,
            0.00 as liquidado_mes11,
            0.00 as liquidado_mes12,
            0.00 as liquidado,
            0.00 as restos

        UNION ALL

        SELECT
            6 as ordem,
            2 as grupo,
            1 as subgrupo,
            cast(''3.2.1.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Indenizações por Demissão e Incentivos a Demissão Voluntária'' as varchar) as descricao ,
            2 as nivel , ';            
        
        intMes := 1;
        WHILE intMes <= 12 LOOP
            --VERIFICA SE O DADO ENCONTRADO NA CONFIGURACAO É REFERENTE AO MESMO MES DO PERIODO
            IF (boValorConfigurado) AND ((SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1) THEN
                stSql := stSql||' (SELECT valor_indenizacoes
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
            ELSE
                stSql := stSql||'
                    COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicioMes[intMes])||', '||quote_literal(dtFimMes[intMes])||', ' ||quote_literal(stCodEntidades)||', ' || quote_literal('(conta_despesa.cod_estrutural like  ''3.1.9.0.94%'') ') || ')), 0.00) as liquidado_mes'||intMes||',  ';            
            END IF;
            intMes := intMes + 1;    
        END LOOP;
        
        stSql := stSql||'COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicial)||', '||quote_literal(dtFinal)||', ' ||quote_literal(stCodEntidades)||', ' || quote_literal('(conta_despesa.cod_estrutural like  ''3.1.9.0.94%'') ') || ')), 0.00) as liquidado, ';
            
        if (boRestos = true) then
            stSql := stSql || '
                COALESCE((select sum(vl_apagar) from tmp_empenhos_restos where cod_estrutural like ''319094%''), 0.00) as restos ';
        else
            stSql := stSql || ' 0.00 as restos ';
        end if;

    stSql := stSql || '

        UNION ALL

        SELECT
            7 as ordem,
            2 as grupo,
            1 as subgrupo,
            cast(''3.2.2.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Decorrentes de Decisão Judicial'' as varchar) as descricao ,
            2 as nivel , ';            
            
        intMes := 1;
        WHILE intMes <= 12 LOOP
            --VERIFICA SE O DADO ENCONTRADO NA CONFIGURACAO É REFERENTE AO MESMO MES DO PERIODO
            IF (boValorConfigurado) AND ((SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1) THEN
                stSql := stSql||' (SELECT valor_decisao_judicial
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
                                          
                                        
            ELSE
                stSql := stSql||'
                    COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicioMes[intMes])||', '||quote_literal(dtFimMes[intMes])||', ' ||quote_literal(stCodEntidades)||', ' || quote_literal('(conta_despesa.cod_estrutural like ''3.1.9.0.91%'') ') || ')), 0.00) as liquidado_mes'||intMes||',  ';           
            END IF;
            intMes := intMes + 1;
        END LOOP;
        
        stSql := stSql || 'COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicial)||', '||quote_literal(dtFinal)||', ' ||quote_literal(stCodEntidades)||', ' || quote_literal('(conta_despesa.cod_estrutural like ''3.1.9.0.91%'') ') || ')), 0.00) as liquidado, ';
            
        if (boRestos = true) then
            stSql := stSql || '
                COALESCE((select sum(vl_apagar) from tmp_empenhos_restos where cod_estrutural like ''319091%''), 0.00) as restos ';
        else
            stSql := stSql || ' 0.00 as restos ';
        end if;
    
    
        -- ENCONTRA COD_ESTRUTURAL CONFIGURADO NA CONFIGURACAO DO ANEXO 1 STN
         SELECT configuracao.valor
           INTO stCodEstruturalExercicioAnterior
           FROM administracao.configuracao
          WHERE exercicio = '2016'
            AND parametro = 'stn_rgf1_despesas_exercicios_anteriores'
            AND cod_modulo = 36 ;
    
    stSql := stSql || '
        UNION ALL

        SELECT
            8 as ordem,
            2 as grupo,
            1 as subgrupo,
            cast(''3.2.3.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Despesas de Exercícios Anteriores'' as varchar) as descricao ,
            2 as nivel , ';

        intMes := 1;
        WHILE intMes <= 12 LOOP
            --VERIFICA SE O DADO ENCONTRADO NA CONFIGURACAO É REFERENTE AO MESMO MES DO PERIODO
            IF (boValorConfigurado) AND ((SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1) THEN
                stSql := stSql||' (SELECT valor_exercicios_anteriores
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
            ELSE
                IF stCodEstruturalExercicioAnterior IS NOT NULL OR stCodEstruturalExercicioAnterior != '' THEN
                    stSql := stSql||'COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicioMes[intMes])||', '||quote_literal(dtFimMes[intMes])||', ' ||quote_literal(stCodEntidades) ||', ' || quote_literal('(conta_despesa.cod_estrutural like '''||stCodEstruturalExercicioAnterior||''') ') || ')), 0.00) as liquidado_mes'||intMes||', ';
                ELSE
                    stSql := stSql||' 0.00 as liquidado_mes'||intMes||', ';
                END IF;
            END IF;
            intMes := intMes + 1;
        END LOOP;
        
        stSql := stSql || 'COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicial)||', '||quote_literal(dtFinal)||', ' ||quote_literal(stCodEntidades) ||', ' || quote_literal('(conta_despesa.cod_estrutural like '''||stCodEstruturalExercicioAnterior||''') ') || ')), 0.00) as liquidado, ';
            
        if (boRestos = true) then
            stSql := stSql || '
                COALESCE((select sum(vl_apagar) from tmp_empenhos_restos where cod_estrutural like  ''3.1.9.0.92%''), 0.00) as restos ';
        else
            stSql := stSql || ' 0.00 as restos ';
        end if;
    
    stSql := stSql || '

        UNION ALL

        SELECT
            9 as ordem,
            2 as grupo,
            1 as subgrupo,
            cast(''3.2.4.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Inativos e Pensionistas com Recursos Vinculados'' as varchar) as descricao ,
            2 as nivel, ';
        
        intMes := 1;
        WHILE intMes <= 12 LOOP
            --VERIFICA SE O DADO ENCONTRADO NA CONFIGURACAO É REFERENTE AO MESMO MES DO PERIODO
            IF (boValorConfigurado) AND ((SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes[intMes],4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes[intMes],7,4)||'' AND cod_entidade::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1) THEN
                stSql := stSql||' (SELECT valor_inativos_pensionistas
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes[intMes],4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes[intMes],7,4)||'''
                                          AND cod_entidade IN ('||stCodEntidades||'))   as liquidado_mes'||intMes||',  ';
            ELSE          
                -- SÓ EXECUTA BLOCO ABAIXO SE RPPS ESTIVER SELECIONADO
                IF (SELECT COUNT(*) FROM administracao.configuracao WHERE parametro = 'cod_entidade_rpps' AND exercicio = ''||stExercicio||'' AND valor::TEXT = ANY (string_to_array(stCodEntidades, ','))) >= 1 THEN
                    stSql := stSql||'
                        COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('||quote_literal(dtInicioMes[intMes])||', '||quote_literal(dtFimMes[intMes])||', '||quote_literal(stCodEntidades)||', '|| quote_literal('(conta_despesa.cod_estrutural like  ''3.1.9.0.01%'' OR conta_despesa.cod_estrutural like ''3.1.9.0.03%'')') || ')), 0.00) as liquidado_mes'||intMes||',  ';
                ELSE
                    stSql := stSql||' 0.00 as liquidado_mes'||intMes||', ';
                END IF;        
                        
            END IF;
            intMes := intMes + 1;
        END LOOP;

        stSql := stSql || ' 0.00 as liquidado, 0.00 as restos ) ';
	

	EXECUTE stSql;
    
    
    -------------------------------------------------
    -- Adiciona o valor da despesa pessoal vinculada
    -------------------------------------------------
    UPDATE tmp_rgf_anexo1_despesa_liquida_mensal
       SET liquidado = liquidado + (SELECT stn.fn_calcula_dp_vinculada(stExercicio,dtFinal,stCodEntidades))
     WHERE grupo = 1
       AND nivel = 2
       AND cod_estrutural = '3.1.1.0.00.00.00.00.00';
	
    /* 
        UPDATE PARA EXERCICIO DE 2016
    */
    IF stExercicio::varchar >= '2016' THEN
        stSql := 'SELECT DISTINCT cod_estrutural FROM tmp_rgf_anexo1_despesa_liquida_mensal ';
    
        FOR reReg IN EXECUTE stSql
        LOOP
            stSqlAux := 'UPDATE tmp_rgf_anexo1_despesa_liquida_mensal SET
                            liquidado = (SELECT 
                                                COALESCE(liquidado_mes1, 0.00)
                                                +COALESCE(liquidado_mes2, 0.00)
                                                +COALESCE(liquidado_mes3, 0.00)
                                                +COALESCE(liquidado_mes4, 0.00)
                                                +COALESCE(liquidado_mes5, 0.00)
                                                +COALESCE(liquidado_mes6, 0.00)
                                                +COALESCE(liquidado_mes7, 0.00)
                                                +COALESCE(liquidado_mes8, 0.00)
                                                +COALESCE(liquidado_mes9, 0.00)
                                                +COALESCE(liquidado_mes10, 0.00)
                                                +COALESCE(liquidado_mes11, 0.00)
                                                +COALESCE(liquidado_mes12, 0.00)
                                                FROM tmp_rgf_anexo1_despesa_liquida_mensal 
                                                WHERE cod_estrutural = '''||reReg.cod_estrutural||''' ) 
                        WHERE cod_estrutural = '''||reReg.cod_estrutural||''' ';
            EXECUTE stSqlAux;
        END LOOP;
    END IF;

    -- Calcular totais do nivel pai

    stSql := 'SELECT DISTINCT grupo FROM tmp_rgf_anexo1_despesa_liquida_mensal ';
    
    FOR reReg IN EXECUTE stSql
    LOOP
    
         stSqlAux := '
            UPDATE tmp_rgf_anexo1_despesa_liquida_mensal SET ';
            
        intMes := 1;
        WHILE intMes <= 12 LOOP
            stSqlAux := stSqlAux || '
                liquidado_mes'||intMes||' = (SELECT COALESCE(SUM(liquidado_mes'||intMes||'), 0.00) FROM tmp_rgf_anexo1_despesa_liquida_mensal WHERE grupo = ' || reReg.grupo || ' ), ';
            intMes := intMes + 1;    
        END LOOP; 
                
         stSqlAux := stSqlAux ||'
             liquidado = (SELECT COALESCE(SUM(liquidado), 0.00) FROM tmp_rgf_anexo1_despesa_liquida_mensal WHERE grupo = ' || reReg.grupo || ' AND nivel = 2),
             restos = (SELECT COALESCE(SUM(restos), 0.00) FROM tmp_rgf_anexo1_despesa_liquida_mensal WHERE grupo = ' || reReg.grupo || ' AND nivel = 2)
            WHERE
                grupo = ' || reReg.grupo || ' AND nivel = 1 ';     

        EXECUTE stSqlAux;
    END LOOP;

    
	stSql :='  SELECT 
                    nivel,
                    cod_estrutural,
                    descricao,
                    liquidado_mes1,
                    liquidado_mes2,
                    liquidado_mes3,
                    liquidado_mes4,
                    liquidado_mes5,                
                    liquidado_mes6,
                    liquidado_mes7,
                    liquidado_mes8,
                    liquidado_mes9,
                    liquidado_mes10,
                    liquidado_mes11,
                    liquidado_mes12,
                    liquidado,
                    restos
                FROM tmp_rgf_anexo1_despesa_liquida_mensal 
                
                ORDER BY ordem, grupo';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

	DROP TABLE tmp_rgf_anexo1_despesa_liquida_mensal ;
    DROP TABLE tmp_empenhos_restos ;

    RETURN;
 
END;
$$
language 'plpgsql';

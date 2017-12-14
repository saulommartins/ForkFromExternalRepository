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
CREATE OR REPLACE FUNCTION tcemg.sub_consulta_despesa_rcl_novo(varchar, varchar, varchar, varchar, varchar, integer) RETURNS SETOF RECORD AS $$
DECLARE
    dtInicial      ALIAS FOR $1;
    dtFinal        ALIAS FOR $2;
    stExercicio    ALIAS FOR $3;
    stEntidades    ALIAS FOR $4;
    cod_estrutural ALIAS FOR $5;
    inNivel        ALIAS FOR $6;

    intI                integer;
    i                   integer;
    inEntidades         integer[];
    dtInicioMes         varchar;
    dtFimMes            varchar;
    arDatas             varchar[];
    stSql               VARCHAR :='';
    stSqlAux            varchar := '';
    inAno               INTEGER;
    stMes               varchar;
    inMes               INTEGER;
    reRegistro          RECORD;
    reReg               RECORD;

BEGIN
    /*
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
    
    stDataIni :=  '01' || substr(dtData,3,8) ;
    stDataFim := dtData;
    
    stSql := ' select cast ( conta_despesa.cod_conta                      as varchar ) as cod_conta
                    , cast ( coalesce(  stn.tituloRCL( publico.fn_mascarareduzida(conta_despesa.cod_estrutural)) , conta_despesa.descricao ) as varchar ) as nom_conta
                    , cast ( conta_despesa.cod_estrutural                 as varchar ) as cod_estrutural ';
    
    i := 12;
    while i >= 1 loop
            stDataIni := arDatas[i];
            inDia := stn.calculaNrDiasAnoMes(  substr(stDataIni,7,4)::integer, substr(stDataIni,4,2)::integer );
            stDataFim := inDia ||  substr(stDataIni,3,8);
            
            IF inNivel = 3 AND cod_estrutural = '3.3.1' THEN
            stSql = stSql || '
            , CASE WHEN COALESCE( CAST( tcemg.fn_somatorio_balancete_despesa( publico.fn_mascarareduzida( conta_despesa.cod_estrutural), '''||stDataIni||''', '''||stDataFim||''') as numeric(14,2) ), 0) = 0 AND '||substr(stDataIni,7,4)::integer||' < 2014
                   THEN (SELECT COALESCE(SUM(valor), 0) FROM stn.despesa_pessoal WHERE mes = '||substr(stDataIni,4,2)::integer||' AND ano = '''||substr(stDataIni,7,4)::integer||''' AND cod_entidade IN ('||stEntidades||') )
                   ELSE COALESCE( CAST( tcemg.fn_somatorio_balancete_despesa( publico.fn_mascarareduzida( conta_despesa.cod_estrutural), '''||stDataIni||''', '''||stDataFim||''') as numeric(14,2) ), 0)
               END AS mes_'||i;
            ELSE
            stSql = stSql || '
            , COALESCE( CAST( tcemg.fn_somatorio_balancete_despesa( publico.fn_mascarareduzida( conta_despesa.cod_estrutural), '''||stDataIni||''', '''||stDataFim||''') as numeric(14,2) ), 0) AS mes_'||i ;
            END IF;
            
            i := i - 1;
    END LOOP;

    stSql := stSql ||'
                 FROM orcamento.conta_despesa
                WHERE conta_despesa.cod_estrutural LIKE ''' ||substr( cod_estrutural, 3,16)||'%'' 
                  AND publico.fn_nivel(conta_despesa.cod_estrutural) = ''' || inNivel-1 || ''' 
                  AND conta_despesa.exercicio = '''|| inExercicio ||'''
            ';
                                                        
    FOR reRegistro IN EXECUTE stSql
    LOOP
       RETURN next reRegistro;        
    END LOOP;
   
    
RETURN;

END;
*/
    inAno :=  substr(dtFinal, 7, 4 ) ;
    inMes :=  substr(dtFinal, 4, 2 ) ; 

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

    inEntidades := regexp_split_to_array(stEntidades,',');

    stSql := '
        CREATE TEMPORARY TABLE tmp_rgf_anexo1_despesa_liquida_mensal AS (

        SELECT
            cast ( conta_despesa.cod_conta                      as varchar ) as cod_conta,
            cast( coalesce(  stn.tituloRCL( publico.fn_mascarareduzida(conta_despesa.cod_estrutural)) , conta_despesa.descricao ) as varchar ) as descricao,
            cast ( conta_despesa.cod_estrutural                 as varchar ) as cod_estrutural,
            ';
        
        i := 1;
        inMes := 12;
        WHILE i <= 12 LOOP
            dtInicioMes := arDatas[inMes];
            dtFimMes    := to_char(to_date(''||arDatas[inMes]||'', 'dd/mm/yyyy') + interval '1 month' - interval '1 day','dd/mm/yyyy');      
            IF inNivel = 3 AND cod_estrutural = '3.3.1' THEN
                IF (SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = SUBSTR(dtInicioMes,4,2)::INTEGER AND ano = ''||SUBSTR(dtInicioMes,7,4)||'' AND cod_entidade IN ( inEntidades[0],inEntidades[1] ) ) >= 1 THEN
                    stSql := stSql||' (SELECT COALESCE(SUM(valor), 0.00)
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||SUBSTR(dtInicioMes,4,2)::INTEGER||'
                                          AND ano = '''||SUBSTR(dtInicioMes,7,4)||'''
                                          AND cod_entidade IN ('||stEntidades||'))   as liquidado_mes'||i||',  ';
                ELSE
                    stSql := stSql||'
                    COALESCE((select * from tcemg.fn_relatorio_demostrativo_rcl_despesa_liquidada('||quote_literal(dtInicioMes)||'
                                                                                                 ,'||quote_literal(dtFimMes)||'
                                                                                                 ,'||quote_literal(stEntidades) ||'
                                                                                                 ,'||quote_literal('(conta_despesa.cod_estrutural like ''' ||substr( cod_estrutural, 3,16)||'%'' )') || ' 
                    )), 0.00) as liquidado_mes'||i||',  ';
                END IF;
            ELSE
                stSql := stSql||'
                    COALESCE((select * from tcemg.fn_relatorio_demostrativo_rcl_despesa_liquidada('||quote_literal(dtInicioMes)||'
                                                                                                 ,'||quote_literal(dtFimMes)||'
                                                                                                 ,'||quote_literal(stEntidades) ||'
                                                                                                 ,'||quote_literal('(conta_despesa.cod_estrutural like ''' ||substr( cod_estrutural, 3,16)||'%'' )') || ' 
                    )), 0.00) as liquidado_mes'||i||',  ';
            END IF;
            i := i + 1;
            inMes  := inMes - 1;
        
        END LOOP;      

        stSql := stSql||'
                        COALESCE((select * from tcemg.fn_relatorio_demostrativo_rcl_despesa_liquidada('||quote_literal(dtInicial)||'
                                                                                    , '||quote_literal(dtFinal)||'
                                                                                    , ' ||  quote_literal(stEntidades) ||'
                                                                                    ,'|| quote_literal('(conta_despesa.cod_estrutural like ''' ||substr( cod_estrutural, 3,16)||'%'' )') || ' 
                        )), 0.00) as liquidado
                 
                FROM orcamento.conta_despesa
                WHERE conta_despesa.cod_estrutural LIKE ''' ||substr( cod_estrutural, 3,16)||'%'' 
                AND publico.fn_nivel(conta_despesa.cod_estrutural) = ''' || inNivel-1 || ''' 
                AND conta_despesa.exercicio = '''|| stExercicio ||'''
            )
            ';

    EXECUTE stSql;  

    stSql := 'SELECT 
                    cod_conta,
                    descricao,
                    cod_estrutural,
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
                    liquidado
     FROM tmp_rgf_anexo1_despesa_liquida_mensal ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_rgf_anexo1_despesa_liquida_mensal ;
        
    RETURN;
 
END;

$$ language 'plpgsql';

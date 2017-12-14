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

/*

CREATE TYPE tcemg.siace_despesa_total_pessoal AS (
    cod_conta           VARCHAR,
    descricao           VARCHAR,
    cod_estrutural      VARCHAR,
    valor               NUMERIC
);

*/

CREATE OR REPLACE FUNCTION tcemg.siace_despesa_total_pessoal(varchar, varchar, varchar, varchar, varchar, integer, varchar) RETURNS SETOF tcemg.siace_despesa_total_pessoal AS $$
DECLARE
    dtInicial      ALIAS FOR $1;
    dtFinal        ALIAS FOR $2;
    stExercicio    ALIAS FOR $3;
    stEntidades    ALIAS FOR $4;
    cod_estrutural ALIAS FOR $5;
    inNivel        ALIAS FOR $6;
    stTipoSituacao ALIAS FOR $7;

    inEntidades         integer[];
    stSql               VARCHAR :='';
    inAno               INTEGER;
    inMes               INTEGER;
    reRegistro          RECORD;

BEGIN
    --Pl base RCL
    inAno :=  substr(dtFinal, 7, 4 ) ;
    inMes :=  substr(dtFinal, 4, 2 ) ; 

    inEntidades := regexp_split_to_array(stEntidades,',');
    
    IF ( stTipoSituacao = 'liquidado' ) THEN
    
        stSql := '
            CREATE TEMPORARY TABLE tmp_despesa_pessoal_mensal AS (
    
            SELECT
                cast ( conta_despesa.cod_conta  as varchar ) as cod_conta,
                cast ( coalesce(  stn.tituloRCL( publico.fn_mascarareduzida(conta_despesa.cod_estrutural)) , conta_despesa.descricao ) as varchar ) as descricao,
                cast ( conta_despesa.cod_estrutural as varchar ) as cod_estrutural
                ';
            
            IF inNivel = 3 AND cod_estrutural = '3.3.1' THEN
                IF (SELECT COUNT(*) FROM stn.despesa_pessoal WHERE mes = inMes::INTEGER AND ano = ''||inAno||'' AND cod_entidade IN ( inEntidades[0],inEntidades[1] ) ) >= 1 THEN
                    stSql := stSql||' ,(SELECT COALESCE(SUM(valor), 0.00)
                                         FROM stn.despesa_pessoal
                                        WHERE mes = '||inMes::INTEGER||'
                                          AND ano = '''||inAno||'''
                                          AND cod_entidade IN ('||stEntidades||')) as mes';
                ELSE
                    stSql := stSql||'
                    , COALESCE((select * from tcemg.fn_relatorio_demostrativo_rcl_despesa_liquidada('||quote_literal(dtInicial)||'
                                                                                                    ,'||quote_literal(dtFinal)||'
                                                                                                    ,'||quote_literal(stEntidades) ||'
                                                                                                    ,'||quote_literal('(conta_despesa.cod_estrutural like ''' ||substr( cod_estrutural, 3,16)||'%'' )') || ' 
                    )), 0.00) as valor';
                END IF;
            ELSE
                stSql := stSql||'
                    , COALESCE((select * from tcemg.fn_relatorio_demostrativo_rcl_despesa_liquidada('||quote_literal(dtInicial)||'
                                                                                                    ,'||quote_literal(dtFinal)||'
                                                                                                    ,'||quote_literal(stEntidades) ||'
                                                                                                    ,'||quote_literal('(conta_despesa.cod_estrutural like ''' ||substr( cod_estrutural, 3,16)||'%'' )') || ' 
                    )), 0.00) as valor';
            END IF;
                
        stSql := stSql||'
                     
                    FROM orcamento.conta_despesa
                   WHERE conta_despesa.cod_estrutural LIKE ''' ||substr( cod_estrutural, 3,16)||'%'' 
                     AND publico.fn_nivel(conta_despesa.cod_estrutural) = ''' || inNivel-1 || ''' 
                     AND conta_despesa.exercicio                        = '''|| stExercicio ||'''
                )';
    
        EXECUTE stSql;
        
    END IF;
    
    IF ( stTipoSituacao = 'empenhado' ) THEN
    
        stSql := '
            CREATE TEMPORARY TABLE tmp_despesa_pessoal_mensal AS (
    
            SELECT
                cast ( conta_despesa.cod_conta  as varchar ) as cod_conta,
                cast ( coalesce(  stn.tituloRCL( publico.fn_mascarareduzida(conta_despesa.cod_estrutural)) , conta_despesa.descricao ) as varchar ) as descricao,
                cast ( conta_despesa.cod_estrutural as varchar ) as cod_estrutural
                ';

        stSql := stSql||'
                    , COALESCE((select * from tcemg.fn_despesa_total_pessoal_empenhada('||quote_literal(dtInicial)||'
                                                                                    ,'||quote_literal(dtFinal)||'
                                                                                    ,'||quote_literal(stEntidades) ||'
                                                                                    ,'||quote_literal('(conta_despesa.cod_estrutural like ''' ||substr( cod_estrutural, 3,16)||'%'' )') || ' 
                    )), 0.00) as valor';            

            stSql := stSql||'
                     
                    FROM orcamento.conta_despesa
                   WHERE conta_despesa.cod_estrutural LIKE ''' ||substr( cod_estrutural, 3,16)||'%'' 
                     AND publico.fn_nivel(conta_despesa.cod_estrutural) = ''' || inNivel-1 || ''' 
                     AND conta_despesa.exercicio                        = '''|| stExercicio ||'''
                )';
    
        EXECUTE stSql;
        
    END IF;
    
       IF ( stTipoSituacao = 'pago' ) THEN
    
        stSql := '
            CREATE TEMPORARY TABLE tmp_despesa_pessoal_mensal AS (
    
            SELECT
                cast ( conta_despesa.cod_conta  as varchar ) as cod_conta,
                cast ( coalesce(  stn.tituloRCL( publico.fn_mascarareduzida(conta_despesa.cod_estrutural)) , conta_despesa.descricao ) as varchar ) as descricao,
                cast ( conta_despesa.cod_estrutural as varchar ) as cod_estrutural
                ';                
                
                stSql := stSql||'
                    , COALESCE((select * from tcemg.fn_despesa_total_pessoal_paga('||quote_literal(dtInicial)||'
                                                                                , '|| quote_literal(dtFinal)||'
                                                                                , '|| quote_literal(stEntidades) ||'
                                                                                , '|| quote_literal(stExercicio) ||'
                                                                                , '|| quote_literal( substr( cod_estrutural, 3,16))|| '
                                                                                , false
                    )), 0.00) as valor';
                
            stSql := stSql||'
                     
                    FROM orcamento.conta_despesa
              LEFT JOIN orcamento.despesa
                        ON despesa.exercicio = conta_despesa.exercicio
                      AND despesa.cod_conta = conta_despesa.cod_conta
                   WHERE conta_despesa.cod_estrutural LIKE ''' ||substr( cod_estrutural, 3,16)||'%'' 
                     AND publico.fn_nivel(conta_despesa.cod_estrutural) = ''' || inNivel-1 || ''' 
                     AND conta_despesa.exercicio                        = '''|| stExercicio ||'''
                )';
    
        EXECUTE stSql;
        
    END IF;


    stSql := 'SELECT cod_conta,
                     descricao,
                     cod_estrutural,
                     valor
                FROM tmp_despesa_pessoal_mensal';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_despesa_pessoal_mensal ;
        
    RETURN;
 
END;

$$ language 'plpgsql';

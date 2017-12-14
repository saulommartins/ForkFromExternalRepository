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

CREATE OR REPLACE FUNCTION stn.fn_rgf_anexo1_despesas ( varchar,varchar,varchar,varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    dtInicial     	ALIAS FOR $2;
    dtFinal      	ALIAS FOR $3;
    stCodEntidades 	ALIAS FOR $4;
    
    reRegistro 		record ;
    reReg   		record ;
    stSql 	        varchar := '';
    stSqlAux    	varchar := '';
    stValor        	varchar := '';
    boRestos            boolean;

BEGIN

    boRestos = false;

    IF (dtFinal = '31/12/'||stExercicio) THEN
         SELECT valor INTO stValor
                     FROM administracao.configuracao 
                            WHERE parametro ilike '%virada%'
                            AND exercicio =  stExercicio   ;
    
        IF (stValor = 'T') THEN
            boRestos = true;
        END IF;
    END IF;
   
	stSql := '
	CREATE TEMPORARY TABLE tmp_rgf_anexo1_despesa AS (

		SELECT 
            1 as ordem,
            1 as grupo,
            0 as subgrupo,
            CAST(''3.1.0.0.00.00.00.00.00'' as VARCHAR) as cod_estrutural,
            CAST(''DESPESA BRUTA COM PESSOAL (I)'' as VARCHAR) as descricao,
            1 as nivel,
            0.00 as liquidado,
            0.00 as restos

        UNION ALL

        SELECT
            2 as ordem,
            1 as grupo,
            1 as subgrupo,
            cast(''3.1.1.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Pessoal Ativo'' as varchar) as descricao ,
            2 as nivel ,
            COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades)  ||', '' (conta_despesa.cod_estrutural like  ''''3.1%'''' 
                    and conta_despesa.cod_estrutural not like ''''3.1.9.0.01%''''
                    and conta_despesa.cod_estrutural not like ''''3.1.9.0.03%''''
                    and conta_despesa.cod_estrutural not like ''''3.1.9.0.34%'''') '')), 0.00) as liquidado,
            ';
        if (boRestos = true) then
            stSql := stSql || ' COALESCE((select * from stn.fn_rgf_despesa_empenhada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades)  ||', '' (conta_despesa.cod_estrutural like  ''''3.1%'''' 
                                         and conta_despesa.cod_estrutural not like ''''3.1.9.0.01%''''
                                         and conta_despesa.cod_estrutural not like ''''3.1.9.0.03%''''
                                         and conta_despesa.cod_estrutural not like ''''3.1.9.0.34%'''') '')), 0.00) as restos ';
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
            2 as nivel ,
            COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades)  ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.01%''''
                     OR conta_despesa.cod_estrutural like ''''3.1.9.0.03%'''') '')), 0.00) as liquidado,
            ';
        if (boRestos = true) then
            stSql := stSql || ' COALESCE((select * from stn.fn_rgf_despesa_empenhada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades)  ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.01%'''' 
                                          OR conta_despesa.cod_estrutural like ''''3.1.9.0.03%'''') '')), 0.00) as restos ';
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
            cast(''Outras despesas de pessoal decorrentes de contratos de terceirização (§ 1º do art. 18 da LRF)'' as varchar) as descricao ,
            2 as nivel ,
            COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades)  ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.34%'''') '')), 0.00) as liquidado,
            ';
        if (boRestos = true) then
            stSql := stSql || ' COALESCE((select * from stn.fn_rgf_despesa_empenhada_anexo1('|| quote_literal(dtInicial) ||', '||quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades) ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.34%'''') '')), 0.00) as restos ';
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
            0.00 as liquidado,
            0.00 as restos

        UNION ALL

        SELECT
            6 as ordem,
            2 as grupo,
            1 as subgrupo,
            cast(''3.2.1.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Indenizações por Demissão e Incentivos a Demissão Voluntária'' as varchar) as descricao ,
            2 as nivel ,
            COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades) ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.94%'''') '')), 0.00) as liquidado,
            ';
        if (boRestos = true) then
            stSql := stSql || ' COALESCE((select * from stn.fn_rgf_despesa_empenhada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades) ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.94%'''') '')), 0.00) as restos ';
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
            cast(''Decorrentes de Decisão Judicial de período anterior ao da apuração'' as varchar) as descricao ,
            2 as nivel ,
            COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades)  ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.91%'''') '')), 0.00) as liquidado,
            ';
        if (boRestos = true) then
            stSql := stSql || ' COALESCE((select * from stn.fn_rgf_despesa_empenhada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades) ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.91%'''') '')), 0.00) as restos ';
        else
            stSql := stSql || ' 0.00 as restos ';
        end if;

    stSql := stSql || '

        UNION ALL

        SELECT
            8 as ordem,
            2 as grupo,
            1 as subgrupo,
            cast(''3.2.3.0.00.00.00.00.00'' as varchar) as cod_estrutural ,
            cast(''Despesas de Exercícios Anteriores de período anterior ao da apuração'' as varchar) as descricao ,
            2 as nivel ,
            COALESCE((select * from stn.fn_rgf_despesa_liquidada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades) ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.92%'''') '')), 0.00) as liquidado,
            ';
        if (boRestos = true) then
            stSql := stSql || ' COALESCE((select * from stn.fn_rgf_despesa_empenhada_anexo1('|| quote_literal(dtInicial) ||', '|| quote_literal(dtFinal) ||', '|| quote_literal(stCodEntidades) ||', '' (conta_despesa.cod_estrutural like  ''''3.1.9.0.92%'''') '')), 0.00) as restos ';
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
            cast(''Inativos e Pensionistas com Recursos Vinculados'' as varchar) as descricao,
            2 as nivel,
            0.00 as liquidado,
            0.00 as restos

    )
	';
	
	EXECUTE stSql;
	
    -------------------------------------------------
    -- Adiciona o valor da despesa pessoal vinculada
    -------------------------------------------------
    UPDATE tmp_rgf_anexo1_despesa
       SET liquidado = liquidado + (SELECT stn.fn_calcula_dp_vinculada(stExercicio,dtFinal,stCodEntidades))
     WHERE grupo = 1
       AND nivel = 2
       AND cod_estrutural = '3.1.1.0.00.00.00.00.00';
	
    -- Calcular totais do nivel pai

    stSql := 'SELECT DISTINCT grupo FROM tmp_rgf_anexo1_despesa ';

    FOR reReg IN EXECUTE stSql
    LOOP

        stSqlAux := '
            UPDATE tmp_rgf_anexo1_despesa SET
                liquidado = (SELECT COALESCE(SUM(liquidado), 0.00) FROM tmp_rgf_anexo1_despesa WHERE grupo = '|| reReg.grupo ||' AND nivel = 2),
                restos = (SELECT COALESCE(SUM(restos), 0.00) FROM tmp_rgf_anexo1_despesa WHERE grupo = '|| reReg.grupo ||' AND nivel = 2)
            WHERE
                grupo = '|| reReg.grupo ||' AND nivel = 1 ';

        EXECUTE stSqlAux;

    END LOOP;

	stSql := 'SELECT 
                    nivel,
                    cod_estrutural,
                    descricao,                                        
                    liquidado::NUMERIC(14,2),
                    restos::NUMERIC(14,2)
     FROM tmp_rgf_anexo1_despesa ORDER BY ordem, grupo';
	
  
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

	DROP TABLE tmp_rgf_anexo1_despesa ; 

    RETURN;
 
END;
   
$$ language 'plpgsql';

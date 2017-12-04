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
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 1
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29316 $
* $Name$
* $Author: lbbarreiro $
* $Date: 2008-04-17 18:13:29 -0300 (Qui, 17 Abr 2008) $
*
* Casos de uso: uc-04.05.28
*/


/*$Log$
 *Revision 1.1  2006/09/26 17:33:42  bruce
 *Colocada a tag de log
 **/

--select * from stn.fn_anexo1('2006','01/03/2006','30/04/2006','1,2');
CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo1_receitas(varchar, varchar, varchar ,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    dtInicial     	        ALIAS FOR $2;
    dtFinal     	        ALIAS FOR $3;
    stCodEntidades          ALIAS FOR $4;
    dtInicioAno             VARCHAR := '';
    dtFimAno                VARCHAR := '';
    stSql                   VARCHAR := '';
    stSql1                  VARCHAR := '';
    stMascClassReceita      VARCHAR := '';
    stMascRecurso           VARCHAR := '';
    reRegistro              RECORD;

BEGIN
        dtInicioAno := '01/01/' || stExercicio;

        stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as primeira
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote
            WHERE

                    ore.exercicio       = ' || quote_literal(stExercicio);
                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
                end if;

            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio

                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = true
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''D''

                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo

            UNION

            SELECT
                  ocr.cod_estrutural as cod_estrutural
                , lote.dt_lote       as data
                , vl.vl_lancamento   as valor
                , vl.oid             as segunda
            FROM
                contabilidade.valor_lancamento      as vl   ,
                orcamento.conta_receita             as ocr  ,
                orcamento.receita                   as ore  ,
                contabilidade.lancamento_receita    as lr   ,
                contabilidade.lancamento            as lan  ,
                contabilidade.lote                  as lote

            WHERE
                ore.exercicio       = ' || quote_literal(stExercicio);  

                if ( stCodEntidades != '' ) then
                   stSql := stSql || ' AND ore.cod_entidade    IN ('|| stCodEntidades ||') ';
                end if;
            stSql := stSql || '

                AND ocr.cod_conta       = ore.cod_conta
                AND ocr.exercicio       = ore.exercicio


                -- join lancamento receita
                AND lr.cod_receita      = ore.cod_receita
                AND lr.exercicio        = ore.exercicio
                AND lr.estorno          = false
                -- tipo de lancamento receita deve ser = A , de arrecadação
                AND lr.tipo             = ''A''

                -- join nas tabelas lancamento_receita e lancamento
                AND lan.cod_lote        = lr.cod_lote
                AND lan.sequencia       = lr.sequencia
                AND lan.exercicio       = lr.exercicio
                AND lan.cod_entidade    = lr.cod_entidade
                AND lan.tipo            = lr.tipo

                -- join nas tabelas lancamento e valor_lancamento
                AND vl.exercicio        = lan.exercicio
                AND vl.sequencia        = lan.sequencia
                AND vl.cod_entidade     = lan.cod_entidade
                AND vl.cod_lote         = lan.cod_lote
                AND vl.tipo             = lan.tipo
                -- na tabela valor lancamento  tipo_valor deve ser credito
                AND vl.tipo_valor       = ''C''

                -- Data Inicial e Data Final, antes iguala codigo do lote
                AND lote.cod_lote       = lan.cod_lote
                AND lote.cod_entidade   = lan.cod_entidade
                AND lote.exercicio      = lan.exercicio
                AND lote.tipo           = lan.tipo )'; 

        EXECUTE stSql;



stSql := '
    SELECT
        1 as grupo,
        cod_estrutural,
        nivel,
        nom_conta,
        previsao_inicial,
        previsao_inicial as previsao_atualizada,
        coalesce(no_bimestre,0.00)*-1 as no_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END as p_no_bimestre,    
        coalesce(ate_bimestre,0.00)*-1 as ate_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END as p_ate_bimestre,    
        coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(pc.cod_estrutural) as nivel,

            pc.cod_estrutural,
            pc.nom_conta,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , '|| quote_literal(stCodEntidades) ||'
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
            contabilidade.plano_conta   as pc,
            orcamento.conta_receita     as ocr
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  AND
            pc.exercicio        = ocr.exercicio             
            -- Filtros
        AND pc.cod_estrutural like ''4.1%''  
  
        AND publico.fn_nivel(pc.cod_estrutural) >       1   
        AND publico.fn_nivel(pc.cod_estrutural) <=      4   
        AND pc.exercicio = '|| quote_literal(stExercicio) ||'
    
        ORDER BY
            pc.cod_estrutural
    ) as tbl

UNION

    SELECT
        2 as grupo,
        cod_estrutural,
        nivel,
        nom_conta,
        coalesce(previsao_inicial,0.00),
        coalesce(previsao_inicial,0.00) as previsao_atualizada,
        coalesce(no_bimestre,0.00)*-1 as no_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END as p_no_bimestre,    
        coalesce(ate_bimestre,0.00)*-1 as ate_bimestre,
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END as p_ate_bimestre,    
        coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(pc.cod_estrutural) as nivel,

            pc.cod_estrutural,
            pc.nom_conta,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , '|| quote_literal(stCodEntidades) ||'
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
            contabilidade.plano_conta   as pc,
            orcamento.conta_receita     as ocr
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  
        AND pc.exercicio        = ocr.exercicio          
        AND pc.cod_estrutural   like ''4.2%'' 

        AND publico.fn_nivel(pc.cod_estrutural) >       1   
        AND publico.fn_nivel(pc.cod_estrutural) <=      4   
        AND pc.exercicio = '|| quote_literal(stExercicio) ||'
    
        ORDER BY
            pc.cod_estrutural
    ) as tbl 
UNION 

    SELECT
        3 as grupo,
        ''7.0.0.0.0.00.00.00.00.00'' as cod_estrutural,
        1 as nivel,
        cast( ''DEDUCOES DA RECEITA'' as varchar )  as nom_conta,
        coalesce(sum(previsao_inicial),0.00) as previsao_inicial,
        coalesce(sum(previsao_inicial),0.00) as previsao_atualizada,
        coalesce(sum(no_bimestre),0.00)*-1 as no_bimestre,
        sum(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_no_bimestre,    
        coalesce(sum(ate_bimestre),0.00)*-1 as ate_bimestre,
        sum(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_ate_bimestre,    
        sum(coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00)) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(pc.cod_estrutural) as nivel,

            pc.cod_estrutural,
            pc.nom_conta,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , '|| quote_literal(stCodEntidades) ||'
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
            contabilidade.plano_conta   as pc,
            orcamento.conta_receita     as ocr
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            CASE WHEN pc.exercicio <= ''2007'' THEN
	                pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  
		        AND pc.cod_estrutural   like ''4.9%'' 
		    ELSE
	                pc.cod_estrutural   = ocr.cod_estrutural  
		        AND pc.cod_estrutural   like ''9.%'' 
			END		    	
        AND pc.exercicio        = ocr.exercicio          

        --AND publico.fn_nivel(pc.cod_estrutural) >       1   
        --AND publico.fn_nivel(pc.cod_estrutural) <=      4   
        
        AND publico.fn_nivel(pc.cod_estrutural) = 2 
        AND pc.exercicio = '|| quote_literal(stExercicio) ||'
        
        ORDER BY
            pc.cod_estrutural
	) as tbl

UNION

    SELECT
        3 as grupo,
        ''7.0.0.0.0.00.00.00.00.00'' as cod_estrutural,
        1 as nivel,
        cast( ''RECEITAS (INTRA-ORCAMENTARIAS)'' as varchar )  as nom_conta,
        coalesce(sum(previsao_inicial),0.00) as previsao_inicial,
        coalesce(sum(previsao_inicial),0.00) as previsao_atualizada,
        coalesce(sum(no_bimestre),0.00)*-1 as no_bimestre,
        sum(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_no_bimestre,    
        coalesce(sum(ate_bimestre),0.00)*-1 as ate_bimestre,
        sum(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_ate_bimestre,    
        sum(coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00)) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(pc.cod_estrutural) as nivel,

            pc.cod_estrutural,
            pc.nom_conta,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , '|| quote_literal(stCodEntidades) ||'
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
            contabilidade.plano_conta   as pc,
            orcamento.conta_receita     as ocr
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  
        AND pc.exercicio        = ocr.exercicio          
        AND (pc.cod_estrutural   like ''4.7%'' or pc.cod_estrutural   like ''4.8%'' )

		-- ** REVISAR ** 
        --AND publico.fn_nivel(pc.cod_estrutural) >       1   
        --AND publico.fn_nivel(pc.cod_estrutural) <=      4   
        
        AND publico.fn_nivel(pc.cod_estrutural) = 2 
        AND pc.exercicio = '|| quote_literal(stExercicio) ||'
        ORDER BY 
            pc.cod_estrutural
	) as tbl

UNION
    SELECT
        4 as grupo,
        ''94.2.1.1.5.00.00.00.00.0'' as cod_estrutural,
        5 as nivel,
        cast(''REFINANCIAMENTO DA DIVIDA MOBILIARIA'' as varchar ) as nom_conta,
        coalesce(sum(previsao_inicial),0.00) as previsao_inicial,
        coalesce(sum(previsao_inicial),0.00) as previsao_atualizada,
        coalesce(sum(no_bimestre),0.00)*-1 as no_bimestre,
        sum(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(no_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_no_bimestre,    
        coalesce(sum(ate_bimestre),0.00)*-1 as ate_bimestre,
        sum(
        CASE WHEN coalesce(previsao_inicial,0.00) <> ''0.00'' THEN 
            CAST((coalesce(ate_bimestre,0.00)*100/coalesce(previsao_inicial,0.00)*-1) as numeric(14,2))
        ELSE
            ''0.00''
        END ) as p_ate_bimestre,    
        sum(coalesce(previsao_inicial,0.00) + coalesce(ate_bimestre,0.00)) as a_realizar

    FROM(
        SELECT
            publico.fn_nivel(pc.cod_estrutural) as nivel,

            pc.cod_estrutural,
            pc.nom_conta,
            orcamento.fn_receita_valor_previsto( '|| quote_literal(stExercicio) ||'
                                                ,publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                , ''1''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicial) ||'
                                                     ,'|| quote_literal(dtFinal)   ||'
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(ocr.cod_estrutural)
                                                     ,'|| quote_literal(dtInicioAno) ||'
                                                     ,'|| quote_literal(dtFinal)     ||'
            ) as ate_bimestre
        FROM
            contabilidade.plano_conta   as pc,
            orcamento.conta_receita     as ocr
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            pc.cod_estrutural   = ''4.''||ocr.cod_estrutural  
        AND pc.cod_estrutural   like ''4.2.1.1.5%''
        AND pc.exercicio        = ocr.exercicio          

        AND publico.fn_nivel(pc.cod_estrutural) >       1   
        AND publico.fn_nivel(pc.cod_estrutural) <=      5
        AND pc.exercicio = '|| quote_literal(stExercicio) ||'
        
        ORDER BY
            pc.cod_estrutural
	) as tbl
		
';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;

    RETURN;
END;
$$ language 'plpgsql';

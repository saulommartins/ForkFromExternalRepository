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
    * Script de função PLPGSQL - Relatório STN - RREO - Anexo 11.
    * Data de Criação: 18/12/2014


    * @author Carolina Schwaab Marçal 

    * Casos de uso: uc-06.01.14

    $Id: $

*/

CREATE OR REPLACE FUNCTION stn.fn_anexo11_despesas2015 ( varchar,integer,varchar,varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    inBimestre     	ALIAS FOR $2;
    stCodEntidades 	ALIAS FOR $3;
    stCodRecursos 	ALIAS FOR $4;

    dtInicial  		varchar := '';
    dtFinal    		varchar := '';
    dtIniExercicio 	VARCHAR := '';
    
    arDatas 		varchar[] ;
    reRegistro 		record ;
    stSql 		varchar := '';

BEGIN

    arDatas := publico.bimestre ( stExercicio, inBimestre );   
    dtInicial := arDatas [ 0 ];
    dtFinal   := arDatas [ 1 ];
    
    dtIniExercicio := '01/01/' || stExercicio;

	-- TABELAS TEMPORARIAS
	stSQL := '
    CREATE TEMPORARY TABLE tmp_rreo_despesa AS (

    SELECT grupo
         , nivel
         , cod_estrutural
         , descricao 
         , dotacao_atualizada AS dot_atu
         , vl_liquidado_total  AS liq_tot
         , vl_pago_total AS pago
         
    FROM (
            SELECT * FROM contabilidade.fn_relatorio_balanco_orcamentario_despesa_novo(''' || stExercicio || ''',''' || dtInicial || ''' ,''' || dtFinal || ''', ''' || stCodEntidades || ''', ''' || stCodRecursos || ''') as
            retorno (                                                                                          
                grupo                   integer ,                                                                  
                cod_estrutural          varchar ,                                                                  
                descricao               varchar ,                                                                  
                nivel                   integer ,                                                                  
                dotacao_inicial         numeric ,                                                            
                creditos_adicionais     numeric ,                                                            
                dotacao_atualizada      numeric ,                                                            
                vl_empenhado_bimestre   numeric ,                                                            
                vl_empenhado_total      numeric ,                                                            
                vl_liquidado_bimestre   numeric ,                                                            
                vl_liquidado_total      numeric ,                                                            
                vl_pago_total	        numeric ,       
                percentual              numeric ,                                                            
                saldo_liquidar          numeric 
         ) 
   WHERE cod_estrutural LIKE ''4%''
 
 ) AS tab1
    
    
    )';
    
    EXECUTE stSQL;
	-- Total das Suplementações	
    -- Suplementado
    
    stSql := 'CREATE TEMPORARY TABLE tmp_suplementacao_suplementada AS
               SELECT
                    sup.dt_suplementacao, 
                    sups.exercicio,                     
                    sups.cod_despesa, 
                    sum(sups.valor)  as vl_suplementado 
               FROM orcamento.suplementacao                as sup, 
                    orcamento.suplementacao_suplementada   as sups, 
                    orcamento.despesa                      as de 
               WHERE sup.exercicio           = '|| quote_literal(stExercicio) ||' 
                 AND sup.dt_suplementacao BETWEEN to_date('''|| dtIniExercicio ||''',''dd/mm/yyyy'') 
                                              AND to_date('''|| dtFinal ||''',''dd/mm/yyyy'') ';
    
    if ( stCodEntidades != '' ) then
        stSql := stSql || 'AND   de.cod_entidade IN (' || stCodEntidades || ' )';
    end if;
    
    stSql := stSql|| ' AND   sup.exercicio           = sups.exercicio
			AND   sup.cod_suplementacao   = sups.cod_suplementacao
			AND   sups.exercicio          = de.exercicio
			AND   sups.cod_despesa        = de.cod_despesa
		GROUP BY sups.cod_despesa, sups.exercicio, sup.dt_suplementacao;
	CREATE INDEX unq_tmp_suplementacao_suplementada ON tmp_suplementacao_suplementada (cod_despesa);
	';

    EXECUTE stSql;

    -- Reduzido
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_suplementacao_reduzida AS
    	SELECT
                sup.dt_suplementacao, 
                supr.exercicio, 
                supr.cod_despesa, 
    		sum(supr.valor) as vl_reduzido 
    	FROM 
    		orcamento.suplementacao as sup, 
    		orcamento.suplementacao_reducao as supr, 
    		orcamento.despesa as de 
    	WHERE 
    		sup.exercicio = '|| quote_literal(stExercicio) ||' AND 
    		sup.dt_suplementacao BETWEEN to_date('''|| dtIniExercicio ||''', ''dd/mm/yyyy'') 
                                         AND to_date('''|| dtFinal ||''', ''dd/mm/yyyy'')
	';
    if (stCodEntidades != '' ) then 
         stSql := stSql || ' AND de.cod_entidade IN (' ||  stCodEntidades ||' ) ';
    end if ;

    stSql := stSql || 'AND sup.exercicio = supr.exercicio AND 
    		sup.cod_suplementacao   = supr.cod_suplementacao AND 
    		supr.exercicio          = de.exercicio AND 
    		supr.cod_despesa        = de.cod_despesa 
    	GROUP BY supr.cod_despesa, sup.dt_suplementacao, supr.exercicio ;
    	
    CREATE INDEX unq_tmp_suplementacao_reduzida ON tmp_suplementacao_reduzida (cod_despesa);
    ';
    
    EXECUTE stSql;

    -- Total das Despesas
    
	stSql := '
	CREATE TEMPORARY TABLE tmp_despesa_totais AS
		SELECT 
			de.exercicio, 
			de.cod_despesa, 
			de.vl_original 
		FROM 
			orcamento.despesa de, 
			empenho.pre_empenho_despesa ped 
		WHERE 
			de.exercicio    = '|| quote_literal(stExercicio) ||' AND 
			de.exercicio    = ped.exercicio AND 
			de.cod_despesa  = ped.cod_despesa 
		GROUP BY 
			de.exercicio, 
			de.cod_despesa, 
			de.vl_original 
		ORDER BY 
			de.exercicio, 
			de.cod_despesa ;
			
	CREATE INDEX unq_tmp_despesa_totais ON tmp_despesa_totais (exercicio,cod_despesa);
	';

	EXECUTE stSql;

	
	-- --------------------------------------------
	-- Total por Despesa
	-- --------------------------------------------

	stSql := '
	CREATE TEMPORARY TABLE tmp_despesa AS 
		SELECT 
			de.cod_conta, 
			de.exercicio, 
			ocd.cod_estrutural, 
			sum(coalesce(de.vl_original,0.00)) as vl_original, 			
			(sum(coalesce(sups.vl_suplementado,0.00)) - sum(coalesce(supr.vl_reduzido,0.00))) as vl_suplementacoes, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_empenhado_bimestre, 
			CAST(0.00 AS NUMERIC(14,2)) as vl_liquidado_bimestre, 
			CAST(0.00 AS NUMERIC(14,2)) as despesas_empenhadas, 
			CAST(0.00 AS NUMERIC(14,2)) as liquidado_ate_bimestre,
			de.cod_recurso 
		FROM 
			orcamento.despesa de 
			INNER JOIN 
			orcamento.conta_despesa ocd ON 
				ocd.exercicio = de.exercicio and 
				ocd.cod_conta = de.cod_conta 
			LEFT JOIN 
			tmp_despesa_totais tdt ON 
				tdt.exercicio = de.exercicio AND 
				tdt.cod_despesa = de.cod_despesa 
			--Suplementacoes
			LEFT JOIN 
			tmp_suplementacao_suplementada sups ON 
				de.cod_despesa = sups.cod_despesa
			LEFT JOIN 
			tmp_suplementacao_reduzida supr ON 
				de.cod_despesa = supr.cod_despesa 
		WHERE 
			de.exercicio = '|| quote_literal(stExercicio) ||' AND 
			de.cod_entidade IN ('||  stCodEntidades ||') 
		GROUP BY 
			de.cod_conta, 
			de.exercicio, 
			ocd.cod_estrutural,
			de.cod_recurso 
		ORDER BY 
			de.cod_conta, 
			de.exercicio 
	';
	
	EXECUTE stSql;


    -- Empenhados
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_empenhado AS (
    SELECT 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta, 
        COALESCE(SUM(ipe.vl_total), 0.00) AS valor_empenhado 
    FROM 
        empenho.pre_empenho pe 
        INNER JOIN 
        empenho.item_pre_empenho ipe ON 
            ipe.cod_pre_empenho = pe.cod_pre_empenho AND 
            ipe.exercicio = pe.exercicio 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = pe.exercicio AND 
            e.cod_pre_empenho = pe.cod_pre_empenho 
        LEFT OUTER JOIN 
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            cd.cod_estrutural, 
            cd.cod_conta 
        FROM
            empenho.pre_empenho_despesa ped
            INNER JOIN
            orcamento.despesa d ON 
                ped.exercicio = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
            INNER JOIN 
            orcamento.conta_despesa cd ON
                d.exercicio = cd.exercicio AND
                d.cod_conta = cd.cod_conta 
        ) AS tb_cd ON
            pe.exercicio = tb_cd.exercicio AND 
            pe.cod_pre_empenho = tb_cd.cod_pre_empenho 
    WHERE 
        tb_cd.cod_estrutural LIKE ''4%'' AND 
        tb_cd.exercicio < ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stCodEntidades || ') AND 
        e.dt_empenho <= TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') 
    GROUP BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    ORDER BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    )
    ';

    
    EXECUTE stSql;
    
    -- Empenhados Anulados
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_anulado AS (
    SELECT 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta, 
        COALESCE(SUM(eai.vl_anulado), 0.00) AS valor_anulado 
    FROM
        empenho.empenho e
        INNER JOIN
        empenho.empenho_anulado ea ON
            ea.exercicio = e.exercicio AND
            ea.cod_entidade = e.cod_entidade AND
            ea.cod_empenho = e.cod_empenho
        INNER JOIN
        empenho.empenho_anulado_item eai ON
            eai.exercicio = ea.exercicio AND
            eai.cod_entidade = ea.cod_entidade AND
            eai.cod_empenho = ea.cod_empenho AND
            eai."timestamp" = ea."timestamp" 
        LEFT OUTER JOIN 
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            cd.cod_estrutural, 
            cd.cod_conta 
        FROM
            empenho.pre_empenho_despesa ped
            INNER JOIN
            orcamento.despesa d ON 
                ped.exercicio = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
            INNER JOIN 
            orcamento.conta_despesa cd ON
                d.exercicio = cd.exercicio AND
                d.cod_conta = cd.cod_conta 
        ) AS tb_cd ON
            e.exercicio = tb_cd.exercicio AND 
            e.cod_pre_empenho = tb_cd.cod_pre_empenho 
    WHERE 
        tb_cd.cod_estrutural LIKE ''4%'' AND 
        tb_cd.exercicio < ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stCodEntidades || ') AND 
        e.dt_empenho <= TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') AND 
        TO_DATE( TO_CHAR( ea."timestamp", ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) <= TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') 
    GROUP BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    ORDER BY 
        tb_cd.exercicio, 
        tb_cd.cod_estrutural, 
        tb_cd.cod_conta 
    )';
    
    EXECUTE stSql;
    
    -- Restos Liquidados
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_liquidado AS (
    SELECT
        e.exercicio,
        tb_cd.cod_estrutural, 
        SUM(nli.vl_total) as valor_liquidado 
    FROM 
        empenho.empenho e 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio_empenho = e.exercicio AND 
            nl.cod_entidade = e.cod_entidade AND 
            nl.cod_empenho = e.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_item nli ON
            nli.exercicio = nl.exercicio AND 
            nli.cod_nota = nl.cod_nota AND
            nli.cod_entidade = nl.cod_entidade
        INNER JOIN 
        empenho.pre_empenho pe ON
            pe.exercicio = e.exercicio AND
            pe.cod_pre_empenho = e.cod_pre_empenho 
        LEFT OUTER JOIN (
        SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            cd.cod_conta,
            cd.cod_estrutural 
        FROM 
            empenho.pre_empenho_despesa as ped, 
            orcamento.despesa as d,
            orcamento.conta_despesa as cd 
        WHERE
            ped.exercicio < ''' || stExercicio || ''' AND 
            ped.cod_despesa = d.cod_despesa AND 
            ped.exercicio = d.exercicio AND 
            ped.cod_conta = cd.cod_conta AND 
            ped.exercicio = cd.exercicio 
    ) AS tb_cd ON
        pe.exercicio = tb_cd.exercicio AND
        pe.cod_pre_empenho = tb_cd.cod_pre_empenho
     
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stCodEntidades || ') AND
        nl.dt_liquidacao <= to_date(''' || dtFinal || ''', ''dd/mm/yyyy'') AND
        tb_cd.cod_estrutural like ''4%'' 
    GROUP BY 
        e.exercicio,
        tb_cd.cod_estrutural 
    ORDER BY
        e.exercicio,
        tb_cd.cod_estrutural
    )
    ';
    
    EXECUTE stSql;
    
    
    -- Restos Liquidados Estornados
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_estornado_liquidacao AS (
    SELECT
        e.exercicio,
        tb_cd.cod_conta, 
        tb_cd.cod_estrutural, 
        SUM(nlia.vl_anulado) as valor_estornado_liq 
    FROM 
        empenho.empenho e
        INNER JOIN 
        empenho.nota_liquidacao nl ON
            nl.exercicio_empenho = e.exercicio AND
            nl.cod_entidade = e.cod_entidade AND
            nl.cod_empenho = e.cod_empenho
        INNER JOIN 
        empenho.nota_liquidacao_item nli ON
            nli.exercicio = nl.exercicio AND
            nli.cod_entidade = nl.cod_entidade AND
            nli.cod_nota = nl.cod_nota
        INNER JOIN   
        empenho.nota_liquidacao_item_anulado nlia ON
            nlia.exercicio = nli.exercicio AND
            nlia.cod_nota = nli.cod_nota AND
            nlia.num_item = nli.num_item AND
            nlia.exercicio_item = nli.exercicio_item AND
            nlia.cod_pre_empenho = nli.cod_pre_empenho AND
            nlia.cod_entidade = nli.cod_entidade
        INNER JOIN 
        empenho.pre_empenho as pe ON
            pe.exercicio = e.exercicio AND
            pe.cod_pre_empenho = e.cod_pre_empenho 
        LEFT OUTER JOIN (
        SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_conta,
            cd.cod_estrutural 
        FROM
            empenho.pre_empenho_despesa as ped, 
            orcamento.despesa           as d,
            orcamento.conta_despesa     as cd 
        WHERE
            ped.exercicio      < ''' || stExercicio || ''' AND
            ped.cod_despesa    = d.cod_despesa and 
            ped.exercicio      = d.exercicio   and 
            ped.cod_conta      = cd.cod_conta  and 
            ped.exercicio      = cd.exercicio
        ) AS tb_cd ON
            pe.exercicio = tb_cd.exercicio AND
            pe.cod_pre_empenho = tb_cd.cod_pre_empenho 
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND
        e.cod_entidade IN (' || stCodEntidades || ') AND 
        TO_DATE(TO_CHAR(nlia.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') <= TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') AND
        tb_cd.cod_estrutural like ''4%'' 
    GROUP BY 
        e.exercicio,
        tb_cd.cod_conta, 
        tb_cd.cod_estrutural 
    ORDER BY 
        e.exercicio,
        tb_cd.cod_estrutural 
    )';
    
    EXECUTE stSql;

    
    -- Pagos
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_pago AS (
    SELECT
        e.exercicio, 
        ped_d_cd.cod_estrutural,
        ped_d_cd.cod_conta, 
        sum(nlp.vl_pago) as valor_pago     
    FROM 
        empenho.empenho e
        INNER JOIN 
        empenho.nota_liquidacao nl ON
            e.exercicio = nl.exercicio_empenho AND
            e.cod_entidade = nl.cod_entidade AND
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_paga nlp ON     
            nl.exercicio = nlp.exercicio AND
            nl.cod_nota = nlp.cod_nota AND
            nl.cod_entidade = nlp.cod_entidade

        INNER JOIN 
        empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp ON
            nlp.cod_entidade = plnlp.cod_entidade AND
            nlp.cod_nota = plnlp.cod_nota AND
            nlp.exercicio = plnlp.exercicio_liquidacao AND
            nlp.timestamp = plnlp.timestamp 
        INNER JOIN 
        empenho.pagamento_liquidacao pl ON    
            pl.cod_ordem = plnlp.cod_ordem AND
            pl.exercicio = plnlp.exercicio AND
            pl.cod_entidade = plnlp.cod_entidade AND
            pl.exercicio_liquidacao = plnlp.exercicio_liquidacao AND
            pl.cod_nota = plnlp.cod_nota     
        LEFT OUTER JOIN
        (SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_conta,
            cd.cod_estrutural 
        FROM
            empenho.pre_empenho_despesa ped, 
            orcamento.despesa d,
            orcamento.conta_despesa cd
        WHERE
            ped.exercicio < ''' || stExercicio || ''' AND 
            ped.cod_despesa = d.cod_despesa and 
            ped.exercicio = d.exercicio and 
            ped.cod_conta = cd.cod_conta  and 
            ped.exercicio = cd.exercicio
        ) as ped_d_cd ON
            e.exercicio = ped_d_cd.exercicio AND
            e.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND
        e.cod_entidade IN (' || stCodEntidades || ') AND 
        to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') AND 
        ped_d_cd.cod_estrutural like ''4%''
    GROUP BY
        e.exercicio, 
        ped_d_cd.cod_estrutural ,
        ped_d_cd.cod_conta 
    ORDER BY
        e.exercicio, 
        ped_d_cd.cod_estrutural 
    )';
    
    EXECUTE stSql;
    
    
    -- Estornados
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_estornado AS (
    SELECT
        e.exercicio, 
        ped_d_cd.cod_estrutural,
        ped_d_cd.cod_conta, 
        sum(nlpa.vl_anulado) as valor_estornado 
    FROM 
        empenho.empenho e 
        INNER JOIN 
        empenho.nota_liquidacao nl ON
            e.exercicio = nl.exercicio_empenho AND
            e.cod_entidade = nl.cod_entidade AND
            e.cod_empenho = nl.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_paga nlp ON     
            nl.exercicio = nlp.exercicio AND
            nl.cod_nota = nlp.cod_nota AND
            nl.cod_entidade = nlp.cod_entidade 
        LEFT OUTER JOIN 
        (SELECT
            p.cod_entidade,
            p.cod_nota,
            p.exercicio_liquidacao,
            p.timestamp,
            pa.cod_plano 
        FROM
            contabilidade.pagamento p
            INNER JOIN 
            contabilidade.lancamento_empenho le ON
                p.cod_lote = le.cod_lote AND 
                p.tipo = le.tipo AND 
                p.sequencia = le.sequencia AND 
                p.exercicio = le.exercicio AND
                p.cod_entidade = le.cod_entidade
            INNER JOIN 
            contabilidade.conta_credito cc ON
                le.cod_lote = cc.cod_lote AND
                le.tipo = cc.tipo AND
                le.exercicio = cc.exercicio AND
                le.cod_entidade = cc.cod_entidade AND
                le.sequencia = cc.sequencia 
            INNER JOIN 
            contabilidade.plano_analitica pa ON
                cc.cod_plano = pa.cod_plano AND
                cc.exercicio = pa.exercicio
            INNER JOIN 
            contabilidade.plano_conta pc ON
                pa.cod_conta = pc.cod_conta AND
                pa.exercicio = pc.exercicio 
        WHERE
            p.exercicio < ''' || stExercicio || ''' AND 
            p.cod_entidade IN (' || stCodEntidades || ') AND 
            pc.cod_estrutural LIKE ''3.4%'' AND
            le.estorno = true 
        ) AS tmp ON
            tmp.exercicio_liquidacao = nlp.exercicio AND 
            tmp.cod_entidade = nlp.cod_entidade AND 
            tmp.cod_nota = nlp.cod_nota AND 
            tmp.timestamp = nlp.timestamp 
        INNER JOIN 
        empenho.nota_liquidacao_paga_anulada nlpa ON
            nlpa.exercicio = nlp.exercicio AND 
            nlpa.cod_nota = nlp.cod_nota AND 
            nlpa.cod_entidade = nlp.cod_entidade AND 
            nlpa."timestamp" = nlp."timestamp" 
        INNER JOIN 
        empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp ON
            nlp.cod_entidade = plnlp.cod_entidade AND
            nlp.cod_nota = plnlp.cod_nota AND
            nlp.exercicio = plnlp.exercicio_liquidacao AND
            nlp.timestamp = plnlp.timestamp 
        INNER JOIN 
        empenho.pagamento_liquidacao pl ON    
            pl.cod_ordem = plnlp.cod_ordem AND
            pl.exercicio = plnlp.exercicio AND
            pl.cod_entidade = plnlp.cod_entidade AND
            pl.exercicio_liquidacao = plnlp.exercicio_liquidacao AND
            pl.cod_nota = plnlp.cod_nota     
        LEFT OUTER JOIN
        (SELECT
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_conta,
            cd.cod_estrutural 
        FROM
            empenho.pre_empenho_despesa ped
            INNER JOIN 
            orcamento.despesa d ON
                ped.exercicio = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
            INNER JOIN 
            orcamento.conta_despesa cd ON 
                d.exercicio = cd.exercicio AND
                d.cod_conta = cd.cod_conta 
        WHERE 
            ped.exercicio < ''' || stExercicio || ''' AND
            cd.cod_estrutural LIKE ''4%''
        ) as ped_d_cd ON 
            e.exercicio = ped_d_cd.exercicio AND 
            e.cod_pre_empenho = ped_d_cd.cod_pre_empenho 
    WHERE 
        e.exercicio < ''' || stExercicio || ''' AND
        e.cod_entidade IN (' || stCodEntidades || ') AND
        TO_DATE(TO_CHAR(nlpa.timestamp_anulada, ''dd/mm/yyyy''), ''dd/mm/yyyy'') <= TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'') AND 
        ped_d_cd.cod_estrutural like ''4%'' 
    GROUP BY 
        e.exercicio, 
        ped_d_cd.cod_estrutural ,
        ped_d_cd.cod_conta 
    ORDER BY
        e.exercicio, 
        ped_d_cd.cod_estrutural 
    )';
    
    EXECUTE stSql;
    
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_resto AS (
    SELECT * FROM (
    SELECT
        cd.exercicio,
        cd.cod_conta, 
        cd.cod_estrutural,
        (COALESCE(SUM(emp.valor_empenhado), 0.00) - (COALESCE(SUM(anl.valor_anulado), 0.00))) - (COALESCE(SUM(liq.valor_liquidado), 0.00) - COALESCE(SUM(est_liq.valor_estornado_liq), 0.00)) AS valor_resto 
    FROM
        orcamento.conta_despesa cd
        LEFT JOIN 
        tmp_empenhado emp ON
            emp.exercicio = cd.exercicio AND
            emp.cod_estrutural = cd.cod_estrutural 
        LEFT JOIN
        tmp_anulado anl ON
            anl.exercicio = cd.exercicio AND
            anl.cod_estrutural = cd.cod_estrutural
        LEFT JOIN
        tmp_liquidado liq ON
            liq.exercicio = cd.exercicio AND
            liq.cod_estrutural = cd.cod_estrutural 
        LEFT JOIN
        tmp_estornado_liquidacao est_liq ON
            est_liq.exercicio = cd.exercicio AND
            est_liq.cod_estrutural = cd.cod_estrutural 
    WHERE
        cd.cod_estrutural LIKE ''4%'' 
    GROUP BY 
        cd.exercicio,
        cd.cod_conta, 
        cd.cod_estrutural
    ) AS tb 
    WHERE valor_resto <> 0.00 
    )';
    
    EXECUTE stSql;
    

    /*
    Despesas Liquidadas do primeiro dia do exercicio
    até o último dia do periodo selecionado
    */
    
    
    stSql := '
    CREATE TEMPORARY TABLE tmp_rreo_despesa_liquidada_total AS (
    SELECT 
        pedcd.exercicio, 
        pedcd.cod_despesa, 
        COALESCE(SUM(nli.vl_total), 0.00) AS vl_liquidado, 
        COALESCE(SUM(nlia.vl_anulado), 0.00) AS vl_estornado 
    FROM 
        empenho.pre_empenho pe 
        LEFT JOIN 
        (SELECT 
            ped.exercicio, 
            ped.cod_pre_empenho, 
            d.cod_despesa 
        FROM 
            empenho.pre_empenho_despesa ped 
            INNER JOIN 
            orcamento.despesa d ON 
                ped.exercicio   = d.exercicio AND
                ped.cod_despesa = d.cod_despesa
        WHERE 
            ped.exercicio = ''' || stExercicio || ''' 
        ) AS pedcd ON 
            pe.exercicio = pedcd.exercicio AND 
            pe.cod_pre_empenho = pedcd.cod_pre_empenho 
        INNER JOIN 
        empenho.empenho e ON 
            e.exercicio = pe.exercicio AND 
            e.cod_pre_empenho = pe.cod_pre_empenho 
        INNER JOIN 
        empenho.nota_liquidacao nl ON 
            nl.exercicio_empenho = e.exercicio AND 
            nl.cod_entidade = e.cod_entidade AND 
            nl.cod_empenho = e.cod_empenho 
        INNER JOIN 
        empenho.nota_liquidacao_item nli ON 
            nli.exercicio = nl.exercicio AND 
            nli.cod_entidade = nl.cod_entidade AND 
            nli.cod_nota = nl.cod_nota 
        LEFT JOIN 
        empenho.nota_liquidacao_item_anulado nlia ON 
            nli.exercicio = nlia.exercicio AND 
            nli.cod_nota = nlia.cod_nota AND 
            nli.cod_entidade = nlia.cod_entidade AND 
            nli.num_item = nlia.num_item AND 
            nli.cod_pre_empenho = nlia.cod_pre_empenho AND 
            nli.exercicio_item = nlia.exercicio_item 
    WHERE 
        e.exercicio = ''' || stExercicio || ''' AND 
        e.cod_entidade IN (' || stCodEntidades || ') AND 
        nl.dt_liquidacao BETWEEN to_date(''' || dtIniExercicio || ''', ''dd/mm/yyyy'') AND 
                                 to_date(''' || dtFinal || ''', ''dd/mm/yyyy'') 
    GROUP BY
        pedcd.exercicio, 
        pedcd.cod_despesa 
    )';
    
    EXECUTE stSql;	

 

	--- FIM DAS TEMPORARIAS
    
	stSql := '
	CREATE TEMPORARY TABLE tmp_rreo_anexo11_despesa AS (

	SELECT * FROM 
		(SELECT 
			CAST(1 AS INTEGER) AS grupo , 
			ocd.cod_estrutural , 
			ocd.descricao , 
			publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
			COALESCE(CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)), 0.00 ) as dotacao_atualizada , 
			COALESCE((SELECT * FROM stn.fn_anexo14_despesas_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS despesas_empenhadas , 
                        COALESCE((SELECT * FROM stn.fn_anexo14_despesas_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS liquidado_ate_bimestre ,
                        COALESCE((select SUM(pago) from tmp_rreo_despesa where tmp_rreo_despesa.cod_estrutural= ocd.cod_estrutural), 0.00) AS pago,
                        COALESCE(( SELECT SUM(valor_resto) FROM tmp_resto WHERE tmp_resto.cod_estrutural = ocd.cod_estrutural), 0.00) as resto
		FROM 
			orcamento.conta_despesa ocd 
			LEFT JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' AND
			    tmp.cod_recurso IN (' || stCodRecursos || ') AND 
			    substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''4%'' AND
			publico.fn_nivel(ocd.cod_estrutural) < 3 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND
			ocd.exercicio = ' || quote_literal(stExercicio) || '
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl
		
	UNION ALL
	
	SELECT * FROM 
		(SELECT 
			CAST(2 AS INTEGER) AS grupo , 
			ocd.cod_estrutural , 
			CAST ( ''Despesas Correntes dos Regimes de Previdência'' as varchar ) as descricao,
			publico.fn_nivel(ocd.cod_estrutural) AS nivel , 
			COALESCE(CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)), 0.00) as dotacao_atualizada ,
			COALESCE((SELECT * FROM stn.fn_anexo14_despesas_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS despesas_empenhadas , 
                        COALESCE((SELECT * FROM stn.fn_anexo14_despesas_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS liquidado_ate_bimestre,
                        COALESCE((select SUM(pago) from tmp_rreo_despesa where tmp_rreo_despesa.cod_estrutural= ocd.cod_estrutural), 0.00) AS pago,
                        COALESCE(( SELECT SUM(valor_resto) FROM tmp_resto WHERE tmp_resto.cod_estrutural = ocd.cod_estrutural), 0.00) as resto
		FROM 
			orcamento.conta_despesa ocd 
			LEFT JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' AND
			    tmp.cod_recurso IN (' || stCodRecursos || ') AND 
			    substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3%'' AND 
			publico.fn_nivel(ocd.cod_estrutural) < 2 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND
			ocd.exercicio = '|| quote_literal(stExercicio) ||' 
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl
		
	UNION ALL
	
	SELECT * FROM 
		(SELECT 
			CAST(2 AS INTEGER) AS grupo , 
			CAST ( ''3.X.0.0.00.00.00.00.00'' as varchar ) as cod_estrutural , 
			CAST ( ''Regime Geral de Previdência Social'' as varchar ) as descricao,
			2 AS nivel , 
			COALESCE(CAST(SUM((tmp.vl_original + tmp.vl_suplementacoes)) AS numeric(14,2)), 0.00 )as dotacao_atualizada ,                        
			COALESCE((SELECT * FROM stn.fn_anexo14_despesas_empenhada( publico.fn_mascarareduzida(ocd.cod_estrutural), '|| quote_literal(stExercicio) ||', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS despesas_empenhadas , 
                        COALESCE((SELECT * FROM stn.fn_anexo14_despesas_liquidada( publico.fn_mascarareduzida(ocd.cod_estrutural), ' || quote_literal(stExercicio) || ', ''' ||  stCodEntidades ||''', '''|| stCodRecursos ||''', '''||dtIniExercicio||''', '''||dtFinal||''', false )), 0.00) AS liquidado_ate_bimestre,
                        COALESCE((select SUM(pago) from tmp_rreo_despesa where tmp_rreo_despesa.cod_estrutural= ocd.cod_estrutural), 0.00) AS pago,
                        COALESCE(( SELECT SUM(valor_resto) FROM tmp_resto WHERE tmp_resto.cod_estrutural  = ocd.cod_estrutural), 0.00) as resto
		FROM 
			orcamento.conta_despesa ocd 
			LEFT JOIN tmp_despesa tmp ON 
				ocd.exercicio = tmp.exercicio AND 
				tmp.cod_estrutural LIKE publico.fn_mascarareduzida(ocd.cod_estrutural) ||''%'' AND
			    tmp.cod_recurso IN (' || stCodRecursos || ') AND 
			    substring(tmp.cod_estrutural, 5, 3) <> ''9.1'' 
		WHERE 
			publico.fn_mascarareduzida(ocd.cod_estrutural) like ''3%'' AND 
			publico.fn_nivel(ocd.cod_estrutural) < 2 AND 
			-- Exceto intra
			substring(ocd.cod_estrutural, 5, 3) <> ''9.1'' AND
			ocd.exercicio = '|| quote_literal(stExercicio) ||'
		GROUP BY 
			ocd.cod_estrutural , 
			ocd.descricao 
		ORDER BY 
			ocd.cod_estrutural
		) AS tbl
		
	UNION ALL
	
	SELECT
	        CAST(2 AS INTEGER) AS grupo , 
		    CAST ( ''3.Y.0.0.00.00.00.00.00'' as varchar ) as cod_estrutural , 
			CAST ( ''Regime Próprio dos Servidores Públicos'' as varchar ) as descricao,
			2 AS nivel , 
			''0.00'' AS dotacao_atualizada ,
			''0.00'' AS despesas_empenhadas , 
                        ''0.00'' AS liquidado_ate_bimestre,
                         ''0.00'' AS pago,
                         ''0.00'' AS resto
		
	) 
	';
	
	EXECUTE stSql;
		
	stSql := 'SELECT * FROM tmp_rreo_anexo11_despesa ORDER BY grupo, nivel';
	
  
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;


	DROP TABLE tmp_suplementacao_suplementada ; 
	DROP TABLE tmp_suplementacao_reduzida ; 
	DROP TABLE tmp_despesa_totais ; 
	DROP TABLE tmp_despesa ; 
	DROP TABLE tmp_rreo_anexo11_despesa ; 
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_estornado_liquidacao;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;
    DROP TABLE tmp_resto;
    DROP TABLE tmp_rreo_despesa_liquidada_total;
    DROP TABLE tmp_rreo_despesa;

    RETURN;
 
END;
   
$$ language 'plpgsql';

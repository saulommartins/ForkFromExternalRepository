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
CREATE OR REPLACE FUNCTION contabilidade.fn_recupera_despesa_paga(stMascRed VARCHAR, stExercicio VARCHAR, stEntidades VARCHAR, stDtIni VARCHAR, stDtFim VARCHAR, boIntra BOOLEAN) RETURNS NUMERIC(14,2) AS 
$$

DECLARE 
	stSQL 		VARCHAR;
	reReg 		RECORD;
	nuPago      NUMERIC(14,2);
	nuEstornado	NUMERIC(14,2);
	nuTotal 	NUMERIC(14,2);
    crCursor 	REFCURSOR;

BEGIN 


/*
	stSql := '
	SELECT coalesce(sum(nlp.vl_pago), 0.00) as vl_total 
	  FROM empenho.pre_empenho pe 

 LEFT JOIN ( SELECT 
                ped.exercicio, 
                ped.cod_pre_empenho, 
                cd.cod_estrutural
                
		   FROM orcamento.conta_despesa cd
           
	 INNER JOIN empenho.pre_empenho_despesa ped
             ON ped.cod_conta   = cd.cod_conta
            AND ped.exercicio   = cd.exercicio 
	 
     INNER JOIN orcamento.despesa d
             ON ped.cod_despesa = d.cod_despesa
            AND ped.exercicio   = d.exercicio
            
		WHERE ped.exercicio = ''' || stExercicio || ''' 

		) AS pedcd
          ON pe.exercicio       = pedcd.exercicio
         AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
         
		INNER JOIN empenho.empenho e
                ON e.exercicio = pe.exercicio
               AND e.cod_pre_empenho = pe.cod_pre_empenho
               
		INNER JOIN empenho.nota_liquidacao nl
                ON nl.exercicio_empenho = e.exercicio
               AND nl.cod_entidade      = e.cod_entidade
               AND nl.cod_empenho       = e.cod_empenho
               
		INNER JOIN empenho.nota_liquidacao_paga nlp
                ON nlp.exercicio    = nl.exercicio
               AND nlp.cod_entidade = nl.cod_entidade
               AND nlp.cod_nota     = nl.cod_nota
        
		INNER JOIN empenho.nota_liquidacao_item nli
                ON nli.exercicio    = nl.exercicio
               AND nli.cod_entidade = nl.cod_entidade
               AND nli.cod_nota     = nl.cod_nota
               
            WHERE e.exercicio = ''' || stExercicio || '''
              AND e.cod_entidade IN (' || stEntidades || ')
              AND nl.dt_liquidacao BETWEEN to_date(''' || stDtIni || ''', ''dd/mm/yyyy'') AND to_date(''' || stDtFim || ''', ''dd/mm/yyyy'')
              AND pedcd.cod_estrutural like ''' || stMascRed || '%''
        ';
		
	-- não inclui as despesas intra-orçamentárias	
	IF (boIntra = FALSE) THEN 
		stSql := stSql || ' AND SUBSTRING(pedcd.cod_estrutural, 5, 3) <> ''9.1''  ';
	END IF;

    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuPago;
    CLOSE crCursor;


	stSQL := '
	SELECT coalesce(sum(nlpa.vl_anulado), 0.00) as valor 
 	 FROM empenho.pre_empenho pe 
LEFT JOIN ( SELECT 
                ped.exercicio, 
                ped.cod_pre_empenho, 
                cd.cod_estrutural
                
		   FROM orcamento.conta_despesa cd 
	 
     INNER JOIN empenho.pre_empenho_despesa ped
             ON ped.cod_conta = cd.cod_conta
            AND ped.exercicio = cd.exercicio 
	 
     INNER JOIN orcamento.despesa d
             ON ped.cod_despesa = d.cod_despesa
            AND ped.exercicio   = d.exercicio
            
          WHERE ped.exercicio = ''' || stExercicio || ''' 

		) AS pedcd
          ON pe.exercicio = pedcd.exercicio
         AND pe.cod_pre_empenho = pedcd.cod_pre_empenho
            
		INNER JOIN empenho.empenho e
                ON e.exercicio       = pe.exercicio
               AND e.cod_pre_empenho = pe.cod_pre_empenho
               
		INNER JOIN empenho.nota_liquidacao nl
                ON e.exercicio    = nl.exercicio_empenho
               AND e.cod_entidade = nl.cod_entidade
               AND e.cod_empenho  = nl.cod_empenho
               
		INNER JOIN empenho.nota_liquidacao_paga nlp
                ON nlp.exercicio    = nl.exercicio
               AND nlp.cod_entidade = nl.cod_entidade
               AND nlp.cod_nota     = nl.cod_nota
               
		INNER JOIN empenho.nota_liquidacao_paga_anulada nlpa
                ON nlpa.exercicio    = nlp.exercicio
               AND nlpa.cod_entidade = nlp.cod_entidade
               AND nlpa.cod_nota     = nlp.cod_nota
               AND nlpa.timestamp    = nlp.timestamp
               
		INNER JOIN  empenho.nota_liquidacao_item nli
                ON nl.exercicio    = nli.exercicio
               AND nl.cod_nota     = nli.cod_nota
               AND nl.cod_entidade = nli.cod_entidade
               
		INNER JOIN empenho.nota_liquidacao_item_anulado nlia
                ON nli.exercicio = nlia.exercicio
               AND nli.cod_nota = nlia.cod_nota
               AND nli.cod_entidade = nlia.cod_entidade
               AND nli.num_item = nlia.num_item
               AND nli.cod_pre_empenho = nlia.cod_pre_empenho
               AND nli.exercicio_item = nlia.exercicio_item
               
	WHERE e.exercicio = ''' || stExercicio || '''
      AND e.cod_entidade IN (' || stEntidades || ')
      AND to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtIni || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFim || ''', ''dd/mm/yyyy'')
      AND pedcd.cod_estrutural like ''' || stMascRed || '%''
    ';
	*/
    
    stSql := 'SELECT ( SUM(liquidacao_paga.vl_total) ) AS vl_total
           --, empenho.cod_empenho
           --, empenho.cod_entidade
           --, sw_cgm.nom_cgm AS nom_entidade
           --
           --, conta_despesa.cod_estrutural
           --, conta_despesa.descricao
           --,''tmp_nao_processados_pago''::VARCHAR AS tipo           
           
        FROM empenho.nota_liquidacao
        
  INNER JOIN empenho.empenho
          ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
         AND empenho.cod_entidade = nota_liquidacao.cod_entidade
         AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
  
  INNER JOIN empenho.pre_empenho
          ON pre_empenho.exercicio       = empenho.exercicio
         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
  
  INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                     , nota_liquidacao_paga.cod_entidade
                     , nota_liquidacao_paga.cod_nota
                     , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
  
                  FROM (  SELECT nota_liquidacao_paga.exercicio
                               , nota_liquidacao_paga.cod_entidade
                               , nota_liquidacao_paga.cod_nota
                               , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                            FROM empenho.nota_liquidacao_paga
                           WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||stDtIni||''',''dd/mm/yyyy'') AND TO_DATE('''||stDtFim||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga.exercicio
                               , nota_liquidacao_paga.cod_entidade
                               , nota_liquidacao_paga.cod_nota
                       ) AS nota_liquidacao_paga
  
             LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                               , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                            FROM empenho.nota_liquidacao_paga_anulada
                           WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,''yyyy-mm-dd'') BETWEEN TO_DATE('''||stDtIni||''',''dd/mm/yyyy'') AND TO_DATE('''||stDtFim||''',''dd/mm/yyyy'')
                        GROUP BY nota_liquidacao_paga_anulada.exercicio
                               , nota_liquidacao_paga_anulada.cod_entidade
                               , nota_liquidacao_paga_anulada.cod_nota
                       ) AS nota_liquidacao_paga_anulada
                    ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                   AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                   AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
              GROUP BY nota_liquidacao_paga.exercicio
                     , nota_liquidacao_paga.cod_entidade
                     , nota_liquidacao_paga.cod_nota
  
             ) AS liquidacao_paga
             
          ON liquidacao_paga.exercicio    = nota_liquidacao.exercicio
         AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
         AND liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota

-- inner para achar a entidade a que ele pertence
  INNER JOIN orcamento.entidade
          ON entidade.exercicio = empenho.exercicio
         AND entidade.cod_entidade = empenho.cod_entidade
  
  INNER JOIN sw_cgm
          ON sw_cgm.numcgm = entidade.numcgm

--left para achar o cod_estrutural
   LEFT JOIN empenho.pre_empenho_despesa
          ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
         AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
    
   LEFT JOIN orcamento.despesa
          ON despesa.exercicio = pre_empenho_despesa.exercicio
         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         
   LEFT JOIN orcamento.conta_despesa
          ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
         AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

   LEFT JOIN empenho.restos_pre_empenho
          ON restos_pre_empenho.exercicio = pre_empenho.exercicio
         AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
   
	WHERE empenho.exercicio = ''' || stExercicio || '''
      AND empenho.cod_entidade IN (' || stEntidades || ')
      AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE(''' || stDtIni || ''',''dd/mm/yyyy'') AND TO_DATE(''' || stDtFim || ''',''dd/mm/yyyy'')
      AND conta_despesa.cod_estrutural like ''' || stMascRed || '%''
';
    
	-- não inclui as despesas intra-orçamentárias	
	IF (boIntra = FALSE) THEN 
		stSql := stSql || ' AND SUBSTRING(conta_despesa.cod_estrutural, 5, 3) <> ''9.1''  ';
	END IF;
	
    OPEN crCursor FOR EXECUTE stSql;
    	FETCH crCursor INTO nuPago;
    CLOSE crCursor;
    

	--nuTotal := nuPago - nuEstornado;
    nuTotal := nuPago;

	if (nuTotal is null) then 
		nuTotal := 0.00;
	end if;

    RETURN nuTotal;

	--RETURN nuEstornado;
END;

$$ language 'plpgsql';

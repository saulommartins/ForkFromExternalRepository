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
CREATE OR REPLACE FUNCTION stn.fn_rgf_despesa_empenhada_anexo1(stDtInicial VARCHAR, stDtFinal VARCHAR, stEntidades VARCHAR, stCondicao VARCHAR) RETURNS NUMERIC(14,2) AS 
$$

DECLARE 
	stSQL 	    	VARCHAR;
	reRegistro 		RECORD;
	nuTotal     	NUMERIC(14,2);
BEGIN 

	stSql := '
        select
            coalesce(sum(coalesce(item_pre_empenho.vl_total,0.00))-sum(coalesce(anulado.vl_anulado,0.00)),0.00) as vl_final
        from
             orcamento.conta_despesa
            ,empenho.pre_empenho_despesa
            ,empenho.pre_empenho
            ,empenho.item_pre_empenho
        left join
        (
            select 
                 cod_pre_empenho
                ,exercicio
                ,num_item
                ,sum(vl_anulado) as vl_anulado
            from
                empenho.empenho_anulado_item
            where
                to_date(timestamp::varchar,''yyyy-mm-dd'') between to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') and to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'')             
            group by
                 cod_pre_empenho
                ,exercicio
                ,num_item
        ) as anulado
        on
            anulado.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
        and anulado.exercicio       = item_pre_empenho.exercicio
        and anulado.num_item        = item_pre_empenho.num_item
        ,empenho.empenho
        where
            conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
        and conta_despesa.exercicio = pre_empenho_despesa.exercicio

        and pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
        and pre_empenho_despesa.exercicio       = pre_empenho.exercicio

        and pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
        and pre_empenho.exercicio       = empenho.exercicio

        and not exists
        (
            select
                1
            from
                 empenho.nota_liquidacao
                ,empenho.nota_liquidacao_paga
            where
                    nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                and nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                and nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota
                
                and nota_liquidacao.exercicio = empenho.exercicio
                and nota_liquidacao.cod_entidade = empenho.cod_entidade
                and nota_liquidacao.cod_empenho = empenho.cod_empenho
        )


        and pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
        and pre_empenho.exercicio       = item_pre_empenho.exercicio


        and dt_empenho between to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') and to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'')
        and ' || stCondicao || '
		and empenho.cod_entidade IN (' || stEntidades || ')

    ';
		
        
    FOR reRegistro IN EXECUTE stSql
    LOOP
        nuTotal := reRegistro.vl_final;
    END LOOP;

    RETURN nuTotal;

END;

$$ LANGUAGE 'plpgsql';

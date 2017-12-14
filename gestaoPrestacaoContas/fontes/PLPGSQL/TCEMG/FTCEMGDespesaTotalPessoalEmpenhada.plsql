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
CREATE OR REPLACE FUNCTION tcemg.fn_despesa_total_pessoal_empenhada(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS NUMERIC(14,2) AS $$
DECLARE 
    stDtInicial  ALIAS FOR $1;
    stDtFinal    ALIAS FOR $2;
    stEntidades  ALIAS FOR $3;
    stCondicao   ALIAS FOR $4;

    stSQL 	 VARCHAR;
    reRegistro   RECORD;
    nuTotal      NUMERIC(14,2);
BEGIN 
--Pl base anexo1

    stSql := '
        SELECT coalesce(sum(coalesce(item_pre_empenho.vl_total,0.00))-sum(coalesce(anulado.vl_anulado,0.00)),0.00) as vl_final
          FROM orcamento.conta_despesa
             , empenho.pre_empenho_despesa
             , empenho.pre_empenho
             , empenho.item_pre_empenho
        LEFT JOIN
        ( SELECT cod_pre_empenho
               , exercicio
               , num_item
               , sum(vl_anulado) as vl_anulado
            FROM empenho.empenho_anulado_item
            WHERE to_date(timestamp::varchar,''yyyy-mm-dd'') between to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') and to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'')             
        GROUP BY cod_pre_empenho
               , exercicio
               , num_item
        ) AS anulado
        ON anulado.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
       AND anulado.exercicio       = item_pre_empenho.exercicio
       AND anulado.num_item        = item_pre_empenho.num_item
       , empenho.empenho
        
      WHERE conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
        AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

        AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
        AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio

        AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
        AND pre_empenho.exercicio       = empenho.exercicio

        AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
        AND pre_empenho.exercicio       = item_pre_empenho.exercicio

        AND dt_empenho between to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''', ''dd/mm/yyyy'')
        AND ' || stCondicao || '
	AND empenho.cod_entidade IN (' || stEntidades || ') ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        nuTotal := reRegistro.vl_final;
    END LOOP;

    RETURN nuTotal;
END;

$$ LANGUAGE 'plpgsql';

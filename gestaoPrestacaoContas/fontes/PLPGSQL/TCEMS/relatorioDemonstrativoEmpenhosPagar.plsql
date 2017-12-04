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
CREATE OR REPLACE FUNCTION empenho.fn_demonstrativo_empenhos_pagar(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$

DECLARE
    stFiltro                        ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    stExercicio                     ALIAS FOR $3;
    stDataInicial                   ALIAS FOR $4;
    stDataFinal                     ALIAS FOR $5;
    stDataSituacao                  ALIAS FOR $6;
    inCodEmpenhoInicial             ALIAS FOR $7;
    inCodEmpenhoFinal               ALIAS FOR $8;
    inCGM                           ALIAS FOR $9;
    inNumOrgao                      ALIAS FOR $10;
    inOrdenacao                     ALIAS FOR $11;
    stCodRecurso                    ALIAS FOR $12;
    stDestinacaoRecurso             ALIAS FOR $13;
    
    stSql                           VARCHAR   := '';
    reRegistro                      RECORD;

BEGIN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhos_pagar AS (

		SELECT
                    e.cod_empenho                                               as cod_empenho,
                    e.cod_empenho ||''/''|| e.exercicio                         as cod_empenho_exercicio,
                    e.exercicio                                                 as exercicio,
                    e.cod_entidade                                              as cod_entidade,
		    lpad(despesa.num_orgao, 2, ''0'') || ''.'' || lpad(despesa.num_unidade, 3, ''0'') || ''.'' || lpad(despesa.cod_funcao, 2, ''0'') || ''.'' || lpad(despesa.cod_subfuncao, 2, ''0'') || ''.'' ||  lpad(despesa.cod_programa, 4, ''0'') || ''.'' || lpad(despesa.num_pao, 4, ''0'') as classificacao,
		    cd.cod_estrutural                                           as elemento,
		    lpad(despesa.cod_recurso, 2, ''0'')                         as fonte,
		    substring(tipo_empenho.nom_tipo from 1 for 1)               as nom_tipo,
		    cd.descricao                                       as descricao,
		    retorno.apagarliquidado                                     as processados,
		    abs((retorno.empenhado - retorno.liquidado)) as nao_processados,
		    retorno.apagar                                              as total
		  FROM empenho.fn_relatorio_empenhos_a_pagar(
		    '''||stFiltro||'''             -- filtro
		  , '''||stCodEntidades||'''       -- cod_entidade
		  , '''||stExercicio||'''          -- exercicio_empenho
		  , '''||stDataInicial||'''        -- data_inicial
		  , '''||stDataFinal||'''          -- data_final
		  , '''||stDataSituacao||'''       -- data_situacao
		  , '''||inCodEmpenhoInicial||'''  -- cod_empenho_inicial
		  , '''||inCodEmpenhoFinal||'''    -- cod_empenho_final
		  , '''||inCGM||'''                -- cgm
		  , '''||inNumOrgao||'''           -- num_orgao
		  , '''||inOrdenacao||'''          -- ordenacao 1-empenho 2-credor
		  , '''||stCodRecurso||'''         -- cod_recurso
		  , '''||stDestinacaoRecurso||'''  -- destinacao_recurso
		  ) as retorno (                                                                              
		    cod_entidade         integer,                                                            
		    cod_empenho          integer,                                                            
		    exercicio            char(4),                                                            
		    dt_emissao           text,                                                               
		    cgm                  integer,                                                            
		    credor               varchar,                                                            
		    empenhado            numeric,                                                            
		    liquidado            numeric,                                                            
		    pago                 numeric,                                                            
		    apagar               numeric,                                                            
		    apagarliquidado      numeric,                                                            
		    cod_recurso          integer,                                                            
		    nom_recurso          varchar,                                                            
		    masc_recurso_red     varchar)

		INNER JOIN empenho.empenho AS e
			ON e.cod_empenho   = retorno.cod_empenho
		       AND e.exercicio     = retorno.exercicio
		       AND e.cod_entidade  = retorno.cod_entidade    

		INNER JOIN empenho.pre_empenho
			ON pre_empenho.cod_pre_empenho = e.cod_pre_empenho
		       AND pre_empenho.exercicio       = retorno.exercicio

		INNER JOIN empenho.tipo_empenho
			ON tipo_empenho.cod_tipo = pre_empenho.cod_tipo       

		INNER JOIN empenho.pre_empenho_despesa
			ON pre_empenho_despesa.cod_pre_empenho = e.cod_pre_empenho
		       AND pre_empenho_despesa.exercicio       = retorno.exercicio

		INNER JOIN orcamento.despesa
			ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
		       AND despesa.exercicio   = retorno.exercicio

		INNER JOIN orcamento.unidade
			ON unidade.num_unidade = despesa.num_unidade
		       AND unidade.exercicio   = retorno.exercicio

		INNER JOIN orcamento.conta_despesa as cd
			ON cd.cod_conta = despesa.cod_conta
		       AND cd.exercicio = retorno.exercicio

		ORDER BY  e.cod_empenho )';
                
    EXECUTE stSql;
    
    stSql := 'SELECT
                    cod_empenho::integer,
                    cod_empenho_exercicio::text,
                    exercicio::varchar,
                    cod_entidade::integer,
		    classificacao::text,
		    elemento::varchar,
		    fonte::text,    
		    nom_tipo::text,
		    descricao::text,
		    processados::numeric,
		    nao_processados::numeric,
		    total::numeric,
                    (SELECT SUM(tmp_empenhos_pagar.total)
                       FROM tmp_empenhos_pagar
                      WHERE tmp_empenhos_pagar.cod_empenho  <= tep.cod_empenho
                        AND tmp_empenhos_pagar.exercicio     = tep.exercicio
                        AND tmp_empenhos_pagar.cod_entidade  = tep.cod_entidade
                        AND tmp_empenhos_pagar.elemento      = tep.elemento
                    ) as total_elemento
                    
                FROM tmp_empenhos_pagar as tep';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP; 

    DROP TABLE tmp_empenhos_pagar;

    RETURN;
END;

$$language 'plpgsql';

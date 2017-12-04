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
    * PL do TCEMG AnexoI - Arquivo TCEMG da GPC 
    * Data de Criação   : 18/07/2014

    * @author Analista:      Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes
    
    $Id: FTCEMGDemonstrativoRCLDespesa.plsql 61205 2014-12-16 12:31:49Z evandro $
*/
CREATE OR REPLACE FUNCTION tcemg.fn_relatorio_demostrativo_rcl_despesa(VARCHAR, VARCHAR, VARCHAR, VARCHAR, INTEGER, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE 
    stExercicio    ALIAS FOR $1;
    stEntidades    ALIAS FOR $2;
    stDtIni        ALIAS FOR $3;
    stDtFim        ALIAS FOR $4;
    -- 1 para DESPESAS
    -- 2 para DEDUÇÕES DE DESPESA
    inTipoDados    ALIAS FOR $5;
    stTipoSituacao ALIAS FOR $6;
    
    inExercicio    INTEGER;
    inCountNivel   INTEGER := 0;
    stSql          VARCHAR := '';
    reReg	   RECORD;
    
BEGIN 
    inExercicio :=  substr(stDtFim, 7, 4 ) ;
    
    IF ( stTipoSituacao = 'empenhado' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_valor_despesa AS (
                    SELECT
                          cd.cod_estrutural as cod_estrutural
                        , coalesce(ipe.vl_total,0.00) as valor
                        , e.dt_empenho as data
                    FROM
                          orcamento.despesa           as od
                        , orcamento.conta_despesa     as cd
                        , empenho.pre_empenho_despesa as ped
                        , empenho.empenho             as e
                        , empenho.pre_empenho         as pe
                        , empenho.item_pre_empenho    as ipe
                    WHERE
                            cd.cod_conta               = ped.cod_conta
                        AND cd.exercicio               = ped.exercicio
            
                        And od.cod_despesa             = ped.cod_despesa
                        AND od.exercicio               = ped.exercicio
            
                        And pe.exercicio               = ped.exercicio
                        And pe.cod_pre_empenho         = ped.cod_pre_empenho
            
                        And e.cod_entidade             IN (' || stEntidades || ')
                        And e.exercicio                = ' || quote_literal(stExercicio) || '
            
                        AND e.exercicio                = pe.exercicio
                        AND e.cod_pre_empenho          = pe.cod_pre_empenho
            
                        AND pe.exercicio               = ipe.exercicio
                        AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
                        
                        And od.exercicio IN ('''||inExercicio||''', '''||inExercicio-1||''')
                  );';
            EXECUTE stSql;
    END IF;
            
    IF ( stTipoSituacao = 'liquidado' ) THEN
        -- criando a tabela temporaria com os dados necessário.             
        stSql := 'CREATE TEMPORARY TABLE tmp_valor_despesa AS (
                    SELECT
                          cd.cod_estrutural as cod_estrutural
                        , nli.vl_total as valor
                        , nl.dt_liquidacao as data                       
                    FROM
                        orcamento.despesa             as od,
                        orcamento.conta_despesa       as cd,
                        empenho.pre_empenho_despesa   as ped,
                        empenho.pre_empenho           as pe,
                        empenho.empenho               as e,
                        empenho.nota_liquidacao_item  as nli,
                        empenho.nota_liquidacao       as nl
                    WHERE
                            cd.cod_conta               = ped.cod_conta
                        AND cd.exercicio               = ped.exercicio
        
                        And od.cod_despesa              = ped.cod_despesa
                        AND od.exercicio                = ped.exercicio
        
                        And pe.exercicio               = ped.exercicio
                        And pe.cod_pre_empenho         = ped.cod_pre_empenho
        
                        And e.cod_entidade             IN (' || stEntidades || ')
                        And e.exercicio                = ' || quote_literal(stExercicio) || '
        
                        AND e.exercicio                = pe.exercicio
                        AND e.cod_pre_empenho          = pe.cod_pre_empenho
        
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
                        AND e.cod_empenho = nl.cod_empenho
        
                        AND nl.exercicio = nli.exercicio
                        AND nl.cod_nota = nli.cod_nota
                        AND nl.cod_entidade = nli.cod_entidade
                        
                        And od.exercicio IN ('''||inExercicio||''', '''||inExercicio-1||''')
                        And nl.cod_entidade IN ('||stEntidades||')
                 );';
        EXECUTE stSql;
        
    END IF;
    
  	-- -------------------------------------	
	-- Estrutura de Tabelas Temporarias
	-- -------------------------------------

	-- Tabela tmp_tcemg_demostrativo_rcl_despesa
	-- Formatação e Cálculos Agrupados para Exibição de Resultados

    stSQL := '
    CREATE TEMPORARY TABLE tmp_tcemg_demostrativo_rcl_despesa (
	   cod_conta      varchar
       , nom_conta      varchar
       , cod_estrutural varchar
       , mes_1          numeric(14,2)
       , mes_2          numeric(14,2)
       , mes_3          numeric(14,2)
       , mes_4          numeric(14,2)
       , mes_5          numeric(14,2)
       , mes_6          numeric(14,2)
       , mes_7          numeric(14,2)
       , mes_8          numeric(14,2)
       , mes_9          numeric(14,2)
       , mes_10         numeric(14,2)
       , mes_11         numeric(14,2)
       , mes_12         numeric(14,2)
       , total          numeric(14,2)
    );
    ';
	
    EXECUTE stSQL ; 	

    IF inTipoDados = 1 THEN
        /* Calculando os valores referente a conta 3.0.0.0.00.00.00  nivel 2 */
        /* OBS.: O calculo está sendo feito apartir da conta 3.1.0.0.00.00.00  nivel 3 */
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl_despesa
                 SELECT 1 AS cod_conta
                      , ''DESPESAS CORRENTES'' AS nom_conta
                      , ''3.0.0.0.00.00.00.00.00'' AS cod_estrutural
                      , mes_1
                      , mes_2
                      , mes_3
                      , mes_4
                      , mes_5
                      , mes_6
                      , mes_7
                      , mes_8
                      , mes_9
                      , mes_10
                      , mes_11
                      , mes_12
                      , total
                   FROM tcemg.sub_consulta_despesa_rcl_novo('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1'', 3)
                               AS retorno ( cod_conta      VARCHAR
                                          , nom_conta      VARCHAR
                                          , cod_estrutural VARCHAR
                                          , mes_1          NUMERIC
                                          , mes_2          NUMERIC
                                          , mes_3          NUMERIC
                                          , mes_4          NUMERIC
                                          , mes_5          NUMERIC
                                          , mes_6          NUMERIC
                                          , mes_7          NUMERIC
                                          , mes_8          NUMERIC
                                          , mes_9          NUMERIC
                                          , mes_10         NUMERIC
                                          , mes_11         NUMERIC
                                          , mes_12         NUMERIC
                                          , total          NUMERIC )
        ;';

        EXECUTE stSql;

        /* Calculando os valores referente a conta 3.1.0.0.00.00.00  nivel 3 */
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl_despesa
                SELECT *
                  FROM tcemg.sub_consulta_despesa_rcl_novo('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1'', 3)
                    AS retorno ( cod_conta      VARCHAR
                               , nom_conta      varchar
                               , cod_estrutural varchar
                               , mes_1          numeric
                               , mes_2          numeric
                               , mes_3          numeric
                               , mes_4          numeric
                               , mes_5          numeric
                               , mes_6          numeric
                               , mes_7          numeric
                               , mes_8          numeric
                               , mes_9          numeric
                               , mes_10         numeric
                               , mes_11         numeric
                               , mes_12         numeric
                               , total          NUMERIC )

        ; ';

        EXECUTE stSql;

    ELSIF inTipoDados = 2 THEN

        /* Calculando os valores referente a conta 3.1.9.0.01.01.00  nivel 7 */
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl_despesa
                SELECT *
                  FROM tcemg.sub_consulta_despesa_rcl_novo('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.01.01'', 7)
                    AS retorno ( cod_conta      VARCHAR
                               , nom_conta      varchar
                               , cod_estrutural varchar
                               , mes_1          numeric
                               , mes_2          numeric
                               , mes_3          numeric
                               , mes_4          numeric
                               , mes_5          numeric
                               , mes_6          numeric
                               , mes_7          numeric
                               , mes_8          numeric
                               , mes_9          numeric
                               , mes_10         numeric
                               , mes_11         numeric
                               , mes_12         numeric
                               , total          NUMERIC )
        ; ';

        EXECUTE stSql;

        /* Calculando os valores referente a conta 3.1.9.0.01.02.00  nivel 7 */
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl_despesa
                SELECT *
                  FROM tcemg.sub_consulta_despesa_rcl_novo('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.01.02'', 7)
                    AS retorno ( cod_conta      varchar
                               , nom_conta      varchar
                               , cod_estrutural varchar
                               , mes_1          numeric
                               , mes_2          numeric
                               , mes_3          numeric
                               , mes_4          numeric
                               , mes_5          numeric
                               , mes_6          numeric
                               , mes_7          numeric
                               , mes_8          numeric
                               , mes_9          numeric
                               , mes_10         numeric
                               , mes_11         numeric
                               , mes_12         numeric
                               , total          NUMERIC )
        ; ';

        EXECUTE stSql;

        /* Calculando os valores referente a conta 3.1.9.0.03.01.00  nivel 7 */
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl_despesa
                SELECT *
                  FROM tcemg.sub_consulta_despesa_rcl_novo('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.03.01'', 7)
                    AS retorno ( cod_conta      varchar
                               , nom_conta      varchar
                               , cod_estrutural varchar
                               , mes_1          numeric
                               , mes_2          numeric
                               , mes_3          numeric
                               , mes_4          numeric
                               , mes_5          numeric
                               , mes_6          numeric
                               , mes_7          numeric
                               , mes_8          numeric
                               , mes_9          numeric
                               , mes_10         numeric
                               , mes_11         numeric
                               , mes_12         numeric
                               , total          NUMERIC )
        ; ';

        EXECUTE stSql;

        /* Calculando os valores referente a conta 3.1.9.0.03.02.00  nivel 7 */
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl_despesa
                SELECT *
                  FROM tcemg.sub_consulta_despesa_rcl_novo('||quote_literal(stDtIni)||', '||quote_literal(stDtFim)||','||quote_literal(stExercicio)||','||quote_literal(stEntidades)||',''3.3.1.9.0.03.02'', 7)
                    AS retorno ( cod_conta      varchar
                               , nom_conta      varchar
                               , cod_estrutural varchar
                               , mes_1          numeric
                               , mes_2          numeric
                               , mes_3          numeric
                               , mes_4          numeric
                               , mes_5          numeric
                               , mes_6          numeric
                               , mes_7          numeric
                               , mes_8          numeric
                               , mes_9          numeric
                               , mes_10         numeric
                               , mes_11         numeric
                               , mes_12         numeric
                               , total          NUMERIC )
        ; ';

        EXECUTE stSql;

    END IF;
    
    stSql := ' SELECT cod_conta      
                    , nom_conta      
                    , SUBSTRING(REPLACE(cod_estrutural,''.'','''')::VARCHAR, 1, 8 )::VARCHAR AS cod_estrutural
                    , mes_1          
                    , mes_2          
                    , mes_3          
                    , mes_4          
                    , mes_5          
                    , mes_6          
                    , mes_7          
                    , mes_8          
                    , mes_9          
                    , mes_10         
                    , mes_11         
                    , mes_12         
                    , total
                 FROM tmp_tcemg_demostrativo_rcl_despesa
             ORDER BY cod_estrutural ASC;';
    
    FOR reReg IN EXECUTE stSql
	LOOP
		RETURN NEXT reReg;	
	END LOOP;
	
    DROP TABLE tmp_tcemg_demostrativo_rcl_despesa;
    DROP TABLE tmp_valor_despesa;
    
    RETURN;
END;
$$ LANGUAGE 'plpgsql';
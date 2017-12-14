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
    
    $Id: FTCEMGDemonstrativoRCL.plsql 64421 2016-02-19 12:14:17Z fabio $
*/
CREATE OR REPLACE FUNCTION tcemg.fn_relatorio_demostrativo_rcl(VARCHAR, VARCHAR, VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE 
    stExercicio ALIAS FOR $1;
    stEntidades ALIAS FOR $2;
    stDtIni     ALIAS FOR $3;
    stDtFim     ALIAS FOR $4;
    -- 1 para todas as receitas
    -- 2 para receitas de exclusão
    inTipoDados ALIAS FOR $5;
    
    inExercicio  INTEGER;
    inCountNivel INTEGER := 0;
    stSql VARCHAR := '';
    stCodContas VARCHAR := '';
    reReg		     RECORD;
    
BEGIN
    inExercicio :=  substr(stDtFim, 7, 4 ) ;
-- criando a tabela temporaria com os dados necessário.
    IF inTipoDados = 2 THEN
        SELECT ARRAY_TO_STRING( ARRAY(SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '7.%'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.2.1.0.29.01.00.00.00'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.2.1.0.29.07.00.00.00'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.2.1.0.29.09.00.00.00'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.2.1.0.29.11.00.00.00'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.2.1.0.29.17.00.00.00'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.2.1.0.29.18.00.00.00'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.2.1.0.29.19.00.00.00'
                                      UNION
                                      SELECT cod_conta FROM orcamento.conta_receita WHERE exercicio IN ((inExercicio)::VARCHAR, (inExercicio-1)::VARCHAR) AND cod_estrutural ILIKE '1.9.2.2.10.%'), ',') INTO stCodContas;
    END IF;

    stSql := '
        CREATE TEMPORARY TABLE tmp_valor AS (
            SELECT ocr.cod_estrutural as cod_estrutural
                 , lote.dt_lote as data
                 , vl.vl_lancamento as valor
                 , vl.oid as primeira
              FROM contabilidade.valor_lancamento      as vl
                 , orcamento.conta_receita             as ocr
                 , orcamento.receita                   as ore
                 , contabilidade.lancamento_receita    as lr
                 , contabilidade.lancamento            as lan
                 , contabilidade.lote                  as lote 
             WHERE ore.exercicio in ( '''||inExercicio||''', '''||inExercicio-1||''' )
               AND ore.cod_entidade    IN ('|| stEntidades ||')
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
               AND lan.cod_historico   != 800
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
               AND lote.tipo           = lan.tipo';

    IF inTipoDados = 2 THEN
    stSql := stSql || ' AND ore.cod_conta IN ( '||stCodContas||' )';
    END IF;

    stSql := stSql || '
             UNION
            SELECT ocr.cod_estrutural as cod_estrutural
                 , lote.dt_lote as data
                 , vl.vl_lancamento as valor
                 , vl.oid as segunda
              FROM contabilidade.valor_lancamento      as vl
                 , orcamento.conta_receita             as ocr
                 , orcamento.receita                   as ore
                 , contabilidade.lancamento_receita    as lr
                 , contabilidade.lancamento            as lan
                 , contabilidade.lote                  as lote 
             WHERE ore.exercicio in ( '''||inExercicio||''', '''||inExercicio-1||''' )
               AND ore.cod_entidade    IN ('|| stEntidades ||')
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
               AND lan.cod_historico   != 800
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
               AND lote.tipo           = lan.tipo ';

    IF inTipoDados = 2 THEN
    stSql := stSql || ' AND ore.cod_conta IN ( '||stCodContas||' )';
    END IF;

    stSql := stSql || '
        )
    ';

    EXECUTE stSql;
    
    
  	-- -------------------------------------	
	-- Estrutura de Tabelas Temporarias
	-- -------------------------------------

	-- Tabela tmp_tcemg_anI_receita
	-- Formatação e Cálculos Agrupados para Exibição de Resultados

	stSQL := '
    CREATE TEMPORARY TABLE tmp_tcemg_demostrativo_rcl (
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
    );
    ';
	
	EXECUTE stSQL ; 	

    /* Calculando os valores referente a conta 1.0.0.0.00.00.00  nivel 2 */
    stSql := '
            INSERT INTO tmp_tcemg_demostrativo_rcl
            SELECT *
              FROM tcemg.sub_consulta_receita_rcl_novo('''||stDtFim||''' ,''4.1'', 2,'||inTipoDados||')
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
                           , mes_12         numeric)
           ; ';

    EXECUTE stSql;

    /* Calculando os valores referente a conta 1.0.0.0.00.00.00  nivel 3 */
    stSql := '
            INSERT INTO tmp_tcemg_demostrativo_rcl
            SELECT *
              FROM tcemg.sub_consulta_receita_rcl_novo('''||stDtFim||''' ,''4.1'', 3,'||inTipoDados||')
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
                           , mes_12         numeric)

                         WHERE mes_12 <> 0.00
                            OR mes_11 <> 0.00
                            OR mes_10 <> 0.00
                            OR mes_9  <> 0.00
                            OR mes_8  <> 0.00
                            OR mes_7  <> 0.00
                            OR mes_6  <> 0.00
                            OR mes_5  <> 0.00
                            OR mes_4  <> 0.00
                            OR mes_3  <> 0.00
                            OR mes_2  <> 0.00
                            OR mes_1  <> 0.00
    ; ';

    EXECUTE stSql;
    
    
    --inCountNivel := 4;
    
    FOR inCountNivel IN 4..7 LOOP
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl
                SELECT *
                  FROM tcemg.sub_consulta_receita_rcl_novo('''||stDtFim||''' ,''4.1.7'', '||inCountNivel||','||inTipoDados||')
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
                               , mes_12         numeric)
                 WHERE mes_12 <> 0.00
                    OR mes_11 <> 0.00
                    OR mes_10 <> 0.00
                    OR mes_9  <> 0.00
                    OR mes_8  <> 0.00
                    OR mes_7  <> 0.00
                    OR mes_6  <> 0.00
                    OR mes_5  <> 0.00
                    OR mes_4  <> 0.00
                    OR mes_3  <> 0.00
                    OR mes_2  <> 0.00
                    OR mes_1  <> 0.00
            ; ';
        EXECUTE stSql;
    END LOOP;
    
    FOR inCountNivel IN 2..4 LOOP
        stSql := '
                INSERT INTO tmp_tcemg_demostrativo_rcl
                SELECT *
                  FROM tcemg.sub_consulta_receita_rcl_novo('''||stDtFim||''' ,''4.9'', '||inCountNivel||','||inTipoDados||')
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
                               , mes_12         numeric)
                 WHERE mes_12 <> 0.00
                    OR mes_11 <> 0.00
                    OR mes_10 <> 0.00
                    OR mes_9  <> 0.00
                    OR mes_8  <> 0.00
                    OR mes_7  <> 0.00
                    OR mes_6  <> 0.00
                    OR mes_5  <> 0.00
                    OR mes_4  <> 0.00
                    OR mes_3  <> 0.00
                    OR mes_2  <> 0.00
                    OR mes_1  <> 0.00
            ; ';
        EXECUTE stSql;
    END LOOP;
    
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
                 FROM tmp_tcemg_demostrativo_rcl
             ORDER BY cod_estrutural ASC;';
    
    FOR reReg IN EXECUTE stSql
	LOOP
		RETURN NEXT reReg;	
	END LOOP;
	
    DROP TABLE tmp_tcemg_demostrativo_rcl;
    DROP TABLE tmp_valor;
    
    RETURN;
END;
$$ LANGUAGE 'plpgsql';

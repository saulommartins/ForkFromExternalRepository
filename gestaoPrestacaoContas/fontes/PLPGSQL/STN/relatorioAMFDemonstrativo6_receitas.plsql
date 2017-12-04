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
    * Script de função PLPGSQL - Relatório STN - AMF - Demonstrativo VI
    * Data de Criação   : 25/06/2008


    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Henrique Girardi dos Santos
    
    * @package URBEM
    * @subpackage 

    * @ignore

    * Casos de uso : uc-06.01.36

    $Id: $
*/

CREATE OR REPLACE FUNCTION stn.fn_amf_demonstrativo6_receitas(varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    ALIAS FOR $1;
    stCodEntidades ALIAS FOR $2;
    stExercicio2   VARCHAR := '';
    stExercicio3   VARCHAR := '';
    stExercicio4   VARCHAR := '';
    dtInicial2     VARCHAR := '';
    dtFinal2       VARCHAR := '';
    dtInicial3     VARCHAR := '';
    dtFinal3       VARCHAR := '';
    dtInicial4     VARCHAR := '';
    dtFinal4       VARCHAR := '';
    stSql          VARCHAR := '';
    stDado         VARCHAR := '';
    reRegistro     RECORD;
BEGIN
    
    stExercicio2 := trim(to_char((to_number(stExercicio, '99999')-2), '99999'));
    stExercicio3 := trim(to_char((to_number(stExercicio, '99999')-3), '99999'));
    stExercicio4 := trim(to_char((to_number(stExercicio, '99999')-4), '99999'));
    
    dtInicial2 := '01/01/' || stExercicio2;
    dtFInal2   := '31/12/' || stExercicio2;
    
    dtInicial3 := '01/01/' || stExercicio3;
    dtFInal3   := '31/12/' || stExercicio3;
    
    dtInicial4 := '01/01/' || stExercicio4;
    dtFInal4   := '31/12/' || stExercicio4;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_valor AS (
        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as primeira
        FROM
            contabilidade.valor_lancamento      ,
            orcamento.conta_receita             ,
            orcamento.receita                   ,
            contabilidade.lancamento_receita    ,
            contabilidade.lancamento            ,
            contabilidade.lote                  
        WHERE

                receita.exercicio IN ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')';
            if ( stCodEntidades != '' ) then
               stSql := stSql ||' AND receita.cod_entidade = '|| quote_literal(stCodEntidades) ||' ';
            end if;

        stSql := stSql || '

            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio

            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = true
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''

            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo

            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''D''

            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo

        UNION

        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as segunda
        FROM
            contabilidade.valor_lancamento      ,
            orcamento.conta_receita             ,
            orcamento.receita                   ,
            contabilidade.lancamento_receita    ,
            contabilidade.lancamento            ,
            contabilidade.lote                  

        WHERE
            receita.exercicio  IN ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')';

            if ( stCodEntidades != '' ) then
               stSql := stSql || ' AND receita.cod_entidade    IN ('|| quote_literal(stCodEntidades) ||') ';
            end if;
        stSql := stSql || '

            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio


            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = false
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''

            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo

            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''C''

            -- Data Inicial e Data Final, antes iguala codigo do lote
            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo ) '; 

    EXECUTE stSql;
    
    stSql := '
        CREATE TEMPORARY TABLE tmp_receitas AS (
            SELECT
                    CAST(1 AS INTEGER) as grupo,
                    cod_estrutural,
                    CAST(1 AS INTEGER) as nivel,
                    CAST('''' AS VARCHAR) AS nom_conta,
                    coalesce(ano2,0.00)*-1 as ano2,
                    coalesce(ano3,0.00)*-1 as ano3,
                    coalesce(ano4,0.00)*-1 as ano4
            FROM(
                SELECT
                    plano_conta.cod_estrutural,
                    --plano_conta.nom_conta,
                    orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                             , '|| quote_literal(dtInicial2) ||'
                                                             , '|| quote_literal(dtFinal2)   ||'
                                                             
                    ) as ano2,
                    orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                             , '|| quote_literal(dtInicial3) ||'
                                                             , '|| quote_literal(dtFinal3)   ||'
                    ) as ano3,
                    orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                             , '|| quote_literal(dtInicial4) ||'
                                                             , '|| quote_literal(dtFinal4)   ||'
                    ) as ano4
                FROM
                    contabilidade.plano_conta,
                    orcamento.conta_receita
                    
                WHERE plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  
                    AND plano_conta.exercicio        = conta_receita.exercicio
                    AND (
                        (plano_conta.cod_estrutural ILIKE ''4.1.2%''
                         AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                        OR (plano_conta.cod_estrutural ILIKE ''4.1.2.1.0.01%''
                            AND publico.fn_nivel(plano_conta.cod_estrutural) = 8)
                        OR (plano_conta.cod_estrutural ILIKE ''4.1.2.1.0.29%''
                            AND publico.fn_nivel(plano_conta.cod_estrutural) = 7)
                        OR (plano_conta.cod_estrutural like ''4.1.2.1.0.99%''
                            AND publico.fn_nivel(plano_conta.cod_estrutural) = 8)
                            
                        OR (plano_conta.cod_estrutural ILIKE ''4.1.3%''
                            AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                            
                        OR (plano_conta.cod_estrutural ILIKE ''4.1.6%''
                            AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                        
                        OR (plano_conta.cod_estrutural ILIKE ''4.1.9%'')
                        
                        OR ((plano_conta.cod_estrutural ILIKE ''4.1.1%''
                            OR plano_conta.cod_estrutural ILIKE ''4.1.4%''
                            OR plano_conta.cod_estrutural ILIKE ''4.1.5%''
                            OR plano_conta.cod_estrutural ILIKE ''4.1.7%'')
                             AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                             
                        OR (plano_conta.cod_estrutural ILIKE ''4.2%''
                         AND publico.fn_nivel(plano_conta.cod_estrutural) = 3)
                        
                        OR ((plano_conta.cod_estrutural ILIKE ''4.7%''
                            OR plano_conta.cod_estrutural ILIKE ''4.8%''))
                            
                        OR (plano_conta.cod_estrutural ILIKE ''4.%''
                            AND publico.fn_nivel(plano_conta.cod_estrutural) = 2)
                        OR (plano_conta.cod_estrutural = ''5.1.2.1.7.99.00.00.00.00'')
                        
                        )
                     AND plano_conta.exercicio IN ('|| quote_literal(stExercicio2) ||', '|| quote_literal(stExercicio3) ||', '|| quote_literal(stExercicio4) ||')
                     
                GROUP BY    plano_conta.cod_estrutural,
                            --plano_conta.nom_conta,
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     , '|| quote_literal(dtInicial2) ||'
                                                                     , '|| quote_literal(dtFinal2)   ||'
                                                                     
                            ),
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     , '|| quote_literal(dtInicial3) ||'
                                                                     , '|| quote_literal(dtFinal3)   ||'
                            ),
                            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                                     , '|| quote_literal(dtInicial4) ||'
                                                                     , '|| quote_literal(dtFinal4)   ||'
                            )
            ) as tbl
            
            ORDER BY cod_estrutural
        )';
    
    EXECUTE stSql;
    
    -- INSERE OS DADOS DE PESSOA MILITAR NA TABELA TEMPORARIA    
    INSERT INTO tmp_receitas (grupo, cod_estrutural, nivel, nom_conta, ano2, ano3, ano4) VALUES (5, 'militar', 4, 'Pessoa Militar', 0.00, 0.00, 0.00);
    
    
    -- FAZ UMA VERIFICACAO SE EXISTE OS CODIGOS ESTRUTURAIS NA TABELA TEMPORARIA, CASO CONTRARIO, ADICIONA ELES.
    -- ISSO DEVE SER FEITO PARA QUE SEMPRE APAREÇA AS DESCRIÇÕES DAS RECEITAS PREVIDENCIARIAS NO RELATORIO
    -- POIS CASO CONTRARIO NÃO VAI SAIR AS DESCRIÇÕES SE NÃO HAVER DETERMINADO CODIGO EM ALGUM ANO
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.1.0.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.1.0.0.0.00.00.00.00.00', 1, 'RECEITAS CORRENTES', 0.00, 0.00, 0.00);
                      ELSE UPDATE tmp_receitas SET nom_conta = 'RECEITAS CORRENTES'         WHERE cod_estrutural = '4.1.0.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.1.2.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN  INSERT INTO tmp_receitas VALUES (1, '4.1.2.0.0.00.00.00.00.00', 1, 'Receita de Contribuições', 0.00, 0.00, 0.00);
                      ELSE  UPDATE tmp_receitas SET nom_conta = 'Receita de Contribuições'   WHERE cod_estrutural = '4.1.2.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.1.3.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.1.3.0.0.00.00.00.00.00', 1, 'Receita Patrimonial', 0.00, 0.00, 0.00);
                      ELSE UPDATE tmp_receitas SET nom_conta = 'Receita Patrimonial'        WHERE cod_estrutural = '4.1.3.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.1.6.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.1.6.0.0.00.00.00.00.00', 1, 'Receita de Serviços', 0.00, 0.00, 0.00);
                      ELSE UPDATE tmp_receitas SET nom_conta = 'Receitas de Serviços'       WHERE cod_estrutural = '4.1.6.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.1.6.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.1.6.0.0.00.00.00.00.00', 1, 'Receita de Serviços', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Receitas de Serviços'       WHERE cod_estrutural = '4.1.6.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.1.9.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.1.9.0.0.00.00.00.00.00', 1, 'Outras Receitas Correntes', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Outras Receitas Correntes'  WHERE cod_estrutural = '4.1.9.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.1.9.2.2.10.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.1.9.2.2.10.00.00.00.00', 1, 'Compensação Previdenciária do RGPS para o RPPS', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Compensação Previdenciária do RGPS para o RPPS' WHERE cod_estrutural = '4.1.9.2.2.10.00.00.00.00';
    END IF;
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.2.0.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.2.0.0.0.00.00.00.00.00', 1, 'RECEITAS DE CAPITAL', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'RECEITAS DE CAPITAL'  WHERE cod_estrutural = '4.2.0.0.0.00.00.00.00.00';
    END IF;
     
     
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.2.2.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.2.2.0.0.00.00.00.00.00', 1, 'Alienação de Bens', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Alienação de Bens' WHERE cod_estrutural = '4.2.2.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.2.3.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.2.3.0.0.00.00.00.00.00', 1, 'Amortização de Empréstimos', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Amortização de Empréstimos' WHERE cod_estrutural = '4.2.3.0.0.00.00.00.00.00';
    END IF;
    
    
    
    
    -- RECEITA INTRA-ORCAMENTARIA
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.7.0.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.7.0.0.0.00.00.00.00.00', 1, 'RECEITAS CORRENTES', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'RECEITAS CORRENTES' WHERE cod_estrutural = '4.7.0.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.7.2.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.7.2.0.0.00.00.00.00.00', 1, 'Receita de Contribuições', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Receita de Contribuições'   WHERE cod_estrutural = '4.7.2.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.8.2.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.8.2.0.0.00.00.00.00.00', 1, 'Alienação de Bens', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Alienação de Bens'          WHERE cod_estrutural = '4.8.2.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.8.3.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.8.3.0.0.00.00.00.00.00', 1, 'Amotização de Empréstimos', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Amotização de Empréstimos'  WHERE cod_estrutural = '4.8.3.0.0.00.00.00.00.00';
    END IF;
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.8.5.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.8.5.0.0.00.00.00.00.00', 1, 'Outras Receitas de Capital', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Outras Receitas de Capital' WHERE cod_estrutural = '4.8.5.0.0.00.00.00.00.00';
    END IF;
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.7.2.1.0.29.13.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.7.2.1.0.29.13.00.00.00', 1, 'Contribuição Previdenciária para Cobertura de Défcit Atuarial', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Contribuição Previdenciária para Cobertura de Défcit Atuarial' WHERE cod_estrutural = '4.7.2.1.0.29.13.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.7.3.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.7.3.0.0.00.00.00.00.00', 1, 'Receita Patrimonial', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Receita Patrimonial'        WHERE cod_estrutural = '4.7.3.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.7.9.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.7.9.0.0.00.00.00.00.00', 1, 'Outras Receitas Correntes', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'Outras Receitas Correntes'  WHERE cod_estrutural = '4.7.9.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '4.8.0.0.0.00.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '4.8.0.0.0.00.00.00.00.00', 1, 'RECEITAS DE CAPITAL', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'RECEITAS DE CAPITAL'  WHERE cod_estrutural = '4.8.0.0.0.00.00.00.00.00';
    END IF;
    
    
    SELECT nivel INTO stDado FROM tmp_receitas WHERE cod_estrutural = '5.1.2.1.7.99.00.00.00.00';
    IF stDado IS NULL THEN INSERT INTO tmp_receitas VALUES (1, '5.1.2.1.7.99.00.00.00.00', 1, 'OUTROS APORTES AO RPPS', 0.00, 0.00, 0.00);
                 ELSE UPDATE tmp_receitas SET nom_conta = 'OUTROS APORTES AO RPPS'  WHERE cod_estrutural = '5.1.2.1.7.99.00.00.00.00';
    END IF;
    
        
    
    stSql := '
        SELECT  cast(1 as integer) as grupo
            ,   cast('''' as varchar) as cod_estrutural
            ,   cast(1 as integer) as nivel
            ,   cast(''RECEITAS PREVIDENCIÁRIAS - RPPS (EXCETO INTRA-ORÇAMENTÁRIAS)'' as varchar) as nom_conta
            ,   COALESCE(SUM(ano2), 0.00) AS ano2
            ,   COALESCE(SUM(ano3), 0.00) AS ano3
            ,   COALESCE(SUM(ano4),0.00)*-1 AS ano4
        FROM    tmp_receitas AS receitas
        WHERE (cod_estrutural NOT ILIKE ''4.9%''
           OR  cod_estrutural NOT ILIKE ''9.%'')
          AND publico.fn_nivel(cod_estrutural) = 3
          
        UNION ALL
        
        SELECT
            publico.fn_nivel(cod_estrutural) as grupo,
            cod_estrutural,
            publico.fn_nivel(cod_estrutural) as nivel,
            CASE WHEN publico.fn_nivel(cod_estrutural) > 2 THEN
                INITCAP(nom_conta)
            ELSE nom_conta END AS nom_conta,
            COALESCE(ano2,0.00)*-1 as ano2,
            COALESCE(ano3,0.00)*-1 as ano3,
            COALESCE(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        
        WHERE (cod_estrutural ILIKE ''4.1.0%''
           OR  cod_estrutural ILIKE ''4.1.2%'')
          AND  publico.fn_nivel(cod_estrutural) < 4
          
        UNION ALL
    
        SELECT  cast(4 as integer) as grupo
            ,   CAST('''' as varchar) AS cod_estrutural
            ,   cast(4 as integer) as nivel
            ,   CAST(''Pessoa Civil'' as varchar) AS nom_conta
            ,   COALESCE(SUM(ano2), 0.00) AS ano2
            ,   COALESCE(SUM(ano3), 0.00) AS ano3
            ,   COALESCE(SUM(ano4), 0.00)*-1 AS ano4
        FROM  tmp_receitas
        WHERE cod_estrutural ILIKE ''4.1.2%''
          AND publico.fn_nivel(cod_estrutural) > 3
          
        UNION ALL
    
        SELECT
            grupo,
            cod_estrutural,
            nivel,
            nom_conta,
            coalesce(ano2,0.00)*-1 as ano2,
            coalesce(ano3,0.00)*-1 as ano3,
            coalesce(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        
        WHERE   cod_estrutural ILIKE ''militar%''
        
        UNION ALL
    
        SELECT
            cast(6 as integer) as grupo,
            cod_estrutural,
            publico.fn_nivel(cod_estrutural) as nivel,
            nom_conta,
            coalesce(ano2,0.00)*-1 as ano2,
            coalesce(ano3,0.00)*-1 as ano3,
            coalesce(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        
        WHERE   cod_estrutural IN (''4.1.3.0.0.00.00.00.00.00''
                                  ,''4.1.6.0.0.00.00.00.00.00'' 
                                  )
                                  
        UNION ALL
    
        SELECT  CAST(10 as integer) as grupo
            ,   CAST('''' as varchar) AS cod_estrutural
            ,   CAST(3 as integer) AS nivel
            ,   CAST(''Outras Receitas Correntes'' as varchar) AS nom_conta
            ,   COALESCE(SUM(ano2), 0.00) AS ano2
            ,   COALESCE(SUM(ano3), 0.00) AS ano3
            ,   COALESCE(SUM(ano4), 0.00)*-1 AS ano4
        FROM  tmp_receitas
        
        WHERE cod_estrutural IN (''4.1.1.0.0.00.00.00.00.00''
                                ,''4.1.4.0.0.00.00.00.00.00''
                                ,''4.1.5.0.0.00.00.00.00.00''
                                ,''4.1.7.0.0.00.00.00.00.00''
                                ,''4.1.9.0.0.00.00.00.00.00''
                                )
        UNION ALL
    
        SELECT  CAST(10 as integer) as grupo
            ,   cod_estrutural
            ,   CAST(4 as integer) AS nivel
            ,   nom_conta
            ,   coalesce(ano2, 0.00) AS ano2
            ,   coalesce(ano3, 0.00) AS ano3
            ,   coalesce(ano4,0.00)*-1 AS ano4
        FROM  tmp_receitas
        WHERE cod_estrutural = ''4.1.9.2.2.10.00.00.00.00''
          
        UNION ALL
    
        SELECT  CAST(11 as integer) as grupo
            ,   CAST('''' as varchar) AS cod_estrutural
            ,   CAST(4 as integer) AS nivel
            ,   CAST(''Demais Receitas Correntes'' as varchar) AS nom_conta
            ,   (total.ano2      - COALESCE(tmp_receitas.ano2, 0.00)) as ano2
            ,   (total.ano3      - COALESCE(tmp_receitas.ano3, 0.00)) as ano3
            ,   (total.ano4      - COALESCE(tmp_receitas.ano4,0.00)*-1) as ano4
        FROM tmp_receitas
            , ( SELECT CAST(COALESCE(SUM(ano2), 0.00) as numeric(14,2)) AS ano2
                     , CAST(COALESCE(SUM(ano3), 0.00) as numeric(14,2)) AS ano3
                     , CAST(COALESCE(SUM(ano4), 0.00)*-1 as numeric(14,2)) AS ano4
                    
                FROM tmp_receitas
                WHERE cod_estrutural IN (''4.1.1.0.0.00.00.00.00.00''
                                        ,''4.1.4.0.0.00.00.00.00.00''
                                        ,''4.1.5.0.0.00.00.00.00.00''
                                        ,''4.1.7.0.0.00.00.00.00.00''
                                        ,''4.1.9.0.0.00.00.00.00.00''
                                        )
            ) AS total
        WHERE tmp_receitas.cod_estrutural = ''4.1.9.2.2.10.00.00.00.00''
    
        UNION ALL
    
        SELECT  CAST(12 as integer) as grupo
            ,   cod_estrutural
            ,   publico.fn_nivel(cod_estrutural) as nivel
            ,   nom_conta
            ,   coalesce(ano2, 0.00) AS ano2
            ,   coalesce(ano3, 0.00) AS ano3
            ,   coalesce(ano4,0.00)*-1 AS ano4
        FROM  tmp_receitas
        
        WHERE   cod_estrutural IN (''4.2.0.0.0.00.00.00.00.00'' 
                                  ,''4.2.2.0.0.00.00.00.00.00''
                                  ,''4.2.3.0.0.00.00.00.00.00''
                                  )
    
        UNION ALL
    
        SELECT  CAST(13 as integer) as grupo
            ,   CAST('''' as varchar) AS cod_estrutural
            ,   CAST(3 as integer) as nivel
            ,   CAST(''Outras Receitas de Capital'' as varchar) AS nom_conta
            ,   COALESCE(SUM(ano2), 0.00) AS ano2
            ,   COALESCE(SUM(ano3), 0.00) AS ano3
            ,   COALESCE(SUM(ano4),0.00)*-1 AS ano4
        FROM  tmp_receitas
        WHERE   cod_estrutural ILIKE ''4.2.1%''
          OR    cod_estrutural ILIKE ''4.2.4%''
          OR    cod_estrutural ILIKE ''4.2.5%''
        
        
        UNION ALL
    
        SELECT  CAST(15 as integer) as grupo
            ,   CAST('''' as varchar) AS cod_estrutural
            ,   CAST(1 as integer) as nivel
            ,   CAST(''RECEITAS PREVIDENCIÁRIAS - RPPS (INTRA-ORÇAMENTÁRIAS)'' as varchar) AS nom_conta
            ,   COALESCE(SUM(ano2), 0.00) AS ano2
            ,   COALESCE(SUM(ano3), 0.00) AS ano3
            ,   COALESCE(SUM(ano4),0.00)*-1 AS ano4
        FROM  tmp_receitas
        
        WHERE (cod_estrutural ILIKE ''4.7%''
           OR  cod_estrutural ILIKE ''4.8%'')
          AND  publico.fn_nivel(cod_estrutural) = 2
        
        
        UNION ALL
        
        SELECT  CAST(1 as integer) as grupo
            ,   cod_estrutural
            ,   publico.fn_nivel(cod_estrutural) as nivel
            ,   nom_conta
            ,   coalesce(ano2, 0.00) AS ano2
            ,   coalesce(ano3, 0.00) AS ano3
            ,   coalesce(ano4,0.00)*-1 AS ano4
        FROM  tmp_receitas
        
        WHERE cod_estrutural IN ( ''4.7.0.0.0.00.00.00.00.00''
                                 ,''4.7.2.0.0.00.00.00.00.00'')
        
        UNION ALL
    
        SELECT  CAST(3 as integer) as grupo
            ,   CAST('''' as varchar) AS cod_estrutural
            ,   CAST(4 as integer) as nivel
            ,   CAST(''Pessoa Civil'' as varchar) AS nom_conta
            ,   COALESCE(SUM(ano2), 0.00) AS ano2
            ,   COALESCE(SUM(ano3), 0.00) AS ano3
            ,   COALESCE(SUM(ano4),0.00)*-1 AS ano4
        FROM  tmp_receitas
        
        WHERE cod_estrutural IN ( ''4.7.2.1.0.01.00.00.00.00''
                                , ''4.7.2.1.0.29.01.00.00.00''
                                , ''4.7.2.1.0.29.03.00.00.00''
                                , ''4.7.2.1.0.29.05.00.00.00'')
        UNION ALL
    
        SELECT
            grupo,
            cod_estrutural,
            nivel,
            nom_conta,
            coalesce(ano2,0.00)*-1 as ano2,
            coalesce(ano3,0.00)*-1 as ano3,
            coalesce(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        WHERE   cod_estrutural ILIKE ''militar%''
        
        UNION ALL
    
        SELECT 
            cast(6 as integer) as grupo,
            cod_estrutural,
            cast(4 as integer) as nivel,
            nom_conta,
            COALESCE(ano2,0.00)*-1 as ano2,
            COALESCE(ano3,0.00)*-1 as ano3,
            COALESCE(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        WHERE cod_estrutural LIKE ''4.7.2.1.0.29.13.00.00.00''
    
        UNION ALL
        
        SELECT  CAST(6 as integer) as grupo
            ,   CAST('''' as varchar) AS cod_estrutural
            ,   CAST(4 as integer) as nivel
            ,   CAST(''Contribuição Previdenciária para em Regime de Débitos e Parcelamentos'' as varchar) AS nom_conta
            ,   COALESCE(SUM(ano2), 0.00) AS ano2
            ,   COALESCE(SUM(ano3), 0.00) AS ano3
            ,   COALESCE(SUM(ano4),0.00)*-1 AS ano4
        FROM  tmp_receitas
        WHERE cod_estrutural IN (''4.7.2.1.0.29.15.00.00.00''
                                ,''4.7.2.1.0.99.00.00.00.00'')
                                
        UNION ALL
    
        SELECT
            cast(7 as integer) as grupo,
            cod_estrutural,
            cast(3 as integer) as nivel,
            nom_conta,
            coalesce(ano2,0.00)*-1 as ano2,
            coalesce(ano3,0.00)*-1 as ano3,
            coalesce(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        WHERE   cod_estrutural IN (''4.7.3.0.0.00.00.00.00.00''
                                  ,''4.7.9.0.0.00.00.00.00.00''
                                  )
        UNION ALL
    
        SELECT
            cast(8 as integer) as grupo,
            cod_estrutural,
            cast(2 as integer) as nivel,
            nom_conta,
            coalesce(ano2,0.00)*-1 as ano2,
            coalesce(ano3,0.00)*-1 as ano3,
            coalesce(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        
        WHERE   cod_estrutural = ''4.8.0.0.0.00.00.00.00.00''
        
        UNION ALL
    
        SELECT
            cast(9 as integer) as grupo,
            cod_estrutural,
            cast(3 as integer) as nivel,
            nom_conta,
            coalesce(ano2,0.00)*-1 as ano2,
            coalesce(ano3,0.00)*-1 as ano3,
            coalesce(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        WHERE   cod_estrutural IN (  ''4.8.2.0.0.00.00.00.00.00''
                                   , ''4.8.3.0.0.00.00.00.00.00''
                                   , ''4.8.5.0.0.00.00.00.00.00'')
        
        UNION ALL
    
        SELECT
            CAST(15 as integer) as grupo,
            CAST('''' AS varchar) AS cod_estrutural,
            CAST(1 as integer) as nivel,
            CAST(''REPASSES PREVIDENCIÁRIOS PARA COBERTURA DE DÉFCIT ATUARIAL - RPPS'' as varchar) AS nom_conta,
            CAST(0.00 as numeric(14,2)) as ano2,
            CAST(0.00 as numeric(14,2)) as ano3,
            CAST(0.00 as numeric(14,2)) as ano4
            
        UNION ALL
        
        SELECT
            CAST(15 as integer) as grupo,
            CAST('''' AS varchar) AS cod_estrutural,
            CAST(1 as integer) as nivel,
            CAST(''REPASSES PREVIDENCIÁRIOS PARA COBERTURA DE DÉFCIT FINANCEIRO - RPPS'' as varchar) AS nom_conta,
            CAST(0.00 as numeric(14,2)) as ano2,
            CAST(0.00 as numeric(14,2)) as ano3,
            CAST(0.00 as numeric(14,2)) as ano4
            
        UNION ALL
        
        SELECT
            CAST(16 as integer) as grupo,
            cod_estrutural,
            CAST(1 as integer) as nivel,
            nom_conta,
            coalesce(ano2,0.00)*-1 as ano2,
            coalesce(ano3,0.00)*-1 as ano3,
            coalesce(ano4,0.00)*-1 as ano4
        FROM  tmp_receitas
        WHERE cod_estrutural = ''5.1.2.1.7.99.00.00.00.00''
        
        --ORDER BY    grupo, cod_estrutural
    ';
    

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
    DROP TABLE tmp_valor;
    DROP TABLE tmp_receitas;
    
    RETURN;
END;
$$ language 'plpgsql';

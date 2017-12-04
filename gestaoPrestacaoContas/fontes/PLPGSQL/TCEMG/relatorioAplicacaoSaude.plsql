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
    * Script de função PLPGSQL - Relatório TCEMG - Demonstrativo da Aplicação nas Ações e Serviços Públicos de Saúde
    * Data de Criação: 09/07/2014

    * @author Eduardo Paculski Schitz

    $Id: $

*/

CREATE OR REPLACE FUNCTION tcemg.relatorio_aplicacao_saude (VARCHAR, VARCHAR, VARCHAR, VARCHAR, INTEGER, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    stDataInicio    ALIAS FOR $2;
    stDataFim       ALIAS FOR $3;
    stEntidades     ALIAS FOR $4;
    inCodOrgao      ALIAS FOR $5;
    inCodUnidade    ALIAS FOR $6;

    stSql           VARCHAR := '';
    reReg           RECORD;

BEGIN

    stSql := '
    CREATE TEMPORARY TABLE tmp_valor AS 
    SELECT conta_receita.cod_estrutural
         , lote.dt_lote       as data 
         , valor_lancamento.vl_lancamento   as valor 
         , valor_lancamento.oid             as primeira
      FROM orcamento.receita
      JOIN orcamento.conta_receita
        ON conta_receita.cod_conta       = receita.cod_conta
       AND conta_receita.exercicio       = receita.exercicio
      JOIN contabilidade.lancamento_receita
        ON lancamento_receita.cod_receita      = receita.cod_receita
       AND lancamento_receita.exercicio        = receita.exercicio
       AND lancamento_receita.estorno          = true
        -- tipo de lancamento receita deve ser = A , de arrecadação
       AND lancamento_receita.tipo             = ''A''
      JOIN contabilidade.lancamento
        ON lancamento.cod_lote        = lancamento_receita.cod_lote
       AND lancamento.sequencia       = lancamento_receita.sequencia
       AND lancamento.exercicio       = lancamento_receita.exercicio
       AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
       AND lancamento.tipo            = lancamento_receita.tipo
      JOIN contabilidade.valor_lancamento
        ON valor_lancamento.exercicio        = lancamento.exercicio
       AND valor_lancamento.sequencia        = lancamento.sequencia
       AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
       AND valor_lancamento.cod_lote         = lancamento.cod_lote
       AND valor_lancamento.tipo             = lancamento.tipo
        -- na tabela valor lancamento  tipo_valor deve ser credito
       AND valor_lancamento.tipo_valor       = ''D''
      JOIN contabilidade.lote
        ON lote.cod_lote       = lancamento.cod_lote
       AND lote.cod_entidade   = lancamento.cod_entidade
       AND lote.exercicio      = lancamento.exercicio
       AND lote.tipo           = lancamento.tipo
 LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
        ON receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
       AND receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
     WHERE receita.exercicio       = ''' || stExercicio || '''
       AND receita.cod_entidade    IN (' || stEntidades || ')
       AND ( 
              ( conta_receita.cod_estrutural LIKE ''9.%''
            AND receita_indentificadores_peculiar_receita.cod_identificador <> 95
              )
          OR conta_receita.cod_estrutural NOT LIKE ''9.%''
           )

    UNION ALL
    
    SELECT conta_receita.cod_estrutural
         , lote.dt_lote       as data 
         , valor_lancamento.vl_lancamento   as valor 
         , valor_lancamento.oid             as primeira
      FROM orcamento.receita
      JOIN orcamento.conta_receita
        ON conta_receita.cod_conta       = receita.cod_conta
       AND conta_receita.exercicio       = receita.exercicio
      JOIN contabilidade.lancamento_receita
        ON lancamento_receita.cod_receita      = receita.cod_receita
       AND lancamento_receita.exercicio        = receita.exercicio
       AND lancamento_receita.estorno          = false
        -- tipo de lancamento receita deve ser = A , de arrecadação
       AND lancamento_receita.tipo             = ''A''
      JOIN contabilidade.lancamento
        ON lancamento.cod_lote        = lancamento_receita.cod_lote
       AND lancamento.sequencia       = lancamento_receita.sequencia
       AND lancamento.exercicio       = lancamento_receita.exercicio
       AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
       AND lancamento.tipo            = lancamento_receita.tipo
      JOIN contabilidade.valor_lancamento
        ON valor_lancamento.exercicio        = lancamento.exercicio
       AND valor_lancamento.sequencia        = lancamento.sequencia
       AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
       AND valor_lancamento.cod_lote         = lancamento.cod_lote
       AND valor_lancamento.tipo             = lancamento.tipo
        -- na tabela valor lancamento  tipo_valor deve ser credito
       AND valor_lancamento.tipo_valor       = ''C''
      JOIN contabilidade.lote
        ON lote.cod_lote       = lancamento.cod_lote
       AND lote.cod_entidade   = lancamento.cod_entidade
       AND lote.exercicio      = lancamento.exercicio
       AND lote.tipo           = lancamento.tipo
 LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
        ON receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
       AND receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
     WHERE receita.exercicio       = ''' || stExercicio || '''
       AND receita.cod_entidade    IN (' || stEntidades || ')
       AND ( 
              ( conta_receita.cod_estrutural LIKE ''9.%''
            AND receita_indentificadores_peculiar_receita.cod_identificador <> 95
              )
          OR conta_receita.cod_estrutural NOT LIKE ''9.%''
           )
    ';

    EXECUTE stSql;

    -- -------------------------------------    
    -- Estrutura de Tabelas Temporarias
    -- -------------------------------------

    -- Tabela tmp_tcemg_saude_receita

    CREATE TEMPORARY TABLE tmp_tcemg_saude_receita (
        grupo       INTEGER         DEFAULT 0 ,
        item        INTEGER         DEFAULT 0 ,
        valor       NUMERIC(14,2)   DEFAULT 0.00
    ) ;

    -- Tabela tmp_retorno
    -- Guarda os resultados para serem retornados na PL
    
    CREATE TEMPORARY TABLE tmp_retorno (
        grupo               INTEGER         DEFAULT 0,
        item                INTEGER         DEFAULT 0,
        tipo_receita        CHAR            DEFAULT NULL,
        cod_estrutural      VARCHAR(150)    DEFAULT NULL ,
        descricao           VARCHAR(150)    DEFAULT NULL ,
        valor               NUMERIC(14,2)   DEFAULT 0.00
    );

    -- -------------------------------------
    -- Fim Estrutura de Tabelas Temporarias
    -- ------------------------------------- 

    -- -------------------------------------------------------------
    -- Impostos  
    -- -------------------------------------------------------------

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 1, 1
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.1.1.2.02.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 1, 2
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.1.1.2.04.31.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 1, 3
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.1.1.2.04.34.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 1, 4
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.1.1.2.08.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 1, 5
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.1.1.3.05.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 1, 6
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.1.1.3.05.03.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    -- -----------------------------------------------------
    -- Transferências Correntes
    -- -----------------------------------------------------

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 2, 1
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.7.2.1.01.02.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 2, 2
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.7.2.1.01.05.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 2, 3
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.7.2.1.36.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 2, 4
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.7.2.2.01.01.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 2, 5
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.7.2.2.01.02.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 2, 6
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.7.2.2.01.04.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    -- -----------------------------------------------------
    -- Outras Receitas Correntes
    -- -----------------------------------------------------

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 3, 1
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.9.1.1.38.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 3, 2
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.9.1.1.40.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 3, 3
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.9.1.3.11.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 3, 4
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.9.1.3.13.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 3, 5
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.9.3.1.11.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 3, 6
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('1.9.3.1.13.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    -- -----------------------------------------------------
    -- Transferências de Capital
    -- -----------------------------------------------------
 
    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 4, 1
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('2.4.2.1.01.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 4, 2
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('2.4.2.2.01.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 4, 3
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('2.4.2.3.01.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 4, 4
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('2.4.7.1.01.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 4, 5
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('2.4.7.2.01.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 4, 6
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('2.4.7.3.01.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    -- -----------------------------------------------------
    -- Deduções das Rceitas
    -- -----------------------------------------------------
 
    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 5, 1
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('9.1.7.2.1.00.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    INSERT INTO tmp_tcemg_saude_receita 
         SELECT 5, 2
              , orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida('9.1.7.2.2.00.00.00.00.00')
                                                        , '' || stDataInicio || ''
                                                        , '' || stDataFim || ''
              ) as valor;

    -- -------------------------------------
    -- Inserts com os valores de cada linha
    -- -------------------------------------

    -- -------------------------------------------------------------
    -- Impostos 
    -- -------------------------------------------------------------

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'A' AS tipo_receita
                   , '1.1.1.2.02.00.00.00.00' AS cod_estrutural
                   , CAST('IPTU - Imposto sobre a Propriedade Predial e Territorial Urbana' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 1
                 AND item = 1
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'A' AS tipo_receita
                   , '1.1.1.2.04.31.00.00.00' AS cod_estrutural
                   , CAST('Imposto de Renda Retido nas Fontes sobre os Rendimentos do Trabalho' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 1
                 AND item = 2
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'A' AS tipo_receita
                   , '1.1.1.2.04.34.00.00.00' AS cod_estrutural
                   , CAST('Retido Nas Fontes - Outros Rendimentos' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 1
                 AND item = 3
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'A' AS tipo_receita
                   , '1.1.1.2.08.00.00.00.00' AS cod_estrutural
                   , CAST('Imposto sobre Transmissão "Inter-Vivos" de Bens Imóveis e de Direitos Reais sobre Imóveis' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 1
                 AND item = 4
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'A' AS tipo_receita
                   , '1.1.1.3.05.00.00.00.00' AS cod_estrutural
                   , CAST('Imposto sobre Serviços de Qualquer Natureza' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 1
                 AND item = 5
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'A' AS tipo_receita
                   , '1.1.1.3.05.03.00.00.00' AS cod_estrutural
                   , CAST('ISS - Simples Nacional' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 1
                 AND item = 6
            GROUP BY grupo
                   , item;

    -- -----------------------------------------------------
    -- Transferências Correntes
    -- -----------------------------------------------------

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'B' AS tipo_receita
                   , '1.7.2.1.01.02.00.00.00' AS cod_estrutural
                   , CAST('Cota-Parte do Fundo de Participação dos Municípios' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 2
                 AND item = 1
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'B' AS tipo_receita
                   , '1.7.2.1.01.05.00.00.00' AS cod_estrutural
                   , CAST('Cota-Parte do Imposto sobre a Propriedade Territorial Rural' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 2
                 AND item = 2
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'B' AS tipo_receita
                   , '1.7.2.1.36.00.00.00.00' AS cod_estrutural
                   , CAST('Transferência Financeira do ICMS - Desoneração - LC 87/96' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 2
                 AND item = 3
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'B' AS tipo_receita
                   , '1.7.2.2.01.01.00.00.00' AS cod_estrutural
                   , CAST('Cota-Parte do ICMS' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 2
                 AND item = 4
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'B' AS tipo_receita
                   , '1.7.2.2.01.02.00.00.00' AS cod_estrutural
                   , CAST('Cota-Parte do Imposto sobre a Propriedade de Veículos Automotores' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 2
                 AND item = 5
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'B' AS tipo_receita
                   , '1.7.2.2.01.04.00.00.00' AS cod_estrutural
                   , CAST('Cota-Parte do IPI sobre Exportação' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 2
                 AND item = 6
            GROUP BY grupo
                   , item;

    -- -----------------------------------------------------
    -- Outras Receitas Correntes
    -- -----------------------------------------------------

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'C' AS tipo_receita
                   , '1.9.1.1.38.00.00.00.00' AS cod_estrutural
                   , CAST('Multas e Juros de Mora do Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 3
                 AND item = 1
            GROUP BY grupo
                   , item;

                 INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'C' AS tipo_receita
                   , '1.9.1.1.40.00.00.00.00' AS cod_estrutural
                   , CAST('Multas e Juros de Mora do Imposto sobre Serviços de Qualquer Natureza - ISS' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 3
                 AND item = 2
            GROUP BY grupo
                   , item;

                 INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'C' AS tipo_receita
                   , '1.9.1.3.11.00.00.00.00' AS cod_estrutural
                   , CAST('Multas e Juros de Mora da Dívida Ativa do Imp. sobre a Propriedade Predial e Territ. Urbana - IPTU' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 3
                 AND item = 3
            GROUP BY grupo
                   , item;

                 INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'C' AS tipo_receita
                   , '1.9.1.3.13.00.00.00.00' AS cod_estrutural
                   , CAST('Multas e Juros de Mora da Dívida Ativa do Imposto sobre Serviços de Qualquer Natureza - ISS' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 3
                 AND item = 4
            GROUP BY grupo
                   , item;

                 INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'C' AS tipo_receita
                   , '1.9.3.1.11.00.00.00.00' AS cod_estrutural
                   , CAST('Receita da Dívida Ativa do Imposto sobre a Propriedade Predial e Territorial Urbana - IPTU' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 3
                 AND item = 5
            GROUP BY grupo
                   , item;

                 INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'C' AS tipo_receita
                   , '1.9.3.1.13.00.00.00.00' AS cod_estrutural
                   , CAST('Receita da Dívida Ativa do Imposto sobre Serviços de Qualquer Natureza - ISS' AS VARCHAR) AS descricao                   
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 3
                 AND item = 6
            GROUP BY grupo
                   , item;

    -- -----------------------------------------------------
    -- Transferências de Capital
    -- -----------------------------------------------------

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'D' AS tipo_receita
                   , '2.4.2.1.01.00.00.00.00' AS cod_estrutural
                   , CAST('Transferências de Recursos do Sistema Único de Saúde - SUS' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 4
                 AND item = 1
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'D' AS tipo_receita
                   , '2.4.2.2.01.00.00.00.00' AS cod_estrutural
                   , CAST('Transferências de Recursos do Sistema Único de Saúde - SUS' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 4
                 AND item = 2
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'D' AS tipo_receita
                   , '2.4.2.3.01.00.00.00.00' AS cod_estrutural
                   , CAST('Transferências de Recursos Destinados a Programas de Saúde' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 4
                 AND item = 3
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'D' AS tipo_receita
                   , '2.4.7.1.01.00.00.00.00' AS cod_estrutural
                   , CAST('Transferências de Convênios da União para o Sistema Único de Saúde - SUS' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 4
                 AND item = 4
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'D' AS tipo_receita
                   , '2.4.7.2.01.00.00.00.00' AS cod_estrutural
                   , CAST('Transferências de Convênios do Estado para o Sistema Único de Saúde - SUS' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 4
                 AND item = 5
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'D' AS tipo_receita
                   , '2.4.7.3.01.00.00.00.00' AS cod_estrutural
                   , CAST('Transferências de Convênios dos Municípios Destinados a Programas de Saúde' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 4
                 AND item = 6
            GROUP BY grupo
                   , item;

    -- -----------------------------------------------------
    -- Deduções das Rceitas (exceto FUNDEB)
    -- -----------------------------------------------------

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'E' AS tipo_receita
                   , '9.1.7.2.1.00.00.00.00.00' AS cod_estrutural
                   , CAST('Deduções das Receitas de Transferências da União' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 5
                 AND item = 1
            GROUP BY grupo
                   , item;

    INSERT INTO tmp_retorno
              SELECT grupo
                   , item
                   , 'E' AS tipo_receita
                   , '9.1.7.2.2.00.00.00.00.00' AS cod_estrutural
                   , CAST('Dedução da Receita de Transferência do Estado' AS VARCHAR) AS descricao
                   , CAST(SUM(valor) AS NUMERIC) AS valor
                FROM tmp_tcemg_saude_receita 
               WHERE grupo = 5
                 AND item = 2
            GROUP BY grupo
                   , item;

    -- -----------------------------------------------------
    -- Retorno
    -- -----------------------------------------------------

    stSql := 'SELECT grupo
                   , item
                   , tipo_receita
                   , cod_estrutural
                   , descricao
                   , CASE WHEN cod_estrutural LIKE ''9.%'' THEN ABS(COALESCE(valor,0)) * (-1)
                          ELSE ABS(COALESCE(valor,0))
                     END AS valor
                FROM tmp_retorno 
            ORDER BY grupo
                   , item
    ';

    FOR reReg IN EXECUTE stSql
    LOOP
        RETURN NEXT reReg;
    END LOOP;


    DROP TABLE tmp_tcemg_saude_receita ;
    DROP TABLE tmp_valor ;
    DROP TABLE tmp_retorno ;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';

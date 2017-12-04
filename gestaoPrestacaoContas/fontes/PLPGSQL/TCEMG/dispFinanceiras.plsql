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
    * Arquivo de mapeamento para a função que busca os dados da disponibilidade financeira 
    * Data de Criação   : 19/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    
    * @package URBEM
    * @subpackage 

    $Id: $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_disp_financeiras(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    
    inEntidadeRPPS      INTEGER;
    stSql               VARCHAR := '';
    reRegistro          RECORD;
BEGIN
    
    SELECT valor 
    INTO inEntidadeRPPS 
    FROM administracao.configuracao 
    WHERE parametro = 'cod_entidade_rpps' 
    AND cod_modulo = 8 
    AND exercicio = stExercicio;

    --Remove a entidade RPPS se foi selecionada no filtro
    IF ( length(stCodEntidade) > 1 ) THEN
        stCodEntidade := REGEXP_REPLACE(stCodEntidade,','||inEntidadeRPPS||'|'||inEntidadeRPPS||',','');
    END IF;

    --Cria tabela com os valores da entidade RPPS
    CREATE TEMPORARY TABLE tmp_balanco_entidade_rpps AS
        SELECT * 
        FROM contabilidade.fn_rl_balancete_verificacao( stExercicio
                                                        , 'cod_entidade IN  ('||inEntidadeRPPS||') '
                                                        , stDtInicial
                                                        , stDtFinal
                                                        , 'A'::CHAR)
        as retorno
                ( cod_estrutural            varchar                                                    
                    ,nivel                  integer                                                               
                    ,nom_conta              varchar                                                           
                    ,cod_sistema            integer                                                         
                    ,indicador_superavit    char(12)                                                    
                    ,vl_saldo_anterior      numeric                                                   
                    ,vl_saldo_debitos       numeric                                                   
                    ,vl_saldo_creditos      numeric                                                   
                    ,vl_saldo_atual         numeric                                                   
                );

    --Cria tabela com os valores da entidade RPPS
    CREATE TEMPORARY TABLE tmp_balanco_outras_entidade AS
        SELECT * 
        FROM contabilidade.fn_rl_balancete_verificacao( stExercicio
                                                        , 'cod_entidade IN  ('||stCodEntidade||') '
                                                        , stDtInicial
                                                        , stDtFinal
                                                        , 'A'::CHAR)
        as retorno
                ( cod_estrutural            varchar                                                    
                    ,nivel                  integer                                                               
                    ,nom_conta              varchar                                                           
                    ,cod_sistema            integer                                                         
                    ,indicador_superavit    char(12)                                                    
                    ,vl_saldo_anterior      numeric                                                   
                    ,vl_saldo_debitos       numeric                                                   
                    ,vl_saldo_creditos      numeric                                                   
                    ,vl_saldo_atual         numeric                                                   
                );                                                
    

    --Cria tabela pra inserir os valore de compromissado RPPS de acordo com o somatorio das obrigações financeiras do relatorio RGF ANEXO 5 de 2015
    CREATE TEMPORARY TABLE tmp_compromissado_entidade_rpps AS (
        SELECT 
            (   liquidados_nao_pagos_exercicios_anteriores 
                + 
                liquidados_nao_pagos 
                + 
                empenhados_nao_liquidados_exercicios_anteriores 
                + 
                obrigacoes_financeiras
            ) as compromissado_rpps
        FROM(
            SELECT 
                  sum(tb.total_processados_exercicios_anteriores) + sum(tb.total_processados_exercicio_anterior) AS liquidados_nao_pagos_exercicios_anteriores
                , sum(tb.liquidados_nao_pagos) AS liquidados_nao_pagos
                , sum(tb.total_nao_processados_exercicios_anteriores) + sum(tb.total_nao_processados_exercicio_anterior) AS empenhados_nao_liquidados_exercicios_anteriores         
                , sum(tb2.consignacoes) AS obrigacoes_financeiras
            FROM ( 
                SELECT 
                      cod_recurso
                    , entidade
                    , tipo
                    , SUM(total_processados_exercicios_anteriores) AS total_processados_exercicios_anteriores
                    , SUM(total_processados_exercicio_anterior) AS total_processados_exercicio_anterior
                    , SUM(total_nao_processados_exercicios_anteriores) AS total_nao_processados_exercicios_anteriores
                    , SUM(total_nao_processados_exercicio_anterior) AS total_nao_processados_exercicio_anterior
                    , SUM(liquidados_nao_pagos) AS liquidados_nao_pagos
                    , SUM(empenhados_nao_liquidados) AS empenhados_nao_liquidados
                    , SUM(empenhados_nao_liquidados_cancelados) AS empenhados_nao_liquidados_cancelados
                    , SUM(caixa_liquida) AS caixa_liquida
                FROM stn.fn_rgf_anexo6novo_recurso(stExercicio,inEntidadeRPPS::varchar,stDtFinal) AS stn_fn
                (   cod_recurso                                   integer
                    , tipo                                        varchar
                    , entidade                                    integer
                    , total_processados_exercicios_anteriores     numeric
                    , total_processados_exercicio_anterior        numeric
                    , total_nao_processados_exercicios_anteriores numeric
                    , total_nao_processados_exercicio_anterior    numeric
                    , liquidados_nao_pagos                        numeric
                    , empenhados_nao_liquidados                   numeric
                    , empenhados_nao_liquidados_cancelados        numeric
                    , caixa_liquida                               numeric 
                )
                GROUP BY cod_recurso
                        , entidade
                        , tipo
            ) as tb
            
            INNER JOIN stn.pl_recurso_descricao(stExercicio,stDtInicial,stDtFinal,' '::varchar,inEntidadeRPPS::varchar,'true') AS tb2
            (   tipo_recurso                   char(1)
                , cod_recurso                  integer
                , exercicio                    varchar
                , nom_recurso                  varchar
                , positivo                     numeric
                , negativo                     numeric
                , saldo                        numeric
                , a_pagar_exercicio            numeric
                , a_pagar_exercicio_anteriores numeric
                , valor_consignacao_positivo   numeric
                , valor_consignacao_negativo   numeric
                , consignacoes                 numeric
                , caixa                        numeric 
            )
                ON tb2.cod_recurso = tb.cod_recurso
                AND tb2.exercicio  = stExercicio
            WHERE tb.entidade NOT IN ((SELECT valor::integer FROM administracao.configuracao WHERE configuracao.parametro = 'cod_entidade_rpps' AND configuracao.exercicio = stExercicio))
        ) as foo
    );
    
    --Cria tabela pra inserir os valore de compromissado de acordo com o somatorio das obrigações financeiras do relatorio RGF ANEXO 5 de 2015
    CREATE TEMPORARY TABLE tmp_compromissado_outras_entidades AS (
        SELECT 
            (   liquidados_nao_pagos_exercicios_anteriores 
                + 
                liquidados_nao_pagos 
                + 
                empenhados_nao_liquidados_exercicios_anteriores 
                + 
                obrigacoes_financeiras
            ) as compromissado
        FROM(
            SELECT 
                  sum(tb.total_processados_exercicios_anteriores) + sum(tb.total_processados_exercicio_anterior) AS liquidados_nao_pagos_exercicios_anteriores
                , sum(tb.liquidados_nao_pagos) AS liquidados_nao_pagos
                , sum(tb.total_nao_processados_exercicios_anteriores) + sum(tb.total_nao_processados_exercicio_anterior) AS empenhados_nao_liquidados_exercicios_anteriores         
                , sum(tb2.consignacoes) AS obrigacoes_financeiras
            FROM ( 
                SELECT 
                      cod_recurso
                    , entidade
                    , tipo
                    , SUM(total_processados_exercicios_anteriores) AS total_processados_exercicios_anteriores
                    , SUM(total_processados_exercicio_anterior) AS total_processados_exercicio_anterior
                    , SUM(total_nao_processados_exercicios_anteriores) AS total_nao_processados_exercicios_anteriores
                    , SUM(total_nao_processados_exercicio_anterior) AS total_nao_processados_exercicio_anterior
                    , SUM(liquidados_nao_pagos) AS liquidados_nao_pagos
                    , SUM(empenhados_nao_liquidados) AS empenhados_nao_liquidados
                    , SUM(empenhados_nao_liquidados_cancelados) AS empenhados_nao_liquidados_cancelados
                    , SUM(caixa_liquida) AS caixa_liquida
                FROM stn.fn_rgf_anexo6novo_recurso(stExercicio,stCodEntidade,stDtFinal) AS stn_fn
                (   cod_recurso                                   integer
                    , tipo                                        varchar
                    , entidade                                    integer
                    , total_processados_exercicios_anteriores     numeric
                    , total_processados_exercicio_anterior        numeric
                    , total_nao_processados_exercicios_anteriores numeric
                    , total_nao_processados_exercicio_anterior    numeric
                    , liquidados_nao_pagos                        numeric
                    , empenhados_nao_liquidados                   numeric
                    , empenhados_nao_liquidados_cancelados        numeric
                    , caixa_liquida                               numeric 
                )
                GROUP BY cod_recurso
                        , entidade
                        , tipo
            ) as tb
            
            INNER JOIN stn.pl_recurso_descricao(stExercicio,stDtInicial,stDtFinal,' '::varchar,stCodEntidade,'false') AS tb2
            (   tipo_recurso                   char(1)
                , cod_recurso                  integer
                , exercicio                    varchar
                , nom_recurso                  varchar
                , positivo                     numeric
                , negativo                     numeric
                , saldo                        numeric
                , a_pagar_exercicio            numeric
                , a_pagar_exercicio_anteriores numeric
                , valor_consignacao_positivo   numeric
                , valor_consignacao_negativo   numeric
                , consignacoes                 numeric
                , caixa                        numeric 
            )
                ON tb2.cod_recurso = tb.cod_recurso
                AND tb2.exercicio  = stExercicio
            WHERE tb.entidade NOT IN ((SELECT valor::integer FROM administracao.configuracao WHERE configuracao.parametro = 'cod_entidade_rpps' AND configuracao.exercicio = stExercicio))
        ) as foo
    );

    --Consulta para buscar os valores
    stSql := '  SELECT   
                (SELECT vl_saldo_atual
                        FROM tmp_balanco_outras_entidade
                        WHERE cod_estrutural like ''1.1.1.1.1.00.00.00.00.00%''
                ) as caixa
                
                ,(SELECT vl_saldo_atual
                        FROM tmp_balanco_outras_entidade
                        WHERE cod_estrutural like ''1.1.1.1.1.19.01.00.00.00%''
                ) as conta_movimento

                ,(SELECT vl_saldo_atual
                        FROM tmp_balanco_outras_entidade
                        WHERE cod_estrutural like ''1.1.1.1.1.19.02.00.00.00%''
                ) as contas_vinculadas

                ,(SELECT SUM(vl_saldo_atual) as vl_saldo_atual
                        FROM tmp_balanco_outras_entidade
                        WHERE cod_estrutural like ''1.1.1.1.1.50.00.00.00.00%''
                        OR cod_estrutural like ''1.1.4%''
                ) as aplicacoes_financeiras                

                ,(  SELECT SUM(compromissado) as compromissado
                        FROM tmp_compromissado_outras_entidades
                ) as compromissado

                ,(SELECT vl_saldo_atual
                        FROM tmp_balanco_entidade_rpps
                        WHERE cod_estrutural like ''1.1.1.1.1.00.00.00.00.00%''
                ) as caixa_rpps
                
                ,(SELECT SUM(vl_saldo_atual) as vl_saldo_atual
                        FROM tmp_balanco_entidade_rpps
                        WHERE cod_estrutural like ''1.1.1.1.1.06%''
                ) as contas_movimento_rpps

                ,(SELECT SUM(vl_saldo_atual) as vl_saldo_atual
                        FROM tmp_balanco_entidade_rpps
                        WHERE cod_estrutural like ''1.1.1.1.1.06%''
                ) as contas_vinculadas_rpps

                ,(SELECT SUM(vl_saldo_atual) as vl_saldo_atual
                        FROM tmp_balanco_entidade_rpps
                        WHERE cod_estrutural like ''1.1.1.1.1.50.00.00.00.00%''
                        OR cod_estrutural like ''1.1.4%''
                ) as aplicacoes_financeiras_rpps

                ,(  SELECT SUM(compromissado_rpps) as compromissado_rpps
                        FROM tmp_compromissado_entidade_rpps
                ) as compromissado_rpps

            ';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_balanco_entidade_rpps;
    DROP TABLE tmp_balanco_outras_entidade;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  


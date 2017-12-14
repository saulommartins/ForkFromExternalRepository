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
/* recuperar_dirf_prestadores_servico
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION criar_tabela_temporaria_prestador_servico(VARCHAR, INTEGER, INTEGER) RETURNS BOOLEAN AS $$
DECLARE
    stEntidade          ALIAS FOR $1;
    inExercicio         ALIAS FOR $2;    
    inCodEntidade       ALIAS FOR $3;    
    stSql               VARCHAR := '';
BEGIN

     stSql := ' CREATE TEMPORARY TABLE tmp_prestador_servico AS (
                SELECT 
                    nom_cgm
                    ,numcgm
                    ,beneficiario
                    ,ident_especie_beneficiario
                    ,cod_conta
                    ,mes
                    ,cod_dirf
                    ,tipo    
                    ,tipo_conta    
                    ,vl_retencao_inss
                    ,vl_empenhado        
                    ,SUM(vl_retencao_irrf) as vl_retencao_irrf
                FROM (
                        SELECT DISTINCT
                        REPLACE(sw_cgm.nom_cgm,''–'',''-'') as nom_cgm
                        , sw_cgm.numcgm
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN
                                sw_cgm_pessoa_fisica.cpf
                          ELSE
                                sw_cgm_pessoa_juridica.cnpj
                          END AS beneficiario                
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL 
                                THEN 1
                                ELSE 2
                          END AS ident_especie_beneficiario
                        ,CASE WHEN receita.cod_conta IS NOT NULL 
                                THEN receita.cod_conta
                                ELSE receita_orcamentaria.cod_conta
                          END AS cod_conta
                        , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                THEN
                                        (SELECT DISTINCT cod_dirf 
                                         FROM ima.configuracao_dirf_prestador
                                         WHERE exercicio = '''||inExercicio||'''
                                         AND tipo = ''F'')
                                ELSE
                                        (SELECT DISTINCT cod_dirf 
                                         FROM ima.configuracao_dirf_prestador
                                         WHERE exercicio = '''||inExercicio||'''
                                         AND tipo = ''J'')
                        END AS cod_dirf
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL 
                                THEN ''F''
                                ELSE ''J''
                          END AS tipo
                        , CASE WHEN receita.tipo_conta IS NOT NULL 
                            THEN ''receita''
                            ELSE CASE WHEN receita_orcamentaria IS NOT NULL
                                    THEN ''plano''
                                    ELSE ''''
                                END
                        END as tipo_conta
                        ,ordem_pagamento_retencao.vl_retencao as vl_retencao_irrf
                        ,COALESCE(retencoes_inss.vl_retencao_inss,0.00) as vl_retencao_inss                 
                        ,empenho.fn_consultar_valor_empenhado_pago_prestadores_dirf(  ordem_pagamento_retencao.exercicio               
                                                                            , empenho.cod_empenho             
                                                                            , empenho.cod_entidade 
                                                                            , to_char(nota_liquidacao_paga.timestamp, ''mm'')::int )  
                        AS vl_empenhado
                        ,ordem_pagamento_retencao.cod_ordem
            
                        FROM empenho.ordem_pagamento_retencao
            
                        LEFT JOIN (
                                    SELECT  
                                            receita.cod_receita
                                        ,   receita.cod_entidade
                                        ,   configuracao_dirf_irrf_conta_receita.exercicio
                                        ,   configuracao_dirf_irrf_conta_receita.cod_conta
                                        ,   conta_receita.descricao
                                        ,  ''receita'' as tipo_conta
                                      FROM  orcamento.receita
                                INNER JOIN  orcamento.conta_receita
                                        ON  conta_receita.cod_conta = receita.cod_conta                                   
                                       AND  conta_receita.exercicio = receita.exercicio                                        
                                INNER JOIn  ima.configuracao_dirf_irrf_conta_receita 
                                        ON  configuracao_dirf_irrf_conta_receita.exercicio = conta_receita.exercicio
                                       AND  configuracao_dirf_irrf_conta_receita.cod_conta = conta_receita.cod_conta
                                ) AS receita
                            ON receita.exercicio = ordem_pagamento_retencao.exercicio
                           AND receita.cod_receita = ordem_pagamento_retencao.cod_receita
            
                        LEFT JOIN (
                                SELECT  plano_analitica.cod_plano                    
                                    ,   plano_analitica.exercicio
                                    ,   plano_conta.nom_conta
                                    ,   plano_analitica.cod_conta
                                    ,   ''plano'' as tipo_conta
                                    FROM  contabilidade.plano_analitica   
                            INNER JOIN contabilidade.plano_conta   
                                    ON plano_analitica.cod_conta = plano_conta.cod_conta
                                   AND plano_analitica.exercicio = plano_conta.exercicio                       
                            INNER JOIN ima.configuracao_dirf_irrf_plano_conta 
                                    ON configuracao_dirf_irrf_plano_conta.exercicio = plano_analitica.exercicio
                                   AND configuracao_dirf_irrf_plano_conta.cod_conta = plano_analitica.cod_conta
                            
                            ) AS receita_orcamentaria
                        ON receita_orcamentaria.exercicio = ordem_pagamento_retencao.exercicio
                       AND receita_orcamentaria.cod_plano = ordem_pagamento_retencao.cod_plano
                          
                INNER JOIN empenho.ordem_pagamento
                        ON ordem_pagamento.cod_ordem    = ordem_pagamento_retencao.cod_ordem    
                       AND ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio    
                       AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
                       
                INNER JOIN empenho.pagamento_liquidacao
                        ON pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem    
                       AND pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio    
                       AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
            
                INNER JOIN empenho.nota_liquidacao
                        ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                       AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
                       AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota
            
                    INNER JOIN empenho.nota_liquidacao_paga
                        ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                       AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota
                
                INNER JOIN empenho.empenho
                        ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                       AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho.exercicio       = empenho.exercicio
                       AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            
                    LEFT JOIN empenho.pre_empenho_despesa
                        ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                       AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
            
                    LEFT JOIN empenho.restos_pre_empenho
                            ON restos_pre_empenho.cod_pre_empenho   = pre_empenho.cod_pre_empenho
                            AND restos_pre_empenho.exercicio        = pre_empenho.exercicio
                
                INNER JOIN sw_cgm
                        ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
            
                    LEFT JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
            
                    LEFT JOIN sw_cgm_pessoa_juridica
                            ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
            
                    LEFT JOIN (     SELECT 
                                            pre_empenho.cod_pre_empenho
                                            ,to_char(nota_liquidacao_paga.timestamp, ''mm'')::int as mes                                    
                                            ,ordem_pagamento_retencao.exercicio
                                            ,ordem_pagamento_retencao.cod_entidade
                                            ,SUM(empenho.ordem_pagamento_retencao.vl_retencao) as vl_retencao_inss
            
                                        FROM contabilidade.plano_analitica
            
                                        INNER JOIN ima.configuracao_dirf_inss
                                             ON configuracao_dirf_inss.exercicio = plano_analitica.exercicio
                                            AND configuracao_dirf_inss.cod_conta = plano_analitica.cod_conta                        
            
                                        INNER JOIN empenho.ordem_pagamento_retencao
                                            ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                            AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio  
                                
                                        INNER JOIN empenho.ordem_pagamento 
                                            ON ordem_pagamento.cod_ordem     = ordem_pagamento_retencao.cod_ordem
                                            AND ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio
                                            AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
                       
                                        INNER JOIN empenho.pagamento_liquidacao
                                            ON pagamento_liquidacao.exercicio       = ordem_pagamento.exercicio
                                            AND pagamento_liquidacao.cod_entidade   = ordem_pagamento.cod_entidade
                                            AND pagamento_liquidacao.cod_ordem      = ordem_pagamento.cod_ordem
                       
                                        INNER JOIN empenho.nota_liquidacao
                                            ON nota_liquidacao.exercicio     = pagamento_liquidacao.exercicio_liquidacao 
                                            AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade 
                                            AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota
            
                                        INNER JOIN empenho.nota_liquidacao_paga
                                            ON nota_liquidacao_paga.exercicio       = nota_liquidacao.exercicio
                                            AND nota_liquidacao_paga.cod_entidade   = nota_liquidacao.cod_entidade
                                            AND nota_liquidacao_paga.cod_nota       = nota_liquidacao.cod_nota
                                    
                                        INNER JOIN ( SELECT exercicio
                                                        , cod_entidade
                                                        , cod_nota
                                                        , max(timestamp) as timestamp
                                                    FROM empenho.nota_liquidacao_paga
                                                GROUP BY exercicio
                                                        , cod_entidade
                                                        , cod_nota ) as max_nota_liquidacao_paga
                                            ON nota_liquidacao_paga.exercicio    = max_nota_liquidacao_paga.exercicio
                                            AND nota_liquidacao_paga.cod_entidade = max_nota_liquidacao_paga.cod_entidade
                                            AND nota_liquidacao_paga.cod_nota     = max_nota_liquidacao_paga.cod_nota
                                            AND nota_liquidacao_paga.timestamp    = max_nota_liquidacao_paga.timestamp
            
                                        INNER JOIN empenho.empenho
                                            ON nota_liquidacao.exercicio_empenho    = empenho.exercicio
                                            AND nota_liquidacao.cod_entidade         = empenho.cod_entidade
                                            AND nota_liquidacao.cod_empenho          = empenho.cod_empenho
            
                                        INNER JOIN empenho.pre_empenho          
                                            ON  pre_empenho.exercicio                = empenho.exercicio
                                            AND pre_empenho.cod_pre_empenho          = empenho.cod_pre_empenho
                                            
                                        LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                            ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                                            AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                                            AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                                            AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp
            
                                        LEFT JOIN empenho.ordem_pagamento_anulada           
                                            ON ordem_pagamento_anulada.exercicio        = ordem_pagamento.exercicio
                                            AND ordem_pagamento_anulada.cod_entidade    = ordem_pagamento.cod_entidade
                                            AND ordem_pagamento_anulada.cod_ordem       = ordem_pagamento.cod_ordem
            
                                        LEFT JOIN empenho.restos_pre_empenho
                                            ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                                            AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
            
                                            where nota_liquidacao_paga_anulada.cod_nota IS NULL
                                            AND ordem_pagamento_anulada.cod_ordem IS NULL
            
                                    GROUP BY pre_empenho.cod_pre_empenho
                                            ,mes
                                            ,ordem_pagamento_retencao.exercicio
                                            ,ordem_pagamento_retencao.cod_entidade
                                                
                            )as retencoes_inss
                                ON retencoes_inss.exercicio = retencoes_inss.exercicio
                                AND retencoes_inss.cod_entidade = retencoes_inss.cod_entidade
                                AND empenho.cod_pre_empenho = retencoes_inss.cod_pre_empenho
                                AND retencoes_inss.mes = to_char(nota_liquidacao_paga.timestamp, ''mm'')::int
                   
                    WHERE ordem_pagamento.exercicio = '''||inExercicio||'''
                    AND ordem_pagamento.cod_entidade IN ('||inCodEntidade||')
            
                    AND EXISTS ( SELECT 1 
                                         FROM empenho.nota_liquidacao_paga
                                    LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                           ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                                          AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                                          AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                          AND nota_liquidacao_paga_anulada.timestamp = nota_liquidacao_paga.timestamp
                                        WHERE  nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                                          AND nota_liquidacao_paga.cod_entidade  = nota_liquidacao.cod_entidade
                                          AND nota_liquidacao_paga.cod_nota      = nota_liquidacao.cod_nota
                                          AND nota_liquidacao_paga_anulada.cod_nota IS NULL
                                        LIMIT 1 )
                    
                    AND NOT EXISTS (SELECT 1                                                                    
                                    FROM empenho.ordem_pagamento_anulada                                            
                                    WHERE ordem_pagamento_anulada.exercicio = ordem_pagamento.exercicio              
                                    AND ordem_pagamento_anulada.cod_entidade = ordem_pagamento.cod_entidade  
                                    AND ordem_pagamento_anulada.cod_ordem = ordem_pagamento.cod_ordem 
                                    LIMIT 1 
                                    )
            
            )as lala
                            
            WHERE cod_conta is NOT NULL
            AND beneficiario IS NOT NULL
            AND vl_retencao_irrf > 0 
            
            GROUP BY 1,2,3,4,5,6,7,8,9,10,11

            ORDER BY ident_especie_beneficiario
        
        )';      

    EXECUTE stSql;

    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;




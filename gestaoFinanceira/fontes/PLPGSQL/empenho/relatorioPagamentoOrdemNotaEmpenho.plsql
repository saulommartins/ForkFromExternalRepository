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
* Script de função PLPGSQL
* fn_relatorio_pagamento_ordem_nota_empenho
* Data de Criação : 03/11/2015
* @author Analista : Dagiane Vieira
* @author Desenvolvedor : Michel Teixeira
* $Id: relatorioPagamentoOrdemNotaEmpenho.plsql 66367 2016-08-18 19:06:43Z michel $
*/

CREATE OR REPLACE FUNCTION empenho.fn_relatorio_pagamento_ordem_nota_empenho(VARCHAR,VARCHAR,VARCHAR,INTEGER,VARCHAR,INTEGER,VARCHAR,INTEGER,BOOLEAN,BOOLEAN) RETURNS SETOF colunasRelatorioPagamentoOrdemNotaEmpenho AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stExercicioEmpenho      ALIAS FOR $3;
    inCodEmpenho            ALIAS FOR $4;
    stExercicioNota         ALIAS FOR $5;
    inCodNota               ALIAS FOR $6;
    stExercicioOrdem        ALIAS FOR $7;
    inCodOrdem              ALIAS FOR $8;
    boRetiraEstornado       ALIAS FOR $9;
    boRetencao              ALIAS FOR $10;

    stSql               VARCHAR := '';
    stFiltro            VARCHAR := '';
    stFiltroAuxiliar    VARCHAR := '';

    reRegistro          RECORD;
    rwRelatorioPagamentoOrdemNotaEmpenho  colunasRelatorioPagamentoOrdemNotaEmpenho%ROWTYPE;

BEGIN
    IF stExercicio IS NULL OR TRIM(stExercicio) = '' THEN
        stSql := 'SELECT DATE_PART(''YEAR'', CURRENT_TIMESTAMP)::VARCHAR AS stExercicio';
        stExercicio := selectIntoVarchar(stSql);
    END IF;
    
    IF (stCodEntidades IS NOT NULL AND TRIM(stCodEntidades)<>'') THEN
        stFiltro := stFiltro || ' AND pagamento.cod_entidade IN (' || stCodEntidades || ') ';
    END IF;
    IF (stExercicioEmpenho IS NOT NULL AND TRIM(stExercicioEmpenho)<>'') THEN
        stFiltro := stFiltro || ' AND empenho.exercicio = ''' || stExercicioEmpenho || ''' ';
    END IF;
    IF (inCodEmpenho IS NOT NULL AND inCodEmpenho > 0) THEN
        stFiltro := stFiltro || ' AND empenho.cod_empenho = ' || inCodEmpenho || ' ';
    END IF;
    IF (stExercicioNota IS NOT NULL AND TRIM(stExercicioNota)<>'') THEN
        stFiltro := stFiltro || ' AND nota_liquidacao.exercicio = ''' || stExercicioNota || ''' ';
    END IF;
    IF (inCodNota IS NOT NULL AND inCodNota > 0) THEN
        stFiltro := stFiltro || ' AND pagamento.cod_nota = ' || inCodNota || ' ';
    END IF;
    IF (stExercicioOrdem IS NOT NULL AND TRIM(stExercicioOrdem)<>'') THEN
        stFiltro := stFiltro || ' AND ordem_pagamento.exercicio = ''' || stExercicioOrdem || ''' ';
    END IF;
    IF (inCodOrdem IS NOT NULL AND inCodOrdem > 0) THEN
        stFiltro := stFiltro || ' AND ordem_pagamento.cod_ordem = ' || inCodOrdem || ' ';
    END IF;
    
    IF boRetiraEstornado IS TRUE THEN
        stFiltroAuxiliar := stFiltroAuxiliar || ' AND bo_pagamento_estornado IS FALSE ';
        stFiltroAuxiliar := stFiltroAuxiliar || ' AND bo_ordem_estornada IS FALSE ';
    END IF;
    
    IF boRetencao IS TRUE THEN
        stFiltroAuxiliar := stFiltroAuxiliar || ' AND vl_pago_retencao > 0 ';
    END IF;

    stSql := '
          SELECT *
            FROM (
                    SELECT empenho.exercicio AS exercicio_empenho
                         , pagamento.cod_entidade
                         , empenho.cod_empenho
                         , empenho.cod_pre_empenho
                         , empenho.dt_empenho
                         , pagamento.timestamp AS timestamp_pagamento
                         , pagamento.exercicio
                         , pagamento.cod_nota
                         , nota_liquidacao.exercicio AS exercicio_nota
                         , nota_liquidacao.dt_liquidacao
                         , CASE WHEN lancamento_retencao.cod_ordem IS NULL THEN
                                    nota_liquidacao_paga.vl_pago
                                ELSE
                                    0.00
                           END AS vl_pago
                         , CASE WHEN nota_liquidacao_paga_anulada.timestamp_anulada IS NOT NULL THEN
                                    TRUE
                                ELSE
                                    FALSE
                           END AS bo_pagamento_estornado
                         , nota_liquidacao_paga_anulada.timestamp_anulada AS timestamp_pagamento_anulada
                         , CASE WHEN lancamento_retencao.cod_ordem IS NOT NULL THEN
                                    nota_liquidacao_paga.vl_pago
                                ELSE
                                    0.00
                           END AS vl_pago_retencao
                         , CASE WHEN lancamento_retencao.cod_ordem IS NOT NULL THEN
                                    CASE WHEN transferencia_ordem_pagamento_retencao.cod_plano IS NOT NULL THEN
                                            ''E''::VARCHAR -- Extra-Orçamentárias
                                         ELSE
                                            ''O''::VARCHAR --Orçamentárias
                                    END
                           END AS tipo_retencao
                         , ordem_pagamento.cod_ordem
                         , ordem_pagamento.exercicio AS exercicio_ordem
                         , pagamento_liquidacao.vl_pagamento AS vl_ordem
                         , COALESCE(ordem_pagamento_retencao.vl_retencao, 0.00) AS vl_retencao
                         , CASE WHEN ordem_pagamento_anulada.timestamp IS NOT NULL THEN
                                    TRUE
                                ELSE
                                    FALSE
                           END AS bo_ordem_estornada
                         , ordem_pagamento_anulada.timestamp AS timestamp_ordem_anulada
                         , conta_despesa_empenho.cod_conta AS cod_conta_dotacao
                         , conta_despesa_empenho.cod_estrutural AS desdobramento
                         , pagamento.exercicio_plano AS exercicio_plano_pagamento
                         , pagamento.cod_plano AS cod_plano_pagamento
                         , plano_conta.cod_conta AS cod_conta_plano_pagamento
                         , plano_conta.nom_conta AS nom_conta_plano_pagamento
                         , plano_conta.cod_estrutural AS cod_estrutural_plano_pagamento
                         , ordem_pagamento_retencao.exercicio AS exercicio_plano_retencao
                         , ordem_pagamento_retencao.cod_plano AS cod_plano_retencao	  
                         , ordem_pagamento_retencao.cod_receita AS cod_receita_retencao
                         , CASE WHEN lancamento_retencao.cod_ordem IS NOT NULL THEN
                                    CASE WHEN transferencia_ordem_pagamento_retencao.cod_plano IS NOT NULL THEN
                                            plano_conta_retencao_extra_orcamentaria.nom_conta
                                         ELSE
                                            conta_receita_orcamentaria.descricao
                                    END
                           END AS nom_conta_retencao
                         , CASE WHEN lancamento_retencao.cod_ordem IS NOT NULL THEN
                                    CASE WHEN transferencia_ordem_pagamento_retencao.cod_plano IS NOT NULL THEN
                                            plano_conta_retencao_extra_orcamentaria.cod_estrutural
                                         ELSE
                                            conta_receita_orcamentaria.cod_estrutural
                                    END
                           END AS cod_estrutural_retencao

                      FROM tesouraria.pagamento
                      
                INNER JOIN empenho.nota_liquidacao_paga
                        ON nota_liquidacao_paga.exercicio       = pagamento.exercicio
                       AND nota_liquidacao_paga.cod_nota        = pagamento.cod_nota
                       AND nota_liquidacao_paga.cod_entidade    = pagamento.cod_entidade
                       AND nota_liquidacao_paga.timestamp       = pagamento.timestamp

                INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                        ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                       AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = nota_liquidacao_paga.cod_entidade
                       AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = nota_liquidacao_paga.cod_nota
                       AND pagamento_liquidacao_nota_liquidacao_paga.timestamp            = nota_liquidacao_paga.timestamp

                INNER JOIN contabilidade.pagamento AS pagamento_contabilidade
                        ON pagamento_contabilidade.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                       AND pagamento_contabilidade.cod_nota             = nota_liquidacao_paga.cod_nota
                       AND pagamento_contabilidade.cod_entidade         = nota_liquidacao_paga.cod_entidade
                       AND pagamento_contabilidade.timestamp            = nota_liquidacao_paga.timestamp
                       
                INNER JOIN contabilidade.lancamento
                        ON lancamento.exercicio     = pagamento_contabilidade.exercicio
                       AND lancamento.cod_lote      = pagamento_contabilidade.cod_lote
                       AND lancamento.tipo          = pagamento_contabilidade.tipo
                       AND lancamento.sequencia     = pagamento_contabilidade.sequencia
                       AND lancamento.cod_entidade  = pagamento_contabilidade.cod_entidade
            
                 LEFT JOIN contabilidade.lancamento_retencao
                        ON lancamento_retencao.exercicio    = lancamento.exercicio
                       AND lancamento_retencao.cod_lote     = lancamento.cod_lote
                       AND lancamento_retencao.tipo         = lancamento.tipo
                       AND lancamento_retencao.sequencia    = lancamento.sequencia
                       AND lancamento_retencao.cod_entidade = lancamento.cod_entidade
            
                INNER JOIN empenho.pagamento_liquidacao
                        ON pagamento_liquidacao.exercicio            = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                       AND pagamento_liquidacao.cod_entidade         = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                       AND pagamento_liquidacao.cod_ordem            = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
                       AND pagamento_liquidacao.exercicio_liquidacao = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                       AND pagamento_liquidacao.cod_nota             = pagamento_liquidacao_nota_liquidacao_paga.cod_nota

                INNER JOIN empenho.ordem_pagamento
                        ON ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio
                       AND ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade
                       AND ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem
            
                 LEFT JOIN empenho.ordem_pagamento_retencao
                        ON ordem_pagamento_retencao.exercicio       = ordem_pagamento.exercicio
                       AND ordem_pagamento_retencao.cod_entidade    = ordem_pagamento.cod_entidade
                       AND ordem_pagamento_retencao.cod_ordem       = ordem_pagamento.cod_ordem
                       AND ordem_pagamento_retencao.cod_ordem       = lancamento_retencao.cod_ordem
                       AND ordem_pagamento_retencao.cod_entidade    = lancamento_retencao.cod_entidade
                       AND ordem_pagamento_retencao.cod_plano       = lancamento_retencao.cod_plano
                       AND ordem_pagamento_retencao.exercicio       = lancamento_retencao.exercicio_retencao
                       AND ordem_pagamento_retencao.sequencial      = lancamento_retencao.sequencial
            
                INNER JOIN empenho.nota_liquidacao
                        ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                       AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
            
                INNER JOIN empenho.empenho
                        ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                       AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
            
                 LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao
                        ON transferencia_ordem_pagamento_retencao.exercicio     = ordem_pagamento_retencao.exercicio
                       AND transferencia_ordem_pagamento_retencao.cod_entidade  = ordem_pagamento_retencao.cod_entidade
                       AND transferencia_ordem_pagamento_retencao.cod_ordem     = ordem_pagamento_retencao.cod_ordem
                       AND transferencia_ordem_pagamento_retencao.cod_plano     = ordem_pagamento_retencao.cod_plano
                       AND transferencia_ordem_pagamento_retencao.sequencial    = ordem_pagamento_retencao.sequencial

                 LEFT JOIN empenho.pre_empenho_despesa
                        ON pre_empenho_despesa.exercicio        = empenho.exercicio
                       AND pre_empenho_despesa.cod_pre_empenho  = empenho.cod_pre_empenho

                 LEFT JOIN empenho.restos_pre_empenho
                        ON restos_pre_empenho.exercicio        = empenho.exercicio
                       AND restos_pre_empenho.cod_pre_empenho  = empenho.cod_pre_empenho

                INNER JOIN orcamento.conta_despesa AS conta_despesa_empenho
                        ON (    conta_despesa_empenho.exercicio = pre_empenho_despesa.exercicio
                            AND conta_despesa_empenho.cod_conta = pre_empenho_despesa.cod_conta
                           )
                        OR (    pre_empenho_despesa.cod_pre_empenho IS NULL
                            AND conta_despesa_empenho.exercicio = '''||stExercicio||'''
                            AND REPLACE(conta_despesa_empenho.cod_estrutural::varchar,''.'','''') = restos_pre_empenho.cod_estrutural
                           )
            
                INNER JOIN contabilidade.plano_analitica
                        ON plano_analitica.cod_plano = pagamento.cod_plano
                       AND plano_analitica.exercicio = pagamento.exercicio_plano
            
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
            
                 LEFT JOIN contabilidade.plano_analitica AS plano_analitica_retencao_extra_orcamentaria
                        ON plano_analitica_retencao_extra_orcamentaria.cod_plano = ordem_pagamento_retencao.cod_plano
                       AND plano_analitica_retencao_extra_orcamentaria.exercicio = ordem_pagamento_retencao.exercicio
            
                 LEFT JOIN contabilidade.plano_conta AS plano_conta_retencao_extra_orcamentaria
                        ON plano_conta_retencao_extra_orcamentaria.cod_conta = plano_analitica_retencao_extra_orcamentaria.cod_conta
                       AND plano_conta_retencao_extra_orcamentaria.exercicio = plano_analitica_retencao_extra_orcamentaria.exercicio 
            
                 LEFT JOIN orcamento.receita AS receita_retencao_orcamentaria
                        ON receita_retencao_orcamentaria.cod_receita    = ordem_pagamento_retencao.cod_receita
                       AND receita_retencao_orcamentaria.exercicio      = ordem_pagamento_retencao.exercicio
            
                 LEFT JOIN orcamento.conta_receita AS conta_receita_orcamentaria
                        ON conta_receita_orcamentaria.cod_conta = receita_retencao_orcamentaria.cod_conta
                       AND conta_receita_orcamentaria.exercicio = receita_retencao_orcamentaria.exercicio
            
                 LEFT JOIN empenho.nota_liquidacao_paga_anulada
                        ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                       AND nota_liquidacao_paga_anulada.cod_nota        = nota_liquidacao_paga.cod_nota
                       AND nota_liquidacao_paga_anulada.cod_entidade    = nota_liquidacao_paga.cod_entidade
                       AND nota_liquidacao_paga_anulada.timestamp       = nota_liquidacao_paga.timestamp
            
                 LEFT JOIN empenho.ordem_pagamento_anulada
                        ON ordem_pagamento_anulada.exercicio    = ordem_pagamento.exercicio
                       AND ordem_pagamento_anulada.cod_ordem    = ordem_pagamento.cod_ordem
                       AND ordem_pagamento_anulada.cod_entidade = ordem_pagamento.cod_entidade
                
                     WHERE DATE_PART(''YEAR'', pagamento.timestamp)::VARCHAR = ''' || stExercicio || '''
                       '|| stFiltro ||'

                  GROUP BY  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11
                         , 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22
                         , 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33

                 ORDER BY exercicio_empenho
                         , cod_entidade
                         , cod_empenho
                         , exercicio_nota
                         , cod_nota
                         , exercicio_ordem
                         , cod_ordem
                 ) AS fn_relatorio_pagamento_ordem_nota_empenho
           WHERE 1 = 1
           '|| stFiltroAuxiliar ||' ';

    FOR reRegistro IN  EXECUTE stSql
    LOOP
        rwRelatorioPagamentoOrdemNotaEmpenho := reRegistro;

        RETURN NEXT rwRelatorioPagamentoOrdemNotaEmpenho;
    END LOOP;

    RETURN;
END;
$$ language 'plpgsql';

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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: relatorioVariacoesPatrimoniais.plsql 64402 2016-02-16 20:59:49Z michel $
*
* Casos de uso: uc-02.02.22
                uc-02.08.07
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_variacao_patrimonial(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidade          ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stExercicioAnterior ALIAS FOR $5;
    inCod               ALIAS FOR $6;
    
    arCount             INTEGER[];
    stSql               VARCHAR := '';
    stSqlComplemento    VARCHAR := '';
    inEntidade          INTEGER := 0;
    arEntidades         INTEGER[];
    inIndex             INTEGER := 1;
    
    reRegistro          RECORD;
    reRegistroAux       RECORD;
    arRetorno           NUMERIC[];
BEGIN
    arEntidades := regexp_split_to_array(stEntidade, ',');
    While inIndex <= array_length(arEntidades ,1)
    LOOP
        IF (arEntidades[inIndex] = '1') OR (arEntidades[inIndex] = '2') OR (arEntidades[inIndex] = '3') THEN
            inEntidade := inEntidade+1;
        END IF;
        inIndex := inIndex +1;
    END LOOP;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
                SELECT *
                FROM (
                        SELECT plano_conta.cod_estrutural
                            , plano_analitica.cod_plano
                            , valor_lancamento.tipo_valor
                            , valor_lancamento.vl_lancamento
                            , valor_lancamento.cod_entidade
                            , lote.cod_lote
                            , lote.dt_lote
                            , lote.exercicio
                            , lote.tipo
                            , valor_lancamento.sequencia
                            , valor_lancamento.oid as oid_temp
                            , sistema_contabil.cod_sistema
                            , plano_conta.escrituracao
                            , plano_conta.indicador_superavit
                             
                         FROM contabilidade.plano_conta            
     
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_conta.cod_conta    = plano_analitica.cod_conta
                           AND plano_conta.exercicio    = plano_analitica.exercicio
                           
                    INNER JOIN contabilidade.conta_debito
                            ON plano_analitica.cod_plano    = conta_debito.cod_plano
                           AND plano_analitica.exercicio    = conta_debito.exercicio
                     
                    INNER JOIN contabilidade.valor_lancamento   
                            ON conta_debito.cod_lote     = valor_lancamento.cod_lote
                           AND conta_debito.tipo         = valor_lancamento.tipo
                           AND conta_debito.sequencia    = valor_lancamento.sequencia
                           AND conta_debito.exercicio    = valor_lancamento.exercicio
                           AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                           AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                    
                    INNER JOIN contabilidade.lancamento             
                            ON valor_lancamento.cod_lote     = lancamento.cod_lote
                           AND valor_lancamento.tipo         = lancamento.tipo
                           AND valor_lancamento.sequencia    = lancamento.sequencia
                           AND valor_lancamento.exercicio    = lancamento.exercicio
                           AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                           AND valor_lancamento.tipo_valor   = ' || quote_literal('D') || '
                           
                    INNER JOIN contabilidade.lote
                            ON lancamento.cod_lote     = lote.cod_lote
                           AND lancamento.exercicio    = lote.exercicio
                           AND lancamento.tipo         = lote.tipo
                           AND lancamento.cod_entidade = lote.cod_entidade
                           
                    INNER JOIN contabilidade.sistema_contabil       
                            ON sistema_contabil.cod_sistema  = plano_conta.cod_sistema
                           AND sistema_contabil.exercicio    = plano_conta.exercicio
                     
                    INNER JOIN contabilidade.historico_contabil       
                            ON historico_contabil.exercicio     = lancamento.exercicio
                           AND historico_contabil.cod_historico = lancamento.cod_historico
                         
                         WHERE plano_analitica.exercicio IN ('|| quote_literal(stExercicio) ||', '|| quote_literal(stExercicioAnterior) ||')
                           AND (  ( lote.dt_lote = to_date( ''31/12/''||EXTRACT(YEAR FROM lote.dt_lote) , ''dd/mm/yyyy'' ) AND (historico_contabil.cod_historico::varchar) NOT LIKE ''8%'' )
                               OR ( lote.dt_lote < to_date( ''31/12/''||EXTRACT(YEAR FROM lote.dt_lote) , ''dd/mm/yyyy'' ) )
                               )
     
                      ORDER BY plano_conta.cod_estrutural
                ) AS tabela
              WHERE cod_entidade IN (' || stEntidade || ')';
    
    EXECUTE stSql;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
                SELECT *
                  FROM (
                        SELECT plano_conta.cod_estrutural
                             , plano_analitica.cod_plano
                             , valor_lancamento.tipo_valor
                             , valor_lancamento.vl_lancamento
                             , valor_lancamento.cod_entidade
                             , lote.cod_lote
                             , lote.dt_lote
                             , lote.exercicio
                             , lote.tipo
                             , valor_lancamento.sequencia
                             , valor_lancamento.oid as oid_temp
                             , sistema_contabil.cod_sistema
                             , plano_conta.escrituracao
                             , plano_conta.indicador_superavit
                          FROM contabilidade.plano_conta 
     
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_conta.cod_conta    = plano_analitica.cod_conta
                           AND plano_conta.exercicio    = plano_analitica.exercicio
     
                    INNER JOIN contabilidade.conta_credito
                            ON plano_analitica.cod_plano    = conta_credito.cod_plano
                           AND plano_analitica.exercicio    = conta_credito.exercicio
                    
                    INNER JOIN contabilidade.valor_lancamento
                            ON conta_credito.cod_lote     = valor_lancamento.cod_lote
                           AND conta_credito.tipo         = valor_lancamento.tipo
                           AND conta_credito.sequencia    = valor_lancamento.sequencia
                           AND conta_credito.exercicio    = valor_lancamento.exercicio
                           AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                           AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                    
                    INNER JOIN contabilidade.lancamento
                            ON valor_lancamento.cod_lote     = lancamento.cod_lote
                           AND valor_lancamento.tipo         = lancamento.tipo
                           AND valor_lancamento.sequencia    = lancamento.sequencia
                           AND valor_lancamento.exercicio    = lancamento.exercicio
                           AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                           AND valor_lancamento.tipo_valor   = '|| quote_literal('C') ||'
                    
                    INNER JOIN contabilidade.lote
                            ON lancamento.cod_lote     = lote.cod_lote
                           AND lancamento.exercicio    = lote.exercicio
                           AND lancamento.tipo         = lote.tipo
                           AND lancamento.cod_entidade = lote.cod_entidade
     
                    INNER JOIN contabilidade.sistema_contabil
                            ON sistema_contabil.cod_sistema  = plano_conta.cod_sistema
                           AND sistema_contabil.exercicio    = plano_conta.exercicio
     
                    INNER JOIN contabilidade.historico_contabil
                            ON historico_contabil.exercicio     = lancamento.exercicio
                           AND historico_contabil.cod_historico = lancamento.cod_historico
                           
                         WHERE plano_analitica.exercicio IN ('|| quote_literal(stExercicio) ||', '|| quote_literal(stExercicioAnterior) ||')
                           AND (  ( lote.dt_lote = to_date( ''31/12/''||EXTRACT(YEAR FROM lote.dt_lote) , ''dd/mm/yyyy'' ) AND (historico_contabil.cod_historico::varchar) NOT LIKE ''8%'' )
                               OR ( lote.dt_lote < to_date( ''31/12/''||EXTRACT(YEAR FROM lote.dt_lote) , ''dd/mm/yyyy'' ) )
                               )
                    ) AS tabela
                WHERE cod_entidade IN (' || stEntidade || ')';
               
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);

    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE tipo <> quote_literal('I')
          AND dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' );
        
    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE tipo <> quote_literal('I')
          AND dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' );
        
    CREATE UNIQUE INDEX unq_totaliza_credito    ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito     ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);
    
    stSqlComplemento := 'exercicio = '''||stExercicioAnterior||''' ';
    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
        SELECT *
          FROM tmp_debito
         WHERE
               ' || stSqlComplemento || '
         UNION
        SELECT *
          FROM tmp_credito
         WHERE
               ' || stSqlComplemento || '
    ';

    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);
    
    if (inCod = 1) THEN
    
    stSql := ' SELECT
                    cod_estrutural::varchar AS cod_estrutural,
                        grupo,
                        CAST(descricao AS VARCHAR) as descricao,
                        CASE WHEN tipo = ''C'' THEN
                                CASE WHEN saldo_atual < 0 THEN
                                        coalesce(saldo_atual * -1,0.00)
                                    ELSE coalesce(saldo_atual,0.00)
                                END
                            ELSE
                                CASE WHEN saldo_atual > 0 THEN
                                        coalesce(saldo_atual * 1,0.00)
                                    ELSE coalesce(saldo_atual,0.00)
                                END
                            END AS saldo_atual,
                        CASE WHEN tipo = ''C'' THEN
                                CASE WHEN saldo_anterior < 0 THEN
                                        coalesce(saldo_anterior * -1,0.00)
                                    ELSE coalesce(saldo_anterior,0.00)
                                END
                            ELSE
                                CASE WHEN saldo_anterior > 0 THEN
                                        coalesce(saldo_anterior * 1,0.00)
                                    ELSE coalesce(saldo_anterior,0.00)
                                END
                            END AS saldo_anterior,
                        borda,
                        linha
            FROM (
              SELECT
                        cod_estrutural::varchar AS cod_estrutural,
                        grupo,
                        CAST(descricao AS VARCHAR) as descricao,
                        SUM(valores[2] * multiplicador) AS saldo_atual,
                        SUM(valores[1] * multiplicador) AS saldo_anterior,
                        borda,
                        linha,
                        tipo
                FROM (
                        --VARIAÇÕES PATRIMONIAIS AUMENTATIVAS
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.0.0.0.0.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    /*UNION ALL
                        
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.1.1.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.1.2.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.1.0.0.00.00-')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.2.1.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.7.2.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.1.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.4.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS AUMENTATIVAS'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             -------------*/
                    UNION ALL
                        -- Impostos, Taxas e Contribuições de Melhoria 
                        SELECT '||quote_literal('4.1.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.1.0.0.0.00.00')||')) AS valores
                             , ''Impostos, Taxas e Contribuições de Melhoria'' AS descricao
                             , 2 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                            , ''C'' AS tipo
                            
                    UNION ALL
                        SELECT '||quote_literal('4.1.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.1.1.0.0.00.00')||')) AS valores
                             , ''Impostos'' AS descricao
                             , 2 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                            , ''C'' AS tipo
                            
                    UNION ALL
                        SELECT '||quote_literal('4.1.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.1.2.0.0.00.00')||')) AS valores
                             , ''Taxas'' AS descricao
                             , 2 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                            , ''C'' AS tipo
                            
                    UNION ALL
                        SELECT '||quote_literal('4.1.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.1.3.0.0.00.00')||')) AS valores
                             , ''Contribuições de Melhoria'' AS descricao
                             , 2 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                            , ''C'' AS tipo
                            
                    UNION ALL
                        -- CONTRIBUIÇÕES
                        SELECT '||quote_literal('4.2.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.0.0.0.00.00')||')) AS valores
                             , ''Contribuições'' AS descricao
                             , 3 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                            , ''C'' AS tipo
                            
                    UNION ALL
                        
                        SELECT '||quote_literal('4.2.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.1.1.2.00.00')||')) AS valores
                             , ''Contribuições'' AS descricao
                             , 3 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.2.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.1.2.2.00.00-')||')) AS valores
                             , ''Contribuições'' AS descricao
                             , 3 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.2.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.1.0.0.00.00')||')) AS valores
                             , ''Contribuições Sociais'' AS descricao
                             , 3 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.2.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.1.1.2.00.00')||')) AS valores
                             , ''Contribuições Sociais'' AS descricao
                             , 3 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.2.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.2.0.0.00.00')||')) AS valores
                             , ''Contribuições de Intervenção no Domínio Econômico'' AS descricao
                             , 3 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        
                        SELECT '||quote_literal('4.2.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.3.0.0.00.00')||')) AS valores
                             , ''Contribuição de Iluminação Publica '' AS descricao
                             , 3 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                    
                    SELECT '||quote_literal('4.2.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.2.4.0.0.00.00')||')) AS valores
                             , ''Contribuições de Interesse das Categorias Profissionais '' AS descricao
                             , 3 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             --------
                    UNION ALL
                        -- Exploração e Venda de Bens, Serviços e Direitos 
                        SELECT '||quote_literal('4.3.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.3.0.0.0.00.00')||')) AS valores
                             , ''Exploração e Venda de Bens, Serviços e Direitos'' AS descricao
                             , 4 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.3.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.3.1.0.0.00.00')||')) AS valores
                             , ''Venda de Mercadorias'' AS descricao
                             , 4 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.3.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.3.2.0.0.00.00')||')) AS valores
                             , ''Venda de Produtos'' AS descricao
                             , 4 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.3.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.3.2.0.0.00.00')||')) AS valores
                             , ''Venda de Produtos'' AS descricao
                             , 4 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.3.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.3.3.0.0.00.00')||')) AS valores
                             , ''Exploração de Bens e Direitos e Prestação de Serviços'' AS descricao
                             , 4 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             -------------
                    UNION ALL
                        --Variações Patrimoniais Aumentativas Financeiras 
                        SELECT '||quote_literal('4.4.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.0.0.0.00.00')||')) AS valores
                             , ''Variações Patrimoniais Aumentativas Financeiras'' AS descricao
                             , 5 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.4.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.1.0.0.00.00')||')) AS valores
                             , ''Juros e Encargos de Empréstimos e Financiamentos Concedidos'' AS descricao
                             , 5 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo

                    UNION ALL
                        SELECT '||quote_literal('4.4.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.2.0.0.00.00')||')) AS valores
                             , ''Juros e Encargos de Mora'' AS descricao
                             , 5 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.4.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.3.0.0.00.00')||')) AS valores
                             , ''Variações Monetárias e Cambiais'' AS descricao
                             , 5 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.4.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.4.0.0.00.00')||')) AS valores
                             , ''Descontos Financeiros Obtidos'' AS descricao
                             , 5 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.4.5.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.5.0.0.00.00')||')) AS valores
                             , ''Remuneração de Depósitos Bancários e Aplicações Financeiras'' AS descricao
                             , 5 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.4.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.9.0.0.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Aumentativas - Financeiras'' AS descricao
                             , 5 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                            -------------
                    ';
                    IF inEntidade = 3 THEN
                        stSql := stSql || '
                    UNION ALL
                        --Transferências e Delegações Recebidas 
                        SELECT '||quote_literal('4.5.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.0.0.0.00.00')||')) AS valores
                             , ''Transferências e Delegações Recebidas '' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.1.0.0.00.00')||')) AS valores
                             , ''Transferências e Delegações Recebidas '' AS descricao
                             , 6 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                    ';
                    ELSE
                        stSql := stSql || '
                    UNION ALL
                        --Transferências e Delegações Recebidas 
                        SELECT '||quote_literal('4.5.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.0.0.0.00.00')||')) AS valores
                             , ''Transferências e Delegações Recebidas '' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                    ';
                    END IF;
                    
                    stSql := stSql || '
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.1.0.0.00.00')||')) AS valores
                             , ''Transferências Intragovernamentais '' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.2.0.0.00.00')||')) AS valores
                             , ''Transferências Intergovernamentais'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.3.0.0.00.00')||')) AS valores
                             , ''Transferências das Instituições Privadas'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.4.0.0.00.00')||')) AS valores
                             , ''Transferências das Instituições Multigovernamentais'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.5.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.5.0.0.00.00')||')) AS valores
                             , ''Transferências de Consórcios Públicos'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.6.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.6.0.0.00.00')||')) AS valores
                             , ''Transferências do Exterior'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.6.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.6.0.0.00.00')||')) AS valores
                             , ''Transferências do Exterior'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.7.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.7.0.0.00.00')||')) AS valores
                             , ''Delegações Recebidas'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.5.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.8.0.0.00.00')||')) AS valores
                             , ''Transferências de Pessoas Físicas'' AS descricao
                             , 6 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                            ---------------
                    UNION ALL
                        --Valorização e Ganhos Com Ativos 
                        SELECT '||quote_literal('4.6.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.6.0.0.0.00.00')||')) AS valores
                             , ''Valorização e Ganhos Com Ativos'' AS descricao
                             , 7 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.6.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.6.1.0.0.00.00')||')) AS valores
                             , ''Reavaliação de Ativos'' AS descricao
                             , 7 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.6.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.6.2.0.0.00.00')||')) AS valores
                             , ''Ganhos com Alienação'' AS descricao
                             , 7 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                    UNION ALL
                        SELECT '||quote_literal('4.6.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.6.3.0.0.00.00')||')) AS valores
                             , ''Ganhos com Incorporação de Ativos'' AS descricao
                             , 7 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo

                    UNION ALL
                        SELECT '||quote_literal('4.6.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.6.4.0.0.00.00')||')) AS valores
                             , ''Ganhos com Desincorporação de Passivos'' AS descricao
                             , 7 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo

                            --------------
                        UNION ALL
                        --Outras Variações Patrimoniais Aumentativas 
                        SELECT '||quote_literal('4.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.2.1.2.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.7.2.2.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                        UNION ALL
                        SELECT '||quote_literal('4.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.0.0.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                        UNION ALL
                        SELECT '||quote_literal('4.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.1.2.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.4.2.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.1.0.0.00.00')||')) AS valores
                             , ''Variação Patrimonial Aumentativa a Classificar'' AS descricao
                             , 8 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.2.0.0.00.00')||')) AS valores
                             , ''Resultado Positivo de Participações'' AS descricao
                             , 8 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.2.1.2.00.00')||')) AS valores
                             , ''Resultado Positivo de Participações'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.7.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.7.0.0.00.00')||')) AS valores
                             , ''Reversão de Provisões e Ajustes de Perdas'' AS descricao
                             , 8 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.7.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.7.2.2.00.00')||')) AS valores
                             , ''Reversão de Provisões e Ajustes de Perdas'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.0.0.00.00')||')) AS valores
                             , ''Diversas Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.1.2.00.00')||')) AS valores
                             , ''Diversas Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('4.9.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.9.9.4.2.00.00')||')) AS valores
                             , ''Diversas Variações Patrimoniais Aumentativas'' AS descricao
                             , 8 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''C'' AS tipo
                        -----------------------------
                        UNION ALL
                        --VARIAÇÕES PATRIMONIAIS DIMINUTIVAS 
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.0.0.0.0.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                       /* UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.1.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.2.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.9.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.2.9.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.1.0.0.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.1.4.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.1.7.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.2.1.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.1.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.2.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.3.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.2.1.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.0.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.9.4.2.00.00')||')) AS valores
                             , ''VARIAÇÕES PATRIMONIAIS DIMINUTIVAS'' AS descricao
                             , 9 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             -------------*/
                        UNION ALL
                        --Pessoal e Encargos 
                        SELECT '||quote_literal('3.1.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.0.0.0.00.00')||')) AS valores
                             , ''Pessoal e Encargos'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.1.2.00.00')||')) AS valores
                             , ''Pessoal e Encargos'' AS descricao
                             , 10 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.2.2.00.00')||')) AS valores
                             , ''Pessoal e Encargos'' AS descricao
                             , 10 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.9.2.00.00')||')) AS valores
                             , ''Pessoal e Encargos'' AS descricao
                             , 10 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.1.0.0.00.00')||')) AS valores
                             , ''Remuneração a Pessoal'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.0.0.00.00')||')) AS valores
                             , ''Encargos Patronais'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.1.2.00.00')||')) AS valores
                             , ''Encargos Patronais'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.2.2.00.00')||')) AS valores
                             , ''Encargos Patronais'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.2.9.2.00.00')||')) AS valores
                             , ''Encargos Patronais'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.3.0.0.00.00')||')) AS valores
                             , ''Benefícios a Pessoal'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.8.0.0.00.00')||')) AS valores
                             , ''Custo de Pessoal e Encargos'' AS descricao
                             , 10 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.1.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.1.9.0.0.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Diminutivas - Pessoal e Encargos'' AS descricao
                             , 10 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             --------------------------
                        UNION ALL
                        --Benefícios Previdenciários e Assistenciais 
                        SELECT '||quote_literal('3.2.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.2.0.0.0.00.00')||')) AS valores
                             , ''Benefícios Previdenciários e Assistenciais'' AS descricao
                             , 11 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.2.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.2.1.0.0.00.00')||')) AS valores
                             , ''Aposentadorias e Reformas'' AS descricao
                             , 11 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.2.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.2.2.0.0.00.00')||')) AS valores
                             , ''Pensões'' AS descricao
                             , 11 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.2.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.2.3.0.0.00.00')||')) AS valores
                             , ''Benefícios de Prestação Continuada'' AS descricao
                             , 11 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.2.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.2.4.0.0.00.00')||')) AS valores
                             , ''Benefícios Eventuais'' AS descricao
                             , 11 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.2.5.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.2.5.0.0.00.00')||')) AS valores
                             , ''Políticas Publicas de Transferência de Renda'' AS descricao
                             , 11 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.2.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.2.9.0.0.00.00')||')) AS valores
                             , ''Outros Benefícios Previdenciários e Assistenciais'' AS descricao
                             , 11 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             ------------------
                        UNION ALL
                        --Uso de Bens, Serviços e Consumo de Capital Fixo
                        SELECT '||quote_literal('3.3.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.3.0.0.0.00.00')||')) AS valores
                             , ''Uso de Bens, Serviços e Consumo de Capital Fixo'' AS descricao
                             , 12 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.3.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.3.1.0.0.00.00')||')) AS valores
                             , ''Uso de Material de Consumo'' AS descricao
                             , 12 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.3.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.3.2.0.0.00.00')||')) AS valores
                             , ''Serviços'' AS descricao
                             , 12 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.3.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.3.3.0.0.00.00')||')) AS valores
                             , ''Depreciação, Amortização de Exaustão'' AS descricao
                             , 12 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.3.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.3.8.0.0.00.00')||')) AS valores
                             , ''Custo de Materiais, Serviços e Consumo de Capital Fixo'' AS descricao
                             , 12 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             ----------------
                        UNION ALL
                        --Variações Patrimoniais Diminutivas Financeiras 
                        SELECT '||quote_literal('3.4.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.0.0.0.00.00')||')) AS valores
                             , ''Variações Patrimoniais Diminutivas Financeiras'' AS descricao
                             , 13 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.4.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.2.9.2.00.00')||')) AS valores
                             , ''Variações Patrimoniais Diminutivas Financeiras'' AS descricao
                             , 13 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.4.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.1.0.0.00.00')||')) AS valores
                             , ''Juros e Encargos de Empréstimos e Financiamentos Obtidos '' AS descricao
                             , 13 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.4.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.2.0.0.00.00')||')) AS valores
                             , ''Juros e Encargos de Mora'' AS descricao
                             , 13 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.4.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.2.9.2.00.00')||')) AS valores
                             , ''Juros e Encargos de Mora'' AS descricao
                             , 13 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.4.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.3.0.0.00.00')||')) AS valores
                             , ''Variações Monetárias e Cambiais'' AS descricao
                             , 13 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.4.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.4.0.0.00.00')||')) AS valores
                             , ''Descontos Financeiros Concedidos'' AS descricao
                             , 13 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.4.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.4.9.0.0.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Diminutivas - Financeiras'' AS descricao
                             , 13 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             --------------
                        ';
                        IF inEntidade = 3 THEN
                        stSql := stSql || '
                    UNION ALL
                        --Transferências e Delegações Concedidas 
                        SELECT '||quote_literal('3.5.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.0.0.0.00.00')||')) AS valores
                             , ''Transferências e Delegações Concedidas'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.1.0.0.00.00')||')) AS valores
                             , ''Transferências e Delegações Concedidas'' AS descricao
                             , 14 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                    ';
                    ELSE
                        stSql := stSql || '
                    UNION ALL
                        --Transferências e Delegações Concedidas 
                        SELECT '||quote_literal('3.5.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.0.0.0.00.00')||')) AS valores
                             , ''Transferências e Delegações Concedidas'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                    ';
                    END IF;
                    
                    stSql := stSql || '
                        UNION ALL
                        SELECT '||quote_literal('3.5.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.1.0.0.00.00')||')) AS valores
                             , ''Transferências Intra Governamentais'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.2.0.0.00.00')||')) AS valores
                             , ''Transferências Inter Governamentais'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.3.0.0.00.00')||')) AS valores
                             , ''Transferências a Instituições Privadas'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.3.0.0.00.00')||')) AS valores
                             , ''Transferências a Instituições Privadas'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.4.0.0.00.00')||')) AS valores
                             , ''Transferências a Instituições Multigovernamentais'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.5.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.5.0.0.00.00')||')) AS valores
                             , ''Transferências a Consórcios Públicos'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.6.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.6.0.0.00.00')||')) AS valores
                             , ''Transferências ao Exterior'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.5.7.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.5.7.0.0.00.00')||')) AS valores
                             , ''Execução Orçamentária Delegada'' AS descricao
                             , 14 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             -----------
                        UNION ALL
                        --Desvalorização e Perda de Ativos 
                        SELECT '||quote_literal('3.6.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.0.0.0.00.00')||')) AS valores
                             , ''Desvalorização e Perda de Ativos'' AS descricao
                             , 15 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.6.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.1.4.2.00.00')||')) AS valores
                             , ''Desvalorização e Perda de Ativos'' AS descricao
                             , 15 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.6.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.1.7.2.00.00')||')) AS valores
                             , ''Desvalorização e Perda de Ativos'' AS descricao
                             , 15 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.6.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.1.0.0.00.00')||')) AS valores
                             , ''Redução a Valor Recuperável e Provisão para Perdas'' AS descricao
                             , 15 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.6.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.1.4.2.00.00')||')) AS valores
                             , ''Redução a Valor Recuperável e Provisão para Perdas'' AS descricao
                             , 15 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.6.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.1.7.2.00.00')||')) AS valores
                             , ''Redução a Valor Recuperável e Provisão para Perdas'' AS descricao
                             , 15 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.6.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.2.0.0.00.00')||')) AS valores
                             , ''Perdas com Alienação'' AS descricao
                             , 15 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.6.3.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.6.3.0.0.00.00')||')) AS valores
                             , ''Perdas Involuntárias'' AS descricao
                             , 15 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             ----------------------
                        UNION ALL
                        --Tributarias 
                        SELECT '||quote_literal('3.7.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.0.0.0.00.00')||')) AS valores
                             , ''Tributarias'' AS descricao
                             , 16 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.2.1.2.00.0')||')) AS valores
                             , ''Tributarias'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.1.2.00.00')||')) AS valores
                             , ''Tributarias'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.2.2.00.00')||')) AS valores
                             , ''Tributarias'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.3.2.00.00')||')) AS valores
                             , ''Tributarias'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.1.0.0.00.00')||')) AS valores
                             , ''Impostos, Taxas e Contribuições de Melhoria'' AS descricao
                             , 16 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.2.0.0.00.00')||')) AS valores
                             , ''Contribuições'' AS descricao
                             , 16 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.2.1.2.00.00')||')) AS valores
                             , ''Contribuições'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.0.0.00.00')||')) AS valores
                             , ''Custo com Tributos'' AS descricao
                             , 16 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.1.2.00.00')||')) AS valores
                             , ''Custo com Tributos'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.2.2.00.00')||')) AS valores
                             , ''Custo com Tributos'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                            , 1 AS borda
                            , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.7.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.7.8.3.2.00.00')||')) AS valores
                             , ''Custo com Tributos'' AS descricao
                             , 16 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             ------------------
                        UNION ALL
                        --Outras Variações Patrimoniais Diminutivas 
                        SELECT '||quote_literal('3.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.0.0.0.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Diminutivas'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.2.1.2.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Diminutivas'' AS descricao
                             , 17 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.9.4.2.00.00')||')) AS valores
                             , ''Outras Variações Patrimoniais Diminutivas'' AS descricao
                             , 17 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 1 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.1.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.1.0.0.00.00')||')) AS valores
                             , ''Premiações'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.2.0.0.00.00')||')) AS valores
                             , ''Resultado Negativo de Participações'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.2.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.2.1.2.00.00')||')) AS valores
                             , ''Resultado Negativo de Participações'' AS descricao
                             , 17 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.4.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.4.0.0.00.00')||')) AS valores
                             , ''Incentivos'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.5.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.5.0.0.00.00')||')) AS valores
                             , ''Subvenções Econômicas'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.6.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.6.0.0.00.00')||')) AS valores
                             , ''Participações e Contribuições'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.7.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.7.0.0.00.00')||')) AS valores
                             , ''VPD de Constituição de Provisões'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.8.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.8.0.0.00.00')||')) AS valores
                             , ''Custo de Outras VPD'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.9.0.0.00.00')||')) AS valores
                             , ''Diversas Variações Patrimoniais Diminutivas'' AS descricao
                             , 17 AS grupo
                             , 1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                        UNION ALL
                        SELECT '||quote_literal('3.9.9.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('3.9.9.4.2.00.00')||')) AS valores
                             , ''Diversas Variações Patrimoniais Diminutivas'' AS descricao
                             , 17 AS grupo
                             , -1 AS multiplicador
                             , 1 AS borda
                             , 0 AS linha
                             , ''D'' AS tipo
                             
                ) as resultado
                GROUP BY resultado.cod_estrutural, resultado.grupo, resultado.descricao, resultado.borda, resultado.linha, resultado.tipo
            ) as tabela
    ';
    
    ELSE
    
        stSql := 'SELECT
                        cod_estrutural::varchar AS cod_estrutural,
                        grupo,
                        CAST(descricao AS VARCHAR) as descricao,
                        SUM(valores[2] * multiplicador) AS saldo_atual,
                        SUM(valores[1] * multiplicador) AS saldo_anterior,
                        borda,
                        linha
                FROM (
                        --VARIAÇÕES PATRIMONIAIS AUMENTATIVAS
                        SELECT '||quote_literal('4.4.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.4.0.0.0.00.00')||')) AS valores
                             , ''Incorporação de ativo'' AS descricao
                             , 1 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             
                     UNION ALL
                        SELECT '||quote_literal('4.4.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.5.0.0.0.00.00')||')) AS valores
                             , ''Incorporação de ativo'' AS descricao
                             , 1 AS grupo
                             , -1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             
                    UNION ALL
                        SELECT '||quote_literal('4.6.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('4.6.0.0.0.00.00')||')) AS valores
                             , ''Desincorporação de passivo'' AS descricao
                             , 2 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             
                    UNION ALL
                        SELECT '||quote_literal('2.1.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('2.1.0.0.0.00.00')||')) AS valores
                             , ''Incorporação de passivo'' AS descricao
                             , 3 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             
                    UNION ALL
                        SELECT '||quote_literal('2.2.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('2.2.0.0.0.00.00')||')) AS valores
                             , ''Desincorporação de ativo'' AS descricao
                             , 4 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                             
                    UNION ALL
                        SELECT '||quote_literal('2.2.0.0.0.00.00')||' AS cod_estrutural
                             , contabilidade.fn_totaliza_variacao_patrimonial(publico.fn_mascarareduzida('||quote_literal('2.3.0.0.0.00.00')||')) AS valores
                             , ''Desincorporação de ativo'' AS descricao
                             , 4 AS grupo
                             , 1 AS multiplicador
                             , 0 AS borda
                             , 0 AS linha
                    ) as resultado
            GROUP BY cod_estrutural, grupo, descricao, borda, linha';
            
    END IF;
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;
        
    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;
    
    RETURN;
END;
$$ LANGUAGE 'plpgsql';
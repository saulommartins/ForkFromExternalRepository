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
* $Revision: 63111 $
* $Name$
* $Author: arthur $
* $Date: 2015-07-27 15:34:04 -0300 (Mon, 27 Jul 2015) $
*
* Casos de uso: uc-02.02.11
*/

CREATE OR REPLACE FUNCTION tcmgo.ativo_permanente_creditos (VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;    
    stCodEntidades      ALIAS FOR $5;

    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    stNomEntidade       VARCHAR   := '';
    stCodEntidadesAux   VARCHAR   := '';
    arCodEntidade       VARCHAR[];
    arNomEntidade       VARCHAR;

    reRegistro          RECORD;
    arRetorno           NUMERIC[];
BEGIN

    arCodEntidade := string_to_array(stCodEntidades,',');

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
                    
                    FROM contabilidade.plano_conta
                       
             INNER JOIN contabilidade.plano_analitica
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                   
             INNER JOIN contabilidade.conta_debito   
                     ON plano_analitica.cod_plano = conta_debito.cod_plano
                    AND plano_analitica.exercicio = conta_debito.exercicio
                    
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
                    AND valor_lancamento.tipo_valor   = ''D''
                    
             INNER JOIN contabilidade.lote
                     ON lancamento.cod_lote     = lote.cod_lote
                    AND lancamento.exercicio    = lote.exercicio
                    AND lancamento.tipo         = lote.tipo
                    AND lancamento.cod_entidade = lote.cod_entidade
                    
                  WHERE plano_analitica.exercicio = ' || quote_literal(stExercicio) || '
                    AND conta_debito.cod_entidade IN (' || stCodEntidades || ')
               
               ORDER BY plano_conta.cod_estrutural

                  ) AS tabela
            WHERE ' || stFiltro ;
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
                    
                    FROM contabilidade.plano_conta

             INNER JOIN contabilidade.plano_analitica
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                    
             INNER JOIN contabilidade.conta_credito
                     ON plano_analitica.cod_plano = conta_credito.cod_plano
                    AND plano_analitica.exercicio = conta_credito.exercicio

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
                    AND valor_lancamento.tipo_valor   = ''C''
                    
             INNER JOIN contabilidade.lote
                     ON lancamento.cod_lote     = lote.cod_lote
                    AND lancamento.exercicio    = lote.exercicio
                    AND lancamento.tipo         = lote.tipo
                    AND lancamento.cod_entidade = lote.cod_entidade
                    
                  WHERE plano_analitica.exercicio = ' || quote_literal(stExercicio) || '
                    AND conta_credito.cod_entidade IN (' || stCodEntidades || ')
                    
               ORDER BY plano_conta.cod_estrutural
               
                ) AS tabela
              WHERE ' || stFiltro ;
    EXECUTE stSql;
    
    CREATE UNIQUE INDEX unq_debito              ON tmp_debito           (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito             ON tmp_credito          (cod_estrutural varchar_pattern_ops, oid_temp);

    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
        SELECT *
        FROM  tmp_debito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
        SELECT *
        FROM  tmp_credito
        WHERE dt_lote BETWEEN to_date( stDtInicial , 'dd/mm/yyyy' ) AND   to_date( stDtFinal , 'dd/mm/yyyy' )
        AND   tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito  ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, oid_temp);

    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
                    SELECT * 
                      FROM tmp_debito
                     WHERE dt_lote = to_date( ' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')
                       AND tipo = ''I''
                UNION
                    SELECT * 
                      FROM tmp_credito
                     WHERE dt_lote = to_date( ' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')
                       AND tipo = ''I''
        ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza            ON tmp_totaliza         (cod_estrutural varchar_pattern_ops, oid_temp);


-- Faz as consultas para cada entidade 
    ---------------------------------------
    FOR i IN 1..ARRAY_UPPER(arCodEntidade,1) LOOP

            stSql :='  SELECT plano_conta.cod_estrutural
                            , publico.fn_nivel(plano_conta.cod_estrutural) as nivel
                            , plano_conta.nom_conta
                            , configuracao_orgao_unidade.num_orgao AS cod_orgao
                            , configuracao_orgao_unidade.num_unidade AS cod_unidade
                            , 0.00 as vl_saldo_anterior
                            , 0.00 as vl_saldo_debitos
                            , 0.00 as vl_saldo_creditos
                            , 0.00 as vl_saldo_atual
                            , sistema_contabil.nom_sistema
                            , balanco_apcaaaa.tipo_lancamento
                         
                        FROM contabilidade.plano_conta
        
                    INNER JOIN contabilidade.sistema_contabil
                            ON plano_conta.cod_sistema = sistema_contabil.cod_sistema
                           AND plano_conta.exercicio   = sistema_contabil.exercicio
        
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_conta = plano_conta.cod_conta
                           AND plano_analitica.exercicio = plano_conta.exercicio
    
                        LEFT JOIN tcmgo.balanco_apcaaaa
                               ON balanco_apcaaaa.cod_plano   = plano_analitica.cod_plano
                              AND balanco_apcaaaa.exercicio   = plano_analitica.exercicio
    
                        INNER JOIN (SELECT exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor, cod_plano
                                      FROM contabilidade.conta_credito
                                     UNION
                                    SELECT exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor, cod_plano
                                      FROM contabilidade.conta_debito
                                ) AS contas
                                ON contas.cod_plano = plano_analitica.cod_plano
                               AND contas.exercicio = plano_analitica.exercicio
    
                        INNER JOIN contabilidade.valor_lancamento
                                ON valor_lancamento.cod_entidade = contas.cod_entidade
                               AND valor_lancamento.exercicio    = contas.exercicio
                               AND valor_lancamento.tipo         = contas.tipo
                               AND valor_lancamento.cod_lote     = contas.cod_lote
                               AND valor_lancamento.sequencia    = contas.sequencia
                               AND valor_lancamento.tipo_valor   = contas.tipo_valor

                        INNER JOIN tcmgo.configuracao_orgao_unidade
                                ON configuracao_orgao_unidade.cod_entidade = contas.cod_entidade
                               AND configuracao_orgao_unidade.exercicio    = contas.exercicio

                        INNER JOIN orcamento.unidade
                                ON unidade.exercicio = configuracao_orgao_unidade.exercicio
                               AND unidade.num_orgao = configuracao_orgao_unidade.num_orgao
                               AND unidade.num_unidade = configuracao_orgao_unidade.num_unidade

                        
                        WHERE plano_conta.exercicio = ' || quote_literal(stExercicio) || '
                          AND configuracao_orgao_unidade.cod_entidade = ' || arCodEntidade[i] || '
                            
                   ORDER BY sistema_contabil.nom_sistema
                          , plano_conta.cod_estrutural ';
                
                FOR reRegistro IN EXECUTE stSql
                LOOP
                    arRetorno := contabilidade.fn_totaliza_balancete_verificacao( publico.fn_mascarareduzida(reRegistro.cod_estrutural) , stDtInicial, stDtFinal);
                    
                    reRegistro.vl_saldo_anterior := arRetorno[1];
                    reRegistro.vl_saldo_debitos  := arRetorno[2];
                    reRegistro.vl_saldo_creditos := arRetorno[3];
                    reRegistro.vl_saldo_atual    := arRetorno[4];

                    IF  ( reRegistro.vl_saldo_anterior <> 0.00 ) OR
                        ( reRegistro.vl_saldo_debitos  <> 0.00 ) OR
                        ( reRegistro.vl_saldo_creditos <> 0.00 )
                        THEN
                            RETURN NEXT reRegistro;
                    END IF;
                END LOOP;
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
$$ LANGUAGE 'plpgsql'

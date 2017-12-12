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
* $Revision: 17524 $
* $Name$
* $Author: cako $
* $Date: 2006-11-09 13:34:45 -0200 (Qui, 09 Nov 2006) $
*
* Casos de uso: uc-02.02.22
                uc-02.08.07
*/

/*
CREATE TYPE variacao_patrimonial_estrutural AS (
    cod_estrutural VARCHAR
  , nivel INTEGER                                                      
  , nom_conta VARCHAR
  , cod_sistema INTEGER
  , indicador_superavit CHAR
  , vl_saldo_anterior NUMERIC                                                   
  , vl_saldo_debitos  NUMERIC                                                   
  , vl_saldo_creditos NUMERIC                                                   
  , vl_saldo_atual    NUMERIC
);
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_variacao_patrimonial_estrutural(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF variacao_patrimonial_estrutural AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidade          ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR := '';
    stSqlComplemento    VARCHAR := '';
    arRetorno           NUMERIC[];
    reRegistro          RECORD;
BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_debito AS
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
                       , plano_conta.indicador_superavit::CHAR
                         
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
                  
              INNER JOIN contabilidade.sistema_contabil 
                      ON sistema_contabil.cod_sistema  = plano_conta.cod_sistema
                     AND sistema_contabil.exercicio    = plano_conta.exercicio
              
              INNER JOIN contabilidade.historico_contabil       
                      ON historico_contabil.exercicio     = lancamento.exercicio
                     AND historico_contabil.cod_historico = lancamento.cod_historico
                  
                  WHERE plano_analitica.exercicio = '||quote_literal(stExercicio)||'
                  AND dt_lote BETWEEN to_date( ''01/01/'||stExercicio||''' , ''dd/mm/yyyy'' ) AND to_date('''||stDtFinal||''' , ''dd/mm/yyyy'' )
                  AND lote.tipo <> ''I''
                  AND valor_lancamento.cod_entidade IN ('||stEntidade||')
                  AND (historico_contabil.cod_historico::varchar) NOT LIKE ''8%''
               ORDER BY plano_conta.cod_estrutural
            ';
    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_credito AS
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
                       ON valor_lancamento.cod_lote     = contabilidade.lancamento.cod_lote
                      AND valor_lancamento.tipo         = contabilidade.lancamento.tipo
                      AND valor_lancamento.sequencia    = contabilidade.lancamento.sequencia
                      AND valor_lancamento.exercicio    = contabilidade.lancamento.exercicio
                      AND valor_lancamento.cod_entidade = contabilidade.lancamento.cod_entidade
                      AND valor_lancamento.tipo_valor   = ''C''
                    
               INNER JOIN contabilidade.lote
                       ON contabilidade.lancamento.cod_lote     = lote.cod_lote
                      AND contabilidade.lancamento.exercicio    = lote.exercicio
                      AND contabilidade.lancamento.tipo         = lote.tipo
                      AND contabilidade.lancamento.cod_entidade = lote.cod_entidade
                    
               INNER JOIN contabilidade.sistema_contabil
                       ON sistema_contabil.cod_sistema  = plano_conta.cod_sistema
                      AND sistema_contabil.exercicio    = plano_conta.exercicio
                    
               INNER JOIN contabilidade.historico_contabil       
                      ON historico_contabil.exercicio     = lancamento.exercicio
                     AND historico_contabil.cod_historico = lancamento.cod_historico

                    WHERE plano_analitica.exercicio = '||quote_literal(stExercicio)||'
                    AND dt_lote BETWEEN to_date( ''01/01/'||stExercicio||''' , ''dd/mm/yyyy'' ) AND to_date('''||stDtFinal||''' , ''dd/mm/yyyy'' )
                    AND lote.tipo <> ''I''
                    AND valor_lancamento.cod_entidade IN ('||stEntidade||')
                    AND (historico_contabil.cod_historico::varchar) NOT LIKE ''8%''
                 ORDER BY plano_conta.cod_estrutural
                ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_debito  ON tmp_debito  (cod_estrutural varchar_pattern_ops, oid_temp);
    CREATE UNIQUE INDEX unq_credito ON tmp_credito (cod_estrutural varchar_pattern_ops, oid_temp);
    
    IF substr(stDtInicial,1,5) = '01/01' THEN
        stSqlComplemento := ' dt_lote = to_date( ' || quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = '||quote_literal('I')||' ';
    ELSE
        stSqlComplemento := ' dt_lote <= to_date( ' || quote_literal(stDtInicial) || ',' || quote_literal('dd/mm/yyyy') || ')-1 ';
    END IF;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_totaliza AS
                SELECT *
                  FROM tmp_debito
                 WHERE ' || stSqlComplemento || '
               
               UNION ALL
         
               SELECT *
                 FROM tmp_credito
                WHERE ' || stSqlComplemento || '
            ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza ON tmp_totaliza (cod_estrutural varchar_pattern_ops, oid_temp);

    stSql := 'CREATE TEMPORARY TABLE tmp_diminutivas AS (
                SELECT plano_conta.cod_estrutural
                     , publico.fn_nivel(plano_conta.cod_estrutural) as nivel
                     , plano_conta.nom_conta
                     , 1 as cod_sistema
                     , plano_conta.indicador_superavit::CHAR
                     , 0.00 as vl_saldo_anterior
                     , 0.00 as vl_saldo_debitos
                     , 0.00 as vl_saldo_creditos
                     , 0.00 as vl_saldo_atual
                 
                  FROM contabilidade.plano_conta
                 WHERE plano_conta.exercicio = '||quote_literal(stExercicio)||'
                   AND plano_conta.cod_estrutural ILIKE ''3.%''
                 ORDER BY cod_estrutural
                )';
    EXECUTE stSql;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_aumentativas AS (
                SELECT plano_conta.cod_estrutural
                     , publico.fn_nivel(plano_conta.cod_estrutural) as nivel
                     , plano_conta.nom_conta
                     , 1 as cod_sistema
                     , plano_conta.indicador_superavit::CHAR
                     , 0.00 as vl_saldo_anterior
                     , 0.00 as vl_saldo_debitos
                     , 0.00 as vl_saldo_creditos
                     , 0.00 as vl_saldo_atual
                 
                  FROM contabilidade.plano_conta
                   
                 WHERE plano_conta.exercicio = '||quote_literal(stExercicio)||'
                   AND plano_conta.cod_estrutural ILIKE ''4.%''
                   
                 ORDER BY cod_estrutural
                )';
                
    EXECUTE stSql;
    
    stSql := '  SELECT *
                  FROM tmp_diminutivas
                
                UNION ALL
                
                SELECT *
                  FROM tmp_aumentativas
                ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := contabilidade.fn_totaliza_variacao_patrimonial_estrutrural( publico.fn_mascarareduzida(reRegistro.cod_estrutural) );
        reRegistro.vl_saldo_anterior := arRetorno[1];
        reRegistro.vl_saldo_atual    := arRetorno[2];
        
        RETURN NEXT reRegistro;
        
    END LOOP;

    DROP INDEX unq_totaliza;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;
    
    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_diminutivas;
    DROP TABLE tmp_aumentativas;
    
    RETURN;
END;
$$ LANGUAGE 'plpgsql';
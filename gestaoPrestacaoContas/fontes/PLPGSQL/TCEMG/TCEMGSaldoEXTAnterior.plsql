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
 * Script de função PLPGSQL - Relatório STN - RREO - Anexo 9
 *
 * URBEM Soluções de Gestão Pública Ltda
 * www.urbem.cnm.org.br
 *
 * Casos de uso: uc-06.01.10
 * 
 * $Id: $
 */

/**
 * Recebe como paramentro exercicio, entidade, periodo
 */

CREATE OR REPLACE FUNCTION tcemg.fn_saldo_ext_anterior(varchar,varchar,integer,integer,integer) RETURNS NUMERIC AS $$
DECLARE
  
  stExercicio         ALIAS FOR $1;
  stCodEntidade       ALIAS FOR $2;
  inMes               ALIAS FOR $3;
  inTipo              ALIAS FOR $4; -- código para saber se é despesa ou receita; 1 para receita e 2 para despesa.
  inCodPlano          ALIAS FOR $5;

  dtInicial           VARCHAR := '';
  dtFinal             VARCHAR := '';
  inMesAnterior       INTEGER;
  stExercicioAnterior VARCHAR := ''; 
  stSql               VARCHAR := '';
  stCondicao          VARCHAR := '';
  reRegistro          NUMERIC;

BEGIN

    stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));
    inMesAnterior := inMes -1;

    IF inMes = 1 THEN
        dtInicial := '01/01/' || stExercicioAnterior;
        dtFinal := TO_CHAR(last_day(TO_DATE(stExercicioAnterior || '-' || 12 || '-' || '01','yyyy-mm-dd')),'dd/mm/yyyy');
        stCondicao := 'I';
    ELSE dtInicial := '01/' || inMesAnterior || '/' || stExercicio;
        dtFinal := TO_CHAR(last_day(TO_DATE(stExercicio || '-' || inMesAnterior || '-' || '01','yyyy-mm-dd')),'dd/mm/yyyy');
        stCondicao := 'T';
    END IF;
  
    stSql := '
            SELECT
                    COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) AS vl_anterior
                    
		     FROM tcemg.balancete_extmmaa
                     
                    JOIN contabilidade.plano_analitica
                      ON balancete_extmmaa.exercicio = plano_analitica.exercicio
                     AND balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                    
                    JOIN contabilidade.plano_conta
                      ON plano_analitica.exercicio = plano_conta.exercicio
                     AND plano_analitica.cod_conta = plano_conta.cod_conta
                    ';
                    
    IF inTipo = 1 THEN
        stSql := stSql || 'JOIN contabilidade.conta_credito
                             ON plano_analitica.exercicio = conta_credito.exercicio
                            AND plano_analitica.cod_plano = conta_credito.cod_plano';
    ELSE
        stSql := stSql || 'JOIN contabilidade.conta_debito
                             ON plano_analitica.exercicio = conta_debito.exercicio
                            AND plano_analitica.cod_plano = conta_debito.cod_plano';
    END IF;
    
    stSql := stSql || '
    
                    JOIN contabilidade.plano_recurso
                      ON plano_recurso.exercicio = plano_analitica.exercicio
                     AND plano_recurso.cod_plano = plano_analitica.cod_plano
                     
		    JOIN orcamento.recurso
		      ON recurso.exercicio = plano_recurso.exercicio
		     AND recurso.cod_recurso = plano_recurso.cod_recurso
                     ';
                     
    IF inTipo = 1 THEN
        stSql := stSql || 'JOIN contabilidade.valor_lancamento
                             ON conta_credito.exercicio = valor_lancamento.exercicio
                            AND conta_credito.cod_entidade = valor_lancamento.cod_entidade  
                            AND conta_credito.tipo = valor_lancamento.tipo          
                            AND conta_credito.cod_lote = valor_lancamento.cod_lote      
                            AND conta_credito.sequencia = valor_lancamento.sequencia     
                            AND conta_credito.tipo_valor = valor_lancamento.tipo_valor                        
                            AND conta_credito.tipo = '''|| stCondicao ||'''
                            ';
    ELSE
        stSql := stSql || 'JOIN contabilidade.valor_lancamento
                             ON conta_debito.exercicio = valor_lancamento.exercicio
                            AND conta_debito.cod_entidade = valor_lancamento.cod_entidade  
                            AND conta_debito.tipo = valor_lancamento.tipo          
                            AND conta_debito.cod_lote = valor_lancamento.cod_lote      
                            AND conta_debito.sequencia = valor_lancamento.sequencia     
                            AND conta_debito.tipo_valor = valor_lancamento.tipo_valor                        
                            AND conta_debito.tipo = '''|| stCondicao ||'''
                            ';
    END IF;
    
    stSql := stSql || '
    
                    JOIN contabilidade.lote
                      ON valor_lancamento.exercicio = lote.exercicio     
                     AND valor_lancamento.cod_entidade = lote.cod_entidade  
                     AND valor_lancamento.tipo = lote.tipo          
                     AND valor_lancamento.cod_lote = lote.cod_lote
                    ';
                    
    
    stSql := stSql || 'WHERE balancete_extmmaa.exercicio = ''' || stExercicio || '''';
   
    IF inMes = 1 THEN 
        stSql := stSql || '
                      AND valor_lancamento.cod_entidade IN (' || stCodEntidade || ')                      
                      AND balancete_extmmaa.cod_plano = ' || inCodPlano || '
                    ';
    ELSE
        stSql := stSql || '
                      AND valor_lancamento.cod_entidade IN (' || stCodEntidade || ')
                      AND lote.dt_lote >= TO_DATE(''' || dtInicial || ''', ''dd/mm/yyyy'')
                      AND lote.dt_lote <= TO_DATE(''' || dtFinal || ''', ''dd/mm/yyyy'')
                      AND balancete_extmmaa.cod_plano = ' || inCodPlano || '
                    ';
    END IF;
    
    FOR reRegistro IN EXECUTE stSql LOOP
        RETURN reRegistro;
    END LOOP;

END;
$$ language 'plpgsql';

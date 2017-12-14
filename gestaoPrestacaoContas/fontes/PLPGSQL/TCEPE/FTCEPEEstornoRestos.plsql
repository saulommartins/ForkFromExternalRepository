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
    * Comentário sobre a finalidade do arquivo. 
    * Data de Criação: 13/03/2008


    * @author Franver Sarmento de Moraes

    * Casos de uso: uc-02.03.09 

    $Id: FTCEPEEstornoRestos.plsql 60554 2014-10-28 16:21:46Z franver $

*/
CREATE OR REPLACE FUNCTION tcepe.fn_estorno_restos(varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stDataInicial ALIAS FOR $1;
    stDataFinal   ALIAS FOR $2;
    stCodEntidade ALIAS FOR $3;

    stSql            VARCHAR := '';
    stSqlExercicio   VARCHAR := '';
    stExercicioAtual VARCHAR := '';
    reReg      RECORD;
    reRegistro RECORD;
    
BEGIN

    CREATE TEMPORARY TABLE tmp_empenhos (
        exercicio_empenho        CHAR(4),
        cod_unidade_orcamentaria VARCHAR,
        num_empenho              INTEGER,
        num_estorno              INTEGER,
        data_estorno             DATE,
        valor_estorno            NUMERIC,
        motivo_estorno           VARCHAR
    );

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
              SELECT p.cod_entidade as cod_entidade
                   , p.cod_nota as cod_nota
                   , p.exercicio_liquidacao as exercicio_liquidacao
                   , p.timestamp as timestamp
                   , pa.cod_plano as cod_plano
                   , pc.nom_conta as nom_conta
                FROM contabilidade.pagamento AS p
                   , contabilidade.lancamento_empenho AS le
                   , contabilidade.conta_debito AS cd
                   , contabilidade.plano_analitica AS pa
                   , contabilidade.plano_conta AS pc
                 --Ligação PAGAMENTO : LANCAMENTO EMPENHO
               WHERE p.cod_entidade IN ('||stCodEntidade||')
                 AND p.cod_lote     = le.cod_lote
                 AND p.tipo         = le.tipo
                 AND p.sequencia    = le.sequencia
                 AND p.exercicio    = le.exercicio
                 AND p.cod_entidade = le.cod_entidade
                 AND le.estorno     = true
 
                 --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
                 AND le.cod_lote     = cd.cod_lote
                 AND le.tipo         = cd.tipo
                 AND le.exercicio    = cd.exercicio
                 AND le.cod_entidade = cd.cod_entidade
 
                 --Ligação CONTA_CREDITO : PLANO ANALITICA
                 AND cd.cod_plano = pa.cod_plano
                 AND cd.exercicio = pa.exercicio
                 AND cd.sequencia = 3
 
                --Ligação PLANO ANALITICA : PLANO CONTA
                 AND pa.cod_conta = pc.cod_conta
                 AND pa.exercicio = pc.exercicio
             )';

    EXECUTE stSql;

    stExercicioAtual := TO_CHAR(TO_DATE(stDataInicial, 'dd/mm/yyyy'), 'yyyy');
    
    stSqlExercicio := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio <> '''||stExercicioAtual||''' ';

    FOR reReg IN EXECUTE stSqlExercicio
    LOOP
        
        stSql := '
             INSERT INTO tmp_empenhos
             SELECT exercicio_empenho
                  , cod_unidade_orcamentaria
                  , num_empenho
                  , num_estorno
                  , data_estorno
                  , SUM(valor_estorno) AS valor_estorno
                  , motivo_estorno
               FROM (
                     SELECT e.exercicio AS exercicio_empenho
                          , LPAD(ped_d_cd.num_orgao::VARCHAR,2,''0'') || LPAD(ped_d_cd.num_unidade::VARCHAR,2,''0'') AS cod_unidade_orcamentaria 
                          , e.cod_empenho AS num_empenho
                          , 1 AS num_estorno
                          , TO_DATE(TO_CHAR(nlpa.timestamp_anulada, ''yyyy-mm-dd''), ''yyyy-mm-dd'') AS data_estorno
                          , nlpa.vl_anulado AS valor_estorno
                          , nlpa.observacao AS motivo_estorno
                       FROM empenho.empenho as e 
                          , empenho.nota_liquidacao AS nl                     
                          , empenho.nota_liquidacao_paga AS nlp
                          , empenho.nota_liquidacao_paga_anulada AS nlpa
                          , tmp_estornado AS tmp
                          , sw_cgm
                          , empenho.pre_empenho as pe
                  LEFT JOIN empenho.restos_pre_empenho as rpe
                         ON pe.exercicio = rpe.exercicio
                        AND pe.cod_pre_empenho = rpe.cod_pre_empenho
                  LEFT JOIN (SELECT ped.exercicio
                                  , ped.cod_pre_empenho
                                  , d.num_pao
                                  , d.num_orgao
                                  , d.num_unidade
                                  , d.cod_recurso
                                  , r.masc_recurso_red
                                  , r.cod_detalhamento
                                  , cd.cod_estrutural
                                  , d.cod_funcao
                                  , d.cod_subfuncao
                               FROM empenho.pre_empenho_despesa as ped
                                  , orcamento.despesa as d
                               JOIN orcamento.recurso('''||reReg.exercicio||''') as r
                                 ON d.cod_recurso = r.cod_recurso AND d.exercicio = r.exercicio
                                  , orcamento.conta_despesa as cd
                              WHERE ped.cod_despesa = d.cod_despesa
                                AND ped.exercicio = d.exercicio
                                AND ped.cod_conta = cd.cod_conta
                                AND d.exercicio   = cd.exercicio
                            ) as ped_d_cd 
                         ON pe.exercicio = ped_d_cd.exercicio 
                        AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
                      WHERE e.exercicio = '''||reReg.exercicio||'''
                        AND e.exercicio         = pe.exercicio
                        AND e.cod_pre_empenho   = pe.cod_pre_empenho
                        AND e.cod_entidade      IN (2) 
                        AND pe.cgm_beneficiario = sw_cgm.numcgm 
                        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.cod_empenho = nl.cod_empenho
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
            
                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                        AND nl.exercicio = nlp.exercicio
                        AND nl.cod_nota = nlp.cod_nota
                        AND nl.cod_entidade = nlp.cod_entidade
            
                        --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
                        AND nlp.exercicio = nlpa.exercicio
                        AND nlp.cod_nota = nlpa.cod_nota
                        AND nlp.cod_entidade = nlpa.cod_entidade
                        AND nlp.timestamp = nlpa.timestamp
                        AND to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND to_date('''||stDataFinal||''',''dd/mm/yyyy'')
            
                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                        AND nlp.exercicio = tmp.exercicio_liquidacao
                        AND nlp.cod_entidade = tmp.cod_entidade
                        AND nlp.cod_nota = tmp.cod_nota
                        AND nlp.timestamp = tmp.timestamp
                                    
                   ORDER BY to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'')
                          , e.cod_entidade
                          , e.cod_empenho
                          , e.exercicio
                          , nlpa.cod_nota
                          , tmp.cod_plano
                          , tmp.nom_conta
                    ) as tbl
              where valor_estorno <> ''0.00''
           GROUP BY exercicio_empenho
                  , cod_unidade_orcamentaria
                  , num_empenho
                  , num_estorno
                  , data_estorno
                  , motivo_estorno
           ORDER BY exercicio_empenho
                  , cod_unidade_orcamentaria
                  , num_empenho
                  , num_estorno
                  , data_estorno
                  , motivo_estorno        
        ';
        
        EXECUTE stSql;
    END LOOP;
    
    stSql :=  ' SELECT exercicio_empenho
                     , cod_unidade_orcamentaria
                     , num_empenho
                     , num_estorno
                     , data_estorno
                     , valor_estorno
                     , motivo_estorno
                  FROM tmp_empenhos
            ';
            
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_estornado;
    DROP TABLE tmp_empenhos;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
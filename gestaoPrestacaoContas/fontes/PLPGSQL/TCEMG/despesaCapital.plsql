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
    * Arquivo de mapeamento para a função que busca os dados da despesa capital
    * Data de Criação   : 27/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    
    * @package URBEM
    * @subpackage 

    $Id: despesaCapital.plsql 63307 2015-08-14 18:35:11Z franver $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_despesa_capital(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;

    inPeriodo           INTEGER := 0;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN

    inPeriodo := EXTRACT( month FROM TO_DATE(''|| stDtInicial ||'','dd/mm/yyyy') ) AS mes;

    CREATE TEMPORARY TABLE tmp_retorno(
        mes                 INTEGER,
        desp_invest         NUMERIC(14,2),
        desp_inv_finan      NUMERIC(14,2),
        desp_amort_div_int  NUMERIC(14,2),
        desp_amort_div_ext  NUMERIC(14,2),
        desp_amort_div_mob  NUMERIC(14,2),
        desp_out_desp_cap   NUMERIC(14,2),
        conc_emprestimos    NUMERIC(14,2),
        aquisicao_titulos   NUMERIC(14,2),
        incent_contrib      NUMERIC(14,2),
        incent_inst_finan   NUMERIC(14,2),
        cod_tipo            INTEGER
    ); 

    --cria os valores empenhados
    stSql := '
          CREATE TEMPORARY TABLE tmp_empenhado AS 
          SELECT e.dt_empenho                AS dataConsulta
               , d.cod_despesa
               , d.exercicio
               , coalesce(ipe.vl_total,0.00) AS valor
               , cd.cod_estrutural           AS cod_estrutural
               , d.num_orgao                 AS num_orgao
               , d.num_unidade               AS num_unidade
            FROM orcamento.despesa           AS d
               , orcamento.conta_despesa     AS cd
               , empenho.pre_empenho_despesa AS ped
               , empenho.empenho             AS e
               , empenho.pre_empenho         AS pe
               , empenho.item_pre_empenho    AS ipe
           WHERE cd.cod_conta               = ped.cod_conta
             AND cd.exercicio               = ped.exercicio
             AND d.cod_despesa              = ped.cod_despesa
             AND d.exercicio                = ped.exercicio
             AND pe.exercicio               = ped.exercicio
             AND pe.cod_pre_empenho         = ped.cod_pre_empenho
             AND e.exercicio                = pe.exercicio
             AND e.cod_pre_empenho          = pe.cod_pre_empenho
             AND pe.exercicio               = ipe.exercicio
             AND pe.cod_pre_empenho         = ipe.cod_pre_empenho
             AND e.cod_entidade             IN ('||stCodEntidade||')
             AND e.exercicio                = '''||stExercicio||'''
             AND cd.cod_estrutural LIKE ''4.%''
             AND e.dt_empenho BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                  AND TO_DATE(''' || stDtFinal ||''',''dd/mm/yyyy'')
    ';
    EXECUTE stSql;

    --cria os valores anulados
    stSql := '
          CREATE TEMPORARY TABLE tmp_anulado AS 
          SELECT to_date(to_char(EEAI.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
               , OD.cod_despesa
               , OD.exercicio
               , EEAI.vl_anulado as valor
               , OCD.cod_estrutural as cod_estrutural
               , OD.num_orgao
               , OD.num_unidade
            from orcamento.despesa           as OD
               , orcamento.conta_despesa     as OCD
               , empenho.pre_empenho_despesa as EPED
               , empenho.pre_empenho         as EPE
               , empenho.item_pre_empenho    as EIPE
               , empenho.empenho_anulado_item as EEAI
           Where OCD.cod_conta            = EPED.cod_conta
             AND OCD.exercicio            = EPED.exercicio
             And EPED.exercicio           = EPE.exercicio
             And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
             And EPE.exercicio            = EIPE.exercicio
             And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
             And EIPE.exercicio           = EEAI.exercicio
             And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
             And EIPE.num_item            = EEAI.num_item
             And OD.cod_despesa           = EPED.cod_despesa
             AND OD.exercicio             = EPED.exercicio
             And EEAI.exercicio           = ''' || stExercicio || '''
             And EEAI.cod_entidade        IN (' || stCodEntidade || ')
             AND OCD.cod_estrutural LIKE ''4.%''
             AND EEAI.timestamp BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                    AND TO_DATE(''' || stDtFinal || ''',''dd/mm/yyyy'')
    ';
    EXECUTE stSql;

    --cria os valores pagos
    stSql := '
          CREATE TEMPORARY TABLE tmp_pago AS 
          SELECT to_date(to_char(ENLP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
               , OD.cod_despesa
               , OD.exercicio
               , ENLP.vl_pago as valor
               , OCD.cod_estrutural as cod_estrutural
               , OD.num_orgao as num_orgao
               , OD.num_unidade as num_unidade
            FROM orcamento.despesa               as OD
               , orcamento.conta_despesa         as OCD
               , empenho.pre_empenho_despesa     as EPED
               , empenho.empenho                 as EE
               , empenho.pre_empenho             as EPE
               , empenho.nota_liquidacao         as ENL
               , empenho.nota_liquidacao_paga    as ENLP
           WHERE OCD.cod_conta            = EPED.cod_conta
             AND OCD.exercicio            = EPED.exercicio
             AND OD.cod_despesa           = EPED.cod_despesa
             AND OD.exercicio             = EPED.exercicio
             And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
             And EPED.exercicio           = EPE.exercicio
             And EPE.exercicio            = EE.exercicio
             And EPE.cod_pre_empenho      = EE.cod_pre_empenho
             And EE.cod_empenho           = ENL.cod_empenho
             And EE.exercicio             = ENL.exercicio_empenho
             And EE.cod_entidade          = ENL.cod_entidade
             And ENL.cod_nota             = ENLP.cod_nota
             And ENL.cod_entidade         = ENLP.cod_entidade
             And ENL.exercicio            = ENLP.exercicio
             And EE.exercicio             = '''||stExercicio||'''
             And EE.cod_entidade          IN ('||stCodEntidade||')
             AND OCD.cod_estrutural LIKE ''4.%''
             AND ENLP.timestamp BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                    AND TO_DATE(''' || stDtFinal || ''',''dd/mm/yyyy'')
    ';
    EXECUTE stSql; 

    --cria os valores estornados
    stSql := '
          CREATE TEMPORARY TABLE tmp_estornado AS 
          SELECT to_date(to_char(ENLPA.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
               , OD.cod_despesa
               , OD.exercicio
               , ENLPA.vl_anulado as valor
               , OCD.cod_estrutural as cod_estrutural
               , OD.num_orgao as num_orgao
               , OD.num_unidade as num_unidade
            FROM orcamento.despesa                    as OD
               , orcamento.conta_despesa              as OCD
               , empenho.pre_empenho_despesa          as EPED
               , empenho.empenho                      as EE
               , empenho.pre_empenho                  as EPE
               , empenho.nota_liquidacao              as ENL
               , empenho.nota_liquidacao_paga         as ENLP
               , empenho.nota_liquidacao_paga_anulada as ENLPA
           WHERE OCD.cod_conta            = EPED.cod_conta
             AND OCD.exercicio            = EPED.exercicio
             And OD.cod_despesa           = EPED.cod_despesa
             AND OD.exercicio             = EPED.exercicio
             And EPED.exercicio           = EPE.exercicio
             And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
             And EPE.exercicio            = EE.exercicio
             And EPE.cod_pre_empenho      = EE.cod_pre_empenho
             And EE.cod_empenho           = ENL.cod_empenho
             And EE.exercicio             = ENL.exercicio_empenho
             And EE.cod_entidade          = ENL.cod_entidade
             And ENL.exercicio            = ENLP.exercicio
             And ENL.cod_nota             = ENLP.cod_nota
             And ENL.cod_entidade         = ENLP.cod_entidade
             And ENLP.cod_entidade        = ENLPA.cod_entidade
             And ENLP.cod_nota            = ENLPA.cod_nota
             And ENLP.exercicio           = ENLPA.exercicio
             And ENLP.timestamp           = ENLPA.timestamp
             And EE.cod_entidade          IN ('||stCodEntidade||')
             And EE.exercicio             = '''||stExercicio||'''
             AND OCD.cod_estrutural LIKE ''4.%''
             AND ENLPA.timestamp BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                     AND TO_DATE('''||stDtFinal||''',''dd/mm/yyyy'')
    ';
    EXECUTE stSql;

    --cria os valores liquidados
    stSql = '
          CREATE TEMPORARY TABLE tmp_liquidado AS 
          SELECT nl.dt_liquidacao as dataConsulta
               , d.cod_despesa
               , d.exercicio
               , nli.vl_total as valor
               , cd.cod_estrutural as cod_estrutural
               , d.num_orgao as num_orgao
               , d.num_unidade as num_unidade
            FROM orcamento.despesa             as d
               , orcamento.conta_despesa       as cd
               , empenho.pre_empenho_despesa   as ped
               , empenho.pre_empenho           as pe
               , empenho.empenho               as e
               , empenho.nota_liquidacao_item  as nli
               , empenho.nota_liquidacao       as nl
           WHERE cd.cod_conta       = ped.cod_conta
             AND cd.exercicio       = ped.exercicio
             And d.cod_despesa      = ped.cod_despesa
             AND d.exercicio        = ped.exercicio
             And pe.exercicio       = ped.exercicio
             And pe.cod_pre_empenho = ped.cod_pre_empenho
             AND e.exercicio        = pe.exercicio
             AND e.cod_pre_empenho  = pe.cod_pre_empenho
             AND e.exercicio        = nl.exercicio_empenho
             AND e.cod_entidade     = nl.cod_entidade
             AND e.cod_empenho      = nl.cod_empenho
             AND nl.exercicio       = nli.exercicio
             AND nl.cod_nota        = nli.cod_nota
             AND nl.cod_entidade    = nli.cod_entidade
             And e.cod_entidade     IN (' || stCodEntidade || ')
             And e.exercicio        = ''' || stExercicio || '''
             AND cd.cod_estrutural LIKE ''4.%''
             AND nl.dt_liquidacao BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                      AND TO_DATE('''||stDtFinal||''',''dd/mm/yyyy'')
    ';
    EXECUTE stSql;

    --cria o valor liquidado estornado
    stSql := '
          CREATE TEMPORARY TABLE tmp_liquidado_estornado AS
          SELECT to_date(to_char(ENLIA.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') as dataConsulta
               , OD.cod_despesa
               , OD.exercicio
               , ENLIA.vl_anulado as valor
               , OCD.cod_estrutural as cod_estrutural
               , OD.num_orgao
               , OD.num_unidade
            from orcamento.despesa                    as OD
               , orcamento.conta_despesa              as OCD
               , empenho.pre_empenho_despesa          as EPED
               , empenho.pre_empenho                  as EPE
               , empenho.empenho                      as EE
               , empenho.nota_liquidacao              as ENL
               , empenho.nota_liquidacao_item         as ENLI
               , empenho.nota_liquidacao_item_anulado as ENLIA
           Where OCD.cod_conta        = EPED.cod_conta
             AND OCD.exercicio        = EPED.exercicio
             And EPE.cod_pre_empenho  = EE.cod_pre_empenho
             And EPE.exercicio        = EE.exercicio
             And EE.exercicio         = ENL.exercicio_empenho
             And EE.cod_entidade      = ENL.cod_entidade
             And EE.cod_empenho       = ENL.cod_empenho
             And ENL.exercicio        = ENLI.exercicio
             And ENL.cod_nota         = ENLI.cod_nota
             And ENL.cod_entidade     = ENLI.cod_entidade
             And ENLI.exercicio       = ENLIA.exercicio
             And ENLI.cod_pre_empenho = ENLIA.cod_pre_empenho
             And ENLI.num_item        = ENLIA.num_item
             And ENLI.cod_entidade    = ENLIA.cod_entidade
             And ENLI.exercicio_item  = ENLIA.exercicio_item
             And ENLI.cod_nota        = ENLIA.cod_nota
             And OD.cod_despesa       = EPED.cod_despesa
             AND OD.exercicio         = EPED.exercicio
             And EPED.exercicio       = EPE.exercicio
             And EPED.cod_pre_empenho = EPE.cod_pre_empenho
             And EE.cod_entidade      IN (' || stCodEntidade || ')
             And EE.exercicio         = ''' || stExercicio || '''
             And OD.cod_entidade      IN (' || stCodEntidade || ')
             AND OCD.cod_estrutural LIKE ''4.%''
             AND ENLIA.timestamp BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'')
                                     AND TO_DATE(''' || stDtFinal || ''',''dd/mm/yyyy'')
    ';

    EXECUTE stSql;

    --retorna os dados da dotacao para as despesas
    stSql := '
          CREATE TEMPORARY TABLE tmp_dotacao AS
          SELECT conta_despesa.exercicio
               , conta_despesa.cod_conta
               , conta_despesa.cod_estrutural
               , SUM(COALESCE(despesa.vl_original,0)) AS vl_original
               , SUM(COALESCE(despesa.vl_original,0) + COALESCE(suplementacao.valor,0) - COALESCE(reducao.valor,0)) AS vl_atualizado
            FROM orcamento.despesa
      INNER JOIN orcamento.conta_despesa
              ON despesa.exercicio = conta_despesa.exercicio
             AND despesa.cod_conta = conta_despesa.cod_conta
       LEFT JOIN (SELECT suplementacao_suplementada.exercicio
                       , suplementacao_suplementada.cod_despesa
                       , SUM(valor) AS valor
                    FROM orcamento.suplementacao_suplementada
              INNER JOIN orcamento.suplementacao
                      ON suplementacao_suplementada.exercicio         = suplementacao.exercicio
                     AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                     AND suplementacao.cod_suplementacao||suplementacao.exercicio IN (SELECT cod_suplementacao||cl.exercicio
                                                                                        FROM contabilidade.transferencia_despesa ctd
                                                                                           , contabilidade.lote cl
                                                                                       WHERE ctd.exercicio = cl.exercicio
                                                                                         AND ctd.cod_lote  = cl.cod_lote
                                                                                         AND ctd.tipo      = cl.tipo
                                                                                         AND ctd.cod_entidade = cl.cod_entidade
                                                                                         AND cl.dt_lote BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                                                                                            AND TO_DATE('''||stDtFinal||''',''dd/mm/yyyy'')
                                                                                     )
                   WHERE suplementacao.dt_suplementacao BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                                            AND TO_DATE('''||stDtFinal||''',''dd/mm/yyyy'')
                     AND NOT EXISTS ( SELECT 1
                                        FROM orcamento.suplementacao_anulada
                                       WHERE suplementacao_anulada.exercicio = suplementacao.exercicio
                                         AND (suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                             OR
                                              suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                             )
                                    )
                GROUP BY suplementacao_suplementada.exercicio
                       , suplementacao_suplementada.cod_despesa
                 ) AS suplementacao
              ON suplementacao.exercicio   = despesa.exercicio
             AND suplementacao.cod_despesa = despesa.cod_despesa
       LEFT JOIN (SELECT suplementacao_reducao.exercicio
                       , suplementacao_reducao.cod_despesa
                       , SUM(valor) AS valor
                    FROM orcamento.suplementacao_reducao
              INNER JOIN orcamento.suplementacao
                      ON suplementacao_reducao.exercicio         = suplementacao.exercicio
                     AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                     AND suplementacao.cod_suplementacao||suplementacao.exercicio IN (SELECT cod_suplementacao||cl.exercicio
                                                                                        FROM contabilidade.transferencia_despesa ctd
                                                                                           , contabilidade.lote cl
                                                                                       WHERE ctd.exercicio = cl.exercicio
                                                                                         AND ctd.cod_lote  = cl.cod_lote
                                                                                         AND ctd.tipo      = cl.tipo
                                                                                         AND ctd.cod_entidade = cl.cod_entidade
                                                                                         AND cl.dt_lote BETWEEN TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')
                                                                                                            AND TO_DATE('''||stDtFinal||''',''dd/mm/yyyy'')
                                                                                     )
                   WHERE suplementacao.dt_suplementacao BETWEEN TO_DATE(''' || stDtInicial || ''',''dd/mm/yyyy'') AND TO_DATE(''' || stDtFinal || ''',''dd/mm/yyyy'')
                     AND NOT EXISTS (SELECT 1
                                       FROM orcamento.suplementacao_anulada
                                      WHERE suplementacao_anulada.exercicio = suplementacao.exercicio
                                        AND (suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                             OR
                                             suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                            )
                                    )
                GROUP BY suplementacao_reducao.exercicio
                       , suplementacao_reducao.cod_despesa
                 ) AS reducao
              ON reducao.exercicio   = despesa.exercicio
             AND reducao.cod_despesa = despesa.cod_despesa
           WHERE conta_despesa.cod_estrutural LIKE ''4%''
             AND despesa.exercicio = ''' || stExercicio || '''
             AND despesa.cod_entidade IN (' || stCodEntidade || ')
        GROUP BY conta_despesa.exercicio
               , conta_despesa.cod_conta
               , conta_despesa.cod_estrutural;';

    EXECUTE stSql;

    IF inPeriodo = 1 THEN
    --insere os dados na tabela de retorno o tipo 1 - dotacao inicial
    INSERT
      INTO tmp_retorno
    VALUES ( inPeriodo
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.4.6%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4%'
                          AND cod_estrutural NOT LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.6%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural NOT LIKE '4.5.9.0.66.01.01%' 
                          AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'
                          AND cod_estrutural LIKE '4.5.9.0.66%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                           OR cod_estrutural LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(vl_original)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                           OR cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00)
           , 0 
           , 1 );
    END IF;
     --insere os dados na tabela de retorno o tipo 2 - dotacao atualizada
    INSERT
      INTO tmp_retorno
    VALUES ( inPeriodo
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.4.6%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4%'
                          AND cod_estrutural NOT LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.6%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural NOT LIKE '4.5.9.0.66.01.01%' 
                          AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'
                          AND cod_estrutural LIKE '4.5.9.0.66%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                           OR cod_estrutural LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                           OR cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00)
           , 0 
           , 2 );        

    --insere os dados na tabela de retorno o tipo 4 - empenho
    INSERT
      INTO tmp_retorno
    VALUES ( inPeriodo
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado 
                        WHERE cod_estrutural LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.4.6%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado
                        WHERE cod_estrutural LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado 
                        WHERE cod_estrutural LIKE '4.6%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado 
                        WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado 
                        WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado 
                        WHERE cod_estrutural LIKE '4%'
                          AND cod_estrutural NOT LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.6%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado 
                        WHERE cod_estrutural NOT LIKE '4.5.9.0.66.01.01%' 
                          AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'
                          AND cod_estrutural LIKE '4.5.9.0.66%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_empenhado 
                        WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                           OR cod_estrutural LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(valor)    
                         FROM tmp_empenhado 
                        WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                           OR cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00)
           , 0 
           , 4 );

    --insere os dados na tabela de retorno o tipo 5 - liquidada
    INSERT
      INTO tmp_retorno
    VALUES ( inPeriodo
           , (COALESCE((SELECT SUM(valor) 
                         FROM tmp_liquidado 
                        WHERE cod_estrutural LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.4.6%'),0.00)
              -
              COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.4.6%'),0.00))
           , (COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado
                         WHERE cod_estrutural LIKE '4.5%'
                           AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                           AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                           AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00)
              -
              COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4.5%'
                           AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                           AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                           AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00))
           , (COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado 
                         WHERE cod_estrutural LIKE '4.6%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                           AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00)
              -
              COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4.6%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.74.01%'
                           AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00))
           , (COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado 
                         WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                           AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00)
              -
              COALESCE((SELECT SUM(valor)
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                           AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00))
           , (COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado 
                         WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                           AND cod_estrutural LIKE '4.6.9.0.74.01%' 
                           AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00)
              -
              COALESCE((SELECT SUM(valor)
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                           AND cod_estrutural LIKE '4.6.9.0.74.01%'
                           AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00))
           , (COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado 
                         WHERE cod_estrutural LIKE '4%'
                           AND cod_estrutural NOT LIKE '4.4%'
                           AND cod_estrutural NOT LIKE '4.5%'
                           AND cod_estrutural NOT LIKE '4.6%'),0.00)
               -
               COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4%'
                           AND cod_estrutural NOT LIKE '4.4%'
                           AND cod_estrutural NOT LIKE '4.5%'
                           AND cod_estrutural NOT LIKE '4.6%'),0.00))
           , (COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado 
                         WHERE cod_estrutural NOT LIKE '4.5.9.0.66.01.01%' 
                           AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'
                           AND cod_estrutural LIKE '4.5.9.0.66%'),0.00)
              -
              COALESCE((SELECT SUM(valor)
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural NOT LIKE '4.5.9.0.66.01.01%'
                           AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'
                           AND cod_estrutural LIKE '4.5.9.0.66%'),0.00))
           , (COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado 
                         WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                            OR cod_estrutural LIKE '4.5.9.0.64%'),0.00)
              -     
              COALESCE((SELECT SUM(valor) 
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4.5.9.0.63%'
                            OR cod_estrutural LIKE '4.5.9.0.64%'),0.00))
           , (COALESCE((SELECT SUM(valor)    
                          FROM tmp_liquidado 
                         WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                            OR cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00)
              -
              COALESCE((SELECT SUM(valor)
                          FROM tmp_liquidado_estornado
                         WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                            OR cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00))
           , 0 
           , 5 );

    --insere os dados na tabela de retorno o tipo 6 - anulado
    INSERT
      INTO tmp_retorno
    VALUES ( inPeriodo
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado 
                        WHERE cod_estrutural LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.4.6%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado
                        WHERE cod_estrutural LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado
                        WHERE cod_estrutural LIKE '4.6%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado 
                        WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado 
                        WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado 
                        WHERE cod_estrutural LIKE '4%'
                          AND cod_estrutural NOT LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.6%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado 
                        WHERE cod_estrutural NOT LIKE '4.5.9.0.66.01.01%' 
                          AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'
                          AND cod_estrutural LIKE '4.5.9.0.66%'),0.00)
           , COALESCE((SELECT SUM(valor) 
                         FROM tmp_anulado 
                        WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                           OR cod_estrutural LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(valor)    
                         FROM tmp_anulado 
                        WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                           OR cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00)
           , 0 
           , 6 );

      --insere os dados na tabela de retorno o tipo 3 - dotacao saldo
    INSERT
      INTO tmp_retorno
    VALUES ( inPeriodo
           , COALESCE((SELECT SUM(vl_atualizado) 
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.4%'
                                            AND cod_estrutural NOT LIKE '4.4.6%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.4%'
                                            AND cod_estrutural NOT LIKE '4.4.6%'),0.00) )
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.4.6%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.5%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.5%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00) )
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.66%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.63%'
                          AND cod_estrutural NOT LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.6%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                                            AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.6%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                                            AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                                            AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00) )
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.77.03%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural NOT LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural NOT LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                                            AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                                            AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00) )
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6.9.0.71.03%'
                          AND cod_estrutural LIKE '4.6.9.0.77.03%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                                            AND cod_estrutural LIKE '4.6.9.0.74.01%'
                                            AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                                            AND cod_estrutural LIKE '4.6.9.0.74.01%'
                                            AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00) )
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.6.9.0.72.01%'
                          AND cod_estrutural LIKE '4.6.9.0.74.01%' 
                          AND cod_estrutural LIKE '4.6.9.0.76.01%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4%'
                                            AND cod_estrutural NOT LIKE '4.4%'
                                            AND cod_estrutural NOT LIKE '4.5%'
                                            AND cod_estrutural NOT LIKE '4.6%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4%'
                                            AND cod_estrutural NOT LIKE '4.4%'
                                            AND cod_estrutural NOT LIKE '4.5%'
                                            AND cod_estrutural NOT LIKE '4.6%'),0.00) )
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4%'
                          AND cod_estrutural NOT LIKE '4.4%'
                          AND cod_estrutural NOT LIKE '4.5%'
                          AND cod_estrutural NOT LIKE '4.6%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.5.9.0.66%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.66.01.01%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.5.9.0.66%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.66.01.01%'
                                            AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'),0.00) )
                         FROM tmp_dotacao 
                        WHERE cod_estrutural NOT LIKE '4.5.9.0.66.01.01%' 
                          AND cod_estrutural NOT LIKE '4.5.9.0.66.02.01%'
                          AND cod_estrutural LIKE '4.5.9.0.66%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                                            AND cod_estrutural LIKE '4.5.9.0.64%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                                            AND cod_estrutural LIKE '4.5.9.0.64%'),0.00) )
                         FROM tmp_dotacao
                        WHERE cod_estrutural LIKE '4.5.9.0.63%' 
                           OR cod_estrutural LIKE '4.5.9.0.64%'),0.00)
           , COALESCE((SELECT SUM(vl_atualizado)
                              + ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_anulado 
                                          WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                                            AND cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00) )
                              - ( COALESCE((SELECT SUM(valor) 
                                           FROM tmp_empenhado 
                                          WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                                            AND cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00) )
                         FROM tmp_dotacao 
                        WHERE cod_estrutural LIKE '4.5.9.0.66.01.01%'
                           OR cod_estrutural LIKE '4.5.9.0.66.02.01%'),0.00)
           , 0 
           , 3 );          


    stSql := 'SELECT * FROM tmp_retorno';                                                 

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_retorno;
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;
    DROP TABLE tmp_liquidado_estornado;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_dotacao;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  

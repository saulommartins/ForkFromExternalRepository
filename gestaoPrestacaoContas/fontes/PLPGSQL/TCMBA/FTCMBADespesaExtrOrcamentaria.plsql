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
    * Arquivo de função tcmba.despesaExtraOrcamentaria
    * 
    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Henrique Boaventura
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    * $Id: FTCMBADespesaExtrOrcamentaria.plsql 63354 2015-08-20 17:42:15Z franver $
    * $Rev: 63354 $
    * $Author: franver $
    * $Date: 2015-08-20 14:42:15 -0300 (Thu, 20 Aug 2015) $
    * 
*/
CREATE OR REPLACE FUNCTION tcmba.despesaExtraOrcamentaria (stExercicio VARCHAR, stEntidades VARCHAR, stDtInicial VARCHAR, stDtFinal VARCHAR, stDtInicialMes VARCHAR) RETURNS SETOF tcmba.tp_desp_ext AS $$
DECLARE 
    stSQL   VARCHAR := '';
    reReg   RECORD;
    
BEGIN


    stSql:= '
          CREATE TEMPORARY TABLE tmp_despesa_ext AS (
          SELECT * FROM (SELECT plano_debito.exercicio
                              , dt_pagamento
                              , cod_lote
                              , cod_plano_credito
                              , sum(coalesce(vl_pago,0.00)) as valor
                              , tipo_despesa
                              , cod_entidade
                              , nom_entidade
                              , plano_debito.nom_conta
                              , coalesce(cpr.cod_recurso, 9999999999) as cod_recurso
                              , coalesce(orr.nom_recurso, '''') as nom_recurso
                              , cod_plano_debito
                              , plano_debito.cod_plano
                              , plano_debito.nome_despesa
                           FROM (
                                 ---------------------------------------------
                                 --                   PAGAMENTOS EXTRA 
                                 ---------------------------------------------
                                 SELECT TT.exercicio
                                      , to_char(to_date(TT.timestamp_transferencia::VARCHAR, ''YYYY-MM-DD''), ''DD/MM/YYYY'') as dt_pagamento
                                      , TT.cod_lote
                                      , CPCD.cod_plano
                                      , TT.cod_plano_credito
                                      , SUM(coalesce(TT.valor,0.00)) as vl_pago
                                      , cast(''EXT'' as varchar) as tipo_despesa
                                      , CPC.nom_conta
                                      , OE.cod_entidade
                                      , OE.nom_cgm as nom_entidade
                                      , TT.timestamp_transferencia
                                      , CPCD.nome_despesa
                                      , TT.cod_plano_debito
                                   FROM tesouraria.transferencia as TT
                                   -- BUSCA CONTA BANCO        
                             INNER JOIN (SELECT CPA.cod_plano||'' - ''||CPC.nom_conta as nom_conta                
                                              , CPA.cod_plano
                                              , CPA.exercicio 
                                           FROM contabilidade.plano_conta as CPC
                                              , contabilidade.plano_analitica as CPA
                                          WHERE CPC.cod_conta = CPA.cod_conta
                                            AND CPC.exercicio = CPA.exercicio 
                                        ) as CPC
                                     ON TT.cod_plano_credito= CPC.cod_plano
                                    AND TT.exercicio        = CPC.exercicio
                                   -- BUSCA CONTA DESPESA        
                             INNER JOIN (SELECT CPC.cod_estrutural||'' - ''||CPC.nom_conta as nome_despesa                
                                              , CPA.cod_plano
                                              , CPA.exercicio 
                                           FROM contabilidade.plano_conta as CPC
                                              , contabilidade.plano_analitica as CPA
                                          WHERE CPC.cod_conta = CPA.cod_conta
                                            AND CPC.exercicio = CPA.exercicio 
                                        ) as CPCD
                                     ON TT.cod_plano_debito = CPCD.cod_plano
                                    AND TT.exercicio        = CPCD.exercicio 
                                   --BUSCA ENTIDADE
                             INNER JOIN (SELECT OE.cod_entidade||'' - ''||CGM.nom_cgm as entidade
                                              , CGM.nom_cgm
                                              , OE.cod_entidade
                                              , OE.exercicio     
                                           FROM orcamento.entidade as OE
                                              , sw_cgm as CGM 
                                          WHERE OE.numcgm = CGM.numcgm
                                        ) as OE
                                     ON OE.cod_entidade = TT.cod_entidade
                                    AND OE.exercicio    = TT.exercicio
                                  WHERE TT.cod_tipo = 1
                                    AND TO_DATE(TO_CHAR(TT.timestamp_transferencia,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN TO_DATE('''|| stDtInicial ||'''::VARCHAR,''dd/mm/yyyy'')
                                                                                                                       AND TO_DATE('''|| stDtFinal ||'''::VARCHAR,''dd/mm/yyyy'')
                                    AND TO_CHAR(TT.timestamp_transferencia,''yyyy'') = '''|| stExercicio ||'''
                               GROUP BY TT.exercicio    
                                      , TT.timestamp_transferencia
                                      , TT.cod_lote
                                      , tt.cod_plano_credito
                                      , cpc.nom_conta
                                      , oe.cod_entidade
                                      , oe.nom_cgm
                                      , cpcd.nome_despesa
                                      , tt.cod_plano_debito
                                      , CPCD.cod_plano
                                      , OE.entidade
                              UNION ALL
                                 ---------------------------------------------
                                 --       ESTORNOS DE PAGAMENTOS EXTRA 
                                 ---------------------------------------------
                                 SELECT TTE.exercicio
                                      , to_char(to_date(TTE.timestamp_estornada::VARCHAR, ''YYYY-MM-DD''), ''DD/MM/YYYY'') as dt_pagamento
                                      , TTE.cod_lote
                                      , CPCD.cod_plano
                                      , TT.cod_plano_credito
                                      , SUM(coalesce(TTE.valor,0.00)) * (-1) as vl_pago
                                      , cast(''EEX'' as varchar) as tipo_despesa
                                      , CPC.nom_conta
                                      , OE.cod_entidade
                                      , OE.nom_cgm as nom_entidade
                                      , TTE.timestamp_estornada
                                      , cpcd.nome_despesa
                                      , TT.cod_plano_debito
                                   FROM tesouraria.transferencia as TT
                             INNER JOIN tesouraria.transferencia_estornada as TTE
                                     on TTE.cod_entidade    = TT.cod_entidade
                                    AND TTE.tipo            = TT.tipo
                                    AND TTE.exercicio       = TT.exercicio
                                    AND TTE.cod_lote        = TT.cod_lote
                                   -- BUSCA CONTA BANCO        
                             INNER JOIN (SELECT CPA.cod_plano||'' - ''||CPC.nom_conta as nom_conta                
                                              , CPA.cod_plano
                                              , CPA.exercicio 
                                           FROM contabilidade.plano_conta as CPC
                                              , contabilidade.plano_analitica as CPA
                                          WHERE CPC.cod_conta = CPA.cod_conta
                                            AND CPC.exercicio = CPA.exercicio 
                                        ) as CPC
                                     ON TT.cod_plano_credito= CPC.cod_plano
                                    AND TT.exercicio        = CPC.exercicio 
                                   -- BUSCA CONTA DESPESA        
                             INNER JOIN (SELECT CPC.cod_estrutural||'' - ''||CPC.nom_conta as nome_despesa                
                                              , CPA.cod_plano
                                              , CPA.exercicio 
                                           FROM contabilidade.plano_conta as CPC
                                              , contabilidade.plano_analitica as CPA
                                          WHERE CPC.cod_conta = CPA.cod_conta
                                            AND CPC.exercicio = CPA.exercicio 
                                        ) as CPCD
                                     on TT.cod_plano_debito = CPCD.cod_plano
                                    AND TT.exercicio        = CPCD.exercicio
                                   --BUSCA ENTIDADE
                             INNER JOIN (SELECT OE.cod_entidade||'' - ''||CGM.nom_cgm as entidade
                                              , CGM.nom_cgm
                                              , OE.cod_entidade
                                              , OE.exercicio     
                                           FROM orcamento.entidade as OE
                                              , sw_cgm as CGM 
                                          WHERE OE.numcgm = CGM.numcgm
                                        ) as OE
                                     on OE.cod_entidade = TT.cod_entidade
                                    AND OE.exercicio    = TT.exercicio
                                  WHERE TT.cod_tipo = 1
                                    AND TO_DATE(TO_CHAR(TT.timestamp_transferencia,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''|| stDtInicial ||'''::VARCHAR,''dd/mm/yyyy'')
                                                                                                                       AND to_date('''|| stDtFinal ||'''::VARCHAR,''dd/mm/yyyy'')
                                    AND TO_CHAR(TT.timestamp_transferencia,''yyyy'') = '''|| stExercicio ||'''
                               GROUP BY tte.exercicio
                                      , tte.timestamp_estornada
                                      , tte.cod_lote 
                                      , tt.cod_plano_credito
                                      , cpc.nom_conta
                                      , oe.cod_entidade
                                      , oe.nom_cgm
                                      , cpcd.nome_despesa
                                      , tt.cod_plano_debito
                                      , CPCD.cod_plano
                                      , OE.entidade
                                    
                              UNION ALL
                                 ---------------------------------------------
                                 --                  PAGAMENTOS RESTOS 
                                 ---------------------------------------------
                                 SELECT plano.exercicio        
                                      , plano.dt_pagamento
                                      , plano.cod_lote
                                      , cpa.cod_plano 
                                      , plano.cod_plano_credito
                                      , plano.vl_pago
                                      , plano.tipo_despesa
                                      , plano.nom_conta
                                      , plano.cod_entidade
                                      , plano.nom_entidade
                                      , plano.timestamp
                                      , CPC.cod_estrutural||'' - ''||CPC.nom_conta as nome_despesa                
                                      , plano.cod_plano_debito
                                   FROM (SELECT tp.exercicio_plano as exercicio
                                              , to_char(to_date(tp.timestamp::VARCHAR, ''YYYY-MM-DD''), ''DD/MM/YYYY'') as dt_pagamento
                                              , cp.cod_lote
                                              , plano_banco.cod_plano
                                              , tp.cod_plano as cod_plano_credito
                                              , nlp.vl_pago AS vl_pago
                                              , cast(''RES'' as varchar) as tipo_despesa
                                              , plano_banco.nom_conta as nom_conta
                                              , oe.cod_entidade
                                              , cgm.nom_cgm as nom_entidade
                                              , tp.timestamp
                                              , '''' as nome_despesa
                                              , contabilidade.fn_recupera_conta_lancamento( CP.exercicio
                                                                                          , CP.cod_entidade
                                                                                          , CP.cod_lote
                                                                                          , CP.tipo
                                                                                          , CP.sequencia
                                                                                          , ''D'') as cod_plano_debito
                                           FROM (SELECT CPA.cod_plano||'' - ''||CPC.nom_conta as nom_conta                
                                                      , cpa.cod_plano as cod_plano
                                                      , cpa.exercicio
                                                   FROM contabilidade.plano_conta as cpc
                                                      , contabilidade.plano_analitica as cpa
                                                  WHERE cpa.cod_conta = cpc.cod_conta
                                                    AND cpc.exercicio = cpa.exercicio
                                                ) as plano_banco
                                              , tesouraria.pagamento AS TP
                                     INNER JOIN orcamento.entidade as oe
                                             ON oe.cod_entidade  = tp.cod_entidade
                                            AND oe.exercicio = tp.exercicio
                                     INNER JOIN sw_cgm as cgm
                                             ON oe.numcgm = cgm.numcgm
                                     INNER JOIN empenho.nota_liquidacao_paga as nlp
                                             ON nlp.cod_nota     = tp.cod_nota
                                            AND nlp.cod_entidade = tp.cod_entidade
                                            AND nlp.exercicio    = tp.exercicio
                                            AND nlp.timestamp    = tp.timestamp
                                     INNER JOIN empenho.nota_liquidacao as nl
                                             ON nl.cod_nota     = nlp.cod_nota
                                            AND nl.exercicio    = nlp.exercicio
                                            AND nl.cod_entidade = nlp.cod_entidade
                                            AND nl.exercicio_empenho < '''|| stExercicio ||'''
                                     INNER JOIN contabilidade.pagamento as cp
                                             ON cp.cod_entidade         = nlp.cod_entidade
                                            AND cp.exercicio_liquidacao = nlp.exercicio
                                            AND cp.cod_nota             = nlp.cod_nota
                                            AND cp.timestamp            = nlp.timestamp
                                     INNER JOIN contabilidade.lancamento_empenho as cle
                                             ON cle.cod_lote     = cp.cod_lote
                                            AND cle.cod_entidade = cp.cod_entidade
                                            AND cle.sequencia    = cp.sequencia
                                            AND cle.exercicio    = cp.exercicio
                                            AND cle.tipo         = cp.tipo
                                          WHERE tp.cod_plano = plano_banco.cod_plano
                                            and tp.exercicio_plano = plano_banco.exercicio
                                            AND TO_DATE(TO_CHAR(tp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''|| stDtInicial ||'''::VARCHAR,''dd/mm/yyyy'')
                                                                                                                 AND to_date('''|| stDtFinal ||'''::VARCHAR,''dd/mm/yyyy'')
                                            AND TO_CHAR(tp.timestamp,''yyyy'') = '''|| stExercicio ||'''
                                       GROUP BY tp.exercicio
                                              , tp.exercicio_plano
                                              , plano_banco.cod_plano
                                              , tp.cod_plano
                                              , nlp.vl_pago
                                              , plano_banco.nom_conta
                                              , oe.cod_entidade
                                              , cgm.nom_cgm
                                              , tp.timestamp
                                              , cp.exercicio
                                              , cp.cod_entidade
                                              , cp.tipo
                                              , cp.sequencia
                                              , cp.cod_lote
                                        ) as plano
                             INNER JOIN contabilidade.plano_analitica as cpa
                                     ON plano.cod_plano_debito   = cpa.cod_plano
                                    AND plano.exercicio          = cpa.exercicio
                             INNER JOIN contabilidade.plano_conta as cpc
                                     ON cpa.cod_conta = cpc.cod_conta
                                    AND cpa.exercicio = cpc.exercicio
                                    AND CPC.cod_estrutural like ''2.1.2.1.1%''
                              UNION ALL
                                 ---------------------------------------------
                                 --       ESTORNOS DE PAGAMENTOS RESTOS
                                 ---------------------------------------------
                                 SELECT plano.exercicio
                                      , plano.dt_pagamento
                                      , plano.cod_lote
                                      , cpa.cod_plano
                                      , plano.cod_plano_credito
                                      , plano.vl_pago
                                      , plano.tipo_despesa
                                      , plano.nom_conta
                                      , plano.cod_entidade
                                      , plano.nom_entidade
                                      , plano.timestamp
                                      , CPC.cod_estrutural || '' - '' || CPC.nom_conta as nome_despesa
                                      , plano.cod_plano_debito
                                   FROM (SELECT tp.exercicio_plano as exercicio
                                              , to_char(to_date(tp.timestamp::VARCHAR, ''YYYY-MM-DD''), ''DD/MM/YYYY'') as dt_pagamento
                                              , cp.cod_lote
                                              , plano_banco.cod_plano
                                              , tp.cod_plano as cod_plano_credito
                                              , nlpa.vl_anulado * (-1) AS vl_pago
                                              , cast(''ERE'' as varchar) as tipo_despesa
                                              , plano_banco.nom_conta as nom_conta
                                              , oe.cod_entidade
                                              , cgm.nom_cgm as nom_entidade
                                              , tp.timestamp
                                              , '''' as nome_despesa
                                              , contabilidade.fn_recupera_conta_lancamento( CP.exercicio
                                                                                          , CP.cod_entidade
                                                                                          , CP.cod_lote
                                                                                          , CP.tipo
                                                                                          , CP.sequencia
                                                                                          , ''D'') as cod_plano_debito
                                           FROM (SELECT CPA.cod_plano||'' - ''||CPC.nom_conta as nom_conta                
                                                      , cpa.cod_plano as cod_plano
                                                      , cpa.exercicio
                                                   FROM contabilidade.plano_conta as cpc
                                                      , contabilidade.plano_analitica as cpa
                                                  WHERE cpa.cod_conta = cpc.cod_conta
                                                    AND cpc.exercicio = cpa.exercicio
                                                ) as plano_banco
                                              , tesouraria.pagamento AS TP
                                     INNER JOIN tesouraria.pagamento_estornado AS TPE
                                             ON tpe.exercicio    = tp.exercicio
                                            AND tpe.cod_entidade = tp.cod_entidade
                                            AND tpe.cod_nota     = tp.cod_nota
                                            AND tpe.timestamp    = tp.timestamp
                                     INNER JOIN orcamento.entidade as oe
                                             ON oe.cod_entidade  = tpe.cod_entidade
                                            AND oe.exercicio = tpe.exercicio
                                     INNER JOIN sw_cgm as cgm
                                             ON oe.numcgm = cgm.numcgm
                                     INNER JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                                             on nlpa.cod_nota     = tp.cod_nota
                                            AND nlpa.cod_entidade = tp.cod_entidade
                                            AND nlpa.exercicio    = tp.exercicio
                                            AND nlpa.timestamp    = tp.timestamp
                                     INNER JOIN empenho.nota_liquidacao_paga as nlp
                                             ON nlp.cod_nota     = nlpa.cod_nota
                                            AND nlp.cod_entidade = nlpa.cod_entidade
                                            AND nlp.exercicio    = nlpa.exercicio
                                            AND nlp.timestamp    = nlpa.timestamp
                                     INNER JOIN empenho.nota_liquidacao as nl
                                             ON nl.cod_nota     = nlp.cod_nota
                                            AND nl.exercicio    = nlp.exercicio
                                            AND nl.cod_entidade = nlp.cod_entidade
                                            AND nl.exercicio_empenho < '''|| stExercicio ||'''
                                     INNER JOIN contabilidade.pagamento as cp
                                             ON cp.cod_entidade         = nlp.cod_entidade
                                            AND cp.exercicio_liquidacao = nlp.exercicio
                                            AND cp.cod_nota             = nlp.cod_nota
                                            AND cp.timestamp            = nlp.timestamp
                                     INNER JOIN contabilidade.lancamento_empenho as cle
                                             ON cle.cod_lote     = cp.cod_lote
                                            AND cle.cod_entidade = cp.cod_entidade
                                            AND cle.sequencia    = cp.sequencia
                                            AND cle.exercicio    = cp.exercicio
                                            AND cle.tipo         = cp.tipo
                                          WHERE tp.cod_plano = plano_banco.cod_plano
                                            and tp.exercicio_plano = plano_banco.exercicio 
                                            AND TO_DATE(TO_CHAR(tp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date('''|| stDtInicial ||'''::VARCHAR,''dd/mm/yyyy'')  AND to_date('''|| stDtFinal ||'''::VARCHAR,''dd/mm/yyyy'')
                                            AND TO_CHAR(tp.timestamp,''yyyy'') = '''|| stExercicio ||'''
                                       GROUP BY tp.exercicio
                                              , tp.exercicio_plano
                                              , plano_banco.cod_plano
                                              , tp.cod_plano
                                              , nlpa.vl_anulado
                                              , plano_banco.nom_conta
                                              , oe.cod_entidade
                                              , cgm.nom_cgm
                                              , tp.timestamp
                                              , cp.exercicio
                                              , cp.cod_entidade
                                              , cp.tipo
                                              , cp.sequencia
                                              , cp.cod_lote
                                        ) as plano
                             INNER JOIN contabilidade.plano_analitica as cpa
                                     ON plano.cod_plano_debito   = cpa.cod_plano
                                    AND plano.exercicio          = cpa.exercicio
                             INNER JOIN contabilidade.plano_conta as cpc
                                     ON cpa.cod_conta = cpc.cod_conta
                                    AND cpa.exercicio = cpc.exercicio
                                    AND CPC.cod_estrutural like ''2.1.2.1.1%''
                                ) as plano_debito
                      LEFT JOIN contabilidade.plano_analitica as cpa
                             ON plano_debito.cod_plano_debito   = cpa.cod_plano
                            AND plano_debito.exercicio          = cpa.exercicio
                      LEFT JOIN contabilidade.plano_recurso as cpr
                             on cpa.exercicio      = cpr.exercicio
                            AND cpa.cod_plano      = cpr.cod_plano
                      LEFT JOIN orcamento.recurso as orr
                             on orr.exercicio      = cpr.exercicio
                            AND orr.cod_recurso    = cpr.cod_recurso
                      LEFT JOIN contabilidade.plano_conta as cpc
                             ON cpa.cod_conta           = cpc.cod_conta
                            AND cpa.exercicio           = cpc.exercicio
                       GROUP BY plano_debito.dt_pagamento
                              , plano_debito.cod_lote
                              , cpr.cod_recurso
                              , orr.nom_recurso
                              , plano_debito.cod_plano
                              , plano_debito.nom_conta
                              , plano_debito.tipo_despesa
                              , plano_debito.cod_plano_credito
                              , plano_debito.cod_entidade
                              , plano_debito.nom_entidade
                              , plano_debito.exercicio
                              , plano_debito.cod_plano_debito
                              , plano_debito.nome_despesa
                        ) as tabela
          )
    ';
    EXECUTE stSql;

    stSql := '
          SELECT SPLIT_PART(nome_despesa,'' - '',1)::VARCHAR AS conta_contabil
               , (SELECT COALESCE(SUM(ext.valor),0.00)
                    FROM tmp_despesa_ext AS ext
                   WHERE TO_DATE(ext.dt_pagamento,''dd/mm/yyyy'') >= TO_DATE('''|| stDtInicialMes ||''',''dd/mm/yyyy'')
                     AND SPLIT_PART(ext.nome_despesa,'' - '',1)::VARCHAR = SPLIT_PART(tmp_despesa_ext.nome_despesa,'' - '',1)::VARCHAR
                 ) AS vl_mes
               , (SELECT COALESCE(SUM(ext.valor),0.00)
                    FROM tmp_despesa_ext AS ext
                   WHERE TO_DATE(ext.dt_pagamento,''dd/mm/yyyy'') < TO_DATE('''|| stDtInicialMes ||''',''dd/mm/yyyy'')
                     AND SPLIT_PART(ext.nome_despesa,'' - '',1)::VARCHAR = SPLIT_PART(tmp_despesa_ext.nome_despesa,'' - '',1)::VARCHAR
                 ) AS vl_ate_mes
            FROM tmp_despesa_ext
        GROUP BY tmp_despesa_ext.nome_despesa
        ORDER BY conta_contabil
    ';

    FOR reReg IN EXECUTE stSQL
    LOOP	
        RETURN NEXT reReg;
    END LOOP;

    DROP TABLE tmp_despesa_ext;

    RETURN;

END;

$$ LANGUAGE 'plpgsql';

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
    * Consulta da Conciliacao das movimentacoes correntes tcmba
    * Data de Criação: 06/06/2016

    * @author Michel Teixeira

    $Id: FTCMBAConciliacaoMovimentacaoCorrente.plsql 66474 2016-08-31 20:45:40Z michel $ 
*/

CREATE OR REPLACE FUNCTION tcmba.fn_conciliacao_movimentacao_corrente(VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;

    stFiltroArrecadacao VARCHAR := '';
    stSqlPlano          VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistroPlano     RECORD;
    reRegistro          RECORD;
    boRetorno           BOOLEAN;

    boTCEMS             BOOLEAN := FALSE;

BEGIN

stFiltroArrecadacao := ' AND TB.exercicio = '''||stExercicio||'''
                         AND (   TO_CHAR(TB.dt_boletim,''mm'')::INTEGER >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::varchar, ''dd/mm/yyyy''),''mm'')::INTEGER
                             AND TO_CHAR(TB.dt_boletim,''mm'')::INTEGER <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::varchar, ''dd/mm/yyyy''),''mm'')::INTEGER
                             )
                       ';

stSqlPlano := ' SELECT conciliacao.cod_plano
                  FROM tesouraria.conciliacao

             LEFT JOIN tcmba.conciliacao_lancamento_arrecadacao
                    ON conciliacao_lancamento_arrecadacao.cod_plano             = conciliacao.cod_plano
                   AND conciliacao_lancamento_arrecadacao.exercicio_conciliacao = conciliacao.exercicio
                   AND conciliacao_lancamento_arrecadacao.mes                   = conciliacao.mes

             LEFT JOIN tcmba.conciliacao_lancamento_arrecadacao_estornada
                    ON conciliacao_lancamento_arrecadacao_estornada.cod_plano             = conciliacao.cod_plano
                   AND conciliacao_lancamento_arrecadacao_estornada.exercicio_conciliacao = conciliacao.exercicio
                   AND conciliacao_lancamento_arrecadacao_estornada.mes                   = conciliacao.mes

             LEFT JOIN tcmba.conciliacao_lancamento_contabil
                    ON conciliacao_lancamento_contabil.cod_plano             = conciliacao.cod_plano
                   AND conciliacao_lancamento_contabil.exercicio_conciliacao = conciliacao.exercicio
                   AND conciliacao_lancamento_contabil.mes                   = conciliacao.mes

             LEFT JOIN tcmba.conciliacao_lancamento_manual
                    ON conciliacao_lancamento_manual.cod_plano = conciliacao.cod_plano
                   AND conciliacao_lancamento_manual.exercicio = conciliacao.exercicio
                   AND conciliacao_lancamento_manual.mes       = conciliacao.mes

                 WHERE conciliacao.exercicio = '''||stExercicio||'''
                   AND conciliacao.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::INTEGER
                   AND conciliacao.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::INTEGER
                   AND (   conciliacao_lancamento_arrecadacao.cod_plano           IS NOT NULL
                        OR conciliacao_lancamento_arrecadacao_estornada.cod_plano IS NOT NULL
                        OR conciliacao_lancamento_contabil.cod_plano              IS NOT NULL
                        OR conciliacao_lancamento_manual.cod_plano                IS NOT NULL
                       )
              GROUP BY conciliacao.cod_plano
              ORDER BY conciliacao.cod_plano;
              ';

CREATE TEMPORARY TABLE tmp_conciliacao_plano 
                     (exercicio                VARCHAR(4),
                      competencia              TEXT,
                      cod_estrutural           VARCHAR(160),
                      cod_tipo_conciliacao     INTEGER,
                      descricao                TEXT,
                      dt_extrato               DATE,
                      timestamp                TIMESTAMP,
                      vl_lancamento            NUMERIC(14,2),
                      cod_tipo_pagamento       INTEGER,
                      num_documento            VARCHAR(8),
                      cod_plano                INTEGER,
                      cod_conciliacao          INTEGER
                     );

IF stExercicio::INTEGER > 2012
    THEN boTCEMS := TRUE;
END IF;

FOR reRegistroPlano IN EXECUTE stSqlPlano LOOP

    SELECT tesouraria.fn_listar_arrecadacao_conciliacao( stFiltroArrecadacao
                                                       , stFiltroArrecadacao
                                                       , stDtFinal
                                                       , stDtInicial
                                                       , reRegistroPlano.cod_plano
                                                       , stCodEntidade
                                                       , stExercicio
                                                       , boTCEMS
                                                       ) INTO boRetorno;

    stSql := '
    -- PAGAMENTOS
    -------------
      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , lc.cod_tipo_conciliacao
           , cast( CASE WHEN TRIM(substring(ENLP.observacao,1,60)) = ''''
                        THEN CASE WHEN (ENL.exercicio_empenho < P.exercicio_boletim)
                                  THEN ''Pagamento de RP nro '' || ENL.cod_empenho || ''/'' || ENL.exercicio_empenho
                                  ELSE ''Pagamento de Empenho nro '' || ENL.cod_empenho || ''/'' || ENL.exercicio_empenho
                             END
                        ELSE CASE WHEN (ENL.exercicio_empenho < P.exercicio_boletim)
                                  THEN ''Pagamento de RP nro ''|| ENL.cod_empenho || ''/'' || ENL.exercicio_empenho
                                  ELSE ''Pagamento de Empenho nro '' || ENL.cod_empenho || ''/'' || ENL.exercicio_empenho
                             END
                   END AS varchar
             ) ||  CASE WHEN (cheque_emissao_ordem_pagamento.num_cheque IS NOT NULL) 
                        THEN '' CH '' || cheque_emissao_ordem_pagamento.num_cheque 
                        ELSE '' ''
                   END
             AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , cast(enlp.vl_pago as numeric ) AS vl_lancamento
           , CASE WHEN documento.cod_tipo IS NOT NULL
                  THEN documento.cod_tipo
                  ELSE 1
             END AS cod_tipo_pagamento
           , CASE WHEN documento.num_documento IS NOT NULL
                  THEN documento.num_documento
                  ELSE ''11111111''
             END AS num_documento
           , p.cod_plano
           , tcmba.fn_cod_concilia( lc.exercicio_conciliacao
                                  , lc.mes
                                  ,   lc.exercicio_conciliacao
                                    ||lc.mes
                                    ||lc.cod_lote
                                    ||lc.tipo
                                    ||lc.sequencia
                                    ||lc.cod_entidade
                                    ||lc.tipo_valor
                                    ||lc.cod_plano
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tesouraria.boletim AS BOLETIM

  INNER JOIN tesouraria.pagamento AS P
          ON BOLETIM.cod_boletim         = P.cod_boletim
         AND BOLETIM.exercicio           = P.exercicio_boletim
         AND BOLETIM.cod_entidade        = P.cod_entidade

  INNER JOIN empenho.nota_liquidacao_paga AS ENLP
          ON P.cod_nota                  = ENLP.cod_nota
         AND P.exercicio                 = ENLP.exercicio
         AND P.cod_entidade              = ENLP.cod_entidade
         AND P.timestamp                 = ENLP.timestamp

  INNER JOIN empenho.nota_liquidacao AS ENL
          ON ENLP.cod_nota               = ENL.cod_nota
         AND ENLP.exercicio              = ENL.exercicio
         AND ENLP.cod_entidade           = ENL.cod_entidade 

  INNER JOIN contabilidade.pagamento AS cp
          ON ENLP.exercicio              = CP.exercicio_liquidacao
         AND ENLP.cod_nota               = CP.cod_nota
         AND ENLP.cod_entidade           = CP.cod_entidade
         AND ENLP.timestamp              = CP.timestamp

  INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP
          ON EPLNLP.exercicio_liquidacao = ENLP.exercicio
         AND EPLNLP.cod_nota             = ENLP.cod_nota
         AND EPLNLP.cod_entidade         = ENLP.cod_entidade
         AND EPLNLP.timestamp            = ENLP.timestamp

  INNER JOIN empenho.pagamento_liquidacao AS EPL
          ON EPL.cod_ordem               = EPLNLP.cod_ordem
         AND EPL.exercicio               = EPLNLP.exercicio
         AND EPL.cod_entidade            = EPLNLP.cod_entidade
         AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
         AND EPL.cod_nota                = EPLNLP.cod_nota

  INNER JOIN tcmba.conciliacao_lancamento_contabil AS lc
          ON cp.cod_lote         = lc.cod_lote
         AND cp.tipo             = lc.tipo
         AND cp.sequencia        = lc.sequencia
         AND cp.exercicio        = lc.exercicio
         AND cp.cod_entidade     = lc.cod_entidade
         AND lc.tipo_valor = ''D''

  INNER JOIN tesouraria.conciliacao
          ON lc.cod_plano             = conciliacao.cod_plano
         AND lc.exercicio_conciliacao = conciliacao.exercicio
         AND lc.mes                   = conciliacao.mes

  INNER JOIN contabilidade.lancamento_empenho AS LE
          ON le.cod_entidade = cp.cod_entidade
         AND le.tipo         = cp.tipo
         AND le.sequencia    = cp.sequencia
         AND le.exercicio    = cp.exercicio
         AND le.cod_lote     = cp.cod_lote
         AND le.estorno = ''false''

  INNER JOIN contabilidade.lote AS lo
          ON le.cod_lote     = lo.cod_lote
         AND le.cod_entidade = lo.cod_entidade
         AND le.tipo         = lo.tipo
         AND le.exercicio    = lo.exercicio

   LEFT JOIN tesouraria.cheque_emissao_ordem_pagamento
          ON cheque_emissao_ordem_pagamento.cod_ordem    = EPL.cod_ordem
         AND cheque_emissao_ordem_pagamento.exercicio    = EPL.exercicio
         AND cheque_emissao_ordem_pagamento.cod_entidade = EPL.cod_entidade
         AND cheque_emissao_ordem_pagamento.timestamp_emissao = ( SELECT MAX(timestamp_emissao)
                                                                    FROM tesouraria.cheque_emissao_ordem_pagamento
                                                                   WHERE cod_ordem    = EPL.cod_ordem
                                                                     AND exercicio    = EPL.exercicio
                                                                     AND cod_entidade = EPL.cod_entidade )

   LEFT JOIN tesouraria.transacoes_pagamento AS TTP
          ON ttp.cod_ordem    = EPLNLP.cod_ordem
         AND ttp.cod_entidade = EPLNLP.cod_entidade
         AND ttp.exercicio    = EPLNLP.exercicio

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta     = plano_conta.cod_conta
         AND plano_analitica.exercicio     = plano_conta.exercicio

   LEFT JOIN tcmba.pagamento_tipo_documento_pagamento AS documento
          ON P.exercicio    = documento.exercicio
         AND P.cod_nota     = documento.cod_nota
         AND P.cod_entidade = documento.cod_entidade
         AND P.timestamp    = documento.timestamp

       WHERE p.cod_plano = '||reRegistroPlano.cod_plano||'
         AND p.cod_entidade in ( '||stCodEntidade||' )
         AND conciliacao.exercicio = '''||stExercicio||'''
         AND conciliacao.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND conciliacao.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND to_char(P.timestamp,''yyyy'')::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND lo.dt_lote = to_date(to_char(P.timestamp,''yyyy-mm-dd''),''yyyy-mm-dd'')

       UNION

      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , lc.cod_tipo_conciliacao
           , CAST( CASE WHEN TRIM(substring(nota_liquidacao_paga_anulada.observacao,1,60)) =  ''''
                        THEN CASE WHEN (nota_liquidacao.exercicio_empenho < pagamento.exercicio_boletim)
                                  THEN ''Estorno de Pagamento de RP nro '' || nota_liquidacao.cod_empenho || ''/'' || nota_liquidacao.exercicio_empenho
                                  ELSE ''Estorno de Pagamento de Empenho nro '' || nota_liquidacao.cod_empenho || ''/'' || nota_liquidacao.exercicio_empenho
                             END
                        ELSE CASE WHEN (nota_liquidacao.exercicio_empenho < pagamento.exercicio_boletim)
                                  THEN ''Estorno de Pagamento de RP nro '' || nota_liquidacao.cod_empenho || ''/'' || nota_liquidacao.exercicio_empenho
                                  ELSE ''Estorno de Pagamento de Empenho nro '' || nota_liquidacao.cod_empenho || ''/'' || nota_liquidacao.exercicio_empenho
                             END
                   END AS varchar
             ) || CASE WHEN (cheque_emissao_ordem_pagamento.num_cheque IS NOT NULL)
                       THEN '' CH '' || cheque_emissao_ordem_pagamento.num_cheque
                  END
             AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , nota_liquidacao_paga_anulada.vl_anulado AS vl_lancamento
           , CASE WHEN documento.cod_tipo IS NOT NULL
                  THEN documento.cod_tipo
                  ELSE 1
             END AS cod_tipo_pagamento
           , CASE WHEN documento.num_documento IS NOT NULL
                  THEN documento.num_documento
                  ELSE ''11111111''
             END AS num_documento
           , pagamento.cod_plano
           , tcmba.fn_cod_concilia( lc.exercicio_conciliacao
                                  , lc.mes
                                  ,   lc.exercicio_conciliacao
                                    ||lc.mes
                                    ||lc.cod_lote
                                    ||lc.tipo
                                    ||lc.sequencia
                                    ||lc.cod_entidade
                                    ||lc.tipo_valor
                                    ||lc.cod_plano
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tesouraria.boletim

  INNER JOIN tesouraria.pagamento_estornado
          ON boletim.cod_boletim  = pagamento_estornado.cod_boletim
         AND boletim.exercicio    = pagamento_estornado.exercicio_boletim
         AND boletim.cod_entidade = pagamento_estornado.cod_entidade

  INNER JOIN tesouraria.pagamento
          ON pagamento_estornado.cod_nota     = pagamento.cod_nota
         AND pagamento_estornado.exercicio    = pagamento.exercicio
         AND pagamento_estornado.cod_entidade = pagamento.cod_entidade
         AND pagamento_estornado.timestamp    = pagamento.timestamp

  INNER JOIN contabilidade.pagamento AS conciliacao_pagamento
          ON pagamento.exercicio    = conciliacao_pagamento.exercicio_liquidacao
         AND pagamento.cod_nota     = conciliacao_pagamento.cod_nota
         AND pagamento.cod_entidade = conciliacao_pagamento.cod_entidade
         AND pagamento.timestamp    = conciliacao_pagamento.timestamp

  INNER JOIN contabilidade.pagamento_estorno
          ON conciliacao_pagamento.cod_lote             = pagamento_estorno.cod_lote
         AND conciliacao_pagamento.tipo                 = pagamento_estorno.tipo
         AND conciliacao_pagamento.sequencia            = pagamento_estorno.sequencia
         AND conciliacao_pagamento.exercicio            = pagamento_estorno.exercicio
         AND conciliacao_pagamento.cod_entidade         = pagamento_estorno.cod_entidade
         AND conciliacao_pagamento.timestamp            = pagamento_estorno.timestamp
         AND conciliacao_pagamento.cod_nota             = pagamento_estorno.cod_nota
         AND conciliacao_pagamento.exercicio_liquidacao = pagamento_estorno.exercicio_liquidacao

  INNER JOIN tcmba.conciliacao_lancamento_contabil AS lc
          ON conciliacao_pagamento.cod_lote         = lc.cod_lote
         AND conciliacao_pagamento.tipo             = lc.tipo
         AND conciliacao_pagamento.sequencia        = lc.sequencia
         AND conciliacao_pagamento.exercicio        = lc.exercicio
         AND conciliacao_pagamento.cod_entidade     = lc.cod_entidade
         AND lc.tipo_valor = ''C''

  INNER JOIN tesouraria.conciliacao
          ON lc.cod_plano             = conciliacao.cod_plano
         AND lc.exercicio_conciliacao = conciliacao.exercicio
         AND lc.mes                   = conciliacao.mes

  INNER JOIN contabilidade.lancamento_empenho
          ON lancamento_empenho.cod_entidade = conciliacao_pagamento.cod_entidade
         AND lancamento_empenho.tipo         = conciliacao_pagamento.tipo
         AND lancamento_empenho.sequencia    = conciliacao_pagamento.sequencia
         AND lancamento_empenho.exercicio    = conciliacao_pagamento.exercicio
         AND lancamento_empenho.cod_lote     = conciliacao_pagamento.cod_lote
         AND lancamento_empenho.estorno = ''true''

  INNER JOIN contabilidade.lote
          ON lancamento_empenho.cod_lote     = lote.cod_lote
         AND lancamento_empenho.cod_entidade = lote.cod_entidade
         AND lancamento_empenho.tipo         = lote.tipo
         AND lancamento_empenho.exercicio    = lote.exercicio

  INNER JOIN empenho.nota_liquidacao_paga_anulada
          ON nota_liquidacao_paga_anulada.exercicio         = pagamento_estorno.exercicio_liquidacao
         AND nota_liquidacao_paga_anulada.cod_nota          = pagamento_estorno.cod_nota
         AND nota_liquidacao_paga_anulada.cod_entidade      = pagamento_estorno.cod_entidade
         AND nota_liquidacao_paga_anulada.timestamp         = pagamento_estorno.timestamp
         AND nota_liquidacao_paga_anulada.timestamp_anulada = pagamento_estorno.timestamp_anulada

  INNER JOIN empenho.nota_liquidacao_paga
          ON nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
         AND nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
         AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
         AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp 

  INNER JOIN empenho.nota_liquidacao
          ON nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
         AND nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
         AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade

  INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
          ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio
         AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = nota_liquidacao_paga.cod_nota
         AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = nota_liquidacao_paga.cod_entidade
         AND pagamento_liquidacao_nota_liquidacao_paga.timestamp            = nota_liquidacao_paga.timestamp

  INNER JOIN empenho.pagamento_liquidacao
          ON pagamento_liquidacao.cod_ordem            = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
         AND pagamento_liquidacao.exercicio            = pagamento_liquidacao_nota_liquidacao_paga.exercicio
         AND pagamento_liquidacao.cod_entidade         = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
         AND pagamento_liquidacao.exercicio_liquidacao = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
         AND pagamento_liquidacao.cod_nota             = pagamento_liquidacao_nota_liquidacao_paga.cod_nota

   LEFT JOIN ( SELECT MAX(timestamp_emissao), num_cheque, cod_ordem, exercicio, cod_entidade
                 FROM tesouraria.cheque_emissao_ordem_pagamento
             GROUP BY num_cheque, cod_ordem, exercicio, cod_entidade
             ) AS cheque_emissao_ordem_pagamento
          ON cheque_emissao_ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem
         AND cheque_emissao_ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio
         AND cheque_emissao_ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade 

   LEFT JOIN tesouraria.transacoes_pagamento
          ON transacoes_pagamento.cod_ordem    = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
         AND transacoes_pagamento.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
         AND transacoes_pagamento.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta     = plano_conta.cod_conta
         AND plano_analitica.exercicio     = plano_conta.exercicio

   LEFT JOIN tcmba.pagamento_tipo_documento_pagamento AS documento
          ON pagamento.exercicio    = documento.exercicio
         AND pagamento.cod_nota     = documento.cod_nota
         AND pagamento.cod_entidade = documento.cod_entidade
         AND pagamento.timestamp    = documento.timestamp

       WHERE pagamento.cod_plano = '||reRegistroPlano.cod_plano||'
         AND pagamento_estornado.cod_entidade in ( '||stCodEntidade||' )
         AND conciliacao.exercicio = '''||stExercicio||'''
         AND conciliacao.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND conciliacao.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND to_char(pagamento_estornado.timestamp_anulado,''yyyy'')::INTEGER BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND to_char(pagamento.timestamp,''yyyy'')::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND lote.dt_lote = to_date(to_char(pagamento_estornado.timestamp_anulado,''yyyy-mm-dd''),''yyyy-mm-dd'')

       UNION

    -- TRANSFERENCIAS
    ------------------
    -- BUSCA AS ARRECADAÇÕES EXTRA, PAGAMENTOS EXTRA, DEPÓSITOS/RETIRADAS, APLICAÇÃO E  RESGATES ( DEBITO )

      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , lc.cod_tipo_conciliacao
           , trim(TTT.descricao || '' - CD:''||T.cod_plano_debito ||'' | CC:'' || T.cod_plano_credito) AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , t.valor AS vl_lancamento
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( lc.exercicio_conciliacao
                                  , lc.mes
                                  ,   lc.exercicio_conciliacao
                                    ||lc.mes
                                    ||lc.cod_lote
                                    ||lc.tipo
                                    ||lc.sequencia
                                    ||lc.cod_entidade
                                    ||lc.tipo_valor
                                    ||lc.cod_plano
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tesouraria.boletim AS BOLETIM

  INNER JOIN tesouraria.transferencia AS T
          ON BOLETIM.cod_boletim   = T.cod_boletim
         AND BOLETIM.exercicio     = T.exercicio
         AND BOLETIM.cod_entidade  = T.cod_entidade

  INNER JOIN tesouraria.tipo_transferencia AS TTT
          ON TTT.cod_tipo = T.cod_tipo

   LEFT JOIN tesouraria.transacoes_transferencia AS tttt
          ON tttt.cod_entidade = t.cod_entidade
         AND tttt.numcgm = t.cgm_usuario
         AND tttt.exercicio = t.exercicio
         AND tttt.cod_plano = t.cod_plano_debito

  INNER JOIN tcmba.conciliacao_lancamento_contabil AS lc
          ON t.cod_lote         = lc.cod_lote
         AND t.tipo             = lc.tipo
         AND t.exercicio        = lc.exercicio
         AND t.cod_entidade     = lc.cod_entidade
         AND t.cod_plano_debito = lc.cod_plano
         AND lc.tipo_valor = ''D''
         AND lc.sequencia = 1

  INNER JOIN tesouraria.conciliacao
          ON lc.cod_plano             = conciliacao.cod_plano
         AND lc.exercicio_conciliacao = conciliacao.exercicio
         AND lc.mes                   = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta     = plano_conta.cod_conta
         AND plano_analitica.exercicio     = plano_conta.exercicio

       WHERE T.cod_plano_debito    = '||reRegistroPlano.cod_plano||'
         AND T.exercicio::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND T.cod_entidade        in ( '||stCodEntidade||' )
         AND conciliacao.exercicio = '''||stExercicio||'''
         AND conciliacao.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND conciliacao.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer >= TO_CHAR(TO_DATE( '''||stDtInicial||''', ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer <= TO_CHAR(TO_DATE( '''||stDtFinal||''', ''dd/mm/yyyy'' ),''mm'')::integer

     UNION

    -- BUSCA AS ARRECADAÇÕES EXTRA, PAGAMENTOS EXTRA, DEPÓSITOS/RETIRADAS, APLICAÇÃO E  RESGATES ( CREDITO )

      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , lc.cod_tipo_conciliacao
           , trim(TTT.descricao || '' - CD:''||T.cod_plano_debito ||'' | CC:'' || T.cod_plano_credito) AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , T.valor AS vl_lancamento
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( lc.exercicio_conciliacao
                                  , lc.mes
                                  ,   lc.exercicio_conciliacao
                                    ||lc.mes
                                    ||lc.cod_lote
                                    ||lc.tipo
                                    ||lc.sequencia
                                    ||lc.cod_entidade
                                    ||lc.tipo_valor
                                    ||lc.cod_plano
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tesouraria.boletim AS BOLETIM

  INNER JOIN tesouraria.transferencia AS T
          ON BOLETIM.cod_boletim   = T.cod_boletim
         AND BOLETIM.exercicio     = T.exercicio
         AND BOLETIM.cod_entidade  = T.cod_entidade

  INNER JOIN tesouraria.tipo_transferencia AS TTT
          ON TTT.cod_tipo = T.cod_tipo

   LEFT JOIN tesouraria.transacoes_transferencia AS tttt
          ON tttt.cod_entidade = t.cod_entidade
         AND tttt.numcgm = t.cgm_usuario
         AND tttt.exercicio = t.exercicio
         AND tttt.cod_plano = t.cod_plano_credito

  INNER JOIN tcmba.conciliacao_lancamento_contabil AS lc
          ON t.cod_lote          = lc.cod_lote
         AND t.tipo              = lc.tipo
         AND t.exercicio         = lc.exercicio
         AND t.cod_entidade      = lc.cod_entidade
         AND t.cod_plano_credito = lc.cod_plano
         AND lc.tipo_valor = ''C''
         AND lc.sequencia = 1

  INNER JOIN tesouraria.conciliacao
          ON lc.cod_plano             = conciliacao.cod_plano
         AND lc.exercicio_conciliacao = conciliacao.exercicio
         AND lc.mes                   = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta     = plano_conta.cod_conta
         AND plano_analitica.exercicio     = plano_conta.exercicio

       WHERE T.cod_plano_credito    = '||reRegistroPlano.cod_plano||'
         AND T.exercicio::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND T.cod_entidade        in ( '||stCodEntidade||' )
         AND conciliacao.exercicio = '''||stExercicio||'''
         AND conciliacao.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND conciliacao.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer >= TO_CHAR(TO_DATE( '''||stDtInicial||''', ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer <= TO_CHAR(TO_DATE( '''||stDtFinal||''', ''dd/mm/yyyy'' ),''mm'')::integer

     UNION

     -- ESTORNO DE TRANSFERENCIAS
      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , lc.cod_tipo_conciliacao
           , trim('' Estorno de '' || TTT.descricao || '' - CD: ''||T.cod_plano_credito ||'' | CC: '' || T.cod_plano_debito ) AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , TE.valor AS vl_lancamento 
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( lc.exercicio_conciliacao
                                  , lc.mes
                                  ,   lc.exercicio_conciliacao
                                    ||lc.mes
                                    ||lc.cod_lote
                                    ||lc.tipo
                                    ||lc.sequencia
                                    ||lc.cod_entidade
                                    ||lc.tipo_valor
                                    ||lc.cod_plano
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tesouraria.boletim AS BOLETIM

  INNER JOIN tesouraria.transferencia_estornada AS TE
          ON BOLETIM.cod_boletim   = TE.cod_boletim
         AND BOLETIM.exercicio     = TE.exercicio
         AND BOLETIM.cod_entidade  = TE.cod_entidade

  INNER JOIN tesouraria.transferencia AS T
          ON TE.exercicio          = T.exercicio
         AND TE.cod_entidade       = T.cod_entidade
         AND TE.cod_lote           = T.cod_lote
         AND TE.tipo               = T.tipo

  INNER JOIN tesouraria.tipo_transferencia AS TTT
          ON TTT.cod_tipo          = T.cod_tipo

   LEFT JOIN tesouraria.transacoes_transferencia AS tttt
          ON tttt.cod_entidade = t.cod_entidade
         AND tttt.numcgm = t.cgm_usuario
         AND tttt.exercicio = t.exercicio
         AND tttt.cod_plano = t.cod_plano_debito

  INNER JOIN tcmba.conciliacao_lancamento_contabil AS lc
          ON te.cod_lote_estorno = lc.cod_lote
         AND te.tipo             = lc.tipo
         AND te.exercicio        = lc.exercicio
         AND te.cod_entidade     = lc.cod_entidade
         AND t.cod_plano_debito  = lc.cod_plano
         AND lc.tipo_valor = ''D''
         AND lc.sequencia = 1

  INNER JOIN tesouraria.conciliacao
          ON lc.cod_plano             = conciliacao.cod_plano
         AND lc.exercicio_conciliacao = conciliacao.exercicio
         AND lc.mes                   = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta     = plano_conta.cod_conta
         AND plano_analitica.exercicio     = plano_conta.exercicio

       WHERE T.cod_plano_debito    = '||reRegistroPlano.cod_plano||'
         AND TE.exercicio::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND T.exercicio::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND TE.cod_entidade        in ( '||stCodEntidade||' )
         AND conciliacao.exercicio = '''||stExercicio||'''
         AND conciliacao.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND conciliacao.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer >= TO_CHAR(TO_DATE( '''||stDtInicial||''', ''dd/mm/yyyy''),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer <= TO_CHAR(TO_DATE( '''||stDtFinal||''', ''dd/mm/yyyy''),''mm'')::integer

    UNION

      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , lc.cod_tipo_conciliacao
           , trim('' Estorno de '' || TTT.descricao || '' - CD: ''||T.cod_plano_credito ||'' | CC: '' || T.cod_plano_debito ) AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , TE.valor AS vl_lancamento  
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( lc.exercicio_conciliacao
                                  , lc.mes
                                  ,   lc.exercicio_conciliacao
                                    ||lc.mes
                                    ||lc.cod_lote
                                    ||lc.tipo
                                    ||lc.sequencia
                                    ||lc.cod_entidade
                                    ||lc.tipo_valor
                                    ||lc.cod_plano
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tesouraria.boletim AS BOLETIM

  INNER JOIN tesouraria.transferencia_estornada AS TE
          ON BOLETIM.cod_boletim   = TE.cod_boletim
         AND BOLETIM.exercicio     = TE.exercicio
         AND BOLETIM.cod_entidade  = TE.cod_entidade

  INNER JOIN tesouraria.transferencia AS T
          ON TE.exercicio          = T.exercicio
         AND TE.cod_entidade       = T.cod_entidade
         AND TE.cod_lote           = T.cod_lote
         AND TE.tipo               = T.tipo

  INNER JOIN tesouraria.tipo_transferencia AS TTT
          ON TTT.cod_tipo = T.cod_tipo

   LEFT JOIN tesouraria.transacoes_transferencia AS tttt
          ON tttt.cod_entidade = t.cod_entidade
         AND tttt.numcgm = t.cgm_usuario
         AND tttt.exercicio = t.exercicio
         AND tttt.cod_plano = t.cod_plano_credito

  INNER JOIN tcmba.conciliacao_lancamento_contabil AS lc
          ON te.cod_lote_estorno = lc.cod_lote
         AND te.tipo             = lc.tipo
         AND te.exercicio        = lc.exercicio
         AND te.cod_entidade     = lc.cod_entidade
         AND t.cod_plano_credito  = lc.cod_plano
         AND lc.tipo_valor = ''C''
         AND lc.sequencia = 1

  INNER JOIN tesouraria.conciliacao
          ON lc.cod_plano             = conciliacao.cod_plano
         AND lc.exercicio_conciliacao = conciliacao.exercicio
         AND lc.mes                   = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta     = plano_conta.cod_conta
         AND plano_analitica.exercicio     = plano_conta.exercicio

       WHERE T.cod_plano_credito    = '||reRegistroPlano.cod_plano||'
         AND TE.exercicio::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND T.exercicio::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND TE.cod_entidade        in ( '||stCodEntidade||' )
         AND conciliacao.exercicio = '''||stExercicio||'''
         AND conciliacao.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND conciliacao.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy'' ),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer >= TO_CHAR(TO_DATE( '''||stDtInicial||''', ''dd/mm/yyyy''),''mm'')::integer
         AND TO_CHAR(BOLETIM.dt_boletim,''mm'')::integer <= TO_CHAR(TO_DATE( '''||stDtFinal||''', ''dd/mm/yyyy''),''mm'')::integer

    UNION

    -- ARRECADACOES
    -- ARRECADACAO DE RECEITA
      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , TCLA.cod_tipo_conciliacao
           , CAST( ''Arrecadação da receita Boletim nro ''||TA.cod_boletim AS varchar ) AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , TA.valor AS vl_lancamento  
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( TCLA.exercicio_conciliacao
                                  , TCLA.mes
                                  ,   TCLA.exercicio
                                    ||TCLA.mes
                                    ||TCLA.cod_arrecadacao
                                    ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(TCLA.timestamp_arrecadacao::TEXT), ''.'',''''), '':'',''''), ''-'',''''), '' '','''')
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tmp_arrecadacao AS TA

  INNER JOIN tcmba.conciliacao_lancamento_arrecadacao AS TCLA
          ON TA.exercicio             = TCLA.exercicio
         AND TA.cod_arrecadacao       = TCLA.cod_arrecadacao
         AND TA.timestamp_arrecadacao = TCLA.timestamp_arrecadacao

  INNER JOIN tesouraria.conciliacao
          ON tcla.cod_plano             = conciliacao.cod_plano
         AND tcla.exercicio_conciliacao = conciliacao.exercicio
         AND tcla.mes                   = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta = plano_conta.cod_conta
         AND plano_analitica.exercicio = plano_conta.exercicio

       WHERE ta.tipo_arrecadacao = ''A''
         AND EXISTS ( SELECT true
                        FROM tesouraria.arrecadacao AS tta
                       WHERE ta.exercicio               = TTA.exercicio
                         AND ta.cod_arrecadacao         = TTA.cod_arrecadacao
                         AND ta.timestamp_arrecadacao   = TTA.timestamp_arrecadacao )

    UNION

    -- ESTORNO DE DEDUCAO
      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , TCLAE.cod_tipo_conciliacao
           , CAST( ''Estorno de Dedução de receita Boletim nro ''||TA.cod_boletim AS varchar) AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , TA.valor AS vl_lancamento  
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( TCLAE.exercicio_conciliacao
                                  , TCLAE.mes
                                  ,   TCLAE.exercicio
                                    ||TCLAE.mes
                                    ||TCLAE.cod_arrecadacao
                                    ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(TCLAE.timestamp_arrecadacao::TEXT), ''.'',''''), '':'',''''), ''-'',''''), '' '','''')
                                    ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(TCLAE.timestamp_estornada::TEXT), ''.'',''''), '':'',''''), ''-'',''''), '' '','''')
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tmp_arrecadacao AS TA

  INNER JOIN tcmba.conciliacao_lancamento_arrecadacao_estornada AS TCLAE
          ON TA.exercicio             = TCLAE.exercicio
         AND TA.cod_arrecadacao       = TCLAE.cod_arrecadacao
         AND TA.timestamp_arrecadacao = TCLAE.timestamp_arrecadacao
         AND TA.timestamp_estornada   = TCLAE.timestamp_estornada

  INNER JOIN tesouraria.conciliacao
          ON tclae.cod_plano             = conciliacao.cod_plano
         AND tclae.exercicio_conciliacao = conciliacao.exercicio
         AND tclae.mes                   = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta = plano_conta.cod_conta
         AND plano_analitica.exercicio = plano_conta.exercicio

       WHERE ta.tipo_arrecadacao = ''D''
         AND EXISTS ( SELECT true 
                        FROM tesouraria.arrecadacao_estornada AS TTE
                       WHERE ta.exercicio               = TTE.exercicio
                         AND ta.cod_arrecadacao         = TTE.cod_arrecadacao
                         AND ta.timestamp_arrecadacao   = TTE.timestamp_arrecadacao
                         AND ta.timestamp_estornada     = TTE.timestamp_estornada )

    UNION

    -- ESTORNO DE ARRECADACAO DE RECEITA
      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , TCLAE.cod_tipo_conciliacao
           , CAST(''Estorno de Arrecadação da receita Boletim nro ''||TAE.cod_boletim AS text) AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , TAE.valor AS vl_lancamento
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( TCLAE.exercicio_conciliacao
                                  , TCLAE.mes
                                  ,   TCLAE.exercicio
                                    ||TCLAE.mes
                                    ||TCLAE.cod_arrecadacao
                                    ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(TCLAE.timestamp_arrecadacao::TEXT), ''.'',''''), '':'',''''), ''-'',''''), '' '','''')
                                    ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(TCLAE.timestamp_estornada::TEXT), ''.'',''''), '':'',''''), ''-'',''''), '' '','''')
                                  , TRUE
                                  )

        FROM tmp_arrecadacao_estornada AS TAE

  INNER JOIN tcmba.conciliacao_lancamento_arrecadacao_estornada AS TCLAE
          ON TAE.exercicio             = TCLAE.exercicio
         AND TAE.cod_arrecadacao       = TCLAE.cod_arrecadacao
         AND TAE.timestamp_arrecadacao = TCLAE.timestamp_arrecadacao
         AND TAE.timestamp             = TCLAE.timestamp_estornada

  INNER JOIN tesouraria.conciliacao
          ON tclae.cod_plano             = conciliacao.cod_plano
         AND tclae.exercicio_conciliacao = conciliacao.exercicio
         AND tclae.mes                   = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta = plano_conta.cod_conta
         AND plano_analitica.exercicio = plano_conta.exercicio

       WHERE tae.tipo_arrecadacao = ''A''
         AND EXISTS ( SELECT true
                        FROM tesouraria.arrecadacao_estornada AS TTE
                       WHERE tae.exercicio               = TTE.exercicio
                         AND tae.cod_arrecadacao         = TTE.cod_arrecadacao
                         AND tae.timestamp_arrecadacao   = TTE.timestamp_arrecadacao
                         AND tae.timestamp               = TTE.timestamp_estornada )

    UNION

    -- DEDUCAO DE RECEITA ou DEVOLUÇAO DE RECEITA
      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , TCLA.cod_tipo_conciliacao
           , CASE WHEN ta.devolucao = ''f''
                  THEN CAST(''Dedução da receita Boletim nro ''||TAE.cod_boletim AS text)
                  ELSE CAST(''Devolução da receita Boletim nro ''||TAE.cod_boletim AS text)
             END AS descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , TAE.valor AS vl_lancamento
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , conciliacao.cod_plano
           , tcmba.fn_cod_concilia( TCLA.exercicio_conciliacao
                                  , TCLA.mes
                                  ,   TCLA.exercicio
                                    ||TCLA.mes
                                    ||TCLA.cod_arrecadacao
                                    ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(TCLA.timestamp_arrecadacao::TEXT), ''.'',''''), '':'',''''), ''-'',''''), '' '','''')
                                  , TRUE
                                  )

        FROM tmp_arrecadacao_estornada AS TAE

  INNER JOIN tcmba.conciliacao_lancamento_arrecadacao AS TCLA
          ON TAE.exercicio             = TCLA.exercicio
         AND TAE.cod_arrecadacao       = TCLA.cod_arrecadacao
         AND TAE.timestamp_arrecadacao = TCLA.timestamp_arrecadacao

  INNER JOIN tesouraria.conciliacao
          ON tcla.cod_plano             = conciliacao.cod_plano
         AND tcla.exercicio_conciliacao = conciliacao.exercicio
         AND tcla.mes                   = conciliacao.mes

   LEFT JOIN tesouraria.arrecadacao AS TA
          ON tae.exercicio               = TA.exercicio
         AND tae.cod_arrecadacao         = TA.cod_arrecadacao
         AND tae.timestamp_arrecadacao   = TA.timestamp_arrecadacao

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta = plano_conta.cod_conta
         AND plano_analitica.exercicio = plano_conta.exercicio

       WHERE tae.tipo_arrecadacao = ''D''
         AND EXISTS ( SELECT true
                        FROM tesouraria.arrecadacao AS tta
                       WHERE tae.exercicio               = TTA.exercicio
                         AND tae.cod_arrecadacao         = TTA.cod_arrecadacao
                         AND tae.timestamp_arrecadacao   = TTA.timestamp_arrecadacao )

    UNION

    --- BORDEROS
      SELECT TTP.exercicio_conciliacao AS exercicio
           , TTP.mes
           , TTP.exercicio_conciliacao||LPAD(TTP.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , TTP.cod_tipo_conciliacao
           , ''Pagamento de Bordero '' || TB.cod_bordero || ''/'' || TB.exercicio || '' - OP - '' || tesouraria.retorna_OPs(TTP.exercicio,TTP.cod_bordero,TTP.cod_entidade) AS descricao
           , TTP.dt_extrato
           , TTP.timestamp
           , TTP.vl_pagamento AS vl_lancamento
           , TTP.cod_tipo_pagamento
           , TTP.num_documento
           , TB.cod_plano
           , TTP.cod_conciliacao

        FROM tesouraria.boletim AS BOLETIM

  INNER JOIN tesouraria.bordero AS TB
          ON TB.cod_boletim       = BOLETIM.cod_boletim
         AND TB.cod_entidade      = BOLETIM.cod_entidade
         AND TB.exercicio_boletim = BOLETIM.exercicio

  INNER JOIN ( SELECT TTP.cod_bordero,
                      TTP.cod_entidade,
                      TTP.exercicio,
                      tclc.cod_plano,
                      tclc.mes,
                      tclc.exercicio_conciliacao,
                      conciliacao.dt_extrato,
                      conciliacao.timestamp, 
                      sum(CVL.vl_lancamento) - coalesce( sum( CVLE.vl_estornado ), 0.00 ) AS vl_pagamento,
                      tclc.cod_tipo_conciliacao,
                      CASE WHEN documento.cod_tipo IS NOT NULL
                           THEN documento.cod_tipo
                           ELSE 1
                      END AS cod_tipo_pagamento,
                      CASE WHEN documento.num_documento IS NOT NULL
                           THEN documento.num_documento
                           ELSE ''11111111''
                      END AS num_documento,
                      tcmba.fn_cod_concilia( tclc.exercicio_conciliacao
                                           , tclc.mes
                                           ,   tclc.exercicio_conciliacao
                                             ||tclc.mes
                                             ||tclc.cod_lote
                                             ||tclc.tipo
                                             ||tclc.sequencia
                                             ||tclc.cod_entidade
                                             ||tclc.tipo_valor
                                             ||tclc.cod_plano
                                           , TRUE
                                           )
                      AS cod_conciliacao

                 FROM tesouraria.transacoes_pagamento AS TTP

           INNER JOIN empenho.ordem_pagamento AS EOP
                   ON TTP.cod_ordem               = EOP.cod_ordem
                  AND TTP.cod_entidade            = EOP.cod_entidade
                  AND TTP.exercicio               = EOP.exercicio

           INNER JOIN empenho.pagamento_liquidacao AS EPL
                   ON EOP.cod_ordem               = EPL.cod_ordem
                  AND EOP.cod_entidade            = EPL.cod_entidade
                  AND EOP.exercicio               = EPL.exercicio

           INNER JOIN empenho.nota_liquidacao AS ENL
                   ON EPL.exercicio_liquidacao    = ENL.exercicio
                  AND EPL.cod_nota                = ENL.cod_nota
                  AND EPL.cod_entidade            = ENL.cod_entidade

           INNER JOIN empenho.empenho AS EE
                   ON ENL.exercicio_empenho       = EE.exercicio
                  AND ENL.cod_empenho             = EE.cod_empenho
                  AND ENL.cod_entidade            = EE.cod_entidade 

           INNER JOIN empenho.pre_empenho AS EPE
                   ON EE.cod_pre_empenho          = EPE.cod_pre_empenho
                  AND EE.exercicio                = EPE.exercicio

           INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP
                   ON EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
                  AND EPL.cod_nota                = EPLNLP.cod_nota
                  AND EPL.cod_entidade            = EPLNLP.cod_entidade
                  AND EPL.cod_ordem               = EPLNLP.cod_ordem
                  AND EPL.exercicio               = EPLNLP.exercicio

           INNER JOIN empenho.nota_liquidacao_paga AS ENLP
                   ON EPLNLP.exercicio_liquidacao = ENLP.exercicio
                  AND EPLNLP.cod_nota             = ENLP.cod_nota
                  AND EPLNLP.cod_entidade         = ENLP.cod_entidade
                  AND EPLNLP.timestamp            = ENLP.timestamp

           INNER JOIN contabilidade.pagamento AS CP
                   ON ENLP.exercicio              = CP.exercicio_liquidacao
                  AND ENLP.cod_nota               = CP.cod_nota
                  AND ENLP.cod_entidade           = CP.cod_entidade
                  AND ENLP.timestamp              = CP.timestamp

           INNER JOIN tcmba.conciliacao_lancamento_contabil AS tclc
                   ON cp.cod_lote         = tclc.cod_lote
                  AND cp.tipo             = tclc.tipo
                  AND cp.sequencia        = tclc.sequencia
                  AND cp.exercicio        = tclc.exercicio
                  AND cp.cod_entidade     = tclc.cod_entidade
                  AND tclc.tipo_valor = ''C''

           INNER JOIN tesouraria.conciliacao
                   ON tclc.cod_plano             = conciliacao.cod_plano
                  AND tclc.exercicio_conciliacao = conciliacao.exercicio
                  AND tclc.mes                   = conciliacao.mes

            LEFT JOIN tcmba.pagamento_tipo_documento_pagamento AS documento
                   ON CP.exercicio    = documento.exercicio
                  AND CP.cod_nota     = documento.cod_nota
                  AND CP.cod_entidade = documento.cod_entidade
                  AND CP.timestamp    = documento.timestamp

            LEFT JOIN ( SELECT CVL.exercicio
                              ,CVL.cod_entidade
                              ,CVL.tipo
                              ,CVL.cod_lote
                              ,CVL.sequencia
                              ,sum( vl_lancamento ) AS vl_lancamento
                          FROM contabilidade.lancamento_empenho AS CLE
                    INNER JOIN contabilidade.valor_lancamento   AS CVL
                            ON CLE.exercicio    = CVL.exercicio
                           AND CLE.cod_entidade = CVL.cod_entidade
                           AND CLE.tipo         = CVL.tipo
                           AND CLE.cod_lote     = CVL.cod_lote
                           AND CLE.sequencia    = CVL.sequencia
                           AND CVL.tipo_valor = ''D''
                         WHERE NOT CLE.estorno
                      GROUP BY CVL.exercicio
                              ,CVL.cod_entidade
                              ,CVL.tipo
                              ,CVL.cod_lote
                              ,CVL.sequencia
                      ORDER BY CVL.exercicio
                              ,CVL.cod_entidade
                              ,CVL.tipo
                              ,CVL.cod_lote
                              ,CVL.sequencia
                      ) AS CVL
                   ON CP.exercicio    = CVL.exercicio
                  AND CP.cod_entidade = CVL.cod_entidade
                  AND CP.tipo         = CVL.tipo
                  AND CP.cod_lote     = CVL.cod_lote
                  AND CP.sequencia    = CVL.sequencia

            LEFT JOIN ( SELECT CVL.exercicio
                              ,CVL.cod_entidade
                              ,CVL.tipo
                              ,CVL.cod_lote
                              ,CVL.sequencia
                              ,sum( vl_lancamento ) AS vl_estornado
                          FROM contabilidade.lancamento_empenho AS CLE
                    INNER JOIN contabilidade.valor_lancamento   AS CVL
                            ON CLE.exercicio    = CVL.exercicio
                           AND CLE.cod_entidade = CVL.cod_entidade
                           AND CLE.tipo         = CVL.tipo
                           AND CLE.cod_lote     = CVL.cod_lote
                           AND CLE.sequencia    = CVL.sequencia
                           AND CVL.tipo_valor = ''D''
                         WHERE CLE.estorno
                      GROUP BY CVL.exercicio
                              ,CVL.cod_entidade
                              ,CVL.tipo
                              ,CVL.cod_lote
                              ,CVL.sequencia
                      ORDER BY CVL.exercicio
                              ,CVL.cod_entidade
                              ,CVL.tipo
                              ,CVL.cod_lote
                              ,CVL.sequencia
                      ) AS CVLE
                   ON CP.exercicio   = CVLE.exercicio
                  AND CP.cod_entidade = CVLE.cod_entidade
                  AND CP.tipo         = CVLE.tipo
                  AND CP.cod_lote     = CVLE.cod_lote
                  AND CP.sequencia    = CVLE.sequencia

                 WHERE to_char(CP.timestamp,''yyyy'')::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer

              GROUP BY TTP.cod_bordero,
                       TTP.cod_entidade,
                       TTP.exercicio,
                       tclc.cod_plano,
                       tclc.mes,
                       tclc.exercicio_conciliacao,
                       conciliacao.dt_extrato,
                       conciliacao.timestamp,
                       tclc.cod_tipo_conciliacao,
                       documento.cod_tipo,
                       documento.num_documento,
                       cod_conciliacao
             ) AS TTP
          ON TTP.cod_bordero    = TB.cod_bordero
         AND TTP.cod_entidade   = TB.cod_entidade
         AND TTP.exercicio      = TB.exercicio

  INNER JOIN contabilidade.plano_analitica
          ON TB.cod_plano     = plano_analitica.cod_plano
         AND TB.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta = plano_conta.cod_conta
         AND plano_analitica.exercicio = plano_conta.exercicio

       WHERE TB.exercicio::integer BETWEEN '''||stExercicio||'''::integer-1 AND '''||stExercicio||'''::integer
         AND TB.cod_plano             = '||reRegistroPlano.cod_plano||'
         AND TB.cod_entidade          in ( '||stCodEntidade||' )
         AND TTP.exercicio_conciliacao = '''||stExercicio||'''
         AND TTP.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy''),''mm'')::integer
         AND TTP.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy''),''mm'')::integer

    UNION

    --- MANUAL
      SELECT conciliacao.exercicio
           , conciliacao.mes
           , conciliacao.exercicio||LPAD(conciliacao.mes::VARCHAR,2, ''0'') AS competencia
           , plano_conta.cod_estrutural
           , lm.cod_tipo_conciliacao
           , lm.descricao
           , conciliacao.dt_extrato
           , conciliacao.timestamp
           , lm.vl_lancamento
           , 1 AS cod_tipo_pagamento
           , ''11111111'' AS num_documento
           , lm.cod_plano
           , tcmba.fn_cod_concilia( lm.exercicio
                                  , lm.mes
                                  ,   lm.exercicio
                                    ||lm.mes
                                    ||lm.sequencia
                                    ||''M''
                                    ||lm.cod_plano
                                    ||REPLACE(REPLACE(REPLACE(REPLACE(TRIM(conciliacao.timestamp::TEXT), ''.'',''''), '':'',''''), ''-'',''''), '' '','''')
                                  , TRUE
                                  )
             AS cod_conciliacao

        FROM tcmba.conciliacao_lancamento_manual AS lm

  INNER JOIN tesouraria.conciliacao
          ON lm.cod_plano = conciliacao.cod_plano
         AND lm.exercicio = conciliacao.exercicio
         AND lm.mes       = conciliacao.mes

  INNER JOIN contabilidade.plano_analitica
          ON conciliacao.cod_plano     = plano_analitica.cod_plano
         AND conciliacao.exercicio     = plano_analitica.exercicio

  INNER JOIN contabilidade.plano_conta
          ON plano_analitica.cod_conta     = plano_conta.cod_conta
         AND plano_analitica.exercicio     = plano_conta.exercicio

       WHERE lm.cod_plano = '||reRegistroPlano.cod_plano||'
         AND lm.exercicio = '''||stExercicio||'''
         AND lm.mes >= TO_CHAR(TO_DATE( '''||stDtInicial||'''::VARCHAR, ''dd/mm/yyyy''),''mm'')::integer
         AND lm.mes <= TO_CHAR(TO_DATE( '''||stDtFinal||'''::VARCHAR, ''dd/mm/yyyy''),''mm'')::integer
         AND NOT lm.conciliado
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP

        INSERT
          INTO tmp_conciliacao_plano
        VALUES ( reRegistro.exercicio
               , reRegistro.competencia
               , reRegistro.cod_estrutural
               , reRegistro.cod_tipo_conciliacao
               , reRegistro.descricao
               , reRegistro.dt_extrato
               , reRegistro.timestamp
               , reRegistro.vl_lancamento
               , reRegistro.cod_tipo_pagamento
               , reRegistro.num_documento
               , reRegistro.cod_plano
               , reRegistro.cod_conciliacao
               );

        UPDATE tcmba.arquivo_concilia
           SET descricao = reRegistro.descricao
             , valor     = reRegistro.vl_lancamento
         WHERE exercicio       = reRegistro.exercicio
           AND mes             = reRegistro.mes
           AND cod_conciliacao = reRegistro.cod_conciliacao;

    END LOOP;

    DROP TABLE tmp_deducao;
    DROP TABLE tmp_deducao_estornada;
    DROP TABLE tmp_arrecadacao_estornada;
    DROP TABLE tmp_arrecadacao;
    DROP TABLE tmp_estorno_arrecadacao;
END LOOP;

stSql := 'SELECT * FROM tmp_conciliacao_plano;';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_conciliacao_plano;

END;

$$ LANGUAGE 'plpgsql';


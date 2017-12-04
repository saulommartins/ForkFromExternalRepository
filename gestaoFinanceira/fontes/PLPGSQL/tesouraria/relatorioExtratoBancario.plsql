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
* $Id: relatorioExtratoBancario.plsql 64978 2016-04-18 14:43:54Z michel $
*
* Casos de uso: uc-02.04.10
*/

CREATE OR REPLACE FUNCTION tesouraria.fn_relatorio_extrato_bancario(integer, varchar, varchar, varchar,varchar, boolean) RETURNS SETOF RECORD AS '
DECLARE
    inCodPlano          ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    stEntidade          ALIAS FOR $3;
    stDtInicial         ALIAS FOR $4;
    stDtFinal           ALIAS FOR $5;
    boTCEMS             ALIAS FOR $6;

    boTabela            BOOLEAN;

    stSql               VARCHAR   := '''';
    stAuxA              VARCHAR   := '''';
    stAuxAE             VARCHAR   := '''';
    stAuxP              VARCHAR   := '''';
    stAuxPE             VARCHAR   := '''';
    stAuxT              VARCHAR   := '''';
    stAuxTE             VARCHAR   := '''';
    stAuxBor            VARCHAR   := '''';

    reRegistro          RECORD;

BEGIN

SELECT tesouraria.fn_listar_arrecadacao_conciliacao('''','''',stDtFinal,stDtInicial,inCodPlano, stEntidade ,stExercicio, boTCEMS) INTO boTabela;

IF (stDtInicial = stDtFinal ) THEN
    stAuxA  := '' AND tbl.dt_boletim = '''''' || stDtInicial || '''''' '';
    stAuxAE := '' AND to_date(TAE.dt_boletim, ''''dd/mm/yyyy'''' ) = TO_DATE('''''' || stDtInicial || '''''', ''''dd/mm/yyyy'''') '';
    stAuxP  := '' AND BOLETIM.dt_boletim = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stAuxPE := '' AND BOLETIM.dt_boletim = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stAuxT  := '' AND BOLETIM.dt_boletim = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stAuxTE := '' AND BOLETIM.dt_boletim = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
    stAuxBor:= '' AND BOLETIM.dt_boletim = to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') '';
ELSE
    stAuxA  := '' AND TO_DATE(tbl.dt_boletim, ''''dd/mm/yyyy'''') BETWEEN TO_DATE(''''''||stDtInicial||'''''',''''dd/mm/yyyy'''')  AND TO_DATE('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stAuxAE := '' AND TO_DATE(TAE.dt_boletim, ''''dd/mm/yyyy'''') BETWEEN TO_DATE(''''''||stDtInicial||'''''',''''dd/mm/yyyy'''')  AND TO_DATE('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stAuxP  := '' AND BOLETIM.dt_boletim BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stAuxPE := '' AND BOLETIM.dt_boletim BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stAuxT  := '' AND BOLETIM.dt_boletim BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stAuxTE := '' AND BOLETIM.dt_boletim BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
    stAuxBor:= '' AND BOLETIM.dt_boletim BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''')  AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''') '';
END IF;

stSql := ''

-- BUSCA AS ARRECADAÇÕES

SELECT
    hora,
    data,
    descricao,
    valor,
    cod_lote,
    cod_arrecadacao,
    tipo_valor,
    situacao,
    cod_situacao
FROM (
    SELECT
        to_char(ta.timestamp_arrecadacao, ''''HH:mm:ss'''') AS hora,
        tbl.dt_boletim AS data,
        cast(
        CASE WHEN tbl.cod_historico = 926 THEN
            ''''Estorno de Arrecadação Receita Dedutora '''' || tbl.cod_receita || '''' - '''' || tbl.cod_entidade || ''''/'''' || tbl.exercicio
        ELSE
            CASE WHEN TRIM(tbl.numeracao) = '''''''' THEN
                        ''''Arrecadação '''' || tbl.cod_receita || '''' - '''' || tbl.cod_entidade || ''''/'''' || tbl.exercicio
                 ELSE
                        ''''Arrecadação '''' || tbl.numeracao || '''' - '''' || tbl.cod_entidade || ''''/'''' || tbl.exercicio
            END
        END
        || '''' - '''' || replace(trim(substring(coalesce(TA.observacao,''''''''),1,60)),''''\r\n'''','''''''') AS varchar) AS descricao,
        cast(CASE WHEN tbl.numeracao = '''''''' THEN
            tbl.valor
        ELSE
            (tbl.valor*(-1))
        END as numeric) AS valor,
        cast(0 as numeric) AS cod_lote,
        cast(ta.cod_arrecadacao as numeric) AS cod_arrecadacao,
        cast(''''D'''' as varchar) AS tipo_valor,
        cast(1 as varchar) AS situacao,
        1::varchar AS cod_situacao
    FROM
        tmp_arrecadacao AS tbl
       ,tesouraria.arrecadacao AS TA
    WHERE
        tbl.exercicio               = TA.exercicio              AND
        tbl.cod_arrecadacao         = TA.cod_arrecadacao        AND
        tbl.timestamp_arrecadacao   = TA.timestamp_arrecadacao  AND
        tbl.cod_entidade            = TA.cod_entidade           AND

        tbl.exercicio               = '''''' || stExercicio || ''''''  AND
        tbl.conta_debito            = '' || inCodPlano || ''           AND
        tbl.cod_entidade            IN ('' || stEntidade || '')
        '' || stAuxA || ''
UNION ALL

-- BUSCA OS ESTORNOS DE ARRECADAÇÕES

    SELECT
        to_char(ta.timestamp_arrecadacao, ''''HH:mm:ss'''') as hora,
        TAE.dt_boletim AS data,
        CASE WHEN TAE.cod_historico = 925 THEN
                    ''''Arrecadação Receita Dedutora '''' || TAE.cod_receita || '''' - '''' || TAE.cod_entidade || ''''/'''' || TAE.exercicio
             WHEN TRIM(TAE.numeracao) =  '''''''' THEN
                    ''''Estorno de Arrecadação '''' || TAE.cod_receita || '''' - '''' || TAE.cod_entidade || ''''/'''' || TAE.exercicio
        ELSE
            ''''Estorno de Arrecadação '''' || TAE.numeracao || '''' - '''' || TAE.cod_entidade || ''''/'''' || TAE.exercicio
        END || '''' - '''' || replace(trim(substring(coalesce(TA.observacao,''''''''),1,60)),''''\r\n'''','''''''') AS descricao,
        cast(CASE WHEN TAE.numeracao = '''''''' THEN
            (TAE.valor*(-1))
        ELSE
            TAE.valor
        END as numeric) AS valor,
        cast(0 as numeric) as cod_lote,
        cast(ta.cod_arrecadacao as numeric) as cod_arrecadacao,
        cast(''''C'''' as varchar) as tipo_valor,
        cast(''''2'''' as varchar) as situacao,
        1::varchar as cod_situacao
    FROM
        tmp_arrecadacao_estornada as TAE,
        tesouraria.arrecadacao as TA
    WHERE
        TAE.exercicio               = TA.exercicio              AND
        TAE.cod_arrecadacao         = TA.cod_arrecadacao        AND
        TAE.timestamp_arrecadacao   = TA.timestamp_arrecadacao  AND
        TAE.cod_entidade            = TA.cod_entidade           AND
        TAE.exercicio               = '''''' || stExercicio || ''''''   AND
        TAE.conta_credito           = '' || inCodPlano || ''            AND
        TAE.cod_entidade            IN ('' || stEntidade || '')
        '' || stAuxAE || ''
UNION ALL

-- BUSCA OS PAGAMENTOS

    SELECT
        to_char(p.timestamp, ''''HH:mm:ss'''') as hora,
        to_char(BOLETIM.dt_boletim, ''''dd/mm/yyyy'''') as data,
        cast(
        CASE WHEN TRIM(substring(ENLP.observacao,1,60)) =  '''''''' THEN
            CASE WHEN (ENL.exercicio_empenho < P.exercicio_boletim) THEN
                 ''''Pagamento de RP n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            ELSE ''''Pagamento de Empenho n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            END
        ELSE
            CASE WHEN (ENL.exercicio_empenho < P.exercicio_boletim) THEN
                 ''''Pagamento de RP n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || replace(trim(substring(coalesce(ENLP.observacao,''''''''),1,60)),''''\r\n'''','''''''') || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            ELSE ''''Pagamento de Empenho n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || replace(trim(substring(coalesce(ENLP.observacao,''''''''),1,60)),''''\r\n'''','''''''') || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            END
        END  as varchar) 
        || CASE WHEN (cheque_emissao_ordem_pagamento.num_cheque IS NOT NULL) 
                THEN '''' CH '''' || cheque_emissao_ordem_pagamento.num_cheque
                ELSE '''' ''''
           END
        as descricao,
        ENLP.vl_pago*(-1) as valor,
        cast(0 as numeric) as cod_lote,
        cast(0 as numeric) as cod_arrecadacao,
        cast('''''''' as varchar) as tipo_valor,
        cast(''''3'''' as varchar) as situacao,
        1::varchar as cod_situacao
    FROM
        tesouraria.boletim as BOLETIM,
        tesouraria.pagamento as P,
        empenho.pagamento_liquidacao as EPL
LEFT JOIN tesouraria.cheque_emissao_ordem_pagamento
       ON cheque_emissao_ordem_pagamento.cod_ordem    = EPL.cod_ordem
      AND cheque_emissao_ordem_pagamento.exercicio    = EPL.exercicio
      AND cheque_emissao_ordem_pagamento.cod_entidade = EPL.cod_entidade
      AND cheque_emissao_ordem_pagamento.timestamp_emissao = ( SELECT MAX(timestamp_emissao) 
                                                                 FROM tesouraria.cheque_emissao_ordem_pagamento
                                                                WHERE cod_ordem    = EPL.cod_ordem
                                                                  AND exercicio    = EPL.exercicio
                                                                  AND cod_entidade = EPL.cod_entidade ),
        empenho.pagamento_liquidacao_nota_liquidacao_paga as EPLNLP,
        empenho.nota_liquidacao_paga                      as ENLP,
        empenho.nota_liquidacao                           as ENL,
        empenho.empenho                                   as EE,
        empenho.pre_empenho                               as EPE,
        sw_cgm                                            as cgm
    WHERE
            BOLETIM.cod_boletim         = P.cod_boletim
        AND BOLETIM.exercicio           = P.exercicio_boletim
        AND BOLETIM.cod_entidade        = P.cod_entidade

        AND P.cod_nota                  = ENLP.cod_nota
        AND P.exercicio                 = ENLP.exercicio
        AND P.cod_entidade              = ENLP.cod_entidade
        AND P.timestamp                 = ENLP.timestamp

        AND ENLP.cod_nota               = ENL.cod_nota
        AND ENLP.exercicio              = ENL.exercicio
        AND ENLP.cod_entidade           = ENL.cod_entidade

        AND EPL.cod_ordem               = EPLNLP.cod_ordem
        AND EPL.exercicio               = EPLNLP.exercicio
        AND EPL.cod_entidade            = EPLNLP.cod_entidade
        AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
        AND EPL.cod_nota                = EPLNLP.cod_nota

        AND EPLNLP.exercicio_liquidacao = ENLP.exercicio
        AND EPLNLP.cod_nota             = ENLP.cod_nota
        AND EPLNLP.cod_entidade         = ENLP.cod_entidade
        AND EPLNLP.timestamp            = ENLP.timestamp
        AND P.cod_plano       =  '' || inCodPlano || ''
        
        AND ENL.exercicio_empenho       = EE.exercicio
        AND ENL.cod_entidade            = EE.cod_entidade
        AND ENL.cod_empenho             = EE.cod_empenho
        
        AND EE.exercicio                = EPE.exercicio
        AND EE.cod_pre_empenho          = EPE.cod_pre_empenho
        
        AND EPE.cgm_beneficiario        = cgm.numcgm

        AND to_char(P.timestamp,''''yyyy'''')   = '''''' || stExercicio || ''''''
        AND P.cod_entidade          IN ('' || stEntidade || '')
            '' || stAuxP || ''
UNION ALL

-- BUSCA OS ESTORNOS DE PAGAMENTOS

    SELECT
        to_char(PE.timestamp, ''''HH:mm:ss'''') as hora,
        to_char(BOLETIM.dt_boletim, ''''dd/mm/yyyy'''') as data,
        cast(
        CASE WHEN TRIM(substring(ENLP.observacao,1,60)) =  '''''''' THEN
            CASE WHEN (ENL.exercicio_empenho < P.exercicio_boletim) THEN
                 ''''Estorno de Pagamento de RP n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            ELSE ''''Estorno de Pagamento de Empenho n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            END
        ELSE
            CASE WHEN (ENL.exercicio_empenho < P.exercicio_boletim) THEN
                 ''''Estorno de Pagamento de RP n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || replace(trim(substring(coalesce(ENLP.observacao,''''''''),1,60)),''''\r\n'''','''''''') || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            ELSE ''''Estorno de Pagamento de Empenho n° '''' || ENL.cod_empenho || ''''/'''' || ENL.exercicio_empenho || '''' - '''' || replace(trim(substring(coalesce(ENLP.observacao,''''''''),1,60)),''''\r\n'''','''''''') || '''' - '''' || ''''Credor: '''' || EPE.cgm_beneficiario || '''' - '''' || cgm.nom_cgm
            END
        END  as varchar)
        || CASE WHEN (cheque_emissao_ordem_pagamento.num_cheque IS NOT NULL) THEN
                '''' CH '''' || cheque_emissao_ordem_pagamento.num_cheque
           ELSE
                ''''''''
           END
        as descricao,
        ENLPA.vl_anulado as valor,
        cast(0 as numeric) as cod_lote,
        cast(0 as numeric) as cod_arrecadacao,
        cast('''''''' as varchar) as tipo_valor,
        cast(''''4'''' as varchar) as situacao,
        1::varchar as cod_situacao
    FROM
        tesouraria.boletim             as BOLETIM,
        tesouraria.pagamento_estornado as PE,
        tesouraria.pagamento           as P,
        empenho.pagamento_liquidacao as EPL
LEFT JOIN tesouraria.cheque_emissao_ordem_pagamento
       ON cheque_emissao_ordem_pagamento.cod_ordem         = EPL.cod_ordem
      AND cheque_emissao_ordem_pagamento.exercicio         = EPL.exercicio
      AND cheque_emissao_ordem_pagamento.cod_entidade      = EPL.cod_entidade
      AND cheque_emissao_ordem_pagamento.timestamp_emissao = ( SELECT MAX(timestamp_emissao) 
                                                                 FROM tesouraria.cheque_emissao_ordem_pagamento
                                                                WHERE cod_ordem    = EPL.cod_ordem
                                                                  AND exercicio    = EPL.exercicio
                                                                  AND cod_entidade = EPL.cod_entidade ),
        empenho.pagamento_liquidacao_nota_liquidacao_paga as EPLNLP,
        empenho.nota_liquidacao_paga                      as ENLP,
        empenho.nota_liquidacao_paga_anulada              as ENLPA,
        empenho.nota_liquidacao                           as ENL,
        empenho.empenho                                   as EE,
        empenho.pre_empenho                               as EPE,
        sw_cgm                                            as cgm
    WHERE
            BOLETIM.cod_boletim         = PE.cod_boletim
        AND BOLETIM.exercicio           = PE.exercicio_boletim
        AND BOLETIM.cod_entidade        = PE.cod_entidade

        AND PE.cod_nota                 = P.cod_nota
        AND PE.exercicio                = P.exercicio
        AND PE.cod_entidade             = P.cod_entidade
        AND PE.timestamp                = P.timestamp

        AND PE.cod_nota                 = ENLPA.cod_nota
        AND PE.exercicio                = ENLPA.exercicio
        AND PE.cod_entidade             = ENLPA.cod_entidade
        AND PE.timestamp_anulado        = ENLPA.timestamp_anulada

        AND ENLPA.cod_nota               = ENLP.cod_nota
        AND ENLPA.exercicio              = ENLP.exercicio
        AND ENLPA.cod_entidade           = ENLP.cod_entidade
        AND ENLPA.timestamp              = ENLP.timestamp

        AND ENLP.cod_nota               = ENL.cod_nota
        AND ENLP.exercicio              = ENL.exercicio
        AND ENLP.cod_entidade           = ENL.cod_entidade

        AND EPL.cod_ordem               = EPLNLP.cod_ordem
        AND EPL.exercicio               = EPLNLP.exercicio
        AND EPL.cod_entidade            = EPLNLP.cod_entidade
        AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
        AND EPL.cod_nota                = EPLNLP.cod_nota

        AND EPLNLP.exercicio_liquidacao = ENLP.exercicio
        AND EPLNLP.cod_nota             = ENLP.cod_nota
        AND EPLNLP.cod_entidade         = ENLP.cod_entidade
        AND EPLNLP.timestamp            = ENLP.timestamp

        AND ENLP.cod_nota                 = P.cod_nota
        AND ENLP.exercicio                = P.exercicio
        AND ENLP.cod_entidade             = P.cod_entidade
        AND ENLP.timestamp                = P.timestamp

        AND ENL.exercicio_empenho       = EE.exercicio
        AND ENL.cod_entidade            = EE.cod_entidade
        AND ENL.cod_empenho             = EE.cod_empenho
        
        AND EE.exercicio                = EPE.exercicio
        AND EE.cod_pre_empenho          = EPE.cod_pre_empenho
        
        AND EPE.cgm_beneficiario        = cgm.numcgm

        AND P.cod_plano       =  '' || inCodPlano || ''
        AND to_char(PE.timestamp_anulado,''''yyyy'''')   = '''''' || stExercicio || ''''''
        AND PE.cod_entidade     IN ('' || stEntidade || '')
            '' || stAuxPE || ''
UNION ALL

-- BUSCA AS ARRECADAÇÕES EXTRA, PAGAMENTOS EXTRA, DEPÓSITOS/RETIRADAS, APLICAÇÃO E  RESGATES ( DEBITO )

    SELECT
        to_char(T.timestamp_transferencia, ''''HH:mm:ss'''') as hora,
        to_char(BOLETIM.dt_boletim, ''''dd/mm/yyyy'''') as data,
        CASE WHEN T.observacao != ''''''''
          THEN trim(TTT.descricao || '''' - CD: ''''||T.cod_plano_debito ||'''' | CC: '''' || T.cod_plano_credito || '''' - '''' || replace(trim(substring(coalesce(T.observacao,''''''''),1,60)),''''\r\n'''',''''''''))
          ELSE trim(TTT.descricao || '''' - CD: ''''||T.cod_plano_debito ||'''' | CC: '''' || T.cod_plano_credito)
        END as descricao,
        T.valor as valor,
        cast(0 as numeric) as cod_lote,
        cast(0 as numeric) as cod_arrecadacao,
        cast('''''''' as varchar) as tipo_valor,
        cast(''''5'''' as varchar) as situacao,
        1::varchar as cod_situacao
    FROM
        tesouraria.boletim              as BOLETIM,
        tesouraria.transferencia        as T,
        tesouraria.tipo_transferencia   as TTT

    WHERE
        TTT.cod_tipo          = T.cod_tipo        AND

        BOLETIM.cod_boletim   = T.cod_boletim     AND
        BOLETIM.exercicio     = T.exercicio       AND
        BOLETIM.cod_entidade  = T.cod_entidade    AND

        T.cod_plano_debito    = '' || inCodPlano || ''          AND
        T.exercicio           = '''''' || stExercicio || '''''' AND
        T.cod_entidade        IN ('' || stEntidade || '')
        '' || stAuxT || ''
UNION ALL

-- BUSCA AS ARRECADAÇÕES EXTRA, PAGAMENTOS EXTRA, DEPÓSITOS/RETIRADAS, APLICAÇÃO E  RESGATES ( CRÉDITO )

    SELECT
        to_char(T.timestamp_transferencia, ''''HH:mm:ss'''') as hora,
        to_char(BOLETIM.dt_boletim, ''''dd/mm/yyyy'''') as data,
        CASE WHEN T.observacao != ''''''''
          THEN trim(TTT.descricao || '''' - CD: ''''||T.cod_plano_debito ||'''' | CC: '''' || T.cod_plano_credito || '''' - '''' || replace(trim(substring(coalesce(T.observacao,''''''''),1,60)),''''\r\n'''',''''''''))
          ELSE trim(TTT.descricao || '''' - CD: ''''||T.cod_plano_debito ||'''' | CC: '''' || T.cod_plano_credito)
        END as descricao,
        T.valor * (-1) as valor,
        cast(0 as numeric) as cod_lote,
        cast(0 as numeric) as cod_arrecadacao,
        cast('''''''' as varchar) as tipo_valor,
        cast(''''6'''' as varchar) as situacao,
        cast(''''1'''' as varchar) as cod_situacao
    FROM
        tesouraria.boletim                  as BOLETIM,
        tesouraria.transferencia            as T,
        tesouraria.tipo_transferencia       as TTT

    WHERE
        TTT.cod_tipo          = T.cod_tipo       AND

        BOLETIM.cod_boletim   = T.cod_boletim    AND
        BOLETIM.exercicio     = T.exercicio      AND
        BOLETIM.cod_entidade  = T.cod_entidade   AND

        T.cod_plano_credito   =  '' || inCodPlano || ''           AND
        T.exercicio           = '''''' || stExercicio || ''''''   AND
        T.cod_entidade       IN ('' || stEntidade || '')
        '' || stAuxTE || ''

UNION ALL

-- ESTORNO DE ARRECACADAO EXTRA

    SELECT
        to_char(TE.timestamp_estornada, ''''HH:mm:ss'''')   as hora,
        to_char(BOLETIM.dt_boletim, ''''dd/mm/yyyy'''')     as data,
        CASE WHEN T.observacao != ''''''''
          THEN trim('''' Estorno de '''' || TTT.descricao || '''' - CD: ''''||T.cod_plano_credito ||'''' | CC: '''' || T.cod_plano_debito || '''' - '''' || replace(trim(substring(coalesce(T.observacao,''''''''),1,60)),''''\r\n'''',''''''''))
          ELSE trim('''' Estorno de '''' || TTT.descricao || '''' - CD: ''''||T.cod_plano_credito ||'''' | CC: '''' || T.cod_plano_debito)
        END as descricao,
        TE.valor * (-1) as valor,
        cast(0 as numeric) as cod_lote,
        cast(0 as numeric) as cod_arrecadacao,
        cast('''''''' as varchar) as tipo_valor,
        cast(''''X'''' as varchar) as situacao,
        cast(''''1'''' as varchar) as cod_situacao
    FROM
        tesouraria.boletim                  as BOLETIM,
        tesouraria.transferencia_estornada  as TE,
        tesouraria.transferencia            as T,
        tesouraria.tipo_transferencia       as TTT
    WHERE
        TE.exercicio          = T.exercicio     AND
        TE.cod_entidade       = T.cod_entidade  AND
        TE.cod_lote           = T.cod_lote      AND
        TE.tipo               = T.tipo          AND

        TTT.cod_tipo          = T.cod_tipo        AND

        BOLETIM.cod_boletim   = TE.cod_boletim     AND
        BOLETIM.exercicio     = TE.exercicio       AND
        BOLETIM.cod_entidade  = TE.cod_entidade    AND

        T.cod_plano_debito    =  '' || inCodPlano || ''           AND
        TE.exercicio          = '''''' || stExercicio || ''''''   AND
        TE.cod_entidade       IN ('' || stEntidade || '')
        '' || stAuxTE || ''
UNION ALL

-- ESTORNO DE PAGAMENTO EXTRA

    SELECT
        to_char(TE.timestamp_estornada, ''''HH:mm:ss'''')   as hora,
        to_char(BOLETIM.dt_boletim, ''''dd/mm/yyyy'''')     as data,
        CASE WHEN T.observacao != ''''''''
          THEN trim('''' Estorno de '''' || TTT.descricao || '''' - CD: ''''||T.cod_plano_credito ||'''' | CC: '''' || T.cod_plano_debito || '''' - '''' || replace(trim(substring(coalesce(T.observacao,''''''''),1,60)),''''\r\n'''',''''''''))
          ELSE trim('''' Estorno de '''' || TTT.descricao || '''' - CD: ''''||T.cod_plano_credito ||'''' | CC: '''' || T.cod_plano_debito)
        END as descricao,
        TE.valor as valor,
        cast(0 as numeric) as cod_lote,
        cast(0 as numeric) as cod_arrecadacao,
        cast('''''''' as varchar) as tipo_valor,
        cast(''''X'''' as varchar) as situacao,
        cast(''''2'''' as varchar) as cod_situacao
    FROM
        tesouraria.boletim                  as BOLETIM,
        tesouraria.transferencia_estornada  as TE,
        tesouraria.transferencia            as T,
        tesouraria.tipo_transferencia       as TTT
    WHERE
        TE.exercicio          = T.exercicio     AND
        TE.cod_entidade       = T.cod_entidade  AND
        TE.cod_lote           = T.cod_lote      AND
        TE.tipo               = T.tipo          AND

        TTT.cod_tipo          = T.cod_tipo        AND

        BOLETIM.cod_boletim   = TE.cod_boletim     AND
        BOLETIM.exercicio     = TE.exercicio       AND
        BOLETIM.cod_entidade  = TE.cod_entidade    AND

        T.cod_plano_credito    =  '' || inCodPlano || ''           AND
        TE.exercicio          = '''''' || stExercicio || ''''''   AND
        TE.cod_entidade       IN ('' || stEntidade || '')
        '' || stAuxTE || ''
UNION ALL
    SELECT
        to_char(TB.timestamp_bordero, ''''HH:mm:ss'''') as hora,
        to_char(BOLETIM.dt_boletim, ''''dd/mm/yyyy'''') as data,
        ''''Pagamento de Bordero '''' || TB.cod_bordero || ''''/'''' || TB.exercicio || '''' - OP - '''' || tesouraria.retorna_OPs(TTP.exercicio,TTP.cod_bordero,TTP.cod_entidade) as descricao,
        TTP.vl_pagamento*(-1) as valor,
        cast(0 as numeric) as cod_lote,
        cast(0 as numeric) as cod_arrecadacao,
        cast('''''''' as varchar) as tipo_valor,
        cast(''''7'''' as varchar) as situacao,
        cast(''''1'''' as varchar) as cod_situacao
    FROM
        tesouraria.boletim AS BOLETIM
        INNER JOIN tesouraria.bordero AS TB  ON (
                TB.cod_boletim       = BOLETIM.cod_boletim
            AND TB.cod_entidade      = BOLETIM.cod_entidade
            AND TB.exercicio_boletim = BOLETIM.exercicio
        )
        LEFT JOIN (
            SELECT
                TTP.cod_bordero,
                TTP.cod_entidade,
                TTP.exercicio,
                sum(CVL.vl_lancamento) - coalesce( sum( CVLE.vl_estornado ), 0.00 ) AS vl_pagamento
            FROM
                 tesouraria.transacoes_pagamento  AS TTP
                ,empenho.ordem_pagamento          AS EOP
                ,empenho.pagamento_liquidacao     AS EPL
                ,empenho.nota_liquidacao          AS ENL
                ,empenho.empenho                  AS EE
                ,empenho.pre_empenho              AS EPE
                ,empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP
                ,empenho.nota_liquidacao_paga                      AS ENLP
                ,contabilidade.pagamento                           AS CP
                LEFT JOIN( SELECT CVL.exercicio
                                 ,CVL.cod_entidade
                                 ,CVL.tipo
                                 ,CVL.cod_lote
                                 ,CVL.sequencia
                                 ,sum( vl_lancamento ) as vl_lancamento
                           FROM contabilidade.lancamento_empenho AS CLE
                               ,contabilidade.valor_lancamento   AS CVL
                           WHERE CLE.exercicio    = CVL.exercicio
                             AND CLE.cod_entidade = CVL.cod_entidade
                             AND CLE.tipo         = CVL.tipo
                             AND CLE.cod_lote     = CVL.cod_lote
                             AND CLE.sequencia    = CVL.sequencia
                             AND NOT CLE.estorno
                             AND CVL.tipo_valor = ''''D''''
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
                ) AS CVL  ON( CP.exercicio    = CVL.exercicio
                          AND CP.cod_entidade = CVL.cod_entidade
                          AND CP.tipo         = CVL.tipo
                          AND CP.cod_lote     = CVL.cod_lote
                          AND CP.sequencia    = CVL.sequencia       )
                LEFT JOIN( SELECT CVL.exercicio
                                 ,CVL.cod_entidade
                                 ,CVL.tipo
                                 ,CVL.cod_lote
                                 ,CVL.sequencia
                                 ,sum( vl_lancamento ) as vl_estornado
                           FROM contabilidade.lancamento_empenho AS CLE
                               ,contabilidade.valor_lancamento   AS CVL
                           WHERE CLE.exercicio    = CVL.exercicio
                             AND CLE.cod_entidade = CVL.cod_entidade
                             AND CLE.tipo         = CVL.tipo
                             AND CLE.cod_lote     = CVL.cod_lote
                             AND CLE.sequencia    = CVL.sequencia
                             AND CLE.estorno
                             AND CVL.tipo_valor = ''''D''''
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
                ) AS CVLE ON( CP.exercicio   = CVLE.exercicio
                          AND CP.cod_entidade = CVLE.cod_entidade
                          AND CP.tipo         = CVLE.tipo
                          AND CP.cod_lote     = CVLE.cod_lote
                          AND CP.sequencia    = CVLE.sequencia       )
            WHERE
                TTP.cod_ordem               = EOP.cod_ordem
            AND TTP.cod_entidade            = EOP.cod_entidade
            AND TTP.exercicio               = EOP.exercicio

            AND EOP.cod_ordem               = EPL.cod_ordem
            AND EOP.cod_entidade            = EPL.cod_entidade
            AND EOP.exercicio               = EPL.exercicio

            AND EPL.exercicio_liquidacao    = ENL.exercicio
            AND EPL.cod_nota                = ENL.cod_nota
            AND EPL.cod_entidade            = ENL.cod_entidade

            AND ENL.exercicio_empenho       = EE.exercicio
            AND ENL.cod_empenho             = EE.cod_empenho
            AND ENL.cod_entidade            = EE.cod_entidade

            AND EE.cod_pre_empenho          = EPE.cod_pre_empenho
            AND EE.exercicio                = EPE.exercicio

            AND EPL.exercicio_liquidacao    = EPLNLP.exercicio_liquidacao
            AND EPL.cod_nota                = EPLNLP.cod_nota
            AND EPL.cod_entidade            = EPLNLP.cod_entidade
            AND EPL.cod_ordem               = EPLNLP.cod_ordem
            AND EPL.exercicio               = EPLNLP.exercicio

            AND EPLNLP.exercicio_liquidacao = ENLP.exercicio
            AND EPLNLP.cod_nota             = ENLP.cod_nota
            AND EPLNLP.cod_entidade         = ENLP.cod_entidade
            AND EPLNLP.timestamp            = ENLP.timestamp

            AND ENLP.exercicio              = CP.exercicio_liquidacao
            AND ENLP.cod_nota               = CP.cod_nota
            AND ENLP.cod_entidade           = CP.cod_entidade
            AND ENLP.timestamp              = CP.timestamp
            GROUP BY
                TTP.cod_bordero,
                TTP.cod_entidade,
                TTP.exercicio
        )AS TTP ON (
                    TTP.cod_bordero    = TB.cod_bordero
            AND     TTP.cod_entidade   = TB.cod_entidade
            AND     TTP.exercicio      = TB.exercicio
        )
    WHERE
            TB.exercicio             = '''''' || stExercicio || ''''''
        AND TB.cod_plano             = '' || inCodPlano || ''
        AND TB.cod_entidade          IN ( '' || stEntidade || '' )
            '' || stAuxBor || ''

) as tabela ORDER BY TO_DATE( tabela.data, ''''dd/mm/yyyy'''' ), tabela.hora
'';
--RAISE NOTICE ''Debug: %'', stSql;
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_arrecadacao;
    DROP TABLE tmp_arrecadacao_estornada;
    DROP TABLE tmp_estorno_arrecadacao;
    DROP TABLE tmp_deducao;
    DROP TABLE tmp_deducao_estornada;

RETURN;

END;

'language 'plpgsql';

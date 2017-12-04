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
* $Revision: 27605 $
* $Name$
* $Author: tonismar $
* $Date: 2008-01-17 15:30:30 -0200 (Qui, 17 Jan 2008) $
*
* Casos de uso: uc-02.02.27
*/

/*
$Log$
Revision 1.14  2007/08/02 17:51:04  tonismar
Bug#9801#

Revision 1.13  2007/06/20 12:44:23  vitor
Bug#8824#, Bug#8828#

Revision 1.12  2007/06/18 21:00:49  vitor
#8824# #8828# 

Revision 1.11  2007/06/07 14:41:54  vitor
#8824# #8828#

Revision 1.9  2007/06/06 18:57:38  vitor
#8828#

Revision 1.8  2007/05/24 14:36:32  vitor
#8828#

Revision 1.7  2007/01/09 15:41:45  cako
Bug #7287#

Revision 1.6  2006/12/08 18:50:28  cako
Bug #7778#

Revision 1.5  2006/11/10 18:55:31  cako
Bug #7261#

Revision 1.4  2006/09/14 16:38:39  jose.eduardo
Bug #6815#

Revision 1.3  2006/09/14 14:57:36  jose.eduardo
Bug #6832#

Revision 1.2  2006/08/24 14:22:32  jose.eduardo
Bug #6765#

Revision 1.1  2006/08/23 17:00:37  jose.eduardo
Bug #6765#


*/

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_razao_historico_completo(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio            ALIAS FOR $1;
    stFiltro               ALIAS FOR $2;
    stCodEstruturalInicial ALIAS FOR $3;
    stCodEstruturalFinal   ALIAS FOR $4;
    stDtInicial            ALIAS FOR $5;
    stDtFinal              ALIAS FOR $6;
    stCodEntidades         ALIAS FOR $7;
    dtInicialAnterior      ALIAS FOR $8;
    dtFinalAnterior        ALIAS FOR $9;
    boMovimentacaoConta    ALIAS FOR $10;
    stSql                  VARCHAR   := '''';
    reRegistro             RECORD;
    stAux                  VARCHAR   := '''';

BEGIN

stSql := '' CREATE TEMPORARY TABLE tmp_empenho AS (
    SELECT
         ce.*
        ,case when replace(trim(coalesce(pe.descricao,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            replace(trim(coalesce(pe.descricao,'''''''')),''''\r\n'''','''''''') || '''' - ''''
         end as descricao
        ,case when replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            ''''Credor: '''' || replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''')
         end as credor
    FROM
        empenho.pre_empenho as pe
        INNER JOIN sw_cgm as cgm ON (
            cgm.numcgm = pe.cgm_beneficiario
        )
        INNER JOIN empenho.empenho as e ON(
                e.cod_pre_empenho = pe.cod_pre_empenho
            AND e.exercicio       = pe.exercicio       
        )
        INNER JOIN contabilidade.empenhamento as ce ON (
                ce.cod_entidade         = e.cod_entidade   
            AND ce.exercicio_empenho    = e.exercicio       
            AND ce.cod_empenho          = e.cod_empenho    
        )
        INNER JOIN contabilidade.lancamento_empenho as le ON (
                le.cod_entidade         = ce.cod_entidade   
            AND le.exercicio            = ce.exercicio       
            AND le.cod_lote             = ce.cod_lote       
            AND le.tipo                 = ce.tipo           
            AND le.sequencia            = ce.sequencia      
            AND NOT le.estorno
        )
    WHERE
        e.exercicio = '''''' || stExercicio || ''''''
    AND e.cod_entidade   IN ('' || stCodEntidades || '')

    UNION

    SELECT
         ce.*
        ,case when replace(trim(coalesce(ea.motivo,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            replace(trim(coalesce(ea.motivo,'''''''')),''''\r\n'''','''''''') || '''' - '''' 
         end as descricao
        ,case when replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            ''''Credor: '''' || replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''')
         end as credor
    FROM
        empenho.pre_empenho as pe
        INNER JOIN sw_cgm as cgm ON (
            cgm.numcgm = pe.cgm_beneficiario
        )
        INNER JOIN empenho.empenho as e ON(
                e.cod_pre_empenho = pe.cod_pre_empenho
            AND e.exercicio       = pe.exercicio       
        )
        INNER JOIN empenho.empenho_anulado as ea ON(
                ea.cod_empenho     = e.cod_empenho
            AND ea.exercicio       = e.exercicio       
            AND ea.cod_entidade    = e.cod_entidade    
        )
        INNER JOIN contabilidade.empenhamento as ce ON (
                ce.cod_entidade         = ea.cod_entidade   
            AND ce.exercicio_empenho    = ea.exercicio       
            AND ce.cod_empenho          = ea.cod_empenho    
        )
        INNER JOIN contabilidade.lancamento_empenho as le ON (
                le.cod_entidade         = ce.cod_entidade   
            AND le.exercicio            = ce.exercicio       
            AND le.cod_lote             = ce.cod_lote       
            AND le.tipo                 = ce.tipo           
            AND le.sequencia            = ce.sequencia      
            AND le.estorno
        )
    WHERE
        e.exercicio = '''''' || stExercicio || ''''''
    AND e.cod_entidade   IN ('' || stCodEntidades || '')
)'';

EXECUTE stSql;


stSql := '' CREATE TEMPORARY TABLE tmp_liquidacao AS (
    SELECT
         cl.*
        ,case when replace(trim(coalesce(nl.observacao,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            replace(trim(coalesce(nl.observacao,'''''''')),''''\r\n'''','''''''') || '''' - '''' 
         end as descricao
        ,case when replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            ''''Credor: '''' || replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''') 
         end as credor
    FROM
        empenho.pre_empenho as pe
        INNER JOIN sw_cgm as cgm ON (
            cgm.numcgm = pe.cgm_beneficiario
        )
        INNER JOIN empenho.empenho as e ON(
                e.cod_pre_empenho = pe.cod_pre_empenho
            AND e.exercicio       = pe.exercicio       
        )
        INNER JOIN empenho.nota_liquidacao as nl ON(
                nl.cod_empenho       = e.cod_empenho    
            AND nl.exercicio_empenho = e.exercicio       
            AND nl.cod_entidade      = e.cod_entidade    
        )
        INNER JOIN contabilidade.liquidacao as cl ON (
                cl.cod_entidade         = nl.cod_entidade   
            AND cl.exercicio_liquidacao = nl.exercicio       
            AND cl.cod_nota             = nl.cod_nota       
        )
        INNER JOIN contabilidade.lancamento_empenho as le ON (
                le.cod_entidade         = cl.cod_entidade   
            AND le.exercicio            = cl.exercicio       
            AND le.cod_lote             = cl.cod_lote       
            AND le.tipo                 = cl.tipo           
            AND le.sequencia            = cl.sequencia      
            AND NOT le.estorno
        )
    WHERE
        nl.exercicio = '''''' || stExercicio || ''''''
    AND nl.cod_entidade   IN ('' || stCodEntidades || '')
)'';

EXECUTE stSql;


stSql := '' CREATE TEMPORARY TABLE tmp_pagamento AS (
    SELECT
         cp.*
        ,case when replace(trim(coalesce(nlp.observacao,'''''''')),''''\r\n'''','''''''') = '''''''' then 
            ''''''''
         else
            replace(trim(coalesce(nlp.observacao,'''''''')),''''\r\n'''','''''''') || '''' - ''''
         end as descricao
        ,case when replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''') = '''''''' then
            '''''''' 
         else
            ''''Credor: '''' || replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''')
         end as credor
    FROM
        empenho.pre_empenho as pe
        INNER JOIN sw_cgm as cgm ON (
            cgm.numcgm = pe.cgm_beneficiario
        )
        INNER JOIN empenho.empenho as e ON(
                e.cod_pre_empenho = pe.cod_pre_empenho
            AND e.exercicio       = pe.exercicio       
        )
        INNER JOIN empenho.nota_liquidacao as nl ON(
                nl.cod_empenho       = e.cod_empenho    
            AND nl.exercicio_empenho = e.exercicio       
            AND nl.cod_entidade      = e.cod_entidade    
        )
        INNER JOIN empenho.nota_liquidacao_paga as nlp ON(
                nlp.cod_nota          = nl.cod_nota       
            AND nlp.exercicio         = nl.exercicio       
            AND nlp.cod_entidade      = nl.cod_entidade    
        )
        INNER JOIN contabilidade.pagamento as cp ON (
                cp.cod_entidade         = nlp.cod_entidade   
            AND cp.exercicio_liquidacao = nlp.exercicio       
            AND cp.cod_nota             = nlp.cod_nota       
            AND cp.timestamp            = nlp.timestamp      
        )
        INNER JOIN contabilidade.lancamento_empenho as le ON (
                le.cod_entidade         = cp.cod_entidade   
            AND le.exercicio            = cp.exercicio       
            AND le.cod_lote             = cp.cod_lote       
            AND le.tipo                 = cp.tipo           
            AND le.sequencia            = cp.sequencia      
            AND NOT le.estorno
        )
    WHERE
        nlp.exercicio = '''''' || stExercicio || ''''''
    AND nlp.cod_entidade   IN ('' || stCodEntidades || '')

    UNION

    SELECT
         cp.*
        ,case when replace(trim(coalesce(nlpa.observacao,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            replace(trim(coalesce(nlpa.observacao,'''''''')),''''\r\n'''','''''''') || '''' - '''' 
         end as descricao
        ,case when replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''') = '''''''' then
            ''''''''
         else
            ''''Credor: '''' || replace(trim(coalesce(cgm.nom_cgm,'''''''')),''''\r\n'''','''''''') 
         end as credor
    FROM
        empenho.pre_empenho as pe
        INNER JOIN sw_cgm as cgm ON (
            cgm.numcgm = pe.cgm_beneficiario
        )
        INNER JOIN empenho.empenho as e ON(
                e.cod_pre_empenho = pe.cod_pre_empenho
            AND e.exercicio       = pe.exercicio       
        )
        INNER JOIN empenho.nota_liquidacao as nl ON(
                nl.cod_empenho       = e.cod_empenho    
            AND nl.exercicio_empenho = e.exercicio       
            AND nl.cod_entidade      = e.cod_entidade    
        )
        INNER JOIN empenho.nota_liquidacao_paga as nlp ON(
                nlp.cod_nota          = nl.cod_nota       
            AND nlp.exercicio         = nl.exercicio       
            AND nlp.cod_entidade      = nl.cod_entidade    
        )
        INNER JOIN empenho.nota_liquidacao_paga_anulada as nlpa ON(
                nlpa.cod_nota          = nlp.cod_nota       
            AND nlpa.exercicio         = nlp.exercicio       
            AND nlpa.cod_entidade      = nlp.cod_entidade    
            AND nlpa.timestamp         = nlp.timestamp       
        )
        INNER JOIN contabilidade.pagamento as cp ON (
                cp.cod_entidade         = nlpa.cod_entidade   
            AND cp.exercicio_liquidacao = nlpa.exercicio       
            AND cp.cod_nota             = nlpa.cod_nota       
            AND cp.timestamp            = nlpa.timestamp      
        )
        INNER JOIN contabilidade.lancamento_empenho as le ON (
                le.cod_entidade         = cp.cod_entidade   
            AND le.exercicio            = cp.exercicio       
            AND le.cod_lote             = cp.cod_lote       
            AND le.tipo                 = cp.tipo           
            AND le.sequencia            = cp.sequencia      
            AND le.estorno
        )
    WHERE
        nlp.exercicio = '''''' || stExercicio || ''''''
    AND nlp.cod_entidade   IN ('' || stCodEntidades || '')
)'';

EXECUTE stSql;

If dtInicialAnterior != dtFinalAnterior Then
   stAux := '' AND     lo.dt_lote BETWEEN to_date(''''''||dtInicialAnterior||'''''',''''dd/mm/yyyy'''') AND (to_date(''''''||dtFinalAnterior||'''''',''''dd/mm/yyyy'''')) '';
Else
   stAux := '' AND     lo.dt_lote = to_date(''''''||dtInicialAnterior||'''''',''''dd/mm/yyyy'''')  and lo.tipo = ''''I'''' '';
End If;

stSql := ''CREATE TEMPORARY TABLE tmp_valor AS (
    SELECT
        pc.cod_estrutural as cod_estrutural,
        pa.cod_plano as cod_plano,
        vl.vl_lancamento as vl_lancamento,
        vl.oid as oid_tmp
    FROM
         contabilidade.plano_conta      as pc
        ,contabilidade.plano_analitica  as pa
        ,contabilidade.conta_debito     as cd
        ,contabilidade.valor_lancamento as vl
        ,contabilidade.lote             as lo
    WHERE
            pc.cod_conta = pa.cod_conta
    AND     pc.exercicio = pa.exercicio
    AND     pc.cod_estrutural BETWEEN '''''' || stCodEstruturalInicial || '''''' AND '''''' || stCodEstruturalFinal || ''''''

    AND     pa.cod_plano = cd.cod_plano
    AND     pa.exercicio = cd.exercicio

    AND     cd.exercicio = '''''' || stExercicio || ''''''
    AND     cd.cod_entidade   IN ('' || stCodEntidades || '')
    AND     cd.tipo_valor       = ''''D''''

    AND     cd.cod_lote  = vl.cod_lote
    AND     cd.tipo      = vl.tipo
    AND     cd.sequencia = vl.sequencia
    AND     cd.exercicio = vl.exercicio
    AND     cd.tipo_valor= vl.tipo_valor
    AND     cd.cod_entidade= vl.cod_entidade

    AND     vl.cod_lote  = lo.cod_lote
    AND     vl.tipo      = lo.tipo
    AND     vl.exercicio = lo.exercicio
    AND     vl.cod_entidade= lo.cod_entidade '' || stAux  || stFiltro || ''

    UNION

    SELECT
        pc.cod_estrutural as cod_estrutural,
        pa.cod_plano as cod_plano,
        vl.vl_lancamento as vl_lancamento,
        vl.oid as oid_tmp
    FROM
         contabilidade.plano_conta      as pc
        ,contabilidade.plano_analitica  as pa
        ,contabilidade.conta_credito    as cc
        ,contabilidade.valor_lancamento as vl
        ,contabilidade.lote             as lo
    WHERE
            pc.cod_conta = pa.cod_conta
    AND     pc.exercicio = pa.exercicio
    AND     pc.cod_estrutural BETWEEN '''''' || stCodEstruturalInicial || '''''' AND '''''' || stCodEstruturalFinal || ''''''

    AND     pa.cod_plano = cc.cod_plano
    AND     pa.exercicio = cc.exercicio

    AND     cc.exercicio = '''''' || stExercicio || ''''''
    AND     cc.cod_entidade   IN ('' || stCodEntidades || '')
    AND     cc.tipo_valor       = ''''C''''

    AND     cc.cod_lote  = vl.cod_lote
    AND     cc.tipo      = vl.tipo
    AND     cc.sequencia = vl.sequencia
    AND     cc.exercicio = vl.exercicio
    AND     cc.tipo_valor= vl.tipo_valor
    AND     cc.cod_entidade= vl.cod_entidade

    AND     vl.cod_lote  = lo.cod_lote
    AND     vl.tipo      = lo.tipo
    AND     vl.exercicio = lo.exercicio
    AND     vl.cod_entidade= lo.cod_entidade '' || stAux || stFiltro || ''

)'';

EXECUTE stSql;

CREATE UNIQUE INDEX uq_valor ON tmp_valor (cod_estrutural varchar_pattern_ops, oid_tmp);

stSql := ''CREATE TEMPORARY TABLE tmp_somatorio AS (
    SELECT
        pc.cod_estrutural,
        contabilidade.fn_somatorio_razao(pc.cod_estrutural) as saldoAnterior
    FROM
        contabilidade.plano_conta      as pc,
        contabilidade.plano_analitica  as pa
    WHERE pc.exercicio = pa.exercicio
      AND pc.cod_conta = pa.cod_conta
      AND pc.exercicio = ''''''||stExercicio||''''''
      '' || stFiltro || ''
)'';

EXECUTE stSql;

CREATE UNIQUE INDEX uq_somatorio ON tmp_somatorio (cod_estrutural varchar_pattern_ops);

If stDtInicial != stDtFinal Then
   stAux := '' AND lo.dt_lote BETWEEN to_date(''''''||stDtInicial||'''''',''''dd/mm/yyyy'''') AND to_date(''''''||stDtFinal||'''''',''''dd/mm/yyyy'''') '';
Else
   stAux := '' AND lo.dt_lote =  to_date(''''''||stDtInicial||'''''',''''dd/mm/yyyy'''') '';
End If;

stSql := ''CREATE TEMPORARY TABLE tmp_razao AS (
   SELECT
        l.oid as oid_tmp,
        l.cod_lote,
        l.sequencia,
        l.cod_historico,
        l.complemento,
        l.exercicio,
        l.cod_entidade,
        l.tipo,
        hc.nom_historico,
        cast(CASE WHEN (tret.cod_recibo_extra IS NOT NULL) OR (tret2.cod_recibo_extra IS NOT NULL) OR (ret.cod_recibo_extra IS NOT NULL)
            THEN coalesce(cast(tret.cod_recibo_extra as varchar),''''''''||coalesce(cast(ret.cod_recibo_extra as varchar),''''''''))
            ELSE ''''''''
           END
         ||CASE WHEN ((tt.observacao IS NOT NULL) OR (tte.observacao IS NOT NULL)) AND (l.tipo != ''''A'''')
            THEN coalesce(tt.observacao, '''''''')||coalesce(tte.observacao,'''''''')
            ELSE ''''''''
            END
         ||CASE WHEN (tarrec.observacao IS NOT NULL)
            THEN coalesce(tarrec.observacao,'''''''')
            ELSE ''''''''
           END
        AS VARCHAR) as observacao,
        vl.vl_lancamento,
        vl.tipo_valor,
        lo.dt_lote,
        cd.cod_plano AS cod_plano
    FROM
          contabilidade.lancamento         AS  l,
          contabilidade.lote               AS lo
          LEFT JOIN tesouraria.transferencia AS tt ON (
                tt.cod_lote     = lo.cod_lote
            AND tt.exercicio    = lo.exercicio
            AND tt.tipo         = lo.tipo
            AND tt.cod_entidade = lo.cod_entidade
          )
          LEFT JOIN tesouraria.transferencia_estornada AS tte ON (
                tte.cod_lote_estorno = lo.cod_lote
            AND tte.exercicio        = lo.exercicio
            AND tte.tipo             = lo.tipo
            AND tte.cod_entidade     = lo.cod_entidade
          )
          LEFT JOIN tesouraria.recibo_extra_transferencia AS ret ON (
                ret.cod_lote         = lo.cod_lote
            AND ret.exercicio        = lo.exercicio
            AND ret.tipo             = lo.tipo
            AND ret.cod_entidade     = lo.cod_entidade
          )

         LEFT JOIN (SELECT tbl.exercicio
                          ,tbl.cod_entidade
                          ,tbll.tipo
                          ,tbll.cod_lote
                          ,ta.observacao
                    FROM
                           tesouraria.boletim_liberado AS tbl
                    LEFT JOIN tesouraria.arrecadacao AS ta
                              ON  ta.exercicio    = tbl.exercicio
                              AND ta.cod_entidade = tbl.cod_entidade
                              AND ta.cod_boletim      = tbl.cod_boletim
                    JOIN tesouraria.boletim_liberado_lote as tbll
                    		ON tbll.cod_boletim = tbl.cod_boletim
                     		AND tbll.cod_entidade = tbl.cod_entidade
                    		AND tbll.exercicio = tbll.exercicio
                    		AND tbll.timestamp_liberado = tbll.timestamp_liberado
                    		AND tbll.timestamp_fechamento = tbll.timestamp_fechamento
                    WHERE
                           ta.exercicio    = tbl.exercicio
                           AND ta.cod_entidade = tbl.cod_entidade
                           AND ta.cod_boletim  = tbl.cod_boletim
                   ) AS tarrec
                     ON (
                        tarrec.cod_lote     = lo.cod_lote    AND
                        tarrec.tipo         = lo.tipo        AND
                        tarrec.exercicio    = lo.exercicio   AND
                        tarrec.cod_entidade = lo.cod_entidade
                        ),

          contabilidade.historico_contabil AS hc,
          contabilidade.valor_lancamento   AS vl

     LEFT JOIN (SELECT
                    tesouraria.transferencia_estornada.cod_lote_estorno,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra
                FROM
                    tesouraria.transferencia_estornada
                LEFT JOIN tesouraria.recibo_extra_transferencia ON (
                    tesouraria.recibo_extra_transferencia.exercicio = tesouraria.transferencia_estornada.exercicio AND
                    tesouraria.recibo_extra_transferencia.cod_entidade = tesouraria.transferencia_estornada.cod_entidade  AND                    tesouraria.recibo_extra_transferencia.cod_lote = tesouraria.transferencia_estornada.cod_lote
                    )
                WHERE
                    tesouraria.transferencia_estornada.exercicio    = '''''' || stExercicio || ''''''  AND
                    tesouraria.transferencia_estornada.cod_entidade  IN ('' || stCodEntidades || '')
                GROUP BY
                    tesouraria.transferencia_estornada.cod_lote_estorno,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra

               ) AS tret
                 ON (
                    tret.cod_lote_estorno  = vl.cod_lote    AND
                    tret.tipo         = vl.tipo        AND
                    tret.exercicio    = vl.exercicio   AND
                    tret.cod_entidade = vl.cod_entidade
                    )

     LEFT JOIN (SELECT
                    tesouraria.transferencia_estornada.cod_lote,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra
                FROM
                    tesouraria.transferencia_estornada
                LEFT JOIN tesouraria.recibo_extra_transferencia ON (
                    tesouraria.recibo_extra_transferencia.exercicio = tesouraria.transferencia_estornada.exercicio AND
                    tesouraria.recibo_extra_transferencia.cod_entidade = tesouraria.transferencia_estornada.cod_entidade  AND                    tesouraria.recibo_extra_transferencia.cod_lote = tesouraria.transferencia_estornada.cod_lote
                    )
                WHERE
                    tesouraria.transferencia_estornada.exercicio    = '''''' || stExercicio || ''''''  AND
                    tesouraria.transferencia_estornada.cod_entidade  IN ('' || stCodEntidades || '')
                GROUP BY
                    tesouraria.transferencia_estornada.cod_lote,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra

               ) AS tret2
                 ON (
                    tret2.cod_lote     = vl.cod_lote     AND
                    tret2.tipo         = vl.tipo         AND
                    tret2.exercicio    = vl.exercicio    AND
                    tret2.cod_entidade = vl.cod_entidade
                    ),
          contabilidade.conta_debito AS cd,
          contabilidade.plano_analitica  AS pa
    WHERE
            cd.exercicio    = vl.exercicio
        AND cd.cod_lote     = vl.cod_lote
        AND cd.tipo         = vl.tipo
        AND cd.sequencia    = vl.sequencia
        AND cd.tipo_valor   = vl.tipo_valor
        AND cd.cod_entidade = vl.cod_entidade

        AND vl.cod_lote      = l.cod_lote
        AND vl.tipo          = l.tipo
        AND vl.sequencia     = l.sequencia
        AND vl.exercicio     = l.exercicio
        AND vl.cod_entidade  = l.cod_entidade

        AND l.exercicio = '''''' || stExercicio || ''''''
        AND l.cod_entidade   IN ('' || stCodEntidades || '')

        AND lo.cod_lote      = l.cod_lote
        AND lo.exercicio     = l.exercicio
        AND lo.tipo          = l.tipo
        AND lo.cod_entidade  = l.cod_entidade '' || stAux ||

     '' AND hc.cod_historico = l.cod_historico
        AND hc.exercicio     = l.exercicio

        AND cd.cod_plano = pa.cod_plano
        AND cd.exercicio = pa.exercicio

        '' || stFiltro || ''
UNION

    SELECT
        l.oid as oid_tmp,
        l.cod_lote,
        l.sequencia,
        l.cod_historico,
        l.complemento,
        l.exercicio,
        l.cod_entidade,
        l.tipo,
        hc.nom_historico,
        cast(CASE WHEN (tret.cod_recibo_extra IS NOT NULL) OR (tret2.cod_recibo_extra IS NOT NULL) OR (ret.cod_recibo_extra IS NOT NULL)
            THEN coalesce(cast(tret.cod_recibo_extra as varchar),''''''''||coalesce(cast(ret.cod_recibo_extra as varchar),''''''''))
            ELSE ''''''''
           END
         ||CASE WHEN ((tt.observacao IS NOT NULL) OR (tte.observacao IS NOT NULL)) AND (l.tipo != ''''A'''')
            THEN coalesce(tt.observacao, '''''''')||coalesce(tte.observacao,'''''''')
            ELSE ''''''''
            END
         ||CASE WHEN (tarrec.observacao IS NOT NULL)
            THEN coalesce(tarrec.observacao,'''''''')
            ELSE ''''''''
           END
        AS VARCHAR) as observacao,
        vl.vl_lancamento,
        vl.tipo_valor,
        lo.dt_lote,
        cc.cod_plano AS cod_plano
    FROM
          contabilidade.lancamento         AS  l,
          contabilidade.lote               AS lo
          LEFT JOIN tesouraria.transferencia AS tt ON (
                tt.cod_lote     = lo.cod_lote
            AND tt.exercicio    = lo.exercicio
            AND tt.tipo         = lo.tipo
            AND tt.cod_entidade = lo.cod_entidade
          )
          LEFT JOIN tesouraria.transferencia_estornada AS tte ON (
                tte.cod_lote_estorno = lo.cod_lote
            AND tte.exercicio        = lo.exercicio
            AND tte.tipo             = lo.tipo
            AND tte.cod_entidade     = lo.cod_entidade
          )
          LEFT JOIN tesouraria.recibo_extra_transferencia AS ret ON (
                ret.cod_lote         = lo.cod_lote
            AND ret.exercicio        = lo.exercicio
            AND ret.tipo             = lo.tipo
            AND ret.cod_entidade     = lo.cod_entidade
          )
         LEFT JOIN (SELECT tbl.exercicio
                          ,tbl.cod_entidade
                          ,tbll.tipo
                          ,tbll.cod_lote
                          ,ta.observacao
                    FROM
                           tesouraria.boletim_liberado AS tbl
                    LEFT JOIN tesouraria.arrecadacao AS ta
                              ON  ta.exercicio    = tbl.exercicio
                              AND ta.cod_entidade = tbl.cod_entidade
                              AND ta.cod_boletim      = tbl.cod_boletim
                    JOIN tesouraria.boletim_liberado_lote as tbll
                    		ON tbll.cod_boletim = tbl.cod_boletim
                     		AND tbll.cod_entidade = tbl.cod_entidade
                    		AND tbll.exercicio = tbll.exercicio
                    		AND tbll.timestamp_liberado = tbll.timestamp_liberado
                    		AND tbll.timestamp_fechamento = tbll.timestamp_fechamento
                    WHERE
                           ta.exercicio    = tbl.exercicio
                           AND ta.cod_entidade = tbl.cod_entidade
                           AND ta.cod_boletim  = tbl.cod_boletim
                   ) AS tarrec
                     ON (
                        tarrec.cod_lote     = lo.cod_lote    AND
                        tarrec.tipo         = lo.tipo        AND
                        tarrec.exercicio    = lo.exercicio   AND
                        tarrec.cod_entidade = lo.cod_entidade
                        ),
          contabilidade.historico_contabil AS hc,
          contabilidade.valor_lancamento   AS vl

     LEFT JOIN (SELECT
                    tesouraria.transferencia_estornada.cod_lote_estorno,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra
                FROM
                    tesouraria.transferencia_estornada
                LEFT JOIN tesouraria.recibo_extra_transferencia ON (
                    tesouraria.recibo_extra_transferencia.exercicio = tesouraria.transferencia_estornada.exercicio AND
                    tesouraria.recibo_extra_transferencia.cod_entidade = tesouraria.transferencia_estornada.cod_entidade  AND                    tesouraria.recibo_extra_transferencia.cod_lote = tesouraria.transferencia_estornada.cod_lote
                    )
                WHERE
                    
                    tesouraria.transferencia_estornada.exercicio    = '''''' || stExercicio || ''''''  AND
                    tesouraria.transferencia_estornada.cod_entidade  IN ('' || stCodEntidades || '')
                GROUP BY
                    tesouraria.transferencia_estornada.cod_lote_estorno,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra

               ) AS tret
                 ON (
                    tret.cod_lote_estorno  = vl.cod_lote    AND
                    tret.tipo         = vl.tipo        AND
                    tret.exercicio    = vl.exercicio   AND
                    tret.cod_entidade = vl.cod_entidade
                    )

     LEFT JOIN (SELECT
                    tesouraria.transferencia_estornada.cod_lote,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra
                FROM
                    tesouraria.transferencia_estornada
                LEFT JOIN tesouraria.recibo_extra_transferencia ON (
                    tesouraria.recibo_extra_transferencia.exercicio = tesouraria.transferencia_estornada.exercicio AND
                    tesouraria.recibo_extra_transferencia.cod_entidade = tesouraria.transferencia_estornada.cod_entidade  AND                    tesouraria.recibo_extra_transferencia.cod_lote = tesouraria.transferencia_estornada.cod_lote
                    )
                WHERE
                    tesouraria.transferencia_estornada.exercicio    = '''''' || stExercicio || ''''''  AND
                    tesouraria.transferencia_estornada.cod_entidade  IN ('' || stCodEntidades || '')
                GROUP BY
                    tesouraria.transferencia_estornada.cod_lote,
                    tesouraria.transferencia_estornada.tipo,
                    tesouraria.transferencia_estornada.exercicio,
                    tesouraria.transferencia_estornada.cod_entidade,
                    tesouraria.recibo_extra_transferencia.cod_recibo_extra

               ) AS tret2
                 ON (
                    tret2.cod_lote     = vl.cod_lote     AND
                    tret2.tipo         = vl.tipo         AND
                    tret2.exercicio    = vl.exercicio    AND
                    tret2.cod_entidade = vl.cod_entidade
                    ),
          contabilidade.conta_credito    AS cc,
          contabilidade.plano_analitica  AS pa
    WHERE
            cc.exercicio    = vl.exercicio
        AND cc.cod_lote     = vl.cod_lote
        AND cc.tipo         = vl.tipo
        AND cc.sequencia    = vl.sequencia
        AND cc.tipo_valor   = vl.tipo_valor
        AND cc.cod_entidade = vl.cod_entidade

        AND vl.cod_lote      = l.cod_lote
        AND vl.tipo          = l.tipo
        AND vl.sequencia     = l.sequencia
        AND vl.exercicio     = l.exercicio
        AND vl.cod_entidade  = l.cod_entidade

        AND l.exercicio = '''''' || stExercicio || ''''''
        AND l.cod_entidade   IN ('' || stCodEntidades || '')

        AND lo.cod_lote      = l.cod_lote
        AND lo.exercicio     = l.exercicio
        AND lo.tipo          = l.tipo
        AND lo.cod_entidade  = l.cod_entidade '' || stAux || ''

        AND hc.cod_historico = l.cod_historico
        AND hc.exercicio     = l.exercicio

        AND cc.cod_plano = pa.cod_plano
        AND cc.exercicio = pa.exercicio

        '' || stFiltro || ''
)'';

EXECUTE stSql;

stSql := ''CREATE TEMPORARY TABLE tmp_relatorio AS
    SELECT
        tabela.cod_lote,
        tabela.sequencia,
        tabela.cod_historico,
        tabela.nom_historico,
        case tabela.tipo
            when ''''E'''' then
                tabela.complemento || '''' - '''' || contabilidade.fn_busca_historico_empenho(tabela.cod_lote,tabela.cod_entidade,tabela.exercicio,tabela.tipo)
            when ''''L'''' then 
                tabela.complemento || '''' - '''' || contabilidade.fn_busca_historico_liquidacao(tabela.cod_lote,tabela.cod_entidade,tabela.exercicio,tabela.tipo)
            when ''''P'''' then
                tabela.complemento || '''' - '''' || contabilidade.fn_busca_historico_pagamento(tabela.cod_lote,tabela.cod_entidade,tabela.exercicio,tabela.tipo)
            else
                tabela.complemento
        end as complemento,
        tabela.exercicio,
        tabela.cod_entidade,
        tabela.tipo,
        tabela.vl_lancamento,
        tabela.tipo_valor,
        cast( to_char( tabela.dt_lote, ''|| quote_literal(''dd/mm/yyyy'') || '' ) as varchar )AS dt_lote,
        tabela.dt_lote as data,
        pa.cod_plano,
        pc.cod_estrutural,
        pc.nom_conta,
        tabela.observacao,
        contabilidade.fn_recupera_contra_partida(
              tabela.exercicio
             ,tabela.cod_lote
             ,tabela.tipo
             ,tabela.sequencia
             ,tabela.tipo_valor
             ,tabela.cod_entidade
        ) AS contra_partida,
        sum(tmp_somatorio.saldoAnterior) as saldoAnterior
    FROM
        contabilidade.plano_conta     AS pc
            LEFT JOIN tmp_somatorio AS tmp_somatorio
                ON ( pc.cod_estrutural = tmp_somatorio.cod_estrutural),
        contabilidade.plano_analitica AS pa
        LEFT JOIN tmp_razao AS tabela
        ON( tabela.cod_plano = pa.cod_plano
        AND tabela.exercicio = pa.exercicio )
    WHERE
            pa.cod_conta     = pc.cod_conta
        AND pa.exercicio     = pc.exercicio
        AND pa.cod_plano is not null
        AND pa.exercicio = '''''' || stExercicio || ''''''
--        AND tabela.tipo<>''''I''''
        AND cast( pa.cod_plano as varchar ) != ''''''''
        AND pc.cod_estrutural BETWEEN '''''' || stCodEstruturalInicial || '''''' AND '''''' || stCodEstruturalFinal || ''''''
        '' || stFiltro || ''
    GROUP BY
        tabela.cod_lote,
        tabela.sequencia,
        tabela.cod_historico,
        tabela.nom_historico,
        tabela.complemento,
        tabela.exercicio,
        tabela.cod_entidade,
        tabela.tipo,
        tabela.vl_lancamento,
        tabela.tipo_valor,
        tabela.dt_lote,
        tabela.observacao,
        pa.cod_plano,
        pc.cod_estrutural,
        pc.nom_conta
    ORDER BY
        pc.cod_estrutural,
        tabela.dt_lote'';

EXECUTE stSql;

stSql := ''CREATE TEMPORARY TABLE tmp_aux_rel AS
    SELECT
         cod_estrutural
        ,count(cod_estrutural) as num_lancamentos
    FROM
        tmp_relatorio
    GROUP BY
         cod_estrutural'';

EXECUTE stSql;

stSql := ''
    SELECT
        rel.cod_lote,
        rel.sequencia,
        rel.cod_historico,
        rel.nom_historico,
        rel.complemento,
        rel.exercicio,
        rel.cod_entidade,
        rel.tipo,
        rel.vl_lancamento,
        rel.tipo_valor,
        rel.dt_lote,
        rel.cod_plano,
        rel.cod_estrutural,
        rel.nom_conta,
        rel.contra_partida,
        rel.saldoAnterior,
        cast(aux.num_lancamentos as integer) as num_lancamentos,
        rel.observacao

    FROM 
         tmp_relatorio as rel
        ,tmp_aux_rel   as aux
    WHERE
        rel.cod_estrutural = aux.cod_estrutural '';

    IF boMovimentacaoConta = ''N'' THEN
        stSql := stSql || '' AND ( ( aux.num_lancamentos >= 1 AND rel.exercicio IS NOT NULL )
                                  OR
                                   ( aux.num_lancamentos >= 1 AND rel.exercicio IS NULL AND rel.saldoAnterior > 0.00 )
                                 )
        '';
    END IF;

    stSql := stSql || '' ORDER BY rel.cod_estrutural, rel.data '';

FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP INDEX uq_valor;
DROP INDEX uq_somatorio;

--DROP TABLE tmp_razao;
DROP TABLE tmp_valor;
DROP TABLE tmp_somatorio;
DROP TABLE tmp_relatorio;
DROP TABLE tmp_aux_rel;
DROP TABLE tmp_empenho;   
DROP TABLE tmp_liquidacao;
DROP TABLE tmp_pagamento;

RETURN;

END;
' LANGUAGE 'plpgsql';

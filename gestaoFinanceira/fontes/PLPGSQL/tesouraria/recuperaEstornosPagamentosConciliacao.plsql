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
* $Revision: 24439 $
* $Name$
* $Author: cako $
* $Date: 2007-08-01 13:01:30 -0300 (Qua, 01 Ago 2007) $
*
* Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.1  2007/08/01 16:01:30  cako
Bug#9496#


*/

CREATE OR REPLACE FUNCTION tesouraria.fn_recupera_estornos_pagamentos_conciliacao(varchar, integer, integer, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    inCodEntidade           ALIAS FOR $2;
    inCodPlano              ALIAS FOR $3;
    stDtInicial             ALIAS FOR $4;
    stDtFinal               ALIAS FOR $5;
    stTipo                  ALIAS FOR $6;

    reRegistro          RECORD;

    stSql               VARCHAR := '';

BEGIN

    CREATE TEMP SEQUENCE tes;
    CREATE TEMP SEQUENCE cont;

stSql := '
        SELECT
             cp.cod_lote,
             boletim.dt_boletim as dt_lancamento,
             pe.exercicio,
             pe.cod_plano,
             cast(
             CASE WHEN TRIM(substring(PE.observacao,1,60)) =  '''' THEN
                 CASE WHEN ( pe.exercicio_empenho < pe.exercicio_boletim ) THEN
                      ''Estorno de Pagamento de RP n° '' || pe.cod_empenho || '' / ''|| pe.exercicio_empenho
                 ELSE ''Estorno de Pagamento de Empenho n° '' || pe.cod_empenho || '' / ''|| pe.exercicio_empenho
                 END
               ELSE
                 CASE WHEN (pe.exercicio_empenho < pe.exercicio_boletim) THEN
                      ''Estorno de Pagamento de RP n° '' || pe.cod_empenho || '' / '' || pe.exercicio_empenho
                 ELSE ''Estorno de Pagamento de Empenho n° '' || pe.cod_empenho || '' / '' || pe.exercicio_empenho 
                 END
             END as varchar) as descricao,
             replace(trim(substring(coalesce(PE.observacao,''''),1,60)),''\r\n'','' '')  as observacao,
             PE.vl_anulado * (-1) as vl_lancamento,
             cast( ''C'' as varchar ) as tipo_valor,
             cp.tipo,
             cp.sequencia,
             pe.cod_entidade,
             CASE
                  WHEN (cp.cod_plano is not null)
                      THEN ''true''
                      ELSE ''''
             END as conciliar,
             CAST(''A'' as varchar ) as tipo_movimentacao,
             0 as cod_arrecadacao,
             0 as cod_receita,
             ttp.cod_bordero,
             CAST('''' as text) as timestamp_arrecadacao,
             CAST('''' as text) as tipo_arrecadacao,
             coalesce( cast( lpad(cp.mes,2,0) as varchar), to_char(boletim.dt_boletim,''mm'') ) as mes
         FROM
            tesouraria.boletim             as BOLETIM
            JOIN ( SELECT  pe.exercicio
                          ,pe.exercicio_boletim
                          ,pe.cod_boletim
                          ,enl.cod_empenho
                          ,enl.exercicio_empenho
                          ,pe.cod_entidade
                          ,pe.cod_nota
                          ,p.cod_plano
                          ,pe.timestamp_anulado
                          ,pe.timestamp
                          ,EPLNLP.cod_ordem
                          ,ENLPA.observacao
                          ,ENLPA.vl_anulado
                          ,nextval(''tes'') as id

                     FROM tesouraria.pagamento_estornado                    as PE,
                          tesouraria.pagamento                              as P,
                          empenho.pagamento_liquidacao                      as EPL,
                          empenho.pagamento_liquidacao_nota_liquidacao_paga as EPLNLP,
                          empenho.nota_liquidacao                           as ENL,
                          empenho.nota_liquidacao_paga                      as ENLP,
                          empenho.nota_liquidacao_paga_anulada              as ENLPA
                    WHERE PE.cod_nota                 = P.cod_nota
                      AND PE.exercicio                = P.exercicio
                      AND PE.cod_entidade             = P.cod_entidade
                      AND PE.timestamp                = P.timestamp

                      AND PE.cod_nota                 = ENLPA.cod_nota
                      AND PE.exercicio                = ENLPA.exercicio
                      AND PE.cod_entidade             = ENLPA.cod_entidade
                      AND PE.timestamp_anulado        = ENLPA.timestamp_anulada

                      AND ENLPA.cod_nota              = ENLP.cod_nota
                      AND ENLPA.exercicio             = ENLP.exercicio
                      AND ENLPA.cod_entidade          = ENLP.cod_entidade
                      AND ENLPA.timestamp             = ENLP.timestamp
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
            ';

            IF (stTipo = 'corrente') THEN
                 stSql := stSql || ' AND to_char(pe.timestamp_anulado,''mm'') = TO_CHAR(TO_DATE( ''' || stDtFinal ||''',''dd/mm/yyyy''),''mm'') ';
            END IF;                                   
 
            IF (stTipo = 'pendente') THEN
                 stSql := stSql || ' AND to_date(to_char(pe.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'') < TO_DATE( '''|| stDtInicial ||''', ''dd/mm/yyyy'' ) 
                                     AND p.cod_plano = '|| inCodPlano ||'          
                                   ';
            END IF;

            stSql := stSql || '

                ORDER BY pe.cod_nota, pe.timestamp_anulado
            ) as PE on (   BOLETIM.cod_boletim  = PE.cod_boletim
                       AND BOLETIM.exercicio    = PE.exercicio_boletim
                       AND BOLETIM.cod_entidade = PE.cod_entidade
            )
            LEFT JOIN tesouraria.transacoes_pagamento as TTP
            ON (    ttp.cod_ordem    = PE.cod_ordem
                AND ttp.cod_entidade = PE.cod_entidade
                AND ttp.exercicio    = PE.exercicio
            )
            ,( SELECT  cp.cod_lote
                      ,cp.cod_entidade 
                      ,cp.tipo
                      ,cp.exercicio
                      ,cp.sequencia
                      ,cp.exercicio_liquidacao
                      ,cp.cod_nota
                      ,cp.timestamp
                      ,lo.dt_lote
                      ,lc.cod_plano
                      ,lc.mes
                      ,nextval(''cont'') as id
                 FROM contabilidade.lote as lo
                      JOIN contabilidade.lancamento_empenho as LE
                      ON (   le.cod_lote     = lo.cod_lote
                         AND le.cod_entidade = lo.cod_entidade
                         AND le.tipo         = lo.tipo
                         AND le.exercicio    = lo.exercicio
                         AND le.estorno = true
                      )
                      JOIN contabilidade.pagamento as cp
                      ON (   le.cod_entidade = cp.cod_entidade
                         AND le.tipo         = cp.tipo
                         AND le.sequencia    = cp.sequencia
                         AND le.exercicio    = cp.exercicio
                         AND le.cod_lote     = cp.cod_lote
                         AND le.estorno = true
                      )
                      LEFT JOIN tesouraria.conciliacao_lancamento_contabil as lc
                      on(    lo.cod_lote         = lc.cod_lote
                         AND lo.tipo             = lc.tipo
                      -- AND lo.sequencia        = lc.sequencia
                         AND lo.exercicio        = lc.exercicio
                         AND lo.cod_entidade     = lc.cod_entidade
                         AND lc.tipo_valor = ''C''
                      ) ';

            IF (stTipo = 'corrente') THEN
                 stSql := stSql || ' WHERE to_char(lo.dt_lote,''mm'') = TO_CHAR(TO_DATE( '''|| stDtFinal ||''',''dd/mm/yyyy''),''mm'') ';
            END IF; 

            IF (stTipo = 'pendente') THEN
                 stSql := stSql || ' 
                      WHERE lo.dt_lote < TO_DATE( '''|| stDtInicial ||''', ''dd/mm/yyyy'' )
                        AND lc.cod_plano = '|| inCodPlano ||'          
                    ';
            END IF;

            stSql := stSql || '
                 ORDER BY cp.cod_nota, cp.cod_lote
            ) as cp

         WHERE   PE.exercicio    = CP.exercicio_liquidacao
             AND PE.cod_nota     = CP.cod_nota
             AND PE.cod_entidade = CP.cod_entidade
             AND PE.id           = CP.id
             AND PE.timestamp    = CP.timestamp ';

        IF (stTipo = 'corrente') THEN        
            stSql := stSql || '

             AND pe.cod_entidade = '|| inCodEntidade ||'
             AND to_char(PE.timestamp_anulado,''yyyy'') = '''|| stExercicio ||'''
             AND cp.dt_lote = to_date(to_char(PE.timestamp_anulado,''yyyy-mm-dd''),''yyyy-mm-dd'')
             AND to_char(BOLETIM.dt_boletim,''mm'') = TO_CHAR(TO_DATE( '''|| stDtFinal ||''',''dd/mm/yyyy''),''mm'') 
            ';
        END IF; 

        IF (stTipo = 'pendente') THEN
            stSql := stSql || '
             AND CASE WHEN (cp.mes IS NOT NULL)
               THEN CASE WHEN (to_char( boletim.dt_boletim, ''mm'' )::integer < lpad(cp.mes,2,0)::integer)
                      THEN TRUE
                      ELSE FALSE
                    END
               ELSE TRUE
             END 
             AND pe.cod_entidade = '|| inCodEntidade ||'           
             AND to_char(PE.timestamp_anulado,''yyyy'') = '''|| stExercicio ||'''                
             AND BOLETIM.dt_boletim < TO_DATE( '''|| stDtInicial ||''', ''dd/mm/yyyy'' ) 
             AND cp.dt_lote = to_date(to_char(PE.timestamp_anulado, ''yyyy-mm-dd''),''yyyy-mm-dd'')         
            ';
        END IF;


FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN next reRegistro;
END LOOP;

DROP SEQUENCE tes;
DROP SEQUENCE cont;

RETURN;
END;

$$ language 'plpgsql';

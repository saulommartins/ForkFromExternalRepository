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
* $Revision: 27052 $
* $Name:  $
* $Author: cako $
* $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $
*
* $Id: empenhoEmpenhadoPagoLiquidado.plsql 32983 2008-09-04 13:55:57Z domluc $

* Casos de uso: uc-02.03.06
*/
CREATE OR REPLACE FUNCTION empenho.fn_empenho_empenhado_pago_liquidado(varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar, varchar, boolean) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                    ALIAS FOR $1;
    stFiltro                       ALIAS FOR $2;
    stDtInicial                    ALIAS FOR $3;
    stDtFinal                      ALIAS FOR $4;
    stCodEntidades                 ALIAS FOR $5;
    stCodOrgao                     ALIAS FOR $6;
    stCodUnidade                   ALIAS FOR $7;
    stCodPao                       ALIAS FOR $8;
    stCodRecurso                   ALIAS FOR $9;
    stCodElementoDispensa          ALIAS FOR $10;
    stDestinacaoRecurso            ALIAS FOR $11;
    inCodDetalhamento              ALIAS FOR $12;
    stCodElementoDispensaMasc      ALIAS FOR $13;
    stSituacao                     ALIAS FOR $14;
    stCodHistorico                 ALIAS FOR $15;
    stOrdenacao                    ALIAS FOR $16;
    inCodFuncao                    ALIAS FOR $17;
    inCodSubFuncao                 ALIAS FOR $18;
    inCodPrograma                  ALIAS FOR $19;
    inCodPlano                     ALIAS FOR $20;
    inCodDotacao                   ALIAS FOR $21;
    boMostrarAnuladoMesmoPeriodo   ALIAS FOR $22;
    
    stSql                          VARCHAR   := '';
    reRegistro                     RECORD;
BEGIN
    
    if (stSituacao = '2') then
        stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
            SELECT
                p.cod_entidade as cod_entidade,
                p.cod_nota as cod_nota,
                p.exercicio_liquidacao as exercicio_liquidacao,
                p.timestamp as timestamp,
                pa.cod_plano as cod_plano,
                pc.nom_conta as nom_conta
            FROM
                contabilidade.pagamento p,
                contabilidade.lancamento_empenho le,
                contabilidade.conta_credito cc,
                contabilidade.plano_analitica pa,
                contabilidade.plano_conta pc
            WHERE
                --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                    p.cod_entidade  IN (' || stCodEntidades || ')
                AND p.exercicio     = ''' || stExercicio || '''
                AND p.cod_lote      = le.cod_lote
                AND p.tipo          = le.tipo
                AND p.sequencia     = le.sequencia
                AND p.exercicio     = le.exercicio
                AND p.cod_entidade  = le.cod_entidade
                AND le.estorno      = false

                --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
                AND le.cod_lote     = cc.cod_lote
                AND le.tipo         = cc.tipo
                AND le.exercicio    = cc.exercicio
                AND le.cod_entidade = cc.cod_entidade
                AND le.sequencia    = cc.sequencia

                --Ligação CONTA_CREDITO : PLANO ANALITICA
                AND cc.cod_plano    = pa.cod_plano
                AND cc.exercicio    = pa.exercicio';

                if ( inCodPlano is not null and TRIM(inCodPlano)<>'') then
                    stSql := stSql || ' and cc.cod_plano = ' || inCodPlano || ' ';
                end if;
                
                stSql := stSql || '
               --Ligação PLANO ANALITICA : PLANO CONTA
                AND pa.cod_conta   = pc.cod_conta
                AND pa.exercicio   = pc.exercicio
        );

        CREATE INDEX idx_tmp_pago ON tmp_pago (cod_entidade, cod_nota, exercicio_liquidacao, timestamp); ';
        EXECUTE stSql;
    end if;

    if (stSituacao = '5') then
        stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
            SELECT
                p.cod_entidade as cod_entidade,
                p.cod_nota as cod_nota,
                p.exercicio_liquidacao as exercicio_liquidacao,
                p.timestamp as timestamp,
                e_nlcp.cod_plano as cod_plano,
                pc.nom_conta as nom_conta
            from contabilidade.pagamento AS p
                    
            join contabilidade.lancamento_empenho AS le
              on ( le.exercicio    = p.exercicio
             and   le.cod_lote     = p.cod_lote
             and   le.tipo         = p.tipo
             and   le.sequencia    = p.sequencia
             and   le.cod_entidade = p.cod_entidade )
            
            join contabilidade.conta_debito AS cd
              on ( cd.cod_lote     = p.cod_lote
             AND   cd.tipo         = p.tipo
             AND   cd.exercicio    = p.exercicio
             AND   cd.cod_entidade = p.cod_entidade
             AND   cd.sequencia    = p.sequencia )
            
            JOIN empenho.nota_liquidacao_conta_pagadora AS e_nlcp
              ON ( e_nlcp.exercicio            = p.exercicio
             AND   e_nlcp.exercicio_liquidacao = p.exercicio_liquidacao
             AND   e_nlcp.cod_entidade         = p.cod_entidade
             AND   e_nlcp.cod_nota             = p.cod_nota
             AND   e_nlcp.timestamp            = p.timestamp )
            
            join contabilidade.plano_analitica AS pa
              on ( pa.exercicio = e_nlcp.exercicio 
             AND   pa.cod_plano = e_nlcp.cod_plano )
            
            JOIN contabilidade.plano_conta AS pc
              ON ( pc.exercicio = pa.exercicio
             AND   pc.cod_conta = pa.cod_conta )
            WHERE
                --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                    p.cod_entidade      IN (' || stCodEntidades || ')
                AND p.exercicio     = ''' || stExercicio || '''
                AND le.estorno = true ';
        if ( inCodPlano is not null and TRIM(inCodPlano)<>'') then
            stSql := stSql || ' and e_nlcp.cod_plano = ' || inCodPlano || ' ';
        end if;
        stSql := stSql || ' )';

        EXECUTE stSql;
    end if;
  
        stSql := '
            SELECT entidade
                 , descricao_categoria
                 , nom_tipo
                 , empenho
                 , exercicio
                 , cgm
                 , razao_social
                 , cod_nota
                 , stData
                 , ordem
                 , conta
                 , coalesce(nome_conta,''NÃO INFORMADO'')
                 , valor
                 , valor_anulado
                 , descricao
                 , recurso
                 , despesa
            FROM (
                    SELECT e.cod_entidade as entidade
                         , categoria_empenho.descricao as descricao_categoria
                         , tipo_empenho.nom_tipo
                         , e.cod_empenho as empenho
                         , e.exercicio as exercicio
                         , pe.cgm_beneficiario as cgm
                         , cgm.nom_cgm as razao_social
                         , cast( pe.descricao as varchar ) as descricao ';

            if (stSituacao = '1') then            
                   stSql := stSql ||
                         ', 0 as cod_nota
                          , 0 as ordem
                          , 0 as conta
                          , cgm.nom_cgm as nome_conta
                          , to_char(e.dt_empenho,''dd/mm/yyyy'') as stData
                          , coalesce(sum(e.vl_anulado), 0.00) as valor_anulado
                          , sum(e.vl_total) as valor ';
            end if;            

            if (stSituacao = '2') then
                    stSql := stSql ||
                          ', to_char(nlp.timestamp,''dd/mm/yyyy'') as stData
                           , nlp.cod_nota as cod_nota
                           , sum(nlp.vl_pago) as valor
                           , cast(0.00 as numeric) as valor_anulado
                           , pl.cod_ordem as ordem
                           , tmp.cod_plano as conta
                           , tmp.nom_conta as nome_conta ';
            end if;
            
            if (stSituacao = '3') then
                    stSql := stSql || '
                           , to_char(nl.dt_liquidacao,''dd/mm/yyyy'') as stData
                           , nli.cod_nota as cod_nota
                           , sum(nli.vl_total) as valor
                           , coalesce(sum(nlia.vl_anulado), 0.00) as valor_anulado
                           , 0 as ordem
                           , 0 as conta
                           , cgm.nom_cgm  as nome_conta ';
            end if;

            if (stSituacao = '4') then
                    stSql := stSql ||
                          ', to_char(ea.timestamp,''dd/mm/yyyy'') as stData
                           , 0 as cod_nota
                           , sum(eai.vl_anulado) as valor
                           , cast(0.00 as numeric) as valor_anulado
                           , 0 as ordem
                           , 0 as conta
                           , cgm.nom_cgm as nome_conta ';
            end if;

            if (stSituacao = '5') then
                    stSql := stSql ||
                           ', to_char(nlpa.timestamp_anulada,''dd/mm/yyyy'') as stData
                            , nlpa.cod_nota as cod_nota
                            , sum(nlpa.vl_anulado) as valor
                            , cast(0.00 as numeric) as valor_anulado
                            , pl.cod_ordem as ordem
                            , tmp.cod_plano as conta
                            , tmp.nom_conta as nome_conta ';
            end if;

            if (stSituacao = '6') then
                    stSql := stSql ||
                           ', to_char(nlia.timestamp,''dd/mm/yyyy'') as stData
                            , nlia.cod_nota as cod_nota
                            , sum(nlia.vl_anulado) as valor
                            , cast(0.00 as numeric) as valor_anulado
                            , 0 as ordem
                            , 0 as conta
                            , cgm.nom_cgm as nome_conta ';
            end if; 
            
            stSql := stSql ||
                           ', ped_d_cd.nom_recurso as recurso
                            , ped_d_cd.cod_estrutural as despesa ';
            
            IF (stSituacao = '1') THEN
            stSql := stSql || '
                  FROM 
                      (
                         SELECT    
                                e.cod_entidade
                            ,   e.cod_empenho
                            ,   e.exercicio
                            ,   e.dt_empenho
                            ,   e.cod_categoria
                            ,   ipe.vl_total
                            ,   ipe.cod_pre_empenho
                            ,   ipe.num_item
                            ,   sum(eai.vl_anulado) as vl_anulado
                        FROM    empenho.empenho as e
                  INNER JOIN    empenho.item_pre_empenho as ipe
                          ON    e.exercicio       = ipe.exercicio
                         AND    e.cod_pre_empenho = ipe.cod_pre_empenho ';
                    IF boMostrarAnuladoMesmoPeriodo THEN
                        stSql := stSql || '
                        LEFT JOIN ';
                    ELSE
                        stSql := stSql || '
                        INNER JOIN ';
                    END IF;
                   stSql := stSql || ' empenho.empenho_anulado ea
                          ON    ea.exercicio = e.exercicio
                         AND    ea.cod_entidade = e.cod_entidade
                         AND    ea.cod_empenho = e.cod_empenho
                         AND to_date( to_char( ea."timestamp", ''dd/mm/yyyy''), ''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                   LEFT JOIN empenho.empenho_anulado_item AS eai
                        ON (    eai.exercicio = ea.exercicio
                            AND eai.cod_entidade = ea.cod_entidade
                            AND eai.cod_empenho = ea.cod_empenho
                            AND eai."timestamp" = ea."timestamp"
                            AND eai.exercicio = e.exercicio
                            AND eai.cod_pre_empenho = ipe.cod_pre_empenho
                            AND eai.num_item = ipe.num_item
                           )
                    GROUP BY    e.cod_entidade
                           ,    e.cod_empenho
                           ,    e.exercicio
                           ,    e.dt_empenho
                           ,    e.cod_categoria
                           ,    ipe.vl_total
                           ,    ipe.cod_pre_empenho
                           ,    ipe.num_item
                      ) as e
                    ';
            ELSE
                stSql := stSql || '
                    FROM
                        empenho.empenho     as e';
            END IF;
            
            stSql := stSql || '
                JOIN empenho.categoria_empenho 
                  ON categoria_empenho.cod_categoria = e.cod_categoria
                   , empenho.historico   as h ';
                
                if (stSituacao = '2') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_paga nlp
                        LEFT OUTER JOIN tmp_pago as tmp ON (
                            --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                                nlp.cod_entidade = tmp.cod_entidade
                            AND nlp.cod_nota = tmp.cod_nota
                            AND nlp.exercicio = tmp.exercicio_liquidacao
                            AND nlp.timestamp = tmp.timestamp
                        )
                    , empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                    , empenho.pagamento_liquidacao pl
                    ';
                end if;

                if (stSituacao = '3') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_item nli
                    ';
                    
                    IF boMostrarAnuladoMesmoPeriodo THEN
                        stSql := stSql || '
                        LEFT JOIN ';
                    ELSE
                        stSql := stSql || '
                        INNER JOIN ';
                    END IF;
                    
                    stSql := stSql ||
                                  '( SELECT nlia.exercicio
                                            ,nlia.cod_nota
                                            ,nlia.cod_entidade
                                            ,nlia.num_item
                                            ,nlia.cod_pre_empenho
                                            ,nlia.exercicio_item
                                            ,SUM(nlia.vl_anulado) AS vl_anulado
                                    FROM empenho.nota_liquidacao_item_anulado AS nlia
                                    WHERE to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                                    GROUP BY nlia.exercicio
                                            ,nlia.cod_nota
                                            ,nlia.cod_entidade
                                            ,nlia.num_item
                                            ,nlia.cod_pre_empenho
                                            ,nlia.exercicio_item
                                ) as nlia
                                 ON nli.exercicio       = nlia.exercicio
                                AND nli.cod_nota        = nlia.cod_nota
                                AND nli.cod_entidade    = nlia.cod_entidade
                                AND nli.num_item        = nlia.num_item
                                AND nli.cod_pre_empenho = nlia.cod_pre_empenho
                                AND nli.exercicio_item  = nlia.exercicio_item
                                ';
                end if;

                if (stSituacao = '4') then
                    stSql := stSql || '
                    , empenho.empenho_anulado ea
                    , empenho.empenho_anulado_item eai
                    ';
                end if;

                if (stSituacao = '5') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_paga nlp

                    INNER JOIN ( SELECT exercicio_liquidacao
                                        , cod_entidade
                                        , cod_nota
                                        , timestamp
                                        , cod_plano
                                        , nom_conta
                                     FROM tmp_estornado
                                 GROUP BY exercicio_liquidacao
                                        , cod_entidade
                                        , cod_nota
                                        , timestamp
                                        , cod_plano
                                        , nom_conta
                      ) AS tmp
                      ON tmp.exercicio_liquidacao = nlp.exercicio
                     AND tmp.cod_entidade = nlp.cod_entidade
                     AND tmp.cod_nota = nlp.cod_nota
                     AND tmp.timestamp = nlp.timestamp

                    , empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                    , empenho.nota_liquidacao_paga_anulada nlpa
                    , empenho.pagamento_liquidacao pl

                    ';
                end if;

                if (stSituacao = '6') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_item nli
                    , empenho.nota_liquidacao_item_anulado nlia
                    ';
                end if;

             stSql := stSql || '
                   , sw_cgm              as cgm
                   , empenho.pre_empenho as pe
                   
                JOIN empenho.tipo_empenho 
                ON  tipo_empenho.cod_tipo = pe.cod_tipo
                
     LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, 
                        ped.cod_pre_empenho, 
                        d.num_pao, 
                        d.num_orgao,
                        d.num_unidade, 
                        d.cod_recurso,
                        d.cod_despesa,
                        rec.nom_recurso, 
                        d.cod_conta,
                        cd.cod_estrutural, 
                        rec.masc_recurso_red,
                        rec.cod_detalhamento,
                        ppa.acao.num_acao,
                        programa.num_programa
                    FROM
                        empenho.pre_empenho_despesa as ped, 
                        orcamento.despesa           as d
                        
                        JOIN orcamento.recurso(''' || stExercicio || ''') as rec
                          ON rec.cod_recurso = d.cod_recurso
                         AND rec.exercicio = d.exercicio
                        
                        JOIN orcamento.programa_ppa_programa
                          ON programa_ppa_programa.cod_programa = d.cod_programa
                         AND programa_ppa_programa.exercicio    = d.exercicio
                        
                        JOIN ppa.programa
                          ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                          
                        JOIN orcamento.pao_ppa_acao
                          ON pao_ppa_acao.num_pao   = d.num_pao
                         AND pao_ppa_acao.exercicio = d.exercicio
                         
                        JOIN ppa.acao 
                          ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                           
                           , orcamento.conta_despesa as cd
                           
                    WHERE ped.exercicio      = ''' || stExercicio || '''
                      AND ped.cod_despesa    = d.cod_despesa
                      AND ped.exercicio      = d.exercicio
                      AND ';
                    
                    if ( inCodFuncao is not null and TRIM(inCodFuncao)<>'') then
                        stSql := stSql || ' d.cod_funcao = ' || inCodFuncao || ' AND ';
                    end if;

                    if ( inCodSubFuncao is not null and TRIM(inCodSubFuncao)<>'') then
                        stSql := stSql || ' d.cod_subfuncao = ' || inCodSubFuncao || ' AND ';
                    end if;

               stSql := stSql || '
                        ped.cod_conta      = cd.cod_conta
                    AND ped.exercicio      = cd.exercicio
                ) AS ped_d_cd
               ON pe.exercicio       = ped_d_cd.exercicio
              AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho 

            WHERE e.exercicio         = ''' || stExercicio || '''
              AND e.exercicio         = pe.exercicio
              AND e.cod_pre_empenho   = pe.cod_pre_empenho
              AND e.cod_entidade      IN (' || stCodEntidades || ')
              AND pe.cgm_beneficiario = cgm.numcgm 
              AND h.cod_historico     = pe.cod_historico    
              AND h.exercicio         = pe.exercicio   ';

                if (stCodHistorico is not null and TRIM(stCodHistorico)<>'') then
                    stSql := stSql || ' and h.cod_historico = ' || stCodHistorico || ' ';
                end if;

                if (stSituacao = '1') then
                    IF boMostrarAnuladoMesmoPeriodo THEN
                        stSql := stSql || ' AND e.dt_empenho BETWEEN ';
                    ELSE
                        stSql := stSql || ' AND e.dt_empenho NOT BETWEEN ';
                    END IF;
                    
                    stSql := stSql || ' to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'') ';
                end if;

                if (stSituacao = '2') then
                    stSql := stSql || '

                       --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
                        AND e.cod_empenho = nl.cod_empenho

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
                        AND nl.exercicio = nlp.exercicio
                        AND nl.cod_nota = nlp.cod_nota
                        AND nl.cod_entidade = nlp.cod_entidade
                        AND to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                        AND nlp.cod_entidade = plnlp.cod_entidade
                        AND nlp.cod_nota = plnlp.cod_nota
                        AND nlp.exercicio = plnlp.exercicio_liquidacao
                        AND nlp.timestamp = plnlp.timestamp

                        --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                        AND pl.cod_ordem = plnlp.cod_ordem
                        AND pl.exercicio = plnlp.exercicio
                        AND pl.cod_entidade = plnlp.cod_entidade
                        AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                        AND pl.cod_nota = plnlp.cod_nota
                    ';
                end if;

                if (stSituacao = '3') then
                    stSql := stSql || '
                    --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.exercicio    = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
                        AND e.cod_empenho  = nl.cod_empenho

                    --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                        AND nl.exercicio    = nli.exercicio
                        AND nl.cod_nota     = nli.cod_nota
                        AND nl.cod_entidade = nli.cod_entidade
                    ';   
                        
                    IF boMostrarAnuladoMesmoPeriodo THEN
                        stSql := stSql || ' AND nl.dt_liquidacao BETWEEN ';
                    ELSE
                        stSql := stSql || ' AND nl.dt_liquidacao NOT BETWEEN ';
                    END IF;
                    
                    stSql := stSql || ' to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'') ';
                    
                end if;

                if (stSituacao = '4') then
                    stSql := stSql || '
                        AND to_date( to_char( ea."timestamp", ''dd/mm/yyyy''), ''dd/mm/yyyy'' ) BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                        AND ea.exercicio = e.exercicio AND ea.exercicio = eai.exercicio
                        AND ea.timestamp = eai.timestamp
                        AND ea.cod_entidade = e.cod_entidade AND ea.cod_entidade = eai.cod_entidade
                        AND ea.cod_empenho = e.cod_empenho AND  ea.cod_empenho = eai.cod_empenho
                    ';
                end if;

                if (stSituacao = '5') then
                    stSql := stSql || '
                        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.cod_empenho = nl.cod_empenho
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                        AND nl.exercicio = nlp.exercicio
                        AND nl.cod_nota = nlp.cod_nota
                        AND nl.cod_entidade = nlp.cod_entidade
    
                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                        AND nlp.cod_entidade = plnlp.cod_entidade
                        AND nlp.cod_nota = plnlp.cod_nota
                        AND nlp.exercicio = plnlp.exercicio_liquidacao
                        AND nlp.timestamp = plnlp.timestamp

                        --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
                        AND pl.cod_ordem = plnlp.cod_ordem
                        AND pl.exercicio = plnlp.exercicio
                        AND pl.cod_entidade = plnlp.cod_entidade
                        AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                        AND pl.cod_nota = plnlp.cod_nota

                        --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
                        AND nlp.exercicio = nlpa.exercicio
                        AND nlp.cod_nota = nlpa.cod_nota
                        AND nlp.cod_entidade = nlpa.cod_entidade
                        AND nlp.timestamp = nlpa.timestamp
                        AND to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                    ';
                end if;

                if (stSituacao = '6') then
                    stSql := stSql || '
                        --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
                        AND e.cod_empenho = nl.cod_empenho

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                        AND to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                        AND nl.exercicio = nli.exercicio
                        AND nl.cod_nota = nli.cod_nota
                        AND nl.cod_entidade = nli.cod_entidade

                        --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
                        AND nli.exercicio = nlia.exercicio
                        AND nli.cod_nota = nlia.cod_nota
                        AND nli.cod_entidade = nlia.cod_entidade
                        AND nli.num_item = nlia.num_item
                        AND nli.cod_pre_empenho = nlia.cod_pre_empenho
                        AND nli.exercicio_item = nlia.exercicio_item
                    ';
                end if;

                if (stCodOrgao is not null and stCodOrgao<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_orgao = '|| stCodOrgao ||' ';
                end if;

                if (stCodUnidade is not null and stCodUnidade<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_unidade = '|| stCodUnidade ||'  ';
                end if;

                if (stCodPao is not null and stCodPao<>'') then
                    --stSql := stSql || ' AND ped_d_cd.num_pao = '|| stCodPao ||' ';
                    stSql := stSql || ' AND ped_d_cd.num_acao ='|| stCodPao ||' ';
                end if;
                
                IF (inCodPrograma IS NOT NULL AND inCodPrograma <> '') THEN
                    stSql := stSql || ' AND ped_d_cd.num_programa = '|| inCodPrograma || ' ';
                END IF;

                if (stCodRecurso is not null and stCodRecurso<>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_recurso = '|| stCodRecurso ||' ';
                end if;
                
                if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                    stSql := stSql || ' AND ped_d_cd.masc_recurso_red like '''|| stDestinacaoRecurso || '''%''' ||''' ';
                end if;
                
                if (inCodDotacao is not null and inCodDotacao <>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_despesa = ' || inCodDotacao || ' ';
                end if;
                
                if (inCodDetalhamento is not null and inCodDetalhamento <> '') then 
                        stSql := stSql || ' AND ped_d_cd.cod_detalhamento = '|| inCodDetalhamento ||' ';
                end if;

                if (stCodElementoDispensa is not null and stCodElementoDispensa<>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_estrutural like publico.fn_mascarareduzida( '''||stCodElementoDispensaMasc||''' ) || ''%'' ';
                end if;

                IF stFiltro != '' THEN
                    stSql := stSql || ' AND ' || stFiltro || ' ';
                END IF;

            stSql := stSql || '
            GROUP BY ';
            
            if (stSituacao = '1') then
                stSql := stSql || 'e.dt_empenho, e.cod_pre_empenho,';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || 'to_char(nlp.timestamp,''dd/mm/yyyy''), nlp.cod_nota,  pl.cod_ordem, tmp.cod_plano, tmp.nom_conta,';
            end if;

            if (stSituacao = '3') then
                    stSql := stSql || 'nl.dt_liquidacao, nli.cod_nota,';
            end if;

            if (stSituacao = '4') then
                    stSql := stSql || 'ea.timestamp,';
            end if;

            if (stSituacao = '5') then
                    stSql := stSql || 'to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''), nlpa.cod_nota, pl.cod_ordem, tmp.cod_plano, tmp.nom_conta,';
            end if;

            if (stSituacao = '6') then
                    stSql := stSql || 'nlia.timestamp, nlia.cod_nota,';
            end if;

            stSql := stSql || ' e.cod_entidade, e.cod_empenho , e.exercicio , pe.cgm_beneficiario, cgm.nom_cgm, pe.descricao, ped_d_cd.cod_estrutural , ped_d_cd.nom_recurso, categoria_empenho.descricao, tipo_empenho.nom_tipo  ORDER BY ';

            if (stSituacao = '1') then
                stSql := stSql || 'e.dt_empenho,';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || 'to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy''),';
            end if;

            if (stSituacao = '3') then
                    stSql := stSql || 'nl.dt_liquidacao,';
            end if;

            if (stSituacao = '4') then
                    stSql := stSql || 'ea.timestamp,';
            end if;

            if (stSituacao = '5') then
                    stSql := stSql || 'to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy''),';
            end if;

            if (stSituacao = '6') then
                    stSql := stSql || 'nlia.timestamp,';
            end if;

            stSql := stSql || 'e.cod_entidade , e.cod_empenho , e.exercicio, ';

            if (stSituacao = '1') then
                    stSql := stSql || 'e.cod_pre_empenho,';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || 'nlp.cod_nota, pl.cod_ordem, tmp.cod_plano, tmp.nom_conta,';
            end if;

            if (stSituacao = '3') then
                    stSql := stSql || ' nli.cod_nota,';
            end if;

            if (stSituacao = '4') then
                    stSql := stSql || ' ea.timestamp,';
            end if;

            if (stSituacao = '5') then
                    stSql := stSql || ' nlpa.cod_nota, pl.cod_ordem, tmp.cod_plano, tmp.nom_conta,';
            end if;

            if (stSituacao = '6') then
                    stSql := stSql || ' nlia.cod_nota,';
            end if;


            stSql := stSql || 'pe.cgm_beneficiario, cgm.nom_cgm
                            ) as tbl
            
                        WHERE valor <> ''0.00''
            ';
            
            if (stOrdenacao = 'data' ) then
                stSql := stSql || ' ORDER BY to_date(stData,''dd/mm/yyyy''), entidade, empenho, exercicio, cgm, razao_social, cod_nota, ordem, conta, nome_conta';
            end if;

            if (stOrdenacao = 'credor' ) then
                stSql := stSql || ' ORDER BY to_date(stData,''dd/mm/yyyy''), razao_social, entidade, empenho, exercicio, cgm, cod_nota, ordem, conta, nome_conta';
            end if;

            if (stOrdenacao = 'credor_data' ) then
                stSql := stSql || ' ORDER BY razao_social, to_date(stData,''dd/mm/yyyy''), entidade, empenho, exercicio, cgm, cod_nota, ordem, conta, nome_conta';
            end if;
    
    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    IF (stSituacao = '2') THEN
        DROP TABLE tmp_pago;
    END IF;
    
    IF (stSituacao = '5') THEN
        DROP TABLE tmp_estornado;
    END IF;
          
    RETURN;
END;
$$ language 'plpgsql';
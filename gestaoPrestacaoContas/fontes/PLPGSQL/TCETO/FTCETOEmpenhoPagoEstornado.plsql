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
    $Id: FTCETOEmpenhoPagoEstornado.plsql 61292 2014-12-30 16:03:12Z michel $

* $Revision: $
* $Name: $
* $Author: $
* $Date: $
*
*/
CREATE OR REPLACE FUNCTION tceto.empenho_pago_estornado(varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    ALIAS FOR $1;
    stDtInicial    ALIAS FOR $2;
    stDtFinal      ALIAS FOR $3;
    stCodEntidades ALIAS FOR $4;
    stOrdenacao    ALIAS FOR $5;
    stSql          VARCHAR := '';
    reRegistro     RECORD;

BEGIN
        stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
          SELECT p.cod_entidade as cod_entidade
               , p.cod_nota as cod_nota
               , p.exercicio_liquidacao as exercicio_liquidacao
               , p.timestamp as timestamp
               , pa.cod_plano as cod_plano
               , pc.nom_conta as nom_conta
               , mb.num_banco AS cod_banco
               , ma.num_agencia AS cod_agencia
               , cpb.conta_corrente
               , ''+''::VARCHAR AS sinal
            FROM contabilidade.pagamento p
               , contabilidade.lancamento_empenho le
               , contabilidade.conta_credito cc
               , contabilidade.plano_analitica pa
               , contabilidade.plano_conta pc
               , empenho.nota_liquidacao_conta_pagadora nlcp
               , contabilidade.plano_banco cpb
               , monetario.conta_corrente mcc
               , monetario.agencia ma
               , monetario.banco mb
            WHERE
                --Ligação PAGAMENTO : LANCAMENTO EMPENHO
                    p.cod_entidade      IN (' || stCodEntidades || ')
                AND p.exercicio     = ''' || stExercicio || '''
                AND p.cod_lote = le.cod_lote
                AND p.tipo = le.tipo
                AND p.sequencia = le.sequencia
                AND p.exercicio = le.exercicio
                AND p.cod_entidade = le.cod_entidade
                AND le.estorno = false

                AND to_date(to_char(p.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                    BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                        AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
                AND le.cod_lote = cc.cod_lote
                AND le.tipo = cc.tipo
                AND le.exercicio = cc.exercicio
                AND le.cod_entidade = cc.cod_entidade
                AND le.sequencia = cc.sequencia
                
                AND nlcp.timestamp = p.timestamp 
                AND nlcp.exercicio_liquidacao = p.exercicio_liquidacao 
                AND nlcp.cod_entidade = p.cod_entidade
                AND nlcp.cod_nota = p.cod_nota

                --Ligação CONTA_CREDITO : PLANO ANALITICA
                AND nlcp.cod_plano = pa.cod_plano
                AND nlcp.exercicio = pa.exercicio

               --Ligação PLANO ANALITICA : PLANO CONTA
                AND pa.cod_conta = pc.cod_conta
                AND pa.exercicio = pc.exercicio
                
               --Ligação PLANO BANCO : PLANO ANALITICA
                AND cpb.cod_plano = pa.cod_plano
                AND cpb.exercicio = pa.exercicio
               --Ligação PLANO BANCO : monetario.conta_corrente()
                AND mcc.cod_banco          = cpb.cod_banco
                AND mcc.cod_agencia        = cpb.cod_agencia
                AND mcc.cod_conta_corrente = cpb.cod_conta_corrente
               --Ligação MONETARIO CONTA CORRENTE : MONETARIO AGENCIA
                AND ma.cod_banco = mcc.cod_banco
                AND ma.cod_agencia = mcc.cod_agencia
               --Ligação MONETARIO AGENCIA : MONETARIO BANCO
               AND mb.cod_banco = ma.cod_banco

        );
        CREATE INDEX idx_tmp_pago ON tmp_pago (cod_entidade, cod_nota, exercicio_liquidacao, timestamp);
        ';
        
        EXECUTE stSql;
        
        stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
                   select nota_liquidacao_paga_anulada.cod_entidade
                        , nota_liquidacao_paga_anulada.cod_nota
                        , nota_liquidacao_paga_anulada.exercicio as exercicio_liquidacao
                        , nota_liquidacao_paga_anulada.timestamp
                        , nota_liquidacao_paga_anulada.timestamp_anulada
                        , nota_liquidacao_paga_anulada.vl_anulado as vl_anulado
                        , plano_analitica.cod_plano as cod_plano
                        , plano_conta.nom_conta as nom_conta
                        , mb.num_banco AS cod_banco
                        , ma.num_agencia AS cod_agencia
                        , plano_banco.conta_corrente
                        , ''-''::VARCHAR AS sinal
                    from empenho.nota_liquidacao_paga_anulada
                    join contabilidade.pagamento_estorno
                      on pagamento_estorno.exercicio_liquidacao = nota_liquidacao_paga_anulada.exercicio
                     and pagamento_estorno.cod_entidade 	    = nota_liquidacao_paga_anulada.cod_entidade
                     and pagamento_estorno.cod_nota 	    = nota_liquidacao_paga_anulada.cod_nota
                     and pagamento_estorno.timestamp 	    = nota_liquidacao_paga_anulada.timestamp
                     and pagamento_estorno.timestamp_anulada    = nota_liquidacao_paga_anulada.timestamp_anulada
                    join contabilidade.pagamento
                      on pagamento.exercicio    = pagamento_estorno.exercicio
                     and pagamento.cod_entidade = pagamento_estorno.cod_entidade
                     and pagamento.sequencia    = pagamento_estorno.sequencia
                     and pagamento.tipo         = pagamento_estorno.tipo
                     and pagamento.cod_lote     = pagamento_estorno.cod_lote
                    join contabilidade.lancamento_empenho
                      on lancamento_empenho.exercicio    = pagamento.exercicio
                     and lancamento_empenho.cod_lote     = pagamento.cod_lote
                     and lancamento_empenho.tipo 	     = pagamento.tipo
                     and lancamento_empenho.sequencia    = pagamento.sequencia
                     and lancamento_empenho.cod_entidade = pagamento.cod_entidade
                    join contabilidade.conta_credito
                      on lancamento_empenho.cod_lote     = conta_credito.cod_lote
                     and lancamento_empenho.tipo 	     = conta_credito.tipo
                     and lancamento_empenho.exercicio    = conta_credito.exercicio
                     and lancamento_empenho.cod_entidade = conta_credito.cod_entidade
                     and lancamento_empenho.sequencia    = conta_credito.sequencia
                    
                    JOIN empenho.nota_liquidacao_conta_pagadora AS nlcp
                      on (nlcp.timestamp = pagamento.timestamp 
                     AND  nlcp.exercicio_liquidacao = pagamento.exercicio_liquidacao 
                     AND  nlcp.cod_entidade = pagamento.cod_entidade
                     AND  nlcp.cod_nota = pagamento.cod_nota)
                     
                    join contabilidade.plano_analitica
                      on nlcp.cod_plano = plano_analitica.cod_plano
                     and nlcp.exercicio = plano_analitica.exercicio
                     
                    JOIN contabilidade.plano_banco
                      ON plano_banco.exercicio = plano_analitica.exercicio
                     AND plano_banco.cod_plano = plano_analitica.cod_plano
                     
                    join contabilidade.plano_conta
                      on plano_conta.cod_conta = plano_analitica.cod_conta
                     and plano_conta.exercicio = plano_analitica.exercicio
                     
                    JOIN monetario.conta_corrente AS mcc
                      ON mcc.cod_banco          = plano_banco.cod_banco
                     AND mcc.cod_agencia        = plano_banco.cod_agencia
                     AND mcc.cod_conta_corrente = plano_banco.cod_conta_corrente
                    JOIN monetario.agencia AS ma
                      ON ma.cod_banco = mcc.cod_banco
                     AND ma.cod_agencia = mcc.cod_agencia
                    JOIN monetario.banco AS mb
                      ON mb.cod_banco = ma.cod_banco

                   where to_date(to_char(nota_liquidacao_paga_anulada.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                         BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                             AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                    AND pagamento.cod_entidade IN (' || stCodEntidades || ')
                    )';

        EXECUTE stSql;

        stSql := '
            SELECT entidade
                 , descricao_categoria
                 , nom_tipo
                 , empenho
                 , exercicio
                 , cgm
                 , razao_social
                 , cod_nota
                 , exercicio_liquidacao
                 , dt_liquidacao
                 , stData
                 , ordem
                 , conta
                 , coalesce(nome_conta,''NÃO INFORMADO'')
                 , valor
                 , vl_anulado
                 , ( valor - vl_anulado ) as vl_liquido
                 , descricao
                 , CAST(substr(recurso, 0, 35) as VARCHAR) as recurso
                 , despesa
                 , cod_banco
                 , cod_agencia
                 , conta_corrente
                 , sinal::VARCHAR
                 , dt_empenho
                 , num_documento
                 , tipo_pagamento
                 , recurso_vinculado
            FROM(

            SELECT e.cod_entidade as entidade
                 , e.cod_empenho as empenho
                 , e.exercicio as exercicio
                 , pe.cgm_beneficiario as cgm
                 , cgm.nom_cgm as razao_social
                 , cast(pe.descricao as varchar ) as descricao
                 , categoria_empenho.descricao as descricao_categoria
                 , tipo_empenho.nom_tipo
                 
                 , CASE WHEN tmp_estornado.timestamp_anulada IS NOT NULL THEN
                        to_char(tmp_estornado.timestamp_anulada,''dd/mm/yyyy'')
                   ELSE
                        to_char(nlp.timestamp,''dd/mm/yyyy'') 
                   END as stData
                 
                 , nlp.cod_nota as cod_nota
                 , nl.exercicio AS exercicio_liquidacao
                 , nl.dt_liquidacao AS dt_liquidacao
                 , nlp.vl_pago as valor
                 , pl.cod_ordem as ordem
                 , tmp.cod_plano as conta
                 , tmp.nom_conta as nome_conta
                 , coalesce(tmp_estornado.vl_anulado,0.00) as vl_anulado
                 , tmp.cod_banco
                 , tmp.cod_agencia
                 , tmp.conta_corrente
                 , tmp.sinal::VARCHAR
                 , e.dt_empenho
                 , ped_d_cd.nom_recurso as recurso
                 , ped_d_cd.cod_estrutural as despesa
                 , cheque_emissao_ordem_pagamento.num_cheque AS num_documento
                 , pagamento_tipo_pagamento.cod_tipo_pagamento as tipo_pagamento
                 , ped_d_cd.cod_recurso as recurso_vinculado
                 
            FROM empenho.empenho     as e 
            
            JOIN empenho.pre_empenho as pe
                 ON pe.exercicio        = e.exercicio
                AND pe.cod_pre_empenho  = e.cod_pre_empenho
            
            JOIN empenho.historico   as h
                 ON h.cod_historico    = pe.cod_historico
                AND h.exercicio        = pe.exercicio
            
            JOIN sw_cgm as cgm
                ON pe.cgm_beneficiario = cgm.numcgm 
            
            JOIN empenho.categoria_empenho
                 ON categoria_empenho.cod_categoria = e.cod_categoria
            
            JOIN empenho.tipo_empenho
                 ON tipo_empenho.cod_tipo = pe.cod_tipo

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            JOIN empenho.nota_liquidacao nl
                 ON e.exercicio = nl.exercicio_empenho
                AND e.cod_entidade = nl.cod_entidade
                AND e.cod_empenho = nl.cod_empenho

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            JOIN empenho.nota_liquidacao_paga AS nlp
                 ON nl.exercicio = nlp.exercicio
                AND nl.cod_nota = nlp.cod_nota
                AND nl.cod_entidade = nlp.cod_entidade
            
            --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
            JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                 ON nlp.cod_entidade = plnlp.cod_entidade
                AND nlp.cod_nota = plnlp.cod_nota
                AND nlp.exercicio = plnlp.exercicio_liquidacao
                AND nlp.timestamp = plnlp.timestamp
            
            --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA
            JOIN empenho.pagamento_liquidacao pl
                 ON pl.cod_ordem = plnlp.cod_ordem
                AND pl.exercicio = plnlp.exercicio
                AND pl.cod_entidade = plnlp.cod_entidade
                AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                AND pl.cod_nota = plnlp.cod_nota

            JOIN empenho.nota_liquidacao_conta_pagadora nlcp
                 ON nlp.cod_entidade = nlcp.cod_entidade
                AND nlp.cod_nota     = nlcp.cod_nota
                AND nlp.exercicio    = nlcp.exercicio_liquidacao
                AND nlp.timestamp    = nlcp.timestamp

            JOIN (  SELECT cod_entidade, cod_nota, exercicio_liquidacao, timestamp, cod_plano, nom_conta, cod_banco, cod_agencia, conta_corrente, sinal FROM tmp_pago   
                    UNION                                                                                
                    SELECT cod_entidade, cod_nota, exercicio_liquidacao, timestamp, cod_plano, nom_conta, cod_banco, cod_agencia, conta_corrente, sinal FROM tmp_estornado
                ) as tmp
                 ON nlp.cod_entidade = tmp.cod_entidade
                AND nlp.cod_nota = tmp.cod_nota
                AND nlp.exercicio = tmp.exercicio_liquidacao
                AND nlp.timestamp = tmp.timestamp

            JOIN empenho.ordem_pagamento
                 ON ordem_pagamento.exercicio       = pl.exercicio
                AND ordem_pagamento.cod_entidade    = pl.cod_entidade
                AND ordem_pagamento.cod_ordem       = pl.cod_ordem
            
            JOIN tesouraria.pagamento
                 ON pagamento.exercicio     = nlp.exercicio
                AND pagamento.cod_nota      = nlp.cod_nota
                AND pagamento.cod_entidade  = nlp.cod_entidade
                AND pagamento.timestamp     = nlp.timestamp

            LEFT JOIN tceto.pagamento_tipo_pagamento
                 ON pagamento_tipo_pagamento.cod_nota       = pagamento.cod_nota
                AND pagamento_tipo_pagamento.exercicio      = pagamento.exercicio
                AND pagamento_tipo_pagamento.cod_entidade   = pagamento.cod_entidade
                AND pagamento_tipo_pagamento.timestamp      = pagamento.timestamp
            
            LEFT JOIN tesouraria.cheque_emissao_ordem_pagamento
                 ON cheque_emissao_ordem_pagamento.cod_ordem    = ordem_pagamento.cod_ordem
                AND cheque_emissao_ordem_pagamento.exercicio    = ordem_pagamento.exercicio
                AND cheque_emissao_ordem_pagamento.cod_entidade = ordem_pagamento.cod_entidade
 
            LEFT JOIN tmp_estornado as tmp_estornado
                 ON tmp_estornado.cod_entidade         = tmp.cod_entidade
                AND tmp_estornado.cod_nota             = tmp.cod_nota
                AND tmp_estornado.exercicio_liquidacao = tmp.exercicio_liquidacao
                AND tmp_estornado.timestamp            = tmp.timestamp
                   

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
                        rec.cod_detalhamento,
                        rec.masc_recurso_red,
                        cd.cod_estrutural,
                        ppa.acao.num_acao,
                        programa.num_programa
                    FROM
                        empenho.pre_empenho_despesa as ped, 
                        orcamento.despesa           as d
                        JOIN orcamento.recurso(''' || stExercicio || ''') as rec
                        ON ( rec.cod_recurso = d.cod_recurso
                            AND rec.exercicio = d.exercicio )
                        JOIN orcamento.programa_ppa_programa
                          ON programa_ppa_programa.cod_programa = d.cod_programa
                         AND programa_ppa_programa.exercicio   = d.exercicio
                        JOIN ppa.programa
                          ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                        JOIN orcamento.pao_ppa_acao
                          ON pao_ppa_acao.num_pao = d.num_pao
                         AND pao_ppa_acao.exercicio = d.exercicio
                        JOIN ppa.acao 
                          ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                        ,orcamento.conta_despesa     as cd
                    WHERE
                        ped.exercicio      = ''' || stExercicio || '''   AND
                        ped.cod_despesa    = d.cod_despesa and 
                        ped.exercicio      = d.exercicio   and ';

               stSql := stSql || '
                        ped.cod_conta      = cd.cod_conta  and 
                        ped.exercicio      = cd.exercicio
            ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho

            WHERE
                    e.exercicio         = ''' || stExercicio || '''
                AND e.cod_entidade      IN (' || stCodEntidades || ')
                     
                ORDER BY 
                    to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'')
                    , e.cod_entidade
                    , e.cod_empenho
                    , e.exercicio 
                    , nlp.cod_nota
                    , pl.cod_ordem
                    , tmp.cod_plano
                    , tmp.nom_conta
                    , pe.cgm_beneficiario
                    , cgm.nom_cgm
            ) AS tbl where valor <> ''0.00'' ';

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

    DROP TABLE tmp_pago;
    DROP TABLE tmp_estornado;
    
    RETURN;
END;
$$ language 'plpgsql';

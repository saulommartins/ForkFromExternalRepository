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

    $Id: FTCETOEmpenhoPagoEstornadoRestos.plsql 61292 2014-12-30 16:03:12Z michel $

*/
CREATE OR REPLACE FUNCTION tceto.empenho_pago_estornado_restos(varchar,varchar,varchar,varchar)
RETURNS SETOF RECORD AS $$
DECLARE
    stDtInicial                 ALIAS FOR $1;
    stDtFinal                   ALIAS FOR $2;
    stCodEntidades              ALIAS FOR $3;
    stSituacao                  ALIAS FOR $4;
    stExercicio         VARCHAR   := '';
    stSql               VARCHAR   := '';
    stSqlExercicio      VARCHAR   := '';
    stExercicioAtual    VARCHAR   := '';
    reRegistro          RECORD;
    reReg               RECORD;

BEGIN

    CREATE TEMPORARY TABLE tmp_empenhos (
        entidade            integer,
        empenho             integer,
        exercicio           char(4),
        credor              varchar,
        cod_estrutural      varchar,
        cod_nota            integer,
        exercicio_liquidacao char(4),
        dt_liquidacao       date,
        data                text,
        conta               integer,
        banco               varchar,
        valor               numeric,
        cod_banco           varchar,
        cod_agencia         varchar, 
        conta_corrente      varchar,
        sinal               varchar,
        dt_empenho          date,
        ordem               integer,
        num_documento       varchar,
        tipo_pagamento      integer,        
        recurso_vinculado   integer
    );

    if (stSituacao = '1') then
        stSql := 'CREATE TEMPORARY TABLE tmp_pago_rp AS (
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
            AND to_date(to_char(p.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                    BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                        AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
            AND p.cod_lote = le.cod_lote
            AND p.tipo = le.tipo
            AND p.sequencia = le.sequencia
            AND p.exercicio = le.exercicio
            AND p.cod_entidade = le.cod_entidade
            AND le.estorno = false

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
            AND cc.cod_plano = pa.cod_plano
            AND cc.exercicio = pa.exercicio
            
            --Ligação PLANO ANALITICA : PLANO CONTA
            AND pa.cod_conta = pc.cod_conta
            AND pa.exercicio = pc.exercicio

            --Ligação PLANO BANCO : PLANO ANALITICA
            AND cpb.cod_plano = pa.cod_plano
            AND cpb.exercicio = pa.exercicio
            --Ligação PLANO BANCO : MONETARIO CONTA CORRENTE
            AND mcc.cod_banco          = cpb.cod_banco
            AND mcc.cod_agencia        = cpb.cod_agencia
            AND mcc.cod_conta_corrente = cpb.cod_conta_corrente
            --Ligação MONETARIO CONTA CORRENTE : MONETARIO AGENCIA
            AND ma.cod_banco = mcc.cod_banco
            AND ma.cod_agencia = mcc.cod_agencia
            --Ligação MONETARIO AGENCIA : MONETARIO BANCO
            AND mb.cod_banco = ma.cod_banco
        )';
        EXECUTE stSql;
    end if;

    if (stSituacao = '2') then
        stSql := 'CREATE TEMPORARY TABLE tmp_estornado_rp AS (
         SELECT p.cod_entidade as cod_entidade
              , p.cod_nota as cod_nota
              , p.exercicio_liquidacao as exercicio_liquidacao
              , p.timestamp as timestamp
              , pa.cod_plano as cod_plano
              , pc.nom_conta as nom_conta
              , mb.num_banco AS cod_banco
              , ma.num_agencia AS cod_agencia
              , cpb.conta_corrente
              , ''-''::VARCHAR AS sinal
           FROM contabilidade.pagamento p
              , contabilidade.lancamento_empenho le
              , contabilidade.conta_debito cd
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
            AND to_date(to_char(p.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                    BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                        AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
            AND p.cod_lote = le.cod_lote
            AND p.tipo = le.tipo
            AND p.sequencia = le.sequencia
            AND p.exercicio = le.exercicio
            AND p.cod_entidade = le.cod_entidade
            AND le.estorno = true

            --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
            AND le.cod_lote = cd.cod_lote
            AND le.tipo = cd.tipo
            AND le.exercicio = cd.exercicio
            AND le.cod_entidade = cd.cod_entidade
            AND le.sequencia = cd.sequencia

            AND nlcp.timestamp = p.timestamp 
            AND nlcp.exercicio_liquidacao = p.exercicio_liquidacao 
            AND nlcp.cod_entidade = p.cod_entidade
            AND nlcp.cod_nota = p.cod_nota

            --Ligação CONTA_CREDITO : PLANO ANALITICA
            AND cd.cod_plano = pa.cod_plano
            AND cd.exercicio = pa.exercicio
            
            --Ligação PLANO ANALITICA : PLANO CONTA
            AND pa.cod_conta = pc.cod_conta
            AND pa.exercicio = pc.exercicio

            --Ligação PLANO BANCO : PLANO ANALITICA
            AND cpb.cod_plano = pa.cod_plano
            AND cpb.exercicio = pa.exercicio
            
            --Ligação PLANO BANCO : MONETARIO CONTA CORRENTE
            AND mcc.cod_banco          = cpb.cod_banco
            AND mcc.cod_agencia        = cpb.cod_agencia
            AND mcc.cod_conta_corrente = cpb.cod_conta_corrente
            --Ligação MONETARIO CONTA CORRENTE : MONETARIO AGENCIA
            AND ma.cod_banco = mcc.cod_banco
            AND ma.cod_agencia = mcc.cod_agencia
            --Ligação MONETARIO AGENCIA : MONETARIO BANCO
            AND mb.cod_banco = ma.cod_banco
        )';
        EXECUTE stSql;
    end if;

    stExercicioAtual := to_char(to_date(stDtInicial, 'dd/mm/yyyy'), 'yyyy');

    stSql := ' INSERT INTO tmp_empenhos
                SELECT entidade
                     , empenho
                     , exercicio
                     , credor
                     , cod_estrutural
                     , cod_nota
                     , exercicio_liquidacao
                     , dt_liquidacao
                     , data
                     , conta
                     , nome_conta
                     , valor
                     , cod_banco
                     , cod_agencia
                     , conta_corrente
                     , sinal
                     , dt_empenho
                     , ordem
                     , num_documento
                     , tipo_pagamento
                     , recurso_vinculado
                  FROM ( SELECT e.cod_entidade as entidade
                              , e.cod_empenho as empenho
                              , e.exercicio as exercicio
                              , sw_cgm.nom_cgm as credor, 
                        CASE WHEN (pe.implantado = FALSE) THEN ped_d_cd.cod_estrutural
                        ELSE rpe.cod_estrutural END as cod_estrutural';

   if (stSituacao = '1') then
           stSql := stSql || ', to_char(nlp.timestamp,''dd/mm/yyyy'') as data
                              , nlp.cod_nota as cod_nota
                              , nlp.vl_pago as valor
                              , tmp.cod_plano as conta
                              , tmp.nom_conta as nome_conta
                              , tmp.cod_banco
                              , tmp.cod_agencia
                              , tmp.conta_corrente
                              , tmp.sinal
                              , e.dt_empenho
                              , pl.cod_ordem as ordem
                              , cheque_emissao_ordem_pagamento.num_cheque AS num_documento 
                              , pagamento_tipo_pagamento.cod_tipo_pagamento::integer AS tipo_pagamento ';
   end if;
            
   if (stSituacao = '2') then
           stSql := stSql || ', to_char(nlpa.timestamp_anulada,''dd/mm/yyyy'') as data
                              , nlpa.cod_nota as cod_nota
                              , nlpa.vl_anulado as valor
                              , tmp.cod_plano as conta
                              , tmp.nom_conta as nome_conta
                              , tmp.cod_banco
                              , tmp.cod_agencia
                              , tmp.conta_corrente
                              , tmp.sinal
                              , e.dt_empenho
                              , pl.cod_ordem as ordem
                              , cheque_emissao_ordem_pagamento.num_cheque AS num_documento 
                              , pagamento_tipo_pagamento.cod_tipo_pagamento AS tipo_pagamento ';
   end if;

            stSql := stSql || '
                              , nl.exercicio AS exercicio_liquidacao
                              , nl.dt_liquidacao AS dt_liquidacao
                              , CASE WHEN (pe.implantado = FALSE) THEN ped_d_cd.cod_recurso
                                ELSE rpe.recurso
                                END AS recurso_vinculado
                 
                           FROM empenho.empenho  as e
                              , empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                              
                           
                           ';

                if (stSituacao = '1') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl
                    , empenho.nota_liquidacao_paga nlp
                    
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
                    
                    , tmp_pago_rp tmp
                    ';
                end if;
                
                if (stSituacao = '2') then
                    stSql := stSql || '
                    , empenho.nota_liquidacao nl                     
                    , empenho.nota_liquidacao_paga nlp

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
                    
                    , empenho.nota_liquidacao_paga_anulada nlpa
                    , tmp_estornado_rp tmp
                    ';
                end if;

             stSql := stSql || '
              , sw_cgm
              , empenho.pre_empenho as pe
                LEFT OUTER JOIN empenho.restos_pre_empenho as rpe ON pe.exercicio = rpe.exercicio AND pe.cod_pre_empenho = rpe.cod_pre_empenho
                LEFT OUTER JOIN (
                    SELECT
                        ped.exercicio, ped.cod_pre_empenho, d.num_pao, d.num_orgao,d.num_unidade, d.cod_recurso, cd.cod_estrutural
                    FROM
                          empenho.pre_empenho_despesa as ped
                        , orcamento.despesa as d
                          JOIN orcamento.recurso as r
                            ON ( d.cod_recurso = r.cod_recurso AND d.exercicio = r.exercicio )
                        , orcamento.conta_despesa as cd
                    WHERE
                        ped.cod_despesa = d.cod_despesa and ped.exercicio = d.exercicio and ped.cod_conta = cd.cod_conta and d.exercicio = cd.exercicio
                ) as ped_d_cd ON pe.exercicio = ped_d_cd.exercicio AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
              , empenho.pagamento_liquidacao pl
                
                JOIN empenho.ordem_pagamento
                     ON ordem_pagamento.exercicio       = pl.exercicio
                    AND ordem_pagamento.cod_entidade    = pl.cod_entidade
                    AND ordem_pagamento.cod_ordem       = pl.cod_ordem
            
                LEFT JOIN tesouraria.cheque_emissao_ordem_pagamento
                     ON cheque_emissao_ordem_pagamento.cod_ordem    = ordem_pagamento.cod_ordem
                    AND cheque_emissao_ordem_pagamento.exercicio    = ordem_pagamento.exercicio
                    AND cheque_emissao_ordem_pagamento.cod_entidade = ordem_pagamento.cod_entidade

            WHERE
                e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho
                AND e.cod_entidade      IN (' || stCodEntidades || ') 
                AND pe.cgm_beneficiario = sw_cgm.numcgm ';                                        

                if (stSituacao = '1') then
                    stSql := stSql || '

                       --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                        AND e.exercicio = nl.exercicio_empenho
                        AND e.cod_entidade = nl.cod_entidade
                        AND e.cod_empenho = nl.cod_empenho

                        --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
                        AND nl.exercicio = nlp.exercicio
                        AND nl.cod_nota = nlp.cod_nota
                        AND nl.cod_entidade = nlp.cod_entidade
                        AND to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')

                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                        AND nlp.cod_entidade = tmp.cod_entidade
                        AND nlp.cod_nota = tmp.cod_nota
                        AND nlp.exercicio = tmp.exercicio_liquidacao
                        AND nlp.timestamp = tmp.timestamp
                    ';
                end if;

                if (stSituacao = '2') then
                    stSql := stSql || '
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
                        AND to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'')

                        --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                        AND nlp.exercicio = tmp.exercicio_liquidacao
                        AND nlp.cod_entidade = tmp.cod_entidade
                        AND nlp.cod_nota = tmp.cod_nota
                        AND nlp.timestamp = tmp.timestamp
                        
                    ';
                end if;
                
                stSql := stSql || '
                
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
                        
                        --SEPARAR SÓ RESTOS
                        AND (
                                rpe.cod_pre_empenho IS NOT NULL
                              OR
                                e.exercicio < '|| quote_literal(stExercicioAtual) ||'
                            )
                ';

            stSql := stSql || ' ORDER BY ';

            if (stSituacao = '1') then
                    stSql := stSql || 'to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy''),';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || 'to_date(to_char(nlpa.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy''),';
            end if;

            stSql := stSql || 'e.cod_entidade , e.cod_empenho , e.exercicio ';

            if (stSituacao = '1') then
                    stSql := stSql || ',nlp.cod_nota, tmp.cod_plano, tmp.nom_conta';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || ' ,nlpa.cod_nota, tmp.cod_plano, tmp.nom_conta';
            end if;

            stSql := stSql || ') as tbl where valor <> ''0.00'' ';
            stSql := stSql || ' ORDER BY to_date(data,''dd/mm/yyyy''), entidade, empenho, exercicio, cod_nota, conta, nome_conta';
    EXECUTE stSql;

    stSql :=  ' SELECT entidade                                    
                     , empenho                                     
                     , exercicio                                   
                     , credor                                      
                     , cod_estrutural
                     , cod_nota
                     , exercicio_liquidacao
                     , dt_liquidacao
                     , data                                        
                     , conta                                       
                     , banco                                       
                     , valor
                     , cod_banco
                     , cod_agencia
                     , conta_corrente
                     , sinal
                     , dt_empenho
                     , ordem
                     , num_documento
                     , tipo_pagamento
                     , recurso_vinculado
                  FROM tmp_empenhos
            ';
            
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    if (stSituacao = '1') then
        DROP TABLE tmp_pago_rp;
    end if;

    if (stSituacao = '2') then
        DROP TABLE tmp_estornado_rp;
    end if;

    DROP TABLE tmp_empenhos;

    RETURN ;
END;
$$
LANGUAGE 'plpgsql';


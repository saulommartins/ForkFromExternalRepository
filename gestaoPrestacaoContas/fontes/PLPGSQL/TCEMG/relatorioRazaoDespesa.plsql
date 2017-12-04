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
    $Id:$

* $Revision:$
* $Name:  $
* $Author:Lisiane Morais $
* $Date:$
*
*/
CREATE OR REPLACE FUNCTION tcemg.razao_despesa(varchar,varchar,varchar,varchar,varchar, varchar, varchar,varchar,varchar, varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                 ALIAS FOR $1;
    stDtInicial                 ALIAS FOR $2;
    stDtFinal                   ALIAS FOR $3;
    stCodEntidades              ALIAS FOR $4;
    stCodOrgao                  ALIAS FOR $5;
    stCodUnidade                ALIAS FOR $6;
    stCodPao                    ALIAS FOR $7;
    stCodRecurso                ALIAS FOR $8;
    stSituacao                  ALIAS FOR $9;
    stTipoRelatorio             ALIAS FOR $10; 

    stSql                       VARCHAR   := '';
    nuValorTotal            NUMERIC   := 0;
    nuValorTotalAnulado     NUMERIC   := 0;
   
   
   -- nuTotalValorEmpenhado       NUMERIC   := 0;
   -- nuTotalValorEmpenhadoAnulado NUMERIC   := 0;
   -- nuTotalValorLiquidado        NUMERIC   := 0;
   -- nuTotalValorLiquidadoAnulado NUMERIC   := 0;
    
    reRegistro          RECORD;

BEGIN
    --INICIO TABELA EMPENHOS EMPENHADOS
    IF( stSituacao = '1' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_empenhos AS (
                  SELECT * 
                    FROM tcemg.empenho_empenhado_liquidado(''' || stExercicio || ''',
                                                          ''' || stDtInicial || ''',
                                                          ''' || stDtFinal || ''',
                                                          ''' || stCodEntidades || ''',
                                                          ''' || stCodOrgao ||''' ,
                                                          ''' || stCodUnidade ||''',
                                                          ''' || stCodPao ||''',
                                                          ''' || stCodRecurso ||''',
                                                          ''' || stSituacao ||''',
                                                          ''' || stTipoRelatorio ||'''
                                                         ) as retorno( entidade            integer,                                           
                                                                      descricao_categoria varchar,                                           
                                                                      nom_tipo            varchar,                                           
                                                                      empenho             integer,                                           
                                                                      exercicio           char(4),                                           
                                                                      cgm                 integer,                                           
                                                                      credor              text,                                           
                                                                      cod_nota            integer,                                           
                                                                      stData                text,                                              
                                                                      ordem               integer,                                           
                                                                      conta               integer,                                           
                                                                      nome_conta          varchar,                                           
                                                                      valor               numeric,                                           
                                                                      valor_anulado       numeric,                                           
                                                                      descricao           varchar,                                           
                                                                      recurso             varchar,                                           
                                                                      despesa             text,
                                                                      banco               varchar, 
                                                                      num_documento       varchar,
                                                                      dt_empenho          text,
                                                                      dotacao             text,
                                                                      cod_recurso         integer,
                                                                      num_orgao           integer,
                                                                      num_unidade         integer
                                                        )
        )';
        EXECUTE stSql; 
    END IF; ----FIM RELATORIO EMPENHOS EMPENHADOS
    
      --INICIO TABELA EMPENHOS PAGOS
    IF( stSituacao = '2' ) THEN
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
                contabilidade.plano_conta pc,
                empenho.nota_liquidacao_conta_pagadora nlcp
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
        );
        CREATE INDEX idx_tmp_pago ON tmp_pago (cod_entidade, cod_nota, exercicio_liquidacao, timestamp);
        ';
        EXECUTE stSql;
        
        stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
                   select nota_liquidacao_paga_anulada.cod_entidade
                        , nota_liquidacao_paga_anulada.cod_nota
                        , nota_liquidacao_paga_anulada.exercicio as exercicio_liquidacao
                        , nota_liquidacao_paga_anulada.timestamp
                        , sum (nota_liquidacao_paga_anulada.vl_anulado) as vl_anulado
                        , plano_analitica.cod_plano as cod_plano
                        , plano_conta.nom_conta as nom_conta
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
                     
                    join contabilidade.plano_conta
                      on plano_conta.cod_conta = plano_analitica.cod_conta
                     and plano_conta.exercicio = plano_analitica.exercicio
                   where to_date(to_char(nota_liquidacao_paga_anulada.timestamp_anulada,''dd/mm/yyyy''),''dd/mm/yyyy'') 
                         BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') 
                             AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                group by nota_liquidacao_paga_anulada.cod_entidade
                       , nota_liquidacao_paga_anulada.cod_nota
                       , nota_liquidacao_paga_anulada.exercicio
                       , nota_liquidacao_paga_anulada.timestamp
                       , plano_analitica.cod_plano
                       , plano_conta.nom_conta     )';
        EXECUTE stSql;
     
        stSql := 'CREATE TEMPORARY TABLE tmp_empenhos AS (
                   SELECT entidade
                        , empenho
                        , exercicio
                        , cgm
                        , cgm||'' - ''||razao_social::varchar AS credor
                        , dt_empenho
                        , valor 
                        , vl_anulado AS valor_anulado
                        , descricao
                        , tbl.cod_recurso
                        , recurso::varchar
                        , despesa || '' - '' || descricao_despesa::varchar AS despesa
                        , dotacao
                        , stData 
                        , ''''::VARCHAR AS banco
                        , 0 AS cod_recurso_banco
                        , num_documento
                        , cod_nota
                        , num_orgao
                        , num_unidade
                        , conta
                     FROM( SELECT e.cod_entidade as entidade
                                , e.cod_empenho as empenho
                                , e.exercicio as exercicio
                                , pe.cgm_beneficiario as cgm
                                , cgm.nom_cgm as razao_social
                                , cast(pe.descricao as varchar ) as descricao
                                , categoria_empenho.descricao as descricao_categoria
                                , tipo_empenho.nom_tipo
                                , to_char(nlp.timestamp,''dd/mm/yyyy'') as stData 
                                , nlp.cod_nota as cod_nota
                                , sum(nlp.vl_pago) as valor
                                , pl.cod_ordem as ordem
                                , tmp.cod_plano as conta
                                , tmp.nom_conta as nome_conta
                                , sum(coalesce(tmp_estornado.vl_anulado,0.00)) as vl_anulado 
                                , ped_d_cd.nom_recurso as recurso
                                , ped_d_cd.cod_estrutural as despesa
                                , ped_d_cd.descricao AS descricao_despesa
                                , pagamento_tipo_documento.num_documento
                                , to_char(e.dt_empenho ,''dd/mm/yyyy'') as dt_empenho
                                , ped_d_cd.cod_recurso
                                , ped_d_cd.num_unidade
                                , ped_d_cd.num_orgao
                                , ped_d_cd.dotacao
                             FROM empenho.empenho     as e 
                                , empenho.categoria_empenho
                                , empenho.tipo_empenho
                                , empenho.historico   as h 
                                , empenho.nota_liquidacao nl
                                , empenho.nota_liquidacao_paga nlp
                       INNER JOIN ( SELECT cod_entidade
                                        , cod_nota
                                        , exercicio_liquidacao
                                        , timestamp
                                        , cod_plano
                                        , nom_conta
                                     FROM tmp_pago
                                    UNION
                                   SELECT cod_entidade
                                        , cod_nota
                                        , exercicio_liquidacao
                                        , timestamp
                                        , cod_plano
                                        , nom_conta
                                     FROM tmp_estornado
                                 ) as tmp
                               ON ( --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                                       nlp.cod_entidade = tmp.cod_entidade
                                   AND nlp.cod_nota = tmp.cod_nota
                                   AND nlp.exercicio = tmp.exercicio_liquidacao
                                   AND nlp.timestamp = tmp.timestamp
                                 )
                        LEFT JOIN tmp_estornado as tmp_estornado
                               ON ( --Ligação PAGAMENTO ESTORNADO : PAGAMENTO
                                       tmp_estornado.cod_entidade         = tmp.cod_entidade
                                   AND tmp_estornado.cod_nota             = tmp.cod_nota
                                   AND tmp_estornado.exercicio_liquidacao = tmp.exercicio_liquidacao
                                   AND tmp_estornado.timestamp            = tmp.timestamp
                                 )
                         LEFT JOIN tcemg.pagamento_tipo_documento
                                ON pagamento_tipo_documento.exercicio    = nlp.exercicio
                               AND pagamento_tipo_documento.cod_nota     = nlp.cod_nota
                               AND pagamento_tipo_documento.cod_entidade = nlp.cod_entidade
                               AND pagamento_tipo_documento.timestamp    = nlp.timestamp
                     
                                 , empenho.pagamento_liquidacao_nota_liquidacao_paga plnlp
                                 , empenho.pagamento_liquidacao pl
                                 , empenho.nota_liquidacao_conta_pagadora nlcp
                                 , sw_cgm              as cgm
                                 , empenho.pre_empenho as pe
                   LEFT OUTER JOIN ( SELECT ped.exercicio, 
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
                                            cd.descricao, 
                                            programa.num_programa,
                                            d.cod_subfuncao,
                                            LPAD(d.num_orgao::VARCHAR, 2, ''0'')||''.''||LPAD(d.num_unidade::VARCHAR, 2, ''0'')||''.''||d.cod_funcao||''.''||d.cod_subfuncao||''.''||ppa.programa.num_programa||''.''||LPAD(d.num_pao::VARCHAR, 4, ''0'')||''.''||REPLACE(cd.cod_estrutural, ''.'', '''') AS dotacao
                                       FROM empenho.pre_empenho_despesa as ped, 
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
                                          , orcamento.conta_despesa     as cd
                                      WHERE ped.exercicio      = ''' || stExercicio || '''
                                        AND ped.cod_despesa    = d.cod_despesa
                                        AND ped.exercicio      = d.exercicio
                                        AND ped.cod_conta = cd.cod_conta AND
                                            ped.exercicio      = cd.exercicio
                                    ) as ped_d_cd
                                   ON pe.exercicio = ped_d_cd.exercicio
                                  AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
                             WHERE e.exercicio         = ''' || stExercicio || '''
                               AND e.exercicio         = pe.exercicio
                               AND e.cod_pre_empenho   = pe.cod_pre_empenho
                               AND e.cod_entidade      IN (' || stCodEntidades || ')
                               AND pe.cgm_beneficiario = cgm.numcgm 
                               AND h.cod_historico     = pe.cod_historico    
                               AND h.exercicio         = pe.exercicio   
                               AND categoria_empenho.cod_categoria = e.cod_categoria
                               AND tipo_empenho.cod_tipo = pe.cod_tipo

                            -- ###Ligação EMPENHO : NOTA LIQUIDAÇÃO
                               AND e.exercicio = nl.exercicio_empenho
                               AND e.cod_entidade = nl.cod_entidade
                               AND e.cod_empenho = nl.cod_empenho
                               
                               --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
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
                              
                               AND nlp.cod_entidade = nlcp.cod_entidade
                               AND nlp.cod_nota     = nlcp.cod_nota
                               AND nlp.exercicio    = nlcp.exercicio_liquidacao
                               AND nlp.timestamp    = nlcp.timestamp ';

                               if (stCodOrgao is not null and stCodOrgao<>'') then
                                   stSql := stSql || ' AND ped_d_cd.num_orgao = '|| stCodOrgao ||' ';
                               end if;
                              
                               if (stCodUnidade is not null and stCodUnidade<>'') then
                                   stSql := stSql || ' AND ped_d_cd.num_unidade = '|| stCodUnidade ||' ';
                               end if;
                              
                               if (stCodPao is not null and stCodPao<>'') then
                                   stSql := stSql || ' AND ped_d_cd.num_pao ='|| stCodPao ||' ';
                               end if;
                               
                               if (stCodRecurso is not null and stCodRecurso<>'') then
                                   stSql := stSql || ' AND ped_d_cd.cod_recurso IN ('|| stCodRecurso ||') ';
                               end if;
                               
                               if (stTipoRelatorio = 'ensino_fundamental') then
                                   stSql := stSql || ' AND ped_d_cd.cod_subfuncao IN ( 361 ) ';
                               end if;
                               
                               if (stTipoRelatorio = 'gasto_25') then
                                  stSql := stSql || ' AND ped_d_cd.cod_subfuncao NOT IN ( 362,363,364 ) ';
                               end if;
               stSql := stSql || 
                         'GROUP BY to_char(nlp.timestamp,''dd/mm/yyyy'')
                                 , nlp.cod_nota
                                 , pl.cod_ordem
                                 , tmp.cod_plano
                                 , tmp.nom_conta
                                 , e.cod_entidade
                                 , e.cod_empenho
                                 , e.exercicio
                                 , pe.cgm_beneficiario
                                 , cgm.nom_cgm
                                 , pe.descricao
                                 , ped_d_cd.cod_estrutural
                                 , ped_d_cd.nom_recurso
                                 , categoria_empenho.descricao
                                 , tipo_empenho.nom_tipo
                                 , pagamento_tipo_documento.num_documento
                                 , e.dt_empenho
                                 , descricao_despesa
                                 , ped_d_cd.cod_recurso
                                 , ped_d_cd.num_orgao
                                 , ped_d_cd.num_unidade
                                 , ped_d_cd.dotacao
                            --    , ped_d_cd.cod_subfuncao
                          ORDER BY ped_d_cd.num_orgao
                                 , ped_d_cd.num_unidade
                                 , ped_d_cd.cod_recurso
                                 , to_date(to_char(nlp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'')
                                 , e.cod_entidade
                                 , e.cod_empenho
                                 , e.exercicio
                                 , nlp.cod_nota
                                 , pl.cod_ordem
                                 , tmp.cod_plano
                                 , tmp.nom_conta
                                 , pe.cgm_beneficiario
                                 , cgm.nom_cgm
                        ) as tbl where valor <> ''0.00'' 
                 ORDER BY num_orgao
                      , num_unidade
                      , cod_recurso
                      , to_date(dt_empenho,''dd/mm/yyyy'')
                      , entidade
                      , empenho
                      , exercicio
                      , cod_nota )';
        EXECUTE stSql;

        stSql := 'UPDATE tmp_empenhos
                     SET banco = conta_bancaria
                       , cod_recurso_banco = registros.cod_recurso_banco
                    FROM ( SELECT tmp_empenhos.conta
		                        , tmp_empenhos.exercicio 
		                        , conta_bancaria
                                , COALESCE (ctb.cod_recurso, 100) AS cod_recurso_banco
                             FROM tmp_empenhos  
                             JOIN ( SELECT distinct conta_corrente.num_conta_corrente
                                         , agencia.num_agencia
                                         , banco.num_banco
                                         , plano_banco.cod_plano
                                         , plano_banco.exercicio
                                         , num_banco||'' / ''||num_agencia||'' / ''||num_conta_corrente AS conta_bancaria
                                         , plano_recurso.cod_recurso AS cod_recurso
                                      FROM contabilidade.plano_banco                                 
                                      JOIN monetario.conta_corrente
                                        ON conta_corrente.cod_banco          = plano_banco.cod_banco
                                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                                       AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
	                                  JOIN monetario.agencia
                                        ON agencia.cod_banco   = conta_corrente.cod_banco
                                       AND agencia.cod_agencia = conta_corrente.cod_agencia
                                      JOIN monetario.banco
                                        ON banco.cod_banco = conta_corrente.cod_banco
                                 LEFT JOIN contabilidade.plano_recurso
                                        ON plano_recurso.exercicio = plano_banco.exercicio
                                       AND plano_recurso.cod_plano = plano_banco.cod_plano
                                INNER JOIN tmp_empenhos as tmp_empenhos_pago
                                        ON plano_banco.exercicio = tmp_empenhos_pago.exercicio
                                       AND plano_banco.cod_plano = tmp_empenhos_pago.conta
                                   ) as ctb
                               ON ctb.exercicio = tmp_empenhos.exercicio
                              AND ctb.cod_plano = tmp_empenhos.conta) as registros
                   WHERE tmp_empenhos.conta = registros.conta
	                 AND tmp_empenhos.exercicio = registros.exercicio ';
        EXECUTE stSql; 
    END IF; ----FIM RELATORIO EMPENHOS PAGOS
    
    --INICIO TABELA EMPENHOS LIQUIDADO
    IF( stSituacao = '3' ) THEN
        stSql := 'CREATE TEMPORARY TABLE tmp_empenhos AS (
                  SELECT * 
                    FROM tcemg.empenho_empenhado_liquidado(''' || stExercicio || ''',
                                                          ''' || stDtInicial || ''',
                                                          ''' || stDtFinal || ''',
                                                          ''' || stCodEntidades || ''',
                                                          ''' || stCodOrgao ||''' ,
                                                          ''' || stCodUnidade ||''',
                                                          ''' || stCodPao ||''',
                                                          ''' || stCodRecurso ||''',
                                                          ''' || stSituacao ||''',
                                                          ''' || stTipoRelatorio ||'''
                                                         ) as retorno( entidade            integer,                                           
                                                                      descricao_categoria varchar,                                           
                                                                      nom_tipo            varchar,                                           
                                                                      empenho             integer,                                           
                                                                      exercicio           char(4),                                           
                                                                      cgm                 integer,                                           
                                                                      credor              text,                                           
                                                                      cod_nota            integer,                                           
                                                                      stData                text,                                              
                                                                      ordem               integer,                                           
                                                                      conta               integer,                                           
                                                                      nome_conta          varchar,                                           
                                                                      valor               numeric,                                           
                                                                      valor_anulado       numeric,                                           
                                                                      descricao           varchar,                                           
                                                                      recurso             varchar,                                           
                                                                      despesa             text,
                                                                      banco               varchar, 
                                                                      num_documento       varchar,
                                                                      dt_empenho          text,
                                                                      dotacao             text,
                                                                      cod_recurso         integer,
                                                                      num_orgao           integer,
                                                                      num_unidade         integer
                                                        )
        )';
        EXECUTE stSql;
        ----TOTAL VALORES LIQUIDADO
        stSql := '  SELECT COALESCE(SUM(valor),0.0) AS valor_total
                            , COALESCE(SUM(valor_anulado),0.0) AS valor_total_anulado
                      FROM tmp_empenhos';
        FOR reRegistro IN EXECUTE stSql
        LOOP
           nuValorTotal := reRegistro.valor_total;
           nuValorTotalAnulado := reRegistro.valor_total_anulado;
        END LOOP;
        
        IF (nuValorTotal != 0.0) THEN 
           stSql := ' SELECT *
                        FROM empenho.fn_empenho_liquidado_total('''  || stExercicio || ''', ''' || stCodEntidades || ''', ''' || stDtInicial || ''', ''' || stDtFinal || ''') AS vl_total_liquidado';
           FOR reRegistro IN EXECUTE stSql
           LOOP
              nuValorTotal := reRegistro.vl_total_liquidado;
           END LOOP;
        END IF;
    END IF; ----FIM RELATORIO EMPENHOS LIQUIDADO
        
        IF( stSituacao != '3' ) THEN
            --TOTAL VALORES 
            stSql := ' SELECT COALESCE(SUM(valor),0.0) AS valor_total
                            , COALESCE(SUM(valor_anulado),0.0) AS valor_total_anulado
                        FROM tmp_empenhos ';
            EXECUTE stSql;  
    
            FOR reRegistro IN EXECUTE stSql
            LOOP
                nuValorTotal := reRegistro.valor_total;
                nuValorTotalAnulado := reRegistro.valor_total_anulado;
            END LOOP;
        END IF ;
  
        --RESULTADO FINAL
        stSql := ' SELECT entidade
                        , empenho
                        , exercicio
                        , cgm
                        , credor
                        , dt_empenho
                        , valor
                        , valor_anulado
                        , descricao
                        , cod_recurso
                        , recurso::varchar
                        , despesa
                        , dotacao
                        , stData
                        , banco
                        , num_documento
                        , cod_nota
                        , num_orgao
                        , num_unidade
                        , '|| nuValorTotal ||' AS vl_total
                        , '|| nuValorTotalAnulado ||' AS vl_total_anulado
                     FROM tmp_empenhos
              ';

       FOR reRegistro IN EXECUTE stSql
       LOOP
           RETURN next reRegistro;
       END LOOP;
    IF ( stSituacao = '2' ) THEN
       DROP TABLE tmp_pago;
       DROP TABLE tmp_estornado;
    END IF;
    
    DROP TABLE tmp_empenhos;
    
    RETURN;
END;
$$ language 'plpgsql';

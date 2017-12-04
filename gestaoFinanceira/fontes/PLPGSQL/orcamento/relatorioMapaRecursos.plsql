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
    * Script de função PLPGSQL - Relatório Despesas Municipais com Educação e Cultura.
    * Data de Criação: 10/09/2008


    * @author Eduardo Paculski Schitz

    $Id: $

*/

CREATE OR REPLACE FUNCTION orcamento.fn_mapa_recursos(VARCHAR, VARCHAR, VARCHAR, INTEGER, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio    	ALIAS FOR $1;
    stDataFinal    	ALIAS FOR $2;
    stCodEntidades  ALIAS FOR $3;
    inCodRecursoIni ALIAS FOR $4;
    inCodRecursoFim ALIAS FOR $5;
    
    reReg           RECORD;
    stSql 			VARCHAR := '''';
    stDataInicial   VARCHAR := '''';

BEGIN

    stDataInicial = '01/01/' || stExercicio;

    stSql := 'CREATE TEMPORARY TABLE tmp_saldo_inicial AS (
        SELECT * 
          FROM ( SELECT OE.entidade
                      , plano_recurso.cod_recurso
                      , SUM(COALESCE(valor_lancamento.vl_lancamento, 0.00)) as saldo_inicial
                   FROM contabilidade.lote
                   JOIN contabilidade.valor_lancamento
                     ON valor_lancamento.cod_lote     = lote.cod_lote
                    AND valor_lancamento.tipo         = lote.tipo
                    AND valor_lancamento.exercicio    = lote.exercicio
                    AND valor_lancamento.cod_entidade = lote.cod_entidade
                   JOIN contabilidade.conta_debito
                     ON conta_debito.cod_lote     = valor_lancamento.cod_lote
                    AND conta_debito.tipo         = valor_lancamento.tipo
                    AND conta_debito.sequencia    = valor_lancamento.sequencia
                    AND conta_debito.exercicio    = valor_lancamento.exercicio
                    AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                    AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                   JOIN contabilidade.plano_analitica
                     ON plano_analitica.cod_plano = conta_debito.cod_plano
                    AND plano_analitica.exercicio = conta_debito.exercicio
                   JOIN contabilidade.plano_conta
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                   JOIN contabilidade.plano_recurso
                     ON plano_recurso.cod_plano = plano_analitica.cod_plano
                    AND plano_recurso.exercicio = plano_analitica.exercicio
             INNER JOIN ( SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                               , OE.cod_entidade
                               , OE.exercicio
                            FROM orcamento.entidade as OE
                               , sw_cgm as CGM
                           WHERE OE.numcgm = CGM.numcgm
                        ) as OE 
                     ON OE.cod_entidade = valor_lancamento.cod_entidade   
                    AND OE.exercicio    = valor_lancamento.exercicio
                  WHERE conta_debito.exercicio    = ''' || stExercicio || '''
                    AND conta_debito.cod_entidade IN (' || stCodEntidades || ')
                    AND conta_debito.tipo_valor   = ''D''
                    AND lote.dt_lote              = to_date(''01/01/' || stExercicio || '''::varchar,''dd/mm/yyyy'')
                    AND lote.tipo                 = ''I'' ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;
            stSql := stSql || '
               GROUP BY OE.entidade
                      , plano_recurso.cod_recurso
            
              UNION ALL
            
                 SELECT OE.entidade
                      , plano_recurso.cod_recurso
                      , SUM(COALESCE(valor_lancamento.vl_lancamento, 0.00)) as saldo_inicial
                   FROM contabilidade.lote
                   JOIN contabilidade.valor_lancamento
                     ON valor_lancamento.cod_lote     = lote.cod_lote
                    AND valor_lancamento.tipo         = lote.tipo
                    AND valor_lancamento.exercicio    = lote.exercicio
                    AND valor_lancamento.cod_entidade = lote.cod_entidade
                   JOIN contabilidade.conta_credito
                     ON conta_credito.cod_lote     = valor_lancamento.cod_lote
                    AND conta_credito.tipo         = valor_lancamento.tipo
                    AND conta_credito.sequencia    = valor_lancamento.sequencia
                    AND conta_credito.exercicio    = valor_lancamento.exercicio
                    AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                    AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                   JOIN contabilidade.plano_analitica
                     ON plano_analitica.cod_plano = conta_credito.cod_plano
                    AND plano_analitica.exercicio = conta_credito.exercicio
                   JOIN contabilidade.plano_conta
                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                    AND plano_conta.exercicio = plano_analitica.exercicio
                   JOIN contabilidade.plano_recurso
                     ON plano_recurso.cod_plano = plano_analitica.cod_plano
                    AND plano_recurso.exercicio = plano_analitica.exercicio
             INNER JOIN ( SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                               , OE.cod_entidade
                               , OE.exercicio
                            FROM orcamento.entidade as OE
                               , sw_cgm as CGM
                           WHERE OE.numcgm = CGM.numcgm
                        ) as OE 
                     ON OE.cod_entidade = valor_lancamento.cod_entidade   
                    AND OE.exercicio    = valor_lancamento.exercicio
                  WHERE conta_credito.exercicio    = ''' || stExercicio || '''
                    AND conta_credito.cod_entidade IN (' || stCodEntidades || ')
                    AND conta_credito.tipo_valor   = ''C''
                    AND lote.dt_lote               = to_date(''01/01/' || stExercicio || '''::varchar,''dd/mm/yyyy'')
                    AND lote.tipo                  = ''I'' ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;
            stSql := stSql || '
               GROUP BY OE.entidade
                      , plano_recurso.cod_recurso
               ) as tabela
        ORDER BY cod_recurso
        )';

              EXECUTE stSql;

        stSql := 'CREATE TEMPORARY TABLE tmp_valor_arrec AS (
          SELECT receita.cod_recurso   as cod_recurso
               , receita.cod_entidade  as cod_entidade
               , receita.exercicio     as exercicio
               , lote.dt_lote          as data
               , valor_lancamento.vl_lancamento   as valor_arrecadado
            FROM orcamento.receita
            JOIN orcamento.conta_receita
              ON receita.cod_conta = conta_receita.cod_conta
             AND receita.exercicio = conta_receita.exercicio
            JOIN contabilidade.lancamento_receita
              ON lancamento_receita.cod_receita = receita.cod_receita
             AND lancamento_receita.exercicio   = receita.exercicio
             AND lancamento_receita.estorno     = false
             -- tipo de lancamento receita deve ser = A , de arrecadação
             AND lancamento_receita.tipo        = ''A''
            JOIN contabilidade.lancamento
              ON lancamento.cod_lote        = lancamento_receita.cod_lote
             AND lancamento.sequencia       = lancamento_receita.sequencia
             AND lancamento.exercicio       = lancamento_receita.exercicio
             AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
             AND lancamento.tipo            = lancamento_receita.tipo
            JOIN contabilidade.valor_lancamento
              ON valor_lancamento.exercicio     = lancamento.exercicio
             AND valor_lancamento.sequencia     = lancamento.sequencia
             AND valor_lancamento.cod_entidade  = lancamento.cod_entidade
             AND valor_lancamento.cod_lote      = lancamento.cod_lote
             AND valor_lancamento.tipo          = lancamento.tipo
             -- na tabela valor lancamento  tipo_valor deve ser credito
             AND valor_lancamento.tipo_valor    = ''D''
            JOIN contabilidade.lote
              ON lote.cod_lote     = lancamento.cod_lote
             AND lote.cod_entidade = lancamento.cod_entidade
             AND lote.exercicio    = lancamento.exercicio
             AND lote.tipo         = lancamento.tipo
            WHERE receita.cod_entidade    IN(' || stCodEntidades || ')
              AND receita.exercicio       = ''' || stExercicio || ''' 
              AND to_date(lote.dt_lote::varchar, ''yyyy-mm-dd'') <= to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'') ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND receita.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND receita.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND receita.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;
            stSql := stSql || '

            )';

        EXECUTE stSql;


        stSql := 'CREATE TEMPORARY TABLE tmp_valor_arrec_estorno AS (
          SELECT receita.cod_recurso    as cod_recurso
               , receita.cod_entidade   as cod_entidade
               , receita.exercicio      as exercicio
               , lote.dt_lote       as data
               , valor_lancamento.vl_lancamento   as valor_estornado
            FROM orcamento.receita
            JOIN orcamento.conta_receita
              ON receita.cod_conta = conta_receita.cod_conta
             AND receita.exercicio = conta_receita.exercicio
            JOIN contabilidade.lancamento_receita
              ON lancamento_receita.cod_receita = receita.cod_receita
             AND lancamento_receita.exercicio   = receita.exercicio
             AND lancamento_receita.estorno     = true
             -- tipo de lancamento receita deve ser = A , de arrecadação
             AND lancamento_receita.tipo        = ''A''
            JOIN contabilidade.lancamento
              ON lancamento.cod_lote        = lancamento_receita.cod_lote
             AND lancamento.sequencia       = lancamento_receita.sequencia
             AND lancamento.exercicio       = lancamento_receita.exercicio
             AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
             AND lancamento.tipo            = lancamento_receita.tipo
            JOIN contabilidade.valor_lancamento
              ON valor_lancamento.exercicio     = lancamento.exercicio
             AND valor_lancamento.sequencia     = lancamento.sequencia
             AND valor_lancamento.cod_entidade  = lancamento.cod_entidade
             AND valor_lancamento.cod_lote      = lancamento.cod_lote
             AND valor_lancamento.tipo          = lancamento.tipo
             -- na tabela valor lancamento  tipo_valor deve ser credito
             AND valor_lancamento.tipo_valor    = ''D''
            JOIN contabilidade.lote
              ON lote.cod_lote     = lancamento.cod_lote
             AND lote.cod_entidade = lancamento.cod_entidade
             AND lote.exercicio    = lancamento.exercicio
             AND lote.tipo         = lancamento.tipo
            WHERE receita.cod_entidade    IN(' || stCodEntidades || ')
              AND receita.exercicio       = ''' || stExercicio || ''' 
              AND to_date(lote.dt_lote::varchar, ''yyyy-mm-dd'') <= to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'') ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND receita.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND receita.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND receita.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;
            stSql := stSql || '

        )';

            EXECUTE stSql;


    stSql := 'CREATE TEMPORARY TABLE tmp_arrecadacao_orcamentaria AS (
    SELECT OE.entidade
         , cod_recurso
         , ABS(SUM(valor_arrecadado)) AS vl_arrec_orcamentaria
         , SUM(valor_estornado) AS vl_est_arrec_orcamentaria
      FROM ( SELECT cod_entidade
                  , cod_recurso
                  , exercicio
                  , SUM(valor_arrecadado) AS valor_arrecadado
                  , 0.00 AS valor_estornado 
               FROM tmp_valor_arrec
           GROUP BY cod_entidade
                  , cod_recurso
                  , exercicio
    
          UNION ALL
    
             SELECT cod_entidade
                  , cod_recurso
                  , exercicio
                  , 0.00 AS valor_arrecadado
                  , SUM(valor_estornado) AS valor_estornado 
               FROM tmp_valor_arrec_estorno
           GROUP BY cod_entidade
                  , cod_recurso
                  , exercicio
           ) as tbl 
      INNER JOIN ( SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                        , OE.cod_entidade
                        , OE.exercicio
                     FROM orcamento.entidade as OE
                        , sw_cgm as CGM
                    WHERE OE.numcgm = CGM.numcgm
                 ) as OE
              ON OE.cod_entidade = tbl.cod_entidade
             AND OE.exercicio    = tbl.exercicio
    GROUP BY OE.entidade
           , cod_recurso
    )';

              EXECUTE stSql;


    stSql := 'CREATE TEMPORARY TABLE tmp_arrecadacao_extra_orcamentaria AS (
        SELECT OE.entidade
             , plano_recurso.cod_recurso
             , sum(coalesce(transferencia.valor, 0.00)) as vl_arrec_extra_orcamentaria
             , sum(coalesce(transferencia_estornada.valor, 0.00)) as vl_est_arrec_extra_orcamentaria
          FROM tesouraria.transferencia
    INNER JOIN contabilidade.plano_recurso 
            ON plano_recurso.cod_plano = transferencia.cod_plano_credito
           AND plano_recurso.exercicio = transferencia.exercicio
     LEFT JOIN tesouraria.transferencia_estornada
            ON transferencia_estornada.cod_lote     = transferencia.cod_lote
           AND transferencia_estornada.tipo         = transferencia.tipo
           AND transferencia_estornada.cod_entidade = transferencia.cod_entidade
           AND transferencia_estornada.exercicio    = transferencia.exercicio
    INNER JOIN ( SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                      , OE.cod_entidade
                      , OE.exercicio
                   FROM orcamento.entidade as OE
                      , sw_cgm as CGM
                  WHERE OE.numcgm = CGM.numcgm
               ) as OE 
            ON OE.cod_entidade = transferencia.cod_entidade
           AND OE.exercicio    = transferencia.exercicio
         WHERE transferencia.cod_tipo = 2
           AND transferencia.exercicio = ''' || stExercicio || '''
           AND to_date(TO_CHAR(transferencia.timestamp_transferencia, ''dd/mm/yyyy''), ''dd/mm/yyyy'')  <= to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'')
           AND transferencia.cod_entidade IN ( ' || stCodEntidades || ')';

        IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
            stSql := stSql || ' AND plano_recurso.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
        ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
            stSql := stSql || ' AND plano_recurso.cod_recurso >= ' || inCodRecursoIni || ' ';
        ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
            stSql := stSql || ' AND plano_recurso.cod_recurso <= ' || inCodRecursoFim || ' ';
        END IF;

        stSql := stSql || '

        GROUP BY  OE.entidade
                , plano_recurso.cod_recurso
        ORDER BY  plano_recurso.cod_recurso
        )';

              EXECUTE stSql;


    stSql := 'CREATE TEMPORARY TABLE tmp_pagamento_orcamentario AS (
    SELECT entidade
         , cod_recurso
         , SUM(vl_pag_orcamentario) AS vl_pag_orcamentario
         , SUM(vl_est_pag_orcamentario) AS vl_est_pag_orcamentario
      FROM (( SELECT OE.entidade
                   , recurso.cod_recurso
                   , sum(coalesce(nota_liquidacao_paga.vl_pago, 0.00)) as vl_pag_orcamentario
                   , 0.00 as vl_est_pag_orcamentario
                FROM tesouraria.pagamento
          --LIGAÇÃO COM NOTA_LIQUIDACAO_PAGA
          INNER JOIN empenho.nota_liquidacao_paga 
                  ON nota_liquidacao_paga.exercicio       = pagamento.exercicio      
                 AND nota_liquidacao_paga.cod_nota        = pagamento.cod_nota       
                 AND nota_liquidacao_paga.cod_entidade    = pagamento.cod_entidade   
                 AND nota_liquidacao_paga.timestamp       = pagamento.timestamp
          --LIGAÇÃO COM NOTA_LIQUIDACAO
          INNER JOIN empenho.nota_liquidacao 
                  ON nota_liquidacao.exercicio       = nota_liquidacao_paga.exercicio 
                 AND nota_liquidacao.cod_nota        = nota_liquidacao_paga.cod_nota  
                 AND nota_liquidacao.cod_entidade    = nota_liquidacao_paga.cod_entidade
          --LIGAÇÃO COM EMPENHO
          INNER JOIN empenho.empenho 
                  ON empenho.exercicio       = nota_liquidacao.exercicio_empenho
                 AND empenho.cod_empenho     = nota_liquidacao.cod_empenho      
                 AND empenho.cod_entidade    = nota_liquidacao.cod_entidade
          --LIGAÇÃO COM PRE_EMPENHO_DESPESA
          INNER JOIN empenho.pre_empenho_despesa
                  ON pre_empenho_despesa.exercicio       = empenho.exercicio         
                 AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
          --LIGAÇÃO COM DESPESA
          INNER JOIN orcamento.despesa
                  ON despesa.exercicio       = pre_empenho_despesa.exercicio     
                 AND despesa.cod_despesa     = pre_empenho_despesa.cod_despesa
          --LIGAÇÃO COM RECURSO
          INNER JOIN ( SELECT nom_recurso as recurso
                            , exercicio
                            , cod_recurso
                            , masc_recurso_red
                            , cod_detalhamento
                         FROM orcamento.recurso(''' || stExercicio || ''') 
                     ) as recurso 
                  ON recurso.exercicio    = despesa.exercicio
                 AND recurso.cod_recurso  = despesa.cod_recurso
          --LIGAÇÃO COM CONTA_DESPESA
           LEFT JOIN orcamento.conta_despesa as OCD
                  ON OCD.exercicio       = pre_empenho_despesa.exercicio
                 AND OCD.cod_conta       = pre_empenho_despesa.cod_conta
          --BUSCA ENTIDADE
          INNER JOIN ( SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                            , OE.cod_entidade
                            , OE.exercicio
                         FROM orcamento.entidade as OE
                            , sw_cgm as CGM
                        WHERE OE.numcgm = CGM.numcgm
                     ) as OE 
                  ON OE.cod_entidade = pagamento.cod_entidade
                 AND OE.exercicio    = pagamento.exercicio
               WHERE pagamento.exercicio = ''' || stExercicio || '''
                 AND to_date(TO_CHAR(pagamento.timestamp, ''dd/mm/yyyy''), ''dd/mm/yyyy'') <= to_date(''' || stDataFinal || '''::varchar, ''dd/mm/yyyy'')
                 AND pagamento.cod_entidade IN (' || stCodEntidades || ') ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND recurso.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND recurso.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND recurso.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;
            stSql := stSql || '
            GROUP BY OE.entidade
                   , recurso.cod_recurso
            ORDER BY recurso.cod_recurso 
        )

        UNION ALL

        ( SELECT OE.entidade
               , recurso.cod_recurso
               , 0.00 as vl_pag_orcamentario
               , nota_liquidacao_paga_anulada.vl_anulado as vl_est_pag_orcamentario
            FROM tesouraria.pagamento
      INNER JOIN tesouraria.pagamento_estornado
              ON pagamento_estornado.cod_entidade    = pagamento.cod_entidade
             AND pagamento_estornado.exercicio       = pagamento.exercicio
             AND pagamento_estornado.timestamp       = pagamento.timestamp
             AND pagamento_estornado.cod_nota        = pagamento.cod_nota
     --LIGAÇÃO COM NOTA_LIQUIDACAO_PAGA_ANULADA
      INNER JOIN empenho.nota_liquidacao_paga_anulada
              ON nota_liquidacao_paga_anulada.exercicio         = pagamento_estornado.exercicio
             AND nota_liquidacao_paga_anulada.cod_nota          = pagamento_estornado.cod_nota
             AND nota_liquidacao_paga_anulada.cod_entidade      = pagamento_estornado.cod_entidade
             AND nota_liquidacao_paga_anulada.timestamp         = pagamento_estornado.timestamp
             AND nota_liquidacao_paga_anulada.timestamp_anulada = pagamento_estornado.timestamp_anulado
     --LIGAÇÃO COM NOTA_LIQUIDACAO
      INNER JOIN empenho.nota_liquidacao
              ON nota_liquidacao.exercicio       = nota_liquidacao_paga_anulada.exercicio
             AND nota_liquidacao.cod_nota        = nota_liquidacao_paga_anulada.cod_nota
             AND nota_liquidacao.cod_entidade    = nota_liquidacao_paga_anulada.cod_entidade
     --LIGAÇÃO COM EMPENHO
      INNER JOIN empenho.empenho
              ON empenho.exercicio       = nota_liquidacao.exercicio_empenho
             AND empenho.cod_empenho     = nota_liquidacao.cod_empenho
             AND empenho.cod_entidade    = nota_liquidacao.cod_entidade
     --LIGAÇÃO COM PRE_EMPENHO_DESPESA
      INNER JOIN empenho.pre_empenho_despesa
              ON pre_empenho_despesa.exercicio       = empenho.exercicio
             AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
     --LIGAÇÃO COM DESPESA
      INNER JOIN orcamento.despesa
              ON despesa.exercicio       = pre_empenho_despesa.exercicio
             AND despesa.cod_despesa     = pre_empenho_despesa.cod_despesa
     --LIGAÇÃO COM RECURSO
      INNER JOIN ( SELECT nom_recurso as recurso
                        , exercicio
                        , cod_recurso
                        , masc_recurso_red
                        , cod_detalhamento
                     FROM orcamento.recurso(''' || stExercicio ||''')
                 ) as recurso
              ON recurso.exercicio    = despesa.exercicio
             AND recurso.cod_recurso  = despesa.cod_recurso
     --LIGAÇÃO COM CONTA_DESPESA
       LEFT JOIN orcamento.conta_despesa
              ON conta_despesa.exercicio       = pre_empenho_despesa.exercicio
             AND conta_despesa.cod_conta       = pre_empenho_despesa.cod_conta
     --BUSCA ENTIDADE
      INNER JOIN ( SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                        , OE.cod_entidade
                        , OE.exercicio
                     FROM orcamento.entidade as OE
                        , sw_cgm as CGM
                    WHERE OE.numcgm = CGM.numcgm
                 ) as OE
              ON OE.cod_entidade = pagamento.cod_entidade
             AND OE.exercicio    = pagamento.exercicio
           WHERE TO_DATE(TO_CHAR(pagamento_estornado.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date( ''' || stDataFinal || '''::varchar,''dd/mm/yyyy'') ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND recurso.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND recurso.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND recurso.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;
            stSql := stSql || '
        ) ) as tabela
    GROUP BY entidade, cod_recurso
    )';

              EXECUTE stSql;


    stSql := 'CREATE TEMPORARY TABLE tmp_pagamento_extra_orcamentario AS (
    SELECT entidade
         , cod_recurso
         , SUM(vl_pag_ext_orc) as vl_pag_extra_orcamentaria
         , SUM(vl_est_pag_ext_orc) as vl_est_pag_extra_orcamentaria
      FROM ( ( SELECT OE.entidade
                    , plano_recurso.cod_recurso
                    , SUM(coalesce(transferencia.valor,0.00)) as vl_pag_ext_orc
                    , 0.00  as vl_est_pag_ext_orc
                 FROM tesouraria.transferencia
         --BUSCA RECURSO
           INNER JOIN contabilidade.plano_recurso
                   ON plano_recurso.cod_plano = transferencia.cod_plano_debito
                  AND plano_recurso.exercicio = transferencia.exercicio
         --BUSCA ENTIDADE
           INNER JOIN ( SELECT entidade.cod_entidade || '' - '' || sw_cgm.nom_cgm as entidade
                             , entidade.cod_entidade
                             , entidade.exercicio
                          FROM orcamento.entidade
                             , sw_cgm
                         WHERE entidade.numcgm = sw_cgm.numcgm
                      ) as OE
                   ON OE.cod_entidade = transferencia.cod_entidade
                  AND OE.exercicio    = transferencia.exercicio
                WHERE transferencia.cod_tipo = 1
                  AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date(''' || stDataFinal ||'''::varchar,''dd/mm/yyyy'') ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;

        stSql := stSql || '
             GROUP BY OE.entidade
                    , plano_recurso.cod_recurso )
    
        UNION ALL
    
           ( SELECT OE.entidade
                  , plano_recurso.cod_recurso
                  , 0.00 as vl_pag_extra_orcamentaria
                  , SUM(coalesce(transferencia_estornada.valor,0.00)) as vl_est_pag_extra_orcamentaria
               FROM tesouraria.transferencia
         INNER JOIN tesouraria.transferencia_estornada
                 ON transferencia_estornada.cod_entidade    = transferencia.cod_entidade
                AND transferencia_estornada.tipo            = transferencia.tipo
                AND transferencia_estornada.exercicio       = transferencia.exercicio
                AND transferencia_estornada.cod_lote        = transferencia.cod_lote
         --BUSCA RECURSO
         INNER JOIN contabilidade.plano_recurso
                 ON plano_recurso.cod_plano = transferencia.cod_plano_debito
                AND plano_recurso.exercicio = transferencia.exercicio
         --BUSCA ENTIDADE
         INNER JOIN ( SELECT entidade.cod_entidade || '' - '' || sw_cgm.nom_cgm as entidade
                           , entidade.cod_entidade
                           , entidade.exercicio
                        FROM orcamento.entidade
                           , sw_cgm
                       WHERE entidade.numcgm = sw_cgm.numcgm
                    ) as OE
                 ON OE.cod_entidade = transferencia.cod_entidade
                AND OE.exercicio    = transferencia.exercicio
              WHERE transferencia.cod_tipo = 1
                AND TO_DATE(TO_CHAR(transferencia_estornada.timestamp_estornada,''dd/mm/yyyy''),''dd/mm/yyyy'') = to_date(''' || stDataFinal || '''::varchar,''dd/mm/yyyy'') ';
            IF (inCodRecursoIni > 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso BETWEEN ' || inCodRecursoIni || ' AND ' || inCodRecursoFim || ' ';
            ELSEIF (inCodRecursoIni > 0 AND inCodRecursoFim = 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso >= ' || inCodRecursoIni || ' ';
            ELSEIF (inCodRecursoIni = 0 AND inCodRecursoFim > 0) THEN
                stSql := stSql || ' AND plano_recurso.cod_recurso <= ' || inCodRecursoFim || ' ';
            END IF;

        stSql := stSql || '
           GROUP BY plano_recurso.cod_recurso
                  , OE.entidade
          )
    ) as tabela
    GROUP BY entidade
           , cod_recurso
    )';

              EXECUTE stSql;

    
    stSql := 'CREATE TEMPORARY TABLE tmp_retorno AS (
        SELECT CAST(entidade as VARCHAR) as entidade
             , cod_recurso
             , nom_recurso
             , saldo_inicial
             , vl_arrec_orcamentaria
             , vl_est_arrec_orcamentaria
             , vl_arrec_extra_orcamentaria
             , vl_est_arrec_extra_orcamentaria
             , vl_pag_orcamentario
             , vl_est_pag_orcamentario
             , vl_pag_extra_orcamentaria
             , vl_est_pag_extra_orcamentaria
             , (saldo_inicial + vl_arrec_orcamentaria - vl_est_arrec_orcamentaria + vl_arrec_extra_orcamentaria - vl_est_arrec_extra_orcamentaria - vl_pag_orcamentario + vl_est_pag_orcamentario - vl_pag_extra_orcamentaria + vl_est_pag_extra_orcamentaria) as saldo_atual            
          FROM ( SELECT entidade
                      , cod_recurso
                      , nom_recurso
                      , sum(coalesce(saldo_inicial, 0.00)) as saldo_inicial
                      , sum(coalesce(vl_arrec_orcamentaria, 0.00)) as vl_arrec_orcamentaria
                      , sum(coalesce(vl_est_arrec_orcamentaria, 0.00)) as vl_est_arrec_orcamentaria
                      , sum(coalesce(vl_arrec_extra_orcamentaria, 0.00)) as vl_arrec_extra_orcamentaria
                      , sum(coalesce(vl_est_arrec_extra_orcamentaria, 0.00)) as vl_est_arrec_extra_orcamentaria
                      , sum(coalesce(vl_pag_orcamentario, 0.00)) as vl_pag_orcamentario
                      , sum(coalesce(vl_est_pag_orcamentario, 0.00)) as vl_est_pag_orcamentario
                      , sum(coalesce(vl_pag_extra_orcamentaria, 0.00)) as vl_pag_extra_orcamentaria
                      , sum(coalesce(vl_est_pag_extra_orcamentaria, 0.00)) as vl_est_pag_extra_orcamentaria
                      , 0.00 as saldo_atual
                   FROM ( ( SELECT tmp_saldo_inicial.entidade
                                 , tmp_saldo_inicial.cod_recurso
                                 , recurso.nom_recurso
                                 , tmp_saldo_inicial.saldo_inicial
                                 , 0.00 as vl_arrec_orcamentaria
                                 , 0.00 as vl_est_arrec_orcamentaria
                                 , 0.00 as vl_arrec_extra_orcamentaria
                                 , 0.00 as vl_est_arrec_extra_orcamentaria
                                 , 0.00 as vl_pag_orcamentario
                                 , 0.00 as vl_est_pag_orcamentario
                                 , 0.00 as vl_pag_extra_orcamentaria
                                 , 0.00 as vl_est_pag_extra_orcamentaria
                              FROM tmp_saldo_inicial
                              JOIN orcamento.recurso
                                ON recurso.cod_recurso = tmp_saldo_inicial.cod_recurso
                               AND recurso.exercicio   = ''' || stExercicio || ''' 
                          )

                         UNION ALL

                          ( SELECT tmp_arrecadacao_orcamentaria.entidade
                                 , tmp_arrecadacao_orcamentaria.cod_recurso
                                 , recurso.nom_recurso
                                 , 0.00 as saldo_inicial
                                 , tmp_arrecadacao_orcamentaria.vl_arrec_orcamentaria
                                 , tmp_arrecadacao_orcamentaria.vl_est_arrec_orcamentaria
                                 , 0.00 as vl_arrec_extra_orcamentaria 
                                 , 0.00 as vl_est_arrec_extra_orcamentaria 
                                 , 0.00 as vl_pag_orcamentario
                                 , 0.00 as vl_est_pag_orcamentario
                                 , 0.00 as vl_pag_extra_orcamentaria
                                 , 0.00 as vl_est_pag_extra_orcamentaria
                              FROM tmp_arrecadacao_orcamentaria
                              JOIN orcamento.recurso
                                ON recurso.cod_recurso = tmp_arrecadacao_orcamentaria.cod_recurso
                               AND recurso.exercicio   = ''' || stExercicio || ''' 
                          )

                         UNION ALL

                          ( SELECT tmp_arrecadacao_extra_orcamentaria.entidade
                                 , tmp_arrecadacao_extra_orcamentaria.cod_recurso
                                 , recurso.nom_recurso
                                 , 0.00 as saldo_inicial
                                 , 0.00 as vl_arrec_orcamentaria
                                 , 0.00 as vl_est_arrec_orcamentaria
                                 , tmp_arrecadacao_extra_orcamentaria.vl_arrec_extra_orcamentaria
                                 , tmp_arrecadacao_extra_orcamentaria.vl_est_arrec_extra_orcamentaria
                                 , 0.00 as vl_pag_orcamentario
                                 , 0.00 as vl_est_pag_orcamentario
                                 , 0.00 as vl_pag_extra_orcamentaria
                                 , 0.00 as vl_est_pag_extra_orcamentaria
                              FROM tmp_arrecadacao_extra_orcamentaria
                              JOIN orcamento.recurso
                                ON recurso.cod_recurso = tmp_arrecadacao_extra_orcamentaria.cod_recurso
                               AND recurso.exercicio   = ''' || stExercicio || ''' 
                          )

                         UNION ALL

                          ( SELECT tmp_pagamento_orcamentario.entidade
                                 , tmp_pagamento_orcamentario.cod_recurso
                                 , recurso.nom_recurso
                                 , 0.00 as saldo_inicial
                                 , 0.00 as vl_arrec_orcamentaria
                                 , 0.00 as vl_est_arrec_orcamentaria
                                 , 0.00 as vl_arrec_extra_orcamentaria
                                 , 0.00 as vl_est_arrec_extra_orcamentaria
                                 , tmp_pagamento_orcamentario.vl_pag_orcamentario
                                 , tmp_pagamento_orcamentario.vl_est_pag_orcamentario
                                 , 0.00 as vl_pag_extra_orcamentaria
                                 , 0.00 as vl_est_pag_extra_orcamentaria
                              FROM tmp_pagamento_orcamentario
                              JOIN orcamento.recurso
                                ON recurso.cod_recurso = tmp_pagamento_orcamentario.cod_recurso
                               AND recurso.exercicio   = ''' || stExercicio || ''' 
                          )

                         UNION ALL

                          ( SELECT tmp_pagamento_extra_orcamentario.entidade
                                 , tmp_pagamento_extra_orcamentario.cod_recurso
                                 , recurso.nom_recurso
                                 , 0.00 as saldo_inicial
                                 , 0.00 as vl_arrec_orcamentaria
                                 , 0.00 as vl_est_arrec_orcamentaria
                                 , 0.00 as vl_arrec_extra_orcamentaria
                                 , 0.00 as vl_est_arrec_extra_orcamentaria
                                 , 0.00 as vl_pag_orcamentario
                                 , 0.00 as vl_est_pag_orcamentario
                                 , tmp_pagamento_extra_orcamentario.vl_pag_extra_orcamentaria
                                 , tmp_pagamento_extra_orcamentario.vl_est_pag_extra_orcamentaria
                              FROM tmp_pagamento_extra_orcamentario
                              JOIN orcamento.recurso
                                ON recurso.cod_recurso = tmp_pagamento_extra_orcamentario.cod_recurso
                               AND recurso.exercicio   = ''' || stExercicio || ''' 
                          )
                        ) as tabela
               GROUP BY entidade
                      , cod_recurso
                      , nom_recurso
               ) as geral
      ORDER BY cod_recurso
    )';

        EXECUTE stSql;

    -----------------------------------------------------------------------------
	--- FIM DAS TEMPORARIAS
    -----------------------------------------------------------------------------
   
    stSql := ' SELECT * FROM tmp_retorno ';
  
    FOR reReg IN EXECUTE stSql
    LOOP
        RETURN next reReg;
    END LOOP;

    DROP TABLE tmp_saldo_inicial;
    DROP TABLE tmp_valor_arrec;
    DROP TABLE tmp_valor_arrec_estorno;
    DROP TABLE tmp_arrecadacao_orcamentaria;
    DROP TABLE tmp_arrecadacao_extra_orcamentaria;
    DROP TABLE tmp_pagamento_orcamentario;
    DROP TABLE tmp_pagamento_extra_orcamentario;
    DROP TABLE tmp_retorno;

    RETURN;
 
END; 
$$ language plpgsql;

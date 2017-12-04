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
  $Id: recuperaMovimentoBanco.plsql 59612 2014-09-02 12:00:51Z gelson $

* $Revision: 29013 $
* $Name$
* $Author: tonismar $
* $Date: 2008-04-04 16:08:37 -0300 (Sex, 04 Abr 2008) $
*
* Casos de uso: uc-02.04.07
*/
CREATE OR REPLACE FUNCTION tesouraria.fn_recupera_movimento_banco(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE

    stFiltroTransferencia          ALIAS FOR $1;
    stFiltroTransferenciaEstornada ALIAS FOR $2;
    stFiltroPagamentoTmp           ALIAS FOR $3;
    stFiltroPagamentoEstornado     ALIAS FOR $4;
    stFiltroArrecadacao            ALIAS FOR $5;
    stFiltroPagamento              ALIAS FOR $6;
    stEntidade                     ALIAS FOR $7;
    stExercicio                    ALIAS FOR $8;
    stDtBoletim                    ALIAS FOR $9;
    boUtilizaEstruturalTCE         ALIAS FOR $10;

    stFiltroArrecadacaoAux VARCHAR := '';
    stSql               VARCHAR   := '';
    stSqlFuncao         VARCHAR   := '';
    reRegistro          RECORD;
    dtInicioAno       VARCHAR := '';

BEGIN

    dtInicioAno := '01/01/' || stExercicio;

    stSql := '
        CREATE TEMPORARY TABLE tmp_transferencia AS (
            SELECT TB.exercicio
                  ,TB.cod_boletim
                  ,TT.cod_entidade
                  ,TT.valor as vl_debito
                  ,0.00 as vl_credito
                  ,TT.cod_plano_debito as cod_plano
                  ,TT.cgm_usuario
                  ,TT.cod_tipo
            FROM tesouraria.boletim             AS TB
                ,tesouraria.transferencia       AS TT
              -- Join com tesouraria_transferencia
            WHERE TB.exercicio   = TT.exercicio
              AND TB.cod_boletim = TT.cod_boletim
              AND TB.cod_entidade= TT.cod_entidade
              -- Filtro
              ' || stFiltroTransferencia || '

        UNION ALL 

            SELECT TB.exercicio
                  ,TB.cod_boletim
                  ,TT.cod_entidade
                  ,0.00 as vl_debito
                  ,TT.valor as vl_credito
                  ,TT.cod_plano_credito as cod_plano
                  ,TT.cgm_usuario
                  ,TT.cod_tipo
            FROM tesouraria.boletim             AS TB
                ,tesouraria.transferencia       AS TT
              -- Join com tesouraria_transferencia
            WHERE TB.exercicio   = TT.exercicio
              AND TB.cod_boletim = TT.cod_boletim
              AND TB.cod_entidade= TT.cod_entidade
              -- Filtro
              ' || stFiltroTransferencia || '

        );
    ';

    
    EXECUTE stSql;
    CREATE INDEX btree_tmp_transferencias ON tmp_transferencia ( exercicio, cod_plano );     


    stSql := '
        CREATE TEMPORARY TABLE tmp_transferencia_estornada AS (
            SELECT TB.exercicio
                  ,TB.cod_boletim
                  ,TTE.cod_entidade
                  ,0.00 as vl_credito
                  ,TTE.valor as vl_debito
                  ,TT.cod_plano_credito as cod_plano
                  ,TTE.cgm_usuario
                  ,TT.cod_tipo
            FROM tesouraria.boletim                 AS TB
                ,tesouraria.transferencia           AS TT
                ,tesouraria.transferencia_estornada AS TTE
              -- Join com tesouraria_transferencia
            WHERE TB.exercicio   = TTE.exercicio
              AND TB.cod_boletim = TTE.cod_boletim
              AND TB.cod_entidade= TTE.cod_entidade
              -- Join com transferencia_estornada
              AND TT.exercicio    = TTE.exercicio
              AND TT.cod_entidade = TTE.cod_entidade
              AND TT.tipo         = TTE.tipo
              AND TT.cod_lote     = TTE.cod_lote  
              ' || stFiltroTransferenciaEstornada || '

        UNION ALL

            SELECT TB.exercicio
                  ,TB.cod_boletim
                  ,TTE.cod_entidade
                  ,TTE.valor as vl_credito
                  ,0.00 as vl_debito
                  ,TT.cod_plano_debito as cod_plano
                  ,TTE.cgm_usuario
                  ,TT.cod_tipo
            FROM tesouraria.boletim                 AS TB
                ,tesouraria.transferencia           AS TT
                ,tesouraria.transferencia_estornada AS TTE
              -- Join com tesouraria_transferencia
            WHERE TB.exercicio   = TTE.exercicio
              AND TB.cod_boletim = TTE.cod_boletim
              AND TB.cod_entidade= TTE.cod_entidade
              -- Join com transferencia_estornada
              AND TT.exercicio    = TTE.exercicio
              AND TT.cod_entidade = TTE.cod_entidade
              AND TT.tipo         = TTE.tipo
              AND TT.cod_lote     = TTE.cod_lote
              ' || stFiltroTransferenciaEstornada || '
        );
    ';
        
    EXECUTE stSql;
    CREATE INDEX btree_tmp_transferencia_estornada ON tmp_transferencia_estornada ( exercicio, cod_plano );     


    stSql := '
            -- Pagamentos                                                                                                    
            CREATE TEMPORARY TABLE tmp_pgto AS (                                                                       
                     SELECT  tp.cod_boletim
                            ,tp.cod_entidade                                                                                 
                            ,tp.exercicio_boletim as exercicio
                            ,tp.timestamp                                                                                    
                            ,tp.cgm_usuario
                            ,tp.cod_terminal
                            ,tp.cod_plano as cod_plano                                                               
                            ,nlp.vl_pago AS vl_credito                                                                          
                                                                                                                             
                      FROM  tesouraria.pagamento AS TP                                                                       
                            JOIN empenho.nota_liquidacao_paga as nlp                                                         
                            ON (    nlp.cod_nota     = tp.cod_nota                                                           
                                AND nlp.cod_entidade = tp.cod_entidade                                                       
                                AND nlp.exercicio    = tp.exercicio                                                          
                                AND nlp.timestamp    = tp.timestamp                                                          
                            )                                                                                                
                            JOIN empenho.nota_liquidacao as nl                                                               
                            ON (    nl.cod_nota     = nlp.cod_nota                                                           
                                AND nl.exercicio    = nlp.exercicio                                                          
                                AND nl.cod_entidade = nlp.cod_entidade                                                       
                            )                                                                                                
                     WHERE tp.cod_boletim is not null
                ' || stFiltroPagamentoTmp || '

            ) ';

        EXECUTE stSql;                                                                                                              
        CREATE INDEX btree_tmp_pagamentos ON tmp_pgto    ( exercicio, cod_plano, cod_entidade, cod_boletim, timestamp  );     
 
        stSql := '                                                                                                                            
            -- Estornos                                                                                                      
            CREATE TEMPORARY TABLE tmp_estornos as (                                                                         
                             SELECT  tpe.cod_boletim
                                    ,tpe.cod_entidade
                                    ,tpe.exercicio_boletim as exercicio
                                    ,tpe.timestamp_anulado as timestamp
                                    ,tpe.cgm_usuario
                                    ,tpe.cod_terminal
                                    ,tp.cod_plano                                                 
                                    ,coalesce(nlpa.vl_anulado,0.00) as vl_debito                                          
                              FROM  tesouraria.pagamento_estornado as tpe                                                    
                                    JOIN empenho.nota_liquidacao_paga_anulada as nlpa                                        
                                    ON (    tpe.timestamp_anulado = nlpa.timestamp_anulada                                   
                                        AND tpe.cod_nota          = nlpa.cod_nota                                            
                                        AND tpe.cod_entidade      = nlpa.cod_entidade                                        
                                        AND tpe.exercicio         = nlpa.exercicio                                           
                                        AND tpe.timestamp         = nlpa.timestamp                                           
                                    )                                                                                        
                                    JOIN (                                                                                   
                                        SELECT    tp.exercicio                                                               
                                                 ,tp.cod_entidade                                                            
                                                 ,tp.timestamp                                                               
                                                 ,tp.cod_nota                                                                
                                                 ,tp.cod_plano                                        
                                                                                                                             
                                           FROM  tesouraria.pagamento AS TP                                                  
                                                 JOIN empenho.nota_liquidacao_paga as nlp                                    
                                                 ON (    nlp.cod_nota     = tp.cod_nota                                      
                                                     AND nlp.cod_entidade = tp.cod_entidade                                  
                                                     AND nlp.exercicio    = tp.exercicio                                     
                                                     AND nlp.timestamp    = tp.timestamp                                     
                                                 )                                                                           
                                                 JOIN empenho.nota_liquidacao as nl                                          
                                                 ON (    nl.cod_nota     = nlp.cod_nota                                      
                                                     AND nl.exercicio    = nlp.exercicio                                     
                                                     AND nl.cod_entidade = nlp.cod_entidade                                  
                                                 )                                                                           
                                         where  tp.cod_boletim is not null                                                          
                                            and tp.cod_entidade in (' || stEntidade || ')                        
                                            and tp.exercicio_boletim = '''|| stExercicio || '''                   
                                                                                                                             
                                    ) as TP on (    tp.cod_nota     = tpe.cod_nota                                           
                                                AND tp.exercicio    = tpe.exercicio                                          
                                                AND tp.cod_entidade = tpe.cod_entidade                                       
                                                AND tp.timestamp    = tpe.timestamp                                          
                                    )                                                                                        
                                                                                                                             
                              WHERE tpe.cod_boletim is not null
                            ' || stFiltroPagamentoEstornado || '
            ) ';                                                                                                                  
            EXECUTE stSql;
            CREATE INDEX btree_tmp_estornos ON tmp_estornos ( exercicio, cod_plano, cod_entidade, cod_boletim, timestamp  );
            
            stSql := '
            CREATE TEMPORARY TABLE tmp_pagamento AS (
                        SELECT
                               tb.cod_boletim
                              ,TO_CHAR( TB.dt_boletim, ''dd/mm/yyyy'' ) as dt_boletim
                              ,TO_CHAR( TP.timestamp, ''HH24:mi:ss'' ) as hora
                              ,TP.exercicio
                              ,coalesce(sum(TP.vl_credito),0.00) as vl_credito
                              ,coalesce(sum(TP.vl_debito),0.00) as vl_debito
                              ,tp.cod_plano
                              ,tp.cgm_usuario
                              ,tp.cod_terminal
                              ,tp.cod_entidade
                        FROM tesouraria.boletim   AS TB
                             JOIN ( SELECT cod_boletim 
                                          ,cod_entidade
                                          ,exercicio
                                          ,timestamp   
                                          ,cgm_usuario
                                          ,cod_terminal
                                          ,cod_plano
                                          ,vl_credito
                                          ,0.00 as vl_debito
                                    FROM tmp_pgto
                                
                                    UNION ALL
            
                                    SELECT cod_boletim
                                          ,cod_entidade
                                          ,exercicio
                                          ,timestamp
                                          ,cgm_usuario
                                          ,cod_terminal
                                          ,cod_plano
                                          ,0.00 as vl_credito
                                          ,vl_debito
                                    FROM tmp_estornos                     
                             ) as TP ON (   tp.cod_boletim  = tb.cod_boletim
                                        AND tp.cod_entidade = tb.cod_entidade
                                        AND tp.exercicio    = tb.exercicio
                             )
                            WHERE tb.dt_boletim is not null
                        ' || stFiltroPagamento || '
            
                            GROUP BY
                               TP.exercicio
                              ,tp.cod_plano
                              ,tb.dt_boletim
                              ,tb.cod_boletim
                              ,tp.timestamp
                              ,tp.cgm_usuario
                              ,tp.cod_terminal
                              ,tp.cod_entidade
                            ORDER BY tp.timestamp
                  );
    ';
    EXECUTE stSql;
    CREATE INDEX btree_tmp_pagamento ON tmp_pagamento ( exercicio, cod_plano );     

    stFiltroArrecadacaoAux := replace( stFiltroArrecadacao, '''', '''''' );

    IF boUtilizaEstruturalTCE = '1' THEN -- obs: 1 é true!
        stSql := '
                SELECT tesouraria.fn_listar_arrecadacao_tce( ''' || stFiltroArrecadacaoAux || ''', ''' || stFiltroArrecadacaoAux || ''' )
        ';
    ELSE
        stSql := '
                SELECT tesouraria.fn_listar_arrecadacao( ''' || stFiltroArrecadacaoAux || ''', ''' || stFiltroArrecadacaoAux || ''' )
        ';    
    END IF;

    EXECUTE stSql;

    stSql := '
        SELECT cod_estrutural
             , cod_plano
             , nom_conta
               ,coalesce( tesouraria.fn_saldo_conta_tesouraria( exercicio                                    
                                                              , cod_plano                                     
                                                              , '''|| dtInicioAno ||'''
                                                              , CASE WHEN '''|| stDtBoletim ||''' = '''||dtInicioAno||''' THEN '''||stDtBoletim ||''' ELSE TO_CHAR(TO_DATE('''|| stDtBoletim ||''',''dd/mm/yyyy'')-1, ''dd/mm/yyyy'' ) END
                                                              , CASE WHEN '''|| stDtBoletim ||''' != '''|| dtInicioAno || ''' THEN true ELSE false END 
                ), 0.00 ) as saldo_anterior                                                                     
             , vl_credito
             , vl_debito
             , cod_recurso
             , nom_recurso
          FROM (
            SELECT 
                CPC.cod_estrutural                                                                                
               ,tbl.cod_plano                                                                                     
               ,CPC.nom_conta                                   
               , CPC.exercicio                                                  
               ,coalesce( sum(tbl.vl_credito), 0.00 ) AS vl_credito                                                   
               ,coalesce( sum(tbl.vl_debito ), 0.00 ) AS vl_debito                                                    
               ,cpr.cod_recurso as cod_recurso
               ,rec.nom_recurso as nom_recurso
            FROM(
                SELECT substr(random()::text,3,10) as id
                      ,TP.exercicio
                      ,TP.cod_plano
                      ,TP.vl_credito AS vl_credito
                      ,TP.vl_debito  AS vl_debito
                FROM tmp_pagamento AS TP

                UNION ALL

                SELECT substr(random()::text,3,10) as id
                      ,TT.exercicio
                      ,TT.cod_plano
                      ,TT.vl_credito as vl_credito
                      ,TT.vl_debito  as vl_debito
                FROM tmp_transferencia AS TT

                UNION ALL

                SELECT substr(random()::text,3,10) as id
                      ,TTE.exercicio
                      ,TTE.cod_plano
                      ,TTE.vl_credito  as vl_credito
                      ,TTE.vl_debito   as vl_debito

                FROM tmp_transferencia_estornada AS TTE

                UNION ALL

                ( SELECT id 
                        ,exercicio
                        ,cod_plano  
                        ,cast(vl_credito as numeric) as vl_credito
                        ,vl_debito
    
                  FROM ( 
                         
                        SELECT substr(random()::text,3,10) as id
                              ,TA.exercicio
                              ,TA.conta_credito as cod_plano
                              ,CAST( TA.valor - vl_desconto + vl_multa + vl_juros  AS NUMERIC ) AS vl_credito
                              ,0.00 as vl_debito
                        FROM tmp_arrecadacao AS TA
    
                        UNION ALL
                        
                        SELECT substr(random()::text,3,10) as id
                              ,TA.exercicio
                              ,TA.conta_debito as cod_plano
                              ,0.00 as vl_credito
                              ,CAST( TA.valor - vl_desconto + vl_multa + vl_juros  AS NUMERIC ) AS vl_debito
                        FROM tmp_arrecadacao AS TA
                       ) as TA
                )

                UNION ALL

                ( SELECT id 
                     ,exercicio
                     ,cod_plano
                     ,vl_credito
                     ,vl_debito

                   FROM (
                        SELECT substr(random()::text,3,10) as id
                              ,TAE.exercicio
                              ,TAE.conta_debito as cod_plano
                              ,CAST( TAE.valor - vl_desconto + vl_multa + vl_juros  AS NUMERIC ) AS vl_debito
                              ,0.00 as vl_credito
                        FROM tmp_arrecadacao_estornada AS TAE

                        UNION ALL

                        SELECT substr(random()::text,3,10) as id
                              ,TAE.exercicio
                              ,TAE.conta_credito as cod_plano
                              ,0.00 as vl_debito
                              ,CAST( TAE.valor - vl_desconto + vl_multa + vl_juros  AS NUMERIC ) AS vl_credito
                        FROM tmp_arrecadacao_estornada AS TAE
                        ) as TAE
                )
                
            ) AS tbl
         JOIN contabilidade.plano_analitica AS CPA
           ON CPA.exercicio = tbl.exercicio
          AND CPA.cod_plano = tbl.cod_plano
         JOIN contabilidade.plano_conta     AS CPC
           ON CPC.exercicio = CPA.exercicio
          AND CPC.cod_conta = CPA.cod_conta
         JOIN contabilidade.plano_recurso   AS CPR
           ON (CPA.cod_plano = CPR.cod_plano
          AND CPA.exercicio = CPR.exercicio)
         JOIN orcamento.recurso('''||stExercicio||''') AS REC
           ON (CPR.exercicio = REC.exercicio
          AND CPR.cod_recurso = REC.cod_recurso)
        WHERE CPC.cod_estrutural like ''1.1.1.%''

          GROUP BY tbl.cod_plano
                 , CPC.exercicio
                 , cpc.nom_conta
                 , cpc.cod_estrutural
                 , cpr.cod_recurso
                 , rec.nom_recurso
        ) AS tbl
 ORDER BY cod_estrutural
    ';
    
FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

    DROP TABLE tmp_pgto;
    DROP TABLE tmp_estornos;
    DROP TABLE tmp_pagamento;
    DROP TABLE tmp_transferencia;
    DROP TABLE tmp_transferencia_estornada;
    DROP TABLE tmp_arrecadacao;
    DROP TABLE tmp_arrecadacao_estornada;
    DROP TABLE tmp_estorno_arrecadacao;
    DROP TABLE tmp_deducao;
    DROP TABLE tmp_deducao_estornada;

RETURN;

END;

$$ language 'plpgsql';

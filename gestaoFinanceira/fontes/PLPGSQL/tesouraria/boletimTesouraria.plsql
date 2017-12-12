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
* $Revision: 26317 $
* $Name$
* $Author: cako $
* $Date: 2007-10-24 15:18:39 -0200 (Qua, 24 Out 2007) $
*
* Casos de uso: uc-02.04.07
*/

/*
$Log$
Revision 1.24  2007/10/16 20:19:11  cako
Ticket#10253#

Revision 1.22  2007/06/28 16:05:05  vitor
Bug#9407#

Revision 1.21  2007/02/16 12:54:18  cako
Bug #7769#

Revision 1.20  2007/02/14 12:44:30  cako
Bug #7549#

Revision 1.19  2007/02/12 15:55:19  cako
Bug #7549#

Revision 1.18  2007/02/12 12:21:37  cako
Bug #7549#

Revision 1.17  2006/11/14 22:51:28  gelson
Bug #7352#

Revision 1.16  2006/11/14 12:22:16  cako
Bug #7232#

Revision 1.15  2006/10/25 14:12:30  cako
Bug #7049#

Revision 1.14  2006/09/26 11:26:40  jose.eduardo
Bug #7049#

Revision 1.13  2006/07/21 19:01:37  jose.eduardo
Ajustes conforme modificações na PL listar arrecadacoes

Revision 1.12  2006/07/19 16:42:02  jose.eduardo
Bug #6596#

Revision 1.11  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_boletim_demonstrativo_caixa(varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE

    stFiltroTransferencia          ALIAS FOR $1;
    stFiltroTransferenciaEstornada ALIAS FOR $2;
    stFiltroPagamentoTmp           ALIAS FOR $3;
    stFiltroPagamentoEstornado     ALIAS FOR $4;
    stFiltroArrecadacao            ALIAS FOR $5;
    stFiltroPagamento              ALIAS FOR $6;
    stEntidade                     ALIAS FOR $7;
    stExercicio                    ALIAS FOR $8;
    boTCEMS                        ALIAS FOR $9;

    stFiltroArrecadacaoAux VARCHAR := '''';
    stSql               VARCHAR   := '''';
    stSqlFuncao         VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN

    stSql := ''
        CREATE TEMPORARY TABLE tmp_transferencia AS (
            SELECT TB.exercicio
                  ,TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TT.timestamp_transferencia, ''''HH24:mi:ss'''' ) as hora
                  ,TT.cod_entidade
                  ,TT.tipo
                  ,TT.cod_lote
                  ,TT.valor as vl_lancamento
                  ,TT.cod_plano_debito as conta_debito
                  ,TT.cod_plano_credito as conta_credito
                  ,TT.cgm_usuario
                  ,TT.cod_tipo
            FROM tesouraria.boletim             AS TB
                ,tesouraria.transferencia       AS TT
              -- Join com tesouraria_transferencia
            WHERE TB.exercicio   = TT.exercicio
              AND TB.cod_boletim = TT.cod_boletim
              AND TB.cod_entidade= TT.cod_entidade
              -- Filtro
              '' || stFiltroTransferencia || ''
        );
    '';
    EXECUTE stSql;

    
    stSql := ''
        CREATE TEMPORARY TABLE tmp_transferencia_estornada AS (
            SELECT TB.exercicio
                  ,TB.cod_boletim
                  ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) AS dt_boletim
                  ,TO_CHAR( TTE.timestamp_estornada, ''''HH24:mi:ss'''' ) as hora
                  ,TTE.cod_entidade
                  ,TTE.tipo
                  ,TTE.cod_lote_estorno as cod_lote
                  ,TTE.valor as vl_lancamento
                  ,TT.cod_plano_credito as conta_debito
                  ,TT.cod_plano_debito  as conta_credito
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
              '' || stFiltroTransferenciaEstornada || ''
        );
    '';
    EXECUTE stSql;


    stSql := ''
            -- Pagamentos                                                                                                    
            CREATE TEMPORARY TABLE tmp_pgto AS (                                                                       
                     SELECT  tp.cod_boletim
                            ,tp.cod_entidade                                                                                 
                            ,tp.exercicio_boletim as exercicio
                            ,tp.timestamp                                                                                    
                            ,nl.exercicio_empenho                                                                            
                            ,nl.cod_empenho
                            ,false as estorno
                            ,cp.tipo
                            ,tp.cgm_usuario
                            ,tp.cod_terminal
                            ,contabilidade.fn_recupera_conta_lancamento( CP.exercicio                                        
                                                          ,CP.cod_entidade                                                   
                                                          ,CP.cod_lote                                                       
                                                          ,CP.tipo                                                           
    '';

    IF stExercicio::integer > 2012 THEN
        stSql := stSql || ''                                       ,2            '';       
    ELSE
        stSql := stSql || ''                                       ,CP.sequencia '';                                        
    END IF;

    stSql := stSql || ''
                                                          ,''''D'''') as cod_plano_debito                                          
                            ,tp.cod_plano as cod_plano_credito                                                               
                            ,nlp.vl_pago AS vl_pago                                                                          
                                                                                                                             
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
                            JOIN contabilidade.pagamento as cp                                                               
                            ON (    cp.cod_entidade         = nlp.cod_entidade                                               
                                AND cp.exercicio_liquidacao = nlp.exercicio                                                  
                                AND cp.cod_nota             = nlp.cod_nota                                                   
                                AND cp.timestamp            = nlp.timestamp                                                  
                            )                                                                                                
                            JOIN contabilidade.lancamento_empenho as cle                                                     
                            ON (    cle.cod_lote     = cp.cod_lote                                                           
                                AND cle.cod_entidade = cp.cod_entidade                                                       
                                AND cle.sequencia    = cp.sequencia                                                          
                                AND cle.exercicio    = cp.exercicio                                                          
                                AND cle.tipo         = cp.tipo                                                               
                            )                                                                                                
                     WHERE  cle.estorno = false
                '' || stFiltroPagamentoTmp || ''

            ); '';
        EXECUTE stSql;                                                                                                              
        CREATE INDEX btree_tmp_pagamentos ON tmp_pgto    ( exercicio, cod_entidade, cod_boletim, timestamp  );     
 
        stSql := ''                                                                                                                            
            -- Estornos                                                                                                      
            CREATE TEMPORARY TABLE tmp_estornos as (                                                                         
                             SELECT  tpe.cod_boletim
                                    ,tpe.cod_entidade
                                    ,tpe.exercicio_boletim as exercicio
                                    ,tpe.timestamp_anulado as timestamp
                                    ,tp.exercicio_empenho
                                    ,tp.cod_empenho
                                    ,true as estorno
                                    ,tp.tipo
                                    ,tpe.cgm_usuario
                                    ,tpe.cod_terminal
                                    ,tp.cod_plano_credito as cod_plano_debito                                                
                                    ,tp.cod_plano_debito as cod_plano_credito                                                
                                    ,coalesce(nlpa.vl_anulado,0.00) as vl_pago                                          
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
                                                 ,nl.exercicio_empenho                                                       
                                                 ,nl.cod_empenho
                                                 ,cp.tipo
                                                 ,contabilidade.fn_recupera_conta_lancamento( CP.exercicio                   
                                                                               ,CP.cod_entidade                              
                                                                               ,CP.cod_lote                                  
                                                                               ,CP.tipo                                      
    '';

    IF stExercicio::integer > 2012 THEN
        stSql := stSql || ''                                       ,2            '';       
    ELSE
        stSql := stSql || ''                                       ,CP.sequencia '';                                        
    END IF;

    stSql := stSql || ''
                                                                               ,''''D'''') as cod_plano_debito                     
                                                 ,tp.cod_plano as cod_plano_credito                                          
                                                                                                                             
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
                                                 JOIN contabilidade.pagamento as cp                                          
                                                 ON (    cp.cod_entidade         = nlp.cod_entidade                          
                                                     AND cp.exercicio_liquidacao = nlp.exercicio                             
                                                     AND cp.cod_nota             = nlp.cod_nota                              
                                                     AND cp.timestamp            = nlp.timestamp                             
                                                 )                                                                           
                                                 JOIN contabilidade.lancamento_empenho as cle                                
                                                 ON (    cle.cod_lote     = cp.cod_lote                                      
                                                     AND cle.cod_entidade = cp.cod_entidade                                  
                                                     AND cle.sequencia    = cp.sequencia                                     
                                                     AND cle.exercicio    = cp.exercicio                                     
                                                     AND cle.tipo         = cp.tipo                                          
                                                 )                                                                           
                                         where  cle.estorno = false                                                          
                                            and tp.cod_entidade in ('' || stEntidade || '')                        
                                            and tp.exercicio_boletim = ''''''|| stExercicio || ''''''                   
                                                                                                                             
                                    ) as TP on (    tp.cod_nota     = tpe.cod_nota                                           
                                                AND tp.exercicio    = tpe.exercicio                                          
                                                AND tp.cod_entidade = tpe.cod_entidade                                       
                                                AND tp.timestamp    = tpe.timestamp                                          
                                    )                                                                                        
                                                                                                                             
                              WHERE tpe.cod_boletim is not null
                            '' || stFiltroPagamentoEstornado || ''
            ); '';                                                                                                                  
            EXECUTE stSql;
            CREATE INDEX btree_tmp_estornos ON tmp_estornos ( exercicio, cod_entidade, cod_boletim, timestamp  );
            
            stSql := ''
            -- DEMONSTRATIVO
            CREATE TEMPORARY TABLE tmp_pagamento AS (
                        SELECT
                               tb.cod_boletim
                              ,TO_CHAR( TB.dt_boletim, ''''dd/mm/yyyy'''' ) as dt_boletim
                              ,TO_CHAR( TP.timestamp, ''''HH24:mi:ss'''' ) as hora
                              ,TP.exercicio
                              ,TP.exercicio_empenho
                              ,TP.cod_empenho as empenho
                              ,TP.vl_pago as vl_pago
                              ,tp.cod_plano_credito as conta_credito
                              ,TP.cod_plano_debito  as conta_debito
                              ,tp.estorno
                              ,tp.tipo
                              ,tp.cgm_usuario
                              ,tp.cod_terminal
                              ,tp.cod_entidade
                        FROM tesouraria.boletim   AS TB
                             JOIN ( SELECT cod_boletim 
                                          ,cod_entidade
                                          ,exercicio
                                          ,timestamp   
                                          ,exercicio_empenho
                                          ,cod_empenho
                                          ,estorno
                                          ,tipo
                                          ,cgm_usuario
                                          ,cod_terminal
                                          ,cod_plano_debito 
                                          ,cod_plano_credito
                                          ,vl_pago
                                    FROM tmp_pgto
                                
                                    UNION
            
                                    SELECT cod_boletim
                                          ,cod_entidade
                                          ,exercicio
                                          ,timestamp
                                          ,exercicio_empenho
                                          ,cod_empenho
                                          ,estorno
                                          ,tipo
                                          ,cgm_usuario
                                          ,cod_terminal
                                          ,cod_plano_debito
                                          ,cod_plano_credito
                                          ,vl_pago
                                    FROM tmp_estornos                     
                             ) as TP ON (   tp.cod_boletim  = tb.cod_boletim
                                        AND tp.cod_entidade = tb.cod_entidade
                                        AND tp.exercicio    = tb.exercicio
                             )
                            WHERE tb.dt_boletim is not null
                        '' || stFiltroPagamento || ''
            
                            GROUP BY
                               TP.exercicio
                              ,TP.exercicio_empenho
                              ,tp.cod_plano_credito
                              ,TP.cod_plano_debito
                              ,tb.dt_boletim
                              ,tb.cod_boletim
                              ,tp.timestamp
                              ,tp.estorno
                              ,tp.vl_pago
                              ,tp.cod_empenho
                              ,tp.tipo
                              ,tp.cgm_usuario
                              ,tp.cod_terminal
                              ,tp.cod_entidade
                            ORDER BY tp.timestamp
                  );
    '';
    EXECUTE stSql;


    stFiltroArrecadacaoAux := replace( stFiltroArrecadacao, '''''''', '''''''''''' );

    IF boTCEMS = ''true'' THEN        
        stSql := ''SELECT tesouraria.fn_listar_arrecadacao_tce( '''''' || stFiltroArrecadacaoAux || '''''', '''''' || stFiltroArrecadacaoAux || '''''' )'';
    ELSE
        stSql := ''SELECT tesouraria.fn_listar_arrecadacao( '''''' || stFiltroArrecadacaoAux || '''''', '''''' || stFiltroArrecadacaoAux || '''''' )'';
    END IF ;

    EXECUTE stSql;


    IF boTCEMS = ''true'' THEN        
        stSql := ''             
                SELECT tbl.cod_boletim
                  ,tbl.dt_boletim
                  ,tbl.hora
                  ,tbl.procedencia
                  ,tbl.estorno
                  ,tbl.valor
                  ,CASE WHEN CPCC.nom_conta IS NULL THEN
                        receita.cod_receita
                   ELSE
                        tbl.conta_credito
                   END
                  ,CASE WHEN CPCC.nom_conta IS NULL THEN
                        conta_receita.descricao
                   ELSE
                        CPCC.nom_conta
                   END AS nom_conta_credito
                  ,CASE WHEN CPCD.nom_conta IS NULL THEN
                        receita.cod_receita 
                   ELSE
                        tbl.conta_debito
                   END
                  ,CASE WHEN CPCD.nom_conta IS NULL THEN
                        conta_receita.descricao
                   ELSE
                        CPCD.nom_conta
                   END AS nom_conta_debito
                  ,tbl.cgm_usuario
                  ,CGM.nom_cgm
                  ,cast( tbl.tipo as VARCHAR )
                  ,tbl.cod_tipo -- Tipo de Transferência | Utilizado para especializar as transferencias no relatório
            '';

    ELSE
        stSql := ''
                SELECT tbl.cod_boletim
                      ,tbl.dt_boletim
                      ,tbl.hora
                      ,tbl.procedencia
                      ,tbl.estorno
                      ,tbl.valor
                      , tbl.conta_credito
                      , CPCC.nom_conta AS nom_conta_credito
                      , tbl.conta_debito
                      , CPCD.nom_conta AS nom_conta_debito                      
                      ,tbl.cgm_usuario
                      ,CGM.nom_cgm
                      ,cast( tbl.tipo as VARCHAR )
                      ,tbl.cod_tipo -- Tipo de Transferência | Utilizado para especializar as transferencias no relatório
            '';
    END IF ;

    stSql := stSql || '' 
                
            FROM(
                SELECT TP.cod_boletim
                      ,substr(random()::VARCHAR,3,10) as id
                      ,TP.exercicio
                      ,CAST( TP.dt_boletim AS VARCHAR ) AS dt_boletim
                      ,CAST( TP.hora AS VARCHAR ) AS hora
                      ,CASE WHEN TP.estorno 
                        THEN CAST( ''''Estorno Pagamento Empenho nr. ''''||TP.cod_entidade||'''' - ''''||TP.empenho||''''/''''||TP.exercicio_empenho AS VARCHAR )
                        ELSE CAST( ''''Pagamento Empenho nr. ''''||TP.cod_entidade||'''' - ''''||TP.empenho||''''/''''||TP.exercicio_empenho AS VARCHAR )
                      END AS procedencia
                      ,CASE WHEN TP.estorno 
                        THEN CAST( TRUE  AS BOOLEAN )
                        ELSE CAST( FALSE AS BOOLEAN )
                      END AS estorno
                      ,CAST( TP.vl_pago AS NUMERIC ) AS valor
                      ,TP.conta_credito
                      ,TP.conta_debito
                      ,TP.cgm_usuario
                      ,''''P'''' as tipo
                      ,0 as cod_tipo
                      ,null::integer AS cod_receita
                FROM tmp_pagamento AS TP

                UNION

                SELECT TT.cod_boletim
                      ,substr(random()::VARCHAR,3,10) as id
                      ,TT.exercicio
                      ,CAST( TT.dt_boletim AS VARCHAR ) AS dt_boletim
                      ,CAST( TT.hora AS VARCHAR ) AS hora
                      ,CASE WHEN TT.cod_tipo = 1 THEN CAST( ''''Pagamento Extra '''' || TT.cod_entidade||'''' - ''''||TT.cod_lote AS VARCHAR )
                            WHEN TT.cod_tipo = 2 THEN CAST( ''''Arrecadação Extra '''' || TT.cod_entidade||'''' - ''''||TT.cod_lote AS VARCHAR )
                            WHEN TT.cod_tipo = 3 THEN CAST( ''''Aplicação '''' || TT.cod_entidade||'''' - ''''||TT.cod_lote AS VARCHAR )
                            WHEN TT.cod_tipo = 4 THEN CAST( ''''Resgate '''' || TT.cod_entidade||'''' - ''''||TT.cod_lote AS VARCHAR )
                            WHEN TT.cod_tipo = 5 THEN CAST( ''''Depósito/Retirada '''' || TT.cod_entidade||'''' - ''''||TT.cod_lote AS VARCHAR )
                            ELSE CAST( ''''Transferência '''' || TT.cod_entidade||'''' - ''''||TT.cod_lote AS VARCHAR )
                       END AS procedencia
                      ,CAST( FALSE AS BOOLEAN ) AS estorno  
                      ,CAST( TT.vl_lancamento AS NUMERIC ) AS valor
                      ,TT.conta_credito
                      ,TT.conta_debito
                      ,TT.cgm_usuario
                      ,''''T'''' as tipo
                      ,TT.cod_tipo as cod_tipo
                      ,null::integer AS cod_receita
                FROM tmp_transferencia AS TT

                UNION

                SELECT TTE.cod_boletim
                      ,substr(random()::VARCHAR,3,10) as id
                      ,TTE.exercicio
                      ,CAST( TTE.dt_boletim AS VARCHAR ) AS dt_boletim
                      ,CAST( TTE.hora AS VARCHAR ) AS hora
                      ,CASE WHEN TTE.cod_tipo = 1 THEN CAST( ''''Estorno de Pagamento Extra '''' || TTE.cod_entidade||'''' - ''''||TTE.cod_lote AS VARCHAR )
                            WHEN TTE.cod_tipo = 2 THEN CAST( ''''Estorno de Arrecadação Extra '''' || TTE.cod_entidade||'''' - ''''||TTE.cod_lote AS VARCHAR )
                      --      WHEN TTE.cod_tipo = 3 THEN CAST( ''''Estorno de Aplicação '''' || TTE.cod_entidade||'''' - ''''||TTE.cod_lote AS VARCHAR )
                      --      WHEN TTE.cod_tipo = 4 THEN CAST( ''''Estorno de Resgate '''' || TTE.cod_entidade||'''' - ''''||TTE.cod_lote AS VARCHAR )
                      --      WHEN TTE.cod_tipo = 5 THEN CAST( ''''Estorno de Depósito/Retirada '''' || TTE.cod_entidade||'''' - ''''||TTE.cod_lote AS VARCHAR )
                            ELSE CAST( ''''Estorno de Transferência '''' || TTE.cod_entidade||'''' - ''''||TTE.cod_lote AS VARCHAR )
                       END AS procedencia
                      ,CAST( TRUE AS BOOLEAN ) AS estorno  
                      ,CAST( TTE.vl_lancamento AS NUMERIC ) AS valor
                      ,TTE.conta_credito
                      ,TTE.conta_debito
                      ,TTE.cgm_usuario
                      ,''''T'''' as tipo
                      ,TTE.cod_tipo as cod_tipo
                      ,null::integer AS cod_receita
                FROM tmp_transferencia_estornada AS TTE

                UNION

                SELECT TA.cod_boletim
                      ,substr(random()::VARCHAR,3,10) as id
                      ,TA.exercicio
                      ,CAST( TA.dt_boletim AS VARCHAR ) AS dt_boletim
                      ,CAST( TA.hora AS VARCHAR ) AS hora
                      ,CASE WHEN TA.numeracao != ''''''''
                         THEN CAST( ''''Arrecadação nr. '''' || TA.numeracao  AS VARCHAR )
                         ELSE CAST( ''''Arrecadação Receita nr. '''' || TA.cod_receita AS VARCHAR )
                       END AS procedencia
                      ,CAST( FALSE AS BOOLEAN ) AS estorno  
                      ,CAST( TA.valor - vl_desconto + vl_multa + vl_juros  AS NUMERIC ) AS valor
                      ,TA.conta_credito
                      ,TA.conta_debito
                      ,TA.cgm_usuario
                      ,''''A'''' as tipo
                      ,0 as cod_tipo
                      ,TA.cod_receita::integer AS cod_receita
                FROM tmp_arrecadacao AS TA

                UNION

                SELECT TAE.cod_boletim
                      ,substr(random()::VARCHAR,3,10) as id
                      ,TAE.exercicio
                      ,CAST( TAE.dt_boletim AS VARCHAR ) AS dt_boletim
                      ,CAST( TAE.hora AS VARCHAR ) AS hora
                      ,CASE WHEN TAE.numeracao != ''''''''
                         THEN CAST( ''''Estorno de Arrecadação nr. '''' || TAE.numeracao  AS VARCHAR )
                         ELSE CAST( ''''Estorno de Arrecadação Receita nr. '''' || TAE.cod_receita AS VARCHAR )
                       END AS procedencia
                      ,CAST( TRUE AS BOOLEAN ) AS estorno  
                      ,CAST( TAE.valor - vl_desconto + vl_multa + vl_juros  AS NUMERIC ) AS valor
                      ,TAE.conta_credito
                      ,TAE.conta_debito
                      ,TAE.cgm_usuario
                      ,''''A'''' as tipo
                      ,0 as cod_tipo
                      ,TAE.cod_receita::integer AS cod_receita
                FROM tmp_arrecadacao_estornada AS TAE
                
            ) AS tbl
            '';

            IF boTCEMS = ''true'' THEN
                stSql := stSql || '' 
                LEFT JOIN contabilidade.plano_analitica CPAD
                       ON tbl.exercicio    = CPAD.exercicio
                      AND tbl.conta_debito = CPAD.cod_plano
                LEFT JOIN contabilidade.plano_conta CPCD
                       ON CPAD.exercicio   = CPCD.exercicio
                      AND CPAD.cod_conta   = CPCD.cod_conta
                LEFT JOIN contabilidade.plano_analitica CPAC
                       ON tbl.exercicio    = CPAC.exercicio
                      AND tbl.conta_credito= CPAC.cod_plano
                LEFT JOIN contabilidade.plano_conta CPCC
                       ON CPAC.exercicio   = CPCC.exercicio
                      AND CPAC.cod_conta   = CPCC.cod_conta
                LEFT JOIN orcamento.receita
                       ON tbl.cod_receita = receita.cod_receita
                      AND tbl.exercicio = receita.exercicio
                LEFT JOIN orcamento.conta_receita
                       ON receita.cod_conta = conta_receita.cod_conta
                      AND receita.exercicio = conta_receita.exercicio
               INNER JOIN sw_cgm CGM
                       ON tbl.cgm_usuario  = CGM.numcgm '';
            ELSE
                stSql := stSql || ''
                    ,contabilidade.plano_analitica AS CPAD
                    ,contabilidade.plano_conta     AS CPCD
                    ,contabilidade.plano_analitica AS CPAC
                    ,contabilidade.plano_conta     AS CPCC
                    ,sw_cgm                        AS CGM
                    WHERE tbl.exercicio    = CPAD.exercicio
                      AND tbl.conta_debito = CPAD.cod_plano
                      AND CPAD.exercicio   = CPCD.exercicio
                      AND CPAD.cod_conta   = CPCD.cod_conta

                      AND tbl.exercicio    = CPAC.exercicio
                      AND tbl.conta_credito= CPAC.cod_plano
                      AND CPAC.exercicio   = CPCC.exercicio
                      AND CPAC.cod_conta   = CPCC.cod_conta

                      AND tbl.cgm_usuario  = CGM.numcgm
                '';
            END IF ;
            stSql := stSql || ''
            ORDER BY cod_boletim
                    ,dt_boletim
    '';


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

'language 'plpgsql';

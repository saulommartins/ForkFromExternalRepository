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
 * Consulta que busca os dados do relatório Razão do Credor
 * Data de Criação   : 07/04/2009


 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz
 
 * @package URBEM
 * @subpackage 

 $Id:$
*/

CREATE OR REPLACE FUNCTION contabilidade.fn_relatorio_razao_credor(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio            ALIAS FOR $1;
    stDtInicial            ALIAS FOR $2;
    stDtFinal              ALIAS FOR $3;
    stCodEntidades         ALIAS FOR $4;
    stCgmCredor            ALIAS FOR $5;
    stCodEmpenho           ALIAS FOR $6;
    stCodOrgao             ALIAS FOR $7;
    stCodUnidade           ALIAS FOR $8;
    stCodRecurso           ALIAS FOR $9;
    stDestinacaoRecurso    ALIAS FOR $10;
    stCodDespesa           ALIAS FOR $11;
    stDemonstrarRestos     ALIAS FOR $12;
    stDemonstrarLiquidacao ALIAS FOR $13;
    stSql                  VARCHAR   := '';
    reRegistro             RECORD;
BEGIN

    stSql := '
    CREATE TEMPORARY TABLE tmp_retorno AS (
    SELECT tbl.*                                                                   
          ,conta_debito.cod_estrutural  AS cod_estrutural_debito                   
          ,conta_credito.cod_estrutural AS cod_estrutural_credito                  
          , publico.fn_mascara_dinamica( ( SELECT valor                            
                                           FROM administracao.configuracao         
                                           WHERE parametro = ''masc_despesa''
                                             AND exercicio = ''' || stExercicio || ''' ), dotacao ) as dotacao_formatada 
    FROM (                                                                          
           SELECT tbl.cgm_beneficiario                                              
                 ,CGM.nom_cgm                                                       
                 ,tbl.exercicio                                                     
                 ,tbl.exercicio_empenho                                             
                 ,tbl.cod_entidade                                                  
                 ,tbl.cod_empenho                                                   
                 ,CLE.estorno                                                       
                 ,CL.complemento                                                    
                 ,tbl.tipo                                                          
                 ,CVL.cod_lote                                                      
                 ,CVL.sequencia                                                     
                 ,CL.cod_historico                                                  
                 ,CHC.nom_historico                                                 
                 ,TO_CHAR( CLO.dt_lote, ''dd/mm/yyyy'' ) as dt_lote                   
                 ,contabilidade.fn_recupera_conta_lancamento( CVL.exercicio         
                                                                 ,CVL.cod_entidade  
                                                                 ,CVL.cod_lote      
                                                                 ,CVL.tipo          
                                                                 ,CVL.sequencia     
                                                                 ,''D''
                 ) AS cod_plano_debito                                              
                 ,contabilidade.fn_recupera_conta_lancamento( CVL.exercicio         
                                                                 ,CVL.cod_entidade  
                                                                 ,CVL.cod_lote      
                                                                 ,CVL.tipo          
                                                                 ,CVL.sequencia     
                                                                 ,''C''               
                 ) AS cod_plano_credito                                             
                 ,abs( CVL.vl_lancamento ) AS vl_lancamento                         
                 ,CASE WHEN tbl.implantado = false                                  
                    THEN OD.num_orgao                                               
                  ||''.''||OD.num_unidade                                             
                  ||''.''||OD.cod_funcao                                              
                  ||''.''||OD.cod_subfuncao                                           
                  ||''.''||PPRO.num_programa                                           
                  ||''.''||acao.num_acao                                              
                  ||''.''||replace(OCD.cod_estrutural,''.'','''')                         
                    ELSE ( SELECT ERPE.num_orgao                                    
                           ||''.''||ERPE.num_unidade                                  
                           ||''.''||ERPE.cod_funcao                                   
                           ||''.''||ERPE.cod_subfuncao                                
                           ||''.''||ERPE.cod_programa                                 
                           ||''.''||ERPE.num_pao                                      
                           ||''.''||ERPE.cod_estrutural                               
                           FROM empenho.restos_pre_empenho AS ERPE                  
                           WHERE ERPE.exercicio       = tbl.exercicio_empenho       
                             AND ERPE.cod_pre_empenho = tbl.cod_pre_empenho )       
                  END AS dotacao                                                    
                 ,CASE WHEN tbl.implantado = false                                  
                     THEN OCD.descricao                                             
                     ELSE ''''                                                        
                  END as descricao                                                  
                 ,EPED.cod_despesa                                                  
           FROM (                                                                   
                  SELECT EPE.cgm_beneficiario                                            
                        ,CE.exercicio                                                    
                        ,EE.exercicio as exercicio_empenho                               
                        ,CE.cod_entidade                                                 
                        ,CE.cod_empenho                                                  
                        ,EPE.cod_pre_empenho                                             
                        ,CE.cod_lote                                                     
                        ,CE.tipo                                                         
                        ,CE.sequencia                                                    
                        ,EPE.implantado                                                  
                  FROM empenho.empenho             AS EE                                 
                      ,empenho.pre_empenho         AS EPE                                
                      ,contabilidade.empenhamento  AS CE                                 
                    -- Join com pre_empenho                                              
                  WHERE EE.cod_pre_empenho   = EPE.cod_pre_empenho                       
                    AND EE.exercicio         = EPE.exercicio                             
                    -- Join com contabilidade_empenhamento                               
                    AND EE.exercicio         = CE.exercicio_empenho                      
                    AND EE.cod_entidade      = CE.cod_entidade                           
                    AND EE.cod_empenho       = CE.cod_empenho ';
                    -- Filtro                                                            
                    IF (stDemonstrarRestos = 'S') THEN
                        stSql := stSql || ' AND EE.exercicio = ''' || stExercicio || ''' ';
                    END IF;
                    stSql := stSql || '
                    AND EPE.cgm_beneficiario = ' || stCgmCredor || '  
                    AND EE.cod_entidade     IN ( ' || stCodEntidades || ' )  
                    AND EE.dt_empenho between TO_DATE( ''' || stDtInicial || '''::varchar, ''dd/mm/yyyy'' ) 
                                          AND TO_DATE( ''' || stDtFinal || '''::varchar  , ''dd/mm/yyyy'' ) 
                                                                                    
                  UNION                                                             
                                                                                    
                  SELECT EPE.cgm_beneficiario                                       
                        ,CL.exercicio                                               
                        ,EE.exercicio as exercicio_empenho                          
                        ,CL.cod_entidade                                            
                        ,ENL.cod_empenho                                            
                        ,EPE.cod_pre_empenho                                        
                        ,CL.cod_lote                                                
                        ,CL.tipo                                                    
                        ,CL.sequencia                                               
                        ,EPE.implantado                                             
                  FROM empenho.pre_empenho        AS EPE                            
                      ,empenho.empenho            AS EE                             
                      ,empenho.nota_liquidacao    AS ENL                            
                      ,contabilidade.liquidacao   AS CL                             
                  WHERE EPE.cod_pre_empenho = EE.cod_pre_empenho                    
                    AND EPE.exercicio       = EE.exercicio                          
                    -- Join com liquidacao                                          
                    AND EE.exercicio        = ENL.exercicio_empenho                 
                    AND EE.cod_entidade     = ENL.cod_entidade                      
                    AND EE.cod_empenho      = ENL.cod_empenho                       
                    -- Join com contabilidade_liquidacao                            
                    AND ENL.exercicio       = CL.exercicio_liquidacao               
                    AND ENL.cod_entidade    = CL.cod_entidade                       
                    AND ENL.cod_nota        = CL.cod_nota ';
                    -- Filtro                                                       
                    IF (stDemonstrarRestos = 'S') THEN
                        stSql := stSql || ' AND EE.exercicio = ''' || stExercicio || ''' ';
                    END IF;
                    stSql := stSql || '
                    AND EPE.cgm_beneficiario = ' || stCgmCredor || ' 
                    AND EE.cod_entidade     IN ( ' || stCodEntidades || ' ) 
                    AND ENL.dt_liquidacao between TO_DATE( ''' || stDtInicial || '''::varchar, ''dd/mm/yyyy'' ) 
                                              AND TO_DATE( ''' || stDtFinal   || '''::varchar, ''dd/mm/yyyy'' ) 
                                                                                       
                  UNION                                                                
                                                                                       
                  SELECT EPE.cgm_beneficiario                                          
                        ,CP.exercicio                                                  
                        ,EE.exercicio as exercicio_empenho                             
                        ,CP.cod_entidade                                               
                        ,ENL.cod_empenho                                               
                        ,EPE.cod_pre_empenho                                           
                        ,CP.cod_lote                                                   
                        ,CP.tipo                                                       
                        ,CP.sequencia                                                  
                        ,EPE.implantado                                                
                  FROM empenho.pre_empenho          AS EPE                             
                      ,empenho.empenho              AS EE                              
                      ,empenho.nota_liquidacao      AS ENL                             
                      ,empenho.nota_liquidacao_paga AS ENLP                            
                      ,contabilidade.pagamento      AS CP                              
                  WHERE EPE.cod_pre_empenho = EE.cod_pre_empenho                       
                    AND EPE.exercicio       = EE.exercicio                             
                    -- Join com liquidacao                                             
                    AND EE.exercicio      = ENL.exercicio_empenho                      
                    AND EE.cod_entidade   = ENL.cod_entidade                           
                    AND EE.cod_empenho    = ENL.cod_empenho                            
                    -- Join com nota_liquidacao_paga                                   
                    AND ENL.exercicio     = ENLP.exercicio                             
                    AND ENL.cod_entidade  = ENLP.cod_entidade                          
                    AND ENL.cod_nota      = ENLP.cod_nota                              
                    -- Join com contabilidade_pagamento                                
                    AND ENLP.exercicio    = CP.exercicio_liquidacao                    
                    AND ENLP.cod_entidade = CP.cod_entidade                            
                    AND ENLP.cod_nota     = CP.cod_nota                                
                    AND ENLP.timestamp    = CP.timestamp ';
                    -- Filtro                                                          
                    IF (stDemonstrarRestos = 'S') THEN
                        stSql := stSql || ' AND EE.exercicio = ''' || stExercicio || ''' ';
                    END IF;
                    stSql := stSql || '
                    AND EPE.cgm_beneficiario = ' || stCgmCredor || '
                    AND EE.cod_entidade     IN ( ' || stCodEntidades || ' )
                    AND TO_DATE(ENLP.timestamp::varchar, ''yyyy-mm-dd'' ) 
                              between TO_DATE( ''' || stDtInicial || '''::varchar, ''dd/mm/yyyy'' ) 
                                  AND TO_DATE( ''' || stDtFinal || '''::varchar  , ''dd/mm/yyyy'' ) 
                                                                                     
                  ORDER BY cgm_beneficiario                                          
                          ,cod_entidade                                              
                          ,exercicio                                                 
                          ,cod_empenho                                               
                          ,cod_lote                                                  
                          ,sequencia                                                 
               )                                 AS tbl                              
               ,contabilidade.lancamento_empenho AS CLE                              
               ,contabilidade.lancamento         AS CL                               
               ,contabilidade.historico_contabil AS CHC                              
               ,contabilidade.valor_lancamento   AS CVL                              
               ,contabilidade.lote               AS CLO                              
               ,empenho.pre_empenho_despesa      AS EPED                             
               ,orcamento.conta_despesa          AS OCD                              
               ,orcamento.despesa                AS OD
                JOIN orcamento.despesa_acao
                  ON despesa_acao.exercicio_despesa     = OD.exercicio
                 AND despesa_acao.cod_despesa           = OD.cod_despesa
                JOIN ppa.acao
                  ON acao.cod_acao      = despesa_acao.cod_acao
                JOIN orcamento.programa AS OPRO
                  ON OPRO.exercicio     = OD.exercicio
                 AND OPRO.cod_programa  = OD.cod_programa
                JOIN orcamento.programa_ppa_programa
                  ON programa_ppa_programa.exercicio    = OPRO.exercicio
                 AND programa_ppa_programa.cod_programa = OPRO.cod_programa
                JOIN ppa.programa AS PPRO
                  ON PPRO.cod_programa  = programa_ppa_programa.cod_programa_ppa
                JOIN orcamento.recurso as REC                                
                  ON od.cod_recurso     = rec.cod_recurso                        
                 AND od.exercicio       = rec.exercicio                         
               ,sw_cgm                           AS CGM                      
             -- Join com lancamento_empenho                                  
           WHERE tbl.exercicio    = CLE.exercicio                            
             AND tbl.cod_entidade = CLE.cod_entidade                         
             AND tbl.tipo         = CLE.tipo                                 
             AND tbl.cod_lote     = CLE.cod_lote                             
             AND tbl.sequencia    = CLE.sequencia                            
             -- Join com lancamento                                          
             AND CLE.exercicio    = CL.exercicio                             
             AND CLE.cod_entidade = CL.cod_entidade                          
             AND CLE.tipo         = CL.tipo                                  
             AND CLE.cod_lote     = CL.cod_lote                              
             -- Join com historico_contabil                                  
             AND CL.cod_historico = CHC.cod_historico                        
             AND CL.exercicio     = CHC.exercicio                            
             -- Join com valor_lancamento                                    
             AND CL.exercicio     = CVL.exercicio                            
             AND CL.cod_entidade  = CVL.cod_entidade                         
             AND CL.tipo          = CVL.tipo                                 
             AND CL.cod_lote      = CVL.cod_lote                             
             AND CL.sequencia     = CVL.sequencia                            
             -- Join com lote                                                
             AND CL.exercicio     = CLO.exercicio                            
             AND CL.cod_entidade  = CLO.cod_entidade                         
             AND CL.cod_lote      = CLO.cod_lote                             
             AND CL.tipo          = CLO.tipo                                 
             -- Join com empenho.pre_empenho_despesa                         
             AND tbl.exercicio       = EPED.exercicio                        
             AND tbl.cod_pre_empenho = EPED.cod_pre_empenho                  
             -- Join com orcamento.conta_despesa                             
             AND EPED.cod_conta      = OCD.cod_conta                         
             AND EPED.exercicio      = OCD.exercicio                         
             -- Join com orcamento_despesa                                   
             AND EPED.cod_despesa    = OD.cod_despesa                        
             AND EPED.exercicio      = OD.exercicio                          
             -- Join com CGM                                                 
             AND tbl.cgm_beneficiario = CGM.numcgm                           
             -- Filtro                                                       
             AND CVL.tipo_valor      = ''D''
             AND CVL.cod_entidade   IN ( ' || stCodEntidades || ' ) 
             AND CLO.dt_lote between TO_DATE( ''' || stDtInicial || '''::varchar, ''dd/mm/yyyy'' )
                                 AND TO_DATE( ''' || stDtFinal || '''::varchar  , ''dd/mm/yyyy'' ) ';

            IF ( stCodOrgao != '' AND stCodOrgao IS NOT NULL ) THEN
                stSql := stSql || ' AND OD.num_orgao = ' || stCodOrgao || ' ';
            END IF;
            IF ( stCodUnidade != '' AND stCodUnidade IS NOT NULL ) THEN
                stSql := stSql || ' AND OD.num_unidade = ' || stCodUnidade || ' ';
            END IF;
            IF ( stCodRecurso != '' AND stCodRecurso IS NOT NULL ) THEN
                stSql := stSql || ' AND OD.cod_recurso = ' || stCodRecurso || ' ';
            END IF;
            IF ( stDestinacaoRecurso != '' AND stDestinacaoRecurso IS NOT NULL ) THEN
                stSql := stSql || ' AND REC.masc_recurso like ''' || stDestinacaoRecurso || ' %'' ';
            END IF;
            IF ( stCodDespesa != '' AND stCodDespesa IS NOT NULL ) THEN
                stSql := stSql || ' AND OCD.cod_estrutural like publico.fn_mascarareduzida(''' || stCodDespesa || ''')|| ''%'' ';
            END IF;
            IF ( stCodEmpenho != '' AND stCodEmpenho IS NOT NULL  ) THEN
                stSql := stSql || ' AND tbl.cod_empenho = ' || stCodEmpenho || ' ';
            END IF;
            IF ( stDemonstrarLiquidacao = 'N') THEN
                stSql := stSql || ' AND tbl.tipo <> ''L'' ';
            END IF;

            stSql := stSql || '
           ORDER BY tbl.cgm_beneficiario                                  
                   ,tbl.cod_entidade                                      
                   ,tbl.exercicio                                         
                   ,tbl.cod_empenho                                       
                   ,tbl.cod_lote                                          
                   ,CVL.sequencia                                         
    ) AS tbl                                                              
    INNER JOIN (  SELECT CPC.exercicio                                    
                        ,CPA.cod_plano                                    
                        ,CPC.cod_estrutural                               
                  FROM contabilidade.plano_conta     AS CPC               
                      ,contabilidade.plano_analitica AS CPA               
                  WHERE CPA.cod_conta = CPC.cod_conta                     
                    AND CPA.exercicio = CPC.exercicio                     
    ) AS conta_debito ON( tbl.cod_plano_debito = conta_debito.cod_plano   
                      AND tbl.exercicio        = conta_debito.exercicio ) 
    INNER JOIN (  SELECT CPC.exercicio                                    
                        ,CPA.cod_plano                                    
                        ,CPC.cod_estrutural                               
                  FROM contabilidade.plano_conta     AS CPC               
                      ,contabilidade.plano_analitica AS CPA               
                  WHERE CPA.cod_conta = CPC.cod_conta                     
                    AND CPA.exercicio = CPC.exercicio                     
    ) AS conta_credito ON( tbl.cod_plano_credito = conta_credito.cod_plano
                      AND tbl.exercicio          = conta_credito.exercicio )
    );
    ';

    EXECUTE stSql;

    stSql := ' SELECT cgm_beneficiario
                    , nom_cgm 
                    , CAST(exercicio AS VARCHAR) AS exercicio
                    , CAST(exercicio_empenho AS VARCHAR) AS exercicio_empenho
                    , cod_entidade
                    , cod_empenho 
                    , estorno
                    , complemento
                    , CAST(tipo AS VARCHAR) AS tipo
                    , cod_lote  
                    , sequencia 
                    , cod_historico
                    , nom_historico
                    , TO_DATE(dt_lote::varchar, ''dd/mm/yyyy'') AS dt_lote
                    , cod_plano_debito 
                    , cod_plano_credito
                    , CAST(vl_lancamento AS NUMERIC) AS vl_lancamento
                    , CAST(dotacao AS VARCHAR) AS dotacao
                    , descricao
                    , cod_despesa           
                    , cod_estrutural_debito 
                    , cod_estrutural_credito
                    , dotacao_formatada
                 FROM tmp_retorno 
             ORDER BY cod_entidade
                    , cod_empenho
                    , dt_lote
                ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

    DROP TABLE tmp_retorno;

END;
$$ LANGUAGE 'plpgsql';

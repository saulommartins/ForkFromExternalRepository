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



-- DROP FUNCTION detalhe_contra_cheque_base (INTEGER, INTEGER, INTEGER, INTEGER, VARCHAR, VARCHAR, VARCHAR);

CREATE OR REPLACE FUNCTION detalhe_contra_cheque_base (INTEGER, INTEGER, INTEGER, INTEGER, VARCHAR, VARCHAR, VARCHAR)
RETURNS SETOF RECORD AS $$

DECLARE
inCodContrato                   ALIAS FOR $1;
inCodPeriodoMovimentacao        ALIAS FOR $2;
inCodComplementar               ALIAS FOR $3;
inTipoFolha                     ALIAS FOR $4;
dtFinal                         ALIAS FOR $5;
stDesdobramento                 ALIAS FOR $6;
timestampSituacao               ALIAS FOR $7;
stSql                           VARCHAR :='';
reRecord                        RECORD;
stTimestampFechamentoPeriodo    VARCHAR;

--0 Complementar
--1 Salario
--2 Ferias
--3 decimo
--4 Rescisao

BEGIN

    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,'');

--CALCULAR SALARIO
    stSql := '  CREATE TEMPORARY TABLE contra_cheque_salario AS
                SELECT contrato_servidor_salario.*
                  FROM pessoal.contrato_servidor_salario
            INNER JOIN (SELECT contrato_servidor_salario.cod_contrato
                             , max(contrato_servidor_salario.timestamp) as timestamp
                          FROM pessoal.contrato_servidor_salario
                         WHERE contrato_servidor_salario.timestamp <= '''||stTimestampFechamentoPeriodo||'''
                        GROUP BY contrato_servidor_salario.cod_contrato) as max_contrato_servidor_salario
                    ON max_contrato_servidor_salario.cod_contrato = contrato_servidor_salario.cod_contrato
                   AND max_contrato_servidor_salario.timestamp = contrato_servidor_salario.timestamp
                 WHERE contrato_servidor_salario.cod_contrato = '|| inCodContrato ||'
            ';

    EXECUTE stSql;
    

CASE inTipoFolha
    --COMPLEMENTAR
        WHEN 0 THEN
            --CALCULAR BASE PREVIDENCIA
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_previdencia as
                                SELECT sum(evento_complementar_calculado.valor) as valor                                                                        
                                    , contrato.cod_contrato                                                                                                    
                                    , registro_evento_complementar.cod_periodo_movimentacao                                                                    
                                    , 0 AS inFolha                                                                                                             
                                FROM folhapagamento.evento_complementar_calculado                                                                             
                                    , folhapagamento.registro_evento_complementar                                                                              
                                    , pessoal.contrato                                                                                                         
                                    , folhapagamento.previdencia_evento                                                                                        
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_evento                                                                                
                                        GROUP BY cod_previdencia) as max_previdencia_evento                                                                     
                                    , folhapagamento.previdencia_previdencia                                                                                   
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_previdencia                                                                           
                                        GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                
                                WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento                               
                                    AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro                             
                                    AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao                         
                                    AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                                
                                    AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                                        
                                    AND registro_evento_complementar.cod_evento = previdencia_evento.cod_evento                                                  
                                    AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                              
                                    AND previdencia_evento.timestamp = max_previdencia_evento.timestamp                                                          
                                    AND previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                             
                                    AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                    
                                    AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                
                                    AND previdencia_previdencia.tipo_previdencia = ''o''                                                                           
                                    AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'                                      
                                    AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                                        
                                    AND previdencia_evento.cod_tipo = 2                                                                 
                                    AND previdencia_previdencia.cod_previdencia IN (                                                                          
                                                SELECT contrato_servidor_previdencia.cod_previdencia                                                        
                                                    FROM pessoal.contrato_servidor_previdencia                                                                             
                                            INNER JOIN ( SELECT cod_contrato                                                                                
                                                                , max(timestamp) as timestamp                                                                 
                                                            FROM pessoal.contrato_servidor_previdencia                                                       
                                                        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                          
                                                    ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato          
                                                    AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                
                                            INNER JOIN pessoal.contrato_servidor                                                                            
                                                    ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                          
                                                    AND contrato_servidor.cod_contrato = contrato.cod_contrato                                               
                                                    AND contrato_servidor_previdencia.bo_excluido IS FALSE                                                   
                                                UNION
                                                SELECT contrato_pensionista_previdencia.cod_previdencia                                                     
                                                    FROM pessoal.contrato_pensionista_previdencia                                                             
                                            INNER JOIN ( SELECT cod_contrato                                                                                
                                                                , max(timestamp) as timestamp                                                                 
                                                            FROM pessoal.contrato_pensionista_previdencia                                                    
                                                        GROUP BY cod_contrato) as max_contrato_pensionista_previdencia                                       
                                                    ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato    
                                                    AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp          
                                            INNER JOIN pessoal.contrato_pensionista                                                                         
                                                    ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                    
                                                    AND contrato_pensionista.cod_contrato = contrato.cod_contrato                                            
                                        )
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                                    
                                    , registro_evento_complementar.cod_periodo_movimentacao
                ';
                EXECUTE stSql;
                
            --CALCULAR BASE FGTS
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_basefgts as        
                                SELECT sum(evento_complementar_calculado.valor) as valor                                                      
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_complementar.cod_periodo_movimentacao                                                      
                                    , 0 AS inFolha                                                                                               
                                FROM folhapagamento.evento_complementar_calculado                                     
                                    , folhapagamento.registro_evento_complementar                                      
                                    , pessoal.contrato                                                                 
                                    , folhapagamento.fgts_evento                                                       
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                               
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento                 
                                AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro               
                                AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao           
                                AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                  
                                AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                          
                                AND registro_evento_complementar.cod_evento = fgts_evento.cod_evento                                           
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'                                      
                                AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                               
                                AND fgts_evento.cod_tipo = 3                                                          
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                    , registro_evento_complementar.cod_periodo_movimentacao
                ';
                EXECUTE stSql;
                
            --CALCULA FGTS DO MES               
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_fgtsmes as            
                                SELECT sum(evento_complementar_calculado.valor) as valor                                                      
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_complementar.cod_periodo_movimentacao                                                      
                                    , 0 AS inFolha                                                                                               
                                FROM folhapagamento.evento_complementar_calculado                                     
                                    , folhapagamento.registro_evento_complementar                                      
                                    , pessoal.contrato                                                                 
                                    , folhapagamento.fgts_evento                                                       
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                               
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento                 
                                AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro               
                                AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao           
                                AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                  
                                AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                          
                                AND registro_evento_complementar.cod_evento = fgts_evento.cod_evento                                           
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'                                      
                                AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                               
                                AND fgts_evento.cod_tipo = 1                                                          
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                    , registro_evento_complementar.cod_periodo_movimentacao
                ';
                EXECUTE stSql;
                
            --CALCULO BASE IRRF             
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_base_irrf as                 
                                SELECT sum(evento_complementar_calculado.valor) as valor                                                 
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_complementar.cod_periodo_movimentacao                                                      
                                    , 0 AS inFolha                                                                                               
                                FROM folhapagamento.evento_complementar_calculado                                     
                                    , folhapagamento.registro_evento_complementar                                      
                                    , pessoal.contrato                                                                 
                                    , folhapagamento.tabela_irrf_evento                                                
                                    , (SELECT cod_tabela                                                                                         
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.tabela_irrf_evento                                        
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                            
                                WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento                 
                                AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro               
                                AND evento_complementar_calculado.cod_configuracao      = registro_evento_complementar.cod_configuracao        
                                AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                  
                                AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                          
                                AND registro_evento_complementar.cod_evento = tabela_irrf_evento.cod_evento                                    
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                          
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                            
                                AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||'                   
                                AND tabela_irrf_evento.cod_tipo = 7                                                   
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                    , registro_evento_complementar.cod_periodo_movimentacao
                ';
                EXECUTE stSql;
        
    
    --SALARIO    
        WHEN 1 THEN           
            --CALCULAR BASE PREVIDENCIA
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_previdencia as    
                                SELECT SUM(evento_calculado.valor) as valor                                                                                    
                                    , contrato.cod_contrato                                                                                                    
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                                         
                                    , 1 AS inFolha                                                                                                             
                                FROM folhapagamento.evento_calculado                                                                                          
                                    , folhapagamento.registro_evento                                                                                           
                                    , folhapagamento.registro_evento_periodo                                                                                   
                                    , pessoal.contrato                                                                                                         
                                    , folhapagamento.previdencia_evento                                                                                        
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_evento                                                                                
                                        GROUP BY cod_previdencia) as max_previdencia_evento                                                                     
                                    , folhapagamento.previdencia_previdencia                                                                                   
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_previdencia                                                                           
                                        GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                
                                WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                                 
                                    AND evento_calculado.cod_registro = registro_evento.cod_registro                                                             
                                    AND evento_calculado.timestamp_registro = registro_evento.timestamp                                                          
                                    AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                                      
                                    AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                                             
                                    AND registro_evento.cod_evento = previdencia_evento.cod_evento                                                               
                                    AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                              
                                    AND previdencia_evento.timestamp = max_previdencia_evento.timestamp                                                          
                                    AND previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                             
                                    AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                    
                                    AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                
                                    AND previdencia_previdencia.tipo_previdencia = ''o''                                                                           
                                    AND previdencia_evento.cod_tipo = 2                                                             
                                    AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                                        
                                    AND previdencia_previdencia.cod_previdencia IN (                                                                          
                                                SELECT contrato_servidor_previdencia.cod_previdencia                                                        
                                                    FROM pessoal.contrato_servidor_previdencia                                                                             
                                            INNER JOIN ( SELECT cod_contrato                                                                                
                                                                , max(timestamp) as timestamp                                                                 
                                                            FROM pessoal.contrato_servidor_previdencia                                                       
                                                        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                          
                                                    ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato          
                                                    AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                
                                            INNER JOIN pessoal.contrato_servidor                                                                            
                                                    ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                          
                                                    AND contrato_servidor.cod_contrato = contrato.cod_contrato                                               
                                                    AND contrato_servidor_previdencia.bo_excluido IS FALSE                                                   
                                                UNION
                                                SELECT contrato_pensionista_previdencia.cod_previdencia                                                     
                                                    FROM pessoal.contrato_pensionista_previdencia                                                             
                                            INNER JOIN ( SELECT cod_contrato                                                                                
                                                                , max(timestamp) as timestamp                                                                 
                                                            FROM pessoal.contrato_pensionista_previdencia                                                    
                                                        GROUP BY cod_contrato) as max_contrato_pensionista_previdencia                                       
                                                    ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato    
                                                    AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp          
                                            INNER JOIN pessoal.contrato_pensionista                                                                         
                                                    ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                    
                                                    AND contrato_pensionista.cod_contrato = contrato.cod_contrato                                            
                                        )
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                                    
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                                             
                ';                
                EXECUTE stSql;
                
            --CALCULAR BASE FGTS
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_basefgts as            
                                SELECT sum(evento_calculado.valor) as valor                                                                   
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                           
                                    , 1 AS inFolha                                                                                               
                                FROM folhapagamento.evento_calculado                                                                              
                                    , folhapagamento.registro_evento                                                                             
                                    , folhapagamento.registro_evento_periodo                                                                     
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                   
                                AND evento_calculado.cod_registro = registro_evento.cod_registro                                               
                                AND evento_calculado.timestamp_registro = registro_evento.timestamp                                            
                                AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                        
                                AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                               
                                AND registro_evento.cod_evento = fgts_evento.cod_evento                                                        
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 3                                                                                   
                                AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                                                                                  
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                           
                ';
                EXECUTE stSql;
                
            --CALCULA FGTS DO MES               
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_fgtsmes as
                                SELECT sum(evento_calculado.valor) as valor                                                                   
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                           
                                    , 1 AS inFolha                                                                                               
                                FROM folhapagamento.evento_calculado                                                                              
                                    , folhapagamento.registro_evento                                                                             
                                    , folhapagamento.registro_evento_periodo                                                                     
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                   
                                AND evento_calculado.cod_registro = registro_evento.cod_registro                                               
                                AND evento_calculado.timestamp_registro = registro_evento.timestamp                                            
                                AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                        
                                AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                               
                                AND registro_evento.cod_evento = fgts_evento.cod_evento                                                        
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 1                                                                                   
                                AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                                                                                  
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                               
                ';
                EXECUTE stSql;
            
            --CALCULO BASE IRRF             
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_base_irrf as                     
                                SELECT SUM(evento_calculado.valor) as valor                                                              
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                           
                                    , 1 AS inFolha                                                                                               
                                FROM folhapagamento.evento_calculado                                                  
                                    , folhapagamento.registro_evento                                                   
                                    , folhapagamento.registro_evento_periodo                                           
                                    , pessoal.contrato                                                                 
                                    , folhapagamento.tabela_irrf_evento                                                
                                    , (SELECT cod_tabela                                                                                         
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.tabela_irrf_evento                                        
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                            
                                WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                   
                                AND evento_calculado.cod_registro = registro_evento.cod_registro                                               
                                AND evento_calculado.timestamp_registro = registro_evento.timestamp                                            
                                AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                        
                                AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                               
                                AND registro_evento.cod_evento = tabela_irrf_evento.cod_evento                                                 
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                          
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                            
                                AND tabela_irrf_evento.cod_tipo = 7                                               
                                and contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                and cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                    , registro_evento_periodo.cod_periodo_movimentacao
                ';
                EXECUTE stSql;
                
        
    --FERIAS    
        WHEN 2 THEN           
            --CALCULAR BASE PREVIDENCIA
                stSql := ' CREATE TEMPORARY TABLE contra_cheque_previdencia as            
                        SELECT sum(evento_ferias_calculado.valor) as valor                                                                                   
                            , contrato.cod_contrato                                                                                                    
                            , registro_evento_ferias.cod_periodo_movimentacao                                                                          
                            , 2 AS inFolha                                                                                                             
                        FROM folhapagamento.evento_ferias_calculado                                                                                   
                            , folhapagamento.registro_evento_ferias                                                                                    
                            , pessoal.contrato                                                                                                         
                            , folhapagamento.previdencia_evento                                                                                        
                            , (SELECT cod_previdencia                                                                                                  
                                    , max(timestamp) as timestamp                                                                                      
                                FROM folhapagamento.previdencia_evento                                                                                
                                GROUP BY cod_previdencia) as max_previdencia_evento                                                                     
                            , folhapagamento.previdencia_previdencia                                                                                   
                            , (SELECT cod_previdencia                                                                                                  
                                    , max(timestamp) as timestamp                                                                                      
                                FROM folhapagamento.previdencia_previdencia                                                                           
                                GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                
                        WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                                           
                            AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                                         
                            AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                                        
                            AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                                            
                            AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                                              
                            AND registro_evento_ferias.cod_evento = previdencia_evento.cod_evento                                                        
                            AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                              
                            AND previdencia_evento.timestamp = max_previdencia_evento.timestamp                                                          
                            AND previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                             
                            AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                    
                            AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                
                            AND previdencia_previdencia.tipo_previdencia = ''o''                                                                           
                            AND previdencia_evento.cod_tipo = 2
                ';                                                             
             
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_ferias_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;

                stSql := stSql ||'  AND previdencia_previdencia.cod_previdencia IN (                                                                          
                                        SELECT contrato_servidor_previdencia.cod_previdencia                                                        
                                            FROM pessoal.contrato_servidor_previdencia                                                                             
                                    INNER JOIN ( SELECT cod_contrato                                                                                
                                                        , max(timestamp) as timestamp                                                                 
                                                    FROM pessoal.contrato_servidor_previdencia                                                       
                                                GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                          
                                            ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato          
                                            AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                
                                    INNER JOIN pessoal.contrato_servidor                                                                            
                                            ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                          
                                            AND contrato_servidor.cod_contrato = contrato.cod_contrato                                               
                                            AND contrato_servidor_previdencia.bo_excluido IS FALSE                                                   
                                        UNION
                                        SELECT contrato_pensionista_previdencia.cod_previdencia                                                     
                                            FROM pessoal.contrato_pensionista_previdencia                                                             
                                    INNER JOIN ( SELECT cod_contrato                                                                                
                                                        , max(timestamp) as timestamp                                                                 
                                                    FROM pessoal.contrato_pensionista_previdencia                                                    
                                                GROUP BY cod_contrato) as max_contrato_pensionista_previdencia                                       
                                            ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato    
                                            AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp          
                                    INNER JOIN pessoal.contrato_pensionista                                                                         
                                            ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                    
                                            AND contrato_pensionista.cod_contrato = contrato.cod_contrato                                            
                                )
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                        , registro_evento_ferias.cod_periodo_movimentacao                                                          
                                        , inFolha
                ';
                EXECUTE stSql;
                
            --CALCULAR BASE FGTS
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_basefgts as
                                SELECT sum(evento_ferias_calculado.valor) as valor                                                            
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_ferias.cod_periodo_movimentacao                                                            
                                    , 2 AS inFolha                                                                                               
                                FROM folhapagamento.evento_ferias_calculado                                                                     
                                    , folhapagamento.registro_evento_ferias                                                                      
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                             
                                AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                           
                                AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                          
                                AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                              
                                AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                                
                                AND registro_evento_ferias.cod_evento = fgts_evento.cod_evento                                                 
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 3
                                
                ';                                                                                  
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_ferias_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_ferias.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
            
                EXECUTE stSql;
                
            --CALCULA FGTS DO MES               
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_fgtsmes as
                                SELECT sum(evento_ferias_calculado.valor) as valor                                                            
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_ferias.cod_periodo_movimentacao                                                            
                                    , 2 AS inFolha                                                                                               
                                FROM folhapagamento.evento_ferias_calculado                                                                     
                                    , folhapagamento.registro_evento_ferias                                                                      
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                             
                                AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                           
                                AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                          
                                AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                              
                                AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                                
                                AND registro_evento_ferias.cod_evento = fgts_evento.cod_evento                                                 
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 1
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                ';                                                                                  
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_ferias_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_ferias.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
                
                EXECUTE stSql;
                
            --CALCULO BASE IRRF             
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_base_irrf as
                                SELECT sum(evento_ferias_calculado.valor) as valor                                                       
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_ferias.cod_periodo_movimentacao                                                            
                                    , 2 AS inFolha                                                                                               
                                FROM folhapagamento.evento_ferias_calculado                                           
                                    , folhapagamento.registro_evento_ferias                                            
                                    , pessoal.contrato                                                                 
                                    , folhapagamento.tabela_irrf_evento                                                
                                    , (SELECT cod_tabela                                                                                         
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.tabela_irrf_evento                                        
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                            
                                WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                             
                                AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                           
                                AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                          
                                AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                              
                                AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                                
                                AND registro_evento_ferias.cod_evento = tabela_irrf_evento.cod_evento                                          
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                          
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                            
                                AND tabela_irrf_evento.cod_tipo = 7
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                ';                                               
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_ferias_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_ferias.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
                
                EXECUTE stSql;
        
            
    --DECIMO    
        WHEN 3 THEN           
            --CALCULAR BASE PREVIDENCIA
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_previdencia as                    
                                SELECT sum(evento_decimo_calculado.valor) as valor                                                                                   
                                    , contrato.cod_contrato                                                                                                    
                                    , registro_evento_decimo.cod_periodo_movimentacao                                                                          
                                    , 3 AS inFolha                                                                                                             
                                FROM folhapagamento.evento_decimo_calculado                                                                                   
                                    , folhapagamento.registro_evento_decimo                                                                                    
                                    , pessoal.contrato                                                                                                         
                                    , folhapagamento.previdencia_evento                                                                                        
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_evento                                                                                
                                        GROUP BY cod_previdencia) as max_previdencia_evento                                                                     
                                    , folhapagamento.previdencia_previdencia                                                                                   
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_previdencia                                                                           
                                        GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                
                                WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                                           
                                    AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                                         
                                    AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                                        
                                    AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                                            
                                    AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                                              
                                    AND registro_evento_decimo.cod_evento = previdencia_evento.cod_evento                                                        
                                    AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                              
                                    AND previdencia_evento.timestamp = max_previdencia_evento.timestamp                                                          
                                    AND previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                             
                                    AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                    
                                    AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                
                                    AND previdencia_previdencia.tipo_previdencia = ''o''                                                                           
                                    AND previdencia_evento.cod_tipo = 2
                ';                                                            
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_decimo_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;                
                    
                stSql := stSql ||'  AND previdencia_previdencia.cod_previdencia IN (                                                                          
                                        SELECT contrato_servidor_previdencia.cod_previdencia                                                        
                                            FROM pessoal.contrato_servidor_previdencia                                                                             
                                    INNER JOIN ( SELECT cod_contrato                                                                                
                                                        , max(timestamp) as timestamp                                                                 
                                                    FROM pessoal.contrato_servidor_previdencia                                                       
                                                GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                          
                                            ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato          
                                            AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                
                                    INNER JOIN pessoal.contrato_servidor                                                                            
                                            ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                          
                                            AND contrato_servidor.cod_contrato = contrato.cod_contrato                                               
                                            AND contrato_servidor_previdencia.bo_excluido IS FALSE                                                   
                                        UNION
                                        SELECT contrato_pensionista_previdencia.cod_previdencia                                                     
                                            FROM pessoal.contrato_pensionista_previdencia                                                             
                                    INNER JOIN ( SELECT cod_contrato                                                                                
                                                        , max(timestamp) as timestamp                                                                 
                                                    FROM pessoal.contrato_pensionista_previdencia                                                    
                                                GROUP BY cod_contrato) as max_contrato_pensionista_previdencia                                       
                                            ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato    
                                            AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp          
                                    INNER JOIN pessoal.contrato_pensionista                                                                         
                                            ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                    
                                            AND contrato_pensionista.cod_contrato = contrato.cod_contrato                                            
                                )
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                      
                                        , registro_evento_decimo.cod_periodo_movimentacao                                                          
                                        , inFolha
                ';
                EXECUTE stSql;
                
            --CALCULAR BASE FGTS
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_basefgts as
                                SELECT sum(evento_decimo_calculado.valor) as valor                                                            
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_decimo.cod_periodo_movimentacao                                                            
                                    , 3 AS inFolha                                                                                               
                                FROM folhapagamento.evento_decimo_calculado                                                                     
                                    , folhapagamento.registro_evento_decimo                                                                      
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                             
                                AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                           
                                AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                          
                                AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                              
                                AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                                
                                AND registro_evento_decimo.cod_evento = fgts_evento.cod_evento                                                 
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 3
                ';                                                                                  
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_decimo_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_decimo.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
                
                EXECUTE stSql;
            
            --CALCULA FGTS DO MES               
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_fgtsmes as    
                                SELECT sum(evento_decimo_calculado.valor) as valor                                                            
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_decimo.cod_periodo_movimentacao                                                            
                                    , 3 AS inFolha                                                                                               
                                FROM folhapagamento.evento_decimo_calculado                                                                     
                                    , folhapagamento.registro_evento_decimo                                                                      
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                             
                                AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                           
                                AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                          
                                AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                              
                                AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                                
                                AND registro_evento_decimo.cod_evento = fgts_evento.cod_evento                                                 
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 1
                ';                                                                                  
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_decimo_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_decimo.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
                                
                EXECUTE stSql;
            
            --CALCULO BASE IRRF             
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_base_irrf as    
                                SELECT sum(evento_decimo_calculado.valor) as valor                                                       
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_decimo.cod_periodo_movimentacao                                                            
                                    , 3 AS inFolha                                                                                               
                                FROM folhapagamento.evento_decimo_calculado                                           
                                    , folhapagamento.registro_evento_decimo                                            
                                    , pessoal.contrato                                                                 
                                    , folhapagamento.tabela_irrf_evento                                                
                                    , (SELECT cod_tabela                                                                                         
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.tabela_irrf_evento                                        
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                            
                                WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                             
                                AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                           
                                AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                          
                                AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                              
                                AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                                
                                AND registro_evento_decimo.cod_evento = tabela_irrf_evento.cod_evento                                          
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                          
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                            
                                AND tabela_irrf_evento.cod_tipo = 7
                ';                                              
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_decimo_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_decimo.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';

                EXECUTE stSql;
                
    --RESCISAO
        WHEN 4 THEN
            --CALCULAR BASE PREVIDENCIA
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_previdencia as        
                                SELECT sum(evento_rescisao_calculado.valor) as valor                                                                                 
                                    , contrato.cod_contrato                                                                                                    
                                    , registro_evento_rescisao.cod_periodo_movimentacao                                                                        
                                    , 4 AS inFolha                                                                                                             
                                FROM folhapagamento.evento_rescisao_calculado                                                                                 
                                    , folhapagamento.registro_evento_rescisao                                                                                  
                                    , pessoal.contrato                                                                                                         
                                    , folhapagamento.previdencia_evento                                                                                        
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_evento                                                                                
                                        GROUP BY cod_previdencia) as max_previdencia_evento                                                                     
                                    , folhapagamento.previdencia_previdencia                                                                                   
                                    , (SELECT cod_previdencia                                                                                                  
                                            , max(timestamp) as timestamp                                                                                      
                                        FROM folhapagamento.previdencia_previdencia                                                                           
                                        GROUP BY cod_previdencia) as max_previdencia_previdencia                                                                
                                WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                                       
                                    AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                                     
                                    AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                                    
                                    AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                                        
                                    AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                                            
                                    AND registro_evento_rescisao.cod_evento = previdencia_evento.cod_evento                                                      
                                    AND previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia                                              
                                    AND previdencia_evento.timestamp = max_previdencia_evento.timestamp                                                          
                                    AND previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                             
                                    AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                                    
                                    AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                                                
                                    AND previdencia_previdencia.tipo_previdencia = ''o''                                                                           
                                    AND previdencia_evento.cod_tipo = 2
                ';                                                            
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_rescisao_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;            
        
                stSql := stSql ||'  AND previdencia_previdencia.cod_previdencia IN (                                                                          
                                        SELECT contrato_servidor_previdencia.cod_previdencia                                                        
                                            FROM pessoal.contrato_servidor_previdencia                                                                             
                                    INNER JOIN ( SELECT cod_contrato                                                                                
                                                        , max(timestamp) as timestamp                                                                 
                                                    FROM pessoal.contrato_servidor_previdencia                                                       
                                                GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                          
                                            ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato          
                                            AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                
                                    INNER JOIN pessoal.contrato_servidor                                                                            
                                            ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                          
                                            AND contrato_servidor.cod_contrato = contrato.cod_contrato                                               
                                            AND contrato_servidor_previdencia.bo_excluido IS FALSE                                                   
                                        UNION
                                        SELECT contrato_pensionista_previdencia.cod_previdencia                                                     
                                            FROM pessoal.contrato_pensionista_previdencia                                                             
                                    INNER JOIN ( SELECT cod_contrato                                                                                
                                                        , max(timestamp) as timestamp                                                                 
                                                    FROM pessoal.contrato_pensionista_previdencia                                                    
                                                GROUP BY cod_contrato) as max_contrato_pensionista_previdencia                                       
                                            ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato    
                                            AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp          
                                    INNER JOIN pessoal.contrato_pensionista                                                                         
                                            ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                    
                                            AND contrato_pensionista.cod_contrato = contrato.cod_contrato                                            
                                )
                                AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                GROUP BY contrato.cod_contrato                                                                                                    
                                        , registro_evento_rescisao.cod_periodo_movimentacao                                                                        
                                        , inFolha                                                                                                             
                ';
                EXECUTE stSql;
                
            --CALCULAR BASE FGTS
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_basefgts as
                                SELECT sum(evento_rescisao_calculado.valor) as valor                                                          
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_rescisao.cod_periodo_movimentacao                                                          
                                    , 4 AS inFolha                                                                                               
                                FROM folhapagamento.evento_rescisao_calculado                                                                   
                                    , folhapagamento.registro_evento_rescisao                                                                    
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                         
                                AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                       
                                AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                      
                                AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                          
                                AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                              
                                AND registro_evento_rescisao.cod_evento = fgts_evento.cod_evento                                               
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 3
               ';                                                                                 
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_rescisao_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_rescisao.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
                
                EXECUTE stSql;
            
            --CALCULA FGTS DO MES               
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_fgtsmes as
                                SELECT sum(evento_rescisao_calculado.valor) as valor                                                          
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_rescisao.cod_periodo_movimentacao                                                          
                                    , 4 AS inFolha                                                                                               
                                FROM folhapagamento.evento_rescisao_calculado                                                                   
                                    , folhapagamento.registro_evento_rescisao                                                                    
                                    , pessoal.contrato                                                                                           
                                    , folhapagamento.fgts_evento                                                                                 
                                    , (SELECT cod_fgts                                                                                           
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.fgts_evento                                                                         
                                        GROUP BY cod_fgts) as max_fgts_evento                                                                     
                                WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                         
                                AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                       
                                AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                      
                                AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                          
                                AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                              
                                AND registro_evento_rescisao.cod_evento = fgts_evento.cod_evento                                               
                                AND fgts_evento.cod_fgts = max_fgts_evento.cod_fgts                                                            
                                AND fgts_evento.timestamp = max_fgts_evento.timestamp                                                          
                                AND fgts_evento.cod_tipo = 1
                ';                                                                                 
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_rescisao_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_rescisao.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
                    
                
                EXECUTE stSql;
                
            --CALCULO BASE IRRF             
                stSql := '  CREATE TEMPORARY TABLE contra_cheque_base_irrf as
                                SELECT sum(evento_rescisao_calculado.valor) as valor                                                     
                                    , contrato.cod_contrato                                                                                      
                                    , registro_evento_rescisao.cod_periodo_movimentacao                                                          
                                    , 4 AS inFolha                                                                                               
                                FROM folhapagamento.evento_rescisao_calculado                                         
                                    , folhapagamento.registro_evento_rescisao                                          
                                    , pessoal.contrato                                                                 
                                    , folhapagamento.tabela_irrf_evento                                                
                                    , (SELECT cod_tabela                                                                                         
                                            , max(timestamp) as timestamp                                                                        
                                        FROM folhapagamento.tabela_irrf_evento                                        
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                            
                                WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                         
                                AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                       
                                AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                      
                                AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                          
                                AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                              
                                AND registro_evento_rescisao.cod_evento = tabela_irrf_evento.cod_evento                                        
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                          
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                            
                                AND tabela_irrf_evento.cod_tipo = 7
                ';                                              
                IF(stDesdobramento != '')THEN
                    stSql := stSql ||' AND evento_rescisao_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
                END IF;
                
                stSql := stSql ||'
                                    AND contrato.cod_contrato = '|| inCodContrato ||'                                                                                          
                                    AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    GROUP BY contrato.cod_contrato                                                                                      
                                                , registro_evento_rescisao.cod_periodo_movimentacao                                                          
                                                , inFolha
                    ';
                
                EXECUTE stSql;
                
--END CASE
END CASE;
                      
--CALCULO FAIXA IRRF
        stSql :=' CREATE TEMPORARY TABLE contra_cheque_faixa_irrf AS
                        SELECT ' || inCodContrato || ' as cod_contrato
                        ,faixa_desconto_irrf.*
                        FROM folhapagamento.faixa_desconto_irrf                                                                               
                            , folhapagamento.tabela_irrf                                                                                       
                            , (  SELECT cod_tabela                                                                                                                       
                                    , max(timestamp) as timestamp                                                                                                      
                                    FROM folhapagamento.tabela_irrf                                                                             
                                WHERE tabela_irrf.vigencia = (SELECT vigencia                                                                                          
                                                                FROM folhapagamento.tabela_irrf                                              
                                                                    , (SELECT cod_tabela                                                                                
                                                                            , max(timestamp) as timestamp                                                               
                                                                        FROM folhapagamento.tabela_irrf                                      
                                                                        WHERE vigencia <= (  SELECT dt_final                                                            
                                                                                                FROM folhapagamento.periodo_movimentacao       
                                                                                            ORDER BY cod_periodo_movimentacao DESC                                       
                                                                                                LIMIT 1)                                                                  
                                                                        GROUP BY cod_tabela) as max_tabela_irrf                                                         
                                                                WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela                                               
                                                                    AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp)                                               
                                GROUP BY cod_tabela) as max_tabela_irrf
                        WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela                                                                                        
                        AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp                                                                                         
                        AND tabela_irrf.cod_tabela = faixa_desconto_irrf.cod_tabela                                                                                    
                        AND tabela_irrf.timestamp  = faixa_desconto_irrf.timestamp                                                                                     
                        AND (SELECT valor
                                FROM (SELECT sum(evento_calculado.valor) as valor                                                                                       
                                    , contrato.cod_contrato                                                                                                              
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                                                   
                                    , 1 AS inFolha                                                                                                                       
                                FROM folhapagamento.evento_calculado                                                                          
                                    , folhapagamento.registro_evento                                                                           
                                    , folhapagamento.registro_evento_periodo                                                                   
                                    , pessoal.contrato                                                                                         
                                    , folhapagamento.tabela_irrf_evento                                                                        
                                    , (SELECT cod_tabela                                                                                                                 
                                            , max(timestamp) as timestamp                                                                                                
                                        FROM folhapagamento.tabela_irrf_evento                                                                
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                    
                                WHERE evento_calculado.cod_evento = registro_evento.cod_evento                                                                           
                                AND evento_calculado.cod_registro = registro_evento.cod_registro                                                                       
                                AND evento_calculado.timestamp_registro = registro_evento.timestamp                                                                    
                                AND registro_evento.cod_registro = registro_evento_periodo.cod_registro                                                                
                                AND registro_evento_periodo.cod_contrato = contrato.cod_contrato                                                                       
                                AND registro_evento.cod_evento = tabela_irrf_evento.cod_evento                                                                         
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                  
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                    
                                AND tabela_irrf_evento.cod_tipo = 7                                                                       
                            GROUP BY contrato.cod_contrato                                                                                                              
                                    , registro_evento_periodo.cod_periodo_movimentacao                                                                                       
                                UNION                                                                                                                                    
                                SELECT evento_ferias_calculado.valor                                                                                                      
                                    , contrato.cod_contrato                                                                                                              
                                    , registro_evento_ferias.cod_periodo_movimentacao                                                                                    
                                    , 2 AS inFolha                                                                                                                       
                                FROM folhapagamento.evento_ferias_calculado                                                                   
                                    , folhapagamento.registro_evento_ferias                                                                    
                                    , pessoal.contrato                                                                                         
                                    , folhapagamento.tabela_irrf_evento                                                                        
                                    , (SELECT cod_tabela                                                                                                                 
                                            , max(timestamp) as timestamp                                                                                                
                                        FROM folhapagamento.tabela_irrf_evento                                                                
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                    
                                WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento                                                     
                                AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro                                                   
                                AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento                                                  
                                AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                                                      
                                AND registro_evento_ferias.cod_contrato = contrato.cod_contrato                                                                        
                                AND registro_evento_ferias.cod_evento = tabela_irrf_evento.cod_evento                                                                  
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                  
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                    
                                AND tabela_irrf_evento.cod_tipo = 7                                                                       
                                AND evento_ferias_calculado.desdobramento = ''F''                                                      
                                
                                UNION                                                                                                                                    
                                
                                SELECT evento_decimo_calculado.valor                                                                                                      
                                    , contrato.cod_contrato                                                                                                              
                                    , registro_evento_decimo.cod_periodo_movimentacao                                                                                    
                                    , 3 AS inFolha                                                                                                                       
                                FROM folhapagamento.evento_decimo_calculado                                                                   
                                    , folhapagamento.registro_evento_decimo                                                                    
                                    , pessoal.contrato                                                                                         
                                    , folhapagamento.tabela_irrf_evento                                                                        
                                    , (SELECT cod_tabela                                                                                                                 
                                            , max(timestamp) as timestamp                                                                                                
                                        FROM folhapagamento.tabela_irrf_evento                                                                
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                    
                                WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento                                                     
                                AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro                                                   
                                AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento                                                  
                                AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp                                                      
                                AND registro_evento_decimo.cod_contrato = contrato.cod_contrato                                                                        
                                AND registro_evento_decimo.cod_evento = tabela_irrf_evento.cod_evento                                                                  
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                  
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                    
                                AND tabela_irrf_evento.cod_tipo = 7                                                                       
                                ';
        IF(stDesdobramento != '')THEN
                stSql := stSql ||' AND evento_decimo_calculado.desdobramento = '|| quote_literal(stDesdobramento) ||' ';
        END IF;
        stSql := stSql ||'                                 
                                UNION                                                                                                                                     
                                
                                SELECT evento_rescisao_calculado.valor                                                                                                    
                                    , contrato.cod_contrato                                                                                                              
                                    , registro_evento_rescisao.cod_periodo_movimentacao                                                                                  
                                    , 4 AS inFolha                                                                                                                       
                                FROM folhapagamento.evento_rescisao_calculado                                                                 
                                    , folhapagamento.registro_evento_rescisao                                                                  
                                    , pessoal.contrato                                                                                         
                                    , folhapagamento.tabela_irrf_evento                                                                        
                                    , (SELECT cod_tabela                                                                                                                 
                                            , max(timestamp) as timestamp                                                                                                
                                        FROM folhapagamento.tabela_irrf_evento                                                                
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                    
                                WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento                                                 
                                AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro                                               
                                AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento                                              
                                AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp                                                  
                                AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                                                      
                                AND registro_evento_rescisao.cod_evento = tabela_irrf_evento.cod_evento                                                                
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                  
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                    
                                AND tabela_irrf_evento.cod_tipo = 7                                                                       
                                AND evento_rescisao_calculado.desdobramento = ''S''                                                      
                                
                                UNION                                                                                                                                     
                                
                                SELECT sum(evento_complementar_calculado.valor) as valor                                                                                  
                                    , contrato.cod_contrato                                                                                                              
                                    , registro_evento_complementar.cod_periodo_movimentacao                                                                              
                                    , 0 AS inFolha                                                                                                                       
                                FROM folhapagamento.evento_complementar_calculado                                                             
                                    , folhapagamento.registro_evento_complementar                                                              
                                    , pessoal.contrato                                                                                         
                                    , folhapagamento.tabela_irrf_evento                                                                        
                                    , (SELECT cod_tabela                                                                                                                 
                                            , max(timestamp) as timestamp                                                                                                
                                        FROM folhapagamento.tabela_irrf_evento                                                                
                                        GROUP BY cod_tabela) as max_tabela_irrf_evento                                                                                    
                                WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento                                         
                                AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro                                       
                                AND evento_complementar_calculado.cod_configuracao      = registro_evento_complementar.cod_configuracao                                
                                AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp                                          
                                AND registro_evento_complementar.cod_contrato = contrato.cod_contrato                                                                  
                                AND registro_evento_complementar.cod_evento = tabela_irrf_evento.cod_evento                                                            
                                AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela                                                                  
                                AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp                                                                    
                                AND registro_evento_complementar.cod_complementar = '|| inCodComplementar || '
                                AND tabela_irrf_evento.cod_tipo = 7                                                                       
                            GROUP BY contrato.cod_contrato                                                                                                              
                                    , registro_evento_complementar.cod_periodo_movimentacao ) as irrf                                                                             
                            WHERE cod_contrato = '|| inCodContrato ||'                                                                                          
                            AND cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                            AND inFolha = '|| inTipoFolha ||')
                        BETWEEN faixa_desconto_irrf.vl_inicial                                                            
                        AND faixa_desconto_irrf.vl_final
                        
        ';
    EXECUTE stSql;

--CALCULO Depedentes
        stSql :=' CREATE TEMPORARY TABLE contra_cheque_dependentes AS
                        SELECT
                        COALESCE( COUNT(servidor_dependente.cod_servidor),0 ) as contador
                        ,servidor_dependente.cod_servidor
                        ,servidor_contrato_servidor.cod_contrato
                        from pessoal.servidor_dependente
                        left outer join pessoal.dependente
                            on dependente.cod_dependente = servidor_dependente.cod_dependente
                        left outer join public.sw_cgm_pessoa_fisica
                            on dependente.numcgm = sw_cgm_pessoa_fisica.numcgm
                        left outer join folhapagamento.vinculo_irrf
                            on vinculo_irrf.cod_vinculo = dependente.cod_vinculo
                        left outer join pessoal.dependente_excluido
                            on servidor_dependente.cod_dependente = dependente_excluido.cod_dependente
                        and servidor_dependente.cod_servidor = dependente_excluido.cod_servidor
                        inner join pessoal.servidor_contrato_servidor
                            on(servidor_contrato_servidor.cod_servidor = servidor_dependente.cod_servidor
                                AND servidor_contrato_servidor.cod_contrato = '|| inCodContrato ||')
                        where sw_cgm_pessoa_fisica.dt_nascimento is not null
                        and dependente.cod_vinculo > 0
                        and ( vinculo_irrf.idade_limite = 0 
                                or (idade( to_char(sw_cgm_pessoa_fisica.dt_nascimento,''yyyy-mm-dd'' ), to_date('|| quote_literal(dtFinal) ||',''dd/mm/yyyy'')::varchar)) <= vinculo_irrf.idade_limite )
                        and dependente_excluido.cod_servidor is null   
                        GROUP BY servidor_dependente.cod_servidor
                                ,servidor_contrato_servidor.cod_contrato
        ';
    EXECUTE stSql;

        
-- SELECT das tabelas temporarias           
    stSql :='    
            
            SELECT   contra_cheque_salario.salario::NUMERIC 		as base_salario
                    ,contra_cheque_previdencia.valor::NUMERIC 	        as base_previdencia
                    ,contra_cheque_basefgts.valor::NUMERIC    	        as base_fgts
                    ,contra_cheque_fgtsmes.valor::NUMERIC 		as fgts_mes
                    ,contra_cheque_base_irrf.valor::NUMERIC		as base_irrf
                    ,contra_cheque_faixa_irrf.aliquota::NUMERIC	        as faixa_irrf
                    ,contra_cheque_dependentes.contador::BIGINT         as dependentes_irrf
            FROM
                 contra_cheque_salario
            LEFT JOIN contra_cheque_previdencia
                ON(contra_cheque_previdencia.cod_contrato = contra_cheque_salario.cod_contrato)
            LEFT JOIN contra_cheque_basefgts
                ON(contra_cheque_basefgts.cod_contrato = contra_cheque_salario.cod_contrato)
            LEFT JOIN contra_cheque_fgtsmes
                ON(contra_cheque_fgtsmes.cod_contrato = contra_cheque_salario.cod_contrato)
            LEFT JOIN contra_cheque_base_irrf
                ON(contra_cheque_base_irrf.cod_contrato = contra_cheque_salario.cod_contrato)
	    LEFT JOIN contra_cheque_dependentes
                ON(contra_cheque_dependentes.cod_contrato = contra_cheque_salario.cod_contrato)
            LEFT JOIN contra_cheque_faixa_irrf
                ON(contra_cheque_faixa_irrf.cod_contrato = contra_cheque_dependentes.cod_contrato)
                
        ';        
    
    
    FOR reRecord IN EXECUTE stSql
    LOOP
        RETURN NEXT reRecord;
    END LOOP;


DROP TABLE contra_cheque_salario;
DROP TABLE contra_cheque_previdencia;
DROP TABLE contra_cheque_basefgts;
DROP TABLE contra_cheque_fgtsmes;
DROP TABLE contra_cheque_base_irrf;
DROP TABLE contra_cheque_faixa_irrf;
DROP TABLE contra_cheque_dependentes;


END;
$$ LANGUAGE 'plpgsql';


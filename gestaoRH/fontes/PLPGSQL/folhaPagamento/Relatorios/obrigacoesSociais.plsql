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
--/**
--    * Função PLSQL
--    * Data de Criação: 13/03/2009
--
--
--    * @author Desenvolvedor: Diego Lemos de Souza
--*/
CREATE OR REPLACE FUNCTION obrigacoesSociais(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,BOOLEAN,NUMERIC,NUMERIC,NUMERIC,VARCHAR,VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER) RETURNS SETOF colunasObrigacoesSociais AS $$
DECLARE
    inCodPeriodoMovimentacao    ALIAS FOR $1;
    inCodPrevidencia            ALIAS FOR $2;
    inCodConfiguracao           ALIAS FOR $3;
    inCodRegimePrevidencia      ALIAS FOR $4;
    inExercicio                 ALIAS FOR $5;
    stTipoFiltro                ALIAS FOR $6;
    boAgrupar                   ALIAS FOR $7;
    nuAliquotaPatronal          ALIAS FOR $8;
    nuAliquotaRat               ALIAS FOR $9;
    nuAliquotaFap               ALIAS FOR $10;
    stSituacao                  ALIAS FOR $11;
    stCodigos                   ALIAS FOR $12;
    inCodAtributo               ALIAS FOR $13;
    inCodTipoAtributo           ALIAS FOR $14;
    stEntidade                  ALIAS FOR $15;
    stOrdenacao                 ALIAS FOR $16;
    inCodComplementar           ALIAS FOR $17;
    stSql                       VARCHAR:='';
    reRegistro                  RECORD;
    rwObrigacoesSociais         colunasObrigacoesSociais%ROWTYPE;    
BEGIN
    stSql := '
    SELECT count(contrato.*) as contador
         , COALESCE(sum(base_previdencia.valor),0) as base_previdencia
         , COALESCE(sum(base_previdencia.valor)*('||nuAliquotaPatronal||'+('||nuAliquotaRat||' * '||nuAliquotaFap||'))/100,0) as vlr_patronal
         , COALESCE(sum(desconto_previdencia.valor),0) as desconto_previdencia     
         , COALESCE(sum(maternidade.valor),0) as maternidade
         , COALESCE(sum(salario_familia.valor),0) as salario_familia  
         , COALESCE(sum(base_fgts.valor),0) as base_fgts  
         , COALESCE(sum(recolhido_fgts.valor),0) as recolhido_fgts';

    IF stTipoFiltro = 'local_grupo' AND boAgrupar IS TRUE THEN
        stSql := stSql ||' , local.cod_local||''-''||local.descricao as agrupamento';
    END IF;
    IF stTipoFiltro = 'sub_divisao_grupo' AND boAgrupar IS TRUE THEN
        stSql := stSql ||' , sub_divisao.cod_sub_divisao||''-''||sub_divisao.descricao as agrupamento';
    END IF;
    IF stTipoFiltro = 'atributo_servidor_grupo' AND boAgrupar IS TRUE THEN
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql ||' , atributo_contrato_servidor_valor.cod_atributo||''-''||atributo_valor_padrao.valor_padrao as agrupamento';        
        ELSE
            stSql := stSql ||' , atributo_contrato_servidor_valor.cod_atributo||''-''||atributo_contrato_servidor_valor.valor as agrupamento';        
        END IF;
    END IF;
    IF stTipoFiltro = 'atributo_pensionista_grupo' AND boAgrupar IS TRUE THEN
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql ||' , atributo_contrato_pensionista.cod_atributo||''-''||atributo_valor_padrao.valor_padrao as agrupamento';        
        ELSE
            stSql := stSql ||' , atributo_contrato_pensionista.cod_atributo||''-''||atributo_contrato_pensionista.valor as agrupamento';        
        END IF;
    END IF;
    IF stTipoFiltro = 'lotacao_grupo' AND boAgrupar IS TRUE THEN
        stSql := stSql ||' , vw_orgao_nivel.orgao||''-''||recuperaDescricaoOrgao(vw_orgao_nivel.cod_orgao,'|| quote_literal(inExercicio ||'-01-01') ||'::date) as agrupamento';
    END IF;
    IF stTipoFiltro = 'geral' OR boAgrupar IS FALSE THEN
        stSql := stSql ||' , '''' as agrupamento';
    END IF;
    stSql := stSql ||' FROM pessoal'||stEntidade||'.contrato';

    --FOLHA COMPLEMENTAR
    IF inCodConfiguracao = 0 THEN
        stSql := stSql || '
             LEFT JOIN (SELECT sum(evento_complementar_calculado.valor) as valor                                                      
                            , registro_evento_complementar.cod_contrato                                                                                                            
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                         
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia                                                    
                           ON previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                   INNER JOIN (  SELECT cod_previdencia                                                                 
                                      , max(timestamp) as timestamp                                                     
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                          
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                 
                           ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia                                                                
                           ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                   INNER JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado
                           ON previdencia_evento.cod_evento = evento_complementar_calculado.cod_evento
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                           ON evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
                          AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                          AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp				  
                        WHERE cod_tipo = 2                                                                              
                          AND tipo_previdencia = ''o''                                                                    
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                      
                          AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                          AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                      GROUP BY registro_evento_complementar.cod_contrato                                                                                                           
                      ORDER BY registro_evento_complementar.cod_contrato) AS base_previdencia                                
                   ON base_previdencia.cod_contrato = contrato.cod_contrato
                   
                   
            LEFT JOIN (SELECT sum(evento_complementar_calculado.valor) as valor                                                                                              
                            , registro_evento_complementar.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                                                   
                            , folhapagamento'||stEntidade||'.previdencia_previdencia                                                                              
                            , (  SELECT cod_previdencia                                                                                           
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                                                    
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                                           
                            , folhapagamento'||stEntidade||'.previdencia                                                                                          
                            , folhapagamento'||stEntidade||'.evento_complementar_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_complementar                                                                              
                        WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                        
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp                                              
                          AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                               
                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                               
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp                                     
                          AND previdencia_evento.cod_evento = evento_complementar_calculado.cod_evento                                                         
                          AND evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro                                                
                          AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                          AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          AND cod_tipo = 1                                                                                                        
                          AND tipo_previdencia = ''o''                                                                                              
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                                                                     
                          AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                          AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'                           
                      GROUP BY registro_evento_complementar.cod_contrato                                                                               
                       ORDER BY registro_evento_complementar.cod_contrato) AS desconto_previdencia                                                     
                    ON desconto_previdencia.cod_contrato = contrato.cod_contrato                                                         
            LEFT JOIN (SELECT sum(evento_complementar_calculado.valor) as valor                                                       
                            , registro_evento_complementar.cod_contrato                                                                  
                         FROM pessoal'||stEntidade||'.assentamento_assentamento                                                                     
                            , pessoal'||stEntidade||'.assentamento                                                                                  
                            , (  SELECT cod_assentamento                                                                            
                                      , max(timestamp) as timestamp                                                                 
                                   FROM pessoal'||stEntidade||'.assentamento                                                                        
                               GROUP BY cod_assentamento) as max_assentamento                                                       
                            , pessoal'||stEntidade||'.assentamento_evento                                                                           
                            , folhapagamento'||stEntidade||'.registro_evento_complementar                                                                
                            , folhapagamento'||stEntidade||'.evento_complementar_calculado                                                                       
                        WHERE assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento                            
                          AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                     
                          AND assentamento.timestamp = max_assentamento.timestamp                                                   
                          AND assentamento.cod_assentamento = assentamento_evento.cod_assentamento                                  
                          AND assentamento.timestamp = assentamento_evento.timestamp                                                
                          AND assentamento_assentamento.cod_motivo = 7                                                              
                          AND assentamento_evento.cod_evento = evento_complementar_calculado.cod_evento                                          
                          AND evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro                                  
                          AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                          AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'             
                          AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                      GROUP BY registro_evento_complementar.cod_contrato                                                      
                       ORDER BY registro_evento_complementar.cod_contrato) AS maternidade                                     
                    ON maternidade.cod_contrato = contrato.cod_contrato                                                  
            LEFT JOIN (SELECT evento_complementar_calculado.cod_evento                                                                                         
                            , evento_complementar_calculado.valor                                                                                              
                            , registro_evento_complementar.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                               
                            , (SELECT cod_regime_previdencia                                                                                      
                                    , max(timestamp) as timestamp                                                                                 
                                 FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                       
                               GROUP BY cod_regime_previdencia) as max_salario_familia_evento                                                     
                            , folhapagamento'||stEntidade||'.evento_complementar_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_complementar                                                                              
                        WHERE salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia                   
                          AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp                                             
                          AND salario_familia_evento.cod_evento = evento_complementar_calculado.cod_evento                                                     
                          AND evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro                                                
                          AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                          AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'   
                          AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                          AND salario_familia_evento.cod_tipo = 1 
                          AND salario_familia_evento.cod_regime_previdencia = '||inCodRegimePrevidencia||') AS salario_familia                                               
                   ON salario_familia.cod_contrato = contrato.cod_contrato                                                                        
            LEFT JOIN (SELECT registro_evento_complementar.cod_contrato                                                                                
                            , sum(evento_complementar_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_complementar_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_complementar                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_complementar_calculado.cod_evento                                                                
                          AND evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro                                                
                          AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                          AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                          AND cod_tipo = 3                                                                                                        
                      GROUP BY registro_evento_complementar.cod_contrato  ) as base_fgts                                                               
                   ON base_fgts.cod_contrato = contrato.cod_contrato                                                                              
            LEFT JOIN (SELECT registro_evento_complementar.cod_contrato                                                                                
                            , SUM(evento_complementar_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_complementar_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_complementar                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_complementar_calculado.cod_evento                                                                
                          AND evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro                                                
                          AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                          AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                          AND cod_tipo = 1                                                                                                        
                      GROUP BY registro_evento_complementar.cod_contrato  ) as recolhido_fgts                                                          
                   ON recolhido_fgts.cod_contrato = contrato.cod_contrato';
    END IF;

    --FOLHA SALÁRIO
    IF inCodConfiguracao = 1 THEN
        stSql := stSql || '
            LEFT JOIN (SELECT sum(evento_calculado.valor) as valor                                                      
                            , registro_evento_periodo.cod_contrato                                                                                                          
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                         
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia         
                           ON previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                   INNER JOIN (  SELECT cod_previdencia 
                                      , max(timestamp) as timestamp
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                               GROUP BY cod_previdencia) as max_previdencia_previdencia
                           ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia
                           ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                   INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                           ON previdencia_evento.cod_evento = evento_calculado.cod_evento
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                           ON evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                        WHERE cod_tipo = 2                                                                              
                          AND tipo_previdencia = ''o''                                                                    
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                      
                          AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                          AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                     
                      GROUP BY registro_evento_periodo.cod_contrato                                                                                                        
                      ORDER BY registro_evento_periodo.cod_contrato) AS base_previdencia                                
                   ON base_previdencia.cod_contrato = contrato.cod_contrato
                   
            LEFT JOIN (SELECT sum(evento_calculado.valor) as valor                                                                                              
                            , registro_evento_periodo.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                                                   
                            , folhapagamento'||stEntidade||'.previdencia_previdencia                                                                              
                            , (  SELECT cod_previdencia                                                                                           
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                                                    
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                                           
                            , folhapagamento'||stEntidade||'.previdencia                                                                                          
                            , folhapagamento'||stEntidade||'.evento_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_periodo                                                                              
                        WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                        
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp                                              
                          AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                               
                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                               
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp                                     
                          AND previdencia_evento.cod_evento = evento_calculado.cod_evento                                                         
                          AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro                                                
                          AND cod_tipo = 1                                                                                                        
                          AND tipo_previdencia = ''o''                                                                                              
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                                                                     
                          AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                               
                      GROUP BY registro_evento_periodo.cod_contrato                                                                               
                       ORDER BY registro_evento_periodo.cod_contrato) AS desconto_previdencia                                                     
                    ON desconto_previdencia.cod_contrato = contrato.cod_contrato                                                         
            LEFT JOIN (SELECT sum(evento_calculado.valor) as valor                                                       
                 , registro_evento_periodo.cod_contrato                                                                  
              FROM pessoal'||stEntidade||'.assentamento_assentamento                                                                     
                 , pessoal'||stEntidade||'.assentamento                                                                                  
                 , (  SELECT cod_assentamento                                                                            
                           , max(timestamp) as timestamp                                                                 
                        FROM pessoal'||stEntidade||'.assentamento                                                                        
                    GROUP BY cod_assentamento) as max_assentamento                                                       
                 , pessoal'||stEntidade||'.assentamento_evento                                                                           
                 , folhapagamento'||stEntidade||'.registro_evento_periodo                                                                
                 , folhapagamento'||stEntidade||'.evento_calculado                                                                       
             WHERE assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento                            
               AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                     
               AND assentamento.timestamp = max_assentamento.timestamp                                                   
               AND assentamento.cod_assentamento = assentamento_evento.cod_assentamento                                  
               AND assentamento.timestamp = assentamento_evento.timestamp                                                
               AND assentamento_assentamento.cod_motivo = 7                                                              
               AND assentamento_evento.cod_evento = evento_calculado.cod_evento                                          
               AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro                                  
               AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'             
                          AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                      
                      GROUP BY registro_evento_periodo.cod_contrato                                                      
                       ORDER BY registro_evento_periodo.cod_contrato) AS maternidade                                     
                    ON maternidade.cod_contrato = contrato.cod_contrato                                                  
            LEFT JOIN (SELECT 
            --evento_calculado.cod_evento                                                                                         
                              sum(evento_calculado.valor)  as valor                                                                                           
                            , registro_evento_periodo.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                               
                            , (SELECT cod_regime_previdencia                                                                                      
                                    , max(timestamp) as timestamp                                                                                 
                                 FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                       
                               GROUP BY cod_regime_previdencia) as max_salario_familia_evento                                                     
                            , folhapagamento'||stEntidade||'.evento_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_periodo                                                                              
                        WHERE salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia                   
                          AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp                                             
                          AND salario_familia_evento.cod_evento = evento_calculado.cod_evento                                                     
                          AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro                                                
                          AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'   
                          AND salario_familia_evento.cod_tipo = 1 
                          AND salario_familia_evento.cod_regime_previdencia = '||inCodRegimePrevidencia||'
                          GROUP BY registro_evento_periodo.cod_contrato) AS salario_familia                                               
                   ON salario_familia.cod_contrato = contrato.cod_contrato                                                                        
            LEFT JOIN (SELECT registro_evento_periodo.cod_contrato                                                                                
                            , sum(evento_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_periodo                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_calculado.cod_evento                                                                
                          AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro                                                
                          AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                               
                          AND cod_tipo = 3                                                                                                        
                      GROUP BY registro_evento_periodo.cod_contrato  ) as base_fgts                                                               
                   ON base_fgts.cod_contrato = contrato.cod_contrato                                                                              
            LEFT JOIN (SELECT registro_evento_periodo.cod_contrato                                                                                
                            , SUM(evento_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_periodo                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_calculado.cod_evento                                                                
                          AND evento_calculado.cod_registro = registro_evento_periodo.cod_registro                                                
                          AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND (desdobramento IS NULL OR desdobramento = ''A'' OR desdobramento = ''F'')                                               
                          AND cod_tipo = 1                                                                                                        
                      GROUP BY registro_evento_periodo.cod_contrato  ) as recolhido_fgts                                                          
                   ON recolhido_fgts.cod_contrato = contrato.cod_contrato';
    END IF;

    --FOLHA FÉRIAS
    IF inCodConfiguracao = 2 THEN
        stSql := stSql || '
            LEFT JOIN (SELECT sum(evento_ferias_calculado.valor) as valor                                                      
                            , registro_evento_ferias.cod_contrato                                                                                                          
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                         
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia                                                    
                           ON previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                   INNER JOIN (  SELECT cod_previdencia                                                                 
                                      , max(timestamp) as timestamp                                                     
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                          
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                 
                           ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia     
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia                                                                
                           ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                   INNER JOIN folhapagamento'||stEntidade||'.evento_ferias_calculado                                                           
                           ON previdencia_evento.cod_evento = evento_ferias_calculado.cod_evento
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_ferias                                                    
                           ON evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro                      
                          AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                          AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp                    
                        WHERE cod_tipo = 2                                                                              
                          AND tipo_previdencia = ''o''                                                                    
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                      
                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                      GROUP BY registro_evento_ferias.cod_contrato                                                                                                         
                      ORDER BY registro_evento_ferias.cod_contrato) AS base_previdencia                                
                   ON base_previdencia.cod_contrato = contrato.cod_contrato         
            LEFT JOIN (SELECT sum(evento_ferias_calculado.valor) as valor                                                                                              
                            , registro_evento_ferias.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                                                   
                            , folhapagamento'||stEntidade||'.previdencia_previdencia                                                                              
                            , (  SELECT cod_previdencia                                                                                           
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                                                    
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                                           
                            , folhapagamento'||stEntidade||'.previdencia                                                                                          
                            , folhapagamento'||stEntidade||'.evento_ferias_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_ferias                                                                              
                        WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                        
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp                                              
                          AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                               
                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                               
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp                                     
                          AND previdencia_evento.cod_evento = evento_ferias_calculado.cod_evento                                                         
                          AND evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro                                                
                          AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                          AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          AND cod_tipo = 1                                                                                                        
                          AND tipo_previdencia = ''o''                                                                                              
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                                                                     
                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                      GROUP BY registro_evento_ferias.cod_contrato                                                                               
                       ORDER BY registro_evento_ferias.cod_contrato) AS desconto_previdencia                                                     
                    ON desconto_previdencia.cod_contrato = contrato.cod_contrato                                                         
            LEFT JOIN (SELECT sum(evento_ferias_calculado.valor) as valor                                                       
                            , registro_evento_ferias.cod_contrato                                                                  
                         FROM pessoal'||stEntidade||'.assentamento_assentamento                                                                     
                            , pessoal'||stEntidade||'.assentamento                                                                                  
                            , (  SELECT cod_assentamento                                                                            
                                      , max(timestamp) as timestamp                                                                 
                                   FROM pessoal'||stEntidade||'.assentamento                                                                        
                               GROUP BY cod_assentamento) as max_assentamento                                                       
                            , pessoal'||stEntidade||'.assentamento_evento                                                                           
                            , folhapagamento'||stEntidade||'.registro_evento_ferias                                                                
                            , folhapagamento'||stEntidade||'.evento_ferias_calculado                                                                       
                        WHERE assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento                            
                          AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                     
                          AND assentamento.timestamp = max_assentamento.timestamp                                                   
                          AND assentamento.cod_assentamento = assentamento_evento.cod_assentamento                                  
                          AND assentamento.timestamp = assentamento_evento.timestamp                                                
                          AND assentamento_assentamento.cod_motivo = 7                                                              
                          AND assentamento_evento.cod_evento = evento_ferias_calculado.cod_evento                                          
                          AND evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro                                  
                          AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                          AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'             
                      GROUP BY registro_evento_ferias.cod_contrato                                                      
                       ORDER BY registro_evento_ferias.cod_contrato) AS maternidade                                     
                    ON maternidade.cod_contrato = contrato.cod_contrato                                                  
            LEFT JOIN (SELECT evento_ferias_calculado.cod_evento                                                                                         
                            , evento_ferias_calculado.valor                                                                                              
                            , registro_evento_ferias.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                               
                            , (SELECT cod_regime_previdencia                                                                                      
                                    , max(timestamp) as timestamp                                                                                 
                                 FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                       
                               GROUP BY cod_regime_previdencia) as max_salario_familia_evento                                                     
                            , folhapagamento'||stEntidade||'.evento_ferias_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_ferias                                                                              
                        WHERE salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia                   
                          AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp                                             
                          AND salario_familia_evento.cod_evento = evento_ferias_calculado.cod_evento                                                     
                          AND evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro                                                
                          AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                          AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'   
                          AND salario_familia_evento.cod_tipo = 1 
                          AND salario_familia_evento.cod_regime_previdencia = '||inCodRegimePrevidencia||') AS salario_familia                                               
                   ON salario_familia.cod_contrato = contrato.cod_contrato                                                                        
            LEFT JOIN (SELECT registro_evento_ferias.cod_contrato                                                                                
                            , sum(evento_ferias_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_ferias_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_ferias                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_ferias_calculado.cod_evento                                                                
                          AND evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro                                                
                          AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                          AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND cod_tipo = 3                                                                                                        
                      GROUP BY registro_evento_ferias.cod_contrato  ) as base_fgts                                                               
                   ON base_fgts.cod_contrato = contrato.cod_contrato                                                                              
            LEFT JOIN (SELECT registro_evento_ferias.cod_contrato                                                                                
                            , SUM(evento_ferias_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_ferias_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_ferias                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_ferias_calculado.cod_evento                                                                
                          AND evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro                                                
                          AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                          AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND cod_tipo = 1                                                                                                        
                      GROUP BY registro_evento_ferias.cod_contrato  ) as recolhido_fgts                                                          
                   ON recolhido_fgts.cod_contrato = contrato.cod_contrato';
    END IF;

    --FOLHA DÉCIMO
    IF inCodConfiguracao = 3 THEN
        stSql := stSql || '
             LEFT JOIN (SELECT sum(evento_decimo_calculado.valor) as valor                                                      
                            , registro_evento_decimo.cod_contrato                                                                                                         
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                         
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia                                                    
                           ON previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia              
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                   INNER JOIN (  SELECT cod_previdencia                                                                 
                                      , max(timestamp) as timestamp                                                     
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                          
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                 
                           ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia     
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia                                                                
                           ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                   INNER JOIN folhapagamento'||stEntidade||'.evento_decimo_calculado                                                           
                           ON previdencia_evento.cod_evento = evento_decimo_calculado.cod_evento
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_decimo                                                    
                           ON evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro                      
                          AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                          AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                        WHERE cod_tipo = 2                                                                              
                          AND tipo_previdencia = ''o''                                                                    
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                      
                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                      GROUP BY registro_evento_decimo.cod_contrato                                                                                                        
                      ORDER BY registro_evento_decimo.cod_contrato) AS base_previdencia                                
                   ON base_previdencia.cod_contrato = contrato.cod_contrato         
            LEFT JOIN (SELECT sum(evento_decimo_calculado.valor) as valor                                                                                              
                            , registro_evento_decimo.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                                                   
                            , folhapagamento'||stEntidade||'.previdencia_previdencia                                                                              
                            , (  SELECT cod_previdencia                                                                                           
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                                                    
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                                           
                            , folhapagamento'||stEntidade||'.previdencia                                                                                          
                            , folhapagamento'||stEntidade||'.evento_decimo_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_decimo                                                                              
                        WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                        
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp                                              
                          AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                               
                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                               
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp                                     
                          AND previdencia_evento.cod_evento = evento_decimo_calculado.cod_evento                                                         
                          AND evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro                                                
                          AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                          AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          AND cod_tipo = 1                                                                                                        
                          AND tipo_previdencia = ''o''                                                                                              
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                                                                     
                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                      GROUP BY registro_evento_decimo.cod_contrato                                                                               
                       ORDER BY registro_evento_decimo.cod_contrato) AS desconto_previdencia                                                     
                    ON desconto_previdencia.cod_contrato = contrato.cod_contrato                                                         
            LEFT JOIN (SELECT sum(evento_decimo_calculado.valor) as valor                                                       
                            , registro_evento_decimo.cod_contrato                                                                  
                         FROM pessoal'||stEntidade||'.assentamento_assentamento                                                                     
                            , pessoal'||stEntidade||'.assentamento                                                                                  
                            , (  SELECT cod_assentamento                                                                            
                                      , max(timestamp) as timestamp                                                                 
                                   FROM pessoal'||stEntidade||'.assentamento                                                                        
                               GROUP BY cod_assentamento) as max_assentamento                                                       
                            , pessoal'||stEntidade||'.assentamento_evento                                                                           
                            , folhapagamento'||stEntidade||'.registro_evento_decimo                                                                
                            , folhapagamento'||stEntidade||'.evento_decimo_calculado                                                                       
                        WHERE assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento                            
                          AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                     
                          AND assentamento.timestamp = max_assentamento.timestamp                                                   
                          AND assentamento.cod_assentamento = assentamento_evento.cod_assentamento                                  
                          AND assentamento.timestamp = assentamento_evento.timestamp                                                
                          AND assentamento_assentamento.cod_motivo = 7                                                              
                          AND assentamento_evento.cod_evento = evento_decimo_calculado.cod_evento                                          
                          AND evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro                                  
                          AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                          AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'             
                      GROUP BY registro_evento_decimo.cod_contrato                                                      
                       ORDER BY registro_evento_decimo.cod_contrato) AS maternidade                                     
                    ON maternidade.cod_contrato = contrato.cod_contrato                                                  
            LEFT JOIN (SELECT evento_decimo_calculado.cod_evento                                                                                         
                            , evento_decimo_calculado.valor                                                                                              
                            , registro_evento_decimo.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                               
                            , (SELECT cod_regime_previdencia                                                                                      
                                    , max(timestamp) as timestamp                                                                                 
                                 FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                       
                               GROUP BY cod_regime_previdencia) as max_salario_familia_evento                                                     
                            , folhapagamento'||stEntidade||'.evento_decimo_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_decimo                                                                              
                        WHERE salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia                   
                          AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp                                             
                          AND salario_familia_evento.cod_evento = evento_decimo_calculado.cod_evento                                                     
                          AND evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro                                                
                          AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                          AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'   
                          AND salario_familia_evento.cod_tipo = 1 
                          AND salario_familia_evento.cod_regime_previdencia = '||inCodRegimePrevidencia||') AS salario_familia                                               
                   ON salario_familia.cod_contrato = contrato.cod_contrato                                                                        
            LEFT JOIN (SELECT registro_evento_decimo.cod_contrato                                                                                
                            , sum(evento_decimo_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_decimo_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_decimo                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_decimo_calculado.cod_evento                                                                
                          AND evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro                                                
                          AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                          AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND cod_tipo = 3                                                                                                        
                      GROUP BY registro_evento_decimo.cod_contrato  ) as base_fgts                                                               
                   ON base_fgts.cod_contrato = contrato.cod_contrato                                                                              
            LEFT JOIN (SELECT registro_evento_decimo.cod_contrato                                                                                
                            , SUM(evento_decimo_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_decimo_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_decimo                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_decimo_calculado.cod_evento                                                                
                          AND evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro                                                
                          AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                          AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND cod_tipo = 1                                                                                                        
                      GROUP BY registro_evento_decimo.cod_contrato  ) as recolhido_fgts                                                          
                   ON recolhido_fgts.cod_contrato = contrato.cod_contrato';
    END IF;

    --FOLHA RESCISÃO
    IF inCodConfiguracao = 4 THEN
        stSql := stSql || '
            LEFT JOIN (SELECT sum(evento_rescisao_calculado.valor) as valor                                                      
                            , registro_evento_rescisao.cod_contrato                                                                                                           
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                         
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia                                                    
                           ON previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                   INNER JOIN (  SELECT cod_previdencia                                                                 
                                      , max(timestamp) as timestamp                                                     
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                          
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                 
                           ON  previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                   INNER JOIN folhapagamento'||stEntidade||'.previdencia                                                                
                           ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                   INNER JOIN folhapagamento'||stEntidade||'.evento_rescisao_calculado                                                           
                           ON previdencia_evento.cod_evento = evento_rescisao_calculado.cod_evento
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao                                                    
                           ON evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro
                          AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                          AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                          AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                         WHERE cod_tipo = 2                                                                              
                          AND tipo_previdencia = ''o''                                                                    
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                      
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                      GROUP BY registro_evento_rescisao.cod_contrato                                                                                                           
                      ORDER BY registro_evento_rescisao.cod_contrato) AS base_previdencia                                
                   ON base_previdencia.cod_contrato = contrato.cod_contrato         
            LEFT JOIN (SELECT sum(evento_rescisao_calculado.valor) as valor                                                                                              
                            , registro_evento_rescisao.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.previdencia_evento                                                                                   
                            , folhapagamento'||stEntidade||'.previdencia_previdencia                                                                              
                            , (  SELECT cod_previdencia                                                                                           
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.previdencia_previdencia                                                                    
                               GROUP BY cod_previdencia) as max_previdencia_previdencia                                                           
                            , folhapagamento'||stEntidade||'.previdencia                                                                                          
                            , folhapagamento'||stEntidade||'.evento_rescisao_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_rescisao                                                                              
                        WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia                                        
                          AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp                                              
                          AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia                                               
                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia                               
                          AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp                                     
                          AND previdencia_evento.cod_evento = evento_rescisao_calculado.cod_evento                                                         
                          AND evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro                                                
                          AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                          AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                          AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          AND cod_tipo = 1                                                                                                        
                          AND tipo_previdencia = ''o''                                                                                              
                          AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'                                                                     
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                      GROUP BY registro_evento_rescisao.cod_contrato                                                                               
                       ORDER BY registro_evento_rescisao.cod_contrato) AS desconto_previdencia                                                     
                    ON desconto_previdencia.cod_contrato = contrato.cod_contrato                                                         
            LEFT JOIN (SELECT sum(evento_rescisao_calculado.valor) as valor                                                       
                            , registro_evento_rescisao.cod_contrato                                                                  
                         FROM pessoal'||stEntidade||'.assentamento_assentamento                                                                     
                            , pessoal'||stEntidade||'.assentamento                                                                                  
                            , (  SELECT cod_assentamento                                                                            
                                      , max(timestamp) as timestamp                                                                 
                                   FROM pessoal'||stEntidade||'.assentamento                                                                        
                               GROUP BY cod_assentamento) as max_assentamento                                                       
                            , pessoal'||stEntidade||'.assentamento_evento                                                                           
                            , folhapagamento'||stEntidade||'.registro_evento_rescisao                                                                
                            , folhapagamento'||stEntidade||'.evento_rescisao_calculado                                                                       
                        WHERE assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento                            
                          AND assentamento.cod_assentamento = max_assentamento.cod_assentamento                                     
                          AND assentamento.timestamp = max_assentamento.timestamp                                                   
                          AND assentamento.cod_assentamento = assentamento_evento.cod_assentamento                                  
                          AND assentamento.timestamp = assentamento_evento.timestamp                                                
                          AND assentamento_assentamento.cod_motivo = 7                                                              
                          AND assentamento_evento.cod_evento = evento_rescisao_calculado.cod_evento                                          
                          AND evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro                                  
                          AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                          AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                          AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'             
                      GROUP BY registro_evento_rescisao.cod_contrato                                                      
                       ORDER BY registro_evento_rescisao.cod_contrato) AS maternidade                                     
                    ON maternidade.cod_contrato = contrato.cod_contrato                                                  
            LEFT JOIN (SELECT evento_rescisao_calculado.cod_evento                                                                                         
                            , evento_rescisao_calculado.valor                                                                                              
                            , registro_evento_rescisao.cod_contrato                                                                                
                         FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                               
                            , (SELECT cod_regime_previdencia                                                                                      
                                    , max(timestamp) as timestamp                                                                                 
                                 FROM folhapagamento'||stEntidade||'.salario_familia_evento                                                                       
                               GROUP BY cod_regime_previdencia) as max_salario_familia_evento                                                     
                            , folhapagamento'||stEntidade||'.evento_rescisao_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_rescisao                                                                              
                        WHERE salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia                   
                          AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp                                             
                          AND salario_familia_evento.cod_evento = evento_rescisao_calculado.cod_evento                                                     
                          AND evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro                                                
                          AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                          AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                          AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'   
                          AND salario_familia_evento.cod_tipo = 1 
                          AND salario_familia_evento.cod_regime_previdencia = '||inCodRegimePrevidencia||') AS salario_familia                                               
                   ON salario_familia.cod_contrato = contrato.cod_contrato                                                                        
            LEFT JOIN (SELECT registro_evento_rescisao.cod_contrato                                                                                
                            , sum(evento_rescisao_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_rescisao_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_rescisao                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_rescisao_calculado.cod_evento                                                                
                          AND evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro                                                
                          AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                          AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                          AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND cod_tipo = 3                                                                                                        
                      GROUP BY registro_evento_rescisao.cod_contrato  ) as base_fgts                                                               
                   ON base_fgts.cod_contrato = contrato.cod_contrato                                                                              
            LEFT JOIN (SELECT registro_evento_rescisao.cod_contrato                                                                                
                            , SUM(evento_rescisao_calculado.valor) as valor                                                                                              
                         FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                          
                            , (  SELECT cod_fgts                                                                                                  
                                      , max(timestamp) as timestamp                                                                               
                                   FROM folhapagamento'||stEntidade||'.fgts_evento                                                                                
                               GROUP BY cod_fgts) as max_fgts_evento                                                                              
                            , folhapagamento'||stEntidade||'.evento                                                                                               
                            , folhapagamento'||stEntidade||'.evento_rescisao_calculado                                                                                     
                            , folhapagamento'||stEntidade||'.registro_evento_rescisao                                                                              
                        WHERE fgts_evento.cod_evento = evento.cod_evento                                                                          
                          AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts                                                                   
                          AND fgts_evento.timestamp  = max_fgts_evento.timestamp                                                                  
                          AND fgts_evento.cod_evento = evento_rescisao_calculado.cod_evento                                                                
                          AND evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro                                                
                          AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                          AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                          AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                           
                          AND cod_tipo = 1                                                                                                        
                      GROUP BY registro_evento_rescisao.cod_contrato  ) as recolhido_fgts                                                          
                   ON recolhido_fgts.cod_contrato = contrato.cod_contrato';
    END IF;

    IF stTipoFiltro = 'local_grupo' AND stSituacao != 'E' THEN    
        stSql := stSql ||'            
                    INNER JOIN pessoal'||stEntidade||'.contrato_servidor_local                       
                            ON contrato_servidor_local.cod_contrato = contrato.cod_contrato
                    INNER JOIN (SELECT cod_contrato                                   
                                     , max(timestamp) as timestamp                    
                                  FROM pessoal'||stEntidade||'.contrato_servidor_local                
                                GROUP BY cod_contrato) as max_contrato_servidor_local 
                            ON max_contrato_servidor_local.cod_contrato = contrato_servidor_local.cod_contrato
                           AND max_contrato_servidor_local.timestamp = contrato_servidor_local.timestamp
                    INNER JOIN organograma.local
                            ON local.cod_local = contrato_servidor_local.cod_local
                           AND local.cod_local IN ('||stCodigos||')';
    END IF;
    IF stTipoFiltro = 'sub_divisao_grupo' AND stSituacao != 'E'  THEN
        stSql := stSql ||'            
                    INNER JOIN pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao                       
                            ON contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato
                    INNER JOIN (SELECT cod_contrato                                   
                                     , max(timestamp) as timestamp                    
                                  FROM pessoal'||stEntidade||'.contrato_servidor_sub_divisao_funcao                
                                GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao 
                            ON max_contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                           AND max_contrato_servidor_sub_divisao_funcao.timestamp = contrato_servidor_sub_divisao_funcao.timestamp
                    INNER JOIN pessoal'||stEntidade||'.sub_divisao
                            ON sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                           AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('||stCodigos||')';
    END IF;
    IF stTipoFiltro = 'atributo_servidor_grupo' THEN
        stSql := stSql ||'
                    INNER JOIN pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                            ON atributo_contrato_servidor_valor.cod_contrato = contrato.cod_contrato
                           AND atributo_contrato_servidor_valor.cod_atributo = '||inCodAtributo;
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql ||' AND trim(atributo_contrato_servidor_valor.valor) IN ('||stCodigos||')
                    INNER JOIN administracao.atributo_valor_padrao
                            ON atributo_valor_padrao.cod_atributo = atributo_contrato_servidor_valor.cod_atributo
                           AND atributo_valor_padrao.cod_cadastro = atributo_contrato_servidor_valor.cod_cadastro
                           AND atributo_valor_padrao.cod_modulo = atributo_contrato_servidor_valor.cod_modulo
                           AND atributo_valor_padrao.cod_valor = trim(atributo_contrato_servidor_valor.valor)';
        ELSE
            stSql := stSql ||' AND atributo_contrato_servidor_valor.valor = '|| quote_loteral(stCodigos) ||' ';
        END IF;
        stSql := stSql ||'
                    INNER JOIN (  SELECT cod_contrato                                                                                                        
                                       , max(timestamp) as timestamp                                                                                         
                                    FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor                                                                                     
                                GROUP BY cod_contrato) as max_atributo_contrato_servidor_valor    
                            ON max_atributo_contrato_servidor_valor.cod_contrato = atributo_contrato_servidor_valor.cod_contrato
                           AND max_atributo_contrato_servidor_valor.timestamp = atributo_contrato_servidor_valor.timestamp';
    END IF;
    IF stTipoFiltro = 'atributo_pensionista_grupo' THEN
        stSql := stSql ||'
                    INNER JOIN pessoal'||stEntidade||'.atributo_contrato_pensionista
                            ON atributo_contrato_pensionista.cod_contrato = contrato.cod_contrato
                           AND atributo_contrato_pensionista.cod_atributo = '||inCodAtributo;
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql ||' AND trim(atributo_contrato_pensionista.valor) IN ('||stCodigos||')
                    INNER JOIN administracao.atributo_valor_padrao
                            ON atributo_valor_padrao.cod_atributo = atributo_contrato_pensionista.cod_atributo
                           AND atributo_valor_padrao.cod_cadastro = atributo_contrato_pensionista.cod_cadastro
                           AND atributo_valor_padrao.cod_modulo = atributo_contrato_pensionista.cod_modulo
                           AND atributo_valor_padrao.cod_valor = trim(atributo_contrato_pensionista.valor)';
        ELSE
            stSql := stSql ||' AND atributo_contrato_pensionista.valor = '|| quote_literal(stCodigos) ||' ';
        END IF;
        stSql := stSql ||'
                    INNER JOIN (  SELECT cod_contrato                                                                                                        
                                       , max(timestamp) as timestamp                                                                                         
                                    FROM pessoal'||stEntidade||'.atributo_contrato_pensionista                                                                                     
                                GROUP BY cod_contrato) as max_atributo_contrato_pensionista    
                            ON max_atributo_contrato_pensionista.cod_contrato = atributo_contrato_pensionista.cod_contrato
                           AND max_atributo_contrato_pensionista.timestamp = atributo_contrato_pensionista.timestamp';        
    END IF;
    IF stTipoFiltro = 'lotacao_grupo' THEN
        IF stSituacao = 'E' THEN
            stSql := stSql ||'         
                    INNER JOIN ultimo_contrato_pensionista_orgao('|| quote_literal(stEntidade) ||','||inCodPeriodoMovimentacao||') as contrato_pensionista_orgao
                            ON contrato_pensionista_orgao.cod_contrato = contrato.cod_contrato
                    INNER JOIN organograma.vw_orgao_nivel
                            ON vw_orgao_nivel.cod_orgao = contrato_pensionista_orgao.cod_orgao';
        ELSE
            stSql := stSql ||'            
                    INNER JOIN ultimo_contrato_servidor_orgao('|| quote_literal(stEntidade) ||','||inCodPeriodoMovimentacao||') as contrato_servidor_orgao
                            ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato
                    INNER JOIN organograma.vw_orgao_nivel
                            ON vw_orgao_nivel.cod_orgao = contrato_servidor_orgao.cod_orgao';
        END IF;
        stSql := stSql ||' AND vw_orgao_nivel.cod_orgao IN ('||stCodigos||')';
    END IF;
    stSql := stSql ||' WHERE recuperarSituacaoDoContrato(contrato.cod_contrato,'||inCodPeriodoMovimentacao||','|| quote_literal(stEntidade) ||') = '|| quote_literal(stSituacao) ||' ';
    IF inCodConfiguracao = 0 THEN
        stSql := stSql ||'
       AND EXISTS (    SELECT 1
                         FROM folhapagamento'||stEntidade||'.evento_complementar_calculado                                             
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                           ON evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro        
                          AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                          AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                          AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                                              
                          AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'        
                          AND registro_evento_complementar.cod_contrato = contrato.cod_contrato)';   
    END IF;
    IF inCodConfiguracao = 1 THEN
        stSql := stSql ||'
       AND contrato.cod_contrato IN (   SELECT registro_evento_periodo.cod_contrato                                        
                                          FROM folhapagamento'||stEntidade||'.evento_calculado                                             
                                             , folhapagamento'||stEntidade||'.registro_evento_periodo                                      
                                         WHERE evento_calculado.cod_registro = registro_evento_periodo.cod_registro        
                                           AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                                              
                                      GROUP BY cod_contrato)';        
    END IF;
    IF inCodConfiguracao = 2 THEN
        stSql := stSql ||'
       AND EXISTS (    SELECT 1
                         FROM folhapagamento'||stEntidade||'.evento_ferias_calculado                                             
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_ferias
                           ON evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro        
                          AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                          AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                          AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                                              
                          AND registro_evento_ferias.cod_contrato = contrato.cod_contrato)';        
    END IF;
    IF inCodConfiguracao = 3 THEN
        stSql := stSql ||'
       AND EXISTS (    SELECT 1
                         FROM folhapagamento'||stEntidade||'.evento_decimo_calculado                                             
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_decimo
                           ON evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro        
                          AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                          AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                          AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                                              
                          AND registro_evento_decimo.cod_contrato = contrato.cod_contrato)';       
    END IF;
    IF inCodConfiguracao = 4 THEN
        stSql := stSql ||'
       AND EXISTS (    SELECT 1
                         FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado                                             
                   INNER JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao
                           ON evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro        
                          AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                          AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                          AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'                                              
                          AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato)';        
    END IF;

    stSql := stSql ||' AND EXISTS (    SELECT 1
                                         FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                   INNER JOIN (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                               GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                           ON max_contrato_servidor_previdencia.cod_contrato = contrato_servidor_previdencia.cod_contrato
                                          AND max_contrato_servidor_previdencia.timestamp = contrato_servidor_previdencia.timestamp
                                        WHERE contrato_servidor_previdencia.cod_contrato = contrato.cod_contrato
                                          AND contrato_servidor_previdencia.bo_excluido IS FALSE
                                          AND contrato_servidor_previdencia.cod_previdencia = '||inCodPrevidencia||')
                         AND (base_previdencia.cod_contrato IS NOT NULL OR desconto_previdencia.cod_contrato IS NOT NULL OR maternidade.cod_contrato IS NOT NULL OR salario_familia.cod_contrato IS NOT NULL OR base_fgts.cod_contrato IS NOT NULL OR recolhido_fgts.cod_contrato IS NOT NULL)                                  
                       GROUP BY agrupamento';

    IF stTipoFiltro = 'lotacao_grupo' THEN
        IF stOrdenacao = 'A' THEN
            stSql := stSql ||' , vw_orgao_nivel.cod_orgao ORDER BY recuperaDescricaoOrgao(vw_orgao_nivel.cod_orgao,'|| quote_literal(inExercicio||'-01-01') ||'::date) ';
        ELSE
            stSql := stSql ||' , vw_orgao_nivel.orgao ORDER BY vw_orgao_nivel.orgao ';
        END IF;
    END IF;
    IF stTipoFiltro = 'local_grupo' THEN
        IF stOrdenacao = 'A' THEN
            stSql := stSql ||' , local.descricao ORDER BY local.descricao ';
        ELSE
            stSql := stSql ||' , local.cod_local ORDER BY local.cod_local ';
        END IF;
    END IF;
    IF stTipoFiltro = 'sub_divisao_grupo' THEN
        IF stOrdenacao = 'A' THEN
            stSql := stSql ||' , sub_divisao.descricao ORDER BY sub_divisao.descricao ';
        ELSE
            stSql := stSql ||' , sub_divisao.cod_sub_divisao ORDER BY sub_divisao.cod_sub_divisao ';
        END IF;
    END IF;
    IF stTipoFiltro = 'atributo_servidor_grupo' THEN
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql ||' , atributo_valor_padrao.valor_padrao ORDER BY atributo_valor_padrao.valor_padrao ';
        ELSE
            stSql := stSql ||' , atributo_contrato_servidor_valor.valor ORDER BY atributo_contrato_servidor_valor.valor ';
        END IF;
    END IF;
    IF stTipoFiltro = 'atributo_pensionista_grupo' THEN
        IF inCodTipoAtributo = 3 OR inCodTipoAtributo = 4 THEN
            stSql := stSql ||' , atributo_valor_padrao.valor_padrao ORDER BY atributo_valor_padrao.valor_padrao ';
        ELSE
            stSql := stSql ||' , atributo_contrato_pensionista.valor ORDER BY atributo_contrato_pensionista.valor ';
        END IF;
    END IF;

    
    FOR reRegistro IN  EXECUTE stSql
    LOOP

        rwObrigacoesSociais.agrupamento             := reRegistro.agrupamento;
        rwObrigacoesSociais.contador                := reRegistro.contador;
        rwObrigacoesSociais.base_previdencia        := reRegistro.base_previdencia;
        rwObrigacoesSociais.vlr_patronal            := reRegistro.vlr_patronal;
        rwObrigacoesSociais.desconto_previdencia    := reRegistro.desconto_previdencia;
        rwObrigacoesSociais.maternidade             := reRegistro.maternidade;
        rwObrigacoesSociais.salario_familia         := reRegistro.salario_familia;
        rwObrigacoesSociais.base_fgts               := reRegistro.base_fgts;
        rwObrigacoesSociais.recolhido_fgts          := reRegistro.recolhido_fgts;
        RETURN NEXT rwObrigacoesSociais; 
    END LOOP;    
    RETURN;
END;
$$LANGUAGE 'plpgsql';

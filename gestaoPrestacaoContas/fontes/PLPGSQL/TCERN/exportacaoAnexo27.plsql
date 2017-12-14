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
* $Revision:$
* $Name$
* $Author:$
* $Date:$
*
* $Id: exportacaoAnexo27.plsql 60030 2014-09-25 19:20:35Z michel $
*
* Casos de uso:
*/

/*
$Log$
*/

CREATE OR REPLACE FUNCTION tcern.fn_exportacao_anexo27(varchar,integer,varchar,varchar) RETURNS SETOF
RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1   ;
    inCodMovimentacao   ALIAS FOR $2   ;
    stEntidade          ALIAS FOR $3   ;
    stEventos           ALIAS FOR $4   ;
    stSql               VARCHAR   := '';
    stEventosTratado    VARCHAR   := '';
    reRegistro          RECORD         ;
BEGIN

    stEventos := REPLACE(stEventos, ',', ''',''');

    stSql := '
               SELECT cod_contrato as cod_contrato
                    , acumulador as acumulador
                    , sum(valor) as valor
                 FROM ( -- folha salario
                       SELECT registro_evento_periodo.cod_contrato
                            , evento.codigo               
                            , evento.descricao 
                            , evento.natureza 
                            , evento_calculado.valor 
                            , CASE WHEN evento.codigo IN ('''|| stEventos ||''')
                                           THEN ''VencimentoBase''
                              WHEN evento.codigo NOT IN ('''|| stEventos ||''') AND evento.natureza=''P''
                                           THEN ''TotalOutrasVantagens''                                                  
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo                                                                                                             
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento 
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia
                                                                                                              , max(previdencia_previdencia.timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE previdencia_previdencia.timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp 							
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE previdencia_evento.timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                              )
                                           THEN ''INSS''
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo                                                                                                                           
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                              )
                                           THEN ''IRRF''
                              WHEN evento.natureza=''D'' AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo                                                                                                                          
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                                  )
                                                         AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo                                                                                                            
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp 							
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                                  )
                                           THEN ''TotalOutrosDescontos''                                                                                                          
                              END as acumulador    
                         FROM folhapagamento'||(stEntidade)||'.registro_evento_periodo
                            , folhapagamento'||(stEntidade)||'.evento_calculado
                            , folhapagamento'||(stEntidade)||'.evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo_evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo
                        WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro
                          AND evento_calculado.cod_evento                      = evento.cod_evento
                          AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
                          AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
                          AND evento.natureza NOT IN (''B'',''I'')
                          AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodMovimentacao ||'
             
                        UNION -- folha ferias
                       SELECT registro_evento_ferias.cod_contrato
                            , evento.codigo
                            , evento.descricao
                            , evento.natureza
                            , evento_ferias_calculado.valor
                            , CASE WHEN evento.codigo IN ('''|| stEventos ||''')
                                           THEN ''VencimentoBase''
                              WHEN evento.codigo NOT IN ('''|| stEventos ||''') AND evento.natureza=''P''
                                           THEN ''TotalOutrasVantagens''                                                  
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo                                                                                                             
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp 							
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                              )
                                           THEN ''INSS''
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                              )
                                           THEN ''IRRF''
                              WHEN evento.natureza=''D'' AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                                  )
                                                         AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo                                                                                                            
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp 							
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                                  )
                                           THEN ''TotalOutrosDescontos''
                              END as acumulador   					 
                         FROM folhapagamento'||(stEntidade)||'.registro_evento_ferias
                            , folhapagamento'||(stEntidade)||'.evento_ferias_calculado
                            , folhapagamento'||(stEntidade)||'.evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo_evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo
                        WHERE registro_evento_ferias.cod_registro             = evento_ferias_calculado.cod_registro
                          AND registro_evento_ferias.desdobramento            = evento_ferias_calculado.desdobramento
                          AND registro_evento_ferias.timestamp                = evento_ferias_calculado.timestamp_registro
                          AND registro_evento_ferias.cod_evento               = evento_ferias_calculado.cod_evento
                          AND evento_ferias_calculado.cod_evento              = evento.cod_evento
                          AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
                          AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
                          AND evento.natureza NOT IN (''B'',''I'')
                          AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodMovimentacao ||'
             
                        UNION -- folha decimo terceiro
                       SELECT registro_evento_decimo.cod_contrato
                            , evento.codigo
                            , evento.descricao
                            , evento.natureza
                            , evento_decimo_calculado.valor
                            , CASE WHEN evento.codigo IN ('''|| stEventos ||''')
                                           THEN ''VencimentoBase''
                              WHEN evento.codigo NOT IN ('''|| stEventos ||''') AND evento.natureza=''P''
                                           THEN ''TotalOutrasVantagens''                                                  
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo                                                                                                             
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp 							
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                              )
                                           THEN ''INSS''
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                              AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                              )
                                           THEN ''IRRF''
                              WHEN evento.natureza=''D'' AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                                  )
                                                         AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo                                                                                                            
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp 							
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                                  )
                                           THEN ''TotalOutrosDescontos''
                              END as acumulador   
             
                         FROM folhapagamento'||(stEntidade)||'.registro_evento_decimo
                            , folhapagamento'||(stEntidade)||'.evento_decimo_calculado
                            , folhapagamento'||(stEntidade)||'.evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo_evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo
                        WHERE registro_evento_decimo.cod_registro             = evento_decimo_calculado.cod_registro
                          AND registro_evento_decimo.cod_evento               = evento_decimo_calculado.cod_evento
                          AND registro_evento_decimo.desdobramento            = evento_decimo_calculado.desdobramento
                          AND registro_evento_decimo.timestamp                = evento_decimo_calculado.timestamp_registro
                          AND evento_decimo_calculado.cod_evento              = evento.cod_evento
                          AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
                          AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
                          AND evento.natureza NOT IN (''B'',''I'')
                          AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodMovimentacao ||'
             
                        UNION -- folha recisao
                       SELECT registro_evento_rescisao.cod_contrato
                            , evento.codigo
                            , evento.descricao
                            , evento.natureza   
                            , evento_rescisao_calculado.valor
                            , CASE WHEN evento.codigo IN ('''|| stEventos ||''')
                                           THEN ''VencimentoBase''
                              WHEN evento.codigo NOT IN ('''|| stEventos ||''') AND evento.natureza=''P''
                                           THEN ''TotalOutrasVantagens''                                                  
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo                                                                                                             
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                              )
                                           THEN ''INSS''
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                              )
                                           THEN ''IRRF''
                              WHEN evento.natureza=''D'' AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                                  )
                                                         AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo                                                                                                            
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                                  )
                                           THEN ''TotalOutrosDescontos''
                              END as acumulador   
                         FROM folhapagamento'||(stEntidade)||'.registro_evento_rescisao
                            , folhapagamento'||(stEntidade)||'.evento_rescisao_calculado
                            , folhapagamento'||(stEntidade)||'.evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo_evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo
                        WHERE registro_evento_rescisao.cod_registro             = evento_rescisao_calculado.cod_registro
                          AND registro_evento_rescisao.cod_evento               = evento_rescisao_calculado.cod_evento
                          AND registro_evento_rescisao.desdobramento            = evento_rescisao_calculado.desdobramento
                          AND registro_evento_rescisao.timestamp                = evento_rescisao_calculado.timestamp_registro
                          AND evento_rescisao_calculado.cod_evento              = evento.cod_evento
                          AND evento.cod_evento                                 = sequencia_calculo_evento.cod_evento
                          AND sequencia_calculo_evento.cod_sequencia            = sequencia_calculo.cod_sequencia
                          AND evento.natureza NOT IN (''B'',''I'')
                          AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodMovimentacao ||'
                        
                        UNION -- folha complementar
                       SELECT registro_evento_complementar.cod_contrato
                            , evento.codigo
                            , evento.descricao
                            , evento.natureza
                            , evento_complementar_calculado.valor
                            , CASE WHEN evento.codigo IN ('''|| stEventos ||''')
                                           THEN ''VencimentoBase''
                              WHEN evento.codigo NOT IN ('''|| stEventos ||''') AND evento.natureza=''P''
                                           THEN ''TotalOutrasVantagens''                                                  
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo                                                                                                             
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                              )
                                           THEN ''INSS''
                              WHEN evento.natureza=''D'' AND evento.codigo IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                              )
                                           THEN ''IRRF''
                              WHEN evento.natureza=''D'' AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo 
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.tabela_irrf_evento
                                                                                            INNER JOIN ( SELECT cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.tabela_irrf_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY cod_tipo ) as max_tabela_irrf_evento
                                                                                                    ON max_tabela_irrf_evento.cod_tipo   = tabela_irrf_evento.cod_tipo
                                                                                                   AND max_tabela_irrf_evento.timestamp  = tabela_irrf_evento.timestamp      
                                                                                 WHERE tabela_irrf_evento.cod_evento = evento.cod_evento
                                                                                   AND tabela_irrf_evento.cod_tipo IN (3,6)
                                                                                  )
                                                         AND evento.codigo NOT IN (
                                                                                SELECT evento.codigo                                                                                                            
                                                                                  FROM folhapagamento'||(stEntidade)||'.evento
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_previdencia
                                                                                            INNER JOIN ( SELECT previdencia_previdencia.cod_previdencia 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_previdencia             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                            AND previdencia_previdencia.tipo_previdencia = ''o''
                                                                                                       GROUP BY cod_previdencia ) as max_previdencia_previdencia
                                                                                                    ON max_previdencia_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                                                                             
                                                                                                   AND max_previdencia_previdencia.timestamp = previdencia_previdencia.timestamp
                                                                                     , folhapagamento'||(stEntidade)||'.previdencia_evento
                                                                                            INNER JOIN ( SELECT previdencia_evento.cod_previdencia 
                                                                                                              , previdencia_evento.cod_tipo 
                                                                                                              , max(timestamp) as timestamp
                                                                                                           FROM folhapagamento'||(stEntidade)||'.previdencia_evento             
                                                                                                          WHERE timestamp <= (select ultimotimestampperiodomovimentacao('|| inCodMovimentacao ||','|| quote_literal(stEntidade) ||')::timestamp)
                                                                                                       GROUP BY previdencia_evento.cod_previdencia
                                                                                                              , previdencia_evento.cod_tipo ) as max_previdencia_evento 
                                                                                                    ON max_previdencia_evento.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                                   AND max_previdencia_evento.cod_tipo = previdencia_evento.cod_tipo                                
                                                                                                   AND max_previdencia_evento.timestamp = previdencia_evento.timestamp 
                                                                                 WHERE previdencia_evento.cod_evento = evento.cod_evento
                                                                                   AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                                                                                   AND previdencia_evento.cod_tipo=1
                                                                                  )
                                           THEN ''TotalOutrosDescontos''
                              END as acumulador   
                         FROM folhapagamento'||(stEntidade)||'.registro_evento_complementar
                            , folhapagamento'||(stEntidade)||'.evento_complementar_calculado
                            , folhapagamento'||(stEntidade)||'.evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo_evento
                            , folhapagamento'||(stEntidade)||'.sequencia_calculo
                        WHERE registro_evento_complementar.cod_registro             = evento_complementar_calculado.cod_registro
                          AND registro_evento_complementar.cod_evento               = evento_complementar_calculado.cod_evento
                          AND registro_evento_complementar.cod_configuracao         = evento_complementar_calculado.cod_configuracao
                          AND registro_evento_complementar.timestamp                = evento_complementar_calculado.timestamp_registro
                          AND evento_complementar_calculado.cod_evento              = evento.cod_evento
                          AND evento.cod_evento                                     = sequencia_calculo_evento.cod_evento
                          AND sequencia_calculo_evento.cod_sequencia                = sequencia_calculo.cod_sequencia
                          AND evento.natureza NOT IN (''B'',''I'')
                          AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodMovimentacao ||'

                      ) AS folhas_totais
             GROUP BY folhas_totais.cod_contrato , folhas_totais.acumulador
             ORDER BY folhas_totais.cod_contrato , folhas_totais.acumulador
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';


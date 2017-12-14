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
 * recuperaContribuicaoPrevidenciaria
 * Data de Criação   : 21/11/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 * @ignore # 

 $Id:$
 */

CREATE OR REPLACE FUNCTION recuperaContribuicaoPrevidenciaria(VARCHAR, INTEGER, INTEGER, INTEGER, VARCHAR, VARCHAR, VARCHAR, VARCHAR, INTEGER) RETURNS SETOF colunaContribuicaoPrevidenciaria AS $$
DECLARE
    stEntidade                    ALIAS FOR $1;
    inCodPeriodoMovimentacao      ALIAS FOR $2;
    inCodPrevidencia              ALIAS FOR $3;
    inCodConfiguracao             ALIAS FOR $4;
    stSituacaoCadastro            ALIAS FOR $5;
    stTipoFiltro                  ALIAS FOR $6;
    stCodigos                     ALIAS FOR $7;
    stAcumularSalCompl            ALIAS FOR $8;
    inCodComplementar             ALIAS FOR $9;
    stSQL                         VARCHAR;
    stSQLPrevidenciaServidor      VARCHAR;
    stSQLPrevidenciaPensionista   VARCHAR;
    stCodEventos                  VARCHAR;
    stExercicio                   VARCHAR;  
    reRegistro                    RECORD;
    reEventoCalculado             RECORD;
    reDados                       RECORD;
    inCodEventoMaternidade        INTEGER;
    inCodEventoDescPrevidencia    INTEGER;
    inCodEventoBasePrevidencia    INTEGER;
    inCodEventoSalFamilia         INTEGER;  
    inCodEvento                   INTEGER;
    nuMaternidade                 NUMERIC;
    nuDescontoPrevidencia         NUMERIC;
    nuBasePrevidencia             NUMERIC;
    nuSalarioFamilia              NUMERIC;
    crDados                       REFCURSOR;        
    boRetorno                     BOOLEAN;
    stDataFinalCompetencia        VARCHAR;
    stConsultaCodComplementar     VARCHAR := '';
    rwContribuicaoPrevidenciaria  colunaContribuicaoPrevidenciaria%ROWTYPE;
BEGIN

    stSQL := 'SELECT dt_final
                FROM folhapagamento'||stEntidade||'.periodo_movimentacao
               WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
               
    stDataFinalCompetencia := selectintovarchar(stSQL);

    IF inCodComplementar <> 0 THEN
        stConsultaCodComplementar := ' AND registro_evento_complementar.cod_complementar = '||inCodComplementar;
    END IF;

    stSQL := '
              SELECT * 
                FROM (
                        --Busca dados do servidor--
                        SELECT contrato_servidor.cod_contrato
                              , ''servidor'' as cadastro
                              , contrato_servidor_orgao.cod_orgao
                              , contrato_servidor_local.cod_local
                              , contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao as cod_sub_divisao
                           FROM pessoal'||stEntidade||'.contrato_servidor                     
                     INNER JOIN ultimo_contrato_servidor_orgao('||quote_literal(stEntidade)||', '||inCodPeriodoMovimentacao||') as contrato_servidor_orgao
                             ON contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
                     INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('||quote_literal(stEntidade)||', '||inCodPeriodoMovimentacao||') as contrato_servidor_sub_divisao_funcao
                             ON contrato_servidor.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato
                      LEFT JOIN ultimo_contrato_servidor_local('||quote_literal(stEntidade)||', '||inCodPeriodoMovimentacao||') as contrato_servidor_local
                             ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato

                       UNION

                        -- Busca dados pensionista--
                        SELECT contrato_pensionista.cod_contrato
                             , ''pensionista'' as cadastro
                             , contrato_pensionista_orgao.cod_orgao
                             , contrato_servidor_local.cod_local
                             , contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao as cod_sub_divisao
                          FROM pessoal'||stEntidade||'.contrato_pensionista
                    INNER JOIN ultimo_contrato_pensionista_orgao('||quote_literal(stEntidade)||', '||inCodPeriodoMovimentacao||') as contrato_pensionista_orgao
                            ON contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('||quote_literal(stEntidade)||', '||inCodPeriodoMovimentacao||') as contrato_servidor_sub_divisao_funcao
                            ON contrato_pensionista.cod_contrato_cedente = contrato_servidor_sub_divisao_funcao.cod_contrato
                     LEFT JOIN ultimo_contrato_servidor_local('||quote_literal(stEntidade)||', '||inCodPeriodoMovimentacao||') as contrato_servidor_local
                            ON contrato_pensionista.cod_contrato_cedente = contrato_servidor_local.cod_contrato
                    ) as temp_table';

    -- Folha Complementar
    IF inCodConfiguracao = 0  THEN
        stSQL := stSQL || ' WHERE EXISTS (  SELECT 1
                                              FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                                        INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                                                ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                                               AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                                               AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                               AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                                             WHERE registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                               AND registro_evento_complementar.cod_contrato = temp_table.cod_contrato
                                               '||stConsultaCodComplementar||'
                                         )';
    END IF;

    -- Folha Salário
    IF inCodConfiguracao = 1  THEN
         stSQL := stSQL || ' WHERE EXISTS (   SELECT 1
                                                FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                                          INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                                                  ON evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                                               WHERE registro_evento_periodo.cod_contrato = temp_table.cod_contrato
                                                 AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                          )';
    END IF;

    -- Folha Férias
    IF inCodConfiguracao = 2  THEN
        stSQL := stSQL || ' WHERE EXISTS (  SELECT 1
                                              FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                                        INNER JOIN folhapagamento'||stEntidade||'.registro_evento_ferias
                                                ON registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                               AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                                               AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                               AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                                             WHERE registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                               AND registro_evento_ferias.cod_contrato = temp_table.cod_contrato
                                         )';
    END IF;

    -- Folha Décimo
    IF inCodConfiguracao = 3  THEN
        stSQL := stSQL || ' WHERE EXISTS (  SELECT 1
                                              FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                                        INNER JOIN folhapagamento'||stEntidade||'.registro_evento_decimo
                                                ON registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                               AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                                               AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                               AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                                             WHERE registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                               AND registro_evento_decimo.cod_contrato = temp_table.cod_contrato
                                         )';
    END IF;

    -- Folha Rescisão
    IF inCodConfiguracao = 4  THEN
        stSQL := stSQL || ' WHERE EXISTS (  SELECT 1
                                              FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                        INNER JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao
                                                ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                               AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                               AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                               AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                                             WHERE registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                               AND registro_evento_rescisao.cod_contrato = temp_table.cod_contrato
                                         )';
    END IF;

    IF stTipoFiltro = 'contrato_todos' OR  stTipoFiltro = 'cgm_contrato_todos' THEN
        stSQL := stSQL || ' AND cod_contrato IN ('||stCodigos||') ';
    END IF;

    IF stTipoFiltro = 'lotacao_grupo' THEN
        stSQL := stSQL || ' AND cod_orgao IN ('||stCodigos||') ';
    END IF;

    IF stTipoFiltro = 'local_grupo' THEN
        stSQL := stSQL || ' AND cod_local IN ('||stCodigos||') ';
    END IF;

    IF stTipoFiltro = 'sub_divisao_grupo' THEN
        stSQL := stSQL || ' AND cod_sub_divisao IN ('||stCodigos||') ';
    END IF;
             
    IF stSituacaoCadastro = 'ativo' THEN
        stSQL := stSQL || ' AND recuperarSituacaoDoContrato(temp_table.cod_contrato, '||inCodPeriodoMovimentacao||', '||quote_literal(stEntidade)||') = ''A''
                            AND cadastro = ''servidor''';

    END IF;

    IF stSituacaoCadastro = 'rescindido' THEN 
        stSQL := stSQL || ' AND recuperarSituacaoDoContrato(temp_table.cod_contrato, '||inCodPeriodoMovimentacao||', '||quote_literal(stEntidade)||') = ''R''
                            AND cadastro = ''servidor''';
    END IF;

    IF stSituacaoCadastro = 'inativo' THEN 
        stSQL := stSQL || ' AND recuperarSituacaoDoContrato(temp_table.cod_contrato, '||inCodPeriodoMovimentacao||', '||quote_literal(stEntidade)||') = ''P''
                            AND cadastro = ''servidor''';
    END IF;

    stSQLPrevidenciaServidor    := '';
    stSQLPrevidenciaPensionista := '';
    IF stSituacaoCadastro = 'pensionista' OR stSituacaoCadastro = 'todos' THEN 
        stSQLPrevidenciaPensionista := ' (cadastro = ''pensionista''
                                          AND EXISTS (    SELECT 1
                                                            FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                                                      INNER JOIN ( SELECT cod_contrato
                                                                        , max(timestamp) as timestamp 
                                                                     FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                                                                 GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                                              ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                                             AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp                            
                                                             AND contrato_pensionista_previdencia.cod_previdencia = '||inCodPrevidencia||'
                                                             AND contrato_pensionista_previdencia.cod_contrato = temp_table.cod_contrato
                                                      )
                                         )';
        IF stSituacaoCadastro = 'todos' THEN
            stSQLPrevidenciaPensionista := ' OR '||stSQLPrevidenciaPensionista;
        END IF;
    END IF;
    
    IF stSituacaoCadastro <> 'pensionista' OR stSituacaoCadastro = 'todos' THEN 
        stSQLPrevidenciaServidor := '    (cadastro <> ''pensionista''
                                          AND EXISTS (      SELECT 1
                                                              FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                                        INNER JOIN (   SELECT cod_contrato
                                                                            , max(timestamp) as timestamp 
                                                                         FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                                                     GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                                                ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                                               AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                            
                                                               AND contrato_servidor_previdencia.cod_previdencia = '||inCodPrevidencia||'
                                                               AND contrato_servidor_previdencia.cod_contrato = temp_table.cod_contrato
                                                               AND contrato_servidor_previdencia.bo_excluido IS FALSE
                                                     )
                                         )';
    END IF;
    
    stSQL := stSQL||' AND ('||stSQLPrevidenciaServidor||stSQLPrevidenciaPensionista||') ';

    FOR reRegistro IN EXECUTE stSQL LOOP
        nuMaternidade           := 0;
        nuDescontoPrevidencia   := 0;
        nuBasePrevidencia       := 0;
        nuSalarioFamilia        := 0;
        stCodEventos := '';
        
        --MATERNIDADE
        stSql := '    SELECT assentamento_evento.cod_evento
                        FROM pessoal'||stEntidade||'.assentamento
                  INNER JOIN (  SELECT cod_assentamento
                                     , max(timestamp) as timestamp
                                  FROM pessoal'||stEntidade||'.assentamento
                              GROUP BY cod_assentamento) as max_assentamento
                          ON assentamento.cod_assentamento = max_assentamento.cod_assentamento
                         AND assentamento.timestamp = max_assentamento.timestamp
                  INNER JOIN pessoal'||stEntidade||'.assentamento_assentamento
                          ON assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento
                  INNER JOIN pessoal'||stEntidade||'.assentamento_evento
                          ON assentamento_evento.cod_assentamento = assentamento.cod_assentamento
                         AND assentamento_evento.timestamp = assentamento.timestamp
                  INNER JOIN pessoal'||stEntidade||'.assentamento_gerado
                          ON assentamento_assentamento.cod_assentamento = assentamento_gerado.cod_assentamento
                  INNER JOIN (  SELECT cod_assentamento_gerado
                                     , max(timestamp) as timestamp
                                  FROM pessoal'||stEntidade||'.assentamento_gerado
                              GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
                          ON assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                         AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                  INNER JOIN pessoal'||stEntidade||'.assentamento_gerado_contrato_servidor
                          ON assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                       WHERE assentamento_assentamento.cod_motivo = 7
                         AND assentamento_gerado_contrato_servidor.cod_contrato = '||reRegistro.cod_contrato||'
                         AND NOT EXISTS (SELECT 1
                                           FROM pessoal'||stEntidade||'.assentamento_gerado_excluido
                                          WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                            AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)';
        inCodEventoMaternidade     := selectIntoInteger(stSql);
        IF inCodEventoMaternidade IS NOT NULL THEN
            stCodEventos := stCodEventos || inCodEventoMaternidade || ',';
        END IF;

        --DESCONTO DE PREVIDÊNCIA
        stSql := 'SELECT previdencia_evento.cod_evento
                    FROM folhapagamento'||stEntidade||'.previdencia_previdencia
              INNER JOIN ( SELECT cod_previdencia
                                , max(timestamp) as timestamp
                             FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                         GROUP BY cod_previdencia) as max_previdencia_previdencia
                      ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                     AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
              INNER JOIN folhapagamento'||stEntidade||'.previdencia
                      ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
              INNER JOIN (
                                  SELECT contrato_servidor_previdencia.cod_previdencia  
                                       , contrato_servidor_previdencia.cod_contrato                                                       
                                   FROM pessoal'||stEntidade||'.contrato_servidor_previdencia                                                                              
                             INNER JOIN ( SELECT cod_contrato                                                                                 
                                               , max(timestamp) as timestamp                                                                  
                                            FROM pessoal'||stEntidade||'.contrato_servidor_previdencia                                                        
                                        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                           
                                     ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato           
                                    AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp                 
                                    AND contrato_servidor_previdencia.bo_excluido IS FALSE
                             INNER JOIN pessoal'||stEntidade||'.contrato_servidor                                                                             
                                     ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                        
                                  UNION 
                                 SELECT contrato_pensionista_previdencia.cod_previdencia     
                                      , contrato_pensionista_previdencia.cod_contrato                                                 
                                   FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia                                                              
                             INNER JOIN ( SELECT cod_contrato                                                                                 
                                               , max(timestamp) as timestamp                                                                  
                                            FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia                                                     
                                        GROUP BY cod_contrato) as max_contrato_pensionista_previdencia                                        
                                     ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato     
                                    AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp           
                             INNER JOIN pessoal'||stEntidade||'.contrato_pensionista                                                                          
                                     ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                     
                         ) contrato_servidor_previdencia
                      ON previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia              
              INNER JOIN folhapagamento'||stEntidade||'.previdencia_evento
                      ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                     AND previdencia_previdencia.timestamp = previdencia_evento.timestamp
                   WHERE previdencia.cod_previdencia = '||inCodPrevidencia||'
                     AND contrato_servidor_previdencia.cod_contrato = '||reRegistro.cod_contrato||'
                     AND previdencia_evento.cod_tipo = 1';
        inCodEventoDescPrevidencia := selectIntoInteger(stSql);
        IF inCodEventoDescPrevidencia IS NOT NULL THEN
            stCodEventos := stCodEventos || inCodEventoDescPrevidencia || ',';
        END IF;

        --BASE DE PREVIDÊNCIA
        stSql := 'SELECT previdencia_evento.cod_evento
                    FROM folhapagamento'||stEntidade||'.previdencia_previdencia
              INNER JOIN ( SELECT cod_previdencia
                                , max(timestamp) as timestamp
                             FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                         GROUP BY cod_previdencia) as max_previdencia_previdencia
                      ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                     AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
              INNER JOIN folhapagamento'||stEntidade||'.previdencia
                      ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
              INNER JOIN (
                                  SELECT contrato_servidor_previdencia.cod_previdencia  
                                       , contrato_servidor_previdencia.cod_contrato                                                       
                                   FROM pessoal'||stEntidade||'.contrato_servidor_previdencia                                                                              
                             INNER JOIN ( SELECT cod_contrato                                                                                 
                                               , max(timestamp) as timestamp                                                                  
                                            FROM pessoal'||stEntidade||'.contrato_servidor_previdencia                                                        
                                        GROUP BY cod_contrato) as max_contrato_servidor_previdencia                                           
                                     ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato           
                                    AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                    AND contrato_servidor_previdencia.bo_excluido IS FALSE
                             INNER JOIN pessoal'||stEntidade||'.contrato_servidor                                                                             
                                     ON contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                        
                                  UNION 
                                 SELECT contrato_pensionista_previdencia.cod_previdencia     
                                      , contrato_pensionista_previdencia.cod_contrato                                                 
                                   FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia                                                              
                             INNER JOIN ( SELECT cod_contrato                                                                                 
                                               , max(timestamp) as timestamp                                                                  
                                            FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia                                                     
                                        GROUP BY cod_contrato) as max_contrato_pensionista_previdencia                                        
                                     ON contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato     
                                    AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp           
                             INNER JOIN pessoal'||stEntidade||'.contrato_pensionista                                                                          
                                     ON contrato_pensionista.cod_contrato = contrato_pensionista_previdencia.cod_contrato                     
                         ) contrato_servidor_previdencia
                      ON previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
              INNER JOIN folhapagamento'||stEntidade||'.previdencia_evento
                      ON previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                     AND previdencia_previdencia.timestamp = previdencia_evento.timestamp
                   WHERE previdencia.cod_previdencia = '||inCodPrevidencia||'
                     AND contrato_servidor_previdencia.cod_contrato = '||reRegistro.cod_contrato||'
                     AND previdencia_evento.cod_tipo = 2';

        inCodEventoBasePrevidencia := selectIntoInteger(stSql);
        IF inCodEventoBasePrevidencia IS NOT NULL THEN
            stCodEventos := stCodEventos || inCodEventoBasePrevidencia || ',';
        END IF;

        --SALÁRIO FAMILIA
        stSql := ' SELECT salario_familia_evento.cod_evento
                     FROM folhapagamento'||stEntidade||'.salario_familia_evento
               INNER JOIN ( SELECT cod_regime_previdencia
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'||stEntidade||'.salario_familia
                          GROUP BY cod_regime_previdencia) as max_salario_familia
                       ON salario_familia_evento.cod_regime_previdencia = max_salario_familia.cod_regime_previdencia
                      AND salario_familia_evento.timestamp = max_salario_familia.timestamp
               INNER JOIN folhapagamento'||stEntidade||'.regime_previdencia
                       ON salario_familia_evento.cod_regime_previdencia = regime_previdencia.cod_regime_previdencia           
               INNER JOIN folhapagamento'||stEntidade||'.previdencia
                       ON regime_previdencia.cod_regime_previdencia = previdencia.cod_regime_previdencia
               INNER JOIN folhapagamento'||stEntidade||'.previdencia_previdencia
                       ON previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
               INNER JOIN ( SELECT cod_previdencia
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                          GROUP BY cod_previdencia) as max_previdencia_previdencia
                       ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                      AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
               INNER JOIN pessoal'||stEntidade||'.contrato_servidor_previdencia
                       ON previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
               INNER JOIN ( SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                          GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                       ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                      AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                      AND contrato_servidor_previdencia.bo_excluido IS FALSE
                 WHERE previdencia.cod_previdencia = '||inCodPrevidencia||'
                   AND contrato_servidor_previdencia.cod_contrato = '||reRegistro.cod_contrato||'
                   AND salario_familia_evento.cod_tipo = 1';
                   
        
        inCodEventoSalFamilia      := selectIntoInteger(stSql);
        IF inCodEventoSalFamilia IS NOT NULL THEN
            stCodEventos := stCodEventos || inCodEventoSalFamilia || ',';
        END IF;
 
        IF TRIM(stCodEventos) != '' THEN
            stCodEventos := substr(stCodEventos,0,char_length(stCodEventos));
            -- FOLHA SALARIO E COMPLEMENTAR
           
           IF stAcumularSalCompl = 'sim' THEN
                IF inCodConfiguracao = 0 OR inCodConfiguracao = 1 THEN
                    stSql := '    SELECT evento_calculado.valor
                                       , evento_calculado.cod_evento
                                    FROM folhapagamento'||stEntidade||'.evento_calculado
                              INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                                      ON evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                                   WHERE (evento_calculado.desdobramento IS NULL OR evento_calculado.desdobramento in (''F'',''A''))
                                     AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                     AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                     AND evento_calculado.cod_evento IN ('||stCodEventos||')
                                   UNION
                                  SELECT evento_complementar_calculado.valor
                                       , evento_complementar_calculado.cod_evento
                                    FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                              INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                                      ON evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
                                     AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                                     AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                                     AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                                   WHERE registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                     AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                      '||stConsultaCodComplementar||'
                                     AND evento_complementar_calculado.cod_evento IN ('||stCodEventos||')';
                END IF;
            END IF;

            IF stAcumularSalCompl = 'nao' THEN
                IF inCodConfiguracao = 0  THEN
                    stSql := '   SELECT evento_complementar_calculado.valor
                                       , evento_complementar_calculado.cod_evento
                                    FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                              INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                                      ON evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
                                     AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                                     AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                                     AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                                   WHERE registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                     AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                                     '||stConsultaCodComplementar||'
                                     AND evento_complementar_calculado.cod_evento IN ('||stCodEventos||')';
                END IF;

                IF inCodConfiguracao = 1 THEN
                      stSql := '    SELECT evento_calculado.valor
                                       , evento_calculado.cod_evento
                                    FROM folhapagamento'||stEntidade||'.evento_calculado
                              INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                                      ON evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                                   WHERE (evento_calculado.desdobramento IS NULL OR evento_calculado.desdobramento in (''F'',''A''))
                                     AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                     AND registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                                     AND evento_calculado.cod_evento IN ('||stCodEventos||') ';

                END IF;
            END IF;
            -- FOLHA FÉRIAS
            IF inCodConfiguracao = 2 THEN
                stSql := '    SELECT evento_ferias_calculado.valor
                                   , evento_ferias_calculado.cod_evento
                                FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_ferias
                                  ON evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
                                 AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                                 AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                                 AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                               WHERE evento_ferias_calculado.desdobramento in (''F'',''A'')
							     AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_ferias.cod_contrato = '||reRegistro.cod_contrato||'
                                 AND evento_ferias_calculado.cod_evento IN ('||stCodEventos||')';
            END IF;

            -- FOLHA DÉCIMO
            IF inCodConfiguracao = 3 THEN
                stSql := '    SELECT evento_decimo_calculado.valor
                                   , evento_decimo_calculado.cod_evento
                                FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_decimo
                                  ON evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro
                                 AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                                 AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                                 AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                               WHERE registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_decimo.cod_contrato = '||reRegistro.cod_contrato||'
                                 AND evento_decimo_calculado.cod_evento IN ('||stCodEventos||')';
            END IF;

            -- FOLHA RESCISÃO
            IF inCodConfiguracao = 4 THEN
                stSql := '    SELECT evento_rescisao_calculado.valor
                                   , evento_rescisao_calculado.cod_evento
                                FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao
                                  ON evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro
                                 AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                                 AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                                 AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                               WHERE registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                                 AND evento_rescisao_calculado.cod_evento IN ('||stCodEventos||')';
            END IF;

            FOR reEventoCalculado IN EXECUTE stSql LOOP
                IF reEventoCalculado.cod_evento = inCodEventoMaternidade THEN
                    nuMaternidade := nuMaternidade + reEventoCalculado.valor;
                END IF;
                IF reEventoCalculado.cod_evento = inCodEventoDescPrevidencia THEN
                    nuDescontoPrevidencia := nuDescontoPrevidencia + reEventoCalculado.valor;
                END IF;
                IF reEventoCalculado.cod_evento = inCodEventoBasePrevidencia THEN
                    nuBasePrevidencia := nuBasePrevidencia + reEventoCalculado.valor;
                END IF;
                IF reEventoCalculado.cod_evento = inCodEventoSalFamilia THEN
                    nuSalarioFamilia := nuSalarioFamilia + reEventoCalculado.valor;
                END IF;
            END LOOP;
        END IF;
        
        IF nuMaternidade         > 0 OR
           nuDescontoPrevidencia > 0 OR 
           nuBasePrevidencia     > 0 OR 
           nuSalarioFamilia      > 0
        THEN
            IF reRegistro.cadastro = 'servidor' THEN
                stSql := '    SELECT (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm
                                   , lpad(contrato_servidor.cod_categoria::varchar,2,''0'') as categoria
                                   , (SELECT num_ocorrencia FROM pessoal'||stEntidade||'.ocorrencia WHERE cod_ocorrencia = contrato_servidor_ocorrencia.cod_ocorrencia) as num_ocorrencia
                                FROM pessoal'||stEntidade||'.servidor_contrato_servidor
                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor                     
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                          INNER JOIN (SELECT contrato_servidor_ocorrencia.*
                                        FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                                           , (  SELECT cod_contrato
                                                     , max(timestamp) as timestamp
                                                  FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                                              GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia
                                        WHERE contrato_servidor_ocorrencia.cod_contrato = max_contrato_servidor_ocorrencia.cod_contrato
                                          AND contrato_servidor_ocorrencia.timestamp = max_contrato_servidor_ocorrencia.timestamp) AS contrato_servidor_ocorrencia
                                  ON servidor_contrato_servidor.cod_contrato = contrato_servidor_ocorrencia.cod_contrato                    
                               WHERE servidor_contrato_servidor.cod_contrato = '||reRegistro.cod_contrato;
            END IF;

            IF reRegistro.cadastro = 'pensionista' THEN
                stSql := '    SELECT (SELECT nom_cgm FROM sw_cgm WHERE numcgm = pensionista.numcgm) as nom_cgm
                                   , lpad(contrato_servidor.cod_categoria::varchar,2,''0'') as categoria
                                   , (SELECT num_ocorrencia FROM pessoal'||stEntidade||'.ocorrencia WHERE cod_ocorrencia = contrato_servidor_ocorrencia.cod_ocorrencia) as num_ocorrencia
                                FROM pessoal'||stEntidade||'.pensionista
                          INNER JOIN pessoal'||stEntidade||'.contrato_pensionista
                                  ON pensionista.cod_pensionista       = contrato_pensionista.cod_pensionista
                                 AND pensionista.cod_contrato_cedente  = contrato_pensionista.cod_contrato_cedente
                                 AND contrato_pensionista.cod_contrato = '||reRegistro.cod_contrato||'
                          INNER JOIN pessoal'||stEntidade||'.contrato_servidor                     
                                  ON contrato_pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato                          
                          INNER JOIN (SELECT contrato_servidor_ocorrencia.*
                                        FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                                           , (  SELECT cod_contrato
                                                     , max(timestamp) as timestamp
                                                  FROM pessoal'||stEntidade||'.contrato_servidor_ocorrencia
                                              GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia
                                        WHERE contrato_servidor_ocorrencia.cod_contrato = max_contrato_servidor_ocorrencia.cod_contrato
                                          AND contrato_servidor_ocorrencia.timestamp = max_contrato_servidor_ocorrencia.timestamp) AS contrato_servidor_ocorrencia
                                  ON contrato_servidor.cod_contrato = contrato_servidor_ocorrencia.cod_contrato';
            END IF;

            OPEN crDados FOR EXECUTE stSql;
                FETCH crDados INTO reDados;
            CLOSE crDados;    

           SELECT max(valor) as exercicio
             INTO stExercicio
             FROM administracao.configuracao 
            WHERE parametro = 'ano_exercicio';       
            
            rwContribuicaoPrevidenciaria.cod_contrato          := reRegistro.cod_contrato;
            rwContribuicaoPrevidenciaria.registro              := selectIntoInteger('SELECT registro FROM pessoal'||stEntidade||'.contrato WHERE cod_contrato = '||reRegistro.cod_contrato);
            rwContribuicaoPrevidenciaria.nom_cgm               := reDados.nom_cgm;
            rwContribuicaoPrevidenciaria.categoria             := reDados.categoria;
            rwContribuicaoPrevidenciaria.num_ocorrencia        := reDados.num_ocorrencia;
            rwContribuicaoPrevidenciaria.contador              := pega0QtdDependentesSalarioFamilia( reRegistro.cod_contrato, stDataFinalCompetencia );
            rwContribuicaoPrevidenciaria.subDivisao            := selectIntoVarchar('SELECT regime.descricao||''-''||sub_divisao.descricao as descricao FROM pessoal'||stEntidade||'.sub_divisao INNER JOIN pessoal'||stEntidade||'.regime using(cod_regime) WHERE cod_sub_divisao = '||reRegistro.cod_sub_divisao);
            rwContribuicaoPrevidenciaria.orgao                 := recuperaDescricaoOrgao(reRegistro.cod_orgao,(stExercicio||'-01-01')::date);
            IF reRegistro.cod_local IS NOT NULL THEN
                rwContribuicaoPrevidenciaria.local             := selectIntoVarchar('SELECT descricao FROM organograma.local WHERE cod_local = '||reRegistro.cod_local);
            END IF;
            rwContribuicaoPrevidenciaria.maternidade           := COALESCE(nuMaternidade,0.00);
            rwContribuicaoPrevidenciaria.familia               := COALESCE(nuSalarioFamilia,0.00);
            rwContribuicaoPrevidenciaria.base                  := COALESCE(nuBasePrevidencia,0.00);
            rwContribuicaoPrevidenciaria.desconto              := COALESCE(nuDescontoPrevidencia,0.00);

            RETURN NEXT rwContribuicaoPrevidenciaria;
        END IF;    
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

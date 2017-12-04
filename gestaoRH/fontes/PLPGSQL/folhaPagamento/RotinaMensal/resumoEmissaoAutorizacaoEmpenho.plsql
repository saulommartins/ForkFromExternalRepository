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
    * Resumo para Emissão das Autorizações de Empenho
    * Data de Criação: 19/07/2007
    
    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza
    
    * @ignore
    
    $Id: resumoEmissaoAutorizacaoEmpenho.plsql 66547 2016-09-21 12:59:39Z michel $
    
    * Casos de uso: uc-04.05.62
*/

DROP TYPE colunasResumo CASCADE;
CREATE TYPE colunasResumo AS (
    orgao               VARCHAR(100),
    unidade             VARCHAR(100),
    saldo_dotacao       VARCHAR,
    red_dotacao         VARCHAR,
    rubrica_despesa     VARCHAR(100),
    valor               NUMERIC,
    num_pao             VARCHAR,
    desc_pao            VARCHAR,
    lla                 VARCHAR,
    fornecedor          VARCHAR,
    evento              VARCHAR
);


CREATE OR REPLACE FUNCTION resumoEmissaoAutorizacaoEmpenho(INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR) RETURNS SETOF colunasResumo AS $$
DECLARE
    inCodPeriodoMovimentacao            ALIAS FOR $1;
    inCodConfiguracao                   ALIAS FOR $2;
    stExercicio                         ALIAS FOR $3;
    stEntidadeParametro                 ALIAS FOR $4;
    inCodConfiguracaoAutorizacao        ALIAS FOR $5;
    stCadastro                          ALIAS FOR $6;
    stOrigem                            ALIAS FOR $7;    
    inCodPrevidencia                    ALIAS FOR $8;
    stFiltro                            ALIAS FOR $9;
    stJonAtributo                       ALIAS FOR $10;
    
    stEntidade                          VARCHAR;
    stSql                               VARCHAR;
    stCodEstrutural                     VARCHAR;
    stCodEstruturalUltimo               VARCHAR:='';
    stCodEstruturalOriginal             VARCHAR:='';
    stJoinAtributoInterno               VARCHAR:='';
    stMascaraDespesa                    VARCHAR;
    stLLA                               VARCHAR;
    stFornecedor                        VARCHAR;
    stOrgao                             VARCHAR;
    stUnidade                           VARCHAR;
    stComparacao1                       VARCHAR:='';
    stComparacao2                       VARCHAR:='';
    arComparacao                        VARCHAR[];
    stEventos                           VARCHAR:='';
    stFiltroInterno                     VARCHAR:='';
    stExercicioDespesa                  VARCHAR:='';
    reRegistro                          RECORD; 
    reDespesa                           RECORD;   
    reConfiguracao                      RECORD;
    reConfiguracaoPorEvento             RECORD;
    inCodDespesa                        VARCHAR;
    inNumPAO                            VARCHAR;
    boRetorno                           BOOLEAN;
    boInserir                           BOOLEAN:=FALSE;
    stCodEvento                         VARCHAR;
    crDespesa                           REFCURSOR;
    crCursor                            REFCURSOR;
    nuAliquotaPrevidencia               NUMERIC;
    nuAliquotaRat                       NUMERIC;
    nuSomaEventos                       NUMERIC:=0.00;
    nuDescontoPrevidencia               NUMERIC:=0.00;
    inCountLotacao                      INTEGER;
    inCountLocal                        INTEGER;
    inCountAtributo                     INTEGER;
    inCountLotacaoConfEvento            INTEGER;
    inCountLocalConfEvento              INTEGER;
    inCountAtributoConfEvento           INTEGER;    
    inCodAtributo                       INTEGER;
    inCodEventoDescontoPrevidencia      INTEGER;
    inCodConta                          INTEGER;
    stTimestampFechamentoPeriodo        VARCHAR;
    rwColunasResumo                     colunasResumo%ROWTYPE;
BEGIN

    stEntidade := criarBufferEntidade(stEntidadeParametro);
    
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidadeParametro);    
    
    ----------------------------------------------------
    --Busca Configuração para a autorizacao de empenho                                              
    ----------------------------------------------------                       

    stSql := '    SELECT ultima_vigencia_competencia.vigencia as dt_vigencia
                       , to_char(ultima_vigencia_competencia.vigencia,''dd/mm/yyyy'') as vigencia
                       , to_char(ultima_vigencia_competencia.vigencia,''yyyy'') as exercicio
                       , ultima_vigencia_competencia.cod_periodo_movimentacao
                       , (   SELECT max(timestamp)                                               
                               FROM (                                                            
                                        SELECT max(timestamp) as timestamp
                                          FROM folhapagamento'|| stEntidade ||'.configuracao_empenho
                                         WHERE vigencia = ultima_vigencia_competencia.vigencia   
                                         UNION
                                        SELECT max(timestamp) as timestamp
                                          FROM folhapagamento'|| stEntidade ||'.configuracao_autorizacao_empenho
                                         WHERE vigencia = ultima_vigencia_competencia.vigencia
                                         UNION
                                        SELECT max(timestamp) as timestamp
                                          FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla
                                         WHERE vigencia = ultima_vigencia_competencia.vigencia
                                    ) as max_timestamp_vigencia
                         ) as timestamp                                                          
                    FROM (   SELECT DISTINCT max(vigencia) as vigencia
                                  , ( SELECT cod_periodo_movimentacao
                                        FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                       WHERE vigencia BETWEEN dt_inicial AND dt_final
                                    ) as cod_periodo_movimentacao
                               FROM ( SELECT vigencia
                                        FROM folhapagamento'|| stEntidade ||'.configuracao_empenho
                                       UNION
                                      SELECT vigencia
                                        FROM folhapagamento'|| stEntidade ||'.configuracao_autorizacao_empenho
                                       UNION
                                      SELECT vigencia
                                        FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla
                                    ) as configuracoes_empenho
                           GROUP BY cod_periodo_movimentacao
                         ) as ultima_vigencia_competencia
                   WHERE ultima_vigencia_competencia.vigencia <= (SELECT dt_final 
                                                                    FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao 
                                                                   WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||')
                     AND to_char(ultima_vigencia_competencia.vigencia,''yyyy'') = '|| quote_literal(stExercicio) ||'
                ORDER BY dt_vigencia DESC LIMIT 1';
                
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reConfiguracao;
    CLOSE crCursor;
    
    stMascaraDespesa := selectIntoVarchar('SELECT valor 
                                             FROM administracao.configuracao 
                                            WHERE parametro = ''masc_class_despesa'' 
                                              AND exercicio = '|| quote_literal(stExercicio) ||' ');
    
    inCountLotacao  := selectIntoInteger('SELECT count(*) 
                                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_lotacao 
                                           WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                             AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                                           
    inCountLocal    := selectIntoInteger('SELECT count(*) 
                                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_local 
                                           WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                             AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                                           
    inCountAtributo := selectIntoInteger('SELECT count(*) 
                                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo 
                                           WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                             AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
    
    inCountLotacaoConfEvento  := selectIntoInteger('SELECT count(*)
                                                      FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lotacao
                                                     WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                       AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                                                     
    inCountLocalConfEvento    := selectIntoInteger('SELECT count(*)
                                                      FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_local 
                                                     WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                       AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                                                     
    inCountAtributoConfEvento := selectIntoInteger('SELECT count(*)
                                                      FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_atributo
                                                     WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                       AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');

    -- origem fgts
    IF stOrigem = 'g' THEN
        stCodEvento := selectIntoInteger('SELECT fgts_evento.cod_evento
                                            FROM folhapagamento'|| stEntidade ||'.fgts_evento
                                               , (  SELECT cod_fgts
                                                         , max(timestamp) as timestamp
                                                      FROM folhapagamento'|| stEntidade ||'.fgts_evento
                                                     WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                  GROUP BY cod_fgts) as max_fgts_evento
                                           WHERE fgts_evento.cod_fgts = max_fgts_evento.cod_fgts
                                             AND fgts_evento.timestamp = max_fgts_evento.timestamp
                                             AND fgts_evento.cod_tipo = 1');    
                                             
        stCodEvento := stCodEvento  ||','|| 
                       selectIntoInteger('SELECT fgts_evento.cod_evento
                                            FROM folhapagamento'|| stEntidade ||'.fgts_evento
                                               , (  SELECT cod_fgts
                                                         , max(timestamp) as timestamp
                                                      FROM folhapagamento'|| stEntidade ||'.fgts_evento
                                                     WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                  GROUP BY cod_fgts) as max_fgts_evento
                                           WHERE fgts_evento.cod_fgts = max_fgts_evento.cod_fgts
                                             AND fgts_evento.timestamp = max_fgts_evento.timestamp
                                             AND fgts_evento.cod_tipo = 2');   
    END IF;
    
    -- origem previdencia
    IF stOrigem = 'p' THEN
        stCodEvento := selectIntoInteger('SELECT previdencia_evento.cod_evento
                                            FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                                               , (  SELECT cod_previdencia
                                                         , max(timestamp) as timestamp
                                                      FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                                                     WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                  GROUP BY cod_previdencia) as max_previdencia_evento
                                               , folhapagamento'|| stEntidade ||'.evento
                                           WHERE previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia
                                             AND previdencia_evento.timestamp = max_previdencia_evento.timestamp
                                             AND previdencia_evento.cod_evento = evento.cod_evento
                                             AND previdencia_evento.cod_previdencia = '|| inCodPrevidencia ||'
                                             AND evento.natureza = ''B''');
                                           
        inCodEventoDescontoPrevidencia := selectIntoInteger('SELECT previdencia_evento.cod_evento
                                                               FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                                                                  , (  SELECT cod_previdencia
                                                                            , max(timestamp) as timestamp
                                                                         FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                                                                        WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                                     GROUP BY cod_previdencia) as max_previdencia_evento
                                                                  , folhapagamento'|| stEntidade ||'.evento
                                                              WHERE previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia
                                                                AND previdencia_evento.timestamp = max_previdencia_evento.timestamp
                                                                AND previdencia_evento.cod_evento = evento.cod_evento
                                                                AND previdencia_evento.cod_previdencia = '|| inCodPrevidencia ||'
                                                                AND evento.natureza = ''D'' ');
                                           
        nuAliquotaPrevidencia := selectIntoNumeric('SELECT aliquota
                                                      FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                         , (SELECT cod_previdencia
                                                                 , max(timestamp) as timestamp 
                                                              FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                             WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'                                                              
                                                             GROUP BY cod_previdencia) as max_previdencia_previdencia
                                                     WHERE previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                                       AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                                       AND previdencia_previdencia.cod_previdencia = '|| inCodPrevidencia);
                                                       
        nuAliquotaRat := selectIntoNumeric('SELECT aliquota_rat
                                              FROM folhapagamento'|| stEntidade ||'.previdencia_regime_rat
                                                 , (  SELECT cod_previdencia
                                                           , max(timestamp) as timestamp 
                                                        FROM folhapagamento'|| stEntidade ||'.previdencia_regime_rat
                                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                    GROUP BY cod_previdencia) as max_previdencia_regime_rat
                                             WHERE previdencia_regime_rat.cod_previdencia = max_previdencia_regime_rat.cod_previdencia
                                               AND previdencia_regime_rat.timestamp = max_previdencia_regime_rat.timestamp
                                               AND previdencia_regime_rat.cod_previdencia = '|| inCodPrevidencia);                                                       
    END IF;
    
    -- origem folha de pagamento
    IF stOrigem = 'f' THEN
        stFiltroInterno := stFiltroInterno || ' AND (evento.natureza = ''P'' OR evento.natureza = ''D'')';
    END IF;
    
    
    IF stCadastro = 'a' OR stCadastro = 'o' THEN
    
        inCodAtributo := selectIntoInteger('SELECT cod_atributo 
                                              FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo 
                                             WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                               AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||'
                                             LIMIT 1');
        
        stJoinAtributoInterno := '  LEFT JOIN (SELECT atributo_contrato_servidor_valor.*                                                                    
                                                 FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                                
                                                    , (  SELECT cod_contrato                                                                            
                                                              , cod_atributo                                                                            
                                                              , max(timestamp) as timestamp                                                             
                                                           FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                      
                                                          WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                       GROUP BY cod_contrato                                                                          
                                                              , cod_atributo) as max_atributo_contrato_servidor_valor                                 
                                                WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
                                                  AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp      
                                                  AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo              
                                                  AND atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo ||') as atributo
                                           ON contrato.cod_contrato = atributo.cod_contrato';
    ELSE
    
        inCodAtributo := selectIntoInteger('SELECT cod_atributo 
                                              FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo 
                                             WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                               AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||'
                                             LIMIT 1');
        
        stJoinAtributoInterno := '  LEFT JOIN (SELECT atributo_contrato_pensionista.*                                                                    
                                                 FROM pessoal'|| stEntidade ||'.atributo_contrato_pensionista 
                                                    , (  SELECT cod_contrato                                                                            
                                                              , cod_atributo                                                                            
                                                              , max(timestamp) as timestamp                                                             
                                                           FROM pessoal'|| stEntidade ||'.atributo_contrato_pensionista                      
                                                          WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                       GROUP BY cod_contrato                                                                          
                                                              , cod_atributo) as max_atributo_contrato_pensionista
                                                WHERE atributo_contrato_pensionista.cod_contrato = max_atributo_contrato_pensionista.cod_contrato
                                                  AND atributo_contrato_pensionista.timestamp    = max_atributo_contrato_pensionista.timestamp      
                                                  AND atributo_contrato_pensionista.cod_atributo = max_atributo_contrato_pensionista.cod_atributo              
                                                  AND atributo_contrato_pensionista.cod_atributo = '|| inCodAtributo ||') as atributo
                                           ON contrato.cod_contrato = atributo.cod_contrato';
    END IF;
    
    --Eventos calculados da Folha Salário
    IF inCodConfiguracao = 1 THEN
        stSql := 'SELECT sum(evento_calculado.valor) as soma
                       , configuracao_evento_despesa.cod_conta
                       , evento_calculado.cod_evento
                       , evento.codigo||''-''||evento.descricao as evento
                       , cadastro.cod_sub_divisao
                       , evento.natureza
                       , cadastro.cod_cargo
                       
                       ';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao
                                , (SELECT orgao||''-''|| recuperaDescricaoOrgao(cadastro.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) FROM organograma.vw_orgao_nivel WHERE cod_orgao = cadastro.cod_orgao) as lla';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local
                                , (SELECT descricao FROM organograma.local WHERE cod_local = cadastro.cod_local) as lla';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo
                                , cadastro.valor as lla';
        END IF;        
        stSql := stSql || '                       
                    FROM folhapagamento'|| stEntidade ||'.evento_calculado
                       , folhapagamento'|| stEntidade ||'.evento
                       , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                       , folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                       , (  SELECT cod_evento
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                             WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY cod_evento) as max_configuracao_evento_despesa
                          
                       , (     SELECT contrato.*
                                    , trim(lower(recuperarsituacaodocontrato(contrato.cod_contrato,'|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||'))) AS situacao
                                    , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                      ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao 
                                    , CASE WHEN contrato_servidor_previdencia.cod_contrato IS NOT NULL THEN contrato_servidor_previdencia.cod_previdencia
                                      ELSE contrato_pensionista_previdencia.cod_previdencia END as cod_previdencia
                                    , contrato_servidor_local.cod_local
                                    , CASE WHEN contrato_servidor_sub_divisao_funcao.cod_contrato IS NOT NULL THEN contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                      ELSE contrato_pensionista_sub_divisao_funcao.cod_sub_divisao END as cod_sub_divisao
                                    , CASE WHEN contrato_servidor_funcao.cod_contrato IS NOT NULL THEN contrato_servidor_funcao.cod_cargo
                                      ELSE contrato_pensionista_funcao.cod_cargo END as cod_cargo
                                    ';
                IF stJonAtributo != '' OR inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN                      
                    stSql := stSql  ||'  , valor,cod_atributo ';         
                ELSE      
                    stSql := stSql  ||'  , 0 AS valor,0 as cod_atributo ';         
                END IF;
              stSql := stSql  ||' FROM pessoal'|| stEntidade ||'.contrato ';
                IF stJonAtributo != '' THEN
                    stSql := stSql || stJonAtributo;
                ELSE
                    IF inCountAtributo >=1 OR inCountAtributoConfEvento >= 1 THEN
                        stSql := stSql || stJoinAtributoInterno;
                    END IF;
                END IF;
              stSql := stSql  ||'                                           
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_funcao.cod_cargo
                                            , contrato_servidor_funcao.timestamp
                                            , contrato_servidor_funcao.vigencia
                                            , contrato_pensionista.cod_contrato
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                            , pessoal'|| stEntidade ||'.contrato_servidor_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato
                                              ) as max_contrato_servidor_funcao
                                        WHERE contrato_servidor_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                          AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                                          AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp
                                      ) as contrato_pensionista_funcao
                                   ON contrato.cod_contrato = contrato_pensionista_funcao.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                            , contrato_servidor_sub_divisao_funcao.timestamp
                                            , contrato_pensionista.cod_contrato
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                            , pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                          AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp
                                      ) as contrato_pensionista_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_pensionista_sub_divisao_funcao.cod_contrato

                            LEFT JOIN (SELECT aposentadoria.*
                                         FROM pessoal'|| stEntidade ||'.aposentadoria
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.aposentadoria
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_aposentadoria
                                        WHERE aposentadoria.cod_contrato = max_aposentadoria.cod_contrato
                                          AND aposentadoria.timestamp = max_aposentadoria.timestamp) as aposentadoria
                                   ON contrato.cod_contrato = aposentadoria.cod_contrato                                     
                                   
                            LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor
                                   ON contrato_servidor.cod_contrato = contrato.cod_contrato
                                   
                                  
                            LEFT JOIN (SELECT contrato_servidor_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                          AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                          AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o''
                                          AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                                   ON contrato.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                                                     
                                   
                            LEFT JOIN (SELECT contrato_pensionista_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                          AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
                                          AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o'') as contrato_pensionista_previdencia
                                   ON contrato.cod_contrato = contrato_pensionista_previdencia.cod_contrato                                                                                                                                        

                            LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   
                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp) as contrato_servidor_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                   
                             
                            LEFT JOIN (SELECT contrato_servidor_funcao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_funcao
                                        WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                                          AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp
                            ) as contrato_servidor_funcao
                                   ON contrato.cod_contrato = contrato_servidor_funcao.cod_contrato     

                                   ) as cadastro
                   WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                     AND evento_calculado.cod_evento = configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.cod_evento = max_configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.timestamp = max_configuracao_evento_despesa.timestamp
                     AND registro_evento_periodo.cod_contrato = cadastro.cod_contrato
                     AND evento_calculado.cod_evento = evento.cod_evento
                     AND configuracao_evento_despesa.cod_configuracao = '|| inCodConfiguracao ||'
                     AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cadastro.situacao = '|| quote_literal(stCadastro) ||' '|| stFiltro||stFiltroInterno;
        IF stCodEvento IS NOT NULL THEN
            stSql := stSql ||' AND evento_calculado.cod_evento IN ('|| stCodEvento ||')';
        END IF;       
        IF inCodPrevidencia != 0 THEN
            stSql := stSql ||' AND cadastro.cod_previdencia = '|| inCodPrevidencia; 
        END IF;

        stSql := stSql || '                     
                GROUP BY configuracao_evento_despesa.cod_conta
                       , evento_calculado.cod_evento
                       , evento.codigo
                       , evento.descricao
                       , cadastro.cod_sub_divisao
                       , evento.natureza
                       , cadastro.cod_cargo
                       
                       ';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo';
        END IF;                 
    END IF;
    --Eventos calculados da Folha Férias
    IF inCodConfiguracao = 2 THEN
        stSql := 'SELECT sum(evento_ferias_calculado.valor) as soma
                       , configuracao_evento_despesa.cod_conta
                       , evento_ferias_calculado.cod_evento
                       , evento.codigo||''-''||evento.descricao as evento
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao
                                , (SELECT orgao||''-''|| recuperaDescricaoOrgao(cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) FROM organograma.vw_orgao_nivel WHERE cod_orgao = cadastro.cod_orgao) as lla';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local
                                , (SELECT descricao FROM organograma.local WHERE cod_local = cadastro.cod_local) as lla';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo
                                , cadastro.valor as lla';
        END IF;        
        stSql := stSql || '                       
                    FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                       , folhapagamento'|| stEntidade ||'.evento
                       , folhapagamento'|| stEntidade ||'.registro_evento_ferias
                       , folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                       , (  SELECT cod_evento
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                             WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY cod_evento) as max_configuracao_evento_despesa
                          
                       , (     SELECT contrato.*
                                    , trim(lower(recuperarsituacaodocontrato(contrato.cod_contrato,'|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||'))) AS situacao
                                    , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                      ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao 
                                    , CASE WHEN contrato_servidor_previdencia.cod_contrato IS NOT NULL THEN contrato_servidor_previdencia.cod_previdencia
                                      ELSE contrato_pensionista_previdencia.cod_previdencia END as cod_previdencia                                      
                                    , contrato_servidor_local.cod_local
                                    , CASE WHEN contrato_servidor_sub_divisao_funcao.cod_contrato IS NOT NULL THEN contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                      ELSE contrato_pensionista_sub_divisao_funcao.cod_sub_divisao END as cod_sub_divisao';
                IF stJonAtributo != '' OR inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN                      
                    stSql := stSql  ||'  , valor,cod_atributo ';         
                ELSE      
                    stSql := stSql  ||'  , 0 AS valor,0 as cod_atributo ';         
                END IF;
              stSql := stSql  ||' FROM pessoal'|| stEntidade ||'.contrato ';
                IF stJonAtributo != '' THEN
                    stSql := stSql || stJonAtributo;
                ELSE
                    IF inCountAtributo >=1 OR inCountAtributoConfEvento >= 1 THEN
                        stSql := stSql || stJoinAtributoInterno;
                    END IF;
                END IF;
              stSql := stSql  ||'                                           
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                              GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                            , contrato_servidor_sub_divisao_funcao.timestamp
                                            , contrato_pensionista.cod_contrato
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                            , pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                          AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp
                                      ) as contrato_pensionista_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_pensionista_sub_divisao_funcao.cod_contrato

                            LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor
                                   ON contrato_servidor.cod_contrato = contrato.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                          AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                          AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o''
                                          AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                                   ON contrato.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                                                     

                            LEFT JOIN (SELECT contrato_pensionista_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                          AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
                                          AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o'') as contrato_pensionista_previdencia
                                   ON contrato.cod_contrato = contrato_pensionista_previdencia.cod_contrato                                                                                                                                                                           
                                   
                            LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   
                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp) as contrato_servidor_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                                                      
                                   ) as cadastro
                   WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                     AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                     AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                     AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                     AND evento_ferias_calculado.cod_evento = configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.cod_evento = max_configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.timestamp = max_configuracao_evento_despesa.timestamp
                     AND registro_evento_ferias.cod_contrato = cadastro.cod_contrato
                     AND evento_ferias_calculado.cod_evento = evento.cod_evento
                     AND configuracao_evento_despesa.cod_configuracao = '|| inCodConfiguracao ||'
                     AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cadastro.situacao = '|| quote_literal(stCadastro) ||' '|| stFiltro||stFiltroInterno;
        IF stCodEvento IS NOT NULL THEN
            stSql := stSql ||' AND evento_ferias_calculado.cod_evento IN ('|| stCodEvento ||')';
        END IF;       
        IF inCodPrevidencia != 0 THEN
            stSql := stSql ||' AND cadastro.cod_previdencia = '|| inCodPrevidencia; 
        END IF;
        stSql := stSql || '                     
                GROUP BY configuracao_evento_despesa.cod_conta
                       , evento_ferias_calculado.cod_evento
                       , evento.codigo
                       , evento.descricao
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo';
        END IF;                                   
    END IF;
    --Eventos calculados da Folha Décimo   
    IF inCodConfiguracao = 3 THEN
        stSql := 'SELECT sum(evento_decimo_calculado.valor) as soma
                       , configuracao_evento_despesa.cod_conta
                       , evento_decimo_calculado.cod_evento
                       , evento.codigo||''-''||evento.descricao as evento
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao
                                , (SELECT orgao||''-''|| recuperaDescricaoOrgao(cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) FROM organograma.vw_orgao_nivel WHERE cod_orgao = cadastro.cod_orgao) as lla';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local
                                , (SELECT descricao FROM organograma.local WHERE cod_local = cadastro.cod_local) as lla';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo
                                , cadastro.valor as lla';
        END IF;         
        stSql := stSql || '                       
                    FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                       , folhapagamento'|| stEntidade ||'.evento
                       , folhapagamento'|| stEntidade ||'.registro_evento_decimo
                       , folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                       , (  SELECT cod_evento
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                             WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY cod_evento) as max_configuracao_evento_despesa
                          
                       , (     SELECT contrato.*
                                    , trim(lower(recuperarsituacaodocontrato(contrato.cod_contrato,'|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||'))) AS situacao
                                    , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                      ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao 
                                    , CASE WHEN contrato_servidor_previdencia.cod_contrato IS NOT NULL THEN contrato_servidor_previdencia.cod_previdencia
                                      ELSE contrato_pensionista_previdencia.cod_previdencia END as cod_previdencia                                      
                                    , contrato_servidor_local.cod_local
                                    , CASE WHEN contrato_servidor_sub_divisao_funcao.cod_contrato IS NOT NULL THEN contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                      ELSE contrato_pensionista_sub_divisao_funcao.cod_sub_divisao END as cod_sub_divisao';
                IF stJonAtributo != '' OR inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN                      
                    stSql := stSql  ||'  , valor,cod_atributo ';         
                ELSE      
                    stSql := stSql  ||'  , 0 AS valor,0 as cod_atributo ';         
                END IF;
              stSql := stSql  ||' FROM pessoal'|| stEntidade ||'.contrato ';
                IF stJonAtributo != '' THEN
                    stSql := stSql || stJonAtributo;
                ELSE
                    IF inCountAtributo >=1 OR inCountAtributoConfEvento >= 1 THEN
                        stSql := stSql || stJoinAtributoInterno;
                    END IF;
                END IF;
              stSql := stSql  ||'                                           
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                            , contrato_servidor_sub_divisao_funcao.timestamp
                                            , contrato_pensionista.cod_contrato
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                            , pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                          AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp
                                      ) as contrato_pensionista_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_pensionista_sub_divisao_funcao.cod_contrato

                            LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor
                                   ON contrato_servidor.cod_contrato = contrato.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                          AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                          AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o''
                                          AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                                   ON contrato.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                                                     

                            LEFT JOIN (SELECT contrato_pensionista_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                          AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
                                          AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o'') as contrato_pensionista_previdencia
                                   ON contrato.cod_contrato = contrato_pensionista_previdencia.cod_contrato                                                                                                                                                                           
                                   
                            LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   
                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp) as contrato_servidor_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                                                      
                                   
                                   ) as cadastro
                   WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                     AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                     AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                     AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                     AND evento_decimo_calculado.cod_evento = configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.cod_evento = max_configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.timestamp = max_configuracao_evento_despesa.timestamp
                     AND registro_evento_decimo.cod_contrato = cadastro.cod_contrato
                     AND evento_decimo_calculado.cod_evento = evento.cod_evento
                     AND configuracao_evento_despesa.cod_configuracao = '|| inCodConfiguracao ||'
                     AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cadastro.situacao = '|| quote_literal(stCadastro) ||' '|| stFiltro||stFiltroInterno;
        IF stCodEvento IS NOT NULL THEN
            stSql := stSql ||' AND evento_decimo_calculado.cod_evento IN ('|| stCodEvento ||')';
        END IF;       
        IF inCodPrevidencia != 0 THEN
            stSql := stSql ||' AND cadastro.cod_previdencia = '|| inCodPrevidencia; 
        END IF;
        stSql := stSql || '                     
                GROUP BY configuracao_evento_despesa.cod_conta
                       , evento_decimo_calculado.cod_evento
                       , evento.codigo
                       , evento.descricao
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo';
        END IF;                                      
    END IF;
    --Eventos calculados da Folha Rescisição
    IF inCodConfiguracao = 4 THEN
        stSql := 'SELECT sum(evento_rescisao_calculado.valor) as soma
                       , configuracao_evento_despesa.cod_conta
                       , evento_rescisao_calculado.cod_evento
                       , evento.codigo||''-''||evento.descricao as evento
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao
                                , (SELECT orgao||''-''|| recuperaDescricaoOrgao(cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) FROM organograma.vw_orgao_nivel WHERE cod_orgao = cadastro.cod_orgao) as lla';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local
                                , (SELECT descricao FROM organograma.local WHERE cod_local = cadastro.cod_local) as lla';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo
                                , cadastro.valor as lla';
        END IF;       
        stSql := stSql || '                       
                    FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                       , folhapagamento'|| stEntidade ||'.evento
                       , folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                       , folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                       , (  SELECT cod_evento
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                             WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY cod_evento) as max_configuracao_evento_despesa
                          
                       , (     SELECT contrato.*
                                    , trim(lower(recuperarsituacaodocontrato(contrato.cod_contrato,'|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||'))) AS situacao
                                    , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                      ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao 
                                    , CASE WHEN contrato_servidor_previdencia.cod_contrato IS NOT NULL THEN contrato_servidor_previdencia.cod_previdencia
                                      ELSE contrato_pensionista_previdencia.cod_previdencia END as cod_previdencia                                      
                                    , contrato_servidor_local.cod_local
                                    , CASE WHEN contrato_servidor_sub_divisao_funcao.cod_contrato IS NOT NULL THEN contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                      ELSE contrato_pensionista_sub_divisao_funcao.cod_sub_divisao END as cod_sub_divisao';
                IF stJonAtributo != '' OR inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN                      
                    stSql := stSql  ||'  , valor,cod_atributo ';         
                ELSE      
                    stSql := stSql  ||'  , 0 AS valor,0 as cod_atributo ';         
                END IF;
              stSql := stSql  ||' FROM pessoal'|| stEntidade ||'.contrato ';
                IF stJonAtributo != '' THEN
                    stSql := stSql || stJonAtributo;
                ELSE
                    IF inCountAtributo >=1 OR inCountAtributoConfEvento >= 1 THEN
                        stSql := stSql || stJoinAtributoInterno;
                    END IF;
                END IF;
              stSql := stSql  ||'                                           
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                            , contrato_servidor_sub_divisao_funcao.timestamp
                                            , contrato_pensionista.cod_contrato
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                            , pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                          AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp
                                      ) as contrato_pensionista_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_pensionista_sub_divisao_funcao.cod_contrato

                            LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor
                                   ON contrato_servidor.cod_contrato = contrato.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                          AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                          AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o''
                                          AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                                   ON contrato.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                                                     

                            LEFT JOIN (SELECT contrato_pensionista_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                          AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
                                          AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o'') as contrato_pensionista_previdencia
                                   ON contrato.cod_contrato = contrato_pensionista_previdencia.cod_contrato                                                                                                                                                                           
                                   
                            LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   
                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp) as contrato_servidor_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                                                      
                                   
                                   ) as cadastro
                   WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                     AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                     AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                     AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                     AND evento_rescisao_calculado.cod_evento = configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.cod_evento = max_configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.timestamp = max_configuracao_evento_despesa.timestamp
                     AND registro_evento_rescisao.cod_contrato = cadastro.cod_contrato
                     AND evento_rescisao_calculado.cod_evento = evento.cod_evento
                     AND configuracao_evento_despesa.cod_configuracao = '|| inCodConfiguracao ||'
                     AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cadastro.situacao = '|| quote_literal(stCadastro) ||' '|| stFiltro||stFiltroInterno;
        IF stCodEvento IS NOT NULL THEN
            stSql := stSql ||' AND evento_rescisao_calculado.cod_evento IN ('|| stCodEvento ||')';
        END IF;       
        IF inCodPrevidencia != 0 THEN
            stSql := stSql ||' AND cadastro.cod_previdencia = '|| inCodPrevidencia; 
        END IF;
        stSql := stSql || '                     
                GROUP BY configuracao_evento_despesa.cod_conta
                       , evento_rescisao_calculado.cod_evento
                       , evento.codigo
                       , evento.descricao
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo';
        END IF;                                    
    END IF;

    --Eventos calculados da Folha Complementar   
    IF inCodConfiguracao = 0 THEN
        stSql := 'SELECT sum(evento_complementar_calculado.valor) as soma
                       , configuracao_evento_despesa.cod_conta
                       , evento_complementar_calculado.cod_evento
                       , evento.codigo||''-''||evento.descricao as evento
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao
                                , (SELECT orgao||''-''|| recuperaDescricaoOrgao(cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) FROM organograma.vw_orgao_nivel WHERE cod_orgao = cadastro.cod_orgao) as lla';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local
                                , (SELECT descricao FROM organograma.local WHERE cod_local = cadastro.cod_local) as lla';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo
                                , cadastro.valor as lla';
        END IF;        
        stSql := stSql || '                       
                    FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                       , folhapagamento'|| stEntidade ||'.evento
                       , folhapagamento'|| stEntidade ||'.registro_evento_complementar
                       , folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                       , (  SELECT cod_evento
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.configuracao_evento_despesa
                             WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                          GROUP BY cod_evento) as max_configuracao_evento_despesa
                          
                       , (     SELECT contrato.*
                                    , trim(lower(recuperarsituacaodocontrato(contrato.cod_contrato,'|| inCodPeriodoMovimentacao ||', '|| quote_literal(stEntidade) ||'))) AS situacao
                                    , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                      ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao 
                                    , CASE WHEN contrato_servidor_previdencia.cod_contrato IS NOT NULL THEN contrato_servidor_previdencia.cod_previdencia
                                      ELSE contrato_pensionista_previdencia.cod_previdencia END as cod_previdencia                                      
                                    , contrato_servidor_local.cod_local
                                    , CASE WHEN contrato_servidor_sub_divisao_funcao.cod_contrato IS NOT NULL THEN contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                      ELSE contrato_pensionista_sub_divisao_funcao.cod_sub_divisao END as cod_sub_divisao';
                IF stJonAtributo != '' OR inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN                      
                    stSql := stSql  ||'  , valor,cod_atributo ';         
                ELSE      
                    stSql := stSql  ||'  , 0 AS valor,0 as cod_atributo ';         
                END IF;
              stSql := stSql  ||' FROM pessoal'|| stEntidade ||'.contrato ';
                IF stJonAtributo != '' THEN
                    stSql := stSql || stJonAtributo;
                ELSE
                    IF inCountAtributo >=1 OR inCountAtributoConfEvento >=1 THEN
                        stSql := stSql || stJoinAtributoInterno;
                    END IF;
                END IF;
              stSql := stSql  ||'                                           
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON contrato.cod_contrato = contrato_pensionista_orgao.cod_contrato  

                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_sub_divisao
                                            , contrato_servidor_sub_divisao_funcao.timestamp
                                            , contrato_pensionista.cod_contrato
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista
                                            , pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_pensionista.cod_contrato_cedente
                                          AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp
                                      ) as contrato_pensionista_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_pensionista_sub_divisao_funcao.cod_contrato

                            LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor
                                   ON contrato_servidor.cod_contrato = contrato.cod_contrato

                            LEFT JOIN (SELECT contrato_servidor_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                          AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                          AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o''
                                          AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                                   ON contrato.cod_contrato = contrato_servidor_previdencia.cod_contrato                                                                                                     

                            LEFT JOIN (SELECT contrato_pensionista_previdencia.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                            , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            , (  SELECT cod_previdencia
                                                      , max(timestamp) as timestamp
                                                   FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_previdencia) as max_previdencia_previdencia 
                                        WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                          AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
                                          AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                          AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                          AND previdencia_previdencia.tipo_previdencia = ''o'') as contrato_pensionista_previdencia
                                   ON contrato.cod_contrato = contrato_pensionista_previdencia.cod_contrato                                                                                                                                                                           
                                   
                            LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON contrato.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON contrato.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   
                            LEFT JOIN (SELECT contrato_servidor_sub_divisao_funcao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                                        WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                                          AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp) as contrato_servidor_sub_divisao_funcao
                                   ON contrato.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato                                                                      
                                   
                                   ) as cadastro
                   WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                     AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                     AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                     AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                     AND evento_complementar_calculado.cod_evento = configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.cod_evento = max_configuracao_evento_despesa.cod_evento
                     AND configuracao_evento_despesa.timestamp = max_configuracao_evento_despesa.timestamp
                     AND registro_evento_complementar.cod_contrato = cadastro.cod_contrato
                     AND evento_complementar_calculado.cod_evento = evento.cod_evento
                     AND configuracao_evento_despesa.cod_configuracao = '|| inCodConfiguracao ||'
                     AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                     AND cadastro.situacao = '|| quote_literal(stCadastro) ||' '|| stFiltro||stFiltroInterno;

        IF stCodEvento IS NOT NULL THEN
            stSql := stSql ||' AND evento_complementar_calculado.cod_evento IN ('|| stCodEvento ||')';
        END IF;       
        IF inCodPrevidencia != 0 THEN
            stSql := stSql ||' AND cadastro.cod_previdencia = '|| inCodPrevidencia; 
        END IF;

        stSql := stSql || '                     
                GROUP BY configuracao_evento_despesa.cod_conta
                       , evento_complementar_calculado.cod_evento
                       , evento.codigo
                       , evento.descricao
                       , cadastro.cod_sub_divisao
                       , evento.natureza';
        IF inCountLotacao >= 1 OR inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_orgao';
        END IF;       
        IF inCountLocal >= 1 OR inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.cod_local';
        END IF;
        IF inCountAtributo >= 1 OR inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , cadastro.valor,cadastro.cod_atributo';
        END IF;                                     
    END IF;
    
    stFornecedor := selectIntoVarchar('SELECT descricao_item
                                         FROM folhapagamento'|| stEntidade ||'.configuracao_autorizacao_empenho
                                        WHERE cod_configuracao_autorizacao = '|| inCodConfiguracaoAutorizacao ||'
                                          AND exercicio = '|| quote_literal(reConfiguracao.exercicio)   ||'
                                          AND vigencia  = '|| quote_literal(reConfiguracao.dt_vigencia) ||'
                                          AND timestamp = '|| quote_literal(reConfiguracao.timestamp)   ||' ');
                                        
    stSql := stSql || ' ORDER BY cod_conta,lla';                                                                                    

    FOR reRegistro IN EXECUTE stSql LOOP
        
        stOrgao                 := '';
        stUnidade               := '';
        inCodDespesa            := ''; 
        stCodEstruturalOriginal := '';
        inNumPAO                := '';
        stLLA                   := '';

        --Busca configuraï¿½ï¿½o da aba eventos da configuraï¿½ï¿½o de autorizaï¿½ï¿½o de empenhos
        --nesta aba sï¿½o feitas as configuraï¿½ï¿½es de exceï¿½ï¿½o que deverï¿½o ser utilizadas
        --no lugar da rï¿½brica de despesa registrada atravï¿½z do cadastro do evento
        
        stSql := 'SELECT configuracao_empenho.*
                       , conta_despesa.cod_conta
                       , configuracao_empenho_cargo.cod_cargo
                    FROM folhapagamento'|| stEntidade ||'.configuracao_empenho
                       , folhapagamento'|| stEntidade ||'.configuracao_empenho_evento
                       , folhapagamento'|| stEntidade ||'.configuracao_empenho_situacao
                       , folhapagamento'|| stEntidade ||'.configuracao_empenho_subdivisao
                       LEFT  JOIN folhapagamento.configuracao_empenho_cargo
                        ON configuracao_empenho_cargo.cod_configuracao  = configuracao_empenho_subdivisao.cod_configuracao
                        AND configuracao_empenho_cargo.exercicio        = configuracao_empenho_subdivisao.exercicio
                        AND configuracao_empenho_cargo.sequencia        = configuracao_empenho_subdivisao.sequencia
                        AND configuracao_empenho_cargo.timestamp        = configuracao_empenho_subdivisao.timestamp
                        AND configuracao_empenho_cargo.cod_sub_divisao  = configuracao_empenho_subdivisao.cod_sub_divisao
                       , orcamento.despesa
                       , orcamento.conta_despesa';
                       
        IF inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || ' , folhapagamento'|| stEntidade ||'.configuracao_empenho_lotacao';
        END IF;       
        IF inCountLocalConfEvento >= 1 THEN
            stSql := stSql || ' , folhapagamento'|| stEntidade ||'.configuracao_empenho_local';
        END IF;
        IF inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || ' , folhapagamento'|| stEntidade ||'.configuracao_empenho_atributo
                                , folhapagamento'|| stEntidade ||'.configuracao_empenho_atributo_valor';
        END IF;                                
                       
        stSql := stSql || '
                   WHERE configuracao_empenho.cod_configuracao = configuracao_empenho_evento.cod_configuracao
                     AND configuracao_empenho.exercicio        = configuracao_empenho_evento.exercicio
                     AND configuracao_empenho.sequencia        = configuracao_empenho_evento.sequencia
                     AND configuracao_empenho.timestamp        = configuracao_empenho_evento.timestamp

                     AND configuracao_empenho.cod_configuracao = configuracao_empenho_subdivisao.cod_configuracao
                     AND configuracao_empenho.exercicio        = configuracao_empenho_subdivisao.exercicio
                     AND configuracao_empenho.sequencia        = configuracao_empenho_subdivisao.sequencia
                     AND configuracao_empenho.timestamp        = configuracao_empenho_subdivisao.timestamp

                     AND configuracao_empenho.cod_configuracao = configuracao_empenho_situacao.cod_configuracao
                     AND configuracao_empenho.exercicio        = configuracao_empenho_situacao.exercicio
                     AND configuracao_empenho.sequencia        = configuracao_empenho_situacao.sequencia                    
                     AND configuracao_empenho.timestamp        = configuracao_empenho_situacao.timestamp
                     
                     AND configuracao_empenho.cod_despesa = despesa.cod_despesa
                     AND configuracao_empenho.exercicio_despesa = despesa.exercicio
   
                     AND despesa.cod_conta = conta_despesa.cod_conta
                     AND despesa.exercicio = conta_despesa.exercicio
                     AND despesa.num_pao = configuracao_empenho.num_pao
                     AND despesa.exercicio = configuracao_empenho.exercicio_pao';                     
        IF inCountLotacaoConfEvento >= 1 THEN
            stSql := stSql || '
                        AND configuracao_empenho.cod_configuracao  = configuracao_empenho_lotacao.cod_configuracao
                        AND configuracao_empenho.exercicio         = configuracao_empenho_lotacao.exercicio
                        AND configuracao_empenho.sequencia         = configuracao_empenho_lotacao.sequencia
                        AND configuracao_empenho.timestamp         = configuracao_empenho_lotacao.timestamp
                        AND configuracao_empenho_lotacao.cod_orgao = '|| reRegistro.cod_orgao;
        END IF;
        IF inCountLocalConfEvento >= 1 THEN
            IF reRegistro.cod_local IS NOT NULL THEN
                stSql := stSql || '
                            AND configuracao_empenho.cod_configuracao = configuracao_empenho_local.cod_configuracao
                            AND configuracao_empenho.exercicio        = configuracao_empenho_local.exercicio
                            AND configuracao_empenho.sequencia        = configuracao_empenho_local.sequencia
                            AND configuracao_empenho.timestamp        = configuracao_empenho_local.timestamp
                            AND configuracao_empenho_local.cod_local  = '|| reRegistro.cod_local;
            END IF;
        END IF;
        IF inCountAtributoConfEvento >= 1 THEN
            stSql := stSql || '
                        AND configuracao_empenho.cod_configuracao          = configuracao_empenho_atributo.cod_configuracao
                        AND configuracao_empenho.exercicio                 = configuracao_empenho_atributo.exercicio
                        AND configuracao_empenho.sequencia                 = configuracao_empenho_atributo.sequencia
                        AND configuracao_empenho.timestamp                 = configuracao_empenho_atributo.timestamp
                        AND configuracao_empenho_atributo.exercicio        = configuracao_empenho_atributo_valor.exercicio  
                        AND configuracao_empenho_atributo.cod_evento       = configuracao_empenho_atributo_valor.cod_evento
                        AND configuracao_empenho_atributo.cod_configuracao = configuracao_empenho_atributo_valor.cod_configuracao
                        AND configuracao_empenho_atributo.sequencia        = configuracao_empenho_atributo_valor.sequencia
                        AND configuracao_empenho_atributo.timestamp        = configuracao_empenho_atributo_valor.timestamp
                        AND configuracao_empenho_atributo.cod_atributo     = configuracao_empenho_atributo_valor.cod_atributo
                        AND configuracao_empenho_atributo.cod_modulo       = configuracao_empenho_atributo_valor.cod_modulo
                        AND configuracao_empenho_atributo.cod_cadastro     = configuracao_empenho_atributo_valor.cod_cadastro
                        AND configuracao_empenho_atributo.cod_atributo     = '|| reRegistro.cod_atributo ||'
                        AND configuracao_empenho_atributo_valor.valor      = '|| quote_literal(reRegistro.valor) ||'
                        ';
        END IF;
        stSql := stSql || '
                     AND configuracao_empenho.cod_configuracao           = '|| inCodConfiguracao            ||'
                     AND configuracao_empenho_evento.cod_evento          = '|| reRegistro.cod_evento        ||'
                     AND configuracao_empenho_situacao.situacao          = '|| quote_literal(stCadastro)    ||'                     
                     AND configuracao_empenho_subdivisao.cod_sub_divisao = '|| reRegistro.cod_sub_divisao   ||'
                     AND configuracao_empenho_cargo.cod_cargo            = '|| reRegistro.cod_cargo   ||'
                     AND configuracao_empenho.exercicio                  = '|| quote_literal(reConfiguracao.exercicio)   ||'
                     AND configuracao_empenho.vigencia                   = '|| quote_literal(reConfiguracao.dt_vigencia) ||'
                     AND configuracao_empenho.timestamp                  = '|| quote_literal(reConfiguracao.timestamp)   ||' ';                     
                     
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reConfiguracaoPorEvento;
        CLOSE crCursor;        

        --Verifica se o recordsetr reConfiguracaoPorEvento estï¿½ nulo
        --no caso de nulo quer disser que nï¿½o hï¿½ configuraï¿½ï¿½o especï¿½fica por evento
        --passando os parametros de filtro definidos na consulta a cima
        --Entï¿½o deverï¿½ ser pego os dados de configuraï¿½ï¿½o geral do evento.
        IF reConfiguracaoPorEvento.cod_configuracao IS NULL THEN
            inCodConta         := reRegistro.cod_conta;
            stExercicioDespesa := reConfiguracao.exercicio;
            IF inCountLotacao >= 1 THEN
                inNumPAO := selectIntoInteger('SELECT num_pao
                                                 FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_lotacao
                                                WHERE cod_orgao = '|| reRegistro.cod_orgao ||'
                                                  AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                  AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
            END IF;
            IF inCountLocal >= 1 THEN
                IF reRegistro.cod_local IS NOT NULL THEN
                    inNumPAO := selectIntoInteger('SELECT num_pao                           
                                                     FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_local
                                                    WHERE cod_local = '|| reRegistro.cod_local ||'
                                                      AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                      AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                END IF;
            END IF;             
            IF inCountAtributo >= 1 THEN
                IF reRegistro.valor IS NOT NULL THEN
                    inNumPAO := selectIntoInteger('SELECT num_pao
                                                     FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo_valor                           
                                                    WHERE cod_atributo = '|| reRegistro.cod_atributo ||'
                                                      AND valor     = '|| quote_literal(reRegistro.valor) ||'
                                                      AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                      AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                END IF;
            END IF;    
        ELSE
            inCodConta         := selectIntoInteger('SELECT cod_conta
                                                       FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_conta_despesa
                                                      WHERE cod_configuracao = '|| reConfiguracaoPorEvento.cod_configuracao ||'
                                                        AND sequencia = '|| reConfiguracaoPorEvento.sequencia ||'
                                                        AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                        AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                                                        
            stExercicioDespesa := reConfiguracaoPorEvento.exercicio;
            inNumPAO           := reConfiguracaoPorEvento.num_pao;
        END IF;

        stSql := 'SELECT cod_estrutural
                    FROM orcamento.conta_despesa
                   WHERE cod_conta = '|| inCodConta ||'
                     AND exercicio = '|| quote_literal(stExercicioDespesa) ||' '; 
        stCodEstrutural         := selectIntoVarchar(stSql);
        stCodEstruturalOriginal := stCodEstrutural;
        
        IF inNumPAO IS NOT NULL AND inNumPAO != '' THEN
            stSql := 'SELECT cod_despesa 
                        FROM orcamento.despesa
                           , orcamento.conta_despesa
                       WHERE despesa.cod_conta = conta_despesa.cod_conta
                         AND despesa.exercicio = conta_despesa.exercicio                     
                         AND despesa.num_pao = '|| inNumPAO ||'
                         AND despesa.cod_conta = '|| inCodConta ||'
                         AND despesa.exercicio = '|| quote_literal(stExercicioDespesa) ||' '; 
            inCodDespesa := selectIntoInteger(stSql);
            IF inCodDespesa IS NULL THEN
                
                WHILE inCodDespesa IS NULL AND stCodEstrutural != stCodEstruturalUltimo LOOP
                    stCodEstruturalUltimo := stCodEstrutural;                    
                    stCodEstrutural := fn_conta_mae(stCodEstrutural);
                    stCodEstrutural := publico.fn_mascara_completa(stMascaraDespesa,stCodEstrutural);
                    stSql := 'SELECT cod_despesa
                                FROM orcamento.despesa
                                   , orcamento.conta_despesa
                               WHERE despesa.cod_conta = conta_despesa.cod_conta
                                 AND despesa.exercicio = conta_despesa.exercicio                     
                                 AND despesa.num_pao = '|| inNumPAO ||'
                                 AND conta_despesa.cod_estrutural = '|| quote_literal(stCodEstrutural) ||'
                                 AND despesa.exercicio = '|| quote_literal(stExercicioDespesa) ||' ';
                    inCodDespesa := selectIntoInteger(stSql);
                END LOOP;
                --stCodEstruturalOriginal := stCodEstrutural;
            END IF;
            
            IF inCodDespesa IS NOT NULL THEN               
                stSql := 'SELECT despesa.num_orgao
                               , orgao.nom_orgao
                               , unidade.nom_unidade
                               , despesa.num_unidade
                            FROM orcamento.despesa
                               , orcamento.unidade
                               , orcamento.orgao
                           WHERE despesa.exercicio = unidade.exercicio
                             AND despesa.num_unidade = unidade.num_unidade
                             AND despesa.num_orgao = unidade.num_orgao
                             AND unidade.exercicio = orgao.exercicio
                             AND unidade.num_orgao = orgao.num_orgao
                             AND cod_despesa = '|| inCodDespesa ||'
                             AND despesa.exercicio = '|| quote_literal(stExercicio) ||'
                             AND num_pao = '|| inNumPAO;

                OPEN crDespesa FOR EXECUTE stSql;
                    FETCH crDespesa INTO reDespesa;
                CLOSE crDespesa;

                stOrgao   := reDespesa.num_orgao ||'-'|| reDespesa.nom_orgao;
                stUnidade := reDespesa.num_unidade ||'-'|| reDespesa.nom_unidade;
            END IF;
        END IF;
        
        IF nuAliquotaPrevidencia IS NOT NULL THEN
            IF inCodConfiguracao = 1 THEN
                nuDescontoPrevidencia := selectIntoNumeric('
                    SELECT sum(evento_calculado.valor) as valor
                      FROM folhapagamento'|| stEntidade ||'.evento_calculado
                         , (SELECT registro_evento_periodo.*
                                 , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                   ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao
                                 , contrato_servidor_local.cod_local
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON registro_evento_periodo.cod_contrato = contrato_pensionista_orgao.cod_contrato                                
                         LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON registro_evento_periodo.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON registro_evento_periodo.cod_contrato = contrato_servidor_orgao.cod_contrato                              
                              ) as cadastro
                 LEFT JOIN (SELECT contrato_servidor_previdencia.*
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                 , (  SELECT cod_contrato
                                           , max(timestamp) as timestamp
                                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                 , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                 , (  SELECT cod_previdencia
                                           , max(timestamp) as timestamp
                                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_previdencia) as max_previdencia_previdencia
                             WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                               AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                               AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                               AND previdencia_previdencia.tipo_previdencia = ''o''
                               AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                        ON cadastro.cod_contrato = contrato_servidor_previdencia.cod_contrato         
                         , (SELECT cod_contrato
                                 , CASE contrato_servidor.ativo
                                                          WHEN TRUE THEN ''a''
                                                          WHEN FALSE THEN ''o''
                                                          WHEN NULL THEN ''p''
                                                           END as situacao     
                              FROM pessoal'|| stEntidade ||'.contrato_servidor) AS contrato_servidor
                     WHERE evento_calculado.cod_registro = cadastro.cod_registro
                       AND cadastro.cod_contrato = contrato_servidor.cod_contrato
                       AND cadastro.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                       AND evento_calculado.cod_evento = '|| inCodEventoDescontoPrevidencia ||'
                       AND situacao = '|| quote_literal(stCadastro) ||'
                       AND contrato_servidor_previdencia.cod_previdencia = '|| inCodPrevidencia||stFiltro);
            END IF;
            
            IF inCodConfiguracao = 2 THEN
                nuDescontoPrevidencia := selectIntoNumeric('
                    SELECT sum(evento_ferias_calculado.valor) as valor
                      FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                         , (SELECT registro_evento_ferias.*
                                 , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                   ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao
                                  , contrato_servidor_local.cod_local
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON registro_evento_ferias.cod_contrato = contrato_pensionista_orgao.cod_contrato                                
                         LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON registro_evento_ferias.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON registro_evento_ferias.cod_contrato = contrato_servidor_orgao.cod_contrato                              
                              ) as cadastro
                 LEFT JOIN (SELECT contrato_servidor_previdencia.*
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                 , (  SELECT cod_contrato
                                           , max(timestamp) as timestamp
                                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                 , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                 , (  SELECT cod_previdencia
                                           , max(timestamp) as timestamp
                                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_previdencia) as max_previdencia_previdencia
                             WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                               AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                               AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                               AND previdencia_previdencia.tipo_previdencia = ''o''
                               AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                        ON cadastro.cod_contrato = contrato_servidor_previdencia.cod_contrato         
                         , (SELECT cod_contrato
                                 , CASE contrato_servidor.ativo
                                                          WHEN TRUE THEN ''a''
                                                          WHEN FALSE THEN ''o''
                                                          WHEN NULL THEN ''p''
                                                           END as situacao     
                              FROM pessoal'|| stEntidade ||'.contrato_servidor) AS contrato_servidor
                     WHERE evento_ferias_calculado.cod_registro = cadastro.cod_registro
                       AND evento_ferias_calculado.cod_evento = cadastro.cod_evento
                       AND evento_ferias_calculado.desdobramento = cadastro.desdobramento
                       AND evento_ferias_calculado.timestamp = cadastro.timestamp_registro
                       AND cadastro.cod_contrato = contrato_servidor.cod_contrato
                       AND cadastro.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                       AND evento_calculado.cod_evento = '|| inCodEventoDescontoPrevidencia ||'
                       AND situacao = '|| quote_literal(stCadastro) ||'
                       AND contrato_servidor_previdencia.cod_previdencia = '|| inCodPrevidencia||stFiltro);
            END IF;           
            IF inCodConfiguracao = 3 THEN
                nuDescontoPrevidencia := selectIntoNumeric('
                    SELECT sum(evento_decimo_calculado.valor) as valor
                      FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                         , (SELECT registro_evento_decimo.*
                                 , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                   ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao
                                 , contrato_servidor_local.cod_local
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON registro_evento_decimo.cod_contrato = contrato_pensionista_orgao.cod_contrato                                
                         LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON registro_evento_decimo.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON registro_evento_decimo.cod_contrato = contrato_servidor_orgao.cod_contrato                              
                              ) as cadastro
                 LEFT JOIN (SELECT contrato_servidor_previdencia.*
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                 , (  SELECT cod_contrato
                                           , max(timestamp) as timestamp
                                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                 , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                 , (  SELECT cod_previdencia
                                           , max(timestamp) as timestamp
                                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_previdencia) as max_previdencia_previdencia
                             WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                               AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                               AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                               AND previdencia_previdencia.tipo_previdencia = ''o''
                               AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                        ON cadastro.cod_contrato = contrato_servidor_previdencia.cod_contrato         
                         , (SELECT cod_contrato
                                 , CASE contrato_servidor.ativo
                                                          WHEN TRUE THEN ''a''
                                                          WHEN FALSE THEN ''o''
                                                          WHEN NULL THEN ''p''
                                                           END as situacao     
                              FROM pessoal'|| stEntidade ||'.contrato_servidor) AS contrato_servidor
                     WHERE evento_decimo_calculado.cod_registro = cadastro.cod_registro
                       AND evento_decimo_calculado.cod_evento = cadastro.cod_evento
                       AND evento_decimo_calculado.desdobramento = cadastro.desdobramento
                       AND evento_decimo_calculado.timestamp = cadastro.timestamp_registro
                       AND cadastro.cod_contrato = contrato_servidor.cod_contrato
                       AND cadastro.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                       AND evento_calculado.cod_evento = '|| inCodEventoDescontoPrevidencia ||'
                       AND situacao = '|| quote_literal(stCadastro) ||'
                       AND contrato_servidor_previdencia.cod_previdencia = '|| inCodPrevidencia||stFiltro);
            END IF;
            IF inCodConfiguracao = 4 THEN
                nuDescontoPrevidencia := selectIntoNumeric('
                    SELECT sum(evento_rescisao_calculado.valor) as valor
                      FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                         , (SELECT registro_evento_rescisao.*
                                 , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                   ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao
                                 , contrato_servidor_local.cod_local
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON registro_evento_rescisao.cod_contrato = contrato_pensionista_orgao.cod_contrato                                
                         LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON registro_evento_rescisao.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON registro_evento_rescisao.cod_contrato = contrato_servidor_orgao.cod_contrato                              
                              ) as cadastro
                 LEFT JOIN (SELECT contrato_servidor_previdencia.*
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                 , (  SELECT cod_contrato
                                           , max(timestamp) as timestamp
                                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                 , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                 , (  SELECT cod_previdencia
                                           , max(timestamp) as timestamp
                                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_previdencia) as max_previdencia_previdencia
                             WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                               AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                               AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                               AND previdencia_previdencia.tipo_previdencia = ''o''
                               AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                        ON cadastro.cod_contrato = contrato_servidor_previdencia.cod_contrato         
                         , (SELECT cod_contrato
                                 , CASE contrato_servidor.ativo
                                                          WHEN TRUE THEN ''a''
                                                          WHEN FALSE THEN ''o''
                                                          WHEN NULL THEN ''p''
                                                           END as situacao     
                              FROM pessoal'|| stEntidade ||'.contrato_servidor) AS contrato_servidor
                     WHERE evento_rescisao_calculado.cod_registro = cadastro.cod_registro
                       AND evento_rescisao_calculado.cod_evento = cadastro.cod_evento
                       AND evento_rescisao_calculado.desdobramento = cadastro.desdobramento
                       AND evento_rescisao_calculado.timestamp = cadastro.timestamp_registro
                       AND cadastro.cod_contrato = contrato_servidor.cod_contrato
                       AND cadastro.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                       AND evento_calculado.cod_evento = '|| inCodEventoDescontoPrevidencia ||'
                       AND situacao = '|| quote_literal(stCadastro) ||'
                       AND contrato_servidor_previdencia.cod_previdencia = '|| inCodPrevidencia||stFiltro);
            END IF;
            IF inCodConfiguracao = 0 THEN
                nuDescontoPrevidencia := selectIntoNumeric('
                    SELECT sum(evento_complementar_calculado.valor) as valor
                      FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                         , (SELECT registro_evento_complementar.*
                                 , CASE WHEN contrato_servidor_orgao.cod_contrato IS NOT NULL THEN contrato_servidor_orgao.cod_orgao
                                   ELSE contrato_pensionista_orgao.cod_orgao END as cod_orgao
                                 , contrato_servidor_local.cod_local
                              FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                            LEFT JOIN (SELECT contrato_pensionista_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                                        WHERE contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                                          AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp) as contrato_pensionista_orgao
                                   ON registro_evento_complementar.cod_contrato = contrato_pensionista_orgao.cod_contrato                                
                         LEFT JOIN (SELECT contrato_servidor_local.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_local
                                        WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                          AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) as contrato_servidor_local
                                   ON registro_evento_complementar.cod_contrato = contrato_servidor_local.cod_contrato                                   
                                   
                            LEFT JOIN (SELECT contrato_servidor_orgao.*
                                         FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                            , (  SELECT cod_contrato
                                                      , max(timestamp) as timestamp
                                                   FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                                  WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                               GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                        WHERE contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                                          AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp) as contrato_servidor_orgao
                                   ON registro_evento_complementar.cod_contrato = contrato_servidor_orgao.cod_contrato                              
                              ) as cadastro
                 LEFT JOIN (SELECT contrato_servidor_previdencia.*
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                 , (  SELECT cod_contrato
                                           , max(timestamp) as timestamp
                                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                 , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                 , (  SELECT cod_previdencia
                                           , max(timestamp) as timestamp
                                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                    GROUP BY cod_previdencia) as max_previdencia_previdencia
                             WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                               AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                               AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                               AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                               AND previdencia_previdencia.tipo_previdencia = ''o''
                               AND contrato_servidor_previdencia.bo_excluido = false) as contrato_servidor_previdencia
                        ON cadastro.cod_contrato = contrato_servidor_previdencia.cod_contrato         
                         , (SELECT cod_contrato
                                 , CASE contrato_servidor.ativo
                                                          WHEN TRUE THEN ''a''
                                                          WHEN FALSE THEN ''o''
                                                          WHEN NULL THEN ''p''
                                                           END as situacao     
                              FROM pessoal'|| stEntidade ||'.contrato_servidor) AS contrato_servidor
                     WHERE evento_complementar_calculado.cod_registro = cadastro.cod_registro
                       AND evento_complementar_calculado.cod_evento = cadastro.cod_evento
                       AND evento_complementar_calculado.cod_configuracao = cadastro.cod_configuracao
                       AND evento_complementar_calculado.timestamp = cadastro.timestamp_registro
                       AND cadastro.cod_contrato = contrato_servidor.cod_contrato
                       AND cadastro.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                       AND evento_calculado.cod_evento = '|| inCodEventoDescontoPrevidencia ||'
                       AND situacao = '|| quote_literal(stCadastro) ||'
                       AND contrato_servidor_previdencia.cod_previdencia = '|| inCodPrevidencia||stFiltro);
            END IF;
            reRegistro.soma := (reRegistro.soma * (nuAliquotaPrevidencia+COALESCE(nuAliquotaRat, 0)) / 100);
        END IF;    
                
        IF stOrgao IS NULL THEN
            stOrgao := '';
        END IF;
        IF stUnidade IS NULL THEN
            stUnidade := '';
        END IF;
        IF inCodDespesa IS NULL THEN
            inCodDespesa := '';
        END IF;        
        IF stCodEstruturalOriginal IS NULL THEN
            stCodEstruturalOriginal := '';
        END IF; 
        IF inNumPAO IS NULL THEN
            inNumPAO := '';
        END IF;    
        IF stFornecedor IS NULL THEN
            stFornecedor := '';
        END IF;                 
        
        stComparacao1  := trim(stOrgao) ||'#'|| trim(stUnidade) ||'#'|| trim(inCodDespesa) ||'#'|| trim(stCodEstruturalOriginal) ||'#'|| trim(inNumPAO) ||'#'|| trim(reRegistro.lla) ||'#'|| trim(stFornecedor);
        IF stComparacao1 != stComparacao2 AND stComparacao2 != '' THEN       
            boInserir := TRUE;
        END IF;        
        IF boInserir IS TRUE THEN
            arComparacao := string_to_array(stComparacao2,'#'); 
                        
            rwColunasResumo.orgao              := arComparacao[1];
            rwColunasResumo.unidade            := arComparacao[2];
            rwColunasResumo.red_dotacao        := arComparacao[3]; 
            rwColunasResumo.rubrica_despesa    := arComparacao[4];
            rwColunasResumo.num_pao            := arComparacao[5];
            rwColunasResumo.lla                := arComparacao[6];
            rwColunasResumo.fornecedor         := arComparacao[7];                        
            rwColunasResumo.evento             := stEventos;
            rwColunasResumo.valor              := nuSomaEventos;              
            SELECT nom_pao
              INTO rwColunasResumo.desc_pao
              FROM orcamento.pao
             WHERE num_pao::varchar = rwColunasResumo.num_pao
               AND exercicio = stExercicio;
            IF arComparacao[3] != '' THEN
                rwColunasResumo.saldo_dotacao  := to_real(empenho.fn_saldo_dotacao(stExercicio,arComparacao[3]::integer));
            ELSE
                rwColunasResumo.saldo_dotacao   := null;
            END IF;

            stEventos     := '';
            nuSomaEventos := 0.00;
            boInserir     := FALSE;
            RETURN NEXT rwColunasResumo;                    
        END IF;

        stComparacao2 := trim(stOrgao) ||'#'|| trim(stUnidade) ||'#'|| trim(inCodDespesa) ||'#'|| trim(stCodEstruturalOriginal) ||'#'|| trim(inNumPAO) ||'#'|| trim(reRegistro.lla) ||'#'|| trim(stFornecedor);        
        stEventos     := replace(stEventos, trim(reRegistro.evento) || ' ', '');
        stEventos     := stEventos || trim(reRegistro.evento) || ' ';
        
        IF reRegistro.natureza = 'D' THEN --se for desconto
            nuSomaEventos := nuSomaEventos - reRegistro.soma;
        ELSE
            nuSomaEventos := nuSomaEventos + reRegistro.soma;
        END IF;
    END LOOP;

    arComparacao := string_to_array(stComparacao2,'#');
    rwColunasResumo.orgao              := arComparacao[1];
    rwColunasResumo.unidade            := arComparacao[2];
    rwColunasResumo.red_dotacao        := arComparacao[3]; 
    rwColunasResumo.rubrica_despesa    := arComparacao[4];
    rwColunasResumo.num_pao            := arComparacao[5];
    rwColunasResumo.lla                := arComparacao[6];
    rwColunasResumo.fornecedor         := arComparacao[7];            
    rwColunasResumo.evento             := stEventos;
    rwColunasResumo.valor              := nuSomaEventos;
    
    SELECT nom_pao
      INTO rwColunasResumo.desc_pao
      FROM orcamento.pao
     WHERE num_pao::varchar = rwColunasResumo.num_pao
       AND exercicio = stExercicio;
    IF arComparacao[3] != '' THEN
        rwColunasResumo.saldo_dotacao   := to_real(empenho.fn_saldo_dotacao(stExercicio,arComparacao[3]::integer));
    ELSE
        rwColunasResumo.saldo_dotacao   := null;
    END IF;

    RETURN NEXT rwColunasResumo;                   
    RETURN;
END
$$ LANGUAGE 'plpgsql';

                                                

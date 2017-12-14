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
    * $Id: exportacaoFolhaPagamento.plsql 66252 2016-08-01 20:37:09Z michel $
*/
CREATE OR REPLACE FUNCTION tcers.imaContaBancoBanrisul( stEntidade varchar, banco varchar, codOrgao integer) RETURNS SETOF tipoBanco as $$
DECLARE
  reRegistros tipoBanco%ROWTYPE;
  stSql       varchar;
BEGIN
    stSql := '
    SELECT
                num_banco as banco
              , replace(num_agencia, ''-'', '''') as agencia
              , replace(num_conta_corrente,''-'','''') as corrente
          FROM ima.configuracao_banrisul_conta
    INNER JOIN ( SELECT  configuracao_banrisul_conta.cod_banco
                       , configuracao_banrisul_conta.cod_agencia
                       , configuracao_banrisul_conta.cod_conta_corrente
                       , max(configuracao_banrisul_conta.timestamp) as max_timestamp
                    FROM ima.configuracao_banrisul_conta
              INNER JOIN ima.configuracao_banrisul_orgao
                      ON configuracao_banrisul_orgao.cod_convenio = configuracao_banrisul_conta.cod_convenio
                     AND configuracao_banrisul_orgao.cod_banco = configuracao_banrisul_conta.cod_banco
                     AND configuracao_banrisul_orgao.cod_agencia = configuracao_banrisul_conta.cod_agencia
                     AND configuracao_banrisul_orgao.cod_conta_corrente = configuracao_banrisul_conta.cod_conta_corrente
                   WHERE cod_orgao = '|| codOrgao ||'
                GROUP BY configuracao_banrisul_conta.cod_banco
                       , configuracao_banrisul_conta.cod_agencia
                       , configuracao_banrisul_conta.cod_conta_corrente
                ORDER BY max_timestamp desc
                 LIMIT 1
               ) as max_configuracao
            ON max_configuracao.cod_banco          = configuracao_banrisul_conta.cod_banco
           AND max_configuracao.cod_agencia        = configuracao_banrisul_conta.cod_agencia
           AND max_configuracao.cod_conta_corrente = configuracao_banrisul_conta.cod_conta_corrente
           AND max_configuracao.max_timestamp      = configuracao_banrisul_conta.timestamp
    INNER JOIN ima.configuracao_banrisul_orgao
            ON configuracao_banrisul_orgao.cod_convenio = configuracao_banrisul_conta.cod_convenio
           AND configuracao_banrisul_orgao.cod_banco = configuracao_banrisul_conta.cod_banco
           AND configuracao_banrisul_orgao.cod_agencia = configuracao_banrisul_conta.cod_agencia
           AND configuracao_banrisul_orgao.cod_conta_corrente = configuracao_banrisul_conta.cod_conta_corrente
           AND configuracao_banrisul_orgao.timestamp = configuracao_banrisul_conta.timestamp
    INNER JOIN monetario.banco
            ON banco.cod_banco = configuracao_banrisul_conta.cod_banco
    INNER JOIN monetario.agencia
            ON agencia.cod_banco = configuracao_banrisul_conta.cod_banco
           AND agencia.cod_agencia = configuracao_banrisul_conta.cod_agencia
    INNER JOIN monetario.conta_corrente
            ON conta_corrente.cod_banco = configuracao_banrisul_conta.cod_banco
           AND conta_corrente.cod_agencia = configuracao_banrisul_conta.cod_agencia
           AND conta_corrente.cod_conta_corrente = configuracao_banrisul_conta.cod_conta_corrente
    INNER JOIN ima.configuracao_convenio_banrisul
            ON configuracao_convenio_banrisul.cod_banco = banco.cod_banco
         WHERE num_banco = '|| quote_literal(banco) ||'
           AND cod_orgao = '|| codOrgao ||'
    ';

    FOR reRegistros IN EXECUTE stSql LOOP
        RETURN NEXT reRegistros;
    END LOOP;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION tcers.imaContaBanco( stEntidade varchar, banco varchar, codOrgao integer ) RETURNS SETOF tipoBanco as $$
DECLARE
  reRegistros tipoBanco%ROWTYPE;
  stSql         varchar;
  stBancoTabela varchar;
  inTipo        integer;
  stEnt         varchar;
BEGIN
    IF stEntidade IS NULL THEN
        stEnt := '';
    ELSE 
        stEnt := stEntidade;
    END IF;

    IF banco = '1000' THEN --banco do Brasil
        stBancoTabela := 'bb';
        inTipo        := 1;
    END IF;

    IF banco = '027' THEN --bESC
        stBancoTabela := 'besc';
        inTipo        := 1;
    END IF;

    IF banco = '041' THEN --banrisul
        stBancoTabela := 'banrisul';
        inTipo        := 2;
    END IF;

    IF banco = '1001' THEN --bradesco
        stBancoTabela := 'bradesco';
        inTipo        := 2;
    END IF;

    IF banco = '1002' THEN --caixa economica federal
       stBancoTabela := 'caixa_economica_federal';
       inTipo        := 2; 
    END IF;


    IF inTipo = 1 THEN
        stSql := '
            SELECT 
                 --    banco.cod_banco
                 --  , agencia.cod_agencia
                 --  , conta_corretne.cod_conta_corrente
                    num_banco as cod_banco
                   , num_agencia as cod_agencia
                   , num_conta_corrente as cod_conta_corrente
              FROM ima'|| stEnt ||'.configuracao_convenio_'|| stBancoTabela ||'
         LEFT JOIN ima'|| stEnt ||'.configuracao_'|| stBancoTabela ||'_conta
             USING (cod_convenio, cod_banco)
         LEFT JOIN ima'|| stEnt ||'.configuracao_'|| stBancoTabela ||'_orgao
             USING (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente)
         LEFT JOIN monetario.conta_corrente
             USING (cod_banco, cod_agencia, cod_conta_corrente)
         LEFT JOIN monetario.banco
             USING (cod_banco)
         LEFT JOIN monetario.agencia
             USING (cod_agencia,cod_banco)
             WHERE banco.cod_banco = '|| banco ||'
               AND cod_orgao = '|| codOrgao ||'
        ';
    ELSE
        stSql := '
               SELECT   
                      --  banco.cod_banco
                      --, agencia.cod_agencia
                      --, conta_corrente.cod_conta_corrente 
                       num_banco as cod_banco
                      , num_agencia as cod_agencia
                      , num_conta_corrente as cod_conta_corrente
                 FROM ima'|| stEnt ||'.configuracao_convenio_'|| stBancoTabela ||'
            LEFT JOIN monetario.banco
                USING (cod_banco)
            LEFT JOIN monetario.agencia
                USING (cod_banco)
            LEFT JOIN monetario.conta_corrente
                USING (cod_banco,cod_agencia)
                WHERE cod_banco = '|| banco ||'
         ';
    END IF;

    IF stSql IS NULL THEN
        stSql := 'SELECT '''' as cod_banco, '''' as cod_agencia, '''' as cod_conta_corrente';
    END IF;

    FOR reRegistros IN EXECUTE stSql LOOP
        RETURN NEXT reRegistros ;
    END LOOP;      
END;
$$LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION tcers.incideIRRF(codEvento integer) RETURNS VARCHAR AS $$
DECLARE
    tabela INTEGER;
BEGIN
    SELECT tabela_irrf_evento.cod_tabela INTO tabela
      FROM folhapagamento.tabela_irrf_evento 
         , (SELECT cod_tabela 
             , max(timestamp) as timestamp 
          FROM folhapagamento.tabela_irrf_evento 
         GROUP BY cod_tabela) as max_tabela_irrf_evento 
     WHERE tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela 
       AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp 
       AND tabela_irrf_evento.cod_evento = codEvento;

    IF tabela IS NULL THEN
        RETURN 'N';
    ELSE
        RETURN 'S';
    END IF;
END;
$$LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION tcers.exportacaoFolhaPagamento(stEntidade varchar, dtInicio varchar, dtFinal varchar) RETURNS setof
colunasExportaFolhaPagamento as $$
DECLARE
    stSql             VARCHAR;
    reContratos       RECORD;
    stSalarioSql      VARCHAR;
    stComplementarSql VARCHAR;
    stDecimoSql       VARCHAR;
    stFeriasSql       VARCHAR;
    stRescisao        VARCHAR;
    stSql2            VARCHAR;
    stFiltro          VARCHAR;
    reSalarios        RECORD;
    reComplementar    RECORD;
    reDecimo          RECORD;
    reFerias          RECORD;
    reRescisao        RECORD;
    reRegistros       colunasExportaFolhaPagamento%ROWTYPE;
BEGIN
    stSalarioSql := '
             SELECT replace(evento_calculado.valor::VARCHAR,''.'','''')::NUMERIC AS valor
                  , evento.descricao as nom_evento
                  , SUBSTR(evento.codigo, 2) AS codigo
                  , CASE 
                        WHEN evento.natureza = ''P'' THEN ''V''
                        WHEN evento.natureza = ''I'' THEN ''O''
                        WHEN evento.natureza = ''B'' THEN ''T''
                        WHEN evento.natureza = ''D'' THEN ''D''
                    END as natureza
                  , CASE 
                        WHEN evento.natureza = ''B'' THEN ''Evento de Base''
                        WHEN evento.natureza = ''I'' THEN ''Evento Informativo''
                    END AS observacao
                  , tcers.incideIRRF(evento.cod_evento) as Irrf

               FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
         INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                 ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao
         INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento
                 ON registro_evento.cod_registro  = registro_evento_periodo.cod_registro
         INNER JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento
                 ON registro_evento.cod_registro = ultimo_registro_evento.cod_registro
                AND registro_evento.cod_evento   = ultimo_registro_evento.cod_evento
                AND registro_evento.timestamp    = ultimo_registro_evento.timestamp
         INNER JOIN folhapagamento'|| stEntidade ||'.evento_calculado
                 ON ultimo_registro_evento.cod_registro  = evento_calculado.cod_registro
                AND ultimo_registro_evento.cod_evento    = evento_calculado.cod_evento
                AND ultimo_registro_evento.timestamp     = evento_calculado.timestamp_registro
         INNER JOIN folhapagamento'|| stEntidade ||'.evento
                 ON evento_calculado.cod_evento = evento.cod_evento
              WHERE periodo_movimentacao.dt_final between to_date('|| quote_literal(dtInicio) ||', ''yyyy-mm-dd'') AND to_date('|| quote_literal(dtFinal) ||', ''yyyy-mm-dd'')
    ';

    stComplementarSql := '
                SELECT replace(evento_complementar_calculado.valor::VARCHAR,''.'','''')::NUMERIC  AS valor
                     , evento.descricao as nom_evento
                     , SUBSTR(evento.codigo, 2) AS codigo
                     , CASE
                           WHEN evento.natureza = ''P'' THEN ''V''
                           WHEN evento.natureza = ''I'' THEN ''O''
                           WHEN evento.natureza = ''B'' THEN ''O''
                           WHEN evento.natureza = ''D'' THEN ''D''
                       END as natureza
                     , CASE
                           WHEN evento.natureza = ''B'' THEN ''Evento de Base''
                           WHEN evento.natureza = ''I'' THEN ''Evento Informativo''
                       END AS observacao
                     , tcers.incideIRRF(evento.cod_evento) as Irrf
                     , '''' as cod_tipo_folha
                  FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
            INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_complementar
                    ON registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro
                   AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento
                   AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                   AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro
            INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                    ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_complementar.cod_periodo_movimentacao
            INNER JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
                    ON registro_evento_complementar.cod_registro     = ultimo_registro_evento_complementar.cod_registro
                   AND registro_evento_complementar.cod_evento       = ultimo_registro_evento_complementar.cod_evento
                   AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
                   AND registro_evento_complementar.timestamp        = ultimo_registro_evento_complementar.timestamp
            INNER JOIN folhapagamento'|| stEntidade ||'.evento
                    ON evento_complementar_calculado.cod_evento      = evento.cod_evento
                 WHERE periodo_movimentacao.dt_final between to_date('|| quote_literal(dtInicio) ||', ''yyyy-mm-dd'') AND to_date('|| quote_literal(dtFinal) ||', ''yyyy-mm-dd'')
            ';

    stDecimoSql := '
                SELECT replace(evento_decimo_calculado.valor::VARCHAR,''.'','''')::NUMERIC  AS valor
  	             , CASE
                            WHEN evento.natureza = ''P'' THEN ''V''
                            WHEN evento.natureza = ''I'' THEN ''O''
                            WHEN evento.natureza = ''B'' THEN ''O''
                            WHEN evento.natureza = ''D'' THEN ''D''
                       END as natureza
                     , CASE 
                            WHEN evento.natureza = ''B'' THEN ''Evento de Base''
                            WHEN evento.natureza = ''I'' THEN ''Evento Informativo''
                       END AS observacao
                     , tcers.incideIRRF(evento.cod_evento) as Irrf
                     , SUBSTR(evento.codigo, 2) AS codigo
                     , evento.descricao as nom_evento
                     , '''' as cod_tipo_folha
                  FROM folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
            INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_decimo
                    ON ultimo_registro_evento_decimo.cod_registro  = registro_evento_decimo.cod_registro
                   AND ultimo_registro_evento_decimo.cod_evento    = registro_evento_decimo.cod_evento
                   AND ultimo_registro_evento_decimo.timestamp     = registro_evento_decimo.timestamp
                   AND ultimo_registro_evento_decimo.desdobramento = registro_evento_decimo.desdobramento 
            INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                    ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_decimo.cod_periodo_movimentacao
            INNER JOIN folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                    ON ultimo_registro_evento_decimo.cod_registro  = evento_decimo_calculado.cod_registro
                   AND ultimo_registro_evento_decimo.cod_evento    = evento_decimo_calculado.cod_evento
                   AND ultimo_registro_evento_decimo.timestamp     = evento_decimo_calculado.timestamp_registro
                   AND ultimo_registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
            INNER JOIN folhapagamento'|| stEntidade ||'.evento
                    ON ultimo_registro_evento_decimo.cod_evento    = evento.cod_evento
                 WHERE periodo_movimentacao.dt_final between to_date('|| quote_literal(dtInicio) ||', ''yyyy-mm-dd'') AND to_date('|| quote_literal(dtFinal) ||', ''yyyy-mm-dd'')
        ';

    stFeriasSql := '
                SELECT replace(evento_ferias_calculado.valor::VARCHAR,''.'','''')::NUMERIC  AS valor
                     , evento.descricao as nom_evento
                     , SUBSTR(evento.codigo, 2) AS codigo
                     , CASE
                            WHEN evento.natureza = ''P'' THEN ''V''
                            WHEN evento.natureza = ''I'' THEN ''O''
                            WHEN evento.natureza = ''B'' THEN ''T''
                            WHEN evento.natureza = ''D'' THEN ''D''
                        END as natureza
                     , CASE 
                            WHEN evento.natureza = ''B'' THEN ''Evento de Base''
                            WHEN evento.natureza = ''I'' THEN ''Evento Informativo''
                       END AS observacao
                     , tcers.incideIRRF(evento.cod_evento) as Irrf
                     , '''' as cod_tipo_folha
                  FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
            INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                    ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_ferias.cod_periodo_movimentacao
            INNER JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                    ON registro_evento_ferias.cod_registro         = ultimo_registro_evento_ferias.cod_registro
                   AND registro_evento_ferias.cod_evento           = ultimo_registro_evento_ferias.cod_evento
                   AND registro_evento_ferias.timestamp            = ultimo_registro_evento_ferias.timestamp
                   AND registro_evento_ferias.desdobramento        = ultimo_registro_evento_ferias.desdobramento
            INNER JOIN folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                    ON ultimo_registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                   AND ultimo_registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                   AND ultimo_registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                   AND ultimo_registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
            INNER JOIN folhapagamento'|| stEntidade ||'.evento
                    ON evento_ferias_calculado.cod_evento          = evento.cod_evento
                 WHERE periodo_movimentacao.dt_final between to_date('|| quote_literal(dtInicio) ||', ''yyyy-mm-dd'') AND to_date('|| quote_literal(dtFinal) ||', ''yyyy-mm-dd'')
        ';

    stRescisao := '
            SELECT replace(evento_rescisao_calculado.valor::VARCHAR,''.'','''')::NUMERIC AS valor
                 , evento.descricao as nom_evento
                 , SUBSTR(evento.codigo, 2) as codigo
                 , CASE
                       WHEN evento.natureza = ''P'' THEN ''V''
                       WHEN evento.natureza = ''I'' THEN ''O''
                       WHEN evento.natureza = ''B'' THEN ''T''
                       WHEN evento.natureza = ''D'' THEN ''D''
                   END as natureza
                 , CASE 
                       WHEN evento.natureza = ''B'' THEN ''Evento de Base''
                       WHEN evento.natureza = ''I'' THEN ''Evento Informativo''
                   END AS observacao
                 , tcers.incideIRRF(evento.cod_evento) as Irrf
                 , '''' as cod_tipo_folha
              FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
        INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                ON registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
               AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
               AND registro_evento_rescisao.desdobramento    = evento_rescisao_calculado.desdobramento
               AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
        INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_rescisao.cod_periodo_movimentacao
        INNER JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                ON registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro
               AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento
               AND registro_evento_rescisao.desdobramento    = ultimo_registro_evento_rescisao.desdobramento
               AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
        INNER JOIN folhapagamento'|| stEntidade ||'.evento
                ON evento_rescisao_calculado.cod_evento      = evento.cod_evento
             WHERE periodo_movimentacao.dt_final between to_date('|| quote_literal(dtInicio) ||', ''yyyy-mm-dd'') AND to_date('|| quote_literal(dtFinal) ||', ''yyyy-mm-dd'')
        ';
   
    stSql := '
                    SELECT registro                                                 as matricula
                         , to_char(periodo_movimentacao.dt_final,   ''dd/mm/yyyy'') as dt_pagamento
                         , to_char(periodo_movimentacao.dt_inicial, ''dd/mm/yyyy'') as dt_competencia
                         , periodo_movimentacao.cod_periodo_movimentacao            as cod_periodo_movimentacao
                         , COALESCE(replace(num_agencia, ''-'', ''''),''000'')      as agencia
                         , COALESCE(num_banco,''000'')                              as banco
                         , COALESCE(replace(replace(contrato_servidor_conta_salario.nr_conta, ''-'', ''''), ''.'', ''''),''00000000000'') as conta_corrente
                         , contrato.cod_contrato
                         , contrato_servidor_orgao.cod_orgao
                         , CASE WHEN num_banco = ''041'' THEN
                                COALESCE ( (configuracao_banrisul.banco||''#''||configuracao_banrisul.agencia||''#''||configuracao_banrisul.corrente)
                                         , ''999#999#99999999''
                                         )
                           ELSE
                                ''999#999#99999999''
                           END as array_banco
                      FROM pessoal'|| stEntidade ||'.contrato
                INNER JOIN (
                                  SELECT cso.cod_contrato
                                       , cso.cod_orgao
                                       , max_contrato_orgao.cod_periodo_movimentacao
                                    FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao cso
                              INNER JOIN (
                                               SELECT cod_contrato
                                                    , max(contrato_servidor_orgao.timestamp) as timestamp
                                                    , periodo_movimentacao.cod_periodo_movimentacao
                                                 FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                           INNER JOIN ( SELECT dt_inicial,
                                                               timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo,
                                                               cod_periodo_movimentacao
                                                          FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                         WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                      ) AS periodo_movimentacao
                                                   ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                WHERE contrato_servidor_orgao.timestamp::VARCHAR <= periodo_movimentacao.timestamp_fechamento_periodo
                                             GROUP BY cod_contrato
                                                    , cod_periodo_movimentacao
                                         ) as max_contrato_orgao
                                      ON max_contrato_orgao.cod_contrato = cso.cod_contrato
                                     AND max_contrato_orgao.timestamp    = cso.timestamp
                              UNION
                                  SELECT cso.cod_contrato
                                       , cso.cod_orgao
                                       , max_contrato_orgao.cod_periodo_movimentacao
                                    FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao cso
                              INNER JOIN (
                                               SELECT cod_contrato
                                                    , max(contrato_pensionista_orgao.timestamp) as timestamp
                                                    , periodo_movimentacao.cod_periodo_movimentacao
                                                 FROM pessoal'|| stEntidade ||'.contrato_pensionista_orgao
                                           INNER JOIN ( SELECT dt_inicial,
                                                               timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo,
                                                               cod_periodo_movimentacao
                                                          FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                         WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                      ) AS periodo_movimentacao
                                                   ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                WHERE contrato_pensionista_orgao.timestamp::VARCHAR <= periodo_movimentacao.timestamp_fechamento_periodo
                                             GROUP BY cod_contrato
                                                    , cod_periodo_movimentacao
                                         ) as max_contrato_orgao
                                      ON max_contrato_orgao.cod_contrato = cso.cod_contrato
                                     AND max_contrato_orgao.timestamp    = cso.timestamp
                           ) as contrato_servidor_orgao
                        ON contrato_servidor_orgao.cod_contrato             = contrato.cod_contrato
                INNER JOIN folhapagamento'|| stEntidade ||'.periodo_movimentacao
                        ON contrato_servidor_orgao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                 LEFT JOIN (
                                  SELECT csc.cod_contrato
                                       , mb.num_banco
                                       , csc.cod_banco
                                       , ma.num_agencia
                                       , csc.cod_agencia
                                       , csc.nr_conta
                                       , conta_salario_interna.cod_periodo_movimentacao
                                    FROM pessoal'|| stEntidade ||'.contrato_servidor_conta_salario_historico csc
                              INNER JOIN monetario.agencia ma
                                      ON csc.cod_agencia = ma.cod_agencia
                                     AND csc.cod_banco = ma.cod_banco
                              INNER JOIN monetario.banco mb
                                      ON csc.cod_banco = mb.cod_banco
                                   INNER JOIN (
                                                  SELECT max(timestamp) as timestamp
                                                       , contrato_servidor_conta_salario_historico.cod_contrato
                                                       , periodo_movimentacao.cod_periodo_movimentacao
                                                    FROM pessoal'|| stEntidade ||'.contrato_servidor_conta_salario_historico
                                              INNER JOIN ( SELECT dt_inicial,
                                                                  timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo,
                                                                  cod_periodo_movimentacao
                                                             FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                            WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                         ) AS periodo_movimentacao
                                                      ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                   WHERE contrato_servidor_conta_salario_historico.timestamp::VARCHAR <= periodo_movimentacao.timestamp_fechamento_periodo
                                                GROUP BY contrato_servidor_conta_salario_historico.cod_contrato
                                                       , periodo_movimentacao.cod_periodo_movimentacao
                                              ) AS conta_salario_interna
                                      ON conta_salario_interna.timestamp    = csc.timestamp
                                     AND conta_salario_interna.cod_contrato = csc.cod_contrato
                              UNION
                                  SELECT csc.cod_contrato
                                       , mb.num_banco
                                       , csc.cod_banco
                                       , ma.num_agencia
                                       , csc.cod_agencia
                                       , csc.nr_conta
                                       , conta_salario_interna.cod_periodo_movimentacao
                                    FROM pessoal'|| stEntidade ||'.contrato_pensionista_conta_salario csc
                              INNER JOIN monetario.agencia ma
                                      ON csc.cod_agencia = ma.cod_agencia
                                     AND csc.cod_banco = ma.cod_banco
                              INNER JOIN monetario.banco mb
                                      ON csc.cod_banco = mb.cod_banco
                              INNER JOIN (
                                             SELECT max(timestamp) as timestamp
                                                  , contrato_pensionista_conta_salario.cod_contrato
                                                  , periodo_movimentacao.cod_periodo_movimentacao
                                               FROM pessoal'|| stEntidade ||'.contrato_pensionista_conta_salario
                                         INNER JOIN ( SELECT dt_inicial,
                                                             timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo,
                                                             cod_periodo_movimentacao
                                                        FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                       WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                    ) AS periodo_movimentacao
                                                 ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                              WHERE contrato_pensionista_conta_salario.timestamp::VARCHAR <= periodo_movimentacao.timestamp_fechamento_periodo
                                           GROUP BY contrato_pensionista_conta_salario.cod_contrato
                                                  , periodo_movimentacao.cod_periodo_movimentacao
                                         ) AS conta_salario_interna
                                      ON conta_salario_interna.timestamp    = csc.timestamp
                                     AND conta_salario_interna.cod_contrato = csc.cod_contrato
                           ) as contrato_servidor_conta_salario
                        ON contrato_servidor_conta_salario.cod_contrato = contrato.cod_contrato
                       AND contrato_servidor_conta_salario.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                 LEFT JOIN (
                                 SELECT banco.num_banco                                       as banco
                                      , replace(agencia.num_agencia, ''-'', '''')             as agencia
                                      , replace(conta_corrente.num_conta_corrente,''-'','''') as corrente
                                      , configuracao_banrisul_conta.timestamp
                                      , configuracao_banrisul_orgao.cod_orgao
                                      , interna.cod_periodo_movimentacao
                                   from ima.configuracao_banrisul_conta
                                   JOIN ima.configuracao_banrisul_orgao
                                     ON configuracao_banrisul_orgao.cod_convenio       = configuracao_banrisul_conta.cod_convenio
                                    AND configuracao_banrisul_orgao.cod_banco          = configuracao_banrisul_conta.cod_banco
                                    AND configuracao_banrisul_orgao.cod_agencia        = configuracao_banrisul_conta.cod_agencia
                                    AND configuracao_banrisul_orgao.cod_conta_corrente = configuracao_banrisul_conta.cod_conta_corrente
                                    AND configuracao_banrisul_orgao.timestamp          = configuracao_banrisul_conta.timestamp
                              LEFT JOIN monetario.conta_corrente
                                     ON conta_corrente.cod_banco          = configuracao_banrisul_conta.cod_banco
                                    AND conta_corrente.cod_agencia        = configuracao_banrisul_conta.cod_agencia
                                    AND conta_corrente.cod_conta_corrente = configuracao_banrisul_conta.cod_conta_corrente
                              LEFT JOIN monetario.agencia
                                     ON agencia.cod_banco   = conta_corrente.cod_banco
                                    AND agencia.cod_agencia = conta_corrente.cod_agencia
                              LEFT JOIN monetario.banco
                                     ON banco.cod_banco = agencia.cod_banco
                                   JOIN (
                                            SELECT periodo_movimentacao.cod_periodo_movimentacao
                                                 , MAX(timestamp) as timestamp
                                              FROM ima.configuracao_banrisul_conta
                                        INNER JOIN ( SELECT dt_inicial,
                                                            timestampfechamentoperiodomovimentacao(cod_periodo_movimentacao, '|| quote_literal(stEntidade) ||') as timestamp_fechamento_periodo,
                                                            cod_periodo_movimentacao
                                                       FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                      WHERE periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                                   ) AS periodo_movimentacao
                                                ON periodo_movimentacao.dt_inicial BETWEEN '|| quote_literal(dtInicio) ||' AND '|| quote_literal(dtFinal) ||'
                                             WHERE configuracao_banrisul_conta.timestamp::VARCHAR <= periodo_movimentacao.timestamp_fechamento_periodo
                                          GROUP BY periodo_movimentacao.cod_periodo_movimentacao
                                        ) AS interna
                                     ON interna.timestamp = configuracao_banrisul_conta.timestamp
                               GROUP BY banco.num_banco
                                      , agencia.num_agencia
                                      , conta_corrente.num_conta_corrente
                                      , configuracao_banrisul_conta.timestamp
                                      , configuracao_banrisul_orgao.cod_orgao
                                      , interna.cod_periodo_movimentacao
                           ) as configuracao_banrisul
                        ON configuracao_banrisul.cod_orgao                = contrato_servidor_orgao.cod_orgao
                       AND configuracao_banrisul.banco                    = contrato_servidor_conta_salario.num_banco
                       AND configuracao_banrisul.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao 
                  ORDER BY matricula, periodo_movimentacao.dt_final
             ';

    FOR reContratos IN EXECUTE stSql LOOP
        stFiltro     := ' AND cod_contrato                                  = '|| reContratos.cod_contrato             ||'
                          AND periodo_movimentacao.cod_periodo_movimentacao = '|| reContratos.cod_periodo_movimentacao ||'
                            ;
                        ';

        stSql2 := stSalarioSql || stFiltro;
        FOR reSalarios IN EXECUTE stSql2 LOOP
            reRegistros.cod_contrato   = reContratos.cod_contrato;
            reRegistros.cod_orgao      = reContratos.cod_orgao;
            reRegistros.array_banco    = reContratos.array_banco;
            reRegistros.matricula      = reContratos.matricula;
            reRegistros.dt_pagamento   = reContratos.dt_pagamento;
            reRegistros.dt_competencia = reContratos.dt_competencia;
            reRegistros.agencia        = reContratos.agencia;
            reRegistros.banco          = reContratos.banco;
            reRegistros.conta_corrente = reContratos.conta_corrente;
            reRegistros.evento         = reSalarios.nom_evento;
            reRegistros.codigo         = reSalarios.codigo;
            reRegistros.valor          = reSalarios.valor;
            reRegistros.natureza       = reSalarios.natureza;
            reRegistros.tipo_folha     = '1'; 
            reRegistros.observacao     = reSalarios.observacao;
            reRegistros.irrf           = reSalarios.irrf;
            RETURN NEXT reRegistros;
        END LOOP;

        stSql2 := stComplementarSql || stFiltro;
        FOR reComplementar IN EXECUTE stSql2 LOOP
            reRegistros.cod_contrato   = reContratos.cod_contrato; 
            reRegistros.cod_orgao      = reContratos.cod_orgao;
            reRegistros.array_banco    = reContratos.array_banco; 
            reRegistros.matricula      = reContratos.matricula;
            reRegistros.dt_pagamento   = reContratos.dt_pagamento;
            reRegistros.dt_competencia = reContratos.dt_competencia;
            reRegistros.agencia        = reContratos.agencia;
            reRegistros.banco          = reContratos.banco;
            reRegistros.conta_corrente = reContratos.conta_corrente;
            reRegistros.evento         = reComplementar.nom_evento;
            reRegistros.codigo         = reComplementar.codigo;
            reRegistros.valor          = reComplementar.valor;
            reRegistros.natureza       = reComplementar.natureza;
            reRegistros.tipo_folha     = '5';
            reRegistros.observacao     = reComplementar.observacao;
            reRegistros.irrf           = reComplementar.irrf;
            RETURN NEXT reRegistros;
        END LOOP;

        stSql2 := stDecimoSql || stFiltro;
        FOR reDecimo IN EXECUTE stSql2 LOOP
            reRegistros.cod_contrato   = reContratos.cod_contrato; 
            reRegistros.cod_orgao      = reContratos.cod_orgao;
            reRegistros.array_banco    = reContratos.array_banco; 
            reRegistros.matricula      = reContratos.matricula;
            reRegistros.dt_pagamento   = reContratos.dt_pagamento;
            reRegistros.dt_competencia = reContratos.dt_competencia;
            reRegistros.agencia        = reContratos.agencia;
            reRegistros.banco          = reContratos.banco;
            reRegistros.conta_corrente = reContratos.conta_corrente;
            reRegistros.evento         = reDecimo.nom_evento;
            reRegistros.codigo         = reDecimo.codigo;
            reRegistros.valor          = reDecimo.valor;
            reRegistros.natureza       = reDecimo.natureza;
            reRegistros.tipo_folha     = '2'; 
            reRegistros.observacao     = reDecimo.observacao;
            reRegistros.irrf           = reDecimo.irrf;
            RETURN NEXT reRegistros;
        END LOOP; 

        stSql2 := stFeriasSql || stFiltro;
        FOR reFerias IN EXECUTE stSql2 LOOP
            reRegistros.cod_contrato   = reContratos.cod_contrato; 
            reRegistros.cod_orgao      = reContratos.cod_orgao;
            reRegistros.array_banco    = reContratos.array_banco; 
            reRegistros.matricula      = reContratos.matricula;
            reRegistros.dt_pagamento   = reContratos.dt_pagamento;
            reRegistros.dt_competencia = reContratos.dt_competencia;
            reRegistros.agencia        = reContratos.agencia;
            reRegistros.banco          = reContratos.banco;
            reRegistros.conta_corrente = reContratos.conta_corrente;
            reRegistros.evento         = reFerias.nom_evento;
            reRegistros.codigo         = reFerias.codigo;
            reRegistros.valor          = reFerias.valor;
            reRegistros.natureza       = reFerias.natureza;
            reRegistros.tipo_folha     = '3'; 
            reRegistros.observacao     = reFerias.observacao;
            reRegistros.irrf           = reFerias.irrf;
            RETURN NEXT reRegistros;
        END LOOP;

        stSql2 := stRescisao || stFiltro;
        FOR reRescisao IN EXECUTE stSql2 LOOP 
            reRegistros.cod_contrato   = reContratos.cod_contrato; 
            reRegistros.cod_orgao      = reContratos.cod_orgao;
            reRegistros.array_banco    = reContratos.array_banco; 
            reRegistros.matricula      = reContratos.matricula;
            reRegistros.dt_pagamento   = reContratos.dt_pagamento;
            reRegistros.dt_competencia = reContratos.dt_competencia;
            reRegistros.agencia        = reContratos.agencia;
            reRegistros.banco          = reContratos.banco;
            reRegistros.conta_corrente = reContratos.conta_corrente;
            reRegistros.evento         = reRescisao.nom_evento;
            reRegistros.codigo         = reRescisao.codigo;
            reRegistros.valor          = reRescisao.valor;
            reRegistros.natureza       = reRescisao.natureza;
            reRegistros.tipo_folha     = '4'; 
            reRegistros.observacao     = reRescisao.observacao;
            reRegistros.irrf           = reRescisao.irrf;
            RETURN NEXT reRegistros;
        END LOOP;
    END LOOP;

END;
$$LANGUAGE 'plpgsql';


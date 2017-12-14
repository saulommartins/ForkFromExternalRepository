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
--    * PLPGSQL para retorno dos dados da conferência da sefip
--    * Data de Criação: 10/12/2007
--
--
--    * @author Diego Lemos de Souza
--
--    $Id: dirf.sql 31697 2008-08-04 19:33:31Z souzadl $
--*/
CREATE OR REPLACE FUNCTION conferenciaSefip(VARCHAR,VARCHAR,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS SETOF colulasConferenciaSefip AS $$
DECLARE
    stTipoFiltro                ALIAS FOR $1;
    stCodigos                   ALIAS FOR $2;
    inCodPeriodoMovimentacao    ALIAS FOR $3;
    inCodAtributo               ALIAS FOR $4;
    inCodTipoAtributo           ALIAS FOR $5;
    stEntidade                  ALIAS FOR $6;
    stSql                       VARCHAR;
    stSqlSalario                VARCHAR;
    stSqlComplementar           VARCHAR;
    stSqlFerias                 VARCHAR;
    stSqlRescisao               VARCHAR;
    stSqlDecimo                 VARCHAR;
    stSqlAliquota               VARCHAR;
    reRegistro                  RECORD;
    reSefip                     RECORD;
    crCursor                    REFCURSOR;
    rwConferenciaSefip          colulasConferenciaSefip%ROWTYPE;
    inCodEventoBaseFGTS         INTEGER;
    inCodEventoBasePrev         INTEGER;
    inCodEventoDescPrev         INTEGER;
    inCodEventoSalFamilia       INTEGER;
    inCodEventoSalMaternidade   INTEGER;
    nuValorBaseFGTS             NUMERIC:=0.00;
    nuValorBaseFGTSDecimo       NUMERIC:=0.00;
    nuValorBasePrev             NUMERIC:=0.00;
    nuValorBasePrevDecimo       NUMERIC:=0.00;
    nuValorDescPrev             NUMERIC:=0.00;
    nuValorDescPrevDecimo       NUMERIC:=0.00;
    nuValorSalFamilia           NUMERIC:=0.00;
    nuValorSalMaternidade       NUMERIC:=0.00;
    nuValorTemp                 NUMERIC:=0.00;
    nuAliquota                  NUMERIC:=0.00;
    nuValorPatronal             NUMERIC:=0.00;
BEGIN
    --Evento de base de FGTS
    stSql := 'SELECT fgts_evento.cod_evento
                FROM folhapagamento'|| stEntidade ||'.fgts_evento
                   , (  SELECT cod_fgts
                             , max(timestamp) as timestamp
                          FROM folhapagamento'|| stEntidade ||'.fgts_evento
                      GROUP BY cod_fgts) as max_fgts_evento
                   , folhapagamento'|| stEntidade ||'.evento
               WHERE fgts_evento.cod_evento = evento.cod_evento
                 AND fgts_evento.cod_fgts   = max_fgts_evento.cod_fgts
                 AND fgts_evento.timestamp  = max_fgts_evento.timestamp
                 AND cod_tipo = 3';
    inCodEventoBaseFGTS := selectIntoInteger(stSql);

    --Evento de salário família
    stSql := 'SELECT cod_evento
                FROM folhapagamento'|| stEntidade ||'.salario_familia_evento
                   , (SELECT cod_regime_previdencia
                           , max(timestamp) as timestamp
                        FROM folhapagamento'|| stEntidade ||'.salario_familia_evento
                      GROUP BY cod_regime_previdencia) as max_salario_familia_evento
               WHERE salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia
                 AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp
                 AND salario_familia_evento.cod_regime_previdencia = 1
                 AND salario_familia_evento.cod_tipo = 1';
    inCodEventoSalFamilia := selectIntoInteger(stSql);

    --Evento de salário maternidade
    stSql := ' SELECT assentamento_evento.cod_evento
                 FROM pessoal'|| stEntidade ||'.assentamento_assentamento
                    , pessoal'|| stEntidade ||'.assentamento
                    , (  SELECT cod_assentamento
                              , max(timestamp) as timestamp
                           FROM pessoal'|| stEntidade ||'.assentamento
                       GROUP BY cod_assentamento) as max_assentamento
                    , pessoal'|| stEntidade ||'.assentamento_evento
                WHERE assentamento_assentamento.cod_assentamento = assentamento.cod_assentamento
                  AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                  AND assentamento.timestamp = max_assentamento.timestamp
                  AND assentamento.cod_assentamento = assentamento_evento.cod_assentamento
                  AND assentamento.timestamp = assentamento_evento.timestamp
                  AND assentamento_assentamento.cod_motivo = 7';
    inCodEventoSalMaternidade := selectIntoInteger(stSql);

    stSql := '    SELECT contrato.*
                       , sw_cgm.nom_cgm
                       , trim(sw_cgm_pessoa_fisica.servidor_pis_pasep) as servidor_pis_pasep
                       , lpad(cod_categoria::varchar,2,''0'') as cod_categoria
                       , lpad(ocorrencia.num_ocorrencia::varchar,2,''0'') as num_ocorrencia
                    FROM pessoal'|| stEntidade ||'.contrato
              INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                      ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato
              INNER JOIN pessoal'|| stEntidade ||'.servidor
                      ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = servidor.numcgm
              INNER JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = servidor.numcgm
              INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor
                      ON contrato_servidor.cod_contrato = contrato.cod_contrato
              INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_ocorrencia
                      ON contrato_servidor_ocorrencia.cod_contrato = contrato.cod_contrato
              INNER JOIN (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_ocorrencia
                          GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia
                      ON max_contrato_servidor_ocorrencia.cod_contrato = contrato_servidor_ocorrencia.cod_contrato
                     AND max_contrato_servidor_ocorrencia.timestamp = contrato_servidor_ocorrencia.timestamp
              INNER JOIN pessoal'|| stEntidade ||'.ocorrencia
                      ON ocorrencia.cod_ocorrencia = contrato_servidor_ocorrencia.cod_ocorrencia
              INNER JOIN ultimo_contrato_servidor_previdencia('''|| stEntidade ||''','''||inCodPeriodoMovimentacao||''') as max_contrato_servidor_previdencia
                        ON contrato_servidor.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                       AND max_contrato_servidor_previdencia.bo_excluido = false  
                INNER JOIN folhapagamento'|| stEntidade ||'.previdencia
                        ON previdencia.cod_regime_previdencia = 1
                       AND previdencia.cod_previdencia = max_contrato_servidor_previdencia.cod_previdencia ';

    IF stTipoFiltro = 'lotacao' THEN
        stSql := stSql || ' INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                    ON contrato_servidor_orgao.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_orgao.cod_orgao IN ('|| stCodigos ||')
                            INNER JOIN (  SELECT cod_contrato
                                               , max(timestamp) as timestamp
                                            FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                                        GROUP BY cod_contrato) as max_contrato_servidor_orgao
                                    ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato
                                   AND max_contrato_servidor_orgao.timestamp = contrato_servidor_orgao.timestamp ';
    END IF;
    IF stTipoFiltro = 'local' THEN
        stSql := stSql || ' INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor_local
                                    ON contrato_servidor_local.cod_contrato = contrato.cod_contrato
                                   AND contrato_servidor_local.cod_local IN ('|| stCodigos ||')
                            INNER JOIN (  SELECT cod_contrato
                                               , max(timestamp) as timestamp
                                            FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                        GROUP BY cod_contrato) as max_contrato_servidor_local
                                    ON max_contrato_servidor_local.cod_contrato = contrato_servidor_local.cod_contrato
                                   AND max_contrato_servidor_local.timestamp = contrato_servidor_local.timestamp ';
    END IF;
    IF stTipoFiltro = 'contrato' OR stTipoFiltro = 'cgm_contrato' THEN
        stSql := stSql || ' WHERE contrato.cod_contrato IN ('|| stCodigos ||')';
    END IF;
    IF stTipoFiltro = 'atributo_servidor' THEN
        stSql := stSql || ' INNER JOIN pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
                                    ON atributo_contrato_servidor_valor.cod_contrato = contrato.cod_contrato
                                   AND atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo;
        IF inCodTipoAtributo = 4 OR inCodTipoAtributo = 3 THEN
            stSql := stSql || ' AND atributo_contrato_servidor_valor.valor IN ('|| stCodigos ||')';
        ELSE
            stSql := stSql || ' AND atributo_contrato_servidor_valor.valor = '|| quote_literal(stCodigos) ||' ';
        END IF;
        stSql := stSql || ' INNER JOIN (  SELECT cod_contrato
                                               , max(timestamp) as timestamp
                                            FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
                                        GROUP BY cod_contrato) as max_atributo_contrato_servidor_valor
                                    ON max_atributo_contrato_servidor_valor.cod_contrato = atributo_contrato_servidor_valor.cod_contrato
                                   AND max_atributo_contrato_servidor_valor.timestamp = atributo_contrato_servidor_valor.timestamp ';
    END IF;

    stSql := stSql || ' ORDER BY nom_cgm';

    FOR reRegistro IN EXECUTE stSql LOOP
        --Evento de base de Previdência
        stSql := 'SELECT previdencia_evento.cod_evento
                    FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                       , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                       , (  SELECT cod_previdencia
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                          GROUP BY cod_previdencia) as max_previdencia_previdencia
                       , folhapagamento'|| stEntidade ||'.previdencia
                       , pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                       , (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                          GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                   WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia
                     AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                     AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                     AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                     AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                     AND previdencia.cod_previdencia             = contrato_servidor_previdencia.cod_previdencia
                     AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                     AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                     AND tipo_previdencia = ''o''
					 AND previdencia.cod_regime_previdencia = 1
                     AND contrato_servidor_previdencia.cod_contrato = '|| reRegistro.cod_contrato ||'
                     AND cod_tipo = ';
        inCodEventoBasePrev := selectIntoInteger(stSql ||' 2');

        --Evento de desconto de Previdência
        inCodEventoDescPrev := selectIntoInteger(stSql ||' 1');

        stSqlAliquota := 'SELECT previdencia_previdencia.aliquota
                    FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                       , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                       , (  SELECT cod_previdencia
                                 , max(timestamp) as timestamp
                              FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                          GROUP BY cod_previdencia) as max_previdencia_previdencia
                       , folhapagamento'|| stEntidade ||'.previdencia
                       , pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                       , (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                          GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                   WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia
                     AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                     AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                     AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                     AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                     AND previdencia.cod_previdencia             = contrato_servidor_previdencia.cod_previdencia
                     AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                     AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                     AND tipo_previdencia = ''o''
                     AND previdencia.cod_regime_previdencia = 1
                     AND contrato_servidor_previdencia.cod_contrato = '|| reRegistro.cod_contrato ||'
                     AND cod_tipo = 1';

        nuValorBaseFGTS       := 0.00;
        nuValorBaseFGTSDecimo := 0.00;
        nuValorBasePrev       := 0.00;
        nuValorBasePrevDecimo := 0.00;
        nuValorDescPrev       := 0.00;
        nuValorDescPrevDecimo := 0.00;
        nuValorSalFamilia     := 0.00;
        nuValorSalMaternidade := 0.00;
        nuValorTemp           := 0.00;
        
        --SALÁRIO
        stSqlSalario := '    SELECT sum(evento_calculado.valor) as valor
                               FROM folhapagamento'|| stEntidade ||'.evento_calculado
                         INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                 ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro								
                              WHERE (evento_calculado.desdobramento is null OR evento_calculado.desdobramento != ''D'')
							    AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                AND registro_evento_periodo.cod_contrato = '|| reRegistro.cod_contrato;
        --###BASE FGTS
        IF inCodEventoBaseFGTS IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlSalario ||' AND evento_calculado.cod_evento = '|| inCodEventoBaseFGTS);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBaseFGTS := nuValorBaseFGTS + nuValorTemp;
            END IF;
        END IF;
        --###BASE PREVIDÊNCIA
        IF inCodEventoBasePrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlSalario ||' AND evento_calculado.cod_evento = '|| inCodEventoBasePrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBasePrev := nuValorBasePrev + nuValorTemp;
            END IF;
        END IF;
        --###DESCONTO PREVIDÊNCIA
        IF inCodEventoDescPrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlSalario ||' AND evento_calculado.cod_evento = '|| inCodEventoDescPrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorDescPrev := nuValorDescPrev + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO FAMÍLIA
        IF inCodEventoSalFamilia IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlSalario ||' AND evento_calculado.cod_evento = '|| inCodEventoSalFamilia);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalFamilia := nuValorSalFamilia + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO MATERNIDADE
        IF inCodEventoSalMaternidade IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlSalario ||' AND evento_calculado.cod_evento = '|| inCodEventoSalMaternidade);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalMaternidade := nuValorSalMaternidade + nuValorTemp;
            END IF;
        END IF;

        --COMPLEMENTAR
        stSqlComplementar := '    SELECT sum(evento_complementar_calculado.valor) as valor
                                    FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                              INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                      ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                                     AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                                     AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                     AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                                   WHERE registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                     AND registro_evento_complementar.cod_contrato = '|| reRegistro.cod_contrato ||'
                                     AND evento_complementar_calculado.cod_configuracao != 3';
        --###BASE FGTS
        IF inCodEventoBaseFGTS IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| inCodEventoBaseFGTS);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBaseFGTS := nuValorBaseFGTS + nuValorTemp;
            END IF;
        END IF;
        --###BASE PREVIDÊNCIA
        IF inCodEventoBasePrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| inCodEventoBasePrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBasePrev := nuValorBasePrev + nuValorTemp;
            END IF;
        END IF;
        --###DESCONTO PREVIDÊNCIA
        IF inCodEventoDescPrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| inCodEventoDescPrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorDescPrev := nuValorDescPrev + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO FAMÍLIA
        IF inCodEventoSalFamilia IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| inCodEventoSalFamilia);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalFamilia := nuValorSalFamilia + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO MATERNIDADE
        IF inCodEventoSalMaternidade IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| inCodEventoSalMaternidade);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalMaternidade := nuValorSalMaternidade + nuValorTemp;
            END IF;
        END IF;

        --FÉRIAS
        stSqlFerias := '    SELECT sum(evento_ferias_calculado.valor) as valor
                              FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                        INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                ON registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                               AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                               AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                               AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                             WHERE registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                               AND registro_evento_ferias.cod_contrato = '|| reRegistro.cod_contrato ||'
                               AND (evento_ferias_calculado.desdobramento = ''F'' OR evento_ferias_calculado.desdobramento = ''A'')';
        --###BASE FGTS
        IF inCodEventoBaseFGTS IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlFerias ||' AND evento_ferias_calculado.cod_evento = '|| inCodEventoBaseFGTS);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBaseFGTS := nuValorBaseFGTS + nuValorTemp;
            END IF;
        END IF;
        --###BASE PREVIDÊNCIA
        IF inCodEventoBasePrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlFerias ||' AND evento_ferias_calculado.cod_evento = '|| inCodEventoBasePrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBasePrev := nuValorBasePrev + nuValorTemp;
            END IF;
        END IF;
        --###DESCONTO PREVIDÊNCIA
        IF inCodEventoDescPrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlFerias ||' AND evento_ferias_calculado.cod_evento = '|| inCodEventoDescPrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorDescPrev := nuValorDescPrev + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO FAMÍLIA
        IF inCodEventoSalFamilia IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlFerias ||' AND evento_ferias_calculado.cod_evento = '|| inCodEventoSalFamilia);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalFamilia := nuValorSalFamilia + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO MATERNIDADE
        IF inCodEventoSalMaternidade IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlFerias ||' AND evento_ferias_calculado.cod_evento = '|| inCodEventoSalMaternidade);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalMaternidade := nuValorSalMaternidade + nuValorTemp;
            END IF;
        END IF;

        --RESCISÃO
        stSqlRescisao := '    SELECT sum(evento_rescisao_calculado.valor) as valor
                                FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                          INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                  ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                 AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                 AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                 AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                               WHERE registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                 AND registro_evento_rescisao.cod_contrato = '|| reRegistro.cod_contrato ||'
                                 AND evento_rescisao_calculado.desdobramento != ''D''';
        --###BASE FGTS
        IF inCodEventoBaseFGTS IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| inCodEventoBaseFGTS);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBaseFGTS := nuValorBaseFGTS + nuValorTemp;
            END IF;
        END IF;
        --###BASE PREVIDÊNCIA
        IF inCodEventoBasePrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| inCodEventoBasePrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorBasePrev := nuValorBasePrev + nuValorTemp;
            END IF;
        END IF;
        --###DESCONTO PREVIDÊNCIA
        IF inCodEventoDescPrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| inCodEventoDescPrev);
            IF nuValorTemp IS NOT NULL THEN
                nuValorDescPrev := nuValorDescPrev + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO FAMÍLIA
        IF inCodEventoSalFamilia IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| inCodEventoSalFamilia);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalFamilia := nuValorSalFamilia + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO MATERNIDADE
        IF inCodEventoSalMaternidade IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| inCodEventoSalMaternidade);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalMaternidade := nuValorSalMaternidade + nuValorTemp;
            END IF;
        END IF;

        --DÉCIMO
        stSqlDecimo := '    SELECT sum(evento_decimo_calculado.valor) as valor
                              FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                        INNER JOIN folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                ON registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                               AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                               AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                               AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                             WHERE registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                               AND registro_evento_decimo.cod_contrato = '|| reRegistro.cod_contrato;
        --###BASE FGTS DÉCIMO
        IF inCodEventoBaseFGTS IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| inCodEventoBaseFGTS ||'
                                                            AND evento_decimo_calculado.desdobramento = ''A'' ');
            IF nuValorTemp IS NOT NULL THEN
                nuValorBaseFGTSDecimo := nuValorBaseFGTSDecimo + nuValorTemp;
            END IF;
        END IF;
        --###BASE PREVIDÊNCIA DÉCIMO
        IF inCodEventoBasePrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| inCodEventoBasePrev ||'
                                                            AND evento_decimo_calculado.desdobramento = ''A'' ');
            IF nuValorTemp IS NOT NULL THEN
                nuValorBasePrevDecimo := nuValorBasePrevDecimo + nuValorTemp;
            END IF;
        END IF;
        --###DESCONTO PREVIDÊNCIA DÉCIMO
        IF inCodEventoDescPrev IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| inCodEventoDescPrev ||'
                                                            AND evento_decimo_calculado.desdobramento = ''A'' ');
            IF nuValorTemp IS NOT NULL THEN
                nuValorDescPrevDecimo := nuValorDescPrevDecimo + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO FAMÍLIA
        IF inCodEventoSalFamilia IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| inCodEventoSalFamilia);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalFamilia := nuValorSalFamilia + nuValorTemp;
            END IF;
        END IF;
        --###SALÁRIO MATERNIDADE
        IF inCodEventoSalMaternidade IS NOT NULL THEN
            nuValorTemp := selectIntoNumeric(stSqlDecimo ||' AND evento_decimo_calculado.cod_evento = '|| inCodEventoSalMaternidade);
            IF nuValorTemp IS NOT NULL THEN
                nuValorSalMaternidade := nuValorSalMaternidade + nuValorTemp;
            END IF;
        END IF;

        IF nuValorBaseFGTS       > 0 OR
           nuValorBaseFGTSDecimo > 0 OR
           nuValorBasePrev       > 0 OR
           nuValorBasePrevDecimo > 0 OR
           nuValorDescPrev       > 0 OR
           nuValorDescPrevDecimo > 0 OR
           nuValorSalFamilia     > 0 OR
           nuValorSalMaternidade > 0 THEN

            stSql := '    SELECT (SELECT trim(num_sefip) FROM pessoal'|| stEntidade ||'.sefip WHERE cod_sefip = assentamento_mov_sefip_saida.cod_sefip_saida) as num_sefip
                               , to_char(periodo_inicial,''dd/mm/yyyy'')|| '' a ''||  to_char(periodo_final,''dd/mm/yyyy'') as periodo
                            FROM pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                               , pessoal'|| stEntidade ||'.assentamento_gerado
                               , (SELECT cod_assentamento_gerado
                                       , max(timestamp) as timestamp
                                    FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                 GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
                               , pessoal'|| stEntidade ||'.assentamento
                               , (SELECT cod_assentamento
                                       , max(timestamp) as timestamp
                                    FROM pessoal'|| stEntidade ||'.assentamento
                                 GROUP BY cod_assentamento) as max_assentamento
                               , pessoal'|| stEntidade ||'.assentamento_mov_sefip_saida
                               , pessoal'|| stEntidade ||'.assentamento_assentamento
                               , pessoal'|| stEntidade ||'.classificacao_assentamento
                           WHERE assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                             AND assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                             AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                             AND assentamento_gerado.cod_assentamento = assentamento.cod_assentamento
                             AND assentamento.cod_assentamento = max_assentamento.cod_assentamento
                             AND assentamento.timestamp = max_assentamento.timestamp
                             AND assentamento.cod_assentamento = assentamento_mov_sefip_saida.cod_assentamento
                             AND assentamento.timestamp = assentamento_mov_sefip_saida.timestamp
                             AND assentamento.cod_assentamento = assentamento_assentamento.cod_assentamento
                             AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao
                             AND (cod_tipo = 2 OR cod_tipo = 3)
                             AND (to_char(periodo_inicial,''yyyy-mm'') = (SELECT to_char(dt_final,''yyyy-mm'') FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||')
                               OR to_char(periodo_final,''yyyy-mm'')   = (SELECT to_char(dt_final,''yyyy-mm'') FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||') )
                             AND NOT EXISTS (SELECT *
                                               FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                              WHERE assentamento_gerado_excluido.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                                                AND assentamento_gerado_excluido.timestamp = assentamento_gerado.timestamp)
                             AND NOT EXISTS (SELECT *
                                               FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                              WHERE contrato_servidor_caso_causa.cod_contrato = assentamento_gerado_contrato_servidor.cod_contrato)
                             AND assentamento_gerado_contrato_servidor.cod_contrato = '|| reRegistro.cod_contrato;
            OPEN crCursor FOR EXECUTE stSql;
                FETCH crCursor INTO reSefip;
            CLOSE crCursor;

            nuAliquota := selectIntoNumeric(stSqlAliquota);
            nuValorPatronal := (nuValorBasePrev*nuAliquota)/100;

            rwConferenciaSefip.registro                     := reRegistro.registro;
            rwConferenciaSefip.nom_cgm                      := reRegistro.nom_cgm;
            rwConferenciaSefip.servidor_pis_pasep           := reRegistro.servidor_pis_pasep;
            rwConferenciaSefip.cod_categoria                := reRegistro.cod_categoria;
            rwConferenciaSefip.num_ocorrencia               := reRegistro.num_ocorrencia;
            rwConferenciaSefip.num_sefip                    := reSefip.num_sefip;
            rwConferenciaSefip.periodo                      := reSefip.periodo;
            rwConferenciaSefip.base_fgts                    := nuValorBaseFGTS;
            rwConferenciaSefip.base_fgts_decimo             := nuValorBaseFGTSDecimo;
            rwConferenciaSefip.base_previdencia             := nuValorBasePrev;
            rwConferenciaSefip.base_previdencia_decimo      := nuValorBasePrevDecimo;
            rwConferenciaSefip.desconto_previdencia         := nuValorDescPrev;
            rwConferenciaSefip.desconto_previdencia_decimo  := nuValorDescPrevDecimo ;
            rwConferenciaSefip.salario_familia              := nuValorSalFamilia;
            rwConferenciaSefip.salario_maternidade          := nuValorSalMaternidade;
            rwConferenciaSefip.valor_patronal               := nuValorPatronal;
            
            RETURN NEXT rwConferenciaSefip;
        END IF;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';



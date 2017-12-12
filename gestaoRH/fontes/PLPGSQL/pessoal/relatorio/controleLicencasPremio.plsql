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
DROP TYPE servidoresLicencaPremio CASCADE;
CREATE TYPE servidoresLicencaPremio AS (
    registro        INTEGER,
    cod_contrato    INTEGER,
    nom_cgm         VARCHAR(200),
    data            VARCHAR(10),
    per_leitura     VARCHAR(23),
    regime          VARCHAR(80),
    sub_divisao     VARCHAR(80),
    funcao          VARCHAR(80),
    especialidade   VARCHAR(80),
    orgao           VARCHAR(80),
    local           VARCHAR(80),
    atributo        VARCHAR(80)
);

CREATE OR REPLACE FUNCTION servidoresLicencaPremio(INTEGER,DATE,INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF servidoresLicencaPremio AS $$
DECLARE
    inExercicio         ALIAS FOR $1;
    dtFinalLeitura      ALIAS FOR $2;
    inCodAssentamento   ALIAS FOR $3;
    stTipoFiltro        ALIAS FOR $4;
    stCodigos           ALIAS FOR $5;
    inCodAtributo       ALIAS FOR $6;
    stEntidade       ALIAS FOR $7;
    stPerLeitura        VARCHAR;
    stAdmissaoPosse     VARCHAR;
    stSelect            VARCHAR;
    stRegime            VARCHAR;
    stSubDivisao        VARCHAR;
    stFuncao            VARCHAR;
    stEspecialidade     VARCHAR;
    reRegistro          RECORD;
    dtData              DATE;
    dtComparacao        DATE;
    inQuantDiasLicPremio INTEGER;
    rwServidores        servidoresLicencaPremio%ROWTYPE;
BEGIN
    stAdmissaoPosse := selectIntoVarchar('SELECT configuracao.valor
                                   FROM administracao.configuracao
                                  WHERE cod_modulo = 22
                                    AND parametro = '|| quote_literal('dtContagemInicial'|| stEntidade) ||'
                                    AND exercicio = '|| quote_literal(inExercicio));

    stSelect := '
SELECT sw_cgm.nom_cgm
     , dt_nomeacao
     , dt_posse
     , dt_admissao
     , contrato_servidor_nomeacao_posse.cod_contrato
     , (SELECT registro FROM pessoal'|| stEntidade ||'.contrato WHERE cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato) as registro';
    IF stTipoFiltro = 'lotacao_grupo' THEN
        stSelect := stSelect || ' , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,('|| quote_literal(inExercicio ||'-01-01') ||')::date) as orgao';
    ELSE
        stSelect := stSelect || ' , '''' as orgao';
    END IF;
    IF stTipoFiltro = 'local_grupo' THEN
        stSelect := stSelect || ' , (SELECT descricao FROM organograma.local WHERE cod_local = contrato_servidor_local.cod_local) as local';
    ELSE
        stSelect := stSelect || ' , '''' as local';
    END IF;
    IF stTipoFiltro = 'atributo_servidor_grupo' THEN
        stSelect := stSelect || ' , CASE WHEN atributo_dinamico.cod_tipo = 4 OR atributo_dinamico.cod_tipo = 3 THEN
                                      (SELECT valor_padrao
                                         FROM administracao.atributo_valor_padrao
                                        WHERE atributo_valor_padrao.cod_modulo = atributo_dinamico.cod_modulo
                                          AND atributo_valor_padrao.cod_cadastro = atributo_dinamico.cod_cadastro
                                          AND atributo_valor_padrao.cod_atributo = atributo_dinamico.cod_atributo
                                          AND atributo_valor_padrao.cod_valor = atributo_contrato_servidor_valor.valor)
                                    ELSE atributo_contrato_servidor_valor.valor END AS atributo';
    ELSE
        stSelect := stSelect || ' , '''' as atributo';
    END IF;

stSelect := stSelect || '
  FROM sw_cgm
     , pessoal'|| stEntidade ||'.servidor
     , pessoal'|| stEntidade ||'.servidor_contrato_servidor ';

    IF stTipoFiltro = 'lotacao_grupo' THEN
        stSelect := stSelect || '
        , pessoal'|| stEntidade ||'.contrato_servidor_orgao
        , (  SELECT cod_contrato
                  , max(timestamp) as timestamp
               FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
           GROUP BY cod_contrato) as max_contrato_servidor_orgao';
    END IF;
    IF stTipoFiltro = 'local_grupo' THEN
        stSelect := stSelect || '
        LEFT JOIN (SELECT contrato_servidor_local.*
                     FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                        , (  SELECT cod_contrato
                                  , max(timestamp) as timestamp
                               FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                           GROUP BY cod_contrato) as max_contrato_servidor_local
                    WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                      AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) AS contrato_servidor_local
               ON contrato_servidor_local.cod_contrato = servidor_contrato_servidor.cod_contrato';
    END IF;
    IF stTipoFiltro = 'atributo_servidor_grupo' THEN
        stSelect := stSelect || '
        , pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
        , (  SELECT cod_contrato
                   , max(timestamp) as timestamp
                FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
            GROUP BY cod_contrato) as max_atributo_contrato_servidor_valor
        , administracao.atributo_dinamico
            ';
    END IF;

stSelect := stSelect || '
     , pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse

     , pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
        GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao

     , pessoal'|| stEntidade ||'.assentamento_sub_divisao
     , (  SELECT cod_assentamento
               , max(timestamp) as timestamp
            FROM pessoal'|| stEntidade ||'.assentamento_sub_divisao
        GROUP BY cod_assentamento) as max_assentamento_sub_divisao

     , pessoal'|| stEntidade ||'.assentamento_assentamento
     , pessoal'|| stEntidade ||'.classificacao_assentamento

 WHERE servidor.numcgm = sw_cgm.numcgm
   AND servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

   AND servidor_contrato_servidor.cod_contrato       = contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.timestamp    = max_contrato_servidor_nomeacao_posse.timestamp

   AND servidor_contrato_servidor.cod_contrato           = contrato_servidor_sub_divisao_funcao.cod_contrato
   AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
   AND contrato_servidor_sub_divisao_funcao.timestamp    = max_contrato_servidor_sub_divisao_funcao.timestamp

   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = assentamento_sub_divisao.cod_sub_divisao
   AND assentamento_sub_divisao.cod_assentamento            = max_assentamento_sub_divisao.cod_assentamento
   AND assentamento_sub_divisao.timestamp                   = max_assentamento_sub_divisao.timestamp

   AND assentamento_sub_divisao.cod_assentamento = assentamento_assentamento.cod_assentamento
   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao

   AND classificacao_assentamento.cod_tipo = 2
   AND assentamento_assentamento.cod_motivo = 9
   AND assentamento_assentamento.cod_assentamento = '|| inCodAssentamento;

    inQuantDiasLicPremio := selectIntoInteger('
        SELECT assentamento.quant_dias_licenca_premio
          FROM pessoal'|| stEntidade ||'.assentamento
             , (  SELECT cod_assentamento
                       , max(timestamp) as timestamp
                    FROM pessoal'|| stEntidade ||'.assentamento
                GROUP BY cod_assentamento) as max_assentamento
         WHERE assentamento.cod_assentamento = max_assentamento.cod_assentamento
           AND assentamento.timestamp = max_assentamento.timestamp
           AND assentamento.cod_assentamento = '|| inCodAssentamento);


    IF stAdmissaoPosse = 'dtPosse' THEN
        IF inQuantDiasLicPremio != NULL THEN
        stSelect := stSelect || ' AND (dt_posse::date +'|| inQuantDiasLicPremio ||') < '|| quote_literal(to_date(dtFinalLeitura::varchar,'yyyy-mm-dd'::varchar)) ||'';
        ELSE
        stSelect := stSelect || ' AND (dt_posse::date) < '|| quote_literal(to_date(dtFinalLeitura::varchar,'yyyy-mm-dd'::varchar)) ||'';
        END IF;
    END IF;
    
    IF stAdmissaoPosse = 'dtNomeacao' THEN
        IF inQuantDiasLicPremio != NULL THEN
        stSelect := stSelect || ' AND (dt_nomeacao::date +'|| inQuantDiasLicPremio ||') < '|| quote_literal(to_date(dtFinalLeitura::varchar,'yyyy-mm-dd'::varchar)) ||'';
        ELSE
        stSelect := stSelect || ' AND (dt_nomeacao::date) < '|| quote_literal(to_date(dtFinalLeitura::varchar,'yyyy-mm-dd'::varchar)) ||'';
        END IF;
    END IF;
    
    IF stAdmissaoPosse = 'dtAdmissao' THEN
        IF inQuantDiasLicPremio != NULL THEN
        stSelect := stSelect || ' AND (dt_admissao::date +'|| inQuantDiasLicPremio ||') < '|| quote_literal(to_date(dtFinalLeitura::varchar,'yyyy-mm-dd'::varchar)) ||' ';
        ELSE
        stSelect := stSelect || ' AND (dt_admissao::date) < '|| quote_literal(to_date(dtFinalLeitura::varchar,'yyyy-mm-dd'::varchar)) ||' ';
        END IF;    
    END IF;

    stSelect := stSelect || '
        AND NOT EXISTS (SELECT 1
                          FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                         WHERE cod_contrato = servidor_contrato_servidor.cod_contrato)';

    IF stTipoFiltro = 'contrato' OR stTipoFiltro = 'cgm_contrato' THEN
        stSelect := stSelect || ' AND servidor_contrato_servidor.cod_contrato IN ('|| stCodigos ||')';
    END IF;
    
    IF stTipoFiltro = 'lotacao_grupo' THEN
        stSelect := stSelect || '
        AND servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
        AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
        AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp
        AND contrato_servidor_orgao.cod_orgao IN ('|| stCodigos ||')';
    END IF;
    
    IF stTipoFiltro = 'local_grupo' THEN
        stSelect := stSelect || ' AND contrato_servidor_local.cod_local IN ('|| stCodigos ||')';
    END IF;
    
    IF stTipoFiltro = 'sub_divisao_funcao_grupo' THEN
        stSelect := stSelect || '
        AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN ('|| stCodigos ||')';
    END IF;
    
    IF stTipoFiltro = 'atributo_servidor_grupo' THEN
        stSelect := stSelect || '
        AND servidor_contrato_servidor.cod_contrato = atributo_contrato_servidor_valor.cod_contrato
        AND atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
        AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp
        AND atributo_contrato_servidor_valor.cod_modulo = atributo_dinamico.cod_modulo
        AND atributo_contrato_servidor_valor.cod_cadastro = atributo_dinamico.cod_cadastro
        AND atributo_contrato_servidor_valor.cod_atributo = atributo_dinamico.cod_atributo
        AND atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo ||'
        AND atributo_contrato_servidor_valor.valor IN ('|| stCodigos ||')';
    END IF;

    FOR reRegistro IN EXECUTE stSelect LOOP
        IF stAdmissaoPosse = 'dtPosse' THEN
            dtData := reRegistro.dt_posse;
        END IF;
        IF stAdmissaoPosse = 'dtNomeacao' THEN
            dtData := reRegistro.dt_nomeacao;
        END IF;
        IF stAdmissaoPosse = 'dtAdmissao' THEN
            dtData := reRegistro.dt_admissao;
        END IF;

        stPerLeitura := to_char(dtData,'dd/mm/yyyy')  ||' a '|| to_char(dtFinalLeitura,'dd/mm/yyyy');

        stRegime := selectIntoVarchar('
            SELECT (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor_regime_funcao.cod_regime) as regime
              FROM pessoal'|| stEntidade ||'.contrato_servidor_regime_funcao
                 , (  SELECT cod_contrato
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_regime_funcao
                    GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao
             WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato
               AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp
               AND contrato_servidor_regime_funcao.cod_contrato = '|| reRegistro.cod_contrato);

        stSubDivisao := selectIntoVarchar('
            SELECT (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao) as sub_divisao
              FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                 , (  SELECT cod_contrato
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                    GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
             WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
               AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp
               AND contrato_servidor_sub_divisao_funcao.cod_contrato = '|| reRegistro.cod_contrato);

        stFuncao := selectIntoVarchar('
            SELECT (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor_funcao.cod_cargo) as funcao
              FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                 , (  SELECT cod_contrato
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                    GROUP BY cod_contrato) as max_contrato_servidor_funcao
             WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
               AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp
               AND contrato_servidor_funcao.cod_contrato = '|| reRegistro.cod_contrato);

        stEspecialidade := selectIntoVarchar('
            SELECT (SELECT descricao FROM pessoal'|| stEntidade ||'.especialidade WHERE cod_especialidade = contrato_servidor_especialidade_funcao.cod_especialidade) as especialidade
              FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
                 , (  SELECT cod_contrato
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
                    GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao
             WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato
               AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp
               AND contrato_servidor_especialidade_funcao.cod_contrato = '|| reRegistro.cod_contrato);

        rwServidores.registro      := reRegistro.registro;
        rwServidores.cod_contrato  := reRegistro.cod_contrato;
        rwServidores.nom_cgm       := reRegistro.nom_cgm;
        rwServidores.data          := dtData;
        rwServidores.per_leitura   := stPerLeitura;
        rwServidores.regime        := stRegime;
        rwServidores.sub_divisao   := stSubDivisao;
        rwServidores.funcao        := stFuncao;
        rwServidores.especialidade := stEspecialidade;
        rwServidores.orgao         := reRegistro.orgao;
        rwServidores.local         := reRegistro.local;
        rwServidores.atributo      := reRegistro.atributo;
        RETURN NEXT rwServidores;
    END LOOP;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';

--SELECT * FROM servidoresLicencaPremio(2007,'2007-08-31',11,'atributo_servidor_grupo','31,37','11','') ORDER BY nom_cgm;

DROP TYPE colunasLicencaPremio CASCADE;
CREATE TYPE colunasLicencaPremio AS (
    sequencia       INTEGER,
    per_licenca     VARCHAR(23),
    con_licenca     VARCHAR(23),
    dia             INTEGER,
    dia_protelados  INTEGER,
    dia_averbados   INTEGER
);

CREATE OR REPLACE FUNCTION controleLicencasPremio(VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasLicencaPremio AS $$
DECLARE
    inExercicio         ALIAS FOR $1;
    dtFinalLeitura      ALIAS FOR $2;
    dtInicialParametro  ALIAS FOR $3;
    inCodContrato       ALIAS FOR $4;
    stEntidade       ALIAS FOR $5;
    stSelect            VARCHAR;
    stConLicenca        VARCHAR;
    stPerAquisitivoLicenca VARCHAR;
    stCondicao          VARCHAR;
    reRegistro          RECORD;
    reVinculo           RECORD;
    curCursor           REFCURSOR;
    dtInicial           DATE:=dtInicialParametro;
    dtFinal             DATE;
    dtPeriodoFinal      DATE;
    dtPeriodoInicial    DATE;
    dtPerInicialProAver DATE;
    dtPerFinalProAver   DATE;
    inDiasProtelarAverbar INTEGER;
    inDiasIncidencia    INTEGER:=1;
    inTotalDiasProtelar INTEGER:=0;
    inTotalDiasAverbar  INTEGER:=0;
    inDiasAssentamento  INTEGER:=0;
    inSequencia         INTEGER:=1;
    rwLicenca           colunasLicencaPremio%ROWTYPE;
BEGIN
    stSelect := '
SELECT dia
     , quant_dias_licenca_premio
     , assentamento_validade.dt_inicial
     , assentamento.cod_assentamento
     , contrato_servidor_sub_divisao_funcao.cod_contrato
  FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
     , (  SELECT cod_contrato
               , max(timestamp) as timestamp
            FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
        GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
     , pessoal'|| stEntidade ||'.assentamento_assentamento
     , pessoal'|| stEntidade ||'.assentamento
     , pessoal'|| stEntidade ||'.assentamento_afastamento_temporario_duracao
     , pessoal'|| stEntidade ||'.assentamento_validade
     , pessoal'|| stEntidade ||'.classificacao_assentamento
     , pessoal'|| stEntidade ||'.assentamento_sub_divisao
     , (  SELECT cod_assentamento
               , max(timestamp) as timestamp
            FROM pessoal'|| stEntidade ||'.assentamento_sub_divisao
        GROUP BY cod_assentamento) as max_assentamento_sub_divisao
 WHERE contrato_servidor_sub_divisao_funcao.cod_sub_divisao = assentamento_sub_divisao.cod_sub_divisao
   AND assentamento_sub_divisao.cod_assentamento = max_assentamento_sub_divisao.cod_assentamento
   AND assentamento_sub_divisao.timestamp        = max_assentamento_sub_divisao.timestamp
   AND assentamento_sub_divisao.cod_assentamento = assentamento_assentamento.cod_assentamento
   AND assentamento_assentamento.cod_classificacao = classificacao_assentamento.cod_classificacao
   AND assentamento_sub_divisao.cod_assentamento = assentamento_afastamento_temporario_duracao.cod_assentamento
   AND assentamento_sub_divisao.timestamp        = assentamento_afastamento_temporario_duracao.timestamp

   AND assentamento.cod_assentamento = max_assentamento_sub_divisao.cod_assentamento
   AND assentamento.timestamp = max_assentamento_sub_divisao.timestamp

   AND assentamento_validade.cod_assentamento = max_assentamento_sub_divisao.cod_assentamento
   AND assentamento_validade.timestamp = max_assentamento_sub_divisao.timestamp

   AND contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
   AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp

   AND classificacao_assentamento.cod_tipo = 2
   AND assentamento_assentamento.cod_motivo = 9
   AND contrato_servidor_sub_divisao_funcao.cod_contrato = '|| inCodContrato;

    FOR reRegistro IN EXECUTE stSelect LOOP
        IF reRegistro.dt_inicial > dtInicial THEN
            dtInicial := reRegistro.dt_inicial;
        END IF;

        dtFinal   := dtInicial + reRegistro.quant_dias_licenca_premio;
        inSequencia := 1;

        --Gerar registro até a data final de leitura do filtro
        WHILE dtFinal < to_date(dtFinalLeitura::varchar,'yyyy-mm-dd'::varchar) LOOP
            --Período de assentamento gerado com averbação ou protelação
            --Período dentro do período aquisitivo da licença

            stSelect := 'SELECT assentamento_vinculado.*
                           FROM pessoal'|| stEntidade ||'.condicao_assentamento
                              , (  SELECT cod_condicao
                                        , max(timestamp) as timestamp
                                     FROM pessoal'|| stEntidade ||'.condicao_assentamento
                                 GROUP BY cod_condicao) as max_condicao_assentamento
                              , pessoal'|| stEntidade ||'.assentamento_vinculado
                          WHERE condicao_assentamento.cod_condicao = assentamento_vinculado.cod_condicao
                            AND condicao_assentamento.timestamp = assentamento_vinculado.timestamp
                            AND condicao_assentamento.cod_condicao = max_condicao_assentamento.cod_condicao
                            AND condicao_assentamento.timestamp = max_condicao_assentamento.timestamp
                            AND condicao_assentamento.cod_assentamento = '|| reRegistro.cod_assentamento ||'
                            ';
            FOR reVinculo IN EXECUTE stSelect LOOP
                stSelect := 'SELECT periodo_inicial
                                  , periodo_final
                           FROM pessoal'|| stEntidade ||'.assentamento_gerado
                              , (  SELECT cod_assentamento_gerado
                                        , max(timestamp) as timestamp
                                     FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                 GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
                              , pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                          WHERE assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                            AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                            AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                            AND assentamento_gerado.cod_assentamento = '|| reVinculo.cod_assentamento_assentamento ||'
                            AND assentamento_gerado_contrato_servidor.cod_contrato = '|| reRegistro.cod_contrato ||'
                            AND periodo_inicial >= '|| quote_literal(dtInicial) ||'
                            AND periodo_final <= '|| quote_literal(dtFinal) ||' ';
                OPEN curCursor FOR EXECUTE stSelect;
                FETCH curCursor into dtPerInicialProAver, dtPerFinalProAver;
                CLOSE curCursor;

                IF dtPerInicialProAver IS NULL THEN
                    stSelect := 'SELECT periodo_inicial
                                      , periodo_final
                               FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                  , (  SELECT cod_assentamento_gerado
                                            , max(timestamp) as timestamp
                                         FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                     GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
                                  , pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                              WHERE assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                                AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                                AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                                AND assentamento_gerado.cod_assentamento = '|| reVinculo.cod_assentamento_assentamento ||'
                                AND assentamento_gerado_contrato_servidor.cod_contrato = '|| reRegistro.cod_contrato ||'
                                AND periodo_inicial >= '|| quote_literal(dtInicial) ||'
                                AND periodo_final   <= '|| quote_literal(dtInicial) ||'
                                AND periodo_final   <= '|| quote_literal(dtFinal)   ||' ';
                    OPEN curCursor FOR EXECUTE stSelect;
                    FETCH curCursor into dtPerInicialProAver, dtPerFinalProAver;
                    CLOSE curCursor;
                END IF;
                IF dtPerInicialProAver IS NULL THEN
                    stSelect := 'SELECT periodo_inicial
                                      , periodo_final
                               FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                  , (  SELECT cod_assentamento_gerado
                                            , max(timestamp) as timestamp
                                         FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                     GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
                                  , pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                              WHERE assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                                AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                                AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                                AND assentamento_gerado.cod_assentamento = '|| reVinculo.cod_assentamento_assentamento ||'
                                AND assentamento_gerado_contrato_servidor.cod_contrato = '|| reRegistro.cod_contrato ||'
                                AND periodo_inicial >= '|| quote_literal(dtFinal)   ||'
                                AND periodo_final   <= '|| quote_literal(dtFinal)   ||'
                                AND periodo_inicial >= '|| quote_literal(dtInicial) ||' ';
                    OPEN curCursor FOR EXECUTE stSelect;
                    FETCH curCursor into dtPerInicialProAver, dtPerFinalProAver;
                    CLOSE curCursor;
                END IF;

                IF dtPerInicialProAver IS NOT NULL THEN
                    inDiasAssentamento := (dtPerFinalProAver-dtPerInicialProAver)+1;
                    inDiasProtelarAverbar := reVinculo.dias_protelar_averbar;
                    inDiasIncidencia      := reVinculo.dias_incidencia;
                ELSE
                    inDiasAssentamento := 0;
                    inDiasProtelarAverbar := 0;
                END IF;
                IF reVinculo.condicao = 'p' THEN
                    inTotalDiasProtelar := (inDiasAssentamento/inDiasIncidencia)*inDiasProtelarAverbar;
                ELSE
                    inTotalDiasAverbar := (inDiasAssentamento/inDiasIncidencia)*inDiasProtelarAverbar;
                END IF;
            END LOOP;

            dtFinal := dtFinal+inTotalDiasProtelar-inTotalDiasAverbar;

            stPerAquisitivoLicenca := to_char(dtInicial,'dd/mm/yyyy') ||' a '|| to_char(dtFinal,'dd/mm/yyyy');

            stSelect := 'SELECT assentamento_gerado.periodo_inicial
                              , assentamento_gerado.periodo_final
                           FROM pessoal'|| stEntidade ||'.assentamento_gerado
                              , (  SELECT cod_assentamento_gerado
                                        , max(timestamp) as timestamp
                                     FROM pessoal'|| stEntidade ||'.assentamento_gerado
                                 GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
                              , pessoal'|| stEntidade ||'.assentamento_gerado_contrato_servidor
                              , pessoal'|| stEntidade ||'.assentamento_licenca_premio
                          WHERE assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                            AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                            AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
                            AND assentamento_gerado.cod_assentamento_gerado = assentamento_licenca_premio.cod_assentamento_gerado
                            AND assentamento_gerado.timestamp = assentamento_licenca_premio.timestamp
                            AND assentamento_gerado.cod_assentamento = '|| reRegistro.cod_assentamento ||'
                            AND assentamento_gerado_contrato_servidor.cod_contrato = '|| reRegistro.cod_contrato ||'
                            AND assentamento_licenca_premio.dt_inicial = '|| quote_literal(dtInicial) ||'
                            AND assentamento_licenca_premio.dt_final = '|| quote_literal(dtFinal) ||'
                            AND NOT EXISTS(SELECT 1
                                            FROM pessoal'|| stEntidade ||'.assentamento_gerado_excluido
                                           WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado
                                             AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp)';
            OPEN curCursor FOR EXECUTE stSelect;
            FETCH curCursor into dtPeriodoInicial, dtPeriodoFinal;
            CLOSE curCursor;
            stConLicenca := to_char(dtPeriodoInicial,'dd/mm/yyyy') ||' a '|| to_char(dtPeriodoFinal,'dd/mm/yyyy');

            rwLicenca.sequencia         := inSequencia;
            rwLicenca.dia               := reRegistro.dia;
            rwLicenca.dia_protelados    := inTotalDiasProtelar;
            rwLicenca.dia_averbados     := inTotalDiasAverbar;
            rwLicenca.per_licenca       := stPerAquisitivoLicenca;
            rwLicenca.con_licenca       := stConLicenca;

            RETURN NEXT rwLicenca;

            dtInicial := dtFinal+1;
            dtFinal   := dtInicial + reRegistro.quant_dias_licenca_premio;
            inSequencia := inSequencia+1;

        END LOOP;
    END LOOP;
    RETURN;
END;
$$ LANGUAGE 'plpgsql';

--SELECT * FROM controleLicencasPremio('2007','2009-01-01','1999-03-31','7903','');

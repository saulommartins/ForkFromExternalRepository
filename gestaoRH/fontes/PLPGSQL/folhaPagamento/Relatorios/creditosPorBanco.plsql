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
* creditosPorBanco
* Data de Criação   : 24/06/2009


* @author Analista      Dagiane
* @author Desenvolvedor Rafael Garbin

* @package URBEM
* @subpackage

* @ignore

$Id:$
*/
DROP TYPE colunasCreditosPorBanco CASCADE;

CREATE TYPE colunasCreditosPorBanco AS (
    registro            VARCHAR,
    nom_cgm             VARCHAR,
    servidor            VARCHAR,
    cpf                 VARCHAR,
    nr_conta            VARCHAR,
    num_agencia         VARCHAR,
    nom_agencia         VARCHAR,
    cod_agencia         INTEGER,
    num_banco           VARCHAR,
    nom_banco           VARCHAR,
    cod_banco           INTEGER,
    valor               NUMERIC,
    lotacao             VARCHAR,
    cod_estrutural      VARCHAR,
    cod_orgao           INTEGER,
    local               VARCHAR,
    cod_local           INTEGER
);

CREATE OR REPLACE FUNCTION creditosPorBanco() RETURNS VARCHAR AS $$
DECLARE
    stTimeStamp   VARCHAR;
    stTimeStamp2  VARCHAR;
    stSql         VARCHAR;
BEGIN
    SELECT 'creditosPorBanco'||replace(replace(replace(replace(cast(current_timestamp as varchar),' ',''),':',''),'-',''),'.','')
      INTO stTimeStamp;
    stTimeStamp2 := stTimeStamp||'_pk';
    stSql := 'CREATE TABLE '|| stTimeStamp || '(
                campo VARCHAR CONSTRAINT '||stTimeStamp2||' PRIMARY KEY,
                valor VARCHAR,
                parametrosPL BOOLEAN,
                tipo VARCHAR,
                ordem INTEGER
                )';
    EXECUTE stSql;
    RETURN stTimeStamp;
END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION relatorioCreditosPorBanco( VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stNomeTabela ALIAS FOR $1;

    stConsultaTabelaFiltro VARCHAR := '';
    stConsultaRegistros VARCHAR := '';
    stFiltroRegistros VARCHAR := '';
    stFiltroPL VARCHAR := '';
    reRegistrosFiltro RECORD;
    reRegistros RECORD;
BEGIN

    stConsultaTabelaFiltro := '
            SELECT campo
                 , valor
                 , parametrosPL
                 , tipo
              FROM '||stNomeTabela||'
          ORDER BY ordem
    ';
   --raise exception 'Debug%', stConsultaTabelaFiltro  ;
    FOR reRegistrosFiltro IN EXECUTE stConsultaTabelaFiltro
    LOOP
        IF reRegistrosFiltro.parametrosPL = 'true' THEN

            --verifica se tipo e varchar
            IF reRegistrosFiltro.tipo = 'varchar' THEN
                RAISE NOTICE 'valor filtro varchar: %', stFiltroPL;
                IF reRegistrosFiltro.valor = '' THEN
                    stFiltroPL := stFiltroPL || ',';
                ELSE
                    stFiltroPL := stFiltroPL || ',' || reRegistrosFiltro.valor ||' ';
                END IF;

            END IF;
            --verifica se  tipo e boolean
            IF reRegistrosFiltro.tipo = 'boolean' THEN
                RAISE NOTICE 'valor filtro boolean: %', stFiltroPL;
                IF reRegistrosFiltro.valor = '' THEN
                    stFiltroPL := stFiltroPL || ',';
                ELSE
                    stFiltroPL := stFiltroPL || ',' || reRegistrosFiltro.valor ||' ';
                END IF;
            END IF;

            --verifica se o tipo e integer
            IF reRegistrosFiltro.tipo = 'integer' THEN
                RAISE NOTICE 'valor filtro integer: %', stFiltroPL;
                    stFiltroPL := stFiltroPL || ',' ||    reRegistrosFiltro.valor ||' ';
            END IF;

        ELSE --else IF parametros true ou false
            stFiltroRegistros := stFiltroRegistros || ' AND ' || reRegistrosFiltro.valor || '';
        END IF;
    END LOOP;

    stConsultaRegistros := '
        SELECT *
          FROM creditosPorBanco('||SUBSTR(stFiltroPL, 2)||')
         WHERE 1 = 1
    '||stFiltroRegistros;
    RAISE NOTICE '%', stConsultaRegistros;

    --EXECUTE 'DROP TABLE ' || stNomeTabela;

    FOR reRegistros IN EXECUTE stConsultaRegistros
    LOOP
        RETURN NEXT reRegistros;
    END LOOP;

END;
$$ language 'plpgsql';

CREATE OR REPLACE FUNCTION creditosPorBanco(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasCreditosPorBanco AS $$
DECLARE
    inCodPeriodoMovimentacao    ALIAS FOR $1;
    inCodConfiguracao           ALIAS FOR $2;
    inCodComplementar           ALIAS FOR $3;
    stCodOrgao                  VARCHAR := $4;
    stCodLocal                  VARCHAR := $5;
    stCodBanco                  VARCHAR := $6;
    stCodAgencia                VARCHAR := $7;
    stSituacao                  VARCHAR := $8;
    stEntidade                  ALIAS FOR $9;
    boAgruparOrgao              ALIAS FOR $10;
    boAgruparLocal              ALIAS FOR $11;
    boAgruparAgencia            ALIAS FOR $12;
    rwCreditosPorBanco          colunasCreditosPorBanco%ROWTYPE;
    stSql                       VARCHAR := '';
    stSqlPensionistas           VARCHAR := '';
    stSqlServidores             VARCHAR := '';
    stContratos                 VARCHAR;
    stCPF                       VARCHAR;
    stExercicio                 VARCHAR;
    reRegistro                  RECORD;
    reContaServidor             RECORD;
    reContrato                  RECORD;
    reOrgao                     RECORD;
    nuProventos                 NUMERIC := 0.00;
    nuDescontos                 NUMERIC := 0.00;
    nuTemp                      NUMERIC := 0.00;
    inNumCgm                    INTEGER;
    boRetorno                   BOOLEAN;

    boFiltrarOrgao              BOOLEAN;
    boFiltrarLocal              BOOLEAN;
    boFiltrarBanco              BOOLEAN;
    boFiltrarAgencia            BOOLEAN;
BEGIN

    stSituacao   := coalesce(trim(stSituacao),'');
    stCodOrgao   := coalesce(trim(stCodOrgao),'');
    stCodLocal   := coalesce(trim(stCodLocal),'');
    stCodBanco   := coalesce(trim(stCodBanco),'');
    stCodAgencia := coalesce(trim(stCodAgencia),'');

    boFiltrarOrgao      := false;
    boFiltrarLocal      := false;
    boFiltrarBanco      := false;
    boFiltrarAgencia    := false;

    IF LENGTH(stCodOrgao) > 0 THEN
      boFiltrarOrgao := true;
    END IF;

    IF LENGTH(stCodLocal) > 0 THEN
      boFiltrarLocal := true;
    END IF;

    IF LENGTH(stCodBanco) > 0 THEN
      boFiltrarBanco := true;
    END IF;

    IF LENGTH(stCodAgencia) > 0 THEN
      boFiltrarAgencia := true;
    END IF;

    IF stSituacao = 'pensionistas' OR stSituacao = 'todos' THEN
        stSql := '    SELECT null::integer as cod_servidor
                           , pensionista.numcgm as numcgm_pensionista
                           , contrato_pensionista_conta_salario.nr_conta
                           , contrato_pensionista_conta_salario.cod_agencia
                           , contrato_pensionista_conta_salario.cod_banco
                           , agencia.num_agencia
                           , agencia.nom_agencia
                           , banco.num_banco
                           , banco.nom_banco';
        IF boAgruparOrgao = 'true' THEN
            stSql := stSql ||', contrato_pensionista_orgao.cod_orgao';
        END IF;
        IF boAgruparLocal = 'true' THEN
            stSql := stSql ||', contrato_servidor_local.cod_local';
        END IF;
        stSql := stSql ||'
                         FROM pessoal'||stEntidade||'.pensionista
                   INNER JOIN pessoal'||stEntidade||'.contrato_pensionista
                           ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                          AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                   INNER JOIN ultimo_contrato_pensionista_orgao('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_pensionista_orgao
                           ON contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato
                    LEFT JOIN ultimo_contrato_servidor_local('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_servidor_local
                           ON contrato_pensionista.cod_contrato_cedente = contrato_servidor_local.cod_contrato
                   INNER JOIN ultimo_contrato_pensionista_conta_salario('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_pensionista_conta_salario
                           ON contrato_pensionista.cod_contrato = contrato_pensionista_conta_salario.cod_contrato
                   INNER JOIN monetario.agencia
                           ON agencia.cod_banco = contrato_pensionista_conta_salario.cod_banco
                          AND agencia.cod_agencia = contrato_pensionista_conta_salario.cod_agencia
                   INNER JOIN monetario.banco
                           ON banco.cod_banco = agencia.cod_banco
                        WHERE EXISTS (SELECT 1
                                        FROM folhapagamento'||stEntidade||'.contrato_servidor_periodo
                                       WHERE contrato_servidor_periodo.cod_contrato = contrato_pensionista.cod_contrato
                                         AND contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')';
        IF boFiltrarOrgao THEN
            stSql := stSql ||' AND contrato_pensionista_orgao.cod_orgao IN ('||stCodOrgao||')';
        END IF;
        IF boFiltrarLocal THEN
            stSql := stSql ||' AND contrato_servidor_local.cod_local IN ('||stCodLocal||')';
        END IF;
        IF boFiltrarBanco THEN
            stSql := stSql ||' AND banco.cod_banco IN ('||stCodBanco||')';
        END IF;
        IF boFiltrarAgencia THEN
            stSql := stSql ||' AND agencia.cod_agencia IN ('||stCodAgencia||')';
        END IF;

        stSql := stSql ||'
                  GROUP BY cod_servidor
                         , pensionista.numcgm
                         , contrato_pensionista_conta_salario.nr_conta
                         , contrato_pensionista_conta_salario.cod_agencia
                         , contrato_pensionista_conta_salario.cod_banco
                         , agencia.num_agencia
                         , agencia.nom_agencia
                         , banco.num_banco
                         , banco.nom_banco';
        IF boAgruparOrgao = 'true' THEN
            stSql := stSql ||', contrato_pensionista_orgao.cod_orgao';
        END IF;
        IF boAgruparLocal = 'true' THEN
            stSql := stSql ||', contrato_servidor_local.cod_local';
        END IF;
    END IF;

    stSqlPensionistas := stSql;
    stSql := '';

    IF stSituacao <> 'pensionistas' OR stSituacao = 'todos' THEN
        stSql := '    SELECT servidor_contrato_servidor.cod_servidor
                           , null::integer as numcgm_pensionista
                           , contrato_servidor_conta_salario.nr_conta
                           , contrato_servidor_conta_salario.cod_agencia
                           , contrato_servidor_conta_salario.cod_banco
                           , agencia.num_agencia
                           , agencia.nom_agencia
                           , banco.num_banco
                           , banco.nom_banco';
        IF boAgruparOrgao = 'true' THEN
            stSql := stSql ||', contrato_servidor_orgao.cod_orgao';
        END IF;
        IF boAgruparLocal = 'true' THEN
            stSql := stSql ||', contrato_servidor_local.cod_local';
        END IF;
        stSql := stSql ||'
                        FROM pessoal'||stEntidade||'.servidor_contrato_servidor
                  INNER JOIN ultimo_contrato_servidor_orgao('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_servidor_orgao
                          ON servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
                   LEFT JOIN ultimo_contrato_servidor_local('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_servidor_local
                          ON servidor_contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato
                  INNER JOIN ultimo_contrato_servidor_conta_salario('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_servidor_conta_salario
                          ON servidor_contrato_servidor.cod_contrato = contrato_servidor_conta_salario.cod_contrato
                  INNER JOIN monetario.agencia
                          ON agencia.cod_banco = contrato_servidor_conta_salario.cod_banco
                         AND agencia.cod_agencia = contrato_servidor_conta_salario.cod_agencia
                  INNER JOIN monetario.banco
                          ON banco.cod_banco = agencia.cod_banco
                  INNER JOIN folhapagamento'||stEntidade||'.contrato_servidor_periodo
                          ON contrato_servidor_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                       WHERE contrato_servidor_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;

        IF boFiltrarOrgao THEN
            stSql := stSql ||' AND contrato_servidor_orgao.cod_orgao IN ('||stCodOrgao||')';
        END IF;
        IF boFiltrarLocal THEN
            stSql := stSql ||' AND contrato_servidor_local.cod_local IN ('||stCodLocal||')';
        END IF;
        IF boFiltrarBanco THEN
            stSql := stSql ||' AND banco.cod_banco IN ('||stCodBanco||')';
        END IF;
        IF boFiltrarAgencia THEN
            stSql := stSql ||' AND agencia.cod_agencia IN ('||stCodAgencia||')';
        END IF;

        IF stSituacao = 'ativos' THEN
            stSql := stSql || ' AND recuperarSituacaoDoContrato(servidor_contrato_servidor.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''A''';
        END IF;

        IF stSituacao = 'rescindidos' THEN
            stSql := stSql || ' AND recuperarSituacaoDoContrato(servidor_contrato_servidor.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''R''';
        END IF;

        IF stSituacao = 'aposentados' THEN
            stSql := stSql || ' AND recuperarSituacaoDoContrato(servidor_contrato_servidor.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''P''';
        END IF;


        stSql := stSql ||'
                    GROUP BY servidor_contrato_servidor.cod_servidor
                           , numcgm_pensionista
                           , contrato_servidor_conta_salario.nr_conta
                           , contrato_servidor_conta_salario.cod_agencia
                           , contrato_servidor_conta_salario.cod_banco
                           , agencia.num_agencia
                           , agencia.nom_agencia
                           , banco.num_banco
                           , banco.nom_banco';
        IF boAgruparOrgao = 'true' THEN
            stSql := stSql ||', contrato_servidor_orgao.cod_orgao';
        END IF;
        IF boAgruparLocal = 'true' THEN
            stSql := stSql ||', contrato_servidor_local.cod_local';
        END IF;

    END IF;

    stSqlServidores := stSql;
    stSql := '';

    IF LENGTH(COALESCE(stSqlServidores,'')) > 0 AND
       LENGTH(COALESCE(stSqlPensionistas,'')) > 0 THEN
        stSql := stSqlServidores||' UNION '||stSqlPensionistas;
    ELSE
        IF LENGTH(COALESCE(stSqlServidores,'')) > 0 THEN
            stSql := stSqlServidores;
        ELSEIF LENGTH(COALESCE(stSqlPensionistas,'')) > 0 THEN
            stSql := stSqlPensionistas;
        END IF;
    END IF;

    stContratos := '';
    FOR reContaServidor IN EXECUTE stSql LOOP

        IF reContaServidor.numcgm_pensionista IS NOT NULL THEN
            stSql := '    SELECT contrato.*
                            FROM pessoal'||stEntidade||'.contrato_pensionista
                      INNER JOIN pessoal'||stEntidade||'.pensionista
                              ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                             AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
                      INNER JOIN ultimo_contrato_pensionista_conta_salario('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_pensionista_conta_salario
                              ON contrato_pensionista.cod_contrato = contrato_pensionista_conta_salario.cod_contrato
                      INNER JOIN pessoal'||stEntidade||'.contrato
                              ON contrato.cod_contrato = contrato_pensionista.cod_contrato
                           WHERE pensionista.numcgm = '||reContaServidor.numcgm_pensionista||'
                             AND contrato_pensionista_conta_salario.cod_agencia = '||reContaServidor.cod_agencia||'
                             AND contrato_pensionista_conta_salario.cod_banco = '||reContaServidor.cod_banco||'
                             AND contrato_pensionista_conta_salario.nr_conta = '''||reContaServidor.nr_conta||'''';
        ELSE
            stSql := '    SELECT contrato.*
                            FROM pessoal'||stEntidade||'.servidor_contrato_servidor
                      INNER JOIN ultimo_contrato_servidor_conta_salario('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_servidor_conta_salario
                              ON servidor_contrato_servidor.cod_contrato = contrato_servidor_conta_salario.cod_contrato
                      INNER JOIN pessoal'||stEntidade||'.contrato
                              ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato';

            IF boAgruparOrgao = 'true' OR
               boFiltrarOrgao THEN
                stSql := stSql ||'
                     INNER JOIN ultimo_contrato_servidor_orgao('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_servidor_orgao
                             ON servidor_contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato';
            END IF;

            IF boFiltrarOrgao THEN
                stSql := stSql ||' AND contrato_servidor_orgao.cod_orgao IN ('||stCodOrgao||')';
            END IF;

            IF boAgruparOrgao = 'true' THEN
                stSql := stSql ||' AND contrato_servidor_orgao.cod_orgao = '||reContaServidor.cod_orgao;
            END IF;

            IF boAgruparLocal = 'true' OR
               boFiltrarLocal THEN
                stSql := stSql ||'
                      INNER JOIN ultimo_contrato_servidor_local('''||stEntidade||''', '||inCodPeriodoMovimentacao||') as contrato_servidor_local
                              ON servidor_contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato';
            END IF;

            IF boFiltrarLocal THEN
                stSql := stSql ||' AND contrato_servidor_local.cod_local IN ('||stCodLocal||')';
            END IF;

            IF boAgruparLocal = 'true' THEN
                stSql := stSql ||' AND contrato_servidor_local.cod_local = '||coalesce(reContaServidor.cod_local,0);
            END IF;

            stSql := stSql ||' WHERE servidor_contrato_servidor.cod_servidor = '||reContaServidor.cod_servidor||'
                                 AND contrato_servidor_conta_salario.cod_agencia = '||reContaServidor.cod_agencia||'
                                 AND contrato_servidor_conta_salario.cod_banco = '||reContaServidor.cod_banco||'
                                 AND contrato_servidor_conta_salario.nr_conta = '''||reContaServidor.nr_conta||'''';

            IF stSituacao = 'ativos' THEN
                stSql := stSql || ' AND recuperarSituacaoDoContrato(contrato.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''A''';
            END IF;

            IF stSituacao = 'rescindidos' THEN
                stSql := stSql || ' AND recuperarSituacaoDoContrato(contrato.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''R''';
            END IF;

            IF stSituacao = 'aposentados' THEN
                stSql := stSql || ' AND recuperarSituacaoDoContrato(contrato.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''P''';
            END IF;
        END IF;
        stContratos := '';
        nuProventos := 0;
        nuDescontos := 0;

        FOR reContrato IN EXECUTE stSql LOOP
            --FOLHA SALÁRIO
            IF inCodConfiguracao = 1 THEN
                stSql := '    SELECT evento_calculado.valor
                                   , evento.natureza
                                FROM folhapagamento'||stEntidade||'.evento_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                                  ON evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                          INNER JOIN folhapagamento'||stEntidade||'.evento
                                  ON evento.cod_evento = evento_calculado.cod_evento
                                 AND (evento.natureza = ''P'' OR evento.natureza = ''D'')
                               WHERE registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_periodo.cod_contrato = '||reContrato.cod_contrato;
            END IF;

            --FOLHA COMPLEMENTAR
            IF inCodConfiguracao = 0 THEN
                stSql := '    SELECT evento_complementar_calculado.valor
                                   , evento.natureza
                                FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                                  ON evento_complementar_calculado.cod_registro = registro_evento_complementar.cod_registro
                                 AND evento_complementar_calculado.cod_evento = registro_evento_complementar.cod_evento
                                 AND evento_complementar_calculado.cod_configuracao = registro_evento_complementar.cod_configuracao
                                 AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                          INNER JOIN folhapagamento'||stEntidade||'.evento
                                  ON evento.cod_evento = evento_complementar_calculado.cod_evento
                                 AND (evento.natureza = ''P'' OR evento.natureza = ''D'')
                               WHERE registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_complementar.cod_contrato = '||reContrato.cod_contrato||'
                                 AND registro_evento_complementar.cod_complementar = '||inCodComplementar;
            END IF;

            --FOLHA FÉRIAS
            IF inCodConfiguracao = 2 THEN
                stSql := '    SELECT evento_ferias_calculado.valor
                                   , evento.natureza
                                FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_ferias
                                  ON evento_ferias_calculado.cod_registro = registro_evento_ferias.cod_registro
                                 AND evento_ferias_calculado.cod_evento = registro_evento_ferias.cod_evento
                                 AND evento_ferias_calculado.desdobramento = registro_evento_ferias.desdobramento
                                 AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                          INNER JOIN folhapagamento'||stEntidade||'.evento
                                  ON evento.cod_evento = evento_ferias_calculado.cod_evento
                                 AND (evento.natureza = ''P'' OR evento.natureza = ''D'')
                               WHERE registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_ferias.cod_contrato = '||reContrato.cod_contrato;
            END IF;

            --FOLHA DÉCIMO
            IF inCodConfiguracao = 3 THEN
                stSql := '    SELECT evento_decimo_calculado.valor
                                   , evento.natureza
                                FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_decimo
                                  ON evento_decimo_calculado.cod_registro = registro_evento_decimo.cod_registro
                                 AND evento_decimo_calculado.cod_evento = registro_evento_decimo.cod_evento
                                 AND evento_decimo_calculado.desdobramento = registro_evento_decimo.desdobramento
                                 AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                          INNER JOIN folhapagamento'||stEntidade||'.evento
                                  ON evento.cod_evento = evento_decimo_calculado.cod_evento
                                 AND (evento.natureza = ''P'' OR evento.natureza = ''D'')
                               WHERE registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_decimo.cod_contrato = '||reContrato.cod_contrato;
            END IF;

            --FOLHA RESCISÃO
            IF inCodConfiguracao = 4 THEN
                stSql := '    SELECT evento_rescisao_calculado.valor
                                   , evento.natureza
                                FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao
                                  ON evento_rescisao_calculado.cod_registro = registro_evento_rescisao.cod_registro
                                 AND evento_rescisao_calculado.cod_evento = registro_evento_rescisao.cod_evento
                                 AND evento_rescisao_calculado.desdobramento = registro_evento_rescisao.desdobramento
                                 AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                          INNER JOIN folhapagamento'||stEntidade||'.evento
                                  ON evento.cod_evento = evento_rescisao_calculado.cod_evento
                                 AND (evento.natureza = ''P'' OR evento.natureza = ''D'')
                               WHERE registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND registro_evento_rescisao.cod_contrato = '||reContrato.cod_contrato;
            END IF;

            FOR reRegistro IN EXECUTE stSql LOOP
                IF reRegistro.natureza = 'P' THEN
                    nuProventos := nuProventos + reRegistro.valor;
                ELSE
                    nuDescontos := nuDescontos + reRegistro.valor;
                END IF;
            END LOOP;
            IF reRegistro.valor IS NOT NULL THEN
                stContratos := stContratos || reContrato.registro || '/';
            END IF;
        END LOOP;
        IF trim(stContratos) != '' THEN
            stContratos := substr(stContratos,1,char_length(stContratos)-1);
        END IF;

        IF nuProventos > 0 OR nuDescontos > 0 THEN
            IF reContaServidor.numcgm_pensionista IS NOT NULL THEN
                inNumCgm := reContaServidor.numcgm_pensionista;
            ELSE
                inNumCgm  := selectIntoInteger ('
                                   SELECT numcgm
                                     FROM pessoal'||stEntidade||'.servidor
                                    WHERE cod_servidor = '||reContaServidor.cod_servidor);
            END IF;
            stCPF := 'SELECT publico.mascara_cpf_cnpj(cpf,''cpf'') as cpf
                        FROM sw_cgm_pessoa_fisica
                       WHERE numcgm = '||inNumCgm;

           select max(valor) as exercicio
             into stExercicio
             from administracao.configuracao
            where parametro = 'ano_exercicio';

            rwCreditosPorBanco.registro         := stContratos;
            stSql := 'SELECT nom_cgm
                        FROM sw_cgm
                       WHERE numcgm = '||inNumCgm;
            rwCreditosPorBanco.nom_cgm          := selectIntoVarchar(stSql);
            rwCreditosPorBanco.servidor         := inNumCgm ||'-'||rwCreditosPorBanco.nom_cgm;
            IF trim(stCPF) != '' OR stCPF IS NOT NULL THEN
                rwCreditosPorBanco.cpf              := selectIntoVarchar(stCPF);
            END IF;

            rwCreditosPorBanco.nr_conta         := reContaServidor.nr_conta;
            rwCreditosPorBanco.num_agencia      := reContaServidor.num_agencia;
            rwCreditosPorBanco.nom_agencia      := reContaServidor.nom_agencia;
            rwCreditosPorBanco.cod_agencia      := reContaServidor.cod_agencia;
            rwCreditosPorBanco.num_banco        := reContaServidor.num_banco;
            rwCreditosPorBanco.nom_banco        := reContaServidor.nom_banco;
            rwCreditosPorBanco.cod_banco        := reContaServidor.cod_banco;
            rwCreditosPorBanco.valor            := nuProventos-nuDescontos;
            IF boAgruparOrgao = 'true' THEN
                SELECT recuperaDescricaoOrgao(cod_orgao,(stExercicio||'-01-01')::date) as descricao
                     , orgao
                  INTO reOrgao
                  FROM organograma.vw_orgao_nivel
                 WHERE cod_orgao = reContaServidor.cod_orgao;

                rwCreditosPorBanco.lotacao          := reOrgao.descricao;
                rwCreditosPorBanco.cod_estrutural   := reOrgao.orgao;
                rwCreditosPorBanco.cod_orgao        := reContaServidor.cod_orgao;
            END IF;
            IF boAgruparLocal = 'true' THEN
                IF reContaServidor.cod_local IS NOT NULL THEN
                    stSql := 'SELECT descricao
                                FROM organograma.local
                            WHERE cod_local = '||reContaServidor.cod_local;
                    rwCreditosPorBanco.local            := selectIntoVarchar(stSql);
                END IF;
                rwCreditosPorBanco.cod_local        := reContaServidor.cod_local;
            END IF;
            RETURN NEXT rwCreditosPorBanco;
        END IF;
    END LOOP;
END
$$ LANGUAGE 'plpgsql';

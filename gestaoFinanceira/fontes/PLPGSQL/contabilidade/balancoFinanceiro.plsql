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
/* Script de função PLPGSQL
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
$Id: balancoFinanceiro.plsql 65889 2016-06-24 14:52:46Z michel $
*/


CREATE OR REPLACE FUNCTION contabilidade.relatorioBalancoFinanceiroRecurso ( VARCHAR,VARCHAR,VARCHAR,VARCHAR,CHAR ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio             ALIAS FOR $1;
    dtInicial               ALIAS FOR $2;
    dtFinal                 ALIAS FOR $3;
    stCodEntidade           ALIAS FOR $4;
    stTipoDespesa           ALIAS FOR $5;
    stSql                   VARCHAR := '';
    stExercicioAnterior     VARCHAR := ''; 
    dtInicialAnterior       VARCHAR := '';
    dtFinalAnterior         VARCHAR := '';
    stDespesa               VARCHAR := '';
    stDespesaAnterior       VARCHAR := '';
    inMovimentacao          INTEGER;
    reRegistro              RECORD;
    reRegistroAux           RECORD;
    arDescricao             VARCHAR[];
    arDescricaoDespesas     VARCHAR[];
    arDescricaoValores      VARCHAR[];
    arDescricaoDespesasValores   VARCHAR[];
    i                       INTEGER;
    totalI                  NUMERIC;
    totalII                 NUMERIC;
    totalIII                NUMERIC;
    totalIV                 NUMERIC;
    totalV                  NUMERIC;

    stFiltroTrans           VARCHAR := '';
    stFiltroExtra           VARCHAR := '';
    
BEGIN

stExercicioAnterior     := (to_number(stExercicio,'9999')-1)::varchar;
dtInicialAnterior       := to_char(to_date(dtInicial::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');
dtFinalAnterior         := to_char(to_date(dtFinal::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');

--Relacionando colunas das tabelas com o tipo de despesa selecionado no filtro
IF (stTipoDespesa = 'E') THEN
        stDespesa := '(empenhado_per - anulado_per) as valor';

        IF (stExercicio >= '2014') THEN
            stDespesaAnterior := '(empenhado_per_anterior - anulado_per_anterior) as valor_anterior';
        END IF;

    END IF;

    IF (stTipoDespesa = 'L') THEN
        stDespesa := 'liquidado_per as valor';

        IF (stExercicio >= '2014') THEN
            stDespesaAnterior := 'liquidado_per_anterior as valor_anterior';
        END IF;
    END IF;

    IF (stTipoDespesa = 'P') THEN
        stDespesa := 'pago_per as valor';

        IF (stExercicio >= '2014') THEN
            stDespesaAnterior := 'pago_per_anterior as valor_anterior';
        END IF;
    END IF;

--Criando tabela para armazerar as receitas referente a cada cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_receita AS
            SELECT
                    descricao
                    ,ABS(SUM(arrecadado_periodo)) as arrecadado_periodo
        ';
    IF(stExercicio >= '2014' )THEN
        stSql := stSql || ',ABS(SUM(arrecadado_periodo_anterior)) as arrecadado_periodo_anterior';
    END IF;

    stSql := stSql || '
            FROM(
                SELECT
                 descricao
                ,SUM(arrecadado_periodo) as arrecadado_periodo
    ';

    IF(stExercicio >= '2014' )THEN
        stSql := stSql || ',SUM(arrecadado_periodo_anterior) as arrecadado_periodo_anterior';
    END IF;

    stSql := stSql ||'
                FROM(
                    SELECT 
                        CASE
                            WHEN recurso like ''0001'' AND cod_estrutural NOT like ''9%''
                            THEN ''recurso_livre''
                            WHEN (recurso IS NULL OR recurso NOT LIKE ''0001'') AND (cod_estrutural like ''1%'' OR cod_estrutural like ''2%'' OR cod_estrutural like ''7%'')
                            THEN ''recurso_vinculado''
                            WHEN recurso like ''0001'' AND cod_estrutural like ''9%''
                            THEN ''deducoes_recurso_livre''
                            WHEN (recurso IS NULL OR recurso NOT LIKE ''0001'') AND cod_estrutural like ''9%''
                            THEN ''deducoes_recurso_vinculado''
                            WHEN cod_estrutural like ''9%'' AND recurso IS NOT NULL
                            THEN ''redutoras_receita_orcamentaria''
                            WHEN (cod_estrutural like ''1%'' OR cod_estrutural like ''2%'' OR cod_estrutural like ''7%'') AND recurso IS NOT NULL
                            THEN ''receita_orcamentaria''
                    END as descricao
                    ,recurso
                    ,arrecadado_periodo
            ';

    IF(stExercicio >= '2014' )THEN
        stSql := stSql || ',0.00 AS arrecadado_periodo_anterior';
    END IF;

    stSql := stSql || '
                    FROM orcamento.fn_balancete_receita('|| quote_literal(stExercicio) ||'
                                                        ,''''
                                                        ,'|| quote_literal(dtInicial) ||'
                                                        ,'|| quote_literal(dtFinal) ||'
                                                        ,'|| quote_literal(stCodEntidade) ||'
                                                        ,'''','''','''','''','''','''',''''
                                                        ) as retorno(
                                                        cod_estrutural      varchar,
                                                        receita             integer,
                                                        recurso             varchar,
                                                        descricao           varchar,
                                                        valor_previsto      numeric,
                                                        arrecadado_periodo  numeric,
                                                        arrecadado_ano      numeric,
                                                        diferenca           numeric
                                                        )
                  WHERE receita IS NOT NULL
    ';
    IF(stExercicio >= '2014' )THEN
        stSql := stSql || '
                UNION

                    SELECT 
                        CASE
                            WHEN recurso_anterior like ''0001'' AND cod_estrutural_anterior NOT like ''9%''
                            THEN ''recurso_livre''
                            WHEN (recurso_anterior IS NULL OR recurso_anterior NOT LIKE ''0001'') AND (cod_estrutural_anterior like ''1%'' OR cod_estrutural_anterior like ''2%'' OR cod_estrutural_anterior like ''7%'')
                            THEN ''recurso_vinculado''
                            WHEN recurso_anterior like ''0001'' AND cod_estrutural_anterior like ''9%''
                            THEN ''deducoes_recurso_livre''
                            WHEN (recurso_anterior IS NULL OR recurso_anterior NOT LIKE ''0001'') AND cod_estrutural_anterior like ''9%''
                            THEN ''deducoes_recurso_vinculado''
                            WHEN cod_estrutural_anterior like ''9%'' AND recurso_anterior IS NOT NULL
                            THEN ''redutoras_receita_orcamentaria''
                            WHEN (cod_estrutural_anterior like ''1%'' OR cod_estrutural_anterior like ''2%'' OR cod_estrutural_anterior like ''7%'') AND recurso_anterior IS NOT NULL
                            THEN ''receita_orcamentaria''
                    END as descricao
                    ,recurso_anterior AS recurso
                    ,0.00 AS arrecadado_periodo
                    ,COALESCE(arrecadado_periodo_anterior, 0.00) AS arrecadado_periodo_anterior
                    FROM orcamento.fn_balancete_receita('|| quote_literal(stExercicioAnterior) ||'
                                                        ,''''
                                                        ,'|| quote_literal(dtInicialAnterior) ||'
                                                        ,'|| quote_literal(dtFinalAnterior) ||'
                                                        ,'|| quote_literal(stCodEntidade) ||'
                                                        ,'''','''','''','''','''','''',''''
                                                        ) AS exercicio_anterior(
                                                        cod_estrutural_anterior      varchar,
                                                        receita_anterior             integer,
                                                        recurso_anterior             varchar,
                                                        descricao_anterior           varchar,
                                                        valor_previsto_anterior      numeric,
                                                        arrecadado_periodo_anterior  numeric,
                                                        arrecadado_ano_anterior      numeric,
                                                        diferenca_anterior           numeric
                                                        )
                  WHERE receita_anterior IS NOT NULL
            ';
    END IF;
    stSql := stSql || '
                ) as tbl
                WHERE descricao IS NOT NULL
             GROUP BY descricao,recurso
            ) resultado
            GROUP BY descricao
    ';

    EXECUTE stSql;

--Criando tabela para armazenar despesas referente a sua classificao para calculo futuro
    stSql := ' CREATE TEMPORARY TABLE tmp_calculo_despesas AS
                SELECT classificacao
                     , num_recurso
                     , SUM(valor) AS valor
                     , SUM(valor_anterior) AS valor_anterior
                     , SUM(empenhado_ano) AS empenhado_ano
                     , SUM(anulado_ano) AS anulado_ano
                     , SUM(liquidado_ano) AS liquidado_ano
                     , SUM(pago_ano) AS pago_ano
                     , SUM(empenhado_ano_anterior) AS empenhado_ano_anterior
                     , SUM(anulado_ano_anterior) AS anulado_ano_anterior
                     , SUM(liquidado_ano_anterior) AS liquidado_ano_anterior
                     , SUM(pago_ano_anterior) AS pago_ano_anterior
                  FROM (
                                    SELECT
                                            classificacao
                                            ,num_recurso
                                            ,'|| stDespesa ||'
                                            ,0.00 as valor_anterior
                                            ,empenhado_ano
                                            ,anulado_ano
                                            ,liquidado_ano
                                            ,pago_ano
                                            ,0.00 AS empenhado_ano_anterior
                                            ,0.00 AS anulado_ano_anterior
                                            ,0.00 AS liquidado_ano_anterior
                                            ,0.00 AS pago_ano_anterior
                                    FROM orcamento.fn_balancete_despesa('|| quote_literal(stExercicio) ||'
                                                                        ,'' AND od.cod_entidade IN  ('|| stCodEntidade ||')''
                                                                        ,'|| quote_literal(dtInicial) ||'
                                                                        ,'|| quote_literal(dtFinal) ||'
                                                                        ,'''','''','''','''','''' ,'''','''', ''''
                                                                        ) AS retorno(
                                                                        exercicio       char(4),
                                                                        cod_despesa     integer,
                                                                        cod_entidade    integer,
                                                                        cod_programa    integer,
                                                                        cod_conta       integer,
                                                                        num_pao         integer,
                                                                        num_orgao       integer,
                                                                        num_unidade     integer,
                                                                        cod_recurso     integer,
                                                                        cod_funcao      integer,
                                                                        cod_subfuncao   integer,
                                                                        tipo_conta      varchar,
                                                                        vl_original     numeric,
                                                                        dt_criacao      date,
                                                                        classificacao   varchar,
                                                                        descricao       varchar,
                                                                        num_recurso     varchar,
                                                                        nom_recurso     varchar,
                                                                        nom_orgao       varchar,
                                                                        nom_unidade     varchar,
                                                                        nom_funcao      varchar,
                                                                        nom_subfuncao   varchar,
                                                                        nom_programa    varchar,
                                                                        nom_pao         varchar,
                                                                        empenhado_ano   numeric,
                                                                        empenhado_per   numeric,
                                                                        anulado_ano     numeric,
                                                                        anulado_per     numeric,
                                                                        pago_ano        numeric,
                                                                        pago_per        numeric,
                                                                        liquidado_ano   numeric,
                                                                        liquidado_per   numeric,
                                                                        saldo_inicial   numeric,
                                                                        suplementacoes  numeric,
                                                                        reducoes        numeric,
                                                                        total_creditos  numeric,
                                                                        credito_suplementar     numeric,
                                                                        credito_especial        numeric,
                                                                        credito_extraordinario  numeric,
                                                                        num_programa    varchar,
                                                                        num_acao        varchar
                                                                        )
        ';

    IF(stExercicio::integer >= 2014 )THEN
        stSql := stSql || '
                                 UNION ALL

                                    SELECT
                                            classificacao_anterior AS classificacao
                                            ,num_recurso_anterior AS num_recurso
                                            ,0.00 AS valor
                                            ,'|| stDespesaAnterior ||'
                                            ,0.00 AS empenhado_ano
                                            ,0.00 AS anulado_ano
                                            ,0.00 AS liquidado_ano
                                            ,0.00 AS pago_ano
                                            ,empenhado_ano_anterior
                                            ,anulado_ano_anterior
                                            ,liquidado_ano_anterior
                                            ,pago_ano_anterior
                                    FROM orcamento.fn_balancete_despesa('|| quote_literal(stExercicioAnterior) ||'
                                                                        ,'' AND od.cod_entidade IN  ('|| stCodEntidade ||')''
                                                                        ,'|| quote_literal(dtInicialAnterior) ||'
                                                                        ,'|| quote_literal(dtFinalAnterior) ||'
                                                                        ,'''','''','''','''','''' ,'''','''', ''''
                                                                        ) AS retorno_anterior(
                                                                        exercicio_anterior       char(4),
                                                                        cod_despesa_anterior     integer,
                                                                        cod_entidade_anterior    integer,
                                                                        cod_programa_anterior    integer,
                                                                        cod_conta_anterior       integer,
                                                                        num_pao_anterior         integer,
                                                                        num_orgao_anterior       integer,
                                                                        num_unidade_anterior     integer,
                                                                        cod_recurso_anterior     integer,
                                                                        cod_funcao_anterior      integer,
                                                                        cod_subfuncao_anterior   integer,
                                                                        tipo_conta_anterior      varchar,
                                                                        vl_original_anterior     numeric,
                                                                        dt_criacao_anterior      date,
                                                                        classificacao_anterior   varchar,
                                                                        descricao_anterior       varchar,
                                                                        num_recurso_anterior     varchar,
                                                                        nom_recurso_anterior     varchar,
                                                                        nom_orgao_anterior       varchar,
                                                                        nom_unidade_anterior     varchar,
                                                                        nom_funcao_anterior      varchar,
                                                                        nom_subfuncao_anterior   varchar,
                                                                        nom_programa_anterior    varchar,
                                                                        nom_pao_anterior         varchar,
                                                                        empenhado_ano_anterior   numeric,
                                                                        empenhado_per_anterior   numeric,
                                                                        anulado_ano_anterior     numeric,
                                                                        anulado_per_anterior     numeric,
                                                                        pago_ano_anterior        numeric,
                                                                        pago_per_anterior        numeric,
                                                                        liquidado_ano_anterior   numeric,
                                                                        liquidado_per_anterior   numeric,
                                                                        saldo_inicial_anterior   numeric,
                                                                        suplementacoes_anterior  numeric,
                                                                        reducoes_anterior        numeric,
                                                                        total_creditos_anterior  numeric,
                                                                        credito_suplementar_anterior     numeric,
                                                                        credito_especial_anterior        numeric,
                                                                        credito_extraordinario_anterior  numeric,
                                                                        num_programa    varchar,
                                                                        num_acao        varchar
                                                                  )
    ';
    END IF;
    
    stSql := stSql || '
                       ) AS calculo_despesas
              GROUP BY classificacao
                     , num_recurso
    ';

EXECUTE stSql;

--INSERT para separar as despesas orcamentarias por sua classificacao.
    IF (stExercicio::integer >= 2014) THEN
        INSERT INTO tmp_calculo_despesas(classificacao,valor,valor_anterior) VALUES('despesas_orcamentarias'
                                                                                    ,(SELECT sum(valor) as valor
                                                                                            FROM tmp_calculo_despesas
                                                                                            WHERE  classificacao like '3%'
                                                                                            OR classificacao like '4%')
                                                                                    ,(SELECT sum(valor_anterior) as valor_anterior
                                                                                            FROM tmp_calculo_despesas
                                                                                            WHERE  classificacao like '3%'
                                                                                            OR classificacao like '4%')
                                                                                    );

    ELSE
        INSERT INTO tmp_calculo_despesas(classificacao,valor) VALUES('despesas_orcamentarias'
                                                                    , (SELECT sum(valor) as valor
                                                                             FROM tmp_calculo_despesas
                                                                             WHERE  classificacao like '3%'
                                                                             OR classificacao like '4%')
                                                                    );
    END IF;


    IF (stTipoDespesa = 'E') THEN
    --INSERT para colocar inscricao_restos_pagar_processados e inscricao_restos_pagar_nao_processados
        INSERT INTO tmp_calculo_despesas(classificacao,valor,valor_anterior) VALUES('inscricao_restos_pagar_processados'
                                                                    , (SELECT
                                                                            (
                                                                            SUM(liquidado_ano) - SUM(pago_ano) 
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    , (SELECT
                                                                            (
                                                                            SUM(liquidado_ano_anterior) - SUM(pago_ano_anterior) 
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    );
        INSERT INTO tmp_calculo_despesas(classificacao,valor,valor_anterior) VALUES('inscricao_restos_pagar_nao_processados'
                                                                    , (SELECT
                                                                            (
                                                                            (SUM(empenhado_ano) - SUM(anulado_ano)) - SUM(liquidado_ano)
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    , (SELECT
                                                                            (
                                                                            (SUM(empenhado_ano_anterior) - SUM(anulado_ano_anterior)) - SUM(liquidado_ano_anterior)
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    );
    END IF;

    IF (stTipoDespesa = 'L') THEN
    --INSERT para colocar inscricao_restos_pagar_processados
        INSERT INTO tmp_calculo_despesas(classificacao,valor,valor_anterior) VALUES('inscricao_restos_pagar_processados'
                                                                    , (SELECT
                                                                            (
                                                                            SUM(liquidado_ano) - SUM(pago_ano) 
                                                                            )
                                                                        FROM tmp_calculo_despesas)
                                                                    , (SELECT
                                                                            (
                                                                            SUM(liquidado_ano_anterior) - SUM(pago_ano_anterior) 
                                                                            )
                                                                        FROM tmp_calculo_despesas
                                                                       WHERE classificacao <> 'despesas_orcamentarias')
                                                                    );
    END IF;

--CRIANDO TABELA PARA DESPESAS a partir da tmp_calculo_despesas
    stSql := ' CREATE TEMPORARY TABLE tmp_despesas AS
                SELECT 
                    CASE
                            WHEN num_recurso like ''0001''
                            THEN ''despesa_recurso_livre''
                            WHEN num_recurso not like ''0001''
                            THEN ''despesa_recurso_vinculado''
                            WHEN classificacao = ''despesas_orcamentarias''
                            THEN ''despesas_orcamentarias''
                            WHEN classificacao = ''inscricao_restos_pagar_processados''
                            THEN ''inscricao_restos_pagar_processados''
                            WHEN classificacao = ''inscricao_restos_pagar_nao_processados''
                            THEN ''inscricao_restos_pagar_nao_processados''
                    END as descricao
                    ,SUM(valor) as valor
    ';
    IF (stExercicio::integer >= 2014) THEN
        stSql := stSql || ',SUM(COALESCE(valor_anterior, 0.00)) as valor_anterior';
    END IF;
    stSql := stSql || '
                FROM tmp_calculo_despesas
                GROUP BY descricao
    ';
    EXECUTE stSql;

--FILTRO LANÇAMENTO DIFERENTE DE TIPO 'M'
stFiltroTrans := 'cod_entidade IN ('|| stCodEntidade ||')
                 AND cod_estrutural SIMILAR TO ''4.5.1.1%|3.5.1.1%|4.5.1.2%|3.5.1.2%|4.5.1.3%|3.5.1.3%''
                 AND tipo != ''M'' ';

stFiltroExtra := 'cod_entidade IN ('|| stCodEntidade ||') 
                 AND (      cod_estrutural NOT SIMILAR TO ''4.5.1.1%|3.5.1.1%|4.5.1.2%|3.5.1.2%|4.5.1.3%|3.5.1.3%|2.1.8%|1.1.3.5%|1.1.3.8%''
                       OR ( cod_estrutural LIKE ''2.1.8%''   AND tipo != ''M'' )
                       OR ( cod_estrutural LIKE ''1.1.3.5%'' AND tipo != ''M'' )
                       OR ( cod_estrutural LIKE ''1.1.3.8%'' AND tipo != ''M'' )
                     ) ';

--Criando tabela para armazenar saldos referente ao cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_saldo AS
                SELECT descricao
                       ,(sum(vl_saldo_anterior)) AS vl_saldo_anterior
                       ,(sum(vl_saldo_debitos)) AS vl_saldo_debitos
                       ,(sum(vl_saldo_creditos)) AS vl_saldo_creditos
                       ,(sum(vl_saldo_atual)) AS vl_saldo_atual
                       ,(sum(vl_saldo_debitos_anterior)) AS vl_saldo_debitos_anterior
                       ,(sum(vl_saldo_creditos_anterior)) AS vl_saldo_creditos_anterior
                       ,(sum(vl_saldo_atual_anterior)) AS vl_saldo_atual_anterior
                       ,(sum(vl_saldo_inicial_anterior)) AS vl_saldo_inicial_anterior
                  FROM (
                        SELECT
                        CASE';
                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN cod_estrutural         like ''2.1.8%'' ';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''1.1.3%'' AND indicador_superavit = ''financeiro''';
                        END IF;
                        stSql := stSql || ' THEN ''depositos_restituiveis_valores_vinculados''';

                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN (cod_estrutural   like ''1.1.3.5%'' OR cod_estrutural   like ''1.1.3.8%'' ) AND indicador_superavit = ''financeiro''';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''1.1.3%'' AND indicador_superavit = ''financeiro''';
                        END IF;
                        stSql := stSql || ' THEN ''depositos_restituiveis_valores_vinculados_saldo''';

                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN (cod_estrutural   like ''1.1.3.5.0%'' OR cod_estrutural   like ''1.1.3.8.0%'' )
                                                     THEN ''outros_recebimentos_ext''';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''2.1.8%'' AND indicador_superavit = ''financeiro''
                                                     THEN ''valores_restituiveis''';
                        END IF;

                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN (cod_estrutural   like ''1.1.1%'' OR cod_estrutural   like ''1.1.4.1%'' ) AND cod_sistema IN (1,2)';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''1.1.1.0%''';
                        END IF;
                        stSql := stSql || '
                                THEN ''caixa_equivalentes''
                            WHEN cod_estrutural          like ''4.5.1.1.0%''
                                THEN ''transferencias_recebidas_orcamentaria''
                            WHEN cod_estrutural          like ''3.5.1.1.0%''
                                THEN ''tranferencias_concedidas_orcamentaria''
                            WHEN cod_estrutural          like ''4.5.1.2.0%''
                                THEN ''transferencias_recebidas_independentes_orcamentaria''
                            WHEN cod_estrutural          like ''3.5.1.2.0%''
                                THEN ''transferencias_concedidas_independentes_orcamentaria''
                            WHEN cod_estrutural          like ''4.5.1.3.0%''
                                THEN ''transferencias_recebidas_cobertura''
                            WHEN cod_estrutural          like ''3.5.1.3%''
                                THEN ''transferencias_concedidas_cobertura''
                            WHEN cod_estrutural          like ''6.3.2.2.0%''
                                THEN ''pagamento_restos_pagar_processados''
                            WHEN cod_estrutural          like ''6.3.1.4.0%''
                                THEN ''pagamento_restos_pagar_nao_processados''
                        END as descricao
                        ,(sum(vl_saldo_anterior)) AS vl_saldo_anterior
                        ,(sum(vl_saldo_debitos)) AS vl_saldo_debitos
                        ,(sum(vl_saldo_creditos)) AS vl_saldo_creditos
                        ,(sum(vl_saldo_atual)) AS vl_saldo_atual
                        ,0.00 AS vl_saldo_debitos_anterior
                        ,0.00 AS vl_saldo_creditos_anterior
                        ,0.00 AS vl_saldo_atual_anterior
                        ,0.00 AS vl_saldo_inicial_anterior
                        ,cod_estrutural
                        FROM ( SELECT *
                                 FROM contabilidade.fn_rl_balancete_verificacao_transferencias('|| quote_literal(stExercicio) ||'
                                                                                               ,'|| quote_literal(stFiltroTrans) ||'
                                                                                               ,'|| quote_literal(dtInicial) ||'
                                                                                               ,'|| quote_literal(dtFinal) ||'
                                                                                               ,''A''::CHAR
                                                                                               ) AS retorno
                                                                                               ( cod_estrutural     varchar
                                                                                               ,nivel               integer
                                                                                               ,nom_conta           varchar
                                                                                               ,cod_sistema         integer
                                                                                               ,indicador_superavit char(12)
                                                                                               ,vl_saldo_anterior   numeric
                                                                                               ,vl_saldo_debitos    numeric
                                                                                               ,vl_saldo_creditos   numeric
                                                                                               ,vl_saldo_atual      numeric
                                                                                               )
                                WHERE cod_estrutural SIMILAR TO ''4.5.1.1.0%|3.5.1.1.0%|4.5.1.2.0%|3.5.1.2.0%|4.5.1.3.0%|3.5.1.3%''
                                UNION
                               SELECT retorno.*
                                 FROM contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicio) ||'
                                                                               ,'|| quote_literal(stFiltroExtra) ||'
                                                                               ,'|| quote_literal(dtInicial) ||'
                                                                               ,'|| quote_literal(dtFinal) ||'
                                                                               ,''A''::CHAR
                                                                               ) AS retorno
                                                                               ( cod_estrutural     varchar
                                                                               ,nivel               integer
                                                                               ,nom_conta           varchar
                                                                               ,cod_sistema         integer
                                                                               ,indicador_superavit char(12)
                                                                               ,vl_saldo_anterior   numeric
                                                                               ,vl_saldo_debitos    numeric
                                                                               ,vl_saldo_creditos   numeric
                                                                               ,vl_saldo_atual      numeric
                                                                               )
                                 JOIN contabilidade.plano_conta
                                   ON plano_conta.exercicio = '|| quote_literal(stExercicio) ||'
                                  AND plano_conta.cod_estrutural = retorno.cod_estrutural
                                  AND plano_conta.escrituracao ilike ''anali%''
                                WHERE retorno.cod_estrutural NOT SIMILAR TO ''4.5.1.1.0%|3.5.1.1.0%|4.5.1.2.0%|3.5.1.2.0%|4.5.1.3.0%|3.5.1.3%''
                             ) AS retorno
                    GROUP BY descricao
                           , cod_estrutural 

                     UNION ALL

                        SELECT
                        CASE';
                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN cod_estrutural         like ''2.1.8%'' ';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''1.1.3%'' AND indicador_superavit = ''financeiro''';
                        END IF;
                        stSql := stSql || ' THEN ''depositos_restituiveis_valores_vinculados''';

                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN (cod_estrutural   like ''1.1.3.5%'' OR cod_estrutural   like ''1.1.3.8%'' ) AND indicador_superavit = ''financeiro''';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''1.1.3%'' AND indicador_superavit = ''financeiro''';
                        END IF;
                        stSql := stSql || ' THEN ''depositos_restituiveis_valores_vinculados_saldo''';

                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN (cod_estrutural   like ''1.1.3.5.0%'' OR cod_estrutural   like ''1.1.3.8.0%'' )
                                                     THEN ''outros_recebimentos_ext''';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''2.1.8%'' AND indicador_superavit = ''financeiro''
                                                     THEN ''valores_restituiveis''';
                        END IF;

                        IF (stExercicio::integer >= 2014)
                            THEN stSql := stSql || ' WHEN (cod_estrutural   like ''1.1.1%'' OR cod_estrutural   like ''1.1.4.1%'' ) AND cod_sistema IN (1,2)';
                            ELSE stSql := stSql || ' WHEN cod_estrutural         like ''1.1.1.0%''';
                        END IF;
                        stSql := stSql || '
                                THEN ''caixa_equivalentes''
                            WHEN cod_estrutural          like ''4.5.1.1.0%''
                                THEN ''transferencias_recebidas_orcamentaria''
                            WHEN cod_estrutural          like ''3.5.1.1.0%''
                                THEN ''tranferencias_concedidas_orcamentaria''
                            WHEN cod_estrutural          like ''4.5.1.2.0%''
                                THEN ''transferencias_recebidas_independentes_orcamentaria''
                            WHEN cod_estrutural          like ''3.5.1.2.0%''
                                THEN ''transferencias_concedidas_independentes_orcamentaria''
                            WHEN cod_estrutural          like ''4.5.1.3.0%''
                                THEN ''transferencias_recebidas_cobertura''
                            WHEN cod_estrutural          like ''3.5.1.3%''
                                THEN ''transferencias_concedidas_cobertura''
                            WHEN cod_estrutural          like ''6.3.2.2.0%''
                                THEN ''pagamento_restos_pagar_processados''
                            WHEN cod_estrutural          like ''6.3.1.4.0%''
                                THEN ''pagamento_restos_pagar_nao_processados''
                        END as descricao
                        ,0.00 AS vl_saldo_anterior
                        ,0.00 AS vl_saldo_debitos
                        ,0.00 AS vl_saldo_creditos
                        ,0.00 AS vl_saldo_atual
                        ,(sum(vl_saldo_debitos)) AS vl_saldo_debitos_anterior
                        ,(sum(vl_saldo_creditos)) AS vl_saldo_creditos_anterior
                        ,(sum(vl_saldo_atual)) AS vl_saldo_atual_anterior
                        ,(sum(vl_saldo_anterior)) AS vl_saldo_inicial_anterior
                        ,cod_estrutural
                        FROM ( SELECT *
                                 FROM contabilidade.fn_rl_balancete_verificacao_transferencias('|| quote_literal(stExercicioAnterior) ||'
                                                                                              ,'|| quote_literal(stFiltroTrans) ||'
                                                                                              ,'|| quote_literal(dtInicialAnterior) ||'
                                                                                              ,'|| quote_literal(dtFinalAnterior) ||'
                                                                                              ,''A''::CHAR
                                                                                              ) AS retorno
                                                                                              (cod_estrutural      varchar
                                                                                              ,nivel               integer
                                                                                              ,nom_conta           varchar
                                                                                              ,cod_sistema         integer
                                                                                              ,indicador_superavit char(12)
                                                                                              ,vl_saldo_anterior   numeric
                                                                                              ,vl_saldo_debitos    numeric
                                                                                              ,vl_saldo_creditos   numeric
                                                                                              ,vl_saldo_atual      numeric
                                                                                              )
                                WHERE cod_estrutural SIMILAR TO ''4.5.1.1.0%|3.5.1.1.0%|4.5.1.2.0%|3.5.1.2.0%|4.5.1.3.0%|3.5.1.3%''
                                UNION
                               SELECT retorno.*
                                 FROM contabilidade.fn_rl_balancete_verificacao('|| quote_literal(stExercicioAnterior) ||'
                                                                               ,'|| quote_literal(stFiltroExtra) ||'
                                                                               ,'|| quote_literal(dtInicialAnterior) ||'
                                                                               ,'|| quote_literal(dtFinalAnterior) ||'
                                                                               ,''A''::CHAR
                                                                               ) AS retorno
                                                                               (cod_estrutural      varchar
                                                                               ,nivel               integer
                                                                               ,nom_conta           varchar
                                                                               ,cod_sistema         integer
                                                                               ,indicador_superavit char(12)
                                                                               ,vl_saldo_anterior   numeric
                                                                               ,vl_saldo_debitos    numeric
                                                                               ,vl_saldo_creditos   numeric
                                                                               ,vl_saldo_atual      numeric
                                                                               )
                                 JOIN contabilidade.plano_conta
                                   ON plano_conta.exercicio = '|| quote_literal(stExercicio) ||'
                                  AND plano_conta.cod_estrutural = retorno.cod_estrutural
                                  AND plano_conta.escrituracao ilike ''anali%''
                                WHERE retorno.cod_estrutural NOT SIMILAR TO ''4.5.1.1.0%|3.5.1.1.0%|4.5.1.2.0%|3.5.1.2.0%|4.5.1.3.0%|3.5.1.3%''
                             ) AS retorno
                    GROUP BY descricao
                           , cod_estrutural
                       ) AS fluxo_caixa_saldo
                 WHERE descricao IS NOT NULL
              GROUP BY descricao
    ';

EXECUTE stSql;

--SELECT para armazenar saldos referente ao cod_estrutural relativos às Transferências, pois não podem possuir histórico = 8
--Feito o update na tabela fluxo_caixa_saldo para armazenar todos os saldos corretamente

stSql := 'SELECT 
            CASE
                WHEN cod_estrutural          like ''6.3.2.2.0%''
                    THEN ''pagamento_restos_pagar_processados''
                WHEN cod_estrutural          like ''6.3.1.4.0%''
                    THEN ''pagamento_restos_pagar_nao_processados''
            END as descricao
            ,ABS(sum(vl_saldo_anterior)) as vl_saldo_anterior
            ,ABS(sum(vl_saldo_debitos)) as vl_saldo_debitos
            ,ABS(sum(vl_saldo_creditos)) as vl_saldo_creditos
            ,ABS(sum(vl_saldo_atual)) as vl_saldo_atual
            FROM ( SELECT cod_estrutural
                        , 0.00 AS vl_saldo_anterior
                        , vl_saldo_debitos
                        , vl_saldo_creditos
                        , vl_saldo_atual
                     FROM contabilidade.fn_rl_balancete_verificacao_transferencias('|| quote_literal(stExercicio) ||'
                                                                                  ,''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                                                  ,'|| quote_literal(dtInicial) ||'
                                                                                  ,'|| quote_literal(dtFinal) ||'
                                                                                  ,''A''::CHAR
                                                                                  ) AS retorno
                                                                                  (cod_estrutural      varchar
                                                                                  ,nivel               integer
                                                                                  ,nom_conta           varchar
                                                                                  ,cod_sistema         integer
                                                                                  ,indicador_superavit char(12)
                                                                                  ,vl_saldo_anterior   numeric
                                                                                  ,vl_saldo_debitos    numeric
                                                                                  ,vl_saldo_creditos   numeric
                                                                                  ,vl_saldo_atual      numeric
                                                                                  )

                    UNION

                   SELECT cod_estrutural
                        , vl_saldo_atual AS vl_saldo_anterior
                        , 0.00 AS vl_saldo_debitos
                        , 0.00 AS vl_saldo_creditos
                        , 0.00 AS vl_saldo_atual
                     FROM contabilidade.fn_rl_balancete_verificacao_transferencias('|| quote_literal(stExercicioAnterior) ||'
                                                                                  ,''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                                                  ,'|| quote_literal(dtInicialAnterior) ||'
                                                                                  ,'|| quote_literal(dtFinalAnterior) ||'
                                                                                  ,''A''::CHAR
                                                                                  ) AS retorno
                                                                                  (cod_estrutural      varchar
                                                                                  ,nivel               integer
                                                                                  ,nom_conta           varchar
                                                                                  ,cod_sistema         integer
                                                                                  ,indicador_superavit char(12)
                                                                                  ,vl_saldo_anterior   numeric
                                                                                  ,vl_saldo_debitos    numeric
                                                                                  ,vl_saldo_creditos   numeric
                                                                                  ,vl_saldo_atual      numeric
                                                                                  )
                 ) AS retorno
           WHERE cod_estrutural SIMILAR TO ''6.3.2.2.0%|6.3.1.4.0%''
        GROUP BY descricao
    ';

FOR reRegistroAux IN EXECUTE stSql
LOOP
    UPDATE fluxo_caixa_saldo SET vl_saldo_anterior = reRegistroAux.vl_saldo_anterior,
                                 vl_saldo_debitos = reRegistroAux.vl_saldo_debitos,
                                 vl_saldo_creditos = reRegistroAux.vl_saldo_creditos,
                                 vl_saldo_atual = reRegistroAux.vl_saldo_atual
                            WHERE descricao = reRegistroAux.descricao;
END LOOP;


--Criando tabela para juntar todos os resultados
IF (stExercicio::integer >= 2014) THEN
    stSql :=' CREATE TEMPORARY TABLE resultado_financeiro AS
            SELECT * FROM(
                            SELECT
                                    descricao
                                    ,vl_saldo_anterior          as valor_anterior
                                    ,vl_saldo_debitos           as valor_debito
                                    ,vl_saldo_creditos          as valor_credito
                                    ,vl_saldo_atual             as valor
                                    ,vl_saldo_debitos_anterior  as valor_debito_anterior
                                    ,vl_saldo_creditos_anterior as valor_credito_anterior
                                    ,vl_saldo_atual_anterior    as valor_atual_anterior
                                    ,vl_saldo_inicial_anterior  as valor_inicial_anterior
                                    FROM fluxo_caixa_saldo
                    UNION
                            SELECT
                                    descricao
                                    ,arrecadado_periodo_anterior    as valor_anterior
                                    ,0                              as valor_debito
                                    ,0                              as valor_credito
                                    ,arrecadado_periodo             as valor
                                    ,0                              as valor_debito_anterior
                                    ,0                              as valor_credito_anterior
                                    ,0                              as valor_atual_anterior
                                    ,0                              as valor_inicial_anterior
                                    FROM fluxo_caixa_receita
                    UNION
                            SELECT
                                    descricao
                                    ,valor_anterior             as valor_anterior
                                    ,0                          as valor_debito
                                    ,0                          as valor_credito
                                    ,valor                      as valor
                                    ,0                          as valor_debito_anterior
                                    ,0                          as valor_credito_anterior
                                    ,0                          as valor_atual_anterior
                                    ,0                          as valor_inicial_anterior
                                    FROM tmp_despesas
                    )as tbl
            WHERE descricao <> ''''
            ORDER BY descricao
            ';
    EXECUTE stSql;
    
ELSE
    stSql :=' CREATE TEMPORARY TABLE resultado_financeiro AS
            SELECT * FROM(
                            SELECT
                                    descricao
                                    ,vl_saldo_anterior  as valor_anterior
                                    ,vl_saldo_debitos   as valor_debito
                                    ,vl_saldo_creditos  as valor_credito
                                    ,vl_saldo_atual     as valor
                                    FROM fluxo_caixa_saldo
                    UNION
                            SELECT
                                    descricao
                                    ,0                  as valor_anterior
                                    ,0                  as valor_debito
                                    ,0                  as valor_credito
                                    ,arrecadado_periodo as valor
                                    FROM fluxo_caixa_receita
                    UNION
                            SELECT
                                    descricao
                                    ,0 as valor_anterior
                                    ,0 as valor_debito
                                    ,0 as valor_credito
                                    ,valor
                                    FROM tmp_despesas
                    )as tbl
            WHERE descricao <> ''''
            ORDER BY descricao
            ';
    EXECUTE stSql;
END IF;

--CRIANDO TABELA PARA RESULTADO DO RELATORIO 
    stSql := 'CREATE TEMPORARY TABLE relatorio_financeiro
                (
                    ordem                           INTEGER
                    ,descricao_ingressos            VARCHAR
                    ,valor_ingresso                 NUMERIC
                    ,valor_ingresso_anterior        NUMERIC
                    ,descricao_dispendios           VARCHAR
                    ,valor_dispendios               NUMERIC
                    ,valor_dispendios_anterior      NUMERIC
                )
        ';

    EXECUTE stSql;

--CRIANDO DESCRICOES
    --RECEITAS POR RECURSO
    arDescricao[0] := 'Receita Orçamentária(I)';
    arDescricao[1] := '';
    arDescricao[2] := 'Ordinária';
    arDescricao[3] := 'Vinculada';
    arDescricao[4] := '';
    arDescricao[5] := '';
    arDescricao[6] := '(-) Deduções da Receita Orçamentária';
    arDescricao[7] := 'Ordinária';
    arDescricao[8] := 'Vinculada';
    arDescricao[9] := '';
    arDescricao[10] := '';
    arDescricao[11] := 'Transferências Financeiras Recebidas (II)';
    arDescricao[12] := 'Transferências Recebidas para a Execução Orçamentária';
    arDescricao[13] := 'Transferências Recebidas Independentes de Execução Orçamentária - Inter OFSS';
    arDescricao[14] := 'Transferências Recebidas para Cobertura do Déficit  Financeiro do RPPS';
    arDescricao[15] := '';
    arDescricao[16] := '';
    arDescricao[17] := 'Recebimentos Extra-Orçamentários (III)';
    arDescricao[18] := 'Inscrição de Restos a Pagar Processados';
    arDescricao[19] := 'Inscrição de Restos a Pagar Não Processados';
    arDescricao[20] := 'Depósitos Restituíveis e Valores Vinculados';
    IF (stExercicio::integer >= 2014) THEN
         arDescricao[21] := 'Outros Recebimentos Extraorçamentários';
    ELSE arDescricao[21] := 'Valores Restituiveis';
    END IF;
    arDescricao[22] := '';
    arDescricao[23] := 'Saldo em Espécie do Exercício Anterior (IV)';
    arDescricao[24] := 'Caixa e Equivalentes de Caixa';
    arDescricao[25] := 'Depósitos Restituíveis e Valores Vinculados';    
    arDescricao[26] := '';
    arDescricao[27] := 'TOTAL (V) = (I+II+III+IV)';
    
    --DESPESAS POR RECURSO
    arDescricaoDespesas[0] := 'Despesa Orçamentária(VI)';
    arDescricaoDespesas[1] := '';
    arDescricaoDespesas[2] := 'Ordinária';
    arDescricaoDespesas[3] := 'Vinculada';
    arDescricaoDespesas[4] := '';
    arDescricaoDespesas[5] := '';
    arDescricaoDespesas[6] := '';
    arDescricaoDespesas[7] := '';
    arDescricaoDespesas[8] := '';
    arDescricaoDespesas[9] := '';
    arDescricaoDespesas[10] := '';
    arDescricaoDespesas[11] := 'Transferências Financeiras Concedidas (VII)';
    arDescricaoDespesas[12] := 'Tranferências Concedidas para a Execução Orçamentária';
    arDescricaoDespesas[13] := 'Tranferências Concedidas Independentes de Execução Orçamentária';
    arDescricaoDespesas[14] := 'Transferências Concedidas para Cobertura do Déficit Financeiro do RPPS';
    arDescricaoDespesas[15] := '';
    arDescricaoDespesas[16] := '';
    arDescricaoDespesas[17] := 'Pagamentos Extra-Orçamentários (VIII)';
    arDescricaoDespesas[18] := 'Pagamentos de Restos a Pagar Processados';
    arDescricaoDespesas[19] := 'Pagamentos de Restos a Pagar Não Processados';
    arDescricaoDespesas[20] := 'Depósitos Restituíveis e Valores Vinculados';
    IF (stExercicio::integer >= 2014) THEN
         arDescricaoDespesas[21] := 'Outros Pagamentos Extraorçamentários';
    ELSE arDescricaoDespesas[21] := 'Valores Restituiveis';
    END IF;
    arDescricaoDespesas[22] := '';
    arDescricaoDespesas[23] := 'Saldo em Espécie para o Exercício Seguinte (IX)';
    arDescricaoDespesas[24] := 'Caixa e Equivalentes de Caixa';
    arDescricaoDespesas[25] := 'Depósitos Restituíveis e Valores Vinculados';    
    arDescricaoDespesas[26] := '';
    arDescricaoDespesas[27] := 'TOTAL (X) = (VI+VII+VIII+IX)';
    
    --Armazenar valores da tabela resultado_financeiro em um array de acordo com a regra pra serem inseridos na tabela relatorio_financeiro
    arDescricaoValores[0] := 'receita_orcamentaria';
    arDescricaoValores[1] := '';
    arDescricaoValores[2] := 'recurso_livre';
    arDescricaoValores[3] := 'recurso_vinculado';
    arDescricaoValores[4] := '';
    arDescricaoValores[5] := '';
    arDescricaoValores[6] := '';
    arDescricaoValores[7] := 'deducoes_recurso_livre';
    arDescricaoValores[8] := 'deducoes_recurso_vinculado';
    arDescricaoValores[9] := '';
    arDescricaoValores[10] := '';
    arDescricaoValores[11] := '';
    arDescricaoValores[12] := 'transferencias_recebidas_orcamentaria';
    arDescricaoValores[13] := 'transferencias_recebidas_independentes_orcamentaria';
    arDescricaoValores[14] := 'transferencias_recebidas_cobertura';
    arDescricaoValores[15] := '';
    arDescricaoValores[16] := '';
    arDescricaoValores[17] := '';
    arDescricaoValores[18] := 'inscricao_restos_pagar_processados';
    arDescricaoValores[19] := 'inscricao_restos_pagar_nao_processados';
    arDescricaoValores[20] := 'depositos_restituiveis_valores_vinculados';
    IF (stExercicio::integer >= 2014) THEN
         arDescricaoValores[21] := 'outros_recebimentos_ext';
    ELSE arDescricaoValores[21] := 'valores_restituiveis';
    END IF;
    arDescricaoValores[21] := 'valores_restituiveis';
    arDescricaoValores[22] := '';
    arDescricaoValores[23] := '';
    arDescricaoValores[24] := 'caixa_equivalentes';
    arDescricaoValores[25] := 'depositos_restituiveis_valores_vinculados';    
    arDescricaoValores[26] := '';
    arDescricaoValores[27] := '';
    
  
    --DESCRICAO DOS CAMPOS PARA CRIAR A RELACAO ENTRE AS TABELAS DESPESAS
    arDescricaoDespesasValores[0] := 'despesas_orcamentarias';
    arDescricaoDespesasValores[1] := '';
    arDescricaoDespesasValores[2] := 'despesa_recurso_livre';
    arDescricaoDespesasValores[3] := 'despesa_recurso_vinculado';
    arDescricaoDespesasValores[4] := '';
    arDescricaoDespesasValores[5] := '';
    arDescricaoDespesasValores[6] := '';
    arDescricaoDespesasValores[7] := '';
    arDescricaoDespesasValores[8] := '';
    arDescricaoDespesasValores[9] := '';
    arDescricaoDespesasValores[10] := '';
    arDescricaoDespesasValores[11] := '';
    arDescricaoDespesasValores[12] := 'tranferencias_concedidas_orcamentaria';
    arDescricaoDespesasValores[13] := 'transferencias_concedidas_independentes_orcamentaria';
    arDescricaoDespesasValores[14] := 'transferencias_concedidas_cobertura';
    arDescricaoDespesasValores[15] := '';
    arDescricaoDespesasValores[16] := '';
    arDescricaoDespesasValores[17] := '';
    arDescricaoDespesasValores[18] := 'pagamento_restos_pagar_processados';
    arDescricaoDespesasValores[19] := 'pagamento_restos_pagar_nao_processados';
    arDescricaoDespesasValores[20] := 'depositos_restituiveis_valores_vinculados';
    IF (stExercicio::integer >= 2014) THEN
         arDescricaoDespesasValores[21] := 'outros_recebimentos_ext';
    ELSE arDescricaoDespesasValores[21] := 'valores_restituiveis';
    END IF;
    arDescricaoDespesasValores[22] := '';
    arDescricaoDespesasValores[23] := '';
    arDescricaoDespesasValores[24] := 'caixa_equivalentes';
    arDescricaoDespesasValores[25] := 'depositos_restituiveis_valores_vinculados';    
    arDescricaoDespesasValores[26] := '';
    arDescricaoDespesasValores[27] := '';


--INSERIR Descricoes na Tabela
    FOR i IN 0..27 LOOP
        INSERT INTO relatorio_financeiro(   ordem
                                            ,descricao_ingressos
                                            ,valor_ingresso
                                            ,valor_ingresso_anterior
                                            ,descricao_dispendios
                                            ,valor_dispendios
                                            ,valor_dispendios_anterior)
                                                                        VALUES( i
                                                                                ,arDescricao[i]
                                                                                ,COALESCE((SELECT valor FROM resultado_financeiro WHERE descricao = arDescricaoValores[i]),0.00)
                                                                                ,COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = arDescricaoValores[i]),0.00)
                                                                                ,arDescricaoDespesas[i]
                                                                                ,COALESCE((SELECT valor FROM resultado_financeiro WHERE descricao = arDescricaoDespesasValores[i]),0.00)
                                                                                ,COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = arDescricaoDespesasValores[i]),0.00)
                                        );
    END LOOP;

--UPDATE para inserir os valores de acordo com a regra de negocio.
    --Passando valor para o valor_anterior para ficar de acordo com a regra da conta
    UPDATE relatorio_financeiro
    SET valor_ingresso = COALESCE((SELECT ABS(valor_credito) FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
        ,valor_ingresso_anterior = COALESCE((SELECT ABS(valor_credito_anterior) FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
    WHERE ordem = 20;

    UPDATE relatorio_financeiro
    SET valor_dispendios = COALESCE((SELECT ABS(valor_debito) FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
        ,valor_dispendios_anterior = COALESCE((SELECT ABS(valor_debito_anterior) FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
    WHERE ordem = 20;

    UPDATE relatorio_financeiro
    SET valor_ingresso = COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados_saldo'),0.00)
        ,valor_ingresso_anterior = COALESCE((SELECT valor_inicial_anterior FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados_saldo'),0.00)
    WHERE ordem = 25;

    UPDATE relatorio_financeiro
    SET valor_dispendios = COALESCE((SELECT valor FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados_saldo'),0.00)
        ,valor_dispendios_anterior = COALESCE((SELECT valor_atual_anterior FROM resultado_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados_saldo'),0.00)
    WHERE ordem = 25;

    IF (stExercicio::integer >= 2014) THEN
         UPDATE relatorio_financeiro
         SET valor_ingresso = COALESCE((SELECT valor_debito FROM resultado_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
           , valor_ingresso_anterior =  COALESCE((SELECT valor_debito_anterior FROM resultado_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
         WHERE ordem = 21;
    ELSE   UPDATE relatorio_financeiro
           SET valor_ingresso =  COALESCE((SELECT valor_debito FROM resultado_financeiro WHERE descricao = 'valores_restituiveis'),0.00)
             , valor_ingresso_anterior =  COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'valores_restituiveis'),0.00)
           WHERE ordem = 21;
    END IF;

    IF (stExercicio::integer >= 2014) THEN
        UPDATE relatorio_financeiro
           SET valor_dispendios = COALESCE((SELECT valor_credito FROM resultado_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
             , valor_dispendios_anterior = COALESCE((SELECT valor_credito_anterior FROM resultado_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
         WHERE ordem = 21;
    ELSE
        UPDATE relatorio_financeiro
           SET valor_dispendios = COALESCE((SELECT valor_credito FROM resultado_financeiro WHERE descricao = 'valores_restituiveis'),0.00)
             , valor_dispendios_anterior = COALESCE((SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'valores_restituiveis'),0.00)
         WHERE ordem = 21;
    END IF;

    UPDATE relatorio_financeiro
    SET valor_ingresso          = COALESCE((SELECT ABS(valor_ingresso) FROM resultado_financeiro WHERE descricao = 'transferencias_recebidas_orcamentaria'),0.00)
      , valor_ingresso_anterior = COALESCE((SELECT ABS(valor_atual_anterior) FROM resultado_financeiro WHERE descricao = 'transferencias_recebidas_orcamentaria'),0.00)
    WHERE ordem = 12;

    UPDATE relatorio_financeiro
    SET valor_ingresso          = COALESCE((SELECT ABS(valor_ingresso) FROM resultado_financeiro WHERE descricao = 'transferencias_recebidas_independentes_orcamentaria'),0.00)
      , valor_ingresso_anterior = COALESCE((SELECT ABS(valor_atual_anterior) FROM resultado_financeiro WHERE descricao = 'transferencias_recebidas_independentes_orcamentaria'),0.00)
    WHERE ordem = 13;

    UPDATE relatorio_financeiro
    SET valor_ingresso          = COALESCE((SELECT ABS(valor_ingresso) FROM resultado_financeiro WHERE descricao = 'transferencias_recebidas_cobertura'),0.00)
      , valor_ingresso_anterior = COALESCE((SELECT ABS(valor_atual_anterior) FROM resultado_financeiro WHERE descricao = 'transferencias_recebidas_cobertura'),0.00)
    WHERE ordem = 14;

    UPDATE relatorio_financeiro
    SET valor_dispendios          = COALESCE((SELECT ABS(valor_dispendios) FROM resultado_financeiro WHERE descricao = 'tranferencias_concedidas_orcamentaria'),0.00)
      , valor_dispendios_anterior = COALESCE((SELECT ABS(valor_atual_anterior) FROM resultado_financeiro WHERE descricao = 'tranferencias_concedidas_orcamentaria'),0.00)
    WHERE ordem = 12;

    UPDATE relatorio_financeiro
    SET valor_dispendios          = COALESCE((SELECT ABS(valor_dispendios) FROM resultado_financeiro WHERE descricao = 'transferencias_concedidas_independentes_orcamentaria'),0.00)
      , valor_dispendios_anterior = COALESCE((SELECT ABS(valor_atual_anterior) FROM resultado_financeiro WHERE descricao = 'transferencias_concedidas_independentes_orcamentaria'),0.00)
    WHERE ordem = 13;

    UPDATE relatorio_financeiro
    SET valor_dispendios          = COALESCE((SELECT ABS(valor_dispendios) FROM resultado_financeiro WHERE descricao = 'transferencias_concedidas_cobertura'),0.00)
      , valor_dispendios_anterior = COALESCE((SELECT ABS(valor_atual_anterior) FROM resultado_financeiro WHERE descricao = 'transferencias_concedidas_cobertura'),0.00)
    WHERE ordem = 14;

    UPDATE relatorio_financeiro
    SET valor_ingresso = (SELECT valor_anterior FROM resultado_financeiro WHERE descricao = 'caixa_equivalentes')
      , valor_ingresso_anterior = (SELECT valor_inicial_anterior FROM resultado_financeiro WHERE descricao = 'caixa_equivalentes')
    WHERE ordem = 24; 

    UPDATE relatorio_financeiro
    SET valor_dispendios = (SELECT valor FROM resultado_financeiro WHERE descricao = 'caixa_equivalentes')
        ,valor_dispendios_anterior = (SELECT valor_atual_anterior FROM resultado_financeiro WHERE descricao = 'caixa_equivalentes')
    WHERE ordem = 24
    AND (select count(cod_lote) as lotes from contabilidade.lote where exercicio = stExercicioAnterior) > 0; 
    
     --Adicionar Somatorio das deduções de receita orcamentarias
    UPDATE relatorio_financeiro
    SET
    valor_ingresso = (SELECT SUM(valor_ingresso)FROM relatorio_financeiro where ordem IN (7,8))
    ,valor_ingresso_anterior = (SELECT SUM(valor_ingresso_anterior) as valor FROM relatorio_financeiro where ordem IN (7,8))
    WHERE ordem IN (6);

    --Receitas Orcamentarias 1.0.0.0 + 2.0.0.0 + 7.0.0.0 - 9.0.0.0
    UPDATE relatorio_financeiro
    SET	    valor_ingresso = (    (SELECT SUM(valor_ingresso)as valor_ingresso FROM relatorio_financeiro WHERE ordem IN (2,3))
                                        -
                                        (SELECT valor_ingresso FROM relatorio_financeiro WHERE ordem IN (6))
                                   )
            ,valor_ingresso_anterior = (    (SELECT SUM(valor_ingresso_anterior)as valor_ingresso_anterior FROM relatorio_financeiro WHERE ordem IN (2,3))
                                            -
                                            (SELECT valor_ingresso_anterior FROM relatorio_financeiro WHERE ordem IN (6))
                                        )
    WHERE ordem = 0;

--CALCULANDO OS TOTAIS DO EXERCICIO ATUAL dos INGRESSOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(ABS(valor_ingresso)) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(ABS(valor_ingresso)) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(ABS(valor_ingresso)) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_ingresso)      as valor_ingresso FROM relatorio_financeiro where ordem in (24,25));
    totalV  := totalI + totalII + totalIII + totalIV;

    UPDATE relatorio_financeiro
    SET valor_ingresso = totalV
    WHERE ordem  = 27;

--CALCULANDO OS TOTAIS DO EXERCICIO ANTERIOR dos INGRESSOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(ABS(valor_ingresso_anterior)) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(ABS(valor_ingresso_anterior)) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(ABS(valor_ingresso_anterior)) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_ingresso_anterior)      as valor_ingresso FROM relatorio_financeiro where ordem in (24,25));
    totalV  := totalI + totalII + totalIII + totalIV;

    UPDATE relatorio_financeiro
    SET valor_ingresso_anterior = totalV
    WHERE ordem  = 27;

--CALCULANDO OS TOTAIS DO EXERCICIO ATUAL dos DISPENDIOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(ABS(valor_dispendios)) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(ABS(valor_dispendios)) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(ABS(valor_dispendios)) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_dispendios)      as valor_ingresso FROM relatorio_financeiro where ordem in (24,25));
    totalV  := totalI + totalII + totalIII + totalIV;

    UPDATE relatorio_financeiro
    SET valor_dispendios = totalV
    WHERE ordem  = 27;

--CALCULANDO OS TOTAIS DO EXERCICIO ANTERIOR dos DISPENDIOS E FAZENDO UPDATE NA TABELA DO RELATORIO
    totalI  := (SELECT SUM(ABS(valor_dispendios_anterior)) as valor_ingresso FROM relatorio_financeiro where ordem in (0));
    totalII := (SELECT SUM(ABS(valor_dispendios_anterior)) as valor_ingresso FROM relatorio_financeiro where ordem in (12,13,14));
    totalIII:= (SELECT SUM(ABS(valor_dispendios_anterior)) as valor_ingresso FROM relatorio_financeiro where ordem in (18,19,20,21));
    totalIV := (SELECT SUM(valor_dispendios_anterior)      as valor_ingresso FROM relatorio_financeiro where ordem in (24,25));
    totalV  := totalI + totalII + totalIII + totalIV;

    UPDATE relatorio_financeiro
    SET valor_dispendios_anterior = totalV
    WHERE ordem  = 27;

--TRANTANDO COLUNAS PARA FICAR EM BRANCO
    UPDATE relatorio_financeiro
    SET
    valor_ingresso = null
    ,valor_ingresso_anterior = null
    WHERE descricao_ingressos = '';

    UPDATE relatorio_financeiro
    SET
    valor_dispendios = null
    ,valor_dispendios_anterior = null
    WHERE descricao_dispendios = '';

    UPDATE relatorio_financeiro
    SET
    valor_ingresso = null
    ,valor_ingresso_anterior = null
    ,valor_dispendios = null
    ,valor_dispendios_anterior = null
    WHERE ordem in (11,17,23);

--RETIRANDO O SINAL DO QUADRO DE Recebimentos Extra-Orçamentários e Pagamentos Extraorçamentários
    UPDATE relatorio_financeiro
    SET 
     valor_ingresso              = (SELECT ABS(valor_ingresso)            FROM relatorio_financeiro WHERE ordem IN (18) ) 
    ,valor_ingresso_anterior     = (SELECT ABS(valor_ingresso_anterior)   FROM relatorio_financeiro WHERE ordem IN (18) )
    ,valor_dispendios            = (SELECT ABS(valor_dispendios)          FROM relatorio_financeiro WHERE ordem IN (18) )
    ,valor_dispendios_anterior   = (SELECT ABS(valor_dispendios_anterior) FROM relatorio_financeiro WHERE ordem IN (18) )
    WHERE ordem IN (18);

    UPDATE relatorio_financeiro
    SET 
     valor_ingresso              = (SELECT ABS(valor_ingresso)            FROM relatorio_financeiro WHERE ordem IN (19) ) 
    ,valor_ingresso_anterior     = (SELECT ABS(valor_ingresso_anterior)   FROM relatorio_financeiro WHERE ordem IN (19) )
    ,valor_dispendios            = (SELECT ABS(valor_dispendios)          FROM relatorio_financeiro WHERE ordem IN (19) )
    ,valor_dispendios_anterior   = (SELECT ABS(valor_dispendios_anterior) FROM relatorio_financeiro WHERE ordem IN (19) )
    WHERE ordem IN (19);

    UPDATE relatorio_financeiro
    SET 
     valor_ingresso              = (SELECT ABS(valor_ingresso)            FROM relatorio_financeiro WHERE ordem IN (20) ) 
    ,valor_ingresso_anterior     = (SELECT ABS(valor_ingresso_anterior)   FROM relatorio_financeiro WHERE ordem IN (20) )
    ,valor_dispendios            = (SELECT ABS(valor_dispendios)          FROM relatorio_financeiro WHERE ordem IN (20) )
    ,valor_dispendios_anterior   = (SELECT ABS(valor_dispendios_anterior) FROM relatorio_financeiro WHERE ordem IN (20) )
    WHERE ordem IN (20);

    UPDATE relatorio_financeiro
    SET 
     valor_ingresso              = (SELECT ABS(valor_ingresso)            FROM relatorio_financeiro WHERE ordem IN (21) ) 
    ,valor_ingresso_anterior     = (SELECT ABS(valor_ingresso_anterior)   FROM relatorio_financeiro WHERE ordem IN (21) )
    ,valor_dispendios            = (SELECT ABS(valor_dispendios)          FROM relatorio_financeiro WHERE ordem IN (21) )
    ,valor_dispendios_anterior   = (SELECT ABS(valor_dispendios_anterior) FROM relatorio_financeiro WHERE ordem IN (21) )
    WHERE ordem IN (21);

    --Retirar os valores do exercicio anterior já que balancete de vereificacao pega os dados do saldo inicial que nao devem ser considerados no relatorio
    --caso nao tenha lancamento no exercicio anterior
    SELECT COUNT(cod_lote)
    INTO inMovimentacao
    FROM contabilidade.lancamento
    WHERE exercicio = (stExercicio::integer-1)::varchar;

    IF (inMovimentacao = 0) THEN
        UPDATE relatorio_financeiro
        SET
        valor_ingresso_anterior     = 0.00
        ,valor_dispendios_anterior  = 0.00
        WHERE ordem NOT IN (1,4,5,9,10,11,15,16,17,22,23,26);
    END IF;

stSql :='SELECT * FROM relatorio_financeiro ORDER by ordem';
    
FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE tmp_despesas;
DROP TABLE relatorio_financeiro;
DROP TABLE resultado_financeiro;
DROP TABLE fluxo_caixa_receita;
DROP TABLE tmp_calculo_despesas;
DROP TABLE fluxo_caixa_saldo;

END;
$$ LANGUAGE 'plpgsql';


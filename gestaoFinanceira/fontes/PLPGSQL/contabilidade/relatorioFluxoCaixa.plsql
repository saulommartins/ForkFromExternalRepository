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
  $Id: relatorioFluxoCaixa.plsql 65969 2016-07-04 20:29:47Z michel $
*/


CREATE OR REPLACE FUNCTION contabilidade.relatorioFluxoCaixa ( VARCHAR,VARCHAR,VARCHAR,VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio             ALIAS FOR $1;
    dtInicial               ALIAS FOR $2;
    dtFinal                 ALIAS FOR $3;
    stCodEntidade           ALIAS FOR $4;
    stSql                   VARCHAR := '';
    stExercicioAnterior     VARCHAR := ''; 
    dtInicialAnterior       VARCHAR := '';
    dtFinalAnterior         VARCHAR := '';
    reRegistro              RECORD;
    arDescricao             VARCHAR[];
    arDescricaoAux          VARCHAR[];
    i                       INTEGER;
    valoresAux              NUMERIC;
    valoresAnteriorAux      NUMERIC;

    stFiltroTrans           VARCHAR := '';
    stFiltroExtra           VARCHAR := '';
BEGIN


stExercicioAnterior     := (to_number(stExercicio,'9999')-1)::varchar;
dtInicialAnterior       := to_char(to_date(dtInicial::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');
dtFinalAnterior         := to_char(to_date(dtFinal::text,'dd/mm/yyyy')- interval '1 year','dd/mm/yyyy');

--Criando tabela para armazerar AS receitas referente a cada cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_receita AS
               SELECT descricao 
                    , SUM(arrecadado_periodo) AS valor
                    , SUM(arrecadado_periodo_anterior) AS valor_anterior
                 FROM (
                       SELECT CASE WHEN cod_estrutural = ''1.1.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''7.1.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.1.0.0.00.00.00.00.00''
                                   THEN ''receita_tributaria''
                                   WHEN cod_estrutural = ''1.2.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''7.2.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.2.0.0.00.00.00.00.00''
                                   THEN ''receita_contribuicoes''
                                   WHEN (receita IS NOT NULL AND cod_estrutural ILIKE ''1.3.%'' AND cod_estrutural NOT SIMILAR TO (''1.3.2.5%|1.3.2.6%|1.3.2.7%|1.3.2.8%''))
                                     OR (receita IS NOT NULL AND cod_estrutural ILIKE ''7.3.%'' AND cod_estrutural NOT SIMILAR TO (''7.3.2.5%|7.3.2.6%|7.3.2.7%|7.3.2.8%''))
                                   THEN ''receita_patrimonial''
                                   WHEN cod_estrutural = ''1.4.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''7.4.0.0.00.00.00.00.00''
                                   THEN ''receita_agropecuaria''
                                   WHEN cod_estrutural = ''1.5.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''7.5.0.0.00.00.00.00.00''
                                   THEN ''receita_industrial''
                                   WHEN cod_estrutural = ''1.6.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''7.6.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.6.0.0.00.00.00.00.00''
                                   THEN ''receita_servicos''
                                   WHEN cod_estrutural = ''1.3.2.5.00.00.00.00.00''
                                     OR cod_estrutural = ''1.3.2.6.00.00.00.00.00''
                                     OR cod_estrutural = ''1.3.2.7.00.00.00.00.00''
                                     OR cod_estrutural = ''1.3.2.8.00.00.00.00.00''
                                     OR cod_estrutural = ''7.3.2.5.00.00.00.00.00''
                                     OR cod_estrutural = ''7.3.2.6.00.00.00.00.00''
                                     OR cod_estrutural = ''7.3.2.7.00.00.00.00.00''
                                     OR cod_estrutural = ''7.3.2.8.00.00.00.00.00''
                                     OR cod_estrutural = ''2.5.4.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.4.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.3.0.0.00.00.00.00.00''
                                   THEN ''remuneracao_disponibilidades''
                                   WHEN cod_estrutural = ''1.9.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''7.9.0.0.00.00.00.00.00''
                                   THEN ''outras_receitas_derivadas''
                                   WHEN cod_estrutural = ''2.5.3.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.5.5.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.5.6.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.5.7.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.5.9.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.3.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.5.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.6.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.7.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.9.0.00.00.00.00.00''
                                   THEN ''outras_receitas_derivadas''
                                   WHEN cod_estrutural = ''1.7.2.1.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.6.1.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.2.1.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.6.1.00.00.00.00.00''
                                   THEN ''transferencia_uniao''
                                   WHEN cod_estrutural = ''1.7.2.2.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.6.2.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.2.2.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.6.2.00.00.00.00.00''
                                   THEN ''transferencia_estados_df''
                                   WHEN cod_estrutural = ''1.7.2.3.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.6.3.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.2.3.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.6.3.00.00.00.00.00''
                                   THEN ''transferencia_municipios''
                                   WHEN cod_estrutural = ''1.7.2.4.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.3.0.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.4.0.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.5.0.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.6.4.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.6.5.00.00.00.00.00''
                                     OR cod_estrutural = ''1.7.7.0.00.00.00.00.00''
                                     OR cod_estrutural = ''7.7.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.2.4.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.3.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.4.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.5.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.6.4.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.6.5.00.00.00.00.00''
                                     OR cod_estrutural = ''9.1.7.7.0.00.00.00.00.00''
                                     OR cod_estrutural = ''9.7.7.0.0.00.00.00.00.00''
                                   THEN ''outras_transferencias''
                                   WHEN cod_estrutural = ''2.2.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.2.0.0.00.00.00.00.00''
                                   THEN ''alienacao_bens''
                                   WHEN cod_estrutural = ''2.3.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.3.0.0.00.00.00.00.00''
                                   THEN ''amortizacao_emprestimos_financiamentos_concedidos''
                                   WHEN cod_estrutural = ''2.1.0.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.1.0.0.00.00.00.00.00''
                                   THEN ''operacao_credito''
                                   WHEN cod_estrutural = ''2.5.2.1.00.00.00.00.00''
                                     OR cod_estrutural = ''2.5.2.2.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.2.1.00.00.00.00.00''
                                     OR cod_estrutural = ''8.5.2.2.00.00.00.00.00''
                                   THEN ''integracao_capital_social_empresas_dependentes''
                                   WHEN cod_estrutural = ''2.4.2.1.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.2.2.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.2.3.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.7.1.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.7.2.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.7.3.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.3.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.4.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.5.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.6.0.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.7.4.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.7.5.00.00.00.00.00''
                                     OR cod_estrutural = ''2.4.8.0.00.00.00.00.00''
                                     OR cod_estrutural = ''8.4.0.0.00.00.00.00.00''
                                   THEN ''transferencias_capital_recebidas''
                               END AS descricao
                            , retorno.arrecadado_periodo
                            , COALESCE(exercicio_anterior.arrecadado_periodo_anterior, 0.00) AS arrecadado_periodo_anterior
                         FROM orcamento.fn_balancete_receita( '''||stExercicio||'''
                                                            , ''''
                                                            , '''||dtInicial||'''
                                                            , '''||dtFinal||'''
                                                            , '''||stCodEntidade||'''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            ,''''
                                                            )
                                                         AS retorno
                                                            ( cod_estrutural      VARCHAR
                                                            , receita             INTEGER
                                                            , recurso             VARCHAR
                                                            , descricao           VARCHAR
                                                            , valor_previsto      NUMERIC
                                                            , arrecadado_periodo  NUMERIC
                                                            , arrecadado_ano      NUMERIC
                                                            , diferenca           NUMERIC
                                                            )
                    LEFT JOIN orcamento.fn_balancete_receita( '''||stExercicioAnterior||'''
                                                            , ''''
                                                            , '''||dtInicialAnterior||'''
                                                            , '''||dtFinalAnterior||'''
                                                            , '''||stCodEntidade||'''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            , ''''
                                                            )
                                                         AS exercicio_anterior
                                                            ( cod_estrutural_anterior      VARCHAR
                                                            , receita_anterior             INTEGER
                                                            , recurso_anterior             VARCHAR
                                                            , descricao_anterior           VARCHAR
                                                            , valor_previsto_anterior      NUMERIC
                                                            , arrecadado_periodo_anterior  NUMERIC
                                                            , arrecadado_ano_anterior      NUMERIC
                                                            , diferenca_anterior           NUMERIC
                                                            )
                           ON retorno.cod_estrutural = exercicio_anterior.cod_estrutural_anterior
                      ) AS tbl
             GROUP BY descricao
    ';
        
    EXECUTE stSql;


--Criando tabela para armazenar despesas referente ao cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_despesa AS
          SELECT descricao
               , SUM(valor) AS valor
               , SUM(valor_anterior) AS valor_anterior
                    FROM (
                            SELECT CASE WHEN cod_funcao = 1 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''legislativa''
                                        WHEN cod_funcao = 2 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''judiciaria''
                                        WHEN cod_funcao = 3 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''essencial_justica''
                                        WHEN cod_funcao = 4 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''administracao''
                                        WHEN cod_funcao = 5 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''defesa_nacional''
                                        WHEN cod_funcao = 6 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''seguranca_publica''
                                        WHEN cod_funcao = 7 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''relacoes_exteriores''
                                        WHEN cod_funcao = 8 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''assistencia_social''
                                        WHEN cod_funcao = 9 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''previdencia_social''
                                        WHEN cod_funcao = 10 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''saude''
                                        WHEN cod_funcao = 11 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''trabalho''
                                        WHEN cod_funcao = 12 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''educacao''
                                        WHEN cod_funcao = 13 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''cultura''
                                        WHEN cod_funcao = 14 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''direitos_cidadania''
                                        WHEN cod_funcao = 15 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''urbanismo''
                                        WHEN cod_funcao = 16 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''habitacao''
                                        WHEN cod_funcao = 17 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''saneamento''
                                        WHEN cod_funcao = 18 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''gestao_ambiental''
                                        WHEN cod_funcao = 19 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''ciencia_tecnologia''
                                        WHEN cod_funcao = 20 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''agricultura''
                                        WHEN cod_funcao = 21 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''organizacao_agraria''
                                        WHEN cod_funcao = 22 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''industria''
                                        WHEN cod_funcao = 23 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''comercio_servicos''
                                        WHEN cod_funcao = 24 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''comunicacoes''
                                        WHEN cod_funcao = 25 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''energia''
                                        WHEN cod_funcao = 26 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''transporte''
                                        WHEN cod_funcao = 27 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''desporto_lazer''
                                        WHEN cod_funcao = 28 AND descricao = ''pessoal_demais_despesas''
                                        THEN ''encargos_especiais''
                                        ELSE descricao
                                    END AS descricao
                                 , valor
                                 , valor_anterior
                              FROM (
                                    SELECT CASE WHEN classificacao LIKE ''3.1.7.1.%''
                                                  OR classificacao LIKE ''3.1.7.3.%''
                                                  OR classificacao LIKE ''3.1.7.4.%''
                                                  OR classificacao LIKE ''3.1.9.0.%''
                                                  OR classificacao LIKE ''3.1.9.3.%''
                                                  OR classificacao LIKE ''3.1.9.4.%''
                                                  OR classificacao LIKE ''3.1.9.5.%''
                                                  OR classificacao LIKE ''3.1.9.6.%''
                                                  OR classificacao LIKE ''3.1.9.9.%''
                                                  OR classificacao LIKE ''3.3.7.1.%''
                                                  OR classificacao LIKE ''3.3.7.2.%''
                                                  OR classificacao LIKE ''3.3.7.3.%''
                                                  OR classificacao LIKE ''3.3.7.4.%''
                                                  OR classificacao LIKE ''3.3.7.5.%''
                                                  OR classificacao LIKE ''3.3.7.6.%''
                                                  OR classificacao LIKE ''3.3.9.0.%''
                                                  OR classificacao LIKE ''3.3.9.3.%''
                                                  OR classificacao LIKE ''3.3.9.4.%''
                                                  OR classificacao LIKE ''3.3.9.5.%''
                                                  OR classificacao LIKE ''3.3.9.6.%''
                                                  OR classificacao LIKE ''3.3.9.9.%''
                                                THEN ''pessoal_demais_despesas''
                                                WHEN ((cod_funcao = 28
                                                 AND   cod_subfuncao IN (841,843))
                                                 AND (classificacao LIKE ''3.2.9.0.21%''
                                                  OR  classificacao LIKE ''3.2.9.0.23%''
                                                  OR  classificacao LIKE ''3.2.9.0.25%''
                                                  OR  classificacao LIKE ''3.2.9.5.21%''
                                                  OR  classificacao LIKE ''3.2.9.6.21%''
                                                  OR  classificacao LIKE ''4.6.9.0.73%''
                                                  OR  classificacao LIKE ''4.6.9.0.74%''
                                                  OR  classificacao LIKE ''4.6.9.0.75%''))
                                                THEN ''juros_correcao_divida_interna''
                                                WHEN ((cod_funcao = 28
                                                 AND   cod_subfuncao IN (842,844))
                                                 AND (classificacao LIKE ''3.2.9.0.21%''
                                                  OR  classificacao LIKE ''3.2.9.0.23%''
                                                  OR  classificacao LIKE ''3.2.9.0.25%''
                                                  OR  classificacao LIKE ''3.2.9.5.21%''
                                                  OR  classificacao LIKE ''3.2.9.6.21%''
                                                  OR  classificacao LIKE ''4.6.9.0.73%''
                                                  OR  classificacao LIKE ''4.6.9.0.74%''
                                                  OR  classificacao LIKE ''4.6.9.0.75%''))
                                                THEN ''juros_correcao_divida_externa''
                                                WHEN classificacao LIKE ''3.2.%''
                                                 AND (cod_funcao NOT IN (28) OR cod_subfuncao NOT IN (841,842,843,844))
                                                 AND (classificacao NOT LIKE ''3.2.9.0.21%''
                                                  OR  classificacao NOT LIKE ''3.2.9.0.23%''
                                                  OR  classificacao NOT LIKE ''3.2.9.0.25%''
                                                  OR  classificacao NOT LIKE ''3.2.9.5.21%''
                                                  OR  classificacao NOT LIKE ''3.2.9.6.21%'')
                                                THEN ''outros_encargos_divida''
                                                WHEN classificacao LIKE ''3.3.2.0%''
                                                  OR classificacao LIKE ''3.3.2.2%''
                                                THEN ''despesa_transferencia_uniao''
                                                WHEN classificacao LIKE ''3.1.3.0%''
                                                  OR classificacao LIKE ''3.3.3.0%''
                                                  OR classificacao LIKE ''3.3.3.1%''
                                                  OR classificacao LIKE ''3.3.3.2%''
                                                  OR classificacao LIKE ''3.3.3.5%''
                                                  OR classificacao LIKE ''3.3.3.6%''
                                                  OR classificacao LIKE ''4.4.3.0%''
                                                  OR classificacao LIKE ''4.4.3.1%''
                                                  OR classificacao LIKE ''4.4.3.2%''
                                                  OR classificacao LIKE ''4.4.3.5%''
                                                  OR classificacao LIKE ''4.4.3.6%''
                                                  OR classificacao LIKE ''4.5.3.0%''
                                                  OR classificacao LIKE ''4.5.3.1%''
                                                  OR classificacao LIKE ''4.5.3.2%''
                                                  OR classificacao LIKE ''4.5.3.5%''
                                                  OR classificacao LIKE ''4.5.3.6%''
                                                THEN ''despesa_transferencia_estado_df''
                                                WHEN classificacao LIKE ''3.3.4.0%''
                                                  OR classificacao LIKE ''3.3.4.1%''
                                                  OR classificacao LIKE ''3.3.4.2%''
                                                  OR classificacao LIKE ''3.3.4.5%''
                                                  OR classificacao LIKE ''3.3.4.6%''
                                                  OR classificacao LIKE ''4.4.4.0%''
                                                  OR classificacao LIKE ''4.4.4.1%''
                                                  OR classificacao LIKE ''4.4.4.2%''
                                                  OR classificacao LIKE ''4.4.4.5%''
                                                  OR classificacao LIKE ''4.4.4.6%''
                                                  OR classificacao LIKE ''4.5.4.0%''
                                                  OR classificacao LIKE ''4.5.4.1%''
                                                  OR classificacao LIKE ''4.5.4.2%''
                                                  OR classificacao LIKE ''4.5.4.5%''
                                                  OR classificacao LIKE ''4.5.4.6%''
                                                THEN ''despesa_transferencia_municipios''
                                                WHEN classificacao LIKE ''3.1.9.1%''
                                                  OR classificacao LIKE ''3.3.9.1%''
                                                  OR classificacao LIKE ''4.4.9.1%''
                                                  OR classificacao LIKE ''4.5.9.1%''
                                                THEN ''despesa_transferencia_intragovernamentais''
                                                WHEN classificacao LIKE ''3.1.8.0%''
                                                  OR classificacao LIKE ''3.3.5.0%''
                                                  OR classificacao LIKE ''3.3.6.0%''
                                                  OR classificacao LIKE ''3.3.7.0%''
                                                  OR classificacao LIKE ''3.3.8.0%''
                                                  OR classificacao LIKE ''4.4.5.0%''
                                                  OR classificacao LIKE ''4.4.6.0%''
                                                  OR classificacao LIKE ''4.4.7.0%''
                                                  OR classificacao LIKE ''4.4.7.6%''
                                                  OR classificacao LIKE ''4.4.8.0%''
                                                  OR classificacao LIKE ''4.5.5.0%''
                                                  OR classificacao LIKE ''4.5.6.0%''
                                                  OR classificacao LIKE ''4.5.7.0%''
                                                  OR classificacao LIKE ''4.5.7.6%''
                                                  OR classificacao LIKE ''4.5.8.0%''
                                                THEN ''outras_transferencia_concedidas''
                                                WHEN classificacao LIKE ''4.4.%''
                                                 AND substring(classificacao from 9 for 2) SIMILAR TO (''51|52|61'')
                                                  OR classificacao LIKE ''4.5.%''
                                                 AND substring(classificacao from 9 for 2) SIMILAR TO (''61|63|64|65'')
                                                THEN ''aquisicao_ativo_nao_circulante''
                                                WHEN classificacao LIKE ''4.5.%'' AND substring(classificacao from 9 for 2) SIMILAR TO (''66'')
                                                THEN ''concessao_emprestimos_financeiros''
                                                WHEN classificacao LIKE ''4.4.%'' AND substring(classificacao from 9 for 2) NOT SIMILAR TO (''51|52|61'')
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.4.%''
                                                 AND classificacao NOT LIKE ''4.4.3.0%''
                                                 AND classificacao NOT LIKE ''4.4.3.1%''
                                                 AND classificacao NOT LIKE ''4.4.3.2%''
                                                 AND classificacao NOT LIKE ''4.4.3.5%''
                                                 AND classificacao NOT LIKE ''4.4.3.6%''
                                                 AND classificacao NOT LIKE ''4.4.4.1%''
                                                 AND classificacao NOT LIKE ''4.4.4.2%''
                                                 AND classificacao NOT LIKE ''4.4.4.5%''
                                                 AND classificacao NOT LIKE ''4.4.4.6%''
                                                 AND classificacao NOT LIKE ''4.4.5.0%''
                                                 AND classificacao NOT LIKE ''4.4.6.0%''
                                                 AND classificacao NOT LIKE ''4.4.7.0%''
                                                 AND classificacao NOT LIKE ''4.4.7.6%''
                                                 AND classificacao NOT LIKE ''4.4.8.0%''
                                                 AND classificacao NOT LIKE ''4.4.9.1%''
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.5.%''
                                                 AND substring(classificacao from 9 for 2) NOT SIMILAR TO (''61|63|64|65|66'')
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.5.%''
                                                 AND classificacao NOT LIKE ''4.5.3.0%''
                                                 AND classificacao NOT LIKE ''4.5.3.1%''
                                                 AND classificacao NOT LIKE ''4.5.3.2%''
                                                 AND classificacao NOT LIKE ''4.5.3.5%''
                                                 AND classificacao NOT LIKE ''4.5.3.6%''
                                                 AND classificacao NOT LIKE ''4.5.4.0%''
                                                 AND classificacao NOT LIKE ''4.5.4.1%''
                                                 AND classificacao NOT LIKE ''4.5.4.2%''
                                                 AND classificacao NOT LIKE ''4.5.4.5%''
                                                 AND classificacao NOT LIKE ''4.5.4.6%''
                                                 AND classificacao NOT LIKE ''4.5.5.0%''
                                                 AND classificacao NOT LIKE ''4.5.6.0%''
                                                 AND classificacao NOT LIKE ''4.5.7.0%''
                                                 AND classificacao NOT LIKE ''4.5.7.6%''
                                                 AND classificacao NOT LIKE ''4.5.8.0%''
                                                 AND classificacao NOT LIKE ''4.5.9.1%''
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.6.%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.71%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.72%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.76%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.77%''
                                                  OR (classificacao NOT LIKE ''4.6.9.0.73%'' AND cod_funcao = 28 AND cod_subfuncao IN (841,842,843,844))
                                                  OR (classificacao NOT LIKE ''4.6.9.0.74%'' AND cod_funcao = 28 AND cod_subfuncao IN (841,842,843,844))
                                                  OR (classificacao NOT LIKE ''4.6.9.0.75%'' AND cod_funcao = 28 AND cod_subfuncao IN (841,842,843,844))
                                                THEN ''amortizacao_refinanciamento_divida''
                                                WHEN classificacao LIKE ''4.6.9.0.71%''
                                                  OR classificacao LIKE ''4.6.9.0.72%''
                                                  OR classificacao LIKE ''4.6.9.0.76%''
                                                  OR classificacao LIKE ''4.6.9.0.77%''
                                                THEN ''outros_desembolsos_financiamentos''
                                              ELSE ''''
                                          END AS descricao
                                       , cod_funcao
                                       , cod_subfuncao
                                       , pago_per AS valor
                                       , 0.00 AS valor_anterior
                                    FROM orcamento.fn_balancete_despesa( '''||stExercicio||'''
                                                                       , '' AND od.cod_entidade IN  ('|| stCodEntidade ||')''
                                                                       , '''||dtInicial||'''
                                                                       , '''||dtFinal||'''
                                                                       , '''','''','''','''','''' ,'''','''', '''' )
                                      AS retorno ( exercicio              CHAR(4)
                                                 , cod_despesa            INTEGER
                                                 , cod_entidade           INTEGER
                                                 , cod_programa           INTEGER
                                                 , cod_conta              INTEGER
                                                 , num_pao                INTEGER
                                                 , num_orgao              INTEGER
                                                 , num_unidade            INTEGER
                                                 , cod_recurso            INTEGER
                                                 , cod_funcao             INTEGER
                                                 , cod_subfuncao          INTEGER
                                                 , tipo_conta             VARCHAR
                                                 , vl_original            NUMERIC
                                                 , dt_criacao             DATE
                                                 , classificacao          VARCHAR
                                                 , descricao              VARCHAR
                                                 , num_recurso            VARCHAR
                                                 , nom_recurso            VARCHAR
                                                 , nom_orgao              VARCHAR
                                                 , nom_unidade            VARCHAR
                                                 , nom_funcao             VARCHAR
                                                 , nom_subfuncao          VARCHAR
                                                 , nom_programa           VARCHAR
                                                 , nom_pao                VARCHAR
                                                 , empenhado_ano          NUMERIC
                                                 , empenhado_per          NUMERIC
                                                 , anulado_ano            NUMERIC
                                                 , anulado_per            NUMERIC
                                                 , pago_ano               NUMERIC
                                                 , pago_per               NUMERIC
                                                 , liquidado_ano          NUMERIC
                                                 , liquidado_per          NUMERIC
                                                 , saldo_inicial          NUMERIC
                                                 , suplementacoes         NUMERIC
                                                 , reducoes               NUMERIC
                                                 , total_creditos         NUMERIC
                                                 , credito_suplementar    NUMERIC
                                                 , credito_especial       NUMERIC
                                                 , credito_extraordinario NUMERIC
                                                 , num_programa           VARCHAR
                                                 , num_acao               VARCHAR
                                                 )
                               UNION ALL
                                    SELECT CASE WHEN classificacao LIKE ''3.1.7.1.%''
                                                  OR classificacao LIKE ''3.1.7.3.%''
                                                  OR classificacao LIKE ''3.1.7.4.%''
                                                  OR classificacao LIKE ''3.1.9.0.%''
                                                  OR classificacao LIKE ''3.1.9.3.%''
                                                  OR classificacao LIKE ''3.1.9.4.%''
                                                  OR classificacao LIKE ''3.1.9.5.%''
                                                  OR classificacao LIKE ''3.1.9.6.%''
                                                  OR classificacao LIKE ''3.1.9.9.%''
                                                  OR classificacao LIKE ''3.3.7.1.%''
                                                  OR classificacao LIKE ''3.3.7.2.%''
                                                  OR classificacao LIKE ''3.3.7.3.%''
                                                  OR classificacao LIKE ''3.3.7.4.%''
                                                  OR classificacao LIKE ''3.3.7.5.%''
                                                  OR classificacao LIKE ''3.3.7.6.%''
                                                  OR classificacao LIKE ''3.3.9.0.%''
                                                  OR classificacao LIKE ''3.3.9.3.%''
                                                  OR classificacao LIKE ''3.3.9.4.%''
                                                  OR classificacao LIKE ''3.3.9.5.%''
                                                  OR classificacao LIKE ''3.3.9.6.%''
                                                  OR classificacao LIKE ''3.3.9.9.%''
                                                THEN ''pessoal_demais_despesas''
                                                WHEN ((cod_funcao = 28
                                                 AND   cod_subfuncao IN (841,843))
                                                 AND (classificacao LIKE ''3.2.9.0.21%''
                                                  OR  classificacao LIKE ''3.2.9.0.23%''
                                                  OR  classificacao LIKE ''3.2.9.0.25%''
                                                  OR  classificacao LIKE ''3.2.9.5.21%''
                                                  OR  classificacao LIKE ''3.2.9.6.21%''
                                                  OR  classificacao LIKE ''4.6.9.0.73%''
                                                  OR  classificacao LIKE ''4.6.9.0.74%''
                                                  OR  classificacao LIKE ''4.6.9.0.75%''))
                                                THEN ''juros_correcao_divida_interna''
                                                WHEN ((cod_funcao = 28
                                                 AND   cod_subfuncao IN (842,844))
                                                 AND (classificacao LIKE ''3.2.9.0.21%''
                                                  OR  classificacao LIKE ''3.2.9.0.23%''
                                                  OR  classificacao LIKE ''3.2.9.0.25%''
                                                  OR  classificacao LIKE ''3.2.9.5.21%''
                                                  OR  classificacao LIKE ''3.2.9.6.21%''
                                                  OR  classificacao LIKE ''4.6.9.0.73%''
                                                  OR  classificacao LIKE ''4.6.9.0.74%''
                                                  OR  classificacao LIKE ''4.6.9.0.75%''))
                                                THEN ''juros_correcao_divida_externa''
                                                WHEN classificacao LIKE ''3.2.%''
                                                 AND (cod_funcao NOT IN (28) OR cod_subfuncao NOT IN (841,842,843,844))
                                                 AND (classificacao NOT LIKE ''3.2.9.0.21%''
                                                  OR  classificacao NOT LIKE ''3.2.9.0.23%''
                                                  OR  classificacao NOT LIKE ''3.2.9.0.25%''
                                                  OR  classificacao NOT LIKE ''3.2.9.5.21%''
                                                  OR  classificacao NOT LIKE ''3.2.9.6.21%'')
                                                THEN ''outros_encargos_divida''
                                                WHEN classificacao LIKE ''3.3.2.0%''
                                                  OR classificacao LIKE ''3.3.2.2%''
                                                THEN ''despesa_transferencia_uniao''
                                                WHEN classificacao LIKE ''3.1.3.0%''
                                                  OR classificacao LIKE ''3.3.3.0%''
                                                  OR classificacao LIKE ''3.3.3.1%''
                                                  OR classificacao LIKE ''3.3.3.2%''
                                                  OR classificacao LIKE ''3.3.3.5%''
                                                  OR classificacao LIKE ''3.3.3.6%''
                                                  OR classificacao LIKE ''4.4.3.0%''
                                                  OR classificacao LIKE ''4.4.3.1%''
                                                  OR classificacao LIKE ''4.4.3.2%''
                                                  OR classificacao LIKE ''4.4.3.5%''
                                                  OR classificacao LIKE ''4.4.3.6%''
                                                  OR classificacao LIKE ''4.5.3.0%''
                                                  OR classificacao LIKE ''4.5.3.1%''
                                                  OR classificacao LIKE ''4.5.3.2%''
                                                  OR classificacao LIKE ''4.5.3.5%''
                                                  OR classificacao LIKE ''4.5.3.6%''
                                                THEN ''despesa_transferencia_estado_df''
                                                WHEN classificacao LIKE ''3.3.4.0%''
                                                  OR classificacao LIKE ''3.3.4.1%''
                                                  OR classificacao LIKE ''3.3.4.2%''
                                                  OR classificacao LIKE ''3.3.4.5%''
                                                  OR classificacao LIKE ''3.3.4.6%''
                                                  OR classificacao LIKE ''4.4.4.0%''
                                                  OR classificacao LIKE ''4.4.4.1%''
                                                  OR classificacao LIKE ''4.4.4.2%''
                                                  OR classificacao LIKE ''4.4.4.5%''
                                                  OR classificacao LIKE ''4.4.4.6%''
                                                  OR classificacao LIKE ''4.5.4.0%''
                                                  OR classificacao LIKE ''4.5.4.1%''
                                                  OR classificacao LIKE ''4.5.4.2%''
                                                  OR classificacao LIKE ''4.5.4.5%''
                                                  OR classificacao LIKE ''4.5.4.6%''
                                                THEN ''despesa_transferencia_municipios''
                                                WHEN classificacao LIKE ''3.1.9.1%''
                                                  OR classificacao LIKE ''3.3.9.1%''
                                                  OR classificacao LIKE ''4.4.9.1%''
                                                  OR classificacao LIKE ''4.5.9.1%''
                                                THEN ''despesa_transferencia_intragovernamentais''
                                                WHEN classificacao LIKE ''3.1.8.0%''
                                                  OR classificacao LIKE ''3.3.5.0%''
                                                  OR classificacao LIKE ''3.3.6.0%''
                                                  OR classificacao LIKE ''3.3.7.0%''
                                                  OR classificacao LIKE ''3.3.8.0%''
                                                  OR classificacao LIKE ''4.4.5.0%''
                                                  OR classificacao LIKE ''4.4.6.0%''
                                                  OR classificacao LIKE ''4.4.7.0%''
                                                  OR classificacao LIKE ''4.4.7.6%''
                                                  OR classificacao LIKE ''4.4.8.0%''
                                                  OR classificacao LIKE ''4.5.5.0%''
                                                  OR classificacao LIKE ''4.5.6.0%''
                                                  OR classificacao LIKE ''4.5.7.0%''
                                                  OR classificacao LIKE ''4.5.7.6%''
                                                  OR classificacao LIKE ''4.5.8.0%''
                                                THEN ''outras_transferencia_concedidas''
                                                WHEN classificacao LIKE ''4.4.%''
                                                 AND substring(classificacao from 9 for 2) SIMILAR TO (''51|52|61'')
                                                  OR classificacao LIKE ''4.5.%''
                                                 AND substring(classificacao from 9 for 2) SIMILAR TO (''61|63|64|65'')
                                                THEN ''aquisicao_ativo_nao_circulante''
                                                WHEN classificacao LIKE ''4.5.%'' AND substring(classificacao from 9 for 2) SIMILAR TO (''66'')
                                                THEN ''concessao_emprestimos_financeiros''
                                                WHEN classificacao LIKE ''4.4.%'' AND substring(classificacao from 9 for 2) NOT SIMILAR TO (''51|52|61'')
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.4.%''
                                                 AND classificacao NOT LIKE ''4.4.3.0%''
                                                 AND classificacao NOT LIKE ''4.4.3.1%''
                                                 AND classificacao NOT LIKE ''4.4.3.2%''
                                                 AND classificacao NOT LIKE ''4.4.3.5%''
                                                 AND classificacao NOT LIKE ''4.4.3.6%''
                                                 AND classificacao NOT LIKE ''4.4.4.1%''
                                                 AND classificacao NOT LIKE ''4.4.4.2%''
                                                 AND classificacao NOT LIKE ''4.4.4.5%''
                                                 AND classificacao NOT LIKE ''4.4.4.6%''
                                                 AND classificacao NOT LIKE ''4.4.5.0%''
                                                 AND classificacao NOT LIKE ''4.4.6.0%''
                                                 AND classificacao NOT LIKE ''4.4.7.0%''
                                                 AND classificacao NOT LIKE ''4.4.7.6%''
                                                 AND classificacao NOT LIKE ''4.4.8.0%''
                                                 AND classificacao NOT LIKE ''4.4.9.1%''
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.5.%''
                                                 AND substring(classificacao from 9 for 2) NOT SIMILAR TO (''61|63|64|65|66'')
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.5.%''
                                                 AND classificacao NOT LIKE ''4.5.3.0%''
                                                 AND classificacao NOT LIKE ''4.5.3.1%''
                                                 AND classificacao NOT LIKE ''4.5.3.2%''
                                                 AND classificacao NOT LIKE ''4.5.3.5%''
                                                 AND classificacao NOT LIKE ''4.5.3.6%''
                                                 AND classificacao NOT LIKE ''4.5.4.0%''
                                                 AND classificacao NOT LIKE ''4.5.4.1%''
                                                 AND classificacao NOT LIKE ''4.5.4.2%''
                                                 AND classificacao NOT LIKE ''4.5.4.5%''
                                                 AND classificacao NOT LIKE ''4.5.4.6%''
                                                 AND classificacao NOT LIKE ''4.5.5.0%''
                                                 AND classificacao NOT LIKE ''4.5.6.0%''
                                                 AND classificacao NOT LIKE ''4.5.7.0%''
                                                 AND classificacao NOT LIKE ''4.5.7.6%''
                                                 AND classificacao NOT LIKE ''4.5.8.0%''
                                                 AND classificacao NOT LIKE ''4.5.9.1%''
                                                THEN ''outros_desembolsos_investimentos''
                                                WHEN classificacao LIKE ''4.6.%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.71%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.72%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.76%''
                                                  OR classificacao NOT LIKE ''4.6.9.0.77%''
                                                  OR (classificacao NOT LIKE ''4.6.9.0.73%'' AND cod_funcao = 28 AND cod_subfuncao IN (841,842,843,844))
                                                  OR (classificacao NOT LIKE ''4.6.9.0.74%'' AND cod_funcao = 28 AND cod_subfuncao IN (841,842,843,844))
                                                  OR (classificacao NOT LIKE ''4.6.9.0.75%'' AND cod_funcao = 28 AND cod_subfuncao IN (841,842,843,844))
                                                THEN ''amortizacao_refinanciamento_divida''
                                              ELSE ''''
                                          END AS descricao
                                       , cod_funcao
                                       , cod_subfuncao
                                       , 0.00 AS valor
                                       , pago_per_anterior AS valor_anterior
                                    FROM orcamento.fn_balancete_despesa( '''||stExercicioAnterior||'''
                                                                       , '' AND od.cod_entidade IN  ('|| stCodEntidade ||')''
                                                                       , '''||dtInicialAnterior||'''
                                                                       , '''||dtFinalAnterior||'''
                                                                       , '''','''','''','''','''' ,'''','''', '''' )
                                      AS retorno( exercicio              CHAR(4)
                                                , cod_despesa            INTEGER
                                                , cod_entidade           INTEGER
                                                , cod_programa           INTEGER
                                                , cod_conta              INTEGER
                                                , num_pao                INTEGER
                                                , num_orgao              INTEGER
                                                , num_unidade            INTEGER
                                                , cod_recurso            INTEGER
                                                , cod_funcao             INTEGER
                                                , cod_subfuncao          INTEGER
                                                , tipo_conta             VARCHAR
                                                , vl_original            NUMERIC
                                                , dt_criacao             DATE
                                                , classificacao          VARCHAR
                                                , descricao              VARCHAR
                                                , num_recurso            VARCHAR
                                                , nom_recurso            VARCHAR
                                                , nom_orgao              VARCHAR
                                                , nom_unidade            VARCHAR
                                                , nom_funcao             VARCHAR
                                                , nom_subfuncao          VARCHAR
                                                , nom_programa           VARCHAR
                                                , nom_pao                VARCHAR
                                                , empenhado_ano          NUMERIC
                                                , empenhado_per          NUMERIC
                                                , anulado_ano            NUMERIC
                                                , anulado_per            NUMERIC
                                                , pago_ano               NUMERIC
                                                , pago_per_anterior      NUMERIC
                                                , liquidado_ano          NUMERIC
                                                , liquidado_per          NUMERIC
                                                , saldo_inicial          NUMERIC
                                                , suplementacoes         NUMERIC
                                                , reducoes               NUMERIC
                                                , total_creditos         NUMERIC
                                                , credito_suplementar    NUMERIC
                                                , credito_especial       NUMERIC
                                                , credito_extraordinario NUMERIC
                                                , num_programa           VARCHAR
                                                , num_acao               VARCHAR
                                                )
                                 ) AS tabela_valores

                 ) AS tabela_relatorio
        GROUP BY descricao
    ';

    EXECUTE stSql;

--Criando tabela para armazenar saldos referente ao cod_estrutural
    stSql := ' CREATE TEMPORARY TABLE fluxo_caixa_saldo AS

      SELECT descricao
           , SUM(saldo_inicial) AS saldo_inicial
           , SUM(saldo_final) AS saldo_final
           , SUM(saldo_inicial_anterior) AS saldo_inicial_anterior
           , SUM(saldo_final_anterior) AS saldo_final_anterior
        FROM (
              SELECT CASE WHEN retorno.cod_estrutural like ''1.1.1.%''
                            OR retorno.cod_estrutural like ''1.1.4.%''
                          THEN ''saldo_caixa''
                      END AS descricao
                   , SUM(vl_saldo_anterior) AS saldo_inicial
                   , SUM(vl_saldo_atual) AS saldo_final
                   , 0.00::NUMERIC AS saldo_inicial_anterior
                   , 0.00::NUMERIC AS saldo_final_anterior
                FROM contabilidade.fn_rl_balancete_verificacao( '''||stExercicio||'''
                                                              , ''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                              , '''||dtInicial||'''
                                                              , '''||dtFinal||'''
                                                              , ''A''::char
                                                              )
                  AS retorno ( cod_estrutural      varchar
                             , nivel               integer
                             , nom_conta           varchar
                             , cod_sistema         integer
                             , indicador_superavit char(12)
                             , vl_saldo_anterior   numeric
                             , vl_saldo_debitos    numeric
                             , vl_saldo_creditos   numeric
                             , vl_saldo_atual      numeric
                             )
             LEFT JOIN contabilidade.plano_conta
                    ON plano_conta.cod_estrutural = retorno.cod_estrutural
                   AND plano_conta.exercicio = '''||stExercicio||'''
             LEFT JOIN contabilidade.plano_analitica
                    ON plano_analitica.exercicio = plano_conta.exercicio
                   AND plano_analitica.cod_conta = plano_conta.cod_conta
             LEFT JOIN contabilidade.plano_banco
                    ON plano_banco.cod_plano = plano_analitica.cod_plano
                   AND plano_banco.exercicio = plano_analitica.exercicio
                 WHERE plano_banco.cod_plano IS NOT NULL
              GROUP BY descricao
             UNION ALL
                SELECT CASE WHEN retorno_anterior.cod_estrutural_anterior like ''1.1.1.%''
                              OR retorno_anterior.cod_estrutural_anterior like ''1.1.4.%''
                            THEN ''saldo_caixa''
                        END AS descricao
                     , 0.00::NUMERIC AS saldo_inicial
                     , 0.00::NUMERIC AS saldo_final
                     , SUM(vl_saldo_anterior_anterior) AS saldo_inicial_anterior
                     , SUM(vl_saldo_atual_anterior) AS saldo_final_anterior
                  FROM contabilidade.fn_rl_balancete_verificacao( '''||stExercicioAnterior||'''
                                                                , ''cod_entidade IN  ('|| stCodEntidade ||') ''
                                                                , '''||dtInicialAnterior||'''
                                                                , '''||dtFinalAnterior||'''
                                                                , ''A''::char
                                                                )
                    AS retorno_anterior ( cod_estrutural_anterior      varchar
                                        , nivel_anterior               integer
                                        , nom_conta_anterior           varchar
                                        , cod_sistema_anterior         integer
                                        , indicador_superavit_anterior char(12)
                                        , vl_saldo_anterior_anterior   numeric
                                        , vl_saldo_debitos_anterior    numeric
                                        , vl_saldo_creditos_anterior   numeric
                                        , vl_saldo_atual_anterior      numeric
                                        )
             LEFT JOIN contabilidade.plano_conta
                    ON plano_conta.cod_estrutural = retorno_anterior.cod_estrutural_anterior
                   AND plano_conta.exercicio = '''||stExercicioAnterior||'''
             LEFT JOIN contabilidade.plano_analitica
                    ON plano_analitica.exercicio = plano_conta.exercicio
                   AND plano_analitica.cod_conta = plano_conta.cod_conta
             LEFT JOIN contabilidade.plano_banco
                    ON plano_banco.cod_plano = plano_analitica.cod_plano
                   AND plano_banco.exercicio = plano_analitica.exercicio
                 WHERE plano_banco.cod_plano IS NOT NULL
              GROUP BY descricao
             ) AS valores_caixa
    GROUP BY valores_caixa.descricao
    ';
    
    EXECUTE stSql;

    --FILTRO LANÇAMENTO DIFERENTE DE TIPO 'M'
    stFiltroTrans := 'cod_entidade IN ('|| stCodEntidade ||')
                     AND cod_estrutural SIMILAR TO ''2.1.8%|1.1.3%''
                     AND tipo != ''M'' ';

    stFiltroExtra := 'cod_entidade IN ('|| stCodEntidade ||') 
                     AND (      cod_estrutural SIMILAR TO ''6.3.2.2%|6.3.1.4%''
                           OR ( cod_estrutural LIKE ''4.5.1.1%'' AND tipo != ''M'' )
                           OR ( cod_estrutural LIKE ''3.5.1.1%'' AND tipo != ''M'' )
                           OR ( cod_estrutural LIKE ''4.5.1.2%'' AND tipo != ''M'' )
                           OR ( cod_estrutural LIKE ''3.5.1.2%'' AND tipo != ''M'' )
                           OR ( cod_estrutural LIKE ''4.5.1.3%'' AND tipo != ''M'' )
                           OR ( cod_estrutural LIKE ''3.5.1.3%'' AND tipo != ''M'' )
                         ) ';

    stSql := '
    CREATE TEMPORARY TABLE fluxo_balanco_financeiro AS
      SELECT descricao
           , ABS(COALESCE(SUM(saldo_inicial), 0.00)) AS saldo_inicial
           , ABS(COALESCE(SUM(saldo_debitos), 0.00)) AS saldo_debitos
           , ABS(COALESCE(SUM(saldo_creditos), 0.00)) AS saldo_creditos
           , ABS(COALESCE(SUM(saldo_final), 0.00)) AS saldo_final
           , ABS(COALESCE(SUM(saldo_inicial_anterior), 0.00)) AS saldo_inicial_anterior
           , ABS(COALESCE(SUM(saldo_debitos_anterior), 0.00)) AS saldo_debitos_anterior
           , ABS(COALESCE(SUM(saldo_creditos_anterior), 0.00)) AS saldo_creditos_anterior
           , ABS(COALESCE(SUM(saldo_final_anterior), 0.00)) AS saldo_final_anterior
        FROM (
              SELECT CASE WHEN retorno.cod_estrutural like ''2.1.8%''
                          THEN ''depositos_restituiveis_valores_vinculados''
                          WHEN retorno.cod_estrutural like ''1.1.3.%''
                          THEN ''outros_recebimentos_ext''
                      END AS descricao
                   , SUM(vl_saldo_anterior) AS saldo_inicial
                   , SUM(vl_saldo_debitos) AS saldo_debitos
                   , SUM(vl_saldo_creditos) AS saldo_creditos
                   , SUM(vl_saldo_atual) AS saldo_final
                   , 0.00::NUMERIC AS saldo_inicial_anterior
                   , 0.00::NUMERIC AS saldo_debitos_anterior
                   , 0.00::NUMERIC AS saldo_creditos_anterior
                   , 0.00::NUMERIC AS saldo_final_anterior
                FROM contabilidade.fn_rl_balancete_verificacao( '''||stExercicio||'''
                                                              , '|| quote_literal(stFiltroTrans) ||'
                                                              , '''||dtInicial||'''
                                                              , '''||dtFinal||'''
                                                              , ''A''::char
                                                              )
                  AS retorno ( cod_estrutural      varchar
                             , nivel               integer
                             , nom_conta           varchar
                             , cod_sistema         integer
                             , indicador_superavit char(12)
                             , vl_saldo_anterior   numeric
                             , vl_saldo_debitos    numeric
                             , vl_saldo_creditos   numeric
                             , vl_saldo_atual      numeric
                             )
          INNER JOIN contabilidade.plano_conta
                  ON plano_conta.exercicio = '''||stExercicio||'''
                 AND plano_conta.cod_estrutural = retorno.cod_estrutural
                 AND plano_conta.escrituracao ilike ''anali%''
            GROUP BY descricao
           UNION ALL
              SELECT CASE WHEN retorno_anterior.cod_estrutural_anterior like ''2.1.8%''
                          THEN ''depositos_restituiveis_valores_vinculados''
                          WHEN retorno_anterior.cod_estrutural_anterior like ''1.1.3.%''
                          THEN ''outros_recebimentos_ext''
                      END AS descricao
                   , 0.00::NUMERIC AS saldo_inicial
                   , 0.00::NUMERIC AS saldo_debitos
                   , 0.00::NUMERIC AS saldo_creditos
                   , 0.00::NUMERIC AS saldo_final
                   , SUM(vl_saldo_anterior_anterior) AS saldo_inicial_anterior
                   , SUM(vl_saldo_debitos_anterior) AS saldo_debitos_anterior
                   , SUM(vl_saldo_creditos_anterior) AS saldo_creditos_anterior
                   , SUM(vl_saldo_atual_anterior) AS saldo_final_anterior
                FROM contabilidade.fn_rl_balancete_verificacao( '''||stExercicioAnterior||'''
                                                              , '|| quote_literal(stFiltroTrans) ||'
                                                              , '''||dtInicialAnterior||'''
                                                              , '''||dtFinalAnterior||'''
                                                              , ''A''::char
                                                              )
                  AS retorno_anterior ( cod_estrutural_anterior      varchar
                                      , nivel_anterior               integer
                                      , nom_conta_anterior           varchar
                                      , cod_sistema_anterior         integer
                                      , indicador_superavit_anterior char(12)
                                      , vl_saldo_anterior_anterior   numeric
                                      , vl_saldo_debitos_anterior    numeric
                                      , vl_saldo_creditos_anterior   numeric
                                      , vl_saldo_atual_anterior      numeric
                                      )
          INNER JOIN contabilidade.plano_conta
                  ON plano_conta.exercicio = '''||stExercicioAnterior||'''
                 AND plano_conta.cod_estrutural = retorno_anterior.cod_estrutural_anterior
                 AND plano_conta.escrituracao ilike ''anali%''
            GROUP BY descricao
           UNION ALL
              SELECT CASE WHEN cod_estrutural like ''4.5.1.1.0%''
                          THEN ''transferencias_recebidas_orcamentaria''
                          WHEN cod_estrutural like ''3.5.1.1.0%''
                          THEN ''tranferencias_concedidas_orcamentaria''
                          WHEN cod_estrutural like ''4.5.1.2.0%''                        
                          THEN ''transferencias_recebidas_independentes_orcamentaria''
                          WHEN cod_estrutural like ''3.5.1.2.0%''                        
                          THEN ''transferencias_concedidas_independentes_orcamentaria''
                          WHEN cod_estrutural like ''4.5.1.3.0%''
                          THEN ''transferencias_recebidas_cobertura''
                          WHEN cod_estrutural like ''3.5.1.3%''
                          THEN ''transferencias_concedidas_cobertura''
                          WHEN cod_estrutural like ''6.3.2.2.0%''
                          THEN ''pagamento_restos_pagar_processados''
                          WHEN cod_estrutural like ''6.3.1.4.0%''
                          THEN ''pagamento_restos_pagar_nao_processados''
                      END AS descricao
                   , SUM(vl_saldo_anterior) AS saldo_inicial
                   , SUM(vl_saldo_debitos) AS saldo_debitos
                   , SUM(vl_saldo_creditos) AS saldo_creditos
                   , SUM(vl_saldo_atual) AS saldo_final
                   , 0.00::NUMERIC AS saldo_inicial_anterior
                   , 0.00::NUMERIC AS saldo_debitos_anterior
                   , 0.00::NUMERIC AS saldo_creditos_anterior
                   , 0.00::NUMERIC AS saldo_final_anterior
                FROM contabilidade.fn_rl_balancete_verificacao_transferencias( '''||stExercicio||'''
                                                                             , '|| quote_literal(stFiltroExtra) ||'
                                                                             , '''||dtInicial||'''
                                                                             , '''||dtFinal||'''
                                                                             , ''A''::CHAR
                                                                             )
                                                                             
                  AS retorno ( cod_estrutural varchar                                                    
                             , nivel integer                                                               
                             , nom_conta varchar                                                           
                             , cod_sistema integer                                                         
                             , indicador_superavit char(12)                                                    
                             , vl_saldo_anterior numeric                                                   
                             , vl_saldo_debitos  numeric                                                   
                             , vl_saldo_creditos numeric                                                   
                             , vl_saldo_atual    numeric                                                   
                             )
               WHERE cod_estrutural SIMILAR TO ''4.5.1.1.0%|3.5.1.1.0%|4.5.1.2.0%|3.5.1.2.0%|4.5.1.3.0%|3.5.1.3%|6.3.2.2.0%|6.3.1.4.0%''
            GROUP BY descricao
           UNION ALL
              SELECT CASE WHEN cod_estrutural like ''4.5.1.1.0%''
                          THEN ''transferencias_recebidas_orcamentaria''
                          WHEN cod_estrutural like ''3.5.1.1.0%''
                          THEN ''tranferencias_concedidas_orcamentaria''
                          WHEN cod_estrutural like ''4.5.1.2.0%''                        
                          THEN ''transferencias_recebidas_independentes_orcamentaria''
                          WHEN cod_estrutural like ''3.5.1.2.0%''                        
                          THEN ''transferencias_concedidas_independentes_orcamentaria''
                          WHEN cod_estrutural like ''4.5.1.3.0%''
                          THEN ''transferencias_recebidas_cobertura''
                          WHEN cod_estrutural like ''3.5.1.3%''
                          THEN ''transferencias_concedidas_cobertura''
                          WHEN cod_estrutural like ''6.3.2.2.0%''
                          THEN ''pagamento_restos_pagar_processados''
                          WHEN cod_estrutural like ''6.3.1.4.0%''
                          THEN ''pagamento_restos_pagar_nao_processados''
                      END AS descricao
                   , 0.00::NUMERIC AS saldo_inicial
                   , 0.00::NUMERIC AS saldo_debitos
                   , 0.00::NUMERIC AS saldo_creditos
                   , 0.00::NUMERIC AS saldo_final
                   , SUM(vl_saldo_anterior) AS saldo_inicial_anterior
                   , SUM(vl_saldo_debitos) AS saldo_debitos_anterior
                   , SUM(vl_saldo_creditos) AS saldo_creditos_anterior
                   , SUM(vl_saldo_atual) AS saldo_final_anterior
                FROM contabilidade.fn_rl_balancete_verificacao_transferencias( '''||stExercicioAnterior||'''
                                                                             , '|| quote_literal(stFiltroExtra) ||'
                                                                             , '''||dtInicialAnterior||'''
                                                                             , '''||dtFinalAnterior||'''
                                                                             , ''A''::CHAR
                                                                             )
                  AS retorno ( cod_estrutural varchar                                                    
                             , nivel integer                                                               
                             , nom_conta varchar                                                           
                             , cod_sistema integer                                                         
                             , indicador_superavit char(12)                                                    
                             , vl_saldo_anterior numeric                                                   
                             , vl_saldo_debitos  numeric                                                   
                             , vl_saldo_creditos numeric                                                   
                             , vl_saldo_atual    numeric                                                   
                             )
               WHERE cod_estrutural SIMILAR TO ''4.5.1.1.0%|3.5.1.1.0%|4.5.1.2.0%|3.5.1.2.0%|4.5.1.3.0%|3.5.1.3%|6.3.2.2.0%|6.3.1.4.0%''
            GROUP BY descricao
             ) AS valores_caixa
    GROUP BY valores_caixa.descricao
    ';

    EXECUTE stSql;
    
--Criando tabela para juntar todos os resultados
    stSql :=' CREATE TEMPORARY TABLE resultado_fluxo_caixa AS
                SELECT  descricao
                        ,ABS(valor) AS valor
                        ,ABS(valor_anterior) AS valor_anterior
                        ,0.00 AS saldo_inicial
                        ,0.00 AS saldo_final
                        ,0.00 AS saldo_inicial_anterior
                        ,0.00 AS saldo_final_anterior
                    FROM fluxo_caixa_receita
                    WHERE descricao <> ''''
            UNION
                SELECT  descricao
                        ,ABS(valor) AS valor
                        ,ABS(valor_anterior) AS valor_anterior
                        ,0.00 AS saldo_inicial
                        ,0.00 AS saldo_final
                        ,0.00 AS saldo_inicial_anterior
                        ,0.00 AS saldo_final_anterior
                    FROM fluxo_caixa_despesa
                    WHERE descricao <> ''''
            UNION
                SELECT  descricao
                        , 0.00 AS valor
                        , 0.00 AS valor_anterior
                        , saldo_inicial
                        , saldo_final
                        , saldo_inicial_anterior
                        , saldo_final_anterior
                    FROM fluxo_caixa_saldo
                    WHERE descricao <> ''''
            ORDER BY descricao

            ';

    EXECUTE stSql;

--CRIANDO TABELA PARA RESULTADO DO RELATORIO 
    stSql := 'CREATE TEMPORARY TABLE fluxo_valores_descricao
                (
                    ordem               INTEGER
                    ,descricao          VARCHAR
                    ,valor              NUMERIC
                    ,valor_anterior     NUMERIC
                )
        ';
    EXECUTE stSql;
    
    
--CRIANDO DESCRICOES    
    arDescricao[0] := 'FLUXOS DE CAIXA DAS ATIVIDADES OPERACIONAIS (I)';
    arDescricao[1] := 'INGRESSOS';
    arDescricao[2] := 'RECEITAS DERIVADAS E ORIGINÁRIAS';
    arDescricao[3] := 'Receita Tributária';
    arDescricao[4] := 'Receita de Contribuições';
    arDescricao[5] := 'Receita Patrimonial';
    arDescricao[6] := 'Receita Agropecuária';
    arDescricao[7] := 'Receita Industrial';
    arDescricao[8] := 'Receita de Serviços';
    arDescricao[9] := 'Remuneração das Disponibilidades';
    arDescricao[10] := 'Outras Receitas Derivadas e Oiginárias';
    arDescricao[11] := 'TRANSFERÊNCIAS CORRENTES RECEBIDAS';
    arDescricao[12] := 'Intergovernamentais';
    arDescricao[13] := 'da União';
    arDescricao[14] := 'de Estados e Distrito Federal';
    arDescricao[15] := 'de Municípios';
    arDescricao[16] := 'Intragovernamentais';
    arDescricao[17] := 'Outras transferências correntes recebidas';
    arDescricao[18] := 'OUTROS INGRESSOS OPERACIONAIS';
    arDescricao[19] := 'Transferências Financeiras Recebidas';
    arDescricao[20] := 'Depósitos Restituíveis e Valores Vinculados';
    arDescricao[21] := 'Outros Recebimentos Extraorçamenários';
    arDescricao[22] := 'DESEMBOLSOS';
    arDescricao[23] := 'PESSOAL E DEMAIS DESPESAS';
    arDescricao[24] := 'Legislativa';
    arDescricao[25] := 'Judiciária';
    arDescricao[26] := 'Essencial à Justiça';
    arDescricao[27] := 'Administração';
    arDescricao[28] := 'Defesa Nacional';
    arDescricao[29] := 'Segurança Pública';
    arDescricao[30] := 'Relações Exteriores';
    arDescricao[31] := 'Assistência Social';
    arDescricao[32] := 'Previdência Social';
    arDescricao[33] := 'Saúde';
    arDescricao[34] := 'Trabalho';
    arDescricao[35] := 'Educação';
    arDescricao[36] := 'Cultura';
    arDescricao[37] := 'Direitos da Cidadania';
    arDescricao[38] := 'Urbanismo';
    arDescricao[39] := 'Habitação';
    arDescricao[40] := 'Saneamento';
    arDescricao[41] := 'Gestão Ambiental';
    arDescricao[42] := 'Ciência e Tecnologia';
    arDescricao[43] := 'Agricultura';
    arDescricao[44] := 'Organização Agrária';
    arDescricao[45] := 'Indústria';
    arDescricao[46] := 'Comércio e Serviços';
    arDescricao[47] := 'Comunicações';
    arDescricao[48] := 'Energia';
    arDescricao[49] := 'Transporte';
    arDescricao[50] := 'Desporto e Lazer';
    arDescricao[51] := 'Encargos Especiais';
    arDescricao[52] := 'JUROS E ENCARGOS DA DÍVIDA';
    arDescricao[53] := 'Juros e Correção Monetária da Dívida Interna';
    arDescricao[54] := 'Juros e Correção Monetária da Dívida Externa';
    arDescricao[55] := 'Outros Encargos da Dívida';
    arDescricao[56] := 'TRANSFERÊNCIAS CONCEDIDAS';
    arDescricao[57] := 'Intergovernamentais';
    arDescricao[58] := 'a União';
    arDescricao[59] := 'a Estados e Distrito Federal';
    arDescricao[60] := 'a Municípios';
    arDescricao[61] := 'Intragovernamentais';
    arDescricao[62] := 'Outras transferências concedidas';
    
    arDescricao[63] := 'OUTROS DESEMBOLSOS OPERACIONAIS';
    arDescricao[64] := 'Transferências Finaceiras Concedidas';
    arDescricao[65] := 'Pagamentos de Restos a Pagar Processados';
    arDescricao[66] := 'Pagamentos de Restos a Pagar Não Processados';
    arDescricao[67] := 'Depósitos Restituíveis e Valores Vinculados';
    arDescricao[68] := 'Outros Pagamentos Extraorçamentários';
    
    arDescricao[69] := 'FLUXOS DE CAIXA DAS ATIVIDADES DE INVESTIMENTO (II)';
    arDescricao[70] := 'INGRESSOS';
    arDescricao[71] := 'ALIENAÇÃO DE BENS';
    arDescricao[72] := 'AMORTIZAÇÃO DE EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS';
    arDescricao[73] := 'OUTROS INGRESSOS DE INVESTIMENTOS';
    arDescricao[74] := 'DESEMBOLSOS';
    arDescricao[75] := 'AQUISIÇÃO DE ATIVO NÃO CIRCULANTE';
    arDescricao[76] := 'CONCESSÃO DE EMPRÉSTIMOS E FINANCIAMENTOS';
    arDescricao[77] := 'OUTROS DESEMBOLSOS DE INVESTIMENTOS';
    arDescricao[78] := 'FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO (III)';
    arDescricao[79] := 'INGRESSOS';
    arDescricao[80] := 'OPERAÇÕES DE CRÉDITO';
    arDescricao[81] := 'INTEGRALIZAÇÃO DO CAPITAL SOCIAL DE EMPRESAS DEPENDENTES';
    arDescricao[82] := 'TRANSFERÊNCIAS DE CAPITAL RECEBIDAS';
    arDescricao[83] := 'OUTROS INGRESSOS DE FINANCIAMENTOS';
    arDescricao[84] := 'DESEMBOLSOS';
    arDescricao[85] := 'AMORTIZAÇÃO / REFINANCIAMENTO DA DÍVIDA';
    arDescricao[86] := 'OUTROS DESEMBOLSOS DE FINANCIAMENTOS';
    arDescricao[87] := 'GERAÇÃO LÍQUIDA DE CAIXA E EQUIVALENTE DE CAIXA (I+II+III)';
    --arDescricao[88] := 'CAIXA E EQUIVALENTE DE CAIXA INICIAL';
    --arDescricao[89] := 'CAIXA E EQUIVALENTE DE CAIXA FINAL';
    

       
--CRIANDO RELACOES entres AS descricoes a serem exibidas com AS do banco de dados   
    arDescricaoAux[0]  := ''; -- 'FLUXOS DE CAIXA DAS ATIVIDADES OPERACIONAIS (I)';
    arDescricaoAux[1]  := ''; -- 'INGRESSOS';
    arDescricaoAux[2]  := ''; -- 'RECEITAS DERIVADAS E ORIGINÁRIAS';
    arDescricaoAux[3]  := 'receita_tributaria' ; -- 'Receita Tributária';
    arDescricaoAux[4]  := 'receita_contribuicoes' ; -- 'Receita de Contribuições';
    arDescricaoAux[5]  := 'receita_patrimonial' ; -- 'Receita Patrimonial';
    arDescricaoAux[6]  := 'receita_agropecuaria' ; -- 'Receita Agropecuária';
    arDescricaoAux[7]  := 'receita_industrial' ; -- 'Receita Industrial';
    arDescricaoAux[8]  := 'receita_servicos' ; -- 'Receita de Serviços';
    arDescricaoAux[9]  := 'remuneracao_disponibilidades' ; -- 'Remuneração das Disponibilidades';
    arDescricaoAux[10] := 'outras_receitas_derivadas' ; -- 'Outras Receitas Derivadas e Oiginárias';
    arDescricaoAux[11] := ''; -- 'TRANSFERÊNCIAS CORRENTES RECEBIDAS';
    arDescricaoAux[12] := ''; -- 'Intergovernamentais';
    arDescricaoAux[13] := 'transferencia_uniao' ; -- 'da União';
    arDescricaoAux[14] := 'transferencia_estados_df' ; -- 'de Estados e Distrito Federal';
    arDescricaoAux[15] := 'transferencia_municipios' ; -- 'de Municípios';
    arDescricaoAux[16] := ''; -- 'Intragovernamentais';
    arDescricaoAux[17] := 'outras_transferencias' ; -- 'Outras transferências correntes recebidas';
    arDescricaoAux[18] := ''; -- 'OUTROS INGRESSOS OPERACIONAIS';
    arDescricaoAux[19] := ''; -- 'Transferências Financeiras Recebidas';
    arDescricaoAux[20] := ''; -- 'Depósitos Restituíveis e Valores Vinculados';
    arDescricaoAux[21] := ''; -- 'Outros Recebimentos Extraorçamenários';
    arDescricaoAux[22] := '';            -- 'DESEMBOLSOS';
    arDescricaoAux[23] := '';            -- 'PESSOAL E DEMAIS DESPESAS';
    arDescricaoAux[24] := 'legislativa' ; -- 'Legislativa';
    arDescricaoAux[25] := 'judiciaria' ; -- 'Judiciária';
    arDescricaoAux[26] := 'essencial_justica' ; -- 'Essencial à Justiça';
    arDescricaoAux[27] := 'administracao' ; -- 'Administração';
    arDescricaoAux[28] := 'defesa_nacional' ; -- 'Defesa Nacional';
    arDescricaoAux[29] := 'seguranca_publica' ; -- 'Segurança Pública';
    arDescricaoAux[30] := 'relacoes_exteriores' ; -- 'Relações Exteriores';
    arDescricaoAux[31] := 'assistencia_social' ; -- 'Assistência Social';
    arDescricaoAux[32] := 'previdencia_social' ; -- 'Previdência Social';
    arDescricaoAux[33] := 'saude'; -- 'Saúde';
    arDescricaoAux[34] := 'trabalho'; -- 'Trabalho';
    arDescricaoAux[35] := 'educacao'; -- 'Educação';
    arDescricaoAux[36] := 'cultura'; -- 'Cultura';
    arDescricaoAux[37] := 'direitos_cidadania'; -- 'Direitos da Cidadania';
    arDescricaoAux[38] := 'urbanismo'; -- 'Urbanismo';
    arDescricaoAux[39] := 'habitacao'; -- 'Habitação';
    arDescricaoAux[40] := 'saneamento'; -- 'Saneamento';
    arDescricaoAux[41] := 'gestao_ambiental'; -- 'Gestão Ambiental';
    arDescricaoAux[42] := 'ciencia_tecnologia'; -- 'Ciência e Tecnologia';
    arDescricaoAux[43] := 'agricultura'; -- 'Agricultura';
    arDescricaoAux[44] := 'organizacao_agraria'; -- 'Organização Agrária';
    arDescricaoAux[45] := 'industria'; -- 'Indústria';
    arDescricaoAux[46] := 'comercio_servicos'; -- 'Comércio e Serviços';
    arDescricaoAux[47] := 'comunicacoes'; -- 'Comunicações';
    arDescricaoAux[48] := 'energia'; -- 'Energia';
    arDescricaoAux[49] := 'transporte'; -- 'Transporte';
    arDescricaoAux[50] := 'desporto_lazer'; -- 'Desporto e Lazer';
    arDescricaoAux[51] := 'encargos_especiais'; -- 'Encargos Especiais';
    arDescricaoAux[52] := ''; -- 'JUROS E ENCARGOS DA DÍVIDA';
    arDescricaoAux[53] := 'juros_correcao_divida_interna'; -- 'Juros e Correção Monetária da Dívida Interna';
    arDescricaoAux[54] := 'juros_correcao_divida_externa'; -- 'Juros e Correção Monetária da Dívida Externa';
    arDescricaoAux[55] := 'outros_encargos_divida'; -- 'Outros Encargos da Dívida';
    arDescricaoAux[56] := '' ; -- 'TRANSFERÊNCIAS CONCEDIDAS';
    arDescricaoAux[57] := '' ; -- 'Intergovernamentais';
    arDescricaoAux[58] := 'despesa_transferencia_uniao' ; -- 'a União';
    arDescricaoAux[59] := 'despesa_transferencia_estado_df' ; -- 'a Estados e Distrito Federal';
    arDescricaoAux[60] := 'despesa_transferencia_municipios' ; -- 'a Municípios';
    arDescricaoAux[61] := 'despesa_transferencia_intragovernamentais' ; -- 'Intragovernamentais';
    arDescricaoAux[62] := 'outras_transferencia_concedidas' ; -- 'Outras transferências concedidas';
    
    arDescricaoAux[63] := '' ; -- 'OUTROS DESEMBOLSOS OPERACIONAIS';
    arDescricaoAux[64] := '' ; -- 'Transferências Finaceiras Concedidas';
    arDescricaoAux[65] := '' ; -- 'Pagamentos de Restos a Pagar Processados';
    arDescricaoAux[66] := '' ; -- 'Pagamentos de Restos a Pagar Não Processados';
    arDescricaoAux[67] := '' ; -- 'Depósitos Restituíveis e Valores Vinculados';
    arDescricaoAux[68] := '' ; -- 'Outros Pagamentos Extraorçamentários';
    
    arDescricaoAux[69] := '' ; -- 'FLUXOS DE CAIXA DAS ATIVIDADES DE INVESTIMENTO (II)';
    arDescricaoAux[70] := '' ; -- 'INGRESSOS';
    arDescricaoAux[71] := 'alienacao_bens' ; -- 'ALIENAÇÃO DE BENS';
    arDescricaoAux[72] := 'amortizacao_emprestimos_financiamentos_concedidos' ; -- 'AMORTIZAÇÃO DE EMPRÉSTIMOS E FINANCIAMENTOS CONCEDIDOS';
    arDescricaoAux[73] := '' ; -- 'OUTROS INGRESSOS DE INVESTIMENTOS';
    arDescricaoAux[74] := '' ; -- 'DESEMBOLSOS';
    arDescricaoAux[75] := 'aquisicao_ativo_nao_circulante'; -- 'AQUISIÇÃO DE ATIVO NÃO CIRCULANTE';
    arDescricaoAux[76] := 'concessao_emprestimos_financeiros' ; -- 'CONCESSÃO DE EMPRÉSTIMOS E FINANCIAMENTOS';
    arDescricaoAux[77] := 'outros_desembolsos_investimentos' ; -- 'OUTROS DESEMBOLSOS DE INVESTIMENTOS';
    arDescricaoAux[78] := '' ; -- 'FLUXOS DE CAIXA DAS ATIVIDADES DE FINANCIAMENTO (III)';
    arDescricaoAux[79] := '' ; -- 'INGRESSOS';
    arDescricaoAux[80] := 'operacao_credito' ; -- 'OPERAÇÕES DE CRÉDITO';
    arDescricaoAux[81] := 'integracao_capital_social_empresas_dependentes' ; -- 'INTEGRALIZAÇÃO DO CAPITAL SOCIAL DE EMPRESAS DEPENDENTES';
    arDescricaoAux[82] := 'transferencias_capital_recebidas' ; -- 'TRANSFERÊNCIAS DE CAPITAL RECEBIDAS';
    arDescricaoAux[83] := '' ; -- 'OUTROS INGRESSOS DE FINANCIAMENTOS';
    arDescricaoAux[84] := '' ; -- 'DESEMBOLSOS';
    arDescricaoAux[85] := 'amortizacao_refinanciamento_divida' ; -- 'AMORTIZAÇÃO / REFINANCIAMENTO DA DÍVIDA';
    arDescricaoAux[86] := 'outros_desembolsos_financiamentos' ; -- 'OUTROS DESEMBOLSOS DE FINANCIAMENTOS';
    arDescricaoAux[87] := '' ; -- 'GERAÇÃO LÍQUIDA DE CAIXA E EQUIVALENTE DE CAIXA (I+II+III)';
    --arDescricaoAux[88] := '' ; -- 'CAIXA E EQUIVALENTE DE CAIXA INICIAL';
    --arDescricaoAux[89] := '' ; -- 'CAIXA E EQUIVALENTE DE CAIXA FINAL';

    
--FOR para insert dos valores na descricões certas  
    FOR i IN 0..87 LOOP
        INSERT INTO fluxo_valores_descricao VALUES( i
                                                    ,arDescricao[i]
                                                    ,COALESCE((SELECT valor FROM resultado_fluxo_caixa WHERE descricao = arDescricaoAux[i]),0.00)
                                                    ,COALESCE((SELECT valor_anterior FROM resultado_fluxo_caixa WHERE descricao = arDescricaoAux[i]),0.00)
                                                    );                                       
    END LOOP;

    --INSERT CAIXA INICIAL
    INSERT INTO fluxo_valores_descricao VALUES( 88
                                                ,'CAIXA E EQUIVALENTE DE CAIXA INICIAL'
                                                ,COALESCE((SELECT saldo_inicial FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                ,COALESCE((SELECT saldo_inicial_anterior FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                );
    --INSERT CAIXA FINAL
    INSERT INTO fluxo_valores_descricao VALUES( 89
                                                ,'CAIXA E EQUIVALENTE DE CAIXA FINAL'
                                                ,COALESCE((SELECT saldo_final FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                ,COALESCE((SELECT saldo_final_anterior FROM resultado_fluxo_caixa WHERE descricao = 'saldo_caixa'),0.00)
                                                );

    
--UPDATES para agregar os valores na tabela
    --RECEITAS DERIVADAS E ORIGINÁRIAS L3(L4+L5+L6+L7+L8+L9+L10+L11)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (3,4,5,6,7,8,9,10))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (3,4,5,6,7,8,9,10))
           WHERE ordem = 2;
    --INTERGOVERNAMENTAIS RECEITA L13(L14+L15+L16)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (13,14,15))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (13,14,15))
           WHERE ordem = 12;
    
    -- Transferências Financeiras Recebidas
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_final) AS valor FROM fluxo_balanco_financeiro WHERE descricao SIMILAR TO ('transferencias_recebidas_orcamentaria|transferencias_recebidas_independentes_orcamentaria|transferencias_recebidas_cobertura')),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_final_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao SIMILAR TO ('transferencias_recebidas_orcamentaria|transferencias_recebidas_independentes_orcamentaria|transferencias_recebidas_cobertura')),0.00)
           WHERE ordem = 19;
    -- Depósitos Restituíveis e Valores Vinculados
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_creditos) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_creditos_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
           WHERE ordem = 20;
    -- Outros Recebimentos Extraorçamenários
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_creditos) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_creditos_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
           WHERE ordem = 21;
    
    --TRANSFERÊNCIA CORRENTE RECEBIDAS L12(L13+L17+L18)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (12,16,17))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (12,16,17))
           WHERE ordem = 11;
    -- OUTROS INGRESSOS OPERACIONAIS
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (19,20,21))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (19,20,21))
           WHERE ordem = 18;
           
    -- INGRESSOS (I) RECEITA L2(L3+L12+L19)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (2,11,18))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (2,11,18))
           WHERE ordem = 1;
    
    
    --PESSOAL E DEMAIS DESPESAS L21 (L22 ATÉ L49)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51))
           WHERE ordem = 23;
    --JUROS E ENCARGOS DA DÍVIDA L50 (L51+L52+L53)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (53,54,55))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (53,54,55))
           WHERE ordem = 52;
    --INTERGOVERNAMENTAIS DESPESA L55 (L56+L57+L58)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (58,59,60))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (58,59,60))
            WHERE ordem = 57;

    -- Transferências Finaceiras Concedidas
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_final) AS valor FROM fluxo_balanco_financeiro WHERE descricao SIMILAR TO ('tranferencias_concedidas_orcamentaria|transferencias_concedidas_independentes_orcamentaria|transferencias_concedidas_cobertura')),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_final_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao SIMILAR TO ('tranferencias_concedidas_orcamentaria|transferencias_concedidas_independentes_orcamentaria|transferencias_concedidas_cobertura')),0.00)
           WHERE ordem = 64;
    -- Pagamentos de Restos a Pagar Processados
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_final) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'pagamento_restos_pagar_processados'),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_final_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'pagamento_restos_pagar_processados'),0.00)
           WHERE ordem = 65;
    -- Pagamentos de Restos a Pagar Não Processados
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_final) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'pagamento_restos_pagar_nao_processados'),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_final_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'pagamento_restos_pagar_nao_processados'),0.00)
           WHERE ordem = 66;
    -- Depósitos Restituíveis e Valores Vinculados
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_debitos) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_debitos_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'depositos_restituiveis_valores_vinculados'),0.00)
           WHERE ordem = 67;
    -- Outros Recebimentos Extraorçamenários
          UPDATE fluxo_valores_descricao
             SET valor = COALESCE((SELECT SUM(saldo_debitos) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
               , valor_anterior = COALESCE((SELECT SUM(saldo_debitos_anterior) AS valor FROM fluxo_balanco_financeiro WHERE descricao = 'outros_recebimentos_ext'),0.00)
           WHERE ordem = 68;

    --TRÂNSFERÊNCIA CONCEDIDAS L54 (L55+L59+L60)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (57,61,62))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (57,61,62))
            WHERE ordem = 56;

    --OUTROS DESEMBOLSOS OPERACIONAIS
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (64,65,66,67,68))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (64,65,66,67,68))
            WHERE ordem = 63;


    --DESEMBOLSOS DESPESA (I) L20 (L21+L50+L54+L61)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (23,52,56,63))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (23,52,56,63))
            WHERE ordem = 22;
    ----FLUXOS DE CAIXA DAS ATIVIDADES OPERACIONAIS (I) L1 (L2-L20)
    --        UPDATE fluxo_valores_descricao
    --        SET	    valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (51,52))
    --                ,valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (51,52))
    --        WHERE ordem = 50;



    --INGRESSOS RECEITAS (II) L63 (L64+L65+L66)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (71,72,73))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (71,72,73))
            WHERE ordem = 70;
    --DESEMBOLSOS DESPESAS (II) L67 (L68+L69+L70)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (75,76,77))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (75,76,77))
            WHERE ordem = 74;
            
    --INGRESSOS RECEITA (III) L72 (L73+L74+L75+L76)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (80,81,82,83))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (80,81,82,83))
            WHERE ordem = 79;
    --DESEMBOLSOS DESPESA (III) L77 (L78+L79)
          UPDATE fluxo_valores_descricao
             SET valor = (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem IN (85,86))
               , valor_anterior = (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem IN (85,86))
            WHERE ordem = 84;

    --FLUXO DE CAIXA LÍQUIDO DAS ATIVIDADES DAS OPERAÇÕES
      UPDATE fluxo_valores_descricao
         SET valor = (SELECT (( SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem =  1) - (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem = 22))
                           + (( SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem = 70) - (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem = 74))
                           + (( SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem = 79) - (SELECT SUM(valor) AS valor FROM fluxo_valores_descricao WHERE ordem = 84)))
           , valor_anterior = (SELECT (( SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem =  1) - (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem = 22))
                                    + (( SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem = 70) - (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem = 74))
                                    + (( SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem = 79) - (SELECT SUM(valor_anterior) AS valor FROM fluxo_valores_descricao WHERE ordem = 84)))
       WHERE ordem = 87;
    
    --INSERCAO dos niveis
       ALTER
       TABLE fluxo_valores_descricao
         ADD
      COLUMN nivel INTEGER;
            
      UPDATE fluxo_valores_descricao
         SET nivel = 1
       WHERE ordem IN (0,69,78,87);
            
      UPDATE fluxo_valores_descricao
         SET nivel = 2
       WHERE ordem IN (1,22,70,74,79,84,88,89);
        
      UPDATE fluxo_valores_descricao
         SET nivel = 3
       WHERE ordem IN (2,11,18,23,52,56,63,71,72,73,75,76,77,80,81,82,83,85,86);

      UPDATE fluxo_valores_descricao
         SET nivel = 5
       WHERE ordem IN (13,14,15,58,59,60);

      UPDATE fluxo_valores_descricao
         SET nivel = 4
       WHERE nivel IS NULL;
            
    
    stSql :='SELECT * FROM fluxo_valores_descricao ORDER by ordem';
    
FOR reRegistro IN EXECUTE stSql
LOOP
    RETURN NEXT reRegistro;
END LOOP;

DROP TABLE fluxo_valores_descricao;
DROP TABLE resultado_fluxo_caixa;
DROP TABLE fluxo_balanco_financeiro;
DROP TABLE fluxo_caixa_receita;
DROP TABLE fluxo_caixa_despesa;
DROP TABLE fluxo_caixa_saldo;

END;
$$ LANGUAGE 'plpgsql';


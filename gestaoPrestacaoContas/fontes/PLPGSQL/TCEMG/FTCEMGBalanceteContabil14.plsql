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

    $Id: FTCEMGBalanceteContabil14.plsql 62872 2015-07-01 20:16:55Z franver $

* $Revision: 62872 $
* $Name$
* $Author: franver $
* $Date: 2015-07-01 17:16:55 -0300 (Wed, 01 Jul 2015) $
*
* Casos de uso: uc-02.02.22
*/
/*
CREATE TYPE balancete_contabil_registro_14
    AS ( tipo_registro              INTEGER
       , conta_contabil             VARCHAR
       , cod_orgao                  VARCHAR
       , num_orgao                  INTEGER
       , num_unidade                INTEGER
       , cod_funcao                 INTEGER
       , cod_sub_funcao             INTEGER
       , cod_programa               INTEGER
       , id_acao                    INTEGER
       , id_sub_acao                VARCHAR
       , natureza_despesa           VARCHAR
       , sub_elemento               VARCHAR
       , cod_fonte_recursos         INTEGER
       , numero_empenho             INTEGER
       , ano_inscricao              VARCHAR
       , saldo_inicial_rsp          NUMERIC
       , natureza_saldo_inicial_rsp CHAR(1)
       , total_debitos_rsp          NUMERIC
       , total_creditos_rsp         NUMERIC
       , saldo_final_rsp            NUMERIC
       , natureza_saldo_final_rsp   CHAR(1)
    );
*/
CREATE OR REPLACE FUNCTION tcemg.fn_balancete_contabil_14(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF balancete_contabil_registro_14 AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stFiltro            ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stSql               VARCHAR := '';
    stSqlComplemento    VARCHAR := '';
    stExercicioAnterior VARCHAR := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
BEGIN
    stExercicioAnterior := stExercicio::INTEGER-1;
    stSql := '
   CREATE TEMPORARY TABLE tmp_debito AS
             SELECT *
               FROM (
                     SELECT pc.cod_estrutural
                          , pa.cod_plano
                          , COALESCE(despesa.num_orgao, 0) AS num_orgao
                          , COALESCE(despesa.num_unidade, 0) AS num_unidade
                          , COALESCE(despesa.cod_funcao, 0) AS cod_funcao
                          , COALESCE(despesa.cod_subfuncao, 0) AS cod_subfuncao
                          , COALESCE(p_programa.num_programa, 0) AS num_programa
                          , COALESCE(acao.num_acao, 0) AS num_acao
                          , COALESCE(recurso.cod_recurso, 0) AS cod_recurso
                          , SUBSTR(REPLACE(conta_despesa.cod_estrutural, ''.'',''''), 1, 8)::VARCHAR AS natureza_despesa
                          , total_lancamento.cod_empenho
                          , total_lancamento.exercicio_empenho
                          , vl.tipo_valor
                          , CASE WHEN lo.tipo = ''S''
                                 THEN total_lancamento.valor
                                 ELSE vl.vl_lancamento
                             END AS vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , vl.oid as oid_temp
                          , sc.cod_sistema
                          , pc.escrituracao
                          , pc.indicador_superavit
                       FROM contabilidade.plano_conta AS pc
                 INNER JOIN contabilidade.plano_analitica AS pa
                         ON pc.cod_conta    = pa.cod_conta
                        AND pc.exercicio    = pa.exercicio 
                 INNER JOIN contabilidade.conta_debito AS cd
                         ON pa.cod_plano    = cd.cod_plano
                        AND pa.exercicio    = cd.exercicio
                 INNER JOIN contabilidade.valor_lancamento AS vl
                         ON cd.cod_lote     = vl.cod_lote
                        AND cd.tipo         = vl.tipo
                        AND cd.sequencia    = vl.sequencia
                        AND cd.exercicio    = vl.exercicio
                        AND cd.tipo_valor   = vl.tipo_valor
                        AND cd.cod_entidade = vl.cod_entidade
                 INNER JOIN contabilidade.lancamento AS la
                         ON vl.cod_lote     = la.cod_lote
                        AND vl.tipo         = la.tipo
                        AND vl.sequencia    = la.sequencia
                        AND vl.exercicio    = la.exercicio
                        AND vl.cod_entidade = la.cod_entidade
                 INNER JOIN contabilidade.lote AS lo
                         ON la.cod_lote     = lo.cod_lote
                        AND la.exercicio    = lo.exercicio
                        AND la.tipo         = lo.tipo
                        AND la.cod_entidade = lo.cod_entidade
                 INNER JOIN contabilidade.sistema_contabil AS sc
                         ON sc.cod_sistema  = pc.cod_sistema
                        AND sc.exercicio    = pc.exercicio

                         -- SUPLEMENTAÇÃO SUPLEMENTAÇÃO
                  LEFT JOIN (SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , COALESCE(suplementacao_suplementada.valor, 0.00) AS valor
                                  , suplementacao_suplementada.exercicio AS exercicio_despesa
                                  , suplementacao_suplementada.cod_despesa
                                  , 0 AS cod_empenho
                                  , ''0''::VARCHAR AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_transferencia
                                 ON lancamento_transferencia.exercicio    = lancamento.exercicio
                                AND lancamento_transferencia.cod_entidade = lancamento.cod_entidade
                                AND lancamento_transferencia.tipo         = lancamento.tipo
                                AND lancamento_transferencia.cod_lote     = lancamento.cod_lote
                                --AND lancamento_transferencia.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.transferencia_despesa
                                 ON transferencia_despesa.exercicio    = lancamento_transferencia.exercicio
                                AND transferencia_despesa.cod_entidade = lancamento_transferencia.cod_entidade
                                AND transferencia_despesa.tipo         = lancamento_transferencia.tipo
                                AND transferencia_despesa.cod_lote     = lancamento_transferencia.cod_lote
                                AND transferencia_despesa.sequencia    = lancamento_transferencia.sequencia
                                AND transferencia_despesa.cod_tipo     = lancamento_transferencia.cod_tipo
                         INNER JOIN orcamento.suplementacao
                                 ON suplementacao.exercicio         = transferencia_despesa.exercicio
                                AND suplementacao.cod_suplementacao = transferencia_despesa.cod_suplementacao
                         INNER JOIN orcamento.suplementacao_suplementada
                                 ON suplementacao_suplementada.exercicio         = suplementacao.exercicio
                                AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                             -- LANÇAMENTOS PAGOS
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_empenho
                                 ON lancamento_empenho.exercicio    = lancamento.exercicio
                                AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                                AND lancamento_empenho.tipo         = lancamento.tipo
                                AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                                --AND lancamento_empenho.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.pagamento
                                 ON pagamento.exercicio    = lancamento_empenho.exercicio
                                AND pagamento.cod_entidade = lancamento_empenho.cod_entidade
                                AND pagamento.cod_lote     = lancamento_empenho.cod_lote
                                AND pagamento.sequencia    = lancamento_empenho.sequencia
                                AND pagamento.tipo         = lancamento_empenho.tipo
                         INNER JOIN empenho.nota_liquidacao_paga
                                 ON nota_liquidacao_paga.exercicio    = pagamento.exercicio_liquidacao
                                AND nota_liquidacao_paga.cod_entidade = pagamento.cod_entidade
                                AND nota_liquidacao_paga.cod_nota     = pagamento.cod_nota
                                AND nota_liquidacao_paga.timestamp    = pagamento.timestamp
                         INNER JOIN empenho.nota_liquidacao
                                 ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                             -- LANÇAMENTO LIQUIDADOS
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_empenho
                                 ON lancamento_empenho.exercicio    = lancamento.exercicio
                                AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                                AND lancamento_empenho.tipo         = lancamento.tipo
                                AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                                --AND lancamento_empenho.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.liquidacao
                                 ON liquidacao.exercicio    = lancamento_empenho.exercicio
                                AND liquidacao.cod_entidade = lancamento_empenho.cod_entidade
                                AND liquidacao.cod_lote     = lancamento_empenho.cod_lote
                                AND liquidacao.sequencia    = lancamento_empenho.sequencia
                                AND liquidacao.tipo         = lancamento_empenho.tipo
                         INNER JOIN empenho.nota_liquidacao
                                 ON nota_liquidacao.exercicio    = liquidacao.exercicio_liquidacao
                                AND nota_liquidacao.cod_entidade = liquidacao.cod_entidade
                                AND nota_liquidacao.cod_nota     = liquidacao.cod_nota
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                 
                             -- LANÇAMENTOS EMPENHADOS
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_empenho
                                 ON lancamento_empenho.exercicio    = lancamento.exercicio
                                AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                                AND lancamento_empenho.tipo         = lancamento.tipo
                                AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                                --AND lancamento_empenho.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.empenhamento
                                 ON empenhamento.exercicio    = lancamento_empenho.exercicio
                                AND empenhamento.cod_entidade = lancamento_empenho.cod_entidade
                                AND empenhamento.cod_lote     = lancamento_empenho.cod_lote
                                AND empenhamento.sequencia    = lancamento_empenho.sequencia
                                AND empenhamento.tipo         = lancamento_empenho.tipo
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = empenhamento.exercicio_empenho
                                AND empenho.cod_entidade = empenhamento.cod_entidade
                                AND empenho.cod_empenho  = empenhamento.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho

                             -- LANÇAMENTO RETENÇÂO
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_retencao
                                 ON lancamento_retencao.exercicio    = lancamento.exercicio
                                AND lancamento_retencao.cod_entidade = lancamento.cod_entidade
                                AND lancamento_retencao.tipo         = lancamento.tipo
                                AND lancamento_retencao.cod_lote     = lancamento.cod_lote
                                --AND lancamento_retencao.sequencia    = lancamento.sequencia
                         INNER JOIN empenho.ordem_pagamento_retencao
                                 ON ordem_pagamento_retencao.cod_ordem    = lancamento_retencao.cod_ordem
                                AND ordem_pagamento_retencao.cod_entidade = lancamento_retencao.cod_entidade
                                AND ordem_pagamento_retencao.cod_plano    = lancamento_retencao.cod_plano
                                AND ordem_pagamento_retencao.exercicio    = lancamento_retencao.exercicio_retencao
                                AND ordem_pagamento_retencao.sequencial   = lancamento_retencao.sequencial
                         INNER JOIN empenho.ordem_pagamento
                                 ON ordem_pagamento.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                                AND ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio
                                AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
                         INNER JOIN empenho.pagamento_liquidacao
                                 ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio
                                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem
                         INNER JOIN empenho.nota_liquidacao
                                 ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                                AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
                                AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                                
                          ) AS total_lancamento
                         ON total_lancamento.exercicio    = la.exercicio
                        AND total_lancamento.cod_entidade = la.cod_entidade
                        AND total_lancamento.tipo         = la.tipo
                        AND total_lancamento.cod_lote     = la.cod_lote
                        AND total_lancamento.sequencia    = la.sequencia
                        
                 INNER JOIN orcamento.despesa
                         ON despesa.exercicio   = total_lancamento.exercicio_despesa
                        AND despesa.cod_despesa = total_lancamento.cod_despesa
                 INNER JOIN orcamento.conta_despesa
                         ON conta_despesa.exercicio = despesa.exercicio
                        AND conta_despesa.cod_conta = despesa.cod_conta
                 INNER JOIN orcamento.programa
                         ON programa.cod_programa = despesa.cod_programa
                        AND programa.exercicio    = despesa.exercicio
                 INNER JOIN orcamento.programa_ppa_programa
                         ON programa_ppa_programa.cod_programa = programa.cod_programa
                        AND programa_ppa_programa.exercicio    = programa.exercicio
                 INNER JOIN ppa.programa AS p_programa
                         ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                 INNER JOIN orcamento.pao
                         ON pao.exercicio = despesa.exercicio
                        AND pao.num_pao   = despesa.num_pao
                 INNER JOIN orcamento.pao_ppa_acao
                         ON pao_ppa_acao.exercicio = despesa.exercicio
                        AND pao_ppa_acao.num_pao   = despesa.num_pao
                 INNER JOIN ppa.acao
                         ON acao.cod_acao = pao_ppa_acao.cod_acao
                 INNER JOIN orcamento.recurso
                         ON recurso.exercicio   = despesa.exercicio
                        AND recurso.cod_recurso = despesa.cod_recurso

                      WHERE vl.tipo_valor   = ''D''
                        AND pa.exercicio    = '''||stExercicio||'''
                   ORDER BY pc.cod_estrutural
                  ) as tabela
                 WHERE ' || stFiltro ;
    EXECUTE stSql;

    stSql := '
   CREATE TEMPORARY TABLE tmp_credito AS
             SELECT *
               FROM (SELECT pc.cod_estrutural
                          , pa.cod_plano
                          , COALESCE(despesa.num_orgao, 0) AS num_orgao
                          , COALESCE(despesa.num_unidade, 0) AS num_unidade
                          , COALESCE(despesa.cod_funcao, 0) AS cod_funcao
                          , COALESCE(despesa.cod_subfuncao, 0) AS cod_subfuncao
                          , COALESCE(p_programa.num_programa, 0) AS num_programa
                          , COALESCE(acao.num_acao, 0) AS num_acao
                          , COALESCE(recurso.cod_recurso, 0) AS cod_recurso
                          , SUBSTR(REPLACE(conta_despesa.cod_estrutural, ''.'',''''), 1, 8)::VARCHAR AS natureza_despesa
                          , total_lancamento.cod_empenho
                          , total_lancamento.exercicio_empenho
                          , vl.tipo_valor
                          , CASE WHEN lo.tipo = ''S''
                                 THEN total_lancamento.valor
                                 ELSE vl.vl_lancamento
                             END AS vl_lancamento
                          , vl.cod_entidade
                          , lo.cod_lote
                          , lo.dt_lote
                          , lo.exercicio
                          , lo.tipo
                          , vl.sequencia
                          , vl.oid as oid_temp
                          , sc.cod_sistema
                          , pc.escrituracao
                          , pc.indicador_superavit
                       FROM contabilidade.plano_conta       as pc
                 INNER JOIN contabilidade.plano_analitica   as pa
                         ON pc.cod_conta    = pa.cod_conta
                        AND pc.exercicio    = pa.exercicio
                 INNER JOIN contabilidade.conta_credito     as cc
                         ON pa.cod_plano    = cc.cod_plano
                        AND pa.exercicio    = cc.exercicio
                 INNER JOIN contabilidade.valor_lancamento  as vl
                         ON cc.cod_lote     = vl.cod_lote
                        AND cc.tipo         = vl.tipo
                        AND cc.sequencia    = vl.sequencia
                        AND cc.exercicio    = vl.exercicio
                        AND cc.tipo_valor   = vl.tipo_valor
                        AND cc.cod_entidade = vl.cod_entidade
                 INNER JOIN contabilidade.lancamento        as la
                         ON vl.cod_lote     = la.cod_lote
                        AND vl.tipo         = la.tipo
                        AND vl.sequencia    = la.sequencia
                        AND vl.exercicio    = la.exercicio
                        AND vl.cod_entidade = la.cod_entidade
                 INNER JOIN contabilidade.lote              as lo
                         ON la.cod_lote     = lo.cod_lote
                        AND la.exercicio    = lo.exercicio
                        AND la.tipo         = lo.tipo
                        AND la.cod_entidade = lo.cod_entidade
                 INNER JOIN contabilidade.sistema_contabil  as sc
                         ON sc.cod_sistema  = pc.cod_sistema
                        AND sc.exercicio    = pc.exercicio

            -- SUPLEMENTAÇÃO REDUÇÃO
                  LEFT JOIN (SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , COALESCE(suplementacao_reducao.valor, 0.00)*-1 AS valor
                                  , suplementacao_reducao.exercicio AS exercicio_despesa
                                  , suplementacao_reducao.cod_despesa
                                  , 0 AS cod_empenho
                                  , ''0''::VARCHAR AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_transferencia
                                 ON lancamento_transferencia.exercicio    = lancamento.exercicio
                                AND lancamento_transferencia.cod_entidade = lancamento.cod_entidade
                                AND lancamento_transferencia.tipo         = lancamento.tipo
                                AND lancamento_transferencia.cod_lote     = lancamento.cod_lote
                                --AND lancamento_transferencia.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.transferencia_despesa
                                 ON transferencia_despesa.exercicio    = lancamento_transferencia.exercicio
                                AND transferencia_despesa.cod_entidade = lancamento_transferencia.cod_entidade
                                AND transferencia_despesa.tipo         = lancamento_transferencia.tipo
                                AND transferencia_despesa.cod_lote     = lancamento_transferencia.cod_lote
                                AND transferencia_despesa.sequencia    = lancamento_transferencia.sequencia
                                AND transferencia_despesa.cod_tipo     = lancamento_transferencia.cod_tipo
                         INNER JOIN orcamento.suplementacao
                                 ON suplementacao.exercicio         = transferencia_despesa.exercicio
                                AND suplementacao.cod_suplementacao = transferencia_despesa.cod_suplementacao
                         INNER JOIN orcamento.suplementacao_reducao
                                 ON suplementacao_reducao.exercicio         = suplementacao.exercicio
                                AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                             -- LANÇAMENTOS PAGOS
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_empenho
                                 ON lancamento_empenho.exercicio    = lancamento.exercicio
                                AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                                AND lancamento_empenho.tipo         = lancamento.tipo
                                AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                                --AND lancamento_empenho.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.pagamento
                                 ON pagamento.exercicio    = lancamento_empenho.exercicio
                                AND pagamento.cod_entidade = lancamento_empenho.cod_entidade
                                AND pagamento.cod_lote     = lancamento_empenho.cod_lote
                                AND pagamento.sequencia    = lancamento_empenho.sequencia
                                AND pagamento.tipo         = lancamento_empenho.tipo
                         INNER JOIN empenho.nota_liquidacao_paga
                                 ON nota_liquidacao_paga.exercicio    = pagamento.exercicio_liquidacao
                                AND nota_liquidacao_paga.cod_entidade = pagamento.cod_entidade
                                AND nota_liquidacao_paga.cod_nota     = pagamento.cod_nota
                                AND nota_liquidacao_paga.timestamp    = pagamento.timestamp
                         INNER JOIN empenho.nota_liquidacao
                                 ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho

                             -- LANÇAMENTO LIQUIDADOS
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_empenho
                                 ON lancamento_empenho.exercicio    = lancamento.exercicio
                                AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                                AND lancamento_empenho.tipo         = lancamento.tipo
                                AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                                --AND lancamento_empenho.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.liquidacao
                                 ON liquidacao.exercicio    = lancamento_empenho.exercicio
                                AND liquidacao.cod_entidade = lancamento_empenho.cod_entidade
                                AND liquidacao.cod_lote     = lancamento_empenho.cod_lote
                                AND liquidacao.sequencia    = lancamento_empenho.sequencia
                                AND liquidacao.tipo         = lancamento_empenho.tipo
                         INNER JOIN empenho.nota_liquidacao
                                 ON nota_liquidacao.exercicio    = liquidacao.exercicio_liquidacao
                                AND nota_liquidacao.cod_entidade = liquidacao.cod_entidade
                                AND nota_liquidacao.cod_nota     = liquidacao.cod_nota
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                 
                             -- LANÇAMENTOS EMPENHADOS
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_empenho
                                 ON lancamento_empenho.exercicio    = lancamento.exercicio
                                AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                                AND lancamento_empenho.tipo         = lancamento.tipo
                                AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                                --AND lancamento_empenho.sequencia    = lancamento.sequencia
                         INNER JOIN contabilidade.empenhamento
                                 ON empenhamento.exercicio    = lancamento_empenho.exercicio
                                AND empenhamento.cod_entidade = lancamento_empenho.cod_entidade
                                AND empenhamento.cod_lote     = lancamento_empenho.cod_lote
                                AND empenhamento.sequencia    = lancamento_empenho.sequencia
                                AND empenhamento.tipo         = lancamento_empenho.tipo
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = empenhamento.exercicio_empenho
                                AND empenho.cod_entidade = empenhamento.cod_entidade
                                AND empenho.cod_empenho  = empenhamento.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho

                             -- LANÇAMENTO RETENÇÃO
                             UNION
                             SELECT lancamento.exercicio
                                  , lancamento.cod_entidade
                                  , lancamento.tipo
                                  , lancamento.cod_lote
                                  , lancamento.sequencia
                                  , 0.00 AS valor
                                  , pre_empenho_despesa.exercicio AS exercicio_despesa
                                  , pre_empenho_despesa.cod_despesa
                                  , empenho.cod_empenho AS cod_empenho
                                  , empenho.exercicio AS exercicio_empenho
                               FROM contabilidade.lancamento
                         INNER JOIN contabilidade.lancamento_retencao
                                 ON lancamento_retencao.exercicio    = lancamento.exercicio
                                AND lancamento_retencao.cod_entidade = lancamento.cod_entidade
                                AND lancamento_retencao.tipo         = lancamento.tipo
                                AND lancamento_retencao.cod_lote     = lancamento.cod_lote
                                --AND lancamento_retencao.sequencia    = lancamento.sequencia
                         INNER JOIN empenho.ordem_pagamento_retencao
                                 ON ordem_pagamento_retencao.cod_ordem    = lancamento_retencao.cod_ordem
                                AND ordem_pagamento_retencao.cod_entidade = lancamento_retencao.cod_entidade
                                AND ordem_pagamento_retencao.cod_plano    = lancamento_retencao.cod_plano
                                AND ordem_pagamento_retencao.exercicio    = lancamento_retencao.exercicio_retencao
                                AND ordem_pagamento_retencao.sequencial   = lancamento_retencao.sequencial
                         INNER JOIN empenho.ordem_pagamento
                                 ON ordem_pagamento.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                                AND ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio
                                AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
                         INNER JOIN empenho.pagamento_liquidacao
                                 ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio
                                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem
                         INNER JOIN empenho.nota_liquidacao
                                 ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                                AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
                                AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota
                         INNER JOIN empenho.empenho
                                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                          ) AS total_lancamento
                         ON total_lancamento.exercicio    = la.exercicio
                        AND total_lancamento.cod_entidade = la.cod_entidade
                        AND total_lancamento.tipo         = la.tipo
                        AND total_lancamento.cod_lote     = la.cod_lote
                        AND total_lancamento.sequencia    = la.sequencia

                 INNER JOIN orcamento.despesa
                         ON despesa.exercicio   = total_lancamento.exercicio_despesa
                        AND despesa.cod_despesa = total_lancamento.cod_despesa
                 INNER JOIN orcamento.conta_despesa
                         ON conta_despesa.exercicio = despesa.exercicio
                        AND conta_despesa.cod_conta = despesa.cod_conta
                 INNER JOIN orcamento.programa
                         ON programa.cod_programa = despesa.cod_programa
                        AND programa.exercicio    = despesa.exercicio
                 INNER JOIN orcamento.programa_ppa_programa
                         ON programa_ppa_programa.cod_programa = programa.cod_programa
                        AND programa_ppa_programa.exercicio    = programa.exercicio
                 INNER JOIN ppa.programa AS p_programa
                         ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                 INNER JOIN orcamento.pao
                         ON pao.exercicio = despesa.exercicio
                        AND pao.num_pao   = despesa.num_pao
                 INNER JOIN orcamento.pao_ppa_acao
                         ON pao_ppa_acao.exercicio = despesa.exercicio
                        AND pao_ppa_acao.num_pao   = despesa.num_pao
                 INNER JOIN ppa.acao
                         ON acao.cod_acao = pao_ppa_acao.cod_acao
                 INNER JOIN orcamento.recurso
                         ON recurso.exercicio   = despesa.exercicio
                        AND recurso.cod_recurso = despesa.cod_recurso
                      WHERE pa.exercicio    = '''||stExercicio||'''
                        AND vl.tipo_valor   = ''C''
                   ORDER BY pc.cod_estrutural
                    ) as tabela
              WHERE '||stFiltro ;
    EXECUTE stSql;

    stSql := '
        INSERT INTO tcemg.balancete_contabil_14_restos_pagar
             SELECT COALESCE(num_orgao, 0) AS num_orgao
                  , COALESCE(num_unidade, 0) AS num_unidade
                  , COALESCE(cod_funcao, 0) AS cod_funcao
                  , COALESCE(cod_subfuncao, 0) AS cod_subfuncao
                  , COALESCE(num_programa, 0) AS num_programa
                  , COALESCE(num_acao, 0) AS num_acao
                  , COALESCE(cod_recurso, 0) AS cod_recurso
                  , natureza_despesa
                  , cod_empenho
                  , exercicio_empenho
                  , ''D'' AS tipo_valor
                  , (valor_liquidado - valor_pago) AS vl_lancamento_rp_processados
                  , (valor_empenhado - valor_liquidado - valor_anulado) AS vl_lancamento_rp_nao_processados
                  , cod_entidade
                  , 999999 AS cod_lote
                  , '''||stExercicio||'-01-01'' AS dt_lote
                  , exercicio
                  , ''I''::CHAR(1) AS tipo
                  , 99 AS sequencia
                  , 999999 AS oid_temp
                  , 4 AS cod_sistema
               FROM (SELECT ped_d_cd.num_orgao
                          , ped_d_cd.num_unidade
                          , ped_d_cd.cod_funcao
                          , ped_d_cd.cod_subfuncao
                          , ped_d_cd.num_programa
                          , ped_d_cd.num_acao
                          , ped_d_cd.cod_recurso
                          , SUBSTR(REPLACE(ped_d_cd.cod_estrutural, ''.'',''''), 1, 8)::VARCHAR AS natureza_despesa
                          , e.cod_empenho
                          , e.exercicio AS exercicio_empenho
                          , e.cod_entidade AS cod_entidade
                          , '''||stExercicio||''' AS exercicio
                          , empenho.fn_empenho_empenhado( e.exercicio ,e.cod_empenho, e.cod_entidade,''01/01/'||stExercicioAnterior||''' ,''31/12/'||stExercicioAnterior||''') AS valor_empenhado
                          , (empenho.fn_empenho_pago( e.exercicio ,e.cod_empenho, e.cod_entidade,''01/01/'||stExercicioAnterior||''' ,''31/12/'||stExercicioAnterior||''') - empenho.fn_empenho_estornado( e.exercicio,e.cod_empenho , e.cod_entidade ,''01/01/'||stExercicioAnterior||''' ,''31/12/'||stExercicioAnterior||'''  )) AS valor_pago
                          , (empenho.fn_empenho_liquidado( e.exercicio ,e.cod_empenho , e.cod_entidade ,''01/01/'||stExercicioAnterior||''' ,''31/12/'||stExercicioAnterior||''') - empenho.fn_empenho_estorno_liquidacao( e.exercicio ,e.cod_empenho ,e.cod_entidade ,''01/01/'||stExercicioAnterior||''' ,''31/12/'||stExercicioAnterior||'''  )) AS valor_liquidado
                          , empenho.fn_empenho_anulado( e.exercicio ,e.cod_empenho , e.cod_entidade,''01/01/'||stExercicioAnterior||''' ,''31/12/'||stExercicioAnterior||''') AS valor_anulado
                       FROM empenho.empenho     AS e
                          , empenho.pre_empenho AS pe
                  LEFT JOIN empenho.restos_pre_empenho AS rpe
                         ON pe.exercicio        = rpe.exercicio
                        AND pe.cod_pre_empenho  = rpe.cod_pre_empenho
                  LEFT JOIN (SELECT ped.exercicio
                                  , ped.cod_pre_empenho
                                  , d.num_orgao
                                  , d.num_unidade
                                  , d.cod_recurso
                                  , d.cod_programa
                                  , d.num_pao
                                  , cd.cod_estrutural
                                  , d.cod_funcao
                                  , d.cod_subfuncao
                                  , rec.masc_recurso_red
                                  , rec.cod_detalhamento
                                  , ppa.programa.num_programa
                                  , ppa.acao.num_acao
                               FROM empenho.pre_empenho_despesa AS ped
                                  , orcamento.despesa           AS d
                         INNER JOIN orcamento.recurso('''||stExercicioAnterior||''') AS rec
                                 ON rec.exercicio = d.exercicio
                                AND rec.cod_recurso = d.cod_recurso
                         INNER JOIN orcamento.programa_ppa_programa
                                 ON programa_ppa_programa.cod_programa = d.cod_programa
                                AND programa_ppa_programa.exercicio   = d.exercicio
                         INNER JOIN ppa.programa
                                 ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                         INNER JOIN orcamento.pao_ppa_acao
                                 ON pao_ppa_acao.num_pao = d.num_pao
                                AND pao_ppa_acao.exercicio = d.exercicio
                         INNER JOIN ppa.acao 
                                 ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                                  , orcamento.conta_despesa     AS cd
                              WHERE ped.cod_despesa = d.cod_despesa
                                AND ped.exercicio   = d.exercicio
                                AND ped.cod_conta     = cd.cod_conta
                                AND ped.exercicio     = cd.exercicio
                            ) AS ped_d_cd
                         ON pe.exercicio       = ped_d_cd.exercicio
                        AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
                      WHERE e.exercicio         = '''||stExercicioAnterior||'''
                        AND e.exercicio         = pe.exercicio
                        AND e.exercicio         = pe.exercicio
                        AND e.cod_pre_empenho   = pe.cod_pre_empenho
                        AND e.'||stFiltro||'
                   ORDER BY e.cod_empenho
                    ) AS tbl

    ';

    EXECUTE stSql;
    
    stSql := '
    INSERT INTO tmp_debito
            SELECT plano_conta.cod_estrutural
                 , plano_analitica.cod_plano
                 , rp_balancete.num_orgao
                 , rp_balancete.num_unidade
                 , rp_balancete.cod_funcao
                 , rp_balancete.cod_subfuncao
                 , rp_balancete.num_programa
                 , rp_balancete.num_acao
                 , rp_balancete.cod_recurso
                 , rp_balancete.natureza_despesa
                 , rp_balancete.cod_empenho
                 , rp_balancete.exercicio_empenho
                 , rp_balancete.tipo_valor
                 , vl_lancamento_rp_processados AS vl_lancamento
                 , rp_balancete.cod_entidade
                 , rp_balancete.cod_lote
                 , rp_balancete.dt_lote
                 , rp_balancete.exercicio
                 , rp_balancete.tipo
                 , rp_balancete.sequencia
                 , rp_balancete.oid_temp
                 , rp_balancete.cod_sistema
                 , plano_conta.escrituracao
                 , plano_conta.indicador_superavit
              FROM tcemg.balancete_contabil_14_restos_pagar AS rp_balancete
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.exercicio = rp_balancete.exercicio
        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.exercicio = plano_conta.exercicio
               AND plano_analitica.cod_conta = plano_conta.cod_conta
             WHERE plano_conta.exercicio = '''||stExercicio||'''
               AND plano_conta.cod_estrutural SIMILAR TO ''5.3.2.7.0.00.00.00.00.00|6.3.2.7.0.00.00.00.00.00''
    ';
    
    EXECUTE stSql;

    stSql := '
    INSERT INTO tmp_credito
            SELECT plano_conta.cod_estrutural
                 , plano_analitica.cod_plano
                 , rp_balancete.num_orgao
                 , rp_balancete.num_unidade
                 , rp_balancete.cod_funcao
                 , rp_balancete.cod_subfuncao
                 , rp_balancete.num_programa
                 , rp_balancete.num_acao
                 , rp_balancete.cod_recurso
                 , rp_balancete.natureza_despesa
                 , rp_balancete.cod_empenho
                 , rp_balancete.exercicio_empenho
                 , rp_balancete.tipo_valor
                 , (vl_lancamento_rp_nao_processados*-1) AS vl_lancamento
                 , rp_balancete.cod_entidade
                 , rp_balancete.cod_lote
                 , rp_balancete.dt_lote
                 , rp_balancete.exercicio
                 , rp_balancete.tipo
                 , rp_balancete.sequencia
                 , rp_balancete.oid_temp
                 , rp_balancete.cod_sistema
                 , plano_conta.escrituracao
                 , plano_conta.indicador_superavit
              FROM tcemg.balancete_contabil_14_restos_pagar AS rp_balancete
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.exercicio = rp_balancete.exercicio
        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.exercicio = plano_conta.exercicio
               AND plano_analitica.cod_conta = plano_conta.cod_conta
             WHERE plano_conta.exercicio = '''||stExercicio||'''
               AND plano_conta.cod_estrutural SIMILAR TO ''5.3.1.7.0.00.00.00.00.00|6.3.1.7.1.00.00.00.00.00''
    ';
    
    EXECUTE stSql;
    
    CREATE UNIQUE INDEX unq_debito  ON tmp_debito  (cod_estrutural varchar_pattern_ops, num_orgao, num_unidade, cod_funcao, cod_subfuncao, num_programa, num_acao, cod_recurso, natureza_despesa, cod_empenho, exercicio_empenho, oid_temp);
    CREATE UNIQUE INDEX unq_credito ON tmp_credito (cod_estrutural varchar_pattern_ops, num_orgao, num_unidade, cod_funcao, cod_subfuncao, num_programa, num_acao, cod_recurso, natureza_despesa, cod_empenho, exercicio_empenho, oid_temp);

    CREATE TEMPORARY TABLE tmp_totaliza_debito AS
      SELECT *
        FROM tmp_debito
       WHERE dt_lote BETWEEN TO_DATE(stDtInicial::VARCHAR, 'dd/mm/yyyy')
                         AND TO_DATE(stDtFinal::VARCHAR  , 'dd/mm/yyyy')
         AND tipo <> 'I';

    CREATE TEMPORARY TABLE tmp_totaliza_credito AS
      SELECT *
        FROM tmp_credito
       WHERE dt_lote BETWEEN TO_DATE(stDtInicial::VARCHAR , 'dd/mm/yyyy' )
                         AND TO_DATE(stDtFinal::VARCHAR   , 'dd/mm/yyyy' )
         AND tipo <> 'I';

    CREATE UNIQUE INDEX unq_totaliza_credito ON tmp_totaliza_credito (cod_estrutural varchar_pattern_ops, num_orgao, num_unidade, cod_funcao, cod_subfuncao, num_programa, num_acao, cod_recurso, natureza_despesa, cod_empenho, exercicio_empenho, oid_temp);
    CREATE UNIQUE INDEX unq_totaliza_debito  ON tmp_totaliza_debito  (cod_estrutural varchar_pattern_ops, num_orgao, num_unidade, cod_funcao, cod_subfuncao, num_programa, num_acao, cod_recurso, natureza_despesa, cod_empenho, exercicio_empenho, oid_temp);
    
    IF substr(stDtInicial,1,5) =  '01/01' THEN
        stSqlComplemento := ' dt_lote = TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'') ';
        stSqlComplemento := stSqlComplemento || ' AND tipo = ''I'' ';
    ELSE
        stSqlComplemento := 'dt_lote BETWEEN TO_DATE(''01/01/''||SUBSTR(TO_CHAR(TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'') - 1,''dd/mm/yyyy'') ,7) ,''dd/mm/yyyy'')
                                         AND TO_DATE('''||stDtInicial||''',''dd/mm/yyyy'')-1 ';
    END IF;
    stSql := '
        CREATE TEMPORARY TABLE tmp_totaliza AS
                  SELECT *
                    FROM tmp_debito
                   WHERE '||stSqlComplemento||'
                   UNION
                  SELECT *
                    FROM tmp_credito
                   WHERE '||stSqlComplemento||'
    ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_totaliza ON tmp_totaliza (cod_estrutural varchar_pattern_ops, num_orgao, num_unidade, cod_funcao, cod_subfuncao, num_programa, num_acao, cod_recurso, natureza_despesa, cod_empenho, exercicio_empenho, oid_temp);

    stSql := '
     CREATE TEMPORARY TABLE tmp_contas_utilizadas AS
               SELECT *
                 FROM tmp_debito
                UNION
               SELECT *
                 FROM tmp_credito
                UNION
               SELECT *
                 FROM tmp_totaliza
      ';
    EXECUTE stSql;

    CREATE UNIQUE INDEX unq_contas_utilizadas ON tmp_contas_utilizadas (cod_estrutural varchar_pattern_ops, num_orgao, num_unidade, cod_funcao, cod_subfuncao, num_programa, num_acao, cod_recurso, natureza_despesa, cod_empenho, exercicio_empenho, oid_temp);

    stSql := '
           SELECT 14::INTEGER AS tipo_registro
                , plano_conta.cod_estrutural_contabil AS conta_contabil
                , LPAD(COALESCE(configuracao_entidade.valor, ''0'')::VARCHAR, 2, ''0'')::VARCHAR AS cod_orgao
                , COALESCE(tmp_contas_utilizadas.num_orgao, 0) AS num_orgao_rsp
                , COALESCE(tmp_contas_utilizadas.num_unidade, 0) AS num_unidade_rsp
                , COALESCE(tmp_contas_utilizadas.cod_funcao, 0) AS cod_funcao_rsp
                , COALESCE(tmp_contas_utilizadas.cod_subfuncao, 0) AS cod_subfuncao_rsp
                , COALESCE(tmp_contas_utilizadas.num_programa, 0) AS num_programa_rsp
                , COALESCE(tmp_contas_utilizadas.num_acao, 0) AS num_acao_rsp
                , '' ''::VARCHAR AS id_sub_acao
                , SUBSTR(RPAD(COALESCE(tmp_contas_utilizadas.natureza_despesa, ''0''), 8, ''0'')::VARCHAR, 1, 6)::VARCHAR AS natureza_despesa_reduzida
                , SUBSTR(RPAD(COALESCE(tmp_contas_utilizadas.natureza_despesa, ''0''), 8, ''0'')::VARCHAR, 7, 2)::VARCHAR AS sub_elemento
                , COALESCE(tmp_contas_utilizadas.cod_recurso, 0) AS cod_recurso_rsp
                , COALESCE(tmp_contas_utilizadas.cod_empenho, 0) AS cod_empenho_rsp
                , COALESCE(tmp_contas_utilizadas.exercicio_empenho, ''0'')::VARCHAR AS exercicio_empenho_rsp
                , 0.00 as vl_saldo_inicial
                , '' ''::CHAR(1) AS natureza_saldo_incial
                , 0.00 as vl_saldo_debitos
                , 0.00 as vl_saldo_creditos
                , 0.00 as vl_saldo_final
                , '' ''::CHAR(1) AS natureza_saldo_final
             FROM (SELECT publico.fn_mascarareduzida(plano_conta.cod_estrutural)||''%'' AS cod_estrutural_reduzido
                        , plano_conta.cod_estrutural AS cod_estrutural_contabil
                        , plano_conta.atributo_tcemg
                     FROM contabilidade.plano_conta
                    WHERE plano_conta.escrituracao_pcasp = ''S''
                      AND plano_conta.atributo_tcemg = 14
                      AND plano_conta.exercicio = '''||stExercicio||'''
                  ) AS plano_conta
        LEFT JOIN tmp_contas_utilizadas
               ON tmp_contas_utilizadas.cod_estrutural ILIKE plano_conta.cod_estrutural_reduzido
        LEFT JOIN administracao.configuracao_entidade
               ON configuracao_entidade.exercicio = '''||stExercicio||'''
              AND configuracao_entidade.cod_entidade = tmp_contas_utilizadas.cod_entidade
              AND configuracao_entidade.cod_modulo = 55
              AND configuracao_entidade.parametro = ''tcemg_codigo_orgao_entidade_sicom''
         GROUP BY conta_contabil
                , cod_orgao
                , num_orgao_rsp
                , num_unidade_rsp
                , cod_funcao_rsp
                , cod_subfuncao_rsp
                , num_programa_rsp
                , num_acao_rsp
                , natureza_despesa_reduzida
                , sub_elemento
                , cod_recurso_rsp
                , cod_empenho_rsp
                , exercicio_empenho_rsp
          ORDER BY conta_contabil ASC';
 
    FOR reRegistro IN EXECUTE stSql
    LOOP
        arRetorno := tcemg.fn_balancete_contabil_totaliza_restos_pagar( publico.fn_mascarareduzida(reRegistro.conta_contabil) , reRegistro.num_orgao_rsp, reRegistro.num_unidade_rsp, reRegistro.cod_funcao_rsp, reRegistro.cod_subfuncao_rsp, reRegistro.num_programa_rsp, reRegistro.num_acao_rsp, reRegistro.cod_recurso_rsp, reRegistro.natureza_despesa_reduzida||reRegistro.sub_elemento, reRegistro.cod_empenho_rsp, reRegistro.exercicio_empenho_rsp);
        reRegistro.vl_saldo_inicial  := arRetorno[1];
        reRegistro.vl_saldo_debitos  := arRetorno[2];
        reRegistro.vl_saldo_creditos := arRetorno[3];
        reRegistro.vl_saldo_final    := arRetorno[4];

        IF arRetorno[1] > 0.00 THEN
            reRegistro.natureza_saldo_incial := 'D';
        ELSIF arRetorno[1] < 0.00 THEN
            reRegistro.natureza_saldo_incial := 'C';
        ELSE
            reRegistro.natureza_saldo_incial := reRegistro.natureza_saldo_incial;
        END IF;

        IF arRetorno[4] > 0.00 THEN
            reRegistro.natureza_saldo_final := 'D';
        ELSIF arRetorno[4] < 0.00 THEN
            reRegistro.natureza_saldo_final := 'C';
        ELSE
            reRegistro.natureza_saldo_final := reRegistro.natureza_saldo_final;
        END IF;
        
        IF  ( reRegistro.vl_saldo_inicial  = 0.00 ) AND
            ( reRegistro.vl_saldo_debitos  = 0.00 ) AND
            ( reRegistro.vl_saldo_creditos = 0.00 ) AND
            ( reRegistro.vl_saldo_final    = 0.00 )
        THEN
        
        ELSE
            RETURN NEXT reRegistro;
        END IF;
    END LOOP;

    DELETE FROM tcemg.balancete_contabil_14_restos_pagar;
    
    DROP INDEX unq_totaliza;
    DROP INDEX unq_totaliza_debito;
    DROP INDEX unq_totaliza_credito;
    DROP INDEX unq_debito;
    DROP INDEX unq_credito;

    DROP TABLE tmp_totaliza;
    DROP TABLE tmp_debito;
    DROP TABLE tmp_credito;
    DROP TABLE tmp_totaliza_debito;
    DROP TABLE tmp_totaliza_credito;
    DROP TABLE tmp_contas_utilizadas;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
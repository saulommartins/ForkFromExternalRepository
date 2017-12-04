<?php
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
?>
<?php
/**
    * Extensão da Classe de mapeamento
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOExtraOrcamentarias.class.php 66540 2016-09-14 21:09:14Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCMGOExtraOrcamentarias extends TOrcamentoContaReceita
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::TOrcamentoContaReceita();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSQL = "
            SELECT *
              FROM (
                     ---- select para as receitas
                     select 10 as tipo_registro
                          , 0 as categoria
                          , 5 as orgao
                          , '01' as num_unidade
                          , balancete_extmmaa.tipo_lancamento
                          , balancete_extmmaa.sub_tipo_lancamento
                          , remove_acentos(plano_conta.nom_conta) AS nom_conta
                          , ABS(SUM(valor_lancamento.vl_lancamento)) as vl_lancamento
                         , CASE WHEN (balancete_extmmaa.tipo_lancamento = 1)
                                THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                          THEN '001'
                                          WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                          THEN '002'
                                          WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                          THEN '003'
                                          WHEN balancete_extmmaa.sub_tipo_lancamento = 4
                                          THEN '004'
                                          ELSE '000'
                                     END
                                WHEN (balancete_extmmaa.tipo_lancamento = 4)
                                THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                          THEN '001'
                                          WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                          THEN '002'
                                          WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                          THEN '003'
                                          ELSE '000'
                                     END
                                ELSE '000'
                       END AS desdobra_subtipo
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
                       , '' AS branco
                       , arquivo_ext.sequencial
                       , plano_analitica.cod_plano as nro_extra_orcamentaria
                       from tcmgo.balancete_extmmaa
                       join contabilidade.plano_analitica
                         on ( balancete_extmmaa.exercicio = plano_analitica.exercicio
                        and   balancete_extmmaa.cod_plano = plano_analitica.cod_plano )
                       join contabilidade.plano_conta
                         on ( plano_analitica.exercicio = plano_conta.exercicio
                        and   plano_analitica.cod_conta = plano_conta.cod_conta )
                       join contabilidade.conta_credito
                         on ( plano_analitica.exercicio = conta_credito.exercicio
                        and   plano_analitica.cod_plano = conta_credito.cod_plano )
                       join contabilidade.valor_lancamento
                         on ( conta_credito.exercicio    = valor_lancamento.exercicio
                        and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                        and   conta_credito.tipo         = valor_lancamento.tipo
                        and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                        and   conta_credito.sequencia    = valor_lancamento.sequencia
                        and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                        and   conta_credito.tipo <> 'I')
                       join contabilidade.lote
                         on ( valor_lancamento.exercicio    = lote.exercicio
                        and   valor_lancamento.cod_entidade = lote.cod_entidade
                        and   valor_lancamento.tipo         = lote.tipo
                        and   valor_lancamento.cod_lote     = lote.cod_lote )

                 INNER JOIN tesouraria.transferencia
                         ON transferencia.cod_lote     = lote.cod_lote
                        AND transferencia.exercicio    = lote.exercicio
                        AND transferencia.tipo         = lote.tipo
                        AND transferencia.cod_entidade = lote.cod_entidade
                  LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao 
                         ON transferencia_ordem_pagamento_retencao.tipo         = transferencia.tipo
                        AND transferencia_ordem_pagamento_retencao.exercicio    = transferencia.exercicio
                        AND transferencia_ordem_pagamento_retencao.cod_entidade = transferencia.cod_entidade
                        AND transferencia_ordem_pagamento_retencao.cod_lote     = transferencia.cod_lote
                  LEFT JOIN tesouraria.transferencia_estornada
                         ON transferencia_estornada.cod_entidade = transferencia_ordem_pagamento_retencao.cod_entidade
                        AND transferencia_estornada.tipo         = transferencia_ordem_pagamento_retencao.tipo
                        AND transferencia_estornada.exercicio    = transferencia_ordem_pagamento_retencao.exercicio
                        AND transferencia_estornada.cod_lote     = transferencia_ordem_pagamento_retencao.cod_lote

                  LEFT JOIN tcmgo.arquivo_ext
                         ON arquivo_ext.cod_plano = plano_analitica.cod_plano
                        AND arquivo_ext.exercicio = plano_analitica.exercicio
                        AND arquivo_ext.mes = ".$this->getDado('mes')."
                      where plano_analitica.exercicio = '".$this->getDado( 'exercicio' ). "'
                        and valor_lancamento.cod_entidade in ( " .$this->getDado ( 'stEntidades' ). ")
                        and lote.dt_lote  >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                        and lote.dt_lote  <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                        AND transferencia_estornada.cod_lote IS NULL
                   GROUP BY  balancete_extmmaa.tipo_lancamento,balancete_extmmaa.sub_tipo_lancamento
                          , plano_conta.nom_conta
                          , plano_conta.cod_estrutural
                          , arquivo_ext.sequencial
                          , plano_analitica.cod_plano

                     union

                     ---- select para as despesas
                     select 10 as tipo_registro
                          , 1 as categoria
                          , 5 as orgao
                          , '01' as num_unidade
                          , balancete_extmmaa.tipo_lancamento
                          , balancete_extmmaa.sub_tipo_lancamento
                          , remove_acentos(plano_conta.nom_conta) AS nom_conta
                          , ABS(SUM(valor_lancamento.vl_lancamento)) as vl_lancamento
                          , CASE WHEN (balancete_extmmaa.tipo_lancamento = 1)
                                 THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                           THEN '001'
                                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                           THEN '002'
                                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                           THEN '003'
                                           WHEN balancete_extmmaa.sub_tipo_lancamento = 4
                                           THEN '004'
                                           ELSE '000'
                                      END
                                 WHEN (balancete_extmmaa.tipo_lancamento = 4)
                                 THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                           THEN '001'
                                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                           THEN '002'
                                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                           THEN '003'
                                           ELSE '000'
                                      END
                                 ELSE '000'
                        END AS desdobra_subtipo
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
                       , '' AS branco
                       , arquivo_ext.sequencial
                       , plano_analitica.cod_plano as nro_extra_orcamentaria
                       from tcmgo.balancete_extmmaa
                       join contabilidade.plano_analitica
                         on ( balancete_extmmaa.exercicio = plano_analitica.exercicio
                        and   balancete_extmmaa.cod_plano = plano_analitica.cod_plano )
                       join contabilidade.plano_conta
                         on ( plano_analitica.exercicio = plano_conta.exercicio
                        and   plano_analitica.cod_conta = plano_conta.cod_conta )
                       join contabilidade.conta_debito
                         on ( plano_analitica.exercicio = conta_debito.exercicio
                        and   plano_analitica.cod_plano = conta_debito.cod_plano )
                       join contabilidade.valor_lancamento
                         on ( conta_debito.exercicio    = valor_lancamento.exercicio
                        and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                        and   conta_debito.tipo         = valor_lancamento.tipo
                        and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                        and   conta_debito.sequencia    = valor_lancamento.sequencia
                        and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                        and   conta_debito.tipo <> 'I')
                       join contabilidade.lote
                         on ( valor_lancamento.exercicio    = lote.exercicio
                        and   valor_lancamento.cod_entidade = lote.cod_entidade
                        and   valor_lancamento.tipo         = lote.tipo
                        and   valor_lancamento.cod_lote     = lote.cod_lote )

                 INNER JOIN tesouraria.transferencia
                         ON transferencia.cod_lote     = lote.cod_lote
                        AND transferencia.exercicio    = lote.exercicio
                        AND transferencia.tipo         = lote.tipo
                        AND transferencia.cod_entidade = lote.cod_entidade
                  LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao 
                         ON transferencia_ordem_pagamento_retencao.tipo         = transferencia.tipo
                        AND transferencia_ordem_pagamento_retencao.exercicio    = transferencia.exercicio
                        AND transferencia_ordem_pagamento_retencao.cod_entidade = transferencia.cod_entidade
                        AND transferencia_ordem_pagamento_retencao.cod_lote     = transferencia.cod_lote
                  LEFT JOIN tesouraria.transferencia_estornada
                         ON transferencia_estornada.cod_entidade = transferencia_ordem_pagamento_retencao.cod_entidade
                        AND transferencia_estornada.tipo         = transferencia_ordem_pagamento_retencao.tipo
                        AND transferencia_estornada.exercicio    = transferencia_ordem_pagamento_retencao.exercicio
                        AND transferencia_estornada.cod_lote     = transferencia_ordem_pagamento_retencao.cod_lote

                  LEFT JOIN tcmgo.arquivo_ext
                         ON arquivo_ext.cod_plano = plano_analitica.cod_plano
                        AND arquivo_ext.exercicio = plano_analitica.exercicio
                        AND arquivo_ext.mes = ".$this->getDado('mes')."
                      where plano_analitica.exercicio = '".$this->getDado( 'exercicio' ). "'
                        and valor_lancamento.cod_entidade in ( " .$this->getDado ( 'stEntidades' ). ")
                        and lote.dt_lote  >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                        and lote.dt_lote  <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                        AND transferencia_estornada.cod_lote IS NULL
                   GROUP BY balancete_extmmaa.tipo_lancamento,balancete_extmmaa.sub_tipo_lancamento
                          , plano_conta.nom_conta
                          , plano_conta.cod_estrutural
                          , arquivo_ext.sequencial
                          , plano_analitica.cod_plano
                ) AS registros
         ORDER BY tipo_registro
                , sequencial
                , orgao
                , 1
                , tipo_lancamento
                , sub_tipo_lancamento
                , desdobra_subtipo
        ";

        return $stSQL;
    }

    public function recuperaTotalConta($stFiltro = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotalConta($stFiltro) ;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $rsRecordSet->getCampo( 'total' );
    }

    public function montaRecuperaTotalConta($stFiltro)
    {
        $stSQL = "
                    select
                         --total de credito
                    ROUND(coalesce (
                       ( select sum ( ROUND(valor_lancamento.vl_lancamento,2) ) as vl_total
                           from contabilidade.plano_conta
                           join contabilidade.plano_analitica
                             on ( plano_conta.exercicio = plano_analitica.exercicio
                            and   plano_conta.cod_conta = plano_analitica.cod_conta )
                     INNER JOIN contabilidade.plano_banco
                             ON plano_banco.cod_plano = plano_analitica.cod_plano
                            AND plano_banco.exercicio = plano_analitica.exercicio
                           join contabilidade.conta_credito
                             on ( plano_analitica.exercicio = conta_credito.exercicio
                            and   plano_analitica.cod_plano = conta_credito.cod_plano )
                           join contabilidade.valor_lancamento
                             on ( conta_credito.exercicio    = valor_lancamento.exercicio
                            and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                            and   conta_credito.tipo         = valor_lancamento.tipo
                            and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                            and   conta_credito.sequencia    = valor_lancamento.sequencia
                            and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                           join contabilidade.lote
                             on ( valor_lancamento.exercicio   = lote.exercicio
                            and   valor_lancamento.cod_entidade= lote.cod_entidade
                            and   valor_lancamento.tipo        = lote.tipo
                            and   valor_lancamento.cod_lote    = lote.cod_lote )
                           $stFiltro
                        ), 0 )
                       +
                        --total de debitos
                    coalesce (
                      ( select sum ( ROUND(valor_lancamento.vl_lancamento,2) ) as vl_total
                          from contabilidade.plano_conta
                          join contabilidade.plano_analitica
                            on ( plano_conta.exercicio = plano_analitica.exercicio
                           and   plano_conta.cod_conta = plano_analitica.cod_conta )
                    INNER JOIN contabilidade.plano_banco
                            ON plano_banco.cod_plano = plano_analitica.cod_plano
                           AND plano_banco.exercicio = plano_analitica.exercicio
                          join contabilidade.conta_debito
                            on ( plano_analitica.exercicio = conta_debito.exercicio
                           and   plano_analitica.cod_plano = conta_debito.cod_plano )
                          join contabilidade.valor_lancamento
                            on ( conta_debito.exercicio    = valor_lancamento.exercicio
                           and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                           and   conta_debito.tipo         = valor_lancamento.tipo
                           and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                           and   conta_debito.sequencia    = valor_lancamento.sequencia
                           and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                          join contabilidade.lote
                            on ( valor_lancamento.exercicio   = lote.exercicio
                           and   valor_lancamento.cod_entidade= lote.cod_entidade
                           and   valor_lancamento.tipo        = lote.tipo
                           and   valor_lancamento.cod_lote    = lote.cod_lote )
                          $stFiltro
                        ) , 0 ),2)   as total
                 ";

        return $stSQL;
    }

    public function recuperaMovimentacaoFinanceira(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMovimentacaoFinanceira",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMovimentacaoFinanceira()
    {
        $stSQL = "
    SELECT *
      FROM (
           -- RECEITA EXTA ESTORNADA
       SELECT 11                                          AS tipo_registro
            , 0                                           AS categoria
            , orgao.num_orgao                             AS orgao
            , '01'                                        AS num_unidade
            , balancete_extmmaa.tipo_lancamento
            , balancete_extmmaa.sub_tipo_lancamento
            , CASE
                  WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                      CASE
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                          ELSE                                                '000'
                       END
                  WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                      CASE
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                          ELSE                                                '000'
                      END
                  ELSE '000'
              END                                         AS desdobra_subtipo
            , banco.num_banco AS banco
            , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                          , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                            ELSE
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                            END AS conta_corrente
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
            , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
            , ABS(SUM(transferencia_estornada.valor))               AS valor
            , '' AS branco
            , plano_analitica.cod_plano as nro_extra_orcamentaria
         FROM tcmgo.orgao
            , tcmgo.balancete_extmmaa
         JOIN contabilidade.plano_analitica
           ON balancete_extmmaa.exercicio           = plano_analitica.exercicio
          AND balancete_extmmaa.cod_plano           = plano_analitica.cod_plano
         JOIN tesouraria.transferencia
           ON transferencia.cod_plano_credito       = plano_analitica.cod_plano
          AND transferencia.exercicio               = plano_analitica.exercicio
          AND transferencia.cod_tipo                = 2
         JOIN tesouraria.transferencia_estornada
           ON transferencia_estornada.cod_entidade  = transferencia.cod_entidade
          AND transferencia_estornada.tipo          = transferencia.tipo
          AND transferencia_estornada.exercicio     = transferencia.exercicio
          AND transferencia_estornada.cod_lote      = transferencia.cod_lote
    LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao 
           ON transferencia_ordem_pagamento_retencao.tipo         = transferencia.tipo
          AND transferencia_ordem_pagamento_retencao.exercicio    = transferencia.exercicio
          AND transferencia_ordem_pagamento_retencao.cod_entidade = transferencia.cod_entidade
          AND transferencia_ordem_pagamento_retencao.cod_lote     = transferencia.cod_lote
    LEFT JOIN empenho.ordem_pagamento_retencao
           ON ordem_pagamento_retencao.exercicio = transferencia_ordem_pagamento_retencao.exercicio
          AND ordem_pagamento_retencao.cod_entidade = transferencia_ordem_pagamento_retencao.cod_entidade
          AND ordem_pagamento_retencao.cod_ordem = transferencia_ordem_pagamento_retencao.cod_ordem
          AND ordem_pagamento_retencao.cod_plano = transferencia_ordem_pagamento_retencao.cod_plano
          AND ordem_pagamento_retencao.sequencial = transferencia_ordem_pagamento_retencao.sequencial
         JOIN contabilidade.plano_banco
           ON plano_banco.cod_plano                 = transferencia.cod_plano_debito
          AND plano_banco.exercicio                 = transferencia.exercicio
        JOIN contabilidade.plano_analitica pa
          ON plano_banco.exercicio       = pa.exercicio
         AND plano_banco.cod_plano       = pa.cod_plano
        JOIN contabilidade.plano_conta
          ON plano_conta.cod_conta = pa.cod_conta
         AND plano_conta.exercicio = pa.exercicio
         JOIN monetario.conta_corrente
           ON conta_corrente.cod_banco              = plano_banco.cod_banco
          AND conta_corrente.cod_agencia            = plano_banco.cod_agencia
          AND conta_corrente.cod_conta_corrente     = plano_banco.cod_conta_corrente
         JOIN monetario.agencia
           ON agencia.cod_agencia                   = conta_corrente.cod_agencia
          AND agencia.cod_banco                     = conta_corrente.cod_banco
         JOIN monetario.banco
           ON banco.cod_banco                       = agencia.cod_banco
        WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
          AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
          AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
          AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
          AND ordem_pagamento_retencao.cod_receita IS NOT NULL
     GROUP BY orgao.num_orgao
            , 1
            , balancete_extmmaa.tipo_lancamento
            , balancete_extmmaa.sub_tipo_lancamento
            , banco.num_banco
            , agencia.num_agencia
            , tipo_conta
            , conta_corrente.num_conta_corrente
            , plano_conta.cod_estrutural
            , plano_analitica.cod_plano

        UNION

            -- RECEITA EXTA
       SELECT 11                                          AS tipo_registro
            , 0                                           AS categoria
            , orgao.num_orgao                             AS orgao
            , '01'                                        AS num_unidade
            , balancete_extmmaa.tipo_lancamento
            , balancete_extmmaa.sub_tipo_lancamento
            , CASE
                  WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                      CASE
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                          ELSE                                                '000'
                       END
                  WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                      CASE
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                          WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                          ELSE                                                '000'
                      END
                  ELSE '000'
              END                                         AS desdobra_subtipo
            , banco.num_banco AS banco
            , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                          , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                            ELSE
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                            END AS conta_corrente
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
            , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
            , ABS(SUM(transferencia.valor))               AS valor
            , '' AS branco
            , plano_analitica.cod_plano as nro_extra_orcamentaria
         FROM tcmgo.orgao
            , tcmgo.balancete_extmmaa
         JOIN contabilidade.plano_analitica
           ON balancete_extmmaa.exercicio           = plano_analitica.exercicio
          AND balancete_extmmaa.cod_plano           = plano_analitica.cod_plano
         JOIN tesouraria.transferencia
           ON transferencia.cod_plano_credito       = plano_analitica.cod_plano
          AND transferencia.exercicio               = plano_analitica.exercicio
          AND transferencia.cod_tipo                = 2
    LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao 
           ON transferencia_ordem_pagamento_retencao.tipo         = transferencia.tipo
          AND transferencia_ordem_pagamento_retencao.exercicio    = transferencia.exercicio
          AND transferencia_ordem_pagamento_retencao.cod_entidade = transferencia.cod_entidade
          AND transferencia_ordem_pagamento_retencao.cod_lote     = transferencia.cod_lote
    LEFT JOIN empenho.ordem_pagamento_retencao
           ON ordem_pagamento_retencao.exercicio = transferencia_ordem_pagamento_retencao.exercicio
          AND ordem_pagamento_retencao.cod_entidade = transferencia_ordem_pagamento_retencao.cod_entidade
          AND ordem_pagamento_retencao.cod_ordem = transferencia_ordem_pagamento_retencao.cod_ordem
          AND ordem_pagamento_retencao.cod_plano = transferencia_ordem_pagamento_retencao.cod_plano
          AND ordem_pagamento_retencao.sequencial = transferencia_ordem_pagamento_retencao.sequencial
         JOIN contabilidade.plano_banco
           ON plano_banco.cod_plano                 = transferencia.cod_plano_debito
          AND plano_banco.exercicio                 = transferencia.exercicio
        JOIN contabilidade.plano_analitica pa
          ON plano_banco.exercicio       = pa.exercicio
         AND plano_banco.cod_plano       = pa.cod_plano
        JOIN contabilidade.plano_conta
          ON plano_conta.cod_conta = pa.cod_conta
         AND plano_conta.exercicio = pa.exercicio
         JOIN monetario.conta_corrente
           ON conta_corrente.cod_banco              = plano_banco.cod_banco
          AND conta_corrente.cod_agencia            = plano_banco.cod_agencia
          AND conta_corrente.cod_conta_corrente     = plano_banco.cod_conta_corrente
         JOIN monetario.agencia
           ON agencia.cod_agencia                   = conta_corrente.cod_agencia
          AND agencia.cod_banco                     = conta_corrente.cod_banco
         JOIN monetario.banco
           ON banco.cod_banco                       = agencia.cod_banco
        WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
          AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
          AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
          AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
          AND ordem_pagamento_retencao.cod_receita IS NOT NULL
     GROUP BY orgao.num_orgao
            , 1
            , balancete_extmmaa.tipo_lancamento
            , balancete_extmmaa.sub_tipo_lancamento
            , banco.num_banco
            , agencia.num_agencia
            , tipo_conta
            , conta_corrente.num_conta_corrente
            , plano_conta.cod_estrutural
            , plano_analitica.cod_plano

       UNION

          -- DESPESA EXTRA
      SELECT 11                                          AS tipo_registro
           , 1                                           AS categoria
           , orgao.num_orgao                             AS orgao
           , '01'                                        AS num_unidade
           , balancete_extmmaa.tipo_lancamento
           , balancete_extmmaa.sub_tipo_lancamento
           , CASE
                 WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                     CASE
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                         ELSE                                                '000'
                      END
                 WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                     CASE
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                         ELSE                                                '000'
                     END
                 ELSE '000'
             END                                         AS desdobra_subtipo
            , banco.num_banco AS banco
            , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                          , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                            ELSE
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                            END AS conta_corrente
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
            , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
            , ABS(SUM(transferencia.valor))               AS valor
            , '' AS branco
            , plano_analitica.cod_plano as nro_extra_orcamentaria
        FROM tcmgo.orgao
           , tcmgo.balancete_extmmaa
        JOIN contabilidade.plano_analitica
          ON balancete_extmmaa.exercicio       = plano_analitica.exercicio
         AND balancete_extmmaa.cod_plano       = plano_analitica.cod_plano
        JOIN tesouraria.transferencia
          ON transferencia.cod_plano_debito    = plano_analitica.cod_plano
         AND transferencia.exercicio           = plano_analitica.exercicio
         AND transferencia.cod_tipo            = 1
        JOIN contabilidade.plano_banco
          ON plano_banco.cod_plano             = transferencia.cod_plano_credito
         AND plano_banco.exercicio             = transferencia.exercicio
        JOIN contabilidade.plano_analitica pa
          ON plano_banco.exercicio       = pa.exercicio
         AND plano_banco.cod_plano       = pa.cod_plano
        JOIN contabilidade.plano_conta
          ON plano_conta.cod_conta = pa.cod_conta
         AND plano_conta.exercicio = pa.exercicio
        JOIN monetario.conta_corrente
          ON conta_corrente.cod_banco          = plano_banco.cod_banco
         AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
         AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
        JOIN monetario.agencia
          ON agencia.cod_agencia               = conta_corrente.cod_agencia
         AND agencia.cod_banco                 = conta_corrente.cod_banco
        JOIN monetario.banco
          ON banco.cod_banco                   = agencia.cod_banco
       WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
         AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
         AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
          AND transferencia.dt_autenticacao  <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
    GROUP BY orgao.num_orgao
           , 1
           , balancete_extmmaa.tipo_lancamento
           , balancete_extmmaa.sub_tipo_lancamento
           , banco.num_banco
           , agencia.num_agencia
           , tipo_conta
           , conta_corrente.num_conta_corrente
           , plano_conta.cod_estrutural
           , plano_analitica.cod_plano

      UNION

          -- DESPESA EXTRA ESTORNADA
      SELECT 11                                          AS tipo_registro
           , 1                                           AS categoria
           , orgao.num_orgao                             AS orgao
           , '01'                                        AS num_unidade
           , balancete_extmmaa.tipo_lancamento
           , balancete_extmmaa.sub_tipo_lancamento
           , CASE
                 WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                     CASE
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                         ELSE                                                '000'
                      END
                 WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                     CASE
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                         WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                         ELSE                                                '000'
                     END
                 ELSE '000'
             END                                         AS desdobra_subtipo
            , banco.num_banco AS banco
            , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                          , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                            ELSE
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                            END AS conta_corrente
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
            , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
            , ABS(SUM(transferencia_estornada.valor))               AS valor
            , '' AS branco
            , plano_analitica.cod_plano as nro_extra_orcamentaria
        FROM tcmgo.orgao
           , tcmgo.balancete_extmmaa
        JOIN contabilidade.plano_analitica
          ON balancete_extmmaa.exercicio       = plano_analitica.exercicio
         AND balancete_extmmaa.cod_plano       = plano_analitica.cod_plano
        JOIN tesouraria.transferencia
          ON transferencia.cod_plano_debito    = plano_analitica.cod_plano
         AND transferencia.exercicio           = plano_analitica.exercicio
         AND transferencia.cod_tipo            = 1
        JOIN tesouraria.transferencia_estornada
          ON transferencia_estornada.cod_entidade  = transferencia.cod_entidade
         AND transferencia_estornada.tipo          = transferencia.tipo
         AND transferencia_estornada.exercicio     = transferencia.exercicio
         AND transferencia_estornada.cod_lote      = transferencia.cod_lote
        JOIN contabilidade.plano_banco
          ON plano_banco.cod_plano             = transferencia.cod_plano_credito
         AND plano_banco.exercicio             = transferencia.exercicio
        JOIN contabilidade.plano_analitica pa
          ON plano_banco.exercicio       = pa.exercicio
         AND plano_banco.cod_plano       = pa.cod_plano
        JOIN contabilidade.plano_conta
          ON plano_conta.cod_conta = pa.cod_conta
         AND plano_conta.exercicio = pa.exercicio
        JOIN monetario.conta_corrente
          ON conta_corrente.cod_banco          = plano_banco.cod_banco
         AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
         AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
        JOIN monetario.agencia
          ON agencia.cod_agencia               = conta_corrente.cod_agencia
         AND agencia.cod_banco                 = conta_corrente.cod_banco
        JOIN monetario.banco
          ON banco.cod_banco                   = agencia.cod_banco
       WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
         AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
         AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
          AND transferencia.dt_autenticacao  <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
    GROUP BY orgao.num_orgao
           , 1
           , balancete_extmmaa.tipo_lancamento
           , balancete_extmmaa.sub_tipo_lancamento
           , banco.num_banco
           , agencia.num_agencia
           , tipo_conta
           , conta_corrente.num_conta_corrente
           , plano_conta.cod_estrutural
           , plano_analitica.cod_plano
          ) AS registros
   ORDER BY tipo_registro
          , orgao
          , 1
          , tipo_lancamento
          , sub_tipo_lancamento
          , desdobra_subtipo

    ";

        return $stSQL;
    }

    public function recuperaDetalhamentoFonteRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamentoFonteRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamentoFonteRecurso()
    {
        $stSQL = "
    SELECT *
      FROM (
            -- RECEITA EXTA ESTORNADA
        SELECT 12                                         AS tipo_registro
             , 0                                          AS categoria
             , orgao.num_orgao                            AS orgao
             , '01'                                       AS num_unidade
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , CASE
                   WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                           ELSE                                                '000'
                        END
                   WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           ELSE                                                '000'
                       END
                   ELSE '000'
               END                                         AS desdobra_subtipo
             , banco.num_banco AS banco
             , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                          , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                            ELSE
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                            END AS conta_corrente
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
             , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
             , plano_recurso.cod_recurso
             , recurso.cod_fonte
             , ABS(SUM(transferencia_estornada.valor))     AS valor
            , '' AS branco
            , pa1.cod_plano as nro_extra_orcamentaria
          FROM tcmgo.orgao
             , tcmgo.balancete_extmmaa
          JOIN contabilidade.plano_analitica pa1
            ON balancete_extmmaa.exercicio          = pa1.exercicio
           AND balancete_extmmaa.cod_plano          = pa1.cod_plano
          JOIN tesouraria.transferencia
            ON transferencia.cod_plano_debito       = pa1.cod_plano
           AND transferencia.exercicio              = pa1.exercicio
           AND transferencia.cod_tipo               = 2
--          JOIN contabilidade.plano_conta
--            ON plano_conta.cod_conta = pa1.cod_conta
--           AND plano_conta.exercicio = pa1.exercicio
          JOIN tesouraria.transferencia_estornada
            ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
           AND transferencia_estornada.tipo         = transferencia.tipo
           AND transferencia_estornada.exercicio    = transferencia.exercicio
           AND transferencia_estornada.cod_lote     = transferencia.cod_lote
          JOIN contabilidade.plano_recurso
            ON plano_recurso.cod_plano              = transferencia.cod_plano_credito
           AND plano_recurso.exercicio              = transferencia.exercicio
          JOIN orcamento.recurso
            ON recurso.cod_recurso = plano_recurso.cod_recurso
           AND recurso.exercicio  = plano_recurso.exercicio
          JOIN contabilidade.plano_banco
            ON plano_banco.cod_plano                = transferencia.cod_plano_credito
           AND plano_banco.exercicio                = transferencia.exercicio
          JOIN contabilidade.plano_analitica pa2
            ON plano_banco.exercicio          = pa2.exercicio
           AND plano_banco.cod_plano          = pa2.cod_plano
          JOIN contabilidade.plano_conta
            ON plano_conta.cod_conta = pa2.cod_conta
           AND plano_conta.exercicio = pa2.exercicio
          JOIN monetario.conta_corrente
            ON conta_corrente.cod_banco             = plano_banco.cod_banco
           AND conta_corrente.cod_agencia           = plano_banco.cod_agencia
           AND conta_corrente.cod_conta_corrente    = plano_banco.cod_conta_corrente
          JOIN monetario.agencia
            ON agencia.cod_agencia                  = conta_corrente.cod_agencia
           AND agencia.cod_banco                    = conta_corrente.cod_banco
          JOIN monetario.banco
            ON banco.cod_banco                      = agencia.cod_banco
         WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
           AND pa1.exercicio       = '".$this->getDado( 'exercicio' ). "'
           AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
           AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
      GROUP BY orgao.num_orgao
             , 1
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , banco.num_banco
             , agencia.num_agencia
             , tipo_conta
             , conta_corrente.num_conta_corrente
             , plano_recurso.cod_recurso
             , recurso.cod_fonte
             , plano_conta.cod_estrutural
             , pa1.cod_plano

        UNION

        -- RECEITA EXTA
        SELECT 12                                         AS tipo_registro
             , 0                                          AS categoria
             , orgao.num_orgao                            AS orgao
             , '01'                                       AS num_unidade
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , CASE
                   WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                           ELSE                                                '000'
                        END
                   WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           ELSE                                                '000'
                       END
                   ELSE '000'
               END                                         AS desdobra_subtipo
             , banco.num_banco AS banco
             , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                          , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                            ELSE
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                            END AS conta_corrente
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
             , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
             , plano_recurso.cod_recurso
             , recurso.cod_fonte
             , ABS(SUM(transferencia.valor))     AS valor
            , '' AS branco
            , pa1.cod_plano as nro_extra_orcamentaria
          FROM tcmgo.orgao
             , tcmgo.balancete_extmmaa
          JOIN contabilidade.plano_analitica pa1
            ON balancete_extmmaa.exercicio          = pa1.exercicio
           AND balancete_extmmaa.cod_plano          = pa1.cod_plano
          JOIN tesouraria.transferencia
            ON transferencia.cod_plano_credito      = pa1.cod_plano
           AND transferencia.exercicio              = pa1.exercicio
           AND transferencia.cod_tipo               = 2
--          JOIN contabilidade.plano_conta
--            ON plano_conta.cod_conta = pa1.cod_conta
--           AND plano_conta.exercicio = pa1.exercicio
          JOIN contabilidade.plano_recurso
            ON plano_recurso.cod_plano              = transferencia.cod_plano_debito
           AND plano_recurso.exercicio              = transferencia.exercicio
          JOIN orcamento.recurso
            ON recurso.cod_recurso = plano_recurso.cod_recurso
           AND recurso.exercicio  = plano_recurso.exercicio
          JOIN contabilidade.plano_banco
            ON plano_banco.cod_plano                = transferencia.cod_plano_debito
           AND plano_banco.exercicio                = transferencia.exercicio
          JOIN contabilidade.plano_analitica pa2
            ON plano_banco.exercicio          = pa2.exercicio
           AND plano_banco.cod_plano          = pa2.cod_plano
          JOIN contabilidade.plano_conta
            ON plano_conta.cod_conta = pa2.cod_conta
           AND plano_conta.exercicio = pa2.exercicio
          JOIN monetario.conta_corrente
            ON conta_corrente.cod_banco             = plano_banco.cod_banco
           AND conta_corrente.cod_agencia           = plano_banco.cod_agencia
           AND conta_corrente.cod_conta_corrente    = plano_banco.cod_conta_corrente
          JOIN monetario.agencia
            ON agencia.cod_agencia                  = conta_corrente.cod_agencia
           AND agencia.cod_banco                    = conta_corrente.cod_banco
          JOIN monetario.banco
            ON banco.cod_banco                      = agencia.cod_banco
         WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
           AND pa1.exercicio       = '".$this->getDado( 'exercicio' ). "'
           AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
           AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
      GROUP BY orgao.num_orgao
             , 1
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , banco.num_banco
             , agencia.num_agencia
             , tipo_conta
             , conta_corrente.num_conta_corrente
             , plano_recurso.cod_recurso
             , recurso.cod_fonte
             , plano_conta.cod_estrutural
             , pa1.cod_plano

         UNION

           -- DESPESA EXTRA
        SELECT 12                                          AS tipo_registro
             , 1                                           AS categoria
             , orgao.num_orgao                             AS orgao
             , '01'                                        AS num_unidade
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , CASE
                   WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                           ELSE                                                '000'
                        END
                   WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           ELSE                                                '000'
                       END
                   ELSE '000'
               END                                         AS desdobra_subtipo
            , banco.num_banco AS banco
            , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                          , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                            ELSE
                                      LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                            END AS conta_corrente
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
            , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
            , plano_recurso.cod_recurso
            , recurso.cod_fonte
            , ABS(SUM(transferencia.valor))               AS valor
            , '' AS branco
            , pa1.cod_plano as nro_extra_orcamentaria

          FROM tcmgo.orgao
             , tcmgo.balancete_extmmaa
          JOIN contabilidade.plano_analitica pa1
            ON balancete_extmmaa.exercicio       = pa1.exercicio
           AND balancete_extmmaa.cod_plano       = pa1.cod_plano
          JOIN tesouraria.transferencia
            ON transferencia.cod_plano_debito   = pa1.cod_plano
           AND transferencia.exercicio           = pa1.exercicio
           AND transferencia.cod_tipo            = 1
--          JOIN contabilidade.plano_conta
--            ON plano_conta.cod_conta = plano_analitica.cod_conta
--           AND plano_conta.exercicio = plano_analitica.exercicio
          JOIN contabilidade.plano_recurso
            ON plano_recurso.cod_plano           = transferencia.cod_plano_credito
           AND plano_recurso.exercicio           = transferencia.exercicio
          JOIN orcamento.recurso
            ON recurso.cod_recurso = plano_recurso.cod_recurso
            AND recurso.exercicio  = plano_recurso.exercicio
          JOIN contabilidade.plano_banco
            ON plano_banco.cod_plano             = transferencia.cod_plano_credito
           AND plano_banco.exercicio             = transferencia.exercicio
          JOIN contabilidade.plano_analitica pa2
            ON plano_banco.exercicio          = pa2.exercicio
           AND plano_banco.cod_plano          = pa2.cod_plano
          JOIN contabilidade.plano_conta
            ON plano_conta.cod_conta = pa2.cod_conta
           AND plano_conta.exercicio = pa2.exercicio
          JOIN monetario.conta_corrente
            ON conta_corrente.cod_banco          = plano_banco.cod_banco
           AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
           AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
          JOIN monetario.agencia
            ON agencia.cod_agencia               = conta_corrente.cod_agencia
           AND agencia.cod_banco                 = conta_corrente.cod_banco
          JOIN monetario.banco
            ON banco.cod_banco                   = agencia.cod_banco
         WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
           AND pa1.exercicio       = '".$this->getDado( 'exercicio' ). "'
           AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
           AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
      GROUP BY orgao.num_orgao
             , 1
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , banco.num_banco
             , agencia.num_agencia
             , tipo_conta
             , conta_corrente.num_conta_corrente
             , plano_recurso.cod_recurso
             , recurso.cod_fonte
             , plano_conta.cod_estrutural
             , pa1.cod_plano

        UNION

           -- DESPESA EXTRA ESTORNADA
        SELECT 12                                          AS tipo_registro
             , 1                                           AS categoria
             , orgao.num_orgao                             AS orgao
             , '01'                                        AS num_unidade
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , CASE
                   WHEN (balancete_extmmaa.tipo_lancamento = 1) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN '004'
                           ELSE                                                '000'
                        END
                   WHEN (balancete_extmmaa.tipo_lancamento = 4) THEN
                       CASE
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 1 THEN '001'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN '002'
                           WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN '003'
                           ELSE                                                '000'
                       END
                   ELSE '000'
               END                                         AS desdobra_subtipo
            , banco.num_banco AS banco
            , ltrim(replace(num_agencia,'-',''),'0') AS agencia
            , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                  LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                ELSE
                  ltrim(split_part(num_conta_corrente,'-',1),'0')
                END AS conta_corrente
            , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta
            , plano_recurso.cod_recurso
            , recurso.cod_fonte
            , ABS(SUM(transferencia_estornada.valor))               AS valor
            , '' AS branco
            , pa1.cod_plano as nro_extra_orcamentaria

          FROM tcmgo.orgao
             , tcmgo.balancete_extmmaa
          JOIN contabilidade.plano_analitica pa1
            ON balancete_extmmaa.exercicio       = pa1.exercicio
           AND balancete_extmmaa.cod_plano       = pa1.cod_plano
          JOIN tesouraria.transferencia
            ON transferencia.cod_plano_credito    = pa1.cod_plano
           AND transferencia.exercicio           = pa1.exercicio
           AND transferencia.cod_tipo            = 1
--          JOIN contabilidade.plano_conta
--            ON plano_conta.cod_conta = plano_analitica.cod_conta
--           AND plano_conta.exercicio = plano_analitica.exercicio
          JOIN tesouraria.transferencia_estornada
            ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
           AND transferencia_estornada.tipo         = transferencia.tipo
           AND transferencia_estornada.exercicio    = transferencia.exercicio
           AND transferencia_estornada.cod_lote     = transferencia.cod_lote
          JOIN contabilidade.plano_recurso
            ON plano_recurso.cod_plano           = transferencia.cod_plano_debito
           AND plano_recurso.exercicio           = transferencia.exercicio
          JOIN orcamento.recurso
            ON recurso.cod_recurso = plano_recurso.cod_recurso
            AND recurso.exercicio  = plano_recurso.exercicio
          JOIN contabilidade.plano_banco
            ON plano_banco.cod_plano             = transferencia.cod_plano_debito
           AND plano_banco.exercicio             = transferencia.exercicio
          JOIN contabilidade.plano_analitica pa2
            ON plano_banco.exercicio          = pa2.exercicio
           AND plano_banco.cod_plano          = pa2.cod_plano
          JOIN contabilidade.plano_conta
            ON plano_conta.cod_conta = pa2.cod_conta
           AND plano_conta.exercicio = pa2.exercicio
          JOIN monetario.conta_corrente
            ON conta_corrente.cod_banco          = plano_banco.cod_banco
           AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
           AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
          JOIN monetario.agencia
            ON agencia.cod_agencia               = conta_corrente.cod_agencia
           AND agencia.cod_banco                 = conta_corrente.cod_banco
          JOIN monetario.banco
            ON banco.cod_banco                   = agencia.cod_banco
         WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
           AND pa1.exercicio       = '".$this->getDado( 'exercicio' ). "'
           AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
           AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
      GROUP BY orgao.num_orgao
             , 1
             , balancete_extmmaa.tipo_lancamento
             , balancete_extmmaa.sub_tipo_lancamento
             , banco.num_banco
             , agencia.num_agencia
             , tipo_conta
             , conta_corrente.num_conta_corrente
             , plano_recurso.cod_recurso
             , recurso.cod_fonte
             , plano_conta.cod_estrutural
             , pa1.cod_plano
        ) AS registros
 ORDER BY tipo_registro
        , orgao
        , 1
        , tipo_lancamento
        , sub_tipo_lancamento
        , desdobra_subtipo

    ";

        return $stSQL;
    }

}
?>

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
/*
    $Id: TTCMGOAnulacaoExtraOrcamentarias.class.php 66540 2016-09-14 21:09:14Z michel $
*/

include_once  CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php";

class TTCMGOAnulacaoExtraOrcamentarias extends TOrcamentoContaReceita
{
    public function __construct()
    {
        parent::TOrcamentoContaReceita();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    public function recuperaReg10(&$rsRecordSet, $stFiltro = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaReg10().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }

    public function montaRecuperaReg10()
    {
        $stSql = "
            SELECT *
              FROM (
                     ---- select para as receitas
                    SELECT 10 as tipo_registro
                         , 0 as categoria
                         , orgao.num_orgao as orgao
                         , '01' as num_unidade
                         , balancete_extmmaa.tipo_lancamento
                         , balancete_extmmaa.sub_tipo_lancamento
                         , remove_acentos(plano_conta.nom_conta) AS nom_conta
                         , ABS(TTE.valor) as vl_anulacao
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
                         , plano_analitica.cod_plano as nro_extra_orcamentaria
                         , TO_CHAR(TTE.timestamp_estornada, 'ddmmyyyy') AS dt_estorno
                         , arquivo_ext.sequencial
                     FROM tesouraria.transferencia AS TT
               INNER JOIN tesouraria.transferencia_estornada AS TTE
                       ON TTE.cod_lote=TT.cod_lote
                      AND TTE.exercicio=TT.exercicio
                      AND TTE.tipo=TT.tipo
                      AND TTE.cod_entidade=TT.cod_entidade
               INNER JOIN contabilidade.plano_analitica
                       ON plano_analitica.cod_plano = TT.cod_plano_credito
                      AND plano_analitica.exercicio = TT.exercicio
               INNER JOIN tcmgo.balancete_extmmaa
                       ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                      AND balancete_extmmaa.exercicio = plano_analitica.exercicio
               INNER JOIN contabilidade.plano_conta
                       ON plano_analitica.exercicio = plano_conta.exercicio
                      AND plano_analitica.cod_conta = plano_conta.cod_conta 
               INNER JOIN contabilidade.conta_credito
                       ON plano_analitica.exercicio = conta_credito.exercicio
                      AND plano_analitica.cod_plano = conta_credito.cod_plano 
               INNER JOIN contabilidade.valor_lancamento
                       ON conta_credito.exercicio    = valor_lancamento.exercicio
                      AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                      AND conta_credito.tipo         = valor_lancamento.tipo
                      AND conta_credito.cod_lote     = valor_lancamento.cod_lote
                      AND conta_credito.sequencia    = valor_lancamento.sequencia
                      AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                      AND conta_credito.tipo <> 'I'
               INNER JOIN contabilidade.lote
                       ON valor_lancamento.exercicio    = lote.exercicio
                      AND valor_lancamento.cod_entidade = lote.cod_entidade
                      AND valor_lancamento.tipo         = lote.tipo
                      AND valor_lancamento.cod_lote     = lote.cod_lote
               INNER JOIN tcmgo.orgao
                       ON orgao.exercicio = balancete_extmmaa.exercicio
                LEFT JOIN tcmgo.arquivo_ext
                       ON arquivo_ext.cod_plano = plano_analitica.cod_plano
                      AND arquivo_ext.exercicio = plano_analitica.exercicio
                      AND arquivo_ext.mes = ".$this->getDado('mes')."
                    WHERE plano_analitica.exercicio = '".$this->getDado( 'exercicio' ). "'
                      AND valor_lancamento.cod_entidade in ( " .$this->getDado ( 'stEntidades' ). ")
                      AND TO_DATE(TTE.timestamp_estornada::VARCHAR, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."', 'dd/mm/yyyy')
                                                                                      AND TO_DATE('".$this->getDado('dtFim')."', 'dd/mm/yyyy')
                      AND TT.cod_tipo            = 1
                 GROUP BY balancete_extmmaa.tipo_lancamento
                        , balancete_extmmaa.sub_tipo_lancamento
                        , plano_conta.nom_conta
                        , plano_conta.cod_estrutural
                        , plano_analitica.cod_plano
                        , TTE.valor
                        , dt_estorno
                        , orgao
                        , arquivo_ext.sequencial

                   UNION

                     ---- select para as despesas
                    SELECT 10 as tipo_registro
                         , 1 as categoria
                         , orgao.num_orgao as orgao
                         , '01' as num_unidade
                         , balancete_extmmaa.tipo_lancamento
                         , balancete_extmmaa.sub_tipo_lancamento
                         , remove_acentos(plano_conta.nom_conta) AS nom_conta
                         , ABS((TTE.valor)) as vl_anulacao
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
                         , plano_analitica.cod_plano as nro_extra_orcamentaria
                         , TO_CHAR(TTE.timestamp_estornada, 'ddmmyyyy') AS dt_estorno
                         , arquivo_ext.sequencial
                      FROM tesouraria.transferencia AS TT
                INNER JOIN tesouraria.transferencia_estornada AS TTE
                        ON TTE.cod_lote=TT.cod_lote
                       AND TTE.exercicio=TT.exercicio
                       AND TTE.tipo=TT.tipo
                       AND TTE.cod_entidade=TT.cod_entidade
                INNER JOIN contabilidade.plano_analitica
                        ON plano_analitica.cod_plano = TT.cod_plano_debito
                       AND plano_analitica.exercicio = TT.exercicio
                INNER JOIN tcmgo.balancete_extmmaa
                        ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                       AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                INNER JOIN contabilidade.plano_conta
                        ON plano_analitica.exercicio = plano_conta.exercicio
                       AND plano_analitica.cod_conta = plano_conta.cod_conta
                INNER JOIN contabilidade.conta_debito
                        ON plano_analitica.exercicio = conta_debito.exercicio
                       AND plano_analitica.cod_plano = conta_debito.cod_plano 
                INNER JOIN contabilidade.valor_lancamento
                        ON conta_debito.exercicio    = valor_lancamento.exercicio
                       AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                       AND conta_debito.tipo         = valor_lancamento.tipo
                       AND conta_debito.cod_lote     = valor_lancamento.cod_lote
                       AND conta_debito.sequencia    = valor_lancamento.sequencia
                       AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                       AND conta_debito.tipo <> 'I'
                INNER JOIN contabilidade.lote
                        ON valor_lancamento.exercicio    = lote.exercicio
                       AND valor_lancamento.cod_entidade = lote.cod_entidade
                       AND valor_lancamento.tipo         = lote.tipo
                       AND valor_lancamento.cod_lote     = lote.cod_lote
                INNER JOIN tcmgo.orgao
                        ON orgao.exercicio = balancete_extmmaa.exercicio
                 LEFT JOIN tcmgo.arquivo_ext
                        ON arquivo_ext.cod_plano = plano_analitica.cod_plano
                       AND arquivo_ext.exercicio = plano_analitica.exercicio
                       AND arquivo_ext.mes = ".$this->getDado('mes')."
                     WHERE plano_analitica.exercicio = '".$this->getDado( 'exercicio' ). "'
                       AND valor_lancamento.cod_entidade in ( " .$this->getDado ( 'stEntidades' ). ")
                       AND TO_DATE(TTE.timestamp_estornada::VARCHAR, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."', 'dd/mm/yyyy')
                                                                                      AND TO_DATE('".$this->getDado('dtFim')."', 'dd/mm/yyyy')
                       AND TT.cod_tipo            = 1
                  GROUP BY balancete_extmmaa.tipo_lancamento
                         , balancete_extmmaa.sub_tipo_lancamento
                         , plano_conta.nom_conta
                         , plano_conta.cod_estrutural
                         , plano_analitica.cod_plano
                         , TTE.valor
                         , dt_estorno
                         , orgao
                         , arquivo_ext.sequencial
                ) AS registros
         ORDER BY tipo_registro
                , orgao
                , tipo_lancamento
                , sub_tipo_lancamento
                , desdobra_subtipo
        ";
        
        return $stSql;
        
    }
    
    
    public function recuperaReg11(&$rsRecordSet, $stFiltro = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaReg11().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }

    public function montaRecuperaReg11()
    {
        $stSql = "
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
                      , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                             ELSE
                                LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                             END AS conta_corrente
                       , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN '03'
                                      WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN '02'
                                      ELSE '01'
                              END as tipo_conta
                      , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                      , ABS(SUM(transferencia_estornada.valor))               AS valor
                      , '' AS branco
                      , plano_analitica.cod_plano as nro_extra_orcamentaria
                      , TO_CHAR(transferencia_estornada.timestamp_estornada, 'ddmmyyyy') AS dt_estorno
                  FROM tcmgo.orgao
            INNER JOIN tcmgo.balancete_extmmaa
                    ON orgao.exercicio = balancete_extmmaa.exercicio
            INNER JOIN contabilidade.plano_analitica
                    ON balancete_extmmaa.exercicio           = plano_analitica.exercicio
                   AND balancete_extmmaa.cod_plano           = plano_analitica.cod_plano
            INNER JOIN  tesouraria.transferencia
                    ON transferencia.cod_plano_credito       = plano_analitica.cod_plano
                   AND transferencia.exercicio               = plano_analitica.exercicio
                   AND transferencia.cod_tipo                = 2
            INNER JOIN tesouraria.transferencia_estornada
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
            INNER JOIN contabilidade.plano_banco
                    ON plano_banco.cod_plano                 = transferencia.cod_plano_debito
                   AND plano_banco.exercicio                 = transferencia.exercicio
            INNER JOIN contabilidade.plano_analitica pa
                    ON plano_banco.exercicio       = pa.exercicio
                   AND plano_banco.cod_plano       = pa.cod_plano
            INNER JOIN contabilidade.plano_conta
                    ON plano_conta.cod_conta = pa.cod_conta
                   AND plano_conta.exercicio = pa.exercicio
            INNER JOIN monetario.conta_corrente
                    ON conta_corrente.cod_banco              = plano_banco.cod_banco
                   AND conta_corrente.cod_agencia            = plano_banco.cod_agencia
                   AND conta_corrente.cod_conta_corrente     = plano_banco.cod_conta_corrente
            INNER JOIN monetario.agencia
                    ON agencia.cod_agencia                   = conta_corrente.cod_agencia
                   AND agencia.cod_banco                     = conta_corrente.cod_banco
            INNER JOIN monetario.banco
                    ON banco.cod_banco                       = agencia.cod_banco
                 WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
                   AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
                   AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                   AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                   AND ordem_pagamento_retencao.cod_receita IS NOT NULL
              GROUP BY orgao.num_orgao
                     , balancete_extmmaa.tipo_lancamento
                     , balancete_extmmaa.sub_tipo_lancamento
                     , banco.num_banco
                     , agencia.num_agencia
                     , tipo_conta
                     , conta_corrente.num_conta_corrente
                     , plano_conta.cod_estrutural
                     , plano_analitica.cod_plano
                     , dt_estorno
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
                      , TO_CHAR(transferencia_estornada.timestamp_estornada, 'ddmmyyyy') AS dt_estorno
                   FROM tcmgo.orgao
             INNER JOIN tcmgo.balancete_extmmaa
                     ON orgao.exercicio = balancete_extmmaa.exercicio
             INNER JOIN contabilidade.plano_analitica
                     ON balancete_extmmaa.exercicio           = plano_analitica.exercicio
                    AND balancete_extmmaa.cod_plano           = plano_analitica.cod_plano
             INNER JOIN tesouraria.transferencia
                     ON transferencia.cod_plano_debito    = plano_analitica.cod_plano
                    AND transferencia.exercicio           = plano_analitica.exercicio
                    AND transferencia.cod_tipo            = 1
             INNER JOIN tesouraria.transferencia_estornada
                     ON transferencia_estornada.cod_entidade  = transferencia.cod_entidade
                    AND transferencia_estornada.tipo          = transferencia.tipo
                    AND transferencia_estornada.exercicio     = transferencia.exercicio
                    AND transferencia_estornada.cod_lote      = transferencia.cod_lote
             INNER JOIN contabilidade.plano_banco
                     ON plano_banco.cod_plano             = transferencia.cod_plano_credito
                    AND plano_banco.exercicio             = transferencia.exercicio
             INNER JOIN contabilidade.plano_analitica pa
                     ON plano_banco.exercicio       = pa.exercicio
                    AND plano_banco.cod_plano       = pa.cod_plano
             INNER JOIN contabilidade.plano_conta
                     ON plano_conta.cod_conta = pa.cod_conta
                    AND plano_conta.exercicio = pa.exercicio
             INNER JOIN monetario.conta_corrente
                     ON conta_corrente.cod_banco          = plano_banco.cod_banco
                    AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                    AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
             INNER JOIN monetario.agencia
                     ON agencia.cod_agencia               = conta_corrente.cod_agencia
                    AND agencia.cod_banco                 = conta_corrente.cod_banco
             INNER JOIN monetario.banco
                     ON banco.cod_banco                   = agencia.cod_banco
                  WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
                    AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
                    AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                    AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
               GROUP BY orgao.num_orgao
                      , balancete_extmmaa.tipo_lancamento
                      , balancete_extmmaa.sub_tipo_lancamento
                      , banco.num_banco
                      , agencia.num_agencia
                      , tipo_conta
                      , conta_corrente.num_conta_corrente
                      , plano_conta.cod_estrutural
                      , plano_analitica.cod_plano
                      , dt_estorno
            ) AS registros
     ORDER BY tipo_registro
            , orgao
            , tipo_lancamento
            , sub_tipo_lancamento
            , desdobra_subtipo
        ";
        return $stSql;
        
    }

    public function recuperaReg12(&$rsRecordSet, $stFiltro = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaReg12().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }

    public function montaRecuperaReg12()
    {
        $stSql = "
             SELECT *
               FROM (
                -- RECEITA EXTA ESTORNADA
                   SELECT 12                                          AS tipo_registro
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
                        , plano_recurso.cod_recurso
                        , recurso.cod_fonte
                        , TO_CHAR(transferencia_estornada.timestamp_estornada, 'ddmmyyyy') AS dt_estorno
                     FROM tcmgo.orgao
               INNER JOIN tcmgo.balancete_extmmaa
                       ON orgao.exercicio = balancete_extmmaa.exercicio
               INNER JOIN contabilidade.plano_analitica
                       ON balancete_extmmaa.exercicio           = plano_analitica.exercicio
                      AND balancete_extmmaa.cod_plano           = plano_analitica.cod_plano
               INNER JOIN tesouraria.transferencia
                       ON transferencia.cod_plano_credito       = plano_analitica.cod_plano
                      AND transferencia.exercicio               = plano_analitica.exercicio
                      AND transferencia.cod_tipo                = 2
               INNER JOIN tesouraria.transferencia_estornada
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
                LEFT JOIN contabilidade.plano_recurso
                       ON plano_recurso.cod_plano              = transferencia.cod_plano_debito
                      AND plano_recurso.exercicio              = transferencia.exercicio
                LEFT JOIN orcamento.recurso
                       ON recurso.cod_recurso = plano_recurso.cod_recurso
                      AND recurso.exercicio  = plano_recurso.exercicio
               INNER JOIN contabilidade.plano_banco
                       ON plano_banco.cod_plano                 = transferencia.cod_plano_debito
                      AND plano_banco.exercicio                 = transferencia.exercicio
               INNER JOIN contabilidade.plano_analitica pa
                       ON plano_banco.exercicio       = pa.exercicio
                      AND plano_banco.cod_plano       = pa.cod_plano
               INNER JOIN contabilidade.plano_conta
                       ON plano_conta.cod_conta = pa.cod_conta
                      AND plano_conta.exercicio = pa.exercicio
               INNER JOIN monetario.conta_corrente
                       ON conta_corrente.cod_banco              = plano_banco.cod_banco
                      AND conta_corrente.cod_agencia            = plano_banco.cod_agencia
                      AND conta_corrente.cod_conta_corrente     = plano_banco.cod_conta_corrente
               INNER JOIN monetario.agencia
                       ON agencia.cod_agencia                   = conta_corrente.cod_agencia
                      AND agencia.cod_banco                     = conta_corrente.cod_banco
               INNER JOIN monetario.banco
                       ON banco.cod_banco                       = agencia.cod_banco
                    WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
                      AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
                      AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                      AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                      AND ordem_pagamento_retencao.cod_receita IS NOT NULL
                 GROUP BY orgao.num_orgao
                        , balancete_extmmaa.tipo_lancamento
                        , balancete_extmmaa.sub_tipo_lancamento
                        , banco.num_banco
                        , agencia.num_agencia
                        , tipo_conta
                        , conta_corrente.num_conta_corrente
                        , plano_conta.cod_estrutural
                        , plano_analitica.cod_plano
                        , plano_recurso.cod_recurso
                        , recurso.cod_fonte
                        , dt_estorno

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
                        , CASE WHEN  (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '9')
                          ELSE
                                    LPAD(ltrim(split_part(num_conta_corrente,'-',1),'0'), 12, '0')
                          END AS conta_corrente
                        , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN '02'
                               ELSE '01'
                          END as tipo_conta
                        , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                        , ABS(SUM(transferencia_estornada.valor))               AS valor
                        , '' AS branco
                        , plano_analitica.cod_plano as nro_extra_orcamentaria
                        , plano_recurso.cod_recurso
                        , recurso.cod_fonte
                        , TO_CHAR(transferencia_estornada.timestamp_estornada, 'ddmmyyyy') AS dt_estorno
                     FROM tcmgo.orgao
               INNER JOIN tcmgo.balancete_extmmaa
                       ON orgao.exercicio = balancete_extmmaa.exercicio
               INNER JOIN contabilidade.plano_analitica
                       ON balancete_extmmaa.exercicio       = plano_analitica.exercicio
                      AND balancete_extmmaa.cod_plano       = plano_analitica.cod_plano
               INNER JOIN tesouraria.transferencia
                       ON transferencia.cod_plano_debito    = plano_analitica.cod_plano
                      AND transferencia.exercicio           = plano_analitica.exercicio
                      AND transferencia.cod_tipo            = 1
               INNER JOIN tesouraria.transferencia_estornada
                       ON transferencia_estornada.cod_entidade  = transferencia.cod_entidade
                      AND transferencia_estornada.tipo          = transferencia.tipo
                      AND transferencia_estornada.exercicio     = transferencia.exercicio
                      AND transferencia_estornada.cod_lote      = transferencia.cod_lote
                LEFT JOIN contabilidade.plano_recurso
                       ON plano_recurso.cod_plano              = transferencia.cod_plano_credito
                      AND plano_recurso.exercicio              = transferencia.exercicio
                LEFT JOIN orcamento.recurso
                       ON recurso.cod_recurso = plano_recurso.cod_recurso
                      AND recurso.exercicio  = plano_recurso.exercicio
               INNER JOIN contabilidade.plano_banco
                       ON plano_banco.cod_plano             = transferencia.cod_plano_credito
                      AND plano_banco.exercicio             = transferencia.exercicio
               INNER JOIN contabilidade.plano_analitica pa
                       ON plano_banco.exercicio       = pa.exercicio
                      AND plano_banco.cod_plano       = pa.cod_plano
               INNER JOIN contabilidade.plano_conta
                       ON plano_conta.cod_conta = pa.cod_conta
                      AND plano_conta.exercicio = pa.exercicio
               INNER JOIN monetario.conta_corrente
                       ON conta_corrente.cod_banco          = plano_banco.cod_banco
                      AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                      AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
               INNER JOIN monetario.agencia
                       ON agencia.cod_agencia               = conta_corrente.cod_agencia
                      AND agencia.cod_banco                 = conta_corrente.cod_banco
               INNER JOIN monetario.banco
                       ON banco.cod_banco                   = agencia.cod_banco
                    WHERE orgao.exercicio                 = '".$this->getDado( 'exercicio' ). "'
                      AND plano_analitica.exercicio       = '".$this->getDado( 'exercicio' ). "'
                      AND transferencia.dt_autenticacao   >= to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' )
                      AND transferencia.dt_autenticacao   <= to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                 GROUP BY orgao.num_orgao
                        , balancete_extmmaa.tipo_lancamento
                        , balancete_extmmaa.sub_tipo_lancamento
                        , banco.num_banco
                        , agencia.num_agencia
                        , tipo_conta
                        , conta_corrente.num_conta_corrente
                        , plano_conta.cod_estrutural
                        , plano_analitica.cod_plano
                        , plano_recurso.cod_recurso
                        , recurso.cod_fonte
                        , dt_estorno
            ) AS registros
     ORDER BY tipo_registro
            , orgao
            , tipo_lancamento
            , sub_tipo_lancamento
            , desdobra_subtipo  ";
        return $stSql;
    }
    

    public function __destruct(){}
}
?>

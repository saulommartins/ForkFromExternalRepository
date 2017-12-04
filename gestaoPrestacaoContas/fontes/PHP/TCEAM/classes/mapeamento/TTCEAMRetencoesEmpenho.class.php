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
    * Data de Criacão: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTPBEmpenho.class.php 39086 2009-03-25 12:07:39Z andrem $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criacão: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCEAMRetencoesEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMRetencoesEmpenho()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function RecuperaRetencoesEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRetencoesEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRetencoesEmpenho()
    {
        $stSql = "  SELECT exercicio                          --ano do empenho para a despesa
        , unidade                            --codigo unidade orcamentaria
        , cod_empenho                        --numero do empenho em questao
        , dt_empenho
        , data_pagamento
        , ano_criacao_conta
        , conta_contabil
        , SUM(valor_retencao) AS valor_retencao
     FROM (
                SELECT despesa.exercicio
                     , LPAD(despesa.num_orgao::varchar,3,'0') || LPAD(despesa.num_unidade::varchar,2,'0') as unidade
                     , empenho.cod_empenho
                     , TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy')  AS dt_empenho
                     , to_char(nota_liquidacao_paga.timestamp::date,'dd/mm/yyyy') AS data_pagamento
                     , plano_conta.exercicio                     AS ano_criacao_conta
                     , substr(replace(plano_conta.cod_estrutural,'.',''),1,15) AS conta_contabil
                     , ordem_pagamento_retencao.vl_retencao      AS valor_retencao
                     , ordem_pagamento.cod_ordem
                     , pagamento_liquidacao.cod_nota
                  FROM empenho.empenho
            INNER JOIN empenho.nota_liquidacao
                    ON nota_liquidacao.cod_empenho       = empenho.cod_empenho
                   AND nota_liquidacao.cod_entidade      = empenho.cod_entidade
                   AND nota_liquidacao.exercicio_empenho = empenho.exercicio
            INNER JOIN empenho.pagamento_liquidacao
                    ON pagamento_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio
                   AND pagamento_liquidacao.cod_entidade         = nota_liquidacao.cod_entidade
                   AND pagamento_liquidacao.cod_nota             = nota_liquidacao.cod_nota
            INNER JOIN empenho.ordem_pagamento
                    ON ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio
                   AND ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade
                   AND ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem
            INNER JOIN empenho.ordem_pagamento_retencao
                    ON ordem_pagamento_retencao.exercicio    = ordem_pagamento.exercicio
                   AND ordem_pagamento_retencao.cod_entidade = ordem_pagamento.cod_entidade
                   AND ordem_pagamento_retencao.cod_ordem    = ordem_pagamento.cod_ordem
             LEFT JOIN empenho.ordem_pagamento_anulada
                    ON ordem_pagamento_anulada.exercicio    = ordem_pagamento.exercicio
                   AND ordem_pagamento_anulada.cod_entidade = ordem_pagamento.cod_entidade
                   AND ordem_pagamento_anulada.cod_ordem    = ordem_pagamento.cod_ordem
             LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao
                    ON transferencia_ordem_pagamento_retencao.exercicio    = ordem_pagamento_retencao.exercicio
                   AND transferencia_ordem_pagamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                   AND transferencia_ordem_pagamento_retencao.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                   AND transferencia_ordem_pagamento_retencao.cod_plano    = ordem_pagamento_retencao.cod_plano
             LEFT JOIN tesouraria.arrecadacao_ordem_pagamento_retencao
                    ON arrecadacao_ordem_pagamento_retencao.exercicio    = ordem_pagamento_retencao.exercicio
                   AND arrecadacao_ordem_pagamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                   AND arrecadacao_ordem_pagamento_retencao.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                   AND arrecadacao_ordem_pagamento_retencao.cod_plano    = ordem_pagamento_retencao.cod_plano
            INNER JOIN contabilidade.plano_analitica
                    ON plano_analitica.exercicio = ordem_pagamento_retencao.exercicio
                   AND plano_analitica.cod_plano = ordem_pagamento_retencao.cod_plano
            INNER JOIN contabilidade.plano_conta
                    ON plano_conta.exercicio = plano_analitica.exercicio
                   AND plano_conta.cod_conta = plano_analitica.cod_conta
            INNER JOIN empenho.nota_liquidacao_paga
                    ON nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                   AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                   AND nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
             LEFT JOIN empenho.nota_liquidacao_paga_anulada
                    ON nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                   AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                   AND nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                   AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
            INNER JOIN empenho.pre_empenho
                    ON pre_empenho.exercicio = empenho.exercicio
                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
            INNER JOIN (
                           SELECT exercicio
                                , cod_pre_empenho
                                , SUM(vl_total) as valor_empenhado
                             FROM empenho.item_pre_empenho
                         GROUP BY exercicio, cod_pre_empenho
                       ) AS item_pre_empenho
                    ON item_pre_empenho.exercicio = pre_empenho.exercicio
                   AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
             LEFT JOIN empenho.item_pre_empenho_julgamento as ipej
                    ON item_pre_empenho.exercicio       = ipej.exercicio
                   AND item_pre_empenho.cod_pre_empenho = ipej.cod_pre_empenho
            INNER JOIN empenho.pre_empenho_despesa
                    ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                   AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
            INNER JOIN orcamento.despesa
                    ON despesa.exercicio = pre_empenho_despesa.exercicio
                   AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
            INNER JOIN orcamento.conta_despesa
                    ON conta_despesa.exercicio = despesa.exercicio
                   AND conta_despesa.cod_conta = despesa.cod_conta
            INNER JOIN orcamento.recurso
                    ON recurso.cod_recurso = despesa.cod_recurso
                   AND recurso.exercicio = despesa.exercicio
                 WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                   AND TO_CHAR(nota_liquidacao_paga.timestamp::date,'mm') = '".$this->getDado('inMes')."'
                   AND nota_liquidacao_paga_anulada.cod_nota IS NULL
                   AND ordem_pagamento_anulada.cod_ordem IS NULL
                   AND despesa.cod_entidade in (".$this->getDado('stEntidades').")
              GROUP BY despesa.exercicio
                     , unidade
                     , empenho.cod_empenho
                     , empenho.dt_empenho
                     , nota_liquidacao_paga.timestamp::date
                     , plano_conta.exercicio
                     , plano_conta.cod_estrutural
                     , ordem_pagamento_retencao.vl_retencao
                     , ordem_pagamento.cod_ordem
                     , pagamento_liquidacao.cod_nota
                     , conta_despesa.exercicio
              ORDER BY conta_despesa.exercicio
                     , empenho.cod_empenho
                     , plano_conta.cod_estrutural
          ) as tbl
 GROUP BY exercicio
        , unidade
        , cod_empenho
        , dt_empenho
        , data_pagamento
        , ano_criacao_conta
        , conta_contabil
 ORDER BY cod_empenho

\n";

        return $stSql;
    }

}

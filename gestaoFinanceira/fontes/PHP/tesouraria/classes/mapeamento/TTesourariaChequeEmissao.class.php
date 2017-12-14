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
 * Mapeamento da tabela tesouraria.cheque_emissao
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTesourariaChequeEmissao extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaChequeEmissao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.cheque_emissao');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_agencia, cod_banco, cod_conta_corrente, num_cheque');

        $this->AddCampo('cod_agencia'        ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_banco'          ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_conta_corrente' ,'integer'   , true , ''     , true , true );
        $this->AddCampo('num_cheque'         ,'varchar'   , true , '15'   , true , true );
        $this->AddCampo('data_emissao'       ,'date'      , true , ''     , false, false);
        $this->AddCampo('valor'              ,'numeric'   , true , '14,2' , false, false);
        $this->AddCampo('descricao'          ,'text'      , false, ''     , false, false);
        $this->AddCampo('timestamp_emissao'  ,'timestamp' , true , ''     , true , false );
    }
    
     
      /**
     * Método que retorna os cheques analitico vinculadoa emissao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    function getChequeEmissao(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->recuperaChequeEmissao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }

    public function recuperaChequeEmissao()
    {
        $stSql = "
            SELECT cheque.num_cheque
                 , conta_corrente.cod_conta_corrente
                 , conta_corrente.num_conta_corrente
                 , agencia.cod_agencia
                 , agencia.num_agencia
                 , agencia.nom_agencia
                 , banco.cod_banco
                 , banco.num_banco
                 , banco.nom_banco
                 , cheque_emissao.data_emissao
                 , cheque_emissao.nom_credor
                 , cheque_emissao.valor
                 , cheque_emissao.descricao
                 , cheque_emissao.tipo_emissao
                 , cheque_emissao.timestamp_emissao
                 , cheque_emissao.timestamp_baixa
              FROM tesouraria.cheque

        INNER JOIN ( SELECT cheque_emissao.num_cheque
                          , cheque_emissao.cod_banco
                          , cheque_emissao.cod_agencia
                          , cheque_emissao.cod_conta_corrente
                          , cheque_emissao.timestamp_emissao
                          , cheque_emissao_baixa.timestamp_baixa
                          , TO_CHAR(cheque_emissao.data_emissao,'dd/mm/yyyy') AS data_emissao
                          , cheque_emissao.valor
                          , CASE WHEN (cheque_emissao_ordem_pagamento.nom_credor IS NOT NULL)
                                 THEN cheque_emissao_ordem_pagamento.nom_credor
                                 WHEN (cheque_emissao_transferencia.nom_credor IS NOT NULL)
                                 THEN cheque_emissao_transferencia.nom_credor
                                 WHEN (cheque_emissao_recibo_extra.nom_credor IS NOT NULL)
                                 THEN cheque_emissao_recibo_extra.nom_credor
                                 ELSE ''
                            END AS nom_credor
                          , CASE WHEN (cheque_emissao_ordem_pagamento.num_cheque IS NOT NULL)
                                 THEN 'ordem_pagamento'
                                 WHEN (cheque_emissao_transferencia.num_cheque IS NOT NULL)
                                 THEN 'transferencia'
                                 WHEN (cheque_emissao_recibo_extra.num_cheque IS NOT NULL)
                                 THEN 'recibo_extra'
                                 ELSE ''
                            END AS tipo_emissao
                          , cheque_emissao.descricao
                       FROM tesouraria.cheque_emissao
                 INNER JOIN ( SELECT cheque_emissao.cod_banco
                                   , cheque_emissao.cod_agencia
                                   , cheque_emissao.cod_conta_corrente
                                   , cheque_emissao.num_cheque
                                   , MAX(cheque_emissao.timestamp_emissao) AS timestamp_emissao
                                FROM tesouraria.cheque_emissao
                            GROUP BY cheque_emissao.cod_banco
                                   , cheque_emissao.cod_agencia
                                   , cheque_emissao.cod_conta_corrente
                                   , cheque_emissao.num_cheque
                            ) AS cheque_emissao_max
                         ON cheque_emissao.cod_banco          = cheque_emissao_max.cod_banco
                        AND cheque_emissao.cod_agencia        = cheque_emissao_max.cod_agencia
                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_max.cod_conta_corrente
                        AND cheque_emissao.num_cheque         = cheque_emissao_max.num_cheque
                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_max.timestamp_emissao

                  LEFT JOIN ( SELECT cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                                   , cheque_emissao_baixa.timestamp_baixa
                                FROM tesouraria.cheque_emissao_baixa
                          INNER JOIN ( SELECT cheque_emissao_baixa.cod_banco
                                            , cheque_emissao_baixa.cod_agencia
                                            , cheque_emissao_baixa.cod_conta_corrente
                                            , cheque_emissao_baixa.num_cheque
                                            , cheque_emissao_baixa.timestamp_emissao
                                            , MAX(cheque_emissao_baixa.timestamp_baixa) AS timestamp_baixa
                                         FROM tesouraria.cheque_emissao_baixa
                                     GROUP BY cheque_emissao_baixa.cod_banco
                                            , cheque_emissao_baixa.cod_agencia
                                            , cheque_emissao_baixa.cod_conta_corrente
                                            , cheque_emissao_baixa.num_cheque
                                            , cheque_emissao_baixa.timestamp_emissao
                                     ) AS cheque_emissao_baixa_max
                                  ON cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_max.cod_banco
                                 AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_max.cod_agencia
                                 AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_max.cod_conta_corrente
                                 AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_max.num_cheque
                                 AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_max.timestamp_emissao
                                 AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_max.timestamp_baixa
                            ) AS cheque_emissao_baixa
                         ON cheque_emissao.cod_banco          = cheque_emissao_baixa.cod_banco
                        AND cheque_emissao.cod_agencia        = cheque_emissao_baixa.cod_agencia
                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_baixa.cod_conta_corrente
                        AND cheque_emissao.num_cheque         = cheque_emissao_baixa.num_cheque
                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_baixa.timestamp_emissao

                  LEFT JOIN ( SELECT cheque_emissao_ordem_pagamento.cod_banco
                                   , cheque_emissao_ordem_pagamento.cod_agencia
                                   , cheque_emissao_ordem_pagamento.cod_conta_corrente
                                   , cheque_emissao_ordem_pagamento.num_cheque
                                   , cheque_emissao_ordem_pagamento.timestamp_emissao
                                   , cgm_credor.nom_cgm AS nom_credor
                                FROM tesouraria.cheque_emissao_ordem_pagamento

                          INNER JOIN ( SELECT pagamento_liquidacao.cod_nota
                                            , pagamento_liquidacao.cod_entidade
                                            , pagamento_liquidacao.exercicio
                                            , pagamento_liquidacao.cod_ordem
                                         FROM empenho.pagamento_liquidacao
                                   INNER JOIN ( SELECT MAX(pagamento_liquidacao.cod_nota) AS cod_nota
                                                     , pagamento_liquidacao.cod_entidade
                                                     , pagamento_liquidacao.exercicio
                                                     , pagamento_liquidacao.cod_ordem
                                                  FROM empenho.pagamento_liquidacao
                                              GROUP BY pagamento_liquidacao.cod_entidade
                                                     , pagamento_liquidacao.exercicio
                                                     , pagamento_liquidacao.cod_ordem
                                              ) AS pagamento_liquidacao_max
                                           ON pagamento_liquidacao.cod_ordem = pagamento_liquidacao_max.cod_ordem
                                          AND pagamento_liquidacao.cod_nota  = pagamento_liquidacao_max.cod_nota
                                          AND pagamento_liquidacao.exercicio = pagamento_liquidacao_max.exercicio
                                          AND pagamento_liquidacao.cod_entidade = pagamento_liquidacao_max.cod_entidade
                                     ) AS pagamento_liquidacao
                                  ON cheque_emissao_ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem
                                 AND cheque_emissao_ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade
                                 AND cheque_emissao_ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio
                          INNER JOIN empenho.nota_liquidacao
                                  ON pagamento_liquidacao.cod_nota     = nota_liquidacao.cod_nota
                                 AND pagamento_liquidacao.cod_entidade = nota_liquidacao.cod_entidade
                                 AND pagamento_liquidacao.exercicio    = nota_liquidacao.exercicio
                          INNER JOIN empenho.empenho
                                  ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                                 AND nota_liquidacao.cod_empenho       = empenho.cod_empenho
                                 AND nota_liquidacao.cod_entidade      = empenho.cod_entidade
                          INNER JOIN empenho.pre_empenho
                                  ON empenho.exercicio       = pre_empenho.exercicio
                                 AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                          INNER JOIN sw_cgm AS cgm_credor
                                  ON pre_empenho.cgm_beneficiario = cgm_credor.numcgm
                            ) AS cheque_emissao_ordem_pagamento
                         ON cheque_emissao.cod_banco          = cheque_emissao_ordem_pagamento.cod_banco
                        AND cheque_emissao.cod_agencia        = cheque_emissao_ordem_pagamento.cod_agencia
                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_ordem_pagamento.cod_conta_corrente
                        AND cheque_emissao.num_cheque         = cheque_emissao_ordem_pagamento.num_cheque
                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_ordem_pagamento.timestamp_emissao

                  LEFT JOIN ( SELECT cheque_emissao_transferencia.cod_banco
                                   , cheque_emissao_transferencia.cod_agencia
                                   , cheque_emissao_transferencia.cod_conta_corrente
                                   , cheque_emissao_transferencia.num_cheque
                                   , cheque_emissao_transferencia.timestamp_emissao
                                   , sw_cgm.nom_cgm AS nom_credor
                                FROM tesouraria.cheque_emissao_transferencia
                           LEFT JOIN tesouraria.transferencia_credor
                                  ON cheque_emissao_transferencia.cod_lote     = transferencia_credor.cod_lote
                                 AND cheque_emissao_transferencia.cod_entidade = transferencia_credor.cod_entidade
                                 AND cheque_emissao_transferencia.tipo         = transferencia_credor.tipo
                                 AND cheque_emissao_transferencia.exercicio    = transferencia_credor.exercicio
                           LEFT JOIN sw_cgm
                                  ON transferencia_credor.numcgm = sw_cgm.numcgm
                            ) AS cheque_emissao_transferencia
                         ON cheque_emissao.cod_banco          = cheque_emissao_transferencia.cod_banco
                        AND cheque_emissao.cod_agencia        = cheque_emissao_transferencia.cod_agencia
                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_transferencia.cod_conta_corrente
                        AND cheque_emissao.num_cheque         = cheque_emissao_transferencia.num_cheque
                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_transferencia.timestamp_emissao

                  LEFT JOIN ( SELECT cheque_emissao_recibo_extra.cod_banco
                                   , cheque_emissao_recibo_extra.cod_agencia
                                   , cheque_emissao_recibo_extra.cod_conta_corrente
                                   , cheque_emissao_recibo_extra.num_cheque
                                   , cheque_emissao_recibo_extra.timestamp_emissao
                                   , sw_cgm.nom_cgm AS nom_credor
                                FROM tesouraria.cheque_emissao_recibo_extra
                           LEFT JOIN tesouraria.recibo_extra_credor
                                  ON cheque_emissao_recibo_extra.cod_entidade     = recibo_extra_credor.cod_entidade
                                 AND cheque_emissao_recibo_extra.exercicio        = recibo_extra_credor.exercicio
                                 AND cheque_emissao_recibo_extra.cod_recibo_extra = recibo_extra_credor.cod_recibo_extra
                                 AND cheque_emissao_recibo_extra.tipo_recibo      = recibo_extra_credor.tipo_recibo
                           LEFT JOIN sw_cgm
                                  ON recibo_extra_credor.numcgm = sw_cgm.numcgm
                            ) AS cheque_emissao_recibo_extra
                         ON cheque_emissao.cod_banco          = cheque_emissao_recibo_extra.cod_banco
                        AND cheque_emissao.cod_agencia        = cheque_emissao_recibo_extra.cod_agencia
                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_recibo_extra.cod_conta_corrente
                        AND cheque_emissao.num_cheque         = cheque_emissao_recibo_extra.num_cheque
                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_recibo_extra.timestamp_emissao

                   ) AS cheque_emissao
                ON cheque.cod_banco          = cheque_emissao.cod_banco
               AND cheque.cod_agencia        = cheque_emissao.cod_agencia
               AND cheque.cod_conta_corrente = cheque_emissao.cod_conta_corrente
               AND cheque.num_cheque         = cheque_emissao.num_cheque

        INNER JOIN monetario.conta_corrente
                ON cheque.cod_conta_corrente  = conta_corrente.cod_conta_corrente
               AND cheque.cod_agencia         = conta_corrente.cod_agencia
               AND cheque.cod_banco           = conta_corrente.cod_banco
        INNER JOIN monetario.agencia
                ON conta_corrente.cod_agencia = agencia.cod_agencia
               AND conta_corrente.cod_banco   = agencia.cod_banco
        INNER JOIN monetario.banco
                ON agencia.cod_banco          = banco.cod_banco
        ";

        return $stSql;
    }

    /**
     * Método que constroi a string SQL para o metodo getChequeAnulacao
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    
    function getChequeAnulacao(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->recuperaChequeAnulacao().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }
    public function recuperaChequeAnulacao()
    {
        $stSql = "
            SELECT cheque.num_cheque
                 , cheque_emissao.cod_conta_corrente
                 , cheque_emissao.cod_agencia
                 , cheque_emissao.cod_banco
                 , cheque_emissao.valor
                 , TO_CHAR(cheque_emissao.data_emissao,'dd/mm/yyyy') AS data_emissao
                 , CASE WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN cheque_emissao_ordem_pagamento.cod_entidade
                        WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN cheque_emissao_recibo_extra.cod_entidade
                        ELSE cheque_emissao_transferencia.cod_entidade
                   END AS cod_entidade
                 , CASE WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN cheque_emissao_ordem_pagamento.nom_entidade
                        WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN cheque_emissao_recibo_extra.nom_entidade
                        ELSE cheque_emissao_transferencia.nom_entidade
                   END AS nom_entidade
                 , CASE WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN 'Despesa Extra'
                        WHEN (cheque_emissao_transferencia.cod_tipo IS NOT NULL)
                        THEN 'Transferência'
                        WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN 'Ordem de Pagamento'
                   END AS tipo_emissao
                 , CASE WHEN (cheque_emissao_recibo_extra.cod_recibo_extra IS NOT NULL)
                        THEN cheque_emissao_recibo_extra.nom_credor
                        WHEN (cheque_emissao_transferencia.cod_tipo IS NOT NULL)
                        THEN cheque_emissao_transferencia.nom_credor
                        WHEN (cheque_emissao_ordem_pagamento.cod_ordem IS NOT NULL)
                        THEN cheque_emissao_ordem_pagamento.nom_credor
                   END AS nom_credor
              FROM tesouraria.cheque

        INNER JOIN monetario.conta_corrente
                ON cheque.cod_conta_corrente  = conta_corrente.cod_conta_corrente
               AND cheque.cod_agencia         = conta_corrente.cod_agencia
               AND cheque.cod_banco           = conta_corrente.cod_banco
        INNER JOIN monetario.agencia
                ON conta_corrente.cod_agencia = agencia.cod_agencia
               AND conta_corrente.cod_banco   = agencia.cod_banco
        INNER JOIN monetario.banco
                ON agencia.cod_banco          = banco.cod_banco

        INNER JOIN tesouraria.cheque_emissao
                ON cheque.cod_banco          = cheque_emissao.cod_banco
               AND cheque.cod_agencia        = cheque_emissao.cod_agencia
               AND cheque.cod_conta_corrente = cheque_emissao.cod_conta_corrente
               AND cheque.num_cheque         = cheque_emissao.num_cheque

        INNER JOIN ( SELECT cheque_emissao.cod_banco
                          , cheque_emissao.cod_agencia
                          , cheque_emissao.cod_conta_corrente
                          , cheque_emissao.num_cheque
                          , MAX(cheque_emissao.timestamp_emissao) AS timestamp_emissao
                       FROM tesouraria.cheque_emissao
                   GROUP BY cheque_emissao.cod_banco
                          , cheque_emissao.cod_agencia
                          , cheque_emissao.cod_conta_corrente
                          , cheque_emissao.num_cheque
                 ) AS cheque_emissao_max
                ON cheque_emissao.cod_banco          = cheque_emissao_max.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_max.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_max.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_max.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_max.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_baixa.cod_banco
                          , cheque_emissao_baixa.cod_agencia
                          , cheque_emissao_baixa.cod_conta_corrente
                          , cheque_emissao_baixa.num_cheque
                          , cheque_emissao_baixa.timestamp_emissao
                          , cheque_emissao_baixa.timestamp_baixa
                       FROM tesouraria.cheque_emissao_baixa
                 INNER JOIN ( SELECT cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                                   , MAX(cheque_emissao_baixa.timestamp_baixa) AS timestamp_baixa
                                FROM tesouraria.cheque_emissao_baixa
                            GROUP BY cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                            ) AS cheque_emissao_baixa_max
                         ON cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_max.cod_banco
                        AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_max.cod_agencia
                        AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_max.cod_conta_corrente
                        AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_max.num_cheque
                        AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_max.timestamp_emissao
                        AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_max.timestamp_baixa
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM tesouraria.cheque_emissao_baixa_anulada
                                          WHERE cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_anulada.cod_banco
                                            AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_anulada.cod_agencia
                                            AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_anulada.cod_conta_corrente
                                            AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_anulada.num_cheque
                                            AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_anulada.timestamp_emissao
                                            AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_anulada.timestamp_baixa
                                       )
                   ) AS cheque_emissao_baixa
                ON cheque_emissao.cod_banco          = cheque_emissao_baixa.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_baixa.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_baixa.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_baixa.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_baixa.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_ordem_pagamento.cod_banco
                          , cheque_emissao_ordem_pagamento.cod_agencia
                          , cheque_emissao_ordem_pagamento.cod_conta_corrente
                          , cheque_emissao_ordem_pagamento.num_cheque
                          , cheque_emissao_ordem_pagamento.timestamp_emissao
                          , entidade.cod_entidade
                          , sw_cgm.nom_cgm AS nom_entidade
                          , cheque_emissao_ordem_pagamento.cod_ordem
                          , cheque_emissao_ordem_pagamento.exercicio
                          , cgm_credor.nom_cgm AS nom_credor
                       FROM tesouraria.cheque_emissao_ordem_pagamento
                 INNER JOIN ( SELECT pagamento_liquidacao.cod_nota
                                   , pagamento_liquidacao.cod_entidade
                                   , pagamento_liquidacao.exercicio
                                   , pagamento_liquidacao.cod_ordem
                                FROM empenho.pagamento_liquidacao
                          INNER JOIN ( SELECT MAX(pagamento_liquidacao.cod_nota) AS cod_nota
                                            , pagamento_liquidacao.cod_entidade
                                            , pagamento_liquidacao.exercicio
                                            , pagamento_liquidacao.cod_ordem
                                         FROM empenho.pagamento_liquidacao
                                     GROUP BY pagamento_liquidacao.cod_entidade
                                            , pagamento_liquidacao.exercicio
                                            , pagamento_liquidacao.cod_ordem
                                     ) AS pagamento_liquidacao_max
                                  ON pagamento_liquidacao.cod_ordem = pagamento_liquidacao_max.cod_ordem
                                 AND pagamento_liquidacao.cod_nota  = pagamento_liquidacao_max.cod_nota
                                 AND pagamento_liquidacao.exercicio = pagamento_liquidacao_max.exercicio
                                 AND pagamento_liquidacao.cod_entidade = pagamento_liquidacao_max.cod_entidade
                            ) AS pagamento_liquidacao
                         ON cheque_emissao_ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem
                        AND cheque_emissao_ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade
                        AND cheque_emissao_ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio
                 INNER JOIN empenho.nota_liquidacao
                         ON pagamento_liquidacao.cod_nota     = nota_liquidacao.cod_nota
                        AND pagamento_liquidacao.cod_entidade = nota_liquidacao.cod_entidade
                        AND pagamento_liquidacao.exercicio    = nota_liquidacao.exercicio
                 INNER JOIN empenho.empenho
                         ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                        AND nota_liquidacao.cod_empenho       = empenho.cod_empenho
                        AND nota_liquidacao.cod_entidade      = empenho.cod_entidade
                 INNER JOIN empenho.pre_empenho
                         ON empenho.exercicio       = pre_empenho.exercicio
                        AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                 INNER JOIN sw_cgm AS cgm_credor
                         ON pre_empenho.cgm_beneficiario = cgm_credor.numcgm
                 INNER JOIN orcamento.entidade
                         ON cheque_emissao_ordem_pagamento.cod_entidade = entidade.cod_entidade
                        AND cheque_emissao_ordem_pagamento.exercicio    = entidade.exercicio
                 INNER JOIN sw_cgm
                         ON entidade.numcgm = sw_cgm.numcgm
                   ) AS cheque_emissao_ordem_pagamento
                ON cheque_emissao.cod_banco          = cheque_emissao_ordem_pagamento.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_ordem_pagamento.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_ordem_pagamento.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_ordem_pagamento.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_ordem_pagamento.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_recibo_extra.cod_banco
                          , cheque_emissao_recibo_extra.cod_agencia
                          , cheque_emissao_recibo_extra.cod_conta_corrente
                          , cheque_emissao_recibo_extra.num_cheque
                          , cheque_emissao_recibo_extra.timestamp_emissao
                          , entidade.cod_entidade
                          , sw_cgm.nom_cgm AS nom_entidade
                          , cheque_emissao_recibo_extra.cod_recibo_extra
                          , cheque_emissao_recibo_extra.exercicio
                          , cgm_credor.nom_cgm AS nom_credor
                       FROM tesouraria.cheque_emissao_recibo_extra
                 INNER JOIN orcamento.entidade
                         ON cheque_emissao_recibo_extra.cod_entidade = entidade.cod_entidade
                        AND cheque_emissao_recibo_extra.exercicio    = entidade.exercicio
                 INNER JOIN sw_cgm
                         ON entidade.numcgm = sw_cgm.numcgm
                  LEFT JOIN tesouraria.recibo_extra_credor
                         ON cheque_emissao_recibo_extra.cod_entidade     = recibo_extra_credor.cod_entidade
                        AND cheque_emissao_recibo_extra.exercicio        = recibo_extra_credor.exercicio
                        AND cheque_emissao_recibo_extra.cod_recibo_extra = recibo_extra_credor.cod_recibo_extra
                        AND cheque_emissao_recibo_extra.tipo_recibo      = recibo_extra_credor.tipo_recibo
                  LEFT JOIN sw_cgm AS cgm_credor
                         ON recibo_extra_credor.numcgm = cgm_credor.numcgm

                   ) AS cheque_emissao_recibo_extra
                ON cheque_emissao.cod_banco          = cheque_emissao_recibo_extra.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_recibo_extra.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_recibo_extra.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_recibo_extra.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_recibo_extra.timestamp_emissao

         LEFT JOIN ( SELECT cheque_emissao_transferencia.cod_banco
                          , cheque_emissao_transferencia.cod_agencia
                          , cheque_emissao_transferencia.cod_conta_corrente
                          , cheque_emissao_transferencia.num_cheque
                          , cheque_emissao_transferencia.timestamp_emissao
                          , entidade.cod_entidade
                          , entidade_cgm.nom_cgm AS nom_entidade
                          , transferencia.exercicio
                          , transferencia.cod_plano_credito
                          , transferencia.cod_plano_debito
                          , transferencia.cod_tipo
                          , sw_cgm.nom_cgm AS nom_credor
                       FROM tesouraria.cheque_emissao_transferencia
                 INNER JOIN tesouraria.transferencia
                         ON cheque_emissao_transferencia.cod_lote     = transferencia.cod_lote
                        AND cheque_emissao_transferencia.cod_entidade = transferencia.cod_entidade
                        AND cheque_emissao_transferencia.exercicio    = transferencia.exercicio
                        AND cheque_emissao_transferencia.tipo         = transferencia.tipo
                  LEFT JOIN tesouraria.transferencia_credor
                         ON cheque_emissao_transferencia.cod_lote     = transferencia_credor.cod_lote
                        AND cheque_emissao_transferencia.cod_entidade = transferencia_credor.cod_entidade
                        AND cheque_emissao_transferencia.tipo         = transferencia_credor.tipo
                        AND cheque_emissao_transferencia.exercicio    = transferencia_credor.exercicio
                  LEFT JOIN sw_cgm
                         ON transferencia_credor.numcgm = sw_cgm.numcgm
                 INNER JOIN orcamento.entidade
                         ON transferencia.cod_entidade = entidade.cod_entidade
                        AND transferencia.exercicio    = entidade.exercicio
                 INNER JOIN sw_cgm AS entidade_cgm
                         ON entidade.numcgm = entidade_cgm.numcgm

                   ) AS cheque_emissao_transferencia
                ON cheque_emissao.cod_banco          = cheque_emissao_transferencia.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_transferencia.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_transferencia.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_transferencia.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_transferencia.timestamp_emissao
         LEFT JOIN tesouraria.cheque_emissao_anulada
                ON cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
        ";

         return $stSql;
    }
    
public function __destruct(){}

}

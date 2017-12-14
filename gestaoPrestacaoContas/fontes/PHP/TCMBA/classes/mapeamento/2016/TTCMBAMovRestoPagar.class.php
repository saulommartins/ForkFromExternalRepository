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
    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 03/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Evandro Melos
    * $Id: TTCMBAMovRestoPagar.class.php 63787 2015-10-13 18:41:08Z lisiane $
    * $Rev:$
    * $Author:$
    * $Date:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAMovRestoPagar extends Persistente {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaMovRestoPagar(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaMovRestoPagar().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMovRestoPagar()
    {
        $stSql = "SELECT 
                         1 AS tipo_registro
                         , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                         , '".$this->getDado('competencia')."'     AS competencia
                         , 9 AS tipo_movimentacao
                         , '".$this->getDado('exercicio_anterior')."' AS exercicio_anterior
                         , '".$this->getDado('exercicio')."'          AS ano
                         , CASE WHEN restos_pre_empenho.cod_pre_empenho IS NOT NULL THEN
                                    restos_pre_empenho.recurso
                               ELSE
                                    despesa.cod_recurso
                         END AS cod_fonte_recurso
                         , '' AS reservado_tcm_1
                         , '' AS reservado_tcm_2
                         , CASE WHEN restos_pre_empenho.cod_pre_empenho IS NOT NULL THEN
                                        SUBSTR(restos_pre_empenho.cod_estrutural,2,1)
                                ELSE
                                        SUBSTR(conta_despesa.cod_estrutural,3,1) 
                          END AS tipo_natureza_despesa
                         , TO_CHAR(liquidacao_paga.timestamp,'dd/mm/yyyy')  AS data_pagamento
                         , liquidacao_paga.vl_total                             AS vl_pagamento

                    FROM empenho.nota_liquidacao
              
              INNER JOIN empenho.empenho
                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho
              
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              
              INNER JOIN (  SELECT nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
                                 , nota_liquidacao_paga.timestamp
                                 , ( SUM(COALESCE(nota_liquidacao_paga.vl_total,0.00)) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) ) AS vl_total
              
                              FROM (  SELECT nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                           , nota_liquidacao_paga.timestamp
                                           , SUM(nota_liquidacao_paga.vl_pago) AS vl_total
                                        FROM empenho.nota_liquidacao_paga
                                       WHERE TO_DATE(nota_liquidacao_paga.timestamp::TEXT,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') 
                                         AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                    GROUP BY nota_liquidacao_paga.exercicio
                                           , nota_liquidacao_paga.cod_entidade
                                           , nota_liquidacao_paga.cod_nota
                                           , nota_liquidacao_paga.timestamp
                                   ) AS nota_liquidacao_paga
              
                         LEFT JOIN (  SELECT nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , nota_liquidacao_paga_anulada.timestamp_anulada AS timestamp
                                           , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_anulado
                                        FROM empenho.nota_liquidacao_paga_anulada
                                       WHERE TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::TEXT,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') 
                                         AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                    GROUP BY nota_liquidacao_paga_anulada.exercicio
                                           , nota_liquidacao_paga_anulada.cod_entidade
                                           , nota_liquidacao_paga_anulada.cod_nota
                                           , nota_liquidacao_paga_anulada.timestamp_anulada
                                   ) AS nota_liquidacao_paga_anulada
                                ON nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                               AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                               AND nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                          GROUP BY nota_liquidacao_paga.exercicio
                                 , nota_liquidacao_paga.cod_entidade
                                 , nota_liquidacao_paga.cod_nota
                                 , nota_liquidacao_paga.timestamp
                         ) AS liquidacao_paga
                      ON liquidacao_paga.exercicio = nota_liquidacao.exercicio
                     AND liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                     AND liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
            
              INNER JOIN orcamento.entidade
                      ON entidade.exercicio = empenho.exercicio
                     AND entidade.cod_entidade = empenho.cod_entidade
              
              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = entidade.numcgm
            
               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                     AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
            
               LEFT JOIN orcamento.conta_despesa
                      ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                     AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
              
               LEFT JOIN orcamento.despesa
                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
            
               LEFT JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                     AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
              
               LEFT JOIN empenho.empenho_anulado
                      ON empenho.exercicio = empenho_anulado.exercicio
                     AND empenho.cod_entidade = empenho_anulado.cod_entidade
                     AND empenho.cod_empenho = empenho_anulado.cod_empenho
            
                   WHERE empenho.exercicio <= '".$this->getDado('exercicio_anterior')."'
                     AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
        ";
        return $stSql;
    }
    
}

?>
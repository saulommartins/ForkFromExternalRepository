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
    * Data de Criação: 24/07/2014
    *
    *
    * @author Desenvolvedor: Arthur Cruz
    *
    * @package URBEM
    * @subpackage Mapeamento
    *
    Id:$
    *
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBEstornoLiquidacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBEstornoLiquidacao()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaEstornoLiquidacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaEstornoLiquidacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEstornoLiquidacao()
{    
    $stSql = " SELECT pre_empenho.exercicio as exercicio_empenho
                    , LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria
                    , empenho.cod_empenho AS num_empenho
                    , nota_liquidacao.cod_nota AS num_liquidacao
                    , SUBSTR(nota_liquidacao_item_anulado.oid::VARCHAR,length(nota_liquidacao_item_anulado.oid::VARCHAR)-6,7) AS numero_estorno
                    , TO_CHAR(nota_liquidacao_item_anulado.timestamp,'dd/mm/yyyy') AS data_estorno
                    , 'Anulação da Liquidação Nro: '||nota_liquidacao.cod_nota||' de '||to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') AS motivo
                    , REPLACE(LPAD(COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)::VARCHAR,16,'0'),'.',',') AS valor_anulado    
                    
                 FROM empenho.pre_empenho
       
           INNER JOIN empenho.empenho
                   ON empenho.exercicio = pre_empenho.exercicio
                  AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
       
           INNER JOIN empenho.nota_liquidacao
                   ON empenho.exercicio    = nota_liquidacao.exercicio_empenho                                 
                  AND empenho.cod_entidade = nota_liquidacao.cod_entidade                                      
                  AND empenho.cod_empenho  = nota_liquidacao.cod_empenho

           INNER JOIN empenho.nota_liquidacao_item_anulado
                   ON nota_liquidacao_item_anulado.cod_entidade=nota_liquidacao.cod_entidade
                  AND nota_liquidacao_item_anulado.exercicio=nota_liquidacao.exercicio
                  AND nota_liquidacao_item_anulado.cod_nota=nota_liquidacao.cod_nota
       
           INNER JOIN empenho.pre_empenho_despesa
                   ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio  
                  AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
          
           INNER JOIN orcamento.despesa
                   ON pre_empenho_despesa.exercicio   = despesa.exercicio  
                  AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa 
          
           INNER JOIN orcamento.conta_despesa
                   ON despesa.exercicio = conta_despesa.exercicio  
                  AND despesa.cod_conta = conta_despesa.cod_conta  
       
            LEFT JOIN tcepb.nota_fiscal
                   ON nota_fiscal.exercicio           = nota_liquidacao.exercicio           
                  AND nota_fiscal.cod_entidade        = nota_liquidacao.cod_entidade     
                  AND nota_fiscal.cod_nota_liquidacao = nota_liquidacao.cod_nota   
       
                WHERE TO_CHAR(nota_liquidacao_item_anulado.timestamp, 'mm') = '".$this->getDado('inMes')."'
                  AND nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'
                  AND nota_liquidacao.cod_entidade IN ( ".$this->getDado('stEntidades')." )
               
             GROUP BY pre_empenho.exercicio,
                      unidade_orcamentaria,
                      empenho.cod_empenho,
                      nota_liquidacao.cod_nota,
                      nota_liquidacao.dt_liquidacao,
                      nota_liquidacao_item_anulado.oid,
                      nota_liquidacao_item_anulado.cod_entidade,
                      nota_liquidacao_item_anulado.exercicio,
                      nota_liquidacao_item_anulado.cod_nota,
                      nota_liquidacao_item_anulado.timestamp,
                      nota_liquidacao_item_anulado.vl_anulado";

    return $stSql;
}

}

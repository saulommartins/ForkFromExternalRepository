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
    * Data de Criação: 12/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63481 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAnulado.class.php" );

class TTBAEmpenhoAnulado extends TEmpenhoEmpenhoAnulado
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::TEmpenhoEmpenhoAnulado();
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql = "
            SELECT 1 AS tipo_registro
                 , ".$this->getDado('inCodGestora')." AS unidade_gestora
                 , despesa.num_unidade AS unidade_orcamentaria
                 , empenho.cod_empenho AS num_empenho
                 , despesa.exercicio     
                 , ROW_NUMBER () OVER (PARTITION BY empenho_anulado.cod_empenho ORDER BY empenho_anulado.cod_empenho) AS numero_empenho_anulado
                 , to_char(empenho_anulado.timestamp,'ddmmyyyy') AS data_anulacao
                 , empenho_anulado.motivo
                 , sume.valor_anulado                                                 
                 , CASE WHEN liquidadas.cod_entidade IS NOT NULL
                        THEN 1
                        ELSE 2
                   END AS despesa_liquidada  
                 , to_char(empenho_anulado.timestamp,'yyyymm') AS competencia
                 , despesa.num_orgao
       
             FROM empenho.empenho
       
       INNER JOIN empenho.pre_empenho
               ON empenho.exercicio       = pre_empenho.exercicio                                 
              AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
       
       INNER JOIN empenho.pre_empenho_despesa
               ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio                                 
              AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
       
       INNER JOIN orcamento.despesa
               ON pre_empenho_despesa.exercicio   = despesa.exercicio                                 
              AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
       
       INNER JOIN orcamento.conta_despesa
               ON pre_empenho_despesa.exercicio = conta_despesa.exercicio                                 
              AND pre_empenho_despesa.cod_conta = conta_despesa.cod_conta                                 
       
       INNER JOIN empenho.empenho_anulado 
               ON empenho.exercicio       = empenho_anulado.exercicio                                 
              AND empenho.cod_entidade    = empenho_anulado.cod_entidade                              
              AND empenho.cod_empenho     = empenho_anulado.cod_empenho
       
        INNER JOIN ( SELECT exercicio                                              
                          , cod_entidade                                           
                          , cod_empenho                                            
                          , timestamp                                              
                          , sum(vl_anulado) as valor_anulado                       
                       FROM empenho.empenho_anulado_item
                      WHERE TO_CHAR(timestamp,'yyyy') = '".$this->getDado('stExercicio')."' \n";

    if ( $this->getDado('stEntidades') ) {
        $stSql .= "      AND cod_entidade IN ( ".$this->getDado('stEntidades')." ) \n";
    }

        $stSql .= " GROUP BY exercicio
                           , cod_entidade
                           , cod_empenho
                           , timestamp        
               ) AS sume 
              ON empenho_anulado.exercicio    = sume.exercicio                                
             AND empenho_anulado.cod_entidade = sume.cod_entidade                             
             AND empenho_anulado.cod_empenho  = sume.cod_empenho                              
             AND empenho_anulado.timestamp    = sume.timestamp
             
       LEFT JOIN ( SELECT exercicio_empenho                                    
                        , cod_entidade                                         
                        , cod_empenho                                          
                     FROM empenho.nota_liquidacao
                    WHERE exercicio = '".$this->getDado('stExercicio')."' \n";
    
    if ( $this->getDado('stEntidades') ) {
        $stSql .= "  AND cod_entidade IN ( ".$this->getDado('stEntidades')." ) \n";
    }
    
    $stSql .= "  GROUP BY exercicio_empenho
                        , cod_entidade
                        , cod_empenho         
               ) AS liquidadas
              ON empenho.exercicio    = liquidadas.exercicio_empenho                  
             AND empenho.cod_entidade = liquidadas.cod_entidade                       
             AND empenho.cod_empenho  = liquidadas.cod_empenho                        
      
           WHERE despesa.exercicio    = '".$this->getDado('stExercicio')."'  \n";
            
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND empenho.cod_entidade IN ( ".$this->getDado('stEntidades')." )   \n";
    }    
            
    $stSql .= " AND TO_DATE(empenho_anulado.timestamp::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                                 AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
       
       ORDER BY despesa.exercicio
              , empenho.cod_empenho ";
  return $stSql;
}

}

?>
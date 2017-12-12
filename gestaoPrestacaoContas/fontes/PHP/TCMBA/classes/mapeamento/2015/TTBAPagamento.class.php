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
    * Data de Criação: 14/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTBAPagamento.class.php 63980 2015-11-13 17:59:02Z lisiane $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoNotaLiquidacaoPaga.class.php';

class TTBAPagamento extends TEmpenhoNotaLiquidacaoPaga
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::TEmpenhoNotaLiquidacaoPaga();
    
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaDadosTribunal()
    {   $stSql .= " SELECT 1 as tp_registro
                         , ".$this->getDado('inCodGestora')." AS unidade_gestora          
                         , CASE WHEN restos_pre_empenho.num_orgao IS NULL THEN
                                    des.num_orgao
                                ELSE
                                    restos_pre_empenho.num_orgao
                          END as cod_orgao --num_orgao
                         , CASE WHEN restos_pre_empenho.num_unidade IS NULL THEN
                                    des.num_unidade
                                ELSE
                                    restos_pre_empenho.num_unidade
                          END as cod_unidade --num_unidade
                         , emp.cod_empenho                                                
                         , to_char(pag.timestamp,'dd/mm/yyyy') as data_pagamento     
                         , liq.exercicio_empenho as exercicio        
                         , sum(vl_pago) AS vl_pago                           
                         , REPLACE(plc.cod_estrutural, '.', '') AS cod_estrutural
                         , doc.cod_tipo 
                         , doc.num_documento     
                         , plnlp.cod_ordem AS nu_processo
                         , to_char(pag.timestamp,'yyyymm') as competencia     
                         , CASE WHEN (emp.exercicio::INTEGER < to_char(pag.timestamp,'yyyy')::INTEGER) THEN 2
                            ELSE 1
                            END AS resto             
                      FROM empenho.empenho                AS emp                          
    
                INNER JOIN empenho.nota_liquidacao AS liq                          
                        ON emp.exercicio    = liq.exercicio_empenho                     
                       AND emp.cod_entidade = liq.cod_entidade                          
                       AND emp.cod_empenho  = liq.cod_empenho                           
    
                INNER JOIN empenho.nota_liquidacao_paga   AS pag                          
                        ON liq.exercicio          = pag.exercicio                             
                       AND liq.cod_entidade       = pag.cod_entidade                          
                       AND liq.cod_nota           = pag.cod_nota
                       
                INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                        ON plnlp.cod_entidade         = pag.cod_entidade
                       AND plnlp.cod_nota             = pag.cod_nota
                       AND plnlp.exercicio_liquidacao = pag.exercicio
                       AND plnlp.timestamp            = pag.timestamp
    
                INNER JOIN empenho.pre_empenho AS pre  
                        ON emp.exercicio       = pre.exercicio                             
                       AND emp.cod_pre_empenho = pre.cod_pre_empenho                       
    
                 INNER JOIN tesouraria.pagamento
                        ON pagamento.exercicio    = pag.exercicio                             
                       AND pagamento.cod_entidade = pag.cod_entidade                          
                       AND pagamento.cod_nota     = pag.cod_nota  
                       AND pagamento.timestamp    = pag.timestamp
    
                INNER JOIN empenho.nota_liquidacao_conta_pagadora as lcp                  
                        ON pag.exercicio   = lcp.exercicio_liquidacao  
                       AND pag.cod_entidade= lcp.cod_entidade          
                       AND pag.cod_nota    = lcp.cod_nota              
                       AND pag.timestamp   = lcp.timestamp
    
                LEFT JOIN empenho.restos_pre_empenho
                         ON restos_pre_empenho.cod_pre_empenho = pre.cod_pre_empenho 
                        AND restos_pre_empenho.exercicio       = pre.exercicio
    
                 LEFT JOIN ( SELECT exercicio                                          
                                  , cod_entidade                                       
                                  , cod_nota                                           
                                  , timestamp                                          
                                  , sum(vl_anulado) as vl_anulado                      
                               FROM empenho.nota_liquidacao_paga_anulada                
                              WHERE to_char(timestamp_anulada,'yyyy') = '".Sessao::getExercicio()."' 
                                AND cod_entidade in ( ".$this->getDado('stEntidades')." )   
                           GROUP BY exercicio, cod_entidade, cod_nota, timestamp       
                         ) as paa                                                        
                        ON pag.exercicio   = paa.exercicio                         
                       AND pag.cod_entidade= paa.cod_entidade                      
                       AND pag.cod_nota    = paa.cod_nota                          
                       AND pag.timestamp   = paa.timestamp
                       
                 LEFT JOIN contabilidade.plano_analitica AS pla                           
                        ON lcp.exercicio   = pla.exercicio                           
                       AND lcp.cod_plano   = pla.cod_plano
                       
                 LEFT JOIN contabilidade.plano_conta as plc                               
                        ON pla.exercicio   = plc.exercicio                           
                       AND pla.cod_conta   = plc.cod_conta                           
                
                 LEFT JOIN empenho.pre_empenho_despesa AS ped                          
                        ON pre.exercicio       = ped.exercicio                             
                       AND pre.cod_pre_empenho = ped.cod_pre_empenho                       
    
                 LEFT JOIN orcamento.despesa AS des 
                        ON ped.exercicio   = des.exercicio                             
                       AND ped.cod_despesa = des.cod_despesa
                
                 LEFT JOIN tcmba.pagamento_tipo_documento_pagamento as doc
                        ON pagamento.exercicio    = doc.exercicio                             
                       AND pagamento.cod_entidade = doc.cod_entidade                          
                       AND pagamento.cod_nota     = doc.cod_nota  
                       AND pagamento.timestamp    = doc.timestamp
    
                     WHERE pag.timestamp BETWEEN TO_DATE('".$this->getDado('dt_inicial')."' , 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                       AND emp.cod_entidade in ( ".$this->getDado('stEntidades')." )
                  
                  GROUP BY pag.timestamp
                         , cod_orgao                                                  
                         , cod_unidade                                                
                         , emp.cod_empenho                                                                     
                         , liq.exercicio_empenho
                         , plc.cod_estrutural
                         , doc.cod_tipo
                         , doc.num_documento       
                         , competencia        
                         , emp.exercicio
                         , plnlp.cod_ordem
                         
                  ORDER BY emp.cod_empenho 
                         , pag.timestamp
        ";
        return $stSql;
    }
    
    function recuperaLogErro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaLogErro().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaLogErro()
    {
        $stSql = " SELECT count(to_char(pag.timestamp,'yyyy')) registros
                        , count(doc.exercicio) as obrigatorio
                     FROM empenho.nota_liquidacao_paga      AS pag 
               INNER JOIN tesouraria.pagamento
                       ON pagamento.exercicio       = pag.exercicio
                      AND pagamento.cod_nota        = pag.cod_nota
                      AND pagamento.cod_entidade    = pag.cod_entidade
                      AND pagamento.timestamp       = pag.timestamp
                LEFT JOIN tcmba.pagamento_tipo_documento_pagamento as doc
                       ON doc.cod_entidade = pagamento.cod_entidade
                      AND doc.exercicio    = pagamento.exercicio
                      AND doc.timestamp    = pagamento.timestamp
                      AND doc.cod_nota     = pagamento.cod_nota
                    WHERE pag.timestamp BETWEEN TO_DATE('".$this->getDado('dt_inicial')."' , 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                      AND pag.cod_entidade IN (".$this->getDado('stEntidades').")
                      ";
        return $stSql;
    }

}

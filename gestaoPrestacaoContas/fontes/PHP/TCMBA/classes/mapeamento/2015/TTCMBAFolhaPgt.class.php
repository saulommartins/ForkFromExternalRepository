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
  * Página Mapeamento arquivo TCM-BA FolhaPgt
  * Data de Criação: 20/10/2015
  * @author Analista:      Valtair Santos 
  * @author Desenvolvedor: Jean da Silva
  * $Id: $
  * $Date: $
  * $Author: $
  * $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAFolhaPgt extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }    

    public function recuperaTribunal (&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaTribunal()
    {
        $stSql = "SELECT 1 as tipo_registro
                         , ".$this->getDado('unidade_gestora')." AS unidade_gestora          
                         , CASE WHEN restos_pre_empenho.num_orgao IS NULL THEN
                                    despesa.num_orgao
                                ELSE
                                    restos_pre_empenho.num_orgao
                          END AS cod_orgao --num_orgao
                         , CASE WHEN restos_pre_empenho.num_unidade IS NULL THEN
                                    despesa.num_unidade
                                ELSE
                                    restos_pre_empenho.num_unidade
                          END AS unidade_orcamentaria
                         , empenho.cod_empenho AS num_empenho
                         , empenho.cod_empenho AS subempenho
                         , TO_CHAR(nota_liquidacao_paga.timestamp,'dd/mm/yyyy') as dt_pagamento_empenho     
                         , nota_liquidacao.exercicio_empenho as exercicio        
                         , SUM(nota_liquidacao_paga.vl_pago) AS vl_pago                           
                         , pre_empenho.descricao AS objeto
                         , to_char(nota_liquidacao_paga.timestamp,'yyyymm') as competencia
                         , to_char(nota_liquidacao_paga.timestamp,'mm') as mes_referencia
                         , to_char(nota_liquidacao_paga.timestamp,'yyyy') as ano_referencia

                      FROM empenho.empenho                          
    
                INNER JOIN empenho.nota_liquidacao                          
                        ON empenho.exercicio = nota_liquidacao.exercicio_empenho                     
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade                          
                       AND empenho.cod_empenho = nota_liquidacao.cod_empenho                           
    
                INNER JOIN empenho.nota_liquidacao_paga                          
                        ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio                             
                       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade                          
                       AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota                              
    
                INNER JOIN empenho.pre_empenho  
                        ON empenho.exercicio = pre_empenho.exercicio                             
                       AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho                       
    
                 INNER JOIN tesouraria.pagamento
                        ON pagamento.exercicio = nota_liquidacao_paga.exercicio                             
                       AND pagamento.cod_entidade = nota_liquidacao_paga.cod_entidade                          
                       AND pagamento.cod_nota = nota_liquidacao_paga.cod_nota  
                       AND pagamento.timestamp = nota_liquidacao_paga.timestamp
    
                INNER JOIN empenho.nota_liquidacao_conta_pagadora                  
                        ON nota_liquidacao_paga.exercicio = nota_liquidacao_conta_pagadora.exercicio_liquidacao  
                       AND nota_liquidacao_paga.cod_entidade = nota_liquidacao_conta_pagadora.cod_entidade          
                       AND nota_liquidacao_paga.cod_nota = nota_liquidacao_conta_pagadora.cod_nota              
                       AND nota_liquidacao_paga.timestamp = nota_liquidacao_conta_pagadora.timestamp
    
                LEFT JOIN empenho.restos_pre_empenho
                         ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho 
                        AND restos_pre_empenho.exercicio = pre_empenho.exercicio
    
                 LEFT JOIN ( SELECT exercicio                                          
                                  , cod_entidade                                       
                                  , cod_nota                                           
                                  , timestamp                                          
                                  , sum(vl_anulado) as vl_anulado                      
                               FROM empenho.nota_liquidacao_paga_anulada                
                              WHERE to_char(timestamp_anulada,'yyyy') = '".Sessao::getExercicio()."' 
                                AND cod_entidade in ( ".$this->getDado('entidades')." )   
                           GROUP BY exercicio, cod_entidade, cod_nota, timestamp       
                         ) as paa                                                        
                        ON nota_liquidacao_paga.exercicio = paa.exercicio                         
                       AND nota_liquidacao_paga.cod_entidade = paa.cod_entidade                      
                       AND nota_liquidacao_paga.cod_nota = paa.cod_nota                          
                       AND nota_liquidacao_paga.timestamp = paa.timestamp
                       
                 LEFT JOIN contabilidade.plano_analitica                           
                        ON nota_liquidacao_conta_pagadora.exercicio = plano_analitica.exercicio                           
                       AND nota_liquidacao_conta_pagadora.cod_plano = plano_analitica.cod_plano
                       
                 LEFT JOIN contabilidade.plano_conta                               
                        ON plano_analitica.exercicio = plano_conta.exercicio                           
                       AND plano_analitica.cod_conta = plano_conta.cod_conta                           
                
                 LEFT JOIN empenho.pre_empenho_despesa                          
                        ON pre_empenho.exercicio = pre_empenho_despesa.exercicio                             
                       AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho                       
    
                 LEFT JOIN orcamento.despesa 
                        ON pre_empenho_despesa.exercicio = despesa.exercicio                             
                       AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                 
                 LEFT JOIN orcamento.conta_despesa
                        ON conta_despesa.exercicio = despesa.exercicio                             
                       AND conta_despesa.cod_conta = despesa.cod_conta
                
                 LEFT JOIN tcmba.pagamento_tipo_documento_pagamento
                        ON pagamento.exercicio = pagamento_tipo_documento_pagamento.exercicio                             
                       AND pagamento.cod_entidade = pagamento_tipo_documento_pagamento.cod_entidade                          
                       AND pagamento.cod_nota = pagamento_tipo_documento_pagamento.cod_nota  
                       AND pagamento.timestamp = pagamento_tipo_documento_pagamento.timestamp
    
                     WHERE nota_liquidacao_paga.timestamp BETWEEN TO_DATE('".$this->getDado('data_inicial')."' , 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('data_final')."', 'dd/mm/yyyy')
                       AND empenho.cod_entidade IN ( ".$this->getDado('entidades')." )
                       AND conta_despesa.cod_estrutural ilike '3.1.9.0%'
                  
                  GROUP BY restos_pre_empenho.num_orgao
                         , despesa.num_orgao
                         , restos_pre_empenho.num_unidade
                         , despesa.num_unidade
                         , empenho.cod_empenho
                         , nota_liquidacao_paga.timestamp
                         , nota_liquidacao.exercicio_empenho
                         , pre_empenho.descricao
        ";
        
    return $stSql;
    }

}//fim da classe
?>
 
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
    * Data de Criação: 13/03/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
    $Id: TTPBLiquidacao.class.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 13/03/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBLiquidacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBLiquidacao()
{
    parent::Persistente();
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT   pre.exercicio as exercicio_empenho   \n";
    $stSql .= "         ,lpad(num_orgao, 2, '0')||lpad(num_unidade, 2, '0') as unidade_orcamentaria   \n";
    $stSql .= "         ,emp.cod_empenho   \n";
    $stSql .= "         , liq.cod_nota   \n";
    $stSql .= "         ,liq.exercicio as exercicio_liquidacao   \n";
    $stSql .= "         ,to_char(liq.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao   \n";
    $stSql .= "         ,sum(notaItem.vl_total) as valor   \n";
    $stSql .= "         , trim(tnf.nro_nota) as nro_nota   \n";
    $stSql .= "         , trim(tnf.nro_serie) as nro_serie   \n";
    $stSql .= "         , tnf.data_emissao   \n";
    $stSql .= " FROM     empenho.pre_empenho         as pre   \n";
    $stSql .= "         ,empenho.pre_empenho_despesa as ped   \n";
    $stSql .= "         ,orcamento.despesa           as des   \n";
    $stSql .= "         ,empenho.empenho             as emp   \n";
    $stSql .= "           LEFT JOIN tcepb.nota_fiscal_empenho as tnfe   \n";
    $stSql .= "             ON tnfe.exercicio = emp.exercicio           \n";
    $stSql .= "            AND tnfe.cod_entidade = emp.cod_entidade     \n";
    $stSql .= "            AND tnfe.cod_empenho = emp.cod_empenho       \n";
    $stSql .= "           LEFT JOIN tcepb.nota_fiscal as tnf            \n";
    $stSql .= "             ON tnf.cod_nota = tnfe.cod_nota             \n";

    $stSql .= "         ,empenho.nota_liquidacao     as liq   \n";
    $stSql .= "         ,empenho.nota_liquidacao_item     as notaItem   \n";
    $stSql .= " WHERE   pre.exercicio       = ped.exercicio                                         \n";
    $stSql .= " AND     pre.cod_pre_empenho = ped.cod_pre_empenho                                   \n";
    $stSql .= " AND     ped.exercicio       = des.exercicio                                         \n";
    $stSql .= " AND     ped.cod_despesa     = des.cod_despesa                                       \n";
    $stSql .= " AND     pre.exercicio       = emp.exercicio                                         \n";
    $stSql .= " AND     pre.cod_pre_empenho = emp.cod_pre_empenho                                   \n";
    $stSql .= " AND     emp.exercicio       = liq.exercicio_empenho                                 \n";
    $stSql .= " AND     emp.cod_entidade    = liq.cod_entidade                                      \n";
    $stSql .= " AND     emp.cod_empenho     = liq.cod_empenho                                       \n";
    $stSql .= " AND     liq.exercicio       = notaItem.exercicio                                    \n";
    $stSql .= " AND     liq.cod_entidade    = notaItem.cod_entidade                                 \n";
    $stSql .= " AND     liq.cod_nota        = notaItem.cod_nota                                     \n";
    $stSql .= " AND     liq.exercicio       = '".$this->getDado('exercicio')."'                     \n";
    $stSql .= " AND     to_char(liq.dt_liquidacao, 'mm')       = '".$this->getDado('inMes')."'      \n";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND liq.cod_entidade in ( ".$this->getDado('stEntidades')." )                   \n";
    }
    $stSql .= " GROUP BY notaItem.exercicio , exercicio_item, notaItem.cod_entidade
                       ,  notaItem.cod_pre_empenho, dt_liquidacao, pre.exercicio
                       ,  num_orgao, num_unidade, liq.cod_empenho, emp.cod_empenho
                       ,  liq.exercicio
                      ,  liq.cod_nota
                      , tnf.nro_nota
                      , tnf.nro_serie
                      , tnf.data_emissao   \n";
    $stSql .= " ORDER BY pre.exercicio                                                              \n";
    $stSql .= "       ,  num_orgao                                                                  \n";
    $stSql .= "       ,  num_unidade                                                                \n";
    $stSql .= "       ,  emp.cod_empenho                                                            \n";
    $stSql .= "       ,  liq.exercicio                                                              \n";
    $stSql .= "       ,  liq.dt_liquidacao                                                          \n";

    return $stSql;
}

function recuperaTodos2009(&$rsRecordSet,$stCondicao = "",$stOrdem = "",$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTodos2009().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTodos2009()
{
   
    $stSql = "
            SELECT pre_empenho.exercicio as exercicio_empenho   
                  , LPAD(num_orgao::varchar, 2, '0')||lpad(num_unidade::varchar, 2, '0') AS unidade_orcamentaria   
                  , empenho.cod_empenho   
                  , nota_liquidacao.cod_nota   
                  , nota_liquidacao.exercicio AS exercicio_liquidacao   
                  , to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') AS dt_liquidacao   
                  , sum(nota_liquidacao_item.vl_total) AS valor   
                  , nota_fiscal.nro_nota   
                  , nota_fiscal.nro_serie   
                  , TO_CHAR(nota_fiscal.data_emissao,'dd/mm/yyyy') AS data_emissao   
             
               FROM empenho.pre_empenho
        
         INNER JOIN empenho.pre_empenho_despesa
                 ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio                                         
                AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                  
         INNER JOIN orcamento.despesa
                 ON pre_empenho_despesa.exercicio   = despesa.exercicio                                         
                AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa
         
         INNER JOIN empenho.empenho
                 ON pre_empenho.exercicio       =  empenho.exercicio                                         
                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
         
         INNER JOIN empenho.nota_liquidacao
                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho                                 
                AND empenho.cod_entidade = nota_liquidacao.cod_entidade                                      
                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                
          LEFT JOIN tcepb.nota_fiscal
                 ON nota_fiscal.exercicio           = nota_liquidacao.exercicio           
                AND nota_fiscal.cod_entidade        = nota_liquidacao.cod_entidade     
                AND nota_fiscal.cod_nota_liquidacao = nota_liquidacao.cod_nota    
         
         INNER JOIN empenho.nota_liquidacao_item
                 ON nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio                                    
                AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade                                 
                AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota                                     
                
              WHERE TO_CHAR(nota_liquidacao.dt_liquidacao, 'mm') = '".$this->getDado('inMes')."'
                AND nota_liquidacao.exercicio    = '".$this->getDado('exercicio')."' \n";
                
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND nota_liquidacao.cod_entidade  in ( ".$this->getDado('stEntidades')." ) \n";
    }      

    $stSql .= "        
           GROUP BY nota_liquidacao_item.exercicio
                  , exercicio_item
                  , nota_liquidacao_item.cod_entidade
                  , nota_liquidacao_item.cod_pre_empenho
                  , dt_liquidacao
                  , pre_empenho.exercicio
                  , num_orgao
                  , num_unidade
                  , nota_liquidacao.cod_empenho
                  , empenho.cod_empenho
                  , nota_liquidacao.exercicio
                  , nota_liquidacao.cod_nota
                  , nota_fiscal.nro_nota
                  , nota_fiscal.nro_serie
                  , nota_fiscal.data_emissao
                  
           ORDER BY pre_empenho.exercicio                                                              
                  , num_orgao                                                                  
                  , num_unidade                                                                
                  , empenho.cod_empenho                                                            
                  , nota_liquidacao.exercicio                                                              
                  , nota_liquidacao.dt_liquidacao ";
                      
    return $stSql;
}

}

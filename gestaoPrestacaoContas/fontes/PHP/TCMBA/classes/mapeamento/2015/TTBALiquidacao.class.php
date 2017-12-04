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
    * Data de Criação: 13/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63468 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/07/16 02:39:13  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php" );

/**
  *
  * Data de Criação: 13/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBALiquidacao extends TEmpenhoNotaLiquidacao
{
/**
    * Método Construtor
    * @access Private
*/
function TTBALiquidacao()
{
    parent::TEmpenhoNotaLiquidacao();

    $this->setDado('exercicio', Sessao::getExercicio() );
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
    $stSql .= " SELECT 1 AS tipo_registro
                     , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                     , despesa.num_unidade AS unidade_orcamentaria
                     , empenho.cod_empenho
                     , TO_CHAR(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') AS data_liquidacao
                     , ( COALESCE(SUM(sum.valor),0.00) - COALESCE(SUM(sumanu.valor),0.00) ) AS saldo
                     , nota_liquidacao.exercicio
                     , ".$this->getDado('exercicio')."::VARCHAR||LPAD(".$this->getDado('mes')."::VARCHAR,2,'0') AS competencia
                     , despesa.num_orgao
                     , '' AS reservado_tcm

                  FROM empenho.empenho

            INNER JOIN empenho.nota_liquidacao
                    ON empenho.exercicio = nota_liquidacao.exercicio_empenho         
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade              
                   AND empenho.cod_empenho = nota_liquidacao.cod_empenho

             LEFT JOIN ( SELECT exercicio                              
                               ,cod_entidade                           
                               ,cod_nota                               
                               ,sum(vl_anulado) as valor
                           FROM empenho.nota_liquidacao_item_anulado as nli        
                          WHERE exercicio = '".$this->getDado('exercicio')."'         
                            AND cod_entidade IN ( ".$this->getDado('entidades')." )   
                       GROUP BY exercicio, cod_entidade, cod_nota      
                        ) AS sumanu                                         
                    ON nota_liquidacao.exercicio = sumanu.exercicio             
                   AND nota_liquidacao.cod_entidade = sumanu.cod_entidade          
                   AND nota_liquidacao.cod_nota = sumanu.cod_nota

            INNER JOIN empenho.pre_empenho
                    ON empenho.exercicio = pre_empenho.exercicio                 
                   AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

            INNER JOIN empenho.pre_empenho_despesa
                    ON pre_empenho.exercicio = pre_empenho_despesa.exercicio                 
                   AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

            INNER JOIN orcamento.conta_despesa
                    ON pre_empenho_despesa.exercicio = conta_despesa.exercicio                 
                   AND pre_empenho_despesa.cod_conta = conta_despesa.cod_conta

            INNER JOIN orcamento.despesa
                    ON pre_empenho_despesa.exercicio = despesa.exercicio                 
                   AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa

            INNER JOIN ( SELECT exercicio                              
                               ,cod_entidade                           
                               ,cod_nota                               
                               ,sum(vl_total) as valor                 
                          FROM empenho.nota_liquidacao_item as nli     
                         WHERE exercicio = '".$this->getDado('exercicio')."'         
                           AND cod_entidade IN ( ".$this->getDado('entidades')." )   
                      GROUP BY exercicio, cod_entidade, cod_nota      
                        ) AS sum
                    ON nota_liquidacao.exercicio = sum.exercicio                 
                   AND nota_liquidacao.cod_entidade = sum.cod_entidade              
                   AND nota_liquidacao.cod_nota = sum.cod_nota 

                 WHERE nota_liquidacao.exercicio = '".$this->getDado('exercicio')."'             
                   AND nota_liquidacao.cod_entidade IN ( ".$this->getDado('entidades')." )   
                   AND nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

              GROUP BY  nota_liquidacao.exercicio                                     
                        ,despesa.num_orgao                                      
                        ,despesa.num_unidade                                    
                        ,empenho.cod_empenho                                    
                        ,nota_liquidacao.dt_liquidacao       

              ORDER BY  nota_liquidacao.exercicio                                     
                        ,despesa.num_orgao                                      
                        ,despesa.num_unidade                                    
                        ,empenho.cod_empenho                                    
                        ,nota_liquidacao.dt_liquidacao          
        ";                        
    return $stSql;
}

}

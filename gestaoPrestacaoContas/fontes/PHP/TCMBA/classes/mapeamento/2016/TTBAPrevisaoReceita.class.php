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
    * Data de Criação: 09/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62937 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php" );

class TTBAPrevisaoReceita extends TOrcamentoPrevisaoReceita
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAPrevisaoReceita()
{
    parent::TOrcamentoPrevisaoReceita();
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
              , conta_receita.exercicio      
              , ".$this->getDado('inCodGestora')." AS unidade_gestora
              , REPLACE(conta_receita.cod_estrutural,'.','') AS item_receita    
              , REPLACE(SUM(previsao_receita.vl_periodo)::VARCHAR, '.', '') AS valor_receita                          
         FROM orcamento.conta_receita
             
   INNER JOIN orcamento.receita
           ON conta_receita.exercicio    = receita.exercicio                      
          AND conta_receita.cod_conta    = receita.cod_conta                      
   
   INNER JOIN orcamento.previsao_receita
           ON receita.exercicio    = previsao_receita.exercicio                      
          AND receita.cod_receita  = previsao_receita.cod_receita                    
  
        WHERE conta_receita.exercicio = '".$this->getDado('stExercicio')."'      
     
     GROUP BY conta_receita.exercicio
            , conta_receita.cod_estrutural                    
     
     ORDER BY conta_receita.exercicio
            , conta_receita.cod_estrutural ";

    return $stSql;
}

}

?>
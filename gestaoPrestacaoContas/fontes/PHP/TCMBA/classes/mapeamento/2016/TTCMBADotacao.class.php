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
    * Data de Criação: 05/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62944 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoDespesa.class.php" );

class TTCMBADotacao extends TOrcamentoDespesa
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::__construct();
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
             , REPLACE(conta_despesa.cod_estrutural,'.','') AS item_despesa
             , despesa.num_unidade AS unidade_orcamentaria
             , despesa.exercicio                                                     
             , orcamento.fn_consulta_tipo_pao(despesa.exercicio, despesa.num_pao) AS tipo_projeto
             , acao.num_acao AS num_projeto
             , despesa.cod_recurso AS fonte_recurso                                                  
             , despesa.cod_funcao
             , despesa.cod_subfuncao
             , programa.num_programa
             , REPLACE(despesa.vl_original::VARCHAR, '.', '') AS vl_dotacao
             , despesa.num_orgao

           FROM orcamento.despesa
             
     INNER JOIN orcamento.conta_despesa
             ON despesa.exercicio = conta_despesa.exercicio                                     
            AND despesa.cod_conta = conta_despesa.cod_conta
            
     INNER JOIN orcamento.pao
             ON pao.exercicio = despesa.exercicio
            AND pao.num_pao   = despesa.num_pao

     INNER JOIN orcamento.pao_ppa_acao
             ON pao_ppa_acao.exercicio = pao.exercicio
            AND pao_ppa_acao.num_pao   = pao.num_pao

     INNER JOIN ppa.acao
             ON acao.cod_acao = pao_ppa_acao.cod_acao

     INNER JOIN ppa.programa
             ON programa.cod_programa = acao.cod_programa
        
          WHERE despesa.exercicio    = '".$this->getDado('stExercicio')."'
            AND despesa.cod_entidade = ".$this->getDado('stEntidades')."
     
      ORDER BY despesa.exercicio                                                    
             , despesa.num_orgao                                                     
             , despesa.num_unidade                                                   
             , despesa.cod_funcao                                                    
             , despesa.cod_subfuncao                                                 
             , despesa.cod_programa                                                  
             , item_despesa
             , despesa.cod_recurso ";
    
    return $stSql;
}

}

?>
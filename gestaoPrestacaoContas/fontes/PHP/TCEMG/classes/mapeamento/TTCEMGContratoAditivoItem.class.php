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
	* Classe de mapeamento da tabela tcemg.contrato_aditivo_item
	* Data de Criação   : 28/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGContratoAditivoItem.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGContratoAditivoItem extends Persistente
{
/**
	* Método Construtor
	* @access Private
*/
function TTCEMGContratoAditivoItem()
{
    parent::Persistente();
    $this->setTabela('tcemg.contrato_aditivo_item');
    
    $this->setCampoCod('cod_contrato_aditivo');
    $this->setComplementoChave('cod_contrato_aditivo_item,exercicio,cod_entidade,num_item');
    
    $this->AddCampo( 'cod_contrato_aditivo_item', 'integer' , true  , ''    , true  , false );
    $this->AddCampo( 'cod_contrato_aditivo'     , 'integer' , true  , ''    , true  , true  );
    $this->AddCampo( 'exercicio'                , 'char'    , true  , '4'   , true  , true  );
    $this->AddCampo( 'cod_entidade'             , 'integer' , true  , ''    , true  , true  );
    $this->AddCampo( 'cod_empenho'              , 'integer' , true  , ''    , false , true  );
    $this->AddCampo( 'exercicio_empenho'        , 'char'    , true  , '4'   , false , true  );
    $this->AddCampo( 'cod_pre_empenho'          , 'integer' , true  , ''    , false , true  );
    $this->AddCampo( 'exercicio_pre_empenho'    , 'char'    , true  , '4'   , false , true  );
    $this->AddCampo( 'num_item'                 , 'integer' , true  , ''    , false , true  );
    $this->AddCampo( 'quantidade'               , 'numeric' , true  , '14,4', false , false );
    $this->AddCampo( 'tipo_acresc_decresc'      , 'integer' , false , ''    , false , false );
}

function recuperaProximoContratoAditivoItem(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stSql = $this->montaRecuperaProximoContratoAditivoItem();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
    return $obErro;
}

function montaRecuperaProximoContratoAditivoItem()
{
    $stSql  = " SELECT max(cod_contrato_aditivo_item) + 1 as cod_contrato_aditivo_item  \n";
    $stSql .= " FROM tcemg.contrato_aditivo_item                           	        \n";
    
    return $stSql;
}

public function __destruct(){}

}
?>

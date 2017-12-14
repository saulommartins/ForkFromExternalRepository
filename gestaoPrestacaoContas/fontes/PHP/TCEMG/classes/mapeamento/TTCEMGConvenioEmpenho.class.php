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
	* Classe de mapeamento da tabela tcemg.convenio_empenho
	* Data de Criação   : 11/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGConvenioEmpenho.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGConvenioEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEMGConvenioEmpenho()
{
	parent::Persistente();
	$this->setTabela('tcemg'.Sessao::getEntidade().'.convenio_empenho');

	$this->setCampoCod('cod_convenio');
	$this->setComplementoChave('cod_entidade, exercicio, cod_empenho');

	$this->AddCampo( 'cod_convenio'         , 'integer' , true  , ''    , true , true);
	$this->AddCampo( 'cod_entidade'         , 'integer' , true  , ''    , true , true);
	$this->AddCampo( 'exercicio'            , 'char'    , true  , '4'   , true , true);
	$this->AddCampo( 'cod_empenho'          , 'integer' , true  , ''    , true , true);
	$this->AddCampo( 'exercicio_empenho'    , 'char'    , true  , '4'   , true , true);
}

function recuperaConvenioEmpenho(&$rsRecordSet, $stFiltro = "")
{
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;

	$stSql = $this->montaConvenioEmpenho().$stFiltro;
	$this->setDebug( $stSql );
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

	return $obErro;
}

function montaConvenioEmpenho()
{
    $stSql  = " SELECT CE.cod_convenio, CE.cod_entidade, CE.exercicio, CE.cod_empenho, CE.exercicio_empenho, sw_cgm.nom_cgm     \n";
    $stSql .= " FROM tcemg.convenio_empenho AS CE                                                                               \n";
    $stSql .= " INNER JOIN empenho.empenho                                                                                      \n";
    $stSql .= " ON empenho.exercicio=CE.exercicio                                                                               \n";
    $stSql .= " AND empenho.cod_empenho=CE.cod_empenho                                                                          \n";
    $stSql .= " AND empenho.cod_entidade=CE.cod_entidade                                                                        \n";
    $stSql .= " INNER JOIN empenho.pre_empenho                                                                                  \n";
    $stSql .= " ON pre_empenho.cod_pre_empenho=empenho.cod_pre_empenho                                                          \n";
    $stSql .= " AND pre_empenho.exercicio=empenho.exercicio                                                                     \n";
    $stSql .= " INNER JOIN sw_cgm                                                                                               \n";
    $stSql .= " ON sw_cgm.numcgm=pre_empenho.cgm_beneficiario                                                                   \n";
    
    return $stSql;
}

public function __destruct(){}

}
?>

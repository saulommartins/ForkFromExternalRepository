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
	* Classe de mapeamento da tabela tcemg.convenio_participante
	* Data de Criação   : 11/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGConvenioParticipante.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGConvenioParticipante extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEMGConvenioParticipante()
{
    parent::Persistente();
    $this->setTabela('tcemg'.Sessao::getEntidade().'.convenio_participante');
    
    $this->setCampoCod('cod_convenio');
    $this->setComplementoChave('exercicio , cod_entidade , cgm_participante');
    
    $this->AddCampo( 'cod_convenio'                 , 'integer' , true  , ''    , true  , true  );
    $this->AddCampo( 'cod_entidade'                 , 'integer' , true  , ''    , true  , true  );
    $this->AddCampo( 'exercicio'                    , 'char'    , true  , '4'   , true  , true  );
    $this->AddCampo( 'vl_concedido'                 , 'numeric' , true  , '14,2', false , false );
    $this->AddCampo( 'percentual'                   , 'numeric' , true  , '5,2' , false , false );
    $this->AddCampo( 'cod_tipo_participante'        , 'integer' , true  , ''    , false , true  );
    $this->AddCampo( 'cgm_participante'             , 'integer' , true  , ''    , true  , true  );
    $this->AddCampo( 'esfera'                       , 'char'    , true  , '10'  , false , false );
}

function recuperaParticipante(&$rsRecordSet, $stFiltro = "")
{
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;

	$stSql = $this->montaParticipante().$stFiltro;
	$this->setDebug( $stSql );
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

	return $obErro;
}

function montaParticipante()
{
    $stSql  = " SELECT CP.vl_concedido, CP.percentual, CP.cod_tipo_participante, CP.esfera,                 \n";
    $stSql .= " CP.cgm_participante ,sw_cgm.nom_cgm, tipo_participante.descricao AS descricao_participacao  \n";
    $stSql .= " FROM tcemg.convenio_participante AS CP                                                      \n";
    $stSql .= " INNER JOIN sw_cgm                                                                           \n";
    $stSql .= " ON sw_cgm.numcgm = CP.cgm_participante                                                      \n";
    $stSql .= " INNER JOIN licitacao.tipo_participante                                                      \n";
    $stSql .= " ON tipo_participante.cod_tipo_participante=CP.cod_tipo_participante                         \n";
    
    return $stSql;
}

public function __destruct(){}

}
?>

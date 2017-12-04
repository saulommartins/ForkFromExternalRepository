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
* Página Oculta de Procura de Orgão
* Data de Criação: 21/10/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TOrgao.class.php");

$stCampoCod  = $_GET['stNomCampoCod'];
$stCampoDesc = $_GET['stIdCampoDesc'];
$inCodigo    = $_REQUEST[ 'inCodigo' ];

switch ($_REQUEST["stCtrl"]) {
    case "buscaOrgao":
        $obTOrgao 	= new TOrgao();
        $rsOrgao	  	= new RecordSet();
        $obTOrgao->setDado('cod_orgao', $_REQUEST['inCodigo']);

        $obTOrgao->recuperaPorChave($rsOrgao);

        if ($rsOrgao->inNumLinhas > 0) {
            $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '".$rsOrgao->getCampo('nom_orgao')."';";
        } else {
            $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $stJs.= "alertaAviso('@Código do Programa (". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
        }
        echo $stJs;
    break;
}

?>

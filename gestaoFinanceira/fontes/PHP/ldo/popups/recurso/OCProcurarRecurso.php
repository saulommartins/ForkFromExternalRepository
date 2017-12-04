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
* Arquivo de popup de busca de Acaos
* Data de Criação: 02/12/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Jânio Eduardo Vasconcellos de Magalhães
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_LDO_VISAO      . 'VLDOManterReceita.class.php';

$inCodReceita 	= $_GET['inCodReceita'];
$inCodRecurso 	= $_GET['inNumRecurso'];
$stCampoCod 	= $_GET['stNomCampoCod'];
$stCampoDesc	= $_GET['stIdCampoDesc'];

switch ($_REQUEST["stCtrl"]) {
    case "listaRecurso":
        if ($inCodRecurso) {
           $param['inCodRecurso'] = $inCodRecurso;
           //busca de referencia da recurso
            $_REQUEST['inNumReceita'] = $inCodReceita;
            $rsRecurso = VLDOManterReceita::recuperarInstancia()->recuperarRecurso($_REQUEST);

            if ($rsRecurso->inNumLinhas > 0) {
                $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '".$rsRecurso->getCampo('nom_recurso')."';\n";

            } else {
                $stJs = "document.getElementById('".$stCampoCod."').value = '';";
                $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
                $stJs .= "document.getElementById('".$stCampoCod."').focus();";
                $stJs.= "alertaAviso('@Código da Recurso (". $inCodRecurso .") não encontrado.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs = "document.getElementById('".$stCampoCod."').value = '';";
            $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";

        }
        echo $stJs;

    break;
}

?>

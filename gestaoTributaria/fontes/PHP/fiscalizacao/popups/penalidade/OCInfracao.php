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
 * Página de Processamento Oculto para popup de Infracao
 * Data de Criação: 11/08/2008

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Fellipe Esteves dos Santos
 * @ignore

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_NEGOCIO . "RFISInfracao.class.php" );
include_once( CAM_GT_FIS_VISAO   . 'VFISInfracao.class.php' );

//Define o nome dos arquivos PHP
$stPrograma 	= "Infracao";
$pgFilt 		= "FL" . $stPrograma . ".php";
$pgList 		= "LS" . $stPrograma . ".php";
$pgForm 		= "FM" . $stPrograma . ".php";
$pgOcul 		= "OC" . $stPrograma . ".php";
$pgJS   		= "JS" . $stPrograma . ".js";

switch ($_REQUEST["stCtrl"]) {
    case "buscaInfracao":
        $obRFISInfracao = new RFISInfracao();
        $obVFISInfracao = new VFISInfracao( $obRFISInfracao );
        $obInfracao = $obVFISInfracao->getInfracao($_REQUEST['inCodInfracao']);

        if ( $obInfracao->getNumLinhas() > 0) {
            $js  = "window.document.getElementById('".$_REQUEST['CampoNom']."').innerHTML = '" . $obInfracao->getCampo( 'nom_infracao' ) . "';";
            $js .= "window.document.frm.".$_REQUEST['CampoNum'].".value = '" . $obInfracao->getCampo( 'cod_infracao' ) . "';";
        } else {
            $js  = "window.document.frm.".$_REQUEST['CampoNum'].".value = '';";
            $js .= "window.document.getElementById('".$_REQUEST['CampoNom']."').innerHTML = '&nbsp;';";
            $js .= "alertaAviso('@Código da Infracao inválido (" . $_REQUEST['inCodInfracao'] . ").','form','erro','" . Sessao::getId() . "');";
        }
            echo $js;
        break;
}

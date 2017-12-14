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
    * Página de Processamento Oculto para popup de FISCALIZACAO.FISCAL
    * Data de Criação   : 25/07/2008

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Bruno Ferreira
    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO."TFISFiscal.class.php" 				                   );

//Define o nome dos arquivos PHP
$stPrograma = "Fiscal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($_REQUEST["stCtrl"]) {
    case "buscaFiscal":
        $obFiscal = new TFISFiscal();
        $rsFiscal = new RecordSet();
        $obFiscal->recuperaListaFiscal($rsFiscal,"WHERE fc.cod_fiscal = ".$_GET['inFiscal'],null,false);
    if (!($rsFiscal->Eof())) {
            $js = "window.document.getElementById('stFiscal').innerHTML = '".$rsFiscal->getCampo('nome')."';";
        } else {
            $js = "window.document.frm.inFiscal.value = '';";
            $js.= "window.document.getElementById('stFiscal').innerHTML = '&nbsp;';";
            $js.= "alertaAviso('@Código do Fiscal inválido (".$_GET['inFiscal'].").','form','erro','".Sessao::getId()."');";
        }
        echo $js;

    break;

}
die();

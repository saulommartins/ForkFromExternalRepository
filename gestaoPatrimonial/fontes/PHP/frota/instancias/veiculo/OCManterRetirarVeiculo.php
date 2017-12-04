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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaInfracao.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInfracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'validaMotorista':
        validaMotorista();
    break;
}

function validaMotorista()
{
    if ($_REQUEST['inCodMotorista'] != '') {
        $obTFrotaInfracao = new TFrotaInfracao();
        $obTFrotaInfracao->setDado('cgm_motorista', $_REQUEST['inCodMotorista']);
        $obTFrotaInfracao->recuperaPontosMotorista($rsPontosMotorista);

        if ($rsPontosMotorista->getCampo('pontos') >= 20) {
            $stJs = "alertaAviso('Este motorista está inabilitado a retirar o veículo, pois possui mais de 20 pontos em infrações.','form','erro','".Sessao::getId()."');";
            $stJs.= "d.getElementById('inCodMotorista').value = '';";
            $stJs.= "d.getElementById('stNomMotorista').innerHTML = '&nbsp;';";

            echo $stJs;
        }
    }
}

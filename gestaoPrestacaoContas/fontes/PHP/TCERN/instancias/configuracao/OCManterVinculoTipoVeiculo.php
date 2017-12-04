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

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoTipoVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {

    case "carregaSelectsForm":
        include_once CAM_GP_FRO_MAPEAMENTO."TFrotaTipoVeiculo.class.php";
        include_once CAM_GPC_TCERN_MAPEAMENTO."TTCERNTipoVeiculoVinculo.class.php";
        include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNEspecieVeiculoTCE.class.php');
        include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNTipoVeiculoTCE.class.php');

        //Lista os Tipos de Veículos cadastrados no Sistema
        $obTFrotaTipoVeiculo = new TFrotaTipoVeiculo;
        $obTFrotaTipoVeiculo->recuperaVinculoTipoVeiculoTCERN ($rsVinculo, '', ' ORDER BY cod_tipo');

        while (!$rsVinculo->EOF()) {

            if ($rsVinculo->getCampo('cod_especie_tce')) {
                $obTTCERNTipoVeiculoVinculoTCE = new TTCERNEspecieVeiculoTCE();
                $obTTCERNTipoVeiculoVinculoTCE->recuperaTodos($rsEspecieVeiculo, ' WHERE cod_especie_tce = '.$rsVinculo->getCampo('cod_especie_tce'));

                $obTTCERNTipoVeiculoTCE = new TTCERNTipoVeiculoTCE();
                $obTTCERNTipoVeiculoTCE->recuperaTodos($rsTipoVeiculo, ' WHERE cod_tipo_tce = '.$rsVinculo->getCampo('cod_tipo_tce'));

                $inCountAUX = 1;

                while (!$rsEspecieVeiculo->EOF()) {
                    if ($rsVinculo->getCampo('cod_especie_tce') == $rsEspecieVeiculo->getCampo('cod_especie_tce')) {
                        $stSelected = 'selected';
                    } else {
                        $stSelected = '';
                    }

                    $stJs .= "f.inCodEspecie_".$rsVinculo->getCampo('cod_tipo')."_".$rsVinculo->getCorrente().".options[".$inCountAUX."] = ";
                    $stJs .= "new Option('".$rsEspecieVeiculo->getCampo('nom_especie_tce')."', '".$rsEspecieVeiculo->getCampo('cod_especie_tce')."', '".$stSelected."');";
                    $inCountAUX++;
                    $rsEspecieVeiculo->proximo();
                }

                $inCountAUX = 1;

                while (!$rsTipoVeiculo->EOF()) {
                    if ($rsVinculo->getCampo('cod_tipo_tce') == $rsTipoVeiculo->getCampo('cod_tipo_tce')) {
                        $stSelected = 'selected';
                    } else {
                        $stSelected = '';
                    }

                    $stJs .= "f.inCodTipo_".$rsVinculo->getCampo('cod_tipo')."_".$rsVinculo->getCorrente().".options[".$inCountAUX."] = ";
                    $stJs .= "new Option('".$rsTipoVeiculo->getCampo('nom_tipo_tce')."', '".$rsTipoVeiculo->getCampo('cod_tipo_tce')."', '".$stSelected."');";
                    $inCountAUX++;
                    $rsTipoVeiculo->proximo();
                }

                $inCountAUX++;
            }

            $rsVinculo->proximo();
        }

    break;
}

if ($stJs) {
    echo $stJs;

}

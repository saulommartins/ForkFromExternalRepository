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
    * Titulo do arquivo : Oculto do Formulário de Vínculo do Tipo de Veículo do TCE para o URBEM
    * Data de Criação   : 22/12/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCManterVinculoTipoVeiculo.php 59612 2014-09-02 12:00:51Z gelson $
*/

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
    case "montaSubtipo":
        $arTipo = explode('_', $_REQUEST['stNomTipo']);
        $stJs .= "limpaSelect(f.inCodSubtipo_".$arTipo[1]."_".$arTipo[2].",0); \n";
        $stJs .= "f.inCodSubtipo_".$arTipo[1]."_".$arTipo[2].".options[0] = new Option('Selecione','', 'selected');";

        if ($_REQUEST['inCodTipo']) {
            include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTMGSubtipoVeiculoTCE.class.php';

            $obTTMGSubtipoVeiculoTCE = new TTMGSubtipoVeiculoTCE();
            $obTTMGSubtipoVeiculoTCE->setDado('inCodTipoVeiculo', $_REQUEST['inCodTipo']);
            $obTTMGSubtipoVeiculoTCE->recuperaSubtipoVeiculoTCE( $rsSubtipoVeiculoTCE );

            $inCount = 1;
            while (!$rsSubtipoVeiculoTCE->EOF()) {
                $stJs .= "f.inCodSubtipo_".$arTipo[1]."_".$arTipo[2].".options[".$inCount."] = ";
                $stJs .= "new Option('".$rsSubtipoVeiculoTCE->getCampo('nom_subtipo_tce')."', '".$rsSubtipoVeiculoTCE->getCampo('cod_subtipo_tce')."');";
                $inCount++;
                $rsSubtipoVeiculoTCE->proximo();
            }
        }

    break;
    case "carregaSelectsForm":
        include_once CAM_GP_FRO_MAPEAMENTO."TFrotaTipoVeiculo.class.php";
        include_once(CAM_GPC_TCEMG_MAPEAMENTO.'TTMGVinculoTipoVeiculoTCE.class.php');
        include_once(CAM_GPC_TCEMG_MAPEAMENTO.'TTMGTipoVeiculoTCE.class.php');
        include_once(CAM_GPC_TCEMG_MAPEAMENTO.'TTMGSubtipoVeiculoTCE.class.php');

        //Lista os Tipos de Veículos cadastrados no Sistema
        $obTFrotaTipoVeiculo = new TFrotaTipoVeiculo;
        $obTFrotaTipoVeiculo->recuperaVinculoTipoVeiculoTCE($rsTipoVeiculo, '', 'tipo_veiculo.cod_tipo');
        while (!$rsTipoVeiculo->EOF()) {
            if ($rsTipoVeiculo->getCampo('cod_tipo_tce')) {
                $obTTMGSubtipoVeiculoTCE = new TTMGSubtipoVeiculoTCE();
                $obTTMGSubtipoVeiculoTCE->setDado('inCodTipoVeiculo', $rsTipoVeiculo->getCampo('cod_tipo_tce'));
                $obTTMGSubtipoVeiculoTCE->recuperaSubtipoVeiculoTCE($rsSubtipoVeiculoTCE);

                $inCountAUX = 1;
                while (!$rsSubtipoVeiculoTCE->EOF()) {

                    if ($rsTipoVeiculo->getCampo('cod_subtipo_tce') == $rsSubtipoVeiculoTCE->getCampo('cod_subtipo_tce')) {
                        $stJs .= "jQuery(\"#spnCodigos select[name='inCodSubtipo_".$rsTipoVeiculo->getCampo('cod_tipo')."_".$rsTipoVeiculo->getCorrente()."']\").append(jQuery(new Option(\"".$rsSubtipoVeiculoTCE->getCampo('nom_subtipo_tce')."\", \"".$rsSubtipoVeiculoTCE->getCampo('cod_subtipo_tce')."\")).attr('selected', 'selected'));";
                    } else {
                        $stJs .= "jQuery(\"#spnCodigos select[name='inCodSubtipo_".$rsTipoVeiculo->getCampo('cod_tipo')."_".$rsTipoVeiculo->getCorrente()."']\").append(jQuery(new Option(\"".$rsSubtipoVeiculoTCE->getCampo('nom_subtipo_tce')."\", \"".$rsSubtipoVeiculoTCE->getCampo('cod_subtipo_tce')."\")));";
                    }
                    
                    $inCountAUX++;
                    $rsSubtipoVeiculoTCE->proximo();
                }
            }

            $rsTipoVeiculo->proximo();
        }

    break;
}
if ($stJs) {
    echo $stJs;
}

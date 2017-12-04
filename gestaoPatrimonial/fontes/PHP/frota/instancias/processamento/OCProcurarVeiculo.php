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
    * Data de Criação: 19/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCProcurarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php");

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
        default:
            if ($_REQUEST['inCodVeiculo'] != '' AND $_REQUEST['inCodVeiculo'] > 0) {
                $obTFrotaVeiculo = new TFrotaVeiculo();
                $obTFrotaVeiculo->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
                $obTFrotaVeiculo->recuperaVeiculoSintetico( $rsVeiculo );
                if ( $rsVeiculo->getNumLinhas() > 0 ) {
                    $stJs .= "$('".$_REQUEST['stIdCampoDesc']."').innerHTML = '".$rsVeiculo->getCampo('nom_modelo')."';";
                    $stJs .= "$('".$_REQUEST['stNomCampoCod']."').value = '".$rsVeiculo->getCampo('cod_veiculo')."';";
                } else {
                    $stJs .= "$('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';";
                    $stJs .= "$('".$_REQUEST['stNomCampoCod']."').value = '';";
                    $stJs .= "alertaAviso('Código do veículo inválido.','form','erro','".Sessao::getId()."');\n";
                }
            } else {
                $stJs .= "$('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';";
                $stJs .= "$('".$_REQUEST['stNomCampoCod']."').value = '';";
            }

        break;

}

echo $stJs;

?>

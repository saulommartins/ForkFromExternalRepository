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
    * Data de Criação: 28/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_FRO_MAPEAMENTO."TFrotaItem.class.php");

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
        default:
            $stJs = isset($stJs) ? $stJs : null;
            if ($_REQUEST['inCodItem'] != '' AND $_REQUEST['inCodItem'] > 0) {
                $obTFrotaItem = new TFrotaItem();
                $stFiltro = " AND item.cod_item = ".$_REQUEST['inCodItem']." ";
                if ($_REQUEST['stTipoConsulta'] == 'sem_combustivel') {
                    $stFiltro .= " AND item.cod_tipo <> 1 ";
                }
                $obTFrotaItem->recuperaItem( $rsItem, $stFiltro );
                if ( $rsItem->getNumLinhas() > 0 ) {
                    $stJs .= "$('".$_REQUEST['stIdCampoDesc']."').innerHTML = '".$rsItem->getCampo('descricao')."';";
                    $stJs .= "$('".$_REQUEST['stNomCampoCod']."').value = '".$rsItem->getCampo('cod_item')."';";
                } else {
                    $stJs .= "$('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';";
                    $stJs .= "$('".$_REQUEST['stNomCampoCod']."').value = '';";
                    $stJs .= "alertaAviso('Código do item inválido.','form','erro','".Sessao::getId()."');\n";
                }
            } else {
                $stJs .= "$('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';";
                $stJs .= "$('".$_REQUEST['stNomCampoCod']."').value = '';";
            }

        break;

}

echo $stJs;

?>

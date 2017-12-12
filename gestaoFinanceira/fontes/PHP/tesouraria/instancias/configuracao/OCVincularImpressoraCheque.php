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
 * Formulario para o vinculo de impressora de cheques com o terminal
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaImpressoraCheque.class.php';
$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
case 'selecionaImpressora':

    $obRTesourariaImpressoraCheque = new RTesourariaImpressoraCheque();
    $obRTesourariaImpressoraCheque->obRTesourariaTerminal->stMac = $_REQUEST['stHashMac'];
    $obRTesourariaImpressoraCheque->findImpressoraTerminal($rsImpressora);

    $stJs = "
        jq('#inCodImpressora').selectOptions('" . $obRTesourariaImpressoraCheque->inCodImpressora . "');
    ";

    echo $stJs;

    break;
}
?>

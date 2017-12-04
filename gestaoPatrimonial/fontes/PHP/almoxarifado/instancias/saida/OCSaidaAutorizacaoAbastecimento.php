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
 * Página oculta de Saída por Autorização de Abastecimento.
 * Data de criação : 09/04/2009

 * @author Analista: Gelson Wolowski
 * @author Programador: Diogo Zarpelon

 * @ignore

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $request->get('stCtrl');

switch ($stCtrl) {
    case 'recuperaSaldoItem':

    $stJs = "";

    if (!empty($_REQUEST['inCodAlmoxarifado']) &&
        !empty($_REQUEST['inCodMarca'])        &&
        !empty($_REQUEST['inCodCentroCusto'])  &&
        !empty($_REQUEST['inCodItem'])){

        include_once TALM."TAlmoxarifadoEstoqueMaterial.class.php";

        $obTAlxamoxarifadoEstoqueMaterial = new TAlmoxarifadoEstoqueMaterial;

        $boUsarMarca = true;
        include_once ( TALM."TAlmoxarifadoCatalogoItemMarca.class.php" );
        $obTAlmoxarifadoCatalogoItemMarca = new TAlmoxarifadoCatalogoItemMarca;
        $stFiltro = " and acim.cod_item = ".$_REQUEST['inCodItem'];
        $stFiltro .= " and spfc.cod_almoxarifado = ".$_REQUEST['inCodAlmoxarifado'];

        $obTAlmoxarifadoCatalogoItemMarca->recuperaItemMarcaComSaldo( $rsMarcas, $stFiltro );
        $boUsarMarca = count($rsMarcas)-1;

        $stFiltro  = " AND aem.cod_centro = ".$_REQUEST['inCodCentroCusto'];
        if($boUsarMarca)
            $stFiltro .= " AND aem.cod_marca  = ".$_REQUEST['inCodMarca'];
        $stFiltro .= " AND aem.cod_item   = ".$_REQUEST['inCodItem'];
        $stFiltro .= " AND aem.cod_almoxarifado = ".$_REQUEST['inCodAlmoxarifado'];

        $obTAlxamoxarifadoEstoqueMaterial->recuperaSaldoEstoque($rsSaldo, $stFiltro);

        if ($rsSaldo->getCampo('saldo_estoque')) {
            $inSaldo = number_format($rsSaldo->getCampo('saldo_estoque'),4,',','.');
            $stJs .= "jQuery('#nuSaldoEstoque').html('".$inSaldo."');";
            $stJs .= "jQuery('#nuHdnSaldoEstoque').val('".$inSaldo."');";
        } else {
            $stJs .= "jQuery('#nuSaldoEstoque').html('0,0000');";
        }
    } else {
        $stJs .= "jQuery('#nuSaldoEstoque').html('0,0000');";
    }

    break;
}

if (!empty($stJs)) {
    echo $stJs;
}

?>

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
 * Data de Criação: 26/11/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: OCManterAutorizacao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.02.13

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/IMontaQuantidadeValores.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculo.class.php';
include_once CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivelItem.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'montaVeiculo' :
        if ( ($_REQUEST['inCodVeiculo'] != '' AND $_REQUEST['inCodVeiculo'] > 0) OR $_REQUEST['stPrefixo'] != '' OR $_REQUEST['stNumPlaca'] != '' ) {
            //recupera os dados do veículo
            $obTFrotaVeiculo = new TFrotaVeiculo();
            $obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculo->setDado( 'prefixo', $_REQUEST['stPrefixo'] );
            $obTFrotaVeiculo->setDado( 'placa', str_replace('-','',$_REQUEST['stNumPlaca']) );
            $obTFrotaVeiculo->recuperaVeiculoSintetico( $rsVeiculo );

            $stJs .= "$('inCodVeiculo').value = '".$rsVeiculo->getCampo('cod_veiculo')."';";
            $stJs .= "$('stNomVeiculo').innerHTML = '".$rsVeiculo->getCampo('nom_modelo')."';";
            $stJs .= "$('stPrefixo').value = '".$rsVeiculo->getCampo('prefixo')."';";

            if ( $rsVeiculo->getCampo('placa_masc') != '-' ) {
                $stJs .= "$('stNumPlaca').value = '".$rsVeiculo->getCampo('placa_masc')."';";
            } else {
                 $stJs .= "$('stNumPlaca').value = '';";
            }

            $obTFrotaCombustivelItem = new TFrotaCombustivelItem();
            $obTFrotaCombustivelItem->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaCombustivelItem->recuperaRelacionamento( $rsCombustivel );

            $inCount = 1;
            $stJs .= "limpaSelect($('slCombustivel'),1);";
            while ( !$rsCombustivel->eof() ) {
                $stJs .= "jQuery('#slCombustivel').append(new Option('".trim($rsCombustivel->getCampo('descricao_resumida'))."','".$rsCombustivel->getCampo('cod_item')."'));";
                $rsCombustivel->proximo();
                $inCount++;
            }
        } else {
            $stJs .= "$('inCodVeiculo').value = '';";
            $stJs .= "$('stNomVeiculo').innerHTML = '&nbsp;';";
            $stJs .= "$('stPrefixo').value = '';";
            $stJs .= "$('stNumPlaca').value = '';";
        }
    break;

    case 'montaCombustivel' :
        $obTFrotaCombustivelItem = new TFrotaCombustivelItem();
        $obTFrotaCombustivelItem->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaCombustivelItem->recuperaRelacionamento( $rsCombustivel );

        $inCount = 1;
        $stJs .= "limpaSelect($('slCombustivel'),1);";
        while ( !$rsCombustivel->eof() ) {
            $stSelected = ( $_REQUEST['slCombustivel'] == $rsCombustivel->getCampo('cod_item') ) ? 'true' : '';
            $stJs .= "jQuery('#slCombustivel').append(new Option('".trim($rsCombustivel->getCampo('descricao_resumida'))."','".$rsCombustivel->getCampo('cod_item')."','true','".$stSelected."'));";
            $rsCombustivel->proximo();
            $inCount++;
        }

    break;

    case 'montaDetalhe' :

        if ($_REQUEST['boCompletar'] == '') {

            if ($_REQUEST['inQuantidade'] >0) {
                $valorUnitario = number_format($_REQUEST['inValor'] / $_REQUEST['inQuantidade'],2,',','.');
            } else {
                    $valorUnitario = number_format(0,2,',','.');
            }

            $obMontaQuantidadeValores = new IMontaQuantidadeValores();
            $obMontaQuantidadeValores->obValorUnitario->setRotulo('Valor Unitário');
            $obMontaQuantidadeValores->obValorUnitario->setNull( false );
            $obMontaQuantidadeValores->obValorUnitario->setNegativo( false );
            $obMontaQuantidadeValores->obValorUnitario->setValue( $valorUnitario );

            $obMontaQuantidadeValores->obQuantidade->setRotulo( 'Quantidade' );
            $obMontaQuantidadeValores->obQuantidade->setName('inQuantidade' );
            $obMontaQuantidadeValores->obQuantidade->setNull(false);
            $obMontaQuantidadeValores->obQuantidade->setNegativo(false);
            $obMontaQuantidadeValores->obQuantidade->setValue( number_format($_REQUEST['inQuantidade'],4,',','.') );

            $obMontaQuantidadeValores->obValorTotal->setRotulo( 'Valor Total' );
            $obMontaQuantidadeValores->obValorTotal->setNull( false );
            $obMontaQuantidadeValores->obValorTotal->setNegativo( false );
            $obMontaQuantidadeValores->obValorTotal->setName('inValor' );
            $obMontaQuantidadeValores->obValorTotal->setValue( number_format($_REQUEST['inValor'],2,',','.') );

            //instancia um formulario
            $obFormulario = new Formulario();
            $obMontaQuantidadeValores->geraFormulario($obFormulario);
            $obFormulario->montaInnerHTML();

            $stJs .= "$('spnDetalhe').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs .= "$('spnDetalhe').innerHTML = '';";
        }

    break;
}

echo $stJs;

?>

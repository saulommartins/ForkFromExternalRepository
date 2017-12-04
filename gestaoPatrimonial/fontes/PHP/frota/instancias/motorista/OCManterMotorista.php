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

    * $Id: OCManterMotorista.php 59857 2014-09-16 14:02:25Z franver $

    * Casos de uso: uc-03.02.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculo.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaMotorista.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaMotoristaVeiculo.class.php' );
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaInfracao.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterMotorista";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaVeiculos($arVeiculos)
{
    $pgOcul = "OCManterMotorista.php";

    if ( !is_array($arVeiculos) ) {
        $arVeiculos = array();
    }

    $rsVeiculos = new RecordSet();
    $rsVeiculos->preenche( $arVeiculos );

    $obTable = new Table();
    $obTable->setRecordset( $rsVeiculos );
    $obTable->setSummary( 'Lista de Veículos Autorizados' );

    $obTable->Head->addCabecalho( 'Código', 10 );
    $obTable->Head->addCabecalho( 'Marca', 35 );
    $obTable->Head->addCabecalho( 'Modelo', 35 );
    $obTable->Head->addCabecalho( 'Padrão', 10 );

    $obTable->Body->addCampo( 'cod_veiculo', 'C' );
    $obTable->Body->addCampo( 'nom_marca', 'L' );
    $obTable->Body->addCampo( 'nom_modelo', 'L' );
    $obTable->Body->addCampo( 'padrao_desc', 'C' );

    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_FRO_INSTANCIAS."motorista/".$pgOcul."?".Sessao::getId()."&inCodVeiculo=%s', 'excluirListaVeiculos' );", array( 'cod_veiculo' ) );

    $obTable->montaHTML( true );

    return "$('spnVeiculos').innerHTML = '".$obTable->getHtml()."';";
}

switch ($stCtrl) {
    case 'preencheDadosMotorista' :
        if ($_REQUEST['inCodMotorista'] != '') {
            //recupera os dados do motorista
            $obTFrotaMotorista = new TFrotaMotorista();
            $obTFrotaMotorista->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );
            $obTFrotaMotorista->recuperaPorChave( $rsMotorista );
            if ( $rsMotorista->getNumLinhas() <= 0 ) {
                //recupera os dados do cgm
                $obTFrotaMotorista->recuperaMotorista( $rsMotorista );
                //preenche os dados
                $stJs .= "$('stNumCNH').value = '".$rsMotorista->getCampo('num_cnh')."';";
                $stJs .= "$('dtValidade').value = '".$rsMotorista->getCampo('dt_validade_cnh')."';";
                if ( $rsMotorista->getCampo( 'cod_categoria_cnh' ) != '' ) {
                    $stJs .= "  for (i=0;i<$('slHabilitacao').length;i++) {
                                    if ( $('slHabilitacao').options[i].value == ".$rsMotorista->getCampo('cod_categoria_cnh').") {
                                        $('slHabilitacao').selectedIndex = i;
                                    }
                                }
                    ";
                } else {
                    $stJs .= "$('slHabilitacao').selectedIndex = 0;";
                }
                $stJs .= "$('hdnHabilitacao').value = '".$rsMotorista->getCampo('cod_categoria_cnh')."';";
            } else {
                $stJs .= "$('stNumCNH').value = '';";
                $stJs .= "$('dtValidade').value = '';";
                $stJs .= "$('slHabilitacao').selectedIndex = 0;";
            }
        } else {
            $stJs .= "$('stNumCNH').value = '';";
            $stJs .= "$('dtValidade').value = '';";
            $stJs .= "$('slHabilitacao').selectedIndex = 0;";
        }
        Sessao::write('veiculosMotorista' , array());
        $stJs .= "$('spnVeiculos').innerHTML = '';";
        break;
    case 'montaVeiculo' :
        if ( ($_REQUEST['inCodVeiculo'] != '' AND $_REQUEST['inCodVeiculo'] > 0) OR $_REQUEST['stPrefixo'] != '' OR $_REQUEST['stNumPlaca'] != '' ) {
            //recupera os dados do veículo
            $obTFrotaVeiculo = new TFrotaVeiculo();
            $obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculo->setDado( 'prefixo', $_REQUEST['stPrefixo'] );
            $obTFrotaVeiculo->setDado( 'placa', str_replace('-','',$_REQUEST['stNumPlaca']) );
            $obTFrotaVeiculo->recuperaVeiculoSintetico( $rsVeiculo );

            if ($rsVeiculo->getCampo('cod_veiculo') > 0) {
                $obLblMarca = new Label();
                $obLblMarca->setRotulo( 'Marca' );
                $obLblMarca->setValue( $rsVeiculo->getCampo( 'nom_marca' ) );

                $obLblModelo = new Label();
                $obLblModelo->setRotulo( 'Modelo' );
                $obLblModelo->setValue( $rsVeiculo->getCampo( 'nom_modelo' ) );

                $obLblTipoVeiculo = new Label();
                $obLblTipoVeiculo->setRotulo( 'Tipo de Veículo' );
                $obLblTipoVeiculo->setValue( $rsVeiculo->getCampo( 'tipo_veiculo' ) );

                //recupera os tipos de combustivel do veiculo
                $obTFrotacombustivel = new TFrotaCombustivel();
                $obTFrotacombustivel->setDado( 'cod_veiculo', $rsVeiculo->getCampo('cod_veiculo') );
                $obTFrotacombustivel->recuperaCombustivelVeiculo( $rsCombustivel );

                while ( !$rsCombustivel->eof() ) {
                    $stCombustivel .= $rsCombustivel->getCampo('nom_combustivel').', ';
                    $rsCombustivel->proximo();
                }

                $obLblTipoCombustivel = new Label();
                $obLblTipoCombustivel->setRotulo( 'Tipo de Combustível' );
                $obLblTipoCombustivel->setValue( substr($stCombustivel,0,-2) );

                $obLblTipoHabilitacao = new Label();
                $obLblTipoHabilitacao->setRotulo( 'Tipo de Habilitação' );
                $obLblTipoHabilitacao->setValue( $rsVeiculo->getCampo( 'nom_categoria' ) );

                //instancia um formulario
                $obFormulario = new Formulario();

                $obFormulario->addComponente( $obLblMarca );
                $obFormulario->addComponente( $obLblModelo );
                $obFormulario->addComponente( $obLblTipoVeiculo );
                $obFormulario->addComponente( $obLblTipoCombustivel );
                $obFormulario->addComponente( $obLblTipoHabilitacao );

                $obFormulario->montaInnerHTML();
                $stJs .= "$('spnDetalhe').innerHTML = '".$obFormulario->getHTML()."';";
                $stJs .= "$('inCodVeiculo').value = '".$rsVeiculo->getCampo('cod_veiculo')."';";
                $stJs .= "$('stPrefixo').value = '".$rsVeiculo->getCampo('prefixo')."';";
                $stJs .= "$('stNumPlaca').value = '".$rsVeiculo->getCampo('placa_masc')."';";
            }
        } else {
            $stJs .= "$('spnDetalhe').innerHTML = '';";
        }
        break;

    case 'incluirListaVeiculos' :

        $veiculosMotorista = Sessao::read('veiculosMotorista');

        //verifica se está preenchido o campo codigo do veiculo
        if ($_REQUEST['slHabilitacao'] == '') {
            $stMensagem = 'Preencha o campo categoria';
        }
        if ($_REQUEST['dtValidade'] == '') {
            $stMensagem = 'Preencha o campo Data de Validade CNH';
        } else {
            if ( implode(array_reverse(explode('/',$_REQUEST['dtValidade']))) < date('Ymd') ) {
                $stMensagem = 'A data de validade do CNH deve ser maior ou igual a data de hoje';
            }
        }
        if ($_REQUEST['stNumCNH'] == '') {
            $stMensagem = 'Preencha o campo Número CNH';
        }
        if ($_REQUEST['inCodMotorista'] == '') {
            $stMensagem = 'Preencha o campo CGM do Motorista';
        }

        if ( count( $veiculosMotorista ) > 0 ) {
            foreach ($veiculosMotorista AS $arTemp) {
                if ($arTemp['cod_veiculo'] == $_REQUEST['inCodVeiculo']) {
                    $stMensagem = 'Este veículo já está na lista.';
                }
            }
        }
        if (!$stMensagem) {
            $obTFrotaVeiculo = new TFrotaVeiculo();
            $obTFrotaVeiculo->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculo->recuperaVeiculoSintetico( $rsVeiculo );
            if ( $rsVeiculo->getNumLinhas() < 0 ) {
                $stMensagem = "Código do veículo inválido";
            } else {
                if ( $rsVeiculo->getCampo('cod_categoria') > $_REQUEST['slHabilitacao'] ) {
                    $stMensagem = 'A Carteira de Habilitação do motorista não possui a categoria adequada para esse veículo!';
                }
            }
        }
        if (!$stMensagem) {
            if ($_REQUEST['boPadrao'] == 1) {

                for ( $i = 0; $i < count( $veiculosMotorista ); $i++ ) {
                    $veiculosMotorista[$i]['padrao'] = false;
                    $veiculosMotorista[$i]['padrao_desc'] = 'Nâo';
                }
            }
            $inCount = count( $veiculosMotorista );
            $veiculosMotorista[$inCount]['cod_veiculo'] = $rsVeiculo->getCampo('cod_veiculo');
            $veiculosMotorista[$inCount]['nom_marca'] = $rsVeiculo->getCampo('nom_marca');
            $veiculosMotorista[$inCount]['nom_modelo'] = $rsVeiculo->getCampo('nom_modelo');
            $veiculosMotorista[$inCount]['padrao'] = ($_REQUEST['boPadrao'] == 1) ? true : false ;
            $veiculosMotorista[$inCount]['padrao_desc'] = ($_REQUEST['boPadrao'] == 1) ? 'Sim' : 'Não' ;
            $stJs .= "$('spnDetalhe').innerHTML = '';";
            $stJs .= "$('inCodVeiculo').value = '';";
            $stJs .= "$('stPrefixo').value = '';";
            $stJs .= "$('stNumPlaca').value = '';";
            $stJs .= "$('boPadraoNao').checked = true;";
            $stJs .= "$('boPadraoSim').checked = false;";
            $stJs .= "$('slHabilitacao').disabled = true;";

            Sessao::remove('veiculosMotorista');
            Sessao::write('veiculosMotorista' , $veiculosMotorista);
            $stJs .= montaListaVeiculos( $veiculosMotorista );
        } else {
            $stJs.= "alertaAviso('".$stMensagem.".','form','erro','".Sessao::getId()."');";
        }
        break;
    case 'excluirListaVeiculos' :
        foreach ( Sessao::read('veiculosMotorista') AS $arTemp ) {
            if ($arTemp['cod_veiculo'] != $_REQUEST['inCodVeiculo']) {
                $arAux[] = $arTemp;
            }
        }
        Sessao::write('veiculosMotorista' , $arAux);
        if ( count( Sessao::read('veiculosMotorista') ) == 0 ) {
            $stJs .= "$('slHabilitacao').disabled = false;";
        }
        $stJs .= montaListaVeiculos( Sessao::read('veiculosMotorista') );

        break;
    case 'preencheListaVeiculos' :
        $obTFrotaMotoristaVeiculo = new TFrotaMotoristaVeiculo();
        $obTFrotaMotoristaVeiculo->setDado( 'cgm_motorista', $_REQUEST['inCodMotorista'] );
        $obTFrotaMotoristaVeiculo->recuperaVeiculosMotorista( $rsVeiculo, ' ORDER BY cod_veiculo' );
        $inCount = 0;
        if ( $rsVeiculo->getNumLinhas() > 0 ) {
            $veiculosMotorista = Sessao::read('veiculosMotorista');
            while ( !$rsVeiculo->eof() ) {
                $veiculosMotorista[$inCount]['cod_veiculo'] = $rsVeiculo->getCampo('cod_veiculo');
                $veiculosMotorista[$inCount]['nom_marca'] = $rsVeiculo->getCampo('nom_marca');
                $veiculosMotorista[$inCount]['nom_modelo'] = $rsVeiculo->getCampo('nom_modelo');
                $veiculosMotorista[$inCount]['padrao'] = ($_REQUEST['boPadrao'] == 1) ? true : false ;
                $veiculosMotorista[$inCount]['padrao_desc'] = ($_REQUEST['boPadrao'] == 1) ? 'Sim' : 'Não' ;
                $inCount++;
                $rsVeiculo->proximo();
            }
            Sessao::write('veiculosMotorista' , $veiculosMotorista);
            $stJs .= montaListaVeiculos( $veiculosMotorista );
            $stJs .= "$('slHabilitacao').disabled = true;";
        }
        break;
    case 'limparVeiculo' :
        $stJs .= "$('spnDetalhe').innerHTML = '';";
        $stJs .= "$('inCodVeiculo').value = '';";
        $stJs .= "$('stPrefixo').value = '';";
        $stJs .= "$('stNumPlaca').value = '';";
        $stJs .= "$('boPadraoNao').checked = true;";
        $stJs .= "$('boPadraoSim').checked = false;";
        $stJs .= "$('spnVeiculos').innerHTML = '';";
        break;

    case 'limpar' :
        $stJs .= "$('spnDetalhe').innerHTML = '';";
        $stJs .= "$('inCodVeiculo').value = '';";
        $stJs .= "$('stPrefixo').value = '';";
        $stJs .= "$('stNumPlaca').value = '';";
        $stJs .= "$('boPadraoNao').checked = true;";
        $stJs .= "$('boPadraoSim').checked = false;";
        $stJs .= "$('spnVeiculos').innerHTML = '';";
        $stJs .= "$('slHabilitacao').disabled = false;";
        $stJs .= "$('hdnHabilitacao').value = '';";
        Sessao::write('veiculosMotorista' , array());
        break;

    case 'carregarListaInfracao':
        //apresenta lista de infrações do motorista
        $obTFrotaInfracao = new TFrotaInfracao();
        $stFiltro = " WHERE cgm_motorista=".$_REQUEST['inCodMotorista'];
        $obTFrotaInfracao->recuperaInfracao( $rsFrotaInfracao, $stFiltro, ' ORDER BY data_infracao DESC ');

        $obTable = new Table();
        $obTable->setRecordset( $rsFrotaInfracao );
        $obTable->setSummary( 'Lista de Infrações do Veículo' );

        $obTable->Head->addCabecalho( 'Veículo'      , 11 );
        $obTable->Head->addCabecalho( 'Auto Infração', 6  );
        $obTable->Head->addCabecalho( 'Data'         , 6  );
        $obTable->Head->addCabecalho( 'Motivo'       , 17 );
        $obTable->Head->addCabecalho( 'Gravidade'    , 5  );
        $obTable->Head->addCabecalho( 'Pontos'       , 4  );

        $obTable->Body->addCampo( 'nom_modelo'   , 'C' );
        $obTable->Body->addCampo( 'auto_infracao', 'C' );
        $obTable->Body->addCampo( 'data_infracao', 'C' );
        $obTable->Body->addCampo( 'motivo'       , 'C' );
        $obTable->Body->addCampo( 'gravidade'    , 'C' );
        $obTable->Body->addCampo( 'pontos'       , 'C' );

        $obTable->montaHTML( true );

        $obLabel = new Label();
        $obLabel->setName('total_infracao');
        $obLabel->setRotulo('Total de infrações');
        if( $rsFrotaInfracao->getNumLinhas() > 0 ) {
            $obLabel->setValue($rsFrotaInfracao->getNumLinhas());
        } else {
            $obLabel->setValue( 0 );
        }
        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obLabel );
        $obFormulario->montaInnerHTML();

        $stJs.= "$('spnInfracao').innerHTML='".$obTable->getHtml().$obFormulario->getHTML()."';";

    break;
}

echo $stJs;

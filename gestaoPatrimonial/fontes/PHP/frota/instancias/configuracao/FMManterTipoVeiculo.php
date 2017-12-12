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
    * Data de Criação: 16/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FMManterTipoVeiculo.php 63195 2015-08-03 20:45:09Z carlos.silva $

    * Casos de uso: uc-03.02.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php' );

$stPrograma = "ManterTipoVeiculo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == 'alterar') {
    $obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
    $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['inCodTipoVeiculo'] );
    $obTFrotaTipoVeiculo->recuperaPorChave( $rsTipoVeiculo );
    
    $inCodTipoVeiculo = $_REQUEST['inCodTipoVeiculo'];
} else {
    $obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
    $obTFrotaTipoVeiculo->ProximoCod( $inCodTipoVeiculo );
    
    $rsTipoVeiculo = new RecordSet();
}

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//cria textbox para o tipo do veiculo
$obTxtCodigoTipoVeiculo = new Inteiro();
$obTxtCodigoTipoVeiculo->setName( 'stCodigoTipoVeiculo' );
$obTxtCodigoTipoVeiculo->setId( 'stCodigoTipoVeiculo' );
$obTxtCodigoTipoVeiculo->setRotulo( 'Código' );
$obTxtCodigoTipoVeiculo->setTitle( 'Informe o Código do Tipo de Veículo.' );
$obTxtCodigoTipoVeiculo->setNull( false );
$obTxtCodigoTipoVeiculo->setMaxLength( 8 );
$obTxtCodigoTipoVeiculo->setSize( 8 );
$obTxtCodigoTipoVeiculo->setValue( $inCodTipoVeiculo );
if ($stAcao == 'alterar') {
    $obTxtCodigoTipoVeiculo->setLabel( true );
}

//cria textbox para o tipo do veiculo
$obTxtTipoVeiculo = new TextBox();
$obTxtTipoVeiculo->setName( 'stTipoVeiculo' );
$obTxtTipoVeiculo->setId( 'stTipoVeiculo' );
$obTxtTipoVeiculo->setRotulo( 'Descrição' );
$obTxtTipoVeiculo->setTitle( 'Informe a descrição do tipo do veículo.' );
$obTxtTipoVeiculo->setNull( false );
$obTxtTipoVeiculo->setMaxLength( 30 );
$obTxtTipoVeiculo->setSize( 30 );
$obTxtTipoVeiculo->setValue( $rsTipoVeiculo->getCampo( 'nom_tipo' ) );

//radio para exige a placa
$obRdPlacaSim = new Radio();
$obRdPlacaSim->setName('boPlaca');
$obRdPlacaSim->setId( 'boPlacaSim' );
$obRdPlacaSim->setValue( true );
$obRdPlacaSim->setRotulo( 'Exige Placa' );
$obRdPlacaSim->setTitle( 'Informe a obrigatoriedade da placa.' );
$obRdPlacaSim->setLabel( 'Sim' );
$obRdPlacaSim->setNull( false );
if ( $stAcao == 'incluir' OR $rsTipoVeiculo->getCampo('placa') == 't' ) {
    $obRdPlacaSim->setChecked( true );
}

//radio para nao exige a placa
$obRdPlacaNao = new Radio();
$obRdPlacaNao->setName('boPlaca');
$obRdPlacaNao->setId( 'boPlacaNao' );
$obRdPlacaNao->setValue( 'false' );
$obRdPlacaNao->setRotulo( 'Exige Placa' );
$obRdPlacaNao->setTitle( 'Informe a obrigatoriedade da placa.' );
$obRdPlacaNao->setLabel( 'Nao' );
$obRdPlacaNao->setNull( false );
if ( $rsTipoVeiculo->getCampo('placa') == 'f' ) {
    $obRdPlacaNao->setChecked( true );
}

//radio para exige prefixo
$obRdPrefixoSim = new Radio();
$obRdPrefixoSim->setName('boPrefixo');
$obRdPrefixoSim->setId( 'boPrefixoSim' );
$obRdPrefixoSim->setValue( true );
$obRdPrefixoSim->setRotulo( 'Exige Prefixo' );
$obRdPrefixoSim->setTitle( 'Informe a obrigatoriedade do prefixo.' );
$obRdPrefixoSim->setLabel( 'Sim' );
$obRdPrefixoSim->setChecked( true );
$obRdPrefixoSim->setNull( false );
if ( $stAcao == 'incluir' OR $rsTipoVeiculo->getCampo('prefixo') == 't' ) {
    $obRdPrefixoSim->setChecked( true );
}

//radio para nao exige prefixo
$obRdPrefixoNao = new Radio();
$obRdPrefixoNao->setName('boPrefixo');
$obRdPrefixoNao->setId( 'boPrefixo' );
$obRdPrefixoNao->setValue( 'false' );
$obRdPrefixoNao->setRotulo( 'Exige Prefixo' );
$obRdPrefixoNao->setTitle( 'Informe a obrigatoriedade do prefixo.' );
$obRdPrefixoNao->setLabel( 'Nao' );
$obRdPrefixoNao->setNull( false );
if ( $rsTipoVeiculo->getCampo('prefixo') == 'f' ) {
    $obRdPrefixoNao->setChecked( true );
}

//radio para Controlar Horas Trabalhadas 
$obRdHorasSim = new Radio();
$obRdHorasSim->setName('boHoras');
$obRdHorasSim->setId( 'boHorasSim' );
$obRdHorasSim->setValue( true );
$obRdHorasSim->setRotulo( 'Controlar Horas Trabalhadas' );
$obRdHorasSim->setTitle( 'Informe se este Tipo de Veículo possuirá controle de horas trabalhadas.' );
$obRdHorasSim->setLabel( 'Sim' );
$obRdHorasSim->setNull( false );

//radio para NÃO Controlar Horas Trabalhadas 
$obRdHorasNao = new Radio();
$obRdHorasNao->setName('boHoras');
$obRdHorasNao->setId( 'boHorasNao' );
$obRdHorasNao->setValue( 'false' );
$obRdHorasNao->setRotulo( 'Controlar Horas Trabalhadas' );
$obRdHorasNao->setTitle( 'Informe se este Tipo de Veículo possuirá controle de horas trabalhadas.' );
$obRdHorasNao->setLabel( 'Nao' );
$obRdHorasNao->setNull( false );

if ( $rsTipoVeiculo->getCampo('controlar_horas_trabalhadas') == 't' )
    $obRdHorasSim->setChecked( true );
else
    $obRdHorasNao->setChecked( true );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.02');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Tipo do Veículo' );

$obFormulario->addComponente( $obTxtCodigoTipoVeiculo );
$obFormulario->addComponente( $obTxtTipoVeiculo );

$obFormulario->agrupaComponentes( array( $obRdPlacaSim, $obRdPlacaNao ) );
$obFormulario->agrupaComponentes( array( $obRdPrefixoSim, $obRdPrefixoNao ) );
$obFormulario->agrupaComponentes( array( $obRdHorasSim, $obRdHorasNao ) );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

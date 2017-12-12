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
    * Data de Criação: 14/02/2009

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    $Id: FMManterTipoVeiculo.php 32939 2008-09-03 21:14:50Z domluc $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaPosto.class.php' );
include_once( CAM_GP_FRO_COMPONENTES.'IPopUpPosto.class.php' );

$stPrograma = "ManterPosto";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == 'alterar') {

    $obTPosto = new TFrotaPosto();
    $obTPosto->recuperaRelacionamento( $rsPosto, " AND cgm_posto=".$_REQUEST['inCGM'] );

} else {
    $rsPosto = new RecordSet();
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

if ($stAcao == 'incluir') {
    $obBscCGM = new IPopUpCGM( $obForm );
    $obBscCGM->setNull     ( true               );
    $obBscCGM->setRotulo   ( "CGM do posto"     );
    $obBscCGM->setTitle    ( "Informe o CGM do Posto" );
    $obBscCGM->setValue( $inCGM );
    $obBscCGM->setNull(false);
    $obBscCGM->setTipo ( 'juridica' );
} else {
    $obHdnCGM = new Hidden;
    $obHdnCGM->setName ("inCGM" );
    $obHdnCGM->setValue( $rsPosto->getCampo('cgm_posto') );

    $obLblCGM = new Label;
    $obLblCGM->setName  ("stAcao");
    $obLblCGM->setRotulo( "CGM do posto"     );
    $obLblCGM->setValue( $rsPosto->getCampo('cgm_posto')." - ".$rsPosto->getCampo('nom_cgm') );
}

$obRdInternoSim = new Radio();
$obRdInternoSim->setName('boInterno');
$obRdInternoSim->setId( 'boInternoSim' );
$obRdInternoSim->setValue( true );
$obRdInternoSim->setRotulo( 'Interno' );
$obRdInternoSim->setTitle( 'Informe se o cgm é interno (Posto da Prefeitura).' );
$obRdInternoSim->setLabel( 'Sim' );
$obRdInternoSim->setNull( false );
if ($rsPosto->getCampo('interno') == 't' ) {
    $obRdInternoSim->setChecked( true );
}

$obRdInternoNao = new Radio();
$obRdInternoNao->setName('boInterno');
$obRdInternoNao->setId( 'boInternoNao' );
$obRdInternoNao->setValue( false );
$obRdInternoNao->setRotulo( 'Interno' );
$obRdInternoNao->setTitle( 'Informe se o cgm é interno.' );
$obRdInternoNao->setLabel( 'Não' );
$obRdInternoNao->setNull( false );
if ($stAcao == 'incluir' ||  $rsPosto->getCampo('interno') == 'f' ) {
    $obRdInternoNao->setChecked( true );
}

if ($stAcao == 'alterar') {
    //radio para exige prefixo
    $obRdAtivoSim = new Radio();
    $obRdAtivoSim->setName('boAtivo');
    $obRdAtivoSim->setValue( true );
    $obRdAtivoSim->setRotulo( 'Ativo' );
    $obRdAtivoSim->setTitle( 'Informe se o posto é ativo.' );
    $obRdAtivoSim->setLabel( 'Sim' );
    $obRdAtivoSim->setChecked( true );
    $obRdAtivoSim->setNull( false );
    if ( $rsPosto->getCampo('ativo') == 't' ) {
        $obRdAtivoSim->setChecked( true );
    }

    //radio para nao exige prefixo
    $obRdAtivoNao = new Radio();
    $obRdAtivoNao->setName('boAtivo');
    $obRdAtivoNao->setValue( false );
    $obRdAtivoNao->setRotulo( 'Exige Prefixo' );
    $obRdAtivoNao->setTitle( 'Informe se o posto é ativo.' );
    $obRdAtivoNao->setLabel( 'Não' );
    $obRdAtivoNao->setNull( false );
    if ( $rsPosto->getCampo('ativo') == 'f' ) {
        $obRdAtivoNao->setChecked( true );
    }
}

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.21');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Posto' );

if ($stAcao == 'incluir') {
    $obFormulario->addComponente( $obBscCGM );
} else {
    $obFormulario->addHidden( $obHdnCGM );
    $obFormulario->addComponente( $obLblCGM );
}

$obFormulario->agrupaComponentes( array( $obRdInternoSim, $obRdInternoNao ) );
if ($stAcao == 'alterar') {
    $obFormulario->agrupaComponentes( array( $obRdAtivoSim, $obRdAtivoNao ) );
}

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

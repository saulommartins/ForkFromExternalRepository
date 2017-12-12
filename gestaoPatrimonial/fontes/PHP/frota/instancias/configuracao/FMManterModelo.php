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

    $Id: FMManterModelo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.04
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaMarca.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaModelo.class.php' );
include_once( CAM_GP_FRO_COMPONENTES.'ISelectMarcaVeiculo.class.php' );

$stPrograma = "ManterModelo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == 'alterar') {
    $obTFrotaModelo = new TFrotaModelo();
    $obTFrotaModelo->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
    $obTFrotaModelo->setDado( 'cod_modelo', $_REQUEST['inCodModelo'] );
    $obTFrotaModelo->recuperaPorChave( $rsModelo );

    //cria um textbox para o codigo do modelo
    $obCodModelo = new TextBox();
    $obCodModelo->setName( 'inCodModelo' );
    $obCodModelo->setId( 'inCodModelo' );
    $obCodModelo->setValue( $rsModelo->getCampo( 'cod_modelo' ) );
    $obCodModelo->setRotulo( 'Código' );
    $obCodModelo->setLabel( true );

} else {
    $rsModelo = new RecordSet();
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

//recupera as marcas do sistema
$obTFrotaMarca = new TFrotaMarca();
$obTFrotaMarca->recuperaTodos( $rsMarca );

//cria um select para as marcas
$obSlMarca = new ISelectMarcaVeiculo( $obForm );
if ($stAcao == 'alterar') {
    $obSlMarca->setValue( $rsModelo->getCampo( 'cod_marca' ) );
    $obSlMarca->setLabel( true );
    $obSlMarca->setNull( true );
} else {
    $obSlMarca->setNull( false );
}

//cria textbox para o modelo
$obTxtModelo = new TextBox();
$obTxtModelo->setName( 'stModelo' );
$obTxtModelo->setId( 'stModelo' );
$obTxtModelo->setRotulo( 'Descrição' );
$obTxtModelo->setTitle( 'Informe a descrição do modelo.' );
$obTxtModelo->setNull( false );
$obTxtModelo->setMaxLength( 30 );
$obTxtModelo->setSize( 30 );
$obTxtModelo->setValue( $rsModelo->getCampo( 'nom_modelo' ) );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.04');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Modelo do Veículo' );

$obFormulario->addComponente( $obSlMarca );
if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obCodModelo );
}
$obFormulario->addComponente( $obTxtModelo );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

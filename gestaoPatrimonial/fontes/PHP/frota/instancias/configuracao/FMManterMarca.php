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

    $Id: FMManterMarca.php 63194 2015-08-03 20:44:43Z carlos.silva $

    * Casos de uso: uc-03.02.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaMarca.class.php' );

$stPrograma = "ManterMarca";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
    
if ($stAcao == 'alterar') {
    $obTFrotaMarca = new TFrotaMarca();
    $obTFrotaMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
    $obTFrotaMarca->recuperaPorChave( $rsMarca );
    
    $inCodMarca = $_REQUEST['inCodMarca'];
} else {
    $obTFrotaMarca = new TFrotaMarca();
    $obTFrotaMarca->ProximoCod( $inCodMarca );
    
    $rsMarca = new RecordSet();
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

$obCodMarca = new Inteiro();
$obCodMarca->setName( 'inCodMarca' );
$obCodMarca->setId( 'inCodMarca' );
$obCodMarca->setRotulo( 'Código' );
$obCodMarca->setMaxLength( 8 );
$obCodMarca->setSize( 8 );
$obCodMarca->setNull( false );
$obCodMarca->setValue( $inCodMarca );
if ($stAcao == 'alterar') {
    $obCodMarca->setLabel( true );
}

//cria textbox para a marca
$obTxtMarca = new TextBox();
$obTxtMarca->setName( 'stMarca' );
$obTxtMarca->setId( 'stMarca' );
$obTxtMarca->setRotulo( 'Descrição' );
$obTxtMarca->setTitle( 'Informe a descrição da marca do veículo.' );
$obTxtMarca->setNull( false );
$obTxtMarca->setMaxLength( 30 );
$obTxtMarca->setSize( 30 );
$obTxtMarca->setValue( $rsMarca->getCampo( 'nom_marca' ) );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.03');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Marca do Veículo' );
$obFormulario->addComponente( $obCodMarca );
$obFormulario->addComponente( $obTxtMarca );

if ($stAcao == 'alterar') {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
} else {
    $obFormulario->OK();
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

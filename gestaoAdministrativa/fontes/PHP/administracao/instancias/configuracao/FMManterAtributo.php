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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_REGRA."RCadastroDinamico.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsAtributoCadastro = $rsAtributo = $rsAtributoCompras = new RecordSet;
$obRRegra = new RCadastroDinamico;
//$obRRegra->obRModulo->setCodModulo(13);
$obRRegra->listar($rsAtributoCadastro);

/*$obRRegra->setCodCadastro();
$obRRegra->recuperaAtributosCadastro();*/

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
} elseif ($stAcao == "alterar") { }

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//$obTxtCadastro = new TextBox;
//$obTxtCadastro->setRotulo        ( "Cadastro" );
//$obTxtCadastro->setName          ( "inCodCadastro" );
//$obTxtCadastro->setValue         ( $inCodCadastro );
//$obTxtCadastro->setSize          ( 8 );
//$obTxtCadastro->setMaxLength     ( 8 );
//$obTxtCadastro->obEvento->SetOnChange("buscaValor('ComboCadastro');");
//$obTxtCadastro->setTitle		 ( "Selecione o Cadastro" );

$obCmbCadastro = new Select;
$obCmbCadastro->setRotulo        ( "Cadastro" );
$obCmbCadastro->setName          ( "inCodCadastro" );
$obCmbCadastro->setId            ( "inCodCadastro" );
$obCmbCadastro->setStyle         ( "width: 200px");
$obCmbCadastro->setCampoID       ( "cod_cadastro" );
$obCmbCadastro->setCampoDesc     ( "nom_cadastro" );
$obCmbCadastro->addOption        ( "", "Selecione" );
$obCmbCadastro->setValue         ( $stCodCadastro );
$obCmbCadastro->setNull          ( false );
$obCmbCadastro->preencheCombo    ( $rsAtributoCadastro );
$obCmbCadastro->obEvento->SetOnChange("document.frm.Ok.disabled = true; buscaValor('ComboCadastro');");

$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName ('inCodAtributos');
$obCmbAtributos->setRotulo ( "Atributos" );
$obCmbAtributos->setNull   ( true );
$obCmbAtributos->setTitle( "Atributos informados" );

// lista de atributos disponiveis
$obCmbAtributos->SetNomeLista1('inCodAtributosDisponiveis');
$obCmbAtributos->setCampoId1('cod_atributo');
$obCmbAtributos->setCampoDesc1('nom_atributo');
$obCmbAtributos->SetRecord1( $rsAtributoCompras );

// lista de atributos selecionados
$obCmbAtributos->SetNomeLista2('inCodAtributosSelecionados');
$obCmbAtributos->setCampoId2('cod_atributo');
$obCmbAtributos->setCampoDesc2('nom_atributo');
$obCmbAtributos->SetRecord2( $rsAtributo );

$obOk = new Ok;
$obOk->setDisabled( true );

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limpar();" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm );
$obFormulario->addHidden                ( $obHdnAcao );
$obFormulario->addHidden                ( $obHdnCtrl );

$obFormulario->addTitulo                ( "Dados para configuração dos atributos" );

//$obFormulario->addComponenteComposto    ( $obTxtCadastro, $obCmbCadastro );
$obFormulario->addComponente            ( $obCmbCadastro  );
$obFormulario->addComponente            ( $obCmbAtributos );

$obFormulario->defineBarra              ( array( $obOk, $obBtnLimpar ) );
$obFormulario->setFormFocus             ( $obCmbCadastro->getId()      );
$obFormulario->show                     ();

include_once($pgJs);

include_once '../../../includes/rodape.php';
?>

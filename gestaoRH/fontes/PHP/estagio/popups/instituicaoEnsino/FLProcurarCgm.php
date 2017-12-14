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
    * Arquivo de filtro de busca de CGM de Instituição/Entidade
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Revision: 30973 $
    $Name$
    $Author: melo $
    $Date: 2007-03-09 18:12:22 -0300 (Sex, 09 Mar 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include( $pgJS );

//destroi arrays de sessao que armazenam os da dos do FILTRO
Sessao::remove('link');
Sessao::remove('filtroRelatorio');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( "telaPrincipal" );

//Define HIDDEN com código do logradouro
$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName( "inCodCgm" );
$obHdnCodLogradouro->setValue( $_REQUEST["inCodCgm"] );

$obHdnFiltro = new Hidden;
$obHdnFiltro->setName( "boFiltro" );
$obHdnFiltro->setValue( $_GET['boFiltro'] );

$obHdnInstituicao = new Hidden;
$obHdnInstituicao->setName( "boInstituicao" );
$obHdnInstituicao->setValue( $_GET['boInstituicao'] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( $_GET['tipoBusca'] );
if($_GET['tipoBusca']=='usuario') $_GET['tipoBusca']='fisica';

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

//Definição das Caixas de Texto
$obTxtNomeCgm = new TextBox;
$obTxtNomeCgm->setTitle( "Informe o nome desejado" );
$obTxtNomeCgm->setName( "stNomeCgm" );
$obTxtNomeCgm->setRotulo( "Nome" );
$obTxtNomeCgm->setSize( 60 );
$obTxtNomeCgm->setMaxLength( 60 );

//Componente que define o tipo de busca
$obTipoBuscaNomCgm = new TipoBusca( $obTxtNomeCgm );

//Radio para definicao de pesquisa
$obRdPessoaFisica = new Radio;
$obRdPessoaFisica->setTitle      ( "Selecione o tipo de busca." );
$obRdPessoaFisica->setRotulo     ( "Tipo" );
$obRdPessoaFisica->setName       ( "stTipoPessoa" );
$obRdPessoaFisica->setValue      ( "F" );
$obRdPessoaFisica->setLabel      ( "Pessoa Física" );
$obRdPessoaFisica->obEvento->setOnClick( "habilitaCampos('F');" );
if ($_GET['tipoBusca'] == 'fisica') {
    $obRdPessoaFisica->setChecked( true );
} elseif ($_GET['tipoBusca'] == 'juridica') {
    $obRdPessoaFisica->setChecked( false );
    $obRdPessoaFisica->setDisabled( true );
}

$obRdPessoaJuridica = new Radio;
$obRdPessoaJuridica->setRotulo   ( "Tipo" );
$obRdPessoaJuridica->setName     ( "stTipoPessoa" );
$obRdPessoaJuridica->setValue    ( "J" );
$obRdPessoaJuridica->setLabel    ( "Pessoa Jurídica" );
$obRdPessoaJuridica->obEvento->setOnClick( "habilitaCampos('J');" );
if ($_GET['tipoBusca'] == 'juridica') {
    $obRdPessoaJuridica->setChecked( true );
} elseif ($_GET['tipoBusca'] == 'fisica') {
    $obRdPessoaJuridica->setChecked( false );
    $obRdPessoaJuridica->setDisabled( true );
}

$obRdTodos = new Radio;
$obRdTodos->setRotulo            ( "Tipo" );
$obRdTodos->setName              ( "stTipoPessoa" );
$obRdTodos->setValue             ( "T" );
$obRdTodos->setLabel             ( "Todos" );
$obRdTodos->obEvento->setOnClick ( "habilitaCampos('T');" );
if ($_GET['tipoBusca'] != 'geral') {
    $obRdTodos->setChecked( false );
    $obRdTodos->setDisabled( true );
} else {

    $obRdTodos->setChecked( true );
    $obRdTodos->setDisabled( false );
}

$arRadios = array( $obRdPessoaFisica, $obRdPessoaJuridica, $obRdTodos );

//Dados para pessoa fisica
$obTxtCPF = new CPF;
$obTxtCPF->setName( "stCPF" );
$obTxtCPF->setRotulo( "CPF" );
$obTxtCPF->setTitle ( "Informe o CPF desejado." );
$obTxtCPF->setNull( true );
$obTxtCPF->obEvento->setOnBlur("javascript: if ( !isCPF( this ) ) { alertaAviso( 'CPF '+this.value+' inválido!','form','erro','".Sessao::getId()."', '../'); }" );
if ($_GET['tipoBusca'] != 'fisica') {
    $obTxtCPF->setDisabled( true );
}

//Dados para pessoa juridica
$obTxtCNPJ = new CNPJ;
$obTxtCNPJ->setName( "stCNPJ" );
$obTxtCNPJ->setTitle ( "Informe o CNPJ desejado." );
$obTxtCNPJ->setRotulo( "CNPJ" );
$obTxtCNPJ->setNull( true );
$obTxtCNPJ->obEvento->setOnBlur( "javascript: if ( !isCPF( this ) ) { alertaAviso( 'CNPJ '+this.value+' inválido!','form','erro','".Sessao::getId()."', '../'); }" );
if ($_GET['tipoBusca'] != 'juridica') {
    $obTxtCNPJ->setDisabled( true );
}

$obTxtNomeFantasia = new TextBox;
$obTxtNomeFantasia->setTitle ( "Informe o nome fantasia desejado" );
$obTxtNomeFantasia->setName( "stNomeFantasia" );
$obTxtNomeFantasia->setRotulo( "Nome Fantasia" );
$obTxtNomeFantasia->setSize( 60 );
$obTxtNomeFantasia->setMaxLength( 60 );
if ($_GET['tipoBusca'] != 'juridica') {
    $obTxtNomeFantasia->setDisabled( true );
}

$obTipoBuscaNomeFantasia = new TipoBusca( $obTxtNomeFantasia );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCodLogradouro );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnFiltro );
$obFormulario->addHidden( $obHdnInstituicao );
$obFormulario->addTitulo( "Dados para CGM" );
$obFormulario->addComponente( $obTipoBuscaNomCgm );
$obFormulario->agrupaComponentes( $arRadios );
$obFormulario->addComponente( $obTxtCPF );
$obFormulario->addComponente( $obTxtCNPJ );
//$obFormulario->addComponente( $obTxtNomeFantasia );
$obFormulario->addComponente( $obTipoBuscaNomeFantasia );
$obFormulario->OK();
$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
//$obIFrame->setSrc("../../../includes/mensagem.php?".Sessao::getId());
$obIFrame->setHeight("10%");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

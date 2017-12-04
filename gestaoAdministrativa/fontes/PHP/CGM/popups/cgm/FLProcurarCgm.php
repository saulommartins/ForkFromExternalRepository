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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28411 $
$Name$
$Author: diogo.zarpelon $
$Date: 2008-03-06 16:32:26 -0300 (Qui, 06 Mar 2008) $

Casos de uso: uc-01.02.92
*/

session_regenerate_id();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterCgm.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include( $pgJS );

$inCodCgm = $request->get('inCodCgm');
$stAcao = $request->get('stAcao');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

//Define HIDDEN com código do logradouro
$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName( "inCodCgm" );
$obHdnCodLogradouro->setValue($request->get('inCodCgm') );

//Define HIDDEN com tabela de vinculo
$obHdnStTabelaVinculo = new Hidden;
$obHdnStTabelaVinculo->setName( "stTabelaVinculo" );
$obHdnStTabelaVinculo->setValue( $request->get('stTabelaVinculo') );

//Define HIDDEN com tabela de vinculo
$obHdnBuscaContrato = new Hidden;
$obHdnBuscaContrato->setName( "buscaContrato" );
$obHdnBuscaContrato->setValue( $request->get('buscaContrato') );

//Define HIDDEN com tabela de vinculo
$obHdnStCampoVinculo = new Hidden;
$obHdnStCampoVinculo->setName( "stCampoVinculo" );
$obHdnStCampoVinculo->setValue( $request->get('stCampoVinculo') );

//Define HIDDEN com tabela de vinculo
$obHdnStId = new Hidden;
$obHdnStId->setName( "stId" );
$obHdnStId->setValue( $request->get('stId') );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $request->get('stId') );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $request->get('nomForm') );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( $request->get('tipoBusca') );

if($request->get('tipoBusca')=='usuario')
    $request->set('tipoBusca', 'fisica');

if ($request->get('tipoBusca') == "vinculoComissaoLicitacao"){
    $obHdnCodLicitacao = new Hidden;
    $obHdnCodLicitacao->setName("hdnCodLicitacao");
    $obHdnCodLicitacao->setValue( $request->get('inCodLicitacao') );
    
    $obHdnCodModalidade = new Hidden;
    $obHdnCodModalidade->setName("hdnCodModalidade");
    $obHdnCodModalidade->setValue( $request->get('inCodModalidade') );


    $obHdnCodComissao = new Hidden;
    $obHdnCodComissao->setName("hdnCodComissao");
    $obHdnCodComissao->setValue( $request->get('inCodComissao') );
}

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

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
switch ($request->get('tipoBusca')) {
    case 'juridica':
    case 'orgaoGerenciador':
        $obRdPessoaFisica->setChecked( false );
        $obRdPessoaFisica->setDisabled( true );
    break;
    case 'fisica':
        $obRdPessoaFisica->setChecked( true );
    break;
}

$obRdPessoaJuridica = new Radio;
$obRdPessoaJuridica->setRotulo   ( "Tipo" );
$obRdPessoaJuridica->setName     ( "stTipoPessoa" );
$obRdPessoaJuridica->setValue    ( "J" );
$obRdPessoaJuridica->setLabel    ( "Pessoa Jurídica" );
$obRdPessoaJuridica->obEvento->setOnClick( "habilitaCampos('J');" );
switch ($request->get('tipoBusca')) {
    case 'juridica':
    case 'orgaoGerenciador':
        $obRdPessoaJuridica->setChecked( true );    
        $jsOnload .= " habilitaCampos('J'); ";
    break;
    case 'fisica':
        $obRdPessoaJuridica->setChecked( false );
        $obRdPessoaJuridica->setDisabled( true );
    break;
}

$obRdTodos = new Radio;
$obRdTodos->setRotulo            ( "Tipo" );
$obRdTodos->setName              ( "stTipoPessoa" );
$obRdTodos->setValue             ( "T" );
$obRdTodos->setLabel             ( "Todos" );
$obRdTodos->obEvento->setOnClick ( "habilitaCampos('T');" );
if ( $request->get('tipoBusca') != 'geral' ) {
    $obRdTodos->setChecked( false );
    $obRdTodos->setDisabled( true );
} else {
    $obRdTodos->setChecked( true );
    $obRdTodos->setDisabled( false );
}

//Dados para pessoa fisica
$obTxtCPF = new CPF;
$obTxtCPF->setName( "stCPF" );
$obTxtCPF->setRotulo( "CPF" );
$obTxtCPF->setTitle ( "Informe o CPF desejado." );
$obTxtCPF->setNull( true );
$obTxtCPF->obEvento->setOnBlur("javascript: if ( !isCPF( this ) ) { alertaAviso( 'CPF '+this.value+' inválido!','form','erro','".Sessao::getId()."', '../'); }" );
if ( $request->get('tipoBusca') != 'fisica' && $request->get('tipoBusca') != strtolower("vinculado") ) {
    $obTxtCPF->setDisabled( true );
}

//Dados para pessoa juridica
$obTxtCNPJ = new CNPJ;
$obTxtCNPJ->setName( "stCNPJ" );
$obTxtCNPJ->setTitle ( "Informe o CNPJ desejado." );
$obTxtCNPJ->setRotulo( "CNPJ" );
$obTxtCNPJ->setNull( true );
$obTxtCNPJ->obEvento->setOnBlur( "javascript: if ( !isCNPJ( this ) ) { alertaAviso( 'CNPJ '+this.value+' inválido!','form','erro','".Sessao::getId()."', '../'); }" );
if ( $request->get('tipoBusca') != 'juridica' && $request->get('tipoBusca') != strtolower("vinculado")) {
    $obTxtCNPJ->setDisabled( true );
}

$obTxtNomeFantasia = new TextBox;
$obTxtNomeFantasia->setTitle ( "Informe o nome fantasia desejado," );
$obTxtNomeFantasia->setName( "stNomeFantasia" );
$obTxtNomeFantasia->setRotulo( "Nome Fantasia" );
$obTxtNomeFantasia->setSize( 60 );
$obTxtNomeFantasia->setMaxLength( 60 );
if ( $request->get('tipoBusca') != 'juridica' && strtolower($request->get('tipoBusca')) != "vinculado") {
    $obTxtNomeFantasia->setDisabled( true );
}

$obTipoBuscaNomeFantasia = new TipoBusca( $obTxtNomeFantasia );

if ( $request->get('tipoBusca')  ==  'vinculado' ) {
    $obRdTodos->setChecked(true);
    $obRdPessoaFisica->setChecked(false);
    $obRdPessoaFisica->setDisabled(true);
    $obRdPessoaJuridica->setChecked(false);
    $obRdPessoaJuridica->setDisabled(true);
}

$arRadios = array( $obRdPessoaFisica, $obRdPessoaJuridica, $obRdTodos );

$arBotoes   = array( new Ok, new Limpar );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCodLogradouro );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->addHidden( $obHdnBuscaContrato);
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnStTabelaVinculo );
$obFormulario->addHidden( $obHdnStCampoVinculo );
$obFormulario->addHidden( $obHdnStId );
if ($request->get('tipoBusca') == "vinculoComissaoLicitacao"){
    $obFormulario->addHidden( $obHdnCodLicitacao );
    $obFormulario->addHidden( $obHdnCodModalidade );
    $obFormulario->addHidden( $obHdnCodComissao );
}
$obFormulario->addTitulo( "Dados para CGM" );
$obFormulario->addComponente( $obTipoBuscaNomCgm );

// Caso o tipo de busca seja vinculado, oculta os tipos (fisica, jurídica, todos)
if ( strtolower($request->get('tipoBusca')) !=  'vinculado' ) {
    $obFormulario->agrupaComponentes( $arRadios );
}

$obFormulario->addComponente( $obTxtCPF );
$obFormulario->addComponente( $obTxtCNPJ );
$obFormulario->addComponente( $obTipoBuscaNomeFantasia );
## $obFormulario->OK();
$obFormulario->defineBarra( $arBotoes, "left" );

$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("10%");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

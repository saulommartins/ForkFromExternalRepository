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
* Arquivo instância para popup de Servidor
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 30907 $
$Name$
$Author: souzadl $
$Date: 2006-12-21 13:50:15 -0200 (Qui, 21 Dez 2006) $

Casos de uso: uc-04.04.07
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

include_once( $pgJS );

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove( "filtroRelatorio" );
Sessao::remove( "link" );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( "telaPrincipal" );

//Define HIDDEN com código do logradouro
$obHdnCodLogradouro = new Hidden;
$obHdnCodLogradouro->setName( "inCodCgm" );
$obHdnCodLogradouro->setValue( $request->get("inCodCgm") );

$obHdnFiltro = new Hidden;
$obHdnFiltro->setName( "inFiltro" );
$obHdnFiltro->setValue( $request->get('inFiltro') );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

//Definição das Caixas de Texto
$obTxtNomeCgm = new TextBox;
$obTxtNomeCgm->setName( "stNomeCgm" );
$obTxtNomeCgm->setRotulo( "Nome" );
$obTxtNomeCgm->setSize( 60 );
$obTxtNomeCgm->setMaxLength( 60 );

//Radio para definicao de pesquisa
$obRdPessoaFisica = new Radio;
$obRdPessoaFisica->setRotulo     ( "Tipo" );
$obRdPessoaFisica->setName       ( "stTipoPessoa" );
$obRdPessoaFisica->setValue      ( "F" );
$obRdPessoaFisica->setLabel      ( "Pessoa Física" );
$obRdPessoaFisica->obEvento->setOnClick( "habilitaCampos('F');" );
$obRdPessoaFisica->setChecked( true );
$obRdPessoaFisica->setDisabled( false );

$obRdPessoaJuridica = new Radio;
$obRdPessoaJuridica->setRotulo   ( "Tipo" );
$obRdPessoaJuridica->setName     ( "stTipoPessoa" );
$obRdPessoaJuridica->setValue    ( "J" );
$obRdPessoaJuridica->setLabel    ( "Pessoa Jurídica" );
$obRdPessoaJuridica->obEvento->setOnClick( "habilitaCampos('J');" );
$obRdPessoaJuridica->setChecked( false );
$obRdPessoaJuridica->setDisabled( true );

$obRdTodos = new Radio;
$obRdTodos->setRotulo            ( "Tipo" );
$obRdTodos->setName              ( "stTipoPessoa" );
$obRdTodos->setValue             ( "T" );
$obRdTodos->setLabel             ( "Todos" );
$obRdTodos->obEvento->setOnClick ( "habilitaCampos('T');" );
$obRdTodos->setChecked( false );
$obRdTodos->setDisabled( true );

$arRadios = array( $obRdPessoaFisica, $obRdPessoaJuridica, $obRdTodos );

//Dados para pessoa fisica
$obTxtCPF = new CPF;
$obTxtCPF->setName( "stCPF" );
$obTxtCPF->setRotulo( "CPF" );
$obTxtCPF->setNull( true );
if ($_GET['tipoBusca'] != 'fisica') {
    $obTxtCPF->setDisabled( true );
}

//Label para informar se está buscando contratos rescindidos
$obLblContrato = new Label;
$obLblContrato->setRotulo( "Matrícula" );
switch ( $request->get('inFiltro') ) {
    case 2:
        $obLblContrato->setValue ( "Vigente" );
    break;
    case 3:
        $obLblContrato->setValue ( "Rescindido" );
    break;
    case 4:
        $obLblContrato->setValue ( "Pensionista" );
    break;
    default:
        $boValidaCgmAtivo = Sessao::read('valida_ativos_cgm');
        if ($boValidaCgmAtivo == 'true') {
            $obLblContrato->setValue ( "Vigente" );    
        }else{
            $obLblContrato->setValue ( "Todos" );
        }
    break;
}

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCodLogradouro );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnFiltro );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addTitulo( "Dados para CGM" );
$obFormulario->addComponente( $obTxtNomeCgm );
$obFormulario->agrupaComponentes( $arRadios );
$obFormulario->addComponente( $obTxtCPF );
$obFormulario->addComponente( $obLblContrato );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

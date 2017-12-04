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
    * Página de Formulario de Inclusao/Alteracao de Carteira

    * Data de Criação   : 04/10/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterCarteira.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.05

*/

/*
$Log$
Revision 1.8  2006/09/15 14:57:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCarteira.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCarteira";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRMONCarteira = new RMONCarteira;
$obRMONConvenio = new RMONConvenio;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnCodCarteira =  new Hidden;
$obHdnCodCarteira->setName   ( "inCodCarteira" );
$obHdnCodCarteira->setValue  ( $_REQUEST["inCodCarteira"]  );
$obHdnNumCarteira =  new Hidden;
$obHdnNumCarteira->setName   ( "inNumCarteira" );
$obHdnNumCarteira->setValue  ( $_REQUEST["inNumCarteira"]  );

$obHdnCodConvenio =  new Hidden;
$obHdnCodConvenio->setName   ( "inCodConvenio" );
$obHdnCodConvenio->setValue  ( $_REQUEST["inCodConvenio"]  );
$obHdnNumConvenio =  new Hidden;
$obHdnNumConvenio->setName   ( "inNumConvenio" );
$obHdnNumConvenio->setValue  ( $_REQUEST["inNumConvenio"]  );

$obLblConvenio = new Label;
$obLblConvenio->setName   ( 'LabelConvenio' );
$obLblConvenio->setTitle  ( 'Convênio' );
$obLblConvenio->setRotulo ( 'Convênio' );
$obLblConvenio->setValue  ( $_REQUEST['inNumConvenio'] );

$obLblCarteira = new Label;
$obLblCarteira->setName   ( 'LabelCarteira' );
$obLblCarteira->setTitle  ( 'Número da Carteira' );
$obLblCarteira->setRotulo ( 'Número da Carteira' );
$obLblCarteira->setValue  ( $_REQUEST['inNumCarteira'] );

//------------------------------------------------------ TEXTBOX
$obTxtNumCarteira = new TextBox;
$obTxtNumCarteira->setRotulo    ( "Número da Carteira" );
$obTxtNumCarteira->setTitle     ( "Número da Carteira" );
$obTxtNumCarteira->setName      ( "inNumCarteira");
$obTxtNumCarteira->setValue     ( $_REQUEST["inNumCarteira"] );
$obTxtNumCarteira->setInteiro   ( true );
$obTxtNumCarteira->setSize      ( 10 );
$obTxtNumCarteira->setMaxLength ( 10 );
$obTxtNumCarteira->setNull      ( false );

$obTxtVariacao = new TextBox;
$obTxtVariacao->setRotulo    ( "Variação da Carteira" );
$obTxtVariacao->setName      ( "flVariacao");
$obTxtVariacao->setValue     ( $_REQUEST["flVariacao"] );
$obTxtVariacao->setTitle     ( "Variação da Carteira" );
$obTxtVariacao->setInteiro   ( true );
$obTxtVariacao->setSize      ( 10 );
$obTxtVariacao->setMaxLength ( 10 );
$obTxtVariacao->setNull      ( false );
//-------------------------------------------------------------/

$obBscConvenio = new BuscaInner;
$obBscConvenio->setRotulo ( "*Convênio" );
$obBscConvenio->setTitle  ( "Convênio ao qual a carteira está vincluada" );
$obBscConvenio->obCampoCod->setName   ( "inNumConvenio" );
$obBscConvenio->obCampoCod->setValue  ( $_REQUEST["inNumConvenio"] );
$obBscConvenio->obCampoCod->obEvento->setOnChange("buscaValor('buscaConvenio');");
$obBscConvenio->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."convenio/FLProcurarConvenio.php','frm','inCodConvenio','stConvenio','todos','".Sessao::getId()."','800','550');" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget ("oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.05" );
$obFormulario->addTitulo     ( "Dados para Carteira" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCodCarteira );
$obFormulario->addHidden     ( $obHdnCodConvenio );

if ($stAcao == "incluir") {
    $obFormulario->addComponente ( $obBscConvenio );
    $obFormulario->addComponente ( $obTxtNumCarteira );
} else {
    $obFormulario->addHidden ( $obHdnCodConvenio );
    $obFormulario->addHidden ( $obHdnNumCarteira );

    $obFormulario->addComponente ( $obLblConvenio );
    $obFormulario->addComponente ( $obLblCarteira );
}

$obFormulario->addComponente ( $obTxtVariacao   );

if ($stAcao == "incluir") {
    $obFormulario->Ok       ();
} else {
    $obFormulario->Cancelar ();
}

$obFormulario->show();

if ($stAcao == 'incluir') {
    $stJs .= 'f.inNumConvenio.focus();';
} else {
    $stJs .= 'f.flVariacao.focus();';
}
sistemaLegado::executaFrameOculto ( $stJs );

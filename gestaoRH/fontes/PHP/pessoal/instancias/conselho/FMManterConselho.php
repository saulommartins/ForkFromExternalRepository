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
* Página de Formulario de Inclusao/Alteracao de Conselho
* Data de Criação: 09/08/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30892 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterConselho";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

//"?stFiltroSiglaConselho=".$_GET["stFiltroSigla"]."&stFiltroDescricaoConselho=".$_GET["stFiltroDescricao"]
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//****************************************//
// Define o filtro da lista
//****************************************//

//Define o filtro por sigla
$obHdnFiltroSiglaConselho = new Hidden;
$obHdnFiltroSiglaConselho->setName ( "stFiltroSiglaConselho" );
$obHdnFiltroSiglaConselho->setValue( $_GET['stFiltroSigla']  );

//Define o filtro por descrição
$obHdnFiltroDescricaoConselho = new Hidden;
$obHdnFiltroDescricaoConselho->setName ( "stFiltroDescricaoConselho" );
$obHdnFiltroDescricaoConselho->setValue( $_GET['stFiltroDescricao']  );

//Define o codigo do conselho
$obHdnCodConselho = new Hidden;
$obHdnCodConselho->setName ( "inCodConselho" );
$obHdnCodConselho->setValue( $_REQUEST["inCodConselho"] );

//Define objeto TEXTBOX para armazenar a DESCRICAO do conselho
$obTxtDescricaoConselho = new TextBox;
$obTxtDescricaoConselho->setRotulo            ( "Nome"                       );
$obTxtDescricaoConselho->setTitle             ( "Informe o nome do conselho." );
$obTxtDescricaoConselho->setName              ( "stDescricaoConselho"        );
$obTxtDescricaoConselho->setId                ( "stDescricaoConselho"        );
$obTxtDescricaoConselho->setValue             ( $_REQUEST["stDescricaoConselho"] );
$obTxtDescricaoConselho->setSize              ( 40 );
$obTxtDescricaoConselho->setMaxLength         ( 80 );
$obTxtDescricaoConselho->setNull              ( false );
$obTxtDescricaoConselho->setCaracteresAceitos ( '[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ/-]' );
$obTxtDescricaoConselho->setEspacosExtras       ( false );

//Define objeto TEXTBOX para armazenar a SIGLA do conselho
$obTxtSiglaConselho = new TextBox;
$obTxtSiglaConselho->setRotulo        ( "Sigla"                       );
$obTxtSiglaConselho->setTitle         ( "Informe a sigla do conselho." );
$obTxtSiglaConselho->setName          ( "stSiglaConselho"             );
$obTxtSiglaConselho->setValue         ( trim($_REQUEST["stSiglaConselho"]) );
$obTxtSiglaConselho->setSize          ( 10 );
$obTxtSiglaConselho->setMaxLength     ( 10 );
$obTxtSiglaConselho->setNull          ( false );
$obTxtSiglaConselho->setAlfaNumerico  ( false );
$obTxtSiglaConselho->setToUpperCase   ( true  );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnCodConselho         );
$obFormulario->addHidden        ( $obHdnFiltroSiglaConselho );
$obFormulario->addHidden        ( $obHdnFiltroDescricaoConselho );
$obFormulario->addTitulo        ( "Informações do Conselho" );
$obFormulario->addComponente    ( $obTxtDescricaoConselho   );
$obFormulario->addComponente    ( $obTxtSiglaConselho      );

if ( $stAcao == "incluir" )
    $obFormulario->OK();
else
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro.'&stFiltroSiglaConselho='.$_GET['stFiltroSigla'].'&stFiltroDescricaoConselho='.$_GET['stFiltroDescricao'] );

$obFormulario->setFormFocus($obTxtDescricaoConselho->getId() );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

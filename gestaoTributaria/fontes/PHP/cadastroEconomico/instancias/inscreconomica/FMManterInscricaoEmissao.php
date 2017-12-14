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
    * Página de Formulario de Emissao da Certidao de Baixa

    * Data de Criação   : 05/11/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: $

    *Casos de uso: uc-05.02.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_OOPARSER."tbs_class.php" );
include_once ( CAM_OOPARSER."tbsooo_class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterInscricao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$arDadosArquivos = explode( "-", $_REQUEST["cmbDocumento"] );
//quando existirem certidoes de outras prefas basta usar o campo $arDadosArquivos[0] para fazer a devida distincao
$stFiltro = " WHERE baixa_cadastro_economico.inscricao_economica = ".$_REQUEST["inInscricaoEconomica"]." ORDER BY baixa_cadastro_economico.timestamp DESC LIMIT 1 ";
$obTCEMCadastroEconomico = new TCEMCadastroEconomico;
$obTCEMCadastroEconomico->recuperaDadosCertidaoBaixa( $rsDados, $stFiltro );

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;

// setting the object
$OOParser->SetZipBinary('zip');
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir('/tmp');

$stDocumento = '/tmp/';
$OOParser->_process_path = $stDocumento; //nome do arquivo pra salva

// create a new openoffice document from the template with an unique id
$OOParser->NewDocFromTpl( CAM_GT_CEM_CLASSES."anexos/modelos_usuario/".$arDadosArquivos[1] ); //arquivo do openof

$OOParser->LoadXmlFromDoc('content.xml');

$OOParser->MergeBlock( 'Dat1', $rsDados->arElementos );

$OOParser->SaveXmlToDoc();

$OOParser->LoadXmlFromDoc('styles.xml');
$OOParser->SaveXmlToDoc();

$arDadosArquivos[2] = $OOParser->GetPathnameDoc();
Sessao::write('dados', $arDadosArquivos);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Documentos para Download" );

$stDownLoadName = "stArq1";
$stLblDownLoadName = "stLBArq1";
$stBtnDownLoadName = "stBtnArq1";

$obLabelDownLoad = new Label;
$obLabelDownLoad->setValue ( $arDadosArquivos[1] );
$obLabelDownLoad->setName   ( $stLblDownLoadName );

$obBtnDownLoad = new Button;
$obBtnDownLoad->setName               ( $stBtnDownLoadName );
$obBtnDownLoad->setValue              ( "Download" );
$obBtnDownLoad->setTipo               ( "button" );
$obBtnDownLoad->obEvento->setOnClick  ( "busca('Download')" );
$obBtnDownLoad->setDisabled           ( false );

$obFormulario->defineBarra ( array( $obLabelDownLoad, $obBtnDownLoad ), 'left', '' );

$obFormulario->show();

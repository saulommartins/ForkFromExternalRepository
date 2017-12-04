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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GT_FIS_INSTANCIAS.'processoFiscal/JSEmitirDocumento.php';
include_once CAM_GT_FIS_NEGOCIO.'/RFISProcessoFiscal.class.php';
include_once CAM_GT_FIS_VISAO.'/VFISProcessoFiscal.class.php';

$obController = new RFISProcessoFiscal;
$obVisao = new VFISProcessoFiscal($obController);

$stAcao 			= $_GET['stAcao'];

$inCodProcesso 	    = $_REQUEST['inCodProcesso'];
$inTipoFiscalizacao = $_REQUEST['inTipoFiscalizacao'];

$stPrograma 	= "ManterProcesso";
$pgFilt         = "FL".$stPrograma.".php";
$pgList     	= "LS".$stPrograma.".php";
$pgForm     	= "FM".$stPrograma.".php";
$pgProc     	= "PR".$stPrograma.".php";
$pgOcul     	= "OC".$stPrograma.".php";
$pgJss      	= "JS".$stPrograma.".php";

include_once( $pgJss );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_GET['stCtrl']  );

$obHdnCodigoProcesso =  new Hidden;
$obHdnCodigoProcesso->setName ( "inCodProcesso" );
$obHdnCodigoProcesso->setValue( $inCodProcesso  );

$obHdnCgm =  new Hidden;
$obHdnCgm->setName ( "numcgm" );
$obHdnCgm->setValue( substr($_SESSION['numCgm'], 5,-2) );

//Processo Fiscal
$obProcessoFiscal = new Label;
$obProcessoFiscal->setRotulo( "Processo Fiscal" );
$obProcessoFiscal->setName( "inProcessoFiscal" );
$obProcessoFiscal->setValue( $inCodProcesso );

//Switch que monta a pesquisa de acordo com o Tipo de Fiscalização
switch ($inTipoFiscalizacao) {
    case 1: //Inscricao Economica
    $obInscricaoEconomica = new Label();
    $obInscricaoEconomica->setRotulo( "Inscrição Econômica" );
    $obInscricaoEconomica->setValue($_REQUEST['inInscricao']);
    break;

    case 2: //Inscricao Municipal
    $obInscricaoMunicipal = new Label();
    $obInscricaoMunicipal->setRotulo( "Inscrição Municipal" );
    $obInscricaoMunicipal->setValue($_REQUEST['inInscricao']);
    break;
}

$rsTipoFiscalizacao  = $obVisao->getTipoFiscalizacao($inTipoFiscalizacao);

//Tipo Fiscalização
$obTipoFiscalizacao = new Label;
$obTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obTipoFiscalizacao->setValue( $inTipoFiscalizacao ."-". $rsTipoFiscalizacao->getCampo('descricao') );

//rsFundamentacao
$rsFundamentacao = $obVisao->getFundamentacao($inCodProcesso, $inTipoFiscalizacao);

//Fundamentação Legal
$obFundamentacaoLegal = new Label;
$obFundamentacaoLegal->setRotulo( "Fundamentação Legal" );
$obFundamentacaoLegal->setValue( $rsFundamentacao->arElementos[0]['cod_processo_protocolo']." / ". $rsFundamentacao->arElementos[0]['ano_exercicio']);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obObservacoes = new TextArea;
$obObservacoes->setTitle( "Justificativa para cancelamento do processo"  );
$obObservacoes->setRotulo("Justificativa");
$obObservacoes->setName("stJustificativa");
$obObservacoes->setId("stJustificativa");
$obObservacoes->setNull(false);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm      );

$obFormulario->addHidden     ( $obHdnAcao   );
$obFormulario->addHidden     ( $obHdnCtrl   );
$obFormulario->addHidden     ( $obHdnCgm   );
$obFormulario->addHidden     ( $obHdnCodigoProcesso   );
$obFormulario->addComponente ( $obTipoFiscalizacao );
$obFormulario->addComponente ( $obProcessoFiscal );
switch ($inTipoFiscalizacao) {
    case 1:		$obFormulario->addComponente ( $obInscricaoEconomica ); break;
    case 2: 	$obFormulario->addComponente ( $obInscricaoMunicipal ); break;
}
$obFormulario->addComponente ( $obFundamentacaoLegal );
$obFormulario->addComponente ( $obObservacoes );

$obFormulario->ok();
$obFormulario->show();

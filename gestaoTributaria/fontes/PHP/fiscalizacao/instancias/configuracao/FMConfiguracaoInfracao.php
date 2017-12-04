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
    * Página de formulário do Manter documentos
    * Data de Criacao: 06/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Fellipe Esteves dos Santos
    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_COMPONENTES . "IPopUpInfracao.class.php" );
include_once( CAM_GA_NORMAS_CLASSES . "componentes/IPopUpNorma.class.php" );
include_once( CAM_GT_FIS_NEGOCIO . "RFISConfiguracaoInfracao.class.php");
require_once( CAM_GA_ADM_COMPONENTES.'ITextBoxSelectDocumento.class.php' );
include_once( CAM_GT_FIS_VISAO . "VFISConfiguracaoInfracao.class.php");
include_once( CAM_GT_FIS_NEGOCIO . "RFISInfracao.class.php" );
include_once( CAM_GT_FIS_VISAO   . 'VFISInfracao.class.php' );

$stAcao = $request->get('stAcao');
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) { $stAcao = "configurar"; }

//Define o nome dos arquivos
$stPrograma = "ConfiguracaoInfracao";
$pgFilt    	= "FL".$stPrograma.".php";
$pgList    	= "LS".$stPrograma.".php";
$pgForm 	= "FM".$stPrograma.".php";
$pgProc  	= "PR".$stPrograma.".php";
$pgOcul   	= "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

include_once( $pgJs );

$obRFISConfiguracaoInfracao = new RFISConfiguracaoInfracao;
$obVFISConfiguracaoInfracao = new VFISConfiguracaoInfracao;

$obRFISInfracao = new RFISInfracao();
$obVFISInfracao = new VFISInfracao( $obRFISInfracao );

$obErro = $obRFISConfiguracaoInfracao->consultar();

if ($obRFISConfiguracaoInfracao->getDocumentoNaoEntregue() != '') {
    $obDocumentoNaoEntregue		    = $obVFISInfracao->getInfracao($obRFISConfiguracaoInfracao->getDocumentoNaoEntregue());
    $inDocumentoNaoEntregue 		= $obDocumentoNaoEntregue->getCampo("cod_infracao");
    $stDocumentoNaoEntregue 		= $obDocumentoNaoEntregue->getCampo("nom_infracao");
}

if ($obRFISConfiguracaoInfracao->getDocumentoEntregueForaPrazo() != '') {
    $obDocumentoEntregueForaPrazo	= $obVFISInfracao->getInfracao($obRFISConfiguracaoInfracao->getDocumentoEntregueForaPrazo());
    $inDocumentoEntregueForaPrazo 	= $obDocumentoEntregueForaPrazo->getCampo("cod_infracao");
    $stDocumentoEntregueForaPrazo 	= $obDocumentoEntregueForaPrazo->getCampo("nom_infracao");
}

if ($obRFISConfiguracaoInfracao->getDocumentoEntregueParcial() != '') {
    $obDocumentoEntregueParcial	    = $obVFISInfracao->getInfracao($obRFISConfiguracaoInfracao->getDocumentoEntregueParcial());
    $inDocumentoEntregueParcial 	= $obDocumentoEntregueParcial->getCampo("cod_infracao");
    $stDocumentoEntregueParcial 	= $obDocumentoEntregueParcial->getCampo("nom_infracao");
}

if ($obRFISConfiguracaoInfracao->getPagamentoMenos() != '') {
    $obPagamentoMenos			    = $obVFISInfracao->getInfracao($obRFISConfiguracaoInfracao->getPagamentoMenos());
    $inPagamentoMenos 			    = $obPagamentoMenos->getCampo("cod_infracao");
    $stPagamentoMenos 			    = $obPagamentoMenos->getCampo("nom_infracao");
}

if ($obRFISConfiguracaoInfracao->getDeclaracaoMenor() != '') {
    $obDeclaracaoMenor			    = $obVFISInfracao->getInfracao($obRFISConfiguracaoInfracao->getDeclaracaoMenor());
    $inDeclaracaoMenor 			    = $obDeclaracaoMenor->getCampo("cod_infracao") ;
    $stDeclaracaoMenor 			    = $obDeclaracaoMenor->getCampo("nom_infracao") ;
}

//Acao do Form
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

//Campos Hidden
$obHdnAcao =  new Hidden;
$obHdnAcao->setName 	("stAcao");
$obHdnAcao->setValue	($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName 	( "stCtrl" );
$obHdnCtrl->setValue	($_REQUEST['stCtrl']);

//MONTA FORMLÁRIO
$obFormulario = new Formulario;
$obFormulario->addForm       	( $obForm				);

$obFormulario->addHidden     ( $obHdnAcao				);
$obFormulario->addHidden     ( $obHdnCtrl				);
$obFormulario->addTitulo 	( "Configuracao de Infrações" 	);

# Define documento não entregue
$obDocumentoNaoEntregue = new IPopUpInfracao();
$obDocumentoNaoEntregue->setRotulo( "Documento Não Entregue" );
$obDocumentoNaoEntregue->setTitle( "Infração referente à não entrega de documento" );

if ($inDocumentoNaoEntregue) {
    $obDocumentoNaoEntregue->obCampoCod->setValue( $inDocumentoNaoEntregue );
}

$obDocumentoNaoEntregue->obCampoCod->setName( "inDocumentoNaoEntregue" );
$obDocumentoNaoEntregue->obCampoCod->setId( "inDocumentoNaoEntregue" );
$obDocumentoNaoEntregue->setId('stDocumentoNaoEntregue');
$obDocumentoNaoEntregue->setNull( false );

if ($stDocumentoNaoEntregue) {
    $obDocumentoNaoEntregue->setValue( $stDocumentoNaoEntregue );
}

$obDocumentoNaoEntregue->geraFormulario( $obFormulario );

# Define documento entregue fora do prazo
$obDocumentoEntregueForaPrazo = new IPopUpInfracao();
$obDocumentoEntregueForaPrazo->setRotulo( "Documento Entregue Fora do Prazo" );
$obDocumentoEntregueForaPrazo->setTitle( "Infraçao referente a entrega fora do prazo" );

if ($inDocumentoEntregueForaPrazo) {
    $obDocumentoEntregueForaPrazo->obCampoCod->setValue( $inDocumentoEntregueForaPrazo );
}

$obDocumentoEntregueForaPrazo->obCampoCod->setName( "inDocumentoEntregueForaPrazo" );
$obDocumentoEntregueForaPrazo->obCampoCod->setId( "inDocumentoEntregueForaPrazo" );
$obDocumentoEntregueForaPrazo->setId('stDocumentoEntregueForaPrazo');
$obDocumentoEntregueForaPrazo->setNull( false );

if ($stDocumentoEntregueForaPrazo) {
    $obDocumentoEntregueForaPrazo->setValue( $stDocumentoEntregueForaPrazo );
}

$obDocumentoEntregueForaPrazo->geraFormulario( $obFormulario );

# Define documento entregue fora do prazo
$obDocumentoEntregueParcial = new IPopUpInfracao();
$obDocumentoEntregueParcial->setRotulo( "Entrega Parcial de Documento" );
$obDocumentoEntregueParcial->setTitle( "Infraçao referente à entrega parcial" );

if ($inDocumentoEntregueParcial) {
    $obDocumentoEntregueParcial->obCampoCod->setValue( $inDocumentoEntregueParcial );
}

$obDocumentoEntregueParcial->obCampoCod->setName( "inDocumentoEntregueParcial" );
$obDocumentoEntregueParcial->obCampoCod->setId( "inDocumentoEntregueParcial" );
$obDocumentoEntregueParcial->setId('stDocumentoEntregueParcial');
$obDocumentoEntregueParcial->setNull( false );

if ($stDocumentoEntregueParcial) {
    $obDocumentoEntregueParcial->setValue( $stDocumentoEntregueParcial );
}

$obDocumentoEntregueParcial->geraFormulario( $obFormulario );

# Define pagamento a menos
$obPagamentoMenos = new IPopUpInfracao();
$obPagamentoMenos->setRotulo( "Pagamento a Menos" );
$obPagamentoMenos->setTitle( "Infraçao referente a pagamento a menos" );

if ($inPagamentoMenos) {
    $obPagamentoMenos->obCampoCod->setValue( $inPagamentoMenos );
}

$obPagamentoMenos->obCampoCod->setName( "inPagamentoMenos" );
$obPagamentoMenos->obCampoCod->setId( "inPagamentoMenos" );
$obPagamentoMenos->setId('stPagamentoMenos');
$obPagamentoMenos->setNull( false );

if ($stPagamentoMenos) {
    $obPagamentoMenos->setValue( $stPagamentoMenos );
}

$obPagamentoMenos->geraFormulario( $obFormulario );

# Define declaração a menor
$obDeclaracaoMenor = new IPopUpInfracao();
$obDeclaracaoMenor->setRotulo( "Declaração a Menor" );
$obDeclaracaoMenor->setTitle( "Infraçao referente à declaração a menor" );

if ($inDeclaracaoMenor) {
    $obDeclaracaoMenor->obCampoCod->setValue( $inDeclaracaoMenor );
}

$obDeclaracaoMenor->obCampoCod->setName( "inDeclaracaoMenor" );
$obDeclaracaoMenor->obCampoCod->setId( "inDeclaracaoMenor" );
$obDeclaracaoMenor->setId('stDeclaracaoMenor');
$obDeclaracaoMenor->setNull( false );

if ($stDeclaracaoMenor) {
    $obDeclaracaoMenor->setValue( $stDeclaracaoMenor );
}

$obDeclaracaoMenor->geraFormulario( $obFormulario );

$obBtnIncluir = new Button;
$obBtnIncluir->setName                  ( "Ok"        );
$obBtnIncluir->setValue                 ( "Ok"        );
$obBtnIncluir->setTipo                  ( "button"    );
$obBtnIncluir->obEvento->setOnClick     ( "Salvar();" );
$obBtnIncluir->setDisabled              (  false      );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                   ( "Limpar"    );
$obBtnLimpar->setValue                  ( "Limpar"    );
$obBtnLimpar->setTipo                   ( "button"    );
$obBtnLimpar->obEvento->setOnClick      ( "LimparConfiguracaoInfracao();" );
$obBtnLimpar->setDisabled               (  false      );

$obFormulario->defineBarra( array( $obBtnIncluir, $obBtnLimpar ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

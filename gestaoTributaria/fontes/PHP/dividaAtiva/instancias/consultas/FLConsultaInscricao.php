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
    * Página de Formulario de Filtro para Consulta de Divida Ativa

    * Data de Criação   : 13/02/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLConsultaInscricao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.09

*/

/*
$Log$
Revision 1.5  2007/07/13 21:28:19  cercato
adicionando componente cobranca/exercicio na consulta

Revision 1.4  2007/07/02 20:51:58  cercato
adicionando validacao para exigir que pelo menos um filtro seja selecionado na consulta.

Revision 1.3  2007/03/01 14:35:09  cercato
Bug #8550#

Revision 1.2  2007/02/28 20:14:49  cercato
Bug #8551#

Revision 1.1  2007/02/26 12:59:16  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDividaIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpCobranca.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpLivroIntervalo.class.php");

if ( empty( $_REQUEST['stAcao'] ) || $_REQUEST['stAcao'] == "incluir" ) {
    $_REQUEST['stAcao'] = "inscrever";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ConsultaInscricao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link'  );
Sessao::remove('stLink');

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpImovel	 = new IPopUpImovelIntervalo;
$obIPopUpDivida  = new IPopUpDividaIntervalo;
$obIPopUpLivro   = new IPopUpLivroIntervalo;
$obIPopUpDivida->obInnerDividaIntervalo->setNull( true );
$obIPopUpLivro->obInnerLivroIntervalo->setNull( true );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
if (isset($_REQUEST['stCtrl'])) {
    $obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick    ( "submeteFiltro();" );

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obInnerFolhas = new BuscaInnerIntervalo;
$obInnerFolhas->setNull                    ( true );
$obInnerFolhas->setTitle                   ( "Busca Folhas" );
$obInnerFolhas->setRotulo                  ( "Folha" );
$obInnerFolhas->obLabelIntervalo->setValue ( "até" );
$obInnerFolhas->obCampoCod->setName        ( "inFolhaInicial" );
$obInnerFolhas->obCampoCod->setInteiro     ( false );
$obInnerFolhas->obCampoCod2->setName       ( "inFolhaFinal" );
$obInnerFolhas->obCampoCod2->setInteiro    ( false );
$obInnerFolhas->setFuncaoBusca (
"abrePopUp('".CAM_GT_DAT_POPUPS."inscricao/FLProcurarFolha.php','frm','".$obInnerFolhas->obCampoCod->stName
."','','todos','".Sessao::getId() ."','800','550');" );
$obInnerFolhas->setFuncaoBusca2 ( "abrePopUp('" .
CAM_GT_DAT_POPUPS."inscricao/FLProcurarFolha.php','frm',
'".$obInnerFolhas->obCampoCod2->stName."','','todos','".Sessao::getId()."','800','550');"
);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.09" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ("Dados para Filtro");

$obIPopUpDivida->geraFormulario ( $obFormulario );
$obIPopUpLivro->geraFormulario  ( $obFormulario );

$obFormulario->addComponente( $obInnerFolhas );

$obIPopUpCobranca = new IPopUpCobranca;
$obIPopUpCobranca->obInnerCobranca->setNull ( true );
$obIPopUpCobranca->geraFormulario( $obFormulario );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "Informe o número do CGM." );

$obFormulario->addComponente( $obPopUpCGM );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

$stJs = "jQuery('#inCodInscricao').focus();   ";
sistemaLegado::executaFrameOculto ( $stJs );

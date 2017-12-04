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
    * Página de Formulario de Filtro para Cobranca Administrativa

    * Data de Criação   : 03/01/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLManterCobranca.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.04

*/

/*
$Log$
Revision 1.3  2007/03/30 19:42:33  cercato
Bug #8962#

Revision 1.2  2007/03/01 14:33:24  cercato
Bug #8546#

Revision 1.1  2007/02/09 18:32:04  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDivida.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) || $stAcao == "incluir" ) {
    $stAcao = "inscrever";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCobranca";
$pgFilt        = "FL".$stPrograma.".php";
$pgListParc 	 = "LS".$stPrograma."Parcelas.php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link');

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpImovel	 = new IPopUpImovelIntervalo;
$obIPopUpDivida  = new IPopUpDivida;
$obIPopUpDivida->obInnerDivida->setNull( true );

$obRdbParcelamento = new Radio;
$obRdbParcelamento->setRotulo   ( "Tipo" );
$obRdbParcelamento->setTitle    ( "Informe o tipo." );
$obRdbParcelamento->setName     ( "stTipo" );
$obRdbParcelamento->setLabel    ( "Parcelamento" );
$obRdbParcelamento->setValue    ( "parcelamento" );
$obRdbParcelamento->setNull     ( false );
$obRdbParcelamento->setChecked  ( true );

//Tipo de autoridade
$obRdbConsolidacao = new Radio;
$obRdbConsolidacao->setRotulo   ( "Tipo" );
$obRdbConsolidacao->setTitle    ( "Informe o tipo." );
$obRdbConsolidacao->setName     ( "stTipo" );
$obRdbConsolidacao->setLabel    ( "Consolidação" );
$obRdbConsolidacao->setValue    ( "consolidacao" );
$obRdbConsolidacao->setNull     ( false );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$stCtrl = empty($stCtrl) ? "" : $stCtrl;
$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.04" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ("Dados para Filtro");

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "Informe o número do CGM." );
$obFormulario->addComponente( $obPopUpCGM );

$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
$obIPopUpDivida->geraFormulario ( $obFormulario );

$obBtnLimpar = new Limpar;

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick  ( "ListarCobranca();" );

$arBotoes = array( $obBtnOK, $obBtnLimpar );
$obFormulario->defineBarra( $arBotoes );
$obFormulario->show();

?>

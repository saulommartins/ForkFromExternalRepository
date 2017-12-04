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


    * Filtro para Relatorio de Inscricao Divida Ativa
    * Data de Criação   : 12/09/2014    
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM    

    * $Id: FLRelatorioInscricaoDividaAtiva.php 60345 2014-10-15 13:50:44Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpModalidade.class.php" );

$stPrograma      = "RelatorioInscricaoDividaAtiva";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once $pgJs;

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obIPopUpGrupoCredito = new MontaGrupoCredito;
$obIPopUpGrupoCredito->setRotulo ( "Grupo de Crédito" );
$obIPopUpGrupoCredito->setTitulo ( "Informe o código do grupo de crédito." );

$obIPopUpCredito = new IPopUpCredito;
$obIPopUpCredito->setRotulo ( "Crédito" );
$obIPopUpCredito->setTitle  ( "Informe o código de crédito." );
$obIPopUpCredito->setNull   ( true );
$obIPopUpCredito->obCampoCod->setStyle("width:100px;");

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpEmpresa->obInnerEmpresaIntervalo->setTitle( "Informe o código da Inscrição Econômica." );
$obIPopUpEmpresa->setVerificaInscricao(false);

$obIPopUpImovel = new IPopUpImovelIntervalo;
$obIPopUpImovel->obInnerImovelIntervalo->setTitle( "Informe o código da Inscrição Imobiliária." );
$obIPopUpImovel->setVerificaInscricao(false);

$dtDiaHoje = date ("d/m/Y");

$obHdnDataHoje = new Hidden;
$obHdnDataHoje->setName ( "dtHoje" );
$obHdnDataHoje->setValue( $dtDiaHoje );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

//DEFINICAO DO PERIODO
$obPeriodo = new Periodo;
$obPeriodo->setRotulo ("Período");
$obPeriodo->setTitle  ("Informe o período da dívida ativa DD/MM/AAAA.");
$obPeriodo->setNull   ( false );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull   ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle  ( "Informe o número do CGM." );
$obPopUpCGM->obCampoCod->setStyle("width:100px;");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnDataHoje );
$obFormulario->addTitulo ( "Dados para Filtro" );

$obFormulario->addComponente          ( $obPeriodo                );
$obIPopUpGrupoCredito->geraFormulario ( $obFormulario, true, true );
$obIPopUpCredito->geraFormulario      ( $obFormulario             );
$obFormulario->addComponente          ( $obPopUpCGM               );
$obIPopUpEmpresa->geraFormulario      ( $obFormulario             );
$obIPopUpImovel->geraFormulario       ( $obFormulario             );

$obBtnOK = new Ok;
$obBtnOK->obEvento->setOnClick("BloqueiaFrames(true,false); Salvar();");

$onBtnLimpar = new Limpar;

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
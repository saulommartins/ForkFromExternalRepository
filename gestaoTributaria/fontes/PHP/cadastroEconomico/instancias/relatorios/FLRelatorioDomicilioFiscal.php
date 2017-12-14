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


    * Filtro para Relatorio de Domicilio Fiscal
    * Data de Criação   : 09/09/2014    
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM    

    * $Id: FLRelatorioDomicilioFiscal.php 59807 2014-09-12 12:31:14Z evandro $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php"         );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"          );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );

$stPrograma      = "RelatorioDomicilioFiscal";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once $pgJs;

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obPopUpEmpresa = new IPopUpEmpresa;

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->obCampoCod->setName ( "inNumCGM" );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "CGM" );

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setRotulo                       ( "Logradouro"                               );
$obBscLogradouro->setTitle                        ( "Logradouro"                               );
$obBscLogradouro->setId                           ( "stLogradouro"                             );
$obBscLogradouro->obCampoCod->setName             ( "inNumLogradouro"                          );
$obBscLogradouro->obCampoCod->setId               ( "inNumLogradouro"                          );
$obBscLogradouro->obCampoCod->obEvento->setOnChange( "buscaLogradouroFiltro();"                );
$stBusca  = "abrePopUp('../../../cadastroImobiliario/popups/logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro',";
$stBusca .= "'stLogradouro','juridica','".Sessao::getId()."','800','550')";
$obBscLogradouro->setFuncaoBusca                  ( $stBusca );

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm );
$obFormulario->addTitulo          ( "Dados para Filtro" );
$obFormulario->addHidden          ( $obHdnAcao );
$obFormulario->addHidden          ( $obHdnCtrl );
$obPopUpEmpresa->geraFormulario   ( $obFormulario );
$obFormulario->addComponente      ( $obPopUpCGM );
$obFormulario->addComponente      ( $obBscLogradouro );
$obMontaAtividade->geraFormulario ( $obFormulario );

$obFormulario->Ok('BloqueiaFrames(true,false);');
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
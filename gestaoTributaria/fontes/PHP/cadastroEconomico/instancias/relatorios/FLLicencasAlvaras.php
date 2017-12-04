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


    * Filtro para Relatorio de Licenças e Alvarás
    * Data de Criação   : 01/09/2014
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @package URBEM
    * @subpackage Regra

    * $Id: FLLicencasAlvaras.php 59681 2014-09-04 18:54:38Z carolina $

    * Casos de uso: uc-05.02.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php" 				);
include_once ( CAM_GT_CEM_COMPONENTES."ITextLicenca.class.php"        	);
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php"        	);
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" 			);

$stPrograma      = "LicencasAlvaras";
$pgFilt          = "FL".$stPrograma.".php";
$pgFiltAlterar   = "FLAlterarLicenca.php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";


$stAcao = $request->get('stAcao');

Sessao::write( "link", "" );

$arConfiguracao = array();
$obRCEMLicenca = new RCEMLicenca;
$obRCEMLicenca->recuperaConfiguracao( $arConfiguracao , $sessao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnTipoLicenca = new Hidden;
$obHdnTipoLicenca->setName('stTipoLicenca');
$obHdnTipoLicenca->setValue ( $arConfiguracao['numero_licenca'] );

$obRCEMLicenca->obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCEMLicenca->obRCEMConfiguracao->getMascaraInscricao();

$obPopUpEmpresa = new IPopUpEmpresa;
$obTxtLicenca = new ITextLicenca;
$obPeriodicidade = new Periodicidade();

$obTipoSituacao = new Select();
$obTipoSituacao->setRotulo		("Situação");
$obTipoSituacao->setName		("stSituacao");

$obTipoSituacao->setNull		( false );
$obTipoSituacao->setCampoId		("cod_situacao");
$obTipoSituacao->addOption		( "", "Selecione uma situação");
$obTipoSituacao->addOption		( "Ativa", "Ativa");
$obTipoSituacao->addOption		( "Vencida", "Vencida");
$obTipoSituacao->addOption		( "Baixada", "Baixada");
$obTipoSituacao->addOption		( "Suspensa", "Suspensa");
$obTipoSituacao->addOption		( "Cassada", "Cassada");
$obTipoSituacao->addOption		( "Todas", "Todas");

$obForm = new Form;
$obForm->setAction($pgOcul);
$obForm->setTarget("oculto");

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnTipoLicenca);
$obFormulario->addTitulo( "Dados para Filtro");

$obTxtLicenca->geraFormulario ( $obFormulario );
$obPopUpEmpresa->geraFormulario ( $obFormulario );
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->addComponente($obTipoSituacao);
$obFormulario->Ok('BloqueiaFrames(true,false);');
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

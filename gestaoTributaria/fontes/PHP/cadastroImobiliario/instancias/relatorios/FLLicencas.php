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

    * Página de Filtro do Relatorio Licenca / Alvaras
    * Data de Criação   : 05/09/2014

    * @author Analista: Luciana Dellay
    * @author Programador: Carolina Schwaab Marçal

    $Id: FLLicencas.php 59977 2014-09-24 15:04:09Z carolina $

    * Casos de uso: uc-05.01.28
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicenca.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."ITextLicenca.class.php"        	);

//Define o nome dos arquivos PHP
$stPrograma = "Licencas";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "definir";
}

Sessao::remove('link');

$obTCIMLicenca = new TCIMLicenca;
$obTCIMLicenca->recuperaMaxLicenca( $rsMaxLicenca );
if ( $rsMaxLicenca->eof() ) {
    $stMascaraLicenca = "9/9999";
} else {
    $stMascaraLicenca = "";
    for ( $inX=0; $inX<strlen( $rsMaxLicenca->getCampo("cod_licenca") ); $inX++ ) {
        $stMascaraLicenca .= "9";
    }

    $stMascaraLicenca .= "/9999";
}

$obTxtLicenca = new ITextLicenca;

$obIPopUpImovel = new IPopUpImovel;
$obIPopUpImovel->obInnerImovel->setNULL( true );

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

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

$obForm = new Form;
$obForm->setAction($pgOcul);
$obForm->setTarget("oculto");

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
        
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Filtro" );
$obTxtLicenca->geraFormulario ( $obFormulario );
//$obFormulario->addComponente ( $obTxtLicenca );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->addComponente($obTipoSituacao);

$obOk  = new Ok(true);
$obOk->setId ("Ok");
        
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "frm.reset();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );

//$obFormulario->Ok('BloqueiaFrames(true,false);');
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

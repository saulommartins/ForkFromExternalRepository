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
    * Data de Criação: 04/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: FLRelatorioMotorista.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.16
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioMotorista";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//define os componentes do formulário

$obForm = new Form;
$obForm->setAction( $pgGera );

$obHdnCtrl   = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

//instancia uma ipopupcgmvinculado para o motorista
$obBscMotorista = new IPopUpCGMVinculado( $obForm );
$obBscMotorista->setTabelaVinculo       ( 'frota.motorista' );
$obBscMotorista->setCampoVinculo        ( 'cgm_motorista' );
$obBscMotorista->setNomeVinculo         ( 'Motorista' );
$obBscMotorista->setRotulo              ( 'CGM Motorista' );
$obBscMotorista->setTitle               ( 'Informe o CGM do motorista.' );
$obBscMotorista->setName                ( 'stNomMotorista');
$obBscMotorista->setId                  ( 'stNomMotorista');
$obBscMotorista->obCampoCod->setName    ( "inCodMotorista"   );
$obBscMotorista->obCampoCod->setId      ( "inCodMotorista"   );
$obBscMotorista->obCampoCod->setNull    ( true               );
$obBscMotorista->setNull                ( true               );

//instancia um periodicidade para o vencimento da cnh
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(date('Y'));
$obPeriodicidade->setNull( true );

//define o formulário

$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm             );
$obFormulario->setAjuda         ("UC-03.02.17");
$obFormulario->addHidden        ( $obHdnCtrl          );
$obFormulario->addTitulo        ( "Dados de Filtro"   );

$obFormulario->addComponente    ( $obBscMotorista );
$obFormulario->addComponente    ( $obPeriodicidade );

$obFormulario->OK();
$obFormulario->show();

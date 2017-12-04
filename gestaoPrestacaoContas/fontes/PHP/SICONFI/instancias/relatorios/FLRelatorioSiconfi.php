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
  * Página de Formulario de Relatório de DCA
  * Data de Criação: 07/07/2015

  * @author Analista:
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: FLRelatorioSiconfi.php 62980 2015-07-14 17:06:33Z carlos.silva $
  * $Date: 2015-07-14 14:06:33 -0300 (Tue, 14 Jul 2015) $
  * $Author: carlos.silva $
  * $Rev: 62980 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioSiconfi";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

switch($stAcao){
    case "anexo_dca_id":
        $stTitulo = "I-D";
        $stPrograma = $stPrograma."AnexoDCAID";
    break;
    
    case "anexo_dca_ie":
        $stTitulo = "I-E";
        $stPrograma = $stPrograma."AnexoDCAIE";
        break;
    
    case "anexo_dca_if":
        $stTitulo = "I-F";
        $stPrograma = $stPrograma."AnexoDCAIF";
        
        $obPeriodicidade = new Periodicidade();
        $obPeriodicidade->setExercicio       ( Sessao::getExercicio() );
        $obPeriodicidade->setValue           ( 4    );
        $obPeriodicidade->setValidaExercicio ( true );
        $obPeriodicidade->setObrigatorio     ( true );
    break;
    
    case "anexo_dca_ig":
        $stTitulo = "I-G";
        $stPrograma = $stPrograma."AnexoDCAIG";
    break;
    
    default:
        $stTitulo = "";
        $stPrograma = $stPrograma;
    break;
}
$pgGera = "OCGera".$stPrograma.".php";

$obForm = new Form;
$obForm->setAction(CAM_GPC_SICONFI_RELATORIOS.$pgGera);
$obForm->setTarget('telaPrincipal');

$obHdnCaminho = new Hidden;
$obHdnCaminho->setValue(CAM_GPC_SICONFI_RELATORIOS.$pgOcul);
$obHdnCaminho->setName("stCaminho");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Defini o objeto Entidades
$obISelectEntidade = new ISelectMultiploEntidadeUsuario();


//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addTitulo    ( "Anexo DCA ".$stTitulo );
$obFormulario->addHidden    ( $obHdnAcao    );
$obFormulario->addHidden    ( $obHdnCtrl    );
$obFormulario->addHidden    ( $obHdnCaminho );

$obFormulario->addComponente( $obISelectEntidade );

switch($stAcao){
    case "anexo_dca_if":
        $obFormulario->addComponente( $obPeriodicidade );
    break;
}

$obFormulario->OK();
$obFormulario->show();


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

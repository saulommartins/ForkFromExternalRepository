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
    * Página de Formulario de Inclusao/Alteracao de Suspensão

    * Data de Criação   : 30/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Márson Luís Oliveira de Paula
    * @ignore

    * $Id: FLManterSuspensao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.08

*/

/*
$Log$
Revision 1.6  2006/11/23 18:12:02  marson
Adição do caso de uso de Suspensão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRSuspensao.class.php"         );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"         );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php"     );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
// "incluir" foi utilizado para indicar suspensão do crédito e
// "alterar" foi utilizado para indicar alteração dos dados da suspensão
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
    }

Sessao::write( "link", "" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterSuspensao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$obRARRSuspensao = new RARRSuspensao;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

$obMontaGrupoCredito = new MontaGrupoCredito ;

$obIPopUpCredito     = new IPopUpCredito ;

if ($stAcao == "alterar") {
    $obRARRTipoSuspensao = new RARRTipoSuspensao;
    $rsTipoSuspensao     = new RecordSet;

    // Preenche RecordSet
    $obRARRTipoSuspensao->listarTipoSuspensao( $rsTipoSuspensao );

    $obTxtTipoSuspensao = new TextBox;
    $obTxtTipoSuspensao->setRotulo        ( "Tipo de Suspensão"                 );
    $obTxtTipoSuspensao->setName          ( "inCodigoTipoSuspensao"             );
    $obTxtTipoSuspensao->setValue         ( $_REQUEST["inCodigoTipoSuspensao"]  );
    $obTxtTipoSuspensao->setSize          ( 8                                   );
    $obTxtTipoSuspensao->setMaxLength     ( 8                                   );
    $obTxtTipoSuspensao->setNull          ( true                                );
    $obTxtTipoSuspensao->setInteiro       ( true                                );

    $obCmbTipoSuspensao = new Select;
    $obCmbTipoSuspensao->setName          ( "cmbTipoSuspensao"                  );
    $obCmbTipoSuspensao->addOption        ( "", "Selecione"                     );
    $obCmbTipoSuspensao->setCampoId       ( "cod_tipo_suspensao"                );
    $obCmbTipoSuspensao->setCampoDesc     ( "descricao"                         );
    $obCmbTipoSuspensao->preencheCombo    ( $rsTipoSuspensao                    );
    $obCmbTipoSuspensao->setValue         ( $_REQUEST["inCodigoTipoSuspensao"]  );
    $obCmbTipoSuspensao->setNull          ( true                                );
    $obCmbTipoSuspensao->setStyle         ( "width: 220px"                      );
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget("TelaPrincipal");
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.03.08" );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );

$obIPopUpCGM = new IPopUpCGM ( $obForm                             ) ;
$obIPopUpCGM->setNull        ( ($stAcao=="incluir") ? true : false );
$obIPopUpCGM->setRotulo      ( "CGM"                               );
$obIPopUpCGM->setTitle       ( "Código do Contribuinte"            );
$obFormulario->addComponente ( $obIPopUpCGM );

$obMontaGrupoCredito->geraFormulario ( $obFormulario, true, ($stAcao=="incluir") ? true : false );

$obIPopUpCredito->setNull        ( true          );
$obIPopUpCredito->geraFormulario ( $obFormulario );

if( $stAcao == "alterar" )
    $obFormulario->addComponenteComposto ( $obTxtTipoSuspensao , $obCmbTipoSuspensao );

$obFormulario->OK();
$obFormulario->show();

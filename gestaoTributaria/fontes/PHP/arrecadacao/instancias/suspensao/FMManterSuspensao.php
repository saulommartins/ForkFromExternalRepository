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

    * $Id: FMManterSuspensao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO    ."RARRSuspensao.class.php"              );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php"          );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"                  );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php"              );
include_once ( CAM_GA_PROT_CLASSES   ."componentes/IPopUpProcesso.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterSuspensao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
    }

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

$inCodLancamento    = $_REQUEST['inCodLancamento'];
$obHdnCodLancamento = new Hidden;
$obHdnCodLancamento->setName   ( "inCodLancamento" );
$obHdnCodLancamento->setValue  ( $inCodLancamento  );

if ($stAcao == "alterar") {
   $inCodSuspensao    = $_REQUEST['inCodSuspensao'];
   $obHdnCodSuspensao = new Hidden;
   $obHdnCodSuspensao->setName   ( "inCodSuspensao" );
   $obHdnCodSuspensao->setValue  ( $inCodSuspensao  );
}

$obCGM = new label;
$obCGM->setRotulo ( "CGM"              );
$obCGM->setTitle  ( "Contribuinte"     );
$obCGM->setName   ( "stCGM"            );
$obCGM->setId     ( "stCGM"            );
$obCGM->setValue  ( $_REQUEST["stCGM"] );

//$obGrupoCredito = new label;
//$obGrupoCredito->setRotulo ( "Grupo de Créditos"         );
//$obGrupoCredito->setTitle  ( "Grupo de Créditos"         );
//$obGrupoCredito->setName   ( "stGrupoCredito"            );
//$obGrupoCredito->setId     ( "stGrupoCredito"            );
//$obGrupoCredito->setValue  ( $_REQUEST["stGrupoCredito"] );

//$obCredito = new label;
//$obCredito->setRotulo ( "Crédito"              );
//$obCredito->setTitle  ( "Crédito"              );
//$obCredito->setName   ( "stCredito"            );
//$obCredito->setId     ( "stCredito"            );
//$obCredito->setValue  ( $_REQUEST["stCredito"] );

$obOrigemCobranca = new label;
$inCodGrupo       = $_REQUEST[ "inCodGrupo"];
$obOrigemCobranca->setRotulo ( $inCodGrupo?"Grupo de Crédito":"Crédito" );
$obOrigemCobranca->setTitle  ( $inCodGrupo?"Grupo de Crédito":"Crédito" );
$obOrigemCobranca->setName   ( "stOrigemCobranca"                       );
$obOrigemCobranca->setId     ( "stOrigemCobranca"                       );
$obOrigemCobranca->setValue  ( $_REQUEST["stOrigemCobranca"]            );

if ($stAcao == "incluir") {
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
   $obTxtTipoSuspensao->setNull          ( false                               );
   $obTxtTipoSuspensao->setInteiro       ( true                                );

   $obCmbTipoSuspensao = new Select;
   $obCmbTipoSuspensao->setName          ( "cmbTipoSuspensao"                  );
   $obCmbTipoSuspensao->addOption        ( "", "Selecione"                     );
   $obCmbTipoSuspensao->setCampoId       ( "cod_tipo_suspensao"                );
   $obCmbTipoSuspensao->setCampoDesc     ( "descricao"                         );
   $obCmbTipoSuspensao->preencheCombo    ( $rsTipoSuspensao                    );
   $obCmbTipoSuspensao->setValue         ( $_REQUEST["inCodigoTipoSuspensao"]  );
   $obCmbTipoSuspensao->setNull          ( false                               );
   $obCmbTipoSuspensao->setStyle         ( "width: 220px"                      );
} else {
   $obTipoSuspensao = new label;
   $obTipoSuspensao->setRotulo ( "Tipo Suspensão"             );
   $obTipoSuspensao->setTitle  ( "Tipo de suspensão"          );
   $obTipoSuspensao->setName   ( "stTipoSuspensao"            );
   $obTipoSuspensao->setId     ( "stTipoSuspensao"            );
   $obTipoSuspensao->setValue  ( $_REQUEST["stTipoSuspensao"] );
}

if ($stAcao == "incluir") {
   $obDataInicio = new Data;
   $obDataInicio->setName        ( "dtInicio"        );
   $obDataInicio->setId          ( "dtInicio"        );
   $obDataInicio->setTitle       ( "Data de Início." );
   $obDataInicio->setMaxLength   ( 10                );
   $obDataInicio->setSize        ( 10                );
   $obDataInicio->setRotulo      ( "Início"          );
   $obDataInicio->setNull        ( false             );
   $obDataInicio->setValue       ( ""                );
} else {
   $obDataInicio = new label;
   $obDataInicio->setRotulo ( "Início"              );
   $obDataInicio->setTitle  ( "Data Início"         );
   $obDataInicio->setName   ( "dtInicio"            );
   $obDataInicio->setId     ( "dtInicio"            );
   $obDataInicio->setValue  ( $_REQUEST["dtInicio"] );
}
$obDataTermino = new Data;
$obDataTermino->setName        ( "dtTermino"                     );
$obDataTermino->setId          ( "dtTermino"                     );
$obDataTermino->setTitle       ( "Data de Término."              );
$obDataTermino->setMaxLength   ( 10                              );
$obDataTermino->setSize        ( 10                              );
$obDataTermino->setRotulo      ( "Término"                       );
$obDataTermino->setNull        ( $stAcao == "incluir"?true:false );
$obDataTermino->setValue       ( ""                              );

$obObservacao = new textarea;
$obObservacao->setName        ( "stObservacao"     );
$obObservacao->setId          ( "stObservacao"     );
$obObservacao->setTitle       ( "Observações."     );
$obObservacao->setRotulo      ( "Observação"       );
$obObservacao->setNull        ( false              );
$obObservacao->setValue       ( ""                 );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obPopUpProcesso = new IPopUpProcesso($obForm);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                );
$obFormulario->setAjuda      ( "UC-05.03.08"          );
$obFormulario->addTitulo     ( "Suspensão de Crédito" );

$obFormulario->addHidden     ( $obHdnAcao          );
$obFormulario->addHidden     ( $obHdnCtrl          );
$obFormulario->addHidden     ( $obHdnCodLancamento );
if( $stAcao == "alterar")
   $obFormulario->addHidden  ( $obHdnCodSuspensao  );

$obFormulario->addComponente( $obCGM           );
$obFormulario->addComponente( $obOrigemCobranca );
if ($stAcao == "incluir") {
   $obFormulario->addComponenteComposto( $obTxtTipoSuspensao,$obCmbTipoSuspensao );
} else {
   $obFormulario->addComponente( $obTipoSuspensao );
}
$obFormulario->addComponente( $obDataInicio    );
$obFormulario->addComponente( $obDataTermino   );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obObservacao    );

$obFormulario->Cancelar();
$obFormulario->show();

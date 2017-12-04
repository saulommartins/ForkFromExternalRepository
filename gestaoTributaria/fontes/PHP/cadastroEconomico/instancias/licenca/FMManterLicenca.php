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
    * Formulario para Licença - Baixa
    * Data de Criação   : 02/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra

    * $Id: FMManterLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.8  2006/10/17 16:35:55  dibueno
Utilização de componentes para BuscaInners

Revision 1.7  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GA_PROT_CLASSES."componentes/IPopUpProcesso.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterLicenca";
$pgFilt          = "FL".$stPrograma.".php";
$pgFiltAlterar   = "FLAlterarLicenca.php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormAtividade = "FMConcederLicencaAtividade.php";
$pgFormEspecial  = "FMConcederLicencaEspecial.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();

// DEFINE OBJETOS DAS CLASSES
$obRCEMLicenca = new RCEMLicenca;
$arConfiguracao = array();
$obRCEMLicenca->recuperaConfiguracao( $arConfiguracao , $sessao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName               ( "stCtrl"                          );
$obHdnCtrl->setValue              ( $_REQUEST["stCtrl"]               );

$obHdnAcao = new Hidden;
$obHdnAcao->setName               ( "stAcao"                          );
$obHdnAcao->setValue              ( $_REQUEST["stAcao"]               );

$obHdnCodigoLicenca = new Hidden;
$obHdnCodigoLicenca->setName      ( "inCodigoLicenca"                 );
$obHdnCodigoLicenca->setValue     ( $_REQUEST["inCodigoLicenca"]."/".$_REQUEST["stExercicio"] );

$obHdnDataSuspensao = new Hidden;
$obHdnDataSuspensao->setName      ( "hdnDtDataSuspensao"              );
$obHdnDataSuspensao->setValue     ( $_REQUEST["dtDataSuspensao"]      );

$obHdnDataConcessao = new Hidden;
$obHdnDataConcessao->setName      ( "hdnDtDataConcessao"              );
$obHdnDataConcessao->setValue     ( $_REQUEST["dtDataConcessao"]      );

$obHdnStMotivo = new Hidden;
$obHdnStMotivo->setName           ( "hdnStMotivo"                     );
$obHdnStMotivo->setValue          ( $_REQUEST["stMotivo"]             );

$obHdnExercicioProcesso = new Hidden;
$obHdnExercicioProcesso->setName  ( "hdnExercicioProcesso"            );
$obHdnExercicioProcesso->setValue ( $_REQUEST["hdnExercicioProcesso"] );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR
// LABELS PARA ALTERACAO, BAIXA E HISTORICO

$obLblCodigoLicenca = new Label;
$obLblCodigoLicenca->setRotulo        ( "Número da Licença"                 );
$obLblCodigoLicenca->setName          ( "lblCodigoLicenca"                  );
if ($arConfiguracao['numero_licenca'] == 2) {
    $obLblCodigoLicenca->setValue         ( $_REQUEST["inCodigoLicenca"]."/".$_REQUEST["stExercicio"] );
} else {
    $obLblCodigoLicenca->setValue         ( $_REQUEST["inCodigoLicenca"] );
}

$obLblNomeCGM = new Label;
$obLblNomeCGM->setRotulo              ( "Nome do CGM"                       );
$obLblNomeCGM->setName                ( "stNomeCGM"                         );
$obLblNomeCGM->setValue               ( $_REQUEST["stNomeCGM"]              );

$obLblProcesso = new Label;
$obLblProcesso->setRotulo             ( "Processo"                          );
$obLblProcesso->setName               ( "inCodigoProcesso"                  );
$obLblProcesso->setValue              ( $_REQUEST["inCodigoProcesso"]       );

$obLblDataSuspensao = new Label;
$obLblDataSuspensao->setRotulo        ( "Data da Suspensão"                 );
$obLblDataSuspensao->setName          ( "dtDataSuspensao"                   );
$obLblDataSuspensao->setValue         ( $_REQUEST["dtDataSuspensao"]        );

//COMPONENTES PARA INCLUSAO E ALTERACAO

if ($_REQUEST["stAcao"] == "baixar") {
    $stTituloProcesso = "Processo Referente ao Pedido de Baixa";
} elseif ($_REQUEST["stAcao"] == "cassar") {
    $stTituloProcesso = "Processo Referente ao Pedido de Cassação";
} elseif ($_REQUEST["stAcao"] == "suspender") {
    $stTituloProcesso = "Processo Referente ao Pedido de Suspensão";
}

$obDtDataBaixa = new Data;
$obDtDataBaixa->setName               ( "dtDataBaixa"                       );
$obDtDataBaixa->setValue              ( $_REQUEST["dtDataBaixa"]            );
$obDtDataBaixa->setRotulo             ( "Data da Baixa"                     );
$obDtDataBaixa->setMaxLength          ( 20                                  );
$obDtDataBaixa->setSize               ( 10                                  );
$obDtDataBaixa->setNull               ( false                               );
$obDtDataBaixa->obEvento->setOnChange ( "validaDataBaixa();"                );

$obDtDataSuspensao = new Data;
$obDtDataSuspensao->setName           ( "dtDataSuspensao"                   );
$obDtDataSuspensao->setValue          ( $_REQUEST["dtDataSuspensao"]        );
$obDtDataSuspensao->setRotulo         ( "Data da Suspensão"                 );
$obDtDataSuspensao->setMaxLength      ( 20                                  );
$obDtDataSuspensao->setSize           ( 10                                  );
$obDtDataSuspensao->setNull           ( false                               );
$obDtDataSuspensao->obEvento->setOnChange ( "validaDataSuspensao();"        );

$obDtDataTermino = new Data;
$obDtDataTermino->setName             ( "dtDataTermino"                     );
$obDtDataTermino->setValue            ( $_REQUEST["dtDataTermino"]          );
$obDtDataTermino->setRotulo           ( "Data de Término"                   );
$obDtDataTermino->setMaxLength        ( 20                                  );
$obDtDataTermino->setSize             ( 10                                  );
if ($_REQUEST["stAcao"] == "suspender") {
   $obDtDataTermino->setNull         ( true                                 );
   //$obDtDataTermino->obEvento->setOnChange ( "validaDataTerminoSuspensao();");
} else {
   $obDtDataTermino->setNull         ( false                                );
   $obDtDataTermino->obEvento->setOnChange ( "validaDataTerminoSuspensao();");

}

$obDtDataCassacao = new Data;
$obDtDataCassacao->setName            ( "dtDataCassacao"                    );
$obDtDataCassacao->setValue           ( $_REQUEST["dtDataCassacao"]         );
$obDtDataCassacao->setRotulo          ( "Data da Cassação"                  );
$obDtDataCassacao->setMaxLength       ( 20                                  );
$obDtDataCassacao->setSize            ( 10                                  );
$obDtDataCassacao->setNull            ( false                               );

$obTxtMotivo = new TextArea;
$obTxtMotivo->setRotulo               ( "Motivo"                            );
$obTxtMotivo->setName                 ( "stMotivo"                          );
$obTxtMotivo->setValue                ( $_REQUEST["stMotivo"]               );
if ($_REQUEST["stAcao"]       == "baixar") {
    $obTxtMotivo->setTitle            ( "Motivo da Baixa"                   );
    $obTxtMotivo->setNull             ( false                               );
} elseif ($_REQUEST["stAcao"] == "cassar") {
    $obTxtMotivo->setTitle            ( "Motivo da Cassação"                );
    $obTxtMotivo->setNull             ( false                               );
} elseif ($_REQUEST["stAcao"] == "suspender") {
    $obTxtMotivo->setTitle            ( "Motivo da Suspensão"               );
    $obTxtMotivo->setNull             ( false                               );
} elseif ($_REQUEST["stAcao"] == "cancelar") {
    $obTxtMotivo->setNull             ( true                                );
    $obTxtMotivo->setReadOnly         ( true                                );
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm     ( $obForm                 );
$obFormulario->addHidden   ( $obHdnCtrl              );
$obFormulario->addHidden   ( $obHdnAcao              );
$obFormulario->addHidden   ( $obHdnCodigoLicenca     );
$obFormulario->addHidden   ( $obHdnExercicioProcesso );

$obFormulario->addTitulo   ( "Dados para Licença" );

$obPopUpProcesso = new IPopUpProcesso ( $obForm );
$obPopUpProcesso->setNull ( true );
$obPopUpProcesso->setTitle ( $stTituloProcesso );
$obPopUpProcesso->obCampoCod->setName("inCodigoProcesso");
$obPopUpProcesso->montaHTML ( );

if ($stAcao == "baixar") {
    $obFormulario->addHidden     ( $obHdnDataConcessao );
    $obFormulario->addComponente ( $obLblCodigoLicenca );
    $obFormulario->addComponente ( $obLblNomeCGM       );

    $obFormulario->addComponente ( $obPopUpProcesso );

    $obFormulario->addComponente ( $obDtDataBaixa      );
    $obFormulario->addComponente ( $obTxtMotivo        );
} elseif ($stAcao == "suspender") {
    $obFormulario->addHidden     ( $obHdnDataConcessao );
    $obFormulario->addComponente ( $obLblCodigoLicenca );
    $obFormulario->addComponente ( $obLblNomeCGM       );

    $obFormulario->addComponente ( $obPopUpProcesso    );

    $obFormulario->addComponente ( $obDtDataSuspensao  );
    $obFormulario->addComponente ( $obDtDataTermino    );
    $obFormulario->addComponente ( $obTxtMotivo        );
} elseif ($stAcao == "cancelar") {
    $obFormulario->addHidden     ( $obHdnDataSuspensao );
    $obFormulario->addHidden     ( $obHdnStMotivo      );
    $obFormulario->addComponente ( $obLblCodigoLicenca );
    $obFormulario->addComponente ( $obLblNomeCGM       );
    $obFormulario->addComponente ( $obLblProcesso      );
    $obFormulario->addComponente ( $obLblDataSuspensao );
    $obFormulario->addComponente ( $obDtDataTermino    );
    $obFormulario->addComponente ( $obTxtMotivo        );
} elseif ($stAcao == "cassar") {
    $obFormulario->addHidden     ( $obHdnDataConcessao );
    $obFormulario->addComponente ( $obLblCodigoLicenca );
    $obFormulario->addComponente ( $obLblNomeCGM       );

    $obFormulario->addComponente ( $obPopUpProcesso    );

    $obFormulario->addComponente ( $obDtDataCassacao   );
    $obFormulario->addComponente ( $obTxtMotivo        );
}
$obFormulario->Cancelar();
$obFormulario->show ();

?>

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
    * Página de alteração de características para o cadastro de edificação
    * Data de Criação   : 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Leusin Oigen

    * @ignore

    * $Id: FMManterEdificacaoCaracteristica.php 63277 2015-08-12 12:59:36Z arthur $

    * Casos de uso: uc-05.01.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTipoEdificacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEdificacao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// DEFINE OBJETOS DAS CLASSES
$obRCIMEdificacao     = new RCIMEdificacao;
$obRCIMTipoEdificacao = new RCIMTipoEdificacao;
$obRCIMConfiguracao   = new RCIMConfiguracao;
$rsTipoEdificacao     = new RecordSet;
$rsAtributos          = new RecordSet;

$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMConfiguracao->getMascaraIM();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

//RECUPERA E MONTA OS ATRIBUTOS DA EDIFICAÇÃO SELECIONADA
$obRCIMEdificacao = new RCIMEdificacao;

$obRCIMEdificacao->setCodigoConstrucao( $_REQUEST["inCodigoConstrucao"] );
$obRCIMEdificacao->listarProcessos( $rsListaProcesso );

// Preenche RecordSet
$obRCIMTipoEdificacao->listarTiposEdificacao( $rsTipoEdificacao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $_REQUEST['stCtrl']           );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $_REQUEST['stAcao']           );

$obHdnCodigoConstrucao = new Hidden;
$obHdnCodigoConstrucao->setName          ( "hdnCodigoConstrucao"         );
$obHdnCodigoConstrucao->setValue         ( $_REQUEST['inCodigoConstrucao']);

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName                ( "hdnCodigoTipo"               );
$obHdnCodigoTipo->setValue               ( $_REQUEST['inCodigoTipo']     );

$obHdnImovelCond = new Hidden;
$obHdnImovelCond->setName                ( "hdnImovelCond"               );
$obHdnImovelCond->setValue               ( $_REQUEST['stImovelCond']     );

$obStImovelCond = new Hidden;
$obStImovelCond->setName                 ( "stImovelCond"                );
$obStImovelCond->setValue                ( $_REQUEST['stImovelCond']     );

$obHdnCodigoConstrucaoAutonoma = new Hidden;
$obHdnCodigoConstrucaoAutonoma->setName  ( "hdnCodigoConstrucaoAutonoma" );
$obHdnCodigoConstrucaoAutonoma->setValue ( $_REQUEST['hdnCodigoConstrucaoAutonoma']  );

$obHdnCodigoTipoAutonoma = new Hidden;
$obHdnCodigoTipoAutonoma->setName        ( "hdnCodigoTipoAutonoma"       );
$obHdnCodigoTipoAutonoma->setValue       ( $_REQUEST['hdnCodigoTipoAutonoma']        );

$obHdnTipoUnidade = new Hidden;
$obHdnTipoUnidade->setName               ( "hdnTipoUnidade"              );
$obHdnTipoUnidade->setValue              ( $_REQUEST['stTipoUnidade']                );

$obHdnVinculoEdificacao = new Hidden;
$obHdnVinculoEdificacao->setName         ( "hdnVinculoEdificacao"        );
$obHdnVinculoEdificacao->setValue        ( $_REQUEST['boVinculoEdificacao']          );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR

// LABELS PARA ALTERACAO, BAIXA E HISTORICO
//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

include_once 'FMManterEdificacaoCaracteristicaAbaCaracteristica.php';
include_once 'FMManterEdificacaoCaracteristicaAbaProcesso.php';

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm   ( $obForm                        );
$obFormulario->setAjuda ( "UC-05.01.11" );
$obFormulario->addHidden ( $obHdnCtrl                     );
$obFormulario->addHidden ( $obHdnAcao                     );
$obFormulario->addHidden ( $obHdnCodigoConstrucao         );
$obFormulario->addHidden ( $obHdnCodigoTipo               );
$obFormulario->addHidden ( $obHdnCodigoConstrucaoAutonoma );
$obFormulario->addHidden ( $obHdnCodigoTipoAutonoma       );
$obFormulario->addHidden ( $obHdnTipoUnidade              );
$obFormulario->addHidden ( $obHdnVinculoEdificacao        );

$obFormulario->addAba       ( "Características"     );
$obFormulario->addTitulo    ( "Dados para Edificação"   );
$obFormulario->addHidden          ( $obStImovelCond             );
$obFormulario->addComponente      ( $obLblCodigoEdificacao      );
$obFormulario->addComponente      ( $obLblTipoEdificacao        );
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obFormulario->addComponente  ( $obLblNomeCondominio        );
} elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obFormulario->addComponente  ( $obLblInscricaoImobiliaria  );
    $obFormulario->addComponente  ( $obLblTipoUnidade           );
}
$obFormulario->addComponente( $obBscProcesso        );
$obMontaAtributosEdificacao->geraFormulario ( $obFormulario     );

$obFormulario->addAba       ( "Processos"     );
$obFormulario->addTitulo    ( "Dados para Edificação"   );
$obFormulario->addHidden          ( $obStImovelCond             );
$obFormulario->addComponente      ( $obLblCodigoEdificacao      );
$obFormulario->addComponente      ( $obLblTipoEdificacao        );
if ($_REQUEST["boVinculoEdificacao"] == "Condomínio") {
    $obFormulario->addComponente  ( $obLblNomeCondominio        );
} elseif ($_REQUEST["boVinculoEdificacao"] == "Imóvel") {
    $obFormulario->addComponente  ( $obLblInscricaoImobiliaria  );
    $obFormulario->addComponente  ( $obLblTipoUnidade           );
}
$obFormulario->addSpan      ( $obSpnProcesso        );
$obFormulario->addSpan      ( $obSpnAtributosProcesso );

$obFormulario->Cancelar ();
$obFormulario->setFormFocus( $obBscProcesso->obCampoCod->getid() );
$obFormulario->show ();

?>
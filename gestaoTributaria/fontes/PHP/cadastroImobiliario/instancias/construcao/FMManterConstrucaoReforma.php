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
    * Página de formulário para inclusão de reforma de construção
    * Data de Criação   : 10/01/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Stephanou

    * @ignore

    * $Id: FMManterConstrucaoReforma.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLote.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConstrucao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once $pgJs;

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "incluir";
}

$obRCIMConstrucao = new RCIMConstrucaoOutros;

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl"  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( $stAcao   );

$obHdnTipo = new Hidden;
$obHdnTipo->setName ( "stTipoVinculo"  );
$obHdnTipo->setValue($_REQUEST["stTipoVinculo"]);

$obHdnCodConstrucao = new Hidden;
$obHdnCodConstrucao->setName ( "inCodigoConstrucao"  );
$obHdnCodConstrucao->setValue($_REQUEST["inCodigoConstrucao"]);

$obHdnInscricao = new Hidden;
$obHdnInscricao->setName ( "inNumeroInscricao"  );
$obHdnInscricao->setValue($_REQUEST["inNumeroInscricao"]);

$obLblCodigoConstrucao = new Label;
$obLblCodigoConstrucao->setName     ( "stCodigoConstrucao" );
$obLblCodigoConstrucao->setRotulo   ( "Código" );
$obLblCodigoConstrucao->setValue    ( $_REQUEST["inCodigoConstrucao"] );

$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setName      ( "inNumeroInscricao" );
$obLblNumeroInscricao->setRotulo    ( "Inscrição Imobiliária" );
$obLblNumeroInscricao->setTitle     ( "Inscrição imobiliária com a qual a construção está vinculada" );
$obLblNumeroInscricao->setValue     ( $_REQUEST["inNumeroInscricao"] );

$obLblNomeCondominio = new Label;
$obLblNomeCondominio->setName      ( "inNomeCondominio" );
$obLblNomeCondominio->setRotulo    ( "Condomínio" );
$obLblNomeCondominio->setTitle     ( "Nome do Condomínio a qual a construção esta vinculada" );
$obLblNomeCondominio->setValue     ( $_REQUEST["stNomeCond"] );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaDado('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obTxtAreaConstrucao = new Numerico;
$obTxtAreaConstrucao->setName      ( "flAreaConstrucao" );
$obTxtAreaConstrucao->setId        ( "flAreaConstrucao" );
$obTxtAreaConstrucao->setRotulo    ( "Área" );
$obTxtAreaConstrucao->setMaxValue  ( 999999999999.99    );
$obTxtAreaConstrucao->setSize      ( 18 );
$obTxtAreaConstrucao->setMaxLength ( 18 );
$obTxtAreaConstrucao->setNull      ( false );
$obTxtAreaConstrucao->setNegativo  ( false );
$obTxtAreaConstrucao->setNaoZero   ( true );
$obTxtAreaConstrucao->setTitle     ( "Área construída em metros quadrados" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->setAjuda             ( "UC-05.01.12" );
$obFormulario->addForm            ( $obForm                     );
$obFormulario->addHidden          ( $obHdnCtrl                  );
$obFormulario->addHidden          ( $obHdnAcao                  );
$obFormulario->addHidden          ( $obHdnTipo                  );
$obFormulario->addHidden          ( $obHdnCodConstrucao         );
$obFormulario->addHidden          ( $obHdnInscricao             );
$obFormulario->addAba             ( "Construção"                );
$obFormulario->addTitulo          ( "Dados para Reforma"        );
$obFormulario->addComponente      ( $obLblCodigoConstrucao      );

if ($_REQUEST["stTipoVinculo"] == "condominio") {
    $obFormulario->addComponente  ( $obLblNomeCondominio        );
} else {
    $obFormulario->addComponente  ( $obLblNumeroInscricao       );
}

$obFormulario->addComponente      ( $obTxtAreaConstrucao        );
$obFormulario->addComponente      ( $obBscProcesso              );
$obFormulario->addAba             ( "Características"           );

$obFormulario->Cancelar();
$obFormulario->setFormFocus( $obTxtAreaConstrucao->getId() );
$obFormulario->show();

?>

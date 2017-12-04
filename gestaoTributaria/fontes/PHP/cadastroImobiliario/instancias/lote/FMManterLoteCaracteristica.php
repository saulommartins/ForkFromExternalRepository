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
    * Página de Formulário para alteração de características de lote
    * Data de Criação   : 29/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterLoteCaracteristica.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterLote";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );
//include_once( $pgOcul );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "historico";
}

//Recupera mascara do processo
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );
$stSeparador = preg_replace( "/[a-zA-Z0-9]/","", $stMascaraProcesso );

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
}

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );

$obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
$obRCIMLote->consultarLote();

$obRCIMLote->listarProcessos( $rsListaProcesso );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdMascaraLote = new Hidden;
$obHdMascaraLote->setName ( "hdnMascaraLote"   );
$obHdMascaraLote->setValue( $request->get('stMascaraLote'));

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $request->get("funcionalidade") );

$obHdnTrecho = new Hidden;
$obHdnTrecho->setName( "stTrecho" );
$obHdnTrecho->setValue( $request->get('stTrecho') );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName ( "inCodigoUF" );
$obHdnCodigoUF->setValue ( $obRCIMLote->obRCIMBairro->getCodigoUF() );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName( "inCodigoMunicipio" );
$obHdnCodigoMunicipio->setValue( $obRCIMLote->obRCIMBairro->getCodigoMunicipio() );

//Alateracao
$obHdnCodigoLote = new Hidden;
$obHdnCodigoLote->setName  ( "inCodigoLote" );
$obHdnCodigoLote->setValue ( $request->get("inCodigoLote") );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue( $request->get("inCodigoLocalizacao") );

$obHdnNumeroLote = new Hidden;
$obHdnNumeroLote->setName  ( "stNumeroLote" );
$obHdnNumeroLote->setValue ( $obRCIMLote->getNumeroLote() );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

include_once 'FMManterLoteCaracteristicaAbaCaracteristica.php';
include_once 'FMManterLoteCaracteristicaAbaProcesso.php';

$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm               );
$obFormulario->setAjuda ( "UC-05.01.08" );
$obFormulario->addHidden          ( $obHdnCtrl            );
$obFormulario->addHidden          ( $obHdnFuncionalidade  );
$obFormulario->addHidden          ( $obHdnCodigoLote      );
$obFormulario->addHidden          ( $obHdMascaraLote      );
$obFormulario->addHidden          ( $obHdnAcao            );
$obFormulario->addHidden          ( $obHdnTrecho          );
$obFormulario->addHidden          ( $obHdnCodigoUF        );
$obFormulario->addHidden          ( $obHdnCodigoMunicipio );
$obFormulario->addHidden          ( $obHdnNumeroLote      );

$obFormulario->addAba       ( "Características"     );
$obFormulario->addTitulo    ( "Dados para Lote"   );
$obFormulario->addComponente      ( $obTxtNumeroLote      );
$obFormulario->addComponente( $obBscProcesso        );
$obMontaAtributos->geraFormulario ( $obFormulario         );

$obFormulario->addAba       ( "Processos" );
$obFormulario->addTitulo    ( "Dados para Lote");
$obFormulario->addComponente( $obTxtNumeroLote      );
$obFormulario->addSpan      ( $obSpnProcesso        );
$obFormulario->addSpan      ( $obSpnAtributosProcesso );

$obFormulario->Cancelar           ( $pgList               );
$obFormulario->setFormFocus ( $obBscProcesso->obCampoCod->getId() );
$obFormulario->show();
?>

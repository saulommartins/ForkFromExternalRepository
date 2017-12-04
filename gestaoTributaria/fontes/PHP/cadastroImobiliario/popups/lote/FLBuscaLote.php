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
    * Página de filtro para o cadastro de lote
    * Data de Criação   : 14/03/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: FLBuscaLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GT_CIM_COMPONENTES."MontaLocalizacaoCombos.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "BuscaLote";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once $pgJs;

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}
Sessao::remove('link');

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($request->get("funcionalidade") == 178 ) {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($request->get("funcionalidade") == 193 ) {
    $obRCIMLote = new RCIMLoteRural;
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$obMontaLocalizacaoCombos = new MontaLocalizacaoCombos;
$obMontaLocalizacaoCombos->boPopUp = true;
$obMontaLocalizacaoCombos->setObrigatorio        ( false );
$obMontaLocalizacaoCombos->setCadastroLocalizacao( false );

$inCodigoNivel = Sessao::read('inCodigoNivel');

if ( Sessao::read('inNumLote')) {
    $obMontaLocalizacaoCombos->setValorReduzido( Sessao::read('inNumLote'));
    $obMontaLocalizacaoCombos->preencheCombos();
}

if ($inCodigoNivel) {
    $obMontaLocalizacaoCombos->setCadastroLocalizacao( true );
    $obMontaLocalizacaoCombos->setCodigoNivel        ( $inCodigoNivel );
}

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $request->get("funcionalidade") );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

$obHdnChaveLocalizacao = new Hidden;
$obHdnChaveLocalizacao->setName ( "stNomeChaveLocalizacao" );
$obHdnChaveLocalizacao->setValue( $request->get('stNomeChaveLocalizacao') );

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

$obTxtNumeroLote = new TextBox;
$obTxtNumeroLote->setName      ( "stNumeroLote"           );
$obTxtNumeroLote->setMaxLength ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setSize      ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setRotulo    ( "Número do Lote"         );
$obTxtNumeroLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );

$obBtnOK = new OK;

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limparFiltro()" );

$obIFrame2 = new IFrame;
$obIFrame2->setName("telaMensagem");
$obIFrame2->setWidth("100%");
$obIFrame2->setHeight("50");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm               );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addHidden            ( $obHdnFuncionalidade  );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addHidden            ( $obHdnCampoNom        );
$obFormulario->addHidden            ( $obHdnCampoNum        );
$obFormulario->addHidden            ( $obHdnChaveLocalizacao );
$obFormulario->addTitulo            ( "Dados para filtro"   );
$obFormulario->addComponente        ( $obTxtNumeroLote      );
$obMontaLocalizacaoCombos->geraFormulario ( $obFormulario         );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();

if ( Sessao::read('inNumLote')) {
 $jsOnload = "document.getElementById('Ok').click();";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

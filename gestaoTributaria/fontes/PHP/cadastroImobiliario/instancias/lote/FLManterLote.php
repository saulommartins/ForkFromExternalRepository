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
    * Data de Criação   : 30/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FLManterLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:54  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLote";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
$arConfrontacoesSessao = array();
Sessao::write('confrontacoes', $arConfrontacoesSessao);

Sessao::remove('link');

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
}

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraLote = $obRCIMConfiguracao->getMascaraLote();

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );
$obMontaLocalizacao->setObrigatorio        ( false );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $_REQUEST["funcionalidade"] );

$obTxtNumeroLote = new TextBox;
$obTxtNumeroLote->setName      ( "stNumeroLote"           );
$obTxtNumeroLote->setId        ( "stNumeroLote"           );
$obTxtNumeroLote->setMaxLength ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setSize      ( strlen( $stMascaraLote ) );
$obTxtNumeroLote->setRotulo    ( "Número do Lote"         );
$obTxtNumeroLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick    ( "submeteFiltro();" );

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limparFiltro()" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm               );
$obFormulario->setAjuda ( "UC-05.01.08" );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addHidden            ( $obHdnFuncionalidade  );
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addTitulo            ( "Dados para filtro"   );
$obFormulario->addComponente        ( $obTxtNumeroLote      );
$obMontaLocalizacao->geraFormulario ( $obFormulario         );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->setFormFocus         ( $obTxtNumeroLote->getId() );
$obFormulario->show();
?>

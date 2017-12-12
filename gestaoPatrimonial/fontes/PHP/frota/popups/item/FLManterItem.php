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
 * Página de Filtro Classificação Contábil
 * Data de Criação   : 10/11/2004

 * @author Analista: Diego Victoria
 * @author Desenvolvedor: Leandro André Zis

 * Casos de uso: uc-03.02.12

 $Id: FLManterItem.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaTipoItem.class.php";

$stPrograma = "ManterItem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$pgProx = $pgList;

include_once $pgJs;

//***********************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/
Sessao::remove('filtro');
Sessao::remove('link');

Sessao::write('transf4' , array());

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnTipoConsulta = new Hidden();
$obHdnTipoConsulta->setName( 'stTipoConsulta' );
$obHdnTipoConsulta->setValue( $_REQUEST['stTipoConsulta'] );

if ( $request->get('nomCampoUnidade') ) {
    $obHdnUnidade = new Hidden;
    $obHdnUnidade->setName ( "nomCampoUnidade" );
    $obHdnUnidade->setValue( $_REQUEST['nomCampoUnidade'] );
}

$obTxtCodItem = new TextBox();
$obTxtCodItem->setRotulo ( "Código Item" );
$obTxtCodItem->setName   ( "inCodItem"   );
$obTxtCodItem->setValue  ( isset($inCodItem) ? $inCodItem : null  );

$obTxtDescricao = new TextBox();
$obTxtDescricao->setRotulo      ( "Descrição"   );
$obTxtDescricao->setName        ( "stDescricao" );
$obTxtDescricao->setValue       ( isset($stDescricao) ? $stDescricao : null );
$obTxtDescricao->setMaxLength   ( 80            );
$obTxtDescricao->setSize        ( 50            );

$obCmpTipoBusca = new TipoBusca( $obTxtDescricao );

if ($_REQUEST['stTipoConsulta'] == 'sem_combustivel') {
    $stFiltro = " WHERE  tipo_item.cod_tipo <> 1 ";
}

$obTFrotaTipoItem = new TFrotaTipoItem();

$obTFrotaTipoItem->recuperaTodos( $rsTipo, $stFiltro );

$arRdTipo = array();
$inCodTipo = 0;

$obRdTipo = new Radio;
$obRdTipo->setRotulo   ( "Tipo" );
$obRdTipo->setTitle    ( "Selecione o tipo de item desejado." );
$obRdTipo->setName     ( "inCodTipo" );
$obRdTipo->setLabel    ( "Todos" );
$obRdTipo->setValue    ( "" );
$obRdTipo->setChecked  ( true );
$obRdTipo->setNull     ( false );
$arRdTipo[] = $obRdTipo;

for ($i = 0; $i < $rsTipo->getNumLinhas(); $i++) {
   $obRdTipo = new Radio;
   $obRdTipo->setRotulo   ( "Tipo" );
   $obRdTipo->setName     ( "inCodTipo" );
   $obRdTipo->setLabel    ( $rsTipo->getCampo('descricao') );
   $obRdTipo->setValue    ( $rsTipo->getCampo('cod_tipo') );
   $obRdTipo->setChecked  ( $inCodTipo == $rsTipo->getCampo('cod_tipo') );
   $obRdTipo->setNull     ( false );
   $arRdTipo[] = $obRdTipo;
   $rsTipo->proximo();
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnTipoConsulta );
$obFormulario->addHidden ( $obHdnForm              );
$obFormulario->addHidden ( $obHdnCampoNum          );
$obFormulario->addHidden ( $obHdnCampoNom          );
if ( $request->get('nomCampoUnidade') ) {
    $obFormulario->addHidden( $obHdnUnidade );
}
$obFormulario->addTitulo         ( "Dados para Filtro" );
$obFormulario->addComponente     ( $obTxtCodItem );
$obFormulario->addComponente     ( $obCmpTipoBusca );
$obFormulario->agrupaComponentes ( $arRdTipo );

$obFormulario->OK   ();
$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

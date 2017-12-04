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
    * Pagina de Formulario de Inclusao/Alteracao de ACRESCIMO

    * Data de Criacao: 08/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterAcrescimo.php 65344 2016-05-13 18:00:50Z jean $

    *Casos de uso: uc-05.05.11

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimoNorma.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."IPopUpFuncao.class.php" );

$obRMONAcrescimo =  new RMONAcrescimo;
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterAcrescimo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//$stCaminho   = CAM_GT_MON_INSTANCIAS."especie/";

/***********************************************/

include_once ( $pgJs );
if ($stAcao == 'alterar') {
    $stCodFuncao = sprintf( "%02d.%02d.%03d", $_REQUEST['inCodModulo'], $_REQUEST['inCodBiblioteca'], $_REQUEST['inCodFuncao'] );
    $stNomFuncao = $_REQUEST['stNomFuncao'];
//    Sessao::remove('link');
//    Sessao::remove('stLink');

    $stFiltro = " WHERE acrescimo_norma.cod_acrescimo = ".$_REQUEST['inCodAcrescimo']." AND acrescimo_norma.cod_tipo = ".$_REQUEST['inCodTipo']." ORDER BY TIMESTAMP DESC LIMIT 1";
    $obTMONAcrescimoNorma = new TMONAcrescimoNorma;
    $obTMONAcrescimoNorma->listaAcrescimoNorma( $rsNorma, $stFiltro );

    $inCodNorma = $rsNorma->getCampo( "cod_norma" );
    $stNomeNorma = $rsNorma->getCampo( "nom_norma" );
}
//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $stCtrl );

$obHdnCodAcrescimo = new Hidden;
$obHdnCodAcrescimo->setName  ('inCodAcrescimo');
$obHdnCodAcrescimo->setValue ( $_REQUEST['inCodAcrescimo'] );

$obHdnCodTipo = new Hidden;
$obHdnCodTipo->setName  ('inCodTipo');
$obHdnCodTipo->setValue ( $_REQUEST['inCodTipo'] );

$obHdnDtVigencia = new Hidden;
$obHdnDtVigencia->setName  ('dtVigencia');
$obHdnDtVigencia->setValue ( $dia );
//echo $dia;
//echo 'Vigencia: '.$_REQUEST['dtVigencia'];

$obTxtCodAcrescimo = new TextBox;
$obTxtCodAcrescimo->setRotulo  ( 'Código');
$obTxtCodAcrescimo->setTitle   ( 'Código do Acréscimo');
$obTxtCodAcrescimo->setName    ( 'inCodAcrescimo');
$obTxtCodAcrescimo->setValue   ( $_REQUEST["inCodAcrescimo"] );
$obTxtCodAcrescimo->setInteiro ( false );
$obTxtCodAcrescimo->setSize    ( 10 );
$obTxtCodAcrescimo->setMaxLength ( 10 );
$obTxtCodAcrescimo->setNull    ( false );

$obTxtDescAcrescimo = new TextBox;
$obTxtDescAcrescimo->setRotulo  ( 'Descrição');
$obTxtDescAcrescimo->setTitle   ( 'Descrição do Acréscimo Legal');
$obTxtDescAcrescimo->setName    ( 'stDescAcrescimo');
$obTxtDescAcrescimo->setValue   ( $_REQUEST["stDescAcrescimo"] );
$obTxtDescAcrescimo->setInteiro ( false );
$obTxtDescAcrescimo->setSize    ( 80 );
$obTxtDescAcrescimo->setMaxLength ( 80 );
$obTxtDescAcrescimo->setNull    ( false );

$obLblCodAcrescimo = new Label;
$obLblCodAcrescimo->setName   ( 'LabelCodAcrescimo' );
$obLblCodAcrescimo->setTitle  ( 'Código' );
$obLblCodAcrescimo->setRotulo ( 'Código do Acréscimo' );
$obLblCodAcrescimo->setValue  ( $_REQUEST['inCodAcrescimo'] );

$obLblCodTipo = new Label;
$obLblCodTipo->setName   ( 'LabelCodTipo' );
$obLblCodTipo->setTitle  ( 'Tipo do Acréscimo' );
$obLblCodTipo->setRotulo ( 'Tipo do Acréscimo' );
$obLblCodTipo->setValue  ( $_REQUEST['inCodTipo'].' - '.$_REQUEST['stNomTipo'] );

$obLblCodFuncao = new Label;
$obLblCodFuncao->setName  ( 'LabelCodFuncao' );
$obLblCodFuncao->setTitle ( 'Fórmula de Cálculo' );
$obLblCodFuncao->setRotulo( 'Fórmula de Cálculo' );
$obLblCodFuncao->setValue ($_REQUEST['inCodModulo'].'.'.$_REQUEST['inCodBiblioteca'].'.'.$_REQUEST['inCodFuncao']);

$obRMONAcrescimo->ListarTipo ( $rsTipo );

$obCmbTipo = new Select;
$obCmbTipo->setRotulo       ( "Tipo de Acréscimo"    );
$obCmbTipo->setTitle        ( "Tipo de Acréscimo"    );
$obCmbTipo->setName         ( "cmbTipo"              );
$obCmbTipo->addOption       ( "", "Selecione"        );
$obCmbTipo->setValue        ( $_REQUEST['inCodTipo'] );
$obCmbTipo->setCampoId      ( "cod_tipo"             );
$obCmbTipo->setCampoDesc    ( "nom_tipo"             );
$obCmbTipo->preencheCombo   ( $rsTipo                );
$obCmbTipo->setNull         ( false                  );
$obCmbTipo->setStyle        ( "width: 220px"         );

$obBscNorma = new BuscaInner;
$obBscNorma->setRotulo ( "*Fundamentação Legal" );
$obBscNorma->setTitle  ( "Fundamentação Legal que normaliza o acréscimo"  );
$obBscNorma->setId     ( "stNorma"  );
$obBscNorma->obCampoCod->setName   ( "inCodNorma" );
$obBscNorma->obCampoCod->setValue  ( $inCodNorma  );
$obBscNorma->setValue  ( $stNomeNorma );
$obBscNorma->obCampoCod->obEvento->setOnChange("buscaValor('buscaNorma');");
$obBscNorma->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."../../normas/popups/normas/FLNorma.php','frm','inCodNorma','stNorma','todos','".Sessao::getId()."','800','550');" );

$obIPopUpFuncao = new IPopUpFuncao;
$obIPopUpFuncao->setTipoFuncaoBusca( "todas" );
$obIPopUpFuncao->setCodModulo( 28 );
$obIPopUpFuncao->setCodBiblioteca( 2 );
//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ("oculto" );
//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( "UC-05.05.11" );
$obFormulario->addTitulo ('Dados para o Acréscimo');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

if ($stAcao == "alterar") {
    $obFormulario->addHidden     ($obHdnCodAcrescimo);
    $obFormulario->addHidden     ($obHdnCodTipo);
    $obFormulario->addHidden     ($obHdnDtVigencia);
    $obFormulario->addComponente ( $obLblCodAcrescimo );
    $obFormulario->addComponente ( $obLblCodTipo );
    $obFormulario->addComponente ( $obTxtDescAcrescimo );
}

if ($stAcao == "incluir") {
    $obFormulario->addComponente ( $obCmbTipo );
    $obFormulario->addComponente ( $obTxtDescAcrescimo );
}

$obFormulario->addComponente ( $obBscNorma );
$obIPopUpFuncao->setCodFuncao ( $stCodFuncao );
$obIPopUpFuncao->geraFormulario( $obFormulario );

if ($stAcao == "incluir") {
    $obFormulario->ok       ();
} else {
    $obFormulario->cancelar ();
}

$obFormulario->show();

if ($stAcao == 'incluir') {
    $stJs .= 'f.cmbTipo.focus();';
} else {
    $stJs .= 'f.stDescAcrescimo.focus();';
}
sistemaLegado::executaFrameOculto ( $stJs );

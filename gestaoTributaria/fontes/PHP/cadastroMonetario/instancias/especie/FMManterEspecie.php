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
    * Pagina de Formulario de Inclusao/Alteracao de ESPECIE

    * Data de Criação: 08/12/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterEspecie.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.09

*/

/*
$Log$
Revision 1.11  2006/09/15 14:57:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONEspecieCredito.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"    );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEspecie";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//$stCaminho   = CAM_GT_MON_INSTANCIAS."especie/";

/***********************************************/

include_once ( $pgJs );

$obRMONEspecieCredito =  new RMONEspecieCredito;

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnCodEspecie = new Hidden;
$obHdnCodEspecie->setName ('inCodEspecie');
$obHdnCodEspecie->setValue ( $_REQUEST['inCodEspecie'] );

$obHdnCodGenero = new Hidden;
$obHdnCodGenero->setName ('inCodGenero');
$obHdnCodGenero->setValue ( $_REQUEST['inCodGenero'] );

$obHdnCodNatureza = new Hidden;
$obHdnCodNatureza->setName ('inCodNatureza');
$obHdnCodNatureza->setValue ( $_REQUEST['inCodNatureza'] );

$obHdnDescricaoEspecie = new Hidden;
$obHdnDescricaoEspecie->setName ('stDescricaoEspecie');
$obHdnDescricaoEspecie->setValue ( $_REQUEST['stDescricaoEspecie'] );

$obTxtCodNatureza = new TextBox;
$obTxtCodNatureza->setRotulo  ( 'Natureza ');
$obTxtCodNatureza->setTitle   ( 'Natureza da Espécie');
$obTxtCodNatureza->setName    ( 'inCodNatureza');
$obTxtCodNatureza->setValue   ( $_REQUEST["inCodNatureza"] );
$obTxtCodNatureza->setInteiro ( true );
$obTxtCodNatureza->setSize    ( 10 );
$obTxtCodNatureza->setMaxLength ( 10 );
$obTxtCodNatureza->setNull    ( false );

$obTxtCodGenero = new TextBox;
$obTxtCodGenero->setRotulo  ( 'Gênero');
$obTxtCodGenero->setTitle   ( 'Gênero da Espécie');
$obTxtCodGenero->setName    ( 'inCodGenero');
$obTxtCodGenero->setValue   ( $_REQUEST["inCodGenero"] );
$obTxtCodGenero->setInteiro ( true );
$obTxtCodGenero->setSize    ( 10 );
$obTxtCodGenero->setMaxLength ( 10 );
$obTxtCodGenero->setNull    ( false );

$obTxtDescricaoEspecie = new TextBox;
$obTxtDescricaoEspecie->setRotulo  ( 'Descrição');
$obTxtDescricaoEspecie->setTitle   ( 'Descrição da Espécie do Crédito');
$obTxtDescricaoEspecie->setName    ( 'stDescricaoEspecie');
$obTxtDescricaoEspecie->setValue   ( $_REQUEST["stDescricaoEspecie"] );
$obTxtDescricaoEspecie->setInteiro ( false );
$obTxtDescricaoEspecie->setSize    ( 80 );
$obTxtDescricaoEspecie->setMaxLength ( 80 );
$obTxtDescricaoEspecie->setNull    ( false );

$obLblCodEspecie = new Label;
$obLblCodEspecie->setName   ( 'LabelCodEspecie' );
$obLblCodEspecie->setTitle  ( 'Codigo da Espécie' );
$obLblCodEspecie->setRotulo ( 'Codigo da Espécie' );
$obLblCodEspecie->setValue  ( $_REQUEST["inCodEspecie"] );

$obLblCodNatureza = new Label;
$obLblCodNatureza->setName   ( 'LabelCodNatureza' );
$obLblCodNatureza->setTitle  ( 'Codigo da Natureza' );
$obLblCodNatureza->setRotulo ( 'Codigo da Natureza' );
$obLblCodNatureza->setValue  ( $_REQUEST['inCodNatureza'] . ' - '.  $_REQUEST['stNomNatureza'] );

$obLblCodGenero = new Label;
$obLblCodGenero->setName   ( 'LabelCodGenero' );
$obLblCodGenero->setTitle  ( 'Codigo do Gênero' );
$obLblCodGenero->setRotulo ( 'Codigo do Gênero' );
$obLblCodGenero->setValue  ( $_REQUEST['inCodGenero'] . ' - '.  $_REQUEST['stNomGenero'] );

$obRMONEspecieCredito->ListarNatureza ( $rsNatureza );
$obCmbNatureza = new Select;
$obCmbNatureza->setName          ( "cmbNatureza"             );
$obCmbNatureza->addOption        ( "", "Selecione"          );
$obCmbNatureza->setValue         ( $_REQUEST['inCodNatureza'] );
$obCmbNatureza->setCampoId       ( "cod_natureza"             );
$obCmbNatureza->setCampoDesc     ( "nom_natureza"             );
$obCmbNatureza->preencheCombo    ( $rsNatureza               );
$obCmbNatureza->setNull          ( false                    );
$obCmbNatureza->setStyle         ( "width: 220px"           );
$obCmbNatureza->obEvento->setOnChange ( "preencheGenero('');"  );

$obRMONEspecieCredito->ListarGenero ( $rsGenero );

$obCmbGenero = new Select;
$obCmbGenero->setName          ( "cmbGenero"             );
$obCmbGenero->addOption        ( "", "Selecione"          );
$obCmbGenero->setValue         ( $_REQUEST['inCodGenero'] );
$obCmbGenero->setCampoId       ( "cod_genero"             );
$obCmbGenero->setCampoDesc     ( "nom_genero"             );
$obCmbGenero->preencheCombo    ( $rsGenero               );
$obCmbGenero->setNull          ( false                    );
$obCmbGenero->setStyle         ( "width: 220px"           );

//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->setAjuda ( "UC-05.05.09" );
$obFormulario->addTitulo ('Dados para a Espécie');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

if ($stAcao == "alterar") {
    $obFormulario->addHidden ($obHdnCodEspecie);
    $obFormulario->addHidden ($obHdnCodGenero);
    $obFormulario->addHidden ($obHdnCodNatureza);
}

if ($stAcao =='alterar') {
    $obFormulario->addComponente ( $obLblCodEspecie );
    $obFormulario->addComponente ( $obLblCodNatureza );
    $obFormulario->addComponente ( $obLblCodGenero );
}

if ($stAcao =='incluir') {
    $obFormulario->addComponenteComposto  ( $obTxtCodNatureza, $obCmbNatureza );
    $obFormulario->addComponenteComposto  ( $obTxtCodGenero, $obCmbGenero );
}

$obFormulario->addComponente ( $obTxtDescricaoEspecie );

if ($stAcao == "incluir") {
    $obFormulario->ok       ();
} else {
    $obFormulario->cancelar ();
}

$obFormulario->show();

if ($stAcao == 'incluir') {
    $stJs .= 'f.inCodNatureza.focus();';
} else {
    $stJs .= 'f.stDescricaoEspecie.focus();';
}
sistemaLegado::executaFrameOculto ( $stJs );

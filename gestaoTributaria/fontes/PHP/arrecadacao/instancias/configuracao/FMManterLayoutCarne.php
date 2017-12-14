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
    * Página de Formulario para Configuração de Layout de carne
    * Data de Criação   : 29/09/2008

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Fernando Piccini Cercato

    * @ignore

    * $Id: $

* Casos de uso: uc-05.03.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRModeloCarne.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLayoutCarne";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obTARRModeloCarne = new TARRModeloCarne;
$obTARRModeloCarne->recuperaListaModeloCarneLayout( $rsModelosArquivo );

$obCmbModeloArquivo = new Select;
$obCmbModeloArquivo->setName         ( "cmbModeloArquivo" );
$obCmbModeloArquivo->addOption       ( "", "Selecione" );
$obCmbModeloArquivo->setRotulo       ( "Modelo de Arquivo" );
$obCmbModeloArquivo->setTitle        ( "Modelo de Arquivo" );
$obCmbModeloArquivo->setCampoId      ( "cod_modelo" );
$obCmbModeloArquivo->setCampoDesc    ( "nom_modelo" );
$obCmbModeloArquivo->preencheCombo   ( $rsModelosArquivo );
$obCmbModeloArquivo->setNull         ( false );
$obCmbModeloArquivo->setStyle        ( "width: 220px" );
$stOnChange = "ajaxJavaScript('".$pgOcul."&inCodModelo='+this.value,'carregaModelo');";
$obCmbModeloArquivo->obEvento->setOnChange( $stOnChange );

$obTxtModeloArquivo = new TextBox;
$obTxtModeloArquivo->setRotulo ( "Novo Modelo de Arquivo" );
$obTxtModeloArquivo->setTitle ( "Informe nome do novo modelo de arquivo a ser utilizado." );
$obTxtModeloArquivo->setName ( "stModeloArquivo" );
$obTxtModeloArquivo->setSize ( 20 );
$obTxtModeloArquivo->setMaxLength ( 10 );
$obTxtModeloArquivo->setNull ( true );

$obBtnIncluirModeloArquivo = new Button;
$obBtnIncluirModeloArquivo->setName ( "btnIncluirModeloArquivo" );
$obBtnIncluirModeloArquivo->setValue ( "Incluir" );
$obBtnIncluirModeloArquivo->setTipo ( "button" );
$obBtnIncluirModeloArquivo->obEvento->setOnClick ( "montaParametrosGET('incluirModeloArquivo', 'stModeloArquivo', true);" );

// spans
$obSpnModulo = new Span;
$obSpnModulo->setId ( "spnModulo" );

$obSpnLista = new Span;
$obSpnLista->setId ( "spnListaVariaveis" );

$rsModulos = new RecordSet;

$obCmbModulo = new Select;
$obCmbModulo->setName         ( "cmbModulos" );
$obCmbModulo->addOption       ( "", "Selecione" );
$obCmbModulo->setRotulo       ( "Módulo" );
$obCmbModulo->setTitle        ( "Módulo" );
$obCmbModulo->setCampoId      ( "cod_tipo" );
$obCmbModulo->setCampoDesc    ( "nom_tipo" );
$obCmbModulo->preencheCombo   ( $rsModulos );
$obCmbModulo->setNull         ( false );
$obCmbModulo->setStyle        ( "width: 220px" );
$stOnChange = "ajaxJavaScript('".$pgOcul."&inCodTipo='+this.value,'carregaModulo');";
$obCmbModulo->obEvento->setOnChange( $stOnChange );

$obRdbCapaUnicaSim = new Radio;
$obRdbCapaUnicaSim->setRotulo     ( "Capa" );
$obRdbCapaUnicaSim->setName       ( "stCapaUnica" );
$obRdbCapaUnicaSim->setLabel      ( "Primeira Folha" );
$obRdbCapaUnicaSim->setValue      ( true );
$obRdbCapaUnicaSim->setTitle      ( "Forma de Impressão da capa do carne."   );
$obRdbCapaUnicaSim->setNull       ( false );

$obRdbCapaUnicaNao = new Radio;
$obRdbCapaUnicaNao->setRotulo   ( "Capa" );
$obRdbCapaUnicaNao->setName     ( "stCapaUnica" );
$obRdbCapaUnicaNao->setLabel    ( "Todas Folhas" );
$obRdbCapaUnicaNao->setValue    ( false );
$obRdbCapaUnicaNao->setNull     ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

$obFormulario->addTitulo ( "Modelo de Carne" );
$obFormulario->addComponente ( $obCmbModeloArquivo );
$obFormulario->agrupaComponentes ( array( $obTxtModeloArquivo, $obBtnIncluirModeloArquivo ) );

$obFormulario->addTitulo ( "Dados para Layout do Carne" );
$obFormulario->agrupaComponentes ( array( $obRdbCapaUnicaSim, $obRdbCapaUnicaNao ) );
$obFormulario->addComponente ( $obCmbModulo );
$obFormulario->addSpan ( $obSpnModulo );
$obFormulario->addSpan ( $obSpnLista );

$obFormulario->Ok();
$obFormulario->setFormFocus( $obCmbModulo->getId() );
$obFormulario->show();

Sessao::write( "layoutVariaveis", array() );
?>

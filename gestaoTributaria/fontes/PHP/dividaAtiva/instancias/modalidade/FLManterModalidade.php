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
    * Página de Formulario de Filtro para Alteracao e Exclusao de Modalidade

    * Data de Criação   : 22/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.07

*/

/*
$Log$
Revision 1.3  2007/09/25 19:32:33  cercato
filtro por tipo de modalidade.

Revision 1.2  2007/02/28 17:07:33  cercato
Bug #8534#

Revision 1.1  2006/09/25 14:56:20  cercato
implementacao dos formularios de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATTipoModalidade.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterModalidade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

//Codigo
$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo ( "Código" );
$obTxtCodigo->setTitle ( "Informe o código da modalidade." );
$obTxtCodigo->setName ( "inCodigo" );
$obTxtCodigo->setID ( "inCodigo" );
$obTxtCodigo->setSize ( 20 );
$obTxtCodigo->setMaxLength ( 20 );
$obTxtCodigo->setNull ( true );
$obTxtCodigo->setInteiro ( true );

//Descricao
$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo ( "Descrição" );
$obTxtDescricao->setTitle ( "Informe a descrição da modalidade." );
$obTxtDescricao->setName ( "stDescricao" );
$obTxtDescricao->setSize ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull ( true );
$obTxtDescricao->setInteiro ( false );

$obTDATTipoModalidade = new TDATTipoModalidade;
$obTDATTipoModalidade->recuperaTodos( $rsTipoModalidade );

$obCmbTipo = new Select;
$obCmbTipo->setRotulo       ( "Tipo de Modalidade" );
$obCmbTipo->setTitle        ( "Tipo de Modalidade" );
$obCmbTipo->setName         ( "cmbTipo" );
$obCmbTipo->addOption       ( "", "Selecione" );
$obCmbTipo->setCampoId      ( "cod_tipo_modalidade" );
$obCmbTipo->setCampoDesc    ( "descricao" );
$obCmbTipo->preencheCombo   ( $rsTipoModalidade );
$obCmbTipo->setNull         ( true );
$obCmbTipo->setStyle        ( "width: 220px" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.07" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addComponente ( $obTxtCodigo );
$obFormulario->addComponente ( $obTxtDescricao );
$obFormulario->addComponente ( $obCmbTipo );
$obFormulario->Ok ();
$obFormulario->setFormFocus( $obTxtCodigo->getId() );
$obFormulario->show();

//$stJs .= 'f.inCodigo.focus();';
//sistemaLegado::executaFrameOculto ( $stJs );

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

    * Data de Criacao   : 08/12/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLManterAcrescimo.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.05.11

*/

/*
$Log$
Revision 1.11  2006/09/19 15:51:38  domluc
Correção para o Bug #7009

Revision 1.10  2006/09/15 14:57:21  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpAcrescimo.class.php" );

//Define a fun?o do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterAcrescimo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRMONAcrescimo = new RMONAcrescimo;

Sessao::remove('link');
Sessao::remove('stLink');
//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obLblCodTipo = new Label;
$obLblCodTipo->setName   ( 'LabelCodTipo' );
$obLblCodTipo->setTitle  ( 'Tipo de Acréscimo' );
$obLblCodTipo->setRotulo ( 'Tipo de Acréscimo' );
$obLblCodTipo->setValue  ( $inCodTipo );

$obLblCodFuncao = new Label;
$obLblCodFuncao->setName  ( 'LabelCodFuncao' );
$obLblCodFuncao->setTitle ( 'Código da Função' );
$obLblCodFuncao->setRotulo( 'Código da Função' );
$obLblCodFuncao->setValue ($_REQUEST['inCodFuncao'].' - '.$_REQUEST['stFuncao']);

$obRMONAcrescimo->ListarTipo ( $rsTipo );
/*
$obCmbTipo = new Select;
$obCmbTipo->setRotulo        ( "Tipo de Acréscimo" );
$obCmbTipo->setTitle         ( "Tipo de Acréscimo" );
$obCmbTipo->setName          ( "cmbTipo"           );
$obCmbTipo->addOption        ( "", "Selecione"     );
$obCmbTipo->setValue         ( $_REQUEST['inCodTipo'] );
$obCmbTipo->setCampoId       ( "cod_tipo"             );
$obCmbTipo->setCampoDesc     ( "nom_tipo"             );
$obCmbTipo->preencheCombo    ( $rsTipo               );
$obCmbTipo->setNull          ( true                    );
//$obCmbTipo->setStyle         ( "width: 220px"           );
*/

$obForm = new Form;
$obForm->setAction ( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->setAjuda  ( "UC-05.05.11" );
$obFormulario->addTitulo ('Dados para Filtro');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obPopUpAcrescimo = new IPopUpAcrescimo;
$obPopUpAcrescimo->setNull ( true );
$obPopUpAcrescimo->geraFormulario( $obFormulario );
//$obFormulario->addComponente ( $obCmbTipo );

$obFormulario->ok();
$obFormulario->show ();

$stJs .= 'f.inCodAcrescimo.focus();';
sistemaLegado::executaFrameOculto ( $stJs );

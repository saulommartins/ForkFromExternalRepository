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
    * Pagina de Formulario de Inclusao/Alteracao de FORMULA DE ACRESCIMO

    * Data de Criacao: 08/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterFormulaAcrescimo.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.11

*/

/*
$Log$
Revision 1.9  2006/09/15 14:57:21  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."IPopUpFuncao.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterFormulaAcrescimo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LSManterAcrescimo.php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OCManterAcrescimo.php";
$pgJs          = "JSManterAcrescimo.js";

/***********************************************/

include_once ( $pgJs );

$obRMONAcrescimo =  new RMONAcrescimo;
if ($_REQUEST['stAcao'] == 'alterar'||$_REQUEST['stAcao'] == 'formula') {
//    $stCodFuncao = $_REQUEST['inCodModulo'].'.'.$_REQUEST['inCodBiblioteca'].'.'.$_REQUEST['inCodFuncao'];

    $stCodFuncao = sprintf( "%02d.%d.%03d", $_REQUEST['inCodModulo'], $_REQUEST['inCodBiblioteca'], $_REQUEST['inCodFuncao'] );
    $stNomFuncao = $_REQUEST['stNomFuncao'];
    $dtInicioVigencia = $_REQUEST['dtInicioVigencia'];
}
//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

//---
$obHdnCodAcrescimo = new Hidden;
$obHdnCodAcrescimo->setName  ('inCodAcrescimo');
$obHdnCodAcrescimo->setValue ( $_REQUEST['inCodAcrescimo'] );

$obHdnCodTipo = new Hidden;
$obHdnCodTipo->setName  ('inCodTipo');
$obHdnCodTipo->setValue ( $_REQUEST['inCodTipo'] );

$obHdnDtVigenciaAntes = new Hidden;
$obHdnDtVigenciaAntes->setName  ('dtVigenciaAntes');
$obHdnDtVigenciaAntes->setValue ( $_REQUEST['dtVigencia'] );

$obHdnDtVigencia = new Hidden;
$obHdnDtVigencia->setName  ('dtVigencia');
$obHdnDtVigencia->setValue ( $dia );

//----- LABELS
$obLblCodAcrescimo = new Label;
$obLblCodAcrescimo->setName   ( 'LabelCodAcrescimo' );
$obLblCodAcrescimo->setTitle  ( 'Código do Acréscimo' );
$obLblCodAcrescimo->setRotulo ( 'Código' );
$obLblCodAcrescimo->setValue  ( $_REQUEST['inCodAcrescimo'] );

$obLblDescAcrescimo = new Label;
$obLblDescAcrescimo->setName   ( 'LabelDescAcrescimo' );
$obLblDescAcrescimo->setRotulo  ( 'Descrição' );
$obLblDescAcrescimo->setTitle ( 'Descrição Legal do Acréscimo' );
$obLblDescAcrescimo->setValue  ( $_REQUEST['stDescAcrescimo'] );

$obLblCodTipo = new Label;
$obLblCodTipo->setName   ( 'LabelCodTipo' );
$obLblCodTipo->setTitle  ( 'Tipo do Acréscimo' );
$obLblCodTipo->setRotulo ( 'Tipo do Acréscimo' );
$obLblCodTipo->setValue  ( $_REQUEST['inCodTipo'].' - '.$_REQUEST['stNomTipo'] );
//----- fim labels

$obDtVigencia  = new Data;
$obDtVigencia->setName               ( "dtVigencia"                      );
$obDtVigencia->setValue              ( $dtInicioVigencia                                    );
$obDtVigencia->setRotulo             ( "Data de Vigência"              );
$obDtVigencia->setTitle              ( "Data de Vigência da fórmula de cálculo" );
$obDtVigencia->setMaxLength          ( 20                                );
$obDtVigencia->setSize               ( 10                                );
$obDtVigencia->setNull               ( false                             );
$obDtVigencia->obEvento->setOnChange ( "validaData1500( this );"         );

//string q agrupa os cod_modulo, cod_biblioteca e cod_funcao para montar a formula
$stFormulaCompleta = $obRMONAcrescimo->obRMONFormulaAcrescimo->getCodModulo().'.'. $obRMONAcrescimo->obRMONFormulaAcrescimo->getCodBiblioteca() .'.'. $obRMONAcrescimo->obRMONFormulaAcrescimo->getCodFuncao();

$obIpopUpFuncao = new IPopUpFuncao;

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

$obFormulario->addHidden     ( $obHdnCodAcrescimo );
$obFormulario->addHidden     ( $obHdnCodTipo );
$obFormulario->addHidden     ( $obHdnDtVigenciaAntes );

//$obFormulario->addHidden ($obHdnDtVigencia);
$obFormulario->addComponente ( $obLblCodAcrescimo );
$obFormulario->addComponente ( $obLblDescAcrescimo );
$obFormulario->addComponente ( $obLblCodTipo );

$obIpopUpFuncao->setCodFuncao ($stCodFuncao);
$obIpopUpFuncao->setCodModulo( 28 );
$obIpopUpFuncao->setCodBiblioteca( 2 );
$obIpopUpFuncao->geraFormulario( $obFormulario);

$obFormulario->addComponente ( $obDtVigencia );

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->ok ();
} else {
    $obFormulario->cancelar ();
}

$obFormulario->show();

//$stJs .= 'f.inCodFuncao.focus();'
//sistemaLegado::executaFrameOculto ( $stJs );

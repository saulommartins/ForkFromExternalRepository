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
    * Pagina de Formulario Definir Valor ACRESCIMO

    * Data de Criacao: 04/08/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMManterValorAcrescimo.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.11

*/

/*
$Log$
Revision 1.4  2007/08/20 14:15:08  cercato
Bug#9958#

Revision 1.3  2006/09/15 14:57:21  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

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
//$stCaminho   = CAM_GT_MON_INSTANCIAS."especie/";

/***********************************************/

include_once ( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnCodAcrescimo = new Hidden;
$obHdnCodAcrescimo->setName  ('inCodAcrescimo');
$obHdnCodAcrescimo->setValue ( $_REQUEST['inCodAcrescimo'] );

$obHdnCodTipo = new Hidden;
$obHdnCodTipo->setName  ('inCodTipo');
$obHdnCodTipo->setValue ( $_REQUEST['inCodTipo'] );

$obHdnDtVigencia = new Hidden;
$obHdnDtVigencia->setName  ('dtVigencia');
$obHdnDtVigencia->setValue ( $_REQUEST["dia"] );

$obLblCodAcrescimo = new Label;
$obLblCodAcrescimo->setName   ( 'LabelCodAcrescimo' );
$obLblCodAcrescimo->setTitle  ( 'Código do acréscimo.' );
$obLblCodAcrescimo->setRotulo ( 'Código' );
$obLblCodAcrescimo->setValue  ( $_REQUEST['inCodAcrescimo'] );

$obLblDescAcrescimo = new Label;
$obLblDescAcrescimo->setName    ( "LabelDescrição" );
$obLblDescAcrescimo->setTitle   ( "Descrição do acréscimo." );
$obLblDescAcrescimo->setRotulo  ( "Descrição" );
$obLblDescAcrescimo->setValue   ( $_REQUEST['stDescAcrescimo'] );

$obTxtValor = new Moeda;
$obTxtValor->setRotulo  ( '*Valor');
$obTxtValor->setTitle   ( 'Valor do acréscimo.');
$obTxtValor->setName    ( 'flValorAcrescimo');
$obTxtValor->setDecimais( 6 );
$obTxtValor->setNegativo ( true );
$obTxtValor->setNull    ( true );

$obDatVigencia = new Data;
$obDatVigencia->setName     ( "dtVigenciaValor" );
$obDatVigencia->setRotulo   ( "*Data de Vigência" );
$obDatVigencia->setTitle    ( "Data de início da vigência do valor." );
$obDatVigencia->setNull    ( true );

$obBtnDefinirValor = new Button;
$obBtnDefinirValor->setName              ( "btnDefinirValor" );
$obBtnDefinirValor->setValue             ( "Definir" );
$obBtnDefinirValor->setTipo              ( "button" );
$obBtnDefinirValor->obEvento->setOnClick ( "definirValor();" );
$obBtnDefinirValor->setDisabled          ( false );

$obBtnLimparValor = new Button;
$obBtnLimparValor->setName               ( "btnLimparValor" );
$obBtnLimparValor->setValue              ( "Limpar" );
$obBtnLimparValor->setTipo               ( "button" );
$obBtnLimparValor->obEvento->setOnClick  ( "buscaValor('limparValor');" );
$obBtnLimparValor->setDisabled           ( false );

$botoesValor = array ( $obBtnDefinirValor, $obBtnLimparValor );

$obSpnValor = new Span;
$obSpnValor->setID("spnListaValor");

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
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnCodAcrescimo );
$obFormulario->addHidden ( $obHdnCodTipo );
$obFormulario->addHidden ( $obHdnDtVigencia );

$obFormulario->addTitulo ('Dados de Acréscimo');
$obFormulario->addComponente ( $obLblCodAcrescimo );
$obFormulario->addComponente ( $obLblDescAcrescimo );

$obFormulario->addTitulo ('Dados para Valor');
$obFormulario->addComponente ( $obTxtValor );
$obFormulario->addComponente ( $obDatVigencia );
$obFormulario->defineBarra ( $botoesValor, 'left', '' );
$obFormulario->addSpan ( $obSpnValor );

$obFormulario->cancelar ();
$obFormulario->show();

sistemaLegado::executaFrameOculto ( "f.flValorAcrescimo.focus();buscaValor('carregaValores');" );

Sessao::write( 'valores', array() );
Sessao::write( 'alterar', -1 );

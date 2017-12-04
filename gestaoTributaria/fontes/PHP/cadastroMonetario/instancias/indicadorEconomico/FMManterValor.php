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
    * Pagina de Formulario de Inclusao/Alteracao de VALOR DO INDICADOR ECONOMICO

    * Data de Criacao: 20/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterValor.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.08

*/

/*
$Log$
Revision 1.4  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterValor";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once ( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnCodIndicador = new Hidden;
$obHdnCodIndicador->setName  ('inCodIndicador');
$obHdnCodIndicador->setValue ( $_REQUEST['inCodIndicador'] );

$obHdnDescricao = new Hidden;
$obHdnDescricao->setName  ('stDescricao');
$obHdnDescricao->setValue ( $_REQUEST['stDescricao'] );

$obHdnDtVigenciaAntes = new Hidden;
$obHdnDtVigenciaAntes->setName  ('dtVigenciaAntes');
$obHdnDtVigenciaAntes->setValue ( $dia );

//------------------------
$obDtVigencia  = new Data;
$obDtVigencia->setName               ( "dtVigencia"                      );
$obDtVigencia->setValue              ( $dia                              );
$obDtVigencia->setRotulo             ( "*Data de Vigência"               );
$obDtVigencia->setTitle              ( "Data de inicio da vigência do valor." );
$obDtVigencia->setMaxLength          ( 20                                );
$obDtVigencia->setSize               ( 10                                );
$obDtVigencia->setNull               ( true                              );
$obDtVigencia->obEvento->setOnChange ( "validaData1500( this );"         );

$obLblCodIndicador = new Label;
$obLblCodIndicador->setName   ( 'LabelCodIndicador' );
$obLblCodIndicador->setTitle  ( 'Código do Indicador' );
$obLblCodIndicador->setRotulo ( 'Código' );
$obLblCodIndicador->setValue  ( $_REQUEST['inCodIndicador'] );

$obLblDescricao = new Label;
$obLblDescricao->setName   ( 'LabelCodIndicador' );
$obLblDescricao->setTitle  ( 'Descrição do Indicador' );
$obLblDescricao->setRotulo ( 'Descrição' );
$obLblDescricao->setValue  ( $_REQUEST['stDescricao'] );

$obTxtValor = new Numerico;
$obTxtValor->setRotulo  ( '*Valor');
$obTxtValor->setTitle   ( 'Valor do indicador econômico.');
$obTxtValor->setName    ( 'inValor');
$obTxtValor->setDecimais ( $_REQUEST['inPrecisao'] );
$obTxtValor->setMaxValue  ( 99999.9999 );
$obTxtValor->setNull      ( true );
$obTxtValor->setNegativo  ( false );
$obTxtValor->setSize    ( 10 );
$obTxtValor->setMaxLength ( 10 );

$obBtnDefinir = new Button;
$obBtnDefinir->setName              ( "btnDefinirValores" );
$obBtnDefinir->setValue             ( "Definir" );
$obBtnDefinir->setTipo              ( "button" );
$obBtnDefinir->obEvento->setOnClick ( "DefinirValores();" );
$obBtnDefinir->setDisabled          ( false );

$obBtnLimpar = new Button;
$obBtnLimpar->setName               ( "btnLimparValores" );
$obBtnLimpar->setValue              ( "Limpar" );
$obBtnLimpar->setTipo               ( "button" );
$obBtnLimpar->obEvento->setOnClick  ( "buscaValor('limparValores');" );
$obBtnLimpar->setDisabled           ( false );

$botoesSpanValores = array ( $obBtnDefinir, $obBtnLimpar );

$obSpnListaValores = new Span;
$obSpnListaValores->setID("spnListaValores");

//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ("oculto" );
//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( "UC-05.05.08" );

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnCodIndicador );
$obFormulario->addHidden ( $obHdnDtVigenciaAntes );

$obFormulario->addTitulo ('Dados de Indicador');
$obFormulario->addComponente ( $obLblCodIndicador );
$obFormulario->addComponente ( $obLblDescricao );

$obFormulario->addTitulo ('Dados para Valor');
$obFormulario->addComponente ( $obTxtValor );
$obFormulario->addComponente ( $obDtVigencia );

$obFormulario->defineBarra ( $botoesSpanValores, 'left', '' );
$obFormulario->addSpan ( $obSpnListaValores );

$obFormulario->cancelar();
$obFormulario->show();

$stJs .= "f.inCodIndicador.focus();buscaValor('carregaValores');";
sistemaLegado::executaFrameOculto ( $stJs );
Sessao::write( "valores", array() );
Sessao::write( "editar", -1 );

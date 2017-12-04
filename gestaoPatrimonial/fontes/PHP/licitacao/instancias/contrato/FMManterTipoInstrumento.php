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
 * Arquivo de instância para manutenção de tipo instrumento
 * Data de Criação: 26042016
 * @author Analista: Gelson Wolowski Gonçalves 
 * @author Desenvolvedor: Lisiane da Rosa Morais
 *
 * $Id:$
 * 
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoTipoInstrumento.class.php";

$stPrograma = "ManterTipoInstrumento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction  ( $pgProc );
$obForm->setTarget  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$rsTipoInstrumento = new RecordSet();

if ($stAcao == "alterar") {
    $inCodigo = $request->get("inCodigo");
    $obTLicitacaoTipoInstrumento= new TLicitacaoTipoInstrumento();
    $obTLicitacaoTipoInstrumento->recuperaTodos( $rsTipoInstrumento, " WHERE cod_tipo = ".$inCodigo );
}

$obTxtCodigo = new TextBox;
$obTxtCodigo->setName      ( "inCodigo" );
$obTxtCodigo->setId        ( "inCodigo" );
$obTxtCodigo->setRotulo    ( "Código" );
$obTxtCodigo->setTitle     ( "Informe o Código do Tipo de Instrumento." );
$obTxtCodigo->setSize      ( 3 );
$obTxtCodigo->setInteiro   ( true );
$obTxtCodigo->setMaxLength ( 3 );
$obTxtCodigo->setNull      ( false );
if ($stAcao == "alterar") {
    $obTxtCodigo->setLabel ( true );
    $obTxtCodigo->setValue ( $rsTipoInstrumento->getCampo('cod_tipo') );
}

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setId        ( "stDescricao" );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Informe a Descrição do Tipo de Instrumento." );
$obTxtDescricao->setSize      ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull      ( false );
if ($stAcao == "alterar") {
    $obTxtDescricao->setValue ( $rsTipoInstrumento->getCampo('descricao') );
}
$obTxtCodigoTribunal = new TextBox;
$obTxtCodigoTribunal->setName      ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setId        ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setRotulo    ( "Código Tribunal" );
$obTxtCodigoTribunal->setTitle     ( "Informe o Código do Tipo de Instrumento Conforme Orientação do Tribunal de Contas." );
$obTxtCodigoTribunal->setSize      ( 3 );
$obTxtCodigoTribunal->setInteiro   ( true );
$obTxtCodigoTribunal->setMaxLength ( 3 );
$obTxtCodigoTribunal->setNull      ( true );
if ($stAcao == "alterar") {
    $obTxtCodigoTribunal->setValue ( $rsTipoInstrumento->getCampo('codigo_tc') );
}

//Radios de Quebrar Página por Conta
$obRdAtivoS = new Radio;
$obRdAtivoS->setRotulo ( "Ativo" );
$obRdAtivoS->setName   ( "boAtivo" );
$obRdAtivoS->setValue  ( "true" );
$obRdAtivoS->setLabel  ( "Sim" );
$obRdAtivoS->setNull   ( false );

if (($stAcao == "alterar" && $rsTipoInstrumento->getCampo('ativo') == 't') || ($rsTipoInstrumento->getCampo('ativo') == NULL) || ($stAcao == "incluir")) {
    $obRdAtivoS->setChecked ( true );
}

$obRdAtivoN = new Radio;
$obRdAtivoN->setName   ( "boAtivo" );
$obRdAtivoN->setValue  ( "false" );
$obRdAtivoN->setLabel  ( "Não" );
$obRdAtivoN->setNull   ( false );
if ($stAcao == "alterar" && $rsTipoInstrumento->getCampo('ativo') == 'f') {
    $obRdAtivoN->setChecked ( true );
}

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm );
$obFormulario->setAjuda          ( "UC-03.05.22" );
$obFormulario->addHidden         ( $obHdnCtrl );
$obFormulario->addHidden         ( $obHdnAcao );
$obFormulario->addTitulo         ( "Dados para Inclusão de Instrumentos" );
$obFormulario->addComponente     ( $obTxtCodigo );
$obFormulario->addComponente     ( $obTxtDescricao );
$obFormulario->addComponente     ( $obTxtCodigoTribunal );
$obFormulario->agrupaComponentes ( array($obRdAtivoS, $obRdAtivoN) );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "Reset" );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTela')" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );

$obFormulario->show();

?>

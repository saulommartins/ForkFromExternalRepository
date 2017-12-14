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
 * Arquivo de instância para manutenção de tipo de contrato
 * Data de Criação: 13/11/2015
 * @author Analista: Gelson Wolowski Gonçalves 
 * @author Desenvolvedor: Jean da Silva
 * 
 * $Id: FMManterTipoContrato.php 64110 2015-12-03 16:11:23Z michel $
 * 
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoTipoContrato.class.php";

$stPrograma = "ManterTipoContrato";
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

$rsTipoContrato = new RecordSet();
if ($stAcao == "alterar") {
    $inCodigo = $request->get("inCodigo");
    $obTLicitacaoTipoContrato = new TLicitacaoTipoContrato();
    $obTLicitacaoTipoContrato->recuperaTodos( $rsTipoContrato, " WHERE cod_tipo = ".$inCodigo );
}

$obTxtCodigo = new TextBox;
$obTxtCodigo->setName      ( "inCodigo" );
$obTxtCodigo->setId        ( "inCodigo" );
$obTxtCodigo->setRotulo    ( "Código" );
$obTxtCodigo->setTitle     ( "Informe o Código do Tipo de Contrato." );
$obTxtCodigo->setSize      ( 3 );
$obTxtCodigo->setInteiro   ( true );
$obTxtCodigo->setMaxLength ( 3 );
$obTxtCodigo->setNull      ( false );
if ($stAcao == "alterar") {
    $obTxtCodigo->setLabel ( true );
    $obTxtCodigo->setValue ( $rsTipoContrato->getCampo('cod_tipo') );
}

$obTxtSigla = new TextBox;
$obTxtSigla->setName      ( "stSigla" );
$obTxtSigla->setId        ( "stSigla" );
$obTxtSigla->setRotulo    ( "Sigla" );
$obTxtSigla->setTitle     ( "Informe a Sigla do tipo de Contrato." );
$obTxtSigla->setSize      ( 8 );
$obTxtSigla->setMaxLength ( 8 );
$obTxtSigla->setNull      ( false );
if ($stAcao == "alterar") {
    $obTxtSigla->setValue ( $rsTipoContrato->getCampo('sigla') );
}

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setId        ( "stDescricao" );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Informe a Descrição do Tipo de Contrato." );
$obTxtDescricao->setSize      ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull      ( false );
if ($stAcao == "alterar") {
    $obTxtDescricao->setValue ( $rsTipoContrato->getCampo('descricao') );
}
$obTxtCodigoTribunal = new TextBox;
$obTxtCodigoTribunal->setName      ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setId        ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setRotulo    ( "Código Tribunal" );
$obTxtCodigoTribunal->setTitle     ( "Informe o Código do Tipo de Contrato Conforme Orientação do Tribunal de Contas." );
$obTxtCodigoTribunal->setSize      ( 3 );
$obTxtCodigoTribunal->setInteiro   ( true );
$obTxtCodigoTribunal->setMaxLength ( 3 );
$obTxtCodigoTribunal->setNull      ( true );
if ($stAcao == "alterar") {
    $obTxtCodigoTribunal->setValue ( $rsTipoContrato->getCampo('tipo_tc') );
}

//Radios de Quebrar Página por Conta
$obRdAtivoS = new Radio;
$obRdAtivoS->setRotulo ( "Ativo" );
$obRdAtivoS->setName   ( "boAtivo" );
$obRdAtivoS->setValue  ( "true" );
$obRdAtivoS->setLabel  ( "Sim" );
$obRdAtivoS->setNull   ( false );

if (($stAcao == "alterar" && $rsTipoContrato->getCampo('ativo') == 't') || ($rsTipoContrato->getCampo('ativo') == NULL) || ($stAcao == "incluir")) {
    $obRdAtivoS->setChecked ( true );
}

$obRdAtivoN = new Radio;
$obRdAtivoN->setName   ( "boAtivo" );
$obRdAtivoN->setValue  ( "false" );
$obRdAtivoN->setLabel  ( "Não" );
$obRdAtivoN->setNull   ( false );
if ($stAcao == "alterar" && $rsTipoContrato->getCampo('ativo') == 'f') {
    $obRdAtivoN->setChecked ( true );
}

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm );
$obFormulario->setAjuda          ( "UC-03.05.22" );
$obFormulario->addHidden         ( $obHdnCtrl );
$obFormulario->addHidden         ( $obHdnAcao );
$obFormulario->addTitulo         ( "Dados para Configuração" );
$obFormulario->addComponente     ( $obTxtCodigo );
$obFormulario->addComponente     ( $obTxtSigla );
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

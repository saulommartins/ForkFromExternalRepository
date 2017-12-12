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
  * Página de Formulário de Permissao para Avaliar Imóvel
  * Data de criação : 20/04/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @ignore

    * $Id: FMPermissaoAvaliarImovel.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.2  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php"                                  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php"                                          );
//Define o nome dos arquivos PHP
$stPrograma = "PermissaoAvaliarImovel";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$stAcao = $request->get('stAcao');
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obBscUsuario = new BuscaInner;
$obBscUsuario->setRotulo( "*Usuário" );
$obBscUsuario->setTitle( "Usuário que terá permissão para efetivar cálculo venal" );
$obBscUsuario->setId( "stNomCGM" );
$obBscUsuario->obCampoCod->setName("inNumCGM");
$obBscUsuario->obCampoCod->setValue( $_REQUEST["inNumCGM"] );
$obBscUsuario->obCampoCod->obEvento->setOnChange("buscaValor('buscaUsuario');");
$obBscUsuario->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."usuario/FLProcurarUsuario.php','frm','inNumCGM','stNomCGM','todos','".Sessao::getId()."','800','550');" );
$obBscUsuario->setNull(true);

$obBtnIncluirUsuario = new Button;
$obBtnIncluirUsuario->setName              ( "btnIncluirAcrescimo" );
$obBtnIncluirUsuario->setValue             ( "Incluir"             );
$obBtnIncluirUsuario->setTipo              ( "button"              );
$obBtnIncluirUsuario->obEvento->setOnClick ( "buscaValor('incluirUsuario');" );
$obBtnIncluirUsuario->setDisabled          ( false                 );

$obBtnLimparUsuario = new Button;
$obBtnLimparUsuario->setName               ( "btnLimparAcrescimo"  );
$obBtnLimparUsuario->setValue              ( "Limpar"              );
$obBtnLimparUsuario->setTipo               ( "button"              );
$obBtnLimparUsuario->obEvento->setOnClick  ( "buscaValor('limparUsuario');"  );
$obBtnLimparUsuario->setDisabled           ( false                 );

$botoesUsuario = array ( $obBtnIncluirUsuario , $obBtnLimparUsuario );

$obSpnListaUsuarios = new Span;
$obSpnListaUsuarios->setID("spnListaUsuarios");

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo ( "Dados para Permissão" );
$obFormulario->addComponente ( $obBscUsuario );
$obFormulario->defineBarra   ( $botoesUsuario, 'left', '' );
$obFormulario->addSpan ( $obSpnListaUsuarios );
$obFormulario->Ok();
$obFormulario->Show();

Sessao::write( 'usuarios', array() );

sistemaLegado::executaFrameOculto("document.frm.inNumCGM.focus();");

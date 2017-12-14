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
* Página de Formulario de filtro do objeto
* Data de Criação   : 04/07/2006

* @author Analista: Diego Victoria
* @author Desenvolvedor: Leandro André Zis

$Id: FLManterObjeto.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso :uc-03.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterObjeto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

#sessao->link= "";
Sessao::write('link' , '');

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obTxtCodigoObjeto = new TextBox;
$obTxtCodigoObjeto->setName      ( "inCodObjeto" );
$obTxtCodigoObjeto->setTitle     ( "Informe o código do objeto." );
$obTxtCodigoObjeto->setRotulo    ( "Código" );
$obTxtCodigoObjeto->setMaxLength ( 15 );
$obTxtCodigoObjeto->setSize      ( 10 );
$obTxtCodigoObjeto->setInteiro   (true);

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setTitle     ( "Informe a descrição" );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setSize      ( 50 );

$obCmpTipoBusca = new TipoBusca( $obTxtDescricao );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.04.07');
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obTxtCodigoObjeto );
$obFormulario->addComponente ( $obCmpTipoBusca );

$obBtnOk = new Ok();
$obBtnOk->setId( 'Ok' );

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick( "limpaFormulario(); document.frm.inCodObjeto.focus();" );

$obFormulario->defineBarra( array($obBtnOk, $obBtnLimpar) );

$obFormulario->show();

?>

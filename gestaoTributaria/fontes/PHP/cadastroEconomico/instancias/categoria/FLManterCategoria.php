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
   * Página de Formulario de Inclusao/Alteracao de Serviços

   * Data de Criação   : 11/04/2005

   * @author Fernando Zank Correa Evangelista
   * @author Desenvolvedor: Lizandro Kirst da Silva

   * @ignore

    * $Id: FLManterCategoria.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.09

*/

/*
$Log$
Revision 1.7  2006/09/15 14:32:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCategoria.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCategoria";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//include_once( $pgJs );
$obRCEMCategoria = new RCEMCategoria;

Sessao::write( "link", "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );
$obTxtCodigoCategoria = new TextBox ;
$obTxtCodigoCategoria->setRotulo    ( "Código" );
$obTxtCodigoCategoria->setName      ( "inCodigoCategoria");
$obTxtCodigoCategoria->setValue     ( $inCodigoCategoria );
$obTxtCodigoCategoria->setId        ( "codigoCategoria" );
$obTxtCodigoCategoria->setTitle     ( "Código da Categoria" );
$obTxtCodigoCategoria->setSize      ( 5 );
$obTxtCodigoCategoria->setMaxLength ( 5 );
$obTxtCodigoCategoria->setInteiro   ( true );

$obTxtNomeCategoria = new TextBox ;
$obTxtNomeCategoria->setRotulo    ( "Nome" );
$obTxtNomeCategoria->setName      ( "stNomeCategoria");
$obTxtNomeCategoria->setValue     ( $stNomeCategoria );
$obTxtNomeCategoria->setTitle     ( "Nome da Categoria" );
$obTxtNomeCategoria->setSize      ( 80 );
$obTxtNomeCategoria->setMaxLength ( 80 );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.09");
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obTxtCodigoCategoria );
$obFormulario->addComponente ( $obTxtNomeCategoria );

$obFormulario->OK();

$obFormulario->setFormFocus( $obTxtCodigoCategoria->getid() );

$obFormulario->show();

sistemaLegado::executaFrameOculto( $stJs );

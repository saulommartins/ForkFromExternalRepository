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
    * Página de Formulario de Inclusao/Alteracao de Bancos

    * Data de Criação   : 10/10/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: FLManterBanco.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.01

*/

/*
$Log$
Revision 1.8  2006/09/15 14:57:32  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterBanco";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//include_once( $pgJs );
$obRMONBanco = new RMONBanco;

Sessao::write( "link", "" );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

$obTxtNumBanco = new TextBox ;
$obTxtNumBanco->setRotulo    ( "Número do Banco" );
$obTxtNumBanco->setName      ( "stNumBanco");
$obTxtNumBanco->setValue     ( $_REQUEST["stNumBanco"] );
$obTxtNumBanco->setTitle     ( "Número do banco." );
$obTxtNumBanco->setSize      ( 3 );
$obTxtNumBanco->setMaxLength ( 3 );
$obTxtNumBanco->setInteiro   ( false );

$obTxtNomBanco = new TextBox ;
$obTxtNomBanco->setRotulo    ( "Nome do Banco" );
$obTxtNomBanco->setName      ( "stNomBanco");
$obTxtNomBanco->setValue     ( $_REQUEST["stNomBanco"] );
$obTxtNomBanco->setTitle     ( "Nome do banco." );
$obTxtNomBanco->setSize      ( 80 );
$obTxtNomBanco->setMaxLength ( 80 );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.01" );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addComponente ( $obTxtNumBanco );
$obFormulario->addComponente ( $obTxtNomBanco );

$obFormulario->OK();
$obFormulario->show();

$stJs .= 'f.stNumBanco.focus();';
sistemaLegado::executaFrameOculto ( $stJs );

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
    * Página de Formulario de Inclusao/Alteracao de Conta Corrente

    * Data de Criação   : 07/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: FLManterConta.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.03

*/

/*
$Log$
Revision 1.11  2006/09/15 14:57:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
    }

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConta";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$obIMontaAgencia = new IMontaAgencia;
$obIMontaAgencia->obITextBoxSelectBanco->setNull( true );
$obIMontaAgencia->obTextBoxSelectAgencia->setNull( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

$obTxtConta = new TextBox ;
$obTxtConta->setRotulo    ( "Conta-Corrente"                         );
$obTxtConta->setName      ( "stNumeroConta"                          );
$obTxtConta->setValue     ( $stNumeroConta                           );
$obTxtConta->setTitle     ( "Número da conta-corrente            "   );
$obTxtConta->setSize      ( 20                                       );
$obTxtConta->setMaxLength ( 20                                       );
$obTxtConta->obEvento->setOnKeyPress( "return validar(event)" );
$obTxtConta->setNull      ( true                                     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.03" );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );

$obIMontaAgencia->geraFormulario( $obFormulario );
$obFormulario->addComponente            ( $obTxtConta                 );

$obFormulario->OK();
$obFormulario->show();

$stJs .= 'f.inNumbanco.focus();';
sistemaLegado::executaFrameOculto ( $stJs );

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
    * Página de filtro para o cadastro de loteamento
    * Data de Criação   : 01/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: FLManterLoteamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterLoteamento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::remove('link');
Sessao::remove('stLink');

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obTxtCodLoteamento = new TextBox;
$obTxtCodLoteamento->setRotulo    ( "Código"                     );
$obTxtCodLoteamento->setTitle     ( "Código do loteamento"       );
$obTxtCodLoteamento->setName      ( "inCodigoLoteamento"         );
$obTxtCodLoteamento->setId        ( "inCodigoLoteamento"         );
$obTxtCodLoteamento->setSize      ( 10                           );
$obTxtCodLoteamento->setMaxLength ( 10                           );
$obTxtCodLoteamento->setInteiro   ( true                         );

$obTxtNomLoteamento = new TextBox;
$obTxtNomLoteamento->setRotulo    ( "Nome"                       );
$obTxtNomLoteamento->setTitle     ( "Nome do loteamento"         );
$obTxtNomLoteamento->setName      ( "stNomeLoteamento"           );
$obTxtNomLoteamento->setSize      ( 80                           );
$obTxtNomLoteamento->setMaxLength ( 80                           );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                ( $pgList                      );
$obForm->setTarget                ( "telaPrincipal"              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm                      );
$obFormulario->setAjuda ( "UC-05.01.15" );
$obFormulario->addHidden          ( $obHdnAcao                   );

$obFormulario->addTitulo          ( "Dados para Filtro"          );
$obFormulario->addComponente      ( $obTxtCodLoteamento          );
$obFormulario->addComponente      ( $obTxtNomLoteamento          );

$obFormulario->OK                 (                              );
$obFormulario->setFormFocus       ( $obTxtCodLoteamento->getId() );
$obFormulario->show               (                              );
?>

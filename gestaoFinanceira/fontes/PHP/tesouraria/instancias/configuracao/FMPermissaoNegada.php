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
    * Página de erro do terminal
    * Data de Criação   : 11/11/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.18

*/

/*
$Log$
Revision 1.4  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterTerminal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

// Define Objeto Label para número do boletim
$obLblCodigoAcesso = new Label;
$obLblCodigoAcesso->setRotulo  ( "Código do Terminal" );
$obLblCodigoAcesso->setId      ( "inCodTerminal"      );
$obLblCodigoAcesso->setValue   ( $_GET['stHashMac']   );

// Define Objeto Label para data do boletim
$obLblUsuario = new Label;
$obLblUsuario->setRotulo  ( "Usuário" );
$obLblUsuario->setId      ( "stDtBoletim"     );
$obLblUsuario->setValue   ( Sessao::read('numCgm')." - ".Sessao::read('nomCgm') );

// Define Objeto Label para entidade
$obLblEntidade = new Label;
$obLblEntidade->setId      ( "stEntidade"      );
$obLblEntidade->setValue   ( "Algoooooooooooooooo"    );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo     ( "Dados do Terminal" );
//$obFormulario->addForm       ( $obForm             );
$obFormulario->addComponente ( $obLblUsuario       );
$obFormulario->addComponente ( $obLblCodigoAcesso  );

SistemaLegado::exibeAviso("<b>ACESSO NEGADO!</b> Informe o Nome de Usuário e o Código do Terminal ao Administrador do sistema.","acesso_negado","erro");

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

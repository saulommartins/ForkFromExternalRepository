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
    * Página de Filtro de Tablela de Conversão
    * Data de Criacao: 11/09/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Vitor Hugo
    * @ignore

    * $Id: FLManterTabela.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.1  2007/09/13 13:37:27  vitor
uc-05.03.23

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma      = "ManterTabela";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write( "link", "" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo    ( "Código"                           );
$obTxtCodigo->setName      ( "inCodTabela"                       );
$obTxtCodigo->setTitle     ( "Código da Tabela de Conversão."   );
$obTxtCodigo->setValue     ( $_REQUEST["inCodTabela"]            );
$obTxtCodigo->setNull      ( true                               );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo ( "Descrição"                    );
$obTxtDescricao->setName   ( "stDescricao"                  );
$obTxtDescricao->setValue  ( $_REQUEST["stDescricao"]       );
$obTxtDescricao->setSize   ( 80                             );
$obTxtDescricao->setMaxLength( 80                             );
$obTxtDescricao->setNull   ( true                           );
$obTxtDescricao->setTitle     ( "Descrição da Tabela de conversão." );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setInteiro       ( true          );
$obTxtExercicio->setMaxLength ( 4             );
$obTxtExercicio->setSize           ( 4             );;
$obTxtExercicio->setRotulo ( "Exercício"                    );
$obTxtExercicio->setName   ( "stExercicio"                  );
$obTxtExercicio->setValue  ( $_REQUEST["stExercicio"]       );
$obTxtExercicio->setNull   ( true                           );
$obTxtExercicio->setTitle     ( "Exercício da Tabela de Conversão." );

$obForm = new Form;
$obForm->setAction           ( $pgList                  );
$obForm->setTarget           ( "telaPrincipal"          );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                  );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obHdnAcao               );

$obFormulario->addTitulo     ( "Dados para Filtro"      );

$obFormulario->addComponente ( $obTxtCodigo     );
$obFormulario->addComponente ( $obTxtDescricao  );
$obFormulario->addComponente ( $obTxtExercicio  );
$obFormulario->Ok();
$obFormulario->show();
?>

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
    * Pagina de filtragem para Grupos de Credito
    * Data de Criação   : 25/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * $Id: FLManterGrupo.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.7  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterGrupo";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write( "link", "" );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

// DEFINE OBJETOS DO FORMULARIO
$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo    ( "Código"                           );
$obTxtCodigo->setName      ( "inCodGrupo"                       );
$obTxtCodigo->setTitle     ( "Código do grupo para filtragem."   );
$obTxtCodigo->setValue     ( $_REQUEST["inCodGrupo"]            );
$obTxtCodigo->setNull      ( true                               );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo ( "Descrição"                    );
$obTxtDescricao->setName   ( "stDescricao"                  );
$obTxtDescricao->setValue  ( $_REQUEST["stDescricao"]       );
$obTxtDescricao->setSize   ( 80                             );
$obTxtDescricao->setMaxLength( 80                             );
$obTxtDescricao->setNull   ( true                           );
$obTxtDescricao->setTitle     ( "Descrição do grupo de crédito." );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setInteiro       ( true          );
$obTxtExercicio->setMaxLength ( 4             );
$obTxtExercicio->setSize           ( 4             );;
$obTxtExercicio->setRotulo ( "Exercício"                    );
$obTxtExercicio->setName   ( "stExercicio"                  );
$obTxtExercicio->setValue  ( $_REQUEST["stExercicio"]       );
$obTxtExercicio->setNull   ( true                           );
$obTxtExercicio->setTitle     ( "Exercício referente ao grupo de crédito." );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgList                  );
$obForm->setTarget           ( "telaPrincipal"          );

//DEFINICAO DO FORMULARIO
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

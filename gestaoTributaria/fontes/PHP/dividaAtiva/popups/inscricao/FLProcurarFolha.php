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
    * Página de Formulario de Filtro para PopUp de Inscricao

    * Data de Criação   : 29/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLProcurarInscricao.php 29252 2008-04-16 14:25:51Z fabio $

    *Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.3  2007/08/08 18:23:53  cercato
correcao do filtro de exercicio.

Revision 1.2  2007/02/28 20:19:31  cercato
Bug #8552#

Revision 1.1  2006/09/29 10:45:38  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_FRAMEWORK."/request/Request.class.php" );
//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarFolha";
$pgList        = "LS".$stPrograma.".php";

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $request->get('stAcao')  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get('stCtrl')  );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName('campoNom');
$obHdnCampoNom->setValue($_REQUEST['campoNom']);

$obHdnLivro = new Hidden;
$obHdnLivro->setName ("campoNum");
$obHdnLivro->setValue ($_REQUEST['campoNum']);

//Inscricao em Divida Ativa
$obTxtLivro = new TextBox;
$obTxtLivro->setRotulo      ( "Livro Dívida Ativa" );
$obTxtLivro->setTitle       ( "Informe o número do Livro." );
$obTxtLivro->setName        ( "inLivro" );
$obTxtLivro->setSize        ( 10 );
$obTxtLivro->setMaxLength   ( 10 );
$obTxtLivro->setNull        ( true );
$obTxtLivro->setInteiro     ( true );

//Exercicio
/*
$obTxtExercicio = new TextBox;
$obTxtExercicio->setTitle       ( "Informe o exercício da inscrição em divida ativa." );
$obTxtExercicio->setName        ( "stExercicio" );
$obTxtExercicio->setNull        ( true );
$obTxtExercicio->setSize        ( 4 );
$obTxtExercicio->setMaxLength   ( 4 );
$obTxtExercicio->setRotulo      ( "Exercício" );
*/
//Página
$obTxtPagina = new TextBox;
$obTxtPagina->setRotulo     ( "Página" );
$obTxtPagina->setTitle      ( "Informe a página." );
$obTxtPagina->setName       ( "inPagina" );
$obTxtPagina->setSize       ( 3 );
$obTxtPagina->setMaxLength  ( 3 );
$obTxtPagina->setNull       ( true );
$obTxtPagina->setInteiro    ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.02" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCampoNom );
$obFormulario->addHidden     ( $obHdnLivro );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addComponente ( $obTxtLivro );
$obFormulario->addComponente ( $obTxtPagina );
//$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->Ok ();

$obFormulario->show();

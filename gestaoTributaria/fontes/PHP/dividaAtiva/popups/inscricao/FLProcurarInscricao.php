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

    * $Id: FLProcurarInscricao.php 59612 2014-09-02 12:00:51Z gelson $

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
include_once(CAM_FRAMEWORK."/request/Request.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarInscricao";
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
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//Inscricao em Divida Ativa
$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo ( "Inscrição em Dívida Ativa" );
$obTxtCodigo->setTitle ( "Informe o número da inscrição em divida atíva." );
$obTxtCodigo->setName ( "inCodigo" );
$obTxtCodigo->setSize ( 20 );
$obTxtCodigo->setMaxLength ( 20 );
$obTxtCodigo->setNull ( true );
$obTxtCodigo->setInteiro ( true );

//Exercicio
$obTxtExercicio = new TextBox;//Exercicio;
$obTxtExercicio->setTitle ( "Informe o exercício da inscrição em divida ativa." );
$obTxtExercicio->setName ( "stExercicio" );
$obTxtExercicio->setNull ( true );
$obTxtExercicio->setSize ( 4 );
$obTxtExercicio->setMaxLength ( 4 );
$obTxtExercicio->setRotulo ( "Exercício" );

//CGM
$obTxtCGM = new TextBox;
$obTxtCGM->setRotulo ( "CGM" );
$obTxtCGM->setTitle ( "Informe o número do CGM do contribuinte." );
$obTxtCGM->setName ( "inCGM" );
$obTxtCGM->setSize ( 20 );
$obTxtCGM->setMaxLength ( 20 );
$obTxtCGM->setNull ( true );
$obTxtCGM->setInteiro ( true );

//Nome
$obTxtNome = new TextBox;
$obTxtNome->setRotulo ( "Nome" );
$obTxtNome->setTitle ( "Informe o nome do contribuinte." );
$obTxtNome->setName ( "stNome" );
$obTxtNome->setSize ( 80 );
$obTxtNome->setMaxLength ( 80 );
$obTxtNome->setNull ( true );
$obTxtNome->setInteiro ( false );

//inscricao imobiliaria
$obTxtInscricaoImobiliaria = new TextBox;
$obTxtInscricaoImobiliaria->setRotulo ( "Inscrição Imobiliária" );
$obTxtInscricaoImobiliaria->setTitle ( "Informe a inscrição imobiliária do imóvel." );
$obTxtInscricaoImobiliaria->setName ( "inInscImob" );
$obTxtInscricaoImobiliaria->setSize ( 20 );
$obTxtInscricaoImobiliaria->setMaxLength ( 20 );
$obTxtInscricaoImobiliaria->setNull ( true );
$obTxtInscricaoImobiliaria->setInteiro ( true );

//inscricao economica
$obTxtInscricaoEconomica = new TextBox;
$obTxtInscricaoEconomica->setRotulo ( "Inscrição Econômica" );
$obTxtInscricaoEconomica->setTitle ( "Informe a inscrição econômica da empresa." );
$obTxtInscricaoEconomica->setName ( "inInscEcon" );
$obTxtInscricaoEconomica->setSize ( 20 );
$obTxtInscricaoEconomica->setMaxLength ( 20 );
$obTxtInscricaoEconomica->setNull ( true );
$obTxtInscricaoEconomica->setInteiro ( true );

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
$obFormulario->addHidden     ( $obHdnCampoNum );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addComponente ( $obTxtCodigo );
$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->addComponente ( $obTxtCGM );
$obFormulario->addComponente ( $obTxtNome );
$obFormulario->addComponente ( $obTxtInscricaoImobiliaria );
$obFormulario->addComponente ( $obTxtInscricaoEconomica );
$obFormulario->Ok ();

$obFormulario->show();

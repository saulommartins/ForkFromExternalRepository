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
    * Página de Formulario de Filtro para Emissao

    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLProcurarEmissao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.03

*/

/*
$Log$
Revision 1.1  2006/09/29 10:50:59  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoDocumento.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarEmissao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('linkPopUp');
Sessao::remove('stLinkPopUp');
//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $request->get('stAcao')  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get('stCtrl')  );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

$obHdnNomCampoCombo = new Hidden;
$obHdnNomCampoCombo->setName( "stNomCampoCombo" );
$obHdnNomCampoCombo->setValue( $request->get('stNomCampoCombo') );

//Inscricao em divida ativa
$obTxtInscricao = new TextBox;
$obTxtInscricao->setRotulo ( "Inscrição em Dívida Ativa" );
$obTxtInscricao->setTitle ( "Informe o número da inscrição em divida atíva." );
$obTxtInscricao->setName ( "stInscricao" );
$obTxtInscricao->setSize ( 20 );
$obTxtInscricao->setMaxLength ( 20 );
$obTxtInscricao->setNull ( true );
$obTxtInscricao->setInteiro ( true );

//exercicio
$obTxtExercicio = new Exercicio;
$obTxtExercicio->setRotulo ( "Exercício" );
$obTxtExercicio->setTitle ( "Informe o exercício da inscrição em divida ativa." );
$obTxtExercicio->setName ( "stExercicio" );
$obTxtExercicio->setNull ( true );

//tipo de documento
$obTAdministracaoTipoDocumento = new TAdministracaoTipoDocumento;
$obTAdministracaoTipoDocumento->recuperaTodos ( $rsTipoDocumento );

$obCmbTipoDocumento = new Select;
$obCmbTipoDocumento->setRotulo  ( "Tipo de Documento" );
$obCmbTipoDocumento->setTitle   ( "Selecione o tipo de documento." );
$obCmbTipoDocumento->setName    ( "inCodTipoDocumento" );
$obCmbTipoDocumento->setId      ( "inCodTipoDocumento" );
$obCmbTipoDocumento->setCampoID ( "cod_tipo_documento" );
$obCmbTipoDocumento->setCampoDesc  ( "descricao" );
$obCmbTipoDocumento->preencheCombo ( $rsTipoDocumento );
$obCmbTipoDocumento->addOption     ( "", "Selecione" );
$obCmbTipoDocumento->setStyle      ( "width: 200px" );

//nome
$obTxtNome = new TextBox;
$obTxtNome->setRotulo ( "Nome" );
$obTxtNome->setTitle ( "Informe o nome do documento." );
$obTxtNome->setName ( "stNome" );
$obTxtNome->setSize ( 80 );
$obTxtNome->setMaxLength ( 80 );
$obTxtNome->setNull ( true );
$obTxtNome->setInteiro ( false );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.03" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnCampoNom );
$obFormulario->addHidden     ( $obHdnCampoNum );
$obFormulario->addHidden ( $obHdnNomCampoCombo );
$obFormulario->addTitulo     ( "Dados para Filtro" );
$obFormulario->addComponente ( $obTxtInscricao );
$obFormulario->addComponente ( $obTxtExercicio );
$obFormulario->addComponente ( $obCmbTipoDocumento );
$obFormulario->addComponente ( $obTxtNome );

$obFormulario->Ok ();
$obFormulario->show();

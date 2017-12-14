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
    * Página de Formulario de Inclusao/Alteracao de Documento

    * Data de Criação   : 25/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_COMPONENTES."ITextBoxSelectTipoFiscalizacao.class.php"                               );

$stAcao   = $_REQUEST['stAcao'];

Sessao::write( 'link', "" );
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) { $stAcao = "incluir"; }

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

$obCompTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao;
$obCompTipoFiscalizacao->setNull(false);

//MONTA FORMULARIO
$obTxtNomDocumento = new TextBox;
$obTxtNomDocumento->setRotulo   ( 'Descrição do Documento'            );
$obTxtNomDocumento->setTitle    ( 'informe a descrição do Documento' );
$obTxtNomDocumento->setName     ( 'nom_documento'                );
$obTxtNomDocumento->setSize     ( 50                                 );
$obTxtNomDocumento->setMaxLength( 80                                 );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm 		( $obForm 	      );
$obFormulario->addHidden     	( $obHdnAcao          );
$obFormulario->addHidden     	( $obHdnCtrl          );
$obFormulario->addTitulo 	( "Dados para Filtro" );
$obCompTipoFiscalizacao->geraFormulario($obFormulario);
$obFormulario->addComponente ( $obTxtNomDocumento);

$obFormulario->Ok();
$obFormulario->show();

//include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php");

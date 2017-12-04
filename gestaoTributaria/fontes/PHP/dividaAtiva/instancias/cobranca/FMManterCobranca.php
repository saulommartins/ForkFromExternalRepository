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
    * Página de Formulario de Cobranca

    * Data de Criação   : 05/02/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMManterCobranca.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.04

*/

/*
$Log$
Revision 1.1  2007/02/09 18:32:04  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCobranca";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obHdnDisposition =  new Hidden;
$obHdnDisposition->setName   ( "stDisposition" );
$obHdnDisposition->setValue  ( Sessao::read("Disposition") );

$obHdnLength =  new Hidden;
$obHdnLength->setName   ( "stLength" );
$obHdnLength->setValue  ( Sessao::read("Length") );

$obHdnArquivo =  new Hidden;
$obHdnArquivo->setName   ( "stArquivo" );
$obHdnArquivo->setValue  ( Sessao::read("Arquivo") );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.04" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnDisposition );
$obFormulario->addHidden     ( $obHdnLength );
$obFormulario->addHidden     ( $obHdnArquivo );
$obFormulario->addTitulo     ( "Documentos para Download" );

$obLabelDownLoad = new Label;
$obLabelDownLoad->setValue ( Sessao::read("Disposition") );
$obLabelDownLoad->setName   ( "stLBArq" );

$obBtnDownLoad = new Button;
$obBtnDownLoad->setName               ( "stBtnArq" );
$obBtnDownLoad->setValue              ( "Download" );
$obBtnDownLoad->setTipo               ( "button" );
$obBtnDownLoad->obEvento->setOnClick  ( "buscaValor('baixarArquivo')" );
$obBtnDownLoad->setDisabled           ( false );

$obFormulario->defineBarra ( array( $obLabelDownLoad, $obBtnDownLoad ), 'left', '' );

$obFormulario->show();

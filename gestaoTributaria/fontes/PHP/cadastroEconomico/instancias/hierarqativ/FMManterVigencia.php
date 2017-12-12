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
    * Página de Formulario de Inclusao de Vigências
    * Data de Criação   : 17/11/2004

    * @author Tonismar Régis Bernardo
    * @ignore

    * $Id: FMManterVigencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.06

*/

/*
$Log$
Revision 1.8  2006/09/15 14:32:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVigencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JSManterHierarquia.js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMNivelAtividade = new RCEMNivelAtividade;

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( 'stAcao' );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "inCodigoVigencia" );
$obHdnCtrl->setValue ( $_REQUEST["inCodigoVigencia"]  );

if ($stAcao == "alterar") {

    $stCodigoVigencia = $_REQUEST["inCodigoVigencia"];
    $obLblCodigoVigencia = new Label;
    $obLblCodigoVigencia->setRotulo ( "Código" );
    $obLblCodigoVigencia->setValue  ( $stCodigoVigencia );
}
$dtDataInicio = $_REQUEST["dtDataInicio"];
$obDtInicio = new Data;
$obDtInicio->setName      ( "dtDataInicio" );
$obDtInicio->setValue     ( $dtDataInicio  );
$obDtInicio->setId        ( "dataInicio" );
$obDtInicio->setRotulo    ( "Data de Início" );
$obDtInicio->setMaxLength ( 20 );
$obDtInicio->setSize      ( 10 );
$obDtInicio->setNull      ( false );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                           );
$obFormulario->setAjuda             ( "UC-05.02.06");
$obFormulario->addHidden            ( $obHdnCtrl                        );
$obFormulario->addTitulo            ( "Dados para Vigência"             );
$obFormulario->addHidden            ( $obHdnAcao                        );
if ($stAcao == "alterar") {
    $obFormulario->addComponente    ( $obLblCodigoVigencia              );
}
$obFormulario->addComponente        ( $obDtInicio                       );
$obFormulario->setFormFocus( $obDtInicio->getid() );

if ($stAcao == "incluir") {
    $obFormulario->Ok       ();
} else {
    $obFormulario->Cancelar ();
}
$obFormulario->show();

?>

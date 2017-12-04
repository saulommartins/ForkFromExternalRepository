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
    * Página de Formulário para o cadastro de corretagem
    * Data de Criação   : 25/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:25  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretor.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCorretagem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgFormInc  = "FM".$stPrograma."Inclusao.php";
$pgFormAlt  = "FM".$stPrograma."Alteracao.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

if (!$_REQUEST["tipoBusca"]) {
    $_REQUEST['tipoBusca'] = "imobiliaria";
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );
//COMPONENTES PARA INCLUSAO
$obRadioImobiliaria = new Radio;
$obRadioImobiliaria->setName      ( "boTipoCorretagem"             );
$obRadioImobiliaria->setId        ( "boTipoCorretagem"             );
$obRadioImobiliaria->setRotulo    ( "Tipo de Corretagem"           );
$obRadioImobiliaria->setValue     ( "imobiliaria"                  );
$obRadioImobiliaria->setLabel     ( "Imobiliária"                  );
$obRadioImobiliaria->setNull      ( false                          );
if ($_REQUEST['tipoBusca'] != 'imobiliaria') {
    $obRadioImobiliaria->setChecked( false               );
} else {
    $obRadioImobiliaria->setChecked( true                );
}

$obRadioCorretor= new Radio;
$obRadioCorretor->setName         ( "boTipoCorretagem"             );
$obRadioCorretor->setId           ( "boTipoCorretagem"             );
$obRadioCorretor->setValue        ( "corretor"                     );
$obRadioCorretor->setLabel        ( "Corretor"                     );
$obRadioCorretor->setNull         ( false                          );
if ($_REQUEST['tipoBusca'] != 'corretor') {
    $obRadioCorretor->setChecked( false                  );
} else {
    $obRadioCorretor->setChecked( true                   );
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgFormInc );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm                               );
$obFormulario->setAjuda ( "UC-05.01.13" );
$obFormulario->addHidden             ( $obHdnCtrl                            );
$obFormulario->addHidden             ( $obHdnAcao                            );
$obFormulario->addTitulo             ( "Dados para corretagem"               );
$obFormulario->addComponenteComposto ( $obRadioImobiliaria, $obRadioCorretor );
$obFormulario->Ok();
$obFormulario->setFormFocus( $obRadioImobiliaria->getId() );
$obFormulario->show ();
?>

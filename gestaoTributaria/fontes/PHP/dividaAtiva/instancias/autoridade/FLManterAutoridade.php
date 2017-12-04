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
    * Página de Formulario de Filtro para Alteracao e Exclusao de Autoridade

    * Data de Criação   : 15/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FLManterAutoridade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.08

*/

/*
$Log$
Revision 1.4  2007/03/01 13:30:45  cercato
Bug #8521#

Revision 1.3  2006/09/29 14:48:27  cercato
correcao da obrigatoriedade do campo tipo.

Revision 1.2  2006/09/26 11:11:52  dibueno
Utilização do componente de Autoridade

Revision 1.1  2006/09/18 17:18:29  cercato
formularios da autoridade de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterAutoridade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

//tipo de autoridade
$obRdbProcurador = new Radio;
$obRdbProcurador->setRotulo   ( "Tipo de Autoridade" );
$obRdbProcurador->setTitle    ( "Informe o tipo de autoridade a ser cadastrada." );
$obRdbProcurador->setName     ( "stTipoAutoridade" );
$obRdbProcurador->setLabel    ( "Procurador Municipal" );
$obRdbProcurador->setValue    ( "procurador" );
$obRdbProcurador->setNull     ( true );

//Tipo de autoridade
$obRdbAutoridade = new Radio;
$obRdbAutoridade->setRotulo   ( "Tipo de Autoridade" );
$obRdbAutoridade->setTitle    ( "Informe o tipo de autoridade a ser cadastrada." );
$obRdbAutoridade->setName     ( "stTipoAutoridade" );
$obRdbAutoridade->setLabel    ( "Autoridade Competente" );
$obRdbAutoridade->setValue    ( "autoridade" );
$obRdbAutoridade->setNull     ( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

$obIPopUpCGMServidor = new IPopUpCGM( $obForm );;
$obIPopUpCGMServidor->setNull ( true );
$obIPopUpCGMServidor->setRotulo ( "Servidor" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.04.08" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Autoridade" );
$obFormulario->addComponenteComposto ( $obRdbProcurador, $obRdbAutoridade );
$obFormulario->AddComponente ( $obIPopUpCGMServidor );

$obFormulario->Ok ();
$obFormulario->show();

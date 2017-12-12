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
    * Página de Filtro para Cadastro de Inscrição Econômica
    * Data de Criação   : 11/01/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FLManterInscricao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.13  2007/02/09 18:33:00  rodrigo
#8342#

Revision 1.12  2006/11/17 12:43:15  domluc
Correção Bug #7437#

Revision 1.11  2006/10/17 13:48:41  dibueno
Utilização de componentes para BuscaInners

Revision 1.10  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php"      );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"			 );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write( "link", "" );
Sessao::write( "stAcao", $_REQUEST[ 'stAcao' ] );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( isset($_REQUEST["stCtrl"]) ? $_REQUEST["stCtrl"] : "" );

$obPopUpEmpresa = new IPopUpEmpresa;
$obPopUpEmpresa->setNull ( true );
$obPopUpEmpresa->obInnerEmpresa->obCampoCod->setName ( "inInscricaoEconomica" );
$obPopUpEmpresa->obInnerEmpresa->obCampoCod->setId ( "inInscricaoEconomica" );

$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINIÇÃO DO FORMULÁRIO

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm    );
$obFormulario->setAjuda      ( "UC-05.02.10");
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Dados para Filtro" );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->obCampoCod->setName ( "inNumCGM" );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "CGM" );
$obFormulario->addComponente( $obPopUpCGM				);

$obPopUpEmpresa->geraFormulario ( $obFormulario );
$obFormulario->Ok();

$obFormulario->Show();

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
    * Filtro para Licenca
    * Data de Criação   : 02/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra

    * $Id: FLAlterarLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.11  2006/11/17 16:42:28  dibueno
Bug #7093#

Revision 1.10  2006/10/17 11:09:08  dibueno
Utilização de componentes para BuscaInners

Revision 1.9  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"        );
include_once ( CAM_GT_CEM_COMPONENTES."ITextLicenca.class.php"        );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php"        	);

//Define o nome dos arquivos PHP
$stPrograma      = "ManterLicenca";
$pgFilt          = "FL".$stPrograma.".php";
$pgFiltAlterar   = "FLAlterarLicenca.php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormAtividade = "FMConcederLicencaAtividade.php";
$pgFormEspecial  = "FMConcederLicencaEspecial.php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$arConfiguracao = array();
$obRCEMLicenca = new RCEMLicenca;
$obRCEMLicenca->recuperaConfiguracao( $arConfiguracao , $sessao );

Sessao::write( "link", "" );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnTipoLicenca = new Hidden;
$obHdnTipoLicenca->setName 	('stTipoLicenca');
$obHdnTipoLicenca->setValue ( $arConfiguracao['numero_licenca'] );

$obRCEMLicenca->obRCEMConfiguracao->consultarConfiguracao();
$obNumeroInscricao  = $obRCEMLicenca->obRCEMConfiguracao->getNumeroInscricao();
$stMascaraInscricao = $obRCEMLicenca->obRCEMConfiguracao->getMascaraInscricao();

$obTextLicenca = new ITextLicenca;
$obPopUpEmpresa = new IPopUpEmpresa;
$obPopUpEmpresa->setNull ( true );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgList                  );
$obForm->setTarget           ( "telaPrincipal"          );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                  	);
$obFormulario->setAjuda     ( "UC-05.02.12"				);
$obFormulario->addHidden    ( $obHdnCtrl               	);
$obFormulario->addHidden    ( $obHdnAcao               	);
$obFormulario->addHidden	( $obHdnTipoLicenca 		);

$obFormulario->addTitulo     ( "Dados para Filtro"      );

$obPopUpEmpresa->geraFormulario ( $obFormulario );
$obTextLicenca->geraFormulario ( $obFormulario );
$obFormulario->Ok();
$obFormulario->show();

?>

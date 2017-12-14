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
    * Filtro para alteração de Elementos da Licenca Geral(Diversa)
    * Data de Criação   : 29/04/2005
    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Regra

    * $Id: FLAlterarLicencaGeral.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.13  2006/11/17 16:42:18  dibueno
Bug #7093#

Revision 1.12  2006/10/17 11:10:14  dibueno
Utilização de componentes para BuscaInners

Revision 1.11  2006/10/11 10:08:36  dibueno
Adaptando cod_licenca de acordo com a configuração do módulo

Revision 1.10  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"        		);
include_once ( CAM_GT_CEM_COMPONENTES."ITextLicenca.class.php"        	);
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"				);

//Define o nome dos arquivos PHP
$stPrograma     = "AlterarLicencaGeral";
$pgFilt         = "FL".$stPrograma.".php"    ;
$pgList         = "LSLicencaGeral.php"       ;
$pgForm         = "FMConcederLicencaGeral.php"       ;
$pgFormTipo     = "FMConcederLicencaGeralTipo.php"   ;
$pgProc         = "PR".$stPrograma.".php"       ;
$pgOcul         = "OCConcederLicencaGeral.php"  ;
$pgJs           = "JS".$stPrograma.".js"        ;
include_once( $pgJs );

Sessao::write( "link", "" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
$arConfiguracao = array();
$obRCEMLicenca = new RCEMLicenca;
$obRCEMLicenca->recuperaConfiguracao( $arConfiguracao , $sessao );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                      ( "stCtrl"                      );
$obHdnCtrl->setValue                     ( $_REQUEST["stCtrl"]           );

$obHdnAcao = new Hidden;
$obHdnAcao->setName                      ( "stAcao"                      );
$obHdnAcao->setValue                     ( $stAcao                       );

//DEFINIÇÃO DOS COMPONENTES
// DADOS PARA LICENÇA

$obHdnTipoLicenca = new Hidden;
$obHdnTipoLicenca->setName 	('stTipoLicenca');
$obHdnTipoLicenca->setValue ( $arConfiguracao['numero_licenca'] );

$obTxtLicenca = new ITextLicenca;
$obTxtLicenca->setTipoLicenca ( 'Diversa');

//DEFINICAO DO FORM

$obForm = new Form;
$obForm->setAction ( $pgList  );
$obForm->setTarget ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo ( "Dados para Filtro");
$obFormulario->addForm   ( $obForm                          );
$obFormulario->setAjuda      ( "UC-05.02.12");
$obFormulario->addHidden ( $obHdnCtrl                       );
$obFormulario->addHidden ( $obHdnAcao                       );
$obFormulario->addHidden ( $obHdnTipoLicenca 				);

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull ( true );
$obPopUpCGM->setRotulo ( "CGM" );
$obPopUpCGM->setTitle ( "CGM" );
$obFormulario->addComponente( $obPopUpCGM				);

$obTxtLicenca->geraFormulario ( $obFormulario );

$obFormulario->ok();
$obFormulario->show();
?>

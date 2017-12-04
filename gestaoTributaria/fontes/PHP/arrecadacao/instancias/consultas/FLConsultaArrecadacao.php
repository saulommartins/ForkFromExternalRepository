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
    * Página de Filtro para Consulta de Imóveis
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: FLConsultaArrecadacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.21  2007/03/01 20:02:09  rodrigo
Bug #5874#

Revision 1.20  2006/11/02 18:55:01  dibueno
*** empty log message ***

Revision 1.19  2006/10/25 19:44:28  hboaventura
bug #6968#

Revision 1.18  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );

include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php");
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaArrecadacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao') ?  $request->get('stAcao') : $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

Sessao::write( 'transf4', array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false ) );
Sessao::write( 'link', array() );

// CONSULTA CONFIGURACAO DO MODULO IMOBILIARIO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();

// CONSULTA CONFIGURACAO DO MODULO ECONOMICO
$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricaoEconomico = $obRCEMConfiguracao->getMascaraInscricao();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );

$obPopUpImovel = new IPopUpImovel;
$obPopUpImovel->obInnerImovel->setNull (true);

$obPopUpEmpresa = new IPopUpEmpresa;
$obPopUpEmpresa->setVerificaInscricao(true);
$obPopUpEmpresa->obInnerEmpresa->setNull (true);

$obTxtNumeracao = new TextBox ;
$obTxtNumeracao->setName            ( "stNumeracao"     );
$obTxtNumeracao->setId              ( "stNumeracao"     );
$obTxtNumeracao->setInteiro         ( true              );
$obTxtNumeracao->setRotulo          ( "Numeração"       );
$obTxtNumeracao->setTitle           ( "Numeração"       );
$obTxtNumeracao->setNull            ( true              );
$obTxtNumeracao->setMaxLength       ( 20                );
$obTxtNumeracao->setSize            ( 20                );

$obTxtExercicio = new TextBox ;
$obTxtExercicio->setName       	( "stExercicio"     );
$obTxtExercicio->setId          ( "stExercicio"     );
$obTxtExercicio->setInteiro     ( true          	);
$obTxtExercicio->setMaxLength 	( 4             	);
$obTxtExercicio->setSize        ( 4             	);
$obTxtExercicio->setRotulo      ( "Exercício"       );
$obTxtExercicio->setTitle       ( "Exercício"       );
$obTxtExercicio->setNull        ( true              );
$obTxtExercicio->setValue       ( Sessao::getExercicio());

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addTitulo( "Dados para Filtro"   );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull 	( true );
$obPopUpCGM->setRotulo 	( "Contribuinte" );
$obPopUpCGM->setTitle 	( "Código do Contribuinte" );
$obFormulario->addComponente( $obPopUpCGM );

$obPopUpEmpresa->geraFormulario ( $obFormulario );
$obPopUpImovel->geraFormulario  ( $obFormulario );
$obFormulario->addComponente	( $obTxtNumeracao );
$obFormulario->addComponente	( $obTxtExercicio );

$obFormulario->ok();
$obFormulario->show();

?>

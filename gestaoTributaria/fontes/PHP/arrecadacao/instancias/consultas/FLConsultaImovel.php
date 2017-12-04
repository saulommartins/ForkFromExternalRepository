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

    * $Id: FLConsultaImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.4  2007/02/05 14:55:59  cercato
Bug #7531#

Revision 1.3  2007/02/05 13:04:50  cercato
Bug #7530#

Revision 1.2  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCConsultaArrecadacao.php";
$pgJS   = "JSConsultaArrecadacao.js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
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
$obHdnCtrl->setValue ( $stCtrl );

$obBscContribuinte = new BuscaInner;
$obBscContribuinte->setId                ( "stContribuinte"          );
$obBscContribuinte->setRotulo        ( "Contribuinte"            );
$obBscContribuinte->setTitle            ( "Código do Contribuinte"  );
$obBscContribuinte->setNull             ( true                     );
$obBscContribuinte->obCampoCod->setName         ("inCodContribuinte"    );
$obBscContribuinte->obCampoCod->setValue        ( $inCodContribuinte    );
$obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinte');");
$obBscContribuinte->obCampoCod->obEvento->setOnKeyPress("iniciaAutoComplete(event,this);");
$obBscContribuinte->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinte','stContribuinte','','".Sessao::getId()."','800','450');" );

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull                 ( true                               );
$obBscInscricaoMunicipal->setRotulo             ( "Inscrição Imobiliária"            );
$obBscInscricaoMunicipal->setTitle                ( "Inscrição Imobiliária"            );
$obBscInscricaoMunicipal->setId                    ( "stInscricaoInscricaoImobiliaria"  );
$obBscInscricaoMunicipal->obCampoCod->setName      ( "inInscricaoImobiliaria"           );
$obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)        );
$obBscInscricaoMunicipal->obCampoCod->setMascara   ( $stMascaraInscricao                );
$obBscInscricaoMunicipal->obCampoCod->setInteiro   ( false                              );
$obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange("buscaValor('procuraImovel');");
$obBscInscricaoMunicipal->setFuncaoBusca( "abrePopUp( '".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php', 'frm', 'inInscricaoImobiliaria', 'stInscricaoInscricaoImobiliaria', 'todos', '".Sessao::getId()."', '800', '550' );" );

$obTxtExercicio = new TextBox ;
$obTxtExercicio->setName       ( "stExercicio"     );
$obTxtExercicio->setId             ( "stExercicio"     );
$obTxtExercicio->setMaxLength   ( 4                 );
$obTxtExercicio->setSize          ( 6 );
$obTxtExercicio->setRotulo      ( "Exercício"       );
$obTxtExercicio->setTitle         ( "Exercício"       );
$obTxtExercicio->setNull          ( true              );
$obTxtExercicio->setValue       ( $_REQUEST["stExercicio"]?$_REQUEST["stExercicio"]:Sessao::getExercicio() );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao            );
$obFormulario->addHidden( $obHdnCtrl            );
$obFormulario->addTitulo( "Dados para Filtro"   );
$obFormulario->addComponente( $obBscContribuinte       );
$obFormulario->addComponente( $obBscInscricaoMunicipal );
$obFormulario->addComponente( $obTxtExercicio          );

$obFormulario->ok();
$obFormulario->show();

?>

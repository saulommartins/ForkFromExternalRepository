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
    * Arquivo de popup para manutenção de funções
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.03.95

    $Id: FLBuscarFuncao.php 64004 2015-11-17 15:41:41Z evandro $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RFuncao.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoModulo.class.php";
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoBiblioteca.class.php");

$stPrograma = "BuscarFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$stAcao = $request->get('stAcao');

if ( $request->get('stCodModulo') == '' ) {
    $inCodModulo = (Sessao::read('stCodModulo') == '') ? $_REQUEST['inCodModulo'] : Sessao::read('stCodModulo') ;
}else{
    $inCodModulo = $request->get('stCodModulo');
}
if ( $request->get('stCodBiblioteca') == '' ) {
    $inCodBiblioteca = (Sessao::read('stCodBiblioteca') == '') ? $_REQUEST['inCodBiblioteca'] : Sessao::read('stCodBiblioteca') ;
}else{
    $inCodBiblioteca = $request->get('stCodBiblioteca');
}

# Condição para armazenar na sessão quando telas específicas passaram a
# biblioteca ou módulo permitidos para listagem.
Sessao::write('stCodModulo'     , $inCodModulo );
Sessao::write('stCodBiblioteca' , $inCodBiblioteca );

$obHdnInCodBiblioteca = new Hidden;
$obHdnInCodBiblioteca->setName ( 'inCodBiblioteca' );
$obHdnInCodBiblioteca->setValue( $inCodBiblioteca  );

$obHdnInCodModulo = new Hidden;
$obHdnInCodModulo->setName ( 'inCodModulo' );
$obHdnInCodModulo->setValue( $inCodModulo  );

$obTAdministracaoModulo = new TModulo;
$obTAdminsitracaoBiblioteca = new TAdministracaoBiblioteca();

//Caso o modulo venha vazio significa que o usuario pode usar qualquer funcao na acao que esta sendo chamada
if ($inCodModulo != '') {
    $stCondicao = " WHERE cod_modulo IN ( ".$inCodModulo." )";
}
$obTAdministracaoModulo->recuperaListaModulos($rsModulos, $stCondicao, "", "", "");

$rsBiblioteca = new RecordSet();
if ($inCodBiblioteca != '') {
    $stFiltro = " WHERE cod_modulo IN ( ".$inCodModulo." ) AND cod_biblioteca IN ( ".$inCodBiblioteca." )";
    $obTAdminsitracaoBiblioteca->recuperaTodos($rsBiblioteca,$stFiltro,"cod_biblioteca");
}

$obCmbModulo = new Select;
$obCmbModulo->setRotulo     ( "Módulo Origem"     );
$obCmbModulo->setId         ( "inCodModulo"       );
$obCmbModulo->setName       ( "inCodModulo"       );
$obCmbModulo->setTitle      ( "Informe o módulo." );
$obCmbModulo->setStyle      ( "width: 250px"      );
$obCmbModulo->setNull       ( false               );
$obCmbModulo->addOption     ( "","Selecione"      );
$obCmbModulo->setCampoId    ( "cod_modulo"        );
$obCmbModulo->setCampoDesc  ( "nom_modulo"        );
$obCmbModulo->preencheCombo ( $rsModulos          );
$obCmbModulo->setValue      ( $inCodModulo        );
$obCmbModulo->obEvento->setOnChange ( "executaFuncaoAjax('preencherBiblioteca','&inCodModulo='+this.value+'&inCodBiblioteca=".$inCodBiblioteca."');");

$obCmbBiblioteca = new Select;
$obCmbBiblioteca->setRotulo             ( "Biblioteca Origem"     );
$obCmbBiblioteca->setId                 ( "inCodBiblioteca"       );
$obCmbBiblioteca->setName               ( "inCodBiblioteca"       );
$obCmbBiblioteca->setTitle              ( "Informe a bibliotéca." );
$obCmbBiblioteca->setStyle              ( "width: 250px"          );
$obCmbBiblioteca->setNull               ( false                   );
$obCmbBiblioteca->addOption             ( "","Selecione"          );
$obCmbBiblioteca->setCampoId            ( "cod_biblioteca"        );
$obCmbBiblioteca->setCampoDesc          ( "nom_biblioteca"        );
$obCmbBiblioteca->preencheCombo         ( $rsBiblioteca           );
$obCmbBiblioteca->setValue              ( $inCodBiblioteca        );
$obCmbBiblioteca->obEvento->setOnchange ( "limpaCampoFuncao();"   );

$obHdnForm = new Hidden;
$obHdnForm->setName( 'nomForm' );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( 'campoNum' );
$obHdnCampoNum->setValue(  $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( 'campoNom' );
$obHdnCampoNom->setValue(  $_REQUEST['campoNom'] );

$obHdnCampoFuncao = new Hidden;
$obHdnCampoFuncao->setName ( 'stNomeFuncao2' );
$obHdnCampoFuncao->setValue( $_REQUEST['stNomeFuncao'] );

$obHdnTipoFuncaoBusca = new Hidden;
$obHdnTipoFuncaoBusca->setName ( 'tipoBusca' );
$obHdnTipoFuncaoBusca->setValue( $_REQUEST['tipoBusca'] );

$obTxtNomeFuncao = new TextBox;
$obTxtNomeFuncao->setRotulo        ( "Nome" );
$obTxtNomeFuncao->setName          ( "stNomeFuncao" );
$obTxtNomeFuncao->setTitle         ( "Nome da fórmula de cálculo desejada." );
$obTxtNomeFuncao->setValue         ( $_REQUEST['stNomeFuncao'] );
$obTxtNomeFuncao->setSize          ( 60 );
$obTxtNomeFuncao->setMaxLength     ( 60 );
$obTxtNomeFuncao->setNull          ( true );

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

$obIFrame2 = new IFrame;
$obIFrame2->setName("telaMensagem");
$obIFrame2->setWidth("100%");
$obIFrame2->setHeight("50");

$obForm = new Form;
$obForm->setAction ( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addTitulo ( "Dados para Filtro" );
$obFormulario->addHidden ( $obHdnForm     );
$obFormulario->addHidden ( $obHdnCampoNum );
$obFormulario->addHidden ( $obHdnCampoNom );
$obFormulario->addHidden ( $obHdnCampoFuncao );
$obFormulario->addHidden ( $obHdnTipoFuncaoBusca );

$obFormulario->addComponente ( $obCmbModulo     );
$obFormulario->addComponente ( $obCmbBiblioteca );
$obFormulario->addComponente ( $obTxtNomeFuncao );

$obBtnOk = new Button;
$obBtnOk->setName("btnOK");
$obBtnOk->setValue ("OK");
$obBtnOk->setTipo ("button");
$obBtnOk->obEvento->setOnClick("filtrar();");
$obBtnOk->setDisabled( false );

$obBtnLimpar = new Button;
$obBtnLimpar->setName               ( "btnLimpar" );
$obBtnLimpar->setValue              ( "Limpar" );
$obBtnLimpar->setTipo               ( "button" );
$obBtnLimpar->obEvento->setOnClick  ( "limpar();" );
$obBtnLimpar->setDisabled           ( false );

$botoes = array( $obBtnOk, $obBtnLimpar );

$obFormulario->defineBarra($botoes, 'left', '');
$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

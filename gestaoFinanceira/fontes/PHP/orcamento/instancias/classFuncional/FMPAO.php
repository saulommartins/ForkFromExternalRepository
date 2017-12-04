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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: rodrigo_sr $
    $Date: 2007-09-12 12:42:46 -0300 (Qua, 12 Set 2007) $

    * Casos de uso: uc-02.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php"       );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"  );

//Define o nome dos arquivos PHP
$stProjeto = "PAO";
$pgFilt = "FL".$stProjeto.".php";
$pgList = "LS".$stProjeto.".php";
$pgForm = "FM".$stProjeto.".php";
$pgProc = "PR".$stProjeto.".php";
$pgOcul = "OC".$stProjeto.".php";
$pgJS   = "JS".$stProjeto.".js";
include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

// Consulta a configuração para selecionar o GRUPO X
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();
$stMascara = $obRConfiguracaoOrcamento->getMascDespesa();
$arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);
// Grupo X;
$stMascara = $arMarcara[5];
$stDigitoProjeto   = $obRConfiguracaoOrcamento->getNumPAODigitosIDProjeto();
$stDigitoAtividade = $obRConfiguracaoOrcamento->getNumPAODigitosIDAtividade();
$stDigitoOrperacao = $obRConfiguracaoOrcamento->getNumPAODigitosIDOperEspeciais();
$inPosicao         = $obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID();

if ($stAcao == "alterar") {
    $inTipoPAO = $_REQUEST['inNumeroProjeto']{$inPosicao-1};
    if ( in_array( $inTipoPAO, explode( ',', $stDigitoProjeto ) ) ) {
        $inTipoPAO = $stDigitoProjeto;
        $stTipoPAO = 'Projeto';
    }
    if ( in_array( $inTipoPAO, explode( ',', $stDigitoAtividade ) ) ) {
        $inTipoPAO = $stDigitoAtividade;
        $stTipoPAO = 'Atividade';
    }
    if ( in_array( $inTipoPAO, explode( ',', $stDigitoOrperacao ) ) ) {
        $inTipoPAO = $stDigitoOrperacao;
        $stTipoPAO = 'Operação Especial';
    }
    $jsOnload = "executaFuncaoAjax('montaSpan','&inNumeroProjeto=".$_REQUEST['inNumeroProjeto']."&inTipoPAO=".$inTipoPAO."&stAcao=".$stAcao."')";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnMascara = new Hidden;
$obHdnMascara->setName  ( "stMascara" );
$obHdnMascara->setValue ( $stMascara  );

if ($stAcao == 'incluir') {
    $obCmbTipoPAO = new Select();
    $obCmbTipoPAO->setName  ( "inTipoPAO"                             );
    $obCmbTipoPAO->setRotulo( "Tipo"                                  );
    $obCmbTipoPAO->setTitle ( "Selecione o tipo."                     );
    $obCmbTipoPAO->setNull  ( false                                   );
    $obCmbTipoPAO->setValue ( $inTipoPAO                              );
    $obCmbTipoPAO->addOption( "", "Selecione"                         );
    $obCmbTipoPAO->addOption( $stDigitoProjeto  , "Projeto"           );
    $obCmbTipoPAO->addOption( $stDigitoAtividade, "Atividade"         );
    $obCmbTipoPAO->addOption( $stDigitoOrperacao, "Operação Especial" );
    $obCmbTipoPAO->obEvento->setOnChange( "montaParametrosGET('montaSpan')" );
    if ( strlen( trim($_REQUEST['inTipoPAO']) ) ) {
        $jsOnLoad = "montaParametrosGET('montaSpan')";
    }
} elseif ($stAcao == 'alterar') {
    $obHdnTipoPAO = new Hidden();
    $obHdnTipoPAO->setName( 'inTipoPAO' );
    $obHdnTipoPAO->setValue( $inTipoPAO );

    $obLblTipoPao = new Label;
    $obLblTipoPao->setRotulo( "Tipo" );
    $obLblTipoPao->setValue( $stTipoPAO );
}

$obSpan = new Span();
$obSpan->setId( "spnPAO");

$obSpanLista = new Span();
$obSpanLista->setId( "spnListaPAO");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.03"                );
$obFormulario->addHidden( $obHdnMascara                );
$obFormulario->addHidden( $obHdnCtrl                   );
$obFormulario->addHidden( $obHdnAcao                   );

$obFormulario->addTitulo( "Dados para o Projeto, Atividades ou Operações Especiais" );
if ($stAcao == 'incluir') {
    $obFormulario->addComponente( $obCmbTipoPAO );
} elseif ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnTipoPAO );
    $obFormulario->addComponente( $obLblTipoPao );
}
$obFormulario->addSpan      ( $obSpan       );

//Define os botões de ação do formulário
$obBtnOK = new OK;
$obBtnOK->setId( "ok");

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Cancelar" );
$obBtnLimpar->obEvento->setOnClick ( "limpar();" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$obFormulario->addSpan ( $obSpanLista );

$arBtn = array();
$arBtn[] = $obBtnOK;
$arBtn[] = $obBtnLimpar;
if ($stAcao=='alterar') {
    $obFormulario->Cancelar($stLocation);
} else {
$obFormulario->defineBarra( $arBtn );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

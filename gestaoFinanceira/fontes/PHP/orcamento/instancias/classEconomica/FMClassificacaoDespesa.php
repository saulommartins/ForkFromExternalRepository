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
    * Data de Criação   : 13/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2007-06-25 12:41:26 -0300 (Seg, 25 Jun 2007) $

    * Casos de uso: uc-02.01.04
*/

/*
$Log$
Revision 1.9  2007/06/25 15:41:26  vitor
Bug#9467#

Revision 1.8  2007/05/21 18:55:34  melo
Bug #9229#

Revision 1.7  2006/07/18 20:25:03  leandro.zis
Bug #6379#

Revision 1.6  2006/07/05 20:42:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ClassificacaoDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;

//Recupera Mascara da Classificao de Despesa
$mascClassificacao = $obROrcamentoClassificacaoDespesa->recuperaMascara();

if ($stAcao == 'alterar') {
    $inCodClassificacao = $_GET['stMascClassDespesa'];
    $stMascClassDespesa = $_GET['stMascClassDespesa'];
    $stDescricao        = $_GET['stDescricao'];
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

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName ( "inCodConta" );
$obHdnCodConta->setValue( $_REQUEST['inCodConta'] );

$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName ( "inCodClassificacao" );
$obHdnCodClassificacao->setValue( $stMascClassDespesa );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $mascClassificacao );

//Define o objeto TEXT para armazenar o CODIGO DA CLASSIFICACAO DE DESPESA
$obTxtCodDespesa = new TextBox;
$obTxtCodDespesa->setName     ( "inCodClassificacao" );
$obTxtCodDespesa->setValue    ( $inCodClassificacao );
$obTxtCodDespesa->setRotulo   ( "Código" );
$obTxtCodDespesa->setSize     ( strlen($mascClassificacao) );
$obTxtCodDespesa->setMaxLength( strlen($mascClassificacao) );
$obTxtCodDespesa->setNull     ( false );
$obTxtCodDespesa->setTitle    ( "Código da rubrica da despesa" );
//$obTxtCodDespesa->setInteiro  ( true );
$obTxtCodDespesa->obEvento->setOnKeyUp("mascaraDinamico('".$mascClassificacao."', this, event);");
$obTxtCodDespesa->obEvento->setOnChange("frm.Ok.disabled = true;buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."')");

$obLblCodDespesa = new Label;
$obLblCodDespesa->setRotulo( "Código" );
$obLblCodDespesa->setValue( $inCodClassificacao );

//Define o objeto TEXT para armazenar a DESCRICAO DA CLASSIFICACAO DE DESPESA
$obTxtDescDespesa = new TextBox;
$obTxtDescDespesa->setName     ( "stDescricao" );
$obTxtDescDespesa->setValue    ( $stDescricao );
$obTxtDescDespesa->setRotulo   ( "Descrição" );
$obTxtDescDespesa->setSize     ( 80 );
$obTxtDescDespesa->setMaxLength( 80 );
$obTxtDescDespesa->setNull     ( false );
$obTxtDescDespesa->setTitle    ( "Descrição da rubrica da despesa" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.04"             );

$obFormulario->addHidden( $obHdnCtrl                );
$obFormulario->addHidden( $obHdnAcao                );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obFormulario->addHidden( $obHdnCodConta            );

$obFormulario->addTitulo( "Dados para Rubrica de Despesa" );
if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnCodClassificacao      );
    $obFormulario->addComponente( $obLblCodDespesa        );
} else {
    $obFormulario->addComponente( $obTxtCodDespesa        );
}
$obFormulario->addComponente( $obTxtDescDespesa           );

//Define os botões de ação do formulário
$obBtnOK = new OK;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "document.frm.reset();" );

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao;

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

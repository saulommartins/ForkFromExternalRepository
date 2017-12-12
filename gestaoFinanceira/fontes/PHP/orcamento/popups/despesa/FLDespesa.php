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
    * Página de Formulario de Inclusao/Alteracao
    * Data de Criação   : 20/11/2006

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 32678 $
    $Name$
    $Autor: $
    $Date: 2007-07-27 11:52:19 -0300 (Sex, 27 Jul 2007) $

    * Casos de uso: uc-02.01.26
*/

/*
$Log$
Revision 1.3  2007/07/27 14:52:19  luciano
Bug#8887#

Revision 1.2  2007/05/11 14:02:06  cako
Bug #8887#

Revision 1.1  2006/11/20 22:57:25  gelson
Bug #7155#

Revision 1.1  2006/07/05 20:43:43  lucas

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Despesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;

//Recupera Mascara da Classificao de Despesa
$mascClassificacao = $obROrcamentoClassificacaoDespesa->recuperaMascara();

//Definições das funções de formulário
$stFncJavaScript .= " function buscaValor() { \n";
$stFncJavaScript .= "     document.frm.target = 'oculto'; \n";
$stFncJavaScript .= "     document.frm.stCtrl.value = 'mascaraClassificacao'; \n";
$stFncJavaScript .= "     document.frm.action = '".$pgOcul."?".Sessao::getId()."'; \n";
$stFncJavaScript .= "     document.frm.submit(); \n";
$stFncJavaScript .= "     document.frm.target = ''; \n";
$stFncJavaScript .= "     document.frm.action = '".$pgList."?".Sessao::getId()."'; \n";
$stFncJavaScript .= " } \n";

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('filtroPopUp');
Sessao::remove('link');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_REQUEST['stCtrl'] );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum']);

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

if (is_array($_REQUEST["inCodEntidade"])) {
    $stEntidades = implode(',', $_REQUEST["inCodEntidade"]);
} else {
    $stEntidades = $_REQUEST["inCodEntidade"];
}

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName( "inCodEntidade" );
$obHdnEntidade->setValue( $stEntidades );
Sessao::write('inCodEntidade',$_REQUEST['inCodEntidade']);

//Define o objeto TEXT
$obTxtCodDespesa = new TextBox;
$obTxtCodDespesa->setName     ( "inCodDespesa" );
$obTxtCodDespesa->setValue    ( $_POST["inCodDespesa"] );
$obTxtCodDespesa->setRotulo   ( "Código Reduzido" );
$obTxtCodDespesa->setSize     ( 10 );
$obTxtCodDespesa->setMaxLength( 10 );
$obTxtCodDespesa->setNull     ( true );
$obTxtCodDespesa->setTitle    ( 'Informe um código' );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtCodClassificacao = new TextBox;
$obTxtCodClassificacao->setName     ( "stMascClassificacaoDespesa" );
$obTxtCodClassificacao->setValue    ( $_REQUEST['stMascClassificacaoDespesa'] );
$obTxtCodClassificacao->setRotulo   ( "Classificação da Despesa" );
$obTxtCodClassificacao->setSize     ( strlen($_REQUEST['mascClassificacao']) );
$obTxtCodClassificacao->setMaxLength( strlen($_REQUEST['mascClassificacao']) );
$obTxtCodClassificacao->setNull     ( true );
$obTxtCodClassificacao->setTitle    ( 'Informe um código' );
$obTxtCodClassificacao->obEvento->setOnKeyUp("mascaraDinamico('".$mascClassificacao."', this, event);");
$obTxtCodClassificacao->obEvento->setOnChange("buscaValor();");

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDescDespesa = new TextBox;
$obTxtDescDespesa->setName     ( "stDescricao" );
$obTxtDescDespesa->setRotulo   ( "Descrição" );
$obTxtDescDespesa->setSize     ( 80 );
$obTxtDescDespesa->setMaxLength( 80 );
$obTxtDescDespesa->setNull     ( true );
$obTxtDescDespesa->setTitle    ( 'Informe uma descrição' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnEntidade          );
$obFormulario->addHidden( $obHdnForm              );
$obFormulario->addHidden( $obHdnTipoBusca         );
$obFormulario->addHidden( $obHdnCampoNum          );
$obFormulario->addHidden( $obHdnCampoNom          );

$obFormulario->addTitulo( "Dados para filtro de Despesa" );
$obFormulario->addComponente( $obTxtCodDespesa   );
//$obFormulario->addComponente( $obTxtCodClassificacao   );
$obFormulario->addComponente( $obTxtDescDespesa  );

$obFormulario->addIFrameOculto("oculto");

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

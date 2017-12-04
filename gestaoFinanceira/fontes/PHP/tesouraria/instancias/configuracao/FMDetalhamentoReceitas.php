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
    * Página de Detalhamento de Receitas
    * Data de Criação   : 05/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 32867 $
    $Name$
    $Autor:$
    $Date: 2007-08-14 11:43:56 -0300 (Ter, 14 Ago 2007) $

    * Casos de uso: uc-02.04.03

*/

/*
$Log$
Revision 1.11  2007/08/14 14:38:53  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.10  2007/08/13 18:48:16  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.9  2007/07/18 20:19:44  vitor
Bug#8920#

Revision 1.8  2007/05/29 14:11:35  domluc
Mudanças na forma de classificação de receitas.

Revision 1.7  2007/03/09 15:41:51  domluc
uc-02.04.33

Revision 1.6  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
include_once( CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php"     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DetalhamentoReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($_REQUEST['stTipoReceita'] == 'extra') {
    // buscar creditos e acrescimos extra-orcamentarios
    require_once( CAM_GF_CONT_MAPEAMENTO . 'TContabilidadePlanoConta.class.php');
    $obTPlanoConta = new TContabilidadePlanoConta();
    $obTPlanoConta->setDado('codigo', $_REQUEST['inCodigo']);
    $obTPlanoConta->setDado('exercicio', $_REQUEST['stExercicio'] );
    $obTPlanoConta->recuperaClassReceitasCreditosExtraOrcamentarios( $rsCreditos );

} elseif ($_REQUEST['stTipoReceita'] == 'orcamentaria') {
    // buscar creditos e acrescimos orcamentarios
    require_once( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoReceita.class.php');
    $obTReceita = new TOrcamentoReceita();
    $obTReceita->setDado('codigo', $_REQUEST['inCodigo']);
    $obTReceita->setDado('exercicio', $_REQUEST['stExercicio'] );
    $obTReceita->recuperaClassReceitasCreditosOrcamentarios( $rsCreditos );
}

//$arCredito = Sessao::read('arCredito');
$arCredito = $rsCreditos->arElementos;
Sessao::write('arCredito', $arCredito);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName( "inCodigo" );
$obHdnCodConta->setValue( $_REQUEST['inCodigo'] );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setValue( $_REQUEST['stExercicio'] );

$obHdnTipoReceita = new Hidden;
$obHdnTipoReceita->setName( "stTipoReceita" );
$obHdnTipoReceita->setId  ( "stTipoReceita" );
$obHdnTipoReceita->setValue( $_REQUEST['stTipoReceita'] );

if ($_REQUEST['stCodEntidade'] != 'cod_entidade') {
    $jsOnload = "executaFuncaoAjax('montaEntidade','&stExercicio=".$_REQUEST['stExercicio']."&stCodEntidade=".$_REQUEST['stCodEntidade']."'); ";
}

// Define objeto Label para codigo reduzido da conta
$obLblCodReduzido = new Label();
$obLblCodReduzido->setRotulo( 'Código Reduzido'            );
$obLblCodReduzido->setValue ( $_REQUEST['inCodigo']      );

// Define objeto Label para descrição da conta
$obLblDescricao = new Label();
$obLblDescricao->setRotulo( 'Descrição'            );
$obLblDescricao->setValue ( $_REQUEST['stDescricao']            );

//Define objeto BuscaInner para credito
$obIPopUpCredito     = new IPopUpCredito;
$obIPopUpCredito->setNull        ( true          );
$obIPopUpCredito->setObrigatorioBarra( true );
$obIPopUpCredito->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('montaComboAcrescimos',false);" );

//Define objeto Button para botão incluir
$obBtnIncluir = new Button();
$obBtnIncluir->setValue     ( 'Incluir' );
$obBtnIncluir->setId        ( 'btnIncluir' );
$obBtnIncluir->obEvento->setOnClick( "montaParametrosGET( 'insereCredito', '' );" );

//Define objeto Button para botão limpar
$obBtnLimpar = new Button();
$obBtnLimpar->setValue ( 'Limpar' );
$obBtnLimpar->obEvento->setOnCLick( "montaParametrosGET('limpaCredito','',false);" );

// Define objeto Span para
$obSpnLista = new Span();
$obSpnLista->setId( 'spnLista' );

$obSpnAcrescimo  = new Span();
$obSpnAcrescimo->setId( 'spnAcrescimo' );

$obSpnEntidade = new Span();
$obSpnEntidade->setId( 'spnEntidade' );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm                 );
$obFormulario->addHidden          ( $obHdnAcao              );
$obFormulario->addHidden          ( $obHdnCtrl              );
$obFormulario->addHidden          ( $obHdnExercicio         );
$obFormulario->addHidden          ( $obHdnCodConta          );
$obFormulario->addHidden          ( $obHdnCodEntidade       );
$obFormulario->addHidden          ( $obHdnTipoReceita       );

//if ($obLblCodEntidade)
//$obFormulario->addComponente      ( $obLblCodEntidade       );

$obFormulario->addSpan            ( $obSpnEntidade          );

$obFormulario->addComponente      ( $obLblCodReduzido       );
$obFormulario->addComponente      ( $obLblDescricao         );

$obFormulario->addTitulo          ( 'Créditos de Arrecadação' );
$obIPopUpCredito->geraFormulario ( $obFormulario );
$obFormulario->addSpan            ( $obSpnAcrescimo );
$obFormulario->agrupaComponentes  ( array(  $obBtnIncluir, $obBtnLimpar ) );
$obFormulario->addSpan            ( $obSpnLista );

$stLocation = 'FMManterReceitas.php?'.Sessao::getId();
$obFormulario->Cancelar( $stLocation );

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

echo "<script>";
echo "	montaParametrosGET('montaLista','stAcao'); ";
echo "</script>";

?>

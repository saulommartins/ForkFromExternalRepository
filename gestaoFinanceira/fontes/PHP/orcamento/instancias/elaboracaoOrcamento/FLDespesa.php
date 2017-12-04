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
    * Data de Criação   : 28/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.11  2007/07/27 22:39:56  tonismar
Bug#9112#

Revision 1.10  2007/07/25 21:13:17  bruce
Bug#9112#

Revision 1.9  2007/07/06 14:24:44  bruce
Bug #9112#

Revision 1.8  2007/05/21 18:58:09  melo
Bug #9229#

Revision 1.7  2006/07/14 19:50:12  leandro.zis
Bug #6382#

Revision 1.6  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                 );
include_once ( CAM_GF_ORC_COMPONENTES."MontaDotacaoOrcamentaria.class.php"      );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpDotacao.class.php"        );
include_once ( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"            );

include_once ( CAM_GF_ORC_COMPONENTES."IPopUpDotacaoFiltro.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Despesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->consultarConfiguracao();

$obROrcamentoDespesa = new ROrcamentoDespesa;

//Recupera Mascara da Classificao de Despesa
$obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setExercicio(Sessao::getExercicio());
$mascClassificacao = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $mascClassificacao );

$obISelectEntidadeGeral = new ISelectMultiploEntidadeUsuario();
$obISelectEntidadeGeral->setNull ( true );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtCodClassificacao = new TextBox;
$obTxtCodClassificacao->setName     ( "inCodClassificacao" );
$obTxtCodClassificacao->setValue    ( $inCodClassificacao );
$obTxtCodClassificacao->setRotulo   ( "Código" );
$obTxtCodClassificacao->setTitle    ( "Informe o código." );
$obTxtCodClassificacao->setSize     ( strlen($mascClassificacao) );
$obTxtCodClassificacao->setMaxLength( strlen($mascClassificacao) );
$obTxtCodClassificacao->setNull     ( true );
$obTxtCodClassificacao->obEvento->setOnKeyUp("mascaraDinamico('".$mascClassificacao."', this, event);");
$obTxtCodClassificacao->obEvento->setOnChange("buscaValor('mascaraClassificacaoFiltro','".$pgOcul."','".$pgList."','telaPrincipal','".Sessao::getId()."')");

$obBscReduzido = new IIntervaloPopUpDotacao( $obISelectEntidadeGeral );
$obBscReduzido->setAutorizacao( "autorizacaoOrcamento" );

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDesc = new TextBox;
$obTxtDesc->setName     ( "stDescricao" );
$obTxtDesc->setRotulo   ( "Descrição" );
$obTxtDesc->setSize     ( 80 );
$obTxtDesc->setMaxLength( 80 );
$obTxtDesc->setNull     ( true );
$obTxtDesc->setTitle    ( 'Informe a descrição.' );

$obMontaDotacaoOrcamentaria = new MontaDotacaoOrcamentaria;
$obMontaDotacaoOrcamentaria->setName           ('stDotacaoOrcamentaria');
$obMontaDotacaoOrcamentaria->setRotulo         ('Dotação Orcamentaria' );
$obMontaDotacaoOrcamentaria->setActionAnterior ( $pgOcul );
$obMontaDotacaoOrcamentaria->setActionPosterior( $pgList );
$obMontaDotacaoOrcamentaria->setTarget         ( 'telaPrincipal' );
$obMontaDotacaoOrcamentaria->setNull           ( true );

//$obBscRecurso = new IPopUpRecurso;
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
if($stAcao != 'incluir') $obIMontaRecursoDestinacao->setFiltro( true );

$stMascaraRubrica    = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
//$size =  strlen($stMascaraRubrica) + 10;
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Rubrica de Despesa" );
$obBscRubricaDespesa->setTitle                ( "Informe a rubrica de despesa." );
$obBscRubricaDespesa->setNull                 ( true );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa" );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setId       ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setValue    ( '' );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnChange( "preencheComZeros( '".$stMascaraRubrica."', this, 'D' );" );
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

if ( $obRConfiguracaoOrcamento->getFormaExecucaoOrcamento() == "1" )
    $obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("montaParametrosGET('mascaraClassificacao');");
else
    $obBscRubricaDespesa->setValoresBusca( CAM_GF_ORC_POPUPS.'classificacaodespesa/OCClassificacaoDespesa.php?'.Sessao::getId(), $obForm->getName(), '' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.06"           );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnMascClassificacao );

$obFormulario->addTitulo( "Dados para Filtro"         );
$obFormulario->addComponente( $obISelectEntidadeGeral );
$obFormulario->addComponente( $obBscReduzido          );
//$obFormulario->addComponente( $obTxtCodClassificacao  );
$obFormulario->addComponente( $obTxtDesc              );
$obMontaDotacaoOrcamentaria->geraFormulario( $obFormulario,$stAcao);
$obFormulario->addComponente ( $obBscRubricaDespesa );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

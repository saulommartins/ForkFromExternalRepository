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
    * Data de Criação: 04/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26727 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-12 16:31:31 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.1  2007/10/17 13:42:13  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php");
include_once( CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php");
include_once( CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once( CAM_GA_ADM_COMPONENTES."IMontaLocalizacao.class.php" );

$stPrograma = "ManterManutencao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//cria o componente IMontaClassificacao
$obIMontaClassificacao = new IMontaClassificacao( $obForm );
$obIMontaClassificacao->setNull( true );

//instancia um textbox para a descrição do bem
$obTxtDescricaoBem = new TextBox();
$obTxtDescricaoBem->setRotulo( 'Descrição' );
$obTxtDescricaoBem->setTitle( 'Informe a descrição do bem.' );
$obTxtDescricaoBem->setName( 'stNomBem' );
$obTxtDescricaoBem->setMaxLength( 60 );
$obTxtDescricaoBem->setSize( 60 );
$obTxtDescricaoBem->setNull( true );

$obTipoBuscaDescricaoBem = new TipoBusca( $obTxtDescricaoBem );

//instancia componente TextBox para o codigo do bem
$obInCodBem = new Inteiro();
$obInCodBem->setRotulo( 'Código do Bem' );
$obInCodBem->setTitle( 'Informe o código do bem.' );
$obInCodBem->setName( 'inCodBem' );

//cria o componente radio para a placa de identificação
$obRdPlacaIdentificacaoSim = new Radio();
$obRdPlacaIdentificacaoSim->setRotulo( 'Placa de Identificação' );
$obRdPlacaIdentificacaoSim->setTitle( 'Informe se o bem possui placa de identificação;' );
$obRdPlacaIdentificacaoSim->setName( 'stPlacaIdentificacao' );
$obRdPlacaIdentificacaoSim->setValue( 'sim' );
$obRdPlacaIdentificacaoSim->setLabel( 'Sim' );
$obRdPlacaIdentificacaoSim->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacaoFiltro', 'stPlacaIdentificacao' );" );

$obRdPlacaIdentificacaoNao = new Radio();
$obRdPlacaIdentificacaoNao->setRotulo( 'Placa de Identificação' );
$obRdPlacaIdentificacaoNao->setTitle( 'Informe se o bem possui placa de identificação;' );
$obRdPlacaIdentificacaoNao->setName( 'stPlacaIdentificacao' );
$obRdPlacaIdentificacaoNao->setValue( 'nao' );
$obRdPlacaIdentificacaoNao->setLabel( 'Não' );
$obRdPlacaIdentificacaoNao->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacaoFiltro', 'stPlacaIdentificacao' );" );

$obRdPlacaIdentificacaoTodos = new Radio();
$obRdPlacaIdentificacaoTodos->setRotulo( 'Placa de Identificação' );
$obRdPlacaIdentificacaoTodos->setTitle( 'Informe se o bem possui placa de identificação.' );
$obRdPlacaIdentificacaoTodos->setName( 'stPlacaIdentificacao' );
$obRdPlacaIdentificacaoTodos->setValue( 'todos' );
$obRdPlacaIdentificacaoTodos->setLabel( 'Todos' );
$obRdPlacaIdentificacaoTodos->setChecked( true );
$obRdPlacaIdentificacaoTodos->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacaoFiltro', 'stPlacaIdentificacao' );" );

//cria span para o número da placa do bem
$obSpnNumeroPlaca = new Span();
$obSpnNumeroPlaca->setId( 'spnNumeroPlaca' );

//instancia componente  Select para a ordenacao
$obSlOrdenacao = new Select();
$obSlOrdenacao->setRotulo( 'Ordernar' );
$obSlOrdenacao->setTitle( 'Seleciona o tipo de ordenação dos bens.' );
$obSlOrdenacao->setName( 'stOrdenacao' );
$obSlOrdenacao->addOption( 'codigo','Código' );
$obSlOrdenacao->addOption( 'descricao','Descrição' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-03.01.07');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obIMontaClassificacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obTipoBuscaDescricaoBem );
$obFormulario->addComponente( $obInCodBem );
$obFormulario->agrupaComponentes( array( $obRdPlacaIdentificacaoSim, $obRdPlacaIdentificacaoNao, $obRdPlacaIdentificacaoTodos ) );
$obFormulario->addSpan 		( $obSpnNumeroPlaca );
$obFormulario->addComponente( $obSlOrdenacao );

$obFormulario->OK();
$obFormulario->show();

//monta o text da placa de identificação por padrão
$jsOnLoad = "montaParametrosGET( 'montaPlacaIdentificacaoFiltro', 'stPlacaIdentificacao' );";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

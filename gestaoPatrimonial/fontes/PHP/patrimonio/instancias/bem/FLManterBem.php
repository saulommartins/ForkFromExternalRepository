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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FLManterBem.php 64184 2015-12-11 14:09:44Z arthur $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php" );
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php" );
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php");
include_once( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php");
include_once( CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php");
include_once( CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";
include_once(CAM_GA_PROT_COMPONENTES.'IPopUpProcesso.class.php');
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");

$stPrograma = "ManterBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

include( $pgJs );

$stAcao = $request->get('stAcao');

Sessao::remove('rsAtributosDinamicos','');

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

//instancia o componente IMontaClassificacao
$obIMontaClassificacao = new IMontaClassificacao( $obForm );
$obIMontaClassificacao->obTxtCodClassificacao->setValue( $stClassificacao );
$obIMontaClassificacao->obTxtCodClassificacao->obEvento->setOnChange( $obIMontaClassificacao->obTxtCodClassificacao->obEvento->getOnChange().";montaParametrosGET( 'montaAtributos', 'stCodClassificacao' );" );
$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->obEvento->setOnChange( $obIMontaClassificacao->obISelectEspecie->obSelectEspecie->obEvento->getOnChange().";montaParametrosGET( 'montaAtributos', 'inCodNatureza,inCodGrupo,inCodEspecie' );"  );
$obIMontaClassificacao->setNull( true );

//cria span para o número da placa do bem
$obSpnAtributos = new Span();
$obSpnAtributos->setId( 'spnAtributos' );

//instancia um textbox para o codigo do bem
$obTxtCodBem = new TextBox();
$obTxtCodBem->setRotulo( 'Código do Bem' );
$obTxtCodBem->setTitle( 'Informe o código do bem.' );
$obTxtCodBem->setName( 'inCodBem' );
$obTxtCodBem->setNull( true );
$obTxtCodBem->setInteiro( true );

//instancia um textbox para a descrição do bem
$obTxtDescricaoBem = new TextBox();
$obTxtDescricaoBem->setRotulo( 'Descrição' );
$obTxtDescricaoBem->setTitle( 'Informe a descrição do bem ' );
$obTxtDescricaoBem->setName( 'stDescricaoBem' );
$obTxtDescricaoBem->setSize( 60 );
$obTxtDescricaoBem->setMaxLength( 60 );
$obTxtDescricaoBem->setNull( true );

//instancia a informação do processo que deu origem a aquisição do bem
$obPopUpProcesso = new IPopUpProcesso($obForm);
$obPopUpProcesso->setRotulo("Processo Administrativo");
$obPopUpProcesso->setValidar(true);
$obPopUpProcesso->setNull(true);

//instancia um tipobusca
$obTipoBuscaDescricaoBem = new TipoBusca( $obTxtDescricaoBem );

//instancia um text para o detalhamento do bem
$obTxtDetalhamentoBem = new TextBox();
$obTxtDetalhamentoBem->setRotulo( 'Detalhamento' );
$obTxtDetalhamentoBem->setTitle( 'Informe o detalhamento do bem ' );
$obTxtDetalhamentoBem->setName( 'stDetalhamentoBem' );
$obTxtDetalhamentoBem->setMaxLength( '' );
$obTxtDetalhamentoBem->setSize( 60 );
$obTxtDetalhamentoBem->setNull( true );

//instancia o componente de busca da marca do bem
$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setNull(true);
$obBscMarca->setRotulo("Marca");
$obBscMarca->setTitle("Informe a marca do item.");

//instancia um tipobusca
$obTipoBuscaDetalhamentoBem = new TipoBusca( $obTxtDetalhamentoBem );

//instancia o componente IPopUpFornecedor
$obIPopUpFornecedor = new IPopUpFornecedor($obForm);
$obIPopUpFornecedor->setId ( "stNomFornecedor" );
$obIPopUpFornecedor->setTitle( "Selecione o Fornecedor." );
$obIPopUpFornecedor->setNull( true );

//instancia um componente Moeda par ao valor do bem
$obInValorBem = new Moeda();
$obInValorBem->setRotulo( 'Valor do Bem' );
$obInValorBem->setTitle( 'Informe o valor do bem.' );
$obInValorBem->setName( 'inValorBem' );
$obInValorBem->setValue( '' );
$obInValorBem->setNull( true );

//instancia um componente periodicidade
$obInValorDepreciacao = new Moeda();
$obInValorDepreciacao->setRotulo( 'Valor da Depreciação Inicial' );
$obInValorDepreciacao->setTitle( 'Informe o valor da depreciação.' );
$obInValorDepreciacao->setName( 'inValorDepreciacao' );
$obInValorDepreciacao->setValue( '' );
$obInValorDepreciacao->setNull( true );

//instancia um componente periodicidade
$obPeriodicidadeDepreciacao = new Periodicidade();
$obPeriodicidadeDepreciacao->setIdComponente( 'Depreciacao' );
$obPeriodicidadeDepreciacao->setRotulo( 'Data da Depreciação' );
$obPeriodicidadeDepreciacao->setTitle( 'Selecione o período da depreciação.' );
$obPeriodicidadeDepreciacao->setNull( true );
$obPeriodicidadeDepreciacao->setExercicio ( Sessao::getExercicio() );

//instancia um componente periodicidade
$obPeriodicidadeAquisicao = new Periodicidade();
$obPeriodicidadeAquisicao->setIdComponente( 'Aquisicao' );
$obPeriodicidadeAquisicao->setRotulo( 'Data da Aquisição' );
$obPeriodicidadeAquisicao->setTitle( 'Selecione o período da aquisição.' );
$obPeriodicidadeAquisicao->setNull( true );
$obPeriodicidadeAquisicao->setExercicio ( Sessao::getExercicio() );

//instancia um componente periodicidade para a data de incorporação
$obPeriodicidadeIncorporacao = new Periodicidade();
$obPeriodicidadeIncorporacao->setIdComponente( 'Incorporacao' );
$obPeriodicidadeIncorporacao->setRotulo( 'Data de Incorporação' );
$obPeriodicidadeIncorporacao->setTitle( 'Selecione o período da liquidação contábil.' );
$obPeriodicidadeIncorporacao->setNull( true );
$obPeriodicidadeIncorporacao->setExercicio ( Sessao::getExercicio() );

//instancia um compenente data para a data de vencimento da garantia
$obPeriodicidadeVencimento = new Periodicidade();
$obPeriodicidadeVencimento->setIdComponente( 'Vencimento' );
$obPeriodicidadeVencimento->setRotulo( 'Vencimento da Garantia' );
$obPeriodicidadeVencimento->setTitle( 'Selecione o período do vencimento da garantia.' );
$obPeriodicidadeVencimento->setNull( true );
$obPeriodicidadeVencimento->setExercicio ( Sessao::getExercicio() );

//cria o componente radio para a placa de identificação
$obRdPlacaIdentificacaoSim = new Radio();
$obRdPlacaIdentificacaoSim->setRotulo( 'Placa de Identificação' );
$obRdPlacaIdentificacaoSim->setTitle( 'Informe se o item possui placa de identificação.' );
$obRdPlacaIdentificacaoSim->setName( 'stPlacaIdentificacao' );
$obRdPlacaIdentificacaoSim->setValue( 'sim' );
$obRdPlacaIdentificacaoSim->setLabel( 'Sim' );
$obRdPlacaIdentificacaoSim->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao,stAcao' );" );

$obRdPlacaIdentificacaoNao = new Radio();
$obRdPlacaIdentificacaoNao->setRotulo( 'Placa de Identificação' );
$obRdPlacaIdentificacaoNao->setTitle( 'Informe se o item possui placa de identificação.' );
$obRdPlacaIdentificacaoNao->setName( 'stPlacaIdentificacao' );
$obRdPlacaIdentificacaoNao->setValue( 'nao' );
$obRdPlacaIdentificacaoNao->setLabel( 'Não' );
$obRdPlacaIdentificacaoNao->obEvento->setOnClick( "montaParametrosGET( 'montaPlacaIdentificacao', 'stPlacaIdentificacao,stAcao' );" );

//cria span para o número da placa do bem
$obSpnNumeroPlaca = new Span();
$obSpnNumeroPlaca->setId( 'spnNumeroPlaca' );

//instancia o componenete ITextBoxSelectEntidadeGeral
$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull( true );

//instancia componente TextBox para o ano do empenho
$obExercicioEmpenho = new Inteiro();
$obExercicioEmpenho->setRotulo( 'Exercício' );
$obExercicioEmpenho->setTitle( 'Informe o exercício do empenho.' );
$obExercicioEmpenho->setMaxLength( 4 );
$obExercicioEmpenho->setSize( 4 );
$obExercicioEmpenho->setNull( true );

//instancia o componente Inteiro para o empenho
$obNumEmpenho = new Inteiro();
$obNumEmpenho->setRotulo( 'Número do Empenho' );
$obNumEmpenho->setTitle( 'Informe o número do empenho do bem.' );
$obNumEmpenho->setName( 'inNumEmpenho' );
$obNumEmpenho->setNull( true );

//instancia o componente Inteiro para a nota fiscal
$obNumNotaFiscal = new Inteiro();
$obNumNotaFiscal->setRotulo( 'Número da Nota Fiscal' );
$obNumNotaFiscal->setTitle( 'Informe o número da nota fiscal do bem.' );
$obNumNotaFiscal->setName( 'stNumNotaFiscal' );
$obNumNotaFiscal->setNull( true );

//instancia o componente IPopUpCGMVinculado para o responsavel
$obIPopUpCGMVinculadoResponsavel = new IPopUpCGMVinculado( $obForm );
$obIPopUpCGMVinculadoResponsavel->setTabelaVinculo    ( 'sw_cgm_pessoa_fisica'   );
$obIPopUpCGMVinculadoResponsavel->setCampoVinculo     ( 'numcgm'                 );
$obIPopUpCGMVinculadoResponsavel->setNomeVinculo      ( 'Responsavel'            );
$obIPopUpCGMVinculadoResponsavel->setRotulo           ( 'Responsável'            );
$obIPopUpCGMVinculadoResponsavel->setTitle            ( 'Informe o responsável pelo bem.' );
$obIPopUpCGMVinculadoResponsavel->setName             ( 'stNomResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->setId               ( 'stNomResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->obCampoCod->setName ( 'inNumResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->obCampoCod->setId   ( 'inNumResponsavel'       );
$obIPopUpCGMVinculadoResponsavel->setNull             ( true                     );

//instancia o componente data para o responsavel
$obPeriodoResponsavel = new Periodicidade();
$obPeriodoResponsavel->setIdComponente( 'Responsavel' );
$obPeriodoResponsavel->setRotulo( 'Data de Início' );
$obPeriodoResponsavel->setTitle( 'Selecione o período de início do responsável pelo bem.' );
$obPeriodoResponsavel->setNull( true );
$obPeriodoResponsavel->setExercicio ( Sessao::getExercicio() );

//instancia o componenete IMontaOrganograma
$obIMontaOrganograma = new IMontaOrganograma(true);
$obIMontaOrganograma->setCodOrgao($codOrgao);
//$obIMontaOrganograma->setNivelObrigatorio(0);

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);

//instancio o componente TextBoxSelect para a situacao do bem
$obITextBoxSelectSituacao = new TextBoxSelect();
$obITextBoxSelectSituacao->setRotulo( 'Situação' );
$obITextBoxSelectSituacao->setTitle( 'Informe a situação do bem.' );
$obITextBoxSelectSituacao->setName( 'inCodTxtSituacao' );
$obITextBoxSelectSituacao->setNull( true );

$obITextBoxSelectSituacao->obTextBox->setName                ( "inCodTxtSituacao"     );
$obITextBoxSelectSituacao->obTextBox->setId                  ( "inCodTxtSituacao"     );
$obITextBoxSelectSituacao->obTextBox->setSize                ( 6                      );
$obITextBoxSelectSituacao->obTextBox->setMaxLength           ( 3                      );
$obITextBoxSelectSituacao->obTextBox->setInteiro             ( true                   );

$obITextBoxSelectSituacao->obSelect->setName                ( "inCodSituacao"                 );
$obITextBoxSelectSituacao->obSelect->setId                  ( "inCodSituacao"                 );
$obITextBoxSelectSituacao->obSelect->setStyle               ( "width: 200px"                  );
$obITextBoxSelectSituacao->obSelect->setCampoID             ( "cod_situacao"                  );
$obITextBoxSelectSituacao->obSelect->setCampoDesc           ( "nom_situacao"                  );
$obITextBoxSelectSituacao->obSelect->addOption              ( "", "Selecione"                 );

//recupero todos os registros da table patrimonio.situacao_bem e preencho o componenete ITextBoxSelect
$obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();
$obTPatrimonioSituacaoBem->recuperaTodos( $rsSituacaoBem );

$obITextBoxSelectSituacao->obSelect->preencheCombo( $rsSituacaoBem );

//instancia um componente TextBox para a descricao da situacao
$obTxtDescricaoSituacao = new TextBox();
$obTxtDescricaoSituacao->setRotulo   ( 'Descrição da Situação' );
$obTxtDescricaoSituacao->setTitle    ( 'Informe a descrição da situação do bem ' );
$obTxtDescricaoSituacao->setName     ( 'stDescricaoSituacao' );
$obTxtDescricaoSituacao->setNull  	 ( true );
$obTxtDescricaoSituacao->setMaxLength( 100 );
$obTxtDescricaoSituacao->setSize( 70 );

//instancia um tipobusca
$obTipoBuscaDescricaoSituacao = new TipoBusca( $obTxtDescricaoSituacao );

//instancia radio para o bem baixado
$obRdBemBaixadoSim = new Radio();
$obRdBemBaixadoSim->setRotulo( 'Bem Baixado' );
$obRdBemBaixadoSim->setTitle( 'Informe se o item está baixado.' );
$obRdBemBaixadoSim->setName( 'stBemBaixado' );
$obRdBemBaixadoSim->setValue( 'sim' );
$obRdBemBaixadoSim->setLabel( 'Sim' );

$obRdBemBaixadoNao = new Radio();
$obRdBemBaixadoNao->setRotulo( 'Bem Baixado' );
$obRdBemBaixadoNao->setTitle( 'Informe se o item está baixado.' );
$obRdBemBaixadoNao->setName( 'stBemBaixado' );
$obRdBemBaixadoNao->setValue( 'nao' );
$obRdBemBaixadoNao->setLabel( 'Não' );

$obRdBemBaixadoTodos = new Radio();
$obRdBemBaixadoTodos->setRotulo( 'Bem Baixado' );
$obRdBemBaixadoTodos->setTitle( 'Informe se o item está baixado.' );
$obRdBemBaixadoTodos->setName( 'stBemBaixado' );
$obRdBemBaixadoTodos->setValue( 'todos' );
$obRdBemBaixadoTodos->setLabel( 'Todos' );
$obRdBemBaixadoTodos->setChecked( true );

$obSelectOrdenacao = new Select();
$obSelectOrdenacao->setRotulo( 'Ordenação' );
$obSelectOrdenacao->setTitle( 'Selecione a ordenação.' );
$obSelectOrdenacao->setName( 'stOrdenacao' );
$obSelectOrdenacao->addOption( '', 'Selecione' );
$obSelectOrdenacao->addOption( 'cod_bem', 'Código do Bem' );
$obSelectOrdenacao->addOption( 'descricao', 'Descrição do Bem' );
$obSelectOrdenacao->addOption( 'classificacao', 'Classificação' );
$obSelectOrdenacao->addOption( 'placa', 'Número da Placa' );

//adicionado filtro por atributos
$obAtributosDinamicos = new RecordSet();
$stOrder = ' ORDER BY nom_atributo ';
require_once(CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php");
$obTPatrimonioEspecieAtributo = new TPatrimonioEspecieAtributo();
$obTPatrimonioEspecieAtributo->recuperaInfoAtributos($obAtributosDinamicos, '', $stOrder);

$obSelectAtributo = new Select;
$obSelectAtributo->setRotulo( "Atributo Dinâmico" );
$obSelectAtributo->setTitle( "Selecione o Atributo." );
$obSelectAtributo->setName( "stAtributo" );
$obSelectAtributo->setId( "stAtributo" );
$obSelectAtributo->setValue( "" );
$obSelectAtributo->setStyle( "width: 200px" );
$obSelectAtributo->addOption( "", "Selecione" );
$obSelectAtributo->setNull( true );
//$obSelectAtributo->obEvento->setOnChange("montaParametrosGET( 'montaListaAtributos', 'stAtributo' );");

while (!$obAtributosDinamicos->eof()) {
    $obSelectAtributo->addOption( $obAtributosDinamicos->getCampo('cod_modulo').','.$obAtributosDinamicos->getCampo('cod_cadastro').','.$obAtributosDinamicos->getCampo('cod_atributo'), $obAtributosDinamicos->getCampo('nom_atributo') );
    $obAtributosDinamicos->proximo();
}

// Define Objeto Button para Incluir Item
$obBtnIncluirAtributos = new Button;
$obBtnIncluirAtributos->setValue( "Incluir" );
$obBtnIncluirAtributos->setId("incluiItem");
$obBtnIncluirAtributos->setValue("incluir");
$obBtnIncluirAtributos->obEvento->setOnClick( "montaParametrosGET( 'montaListaAtributos', 'stAtributo' );");

// Define Objeto Button para Limpar Item
$obBtnLimparAtributos = new Button;
$obBtnLimparAtributos->setValue( "Limpar" );
$obBtnLimparAtributos->obEvento->setOnClick("limpaAtributoDinamico();");

$obSpanListaAtributos = new Span();
$obSpanListaAtributos->setId( "spnListaAtributos" );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Bem' );
$obFormulario->addComponente( $obTxtCodBem );

$obFormulario->addTitulo    ( 'Classificação' );

$obIMontaClassificacao->geraFormulario( $obFormulario );
$obFormulario->addSpan      ( $obSpnAtributos );

$obFormulario->addTitulo    ( 'Informações Básicas' );
$obFormulario->addComponente( $obTipoBuscaDescricaoBem );
$obFormulario->addComponente( $obPopUpProcesso );
$obFormulario->addComponente( $obTipoBuscaDetalhamentoBem );
$obFormulario->addComponente( $obBscMarca );
$obFormulario->addComponente( $obIPopUpFornecedor );
$obFormulario->addComponente( $obInValorBem );
$obFormulario->addComponente( $obInValorDepreciacao );
$obFormulario->addComponente( $obPeriodicidadeDepreciacao );
$obFormulario->addComponente( $obPeriodicidadeAquisicao );
$obFormulario->addComponente( $obPeriodicidadeVencimento );

$obFormulario->agrupaComponentes( array( $obRdPlacaIdentificacaoSim, $obRdPlacaIdentificacaoNao ) );
$obFormulario->addSpan 		( $obSpnNumeroPlaca );

$obFormulario->addTitulo    ( 'Informações Financeiras' );
$obFormulario->addComponente( $obExercicioEmpenho );
$obFormulario->addComponente( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente( $obPeriodicidadeIncorporacao );
$obFormulario->addComponente( $obNumEmpenho );
$obFormulario->addComponente( $obNumNotaFiscal );

$obFormulario->addTitulo	( 'Responsável' );
$obFormulario->addComponente( $obIPopUpCGMVinculadoResponsavel );
$obFormulario->addComponente( $obPeriodoResponsavel );

$obFormulario->addTitulo  	( 'Histórico' );
$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->addComponente( $obITextBoxSelectSituacao );
$obFormulario->addComponente( $obTipoBuscaDescricaoSituacao );

$obFormulario->addTitulo    ( 'Baixa' );
$obFormulario->agrupaComponentes( array( $obRdBemBaixadoSim, $obRdBemBaixadoNao, $obRdBemBaixadoTodos ) );

$obFormulario->addTitulo 	( 'Ordenação' );
$obFormulario->addComponente( $obSelectOrdenacao );

$obFormulario->addTitulo 	( 'Atributo' );
$obFormulario->addComponente( $obSelectAtributo );
$obFormulario->agrupaComponentes( array( $obBtnIncluirAtributos, $obBtnLimparAtributos) );
$obFormulario->addSpan( $obSpanListaAtributos );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

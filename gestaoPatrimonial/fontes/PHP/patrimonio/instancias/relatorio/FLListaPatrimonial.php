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
    * Página de Filtro para emissão do relatório de Lista Patrimonial
    * Data de criação : 24/10/2005

    * @author Analista:
    * @author Programador: Lucas Teixeira Stephanou

    Caso de uso: uc-03.01.21

    $Id: FLListaPatrimonial.php 59612 2014-09-02 12:00:51Z gelson $

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

include_once CAM_GP_PAT_NEGOCIO."RPatrimonioBem.class.php";
include_once CAM_GP_PAT_COMPONENTES."ISelectEspecie.class.php";

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";

include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ListaPatrimonial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgGera = "OCGera".$stPrograma.".php";

include_once($pgJS);

$obISelectEspecie = new ISelectEspecie($obForm);
$obISelectEspecie->obSelectEspecie->setNull(true);
$obISelectEspecie->obISelectGrupo->obSelectGrupo->setNull(true);

$bemPatrimonio = new RPatrimonioBem;
$bemPatrimonio->listarMax($maxBem);

$bemPatrimonio->listarTodasSituacoes($rsSituacoes);

$codMax = $maxBem->getCampo("max");

$obForm = new Form;
$obForm->setAction($pgGera);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue( " " );

// Define Objeto TextBox para intervalo código bem inicial
$obTxtBemInicial = new TextBox;
$obTxtBemInicial->setRotulo  ( "Intervalo entre Códigos de Bens");
$obTxtBemInicial->setTitle   ( "Informe o intervalo de código do bem." );
$obTxtBemInicial->setName    ( "inCodBemInicial"                 );
$obTxtBemInicial->setValue   ( 1                                 );
$obTxtBemInicial->setNull    ( false                              );
$obTxtBemInicial->setInteiro ( true                              );

//define label do intervalo
$obLblIntervalo = new label;
$obLblIntervalo->setValue ("até");

// Define Objeto TextBox para intervalo código bem final
$obTxtBemFinal = new TextBox;
$obTxtBemFinal->setRotulo  ( "Intervalo entre Códigos de Bens"           );
$obTxtBemFinal->setTitle   ( "Informe o intervalo de código do bem." );
$obTxtBemFinal->setName    ( "inCodBemFinal"                      );
$obTxtBemFinal->setValue   ( "$codMax"                              );
$obTxtBemFinal->setNull    ( false                                  );
$obTxtBemFinal->setInteiro ( true                                   );

// Define Objeto TextBox para intervalo código bem inicial
$obTxtPlacaInicial = new TextBox;
$obTxtPlacaInicial->setRotulo  ( "Intervalo entre Placa");
$obTxtPlacaInicial->setTitle   ( "Informe o intervalo de placas." );
$obTxtPlacaInicial->setName    ( "stNumPlacaInicial"                 );
$obTxtPlacaInicial->setNull    ( true                               );
$obTxtPlacaInicial->setMaxLength ( 20 );

// Define Objeto TextBox para intervalo código bem final
$obTxtPlacaFinal = new TextBox;
$obTxtPlacaFinal->setRotulo  ( " Intervalo entre Placa");
$obTxtPlacaFinal->setTitle   ( " Informe o intervalo de placas." );
$obTxtPlacaFinal->setName    ( "stNumPlacaFinal"                      );
$obTxtPlacaFinal->setNull    ( true                                   );
$obTxtPlacaFinal->setMaxLength ( 20 );

//instancia um textbox para a descrição do bem
$obTxtDescricaoBem = new TextBox;
$obTxtDescricaoBem->setRotulo( 'Descrição' );
$obTxtDescricaoBem->setTitle( 'Informe a descrição do bem ' );
$obTxtDescricaoBem->setName( 'stDescricaoBem' );
$obTxtDescricaoBem->setSize( 60 );
$obTxtDescricaoBem->setMaxLength( 60 );
$obTxtDescricaoBem->setNull( true );

//instancia um tipobusca
$obTipoBuscaDescricaoBem = new TipoBusca( $obTxtDescricaoBem );

//instancia o componenete ITextBoxSelectEntidadeGeral
$obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidadeGeral->setNull( true );

$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setRotulo("Fornecedor");
$obIPopUpCGM->setTitle ("Informe o fornecedor.");
$obIPopUpCGM->setTipo("todos");
$obIPopUpCGM->setNull(true);

$obDtDataAquisicao = new Periodicidade;
$obDtDataAquisicao->setIdComponente ('dtAquisicao');
$obDtDataAquisicao->setName         ('dtAquisicao');
$obDtDataAquisicao->setExercicio    ( Sessao::getExercicio() );
$obDtDataAquisicao->setRotulo       ('Data de Aquisição');

$obDtDataIncorporacao = new Periodicidade;
$obDtDataIncorporacao->setIdComponente ('dtIncorporacao');
$obDtDataIncorporacao->setName         ('dtIncorporacao');
$obDtDataIncorporacao->setExercicio    ( Sessao::getExercicio() );
$obDtDataIncorporacao->setRotulo       ('Data de Incorporação');

# Instancia do componenete IMontaOrganograma
$obIMontaOrganograma = new IMontaOrganograma(true);
$obIMontaOrganograma->setCodOrgao($codOrgao);
$obIMontaOrganograma->setCadastroOrganograma(false);

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setValue($codLocal);

$obCmbSituacao = new Select();
$obCmbSituacao->setName     ('inCodSituacao');
$obCmbSituacao->setRotulo   ( "Situação do Bem" );
$obCmbSituacao->setTitle    ( "Selecione o filtro para a situação do bem." );
$obCmbSituacao->addOption   ( "", "Selecione"                    );
$obCmbSituacao->setCampoId  ("cod_situacao");
$obCmbSituacao->setCampoDesc("nom_situacao");
$obCmbSituacao->preencheCombo($rsSituacoes);

$obChkLocal = new Checkbox;
$obChkLocal->setName    ( 'demo_cod_local' );
$obChkLocal->setId      ( 'demo_cod_local' );
$obChkLocal->setRotulo  ( 'Local'       );
$obChkLocal->setChecked ( true          );
$obChkLocal->setValue   (1);

$obChkCodBem = new Checkbox;
$obChkCodBem->setName    ( 'demo_cod_bem'   );
$obChkCodBem->setId      ( 'demo_cod_bem'   );
$obChkCodBem->setRotulo  ( 'Código do Bem'       );
$obChkCodBem->setChecked ( true          );
$obChkCodBem->setValue   (1);

$obChkDescBem = new Checkbox;
$obChkDescBem->setName    ( 'demo_descricao'   );
$obChkDescBem->setId      ( 'demo_descricao'   );
$obChkDescBem->setRotulo  ( 'Descrição do Bem' );
$obChkDescBem->setChecked ( true          );
$obChkDescBem->setValue   (1);

$obChkDetalhamento = new Checkbox;
$obChkDetalhamento->setName    ( 'demo_detalhamento'   );
$obChkDetalhamento->setId      ( 'demo_detalhamento'   );
$obChkDetalhamento->setRotulo  ( 'Detalhamento'       );
$obChkDetalhamento->setChecked ( true          );
$obChkDetalhamento->setValue   (1);

$obChkDataAquisicao = new Checkbox;
$obChkDataAquisicao->setName    ( 'demo_dt_aquisicao'   );
$obChkDataAquisicao->setId      ( 'demo_dt_aquisicao'   );
$obChkDataAquisicao->setRotulo  ( 'Data de Aquisição'       );
$obChkDataAquisicao->setChecked ( true          );
$obChkDataAquisicao->setValue   (1);

$obChkDataIncorporacao = new Checkbox;
$obChkDataIncorporacao->setName    ( 'demo_dt_incorporacao' );
$obChkDataIncorporacao->setId      ( 'demo_dt_incorporacao' );
$obChkDataIncorporacao->setRotulo  ( 'Data de Incorporação' );
$obChkDataIncorporacao->setChecked ( true                   );
$obChkDataIncorporacao->setValue   (1);

$obChkPlaca = new Checkbox;
$obChkPlaca->setName    ( 'demo_placa'   );
$obChkPlaca->setId      ( 'demo_placa'   );
$obChkPlaca->setRotulo  ( 'Placa'       );
$obChkPlaca->setChecked ( true          );
$obChkPlaca->setValue   (1);

$obChkValor = new Checkbox;
$obChkValor->setName    ( 'demo_valor'   );
$obChkValor->setId      ( 'demo_valor'   );
$obChkValor->setRotulo  ( 'Valor'       );
$obChkValor->setChecked ( true          );
$obChkValor->setValue   (1);

$obChkNotaFiscal = new Checkbox;
$obChkNotaFiscal->setName    ( 'demo_nota_fiscal'   );
$obChkNotaFiscal->setId      ( 'demo_nota_fiscal'   );
$obChkNotaFiscal->setRotulo  ( 'Nota Fiscal'       );
$obChkNotaFiscal->setChecked ( true          );
$obChkNotaFiscal->setValue   (1);

$obChkFornecedor = new Checkbox;
$obChkFornecedor->setName    ( 'demo_nom_cgm'   );
$obChkFornecedor->setId      ( 'demo_nom_cgm'   );
$obChkFornecedor->setRotulo  ( 'Fornecedor'    );
$obChkFornecedor->setChecked ( true          );
$obChkFornecedor->setValue   (1);

// SELECT MULTIPLO  CAMPOS A DEMONSTRAR
$arCamposSelecionados = array (
    array("nom_campo" => "historico_bem.cod_bem"    , "descricao" => "Código do Bem"),
    array("nom_campo" => "bem.descricao"            , "descricao" => "Descrição do Bem"),
    array("nom_campo" => "bem.dt_aquisicao"         , "descricao" => "Data de Aquisição"),
    array("nom_campo" => "bem.dt_incorporacao"      , "descricao" => "Data de Incorporação"),
    array("nom_campo" => "bem.num_placa"            , "descricao" => "Placa"),
    array("nom_campo" => "bem.vl_bem"               , "descricao" => "Valor"),
    array("nom_campo" => "bem_comprado.nota_fiscal" , "descricao" => "Nota Fiscal"),
    array("nom_campo" => "sw_cgm.nom_cgm"           , "descricao" => "Fornecedor")
);

$arCamposDisponiveis = array (
    array("nom_campo" => "local.descricao" , "descricao" => "Local"));

$rsCamposDisponiveis = new RecordSet;
$rsCamposDisponiveis->preenche( $arCamposSelecionados );

$rsCamposSelecionados = new Recordset;
$rsCamposSelecionados->preenche( $arCamposDisponiveis );

$obCmbOrdenarCampos = new SelectMultiplo;
$obCmbOrdenarCampos->setOrdenacao('value');
$obCmbOrdenarCampos->setTitle    ( "Selecione os campos que você deseja que <br>ordenem a Lista, respeitando a precendencia. " );
$obCmbOrdenarCampos->setName     ( "inCamposSelecionados" );
$obCmbOrdenarCampos->setRotulo   ( "Ordenação" );
$obCmbOrdenarCampos->setNull     ( true );

$obCmbOrdenarCampos->SetNomeLista1 ( "inCamposDisponiveis" );
$obCmbOrdenarCampos->setCampoId1   ( "nom_campo" );
$obCmbOrdenarCampos->setCampoDesc1 ( "descricao" );
$obCmbOrdenarCampos->SetRecord1    ( $rsCamposDisponiveis    );

$obCmbOrdenarCampos->SetNomeLista2 ( "inCamposSelecionados" );
$obCmbOrdenarCampos->setCampoId2   ( "nom_campo" );
$obCmbOrdenarCampos->setCampoDesc2 ( "descricao" );
$obCmbOrdenarCampos->SetRecord2    ( $rsCamposSelecionados    );

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

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                          );
$obFormulario->setAjuda         ("UC-03.01.21");
$obFormulario->addHidden        ( $obHdnCtrl                       );
$obFormulario->addTitulo        ( "Insira os Dados para Procura"   );
$obISelectEspecie->geraFormulario( $obFormulario );
$obFormulario->agrupaComponentes( array( $obTxtBemInicial ,$obLblIntervalo, $obTxtBemFinal));
$obFormulario->agrupaComponentes( array( $obTxtPlacaInicial ,$obLblIntervalo, $obTxtPlacaFinal));

$obFormulario->addComponente	( $obTipoBuscaDescricaoBem);
$obFormulario->addComponente    ( $obITextBoxSelectEntidadeGeral );
$obFormulario->addComponente    ( $obIPopUpCGM);
$obFormulario->addComponente    ( $obDtDataAquisicao);

$obFormulario->addComponente    ( $obDtDataIncorporacao );

$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->addComponente    ( $obCmbSituacao);
$obFormulario->addTitulo        ( "Demonstrar Campos"   );
$obFormulario->addComponente    ( $obChkLocal   );
$obFormulario->addComponente    ( $obChkCodBem          );
$obFormulario->addComponente    ( $obChkDescBem         );
$obFormulario->addComponente    ( $obChkDataAquisicao   );
$obFormulario->addComponente    ( $obChkDataIncorporacao );
$obFormulario->addComponente    ( $obChkPlaca           );
$obFormulario->addComponente    ( $obChkValor           );
$obFormulario->addComponente    ( $obChkNotaFiscal      );
$obFormulario->addComponente    ( $obChkFornecedor      );
$obFormulario->addTitulo        ( "Ordenação"   );
$obFormulario->addComponente    ( $obCmbOrdenarCampos );

$obFormulario->addTitulo 	( 'Atributo' );
$obFormulario->addComponente( $obSelectAtributo );
$obFormulario->agrupaComponentes( array( $obBtnIncluirAtributos, $obBtnLimparAtributos) );
$obFormulario->addSpan( $obSpanListaAtributos );

$obFormulario->OK();
$obFormulario->show();

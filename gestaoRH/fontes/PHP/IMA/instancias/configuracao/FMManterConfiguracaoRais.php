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
    * Arquivo de Formulário
    * Data de Criação: 25/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: FMManterConfiguracaoRais.php 62188 2015-04-06 18:21:28Z carlos.silva $

    * Casos de uso: uc-04.08.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoRais.class.php"                                   );

$stPrograma = "ManterConfiguracaoRais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $_REQUEST["stAcao"]                                                   );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$rsConfiguracaoRais = new RecordSet();
$inTipoInscricao = 2;
if ($_REQUEST["stAcao"] == "alterar") {
    $obTIMAConfiguracaoRais = new TIMAConfiguracaoRais();
    $obTIMAConfiguracaoRais->setDado("exercicio",$_GET["inExercicio"]);
    $obTIMAConfiguracaoRais->recuperaPorChave($rsConfiguracaoRais);
    if ($rsConfiguracaoRais->getNumLinhas() == 1) {
        $inTipoInscricao    = $rsConfiguracaoRais->getCampo("tipo_inscricao");
        $stTelefone         = substr($rsConfiguracaoRais->getCampo("telefone"),0,2)."-".substr($rsConfiguracaoRais->getCampo("telefone"),2,strlen($rsConfiguracaoRais->getCampo("telefone")));
        $stEmail            = $rsConfiguracaoRais->getCampo("email");
        $stNatureza         = $rsConfiguracaoRais->getCampo("natureza_juridica");
        $inCodMunicipio     = $rsConfiguracaoRais->getCampo("cod_municipio");
        $inDataBase         = $rsConfiguracaoRais->getCampo("dt_base_categoria");
        $boCNPJ             = ($rsConfiguracaoRais->getCampo("cei_vinculado") == "t") ? "true" : "false";
        $inCei              = $rsConfiguracaoRais->getCampo("numero_cei");
        $inPrefixo          = $rsConfiguracaoRais->getCampo("prefixo");
        $inExercicio        = $rsConfiguracaoRais->getCampo("exercicio");
        $jsOnload = "executaFuncaoAjax('preencherFormAlterar');";
    } else {
        $inExercicio = "";
    }
}

Sessao::write("obForm"         , $obForm);
Sessao::write("inTipoInscricao", $inTipoInscricao);
Sessao::write("inCGM"          , $rsConfiguracaoRais->getCampo("numcgm"));
Sessao::write("boCNPJ"         , $boCNPJ);
Sessao::write("inCei"          , $inCei);
Sessao::write("inPrefixo"      , $inPrefixo);
Sessao::write("inExercicio"    , $inExercicio);

$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setRotulo("CGM do Responsável");
$obIPopUpCGM->setTitle("Selecione o CGM do responsável pela entrega da RAIS.");
$obIPopUpCGM->setNull(false);
$obIPopUpCGM->setTipo('fisica');

$obSpnCGMResponsavel = new Span();
$obSpnCGMResponsavel->setId("spnCGMResponsavel");

$obHdnCGMResponsavel = new hiddenEval();
$obHdnCGMResponsavel->setName("hdnCGMResponsavel");

$obTxtTelefone = new TextBox();
$obTxtTelefone->setRotulo("Telefone");
$obTxtTelefone->setName("stTelefone");
$obTxtTelefone->setTitle("Informe o telefone para contato do responsável pelas informações da RAIS (DDD+número).");
$obTxtTelefone->setNull(false);
$obTxtTelefone->setSize(12);
$obTxtTelefone->setMascara("99-999999999");
$obTxtTelefone->setValue($stTelefone);;

$obTxtEmail = new TextBox();
$obTxtEmail->setRotulo("Email");
$obTxtEmail->setName("stMail");
$obTxtEmail->setTitle("Informe o email do responsável pelas informações da RAIS.");
$obTxtEmail->setNull(false);
$obTxtEmail->setSize(40);
$obTxtEmail->setMaxLength(30);
$obTxtEmail->setValue($stEmail);

$obTxtNatureza = new TextBox();
$obTxtNatureza->setRotulo("Natureza Jurídica do Estabelecimento");
$obTxtNatureza->setName("stNatureza");
$obTxtNatureza->setTitle("Informe o código da natureza jurídica do estabelecimento, conforme manual/programa da RAIS.");
$obTxtNatureza->setNull(false);
$obTxtNatureza->setSize(5);
$obTxtNatureza->setMaxLength(4);
$obTxtNatureza->setValue($stNatureza);

$obTxtMunicipio = new Inteiro();
$obTxtMunicipio->setRotulo("Código do Município");
$obTxtMunicipio->setName("inCodMunicipio");
$obTxtMunicipio->setTitle("Informe o código da municipío da entidade, conforme manual/programa da RAIS.");
$obTxtMunicipio->setNull(false);
$obTxtMunicipio->setSize(8);
$obTxtMunicipio->setMaxLength(7);
$obTxtMunicipio->setValue($inCodMunicipio);

$obTxtDataBase = new Inteiro();
$obTxtDataBase->setRotulo("Data Base da Categoria");
$obTxtDataBase->setName("inDataBase");
$obTxtDataBase->setTitle("Informe a data-base da categoria (mês do reajuste salarial) com maior número de empregados no estabelecimento/entidade. Exemplo: 01-Janeiro, 02-Fevereiro, etc..");
$obTxtDataBase->setNull(false);
$obTxtDataBase->setSize(3);
$obTxtDataBase->setMaxLength(2);
$obTxtDataBase->setValue($inDataBase);

$obChkCNPJ = new CheckBox();
$obChkCNPJ->setRotulo("CNPJ Possui CEI Vinculado");
$obChkCNPJ->setName("boCNPJ");
$obChkCNPJ->setValue("true");
$obChkCNPJ->setTitle("Marque para informar o CEI da Entidade, somente pelo estabelecimento que possuir obra de construção civil.");
$obChkCNPJ->obEvento->setOnChange(" if (this.checked == true) { this.value = true; } else { this.value = false; } montaParametrosGET('gerarSpanCEI','boCNPJ')");
if ($boCNPJ == "true") {
    $obChkCNPJ->setChecked(true);
}

$obSpnCNPJ = new Span();
$obSpnCNPJ->setId("spnCNPJ");

$obHdnCNPJ = new hiddenEval();
$obHdnCNPJ->setName("hdnCNPJ");

//Combo Tipo de Sistema de Controle de Ponto
$obTIMATipoControlePonto  = new TIMAConfiguracaoRais();
$obTIMATipoControlePonto->recuperaTipoControlePonto($rsTipoControlePonto);

$stFiltro = "WHERE tipo_controle_ponto.cod_tipo_controle_ponto=
(SELECT cod_tipo_controle_ponto FROM ima.configuracao_rais WHERE exercicio='".$_GET["inExercicio"]."')";
$obTIMATipoControlePonto->recuperaTipoControlePonto($rsCodTipoControlePonto, $stFiltro);
if ($rsCodTipoControlePonto->getCampo('cod_tipo_controle_ponto')!=null) {
    $inCodTipoControlePonto = $rsCodTipoControlePonto->getCampo('cod_tipo_controle_ponto');
}

$obTxtTipoControlePonto  = new TextBox;
$obTxtTipoControlePonto->setRotulo    ( "Tipo de Sistema de Controle de Ponto" );
$obTxtTipoControlePonto->setTitle     ( "Informe o tipo de sistema de controle de ponto, conforme manual/programa da RAIS." );
$obTxtTipoControlePonto->setName      ( "inCodTipoControlePontoTxt" );
$obTxtTipoControlePonto->setValue     ( isset($inCodTipoControlePonto) ? $inCodTipoControlePonto : null );
$obTxtTipoControlePonto->setSize      ( 1 );
$obTxtTipoControlePonto->setMaxLength ( 1 );
$obTxtTipoControlePonto->setInteiro   ( true  );
$obTxtTipoControlePonto->setNull      ( false );

$obCmbTipoControlePonto = new Select;
$obCmbTipoControlePonto->setRotulo     ( "Tipo de Sistema de Controle de Ponto" );
$obCmbTipoControlePonto->setName       ( "inCodTipoControlePonto" );
$obCmbTipoControlePonto->setValue      ( isset($inCodTipoControlePonto) ? $inCodTipoControlePonto : null );

$obCmbTipoControlePonto->setStyle      ( "width: 600px; word-wrap:'break-word'");
$obCmbTipoControlePonto->setCampoID    ( "cod_tipo_controle_ponto" );
$obCmbTipoControlePonto->setCampoDesc  ( "descricao" );
$obCmbTipoControlePonto->addOption     ( "", "Selecione" );
$obCmbTipoControlePonto->setNull       ( false );
$obCmbTipoControlePonto->preencheCombo ( $rsTipoControlePonto );
//Fim do Combo Tipo de Sistema de Controle de Ponto

include_once(CAM_GRH_FOL_COMPONENTES."ISelectMultiploEvento.class.php");
$obISelectMultiploEvento = new ISelectMultiploEvento();
$obISelectMultiploEvento->setRotulo("Composição da Remuneração Rais");
$obISelectMultiploEvento->setNull(false);
$obISelectMultiploEvento->setTitle("Selecionar os eventos que fazem parte dos rendimentos mensais para RAIS. Não deve integrar a base da remuneração eventos como indenizações, salário família, férias indenizadas na rescisão e seu respectivo abono, vale-transporte, vale-refeição  entre outros, conforme descrito no manual da RAIS.");
$obISelectMultiploEvento->setProventos();
$obISelectMultiploEvento->setDescontos();
$obISelectMultiploEvento->setBases();
$obISelectMultiploEvento->montarEventosDisponiveis();

$obISelectMultiploEvento2 = new ISelectMultiploEvento();
$obISelectMultiploEvento2->setRotulo("Eventos Horas Extras");
$obISelectMultiploEvento2->setNull(false);
$obISelectMultiploEvento2->setTitle("Selecione os eventos referente a pagamento de horas extras, a fim de informar a quantidade total de horas extras trabalhadas mensalmente na RAIS.");
$obISelectMultiploEvento->setProventos();
$obISelectMultiploEvento->setDescontos();
$obISelectMultiploEvento->setBases();
$obISelectMultiploEvento->montarEventosDisponiveis();
$obISelectMultiploEvento2->setNomeLista1($obISelectMultiploEvento2->getNomeLista1()."2");
$obISelectMultiploEvento2->setNomeLista2($obISelectMultiploEvento2->getNomeLista2()."2");

$obIntExercicio = new Inteiro();
$obIntExercicio->setRotulo("Exercício");
$obIntExercicio->setTitle("Informe o exercício de vigência das configurações.");
$obIntExercicio->setName("inExercicio");
$obIntExercicio->setSize(5);
$obIntExercicio->setMaxLength(4);
$obIntExercicio->setNull(false);
$obIntExercicio->setValue($inExercicio);
if ($_REQUEST["stAcao"] == "alterar") {
    $obIntExercicio->setReadOnly(true);
}

$obBtnOk = new Ok;

$obBtnLimpar = new Button();
$obBtnLimpar->setName								( "btnLimpar" 															);
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setTitle								( "Clique para limpar os dados dos campos." 							);
$obBtnLimpar->obEvento->setOnClick				    ( "montaParametrosGET('limpar', '', true);						   ");

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addForm								( $obForm 																);
$obFormulario->addTitulo("Configuração RAIS");
$obFormulario->addComponente($obIntExercicio);
$obFormulario->addTitulo("Informações do Responsável pela Entrega");
$obFormulario->addComponente($obIPopUpCGM);
$obFormulario->addHidden($obHdnCGMResponsavel,true);
$obFormulario->addComponente($obTxtTelefone);
$obFormulario->addComponente($obTxtEmail);
$obFormulario->addTitulo("Informações do Estabelecimento");
$obFormulario->addComponente($obTxtNatureza);
$obFormulario->addComponente($obTxtMunicipio);
$obFormulario->addComponente($obTxtDataBase);
$obFormulario->addComponente($obChkCNPJ);
$obFormulario->addSpan($obSpnCNPJ);
$obFormulario->addHidden($obHdnCNPJ,true);
$obFormulario->addComponenteComposto( $obTxtTipoControlePonto , $obCmbTipoControlePonto );
$obFormulario->addTitulo("Informações dos Rendimentos");
$obFormulario->addComponente($obISelectMultiploEvento);
$obFormulario->addComponente($obISelectMultiploEvento2);
$obFormulario->defineBarra  					    ( array( $obBtnOk, $obBtnLimpar ) 										);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Arquivo de Formulário para configuração da exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.20

    $Id: FMConfiguracaoCAGED.php 66444 2016-08-29 19:13:17Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = "ConfiguracaoCAGED";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$jsOnload = "executaFuncaoAjax('preencherDadosCaged');";

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoCaged.class.php");
$obTIMAConfiguracaoCaged = new TIMAConfiguracaoCaged();
$obTIMAConfiguracaoCaged->recuperaTodos($rsConfiguracaoCaged);
$rsCnae = new recordset();
if ($rsConfiguracaoCaged->getNumLinhas() > 0) {
    $stPrimeiraDeclaracao = $rsConfiguracaoCaged->getCampo("tipo_declaracao");
    $inCodCnae = $rsConfiguracaoCaged->getCampo("cod_cnae");
    $inCodConfiguracao = $rsConfiguracaoCaged->getCampo("cod_configuracao");

    include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php"         );
    $obTCEMCnaeFiscal = new TCEMCnaeFiscal();
    $stFiltro = " WHERE cod_cnae = ".$inCodCnae;
    $obTCEMCnaeFiscal->recuperaTodos($rsCnae,$stFiltro);
}

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao");
$obHdnAcao->setValue( 'configurar' );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( 'submeter' );

$obHdnCodConfiguracao =  new Hidden;
$obHdnCodConfiguracao->setName( "inCodConfiguracao" );
$obHdnCodConfiguracao->setValue( $inCodConfiguracao );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );
Sessao::write("obForm", $obForm);

$obChkInformarResponsavel = new Checkbox();
$obChkInformarResponsavel->setRotulo("Informar Responsável");
$obChkInformarResponsavel->setName("boInformarResponsavel");
$obChkInformarResponsavel->setId("boInformarResponsavel");
$obChkInformarResponsavel->setTitle("Para informar um responsável autorizado pelas informações da Entidade, marque está opção. Caso contrário o sistema utilizará as informações do próprio estabelecimento.");
$obChkInformarResponsavel->setValue("true");
$obChkInformarResponsavel->obEvento->setOnChange("if (this.checked){montaParametrosGET('gerarSpanInformarResponsavel','boInformarResponsavel');}else{ jq('#spnInformarResponsavel').html(''); } ");

$obSpnInformarResponsavel = new Span();
$obSpnInformarResponsavel->setId("spnInformarResponsavel");

$obHdnInformarResponsavel = new HiddenEval();
$obHdnInformarResponsavel->setName("hdnInformarResponsavel");

$obChkInformarCEI = new Checkbox();
$obChkInformarCEI->setRotulo("Informar CEI");
$obChkInformarCEI->setName("boInformarCEI");
$obChkInformarCEI->setId("boInformarCEI");
$obChkInformarCEI->setTitle("Marque para utilizar o número do CEI ao invés de CNPJ da Entidade.");
$obChkInformarCEI->setValue("true");
$obChkInformarCEI->obEvento->setOnChange("if (this.checked){montaParametrosGET('gerarSpanInformarCEI','boInformarCEI');}else{ jq('#spnInformarCEI').html(''); } ");

$obSpnInformarCEI = new Span();
$obSpnInformarCEI->setId("spnInformarCEI");

$obHdnInformarCEI = new HiddenEval();
$obHdnInformarCEI->setName("hdnInformarCEI");

$obCmbPrimeiraDeclaracao = new Select;
$obCmbPrimeiraDeclaracao->setRotulo("Primeira Declaração");
$obCmbPrimeiraDeclaracao->setName("stPrimeiraDeclaracao");
$obCmbPrimeiraDeclaracao->setStyle("width: 200px");
$obCmbPrimeiraDeclaracao->setTitle("Informe se a declaração é a primeira emissão do CAGED ou se já existe emissões anteriores.");
$obCmbPrimeiraDeclaracao->addOption("","Selecione");
$obCmbPrimeiraDeclaracao->addOption("1","Primeira declaração");
$obCmbPrimeiraDeclaracao->addOption("2","Já informou ao CAGED anteriormente");
$obCmbPrimeiraDeclaracao->setValue($stPrimeiraDeclaracao);
$obCmbPrimeiraDeclaracao->setNull(false);

$obBscCodigoCnae = new BuscaInner;
$obBscCodigoCnae->setRotulo("CNAE Fiscal");
$obBscCodigoCnae->setTitle("Informe o CNAE fiscal da entidade.");
$obBscCodigoCnae->setNull(false);
$obBscCodigoCnae->setId("stCnae");
$obBscCodigoCnae->setValue($rsCnae->getCampo("nom_atividade"));
$obBscCodigoCnae->obCampoCod->setInteiro(false);
$obBscCodigoCnae->obCampoCodHidden->setValue($rsCnae->getCampo("cod_cnae"));
$obBscCodigoCnae->obCampoCod->setName("inCodCnae");
$obBscCodigoCnae->obCampoCod->setValue($rsCnae->getCampo("cod_estrutural"));
$obBscCodigoCnae->obCampoCod->setSize(20);
$obBscCodigoCnae->obCampoCod->setMaxLength(160);
$obBscCodigoCnae->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaCnae','inCodCnae');");
$obBscCodigoCnae->setFuncaoBusca("abrePopUp('".CAM_GRH_IMA_POPUPS."configuracao/LSProcurarCnae.php','frm','inCodCnae','stCnae','','".Sessao::getId()."','800','550')");

include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php");
$obISelectMultiploRegSubCarEsp = new ISelectMultiploRegSubCarEsp();
$obISelectMultiploRegSubCarEsp->setDisabledCargo(true);
$obISelectMultiploRegSubCarEsp->setDisabledFuncao(true);
$obISelectMultiploRegSubCarEsp->setDisabledEspecialidade(true);

include_once(CAM_GRH_FOL_COMPONENTES."ISelectMultiploEvento.class.php");
$obISelectMultiploEvento = new ISelectMultiploEvento();
$obISelectMultiploEvento->setNull(false);
$obISelectMultiploEvento->setProventos();
$obISelectMultiploEvento->setBases();
$obISelectMultiploEvento->montarEventosDisponiveis();
$obISelectMultiploEvento->setTitle("Selecione os eventos que farão parte da composição da remuneração para o CAGED.");

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick ( " if ( Valida() ) { montaParametrosPOST('submeter', '', true); }" );

$obBtnLimpar = new Limpar();

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnCodConfiguracao);
$obFormulario->addTitulo($obRFolhaPagamentoFolhaSituacao->consultarCompetencia(),"right");
$obFormulario->addTitulo("Configuração da Exportação CAGED");
$obFormulario->addTitulo("Informações do Estabelecimento Responsável pelas Informações (AUTORIZADO)");
$obFormulario->addComponente($obChkInformarResponsavel);
$obFormulario->addSpan($obSpnInformarResponsavel);
$obFormulario->addHidden($obHdnInformarResponsavel,true);
$obFormulario->addTitulo("Informações do Estabelecimento/Entidade");
$obFormulario->addComponente($obChkInformarCEI);
$obFormulario->addSpan($obSpnInformarCEI);
$obFormulario->addHidden($obHdnInformarCEI,true);
$obFormulario->addComponente($obCmbPrimeiraDeclaracao);
$obFormulario->addComponente($obBscCodigoCnae);
$obFormulario->addTitulo("Configuração do Tipo de Movimento - Admissão");
$obFormulario->addTitulo("Contrato de Prazo Determinado");
$obISelectMultiploRegSubCarEsp->geraFormulario($obFormulario);
$obFormulario->addTitulo("Configuração da Remuneração");
$obFormulario->addTitulo("Lista de Eventos");
$obFormulario->addComponente($obISelectMultiploEvento);
$obFormulario->defineBarra( array($obBtnOk,$obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

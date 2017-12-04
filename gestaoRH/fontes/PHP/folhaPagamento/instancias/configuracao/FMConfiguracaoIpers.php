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
    * Titulo do arquivo (Ex.: "Formulario de configura��o do IPERS")
    * Data de Cria��o   : 23/06/2008

    * @author Rafael Garbin

    * Casos de uso: uc-04.05.66

    $Id: FMConfiguracaoIpers.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php");
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php");
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
include_once ( CAM_GRH_FOL_COMPONENTES."IBuscaInnerEvento.class.php");
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoIpe.class.php" );

$stLink = "";
foreach ($_GET as $stCampo=>$stValor) {
    if ($stCampo != 'PHPSESSID' and $stCampo != 'iURLRandomica' and $stCampo != 'stAcao') {
        $stLink .= "&".$stCampo."=".$stValor;
    }
}

$stPrograma = "ConfiguracaoIpers";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stLocationDestino = "";
switch ($stAcao) {
    case "alterar";
    case "excluir";
        $stLocationDestino = $pgList;
        break;
    case "incluir";
        $stLocationDestino = $pgForm;
        break;
}
$stLocationDestino = $stLocationDestino."?stAcao=".$stAcao;

$stAcao = trim($_REQUEST["stAcao"]);
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obTFolhaPagamentoConfiguracaoIpe = new TFolhaPagamentoConfiguracaoIpe();
if (isset($_REQUEST["dtVigencia"]) && trim($_REQUEST["dtVigencia"]) != "") {
    $stFiltro = " WHERE configuracao_ipe.vigencia = to_date('".$_REQUEST["dtVigencia"]."', 'dd/mm/yyyy')";
}
$stOrdem = " configuracao_ipe.vigencia::varchar||configuracao_ipe.cod_configuracao::varchar DESC";
$stOrdem .= " LIMIT 1";
$rsConfiguracaoIpe = new RecordSet();

if ($stAcao != "incluir") {
    $jsOnload = "montaParametrosGET('preencherInnerEventos','');";
    $obTFolhaPagamentoConfiguracaoIpe->recuperaRelacionamento($rsConfiguracaoIpe, $stFiltro, $stOrdem);
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl );

#Pensionista
$obTAtributoDinamico = new TAdministracaoAtributoDinamico;
$stFiltro = " WHERE cod_modulo = 22";
$stFiltro .="   AND cod_cadastro = 7";
$obTAtributoDinamico->recuperaTodos($rsAtributosDinamicos, $stFiltro, $stOrderm='nom_atributo');

$obCmbNumeroMatriculaPensionista = new Select();
$obCmbNumeroMatriculaPensionista->setRotulo("Número da Matricula IPE/RS");
$obCmbNumeroMatriculaPensionista->setTitle("Informar o atributo dinâmico referente a matrícula do IPE. Este atributo deve ser criado na gestão administrativa, campo numérico de 13 posições para uso no cadastro do servidor (não obrigatário).");
$obCmbNumeroMatriculaPensionista->setValue($rsConfiguracaoIpe->getCampo("cod_modulo_mat_pen")."-".$rsConfiguracaoIpe->getCampo("cod_cadastro_mat_pen")."-".$rsConfiguracaoIpe->getCampo("cod_atributo_mat_pen"));
$obCmbNumeroMatriculaPensionista->setName("inNumeroMatriculaPensionistaIPERS");
$obCmbNumeroMatriculaPensionista->setNullBarra(false);
$obCmbNumeroMatriculaPensionista->addOption("","Selecione");
$obCmbNumeroMatriculaPensionista->setStyle( "width: 250px" );
$obCmbNumeroMatriculaPensionista->setCampoId("[cod_modulo]-[cod_cadastro]-[cod_atributo]");
$obCmbNumeroMatriculaPensionista->setCampoDesc("nom_atributo");
$obCmbNumeroMatriculaPensionista->preencheCombo($rsAtributosDinamicos);

#Pensionista
$rsAtributosDinamicos->setPrimeiroElemento();
$obCmbNumeroDtIngressoPensionista = new Select();
$obCmbNumeroDtIngressoPensionista->setRotulo("Data de Ingresso");
$obCmbNumeroDtIngressoPensionista->setTitle("Informar o atributo dinâmico referente a data de ingresso no IPE. Este atributo deve ser criado na gestão administrativa, campo do tipo data para uso no cadastro do servidor (não obrigatório).");
$obCmbNumeroDtIngressoPensionista->setName("stDataIngressoPensionistaIPERS");
$obCmbNumeroDtIngressoPensionista->setValue($rsConfiguracaoIpe->getCampo("cod_modulo_data_pen")."-".$rsConfiguracaoIpe->getCampo("cod_cadastro_data_pen")."-".$rsConfiguracaoIpe->getCampo("cod_atributo_data_pen"));
$obCmbNumeroDtIngressoPensionista->setNullBarra(false);
$obCmbNumeroDtIngressoPensionista->addOption("","Selecione");
$obCmbNumeroDtIngressoPensionista->setStyle( "width: 250px" );
$obCmbNumeroDtIngressoPensionista->setCampoId("[cod_modulo]-[cod_cadastro]-[cod_atributo]");
$obCmbNumeroDtIngressoPensionista->setCampoDesc("nom_atributo");
$obCmbNumeroDtIngressoPensionista->preencheCombo($rsAtributosDinamicos);

#Servidor
$obTAtributoDinamico = new TAdministracaoAtributoDinamico;
$stFiltro = " WHERE cod_modulo = 22";
$stFiltro .="   AND cod_cadastro = 5";
$obTAtributoDinamico->recuperaTodos($rsAtributosDinamicos, $stFiltro, $stOrderm='nom_atributo');

$obCmbNumeroMatriculaServidor = new Select();
$obCmbNumeroMatriculaServidor->setRotulo("Número da Matricula IPE/RS");
$obCmbNumeroMatriculaServidor->setTitle("Informar o atributo dinâmico referente a matrícula do IPE. Este atributo deve ser criado na gestão administrativa, campo numérico de 13 posições para uso no cadastro do servidor (não obrigatório).");
$obCmbNumeroMatriculaServidor->setValue($rsConfiguracaoIpe->getCampo("cod_modulo_mat")."-".$rsConfiguracaoIpe->getCampo("cod_cadastro_mat")."-".$rsConfiguracaoIpe->getCampo("cod_atributo_mat"));
$obCmbNumeroMatriculaServidor->setName("inNumeroMatriculaServidorIPERS");
$obCmbNumeroMatriculaServidor->setNullBarra(false);
$obCmbNumeroMatriculaServidor->setNull(false);
$obCmbNumeroMatriculaServidor->addOption("","Selecione");
$obCmbNumeroMatriculaServidor->setStyle( "width: 250px" );
$obCmbNumeroMatriculaServidor->setCampoId("[cod_modulo]-[cod_cadastro]-[cod_atributo]");
$obCmbNumeroMatriculaServidor->setCampoDesc("nom_atributo");
$obCmbNumeroMatriculaServidor->preencheCombo($rsAtributosDinamicos);

#Servidor
$rsAtributosDinamicos->setPrimeiroElemento();
$obCmbNumeroDtIngressoServidor = new Select();
$obCmbNumeroDtIngressoServidor->setRotulo("Data de Ingresso");
$obCmbNumeroDtIngressoServidor->setTitle("Informar o atributo dinâmico referente a data de ingresso no IPE. Este atributo deve ser criado na gestão administrativa, campo do tipo data para uso no cadastro do servidor (não obrigatório).");
$obCmbNumeroDtIngressoServidor->setName("stDataIngressoServidorIPERS");
$obCmbNumeroDtIngressoServidor->setValue($rsConfiguracaoIpe->getCampo("cod_modulo_data")."-".$rsConfiguracaoIpe->getCampo("cod_cadastro_data")."-".$rsConfiguracaoIpe->getCampo("cod_atributo_data"));
$obCmbNumeroDtIngressoServidor->setNullBarra(false);
$obCmbNumeroDtIngressoServidor->setNull(false);
$obCmbNumeroDtIngressoServidor->addOption("","Selecione");
$obCmbNumeroDtIngressoServidor->setStyle( "width: 250px" );
$obCmbNumeroDtIngressoServidor->setCampoId("[cod_modulo]-[cod_cadastro]-[cod_atributo]");
$obCmbNumeroDtIngressoServidor->setCampoDesc("nom_atributo");
$obCmbNumeroDtIngressoServidor->preencheCombo($rsAtributosDinamicos);

$obIBscEventoBase = new IBuscaInnerEvento();
$obIBscEventoBase->setRotulo("Evento Base da Remuneração do IPE/RS");
$obIBscEventoBase->setId("stEventoBaseIPERS");
$obIBscEventoBase->setTitle("Informe o evento de base da remuneração do IPERS. Este evento será utilizado tanto para o arquivo quanto para o desconto automático do IPERS.");
$obIBscEventoBase->setNull(false);
$obIBscEventoBase->obCampoCod->setName("inCodigoEventoBaseIPERS");
$obIBscEventoBase->obCampoCod->setId("inCodigoEventoBaseIPERS");
$obIBscEventoBase->setNaturezasBase();
$obIBscEventoBase->setEventoSistema(true);
$obIBscEventoBase->setNaturezaChecked("B");
$obIBscEventoBase->montaOnChange();
$obIBscEventoBase->montaPopUp();

$obIBscEventoDesconto = new IBuscaInnerEvento();
$obIBscEventoDesconto->setRotulo("Evento Automático de Desconto do IPE/RS");
$obIBscEventoDesconto->setId("stEventoDescontoIPERS");
$obIBscEventoDesconto->setTitle("Informe o evento automático de desconto do IPE, cadastrado previamente no cadastro de eventos.");
$obIBscEventoDesconto->setNull(false);
$obIBscEventoDesconto->obCampoCod->setName("inCodigoEventoDescontoIPERS");
$obIBscEventoDesconto->obCampoCod->setId("inCodigoEventoDescontoIPERS");
$obIBscEventoDesconto->setNaturezasDesconto();
$obIBscEventoDesconto->setEventoSistema(true);
$obIBscEventoDesconto->setNaturezaChecked("D");
$obIBscEventoDesconto->montaOnChange();
$obIBscEventoDesconto->montaPopUp();

$obTxtOrgao = new TextBox();
$obTxtOrgao->setRotulo("Código do Órgão");
$obTxtOrgao->setTitle("Informe o código do órgão que identifica a entidade perante o IPE.");
$obTxtOrgao->setName("inCodOrgao");
$obTxtOrgao->setValue($rsConfiguracaoIpe->getCampo("codigo_orgao"));
$obTxtOrgao->setInteiro(true);
$obTxtOrgao->setNullBarra(false);
$obTxtOrgao->setNull(false);
$obTxtOrgao->setMascara(999);

$obTxtPercContribuicaoPatronal = new Numerico();
$obTxtPercContribuicaoPatronal->setRotulo("Percentual de Contribuição Patronal:");
$obTxtPercContribuicaoPatronal->setName("stPercContribPatronal");
$obTxtPercContribuicaoPatronal->setTitle("Informe o percentual de contribuição patronal(do empregado).");
$obTxtPercContribuicaoPatronal->setSize(10);
$obTxtPercContribuicaoPatronal->setNull(false);
$obTxtPercContribuicaoPatronal->setMaxLength(6);
$obTxtPercContribuicaoPatronal->setValue($rsConfiguracaoIpe->getCampo("contribuicao_pat"));

$obTxtPercContribuicaoServidor = new Numerico();
$obTxtPercContribuicaoServidor->setRotulo("Percentual de Contribuição do Servidor:");
$obTxtPercContribuicaoServidor->setName("stPercContribServidor");
$obTxtPercContribuicaoServidor->setTitle("Informe o percentual de desconto de contribuição do servidor.");
$obTxtPercContribuicaoServidor->setSize(10);
$obTxtPercContribuicaoServidor->setNull(false);
$obTxtPercContribuicaoServidor->setMaxLength(6);
$obTxtPercContribuicaoServidor->setValue($rsConfiguracaoIpe->getCampo("contibuicao_serv"));

$obDtVigencia = new Data;
$obDtVigencia->setName("dtVigencia");
$obDtVigencia->setRotulo("Vigência");
$obDtVigencia->setNull(false);
$obDtVigencia->setTitle("Informe a vigência, a partir de quando passa a vigorar as configurações.");
$obDtVigencia->setValue($rsConfiguracaoIpe->getCampo("vigencia"));

$obLabelPerc = new Label();
$obLabelPerc->setValue("%");

$obBtnCancelar = new Button;
$obBtnCancelar->setName ( "cancelar" );
$obBtnCancelar->setValue( "Cancelar" );
$obBtnCancelar->setTitle( "Clique para cancelar." );
$obBtnCancelar->obEvento->setOnClick( "Cancelar('".$stLocationDestino."');" );

$obBtnOk = new ok;

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm );
$obFormulario->addTitulo         ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden         ( $obHdnAcao );
$obFormulario->addHidden         ( $obHdnCtrl );
$obFormulario->addTitulo         ( "Configuração do IPE/RS");
$obFormulario->addTitulo         ( "Atributo Dinâmico Cadastro do Pensionista" );
$obFormulario->addComponente     ( $obCmbNumeroMatriculaPensionista );
$obFormulario->addComponente     ( $obCmbNumeroDtIngressoPensionista );
$obFormulario->addTitulo         ( "Atributo Dinâmico Cadastro do Servidor" );
$obFormulario->addComponente     ( $obCmbNumeroMatriculaServidor );
$obFormulario->addComponente     ( $obCmbNumeroDtIngressoServidor );
$obFormulario->addTitulo         ( "Dados da Remuneração" );
$obFormulario->addComponente     ( $obIBscEventoBase );
$obFormulario->addComponente     ( $obIBscEventoDesconto );
$obFormulario->addTitulo         ( "Dados Gerais" );
$obFormulario->addComponente     ( $obTxtOrgao );
$obFormulario->agrupaComponentes ( array($obTxtPercContribuicaoPatronal, $obLabelPerc) );
$obFormulario->agrupaComponentes ( array($obTxtPercContribuicaoServidor, $obLabelPerc) );
$obFormulario->addComponente     ( $obDtVigencia );
$obFormulario->defineBarra       ( array($obBtnOk,$obBtnCancelar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

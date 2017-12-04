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
    * Arquivo de Filtro
    * Data de Criação: 09/09/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                          );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

$stPrograma = "CertidaoTempoServico";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$jsOnload = "executaFuncaoAjax('processarFormulario');";
$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setTodos();
$obIFiltroComponentes->setGeral(false);
$onChange = $obIFiltroComponentes->obCmbTipoFiltro->obEvento->getOnChange();
$onChange .= " montaParametrosGET('gerarSpanOrdenacaoMatricula', 'stTipoFiltro');";
$obIFiltroComponentes->obCmbTipoFiltro->obEvento->setOnChange($onChange);

$obSpnOrdenacaoMatricula = new Span();
$obSpnOrdenacaoMatricula->setId("spnOrdenacaoMatricula");

$obPeriodo = new Periodo();
$obPeriodo->setNull(false);
$obPeriodo->setRotulo("Período da Contagem");
$obPeriodo->setTitle("Informe o período de contagem para o tempo de serviço. Deixar em branco para contagem total de tempo do servidor até a data atual.");
$obPeriodo->obDataInicial->setValue('01/01/1900');
$obPeriodo->obDataFinal->setValue(date("d/m/Y"));

$obTxtNumeroCertidao = new TextBox();
$obTxtNumeroCertidao->setRotulo("Número da Certidão");
$obTxtNumeroCertidao->setName("stNumeroCertidao");
$obTxtNumeroCertidao->setTitle("Informe o número da certidão, caso exista.");

include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroContrato.class.php'   );
$stTipo = "contrato_todos";
$obIContratoDigitoVerificador = new IContratoDigitoVerificador();
$obIContratoDigitoVerificador->setNull(false);
$obIContratoDigitoVerificador->setName("inContratoResponsavel");
$obIContratoDigitoVerificador->setRotulo("Matrícula do Responsável pelas Informações");
$obIContratoDigitoVerificador->obTxtRegistroContrato->setName("inContratoResponsavel");
$obIContratoDigitoVerificador->obTxtRegistroContrato->setId("inContratoResponsavel");
$obIContratoDigitoVerificador->obTxtRegistroContrato->setNull(false);
$obIContratoDigitoVerificador->setPagFiltro(true);
$obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("");
$obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange( "montaParametrosGET('validarMatricula','inContratoResponsavel');" );
$obIContratoDigitoVerificador->setFuncaoBuscaFiltro("abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLMatriculaFiltroComponente.php','frm','".$obIContratoDigitoVerificador->obTxtRegistroContrato->getName()."','".$obIContratoDigitoVerificador->obTxtRegistroContrato->getId()."','','".Sessao::getId()."&stTipo=".$stTipo."','800','550')");

$obCmbTipoCertidao = new Select;
$obCmbTipoCertidao->setRotulo( "Tipo de Certidão" );
$obCmbTipoCertidao->setTitle( "Selecione o tipo de certidão para emissão." );
$obCmbTipoCertidao->setName( "stTipoCertidao" );
$obCmbTipoCertidao->setStyle( "width: 200px" );
$obCmbTipoCertidao->addOption( "completa", "Completa" );
$obCmbTipoCertidao->addOption( "descritiva", "Descritiva" );
$obCmbTipoCertidao->addOption( "inss", "Modelo INSS" );
$obCmbTipoCertidao->setNull( false );
$obCmbTipoCertidao->obEvento->setOnChange("montaParametrosGET('gerarSpanTipoCertidao','stTipoCertidao');");

$obSpnTipoCertidao = new Span();
$obSpnTipoCertidao->setId("spnTipoCertidao");

$obHdnTipoCertidao = new hiddenEval();
$obHdnTipoCertidao->setId("hdnTipoCertidao");
$obHdnTipoCertidao->setName("hdnTipoCertidao");

include_once(CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obIMontaAssinaturas = new IMontaAssinaturas();

$obHdnEntidade = new hidden();
$obHdnEntidade->setId("inCodEntidade");
$obHdnEntidade->setName("inCodEntidade");
$obHdnEntidade->setValue(Sessao::getCodEntidade($boTransacao));

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
Sessao::write("obForm", $obForm);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo   ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm     ( $obForm );
$obFormulario->addHidden   ( $obHdnAcao );
$obFormulario->addHidden   ( $obHdnEntidade );
$obFormulario->addHidden   ( $obHdnTipoCertidao,true );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addSpan($obSpnOrdenacaoMatricula);
$obFormulario->addComponente($obPeriodo);
$obFormulario->addComponente($obTxtNumeroCertidao);
$obIContratoDigitoVerificador->geraFormulario($obFormulario);
$obFormulario->addComponente($obCmbTipoCertidao);
$obFormulario->addSpan($obSpnTipoCertidao);
$obIMontaAssinaturas->geraFormulario($obFormulario);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

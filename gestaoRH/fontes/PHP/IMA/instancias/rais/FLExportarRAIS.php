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
    * Data de Criação: 26/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: FLExportarRAIS.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.13
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRAIS";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::write('link', '');

$stAcao = $request->get("stAcao");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                           );
$obForm->setTarget("oculto");

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegSubFunEsp();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setTodos();

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
$arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
$inExercicio = $arCompetencia[2]-1;

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoRais.class.php");
$obTIMAConfiguracaoRAIS = new TIMAConfiguracaoRais();
$obTIMAConfiguracaoRAIS->setDado("exercicio",$inExercicio);
$obTIMAConfiguracaoRAIS->recuperaPorChave($rsConfiguracao);
if ($rsConfiguracao->getNumLinhas() < 0) {
    $inExercicio = '';
}

include_once(CAM_GRH_PES_COMPONENTES."ISelectAnoCompetencia.class.php");
$obISelectAnoCompetencia = new ISelectAnoCompetencia();
$obISelectAnoCompetencia->obCmbAnoCompetencia->setValue($inExercicio);
$obISelectAnoCompetencia->obCmbAnoCompetencia->setRotulo("Ano-Base");
$obISelectAnoCompetencia->obCmbAnoCompetencia->setTitle("Selecione o ano-base para emissão do arquivo da RAIS. Exemplo: entrega em 2007 da RAIS ano-base 2006.");
$obISelectAnoCompetencia->obCmbAnoCompetencia->setNull(false);
$obISelectAnoCompetencia->obCmbAnoCompetencia->obEvento->setOnChange("montaParametrosGET('validarConfiguracaoAno','inAnoCompetencia');");

$obRdoNormal = new Radio();
$obRdoNormal->setRotulo("Indicador de Recolhimento");
$obRdoNormal->setTitle("Informe o tipo de recolhimento: normal ou retificadora (em caso de correção de aquivo já enviado ao MTE).");
$obRdoNormal->setName("stIndicador");
$obRdoNormal->setValue("2");
$obRdoNormal->setLabel("Normal");
$obRdoNormal->setNull(false);
$obRdoNormal->setChecked(true);
$obRdoNormal->obEvento->setOnChange("montaParametrosGET('gerarSpanDataRetificacao','stIndicador');");

$obRdoRetificadora = new Radio();
$obRdoRetificadora->setRotulo("Indicador de Recolhimento");
$obRdoRetificadora->setTitle("Informe o tipo de recolhimento: normal ou retificadora (em caso de correção de aquivo já enviado ao MTE).");
$obRdoRetificadora->setName("stIndicador");
$obRdoRetificadora->setValue("1");
$obRdoRetificadora->setLabel("Retificadora");
$obRdoRetificadora->setNull(false);
$obRdoRetificadora->obEvento->setOnChange("montaParametrosGET('gerarSpanDataRetificacao','stIndicador');");

$obSpnDataRetificacao = new Span();
$obSpnDataRetificacao->setId("spnDataRetificacao");

$obHdnDataRetificacao = new hiddenEval();
$obHdnDataRetificacao->setName("hdnDataRetificacao");

$obDtGeracaoArquivo = new Data();
$obDtGeracaoArquivo->setRotulo("Data da Geração do Arquivo");
$obDtGeracaoArquivo->setName("dtGeracaoArquivo");
$obDtGeracaoArquivo->setTitle("Informe a data da geração do arquivo da RAIS.");
$obDtGeracaoArquivo->setNull(false);

$obBtnOk = new Ok();

$obBtnLimpar = new Limpar();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $stTitulo ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obISelectAnoCompetencia->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes(array($obRdoNormal,$obRdoRetificadora));
$obFormulario->addSpan($obSpnDataRetificacao);
$obFormulario->addHidden($obHdnDataRetificacao,true);
$obFormulario->addComponente($obDtGeracaoArquivo);
$obFormulario->ok(true);
// $obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

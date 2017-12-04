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
    * Página de Formulário do Exportação Remessa Banpara
    * Data de Criação: 10/04/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FLExportarRemessaBanPara.php 31278 2008-07-21 12:13:37Z alex $

    * Casos de uso: uc-04.08.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaEmpresa.class.php"                        );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanpara.class.php"                               );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaBanPara";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$jsOnload = "montaParametrosGET('gerarSpan','stSituacao');";
$stAcao   = $_REQUEST["stAcao"];

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");
$obHdnTipoFiltroExtra->setValue("eval(document.frm.hdnTipoFiltro.value);");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick(" if (Valida()) { montaParametrosGET('submeter','stSituacao,inCodMes,inAno,inCodConfiguracao,inCodComplementar,stDesdobramento,stTipoFiltro,inCodAtributo,inCodEmpresa,inCodOrgaosSelecionados',true); }");

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparForm');");

$obComboSituacao = new Select;
$obComboSituacao->setRotulo                         ( "Cadastro"                                            );
$obComboSituacao->setTitle                          ( "Selecione o cadastro para filtro."                   );
$obComboSituacao->setName                           ( "stSituacao"                                          );
$obComboSituacao->setValue                          ( 'ativos'                                              );
$obComboSituacao->setStyle                          ( "width: 200px"                                        );
$obComboSituacao->addOption                         ( "", "Selecione"                                       );
$obComboSituacao->addOption                         ( "ativos", "Ativos"                                    );
$obComboSituacao->addOption                         ( "aposentados", "Aposentados"                          );
$obComboSituacao->addOption                         ( "pensionistas", "Pensionistas"                        );
$obComboSituacao->addOption                         ( "estagiarios", "Estagiários"                          );
$obComboSituacao->addOption                         ( "rescindidos", "Rescindidos"                          );
$obComboSituacao->addOption                         ( "pensao_judicial", "Pensão Judicial"                  );
$obComboSituacao->setNull                           ( false                                                 );
$obComboSituacao->obEvento->setOnChange("montaParametrosGET('gerarSpan','stSituacao');");

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

$obSpnAtivosAposentadosPensionistas = new Span();
$obSpnAtivosAposentadosPensionistas->setId("spnAtivosAposentadosPensionistas");

$obMonValorLiquidoInicial = new Moeda();
$obMonValorLiquidoInicial->setRotulo("Filtrar Valores Líquidos de:");
$obMonValorLiquidoInicial->setName("nuValorLiquidoInicial");
$obMonValorLiquidoInicial->setTitle("Informe a faixa de salários líquidos que deverão ser considerados no arquivo.");
$obMonValorLiquidoInicial->setValue("0,00");
$obMonValorLiquidoInicial->obEvento->setOnChange("montaParametrosGET('validarValores','nuValorLiquidoInicial,nuValorLiquidoFinal');");

$obLblAte = new Label();
$obLblAte->setValue("Até");

$obMonValorLiquidoFinal = new Moeda();
$obMonValorLiquidoFinal->setName("nuValorLiquidoFinal");
$obMonValorLiquidoFinal->setValue("99.999.999,99");
$obMonValorLiquidoFinal->obEvento->setOnChange("montaParametrosGET('validarValores','nuValorLiquidoInicial,nuValorLiquidoFinal');");

$arFiltrarValores = array($obMonValorLiquidoInicial,$obLblAte,$obMonValorLiquidoFinal);

$obPercentual = new Numerico();
$obPercentual->setRotulo("Percentual à Pagar do Líquido:");
$obPercentual->setName("nuPercentualPagar");
$obPercentual->setTitle("Informe o percentual à pagar do salário líquido de cada servidor.");
$obPercentual->setSize(10);
$obPercentual->setMaxLength(6);
$obPercentual->setValue("100,00");

$obLblPercentual = new Label();
$obLblPercentual->setValue("%");

$arPercentual = array($obPercentual,$obLblPercentual);

$obDtGeracaoArquivo = new Data();
$obDtGeracaoArquivo->setRotulo("Data da Geração Arquivo");
$obDtGeracaoArquivo->setName("dtGeracaoArquivo");
$obDtGeracaoArquivo->setTitle("Informar a data de geração do arquivo.");
$obDtGeracaoArquivo->setNull(false);
$obDtGeracaoArquivo->setValue(date('d/m/Y'));

$obDtPagamento = new Data();
$obDtPagamento->setRotulo("Data do Pagamento");
$obDtPagamento->setName("dtPagamento");
$obDtPagamento->setTitle("Informar a data provável de pagamento.");
$obDtPagamento->setNull(false);
$obDtPagamento->setValue(date('d/m/Y'));
$obDtPagamento->obEvento->setOnChange("montaParametrosGET('validarDataPagamento','dtPagamento,dtGeracaoArquivo')");

$obCmbTipoPagamento = new Select();
$obCmbTipoPagamento->setRotulo("Tipo de Pagamento");
$obCmbTipoPagamento->setTitle("Selecionar o tipo de pagamento do arquivo");
$obCmbTipoPagamento->setName("stTipoPagamento");
$obCmbTipoPagamento->addOption("00", "00 - Normal");
$obCmbTipoPagamento->addOption("01", "01 - Eventual");
$obCmbTipoPagamento->setNull(false);

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

$stOrdem = " ORDER BY cod_empresa DESC LIMIT 1";
$obTIMAConfiguracaoBanparaEmpresa = new TIMAConfiguracaoBanparaEmpresa();
$obTIMAConfiguracaoBanparaEmpresa->setDado('cod_periodo_movimentacao', $rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'));
$obTIMAConfiguracaoBanparaEmpresa->recuperaTodos($rsBanparaEmpresa, $stFiltro="", $stOrdem);
$inCodEmpresa = $rsBanparaEmpresa->getCampo('cod_empresa');

$obHdnCodEmpresa = new hidden();
$obHdnCodEmpresa->setName('inCodEmpresa');
$obHdnCodEmpresa->setValue($inCodEmpresa);

$obSpanGrupoOrgaos = new Span();
$obSpanGrupoOrgaos->setId('spanGrupoOrgaos');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnTipoFiltroExtra,true											);
$obFormulario->addComponente($obComboSituacao);
$obFormulario->addSpan($obSpnCadastro);
$obFormulario->addSpan($obSpnAtivosAposentadosPensionistas);
$obFormulario->addHidden($obHdnCodEmpresa);
$obFormulario->addSpan($obSpanGrupoOrgaos);
$obFormulario->addTitulo                        ( "Informações Gerais para emissão do arquivo" ,"left"                  );
$obFormulario->agrupaComponentes($arFiltrarValores);
$obFormulario->agrupaComponentes($arPercentual);
$obFormulario->addComponente($obDtGeracaoArquivo);
$obFormulario->addComponente($obDtPagamento);
$obFormulario->addComponente($obCmbTipoPagamento);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

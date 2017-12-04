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
    * Página de Filtro do Comprovante Rendimento IRRF
    * Data de Criação: 22/11/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FLComprovanteRendimentosIRRF.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.05.37
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectAnoCompetencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ComprovanteRendimentosIRRF";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");
$obHdnTipoFiltroExtra->setValue("eval(document.frm.hdnTipoFiltro.value);");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','stSituacao,inCodAtributo,inAnoCompetencia,inNumCGMResponsavel',true);");

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparForm');");

$obChkAtivos = new Radio();
$obChkAtivos->setName("stSituacao");
$obChkAtivos->setRotulo("Cadastro");
$obChkAtivos->setLabel("Ativos");
$obChkAtivos->setValue("ativo");
$obChkAtivos->setNull(false);
$obChkAtivos->setChecked(true);
$obChkAtivos->setTitle("Selecione o tipo de cadastro para emissão do comprovante de rendimentos");
$obChkAtivos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkAposentados = new Radio();
$obChkAposentados->setName("stSituacao");
$obChkAposentados->setLabel("Aposentados");
$obChkAposentados->setValue("aposentado");
$obChkAposentados->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkPensionistas = new Radio();
$obChkPensionistas->setName("stSituacao");
$obChkPensionistas->setLabel("Pensionistas");
$obChkPensionistas->setValue("pensionista");
$obChkPensionistas->obEvento->setOnChange("montaParametrosGET('gerarSpanPensionistas','stSituacao');");

$obChkRescindidos = new Radio();
$obChkRescindidos->setName("stSituacao");
$obChkRescindidos->setLabel("Rescindidos");
$obChkRescindidos->setValue("rescindido");
$obChkRescindidos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkTodos = new Radio();
$obChkTodos->setName("stSituacao");
$obChkTodos->setLabel("Todos");
$obChkTodos->setValue("todos");
$obChkTodos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$arChkCadastro = array($obChkAtivos,$obChkRescindidos,$obChkAposentados,$obChkPensionistas,$obChkTodos);

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

$obISelectAnoCompetencia = new ISelectAnoCompetencia();
$obISelectAnoCompetencia->obCmbAnoCompetencia->setRotulo("Ano Calendário");
$obISelectAnoCompetencia->obCmbAnoCompetencia->setTitle("Selecione o ano calendário para a emissão do comprovante de Rendimentos");
$obISelectAnoCompetencia->obCmbAnoCompetencia->setNull(false);

$rsAnoCompetencia = $obISelectAnoCompetencia->getRecordSet();
if ($rsAnoCompetencia->getNumLinhas() > 1) {
    $rsAnoCompetencia->proximo();
    $obISelectAnoCompetencia->setValue($rsAnoCompetencia->getCampo('ano'));
}

$obChkCompRetencao = new Radio();
$obChkCompRetencao->setName("stComprovantes");
$obChkCompRetencao->setRotulo("Comprovantes");
$obChkCompRetencao->setLabel("Todos");
$obChkCompRetencao->setValue("todos");
$obChkCompRetencao->setNull(false);
$obChkCompRetencao->setChecked(true);
$obChkCompRetencao->setTitle("Marque uma das opções: para emitir apenas comprovantes de rendimentos de servidores que tiveram alguma retenção de IRRF no Ano Calendário ou Todos");

$obChkCompTodos = new Radio();
$obChkCompTodos->setName("stComprovantes");
$obChkCompTodos->setLabel("Somente com retenção de IRRF");
$obChkCompTodos->setValue("somente_retencao_irrf");

$arChkComprovantes = array($obChkCompRetencao,$obChkCompTodos);

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo           ( "Responsável pelas informações");
$obBscCGM->setTitle            ( "CGM do Responsável pelas informações contidas no comprovante de rendimentos. Deve estar cadastrado no CGM.");
$obBscCGM->setNull             ( false                      );
$obBscCGM->setId               ( "inCampoInnerResponsavel"  );
$obBscCGM->obCampoCod->setName ( "inNumCGMResponsavel"      );
$obBscCGM->obCampoCod->setId   ( "inNumCGMResponsavel"      );
$obBscCGM->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaCGM','inNumCGMResponsavel');");
$obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgm.php','frm','inNumCGMResponsavel','inCampoInnerResponsavel','fisica','".Sessao::getId()."&inFiltro=2','800','550')" );

$obChkAgruparMatriculas = new Checkbox();
$obChkAgruparMatriculas->setName("boSomarMatriculas");
$obChkAgruparMatriculas->setRotulo("Somar Matrículas do Servidor");
$obChkAgruparMatriculas->setTitle("Somar valores de matrículas de mesmo servidor. Quando selecionada a opção 'Agrupar', soma as matrículas de mesmo servidor em um mesmo agrupamento.");
$obChkAgruparMatriculas->setValue("true");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnTipoFiltroExtra,true											);
$obISelectAnoCompetencia->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes($arChkCadastro);
$obFormulario->addSpan($obSpnCadastro);
$obFormulario->addComponente($obBscCGM);
$obFormulario->agrupaComponentes($arChkComprovantes);
$obFormulario->addComponente($obChkAgruparMatriculas);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

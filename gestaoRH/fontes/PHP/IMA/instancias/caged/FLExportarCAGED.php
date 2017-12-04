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
    * Arquivo de Filtro para exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: FLExportarCAGED.php 30829 2008-07-07 19:59:54Z alex $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$jsOnload = "executaFuncaoAjax('gerarSpanFiltro');";

//Define o nome dos arquivos PHP
$stPrograma = "ExportarCAGED";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::write('link', '');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                           );
$obForm->setTarget("oculto");

$obRdoMovimentoMensal = new Radio();
$obRdoMovimentoMensal->setRotulo("Tipo de Emissão");
$obRdoMovimentoMensal->setLabel("Movimento Mensal");
$obRdoMovimentoMensal->setTitle("Marque a opção de movimento mensal, para informações da competência ou acerto, para enviar acertos de movimentações de competências anteriores.");
$obRdoMovimentoMensal->setValue("movimento");
$obRdoMovimentoMensal->setName("stTipoEmissao");
$obRdoMovimentoMensal->setNull(false);
$obRdoMovimentoMensal->setChecked(true);
$obRdoMovimentoMensal->obEvento->setOnChange("montaParametrosGET('gerarSpanFiltro','stTipoEmissao');");

$obRdoAcerto = new Radio();
$obRdoAcerto->setRotulo("Tipo de Emissão");
$obRdoAcerto->setLabel("Acerto (meses anteriores)");
$obRdoAcerto->setTitle("Marque a opção de movimento mensal, para informações da competência ou acerto, para enviar acertos de movimentações de competências anteriores.");
$obRdoAcerto->setValue("acerto");
$obRdoAcerto->setName("stTipoEmissao");
$obRdoAcerto->setNull(false);
$obRdoAcerto->obEvento->setOnChange("montaParametrosGET('gerarSpanFiltro','stTipoEmissao');");

$obSpnFiltro = new Span();
$obSpnFiltro->setId("spnFiltro");

$obHdnFiltro = new HiddenEval();
$obHdnFiltro->setName("hdnFiltro");

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
$arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));

include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
$obIFiltroCompetencia = new IFiltroCompetencia();

$obCkbAtualizarDados = new Checkbox();
$obCkbAtualizarDados->setRotulo("Atualizar Dados Cadastrais do Autorizado/Entidade?");
$obCkbAtualizarDados->setName("boAtualizarDados");
$obCkbAtualizarDados->setValue(true);
$obCkbAtualizarDados->setLabel("Sim");

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("BloqueiaFrames(true,false);Salvar();");

$obBtnLimpar = new Limpar();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->addTitulo("Dados de Emissão do Arquivo");
$obFormulario->addComponenteComposto($obRdoMovimentoMensal,$obRdoAcerto);
$obFormulario->addTitulo("Seleção do Filtro");
$obFormulario->addSpan($obSpnFiltro);
$obFormulario->addHidden($obHdnFiltro,true);
$obIFiltroCompetencia->geraFormulario($obFormulario);
$obFormulario->addComponente($obCkbAtualizarDados);
// $obFormulario->ok();
$obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

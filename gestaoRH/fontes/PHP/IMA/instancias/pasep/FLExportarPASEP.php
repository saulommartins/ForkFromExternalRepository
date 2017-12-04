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
    * Data de Criação: 29/10/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.23

    $Id: FLExportarPASEP.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();
Sessao::write("dtCompetenciaPASEP",$obRFolhaPagamentoFolhaSituacao->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal());

//Define o nome dos arquivos PHP
$stPrograma = "ExportarPASEP";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::write('link', '');
$stAcao = $request->get('stAcao');

// Verificando se possui configuração de PASEP
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoPasep.class.php");
$obTIMAConfiguracaoPasep = new TIMAConfiguracaoPasep;
$obTIMAConfiguracaoPasep->recuperaRelacionamento($rsPasep);

$stMensagem = "Deve ser realizado a configuração do PASEP para poder realizar a geração do arquivo.";
$obLblMensagem = new Label;
$obLblMensagem->setRotulo( "Atenção"   );
$obLblMensagem->setValue ( $stMensagem );

if ($rsPasep->getNumLinhas() != -1) {
    $jsOnLoad = "executaFuncaoAjax('montarSpanFiltro');";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                           );
$obForm->setTarget("oculto");
$obForm->setEncType     ( "multipart/form-data" );

$obRdoPartAPagar = new Radio();
$obRdoPartAPagar->setRotulo("Etapa de Processamento");
$obRdoPartAPagar->setName("inEtapaProcessamento");
$obRdoPartAPagar->setValue(1);
$obRdoPartAPagar->setLabel("1-Lista de Participantes a Pagar (Exportar FPS900)<br>");
$obRdoPartAPagar->setNull(false);
$obRdoPartAPagar->setChecked(true);
$obRdoPartAPagar->obEvento->setOnChange("montaParametrosGET('montarSpanFiltro','inEtapaProcessamento')");

$obRdoRejeiFPS909 = new Radio();
$obRdoRejeiFPS909->setRotulo("Etapa de Processamento");
$obRdoRejeiFPS909->setName("inEtapaProcessamento");
$obRdoRejeiFPS909->setValue(2);
$obRdoRejeiFPS909->setLabel("2-Rejeitados do FPS900 (Importar FPS909)<br>");
$obRdoRejeiFPS909->setNull(false);
$obRdoRejeiFPS909->obEvento->setOnChange("montaParametrosGET('montarSpanFiltro','inEtapaProcessamento')");

$obRdoRetorFPS900 = new Radio();
$obRdoRetorFPS900->setRotulo("Etapa de Processamento");
$obRdoRetorFPS900->setName("inEtapaProcessamento");
$obRdoRetorFPS900->setValue(3);
$obRdoRetorFPS900->setLabel("3-Retorno do FPS900 - Valores a Lançar na Folha (Importar FPS910)<br>");
$obRdoRetorFPS900->setNull(false);
$obRdoRetorFPS900->obEvento->setOnChange("montaParametrosGET('montarSpanFiltro','inEtapaProcessamento')");

$obRdoPartNPagos = new Radio();
$obRdoPartNPagos->setRotulo("Etapa de Processamento");
$obRdoPartNPagos->setName("inEtapaProcessamento");
$obRdoPartNPagos->setValue(4);
$obRdoPartNPagos->setLabel("4-Lista de Participantes não Pagos na Folha (Exportar FPS950)<br>");
$obRdoPartNPagos->setNull(false);
$obRdoPartNPagos->obEvento->setOnChange("montaParametrosGET('montarSpanFiltro','inEtapaProcessamento')");

$obRdoRejeiFPS900 = new Radio();
$obRdoRejeiFPS900->setRotulo("Etapa de Processamento");
$obRdoRejeiFPS900->setName("inEtapaProcessamento");
$obRdoRejeiFPS900->setValue(5);
$obRdoRejeiFPS900->setLabel("5-Rejeitados do FPS900 (Importar FPS959)<br>");
$obRdoRejeiFPS900->setNull(false);
$obRdoRejeiFPS900->obEvento->setOnChange("montaParametrosGET('montarSpanFiltro','inEtapaProcessamento')");

$obRdoRetorFPS950 = new Radio();
$obRdoRetorFPS950->setRotulo("Etapa de Processamento");
$obRdoRetorFPS950->setName("inEtapaProcessamento");
$obRdoRetorFPS950->setValue(6);
$obRdoRetorFPS950->setLabel("6-Retorno Definitivo do FPS950 (Importar FPS952)");
$obRdoRetorFPS950->setNull(false);
$obRdoRetorFPS950->obEvento->setOnChange("montaParametrosGET('montarSpanFiltro','inEtapaProcessamento')");

$arEtapaProcessamento = array($obRdoPartAPagar,$obRdoRejeiFPS909,$obRdoRetorFPS900,$obRdoPartNPagos,$obRdoRejeiFPS900,$obRdoRetorFPS950);

$obSpnEtapaProcessamento = new Span();
$obSpnEtapaProcessamento->setId("spnEtapaProcessamento");

$obHdnEtapaProcessamento = new HiddenEval();
$obHdnEtapaProcessamento->setName("hdnEtapaProcessamento");
$obHdnEtapaProcessamento->setId("hdnEtapaProcessamento");

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','inEtapaProcessamento,inCodTipoFolha',true);");

$obBtnLimpar = new Limpar();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo($stTitulo ,"right");
$obFormulario->addForm($obForm);
if ($rsPasep->getNumLinhas() != -1) {
    $obFormulario->addHidden($obHdnAcao);
    $obFormulario->agrupaComponentes($arEtapaProcessamento);
    $obFormulario->addSpan($obSpnEtapaProcessamento);
    $obFormulario->addHidden($obHdnEtapaProcessamento,true);
    $obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
} else {
    $obFormulario->addComponente($obLblMensagem);
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

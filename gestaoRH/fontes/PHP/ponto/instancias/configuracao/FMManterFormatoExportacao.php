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
/*
 * Fornulário para Configuração Formato de Exportação
 * Data de Criação   : 21/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = "ManterFormatoExportacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
sessao::write("stAcao",$stAcao);
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

Sessao::remove("arDadosExportacao");

$jsOnLoad = "executaFuncaoAjax('onLoad');";

if ($stAcao == "alterar") {
    sessao::write("inCodFormato",$_GET["inCodFormato"]);
    $stLocation = $pgList;
    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoExportacao.class.php");
    $obTPontoFormatoExportacao = new TPontoFormatoExportacao();

    $obTPontoFormatoExportacao->setDado("cod_formato",$_GET["inCodFormato"]);
    $obTPontoFormatoExportacao->recuperaPorChave($rsFormatoExportacao);
    $inCodFormato = $rsFormatoExportacao->getCampo("cod_formato");
    $stDescricao = $rsFormatoExportacao->getCampo("descricao");
    $stFormatoMinutos = $rsFormatoExportacao->getCampo("formato_minutos");
}

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $_REQUEST["stAcao"]                                                   );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

$obHdnCodFormato =  new Hidden;
$obHdnCodFormato->setName("inCodFormato");
$obHdnCodFormato->setValue($inCodFormato);

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obTxtDescricao = new TextBox();
$obTxtDescricao->setName('stDescricao');
$obTxtDescricao->setId('stDescricao');
$obTxtDescricao->setValue($stDescricao);
$obTxtDescricao->setRotulo('Descrição');
$obTxtDescricao->setTitle('Informe a descrição do formato de exportação.');
$obTxtDescricao->setNull(false);
$obTxtDescricao->setSize('60');
$obTxtDescricao->setMaxLength('60');

$obRdoDecimal = new Radio();
$obRdoDecimal->setRotulo("Formato dos Minutos");
$obRdoDecimal->setName("stFormatoMinutos");
$obRdoDecimal->setId("stFormatoMinutos");
$obRdoDecimal->setLabel("Decimal");
$obRdoDecimal->setValue("D");
$obRdoDecimal->setTitle("Selecione o formato dos minutos da hora, em formato decimal ou hora. Exemplo: 10hs e 50 minutos. Em formato decimal ficaria: 83 minutos, em formato de hora ficaria 50 minutos.");
$obRdoDecimal->setNull(false);
$obRdoDecimal->setChecked(($stFormatoMinutos == "D" || $stFormatoMinutos == "") ? true : false);

$obRdoHora = new Radio();
$obRdoHora->setRotulo("Limites de Tolerância");
$obRdoHora->setName("stFormatoMinutos");
$obRdoHora->setId("stFormatoMinutos");
$obRdoHora->setLabel("Hora");
$obRdoHora->setValue("H");
$obRdoHora->setTitle("Selecione o formato dos minutos da hora, em formato decimal ou hora. Exemplo: 10hs e 50 minutos. Em formato decimal ficaria: 83 minutos, em formato de hora ficaria 50 minutos.");
$obRdoHora->setNull(false);
$obRdoHora->setChecked(($stFormatoMinutos == "H") ? true : false);

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoTipoInformacao.class.php");
$obTPontoTipoInformacao = new TPontoTipoInformacao();
$obTPontoTipoInformacao->recuperaTodos($rsTipoInformacao);
$obCmbInformacaoPonto = new Select();
$obCmbInformacaoPonto->setRotulo("Informação do Relógio Ponto");
$obCmbInformacaoPonto->setName("inCodTipo");
$obCmbInformacaoPonto->setId("inCodTipo");
$obCmbInformacaoPonto->setValue($inCodTipo);
$obCmbInformacaoPonto->addOption('','Selecione');
$obCmbInformacaoPonto->setCampoId("cod_tipo");
$obCmbInformacaoPonto->setCampoDesc("descricao");
$obCmbInformacaoPonto->preencheCombo($rsTipoInformacao);
$obCmbInformacaoPonto->setNullBarra(false);
$obCmbInformacaoPonto->setTitle("Selecione um item de informação do relógio ponto para exportação.");
$obCmbInformacaoPonto->obEvento->setOnChange("montaParametrosGET('gerarSpansExportar','inCodTipo')");

$obSpnInformacaoPonto = new Span();
$obSpnInformacaoPonto->setId("spnInformacaoPonto");

include_once(CAM_GRH_FOL_COMPONENTES."IBuscaInnerEvento.class.php");
$obIBuscaInnerEvento = new IBuscaInnerEvento();
$obIBuscaInnerEvento->setRotulo("Código do Evento à Exportar");
$obIBuscaInnerEvento->setNaturezasDesconto();
$obIBuscaInnerEvento->setNaturezasProvento();
$obIBuscaInnerEvento->setNaturezasInformativo();
$obIBuscaInnerEvento->setNaturezaChecked('P');
$obIBuscaInnerEvento->setEventoSistema(false);
$obIBuscaInnerEvento->montaOnChange();
$obIBuscaInnerEvento->montaPopUp();
$obIBuscaInnerEvento->setNullBarra(false);
$obIBuscaInnerEvento->obCampoCod->setNullBarra(false);

$arComponentes = array($obCmbInformacaoPonto,$obIBuscaInnerEvento,);

$obSpnConfiguracao = new Span();
$obSpnConfiguracao->setId("spnConfiguracao");

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnCodFormato);
$obFormulario->addTitulo($obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right");
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Configuração dos Formatos de Exportação");
$obFormulario->addComponente($obTxtDescricao);
$obFormulario->agrupaComponentes(array($obRdoDecimal,$obRdoHora));
$obFormulario->addTitulo("Dados a Exportar");
$obFormulario->addComponente($obCmbInformacaoPonto);
$obFormulario->addSpan($obSpnInformacaoPonto);
$obFormulario->addComponente($obIBuscaInnerEvento);
$obFormulario->incluirAlterar('Configuracao',$arComponentes,true);
$obFormulario->addSpan($obSpnConfiguracao);
if ($stAcao == "incluir") {
    $obFormulario->ok();
} else {
    $obFormulario->cancelar($stLocation);
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

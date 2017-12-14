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
    * Formulário que Inicia o Processo Fiscal e insere os documentos para o mesmo
    * Data de Criação: 25/07/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GT_FIS_NEGOCIO."RFISIniciarProcessoFiscal.class.php");
require_once(CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php");
include_once(CAM_GT_FIS_INSTANCIAS."processoFiscal/JSEmitirDocumento.php");

//Instanciando a Classe de Controle e de Visao
$obController = new RFISIniciarProcessoFiscal;
$obVisao = new VFISIniciarProcessoFiscal($obController);

$stPrograma = "IniciarProcessoFiscal";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".php";

include_once($pgJs);
Sessao::write('arDocumentos', array());

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Switch que monta a pesquisa de acordo com o Tipo de Fiscalização
switch ($_REQUEST['inTipoFiscalizacao']) {
    case 1:
        $_REQUEST['inInscricaoEconomica'] = $_REQUEST['inInscricao'];
        //Filtros da pesquisa.
        $where = $obVisao->filtrosProcessoFiscal($_REQUEST);
        $obRsProcesso = $obVisao->iniciarProcessoFiscalEconomica($where);
        $inInscricaoEconomica = $obRsProcesso->arElementos[0]['inscricao'];
        $inCodAtividade = $obRsProcesso->arElementos[0]['cod_atividade'];
    break;

    case 2:
        $_REQUEST['inCodImovel'] = $_REQUEST['inInscricao'];
        //Filtros da pesquisa.
        $where = $obVisao->filtrosProcessoFiscal($_REQUEST);
        $obRsProcesso = $obVisao->iniciarProcessoFiscalObra($where);
        $inInscricaoMunicipal = $obRsProcesso->arElementos[0]['inscricao'];
    break;
}

//Valores da Regra de Negócio
$stTipoFiscalizacao = $obRsProcesso->arElementos[0]['cod_tipo']. " - " . $obRsProcesso->arElementos[0]['descricao'];
$inProcessoFiscal = $obRsProcesso->arElementos[0]['cod_processo'];
$stFundamentacaoLegal = $obRsProcesso->arElementos[0]['cod_processo_protocolo']. "/" . $obRsProcesso->arElementos[0]['ano_exercicio'];
$inCodGrupo = $obRsProcesso->arElementos[0]['cod_grupo'];
$inSequencia = $obRsProcesso->arElementos[0]['sequencia'];

//Cria um novo formulario
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

#### Campos Hidden ####

//stAcao
$obHdnAcao = new Hidden();
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

//stCtrl
$obHdnCtrl = new Hidden();
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("iniciarProcessoFiscal");

//Cod. Tipo Fiscalização
$obHdnInTipoFiscalizacao = new Hidden();
$obHdnInTipoFiscalizacao->setName("inTipoFiscalizacao");
$obHdnInTipoFiscalizacao->setValue($_REQUEST["inTipoFiscalizacao"]);

//Cod. Processo Fiscal
$obHdnInCodProcesso = new Hidden();
$obHdnInCodProcesso->setName("inCodProcesso");
$obHdnInCodProcesso->setValue($_REQUEST["inCodProcesso"]);

//Cod. Fiscal
$obHdnInCodFiscal = new Hidden();
$obHdnInCodFiscal->setName("inCodFiscal");
$obHdnInCodFiscal->setValue($_REQUEST["inCodFiscal"]);

//Cod. Atividade
if ($inCodAtividade) {
    $obHdnInCodAtividade = new Hidden();
    $obHdnInCodAtividade->setName("inCodAtividade");
    $obHdnInCodAtividade->setValue($inCodAtividade);
}

//Sequencia
$obHdnInSequencia = new Hidden();
$obHdnInSequencia->setName("inSequencia");
$obHdnInSequencia->setValue($inSequencia);

//Tipo Fiscalização
$obTipoFiscalizacao = new Label();
$obTipoFiscalizacao->setRotulo("Tipo de Fiscalização");
$obTipoFiscalizacao->setName("stTipoFiscalizacao");
$obTipoFiscalizacao->setValue($stTipoFiscalizacao);

//Processo Fiscal
$obProcessoFiscal = new Label();
$obProcessoFiscal->setRotulo("Processo Fiscal");
$obProcessoFiscal->setName("inProcessoFiscal");
$obProcessoFiscal->setValue($inProcessoFiscal);

//Fundamentação Legal
$obFundamentacaoLegal = new Label();
$obFundamentacaoLegal->setRotulo("Fundamentação Legal");
$obFundamentacaoLegal->setName("stFundamentacaoLegal");
$obFundamentacaoLegal->setValue($stFundamentacaoLegal);

//Inscricao Economica
$obInscricaoEconomica = new Label();
$obInscricaoEconomica->setRotulo("Inscrição Econômica");
$obInscricaoEconomica->setName("stInscricaoEconomica");
$obInscricaoEconomica->setValue($inInscricaoEconomica);

//Inscricao Municipal
$obInscricaoMunicipal = new Label();
$obInscricaoMunicipal->setRotulo("Inscrição Municipal");
$obInscricaoMunicipal->setName("stInscricaoMunicipal");
$obInscricaoMunicipal->setValue($inInscricaoMunicipal);

//Monta o formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnInTipoFiscalizacao);
$obFormulario->addHidden($obHdnInCodProcesso);
$obFormulario->addHidden($obHdnInCodFiscal);
$obFormulario->addHidden($obHdnInSequencia);

if ($inCodAtividade) {
    $obFormulario->addHidden($obHdnInCodAtividade);
}

//Inscrição
switch ($_REQUEST['inTipoFiscalizacao']) {
    case "1"://Econômica
        $obHdnInInscricaoEconomica = new Hidden();
        $obHdnInInscricaoEconomica->setName("inIncricaoEconomica");
        $obHdnInInscricaoEconomica->setValue($_REQUEST["inInscricao"]);
        $obFormulario->addHidden($obHdnInInscricaoEconomica);
    break;

    case "2"://Municipal
        $obHdnInInscricaoMunicipal = new Hidden();
        $obHdnInInscricaoMunicipal->setName("inInscricaoMunicipal");
        $obHdnInInscricaoMunicipal->setValue($_REQUEST["inInscricao"]);
        $obFormulario->addHidden($obHdnInInscricaoMunicipal);
    break;
}

$obFormulario->addTitulo("Dados para Início de Processo Fiscal");
$obFormulario->addComponente($obTipoFiscalizacao);
$obFormulario->addComponente($obProcessoFiscal);

//Inscrição
switch ($_REQUEST['inTipoFiscalizacao']) {
    case "1"://Econômica
        $obFormulario->addComponente($obInscricaoEconomica);
        $obFormulario->addComponente($obFundamentacaoLegal);

        $stFiltro2 = " pf.cod_tipo = " .$_REQUEST['inTipoFiscalizacao']. "\n";
        $stFiltro2 .= " AND pf.cod_processo = " .$_REQUEST['inCodProcesso']. "\n";

        //Tabela Lista de Créditos / Grupos de Créditos
        $tableListaCredito = new Table();
        $tableListaCredito->setRecordset($obVisao->iniciarProcessoFiscalDocumento($stFiltro2));
        $tableListaCredito->setSummary("Lista de Créditos / Grupos de Créditos");
        //$tableListaCredito->setConditional(true , "#ddd");
        $tableListaCredito->Head->addCabecalho('Código', 30,'');
        $tableListaCredito->Head->addCabecalho('Descrição', 50,'');
        $tableListaCredito->Body->addCampo('codigo' , 'E','');
        $tableListaCredito->Body->addCampo('descricao', 'E','');
        $tableListaCredito->montaHTML();

        //Span
        $obSpanListaCredito = new Span;
        $obSpanListaCredito->setValue($tableListaCredito->getHtml());
        $obFormulario->addSpan($obSpanListaCredito);
    break;

    case "2"://Municipal
        $obFormulario->addComponente($obInscricaoMunicipal);
        $obFormulario->addComponente($obFundamentacaoLegal);
    break;
}

//Data de Início
$obDataInicio = new Data;
$obDataInicio->setName("dtDataInicio");
$obDataInicio->setId("dtDataInicio");
$obDataInicio->setSize("8");
$obDataInicio->setRotulo("Data de Início");
$obDataInicio->setTitle("Informe a Data de Início do Processo Fiscal.");
$obDataInicio->setNull(false);

//Local de Entrega
$obLocalEntrega = new TextBox;
$obLocalEntrega->setName("stLocalEntrega");
$obLocalEntrega->setId("stLocalEntrega");
$obLocalEntrega->setSize("80");
$obLocalEntrega->setMaxLength("120");
$obLocalEntrega->setRotulo("Local de Entrega");
$obLocalEntrega->setTitle("Informe um Local de Entrega do Processo Fiscal.");
$obLocalEntrega->setNull(false);

//Prazo para Entrega
$obPrazoEntrega = new Data;
$obPrazoEntrega->setName("dtPrazoEntrega");
$obPrazoEntrega->setId("dtPrazoEntrega");
$obPrazoEntrega->setSize("8");
$obPrazoEntrega->setRotulo("Prazo para Entrega");
$obPrazoEntrega->setTitle("Informe um Prazo para Entrega.");
$obPrazoEntrega->setNull(false);

//Observações
$obObservacao = new Textarea;
$obObservacao->setName("stObservacao");
$obObservacao->setId("stObservacao");
$obObservacao->setRotulo("Observações");
$obObservacao->setTitle("Informe as Observações para o Início do Processo Fiscal.");
$obObservacao->setNull(false);

//Termo de Inicio
$obTermoInicio = new ITextBoxSelectDocumento;
$obTermoInicio->setCodAcao(substr($_SESSION["acao"], 5,-2)) ;
$obTermoInicio->obTextBoxSelectDocumento->setNull(false);
$obTermoInicio->obTextBoxSelectDocumento->setRotulo("Termo de Início");
$obTermoInicio->obTextBoxSelectDocumento->setName("stCodDocumento");
$obTermoInicio->obTextBoxSelectDocumento->setTitle("Selecione o Termo de Início.");
$obTermoInicio->obTextBoxSelectDocumento->obTextBox->setSize(10);
$obTermoInicio->obTextBoxSelectDocumento->obSelect->setStyle("width: 261px;");

//Tipo Documentos
$obTipoDocumento = new IFISTextBoxSelectDocumento($_REQUEST["inTipoFiscalizacao"]." and uso_interno = 'f' and ativo = 't'");
$obTipoDocumento->setNull(true);
$obTipoDocumento->setTitle("Selecione os Documentos.");
$obTipoDocumento->obTxtDocumento->setRotulo("*Documento");
$obTipoDocumento->obCmbDocumento->setRotulo("*Documento");
$obTipoDocumento->obTxtDocumento->setId("txtDocumentos");
$obTipoDocumento->obCmbDocumento->setId("cmbDocumentos");

//Botões de Inclusão e Limpar Documentos
$obBtnIncluirDocumento = new Button;
$obBtnIncluirDocumento->setName("btnIncluirDocumento");
$obBtnIncluirDocumento->setValue("Incluir");
$obBtnIncluirDocumento->setTipo("button");
$obBtnIncluirDocumento->obEvento->setOnClick("incluirDocumento();");
$obBtnIncluirDocumento->setDisabled(false);

$obBtnLimparDocumento = new Button;
$obBtnLimparDocumento->setName("btnLimparDocumento");
$obBtnLimparDocumento->setValue("Limpar");
$obBtnLimparDocumento->setTipo("button");
$obBtnLimparDocumento->obEvento->setOnClick("limparDocumento();");
$obBtnLimparDocumento->setDisabled(false);

//Span
$obSpanListaDocumento = new Span;
$obSpanListaDocumento->setId('spnDocumentos');

// Conclusão do formulário
$obBtnOK = new Ok();

$obBtnLimpar = new Limpar();

if ($inCodAtividade) {
    $obBtnLimpar->obEvento->setOnClick("LimparFormInicio();");
}

$arBotoes = array($obBtnOK, $obBtnLimpar);

$obFormulario->addComponente($obDataInicio);
$obFormulario->addComponente($obLocalEntrega);
$obFormulario->addComponente($obPrazoEntrega);
$obFormulario->addComponente($obObservacao);
$obTermoInicio->geraFormulario($obFormulario);
$obFormulario->addTitulo("Dados para Documentos");
$obTipoDocumento->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes(array($obBtnIncluirDocumento, $obBtnLimparDocumento));
$obFormulario->addSpan($obSpanListaDocumento);
$obFormulario->defineBarra($arBotoes, 'left', '<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;');
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

if ($inCodAtividade) {
    SistemaLegado::executaFrameOculto( "montaParametrosGET('montaListaDocumentosVinculados', '', true);" );
}
?>

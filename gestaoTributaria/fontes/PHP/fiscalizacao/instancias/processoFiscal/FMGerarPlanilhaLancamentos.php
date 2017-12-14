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
    * Formulário que Mostra os Dados do Relatório antes de Gerar a Planilha de Lançamentos
    * Data de Criação: 03/09/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes Pereira da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GT_FIS_NEGOCIO."RFISGerarPlanilhaLancamentos.class.php");
require_once(CAM_GT_FIS_VISAO."VFISGerarPlanilhaLancamentos.class.php");

//Instanciando a Classe de Controle e de Visao
$obController = new RFISGerarPlanilhaLancamentos;
$obVisao = new VFISGerarPlanilhaLancamentos($obController);

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Define o nome dos arquivos PHP
$stPrograma = "GerarPlanilhaLancamentos";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

include_once($pgJs);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$_REQUEST['inTipoFiscalizacao'] = $_REQUEST['stTipoFiscalizacao'];

$arCodDesc = explode('-', $_REQUEST['inCodProcessoInscricao']);

$_REQUEST['inCodProcesso'] = $arCodDesc[0];

//Filtros da pesquisa.
$where = $obVisao->filtrosPlanilhas($_REQUEST);
$obRsProcessoFiscalEndereco = $obVisao->recuperarEnderecoPlanilhaLancamentos($where);

$inInscricaoEconomica = $obRsProcessoFiscalEndereco->arElementos[0]['inscricao_economica'];
$inProcessoFiscal = $obRsProcessoFiscalEndereco->arElementos[0]['cod_processo'];
$inCodFiscal = $obRsProcessoFiscalEndereco->arElementos[0]['cod_fiscal'];
$stNomeEmpresa = $obRsProcessoFiscalEndereco->arElementos[0]['nom_cgm'];
$stEndereco = $obRsProcessoFiscalEndereco->arElementos[0]['endereco'];
$stAtividade = $obRsProcessoFiscalEndereco->arElementos[0]['nom_atividade'];

$obListaLancamento = $obVisao->montaPlanilhaLancamentos($_REQUEST);

//Cria um novo formulario
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

#### Campos Hidden ####

//stAcao
$obHdnAcao = new Hidden();
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

//stCtrlfif.cod_documento
$obHdnCtrl = new Hidden();
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("cadastrarLevantamentosFiscais");

//Hidden Cod. Tipo Fiscalização
$obHdnInTipoFiscalizacao = new Hidden();
$obHdnInTipoFiscalizacao->setName("inTipoFiscalizacao");
$obHdnInTipoFiscalizacao->setValue($_REQUEST["inTipoFiscalizacao"]);

//Hidden Cod. Processo Fiscal
$obHdnInCodProcesso = new Hidden();
$obHdnInCodProcesso->setName("inCodProcesso");
$obHdnInCodProcesso->setValue($inProcessoFiscal);

//Hidden Inscricao Economica
$obHdnInscricaoEconomica = new Hidden();
$obHdnInscricaoEconomica->setName("inInscricaoEconomica");
$obHdnInscricaoEconomica->setValue($inInscricaoEconomica);

//Inscricao Economica
$obInscricaoEconomica = new Label();
$obInscricaoEconomica->setRotulo("Inscrição");
$obInscricaoEconomica->setName("stInscricaoEconomica");
$obInscricaoEconomica->setValue($inInscricaoEconomica);
//print_r($inInscricaoEconomica);

//Nome Empresa
$obNomeEmpresa = new Label();
$obNomeEmpresa->setRotulo("Nome");
$obNomeEmpresa->setName("stNomeEmpresa");
$obNomeEmpresa->setValue($stNomeEmpresa);

//Endereço
$obEndereco = new Label();
$obEndereco->setRotulo("Endereço");
$obEndereco->setName("stEndereco");
$obEndereco->setValue($stEndereco);

//Atividade
$obAtividade = new Label();
$obAtividade->setRotulo("Atividade");
$obAtividade->setName("stAtividade");
$obAtividade->setValue($stAtividade);

//Span
$obSpanListaLancamentos = new Span;
$obSpanListaLancamentos->setValue($obListaLancamento);

//Monta o formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnInTipoFiscalizacao);
$obFormulario->addHidden($obHdnInCodProcesso);
$obFormulario->addHidden($obHdnInscricaoEconomica);
$obFormulario->addTitulo("Planilha de Levantamento de ISSQN");
$obFormulario->addComponente($obInscricaoEconomica);
$obFormulario->addComponente($obNomeEmpresa);
$obFormulario->addComponente($obEndereco);
$obFormulario->addComponente($obAtividade);
$obFormulario->addSpan($obSpanListaLancamentos);

if ($obVisao->getBoLevantamento()) {
    //$obFormulario->OK();
    $obBtnVoltar = new Button;
    $obBtnVoltar->setName('btnVoltar');
    $obBtnVoltar->setValue('Voltar');
    $obBtnVoltar->setTipo('button');
    $obBtnVoltar->obEvento->setOnClick("voltarLancamentos();");
    $obBtnVoltar->setDisabled(false);

    $obOK = new OK;
    $arBotoes[] = $obOK;
    $arBotoes[] = $obBtnVoltar;
    $obFormulario->defineBarra($arBotoes, 'left','');
} else {
    $obBtnVoltar = new Button;
    $obBtnVoltar->setName('btnVoltar');
    $obBtnVoltar->setValue('Voltar');
    $obBtnVoltar->setTipo('button');
    $obBtnVoltar->obEvento->setOnClick("voltarLancamentos();");
    $obBtnVoltar->setDisabled(false);

    $arBotoes[] = $obBtnVoltar;
    $obFormulario->defineBarra($arBotoes, 'left','');
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Página de Manter vinculo de atividade com documentos
    * Data de Criacao: 28/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GT_FIS_COMPONENTES."ITextBoxSelectTipoFiscalizacao.class.php");
include_once(CAM_GT_FIS_COMPONENTES."IFISTextBoxSelectDocumento.class.php");
include_once(CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php");

$stAcao = $request->get('stAcao');
Sessao::write('arValores', array());

if (empty($stAcao)) {
    $stAcao = "vincular";
}

//Define o nome dos arquivos

$stPrograma = "ManterVinculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once($pgJs);

Sessao::write('Atividades', array());

//Acao do Form
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Campos Hidden
$obHdnAcao =  new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue($_REQUEST['stCtrl']);

//Adciona Componente MontaAtividade
$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setTitle("Selecione todos os Níveis da Atividade");
$obMontaAtividade->setCadastroAtividade(false);

$obHdnCodigoAtividade = new Hidden;
$obHdnCodigoAtividade->setName("inCodigoAtividade");
$obHdnCodigoAtividade->setValue($_REQUEST["inCodigoAtividade"]);

//Tipo Fiscalizacao
$obTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao;
$obTipoFiscalizacao->setNull(true);
$obTipoFiscalizacao->setTitle("Informe o Tipo de Fiscalização.");
$obTipoFiscalizacao->obTxtTipoFiscalizacao->setRotulo  ('*Tipo Fiscalização');
$obTipoFiscalizacao->obTxtTipoFiscalizacao->setId("txtTipoFiscalizacao");
$obTipoFiscalizacao->obCmbTipoFiscalizacao->setId("cmbTipoFiscalizacao");

//Eventos
$obTipoFiscalizacao->obTxtTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('montaForm','cmbTipoFiscalizacao');");
$obTipoFiscalizacao->obCmbTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('montaForm','cmbTipoFiscalizacao');");

//SPAN ALOCADO PARA ADCIONAR O COMPONENTE IFISTEXTBOXSELECTDOCUMENTO
$obSpanDocumento = new Span;
$obSpanDocumento->setId('spnForm');

//SPAN ALOCADO PARA ADCIONAR A LISTA DE DOCUMENTOS VINCULADOS A ATIVIDADE
$obSpanListaDocumento = new Span;
$obSpanListaDocumento->setId('spnDocumentos');

$obBtnIncluirDocumento = new Button;
$obBtnIncluirDocumento->setName("btnIncluir");
$obBtnIncluirDocumento->setValue("Incluir");
$obBtnIncluirDocumento->setTipo("button");
$obBtnIncluirDocumento->obEvento->setOnClick("incluirDocumento();");
$obBtnIncluirDocumento->setDisabled(false);

$obBtnLimparDocumento = new Button;
$obBtnLimparDocumento->setName("btnLimpar");
$obBtnLimparDocumento->setValue("Limpar");
$obBtnLimparDocumento->setTipo("button");
$obBtnLimparDocumento->obEvento->setOnClick("limparCampo();");
$obBtnLimparDocumento->setDisabled(false);
$botoesSpanDocumento = array($obBtnIncluirDocumento, $obBtnLimparDocumento);

// Conclusão do formulário
$obBtnOK = new OK();

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick("LimparFormInicio();");
$obBtnLimpar->setDisabled(false);

$arBotoes = array($obBtnOK, $obBtnLimpar);

//MONTANDO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addTitulo("Dados para Vínculo");
$obMontaAtividade->geraFormulario($obFormulario);
$obFormulario->addTitulo("Dados para Documento");
$obTipoFiscalizacao->geraFormulario($obFormulario);
$obFormulario->addSpan($obSpanDocumento);
$obFormulario->defineBarra($botoesSpanDocumento, 'left', '');
$obFormulario->addSpan($obSpanListaDocumento);

//$obFormulario->Ok();
$obFormulario->defineBarra($arBotoes, 'left', '<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;');
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

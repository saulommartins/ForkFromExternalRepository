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
 * Página de Filtro para o Relatório de Retenções de Ordens de Pagamentos
 *
 * @category   Urbem
 * @package    Empenho
 * @ignore     Relatorio
 * @author     Analista Tonismar R. Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id:$
 * Casos de uso: uc-02.03.40
 */

/* includes do sistema */
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

/* includes de componentes */
include CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';
include CAM_GF_EMP_COMPONENTES.'IPopUpCredor.class.php';
include CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";

/* Monta caminhos */
$stPrograma = "RetencoesOrdemPagamento";
$pgOcul = "OC".$stPrograma.".php";
$pgGeraRel = "OCGeraRelatorio".$stPrograma.".php";

$obForm = new Form;
$obForm->setAction($pgGeraRel);
$obForm->setTarget("telaPrincipal");

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue("");

// Componente para filtrar as entidades
$obSelectMultiploEntidadeUsuario = new ISelectMultiploEntidadeUsuario;

// Monta o componente de Periodicidade
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(Sessao::getExercicio());
$obPeriodicidade->setNull     (false);

// Monta o intervalo de empenho
$obTxtEmpenhoInicio = new TextBox();
$obTxtEmpenhoInicio->setId    ("inCodEmpenhoInicial");
$obTxtEmpenhoInicio->setName  ("inCodEmpenhoInicial");
$obTxtEmpenhoInicio->setRotulo("Número do Empenho");
$obTxtEmpenhoInicio->setTitle ("Informe a faixa de números de empenho para o filtro.");

$obLblEmpenho = new Label();
$obLblEmpenho->setValue(" a ");

$obTxtEmpenhoFinal = new TextBox();
$obTxtEmpenhoFinal->setId    ("inCodEmpenhoFinal");
$obTxtEmpenhoFinal->setName  ("inCodEmpenhoFinal");
$obTxtEmpenhoFinal->setRotulo("Número do Empenho");

// Monta o intervalo de ordem de pagamento
$obTxtOrdemInicio = new TextBox();
$obTxtOrdemInicio->setName  ("inCodOrdemInicial");
$obTxtOrdemInicio->setId    ("inCodOrdemInicial");
$obTxtOrdemInicio->setRotulo("Número da Ordem");
$obTxtOrdemInicio->setTitle ("Informe a faixa de número de ordens para o filtro.");

$obLblOrdem = new Label();
$obLblOrdem->setValue(" a ");
$obLblOrdem->setTitle ("Informe a faixa de número de ordens para o filtro.");

$obTxtOrdemFinal = new TextBox();
$obTxtOrdemFinal->setId    ("inCodOrdemFinal");
$obTxtOrdemFinal->setName  ("inCodOrdemFinal");
$obTxtOrdemFinal->setRotulo("Número da Ordem");
$obTxtOrdemFinal->setTitle ("Informe a faixa de número de ordens para o filtro.");

// Define Objeto Select para Tipo de receita
$obCmbTipoReceita = new Select();
$obCmbTipoReceita->setRotulo("Receitas");
$obCmbTipoReceita->setId    ("stTipoReceita");
$obCmbTipoReceita->setName  ("stTipoReceita");
$obCmbTipoReceita->setTitle ("Selecione o Tipo de Receita a ser Demonstrada.");
$obCmbTipoReceita->addOption("", "Geral");
$obCmbTipoReceita->addOption("orcamentaria", "Orçamentária");
$obCmbTipoReceita->addOption("extra", "Extra-Orçamentária");
$obCmbTipoReceita->obEvento->setOnChange("montaParametrosGET('mostraSpanReceita');");

// Define Objeto Span para Itens da ordem ou liquidacao
$obSpnContas = new Span();
$obSpnContas->setId('spnReceitas');

// Monta a popup de credor
$ObIPopUpCredor = new IPopUpCredor($obForm);
$ObIPopUpCredor->obCampoCod->setId('inCodCredor');
$ObIPopUpCredor->setNull(true);

// Define Objeto Text para Situação
$obTxtSituacao = new TextBox;
$obTxtSituacao->setRotulo   ("Situação");
$obTxtSituacao->setTitle    ("Informe a situação para filtro");
$obTxtSituacao->setName     ("situacaoTxt");
$obTxtSituacao->setValue    ($situacaoTxt);
$obTxtSituacao->setSize     (6);
$obTxtSituacao->setMaxLength(3);
$obTxtSituacao->setInteiro  (true);

// Define Objeto Select para Situação
$obCmbSituacao = new Select();
$obCmbSituacao->setRotulo("Situação");
$obCmbSituacao->setId    ("inSituacao");
$obCmbSituacao->setName  ("inSituacao");
$obCmbSituacao->setTitle ("Selecione a Situação para o filtro.");
$obCmbSituacao->addOption("", "Selecione");
$obCmbSituacao->addOption("1", "Pagas");
$obCmbSituacao->addOption("2", "À Pagar");
$obCmbSituacao->obEvento->setOnChange("montaParametrosGET('validaSituacao');");

// Define Objeto Select para Ordenação
$obCmbOrdenacao = new Select();
$obCmbOrdenacao->setRotulo("Ordenação");
$obCmbOrdenacao->setId    ("stOrdenacao");
$obCmbOrdenacao->setName  ("stOrdenacao");
$obCmbOrdenacao->setTitle ("Selecione a Ordenação para o filtro.");
$obCmbOrdenacao->addOption("", "Selecione");
$obCmbOrdenacao->addOption("empenho", "Por Empenho");
$obCmbOrdenacao->addOption("receita", "Por Receita");
$obCmbOrdenacao->addOption("credor", "Por Credor");
$obCmbOrdenacao->addOption("data_pagamento", "Por Data");

$obRdbDataPagamentoSim = new Radio;
$obRdbDataPagamentoSim->setTitle  ("Exibir Data de Pagamento");
$obRdbDataPagamentoSim->setRotulo ("Exibir Data de Pagamento");
$obRdbDataPagamentoSim->setName   ("boDataPagamento");
$obRdbDataPagamentoSim->setId     ("boDataPagamento");
$obRdbDataPagamentoSim->setLabel  ("Sim");
$obRdbDataPagamentoSim->setValue  ("S");
$obRdbDataPagamentoSim->setChecked( false );
$obRdbDataPagamentoSim->setNull   ( false );

$obRdbDataPagamentoNao = new Radio;
$obRdbDataPagamentoNao->setName   ("boDataPagamento");
$obRdbDataPagamentoNao->setId     ("boDataPagamento");
$obRdbDataPagamentoNao->setLabel  ("Não");
$obRdbDataPagamentoNao->setValue  ("N");
$obRdbDataPagamentoNao->setChecked( true );
$obRdbDataPagamentoSim->setNull   ( false );

// Componente para montas as assintatuas
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades($obSelectMultiploEntidadeUsuario);

/***************************
    MONTA O FORMULARIO
****************************/
$obFormulario = new Formulario;
$obFormulario->addForm              ($obForm);
$obFormulario->addHidden            ($obHdnCaminho);
$obFormulario->addTitulo            ("Dados para Filtro");
$obFormulario->addComponente        ($obSelectMultiploEntidadeUsuario);
$obFormulario->addComponente        ($obPeriodicidade);
$obFormulario->agrupaComponentes    (array($obTxtEmpenhoInicio, $obLblEmpenho ,$obTxtEmpenhoFinal));
$obFormulario->agrupaComponentes    (array($obTxtOrdemInicio, $obLblOrdem , $obTxtOrdemFinal));
$obFormulario->addComponente        ($obCmbTipoReceita);
$obFormulario->addSpan              ($obSpnContas);
$obFormulario->addComponente        ($ObIPopUpCredor);
$obFormulario->addComponenteComposto($obTxtSituacao, $obCmbSituacao);
$obFormulario->addComponente        ($obCmbOrdenacao);
$obFormulario->agrupaComponentes( array($obRdbDataPagamentoSim, $obRdbDataPagamentoNao) );
$obMontaAssinaturas->geraFormulario ($obFormulario);

$obFormulario->OK();
$obFormulario->show();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
 * Página de formulário de inclusão/alteração de Penalidades
 * Data de Criacao: 25/07/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: FMManterPenalidade.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php";
include_once CAM_GA_NORMAS_CLASSES . 'componentes/IPopUpNorma.class.php';
include_once CAM_GT_FIS_COMPONENTES . 'ITextBoxSelectTipoPenalidade.class.php';
include_once CAM_GA_ADM_COMPONENTES . 'ITextBoxSelectDocumento.class.php';
include_once CAM_GT_FIS_NEGOCIO . 'RFISPenalidade.class.php';
include_once CAM_GT_FIS_VISAO . 'VFISPenalidade.class.php';

//Instanciando a Classe de Controle e de Visao
$obController = new RFISPenalidade;
$obVisao = new VFISPenalidade($obController);

$stAcao = $request->get('stAcao');

# Inicializa tabela sem valores
Sessao::write( 'arValores', array() );
Sessao::write( 'arDescontos', array() );

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

# Define o nome dos arquivos PHP
$stPrograma = "ManterPenalidade";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".php";

include_once( $pgJs );

# Definição dos componentes
$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

# Define form
$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

# Define formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados para Penalidade" );

# Acrescenta label com código da penalidade quando em alteração
if ($stAcao == "reativar") {
    $obHdnCodigo = new Hidden();
    $obHdnCodigo->setName( "inCodPenalidade" );
    $obHdnCodigo->setValue( $_REQUEST['inCodPenalidade'] );
    $obFormulario->addHidden( $obHdnCodigo );

    $obHdnTimestampInicio = new Hidden();
    $obHdnTimestampInicio->setName( "stTimestampInicio" );
    $obHdnTimestampInicio->setValue( $_REQUEST['stTimestampInicio'] );
    $obFormulario->addHidden( $obHdnTimestampInicio );

    $stNomPenalidade = htmlentities(stripslashes(stripslashes($_REQUEST['stNomPenalidade'])), ENT_QUOTES);

    $obLblCodigo = new Label();
    $obLblCodigo->setRotulo( "Penalidade" );
    $obLblCodigo->setTitle( "Código e descrição da penalidade" );
    $obLblCodigo->setValue( $_REQUEST['inCodPenalidade']." - ".$stNomPenalidade );
    $obFormulario->addComponente( $obLblCodigo );

    $obLblTipoPenalidade = new Label();
    $obLblTipoPenalidade->setRotulo( "Tipo de Penalidade" );
    $obLblTipoPenalidade->setTitle( "Tipo de penalidade gerada pela infração." );
    $obLblTipoPenalidade->setValue( $_REQUEST['inCodTipoPenalidade'] . ' - '.$_REQUEST["stNomTipoPenalidade"] );
    $obLblTipoPenalidade->setId('stCodPenalidade');
    $obFormulario->addComponente( $obLblTipoPenalidade );

    $obLblPenalidadeBaixaDT = new Label();
    $obLblPenalidadeBaixaDT->setRotulo( "Data de Baixa" );
    $obLblPenalidadeBaixaDT->setTitle( "Data em que a penalidade foi baixada." );
    $obLblPenalidadeBaixaDT->setValue( $_REQUEST['dtBaixa'] );
    $obLblPenalidadeBaixaDT->setId('stPenalidadeBaixa');
    $obFormulario->addComponente( $obLblPenalidadeBaixaDT );

    $obLblPenalidadeBaixaMotivo = new Label();
    $obLblPenalidadeBaixaMotivo->setRotulo( "Motivo da Baixa" );
    $obLblPenalidadeBaixaMotivo->setTitle( "Motivo da baixa." );
    $obLblPenalidadeBaixaMotivo->setValue( $_REQUEST['stMotivo'] );
    $obLblPenalidadeBaixaMotivo->setId('stPenalidadeBaixaMotivo');
    $obFormulario->addComponente( $obLblPenalidadeBaixaMotivo );
}else
if ($stAcao == "baixar") {
    $obHdnCodigo = new Hidden();
    $obHdnCodigo->setName( "inCodPenalidade" );
    $obHdnCodigo->setValue( $_REQUEST['inCodPenalidade'] );
    $obFormulario->addHidden( $obHdnCodigo );

    $stNomPenalidade = htmlentities(stripslashes(stripslashes($_REQUEST['stNomPenalidade'])), ENT_QUOTES);

    $obLblCodigo = new Label();
    $obLblCodigo->setRotulo( "Penalidade" );
    $obLblCodigo->setTitle( "Código e descrição da penalidade" );
    $obLblCodigo->setValue( $_REQUEST['inCodPenalidade']." - ".$stNomPenalidade );
    $obFormulario->addComponente( $obLblCodigo );

    $obLblTipoPenalidade = new Label();
    $obLblTipoPenalidade->setRotulo( "Tipo de Penalidade" );
    $obLblTipoPenalidade->setTitle( "Tipo de penalidade gerada pela infração." );
    $obLblTipoPenalidade->setValue( $_REQUEST['inCodTipoPenalidade'] . ' - '.$_REQUEST["stNomTipoPenalidade"] );
    $obLblTipoPenalidade->setId('stCodPenalidade');
    $obFormulario->addComponente( $obLblTipoPenalidade );

    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName ( "stMotivo" );
    $obTxtMotivo->setNull ( false );
    $obTxtMotivo->setTitle ( "Informe o motivo para a baixa da penalidade." );
    $obTxtMotivo->setRotulo ("Motivo");
    $obFormulario->addComponente( $obTxtMotivo );

    $obPopUpProcesso = new IPopUpProcesso($obForm);
    $obPopUpProcesso->setRotulo("Processo");
    $obPopUpProcesso->setValidar(true);

    $obFormulario->addComponente( $obPopUpProcesso );
}else
if ($stAcao == 'alterar') {
    $obHdnCodigo = new Hidden();
    $obHdnCodigo->setName( "inCodPenalidade" );
    $obHdnCodigo->setValue( $_REQUEST['inCodPenalidade'] );
    $obFormulario->addHidden( $obHdnCodigo );

    $obLblCodigo = new Label();
    $obLblCodigo->setRotulo( "Código" );
    $obLblCodigo->setTitle( "Código da penalidade" );
    $obLblCodigo->setValue( $_REQUEST['inCodPenalidade'] );
    $obFormulario->addComponente( $obLblCodigo );

    # Define componente tipo de penalidade
    $obHdnCodigoTipo = new Hidden();
    $obHdnCodigoTipo->setName( "inCodTipoPenalidade" );
    $obHdnCodigoTipo->setValue( $_REQUEST['inCodTipoPenalidade'] );
    $obFormulario->addHidden( $obHdnCodigoTipo );

    $obLblTipoPenalidade = new Label();
    $obLblTipoPenalidade->setRotulo( "Tipo de Penalidade" );
    $obLblTipoPenalidade->setTitle( "Tipo de penalidade gerada pela infração." );
    $obLblTipoPenalidade->setValue( $_REQUEST['inCodTipoPenalidade'] . ' - '.$_REQUEST["stNomTipoPenalidade"] );
    $obLblTipoPenalidade->setId('stCodPenalidade');
    $obFormulario->addComponente( $obLblTipoPenalidade );
} else {
    # Define componente tipo de penalidade
    $obITextBoxSelectTipoPenalidade = new ITextBoxSelectTipoPenalidade();
    $obITextBoxSelectTipoPenalidade->setName( "inCodTipoPenalidade" );
    $obITextBoxSelectTipoPenalidade->obCmbTipoPenalidade->setId('inCodSelecTipoPenalidade');
    $obITextBoxSelectTipoPenalidade->setTitle( "Tipo de penalidade gerada pela infração." );
    $obITextBoxSelectTipoPenalidade->setNull( false );
    $obITextBoxSelectTipoPenalidade->setValue( $_REQUEST['inCodTipoPenalidade'] );
    $obITextBoxSelectTipoPenalidade->setOnChange( "montaParametrosGET('montaFormulario');" );
    $obITextBoxSelectTipoPenalidade->geraFormulario( $obFormulario );
}

if ( ( $stAcao != "baixar" ) && ( $stAcao != "reativar" ) ) {
    # Define descrição da penalidade
    $obDescricao = new TextBox();
    $obDescricao->setName( "stNomPenalidade" );
    $obDescricao->setId( "stNomPenalidade" );
    $obDescricao->setRotulo( "Descrição da Penalidade" );
    $obDescricao->setTitle( "Informe a descrição da penalidade." );
    $obDescricao->setSize( 50 );
    $obDescricao->setMaxLength( 80 );
    $obDescricao->setNull( false );
    $stNomPenalidade = htmlentities(stripslashes(stripslashes($_REQUEST['stNomPenalidade'])), ENT_QUOTES);
    $obDescricao->setValue($stNomPenalidade);
    $obFormulario->addComponente( $obDescricao );

    # Define fundamentacao legal
    $obIPopUpNorma = new IPopUpNorma();
    $obIPopUpNorma->obInnerNorma->setRotulo( "Fundamentação Legal" );
    $obIPopUpNorma->obInnerNorma->setTitle( "Fundamentação legal que regulamenta a aplicação da penalidade." );
    $obIPopUpNorma->setCodNorma( $_REQUEST['inCodNorma'] );
    $obIPopUpNorma->geraFormulario( $obFormulario );
}

# Define span para penalidade do tipo multa
$obSpanMulta = new Span();
$obSpanMulta->setId('spnMulta');

# Define span para o valor da multa de acordo com o seu tipo
$obSpanMultaValor = new Span();
$obSpanMultaValor->setId('spnMultaValor');

# Define span Dados para Descontos
$obSpanMultaDescontos = new Span();
$obSpanMultaDescontos->setId('spnMultaDescontos');

# Define spans para formulário dinâmico
$obFormulario->addSpan( $obSpanMulta );
$obFormulario->addSpan( $obSpanMultaValor );
$obFormulario->addSpan( $obSpanMultaDescontos );

# Documento de notificação
$obTermoDevolucao = new ITextBoxSelectDocumento();
$obTermoDevolucao->setCodAcao(Sessao::read('acao')) ;
$obTermoDevolucao->obTextBoxSelectDocumento->setRotulo('Documento de Notificação');
$obTermoDevolucao->obTextBoxSelectDocumento->setName('stCodDocumento' );
$obTermoDevolucao->obTextBoxSelectDocumento->setTitle('Documento a ser emitido no ato da notificação.');
$obTermoDevolucao->obTextBoxSelectDocumento->obTextBox->setSize(10);
$obTermoDevolucao->obTextBoxSelectDocumento->obSelect->setStyle('width: 261px;');
$obTermoDevolucao->obTextBoxSelectDocumento->setNull(true);

if ($stAcao == 'alterar') {
    $obTermoDevolucao->obTextBoxSelectDocumento->obTextBox->setValue($_REQUEST['stCodDocumento']);
    $obTermoDevolucao->obTextBoxSelectDocumento->obSelect->setValue($_REQUEST['stCodDocumento']);
}

if ( ( $stAcao != "baixar" ) && ( $stAcao != "reativar" ) ) {
    $obTermoDevolucao->geraFormulario($obFormulario);
}

# Cria botões do formulário
$obBtnOK = new OK();
$obBtnLimpar = new Button();
$obBtnLimpar->setName('btnLimpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->setTipo('button');
$obBtnLimpar->obEvento->setOnClick('limparFormulario();');
$arBotoesFormulario = array($obBtnOK, $obBtnLimpar);
//$obFormulario->defineBarra($arBotoesFormulario);

if ($stAcao != "reativar") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar($pgList);
}

# Exibe o formulário
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

# Ativa painel oculto no caso de alteração de Penalidade existente
if ($_REQUEST['stAcao'] == 'alterar') {
    SistemaLegado::executaFrameOculto("montaParametrosGET('montaFormulario');");
}

?>

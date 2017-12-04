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
 * Página de formulário de Autos de Infração
 * Data de Criação: 20/08/2008
 *
 *
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: FMEmitirAutoInfracao.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de Uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/componentes/ITextBoxSelectDocumento.class.php';
require_once( CAM_GT_FIS_INSTANCIAS."processoFiscal/JSEmitirDocumento.php" );
include_once( CAM_GT_FIS_COMPONENTES . "ITextBoxSelectInfracao.class.php" );

$stAcao = $request->get('stAcao');

Sessao::write( 'arPenalidades', array() );
Sessao::write( 'arInfracoes', array() );

if ( empty( $stAcao ) ) {
    $stAcao = "incluirAutoInfracao";
}

# Define o nome dos arquivos PHP
$stPrograma = "EmitirAutoInfracao";
$pgFilt     = "FL" . $stPrograma . ".php";
$pgList     = "LS" . $stPrograma . ".php";
$pgForm     = "FM" . $stPrograma . ".php";
$pgProc     = "PR" . $stPrograma . ".php";
$pgOcul     = "OC" . $stPrograma . ".php";
$pgJs       = "JS" . $stPrograma . ".php";

include_once( $pgJs );

# Definição dos componentes ocultos
$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnTipoFiscalizacao = new Hidden();
$obHdnTipoFiscalizacao->setName( "inTipoFiscalizacao" );
$obHdnTipoFiscalizacao->setValue( $_REQUEST['inTipoFiscalizacao'] );

$obHdnInscricao = new Hidden();
$obHdnInscricao->setName( "inInscricao" );
$obHdnInscricao->setValue( $_REQUEST['inInscricao'] );

$obHdnCodProcesso = new Hidden();
$obHdnCodProcesso->setName( "inCodProcesso" );
$obHdnCodProcesso->setValue( $_REQUEST['inCodProcesso'] );

# Define form
$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( "telaPrincipal" );

# Define formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnTipoFiscalizacao );
$obFormulario->addHidden( $obHdnInscricao );
$obFormulario->addHidden( $obHdnCodProcesso );
$obFormulario->addTitulo( "Dados para Notificação Fiscal" );

# Define label Tipo de Fiscalização
$obLblTipoFiscalizacao = new Label();
$obLblTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obLblTipoFiscalizacao->setValue( $_REQUEST['inTipoFiscalizacao'] . ' - ' . $_REQUEST['stDescricao'] );
$obFormulario->addComponente( $obLblTipoFiscalizacao );

# Define label Processo Fiscal
$obLblProcessoFiscal = new Label();
$obLblProcessoFiscal->setName( "inCodProcesso" );
$obLblProcessoFiscal->setRotulo( "Processo Fiscal" );
$obLblProcessoFiscal->setValue( $_REQUEST['inCodProcesso'] );
$obFormulario->addComponente( $obLblProcessoFiscal );

# Define span da inscrição imobiliária/econômica
$obSpnInscricao = new Span();
$obSpnInscricao->setID( "spnInscricao" );
$obFormulario->addSpan( $obSpnInscricao );

# Define data de notificação referente ao processo fiscal
$obDtNotificacao = new Data();
$obDtNotificacao->setName( "dtNotificacao" );
$obDtNotificacao->setRotulo( "Data" );
$obDtNotificacao->setTitle( "Data da notificação referente ao processo fiscal." );
$obDtNotificacao->setNull( false );
$obFormulario->addComponente( $obDtNotificacao );

# Define área de texto para observações da notificação fiscal.
$obObservacoes = new TextArea();
$obObservacoes->setName( "stObservacoes" );
$obObservacoes->setRotulo( "Observações" );
$obObservacoes->setTitle( "Observações referentes à notificação." );
$obFormulario->addComponente( $obObservacoes );

# Termo de notificação
$obTermoInicio = new ITextBoxSelectDocumento;
$obTermoInicio->setCodAcao(Sessao::read('acao')) ;
$obTermoInicio->obTextBoxSelectDocumento->setNull( false );
$obTermoInicio->obTextBoxSelectDocumento->setRotulo( "Termo de Notificação" );
$obTermoInicio->obTextBoxSelectDocumento->setName( "stCodDocumento" );
$obTermoInicio->obTextBoxSelectDocumento->setTitle( "Selecione o Termo de Início." );
$obTermoInicio->obTextBoxSelectDocumento->obTextBox->setSize( 10 );
$obTermoInicio->obTextBoxSelectDocumento->obSelect->setStyle( "width: 261px;" );
$obTermoInicio->geraFormulario( $obFormulario );

# Dados de nota fiscal
$obFormulario->addTitulo( "Dados para Infração" );

# Define infração
$obITextBoxSelectInfracao = new ITextBoxSelectInfracao( $_REQUEST['inCodProcesso'] );
$obITextBoxSelectInfracao->setName( "inCodInfracao" );
$obITextBoxSelectInfracao->setRotulo( "Infração" );
$obITextBoxSelectInfracao->setTitle( "Infração cometida pelo contribuinte." );
$obITextBoxSelectInfracao->setOnChange( "montaParametrosGET('alterarInfracao');" );
$obITextBoxSelectInfracao->geraFormulario( $obFormulario );

# Define área de texto para observações das infrações
$obObservacoesInfracao = new TextArea();
$obObservacoesInfracao->setName( "stObservacoesInfracao" );
$obObservacoesInfracao->setRotulo( "Observações Infração" );
$obObservacoesInfracao->setTitle( "Observações referentes à infração." );
$obFormulario->addComponente( $obObservacoesInfracao );

# Define span da lista de penalidades.
$obSpnListaPenalidades = new Span();
$obSpnListaPenalidades->setID( 'spnListaPenalidades' );
$obFormulario->addSpan( $obSpnListaPenalidades );

# Botões de infração.
$obBtnIncluirInfracao = new Button();
$obBtnIncluirInfracao->setName( "btnIncluirInfracao" );
$obBtnIncluirInfracao->setValue( "Incluir" );
$obBtnIncluirInfracao->setTipo( "button" );
$obBtnIncluirInfracao->obEvento->setOnClick( "incluirInfracao();" );
$obBtnIncluirInfracao->setDisabled( false );

$obBtnLimparInfracao = new Button();
$obBtnLimparInfracao->setName( "btnLimparInfracao" );
$obBtnLimparInfracao->setValue( "Limpar" );
$obBtnLimparInfracao->setTipo( "button" );
$obBtnLimparInfracao->obEvento->setOnClick( "montaParametrosGET('limparInfracao');" );
$obBtnLimparInfracao->setDisabled( false );

# Define formulário
$obFormulario2 = new Formulario();
$obFormulario2->addHidden( $obHdnAcao );
$obFormulario2->addHidden( $obHdnCtrl );
$obFormulario2->addHidden( $obHdnTipoFiscalizacao );
$obFormulario2->addHidden( $obHdnInscricao );
$obFormulario2->addHidden( $obHdnCodProcesso );
$obFormulario->addFormulario( $obFormulario2 );

$arBotoesInfracao = array( $obBtnIncluirInfracao, $obBtnLimparInfracao );
$obFormulario2->defineBarra( $arBotoesInfracao, "left", "" );

# Define span de registros de infração.
$obSpnRegistrosInfracao = new Span();
$obSpnRegistrosInfracao->setID( "spnRegistrosInfracao" );
$obFormulario->addSpan( $obSpnRegistrosInfracao );

# Define opção de impressão por infração
$obRadImpressaoInfracao = new Radio();
$obRadImpressaoInfracao->setName( "boImpressao" );
$obRadImpressaoInfracao->setRotulo( "Tipo de Impressão" );
$obRadImpressaoInfracao->setLabel( "Impressão por Infração" );
$obRadImpressaoInfracao->setValue( "f" );

# Define opção de impressão por documento único
$obRadImpressaoDocumento = new Radio();
$obRadImpressaoDocumento->setName( "boImpressao" );
$obRadImpressaoDocumento->setRotulo( "Tipo de Impressão" );
$obRadImpressaoDocumento->setLabel( "Impressão por Documento único" );
$obRadImpressaoDocumento->setValue( "t" );
$obRadImpressaoDocumento->setChecked( true );

$obFormulario->agrupaComponentes( array( $obRadImpressaoInfracao, $obRadImpressaoDocumento ) );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

SistemaLegado::executaFrameOculto( "montaParametrosGET('montaFormulario');" );

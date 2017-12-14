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
 * Página de formulário de Emitir Termo Obra
 * Data de Criação: 11/11/2008
 *
 *
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Marcio Medeiros
 * @ignore
 *
 * $Id: FMEmitirTermo.php 59612 2014-09-02 12:00:51Z gelson $
 *
 * Casos de Uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GT_FIS_INSTANCIAS."processoFiscal/JSEmitirDocumento.php" );
include_once CAM_GA_ADM_COMPONENTES . 'ITextBoxSelectDocumento.class.php';
include_once( CAM_GT_FIS_COMPONENTES . "ITextBoxSelectInfracao.class.php" );

if (isset($_REQUEST['stAcaoControle'])) {
    $stAcaoControle = strtolower($_REQUEST['stAcaoControle']);
} else {
    $stAcaoControle = 'incluir';
}

// demolicao, embargo ou interdicao
$stTipoTermo = $_REQUEST['stAcao'];
switch ($stTipoTermo) {
    case 'demolicao':
        $stNomeTermo = 'Demolição';
        break;

    case 'embargo':
        $stNomeTermo = 'Embargo';
        break;
    case 'interdicao':
        $stNomeTermo = 'Interdição';
        break;

    default:
        $stNomeTermo = $stAcao;
}

# Inicializa tabelas sem valores.
Sessao::write( 'arPenalidades', array() );
Sessao::write( 'arInfracoes', array() );

# Define o nome dos arquivos PHP
$stPrograma = "EmitirTermo";
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
$obHdnAcao->setValue( $stAcaoControle );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnTipoTermo = new Hidden();
$obHdnTipoTermo->setName( "hdnTipoTermo" );
$obHdnTipoTermo->setID( "hdnTipoTermo" );
$obHdnTipoTermo->setValue( $stTipoTermo );

$obHdnTipoFiscalizacao = new Hidden();
$obHdnTipoFiscalizacao->setName( "hdnTipoFiscalizacao" );
$obHdnTipoFiscalizacao->setID( "hdnTipoFiscalizacao" );
$obHdnTipoFiscalizacao->setValue( $_REQUEST['inTipoFiscalizacao'] );

$obHdnInscricaoImobiliaria = new Hidden();
$obHdnInscricaoImobiliaria->setName( "hdnCodInscricaoImobiliaria" );
$obHdnInscricaoImobiliaria->setID( "hdnCodInscricaoImobiliaria" );
$obHdnInscricaoImobiliaria->setValue( $_REQUEST['inInscricao'] );

$obHdnCodProcessoFiscal = new Hidden();
$obHdnCodProcessoFiscal->setName( "hdnCodProcessoFiscal" );
$obHdnCodProcessoFiscal->setID( "hdnCodProcessoFiscal" );
$obHdnCodProcessoFiscal->setValue( $_REQUEST['inCodProcesso'] );

# Define form
$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

# Define formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnTipoTermo );
$obFormulario->addHidden( $obHdnTipoFiscalizacao );
$obFormulario->addHidden( $obHdnCodProcessoFiscal );
$obFormulario->addHidden( $obHdnInscricaoImobiliaria );
$obFormulario->addTitulo( "Dados para Embrago ou Demolição de Obra" );

# Tipo de Fiscalização
$obLblTipoFiscalizacao = new Label();
$obLblTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obLblTipoFiscalizacao->setValue( $_REQUEST['inTipoFiscalizacao'] . ' - ' . $_REQUEST['stDescricao'] );
$obFormulario->addComponente( $obLblTipoFiscalizacao );

# Processo Fiscal
$obLblProcessoFiscal = new Label();
$obLblProcessoFiscal->setName( "inCodProcessoFiscal" );
$obLblProcessoFiscal->setRotulo( "Processo Fiscal" );
$obLblProcessoFiscal->setValue( $_REQUEST['inCodProcesso'] );
$obFormulario->addComponente( $obLblProcessoFiscal );

// Inscrição Imobiliária
$obLblInscricaoImobiliaria = new Label();
$obLblInscricaoImobiliaria->setName( "inCodInscricaoImobiliaria" );
$obLblInscricaoImobiliaria->setRotulo( "Inscrição Imobiliária" );
$obLblInscricaoImobiliaria->setValue( $_REQUEST['inInscricao'] );
$obFormulario->addComponente( $obLblInscricaoImobiliaria );

// Data
$obDtNotificacao = new Data();
$obDtNotificacao->setName( "stDataNotificacao" );
$obDtNotificacao->setID( "stDataNotificacao" );
$obDtNotificacao->setRotulo( "Data da Notificação" );
$obDtNotificacao->setTitle( "Data da notificação referente ao processo fiscal." );
$obDtNotificacao->setNull( false );
$obFormulario->addComponente( $obDtNotificacao );

// Observações
$obObservacoes = new TextArea();
$obObservacoes->setName( "stObservacoes" );
$obObservacoes->setID( "stObservacoes" );
$obObservacoes->setRotulo( "Observações" );
$obObservacoes->setTitle( "Observações" );
$obObservacoes->setNull( false );
$obFormulario->addComponente( $obObservacoes );

// Termo (Demolição, Embargo ou Interdição)
$obTermo = new ITextBoxSelectDocumento();
$obTermo->setCodAcao( substr($_SESSION["acao"], 5,-2) ) ;
$obTermo->obTextBoxSelectDocumento->setNull( false );
$obTermo->obTextBoxSelectDocumento->setRotulo( "Termo de " . $stNomeTermo );
$obTermo->obTextBoxSelectDocumento->setTitle( "Selecione o Termo de " . $stNomeTermo);
$obTermo->obTextBoxSelectDocumento->obTextBox->setSize( 10 );
$obTermo->obTextBoxSelectDocumento->obSelect->setStyle( "width: 261px;" );
$obTermo->geraFormulario($obFormulario);

# Infração
$obFormulario->addTitulo( "Dados para Infração" );
$obITextBoxSelectInfracao = new ITextBoxSelectInfracao( $_REQUEST['inCodProcesso'] );
$obITextBoxSelectInfracao->setName( "inCodInfracao" );
$obITextBoxSelectInfracao->setRotulo( "Infração" );
$obITextBoxSelectInfracao->setTitle( "Infração cometida pelo contribuinte." );
$obITextBoxSelectInfracao->setNull( false );
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

# Define span de registros de infração.
$obSpnRegistrosInfracao = new Span();
$obSpnRegistrosInfracao->setID( "spnRegistrosInfracao" );
$obFormulario->addSpan( $obSpnRegistrosInfracao );

$arBotoesInfracao = array( $obBtnIncluirInfracao, $obBtnLimparInfracao );
$obFormulario->defineBarra( $arBotoesInfracao, "left", "" );

// Conclusão do formulário
$obBtnOK = new Button();
$obBtnOK->setValue( "OK" );
$obBtnOK->setTipo( "button" );
$obBtnOK->setStyle( "width: 60px;" );
$obBtnOK->obEvento->setOnClick( "IncluirTermo();" );
$obBtnOK->setDisabled( false );

$obBtnLimpar = new Limpar();

$arBotoes = array( $obBtnOK, $obBtnLimpar );
$obFormulario->defineBarra( $arBotoes, 'left', '<b>* Campos Obrigatorios</b>' );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

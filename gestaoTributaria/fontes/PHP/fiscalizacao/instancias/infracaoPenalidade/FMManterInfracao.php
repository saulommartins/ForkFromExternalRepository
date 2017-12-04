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
 * Página de formulário de inclusão/alteração de Infração
 * Data de Criacao: 30/07/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: FMManterInfracao.php 59612 2014-09-02 12:00:51Z gelson $

 * alteração: 22/08/2008 jânio Eduardo

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php";
include_once CAM_GA_NORMAS_CLASSES . 'componentes/IPopUpNorma.class.php';
include_once CAM_GT_FIS_COMPONENTES . 'ITextBoxSelectTipoFiscalizacao.class.php';
include_once CAM_GT_FIS_COMPONENTES . 'IPopUpPenalidade.class.php';
include_once CAM_GT_FIS_NEGOCIO . 'RFISInfracao.class.php';
include_once CAM_GT_FIS_VISAO . 'VFISInfracao.class.php';

$stAcao = $request->get('stAcao');

# Inicializa tabela sem valores
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) {
    $stAcao = 'incluir';
}

# Define o nome dos arquivos PHP
$stPrograma = 'ManterInfracao';
$pgFilt     = 'FL' . $stPrograma . '.php';
$pgList     = 'LS' . $stPrograma . '.php';
$pgForm     = 'FM' . $stPrograma . '.php';
$pgProc     = 'PR' . $stPrograma . '.php';
$pgOcul     = 'OC' . $stPrograma . '.php';
$pgJs       = 'JS' . $stPrograma . '.php';

include_once( $pgJs );

# Definição dos componentes hidden
$obHdnAcao = new Hidden();
$obHdnAcao->setName( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCodNorma = new Hidden();
$obHdnCodNorma->setName( 'inHdnCodNorma' );

# Definição do form
$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );
$obForm->setEncType( 'multipart/form-data' );

# Definição do formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnCodNorma );
$obFormulario->addTitulo( 'Dados para Infração' );

# Cria objeto regra e visão.
$obRegra = new RFISInfracao();
$obVisao = new VFISInfracao( $obRegra );

# Acrescenta label com código da infração quando em alteração.
if ($stAcao == 'reativar') {
    $obHdnCodInfracao = new Hidden();
    $obHdnCodInfracao->setName('inCodInfracao');
    $obHdnCodInfracao->setValue($_REQUEST['inCodInfracao']);
    $obFormulario->addHidden($obHdnCodInfracao);

    $obHdnTimestampInicio = new Hidden();
    $obHdnTimestampInicio->setName( "stTimestampInicio" );
    $obHdnTimestampInicio->setValue( $_REQUEST['stTimestampInicio'] );
    $obFormulario->addHidden( $obHdnTimestampInicio );

    $obLblCodigo = new Label();
    $obLblCodigo->setRotulo('Código');
    $obLblCodigo->setTitle('Código da infração');
    $obLblCodigo->setValue( $_REQUEST['inCodInfracao']." - ".$_REQUEST['stNomInfracao'] );
    $obFormulario->addComponente($obLblCodigo);

    $obLblTipoFiscalizacao = new Label();
    $obLblTipoFiscalizacao->setRotulo('Tipo de Fiscalização');
    $obLblTipoFiscalizacao->setTitle('Tipo de fiscalização da infração.');
    $obLblTipoFiscalizacao->setValue( $_REQUEST['inTipoFiscalizacao']." - ".$_REQUEST['stNomTipoFiscalizacao'] );
    $obFormulario->addComponente( $obLblTipoFiscalizacao );

    $obLblInfracaoBaixaDT = new Label();
    $obLblInfracaoBaixaDT->setRotulo( "Data de Baixa" );
    $obLblInfracaoBaixaDT->setTitle( "Data em que a penalidade foi baixada." );
    $obLblInfracaoBaixaDT->setValue( $_REQUEST['stDtbaixa'] );
    $obLblInfracaoBaixaDT->setId('stInfracaoDtBaixa');
    $obFormulario->addComponente( $obLblInfracaoBaixaDT );

    $obLblInfracaoBaixaMotivo = new Label();
    $obLblInfracaoBaixaMotivo->setRotulo( "Motivo da Baixa" );
    $obLblInfracaoBaixaMotivo->setTitle( "Motivo da baixa." );
    $obLblInfracaoBaixaMotivo->setValue( $_REQUEST['stMotivo'] );
    $obLblInfracaoBaixaMotivo->setId('stInfracaoBaixaMotivo');
    $obFormulario->addComponente( $obLblInfracaoBaixaMotivo );
}else
if ($stAcao == 'baixar') {
    $obHdnCodInfracao = new Hidden();
    $obHdnCodInfracao->setName('inCodInfracao');
    $obHdnCodInfracao->setValue($_REQUEST['inCodInfracao']);
    $obFormulario->addHidden($obHdnCodInfracao);

    $obLblCodigo = new Label();
    $obLblCodigo->setRotulo('Código');
    $obLblCodigo->setTitle('Código da infração');
    $obLblCodigo->setValue( $_REQUEST['inCodInfracao']." - ".$_REQUEST['stNomInfracao'] );
    $obFormulario->addComponente($obLblCodigo);

    $obLblTipoFiscalizacao = new Label();
    $obLblTipoFiscalizacao->setRotulo('Tipo de Fiscalização');
    $obLblTipoFiscalizacao->setTitle('Tipo de fiscalização da infração.');
    $obLblTipoFiscalizacao->setValue( $_REQUEST['inTipoFiscalizacao']." - ".$_REQUEST['stNomTipoFiscalizacao'] );
    $obFormulario->addComponente( $obLblTipoFiscalizacao );

    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName ( "stMotivo" );
    $obTxtMotivo->setNull ( false );
    $obTxtMotivo->setTitle ( "Informe o motivo para a baixa da infração." );
    $obTxtMotivo->setRotulo ("Motivo");
    $obFormulario->addComponente( $obTxtMotivo );

    $obPopUpProcesso = new IPopUpProcesso($obForm);
    $obPopUpProcesso->setRotulo("Processo");
    $obPopUpProcesso->setValidar(true);

    $obFormulario->addComponente( $obPopUpProcesso );
}else
if ($stAcao == 'alterar') {
    $rsInfracao = $obVisao->getInfracao($_REQUEST['inCodInfracao']);

    if (!$rsInfracao->eof()) {
        $arInfracao = array_shift($rsInfracao->getElementos());
        $_REQUEST['inCodNorma'] = $arInfracao['cod_norma'];
        $_REQUEST['boCominar']  = $arInfracao['comminar'];
    }

    # Define código da norma
    $obHdnCodNorma->setValue($_REQUEST['inCodNorma']);

    # Define código da infração
    $obHdnCodInfracao = new Hidden();
    $obHdnCodInfracao->setName('inCodInfracao');
    $obHdnCodInfracao->setValue($_REQUEST['inCodInfracao']);
    $obFormulario->addHidden($obHdnCodInfracao);

    # Define label com o código da infração
    $obLblCodigo = new Label();
    $obLblCodigo->setRotulo('Código');
    $obLblCodigo->setTitle('Código da infração');
    $obLblCodigo->setValue($_REQUEST['inCodInfracao']);
    $obFormulario->addComponente($obLblCodigo);
}

if ($stAcao != 'baixar' && $stAcao != 'reativar') {
    # Define componente de tipo de fiscalização
    $obITextBoxSelectTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao();
    $obITextBoxSelectTipoFiscalizacao->setTitle('Tipo de fiscalização que poderá utilizar a infração.');
    $obITextBoxSelectTipoFiscalizacao->setNULL(false);
    $obITextBoxSelectTipoFiscalizacao->setValue($_REQUEST['inTipoFiscalizacao']);
    $obITextBoxSelectTipoFiscalizacao->obCmbTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('montaPenalidade');");
    $obITextBoxSelectTipoFiscalizacao->geraFormulario( $obFormulario );

    # Define descrição da infração
    $obDescricao = new TextBox();
    $obDescricao->setName('stNomInfracao');
    $obDescricao->setId('stNomInfracao');
    $obDescricao->setRotulo('Descrição da Infração');
    $obDescricao->setTitle('Informe a descrição da infração.');
    $obDescricao->setSize( 50 );
    $obDescricao->setMaxLength( 80 );
    $obDescricao->setNull( false );
    $obDescricao->setValue( $_REQUEST['stNomInfracao'] );
    $obFormulario->addComponente( $obDescricao );

    # Define fundamentacao legal
    $obIPopUpNorma = new IPopUpNorma();
    $obIPopUpNorma->obInnerNorma->setRotulo('Fundamentação Legal');
    $obIPopUpNorma->obInnerNorma->setTitle('Fundamentação legal que regulamenta a infração.');
    $obIPopUpNorma->setCodNorma( $_REQUEST['inCodNorma'] );
    $obIPopUpNorma->geraFormulario( $obFormulario );

    # Define opção para cominar
    $obRadioCominarSim = new Radio();
    $obRadioCominarSim->setName('boCominar');
    $obRadioCominarSim->setRotulo('Agrupar');
    $obRadioCominarSim->setTitle('Informe se a infração poderá ser aplicada em conjunto com outras infrações.');
    $obRadioCominarSim->setLabel('Sim');
    $obRadioCominarSim->setValue('t');

    if ( (! isset( $_REQUEST['boCominar'] ) ) || ( $_REQUEST['boCominar'] == 't' ) ) {
        $boCominar = true;
    } else {
        $boCominar = false;
    }

    $obRadioCominarSim->setChecked( $boCominar );
    $obRadioCominarSim->setNull( false );

    # Define opção para não cominar
    $obRadioCominarNao = new Radio();
    $obRadioCominarNao->setName('boCominar');
    $obRadioCominarNao->setLabel('Não');
    $obRadioCominarNao->setValue('f');
    $obRadioCominarNao->setChecked(!$boCominar);
    $obRadioCominarNao->setNull(false);

    $obFormulario->agrupaComponentes(array( $obRadioCominarSim, $obRadioCominarNao));

    # Define span que separa os dados das penalidades
    $obSpanPenalidades = new Span();
    $obSpanPenalidades->setId('spnPenalidades');
    $obFormulario->addSpan($obSpanPenalidades);

    # Define span que separa os dados das penalidades
    $obSpanListaPenalidades = new Span();
    $obSpanListaPenalidades->setId('spnListaPenalidades');
    $obFormulario->addSpan( $obSpanListaPenalidades );
}

$obBtnOK = new OK();
$obBtnLimpar = new Button();
$obBtnLimpar->setName('btnLimpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->setTipo('button');
$obBtnLimpar->obEvento->setOnClick('limparFormulario();');
$arBotoesFormulario = array($obBtnOK, $obBtnLimpar);

if ($stAcao != 'reativar') {
    $obFormulario->defineBarra($arBotoesFormulario);
} else {
    $obFormulario->Cancelar($pgList);
}

# Exibe o formulário
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

# Ativa painel oculto no caso de alteração de Infração existente
switch ($stAcao) {
    case 'alterar':
        SistemaLegado::executaFrameOculto( "montaParametrosGET('carregarPenalidades');" );
        break;
}

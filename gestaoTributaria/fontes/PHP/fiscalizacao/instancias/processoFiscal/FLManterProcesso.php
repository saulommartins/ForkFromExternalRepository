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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GT_FIS_COMPONENTES."ITextBoxSelectTipoFiscalizacao.class.php" );
include_once (CAM_GT_FIS_NEGOCIO."/RFISProcessoFiscal.class.php");
include_once (CAM_GT_FIS_VISAO."/VFISProcessoFiscal.class.php");

$obControllerProcessoFiscal = new RFISProcessoFiscal;
$obVisaoProcessoFiscal = new VFISProcessoFiscal($obControllerProcessoFiscal);

$stAcao = $_GET['stAcao'];

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcesso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

include($pgJs);

switch ($stAcao) {
    case  'alterar' :
        $pgForm = $pgList;
        $stAcao = "alterar";
    break;
    case  'cancelar':
        $pgForm = $pgList;
        $stAcao = "cancelar";
    break;
    case  'encerrar':
        $pgForm = $pgList;
        $stAcao = "encerrar";
    break;
        case  'notificar':
        $pgForm = "LSNotificarProcesso.php";
        $stAcao = "notificar";

        $obHDTipoFiscalizacao = new Hidden;
        $obHDTipoFiscalizacao->setName ( "stTipoFiscalizacao" );
        $obHDTipoFiscalizacao->setValue("1");
    break;
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_GET['stCtrl']  );

$obHdnCgm =  new Hidden;
$obHdnCgm->setName ( "numcgm" );
$obHdnCgm->setValue(Sessao::read('numCgm'));

$obHdnInicio =  new Hidden;
$obHdnInicio->setName ( "boInicio" );
$obHdnInicio->setValue( true );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgForm );
$obForm->setTarget ( "telaPrincipal" );

if ($stAcao == 'incluir') {
    $obRsTiposFiscalizacao = new RecordSet();
    $obTTipoFiscalizacao = new TFISTipoFiscalizacao();
    $obTTipoFiscalizacao->recuperaTodos($obRsTiposFiscalizacao,' where cod_tipo in(1,2)');

    $obCmbTipoFiscalizacao = new Select();
    $obCmbTipoFiscalizacao->addOption('','Selecione');
    $obCmbTipoFiscalizacao->setId('inTipoFiscalizacao');
    $obCmbTipoFiscalizacao->setName('inTipoFiscalizacao');
    $obCmbTipoFiscalizacao->setNull(false);
    $obCmbTipoFiscalizacao->setRotulo('Tipo de Fiscalização');
    $obCmbTipoFiscalizacao->setCampoId('cod_tipo');
    $obCmbTipoFiscalizacao->setCampoDesc('descricao');
    $obCmbTipoFiscalizacao->preencheCombo($obRsTiposFiscalizacao);
    $obCmbTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('verificaAtribuicaoFiscal','',true);");
}

if ($stAcao != 'incluir') {
    //Tipo Fiscalizacao
    $obTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao;
    $obTipoFiscalizacao->setNull(false);
    $obTipoFiscalizacao->setTitle( "Informe o Tipo de Fiscalização." );
    $obTipoFiscalizacao->obTxtTipoFiscalizacao->setId("txtTipoFiscalizacao");
    $obTipoFiscalizacao->obCmbTipoFiscalizacao->setId("cmbTipoFiscalizacao");

    if ($stAcao == 'notificar') {
        $obTipoFiscalizacao->setNull(null);
        $obTipoFiscalizacao->setValue( 1 );
        $obTipoFiscalizacao->obTxtTipoFiscalizacao->setDisabled( true );
        $obTipoFiscalizacao->obCmbTipoFiscalizacao->setDisabled( true );
    }

    $obTipoFiscalizacao->obTxtTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('montaForm, ','cmbTipoFiscalizacao');");
    $obTipoFiscalizacao->obCmbTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('montaForm','cmbTipoFiscalizacao');");

    $obTipoFiscalizacao->obTxtTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('verificaAtribuicaoFiscal','');");
    $obTipoFiscalizacao->obCmbTipoFiscalizacao->obEvento->setOnChange("montaParametrosGET('verificaAtribuicaoFiscal','');");
}

if ($stAcao != 'incluir') {
    $obProcessoFiscal = new TextBox;
    $obProcessoFiscal->setName( "inCodProcesso" );
    $obProcessoFiscal->setId( "inCodProcesso" );
    $obProcessoFiscal->setInteiro(true);
    $obProcessoFiscal->setSize( "10" );
    $obProcessoFiscal->setRotulo( "Processo Fiscal" );
    $obProcessoFiscal->setTitle( "Informe o Código do Processo Fiscal." );
    $obProcessoFiscal->setNull( true );
}

if ($stAcao != 'incluir') {
    $obSpanTipoInscricao = new Span;
    $obSpanTipoInscricao->setId('spnForm');
}

//Novo Formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnInicio );

if ($stAcao == 'notificar') {
    $obFormulario->addHidden( $obHDTipoFiscalizacao );
}

if ($stAcao != 'incluir') {
    $obFormulario->addTitulo( "Dados para Filtro" );
    $obTipoFiscalizacao->geraFormulario( $obFormulario );
    $obFormulario->addComponente( $obProcessoFiscal );
}

if ($stAcao == 'incluir') {
    $obFormulario->addTitulo        ( "Dados para Processo Fiscal" );
    $obFormulario->addComponente    ( $obCmbTipoFiscalizacao  );
}

if ($stAcao != 'incluir') {
    //Add o Span no formulário
    $obFormulario->addSpan( $obSpanTipoInscricao );
}

$obFormulario->Ok();

if (!$obVisaoProcessoFiscal->getFiscalAtivo()) {
   SistemaLegado::exibeAviso("Fiscal não Habilitado para este tipo de operação.","","erro");
   $obFormulario = new Formulario;
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

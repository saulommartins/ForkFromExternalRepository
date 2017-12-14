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
 * Página de formulário Manter Riscos Fiscais
 * Data de Criação: 10/03/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.06 - Manter Riscos Fiscais
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once CAM_GF_LDO_VISAO . 'VLDOManterRiscoFiscal.class.php';
include_once CAM_GF_LDO_VISAO . 'VLDOManterLDO.class.php';

$stModulo = 'ManterRiscoFiscal';
$pgProc   = 'PR' . $stModulo . '.php';
$pgJS     = 'JS' . $stModulo . '.php';
$pgProc   = 'PR' . $stModulo . '.php';
$pgOcul   = 'OC' . $stModulo . '.php';
$pgList   = 'LS' . $stModulo . '.php';

include_once( $pgJS );

VLDOManterLDO::recuperarInstancia()->recuperarPPA();

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");

if (isset($_REQUEST['inCodRiscoFiscal'])) {
    $obHdnCodRiscoFiscal = new Hidden;
    $obHdnCodRiscoFiscal->setName ('inCodRiscoFiscal');
    $obHdnCodRiscoFiscal->setValue($_REQUEST['inCodRiscoFiscal']);
    $obFormulario->addHidden($obHdnCodRiscoFiscal);
}

$rsLDO    = VLDOManterLDO::recuperarInstancia()->recuperarLDO();
$inAnoLDO = $_REQUEST['inAnoLDO'] ? $_REQUEST['inAnoLDO'] : $rsLDO->getCampo("ano");

$obHdnAno = new Hidden;
$obHdnAno->setName('inAnoLDO');
$obHdnAno->setValue($inAnoLDO);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAno);

$obFormulario->addTitulo('Dados para ' . $stAcao . ' Riscos Fiscais');
$obTxtRiscoFiscal = new TextBox;
$obTxtRiscoFiscal->setName     ( 'stDescRiscoFiscal' );
$obTxtRiscoFiscal->setId       ( 'stDescRiscoFiscal' );
$obTxtRiscoFiscal->setRotulo   ( "Risco Fiscal" );
$obTxtRiscoFiscal->setSize     ( 80 );
$obTxtRiscoFiscal->setMaxLength( 100 );
$obTxtRiscoFiscal->setNull     ( true );
$obTxtRiscoFiscal->setTitle    ( 'Informe o Risco Fiscal' );
$obTxtRiscoFiscal->setObrigatorio( true );
if (isset($_REQUEST['descricao'])) {
    $stDescricao = stripslashes($_REQUEST['descricao']);
    $obTxtRiscoFiscal->setValue(stripslashes($stDescricao));
}
$obFormulario->addComponente($obTxtRiscoFiscal);

$obNumValorRiscoFiscal = new Numerico;
$obNumValorRiscoFiscal->setRotulo    ('Valor');
$obNumValorRiscoFiscal->setTitle     ('Informe o valor do Risco Fiscal');
$obNumValorRiscoFiscal->setName      ('flValorRiscoFiscal');
$obNumValorRiscoFiscal->setId        ('flValorRiscoFiscal');
$obNumValorRiscoFiscal->setDecimais  (2);
$obNumValorRiscoFiscal->setMaxValue  (999999999999.99);
$obNumValorRiscoFiscal->setNegativo  (false);
$obNumValorRiscoFiscal->setNaoZero   (false);
$obNumValorRiscoFiscal->setSize      (14);
$obNumValorRiscoFiscal->setMaxLength (12);
$obNumValorRiscoFiscal->setObrigatorio( true );
if (isset($_REQUEST['valor'])) {
    $stValor = LDOString::retornarValorMonetario($_REQUEST['valor']);
    $obNumValorRiscoFiscal->setValue($stValor);
}
$obFormulario->addComponente($obNumValorRiscoFiscal);

$obFormulario->addTitulo('Dados da Providência');

$obTxtProvidencia = new TextBox;
$obTxtProvidencia->setName     ( 'stDescProvidencia' );
$obTxtProvidencia->setId       ( 'stDescProvidencia' );
$obTxtProvidencia->setRotulo   ( '*Providência' );
$obTxtProvidencia->setSize     ( 80 );
$obTxtProvidencia->setMaxLength( 250 );
$obTxtProvidencia->setNull     ( true );
$obTxtProvidencia->setTitle    ( 'Informe a Providência' );
$obTxtProvidencia->setObrigatorio( false );
$obFormulario->addComponente($obTxtProvidencia);

$obNumValorProvidencia = new Numerico;
$obNumValorProvidencia->setRotulo    ('*Valor');
$obNumValorProvidencia->setTitle     ('Informe o valor da Providência');
$obNumValorProvidencia->setName      ('flValorProvidencia');
$obNumValorProvidencia->setId        ('flValorProvidencia');
$obNumValorProvidencia->setDecimais  (2);
$obNumValorProvidencia->setMaxValue  (999999999999.99);
$obNumValorProvidencia->setNegativo  (false);
$obNumValorProvidencia->setNaoZero   (false);
$obNumValorProvidencia->setSize      (14);
$obNumValorProvidencia->setMaxLength (12);
$obNumValorProvidencia->setObrigatorio( false );
$obFormulario->addComponente($obNumValorProvidencia);

// Lista de Providências
$obBtnIncluirProvidencia = new Button;
$obBtnIncluirProvidencia->setName              ('btnIncluirProvidencia');
$obBtnIncluirProvidencia->setValue             ('Incluir');
$obBtnIncluirProvidencia->setTipo              ('button');
$obBtnIncluirProvidencia->obEvento->setOnClick ("inserirProvidencia();" );
$obBtnIncluirProvidencia->setDisabled          (false);

$obBtnLimparProvidencia = new Button;
$obBtnLimparProvidencia->setName               ('btnLimparProvidencia');
$obBtnLimparProvidencia->setValue              ('Limpar');
$obBtnLimparProvidencia->setTipo               ('button');
$obBtnLimparProvidencia->obEvento->setOnClick  ("limparProvidencia();");
$obBtnLimparProvidencia->setDisabled           (false);
$botoesProvidencia = array ( $obBtnIncluirProvidencia , $obBtnLimparProvidencia);
$obFormulario->agrupaComponentes($botoesProvidencia);

$obSpnListaProvidencia = new Span();
$obSpnListaProvidencia->setID('spnListaProvidencia');
$obFormulario->addSpan($obSpnListaProvidencia);

$obLblTlProvidencia = new Label();
$obLblTlProvidencia->setRotulo('Total');
$obLblTlProvidencia->setTitle ('Total de Providencias');
$obLblTlProvidencia->setID    ('lbTotalProvidencia');
$obLblTlProvidencia->setValue ('&nbsp;');
$obFormulario->addComponente($obLblTlProvidencia);

if ($stAcao == 'incluir') {
    $obFormulario->OK(true);
} else {
    $stLocation = $pgList.'?'.$sessao->id.'&stAcao='.$stAcao.'&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos'].'&inAnoLDO='.$_REQUEST['inAnoLDO'];
    $obFormulario->Cancelar( $stLocation );
}

$obFormulario->show();
if ($stAcao == 'alterar') {
    $jsOnLoad = 'recuperarProvidenciaFiscal();';
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

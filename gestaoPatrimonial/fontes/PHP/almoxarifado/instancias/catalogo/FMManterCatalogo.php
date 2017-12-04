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
    * Página de Formulário Catálogo
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    $Id: FMManterCatalogo.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-03.03.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogo.class.php");
$stPrograma = "ManterCatalogo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$arrayTransf = Sessao::read('transf4');

if ($arrayTransf) {
    $stFiltro = '';

    foreach ($arrayTransf as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stFiltro = isset($stFiltro) ? $stFiltro : null;
$stLocation = $pgList . "?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'].$stFiltro;;

Sessao::write('Valores', array());

$inCount = 0;

$obRAlmoxarifadoCatalogo = new RAlmoxarifadoCatalogo;

$stAcao = $request->get("stAcao");

$inCodigo = $_REQUEST['inCodigo'];
$stDescQuestao = $_REQUEST['stDescQuestao'];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ($_REQUEST['inCodigo']) {
    $obRAlmoxarifadoCatalogo->setCodigo($inCodigo);
    $obRAlmoxarifadoCatalogo->consultar();

    $inCodigo = $obRAlmoxarifadoCatalogo->getCodigo();
    $stDescricaoCatalogo = $obRAlmoxarifadoCatalogo->getDescricao();

    $stLocation .= "&inCodigo=$inCodigo";

    for ($inPos = 0; $inPos < count($obRAlmoxarifadoCatalogo->arCatalogoNivel); $inPos++) {
        $arElementos['inId']            = $inPos;
        $arElementos['nivel']           = $obRAlmoxarifadoCatalogo->arCatalogoNivel[$inPos]->getNivel();
        $arElementos['mascara']         = $obRAlmoxarifadoCatalogo->arCatalogoNivel[$inPos]->getMascara();
        $arElementos['descricao']       = $obRAlmoxarifadoCatalogo->arCatalogoNivel[$inPos]->getDescricao();
        $arr['Valores'][] = $arElementos;
    }

    Sessao::write('Valores',$arr['Valores']);

    $obRAlmoxarifadoCatalogoL = new RAlmoxarifadoCatalogo();
    $obRAlmoxarifadoCatalogoL->setCodigo( $obRAlmoxarifadoCatalogo->getCodigo() );
    $obErro = $obRAlmoxarifadoCatalogoL->verificarClassificacao($obErroVerificar);
    $boDesabilitaBotao = $obErroVerificar->ocorreu();
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCod = new Hidden;
$obHdnCod->setName("inCodigo");
$obHdnCod->setValue($inCodigo);

$obHdnMascara = new hidden();
$obHdnMascara->setId('hdnMascara');
$obHdnMascara->setName('hdnMascara');
$obHdnMascara->setValue('');

$obHdnIDPos = new Hidden;
$obHdnIDPos->setName("inIDPos");

$obLblCodigo= new Label;
$obLblCodigo->setRotulo ( "Código"  );
$obLblCodigo->setValue  ( $inCodigo );

$obTxtDescricaoCatalogo = new TextBox;
$obTxtDescricaoCatalogo->setRotulo        ( "Descrição" );
$obTxtDescricaoCatalogo->setTitle         ( "Informe a descrição do catálogo." );
$obTxtDescricaoCatalogo->setName          ( "stDescricaoCatalogo" );
$obTxtDescricaoCatalogo->setValue         ( $stDescricaoCatalogo  );
$obTxtDescricaoCatalogo->setSize          ( 50 );
$obTxtDescricaoCatalogo->setMaxLength     ( 160 );
$obTxtDescricaoCatalogo->setNull          ( false );

$obTxtMascara = new TextBox;
$obTxtMascara->setRotulo        ( "*Máscara" );
$obTxtMascara->setTitle         ( "Informe a máscara para este nível." );
$obTxtMascara->setName          ( "stMascara" );
$obTxtMascara->setValue         ( isset($stMascara) ? $stMascara : null);
$obTxtMascara->setSize          ( 10 );
$obTxtMascara->setMaxLength     ( 10 );
$obTxtMascara->setInteiro       ( true );
$obTxtMascara->setNull          ( true );
$obTxtMascara->obEvento->setOnKeyUp("verificaTecla(event, this);");

$obTxtDescricaoNivel = new TextBox;
$obTxtDescricaoNivel->setRotulo        ( "*Descrição" );
$obTxtDescricaoNivel->setTitle         ( "Informe a descrição do nível." );
$obTxtDescricaoNivel->setName          ( "stDescricaoNivel" );
$obTxtDescricaoNivel->setId	       ( "stDescricaoNivel" );
$obTxtDescricaoNivel->setValue         ( isset($stDescricaoNivel) ? $stDescricaoNivel : null);
$obTxtDescricaoNivel->setSize          ( 50 );
$obTxtDescricaoNivel->setMaxLength     ( 160 );
$obTxtDescricaoNivel->setNull          ( true );

$obBtnIncluir= new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setId ( "btnIncluir" );
$obBtnIncluir->setTipo ( "button" );
if ($stAcao == 'alterar') {
    $obBtnIncluir->setValue( 'Alterar' );
    $obBtnIncluir->obEvento->setOnClick ( "return AdicionaValores('alteraValor');" );
} else {
    $obBtnIncluir->setValue( "Incluir" );
    $obBtnIncluir->obEvento->setOnClick ( "return AdicionaValores('MontaValoresLista');" );
}

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "button" );
$obBtnLimpar->obEvento->setOnClick ( "limpaValores();" );

$obSpnListaValores = new Span;
$obSpnListaValores->setID('spnListaValores');

$obSpnFormulario = new Span();
$obSpnFormulario->setId('spnFormulario');

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->setAjuda             ("UC-03.03.04"  );
$obFormulario->addHidden            ($obHdnIDPos);
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addHidden            ($obHdnCod);
$obFormulario->addHidden            ($obHdnMascara);

$obFormulario->addTitulo            ( "Dados do Catálogo" );

if ($stAcao == 'alterar') {
    $obFormulario->addComponente       ($obLblCodigo);
}

$obFormulario->addComponente        ( $obTxtDescricaoCatalogo );

$obFormulario->addSpan      ( $obSpnFormulario );
$obFormulario->addSpan      ( $obSpnListaValores );

if ($stAcao == 'alterar') {
    SistemaLegado::ExecutaFrameOculto("redirecionaPagina( '$pgOcul?".Sessao::getId()."boDesabilitaBotao=$boDesabilitaBotao', 'frm' , 'MontaValoresListaAltera'  );");
}

if ($stAcao=="incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar( $stLocation );
}

$obFormulario->show();

include_once($pgJs);

echo "<script type='text/javascript'>montaParametrosGET('montaFormulario','inCodigo',true);</script>";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

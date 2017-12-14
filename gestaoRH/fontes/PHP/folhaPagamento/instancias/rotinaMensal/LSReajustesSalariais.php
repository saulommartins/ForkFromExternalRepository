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
    * Arquivo de Lista
    * Data de Criação: 27/09/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30849 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-02 14:35:36 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-04.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                     );

//Define o nome dos arquivos PHP
$stPrograma = "ReajustesSalariais";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";
$pgProcRel = "PR".$stPrograma."Relatorio.php";

$stCaminho = CAM_GRH_FOL_INSTANCIAS."Padrao/";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a ação
$stAcao = $request->get('stAcao');

//Define a página
$pgProx = $pgProc;

if (trim($stAcao)=="excluir") {
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajuste.class.php");
    list($inCodReajuste, $stOrigem) = explode("*_*", $_POST["inCodReajuste"]);
    $stFiltro = " WHERE cod_reajuste = ".$inCodReajuste;
    $obTFolhaPagamentoReajuste = new TFolhaPagamentoReajuste();
    $obTFolhaPagamentoReajuste->recuperaRelacionamento($rsReajuste, $stFiltro, "cod_reajuste");

    $stTipoReajuste       = $rsReajuste->getCampo("tipo_reajuste");
    $nuValorReajuste      = $rsReajuste->getCampo("valor");
    $nuPercentualReajuste = $rsReajuste->getCampo("percentual");
    $nuFaixaInicial       = $rsReajuste->getCampo("faixa_inicial");
    $nuFaixaFinal         = $rsReajuste->getCampo("faixa_final");

    if ($stTipoReajuste == 'v') {
        $stObservacao = "Reajuste salarial a partir de ".$rsReajuste->getCampo("dt_reajuste");
    } else {
        $stObservacao = "Reajuste salarial de ".$rsReajuste->getCampo("percentual")."% a partir de ".$rsReajuste->getCampo("dt_reajuste");
    }
    Sessao::write("stObservacao", $stObservacao);
} else {
    $stTipoReajuste             = $_POST["stTipoReajuste"];
    $nuValorReajuste            = str_replace(",", ".", str_replace(".", "", $_POST["nuValorReajuste"]));
    $nuPercentualReajuste       = str_replace(",", ".", str_replace(".", "", $_POST["nuPercentualReajuste"]));
    $nuFaixaInicial             = str_replace(",", ".", str_replace(".", "", $_POST["nuFaixaInicial"]));
    $nuFaixaFinal               = str_replace(",", ".", str_replace(".", "", $_POST["nuFaixaFinal"]));
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao."&inAba=".$request->get('inAba')."&inNumCGM=".$request->get('inNumCGM');
$link = Sessao::read("link");
if ($request->get('pg') and  $request->get('pos')) {
    $stLink.= "&pg=".$request->get('pg')."&pos=".$request->get('pos');
    $link["pg"]  = $request->get('pg');
    $link["pos"] = $request->get('pos');
    Sessao::write("link",$link);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($request->getAll() as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}
foreach ($request->getAll() as $key => $valor) {
    Sessao::write($key,$valor);
}

if (trim($stAcao) == "incluir") {
    $jsOnload = "BloqueiaFrames(true,false);
                 executaFuncaoAjax('gerarSpanReajustesSalariais');";
} elseif (trim($stAcao) == "excluir") {
    $obLblReajuste = new Label();
    $obLblReajuste->setRotulo("Reajuste");
    $obLblReajuste->setValue($rsReajuste->getCampo("cod_reajuste"));

    $obBtnMarcaTodos = new Ok();
    $obBtnMarcaTodos->setValue("Marca Todos");
    $obBtnMarcaTodos->obEvento->setOnClick("montaParametrosGET('marcaTodos', 'stAcao');");
    $obBtnMarcaTodos->setDisabled(true);
    $obBtnMarcaTodos->setStyle("width:110px");
    $obBtnMarcaTodos->setId("marcaTodos");

    $obBtnDesmarcaTodos = new Ok();
    $obBtnDesmarcaTodos->setValue("Desmarca Todos");
    $obBtnDesmarcaTodos->obEvento->setOnClick("montaParametrosGET('desmarcaTodos', 'stAcao');");
    $obBtnDesmarcaTodos->setDisabled(true);
    $obBtnDesmarcaTodos->setStyle("width:110px");
    $obBtnDesmarcaTodos->setId("desmarcaTodos");

    $jsOnload = "BloqueiaFrames(true,false);
                 executaFuncaoAjax('gerarSpanReajustesSalariaisExclusao');";
}

$obForm = new Form;
$obForm->setAction ( $pgProcRel  );
$obForm->setTarget("telaPrincipal");

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

$obLblPercentualValor = new Label();
$obLblPercentualValor->setRotulo("Reajuste em");
$obLblPercentualValor->setValue($stTipoReajuste=='v'?"Valor":"Percentual");

if (isset($_REQUEST['nuValorReajuste'])) {
    $obLblValor = new Label();
    $obLblValor->setRotulo("Valor de Reajuste");
    $obLblValor->setValue(number_format($nuValorReajuste, 2, ",", "."));
}

if (isset($_REQUEST['nuPercentualReajuste']) || isset($_REQUEST['inCodReajuste'])) {
    $obLblPercentual = new Label();
    $obLblPercentual->setRotulo("Percentual de Reajuste");
    $obLblPercentual->setValue(number_format($nuPercentualReajuste, 4, ",", "."));
}

$obLblFaixa = new Label();
$obLblFaixa->setRotulo("Faixa para Reajuste");
$obLblFaixa->setValue(number_format($nuFaixaInicial,2,",",".") ." até ". number_format($nuFaixaFinal,2,",",".") );

$obSpnReajustesSalariais = new Span();
$obSpnReajustesSalariais->setId("spnReajustesSalariais");

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeterSimulacao');");
$obBtnOk->setDisabled(true);
$obBtnOk->setId("ok");

$obBtnExcluir = new Ok();
$obBtnExcluir->setValue("Excluir");
$obBtnExcluir->setName("btnExcluir");
$obBtnExcluir->setId("btnExcluir");
$obBtnExcluir->obEvento->setOnClick("montaParametrosGET('removerReajuste', 'stAcao');");
$obBtnExcluir->setDisabled(true);

$obBtnImprimir = new Ok();
$obBtnImprimir->setValue("Imprimir");
$obBtnImprimir->setName("imprimir");
$obBtnImprimir->setId("imprimir");
$obBtnImprimir->setDisabled(true);

$stLocation = $pgForm."?".Sessao::getId()."&stAcao=".$stAcao;
$obBtnCancelar = new Button();
$obBtnCancelar->setValue("Cancelar");
$obBtnCancelar->setId("cancelar");
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."');");
$obBtnCancelar->setDisabled(true);

$obFormulario = new Formulario();
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
if (trim($stAcao) == "excluir") {
    $obFormulario->addComponente($obLblReajuste);
}
$obFormulario->addComponente($obLblPercentualValor);
if ($stTipoReajuste == 'v') {
    $obFormulario->addComponente($obLblValor);
} else {
    $obFormulario->addComponente($obLblPercentual);
}
$obFormulario->addComponente($obLblFaixa);
$obFormulario->addSpan($obSpnReajustesSalariais);
if (trim($stAcao)=="incluir") {
    $obFormulario->defineBarra(array($obBtnOk,$obBtnImprimir,$obBtnCancelar),"","");
} else {
    $obFormulario->defineBarra(array($obBtnExcluir,$obBtnImprimir,$obBtnCancelar,$obBtnMarcaTodos,$obBtnDesmarcaTodos),"","");
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
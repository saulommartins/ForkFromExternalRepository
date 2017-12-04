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
    * Página de Lista para Manter Vinculo de Escalas
    * Data de Criação: 11/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaContrato.class.php"                           );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$stCaminho  = CAM_GRH_PON_INSTANCIAS."escalaHorario/";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                         );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Mantem filtro e paginacao
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        IF(!in_array($key,array('hdnTipoFiltro','hdnValidaMatriculas'))){
            $link[$key] = $valor;
            $_REQUEST   = $link;
        }
    }
    Sessao::write("link",$link);
}
$stAcao = $request->get('stAcao');
$stLink .= "&stAcao=".$stAcao;

//Monta o filtro
$stFiltro = "";
$rsContratos = new RecordSet();
$obTPontoEscalaContratos = new TPontoEscalaContrato();

switch ($_REQUEST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stCodContratos = "";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodContratos .= $arContrato["cod_contrato"].",";
        }
        $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
        $stRetorno   = 'contrato';
        $obTPontoEscalaContratos->setDado('stCodigos', $stCodContratos);
        break;
    case "lotacao":
        $stCodOrgao  = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
        $stRetorno   = 'lotacao';
        $obTPontoEscalaContratos->setDado('stCodigos', $stCodOrgao);
        break;
    case "local":
        $stCodLocal  = implode(",",$_REQUEST["inCodLocalSelecionados"]);
        $stRetorno   = 'local';
        $obTPontoEscalaContratos->setDado('stCodigos', $stCodLocal);
        break;
    case "sub_divisao_funcao":
        $stCodSubDivisaoFuncao = implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"]);
        $stRetorno   = 'sub_divisao_funcao';
        $obTPontoEscalaContratos->setDado('stCodigos', $stCodSubDivisaoFuncao);
        break;
}

if ($_REQUEST['inCodEscala']) {
    $obTPontoEscalaContratos->setDado('inCodEscala', $_REQUEST['inCodEscala']);
}

if ($_REQUEST['dtInicioPeriodo']) {
    $obTPontoEscalaContratos->setDado('dtInicioPeriodo', $_REQUEST['dtInicioPeriodo']);
}

if ($_REQUEST['dtFimPeriodo']) {
    $obTPontoEscalaContratos->setDado('dtFimPeriodo', $_REQUEST['dtFimPeriodo']);
}

$obTPontoEscalaContratos->setDado('stRetorno', $stRetorno);
$obTPontoEscalaContratos->recuperaContratosDetalhadosEscala($rsContratos);

$obLista = new Lista;
$obLista->setRecordSet( $rsContratos );

$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

switch ($stRetorno) {
    case "contrato":
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Matrícula" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "registro" );
        $obLista->commitDado();

        $stLabel = "Nome";
        $stCampo = "nom_cgm";
        $stChave = "[cod_escala]_[cod_contrato]";
        break;

    case "lotacao":
        $stLabel = "Lotação";
        $stCampo = "[mascara_orgao] - [descricao_orgao]";
        $stChave = "[cod_escala]_[cod_orgao]";
        break;

    case "local":
        $stLabel = "Local";
        $stCampo = "[cod_local_formatado] - [descricao_local]";
        $stChave = "[cod_escala]_[cod_local]";
        break;

    case "sub_divisao_funcao":
        $stLabel = "Regime / Subdivisão / Função";
        $stCampo = "[descricao_regime] / [descricao_sub_divisao] / [descricao_cargo]";
        $stChave = "[cod_escala]_[cod_regime]_[cod_sub_divisao]_[cod_cargo]";
        break;
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( $stLabel );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( $stCampo );
$obLista->commitDado();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Escala" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_escala_formatado" );
$obLista->commitDado();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Período da Escala" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[min_turno] à [max_turno]" );
$obLista->commitDado();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'consultar' );
$obLista->ultimaAcao->addCampo( "&stChave", $stChave );
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stRetorno=".$stRetorno );
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'consultar' );
$obLista->ultimaAcao->setFuncao("true");
$obLista->ultimaAcao->setLink("javaScript:processarPopUp('".$_REQUEST["stTipoFiltro"]."','".$_REQUEST['dtInicioPeriodo']."','".$_REQUEST['dtFimPeriodo']."')");
$obLista->ultimaAcao->addCampo("&codigo"          ,"codigo");
$obLista->commitAcao();

if ($stAcao == "consultar") {
    $obLista->show();
}

if ($stAcao == "excluir") {

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obHdnFiltro = new Hidden;
    $obHdnFiltro->setName("stTipoFiltro");
    $obHdnFiltro->setValue($_REQUEST["stTipoFiltro"]);

    $obCkbMarcar = new checkbox;
    $obCkbMarcar->setName('excluirVinculoEscala_'.$stChave.'_');
    $obCkbMarcar->obEvento->setOnClick( " habilitaBotaoVinculo(); " );

    $obLista->addDadoComponente($obCkbMarcar);
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->ultimoDado->setCampo( "booleano" );
    $obLista->commitDadoComponente();

    $obLista->setMostraSelecionaTodos(true);
    $obLista->montaHTML();

    $obHdnAcao =  new Hidden;
    $obHdnAcao->setName( "stAcao" );
    $obHdnAcao->setValue( $_REQUEST["stAcao"] );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName( "stCtrl" );
    $obHdnCtrl->setValue( $stCtrl );

    $obSpan = new Span();
    $obSpan->setId("spnLista");
    $obSpan->setValue($obLista->getHTML());

    $obBotao = new Ok();
    $obBotao->setId("btManterVinculo");
    $obBotao->setValue(ucwords($stAcao));
    $obBotao->setDisabled(true);

    $stLocation = $pgFilt."?".Sessao::getId()."&stAcao=".$stAcao;
    $obCancelar = new Cancelar();
    $obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."');");

    //Instancia o form
    $obForm = new Form;
    $obForm->setAction( $pgProc  );
    $obForm->setTarget( "oculto" );

    $obFormulario = new Formulario;
    $obFormulario->addHidden( $obHdnAcao );
    $obFormulario->addHidden( $obHdnCtrl );
    $obFormulario->addHidden( $obHdnFiltro );
    $obFormulario->addForm( $obForm );
    $obFormulario->addSpan($obSpan);
    $obFormulario->defineBarra(array($obBotao,$obCancelar),"","");
    $obFormulario->show();
}

include_once ($pgJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

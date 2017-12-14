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
/*
    * Lista para cadastro de compensações de horas
    * Data de Criação   : 06/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      				);

//Define o nome dos arquivos PHP
$stPrograma = "ManterCompensacaoHoras";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";
include($pgJS);
$stCaminho = CAM_GRH_PON_INSTANCIAS."compensacoes/";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read("link");
$stLink = Sessao::getId()."&stAcao=".$stAcao;

if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($arLink) ) {
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}
Sessao::write("link",$arLink);

$stLink .= "&stTipoFiltro=".$_REQUEST["stTipoFiltro"];
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm."?".$stLink; break;
    case 'excluir': $pgProx = $pgProc."?".$stLink; break;
    DEFAULT       : $pgProx = $pgForm."?".$stLink;
}

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoCompensacaoHoras.class.php");
$obTPontoCompensacaoHoras = new TPontoCompensacaoHoras();

if (trim($_REQUEST["dtFaltaInicial"]) != "") {
    $ardtFaltaInicial = explode("/",$_REQUEST["dtFaltaInicial"]);
    $dtFaltaInicial   = $ardtFaltaInicial[2]."-".$ardtFaltaInicial[1]."-".$ardtFaltaInicial[0];
}
if (trim($_REQUEST["dtFaltaFinal"]) != "") {
    $ardtFaltaFinal = explode("/",$_REQUEST["dtFaltaFinal"]);
    $dtFaltaFinal   = $ardtFaltaFinal[2]."-".$ardtFaltaFinal[1]."-".$ardtFaltaFinal[0];
}
if (trim($_REQUEST["dtCompensacaoInicial"]) != "") {
    $ardtCompensacaoInicial = explode("/",$_REQUEST["dtCompensacaoInicial"]);
    $dtCompensacaoInicial   = $ardtCompensacaoInicial[2]."-".$ardtCompensacaoInicial[1]."-".$ardtCompensacaoInicial[0];
}
if (trim($_REQUEST["dtCompensacaoFinal"]) != "") {
    $ardtCompensacaoFinal = explode("/",$_REQUEST["dtCompensacaoFinal"]);
    $dtCompensacaoFinal   = $ardtCompensacaoFinal[2]."-".$ardtCompensacaoFinal[1]."-".$ardtCompensacaoFinal[0];
}
$rsCompesacoes = new recordset;
switch ($_REQUEST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        $stOrdem = " ORDER BY nom_cgm,registro";
        $arContratos = Sessao::read("arContratos");
        $stCodContrato = "";
        foreach ($arContratos as $arContrato) {
            $stCodContrato .= $arContrato["cod_contrato"].",";
        }
        $stCodContrato = substr($stCodContrato,0,strlen($stCodContrato)-1);
        if (trim($dtFaltaInicial) != "" and trim($dtFaltaFinal) != "") {
            $stFiltro .= "   AND compensacao_horas.dt_falta BETWEEN '".trim($dtFaltaInicial)."' AND '".trim($dtFaltaFinal)."'";
        }
        if (trim($dtFaltaInicial) != "" and trim($dtFaltaFinal) == "") {
            $stFiltro .= "   AND compensacao_horas.dt_falta = '".trim($dtFaltaInicial)."'";
        }
        if (trim($dtFaltaInicial) == "" and trim($dtFaltaFinal) != "") {
            $stFiltro .= "   AND compensacao_horas.dt_falta = '".trim($dtFaltaFinal)."'";
        }
        if (trim($dtCompensacaoInicial) != "" and trim($dtCompensacaoFinal) != "") {
            $stFiltro .= "   AND compensacao_horas.dt_compensacao BETWEEN '".trim($dtCompensacaoInicial)."' AND '".trim($dtCompensacaoFinal)."'";
        }
        if (trim($dtCompensacaoInicial) != "" and trim($dtCompensacaoFinal) == "") {
            $stFiltro .= "   AND compensacao_horas.dt_compensacao = '".trim($dtCompensacaoInicial)."'";
        }
        if (trim($dtCompensacaoInicial) == "" and trim($dtCompensacaoFinal) != "") {
            $stFiltro .= "   AND compensacao_horas.dt_compensacao = '".trim($dtCompensacaoFinal)."'";
        }

        $obTPontoCompensacaoHoras->setDado('stTipoFiltro', $_REQUEST["stTipoFiltro"]);
        $obTPontoCompensacaoHoras->setDado('stCodigos', $stCodContrato);
        $obTPontoCompensacaoHoras->recuperaRelacionamento($rsCompesacoes,$stFiltro,$stOrdem);
        break;
    case "lotacao":
        $stRotulo = "Lotação";
        $stOrdem = " ORDER BY descricao,dt_falta,dt_compensacao";
        $obTPontoCompensacaoHoras->setDado('stTipoFiltro', $_REQUEST["stTipoFiltro"]);
        $obTPontoCompensacaoHoras->setDado("stCodigos",implode(",",$_REQUEST["inCodLotacaoSelecionados"]));
        $obTPontoCompensacaoHoras->setDado("dt_falta_inicial",$dtFaltaInicial);
        $obTPontoCompensacaoHoras->setDado("dt_falta_final",$dtFaltaFinal);
        $obTPontoCompensacaoHoras->setDado("dt_compensacao_inicial",$dtCompensacaoInicial);
        $obTPontoCompensacaoHoras->setDado("dt_compensacao_final",$dtCompensacaoFinal);
        $obTPontoCompensacaoHoras->recuperaCompesacoesLotacao($rsCompesacoes,$stFiltro,$stOrdem);
        break;
    case "local":
        $stRotulo = "Local";
        $stOrdem = "ORDER BY codigo,dt_falta,dt_compensacao";
        $obTPontoCompensacaoHoras->setDado('stTipoFiltro', $_REQUEST["stTipoFiltro"]);
        $obTPontoCompensacaoHoras->setDado("stCodigos",implode(",",$_REQUEST["inCodLocalSelecionados"]));
        $obTPontoCompensacaoHoras->setDado("dt_falta_inicial",$dtFaltaInicial);
        $obTPontoCompensacaoHoras->setDado("dt_falta_final",$dtFaltaFinal);
        $obTPontoCompensacaoHoras->setDado("dt_compensacao_inicial",$dtCompensacaoInicial);
        $obTPontoCompensacaoHoras->setDado("dt_compensacao_final",$dtCompensacaoFinal);
        $obTPontoCompensacaoHoras->recuperaCompesacoesLocal($rsCompesacoes,$stFiltro,$stOrdem);
        break;
    case "reg_sub_fun_esp":
        $stRotulo = "Regime/Subdivisão/Função";
        $stOrdem = "ORDER BY descricao,dt_falta,dt_compensacao";
        $obTPontoCompensacaoHoras->setDado('stTipoFiltro', $_REQUEST["stTipoFiltro"]);
        $stCodigos  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
        $stCodigos .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
         $stCodigos .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
        }
        $obTPontoCompensacaoHoras->setDado("stCodigos",$stCodigos);
        $obTPontoCompensacaoHoras->setDado("dt_falta_inicial",$dtFaltaInicial);
        $obTPontoCompensacaoHoras->setDado("dt_falta_final",$dtFaltaFinal);
        $obTPontoCompensacaoHoras->setDado("dt_compensacao_inicial",$dtCompensacaoInicial);
        $obTPontoCompensacaoHoras->setDado("dt_compensacao_final",$dtCompensacaoFinal);
        $obTPontoCompensacaoHoras->recuperaCompesacoesFuncao($rsCompesacoes,$stFiltro,$stOrdem);

        break;
}

$obLista = new Lista;
$obLista->setRecordset( $rsCompesacoes );
$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

if ($_REQUEST["stTipoFiltro"] == "contrato" or $_REQUEST["stTipoFiltro"] == "cgm_contrato") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Dia da Falta" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Compensação" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_falta" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_compensacao" );
    $obLista->commitDado();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo($stRotulo);
    $obLista->ultimoCabecalho->setWidth( 50 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Dia da Falta" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Compensação" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_falta" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "dt_compensacao" );
    $obLista->commitDado();
}

if ($stAcao == "excluir" or $stAcao == "alterar") {
    if ($stAcao == "excluir") {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Marcar");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obHdnFiltro = new Hidden;
        $obHdnFiltro->setName("stTipoFiltro");
        $obHdnFiltro->setValue($_REQUEST["stTipoFiltro"]);

        $obCkbMarcar = new checkbox;
        $obCkbMarcar->setName("excluirCompensacao_[codigo]_[dt_falta]_[dt_compensacao]_");

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

        $obExcluir = new Ok();
        $obExcluir->setValue("Excluir");
        if ($rsCompesacoes->getNumLinhas() == -1) {
            $obExcluir->setDisabled(true);
        }

        $stLocation = $pgFilt."?".Sessao::getId()."&stAcao=".$stAcao;
        $obCancelar = new Cancelar();
        $obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."');");

        //Instancia o form
        $obForm = new Form;
        $obForm->setAction      ( $pgProc  );
        $obForm->setTarget      ( "oculto" );

        $obFormulario = new Formulario;
        $obFormulario->addHidden( $obHdnAcao );
        $obFormulario->addHidden( $obHdnCtrl );
        $obFormulario->addHidden( $obHdnFiltro );
        $obFormulario->addForm( $obForm );
        $obFormulario->addSpan($obSpan);
        $obFormulario->defineBarra(array($obExcluir,$obCancelar),"","");
        $obFormulario->show();

    }
    if ($stAcao == "alterar") {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo("&codigo"     ,"codigo");
        $obLista->ultimaAcao->addCampo("&descricao"  ,"descricao");
        $obLista->ultimaAcao->addCampo("&dt_falta"   ,"dt_falta");
        $obLista->ultimaAcao->addCampo("&dt_compensacao"  ,"dt_compensacao");
        $obLista->ultimaAcao->setLink( $pgProx );
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "visualizar" );
        $obLista->ultimaAcao->setFuncao("true");
        $obLista->ultimaAcao->setLink("javaScript:processarPopUp('".$_REQUEST["stTipoFiltro"]."')");
        $obLista->ultimaAcao->addCampo("&codigo"          ,"codigo");
        $obLista->ultimaAcao->addCampo("&descricao"       ,"descricao");
        $obLista->ultimaAcao->addCampo("&dt_falta"        ,"dt_falta");
        $obLista->ultimaAcao->addCampo("&dt_compensacao"  ,"dt_compensacao");
        $obLista->commitAcao();

        $obLista->show();
    }
}
if ($stAcao == "consultar") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "visualizar" );
    $obLista->ultimaAcao->setFuncao("true");
    $obLista->ultimaAcao->setLink("javaScript:processarPopUp('".$_REQUEST["stTipoFiltro"]."')");
    $obLista->ultimaAcao->addCampo("&codigo"          ,"codigo");
    $obLista->ultimaAcao->addCampo("&descricao"       ,"descricao");
    $obLista->ultimaAcao->addCampo("&dt_falta"        ,"dt_falta");
    $obLista->ultimaAcao->addCampo("&dt_compensacao"  ,"dt_compensacao");
    $obLista->commitAcao();

    $obLista->show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

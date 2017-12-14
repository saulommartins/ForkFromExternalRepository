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
    * Página de Lista do Férias
    * Data de Criação: 08/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32200 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.04.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterCadastroFerias";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
//sistemalegado::BloqueiaFrames();
flush();
include_once($pgJS);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCaminho = CAM_GRH_PES_INSTANCIAS."ferias/";

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read("link");
$stLink = Sessao::getId()."&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink .= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
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

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm."?".$stLink; break;
    case 'excluir': $pgProx = $pgProc."?".$stLink; break;
    DEFAULT       : $pgProx = $pgForm."?".$stLink;
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_REQUEST["inAno"]);
if ($stAcao != "consultar") 
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_REQUEST["inCodMes"]);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

if ($rsPeriodoMovimentacao->getNumLinhas() == -1) {
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
}

$stValoresFiltro = "";
$stFiltro = "";
switch ($_REQUEST['stTipoFiltro']) {
    case "contrato":
    case "cgm_contrato":
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stValoresFiltro = "";
        $arContratos = Sessao::read("arContratos");
        foreach ($arContratos as $arContrato) {
            $stValoresFiltro .= $arContrato["cod_contrato"].",";
        }
        $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
    break;
    case "funcao":
        $stValoresFiltro = implode(",",$_REQUEST["inCodFuncaoSelecionados"]);
    break;
    case "lotacao":
        $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
    break;
    case "local":
        $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
    break;
}

if ($stAcao == "incluir") {
    $stFiltro = " AND recuperarSituacaoDoContrato(concederFerias.cod_contrato,".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao").",'".Sessao::getEntidade()."') = 'A'";
}
if ($stAcao == "consultar") {
    $stWhereAnd = "\n AND ";
    if(isset($_REQUEST['boConsultarCompetencia'])&&$_REQUEST['boConsultarCompetencia']==true){
        $stFiltro = $stWhereAnd."concederFerias.ano_competencia = '".$_REQUEST['inAno']."'";

        if($_REQUEST['inCodMes']!= "")
            $stFiltro .= $stWhereAnd."concederFerias.mes_competencia::integer = ".$_REQUEST['inCodMes'];
    }
    if (trim($_REQUEST['stDataInicial']) != "" and trim($_REQUEST['stDataFinal']) != "") {
        $stFiltro .= $stWhereAnd."( concederFerias.dt_inicio BETWEEN to_date('".$_REQUEST['stDataInicial']."','dd/mm/yyyy') and to_date('".$_REQUEST['stDataFinal']."','dd/mm/yyyy')";
        $stFiltro .= "              OR concederFerias.dt_fim    BETWEEN to_date('".$_REQUEST['stDataInicial']."','dd/mm/yyyy') and to_date('".$_REQUEST['stDataFinal']."','dd/mm/yyyy'))";
    }
}

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
$obTPessoalFerias = new TPessoalFerias();
$obTPessoalFerias->setDado("stAcao"                     ,$_REQUEST["stAcao"]);
$obTPessoalFerias->setDado("stTipoFiltro"               ,$_REQUEST["stTipoFiltro"]);
$obTPessoalFerias->setDado("stValoresFiltro"            ,$stValoresFiltro);
$obTPessoalFerias->setDado("inCodPeriodoMovimentacao"   ,$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$obTPessoalFerias->setDado("boFeriasVencidas"           ,(trim($_REQUEST['boApresentarSomenteFerias']) != "") ? 'true' : 'false');
$obTPessoalFerias->setDado("inCodLote"                  ,(trim($_REQUEST["inCodLote"]) != "") ? $_REQUEST["inCodLote"] : 0);
$obTPessoalFerias->setDado("inCodRegime"                ,0);
$obTPessoalFerias->concederFerias($rsLista,$stFiltro,"\n ORDER BY concederFerias.nom_cgm, concederFerias.dt_inicial_aquisitivo, concederFerias.dt_final_aquisitivo");

//adicionado para poder verificar a quantidade de registros
Sessao::write('arListaFerias',$rsLista->getElementos());

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$stTitulo = ' </div></td></tr><tr><td colspan="8" class="alt_dados">Matrículas';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Servidor" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lotação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Período Aquisitivo" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

if ($_REQUEST['stAcao'] == "consultar" or  $_REQUEST['stAcao'] == "excluir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Competência de Pagamento" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Férias a Gozar em" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Situação das Férias" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[orgao]-[desc_orgao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[dt_inicial_aquisitivo_formatado] a [dt_final_aquisitivo_formatado]" );
$obLista->commitDado();

if ($_REQUEST['stAcao'] == "consultar" or  $_REQUEST['stAcao'] == "excluir") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "competencia" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodFerias"            , "cod_ferias" );
$obLista->ultimaAcao->addCampo( "&inCodContrato"          , "cod_contrato" );
$obLista->ultimaAcao->addCampo( "&inRegistro"             , "registro");
$obLista->ultimaAcao->addCampo( "&inNumCGM"               , "numcgm");
$obLista->ultimaAcao->addCampo( "&stNomCGM"               , "nom_cgm");
$obLista->ultimaAcao->addCampo( "&inCodEstrutural"        , "orgao");
$obLista->ultimaAcao->addCampo( "&stDescLotacao"          , "desc_orgao");
$obLista->ultimaAcao->addCampo( "&stDescFuncao"           , "desc_funcao");
$obLista->ultimaAcao->addCampo( "&stDescRegime"           , "desc_regime_funcao");
$obLista->ultimaAcao->addCampo( "&inCodRegime"            , "cod_regime_funcao");
$obLista->ultimaAcao->addCampo( "&dtInicial"              , "dt_inicial_aquisitivo_formatado");
$obLista->ultimaAcao->addCampo( "&dtFinal"                , "dt_final_aquisitivo_formatado");
$obLista->ultimaAcao->addCampo( "&boFeriasCadastradas"    , "bo_cadastradas");

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "&inAnoCompetencia"   , "ano_competencia");
    $obLista->ultimaAcao->addCampo( "&inMesCompetencia"   , "mes_competencia");
    $obLista->ultimaAcao->addCampo( "&stDescQuestao"      , "Competência [mes_competencia]/[ano_competencia] - Matrícula [registro]");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx );
}
if ($stAcao == "consultar") {
    $obLista->ultimaAcao->setFuncao(true);
    $obLista->ultimaAcao->setLink("javascript: abrePopUpConsulta();");
}
if ($stAcao == "incluir") {
    $obLista->ultimaAcao->setLink( $pgProx );
}
$obLista->commitAcao();
$obLista->show();

sistemalegado::LiberaFrames();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

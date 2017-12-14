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
    * Formulário
    * Data de Criação: 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    $Id:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once( CAM_GRH_PON_MAPEAMENTO."TPontoDadosRelogioPonto.class.php"                                );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPonto";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = CAM_GRH_PON_INSTANCIAS."manutencao/";

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
        if (!in_array($key,array('hdnTipoFiltro','hdnValidaMatriculas','hdnTipoManutencao'))) {
            $arLink[$key] = $valor;
            $_REQUEST     = $arLink;
        }
    }
}
Sessao::write("link",$arLink);

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm."?".$stLink; break;
    case 'excluir': $pgProx = $pgProc."?".$stLink; break;
    DEFAULT       : $pgProx = $pgForm."?".$stLink;
}

$stTipoFiltro = trim($_REQUEST["stTipoFiltro"]);
switch ($stTipoFiltro) {
    case "cgm_contrato":
    case "contrato":
        $arContratos = Sessao::read("arContratos");
        foreach ($arContratos as $chave => $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        break;
    case "local":
        $stCodigos = implode(",", $_REQUEST["inCodLocalSelecionados"]);
        break;
    case "lotacao":
        $stCodigos = implode(",", $_REQUEST["inCodLotacaoSelecionados"]);
        break;
    case "sub_divisao_funcao":
        $stCodigos = implode(",", $_REQUEST["inCodSubDivisaoSelecionadosFunc"]);
        break;
}
$stCodigos = rtrim($stCodigos, ",");

$stFiltro  = "";
if (trim($_REQUEST["boOrdenacao"]) == "NOME") {
    $stOrdem = " ORDER BY nom_cgm";
} else {
    $stOrdem = " ORDER BY registro";
}

$obTPontoDadosRelogioPonto = new TPontoDadosRelogioPonto();
$obTPontoDadosRelogioPonto->setDado($stTipoFiltro, $stCodigos);
$obTPontoDadosRelogioPonto->recuperaDadosContratoServidor($rsDadosRelogioPonto, $stFiltro, $stOrdem);

$obLista = new Lista;
$obLista->setRecordSet( $rsDadosRelogioPonto );

$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Função" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "registro" );
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "funcao" );
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodContrato","cod_contrato");

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

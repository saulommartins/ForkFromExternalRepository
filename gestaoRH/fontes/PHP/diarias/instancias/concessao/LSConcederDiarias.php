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
    * Página de Lista para Concessão de Diárias
    * Data de Criação: 05/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: LSConcederDiarias.php 59875 2014-09-17 13:49:54Z jean $

    * Casos de uso: uc-04.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_DIA_MAPEAMENTO."TDiariasDiaria.class.php"                                 );
include_once( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederDiarias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
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
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

$stAcao = $request->get('stAcao');
$stLink .= "&stAcao=".$stAcao;

//Monta o filtro
$rsDiarias = new RecordSet();

if ($stAcao == "conceder") {

    $stFiltro = "";
    if ( strlen($_REQUEST["inNumCGM"]) > 0 ) {
        $stFiltro .= " AND sw_cgm.numcgm = ".$_REQUEST["inNumCGM"];
    }
    if ( strlen($_REQUEST["inContrato"]) > 0 ) {
        $stFiltro .= " AND contrato.registro = ".$_REQUEST['inContrato'];
    }

    $obTDiariasDiaria = new TDiariasDiaria();
    $obTDiariasDiaria->setDado('ativos', true);
    $obTDiariasDiaria->recuperaListaContratos($rsDiarias, $stFiltro);

} else {

    $stFiltro = "";
    if ( strlen($_REQUEST["inNumCGM"]) > 0 ) {
        $stFiltro .= " AND sw_cgm.numcgm = ".$_REQUEST["inNumCGM"];
    }
    if ( strlen($_REQUEST["inContrato"]) > 0 ) {
        $stFiltro .= " AND contrato.registro = ".$_REQUEST['inContrato'];
    }

    /*
    if ( strlen($_REQUEST["dtPagamentoInicial"]) > 0 ) {
        $stFiltro .= " AND diaria.dt_pagamento >= TO_DATE('".$_REQUEST['dtPagamentoInicial']."' , 'dd/mm/yyyy')";
    }

    if ( strlen($_REQUEST["dtPagamentoFinal"]) > 0 ) {
        $stFiltro .= " AND diaria.dt_pagamento <= TO_DATE('".$_REQUEST['dtPagamentoInicial']."' , 'dd/mm/yyyy')";
    }
    */

    if ( strlen($_REQUEST["dtInicioViagem"]) > 0 ) {
        $stFiltro .= " AND diaria.dt_inicio >= TO_DATE('".$_REQUEST['dtInicioViagem']."', 'dd/mm/yyyy')";
    }

    if ( strlen($_REQUEST["dtTerminoViagem"]) > 0 ) {
        $stFiltro .= " AND diaria.dt_termino <= TO_DATE('".$_REQUEST['dtTerminoViagem']."', 'dd/mm/yyyy')";
    }

    if ( strlen($_REQUEST["inCodTipoDiaria"]) > 0 ) {
        $stFiltro .= " AND diaria.cod_tipo = ".$_REQUEST['inCodTipoDiaria'];
    }

    $obTDiariasDiaria = new TDiariasDiaria();
    $obTDiariasDiaria->recuperaRelacionamento($rsDiarias, $stFiltro, " ORDER BY nom_cgm, diaria.dt_inicio ");

    $rsDiarias->addFormatacao('vl_total', 'NUMERIC_BR');
}

$obLista = new Lista;
$obLista->setRecordSet( $rsDiarias );

$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lotação" );
$obLista->ultimoCabecalho->setWidth( 30 );
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
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_estrutural] - [descricao_lotacao]" );
$obLista->commitDado();

if ($stAcao == "conceder") {

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo( "&inRegistro"      , "registro"        );
    $obLista->ultimaAcao->addCampo( "&inCodContrato"   , "cod_contrato"    );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
    $obLista->commitAcao();

} else {

    /*
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Pagamento" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    */

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Período da Viagem" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    /*
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[dt_pagamento]" );
    $obLista->commitDado();
    */

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[dt_inicio] à [dt_termino]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "R$ [vl_total]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "consultar" );
    $obLista->ultimaAcao->addCampo( "&inRegistro"      , "registro"        );
    $obLista->ultimaAcao->addCampo( "&inCodContrato"   , "cod_contrato"    );
    $obLista->ultimaAcao->addCampo( "&inCodDiaria"     , "cod_diaria"      );
    $obLista->ultimaAcao->addCampo( "&stTimestamp"     , "timestamp"       );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "imprimir" );
    $obLista->ultimaAcao->addCampo( "&inRegistro"      , "registro"        );
    $obLista->ultimaAcao->addCampo( "&inCodContrato"   , "cod_contrato"    );
    $obLista->ultimaAcao->addCampo( "&inCodDiaria"     , "cod_diaria"      );
    $obLista->ultimaAcao->addCampo( "&stTimestamp"     , "timestamp"       );
    $obLista->ultimaAcao->setLink( $pgProc."?".Sessao::getId().$stLink."&stRetorno=recibo" );
    $obLista->ultimaAcao->setTarget("telaPrincipal");
    $obLista->commitAcao();

}

$obLista->show();

?>

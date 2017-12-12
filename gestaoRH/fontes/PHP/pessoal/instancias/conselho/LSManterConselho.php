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
* Página de lista do Conselho
* Data de Criação: 10/08/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30892 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalConselho.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConselho";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCaminho = CAM_GRH_PES_INSTANCIAS."conselho/";

if ($_POST[ 'stFiltroDescricaoConselho' ])
    $stFiltroDescricao = $_POST['stFiltroDescricaoConselho'];
else
     $stFiltroDescricao = $_GET['stFiltroDescricaoConselho'];
if ($_POST[ 'stFiltroSiglaConselho'])
    $stFiltroSigla     = $_POST['stFiltroSiglaConselho'];
else
    $stFiltroSigla     = $_GET['stFiltroSiglaConselho'];

//Define a função do arquivo, ex: excluir ou alterar
$arLink = Sessao::read('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"]."&stFiltroSigla=".$stFiltroSigla."&stFiltroDescricao=".$stFiltroDescricao;

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}
$arLink = Sessao::read("link");
$stLink .= "&stAcao=".$stAcao;
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

$rsLista            = new RecordSet;
$obRPessoalConselho = new RPessoalConselho;
$obRPessoalConselho->setDescricao   ($stFiltroDescricao     );
$obRPessoalConselho->setSigla       ($stFiltroSigla         );
$obRPessoalConselho->listarConselho ($rsLista, "descricao"  );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet          ( $rsLista );
//$obLista->setTitulo             ("Conselhos Cadastrados");
$stTitulo = ' </div></td></tr><tr><td colspan="5" class="alt_dados">Conselhos Cadastrados';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Sigla" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição do Conselho" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_conselho" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "sigla" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodConselho" , "cod_conselho" );
$obLista->ultimaAcao->addCampo( "&stSiglaConselho" , "sigla" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "stDescQuestao" , "sigla" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->addCampo( "&stDescricaoConselho" , "descricao" );
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();

$obLista->show();
?>

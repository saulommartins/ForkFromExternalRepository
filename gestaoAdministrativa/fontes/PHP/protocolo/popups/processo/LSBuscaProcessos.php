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
 * Arquivo de instância para popup
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 Casos de uso: uc-01.06.98

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_PROT_NEGOCIO."RProcesso.class.php";
include_once CAM_FW_COMPONENTES."ExpReg/ExpReg.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "BuscaProcessos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js" ;

include_once( $pgJS );

$stCaminho = "../popups/popups/processo/";

$stAcao = $request->get("stAcao");
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$stLink = isset($stLink) ? $stLink : null;

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&campoNom=".$request->get("campoNom");
$stLink .= "&campoNum=".$request->get("campoNum");
$stLink .= "&nomForm=".$request->get("nomForm");

$stMascara = SistemaLegado::pegaConfiguracao('mascara_processo', 5, Sessao::getExercicio() );
$arMascaraProcesso = preg_split("/[^a-zA-Z0-9]/",$stMascara);
$obExpReg = new ExpReg();
$obExpReg->setExpReg("[^a-zA-Z0-9]");
$obExpReg->setContexto( $stMascara );
$arSerapador = $obExpReg->buscarOcorrencias();

$obRProcesso = new RProcesso;

//MANTEM FILTRO E PAGINACAO
//$stLink .= "&stAcao=".$stAcao;
if ( $request->get("pg") and  $request->get("pos") ) {
    Sessao::write('link_pg',$_GET["pg"]);
    Sessao::write('link_pos',$_GET["pos"]);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('link')) ) {
    $_REQUEST = Sessao::read('link');
} else {
    $arLink = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('link',$arLink);
}

if ($_REQUEST["inCodClassificacao"]) {
    $obRProcesso->setCodigoClassificacao( $_REQUEST["inCodClassificacao"] );
    $stLink .= "&inCodClassificacao=".$_REQUEST["inCodClassificacao"];
}

if ($_REQUEST["inCodAssunto"]) {
    $obRProcesso->setCodigoAssunto( $_REQUEST["inCodAssunto"] );
    $stLink .= "&inCodAssunto=".$_REQUEST["inCodAssunto"];
}

if ($_REQUEST["inCGM"]) {
    $obRProcesso->obRCGM->setNumCGM( $_REQUEST["inCGM"] );
    $stLink .= "&inCGM=".$_REQUEST["inCGM"];
}

if ($_REQUEST["stNome"]) {
    $obRProcesso->obRCGM->setNomCGM( $_REQUEST["stNome"] );
    $stLink .= "&stNome=".$_REQUEST["stNome"];
}

if ($request->get("codSituacao")) {
    $obRProcesso->setCodigoSituacao( $request->get("codSituacao") );
}

$obRProcesso->listarProcesso( $rsProcessos );

$rsProcessos->addStrPad("cod_processo", strlen($arMascaraProcesso[0]));
$rsProcessos->addStrPad("ano_exercicio", strlen($arMascaraProcesso[1]));

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsProcessos );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Processo ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Assunto" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
//$obLista->ultimoDado->setCampo( "cod_processo_completo" );
$obLista->ultimoDado->setCampo( "[cod_processo]".$arSerapador[0]."[ano_exercicio]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_assunto" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
//$obLista->ultimaAcao->addCampo("1","cod_processo_completo");
$obLista->ultimaAcao->addCampo("1","[cod_processo]".$arSerapador[0]."[ano_exercicio]");
$obLista->commitAcao();
$obLista->show();

?>

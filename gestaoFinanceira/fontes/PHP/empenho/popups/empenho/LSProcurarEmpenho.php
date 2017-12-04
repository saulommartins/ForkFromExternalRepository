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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" 										);

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript .= " function insereEmpenho(nom,descr) {  																					     \n";
$stFncJavaScript .= " var sNom;                  							  														         \n";
$stFncJavaScript .= " sNom = nom;                							  																 \n";
$stFncJavaScript .= " descr = descr.replace(/[+]/g, ' ');";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = descr;   \n";
$stFncJavaScript .= " window.close();            							  																 \n";
$stFncJavaScript .= " }                          							  															     \n";

//Define a paginacao
if ( !Sessao::read('paginando') ) {
    $arFiltro = Sessao::read('filtro');
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    Sessao::write('paginando', true);
    Sessao::write('filtro', $arFiltro);
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
}

Sessao::write('pg', $inPg);
Sessao::write('pos', $inPos);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'consultar': $pgProx = $pgForm; break;
    default         : $pgProx = $pgForm;
}

//Consulta
$obTEmpenhoEmpenho = new TEmpenhoEmpenho;
$stFiltro .= " AND e.exercicio    = '".$_REQUEST['stExercicio']."' \n";

$inCodEntidade = ($_REQUEST['inCodigoEntidade'] ? $_REQUEST['inCodigoEntidade'] : (Sessao::read('inCodEntidade') ? Sessao::read('inCodEntidade') : ""));

if ($inCodEntidade != "") {
    $stFiltro .= " AND e.cod_entidade = ".$inCodEntidade. "\n";
}
if ($_REQUEST['cgmCredor']) {
    $stFiltro .= " AND pe.cgm_beneficiario = ".$_REQUEST['cgmCredor']. 		 "\n";
}

if ($_REQUEST['dtVigencia']) {
    $stFiltro .= " AND to_date( e.dt_empenho,'yyyy-mm-dd') > to_date('".$_REQUEST['dtVigencia']."','yyyy-mm-dd')". "\n";
}

if ($_REQUEST['inCodEmpenhoInicial'] and $_REQUEST['inCodEmpenhoFinal']) {
    $stFiltro .= " AND e.cod_empenho between ".$_REQUEST['inCodEmpenhoInicial']." AND ".$_REQUEST['inCodEmpenhoFinal']. "\n";
}
if ($_REQUEST['stDtInicial'] and $_REQUEST['stDtFinal']) {
    $stFiltro .= " AND e.dt_empenho between to_date('".$_REQUEST['stDtInicial']."','dd/mm/yyyy') AND to_date('".$_REQUEST['stDtFinal']."','dd/mm/yyyy') \n";
}
if ($_REQUEST['dtInicial']) {
    $stFiltro .= " AND e.dt_empenho >= to_date('".$_REQUEST['dtInicial']."','dd/mm/yyyy')". "\n";
}
if ($_REQUEST['dtFinal']) {
    $stFiltro .= " AND e.dt_empenho <= to_date('".$_REQUEST['dtFinal']."','dd/mm/yyyy')". "\n";
}
if ($_REQUEST['dtEmissao']) {
    $obTEmpenhoEmpenho->setDado('dt_emissao', $_REQUEST['dtEmissao']);
}

# Busca somente empenhos da modalidade Registro de Preços
if ($_REQUEST['registroPrecos'] == true) {
    $obTEmpenhoEmpenho->setDado('registro_precos', true);
}

switch ($_REQUEST['tipoBusca']) {
case 'obra_tcmgo':
    $stFiltro .= " AND  pe.cod_estrutural LIKE '4.4.9.0.51.%' ";
    $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoObras($rsRecordset, $stFiltro);
    break;
case 'empenhoNotaFiscal':
    $obTEmpenhoEmpenho->recuperaEmpenhoNotaFiscal($rsRecordset, $stFiltro);
    break;
default:
    $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordset, $stFiltro);
    break;
}


$obLista = new Lista;

$obLista->setRecordSet( $rsRecordset );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Empenho");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Vencimento");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Credor");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_empenho" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_vencimento" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "credor" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "SELECIONAR" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereEmpenho();" );
$obLista->ultimaAcao->addCampo("1","[cod_empenho]/[exercicio]");
$obLista->ultimaAcao->addCampo("2","[dt_empenho]-[credor]");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>

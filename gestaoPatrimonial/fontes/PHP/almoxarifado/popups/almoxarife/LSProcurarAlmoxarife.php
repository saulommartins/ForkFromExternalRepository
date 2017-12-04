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
    * Arquivo de popup para manutenção de usuários
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-03.03.02

    $Id: LSProcurarAlmoxarife.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarAlmoxarife";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

include_once( $pgJS );

//$stCaminho   = "../modulos/calendario/relatorio/";

$rsLista = new RecordSet;
$obRegra = new RAlmoxarifadoAlmoxarife;
$rsLista = new Recordset;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'baixar'   : $pgProx = $pgBaix; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'prorrogar': $pgProx = $pgCons; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

//Monta sessao com os valores do filtro
if ( is_array(Sessao::read('linkPopUp')) ) {
    $_REQUEST = Sessao::read('linkPopUp');
} else {
    foreach ($_REQUEST as $key => $valor) {
        #sessao->linkPopUp[$key] = $valor;
        $linkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp', $linkPopUp);
}

$stLink .= "&nomForm=".$_REQUEST["nomForm"];
$stLink .= "&campoNom=".$_REQUEST["campoNom"];
$stLink .= "&campoNum=".$_REQUEST["campoNum"];

$obRegra->obRCGMAlmoxarife->obRCGM->setNumCGM('');

//$obRegra->obRCGMAlmoxarife->obRCGM->setNumCGM('');

if ($_REQUEST['inNumCGM'] != '') {
    $obRegra->obRCGMAlmoxarife->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
    $stLink .= '&inNumCGM='.$_REQUEST['inNumCGM'];
}
if ($_REQUEST['stNomCGM'] != '') {
    $obRegra->obRCGMAlmoxarife->obRCGM->setNomCGM( $_REQUEST['stNomCGM'] );
    $stLink .= '&stNomCGM='.$_REQUEST['stNomCGM'];
}

if ($_REQUEST['stUsername']) {
    $obRegra->obRCGMAlmoxarife->setUsername( $_REQUEST['stUsername'] );
    $stLink .= '&stUsername='.$_REQUEST['stUsername'];
}

$stLink .= "&stAcao=".$stAcao;

$obRegra->listarTodos($rsLista);
$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Usuário");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cgm_almoxarife" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_cgm");
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insere();" );
$obLista->ultimaAcao->addCampo("1","cgm_almoxarife");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->ultimaAcao->addCampo("3","username");
$obLista->commitAcao();

$obLista->show();

?>

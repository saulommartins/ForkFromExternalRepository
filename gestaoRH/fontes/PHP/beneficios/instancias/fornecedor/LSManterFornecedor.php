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
* Página de Lista de Fornecedor
* Data de Criação   : 08/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioFornecedorValeTransporte.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho   = CAM_GRH_BEN_INSTANCIAS."fornecedor/";

$obRBeneficioFornecedorValeTransporte = new RBeneficioFornecedorValeTransporte;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgList;
}

$arSessaoLink = Sessao::read('link');
if (!$arSessaoLink['paginando']) {
    foreach ($_POST as $stCampo => $stValor) {
        $arSessaoLink['filtro'][$stCampo] = $stValor;
    }
    $arSessaoLink['pg']  = $_GET['pg'] ? $_GET['pg'] : 0;
    $arSessaoLink['pos'] = $_GET['pos']? $_GET['pos'] : 0;
    $arSessaoLink['paginando'] = true;
} else {
    $arSessaoLink['pg']  = $_GET['pg'];
    $arSessaoLink['pos'] = $_GET['pos'];
}
Sessao::write('link', $arSessaoLink);

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLinkPagina = "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}
$stOrdem = " ORDER BY c.nom_cgm";
$obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporte( $rsLista, $stOrdem );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo( "Fornecedores Cadastrados" );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome do Fornecedor");
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
if ($stAcao == "excluir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
if ($stAcao == "excluir") {
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inNumCGM"     , "numcgm");
    $obLista->ultimaAcao->addCampo("&stNomCGM"     , "nom_cgm");
    $obLista->ultimaAcao->addCampo("stDescQuestao" , "nom_cgm");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
    $obLista->commitAcao();
}
$obLista->show();
?>

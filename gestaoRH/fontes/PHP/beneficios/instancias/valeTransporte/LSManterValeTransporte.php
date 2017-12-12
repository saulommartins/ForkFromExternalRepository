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
* Página de lista do vale transporte
* Data de Criação: 11/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30922 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php"        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$stCaminho   = CAM_GRH_BEN_INSTANCIAS."valeTransporte/";

$obRBeneficioValeTransporte = new RBeneficioValeTransporte;

$stOrdem = " ORDER BY sw_cgm.nom_cgm";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$stLink .= "&stAcao=".$stAcao."&stNomeEmpresaVT=".$_REQUEST['stNomeEmpresaVT'];

$arSessaoLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $arSessaoLink["pg"]  = $_GET["pg"];
    $arSessaoLink["pos"] = $_GET["pos"];
}

if ( is_array($arSessaoLink) ) {
    $_REQUEST = $arSessaoLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arSessaoLink[$key] = $valor;
    }
}
Sessao::write('link', $arSessaoLink);

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'consultar'  : $pgProx = $pgForm;  break;
    case 'alterar'    : $pgProx = $pgForm;  break;
    case 'excluir'    : $pgProx = $pgProc;  break;
    DEFAULT           : $pgProx = $pgForm;
}

if ($_REQUEST['inCodEmpresaVT']) {
    $obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->setNumCGM( $_REQUEST['stNomeEmpresaVT'] );
}

$obRBeneficioValeTransporte->listarValeTransporte( $rsLista, $stFiltro, $stOrdem );
$obLista = new Lista;
$obLista->setTitulo( "Registros de Vale-Transporte" );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Fornecedor");
$obLista->ultimoCabecalho->setWidth( 32 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Origem");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Destino");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_municipio_o]/[origem]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_municipio_d]/[destino]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodValeTransporte"      , "cod_vale_transporte" );
$obLista->ultimaAcao->addCampo("inNumCGM"                  , "numcgm"  );
$obLista->ultimaAcao->addCampo("stDescQuestao"             , "nom_cgm" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
}
$obLista->commitAcao();
$obLista->show();
?>

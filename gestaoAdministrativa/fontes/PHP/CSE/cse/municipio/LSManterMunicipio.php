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
Arquivo de instância para manutenção de municipios
* Data de Criação: 25/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CSE_MAPEAMENTO."TMunicipio.class.php"                                          );

$stPrograma = "ManterMunicipio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stLocation = $pgList."?".$sessao->id."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

switch ($stAcao) {
    case 'alterar'   : $pgProx = $pgForm;      break;
    case 'baixar'    : $pgProx = $stFormBaixa; break;
    case 'excluir'   : $pgProx = $pgProc;      break;
    DEFAULT          : $pgProx = $pgForm;
}

//filtros
if (!$sessao->transf4['paginando']) {
    foreach ($_POST as $stCampo => $stValor) {
        $sessao->transf4['filtro'][$stCampo] = $stValor;
    }
    $sessao->transf4['pg'       ] = $_GET['pg'] ? $_GET['pg'] : 0;
    $sessao->transf4['pos'      ] = $_GET['pos']? $_GET['pos'] : 0;
    $sessao->transf4['paginando'] = true;
} else {
    $sessao->transf4['pg' ] = $_GET['pg'];
    $sessao->transf4['pos'] = $_GET['pos'];
}

if ($sessao->transf4['filtro']) {
    foreach ($sessao->transf4['filtro'] as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}
$sessao->transf4['paginando'] = true;

$stLink = "";
$stLink.= "&stAcao=".$stAcao;

$rsLista      = new RecordSet();
$obTMunicipio = new TMunicipio();

$stFiltro = "";
if ($_REQUEST['inCodMunicipio']!="") { $stFiltro.=" AND sw_municipio.cod_municipio = ".$_REQUEST['inCodMunicipio']."  \n";}
if ($_REQUEST['inCodUf']!="") {        $stFiltro.=" AND sw_uf.cod_uf               = ".$_REQUEST['inCodUf']."         \n";}
if ($_REQUEST['inCodPais']!="") {      $stFiltro.=" AND sw_pais.cod_pais           = ".$_REQUEST['inCodPais']."       \n";}
if ($_REQUEST['stNomeMunicipio']!="") {$stFiltro.=" AND sw_municipio.nom_municipio ILIKE '%".$_REQUEST['stNomeMunicipio']."%'\n";}

$obTMunicipio->recuperaMunicipio($rsLista, $stFiltro);

$inCount = 0;

while (!$rsLista->eof()) {
        $arLista[$inCount]['cod_municipio'] = $rsLista->getCampo('cod_municipio');
        $arLista[$inCount]['nom_municipio'] = $rsLista->getCampo('nom_municipio');
        $arLista[$inCount]['cod_uf']        = $rsLista->getCampo('cod_uf'       );
        $arLista[$inCount]['nom_uf']        = $rsLista->getCampo('nom_uf'       );
        $arLista[$inCount]['cod_pais']      = $rsLista->getCampo('cod_pais'     );
        $arLista[$inCount]['nom_pais']      = $rsLista->getCampo('nom_pais'     );
        $inCount++;
    $rsLista->proximo();
}
if (count($arLista) > 0) {
    $rsLista->preenche($arLista);
    $rsLista->setPrimeiroElemento();
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 1 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "País" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "UF" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_municipio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_municipio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_pais" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_uf" );
$obLista->commitDado();

$obLista->addAcao();

$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo("&inCodMunicipio" ,"cod_municipio"                    );
$obLista->ultimaAcao->addCampo("&stNomeMunicipio","nom_municipio"                    );
$obLista->ultimaAcao->addCampo("&inCodUf"        ,"cod_uf"                           );
$obLista->ultimaAcao->addCampo("&stNomeUf"       ,"nom_uf"                           );
$obLista->ultimaAcao->addCampo("&inCodPais"      ,"cod_pais"                         );
$obLista->ultimaAcao->addCampo("&stNomePais"     ,"nom_pais"                         );
$obLista->ultimaAcao->addCampo("&stDescQuestao"  ,"[cod_municipio] - [nom_municipio]");

if ($stAcao == "excluir") {
    $stCaminho = CAM_CSE."cse/municipio";
    $obLista->ultimaAcao->setLink( $stCaminho."/".$pgProx."?".$sessao->id.$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgForm."?".$sessao->id.$stLink );
}

$obLista->commitAcao();
$obLista->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

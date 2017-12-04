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
Arquivo de instância para manutenção de países
* Data de Criação: 18/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CSE_MAPEAMENTO."TPais.class.php"                                               );

$stPrograma = "ManterPais";
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

$rsLista = new RecordSet();
$obTPais = new TPais();

$stFiltro = "\n WHERE 1 = 1 \n";
if ($_REQUEST['inCodPais']!="") {$stFiltro.=" AND cod_pais = ".$_REQUEST['inCodPais']."      \n";}
if ($_REQUEST['stNome']!="") {   $stFiltro.=" AND nom_pais ILIKE '%".$_REQUEST['stNome']."%' \n";}

$obTPais->recuperaTodos($rsLista, $stFiltro);

$inCount = 0;

while (!$rsLista->eof()) {
        $arLista[$inCount]['cod_pais'     ] = $rsLista->getCampo('cod_pais'     );
        $arLista[$inCount]['cod_rais'     ] = $rsLista->getCampo('cod_rais'     );
        $arLista[$inCount]['nom_pais'     ] = $rsLista->getCampo('nom_pais'     );
        $arLista[$inCount]['nacionalidade'] = $rsLista->getCampo('nacionalidade');
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
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_pais" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_pais" );
$obLista->commitDado();

$obLista->addAcao();

$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo("&inCodRais"      ,"cod_rais"               );
$obLista->ultimaAcao->addCampo("&inCodPais"      ,"cod_pais"               );
$obLista->ultimaAcao->addCampo("&stNome"         ,"nom_pais"               );
$obLista->ultimaAcao->addCampo("&stNacionalidade","nacionalidade"          );
$obLista->ultimaAcao->addCampo("&stDescQuestao"  ,"[cod_pais] - [nom_pais]");

if ($stAcao == "excluir") {
     $stCaminho = CAM_CSE."cse/pais";
    $obLista->ultimaAcao->setLink( $stCaminho."/".$pgProx."?".$sessao->id.$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgForm."?".$sessao->id.$stLink );
}

$obLista->commitAcao();
$obLista->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

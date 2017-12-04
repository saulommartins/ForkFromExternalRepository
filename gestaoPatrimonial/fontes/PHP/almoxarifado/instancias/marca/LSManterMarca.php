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
* Página de Listagem da Marca
* Data de Criação   : 05/05/2005

* @author Analista:
* @author Desenvolvedor: Leandro André Zis

* @ignore

* Casos de uso :uc-03.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoMarca.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterMarca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GP_ALM_INSTANCIAS."marca/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

$stLink = "&stAcao=".$stAcao;

/*if ($_GET["pg"] and  $_GET["pos"]) {
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
} elseif ( is_array($sessao->link) ) {
    $_GET = $sessao->link;
    $_REQUEST = $sessao->link;
    $sessao->link = "";
} else {
    foreach ($_REQUEST as $key => $valor) {
        $sessao->link[$key] = $valor;
    }
}*/

if ($_REQUEST['stHdnDescricao'] || $_REQUEST['inCodEntidade']) {
    foreach ($_REQUEST as $key => $value) {
        Sessao::write("filtro['".$key."']",$value);
        ##sessao->transf4['filtro'][$key] = $value;
    }
} else {
    $arrayFiltro = Sessao::read("filtro");
    ##sessao->transf4['filtro']

    if ( is_array($arrayFiltro) ) {
        ##sessao->transf4['filtro']
        foreach ($arrayFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }

    ##sessao->transf4['paginando'] = true;
    Sessao::write("paginando",true);
}

$obRegra = new RAlmoxarifadoMarca;
$rsLista = new RecordSet;

if ($_REQUEST['inCodigo']) {
   $obRegra->setCodigo($_REQUEST['inCodigo']);
}
if ($_REQUEST['stDescricao']) {
   $obRegra->setDescricao($_REQUEST['stHdnDescricao']);
}

$obRegra->listar($rsLista );

$i = 0;
foreach ($rsLista->arElementos as $arTMP) {
    $rsLista->arElementos[$i]['descricao_popup'] = addslashes($arTMP['descricao']);
    $i++;
}

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Marcas Cadastradas");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_marca" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodigo"          , "cod_marca" );
$obLista->ultimaAcao->addCampo( "&stDescricaoMarca"    , "descricao" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&stDescQuestao"  ,"[cod_marca] - [descricao_popup]");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->setAjuda("UC-03.03.03");
$obLista->commitAcao();
$obLista->show();

?>

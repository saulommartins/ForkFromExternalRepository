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
    * Página de lista do fornecedor
    * Data de Criação   : 22/09/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.04.03

    $Id: LSManterFornecedor.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasFornecedor.class.php");

$stPrograma = "ManterFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_COM_INSTANCIAS."fornecedor/";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;

    case 'ativar/desativar':
        $pgProx = $pgProc;
    break;
}

$arFiltro = Sessao::read('filtro');
//filtros
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg'  , ($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos' , ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando' , true);
    Sessao::write('filtro', $arFiltro);
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

if (is_array($arFiltro)) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

$obTComprasFornecedor = new TComprasFornecedor();
if ($_REQUEST['inCGM'])
    $obTComprasFornecedor->setDado('cgm_fornecedor',$_REQUEST['inCGM']);
if ($_REQUEST['inCodCatalogo'])
    $obTComprasFornecedor->setDado('cod_catalogo',$_REQUEST['inCodCatalogo']);

if ($_REQUEST['stChaveClassificacao']) {
    $arClassificacao = explode('.',$_REQUEST['stChaveClassificacao']);
//Validando para passar a classificação apenas quando esta estiver completa
    if (($_REQUEST['inNumNiveisClassificacao']-1) == count($arClassificacao)) {
        include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoClassificacao.class.php");
        $obTAlmoxarifadoCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao();
        $obTAlmoxarifadoCatalogoClassificacao->recuperaTodos($rsAlmoxaridadoCatalogoClassificacao);
        while (!$rsAlmoxaridadoCatalogoClassificacao->eof()) {
            $arCatalogoAlmoxarifadoClassificacao[$rsAlmoxaridadoCatalogoClassificacao->getCampo('cod_catalogo')][$rsAlmoxaridadoCatalogoClassificacao->getCampo('cod_estrutural')] = $rsAlmoxaridadoCatalogoClassificacao->getCampo('cod_classificacao');
            $rsAlmoxaridadoCatalogoClassificacao->proximo();
        }
        $obTComprasFornecedor->setDado('cod_classificacao',$arCatalogoAlmoxarifadoClassificacao[$_REQUEST['inCodCatalogo']][$_REQUEST['stChaveClassificacao']]);

    }
}
if ($_REQUEST['inCodAtividade_1']) {
    $cod_atividade = explode('-',$_REQUEST['inCodAtividade_1']);
    $cod_atividade = $cod_atividade[1];
    $obTComprasFornecedor->setDado('cod_atividade',$cod_atividade);
}

if ($stAcao == 'ativar/desativar') {
    if ($_REQUEST['stStatus'] != 'T')
        $obTComprasFornecedor->setDado('status',$_REQUEST['stStatus'] == 'A' ? 'ativo' : 'inativo' );
} else {
    $stFiltro = "\n  and f.ativo = true ";
}

$obTComprasFornecedor->recuperaListaFornecedor($rsLista, $stFiltro,"ORDER BY nom_cgm  \n");

$inCount = 0;
$cgmAnterior = 0;
while (!$rsLista->eof()) {
    if ( $cgmAnterior != $rsLista->getCampo('cgm_fornecedor')) {
        $arLista[$inCount]['cgm_fornecedor'] = $rsLista->getCampo('cgm_fornecedor');
        $arLista[$inCount]['nom_cgm'       ] = $rsLista->getCampo('nom_cgm');
        $arLista[$inCount]['status'        ] = $rsLista->getCampo('status');
        $arLista[$inCount]['nom_atividade' ] = $rsLista->getCampo('nom_atividade');
        $arLista[$inCount]['vl_minimo_nf'  ] = $rsLista->getCampo('vl_minimo_nf');
        $arLista[$inCount]['motivo'        ] = $rsLista->getCampo('motivo');
        $arLista[$inCount]['tipo'          ] = $rsLista->getCampo('tipo');
        $inCount++;
    }
    $cgmAnterior = $rsLista->getCampo('cgm_fornecedor');
    $rsLista->proximo();
}
if (count($arLista) > 0) {
    $rsLista->preenche($arLista);
    $rsLista->setPrimeiroElemento();
}
$stLink  = "";
$stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

if ($stAcao == "ativar/desativar") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Status" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ramo de Atividade" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cgm_fornecedor" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

if ($stAcao == "ativar/desativar") {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "status" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_atividade" );
$obLista->commitDado();

$obLista->addAcao();
if ($stAcao == 'ativar/desativar') {
    $obLista->ultimaAcao->setAcao( 'ativar' );
} else {
    $obLista->ultimaAcao->setAcao( $stAcao  );
}
$obLista->ultimaAcao->addCampo("&inCodCatalogo", "cod_catalogo");
$obLista->ultimaAcao->addCampo("&inCGM", "cgm_fornecedor");
$obLista->ultimaAcao->addCampo("&inCodClassificacao", "cod_classificacao");
$obLista->ultimaAcao->addCampo("&stStatus", "status");
$obLista->ultimaAcao->addCampo("&stNomCGM", "nom_cgm");
$obLista->ultimaAcao->addCampo("&vl_minimo_nf","vl_minimo_nf");
$obLista->ultimaAcao->addCampo("&stMotivo", "motivo");
$obLista->ultimaAcao->addCampo("&stTipo", "tipo");
$obLista->ultimaAcao->addCampo("&stDescQuestao", "cgm_fornecedor");

$obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
$obLista->setAjuda("UC-03.04.03");
$obLista->commitAcao();
$obLista->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

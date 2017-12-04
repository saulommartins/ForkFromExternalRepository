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
 * Pagina de Lista tipo do uc-02.10.04
 * Data de Criação: 06/03/209
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author <analista> Bruno Ferreira Santos <bruno.ferreira>
 * @author <desenvolvedor> Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage ldo
 * @uc uc-02.10.04
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_LDO_VISAO   . 'VLDOManterReceita.class.php';
include_once CAM_GF_LDO_VISAO          . 'VLDOManterLDO.class.php';

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$stPrograma = "ManterReceita";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";
$pgExcl = 'FMExcluirReceita.php';
$stCaminho   = CAM_GF_LDO_INSTANCIAS."receita/";

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' : $pgProx = $pgForm; break;
    case 'exluir' :  $pgProx = $pgExcl; break;
    DEFAULT        : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO

if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
}

$link = Sessao::read( 'link' );
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if (is_array($link)) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write( 'link', $link );
}

#mantem a ação
$stAcao =  $_REQUEST['stAcao'];
$stLink .= "&stAcao=".$stAcao;

$rsLDO = VLDOManterLDO::recuperarInstancia()->recuperarLDO();
$rsLDO->getCampo('ano');

#Busca array das receitas
$arReceitas = VLDOManterReceita::recuperarInstancia()->recuperarReceitaLDO($_REQUEST);

$obLista = new Lista();

$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Receitas');
$obLista->setRecordSet($arReceitas);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição da Receita');
$obLista->ultimoCabecalho->setWidth(70);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Total da Receita');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

# Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_estrutural');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('descricao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('total_receita');
$obLista->commitDado();

$obLista->addAcao();

if ($stAcao == "excluir") {
    if (VLDOManterLDO::recuperarInstancia()->recuperarLDOHomologado()) {
        $obLista->ultimaAcao->setAcao(strtoupper($stAcao));
        $obLista->ultimaAcao->setLink( $stCaminho.$pgExcl."?".Sessao::getId().$stLink );
    } else {
        $obLista->ultimaAcao->setAcao($stAcao);
        $obLista->ultimaAcao->setLink($stCaminho.$pgProc."?".Sessao::getId().$stLink);
    }
} else {
    $obLista->ultimaAcao->setAcao($stAcao);
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink            );
}

$obLista->ultimaAcao->addCampo('&inNumReceita', 'cod_receita_ppa');
$obLista->ultimaAcao->addCampo('inCodReceita', 'cod_receita');
$obLista->ultimaAcao->addCampo('stDescricao', 'descricao');
$obLista->ultimaAcao->addCampo('inCodReceitaDados', 'cod_receita_dados');
$obLista->ultimaAcao->addCampo('inTotalReceita', 'total_receita');
$obLista->ultimaAcao->addCampo('inValorTotal', 'total_receita_ppa');
$obLista->ultimaAcao->addCampo('inCodPPA', 'cod_ppa');
$obLista->ultimaAcao->addCampo('inTotalLancado', 'total_lancado');
$obLista->ultimaAcao->addCampo('stAnoLDO', $rsLDO->getCampo('ano'));
$obLista->ultimaAcao->addCampo( "&stDescQuestao","[cod_receita_ppa] - [descricao]" );
$obLista->commitAcao();
$obLista->show();

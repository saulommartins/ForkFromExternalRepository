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
* Página de Formulario de Inclusao de Sequência de Cálculo
* Data de Criação: 05/01/2006

* @author Analista:
* @author Desenvolvedor: Leandro André Zis

* @ignore

* Casos de uso: uc-03.03.09
*/

/*

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferencia.class.php" );

$stPrograma = "MovimentacaoTransferencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$stCaminho = CAM_GP_ALM_INSTANCIAS."saida/";

$arFiltro = array();

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_REQUEST["pg"] and  $_REQUEST["pos"]) {
    $stLink.= "&pg=".$_REQUEST["pg"]."&pos=".$_REQUEST["pos"];
}

$arFiltro = Sessao::read('filtro');

if (!is_array($arFiltro)) {
    Sessao::write('filtro', $_REQUEST);
    $arFiltro = Sessao::read('filtro');
}

// carrega permissoes do almoxarife
include(CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoAlmoxarife.class.php');

$stFiltroAlmoxarife = "      AND a1.cgm_almoxarife = ".Sessao::read('numCgm')." \n";
$stFiltroAlmoxarife.= " GROUP BY a1.cgm_almoxarife                              \n";
$stFiltroAlmoxarife.= "        , a1_cgm.nom_cgm                                     \n";
$stFiltroAlmoxarife.= "        , a1.ativo                                     \n";
$stFiltroAlmoxarife.= "        , a2.cod_almoxarifado                                \n";
$stFiltroAlmoxarife.= "        , a2_cgm.nom_cgm                                     \n";
$stFiltroAlmoxarife.= "        , p.padrao                                           \n";

$obTAlmoxarifadoAlmoxarife = new TAlmoxarifadoAlmoxarife();
$obTAlmoxarifadoAlmoxarife->recuperaAlmoxarifePermissoes($rsAlmoxarifadoAlmoxarife, $stFiltroAlmoxarife);

$arAlmoxarife = $rsAlmoxarifadoAlmoxarife->arElementos;

$stCodAlmoxarifados = '';
while (!$rsAlmoxarifadoAlmoxarife->eof()) {
    $arAlmoxarifado = explode(' - ', $rsAlmoxarifadoAlmoxarife->getCampo('almoxarifados'));
    $stCodAlmoxarifados .= $arAlmoxarifado[0].',';
    $rsAlmoxarifadoAlmoxarife->proximo();
}
$stCodAlmoxarifados = substr($stCodAlmoxarifados, 0, -1);

# Define o nome dos arquivos PHP
$obTAlmoxarifadoPedidoTransferencia = new TAlmoxarifadoPedidoTransferencia;

if ($arFiltro["stExercicio"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('exercicio', $arFiltro["stExercicio"] );

if ($arFiltro["inCodAlmoxarifadoOrigem"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_almoxarifado_origem', $arFiltro["inCodAlmoxarifadoOrigem"] );

if ($arFiltro["inCodAlmoxarifadoDestino"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_almoxarifado_destino', $arFiltro["inCodAlmoxarifadoDestino"] );

if ($arFiltro["inCodTransferencia"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_transferencia', $arFiltro["inCodTransferencia"] );

if ($arFiltro["inCodItem"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_item', $arFiltro["inCodItem"] );

if ($arFiltro["inCodMarca"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_marca', $arFiltro["inCodMarca"] );

if ($arFiltro["inCodCentroCusto"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('cod_centro', $arFiltro["inCodCentroCusto"] );

if ($arFiltro["stObservacao"])
    $obTAlmoxarifadoPedidoTransferencia->setDado('observacao', $arFiltro["stHdnObservacao"] );

$stOrder .= " GROUP BY pedido_transferencia.exercicio
                     , pedido_transferencia.cod_transferencia
                     , pedido_transferencia.cod_almoxarifado_origem
                     , sw_cgm_origem.nom_cgm
                     , pedido_transferencia.cod_almoxarifado_destino
                     , sw_cgm_destino.nom_cgm
                     , pedido_transferencia.observacao ";

if ($stAcao == "entrada") {

    if ($stCodAlmoxarifados != '') {
        $stFiltro .= " AND pedido_transferencia.cod_almoxarifado_destino \n";
        $stFiltro .= "  IN (".$stCodAlmoxarifados.")                     \n";
    }

    // TESTE PARA NÃO TRAZER TRANSFERENCIAS QUE JA DERAM ENTRADA !!
    $stFiltro .= " AND NOT EXISTS (SELECT cod_transferencia from almoxarifado.transferencia_almoxarifado_item_destino
                                    WHERE transferencia_almoxarifado_item_destino.cod_transferencia = transferencia_item_origem.cod_transferencia) \n";

    $obTAlmoxarifadoPedidoTransferencia->recuperaTransferenciasEntrada( $rsTransferencias, $stFiltro, $stOrder );
} else {

    if ($stCodAlmoxarifados != '') {
        $stFiltro  = " AND pedido_transferencia.cod_almoxarifado_origem  \n";
        $stFiltro .= "  IN (".$stCodAlmoxarifados.")                     \n";
    }

    $obTAlmoxarifadoPedidoTransferencia->recuperaTransferenciasSaida( $rsTransferencias, $stFiltro, $stOrder);
}

$obLista = new Lista;

$obLista->setRecordSet( $rsTransferencias );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercício" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Almoxarifado de Origem" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Almoxarifado de Destino" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_transferencia" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_almoxarifado_origem]-[nom_almoxarifado_origem]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_almoxarifado_destino]-[nom_almoxarifado_destino]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "Selecionar" );
$obLista->ultimaAcao->addCampo("&stExercicio" , "exercicio" );
$obLista->ultimaAcao->addCampo("&inCodTransferencia" , "cod_transferencia" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"descricao");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".$sessao->id.$stLink);
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".$sessao->id.$stLink );
}
$obLista->commitAcao();

$obLista->show();
?>

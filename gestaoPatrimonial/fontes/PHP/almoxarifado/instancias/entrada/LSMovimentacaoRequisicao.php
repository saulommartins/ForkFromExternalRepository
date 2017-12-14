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
    * Página de lista do CID
    * Data de Criação: 04/01/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.11

    $Id: LSMovimentacaoRequisicao.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoRequisicao.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoRequisicao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$stLink = '&stAcao='.$stAcao;

if ($_REQUEST['stAno'] != '') {
    $stExercicio = $_REQUEST['stAno'];
} elseif ($_REQUEST['stAnoMes']) {
    $stExercicio = $_REQUEST['stAnoMes'];
}

if ($stExercicio) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }

    Sessao::write('filtro', $filtro);
} else {
    $arrayFiltro = Sessao::read("filtro");
    if (is_array($arrayFiltro)) {
        foreach ($arrayFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }

    Sessao::write("paginando",true);
}

$rsLista = new RecordSet;
$obLista = new Lista;
$obLista->obPaginacao->setFiltro('&stLink='.$stLink);
$obRAlmoxarifadoRequisicao = new RAlmoxarifadoRequisicao();
$obRAlmoxarifadoRequisicao->addRequisicaoItem();
$arCodAlmoxarifados = array();
$dtDataInicial = $dtDataFinal = '';

//montando filtro
if (stExercicio) {
    $obRAlmoxarifadoRequisicao->setExercicio($stExercicio);
}
if ( !empty($_REQUEST['inCodAlmoxarifado'])  ) {
    $arCodAlmoxarifados = $_REQUEST['inCodAlmoxarifado'] ;
}

if ($_REQUEST['inCodRequisicao']) {
    $obRAlmoxarifadoRequisicao->setCodigo( $_REQUEST['inCodRequisicao'] );
}

if ($_REQUEST['stHdnObservacao']) {
    $obRAlmoxarifadoRequisicao->setObservacao( $_REQUEST['stHdnObservacao'] );
}

if ($_REQUEST['stDataInicial']) {
    $dtDataInicial = $_REQUEST['stDataInicial'];
    $dtDataFinal   = $_REQUEST['stDataFinal'];
}

if ($_REQUEST['inCodItem']) {
    $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCatalogoItem->setCodigo( $_REQUEST['inCodItem'] );
}
if ($_REQUEST['inCodMarca']) {
    $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRMarca->setCodigo( $_REQUEST['inCodMarca'] );
}
if ($_REQUEST['inCodCentroCusto']) {
    $obRAlmoxarifadoRequisicao->roUltimoRequisicaoItem->obRAlmoxarifadoEstoqueItem->obRCentroDeCustos->setCodigo( $_REQUEST['inCodCentroCusto'] );
}
if ($stAcao=='saida') {
   $obRAlmoxarifadoRequisicao->listarPermiteMovimentacao($rsLista, $arCodAlmoxarifados, $dtDataInicial, $dtDataFinal, 'S',' order by exercicio desc, cod_requisicao desc, dt_data_requisicao desc');
} else {
   $obRAlmoxarifadoRequisicao->listarPermiteMovimentacao($rsLista, $arCodAlmoxarifados, $dtDataInicial, $dtDataFinal, 'E',' order by exercicio desc, cod_requisicao desc');
}

$obLista->setRecordSet          ( $rsLista );
$obLista->setTitulo             ("Registros");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercício" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Almoxarifado" );
$obLista->ultimoCabecalho->setWidth( 33 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_requisicao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_almoxarifado]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_requisicao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'Selecionar' );
$obLista->ultimaAcao->addCampo( "&inCodRequisicao" , "cod_requisicao" );
$obLista->ultimaAcao->addCampo( "&inCodAlmoxarifado" , "cod_almoxarifado" );
$obLista->ultimaAcao->addCampo( "&stExercicio",        "exercicio");
$obLista->ultimaAcao->addCampo( "&inCodItem",       "cod_item");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

?>

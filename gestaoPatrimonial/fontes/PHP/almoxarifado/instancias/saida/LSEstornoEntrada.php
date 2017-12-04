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
    * Página de lista
    * Data de Criação: 04/01/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * Casos de uso: uc-03.03.11
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterialEstorno.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "EstornoEntrada";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stAcao   = $_REQUEST["stAcao"];
$stLink   = '&stAcao='.$stAcao;
$arFiltro = Sessao::read("filtro");

if (is_array($arFiltro)) {
    foreach($arFiltro as $key => $value )
        $_REQUEST[$key] = $value;
} else {
    foreach ($_REQUEST as $key => $value) {
        if(is_array($value))
            foreach($value as $inCount => $valueArray)
                $arFiltro[$key][] = $valueArray;
        else
            $arFiltro[$key] = $value;
    }
}

Sessao::write("paginando",true);
Sessao::write("filtro", $arFiltro);

$rsLista = new RecordSet;
$obLista = new Lista;
$obLista->obPaginacao->setFiltro('&stLink='.$stLink);

$obTLancamentoEstorno = new TAlmoxarifadoLancamentoMaterialEstorno;

$arCodAlmoxarifados = array();

$dtDataInicial = $dtDataFinal = '';

//montando filtro
if ($_REQUEST['stExercicio']) {
    $obTLancamentoEstorno->setDado( 'stExercicio', $_REQUEST['stExercicio'] );
}

if ( !empty($_REQUEST['inCodAlmoxarifado'])  ) {
    if (is_array($_REQUEST['inCodAlmoxarifado']))
        $obTLancamentoEstorno->setDado('stCodAlmoxarifado', implode(",",$_REQUEST['inCodAlmoxarifado']));
    else
        $obTLancamentoEstorno->setDado('stCodAlmoxarifado', $_REQUEST['inCodAlmoxarifado']);
}

if ($_REQUEST['stDataInicial']) {
    $obTLancamentoEstorno->setDado( 'stDataInicial', $_REQUEST['stDataInicial'] );
}
if ($_REQUEST['stDataFinal']) {
    $obTLancamentoEstorno->setDado( 'stDataFinal', $_REQUEST['stDataFinal'] );
}

if ($_REQUEST['inNroEntradaInicial'] or $_REQUEST['inNroEntradaFinal']) {
    $obTLancamentoEstorno->setDado( 'inNroEntradaInicial' , $_REQUEST['inNroEntradaInicial'] );
    $obTLancamentoEstorno->setDado( 'inNroEntradaFinal'   , $_REQUEST['inNroEntradaFinal']);
}

if (!empty($_REQUEST['inCodEmpenhoInicial'])) {
    $obTLancamentoEstorno->setDado( 'inCodEmpenhoInicial' , $_REQUEST['inCodEmpenhoInicial'] );
    $obTLancamentoEstorno->setDado( 'inCodEmpenhoFinal'   , (!empty($_REQUEST['inCodEmpenhoFinal']) ? $_REQUEST['inCodEmpenhoFinal'] : $_REQUEST['inCodEmpenhoInicial']));
} elseif (empty($_REQUEST['inCodEmpenhoInicial']) && !empty($_REQUEST['inCodEmpenhoFinal'])) {
    $obTLancamentoEstorno->setDado( 'inCodEmpenhoInicial' , $_REQUEST['inCodEmpenhoFinal'] );
    $obTLancamentoEstorno->setDado( 'inCodEmpenhoFinal'   , $_REQUEST['inCodEmpenhoFinal'] );
}

if ($_REQUEST['inCodEmpenhoFinal']) {
    $obTLancamentoEstorno->setDado( 'inCodEmpenhoFinal', $_REQUEST['inCodEmpenhoFinal'] );
    if( !$_REQUEST['inCodEmpenhoInicial'] )
        $obTLancamentoEstorno->setDado( 'inCodEmpenhoInicial', $_REQUEST['inCodEmpenhoInicial'] );
}

if (!empty($_REQUEST['inCodOrdemInicial'])) {
    $obTLancamentoEstorno->setDado( 'inCodOrdemInicial' , $_REQUEST['inCodOrdemInicial'] );
    $obTLancamentoEstorno->setDado( 'inCodOrdemFinal'   , (!empty($_REQUEST['inCodOrdemFinal']) ?  $_REQUEST['inCodOrdemFinal'] : $_REQUEST['inCodOrdemInicial']));
} elseif (empty($_REQUEST['inCodOrdemInicial']) && !empty($_REQUEST['inCodOrdemFinal'])) {
    $obTLancamentoEstorno->setDado( 'inCodOrdemInicial' , $_REQUEST['inCodOrdemFinal']);
    $obTLancamentoEstorno->setDado( 'inCodOrdemFinal'   , $_REQUEST['inCodOrdemFinal']);
}

if ($_REQUEST['inCodItem']) {
    $obTLancamentoEstorno->setDado( 'inCodItem', $_REQUEST['inCodItem'] );
}
if ($_REQUEST['inCodMarca']) {
    $obTLancamentoEstorno->setDado( 'inCodMarca', $_REQUEST['inCodMarca'] );
}
if ($_REQUEST['inCodCentroCusto']) {
    $obTLancamentoEstorno->setDado( 'inCodCentro', $_REQUEST['inCodCentroCusto'] );
}

$obTLancamentoEstorno->recuperaLancamentos($rsLista);

$obLista->setRecordSet          ( $rsLista );
$obLista->setTitulo             ("Registros");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lançamento" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Empenho" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "O.C." );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Almoxarifado" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Almoxarife" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_lancamento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_lancamento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "&nbsp;[cod_exercicio_empenho]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "&nbsp;[cod_exercicio_ordem]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_almoxarifado]-[nom_almoxarifado]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cgm_almoxarife]-[nom_almoxarife]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'Selecionar' );
$obLista->ultimaAcao->addCampo( "&stExercicioLancamento","exercicio_lancamento");
$obLista->ultimaAcao->addCampo( "&inNumLancamento"      , "num_lancamento" );
$obLista->ultimaAcao->addCampo( "&inCodNatureza"        , "cod_natureza"     );
$obLista->ultimaAcao->addCampo( "&stTipoNatureza"       , "tipo_natureza" );

$obLista->ultimaAcao->addCampo( "&inCodAlmoxarifado"    , "cod_almoxarifado"   );
$obLista->ultimaAcao->addCampo( "&stNomAlmoxarifado"    , "nom_almoxarifado"   );
$obLista->ultimaAcao->addCampo( "&inCgmAlmoxarife"      , "cgm_almoxarife"   );
$obLista->ultimaAcao->addCampo( "&stNomAlmoxarife"      , "nom_almoxarife"   );
$obLista->ultimaAcao->addCampo( "&stDataLancamento"     , "dt_lancamento"   );
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

?>

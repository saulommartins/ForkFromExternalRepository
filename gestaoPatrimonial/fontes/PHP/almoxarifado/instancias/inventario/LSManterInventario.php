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
    * Página de Lista de Manter Inventario
    * Data de Criação: 26/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id:$

    * Casos de uso: uc-03.03.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";

$stCodAlmoxarifado = "";
for ( $i=0; $i<count($_POST['inCodAlmoxarifado']); $i++ ) {
    $stCodAlmoxarifado .= " , ".$_POST['inCodAlmoxarifado'][$i];
}

$stCodAlmoxarifado = substr($stCodAlmoxarifado,3,strlen($stCodAlmoxarifado)-3);

$stCaminho = CAM_GP_ALM_INSTANCIAS."inventario/";
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&stAcao=$stAcao";

$obTInventario = new TAlmoxarifadoInventario();
$obTInventario->setDado('exercicio'       , $_POST['stExercicio'] );
$obTInventario->setDado('cod_almoxarifado', $stCodAlmoxarifado );
$obTInventario->setDado('cod_inventario'  , $_POST['inCodInventario'] );
$obTInventario->setDado('dt_inventario'   , $_POST['dtDataInventario'] );
$obTInventario->setDado('observacao'      , $_POST['stHdnObservacao'] );
$obTInventario->setDado('cod_catalogo'    , $_POST['inCodCatalogo'] );
$obTInventario->setDado('cod_estrutural'  , $request->get('stChaveClassificacao') );
$obTInventario->setDado('cod_item'        , $_POST['inCodItem'] );
$obTInventario->setDado('cod_marca'       , $_POST['inCodMarca'] );
$obTInventario->setDado('cod_centro'      , $_POST['inCodCentroCusto'] );
$obTInventario->recuperaInventario( $rsInventarios );

$obLista = new Lista;
$obLista->setTitulo( "Itens da Classificação Bloqueada para Inventário" );
$obLista->setMostraPaginacao( true );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsInventarios );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 3 );
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
$obLista->ultimoCabecalho->addConteudo( "Almoxarifado" );
$obLista->ultimoCabecalho->setWidth( 52 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_inventario" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_almoxarifado]-[desc_almoxarifado]" );
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
$obLista->ultimaAcao->addCampo("&stExercicio"      , "exercicio");
$obLista->ultimaAcao->addCampo("&inCodInventario"  , "cod_inventario");
$obLista->ultimaAcao->addCampo("&inCodAlmoxarifado", "cod_almoxarifado");
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

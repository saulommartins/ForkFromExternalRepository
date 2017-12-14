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
 * Página de Listagem de Mapa de Compras
 * Data de Criação   : 19/03/2007

 * @author Analista: Gelson Wolowski Goncalves
 * @author Desenvolvedor: Bruce Cruz de Sena

 * @ignore

 * Casos de uso: uc-03.04.33

 $Id: LSManterAutorizacao.php 59817 2014-09-12 17:31:26Z evandro $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCompraDireta.class.php';

$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

if (!isset($_REQUEST['inPeriodicidade'])) {
    $_REQUEST = Sessao::read('stFiltroRequest');
}

Sessao::write('stFiltroRequest', $_REQUEST);

if ( isset($_REQUEST['stMapaCompras'] )) {
    foreach ($_REQUEST as $key => $valor) {
        #sessao->link[$key] = $valor;
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
} else {
    $_REQUEST = Sessao::read('link');
}

$arFiltro = array();

$stAcao = $request->get('stAcao');

$obTCompraDireta = new TComprasCompraDireta();
$obTCompraDireta->setDado('exercicio',Sessao::getExercicio());
if ($_REQUEST['inCodEntidade']) {
    $obTCompraDireta->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
}

if ($_REQUEST['inCodModalidade']) {
    $obTCompraDireta->setDado('cod_modalidade', $_REQUEST['inCodModalidade'] );
}

if ($_REQUEST['inCompraDireta']) {
    $obTCompraDireta->setDado('cod_compra_direta', $_REQUEST['inCompraDireta'] );
}

if ($_REQUEST['stMapaCompras']) {
    $arMapa = explode ( '/',  $_REQUEST['stMapaCompras'] );
    $obTCompraDireta->setDado('cod_mapa', $arMapa[0] );
}

//ver datas abaixo provavel que deeem erros...
$arFiltro[] = "to_date( compra_direta.timestamp::VARCHAR, 'yyyy' ) =  to_date ( '".Sessao::getExercicio()."' , 'yyyy' )
                AND homologacao.homologado = true ";

if ($_REQUEST['stDtInicial']) {
    $arFiltro[] = "to_date( compra_direta.timestamp::VARCHAR, 'yyyy/mm/dd' ) >= to_date ( '".$_POST['stDtInicial']."' , 'dd/mm/yyyy' )     ";
}

if ($_REQUEST['stDtFinal']) {
    $arFiltro[] = "to_date( compra_direta.timestamp::VARCHAR, 'yyyy/mm/dd' ) <=  to_date ( '".$_POST['stDtFinal']."', 'dd/mm/yyyy' )   ";
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro =  ' and ' . implode ( ' and ' , $arFiltro );
}

$stOrder = "
        ORDER BY    compra_direta.cod_entidade
               ,    compra_direta.timestamp DESC
               ,    compra_direta.cod_compra_direta ASC
";

$obTCompraDireta->recuperaCompraDiretaAutorizacaoEmpenho( $rsCompraDireta, $stFiltro, $stOrder );

$obLista = new Lista();

$obLista->setRecordSet( $rsCompraDireta );

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Entidade');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Modalidade');
$obLista->ultimoCabecalho->setWidth(25);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Cod. Compra Direta');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Mapa');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [modalidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_compra_direta]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[data]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_mapa]/[exercicio_mapa]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( 'selecionar' );
$obLista->ultimaAcao->addCampo ( "&inCodCompraDireta"   , "cod_compra_direta"  );
$obLista->ultimaAcao->addCampo ( "&inCodEntidade"       , "cod_entidade"       );
$obLista->ultimaAcao->addCampo ( "&inCodModalidade"     , "cod_modalidade"     );
$obLista->ultimaAcao->addCampo ( "&stExercicioEntidade" , "exercicio_entidade" );
$obLista->ultimaAcao->setLink  ( $pgForm."?stAcao=$stAcao&".Sessao::getId().$stLink );

//$obLista->ultimaAcao->setLink( $pgCons."?stAcao=$stAcao&".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

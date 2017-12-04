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
    * Data de Criação   : 19/09/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: LSManterObra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( TTGO.'TTGOObras.class.php' );

$stPrograma = "ManterObra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$arLink = Sessao::read('link');

if ( isset($_REQUEST['stDescricao'] )) {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('link', $arLink);
} else {
    $_REQUEST = $arLink;
}

if ($_REQUEST['stAcao']) {
    $stAcao = $request->get('stAcao');
} else {
    $stAcao = 'excluir';
}

$obForm = new Form;
$obForm->setAction                  ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$rsObras = new RecordSet;

$obTTGOObras = new TTGOObras;

$arFiltro = array();
$stFiltro = '';

if ($_REQUEST['inCodObra']) {
    $arFiltro[] = " cod_obra = " . $_REQUEST['inCodObra'] ;
}
if ($_REQUEST['stExercicio']) {
    $arFiltro[] = " ano_obra = " .$_REQUEST['stExercicio'] ;
}

if ($_REQUEST['stDescricao']) {
    $arFiltro[] = " especificacao ilike '". $_REQUEST['stDescricao'] ."%'";
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " where " .implode ( ' and ' , $arFiltro );
}

$obTTGOObras->recuperaTodos ( $rsObras, $stFiltro );

$obLista = new Lista;

$obLista->setRecordSet( $rsObras );

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ano');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Especificação');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_obra" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "ano_obra" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "especificacao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&cod_obra" , "cod_obra" );
$obLista->ultimaAcao->addCampo("&ano_obra", "ano_obra"  );
if ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $pgForm."?stAcao=$stAcao&".Sessao::getId().$stLink );
} elseif ($stAcao == 'excluir') {
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"cod_obra");
    $obLista->ultimaAcao->setLink( CAM_GPC_TGO_INSTANCIAS.'configuracao/'.$pgProc."?stAcao=$stAcao&".Sessao::getId().$stLink );
}
$obLista->commitAcao();

$obLista->show();

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
    * Data de Criação: 16/11/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Id: LSManterTipoVeiculo.php 32939 2008-09-03 21:14:50Z domluc $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaPosto.class.php");

$stPrograma = "ManterPosto";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."posto/";

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $filtro[$stCampo] = $stValor;
    }
    Sessao::write('pg'  , ($_GET['pg'] ? $_GET['pg']  : 0));
    Sessao::write('pos' , ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando' , true);
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

if ( Sessao::read('filtro') ) {
    foreach ( Sessao::read('filtro') as $key => $value ) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando' , true);

//recupera os registro do banco
$obTPosto = new TFrotaPosto();

//seta os filtros
if ($_REQUEST['inCGMPosto'] != '') {
    $stFiltro = " AND  PO.cgm_posto = ".$_REQUEST['inCGMPosto']." ";
}
if ($_REQUEST['boInterno'] != '') {
    $stFiltro = " AND  PO.interno = '".$_REQUEST['boInterno']."' ";
}
if ($_REQUEST['boAtivo'] != '') {
    $stFiltro = " AND  PO.ativo = '".$_REQUEST['boAtivo']."' ";
}

$obTPosto->recuperaRelacionamento( $rsRecordSet, $stFiltro, ' ORDER BY CGM.numcgm' );

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('uc-03.02.21');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsRecordSet );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Posto" );
$obLista->ultimoCabecalho->setWidth( 80  );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Interno" );
$obLista->ultimoCabecalho->setWidth( 5  );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ativo" );
$obLista->ultimoCabecalho->setWidth( 5  );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_posto] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[interno_label]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[ativo_label]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCGM", "cgm_posto"  );
$obLista->ultimaAcao->addCampo( "&stCGM", "nom_cgm"  );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "[nom_cgm]" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

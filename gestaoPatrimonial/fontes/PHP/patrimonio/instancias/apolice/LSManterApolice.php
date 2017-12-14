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
    * Data de Criação: 16/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26727 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-11-12 16:31:31 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.1  2007/10/17 13:41:48  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioApolice.class.php");

$stPrograma = "ManterApolice";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_PAT_INSTANCIAS."apolice/";

$arFiltro = Sessao::read('filtro');
//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg',($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos',($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg',$_GET['pg']);
    Sessao::write('pos',$_GET['pos']);
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

if ($_REQUEST['stDataInicial'] != '') {
    $stFiltro .= " AND apolice.dt_vencimento BETWEEN TO_DATE('".$_REQUEST['stDataInicial']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinal']."','dd/mm/yyyy') ";
}

if ($_REQUEST['stNumApolice'] != '') {
    $stFiltro .= " AND apolice.num_apolice = '".$_REQUEST['stNumApolice']."' ";
}

if ($_REQUEST['inNumCGM'] != '') {
    $stFiltro .= " AND apolice.numcgm = ".$_REQUEST['inNumCGM']." ";
}

if ($_REQUEST['stHdnContato'] != '') {
    $stFiltro .= " AND apolice.contato ILIKE '".$_REQUEST['stHdnContato']."' ";
}

if ($stAcao == 'excluir') {
    $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                  FROM patrimonio.apolice_bem
                                 WHERE apolice_bem.cod_apolice = apolice.cod_apolice
                              ) ";
}

if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr($stFiltro,4);
}

$stOrder = " ORDER BY apolice.dt_vencimento ";

$obTPatrimonioApolice = new TPatrimonioApolice();
$obTPatrimonioApolice->recuperaApolices( $rsApolice, $stFiltro, $stOrder );

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda     ('UC-03.01.08');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsApolice );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Apólice" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Seguradora" );
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data de Vencimento" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "num_apolice" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "nom_seguradora" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->ultimoDado->setCampo( "dt_vencimento" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodApolice", "cod_apolice");
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "[num_apolice] - [nom_seguradora]" );

if ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm.'?'.Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

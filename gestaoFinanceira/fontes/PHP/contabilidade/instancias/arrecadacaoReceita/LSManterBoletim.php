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
    * Página de Listagem Evento
    * Data de Criação   : 10/02/2005

    * @author Lucas Leusin Oaigen

    * @ignore

    $Revision: 30739 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.17

*/

/*
$Log$
Revision 1.5  2006/07/05 20:50:39  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceitaBoletim.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GF_CONT_INSTANCIAS."arrecadacaoReceita/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($stAcao == "alterar") {
    $pgProx = $pgForm;
} elseif ($stAcao == "baixar") {
    $pgProx = $pgBaix;
} else {
    $pgProx = $pgProc;
}

$obRegra = new RContabilidadeLancamentoReceitaBoletim;

//Código para manter a paginação e filtro
$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando')) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
}

$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}
//<--

$stFiltro = "";

$stLink .= "&stAcao=".$stAcao;
//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write('link', $link);
}

$rsLista = new RecordSet;
if ($_REQUEST["inCodEntidade"]) {
    $obRegra->obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade ($_REQUEST['inCodEntidade']);
}
if ($_REQUEST["stDtInicial"]) {
    $obRegra->setDataInicial( $_REQUEST['stDtInicial'] );
}
if ($_REQUEST["stDtFinal"]) {
    $obRegra->setDataFinal( $_REQUEST['stDtFinal'] );
}
if ($_REQUEST["inNumeroBoletim"]) {
     $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote($_REQUEST['inNumeroBoletim']);
}
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio(Sessao::getExercicio());
$obRegra->listarBoletins( $rsLista );

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Número do Boletim ");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data do Boletim" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "nom_lote" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_lote" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
//if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&dtLote","dt_lote");
    $obLista->ultimaAcao->addCampo("inCodEntidade","cod_entidade");
    $obLista->ultimaAcao->addCampo("inNumeroBoletim","nom_lote");
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"As arrecadações automáticas do dia [dt_lote] serão excluídas. Tem certeza que deseja continuar?");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
/*} else {
    $obLista->ultimaAcao->addCampo("&dtLote","dt_lote");
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}*/
$obLista->commitAcao();
$obLista->show();

?>

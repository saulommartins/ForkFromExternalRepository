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
    * Página de Listagem de Históricos de Empenho
    * Data de Criação   : 01/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Id: LSManterHistorico.php 66483 2016-09-02 17:16:31Z michel $

    * Casos de uso: uc-02.03.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoHistorico.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPreEmpenho.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterHistorico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProx = $pgForm;

$stCaminho = CAM_GF."PHP/empenho/instancias/configuracao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Código para manter a paginação e filtro
if ( !Sessao::read('paginando') ) {
    $arFiltro = array();
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

$obRegra = new REmpenhoHistorico;
$stFiltro = "";

$stLink .= '&inCodHistorico='.$_REQUEST['inCodHistorico'];

$rsLista = new RecordSet;
$obRegra->setCodHistorico( $_REQUEST['inCodHistorico'] );
$obRegra->setNomHistorico( $_REQUEST['stNomHistorico'] );
$obRegra->setExercicio   ( Sessao::getExercicio()      );

$obRegra->listar( $rsLista );

$rsListaTratada = new RecordSet();
$arLista = array();

if($stAcao == "excluir"){
    foreach( $rsLista->getElementos() AS $lista ){
        $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
        $obREmpenhoPreEmpenho->setExercicio(Sessao::getExercicio());
        $obREmpenhoPreEmpenho->obREmpenhoHistorico->setCodHistorico($lista['cod_historico']);
        $obREmpenhoPreEmpenho->obRUsuario->obRCGM->setNumCGM(NULL);
        $obREmpenhoPreEmpenho->listar($rsPreEmpenho);

        if($rsPreEmpenho->getNumLinhas() < 1 && $lista['cod_historico'] > 0)
            $arLista[] = $lista;
    }

    $rsListaTratada->preenche($arLista);

    $rsLista = $rsListaTratada;
}

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_historico" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_historico" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodHistorico","cod_historico");
$obLista->ultimaAcao->addCampo("&stNomHistorico","nom_historico");
$obLista->ultimaAcao->addCampo("&stExercicio"   ,"exercicio");
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"[cod_historico] - [nom_historico]");

if ($stAcao == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

?>

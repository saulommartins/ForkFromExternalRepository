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
    * Página de Listagem do Almoxarife
    * Data de Criação   : 05/12/2004

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.02

*/

/*
$Log$
Revision 1.17  2006/07/20 21:10:07  fernando
alteração na padronização dos UC

Revision 1.16  2006/07/19 19:16:04  fernando
Inclusão do  Ajuda.

Revision 1.15  2006/07/11 13:33:03  fernando
tamanho do rótulo de ação na lista.

Revision 1.14  2006/07/07 18:57:06  fernando
Bug #6095

Revision 1.13  2006/07/06 18:46:04  gelson
Retirado o rótulo da coluna ação para ficar no padrão.

Revision 1.12  2006/07/06 18:31:44  gelson
Adicionado o rótulo da coluna ação.

Revision 1.11  2006/07/06 14:00:21  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:09:52  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAlmoxarife";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GP_ALM_INSTANCIAS."almoxarife/";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$rsLista = new RecordSet;

$obRegra = new RAlmoxarifadoAlmoxarife();

$stLink = "&stAcao=".$stAcao;

if ($_GET["pg"] and  $_GET["pos"]) {
    #sessao->link["pg"]  = $_GET["pg"];
    #sessao->link["pos"] = $_GET["pos"];
    $link['pg']  = $_GET["pg"];
    $link['pos'] = $_GET["pos"];
    Sessao::write('link' , $link);
} elseif ( is_array(Sessao::read('link')) ) {
    $_GET = Sessao::read('link');
    $_REQUEST = Sessao::read('link');
} else {
    foreach ($_REQUEST as $key => $valor) {
        #sessao->link[$key] = $valor;
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
}

if ($_REQUEST['stDescricao']) {
   $obRegra->setDescricao($_REQUEST['stDescricao']);
}
if ($_REQUEST['inCodEntidade']) {
   foreach ($_REQUEST['inCodEntidade'] as $inCodEntidade) {
      $obRegra->addEntidade();
      $obRegra->roUltimaEntidade->setCodigoEntidade($inCodEntidade);
   }
}

$obRegra->listarTodos($rsLista);

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Almoxarife");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Status");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Almoxarifados");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cgm_almoxarife" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "status" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "almoxarifados" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCGMAlmoxarife" , "cgm_almoxarife" );
$obLista->ultimaAcao->addCampo( "&stNomCGMAlmoxarife" , "nom_cgm" );
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"[cgm_almoxarife] - [nom_cgm]");
if ($stAcao == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->setAjuda("UC-03.03.02");
$obLista->commitAcao();
$obLista->show();

?>

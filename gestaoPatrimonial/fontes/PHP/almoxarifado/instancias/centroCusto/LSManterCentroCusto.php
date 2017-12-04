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
    * Página de Formulário Almoxarifado
    * Data de Criação   : 22/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.07

    $Id: LSManterCentroCusto.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCentroDeCustos.class.php");

$stPrograma = "ManterCentroCusto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_ALM_INSTANCIAS."centroCusto/";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ($_REQUEST['stHdnDescricao'] || $_REQUEST['inCodEntidade']) {
    foreach ($_REQUEST as $key => $value) {
        #sessao->transf4['filtro'][$key] = $value;
        $filtro[$key] = $value;
    }
    Sessao::write('filtro', $filtro);
} else {
    if ( Sessao::read('filtro') ) {
        foreach ( Sessao::read('filtro') as $key => $value ) {
            $_REQUEST[$key] = $value;
        }
    }

    Sessao::write('paginando', true);
}

$obRegra = new RAlmoxarifadoCentroDeCustos;

if ($_REQUEST['stHdnDescricao']) {
   $obRegra->setDescricao($_REQUEST['stHdnDescricao']);
}

$inCodEntidade = $_REQUEST['inCodEntidade'];

if ($inCodEntidade) {

    if (!is_array($inCodEntidade)) {
        $inCodEntidade = explode(",",$inCodEntidade);
    }

   foreach ($inCodEntidade as $inEntidade) {
      $obRegra->addEntidade();
      $obRegra->roUltimaEntidade->setCodigoEntidade($inEntidade);
   }
}

if ($_REQUEST['stOrder']) {
    $stOrder = $_REQUEST['stOrder'];
}

$stFiltro = "";
$stLink   = "";

$stLink .= '&inCodigo='.$_REQUEST['inCodigo'];
$stLink .= "&stAcao=".$stAcao;
$stLink .= "&stOrder=".$_REQUEST['stOrder'];
if ($inCodEntidade != '') {
    $stLink .= "&inCodEntidade=".implode(",",$inCodEntidade);
} else {
    $stLink .= "&inCodEntidade=";
}
$stLink .= "&stHdnDescricao=".$_REQUEST['stHdnDescricao'];

$rsLista = new RecordSet;

$obRegra->listar( $rsLista, $stOrder );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 38 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Responsável" );
$obLista->ultimoCabecalho->setWidth( 28 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Vigência" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_centro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_entidade] - [desc_entidade]" );
$obLista->commitDado();

$obLista->addDado();
//$obLista->ultimoDado->setCampo( "[cgm_responsavel] - [nom_cgm]" );
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_vigencia" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigo"              , "cod_centro");
$obLista->ultimaAcao->addCampo("&stDescricao"           , "descricao");
$obLista->ultimaAcao->addCampo("&inCodEntidade"         , "cod_entidade");
//$obLista->ultimaAcao->addCampo("&inCGMResponsavel"      , "cgm_responsavel" );
$obLista->ultimaAcao->addCampo("&inCGMResponsavel"      , "numcgm" );
$obLista->ultimaAcao->addCampo("&stNomCGMResponsavel"   , "nom_cgm" );
$obLista->ultimaAcao->addCampo("&dtDataVigencia"        , "dt_vigencia" );
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"[cod_centro] - [descricao]");

if ($stAcao == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->setAjuda("UC-03.03.07");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

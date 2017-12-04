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
    * Página de listagem de programa

    * Data de Criação   : 29/09/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../classes/visao/VPPAManterPrograma.class.php';
include_once '../../classes/negocio/RPPAManterPrograma.class.php';

//Instanciando a Classe de Controle e de Visao
$obController = new RPPAManterPrograma;
$obVisao = new VPPAManterPrograma( $obController );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$stPrograma = "ManterPrograma";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";
$pgExcl     = 'FMExcluirPrograma.php';
$stCaminho  = CAM_GF_PPA_INSTANCIAS."programas/";

if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
}

$link = Sessao::read( 'link' );

if (is_array($link)) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link', $link);
}

$stAcao =  $_REQUEST['stAcao'];
$stLink .= "&stAcao=".$stAcao;

if ($stAcao == 'alterar') {
    $arPrograma = $obVisao->buscaProgramaLista($_REQUEST);
} else {
    $arPrograma = $obVisao->buscaProgramaListaExclusao($_REQUEST);
}

$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( 'Lista de Programas' );

$obLista->setRecordSet( $arPrograma );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 5        );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth   ( 8       );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "PPA" );
$obLista->ultimoCabecalho->setWidth   ( 4       );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Identificador" );
$obLista->ultimoCabecalho->setWidth   ( 57    );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza temporal" );
$obLista->ultimoCabecalho->setWidth   ( 10         );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth   ( 10       );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO"     );
$obLista->ultimoDado->setCampo      ( "num_programa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO"     );
$obLista->ultimoDado->setCampo      ( "cod_ppa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo      ( "identificacao"  );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo      ( "continuo"    );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( $stAcao );

$obLista->ultimaAcao->addCampo("&inNumPrograma"      , "num_programa");
$obLista->ultimaAcao->addCampo("&inCodPrograma"      , "cod_programa");
$obLista->ultimaAcao->addCampo("&inCodPPA"           , "cod_ppa");
$obLista->ultimaAcao->addCampo("&inCodMacro"         , "cod_macro");
$obLista->ultimaAcao->addCampo("&inCodSetorial"      , "cod_setorial");
if ($stAcao != "excluir") {
    $obLista->ultimaAcao->addCampo("&stNomMacro"         , "nom_macro");
    $obLista->ultimaAcao->addCampo("&stNomSetorial"      , "nom_setorial");
}
$obLista->ultimaAcao->addCampo("&inContinuo"         , "continuo");
$obLista->ultimaAcao->addCampo("&stExercicioUnidade" , "exercicio_unidade");
$obLista->ultimaAcao->addCampo("&inPeriodo"          , "[ano_inicio] - [ano_final]");
$obLista->ultimaAcao->addCampo("&inAnoInicioPPA"     , "ano_inicio");
$obLista->ultimaAcao->addCampo("&inAnoFinalPPA"      , "ano_final");
$obLista->ultimaAcao->addCampo("&inDtInicio"         , "dt_inicial");
$obLista->ultimaAcao->addCampo("&inDtTermino"        , "dt_final");
$obLista->ultimaAcao->addCampo("&inCodNormaVinculada", "cod_norma");
$obLista->ultimaAcao->addCampo("&inNomNormaVinculada", "nom_norma");
$obLista->ultimaAcao->addCampo("&inCodTipoPrograma"  , "cod_tipo_programa");
$obLista->ultimaAcao->addCampo("&stNomTipoPrograma"  , "nom_tipo_programa");
$obLista->ultimaAcao->addCampo("&stDescQuestao"      , "[num_programa]");

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' : $pgProx = $pgForm; break;
    case 'excluir' : $pgProx = $pgProc; break;
    DEFAULT        : $pgProx = $pgForm;
}

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink($stCaminho.$pgProx."?".Sessao::getId().$stLink);
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink($pgProx."?".Sessao::getId().$stLink);
} else {
    $obLista->ultimaAcao->setLink("FMConsultarPrograma.php?".Sessao::getId().$stLink);
}

$obLista->commitAcao();
$obLista->show();

?>

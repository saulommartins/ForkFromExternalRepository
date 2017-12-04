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
    * Data de Criação: 06/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 28252 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-27 13:51:49 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-03.01.05
*/

/*
$Log$
Revision 1.2  2007/09/27 12:57:13  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:11  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php");

$stPrograma = "ManterEspecie";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_PAT_INSTANCIAS."configuracao/";

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

//recupera os registro do banco
$obTPatrimonioEspecie = new TPatrimonioEspecie();
if ($_REQUEST['stHdnDescricaoEspecie'] != '') {
    $stFiltro = " especie.nom_especie ILIKE ('".$_REQUEST['stHdnDescricaoEspecie']."')   AND ";
}
if ($_REQUEST['inCodNatureza'] != '') {
    $stFiltro .= " especie.cod_natureza = ".$_REQUEST['inCodNatureza']." AND ";
}
if ($_REQUEST['inCodGrupo'] != '') {
    $stFiltro .= " especie.cod_grupo = ".$_REQUEST['inCodGrupo']." AND ";
}

// se acao for excluir, retorna so os grupos sem especies vinculadas
if ($stAcao == 'excluir') {
    $stFiltro .= "
        NOT EXISTS 	(  	SELECT 	1
                          FROM  patrimonio.bem
                         WHERE  bem.cod_natureza = especie.cod_natureza
                           AND  bem.cod_grupo = especie.cod_grupo
                           AND  bem.cod_especie = especie.cod_especie
                    ) AND ";
}

if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr( $stFiltro,0,-4 );
}

$obTPatrimonioEspecie->recuperaEspecie( $rsEspecie, $stFiltro, ' ORDER BY cod_natureza, cod_grupo, cod_especie' );

$inCount = 0;
foreach ($rsEspecie->arElementos as $arTemp) {
    $rsEspecie->arElementos[$inCount]['desc_especie'] = addslashes($rsEspecie->arElementos[$inCount]['nom_especie']);
    $inCount++;
}

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('UC-03.01.05');
$stLink .= "&stAcao=".$stAcao;
//$stLink .= '&inCodGrupo='.$rsEspecie->getCampo['cod_grupo'];
//$stLink .= '&inCodNatureza='.$rsEspecie->getCampo['cod_natureza'];
//$stLink .= '&inCodEspecie='.$rsEspecie->getCampo['cod_especie'];
//$stLink .= '&stNomGrupo='.$rsEspecie->getCampo['nom_grupo'];
//$stLink .= '&stNomNatureza='.$rsEspecie->getCampo['nom_natureza'];
//$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsEspecie );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Grupo" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Espécie" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_natureza] - [nom_natureza]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_grupo] - [nom_grupo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_especie] - [nom_especie]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodNatureza", "cod_natureza"    );
$obLista->ultimaAcao->addCampo( "&inCodGrupo", "cod_grupo"    );
$obLista->ultimaAcao->addCampo( "&inCodEspecie", "cod_especie"    );
$obLista->ultimaAcao->addCampo( "stNomEspecie" , "desc_especie"    );
$obLista->ultimaAcao->addCampo( "stDescQuestao" , "[desc_especie]" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&pos=".Sessao::read('pos')."&pg=".Sessao::read('pg') );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

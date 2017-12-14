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
 * Data de Criação: 05/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * @package URBEM
 * @subpackage

 * Casos de uso: uc-03.01.04

 * $Id: LSManterGrupo.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php";

$stPrograma = "ManterGrupo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

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
$obTPatrimonioGrupo = new TPatrimonioGrupo();
if ($_REQUEST['stHdnDescricaoGrupo'] != '') {
    $stFiltro = " AND grupo.nom_grupo ILIKE ('".$_REQUEST['stHdnDescricaoGrupo']."')";
}

if ($_REQUEST['inCodNatureza'] != '') {
    $stFiltro .= " AND grupo.cod_natureza = ".$_REQUEST['inCodNatureza'];
}

// se acao for excluir, retorna so os grupos sem especies vinculadas
if ($stAcao == 'excluir') {
    $stFiltro .= "
        AND NOT EXISTS 	(  	SELECT 	1
                              FROM  patrimonio.especie
                             WHERE  especie.cod_natureza = grupo.cod_natureza
                               AND  especie.cod_grupo = grupo.cod_grupo
                        )  ";
}

$obTPatrimonioGrupo->recuperaDadosGrupo( $rsGrupo, $stFiltro, ' ORDER BY grupo.cod_natureza, grupo.cod_grupo' );

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('UC-03.01.04');
$stLink .= "&stAcao=".$stAcao;
$stLink .= '&inCodGrupo='.$rsGrupo->getCampo('cod_grupo');
$stLink .= '&inCodNatureza='.$rsGrupo->getCampo('cod_natureza');
$stLink .= '&stNomGrupo='.$rsGrupo->getCampo('nom_grupo');
$stLink .= '&stNomNatureza='.$rsGrupo->getCampo('nom_natureza');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsGrupo );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Grupo" );
$obLista->ultimoCabecalho->setWidth( 40 );
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

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodNatureza", "cod_natureza"    );
$obLista->ultimaAcao->addCampo( "&inCodGrupo", "cod_grupo"    );
$obLista->ultimaAcao->addCampo( "stNomGrupo" , "nom_grupo"    );
$obLista->ultimaAcao->addCampo( "stDescQuestao" , "[nom_grupo]" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&pos=".Sessao::read('pos')."&pg=".Sessao::read('pg') );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
